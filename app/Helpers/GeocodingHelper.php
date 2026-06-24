<?php
/**
 * Geocoding Helper
 * Provides distance calculation and postal code geocoding for delivery routes.
 * Uses Haversine formula x 1.3 road factor for ~85-90% accuracy.
 */

class GeocodingHelper
{
    private const ROAD_FACTOR = 1.3;
    private const EARTH_RADIUS_KM = 6371;
    private const NOMINATIM_URL = 'https://nominatim.openstreetmap.org/search';
    private const USER_AGENT = 'OCSAPP-Distribution/1.0';

    /**
     * Geocode a postal code using OpenStreetMap Nominatim API
     * Returns [latitude, longitude] or null on failure
     */
    public static function geocodePostalCode(string $postalCode, string $country = 'CA'): ?array
    {
        $postalCode = strtoupper(preg_replace('/\s+/', ' ', trim($postalCode)));

        if (empty($postalCode)) {
            return null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: ' . self::USER_AGENT . "\r\n",
                'timeout' => 10
            ]
        ]);

        // Try with full postal code first, then FSA (first 3 chars) as fallback
        $attempts = [$postalCode];
        $fsa = substr(preg_replace('/\s+/', '', $postalCode), 0, 3);
        if (strlen($fsa) === 3) {
            $attempts[] = $fsa;
        }

        foreach ($attempts as $code) {
            $params = http_build_query([
                'postalcode' => $code,
                'countrycodes' => $country,
                'format' => 'json',
                'limit' => 1
            ]);

            $url = self::NOMINATIM_URL . '?' . $params;
            $response = @file_get_contents($url, false, $context);

            if ($response !== false) {
                $data = json_decode($response, true);
                if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                    return [
                        'lat' => (float)$data[0]['lat'],
                        'lng' => (float)$data[0]['lon']
                    ];
                }
            }

            usleep(100000); // 100ms between attempts
        }

        error_log("No geocoding results for postal code: {$postalCode}");
        return null;
    }

    /**
     * Geocode using city and province as fallback when postal code fails
     */
    public static function geocodeAddress(string $city, string $province = '', string $country = 'Canada'): ?array
    {
        $query = trim("{$city} {$province} {$country}");
        if (empty($city)) return null;

        $params = http_build_query([
            'q' => $query,
            'countrycodes' => 'CA',
            'format' => 'json',
            'limit' => 1
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: ' . self::USER_AGENT . "\r\n",
                'timeout' => 10
            ]
        ]);

        $url = self::NOMINATIM_URL . '?' . $params;
        $response = @file_get_contents($url, false, $context);

        if ($response !== false) {
            $data = json_decode($response, true);
            if (!empty($data) && isset($data[0]['lat'], $data[0]['lon'])) {
                return [
                    'lat' => (float)$data[0]['lat'],
                    'lng' => (float)$data[0]['lon']
                ];
            }
        }

        return null;
    }

    /**
     * Calculate straight-line distance between two points using Haversine formula
     * Returns distance in kilometers
     */
    public static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return self::EARTH_RADIUS_KM * $c;
    }

    /**
     * Calculate total route distance through waypoints with road factor
     * Waypoints: array of [lat, lng] arrays in order of travel
     * Returns estimated road distance in km
     */
    public static function calculateRouteDistance(array $waypoints): float
    {
        if (count($waypoints) < 2) {
            return 0;
        }

        $totalDistance = 0;
        for ($i = 0; $i < count($waypoints) - 1; $i++) {
            $totalDistance += self::haversineDistance(
                $waypoints[$i]['lat'], $waypoints[$i]['lng'],
                $waypoints[$i + 1]['lat'], $waypoints[$i + 1]['lng']
            );
        }

        return round($totalDistance * self::ROAD_FACTOR, 1);
    }

    /**
     * Order suppliers using nearest-neighbor heuristic, then add customer as final stop
     * Returns ordered waypoints array
     */
    public static function optimizeRoute(array $supplierCoords, array $customerCoord): array
    {
        if (empty($supplierCoords)) {
            return [];
        }

        // If only one supplier, route is simple
        if (count($supplierCoords) === 1) {
            return [array_values($supplierCoords)[0], $customerCoord];
        }

        // Nearest-neighbor: start from first supplier, always go to nearest unvisited
        $unvisited = $supplierCoords;
        $route = [];

        // Pick the supplier nearest to customer as starting point (reverse logic for pickup)
        $currentLat = $customerCoord['lat'];
        $currentLng = $customerCoord['lng'];

        // Find farthest supplier from customer (start there, work back)
        $farthestDist = 0;
        $farthestKey = array_key_first($unvisited);
        foreach ($unvisited as $key => $coord) {
            $dist = self::haversineDistance($currentLat, $currentLng, $coord['lat'], $coord['lng']);
            if ($dist > $farthestDist) {
                $farthestDist = $dist;
                $farthestKey = $key;
            }
        }

        // Start from farthest supplier
        $route[] = $unvisited[$farthestKey];
        $currentLat = $unvisited[$farthestKey]['lat'];
        $currentLng = $unvisited[$farthestKey]['lng'];
        unset($unvisited[$farthestKey]);

        // Visit nearest unvisited supplier
        while (!empty($unvisited)) {
            $nearestDist = PHP_FLOAT_MAX;
            $nearestKey = null;
            foreach ($unvisited as $key => $coord) {
                $dist = self::haversineDistance($currentLat, $currentLng, $coord['lat'], $coord['lng']);
                if ($dist < $nearestDist) {
                    $nearestDist = $dist;
                    $nearestKey = $key;
                }
            }
            $route[] = $unvisited[$nearestKey];
            $currentLat = $unvisited[$nearestKey]['lat'];
            $currentLng = $unvisited[$nearestKey]['lng'];
            unset($unvisited[$nearestKey]);
        }

        // Customer is the final destination
        $route[] = $customerCoord;

        return $route;
    }

    /**
     * Get driving route from OSRM (Open Source Routing Machine)
     * Uses the free demo server for road-based routing.
     * Returns distance, duration, and polyline coordinates.
     *
     * @param array $waypoints Array of ['lat' => float, 'lng' => float]
     * @return array|null {distance_km, duration_min, polyline: [[lat,lng],...]}
     */
    public static function getOSRMRoute(array $waypoints): ?array
    {
        if (count($waypoints) < 2) {
            return null;
        }

        // OSRM expects lng,lat format
        $coords = array_map(
            fn($w) => round($w['lng'], 6) . ',' . round($w['lat'], 6),
            $waypoints
        );

        $url = 'https://router.project-osrm.org/route/v1/driving/'
            . implode(';', $coords)
            . '?overview=full&geometries=geojson';

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: ' . self::USER_AGENT . "\r\n",
                'timeout' => 10
            ]
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            error_log('OSRM request failed for route');
            return null;
        }

        $data = json_decode($response, true);
        if (empty($data['routes'][0])) {
            return null;
        }

        $route = $data['routes'][0];

        // Convert GeoJSON coordinates [lng, lat] to [lat, lng]
        $polyline = array_map(
            fn($coord) => ['lat' => $coord[1], 'lng' => $coord[0]],
            $route['geometry']['coordinates']
        );

        return [
            'distance_km' => round($route['distance'] / 1000, 1),
            'duration_min' => round($route['duration'] / 60),
            'polyline' => $polyline
        ];
    }
}
