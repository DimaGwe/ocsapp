<?php
/**
 * Migration: Add supplier_id column to supplier_applications table
 * Links the application to the supplier account created on submission
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM supplier_applications LIKE 'supplier_id'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE supplier_applications ADD COLUMN supplier_id INT NULL AFTER lead_id");
        $db->exec("ALTER TABLE supplier_applications ADD INDEX idx_supplier_id (supplier_id)");
        echo "Added 'supplier_id' column to supplier_applications table.\n";
    } else {
        echo "'supplier_id' column already exists.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
