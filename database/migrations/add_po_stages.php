<?php
/**
 * Migration: Add PO stages (accepted, preparing, ready_for_pickup, picked_up)
 * Renames existing 'receiving' status → 'accepted'
 */

require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    // Step 1: Expand the enum to include all new values (keep 'receiving' temporarily)
    $db->exec("
        ALTER TABLE purchase_orders
        MODIFY COLUMN status ENUM(
            'draft','sent','receiving','accepted','preparing',
            'ready_for_pickup','picked_up','completed','cancelled'
        ) DEFAULT 'draft'
    ");
    echo "Step 1: Enum expanded.\n";

    // Step 2: Rename existing 'receiving' rows → 'accepted'
    $updated = $db->exec("UPDATE purchase_orders SET status = 'accepted' WHERE status = 'receiving'");
    echo "Step 2: {$updated} rows renamed from 'receiving' to 'accepted'.\n";

    // Step 3: Remove the legacy 'receiving' value from enum
    $db->exec("
        ALTER TABLE purchase_orders
        MODIFY COLUMN status ENUM(
            'draft','sent','accepted','preparing',
            'ready_for_pickup','picked_up','completed','cancelled'
        ) DEFAULT 'draft'
    ");
    echo "Step 3: Legacy 'receiving' removed from enum.\n";

    echo "\nDone — PO stages migration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
