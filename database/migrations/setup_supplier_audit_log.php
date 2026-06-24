<?php

/**
 * Migration: Create supplier_audit_log table
 * Tracks all actions on supplier accounts for audit purposes
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    $db->exec("
        CREATE TABLE IF NOT EXISTS supplier_audit_log (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            supplier_id BIGINT UNSIGNED NOT NULL,
            action VARCHAR(50) NOT NULL,
            details TEXT DEFAULT NULL,
            performed_by INT UNSIGNED DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_supplier (supplier_id),
            INDEX idx_action (action),
            INDEX idx_created (created_at),
            FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "SUCCESS: supplier_audit_log table created.\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
