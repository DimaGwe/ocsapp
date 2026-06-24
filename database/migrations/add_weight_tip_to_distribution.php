<?php
/**
 * Migration: Add weight, tip, and membership columns to distribution_requests
 * Supports new pricing model: $0.20/kg handling, optional tips, $7.99 membership
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $columns = [
        'total_weight_kg' => "ALTER TABLE distribution_requests ADD COLUMN total_weight_kg DECIMAL(10,2) DEFAULT 0 AFTER handling_fee",
        'tip_amount' => "ALTER TABLE distribution_requests ADD COLUMN tip_amount DECIMAL(10,2) DEFAULT 0 AFTER qst_amount",
        'tip_percentage' => "ALTER TABLE distribution_requests ADD COLUMN tip_percentage TINYINT DEFAULT 0 AFTER tip_amount",
    ];

    foreach ($columns as $colName => $sql) {
        $check = $db->prepare("
            SELECT COUNT(*) FROM information_schema.columns
            WHERE table_schema = DATABASE()
            AND table_name = 'distribution_requests'
            AND column_name = :col
        ");
        $check->execute(['col' => $colName]);

        if ($check->fetchColumn() > 0) {
            echo "{$colName} already exists. Skipping.\n";
        } else {
            $db->exec($sql);
            echo "{$colName} added successfully.\n";
        }
    }

    echo "Migration complete.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
