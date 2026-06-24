<?php
/**
 * Bootstrap Initialization
 * This file loads all necessary configurations and helper functions
 */

// Start session with secure configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.gc_maxlifetime', '1800');
    ini_set('session.cookie_lifetime', '0');
    ini_set('session.sid_length', '48');
    ini_set('session.sid_bits_per_character', '6');

    // Enable secure cookies when using HTTPS
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', '1');
    }

    session_start();

    // Session timeout (30 minutes of inactivity)
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['LAST_ACTIVITY'] = time();

    // Initialize default language for new sessions (FR required by law in QC)
    if (!isset($_SESSION['language'])) {
        $_SESSION['language'] = 'fr';
    }

    // Allow ?lang= URL param to override session language (used in email links)
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'fr'], true)) {
        $_SESSION['language'] = $_GET['lang'];
    }

    // Regenerate session ID periodically (every 15 minutes)
    if (!isset($_SESSION['CREATED'])) {
        $_SESSION['CREATED'] = time();
    } elseif (time() - $_SESSION['CREATED'] > 900) {
        session_regenerate_id(true);
        $_SESSION['CREATED'] = time();
    }
}

// Load environment variables from .env file
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    foreach ($env as $key => $value) {
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Environment helper function
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        // Convert boolean strings
        if (in_array(strtolower($value), ['true', '1', 'yes', 'on'])) {
            return true;
        }
        if (in_array(strtolower($value), ['false', '0', 'no', 'off', ''])) {
            return false;
        }

        return $value;
    }
}

// Branding helper function
if (!function_exists('brand')) {
    function brand($key = null, $default = '') {
        static $branding = null;

        if ($branding === null) {
            $branding = require dirname(__DIR__) . '/config/branding.php';
        }

        if ($key === null) {
            return $branding;
        }

        return $branding[$key] ?? $default;
    }
}

// URL helper function
if (!function_exists('url')) {
    function url($path = '') {
        $baseUrl = env('APP_URL', '');
        $basePath = env('BASE_PATH', '');

        // If APP_URL is not set or empty, auto-detect from server
        if (empty($baseUrl)) {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = $scheme . '://' . $host;
        }

        // Ensure baseUrl has a scheme (handle edge cases)
        if (!preg_match('/^https?:\/\//', $baseUrl)) {
            $baseUrl = 'http://' . ltrim($baseUrl, '/');
        }

        // Clean up path
        $path = ltrim($path, '/');

        // Build and return full URL
        $fullUrl = rtrim($baseUrl . $basePath, '/') . '/' . $path;

        return $fullUrl;
    }
}

// Asset URL helper
if (!function_exists('asset')) {
    function asset($path) {
        return url('assets/' . ltrim($path, '/'));
    }
}

