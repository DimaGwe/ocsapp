<?php
/**
 * Migration: Create driver_applications table
 * Run: php database/migrations/setup_driver_applications.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

try {
    $db = Database::getConnection();

    // Check if table already exists
    $check = $db->query("SHOW TABLES LIKE 'driver_applications'");
    if ($check->rowCount() > 0) {
        echo "Table driver_applications already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE driver_applications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                first_name VARCHAR(100) NOT NULL,
                last_name VARCHAR(100) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(20) NOT NULL,
                date_of_birth DATE NOT NULL,
                street_address VARCHAR(255) NOT NULL,
                city VARCHAR(100) NOT NULL,
                province VARCHAR(100) NOT NULL,
                postal_code VARCHAR(10) NOT NULL,
                vehicle_type ENUM('bicycle', 'e-bike', 'scooter', 'car', 'van') NOT NULL DEFAULT 'car',
                license_number VARCHAR(100) NULL,
                license_expiry DATE NULL,
                available_days VARCHAR(100) NOT NULL COMMENT 'comma-separated: mon,tue,wed...',
                preferred_shift VARCHAR(50) NOT NULL DEFAULT 'flexible',
                motivation TEXT NULL,
                previous_experience ENUM('yes', 'no') DEFAULT 'no',
                status ENUM('pending', 'under_review', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,
                reviewed_by BIGINT UNSIGNED NULL,
                reviewed_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE INDEX idx_email (email),
                INDEX idx_status (status),
                INDEX idx_created (created_at),
                FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created driver_applications table.\n";
    }

    // Add tracking_code column to delivery_assignments if not exists
    $cols = $db->query("DESCRIBE delivery_assignments")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('tracking_code', $cols)) {
        $db->exec("ALTER TABLE delivery_assignments ADD COLUMN tracking_code VARCHAR(20) NULL AFTER delivery_type");
        $db->exec("CREATE UNIQUE INDEX idx_tracking_code ON delivery_assignments(tracking_code)");
        echo "Added tracking_code column to delivery_assignments.\n";

        // Generate tracking codes for existing records
        $stmt = $db->query("SELECT id FROM delivery_assignments WHERE tracking_code IS NULL");
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $update = $db->prepare("UPDATE delivery_assignments SET tracking_code = ? WHERE id = ?");
        foreach ($rows as $id) {
            $code = 'OCS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            $update->execute([$code, $id]);
        }
        echo "Generated tracking codes for " . count($rows) . " existing assignments.\n";
    } else {
        echo "tracking_code column already exists. Skipping.\n";
    }

    echo "\nMigration complete!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
