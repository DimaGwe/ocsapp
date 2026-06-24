<?php
/**
 * Migration: Add geocoding columns to suppliers and business_profiles
 * Adds latitude/longitude for auto-distance calculation
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
    echo "Adding geocoding columns...\n";

    // Add lat/lng to suppliers table
    if (!columnExists($pdo, 'suppliers', 'latitude')) {
        $pdo->exec("ALTER TABLE suppliers ADD COLUMN latitude DECIMAL(10,7) NULL DEFAULT NULL");
        echo "- Added latitude to suppliers table\n";
    } else {
        echo "- latitude already exists in suppliers table\n";
    }

    if (!columnExists($pdo, 'suppliers', 'longitude')) {
        $pdo->exec("ALTER TABLE suppliers ADD COLUMN longitude DECIMAL(10,7) NULL DEFAULT NULL");
        echo "- Added longitude to suppliers table\n";
    } else {
        echo "- longitude already exists in suppliers table\n";
    }

    // Add delivery lat/lng to business_profiles table
    if (!columnExists($pdo, 'business_profiles', 'delivery_latitude')) {
        $pdo->exec("ALTER TABLE business_profiles ADD COLUMN delivery_latitude DECIMAL(10,7) NULL DEFAULT NULL");
        echo "- Added delivery_latitude to business_profiles table\n";
    } else {
        echo "- delivery_latitude already exists in business_profiles table\n";
    }

    if (!columnExists($pdo, 'business_profiles', 'delivery_longitude')) {
        $pdo->exec("ALTER TABLE business_profiles ADD COLUMN delivery_longitude DECIMAL(10,7) NULL DEFAULT NULL");
        echo "- Added delivery_longitude to business_profiles table\n";
    } else {
        echo "- delivery_longitude already exists in business_profiles table\n";
    }

    echo "\nMigration completed successfully!\n";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
