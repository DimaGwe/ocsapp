<?php
/**
 * Migration: Create driver_compliance_docs table
 * Stores the 5 compliance documents required for every contract driver:
 *   1. Class 5 Driver's License
 *   2. SAAQ Driving Record (within 30 days, ≤3 demerit points)
 *   3. Proof of Commercial Insurance (COI)
 *   4. Vehicle Registration
 *   5. Proof of Work Authorization
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = \Database::getConnection();

try {
    echo "Creating driver_compliance_docs table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS `driver_compliance_docs` (
            `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `application_id` BIGINT UNSIGNED NOT NULL,
            `driver_id`      BIGINT UNSIGNED NOT NULL,
            `doc_type`       ENUM(
                                 'class5_license',
                                 'saaq_record',
                                 'commercial_insurance',
                                 'vehicle_registration',
                                 'work_authorization'
                             ) NOT NULL,
            `status`         ENUM('not_uploaded','not_required','uploaded','verified','flagged')
                             NOT NULL DEFAULT 'not_uploaded',
            `file_path`      VARCHAR(500) NULL,
            `doc_date`       DATE NULL COMMENT 'Date printed on the document',
            `doc_subtype`    VARCHAR(100) NULL COMMENT 'e.g. work permit type, insurance type',
            `admin_notes`    TEXT NULL,
            `uploaded_at`    TIMESTAMP NULL,
            `verified_at`    TIMESTAMP NULL,
            `verified_by`    BIGINT UNSIGNED NULL,
            `created_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_app_doc` (`application_id`, `doc_type`),
            KEY `idx_driver_id` (`driver_id`),
            KEY `idx_status`    (`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "  + table driver_compliance_docs created\n";

    // Create upload directory
    $uploadDir = __DIR__ . '/../../storage/uploads/compliance_docs';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0750, true);
        file_put_contents($uploadDir . '/.htaccess', "Deny from all\n");
        echo "  + created storage/uploads/compliance_docs/\n";
    } else {
        echo "  = upload directory already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
