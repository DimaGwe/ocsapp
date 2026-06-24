<?php
require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $pdo = Database::getConnection();

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS waitlist (
            id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            email         VARCHAR(255) NOT NULL,
            first_name    VARCHAR(100) NOT NULL,
            last_name     VARCHAR(100) NULL,
            role          ENUM('buyer','seller','supplier','driver','business') NOT NULL,
            locale        ENUM('fr','en') NOT NULL DEFAULT 'fr',
            referral_code VARCHAR(20)  NOT NULL,
            referred_by   VARCHAR(20)  NULL,
            status        ENUM('pending','notified','converted') NOT NULL DEFAULT 'pending',
            ip_address    VARCHAR(45)  NULL,
            created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uk_email    (email),
            UNIQUE KEY uk_ref_code (referral_code),
            INDEX idx_role         (role),
            INDEX idx_status       (status),
            INDEX idx_referred_by  (referred_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "✅ Waitlist table created successfully!\n";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
