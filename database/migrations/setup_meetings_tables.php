<?php
/**
 * Meeting Minutes Database Setup
 * Creates tables for meetings, attendees, items, and action tracking
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

$db = Database::getConnection();

try {
    echo "Setting up Meeting Minutes tables...\n\n";

    // 1. Planner Meetings Table
    echo "Creating planner_meetings table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_meetings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            meeting_date DATE NOT NULL,
            meeting_time TIME NULL,
            location VARCHAR(255) NULL,
            status ENUM('draft', 'in_progress', 'completed', 'sent') DEFAULT 'draft',
            previous_meeting_id BIGINT UNSIGNED NULL,
            next_meeting_date DATE NULL,
            next_meeting_topics TEXT NULL,
            email_subject VARCHAR(255) NULL,
            email_draft LONGTEXT NULL,
            sent_at TIMESTAMP NULL,
            created_by BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (previous_meeting_id) REFERENCES planner_meetings(id) ON DELETE SET NULL,
            INDEX idx_status (status),
            INDEX idx_meeting_date (meeting_date),
            INDEX idx_created_by (created_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ planner_meetings table created\n\n";

    // 2. Planner Meeting Attendees Table
    echo "Creating planner_meeting_attendees table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_meeting_attendees (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            meeting_id BIGINT UNSIGNED NOT NULL,
            user_id BIGINT UNSIGNED NULL,
            email VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            attended BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (meeting_id) REFERENCES planner_meetings(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_meeting_id (meeting_id),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ planner_meeting_attendees table created\n\n";

    // 3. Planner Meeting Items Table (agenda, discussions, decisions)
    echo "Creating planner_meeting_items table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_meeting_items (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            meeting_id BIGINT UNSIGNED NOT NULL,
            item_type ENUM('agenda', 'discussion', 'decision') NOT NULL,
            content TEXT NOT NULL,
            owner_id BIGINT UNSIGNED NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (meeting_id) REFERENCES planner_meetings(id) ON DELETE CASCADE,
            FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_meeting_id (meeting_id),
            INDEX idx_item_type (item_type),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ planner_meeting_items table created\n\n";

    // 4. Planner Meeting Actions Table
    echo "Creating planner_meeting_actions table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_meeting_actions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            meeting_id BIGINT UNSIGNED NOT NULL,
            description TEXT NOT NULL,
            assigned_to BIGINT UNSIGNED NULL,
            assigned_name VARCHAR(255) NULL,
            due_date DATE NULL,
            status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
            completed_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (meeting_id) REFERENCES planner_meetings(id) ON DELETE CASCADE,
            FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_meeting_id (meeting_id),
            INDEX idx_assigned_to (assigned_to),
            INDEX idx_status (status),
            INDEX idx_due_date (due_date)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✓ planner_meeting_actions table created\n\n";

    echo "✅ All Meeting Minutes tables created successfully!\n";
    echo "\nYou can now use the Meetings feature in the Team Planner.\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
