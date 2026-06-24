<?php
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();
$stmt = $db->query("DESCRIBE supplier_products");
while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $r['Field'] . ' | ' . $r['Type'] . ' | Null:' . $r['Null'] . ' | Default:' . ($r['Default'] ?? 'NULL') . "\n";
}
