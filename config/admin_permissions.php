<?php
/**
 * Admin Permissions Configuration
 *
 * Defines access levels for different admin role tiers:
 * - super_admin (Tier 1): Full access to everything
 * - admin (Tier 2): Standard access, no system/content management
 * - admin_staff (Tier 3): Limited access for daily operations
 */

return [
    // All admin role names (in order of privilege)
    'admin_roles' => ['super_admin', 'admin', 'admin_staff'],

    // Tier levels for each role (lower = more privileges)
    'role_tiers' => [
        'super_admin' => 1,
        'admin' => 2,
        'admin_staff' => 3,
    ],

    // Menu access by role
    // Each key is a menu identifier, value is array of roles that can access it
    'menu_access' => [
        // Dashboard & Analytics
        'dashboard' => ['super_admin', 'admin', 'admin_staff'],
        'planner' => ['super_admin', 'admin'],
        'html-editor' => ['super_admin', 'admin'],
        'analytics' => ['super_admin', 'admin'],

        // User Management
        'users' => ['super_admin'],
        'sellers' => ['super_admin', 'admin', 'admin_staff'],
        'business-accounts' => ['super_admin', 'admin'],
        'distribution' => ['super_admin', 'admin'],
        'shipments' => ['super_admin', 'admin', 'admin_staff'],
        'delivery' => ['super_admin', 'admin', 'admin_staff'],
        'shops' => ['super_admin', 'admin', 'admin_staff'],
        'ocs-store' => ['super_admin', 'admin'],
        'leads' => ['super_admin', 'admin'],

        // Catalog
        'products' => ['super_admin', 'admin', 'admin_staff'],
        'stock' => ['super_admin', 'admin', 'admin_staff'],
        'suppliers' => ['super_admin', 'admin'],
        'purchase-orders' => ['super_admin', 'admin'],
        'supplier-catalog' => ['super_admin', 'admin'],
        'categories' => ['super_admin', 'admin'],
        'sales' => ['super_admin', 'admin'],

        // Sales
        'orders' => ['super_admin', 'admin', 'admin_staff'],
        'reports' => ['super_admin', 'admin'],

        // Marketing
        'promo-banners' => ['super_admin', 'admin'],
        'ads' => ['super_admin', 'admin'],
        'affiliates' => ['super_admin', 'admin'],
        'coupons' => ['super_admin', 'admin'],

        // System (Super Admin Only)
        'settings' => ['super_admin'],
        'homepage' => ['super_admin'],

        // Content Management (Super Admin Only)
        'cms' => ['super_admin'],
        'content-pages' => ['super_admin'],
        'legal' => ['super_admin'],
        'sliders' => ['super_admin'],
        'emails' => ['super_admin'],
        'translations' => ['super_admin'],

        // Notifications (all admin tiers)
        'notifications' => ['super_admin', 'admin', 'admin_staff'],
    ],

    // Route protection - routes that require specific roles
    // Key is route prefix, value is array of allowed roles
    'route_access' => [
        // System routes - super_admin only
        '/admin/settings' => ['super_admin'],
        '/admin/homepage' => ['super_admin'],

        // Content management routes - super_admin only
        '/admin/cms' => ['super_admin'],
        '/admin/content-pages' => ['super_admin'],
        '/admin/legal' => ['super_admin'],
        '/admin/sliders' => ['super_admin'],
        '/admin/emails' => ['super_admin'],
        '/admin/translations' => ['super_admin'],

        // User management - super_admin only
        '/admin/users' => ['super_admin'],

        // Business & supplier management - admin+ only
        '/admin/business-accounts' => ['super_admin', 'admin'],
        '/admin/suppliers' => ['super_admin', 'admin'],
        '/admin/purchase-orders' => ['super_admin', 'admin'],
        '/admin/supplier-catalog' => ['super_admin', 'admin'],
        '/admin/distribution' => ['super_admin', 'admin'],

        // Planner & analytics - admin+ only
        '/admin/planner' => ['super_admin', 'admin'],
        '/admin/visitor-analytics' => ['super_admin', 'admin'],

        // Marketing - admin+ only
        '/admin/promo-banners' => ['super_admin', 'admin'],
        '/admin/ads' => ['super_admin', 'admin'],
        '/admin/affiliates' => ['super_admin', 'admin'],
        '/admin/coupons' => ['super_admin', 'admin'],

        // Categories & sales management - admin+ only
        '/admin/categories' => ['super_admin', 'admin'],
        '/admin/sales' => ['super_admin', 'admin'],
        '/admin/reports' => ['super_admin', 'admin'],

        // OCS Store - admin+ only
        '/admin/ocs-store' => ['super_admin', 'admin'],

        // Leads CRM - admin+ only
        '/admin/leads' => ['super_admin', 'admin'],
    ],

    // View-only restrictions for admin_staff
    // These routes allow viewing but not editing for admin_staff
    'view_only' => [
        'admin_staff' => [
            '/admin/sellers',
            '/admin/shops',
        ],
    ],

    // Actions that admin_staff cannot perform (even on accessible routes)
    'restricted_actions' => [
        'admin_staff' => [
            'delete',
            'suspend',
            'approve',
            'reject',
            'create',  // Can only view and update status
        ],
    ],

    // Menu section labels (for grouping in sidebar)
    'menu_sections' => [
        'main' => [
            'label' => '',
            'items' => ['dashboard', 'planner'],
        ],
        'users_accounts' => [
            'label' => 'Users & Accounts',
            'items' => ['users', 'sellers', 'business-accounts', 'shops'],
        ],
        'operations' => [
            'label' => 'Operations',
            'items' => ['orders', 'shipments', 'delivery', 'distribution'],
        ],
        'catalog' => [
            'label' => 'Catalog',
            'items' => ['products', 'stock', 'categories', 'ocs-store'],
        ],
        'procurement' => [
            'label' => 'Procurement',
            'items' => ['suppliers', 'purchase-orders', 'supplier-catalog'],
        ],
        'sales_marketing' => [
            'label' => 'Sales & Marketing',
            'items' => ['leads', 'sales', 'promo-banners', 'ads', 'affiliates', 'coupons'],
        ],
        'reports_analytics' => [
            'label' => 'Reports & Analytics',
            'items' => ['analytics', 'reports'],
        ],
        'content' => [
            'label' => 'Content Management',
            'items' => ['homepage', 'cms', 'content-pages', 'legal', 'sliders', 'emails', 'translations'],
        ],
        'system' => [
            'label' => 'System',
            'items' => ['settings'],
        ],
    ],
];
