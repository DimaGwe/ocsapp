<?php
/**
 * Migration: Add purchase order status translations for supplier dashboard
 * Fixes bilingual display of PO status badges (Finding 5 - Test 01)
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$keys = [
    'po_status_draft'      => ['en' => 'Draft',       'fr' => 'Brouillon'],
    'po_status_sent'       => ['en' => 'Sent',         'fr' => 'Envoyé'],
    'po_status_receiving'  => ['en' => 'Receiving',    'fr' => 'En réception'],
    'po_status_completed'  => ['en' => 'Completed',    'fr' => 'Complété'],
    'po_status_cancelled'  => ['en' => 'Cancelled',    'fr' => 'Annulé'],
];

$inserted = 0;
$skipped  = 0;

foreach ($keys as $key => $translations) {
    // Check if key already exists
    $check = $db->prepare("SELECT COUNT(*) FROM translations WHERE `key` = ?");
    $check->execute([$key]);
    if ((int) $check->fetchColumn() > 0) {
        echo "SKIP: {$key} already exists\n";
        $skipped++;
        continue;
    }

    $stmt = $db->prepare("
        INSERT INTO translations (`key`, category, en, fr)
        VALUES (?, 'supplier', ?, ?)
    ");
    $stmt->execute([$key, $translations['en'], $translations['fr']]);
    echo "OK: {$key} => EN: {$translations['en']} | FR: {$translations['fr']}\n";
    $inserted++;
}

echo "\nDone — {$inserted} inserted, {$skipped} skipped.\n";
