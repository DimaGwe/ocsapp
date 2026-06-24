<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * AdminDistributionController - Admin Management for Distribution Requests
 * Handles viewing, quoting, and managing procurement requests
 */
class AdminDistributionController
{
    private $db;

    // Pricing Tiers Configuration (must match DistributionRequestController)
    private const PRICING_TIERS = [
        1 => ['maxAmount' => 500, 'serviceFee' => 0.25, 'freeDeliveryKm' => 5, 'perKmRate' => 1.00],
        2 => ['maxAmount' => 1500, 'serviceFee' => 0.20, 'freeDeliveryKm' => 5, 'perKmRate' => 1.30],
        3 => ['maxAmount' => 3000, 'serviceFee' => 0.15, 'freeDeliveryKm' => 5, 'perKmRate' => 2.00],
        4 => ['maxAmount' => PHP_FLOAT_MAX, 'serviceFee' => 0.12, 'freeDeliveryKm' => 5, 'perKmRate' => 2.20]
    ];

    // Weight-based handling fee: $0.20 per kg
    private const HANDLING_RATE_PER_KG = 0.20;

    // Tax Rates (Quebec)
    private const GST_RATE = 0.05;       // 5%
    private const QST_RATE = 0.09975;    // 9.975%

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Get pricing tier based on items total
     */
    private function getTier(float $itemsTotal): int
    {
        if ($itemsTotal <= 500) return 1;
        if ($itemsTotal <= 1500) return 2;
        if ($itemsTotal <= 3000) return 3;
        return 4;
    }

