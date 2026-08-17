<?php

namespace App\Helpers;

require_once __DIR__ . '/ChargebackHelper.php';
require_once __DIR__ . '/PaymentGatewayHelper.php';
require_once __DIR__ . '/StoreCreditHelper.php';
require_once __DIR__ . '/BusinessCreditNoteHelper.php';

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
     * Sec B5 - Track B "Resolution Options": Replacement (default), Credit
     * note (+5% bonus to the business's account balance), or Cash refund /
     * payout adjustment - covers both Approvisionnement PO claims
     * (distribution_request_id) and Distribution-shipment claims
     * (distribution_shipment_id). Mirrors resolveReturnAction()'s
     * exchange-first ordering, but Track B has no reverse-pickup/returnless-
     * refund threshold logic - B2B claims don't carry a physical item back
     * through Centrale Livreur the way Track A's does, per the Returns
     * Policy (Track B is shipment/PO-level, not per-item).
     *
     * @return array{success: bool, action: ?string, error: ?string}
     */
    public static function resolveReturnActionTrackB(int $claimId): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM order_claims WHERE id = ? LIMIT 1");
        $stmt->execute([$claimId]);
        $claim = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$claim || $claim['track'] !== 'B' || (!$claim['distribution_request_id'] && !$claim['distribution_shipment_id'])) {
            return ['success' => false, 'action' => null, 'error' => 'Only Track B claims against a procurement request or shipment support this resolution.'];
        }
        if (!in_array($claim['status'], ['resolved', 'approved'], true)) {
            return ['success' => false, 'action' => null, 'error' => 'Claim must be resolved (fault determined) before dispatching a resolution.'];
        }

        $replacement = self::attemptTrackBReplacement($claimId, $claim);
        if (!$replacement['success']) {
            return ['success' => false, 'action' => null, 'error' => $replacement['error']];
        }

        if ($replacement['available']) {
            $kind = $claim['distribution_shipment_id'] ? 'shipment' : 'procurement request';
            self::logAction($claimId, 'replacement_dispatched', "Replacement {$kind} #{$replacement['replacement_number']} created at no charge, queued into the normal fulfillment pipeline (Sec B5).");
            return ['success' => true, 'action' => 'replacement_dispatched', 'error' => null];
        }

        // B5's own fallback when the original record no longer exists to
        // clone (cancelled/deleted) - falls through to the business's chosen
        // credit-note-vs-cash preference, same as Track A's exchange fallback.
        self::logAction($claimId, 'replacement_unavailable', 'Original record not found - falling back to credit note / cash per Sec B5.');

        $businessProfileId = self::resolveTrackBBusinessProfileId($claim);
        if (!$businessProfileId) {
            return ['success' => false, 'action' => null, 'error' => 'Could not resolve the business account for this claim.'];
        }

        if (($claim['preferred_refund_method'] ?? 'cash') === 'credit_note') {
            $credit = \App\Helpers\BusinessCreditNoteHelper::addClaimRefundCredit($businessProfileId, (float)$claim['claimed_value'], $claimId);
            if (!$credit['success']) {
                return ['success' => false, 'action' => null, 'error' => $credit['error']];
            }
            self::logAction($claimId, 'credit_note_issued', "Issued \${$credit['credited_amount']} credit note (incl. \${$credit['bonus_amount']} bonus) to the business's account balance.");
            return ['success' => true, 'action' => 'credit_note_issued', 'error' => null];
        }

        // Cash refund / payout adjustment (Sec B5). A card charge can be
        // refunded via Stripe directly; bank_transfer/net-30 invoice
        // payments have no automated payout rail anywhere in this codebase
        // (same gap as finding #4's weekly payout batching - see
        // [[project_ecosystem_requirements]]), so this degrades honestly to
        // a flagged admin task instead of fabricating an invoice-netting
        // mechanism that doesn't exist.
        $refund = self::refundTrackBPayment($claim);
        if (!$refund['success']) {
            self::logAction($claimId, 'cash_adjustment_needs_manual_action', $refund['error']);
            require_once __DIR__ . '/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'claim', '💳 Manual Payout Adjustment Needed',
                "Claim #{$claimId}: cash refund/payout adjustment of \${$claim['claimed_value']} could not be processed automatically ({$refund['error']}) - handle manually.",
                ['link' => '/admin/claims/view?id=' . $claimId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );
            return ['success' => true, 'action' => 'cash_adjustment_flagged', 'error' => null];
        }
        self::logAction($claimId, 'cash_refund_processed', "Refunded \${$claim['claimed_value']} to the original payment method.");
        return ['success' => true, 'action' => 'cash_refund_processed', 'error' => null];
    }

    /**
     * @return array{success: bool, available: bool, replacement_number: ?string, error: ?string}
     */
    private static function attemptTrackBReplacement(int $claimId, array $claim): array
    {
        $db = self::db();

        // Idempotency guard - same reasoning as Track A's attemptExchange().
        if (!empty($claim['replacement_request_id'])) {
            $existing = $db->prepare("SELECT request_number FROM distribution_requests WHERE id = ?");
            $existing->execute([(int)$claim['replacement_request_id']]);
            return ['success' => true, 'available' => true, 'replacement_number' => $existing->fetchColumn() ?: null, 'error' => null];
        }
        if (!empty($claim['replacement_shipment_id'])) {
            $existing = $db->prepare("SELECT shipment_number FROM distribution_shipments WHERE id = ?");
            $existing->execute([(int)$claim['replacement_shipment_id']]);
            return ['success' => true, 'available' => true, 'replacement_number' => $existing->fetchColumn() ?: null, 'error' => null];
        }

        if (!empty($claim['distribution_request_id'])) {
            return self::createReplacementRequest($claimId, (int)$claim['distribution_request_id']);
        }
        if (!empty($claim['distribution_shipment_id'])) {
            return self::createReplacementShipment($claimId, (int)$claim['distribution_shipment_id']);
        }

        return ['success' => true, 'available' => false, 'replacement_number' => null, 'error' => null];
    }

    /**
     * Approvisionnement replacement: clones the original request + its
     * catalog items into a new $0 request, status 'paid' (skips
     * pending/approval/payment - the admin's claim-resolution action IS the
     * approval), so it flows through the exact same existing procurement ->
     * delivery pipeline any normal paid request uses. Deliberately does NOT
     * try to auto-dispatch a driver or re-trigger supplier
     * invoicing/purchase-orders itself - procurement sourcing is
     * admin-driven even for a brand-new request today, so inventing
     * automation for just the replacement path would be a bigger claim than
     * this codebase can honestly back. Shopping-list items
     * (distribution_shopping_items) are not cloned - out of scope, flagged.
     */
    private static function createReplacementRequest(int $claimId, int $originalRequestId): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM distribution_requests WHERE id = ?");
        $stmt->execute([$originalRequestId]);
        $original = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$original) {
            return ['success' => true, 'available' => false, 'replacement_number' => null, 'error' => null];
        }

        $itemsStmt = $db->prepare("SELECT * FROM distribution_request_items WHERE distribution_request_id = ?");
        $itemsStmt->execute([$originalRequestId]);
        $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

        try {
            $db->beginTransaction();

            $newNumber = 'REPL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $db->prepare("
                INSERT INTO distribution_requests
                (business_profile_id, request_number, request_type, status, subtotal, tax_amount, delivery_fee,
                 discount_amount, total_amount, payment_status, paid_at,
                 delivery_street, delivery_city, delivery_province, delivery_postal_code, delivery_country,
                 delivery_instructions, requested_delivery_date, business_notes, submitted_at, created_at, updated_at)
                VALUES (?, ?, ?, 'paid', 0.00, 0.00, 0.00, 0.00, 0.00, 'paid', NOW(),
                        ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), NOW())
            ")->execute([
                (int)$original['business_profile_id'],
                $newNumber,
                $original['request_type'],
                $original['delivery_street'], $original['delivery_city'], $original['delivery_province'],
                $original['delivery_postal_code'], $original['delivery_country'],
                $original['delivery_instructions'], $original['requested_delivery_date'],
                "Replacement for claim #{$claimId} (original request #{$original['request_number']}) - no charge, Sec B5.",
            ]);
            $newRequestId = (int)$db->lastInsertId();

            foreach ($items as $item) {
                $db->prepare("
                    INSERT INTO distribution_request_items
                    (distribution_request_id, product_id, product_name, product_sku, product_image, quantity, unit_price, subtotal, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW(), NOW())
                ")->execute([
                    $newRequestId, $item['product_id'], $item['product_name'], $item['product_sku'],
                    $item['product_image'], $item['quantity'], $item['unit_price'], $item['subtotal'],
                ]);
            }

            $db->prepare("UPDATE order_claims SET replacement_request_id = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$newRequestId, $claimId]);

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('createReplacementRequest error: ' . $e->getMessage());
            return ['success' => false, 'available' => false, 'replacement_number' => null, 'error' => $e->getMessage()];
        }

        require_once __DIR__ . '/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'distribution', '🔁 Replacement Procurement Request Created',
            "Request #{$newNumber} created at no charge for claim #{$claimId} - needs re-sourcing from the supplier via the normal procurement flow.",
            ['link' => '/admin/distribution/view?id=' . $newRequestId, 'icon' => 'box-seam', 'priority' => 'high']
        );

        return ['success' => true, 'available' => true, 'replacement_number' => $newNumber, 'error' => null];
    }

    /**
     * Distribution-shipment replacement: the business's own goods, not
     * OCSAPP inventory, so there's nothing to stock-check or re-source -
     * "replacement" here means the business physically replaces the
     * damaged/lost goods on their end and OCSAPP dispatches a new $0
     * delivery run for it (per Dima's scoping call: free re-delivery run,
     * not a fabricated goods-replacement mechanism). Clones the shipment +
     * its destinations, status 'paid' (ready for the exact same admin
     * scheduling flow AdminShipmentController already uses - no new
     * dispatch mechanism invented, since shipments have no driver
     * auto-assign even on the happy path).
     */
    private static function createReplacementShipment(int $claimId, int $originalShipmentId): array
    {
        $db = self::db();
        $stmt = $db->prepare("SELECT * FROM distribution_shipments WHERE id = ?");
        $stmt->execute([$originalShipmentId]);
        $original = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$original) {
            return ['success' => true, 'available' => false, 'replacement_number' => null, 'error' => null];
        }

        $destStmt = $db->prepare("SELECT * FROM distribution_shipment_destinations WHERE shipment_id = ? ORDER BY sequence_order ASC");
        $destStmt->execute([$originalShipmentId]);
        $destinations = $destStmt->fetchAll(\PDO::FETCH_ASSOC);

        try {
            $db->beginTransaction();

            $newNumber = 'REPL-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            $db->prepare("
                INSERT INTO distribution_shipments
                (business_profile_id, shipment_number, shipment_type, status,
                 pickup_street, pickup_city, pickup_province, pickup_postal_code,
                 pickup_contact_name, pickup_contact_phone, pickup_instructions,
                 is_multi_drop, destination_street, destination_city, destination_province,
                 destination_postal_code, destination_contact_name, destination_contact_phone,
                 destination_instructions, total_packages, total_weight_kg, package_description,
                 declared_value, subtotal, tax_amount, total_amount, payment_status, paid_at,
                 business_notes, submitted_at, created_at, updated_at)
                VALUES (?, ?, ?, 'paid', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0.00, 0.00, 0.00, 'paid', NOW(), ?, NOW(), NOW(), NOW())
            ")->execute([
                (int)$original['business_profile_id'], $newNumber, $original['shipment_type'],
                $original['pickup_street'], $original['pickup_city'], $original['pickup_province'], $original['pickup_postal_code'],
                $original['pickup_contact_name'], $original['pickup_contact_phone'], $original['pickup_instructions'],
                $original['is_multi_drop'], $original['destination_street'], $original['destination_city'], $original['destination_province'],
                $original['destination_postal_code'], $original['destination_contact_name'], $original['destination_contact_phone'],
                $original['destination_instructions'], $original['total_packages'], $original['total_weight_kg'], $original['package_description'],
                $original['declared_value'],
                "Replacement for claim #{$claimId} (original shipment #{$original['shipment_number']}) - no charge, Sec B5.",
            ]);
            $newShipmentId = (int)$db->lastInsertId();

            foreach ($destinations as $dest) {
                $db->prepare("
                    INSERT INTO distribution_shipment_destinations
                    (shipment_id, sequence_order, destination_name, street, city, province, postal_code,
                     contact_name, contact_phone, delivery_instructions, status, packages_count, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())
                ")->execute([
                    $newShipmentId, $dest['sequence_order'], $dest['destination_name'], $dest['street'], $dest['city'],
                    $dest['province'], $dest['postal_code'], $dest['contact_name'], $dest['contact_phone'],
                    $dest['delivery_instructions'], $dest['packages_count'],
                ]);
            }

            $db->prepare("UPDATE order_claims SET replacement_shipment_id = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$newShipmentId, $claimId]);

            $db->commit();
        } catch (\Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('createReplacementShipment error: ' . $e->getMessage());
            return ['success' => false, 'available' => false, 'replacement_number' => null, 'error' => $e->getMessage()];
        }

        require_once __DIR__ . '/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'distribution', '🔁 Replacement Shipment Created',
            "Shipment #{$newNumber} created at no charge for claim #{$claimId} - coordinate pickup of the replacement goods with the business, then schedule as usual.",
            ['link' => '/admin/shipments/view?id=' . $newShipmentId, 'icon' => 'box-seam', 'priority' => 'high']
        );

        return ['success' => true, 'available' => true, 'replacement_number' => $newNumber, 'error' => null];
    }

    /**
     * Resolves via filed_by_user_id -> business_profiles.user_id, NOT via
     * the distribution_request/shipment row - this is the fallback path
     * that only runs when that original record is already gone
     * (attemptTrackBReplacement's 'available' => false case), so looking it
     * up through the same missing row would always fail exactly when this
     * method is needed. Found by testing the fallback branch directly
     * before shipping it.
     */
    private static function resolveTrackBBusinessProfileId(array $claim): ?int
    {
        if (empty($claim['filed_by_user_id'])) {
            return null;
        }
        $stmt = self::db()->prepare("SELECT id FROM business_profiles WHERE user_id = ?");
        $stmt->execute([(int)$claim['filed_by_user_id']]);
        $id = $stmt->fetchColumn();
        return $id ? (int)$id : null;
    }

    /**
     * Cash refund / payout adjustment leg of Sec B5. Only card (Stripe)
     * payments can be refunded automatically today - reuses the same
     * Stripe\Refund capability built for Track A, keyed off whichever
     * record's payment_intent_id/payment_reference is on file.
     */
    private static function refundTrackBPayment(array $claim): array
    {
        $db = self::db();
        if (!empty($claim['distribution_request_id'])) {
            $stmt = $db->prepare("SELECT payment_method, payment_reference FROM distribution_requests WHERE id = ?");
            $stmt->execute([(int)$claim['distribution_request_id']]);
        } else {
            $stmt = $db->prepare("SELECT payment_method, payment_reference FROM distribution_shipments WHERE id = ?");
            $stmt->execute([(int)$claim['distribution_shipment_id']]);
        }
        $payment = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$payment || $payment['payment_method'] !== 'stripe' || empty($payment['payment_reference'])) {
            return ['success' => false, 'error' => 'No card payment on file to refund automatically - this account pays by bank transfer/net-30, which has no automated payout rail yet.'];
        }

        $config = getStripeConfig();
        if (empty($config['secret_key'])) {
            return ['success' => false, 'error' => 'Card payments are not configured.'];
        }

        try {
            \Stripe\Stripe::setApiKey($config['secret_key']);
            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment['payment_reference'],
                'amount' => (int)round((float)$claim['claimed_value'] * 100),
                'reason' => 'requested_by_customer',
                'metadata' => ['claim_id' => $claim['id']],
            ]);
            return ['success' => $refund->status !== 'failed', 'error' => null];
        } catch (\Exception $e) {
            error_log('refundTrackBPayment error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
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
