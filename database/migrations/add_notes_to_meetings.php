<?php
/**
 * Migration: Add notes column to planner_meetings table
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM planner_meetings LIKE 'notes'");
    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE planner_meetings ADD COLUMN notes TEXT NULL AFTER next_meeting_topics");
        echo "Added 'notes' column to planner_meetings table.\n";
    } else {
        echo "'notes' column already exists.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
