<?php
/**
 * Migration: Pickup Weight Discrepancy schema (Ecosystem Backend Requirements Sec. 3,
 * "Weight/stop-count discrepancy flag at pickup").
 *
 * Reuses the mandatory-pickup-photo pattern already built for Sec 4
 * (DriverApiController::orderPickupPhoto / delivery_assignments.pickup_photo_path)
 * rather than inventing new evidence-capture infra. Stop-count is not included
 * here - per Pricing Strategy Sec 8.4d, stop count is exactly known at checkout
 * (it equals the number of sellers consolidated into the order), so there is no
 * discrepancy case for it the way there is for seller-declared weight.
 *
 * This is a flag-and-notify mechanism only, matching the Dynamic Chargeback and
 * "Stalled (2h+)" precedents already in this codebase: it surfaces the mismatch
 * with photo evidence for admin review, it does not automatically recalculate
 * or rebill the oversize surcharge. Per Pricing Strategy Sec 8.4c's "Who
 * Determines Weight" section, a confirmed discrepancy can trigger the surcharge
 * retroactively - that remains an admin decision, not an automatic one.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function wdTableExists(PDO $db, string $table): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = ?
    ");
    $stmt->execute([$table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (wdTableExists($db, 'order_weight_discrepancies')) {
        echo "order_weight_discrepancies already exists. Skipping.\n";
    } else {
        $db->exec("
            CREATE TABLE order_weight_discrepancies (
                id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                order_id BIGINT UNSIGNED NOT NULL,
                delivery_id BIGINT UNSIGNED NULL,
                driver_id BIGINT UNSIGNED NOT NULL,
                declared_weight_kg DECIMAL(10,2) NOT NULL COMMENT 'orders.total_weight_kg at time of report',
                reported_weight_kg DECIMAL(10,2) NOT NULL COMMENT 'What the driver actually weighed/found at pickup',
                photo_path VARCHAR(255) NOT NULL,
                status ENUM('pending','confirmed','dismissed') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,
                resolved_by BIGINT UNSIGNED NULL,
                resolved_at TIMESTAMP NULL DEFAULT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_order_id (order_id),
                KEY idx_delivery_id (delivery_id),
                KEY idx_driver_id (driver_id),
                KEY idx_status (status),
                CONSTRAINT fk_wd_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
                CONSTRAINT fk_wd_driver FOREIGN KEY (driver_id) REFERENCES users (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
              COMMENT='Driver-flagged pickup weight discrepancies (Ecosystem Backend Requirements Sec. 3)'
        ");
        echo "order_weight_discrepancies created.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
