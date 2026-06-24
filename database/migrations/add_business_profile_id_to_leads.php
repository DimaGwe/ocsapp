<?php
/**
 * Migration: Add business_profile_id to leads table
 * Links distribution business account registrations to their CRM lead entry
 */

require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Add column
    $db->exec("ALTER TABLE leads ADD COLUMN business_profile_id INT NULL DEFAULT NULL AFTER interest_details");
    echo "Added business_profile_id column to leads table.\n";

    // Add index
    $db->exec("ALTER TABLE leads ADD INDEX idx_leads_business_profile_id (business_profile_id)");
    echo "Added index on business_profile_id.\n";

    // Backfill existing leads: parse "Business Profile ID: #N" from interest_details
    $stmt = $db->query("SELECT id, interest_details FROM leads WHERE interest_type = 'business' AND business_profile_id IS NULL");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $backfilled = 0;
    foreach ($rows as $row) {
        if (preg_match('/Business Profile ID:\s*#(\d+)/i', $row['interest_details'], $m)) {
            $db->prepare("UPDATE leads SET business_profile_id = ? WHERE id = ?")->execute([$m[1], $row['id']]);
            echo "  Backfilled lead #{$row['id']} → business_profile_id = {$m[1]}\n";
            $backfilled++;
        }
    }
    echo "Backfilled {$backfilled} existing lead(s).\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
