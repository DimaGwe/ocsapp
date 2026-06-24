<?php
/**
 * Migration: Add role column to users table
 * This migration adds the missing role column and sets default values
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    echo "Starting migration: Add role column to users table..." . PHP_EOL;

    // Step 1: Add role column
    echo "  - Adding role column..." . PHP_EOL;
    $db->exec("
        ALTER TABLE users
        ADD COLUMN role ENUM('admin', 'seller', 'buyer', 'delivery')
        NOT NULL DEFAULT 'buyer'
        AFTER email
    ");
    echo "    ✓ Role column added successfully" . PHP_EOL;

    // Step 2: Set roles for existing users based on email or name patterns
    echo "  - Setting roles for existing users..." . PHP_EOL;

    // Set admin role for users with 'admin' in email or name
    $stmt = $db->exec("
        UPDATE users
        SET role = 'admin'
        WHERE email LIKE '%admin%'
           OR LOWER(first_name) LIKE '%admin%'
           OR LOWER(last_name) LIKE '%admin%'
    ");
    echo "    ✓ Set " . $stmt . " users as admin" . PHP_EOL;

    // Set delivery role for users with 'driver' or 'delivery' in email or name
    $stmt = $db->exec("
        UPDATE users
        SET role = 'delivery'
        WHERE role = 'buyer'
          AND (email LIKE '%driver%'
           OR email LIKE '%delivery%'
           OR LOWER(first_name) LIKE '%driver%'
           OR LOWER(first_name) LIKE '%delivery%')
    ");
    echo "    ✓ Set " . $stmt . " users as delivery" . PHP_EOL;

    // Set seller role for users with 'seller' or 'vendor' in email or name
    $stmt = $db->exec("
        UPDATE users
        SET role = 'seller'
        WHERE role = 'buyer'
          AND (email LIKE '%seller%'
           OR email LIKE '%vendor%'
           OR LOWER(first_name) LIKE '%seller%'
           OR LOWER(first_name) LIKE '%vendor%')
    ");
    echo "    ✓ Set " . $stmt . " users as seller" . PHP_EOL;

    // Step 3: Display role counts
    echo PHP_EOL . "  - Role distribution:" . PHP_EOL;
    $stmt = $db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
    $roleCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($roleCounts as $roleCount) {
        echo "    • " . ucfirst($roleCount['role']) . ": " . $roleCount['count'] . " users" . PHP_EOL;
    }

    echo PHP_EOL . "✅ Migration completed successfully!" . PHP_EOL;

} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
