<?php
/**
 * Migration: Add pending_verification to suppliers status enum
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check current enum values
    $r = $db->query("SHOW COLUMNS FROM suppliers WHERE Field='status'");
    $col = $r->fetch(PDO::FETCH_ASSOC);
    echo "Current type: {$col['Type']}\n";

    if (strpos($col['Type'], 'pending_verification') === false) {
        $db->exec("ALTER TABLE suppliers MODIFY COLUMN status ENUM('active','inactive','suspended','pending_verification') DEFAULT 'active'");
        echo "Added 'pending_verification' to suppliers status enum.\n";
    } else {
        echo "'pending_verification' already exists in enum.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
