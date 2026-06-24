<?php

namespace App\Helpers;

/**
 * Visitor Tracking Helper
 * Tracks page views, sessions, and user behavior
 */
class VisitorTracker {
    
    /**
     * Track a page view
     */
    public static function track(): void {
        // Only track if the user has given cookie consent (Quebec Law 25 / PIPEDA)
        if (empty($_COOKIE['cookie_consent']) || $_COOKIE['cookie_consent'] !== 'accepted') {
            return;
        }

        try {
            $db = \Database::getConnection();

            // Skip bots and crawlers
            $userAgentRaw = $_SERVER['HTTP_USER_AGENT'] ?? '';
            if (preg_match('/bot|crawl|slurp|spider|mediapartners|googlebot|bingbot|semrush|ahrefs|python-requests|curl|wget|scrapy|lighthouse|headless|phantomjs|prerender|facebookexternalhit|twitterbot|linkedinbot/i', $userAgentRaw)) {
                return;
            }

            // Get visitor info
            $visitorId = self::getVisitorId();
            $sessionId = self::getSessionId();
            $userId = function_exists('userId') ? userId() : null;
            $isNewVisitor = !isset($_COOKIE['visitor_id']);
            
            // Page info
            $url = $_SERVER['REQUEST_URI'] ?? '/';
            $pageTitle = self::getPageTitle();
            $referrer = $_SERVER['HTTP_REFERER'] ?? null;
            
            // Device & location info
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ipAddress = self::getIpAddress();
            $device = self::detectDevice($userAgent);
            $browser = self::detectBrowser($userAgent);
            $os = self::detectOS($userAgent);
            
            // Geographic info (lookup from IP)
            $geoData = self::getGeolocation($ipAddress);
            $country = $geoData['country'] ?? null;
            $countryCode = $geoData['country_code'] ?? null;
            $region = $geoData['region'] ?? null;
            $city = $geoData['city'] ?? null;
            $latitude = $geoData['latitude'] ?? null;
            $longitude = $geoData['longitude'] ?? null;
            $timezone = $geoData['timezone'] ?? null;
            $isp = $geoData['isp'] ?? null;
            $organization = $geoData['org'] ?? null;
            $asn = $geoData['as'] ?? null;

            // Insert page view
            $stmt = $db->prepare("
                INSERT INTO visitor_analytics (
                    visitor_id, session_id, user_id, page_url, page_title,
                    referrer, ip_address, user_agent, device_type, browser,
                    operating_system, country, country_code, region, city,
                    latitude, longitude, timezone, isp, organization, asn,
                    is_new_visitor, visited_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $visitorId,
                $sessionId,
                $userId,
                $url,
                $pageTitle,
                $referrer,
                $ipAddress,
                $userAgent,
                $device,
                $browser,
                $os,
                $country,
                $countryCode,
                $region,
                $city,
                $latitude,
                $longitude,
                $timezone,
                $isp,
                $organization,
                $asn,
                $isNewVisitor ? 1 : 0
            ]);
            
            // Update session end time
            self::updateSessionEndTime($sessionId);
            
            // Set visitor cookie (1 year)
            if ($isNewVisitor) {
                setcookie('visitor_id', $visitorId, time() + 31536000, '/');
            }
            
        } catch (\PDOException $e) {
            // Silently fail - don't break the page if tracking fails
            error_log("Visitor tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Get or create visitor ID
     */
    private static function getVisitorId(): string {
        if (isset($_COOKIE['visitor_id'])) {
            return $_COOKIE['visitor_id'];
        }
        
        // Generate unique visitor ID
        return uniqid('v_', true) . '_' . md5($_SERVER['HTTP_USER_AGENT'] ?? '' . self::getIpAddress());
    }
    
    /**
     * Get or create session ID
     */
    private static function getSessionId(): string {
        if (isset($_SESSION['tracking_session_id'])) {
            // Check if session expired (30 min inactivity)
            $lastActivity = $_SESSION['last_tracking_activity'] ?? 0;
            if (time() - $lastActivity > 1800) {
                // Session expired, create new one
                $_SESSION['tracking_session_id'] = uniqid('s_', true);
            }
        } else {
            $_SESSION['tracking_session_id'] = uniqid('s_', true);
        }
        
        $_SESSION['last_tracking_activity'] = time();
        return $_SESSION['tracking_session_id'];
    }
    
    /**
     * Update session end time
     */
    private static function updateSessionEndTime(string $sessionId): void {
        try {
            $db = \Database::getConnection();
            
            // Check if session record exists
            $stmt = $db->prepare("SELECT id FROM visitor_sessions WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            
            if ($stmt->fetch()) {
                // Update existing session
                $stmt = $db->prepare("
                    UPDATE visitor_sessions 
                    SET 
                        last_activity_at = NOW(),
                        page_views = page_views + 1
                    WHERE session_id = ?
                ");
                $stmt->execute([$sessionId]);
            } else {
                // Create new session record
                $stmt = $db->prepare("
                    INSERT INTO visitor_sessions (
                        session_id, visitor_id, user_id, started_at, last_activity_at, page_views
                    ) VALUES (?, ?, ?, NOW(), NOW(), 1)
                ");
                $stmt->execute([
                    $sessionId,
                    self::getVisitorId(),
                    function_exists('userId') ? userId() : null
                ]);
            }
        } catch (\PDOException $e) {
            error_log("Session update error: " . $e->getMessage());
        }
    }
    
    /**
     * Get page title from URL
     */
    private static function getPageTitle(): ?string {
        // Strip query string
        $url = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');

        $titles = [
            '/login'             => 'Login',
            '/register'          => 'Register',
            '/cart'              => 'Cart',
            '/checkout'          => 'Checkout',
            '/best-sellers'      => 'Best Sellers',
            '/deals'             => 'Deals',
            '/search'            => 'Search',
            '/categories'        => 'Categories',
            '/shops'             => 'Shops',
            '/products'          => 'Products',
            '/supplier-central'  => 'Supplier Central',
            '/seller-central'    => 'Seller Central',
            '/buyer-central'     => 'Buyer Central',
            '/driver-central'    => 'Driver Central',
            '/distribution'      => 'Distribution',
            '/about'             => 'About',
            '/contact'           => 'Contact',
            '/faq'               => 'FAQ',
            '/shop'              => 'Shop',
            '/product'           => 'Product',
            '/'                  => 'Home',
        ];

        // Exact match
        if (isset($titles[$url])) {
            return $titles[$url];
        }

        // Prefix match - '/' is excluded to avoid matching everything
        foreach ($titles as $pattern => $title) {
            if ($pattern === '/') continue;
            if (strpos($url, $pattern . '/') === 0 || strpos($url, $pattern . '?') === 0) {
                return $title;
            }
        }

        // Fall back to first URL segment, humanized
        $parts = explode('/', trim($url, '/'));
        if (!empty($parts[0])) {
            return ucwords(str_replace(['-', '_'], ' ', $parts[0]));
        }

        return 'Unknown Page';
    }
    
    /**
     * Get real IP address
     */
    private static function getIpAddress(): string {
        $headers = [
            'HTTP_CF_CONNECTING_IP', // Cloudflare
            'HTTP_X_FORWARDED_FOR',  // Proxy
            'HTTP_X_REAL_IP',        // Nginx
            'REMOTE_ADDR'            // Default
        ];
        
        foreach ($headers as $header) {
            if (isset($_SERVER[$header])) {
                $ip = $_SERVER[$header];
                // Take first IP if comma-separated
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Detect device type
     */
    private static function detectDevice(string $userAgent): string {
        $userAgent = strtolower($userAgent);
        
        if (preg_match('/mobile|android|iphone|ipod|blackberry|opera mini/i', $userAgent)) {
            return 'mobile';
        }
        
        if (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }
    
    /**
     * Detect browser
     */
    private static function detectBrowser(string $userAgent): string {
        if (preg_match('/Edge/i', $userAgent)) return 'Edge';
        if (preg_match('/Chrome/i', $userAgent)) return 'Chrome';
        if (preg_match('/Safari/i', $userAgent)) return 'Safari';
        if (preg_match('/Firefox/i', $userAgent)) return 'Firefox';
        if (preg_match('/MSIE|Trident/i', $userAgent)) return 'IE';
        if (preg_match('/Opera/i', $userAgent)) return 'Opera';
        
        return 'Unknown';
    }
    
    /**
     * Detect operating system
     */
    private static function detectOS(string $userAgent): string {
        if (preg_match('/windows/i', $userAgent)) return 'Windows';
        if (preg_match('/mac/i', $userAgent)) return 'macOS';
        if (preg_match('/linux/i', $userAgent)) return 'Linux';
        if (preg_match('/android/i', $userAgent)) return 'Android';
        if (preg_match('/iphone|ipad|ipod/i', $userAgent)) return 'iOS';

        return 'Unknown';
    }

    /**
     * Get geolocation data from IP address
     * Uses ip-api.com (free, 45 requests/minute)
     */
    private static function getGeolocation(string $ipAddress): array {
        // Skip for localhost/private/reserved IPs
        if (!filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return [];
        }

        try {
            // Check cache first (store in session for 1 hour)
            $cacheKey = 'geo_' . md5($ipAddress);
            if (isset($_SESSION[$cacheKey]) &&
                isset($_SESSION[$cacheKey . '_time']) &&
                time() - $_SESSION[$cacheKey . '_time'] < 3600) {
                return $_SESSION[$cacheKey];
            }

            // Query IP geolocation API
            $url = "http://ip-api.com/json/{$ipAddress}?fields=status,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,query";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            $response = curl_exec($ch);
            curl_close($ch);

            if ($response) {
                $data = json_decode($response, true);

                if ($data && isset($data['status']) && $data['status'] === 'success') {
                    $geoData = [
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                        'region' => $data['regionName'] ?? null,
                        'city' => $data['city'] ?? null,
                        'zip' => $data['zip'] ?? null,
                        'latitude' => $data['lat'] ?? null,
                        'longitude' => $data['lon'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                        'isp' => $data['isp'] ?? null,
                        'org' => $data['org'] ?? null,
                        'as' => $data['as'] ?? null,
                    ];

                    // Cache result
                    $_SESSION[$cacheKey] = $geoData;
                    $_SESSION[$cacheKey . '_time'] = time();

                    return $geoData;
                }
            }
        } catch (\Exception $e) {
            // Silently fail - don't break tracking
            error_log("Geolocation API error: " . $e->getMessage());
        }

        return [];
    }
}
