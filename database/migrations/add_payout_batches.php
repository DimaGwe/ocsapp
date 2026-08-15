<?php
/**
 * Migration: Weekly Payout Batches (Ecosystem Backend Requirements Sec. 9 /
 * contract-reconciliation finding #4)
 *
 * Seller and Driver Account Agreements both promise weekly Monday direct
 * deposit. No automated cadence existed at all before this - seller_payouts
 * and delivery_earnings only ever had an admin "mark paid whenever" flow with
 * no schedule. There is still no real bank-transfer rail anywhere in this
 * codebase (Stripe is only ever used to charge people here, never to pay
 * them) - Dima explicitly chose to scope this as automating the weekly
 * aggregation/notification cadence, not literal money movement. A Monday
 * cron groups pending ledger rows into a batch; admin still executes the
 * actual transfer outside the system and marks the batch paid, same
 * precedent as every other payout mechanism in this codebase.
 *
 * seller_payout_batches also enforces the $25 rollover threshold (Seller
 * Account Agreement Sec 7.2 / seller-central.php marketing copy) - a shop
 * whose accumulated pending net total is under $25 is left unbatched
 * (batch_id stays NULL) so it naturally rolls into the following week's run.
 * driver_payout_batches has no such threshold - nothing in the Driver
 * Independent Contractor Agreement (Sec 8.5) specifies one, confirmed by a
 * full read of the UPDATED draft.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function pbColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    $stmt = $db->query("SHOW TABLES LIKE 'seller_payout_batches'");
    if ($stmt->fetch()) {
        echo "seller_payout_batches already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE seller_payout_batches (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                batch_number VARCHAR(30) NOT NULL,
                batch_date DATE NOT NULL,
                status ENUM('open','completed') NOT NULL DEFAULT 'open',
                shop_count INT UNSIGNED NOT NULL DEFAULT 0,
                item_count INT UNSIGNED NOT NULL DEFAULT 0,
                total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME NULL,
                UNIQUE KEY uniq_batch_date (batch_date),
                UNIQUE KEY uniq_batch_number (batch_number)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "seller_payout_batches table created.\n";
    }

    $stmt = $db->query("SHOW TABLES LIKE 'driver_payout_batches'");
    if ($stmt->fetch()) {
        echo "driver_payout_batches already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE driver_payout_batches (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                batch_number VARCHAR(30) NOT NULL,
                batch_date DATE NOT NULL,
                status ENUM('open','completed') NOT NULL DEFAULT 'open',
                driver_count INT UNSIGNED NOT NULL DEFAULT 0,
                item_count INT UNSIGNED NOT NULL DEFAULT 0,
                total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                completed_at DATETIME NULL,
                UNIQUE KEY uniq_batch_date (batch_date),
                UNIQUE KEY uniq_batch_number (batch_number)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "driver_payout_batches table created.\n";
    }

    if (!pbColumnExists($db, 'seller_payouts', 'batch_id')) {
        $db->exec("ALTER TABLE seller_payouts ADD COLUMN batch_id BIGINT UNSIGNED NULL AFTER status");
        $db->exec("ALTER TABLE seller_payouts ADD INDEX idx_batch (batch_id)");
        echo "seller_payouts.batch_id added.\n";
    } else {
        echo "seller_payouts.batch_id already exists. Skipping.\n";
    }

    if (!pbColumnExists($db, 'delivery_earnings', 'batch_id')) {
        $db->exec("ALTER TABLE delivery_earnings ADD COLUMN batch_id BIGINT UNSIGNED NULL AFTER payment_status");
        $db->exec("ALTER TABLE delivery_earnings ADD INDEX idx_batch (batch_id)");
        echo "delivery_earnings.batch_id added.\n";
    } else {
        echo "delivery_earnings.batch_id already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
