<?php

namespace App\Middlewares;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

class AuthMiddleware {

    /**
     * Check if user is authenticated and optionally has required role
     */
    public static function handle(string $requiredRole = null): void {
        // Check if user is logged in
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to continue');
            redirect(url('login'));
        }

        // Check role if specified
        if ($requiredRole !== null) {
            $userRole = userRole();

            // Super admin can access everything
            if ($userRole === 'super_admin') {
                return;
            }

            // If requiring 'admin' access, allow any admin tier
            if ($requiredRole === 'admin' && \AdminPermissionHelper::isAdminRole($userRole)) {
                return;
            }

            // Check if user has required role
            if ($userRole !== $requiredRole) {
                setFlash('error', 'You do not have permission to access this page');

                // Redirect to appropriate dashboard
                redirect(self::getDashboardUrl($userRole));
            }
        }
    }

    /**
     * Alias for handle() - just checks authentication
     */
    public static function auth(): void {
        self::handle();
    }

    /**
     * Check if user has one of the allowed roles
     *
     * @param array $allowedRoles Array of role names that are allowed
     */
    public static function role(array $allowedRoles): void {
        // First ensure user is authenticated
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to continue');
            redirect(url('login'));
        }

        $userRole = userRole();

        // Super admin can access everything
        if ($userRole === 'super_admin') {
            return;
        }

        // Check if user's role is in allowed roles
        if (!in_array($userRole, $allowedRoles)) {
            setFlash('error', 'You do not have permission to access this page');
            redirect(self::getDashboardUrl($userRole));
        }
    }

    /**
     * Require minimum admin tier level
     * Tier 1 = super_admin (highest), Tier 2 = admin, Tier 3 = admin_staff (lowest)
     *
     * @param int $minTier Minimum tier required (1, 2, or 3)
     */
    public static function adminTier(int $minTier = 3): void {
        // First ensure user is authenticated
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to continue');
            redirect(url('login'));
        }

        $userRole = userRole();

        // Check if user is an admin tier at all
        if (!\AdminPermissionHelper::isAdminRole($userRole)) {
            setFlash('error', 'You do not have permission to access this page');
            redirect(self::getDashboardUrl($userRole));
        }

        // Check if user has minimum required tier
        if (!\AdminPermissionHelper::hasMinimumTier($userRole, $minTier)) {
            setFlash('error', 'You do not have permission to access this page');
            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Require super admin access only
     */
    public static function superAdmin(): void {
        self::adminTier(1);
    }

    /**
     * Check if current route is accessible based on permissions config
     */
    public static function checkRouteAccess(): void {
        // First ensure user is authenticated
        if (!isLoggedIn()) {
            setFlash('error', 'Please login to continue');
            redirect(url('login'));
        }

        $userRole = userRole();
        $currentRoute = $_SERVER['REQUEST_URI'] ?? '';

        // Parse the route (remove query string)
        $route = parse_url($currentRoute, PHP_URL_PATH);

        // Check route access
        if (!\AdminPermissionHelper::canAccessRoute($route, $userRole)) {
            setFlash('error', 'You do not have permission to access this page');

            // Log unauthorized access attempt
            self::logUnauthorizedAccess($route, $userRole);

            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Log unauthorized access attempts for security monitoring
     */
    private static function logUnauthorizedAccess(string $route, string $role): void {
        try {
            $db = \Database::getConnection();
            $userId = userId();

            // Log to audit_logs table
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, entity_type, ip_address, user_agent, old_values, created_at)
                VALUES (?, 'unauthorized_access', 'route', ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                json_encode(['route' => $route, 'role' => $role])
            ]);
        } catch (\PDOException $e) {
            logger("Failed to log unauthorized access: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Ensure user is NOT logged in (for login/register pages)
     */
    public static function guest(): void {
        if (isLoggedIn()) {
            $role = userRole();
            redirect(self::getDashboardUrl($role));
        }
    }

    /**
     * Get the dashboard URL for a specific role
     */
    private static function getDashboardUrl(string $role): string {
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

    /**
     * Check rate limiting for login attempts
     */
    public static function checkRateLimitLogin(string $email): bool {
        $maxAttempts = 5;
        $lockoutMinutes = 15;
        
        try {
            $db = \Database::getConnection();
            
            // Clean old attempts
            $cutoff = date('Y-m-d H:i:s', strtotime("-{$lockoutMinutes} minutes"));
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE attempted_at < ?");
            $stmt->execute([$cutoff]);
            
            // Count recent attempts
            $stmt = $db->prepare("
                SELECT COUNT(*) as count 
                FROM login_attempts 
                WHERE email = ? 
                AND attempted_at >= ?
            ");
            $stmt->execute([$email, $cutoff]);
            $result = $stmt->fetch();
            
            return $result['count'] < $maxAttempts;
            
        } catch (\PDOException $e) {
            logger("Rate limit check error: " . $e->getMessage(), 'error');
            return true; // Fail open
        }
    }

    /**
     * Log a failed login attempt
     */
    public static function logLoginAttempt(string $email): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO login_attempts (email, ip_address) 
                VALUES (?, ?)
            ");
            $stmt->execute([$email, $_SERVER['REMOTE_ADDR'] ?? 'unknown']);
        } catch (\PDOException $e) {
            logger("Failed to log login attempt: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Clear login attempts for an email
     */
    public static function clearLoginAttempts(string $email): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE email = ?");
            $stmt->execute([$email]);
        } catch (\PDOException $e) {
            logger("Failed to clear login attempts: " . $e->getMessage(), 'error');
        }
    }
}