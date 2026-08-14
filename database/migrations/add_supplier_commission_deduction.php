<?php
/**
 * Migration: Wholesale Commission deduction on supplier invoices (Ecosystem
 * Backend Requirements Sec. 7 follow-up / Pricing Strategy Sec. 5.2)
 *
 * Audit found suppliers.commission_rate (8%/6%/5% by package tier) is set at
 * signup and shown on the supplier dashboard/settings as "your commission
 * rate", but nothing in the codebase ever deducted it - every purchase_order
 * traces back to a distribution_request_id (Approvisionnement), where OCS is
 * the buyer and AdminPayablesController::createInvoiceForPO() pays the
 * supplier's invoice in full (subtotal + shipping + tax, no commission line).
 * The rate was cosmetic. This migration adds the columns needed to actually
 * apply it: a per-invoice snapshot of the rate (so a later plan change never
 * rewrites history), the computed commission amount, and net_payable - the
 * amount OCS actually owes the supplier after commission, which becomes the
 * new balance_due basis instead of the gross total_amount.
 *
 * Commission is computed on subtotal (goods value) only, not shipping/tax -
 * same base already used for the seller-side commission/processing fee.
 *
 * Backfill: net_payable = total_amount for every existing invoice (commission
 * columns default to 0). This is deliberate, not a placeholder - invoices
 * already sent/paid under the old no-commission math keep that math; only
 * invoices generated after this migration runs apply the deduction.
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

function scdColumnExists(PDO $db, string $table, string $column): bool {
    $stmt = $db->prepare("
        SELECT COUNT(*) FROM information_schema.columns
        WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?
    ");
    $stmt->execute([$table, $column]);
    return $stmt->fetchColumn() > 0;
}

function scdAddColumn(PDO $db, string $table, string $column, string $definition): void {
    if (scdColumnExists($db, $table, $column)) {
        echo "{$table}.{$column} already exists. Skipping.\n";
        return;
    }
    $db->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    echo "{$table}.{$column} added.\n";
}

try {
    $db = Database::getConnection();

    scdAddColumn($db, 'supplier_invoices', 'commission_rate', "DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER tax_qst");
    scdAddColumn($db, 'supplier_invoices', 'commission_amount', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER commission_rate");
    scdAddColumn($db, 'supplier_invoices', 'net_payable', "DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER total_amount");

    // Backfill existing invoices: net_payable = total_amount (no retroactive commission)
    $upd = $db->exec("UPDATE supplier_invoices SET net_payable = total_amount WHERE net_payable = 0.00");
    echo "Backfilled net_payable on existing invoices ({$upd} row(s)).\n";

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
