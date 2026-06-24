<?php
/**
 * Migration: Add Agreement Confirmation Fields to Business Profiles
 *
 * Adds tracking for when a distribution client agrees to the Distribution
 * Service Agreement, including the IP address and the legal_content version
 * they agreed to (for legal proof).
 *
 * Run: php database/migrations/add_business_agreement_fields.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Adding Business Agreement Fields...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    $columns = [
        'agreement_agreed_at' => 'TIMESTAMP NULL AFTER verified_by',
        'agreement_ip'        => 'VARCHAR(45) NULL AFTER agreement_agreed_at',
        'agreement_version'   => 'INT UNSIGNED NULL AFTER agreement_ip',
    ];

    foreach ($columns as $col => $def) {
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

    echo "\n===========================================\n";
    echo "Migration complete.\n";
    echo "===========================================\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
