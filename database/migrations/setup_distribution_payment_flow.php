<?php
/**
 * Distribution Payment Flow Migration
 *
 * Adds new columns for prepayment workflow:
 * - Approval tracking (approved_at, approved_by)
 * - Payment tracking (paid_at, payment_intent_id, payment_method)
 * - Payment link expiry (payment_link_token, payment_link_expires_at)
 * - Cancellation tracking (cancellation_reason, cancelled_by)
 * - Document generation table
 *
 * New Status Flow: pending → approved → paid → procurement → in_transit → delivered
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';

echo "=== Distribution Payment Flow Migration ===\n\n";

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo "Connected to database.\n\n";

    // 1. Add new columns to distribution_requests
    echo "1. Adding new columns to distribution_requests...\n";

    $columnsToAdd = [
        // Approval tracking
        "approved_at DATETIME NULL AFTER status",
        "approved_by INT NULL AFTER approved_at",

        // Payment tracking
        "paid_at DATETIME NULL AFTER approved_by",
        "payment_intent_id VARCHAR(255) NULL AFTER paid_at",
        "payment_method VARCHAR(100) NULL AFTER payment_intent_id",

        // Payment link
        "payment_link_token VARCHAR(64) NULL AFTER payment_method",
        "payment_link_expires_at DATETIME NULL AFTER payment_link_token",

        // Cancellation tracking
        "cancellation_reason TEXT NULL AFTER payment_link_expires_at",
        "cancelled_by ENUM('customer', 'ocs', 'system') NULL AFTER cancellation_reason",

        // Procurement/delivery tracking
        "procurement_started_at DATETIME NULL AFTER cancelled_by",
        "in_transit_at DATETIME NULL AFTER procurement_started_at",
        "delivered_at DATETIME NULL AFTER in_transit_at",
        "delivery_confirmed_by VARCHAR(255) NULL AFTER delivered_at",
        "delivery_photo_path VARCHAR(500) NULL AFTER delivery_confirmed_by"
    ];

    foreach ($columnsToAdd as $columnDef) {
        $columnName = explode(' ', $columnDef)[0];

        // Check if column exists
        $check = $db->query("SHOW COLUMNS FROM distribution_requests LIKE '$columnName'");
        if ($check->rowCount() === 0) {
            $db->exec("ALTER TABLE distribution_requests ADD COLUMN $columnDef");
            echo "   Added column: $columnName\n";
        } else {
            echo "   Column exists: $columnName (skipped)\n";
        }
    }

    // 2. Update status enum to include new statuses
    echo "\n2. Updating status enum...\n";

    // First, update any 'submitted' to 'pending' for consistency
    $db->exec("UPDATE distribution_requests SET status = 'pending' WHERE status = 'submitted'");
    $db->exec("UPDATE distribution_requests SET status = 'approved' WHERE status = 'quoted'");
    $db->exec("UPDATE distribution_requests SET status = 'approved' WHERE status = 'pending_payment'");
    $db->exec("UPDATE distribution_requests SET status = 'procurement' WHERE status = 'processing'");
    $db->exec("UPDATE distribution_requests SET status = 'delivered' WHERE status = 'completed'");
    $db->exec("UPDATE distribution_requests SET status = 'delivered' WHERE status = 'ready'");
    echo "   Migrated old statuses to new values\n";

    // Alter the enum
    $db->exec("ALTER TABLE distribution_requests MODIFY COLUMN status ENUM('draft', 'pending', 'approved', 'paid', 'procurement', 'in_transit', 'delivered', 'cancelled', 'expired') DEFAULT 'draft'");
    echo "   Updated status enum\n";

    // 3. Create distribution_documents table
    echo "\n3. Creating distribution_documents table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_documents (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            type ENUM('invoice', 'purchase_order', 'sales_order', 'procurement_list') NOT NULL,
            document_number VARCHAR(50) NOT NULL,
            file_path VARCHAR(500) NULL,
            file_name VARCHAR(255) NULL,
            generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,

            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE,
            UNIQUE KEY unique_doc_number (type, document_number),
            INDEX idx_request (distribution_request_id),
            INDEX idx_type (type)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "   Created distribution_documents table\n";

    // 4. Create document number sequences table
    echo "\n4. Creating document_sequences table...\n";

    $db->exec("
        CREATE TABLE IF NOT EXISTS document_sequences (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            year INT NOT NULL,
            last_number INT DEFAULT 0,

            UNIQUE KEY unique_type_year (type, year)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // Initialize sequences for current year
    $currentYear = date('Y');
    $types = ['invoice', 'purchase_order', 'sales_order'];
    foreach ($types as $type) {
        $db->exec("INSERT IGNORE INTO document_sequences (type, year, last_number) VALUES ('$type', $currentYear, 0)");
    }
    echo "   Created document_sequences table and initialized for $currentYear\n";

    // 5. Add index for payment_link_token
    echo "\n5. Adding indexes...\n";

    // Check if index exists before adding
    $indexCheck = $db->query("SHOW INDEX FROM distribution_requests WHERE Key_name = 'idx_payment_token'");
    if ($indexCheck->rowCount() === 0) {
        $db->exec("CREATE INDEX idx_payment_token ON distribution_requests(payment_link_token)");
        echo "   Added index: idx_payment_token\n";
    } else {
        echo "   Index exists: idx_payment_token (skipped)\n";
    }

    $indexCheck = $db->query("SHOW INDEX FROM distribution_requests WHERE Key_name = 'idx_status_created'");
    if ($indexCheck->rowCount() === 0) {
        $db->exec("CREATE INDEX idx_status_created ON distribution_requests(status, created_at)");
        echo "   Added index: idx_status_created\n";
    } else {
        echo "   Index exists: idx_status_created (skipped)\n";
    }

    // 6. Update status history table to include new statuses
    echo "\n6. Updating distribution_status_history...\n";

    // Update old status names in history
    $db->exec("UPDATE distribution_status_history SET from_status = 'pending' WHERE from_status = 'submitted'");
    $db->exec("UPDATE distribution_status_history SET to_status = 'pending' WHERE to_status = 'submitted'");
    $db->exec("UPDATE distribution_status_history SET from_status = 'approved' WHERE from_status IN ('quoted', 'pending_payment')");
    $db->exec("UPDATE distribution_status_history SET to_status = 'approved' WHERE to_status IN ('quoted', 'pending_payment')");
    $db->exec("UPDATE distribution_status_history SET from_status = 'procurement' WHERE from_status = 'processing'");
    $db->exec("UPDATE distribution_status_history SET to_status = 'procurement' WHERE to_status = 'processing'");
    $db->exec("UPDATE distribution_status_history SET from_status = 'delivered' WHERE from_status IN ('completed', 'ready')");
    $db->exec("UPDATE distribution_status_history SET to_status = 'delivered' WHERE to_status IN ('completed', 'ready')");
    echo "   Updated status history records\n";

    echo "\n=== Migration Complete ===\n\n";

    echo "Summary of changes:\n";
    echo "- Added approval tracking columns (approved_at, approved_by)\n";
    echo "- Added payment tracking columns (paid_at, payment_intent_id, payment_method)\n";
    echo "- Added payment link columns (payment_link_token, payment_link_expires_at)\n";
    echo "- Added cancellation columns (cancellation_reason, cancelled_by)\n";
    echo "- Added delivery tracking columns (procurement_started_at, in_transit_at, delivered_at, etc.)\n";
    echo "- Updated status enum: draft → pending → approved → paid → procurement → in_transit → delivered\n";
    echo "- Created distribution_documents table\n";
    echo "- Created document_sequences table for INV/PO/SO numbering\n";
    echo "\nNew Status Flow:\n";
    echo "1. PENDING - Customer submitted, awaiting OCS review\n";
    echo "2. APPROVED - OCS confirmed, awaiting payment\n";
    echo "3. PAID - Payment received, ready for procurement\n";
    echo "4. PROCUREMENT - OCS purchasing from suppliers\n";
    echo "5. IN_TRANSIT - Items collected, en route\n";
    echo "6. DELIVERED - Complete\n";
    echo "\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
