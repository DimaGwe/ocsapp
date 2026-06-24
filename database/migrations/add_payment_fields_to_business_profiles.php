<?php
/**
 * Migration: Add Payment Preference Fields to Business Profiles
 *
 * Adds:
 * - po_number: standing purchase order number
 * - payment_notes: free-text billing/EFT notes
 *
 * Run: php database/migrations/add_payment_fields_to_business_profiles.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Adding Payment Fields to business_profiles...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    $columns = [
        'po_number'      => "VARCHAR(100) NULL AFTER notes",
        'payment_notes'  => "TEXT NULL AFTER po_number",
    ];

    foreach ($columns as $col => $def) {
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

    echo "\n===========================================\n";
    echo "Migration complete.\n";
    echo "===========================================\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
