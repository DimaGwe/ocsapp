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
    'POST /shops/{slug}/review' => ['HomeController', 'submitReview'],
    'POST /shops/{slug}/contact' => ['HomeController', 'contactShop'],
    'POST /shops/{slug}/report' => ['HomeController', 'reportShop'],

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
    'POST /language/switch' => ['LanguageController', 'switch'],
    'GET /language/current' => ['LanguageController', 'current'],
    'POST /set-language' => ['HomeController', 'setLanguage'],
    'POST /set-location' => ['HomeController', 'setLocation'],
    'POST /api/location/search' => ['LocationController', 'search'],
    'POST /api/location/reverse-geocode' => ['LocationController', 'reverseGeocode'],
    'POST /api/geocode-address' => ['LocationController', 'geocodeAddress'],

    // Static Pages
    'GET /terms' => ['PageController', 'terms'],
    'GET /privacy' => ['PageController', 'privacy'],
    'GET /cookies' => ['PageController', 'cookies'],
    'GET /returns' => ['PageController', 'returns'],
    'GET /accessibility' => ['PageController', 'accessibility'],
    'GET /seller-agreement'       => ['PageController', 'sellerAgreement'],
    'GET /supplier-agreement'     => ['PageController', 'supplierAgreement'],
    'GET /driver-agreement'       => ['PageController', 'driverAgreement'],
    'GET /distribution-agreement' => ['PageController', 'distributionAgreement'],
    'GET /nda'                    => ['PageController', 'nda'],
    'GET /about' => ['PageController', 'about'],
    'GET /contact' => ['PageController', 'contact'],
    'POST /contact/submit' => ['PageController', 'submitContact'],

    // ===================================
    // AUTHENTICATION
    // ===================================
    'GET /login' => ['AuthController', 'showLogin'],
    'POST /login' => ['AuthController', 'login'],
    'GET /seller/login' => ['AuthController', 'showSellerLogin'],
    'GET /buyer/login' => ['AuthController', 'showBuyerLogin'],
    'GET /delivery/login' => ['AuthController', 'showDriverLogin'],
    'GET /register' => ['AuthController', 'showRegister'],
    'POST /register' => ['AuthController', 'register'],
    'GET /verify-email' => ['AuthController', 'showVerifyEmail'],
    'GET /verify-email/auto' => ['AuthController', 'autoVerifyEmail'],
    'POST /verify-email' => ['AuthController', 'verifyEmail'],
    'POST /resend-verification' => ['AuthController', 'resendVerification'],
    'POST /logout' => ['AuthController', 'logout'],
    'GET /logout'  => ['AuthController', 'logout'],
    'GET /forgot-password' => ['AuthController', 'showForgotPassword'],
    'POST /forgot-password' => ['AuthController', 'forgotPassword'],
    'GET /reset-password' => ['AuthController', 'showResetPassword'],
    'POST /reset-password' => ['AuthController', 'resetPassword'],

    // ===================================
    // USER ACCOUNT ROUTES (SPECIFIC FIRST!)
    // ===================================

    // Orders (MUST come before general /account routes)
    'GET /account/orders/detail' => ['OrderController', 'orderDetail'],
    'POST /account/orders/cancel' => ['OrderController', 'cancelOrder'],
    'POST /account/orders/rate-driver' => ['OrderController', 'rateDriver'],
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

    // Become a Seller (for existing buyers)
    'GET /account/become-seller' => ['AccountController', 'becomeSeller'],
    'POST /account/become-seller' => ['AccountController', 'submitSellerApplication'],

    // Dashboard (LAST)
    'GET /account/dashboard' => ['AccountController', 'index'],
    'GET /account' => ['AccountController', 'index'],

    // ===================================
    // WAITLIST
    // ===================================
    'GET /waitlist'  => ['WaitlistController', 'index'],
    'POST /waitlist' => ['WaitlistController', 'store'],

    // ===================================
    // SELLER PUBLIC PAGES
    // ===================================
    'GET /seller-central' => ['PageController', 'sellerCentral'],

    // ===================================
    // BUYER PUBLIC PAGES
    // ===================================
    'GET /buyer-central' => ['PageController', 'buyerCentral'],

    // ===================================
    // DRIVER PUBLIC PAGES
    // ===================================
    'GET /driver-central' => ['PageController', 'driverCentral'],

    // ===================================
    // SUPPLIER PUBLIC PAGES
    // ===================================
    'GET /supplier-central' => ['SupplierAuthController', 'landing'],
    'GET /supplier/apply' => ['SupplierAuthController', 'apply'],
    'POST /supplier/apply' => ['SupplierAuthController', 'submitApplication'],
    'GET /supplier/verify-email' => ['SupplierAuthController', 'showVerifyEmail'],
    'GET /supplier/verify-email/auto' => ['SupplierAuthController', 'autoVerifyEmail'],
    'POST /supplier/verify-email' => ['SupplierAuthController', 'verifyEmail'],
    'POST /supplier/resend-verification' => ['SupplierAuthController', 'resendSupplierVerification'],

    // ===================================
    // ADMIN ROUTES
    // ===================================
    'GET /admin/command-center' => ['AdminCommandCenterController', 'index'],
    'GET /admin/dashboard' => ['AdminController', 'dashboard'],
    'GET /admin/planner' => ['AdminController', 'planner'],
    'GET /admin/planner/me' => ['AdminController', 'plannerDashboard'],
    'GET /admin/notifications' => ['AdminController', 'notifications'],
    'GET /admin/profile' => ['AdminController', 'profile'],
    'POST /admin/profile/update-preferences' => ['AdminController', 'updateNotificationPreferences'],

    // Users Management
    'GET /admin/buyers'   => ['AdminController', 'buyers'],
    'GET /admin/drivers'  => ['AdminDeliveryController', 'drivers'],
    'GET /admin/users' => ['AdminController', 'users'],
    'GET /admin/users/edit' => ['AdminController', 'editUser'],
    'POST /admin/users/store' => ['AdminController', 'storeUser'],
    'POST /admin/users/update' => ['AdminController', 'updateUser'],
    'POST /admin/users/change-status' => ['AdminController', 'changeUserStatus'],
    'POST /admin/users/delete' => ['AdminController', 'deleteUser'],
    'POST /admin/users/supplier-change-status' => ['AdminController', 'changeSupplierStatus'],
    'POST /admin/users/supplier-delete' => ['AdminController', 'deleteSupplier'],
    'POST /admin/users/toggle-test-account' => ['AdminController', 'toggleTestAccount'],
    'POST /admin/users/reset-test-account'  => ['AdminController', 'resetTestUser'],
    'GET /admin/deleted-users'              => ['AdminController', 'deletedUsers'],
    'POST /admin/deleted-users/toggle-ban'  => ['AdminController', 'toggleDeletedUserBan'],

    // Sellers Management
    'GET /admin/sellers' => ['AdminSellerController', 'index'],
    'GET /admin/sellers/view' => ['AdminSellerController', 'view'],
    'GET /admin/sellers/verification-review' => ['AdminSellerController', 'verificationReview'],
    'POST /admin/sellers/verification/approve' => ['AdminSellerController', 'verificationAction'],
    'GET /admin/sellers/document' => ['AdminSellerController', 'downloadDocument'],
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

    // Email Log (Archive)
    'GET /admin/email-log' => ['AdminEmailLogController', 'index'],
    'GET /admin/email-log/view' => ['AdminEmailLogController', 'view'],

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
    'POST /admin/business-accounts/approve' => ['AdminBusinessController', 'approve'],
    'POST /admin/business-accounts/reject' => ['AdminBusinessController', 'reject'],
    'POST /admin/business-accounts/suspend' => ['AdminBusinessController', 'suspend'],
    'POST /admin/business-accounts/activate' => ['AdminBusinessController', 'activate'],
    'POST /admin/business-accounts/extend-deadline' => ['AdminBusinessController', 'extendDeadline'],
    'POST /admin/business-accounts/update-tier' => ['AdminBusinessController', 'updateTier'],
    'POST /admin/business-accounts/delete' => ['AdminBusinessController', 'delete'],
    'POST /admin/business-accounts/messages/send' => ['BusinessMessagesController', 'adminSend'],
    'GET /admin/business-accounts/messages' => ['BusinessMessagesController', 'adminIndex'],
    'POST /admin/business-accounts/documents/verify'  => ['AdminBusinessController', 'verifyDocument'],
    'POST /admin/business-accounts/documents/reject'  => ['AdminBusinessController', 'rejectDocument'],
    'POST /admin/business-accounts/documents/request' => ['AdminBusinessController', 'requestDocument'],

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
    'POST /admin/distribution/mark-supplier-paid' => ['AdminDistributionController', 'markSupplierPaid'],
    'POST /admin/distribution/mark-in-transit' => ['AdminDistributionController', 'markInTransit'],
    'POST /admin/distribution/mark-delivered' => ['AdminDistributionController', 'markDelivered'],

    // Marketing Content Generator
    'GET /admin/marketing'           => ['AdminMarketingController', 'index'],
    'POST /admin/marketing/generate' => ['AdminMarketingController', 'generate'],

    // Content System (AI Creator + Library)
    'GET /admin/content/create'           => ['AdminContentController', 'create'],
    'GET /admin/content/library'          => ['AdminContentController', 'library'],
    'POST /admin/content/chat'            => ['AdminContentController', 'chat'],
    'POST /admin/content/generate-image'  => ['AdminContentController', 'genImage'],
    'POST /admin/content/generate-video'  => ['AdminContentController', 'genVideo'],
    'POST /admin/content/save'            => ['AdminContentController', 'save'],
    'POST /admin/content/update-status'   => ['AdminContentController', 'updateStatus'],
    'POST /admin/content/delete'          => ['AdminContentController', 'delete'],

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
    'POST /admin/leads/approve-supplier' => ['AdminLeadsController', 'approveSupplier'],
    'POST /admin/leads/reject-supplier' => ['AdminLeadsController', 'rejectSupplier'],
    'POST /admin/leads/request-supplier-info' => ['AdminLeadsController', 'requestSupplierInfo'],
    'POST /admin/leads/extend-supplier-deadline' => ['AdminLeadsController', 'extendSupplierDeadline'],
    'POST /admin/leads/update-doc-status' => ['AdminLeadsController', 'updateDocStatus'],

    // Shipment Management (Admin - Phase 3)
    'GET /admin/shipments' => ['AdminShipmentController', 'index'],
    'GET /admin/shipments/view' => ['AdminShipmentController', 'view'],
    'POST /admin/shipments/quote' => ['AdminShipmentController', 'createQuote'],
    'POST /admin/shipments/status' => ['AdminShipmentController', 'updateStatus'],
    'POST /admin/shipments/mark-paid' => ['AdminShipmentController', 'markPaid'],
    'POST /admin/shipments/destination-status' => ['AdminShipmentController', 'updateDestinationStatus'],

    // Supplier Management (Product Suppliers)
    'GET /admin/suppliers' => ['SupplierController', 'index'],
    'GET /admin/suppliers/performance' => ['SupplierController', 'performance'],
    'GET /admin/suppliers/create' => ['SupplierController', 'create'],
    'POST /admin/suppliers/store' => ['SupplierController', 'store'],
    'GET /admin/suppliers/edit' => ['SupplierController', 'edit'],
    'POST /admin/suppliers/update' => ['SupplierController', 'update'],
    'POST /admin/suppliers/delete' => ['SupplierController', 'delete'],
    'POST /admin/suppliers/restore' => ['SupplierController', 'restore'],
    'POST /admin/suppliers/send-invite' => ['SupplierController', 'sendInvite'],
    'POST /admin/suppliers/resend-invite' => ['SupplierController', 'resendInvite'],
    'POST /admin/suppliers/cancel-invite' => ['SupplierController', 'cancelInvite'],
    'POST /admin/suppliers/delete-invite' => ['SupplierController', 'deleteInvite'],
    'POST /admin/suppliers/reset-password' => ['SupplierController', 'resetPassword'],
    'POST /admin/suppliers/update-package' => ['SupplierController', 'updatePackage'],

    // Purchase Orders
    'GET /admin/purchase-orders' => ['PurchaseOrderController', 'index'],
    'GET /admin/purchase-orders/create' => ['PurchaseOrderController', 'create'],
    'POST /admin/purchase-orders/store' => ['PurchaseOrderController', 'store'],
    'GET /admin/purchase-orders/view' => ['PurchaseOrderController', 'view'],
    'GET /admin/purchase-orders/receive' => ['PurchaseOrderController', 'receive'],
    'POST /admin/purchase-orders/process-receiving' => ['PurchaseOrderController', 'processReceiving'],
    'POST /admin/purchase-orders/update-status' => ['PurchaseOrderController', 'updateStatus'],
    'POST /admin/purchase-orders/assign-driver' => ['PurchaseOrderController', 'assignPickupDriver'],
    'POST /admin/purchase-orders/mark-picked-up' => ['PurchaseOrderController', 'markPickedUp'],
    'POST /admin/purchase-orders/update-tax' => ['PurchaseOrderController', 'updateTax'],
    'POST /admin/purchase-orders/{id}/notify-driver' => ['PurchaseOrderController', 'notifyPickupDriver'],

    // Supplier Catalog Browser
    'GET /admin/supplier-catalog' => ['SupplierCatalogController', 'browse'],
    'POST /admin/supplier-catalog/add-to-draft' => ['SupplierCatalogController', 'addToDraft'],
    'GET /admin/supplier-catalog/draft' => ['SupplierCatalogController', 'viewDraft'],
    'POST /admin/supplier-catalog/update-draft-item' => ['SupplierCatalogController', 'updateDraftItem'],
    'POST /admin/supplier-catalog/remove-draft-item' => ['SupplierCatalogController', 'removeDraftItem'],
    'POST /admin/supplier-catalog/clear-draft' => ['SupplierCatalogController', 'clearDraft'],
    'POST /admin/supplier-catalog/create-po-from-draft' => ['SupplierCatalogController', 'createPOFromDraft'],
    'GET /admin/supplier-catalog/create-pos' => ['SupplierCatalogController', 'createPOs'],
    'POST /admin/supplier-catalog/create-all-pos' => ['SupplierCatalogController', 'storeAllFromDraft'],
    'GET /admin/supplier-catalog/alternatives'          => ['SupplierCatalogController', 'getAlternatives'],
    'POST /admin/supplier-catalog/alternatives/add'     => ['SupplierCatalogController', 'addAlternative'],
    'POST /admin/supplier-catalog/alternatives/remove'  => ['SupplierCatalogController', 'removeAlternative'],
    'POST /admin/supplier-catalog/alternatives/reorder' => ['SupplierCatalogController', 'reorderAlternatives'],

    // Supplier Payables (Admin)
    'GET /admin/payables' => ['AdminPayablesController', 'index'],
    'GET /admin/payables/view' => ['AdminPayablesController', 'viewInvoice'],
    'POST /admin/payables/generate-invoice' => ['AdminPayablesController', 'generateInvoice'],
    'POST /admin/payables/record-payment' => ['AdminPayablesController', 'recordPayment'],
    'GET /admin/payables/export' => ['AdminPayablesController', 'export'],
    'GET /admin/payables/download-pdf' => ['AdminPayablesController', 'downloadInvoicePdf'],

    // Receivables (Admin)
    'GET /admin/receivables' => ['AdminReceivablesController', 'index'],
    'POST /admin/receivables/mark-paid' => ['AdminReceivablesController', 'markPaid'],
    'POST /admin/receivables/mark-refunded' => ['AdminReceivablesController', 'markRefunded'],
    'GET /admin/receivables/export' => ['AdminReceivablesController', 'export'],

    // Orders Management (Admin)
    'GET /admin/orders' => ['AdminOrdersController', 'index'],
    'GET /admin/orders/view' => ['AdminOrdersController', 'view'],
    'POST /admin/orders/update-status' => ['AdminOrdersController', 'updateStatus'],
    'POST /admin/orders/mark-paid' => ['AdminOrdersController', 'markAsPaid'],
    'POST /admin/orders/delete' => ['AdminOrdersController', 'delete'],
    'POST /admin/orders/{id}/notify-driver' => ['AdminOrdersController', 'notifyDriver'],

    // Admin Delivery Management Routes
    'GET /admin/delivery' => ['AdminDeliveryController', 'index'],
    'GET /admin/delivery/staff' => ['AdminDeliveryController', 'staff'],
    'GET /admin/delivery/add-driver' => ['AdminDeliveryController', 'addDriver'],
    'POST /admin/delivery/add-driver' => ['AdminDeliveryController', 'addDriver'],
    'GET /admin/delivery/edit-driver' => ['AdminDeliveryController', 'editDriver'],
    'POST /admin/delivery/update-driver' => ['AdminDeliveryController', 'updateDriver'],
    'GET /admin/delivery/active' => ['AdminDeliveryController', 'activeDeliveries'],
    'POST /admin/delivery/assign' => ['AdminDeliveryController', 'assignDelivery'],
    'GET /admin/delivery/zones'         => ['AdminDeliveryController', 'zones'],
    'GET /admin/delivery/zones/get'     => ['AdminDeliveryController', 'getZone'],
    'POST /admin/delivery/zones/create' => ['AdminDeliveryController', 'createZone'],
    'POST /admin/delivery/zones/update' => ['AdminDeliveryController', 'updateZone'],
    'POST /admin/delivery/zones/toggle' => ['AdminDeliveryController', 'toggleZone'],
    'GET /admin/delivery/analytics' => ['AdminDeliveryController', 'analytics'],
    'GET /admin/delivery/earnings' => ['AdminDeliveryController', 'earnings'],
    'GET /admin/delivery/driver-details' => ['AdminDeliveryController', 'driverDetails'],
    'POST /admin/delivery/mark-paid' => ['AdminDeliveryController', 'markPaid'],
    'GET /admin/delivery/details' => ['AdminDeliveryController', 'deliveryDetails'],
    'POST /admin/delivery/assign-driver' => ['AdminDeliveryController', 'assignDriverToDelivery'],
    'POST /admin/delivery/assign-distribution-driver' => ['AdminDeliveryController', 'assignDistributionDriver'],
    'GET /admin/delivery/live-map' => ['AdminDeliveryController', 'liveMap'],
    'GET /admin/delivery/route-replay' => ['AdminDeliveryController', 'routeReplay'],
    'GET /api/delivery/replay-data' => ['AdminDeliveryController', 'replayData'],
    'GET /admin/delivery/route-optimizer' => ['AdminDeliveryController', 'routeOptimizer'],
    'POST /admin/delivery/optimize-route' => ['AdminDeliveryController', 'optimizeRoute'],
    'POST /admin/delivery/export-earnings' => ['AdminDeliveryController', 'exportEarnings'],
    'POST /admin/delivery/pipeline/approve'           => ['AdminDeliveryController', 'approveApplicationPipeline'],
    'POST /admin/delivery/pipeline/reject'            => ['AdminDeliveryController', 'rejectApplicationPipeline'],
    'POST /admin/delivery/pipeline/under-review'      => ['AdminDeliveryController', 'markUnderReview'],
    'POST /admin/delivery/pipeline/request-interview' => ['AdminDeliveryController', 'requestInterview'],
    'POST /admin/delivery/pipeline/send-message'      => ['AdminDeliveryController', 'sendApplicationMessage'],
    'GET  /admin/delivery/pipeline/messages'          => ['AdminDeliveryController', 'getApplicationMessages'],

    // Admin Pickup Requests (supplier-initiated)
    'GET /admin/pickup-requests'            => ['AdminDeliveryController', 'pickupRequests'],
    'POST /admin/pickup-requests/schedule'  => ['AdminDeliveryController', 'schedulePickup'],
    'POST /admin/pickup-requests/cancel'    => ['AdminDeliveryController', 'cancelPickupRequest'],

    // Driver Activity Log (Admin)
    'GET /admin/driver-activity'        => ['AdminDriverActivityController', 'index'],
    'GET /admin/driver-activity/export' => ['AdminDriverActivityController', 'export'],

    // Admin Training Management
    'GET /admin/training'                        => ['AdminTrainingController', 'index'],
    'GET /admin/training/module/edit'            => ['AdminTrainingController', 'editModule'],
    'POST /admin/training/module/save'           => ['AdminTrainingController', 'saveModule'],
    'POST /admin/training/question/save'         => ['AdminTrainingController', 'saveQuestion'],
    'POST /admin/training/question/delete'       => ['AdminTrainingController', 'deleteQuestion'],
    'POST /admin/training/driver/reset-module'   => ['AdminTrainingController', 'resetDriverModule'],
    'POST /admin/training/driver/certify'        => ['AdminTrainingController', 'manualCertify'],

    // Universal Search & Contact Card API
    'GET /admin/api/universal-search' => ['Api\\UniversalSearchController', 'search'],
    'GET /admin/api/contact-card'     => ['Api\\UniversalSearchController', 'contactCard'],

    // Support Ticket Inbox (Contact Center)
    'GET /admin/support'                  => ['AdminSupportController', 'index'],
    'GET /admin/support/create'           => ['AdminSupportController', 'create'],
    'POST /admin/support/store'           => ['AdminSupportController', 'store'],
    'GET /admin/support/view'             => ['AdminSupportController', 'view'],
    'POST /admin/support/reply'           => ['AdminSupportController', 'reply'],
    'POST /admin/support/update-status'   => ['AdminSupportController', 'updateStatus'],
    'POST /admin/support/assign'          => ['AdminSupportController', 'assign'],
    'POST /admin/support/update-priority' => ['AdminSupportController', 'updatePriority'],
    'POST /admin/support/delete'          => ['AdminSupportController', 'delete'],
    'GET /admin/api/support/ticket'       => ['Api\\SupportController', 'ticket'],

    // Agent Dashboard
    'GET /admin/agent-dashboard'           => ['AdminAgentDashboardController', 'index'],
    'POST /admin/agent-dashboard/status'   => ['AdminAgentDashboardController', 'updateStatus'],

    // Call Log
    'GET /admin/call-log'                  => ['AdminCallLogController', 'index'],
    'POST /admin/call-log/store'           => ['AdminCallLogController', 'store'],
    'GET /admin/api/call-log/callbacks'    => ['AdminCallLogController', 'callbacks'],

    // GPS Tracking API
    'POST /api/delivery/location' => ['Api\\DeliveryTrackingController', 'updateLocation'],
    'GET /api/delivery/location' => ['Api\\DeliveryTrackingController', 'getLocation'],
    'GET /api/admin/delivery/active-drivers'  => ['Api\\DeliveryTrackingController', 'getActiveDrivers'],
    'GET /admin/api/delivery/oda-live'        => ['Api\\DeliveryTrackingController', 'odaLive'],
    'GET /admin/api/delivery/driver-route'    => ['Api\\DeliveryTrackingController', 'driverRoute'],

    // Vehicle Management
    'GET /admin/delivery/vehicles' => ['AdminVehicleController', 'index'],
    'GET /admin/delivery/vehicles/create' => ['AdminVehicleController', 'create'],
    'POST /admin/delivery/vehicles/store' => ['AdminVehicleController', 'store'],
    'GET /admin/delivery/vehicles/edit' => ['AdminVehicleController', 'edit'],
    'POST /admin/delivery/vehicles/update' => ['AdminVehicleController', 'update'],
    'GET /admin/delivery/vehicles/view' => ['AdminVehicleController', 'view'],
    'POST /admin/delivery/vehicles/assign-driver' => ['AdminVehicleController', 'assignDriver'],
    'POST /admin/delivery/vehicles/update-status' => ['AdminVehicleController', 'updateStatus'],

    // Reports
    'GET /admin/reports' => ['ReportsController', 'index'],
    'GET /admin/reports/sales' => ['ReportsController', 'sales'],
    'GET /admin/reports/products' => ['ReportsController', 'products'],
    'GET /admin/reports/customers' => ['ReportsController', 'customers'],
    'GET /admin/reports/inventory' => ['ReportsController', 'inventory'],
    'GET /admin/reports/export' => ['ReportsController', 'export'],

    // Waitlist Management
    'GET /admin/waitlist'         => ['AdminWaitlistController', 'index'],
    'POST /admin/waitlist/notify' => ['AdminWaitlistController', 'notify'],
    'POST /admin/waitlist/status' => ['AdminWaitlistController', 'updateStatus'],
    'POST /admin/waitlist/delete' => ['AdminWaitlistController', 'delete'],
    'GET /admin/waitlist/export'  => ['AdminWaitlistController', 'export'],

    // Newsletter (multi-list email campaigns)
    'GET /admin/newsletter'                  => ['AdminNewsletterController', 'index'],
    'POST /admin/newsletter/send'            => ['AdminNewsletterController', 'send'],
    'GET /admin/newsletter/subscribers'      => ['AdminNewsletterController', 'subscribers'],
    'GET /admin/newsletter/subscribers/export' => ['AdminNewsletterController', 'exportSubscribers'],

    // Analytics
    'GET /admin/visitor-analytics' => ['VisitorAnalyticsController', 'index'],

    // Settings
    'GET /admin/settings' => ['SettingsController', 'index'],
    'POST /admin/settings/update' => ['SettingsController', 'update'],
    'GET /admin/settings/payment' => ['SettingsController', 'payment'],
    'POST /admin/settings/payment/save' => ['SettingsController', 'savePayment'],
    'GET /admin/settings/integrations' => ['SettingsController', 'integrations'],
    'POST /admin/settings/integrations/save' => ['SettingsController', 'saveIntegrations'],
    'POST /admin/settings/integrations/test' => ['SettingsController', 'testIntegration'],

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
    // DELIVERY PUBLIC ROUTES
    // ===================================
    'GET /delivery/apply'  => ['DeliveryController', 'apply'],
    'POST /delivery/apply' => ['DeliveryController', 'apply'],
    'GET /delivery/verify-email'       => ['DeliveryController', 'showVerifyEmail'],
    'GET /delivery/verify-email/auto'  => ['DeliveryController', 'autoVerifyDriverEmail'],
    'POST /delivery/verify-email'      => ['DeliveryController', 'verifyDriverEmail'],
    'POST /delivery/resend-verification' => ['DeliveryController', 'resendDriverVerification'],
    'GET /track'           => ['DeliveryController', 'track'],
    // Applicant portal
    'GET /delivery/application-status'                => ['DeliveryController', 'applicationStatus'],
    'GET /delivery/application-status/poll'           => ['DeliveryController', 'pollApplicationStatus'],
    'GET /delivery/documents'                          => ['DeliveryController', 'documents'],
    'GET /delivery/messages'                           => ['DeliveryController', 'messages'],
    'GET /delivery/emails'                             => ['DeliveryController', 'emails'],
    'POST /delivery/send-application-message'         => ['DeliveryController', 'sendApplicationMessage'],
    'POST /delivery/select-interview-time'            => ['DeliveryController', 'selectInterviewTime'],

    // ===================================
    // DELIVERY DRIVER ROUTES (auth required)
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
    // Driver Training
    'GET /delivery/training'              => ['DeliveryController', 'training'],
    'GET /delivery/training/module'       => ['DeliveryController', 'trainingModule'],
    'POST /delivery/training/quiz/submit' => ['DeliveryController', 'submitQuiz'],
    'GET /delivery/training/certificate'  => ['DeliveryController', 'certificate'],

    // First-login forced password reset
    'GET /delivery/change-password'     => ['DeliveryController', 'showChangePassword'],
    'POST /delivery/change-password'    => ['DeliveryController', 'processChangePassword'],

    // Driver profile
    'GET /delivery/profile'             => ['DeliveryController', 'profile'],
    'GET /delivery/settings'            => ['DeliveryController', 'settings'],
    'POST /delivery/profile/photo'      => ['DeliveryController', 'updatePhoto'],
    'POST /delivery/profile/password'   => ['DeliveryController', 'changePassword'],
    'POST /delivery/update-profile'     => ['DeliveryController', 'updateProfileAjax'],
    'POST /delivery/update-password'    => ['DeliveryController', 'updatePasswordAjax'],
    'POST /delivery/update-payment'     => ['DeliveryController', 'updatePaymentAjax'],

    // Background check — driver portal (authenticated)
    'GET /delivery/bgcheck'         => ['DeliveryController', 'bgcheck'],
    'POST /delivery/bgcheck/upload' => ['DeliveryController', 'bgcheckUpload'],

    // Background check — admin actions
    'GET /admin/delivery/bgcheck/download'  => ['BgcheckController', 'adminDownload'],
    'POST /admin/delivery/bgcheck/verify'   => ['AdminDeliveryController', 'verifyBgcheck'],
    'POST /admin/delivery/bgcheck/request'  => ['AdminDeliveryController', 'requestBgcheck'],
    'POST /admin/delivery/compliance/request' => ['AdminDeliveryController', 'requestComplianceDocs'],

    // Compliance documents — driver portal
    'GET /delivery/compliance'                  => ['DeliveryController', 'complianceDocs'],
    'POST /delivery/compliance/upload'          => ['DeliveryController', 'complianceUpload'],
    'POST /delivery/compliance/not-required'    => ['DeliveryController', 'complianceNotRequired'],

    // Compliance documents — admin download + review
    'GET /admin/delivery/compliance/download'   => ['AdminDeliveryController', 'complianceDownload'],
    'POST /admin/delivery/compliance/review'    => ['AdminDeliveryController', 'reviewComplianceDoc'],

    // ===================================
    // SUPPLIER PORTAL ROUTES
    // ===================================

    // Supplier Authentication
    'GET /supplier/login' => ['SupplierAuthController', 'login'],
    'POST /supplier/login' => ['SupplierAuthController', 'processLogin'],
    'GET /supplier/logout' => ['SupplierAuthController', 'logout'],
    'GET /supplier/forgot-password' => ['SupplierAuthController', 'forgotPassword'],
    'POST /supplier/forgot-password' => ['SupplierAuthController', 'sendResetLink'],
    'GET /supplier/reset-password' => ['SupplierAuthController', 'resetPassword'],
    'POST /supplier/reset-password' => ['SupplierAuthController', 'processResetPassword'],
    'GET /supplier/accept-invite' => ['SupplierAuthController', 'acceptInvite'],
    'POST /supplier/complete-registration' => ['SupplierAuthController', 'completeRegistration'],

    // Supplier Dashboard
    'GET /supplier/dashboard' => ['SupplierAuthController', 'dashboard'],

    // Supplier Settings
    'GET /supplier/settings' => ['SupplierAuthController', 'settings'],
    'POST /supplier/update-profile' => ['SupplierAuthController', 'updateProfile'],
    'POST /supplier/update-banking' => ['SupplierAuthController', 'updateBanking'],
    'POST /supplier/update-password' => ['SupplierAuthController', 'updatePassword'],
    'POST /supplier/dismiss-password-reminder' => ['SupplierAuthController', 'dismissPasswordReminder'],

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
    'POST /supplier/orders/start-preparing' => ['SupplierProductController', 'startPreparing'],
    'POST /supplier/orders/report-issue' => ['SupplierProductController', 'reportIssue'],
    'POST /supplier/orders/ready-for-pickup' => ['SupplierProductController', 'markReadyForPickup'],

    // Supplier Analytics
    'GET /supplier/analytics' => ['SupplierProductController', 'analytics'],

    // Supplier Sales Orders (accepted POs = SOs)
    'GET /supplier/sales-orders' => ['SupplierSalesOrderController', 'index'],

    // Supplier Receivables (what admin owes supplier)
    'GET /supplier/receivables' => ['SupplierReceivablesController', 'index'],

    // Supplier Invoices & Payments
    'GET /supplier/invoices' => ['SupplierProductController', 'invoices'],
    'GET /supplier/invoices/view' => ['SupplierProductController', 'viewInvoice'],
    'GET /supplier/invoices/download-pdf' => ['SupplierProductController', 'downloadInvoicePdf'],
    'GET /supplier/orders/download-pdf'   => ['SupplierProductController', 'downloadOrderPdf'],

    // Supplier Email History
    'GET /supplier/emails'      => ['SupplierProductController', 'emails'],

    // Distribution Email History
    'GET /distribution/emails'  => ['DistributionAuthController', 'emails'],

    // Supplier Messages
    'GET /supplier/messages'       => ['SupplierMessagesController', 'index'],
    'POST /supplier/messages/send' => ['SupplierMessagesController', 'send'],

    // Supplier Documents
    'GET /supplier/documents'                        => ['SupplierProductController', 'documents'],
    'POST /supplier/documents/upload'                => ['SupplierProductController', 'uploadDocument'],
    'GET /supplier/documents/agreement.pdf'          => ['SupplierProductController', 'agreementPdf'],
    'GET /supplier/documents/onboarding.pdf'         => ['SupplierProductController', 'onboardingPdf'],
    'POST /supplier/documents/confirm-agreement'     => ['SupplierProductController', 'confirmAgreement'],

    // Supplier Pickup Scheduling
    'GET /supplier/pickup'          => ['SupplierProductController', 'pickupIndex'],
    'POST /supplier/pickup/request' => ['SupplierProductController', 'pickupRequest'],
    'POST /supplier/pickup/cancel'  => ['SupplierProductController', 'pickupCancel'],

    // ===================================
    // DISTRIBUTION PORTAL ROUTES
    // ===================================

    // Distribution Landing & Auth
    'GET /distribution' => ['DistributionAuthController', 'landing'],
    'GET /distribution/login' => ['DistributionAuthController', 'showLogin'],
    'POST /distribution/login' => ['DistributionAuthController', 'login'],
    'GET /distribution/register' => ['DistributionAuthController', 'showRegister'],
    'POST /distribution/register' => ['DistributionAuthController', 'register'],
    'GET /distribution/verify-email'         => ['DistributionAuthController', 'showVerifyEmail'],
    'GET /distribution/verify-email/auto'   => ['DistributionAuthController', 'autoVerifyEmail'],
    'POST /distribution/verify-email'        => ['DistributionAuthController', 'verifyEmail'],
    'POST /distribution/resend-verification' => ['DistributionAuthController', 'resendVerification'],
    'GET /distribution/logout' => ['DistributionAuthController', 'logout'],

    // Distribution Dashboard
    'GET /distribution/dashboard' => ['DistributionAuthController', 'dashboard'],
    'GET /distribution/settings'  => ['DistributionAuthController', 'settings'],
    'POST /distribution/update-address'  => ['DistributionAuthController', 'updateAddress'],
    'POST /distribution/update-billing'  => ['DistributionAuthController', 'updateBilling'],
    'POST /distribution/update-payment'  => ['DistributionAuthController', 'updatePayment'],
    'POST /distribution/update-password' => ['DistributionAuthController', 'updatePassword'],
    'GET /distribution/documents'                    => ['DistributionAuthController', 'documents'],
    'POST /distribution/documents/upload'            => ['DistributionAuthController', 'uploadDocument'],
    'GET /distribution/documents/agreement.pdf'      => ['DistributionAuthController', 'agreementPdf'],
    'GET /distribution/documents/onboarding.pdf'     => ['DistributionAuthController', 'onboardingPdf'],
    'POST /distribution/documents/confirm-agreement' => ['DistributionAuthController', 'confirmAgreement'],

    // Distribution Messages
    'GET /distribution/messages'       => ['BusinessMessagesController', 'index'],
    'POST /distribution/messages/send' => ['BusinessMessagesController', 'send'],

    // Distribution Invoices
    'GET /distribution/invoices' => ['DistributionRequestController', 'invoices'],

    // Distribution Payables (what the business owes)
    'GET /distribution/payables' => ['DistributionPayablesController', 'index'],

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
    'POST /distribution/requests/confirm-price-change' => ['DistributionRequestController', 'confirmPriceChange'],
    'POST /distribution/requests/decline-price-change' => ['DistributionRequestController', 'declinePriceChange'],

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
    'GET /distribution/pay/done' => ['DistributionPaymentController', 'paymentDone'],
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
    // STATIC PAGES (faq + help only; others defined earlier)
    // ===================================
    'GET /faq' => ['PageController', 'faq'],
    'GET /help' => ['PageController', 'help'],

    // ===================================
    // API ROUTES (for AJAX calls)
    // ===================================
    'POST /api/newsletter/subscribe' => ['NewsletterController', 'subscribe'],
    // Token-based preference centre (manage individual lists / unsubscribe all)
    'GET /newsletter/preferences' => ['NewsletterController', 'preferences'],
    'POST /newsletter/preferences' => ['NewsletterController', 'updatePreferences'],
    'POST /newsletter/unsubscribe-all' => ['NewsletterController', 'unsubscribeAll'],
    // Legacy unsubscribe (email-based) — kept for old email footers
    'GET /unsubscribe' => ['NewsletterController', 'unsubscribePage'],
    'POST /unsubscribe' => ['NewsletterController', 'unsubscribe'],
    'GET /api/newsletter/unsubscribe' => ['NewsletterController', 'unsubscribe'],
    'GET /api/search/suggestions' => ['SearchController', 'suggestions'],

    // ===================================
    // PLANNER API ROUTES
    // ===================================
    // Notes
    'GET /api/planner/notes' => ['Api\\PlannerNotesController', 'index'],
    'POST /api/planner/notes' => ['Api\\PlannerNotesController', 'store'],
    'DELETE /api/planner/notes' => ['Api\\PlannerNotesController', 'destroy'],
    'PUT /api/planner/notes/archive' => ['Api\\PlannerNotesController', 'toggleArchive'],
    'GET /api/planner/notes/comments' => ['Api\\PlannerNotesController', 'getComments'],
    'POST /api/planner/notes/comments' => ['Api\\PlannerNotesController', 'storeComment'],

    // Todos
    'GET /api/planner/todos' => ['Api\\PlannerTodosController', 'index'],
    'POST /api/planner/todos' => ['Api\\PlannerTodosController', 'store'],
    'PUT /api/planner/todos' => ['Api\\PlannerTodosController', 'toggleComplete'],
    'DELETE /api/planner/todos' => ['Api\\PlannerTodosController', 'destroy'],
    'GET /api/planner/todos/comments' => ['Api\\PlannerTodosController', 'getComments'],
    'POST /api/planner/todos/comments' => ['Api\\PlannerTodosController', 'storeComment'],

    // Todo checklist items
    'POST /api/planner/todos/items' => ['Api\\PlannerTodosController', 'storeItem'],
    'PUT /api/planner/todos/items' => ['Api\\PlannerTodosController', 'toggleItem'],
    'DELETE /api/planner/todos/items' => ['Api\\PlannerTodosController', 'destroyItem'],

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
    'POST /api/planner/templates/categories' => ['Api\\PlannerTemplatesController', 'storeCategory'],
    'PUT /api/planner/templates/categories' => ['Api\\PlannerTemplatesController', 'updateCategory'],
    'DELETE /api/planner/templates/categories' => ['Api\\PlannerTemplatesController', 'deleteCategory'],
    'POST /api/planner/templates/pdf' => ['Api\\PlannerTemplatesController', 'generatePdf'],

    // PDF Generation
    'POST /api/pdf/generate' => ['Api\\PdfController', 'generate'],

    // Activity
    'GET /api/planner/activity' => ['Api\\PlannerActivityController', 'index'],

    // Users
    'GET /api/planner/users' => ['Api\\PlannerUsersController', 'index'],

    // Meetings - specific routes must come before general ones
    'GET /api/planner/meetings/team-members' => ['Api\\PlannerMeetingsController', 'getTeamMembers'],
    'GET /api/planner/meetings/previous' => ['Api\\PlannerMeetingsController', 'getPreviousMeetings'],
    'GET /api/planner/meetings/show' => ['Api\\PlannerMeetingsController', 'show'],
    'GET /api/planner/meetings/generate-email' => ['Api\\PlannerMeetingsController', 'generateEmail'],
    'POST /api/planner/meetings/send-email' => ['Api\\PlannerMeetingsController', 'sendEmail'],
    'POST /api/planner/meetings/item' => ['Api\\PlannerMeetingsController', 'addItem'],
    'DELETE /api/planner/meetings/item' => ['Api\\PlannerMeetingsController', 'deleteItem'],
    'POST /api/planner/meetings/action' => ['Api\\PlannerMeetingsController', 'addAction'],
    'PUT /api/planner/meetings/action' => ['Api\\PlannerMeetingsController', 'updateAction'],
    'DELETE /api/planner/meetings/action' => ['Api\\PlannerMeetingsController', 'deleteAction'],
    'GET /api/planner/meetings' => ['Api\\PlannerMeetingsController', 'index'],
    'POST /api/planner/meetings' => ['Api\\PlannerMeetingsController', 'store'],
    'PUT /api/planner/meetings' => ['Api\\PlannerMeetingsController', 'update'],
    'DELETE /api/planner/meetings' => ['Api\\PlannerMeetingsController', 'destroy'],

    // ===================================
    // ADMIN NOTIFICATIONS API
    // ===================================
    'GET /api/admin/notifications' => ['Api\\NotificationsController', 'index'],
    'GET /api/admin/notifications/count' => ['Api\\NotificationsController', 'count'],
    'GET /api/admin/notifications/stream' => ['Api\\NotificationsController', 'stream'],
    'POST /api/admin/notifications/mark-read' => ['Api\\NotificationsController', 'markRead'],
    'POST /api/admin/notifications/mark-all-read' => ['Api\\NotificationsController', 'markAllRead'],

    // ===================================
    // SUPPLIER MESSAGES API
    // ===================================
    'GET /api/supplier/messages/count'       => ['Api\\SupplierMessagesController', 'count'],
    'POST /api/supplier/messages/mark-read'  => ['Api\\SupplierMessagesController', 'markRead'],

    // ===================================
    // BUSINESS MESSAGES API
    // ===================================
    'GET /api/business/messages/count'       => ['Api\\BusinessMessagesController', 'count'],
    'POST /api/business/messages/mark-read'  => ['Api\\BusinessMessagesController', 'markRead'],

    // ===================================
    // BUSINESS NOTIFICATIONS API
    // ===================================
    'GET /api/business/notifications'             => ['Api\\BusinessNotificationsController', 'index'],
    'GET /api/business/notifications/count'       => ['Api\\BusinessNotificationsController', 'count'],
    'GET /api/business/notifications/stream'      => ['Api\\BusinessNotificationsController', 'stream'],
    'POST /api/business/notifications/mark-read'  => ['Api\\BusinessNotificationsController', 'markRead'],
    'POST /api/business/notifications/mark-all-read' => ['Api\\BusinessNotificationsController', 'markAllRead'],

    // Admin messages send + poll (AJAX)
    'POST /admin/suppliers/messages/send'    => ['SupplierMessagesController', 'adminSend'],
    'GET /api/admin/supplier-messages'       => ['SupplierMessagesController', 'apiGetMessages'],

    // ===================================
    // SUPPLIER NOTIFICATIONS API
    // ===================================
    'GET /api/supplier/notifications' => ['Api\\SupplierNotificationsController', 'index'],
    'GET /api/supplier/notifications/count' => ['Api\\SupplierNotificationsController', 'count'],
    'GET /api/supplier/notifications/stream' => ['Api\\SupplierNotificationsController', 'stream'],
    'POST /api/supplier/notifications/mark-read' => ['Api\\SupplierNotificationsController', 'markRead'],
    'POST /api/supplier/notifications/mark-all-read' => ['Api\\SupplierNotificationsController', 'markAllRead'],

    // ===================================
    // TWILIO API - CRM Communication Center
    // ===================================
    'GET /api/twilio/status' => ['Api\\TwilioController', 'status'],
    'GET /api/twilio/sms-templates' => ['Api\\TwilioController', 'getSMSTemplates'],
    'GET /api/twilio/communications' => ['Api\\TwilioController', 'getCommunications'],
    'POST /api/twilio/send-sms' => ['Api\\TwilioController', 'sendSMS'],
    'POST /api/twilio/make-call' => ['Api\\TwilioController', 'makeCall'],
    'POST /api/twilio/update-communication' => ['Api\\TwilioController', 'updateCommunicationOutcome'],

    // Twilio Webhooks (public, called by Twilio servers)
    'GET /api/twilio/voice-connect' => ['Api\\TwilioController', 'voiceConnect'],
    'POST /api/twilio/call-status' => ['Api\\TwilioController', 'callStatus'],
    'POST /api/twilio/sms-status' => ['Api\\TwilioController', 'smsStatus'],
    'POST /api/twilio/sms-webhook' => ['Api\\TwilioController', 'smsWebhook'],

    // ===================================
    // ODA — Driver Mobile App API
    // ===================================
    'POST /api/auth/login'                    => ['Api\\DriverApiController', 'login'],
    'POST /api/auth/logout'                   => ['Api\\DriverApiController', 'logout'],
    'GET /api/driver/profile'                 => ['Api\\DriverApiController', 'profile'],
    'GET /api/driver/compliance-docs'         => ['Api\\DriverApiController', 'complianceDocs'],
    'POST /api/driver/status'                 => ['Api\\DriverApiController', 'updateStatus'],
    'GET /api/orders/available'               => ['Api\\DriverApiController', 'availableOrders'],
    'GET /api/orders/{id}'                    => ['Api\\DriverApiController', 'getOrder'],
    'POST /api/orders/{id}/accept'            => ['Api\\DriverApiController', 'acceptOrder'],
    'POST /api/orders/{id}/status'            => ['Api\\DriverApiController', 'updateOrderStatus'],
    'POST /api/orders/{id}/photo'             => ['Api\\DriverApiController', 'orderPhoto'],
    'POST /api/orders/{id}/outcome'           => ['Api\\DriverApiController', 'orderOutcome'],
    'POST /api/orders/{id}/cancel'            => ['Api\\DriverApiController', 'cancelOrder'],
    'GET /api/driver/earnings'                => ['Api\\DriverApiController', 'earnings'],
    'GET /api/chat/messages'                  => ['Api\\DriverApiController', 'chatMessages'],
    'POST /api/chat/send'                     => ['Api\\DriverApiController', 'chatSend'],
    'POST /api/driver/location'               => ['Api\\DriverApiController', 'updateLocation'],
    'POST /api/driver/fcm-token'              => ['Api\\DriverApiController', 'saveFcmToken'],
    'POST /api/driver/photo'                  => ['Api\\DriverApiController', 'updatePhoto'],
    'POST /api/route'                         => ['Api\\DriverApiController', 'getRoute'],
    'GET /api/driver/notifications'                    => ['Api\\DriverApiController', 'notifications'],
    'POST /api/driver/notifications/{id}/read'         => ['Api\\DriverApiController', 'markNotificationRead'],
    'GET /api/driver/notifications/inbox'              => ['Api\\DriverNotificationsController', 'index'],
    'GET /api/driver/notifications/count'              => ['Api\\DriverNotificationsController', 'count'],
    'POST /api/driver/notifications/mark-read'         => ['Api\\DriverNotificationsController', 'markRead'],
    'POST /api/driver/notifications/mark-all-read'     => ['Api\\DriverNotificationsController', 'markAllRead'],
    'GET /api/pickups'                        => ['Api\\DriverApiController', 'listPickups'],
    'GET /api/pickups/{id}'                   => ['Api\\DriverApiController', 'pickupDetail'],
    'POST /api/pickups/{id}/accept'           => ['Api\\DriverApiController', 'acceptPickup'],
    'POST /api/pickups/{id}/decline'          => ['Api\\DriverApiController', 'declinePickup'],
    'POST /api/pickups/{id}/confirm'          => ['Api\\DriverApiController', 'confirmPickup'],
    'POST /api/pickups/{id}/complete'         => ['Api\\DriverApiController', 'completePickup'],
    'POST /api/distribution/{id}/complete'    => ['Api\\DriverApiController', 'completeDistributionDelivery'],
    'POST /api/distribution/{id}/step'        => ['Api\\DriverApiController', 'recordDistributionStep'],
    'GET /api/distribution/request/status'   => ['Api\\DistributionStatusController', 'status'],
    'GET /api/supplier/order/status'         => ['Api\\SupplierOrderStatusController', 'status'],
    'GET /api/admin/distribution/status'     => ['Api\\AdminDistributionStatusController', 'status'],
];
