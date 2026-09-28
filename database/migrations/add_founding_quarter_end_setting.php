<?php
/**
 * Migration: `founding_quarter_end` setting (general category, editable in /admin/settings).
 * Date (YYYY-MM-DD) the Founding Quarter ends; the /founding countdown shows only while it is a
 * future date (PageController::foundingQuarterEnd()). Created EMPTY: no countdown until a real
 * deadline is set, because a countdown must reflect a true deadline (Competition Act, fake urgency cues).
 *
 * Run: php database/migrations/add_founding_quarter_end_setting.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_founding_quarter_end_setting\n";

try {
    $stmt = $db->prepare("
        INSERT INTO settings (`key`, category, label, description, value, type)
        VALUES ('founding_quarter_end', 'general', 'Founding Quarter end date',
                'YYYY-MM-DD. Shows the countdown on /founding until this date. Leave empty to hide the countdown. Must be a real deadline: never reset or extend it while the offer continues.',
                '', 'text')
        ON DUPLICATE KEY UPDATE `key` = `key`
    ");
    $stmt->execute();
    echo $stmt->rowCount() === 1 ? "  [OK] Added setting founding_quarter_end (empty)\n" : "  [SKIP] founding_quarter_end already exists\n";
    echo "\nMigration complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
