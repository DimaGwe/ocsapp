<?php

/**
 * Migration: Add structured task support
 * - Adds description column to planner_todos
 * - Creates planner_todo_items table for checklist sub-items
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    // 1A: Add description column to planner_todos
    $columns = $db->query("SHOW COLUMNS FROM planner_todos LIKE 'description'")->fetchAll();
    if (empty($columns)) {
        echo "Adding description column to planner_todos...\n";
        $db->exec("ALTER TABLE planner_todos ADD COLUMN description TEXT NULL AFTER task");
        echo "Done.\n\n";
    } else {
        echo "description column already exists in planner_todos. Skipping.\n\n";
    }

    // 1B: Create planner_todo_items table
    echo "Creating planner_todo_items table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_todo_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            todo_id BIGINT UNSIGNED NOT NULL,
            title VARCHAR(500) NOT NULL,
            is_completed BOOLEAN DEFAULT FALSE,
            completed_by BIGINT UNSIGNED NULL,
            completed_at TIMESTAMP NULL,
            sort_order INT UNSIGNED DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (todo_id) REFERENCES planner_todos(id) ON DELETE CASCADE,
            FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_todo_id (todo_id),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done.\n\n";

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
