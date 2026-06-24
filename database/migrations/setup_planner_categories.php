<?php
/**
 * Migration: Planner Template Categories
 * Creates: planner_template_categories
 * Seeds with the 7 built-in categories previously hard-coded.
 *
 * Run: php database/migrations/setup_planner_categories.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Planner Categories Migration\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->beginTransaction();

    echo "Creating planner_template_categories table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS planner_template_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL,
            sort_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            UNIQUE KEY uq_slug (slug),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "  - planner_template_categories table created\n";

    // Seed with the 7 categories previously hard-coded in the controller
    $seeds = [
        ['onboarding',      'Onboarding',       1],
        ['job-descriptions','Job Descriptions',  2],
        ['policies',        'Policies',          3],
        ['legal',           'Legal Documents',   4],
        ['email-templates', 'Email Templates',   5],
        ['contracts',       'Contracts',         6],
        ['general',         'Other',             7],
    ];

    $stmt = $db->prepare("
        INSERT IGNORE INTO planner_template_categories (slug, name, sort_order)
        VALUES (?, ?, ?)
    ");

    echo "\nSeeding default categories...\n";
    foreach ($seeds as [$slug, $name, $order]) {
        $stmt->execute([$slug, $name, $order]);
        echo "  - $name ($slug)\n";
    }

    $db->commit();

    echo "\n===========================================\n";
    echo "Migration completed successfully!\n";
    echo "===========================================\n";
    echo "\nTable created:\n";
    echo "  - planner_template_categories (7 default categories seeded)\n";

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
