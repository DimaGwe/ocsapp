<?php
/**
 * Migration: Distribution Fee Automation (Business Account Agreement Section 8,
 * Schedule B). Jack's direction (2026-08-14): the Débutant/Pro Distribution Fee
 * (5%/7% of declared shipment value) plus the flat B2B zone delivery fee, the
 * Oversize Surcharge (Sec. 8.10, >25kg) and the Long-Distance Surcharge
 * (Sec. 8.10a, >10km routed) must be automated for Débutant/Pro. Enterprise
 * stays custom-quoted per Section 8.5, unaffected by this migration.
 *
 * The B2B surcharge rates ($10/$11.25/$12.50 base, $5/$5.63/$6.25 increment)
 * are identical between Approvisionnement (Sec 7.5/7.5a) and Distribution
 * (Sec 8.10/8.10a) in the contract, so these delivery_zones columns are named
 * generically (b2b_*) rather than distribution-specific - reusable later for
 * Approvisionnement's still-unbuilt 7.4/7.5/7.5a surcharges without a second
 * migration, not scope creep for this round (Distribution only, per Jack's
 * explicit instruction).
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function dfaColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function dfaAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (dfaColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    // delivery_zones: shared B2B rate set (Approvisionnement Sec 7.5/7.5a today
    // reuses the same numbers - Distribution Sec 8.10/8.10a is what actually
    // consumes these this round).
    dfaAddColumn($db, 'delivery_zones', 'b2b_base_fee', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER long_distance_increment_rate");
    dfaAddColumn($db, 'delivery_zones', 'b2b_oversize_base_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER b2b_base_fee");
    dfaAddColumn($db, 'delivery_zones', 'b2b_oversize_increment_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER b2b_oversize_base_rate");
    dfaAddColumn($db, 'delivery_zones', 'b2b_long_distance_base_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER b2b_oversize_increment_rate");
    dfaAddColumn($db, 'delivery_zones', 'b2b_long_distance_increment_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER b2b_long_distance_base_rate");
    dfaAddColumn($db, 'delivery_zones', 'b2b_stop_fee_rate', "DECIMAL(6,2) NOT NULL DEFAULT 0.00 AFTER b2b_long_distance_increment_rate");

    // Seed from Business Account Agreement Schedule A/B (idempotent - only fills zero rows).
    $rates = [
        // code => [delivery, oversize_base, oversize_incr, ld_base, ld_incr, stop]
        'WI'  => [19.00, 10.00, 5.00, 10.00, 5.00, 4.00],
        'LAV' => [21.00, 11.25, 5.63, 11.25, 5.63, 4.50],
        'MTL' => [24.00, 12.50, 6.25, 12.50, 6.25, 5.00],
    ];
    $cols = ['b2b_base_fee', 'b2b_oversize_base_rate', 'b2b_oversize_increment_rate', 'b2b_long_distance_base_rate', 'b2b_long_distance_increment_rate', 'b2b_stop_fee_rate'];
    foreach ($rates as $code => $vals) {
        foreach ($cols as $i => $col) {
            $upd = $db->prepare("UPDATE delivery_zones SET {$col} = ? WHERE code = ? AND {$col} = 0.00");
            $upd->execute([$vals[$i], $code]);
            echo "delivery_zones[{$code}].{$col} -> {$vals[$i]} ({$upd->rowCount()} row(s)).\n";
        }
    }

    // distribution_shipments: declared value (the Distribution Fee base - businesses
    // ship their own goods, so there's no existing catalog price to derive this from,
    // unlike Approvisionnement's supplier-priced items), geocoded coordinates + routed
    // distance (lazy-geocode-then-cache, same pattern as Sec 2.1b), resolved zone.
    dfaAddColumn($db, 'distribution_shipments', 'declared_value', "DECIMAL(12,2) NULL AFTER package_description");
    dfaAddColumn($db, 'distribution_shipments', 'pickup_latitude', "DECIMAL(10,8) NULL AFTER pickup_postal_code");
    dfaAddColumn($db, 'distribution_shipments', 'pickup_longitude', "DECIMAL(11,8) NULL AFTER pickup_latitude");
    dfaAddColumn($db, 'distribution_shipments', 'destination_latitude', "DECIMAL(10,8) NULL AFTER destination_postal_code");
    dfaAddColumn($db, 'distribution_shipments', 'destination_longitude', "DECIMAL(11,8) NULL AFTER destination_latitude");
    dfaAddColumn($db, 'distribution_shipments', 'routed_distance_km', "DECIMAL(6,2) NULL AFTER declared_value");
    dfaAddColumn($db, 'distribution_shipments', 'zone_code', "VARCHAR(10) NULL AFTER routed_distance_km");

    // distribution_shipment_quotes: the Distribution Fee itself (% of declared value)
    // has no existing column - base_rate historically meant a flat admin-typed
    // starting number. is_automated distinguishes a Sec 8.10/8.10a formula-generated
    // quote (Débutant/Pro) from a manually-typed Enterprise quote (Sec 8.5, unaffected).
    dfaAddColumn($db, 'distribution_shipment_quotes', 'declared_value', "DECIMAL(12,2) NULL AFTER shipment_id");
    dfaAddColumn($db, 'distribution_shipment_quotes', 'distribution_fee_amount', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER declared_value");
    dfaAddColumn($db, 'distribution_shipment_quotes', 'is_automated', "TINYINT(1) NOT NULL DEFAULT 0 AFTER notes");

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
