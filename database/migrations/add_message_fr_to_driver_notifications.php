<?php
/**
 * Migration: bilingual driver notifications.
 * - driver_delivery_notifications.message_fr (NULL = no French version; readers fall back to `message`)
 * - Repairs rows stored with an empty type: callers passed 'normal' / 'founding_partner', which are
 *   not in the enum (info|warning|urgent) and MySQL (non-strict sql_mode) stored ''.
 *
 * Run: php database/migrations/add_message_fr_to_driver_notifications.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_message_fr_to_driver_notifications\n";

try {
    $has = $db->query("SHOW COLUMNS FROM driver_delivery_notifications LIKE 'message_fr'")->fetchColumn();
    if ($has) {
        echo "  [SKIP] message_fr already exists\n";
    } else {
        $db->exec("ALTER TABLE driver_delivery_notifications ADD COLUMN message_fr TEXT NULL AFTER message");
        echo "  [OK] Added message_fr\n";
    }

    $fixed = $db->exec("UPDATE driver_delivery_notifications SET type = 'info' WHERE type = '' OR type IS NULL");
    echo "  [OK] Repaired {$fixed} row(s) with an empty type\n";

    echo "\nMigration complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
