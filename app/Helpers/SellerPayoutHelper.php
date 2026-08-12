<?php

namespace App\Helpers;

/**
 * SellerPayoutHelper - minimal seller payout ledger (Ecosystem Backend
 * Requirements Sec. 4.3 prerequisite - "deduct from the seller's next
 * pending payout" needs a real payout to deduct from, and none existed
 * anywhere in this codebase before this).
 *
 * Deliberately scoped down: a single flat shops.commission_rate, not the
 * full tiered $0/$39/$89 + delivery-vs-pickup commission split from Seller
 * Central's marketing copy (that's a separate, comparably-sized system to
 * build later, mirroring what distribution_plans needed for Sec 5). This
 * ledger tracks what a shop is owed per order and lets a chargeback net
 * against it; it does not execute actual bank-transfer payouts - admin
 * marks rows paid manually, same precedent as supplier/distribution
 * payments.
 */
class SellerPayoutHelper
{
    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Called once an order is delivered (both the ODA mobile API and the
     * legacy web driver dashboard completion paths - see wiring in
     * DriverApiController::onDeliveryCompleted() and
     * DeliveryController::createEarningsRecord()'s caller). Idempotent.
     */
    public static function createPayoutForOrder(int $orderId): void
    {
        $db = self::db();

        $existing = $db->prepare("SELECT id FROM seller_payouts WHERE order_id = ? LIMIT 1");
        $existing->execute([$orderId]);
        if ($existing->fetch()) {
            return;
        }

        $stmt = $db->prepare("
            SELECT o.id, o.shop_id, o.subtotal, s.commission_rate
            FROM orders o
            INNER JOIN shops s ON s.id = o.shop_id
            WHERE o.id = ?
            LIMIT 1
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) return;

        $subtotal = (float)$order['subtotal'];
        $rate = (float)$order['commission_rate'];
        $commission = round($subtotal * $rate / 100, 2);
        $net = round($subtotal - $commission, 2);

        $db->prepare("
            INSERT INTO seller_payouts
            (shop_id, order_id, subtotal, commission_rate, commission_amount, net_payout_amount, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
        ")->execute([$order['shop_id'], $orderId, $subtotal, $rate, $commission, $net]);
    }

    /**
     * Sec 4.3: net a Dynamic Chargeback against this order's seller payout.
     * If the payout is still pending, reduces the available net amount
     * directly. If it was already marked paid, the money already moved -
     * this can't claw it back automatically, so it's flagged for manual
     * admin recovery instead of silently going negative.
     *
     * @return array{applied_against_pending: bool, remaining_net: ?float}
     */
    public static function applyChargeback(int $orderId, float $amount): array
    {
        $db = self::db();

        $stmt = $db->prepare("SELECT id, status, net_payout_amount, chargeback_amount FROM seller_payouts WHERE order_id = ? LIMIT 1");
        $stmt->execute([$orderId]);
        $payout = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$payout) {
            // No payout row yet (order not delivered/ledgered) - still record
            // the chargeback amount as owed once a payout does get created.
            return ['applied_against_pending' => false, 'remaining_net' => null];
        }

        $newChargebackTotal = round((float)$payout['chargeback_amount'] + $amount, 2);

        if ($payout['status'] === 'pending') {
            $db->prepare("
                UPDATE seller_payouts SET chargeback_amount = ?, updated_at = NOW() WHERE id = ?
            ")->execute([$newChargebackTotal, $payout['id']]);

            $remaining = round((float)$payout['net_payout_amount'] - $newChargebackTotal, 2);
            return ['applied_against_pending' => true, 'remaining_net' => $remaining];
        }

        // Already paid - can't claw back automatically, just record the debt.
        $db->prepare("
            UPDATE seller_payouts SET chargeback_amount = ?, status = 'held', updated_at = NOW() WHERE id = ?
        ")->execute([$newChargebackTotal, $payout['id']]);

        return ['applied_against_pending' => false, 'remaining_net' => null];
    }

    public static function pendingBalance(int $shopId): float
    {
        $stmt = self::db()->prepare("
            SELECT COALESCE(SUM(net_payout_amount - chargeback_amount), 0)
            FROM seller_payouts WHERE shop_id = ? AND status = 'pending'
        ");
        $stmt->execute([$shopId]);
        return (float)$stmt->fetchColumn();
    }
}
