<?php
/**
 * Migration: Payment Processing Fee on Distribution shipment quotes
 * (Business Account Agreement Sec. 9.9 correction, confirmed by Jack 2026-08-15):
 * under Distribution the Business Client is the providing party (distributing its
 * own goods to its own receiving customers, structurally the same role a Seller or
 * Supplier plays elsewhere), so it absorbs the 2.9%+$0.30 processing fee, added to
 * the shipment invoice and itemized separately from the Distribution Fee - applies
 * to both the automated (Debutant/Pro) and manual (Enterprise) quote paths.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function adpfColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!adpfColumnExists($db, 'distribution_shipment_quotes', 'processing_fee_amount')) {
        $db->exec("ALTER TABLE distribution_shipment_quotes ADD COLUMN processing_fee_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER rush_surcharge");
        echo "distribution_shipment_quotes.processing_fee_amount added.\n";
    } else {
        echo "distribution_shipment_quotes.processing_fee_amount already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
