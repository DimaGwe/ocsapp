<?php

/**
 * Migration: Add email verification columns to users table
 * Run: php database/migrations/add_email_verification_to_users.php
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

try {
    $db = \Database::getConnection();

    $columns = [
        'email_verification_code'         => "VARCHAR(6) NULL AFTER email_verified_at",
        'email_verification_expires_at'   => "DATETIME NULL AFTER email_verification_code",
        'email_verification_attempts'     => "TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER email_verification_expires_at",
    ];

    $existing = $db->query("SHOW COLUMNS FROM users")->fetchAll(\PDO::FETCH_COLUMN);

    foreach ($columns as $col => $definition) {
        if (in_array($col, $existing)) {
            echo "  SKIP   {$col} (already exists)\n";
            continue;
        }
        $db->exec("ALTER TABLE users ADD COLUMN {$col} {$definition}");
        echo "  ADDED  {$col}\n";
    }

    // Add 'unverified' to status ENUM if not already present
    $row = $db->query("SHOW COLUMNS FROM users LIKE 'status'")->fetch(\PDO::FETCH_ASSOC);
    if ($row && strpos($row['Type'], 'unverified') === false) {
        $db->exec("ALTER TABLE users MODIFY COLUMN status ENUM('active','pending','suspended','rejected','unverified') NOT NULL DEFAULT 'active'");
        echo "  UPDATED status ENUM to include 'unverified'\n";
    } else {
        echo "  SKIP   status ENUM (already has 'unverified')\n";
    }

    echo "\nMigration complete.\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
