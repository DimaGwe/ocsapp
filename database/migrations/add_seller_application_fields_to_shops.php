<?php
/**
 * Migration: Add NEQ/legal-identity/address/document fields to shops
 * to support the new Seller Central apply flow (parity with the
 * supplier_applications field set).
 *
 * Run: php database/migrations/add_seller_application_fields_to_shops.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: add_seller_application_fields_to_shops\n";

try {
    $cols = $db->query("SHOW COLUMNS FROM shops LIKE 'neq_number'")->fetchAll();
    if (empty($cols)) {
        $db->exec("
            ALTER TABLE shops
            ADD COLUMN neq_number VARCHAR(20) NULL COMMENT 'Quebec business registration number' AFTER slug,
            ADD COLUMN legal_name VARCHAR(255) NULL COMMENT 'Registered legal business name' AFTER neq_number,
            ADD COLUMN operating_names VARCHAR(255) NULL COMMENT 'Trade/operating name(s), if different from legal name' AFTER legal_name,
            ADD COLUMN registered_address_street VARCHAR(255) NULL AFTER operating_names,
            ADD COLUMN registered_address_city VARCHAR(100) NULL AFTER registered_address_street,
            ADD COLUMN registered_address_province VARCHAR(100) NOT NULL DEFAULT 'Quebec' AFTER registered_address_city,
            ADD COLUMN registered_address_postal VARCHAR(10) NULL AFTER registered_address_province,
            ADD COLUMN doc_certificate_incorporation VARCHAR(500) NULL COMMENT 'Relative web path to uploaded document' AFTER registered_address_postal,
            ADD COLUMN doc_declaration_registration VARCHAR(500) NULL AFTER doc_certificate_incorporation,
            ADD COLUMN doc_enterprise_register VARCHAR(500) NULL AFTER doc_declaration_registration,
            ADD INDEX idx_neq_number (neq_number)
        ");
        echo "  [OK] Added NEQ/legal/address/document columns to shops\n";
    } else {
        echo "  [SKIP] shops.neq_number already exists\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
