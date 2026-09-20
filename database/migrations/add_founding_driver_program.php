<?php
/**
 * Migration: Founding Driver Partner Program
 *
 * Founding Driver Program (draft, Updates/OCSAPP_Founding_Driver_Program.pdf) -
 * a 50-driver cohort earning a permanent "Founding Driver" badge. Unlike
 * Seller/Supplier, this has no locked commission rate or expiry - the
 * milestone bonus, referral bonus, and dispatch-priority tier described in
 * the PDF are deliberately deferred (no crediting/tier mechanism exists yet
 * in this codebase), so only the cohort slot + badge are built here.
 *
 * Same single-row-mutex pattern as founding_buyer_program /
 * founding_seller_supplier_program.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fdColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function fdTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // --- Founding Driver status (Driver Agreement Sec 6.2/8.13/Schedule D) ---
    // Distinct column names from founding_buyer's users.founding_buyer* - drivers
    // are users rows too, must not collide.
    foreach ([
        'founding_driver' => "TINYINT(1) NOT NULL DEFAULT 0",
        'founding_driver_number' => "INT NULL",
        'founding_driver_granted_at' => "DATETIME NULL",
    ] as $col => $def) {
        if (!fdColumnExists($db, 'users', $col)) {
            $db->exec("ALTER TABLE users ADD COLUMN {$col} {$def}");
            echo "users.{$col} added.\n";
        } else {
            echo "users.{$col} already exists. Skipping.\n";
        }
    }

    // --- Global cohort counter - single-row mutex, locked via SELECT...FOR UPDATE
    //     at claim time, same pattern as founding_seller_program. ---
    if (!fdTableExists($db, 'founding_driver_program')) {
        $db->exec("
            CREATE TABLE founding_driver_program (
                id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
                slots_used INT NOT NULL DEFAULT 0,
                slots_total INT NOT NULL DEFAULT 50,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $db->exec("INSERT INTO founding_driver_program (id, slots_used, slots_total) VALUES (1, 0, 50)");
        echo "founding_driver_program table created and seeded (0/50).\n";
    } else {
        echo "founding_driver_program already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
