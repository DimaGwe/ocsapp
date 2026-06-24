<?php
/**
 * Migration: Add distribution_request_id to purchase_orders
 * Links auto-created POs back to the business distribution request that triggered them.
 *
 * Run: php database/migrations/add_distribution_request_to_purchase_orders.php
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM purchase_orders LIKE 'distribution_request_id'");
    if ($stmt->fetch()) {
        echo "Column distribution_request_id already exists — skipping.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE purchase_orders
        ADD COLUMN distribution_request_id INT NULL DEFAULT NULL
            COMMENT 'Set when PO is auto-created from a business distribution request',
        ADD INDEX idx_distribution_request (distribution_request_id)
    ");

    echo "Done — distribution_request_id added to purchase_orders.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
