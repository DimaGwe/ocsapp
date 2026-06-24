<?php

namespace App\Controllers;

use PDO;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminOrdersController
 *
 * Manages orders in the admin panel
 */
class AdminOrdersController {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
        $this->db = \Database::getConnection();
    }
    
    /**
     * Display all orders
     */
    public function index() {
        $page = max(1, (int)get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $status = get('status', 'all');
        $search = get('search', '');
        
        // Build WHERE clause
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if ($status !== 'all') {
            $whereClause .= " AND o.status = ?";
            $params[] = $status;
        }
        
        if ($search) {
            $whereClause .= " AND (o.order_number LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Get total count
        $countQuery = "
            SELECT COUNT(*) 
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            $whereClause
        ";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();
        
        // Get orders
        $query = "
            SELECT 
                o.*,
                u.first_name,
                u.last_name,
                u.email,
                s.name as shop_name,
                (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as items_count
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON o.shop_id = s.id
            $whereClause
            ORDER BY o.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalPages = ceil($total / $perPage);
        
        // Get order statistics
        $statsQuery = "
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(total) as total_revenue
            FROM orders
        ";
        $stmt = $this->db->prepare($statsQuery);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return view('admin/orders/index', [
            'orders' => $orders,
            'stats' => $stats,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'status' => $status,
            'search' => $search,
            'pageTitle' => 'Orders Management'
        ]);
    }
    
    /**
     * View order details
     */
    public function view() {
        $orderId = get('id');

        if (!$orderId) {
            setFlash('error', 'Order ID is required');
            redirect('admin/orders');
            return;
        }

        // Get order details
        $stmt = $this->db->prepare("
            SELECT
                o.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                s.name as shop_name,
                s.phone as shop_phone
            FROM orders o
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON o.shop_id = s.id
            WHERE o.id = ?
        ");
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            setFlash('error', 'Order not found');
            redirect('admin/orders');
            return;
        }

        // Get order items with product images
        $stmt = $this->db->prepare("
            SELECT
                oi.*,
                p.name as product_name,
                (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as product_image
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get order history/timeline (try both possible table names)
        $history = [];
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM delivery_status_history
                WHERE order_id = ?
                ORDER BY created_at ASC
            ");
            $stmt->execute([$orderId]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Table might not exist, try order_status_history
            try {
                $stmt = $this->db->prepare("
                    SELECT * FROM order_status_history
                    WHERE order_id = ?
                    ORDER BY created_at ASC
                ");
                $stmt->execute([$orderId]);
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\PDOException $e2) {
                logger("Failed to fetch order history: " . $e2->getMessage(), 'warning');
            }
        }

        return view('admin/orders/view', [
            'order' => $order,
            'items' => $items,
            'history' => $history,
            'pageTitle' => 'Order #' . $order['order_number']
        ]);
    }
    
    /**
     * Update order status
     */
    public function updateStatus() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        $orderId = post('order_id');
        $status = post('status');
        $notes = post('notes', '');

        // Validate status
        $validStatuses = ['pending', 'confirmed', 'processing', 'ready', 'out_for_delivery', 'delivered', 'cancelled', 'refunded'];
        if (!in_array($status, $validStatuses)) {
            return jsonResponse(['error' => 'Invalid status'], 400);
        }

        try {
            $this->db->beginTransaction();

            // Get current order status
            $stmt = $this->db->prepare("SELECT status FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $currentOrder = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentOrder) {
                $this->db->rollBack();
                return jsonResponse(['error' => 'Order not found'], 404);
            }

            $oldStatus = $currentOrder['status'];

            // Update order status
            $stmt = $this->db->prepare("
                UPDATE orders
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $orderId]);

            // Try to add to history - but don't fail if tables don't exist
            try {
                // Get user ID safely
                $changedBy = null;
                try {
                    $changedBy = userId();
                } catch (\Exception $e) {
                    $changedBy = $_SESSION['user']['id'] ?? null;
                }

                // Try delivery_status_history first
                try {
                    $stmt = $this->db->prepare("
                        INSERT INTO delivery_status_history
                        (order_id, old_status, new_status, changed_by, notes, created_at)
                        VALUES (?, ?, ?, ?, ?, NOW())
                    ");
                    $stmt->execute([$orderId, $oldStatus, $status, $changedBy, $notes]);
                } catch (\PDOException $e) {
                    // Try order_status_history as fallback
                    try {
                        $stmt = $this->db->prepare("
                            INSERT INTO order_status_history
                            (order_id, status, notes, created_by, created_at)
                            VALUES (?, ?, ?, ?, NOW())
                        ");
                        $stmt->execute([$orderId, $status, $notes, $changedBy]);
                    } catch (\PDOException $e2) {
                        // Neither table exists - log but continue
                        logger("Could not save status history (tables may not exist): " . $e2->getMessage(), 'info');
                    }
                }
            } catch (\Exception $e) {
                // Log but don't fail the update
                logger("Status history save failed: " . $e->getMessage(), 'warning');
            }

            $this->db->commit();

            // Auto-assign delivery when marking as "Processing" (done after commit to avoid rollback on failure)
            $deliveryMessage = '';
            if ($status === 'processing') {
                try {
                    $deliveryAssigned = $this->autoAssignDelivery($orderId);
                    if ($deliveryAssigned) {
                        $deliveryMessage = ' Driver has been notified and will arrive when order is ready.';
                    } else {
                        $deliveryMessage = ' Note: No available drivers found. Please assign manually.';
                    }
                } catch (\Exception $e) {
                    // Don't fail the status update if delivery assignment fails
                    logger("Auto-assign delivery failed but status was updated: " . $e->getMessage(), 'warning');
                    $deliveryMessage = ' (Delivery assignment will be handled manually)';
                }
            }

            return jsonResponse([
                'success' => true,
                'message' => 'Order status updated successfully.' . $deliveryMessage
            ]);

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger("Error updating order status: " . $e->getMessage(), 'error');
            return jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * POST /admin/orders/:id/notify-driver
     * Send an in-app message to the driver currently handling this order.
     */
    public function notifyDriver(int $orderId): void
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $body    = json_decode(file_get_contents('php://input'), true) ?? [];
        $message = trim($body['message'] ?? '');
        $type    = in_array($body['type'] ?? '', ['info','warning','urgent'])
                   ? $body['type'] : 'info';

        if ($message === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Message is required']);
            return;
        }

        // Verify order exists and has an active driver
        $stmt = $this->db->prepare(
            "SELECT id, driver_id, order_number FROM orders WHERE id = ? AND status = 'out_for_delivery' LIMIT 1"
        );
        $stmt->execute([$orderId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order || !$order['driver_id']) {
            http_response_code(404);
            echo json_encode(['error' => 'No active driver found for this order']);
            return;
        }

        $adminId  = $_SESSION['user']['id'] ?? 0;
        $driverId = (int)$order['driver_id'];

        $this->db->prepare(
            "INSERT INTO driver_delivery_notifications (driver_id, order_id, message, type, sent_by)
             VALUES (?, ?, ?, ?, ?)"
        )->execute([$driverId, $orderId, $message, $type, $adminId]);

        // Push notification — fires immediately to driver's device
        $pushTitle = match($type) {
            'urgent'  => '🚨 Urgent — Order #' . $order['order_number'],
            'warning' => '⚠️ Order #' . $order['order_number'],
            default   => 'Order #' . $order['order_number'],
        };
        \App\Controllers\Api\DriverApiController::sendPush(
            $this->db,
            $driverId,
            $pushTitle,
            $message,
            ['type' => $type, 'order_id' => (string)$orderId]
        );

        echo json_encode(['success' => true]);
    }

    /**
     * Delete order
     */
    public function delete() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }
        
        $orderId = post('order_id');
        
        try {
            $this->db->beginTransaction();

            // Restore stock for non-completed orders before deleting
            $stmt = $this->db->prepare("SELECT o.status FROM orders o WHERE o.id = ?");
            $stmt->execute([$orderId]);
            $orderRow = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($orderRow && !in_array($orderRow['status'], ['completed', 'delivered'])) {
                $stmt = $this->db->prepare("SELECT product_id, shop_id, quantity FROM order_items WHERE order_id = ?");
                $stmt->execute([$orderId]);
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($items as $item) {
                    $this->db->prepare(
                        "UPDATE shop_inventory SET stock_quantity = stock_quantity + ? WHERE product_id = ? AND shop_id = ?"
                    )->execute([$item['quantity'], $item['product_id'], $item['shop_id']]);
                }
            }

            // Delete order items first
            $stmt = $this->db->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->execute([$orderId]);

            // Delete order history (try both table names)
            try {
                $stmt = $this->db->prepare("DELETE FROM delivery_status_history WHERE order_id = ?");
                $stmt->execute([$orderId]);
            } catch (\PDOException $e) { /* table may not exist */ }
            try {
                $stmt = $this->db->prepare("DELETE FROM order_status_history WHERE order_id = ?");
                $stmt->execute([$orderId]);
            } catch (\PDOException $e) { /* table may not exist */ }

            // Delete order
            $stmt = $this->db->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);

            $this->db->commit();
            
            setFlash('success', 'Order deleted successfully');
            return jsonResponse(['success' => true]);
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            return jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark an Interac e-Transfer order as paid
     * POST /admin/orders/mark-paid
     */
    public function markAsPaid() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        $orderId = post('order_id');

        if (!$orderId) {
            return jsonResponse(['error' => 'Order ID is required'], 400);
        }

        try {
            // Get current order
            $stmt = $this->db->prepare("SELECT id, status, payment_status, payment_method, shop_id, total, order_number FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return jsonResponse(['error' => 'Order not found'], 404);
            }

            if ($order['payment_status'] === 'paid') {
                return jsonResponse(['error' => 'Order is already marked as paid'], 400);
            }

            $this->db->beginTransaction();

            // Update order: payment_status -> paid, status -> confirmed
            $stmt = $this->db->prepare("
                UPDATE orders
                SET payment_status = 'paid',
                    status = 'confirmed',
                    payment_gateway = 'interac',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$orderId]);

            // Log status change
            $changedBy = $_SESSION['user']['id'] ?? null;
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO delivery_status_history
                    (order_id, old_status, new_status, changed_by, notes, created_at)
                    VALUES (?, ?, 'confirmed', ?, 'Interac e-Transfer payment confirmed by admin', NOW())
                ");
                $stmt->execute([$orderId, $order['status'], $changedBy]);
            } catch (\PDOException $e) {
                logger("Status history log failed: " . $e->getMessage(), 'warning');
            }

            $this->db->commit();

            // Send payment confirmation email to buyer
            try {
                \App\Helpers\EmailHelper::sendOrderStatusUpdate($order, $order['status'], 'confirmed');
            } catch (\Exception $e) {
                logger("Failed to send payment confirmation email for order #{$order['order_number']}: " . $e->getMessage(), 'warning');
            }

            // Auto-assign delivery after payment confirmation (after commit)
            $deliveryMessage = '';
            try {
                $deliveryAssigned = $this->autoAssignDelivery($orderId);
                if ($deliveryAssigned) {
                    $deliveryMessage = ' Delivery driver has been assigned.';
                } else {
                    $deliveryMessage = ' Note: No available drivers. Please assign manually.';
                }
            } catch (\Exception $e) {
                logger("Auto-assign delivery failed after mark-paid: " . $e->getMessage(), 'warning');
                $deliveryMessage = ' (Delivery will be assigned manually)';
            }

            logger("Order #{$order['order_number']} marked as paid (Interac e-Transfer) by admin #{$changedBy}", 'info');

            return jsonResponse([
                'success' => true,
                'message' => 'Order marked as paid successfully.' . $deliveryMessage
            ]);

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger("Error marking order as paid: " . $e->getMessage(), 'error');
            return jsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Auto-assign delivery driver when order is marked as "Processing"
     * Finds available driver and creates delivery assignment with estimated ready time
     *
     * @param int $orderId Order ID to assign delivery for
     * @return bool True if delivery was assigned, false otherwise
     */
    private function autoAssignDelivery($orderId) {
        try {
            // Get order details with shop info
            $stmt = $this->db->prepare("
                SELECT o.*,
                       u.first_name,
                       u.last_name,
                       u.phone,
                       s.id as shop_id
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN shops s ON o.shop_id = s.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                logger("Auto-assign delivery failed: Order {$orderId} not found", 'warning');
                return false;
            }

            if (!$order['shop_id']) {
                logger("Auto-assign delivery failed: Order {$orderId} has no shop_id", 'warning');
                return false;
            }

            // Check if delivery assignment already exists
            $stmt = $this->db->prepare("
                SELECT id FROM delivery_assignments WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
            if ($stmt->fetch()) {
                logger("Auto-assign delivery skipped: Order {$orderId} already has assignment", 'info');
                return true; // Already assigned, consider it success
            }

            // Find available driver (active drivers sorted by least recent assignment)
            // Uses user_roles table for role lookup
            $stmt = $this->db->prepare("
                SELECT u.id, u.first_name, u.last_name, u.phone,
                       COALESCE(MAX(da.created_at), '2000-01-01') as last_assignment
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN delivery_assignments da ON u.id = da.driver_id
                WHERE r.name = 'delivery'
                  AND u.status = 'active'
                GROUP BY u.id, u.first_name, u.last_name, u.phone
                ORDER BY last_assignment ASC
                LIMIT 1
            ");
            $stmt->execute();
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);

            // Calculate estimated time (20-30 minutes)
            $estimatedMinutes = rand(20, 30);

            // Prepare delivery addresses
            $deliveryAddress = $order['delivery_address'] ?? $order['address'] ?? '';
            $customerPhone = $order['phone'] ?? $order['customer_phone'] ?? '';
            $deliveryFee = $order['delivery_fee'] ?? 50.00;

            // Create delivery assignment (with or without driver)
            if ($driver) {
                // Create assigned delivery
                $stmt = $this->db->prepare("
                    INSERT INTO delivery_assignments (
                        order_id,
                        driver_id,
                        shop_id,
                        status,
                        estimated_time,
                        delivery_fee,
                        delivery_address,
                        customer_phone,
                        delivery_notes
                    ) VALUES (?, ?, ?, 'assigned', ?, ?, ?, ?, ?)
                ");

                $deliveryNotes = "Auto-assigned when order marked as Processing. Estimated ready in {$estimatedMinutes} minutes.";

                $stmt->execute([
                    $orderId,
                    $driver['id'],
                    $order['shop_id'],
                    $estimatedMinutes,
                    $deliveryFee,
                    $deliveryAddress,
                    $customerPhone,
                    $deliveryNotes
                ]);

                $assignmentId = $this->db->lastInsertId();
                logger("Auto-assigned delivery for order #{$order['order_number']} to driver {$driver['first_name']} {$driver['last_name']} (ID: {$assignmentId})", 'info');
                logger("Driver notification: Order #{$order['order_number']} assigned to {$driver['first_name']}. Ready in {$estimatedMinutes} minutes.", 'info');

                return true;
            } else {
                // Create unassigned delivery (pending driver assignment)
                $stmt = $this->db->prepare("
                    INSERT INTO delivery_assignments (
                        order_id,
                        driver_id,
                        shop_id,
                        status,
                        estimated_time,
                        delivery_fee,
                        delivery_address,
                        customer_phone,
                        delivery_notes
                    ) VALUES (?, NULL, ?, 'pending', ?, ?, ?, ?, ?)
                ");

                $deliveryNotes = "Awaiting driver assignment. Order being processed.";

                $stmt->execute([
                    $orderId,
                    $order['shop_id'],
                    $estimatedMinutes,
                    $deliveryFee,
                    $deliveryAddress,
                    $customerPhone,
                    $deliveryNotes
                ]);

                $assignmentId = $this->db->lastInsertId();
                logger("Created pending delivery assignment for order #{$order['order_number']} (ID: {$assignmentId}) - awaiting driver", 'info');

                return false; // Return false to indicate no driver was assigned
            }

        } catch (\PDOException $e) {
            logger("Auto-assign delivery error: " . $e->getMessage(), 'error');
            return false;
        }
    }
}