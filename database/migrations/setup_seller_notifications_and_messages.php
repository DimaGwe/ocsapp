<?php
/**
 * Migration: Create seller_notifications and seller_messages tables
 * Mirrors supplier_notifications / supplier_messages, but keyed by seller_id (users.id),
 * since sellers are regular platform users, not a separate identity table.
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS seller_notifications (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            seller_id BIGINT UNSIGNED NOT NULL COMMENT 'users.id of the seller',
            type VARCHAR(50) NOT NULL COMMENT 'notification type: order, message, account, system',
            title VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            link VARCHAR(500) NULL COMMENT 'relative URL to navigate when clicked',
            icon VARCHAR(50) DEFAULT 'bell' COMMENT 'FontAwesome icon name without fa- prefix',
            is_read TINYINT(1) DEFAULT 0,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_seller_id (seller_id),
            INDEX idx_is_read (is_read),
            INDEX idx_type (type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done - seller_notifications table created (or already existed)\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS seller_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            seller_id BIGINT UNSIGNED NOT NULL COMMENT 'users.id of the seller',
            sender_type ENUM('admin', 'seller') NOT NULL,
            sender_id BIGINT UNSIGNED NOT NULL COMMENT 'admin user id or seller (users.id)',
            message TEXT NOT NULL,
            is_read TINYINT(1) DEFAULT 0,
            read_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_seller_id (seller_id),
            INDEX idx_is_read (is_read),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done - seller_messages table created (or already existed)\n";

    echo "\nMigration completed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
