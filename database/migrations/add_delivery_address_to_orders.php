<?php
/**
 * Migration: orders.delivery_address
 *
 * Same gap pattern as orders.delivered_at (added earlier this session):
 * CheckoutController writes JSON-encoded address data into this column, and
 * ChargebackHelper (Sec 4.3) reads it back to resolve the delivery zone for
 * the reverse-logistics fee, but no migration file adds it - only ever
 * created by hand directly on staging/prod.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $exists = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'orders' AND column_name = 'delivery_address'
    ");
    $exists->execute();
    if ($exists->fetchColumn() > 0) {
        echo "orders.delivery_address already exists. Skipping.\n";
    } else {
        $db->exec("ALTER TABLE orders ADD COLUMN delivery_address TEXT NULL AFTER delivery_time");
        echo "orders.delivery_address added.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
