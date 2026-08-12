<?php
/**
 * Migration: distribution_plans (prerequisite for Ecosystem Backend Requirements Sec 5.2)
 *
 * The Distribution pricing tiers (Procurement/Débutant/Pro/Enterprise) shown on
 * distribution/landing.php are marketing copy only - no backend plan/subscription
 * concept exists to derive Sec 5.2's tiered credit limits from. This table is that
 * missing plumbing: monthly_fee + commission_rate + credit_limit per plan, so
 * business_profiles.credit_limit (the existing, already-used effective limit) can
 * be suggested/reset from a real plan assignment instead of being a free-standing
 * admin-typed number with no connection to what the business is actually paying for.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $stmt = $db->query("SHOW TABLES LIKE 'distribution_plans'");
    if ($stmt->fetch()) {
        echo "distribution_plans already exists. Skipping table creation.\n";
    } else {
        $db->exec("
            CREATE TABLE distribution_plans (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                code VARCHAR(30) NOT NULL UNIQUE,
                name VARCHAR(100) NOT NULL,
                name_fr VARCHAR(100) NOT NULL,
                monthly_fee DECIMAL(8,2) NOT NULL DEFAULT 0.00,
                commission_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                credit_limit DECIMAL(10,2) NULL,
                is_negotiated TINYINT(1) NOT NULL DEFAULT 0,
                is_active TINYINT(1) NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "distribution_plans table created.\n";
    }

    // Seed the 4 doc-specified plans (idempotent - only inserts if the code is missing).
    // credit_limit NULL + is_negotiated=1 for Enterprise per Sec 5.2 ("negotiated individually").
    $plans = [
        ['procurement', 'Procurement', 'Approvisionnement', 0.00, 1.00, 2500.00, 0, 1],
        ['debutant',    'Distribution Starter', 'Distribution Débutant', 49.00, 5.00, 2500.00, 0, 2],
        ['pro',         'Distribution Pro', 'Distribution Pro', 179.00, 7.00, 10000.00, 0, 3],
        ['enterprise',  'Distribution Enterprise', 'Distribution Enterprise', 0.00, 0.00, null, 1, 4],
    ];

    $checkStmt = $db->prepare("SELECT id FROM distribution_plans WHERE code = ?");
    $insertStmt = $db->prepare("
        INSERT INTO distribution_plans (code, name, name_fr, monthly_fee, commission_rate, credit_limit, is_negotiated, sort_order)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    foreach ($plans as $plan) {
        $checkStmt->execute([$plan[0]]);
        if ($checkStmt->fetch()) {
            echo "Plan '{$plan[0]}' already exists. Skipping.\n";
            continue;
        }
        $insertStmt->execute($plan);
        echo "Plan '{$plan[0]}' seeded.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
