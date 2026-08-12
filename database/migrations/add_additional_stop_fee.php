<?php
/**
 * Migration: Additional-Stop Fee schema (Ecosystem Backend Requirements Sec. 2.2)
 *
 * Marché multi-vendor checkout already creates one `orders` row per shop
 * (CheckoutController::process()), each independently billed the full zone
 * delivery fee - there is no "single consolidated order with multiple stops"
 * concept on the B2C side (that only exists for B2B Distribution shipments).
 * Rather than rebuild checkout/dispatch around a consolidated-order model,
 * this adds a surcharge on top of the existing per-shop orders:
 *
 *   - checkout_session_id correlates the sibling orders created together in
 *     one checkout, so we can count "distinct pickup locations" per the doc.
 *   - additional_stop_fee / stop_count are written per order (the total
 *     session-level stop fee split evenly across the sibling orders).
 *   - delivery_zones.stop_fee_rate is the admin-editable per-stop rate
 *     (Marché scope only for this slice), seeded from the source doc.
 *   - delivery_earnings.additional_stop_fee itemizes the driver's payout
 *     share separately, per Sec. 3.3 (no blended totals).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function columnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function addColumn(PDO $db, string $table, string $column, string $definition): void {
    if (columnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    addColumn($db, 'orders', 'checkout_session_id', "VARCHAR(36) NULL DEFAULT NULL AFTER order_number");
    addColumn($db, 'orders', 'stop_count', "TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER delivery_fee");
    addColumn($db, 'orders', 'additional_stop_fee', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER stop_count");

    addColumn($db, 'delivery_zones', 'stop_fee_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER per_km_fee");

    addColumn($db, 'delivery_earnings', 'additional_stop_fee', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER base_fee");

    // Index for the sibling-order lookup used at checkout confirmation time.
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.statistics
        WHERE table_schema = DATABASE() AND table_name = 'orders' AND index_name = 'idx_checkout_session_id'
    ");
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        echo "orders.idx_checkout_session_id already exists. Skipping.\n";
    } else {
        $db->exec("ALTER TABLE orders ADD INDEX idx_checkout_session_id (checkout_session_id)");
        echo "orders.idx_checkout_session_id added.\n";
    }

    // Seed Marché stop-fee rates (Sec 2.2 table) onto the existing WI/LAV/MTL zone rows,
    // only where the rate hasn't already been set (idempotent re-run safe).
    $rates = ['WI' => 2.00, 'LAV' => 2.25, 'MTL' => 2.50];
    foreach ($rates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET stop_fee_rate = ? WHERE code = ? AND stop_fee_rate = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].stop_fee_rate -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
