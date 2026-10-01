<?php

namespace App\Helpers;

require_once __DIR__ . '/FoundingBonusHelper.php';

/**
 * FoundingSupplierHelper - Founding Supplier Partner Program (Supplier Agreement
 * Sec 4.2/7.4/Schedule A): a 15-supplier cohort locked to Prestige-tier commission
 * (5%, no monthly fee) for 6 months from activation.
 *
 * Same single-row-mutex pattern as FoundingSellerHelper/FoundingBuyerHelper - a
 * global, race-safe counter (founding_supplier_program) decides eligibility at
 * the moment a supplier transitions pending_verification -> active
 * (AdminController::changeSupplierStatus()), locked via SELECT...FOR UPDATE.
 */
class FoundingSupplierHelper
{
    const TOTAL_SLOTS = 15;
    const LOCK_MONTHS = 6;

    // Supplier Account Agreement Sec 7.4.2 - milestone bonus
    const MILESTONE_POS = 10;
    const MILESTONE_BONUS = 150.00;
    const MILESTONE_DAYS = 30;
    // Sec 7.4.3 - referral bonus (paid to both suppliers)
    const REFERRAL_POS = 5;
    const REFERRAL_BONUS = 75.00;
    const REFERRAL_DAYS = 30;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Call once, at the pending_verification -> active status transition.
     * Idempotent - a supplier already granted founding status just re-reports
     * its existing slot.
     *
     * @return array{eligible: bool, founding_partner_number: ?int}
     */
    public static function claimSlotIfEligible(int $supplierId): array
    {
        $db = self::db();

        $supplierStmt = $db->prepare("SELECT founding_partner, founding_partner_number FROM suppliers WHERE id = ? FOR UPDATE");
        $supplierStmt->execute([$supplierId]);
        $supplier = $supplierStmt->fetch(\PDO::FETCH_ASSOC);
        if ($supplier && (int)$supplier['founding_partner'] === 1) {
            return ['eligible' => true, 'founding_partner_number' => (int)$supplier['founding_partner_number']];
        }

        $startedTransaction = false;
        try {
            if (!$db->inTransaction()) {
                $db->beginTransaction();
                $startedTransaction = true;
            }

            $counterStmt = $db->prepare("SELECT slots_used, slots_total FROM founding_supplier_program WHERE id = 1 FOR UPDATE");
            $counterStmt->execute();
            $counter = $counterStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$counter || (int)$counter['slots_used'] >= (int)$counter['slots_total']) {
                if ($startedTransaction) $db->rollBack();
                return ['eligible' => false, 'founding_partner_number' => null];
            }

            $slotNumber = (int)$counter['slots_used'] + 1;

            $db->prepare("UPDATE founding_supplier_program SET slots_used = ? WHERE id = 1")
               ->execute([$slotNumber]);

            $db->prepare("
                UPDATE suppliers SET
                    founding_partner = 1, founding_partner_number = ?, founding_partner_granted_at = NOW(),
                    founding_partner_expires_at = DATE_ADD(NOW(), INTERVAL ? MONTH),
                    subscription_package = 'Prestige', commission_rate = 5.00
                WHERE id = ?
            ")->execute([$slotNumber, self::LOCK_MONTHS, $supplierId]);

            if ($startedTransaction) $db->commit();

            return ['eligible' => true, 'founding_partner_number' => $slotNumber];
        } catch (\Exception $e) {
            if ($startedTransaction && $db->inTransaction()) $db->rollBack();
            error_log('FoundingSupplierHelper::claimSlotIfEligible error: ' . $e->getMessage());
            return ['eligible' => false, 'founding_partner_number' => null];
        }
    }

    public static function remainingSlots(): int
    {
        $stmt = self::db()->query("SELECT slots_total - slots_used FROM founding_supplier_program WHERE id = 1");
        return max(0, (int)($stmt->fetchColumn() ?: 0));
    }

