<?php
/**
 * Migration: Add terms consent fields to users table
 *
 * PIPEDA accountability principle requires proof of consent at registration.
 * Stores when the user accepted Terms of Service and from which IP.
 */
require_once __DIR__ . '/../../bootstrap/init.php';

$db = Database::getConnection();

try {
    $db->exec("ALTER TABLE users ADD COLUMN terms_accepted_at DATETIME NULL AFTER phone");
    echo "Added column: terms_accepted_at\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column terms_accepted_at already exists - skipped\n";
    } else {
        throw $e;
    }
}

try {
    $db->exec("ALTER TABLE users ADD COLUMN terms_accepted_ip VARCHAR(45) NULL AFTER terms_accepted_at");
    echo "Added column: terms_accepted_ip\n";
} catch (\PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Column terms_accepted_ip already exists - skipped\n";
    } else {
        throw $e;
    }
}

echo "Done.\n";
