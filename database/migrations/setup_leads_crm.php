<?php
/**
 * Setup Leads CRM Tables
 * Run this migration to create the leads management system
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/config/app.php';
require BASE_PATH . '/config/database.php';

try {
    $db = Database::getConnection();

    echo "Setting up Leads CRM tables...\n\n";

    // Create leads table
    $db->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INT AUTO_INCREMENT PRIMARY KEY,

            -- Contact Info
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100),
            email VARCHAR(255),
            phone VARCHAR(50),
            company_name VARCHAR(255),
            job_title VARCHAR(100),

            -- Lead Details
            source ENUM('manual', 'website', 'referral', 'social_media', 'event', 'cold_call', 'email_campaign', 'other') DEFAULT 'manual',
            source_details VARCHAR(255),
            status ENUM('new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won', 'lost') DEFAULT 'new',
            priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',

            -- Interest
            interest_type ENUM('seller', 'buyer', 'business', 'supplier', 'delivery_partner', 'investor', 'other') DEFAULT 'other',
            interest_details TEXT,
            estimated_value DECIMAL(10,2),

            -- Location
            city VARCHAR(100),
            province VARCHAR(50),
            country VARCHAR(50) DEFAULT 'Canada',

            -- Assignment
            assigned_to INT,

            -- Notes & Follow-up
            notes TEXT,
            next_follow_up DATE,
            last_contacted_at DATETIME,

            -- Tracking
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            converted_at DATETIME,

            INDEX idx_status (status),
            INDEX idx_source (source),
            INDEX idx_priority (priority),
            INDEX idx_interest_type (interest_type),
            INDEX idx_assigned_to (assigned_to),
            INDEX idx_next_follow_up (next_follow_up),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created leads table\n";

    // Create lead_activities table for tracking interactions
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_activities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            lead_id INT NOT NULL,
            activity_type ENUM('note', 'call', 'email', 'meeting', 'status_change', 'follow_up', 'other') NOT NULL,
            description TEXT NOT NULL,
            outcome VARCHAR(255),
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            INDEX idx_lead_id (lead_id),
            INDEX idx_activity_type (activity_type),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created lead_activities table\n";

    // Create lead_tags table
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            color VARCHAR(7) DEFAULT '#6b7280',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created lead_tags table\n";

    // Create lead_tag_assignments table
    $db->exec("
        CREATE TABLE IF NOT EXISTS lead_tag_assignments (
            lead_id INT NOT NULL,
            tag_id INT NOT NULL,
            PRIMARY KEY (lead_id, tag_id),
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES lead_tags(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "Created lead_tag_assignments table\n";

    // Insert default tags
    $db->exec("
        INSERT IGNORE INTO lead_tags (name, color) VALUES
        ('Hot Lead', '#ef4444'),
        ('Follow Up', '#f59e0b'),
        ('Interested', '#22c55e'),
        ('Needs Info', '#3b82f6'),
        ('Decision Maker', '#8b5cf6'),
        ('Budget Approved', '#10b981')
    ");
    echo "Inserted default tags\n";

    echo "\n✅ Leads CRM setup complete!\n";

} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
