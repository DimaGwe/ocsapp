<?php
/**
 * Add scope column to planner_notes
 * scope='team' (default) - visible to all admins
 * scope='personal' - visible only to the creator
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    echo "Adding scope column to planner_notes...\n";

    $check = $db->query("SHOW COLUMNS FROM planner_notes LIKE 'scope'");
    if ($check->rowCount() > 0) {
        echo "Column 'scope' already exists. Skipping.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE planner_notes
        ADD COLUMN scope ENUM('team', 'personal') NOT NULL DEFAULT 'team'
        AFTER content
    ");

    echo "✓ scope column added to planner_notes\n";
    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
