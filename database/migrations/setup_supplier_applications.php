<?php
/**
 * Migration: Create supplier_applications table
 * Stores supplier application forms submitted via /supplier/apply
 * Each application creates a corresponding lead in the CRM
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    echo "Setting up supplier_applications table...\n\n";

    // Check if table already exists
    $check = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'supplier_applications'");
    if ($check->fetchColumn() > 0) {
        echo "supplier_applications table already exists. Skipping.\n";
        exit(0);
    }

    $db->exec("
        CREATE TABLE supplier_applications (
            id INT AUTO_INCREMENT PRIMARY KEY,

            -- Business Owner Info
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            business_name VARCHAR(255) NOT NULL,

            -- Quebec Legal Identity
            neq_number VARCHAR(10) NULL,
            legal_name VARCHAR(255) NULL,
            operating_names TEXT NULL,
            registered_address_street VARCHAR(255) NULL,
            registered_address_city VARCHAR(100) NULL,
            registered_address_province VARCHAR(50) DEFAULT 'Quebec',
            registered_address_postal VARCHAR(10) NULL,

            -- Documents (file paths)
            doc_certificate_incorporation VARCHAR(500) NULL,
            doc_declaration_registration VARCHAR(500) NULL,
            doc_enterprise_register VARCHAR(500) NULL,

            -- Application Status
            status ENUM('pending', 'under_review', 'approved', 'rejected') DEFAULT 'pending',
            lead_id INT NULL,
            reviewed_by INT NULL,
            reviewed_at DATETIME NULL,
            admin_notes TEXT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_status (status),
            INDEX idx_email (email),
            INDEX idx_neq (neq_number),
            INDEX idx_lead_id (lead_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "supplier_applications table created successfully.\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
