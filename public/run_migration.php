<?php
/**
 * Web-accessible migration runner
 * Access via: https://ocsapp.ca/run_migration.php?key=migrate123
 */

// Simple security - remove this file after running!
$securityKey = 'migrate123';
if (!isset($_GET['key']) || $_GET['key'] !== $securityKey) {
    die('Unauthorized');
}

// Load database connection
require_once __DIR__ . '/../bootstrap/init.php';

echo "<pre>";
echo "=================================\n";
echo "VENDOR SYSTEM MIGRATION\n";
echo "=================================\n\n";

try {
    $db = \Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERR_EXCEPTION);

    echo "✓ Database connected\n\n";

    // Check if vendor_invites table exists
    $stmt = $db->query("SHOW TABLES LIKE 'vendor_invites'");
    if ($stmt->rowCount() > 0) {
        echo "⚠ vendor_invites table already exists. Skipping...\n";
    } else {
        echo "Creating vendor_invites table...\n";

        $sql = "
        CREATE TABLE IF NOT EXISTS `vendor_invites` (
          `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          `code` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Unique invite code',
          `created_by_admin_id` INT UNSIGNED NOT NULL,
          `note` TEXT NULL COMMENT 'Admin note about this invite',
          `max_uses` INT DEFAULT 1 COMMENT 'How many times this code can be used',
          `uses_count` INT DEFAULT 0 COMMENT 'How many times it has been used',
          `expires_at` TIMESTAMP NULL COMMENT 'When this invite expires',
          `used_by_vendor_id` INT UNSIGNED NULL COMMENT 'Vendor who used this invite (first use)',
          `used_at` TIMESTAMP NULL COMMENT 'When it was first used',
          `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
          `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          FOREIGN KEY (`created_by_admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
          FOREIGN KEY (`used_by_vendor_id`) REFERENCES `vendors`(`id`) ON DELETE SET NULL,
          INDEX `idx_code` (`code`),
          INDEX `idx_created_by` (`created_by_admin_id`),
          INDEX `idx_expires_at` (`expires_at`),
          INDEX `idx_uses` (`uses_count`, `max_uses`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        $db->exec($sql);
        echo "✓ vendor_invites table created successfully!\n\n";
    }

    // Verify tables
    echo "Verifying vendor system tables...\n";
    echo "==================================\n";

    $tables = ['vendors', 'vendor_products', 'vendor_orders', 'vendor_invites'];
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "✓ $table exists ($count records)\n";
        } else {
            echo "✗ $table NOT FOUND\n";
        }
    }

    echo "\n=================================\n";
    echo "MIGRATION COMPLETED SUCCESSFULLY!\n";
    echo "=================================\n";
    echo "\n⚠ IMPORTANT: Delete this file (run_migration.php) for security!\n";

} catch (PDOException $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
}

echo "</pre>";
