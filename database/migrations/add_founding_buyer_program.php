<?php
/**
 * Migration: Founding Buyer Program (Buyer Terms of Service Sec. 12)
 *
 * Two genuinely new mechanisms, confirmed by grep before building - neither
 * existed anywhere in this codebase:
 *
 * 1. First-Order Free Delivery (Sec 12.1/12.2) - the first 200 buyer
 *    accounts to place a qualifying delivery Order get that Order's base
 *    Delivery Fee waived, "determined by OCSAPP's systems based on order
 *    sequence" - a global, race-safe counter (founding_buyer_program is a
 *    single-row mutex, same FOR-UPDATE-row-lock pattern used everywhere else
 *    in this codebase for a shared balance/counter) decides eligibility at
 *    the moment a buyer's first-ever order is created, not at payment time -
 *    the fee shown to the buyer at checkout must already be final.
 * 2. Referral Credit (Sec 12.1) - $5 to both parties when a referred buyer
 *    completes (is delivered) their first Order, unlimited, available to
 *    every buyer regardless of Founding status. Reuses the store-credit
 *    ledger already built for Sec A6 (Returns Policy) rather than inventing
 *    a second wallet - same balance, a new transaction type.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fbColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function fbTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // --- Founding Buyer status (Sec 12.1/12.2) ---
    if (!fbColumnExists($db, 'users', 'founding_buyer')) {
        $db->exec("ALTER TABLE users ADD COLUMN founding_buyer TINYINT(1) NOT NULL DEFAULT 0");
        echo "users.founding_buyer added.\n";
    } else {
        echo "users.founding_buyer already exists. Skipping.\n";
    }
    if (!fbColumnExists($db, 'users', 'founding_buyer_number')) {
        $db->exec("ALTER TABLE users ADD COLUMN founding_buyer_number INT NULL");
        echo "users.founding_buyer_number added.\n";
    } else {
        echo "users.founding_buyer_number already exists. Skipping.\n";
    }
    if (!fbColumnExists($db, 'users', 'founding_buyer_granted_at')) {
        $db->exec("ALTER TABLE users ADD COLUMN founding_buyer_granted_at DATETIME NULL");
        echo "users.founding_buyer_granted_at added.\n";
    } else {
        echo "users.founding_buyer_granted_at already exists. Skipping.\n";
    }

    // --- Referral program (Sec 12.1) ---
    if (!fbColumnExists($db, 'users', 'referral_code')) {
        $db->exec("ALTER TABLE users ADD COLUMN referral_code VARCHAR(20) NULL");
        $db->exec("ALTER TABLE users ADD UNIQUE INDEX uk_users_referral_code (referral_code)");
        echo "users.referral_code added.\n";
    } else {
        echo "users.referral_code already exists. Skipping.\n";
    }
    if (!fbColumnExists($db, 'users', 'referred_by_user_id')) {
        $db->exec("ALTER TABLE users ADD COLUMN referred_by_user_id BIGINT UNSIGNED NULL");
        $db->exec("ALTER TABLE users ADD INDEX idx_users_referred_by (referred_by_user_id)");
        echo "users.referred_by_user_id added.\n";
    } else {
        echo "users.referred_by_user_id already exists. Skipping.\n";
    }
    if (!fbColumnExists($db, 'users', 'referral_bonus_credited_at')) {
        $db->exec("ALTER TABLE users ADD COLUMN referral_bonus_credited_at DATETIME NULL");
        echo "users.referral_bonus_credited_at added.\n";
    } else {
        echo "users.referral_bonus_credited_at already exists. Skipping.\n";
    }

    // --- Order-level record of the waived fee (transparency: buyer/admin can see it happened) ---
    if (!fbColumnExists($db, 'orders', 'founding_buyer_delivery_waived')) {
        $db->exec("ALTER TABLE orders ADD COLUMN founding_buyer_delivery_waived DECIMAL(6,2) NOT NULL DEFAULT 0.00");
        echo "orders.founding_buyer_delivery_waived added.\n";
    } else {
        echo "orders.founding_buyer_delivery_waived already exists. Skipping.\n";
    }

    // --- Global 200-slot counter - single-row mutex, locked via SELECT...FOR UPDATE
    //     at claim time so concurrent first-orders can't both grab slot #200. ---
    if (!fbTableExists($db, 'founding_buyer_program')) {
        $db->exec("
            CREATE TABLE founding_buyer_program (
                id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
                slots_used INT NOT NULL DEFAULT 0,
                slots_total INT NOT NULL DEFAULT 200,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $db->exec("INSERT INTO founding_buyer_program (id, slots_used, slots_total) VALUES (1, 0, 200)");
        echo "founding_buyer_program table created and seeded (0/200).\n";
    } else {
        echo "founding_buyer_program already exists. Skipping.\n";
    }

    // --- Widen the existing store-credit ledger (Sec A6) with a referral type,
    //     rather than building a second wallet for the same $5-toward-a-future-order concept. ---
    $db->exec("ALTER TABLE store_credit_transactions MODIFY COLUMN type ENUM('claim_refund_bonus','checkout_applied','admin_adjustment','referral_bonus') NOT NULL");
    echo "store_credit_transactions.type widened to include referral_bonus.\n";

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
