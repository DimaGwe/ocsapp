<?php

// Load translation helper
require_once __DIR__ . '/translation_helper.php';

/**
 * Global Helper Functions
 */

// CSRF Protection — use _csrf_token as session key (consistent with env CSRF_TOKEN_NAME default)
if (!function_exists('generateCsrfToken')) {
    function generateCsrfToken(): string {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }
}

if (!function_exists('verifyCsrfToken')) {
    function verifyCsrfToken(string $token): bool {
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }
}

if (!function_exists('csrfField')) {
    function csrfField(): string {
        $token = generateCsrfToken();
        $name = env('CSRF_TOKEN_NAME', '_csrf_token');
        return "<input type='hidden' name='{$name}' value='" . htmlspecialchars($token) . "'>";
    }
}

if (!function_exists('csrfMeta')) {
    function csrfMeta(): string {
        $token = generateCsrfToken();
        $name = env('CSRF_TOKEN_NAME', '_csrf_token');
        return "<meta name='csrf-token' content='" . htmlspecialchars($token) . "' data-name='{$name}'>";
    }
}

if (!function_exists('csrfToken')) {
    function csrfToken(): string {
        return generateCsrfToken();
    }
}

/**
 * Resolve a configuration value DB-first, .env-fallback.
 * Reads the `settings` table (key/value), and if empty falls back to the
 * matching uppercased .env variable. Lets admins manage API keys from the
 * System > Integrations UI without touching .env, while keeping .env working.
 * Result is cached per-request.
 */
if (!function_exists('setting')) {
    function setting(string $key, $default = null) {
        static $cache = [];
        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }
        $val = null;
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("SELECT `value` FROM settings WHERE `key` = ? LIMIT 1");
            $stmt->execute([$key]);
            $row = $stmt->fetchColumn();
            if ($row !== false && $row !== null && $row !== '') {
                $val = $row;
            }
        } catch (\Throwable $e) {
            // settings table may not exist yet - fall through to .env
        }
        if ($val === null || $val === '') {
            $envVal = env(strtoupper($key), '');
            if ($envVal !== '' && $envVal !== null) {
                $val = $envVal;
            }
        }
        $cache[$key] = ($val === null || $val === '') ? $default : $val;
        return $cache[$key];
    }
}

// Request Helpers
function request(string $key = null, $default = null) {
    if ($key === null) {
        return $_REQUEST;
    }
    return $_REQUEST[$key] ?? $default;
}

function post(string $key = null, $default = null) {
    if ($key === null) {
        return $_POST;
    }
    return $_POST[$key] ?? $default;
}

function get(string $key = null, $default = null) {
    if ($key === null) {
        return $_GET;
    }
    return $_GET[$key] ?? $default;
}

function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function isGet(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

// Validation & Sanitization
function sanitize(string $data): string {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateRequired(array $fields, array $data): array {
    $errors = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[$field] = ucfirst($field) . " is required";
        }
    }
    return $errors;
}

// Response Helpers
function redirect(string $path, int $statusCode = 302): void {
    // If the path doesn't start with http:// or https://, convert it to absolute URL
    if (!preg_match('/^https?:\/\//', $path)) {
        $path = url($path);
    }
    header("Location: {$path}", true, $statusCode);
    exit;
}

function back(): void {
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    // Only redirect to same-origin referers to prevent open redirect attacks
    $appHost = parse_url(env('APP_URL', 'https://ocsapp.ca'), PHP_URL_HOST);
    $refHost = $referer ? parse_url($referer, PHP_URL_HOST) : '';
    if ($referer && $refHost === $appHost) {
        redirect($referer);
    } else {
        redirect(url('/'));
    }
}

