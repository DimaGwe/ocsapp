<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$stmt = $db->query("SELECT id, product_name, cost_price, unit_price FROM supplier_products LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "ID:{$r['id']} | {$r['product_name']} | cost_price: {$r['cost_price']} | unit_price: {$r['unit_price']}\n";
}
echo "Total rows: " . count($rows) . "\n";
