<?php
/**
 * Admin Notifications Database Setup
 * Creates the admin_notifications table for system notifications
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    echo "Setting up Admin Notifications table...\n\n";

    // Admin Notifications Table
    echo "Creating admin_notifications table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL COMMENT 'notification type: account_lockout, new_user, seller_application, order, system',
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            data JSON NULL COMMENT 'additional context data in JSON format',
            link VARCHAR(500) NULL COMMENT 'URL to navigate when clicked',
            icon VARCHAR(50) DEFAULT 'bell' COMMENT 'FontAwesome icon name without fa- prefix',
            priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
            is_read TINYINT(1) DEFAULT 0,
            read_at TIMESTAMP NULL,
            read_by BIGINT UNSIGNED NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_is_read (is_read),
            INDEX idx_type (type),
            INDEX idx_priority (priority),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done - admin_notifications table created\n\n";

    echo "All Admin Notification tables created successfully!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
