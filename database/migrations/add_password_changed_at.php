<?php
/**
 * Migration: Add password_changed_at column to suppliers table
 * Tracks when supplier last changed their password (NULL = never changed from system-generated)
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->prepare("SHOW COLUMNS FROM suppliers LIKE 'password_changed_at'");
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE suppliers ADD COLUMN password_changed_at DATETIME NULL DEFAULT NULL AFTER password_hash");
        echo "Added password_changed_at column to suppliers table.\n";
    } else {
        echo "Column password_changed_at already exists.\n";
    }

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
