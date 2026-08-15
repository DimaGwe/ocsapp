<?php

namespace App\Helpers;

require_once __DIR__ . '/ChargebackHelper.php';
require_once __DIR__ . '/PaymentGatewayHelper.php';
require_once __DIR__ . '/StoreCreditHelper.php';

/**
 * ReturnsDispatchHelper - Instant Reverse Routing + Returnless Refunds
 * (Ecosystem Backend Requirements Sec. 4.4).
 *
 * Stripe refunds are genuinely new capability - no refund-to-processor call
 * exists anywhere in this codebase today (confirmed by research before
 * building Sec 4: cancelOrder() and markRefunded() are both pre-payment or
 * manual-bookkeeping-only, no live Stripe\Refund call anywhere).
 */
class ReturnsDispatchHelper
{
    private static function db(): \PDO
    {
        return \Database::getConnection();
    }

    /**
     * Sec 4.4's threshold rule: dispatch a reverse pickup if the claimed
     * value covers the reverse-logistics cost, otherwise skip dispatch
     * entirely and refund directly ("Returnless Refunds"). Track A only -
     * the doc scopes returnless refunds to Track A (B2C) low-value items.
     *
     * @return array{success: bool, action: ?string, error: ?string}
     */
    public static function resolveReturnAction(int $claimId): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM order_claims WHERE id = ? LIMIT 1");
        $stmt->execute([$claimId]);
        $claim = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$claim || $claim['track'] !== 'A' || !$claim['order_id']) {
            return ['success' => false, 'action' => null, 'error' => 'Only Track A (Marche) claims support returnless refund evaluation.'];
        }
        if (!in_array($claim['status'], ['resolved', 'approved'], true)) {
            return ['success' => false, 'action' => null, 'error' => 'Claim must be resolved (fault determined) before dispatching a return.'];
        }

        // Sec A5 Exchange-First: for defective/damaged/incorrect items, offer
        // an immediate identical exchange via the same pickup/drop-off run
        // BEFORE presenting a cash refund. Buyer change-of-mind and other
        // claim types skip straight to the threshold logic below - A5 only
        // applies to items that are actually wrong.
        if (in_array($claim['claim_type'], ['damaged', 'wrong_item', 'not_as_described'], true)) {
            $exchange = self::attemptExchange($claimId, $claim);

            if (!$exchange['success']) {
                return ['success' => false, 'action' => null, 'error' => $exchange['error']];
            }

            if ($exchange['available']) {
                self::logAction($claimId, 'exchange_dispatched', "Identical replacement (order #{$exchange['replacement_order_number']}) dispatched via the same run as the reverse pickup - no refund issued.");
                return ['success' => true, 'action' => 'exchange_dispatched', 'error' => null];
            }

            // No identical replacement in stock - Sec A5's own fallback:
            // "you'll get a full refund automatically." Falls through to the
            // existing threshold logic below, which already handles that.
            self::logAction($claimId, 'exchange_unavailable', 'No identical replacement in stock for one or more items - falling back to refund path per Sec A5.');
        }

        $city = self::resolveOrderCity((int)$claim['order_id']);
        $reverseFee = \App\Helpers\ChargebackHelper::reverseLogisticsRate($city, 'A');
        $claimedValue = (float)$claim['claimed_value'];

        if ($claimedValue < $reverseFee) {
            $wantsCredit = ($claim['preferred_refund_method'] ?? 'cash') === 'store_credit';

            if ($wantsCredit) {
                $credit = \App\Helpers\StoreCreditHelper::addClaimRefundCredit((int)$claim['filed_by_user_id'], $claimedValue, $claimId);
                if (!$credit['success']) {
                    return ['success' => false, 'action' => null, 'error' => $credit['error']];
                }
                self::logAction($claimId, 'returnless_store_credit', "Item value \${$claimedValue} < reverse trip cost \${$reverseFee} - issued \${$credit['credited_amount']} store credit (incl. \${$credit['bonus_amount']} bonus), no pickup dispatched.");
                return ['success' => true, 'action' => 'returnless_store_credit', 'error' => null];
            }

            $refund = self::refundBuyer((int)$claim['order_id'], $claimedValue, "Returnless refund - claim #{$claimId}");
            if (!$refund['success']) {
                return ['success' => false, 'action' => null, 'error' => $refund['error']];
            }
            self::logAction($claimId, 'returnless_refund', "Item value \${$claimedValue} < reverse trip cost \${$reverseFee} - refunded directly, no pickup dispatched.");
            return ['success' => true, 'action' => 'returnless_refund', 'error' => null];
        }

        $dispatch = self::dispatchReversePickup((int)$claim['order_id'], $claimId, $reverseFee);
        if (!$dispatch['success']) {
            return ['success' => false, 'action' => null, 'error' => $dispatch['error']];
        }
        self::logAction($claimId, 'reverse_pickup_dispatched', "Reverse pickup assignment #{$dispatch['assignment_id']} created at \${$reverseFee}.");
        return ['success' => true, 'action' => 'reverse_pickup_dispatched', 'error' => null];
    }

    /**
     * Sec A5 Exchange-First. Re-ships the exact contents of the original
     * order (claims are order-level in this codebase - order_claims has no
     * item-level column, so "identical replacement" means the whole order's
     * contents, same as claimed_value already is) as a new $0 order, and
     * dispatches both halves of the "same pickup/drop-off run": a forward
     * delivery for the replacement and a reverse pickup for the defective
     * item, preferring the same driver for both where one is available.
     *
     * 'available' => false is not an error - it's Sec A5's own trigger to
     * fall back to a refund ("If an identical replacement isn't available,
     * you'll get a full refund automatically"), handled by the caller.
     *
     * @return array{success: bool, available: bool, replacement_order_id: ?int, replacement_order_number: ?string, error: ?string}
     */
    private static function attemptExchange(int $claimId, array $claim): array
    {
        $db = self::db();
        $orderId = (int)$claim['order_id'];

        // Idempotency guard - resolveReturnAction() has no built-in
        // protection against being re-run on an already-dispatched claim
        // (e.g. a double form submit from the admin claims page), and unlike
        // the reverse-pickup/refund branches, a repeat exchange dispatch
        // would double-decrement real inventory and create a second $0
        // order. If this claim already has a replacement order, treat it as
        // already succeeded rather than dispatching again.
        if (!empty($claim['replacement_order_id'])) {
            $existing = $db->prepare("SELECT order_number FROM orders WHERE id = ?");
            $existing->execute([(int)$claim['replacement_order_id']]);
            return [
                'success' => true, 'available' => true,
                'replacement_order_id' => (int)$claim['replacement_order_id'],
                'replacement_order_number' => $existing->fetchColumn() ?: null,
                'error' => null,
            ];
        }

        $orderStmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) {
            return ['success' => false, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => 'Original order not found.'];
        }

        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $itemsStmt->execute([$orderId]);
        $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($items)) {
            return ['success' => false, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => 'Original order has no items to replace.'];
        }

        // Stock check first, no writes yet - if any line item can't be
        // covered, this is an "unavailable" outcome (Sec A5's own fallback
        // trigger), not a failure.
        foreach ($items as $item) {
            if (!$item['shop_inventory_id']) {
                return ['success' => true, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => null];
            }
            $stockStmt = $db->prepare("SELECT stock_quantity FROM shop_inventory WHERE id = ?");
            $stockStmt->execute([$item['shop_inventory_id']]);
            $available = $stockStmt->fetchColumn();
            if ($available === false || (int)$available < (int)$item['quantity']) {
                return ['success' => true, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => null];
            }
        }

        try {
            $db->beginTransaction();

            foreach ($items as $item) {
                $stmt = $db->prepare("
                    UPDATE shop_inventory
                    SET stock_quantity = stock_quantity - ?, sold_quantity = sold_quantity + ?, updated_at = NOW()
                    WHERE id = ? AND stock_quantity >= ?
                ");
                $stmt->execute([$item['quantity'], $item['quantity'], $item['shop_inventory_id'], $item['quantity']]);
                if ($stmt->rowCount() === 0) {
                    // Lost the race to a concurrent sale between the check above
                    // and now - roll back and fall through to the refund path,
                    // exactly like an upfront "unavailable" result.
                    $db->rollBack();
                    return ['success' => true, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => null];
                }
            }

            $replacementNumber = 'OCS' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            $subtotal = array_sum(array_map(fn($i) => (float)$i['subtotal'], $items));

            $db->prepare("
                INSERT INTO orders
                (user_id, shop_id, order_number, subtotal, tax, delivery_fee, stop_count, total,
                 payment_method, payment_status, payment_gateway, delivery_address, notes, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 0.00, 0.00, 1, 0.00, 'cash', 'paid', 'exchange', ?, ?, 'pending', NOW(), NOW())
            ")->execute([
                (int)$order['user_id'],
                (int)$order['shop_id'],
                $replacementNumber,
                $subtotal,
                $order['delivery_address'],
                "Exchange replacement for claim #{$claimId} (original order #{$order['order_number']}) - no charge, Sec A5.",
            ]);
            $replacementOrderId = (int)$db->lastInsertId();

            foreach ($items as $item) {
                $db->prepare("
                    INSERT INTO order_items (order_id, product_id, shop_inventory_id, product_name, sku, quantity, price, subtotal, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ")->execute([
                    $replacementOrderId,
                    $item['product_id'],
                    $item['shop_inventory_id'],
                    $item['product_name'],
                    $item['sku'],
                    $item['quantity'],
                    $item['price'],
                    $item['subtotal'],
                ]);
            }

            $db->prepare("UPDATE order_claims SET replacement_order_id = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$replacementOrderId, $claimId]);

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log('attemptExchange error: ' . $e->getMessage());
            return ['success' => false, 'available' => false, 'replacement_order_id' => null, 'replacement_order_number' => null, 'error' => $e->getMessage()];
        }

        // Dispatch happens outside the DB transaction (notifications, driver
        // lookups) - the order/stock/claim state above is already committed
        // and correct even if dispatch itself only partially succeeds (same
        // "flag for manual assignment" degrade-gracefully pattern used
        // throughout this helper).
        $driver = self::findAvailableDriver((int)$order['shop_id']);
        $preferredDriverId = $driver['driver_id'] ?? null;

        self::dispatchReplacementDelivery($replacementOrderId, $preferredDriverId, (int)$order['shop_id']);
        self::dispatchReversePickup($orderId, $claimId, \App\Helpers\ChargebackHelper::reverseLogisticsRate(self::resolveOrderCity($orderId), 'A'), $preferredDriverId);

        return ['success' => true, 'available' => true, 'replacement_order_id' => $replacementOrderId, 'replacement_order_number' => $replacementNumber, 'error' => null];
    }

    /**
     * Shared driver lookup (zone-aware via driver_availability) used by both
     * halves of an exchange dispatch, so the reverse pickup and the
     * replacement delivery prefer landing on the same driver where possible -
     * as close to Sec A5's "same run" as this codebase's dispatch model
     * supports without ODA app changes (a single combined pickup+drop-off
     * screen in the driver app is a separate follow-up, flagged not built).
     */
    private static function findAvailableDriver(int $shopId): ?array
    {
        // Pre-existing bug found and fixed while verifying this build:
        // shops has no zone_id column (confirmed via DESCRIBE - it doesn't
        // exist anywhere on that table), so the original version of this
        // query, inherited unchanged from dispatchReversePickup(), always
        // threw a "column not found" SQL error, was swallowed by the
        // try/catch, and silently degraded to "no driver, flag admin" every
        // single time - the auto-assign half of Instant Reverse Routing has
        // never actually run since it was built. driver_availability.zone_id
        // exists and is real (used for cross-Zone dispatch), but shops has
        // no equivalent to join against, so this now matches on capacity/
        // workload only, same as it always effectively fell back to anyway.
        try {
            $stmt = self::db()->prepare("
                SELECT da.driver_id
                FROM driver_availability da
                WHERE da.status = 'available'
                  AND da.active_deliveries < COALESCE(da.max_deliveries, 3)
                ORDER BY da.active_deliveries ASC
                LIMIT 1
            ");
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log('findAvailableDriver failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Standard forward delivery_assignment (delivery_type defaults to
     * 'order') for a no-charge exchange replacement order.
     */
    private static function dispatchReplacementDelivery(int $orderId, ?int $preferredDriverId, int $shopId): array
    {
        $db = self::db();
        $driverId = $preferredDriverId ?? (self::findAvailableDriver($shopId)['driver_id'] ?? null);

        if (!$driverId) {
            require_once __DIR__ . '/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'delivery', '🔁 Exchange Replacement Needs Manual Assignment',
                "No available driver for exchange replacement order #{$orderId} - assign manually.",
                ['link' => '/admin/delivery/active', 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );
            return ['success' => true, 'assignment_id' => null, 'error' => null, 'needs_manual_assignment' => true];
        }

        $stmt = $db->prepare("
            INSERT INTO delivery_assignments
            (order_id, driver_id, shop_id, status, delivery_fee, delivery_notes, assigned_at, created_at)
            VALUES (?, ?, ?, 'assigned', 0.00, ?, NOW(), NOW())
        ");
        $stmt->execute([$orderId, $driverId, $shopId, "Exchange replacement delivery for order #{$orderId} - no charge."]);
        $assignmentId = (int)$db->lastInsertId();

        require_once __DIR__ . '/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'delivery', '🔁 Exchange Replacement Assigned',
            "Exchange replacement delivery for order #{$orderId} assigned to driver.",
            ['link' => '/admin/delivery/active', 'icon' => 'box-seam', 'priority' => 'normal']
        );

        return ['success' => true, 'assignment_id' => $assignmentId, 'error' => null];
    }

    /**
     * "Instant Reverse Routing" - creates a new delivery_assignment for the
     * reverse pickup, at the zone-calibrated reverse-logistics rate. Mirrors
     * the existing auto-assign pattern (nearest available driver by zone/
     * workload) used for forward deliveries. Accepts an optional preferred
     * driver so an Exchange-First dispatch (Sec A5) can try to land the
     * reverse pickup on the same driver as the replacement delivery.
     */
    private static function dispatchReversePickup(int $orderId, int $claimId, float $reverseFee, ?int $preferredDriverId = null): array
    {
        $db = self::db();

        $orderStmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) {
            return ['success' => false, 'assignment_id' => null, 'error' => 'Order not found.'];
        }

        $driverId = $preferredDriverId ?? (self::findAvailableDriver((int)$order['shop_id'])['driver_id'] ?? null);

        if (!$driverId) {
            require_once __DIR__ . '/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'delivery', '↩️ Reverse Pickup Needs Manual Assignment',
                "No available driver for the reverse pickup on order #{$order['order_number']} - assign manually.",
                ['link' => '/admin/delivery/active', 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );
            return ['success' => true, 'assignment_id' => null, 'error' => null, 'needs_manual_assignment' => true];
        }

        $stmt = $db->prepare("
            INSERT INTO delivery_assignments
            (order_id, driver_id, shop_id, status, delivery_fee, delivery_type, delivery_notes, assigned_at, created_at)
            VALUES (?, ?, ?, 'assigned', ?, 'reverse_pickup', ?, NOW(), NOW())
        ");
        $stmt->execute([
            $orderId,
            $driverId,
            $order['shop_id'],
            $reverseFee,
            "Reverse pickup for claim #{$claimId} - order #{$order['order_number']}",
        ]);
        $assignmentId = (int)$db->lastInsertId();

        require_once __DIR__ . '/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'delivery', '↩️ Reverse Pickup Assigned',
            "Reverse pickup for order #{$order['order_number']} assigned to driver.",
            ['link' => '/admin/delivery/active', 'icon' => 'undo', 'priority' => 'normal']
        );

        return ['success' => true, 'assignment_id' => $assignmentId, 'error' => null];
    }

    /**
     * Genuinely new capability - no Stripe refund call exists anywhere else
     * in this codebase. Refunds against the order's stored payment_intent_id
     * from the original Checkout Session.
     */
    public static function refundBuyer(int $orderId, float $amount, string $reason): array
    {
        $config = getStripeConfig();
        if (empty($config['secret_key'])) {
            return ['success' => false, 'error' => 'Card payments are not configured.'];
        }

        $db = self::db();
        $stmt = $db->prepare("SELECT payment_intent_id, payment_method FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (empty($order['payment_intent_id']) || $order['payment_method'] !== 'card') {
            return ['success' => false, 'error' => 'No card payment on file for this order to refund - process manually.'];
        }

        try {
            \Stripe\Stripe::setApiKey($config['secret_key']);
            $refund = \Stripe\Refund::create([
                'payment_intent' => $order['payment_intent_id'],
                'amount' => (int)round($amount * 100),
                'reason' => 'requested_by_customer',
                'metadata' => ['order_id' => $orderId, 'reason' => $reason],
            ]);

            $db->prepare("UPDATE orders SET payment_status = 'refunded', updated_at = NOW() WHERE id = ?")->execute([$orderId]);

            return ['success' => $refund->status !== 'failed', 'error' => null, 'refund_id' => $refund->id];
        } catch (\Exception $e) {
            error_log('Stripe refund error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private static function resolveOrderCity(int $orderId): ?string
    {
        $stmt = self::db()->prepare("SELECT delivery_address FROM orders WHERE id = ?");
        $stmt->execute([$orderId]);
        $raw = $stmt->fetchColumn();
        $decoded = $raw ? json_decode($raw, true) : null;
        return $decoded['city'] ?? null;
    }

    private static function logAction(int $claimId, string $eventType, string $details): void
    {
        self::db()->prepare("
            UPDATE order_claims SET admin_notes = CONCAT(COALESCE(admin_notes, ''), '\n', ?), updated_at = NOW() WHERE id = ?
        ")->execute(["[{$eventType}] {$details}", $claimId]);
    }
}
