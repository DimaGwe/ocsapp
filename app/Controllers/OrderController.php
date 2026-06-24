<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * OrderController - Central hub for all order operations
 * Handles orders for Customers, Sellers, and Admin
 */
class OrderController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::getConnection();
    }
    
    // ========================================
    // CUSTOMER ORDER METHODS
    // ========================================
    
    /**
     * Customer: View their order history
     */
    public function myOrders(): void
    {
        if (!isLoggedIn()) {
            redirect(url('login'));
            return;
        }
        
        $userId = userId();
        $page = intval(get('page', 1));
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Get filter parameters
        $status = sanitize(get('status', ''));
        $search = sanitize(get('search', ''));
        
        try {
            // Build query with filters
            $where = "WHERE o.user_id = :user_id";
            $params = ['user_id' => $userId];
            
            if (!empty($status)) {
                $where .= " AND o.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($search)) {
                $where .= " AND (o.order_number LIKE :search OR s.name LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM orders o 
                          LEFT JOIN shops s ON o.shop_id = s.id 
                          $where";
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute($params);
            $totalOrders = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get orders
            $query = "
                SELECT 
                    o.*,
                    s.name as shop_name,
                    s.logo as shop_logo,
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
                FROM orders o
                LEFT JOIN shops s ON o.shop_id = s.id
                $where
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Calculate pagination
            $totalPages = ceil($totalOrders / $limit);
            
            view('buyer/account/orders', [
                'orders' => $orders,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'status' => $status,
                'search' => $search
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching orders: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to load orders');
            view('buyer/account/orders', ['orders' => []]);
        }
    }
    
    /**
     * Customer: View single order detail
     */
    public function orderDetail(): void
{
    if (!isLoggedIn()) {
        redirect(url('login'));
        return;
    }
    
    $userId = userId();
    $orderId = intval(get('id', 0));
    
    if (!$orderId) {
        setFlash('error', 'Invalid order');
        redirect(url('account/orders'));
        return;
    }
    
    try {
        // Get order with shop details
        $stmt = $this->db->prepare("
            SELECT 
                o.*,
                s.name as shop_name,
                s.logo as shop_logo,
                s.phone as shop_phone,
                s.email as shop_email,
                s.address as shop_address
            FROM orders o
            LEFT JOIN shops s ON o.shop_id = s.id
            WHERE o.id = :id AND o.user_id = :user_id
        ");
        
        $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$order) {
            setFlash('error', 'Order not found');
            redirect(url('account/orders'));
            return;
        }
        
        // Get order items with product details
        $stmt = $this->db->prepare("
            SELECT 
                oi.*,
                p.slug as product_slug,
                (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as image_path
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        
        $stmt->execute(['order_id' => $orderId]);
        $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get order status history - FIXED QUERY
        $statusHistory = [];
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    dsh.*,
                    u.first_name,
                    u.last_name
                FROM delivery_status_history dsh
                LEFT JOIN users u ON dsh.changed_by = u.id
                WHERE dsh.order_id = :order_id
                ORDER BY dsh.created_at DESC
            ");
            
            $stmt->execute(['order_id' => $orderId]);
            $statusHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log but don't fail - status history is optional
            logger("Status history query failed: " . $e->getMessage(), 'warning');
        }
        
        // Get delivery assignment if exists - OPTIONAL
        $delivery = null;
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    da.*,
                    u.first_name as driver_first_name,
                    u.last_name as driver_last_name,
                    u.phone as driver_phone
                FROM delivery_assignments da
                LEFT JOIN users u ON da.driver_id = u.id
                WHERE da.order_id = :order_id
                ORDER BY da.created_at DESC
                LIMIT 1
            ");
            
            $stmt->execute(['order_id' => $orderId]);
            $delivery = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log but don't fail - delivery assignment is optional
            logger("Delivery assignment query failed: " . $e->getMessage(), 'warning');
        }
        
        // Driver rating: fetch existing rating if order is delivered and has driver
        $driverRating = null;
        $driverName = null;
        $ratingDriverId = $order['driver_id'] ?? ($delivery['driver_id'] ?? null);
        if (($order['status'] ?? '') === 'delivered' && $ratingDriverId) {
            // Get driver name
            $driverStmt = $this->db->prepare(
                "SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1"
            );
            $driverStmt->execute([$ratingDriverId]);
            $driverRow = $driverStmt->fetch(\PDO::FETCH_ASSOC);
            if ($driverRow) {
                $driverName = trim($driverRow['first_name'] . ' ' . $driverRow['last_name']);
            }

            // Check for existing rating
            try {
                $ratingStmt = $this->db->prepare(
                    "SELECT rating, comment FROM driver_ratings WHERE order_id = ? AND customer_id = ? LIMIT 1"
                );
                $ratingStmt->execute([$orderId, $userId]);
                $driverRating = $ratingStmt->fetch(\PDO::FETCH_ASSOC) ?: null;
            } catch (\PDOException $e) {
                // driver_ratings table may not exist yet — silently skip
            }
        }

        view('buyer/account/order-detail', [
            'order' => $order,
            'items' => $items,
            'statusHistory' => $statusHistory,
            'delivery' => $delivery,
            'ratingDriverId' => $ratingDriverId,
            'driverName' => $driverName,
            'driverRating' => $driverRating,
        ]);
        
    } catch (\PDOException $e) {
        logger("Error fetching order detail: " . $e->getMessage(), 'error');
        setFlash('error', 'Failed to load order details');
        redirect(url('account/orders'));
    }
}

    
    /**
     * Customer: Cancel order
     */
    public function cancelOrder(): void
    {
        if (!isLoggedIn()) {
            jsonResponse(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        verifyCsrfForApi();

        $userId = userId();
        $orderId = intval(post('order_id', 0));
        $reason = sanitize(post('reason', 'Customer requested cancellation'));
        
        if (!$orderId) {
            jsonResponse(['success' => false, 'message' => 'Invalid order']);
            return;
        }
        
        try {
            $this->db->beginTransaction();
            
            // Verify order belongs to user and can be cancelled
            $stmt = $this->db->prepare("
                SELECT * FROM orders 
                WHERE id = :id AND user_id = :user_id 
                AND status IN ('pending', 'confirmed')
            ");
            
            $stmt->execute(['id' => $orderId, 'user_id' => $userId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$order) {
                $this->db->rollBack();
                jsonResponse(['success' => false, 'message' => 'Order cannot be cancelled']);
                return;
            }
            
            // Update order status
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET status = 'cancelled',
                    cancellation_reason = :reason,
                    cancelled_at = NOW(),
                    cancelled_by = :user_id,
                    updated_at = NOW()
                WHERE id = :id
            ");
            
            $stmt->execute([
                'reason' => $reason,
                'user_id' => $userId,
                'id' => $orderId
            ]);
            
            // Add to status history
            $stmt = $this->db->prepare("
                INSERT INTO delivery_status_history 
                (order_id, old_status, new_status, changed_by, notes, created_at)
                VALUES (:order_id, :old_status, 'cancelled', :user_id, :reason, NOW())
            ");
            
            $stmt->execute([
                'order_id' => $orderId,
                'old_status' => $order['status'],
                'user_id' => $userId,
                'reason' => $reason
            ]);
            
            // Restore inventory stock
            $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
            $stmt->execute(['order_id' => $orderId]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($items as $item) {
                if ($item['shop_inventory_id']) {
                    // Restore stock to shop_inventory
                    $stmt = $this->db->prepare("
                        UPDATE shop_inventory
                        SET stock_quantity = stock_quantity + :quantity,
                            sold_quantity = sold_quantity - :quantity,
                            updated_at = NOW()
                        WHERE id = :shop_inventory_id
                    ");

                    $stmt->execute([
                        'quantity' => $item['quantity'],
                        'shop_inventory_id' => $item['shop_inventory_id']
                    ]);

                    // Log stock movement
                    $stmt = $this->db->prepare("
                        INSERT INTO stock_movements
                        (shop_inventory_id, type, quantity, reference_type, reference_id, reason, created_at)
                        VALUES (:shop_inventory_id, 'return', :quantity, 'order', :order_id, :reason, NOW())
                    ");

                    $stmt->execute([
                        'shop_inventory_id' => $item['shop_inventory_id'],
                        'quantity' => $item['quantity'],
                        'order_id' => $orderId,
                        'reason' => "Order #{$order['order_number']} cancelled: {$reason}"
                    ]);

                    logger("Restored {$item['quantity']} units to shop_inventory #{$item['shop_inventory_id']} for cancelled order", 'info');
                }
            }
            
            $this->db->commit();
            
            // Send cancellation email to customer
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                
                // Get complete order data
                $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
                $stmt->execute(['id' => $orderId]);
                $order = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($order) {
                    $cancelReason = $reason ?: 'Order cancelled by customer';
                    \App\Helpers\EmailHelper::sendOrderCancelled($order, $cancelReason);
                    logger("Order cancellation email sent for order #{$order['order_number']}", 'info');
                }
            } catch (\Exception $e) {
                logger("Failed to send cancellation email: " . $e->getMessage(), 'warning');
            }
            
            logger("Order #{$order['order_number']} cancelled by customer", 'info');
            
            jsonResponse([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
            
        } catch (\PDOException $e) {
            $this->db->rollBack();
            logger("Error cancelling order: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to cancel order']);
        }
    }
    
    /**
     * Customer: Rate the delivery driver after delivery
     */
    public function rateDriver(): void
    {
        if (!isLoggedIn()) {
            redirect(url('login'));
            return;
        }

        $userId  = userId();
        $orderId = intval(post('order_id', 0));
        $rating  = intval(post('rating', 0));
        $comment = sanitize(post('comment', ''));

        if (!$orderId || $rating < 1 || $rating > 5) {
            setFlash('error', 'Invalid rating submission.');
            redirect(url("account/orders/detail?id=$orderId"));
            return;
        }

        // Verify order belongs to user, is delivered, and has a driver
        $stmt = $this->db->prepare(
            "SELECT id, driver_id FROM orders
             WHERE id = ? AND user_id = ? AND status = 'delivered' AND driver_id IS NOT NULL
             LIMIT 1"
        );
        $stmt->execute([$orderId, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) {
            setFlash('error', 'Order not eligible for rating.');
            redirect(url("account/orders/detail?id=$orderId"));
            return;
        }

        try {
            $this->db->prepare(
                "INSERT INTO driver_ratings (order_id, driver_id, customer_id, rating, comment)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)"
            )->execute([$orderId, $order['driver_id'], $userId, $rating, $comment ?: null]);

            setFlash('success', 'Thank you for your rating!');
        } catch (\PDOException $e) {
            setFlash('error', 'Could not save rating. Please try again.');
        }

        redirect(url("account/orders/detail?id=$orderId"));
    }

    // ========================================
    // SELLER ORDER METHODS
    // ========================================
    
    /**
     * Seller: View orders for their shop
     */
    public function sellerOrders(): void
    {
        if (!hasRole('seller')) {
            redirect(url('/'));
            return;
        }
        
        $userId = userId();
        
        // Get seller's shop
        $stmt = $this->db->prepare("SELECT id FROM shops WHERE seller_id = :seller_id AND is_active = 1");
        $stmt->execute(['seller_id' => $userId]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$shop) {
            setFlash('error', 'No active shop found');
            redirect(url('seller/dashboard'));
            return;
        }
        
        $shopId = $shop['id'];
        $page = intval(get('page', 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Get filters
        $status = sanitize(get('status', ''));
        $date = sanitize(get('date', ''));
        
        try {
            // Build query
            $where = "WHERE o.shop_id = :shop_id";
            $params = ['shop_id' => $shopId];
            
            if (!empty($status)) {
                $where .= " AND o.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($date)) {
                $where .= " AND DATE(o.created_at) = :date";
                $params['date'] = $date;
            }
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM orders o $where";
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute($params);
            $totalOrders = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get orders
            $query = "
                SELECT 
                    o.*,
                    u.first_name,
                    u.last_name,
                    u.phone as customer_phone,
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                $where
                ORDER BY 
                    CASE o.status
                        WHEN 'pending' THEN 1
                        WHEN 'confirmed' THEN 2
                        WHEN 'processing' THEN 3
                        WHEN 'ready' THEN 4
                        ELSE 5
                    END,
                    o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get order statistics
            $stmt = $this->db->prepare("
                SELECT 
                    status,
                    COUNT(*) as count,
                    SUM(total) as total_amount
                FROM orders
                WHERE shop_id = :shop_id
                AND DATE(created_at) = CURDATE()
                GROUP BY status
            ");
            
            $stmt->execute(['shop_id' => $shopId]);
            $todayStats = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            $totalPages = ceil($totalOrders / $limit);
            
            view('seller/orders/index', [
                'orders' => $orders,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'todayStats' => $todayStats,
                'status' => $status,
                'date' => $date
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching seller orders: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to load orders');
            view('seller/orders/index', [
                'orders'      => [],
                'currentPage' => $page,
                'totalPages'  => 1,
                'totalOrders' => 0,
                'todayStats'  => [],
                'status'      => $status,
                'date'        => $date,
            ]);
        }
    }
    
    /**
     * Seller: Update order status
     */
    public function updateOrderStatus(): void
    {
        if (!hasRole('seller')) {
            jsonResponse(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $userId = userId();
        $orderId = intval(post('order_id', 0));
        $newStatus = sanitize(post('status', ''));
        $notes = sanitize(post('notes', ''));

        $allowedStatuses = ['confirmed', 'processing', 'ready', 'cancelled'];

        if (!in_array($newStatus, $allowedStatuses)) {
            jsonResponse(['success' => false, 'message' => 'Invalid status']);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Verify order belongs to seller's shop
            $stmt = $this->db->prepare("
                SELECT o.*, s.seller_id
                FROM orders o
                JOIN shops s ON o.shop_id = s.id
                WHERE o.id = :id AND s.seller_id = :seller_id
            ");

            $stmt->execute(['id' => $orderId, 'seller_id' => $userId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                $this->db->rollBack();
                jsonResponse(['success' => false, 'message' => 'Order not found']);
                return;
            }

            $oldStatus = $order['status'];

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                $this->db->rollBack();
                jsonResponse([
                    'success' => false,
                    'message' => "Cannot change order status from '{$oldStatus}' to '{$newStatus}'"
                ]);
                return;
            }
            
            // Update order status
            $statusField = '';
            if ($newStatus === 'confirmed') {
                $statusField = ', confirmed_at = NOW()';
            } elseif ($newStatus === 'ready') {
                $statusField = ', ready_at = NOW()';
            } elseif ($newStatus === 'cancelled') {
                $statusField = ', cancelled_at = NOW(), cancelled_by = :user_id';
            }
            
            $query = "UPDATE orders SET status = :status, updated_at = NOW() $statusField WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $params = ['status' => $newStatus, 'id' => $orderId];
            
            if ($newStatus === 'cancelled') {
                $params['user_id'] = $userId;
            }
            
            $stmt->execute($params);
            
            // Add to status history
            $stmt = $this->db->prepare("
                INSERT INTO delivery_status_history 
                (order_id, old_status, new_status, changed_by, notes, created_at)
                VALUES (:order_id, :old_status, :new_status, :user_id, :notes, NOW())
            ");
            
            $stmt->execute([
                'order_id' => $orderId,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'user_id' => $userId,
                'notes' => $notes
            ]);
            
            $this->db->commit();
            
            // Send status update email to customer
            if (in_array($newStatus, ['confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered'])) {
                try {
                    require_once __DIR__ . '/../Helpers/EmailHelper.php';
                    
                    // Get complete order data
                    $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = :id");
                    $stmt->execute(['id' => $orderId]);
                    $order = $stmt->fetch(\PDO::FETCH_ASSOC);
                    
                    if ($order) {
                        \App\Helpers\EmailHelper::sendOrderStatusUpdate($order, $oldStatus, $newStatus);
                        logger("Status update email sent for order #{$order['order_number']}", 'info');
                    }
                } catch (\Exception $e) {
                    logger("Failed to send status update email: " . $e->getMessage(), 'warning');
                }
            }
            
            logger("Order #{$order['order_number']} status updated to {$newStatus} by seller", 'info');
            
            jsonResponse([
                'success' => true,
                'message' => 'Order status updated successfully',
                'new_status' => $newStatus
            ]);
            
        } catch (\PDOException $e) {
            $this->db->rollBack();
            logger("Error updating order status: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to update order status']);
        }
    }
    
    // ========================================
    // ADMIN ORDER METHODS
    // ========================================
    
    /**
     * Admin: View all orders
     */
    public function adminOrders(): void
    {
        if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect(url('/'));
            return;
        }
        
        $page = intval(get('page', 1));
        $limit = 25;
        $offset = ($page - 1) * $limit;
        
        // Get filters
        $status = sanitize(get('status', ''));
        $shop = sanitize(get('shop', ''));
        $dateFrom = sanitize(get('date_from', ''));
        $dateTo = sanitize(get('date_to', ''));
        $search = sanitize(get('search', ''));
        
        try {
            // Build query
            $where = "WHERE 1=1";
            $params = [];
            
            if (!empty($status)) {
                $where .= " AND o.status = :status";
                $params['status'] = $status;
            }
            
            if (!empty($shop)) {
                $where .= " AND o.shop_id = :shop_id";
                $params['shop_id'] = $shop;
            }
            
            if (!empty($dateFrom)) {
                $where .= " AND DATE(o.created_at) >= :date_from";
                $params['date_from'] = $dateFrom;
            }
            
            if (!empty($dateTo)) {
                $where .= " AND DATE(o.created_at) <= :date_to";
                $params['date_to'] = $dateTo;
            }
            
            if (!empty($search)) {
                $where .= " AND (o.order_number LIKE :search OR u.first_name LIKE :search OR u.last_name LIKE :search)";
                $params['search'] = "%$search%";
            }
            
            // Get total count
            $countQuery = "SELECT COUNT(*) as total FROM orders o $where";
            $stmt = $this->db->prepare($countQuery);
            $stmt->execute($params);
            $totalOrders = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Get orders
            $query = "
                SELECT 
                    o.*,
                    s.name as shop_name,
                    u.first_name,
                    u.last_name,
                    u.email as customer_email,
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
                FROM orders o
                LEFT JOIN shops s ON o.shop_id = s.id
                LEFT JOIN users u ON o.user_id = u.id
                $where
                ORDER BY o.created_at DESC
                LIMIT :limit OFFSET :offset
            ";
            
            $stmt = $this->db->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            
            $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get all shops for filter dropdown
            $stmt = $this->db->query("SELECT id, name FROM shops WHERE is_active = 1 ORDER BY name");
            $shops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Get statistics
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN total ELSE 0 END) as today_revenue,
                    SUM(total) as total_revenue
                FROM orders
            ");
            
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $totalPages = ceil($totalOrders / $limit);
            
            view('admin/orders/index', [
                'orders' => $orders,
                'shops' => $shops,
                'stats' => $stats,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalOrders' => $totalOrders,
                'filters' => [
                    'status' => $status,
                    'shop' => $shop,
                    'date_from' => $dateFrom,
                    'date_to' => $dateTo,
                    'search' => $search
                ]
            ]);
            
        } catch (\PDOException $e) {
            logger("Error fetching admin orders: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to load orders');
            view('admin/orders/index', ['orders' => [], 'shops' => []]);
        }
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Validate order status transition
     * Prevents invalid status changes (e.g., delivered → pending)
     *
     * @param string $currentStatus Current order status
     * @param string $newStatus New status to transition to
     * @return bool True if transition is valid, false otherwise
     */
    private function isValidStatusTransition(string $currentStatus, string $newStatus): bool
    {
        // Define allowed transitions for each status
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['ready', 'cancelled'],
            'ready' => ['out_for_delivery', 'cancelled'],
            'out_for_delivery' => ['delivered', 'failed', 'cancelled'],
            'delivered' => [],  // Terminal state - no transitions allowed
            'cancelled' => [],  // Terminal state - no transitions allowed
            'failed' => ['pending'],  // Can retry failed delivery
            'refunded' => []  // Terminal state - no transitions allowed
        ];

        // Check if current status exists in transition map
        if (!isset($allowedTransitions[$currentStatus])) {
            logger("Unknown order status: {$currentStatus}", 'warning');
            return false;
        }

        // Check if new status is in allowed transitions
        $allowed = in_array($newStatus, $allowedTransitions[$currentStatus]);

        if (!$allowed) {
            logger("Invalid status transition attempted: {$currentStatus} → {$newStatus}", 'warning');
        }

        return $allowed;
    }
}