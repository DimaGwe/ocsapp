<?php
/**
 * Migration: Payout Enhancements
 * - Adds payment_reference and payment_notes to delivery_earnings
 * Run: php database/migrations/setup_payout_enhancements.php
 */

require_once dirname(dirname(__DIR__)) . '/bootstrap/init.php';
require_once dirname(dirname(__DIR__)) . '/config/database.php';

try {
    $db = Database::getConnection();

    $cols = $db->query("DESCRIBE delivery_earnings")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('payment_reference', $cols)) {
        $db->exec("ALTER TABLE delivery_earnings ADD COLUMN payment_reference VARCHAR(100) NULL AFTER paid_at");
        echo "Added payment_reference to delivery_earnings.\n";
    } else {
        echo "payment_reference already exists. Skipping.\n";
    }

    if (!in_array('payment_notes', $cols)) {
        $db->exec("ALTER TABLE delivery_earnings ADD COLUMN payment_notes TEXT NULL AFTER payment_reference");
        echo "Added payment_notes to delivery_earnings.\n";
    } else {
        echo "payment_notes already exists. Skipping.\n";
    }

    echo "\nPayout enhancements migration complete!\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
