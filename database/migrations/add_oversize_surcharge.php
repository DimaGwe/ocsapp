<?php
/**
 * Migration: Oversize Surcharge schema (Ecosystem Backend Requirements Sec. 2.1/2.1a)
 *
 * Marché scope only (matches the Sec 2.2 Additional-Stop Fee precedent) - sums
 * declared weight x quantity per shop-order, applies a flat base surcharge once
 * total weight crosses 15kg, plus $X per full 10kg increment beyond 25kg
 * (rounded up), with a 40kg hard cap that blocks standard checkout entirely.
 * Approvisionnement/B2B PO-side thresholds ($10/$11.25/$12.50 base,
 * $5/$5.63/$6.25 increment, 25kg threshold/50kg base-band/100kg cap) not built
 * this round - different code path (PO submission, not CheckoutController).
 *
 * Base surcharge and increment surcharge are stored separately (not just a
 * total) because the source doc requires both amounts itemized separately in
 * the order summary and driver assignment screen, not collapsed into one
 * number (Sec 2.1a).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function osColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function osAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (osColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    osAddColumn($db, 'orders', 'total_weight_kg', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER additional_stop_fee");
    osAddColumn($db, 'orders', 'oversize_base_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER total_weight_kg");
    osAddColumn($db, 'orders', 'oversize_increment_count', "TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER oversize_base_surcharge");
    osAddColumn($db, 'orders', 'oversize_increment_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER oversize_increment_count");

    osAddColumn($db, 'delivery_zones', 'oversize_base_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER stop_fee_rate");
    osAddColumn($db, 'delivery_zones', 'oversize_increment_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER oversize_base_rate");

    osAddColumn($db, 'delivery_earnings', 'oversize_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER additional_stop_fee");

    // Seed Marché oversize rates (Sec 2.1 table) onto the WI/LAV/MTL zone rows, idempotent re-run safe.
    $baseRates = ['WI' => 4.00, 'LAV' => 4.50, 'MTL' => 5.00];
    foreach ($baseRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET oversize_base_rate = ? WHERE code = ? AND oversize_base_rate = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].oversize_base_rate -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }
    $incrementRates = ['WI' => 2.00, 'LAV' => 2.25, 'MTL' => 2.50];
    foreach ($incrementRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET oversize_increment_rate = ? WHERE code = ? AND oversize_increment_rate = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].oversize_increment_rate -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
