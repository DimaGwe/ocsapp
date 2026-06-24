<?php
/**
 * Migration: Add invited_by column to supplier_invites table
 * Fixes BLOCKER: SupplierController::index() JOINs on si.invited_by which was missing,
 * causing the admin supplier management page to crash with a SQL error.
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'supplier_invites'
          AND COLUMN_NAME = 'invited_by'
    ");
    $stmt->execute();
    if ((int)$stmt->fetchColumn() > 0) {
        echo "Column 'invited_by' already exists on supplier_invites — nothing to do.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE supplier_invites
        ADD COLUMN invited_by INT NULL AFTER token,
        ADD CONSTRAINT fk_supplier_invites_invited_by
            FOREIGN KEY (invited_by) REFERENCES users(id) ON DELETE SET NULL
    ");

    echo "SUCCESS: Added 'invited_by' column to supplier_invites table.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
