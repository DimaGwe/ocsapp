<?php
/**
 * Migration: Vehicle Registry
 * - Creates vehicles table
 * - Adds current_vehicle_id to driver_availability
 * Run: php database/migrations/setup_vehicle_registry.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

try {
    $db = Database::getConnection();

    // 1. Create vehicles table
    $check = $db->query("SHOW TABLES LIKE 'vehicles'");
    if ($check->rowCount() > 0) {
        echo "Table vehicles already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE vehicles (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                driver_id BIGINT UNSIGNED NULL,
                vehicle_type ENUM('bicycle', 'e-bike', 'scooter', 'motorcycle', 'car', 'van') NOT NULL DEFAULT 'car',
                make VARCHAR(100) NULL,
                model VARCHAR(100) NULL,
                year INT NULL,
                plate_number VARCHAR(50) NULL,
                color VARCHAR(50) NULL,
                insurance_expiry DATE NULL,
                status ENUM('active', 'maintenance', 'retired') NOT NULL DEFAULT 'active',
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE INDEX idx_plate (plate_number),
                INDEX idx_driver (driver_id),
                INDEX idx_status (status),
                FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created vehicles table.\n";
    }

    // 2. Add current_vehicle_id to driver_availability
    $cols = $db->query("DESCRIBE driver_availability")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('current_vehicle_id', $cols)) {
        $db->exec("ALTER TABLE driver_availability ADD COLUMN current_vehicle_id INT UNSIGNED NULL AFTER zone_id");
        echo "Added current_vehicle_id to driver_availability.\n";
    } else {
        echo "current_vehicle_id already exists. Skipping.\n";
    }

    echo "\nVehicle registry migration complete!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
