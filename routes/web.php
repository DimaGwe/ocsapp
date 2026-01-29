<?php

/**
 * Web Routes - CLEANED (No Duplicates)
 */

return [
    // ===================================
    // PUBLIC ROUTES (Customer-facing)
    // ===================================

    // Landing Page
    'GET /' => function() {
        require __DIR__ . '/../public/landing.php';
        exit;
    },

    // Marketplace Home
    'GET /home' => ['HomeController', 'index'],

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
    'POST /api/location/search' => ['LocationController', 'search'],
    'POST /api/location/reverse-geocode' => ['LocationController', 'reverseGeocode'],

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
    // SELLER PUBLIC PAGES
    // ===================================
    'GET /seller-central' => ['PageController', 'sellerCentral'],

    // ===================================
    // BUYER PUBLIC PAGES
    // ===================================
    'GET /buyer-central' => ['PageController', 'buyerCentral'],

    // ===================================
    // SUPPLIER PUBLIC PAGES
    // ===================================
    'GET /supplier-central' => ['SupplierAuthController', 'landing'],

    // ===================================
    // ADMIN ROUTES
    // ===================================
    'GET /admin/dashboard' => ['AdminController', 'dashboard'],
    'GET /admin/planner' => ['AdminController', 'planner'],
    'GET /admin/planner/html-editor' => ['AdminController', 'htmlEditor'],
    'GET /admin/notifications' => ['AdminController', 'notifications'],

    // Users Management
    'GET /admin/users' => ['AdminController', 'users'],
    'GET /admin/users/edit' => ['AdminController', 'editUser'],
    'POST /admin/users/store' => ['AdminController', 'storeUser'],
    'POST /admin/users/update' => ['AdminController', 'updateUser'],
    'POST /admin/users/change-status' => ['AdminController', 'changeUserStatus'],
    'POST /admin/users/delete' => ['AdminController', 'deleteUser'],

    // Sellers Management
    'GET /admin/sellers' => ['AdminSellerController', 'index'],
    'GET /admin/sellers/view' => ['AdminSellerController', 'view'],
    'GET /admin/sellers/verification-review' => ['AdminSellerController', 'verificationReview'],
    'POST /admin/sellers/verification/approve' => ['AdminSellerController', 'verificationAction'],
    'GET /admin/sellers/edit' => ['AdminController', 'editSeller'],
    'POST /admin/sellers/update' => ['AdminController', 'updateSeller'],
    'POST /admin/sellers/suspend' => ['AdminSellerController', 'suspend'],
    'POST /admin/sellers/activate' => ['AdminSellerController', 'activate'],
    'POST /admin/sellers/delete' => ['AdminSellerController', 'delete'],

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

    // Email Templates Management
    'GET /admin/emails' => ['AdminEmailController', 'index'],
    'GET /admin/emails/edit' => ['AdminEmailController', 'edit'],
    'GET /admin/emails/preview' => ['AdminEmailController', 'preview'],
    'POST /admin/emails/update' => ['AdminEmailController', 'update'],

    // Products Management
    'GET /admin/products' => ['ProductController', 'index'],
    'GET /admin/products/create' => ['ProductController', 'create'],
    'POST /admin/products/store' => ['ProductController', 'store'],
    'GET /admin/products/edit' => ['ProductController', 'edit'],
    'POST /admin/products/update' => ['ProductController', 'update'],
    'POST /admin/products/delete' => ['ProductController', 'delete'],

    // Sales Management
    'GET /admin/sales' => ['AdminSalesController', 'index'],
    'GET /admin/sales/create' => ['AdminSalesController', 'create'],
    'POST /admin/sales/store' => ['AdminSalesController', 'store'],
    'GET /admin/sales/edit' => ['AdminSalesController', 'edit'],
    'POST /admin/sales/update' => ['AdminSalesController', 'update'],
    'POST /admin/sales/end' => ['AdminSalesController', 'endSale'],
    'GET /admin/sales/process-scheduled' => ['AdminSalesController', 'processScheduledSales'],
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
    'GET /admin/products/allocations' => ['ProductController', 'allocations'],
    'GET /admin/products/stock-movements' => ['ProductController', 'stockMovements'],

    // Business Accounts Management (Distribution Portal)
    'GET /admin/business-accounts' => ['AdminBusinessController', 'index'],
    'GET /admin/business-accounts/view' => ['AdminBusinessController', 'view'],
    'POST /admin/business-accounts/suspend' => ['AdminBusinessController', 'suspend'],
    'POST /admin/business-accounts/activate' => ['AdminBusinessController', 'activate'],
    'POST /admin/business-accounts/update-tier' => ['AdminBusinessController', 'updateTier'],
    'POST /admin/business-accounts/delete' => ['AdminBusinessController', 'delete'],

    // Distribution Requests Management (Admin)
    'GET /admin/distribution' => ['AdminDistributionController', 'index'],
    'GET /admin/distribution/view' => ['AdminDistributionController', 'view'],
    'POST /admin/distribution/update-prices' => ['AdminDistributionController', 'updatePrices'],
    'POST /admin/distribution/create-invoice' => ['AdminDistributionController', 'createInvoice'],
    'POST /admin/distribution/update-status' => ['AdminDistributionController', 'updateStatus'],
    'POST /admin/distribution/approve' => ['AdminDistributionController', 'approve'],
    'POST /admin/distribution/cancel' => ['AdminDistributionController', 'cancel'],
    'POST /admin/distribution/resend-payment-link' => ['AdminDistributionController', 'resendPaymentLink'],
    'POST /admin/distribution/start-procurement' => ['AdminDistributionController', 'startProcurement'],
    'POST /admin/distribution/mark-in-transit' => ['AdminDistributionController', 'markInTransit'],
    'POST /admin/distribution/mark-delivered' => ['AdminDistributionController', 'markDelivered'],

    // Leads CRM
    'GET /admin/leads' => ['AdminLeadsController', 'index'],
    'GET /admin/leads/create' => ['AdminLeadsController', 'create'],
    'POST /admin/leads/store' => ['AdminLeadsController', 'store'],
    'GET /admin/leads/view' => ['AdminLeadsController', 'view'],
    'GET /admin/leads/edit' => ['AdminLeadsController', 'edit'],
    'POST /admin/leads/update' => ['AdminLeadsController', 'update'],
    'POST /admin/leads/add-activity' => ['AdminLeadsController', 'addActivity'],
    'POST /admin/leads/update-status' => ['AdminLeadsController', 'updateStatus'],
    'POST /admin/leads/delete' => ['AdminLeadsController', 'delete'],

    // Shipment Management (Admin - Phase 3)
    'GET /admin/shipments' => ['AdminShipmentController', 'index'],
    'GET /admin/shipments/view' => ['AdminShipmentController', 'view'],
    'POST /admin/shipments/quote' => ['AdminShipmentController', 'createQuote'],
    'POST /admin/shipments/status' => ['AdminShipmentController', 'updateStatus'],
    'POST /admin/shipments/mark-paid' => ['AdminShipmentController', 'markPaid'],
    'POST /admin/shipments/destination-status' => ['AdminShipmentController', 'updateDestinationStatus'],

    // Supplier Management (Product Suppliers)
    'GET /admin/suppliers' => ['SupplierController', 'index'],
    'GET /admin/suppliers/create' => ['SupplierController', 'create'],
    'POST /admin/suppliers/store' => ['SupplierController', 'store'],
    'GET /admin/suppliers/edit' => ['SupplierController', 'edit'],
    'POST /admin/suppliers/update' => ['SupplierController', 'update'],
    'POST /admin/suppliers/delete' => ['SupplierController', 'delete'],
    'POST /admin/suppliers/send-invite' => ['SupplierController', 'sendInvite'],
    'POST /admin/suppliers/resend-invite' => ['SupplierController', 'resendInvite'],
    'POST /admin/suppliers/cancel-invite' => ['SupplierController', 'cancelInvite'],
    'POST /admin/suppliers/delete-invite' => ['SupplierController', 'deleteInvite'],
    'POST /admin/suppliers/reset-password' => ['SupplierController', 'resetPassword'],

    // Purchase Orders
    'GET /admin/purchase-orders' => ['PurchaseOrderController', 'index'],
    'GET /admin/purchase-orders/create' => ['PurchaseOrderController', 'create'],
    'POST /admin/purchase-orders/store' => ['PurchaseOrderController', 'store'],
    'GET /admin/purchase-orders/view' => ['PurchaseOrderController', 'view'],
    'GET /admin/purchase-orders/receive' => ['PurchaseOrderController', 'receive'],
    'POST /admin/purchase-orders/process-receiving' => ['PurchaseOrderController', 'processReceiving'],
    'POST /admin/purchase-orders/update-status' => ['PurchaseOrderController', 'updateStatus'],

    // Supplier Catalog Browser
    'GET /admin/supplier-catalog' => ['SupplierCatalogController', 'browse'],
    'POST /admin/supplier-catalog/add-to-draft' => ['SupplierCatalogController', 'addToDraft'],
    'GET /admin/supplier-catalog/draft' => ['SupplierCatalogController', 'viewDraft'],
    'POST /admin/supplier-catalog/update-draft-item' => ['SupplierCatalogController', 'updateDraftItem'],
    'POST /admin/supplier-catalog/remove-draft-item' => ['SupplierCatalogController', 'removeDraftItem'],
    'POST /admin/supplier-catalog/clear-draft' => ['SupplierCatalogController', 'clearDraft'],
    'POST /admin/supplier-catalog/create-po-from-draft' => ['SupplierCatalogController', 'createPOFromDraft'],
    'GET /admin/supplier-catalog/create-pos' => ['SupplierCatalogController', 'createPOs'],

    // Orders Management (Admin)
    'GET /admin/orders' => ['AdminOrdersController', 'index'],
    'GET /admin/orders/view' => ['AdminOrdersController', 'view'],
    'POST /admin/orders/update-status' => ['AdminOrdersController', 'updateStatus'],
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
    'GET /admin/delivery/details' => ['AdminDeliveryController', 'deliveryDetails'],
    'POST /admin/delivery/assign-driver' => ['AdminDeliveryController', 'assignDriverToDelivery'],

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
    'GET /admin/settings/payment' => ['SettingsController', 'payment'],
    'POST /admin/settings/payment/save' => ['SettingsController', 'savePayment'],

    // CMS (Content Management)
    'GET /admin/cms' => ['CmsController', 'index'],
    'GET /admin/cms/edit' => ['CmsController', 'edit'],
    'POST /admin/cms/update' => ['CmsController', 'update'],
    'POST /admin/cms/quick-update' => ['CmsController', 'quickUpdate'],
    'GET /admin/cms/create' => ['CmsController', 'create'],
    'POST /admin/cms/create' => ['CmsController', 'create'],
    'POST /admin/cms/delete' => ['CmsController', 'delete'],

    // Homepage Settings (NEW!)
    'GET /admin/homepage' => ['AdminHomepageController', 'index'],
    'POST /admin/homepage/update' => ['AdminHomepageController', 'update'],
    'POST /admin/homepage/reset' => ['AdminHomepageController', 'reset'],

    // Hero Sliders Management
    'GET /admin/sliders' => ['SliderController', 'index'],
    'GET /admin/sliders/edit' => ['SliderController', 'edit'],
    'POST /admin/sliders/update' => ['SliderController', 'update'],
    'GET /admin/sliders/create' => ['SliderController', 'create'],
    'POST /admin/sliders/create' => ['SliderController', 'create'],
    'POST /admin/sliders/delete' => ['SliderController', 'delete'],
    'POST /admin/sliders/update-order' => ['SliderController', 'updateOrder'],

    // Promo Banners Management
    'GET /admin/promo-banners' => ['PromoBannerController', 'index'],
    'GET /admin/promo-banners/edit' => ['PromoBannerController', 'edit'],
    'POST /admin/promo-banners/update' => ['PromoBannerController', 'update'],
    'GET /admin/promo-banners/create' => ['PromoBannerController', 'create'],
    'POST /admin/promo-banners/create' => ['PromoBannerController', 'create'],
    'POST /admin/promo-banners/delete' => ['PromoBannerController', 'delete'],
    'POST /admin/promo-banners/update-order' => ['PromoBannerController', 'updateOrder'],

    // Legal Content Management
    'GET /admin/legal' => ['AdminLegalController', 'index'],
    'GET /admin/legal/edit' => ['AdminLegalController', 'edit'],
    'POST /admin/legal/update' => ['AdminLegalController', 'update'],
    'GET /admin/legal/preview' => ['AdminLegalController', 'preview'],
    'POST /admin/legal/restore' => ['AdminLegalController', 'restore'],

    // Content Pages Management (About, Contact, etc.)
    'GET /admin/content-pages' => ['AdminContentPagesController', 'index'],
    'GET /admin/content-pages/edit' => ['AdminContentPagesController', 'edit'],
    'POST /admin/content-pages/update' => ['AdminContentPagesController', 'update'],
    'GET /admin/content-pages/create' => ['AdminContentPagesController', 'create'],
    'POST /admin/content-pages/delete' => ['AdminContentPagesController', 'delete'],

    // Translations Management (Multilingual)
    'GET /admin/translations' => ['AdminTranslationsController', 'index'],
    'GET /admin/translations/edit' => ['AdminTranslationsController', 'edit'],
    'POST /admin/translations/update' => ['AdminTranslationsController', 'update'],
    'POST /admin/translations/bulk-update' => ['AdminTranslationsController', 'bulkUpdate'],
    'GET /admin/translations/create' => ['AdminTranslationsController', 'create'],
    'POST /admin/translations/create' => ['AdminTranslationsController', 'create'],
    'POST /admin/translations/delete' => ['AdminTranslationsController', 'delete'],
    'GET /admin/translations/export' => ['AdminTranslationsController', 'export'],

    // ===================================
    // SELLER ROUTES
    // ===================================
    'GET /seller/dashboard' => ['ShopController', 'dashboard'],
    'GET /seller/shop/create' => ['ShopController', 'create'],
    'POST /seller/shop/store' => ['ShopController', 'store'],
    'GET /seller/shop/settings' => ['ShopController', 'settings'],
    'POST /seller/shop/update' => ['ShopController', 'update'],

    // Seller Verification
    'GET /seller/verification' => ['SellerVerificationController', 'index'],
    'POST /seller/verification/submit' => ['SellerVerificationController', 'submit'],

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
    // SUPPLIER PORTAL ROUTES
    // ===================================

    // Supplier Authentication
    'GET /supplier/login' => ['SupplierAuthController', 'login'],
    'POST /supplier/login' => ['SupplierAuthController', 'processLogin'],
    'GET /supplier/logout' => ['SupplierAuthController', 'logout'],
    'GET /supplier/accept-invite' => ['SupplierAuthController', 'acceptInvite'],
    'POST /supplier/complete-registration' => ['SupplierAuthController', 'completeRegistration'],

    // Supplier Dashboard
    'GET /supplier/dashboard' => ['SupplierAuthController', 'dashboard'],

    // Supplier Settings
    'GET /supplier/settings' => ['SupplierAuthController', 'settings'],
    'POST /supplier/update-password' => ['SupplierAuthController', 'updatePassword'],

    // Supplier Product Management
    'GET /supplier/products' => ['SupplierProductController', 'index'],
    'GET /supplier/products/create' => ['SupplierProductController', 'create'],
    'POST /supplier/products/store' => ['SupplierProductController', 'store'],
    'GET /supplier/products/edit' => ['SupplierProductController', 'edit'],
    'POST /supplier/products/update' => ['SupplierProductController', 'update'],
    'POST /supplier/products/delete' => ['SupplierProductController', 'delete'],

    // Supplier Orders
    'GET /supplier/orders' => ['SupplierProductController', 'orders'],
    'GET /supplier/orders/view' => ['SupplierProductController', 'viewOrder'],
    'POST /supplier/orders/accept' => ['SupplierProductController', 'acceptOrder'],
    'POST /supplier/orders/decline' => ['SupplierProductController', 'declineOrder'],

    // Supplier Analytics
    'GET /supplier/analytics' => ['SupplierProductController', 'analytics'],

    // ===================================
    // DISTRIBUTION PORTAL ROUTES
    // ===================================

    // Distribution Landing & Auth
    'GET /distribution' => ['DistributionAuthController', 'landing'],
    'GET /distribution/login' => ['DistributionAuthController', 'showLogin'],
    'POST /distribution/login' => ['DistributionAuthController', 'login'],
    'GET /distribution/register' => ['DistributionAuthController', 'showRegister'],
    'POST /distribution/register' => ['DistributionAuthController', 'register'],
    'GET /distribution/logout' => ['DistributionAuthController', 'logout'],

    // Distribution Dashboard
    'GET /distribution/dashboard' => ['DistributionAuthController', 'dashboard'],

    // Distribution Procurement Requests (Phase 2)
    'GET /distribution/requests' => ['DistributionRequestController', 'index'],
    'GET /distribution/requests/create' => ['DistributionRequestController', 'create'],
    'POST /distribution/requests/store' => ['DistributionRequestController', 'store'],
    'GET /distribution/requests/show' => ['DistributionRequestController', 'show'],
    'GET /distribution/requests/edit' => ['DistributionRequestController', 'edit'],
    'POST /distribution/requests/update' => ['DistributionRequestController', 'update'],
    'POST /distribution/requests/submit' => ['DistributionRequestController', 'submit'],
    'POST /distribution/requests/cancel' => ['DistributionRequestController', 'cancel'],
    'POST /distribution/requests/delete' => ['DistributionRequestController', 'delete'],

    // Distribution Shipments (Phase 3 - Outbound Distribution)
    'GET /distribution/shipments' => ['DistributionShipmentController', 'index'],
    'GET /distribution/shipments/create' => ['DistributionShipmentController', 'create'],
    'POST /distribution/shipments/store' => ['DistributionShipmentController', 'store'],
    'GET /distribution/shipments/show' => ['DistributionShipmentController', 'show'],
    'GET /distribution/shipments/edit' => ['DistributionShipmentController', 'edit'],
    'POST /distribution/shipments/update' => ['DistributionShipmentController', 'update'],
    'POST /distribution/shipments/submit' => ['DistributionShipmentController', 'submit'],
    'POST /distribution/shipments/cancel' => ['DistributionShipmentController', 'cancel'],
    'GET /distribution/shipments/track' => ['DistributionShipmentController', 'track'],

    // Distribution Payment Flow
    'GET /distribution/pay' => ['DistributionPaymentController', 'showPaymentPage'],
    'POST /distribution/pay/create-session' => ['DistributionPaymentController', 'createCheckoutSession'],
    'GET /distribution/pay/success' => ['DistributionPaymentController', 'paymentSuccess'],
    'POST /distribution/pay/webhook' => ['DistributionPaymentController', 'webhook'],

    // Distribution Documents (PDF Downloads)
    'GET /distribution/documents/invoice' => ['DistributionDocumentController', 'invoice'],
    'GET /distribution/documents/purchase-order' => ['DistributionDocumentController', 'purchaseOrder'],
    'GET /distribution/documents/sales-order' => ['DistributionDocumentController', 'salesOrder'],

    // Distribution Recurring Routes (Phase 3)
    'GET /distribution/routes' => ['DistributionRouteController', 'index'],
    'GET /distribution/routes/create' => ['DistributionRouteController', 'create'],
    'POST /distribution/routes/store' => ['DistributionRouteController', 'store'],
    'GET /distribution/routes/show' => ['DistributionRouteController', 'show'],
    'GET /distribution/routes/edit' => ['DistributionRouteController', 'edit'],
    'POST /distribution/routes/update' => ['DistributionRouteController', 'update'],
    'POST /distribution/routes/pause' => ['DistributionRouteController', 'pause'],
    'POST /distribution/routes/resume' => ['DistributionRouteController', 'resume'],
    'POST /distribution/routes/cancel' => ['DistributionRouteController', 'cancel'],
    'GET /distribution/routes/draft' => ['DistributionRouteController', 'viewDraft'],
    'POST /distribution/routes/approve' => ['DistributionRouteController', 'approveDraft'],

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

    // ===================================
    // PLANNER API ROUTES
    // ===================================
    // Notes
    'GET /api/planner/notes' => ['Api\\PlannerNotesController', 'index'],
    'POST /api/planner/notes' => ['Api\\PlannerNotesController', 'store'],
    'DELETE /api/planner/notes' => ['Api\\PlannerNotesController', 'destroy'],
    'GET /api/planner/notes/comments' => ['Api\\PlannerNotesController', 'getComments'],
    'POST /api/planner/notes/comments' => ['Api\\PlannerNotesController', 'storeComment'],

    // Todos
    'GET /api/planner/todos' => ['Api\\PlannerTodosController', 'index'],
    'POST /api/planner/todos' => ['Api\\PlannerTodosController', 'store'],
    'PUT /api/planner/todos' => ['Api\\PlannerTodosController', 'toggleComplete'],
    'DELETE /api/planner/todos' => ['Api\\PlannerTodosController', 'destroy'],
    'GET /api/planner/todos/comments' => ['Api\\PlannerTodosController', 'getComments'],
    'POST /api/planner/todos/comments' => ['Api\\PlannerTodosController', 'storeComment'],

    // Documents
    'GET /api/planner/documents' => ['Api\\PlannerDocumentsController', 'index'],
    'POST /api/planner/documents' => ['Api\\PlannerDocumentsController', 'store'],
    'DELETE /api/planner/documents' => ['Api\\PlannerDocumentsController', 'destroy'],
    'GET /api/planner/documents/view' => ['Api\\PlannerDocumentsController', 'view'],
    'GET /api/planner/documents/download' => ['Api\\PlannerDocumentsController', 'download'],

    // Templates
    'GET /api/planner/templates' => ['Api\\PlannerTemplatesController', 'index'],
    'GET /api/planner/templates/show' => ['Api\\PlannerTemplatesController', 'show'],
    'POST /api/planner/templates' => ['Api\\PlannerTemplatesController', 'store'],
    'PUT /api/planner/templates' => ['Api\\PlannerTemplatesController', 'update'],
    'DELETE /api/planner/templates' => ['Api\\PlannerTemplatesController', 'destroy'],
    'GET /api/planner/templates/revisions' => ['Api\\PlannerTemplatesController', 'getRevisions'],
    'GET /api/planner/templates/revision' => ['Api\\PlannerTemplatesController', 'getRevision'],
    'POST /api/planner/templates/restore' => ['Api\\PlannerTemplatesController', 'restoreRevision'],
    'GET /api/planner/templates/categories' => ['Api\\PlannerTemplatesController', 'getCategories'],

    // PDF Generation
    'POST /api/pdf/generate' => ['Api\\PdfController', 'generate'],

    // Activity
    'GET /api/planner/activity' => ['Api\\PlannerActivityController', 'index'],

    // Users
    'GET /api/planner/users' => ['Api\\PlannerUsersController', 'index'],

    // ===================================
    // ADMIN NOTIFICATIONS API
    // ===================================
    'GET /api/admin/notifications' => ['Api\\NotificationsController', 'index'],
    'GET /api/admin/notifications/count' => ['Api\\NotificationsController', 'count'],
    'POST /api/admin/notifications/mark-read' => ['Api\\NotificationsController', 'markRead'],
    'POST /api/admin/notifications/mark-all-read' => ['Api\\NotificationsController', 'markAllRead'],
];