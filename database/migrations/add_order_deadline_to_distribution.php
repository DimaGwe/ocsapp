<?php
/**
 * Migration: Add order_deadline to distribution_requests
 * Tracks the promised delivery completion time (starts at submission).
 * - ASAP:      submitted_at + 2 hours
 * - Same Day:  scheduled_date + scheduled_time_to
 * - Scheduled: scheduled_date + scheduled_time_to
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$check = $db->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'distribution_requests' AND COLUMN_NAME = 'order_deadline'
");
$check->execute();
if ($check->fetchColumn() == 0) {
    $db->exec("
        ALTER TABLE distribution_requests
            ADD COLUMN order_deadline DATETIME NULL
                COMMENT 'Promised delivery completion time — set on submission'
                AFTER submitted_at
    ");
    echo "Migration complete: order_deadline added to distribution_requests.\n";
} else {
    echo "Column order_deadline already exists — skipped.\n";
}
