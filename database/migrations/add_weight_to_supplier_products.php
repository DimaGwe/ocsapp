<?php
/**
 * Migration: Add weight_kg column to supplier_products table
 * Used for calculating weight-based handling fees ($0.20/kg)
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $check = $db->query("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE()
        AND table_name = 'supplier_products'
        AND column_name = 'weight_kg'
    ");
    if ($check->fetchColumn() > 0) {
        echo "weight_kg column already exists. Skipping.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE supplier_products
        ADD COLUMN weight_kg DECIMAL(10,2) NULL DEFAULT NULL
        AFTER unit_price
    ");

    echo "weight_kg column added to supplier_products successfully.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
