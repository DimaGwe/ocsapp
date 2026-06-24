<?php
/**
 * Migration: Add is_archived column to planner_notes
 * Enables archive/restore workflow instead of delete-only
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();
    echo "Adding is_archived column to planner_notes...\n";

    // Check if column already exists
    $cols = $db->query("DESCRIBE planner_notes")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('is_archived', $cols)) {
        $db->exec("ALTER TABLE planner_notes ADD COLUMN is_archived BOOLEAN DEFAULT FALSE AFTER content");
        $db->exec("ALTER TABLE planner_notes ADD COLUMN archived_at TIMESTAMP NULL AFTER is_archived");
        $db->exec("ALTER TABLE planner_notes ADD INDEX idx_archived (is_archived)");
        echo "Done.\n";
    } else {
        echo "Column already exists, skipping.\n";
    }

    echo "\nMigration completed successfully!\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
