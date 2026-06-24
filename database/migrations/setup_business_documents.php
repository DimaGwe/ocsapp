<?php
/**
 * Migration: Business Documents Table
 *
 * Tracks individual document uploads for business/distribution accounts
 * with per-document verification status, rejection reason, and audit trail.
 *
 * Run: php database/migrations/setup_business_documents.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Setting up Business Documents table...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    $db->exec("
        CREATE TABLE IF NOT EXISTS business_documents (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            business_id INT UNSIGNED NOT NULL,
            doc_type VARCHAR(50) NOT NULL COMMENT 'doc_certificate or doc_declaration',
            doc_label VARCHAR(150) NOT NULL COMMENT 'Human-readable label shown in admin',
            file_path VARCHAR(500) NOT NULL,
            status ENUM('pending', 'verified', 'rejected') NOT NULL DEFAULT 'pending',
            rejection_reason TEXT NULL,
            verified_by BIGINT UNSIGNED NULL COMMENT 'admin user id who actioned this',
            verified_at TIMESTAMP NULL,
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_business_id (business_id),
            INDEX idx_status (status),
            INDEX idx_doc_type (doc_type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "business_documents table created.\n";

    // Back-fill any existing documents already stored in business_profiles columns
    $existing = $db->query("
        SELECT id, doc_certificate, doc_declaration
        FROM business_profiles
        WHERE doc_certificate IS NOT NULL OR doc_declaration IS NOT NULL
    ")->fetchAll(PDO::FETCH_ASSOC);

    $inserted = 0;
    foreach ($existing as $row) {
        foreach (['doc_certificate' => "Certificat d'incorporation / Certificate of Incorporation", 'doc_declaration' => "Déclaration d'immatriculation"] as $col => $label) {
            if (!empty($row[$col])) {
                // Only insert if not already tracked
                $check = $db->prepare("SELECT id FROM business_documents WHERE business_id = ? AND doc_type = ? LIMIT 1");
                $check->execute([$row['id'], $col]);
                if (!$check->fetchColumn()) {
                    $db->prepare("
                        INSERT INTO business_documents (business_id, doc_type, doc_label, file_path, status, uploaded_at)
                        VALUES (?, ?, ?, ?, 'pending', NOW())
                    ")->execute([$row['id'], $col, $label, $row[$col]]);
                    $inserted++;
                }
            }
        }
    }

    echo "Back-filled {$inserted} existing document(s).\n";
    echo "\nDone.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
