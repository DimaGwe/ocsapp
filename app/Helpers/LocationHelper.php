<?php
/**
 * Location Helper - Distance calculations and filtering
 */

if (!function_exists('calculateDistance')) {
    /**
     * Calculate distance between two coordinates using Haversine formula
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in kilometers
     */
    function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}

if (!function_exists('getUserLocation')) {
    /**
     * Get user's location from session
     *
     * @return array|null Location data or null
     */
    function getUserLocation(): ?array
    {
        if (empty($_SESSION['user_latitude']) || empty($_SESSION['user_longitude'])) {
            return null;
        }

        return [
            'latitude' => (float) $_SESSION['user_latitude'],
            'longitude' => (float) $_SESSION['user_longitude'],
            'radius' => (int) ($_SESSION['delivery_radius'] ?? 10),
            'name' => $_SESSION['location'] ?? 'Unknown'
        ];
    }
}

if (!function_exists('isShopInDeliveryRange')) {
    /**
     * Check if a shop delivers to user's location
     *
     * @param array $shop Shop data with latitude, longitude, delivery_radius
     * @param array|null $userLocation User location data
     * @return bool True if shop delivers to user's location
     */
    function isShopInDeliveryRange(array $shop, ?array $userLocation = null): bool
    {
        if (!$userLocation) {
            $userLocation = getUserLocation();
        }

        // If no user location, show all shops
        if (!$userLocation) {
            return true;
        }

        // If shop has no coordinates, show it (assume it delivers everywhere)
        if (empty($shop['latitude']) || empty($shop['longitude'])) {
            return true;
        }

        $distance = calculateDistance(
            $userLocation['latitude'],
            $userLocation['longitude'],
            (float) $shop['latitude'],
            (float) $shop['longitude']
        );

        // Check if user is within shop's delivery radius
        $shopRadius = (int) ($shop['delivery_radius'] ?? 15);

        return $distance <= $shopRadius;
    }
}

if (!function_exists('filterShopsByLocation')) {
    /**
     * Filter shops array to only those that deliver to user's location
     *
     * @param array $shops Array of shops
     * @param array|null $userLocation User location data
     * @return array Filtered shops with distance added
     */
    function filterShopsByLocation(array $shops, ?array $userLocation = null): array
    {
        if (!$userLocation) {
            $userLocation = getUserLocation();
        }

        // If no user location, return all shops
        if (!$userLocation) {
            return $shops;
        }

        $filtered = [];
        foreach ($shops as $shop) {
            if (isShopInDeliveryRange($shop, $userLocation)) {
                // Add distance to shop data
                if (!empty($shop['latitude']) && !empty($shop['longitude'])) {
                    $shop['distance'] = round(calculateDistance(
                        $userLocation['latitude'],
                        $userLocation['longitude'],
                        (float) $shop['latitude'],
                        (float) $shop['longitude']
                    ), 1);
                } else {
                    $shop['distance'] = null;
                }
                $filtered[] = $shop;
            }
        }

        // Sort by distance (closest first)
        usort($filtered, function ($a, $b) {
            if ($a['distance'] === null) return 1;
            if ($b['distance'] === null) return -1;
            return $a['distance'] <=> $b['distance'];
        });

        return $filtered;
    }
}

if (!function_exists('getShopsInDeliveryRange')) {
    /**
     * Get shops that deliver to a specific location from database
     *
     * @param float $latitude User latitude
     * @param float $longitude User longitude
     * @param string|null $shopType Filter by shop type
     * @param int $limit Maximum shops to return
     * @return array Shops that can deliver to location
     */
    function getShopsInDeliveryRange(float $latitude, float $longitude, ?string $shopType = null, int $limit = 20): array
    {
        try {
            $db = \Database::getConnection();

            // Build query with Haversine distance calculation
            $sql = "
                SELECT s.*,
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(latitude))
                    )) AS distance
                FROM shops s
                WHERE s.status = 'active'
                AND s.latitude IS NOT NULL
                AND s.longitude IS NOT NULL
                HAVING distance <= s.delivery_radius
            ";

            $params = [$latitude, $longitude, $latitude];

            if ($shopType) {
                $sql .= " AND s.shop_type = ?";
                $params[] = $shopType;
            }

            $sql .= " ORDER BY distance ASC LIMIT ?";
            $params[] = $limit;

            $stmt = $db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            error_log("Location filter error: " . $e->getMessage());
            return [];
        }
    }
}
