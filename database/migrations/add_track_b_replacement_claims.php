<?php
/**
 * Migration: Track B B5 Replacement (Returns Policy Sec B5) - Distribution-
 * shipment claim schema + business credit note ledger.
 *
 * Two gaps closed here:
 * 1. order_claims previously only supported Track B claims against
 *    distribution_request_id (Approvisionnement) - Distribution *shipments*
 *    (Business Account Agreement Sec 8, distribution_shipments) had no
 *    column to reference them at all, so a claim could never be filed
 *    against one. See [[project_ecosystem_requirements]] finding #5.
 * 2. None of B5's three resolution options (Replacement/Credit note +5%/
 *    Cash refund-payout adjustment) existed for ANY Track B claim before
 *    this - ReturnsDispatchHelper::resolveReturnAction() explicitly
 *    rejected non-Track-A claims. Credit note needs a real balance to add
 *    to ("applied to your account balance"), which doesn't exist on
 *    business_profiles yet - mirrors the users.store_credit_balance /
 *    store_credit_transactions pattern built for Track A's Sec A6.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function tbColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function tbTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // 1. order_claims: distribution_shipment_id (the actual schema gap flagged)
    if (!tbColumnExists($db, 'order_claims', 'distribution_shipment_id')) {
        $db->exec("ALTER TABLE order_claims ADD COLUMN distribution_shipment_id INT NULL AFTER distribution_request_id");
        $db->exec("ALTER TABLE order_claims ADD INDEX idx_distribution_shipment (distribution_shipment_id)");
        echo "order_claims.distribution_shipment_id added.\n";
    } else {
        echo "order_claims.distribution_shipment_id already exists. Skipping.\n";
    }

    // 2. Links to the $0 replacement record created for a B5 "Replacement (default)"
    //    resolution - separate columns since a Track B claim references exactly one
    //    of distribution_request_id/distribution_shipment_id, never both.
    if (!tbColumnExists($db, 'order_claims', 'replacement_request_id')) {
        $db->exec("ALTER TABLE order_claims ADD COLUMN replacement_request_id INT NULL AFTER replacement_order_id");
        echo "order_claims.replacement_request_id added.\n";
    } else {
        echo "order_claims.replacement_request_id already exists. Skipping.\n";
    }
    if (!tbColumnExists($db, 'order_claims', 'replacement_shipment_id')) {
        $db->exec("ALTER TABLE order_claims ADD COLUMN replacement_shipment_id INT NULL AFTER replacement_request_id");
        echo "order_claims.replacement_shipment_id added.\n";
    } else {
        echo "order_claims.replacement_shipment_id already exists. Skipping.\n";
    }

    // 3. Widen preferred_refund_method for Track B's "credit note" language (Sec B5) -
    //    distinct from Track A's "store_credit" term even though the mechanism (balance +
    //    bonus) is the same shape, because B5's bonus rate (5%) and ledger (business, not
    //    buyer) differ from A6.
    $db->exec("ALTER TABLE order_claims MODIFY COLUMN preferred_refund_method ENUM('cash','store_credit','credit_note') NOT NULL DEFAULT 'cash'");
    echo "order_claims.preferred_refund_method widened to include credit_note.\n";

    // 4. business_profiles credit note balance (Sec B5: "applied to your account balance
    //    for use against future orders")
    if (!tbColumnExists($db, 'business_profiles', 'credit_note_balance')) {
        $db->exec("ALTER TABLE business_profiles ADD COLUMN credit_note_balance DECIMAL(10,2) NOT NULL DEFAULT 0.00");
        echo "business_profiles.credit_note_balance added.\n";
    } else {
        echo "business_profiles.credit_note_balance already exists. Skipping.\n";
    }

    // 5. business_credit_note_transactions ledger - mirrors store_credit_transactions'
    //    audit-trail-not-just-a-mutated-column pattern.
    if (!tbTableExists($db, 'business_credit_note_transactions')) {
        $db->exec("
            CREATE TABLE business_credit_note_transactions (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                business_profile_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL COMMENT 'positive = credit added, negative = credit spent',
                type ENUM('claim_refund_bonus','applied_to_order','admin_adjustment') NOT NULL,
                reference_claim_id BIGINT UNSIGNED NULL,
                balance_after DECIMAL(10,2) NOT NULL,
                notes VARCHAR(255) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_business (business_profile_id),
                INDEX idx_claim (reference_claim_id),
                CONSTRAINT fk_bcnt_business_profile FOREIGN KEY (business_profile_id)
                    REFERENCES business_profiles(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "business_credit_note_transactions table created.\n";
    } else {
        echo "business_credit_note_transactions already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
