<?php
/**
 * Migration: Create supplier_pickup_requests table
 * Enables suppliers to schedule pickups for accepted purchase orders (Finding 8 - Test 01)
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// Check if table exists
$check = $db->query("SHOW TABLES LIKE 'supplier_pickup_requests'");
if ($check->rowCount() > 0) {
    echo "Table supplier_pickup_requests already exists. Skipping.\n";
    exit(0);
}

$db->exec("
    CREATE TABLE supplier_pickup_requests (
        id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        supplier_id             BIGINT UNSIGNED NOT NULL,
        purchase_order_ids      JSON NOT NULL COMMENT 'Array of purchase_orders.id values',
        pickup_address          TEXT NOT NULL,
        requested_date          DATE NOT NULL,
        requested_time_from     TIME NOT NULL,
        requested_time_to       TIME NOT NULL,
        notes                   TEXT NULL,
        status                  ENUM('pending','scheduled','completed','cancelled') NOT NULL DEFAULT 'pending',
        assigned_driver_id      BIGINT UNSIGNED NULL,
        delivery_assignment_id  BIGINT UNSIGNED NULL,
        admin_notes             TEXT NULL,
        scheduled_at            DATETIME NULL,
        cancelled_at            DATETIME NULL,
        created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_supplier (supplier_id),
        INDEX idx_status (status),
        INDEX idx_requested_date (requested_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");

echo "OK: Created supplier_pickup_requests table.\n";
