<?php
/**
 * Migration: Mandatory pickup photo + delivery signature columns
 * (Ecosystem Backend Requirements Sec. 4.1)
 *
 * "Mandatory photo capture at pickup (dispatch)... required on every Order,
 * not just returns" (Driver Agreement Sec. 7.7) - pickup-side photo capture
 * does not exist at all today for buyer (Marché) orders; delivery-side
 * capture exists (delivery_assignments.proof_of_delivery) but is completely
 * optional - a driver can mark an order delivered with zero photos. This
 * migration adds the missing pickup column; enforcement itself is in
 * DriverApiController (updateOrderStatus/orderOutcome), not the DB layer.
 *
 * Scope note: this covers B2C Marché orders (delivery_assignments) only.
 * B2B Distribution has two parallel delivery code paths
 * (distribution_requests via completeDistributionDelivery(), and a separate
 * distribution_shipments/distribution_shipment_destinations multi-drop
 * system that already has its own signature_collected/photo_proof_path
 * columns) - which one is actually live wasn't fully resolved this round,
 * so B2B enforcement is flagged as a follow-up, not built here.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function ppColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function ppAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (ppColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    ppAddColumn($db, 'delivery_assignments', 'pickup_photo_path', "VARCHAR(255) NULL AFTER proof_of_delivery");
    ppAddColumn($db, 'delivery_assignments', 'signature_collected', "TINYINT(1) NOT NULL DEFAULT 0 AFTER pickup_photo_path");

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
