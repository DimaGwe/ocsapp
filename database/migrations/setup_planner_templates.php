<?php
/**
 * Migration: Planner Template Management System
 * Creates: planner_templates, planner_template_revisions
 *
 * This migration adds template storage with full revision history tracking.
 *
 * Run: php database/migrations/setup_planner_templates.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Planner Templates Migration\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $db->beginTransaction();

    // =========================================
    // Create planner_templates table
    // =========================================
    echo "Creating planner_templates table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            category VARCHAR(100) DEFAULT 'general',
            content LONGTEXT NOT NULL,
            created_by BIGINT UNSIGNED NOT NULL,
            updated_by BIGINT UNSIGNED NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            UNIQUE KEY uq_slug (slug),
            INDEX idx_category (category),
            INDEX idx_created_by (created_by),
            INDEX idx_is_active (is_active),
            INDEX idx_updated_at (updated_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "  - planner_templates table created\n";

    // Add foreign key for created_by (check if users table exists)
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        // Check if foreign key already exists
        $stmt = $db->query("
            SELECT COUNT(*) as cnt
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'planner_templates'
            AND CONSTRAINT_NAME = 'fk_templates_created_by'
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['cnt'] == 0) {
            $db->exec("
                ALTER TABLE planner_templates
                ADD CONSTRAINT fk_templates_created_by
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
            ");
            echo "  - Foreign key fk_templates_created_by added\n";
        }
    }

    // =========================================
    // Create planner_template_revisions table
    // =========================================
    echo "\nCreating planner_template_revisions table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_template_revisions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            template_id INT NOT NULL,
            revision_number INT NOT NULL,
            content LONGTEXT NOT NULL,
            change_summary VARCHAR(500) NULL,
            changed_by BIGINT UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_template_id (template_id),
            INDEX idx_revision_number (revision_number),
            INDEX idx_changed_by (changed_by),
            INDEX idx_created_at (created_at),
            UNIQUE KEY uq_template_revision (template_id, revision_number),

            CONSTRAINT fk_revisions_template
            FOREIGN KEY (template_id) REFERENCES planner_templates(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "  - planner_template_revisions table created\n";

    // Add foreign key for changed_by (check if users table exists)
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        // Check if foreign key already exists
        $stmt = $db->query("
            SELECT COUNT(*) as cnt
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE CONSTRAINT_SCHEMA = DATABASE()
            AND TABLE_NAME = 'planner_template_revisions'
            AND CONSTRAINT_NAME = 'fk_revisions_changed_by'
        ");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['cnt'] == 0) {
            $db->exec("
                ALTER TABLE planner_template_revisions
                ADD CONSTRAINT fk_revisions_changed_by
                FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE
            ");
            echo "  - Foreign key fk_revisions_changed_by added\n";
        }
    }

    // Commit transaction
    $db->commit();

    echo "\n===========================================\n";
    echo "Migration completed successfully!\n";
    echo "===========================================\n";
    echo "\nTables created:\n";
    echo "  - planner_templates (stores template metadata and current content)\n";
    echo "  - planner_template_revisions (stores revision history)\n";
    echo "\nYou can now use the Templates feature in Admin > Planner.\n";

} catch (PDOException $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n===========================================\n";
    echo "Migration FAILED!\n";
    echo "===========================================\n";
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
