<?php
require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->query("SHOW COLUMNS FROM waitlist LIKE 'business_name'");
    if ($stmt->fetch()) {
        echo "Column business_name already exists on waitlist - skipping.\n";
        exit(0);
    }

    $pdo->exec("
        ALTER TABLE waitlist
        ADD COLUMN business_name VARCHAR(150) NULL AFTER last_name
    ");

    echo "Added business_name column to waitlist table.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
