<?php
/**
 * Migration: Create supplier_messages table
 * Bidirectional messaging between suppliers and admin
 */

require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

$sql = "
CREATE TABLE IF NOT EXISTS supplier_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT UNSIGNED NOT NULL,
    sender_type ENUM('admin', 'supplier') NOT NULL,
    sender_id INT UNSIGNED NOT NULL COMMENT 'admin user id or supplier id',
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_supplier_id (supplier_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $db->exec($sql);
    echo "✓ supplier_messages table created successfully.\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