// Request helpers
if (!function_exists('isPost')) {
    function isPost(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
}

if (!function_exists('isGet')) {
    function isGet(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

if (!function_exists('post')) {
    function post($key, $default = null) {
        return $_POST[$key] ?? $default;
    }
}

if (!function_exists('get')) {
    function get($key, $default = null) {
        return $_GET[$key] ?? $default;
    }
}

// JSON response helper
if (!function_exists('jsonResponse')) {
    function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// CSRF Token helpers
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken($token): bool {
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('csrfMeta')) {
    function csrfMeta(): string {
        $token = generateCsrfToken();
        return '<meta name="csrf-token" content="' . htmlspecialchars($token) . '">';
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string {
        $token = generateCsrfToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }
}

/**
 * Verify CSRF token for API (JSON) requests.
 * Checks X-CSRF-TOKEN header first, then _csrf_token in request body.
 * Returns true if valid, sends 403 JSON response and exits if invalid.
 */
if (!function_exists('verifyCsrfForApi')) {
    function verifyCsrfForApi(): bool {
        // Skip CSRF check for GET/HEAD/OPTIONS requests
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'])) {
            return true;
        }

        // Check X-CSRF-TOKEN header first (sent by fetch calls)
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        // Fallback: check request body
        if (empty($token)) {
            $input = json_decode(file_get_contents('php://input'), true);
            $token = $input['_csrf_token'] ?? '';
        }

        // Fallback: check POST data
        if (empty($token)) {
            $token = $_POST[env('CSRF_TOKEN_NAME', '_csrf_token')] ?? '';
        }

        if (!empty($token) && verifyCsrfToken($token)) {
            return true;
        }

        http_response_code(403);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Invalid or missing CSRF token']);
        exit;
    }
}

// Logger helper — always writes error/warning; info/debug only when APP_DEBUG=true
if (!function_exists('logger')) {
    function logger($message, $level = 'info') {
        $alwaysLog = in_array(strtolower($level), ['error', 'warning', 'critical']);
        if ($alwaysLog || env('APP_DEBUG', false)) {
            error_log("[{$level}] {$message}");
        }
    }
}

// Set timezone to Eastern Time (UTC-5)
$appConfig = require dirname(__DIR__) . '/config/app.php';
date_default_timezone_set($appConfig['timezone'] ?? 'America/Toronto');

// Load Admin Permission Helper (role-based access control)
$adminPermissionHelper = dirname(__DIR__) . '/app/Helpers/AdminPermissionHelper.php';
if (file_exists($adminPermissionHelper)) {
    require_once $adminPermissionHelper;
}

// Load Translation Helper (database-driven translations)
$translationHelper = dirname(__DIR__) . '/app/Helpers/translation_helper.php';
if (file_exists($translationHelper)) {
    require_once $translationHelper;
}

// Load Homepage Settings Helper
$homepageHelper = dirname(__DIR__) . '/app/Helpers/homepage_helper.php';
if (file_exists($homepageHelper)) {
    require_once $homepageHelper;
}

// Account URL helper - Returns role-based dashboard URL
if (!function_exists('accountUrl')) {
    function accountUrl(): string {
        if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
            return url('login');
        }

        $role = $_SESSION['user']['role'] ?? 'buyer';

        switch ($role) {
            case 'super_admin':
            case 'admin':
            case 'admin_staff':
                return url('admin/dashboard');
            case 'seller':
                return url('seller/dashboard');
            case 'delivery':
                return url('delivery/dashboard');
            case 'buyer':
            default:
                return url('account');
        }
    }
}

// Currency formatter helper
if (!function_exists('currency')) {
    function currency($amount, $currency = 'CAD'): string {
        $amount = (float) $amount;

        // Format based on currency
        switch ($currency) {
            case 'USD':
                return '$' . number_format($amount, 2);
            case 'CAD':
            default:
                return '$' . number_format($amount, 2) . ' CAD';
        }
    }
}

// Currency symbol helper
if (!function_exists('currencySymbol')) {
    function currencySymbol(string $currency = 'CAD'): string {
        switch ($currency) {
            case 'USD':
            case 'CAD':
            default:
                return '$';
        }
    }
}

// Flash message helpers
if (!function_exists('setFlash')) {
    function setFlash(string $type, string $message): void {
        $_SESSION['flash'][$type] = $message;
    }
}

if (!function_exists('getFlash')) {
    function getFlash(string $type = null) {
        if ($type) {
            $message = $_SESSION['flash'][$type] ?? null;
            unset($_SESSION['flash'][$type]);
            return $message;
        }

        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}

if (!function_exists('hasFlash')) {
    function hasFlash(string $type = null): bool {
        if ($type) {
            return isset($_SESSION['flash'][$type]);
        }
        return !empty($_SESSION['flash']);
    }
}

// Authentication helper
if (!function_exists('isLoggedIn')) {
    function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']) && isset($_SESSION['user']['id']);
    }
}

// Redirect helper
if (!function_exists('redirect')) {
    function redirect($url) {
        // If the URL doesn't start with http:// or https://, treat it as a relative path
        if (!preg_match('/^https?:\/\//', $url)) {
            // Convert relative URL to absolute URL using url() helper
            $url = url($url);
        }
        header("Location: " . $url);
        exit;
    }
}

// View renderer
if (!function_exists('view')) {
    function view($viewPath, $data = []) {
        extract($data);
        $viewFile = dirname(__DIR__) . '/app/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new Exception("View not found: {$viewPath}");
        }
    }
}

// Sanitize helper
if (!function_exists('sanitize')) {
    function sanitize(string $data): string {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
}

// Old input helpers (for form repopulation)
if (!function_exists('setOldInput')) {
    function setOldInput(array $data): void {
        $_SESSION['old_input'] = $data;
    }
}

if (!function_exists('old')) {
    function old(string $key, $default = ''): string {
        $value = $_SESSION['old_input'][$key] ?? $default;
        return sanitize((string)$value);
    }
}

if (!function_exists('clearOldInput')) {
    function clearOldInput(): void {
        unset($_SESSION['old_input']);
    }
}

// Back helper — validates Referer is same origin to prevent open redirect
if (!function_exists('back')) {
    function back(): void {
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        if ($referrer !== '') {
            $host = parse_url($referrer, PHP_URL_HOST);
            $ownHost = parse_url(env('APP_URL', ''), PHP_URL_HOST) ?: ($_SERVER['HTTP_HOST'] ?? '');
            if ($host === $ownHost) {
                redirect($referrer);
                return;
            }
        }
        redirect(url('/'));
    }
}

// User helper
if (!function_exists('user')) {
    function user(): ?array {
        return $_SESSION['user'] ?? null;
    }
}

// Get current user's ID
if (!function_exists('userId')) {
    function userId(): ?int {
        $user = user();
        return $user['id'] ?? null;
    }
}

// Get current user's role
if (!function_exists('userRole')) {
    function userRole(): ?string {
        $user = user();
        return $user['role'] ?? null;
    }
}

// Check if user has specific role
if (!function_exists('hasRole')) {
    function hasRole(string $role): bool {
        $user = user();
        return $user && isset($user['role']) && $user['role'] === $role;
    }
}

// Check if user is authenticated
if (!function_exists('isAuthenticated')) {
    function isAuthenticated(): bool {
        return user() !== null;
    }
}

// Validation helpers
if (!function_exists('validateRequired')) {
    function validateRequired(array $fields, array $data): array {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }
        return $errors;
    }
}

if (!function_exists('validateEmail')) {
    function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('validateLength')) {
    function validateLength(string $value, int $min, int $max = PHP_INT_MAX): bool {
        $length = strlen($value);
        return $length >= $min && $length <= $max;
    }
}

if (!function_exists('validateMatch')) {
    function validateMatch(string $value1, string $value2): bool {
        return $value1 === $value2;
    }
}

// Date formatting helper
if (!function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'M d, Y'): string {
        if (empty($date)) {
            return 'N/A';
        }
        try {
            $dateTime = new DateTime($date);
            return $dateTime->format($format);
        } catch (Exception $e) {
            return 'Invalid Date';
        }
    }
}

// DateTime formatting helper
if (!function_exists('formatDateTime')) {
    function formatDateTime(?string $date, string $format = 'M d, Y H:i'): string {
        return formatDate($date, $format);
    }
}

// Password strength validation
if (!function_exists('validatePasswordStrength')) {
    function validatePasswordStrength(string $password): array {
        $errors = [];

        if (strlen($password) < 10) {
            $errors[] = 'at least 10 characters';
        }
        if (strlen($password) > 72) {
            $errors[] = 'no more than 72 characters';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'one uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'one lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'one number';
        }
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'one special character';
        }

        $common = ['password', 'password123', '12345678', 'qwerty123', 'admin123', 'letmein', 'welcome1', '1234567890'];
        if (in_array(strtolower($password), $common, true)) {
            $errors[] = 'not be a common password';
        }

        return $errors;
    }
}

