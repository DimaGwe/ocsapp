<?php
/**
 * Migration: Create business_messages table
 * Bidirectional messaging between distribution businesses and admin
 */

require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS business_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    business_id BIGINT UNSIGNED NOT NULL,
    sender_type ENUM('admin', 'business') NOT NULL,
    sender_id BIGINT UNSIGNED NOT NULL COMMENT 'admin user id or business_profiles id',
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_business_id (business_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "✓ business_messages table created successfully.\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
