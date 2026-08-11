<?php
/**
 * Migration: Enforce product weight requirements (Backend Requirements Sec. 1)
 *
 * - products.weight: convert legacy 0.00 values to NULL (0 = "unknown", not a real weight),
 *   drop the 0.00 default, and add a DB-level CHECK rejecting zero/negative values.
 * - supplier_products.weight_kg: same CHECK constraint.
 * - supplier_products.dimensions: optional L x W x H (cm) field for future dimensional-weight
 *   calculations, mirroring the existing products.dimensions column.
 *
 * NULL remains allowed at the DB level so legacy rows stay readable — the backfill path is
 * app-level enforcement: every save/update path now requires a positive weight, so each
 * listing gets a real weight the next time it is edited.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function constraintExists(PDO $db, string $table, string $constraint): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.table_constraints
        WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ?
    ");
    $stmt->execute([$table, $constraint]);
    return $stmt->fetchColumn() > 0;
}

function columnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    // --- products.weight ---
    $updated = $db->exec("UPDATE products SET weight = NULL WHERE weight <= 0");
    echo "products: {$updated} legacy zero-weight rows set to NULL.\n";

    $db->exec("ALTER TABLE products MODIFY COLUMN weight DECIMAL(8,2) NULL DEFAULT NULL");
    echo "products.weight default changed to NULL.\n";

    if (!constraintExists($db, 'products', 'chk_products_weight_positive')) {
        $db->exec("
            ALTER TABLE products
            ADD CONSTRAINT chk_products_weight_positive CHECK (weight IS NULL OR weight > 0)
        ");
        echo "products: CHECK constraint added.\n";
    } else {
        echo "products: CHECK constraint already exists. Skipping.\n";
    }

    // --- supplier_products.weight_kg ---
    $updated = $db->exec("UPDATE supplier_products SET weight_kg = NULL WHERE weight_kg <= 0");
    echo "supplier_products: {$updated} legacy zero-weight rows set to NULL.\n";

    if (!constraintExists($db, 'supplier_products', 'chk_supplier_products_weight_positive')) {
        $db->exec("
            ALTER TABLE supplier_products
            ADD CONSTRAINT chk_supplier_products_weight_positive CHECK (weight_kg IS NULL OR weight_kg > 0)
        ");
        echo "supplier_products: CHECK constraint added.\n";
    } else {
        echo "supplier_products: CHECK constraint already exists. Skipping.\n";
    }

    // --- supplier_products.dimensions ---
    if (!columnExists($db, 'supplier_products', 'dimensions')) {
        $db->exec("
            ALTER TABLE supplier_products
            ADD COLUMN dimensions VARCHAR(100) NULL DEFAULT NULL AFTER weight_kg
        ");
        echo "supplier_products.dimensions column added.\n";
    } else {
        echo "supplier_products.dimensions already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
