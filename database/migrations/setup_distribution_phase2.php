<?php
/**
 * Distribution Portal Phase 2 - Database Migration
 * Creates tables for procurement requests, invoices, recurring orders
 *
 * Run: php database/migrations/setup_distribution_phase2.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "Starting Distribution Phase 2 Migration...\n\n";

try {
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $db->beginTransaction();

    // 1. Create distribution_requests table
    echo "Creating distribution_requests table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_requests (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_profile_id INT NOT NULL,
            request_number VARCHAR(50) NOT NULL UNIQUE,
            request_type ENUM('catalog', 'shopping_list', 'mixed') DEFAULT 'catalog',

            -- Status workflow
            status ENUM('draft','submitted','quoted','pending_payment','paid','processing','ready','completed','cancelled') DEFAULT 'draft',

            -- Financial
            subtotal DECIMAL(12,2) DEFAULT 0.00,
            tax_amount DECIMAL(12,2) DEFAULT 0.00,
            delivery_fee DECIMAL(12,2) DEFAULT 0.00,
            discount_amount DECIMAL(12,2) DEFAULT 0.00,
            total_amount DECIMAL(12,2) DEFAULT 0.00,

            -- Payment
            payment_method ENUM('stripe', 'bank_transfer') NULL,
            payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
            payment_reference VARCHAR(255) NULL,
            paid_at DATETIME NULL,

            -- Delivery Address
            delivery_street VARCHAR(255) NOT NULL,
            delivery_city VARCHAR(100) NOT NULL,
            delivery_province VARCHAR(50) NOT NULL,
            delivery_postal_code VARCHAR(20) NOT NULL,
            delivery_country VARCHAR(50) DEFAULT 'Canada',
            delivery_instructions TEXT NULL,
            requested_delivery_date DATE NULL,

            -- Recurring reference
            recurring_order_id INT NULL,
            is_recurring_instance TINYINT(1) DEFAULT 0,

            -- Notes
            business_notes TEXT NULL,
            admin_notes TEXT NULL,

            -- Timestamps
            submitted_at DATETIME NULL,
            quoted_at DATETIME NULL,
            completed_at DATETIME NULL,
            cancelled_at DATETIME NULL,
            cancelled_reason TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_request_number (request_number),
            INDEX idx_payment_status (payment_status),
            INDEX idx_recurring (recurring_order_id),
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_requests created\n";

    // 2. Create distribution_request_items table (catalog items)
    echo "Creating distribution_request_items table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_request_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            product_id INT NULL,
            product_name VARCHAR(255) NOT NULL,
            product_sku VARCHAR(100) NULL,
            product_image VARCHAR(500) NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) DEFAULT 0.00,
            subtotal DECIMAL(12,2) DEFAULT 0.00,
            notes TEXT NULL,
            status ENUM('pending', 'available', 'unavailable', 'substituted', 'fulfilled') DEFAULT 'pending',
            substitution_notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_request (distribution_request_id),
            INDEX idx_product (product_id),
            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_request_items created\n";

    // 3. Create distribution_shopping_items table (free-form items)
    echo "Creating distribution_shopping_items table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shopping_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            item_description TEXT NOT NULL,
            preferred_brand VARCHAR(255) NULL,
            preferred_store VARCHAR(255) NULL,
            quantity VARCHAR(100) NOT NULL,
            estimated_price DECIMAL(10,2) NULL,
            fulfilled_product_name VARCHAR(255) NULL,
            fulfilled_quantity INT NULL,
            unit_price DECIMAL(10,2) NULL,
            subtotal DECIMAL(12,2) NULL,
            status ENUM('pending', 'sourcing', 'quoted', 'unavailable', 'fulfilled') DEFAULT 'pending',
            admin_notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_request (distribution_request_id),
            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_shopping_items created\n";

    // 4. Create distribution_invoices table
    echo "Creating distribution_invoices table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_invoices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            business_profile_id INT NOT NULL,
            invoice_number VARCHAR(50) NOT NULL UNIQUE,

            -- Billing snapshot
            billing_company_name VARCHAR(255) NOT NULL,
            billing_contact_name VARCHAR(255) NULL,
            billing_email VARCHAR(255) NULL,
            billing_phone VARCHAR(50) NULL,
            billing_street VARCHAR(255) NOT NULL,
            billing_city VARCHAR(100) NOT NULL,
            billing_province VARCHAR(50) NOT NULL,
            billing_postal_code VARCHAR(20) NOT NULL,
            billing_country VARCHAR(50) DEFAULT 'Canada',

            -- Amounts
            subtotal DECIMAL(12,2) NOT NULL,
            tax_rate DECIMAL(5,2) DEFAULT 14.975,
            tax_amount DECIMAL(12,2) DEFAULT 0.00,
            delivery_fee DECIMAL(12,2) DEFAULT 0.00,
            discount_amount DECIMAL(12,2) DEFAULT 0.00,
            total_amount DECIMAL(12,2) NOT NULL,

            -- Status
            status ENUM('draft', 'sent', 'paid', 'overdue', 'cancelled') DEFAULT 'draft',

            -- Dates
            invoice_date DATE NOT NULL,
            due_date DATE NOT NULL,
            paid_at DATETIME NULL,
            sent_at DATETIME NULL,

            -- PDF
            pdf_path VARCHAR(500) NULL,

            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_request (distribution_request_id),
            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_invoice_number (invoice_number),
            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE,
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_invoices created\n";

    // 5. Create distribution_recurring_orders table
    echo "Creating distribution_recurring_orders table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_recurring_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_profile_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,

            -- Schedule
            frequency ENUM('weekly', 'biweekly', 'monthly') NOT NULL,
            day_of_week TINYINT NULL,
            day_of_month TINYINT NULL,

            -- Timing
            start_date DATE NOT NULL,
            end_date DATE NULL,
            next_generation_date DATE NOT NULL,
            last_generated_at DATETIME NULL,

            -- Settings
            auto_submit TINYINT(1) DEFAULT 0,
            notify_days_before TINYINT DEFAULT 3,

            -- Template items (JSON)
            template_catalog_items JSON NULL,
            template_shopping_items JSON NULL,

            -- Delivery preferences
            delivery_instructions TEXT NULL,

            -- Status
            status ENUM('active', 'paused', 'completed', 'cancelled') DEFAULT 'active',
            paused_at DATETIME NULL,
            cancelled_at DATETIME NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_next_gen (next_generation_date),
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_recurring_orders created\n";

    // 6. Create distribution_status_history table (audit trail)
    echo "Creating distribution_status_history table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_status_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            old_status VARCHAR(50) NULL,
            new_status VARCHAR(50) NOT NULL,
            changed_by INT NULL,
            changed_by_type ENUM('business', 'admin', 'system') DEFAULT 'system',
            notes TEXT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_request (distribution_request_id),
            INDEX idx_created (created_at),
            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_status_history created\n";

    // 7. Create distribution_payments table (payment transaction log)
    echo "Creating distribution_payments table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            distribution_request_id INT NOT NULL,
            business_profile_id INT NOT NULL,

            payment_method ENUM('stripe', 'bank_transfer') NOT NULL,
            amount DECIMAL(12,2) NOT NULL,

            -- External references
            stripe_payment_intent_id VARCHAR(255) NULL,
            stripe_charge_id VARCHAR(255) NULL,
            stripe_session_id VARCHAR(255) NULL,
            bank_transfer_reference VARCHAR(255) NULL,

            status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',

            -- Error handling
            error_code VARCHAR(100) NULL,
            error_message TEXT NULL,

            -- Timestamps
            completed_at DATETIME NULL,
            refunded_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_request (distribution_request_id),
            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_stripe_session (stripe_session_id),
            FOREIGN KEY (distribution_request_id) REFERENCES distribution_requests(id) ON DELETE CASCADE,
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  ✓ distribution_payments created\n";

    // Commit transaction
    $db->commit();

    echo "\n========================================\n";
    echo "Distribution Phase 2 Migration Complete!\n";
    echo "========================================\n";
    echo "\nTables created:\n";
    echo "  1. distribution_requests\n";
    echo "  2. distribution_request_items\n";
    echo "  3. distribution_shopping_items\n";
    echo "  4. distribution_invoices\n";
    echo "  5. distribution_recurring_orders\n";
    echo "  6. distribution_status_history\n";
    echo "  7. distribution_payments\n";

} catch (PDOException $e) {
    // Rollback on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
