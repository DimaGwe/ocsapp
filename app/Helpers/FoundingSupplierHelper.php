<?php

namespace App\Helpers;

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
}
