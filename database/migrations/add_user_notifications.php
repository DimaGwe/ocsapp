<?php

/**
 * Migration: Add per-user notification support
 * - Adds user_id column to admin_notifications (NULL = broadcast, specific = targeted)
 * - Creates admin_notification_preferences table
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    // 1A: Add user_id column to admin_notifications
    $columns = $db->query("SHOW COLUMNS FROM admin_notifications LIKE 'user_id'")->fetchAll();
    if (empty($columns)) {
        echo "Adding user_id column to admin_notifications...\n";
        $db->exec("
            ALTER TABLE admin_notifications
            ADD COLUMN user_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER id,
            ADD INDEX idx_user_id (user_id),
            ADD CONSTRAINT fk_admin_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "Done.\n\n";
    } else {
        echo "user_id column already exists in admin_notifications. Skipping.\n\n";
    }

    // 1B: Create admin_notification_preferences table
    echo "Creating admin_notification_preferences table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS admin_notification_preferences (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL,
            notification_type VARCHAR(50) NOT NULL COMMENT 'e.g. task_assigned, task_completed, task_comment, note_comment, mention',
            in_app_enabled TINYINT(1) DEFAULT 1,
            email_enabled TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_user_type (user_id, notification_type),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Done.\n\n";

    echo "Migration completed successfully!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
