<?php
/**
 * Migration: Buyer Store Credit (Returns & Refund Policy Track A, Sec. A6).
 *
 * "If you choose OCSAPP store credit instead of a cash refund, we add a 10%
 * bonus value to the credit... always optional; cash refund remains your
 * right under Quebec consumer law and is never withheld in favor of credit."
 *
 * Genuinely new capability - grepped the whole codebase before building this
 * (store_credit/storeCredit), confirmed nothing existed. Refunds were
 * cash-only via Stripe\Refund (built for Sec 4's Returnless Refunds).
 *
 * Ledger design mirrors the distribution_credit_events audit-trail pattern
 * already used for business net-30 credit (Sec 5) - every balance change
 * writes a row, balance itself is a denormalized running total on `users`
 * for fast reads.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function bscColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function bscTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = ?
    ");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!bscColumnExists($db, 'users', 'store_credit_balance')) {
        $db->exec("ALTER TABLE users ADD COLUMN store_credit_balance DECIMAL(10,2) NOT NULL DEFAULT 0.00");
        echo "users.store_credit_balance added.\n";
    } else {
        echo "users.store_credit_balance already exists. Skipping.\n";
    }

    if (!bscColumnExists($db, 'order_claims', 'preferred_refund_method')) {
        $db->exec("ALTER TABLE order_claims ADD COLUMN preferred_refund_method ENUM('cash','store_credit') NOT NULL DEFAULT 'cash' AFTER claimed_value");
        echo "order_claims.preferred_refund_method added.\n";
    } else {
        echo "order_claims.preferred_refund_method already exists. Skipping.\n";
    }

    if (!bscColumnExists($db, 'orders', 'store_credit_applied')) {
        $db->exec("ALTER TABLE orders ADD COLUMN store_credit_applied DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER discount");
        echo "orders.store_credit_applied added.\n";
    } else {
        echo "orders.store_credit_applied already exists. Skipping.\n";
    }

    if (!bscTableExists($db, 'store_credit_transactions')) {
        $db->exec("
            CREATE TABLE store_credit_transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT UNSIGNED NOT NULL,
                amount DECIMAL(10,2) NOT NULL COMMENT 'positive = credit added, negative = credit spent',
                type ENUM('claim_refund_bonus','checkout_applied','admin_adjustment') NOT NULL,
                reference_claim_id BIGINT UNSIGNED NULL,
                reference_order_id BIGINT UNSIGNED NULL,
                balance_after DECIMAL(10,2) NOT NULL,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_user (user_id),
                INDEX idx_claim (reference_claim_id),
                INDEX idx_order (reference_order_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        echo "store_credit_transactions table created.\n";
    } else {
        echo "store_credit_transactions already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
