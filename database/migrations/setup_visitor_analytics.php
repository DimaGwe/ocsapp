<?php
/**
 * Migration: Setup Visitor Analytics
 * Creates visitor_logs table and adds view_count to products
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    echo "Starting migration: Setup Visitor Analytics..." . PHP_EOL;

    // Step 1: Create visitor_logs table
    echo "  - Creating visitor_logs table..." . PHP_EOL;
    $db->exec("
        CREATE TABLE IF NOT EXISTS visitor_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45),
            user_agent TEXT,
            page_url VARCHAR(500),
            referrer VARCHAR(500),
            session_id VARCHAR(100),
            user_id BIGINT UNSIGNED NULL,
            visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip (ip_address),
            INDEX idx_visited_at (visited_at),
            INDEX idx_page_url (page_url(191)),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "    ✓ visitor_logs table created" . PHP_EOL;

    // Step 2: Add view_count column to products
    echo "  - Adding view_count column to products table..." . PHP_EOL;

    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'view_count'");
    if ($stmt->rowCount() == 0) {
        $db->exec("
            ALTER TABLE products
            ADD COLUMN view_count INT UNSIGNED DEFAULT 0
            AFTER available_stock
        ");
        echo "    ✓ view_count column added to products" . PHP_EOL;
    } else {
        echo "    ℹ view_count column already exists" . PHP_EOL;
    }

    // Step 3: Create product_views tracking table (optional, for detailed tracking)
    echo "  - Creating product_views table..." . PHP_EOL;
    $db->exec("
        CREATE TABLE IF NOT EXISTS product_views (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            product_id BIGINT UNSIGNED NOT NULL,
            ip_address VARCHAR(45),
            user_id BIGINT UNSIGNED NULL,
            session_id VARCHAR(100),
            viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_product_id (product_id),
            INDEX idx_viewed_at (viewed_at),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "    ✓ product_views table created" . PHP_EOL;

    // Step 4: Create search_logs table
    echo "  - Creating search_logs table..." . PHP_EOL;
    $db->exec("
        CREATE TABLE IF NOT EXISTS search_logs (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            search_query VARCHAR(255) NOT NULL,
            ip_address VARCHAR(45),
            user_id BIGINT UNSIGNED NULL,
            results_count INT DEFAULT 0,
            searched_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_query (search_query),
            INDEX idx_searched_at (searched_at),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "    ✓ search_logs table created" . PHP_EOL;

    echo PHP_EOL . "✅ Visitor analytics setup completed successfully!" . PHP_EOL;
    echo PHP_EOL . "Tables created:" . PHP_EOL;
    echo "  • visitor_logs - General page tracking" . PHP_EOL;
    echo "  • product_views - Product view tracking" . PHP_EOL;
    echo "  • search_logs - Search query tracking" . PHP_EOL;
    echo "  • products.view_count - Product view counter" . PHP_EOL;

} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
