<?php
/**
 * Migration: browser softphone presence.
 * support_agent_status.softphone_seen_at is refreshed every 30s while an agent's Phone window is online;
 * inbound calls ring agents seen in the last 90 seconds in the browser.
 *
 * Run: php database/migrations/add_softphone_presence.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_softphone_presence\n";

try {
    $stmt = $db->prepare("
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'support_agent_status' AND COLUMN_NAME = 'softphone_seen_at'
    ");
    $stmt->execute();

    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE support_agent_status
            ADD COLUMN softphone_seen_at DATETIME NULL DEFAULT NULL COMMENT 'Last heartbeat from the browser Phone window' AFTER phone
        ");
        echo "  [OK] Added support_agent_status.softphone_seen_at\n";
    } else {
        echo "  [SKIP] support_agent_status.softphone_seen_at already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
