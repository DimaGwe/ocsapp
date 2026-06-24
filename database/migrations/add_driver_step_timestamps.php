<?php
/**
 * Migration: Add heading_to_supplier_at + en_route_to_customer_at to delivery_assignments
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$cols = [
    'heading_to_supplier_at'  => "ALTER TABLE delivery_assignments ADD COLUMN heading_to_supplier_at DATETIME NULL COMMENT 'When driver started heading to first supplier'",
    'en_route_to_customer_at' => "ALTER TABLE delivery_assignments ADD COLUMN en_route_to_customer_at DATETIME NULL COMMENT 'When driver left last supplier heading to delivery address'",
];

foreach ($cols as $col => $sql) {
    $check = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'delivery_assignments' AND COLUMN_NAME = ?");
    $check->execute([$col]);
    if ((int)$check->fetchColumn() === 0) {
        $db->exec($sql);
        echo "OK: Added delivery_assignments.$col\n";
    } else {
        echo "SKIP: delivery_assignments.$col already exists\n";
    }
}

echo "Done.\n";
