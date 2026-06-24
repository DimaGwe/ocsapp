<?php
/**
 * Migration: Add department column to users table
 * Run: php database/migrations/add_department_to_users.php
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    $exists = $db->query("SHOW COLUMNS FROM users LIKE 'department'")->fetchAll();

    if (!empty($exists)) {
        echo "department column already exists. Nothing to do.\n";
        exit(0);
    }

    $db->exec("
        ALTER TABLE users
        ADD COLUMN department VARCHAR(50) NULL DEFAULT NULL
            COMMENT 'Admin team department: ops, finance, support, logistics, tech, management'
            AFTER phone,
        ADD INDEX idx_department (department)
    ");

    echo "department column added to users table.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
