<?php
/**
 * Migration: Track pre-meeting invitations on planner meetings.
 * Invites are separate from the minutes email (email_subject/email_draft/sent_at)
 * and do not change the meeting status.
 *
 * Run: php database/migrations/add_invite_sent_at_to_planner_meetings.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_invite_sent_at_to_planner_meetings\n";

try {
    $cols = $db->query("SHOW COLUMNS FROM planner_meetings LIKE 'invite_sent_at'")->fetchAll();
    if (empty($cols)) {
        $db->exec("
            ALTER TABLE planner_meetings
            ADD COLUMN invite_sent_at TIMESTAMP NULL DEFAULT NULL COMMENT 'Last time the pre-meeting invitation was emailed' AFTER sent_at
        ");
        echo "  [OK] Added planner_meetings.invite_sent_at\n";
    } else {
        echo "  [SKIP] planner_meetings.invite_sent_at already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