// Audit logging for sensitive operations
if (!function_exists('auditLog')) {
    function auditLog(string $action, string $details = '', ?int $targetUserId = null): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO audit_log (user_id, action, details, target_user_id, ip_address, user_agent, created_at)
                VALUES (:user_id, :action, :details, :target_user_id, :ip_address, :user_agent, NOW())
            ");
            $stmt->execute([
                'user_id' => userId(),
                'action' => $action,
                'details' => mb_substr($details, 0, 1000),
                'target_user_id' => $targetUserId,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            ]);
        } catch (\Exception $e) {
            error_log("Audit log failed: " . $e->getMessage());
        }
    }
}

// Session-based rate limiting
if (!function_exists('rateLimit')) {
    /**
     * Check and enforce rate limit using session storage.
     * @param string $key Unique key for the action (e.g., 'checkout', 'login')
     * @param int $maxAttempts Maximum attempts allowed in the window
     * @param int $windowSeconds Time window in seconds
     * @return bool True if within limit, false if rate limited
     */
    function rateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool {
        $sessionKey = "_rate_limit_{$key}";
        $now = time();

        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = [];
        }

        // Remove expired entries
        $_SESSION[$sessionKey] = array_filter($_SESSION[$sessionKey], function($ts) use ($now, $windowSeconds) {
            return ($now - $ts) < $windowSeconds;
        });

        // Check if over limit
        if (count($_SESSION[$sessionKey]) >= $maxAttempts) {
            return false;
        }

        // Record this attempt
        $_SESSION[$sessionKey][] = $now;
        return true;
    }
}
