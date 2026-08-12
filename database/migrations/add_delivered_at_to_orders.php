<?php
/**
 * Migration: orders.delivered_at
 *
 * Referenced throughout DriverApiController (onDeliveryCompleted,
 * orderOutcome, updateOrderStatus) but no migration file adds it - same
 * pattern as several other orders columns (driver_id, driver_payout,
 * delivery_address) found missing from migration history earlier this
 * project: added by hand directly on staging/prod at some point, never
 * captured in a migration file, so local dev never got it. Adding it here
 * since ClaimHelper (Sec 4) needs to read it to compute the Track A 14-day
 * return window deadline.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $exists = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'orders' AND column_name = 'delivered_at'
    ");
    $exists->execute();
    if ($exists->fetchColumn() > 0) {
        echo "orders.delivered_at already exists. Skipping.\n";
    } else {
        $db->exec("ALTER TABLE orders ADD COLUMN delivered_at DATETIME NULL AFTER status");
        echo "orders.delivered_at added.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
