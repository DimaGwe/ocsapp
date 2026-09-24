<?php
/**
 * Migration: Meeting collaboration.
 * - planner_meetings.note_taker_id: the one person who owns the minutes
 * - planner_meetings.revision + updated_by: edit guard, so a stale save cannot
 *   silently overwrite someone else's changes
 * - planner_meeting_comments: attendees suggest agenda items / review minutes
 *
 * Run: php database/migrations/add_meeting_collaboration.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_meeting_collaboration\n";

function hasColumn(PDO $db, string $table, string $column): bool
{
    $stmt = $db->prepare("
        SELECT 1 FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (bool)$stmt->fetch();
}

try {
    if (!hasColumn($db, 'planner_meetings', 'note_taker_id')) {
        $db->exec("
            ALTER TABLE planner_meetings
            ADD COLUMN note_taker_id BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Person responsible for the minutes' AFTER location,
            ADD CONSTRAINT fk_planner_meetings_note_taker FOREIGN KEY (note_taker_id) REFERENCES users(id) ON DELETE SET NULL
        ");
        echo "  [OK] Added planner_meetings.note_taker_id\n";
    } else {
        echo "  [SKIP] planner_meetings.note_taker_id already exists\n";
    }

    if (!hasColumn($db, 'planner_meetings', 'revision')) {
        $db->exec("
            ALTER TABLE planner_meetings
            ADD COLUMN revision INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Bumped on every content change; stale saves are rejected' AFTER invite_sent_at
        ");
        echo "  [OK] Added planner_meetings.revision\n";
    } else {
        echo "  [SKIP] planner_meetings.revision already exists\n";
    }

    if (!hasColumn($db, 'planner_meetings', 'updated_by')) {
        $db->exec("
            ALTER TABLE planner_meetings
            ADD COLUMN updated_by BIGINT UNSIGNED NULL DEFAULT NULL COMMENT 'Last person to change the meeting content' AFTER revision,
            ADD CONSTRAINT fk_planner_meetings_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
        ");
        echo "  [OK] Added planner_meetings.updated_by\n";
    } else {
        echo "  [SKIP] planner_meetings.updated_by already exists\n";
    }

    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_meeting_comments (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            meeting_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NOT NULL,
            comment TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY idx_meeting_id (meeting_id),
            KEY idx_user_id (user_id),
            CONSTRAINT fk_planner_meeting_comments_meeting FOREIGN KEY (meeting_id) REFERENCES planner_meetings(id) ON DELETE CASCADE,
            CONSTRAINT fk_planner_meeting_comments_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  [OK] planner_meeting_comments ready\n";

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
