<?php
/**
 * Add summary breakdown columns to distribution_requests table
 *
 * Run: php database/migrations/add_summary_columns.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "Adding summary columns to distribution_requests table...\n\n";

try {
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check what columns already exist
    $stmt = $db->query("DESCRIBE distribution_requests");
    $existingColumns = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $existingColumns[] = $row['Field'];
    }

    $columnsToAdd = [];

    // Add delivery_distance column if not exists
    if (!in_array('delivery_distance', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN delivery_distance DECIMAL(10,2) DEFAULT 0 AFTER delivery_country";
        echo "  Will add: delivery_distance\n";
    }

    // Add tier column if not exists
    if (!in_array('tier', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN tier TINYINT DEFAULT 1 AFTER delivery_distance";
        echo "  Will add: tier\n";
    }

    // Add items_total column if not exists
    if (!in_array('items_total', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN items_total DECIMAL(12,2) DEFAULT 0 AFTER tier";
        echo "  Will add: items_total\n";
    }

    // Add service_fee column if not exists
    if (!in_array('service_fee', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN service_fee DECIMAL(12,2) DEFAULT 0 AFTER items_total";
        echo "  Will add: service_fee\n";
    }

    // Add handling_fee column if not exists
    if (!in_array('handling_fee', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN handling_fee DECIMAL(12,2) DEFAULT 0 AFTER service_fee";
        echo "  Will add: handling_fee\n";
    }

    // Add gst_amount column if not exists
    if (!in_array('gst_amount', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN gst_amount DECIMAL(12,2) DEFAULT 0 AFTER tax_amount";
        echo "  Will add: gst_amount\n";
    }

    // Add qst_amount column if not exists
    if (!in_array('qst_amount', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN qst_amount DECIMAL(12,2) DEFAULT 0 AFTER gst_amount";
        echo "  Will add: qst_amount\n";
    }

    // Rename columns if needed (request_name might be missing)
    if (!in_array('request_name', $existingColumns) && in_array('name', $existingColumns)) {
        $columnsToAdd[] = "CHANGE COLUMN name request_name VARCHAR(255) NOT NULL";
        echo "  Will rename: name -> request_name\n";
    }

    // Add request_name if doesn't exist at all
    if (!in_array('request_name', $existingColumns) && !in_array('name', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN request_name VARCHAR(255) NOT NULL AFTER request_type";
        echo "  Will add: request_name\n";
    }

    // Add notes column if doesn't exist
    if (!in_array('notes', $existingColumns) && !in_array('business_notes', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN notes TEXT NULL AFTER request_name";
        echo "  Will add: notes\n";
    }

    // Add preferred_delivery_date if doesn't exist
    if (!in_array('preferred_delivery_date', $existingColumns) && !in_array('requested_delivery_date', $existingColumns)) {
        $columnsToAdd[] = "ADD COLUMN preferred_delivery_date DATE NULL AFTER delivery_instructions";
        echo "  Will add: preferred_delivery_date\n";
    }

    if (empty($columnsToAdd)) {
        echo "\n✓ All columns already exist. No changes needed.\n";
    } else {
        $sql = "ALTER TABLE distribution_requests " . implode(", ", $columnsToAdd);
        echo "\nExecuting: $sql\n\n";
        $db->exec($sql);
        echo "✓ Columns added successfully!\n";
    }

    // Show current table structure
    echo "\nCurrent distribution_requests columns:\n";
    $stmt = $db->query("DESCRIBE distribution_requests");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['Field']} ({$row['Type']})\n";
    }

} catch (PDOException $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone!\n";
