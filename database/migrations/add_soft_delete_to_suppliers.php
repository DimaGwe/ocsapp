<?php

/**
 * Migration: Add soft delete column to suppliers table
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM suppliers LIKE 'deleted_at'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE suppliers ADD COLUMN deleted_at DATETIME DEFAULT NULL");
        $db->exec("CREATE INDEX idx_suppliers_deleted_at ON suppliers(deleted_at)");
        echo "SUCCESS: Added deleted_at column to suppliers table.\n";
    } else {
        echo "SKIP: deleted_at column already exists.\n";
    }

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
