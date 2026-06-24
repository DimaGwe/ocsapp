<?php
/**
 * Migration: Content System (AI Content Creator + Manager)
 * Stores social media posts batch-created in the admin Content Creator,
 * queued and tracked through the post pipeline in the Content Library.
 * Run: php database/migrations/setup_content_system.php
 */

$envFile = __DIR__ . '/../../.env';
if (!file_exists($envFile)) { die("No .env file found\n"); }

$env = parse_ini_file($envFile);
function env_get(string $key, string $default = ''): string {
    global $env;
    return $env[$key] ?? $default;
}

$dsn = 'mysql:host=' . env_get('DB_HOST','127.0.0.1')
     . ';dbname=' . env_get('DB_NAME','')
     . ';charset=utf8mb4';

try {
    $pdo = new PDO($dsn, env_get('DB_USER'), env_get('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (Exception $e) {
    die("DB connection failed: " . $e->getMessage() . "\n");
}

$pdo->exec("
    CREATE TABLE IF NOT EXISTS content_posts (
        id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        title         VARCHAR(200) NOT NULL DEFAULT 'Untitled',
        caption_en    TEXT NULL,
        caption_fr    TEXT NULL,
        hashtags      VARCHAR(500) NOT NULL DEFAULT '',
        platforms     VARCHAR(200) NOT NULL DEFAULT '',
        status        ENUM('idea','approved','scheduled','posted') NOT NULL DEFAULT 'idea',
        post_date     DATE NULL,
        image_path    VARCHAR(500) NULL,
        video_path    VARCHAR(500) NULL,
        generated_by  VARCHAR(50) NOT NULL DEFAULT 'manual',
        created_by    INT UNSIGNED NULL,
        created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status   (status),
        INDEX idx_postdate (post_date),
        INDEX idx_created  (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "✓ content_posts table created\n";

// Ensure the uploads directory for generated images exists
$uploadDir = __DIR__ . '/../../public/uploads/content';
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0775, true)) {
        echo "✓ public/uploads/content directory created\n";
    } else {
        echo "! Could not create public/uploads/content - create it manually\n";
    }
} else {
    echo "✓ public/uploads/content directory already exists\n";
}

echo "Done.\n";
