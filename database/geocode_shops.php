<?php
/**
 * Geocode Shop Addresses
 * Converts shop addresses to lat/lng coordinates using Nominatim (OpenStreetMap)
 *
 * Run: php geocode_shops.php
 */

echo "<pre>\n";
echo "===========================================\n";
echo "Geocoding Shop Addresses\n";
echo "===========================================\n\n";

// Load environment
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Database connection
$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'marketplace_db';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected to database.\n\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

// Get shops without coordinates
$stmt = $pdo->query("
    SELECT id, name, address
    FROM shops
    WHERE (latitude IS NULL OR longitude IS NULL)
    AND address IS NOT NULL
    AND address != ''
");
$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($shops) . " shops to geocode.\n\n";

// Geocode function using Nominatim
function geocodeAddress($address) {
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'q' => $address,
        'format' => 'json',
        'limit' => 1,
        'addressdetails' => 1
    ]);

    $opts = [
        'http' => [
            'header' => "User-Agent: OCSMarketplace/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);

    $response = @file_get_contents($url, false, $context);
    if (!$response) return null;

    $data = json_decode($response, true);
    if (empty($data)) return null;

    return [
        'lat' => (float) $data[0]['lat'],
        'lng' => (float) $data[0]['lon'],
        'display_name' => $data[0]['display_name']
    ];
}

// Process each shop
$updated = 0;
$failed = 0;

foreach ($shops as $shop) {
    echo "Geocoding: {$shop['name']} ({$shop['address']})... ";

    $result = geocodeAddress($shop['address']);

    if ($result) {
        // Update shop with coordinates
        $updateStmt = $pdo->prepare("
            UPDATE shops
            SET latitude = ?, longitude = ?
            WHERE id = ?
        ");
        $updateStmt->execute([$result['lat'], $result['lng'], $shop['id']]);

        echo "OK ({$result['lat']}, {$result['lng']})\n";
        $updated++;
    } else {
        echo "FAILED (no results)\n";
        $failed++;
    }

    // Rate limiting - Nominatim requires 1 second between requests
    sleep(1);
}

echo "\n===========================================\n";
echo "Geocoding Complete!\n";
echo "Updated: {$updated}\n";
echo "Failed: {$failed}\n";
echo "===========================================\n";

// Show results
$stmt = $pdo->query("SELECT id, name, latitude, longitude, delivery_radius FROM shops WHERE latitude IS NOT NULL");
$geocodedShops = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "\nGeocoded Shops:\n";
foreach ($geocodedShops as $shop) {
    echo "  - {$shop['name']}: ({$shop['latitude']}, {$shop['longitude']}) - {$shop['delivery_radius']}km radius\n";
}

echo "</pre>\n";
