<?php
/**
 * Geocode Shop Addresses - Production
 */
$host = "ocs-marketplace-db.c98w8ccoghgv.us-east-2.rds.amazonaws.com";
$dbname = "marketplace_db";
$user = "ocs_admin";
$pass = "t4Dru3gVenBIa3Jhj3ze";

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
echo "Connected to database\n";

// Get shops without coordinates
$stmt = $pdo->query("SELECT id, name, address FROM shops WHERE (latitude IS NULL OR longitude IS NULL) AND address IS NOT NULL");
$shops = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Shops to geocode: " . count($shops) . "\n\n";

foreach ($shops as $shop) {
    $address = trim($shop['address']);
    if (empty($address)) continue;

    echo "Geocoding: {$shop['name']} ({$address})... ";

    $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
        'q' => $address,
        'format' => 'json',
        'limit' => 1
    ]);

    $opts = stream_context_create([
        'http' => ['header' => "User-Agent: OCSMarketplace/1.0\r\n"]
    ]);

    $response = @file_get_contents($url, false, $opts);

    if ($response) {
        $data = json_decode($response, true);
        if (!empty($data)) {
            $lat = $data[0]['lat'];
            $lon = $data[0]['lon'];

            $upd = $pdo->prepare("UPDATE shops SET latitude = ?, longitude = ? WHERE id = ?");
            $upd->execute([$lat, $lon, $shop['id']]);

            echo "OK ({$lat}, {$lon})\n";
        } else {
            echo "No results\n";
        }
    } else {
        echo "API call failed\n";
    }

    sleep(1); // Rate limiting for Nominatim
}

echo "\n=== Geocoded Shops ===\n";
$result = $pdo->query("SELECT id, name, latitude, longitude, delivery_radius FROM shops WHERE latitude IS NOT NULL");
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    echo "  - {$row['name']}: ({$row['latitude']}, {$row['longitude']}) - {$row['delivery_radius']}km radius\n";
}
echo "\nDone!\n";
