<?php
/**
 * Migration: Setup Business Accounts for Distribution Portal
 *
 * Creates:
 * - business_profiles table
 * - 'business' role in roles table
 *
 * Run: php database/migrations/setup_business_accounts.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "===========================================\n";
echo "Setting up Business Accounts...\n";
echo "===========================================\n\n";

try {
    $db = Database::getConnection();

    // Start transaction
    $db->beginTransaction();

    // 1. Create business_profiles table
    echo "Creating business_profiles table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS business_profiles (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT UNSIGNED NOT NULL UNIQUE,
            company_name VARCHAR(255) NOT NULL,

            -- Delivery Address
            delivery_street VARCHAR(255) NOT NULL,
            delivery_city VARCHAR(100) NOT NULL,
            delivery_province VARCHAR(50) NOT NULL,
            delivery_postal_code VARCHAR(20) NOT NULL,
            delivery_country VARCHAR(50) DEFAULT 'Canada',

            -- Billing Address (optional)
            billing_street VARCHAR(255) NULL,
            billing_city VARCHAR(100) NULL,
            billing_province VARCHAR(50) NULL,
            billing_postal_code VARCHAR(20) NULL,
            billing_country VARCHAR(50) NULL,
            use_delivery_for_billing TINYINT(1) DEFAULT 1,

            -- Tiered Access
            account_tier ENUM('standard', 'approved', 'premium') DEFAULT 'standard',
            credit_limit DECIMAL(10,2) DEFAULT 0.00,
            is_credit_approved TINYINT(1) DEFAULT 0,
            credit_approved_at DATETIME NULL,
            credit_approved_by BIGINT UNSIGNED NULL,

            -- Status
            status ENUM('active', 'suspended') DEFAULT 'active',
            notes TEXT NULL,

            -- Timestamps
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_company_name (company_name),
            INDEX idx_status (status),
            INDEX idx_account_tier (account_tier),
            INDEX idx_user_id (user_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    echo "  - business_profiles table created.\n";

    // 2. Add foreign key constraints
    echo "Adding foreign key constraints...\n";

    // Check if foreign key exists before adding
    $stmt = $db->query("
        SELECT COUNT(*) as cnt
        FROM information_schema.TABLE_CONSTRAINTS
        WHERE CONSTRAINT_SCHEMA = DATABASE()
        AND TABLE_NAME = 'business_profiles'
        AND CONSTRAINT_NAME = 'fk_business_user'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['cnt'] == 0) {
        $db->exec("
            ALTER TABLE business_profiles
            ADD CONSTRAINT fk_business_user
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ");
        echo "  - Foreign key fk_business_user added.\n";
    } else {
        echo "  - Foreign key fk_business_user already exists.\n";
    }

    // 3. Add 'business' role if not exists
    echo "Adding 'business' role...\n";

    $stmt = $db->prepare("SELECT id FROM roles WHERE name = 'business'");
    $stmt->execute();
    $existingRole = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$existingRole) {
        $stmt = $db->prepare("
            INSERT INTO roles (name, description, created_at, updated_at)
            VALUES ('business', 'Business/Distribution Customer', NOW(), NOW())
        ");
        $stmt->execute();
        echo "  - 'business' role added (ID: " . $db->lastInsertId() . ").\n";
    } else {
        echo "  - 'business' role already exists (ID: " . $existingRole['id'] . ").\n";
    }

    // Commit transaction
    $db->commit();

    echo "\n===========================================\n";
    echo "Business Accounts setup completed!\n";
    echo "===========================================\n";

} catch (PDOException $e) {
    // Rollback on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }

    echo "\nERROR: " . $e->getMessage() . "\n";
    echo "Migration failed. All changes have been rolled back.\n";
    exit(1);
}
