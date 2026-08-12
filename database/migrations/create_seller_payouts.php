<?php
/**
 * Migration: Seller Payout Ledger (prerequisite for Ecosystem Backend
 * Requirements Sec. 4.3 Dynamic Chargeback)
 *
 * No seller payout tracking exists anywhere in this codebase - confirmed by
 * full-repo grep before building this. Seller Central's marketing copy
 * promises "Monday payouts" and a commission structure (15-8/12-6/10-5% by
 * tier), but nothing in the database or a controller actually computes or
 * tracks what a shop is owed. Sec 4.3 requires deducting a vendor-caused
 * chargeback from "the seller's next pending payout" - there is no such
 * thing to deduct from without this table.
 *
 * Deliberately scoped down from the full tiered-commission/subscription
 * system (which doesn't exist either, mirrors what Distribution plans
 * needed before Sec 5): shops.commission_rate is a single flat admin-
 * editable rate, not the 3-tier $0/$39/$89 + delivery-vs-pickup split from
 * the Seller Central marketing copy. Building that properly is a separate,
 * comparably-sized undertaking (mirrors distribution_plans) - flagged, not
 * built here. This ledger exists so a chargeback has something real to net
 * against; it does not implement actual payout execution (bank transfer)
 * either - admin marks rows paid manually, same precedent already used for
 * supplier/distribution payments.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function spColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!spColumnExists($db, 'shops', 'commission_rate')) {
        $db->exec("ALTER TABLE shops ADD COLUMN commission_rate DECIMAL(5,2) NOT NULL DEFAULT 10.00 AFTER min_order_amount");
        echo "shops.commission_rate added.\n";
    } else {
        echo "shops.commission_rate already exists. Skipping.\n";
    }

    $stmt = $db->query("SHOW TABLES LIKE 'seller_payouts'");
    if ($stmt->fetch()) {
        echo "seller_payouts already exists. Skipping table creation.\n";
    } else {
        $db->exec("
            CREATE TABLE seller_payouts (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                shop_id BIGINT UNSIGNED NOT NULL,
                order_id BIGINT UNSIGNED NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                commission_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                net_payout_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                chargeback_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                status ENUM('pending','paid','held') NOT NULL DEFAULT 'pending',
                paid_at DATETIME NULL,
                paid_by BIGINT UNSIGNED NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uniq_order (order_id),
                INDEX idx_shop_status (shop_id, status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "seller_payouts table created.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
