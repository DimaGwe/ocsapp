<?php
/**
 * Migration: Create supplier payment tables
 * - supplier_invoices: Invoice records linked to purchase orders
 * - supplier_payments: Actual payment records
 * - supplier_invoice_payments: Links payments to invoices (one payment can cover multiple invoices)
 */
require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    echo "Setting up supplier payment tables...\n\n";

    // ─── 1. SUPPLIER INVOICES ───
    $check = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'supplier_invoices'");
    if ($check->fetchColumn() == 0) {
        $db->exec("
            CREATE TABLE supplier_invoices (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                invoice_number VARCHAR(50) UNIQUE NOT NULL,
                supplier_id BIGINT UNSIGNED NOT NULL,
                po_id BIGINT UNSIGNED DEFAULT NULL,

                -- Financial
                subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                tax_gst DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                tax_qst DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                shipping DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                amount_paid DECIMAL(10,2) NOT NULL DEFAULT 0.00,
                balance_due DECIMAL(10,2) NOT NULL DEFAULT 0.00,

                -- Status & Dates
                status ENUM('draft','sent','paid','partial','overdue','cancelled') DEFAULT 'draft',
                issue_date DATE NOT NULL,
                due_date DATE NOT NULL,
                paid_at DATETIME DEFAULT NULL,

                -- Metadata
                notes TEXT DEFAULT NULL,
                created_by BIGINT UNSIGNED DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                INDEX idx_supplier_id (supplier_id),
                INDEX idx_po_id (po_id),
                INDEX idx_status (status),
                INDEX idx_due_date (due_date),
                INDEX idx_issue_date (issue_date),
                INDEX idx_invoice_number (invoice_number)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created 'supplier_invoices' table.\n";
    } else {
        echo "'supplier_invoices' table already exists.\n";
    }

    // ─── 2. SUPPLIER PAYMENTS ───
    $check = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'supplier_payments'");
    if ($check->fetchColumn() == 0) {
        $db->exec("
            CREATE TABLE supplier_payments (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                payment_number VARCHAR(50) UNIQUE NOT NULL,
                supplier_id BIGINT UNSIGNED NOT NULL,

                amount DECIMAL(10,2) NOT NULL,
                payment_method ENUM('interac','bank_transfer','cheque','other') NOT NULL,
                reference_number VARCHAR(255) DEFAULT NULL,
                payment_date DATE NOT NULL,

                notes TEXT DEFAULT NULL,
                created_by BIGINT UNSIGNED DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

                INDEX idx_supplier_id (supplier_id),
                INDEX idx_payment_date (payment_date),
                INDEX idx_payment_method (payment_method)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created 'supplier_payments' table.\n";
    } else {
        echo "'supplier_payments' table already exists.\n";
    }

    // ─── 3. INVOICE-PAYMENT JUNCTION ───
    $check = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'supplier_invoice_payments'");
    if ($check->fetchColumn() == 0) {
        $db->exec("
            CREATE TABLE supplier_invoice_payments (
                id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                invoice_id BIGINT UNSIGNED NOT NULL,
                payment_id BIGINT UNSIGNED NOT NULL,
                amount_applied DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

                INDEX idx_invoice_id (invoice_id),
                INDEX idx_payment_id (payment_id),
                UNIQUE KEY uk_invoice_payment (invoice_id, payment_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "Created 'supplier_invoice_payments' table.\n";
    } else {
        echo "'supplier_invoice_payments' table already exists.\n";
    }

    echo "\nMigration complete.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
