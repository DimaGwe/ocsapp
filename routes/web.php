<?php

/**
 * Web Routes - CLEANED (No Duplicates)
 */

return [
    // ===================================
    // PUBLIC ROUTES (Customer-facing)
    // ===================================
    
    // Home
    'GET /' => ['HomeController', 'index'],

    // SEO - Sitemap & Robots
    'GET /sitemap.xml' => ['SeoController', 'sitemap'],
    'GET /robots.txt' => ['SeoController', 'robots'],

    // Product Pages
    'GET /product/{slug}' => ['ProductController', 'show'],
    
    // Search & Browse
    'GET /search' => ['SearchController', 'index'],
    'GET /categories' => ['PublicCategoryController', 'index'],
    'GET /category/{slug}' => ['PublicCategoryController', 'show'],
    'GET /best-sellers' => ['HomeController', 'bestSellers'],
    'GET /deals' => ['DealsController', 'index'],
    
    // Shops
    'GET /shops' => ['HomeController', 'shops'],
    'GET /shops/{slug}' => ['HomeController', 'shop'],
    
    // Cart
    'GET /cart' => ['CartController', 'index'],
    'GET /cart/count' => ['CartController', 'getCount'],
    'POST /cart/add' => ['CartController', 'add'],
    'POST /cart/update' => ['CartController', 'update'],
    'POST /cart/remove' => ['CartController', 'remove'],
    'POST /cart/clear' => ['CartController', 'clear'],
    
    // Checkout
    'GET /checkout' => ['CheckoutController', 'index'],
    'POST /checkout/process' => ['CheckoutController', 'process'],
    'GET /checkout/success' => ['CheckoutController', 'success'],

    // Payment (Stripe)
    'POST /payment/create-session' => ['PaymentController', 'createCheckoutSession'],
    'GET /payment/success' => ['PaymentController', 'success'],
    'GET /payment/cancel' => ['PaymentController', 'cancel'],
    'POST /payment/webhook' => ['PaymentController', 'webhook'],

    // Language & Location Settings
    'POST /set-language' => ['HomeController', 'setLanguage'],
    'POST /set-location' => ['HomeController', 'setLocation'],

    // Static Pages
    'GET /terms' => ['PageController', 'terms'],
    'GET /privacy' => ['PageController', 'privacy'],
    'GET /about' => ['PageController', 'about'],
    'GET /contact' => ['PageController', 'contact'],
    'POST /contact/submit' => ['PageController', 'submitContact'],

    // ===================================
    // AUTHENTICATION
    // ===================================
    'GET /login' => ['AuthController', 'showLogin'],
    'POST /login' => ['AuthController', 'login'],
    'GET /register' => ['AuthController', 'showRegister'],
    'POST /register' => ['AuthController', 'register'],
    'GET /logout' => ['AuthController', 'logout'],
    'GET /forgot-password' => ['AuthController', 'forgotPassword'],
    'POST /reset-password' => ['AuthController', 'resetPassword'],

    // ===================================
    // USER ACCOUNT ROUTES (SPECIFIC FIRST!)
    // ===================================
    
    // Orders (MUST come before general /account routes)
    'GET /account/orders/detail' => ['OrderController', 'orderDetail'],
    'POST /account/orders/cancel' => ['OrderController', 'cancelOrder'],
    'GET /account/orders' => ['OrderController', 'myOrders'],
    
    // Settings
    'GET /account/settings' => ['AccountController', 'settings'],
    'POST /account/settings/update-profile' => ['AccountController', 'updateProfile'],
    'POST /account/settings/update-password' => ['AccountController', 'updatePassword'],
    'POST /account/settings/update-notifications' => ['AccountController', 'updateNotifications'],
    
    // Addresses
    'GET /account/addresses' => ['AccountController', 'addresses'],
    'POST /account/addresses/add' => ['AccountController', 'addAddress'],
    'POST /account/addresses/store' => ['AccountController', 'addAddress'],
    'POST /account/addresses/update' => ['AccountController', 'updateAddress'],
    'POST /account/addresses/delete' => ['AccountController', 'deleteAddress'],
    'POST /account/addresses/set-default' => ['AccountController', 'setDefaultAddress'],
    
    // Wishlist
    'GET /account/wishlist' => ['AccountController', 'wishlist'],
    'POST /account/wishlist/add' => ['AccountController', 'addToWishlist'],
    'POST /account/wishlist/remove' => ['AccountController', 'removeFromWishlist'],
    'POST /api/wishlist/toggle' => ['WishlistController', 'toggle'],
    
    // Dashboard (LAST)
    'GET /account/dashboard' => ['AccountController', 'index'],
    'GET /account' => ['AccountController', 'index'],

    // ===================================
    // ADMIN ROUTES
    // ===================================
    'GET /admin/dashboard' => ['AdminController', 'dashboard'],
    
    // Users Management
    'GET /admin/users' => ['AdminController', 'users'],
    'GET /admin/users/edit' => ['AdminController', 'editUser'],
    'POST /admin/users/update' => ['AdminController', 'updateUser'],
    'POST /admin/users/delete' => ['AdminController', 'deleteUser'],

    // Sellers Management
    'GET /admin/sellers' => ['AdminSellerController', 'index'],
    'GET /admin/sellers/view' => ['AdminSellerController', 'view'],
    'GET /admin/sellers/edit' => ['AdminController', 'editSeller'],
    'POST /admin/sellers/update' => ['AdminController', 'updateSeller'],
    'POST /admin/sellers/suspend' => ['AdminSellerController', 'suspend'],
    'POST /admin/sellers/activate' => ['AdminSellerController', 'activate'],

    // Shops Management
    'GET /admin/shops' => ['AdminShopController', 'index'],
    'POST /admin/shops/approve' => ['AdminShopController', 'approve'],
    'POST /admin/shops/reject' => ['AdminShopController', 'reject'],
    'POST /admin/shops/activate' => ['AdminShopController', 'activate'],
    'POST /admin/shops/deactivate' => ['AdminShopController', 'deactivate'],
    'POST /admin/shops/delete' => ['AdminShopController', 'delete'],
    'POST /admin/shops/toggle-status' => ['AdminShopController', 'toggleStatus'],

    // OCS Store Management (Shop ID 1)
    'GET /admin/ocs-store/edit' => ['AdminShopController', 'editOcsStore'],
    'POST /admin/ocs-store/update' => ['AdminShopController', 'updateOcsStore'],

    // Categories Management
    'GET /admin/categories' => ['CategoryController', 'index'],
    'GET /admin/categories/create' => ['CategoryController', 'create'],
    'POST /admin/categories/store' => ['CategoryController', 'store'],
    'GET /admin/categories/edit' => ['CategoryController', 'edit'],
    'POST /admin/categories/update' => ['CategoryController', 'update'],
    'POST /admin/categories/delete' => ['CategoryController', 'delete'],

    // Brands Management
    'GET /admin/brands' => ['BrandController', 'index'],
    'GET /admin/brands/create' => ['BrandController', 'create'],
    'POST /admin/brands/store' => ['BrandController', 'store'],
    'GET /admin/brands/edit' => ['BrandController', 'edit'],
    'POST /admin/brands/update' => ['BrandController', 'update'],
    'POST /admin/brands/delete' => ['BrandController', 'delete'],

    // Products Management
    'GET /admin/products' => ['ProductController', 'index'],
    'GET /admin/products/create' => ['ProductController', 'create'],
    'POST /admin/products/store' => ['ProductController', 'store'],
    'GET /admin/products/edit' => ['ProductController', 'edit'],
    'POST /admin/products/update' => ['ProductController', 'update'],
    'POST /admin/products/delete' => ['ProductController', 'delete'],
    'POST /admin/products/upload-images' => ['ProductController', 'uploadImages'],
    'POST /admin/products/delete-image' => ['ProductController', 'deleteImage'],
    'POST /admin/products/toggle-feature' => ['ProductController', 'toggleFeature'],

    // Bulk Upload
    'GET /admin/products/bulk-upload' => ['ProductController', 'bulkUpload'],
    'GET /admin/products/bulk-upload/template' => ['ProductController', 'downloadTemplate'],
    'POST /admin/products/bulk-upload/process' => ['ProductController', 'processBulkUpload'],

    // Stock Management
    'GET /admin/products/stock' => ['ProductController', 'stock'],
    'POST /admin/products/update-stock' => ['ProductController', 'updateStock'],
    'POST /admin/products/restock' => ['ProductController', 'restock'],
    'GET /admin/products/stock/export' => ['ProductController', 'exportStock'],

    // Orders Management (Admin)
    'GET /admin/orders' => ['OrderController', 'adminOrders'],
    'GET /admin/orders/view' => ['OrderController', 'orderDetail'],
    'POST /admin/orders/update-status' => ['OrderController', 'updateOrderStatus'],
    'POST /admin/orders/delete' => ['AdminOrdersController', 'delete'],

    // Admin Delivery Management Routes
    'GET /admin/delivery' => ['AdminDeliveryController', 'index'],
    'GET /admin/delivery/staff' => ['AdminDeliveryController', 'staff'],
    'GET /admin/delivery/add-driver' => ['AdminDeliveryController', 'addDriver'],
    'POST /admin/delivery/add-driver' => ['AdminDeliveryController', 'addDriver'],
    'GET /admin/delivery/edit-driver' => ['AdminDeliveryController', 'editDriver'],
    'POST /admin/delivery/update-driver' => ['AdminDeliveryController', 'updateDriver'],
    'GET /admin/delivery/active' => ['AdminDeliveryController', 'activeDeliveries'],
    'POST /admin/delivery/assign' => ['AdminDeliveryController', 'assignDelivery'],
    'GET /admin/delivery/zones' => ['AdminDeliveryController', 'zones'],
    'GET /admin/delivery/analytics' => ['AdminDeliveryController', 'analytics'],
    'GET /admin/delivery/earnings' => ['AdminDeliveryController', 'earnings'],
    'GET /admin/delivery/driver-details' => ['AdminDeliveryController', 'driverDetails'],
    'POST /admin/delivery/mark-paid' => ['AdminDeliveryController', 'markPaid'],

    // Reports
    'GET /admin/reports' => ['ReportsController', 'index'],
    'GET /admin/reports/sales' => ['ReportsController', 'sales'],
    'GET /admin/reports/products' => ['ReportsController', 'products'],
    'GET /admin/reports/customers' => ['ReportsController', 'customers'],
    'GET /admin/reports/inventory' => ['ReportsController', 'inventory'],
    'GET /admin/reports/export' => ['ReportsController', 'export'],

    // Analytics
    'GET /admin/visitor-analytics' => ['VisitorAnalyticsController', 'index'],

    // Settings
    'GET /admin/settings' => ['SettingsController', 'index'],
    'POST /admin/settings/update' => ['SettingsController', 'update'],
    
    // CMS (Content Management)
    'GET /admin/cms' => ['CmsController', 'index'],
    'GET /admin/cms/edit' => ['CmsController', 'edit'],
    'POST /admin/cms/update' => ['CmsController', 'update'],
    'POST /admin/cms/quick-update' => ['CmsController', 'quickUpdate'],
    'GET /admin/cms/create' => ['CmsController', 'create'],
    'POST /admin/cms/create' => ['CmsController', 'create'],
    'POST /admin/cms/delete' => ['CmsController', 'delete'],

    // Hero Sliders Management
    'GET /admin/sliders' => ['SliderController', 'index'],
    'GET /admin/sliders/edit' => ['SliderController', 'edit'],
    'POST /admin/sliders/update' => ['SliderController', 'update'],
    'GET /admin/sliders/create' => ['SliderController', 'create'],
    'POST /admin/sliders/create' => ['SliderController', 'create'],
    'POST /admin/sliders/delete' => ['SliderController', 'delete'],
    'POST /admin/sliders/update-order' => ['SliderController', 'updateOrder'],

    // ===================================
    // SELLER ROUTES
    // ===================================
    'GET /seller/dashboard' => ['ShopController', 'dashboard'],
    'GET /seller/shop/create' => ['ShopController', 'create'],
    'POST /seller/shop/store' => ['ShopController', 'store'],
    'GET /seller/shop/settings' => ['ShopController', 'settings'],
    'POST /seller/shop/update' => ['ShopController', 'update'],
    
    // Inventory Management
    'GET /seller/inventory' => ['InventoryController', 'index'],
    'GET /seller/inventory/add' => ['InventoryController', 'add'],
    'POST /seller/inventory/store' => ['InventoryController', 'store'],
    'GET /seller/inventory/edit' => ['InventoryController', 'edit'],
    'POST /seller/inventory/update' => ['InventoryController', 'update'],
    'POST /seller/inventory/delete' => ['InventoryController', 'delete'],
    'GET /seller/inventory/create-product' => ['InventoryController', 'createProduct'],
    'POST /seller/inventory/store-product' => ['InventoryController', 'storeProduct'],
    
    // Seller Order Management
    'GET /seller/orders' => ['OrderController', 'sellerOrders'],
    'POST /seller/orders/update-status' => ['OrderController', 'updateOrderStatus'],

    // ===================================
    // DELIVERY DRIVER ROUTES
    // ===================================
    'GET /delivery/dashboard' => ['DeliveryController', 'dashboard'],
    'GET /delivery/available' => ['DeliveryController', 'availableOrders'],
    'GET /delivery/details' => ['DeliveryController', 'deliveryDetails'],
    'GET /delivery/earnings' => ['DeliveryController', 'earnings'],
    'GET /delivery/history' => ['DeliveryController', 'history'],
    'POST /delivery/accept' => ['DeliveryController', 'acceptDelivery'],
    'POST /delivery/reject' => ['DeliveryController', 'rejectDelivery'],
    'POST /delivery/status' => ['DeliveryController', 'updateStatus'],
    'POST /delivery/availability' => ['DeliveryController', 'updateAvailability'],

    // ===================================
    // UNIFIED DASHBOARD ROUTES
    // ===================================
    'GET /dashboard' => ['DashboardController', 'index'],

    // ===================================
    // BUYER ROUTES (Legacy)
    // ===================================
    'GET /buyer/dashboard' => ['DashboardController', 'buyerDashboard'],
    'GET /buyer/orders' => ['BuyerController', 'orders'],
    'GET /buyer/orders/{id}' => ['BuyerController', 'orderDetail'],
    
    // ===================================
    // PROFILE MANAGEMENT (Legacy)
    // ===================================
    'GET /profile' => ['DashboardController', 'showProfile'],
    'POST /profile/update' => ['DashboardController', 'updateProfile'],
    'POST /profile/change-password' => ['DashboardController', 'changePassword'],
    
    // ===================================
    // ADVERTISER ROUTES
    // ===================================
    'GET /advertiser/dashboard' => ['DashboardController', 'advertiserDashboard'],
    
    // ===================================
    // AFFILIATE ROUTES
    // ===================================
    'GET /affiliate/dashboard' => ['DashboardController', 'affiliateDashboard'],

    // ===================================
    // STATIC PAGES
    // ===================================
    'GET /about' => ['PageController', 'about'],
    'GET /contact' => ['PageController', 'contact'],
    'GET /faq' => ['PageController', 'faq'],
    'GET /terms' => ['PageController', 'terms'],
    'GET /privacy' => ['PageController', 'privacy'],
    'GET /help' => ['PageController', 'help'],

    // ===================================
    // API ROUTES (for AJAX calls)
    // ===================================
    'POST /api/newsletter/subscribe' => ['NewsletterController', 'subscribe'],
    'GET /api/search/suggestions' => ['SearchController', 'suggestions'],
];