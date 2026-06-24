<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

// Find all tables with supplier_id column
$stmt = $db->query("SELECT TABLE_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND COLUMN_NAME = 'supplier_id'");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Tables with supplier_id column:\n";
foreach ($rows as $r) { echo "  - " . $r['TABLE_NAME'] . "\n"; }

// Check for foreign keys referencing suppliers
$stmt = $db->query("SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = 'suppliers'");
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nForeign keys referencing suppliers table:\n";
foreach ($fks as $fk) { echo "  - " . $fk['TABLE_NAME'] . "." . $fk['COLUMN_NAME'] . "\n"; }

// Show suppliers table columns
$stmt = $db->query("DESCRIBE suppliers");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nSuppliers table columns:\n";
foreach ($cols as $c) { echo "  - " . $c['Field'] . " (" . $c['Type'] . ") " . ($c['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . "\n"; }

// Count related data for sample
$stmt = $db->query("SELECT id, name, company_name FROM suppliers LIMIT 5");
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nSample suppliers and related data:\n";
foreach ($suppliers as $s) {
    echo "  Supplier #{$s['id']}: {$s['name']} ({$s['company_name']})\n";

    $stmt2 = $db->prepare("SELECT COUNT(*) as cnt FROM supplier_products WHERE supplier_id = ?");
    $stmt2->execute([$s['id']]);
    echo "    - supplier_products: " . $stmt2->fetch()['cnt'] . "\n";

    $stmt2 = $db->prepare("SELECT COUNT(*) as cnt FROM purchase_orders WHERE supplier_id = ?");
    $stmt2->execute([$s['id']]);
    echo "    - purchase_orders: " . $stmt2->fetch()['cnt'] . "\n";
}