    /**
     * Lazy expiry (same pragmatic choice as SellerPayoutHelper's - no cron
     * infrastructure exists in this codebase): call before any code reads a
     * supplier's commission_rate for a real payout/invoice calculation. A
     * Founding supplier whose 6-month lock (Sec 7.4.1) has passed drops back
     * to Essential-tier pricing as a side effect of that read, rather than a
     * scheduled job catching it. No-op if the supplier isn't Founding, or is
     * Founding but still within the window.
     *
     * Wired into both real commission-calculation call sites:
     * AdminPayablesController::createInvoiceForPO() and
     * AdminDistributionController's PO-payment action.
     */
    public static function applyLazyExpiryIfNeeded(int $supplierId): void
    {
        $db = self::db();

        $stmt = $db->prepare("SELECT founding_partner, founding_partner_expires_at FROM suppliers WHERE id = ? LIMIT 1");
        $stmt->execute([$supplierId]);
        $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$supplier || (int)$supplier['founding_partner'] !== 1) return;
        if (!$supplier['founding_partner_expires_at']) return;
        if (strtotime($supplier['founding_partner_expires_at']) >= time()) return;

        $db->prepare("
            UPDATE suppliers SET founding_partner = 0, subscription_package = 'Essential', commission_rate = 8.00
            WHERE id = ?
        ")->execute([$supplierId]);
    }

    /**
     * SQL condition for "featured placement" in the business procurement
     * catalog: a Prestige plan feature (Supplier Agreement Schedule A), which
     * Enterprise includes and Founding Suppliers get through their Prestige
     * rate lock (Sec 7.4.1).
     */
    public static function featuredSql(string $alias = 's'): string
    {
        return "({$alias}.subscription_package IN ('Prestige', 'Enterprise'))";
    }

