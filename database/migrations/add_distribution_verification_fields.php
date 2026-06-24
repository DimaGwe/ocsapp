<?php
/**
 * Migration: Add Verification Fields to Business Profiles
 *
 * Adds Quebec Legal Identity, document upload, and approval workflow fields.
 * Changes status enum to include 'pending' for new applications under review.
 *
 * Run: php database/migrations/add_distribution_verification_fields.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Adding Distribution Verification Fields...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    // 1. Modify status ENUM to add 'pending'
    echo "Updating status ENUM to include 'pending'...\n";
    $db->exec("
        ALTER TABLE business_profiles
        MODIFY COLUMN status ENUM('pending', 'active', 'suspended') DEFAULT 'pending'
    ");
    echo "  - status ENUM updated.\n";

    // 2. Add Quebec Legal Identity fields
    echo "Adding Quebec Legal Identity fields...\n";
    $columns = [
        "neq_number"                  => "VARCHAR(10) NULL AFTER company_name",
        "legal_name"                  => "VARCHAR(255) NULL AFTER neq_number",
        "operating_names"             => "VARCHAR(500) NULL AFTER legal_name",
        "registered_address_street"   => "VARCHAR(255) NULL AFTER operating_names",
        "registered_address_city"     => "VARCHAR(100) NULL AFTER registered_address_street",
        "registered_address_province" => "VARCHAR(50) NULL AFTER registered_address_city",
        "registered_address_postal"   => "VARCHAR(20) NULL AFTER registered_address_province",
    ];

    foreach ($columns as $col => $def) {
        // Check if column already exists
        $stmt = $db->prepare("
            SELECT COUNT(*) as cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'business_profiles'
              AND COLUMN_NAME = ?
        ");
        $stmt->execute([$col]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] == 0) {
            $db->exec("ALTER TABLE business_profiles ADD COLUMN $col $def");
            echo "  - Added column: $col\n";
        } else {
            echo "  - Column already exists: $col (skipped)\n";
        }
    }

    // 3. Add document upload field
    echo "Adding document upload field...\n";
    $docColumns = [
        "doc_certificate" => "VARCHAR(500) NULL AFTER registered_address_postal",
    ];
    foreach ($docColumns as $col => $def) {
        $stmt = $db->prepare("
            SELECT COUNT(*) as cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'business_profiles'
              AND COLUMN_NAME = ?
        ");
        $stmt->execute([$col]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] == 0) {
            $db->exec("ALTER TABLE business_profiles ADD COLUMN $col $def");
            echo "  - Added column: $col\n";
        } else {
            echo "  - Column already exists: $col (skipped)\n";
        }
    }

    // 4. Add approval workflow fields
    echo "Adding approval workflow fields...\n";
    $workflowColumns = [
        "rejection_reason" => "TEXT NULL AFTER doc_certificate",
        "verified_at"      => "DATETIME NULL AFTER rejection_reason",
        "verified_by"      => "BIGINT UNSIGNED NULL AFTER verified_at",
    ];
    foreach ($workflowColumns as $col => $def) {
        $stmt = $db->prepare("
            SELECT COUNT(*) as cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'business_profiles'
              AND COLUMN_NAME = ?
        ");
        $stmt->execute([$col]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] == 0) {
            $db->exec("ALTER TABLE business_profiles ADD COLUMN $col $def");
            echo "  - Added column: $col\n";
        } else {
            echo "  - Column already exists: $col (skipped)\n";
        }
    }

    // 5. Existing rows that are 'active' stay 'active' — no change needed.
    //    New registrations will default to 'pending'.
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE status = 'active'");
    $existing = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];
    echo "\nExisting active accounts: $existing (not touched)\n";

    echo "\n===========================================\n";
    echo "Migration complete.\n";
    echo "===========================================\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
