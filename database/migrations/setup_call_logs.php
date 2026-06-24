<?php
/**
 * Migration: Call Logs Table
 * Stores agent call dispositions from the Quick Disposition Modal
 * Run: php database/migrations/setup_call_logs.php
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
    CREATE TABLE IF NOT EXISTS call_logs (
        id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        agent_id         INT UNSIGNED NOT NULL,
        direction        ENUM('inbound','outbound') NOT NULL DEFAULT 'outbound',
        contact_type     ENUM('buyer','seller','driver','supplier','lead','unknown') NOT NULL DEFAULT 'unknown',
        contact_id       INT UNSIGNED NULL,
        contact_name     VARCHAR(120) NOT NULL DEFAULT '',
        contact_phone    VARCHAR(40)  NOT NULL DEFAULT '',
        contact_email    VARCHAR(180) NOT NULL DEFAULT '',
        outcome          ENUM('resolved','follow_up','no_answer','voicemail','wrong_number','transferred','callback_scheduled','other') NOT NULL DEFAULT 'other',
        notes            TEXT NULL,
        ticket_id        INT UNSIGNED NULL,
        ticket_subject   VARCHAR(220) NULL,
        callback_at      DATETIME NULL,
        duration_seconds SMALLINT UNSIGNED NULL,
        created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_agent    (agent_id),
        INDEX idx_contact  (contact_type, contact_id),
        INDEX idx_outcome  (outcome),
        INDEX idx_callback (callback_at),
        INDEX idx_created  (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "✓ call_logs table created\n";
echo "Done.\n";
