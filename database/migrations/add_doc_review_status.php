<?php
/**
 * Migration: Add document review status columns to supplier_applications
 * Tracks admin review status for each verification document: pending, approved, rejected, na
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

try {
    $db = Database::getConnection();

    $columns = [
        'doc_certificate_incorporation_status' => "ENUM('pending','approved','rejected','na') DEFAULT 'pending'",
        'doc_declaration_registration_status' => "ENUM('pending','approved','rejected','na') DEFAULT 'pending'",
        'doc_enterprise_register_status' => "ENUM('pending','approved','rejected','na') DEFAULT 'pending'",
    ];

    foreach ($columns as $col => $definition) {
        // Check if column exists
        $check = $db->query("SHOW COLUMNS FROM supplier_applications LIKE '{$col}'");

        if (!$check->fetch()) {
            $db->exec("ALTER TABLE supplier_applications ADD COLUMN {$col} {$definition}");
            echo "Added column: {$col}\n";
        } else {
            echo "Column already exists: {$col}\n";
        }
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
    exit(1);
}
