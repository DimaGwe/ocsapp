<?php
/**
 * Migration: Live NEQ (Registraire des entreprises du Québec) verification
 * as "Option 2", per Jack's direction (2026-08-14):
 *
 *   Option 1 = format validation (already live - preg_match 10-digit check
 *   at registration, DistributionAuthController::validateBusinessData).
 *   Option 2 = a live registry lookup, run automatically as a second
 *   verification layer (format-only can't catch a fake-but-valid-looking
 *   NEQ), with the result routed to an Administrator for confirmation - not
 *   auto-accepted or auto-rejected. "Double tap" = two distinct admin
 *   confirmation steps before the verification is treated as final.
 *
 * No live Registraire API integration exists yet (same situation as the
 * Equifax/D&B credit-bureau gate in CreditHelper::runCreditCheck() - no
 * credentials or verified API shape to build against). This table and the
 * NEQVerificationHelper it backs are real, live plumbing: automatic trigger
 * at registration, admin-facing review UI, two-step confirmation - it just
 * degrades to "flagged for manual review" instead of a live API call until
 * real Registraire API access exists, exactly like the credit-bureau gate
 * already does. Not a fake API call.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $stmt = $db->query("SHOW TABLES LIKE 'business_neq_verifications'");
    if ($stmt->fetch()) {
        echo "business_neq_verifications already exists. Skipping table creation.\n";
    } else {
        $db->exec("
            CREATE TABLE business_neq_verifications (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                business_profile_id INT NOT NULL,
                neq_number VARCHAR(20) NOT NULL,

                -- Lookup outcome
                lookup_status ENUM('api_not_configured','matched','mismatch','not_found','api_error') NOT NULL DEFAULT 'api_not_configured',
                req_legal_name VARCHAR(255) NULL COMMENT 'Legal name returned by the registry lookup, when available',
                req_status VARCHAR(100) NULL COMMENT 'Registry-reported business status (active/inactive/etc), when available',
                raw_response TEXT NULL COMMENT 'Raw API response for admin review, once a live integration exists',
                looked_up_at DATETIME NULL,

                -- Double-tap admin confirmation (two distinct steps, not one click)
                admin_step1_confirmed_at DATETIME NULL,
                admin_step1_confirmed_by INT NULL,
                admin_step2_confirmed_at DATETIME NULL,
                admin_step2_confirmed_by INT NULL,
                final_status ENUM('pending','confirmed','rejected') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,

                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                INDEX idx_business_profile (business_profile_id),
                INDEX idx_final_status (final_status),
                CONSTRAINT fk_neqv_business FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
              COMMENT='Live NEQ registry lookup + double-tap admin confirmation (Sec. 6, Option 2)'
        ");
        echo "business_neq_verifications created.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
