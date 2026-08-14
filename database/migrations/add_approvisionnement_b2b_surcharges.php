<?php
/**
 * Migration: Oversize/Long-Distance/Additional-Stop surcharges for Approvisionnement
 * (Business Account Agreement Sec. 7.4-7.8 / Schedule A).
 *
 * These three surcharges were built for Distribution shipments the same day
 * (DistributionFeeHelper, commit c5eb6f9) but never wired into Approvisionnement's
 * own checkout math in DistributionRequestController - confirmed by the Business
 * Account Agreement's own Section 7.4-7.8 promising the same surcharges apply to
 * Approvisionnement, and by grep showing zero calls to the B2B surcharge helper
 * functions anywhere in DistributionRequestController. Reuses the existing shared
 * b2b_* rate columns on delivery_zones and the calculateB2BOversizeSurcharge()/
 * calculateB2BLongDistanceSurcharge()/calculateB2BAdditionalStopFee() helpers -
 * no new rate data needed, just new columns to persist the result on
 * distribution_requests. Column names match the simplified aggregate-total
 * convention already used on distribution_shipment_quotes (weight_surcharge/
 * distance_surcharge), not the detailed base+increment split used internally by
 * DistributionFeeHelper's return array.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function abscColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function abscAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (abscColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    abscAddColumn($db, 'distribution_requests', 'zone_code', "VARCHAR(10) NULL AFTER delivery_distance");
    abscAddColumn($db, 'distribution_requests', 'stop_count', "TINYINT UNSIGNED NOT NULL DEFAULT 1 AFTER zone_code");
    abscAddColumn($db, 'distribution_requests', 'oversize_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER total_weight_kg");
    abscAddColumn($db, 'distribution_requests', 'long_distance_surcharge', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER oversize_surcharge");
    abscAddColumn($db, 'distribution_requests', 'additional_stop_fee', "DECIMAL(8,2) NOT NULL DEFAULT 0.00 AFTER long_distance_surcharge");

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
