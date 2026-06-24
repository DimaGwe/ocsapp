<?php
/**
 * Migration: Create email_log table
 * Tracks all outgoing emails sent through EmailHelper
 *
 * Run: php database/migrations/setup_email_log.php
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    echo "=== Setting up Email Log table ===\n\n";

    // Create email_log table
    $db->exec("
        CREATE TABLE IF NOT EXISTS email_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            recipient_email VARCHAR(255) NOT NULL,
            recipient_name VARCHAR(255) NULL,
            subject VARCHAR(500) NOT NULL,
            body MEDIUMTEXT NULL,
            email_type VARCHAR(100) NULL COMMENT 'Category: supplier_approved, order_confirmation, verification_reminder, etc.',
            status ENUM('sent', 'failed', 'test_mode') NOT NULL DEFAULT 'sent',
            error_message TEXT NULL,
            related_type VARCHAR(50) NULL COMMENT 'Entity type: supplier, user, order, etc.',
            related_id INT NULL COMMENT 'Entity ID',
            sender_email VARCHAR(255) NULL,
            sender_name VARCHAR(255) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_recipient (recipient_email),
            INDEX idx_email_type (email_type),
            INDEX idx_status (status),
            INDEX idx_related (related_type, related_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created email_log table.\n";

    echo "\n=== Done! ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
