<?php
/**
 * Migration: Founding program bonuses and driver perks.
 *
 * - founding_bonuses: one ledger for the Founding Driver and Founding Supplier
 *   milestone and referral bonuses (Driver Agreement Sec 8.13, Supplier
 *   Agreement Sec 7.4). The UNIQUE key is what guarantees a bonus is paid
 *   once: awarding is an INSERT IGNORE on it.
 * - driver_applications.referred_by_user_id: who referred this driver
 *   (referral code typed on the application).
 * - users.founding_kit_status: Founding Driver equipment kit tracking.
 * - suppliers.referred_by_supplier_id: supplier referrals (the referral code a
 *   supplier shares is its existing supplier_code).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fbnColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    $db->exec("
        CREATE TABLE IF NOT EXISTS founding_bonuses (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            program ENUM('driver','supplier') NOT NULL,
            bonus_type ENUM('milestone','referral_referrer','referral_referred') NOT NULL,
            beneficiary_id BIGINT UNSIGNED NOT NULL COMMENT 'users.id for drivers, suppliers.id for suppliers',
            related_id BIGINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'the other party of a referral, 0 for a milestone',
            amount DECIMAL(10,2) NOT NULL,
            earning_id BIGINT UNSIGNED NULL COMMENT 'delivery_earnings.id carrying a driver bonus',
            status ENUM('pending','paid') NOT NULL DEFAULT 'pending' COMMENT 'supplier bonuses: paid with the next payout',
            paid_at DATETIME NULL,
            notes VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_bonus (program, bonus_type, beneficiary_id, related_id),
            KEY idx_beneficiary (program, beneficiary_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "founding_bonuses table ready.\n";

    $columns = [
        ['driver_applications', 'referred_by_user_id', "ADD COLUMN referred_by_user_id BIGINT UNSIGNED NULL"],
        ['users', 'founding_kit_status', "ADD COLUMN founding_kit_status ENUM('pending','shipped','delivered') NULL"],
        ['users', 'founding_kit_updated_at', "ADD COLUMN founding_kit_updated_at DATETIME NULL"],
        ['suppliers', 'referred_by_supplier_id', "ADD COLUMN referred_by_supplier_id BIGINT UNSIGNED NULL"],
    ];
    foreach ($columns as [$table, $name, $ddl]) {
        if (fbnColumnExists($db, $table, $name)) {
            echo "{$table}.{$name} already exists, skipping.\n";
            continue;
        }
        $db->exec("ALTER TABLE {$table} {$ddl}");
        echo "Added {$table}.{$name}.\n";
    }

    // Existing Founding Drivers are owed a kit
    $n = $db->exec("UPDATE users SET founding_kit_status = 'pending', founding_kit_updated_at = NOW() WHERE founding_driver = 1 AND founding_kit_status IS NULL");
    echo "Founding Drivers marked kit pending: {$n}.\n";

    echo "Done.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
