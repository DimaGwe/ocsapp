<?php
/**
 * Migration: Add subscription_package and commission_rate to suppliers
 * and supplier_applications tables.
 *
 * Run: php database/migrations/add_subscription_package_to_suppliers.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';

$db = Database::getConnection();

echo "Running migration: add_subscription_package_to_suppliers\n";

try {
    $db->beginTransaction();

    // 1. suppliers table
    $cols = $db->query("SHOW COLUMNS FROM suppliers LIKE 'subscription_package'")->fetchAll();
    if (empty($cols)) {
        $db->exec("
            ALTER TABLE suppliers
            ADD COLUMN subscription_package ENUM('Essential','Experience','Prestige','Enterprise') NOT NULL DEFAULT 'Essential'
                COMMENT 'Selected subscription tier' AFTER status,
            ADD COLUMN commission_rate DECIMAL(5,2) NOT NULL DEFAULT 12.00
                COMMENT 'Commission % applied to orders' AFTER subscription_package,
            ADD INDEX idx_subscription_package (subscription_package)
        ");
        echo "  [OK] Added subscription_package + commission_rate to suppliers\n";
    } else {
        echo "  [SKIP] suppliers.subscription_package already exists\n";
    }

    // 2. supplier_applications table
    $cols2 = $db->query("SHOW COLUMNS FROM supplier_applications LIKE 'subscription_package'")->fetchAll();
    if (empty($cols2)) {
        $db->exec("
            ALTER TABLE supplier_applications
            ADD COLUMN subscription_package ENUM('Essential','Experience','Prestige','Enterprise') NOT NULL DEFAULT 'Essential'
                COMMENT 'Package selected during application' AFTER status,
            ADD INDEX idx_sa_subscription_package (subscription_package)
        ");
        echo "  [OK] Added subscription_package to supplier_applications\n";
    } else {
        echo "  [SKIP] supplier_applications.subscription_package already exists\n";
    }

    // 3. Set commission_rate defaults based on existing package value (all default to Essential -> 12%)
    //    When admin assigns a real package later the rate will be updated.
    // Nothing extra needed — DEFAULT 12.00 covers new rows; existing rows get 12.00 from ALTER.

    $db->commit();
    echo "\nMigration complete.\n";

} catch (Exception $e) {
    $db->rollBack();
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
