<?php
/**
 * Scheduled Task: Supplier Response Timer — Reminders & Auto-Reassign
 *
 * Supplier has 10 minutes to accept a PO.
 * Reminders:
 *   - 5 min remaining → in-app + email reminder
 *   - 2 min remaining (8 min elapsed) → urgent in-app + email reminder
 * Expiry:
 *   - Deadline passed with no response → auto-cancel + move to next supplier on shortlist
 *   - Shortlist exhausted → admin alert
 *
 * Cron: Run every 1 minute
 *   * * * * * cd /var/www/html/marketplace && /usr/bin/php scheduled_tasks/escalate_expired_confirmations.php >> /var/www/html/marketplace/storage/logs/cron-escalation.log 2>&1
 */

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

$now = date('Y-m-d H:i:s');
echo "[{$now}] escalate_expired_confirmations starting\n";

try {
    // Fetch all active POs in the 10-minute window (status=sent, B2B flow)
    $stmt = $db->prepare("
        SELECT po.*,
               s.company_name AS supplier_company,
               s.name         AS supplier_contact,
               s.email        AS supplier_email
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.id
        WHERE po.status = 'sent'
          AND po.distribution_request_id IS NOT NULL
          AND po.confirmation_deadline IS NOT NULL
    ");
    $stmt->execute();
    $activePOs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($activePOs)) {
        echo "[{$now}] No active POs in confirmation window.\n";
        exit(0);
    }

    echo "[{$now}] Checking " . count($activePOs) . " active PO(s).\n";

    foreach ($activePOs as $po) {
        $deadlineTs    = strtotime($po['confirmation_deadline']);
        $secondsLeft   = $deadlineTs - time();
        $supplierName  = $po['supplier_company'] ?: $po['supplier_contact'] ?: 'Supplier';
        $poNumber      = $po['po_number'];

        // ── EXPIRED: no response, auto-reassign ───────────────────────────────
        if ($secondsLeft <= 0) {
            echo "[{$now}] PO #{$poNumber} EXPIRED — auto-cancelling and escalating.\n";
            try {
                $db->beginTransaction();
                $db->prepare("
                    UPDATE purchase_orders SET
                        status             = 'cancelled',
                        supplier_declined_at = NOW(),
                        decline_reason     = 'Auto-cancelled: 10-minute response window expired',
                        updated_at         = NOW()
                    WHERE id = ?
                ")->execute([$po['id']]);
                $db->commit();

                _escalateExpiredPO($po, $db);
            } catch (Exception $e) {
                if ($db->inTransaction()) $db->rollBack();
                echo "[{$now}] ERROR escalating PO #{$poNumber}: " . $e->getMessage() . "\n";
                error_log("escalate_expired_confirmations: PO #{$poNumber} error: " . $e->getMessage());
            }
            continue;
        }

        // ── 8-MIN REMINDER: ≤ 2 minutes remaining ────────────────────────────
        if ($secondsLeft <= 120 && !$po['reminder_8min_sent']) {
            echo "[{$now}] PO #{$poNumber} — sending 8-min (urgent) reminder ({$secondsLeft}s left).\n";
            _sendReminder($po, $db, '8min', $secondsLeft);
            continue;
        }

        // ── 5-MIN REMINDER: ≤ 5 minutes remaining ────────────────────────────
        if ($secondsLeft <= 300 && !$po['reminder_5min_sent']) {
            echo "[{$now}] PO #{$poNumber} — sending 5-min reminder ({$secondsLeft}s left).\n";
            _sendReminder($po, $db, '5min', $secondsLeft);
            continue;
        }
    }

    echo "[{$now}] Done checking confirmation window.\n";

    // ── POST-ACCEPTANCE REMINDERS ─────────────────────────────────────────────
    // Remind suppliers who accepted but haven't started preparing (>30 min)
    // Remind suppliers who started preparing but aren't ready for pickup (>60 min)
    _checkProgressReminders($db, $now);

    echo "[{$now}] Done.\n";

} catch (Exception $e) {
    echo "[{$now}] FATAL: " . $e->getMessage() . "\n";
    error_log("escalate_expired_confirmations fatal: " . $e->getMessage());
    exit(1);
}

// ─────────────────────────────────────────────────────────────────────────────

function _sendReminder(array $po, PDO $db, string $stage, int $secondsLeft): void
{
    $supplierName = $po['supplier_company'] ?: $po['supplier_contact'] ?: 'Supplier';
    $poNumber     = $po['po_number'];
    $poId         = (int)$po['id'];
    $supplierId   = (int)$po['supplier_id'];

    $minsLeft  = ceil($secondsLeft / 60);
    $isUrgent  = $stage === '8min';
    $prefix    = $isUrgent ? '🚨 URGENT' : '⏰ Reminder';
    $col       = $stage === '8min' ? 'reminder_8min_sent' : 'reminder_5min_sent';

    // Mark as sent first to prevent double-send on overlapping cron runs
    $db->prepare("UPDATE purchase_orders SET {$col} = 1, updated_at = NOW() WHERE id = ?")->execute([$poId]);

    // In-app notification to supplier
    $prefixFr = $isUrgent ? '🚨 URGENT' : '⏰ Rappel';
    \App\Helpers\NotificationHelper::addSupplierNotification(
        $supplierId,
        'purchase_order',
        "{$prefix} — PO #{$poNumber} expires in {$minsLeft} min",
        $isUrgent
            ? "Only {$minsLeft} minute(s) left to respond to PO #{$poNumber}. If you do not respond the order will be automatically reassigned to another supplier."
            : "You have {$minsLeft} minutes to accept or decline PO #{$poNumber}. Please respond now to avoid automatic reassignment.",
        "supplier/orders/view?id={$poId}",
        $isUrgent ? 'exclamation-triangle' : 'clock',
        "{$prefixFr} — BC #{$poNumber} expire dans {$minsLeft} min",
        $isUrgent
            ? "Il ne reste que {$minsLeft} minute(s) pour répondre à BC #{$poNumber}. Sans réponse, la commande sera automatiquement réassignée à un autre fournisseur."
            : "Vous avez {$minsLeft} minutes pour accepter ou refuser BC #{$poNumber}. Veuillez répondre maintenant pour éviter la réassignation automatique."
    );

    // Email reminder if supplier has email
    if (!empty($po['supplier_email'])) {
        try {
            \App\Helpers\EmailHelper::send(
                $po['supplier_email'],
                "{$prefix}: PO #{$poNumber} — Respond within {$minsLeft} minute(s)",
                'supplier-po-reminder',
                [
                    'supplier_name'  => $supplierName,
                    'po_number'      => $poNumber,
                    'minutes_left'   => $minsLeft,
                    'is_urgent'      => $isUrgent,
                    'action_url'     => 'https://ocsapp.ca/supplier/orders/view?id=' . $poId,
                    'current_year'   => date('Y'),
                ]
            );
        } catch (Exception $e) {
            error_log("escalate_expired_confirmations: email reminder failed for PO #{$poNumber}: " . $e->getMessage());
        }
    }
}

function _escalateExpiredPO(array $po, PDO $db): void
{
    $distRequestId     = (int)$po['distribution_request_id'];
    $escalationAttempt = (int)($po['escalation_attempt'] ?? 0);
    $poNumber          = $po['po_number'];

    // Get delivery type and shortlist from the distribution request
    $stmt = $db->prepare("SELECT delivery_type FROM distribution_requests WHERE id = ? LIMIT 1");
    $stmt->execute([$distRequestId]);
    $dr = $stmt->fetch(PDO::FETCH_ASSOC);
    $deliveryType = $dr['delivery_type'] ?? 'scheduled';

    // Max 2 escalation attempts (original + 2 backups = 3 suppliers tried)
    if ($escalationAttempt >= 2) {
        \App\Helpers\NotificationHelper::add(
            'new_order',
            '⚠️ All Suppliers Exhausted — Manual Action Required',
            "PO #{$poNumber} timed out and all backup suppliers have been tried. Distribution request #{$distRequestId} needs manual intervention.",
            ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
        );
        return;
    }

    // Get product IDs from the expired PO
    $stmt = $db->prepare("SELECT product_id FROM purchase_order_items WHERE purchase_order_id = ?");
    $stmt->execute([$po['id']]);
    $productIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Already-tried supplier IDs for this distribution request (avoid re-sending to same supplier)
    $stmt = $db->prepare("
        SELECT DISTINCT supplier_id FROM purchase_orders
        WHERE distribution_request_id = ? AND supplier_id IS NOT NULL
    ");
    $stmt->execute([$distRequestId]);
    $triedSupplierIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Find next supplier from the ranked shortlist (excludes already-tried suppliers)
    $backup = null;
    foreach ($productIds as $productId) {
        $backup = _getNextSupplierForProduct((int)$productId, $triedSupplierIds, $db);
        if ($backup) break;
    }

    if (!$backup) {
        \App\Helpers\NotificationHelper::add(
            'new_order',
            '⚠️ No More Suppliers Available — Manual Action Required',
            "PO #{$poNumber} timed out and no further backup supplier is available. Distribution request #{$distRequestId} needs manual review.",
            ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
        );
        return;
    }

    // Build new PO
    $last = $db->query("SELECT po_number FROM purchase_orders ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    $nextNum = 1;
    if ($last && preg_match('/PO-(\d+)/', $last['po_number'], $m)) $nextNum = (int)$m[1] + 1;
    $newPoNumber = 'PO-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

    // Get quantity from original PO items
    $stmt = $db->prepare("
        SELECT product_id, quantity_ordered FROM purchase_order_items WHERE purchase_order_id = ?
    ");
    $stmt->execute([$po['id']]);
    $originalItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $subtotal = 0;
    foreach ($originalItems as $item) {
        $subtotal += $item['quantity_ordered'] * $backup['unit_price'];
    }
    $taxGst      = round($subtotal * 0.05, 2);
    $taxQst      = round($subtotal * 0.09975, 2);
    $totalAmount = $subtotal + $taxGst + $taxQst;

    // New 10-minute deadline for the backup supplier
    $newDeadline = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    $db->prepare("
        INSERT INTO purchase_orders
            (po_number, supplier_id, order_date, status, subtotal, tax_gst, tax_qst, tax_amount,
             shipping_cost, total_amount, notes, created_by, distribution_request_id,
             confirmation_deadline, escalation_attempt)
        VALUES (?, ?, CURDATE(), 'sent', ?, ?, ?, ?, 0, ?, ?, 0, ?, ?, ?)
    ")->execute([
        $newPoNumber,
        $backup['supplier_id'],
        $subtotal, $taxGst, $taxQst, $taxGst + $taxQst, $totalAmount,
        "Auto-escalated (10-min timeout): replaces PO #{$poNumber}",
        $distRequestId,
        $newDeadline,
        $escalationAttempt + 1,
    ]);
    $newPoId = $db->lastInsertId();

    // Copy items to the new PO
    $itemStmt = $db->prepare("
        INSERT INTO purchase_order_items (purchase_order_id, product_id, quantity_ordered, unit_cost, total_cost)
        VALUES (?, ?, ?, ?, ?)
    ");
    foreach ($originalItems as $item) {
        $itemStmt->execute([
            $newPoId,
            $item['product_id'],
            $item['quantity_ordered'],
            $backup['unit_price'],
            $item['quantity_ordered'] * $backup['unit_price'],
        ]);
    }

    // Notify the backup supplier
    $urgencyLabel   = $deliveryType === 'express' ? '⚡ EXPRESS' : ($deliveryType === 'same_day' ? '☀️ SAME DAY' : '📅 SCHEDULED');
    $urgencyLabelFr = $deliveryType === 'express' ? '⚡ EXPRESS' : ($deliveryType === 'same_day' ? '☀️ MÊME JOUR' : '📅 PLANIFIÉ');
    \App\Helpers\NotificationHelper::addSupplierNotification(
        $backup['supplier_id'],
        'purchase_order',
        "New PO #{$newPoNumber} — Respond within 10 minutes",
        "{$urgencyLabel}: Please accept or decline PO #{$newPoNumber} within 10 minutes or it will be reassigned.",
        "supplier/orders/view?id={$newPoId}",
        'clipboard-check',
        "Nouveau BC #{$newPoNumber} — Répondez dans 10 minutes",
        "{$urgencyLabelFr} : Veuillez accepter ou refuser BC #{$newPoNumber} dans 10 minutes, sinon il sera réassigné."
    );

    // Admin notification
    \App\Helpers\NotificationHelper::add(
        'new_order',
        "PO Auto-Reassigned — #{$poNumber} → #{$newPoNumber}",
        "PO #{$poNumber} timed out with no response. New PO #{$newPoNumber} sent to {$backup['supplier_name']} (attempt " . ($escalationAttempt + 1) . ").",
        ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'sync', 'priority' => 'normal']
    );

    echo "[" . date('Y-m-d H:i:s') . "] Reassigned: new PO #{$newPoNumber} → {$backup['supplier_name']}\n";
}

/**
 * Check suppliers who accepted a PO but haven't progressed — send nudge reminders.
 * Thresholds (for distribution orders):
 *   accepted → no preparing start after 30 min  → reminder at 30 min
 *   accepted → no preparing start after 55 min  → urgent reminder
 *   preparing → not ready_for_pickup after 60 min → reminder
 *   preparing → not ready_for_pickup after 90 min → urgent reminder
 */
function _checkProgressReminders(PDO $db, string $now): void
{
    // Re-use updated_at as the "last status change" proxy
    $stuck = $db->prepare("
        SELECT po.*,
               s.company_name AS supplier_company,
               s.name         AS supplier_contact,
               s.email        AS supplier_email
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.id
        WHERE po.status IN ('accepted','preparing')
          AND po.distribution_request_id IS NOT NULL
    ");
    $stuck->execute();
    $stuckPOs = $stuck->fetchAll(PDO::FETCH_ASSOC);

    foreach ($stuckPOs as $po) {
        $poId        = (int)$po['id'];
        $poNumber    = $po['po_number'];
        $supplierId  = (int)$po['supplier_id'];
        $status      = $po['status'];
        $supplierName = $po['supplier_company'] ?: $po['supplier_contact'] ?: 'Supplier';

        // Use supplier_accepted_at for 'accepted' stage, updated_at for 'preparing'
        $sinceTs = null;
        if ($status === 'accepted' && !empty($po['supplier_accepted_at'])) {
            $sinceTs = strtotime($po['supplier_accepted_at']);
        } elseif ($status === 'preparing') {
            $sinceTs = strtotime($po['updated_at']);
        }
        if (!$sinceTs) continue;

        $elapsedMins = (time() - $sinceTs) / 60;

        if ($status === 'accepted') {
            // Build a session key: we use notification deduplication via checking recent notifs
            $urgentKey  = "progress_urgent_accepted_{$poId}";
            $normalKey  = "progress_remind_accepted_{$poId}";

            if ($elapsedMins >= 55) {
                // Urgent: 55+ min without starting preparation
                $already = $db->prepare("SELECT COUNT(*) FROM supplier_notifications
                    WHERE supplier_id = ? AND type = 'progress_urgent' AND message LIKE ? AND created_at > NOW() - INTERVAL 2 HOUR");
                $already->execute([$supplierId, "%#{$poNumber}%"]);
                if (!$already->fetchColumn()) {
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        $supplierId, 'progress_urgent',
                        "🚨 Action Required — PO #{$poNumber}",
                        "You accepted PO #{$poNumber} nearly an hour ago but haven't started preparing. Please begin immediately or contact us if there's an issue.",
                        "supplier/orders/view?id={$poId}",
                        'exclamation-triangle',
                        "🚨 Action requise — BC #{$poNumber}",
                        "Vous avez accepté BC #{$poNumber} il y a près d'une heure mais n'avez pas commencé la préparation. Veuillez commencer immédiatement ou nous contacter s'il y a un problème."
                    );
                    echo "[{$now}] URGENT progress reminder sent for accepted PO #{$poNumber} ({$elapsedMins:.0f} min elapsed).\n";
                }
            } elseif ($elapsedMins >= 30) {
                // Normal: 30+ min without starting preparation
                $already = $db->prepare("SELECT COUNT(*) FROM supplier_notifications
                    WHERE supplier_id = ? AND type = 'progress_reminder' AND message LIKE ? AND created_at > NOW() - INTERVAL 2 HOUR");
                $already->execute([$supplierId, "%#{$poNumber}%"]);
                if (!$already->fetchColumn()) {
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        $supplierId, 'progress_reminder',
                        "⏰ Reminder — Start Preparing PO #{$poNumber}",
                        "You accepted PO #{$poNumber} 30+ minutes ago. Please start preparing the items so the driver can be dispatched on time.",
                        "supplier/orders/view?id={$poId}",
                        'clock',
                        "⏰ Rappel — Commencez à préparer BC #{$poNumber}",
                        "Vous avez accepté BC #{$poNumber} il y a plus de 30 minutes. Veuillez commencer à préparer les articles pour que le chauffeur puisse être dépêché à temps."
                    );
                    echo "[{$now}] Progress reminder sent for accepted PO #{$poNumber} ({$elapsedMins:.0f} min elapsed).\n";
                }
            }
        } elseif ($status === 'preparing') {
            if ($elapsedMins >= 90) {
                $already = $db->prepare("SELECT COUNT(*) FROM supplier_notifications
                    WHERE supplier_id = ? AND type = 'progress_urgent' AND message LIKE ? AND created_at > NOW() - INTERVAL 2 HOUR");
                $already->execute([$supplierId, "%#{$poNumber}%"]);
                if (!$already->fetchColumn()) {
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        $supplierId, 'progress_urgent',
                        "🚨 Action Required — PO #{$poNumber} Still Preparing",
                        "PO #{$poNumber} has been in 'Preparing' for over 90 minutes. Please mark it ready for pickup or report an issue immediately.",
                        "supplier/orders/view?id={$poId}",
                        'exclamation-triangle',
                        "🚨 Action requise — BC #{$poNumber} toujours en préparation",
                        "BC #{$poNumber} est en préparation depuis plus de 90 minutes. Veuillez le marquer comme prêt pour la collecte ou signaler un problème immédiatement."
                    );
                    echo "[{$now}] URGENT preparing reminder for PO #{$poNumber} ({$elapsedMins:.0f} min).\n";
                }
            } elseif ($elapsedMins >= 60) {
                $already = $db->prepare("SELECT COUNT(*) FROM supplier_notifications
                    WHERE supplier_id = ? AND type = 'progress_reminder' AND message LIKE ? AND created_at > NOW() - INTERVAL 2 HOUR");
                $already->execute([$supplierId, "%#{$poNumber}%"]);
                if (!$already->fetchColumn()) {
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        $supplierId, 'progress_reminder',
                        "⏰ Reminder — PO #{$poNumber} Ready for Pickup?",
                        "PO #{$poNumber} has been in 'Preparing' for over 60 minutes. If items are ready, please mark it as Ready for Pickup.",
                        "supplier/orders/view?id={$poId}",
                        'truck',
                        "⏰ Rappel — BC #{$poNumber} prêt pour la collecte ?",
                        "BC #{$poNumber} est en préparation depuis plus de 60 minutes. Si les articles sont prêts, veuillez le marquer comme prêt pour la collecte."
                    );
                    echo "[{$now}] Preparing reminder for PO #{$poNumber} ({$elapsedMins:.0f} min).\n";
                }
            }
        }
    }
}

/**
 * Find the next ranked supplier for a given product, excluding already-tried suppliers.
 * Ranks by: active status → nearest (proxy: join on supplier_products) → unit_price asc.
 */
function _getNextSupplierForProduct(int $productId, array $excludeSupplierIds, PDO $db): ?array
{
    $placeholders = $excludeSupplierIds ? implode(',', array_fill(0, count($excludeSupplierIds), '?')) : '0';

    $stmt = $db->prepare("
        SELECT sp.id, sp.supplier_id, sp.unit_price,
               s.company_name AS supplier_name, s.email AS supplier_email
        FROM supplier_products sp
        INNER JOIN suppliers s ON sp.supplier_id = s.id
        WHERE sp.product_id = ?
          AND s.status = 'active'
          AND sp.is_available = 1
          AND sp.supplier_id NOT IN ({$placeholders})
        ORDER BY sp.unit_price ASC
        LIMIT 1
    ");
    $params = array_merge([$productId], $excludeSupplierIds ?: [0]);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}