    /**
     * List all distribution requests
     */
    public function index(): void
    {
        AuthMiddleware::handle('admin');

        $status = sanitize($_GET['status'] ?? '');
        $search = sanitize($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            if ($status && in_array($status, ['draft', 'submitted', 'pending', 'approved', 'quoted', 'pending_payment', 'paid', 'procurement', 'processing', 'in_transit', 'ready', 'delivered', 'completed', 'cancelled', 'expired'])) {
                $whereClause .= " AND dr.status = ?";
                $params[] = $status;
            }

            if ($search) {
                $whereClause .= " AND (dr.request_number LIKE ? OR dr.request_name LIKE ? OR bp.company_name LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get requests with pagination
            $stmt = $this->db->prepare("
                SELECT
                    dr.*,
                    bp.company_name,
                    u.email as contact_email,
                    (SELECT COUNT(*) FROM distribution_request_items WHERE distribution_request_id = dr.id) as catalog_items_count,
                    (SELECT COUNT(*) FROM distribution_shopping_items WHERE distribution_request_id = dr.id) as shopping_items_count,
                    di.invoice_number,
                    di.total_amount as invoice_total
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
                $whereClause
                ORDER BY dr.created_at DESC
                LIMIT :_limit OFFSET :_offset
            ");
            foreach ($params as $i => $val) {
                $stmt->bindValue($i + 1, $val);
            }
            $stmt->bindValue(':_limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':_offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get stats
            $statsStmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status IN ('pending', 'submitted') THEN 1 ELSE 0 END) as pending_review,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as awaiting_payment,
                    SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as needs_supplier_payment,
                    SUM(CASE WHEN status IN ('procurement', 'processing', 'in_transit') THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed
                FROM distribution_requests
            ");
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            $totalPages = ceil($total / $perPage);

            view('admin.distribution.index', [
                'requests' => $requests,
                'stats' => $stats,
                'currentStatus' => $status,
                'search' => $search,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'total' => $total
            ]);

        } catch (\PDOException $e) {
            error_log('Admin distribution index error: ' . $e->getMessage());
            setFlash('error', 'Error loading requests.');
            redirect('admin/dashboard');
        }
    }

    /**
     * View single request details
     */
    public function view(): void
    {
        AuthMiddleware::handle('admin');

        $requestId = (int)($_GET['id'] ?? 0);

        try {
            // Get request with business info
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, bp.delivery_street as bp_street, bp.delivery_city as bp_city,
                       bp.delivery_province as bp_province, bp.delivery_postal_code as bp_postal,
                       u.first_name, u.last_name, u.email, u.phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ?
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found.');
                redirect('admin/distribution');
                return;
            }

            // Get catalog items (product_id references supplier_products)
            $stmt = $this->db->prepare("
                SELECT dri.*,
                       sp.product_name, sp.sku, sp.image, sp.supplier_id,
                       s.name as supplier_name, s.company_name as supplier_company
                FROM distribution_request_items dri
                LEFT JOIN supplier_products sp ON dri.product_id = sp.id
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE dri.distribution_request_id = ?
            ");
            $stmt->execute([$requestId]);
            $catalogItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get shopping items
            $stmt = $this->db->prepare("SELECT * FROM distribution_shopping_items WHERE distribution_request_id = ?");
            $stmt->execute([$requestId]);
            $shoppingItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get invoice
            $stmt = $this->db->prepare("SELECT * FROM distribution_invoices WHERE distribution_request_id = ?");
            $stmt->execute([$requestId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get status history
            $stmt = $this->db->prepare("SELECT * FROM distribution_status_history WHERE distribution_request_id = ? ORDER BY created_at DESC");
            $stmt->execute([$requestId]);
            $statusHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get payments
            $stmt = $this->db->prepare("SELECT * FROM distribution_payments WHERE distribution_request_id = ? ORDER BY created_at DESC");
            $stmt->execute([$requestId]);
            $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate totals
            $catalogTotal = array_reduce($catalogItems, fn($sum, $item) => $sum + ($item['quantity'] * $item['unit_price']), 0);
            $shoppingTotal = array_reduce($shoppingItems, fn($sum, $item) => $sum + ($item['quantity'] * ($item['unit_price'] ?? 0)), 0);

            $shoppingEstimate = 0;
            foreach ($shoppingItems as $item) {
                if (!empty($item['unit_price'])) {
                    $shoppingEstimate += (int)$item['quantity'] * $item['unit_price'];
                } elseif (!empty($item['estimated_price'])) {
                    $shoppingEstimate += (int)$item['quantity'] * $item['estimated_price'];
                }
            }

            // Get tier configuration for display (same as DistributionRequestController)
            $tier = $request['tier'] ?? $this->getTier($catalogTotal);
            $tierConfig = self::PRICING_TIERS[$tier] ?? self::PRICING_TIERS[1];
            $tierVehicles = [
                1 => 'Small Car/Van',
                2 => 'Medium Truck/Van',
                3 => 'Large Truck/Forklift',
                4 => 'Large Truck/Forklift'
            ];

            // Build summary data (use stored values or calculate if not present)
            $summary = [
                'tier' => $tier,
                'tier_vehicle' => $tierVehicles[$tier] ?? 'Standard',
                'items_total' => $request['items_total'] ?? $catalogTotal,
                'service_fee' => $request['service_fee'] ?? ($catalogTotal * $tierConfig['serviceFee']),
                'service_fee_percent' => round($tierConfig['serviceFee'] * 100),
                'handling_fee' => $request['handling_fee'] ?? 0,
                'total_weight_kg' => $request['total_weight_kg'] ?? 0,
                'delivery_distance' => $request['delivery_distance'] ?? 0,
                'delivery_fee' => $request['delivery_fee'] ?? 0,
                'free_delivery_km' => $tierConfig['freeDeliveryKm'],
                'per_km_rate' => $tierConfig['perKmRate'],
                'tip_amount' => $request['tip_amount'] ?? 0,
                'tip_percentage' => $request['tip_percentage'] ?? 0,
                'subtotal' => $request['subtotal'] ?? 0,
                'gst_amount' => $request['gst_amount'] ?? 0,
                'qst_amount' => $request['qst_amount'] ?? 0,
                'tax_amount' => $request['tax_amount'] ?? 0,
                'total_amount' => $request['total_amount'] ?? 0
            ];

            // Get available drivers for assignment (when in delivery-relevant status)
            $availableDrivers = [];
            $deliveryAssignment = null;
            if (in_array($request['status'], ['paid', 'processing', 'ready', 'in_transit'])) {
                $stmt = $this->db->prepare("
                    SELECT u.id, u.first_name, u.last_name, u.phone,
                           dav.status as availability_status, dav.active_deliveries
                    FROM users u
                    JOIN user_roles ur ON u.id = ur.user_id
                    JOIN roles r ON ur.role_id = r.id
                    LEFT JOIN driver_availability dav ON u.id = dav.driver_id
                    JOIN driver_api_tokens dat ON dat.user_id = u.id AND dat.driver_online = 1
                    WHERE r.name = 'delivery' AND u.status = 'active'
                    ORDER BY u.first_name
                ");
                $stmt->execute();
                $availableDrivers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Check for existing delivery assignment
                $stmt = $this->db->prepare("
                    SELECT da.*, d.first_name as driver_first_name, d.last_name as driver_last_name,
                           d.phone as driver_phone
                    FROM delivery_assignments da
                    LEFT JOIN users d ON da.driver_id = d.id
                    WHERE da.distribution_request_id = ? AND da.delivery_type = 'distribution'
                    AND da.status NOT IN ('cancelled', 'failed')
                    ORDER BY da.created_at DESC LIMIT 1
                ");
                $stmt->execute([$requestId]);
                $deliveryAssignment = $stmt->fetch(\PDO::FETCH_ASSOC);
            }

            // Get linked purchase orders auto-created from this distribution request
            $stmt = $this->db->prepare("
                SELECT po.id, po.po_number, po.status, po.total_amount, po.created_at,
                       po.admin_paid_at,
                       po.supplier_accepted_at, po.supplier_declined_at,
                       po.confirmation_deadline, po.ready_by_time, po.escalation_attempt,
                       s.name as supplier_name, s.company_name as supplier_company,
                       s.email as supplier_email, s.id as supplier_id,
                       COUNT(poi.id) as item_count
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id
                WHERE po.distribution_request_id = ?
                GROUP BY po.id
                ORDER BY po.id ASC
            ");
            $stmt->execute([$requestId]);
            $linkedPOs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get delivery assignment timing details
            $daTimingStmt = $this->db->prepare("
                SELECT da.id, da.status, da.created_at as assigned_at,
                       da.picked_up_at, da.delivered_at,
                       da.heading_to_supplier_at, da.en_route_to_customer_at,
                       u.first_name as driver_first, u.last_name as driver_last
                FROM delivery_assignments da
                LEFT JOIN users u ON da.driver_id = u.id
                WHERE da.distribution_request_id = ? AND da.delivery_type = 'distribution'
                ORDER BY da.created_at DESC LIMIT 1
            ");
            $daTimingStmt->execute([$requestId]);
            $daTiming = $daTimingStmt->fetch(\PDO::FETCH_ASSOC) ?: [];

            // Load performance scores if order is completed/delivered
            $performanceScores = [];
            if (in_array($request['status'] ?? '', ['delivered','completed'])) {
                try {
                    $performanceScores = \App\Services\ScoringService::getScoresForRequest($requestId, $this->db);
                } catch (\Throwable $e) { /* non-fatal */ }
            }

            view('admin.distribution.view', [
                'request' => $request,
                'catalogItems' => $catalogItems,
                'shoppingItems' => $shoppingItems,
                'invoice' => $invoice,
                'statusHistory' => $statusHistory,
                'payments' => $payments,
                'catalogTotal' => $catalogTotal,
                'shoppingTotal' => $shoppingTotal,
                'shoppingEstimate' => $shoppingEstimate,
                'summary' => $summary,
                'availableDrivers' => $availableDrivers,
                'deliveryAssignment' => $deliveryAssignment,
                'linkedPOs' => $linkedPOs,
                'daTiming' => $daTiming,
                'performanceScores' => $performanceScores,
            ]);

        } catch (\PDOException $e) {
            error_log('Admin distribution view error: ' . $e->getMessage());
            setFlash('error', 'Error loading request.');
            redirect('admin/distribution');
        }
    }

    /**
     * Update shopping item prices
     */
    public function updatePrices(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $prices = $_POST['prices'] ?? [];

        try {
            foreach ($prices as $itemId => $price) {
                if ($price !== '') {
                    $stmt = $this->db->prepare("
                        UPDATE distribution_shopping_items
                        SET unit_price = ?, updated_at = NOW()
                        WHERE id = ? AND distribution_request_id = ?
                    ");
                    $stmt->execute([(float)$price, (int)$itemId, $requestId]);
                }
            }

            setFlash('success', 'Prices updated successfully.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Admin distribution update prices error: ' . $e->getMessage());
            setFlash('error', 'Error updating prices.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Create invoice/quote for request
     */
    public function createInvoice(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $taxRate = (float)($_POST['tax_rate'] ?? 13);
        $deliveryFee = (float)($_POST['delivery_fee'] ?? 0);
        $dueDate = sanitize($_POST['due_date'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        try {
            // Get request
            $stmt = $this->db->prepare("SELECT * FROM distribution_requests WHERE id = ? AND status IN ('submitted', 'pending')");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or not in a reviewable status.');
                redirect('admin/distribution');
                return;
            }

            // Check if all shopping items have prices
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_shopping_items WHERE distribution_request_id = ? AND unit_price IS NULL");
            $stmt->execute([$requestId]);
            $unpricedCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

            if ($unpricedCount > 0) {
                setFlash('error', 'Please set prices for all shopping list items before creating an invoice.');
                redirect('admin/distribution/view?id=' . $requestId);
                return;
            }

            // Calculate totals
            $stmt = $this->db->prepare("SELECT SUM(quantity * unit_price) as total FROM distribution_request_items WHERE distribution_request_id = ?");
            $stmt->execute([$requestId]);
            $catalogTotal = (float)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            $stmt = $this->db->prepare("SELECT SUM(quantity * unit_price) as total FROM distribution_shopping_items WHERE distribution_request_id = ?");
            $stmt->execute([$requestId]);
            $shoppingTotal = (float)($stmt->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0);

            $subtotal = $catalogTotal + $shoppingTotal;
            $taxAmount = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $taxAmount + $deliveryFee;

            // Get business info for billing
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$request['business_profile_id']]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->db->beginTransaction();

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Create invoice with individual billing fields
            $stmt = $this->db->prepare("
                INSERT INTO distribution_invoices
                (distribution_request_id, business_profile_id, invoice_number,
                 billing_company_name, billing_contact_name, billing_email, billing_phone,
                 billing_street, billing_city, billing_province, billing_postal_code, billing_country,
                 subtotal, tax_rate, tax_amount, delivery_fee, total_amount,
                 invoice_date, due_date, notes, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, ?, 'sent', NOW(), NOW())
            ");
            $stmt->execute([
                $requestId,
                $request['business_profile_id'],
                $invoiceNumber,
                $business['company_name'],
                $business['first_name'] . ' ' . $business['last_name'],
                $business['email'],
                $business['phone'] ?? '',
                $request['delivery_street'],
                $request['delivery_city'],
                $request['delivery_province'],
                $request['delivery_postal_code'],
                'Canada',
                $subtotal,
                $taxRate,
                $taxAmount,
                $deliveryFee,
                $totalAmount,
                $dueDate ?: date('Y-m-d', strtotime('+7 days')),
                $notes
            ]);

            // Update request status to quoted
            $stmt = $this->db->prepare("UPDATE distribution_requests SET status = 'quoted', quoted_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$requestId]);

            // Log status change
            $this->logStatusChange($requestId, 'submitted', 'quoted', 'Invoice created: ' . $invoiceNumber);

            $this->db->commit();

            // Send invoice email to business
            $this->sendInvoiceEmail($request, $business, $invoiceNumber, $subtotal, $taxAmount, $deliveryFee, $totalAmount, $dueDate ?: date('Y-m-d', strtotime('+7 days')));

            setFlash('success', 'Invoice #' . $invoiceNumber . ' created and emailed to ' . $business['email'] . '.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin distribution create invoice error: ' . $e->getMessage());
            setFlash('error', 'Error creating invoice.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Update request status
     */
    public function updateStatus(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        $validStatuses = ['pending', 'approved', 'paid', 'procurement', 'in_transit', 'delivered', 'cancelled', 'expired'];
        if (!in_array($newStatus, $validStatuses)) {
            setFlash('error', 'Invalid status.');
            redirect('admin/distribution/view?id=' . $requestId);
            return;
        }

        try {
            // Get current status
            $stmt = $this->db->prepare("SELECT status FROM distribution_requests WHERE id = ?");
            $stmt->execute([$requestId]);
            $current = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$current) {
                setFlash('error', 'Request not found.');
                redirect('admin/distribution');
                return;
            }

            $oldStatus = $current['status'];

            // Update status
            $updateFields = "status = ?, updated_at = NOW()";
            $params = [$newStatus];

            if ($newStatus === 'completed') {
                $updateFields .= ", completed_at = NOW()";
            } elseif ($newStatus === 'cancelled') {
                $updateFields .= ", cancelled_at = NOW()";
            }

            $params[] = $requestId;
            $stmt = $this->db->prepare("UPDATE distribution_requests SET $updateFields WHERE id = ?");
            $stmt->execute($params);

            // Update invoice status if applicable
            if ($newStatus === 'paid') {
                $stmt = $this->db->prepare("UPDATE distribution_invoices SET status = 'paid', paid_at = NOW() WHERE distribution_request_id = ?");
                $stmt->execute([$requestId]);
            }

            // Log status change
            $this->logStatusChange($requestId, $oldStatus, $newStatus, $notes ?: 'Status updated by admin');

            setFlash('success', 'Status updated to ' . ucwords(str_replace('_', ' ', $newStatus)));
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Admin distribution update status error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $requestId, string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, ?, ?, 'admin', ?, ?, NOW())
            ");
            $stmt->execute([
                $requestId,
                $fromStatus,
                $toStatus,
                $_SESSION['user']['id'] ?? null,
                $notes
            ]);
        } catch (\PDOException $e) {
            error_log('Status history log error: ' . $e->getMessage());
        }
    }

    /**
     * Approve a pending request - generates payment link and notifies customer
     */
    public function approve(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId    = (int)($_POST['distribution_request_id'] ?? 0);
        $notes        = sanitize($_POST['notes'] ?? '');
        $deliveryType = in_array($_POST['delivery_type'] ?? '', ['express', 'scheduled', 'same_day']) ? $_POST['delivery_type'] : 'scheduled';
        if ($deliveryType === 'same_day') {
            $scheduledDate     = date('Y-m-d');
            $scheduledTimeFrom = sanitize($_POST['scheduled_time_from'] ?? '');
            $scheduledTimeTo   = sanitize($_POST['scheduled_time_to'] ?? '');
        } else {
            $scheduledDate     = sanitize($_POST['scheduled_date'] ?? '');
            $scheduledTimeFrom = sanitize($_POST['scheduled_time_from'] ?? '');
            $scheduledTimeTo   = sanitize($_POST['scheduled_time_to'] ?? '');
        }

        try {
            // Get request (support both old 'submitted' and new 'pending' statuses)
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('pending', 'submitted')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or not ready for review.');
                redirect('admin/distribution');
                return;
            }

            $this->db->beginTransaction();

            // Update request status to approved + set delivery type/schedule
            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'approved',
                    approved_at = NOW(),
                    approved_by = ?,
                    delivery_type = ?,
                    scheduled_date = ?,
                    scheduled_time_from = ?,
                    scheduled_time_to = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $_SESSION['user']['id'] ?? null,
                $deliveryType,
                $scheduledDate ?: null,
                $scheduledTimeFrom ?: null,
                $scheduledTimeTo ?: null,
                $requestId,
            ]);

            // Log status change
            $this->logStatusChange($requestId, $request['status'], 'approved', $notes ?: 'Request approved by admin');

            $this->db->commit();

            // Auto-send POs to suppliers (status: sent, with confirmation deadline)
            $poCount = $this->autoCreatePurchaseOrders($requestId, $request['request_number'], $deliveryType);

            // Notify business: request approved, awaiting supplier confirmation
            $this->sendAwaitingSupplierEmail($request);

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                'Distribution Request Approved',
                "Request #{$request['request_number']} ({$request['company_name']}) approved."
                    . ($poCount > 0 ? " {$poCount} PO(s) sent to suppliers for confirmation." : ''),
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'clipboard-check', 'priority' => 'normal']
            );

            $poMsg = $poCount > 0 ? " {$poCount} PO(s) sent to suppliers — awaiting confirmation." : '';
            setFlash('success', 'Request approved.' . $poMsg . ' Business notified.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin distribution approve error: ' . $e->getMessage());
            setFlash('error', 'Error approving request.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Cancel a request with reason
     */
    public function cancel(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $reason = sanitize($_POST['cancellation_reason'] ?? '');

        if (empty($reason)) {
            setFlash('error', 'Please provide a cancellation reason.');
            redirect('admin/distribution/view?id=' . $requestId);
            return;
        }

        try {
            // Get request (support both old and new statuses)
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('pending', 'submitted', 'approved', 'quoted', 'pending_payment', 'awaiting_payment')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or cannot be cancelled at this stage.');
                redirect('admin/distribution');
                return;
            }

            $oldStatus = $request['status'];

            $this->db->beginTransaction();

            // Update request status to cancelled
            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'cancelled',
                    cancelled_at = NOW(),
                    cancellation_reason = ?,
                    cancelled_by = 'ocs',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reason, $requestId]);

            // Log status change
            $this->logStatusChange($requestId, $oldStatus, 'cancelled', 'Cancelled by admin: ' . $reason);

            $this->db->commit();

            // Send cancellation email
            $this->sendCancellationEmail($request, $reason);

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'alert',
                'Distribution Request Cancelled',
                "Request #{$request['request_number']} ({$request['company_name']}) cancelled. Reason: {$reason}",
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'times-circle', 'priority' => 'high']
            );

            setFlash('success', 'Request cancelled. Customer has been notified.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin distribution cancel error: ' . $e->getMessage());
            setFlash('error', 'Error cancelling request.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Resend payment link for approved request
     */
    public function resendPaymentLink(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);

        try {
            // Get request (support both old and new statuses)
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('approved', 'quoted', 'pending_payment', 'awaiting_payment')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or not awaiting payment.');
                redirect('admin/distribution');
                return;
            }

            // Generate new payment link token (48 hour expiry)
            $paymentToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours'));

            // Update payment link
            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET payment_link_token = ?,
                    payment_link_expires_at = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$paymentToken, $expiresAt, $requestId]);

            // Send email
            $this->sendApprovalEmail($request, $paymentToken, $expiresAt);

            // Ensure an invoice record exists (creates with status='sent' if missing)
            self::ensureDistributionInvoice($requestId, $this->db);

            setFlash('success', 'New payment link sent to customer.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Admin distribution resend payment link error: ' . $e->getMessage());
            setFlash('error', 'Error resending payment link.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Mark request as procurement started
     */
    public function startProcurement(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);

        try {
            $stmt = $this->db->prepare("SELECT status FROM distribution_requests WHERE id = ?");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request || $request['status'] !== 'paid') {
                setFlash('error', 'Request must be in paid status to start procurement.');
                redirect('admin/distribution/view?id=' . $requestId);
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'processing',
                    procurement_started_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$requestId]);

            $this->logStatusChange($requestId, 'paid', 'processing', 'Procurement started');

            // Email business: procurement has started
            try {
                $bizStmt = $this->db->prepare("
                    SELECT dr.request_number, bp.company_name, u.email, u.first_name
                    FROM distribution_requests dr
                    JOIN business_profiles bp ON dr.business_profile_id = bp.id
                    JOIN users u ON bp.user_id = u.id
                    WHERE dr.id = ?
                ");
                $bizStmt->execute([$requestId]);
                $bizData = $bizStmt->fetch(\PDO::FETCH_ASSOC);

                if ($bizData && !empty($bizData['email'])) {
                    \App\Helpers\EmailHelper::setNextMeta('distribution_procurement', 'distribution_request', $requestId);
                    \App\Helpers\EmailHelper::sendTemplate(
                        $bizData['email'],
                        'planner-notification',
                        [
                            'user_first_name'      => $bizData['first_name'] ?? $bizData['company_name'],
                            'notification_title'   => 'Your Order is Being Prepared',
                            'notification_message' => "Great news! Your distribution request #{$bizData['request_number']} is now being processed. Our team has started sourcing and preparing your items. We'll notify you when your order is on its way.",
                            'action_url'           => 'https://ocsapp.ca/distribution/requests/show?id=' . $requestId,
                            'current_year'         => date('Y'),
                        ]
                    );
                }
            } catch (\Exception $e) {
                logger('Distribution procurement notification failed: ' . $e->getMessage(), 'warning');
            }

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                'Procurement Started',
                "Distribution request #{$requestId} is now in procurement/processing.",
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'boxes', 'priority' => 'normal']
            );

            setFlash('success', 'Procurement started.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Start procurement error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Admin marks a supplier PO as paid (manual e-transfer/cheque).
     * Creates supplier invoice + payment record, notifies supplier.
     * When all linked POs are paid → auto-advance DR to processing.
     */
    public function markSupplierPaid(): void
    {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $this->jsonError('Invalid request.');
            return;
        }

        $poId          = (int)($_POST['po_id'] ?? 0);
        $requestId     = (int)($_POST['distribution_request_id'] ?? 0);
        $paymentMethod = in_array($_POST['payment_method'] ?? '', ['interac','bank_transfer','cheque','other'])
                         ? $_POST['payment_method'] : 'interac';
        $reference     = trim($_POST['reference'] ?? '');
        $adminId       = $_SESSION['user']['id'] ?? null;

        if (!$poId || !$requestId) {
            $this->jsonError('Missing parameters.');
            return;
        }

        try {
            // Load PO with supplier and DR info
            $stmt = $this->db->prepare("
                SELECT po.id, po.po_number, po.admin_paid_at, po.supplier_id,
                       po.subtotal, po.tax_gst, po.tax_qst, po.shipping_cost, po.total_amount,
                       s.email AS supplier_email, s.company_name AS supplier_company,
                       dr.request_number, dr.status AS dr_status, dr.business_profile_id
                FROM purchase_orders po
                JOIN distribution_requests dr ON dr.id = po.distribution_request_id
                LEFT JOIN suppliers s ON s.id = po.supplier_id
                WHERE po.id = ? AND po.distribution_request_id = ?
                LIMIT 1
            ");
            $stmt->execute([$poId, $requestId]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po) {
                $this->jsonError('PO not found.');
                return;
            }

            if ($po['admin_paid_at']) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'already_paid' => true]);
                exit;
            }

            $this->db->beginTransaction();

            // 1. Mark PO as admin-paid
            $this->db->prepare("
                UPDATE purchase_orders SET admin_paid_at = NOW(), updated_at = NOW() WHERE id = ?
            ")->execute([$poId]);

            // 2. Upsert supplier invoice for this PO
            $existingInv = $this->db->prepare(
                "SELECT id FROM supplier_invoices WHERE po_id = ? LIMIT 1"
            );
            $existingInv->execute([$poId]);
            $invRow = $existingInv->fetch(\PDO::FETCH_ASSOC);

            if ($invRow) {
                // Update existing invoice to paid
                $this->db->prepare("
                    UPDATE supplier_invoices
                    SET amount_paid  = total_amount,
                        balance_due  = 0,
                        status       = 'paid',
                        paid_at      = NOW(),
                        updated_at   = NOW()
                    WHERE id = ?
                ")->execute([$invRow['id']]);
                $invoiceId = (int)$invRow['id'];
            } else {
                // Generate invoice number: INV-YYYYMM-NNNN
                $lastInv = $this->db->query(
                    "SELECT invoice_number FROM supplier_invoices ORDER BY id DESC LIMIT 1"
                )->fetchColumn();
                $prefix = 'INV-' . date('Ym') . '-';
                $seq    = 1;
                if ($lastInv && strpos($lastInv, $prefix) === 0) {
                    $seq = (int)substr($lastInv, strlen($prefix)) + 1;
                }
                $invoiceNumber = $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
                $total = (float)$po['total_amount'];

                $this->db->prepare("
                    INSERT INTO supplier_invoices
                    (invoice_number, supplier_id, po_id, subtotal, tax_gst, tax_qst, shipping,
                     total_amount, amount_paid, balance_due, status,
                     issue_date, due_date, paid_at, created_by, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'paid', CURDATE(), CURDATE(), NOW(), ?, NOW(), NOW())
                ")->execute([
                    $invoiceNumber,
                    $po['supplier_id'],
                    $poId,
                    (float)$po['subtotal'],
                    (float)$po['tax_gst'],
                    (float)$po['tax_qst'],
                    (float)$po['shipping_cost'],
                    $total,
                    $total,
                    $adminId,
                ]);
                $invoiceId = (int)$this->db->lastInsertId();
            }

            // 3. Create supplier_payments record
            $lastPay = $this->db->query(
                "SELECT payment_number FROM supplier_payments ORDER BY id DESC LIMIT 1"
            )->fetchColumn();
            $payPrefix = 'PAY-' . date('Ym') . '-';
            $paySeq    = 1;
            if ($lastPay && strpos($lastPay, $payPrefix) === 0) {
                $paySeq = (int)substr($lastPay, strlen($payPrefix)) + 1;
            }
            $paymentNumber = $payPrefix . str_pad($paySeq, 4, '0', STR_PAD_LEFT);

            $this->db->prepare("
                INSERT INTO supplier_payments
                (payment_number, supplier_id, amount, payment_method, reference_number,
                 payment_date, notes, created_by, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, CURDATE(), ?, ?, NOW(), NOW())
            ")->execute([
                $paymentNumber,
                $po['supplier_id'],
                (float)$po['total_amount'],
                $paymentMethod,
                $reference ?: null,
                'Distribution request #' . $po['request_number'] . ' — PO #' . $po['po_number'],
                $adminId,
            ]);
            $paymentId = (int)$this->db->lastInsertId();

            // 4. Link payment to invoice
            $this->db->prepare("
                INSERT IGNORE INTO supplier_invoice_payments (invoice_id, payment_id, amount_applied, created_at)
                VALUES (?, ?, ?, NOW())
            ")->execute([$invoiceId, $paymentId, (float)$po['total_amount']]);

            $this->db->commit();

            // 5. Supplier bell — notify to prepare goods
            \App\Helpers\NotificationHelper::addSupplierNotification(
                (int)$po['supplier_id'],
                'payment',
                '💳 Payment Received — PO #' . $po['po_number'],
                'Your payment of $' . number_format((float)$po['total_amount'], 2) . ' CAD has been sent for PO #' . $po['po_number'] . '. Please prepare the goods for driver pickup.',
                'supplier/orders/view?id=' . $poId,
                'credit-card',
                '💳 Paiement reçu — BC #' . $po['po_number'],
                'Votre paiement de ' . number_format((float)$po['total_amount'], 2) . ' $ CAD a été envoyé pour BC #' . $po['po_number'] . '. Veuillez préparer les marchandises pour la collecte par le chauffeur.'
            );

            // 6. Check if ALL POs for this DR are now admin-paid → advance to processing
            $unpaid = $this->db->prepare("
                SELECT COUNT(*) FROM purchase_orders
                WHERE distribution_request_id = ?
                  AND status NOT IN ('cancelled')
                  AND admin_paid_at IS NULL
            ");
            $unpaid->execute([$requestId]);
            $unpaidCount = (int)$unpaid->fetchColumn();

            $advancedToProcessing = false;
            if ($unpaidCount === 0 && $po['dr_status'] === 'paid') {
                $this->db->prepare("
                    UPDATE distribution_requests
                    SET status = 'processing', procurement_started_at = NOW(), updated_at = NOW()
                    WHERE id = ?
                ")->execute([$requestId]);

                $this->logStatusChange($requestId, 'paid', 'processing', 'Supplier(s) preparing order and should be ready for pick up shortly.');

                \App\Helpers\NotificationHelper::add(
                    'payment',
                    '✅ All Suppliers Paid — #' . $po['request_number'],
                    'All payments recorded for distribution request #' . $po['request_number'] . '. Suppliers are now preparing goods.',
                    ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'check-circle', 'priority' => 'normal']
                );

                $advancedToProcessing = true;
            }

            header('Content-Type: application/json');
            echo json_encode([
                'success'                => true,
                'payment_number'         => $paymentNumber,
                'advanced_to_processing' => $advancedToProcessing,
                'unpaid_count'           => $unpaidCount,
            ]);
            exit;

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            error_log('Mark supplier paid error: ' . $e->getMessage());
            $this->jsonError('Database error.');
        }
    }

    private function jsonError(string $message, int $code = 400): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }

    /**
     * Mark request as in transit
     */
    public function markInTransit(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);

        try {
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('procurement', 'processing')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request must be in procurement/processing status.');
                redirect('admin/distribution/view?id=' . $requestId);
                return;
            }

            $oldStatus = $request['status'];

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'ready',
                    in_transit_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$requestId]);

            $this->logStatusChange($requestId, $oldStatus, 'ready', 'Items collected, en route to customer');

            // Send in-transit email to customer
            $this->sendInTransitEmail($request);

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'delivery',
                'Distribution Order In Transit',
                "Request #{$request['request_number']} ({$request['company_name']}) is now en route to the customer.",
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'truck', 'priority' => 'normal']
            );

            setFlash('success', 'Status updated to In Transit. Customer notified.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Mark in transit error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Mark request as delivered
     */
    public function markDelivered(): void
    {
        AuthMiddleware::handle('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/distribution');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/distribution');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $confirmedBy = sanitize($_POST['confirmed_by'] ?? '');

        try {
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('in_transit', 'ready')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request must be in transit/ready status.');
                redirect('admin/distribution/view?id=' . $requestId);
                return;
            }

            $oldStatus = $request['status'];

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'completed',
                    delivered_at = NOW(),
                    completed_at = NOW(),
                    delivery_confirmed_by = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$confirmedBy ?: 'Admin', $requestId]);

            // Sync linked delivery_assignment to 'delivered'
            $this->db->prepare("
                UPDATE delivery_assignments
                SET status = 'delivered', delivered_at = NOW(), updated_at = NOW()
                WHERE distribution_request_id = ? AND delivery_type = 'distribution'
                AND status NOT IN ('delivered', 'cancelled')
            ")->execute([$requestId]);

            $this->logStatusChange($requestId, $oldStatus, 'completed', 'Order delivered');

            // Send delivery confirmation email
            $this->sendDeliveryEmail($request);

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                'Distribution Order Delivered',
                "Request #{$request['request_number']} ({$request['company_name']}) delivered."
                    . (!empty($confirmedBy) ? " Received by: {$confirmedBy}." : ''),
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'check-double', 'priority' => 'normal']
            );

            setFlash('success', 'Order marked as delivered. Customer notified.');
            redirect('admin/distribution/view?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Mark delivered error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/distribution/view?id=' . $requestId);
        }
    }

    /**
     * Auto-create draft purchase orders from a distribution request's catalog items.
     * Groups items by supplier and creates one PO per supplier, all as 'draft'.
     * Returns the number of POs created.
     */
    public function autoCreatePurchaseOrders(int $distributionRequestId, string $requestNumber, string $deliveryType = 'scheduled'): int
    {
        try {
            // Fetch catalog items with supplier info and cost price
            $stmt = $this->db->prepare("
                SELECT dri.id, dri.product_id, dri.product_name, dri.product_sku, dri.quantity,
                       dri.unit_price,
                       dri.unit_price as cost_price,
                       sp.supplier_id,
                       sp.unit
                FROM distribution_request_items dri
                INNER JOIN supplier_products sp ON dri.product_id = sp.id
                WHERE dri.distribution_request_id = ?
                  AND sp.is_available = 1
            ");
            $stmt->execute([$distributionRequestId]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($items)) {
                return 0;
            }

            // Group items by supplier_id
            $bySupplier = [];
            foreach ($items as $item) {
                $bySupplier[$item['supplier_id']][] = $item;
            }

            // Get next PO number base
            $lastStmt = $this->db->query("SELECT po_number FROM purchase_orders ORDER BY id DESC LIMIT 1");
            $lastPO = $lastStmt->fetch(\PDO::FETCH_ASSOC);
            $nextNumber = 1;
            if ($lastPO && preg_match('/PO-(\d+)/', $lastPO['po_number'], $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            }

            // Supplier must respond within 10 minutes regardless of delivery type
            $confirmationDeadline = date('Y-m-d H:i:s', strtotime('+10 minutes'));

            $createdCount = 0;
            $today = date('Y-m-d');

            foreach ($bySupplier as $supplierId => $supplierItems) {
                $poNumber = 'PO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                $nextNumber++;

                // Calculate subtotal for this supplier's items
                $subtotal = 0;
                foreach ($supplierItems as $item) {
                    $subtotal += $item['quantity'] * $item['cost_price'];
                }

                // Apply GST (5%) + QST (9.975%)
                $taxGst    = round($subtotal * 0.05, 2);
                $taxQst    = round($subtotal * 0.09975, 2);
                $taxAmount = $taxGst + $taxQst;
                $totalAmount = $subtotal + $taxAmount;

                // Insert PO — status 'sent' immediately (no manual admin step needed)
                $poStmt = $this->db->prepare("
                    INSERT INTO purchase_orders
                        (po_number, supplier_id, order_date, status, subtotal, tax_gst, tax_qst, tax_amount,
                         shipping_cost, total_amount, notes, created_by, distribution_request_id,
                         confirmation_deadline)
                    VALUES (?, ?, ?, 'sent', ?, ?, ?, ?, 0, ?, ?, ?, ?, ?)
                ");
                $poStmt->execute([
                    $poNumber,
                    $supplierId,
                    $today,
                    $subtotal,
                    $taxGst,
                    $taxQst,
                    $taxAmount,
                    $totalAmount,
                    "Auto-created from distribution request {$requestNumber}",
                    $_SESSION['user']['id'] ?? 0,
                    $distributionRequestId,
                    $confirmationDeadline,
                ]);
                $poId = $this->db->lastInsertId();

                // Insert PO items
                $itemStmt = $this->db->prepare("
                    INSERT INTO purchase_order_items
                        (purchase_order_id, product_id, quantity_ordered, unit_cost, total_cost, notes)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                foreach ($supplierItems as $item) {
                    $totalCost = $item['quantity'] * $item['cost_price'];
                    $itemStmt->execute([
                        $poId,
                        $item['product_id'],
                        $item['quantity'],
                        $item['cost_price'],
                        $totalCost,
                        $item['product_sku'] ? "SKU: {$item['product_sku']}" : null,
                    ]);
                }

                // Backwards scheduling: set ready_by_time for scheduled orders
                $readyByTime = $this->calculateReadyByTime($distributionRequestId, $supplierId, $deliveryType);
                if ($readyByTime) {
                    $this->db->prepare("UPDATE purchase_orders SET ready_by_time = ? WHERE id = ?")
                             ->execute([$readyByTime, $poId]);
                }

                // Notify supplier via portal + email
                $this->notifySupplierNewPO($supplierId, $poNumber, $poId, $deliveryType, $confirmationDeadline, $requestNumber, $readyByTime);

                $createdCount++;
                logger("Auto-sent PO #{$poNumber} (supplier #{$supplierId}) for distribution request {$requestNumber}", 'info');
            }

            return $createdCount;

        } catch (\Exception $e) {
            logger("autoCreatePurchaseOrders error for distribution #{$distributionRequestId}: " . $e->getMessage(), 'error');
            return 0; // Non-fatal — approval still succeeds
        }
    }

    /**
     * Notify a supplier about a new PO requiring confirmation.
     */
    private function notifySupplierNewPO(int $supplierId, string $poNumber, int $poId, string $deliveryType, string $deadline, string $requestNumber, ?string $readyByTime = null): void
    {
        try {
            $deadlineFormatted = date('M j, Y \a\t g:i A', strtotime($deadline));
            $isUrgent = in_array($deliveryType, ['express', 'same_day']);
            $urgencyLabel = match($deliveryType) {
                'express'  => '⚡ EXPRESS — 2 hour window',
                'same_day' => '☀️ SAME DAY — 2 hour window',
                default    => '📅 Scheduled — 24 hour window',
            };
            $readyByLine = $readyByTime ? '<p style="margin:8px 0 0;color:#374151;"><strong>📦 Be ready for pickup by:</strong> ' . date('M j, Y \a\t g:i A', strtotime($readyByTime)) . '</p>' : '';

            // Portal bell notification
            $urgencyLabelFr = match($deliveryType) {
                'express'  => '⚡ EXPRESS — fenêtre de 2 heures',
                'same_day' => '☀️ MÊME JOUR — fenêtre de 2 heures',
                default    => '📅 Planifié — fenêtre de 24 heures',
            };
            $deadlineFormattedFr = date('j M Y \à G\hi', strtotime($deadline));
            \App\Helpers\NotificationHelper::addSupplierNotification(
                $supplierId,
                'purchase_order',
                "New PO #{$poNumber} — Confirmation Required",
                "{$urgencyLabel}. Please confirm you can fulfill PO #{$poNumber} by {$deadlineFormatted}.",
                "supplier/orders/view?id={$poId}",
                'clipboard-check',
                "Nouveau BC #{$poNumber} — Confirmation requise",
                "{$urgencyLabelFr}. Veuillez confirmer que vous pouvez exécuter BC #{$poNumber} avant le {$deadlineFormattedFr}."
            );

            // Email notification
            $stmt = $this->db->prepare("SELECT email, contact_name, company_name FROM suppliers WHERE id = ? LIMIT 1");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($supplier && !empty($supplier['email'])) {
                $name    = $supplier['contact_name'] ?? $supplier['company_name'] ?? 'Supplier';
                $poUrl   = url("supplier/orders/view?id={$poId}");
                $subject = $isUrgent
                    ? ($deliveryType === 'same_day'
                        ? "☀️ ACTION REQUIRED — Same-Day PO #{$poNumber} Needs Confirmation Within 2 Hours"
                        : "⚡ ACTION REQUIRED — Express PO #{$poNumber} Needs Confirmation Within 2 Hours")
                    : "ACTION REQUIRED — PO #{$poNumber} Needs Confirmation";

                $accentColor = $isUrgent ? '#dc2626' : '#4f46e5';
                $headerTitle = match($deliveryType) {
                    'express'  => '⚡ Express Order',
                    'same_day' => '☀️ Same-Day Order',
                    default    => 'New Purchase Order',
                };

                $body = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                    <div style='background:{$accentColor};padding:20px;text-align:center;'>
                        <h1 style='color:white;margin:0;'>{$headerTitle}</h1>
                    </div>
                    <div style='padding:30px;background:#f8f9fa;'>
                        <p>Hi {$name},</p>
                        <p>A new purchase order <strong>#{$poNumber}</strong> has been assigned to you from distribution request <strong>#{$requestNumber}</strong>.</p>
                        <div style='background:white;border-radius:8px;padding:20px;margin:20px 0;border-left:4px solid {$accentColor};'>
                            <p style='margin:0;font-weight:bold;'>⏰ Confirmation Deadline: {$deadlineFormatted}</p>
                            <p style='margin:8px 0 0;color:#666;font-size:14px;'>Please confirm or decline before this time. No response = treated as declined.</p>
                            {$readyByLine}
                        </div>
                        <div style='text-align:center;margin:30px 0;'>
                            <a href='{$poUrl}' style='display:inline-block;background:{$accentColor};color:white;padding:14px 36px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;'>
                                Review & Confirm PO
                            </a>
                        </div>
                    </div>
                </div>";

                \App\Helpers\EmailHelper::send($supplier['email'], $subject, $body);
            }
        } catch (\Exception $e) {
            logger("notifySupplierNewPO error: " . $e->getMessage(), 'error');
        }
    }

    // ─── Backwards Scheduling ────────────────────────────────────────────────

    /**
     * Haversine formula — returns distance in km between two lat/lng points.
     */
    private static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Calculate when a supplier must have the order ready for pickup.
     * Works backwards from the delivery window start minus travel time and buffer.
     * Express: not calculated here (supplier sets their own ready_by_time on accept).
     */
    private function calculateReadyByTime(int $distributionRequestId, int $supplierId, string $deliveryType): ?string
    {
        if (!in_array($deliveryType, ['scheduled', 'same_day'])) return null;

        try {
            $stmt = $this->db->prepare("
                SELECT dr.scheduled_date, dr.scheduled_time_from,
                       bp.delivery_latitude, bp.delivery_longitude
                FROM distribution_requests dr
                JOIN business_profiles bp ON dr.business_profile_id = bp.id
                WHERE dr.id = ?
            ");
            $stmt->execute([$distributionRequestId]);
            $dr = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$dr || !$dr['scheduled_date'] || !$dr['scheduled_time_from']) return null;

            $deliveryWindowStart = strtotime($dr['scheduled_date'] . ' ' . $dr['scheduled_time_from']);

            // Get supplier coordinates
            $stmt = $this->db->prepare("SELECT latitude, longitude FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            $travelMinutes = 60; // fallback: 1h if no coordinates
            if ($supplier && $supplier['latitude'] && $dr['delivery_latitude']) {
                $distKm = self::haversineKm(
                    (float)$supplier['latitude'],   (float)$supplier['longitude'],
                    (float)$dr['delivery_latitude'], (float)$dr['delivery_longitude']
                );
                // 35 km/h average urban speed
                $travelMinutes = (int)ceil($distKm / 35 * 60);
                $travelMinutes = max(15, $travelMinutes); // floor at 15 min
            }

            $bufferMinutes = 30; // loading/staging buffer
            $readyByTs = $deliveryWindowStart - (($travelMinutes + $bufferMinutes) * 60);

            // Must be at least 1h from now — never set a deadline in the past
            $minTs = time() + 3600;
            if ($readyByTs < $minTs) $readyByTs = $minTs;

            return date('Y-m-d H:i:s', $readyByTs);

        } catch (\Exception $e) {
            logger("calculateReadyByTime error: " . $e->getMessage(), 'warning');
            return null;
        }
    }

    // ─── Auto Driver Assignment ───────────────────────────────────────────────

    /**
     * Auto-assign the closest available driver when all suppliers for a distribution
     * request have marked their PO as ready_for_pickup.
     * Builds a nearest-neighbour multi-stop route (suppliers → business).
     */
    public static function autoAssignDistributionDriver(int $distRequestId, \PDO $db): void
    {
        try {
            // Safety gate: never assign a driver before payment is confirmed
            $statusCheck = $db->prepare("SELECT status FROM distribution_requests WHERE id = ? LIMIT 1");
            $statusCheck->execute([$distRequestId]);
            $currentStatus = (string)$statusCheck->fetchColumn();
            if (!in_array($currentStatus, ['paid', 'processing'])) {
                logger("autoAssignDistributionDriver blocked for DR #{$distRequestId} — status is '{$currentStatus}'.", 'info');
                return;
            }

            // 1. Get distribution request + business delivery coordinates
            $stmt = $db->prepare("
                SELECT dr.*, bp.company_name, bp.delivery_latitude, bp.delivery_longitude,
                       u.phone as customer_phone
                FROM distribution_requests dr
                JOIN business_profiles bp ON dr.business_profile_id = bp.id
                JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ?
            ");
            $stmt->execute([$distRequestId]);
            $dr = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$dr) return;

            // 2. Get all POs + supplier coordinates for this request
            $stmt = $db->prepare("
                SELECT po.id as po_id, po.po_number,
                       s.id as supplier_id, s.company_name as supplier_name,
                       COALESCE(NULLIF(s.address,''),'') as s_address,
                       s.city as s_city, s.province as s_province, s.postal_code as s_postal,
                       s.latitude as s_lat, s.longitude as s_lng
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.distribution_request_id = ? AND po.status = 'ready_for_pickup'
            ");
            $stmt->execute([$distRequestId]);
            $poRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($poRows)) return;

            // 3. Group POs by supplier (a supplier might have multiple POs)
            $supplierStops = [];
            foreach ($poRows as $row) {
                $sid = $row['supplier_id'];
                if (!isset($supplierStops[$sid])) {
                    $supplierStops[$sid] = [
                        'supplier_id'   => $sid,
                        'supplier_name' => $row['supplier_name'],
                        'address'       => trim("{$row['s_address']}, {$row['s_city']}, {$row['s_province']} {$row['s_postal']}"),
                        'latitude'      => $row['s_lat'] ? (float)$row['s_lat'] : null,
                        'longitude'     => $row['s_lng'] ? (float)$row['s_lng'] : null,
                        'po_ids'        => [],
                        'po_numbers'    => [],
                    ];
                }
                $supplierStops[$sid]['po_ids'][]     = $row['po_id'];
                $supplierStops[$sid]['po_numbers'][] = $row['po_number'];
            }
            $supplierStops = array_values($supplierStops);

            // 4. Find closest available driver to the first supplier stop.
            //    driver_online = 1 is the explicit source of truth — a stationary driver
            //    may not emit GPS pings but is still genuinely available. Don't filter by
            //    GPS staleness; trust the toggle the driver explicitly set.
            $stmt = $db->prepare("
                SELECT da.driver_id, da.current_latitude, da.current_longitude,
                       u.first_name, u.last_name, u.phone,
                       (SELECT fcm_token FROM driver_api_tokens
                        WHERE user_id = da.driver_id AND driver_online = 1
                        ORDER BY updated_at DESC LIMIT 1) AS fcm_token
                FROM driver_availability da
                JOIN users u ON da.driver_id = u.id
                WHERE da.status = 'available'
                  AND u.status = 'active'
                  AND u.role = 'delivery'
                  AND EXISTS (
                      SELECT 1 FROM driver_api_tokens dat
                      WHERE dat.user_id = da.driver_id
                        AND dat.driver_online = 1
                  )
            ");
            $stmt->execute();
            $availableDrivers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($availableDrivers)) {
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    '⚠️ No Available Driver — Distribution Request Ready',
                    "All suppliers for distribution request #{$dr['request_number']} are ready for pickup but no drivers are available. Manual assignment required.",
                    ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );
                return;
            }

            // Use first supplier's location as the reference point for driver proximity
            $refLat = $supplierStops[0]['latitude'];
            $refLng = $supplierStops[0]['longitude'];

            $bestDriver  = null;
            $bestDistKm  = PHP_FLOAT_MAX;
            foreach ($availableDrivers as $driver) {
                if (!$driver['current_latitude'] || !$driver['current_longitude']) {
                    // No location — still eligible but low priority
                    if ($bestDistKm === PHP_FLOAT_MAX) $bestDriver = $driver;
                    continue;
                }
                if ($refLat && $refLng) {
                    $d = self::haversineKm($refLat, $refLng, (float)$driver['current_latitude'], (float)$driver['current_longitude']);
                    if ($d < $bestDistKm) { $bestDistKm = $d; $bestDriver = $driver; }
                } else {
                    if (!$bestDriver) $bestDriver = $driver;
                }
            }

            if (!$bestDriver) {
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    '⚠️ Driver Assignment Failed',
                    "Could not determine closest driver for distribution request #{$dr['request_number']}. Please assign manually.",
                    ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );
                return;
            }

            $driverId = (int)$bestDriver['driver_id'];

            // 5. Build nearest-neighbour ordered pickup stop list
            $orderedStops   = [];
            $currentLat     = $refLat ?? $bestDriver['current_latitude'];
            $currentLng     = $refLng ?? $bestDriver['current_longitude'];
            $remaining      = $supplierStops;

            while (!empty($remaining)) {
                $nearestIdx  = 0;
                $nearestDist = PHP_FLOAT_MAX;
                foreach ($remaining as $idx => $stop) {
                    if ($stop['latitude'] && $currentLat) {
                        $d = self::haversineKm((float)$currentLat, (float)$currentLng, $stop['latitude'], $stop['longitude']);
                    } else {
                        $d = PHP_FLOAT_MAX - $idx; // keep original order if no coords
                    }
                    if ($d < $nearestDist) { $nearestDist = $d; $nearestIdx = $idx; }
                }
                $next = $remaining[$nearestIdx];
                $orderedStops[] = $next;
                $currentLat = $next['latitude'];
                $currentLng = $next['longitude'];
                array_splice($remaining, $nearestIdx, 1);
            }

            // 6. Build pickup_stops JSON for delivery_assignment
            $stopsJson = [];
            foreach ($orderedStops as $i => $stop) {
                $stopsJson[] = [
                    'stop_order'    => $i + 1,
                    'type'          => 'pickup',
                    'supplier_id'   => $stop['supplier_id'],
                    'supplier_name' => $stop['supplier_name'],
                    'address'       => $stop['address'],
                    'po_ids'        => $stop['po_ids'],
                    'po_numbers'    => $stop['po_numbers'],
                    'latitude'      => $stop['latitude'],
                    'longitude'     => $stop['longitude'],
                ];
            }
            // Final stop = business delivery
            $stopsJson[] = [
                'stop_order'    => count($orderedStops) + 1,
                'type'          => 'delivery',
                'business_name' => $dr['company_name'],
                'address'       => trim("{$dr['delivery_street']}, {$dr['delivery_city']}, {$dr['delivery_province']} {$dr['delivery_postal_code']}"),
                'latitude'      => $dr['delivery_latitude'] ?: null,
                'longitude'     => $dr['delivery_longitude'] ?: null,
            ];

            $pickupAddressSummary = implode(' → ', array_column(
                array_filter($stopsJson, fn($s) => $s['type'] === 'pickup'), 'supplier_name'
            ));

            $deliveryAddress = trim("{$dr['delivery_street']}, {$dr['delivery_city']}, {$dr['delivery_province']} {$dr['delivery_postal_code']}");

            // 7. Create or update delivery_assignment
            $stmt = $db->prepare("
                SELECT id FROM delivery_assignments
                WHERE distribution_request_id = ? AND delivery_type = 'distribution'
                AND status NOT IN ('cancelled','failed')
            ");
            $stmt->execute([$distRequestId]);
            $existing = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existing) {
                $db->prepare("
                    UPDATE delivery_assignments SET
                        driver_id = ?, status = 'assigned', assigned_at = NOW(),
                        pickup_stops = ?, total_stops = ?, pickup_address = ?, updated_at = NOW()
                    WHERE id = ?
                ")->execute([
                    $driverId,
                    json_encode($stopsJson),
                    count($orderedStops),
                    $pickupAddressSummary,
                    $existing['id'],
                ]);
                $deliveryId = $existing['id'];
            } else {
                $db->prepare("
                    INSERT INTO delivery_assignments
                    (delivery_type, distribution_request_id, driver_id, status,
                     delivery_fee, delivery_address, customer_phone, pickup_address,
                     pickup_stops, total_stops, assigned_at)
                    VALUES ('distribution', ?, ?, 'assigned', ?, ?, ?, ?, ?, ?, NOW())
                ")->execute([
                    $distRequestId, $driverId,
                    $dr['delivery_fee'] ?? 0,
                    $deliveryAddress,
                    $dr['customer_phone'] ?? '',
                    $pickupAddressSummary,
                    json_encode($stopsJson),
                    count($orderedStops),
                ]);
                $deliveryId = $db->lastInsertId();
            }

            // 8. Stamp all POs with driver info
            $allPoIds = array_merge(...array_column($orderedStops, 'po_ids'));
            if ($allPoIds) {
                $placeholders = implode(',', array_fill(0, count($allPoIds), '?'));
                $params = array_merge([$driverId], $allPoIds);
                $db->prepare("
                    UPDATE purchase_orders SET
                        assigned_driver_id = ?, driver_assigned_at = NOW(), updated_at = NOW()
                    WHERE id IN ({$placeholders})
                ")->execute($params);
            }

            // 9. Update distribution request → 'ready'
            $db->prepare("
                UPDATE distribution_requests SET status = 'ready', updated_at = NOW() WHERE id = ?
            ")->execute([$distRequestId]);

            // 10. Notify driver via portal + delivery_notifications table
            $driverName = trim(($bestDriver['first_name'] ?? '') . ' ' . ($bestDriver['last_name'] ?? ''));
            $stopCount  = count($orderedStops);
            $db->prepare("
                INSERT INTO driver_delivery_notifications
                (driver_id, message, type, sent_by, created_at)
                VALUES (?, ?, 'urgent', 0, NOW())
            ")->execute([
                $driverId,
                "New delivery assignment: {$stopCount} pickup stop(s) → {$dr['company_name']}. Request #{$dr['request_number']}.",
            ]);

            // 11. Admin bell — driver assigned
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "Driver Auto-Assigned — #{$dr['request_number']}",
                "Driver {$driverName} auto-assigned for distribution request #{$dr['request_number']}. {$stopCount} pickup stop(s)." . ($bestDistKm < PHP_FLOAT_MAX ? sprintf(' Distance to first stop: %.1f km.', $bestDistKm) : ''),
                ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'truck', 'priority' => 'normal']
            );

            // Business bell — driver assigned and heading to pick up their goods
            \App\Helpers\NotificationHelper::addBusinessNotification(
                (int)$dr['business_profile_id'],
                'delivery',
                '🚗 Driver Assigned — #' . $dr['request_number'],
                "Driver {$driverName} has been assigned and will collect your items from {$stopCount} supplier(s). You will be notified once your order is on its way.",
                'distribution/requests/show?id=' . $distRequestId
            );

            // Supplier bell — notify each supplier that the driver is coming
            try {
                $supStmt = $db->prepare("
                    SELECT po.id, po.po_number, po.supplier_id
                    FROM purchase_orders po
                    WHERE po.distribution_request_id = ?
                      AND po.status NOT IN ('cancelled','completed')
                ");
                $supStmt->execute([$distRequestId]);
                foreach ($supStmt->fetchAll(\PDO::FETCH_ASSOC) as $supPo) {
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        (int)$supPo['supplier_id'],
                        'delivery',
                        '🚗 Driver Coming — PO #' . $supPo['po_number'],
                        "Driver {$driverName} has been assigned and is on the way to collect PO #{$supPo['po_number']}.",
                        'supplier/orders/view?id=' . $supPo['id'],
                        'truck',
                        '🚗 Chauffeur en route — BC #' . $supPo['po_number'],
                        "Le chauffeur {$driverName} a été assigné et est en route pour collecter BC #{$supPo['po_number']}."
                    );
                }
            } catch (\Exception $e) { /* non-fatal */ }

            logger("Auto-assigned driver #{$driverId} ({$driverName}) to distribution request #{$dr['request_number']} ({$stopCount} stops)", 'info');

        } catch (\Exception $e) {
            logger("autoAssignDistributionDriver error: " . $e->getMessage(), 'error');
            // Fallback: alert admin to assign manually
            \App\Helpers\NotificationHelper::add(
                'new_order',
                '⚠️ Auto Driver Assignment Failed',
                "All suppliers ready for distribution request, but auto-assignment failed: " . $e->getMessage() . ". Please assign manually.",
                ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );
        }
    }

    /**
     * Notify business that their request was approved and suppliers are being confirmed.
     * Payment link is NOT sent here — it's sent when all suppliers confirm.
     */
    public function sendAwaitingSupplierEmail(array $request): void
    {
        try {
            $subject = "Your OCS Distribution Request #{$request['request_number']} Has Been Sent to Suppliers";
            $body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#4f46e5;padding:20px;text-align:center;'>
                    <h1 style='color:white;margin:0;'>Request Submitted!</h1>
                </div>
                <div style='padding:30px;background:#f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>Your distribution request <strong>#{$request['request_number']}</strong> has been received and automatically sent to our suppliers for confirmation.</p>
                    <div style='background:white;border-radius:8px;padding:20px;margin:20px 0;border-left:4px solid #4f46e5;'>
                        <p style='margin:0;font-weight:bold;'>⏳ Awaiting Supplier Confirmation</p>
                        <p style='margin:8px 0 0;color:#666;font-size:14px;'>Suppliers are confirming they can fulfill your order. You'll receive a payment link as soon as all suppliers confirm availability.</p>
                    </div>
                    <p style='color:#666;font-size:14px;'>This typically takes a few hours. We'll notify you immediately once confirmed.</p>
                    <hr style='border:none;border-top:1px solid #ddd;margin:30px 0;'>
                    <p style='color:#888;font-size:12px;'>OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a></p>
                </div>
            </div>";
            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('sendAwaitingSupplierEmail error: ' . $e->getMessage());
        }
    }

    /**
     * Create a distribution_invoices record (status='sent') if one does not already exist.
     * Called whenever a payment link is sent/resent so the business can see an invoice before paying.
     * After payment, completePayment() will update it to 'paid'.
     *
     * @param int        $requestId  distribution_requests.id
     * @param \PDO       $db
     * @return string|null  invoice number if created, null if already existed or error
     */
    public static function ensureDistributionInvoice(int $requestId, \PDO $db): ?string
    {
        try {
            // Already exists? Leave it alone (may have been manually created with custom amounts).
            $check = $db->prepare("SELECT id FROM distribution_invoices WHERE distribution_request_id = ? LIMIT 1");
            $check->execute([$requestId]);
            if ($check->fetch()) {
                return null;
            }

            // Fetch request + business billing details
            $stmt = $db->prepare("
                SELECT dr.*,
                       bp.company_name,
                       COALESCE(bp.billing_street,      bp.delivery_street)      AS bill_street,
                       COALESCE(bp.billing_city,        bp.delivery_city)        AS bill_city,
                       COALESCE(bp.billing_province,    bp.delivery_province)    AS bill_province,
                       COALESCE(bp.billing_postal_code, bp.delivery_postal_code) AS bill_postal,
                       u.email, u.first_name, u.last_name, u.phone AS u_phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users            u  ON bp.user_id = u.id
                WHERE dr.id = ?
                LIMIT 1
            ");
            $stmt->execute([$requestId]);
            $r = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$r) return null;

            $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            $subtotal  = (float)($r['subtotal']      ?? 0);
            $taxTotal  = (float)($r['gst_amount']    ?? 0) + (float)($r['qst_amount'] ?? 0);
            $taxRate   = $subtotal > 0 ? round($taxTotal / $subtotal * 100, 2) : 14.98;
            $delivery  = (float)($r['delivery_fee']  ?? 0);
            $total     = (float)($r['total_amount']  ?? 0);
            // Fallback: if subtotal is 0 but total exists, derive
            if ($subtotal === 0.0 && $total > 0) {
                $subtotal = $total - $taxTotal - $delivery;
            }
            $dueDate = date('Y-m-d', strtotime('+7 days'));

            $db->prepare("
                INSERT INTO distribution_invoices
                (distribution_request_id, business_profile_id, invoice_number,
                 billing_company_name, billing_contact_name, billing_email, billing_phone,
                 billing_street, billing_city, billing_province, billing_postal_code, billing_country,
                 subtotal, tax_rate, tax_amount, delivery_fee, total_amount,
                 invoice_date, due_date, status, sent_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada',
                        ?, ?, ?, ?, ?, CURDATE(), ?, 'sent', NOW(), NOW(), NOW())
            ")->execute([
                $requestId,
                $r['business_profile_id'],
                $invoiceNumber,
                $r['company_name'],
                trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')),
                $r['email'],
                $r['u_phone'] ?? '',
                $r['bill_street'] ?? '',
                $r['bill_city']   ?? '',
                $r['bill_province'] ?? '',
                $r['bill_postal']   ?? '',
                $subtotal, $taxRate, $taxTotal, $delivery, $total,
                $dueDate,
            ]);

            return $invoiceNumber;
        } catch (\Exception $e) {
            error_log('ensureDistributionInvoice error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Send payment link email to business (called when all suppliers confirm).
     * Public static so SupplierProductController can call it.
     */
    public static function sendPaymentLinkEmail(array $request, string $paymentToken, string $expiresAt): void
    {
        try {
            $paymentUrl       = url('distribution/pay?token=' . $paymentToken);
            $expiryFormatted  = date('F j, Y \a\t g:i A', strtotime($expiresAt));
            $subject = "All Suppliers Confirmed — Complete Payment for Request #{$request['request_number']}";
            $body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#00b207;padding:20px;text-align:center;'>
                    <h1 style='color:white;margin:0;'>Suppliers Confirmed!</h1>
                </div>
                <div style='padding:30px;background:#f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>All suppliers have confirmed availability for your distribution request <strong>#{$request['request_number']}</strong>. Your order is ready to proceed — please complete your payment to begin fulfillment.</p>
                    <div style='background:white;border-radius:8px;padding:20px;margin:20px 0;'>
                        <p><strong>Total Amount:</strong> \$" . number_format($request['total_amount'], 2) . " CAD</p>
                    </div>
                    <div style='text-align:center;margin:30px 0;'>
                        <a href='{$paymentUrl}' style='display:inline-block;background:#00b207;color:white;padding:15px 40px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:16px;'>
                            Complete Payment
                        </a>
                    </div>
                    <p style='color:#666;font-size:14px;'><strong>Important:</strong> This payment link expires on {$expiryFormatted}.</p>
                    <hr style='border:none;border-top:1px solid #ddd;margin:30px 0;'>
                    <p style='color:#888;font-size:12px;'>OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a></p>
                </div>
            </div>";
            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('sendPaymentLinkEmail error: ' . $e->getMessage());
        }
    }

    /**
     * @deprecated Use sendPaymentLinkEmail() instead.
     */
    private function sendApprovalEmail(array $request, string $paymentToken, string $expiresAt): void
    {
        try {
            $paymentUrl = url('distribution/pay?token=' . $paymentToken);
            $expiryFormatted = date('F j, Y \a\t g:i A', strtotime($expiresAt));

            $subject = "Your OCS Distribution Request #{$request['request_number']} Has Been Approved";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #00b207; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Request Approved!</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>Great news! Your distribution request <strong>#{$request['request_number']}</strong> has been approved and is ready for payment.</p>

                    <div style='background: white; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #333;'>Order Summary</h3>
                        <p><strong>Request:</strong> {$request['request_name']}</p>
                        <p><strong>Total Amount:</strong> $" . number_format($request['total_amount'], 2) . " CAD</p>
                    </div>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$paymentUrl}' style='display: inline-block; background: #00b207; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;'>
                            Complete Payment
                        </a>
                    </div>

                    <p style='color: #666; font-size: 14px;'>
                        <strong>Important:</strong> This payment link expires on {$expiryFormatted}.<br>
                        Once payment is received, we'll begin procuring your items immediately.
                    </p>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        If you have any questions, please contact our support team.<br>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Send approval email error: ' . $e->getMessage());
        }
    }

    /**
     * Send cancellation email
     */
    private function sendCancellationEmail(array $request, string $reason): void
    {
        try {
            $subject = "Your OCS Distribution Request #{$request['request_number']} Has Been Cancelled";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #dc2626; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Request Cancelled</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>We regret to inform you that your distribution request <strong>#{$request['request_number']}</strong> has been cancelled.</p>

                    <div style='background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px 20px; margin: 20px 0; border-radius: 0 8px 8px 0;'>
                        <strong>Reason:</strong><br>
                        {$reason}
                    </div>

                    <p>If you believe this was done in error or would like to discuss alternatives, please don't hesitate to contact us.</p>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Send cancellation email error: ' . $e->getMessage());
        }
    }

    /**
     * Send in-transit email
     */
    private function sendInTransitEmail(array $request): void
    {
        try {
            $subject = "Your OCS Order #{$request['request_number']} is On Its Way!";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #3b82f6; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>🚚 Your Order is On Its Way!</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>Great news! Your order <strong>#{$request['request_number']}</strong> has been picked up and is now en route to your delivery address.</p>

                    <div style='background: white; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #333;'>Delivery Address</h3>
                        <p style='margin: 0;'>
                            {$request['delivery_street']}<br>
                            {$request['delivery_city']}, {$request['delivery_province']}<br>
                            {$request['delivery_postal_code']}
                        </p>
                    </div>

                    <p>You will receive another notification once your order has been delivered.</p>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Send in-transit email error: ' . $e->getMessage());
        }
    }

    /**
     * Send invoice email to business when admin creates invoice
     */
    private function sendInvoiceEmail(array $request, array $business, string $invoiceNumber, float $subtotal, float $taxAmount, float $deliveryFee, float $totalAmount, string $dueDate): void
    {
        try {
            $subject = "Invoice #{$invoiceNumber} for Your OCS Distribution Request #{$request['request_number']}";

            $formattedDue = date('F j, Y', strtotime($dueDate));
            $subtotalFmt  = number_format($subtotal, 2);
            $taxFmt       = number_format($taxAmount, 2);
            $deliveryFmt  = number_format($deliveryFee, 2);
            $totalFmt     = number_format($totalAmount, 2);

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #00b207; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Invoice Ready</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$business['first_name']},</p>
                    <p>Your distribution request <strong>#{$request['request_number']}</strong> has been reviewed and your invoice is ready.</p>

                    <div style='background: white; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #333;'>Invoice #{$invoiceNumber}</h3>
                        <table style='width: 100%; border-collapse: collapse;'>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Subtotal</td>
                                <td style='padding: 8px 0; text-align: right;'>\${$subtotalFmt} CAD</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Taxes</td>
                                <td style='padding: 8px 0; text-align: right;'>\${$taxFmt} CAD</td>
                            </tr>
                            <tr>
                                <td style='padding: 8px 0; color: #666;'>Delivery Fee</td>
                                <td style='padding: 8px 0; text-align: right;'>\${$deliveryFmt} CAD</td>
                            </tr>
                            <tr style='border-top: 2px solid #e5e7eb;'>
                                <td style='padding: 12px 0; font-weight: bold; font-size: 16px;'>Total</td>
                                <td style='padding: 12px 0; text-align: right; font-weight: bold; font-size: 16px; color: #00b207;'>\${$totalFmt} CAD</td>
                            </tr>
                        </table>
                        <p style='margin: 10px 0 0; color: #dc2626; font-size: 14px;'><strong>Due:</strong> {$formattedDue}</p>
                    </div>

                    <p>Log in to your account to review the full quote and complete payment once you're ready to proceed.</p>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . url('distribution/requests/show?id=' . $request['id']) . "' style='display: inline-block; background: #00b207; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;'>
                            View Invoice &amp; Pay
                        </a>
                    </div>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($business['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Send invoice email error: ' . $e->getMessage());
        }
    }

    /**
     * Send delivery confirmation email
     */
    private function sendDeliveryEmail(array $request): void
    {
        try {
            $subject = "Your OCS Order #{$request['request_number']} Has Been Delivered!";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #00b207; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>📦 Order Delivered!</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>Your order <strong>#{$request['request_number']}</strong> has been successfully delivered.</p>

                    <div style='background: #d1fae5; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;'>
                        <p style='font-size: 18px; color: #065f46; margin: 0;'>✓ Delivery Complete</p>
                        <p style='color: #666; margin: 10px 0 0;'>" . date('F j, Y \a\t g:i A') . "</p>
                    </div>

                    <p>Thank you for choosing OCS Distribution. We hope everything meets your expectations!</p>

                    <p>If you have any questions or concerns about your delivery, please contact our support team.</p>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);
        } catch (\Exception $e) {
            error_log('Send delivery email error: ' . $e->getMessage());
        }
    }
}
