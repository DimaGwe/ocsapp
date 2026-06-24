<?php
/**
 * Migration: Add banking/payment info fields to suppliers table
 * Supports EFT, Interac e-Transfer, and cheque payment preferences (Finding 6 - Test 01)
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$columns = [
    'payment_preference'   => "ENUM('eft','interac','cheque') DEFAULT NULL",
    'bank_name'            => "VARCHAR(100) DEFAULT NULL",
    'bank_transit'         => "VARCHAR(10) DEFAULT NULL",
    'bank_institution'     => "VARCHAR(5) DEFAULT NULL",
    'bank_account'         => "VARCHAR(20) DEFAULT NULL",
    'bank_account_holder'  => "VARCHAR(255) DEFAULT NULL",
    'bank_account_type'    => "ENUM('chequing','savings') DEFAULT NULL",
    'interac_email'        => "VARCHAR(255) DEFAULT NULL",
];

// Check which columns already exist
$existingCols = [];
$result = $db->query("SHOW COLUMNS FROM suppliers");
foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $col) {
    $existingCols[] = $col['Field'];
}

$added   = 0;
$skipped = 0;
$after   = 'tax_number';

foreach ($columns as $colName => $colDef) {
    if (in_array($colName, $existingCols)) {
        echo "SKIP: {$colName} already exists\n";
        $skipped++;
    } else {
        $db->exec("ALTER TABLE suppliers ADD COLUMN `{$colName}` {$colDef} AFTER `{$after}`");
        echo "OK: Added {$colName}\n";
        $added++;
    }
    $after = $colName;
}

echo "\nDone — {$added} added, {$skipped} skipped.\n";
