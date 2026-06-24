<?php
/**
 * Migration: Add pickup_stops to delivery_assignments
 *
 * Stores a JSON array of ordered stops for multi-supplier distribution deliveries.
 * Each stop: { stop_order, type (pickup|delivery), supplier_id?, supplier_name?,
 *              address, po_numbers[], latitude?, longitude? }
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

function colExists3(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    if (!colExists3($db, 'delivery_assignments', 'pickup_stops')) {
        $db->exec("ALTER TABLE delivery_assignments
            ADD COLUMN pickup_stops JSON NULL COMMENT 'Ordered multi-stop route array for distribution deliveries' AFTER pickup_address");
        echo "✓ delivery_assignments.pickup_stops added\n";
    } else {
        echo "– delivery_assignments.pickup_stops already exists, skipping\n";
    }

    if (!colExists3($db, 'delivery_assignments', 'total_stops')) {
        $db->exec("ALTER TABLE delivery_assignments
            ADD COLUMN total_stops TINYINT UNSIGNED DEFAULT 1 COMMENT 'Number of pickup stops' AFTER pickup_stops");
        echo "✓ delivery_assignments.total_stops added\n";
    } else {
        echo "– delivery_assignments.total_stops already exists, skipping\n";
    }

    echo "\nMigration complete.\n";
} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
