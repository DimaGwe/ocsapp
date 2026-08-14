<?php
/**
 * Migration: Payment Processing Fee on seller payouts (Ecosystem Backend
 * Requirements Sec. 7 / Pricing Strategy Sec. 9.1)
 *
 * Seller Central's marketing copy promises the 2.9%+$0.30 Stripe/PayPal
 * processing fee is "absorbed by the seller and deducted from net proceeds
 * before payout, alongside commission" - confirmed by audit that nothing in
 * the codebase actually computed or stored this. Pricing Strategy Sec 9.1
 * settles the fee basis: same base as commission (subtotal, not delivery
 * fee/tax), deducted the same way, same moment.
 *
 * Scoped to sellers only this round - Sec 9.1 extends the same treatment to
 * suppliers/distribution, but those flow through a structurally different
 * accounts-payable model (OCS buying, not taking a cut) with no commission
 * concept today; extending there is a separate, comparably-sized build.
 *
 * No backfill: seller_payouts only has synthetic/test rows from this week's
 * Sec 4 chargeback-netting work, nothing real to recompute.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function spfColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

try {
    $db = Database::getConnection();

    if (!spfColumnExists($db, 'seller_payouts', 'processing_fee_amount')) {
        $db->exec("ALTER TABLE seller_payouts ADD COLUMN processing_fee_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER commission_amount");
        echo "seller_payouts.processing_fee_amount added.\n";
    } else {
        echo "seller_payouts.processing_fee_amount already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
