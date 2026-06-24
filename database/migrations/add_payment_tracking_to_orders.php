<?php
/**
 * Migration: Add payment tracking columns to orders table
 * Adds payment_intent_id, payment_gateway for tracking gateway transactions
 * Also seeds Interac e-Transfer settings into payment_settings
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

function columnExists(PDO $pdo, string $table, string $column): bool {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
    ");
    $stmt->execute([$table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    $pdo = Database::getConnection();
    echo "Adding payment tracking columns to orders table...\n";

    // 1. Add payment_intent_id column (stores Stripe session ID / PayPal order ID)
    if (!columnExists($pdo, 'orders', 'payment_intent_id')) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_intent_id VARCHAR(255) NULL DEFAULT NULL");
        echo "- Added payment_intent_id column\n";
    } else {
        echo "- payment_intent_id already exists\n";
    }

    // 2. Add payment_gateway column (stripe/paypal/interac)
    if (!columnExists($pdo, 'orders', 'payment_gateway')) {
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_gateway VARCHAR(50) NULL DEFAULT NULL");
        echo "- Added payment_gateway column\n";
    } else {
        echo "- payment_gateway already exists\n";
    }

    // 3. Add Interac e-Transfer settings to payment_settings table
    echo "\nSeeding Interac e-Transfer settings...\n";

    // Check if payment_settings table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'payment_settings'");
    if (!$stmt->fetch()) {
        $pdo->exec("
            CREATE TABLE payment_settings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) UNIQUE NOT NULL,
                setting_value TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        echo "- Created payment_settings table\n";
    }

    // Insert Interac settings (only if not already present)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM payment_settings WHERE setting_key = ?");

    $stmt->execute(['interac_email']);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('interac_email', '')");
        echo "- Added interac_email setting\n";
    } else {
        echo "- interac_email already exists\n";
    }

    $stmt->execute(['interac_instructions']);
    if ((int)$stmt->fetchColumn() === 0) {
        $pdo->exec("INSERT INTO payment_settings (setting_key, setting_value) VALUES ('interac_instructions', 'Please send an Interac e-Transfer to the email above with your order number as the message.')");
        echo "- Added interac_instructions setting\n";
    } else {
        echo "- interac_instructions already exists\n";
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
