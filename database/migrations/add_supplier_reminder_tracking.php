<?php
/**
 * Migration: Add reminder tracking columns to purchase_orders
 * Tracks whether 5-min and 8-min supplier response reminders have been sent.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

$check5 = $db->prepare("
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'purchase_orders' AND COLUMN_NAME = 'reminder_5min_sent'
");
$check5->execute();
if ($check5->fetchColumn() == 0) {
    $db->exec("
        ALTER TABLE purchase_orders
            ADD COLUMN reminder_5min_sent TINYINT(1) NOT NULL DEFAULT 0
                COMMENT '1 once 5-min-remaining reminder has been sent' AFTER confirmation_deadline,
            ADD COLUMN reminder_8min_sent TINYINT(1) NOT NULL DEFAULT 0
                COMMENT '1 once 8-min-remaining reminder has been sent' AFTER reminder_5min_sent
    ");
    echo "Migration complete: reminder_5min_sent + reminder_8min_sent added to purchase_orders.\n";
} else {
    echo "Reminder columns already exist — skipped.\n";
}
