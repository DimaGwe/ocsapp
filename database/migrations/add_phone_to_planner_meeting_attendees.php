<?php
/**
 * Migration: Mobile number per meeting attendee, for the short SMS that goes out
 * with meeting invites and minutes (Twilio). Prefilled from users.phone in the UI.
 *
 * Run: php database/migrations/add_phone_to_planner_meeting_attendees.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_phone_to_planner_meeting_attendees\n";

try {
    $stmt = $db->prepare("
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'planner_meeting_attendees' AND COLUMN_NAME = 'phone'
    ");
    $stmt->execute();

    if (!$stmt->fetch()) {
        $db->exec("
            ALTER TABLE planner_meeting_attendees
            ADD COLUMN phone VARCHAR(20) NULL DEFAULT NULL COMMENT 'Mobile for meeting SMS' AFTER email
        ");
        echo "  [OK] Added planner_meeting_attendees.phone\n";
    } else {
        echo "  [SKIP] planner_meeting_attendees.phone already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
