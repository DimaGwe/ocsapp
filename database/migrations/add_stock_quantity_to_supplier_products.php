<?php
/**
 * Migration: Add stock_quantity column to supplier_products
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM supplier_products LIKE 'stock_quantity'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE supplier_products ADD COLUMN stock_quantity INT NULL DEFAULT NULL AFTER minimum_order_quantity");
        echo "Added stock_quantity column to supplier_products.\n";
    } else {
        echo "stock_quantity column already exists.\n";
    }

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
