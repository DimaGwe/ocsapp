<?php
/**
 * Migration: Business Credit Management System schema (Ecosystem Backend
 * Requirements Sec. 5 - all of 5.1/5.2/5.3)
 *
 * Adds, in one pass:
 *   - Distribution plan assignment + billing cycle (Sec 5.2, depends on
 *     distribution_plans - run create_distribution_plans.php first)
 *   - Card-on-file (Stripe Customer + saved PaymentMethod, Sec 5.3)
 *   - Net-30 qualification/approval state machine (Sec 5.1) - net-30 does not
 *     exist anywhere in this codebase today (confirmed: no column, no enum,
 *     no controller branch), so this is new functionality, not a retrofit.
 *   - Credit review state (Sec 5.1's bureau-check requirement - no live
 *     Equifax/D&B CA integration exists or can be built without real
 *     credentials Jack would need to obtain; this degrades to the same
 *     manual-admin-review pattern already used for NEQ verification)
 *   - Suspension state (Sec 5.3)
 *
 * All new accounts default to payment_terms='prepay' - net-30 is opt-in via
 * the qualification + approval flow, never automatic.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function cmColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function cmAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (cmColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    // --- Distribution plan assignment (Sec 5.2) ---
    cmAddColumn($db, 'business_profiles', 'distribution_plan_id', "BIGINT UNSIGNED NULL AFTER account_tier");
    cmAddColumn($db, 'business_profiles', 'plan_started_at', "DATETIME NULL AFTER distribution_plan_id");
    cmAddColumn($db, 'business_profiles', 'next_billing_date', "DATE NULL AFTER plan_started_at");
    cmAddColumn($db, 'business_profiles', 'plan_status', "ENUM('active','past_due','cancelled') NOT NULL DEFAULT 'active' AFTER next_billing_date");

    // --- Card-on-file (Sec 5.3) ---
    cmAddColumn($db, 'business_profiles', 'stripe_customer_id', "VARCHAR(255) NULL AFTER plan_status");
    cmAddColumn($db, 'business_profiles', 'stripe_payment_method_id', "VARCHAR(255) NULL AFTER stripe_customer_id");
    cmAddColumn($db, 'business_profiles', 'card_brand', "VARCHAR(30) NULL AFTER stripe_payment_method_id");
    cmAddColumn($db, 'business_profiles', 'card_last4', "VARCHAR(4) NULL AFTER card_brand");
    cmAddColumn($db, 'business_profiles', 'card_exp_month', "TINYINT UNSIGNED NULL AFTER card_last4");
    cmAddColumn($db, 'business_profiles', 'card_exp_year', "SMALLINT UNSIGNED NULL AFTER card_exp_month");

    // --- Net-30 qualification + approval state machine (Sec 5.1) ---
    cmAddColumn($db, 'business_profiles', 'payment_terms', "ENUM('prepay','net30') NOT NULL DEFAULT 'prepay' AFTER card_exp_year");
    cmAddColumn($db, 'business_profiles', 'net30_eligible_at', "DATETIME NULL AFTER payment_terms");
    cmAddColumn($db, 'business_profiles', 'net30_approved_at', "DATETIME NULL AFTER net30_eligible_at");
    cmAddColumn($db, 'business_profiles', 'net30_approved_by', "BIGINT UNSIGNED NULL AFTER net30_approved_at");
    cmAddColumn($db, 'business_profiles', 'credit_review_status', "ENUM('not_required','pending_manual_review','bureau_approved','bureau_declined','admin_approved','admin_waived') NOT NULL DEFAULT 'not_required' AFTER net30_approved_by");
    cmAddColumn($db, 'business_profiles', 'credit_review_notes', "TEXT NULL AFTER credit_review_status");

    // --- Suspension state (Sec 5.3) ---
    cmAddColumn($db, 'business_profiles', 'net30_suspended_at', "DATETIME NULL AFTER credit_review_notes");
    cmAddColumn($db, 'business_profiles', 'net30_suspension_reason', "VARCHAR(255) NULL AFTER net30_suspended_at");

    // FK: distribution_plan_id -> distribution_plans.id (best-effort; skip if constraint already present)
    $fkCheck = $db->query("
        SELECT COUNT(*) FROM information_schema.table_constraints
        WHERE table_schema = DATABASE() AND table_name = 'business_profiles' AND constraint_name = 'fk_business_profiles_distribution_plan'
    ")->fetchColumn();
    if ($fkCheck > 0) {
        echo "FK fk_business_profiles_distribution_plan already exists. Skipping.\n";
    } else {
        try {
            $db->exec("
                ALTER TABLE business_profiles
                ADD CONSTRAINT fk_business_profiles_distribution_plan
                FOREIGN KEY (distribution_plan_id) REFERENCES distribution_plans(id) ON DELETE SET NULL
            ");
            echo "FK fk_business_profiles_distribution_plan added.\n";
        } catch (\PDOException $e) {
            echo "FK add skipped (non-fatal): " . $e->getMessage() . "\n";
        }
    }

    // Default every existing business account onto the free Procurement plan so
    // credit_limit/commission_rate always resolve to something, not NULL.
    $procurementId = $db->query("SELECT id FROM distribution_plans WHERE code = 'procurement' LIMIT 1")->fetchColumn();
    if ($procurementId) {
        $upd = $db->prepare("
            UPDATE business_profiles SET distribution_plan_id = ?, plan_started_at = COALESCE(plan_started_at, created_at)
            WHERE distribution_plan_id IS NULL
        ");
        $upd->execute([$procurementId]);
        echo "Defaulted {$upd->rowCount()} existing business account(s) onto the Procurement plan.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
