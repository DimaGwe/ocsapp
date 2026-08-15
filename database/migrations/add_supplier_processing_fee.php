<?php
/**
 * Migration: Payment Processing Fee deduction on supplier invoices
 * (Business Account Agreement Sec. 9.9 correction / Supplier Account Agreement
 * Sec. 9.2, confirmed by Jack 2026-08-15: the fee is absorbed by the providing
 * side of a transaction, never the receiving side - under Approvisionnement the
 * Business Client receives goods, so the Supplier absorbs the processing fee,
 * same base as the seller-side processing fee (Sec. 7) - 2.9% + $0.30 on goods
 * subtotal, deducted from the supplier's net payout alongside the wholesale
 * commission added in add_supplier_commission_deduction.php.
 *
 * No backfill: existing invoices keep their original math (net_payable already
 * reflects only the commission deduction) - only invoices generated after this
 * migration runs apply the processing fee too.
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

    if (!spfColumnExists($db, 'supplier_invoices', 'processing_fee_amount')) {
        $db->exec("ALTER TABLE supplier_invoices ADD COLUMN processing_fee_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER commission_amount");
        echo "supplier_invoices.processing_fee_amount added.\n";
    } else {
        echo "supplier_invoices.processing_fee_amount already exists. Skipping.\n";
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
