<?php
/**
 * Migration: GPS Tracking Phase 3
 * - Creates driver_location_log table
 * - Adds last_latitude/longitude/location_update to delivery_assignments
 * Run: php database/migrations/setup_gps_tracking_phase3.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

try {
    $db = Database::getConnection();

    // 1. Create driver_location_log table
    $check = $db->query("SHOW TABLES LIKE 'driver_location_log'");
    if ($check->rowCount() > 0) {
        echo "Table driver_location_log already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE driver_location_log (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                driver_id BIGINT UNSIGNED NOT NULL,
                delivery_id BIGINT UNSIGNED NULL,
                latitude DECIMAL(10, 8) NOT NULL,
                longitude DECIMAL(11, 8) NOT NULL,
                accuracy FLOAT NULL COMMENT 'GPS accuracy in meters',
                heading INT NULL COMMENT 'Direction 0-360 degrees',
                speed FLOAT NULL COMMENT 'Speed in km/h',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_driver_time (driver_id, created_at),
                INDEX idx_delivery_time (delivery_id, created_at),
                FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (delivery_id) REFERENCES delivery_assignments(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created driver_location_log table.\n";
    }

    // 2. Add last_latitude, last_longitude, last_location_update to delivery_assignments
    $cols = $db->query("DESCRIBE delivery_assignments")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('last_latitude', $cols)) {
        $db->exec("ALTER TABLE delivery_assignments ADD COLUMN last_latitude DECIMAL(10, 8) NULL AFTER tracking_code");
        echo "Added last_latitude to delivery_assignments.\n";
    } else {
        echo "last_latitude already exists. Skipping.\n";
    }

    if (!in_array('last_longitude', $cols)) {
        $db->exec("ALTER TABLE delivery_assignments ADD COLUMN last_longitude DECIMAL(11, 8) NULL AFTER last_latitude");
        echo "Added last_longitude to delivery_assignments.\n";
    } else {
        echo "last_longitude already exists. Skipping.\n";
    }

    if (!in_array('last_location_update', $cols)) {
        $db->exec("ALTER TABLE delivery_assignments ADD COLUMN last_location_update DATETIME NULL AFTER last_longitude");
        echo "Added last_location_update to delivery_assignments.\n";
    } else {
        echo "last_location_update already exists. Skipping.\n";
    }

    echo "\nGPS tracking migration complete!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
