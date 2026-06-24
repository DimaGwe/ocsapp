<?php
/**
 * Migration: Add missing supplier nav translation keys
 * Adds: sup_sales_orders, sup_receivables
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();

$keys = [
    ['key' => 'sup_sales_orders',   'cat' => 'supplier', 'en' => 'Sales Orders',                                      'fr' => 'Bons de vente'],
    ['key' => 'sup_receivables',    'cat' => 'supplier', 'en' => 'Receivables',                                       'fr' => 'Comptes clients'],
    ['key' => 'sup_documents',      'cat' => 'supplier', 'en' => 'My Documents',                                      'fr' => 'Mes documents'],
    ['key' => 'sup_emails',         'cat' => 'supplier', 'en' => 'My Emails',                                         'fr' => 'Mes courriels'],
    ['key' => 'sup_messages',       'cat' => 'supplier', 'en' => 'Messages',                                          'fr' => 'Messages'],
    ['key' => 'sup_locked_tooltip', 'cat' => 'supplier', 'en' => 'Available after account approval',                  'fr' => 'Disponible après approbation du compte'],
    ['key' => 'sup_unlock_hint',    'cat' => 'supplier', 'en' => 'Upload your documents to unlock all features.',     'fr' => 'Téléversez vos documents pour débloquer toutes les fonctionnalités.'],
    ['key' => 'sup_go_to_docs',     'cat' => 'supplier', 'en' => 'Go to Documents &rarr;',                            'fr' => 'Aller aux documents &rarr;'],
];

$stmt = $pdo->prepare("
    INSERT INTO translations (`key`, category, en, fr)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)
");

$inserted = 0;
$updated  = 0;

foreach ($keys as $row) {
    $stmt->execute([$row['key'], $row['cat'], $row['en'], $row['fr']]);
    if ($stmt->rowCount() === 1) {
        $inserted++;
        echo "Inserted: {$row['key']}\n";
    } else {
        $updated++;
        echo "Updated:  {$row['key']}\n";
    }
}

echo "\nDone. Inserted: $inserted, Updated: $updated\n";