function jsonResponse(array $data, int $statusCode = 200): void {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// URL Helpers
function url(string $path = ''): string {
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
    return rtrim($baseUrl . $basePath, '/') . '/' . $path;
}

function asset(string $path): string {
    $path = ltrim($path, '/');

    // If path starts with 'uploads/', it's a user-uploaded file
    // These are stored in public/uploads/, accessible without 'public/' in URL
    if (strpos($path, 'uploads/') === 0) {
        return url($path);
    }

    // Default: prepend 'assets/' for static assets (CSS, JS, static images)
    return url('assets/' . $path);
}

/**
 * Get the account/dashboard URL based on user role
 *
 * @return string The appropriate dashboard URL for the logged-in user
 */
function accountUrl(): string {
    if (!isLoggedIn()) {
        return url('login');
    }

    $role = userRole();

    $dashboards = [
        'super_admin' => 'admin/dashboard',
        'admin' => 'admin/dashboard',
        'admin_staff' => 'admin/dashboard',
        'seller' => 'seller/dashboard',
        'buyer' => 'account',
        'delivery' => 'delivery/dashboard',
        'advertiser' => 'advertiser/dashboard',
        'affiliate' => 'affiliate/dashboard',
    ];

    return url($dashboards[$role] ?? 'account');
}

// View Helpers
function view(string $view, array $data = []): void {
    extract($data);
    $viewPath = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';

    if (!file_exists($viewPath)) {
        throw new Exception("View not found: {$view}");
    }

    require $viewPath;
}

function renderLayout(string $content, array $data = []): string {
    ob_start();
    extract($data);
    echo $content;
    return ob_get_clean();
}

// Flash Messages
function setFlash(string $key, string $message): void {
    $_SESSION['flash'][$key] = $message;
}

function getFlash(string $key): ?string {
    $message = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $message;
}

function hasFlash(string $key): bool {
    return isset($_SESSION['flash'][$key]);
}

// Old Input (for form repopulation)
function setOldInput(array $data): void {
    $_SESSION['old_input'] = $data;
}

function old(string $key, $default = ''): string {
    $value = $_SESSION['old_input'][$key] ?? $default;
    return sanitize((string)$value);
}

function clearOldInput(): void {
    unset($_SESSION['old_input']);
}

// Translation
function __(string $key, string $locale = null): string {
    static $translations = [];

    $locale = $locale ?? env('APP_LOCALE', 'en');

    if (!isset($translations[$locale])) {
        $langFile = dirname(dirname(__DIR__)) . "/lang/{$locale}.php";
        $translations[$locale] = file_exists($langFile) ? require $langFile : [];
    }

    return $translations[$locale][$key] ?? $key;
}

// Debug Helper
function dd(...$vars): void {
    echo '<pre>';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    die();
}

function logger(string $message, string $level = 'info'): void {
    $logFile = dirname(dirname(__DIR__)) . '/storage/logs/' . date('Y-m-d') . '.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Auth Helpers
function auth(): ?array {
    return $_SESSION['user'] ?? null;
}

function user(): ?array {
    return auth();
}

function userId(): ?int {
    return auth()['id'] ?? null;
}

function userRole(): ?string {
    return auth()['role'] ?? null;
}

function isLoggedIn(): bool {
    if (!isset($_SESSION['user']['id'])) {
        return false;
    }
    // Verify the user record still exists — kicks deleted/suspended accounts
    static $__loginChecked = false;
    if (!$__loginChecked) {
        $__loginChecked = true;
        try {
            $__stmt = \Database::getConnection()->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $__stmt->execute([(int)$_SESSION['user']['id']]);
            if (!$__stmt->fetch()) {
                session_unset();
                session_destroy();
                return false;
            }
        } catch (\Throwable $e) {}
    }
    return true;
}

function isGuest(): bool {
    return !isLoggedIn();
}

/**
 * Check if logged-in user has a specific role
 *
 * @param string $role The role to check (e.g., 'admin', 'seller', 'buyer')
 * @return bool True if user has the role, false otherwise
 */
function hasRole(string $role): bool {
    return isLoggedIn() && userRole() === $role;
}

/**
 * Check if logged-in user has any of the specified roles
 *
 * @param array $roles Array of roles to check
 * @return bool True if user has any of the roles, false otherwise
 */
function hasAnyRole(array $roles): bool {
    if (!isLoggedIn()) {
        return false;
    }

    $userRole = userRole();
    return in_array($userRole, $roles);
}

// Date/Time Helpers
function formatDate($date, string $format = 'Y-m-d H:i:s'): string {
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    return $date->format($format);
}

function now(string $format = 'Y-m-d H:i:s'): string {
    return date($format);
}

// Currency Helper
function currency(float $amount, string $currency = null): string {
    $currency = $currency ?? env('APP_CURRENCY', 'CAD');

    $symbols = [
        'CAD' => '$',
        'USD' => 'US$',
        'DOP' => 'RD$',
        'EUR' => '€',
    ];

    $symbol = $symbols[$currency] ?? $currency;
    return $symbol . number_format($amount, 2);
}

// Get currency symbol only
function currencySymbol(string $currency = null): string {
    $currency = $currency ?? env('APP_CURRENCY', 'CAD');

    $symbols = [
        'CAD' => '$',
        'USD' => 'US$',
        'DOP' => 'RD$',
        'EUR' => '€',
    ];

    return $symbols[$currency] ?? $currency;
}

/**
 * Generate a URL-friendly slug from a string
 *
 * @param string $text The text to slugify
 * @param string $separator The separator to use (default: '-')
 * @return string The slugified string
 */
function generateSlug(string $text, string $separator = '-'): string {
    // Convert to lowercase
    $text = strtolower($text);

    // Replace non-alphanumeric characters with separator
    $text = preg_replace('/[^a-z0-9]+/i', $separator, $text);

    // Remove separator from start and end
    $text = trim($text, $separator);

    // Replace multiple separators with single separator
    $text = preg_replace('/' . preg_quote($separator) . '+/', $separator, $text);

    return $text;
}

/**
 * Generate a unique slug for a database table
 *
 * @param string $text The text to slugify
 * @param string $table The database table name
 * @param string $column The column name for the slug (default: 'slug')
 * @param int|null $ignoreId Optional ID to ignore when checking uniqueness (for updates)
 * @return string The unique slug
 */
function generateUniqueSlug(string $text, string $table, string $column = 'slug', ?int $ignoreId = null): string {
    $slug = generateSlug($text);

    // If slug is empty, generate a random one
    if (empty($slug)) {
        $slug = 'item-' . time() . '-' . rand(1000, 9999);
    }

    $db = \Database::getConnection();
    $originalSlug = $slug;
    $counter = 1;

    while (true) {
        // Check if slug exists
        $query = "SELECT COUNT(*) as count FROM {$table} WHERE {$column} = ?";
        $params = [$slug];

        if ($ignoreId !== null) {
            $query .= " AND id != ?";
            $params[] = $ignoreId;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch();

        if ($result['count'] == 0) {
            return $slug;
        }

        // Slug exists, append counter
        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }
}

/**
 * Convert PDO exception to friendly error message
 *
 * @param PDOException $e The PDO exception
 * @param string $context Optional context (e.g., 'product', 'category', 'user')
 * @return string Friendly error message
 */
function getFriendlyDatabaseError(\PDOException $e, string $context = 'item'): string {
    $errorMessage = $e->getMessage();

    // Duplicate entry errors
    if (strpos($errorMessage, 'Duplicate entry') !== false) {
        // Extract the field name from error message
        preg_match("/for key '([^']+)'/", $errorMessage, $matches);
        $field = $matches[1] ?? 'unknown field';

        // Extract the duplicate value
        preg_match("/Duplicate entry '([^']+)'/", $errorMessage, $valueMatches);
        $value = $valueMatches[1] ?? '';

        // Make field name more readable
        $fieldName = str_replace('_', ' ', $field);
        $fieldName = ucfirst($fieldName);

        if ($field === 'slug') {
            return empty($value)
                ? "This URL slug already exists. Please use a different one."
                : "The URL slug '$value' is already taken. Please use a different slug.";
        }

        if ($field === 'sku') {
            return empty($value)
                ? "SKU conflict: Another {$context} already has an empty SKU. Please enter a unique SKU or leave it blank for auto-generation."
                : "SKU '$value' is already in use. Please enter a different SKU.";
        }

        if ($field === 'email') {
            return "The email address '$value' is already registered. Please use a different email.";
        }

        if ($field === 'name' || $field === 'username') {
            return empty($value)
                ? "This {$fieldName} already exists."
                : "'{$value}' already exists. Please choose a different {$fieldName}.";
        }

        return "A {$context} with this {$fieldName} already exists. Please use different information.";
    }

    // Foreign key constraint errors
    if (strpos($errorMessage, 'foreign key constraint') !== false || strpos($errorMessage, 'FOREIGN KEY') !== false) {
        if (strpos($errorMessage, 'Cannot delete') !== false || strpos($errorMessage, 'Cannot truncate') !== false) {
            return "Cannot delete this {$context} because it's being used elsewhere. Please remove related items first.";
        }
        return "Invalid selection. The selected option no longer exists. Please refresh and try again.";
    }

    // Data too long errors
    if (strpos($errorMessage, 'Data too long') !== false) {
        preg_match("/for column '([^']+)'/", $errorMessage, $matches);
        $field = $matches[1] ?? 'field';
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        return "{$fieldName} is too long. Please shorten your text and try again.";
    }

    // Out of range errors
    if (strpos($errorMessage, 'Out of range') !== false) {
        return "One of your numbers is too large. Please enter a reasonable value.";
    }

    // Cannot be null errors
    if (strpos($errorMessage, 'cannot be null') !== false) {
        preg_match("/Column '([^']+)'/", $errorMessage, $matches);
        $field = $matches[1] ?? 'field';
        $fieldName = ucfirst(str_replace('_', ' ', $field));
        return "{$fieldName} is required. Please fill in this field.";
    }

    // Incorrect value errors
    if (strpos($errorMessage, 'Incorrect') !== false) {
        return "Invalid data format. Please check your input and try again.";
    }

    // Connection errors
    if (strpos($errorMessage, 'Connection') !== false || strpos($errorMessage, 'SQLSTATE[HY000]') !== false) {
        return "Database connection error. Please try again in a moment.";
    }

    // Default fallback
    return "An error occurred while saving. Please try again or contact support if the problem persists.";
}

/**
 * Log a supplier audit trail entry
 */
function supplierAuditLog(int $supplierId, string $action, string $details = '', ?int $performedBy = null): void {
    try {
        $db = \Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO supplier_audit_log (supplier_id, action, details, performed_by, ip_address, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $supplierId,
            $action,
            mb_substr($details, 0, 1000),
            $performedBy ?? userId(),
            $_SERVER['REMOTE_ADDR'] ?? null
        ]);
    } catch (\Exception $e) {
        logger("Supplier audit log error: " . $e->getMessage(), 'error');
    }
}

/**
 * Get the next available backup supplier product for a given supplier_product_id.
 *
 * Escalation order:
 *   1. Explicit backups in supplier_product_alternatives, ordered by priority ASC.
 *      Skips any that are unavailable (is_available=0) or whose supplier is inactive.
 *   2. Auto-discovered: other supplier_products sharing the same marketplace_product_id,
 *      ordered by unit_price ASC (cheapest first).
 *
 * @param int   $supplierProductId   The primary supplier_product.id that could not fulfill.
 * @param array $excludeIds          supplier_product IDs already tried (to avoid loops).
 * @return array|null  Full supplier_products row + supplier info, or null if none found.
 */
function getBackupSupplierProduct(int $supplierProductId, array $excludeIds = []): ?array {
    try {
        $db = \Database::getConnection();

        // Always exclude the primary itself
        $excludeIds[] = $supplierProductId;
        $excludeIds   = array_unique(array_map('intval', $excludeIds));

        $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));

        // 1. Explicit backups
        $stmt = $db->prepare("
            SELECT sp.*, s.name as supplier_name, s.company_name as supplier_company,
                   s.email as supplier_email, spa.priority
            FROM supplier_product_alternatives spa
            JOIN supplier_products sp ON spa.alternative_supplier_product_id = sp.id
            JOIN suppliers s ON sp.supplier_id = s.id
            WHERE spa.supplier_product_id = ?
            AND sp.id NOT IN ($placeholders)
            AND sp.is_available = 1
            AND s.status = 'active'
            ORDER BY spa.priority ASC
            LIMIT 1
        ");
        $params = array_merge([$supplierProductId], $excludeIds);
        $stmt->execute($params);
        $backup = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($backup) return $backup;

        // 2. Auto-discover via marketplace_product_id
        $stmt = $db->prepare("
            SELECT marketplace_product_id FROM supplier_products WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$supplierProductId]);
        $marketplaceId = $stmt->fetchColumn();

        if (!$marketplaceId) return null;

        $stmt = $db->prepare("
            SELECT sp.*, s.name as supplier_name, s.company_name as supplier_company,
                   s.email as supplier_email
            FROM supplier_products sp
            JOIN suppliers s ON sp.supplier_id = s.id
            WHERE sp.marketplace_product_id = ?
            AND sp.id NOT IN ($placeholders)
            AND sp.is_available = 1
            AND s.status = 'active'
            ORDER BY sp.unit_price ASC
            LIMIT 1
        ");
        $params = array_merge([$marketplaceId], $excludeIds);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;

    } catch (\Exception $e) {
        logger("getBackupSupplierProduct error: " . $e->getMessage(), 'error');
        return null;
    }
}
