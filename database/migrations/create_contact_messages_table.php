<?php
/**
 * Migration: Create contact_messages table
 * Backs the /contact form. Table never existed on staging/prod - every submission
 * has been silently failing (PageController::submitContact() catches the resulting
 * PDOException and returns a generic "failed to send" message). Expanded to match
 * the 2026-09-08 ecosystem-standard contact form redesign (phone, contact method,
 * role/Central, organization, priority, reference, privacy consent).
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(150) NOT NULL,
            email VARCHAR(190) NOT NULL,
            phone VARCHAR(30) NULL,
            contact_method VARCHAR(20) NULL COMMENT 'Email, Phone, Either',
            role VARCHAR(50) NULL COMMENT 'which Central / relationship to OCSAPP',
            organization VARCHAR(190) NULL,
            subject VARCHAR(50) NOT NULL DEFAULT 'General Inquiry',
            priority VARCHAR(20) NOT NULL DEFAULT 'Normal',
            reference_number VARCHAR(100) NULL,
            message TEXT NOT NULL,
            privacy_consent TINYINT(1) NOT NULL DEFAULT 0,
            language VARCHAR(5) NOT NULL DEFAULT 'fr',
            status ENUM('new', 'in_progress', 'resolved') NOT NULL DEFAULT 'new',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_subject (subject),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done - contact_messages table created (or already existed)\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
