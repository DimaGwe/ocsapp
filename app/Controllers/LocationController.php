<?php

namespace App\Controllers;

class LocationController {

    /**
     * Reverse geocode coordinates to get location name
     */
    public function reverseGeocode() {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get coordinates from request
        $data = json_decode(file_get_contents('php://input'), true);
        $lat = $data['lat'] ?? null;
        $lon = $data['lon'] ?? null;

        if (!$lat || !$lon) {
            http_response_code(400);
            echo json_encode(['error' => 'Latitude and longitude are required']);
            return;
        }

        // Validate coordinates
        if (!is_numeric($lat) || !is_numeric($lon)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid coordinates']);
            return;
        }

        try {
            // Call Nominatim API from server-side (no CORS issues)
            $url = "https://nominatim.openstreetmap.org/reverse?" . http_build_query([
                'lat' => $lat,
                'lon' => $lon,
                'format' => 'json',
                'addressdetails' => 1
            ]);

            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: OCSAPP/1.0\r\n"
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            if ($response === false) {
                throw new \Exception('Failed to fetch geocoding data');
            }

            $data = json_decode($response, true);

            // Extract location name
            $locationName = $data['address']['city'] ??
                          $data['address']['town'] ??
                          $data['address']['municipality'] ??
                          $data['address']['village'] ??
                          $data['address']['county'] ??
                          'Your Location';

            // Return successful response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'location' => $locationName,
                'full_address' => $data['display_name'] ?? '',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Geocoding failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Forward geocode - search for locations by address/city/postal code
     */
    public function search() {
        // Only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // Get search query from request
        $data = json_decode(file_get_contents('php://input'), true);
        $query = $data['query'] ?? '';

        if (empty($query)) {
            http_response_code(400);
            echo json_encode(['error' => 'Query is required']);
            return;
        }

        try {
            // Format query for better Canadian postal code matching
            $formattedQuery = $this->formatPostalCode($query);

            // Call Nominatim API from server-side (no CORS issues)
            $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
                'q' => $formattedQuery,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 5,
                'countrycodes' => 'ca' // Canada only - not delivering to US
            ]);

            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: OCSAPP/1.0\r\n"
                ]
            ];

            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);

            if ($response === false) {
                throw new \Exception('Failed to fetch geocoding data');
            }

            $data = json_decode($response, true);

            // Transform results to our format
            $results = array_map(function($item) {
                // Extract city/town name
                $cityName = $item['address']['city'] ??
                          $item['address']['town'] ??
                          $item['address']['village'] ??
                          $item['address']['municipality'] ??
                          $item['address']['county'] ??
                          $item['name'] ?? 'Unknown';

                // Get province/state
                $region = $item['address']['state'] ?? $item['address']['province'] ?? '';

                // Get country
                $country = $item['address']['country'] ?? '';

                // Format display name
                $displayName = trim("{$cityName}, {$region}, {$country}", ', ');

                return [
                    'name' => $cityName,
                    'address' => $displayName,
                    'type' => $item['type'] ?? 'location',
                    'lat' => $item['lat'] ?? null,
                    'lon' => $item['lon'] ?? null
                ];
            }, $data);

            // Return successful response
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Geocoding failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Forward geocode a structured Canadian address → lat/lng
     * Used by the distribution request form to get delivery coordinates.
     * Proxies through server so we can send a proper User-Agent (Nominatim requirement).
     */
    public function geocodeAddress() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $street = trim($body['street']      ?? '');
        $city   = trim($body['city']        ?? '');
        $province = trim($body['province']  ?? '');
        $postal = strtoupper(trim($body['postal_code'] ?? ''));

        if (empty($city) && empty($postal)) {
            http_response_code(400);
            echo json_encode(['error' => 'city or postal_code required']);
            return;
        }

        header('Content-Type: application/json');

        $options = ['http' => ['method' => 'GET', 'header' => "User-Agent: OCSAPP-Distribution/1.0 (info@ocsapp.ca)\r\n", 'timeout' => 8]];
        $context = stream_context_create($options);

        // Strategy 1: full structured address (most accurate)
        if ($street && $city && $province && $postal) {
            $result = $this->_nominatimStructured($street, $city, $province, $postal, $context);
            if ($result) { echo json_encode(['success' => true] + $result); return; }
        }

        // Strategy 2: postal + city
        if ($postal && $city) {
            $result = $this->_nominatimQuery("$postal $city $province Canada", $context);
            if ($result) { echo json_encode(['success' => true] + $result); return; }
        }

        // Strategy 3: postal code only (full, then FSA fallback)
        if ($postal) {
            $result = $this->_nominatimPostal($postal, $context);
            if ($result) { echo json_encode(['success' => true] + $result); return; }
        }

        // Strategy 4: city + province
        if ($city) {
            $result = $this->_nominatimQuery("$city $province Canada", $context);
            if ($result) { echo json_encode(['success' => true] + $result); return; }
        }

        echo json_encode(['success' => false, 'error' => 'Could not geocode address']);
    }

    private function _nominatimStructured(string $street, string $city, string $province, string $postal, $ctx): ?array {
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'street'       => $street,
            'city'         => $city,
            'state'        => $province,
            'postalcode'   => $postal,
            'country'      => 'Canada',
            'format'       => 'json',
            'addressdetails' => 1,
            'limit'        => 1,
        ]);
        return $this->_nominatimFetch($url, $ctx);
    }

    private function _nominatimPostal(string $postal, $ctx): ?array {
        $clean = preg_replace('/\s+/', '', $postal);
        foreach ([$postal, substr($clean, 0, 3)] as $code) {
            $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
                'postalcode'   => $code,
                'countrycodes' => 'CA',
                'format'       => 'json',
                'limit'        => 1,
            ]);
            $r = $this->_nominatimFetch($url, $ctx);
            if ($r) return $r;
            usleep(150000);
        }
        return null;
    }

    private function _nominatimQuery(string $q, $ctx): ?array {
        $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
            'q'            => $q,
            'countrycodes' => 'CA',
            'format'       => 'json',
            'limit'        => 1,
        ]);
        return $this->_nominatimFetch($url, $ctx);
    }

    private function _nominatimFetch(string $url, $ctx): ?array {
        $resp = @file_get_contents($url, false, $ctx);
        if ($resp === false) return null;
        $data = json_decode($resp, true);
        if (!empty($data[0]['lat'])) {
            return ['lat' => (float)$data[0]['lat'], 'lng' => (float)$data[0]['lon']];
        }
        return null;
    }

    /**
     * Format postal code for better search results
     * Handles Canadian postal codes (A1A 1A1)
     */
    private function formatPostalCode($query) {
        $query = strtoupper(trim($query));

        // Canadian postal code pattern: A1A 1A1 or A1A1A1
        // If it matches the pattern without space, add the space
        if (preg_match('/^([A-Z]\d[A-Z])(\d[A-Z]\d)$/', $query, $matches)) {
            // Full postal code without space: H3Z2Y7 -> H3Z 2Y7
            return $matches[1] . ' ' . $matches[2];
        }

        // If it's already properly formatted, return as is
        if (preg_match('/^[A-Z]\d[A-Z]\s\d[A-Z]\d$/', $query)) {
            return $query;
        }

        // For partial Canadian postal codes (3-5 characters), add ", Canada" to help search
        if (preg_match('/^[A-Z]\d[A-Z](\d[A-Z]?)?$/', $query)) {
            return $query . ', Canada';
        }

        // Return original query for everything else (city names, etc.)
        return $query;
    }

    /**
     * Set user's location in session
     */
    public function setLocation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $location = $_POST['location'] ?? '';

        if (empty($location)) {
            http_response_code(400);
            echo json_encode(['error' => 'Location is required']);
            return;
        }

        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Save location to session
        $_SESSION['user_location'] = $location;

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'location' => $location
        ]);
    }
}
