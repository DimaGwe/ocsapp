<?php
/**
 * Migration: Per-status timestamps on orders (Backend Requirements Sec. 9)
 *
 * Seller Agreement 5.3.3 / Supplier Agreement 6.5.3 require each order-status
 * transition to be timestamped. OrderController::updateOrderStatus already
 * writes confirmed_at / ready_at / cancelled_at / cancelled_by - but the
 * columns never existed, so those transitions failed and rolled back.
 * This adds the missing columns plus processing_at.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function orderColumnExists(PDO $db, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = 'orders' AND column_name = ?
    ");
    $stmt->execute([$column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    $columns = [
        'confirmed_at'  => "DATETIME NULL DEFAULT NULL AFTER status",
        'processing_at' => "DATETIME NULL DEFAULT NULL AFTER confirmed_at",
        'ready_at'      => "DATETIME NULL DEFAULT NULL AFTER processing_at",
        'cancelled_at'  => "DATETIME NULL DEFAULT NULL AFTER ready_at",
        'cancelled_by'  => "BIGINT UNSIGNED NULL DEFAULT NULL AFTER cancelled_at",
    ];

    foreach ($columns as $name => $definition) {
        if (orderColumnExists($db, $name)) {
            echo "orders.{$name} already exists. Skipping.\n";
            continue;
        }
        $db->exec("ALTER TABLE orders ADD COLUMN {$name} {$definition}");
        echo "orders.{$name} added.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
