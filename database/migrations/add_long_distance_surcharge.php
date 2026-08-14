<?php
/**
 * Migration: Long-Distance Surcharge schema (Ecosystem Backend Requirements Sec. 2.1b)
 *
 * Marché scope only (matches the Sec 2.1/2.1a/2.2 precedent) - computes routed
 * pickup-to-delivery distance per shop-order, applies a flat base surcharge once
 * distance crosses the 8-12km base band, plus $X per full 4km increment beyond
 * 12km (rounded up), with a 20km hard cap that blocks standard checkout entirely.
 * Fournisseur/B2B thresholds (10-15km base band, 5km increment, 30km cap) not
 * built this round - different code path (PO submission, not CheckoutController).
 *
 * addresses.latitude/longitude are new - the buyer address table has no
 * coordinates today, unlike shops (which already have both). Both are lazily
 * geocoded and cached on first use rather than backfilled in bulk here.
 *
 * orders.routed_distance_km is deliberately a new, distinctly-named column -
 * NOT the same as the existing undocumented orders.distance_km (no migration
 * ever created that one; unclear what process populates it, likely a
 * GPS/display concept unrelated to this pricing input).
 *
 * Base surcharge and increment surcharge are stored separately (not just a
 * total), same itemization rule as the Oversize Surcharge (Sec 2.1a).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function ldColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function ldAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (ldColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    ldAddColumn($db, 'addresses', 'latitude', "DECIMAL(10,8) NULL DEFAULT NULL AFTER postal_code");
    ldAddColumn($db, 'addresses', 'longitude', "DECIMAL(11,8) NULL DEFAULT NULL AFTER latitude");

    ldAddColumn($db, 'orders', 'routed_distance_km', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER oversize_increment_surcharge");
    ldAddColumn($db, 'orders', 'long_distance_base_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER routed_distance_km");
    ldAddColumn($db, 'orders', 'long_distance_increment_count', "TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER long_distance_base_surcharge");
    ldAddColumn($db, 'orders', 'long_distance_increment_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER long_distance_increment_count");

    ldAddColumn($db, 'delivery_zones', 'long_distance_base_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER oversize_increment_rate");
    ldAddColumn($db, 'delivery_zones', 'long_distance_increment_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER long_distance_base_rate");

    ldAddColumn($db, 'delivery_earnings', 'long_distance_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER oversize_surcharge");

    // Seed Marché long-distance rates (Sec 2.1b / Pricing Strategy Sec 8.4e) onto the
    // WI/LAV/MTL zone rows, idempotent re-run safe.
    $baseRates = ['WI' => 4.00, 'LAV' => 4.50, 'MTL' => 5.00];
    foreach ($baseRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET long_distance_base_rate = ? WHERE code = ? AND long_distance_base_rate = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].long_distance_base_rate -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }
    $incrementRates = ['WI' => 2.00, 'LAV' => 2.25, 'MTL' => 2.50];
    foreach ($incrementRates as $code => $rate) {
        $upd = $db->prepare("UPDATE delivery_zones SET long_distance_increment_rate = ? WHERE code = ? AND long_distance_increment_rate = 0.00");
        $upd->execute([$rate, $code]);
        echo "delivery_zones[{$code}].long_distance_increment_rate -> {$rate} ({$upd->rowCount()} row(s)).\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
