<?php
/**
 * One-time script: Geocode existing suppliers and business profiles
 * Uses OpenStreetMap Nominatim API with 1 req/sec rate limit
 */

require_once dirname(__DIR__, 2) . '/bootstrap/init.php';
require_once dirname(__DIR__, 2) . '/config/database.php';
require_once dirname(__DIR__, 2) . '/app/Helpers/GeocodingHelper.php';

try {
    $pdo = Database::getConnection();
    $geocoded = 0;
    $failed = 0;

    // Geocode suppliers with postal codes but no lat/lng
    echo "=== Geocoding Suppliers ===\n";
    $stmt = $pdo->query("
        SELECT id, name, postal_code
        FROM suppliers
        WHERE postal_code IS NOT NULL AND postal_code != ''
        AND (latitude IS NULL OR longitude IS NULL)
    ");
    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($suppliers as $supplier) {
        echo "Geocoding supplier #{$supplier['id']} ({$supplier['name']}), postal: {$supplier['postal_code']}... ";

        $coords = GeocodingHelper::geocodePostalCode($supplier['postal_code']);
        if ($coords) {
            $update = $pdo->prepare("UPDATE suppliers SET latitude = ?, longitude = ? WHERE id = ?");
            $update->execute([$coords['lat'], $coords['lng'], $supplier['id']]);
            echo "OK ({$coords['lat']}, {$coords['lng']})\n";
            $geocoded++;
        } else {
            echo "FAILED\n";
            $failed++;
        }

        sleep(1); // Respect Nominatim rate limit
    }

    // Geocode business profiles with delivery postal codes but no lat/lng
    echo "\n=== Geocoding Business Profiles ===\n";
    $stmt = $pdo->query("
        SELECT id, company_name, delivery_postal_code
        FROM business_profiles
        WHERE delivery_postal_code IS NOT NULL AND delivery_postal_code != ''
        AND (delivery_latitude IS NULL OR delivery_longitude IS NULL)
    ");
    $profiles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($profiles as $profile) {
        echo "Geocoding business #{$profile['id']} ({$profile['company_name']}), postal: {$profile['delivery_postal_code']}... ";

        $coords = GeocodingHelper::geocodePostalCode($profile['delivery_postal_code']);
        if ($coords) {
            $update = $pdo->prepare("UPDATE business_profiles SET delivery_latitude = ?, delivery_longitude = ? WHERE id = ?");
            $update->execute([$coords['lat'], $coords['lng'], $profile['id']]);
            echo "OK ({$coords['lat']}, {$coords['lng']})\n";
            $geocoded++;
        } else {
            echo "FAILED\n";
            $failed++;
        }

        sleep(1); // Respect Nominatim rate limit
    }

    echo "\n=== Summary ===\n";
    echo "Geocoded: {$geocoded}\n";
    echo "Failed: {$failed}\n";
    echo "Done!\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
