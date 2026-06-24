<?php
/**
 * Driver Training System Migration
 * Creates tables for training modules, questions, driver progress, attempts, and certificates.
 */

require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

echo "Creating driver training tables...\n";

$db->exec("
CREATE TABLE IF NOT EXISTS training_modules (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_num   INT NOT NULL DEFAULT 1,
    title       VARCHAR(200) NOT NULL,
    description TEXT NULL,
    content_html LONGTEXT NULL,
    pass_score  INT NOT NULL DEFAULT 80,
    max_attempts INT NOT NULL DEFAULT 3,
    is_active   TINYINT NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order (order_num)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - training_modules created\n";

$db->exec("
CREATE TABLE IF NOT EXISTS training_questions (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    module_id      INT UNSIGNED NOT NULL,
    question_text  TEXT NOT NULL,
    option_a       VARCHAR(500) NOT NULL,
    option_b       VARCHAR(500) NOT NULL,
    option_c       VARCHAR(500) NULL,
    option_d       VARCHAR(500) NULL,
    correct_option ENUM('a','b','c','d') NOT NULL,
    explanation    TEXT NULL,
    order_num      INT NOT NULL DEFAULT 1,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_module (module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - training_questions created\n";

$db->exec("
CREATE TABLE IF NOT EXISTS driver_training_progress (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id    BIGINT UNSIGNED NOT NULL,
    module_id    INT UNSIGNED NOT NULL,
    status       ENUM('locked','available','passed','failed') NOT NULL DEFAULT 'locked',
    attempts     INT NOT NULL DEFAULT 0,
    best_score   INT NOT NULL DEFAULT 0,
    unlocked_at  TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_driver_module (driver_id, module_id),
    INDEX idx_driver (driver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - driver_training_progress created\n";

$db->exec("
CREATE TABLE IF NOT EXISTS driver_training_attempts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id    BIGINT UNSIGNED NOT NULL,
    module_id    INT UNSIGNED NOT NULL,
    answers_json JSON NOT NULL,
    score        INT NOT NULL,
    passed       TINYINT NOT NULL DEFAULT 0,
    taken_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_driver_module (driver_id, module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - driver_training_attempts created\n";

$db->exec("
CREATE TABLE IF NOT EXISTS driver_certificates (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    driver_id   BIGINT UNSIGNED NOT NULL,
    cert_number VARCHAR(20) NOT NULL,
    issued_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at  TIMESTAMP NULL,
    UNIQUE KEY uq_driver (driver_id),
    UNIQUE KEY uq_cert (cert_number),
    INDEX idx_driver (driver_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");
echo "  - driver_certificates created\n";

// Seed the 7 default modules
$modules = [
    [1, 'Customer Service',         'Greetings, communication, handling complaints, professional appearance, and customer satisfaction.'],
    [2, 'App Training (ODA)',        'How to use the OCSAPP Delivery App: login, accepting orders, navigation, status updates, and proof of delivery.'],
    [3, 'On-Road Etiquette',         'Parking responsibly, building entry procedures, elevator etiquette, and how to handle parcel handoffs correctly.'],
    [4, 'Delivery Laws (Canada)',    'Canadian regulations for hand-to-hand vs door delivery, age-restricted items, signature requirements, and liability.'],
    [5, 'Safety',                    'Vehicle pre-checks, driving in weather, safe lifting techniques, personal safety on the job, and emergency procedures.'],
    [6, 'Driving Laws',              'Traffic laws, distracted driving rules, commercial vehicle regulations, and road safety standards.'],
    [7, 'Payments & Earnings',       'How your pay is calculated, payment schedules, cash handling if applicable, and how to dispute earnings.'],
];

$stmt = $db->prepare("
    INSERT IGNORE INTO training_modules (order_num, title, description, pass_score, max_attempts, is_active)
    VALUES (?, ?, ?, 80, 3, 1)
");

foreach ($modules as [$order, $title, $desc]) {
    $count = $db->prepare("SELECT COUNT(*) FROM training_modules WHERE order_num = ?");
    $count->execute([$order]);
    if ($count->fetchColumn() == 0) {
        $stmt->execute([$order, $title, $desc]);
        echo "  - Seeded module {$order}: {$title}\n";
    } else {
        echo "  - Module {$order} already exists, skipping\n";
    }
}

echo "\nDone! All driver training tables created and seeded.\n";
