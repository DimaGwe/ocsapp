<?php
/**
 * Migration: Self-pickup checkout (orders.fulfillment_type)
 *
 * shops.allow_self_pickup was a seller-settings toggle nothing in checkout ever
 * read - no order-level fulfillment concept existed at all. This is the
 * prerequisite for a real pickup commission rate to ever apply to a real order
 * (see Founding Seller/Supplier Partner Program work, Sec A of that plan).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function spColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!spColumnExists($db, 'orders', 'fulfillment_type')) {
        $db->exec("ALTER TABLE orders ADD COLUMN fulfillment_type ENUM('delivery','pickup') NOT NULL DEFAULT 'delivery' AFTER delivery_fee");
        echo "orders.fulfillment_type added.\n";
    } else {
        echo "orders.fulfillment_type already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
