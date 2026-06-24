<?php
/**
 * Migration: Add Email Verification to Business Profiles
 *
 * - Adds 'unverified' as the first status (new registrations start here)
 * - Adds email_verification_code, email_verification_expires_at, email_verification_attempts
 *
 * Run: php database/migrations/add_email_verification_to_business_profiles.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "==========================================================\n";
echo "Adding Email Verification to Business Profiles...\n";
echo "==========================================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Extend status ENUM to include 'unverified' as the first value
    echo "Updating status ENUM to include 'unverified'...\n";
    $db->exec("
        ALTER TABLE business_profiles
        MODIFY COLUMN status ENUM('unverified','pending','active','suspended') NOT NULL DEFAULT 'unverified'
    ");
    echo "  - status ENUM updated.\n\n";

    // 2. Add verification columns (idempotent checks)
    $newColumns = [
        'email_verification_code'         => "VARCHAR(6) NULL AFTER doc_certificate",
        'email_verification_expires_at'   => "DATETIME NULL AFTER email_verification_code",
        'email_verification_attempts'     => "TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER email_verification_expires_at",
    ];

    foreach ($newColumns as $col => $def) {
        $stmt = $db->prepare("
            SELECT COUNT(*) as cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'business_profiles'
              AND COLUMN_NAME  = ?
        ");
        $stmt->execute([$col]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] == 0) {
            $db->exec("ALTER TABLE business_profiles ADD COLUMN $col $def");
            echo "  - Added column: $col\n";
        } else {
            echo "  - Column already exists: $col (skipped)\n";
        }
    }

    // 3. Existing active/pending accounts are already verified — leave them as-is.
    $stmt = $db->query("
        SELECT status, COUNT(*) as cnt
        FROM business_profiles
        GROUP BY status
    ");
    echo "\nCurrent status distribution:\n";
    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
        echo "  {$row['status']}: {$row['cnt']}\n";
    }

    echo "\n==========================================================\n";
    echo "Migration complete.\n";
    echo "==========================================================\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
