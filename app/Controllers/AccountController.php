<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * AccountController - CLEAN & ORGANIZED
 * Complete user account management
 */
class AccountController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::getConnection();
    }
    
    // ==========================================
    // DASHBOARD
    // ==========================================
    
    /**
     * Account dashboard
     */
    public function index(): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=/account');
            return;
        }

        // Non-buyer roles have their own dashboards — redirect them there
        if (hasRole('seller')) {
            redirect(url('seller/dashboard'));
            return;
        }
        if (hasRole('delivery')) {
            redirect(url('delivery/dashboard'));
            return;
        }
        if (hasRole('admin') || hasRole('super_admin') || hasRole('admin_staff')) {
            redirect(url('admin/dashboard'));
            return;
        }

        $user = user();
        
        // Get order statistics
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'completed' OR status = 'delivered' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'completed' OR status = 'delivered' THEN total ELSE 0 END) as total_spent
                FROM orders
                WHERE user_id = :user_id
            ");
            $stmt->execute(['user_id' => userId()]);
            $orderStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            $orderStats = [
                'total_orders' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'total_spent' => 0
            ];
        }
        
        // Get recent orders (last 5)
        $recentOrders = [];
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, 
                       COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = :user_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 5
            ");
            $stmt->execute(['user_id' => userId()]);
            $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            logger("Error fetching orders: " . $e->getMessage(), 'warning');
        }
        
        // Get cart count
        $cartCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'] ?? 0;
            }
        }
        
        // Prepare stats
        $stats = [
            'total_orders' => intval($orderStats['total_orders'] ?? 0),
            'pending_orders' => intval($orderStats['pending_orders'] ?? 0),
            'completed_orders' => intval($orderStats['completed_orders'] ?? 0),
            'total_spent' => floatval($orderStats['total_spent'] ?? 0),
            'wishlist_count' => 0
        ];
        
        view('buyer/account/index', [
            'user' => $user,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'cartCount' => $cartCount,
            'pageTitle' => 'My Account'
        ]);
    }
    
    // ==========================================
    // ORDERS
    // ==========================================
    
    /**
     * Orders list page
     */
    public function orders(): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=/account/orders');
            return;
        }
        
        try {
            // Get filter from query string
            $statusFilter = sanitize(get('status', 'all'));
            
            // Build query
            $query = "
                SELECT o.*,
                       COUNT(oi.id) as item_count
                FROM orders o
                LEFT JOIN order_items oi ON o.id = oi.order_id
                WHERE o.user_id = :user_id
            ";
            
            $params = ['user_id' => userId()];
            
            // Add status filter if not 'all'
            if ($statusFilter !== 'all') {
                $query .= " AND o.status = :status";
                $params['status'] = $statusFilter;
            }
            
            $query .= " GROUP BY o.id ORDER BY o.created_at DESC";
            
            // Execute query
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get order stats
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status IN ('completed', 'delivered') THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM orders
                WHERE user_id = :user_id
            ");
            $stmt->execute(['user_id' => userId()]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            view('buyer/account/orders', [
                'orders' => $orders,
                'stats' => $stats,
                'cartCount' => $cartCount
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching orders: " . $e->getMessage(), 'error');
            
            view('buyer/account/orders', [
                'orders' => [],
                'stats' => ['total' => 0, 'pending' => 0, 'completed' => 0, 'cancelled' => 0],
                'cartCount' => 0
            ]);
        }
    }
    
    /**
     * Order detail page
     */
    public function orderDetail(): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=/account/orders');
            return;
        }
        
        // Get order ID from URL
        $orderId = intval(get('id', 0));
        
        if ($orderId === 0) {
            setFlash('error', 'Invalid order ID');
            redirect('/account/orders');
            return;
        }
        
        try {
            // Get order details
            $stmt = $this->db->prepare("
                SELECT * FROM orders
                WHERE id = :id AND user_id = :user_id
            ");
            $stmt->execute([
                'id' => $orderId,
                'user_id' => userId()
            ]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$order) {
                setFlash('error', 'Order not found');
                redirect('/account/orders');
                return;
            }
            
            // Get order items
            $stmt = $this->db->prepare("
                SELECT oi.*,
                       (SELECT image_path FROM product_images 
                        WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as product_image
                FROM order_items oi
                WHERE oi.order_id = :order_id
            ");
            $stmt->execute(['order_id' => $orderId]);
            $orderItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Parse delivery address
            $deliveryAddress = json_decode($order['delivery_address'] ?? '{}', true);
            
            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            view('buyer/account/order-detail', [
                'order' => $order,
                'orderItems' => $orderItems,
                'deliveryAddress' => $deliveryAddress,
                'cartCount' => $cartCount
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching order details: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading order details');
            redirect('/account/orders');
        }
    }
    
    // ==========================================
    // ADDRESSES
    // ==========================================
    
    /**
     * Addresses list page
     */
    public function addresses(): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=/account/addresses');
            return;
        }
        
        try {
            // Get all addresses for the user
            $stmt = $this->db->prepare("
                SELECT * FROM addresses
                WHERE user_id = :user_id
                ORDER BY is_default DESC, created_at DESC
            ");
            $stmt->execute(['user_id' => userId()]);
            $addresses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            view('buyer/account/addresses', [
                'addresses' => $addresses,
                'cartCount' => $cartCount
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching addresses: " . $e->getMessage(), 'error');
            view('buyer/account/addresses', [
                'addresses' => [],
                'cartCount' => 0
            ]);
        }
    }
    
    /**
     * Add new address
     */
    public function addAddress(): void
    {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login']);
            return;
        }
        
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        // Validate required fields
        $name = sanitize(post('name', ''));
        $type = sanitize(post('type', 'home'));
        $addressLine1 = sanitize(post('address_line_1', ''));
        $city = sanitize(post('city', ''));
        $phone = sanitize(post('phone', ''));
        
        if (empty($name) || empty($addressLine1) || empty($city) || empty($phone)) {
            jsonResponse(['success' => false, 'message' => 'Please fill in all required fields']);
            return;
        }
        
        try {
            // If this should be default, unset current default
            $isDefault = post('is_default', 0) ? 1 : 0;
            if ($isDefault) {
                $stmt = $this->db->prepare("
                    UPDATE addresses SET is_default = 0 
                    WHERE user_id = :user_id
                ");
                $stmt->execute(['user_id' => userId()]);
            }
            
            // Insert new address
            $stmt = $this->db->prepare("
                INSERT INTO addresses (
                    user_id, name, type, address_line_1, address_line_2,
                    city, state, postal_code, phone, is_default,
                    created_at, updated_at
                ) VALUES (
                    :user_id, :name, :type, :address_line_1, :address_line_2,
                    :city, :state, :postal_code, :phone, :is_default,
                    NOW(), NOW()
                )
            ");
            
            $stmt->execute([
                'user_id' => userId(),
                'name' => $name,
                'type' => $type,
                'address_line_1' => $addressLine1,
                'address_line_2' => sanitize(post('address_line_2', '')),
                'city' => $city,
                'state' => sanitize(post('state', '')),
                'postal_code' => sanitize(post('postal_code', '')),
                'phone' => $phone,
                'is_default' => $isDefault
            ]);
            
            jsonResponse(['success' => true, 'message' => 'Address added successfully']);
            
        } catch (\PDOException $e) {
            logger("Error adding address: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to add address']);
        }
    }
    
    /**
     * Update address
     */
    public function updateAddress(): void
    {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login']);
            return;
        }
        
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        $addressId = intval(post('id', 0));
        
        if ($addressId === 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid address ID']);
            return;
        }
        
        // Validate required fields
        $name = sanitize(post('name', ''));
        $type = sanitize(post('type', 'home'));
        $addressLine1 = sanitize(post('address_line_1', ''));
        $city = sanitize(post('city', ''));
        $phone = sanitize(post('phone', ''));
        
        if (empty($name) || empty($addressLine1) || empty($city) || empty($phone)) {
            jsonResponse(['success' => false, 'message' => 'Please fill in all required fields']);
            return;
        }
        
        try {
            // Verify ownership
            $stmt = $this->db->prepare("SELECT id FROM addresses WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $addressId, 'user_id' => userId()]);
            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Address not found']);
                return;
            }
            
            // If this should be default, unset current default
            $isDefault = post('is_default', 0) ? 1 : 0;
            if ($isDefault) {
                $stmt = $this->db->prepare("
                    UPDATE addresses SET is_default = 0 
                    WHERE user_id = :user_id AND id != :id
                ");
                $stmt->execute(['user_id' => userId(), 'id' => $addressId]);
            }
            
            // Update address
            $stmt = $this->db->prepare("
                UPDATE addresses SET
                    name = :name,
                    type = :type,
                    address_line_1 = :address_line_1,
                    address_line_2 = :address_line_2,
                    city = :city,
                    state = :state,
                    postal_code = :postal_code,
                    phone = :phone,
                    is_default = :is_default,
                    updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->execute([
                'id' => $addressId,
                'user_id' => userId(),
                'name' => $name,
                'type' => $type,
                'address_line_1' => $addressLine1,
                'address_line_2' => sanitize(post('address_line_2', '')),
                'city' => $city,
                'state' => sanitize(post('state', '')),
                'postal_code' => sanitize(post('postal_code', '')),
                'phone' => $phone,
                'is_default' => $isDefault
            ]);
            
            jsonResponse(['success' => true, 'message' => 'Address updated successfully']);
            
        } catch (\PDOException $e) {
            logger("Error updating address: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to update address']);
        }
    }
    
    /**
     * Delete address
     */
    public function deleteAddress(): void
    {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login']);
            return;
        }
        
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        $addressId = intval(post('id', 0));
        
        if ($addressId === 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid address ID']);
            return;
        }
        
        try {
            // Verify ownership
            $stmt = $this->db->prepare("SELECT id FROM addresses WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $addressId, 'user_id' => userId()]);
            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Address not found']);
                return;
            }
            
            // Delete address
            $stmt = $this->db->prepare("DELETE FROM addresses WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $addressId, 'user_id' => userId()]);
            
            jsonResponse(['success' => true, 'message' => 'Address deleted successfully']);
            
        } catch (\PDOException $e) {
            logger("Error deleting address: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to delete address']);
        }
    }
    
    /**
     * Set default address
     */
    public function setDefaultAddress(): void
    {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login']);
            return;
        }
        
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        $addressId = intval(post('id', 0));
        
        if ($addressId === 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid address ID']);
            return;
        }
        
        try {
            // Verify ownership
            $stmt = $this->db->prepare("SELECT id FROM addresses WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $addressId, 'user_id' => userId()]);
            if (!$stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Address not found']);
                return;
            }
            
            // Unset all defaults
            $stmt = $this->db->prepare("UPDATE addresses SET is_default = 0 WHERE user_id = :user_id");
            $stmt->execute(['user_id' => userId()]);
            
            // Set new default
            $stmt = $this->db->prepare("UPDATE addresses SET is_default = 1 WHERE id = :id AND user_id = :user_id");
            $stmt->execute(['id' => $addressId, 'user_id' => userId()]);
            
            jsonResponse(['success' => true, 'message' => 'Default address updated']);
            
        } catch (\PDOException $e) {
            logger("Error setting default address: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to set default address']);
        }
    }
    
    // ==========================================
    // WISHLIST (Placeholders)
    // ==========================================
    
    /**
     * Wishlist page
     */
    public function wishlist(): void
    {
        if (!isLoggedIn()) {
            redirect('/login?redirect=/account/wishlist');
            return;
        }
        
        $user = user();
        $wishlist = [];
        
        $cartCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'] ?? 0;
            }
        }
        
        view('buyer/account/wishlist', [
            'user' => $user,
            'wishlist' => $wishlist,
            'cartCount' => $cartCount
        ]);
    }
    
    // ==========================================
    // SETTINGS
    // ==========================================
    
    /**
     * Settings page
     */
    public function settings(): void
{
    if (!isLoggedIn()) {
        redirect(url('login'));
        return;
    }
    
    $userId = userId();
    
    try {
        $db = \Database::getConnection();
        
        // Get user data
        $stmt = $db->prepare("
            SELECT id, first_name, last_name, email, phone, role, created_at
            FROM users
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            setFlash('error', 'User not found');
            redirect(url('account'));
            return;
        }
        
        // Get user preferences
        $stmt = $db->prepare("
            SELECT email_orders, email_promotions, email_newsletter
            FROM user_preferences
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $preferences = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Default preferences if not set
        if (!$preferences) {
            $preferences = [
                'email_orders' => 1,
                'email_promotions' => 1,
                'email_newsletter' => 0
            ];
        }
        
        view('buyer/account/settings', [
            'user' => $user,
            'preferences' => $preferences,
            'cartCount' => getCartCount()
        ]);
        
    } catch (\PDOException $e) {
        logger("Settings page error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error loading settings');
        redirect(url('account'));
    }
}

/**
 * Update user password
 */
public function updatePassword(): void
{
    error_log("=== UPDATE PASSWORD CALLED ===");
    
    if (!isPost()) {
        setFlash('error', 'Invalid request method');
        redirect(url('account/settings'));
        return;
    }
    
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in');
        redirect(url('login'));
        return;
    }
    
    // Verify CSRF token
    $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
    if (!verifyCsrfToken($token)) {
        error_log("CSRF verification failed");
        setFlash('error', 'Invalid security token. Please try again.');
        redirect(url('account/settings'));
        return;
    }
    
    $userId = userId();
    $currentPassword = post('current_password', '');
    $newPassword = post('new_password', '');
    $confirmPassword = post('confirm_password', '');
    
    error_log("User ID: $userId");
    error_log("Current password provided: " . (strlen($currentPassword) > 0 ? 'YES' : 'NO'));
    error_log("New password length: " . strlen($newPassword));
    
    // Validation
    if (empty($currentPassword)) {
        setFlash('error', 'Current password is required');
        redirect(url('account/settings'));
        return;
    }
    
    if (empty($newPassword)) {
        setFlash('error', 'New password is required');
        redirect(url('account/settings'));
        return;
    }
    
    if (strlen($newPassword) < 8) {
        setFlash('error', 'New password must be at least 8 characters');
        redirect(url('account/settings'));
        return;
    }
    
    if (strlen($newPassword) > 72) {
        setFlash('error', 'New password must be less than 72 characters');
        redirect(url('account/settings'));
        return;
    }
    
    if ($newPassword !== $confirmPassword) {
        setFlash('error', 'New passwords do not match');
        redirect(url('account/settings'));
        return;
    }
    
    try {
        $db = \Database::getConnection();
        
        // Get current password hash
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$user) {
            error_log("User not found: $userId");
            setFlash('error', 'User not found');
            redirect(url('account/settings'));
            return;
        }
        
        // Verify current password
        $passwordVerified = password_verify($currentPassword, $user['password']);
        error_log("Current password verified: " . ($passwordVerified ? 'YES' : 'NO'));
        
        if (!$passwordVerified) {
            setFlash('error', 'Current password is incorrect');
            redirect(url('account/settings'));
            return;
        }
        
        // Check if new password is same as current
        if (password_verify($newPassword, $user['password'])) {
            setFlash('error', 'New password must be different from current password');
            redirect(url('account/settings'));
            return;
        }
        
        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        error_log("New password hash created");
        
        // Update password
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $result = $stmt->execute([$newPasswordHash, $userId]);
        
        error_log("Password update result: " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("Rows affected: " . $stmt->rowCount());
        
        if ($result && $stmt->rowCount() > 0) {
            error_log("Password updated successfully for user $userId");
            setFlash('success', 'Password updated successfully!');
        } else {
            error_log("Password update failed - no rows affected");
            setFlash('error', 'Failed to update password');
        }
        
    } catch (\PDOException $e) {
        error_log("PDO Error in updatePassword: " . $e->getMessage());
        setFlash('error', 'Database error: ' . $e->getMessage());
    } catch (\Exception $e) {
        error_log("General Error in updatePassword: " . $e->getMessage());
        setFlash('error', 'Error updating password: ' . $e->getMessage());
    }
    
    redirect(url('account/settings'));
}

/**
 * Update notification preferences
 */
public function updateNotifications(): void
{
    error_log("=== UPDATE NOTIFICATIONS CALLED ===");
    
    if (!isPost()) {
        setFlash('error', 'Invalid request method');
        redirect(url('account/settings'));
        return;
    }
    
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in');
        redirect(url('login'));
        return;
    }
    
    // Verify CSRF token
    $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
    if (!verifyCsrfToken($token)) {
        error_log("CSRF verification failed");
        setFlash('error', 'Invalid security token. Please try again.');
        redirect(url('account/settings'));
        return;
    }
    
    $userId = userId();
    
    // Get checkbox values (checkboxes only send value if checked)
    $emailOrders = isset($_POST['email_orders']) ? 1 : 0;
    $emailPromotions = isset($_POST['email_promotions']) ? 1 : 0;
    $emailNewsletter = isset($_POST['email_newsletter']) ? 1 : 0;
    
    error_log("User ID: $userId");
    error_log("Email Orders: $emailOrders");
    error_log("Email Promotions: $emailPromotions");
    error_log("Email Newsletter: $emailNewsletter");
    
    try {
        $db = \Database::getConnection();
        
        // Create table if not exists
        $this->createUserPreferencesTable();
        
        // Check if preferences exist
        $stmt = $db->prepare("SELECT id FROM user_preferences WHERE user_id = ?");
        $stmt->execute([$userId]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing preferences
            error_log("Updating existing preferences");
            $stmt = $db->prepare("
                UPDATE user_preferences 
                SET email_orders = ?,
                    email_promotions = ?,
                    email_newsletter = ?
                WHERE user_id = ?
            ");
            $result = $stmt->execute([
                $emailOrders,
                $emailPromotions,
                $emailNewsletter,
                $userId
            ]);
        } else {
            // Insert new preferences
            error_log("Inserting new preferences");
            $stmt = $db->prepare("
                INSERT INTO user_preferences 
                (user_id, email_orders, email_promotions, email_newsletter)
                VALUES (?, ?, ?, ?)
            ");
            $result = $stmt->execute([
                $userId,
                $emailOrders,
                $emailPromotions,
                $emailNewsletter
            ]);
        }
        
        error_log("Preferences update result: " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("Rows affected: " . $stmt->rowCount());
        
        if ($result) {
            error_log("Notification preferences updated for user $userId");
            setFlash('success', 'Notification preferences saved!');
        } else {
            error_log("Preferences update failed");
            setFlash('error', 'Failed to save preferences');
        }
        
    } catch (\PDOException $e) {
        error_log("PDO Error in updateNotifications: " . $e->getMessage());
        setFlash('error', 'Database error: ' . $e->getMessage());
    } catch (\Exception $e) {
        error_log("General Error in updateNotifications: " . $e->getMessage());
        setFlash('error', 'Error saving preferences: ' . $e->getMessage());
    }
    
    redirect(url('account/settings'));
}

/**
 * Create user_preferences table if it doesn't exist
 */
private function createUserPreferencesTable(): void
{
    try {
        $db = \Database::getConnection();
        
        $db->exec("
            CREATE TABLE IF NOT EXISTS user_preferences (
                id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                user_id BIGINT UNSIGNED NOT NULL,
                email_orders TINYINT(1) DEFAULT 1,
                email_promotions TINYINT(1) DEFAULT 1,
                email_newsletter TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_user (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        
        error_log("user_preferences table checked/created");
        
    } catch (\PDOException $e) {
        error_log("Error creating user_preferences table: " . $e->getMessage());
        // Don't throw - table might already exist
    }
}
/**
 * Update user profile information
 */
public function updateProfile(): void
{
    // Log the attempt
    error_log("=== UPDATE PROFILE CALLED ===");
    
    if (!isPost()) {
        setFlash('error', 'Invalid request method');
        redirect(url('account/settings'));
        return;
    }
    
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in');
        redirect(url('login'));
        return;
    }
    
    // Verify CSRF token
    $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
    if (!verifyCsrfToken($token)) {
        error_log("CSRF verification failed");
        setFlash('error', 'Invalid security token. Please try again.');
        redirect(url('account/settings'));
        return;
    }
    
    $userId = userId();
    $firstName = trim(post('first_name', ''));
    $lastName = trim(post('last_name', ''));
    $phone = trim(post('phone', ''));
    
    error_log("User ID: $userId");
    error_log("First Name: $firstName");
    error_log("Last Name: $lastName");
    error_log("Phone: $phone");
    
    // Validation
    if (empty($firstName)) {
        setFlash('error', 'First name is required');
        redirect(url('account/settings'));
        return;
    }
    
    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        setFlash('error', 'First name must be between 2 and 50 characters');
        redirect(url('account/settings'));
        return;
    }
    
    if (empty($lastName)) {
        setFlash('error', 'Last name is required');
        redirect(url('account/settings'));
        return;
    }
    
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        setFlash('error', 'Last name must be between 2 and 50 characters');
        redirect(url('account/settings'));
        return;
    }
    
    if (!empty($phone) && !preg_match('/^[\d\s\-\+\(\)]+$/', $phone)) {
        setFlash('error', 'Invalid phone number format');
        redirect(url('account/settings'));
        return;
    }
    
    try {
        $db = \Database::getConnection();
        
        // First verify user exists
        $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        if (!$stmt->fetch()) {
            error_log("User not found: $userId");
            setFlash('error', 'User not found');
            redirect(url('account/settings'));
            return;
        }
        
        // Update user profile
        $stmt = $db->prepare("
            UPDATE users 
            SET first_name = ?,
                last_name = ?,
                phone = ?
            WHERE id = ?
        ");
        
        $result = $stmt->execute([
            $firstName,
            $lastName,
            $phone ?: null,
            $userId
        ]);
        
        error_log("Update result: " . ($result ? 'SUCCESS' : 'FAILED'));
        error_log("Rows affected: " . $stmt->rowCount());
        
        if ($result && $stmt->rowCount() >= 0) {
            // Update session data
            $_SESSION['user']['first_name'] = $firstName;
            $_SESSION['user']['last_name'] = $lastName;
            $_SESSION['user']['phone'] = $phone;
            
            error_log("Profile updated successfully for user $userId");
            setFlash('success', 'Profile updated successfully!');
        } else {
            error_log("Update failed - no rows affected");
            setFlash('error', 'No changes were made to your profile');
        }
        
    } catch (\PDOException $e) {
        error_log("PDO Error in updateProfile: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        setFlash('error', 'Database error: ' . $e->getMessage());
    } catch (\Exception $e) {
        error_log("General Error in updateProfile: " . $e->getMessage());
        setFlash('error', 'Error updating profile: ' . $e->getMessage());
    }
    
    redirect(url('account/settings'));
}
    
    /**
     * Update account settings
     */
    public function updateSettings(): void
    {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        // Validate CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Please login']);
            return;
        }
        
        $name = sanitize(post('name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        
        // Validation
        if (empty($name)) {
            jsonResponse(['success' => false, 'message' => 'Name is required']);
            return;
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(['success' => false, 'message' => 'Valid email is required']);
            return;
        }
        
        try {
            // Check if email is taken
            $stmt = $this->db->prepare("
                SELECT id FROM users
                WHERE email = :email AND id != :user_id
            ");
            $stmt->execute([
                'email' => $email,
                'user_id' => userId()
            ]);
            
            if ($stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Email already in use']);
                return;
            }
            
            // Update user
            $stmt = $this->db->prepare("
                UPDATE users
                SET name = :name,
                    email = :email,
                    phone = :phone,
                    updated_at = NOW()
                WHERE id = :user_id
            ");
            
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'user_id' => userId()
            ]);
            
            // Update session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            
            jsonResponse(['success' => true, 'message' => 'Settings updated successfully']);

        } catch (\PDOException $e) {
            logger("Settings update failed: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to update settings']);
        }
    }

    /**
     * Show the "Become a Seller" application form
     * For existing buyers who want to upgrade to seller status
     */
    public function becomeSeller(): void {
        AuthMiddleware::auth();

        $userId = userId();

        // Get current user info
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        // Check role via both users.role column and user_roles table (authoritative)
        $roleStmt = $this->db->prepare("
            SELECT r.name FROM roles r
            INNER JOIN user_roles ur ON r.id = ur.role_id
            WHERE ur.user_id = ? LIMIT 1
        ");
        $roleStmt->execute([$userId]);
        $actualRole = $roleStmt->fetchColumn() ?: ($user['role'] ?? 'buyer');

        if ($actualRole === 'seller') {
            setFlash('info', 'You already have a seller account.');
            redirect(url('seller/dashboard'));
            return;
        }

        // Check if there's a pending application
        $stmt = $this->db->prepare("
            SELECT * FROM seller_applications
            WHERE user_id = ? AND status = 'pending'
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        $pendingApplication = $stmt->fetch();

        if ($pendingApplication) {
            setFlash('info', 'You already have a pending seller application. We will review it shortly.');
        }

        view('account.become-seller', [
            'user' => $user,
            'pendingApplication' => $pendingApplication
        ]);
    }

    /**
     * Process the seller application form submission
     */
    public function submitSellerApplication(): void {
        AuthMiddleware::auth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('account/become-seller'));
            return;
        }

        $userId = userId();

        // Validate required fields
        $businessName = sanitize(post('business_name', ''));
        $businessType = sanitize(post('business_type', ''));
        $businessAddress = sanitize(post('business_address', ''));
        $city = sanitize(post('city', ''));
        $province = sanitize(post('province', ''));
        $postalCode = strtoupper(sanitize(post('postal_code', '')));
        $businessPhone = sanitize(post('business_phone', ''));
        $productDescription = sanitize(post('product_description', ''));
        $businessRegistration = sanitize(post('business_registration', ''));
        $taxNumber = sanitize(post('tax_number', ''));
        $agreeTerms = post('agree_terms', false);

        // Validation
        $errors = [];
        if (empty($businessName)) $errors[] = 'Business name is required';
        if (empty($businessType)) $errors[] = 'Business type is required';
        if (empty($businessAddress)) $errors[] = 'Business address is required';
        if (empty($city)) $errors[] = 'City is required';
        if (empty($province)) $errors[] = 'Province is required';
        if (empty($postalCode)) $errors[] = 'Postal code is required';
        if (empty($businessPhone)) $errors[] = 'Business phone is required';
        if (empty($productDescription)) $errors[] = 'Product description is required';
        if (!$agreeTerms) $errors[] = 'You must agree to the terms';

        if (!empty($errors)) {
            setFlash('error', implode('. ', $errors));
            redirect(url('account/become-seller'));
            return;
        }

        try {
            // Check for existing pending application
            $stmt = $this->db->prepare("
                SELECT id FROM seller_applications
                WHERE user_id = ? AND status = 'pending'
            ");
            $stmt->execute([$userId]);
            if ($stmt->fetch()) {
                setFlash('error', 'You already have a pending application.');
                redirect(url('account/become-seller'));
                return;
            }

            // Create seller_applications table if it doesn't exist
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS seller_applications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    business_name VARCHAR(255) NOT NULL,
                    business_type VARCHAR(100) NOT NULL,
                    business_registration VARCHAR(100),
                    tax_number VARCHAR(100),
                    business_address TEXT NOT NULL,
                    city VARCHAR(100) NOT NULL,
                    province VARCHAR(50) NOT NULL,
                    postal_code VARCHAR(20) NOT NULL,
                    business_phone VARCHAR(50) NOT NULL,
                    product_description TEXT,
                    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                    admin_notes TEXT,
                    reviewed_by INT,
                    reviewed_at DATETIME,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ");

            // Insert application
            $stmt = $this->db->prepare("
                INSERT INTO seller_applications
                (user_id, business_name, business_type, business_registration, tax_number,
                 business_address, city, province, postal_code, business_phone, product_description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $businessName,
                $businessType,
                $businessRegistration,
                $taxNumber,
                $businessAddress,
                $city,
                $province,
                $postalCode,
                $businessPhone,
                $productDescription
            ]);

            // Update user role to 'seller' with pending status so admin pipeline works correctly
            $this->db->prepare("UPDATE users SET role = 'seller', status = 'pending' WHERE id = ?")->execute([$userId]);

            // Sync user_roles table
            $roleRow = $this->db->prepare("SELECT id FROM roles WHERE name = 'seller' LIMIT 1");
            $roleRow->execute();
            $sellerRole = $roleRow->fetch();
            if ($sellerRole) {
                $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE role_id = VALUES(role_id)
                ")->execute([$userId, $sellerRole['id']]);
            }

            // Update session so redirect works immediately
            $_SESSION['user']['role'] = 'seller';
            $_SESSION['user']['status'] = 'pending';

            // Admin bell notification
            \App\Helpers\NotificationHelper::sellerApplication([
                'email'      => user()['email'] ?? '',
                'first_name' => user()['first_name'] ?? '',
                'last_name'  => user()['last_name'] ?? '',
            ]);

            logger("Seller application submitted by user {$userId}: {$businessName}", 'info');

            setFlash('success', 'Your seller application has been submitted successfully! We will review it and contact you within 2-3 business days.');
            redirect(url('account'));

        } catch (\PDOException $e) {
            logger("Seller application error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('account/become-seller'));
        }
    }
}