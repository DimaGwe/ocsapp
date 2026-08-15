<?php
/**
 * Migration: Exchange-First Replacement (Returns Policy Sec A5, Ecosystem
 * Backend Requirements contract-reconciliation finding #5 part 2)
 *
 * Track A only this round (Sec A5, Marketplace/B2C). Track B's B5
 * "Replacement (default)" is a separate, comparably-sized build against a
 * different order model (distribution_requests/supplier_products, not
 * orders/order_items) - flagged, not built here, same discipline as every
 * other correctly-scoped gap in this program.
 *
 * order_claims.replacement_order_id links a resolved exchange claim to the
 * new no-charge replacement order created for it, so admin/buyer views can
 * show what was actually shipped instead of a refund.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function erColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!erColumnExists($db, 'order_claims', 'replacement_order_id')) {
        $db->exec("ALTER TABLE order_claims ADD COLUMN replacement_order_id BIGINT UNSIGNED NULL AFTER resolution");
        $db->exec("ALTER TABLE order_claims ADD INDEX idx_replacement_order (replacement_order_id)");
        echo "order_claims.replacement_order_id added.\n";
    } else {
        echo "order_claims.replacement_order_id already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
