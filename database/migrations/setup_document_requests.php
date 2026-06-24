<?php
/**
 * Migration: Document Requests Table
 *
 * Allows admins to formally request specific documents from business accounts.
 * Businesses see pending requests on their documents page and can upload directly against them.
 *
 * Run: php database/migrations/setup_document_requests.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Setting up Document Requests table...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    $db->exec("
        CREATE TABLE IF NOT EXISTS document_requests (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            business_id INT UNSIGNED NOT NULL,
            doc_type VARCHAR(50) NOT NULL COMMENT 'doc_certificate, doc_declaration, or other',
            doc_label VARCHAR(150) NOT NULL COMMENT 'Human-readable label shown to business',
            message TEXT NULL COMMENT 'Optional custom message from admin',
            deadline DATE NULL,
            status ENUM('pending', 'fulfilled', 'cancelled') NOT NULL DEFAULT 'pending',
            requested_by BIGINT UNSIGNED NULL COMMENT 'Admin user id who sent the request',
            fulfilled_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_business_id (business_id),
            INDEX idx_status (status),
            INDEX idx_business_status (business_id, status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "document_requests table created.\n\nDone.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
