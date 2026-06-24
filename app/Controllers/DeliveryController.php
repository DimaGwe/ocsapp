<?php

namespace App\Controllers;

use PDO;
use Exception;
use DateTime;

/**
 * DeliveryController
 * 
 * Handles all delivery driver operations:
 * - Dashboard with active deliveries
 * - Accept/reject delivery assignments
 * - Update delivery status
 * - Track location and progress
 * - Manage earnings
 * - View delivery history
 */

class DeliveryController {
    private $db;

    public function __construct() {
        $this->db = \Database::getConnection();
    }

    private function isFr(): bool {
        return (($_SESSION['language'] ?? 'fr') === 'fr');
    }
    
    /**
     * Driver Dashboard - Main landing page
     */
    public function dashboard() {
        // Check if user is delivery driver
        if (!$this->isDeliveryDriver()) {
            setFlash('error', 'Access denied. Delivery role required.');
            redirect('/');
        }
        
        $driverId = userId();
        
        // Get driver statistics
        $stats = $this->getDriverStats($driverId);
        
        // Get active deliveries (assigned or in progress)
        $activeDeliveries = $this->getActiveDeliveries($driverId);
        
        // Get recent completed deliveries
        $recentDeliveries = $this->getRecentDeliveries($driverId, 10);
        
        // Get driver availability status
        $availability = $this->getDriverAvailability($driverId);

        // Get assigned vehicle (if vehicles table exists)
        $assignedVehicle = null;
        try {
            $check = $this->db->query("SHOW TABLES LIKE 'vehicles'");
            if ($check->rowCount() > 0) {
                $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE driver_id = ? AND status = 'active' LIMIT 1");
                $stmt->execute([$driverId]);
                $assignedVehicle = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
            }
        } catch (\Exception $e) {
            // vehicles table may not exist yet
        }

        // Get application status for pending-state detection
        $applicationStatus = 'pending';
        try {
            $appStatusStmt = $this->db->prepare("SELECT status, created_at FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $appStatusStmt->execute([$driverId]);
            $appStatusRow = $appStatusStmt->fetch(PDO::FETCH_ASSOC);
            if ($appStatusRow) {
                $applicationStatus = $appStatusRow['status'];
                $applicationSubmittedAt = $appStatusRow['created_at'];
            }
        } catch (\Exception $e) {}

        // Get recent admin messages for this driver
        $messages = [];
        $unreadCount = 0;
        $applicationId = 0;
        try {
            $appStmt = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
            $appStmt->execute([$driverId]);
            $applicationId = (int) $appStmt->fetchColumn();
            if ($applicationId) {
                $msgStmt = $this->db->prepare("
                    SELECT * FROM driver_application_messages
                    WHERE application_id = ?
                    ORDER BY created_at DESC
                    LIMIT 20
                ");
                $msgStmt->execute([$applicationId]);
                $messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);
                $unreadCount = count(array_filter($messages, fn($m) => $m['sender_type'] === 'admin' && !$m['is_read']));
                // Mark admin messages as read now that driver saw the dashboard
                $this->db->prepare("
                    UPDATE driver_application_messages
                    SET is_read = 1
                    WHERE application_id = ? AND sender_type = 'admin' AND is_read = 0
                ")->execute([$applicationId]);
            }
        } catch (\Exception $e) {
            // ignore
        }

        return view('delivery/dashboard', [
            'stats' => $stats,
            'activeDeliveries' => $activeDeliveries,
            'recentDeliveries' => $recentDeliveries,
            'availability' => $availability,
            'assignedVehicle' => $assignedVehicle,
            'messages' => $messages,
            'unreadCount' => $unreadCount,
            'applicationId' => $applicationId,
            'applicationStatus' => $applicationStatus,
            'applicationSubmittedAt' => $applicationSubmittedAt ?? null,
            'pageTitle' => $this->isFr() ? 'Tableau de bord' : 'Delivery Dashboard'
        ]);
    }
    
    /**
     * Get available delivery orders for driver to accept
     */
    public function availableOrders() {
        if (!$this->isDeliveryDriver()) {
            return jsonResponse(['error' => 'Unauthorized'], 403);
        }
        $this->requireTraining();

        $driverId = userId();

        // Get driver's current zone
        $driverZone = $this->getDriverZone($driverId);

        // Get available orders in driver's zone (both B2C and B2B)
        $query = "
            SELECT
                da.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                COALESCE(o.total, dr.total_amount) as total,
                COALESCE(o.notes, dr.notes) as notes,
                COALESCE(u.first_name, bp_u.first_name) as customer_first_name,
                COALESCE(u.last_name, bp_u.last_name) as customer_last_name,
                COALESCE(u.phone, bp_u.phone) as customer_phone,
                COALESCE(s.name, bp.company_name) as shop_name,
                COALESCE(s.address, da.pickup_address) as shop_address,
                COALESCE(s.phone, '') as shop_phone
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
            LEFT JOIN users bp_u ON bp.user_id = bp_u.id
            WHERE da.status = 'assigned'
            AND da.driver_id = ?
            ORDER BY da.assigned_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$driverId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return view('delivery/available-orders', [
            'orders' => $orders,
            'pageTitle' => $this->isFr() ? 'Mes livraisons' : 'Available Deliveries'
        ]);
    }
    
    /**
     * Accept a delivery assignment
     */
    public function acceptDelivery() {
        if (!$this->isDeliveryDriver() || !isPost()) {
            return jsonResponse(['error' => 'Unauthorized'], 403);
        }
        $this->requireTraining();
        verifyCsrfForApi();
        
        $deliveryId = post('delivery_id');
        $driverId = userId();
        
        try {
            $this->db->beginTransaction();
            
            // Verify delivery is assigned to this driver and not yet accepted
            $stmt = $this->db->prepare("
                SELECT * FROM delivery_assignments 
                WHERE id = ? AND driver_id = ? AND status = 'assigned'
            ");
            $stmt->execute([$deliveryId, $driverId]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$delivery) {
                throw new Exception('Delivery not found or already accepted');
            }
            
            // Update delivery status to accepted
            $stmt = $this->db->prepare("
                UPDATE delivery_assignments 
                SET status = 'accepted', accepted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$deliveryId]);
            
            // Add status history
            $this->addStatusHistory($deliveryId, 'accepted', 'Driver accepted delivery', $driverId);
            
            // Update driver availability
            $this->updateDriverAvailability($driverId, 'busy');
            
            $this->db->commit();
            
            logger("Driver $driverId accepted delivery $deliveryId", 'info');
            
            return jsonResponse([
                'success' => true,
                'message' => 'Delivery accepted successfully',
                'delivery_id' => $deliveryId
            ]);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            logger("Error accepting delivery: " . $e->getMessage(), 'error');
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Reject a delivery assignment
     */
    public function rejectDelivery() {
        if (!$this->isDeliveryDriver() || !isPost()) {
            return jsonResponse(['error' => 'Unauthorized'], 403);
        }
        $this->requireTraining();
        verifyCsrfForApi();

        $deliveryId = post('delivery_id');
        $reason = post('reason', 'No reason provided');
        $driverId = userId();
        
        try {
            // Verify delivery belongs to this driver
            $stmt = $this->db->prepare("
                SELECT * FROM delivery_assignments 
                WHERE id = ? AND driver_id = ? AND status = 'assigned'
            ");
            $stmt->execute([$deliveryId, $driverId]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$delivery) {
                throw new Exception('Delivery not found or cannot be rejected');
            }
            
            // Update delivery to cancelled
            $stmt = $this->db->prepare("
                UPDATE delivery_assignments 
                SET status = 'cancelled', 
                    cancelled_at = NOW(),
                    failure_reason = ? 
                WHERE id = ?
            ");
            $stmt->execute([$reason, $deliveryId]);
            
            // Add status history
            $this->addStatusHistory($deliveryId, 'cancelled', "Rejected by driver: $reason", $driverId);

            // Notify admin so they can manually reassign
            $orderNumber = $delivery['order_number'] ?? "DA#{$deliveryId}";
            try {
                $config = require dirname(__DIR__, 2) . '/config/mail.php';
                $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';
                $subject = "Driver Rejected Delivery — Manual Reassignment Needed";
                $body = "
                    <p>A driver has rejected delivery assignment <strong>#{$deliveryId}</strong> (Order {$orderNumber}).</p>
                    <p><strong>Reason:</strong> " . htmlspecialchars($reason) . "</p>
                    <p>Please <a href=\"" . url('admin/delivery') . "\">log in to the admin panel</a> to assign a new driver.</p>
                ";
                \App\Helpers\EmailHelper::sendRaw($adminEmail, $subject, $body);
            } catch (\Exception $e) {
                logger("Failed to send driver-rejection admin email: " . $e->getMessage(), 'warning');
            }

            try {
                \App\Helpers\NotificationHelper::add(
                    'delivery_rejected',
                    'Driver Rejected Delivery',
                    "Driver rejected assignment #{$deliveryId} (Order {$orderNumber}). Manual reassignment needed. Reason: " . mb_strimwidth($reason, 0, 100, '…'),
                    ['link' => '/admin/delivery', 'icon' => 'triangle-exclamation', 'priority' => 'high']
                );
            } catch (\Exception $e) {
                logger("Failed to send driver-rejection admin bell: " . $e->getMessage(), 'warning');
            }

            return jsonResponse([
                'success' => true,
                'message' => 'Delivery rejected. Admin has been notified for reassignment.'
            ]);
            
        } catch (Exception $e) {
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Update delivery status (picked up, on the way, delivered)
     */
    public function updateStatus() {
        if (!$this->isDeliveryDriver() || !isPost()) {
            return jsonResponse(['error' => 'Unauthorized'], 403);
        }
        $this->requireTraining();
        verifyCsrfForApi();

        $deliveryId = post('delivery_id');
        $newStatus = post('status');
        $notes = post('notes', '');
        $latitude = post('latitude');
        $longitude = post('longitude');
        $proofImage = $_FILES['proof'] ?? null;

        $driverId = userId();

        // Valid status transitions
        $validStatuses = ['picked_up', 'on_the_way', 'delivered', 'failed'];
        if (!in_array($newStatus, $validStatuses)) {
            return jsonResponse(['error' => 'Invalid status'], 400);
        }

        try {
            $this->db->beginTransaction();

            // Get current delivery
            $stmt = $this->db->prepare("
                SELECT * FROM delivery_assignments
                WHERE id = ? AND driver_id = ?
            ");
            $stmt->execute([$deliveryId, $driverId]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$delivery) {
                throw new Exception('Delivery not found');
            }

            // Prepare update
            $updateField = $newStatus . '_at';
            $updateQuery = "UPDATE delivery_assignments SET status = ?, {$updateField} = NOW()";
            $params = [$newStatus];

            // Handle proof of delivery for completed deliveries
            if ($newStatus === 'delivered' && $proofImage) {
                $proofPath = $this->uploadProof($proofImage);
                $updateQuery .= ", proof_of_delivery = ?";
                $params[] = $proofPath;

                // Calculate actual delivery time
                $actualTime = $this->calculateDeliveryTime($delivery['picked_up_at']);
                $updateQuery .= ", actual_time = ?";
                $params[] = $actualTime;
            }

            if ($newStatus === 'failed') {
                $failureReason = post('failure_reason', 'Delivery failed');
                $updateQuery .= ", failure_reason = ?";
                $params[] = $failureReason;
            }

            $updateQuery .= " WHERE id = ?";
            $params[] = $deliveryId;

            // Execute update
            $stmt = $this->db->prepare($updateQuery);
            $stmt->execute($params);

            // Add to history
            $this->addStatusHistory($deliveryId, $newStatus, $notes, $driverId, $latitude, $longitude);

            // If delivered, create earnings record and update source
            if ($newStatus === 'delivered') {
                $this->createEarningsRecord($deliveryId);
                $this->updateDriverAvailability($driverId, 'available');

                $deliveryType = $delivery['delivery_type'] ?? 'order';

                if ($deliveryType === 'distribution' && !empty($delivery['distribution_request_id'])) {
                    // Update distribution request status to completed
                    $stmt = $this->db->prepare("
                        UPDATE distribution_requests
                        SET status = 'completed', delivered_at = NOW(), completed_at = NOW(), updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$delivery['distribution_request_id']]);

                    // Log to distribution status history
                    $stmt = $this->db->prepare("
                        INSERT INTO distribution_status_history
                        (distribution_request_id, old_status, new_status, changed_by, notes, created_at)
                        VALUES (?, 'in_transit', 'completed', ?, 'Delivery completed by driver', NOW())
                    ");
                    $stmt->execute([$delivery['distribution_request_id'], $driverId]);
                } else {
                    // Update B2C order status
                    $this->updateOrderStatus($delivery['order_id'], 'delivered');
                }
            }

            // For distribution deliveries, also sync in_transit status
            if ($newStatus === 'on_the_way' && ($delivery['delivery_type'] ?? 'order') === 'distribution' && !empty($delivery['distribution_request_id'])) {
                $stmt = $this->db->prepare("
                    UPDATE distribution_requests SET status = 'in_transit', updated_at = NOW() WHERE id = ? AND status != 'in_transit'
                ");
                $stmt->execute([$delivery['distribution_request_id']]);
            }

            $this->db->commit();

            // Buyer email notifications (B2C orders only, fire-and-forget after commit)
            $deliveryType = $delivery['delivery_type'] ?? 'order';
            if (in_array($newStatus, ['on_the_way', 'delivered']) && $deliveryType === 'order' && !empty($delivery['order_id'])) {
                try {
                    $oStmt = $this->db->prepare("
                        SELECT o.id, o.order_number, o.total, o.subtotal, o.delivery_fee,
                               u.email AS customer_email, u.first_name AS customer_first_name
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        WHERE o.id = ?
                    ");
                    $oStmt->execute([$delivery['order_id']]);
                    $buyerOrder = $oStmt->fetch(PDO::FETCH_ASSOC);

                    if ($buyerOrder) {
                        $buyerOrder['delivery_address'] = $delivery['delivery_address'] ?? '';
                        $buyerOrder['items_summary']    = '';

                        if ($newStatus === 'on_the_way') {
                            $dStmt = $this->db->prepare("SELECT first_name, last_name, phone FROM users WHERE id = ?");
                            $dStmt->execute([$driverId]);
                            $driverUser = $dStmt->fetch(PDO::FETCH_ASSOC);
                            \App\Helpers\EmailHelper::sendBuyerOutForDelivery($buyerOrder, [
                                'name'  => trim(($driverUser['first_name'] ?? '') . ' ' . ($driverUser['last_name'] ?? '')),
                                'phone' => $driverUser['phone'] ?? '',
                            ]);
                        } else {
                            $buyerOrder['delivered_at'] = date('F j, Y \a\t g:i A');
                            \App\Helpers\EmailHelper::sendBuyerOrderDelivered($buyerOrder);
                        }
                    }
                } catch (Exception $e) {
                    logger('Buyer delivery email failed: ' . $e->getMessage(), 'warning');
                }
            }

            // Admin bell + customer notification for failed deliveries
            if ($newStatus === 'failed') {
                $orderRef = $delivery['order_number'] ?? "DA#{$deliveryId}";
                try {
                    \App\Helpers\NotificationHelper::add(
                        'delivery_failed',
                        'Delivery Failed',
                        "Delivery #{$deliveryId} (Order {$orderRef}) failed. Reason: " . mb_strimwidth($failureReason ?? 'Not specified', 0, 120, '…'),
                        ['link' => '/admin/delivery', 'icon' => 'circle-xmark', 'priority' => 'urgent']
                    );
                } catch (\Exception $e) {
                    logger('Failed to send delivery-failure admin bell: ' . $e->getMessage(), 'warning');
                }

                if (($delivery['delivery_type'] ?? 'order') === 'order' && !empty($delivery['order_id'])) {
                    try {
                        $oStmt = $this->db->prepare("
                            SELECT o.order_number, u.email AS customer_email, u.first_name AS customer_first_name
                            FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?
                        ");
                        $oStmt->execute([$delivery['order_id']]);
                        $buyerData = $oStmt->fetch(\PDO::FETCH_ASSOC);
                        if ($buyerData) {
                            \App\Helpers\EmailHelper::sendRaw(
                                $buyerData['customer_email'],
                                'Update on your order #' . $buyerData['order_number'],
                                "<p>Hi {$buyerData['customer_first_name']},</p>
                                 <p>Unfortunately, we were unable to complete the delivery for your order <strong>#{$buyerData['order_number']}</strong>.</p>
                                 <p>Our team has been notified and will reach out to you shortly to arrange an alternative.</p>
                                 <p>We apologize for the inconvenience.<br>The OCSAPP Team</p>"
                            );
                        }
                    } catch (\Exception $e) {
                        logger('Failed to send delivery-failure customer email: ' . $e->getMessage(), 'warning');
                    }
                }
            }

            return jsonResponse([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            logger("Error updating delivery status: " . $e->getMessage(), 'error');
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    /**
     * Get delivery details
     */
    public function deliveryDetails() {
        if (!$this->isDeliveryDriver()) {
            setFlash('error', 'Unauthorized');
            redirect('/');
        }
        $this->requireTraining();

        $deliveryId = get('id');
        $driverId = userId();

        // Get delivery with all related info (supports both B2C and B2B)
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                COALESCE(o.total, dr.total_amount) as total,
                COALESCE(o.subtotal, dr.subtotal) as subtotal,
                COALESCE(o.tax, dr.tax_amount) as tax,
                COALESCE(o.notes, dr.notes) as order_notes,
                COALESCE(u.first_name, bp_u.first_name) as customer_first_name,
                COALESCE(u.last_name, bp_u.last_name) as customer_last_name,
                COALESCE(u.email, bp_u.email) as customer_email,
                COALESCE(u.phone, bp_u.phone) as customer_phone,
                COALESCE(s.name, bp.company_name) as shop_name,
                COALESCE(s.address, da.pickup_address) as shop_address,
                COALESCE(s.phone, '') as shop_phone,
                s.latitude as shop_latitude,
                s.longitude as shop_longitude,
                bp.company_name as business_name,
                dr.delivery_address as dist_delivery_address
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
            LEFT JOIN users bp_u ON bp.user_id = bp_u.id
            WHERE da.id = ? AND da.driver_id = ?
        ");
        $stmt->execute([$deliveryId, $driverId]);
        $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$delivery) {
            setFlash('error', 'Delivery not found');
            redirect('/delivery/dashboard');
        }

        // Get items based on delivery type
        $items = [];
        $deliveryType = $delivery['delivery_type'] ?? 'order';

        if ($deliveryType === 'distribution' && !empty($delivery['distribution_request_id'])) {
            // Get distribution request items
            $stmt = $this->db->prepare("
                SELECT dri.*, p.name as product_name, p.sku
                FROM distribution_request_items dri
                LEFT JOIN products p ON dri.product_id = p.id
                WHERE dri.request_id = ?
            ");
            $stmt->execute([$delivery['distribution_request_id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif (!empty($delivery['order_id'])) {
            // Get order items
            $stmt = $this->db->prepare("
                SELECT * FROM order_items WHERE order_id = ?
            ");
            $stmt->execute([$delivery['order_id']]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Get status history
        $stmt = $this->db->prepare("
            SELECT
                dsh.*,
                u.first_name,
                u.last_name
            FROM delivery_status_history dsh
            LEFT JOIN users u ON dsh.created_by = u.id
            WHERE dsh.delivery_id = ?
            ORDER BY dsh.created_at DESC
        ");
        $stmt->execute([$deliveryId]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return view('delivery/details', [
            'delivery' => $delivery,
            'items' => $items,
            'history' => $history,
            'pageTitle' => $this->isFr() ? 'Détails de la livraison' : 'Delivery Details'
        ]);
    }
    
    /**
     * Driver earnings page
     */
    public function earnings() {
        if (!$this->isDeliveryDriver()) {
            setFlash('error', 'Unauthorized');
            redirect('/');
        }
        $this->requireTraining();

        $driverId = userId();
        $period = get('period', 'week'); // week, month, all
        
        // Get earnings summary
        $earningsSummary = $this->getEarningsSummary($driverId, $period);
        
        // Get earnings details (both B2C and B2B)
        $stmt = $this->db->prepare("
            SELECT
                de.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                da.delivered_at,
                da.delivery_type
            FROM delivery_earnings de
            JOIN delivery_assignments da ON de.delivery_id = da.id
            LEFT JOIN orders o ON de.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            WHERE de.driver_id = ?
            ORDER BY de.created_at DESC
            LIMIT 100
        ");
        $stmt->execute([$driverId]);
        $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return view('delivery/earnings', [
            'summary' => $earningsSummary,
            'earnings' => $earnings,
            'period' => $period,
            'pageTitle' => $this->isFr() ? 'Mes revenus' : 'My Earnings'
        ]);
    }
    
    /**
     * Driver history
     */
    public function history() {
        if (!$this->isDeliveryDriver()) {
            setFlash('error', 'Unauthorized');
            redirect('/');
        }
        $this->requireTraining();

        $driverId = userId();
        $page = max(1, (int)get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        $total = $stmt->fetchColumn();
        
        // Get deliveries (both B2C and B2B)
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                COALESCE(o.total, dr.total_amount) as total,
                COALESCE(u.first_name, bp_u.first_name) as customer_first_name,
                COALESCE(u.last_name, bp_u.last_name) as customer_last_name,
                COALESCE(s.name, bp.company_name) as shop_name
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
            LEFT JOIN users bp_u ON bp.user_id = bp_u.id
            WHERE da.driver_id = ?
            ORDER BY da.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$driverId, $perPage, $offset]);
        $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalPages = ceil($total / $perPage);
        
        return view('delivery/history', [
            'deliveries' => $deliveries,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => $this->isFr() ? 'Historique' : 'Delivery History'
        ]);
    }
    
    /**
     * Update driver availability status
     */
    public function updateAvailability() {
        if (!$this->isDeliveryDriver() || !isPost()) {
            return jsonResponse(['error' => 'Unauthorized'], 403);
        }
        verifyCsrfForApi();

        $driverId = userId();
        $status = post('status'); // available, busy, offline, break
        $zoneId = post('zone_id');
        $latitude = post('latitude');
        $longitude = post('longitude');

        // Block drivers from going online until training + background check are both complete
        if ($status !== 'offline') {
            if (!$this->isTrainingCertified()) {
                return jsonResponse([
                    'error' => $this->isFr()
                        ? 'Vous devez compléter votre formation de livreur avant de vous mettre en ligne.'
                        : 'You must complete your driver training before going online.',
                    'redirect' => url('delivery/training'),
                ], 403);
            }
            if (!$this->isBgcheckClear()) {
                return jsonResponse([
                    'error' => $this->isFr()
                        ? 'Votre vérification des antécédents doit être approuvée avant de vous mettre en ligne.'
                        : 'Your background check must be verified before you can go online.',
                    'redirect' => url('delivery/bgcheck'),
                ], 403);
            }
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO driver_availability 
                (driver_id, status, zone_id, current_latitude, current_longitude, last_location_update, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                    status = VALUES(status),
                    zone_id = VALUES(zone_id),
                    current_latitude = VALUES(current_latitude),
                    current_longitude = VALUES(current_longitude),
                    last_location_update = NOW(),
                    updated_at = NOW()
            ");
            
            $stmt->execute([$driverId, $status, $zoneId, $latitude, $longitude]);
            
            return jsonResponse([
                'success' => true,
                'message' => 'Availability updated',
                'status' => $status
            ]);
            
        } catch (Exception $e) {
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    private function isDeliveryDriver(): bool {
        if (!hasRole('delivery')) {
            return false;
        }
        // Verify the user record still exists — kicks deleted drivers
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([(int)userId()]);
            if (!$stmt->fetch()) {
                session_unset();
                session_destroy();
                return false;
            }
        } catch (\Throwable $e) {}
        return true;
    }

    private function isBgcheckClear(): bool {
        try {
            $stmt = $this->db->prepare("
                SELECT bgcheck_status FROM driver_applications
                WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->execute([userId()]);
            $status = $stmt->fetchColumn();
            return in_array($status, ['verified', 'waived'], true);
        } catch (\Exception $e) {
            return false;
        }
    }

    private function isTrainingCertified(): bool {
        try {
            $stmt = $this->db->prepare("SELECT id FROM driver_certificates WHERE driver_id = ? LIMIT 1");
            $stmt->execute([userId()]);
            return (bool) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return false;
        }
    }

    private function requireTraining(): void {
        if (!$this->isTrainingCertified()) {
            $fr = ($_SESSION['language'] ?? 'en') === 'fr';
            setFlash('info', $fr
                ? 'Veuillez compléter votre formation de livreur avant d\'accéder aux livraisons.'
                : 'Please complete your driver training before accessing deliveries.'
            );
            redirect(url('delivery/training'));
            exit;
        }
    }
    
    private function getDriverStats($driverId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN status IN ('accepted', 'picked_up', 'on_the_way') THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
                COALESCE(SUM(CASE WHEN status = 'delivered' THEN delivery_fee ELSE 0 END), 0) as total_earnings
            FROM delivery_assignments
            WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getActiveDeliveries($driverId) {
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                COALESCE(o.total, dr.total_amount) as total,
                COALESCE(u.first_name, bp_u.first_name) as customer_first_name,
                COALESCE(u.last_name, bp_u.last_name) as customer_last_name,
                COALESCE(u.phone, bp_u.phone) as customer_phone,
                COALESCE(s.name, bp.company_name) as shop_name
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
            LEFT JOIN users bp_u ON bp.user_id = bp_u.id
            WHERE da.driver_id = ?
            AND da.status IN ('assigned', 'accepted', 'picked_up', 'on_the_way')
            ORDER BY da.assigned_at ASC
        ");
        $stmt->execute([$driverId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getRecentDeliveries($driverId, $limit = 10) {
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            WHERE da.driver_id = ? AND da.status = 'delivered'
            ORDER BY da.delivered_at DESC
            LIMIT ?
        ");
        $stmt->execute([$driverId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getDriverAvailability($driverId) {
        $stmt = $this->db->prepare("
            SELECT * FROM driver_availability WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'status' => 'offline',
            'active_deliveries' => 0
        ];
    }
    
    private function getDriverZone($driverId) {
        $stmt = $this->db->prepare("
            SELECT zone_id FROM driver_availability WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        return $stmt->fetchColumn();
    }
    
    private function addStatusHistory($deliveryId, $status, $notes, $userId, $lat = null, $lon = null) {
        $stmt = $this->db->prepare("
            INSERT INTO delivery_status_history 
            (delivery_id, status, notes, latitude, longitude, created_by)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$deliveryId, $status, $notes, $lat, $lon, $userId]);
    }
    
    private function updateDriverAvailability($driverId, $status) {
        $stmt = $this->db->prepare("
            UPDATE driver_availability 
            SET status = ?, updated_at = NOW()
            WHERE driver_id = ?
        ");
        $stmt->execute([$status, $driverId]);
    }
    
    private function createEarningsRecord($deliveryId) {
        // Get delivery details
        $stmt = $this->db->prepare("
            SELECT driver_id, order_id, delivery_fee, distance_km
            FROM delivery_assignments
            WHERE id = ?
        ");
        $stmt->execute([$deliveryId]);
        $delivery = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$delivery) return;
        
        $baseFee = $delivery['delivery_fee'];
        $distanceFee = ($delivery['distance_km'] ?? 0) * 10; // 10 DOP per km
        $platformCommission = ($baseFee + $distanceFee) * 0.20; // 20% commission
        $netEarning = ($baseFee + $distanceFee) - $platformCommission;
        
        $stmt = $this->db->prepare("
            INSERT INTO delivery_earnings 
            (driver_id, delivery_id, order_id, base_fee, distance_fee, total_earning, platform_commission, net_earning)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $delivery['driver_id'],
            $deliveryId,
            $delivery['order_id'],
            $baseFee,
            $distanceFee,
            $baseFee + $distanceFee,
            $platformCommission,
            $netEarning
        ]);
    }
    
    private function updateOrderStatus($orderId, $status) {
        $stmt = $this->db->prepare("
            UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?
        ");
        $stmt->execute([$status, $orderId]);
    }
    
    private function getEarningsSummary($driverId, $period) {
        $whereClause = "WHERE driver_id = ?";
        $params = [$driverId];
        
        if ($period === 'week') {
            $whereClause .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $whereClause .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_deliveries,
                SUM(total_earning) as total_earned,
                SUM(platform_commission) as total_commission,
                SUM(net_earning) as net_earned,
                SUM(tip) as total_tips,
                SUM(CASE WHEN payment_status = 'paid' THEN net_earning ELSE 0 END) as paid_amount,
                SUM(CASE WHEN payment_status = 'pending' THEN net_earning ELSE 0 END) as pending_amount
            FROM delivery_earnings
            $whereClause
        ");
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function calculateDeliveryTime($pickupTime) {
        if (!$pickupTime) return null;
        
        $pickup = new DateTime($pickupTime);
        $now = new DateTime();
        $diff = $now->getTimestamp() - $pickup->getTimestamp();
        return round($diff / 60); // minutes
    }
    
    private function uploadProof($file) {
        $uploadDir = __DIR__ . '/../../public/uploads/delivery/proof/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf'];
        if (!isset($allowed[$mimeType])) {
            return null;
        }

        $filename = 'proof_' . uniqid() . '_' . time() . '.' . $allowed[$mimeType];
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'uploads/delivery/proof/' . $filename;
        }

        return null;
    }

    // ========================================
    // TRAINING METHODS
    // ========================================

    /**
     * Training dashboard — list of modules with driver's progress
     */
    public function training(): void {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }
        $driverId = userId();

        $modules = $this->db->query("SELECT * FROM training_modules WHERE is_active = 1 ORDER BY order_num ASC")->fetchAll(\PDO::FETCH_ASSOC);

        // Load driver progress keyed by module_id
        $stmt = $this->db->prepare("SELECT * FROM driver_training_progress WHERE driver_id = ?");
        $stmt->execute([$driverId]);
        $progress = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $progress[$row['module_id']] = $row;
        }

        $stmt = $this->db->prepare("SELECT * FROM driver_certificates WHERE driver_id = ? LIMIT 1");
        $stmt->execute([$driverId]);
        $certificate = $stmt->fetch(\PDO::FETCH_ASSOC);

        $passedCount  = count(array_filter($progress, fn($p) => $p['status'] === 'passed'));
        $totalModules = count($modules);
        $availability = $this->getDriverAvailability($driverId);
        $pageTitle    = $this->isFr() ? 'Formation livreur' : 'Driver Training';

        view('delivery/training', compact('modules', 'progress', 'certificate', 'passedCount', 'totalModules', 'availability', 'pageTitle'));
    }

    /**
     * Read a training module content + quiz
     */
    public function trainingModule(): void {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }
        $driverId = userId();
        $moduleId = (int) ($_GET['id'] ?? 0);
        $phase    = sanitize($_GET['phase'] ?? 'read');

        if (!$moduleId) {
            redirect(url('delivery/training'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM training_modules WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$moduleId]);
        $module = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$module) {
            setFlash('error', 'Module not found.');
            redirect(url('delivery/training'));
            return;
        }

        // Check if driver has access to this module
        $stmt = $this->db->prepare("SELECT * FROM driver_training_progress WHERE driver_id = ? AND module_id = ? LIMIT 1");
        $stmt->execute([$driverId, $moduleId]);
        $progress = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$progress || $progress['status'] === 'locked') {
            setFlash('error', 'Complete the previous module first.');
            redirect(url('delivery/training'));
            return;
        }

        if ($progress['attempts'] >= $module['max_attempts'] && $progress['status'] === 'failed') {
            setFlash('error', 'You have reached the maximum attempts. Please contact admin to reset this module.');
            redirect(url('delivery/training'));
            return;
        }

        // Always load questions — needed for count display on read phase and full data on quiz/results
        $stmt = $this->db->prepare("SELECT * FROM training_questions WHERE module_id = ? ORDER BY order_num, id ASC");
        $stmt->execute([$moduleId]);
        $questions    = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $results      = null;
        $attemptsLeft = max(0, $module['max_attempts'] - ($progress['attempts'] ?? 0));
        $availability = $this->getDriverAvailability($driverId);
        $pageTitle    = ($this->isFr() ? 'Module ' : 'Module ') . $module['order_num'] . ' : ' . $module['title'];

        if ($phase === 'quiz') {
            if (empty($questions)) {
                setFlash('error', 'No questions added for this module yet. Please contact admin.');
                redirect(url('delivery/training'));
                return;
            }
        } elseif ($phase === 'results') {
            $sessionResults = $_SESSION['quiz_results'] ?? null;
            if (!$sessionResults || (int)($sessionResults['module_id'] ?? 0) !== $moduleId) {
                redirect(url('delivery/training/module?id=' . $moduleId));
                return;
            }
            $results   = $sessionResults;
            // Reload progress in case it just changed
            $stmt = $this->db->prepare("SELECT * FROM driver_training_progress WHERE driver_id = ? AND module_id = ? LIMIT 1");
            $stmt->execute([$driverId, $moduleId]);
            $progress     = $stmt->fetch(\PDO::FETCH_ASSOC) ?: $progress;
            $attemptsLeft = max(0, $module['max_attempts'] - ($progress['attempts'] ?? 0));
            unset($_SESSION['quiz_results']);
        }

        view('delivery/training-module', compact('module', 'phase', 'progress', 'questions', 'results', 'attemptsLeft', 'availability', 'pageTitle'));
    }

    /**
     * Submit quiz answers, calculate score, update progress
     */
    public function submitQuiz(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/training'));
            return;
        }

        $driverId = userId();
        $moduleId = (int) post('module_id', 0);
        $answers  = post('answers', []); // [question_id => 'a'/'b'/'c'/'d']

        if (!$moduleId) {
            redirect(url('delivery/training'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM training_modules WHERE id = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$moduleId]);
        $module = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$module) {
            redirect(url('delivery/training'));
            return;
        }

        // Load progress
        $stmt = $this->db->prepare("SELECT * FROM driver_training_progress WHERE driver_id = ? AND module_id = ? LIMIT 1");
        $stmt->execute([$driverId, $moduleId]);
        $progress = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$progress || $progress['status'] === 'locked') {
            setFlash('error', 'Access denied.');
            redirect(url('delivery/training'));
            return;
        }

        // Load questions
        $stmt = $this->db->prepare("SELECT * FROM training_questions WHERE module_id = ? ORDER BY order_num, id ASC");
        $stmt->execute([$moduleId]);
        $questions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($questions)) {
            redirect(url('delivery/training'));
            return;
        }

        // Grade
        $total   = count($questions);
        $correct = 0;
        $results = [];
        foreach ($questions as $q) {
            $given   = $answers[$q['id']] ?? '';
            $isRight = ($given === $q['correct_option']);
            if ($isRight) $correct++;
            $results[] = [
                'question'       => $q['question_text'],
                'your_answer'    => $given,
                'correct_answer' => $q['correct_option'],
                'option_a'       => $q['option_a'],
                'option_b'       => $q['option_b'],
                'option_c'       => $q['option_c'],
                'option_d'       => $q['option_d'],
                'passed'         => $isRight,
                'explanation'    => $q['explanation'],
            ];
        }
        $score   = $total > 0 ? round(($correct / $total) * 100) : 0;
        $passed  = $score >= $module['pass_score'];
        $attempts = ($progress['attempts'] ?? 0) + 1;

        try {
            // Log attempt
            $this->db->prepare("
                INSERT INTO driver_training_attempts (driver_id, module_id, answers_json, score, passed)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$driverId, $moduleId, json_encode($answers), $score, $passed ? 1 : 0]);

            if ($passed) {
                // Mark as passed
                $this->db->prepare("
                    UPDATE driver_training_progress
                    SET status = 'passed', attempts = ?, best_score = GREATEST(best_score, ?), completed_at = NOW(), updated_at = NOW()
                    WHERE driver_id = ? AND module_id = ?
                ")->execute([$attempts, $score, $driverId, $moduleId]);

                // Unlock next module
                $nextStmt = $this->db->prepare("SELECT id FROM training_modules WHERE is_active=1 AND order_num > ? ORDER BY order_num ASC LIMIT 1");
                $nextStmt->execute([$module['order_num']]);
                $nextModuleId = $nextStmt->fetchColumn();

                if ($nextModuleId) {
                    $this->db->prepare("
                        INSERT IGNORE INTO driver_training_progress (driver_id, module_id, status, unlocked_at)
                        VALUES (?, ?, 'available', NOW())
                    ")->execute([$driverId, $nextModuleId]);
                }

                // Check if all modules are passed → issue certificate
                $totalModules = (int) $this->db->query("SELECT COUNT(*) FROM training_modules WHERE is_active=1")->fetchColumn();
                $countStmt = $this->db->prepare("SELECT COUNT(*) FROM driver_training_progress WHERE driver_id = ? AND status = 'passed'");
                $countStmt->execute([$driverId]);
                $passedModules = (int) $countStmt->fetchColumn();

                if ($passedModules >= $totalModules && $totalModules > 0) {
                    $certNum = 'OCS-DRV-' . str_pad($driverId, 5, '0', STR_PAD_LEFT);
                    $inserted = $this->db->prepare("INSERT IGNORE INTO driver_certificates (driver_id, cert_number, issued_at) VALUES (?, ?, NOW())");
                    $inserted->execute([$driverId, $certNum]);

                    // Notify admin that driver is now certified
                    if ($inserted->rowCount() > 0) {
                        $u = user();
                        try {
                            \App\Helpers\NotificationHelper::add(
                                'training_certified',
                                'Driver Training Complete',
                                "{$u['first_name']} {$u['last_name']} has passed all {$totalModules} training modules and earned certificate {$certNum}. They can now accept deliveries.",
                                ['link' => '/admin/training?tab=drivers', 'priority' => 'normal', 'icon' => 'graduation-cap']
                            );
                        } catch (\Exception $e) { /* non-critical */ }
                        try {
                            \App\Helpers\EmailHelper::sendDriverTrainingComplete([
                                'first_name'  => $u['first_name'],
                                'email'       => $u['email'],
                                'cert_number' => $certNum,
                            ]);
                        } catch (\Exception $e) { logger('Training complete email failed: ' . $e->getMessage(), 'warning'); }
                    }
                }
            } else {
                $newStatus = ($attempts >= $module['max_attempts']) ? 'failed' : 'available';
                $this->db->prepare("
                    UPDATE driver_training_progress
                    SET status = ?, attempts = ?, best_score = GREATEST(best_score, ?), updated_at = NOW()
                    WHERE driver_id = ? AND module_id = ?
                ")->execute([$newStatus, $attempts, $score, $driverId, $moduleId]);

                // Notify admin if max attempts reached
                if ($attempts >= $module['max_attempts']) {
                    try {
                        $user = user();
                        \App\Helpers\NotificationHelper::add(
                            'training_failed',
                            'Driver Training — Max Attempts Reached',
                            "{$user['first_name']} {$user['last_name']} has failed Module {$module['order_num']} ({$module['title']}) {$attempts} times and needs admin reset.",
                            ['link' => '/admin/training?tab=drivers', 'priority' => 'normal', 'icon' => 'graduation-cap']
                        );
                    } catch (\Exception $e) { /* non-critical */ }
                }
            }

            // Find next module info for results view
            $nextModuleId    = null;
            $nextModuleOrder = null;
            if ($passed) {
                $nextStmt2 = $this->db->prepare("SELECT id, order_num FROM training_modules WHERE is_active=1 AND order_num > ? ORDER BY order_num ASC LIMIT 1");
                $nextStmt2->execute([$module['order_num']]);
                $nextRow = $nextStmt2->fetch(\PDO::FETCH_ASSOC);
                if ($nextRow) {
                    $nextModuleId    = $nextRow['id'];
                    $nextModuleOrder = $nextRow['order_num'];
                }
            }

            // Store results in session for display — structure matches training-module.php results phase
            $_SESSION['quiz_results'] = [
                'module_id'        => $moduleId,
                'score'            => $score,
                'passed'           => $passed,
                'correct'          => $correct,
                'total'            => $total,
                'answers'          => $answers,      // [question_id => 'a'/'b'/'c'/'d']
                'next_module_id'   => $nextModuleId,
                'next_module_order' => $nextModuleOrder,
                'attempts_left'    => max(0, $module['max_attempts'] - $attempts),
            ];

            redirect(url('delivery/training/module?id=' . $moduleId . '&phase=results'));

        } catch (\Exception $e) {
            logger('Quiz submit error for driver ' . $driverId . ' module ' . $moduleId . ': ' . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred submitting your quiz. Please try again.');
            redirect(url('delivery/training/module?id=' . $moduleId . '&phase=quiz'));
        }
    }

    /**
     * Driver training certificate
     */
    public function certificate(): void {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }
        $stmt = $this->db->prepare("SELECT * FROM driver_certificates WHERE driver_id = ? LIMIT 1");
        $stmt->execute([userId()]);
        $certificate = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$certificate) {
            setFlash('info', 'You have not yet earned your training certificate. Complete all modules first.');
            redirect(url('delivery/training'));
            return;
        }

        $user        = user();
        $driverName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $certDate    = $certificate['issued_at'];
        $certId      = $certificate['id'];
        $modules     = $this->db->query("SELECT * FROM training_modules WHERE is_active = 1 ORDER BY order_num ASC")->fetchAll(\PDO::FETCH_ASSOC);
        $availability = $this->getDriverAvailability(userId());
        $pageTitle   = $this->isFr() ? 'Certificat de formation' : 'Training Certificate';

        view('delivery/training-certificate', compact('driverName', 'certDate', 'certId', 'modules', 'availability', 'pageTitle'));
    }

    /**
     * Background check portal page (authenticated driver)
     */
    public function bgcheck(): void {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }

        $driverId    = userId();
        $availability = $this->getDriverAvailability($driverId);
        $pageTitle   = $this->isFr() ? 'Vérification des antécédents' : 'Background Check';

        // Get driver's application bgcheck data
        $stmt = $this->db->prepare("
            SELECT id, bgcheck_status, bgcheck_doc_type, bgcheck_doc_date,
                   bgcheck_uploaded_at, bgcheck_verified_at, bgcheck_notes, bgcheck_file_path
            FROM driver_applications
            WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$driverId]);
        $application = $stmt->fetch(\PDO::FETCH_ASSOC);

        view('delivery/bgcheck', compact('availability', 'pageTitle', 'application'));
    }

    /**
     * Handle background check upload from driver portal
     */
    public function bgcheckUpload(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/bgcheck'));
            return;
        }

        $driverId = userId();

        // Get application
        $stmt = $this->db->prepare("
            SELECT id, lead_id, bgcheck_status, bgcheck_file_path
            FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$driverId]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$app) {
            setFlash('error', $this->isFr() ? 'Aucune candidature trouvée. Veuillez contacter le support.' : 'No application found. Please contact support.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        if ($app['bgcheck_status'] === 'verified') {
            setFlash('error', $this->isFr() ? 'Votre vérification des antécédents a déjà été vérifiée.' : 'Your background check has already been verified.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        $file = $_FILES['bgcheck_document'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', $this->isFr() ? 'Aucun fichier reçu ou erreur de téléversement. Veuillez réessayer.' : 'No file received or upload error. Please try again.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            setFlash('error', $this->isFr() ? 'Le fichier est trop volumineux. Taille maximale : 10 Mo.' : 'File is too large. Maximum size is 10 MB.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowed, true)) {
            setFlash('error', $this->isFr() ? 'Type de fichier invalide. Veuillez téléverser un PDF, JPG ou PNG.' : 'Invalid file type. Please upload a PDF, JPG, or PNG.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        $docType = sanitize(post('doc_type', 'Not specified'));
        $docDate = post('doc_date', '');
        if ($docDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $docDate)) {
            $docDate = null;
        }

        $uploadDir = __DIR__ . '/../../storage/uploads/bgchecks/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0750, true);
        }
        $ext       = $mimeType === 'application/pdf' ? 'pdf' : ($mimeType === 'image/png' ? 'png' : 'jpg');
        $filename  = 'bgcheck_' . $app['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath  = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', $this->isFr() ? 'Échec du téléversement. Veuillez réessayer ou contacter le support.' : 'Upload failed. Please try again or contact support.');
            redirect(url('delivery/bgcheck'));
            return;
        }

        // Delete old file if exists
        if (!empty($app['bgcheck_file_path'])) {
            $oldPath = $uploadDir . basename($app['bgcheck_file_path']);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $this->db->prepare("
            UPDATE driver_applications
            SET bgcheck_status = 'uploaded',
                bgcheck_file_path = ?,
                bgcheck_doc_type = ?,
                bgcheck_doc_date = ?,
                bgcheck_uploaded_at = NOW()
            WHERE id = ?
        ")->execute([$filename, $docType, $docDate ?: null, $app['id']]);

        // Notify admin
        try {
            $user = user();
            \App\Helpers\NotificationHelper::add(
                'system',
                'Background Check Uploaded',
                "{$user['first_name']} {$user['last_name']} has uploaded their background check and is awaiting verification.",
                [
                    'link'     => !empty($app['lead_id']) ? "/admin/leads/view?id={$app['lead_id']}" : '/admin/leads?interest_type=driver',
                    'icon'     => 'shield-halved',
                    'priority' => 'high',
                ]
            );
        } catch (\Exception $e) { /* non-critical */ }

        setFlash('success', $this->isFr() ? 'Votre vérification des antécédents a été téléversée avec succès. Notre équipe la révisera sous peu.' : 'Your background check has been uploaded successfully. Our team will review it shortly.');
        redirect(url('delivery/bgcheck'));
    }

    // ========================================
    // COMPLIANCE DOCUMENTS
    // ========================================

    private const COMPLIANCE_TYPES = [
        'class5_license',
        'saaq_record',
        'commercial_insurance',
        'vehicle_registration',
        'work_authorization',
    ];

    private const COMPLIANCE_UPLOAD_DIR = __DIR__ . '/../../storage/uploads/compliance_docs/';

    /**
     * GET /delivery/compliance
     */
    public function complianceDocs(): void {
        if (!$this->isDeliveryDriver()) { redirect(url('login')); return; }

        $driverId    = userId();
        $availability = $this->getDriverAvailability($driverId);
        $pageTitle   = $this->isFr() ? 'Documents de conformité' : 'Compliance Documents';

        $appStmt = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $appStmt->execute([$driverId]);
        $application = $appStmt->fetch(\PDO::FETCH_ASSOC);

        $docs = [];
        if ($application) {
            $rows = $this->db->prepare("SELECT * FROM driver_compliance_docs WHERE application_id = ?");
            $rows->execute([$application['id']]);
            foreach ($rows->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $docs[$row['doc_type']] = $row;
            }
        }

        view('delivery/compliance', compact('availability', 'pageTitle', 'application', 'docs'));
    }

    /**
     * POST /delivery/compliance/upload
     */
    public function complianceUpload(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/compliance'));
            return;
        }

        $driverId = userId();
        $docType  = sanitize(post('doc_type', ''));

        if (!in_array($docType, self::COMPLIANCE_TYPES, true)) {
            setFlash('error', $this->isFr() ? 'Type de document invalide.' : 'Invalid document type.');
            redirect(url('delivery/compliance'));
            return;
        }

        $appStmt = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $appStmt->execute([$driverId]);
        $app = $appStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$app) {
            setFlash('error', $this->isFr() ? 'Aucune candidature trouvée. Veuillez contacter le support.' : 'No application found. Please contact support.');
            redirect(url('delivery/compliance'));
            return;
        }

        // Check not already verified
        $existing = $this->db->prepare("SELECT * FROM driver_compliance_docs WHERE application_id = ? AND doc_type = ? LIMIT 1");
        $existing->execute([$app['id'], $docType]);
        $existingDoc = $existing->fetch(\PDO::FETCH_ASSOC);

        if ($existingDoc && $existingDoc['status'] === 'verified') {
            setFlash('error', $this->isFr() ? 'Ce document a déjà été vérifié et ne peut pas être remplacé.' : 'This document has already been verified and cannot be replaced.');
            redirect(url('delivery/compliance'));
            return;
        }

        $file = $_FILES['doc_file'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', $this->isFr() ? 'Aucun fichier reçu ou erreur de téléversement. Veuillez réessayer.' : 'No file received or upload error. Please try again.');
            redirect(url('delivery/compliance'));
            return;
        }

        if ($file['size'] > 10 * 1024 * 1024) {
            setFlash('error', $this->isFr() ? 'Le fichier est trop volumineux. Taille maximale : 10 Mo.' : 'File is too large. Maximum size is 10 MB.');
            redirect(url('delivery/compliance'));
            return;
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $allowed  = ['application/pdf', 'image/jpeg', 'image/png'];
        if (!in_array($mimeType, $allowed, true)) {
            setFlash('error', $this->isFr() ? 'Type de fichier invalide. Veuillez téléverser un PDF, JPG ou PNG.' : 'Invalid file type. Please upload a PDF, JPG, or PNG.');
            redirect(url('delivery/compliance'));
            return;
        }

        $docDate    = post('doc_date', '');
        if ($docDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $docDate)) $docDate = null;
        $docSubtype = sanitize(post('doc_subtype', ''));

        $uploadDir = self::COMPLIANCE_UPLOAD_DIR;
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0750, true);

        $ext      = $mimeType === 'application/pdf' ? 'pdf' : ($mimeType === 'image/png' ? 'png' : 'jpg');
        $filename = $docType . '_' . $app['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', $this->isFr() ? 'Échec du téléversement. Veuillez réessayer ou contacter le support.' : 'Upload failed. Please try again or contact support.');
            redirect(url('delivery/compliance'));
            return;
        }

        // Delete old file if exists
        if ($existingDoc && !empty($existingDoc['file_path'])) {
            $old = $uploadDir . basename($existingDoc['file_path']);
            if (file_exists($old)) @unlink($old);
        }

        if ($existingDoc) {
            $this->db->prepare("
                UPDATE driver_compliance_docs
                SET status = 'uploaded', file_path = ?, doc_date = ?, doc_subtype = ?,
                    uploaded_at = NOW(), verified_at = NULL, verified_by = NULL, admin_notes = NULL
                WHERE id = ?
            ")->execute([$filename, $docDate ?: null, $docSubtype ?: null, $existingDoc['id']]);
        } else {
            $this->db->prepare("
                INSERT INTO driver_compliance_docs
                    (application_id, driver_id, doc_type, status, file_path, doc_date, doc_subtype, uploaded_at)
                VALUES (?, ?, ?, 'uploaded', ?, ?, ?, NOW())
            ")->execute([$app['id'], $driverId, $docType, $filename, $docDate ?: null, $docSubtype ?: null]);
        }

        try {
            $u = user();
            \App\Helpers\NotificationHelper::add(
                'system',
                'Compliance Document Uploaded',
                "{$u['first_name']} {$u['last_name']} uploaded a compliance document: " . ucwords(str_replace('_', ' ', $docType)) . ".",
                ['link' => '/admin/delivery/driver-details?id=' . $driverId, 'icon' => 'file-shield', 'priority' => 'normal']
            );
        } catch (\Exception $e) { /* non-critical */ }

        setFlash('success', $this->isFr() ? 'Document téléversé avec succès. Notre équipe le révisera sous peu.' : 'Document uploaded successfully. Our team will review it shortly.');
        redirect(url('delivery/compliance'));
    }

    /**
     * POST /delivery/compliance/not-required
     */
    public function complianceNotRequired(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/compliance'));
            return;
        }

        $driverId = userId();
        $docType  = sanitize(post('doc_type', ''));
        $reason   = trim(post('reason', ''));

        if (!in_array($docType, self::COMPLIANCE_TYPES, true) || empty($reason)) {
            setFlash('error', $this->isFr() ? 'Requête invalide.' : 'Invalid request.');
            redirect(url('delivery/compliance'));
            return;
        }

        $appStmt = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $appStmt->execute([$driverId]);
        $app = $appStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$app) {
            setFlash('error', $this->isFr() ? 'Aucune candidature trouvée.' : 'No application found.');
            redirect(url('delivery/compliance'));
            return;
        }

        $existing = $this->db->prepare("SELECT id, status FROM driver_compliance_docs WHERE application_id = ? AND doc_type = ? LIMIT 1");
        $existing->execute([$app['id'], $docType]);
        $existingDoc = $existing->fetch(\PDO::FETCH_ASSOC);

        if ($existingDoc && $existingDoc['status'] === 'verified') {
            setFlash('error', $this->isFr() ? 'Ce document a déjà été vérifié.' : 'This document has already been verified.');
            redirect(url('delivery/compliance'));
            return;
        }

        if ($existingDoc) {
            $this->db->prepare("
                UPDATE driver_compliance_docs SET status = 'not_required', admin_notes = ?, file_path = NULL, uploaded_at = NULL
                WHERE id = ?
            ")->execute([$reason, $existingDoc['id']]);
        } else {
            $this->db->prepare("
                INSERT INTO driver_compliance_docs (application_id, driver_id, doc_type, status, admin_notes)
                VALUES (?, ?, ?, 'not_required', ?)
            ")->execute([$app['id'], $driverId, $docType, $reason]);
        }

        setFlash('success', $this->isFr() ? 'Document marqué comme non requis. Un administrateur examinera cette demande.' : 'Document marked as not required. An admin will review this.');
        redirect(url('delivery/compliance'));
    }

    // ========================================
    // DRIVER PROFILE
    // ========================================

    /**
     * Driver settings page
     */
    public function settings(): void {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }

        $payStmt = $this->db->prepare(
            "SELECT * FROM driver_payment_info WHERE user_id = ? LIMIT 1"
        );
        $payStmt->execute([userId()]);
        $payment = $payStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        view('delivery.settings', [
            'pageTitle'   => $this->isFr() ? 'Paramètres' : 'Settings',
            'currentPage' => 'settings',
            'driver'      => user(),
            'payment'     => $payment,
        ]);
    }

    /**
     * Save driver payment info — JSON response
     */
    public function updatePaymentAjax(): void {
        header('Content-Type: application/json');
        if (!$this->isDeliveryDriver() || !isPost()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $fr = $this->isFr();

        $pref    = $_POST['payment_preference'] ?? '';
        $allowed = ['eft', 'interac', 'cheque'];
        if ($pref && !in_array($pref, $allowed, true)) {
            echo json_encode(['success' => false, 'message' => $fr ? 'Préférence invalide.' : 'Invalid payment preference.']);
            return;
        }

        $bankName    = trim($_POST['bank_name']           ?? '');
        $holder      = trim($_POST['bank_account_holder'] ?? '');
        $transit     = preg_replace('/\D/', '', $_POST['bank_transit']    ?? '');
        $institution = preg_replace('/\D/', '', $_POST['bank_institution'] ?? '');
        $account     = preg_replace('/\D/', '', $_POST['bank_account']    ?? '');
        $accountType = $_POST['bank_account_type'] ?? '';
        $interacEmail= trim($_POST['interac_email'] ?? '');

        if ($pref === 'eft' && (!$bankName || !$holder)) {
            echo json_encode(['success' => false, 'message' => $fr
                ? 'Nom de la banque et titulaire du compte requis pour le virement direct.'
                : 'Bank name and account holder are required for EFT/Direct Deposit.'
            ]);
            return;
        }

        if ($pref === 'interac' && (!$interacEmail || !filter_var($interacEmail, FILTER_VALIDATE_EMAIL))) {
            echo json_encode(['success' => false, 'message' => $fr
                ? 'Courriel valide requis pour le virement Interac.'
                : 'A valid email is required for Interac e-Transfer.'
            ]);
            return;
        }

        $this->db->prepare("
            INSERT INTO driver_payment_info
                (user_id, payment_preference, bank_name, bank_account_holder,
                 bank_transit, bank_institution, bank_account, bank_account_type, interac_email)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                payment_preference  = VALUES(payment_preference),
                bank_name           = VALUES(bank_name),
                bank_account_holder = VALUES(bank_account_holder),
                bank_transit        = VALUES(bank_transit),
                bank_institution    = VALUES(bank_institution),
                bank_account        = VALUES(bank_account),
                bank_account_type   = VALUES(bank_account_type),
                interac_email       = VALUES(interac_email),
                updated_at          = NOW()
        ")->execute([
            userId(),
            $pref ?: null,
            $bankName    ?: null,
            $holder      ?: null,
            $transit     ?: null,
            $institution ?: null,
            $account     ?: null,
            in_array($accountType, ['chequing','savings']) ? $accountType : null,
            $interacEmail ?: null,
        ]);

        echo json_encode(['success' => true, 'message' => $fr
            ? 'Informations de paiement enregistrées.'
            : 'Payment information saved.'
        ]);
    }

    /**
     * Update driver profile info (name/phone) — JSON response
     */
    public function updateProfileAjax(): void {
        header('Content-Type: application/json');
        if (!$this->isDeliveryDriver() || !isPost()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $fr = $this->isFr();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $phone     = trim($_POST['phone']      ?? '');

        if (!$firstName || !$lastName) {
            echo json_encode(['success' => false, 'message' => $fr ? 'Prénom et nom requis.' : 'First and last name are required.']);
            return;
        }

        $this->db->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW() WHERE id = ?"
        )->execute([$firstName, $lastName, $phone, userId()]);

        $_SESSION['user']['first_name'] = $firstName;
        $_SESSION['user']['last_name']  = $lastName;
        $_SESSION['user']['phone']      = $phone;

        echo json_encode(['success' => true, 'message' => $fr ? 'Profil enregistré.' : 'Profile saved.']);
    }

    /**
     * Show forced password-change page (first login after admin approval)
     */
    public function showChangePassword(): void
    {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }

        $stmt = $this->db->prepare("SELECT force_password_reset FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([userId()]);
        if (!(bool)$stmt->fetchColumn()) {
            redirect(url('delivery/dashboard'));
            return;
        }

        view('delivery.change-password', [
            'pageTitle' => $this->isFr() ? 'Créer votre mot de passe' : 'Create your password',
        ]);
    }

    /**
     * Process forced password change (POST)
     */
    public function processChangePassword(): void
    {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('login'));
            return;
        }

        $stmt = $this->db->prepare("SELECT force_password_reset FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([userId()]);
        if (!(bool)$stmt->fetchColumn()) {
            redirect(url('delivery/dashboard'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', $this->isFr() ? 'Requête invalide.' : 'Invalid request.');
            redirect(url('delivery/change-password'));
            return;
        }

        $fr      = $this->isFr();
        $new     = post('new_password', '');
        $confirm = post('confirm_password', '');

        if (
            strlen($new) < 10 ||
            !preg_match('/[A-Z]/', $new) ||
            !preg_match('/[a-z]/', $new) ||
            !preg_match('/[0-9]/', $new) ||
            !preg_match('/[!@#$%^&*]/', $new)
        ) {
            setFlash('error', $fr
                ? 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (!@#$%^&*).'
                : 'Password must be at least 10 characters and include uppercase, lowercase, number, and special character (!@#$%^&*).');
            redirect(url('delivery/change-password'));
            return;
        }

        if ($new !== $confirm) {
            setFlash('error', $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.');
            redirect(url('delivery/change-password'));
            return;
        }

        $this->db->prepare("
            UPDATE users SET password = ?, force_password_reset = 0, updated_at = NOW() WHERE id = ?
        ")->execute([password_hash($new, PASSWORD_DEFAULT), userId()]);

        setFlash('success', $fr ? 'Mot de passe créé avec succès. Bienvenue!' : 'Password set successfully. Welcome!');
        redirect(url('delivery/dashboard'));
    }

    /**
     * Update driver password — JSON response
     */
    public function updatePasswordAjax(): void {
        header('Content-Type: application/json');
        if (!$this->isDeliveryDriver() || !isPost()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        $fr = $this->isFr();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$new || !$confirm) {
            echo json_encode(['success' => false, 'message' => $fr ? 'Tous les champs sont requis.' : 'All fields are required.']);
            return;
        }

        if (
            strlen($new) < 10 ||
            !preg_match('/[A-Z]/', $new) ||
            !preg_match('/[a-z]/', $new) ||
            !preg_match('/[0-9]/', $new) ||
            !preg_match('/[!@#$%^&*]/', $new)
        ) {
            echo json_encode(['success' => false, 'message' => $fr
                ? 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (!@#$%^&*).'
                : 'Password must be at least 10 characters and include one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*).'
            ]);
            return;
        }

        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'message' => $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.']);
            return;
        }

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([userId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password'])) {
            echo json_encode(['success' => false, 'message' => $fr ? 'Mot de passe actuel incorrect.' : 'Current password is incorrect.']);
            return;
        }

        $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")
                 ->execute([password_hash($new, PASSWORD_DEFAULT), userId()]);

        echo json_encode(['success' => true, 'message' => $fr ? 'Mot de passe mis à jour avec succès.' : 'Password updated successfully.']);
    }

    /**
     * Driver profile page (web) — kept for redirect compatibility
     */
    public function profile(): void {
        redirect(url('delivery/settings'));
    }

    /**
     * Update driver photo from web portal
     */
    public function updatePhoto(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/profile'));
            return;
        }
        $fr = $this->isFr();

        $file = $_FILES['photo'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            setFlash('error', $fr ? 'Aucun fichier reçu.' : 'No file uploaded.');
            redirect(url('delivery/profile'));
            return;
        }

        // Validate size (5 MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            setFlash('error', $fr ? 'La photo doit faire moins de 5 Mo.' : 'Photo must be under 5 MB.');
            redirect(url('delivery/profile'));
            return;
        }

        // Validate MIME type
        $mime = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($mime, $allowed, true)) {
            setFlash('error', $fr ? 'Seuls les formats JPEG, PNG ou WebP sont acceptés.' : 'Only JPEG, PNG or WebP images are allowed.');
            redirect(url('delivery/profile'));
            return;
        }

        $uploadDir = BASE_PATH . '/public/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $ext      = $mime === 'image/png' ? 'png' : ($mime === 'image/webp' ? 'webp' : 'jpg');
        $filename = 'driver_' . userId() . '_' . time() . '.' . $ext;
        $destPath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', $fr ? 'Échec du téléversement. Veuillez réessayer.' : 'Upload failed. Please try again.');
            redirect(url('delivery/profile'));
            return;
        }

        // Delete old avatar file if it's in the avatars folder
        $current = user()['avatar'] ?? '';
        if ($current && str_starts_with($current, 'uploads/avatars/')) {
            $oldPath = BASE_PATH . '/public/' . $current;
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        $relativePath = 'uploads/avatars/' . $filename;
        $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?")
                 ->execute([$relativePath, userId()]);

        // Refresh session user data
        $_SESSION['user']['avatar'] = $relativePath;

        setFlash('success', $fr ? 'Photo de profil mise à jour.' : 'Profile photo updated successfully.');
        redirect(url('delivery/profile'));
    }

    /**
     * Change driver password from web portal
     */
    public function changePassword(): void {
        if (!$this->isDeliveryDriver() || !isPost()) {
            redirect(url('delivery/profile'));
            return;
        }
        $fr = $this->isFr();

        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$current || !$new || !$confirm) {
            setFlash('error', $fr ? 'Tous les champs sont requis.' : 'All fields are required.');
            redirect(url('delivery/profile'));
            return;
        }

        if (
            strlen($new) < 10 ||
            !preg_match('/[A-Z]/', $new) ||
            !preg_match('/[a-z]/', $new) ||
            !preg_match('/[0-9]/', $new) ||
            !preg_match('/[!@#$%^&*]/', $new)
        ) {
            setFlash('error', $fr
                ? 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (!@#$%^&*).'
                : 'Password must be at least 10 characters and include one uppercase letter, one lowercase letter, one number, and one special character (!@#$%^&*).'
            );
            redirect(url('delivery/profile'));
            return;
        }

        if ($new !== $confirm) {
            setFlash('error', $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.');
            redirect(url('delivery/profile'));
            return;
        }

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([userId()]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($current, $row['password'])) {
            setFlash('error', $fr ? 'Mot de passe actuel incorrect.' : 'Current password is incorrect.');
            redirect(url('delivery/profile'));
            return;
        }

        $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")
                 ->execute([password_hash($new, PASSWORD_DEFAULT), userId()]);

        setFlash('success', $fr ? 'Mot de passe mis à jour avec succès.' : 'Password updated successfully.');
        redirect(url('delivery/profile'));
    }

    // ========================================
    // PUBLIC PAGES (no auth required)
    // ========================================

    /**
     * Driver application form (public)
     */
    public function apply() {
        if (isPost()) {
            return $this->processApplication();
        }

        return view('delivery/apply', [
            'pageTitle' => $this->isFr() ? 'Postuler comme livreur' : 'Apply to Deliver'
        ]);
    }

    /**
     * Process driver application submission
     */
    private function processApplication() {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('delivery/apply'));
            return;
        }

        // Save submitted data to session so the form can be re-populated on error
        $_SESSION['_apply_old_input'] = $_POST;

        $firstName = sanitize(post('first_name', ''));
        $lastName = sanitize(post('last_name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        $dob = post('date_of_birth', '');
        $street = sanitize(post('street_address', ''));
        $city = sanitize(post('city', ''));
        $province = sanitize(post('province', ''));
        $postalCode = sanitize(post('postal_code', ''));
        $vehicleType = sanitize(post('vehicle_type', ''));
        $licenseNumber = sanitize(post('license_number', ''));
        $licenseExpiry = post('license_expiry', '') ?: null;
        $availableDays = post('available_days', []);
        $preferredShift = sanitize(post('preferred_shift', 'flexible'));
        $motivation = sanitize(post('motivation', ''));
        $previousExp = post('previous_experience', 'no');
        $criminalRecord = post('criminal_record', 'no') === 'yes' ? 1 : 0;
        $criminalDetails = $criminalRecord ? sanitize(post('criminal_record_details', '')) : null;

        // Validate required fields
        if (!$firstName || !$lastName || !$email || !$phone || !$dob || !$street || !$city || !$province || !$postalCode || !$vehicleType) {
            setFlash('error', 'Please fill in all required fields.');
            redirect(url('delivery/apply'));
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            redirect(url('delivery/apply'));
            return;
        }

        // Check age (must be 18+)
        $age = (int)date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 18) {
            setFlash('error', 'You must be at least 18 years old to apply.');
            redirect(url('delivery/apply'));
            return;
        }

        if (empty($availableDays)) {
            setFlash('error', 'Please select at least one available day.');
            redirect(url('delivery/apply'));
            return;
        }

        try {
            // Check 90-day reapplication cooling period for rejected applicants
            $stmt = $this->db->prepare("
                SELECT id, status, reapply_after FROM driver_applications
                WHERE email = ? AND status = 'rejected'
                ORDER BY updated_at DESC LIMIT 1
            ");
            $stmt->execute([$email]);
            $rejectedApp = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($rejectedApp && $rejectedApp['reapply_after'] && strtotime($rejectedApp['reapply_after']) > time()) {
                $coolDate = date('F j, Y', strtotime($rejectedApp['reapply_after']));
                setFlash('error', "You may reapply after $coolDate.");
                redirect(url('delivery/apply'));
                return;
            }

            // Check for any existing application to prevent duplicate key errors
            $stmt = $this->db->prepare("SELECT id, status FROM driver_applications WHERE email = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$email]);
            $existingApp = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existingApp) {
                $status = $existingApp['status'];
                if (in_array($status, ['pending', 'under_review', 'interview_requested', 'interview_scheduled'])) {
                    setFlash('error', 'An application with this email is already under review. Please log in to check your status.');
                    redirect(url('delivery/apply'));
                    return;
                }
                if ($status === 'approved') {
                    setFlash('error', 'You are already an approved driver. Please log in to access your dashboard.');
                    redirect(url('delivery/apply'));
                    return;
                }
                // For rejected (past cooling) or other statuses, allow reapply
            }

            // Block only truly active drivers - rejected/inactive/unverified users can re-apply
            $stmt = $this->db->prepare("
                SELECT u.id FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id AND r.name = 'delivery'
                WHERE u.email = ? AND (u.role = 'delivery' OR r.name = 'delivery')
                AND u.status = 'active'
            ");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                setFlash('error', 'You are already an active delivery driver. Please log in to access your dashboard.');
                redirect(url('delivery/apply'));
                return;
            }

            $daysStr = implode(',', array_map('sanitize', $availableDays));

            // ── 1. Create unverified user account ───────────────────────────
            $tempPassword   = bin2hex(random_bytes(5));
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

            $existingUser = $this->db->prepare("SELECT id, status FROM users WHERE email = ? LIMIT 1");
            $existingUser->execute([$email]);
            $existingRow = $existingUser->fetch(PDO::FETCH_ASSOC);

            $this->db->beginTransaction();

            if (!$existingRow) {
                $this->db->prepare("
                    INSERT INTO users (email, password, first_name, last_name, phone, status, role, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, 'unverified', 'delivery', NOW(), NOW())
                ")->execute([$email, $hashedPassword, $firstName, $lastName, $phone]);
                $userId      = $this->db->lastInsertId();
                $isNewUser   = true;

                $rStmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'delivery' LIMIT 1");
                $rStmt->execute();
                $roleId = $rStmt->fetchColumn();
                if (!$roleId) {
                    $this->db->prepare("INSERT INTO roles (name, display_name, description) VALUES ('delivery','Delivery Driver','Delivery driver role')")->execute();
                    $roleId = $this->db->lastInsertId();
                }
                $this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)")->execute([$userId, $roleId]);
            } else {
                $userId      = $existingRow['id'];
                $isNewUser   = false;
                $tempPassword = null;
            }

            // Store verification code in users table
            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->db->prepare("
                UPDATE users SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?
                WHERE id = ?
            ")->execute([$verificationCode, $verificationExpires, $userId]);

            $this->db->commit();

            // Store all form data in session to complete application after verification
            $_SESSION['pending_driver_verification'] = [
                'user_id'       => $userId,
                'is_new_user'   => $isNewUser,
                'temp_password' => $tempPassword,
                'email'         => $email,
                'first_name'    => $firstName,
                'last_name'     => $lastName,
                'phone'         => $phone,
                'dob'           => $dob,
                'street'        => $street,
                'city'          => $city,
                'province'      => $province,
                'postal_code'   => $postalCode,
                'vehicle_type'  => $vehicleType,
                'license_number'=> $licenseNumber,
                'license_expiry'=> $licenseExpiry,
                'days_str'      => $daysStr,
                'preferred_shift' => $preferredShift,
                'motivation'    => $motivation,
                'previous_exp'  => $previousExp,
                'criminal_record' => $criminalRecord,
                'criminal_details' => $criminalDetails,
            ];
            $_SESSION['driver_verification_attempts'] = 0;

            // Persist draft to DB so verification works if session expires
            $this->db->prepare("UPDATE users SET application_draft = ? WHERE id = ?")
                ->execute([json_encode($_SESSION['pending_driver_verification']), $userId]);

            unset($_SESSION['_apply_old_input']);

            try {
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendUserVerificationCode([
                    'first_name'        => $firstName,
                    'email'             => $email,
                    'verification_code' => $verificationCode,
                    'verify_url_fr'     => $appUrl . '/delivery/verify-email?uid=' . $userId . '&lang=fr',
                    'verify_url_en'     => $appUrl . '/delivery/verify-email?uid=' . $userId . '&lang=en',
                    'magic_link_url_fr' => $appUrl . '/delivery/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/delivery/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
                logger("Driver verification code sent to {$email}", 'info');
            } catch (Exception $e) {
                error_log('Driver verification code email failed: ' . $e->getMessage());
            }

            redirect(url('delivery/verify-email'));

        } catch (Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log('Driver application error: ' . $e->getMessage());
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('delivery/apply'));
        }
    }

    // ========================================
    // ========================================
    // EMAIL VERIFICATION (driver applicants)
    // ========================================

    public function showVerifyEmail(): void {
        if (empty($_SESSION['pending_driver_verification'])) {
            // Allow rendering when arriving from a magic-link fallback (?uid= present)
            $uid = (int) ($_GET['uid'] ?? 0);
            if (!$uid) {
                redirect(url('delivery/apply'));
                return;
            }
            // Reload session from DB draft so the rest of the flow works normally
            $stmt = $this->db->prepare("SELECT application_draft FROM users WHERE id = ? AND status = 'unverified'");
            $stmt->execute([$uid]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$row || empty($row['application_draft'])) {
                redirect(url('delivery/apply'));
                return;
            }
            $_SESSION['pending_driver_verification'] = json_decode($row['application_draft'], true);
            $_SESSION['driver_verification_attempts'] = $_SESSION['driver_verification_attempts'] ?? 0;
        }
        view('delivery.verify-email');
    }

    public function verifyDriverEmail(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('delivery/verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('delivery/verify-email'));
            return;
        }

        $pending = $_SESSION['pending_driver_verification'] ?? null;
        if (!$pending) {
            redirect(url('delivery/apply'));
            return;
        }

        $maxAttempts = 5;
        $attempts    = &$_SESSION['driver_verification_attempts'];

        if ($attempts >= $maxAttempts) {
            setFlash('error', 'Too many attempts. Please request a new code.');
            redirect(url('delivery/verify-email'));
            return;
        }

        $submitted = preg_replace('/\D/', '', post('code', ''));

        if (strlen($submitted) !== 6) {
            $attempts++;
            setFlash('error', 'Please enter the complete 6-digit code.');
            redirect(url('delivery/verify-email'));
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT email_verification_code, email_verification_expires_at
                FROM users WHERE id = ? AND status = 'unverified'
            ");
            $stmt->execute([$pending['user_id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                setFlash('error', 'Account not found. Please apply again.');
                unset($_SESSION['pending_driver_verification'], $_SESSION['driver_verification_attempts']);
                redirect(url('delivery/apply'));
                return;
            }

            if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
                setFlash('error', 'Your code has expired. Please request a new one.');
                redirect(url('delivery/verify-email'));
                return;
            }

            if (!hash_equals($row['email_verification_code'], $submitted)) {
                $attempts++;
                $remaining = $maxAttempts - $attempts;
                $msg = $remaining > 0
                    ? "Incorrect code. {$remaining} attempt(s) remaining."
                    : 'Too many failed attempts. Please request a new code.';
                setFlash('error', $msg);
                redirect(url('delivery/verify-email'));
                return;
            }

            // Code valid — complete the application
            $this->completeDriverVerification($pending);
            setFlash('success', $this->isFr()
                ? 'Courriel vérifié ! Votre candidature a été soumise. Connectez-vous dès que votre compte est activé par notre équipe.'
                : 'Email verified! Your application has been submitted. Log in once our team activates your account.'
            );
            redirect(url('delivery/login'));

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log('Driver email verification error: ' . $e->getMessage());
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('delivery/verify-email'));
        }
    }

    public function autoVerifyDriverEmail(): void
    {
        $uid  = (int) ($_GET['uid'] ?? 0);
        $code = preg_replace('/\D/', '', $_GET['code'] ?? '');

        if (!$uid || strlen($code) !== 6) {
            setFlash('error', 'Invalid verification link.');
            redirect(url('delivery/apply'));
            return;
        }

        $pending = $_SESSION['pending_driver_verification'] ?? null;

        // No session or wrong user — try loading draft from DB
        if (!$pending || (int) $pending['user_id'] !== $uid) {
            $stmt = $this->db->prepare("SELECT application_draft FROM users WHERE id = ? AND status = 'unverified'");
            $stmt->execute([$uid]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($row && !empty($row['application_draft'])) {
                $pending = json_decode($row['application_draft'], true);
                $_SESSION['pending_driver_verification'] = $pending;
                $_SESSION['driver_verification_attempts'] = 0;
            } else {
                // Draft gone — send to manual entry with uid + code so page can still render
                redirect(url('delivery/verify-email') . '?uid=' . $uid . '&code=' . urlencode($code));
                return;
            }
        }

        try {
            $stmt = $this->db->prepare("
                SELECT email_verification_code, email_verification_expires_at
                FROM users WHERE id = ? AND status = 'unverified'
            ");
            $stmt->execute([$uid]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                setFlash('info', 'Your email may already be verified. Please log in.');
                redirect(url('login'));
                return;
            }

            if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
                setFlash('error', 'This verification link has expired. Please enter your code manually.');
                redirect(url('delivery/verify-email'));
                return;
            }

            if (!hash_equals($row['email_verification_code'], $code)) {
                setFlash('error', 'Invalid verification link. Please enter your code manually.');
                redirect(url('delivery/verify-email'));
                return;
            }

            $this->completeDriverVerification($pending);
            setFlash('success', $this->isFr()
                ? 'Courriel vérifié ! Votre candidature a été soumise. Connectez-vous dès que votre compte est activé par notre équipe.'
                : 'Email verified! Your application has been submitted. Log in once our team activates your account.'
            );
            redirect(url('delivery/login'));

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log('Auto verify error: ' . $e->getMessage());
            setFlash('error', 'An error occurred. Please enter your code manually.');
            redirect(url('delivery/verify-email'));
        }
    }

    private function completeDriverVerification(array $pending): void
    {
        $this->db->beginTransaction();

        $this->db->prepare("
            UPDATE users SET
                status = 'pending',
                email_verified_at = NOW(),
                email_verification_code = NULL,
                email_verification_expires_at = NULL,
                email_verification_attempts = 0,
                application_draft = NULL
            WHERE id = ?
        ")->execute([$pending['user_id']]);

        $leadId = null;
        $leadsExists = $this->db->query("SHOW TABLES LIKE 'leads'")->rowCount();
        if ($leadsExists) {
            $this->db->prepare("
                INSERT INTO leads (first_name, last_name, email, phone, source, source_details, status, priority, interest_type, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'website', 'Driver Application Form', 'new', 'medium', 'driver', NOW(), NOW())
            ")->execute([$pending['first_name'], $pending['last_name'], $pending['email'], $pending['phone']]);
            $leadId = $this->db->lastInsertId();
        }

        $stmt = $this->db->prepare("
            INSERT INTO driver_applications
            (user_id, lead_id, first_name, last_name, email, phone, date_of_birth, street_address, city, province, postal_code,
             vehicle_type, license_number, license_expiry, available_days, preferred_shift, motivation, previous_experience,
             criminal_record, criminal_record_details, status, pipeline_stage)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'submitted')
        ");
        $stmt->execute([
            $pending['user_id'], $leadId,
            $pending['first_name'], $pending['last_name'], $pending['email'], $pending['phone'], $pending['dob'],
            $pending['street'], $pending['city'], $pending['province'], $pending['postal_code'],
            $pending['vehicle_type'], $pending['license_number'] ?: null, $pending['license_expiry'],
            $pending['days_str'], $pending['preferred_shift'], $pending['motivation'] ?: null, $pending['previous_exp'],
            $pending['criminal_record'], $pending['criminal_details'],
        ]);
        $applicationId = $this->db->lastInsertId();

        $this->db->commit();

        try {
            \App\Helpers\EmailHelper::sendDriverApplicationReceived([
                'first_name'     => $pending['first_name'],
                'email'          => $pending['email'],
                'application_id' => $applicationId,
                'temp_password'  => $pending['temp_password'],
            ]);
        } catch (\Exception $e) {
            error_log('Driver application email failed: ' . $e->getMessage());
        }

        try {
            $notifLink = $leadId ? "/admin/leads/view?id={$leadId}" : "/admin/delivery/staff?tab=applications&app={$applicationId}";

            \App\Helpers\NotificationHelper::add(
                'driver_application',
                'New Driver Application',
                "New driver application #{$applicationId} from {$pending['first_name']} {$pending['last_name']} ({$pending['email']}).",
                ['link' => $notifLink, 'icon' => 'car']
            );

            $mailConfig = require dirname(__DIR__, 2) . '/config/mail.php';
            $adminEmail = $mailConfig['admin_email'] ?? 'info@ocsapp.ca';

            \App\Helpers\EmailHelper::sendRaw(
                $adminEmail,
                "New Driver Application #{$applicationId}",
                "<p>A new driver application has been submitted.</p>
                 <p><strong>Name:</strong> {$pending['first_name']} {$pending['last_name']}<br>
                 <strong>Email:</strong> {$pending['email']}<br>
                 <strong>Phone:</strong> {$pending['phone']}<br>
                 <strong>Vehicle:</strong> {$pending['vehicle_type']}<br>
                 <strong>City:</strong> {$pending['city']}, {$pending['province']}</p>
                 <p><a href='https://ocsapp.ca/admin/delivery/staff?tab=applications'>Review Application</a></p>"
            );
        } catch (\Exception $e) {
            error_log('Driver application admin notification failed: ' . $e->getMessage());
        }

        unset($_SESSION['pending_driver_verification'], $_SESSION['driver_verification_attempts']);
        logger("Driver application verified and submitted: {$pending['email']} (App #{$applicationId})", 'info');
    }

    public function resendDriverVerification(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('delivery/verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('delivery/verify-email'));
            return;
        }

        $pending = $_SESSION['pending_driver_verification'] ?? null;
        if (!$pending) {
            redirect(url('delivery/apply'));
            return;
        }

        try {
            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $this->db->prepare("
                UPDATE users SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?,
                    email_verification_attempts = 0
                WHERE id = ? AND status = 'unverified'
            ")->execute([$verificationCode, $verificationExpires, $pending['user_id']]);

            $_SESSION['driver_verification_attempts'] = 0;

            try {
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendUserVerificationCode([
                    'first_name'        => $pending['first_name'],
                    'email'             => $pending['email'],
                    'verification_code' => $verificationCode,
                    'verify_url_fr'     => $appUrl . '/delivery/verify-email?uid=' . $pending['user_id'] . '&lang=fr',
                    'verify_url_en'     => $appUrl . '/delivery/verify-email?uid=' . $pending['user_id'] . '&lang=en',
                    'magic_link_url_fr' => $appUrl . '/delivery/verify-email/auto?uid=' . $pending['user_id'] . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/delivery/verify-email/auto?uid=' . $pending['user_id'] . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
            } catch (\Exception $e) {
                error_log('Driver resend verification failed: ' . $e->getMessage());
            }

            setFlash('success', 'A new code has been sent to your email.');
            redirect(url('delivery/verify-email'));

        } catch (\Exception $e) {
            error_log('Driver resend verification error: ' . $e->getMessage());
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('delivery/verify-email'));
        }
    }

    // DRIVER APPLICATION PORTAL (pending users)
    // ========================================

    /**
     * Application status portal — for pending driver applicants
     */
    public function applicationStatus()
    {
        if (!isset($_SESSION['user'])) {
            redirect(url('login'));
            return;
        }

        $userId = userId();
        $userStatus = $_SESSION['user']['status'] ?? 'active';
        $role = $_SESSION['user']['role'] ?? '';

        // Only pending delivery users can see this
        if ($role !== 'delivery' || $userStatus !== 'pending') {
            redirect(url('delivery/dashboard'));
            return;
        }

        // Load application
        $stmt = $this->db->prepare("
            SELECT * FROM driver_applications
            WHERE user_id = ?
            ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$userId]);
        $application = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$application) {
            setFlash('error', 'No application found for your account.');
            redirect(url('login'));
            return;
        }

        // Load messages (visible to applicant)
        $msgStmt = $this->db->prepare("
            SELECT * FROM driver_application_messages
            WHERE application_id = ?
            ORDER BY created_at ASC
        ");
        $msgStmt->execute([$application['id']]);
        $messages = $msgStmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark admin messages as read
        $this->db->prepare("
            UPDATE driver_application_messages
            SET is_read = 1
            WHERE application_id = ? AND sender_type = 'admin' AND is_read = 0
        ")->execute([$application['id']]);

        // Decode proposed interview times
        $proposedTimes = [];
        if (!empty($application['interview_proposed_times'])) {
            $proposedTimes = json_decode($application['interview_proposed_times'], true) ?? [];
        }

        return view('delivery/application-status', [
            'application'   => $application,
            'messages'      => $messages,
            'proposedTimes' => $proposedTimes,
            'pageTitle'     => $this->isFr() ? 'Statut de candidature' : 'Application Status',
        ]);
    }

    /**
     * Lightweight polling endpoint for application-status page
     * Returns new messages since ?since= and current pipeline status
     */
    public function pollApplicationStatus()
    {
        if (!isset($_SESSION['user'])) {
            jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }

        $userId = userId();
        $appId  = (int) get('app_id', 0);
        $since  = trim(get('since', '')); // MySQL datetime string

        if (!$appId) {
            jsonResponse(['error' => 'Missing app_id'], 400);
            return;
        }

        // Verify application belongs to this user
        $stmt = $this->db->prepare("
            SELECT pipeline_stage, interview_selected_time
            FROM driver_applications
            WHERE id = ? AND user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$appId, $userId]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$app) {
            jsonResponse(['error' => 'Not found'], 404);
            return;
        }

        // Fetch only messages newer than $since
        $messages = [];
        if ($since) {
            $msgStmt = $this->db->prepare("
                SELECT sender_type, message, message_fr, created_at
                FROM driver_application_messages
                WHERE application_id = ? AND created_at > ?
                ORDER BY created_at ASC
            ");
            $msgStmt->execute([$appId, $since]);
            $messages = $msgStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Mark newly fetched admin messages as read
            if (!empty($messages)) {
                $this->db->prepare("
                    UPDATE driver_application_messages
                    SET is_read = 1
                    WHERE application_id = ? AND sender_type = 'admin' AND created_at > ? AND is_read = 0
                ")->execute([$appId, $since]);
            }
        }

        jsonResponse([
            'messages'               => $messages,
            'status'                 => $app['pipeline_stage'],
            'interview_selected_time'=> $app['interview_selected_time'],
            'server_time'            => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Applicant sends a message to admin
     */
    public function sendApplicationMessage()
    {
        if (!isset($_SESSION['user']) || !isPost()) {
            redirect(url('login'));
            return;
        }
        verifyCsrfForApi();

        $userId  = userId();
        $appId   = (int) post('application_id', 0);
        $message = trim(post('message', ''));

        if (!$appId || !$message) {
            jsonResponse(['error' => 'Message cannot be empty.'], 422);
            return;
        }

        // Verify this application belongs to this user
        $stmt = $this->db->prepare("SELECT id FROM driver_applications WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->execute([$appId, $userId]);
        if (!$stmt->fetch()) {
            jsonResponse(['error' => 'Unauthorized.'], 403);
            return;
        }

        $this->db->prepare("
            INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message)
            VALUES (?, 'applicant', ?, ?)
        ")->execute([$appId, $userId, htmlspecialchars($message)]);

        $driverName = ($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '');

        // Fetch lead_id once — used for both bell link and activity log
        $leadRow = $this->db->prepare("SELECT lead_id FROM driver_applications WHERE id = ? LIMIT 1");
        $leadRow->execute([$appId]);
        $leadId = $leadRow->fetchColumn();

        // Notify admin (bell) — link to leads/view if linked, else delivery/staff
        try {
            $bellLink = $leadId
                ? "/admin/leads/view?id={$leadId}#messages"
                : "/admin/delivery/staff?tab=applications&app={$appId}";
            \App\Helpers\NotificationHelper::add(
                'driver_message',
                'Driver Applicant Message',
                trim($driverName) . ' replied on their application.',
                ['link' => $bellLink, 'icon' => 'comment']
            );
        } catch (\Exception $e) { /* non-fatal */ }

        // Log to lead activity timeline
        try {
            if ($leadId) {
                $this->db->prepare("
                    INSERT INTO lead_activities (lead_id, activity_type, description, outcome, created_by)
                    VALUES (?, 'email', ?, NULL, ?)
                ")->execute([$leadId, trim($driverName) . ' (applicant) sent a message: ' . mb_strimwidth($message, 0, 100, '…'), $userId]);
            }
        } catch (\Exception $e) { /* non-fatal */ }

        jsonResponse(['success' => true, 'message' => 'Message sent.']);
    }

    /**
     * Applicant selects an interview time slot proposed by admin
     */
    public function selectInterviewTime()
    {
        if (!isset($_SESSION['user']) || !isPost()) {
            redirect(url('login'));
            return;
        }
        verifyCsrfForApi();

        $userId        = userId();
        $appId         = (int) post('application_id', 0);
        $selectedTime  = sanitize(post('selected_time', ''));

        if (!$appId || !$selectedTime) {
            jsonResponse(['error' => 'Invalid selection.'], 422);
            return;
        }

        // Verify ownership
        $stmt = $this->db->prepare("
            SELECT id, interview_proposed_times FROM driver_applications
            WHERE id = ? AND user_id = ? AND pipeline_stage = 'interview_requested'
            LIMIT 1
        ");
        $stmt->execute([$appId, $userId]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$app) {
            jsonResponse(['error' => 'Application not found or not at interview stage.'], 404);
            return;
        }

        // Validate that the selected time is one of the proposed options
        $proposed = json_decode($app['interview_proposed_times'] ?? '[]', true) ?? [];
        if (!in_array($selectedTime, $proposed)) {
            jsonResponse(['error' => 'Selected time is not one of the proposed options.'], 422);
            return;
        }

        // Update application
        $this->db->prepare("
            UPDATE driver_applications
            SET interview_selected_time = ?,
                pipeline_stage = 'interview_scheduled',
                status = 'interview_scheduled',
                updated_at = NOW()
            WHERE id = ?
        ")->execute([$selectedTime, $appId]);

        // Log to lead activity timeline
        try {
            $leadRow2 = $this->db->prepare("SELECT lead_id, first_name, last_name FROM driver_applications WHERE id = ? LIMIT 1");
            $leadRow2->execute([$appId]);
            $appRow2 = $leadRow2->fetch(\PDO::FETCH_ASSOC);
            if ($appRow2 && $appRow2['lead_id']) {
                $this->db->prepare("
                    INSERT INTO lead_activities (lead_id, activity_type, description, outcome, created_by)
                    VALUES (?, 'meeting', ?, NULL, ?)
                ")->execute([
                    $appRow2['lead_id'],
                    "{$appRow2['first_name']} {$appRow2['last_name']} confirmed interview slot: " . date('D M j, Y g:i A', strtotime($selectedTime)),
                    $userId
                ]);
            }
        } catch (\Exception $e) { /* non-fatal */ }

        // Notify admin
        try {
            $user = $_SESSION['user'];
            \App\Helpers\NotificationHelper::add(
                'interview_confirmed',
                'Interview Time Confirmed',
                "{$user['first_name']} {$user['last_name']} confirmed interview: " . date('M j, Y g:ia', strtotime($selectedTime)),
                ['link' => !empty($appRow2['lead_id']) ? "/admin/leads/view?id={$appRow2['lead_id']}" : "/admin/delivery/staff?tab=applications&app={$appId}", 'icon' => 'calendar-check']
            );
        } catch (\Exception $e) { /* non-fatal */ }

        // Send confirmation message + email to driver
        try {
            $ts            = strtotime($selectedTime);
            $formattedTime = date('l, F j, Y \a\t g:i A', $ts);
            $confirmMsg    = "Your interview has been confirmed for {$formattedTime}. We look forward to speaking with you! If you have any questions before then, feel free to message us here.";

            $frMonths    = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
            $frDays      = ['dimanche','lundi','mardi','mercredi','jeudi','vendredi','samedi'];
            $frTime      = $frDays[(int)date('w', $ts)] . ' ' . (int)date('j', $ts) . ' ' . $frMonths[(int)date('n', $ts)] . ' ' . date('Y', $ts) . ' à ' . date('G', $ts) . 'h' . date('i', $ts);
            $confirmMsgFr = "Votre entretien est confirmé pour le {$frTime}. Nous avons hâte de vous parler ! N'hésitez pas à nous écrire si vous avez des questions d'ici là.";

            // Insert system message in thread
            $this->db->prepare("
                INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message, message_fr)
                VALUES (?, 'admin', 0, ?, ?)
            ")->execute([$appId, $confirmMsg, $confirmMsgFr]);

            // Email the driver
            $driverRow = $this->db->prepare("SELECT u.email, u.first_name FROM users u JOIN driver_applications da ON da.user_id = u.id WHERE da.id = ? LIMIT 1");
            $driverRow->execute([$appId]);
            $driver = $driverRow->fetch(\PDO::FETCH_ASSOC);
            if ($driver) {
                \App\Helpers\EmailHelper::sendDriverInterviewConfirmed([
                    'first_name'        => $driver['first_name'],
                    'email'             => $driver['email'],
                    'interview_time'    => $formattedTime,
                    'interview_time_fr' => $frTime,
                ]);
            }
        } catch (\Exception $e) { /* non-fatal */ }

        jsonResponse(['success' => true, 'message' => 'Interview time confirmed!']);
    }

    /**
     * Public delivery tracking page (no auth required)
     */
    public function track() {
        $code = trim(get('code', ''));
        $delivery = null;
        $history = [];

        if ($code) {
            // Search by tracking code or order number
            $stmt = $this->db->prepare("
                SELECT
                    da.*,
                    COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                    COALESCE(u.first_name, bp_u.first_name) as customer_first_name,
                    COALESCE(u.last_name, bp_u.last_name) as customer_last_name,
                    COALESCE(s.name, bp.company_name) as shop_name,
                    d_user.first_name as driver_first_name,
                    d_user.last_name as driver_last_name,
                    d_user.phone as driver_phone
                FROM delivery_assignments da
                LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
                LEFT JOIN users u ON o.user_id = u.id
                LEFT JOIN shops s ON da.shop_id = s.id
                LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
                LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
                LEFT JOIN users bp_u ON bp.user_id = bp_u.id
                LEFT JOIN users d_user ON da.driver_id = d_user.id
                WHERE da.tracking_code = ? OR COALESCE(o.order_number, '') = ?
                LIMIT 1
            ");
            $stmt->execute([$code, $code]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($delivery) {
                // Get status history
                $stmt = $this->db->prepare("
                    SELECT status, notes, created_at
                    FROM delivery_status_history
                    WHERE delivery_id = ?
                    ORDER BY created_at ASC
                ");
                $stmt->execute([$delivery['id']]);
                $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }

        return view('delivery/track', [
            'delivery' => $delivery,
            'history' => $history,
            'trackingCode' => $code,
            'pageTitle' => $this->isFr() ? 'Suivre la livraison' : 'Track Delivery'
        ]);
    }

    public function documents(): void
    {
        if (!$this->isDeliveryDriver()) { redirect(url('login')); return; }

        $driverId    = userId();
        $availability = $this->getDriverAvailability($driverId);

        // Bgcheck data
        $stmt = $this->db->prepare("
            SELECT id, bgcheck_status, bgcheck_doc_type, bgcheck_doc_date,
                   bgcheck_uploaded_at, bgcheck_verified_at, bgcheck_notes, bgcheck_file_path
            FROM driver_applications
            WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
        ");
        $stmt->execute([$driverId]);
        $application = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Compliance docs
        $docs = [];
        if ($application) {
            $rows = $this->db->prepare("SELECT * FROM driver_compliance_docs WHERE application_id = ?");
            $rows->execute([$application['id']]);
            foreach ($rows->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $docs[$row['doc_type']] = $row;
            }
        }

        view('delivery/documents', [
            'pageTitle'   => $this->isFr() ? 'Documents' : 'Documents',
            'currentPage' => 'documents',
            'application' => $application,
            'docs'        => $docs,
            'availability'=> $availability,
        ]);
    }

    public function messages(): void
    {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }

        $userId = userId();

        $appStmt = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $appStmt->execute([$userId]);
        $appId = $appStmt->fetchColumn() ?: null;

        $messages = [];
        if ($appId) {
            $msgStmt = $this->db->prepare("SELECT * FROM driver_application_messages WHERE application_id = ? ORDER BY created_at ASC");
            $msgStmt->execute([$appId]);
            $messages = $msgStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Mark admin messages as read
            $this->db->prepare("
                UPDATE driver_application_messages
                SET is_read = 1
                WHERE application_id = ? AND sender_type = 'admin' AND is_read = 0
            ")->execute([$appId]);
        }

        view('delivery/messages', [
            'pageTitle'   => $this->isFr() ? 'Messages' : 'Messages',
            'currentPage' => 'messages',
            'messages'    => $messages,
            'appId'       => $appId,
        ]);
    }

    public function emails(): void
    {
        if (!$this->isDeliveryDriver()) {
            redirect(url('login'));
            return;
        }

        $fr = $this->isFr();

        try {
            $stmt = $this->db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([(int)userId()]);
            $driverEmail = $stmt->fetchColumn();

            if (!$driverEmail) {
                setFlash('error', $fr ? 'Compte introuvable.' : 'Account not found.');
                redirect(url('delivery/dashboard'));
                return;
            }

            $stmt = $this->db->prepare("
                SELECT id, recipient_email, subject, email_type, status, error_message, created_at
                FROM email_log
                WHERE recipient_email = ?
                ORDER BY created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$driverEmail]);
            $emails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    MIN(created_at) as first_email,
                    MAX(created_at) as last_email
                FROM email_log
                WHERE recipient_email = ?
            ");
            $stmt->execute([$driverEmail]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('delivery/emails', [
                'pageTitle'   => $fr ? 'Mes courriels' : 'My Emails',
                'currentPage' => 'emails',
                'emails'      => $emails,
                'stats'       => $stats,
            ]);
        } catch (\PDOException $e) {
            logger("Delivery emails error: " . $e->getMessage(), 'error');
            setFlash('error', $fr ? 'Erreur lors du chargement des courriels.' : 'Error loading email history.');
            redirect(url('delivery/dashboard'));
        }
    }
}