    /**
     * Was this supplier ever admitted to the Founding cohort? founding_partner
     * itself is reset to 0 when the 6-month rate lock expires, so the cohort
     * number is the lasting marker.
     */
    public static function isFoundingMember(int $supplierId): bool
    {
        $stmt = self::db()->prepare("SELECT founding_partner_number FROM suppliers WHERE id = ? LIMIT 1");
        $stmt->execute([$supplierId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /** When the supplier became active: founding grant, else application approval, else creation. */
    public static function activeSince(int $supplierId): ?string
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT founding_partner_granted_at, created_at FROM suppliers WHERE id = ? LIMIT 1");
        $stmt->execute([$supplierId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;
        if (!empty($row['founding_partner_granted_at'])) return $row['founding_partner_granted_at'];

        $app = $db->prepare("SELECT reviewed_at FROM supplier_applications WHERE supplier_id = ? AND reviewed_at IS NOT NULL ORDER BY id DESC LIMIT 1");
        $app->execute([$supplierId]);
        return $app->fetchColumn() ?: $row['created_at'];
    }

    /** Purchase Orders fulfilled (completed) inside the supplier's first $days days. */
    public static function fulfilledInWindow(int $supplierId, string $since, int $days): int
    {
        $stmt = self::db()->prepare("
            SELECT COUNT(*) FROM purchase_orders
            WHERE supplier_id = ? AND status = 'completed'
              AND updated_at >= ? AND updated_at < DATE_ADD(?, INTERVAL ? DAY)
        ");
        $stmt->execute([$supplierId, $since, $since, $days]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Award the milestone and referral bonuses this supplier has earned.
     * Idempotent (FoundingBonusHelper's ledger decides) and never throws, so
     * it is safe to call from the daily cron and from invoice creation.
     *
     * @return int number of bonuses newly awarded
     */
    public static function evaluateBonuses(int $supplierId): int
    {
        $awarded = 0;
        try {
            $since = self::activeSince($supplierId);
            if (!$since) return 0;

            if (self::isFoundingMember($supplierId)
                && !FoundingBonusHelper::has('supplier', 'milestone', $supplierId)
                && self::fulfilledInWindow($supplierId, $since, self::MILESTONE_DAYS) >= self::MILESTONE_POS) {
                $awarded += self::award($supplierId, 'milestone', 0, self::MILESTONE_BONUS,
                    'Founding Supplier milestone bonus (' . self::MILESTONE_POS . ' Purchase Orders in ' . self::MILESTONE_DAYS . ' days)',
                    'Milestone bonus earned', 'You fulfilled ' . self::MILESTONE_POS . ' Purchase Orders in your first ' . self::MILESTONE_DAYS . ' days. Your $' . number_format(self::MILESTONE_BONUS, 0) . ' Founding Supplier bonus will be added to your next payout.',
                    "Prime d'étape obtenue", 'Vous avez exécuté ' . self::MILESTONE_POS . ' Bons de commande dans vos ' . self::MILESTONE_DAYS . ' premiers jours. Votre prime Fournisseur fondateur de ' . number_format(self::MILESTONE_BONUS, 0) . ' $ sera ajoutée à votre prochain versement.');
            }

            $ref = self::db()->prepare("SELECT referred_by_supplier_id FROM suppliers WHERE id = ? LIMIT 1");
            $ref->execute([$supplierId]);
            $referrerId = (int)$ref->fetchColumn();
            if ($referrerId && $referrerId !== $supplierId && self::isFoundingMember($referrerId)
                && !FoundingBonusHelper::has('supplier', 'referral_referred', $supplierId, $referrerId)
                && self::fulfilledInWindow($supplierId, $since, self::REFERRAL_DAYS) >= self::REFERRAL_POS) {
                $amount = number_format(self::REFERRAL_BONUS, 0);
                $awarded += self::award($supplierId, 'referral_referred', $referrerId, self::REFERRAL_BONUS,
                    'Founding Supplier referral bonus (referred supplier)',
                    'Referral bonus earned', 'You fulfilled ' . self::REFERRAL_POS . ' Purchase Orders in your first ' . self::REFERRAL_DAYS . ' days. Your $' . $amount . ' referral bonus will be added to your next payout.',
                    'Prime de parrainage obtenue', 'Vous avez exécuté ' . self::REFERRAL_POS . ' Bons de commande dans vos ' . self::REFERRAL_DAYS . ' premiers jours. Votre prime de parrainage de ' . $amount . ' $ sera ajoutée à votre prochain versement.');
                $awarded += self::award($referrerId, 'referral_referrer', $supplierId, self::REFERRAL_BONUS,
                    'Founding Supplier referral bonus (referrer)',
                    'Referral bonus earned', 'A supplier you referred fulfilled ' . self::REFERRAL_POS . ' Purchase Orders. Your $' . $amount . ' referral bonus will be added to your next payout.',
                    'Prime de parrainage obtenue', 'Un fournisseur que vous avez recommandé a exécuté ' . self::REFERRAL_POS . ' Bons de commande. Votre prime de parrainage de ' . $amount . ' $ sera ajoutée à votre prochain versement.');
            }
        } catch (\Throwable $e) {
            error_log('FoundingSupplierHelper::evaluateBonuses error: ' . $e->getMessage());
        }
        return $awarded;
    }

    private static function award(int $supplierId, string $type, int $relatedId, float $amount, string $note, string $titleEn, string $msgEn, string $titleFr, string $msgFr): int
    {
        if (!FoundingBonusHelper::award('supplier', $type, $supplierId, $relatedId, $amount, $note)) {
            return 0;
        }
        require_once __DIR__ . '/NotificationHelper.php';
        NotificationHelper::addSupplierNotification($supplierId, 'payment', $titleEn, $msgEn, '/supplier/payments', 'gift', $titleFr, $msgFr);
        NotificationHelper::add('supplier', 'Founding Supplier bonus awarded',
            "Supplier #{$supplierId}: {$note} - \$" . number_format($amount, 2) . ' is added to the supplier\'s next invoice automatically.',
            ['link' => '/admin/payables']);
        return 1;
    }

    /**
     * Add this supplier's pending bonuses to a payable invoice ("paid with
     * the next scheduled payout"). Called right after the invoice is created.
     *
     * @return float total added
     */
    public static function applyPendingBonusesToInvoice(int $supplierId, int $invoiceId): float
    {
        $db = self::db();
        try {
            $stmt = $db->prepare("SELECT id, amount, notes FROM founding_bonuses WHERE program = 'supplier' AND beneficiary_id = ? AND status = 'pending' ORDER BY id");
            $stmt->execute([$supplierId]);
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$rows) return 0.0;

            $total = 0.0;
            $labels = [];
            $mark = $db->prepare("UPDATE founding_bonuses SET status = 'paid', paid_at = NOW(), notes = CONCAT(COALESCE(notes, ''), ' - added to supplier invoice #', ?) WHERE id = ? AND status = 'pending'");
            foreach ($rows as $row) {
                $mark->execute([$invoiceId, $row['id']]);
                if ($mark->rowCount() === 1) {
                    $total += (float)$row['amount'];
                    $labels[] = $row['notes'] . ' $' . number_format((float)$row['amount'], 2);
                }
            }
            if ($total > 0) {
                $db->prepare("
                    UPDATE supplier_invoices
                    SET net_payable = net_payable + ?, balance_due = balance_due + ?,
                        notes = TRIM(CONCAT(COALESCE(notes, ''), ' Includes: ', ?))
                    WHERE id = ?
                ")->execute([$total, $total, implode('; ', $labels), $invoiceId]);
            }
            return round($total, 2);
        } catch (\Throwable $e) {
            error_log('FoundingSupplierHelper::applyPendingBonusesToInvoice error: ' . $e->getMessage());
            return 0.0;
        }
    }

    /** Dashboard data: bonus progress and the code to share for referrals. */
    public static function perks(int $supplierId): array
    {
        $since = self::activeSince($supplierId);
        $stmt = self::db()->prepare("SELECT supplier_code FROM suppliers WHERE id = ? LIMIT 1");
        $stmt->execute([$supplierId]);
        $code = (string)$stmt->fetchColumn();
        if ($code === '') {
            // Older accounts were created without a supplier code; the code is what a supplier shares for referrals.
            $code = 'SUP-' . strtoupper(bin2hex(random_bytes(4)));
            self::db()->prepare("UPDATE suppliers SET supplier_code = ? WHERE id = ? AND (supplier_code IS NULL OR supplier_code = '')")->execute([$code, $supplierId]);
        }
        $refs = self::db()->prepare("SELECT COUNT(*) FROM founding_bonuses WHERE program = 'supplier' AND bonus_type = 'referral_referrer' AND beneficiary_id = ?");
        $refs->execute([$supplierId]);
        // Measured on the DB clock: timestamps are stored in DB time, PHP runs in America/Toronto
        $daysLeft = 0;
        if ($since) {
            $dl = self::db()->prepare("SELECT GREATEST(0, ? - TIMESTAMPDIFF(DAY, ?, NOW()))");
            $dl->execute([self::MILESTONE_DAYS, $since]);
            $daysLeft = (int)$dl->fetchColumn();
        }
        return [
            'milestone_done'   => FoundingBonusHelper::has('supplier', 'milestone', $supplierId),
            'milestone_count'  => $since ? self::fulfilledInWindow($supplierId, $since, self::MILESTONE_DAYS) : 0,
            'milestone_target' => self::MILESTONE_POS,
            'milestone_bonus'  => self::MILESTONE_BONUS,
            'days_left'        => $daysLeft,
            'referral_code'    => $code,
            'referral_bonus'   => self::REFERRAL_BONUS,
            'referral_pos'     => self::REFERRAL_POS,
            'referrals_paid'   => (int)$refs->fetchColumn(),
        ];
    }
}
