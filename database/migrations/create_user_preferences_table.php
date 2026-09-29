<?php
/**
 * Migration: `user_preferences` table (buyer email notification choices).
 * AccountController::settings() / updateNotifications() read and write it, but it never existed on
 * staging or prod, so /account/settings always failed and redirected back to /account (found 2026-09-28).
 * Marketing choices default OFF (CASL: express consent); order updates default ON (transactional).
 *
 * Run: php database/migrations/create_user_preferences_table.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: create_user_preferences_table\n";

try {
    $exists = $db->query("SHOW TABLES LIKE 'user_preferences'")->fetchColumn();
    if ($exists) {
        echo "  [SKIP] user_preferences already exists\n";
    } else {
        $db->exec("
            CREATE TABLE user_preferences (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                email_orders TINYINT(1) NOT NULL DEFAULT 1,
                email_promotions TINYINT(1) NOT NULL DEFAULT 0,
                email_newsletter TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_user_preferences_user (user_id),
                CONSTRAINT fk_user_preferences_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "  [OK] Created user_preferences\n";
    }
    echo "\nMigration complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
