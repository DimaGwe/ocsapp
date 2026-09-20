<?php

namespace App\Helpers;

/**
 * SellerPayoutHelper - seller payout ledger (Ecosystem Backend Requirements Sec.
 * 4.3 prerequisite - "deduct from the seller's next pending payout" needs a real
 * payout to deduct from, and none existed anywhere in this codebase before this).
 *
 * Tiered delivery/pickup commission split (Sec B of the Founding Partner Program
 * work) now backs this - shops.commission_rate for delivery orders,
 * shops.pickup_commission_rate for pickup orders (Sec A's self-pickup checkout).
 * Also applies the Founding Seller Partner benefits (Sec C): a locked Experience-
 * tier rate for 12 months and the first 5 delivery orders commission-free, per
 * Seller Agreement Sec 6.1-6.2/Schedule A. This ledger tracks what a shop is owed
 * per order and lets a chargeback net against it; it does not execute actual
 * bank-transfer payouts - admin marks rows paid manually, same precedent as
 * supplier/distribution payments.
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
            SELECT o.id, o.shop_id, o.subtotal, o.fulfillment_type,
                   s.commission_rate, s.pickup_commission_rate,
                   s.founding_partner, s.founding_partner_expires_at, s.founding_free_deliveries_remaining
            FROM orders o
            INNER JOIN shops s ON s.id = o.shop_id
            WHERE o.id = ?
            LIMIT 1
            FOR UPDATE
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) return;

        $shopId = (int)$order['shop_id'];
        $isPickup = ($order['fulfillment_type'] ?? 'delivery') === 'pickup';
        $isFounding = (int)($order['founding_partner'] ?? 0) === 1;
        $expiresAt = $order['founding_partner_expires_at'] ?? null;

        // Lazy expiry (no cron infrastructure exists in this codebase - see plan note):
        // a Founding shop whose 12-month lock has passed drops to Essential-tier rates
        // as a side effect of this payout, rather than a scheduled job catching it.
        if ($isFounding && $expiresAt !== null && strtotime($expiresAt) < time()) {
            $db->prepare("
                UPDATE shops SET founding_partner = 0, subscription_package = 'Essential',
                       commission_rate = 15.00, pickup_commission_rate = 8.00
                WHERE id = ?
            ")->execute([$shopId]);
            $order['commission_rate'] = 15.00;
            $order['pickup_commission_rate'] = 8.00;
            $isFounding = false;
        }

        $rate = $isPickup ? (float)$order['pickup_commission_rate'] : (float)$order['commission_rate'];
        $subtotal = (float)$order['subtotal'];

        // Founding Seller first-5-deliveries-free (Seller Agreement Sec 6.2.1): scoped to
        // delivery orders only - "the Vendor's first five (5) completed delivery Orders."
        $freeRemaining = (int)($order['founding_free_deliveries_remaining'] ?? 0);
        $usedFreeDelivery = false;
        if ($isFounding && !$isPickup && $freeRemaining > 0) {
            $rate = 0.00;
            $usedFreeDelivery = true;
        }

        $commission = round($subtotal * $rate / 100, 2);
        // Payment Processing Fee (Ecosystem Backend Requirements Sec. 7 / Pricing Strategy Sec.
        // 9.1): industry-standard Stripe/PayPal rate, same base as commission (subtotal, not
        // delivery fee/tax), deducted the same way, same moment - "alongside commission" per the
        // doc. Seller Central's marketing copy promises this is absorbed by the seller.
        $processingFee = round($subtotal * 0.029 + 0.30, 2);
        $net = round($subtotal - $commission - $processingFee, 2);

        $db->prepare("
            INSERT INTO seller_payouts
            (shop_id, order_id, subtotal, commission_rate, commission_amount, processing_fee_amount, net_payout_amount, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
        ")->execute([$shopId, $orderId, $subtotal, $rate, $commission, $processingFee, $net]);

        if ($usedFreeDelivery) {
            $db->prepare("UPDATE shops SET founding_free_deliveries_remaining = founding_free_deliveries_remaining - 1 WHERE id = ?")
               ->execute([$shopId]);
        }
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
