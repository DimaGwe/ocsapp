<?php
/**
 * Migration: Founding Seller/Supplier Partner Program
 *
 * Seller Agreement Sec 3.2/6.1-6.2/Schedule A (20-seller cap) and Supplier
 * Agreement Sec 4.2/7.4/Schedule A (15-supplier cap) both shipped live with a
 * "Position ___ of 20/15 in the cohort" blank and zero backend behind it - no
 * DB table, no cohort counter, no code anywhere. Same single-row-mutex pattern
 * as the working founding_buyer_program (add_founding_buyer_program.php).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function fssColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function fssTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // --- Founding Seller status (Seller Agreement Sec 3.2/6.1-6.2) ---
    foreach ([
        'founding_partner' => "TINYINT(1) NOT NULL DEFAULT 0",
        'founding_partner_number' => "INT NULL",
        'founding_partner_granted_at' => "DATETIME NULL",
        'founding_partner_expires_at' => "DATETIME NULL",
        'founding_free_deliveries_remaining' => "TINYINT UNSIGNED NOT NULL DEFAULT 0",
    ] as $col => $def) {
        if (!fssColumnExists($db, 'shops', $col)) {
            $db->exec("ALTER TABLE shops ADD COLUMN {$col} {$def}");
            echo "shops.{$col} added.\n";
        } else {
            echo "shops.{$col} already exists. Skipping.\n";
        }
    }

    // --- Founding Supplier status (Supplier Agreement Sec 4.2/7.4) - same four
    //     fields, no free-deliveries counter (that benefit is seller-only). ---
    foreach ([
        'founding_partner' => "TINYINT(1) NOT NULL DEFAULT 0",
        'founding_partner_number' => "INT NULL",
        'founding_partner_granted_at' => "DATETIME NULL",
        'founding_partner_expires_at' => "DATETIME NULL",
    ] as $col => $def) {
        if (!fssColumnExists($db, 'suppliers', $col)) {
            $db->exec("ALTER TABLE suppliers ADD COLUMN {$col} {$def}");
            echo "suppliers.{$col} added.\n";
        } else {
            echo "suppliers.{$col} already exists. Skipping.\n";
        }
    }

    // --- Global cohort counters - single-row mutex, locked via SELECT...FOR UPDATE
    //     at claim time, same pattern as founding_buyer_program. ---
    if (!fssTableExists($db, 'founding_seller_program')) {
        $db->exec("
            CREATE TABLE founding_seller_program (
                id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
                slots_used INT NOT NULL DEFAULT 0,
                slots_total INT NOT NULL DEFAULT 20,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $db->exec("INSERT INTO founding_seller_program (id, slots_used, slots_total) VALUES (1, 0, 20)");
        echo "founding_seller_program table created and seeded (0/20).\n";
    } else {
        echo "founding_seller_program already exists. Skipping.\n";
    }

    if (!fssTableExists($db, 'founding_supplier_program')) {
        $db->exec("
            CREATE TABLE founding_supplier_program (
                id TINYINT UNSIGNED PRIMARY KEY DEFAULT 1,
                slots_used INT NOT NULL DEFAULT 0,
                slots_total INT NOT NULL DEFAULT 15,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $db->exec("INSERT INTO founding_supplier_program (id, slots_used, slots_total) VALUES (1, 0, 15)");
        echo "founding_supplier_program table created and seeded (0/15).\n";
    } else {
        echo "founding_supplier_program already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
