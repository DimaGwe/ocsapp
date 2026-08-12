<?php

namespace App\Helpers;

require_once __DIR__ . '/SellerPayoutHelper.php';

/**
 * ChargebackHelper - Dynamic Chargeback execution (Ecosystem Backend
 * Requirements Sec. 4.3), plus the reverse-logistics rate lookup shared by
 * chargeback fee calculation and Instant Reverse Routing dispatch (Sec 4.4).
 *
 * Vendor-caused: deducts claimed value + reverse-logistics fee from the
 * seller's next pending payout (SellerPayoutHelper, built this round since
 * nothing existed before) or nets against the supplier's next open invoice
 * (supplier_invoices.balance_due - suppliers already had invoice-based AP,
 * unlike sellers).
 * Transit-caused: OCSAPP absorbs, no deduction, tracked here for reporting.
 */
class ChargebackHelper
{
    const DISPUTE_WINDOW_BUSINESS_DAYS = 5;

    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * "Schedule C.1a" doesn't exist as a literal document anywhere in this
     * codebase - the real numbers live in the Pricing Strategy PDF Sec 8.4a,
     * seeded onto delivery_zones.reverse_logistics_rate_b2c/b2b by the
     * add_reverse_logistics_rates migration.
     */
    public static function reverseLogisticsRate(?string $city, string $track): float
    {
        $zone = resolveDeliveryZoneFee($city)['zone_code'] ?? null;
        if (!$zone) return 0.00;

        $column = $track === 'A' ? 'reverse_logistics_rate_b2c' : 'reverse_logistics_rate_b2b';
        $stmt = self::db()->prepare("SELECT {$column} FROM delivery_zones WHERE code = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$zone]);
        return (float)($stmt->fetchColumn() ?: 0.00);
    }

    /**
     * Admin confirms fault=vendor_caused and triggers this. $partyType/
     * $partyId are resolved automatically for Track A (the order's shop);
     * Track B requires the admin to specify which supplier is at fault
     * (a distribution request can span multiple suppliers, so this isn't
     * always inferable).
     */
    public static function createVendorChargeback(int $claimId, int $adminId, string $partyType, int $partyId): array
    {
        $db = self::db();

        $claim = self::loadClaim($claimId);
        if (!$claim) {
            return ['success' => false, 'error' => 'Claim not found.'];
        }

        $city = self::resolveDeliveryCity($claim);
        $reverseFee = self::reverseLogisticsRate($city, $claim['track']);
        $totalDeduction = round((float)$claim['claimed_value'] + $reverseFee, 2);
        $disputeDeadline = self::addBusinessDays(new \DateTime(), self::DISPUTE_WINDOW_BUSINESS_DAYS);

        $db->beginTransaction();
        try {
            $db->prepare("
                INSERT INTO chargebacks
                (order_claim_id, party_type, party_id, claimed_value, reverse_logistics_fee, total_deduction,
                 status, dispute_window_ends_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'pending_deduction', ?, NOW(), NOW())
            ")->execute([
                $claimId, $partyType, $partyId, $claim['claimed_value'], $reverseFee, $totalDeduction,
                $disputeDeadline->format('Y-m-d H:i:s'),
            ]);
            $chargebackId = (int)$db->lastInsertId();

            // Net against the party's payout/invoice immediately - the
            // "pending_deduction" status tracks the dispute window, not
            // whether the money has been netted (per the doc: "deduct...
            // automatically", not "deduct after the dispute window").
            if ($partyType === 'seller') {
                self::applySellerDeduction($claim, $partyId, $totalDeduction);
            } else {
                self::applySupplierDeduction($partyId, $totalDeduction);
            }

            $db->prepare("
                UPDATE order_claims
                SET fault_determination = 'vendor_caused', fault_determined_at = NOW(), fault_determined_by = ?,
                    status = 'resolved', resolution = 'chargeback', updated_at = NOW()
                WHERE id = ?
            ")->execute([$adminId, $claimId]);

            $db->commit();

            // Notification failures must never make an already-committed
            // chargeback report itself as failed - the money already moved.
            try {
                self::notifyParty($partyType, $partyId, $totalDeduction, $chargebackId, $disputeDeadline);
            } catch (\Exception $notifyError) {
                error_log('createVendorChargeback notification error (chargeback itself succeeded): ' . $notifyError->getMessage());
            }

            return ['success' => true, 'chargeback_id' => $chargebackId, 'total_deduction' => $totalDeduction];
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('createVendorChargeback error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create chargeback.'];
        }
    }

    /**
     * Admin confirms fault=transit_caused. OCSAPP absorbs the cost - no
     * deduction from anyone, tracked here purely for reporting (the doc's
     * "Section 11.6 cost-tracking" reference in the Financial Model, which
     * isn't a document this codebase has access to build against directly -
     * this table is the closest available substitute).
     */
    public static function absorbTransitLoss(int $claimId, int $adminId): array
    {
        $db = self::db();
        $claim = self::loadClaim($claimId);
        if (!$claim) {
            return ['success' => false, 'error' => 'Claim not found.'];
        }

        $city = self::resolveDeliveryCity($claim);
        $reverseFee = self::reverseLogisticsRate($city, $claim['track']);
        $totalCost = round((float)$claim['claimed_value'] + $reverseFee, 2);

        $db->beginTransaction();
        try {
            $db->prepare("
                INSERT INTO chargebacks
                (order_claim_id, party_type, party_id, claimed_value, reverse_logistics_fee, total_deduction,
                 status, dispute_window_ends_at, resolved_at, resolved_by, created_at, updated_at)
                VALUES (?, 'seller', 0, ?, ?, ?, 'reversed', NOW(), NOW(), ?, NOW(), NOW())
            ")->execute([$claimId, $claim['claimed_value'], $reverseFee, $totalCost, $adminId]);

            $db->prepare("
                UPDATE order_claims
                SET fault_determination = 'transit_caused', fault_determined_at = NOW(), fault_determined_by = ?,
                    status = 'resolved', resolution = 'absorbed', updated_at = NOW()
                WHERE id = ?
            ")->execute([$adminId, $claimId]);

            $db->commit();
            return ['success' => true, 'absorbed_amount' => $totalCost];
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('absorbTransitLoss error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to record absorbed loss.'];
        }
    }

    private static function applySellerDeduction(array $claim, int $shopId, float $amount): void
    {
        if (empty($claim['order_id'])) return;
        \App\Helpers\SellerPayoutHelper::createPayoutForOrder((int)$claim['order_id']); // ensure a row exists
        \App\Helpers\SellerPayoutHelper::applyChargeback((int)$claim['order_id'], $amount);
    }

    /**
     * Nets against the supplier's earliest open invoice. If it doesn't fully
     * cover the deduction (or none is open), the remainder is left on the
     * invoice's balance_due at 0 and flagged via admin_notes for manual
     * follow-up rather than going negative or silently failing.
     */
    private static function applySupplierDeduction(int $supplierId, float $amount): void
    {
        $db = self::db();
        $stmt = $db->prepare("
            SELECT id, balance_due FROM supplier_invoices
            WHERE supplier_id = ? AND status IN ('sent','partial','overdue') AND balance_due > 0
            ORDER BY due_date ASC LIMIT 1
        ");
        $stmt->execute([$supplierId]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$invoice) {
            // No open invoice to net against - not automatable, flag for finance.
            require_once __DIR__ . '/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'chargeback', '⚠️ Chargeback needs manual netting',
                "Supplier #{$supplierId} has no open invoice to net a \${$amount} chargeback against - needs manual finance follow-up.",
                ['link' => '/admin/payables', 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );
            return;
        }

        $newBalance = max(0, round((float)$invoice['balance_due'] - $amount, 2));
        $newStatus = $newBalance <= 0 ? 'paid' : 'partial';
        $db->prepare("
            UPDATE supplier_invoices SET balance_due = ?, status = ?, updated_at = NOW() WHERE id = ?
        ")->execute([$newBalance, $newStatus, $invoice['id']]);
    }

    private static function loadClaim(int $claimId): ?array
    {
        $stmt = self::db()->prepare("SELECT * FROM order_claims WHERE id = ? LIMIT 1");
        $stmt->execute([$claimId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private static function resolveDeliveryCity(array $claim): ?string
    {
        $db = self::db();
        if ($claim['track'] === 'A' && $claim['order_id']) {
            $addr = $db->prepare("SELECT delivery_address FROM orders WHERE id = ?");
            $addr->execute([$claim['order_id']]);
            $raw = $addr->fetchColumn();
            $decoded = $raw ? json_decode($raw, true) : null;
            return $decoded['city'] ?? null;
        }
        if ($claim['distribution_request_id']) {
            $addr = $db->prepare("SELECT delivery_city FROM distribution_requests WHERE id = ?");
            $addr->execute([$claim['distribution_request_id']]);
            return $addr->fetchColumn() ?: null;
        }
        return null;
    }

    private static function addBusinessDays(\DateTime $start, int $days): \DateTime
    {
        $result = clone $start;
        $added = 0;
        while ($added < $days) {
            $result->modify('+1 day');
            if ((int)$result->format('N') < 6) $added++;
        }
        return $result;
    }

    private static function notifyParty(string $partyType, int $partyId, float $amount, int $chargebackId, \DateTime $disputeDeadline): void
    {
        require_once __DIR__ . '/NotificationHelper.php';
        $deadlineStr = $disputeDeadline->format('M j, Y');
        if ($partyType === 'seller') {
            $shop = self::db()->prepare("SELECT seller_id, name FROM shops WHERE id = ?");
            $shop->execute([$partyId]);
            $shopRow = $shop->fetch(\PDO::FETCH_ASSOC);
            if ($shopRow) {
                self::db()->prepare("
                    INSERT INTO user_notifications (user_id, type, title, message, link, is_read, created_at)
                    VALUES (?, 'chargeback', ?, ?, '/seller/orders', 0, NOW())
                ")->execute([
                    $shopRow['seller_id'],
                    '⚠️ Chargeback Applied',
                    "A \${$amount} chargeback was applied to your account. You have until {$deadlineStr} to submit counter-evidence.",
                ]);
            }
        } else {
            \App\Helpers\NotificationHelper::addSupplierNotification(
                $partyId, 'chargeback', 'Chargeback Applied',
                "A \${$amount} chargeback was applied to your next invoice. You have until {$deadlineStr} to submit counter-evidence.",
                '/supplier/invoices'
            );
        }
    }
}
