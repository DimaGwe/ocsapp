<?php
require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE 'city_region'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE waitlist ADD COLUMN city_region VARCHAR(150) NULL AFTER business_name");
        echo "Added city_region column to waitlist table.\n";
    } else {
        echo "Column city_region already exists on waitlist - skipping.\n";
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE 'heard_about'");
    if (!$stmt->fetch()) {
        $pdo->exec("
            ALTER TABLE waitlist
            ADD COLUMN heard_about ENUM('social_media','friend_family','google_search','press','other') NULL AFTER city_region
        ");
        echo "Added heard_about column to waitlist table.\n";
    } else {
        echo "Column heard_about already exists on waitlist - skipping.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
