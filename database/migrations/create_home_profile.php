<?php
/**
 * Migration: Home Profile (teen accounts under a parent or guardian).
 * Standalone accounts are 18+; teens 13 to 17 join only through a guardian's Home Profile.
 * - home_profile_members: guardian <-> member link, invite token (hashed), status
 * - users.account_type / users.birth_date: marks member accounts
 * - users.stripe_customer_id: guardian's saved card, charged for member orders
 * - products.age_restricted: 18+ products, blocked for members
 * - orders.guardian_user_id: set on member orders (supervision + billing)
 *
 * Run: php database/migrations/create_home_profile.php
 */

define('BASE_PATH', dirname(__DIR__, 2));
require BASE_PATH . '/bootstrap/init.php';
require BASE_PATH . '/config/database.php';

$db = Database::getConnection();

echo "Running migration: create_home_profile\n";

function hpColumnExists(PDO $db, string $table, string $column): bool
{
    // SHOW COLUMNS ... LIKE ? can't be prepared natively (ATTR_EMULATE_PREPARES is off)
    $stmt = $db->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->execute([$table, $column]);
    return (bool) $stmt->fetch();
}

try {
    $db->exec("CREATE TABLE IF NOT EXISTS home_profile_members (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        guardian_user_id BIGINT UNSIGNED NOT NULL,
        member_user_id BIGINT UNSIGNED NULL,
        invite_email VARCHAR(255) NOT NULL,
        first_name VARCHAR(100) NOT NULL,
        birth_date DATE NOT NULL,
        invite_token_hash CHAR(64) NULL,
        invite_expires_at DATETIME NULL,
        status ENUM('invited','active','removed') NOT NULL DEFAULT 'invited',
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        accepted_at DATETIME NULL,
        removed_at DATETIME NULL,
        KEY idx_guardian (guardian_user_id, status),
        UNIQUE KEY uq_member (member_user_id),
        UNIQUE KEY uq_token (invite_token_hash),
        CONSTRAINT fk_hpm_guardian FOREIGN KEY (guardian_user_id) REFERENCES users(id) ON DELETE CASCADE,
        CONSTRAINT fk_hpm_member FOREIGN KEY (member_user_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "  [OK] home_profile_members\n";

    $columns = [
        ['users', 'account_type', "ALTER TABLE users ADD COLUMN account_type ENUM('standard','home_member') NOT NULL DEFAULT 'standard' AFTER role"],
        ['users', 'birth_date', "ALTER TABLE users ADD COLUMN birth_date DATE NULL AFTER account_type"],
        ['users', 'stripe_customer_id', "ALTER TABLE users ADD COLUMN stripe_customer_id VARCHAR(255) NULL"],
        ['users', 'stripe_payment_method_id', "ALTER TABLE users ADD COLUMN stripe_payment_method_id VARCHAR(255) NULL"],
        ['users', 'card_brand', "ALTER TABLE users ADD COLUMN card_brand VARCHAR(30) NULL"],
        ['users', 'card_last4', "ALTER TABLE users ADD COLUMN card_last4 CHAR(4) NULL"],
        ['products', 'age_restricted', "ALTER TABLE products ADD COLUMN age_restricted TINYINT(1) NOT NULL DEFAULT 0 COMMENT '18+ only; hidden and blocked for Home Profile members'"],
        ['orders', 'guardian_user_id', "ALTER TABLE orders ADD COLUMN guardian_user_id BIGINT UNSIGNED NULL COMMENT 'Home Profile guardian billed and notified for a member order', ADD KEY idx_orders_guardian (guardian_user_id)"],
    ];
    foreach ($columns as [$table, $column, $sql]) {
        if (hpColumnExists($db, $table, $column)) {
            echo "  [SKIP] $table.$column exists\n";
        } else {
            $db->exec($sql);
            echo "  [OK] $table.$column\n";
        }
    }

    echo "\nMigration complete.\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
