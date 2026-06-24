<?php
/**
 * Migration: B2B Distribution Flow — delivery type, confirmation windows, supplier escalation
 *
 * Adds:
 * - distribution_requests.delivery_type, scheduled_date/time columns
 * - distribution_requests.awaiting_supplier + awaiting_payment status values
 * - purchase_orders.confirmation_deadline, ready_by_time, supplier_declined_at, escalation_attempt
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// Helper: check if column exists
function columnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    // ── distribution_requests: delivery type & scheduling ────────────────────
    if (!columnExists($db, 'distribution_requests', 'delivery_type')) {
        $db->exec("ALTER TABLE distribution_requests
            ADD COLUMN delivery_type ENUM('express','scheduled') NOT NULL DEFAULT 'scheduled'
                COMMENT 'express = same-day, scheduled = specific date/window'
                AFTER business_profile_id");
        echo "✓ distribution_requests.delivery_type added\n";
    } else {
        echo "– distribution_requests.delivery_type already exists, skipping\n";
    }

    if (!columnExists($db, 'distribution_requests', 'scheduled_date')) {
        $db->exec("ALTER TABLE distribution_requests
            ADD COLUMN scheduled_date DATE NULL
                COMMENT 'Target delivery date for scheduled orders'
                AFTER delivery_type");
        echo "✓ distribution_requests.scheduled_date added\n";
    } else {
        echo "– distribution_requests.scheduled_date already exists, skipping\n";
    }

    if (!columnExists($db, 'distribution_requests', 'scheduled_time_from')) {
        $db->exec("ALTER TABLE distribution_requests
            ADD COLUMN scheduled_time_from TIME NULL
                COMMENT 'Start of delivery window (e.g. 14:00)'
                AFTER scheduled_date");
        echo "✓ distribution_requests.scheduled_time_from added\n";
    } else {
        echo "– distribution_requests.scheduled_time_from already exists, skipping\n";
    }

    if (!columnExists($db, 'distribution_requests', 'scheduled_time_to')) {
        $db->exec("ALTER TABLE distribution_requests
            ADD COLUMN scheduled_time_to TIME NULL
                COMMENT 'End of delivery window (e.g. 16:00)'
                AFTER scheduled_time_from");
        echo "✓ distribution_requests.scheduled_time_to added\n";
    } else {
        echo "– distribution_requests.scheduled_time_to already exists, skipping\n";
    }

    // ── distribution_requests: expand status ENUM ────────────────────────────
    $db->exec("
        ALTER TABLE distribution_requests
        MODIFY COLUMN status ENUM(
            'draft','submitted','pending','approved','awaiting_supplier','awaiting_payment',
            'quoted','pending_payment','paid','procurement','processing',
            'in_transit','ready','delivered','completed','cancelled','expired'
        ) NOT NULL DEFAULT 'draft'
    ");
    echo "✓ distribution_requests: awaiting_supplier + awaiting_payment added to status ENUM\n";

    // ── purchase_orders: confirmation window + escalation tracking ───────────
    if (!columnExists($db, 'purchase_orders', 'confirmation_deadline')) {
        $db->exec("ALTER TABLE purchase_orders
            ADD COLUMN confirmation_deadline DATETIME NULL
                COMMENT '2h for express orders, 24h for scheduled'
                AFTER distribution_request_id");
        echo "✓ purchase_orders.confirmation_deadline added\n";
    } else {
        echo "– purchase_orders.confirmation_deadline already exists, skipping\n";
    }

    if (!columnExists($db, 'purchase_orders', 'ready_by_time')) {
        $db->exec("ALTER TABLE purchase_orders
            ADD COLUMN ready_by_time DATETIME NULL
                COMMENT 'Supplier committed ready-by time (express orders)'
                AFTER confirmation_deadline");
        echo "✓ purchase_orders.ready_by_time added\n";
    } else {
        echo "– purchase_orders.ready_by_time already exists, skipping\n";
    }

    if (!columnExists($db, 'purchase_orders', 'supplier_declined_at')) {
        // Find position — after supplier_accepted_at if it exists, else just add
        $afterClause = columnExists($db, 'purchase_orders', 'supplier_accepted_at')
            ? 'AFTER supplier_accepted_at'
            : '';
        $db->exec("ALTER TABLE purchase_orders
            ADD COLUMN supplier_declined_at TIMESTAMP NULL $afterClause");
        echo "✓ purchase_orders.supplier_declined_at added\n";
    } else {
        echo "– purchase_orders.supplier_declined_at already exists, skipping\n";
    }

    if (!columnExists($db, 'purchase_orders', 'escalation_attempt')) {
        $db->exec("ALTER TABLE purchase_orders
            ADD COLUMN escalation_attempt TINYINT UNSIGNED NOT NULL DEFAULT 0
                COMMENT '0=original supplier, 1=first backup, 2=second backup (admin takes over after 2)'
                AFTER supplier_declined_at");
        echo "✓ purchase_orders.escalation_attempt added\n";
    } else {
        echo "– purchase_orders.escalation_attempt already exists, skipping\n";
    }

    echo "\nMigration complete.\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
