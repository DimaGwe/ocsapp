<?php
/**
 * Migration: Add application_draft column to users table
 * Stores pending driver application form data as JSON so email verification
 * works even when the PHP session has expired (different device, browser restart).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $stmt = $db->prepare("SHOW COLUMNS FROM users LIKE 'application_draft'");
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        $db->exec("ALTER TABLE users ADD COLUMN application_draft LONGTEXT NULL DEFAULT NULL AFTER updated_at");
        echo "Added application_draft column to users table.\n";
    } else {
        echo "Column application_draft already exists.\n";
    }

    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
