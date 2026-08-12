<?php

namespace App\Helpers;

require_once __DIR__ . '/ChargebackHelper.php';
require_once __DIR__ . '/PaymentGatewayHelper.php';

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

        $city = self::resolveOrderCity((int)$claim['order_id']);
        $reverseFee = \App\Helpers\ChargebackHelper::reverseLogisticsRate($city, 'A');
        $claimedValue = (float)$claim['claimed_value'];

        if ($claimedValue < $reverseFee) {
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
     * "Instant Reverse Routing" - creates a new delivery_assignment for the
     * reverse pickup, at the zone-calibrated reverse-logistics rate. Mirrors
     * the existing auto-assign pattern (nearest available driver by zone/
     * workload) used for forward deliveries.
     */
    private static function dispatchReversePickup(int $orderId, int $claimId, float $reverseFee): array
    {
        $db = self::db();

        $orderStmt = $db->prepare("SELECT * FROM orders WHERE id = ?");
        $orderStmt->execute([$orderId]);
        $order = $orderStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) {
            return ['success' => false, 'assignment_id' => null, 'error' => 'Order not found.'];
        }

        // Find an available driver via driver_availability (the real,
        // live-used source of truth for status/zone/workload elsewhere in
        // this codebase - not a reconstructed guess from delivery_assignments).
        // delivery_assignments.driver_id is NOT NULL with no default, so a
        // row literally cannot be created without a real driver - if none
        // is available, this skips creation entirely and flags for manual
        // admin dispatch rather than attempting an invalid insert.
        $driver = null;
        try {
            $driverStmt = $db->prepare("
                SELECT da.driver_id
                FROM driver_availability da
                LEFT JOIN shops s ON s.id = ?
                WHERE da.status = 'available'
                  AND (da.zone_id = s.zone_id OR da.zone_id IS NULL OR s.zone_id IS NULL)
                  AND da.active_deliveries < COALESCE(da.max_deliveries, 3)
                ORDER BY da.active_deliveries ASC
                LIMIT 1
            ");
            $driverStmt->execute([$order['shop_id']]);
            $driver = $driverStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
        } catch (\Exception $e) {
            error_log('dispatchReversePickup driver lookup failed: ' . $e->getMessage());
        }

        if (!$driver) {
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
            $driver['driver_id'],
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
