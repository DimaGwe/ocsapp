<?php
/**
 * Admin Permission Helper
 *
 * Provides helper functions for checking admin role permissions
 * and access levels throughout the application.
 */

class AdminPermissionHelper
{
    private static ?array $config = null;

    /**
     * Load and cache the permissions config
     */
    private static function getConfig(): array
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/admin_permissions.php';
        }
        return self::$config;
    }

    /**
     * Check if a role is any admin tier
     */
    public static function isAdminRole(?string $role): bool
    {
        if (!$role) {
            return false;
        }
        $config = self::getConfig();
        return in_array($role, $config['admin_roles'], true);
    }

    /**
     * Get the tier level for a role (1 = highest, 3 = lowest)
     * Returns null if not an admin role
     */
    public static function getAdminTier(?string $role): ?int
    {
        if (!$role) {
            return null;
        }
        $config = self::getConfig();
        return $config['role_tiers'][$role] ?? null;
    }

    /**
     * Check if a role can access a specific menu item
     */
    public static function canAccessMenu(string $menuKey, ?string $role): bool
    {
        if (!$role) {
            return false;
        }
        $config = self::getConfig();
        $allowedRoles = $config['menu_access'][$menuKey] ?? [];
        return in_array($role, $allowedRoles, true);
    }

    /**
     * Check if a role can access a specific route
     */
    public static function canAccessRoute(string $route, ?string $role): bool
    {
        if (!$role) {
            return false;
        }

        // Super admin can access everything
        if ($role === 'super_admin') {
            return true;
        }

        $config = self::getConfig();

        // Check each protected route prefix
        foreach ($config['route_access'] as $routePrefix => $allowedRoles) {
            if (strpos($route, $routePrefix) === 0) {
                return in_array($role, $allowedRoles, true);
            }
        }

        // If route not specifically protected, allow all admin roles
        return self::isAdminRole($role);
    }

    /**
     * Check if a role has view-only access to a route
     */
    public static function isViewOnly(string $route, ?string $role): bool
    {
        if (!$role) {
            return true;
        }

        $config = self::getConfig();
        $viewOnlyRoutes = $config['view_only'][$role] ?? [];

        foreach ($viewOnlyRoutes as $routePrefix) {
            if (strpos($route, $routePrefix) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if an action is restricted for a role
     */
    public static function isActionRestricted(string $action, ?string $role): bool
    {
        if (!$role) {
            return true;
        }

        // Super admin has no restrictions
        if ($role === 'super_admin') {
            return false;
        }

        $config = self::getConfig();
        $restrictedActions = $config['restricted_actions'][$role] ?? [];

        return in_array($action, $restrictedActions, true);
    }

    /**
     * Get current user's department from session
     */
    public static function getCurrentDept(): ?string
    {
        return $_SESSION['user']['department'] ?? null;
    }

    /**
     * Check if a department allows access to a menu key.
     * Returns true when: no department set, role is not admin_staff,
     * department is 'management', or the menu key is in the department's list.
     */
    public static function canAccessMenuForDept(string $menuKey, ?string $role, ?string $dept): bool
    {
        // Only apply department filter to admin_staff with a department set
        if ($role !== 'admin_staff' || !$dept) {
            return true;
        }

        $config = self::getConfig();
        $deptAccess = $config['department_menu_access'][$dept] ?? null;

        // Unknown department or management = no extra restriction
        if ($deptAccess === null || $dept === 'management') {
            return true;
        }

        return in_array($menuKey, $deptAccess, true);
    }

    /**
     * Get all accessible menu items for a role
     */
    public static function getAccessibleMenus(?string $role): array
    {
        if (!$role) {
            return [];
        }

        $config = self::getConfig();
        $accessible = [];

        foreach ($config['menu_access'] as $menuKey => $allowedRoles) {
            if (in_array($role, $allowedRoles, true)) {
                $accessible[] = $menuKey;
            }
        }

        return $accessible;
    }

    /**
     * Check if user has minimum required tier
     * Tier 1 = super_admin (highest), Tier 3 = admin_staff (lowest)
     */
    public static function hasMinimumTier(?string $role, int $requiredTier): bool
    {
        $userTier = self::getAdminTier($role);
        if ($userTier === null) {
            return false;
        }
        // Lower number = higher privilege
        return $userTier <= $requiredTier;
    }

    /**
     * Check if current user is super admin
     */
    public static function isSuperAdmin(): bool
    {
        $role = $_SESSION['user']['role'] ?? null;
        return $role === 'super_admin';
    }

    /**
     * Check if current user is any admin tier
     */
    public static function isAdmin(): bool
    {
        $role = $_SESSION['user']['role'] ?? null;
        return self::isAdminRole($role);
    }

    /**
     * Get current user's role
     */
    public static function getCurrentRole(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    /**
     * Get current user's admin tier
     */
    public static function getCurrentTier(): ?int
    {
        return self::getAdminTier(self::getCurrentRole());
    }

    /**
     * Check if current user can access a menu item (role + department combined)
     */
    public static function currentUserCanAccessMenu(string $menuKey): bool
    {
        $role = self::getCurrentRole();
        return self::canAccessMenu($menuKey, $role)
            && self::canAccessMenuForDept($menuKey, $role, self::getCurrentDept());
    }

    /**
     * Check if current user can access a route
     */
    public static function currentUserCanAccessRoute(string $route): bool
    {
        return self::canAccessRoute($route, self::getCurrentRole());
    }

    /**
     * Get role display name
     */
    public static function getRoleDisplayName(?string $role): string
    {
        $names = [
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'admin_staff' => 'Admin Staff',
            'seller' => 'Seller',
            'buyer' => 'Buyer',
            'delivery' => 'Delivery Staff',
        ];

        return $names[$role] ?? ucfirst($role ?? 'Unknown');
    }

    /**
     * Get badge color for role
     */
    public static function getRoleBadgeClass(?string $role): string
    {
        $classes = [
            'super_admin' => 'badge-danger',      // Red for super admin
            'admin' => 'badge-primary',           // Blue for admin
            'admin_staff' => 'badge-secondary',   // Gray for staff
            'seller' => 'badge-success',          // Green for seller
            'buyer' => 'badge-info',              // Cyan for buyer
            'delivery' => 'badge-warning',        // Yellow for delivery
        ];

        return $classes[$role] ?? 'badge-secondary';
    }
}

// Shorthand functions for common checks
if (!function_exists('isSuperAdmin')) {
    function isSuperAdmin(): bool
    {
        return AdminPermissionHelper::isSuperAdmin();
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return AdminPermissionHelper::isAdmin();
    }
}

if (!function_exists('canAccessMenu')) {
    function canAccessMenu(string $menuKey): bool
    {
        return AdminPermissionHelper::currentUserCanAccessMenu($menuKey);
    }
}

if (!function_exists('canAccessRoute')) {
    function canAccessRoute(string $route): bool
    {
        return AdminPermissionHelper::currentUserCanAccessRoute($route);
    }
}

if (!function_exists('getAdminTier')) {
    function getAdminTier(): ?int
    {
        return AdminPermissionHelper::getCurrentTier();
    }
}
