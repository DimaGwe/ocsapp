<?php
/**
 * Migration: delivery_status_history.delivery_id NULLable.
 * The table is written for order-level events too (checkout, seller status changes, buyer cancel,
 * admin mark-paid, payments) with no delivery assignment, but delivery_id was NOT NULL with a FK to
 * delivery_assignments, so those inserts failed. Inside a transaction that rolled the whole action
 * back: sellers could not change any order status, buyers could not cancel (found 2026-09-28,
 * staging and prod). The FK still applies when a delivery is set.
 *
 * Run: php database/migrations/make_delivery_status_history_delivery_id_nullable.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: make_delivery_status_history_delivery_id_nullable\n";

try {
    $col = $db->query("SHOW COLUMNS FROM delivery_status_history LIKE 'delivery_id'")->fetch(PDO::FETCH_ASSOC);
    if (!$col) {
        echo "  [SKIP] delivery_id column not found\n";
    } elseif (strtoupper($col['Null']) === 'YES') {
        echo "  [SKIP] delivery_id is already NULLable\n";
    } else {
        $db->exec("ALTER TABLE delivery_status_history
                   MODIFY delivery_id BIGINT UNSIGNED NULL COMMENT 'Delivery assignment (NULL for order-level events)'");
        echo "  [OK] delivery_id is now NULLable\n";
    }
    echo "\nMigration complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
