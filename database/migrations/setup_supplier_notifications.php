<?php
/**
 * Migration: Create supplier_notifications table
 * Mirrors admin_notifications but for the supplier portal
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    echo "Setting up Supplier Notifications table...\n\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS supplier_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            supplier_id INT UNSIGNED NOT NULL,
            type VARCHAR(50) NOT NULL COMMENT 'notification type: purchase_order, document, account, system',
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(500) NULL COMMENT 'relative URL to navigate when clicked',
            icon VARCHAR(50) DEFAULT 'bell' COMMENT 'FontAwesome icon name without fa- prefix',
            is_read TINYINT(1) DEFAULT 0,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_supplier_id (supplier_id),
            INDEX idx_is_read (is_read),
            INDEX idx_type (type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done - supplier_notifications table created\n";

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
