<?php
/**
 * Distribution Portal Phase 3 - Outbound Distribution Migration
 * Creates tables for shipments, multi-drop routes, recurring routes
 *
 * Run: php database/migrations/setup_distribution_phase3.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

echo "Starting Distribution Phase 3 Migration (Outbound Distribution)...\n\n";

try {
    $db = Database::getConnection();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Start transaction
    $db->beginTransaction();

    // 1. Create distribution_shipments table
    echo "Creating distribution_shipments table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_profile_id INT NOT NULL,
            shipment_number VARCHAR(50) NOT NULL UNIQUE,
            shipment_type ENUM('parcel', 'product_fulfillment', 'multi_drop') DEFAULT 'parcel',

            -- Status workflow
            status ENUM('draft','submitted','quoted','pending_payment','paid','scheduled','picked_up','in_transit','delivered','completed','cancelled') DEFAULT 'draft',

            -- Pickup Info (from business)
            pickup_street VARCHAR(255) NOT NULL,
            pickup_city VARCHAR(100) NOT NULL,
            pickup_province VARCHAR(50) NOT NULL,
            pickup_postal_code VARCHAR(20) NOT NULL,
            pickup_contact_name VARCHAR(255) NULL,
            pickup_contact_phone VARCHAR(50) NULL,
            pickup_instructions TEXT NULL,
            requested_pickup_date DATE NULL,
            requested_pickup_time_start TIME NULL,
            requested_pickup_time_end TIME NULL,
            actual_pickup_at DATETIME NULL,

            -- For single-destination shipments
            is_multi_drop TINYINT(1) DEFAULT 0,
            destination_street VARCHAR(255) NULL,
            destination_city VARCHAR(100) NULL,
            destination_province VARCHAR(50) NULL,
            destination_postal_code VARCHAR(20) NULL,
            destination_contact_name VARCHAR(255) NULL,
            destination_contact_phone VARCHAR(50) NULL,
            destination_instructions TEXT NULL,

            -- Package Details
            total_packages INT DEFAULT 1,
            total_weight_kg DECIMAL(10,2) NULL,
            package_description TEXT NULL,

            -- Financial
            subtotal DECIMAL(12,2) DEFAULT 0.00,
            tax_amount DECIMAL(12,2) DEFAULT 0.00,
            total_amount DECIMAL(12,2) DEFAULT 0.00,

            -- Payment
            payment_method ENUM('stripe', 'bank_transfer', 'account') NULL,
            payment_status ENUM('pending', 'paid', 'refunded', 'failed') DEFAULT 'pending',
            payment_reference VARCHAR(255) NULL,
            paid_at DATETIME NULL,

            -- Recurring reference
            recurring_route_id INT NULL,
            is_recurring_instance TINYINT(1) DEFAULT 0,

            -- Notes
            business_notes TEXT NULL,
            admin_notes TEXT NULL,

            -- Timestamps
            submitted_at DATETIME NULL,
            quoted_at DATETIME NULL,
            scheduled_for DATE NULL,
            completed_at DATETIME NULL,
            cancelled_at DATETIME NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_shipment_number (shipment_number),
            INDEX idx_scheduled (scheduled_for),
            INDEX idx_payment_status (payment_status),
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipments created\n";

    // 2. Create distribution_shipment_destinations table
    echo "Creating distribution_shipment_destinations table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipment_destinations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shipment_id INT NOT NULL,
            sequence_order INT NOT NULL DEFAULT 1,

            -- Destination Address
            destination_name VARCHAR(255) NOT NULL,
            street VARCHAR(255) NOT NULL,
            city VARCHAR(100) NOT NULL,
            province VARCHAR(50) NOT NULL,
            postal_code VARCHAR(20) NOT NULL,
            contact_name VARCHAR(255) NULL,
            contact_phone VARCHAR(50) NULL,
            delivery_instructions TEXT NULL,

            -- Status per destination
            status ENUM('pending', 'in_transit', 'delivered', 'failed', 'returned') DEFAULT 'pending',
            delivered_at DATETIME NULL,
            delivery_notes TEXT NULL,
            signature_collected TINYINT(1) DEFAULT 0,
            photo_proof_path VARCHAR(500) NULL,

            -- Package info for this stop
            packages_count INT DEFAULT 1,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_shipment (shipment_id),
            INDEX idx_status (status),
            INDEX idx_sequence (sequence_order),
            FOREIGN KEY (shipment_id) REFERENCES distribution_shipments(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipment_destinations created\n";

    // 3. Create distribution_shipment_items table
    echo "Creating distribution_shipment_items table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipment_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shipment_id INT NOT NULL,
            destination_id INT NULL,

            -- Item Details
            item_name VARCHAR(255) NOT NULL,
            item_sku VARCHAR(100) NULL,
            item_description TEXT NULL,
            quantity INT NOT NULL DEFAULT 1,
            unit_value DECIMAL(10,2) NULL,
            weight_kg DECIMAL(10,2) NULL,

            -- For fragile/special handling
            is_fragile TINYINT(1) DEFAULT 0,
            special_handling TEXT NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_shipment (shipment_id),
            INDEX idx_destination (destination_id),
            FOREIGN KEY (shipment_id) REFERENCES distribution_shipments(id) ON DELETE CASCADE,
            FOREIGN KEY (destination_id) REFERENCES distribution_shipment_destinations(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipment_items created\n";

    // 4. Create distribution_shipment_quotes table
    echo "Creating distribution_shipment_quotes table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipment_quotes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shipment_id INT NOT NULL,

            -- Pricing breakdown
            base_rate DECIMAL(10,2) NOT NULL,
            per_stop_rate DECIMAL(10,2) DEFAULT 0.00,
            stops_count INT DEFAULT 1,
            stops_total DECIMAL(10,2) DEFAULT 0.00,
            weight_surcharge DECIMAL(10,2) DEFAULT 0.00,
            distance_surcharge DECIMAL(10,2) DEFAULT 0.00,
            rush_surcharge DECIMAL(10,2) DEFAULT 0.00,

            subtotal DECIMAL(12,2) NOT NULL,
            tax_rate DECIMAL(5,2) DEFAULT 14.975,
            tax_amount DECIMAL(12,2) NOT NULL,
            total_amount DECIMAL(12,2) NOT NULL,

            -- Quote validity
            valid_until DATE NOT NULL,
            notes TEXT NULL,

            created_by INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_shipment (shipment_id),
            FOREIGN KEY (shipment_id) REFERENCES distribution_shipments(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipment_quotes created\n";

    // 5. Create distribution_recurring_routes table
    echo "Creating distribution_recurring_routes table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_recurring_routes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            business_profile_id INT NOT NULL,
            route_name VARCHAR(255) NOT NULL,

            -- Schedule
            frequency ENUM('daily', 'weekly', 'biweekly', 'monthly') NOT NULL,
            days_of_week JSON NULL,
            day_of_month TINYINT NULL,

            -- Pickup template
            pickup_street VARCHAR(255) NOT NULL,
            pickup_city VARCHAR(100) NOT NULL,
            pickup_province VARCHAR(50) NOT NULL,
            pickup_postal_code VARCHAR(20) NOT NULL,
            pickup_time_start TIME NULL,
            pickup_time_end TIME NULL,

            -- Destinations template (JSON array)
            destinations_template JSON NOT NULL,

            -- Schedule management
            start_date DATE NOT NULL,
            end_date DATE NULL,
            next_generation_date DATE NOT NULL,
            last_generated_at DATETIME NULL,

            -- Settings
            auto_submit TINYINT(1) DEFAULT 0,
            notify_days_before TINYINT DEFAULT 2,

            status ENUM('active', 'paused', 'completed', 'cancelled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_next_gen (next_generation_date),
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_recurring_routes created\n";

    // 6. Create distribution_shipment_status_history table
    echo "Creating distribution_shipment_status_history table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipment_status_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shipment_id INT NOT NULL,
            destination_id INT NULL,

            old_status VARCHAR(50) NULL,
            new_status VARCHAR(50) NOT NULL,
            changed_by_type ENUM('business', 'admin', 'driver', 'system') DEFAULT 'system',
            changed_by_id INT NULL,
            notes TEXT NULL,
            location_lat DECIMAL(10,8) NULL,
            location_lng DECIMAL(11,8) NULL,

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

            INDEX idx_shipment (shipment_id),
            INDEX idx_destination (destination_id),
            INDEX idx_created (created_at),
            FOREIGN KEY (shipment_id) REFERENCES distribution_shipments(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipment_status_history created\n";

    // 7. Create distribution_shipment_payments table
    echo "Creating distribution_shipment_payments table...\n";
    $db->exec("
        CREATE TABLE IF NOT EXISTS distribution_shipment_payments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shipment_id INT NOT NULL,
            business_profile_id INT NOT NULL,

            payment_method ENUM('stripe', 'bank_transfer', 'account') NOT NULL,
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

            INDEX idx_shipment (shipment_id),
            INDEX idx_business (business_profile_id),
            INDEX idx_status (status),
            INDEX idx_stripe_session (stripe_session_id),
            FOREIGN KEY (shipment_id) REFERENCES distribution_shipments(id) ON DELETE CASCADE,
            FOREIGN KEY (business_profile_id) REFERENCES business_profiles(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "  + distribution_shipment_payments created\n";

    // Commit transaction
    $db->commit();

    echo "\n========================================\n";
    echo "Distribution Phase 3 Migration Complete!\n";
    echo "========================================\n";
    echo "\nTables created:\n";
    echo "  1. distribution_shipments\n";
    echo "  2. distribution_shipment_destinations\n";
    echo "  3. distribution_shipment_items\n";
    echo "  4. distribution_shipment_quotes\n";
    echo "  5. distribution_recurring_routes\n";
    echo "  6. distribution_shipment_status_history\n";
    echo "  7. distribution_shipment_payments\n";

} catch (PDOException $e) {
    // Rollback on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    echo "\n[ERROR] Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
