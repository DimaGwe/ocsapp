<?php
/**
 * Migration: Setup Support Tickets (Contact Center Inbox)
 * Run: php database/migrations/setup_support_tickets.php
 */

$envFile = __DIR__ . '/../../.env';
if (file_exists($envFile)) {
    foreach (parse_ini_file($envFile) as $k => $v) putenv("$k=$v");
}

function env_get($key, $default = null) {
    $v = getenv($key);
    return $v !== false ? $v : $default;
}

$pdo = new PDO(
    "mysql:host=" . env_get('DB_HOST') . ";port=" . env_get('DB_PORT', 3306) . ";dbname=" . env_get('DB_NAME') . ";charset=utf8mb4",
    env_get('DB_USER'),
    env_get('DB_PASS'),
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

echo "Running support ticket migration...\n\n";

$pdo->exec("
    CREATE TABLE IF NOT EXISTS support_tickets (
        id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_number    VARCHAR(20)  NOT NULL UNIQUE,
        subject          VARCHAR(255) NOT NULL,
        channel          ENUM('phone','email','web_form','walk_in','chat') NOT NULL DEFAULT 'phone',
        category         ENUM('order_issue','payment','account','delivery','product','billing','general') NOT NULL DEFAULT 'general',
        priority         ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
        status           ENUM('open','in_progress','pending_contact','resolved','closed') NOT NULL DEFAULT 'open',

        contact_type     ENUM('buyer','seller','supplier','driver','lead','unknown') NOT NULL DEFAULT 'unknown',
        contact_id       INT UNSIGNED NULL,
        contact_name     VARCHAR(150) NULL,
        contact_email    VARCHAR(150) NULL,
        contact_phone    VARCHAR(30)  NULL,

        order_id         INT UNSIGNED NULL,
        assigned_to      INT UNSIGNED NULL,
        created_by       INT UNSIGNED NOT NULL,

        description      TEXT NULL,
        resolution_notes TEXT NULL,

        first_response_at DATETIME NULL,
        resolved_at       DATETIME NULL,
        escalated_at      DATETIME NULL,
        escalated_to      INT UNSIGNED NULL,

        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        INDEX idx_status   (status),
        INDEX idx_priority (priority),
        INDEX idx_assigned (assigned_to),
        INDEX idx_contact  (contact_type, contact_id),
        INDEX idx_created  (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ support_tickets table ready\n";

$pdo->exec("
    CREATE TABLE IF NOT EXISTS support_ticket_messages (
        id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        ticket_id   INT UNSIGNED NOT NULL,
        message     TEXT NOT NULL,
        sender_type ENUM('agent','contact','system') NOT NULL DEFAULT 'agent',
        sender_id   INT UNSIGNED NULL,
        sender_name VARCHAR(100) NULL,
        is_internal TINYINT(1) NOT NULL DEFAULT 0,
        created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        INDEX idx_ticket (ticket_id),
        FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
");
echo "✓ support_ticket_messages table ready\n";

echo "\nDone.\n";
