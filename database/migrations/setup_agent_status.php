<?php
/**
 * Migration: Agent Status Table
 * Creates support_agent_status for tracking agent availability
 * Run: php database/migrations/setup_agent_status.php
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
    CREATE TABLE IF NOT EXISTS support_agent_status (
        id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id    INT UNSIGNED NOT NULL UNIQUE,
        status     ENUM('available','busy','break','offline') NOT NULL DEFAULT 'offline',
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "✓ support_agent_status table created\n";
echo "Done.\n";
