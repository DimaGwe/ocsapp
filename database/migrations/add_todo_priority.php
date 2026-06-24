<?php
/**
 * Add priority column to planner_todos
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    echo "Adding priority column to planner_todos...\n";

    // Check if column already exists
    $check = $db->query("SHOW COLUMNS FROM planner_todos LIKE 'priority'");
    if ($check->rowCount() > 0) {
        echo "Column 'priority' already exists. Skipping.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE planner_todos
        ADD COLUMN priority ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium'
        AFTER assigned_to
    ");

    echo "✓ priority column added to planner_todos\n";
    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
