<?php
/**
 * Migration: Founding Business Partner Program
 *
 * Founding Business Program (draft, Updates/OCSAPP_Business_Founding_Program.pdf):
 * a 5-account cohort locked to Debutant-tier Distribution (5% commission,
 * $0/mo fee waived) for 6 months from approval. business_profiles has no
 * commission_rate column of its own (rate is normally only reached via
 * distribution_plan_id -> distribution_plans.commission_rate), so this adds
 * a nullable override column read preferentially at the real commission
 * call sites instead of mutating the shared plan row or repointing
 * distribution_plan_id (which also drives credit_limit and the paid/fee gate
 * in AdminBusinessController::updatePlan()).
 *
 * Same single-row-mutex pattern as founding_seller_supplier_program.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fbizColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function fbizTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // --- Founding Business status (Business Account Agreement Sec 4.3/7.9/8.12) ---
    foreach ([
        'founding_partner' => "TINYINT(1) NOT NULL DEFAULT 0",
        'founding_partner_number' => "INT NULL",
        'founding_partner_granted_at' => "DATETIME NULL",
        'founding_partner_expires_at' => "DATETIME NULL",
        'founding_commission_rate_override' => "DECIMAL(5,2) NULL",
    ] as $col => $def) {
        if (!fbizColumnExists($db, 'business_profiles', $col)) {
            $db->exec("ALTER TABLE business_profiles ADD COLUMN {$col} {$def}");
            echo "business_profiles.{$col} added.\n";
        } else {
            echo "business_profiles.{$col} already exists. Skipping.\n";
        }
    }

    // --- Global cohort counter - single-row mutex, locked via SELECT...FOR UPDATE
    //     at claim time, same pattern as founding_seller_program. ---
    if (!fbizTableExists($db, 'founding_business_program')) {
        $db->exec("
            CREATE TABLE founding_business_program (
                id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
                slots_used INT NOT NULL DEFAULT 0,
                slots_total INT NOT NULL DEFAULT 5,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $db->exec("INSERT INTO founding_business_program (id, slots_used, slots_total) VALUES (1, 0, 5)");
        echo "founding_business_program table created and seeded (0/5).\n";
    } else {
        echo "founding_business_program already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
