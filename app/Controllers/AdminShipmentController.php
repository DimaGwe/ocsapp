<?php

namespace App\Controllers;

/**
 * AdminShipmentController - Admin Management for Outbound Shipments
 * Handles viewing, quoting, and managing distribution shipments
 */
class AdminShipmentController
{
    private $db;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
        $this->db = \Database::getConnection();
    }

    /**
     * List all shipments
     */
    public function index(): void
    {
        $status = sanitize($_GET['status'] ?? '');
        $search = sanitize($_GET['search'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            if ($status && in_array($status, ['draft', 'submitted', 'quoted', 'pending_payment', 'paid', 'scheduled', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'])) {
                $whereClause .= " AND s.status = ?";
                $params[] = $status;
            }

            if ($search) {
                $whereClause .= " AND (s.shipment_number LIKE ? OR bp.company_name LIKE ?)";
                $searchTerm = '%' . $search . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM distribution_shipments s
                INNER JOIN business_profiles bp ON s.business_profile_id = bp.id
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get shipments with pagination
            $stmt = $this->db->prepare("
                SELECT
                    s.*,
                    bp.company_name,
                    u.email as contact_email,
                    (SELECT COUNT(*) FROM distribution_shipment_destinations WHERE shipment_id = s.id) as destinations_count,
                    q.total_amount as quote_total
                FROM distribution_shipments s
                INNER JOIN business_profiles bp ON s.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                LEFT JOIN distribution_shipment_quotes q ON s.id = q.shipment_id
                $whereClause
                ORDER BY
                    CASE s.status
                        WHEN 'submitted' THEN 1
                        WHEN 'paid' THEN 2
                        WHEN 'scheduled' THEN 3
                        WHEN 'picked_up' THEN 4
                        WHEN 'in_transit' THEN 5
                        ELSE 6
                    END,
                    s.created_at DESC
                LIMIT :_limit OFFSET :_offset
            ");
            foreach ($params as $i => $val) {
                $stmt->bindValue($i + 1, $val);
            }
            $stmt->bindValue(':_limit', $perPage, \PDO::PARAM_INT);
            $stmt->bindValue(':_offset', $offset, \PDO::PARAM_INT);
            $stmt->execute();
            $shipments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get stats
            $statsStmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) as pending_quote,
                    SUM(CASE WHEN status IN ('paid', 'scheduled', 'picked_up', 'in_transit') THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status IN ('delivered', 'completed') THEN 1 ELSE 0 END) as completed
                FROM distribution_shipments
            ");
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            $totalPages = ceil($total / $perPage);

            view('admin.shipments.index', [
                'shipments' => $shipments,
                'stats' => $stats,
                'currentStatus' => $status,
                'search' => $search,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'total' => $total
            ]);

        } catch (\PDOException $e) {
            error_log('Admin shipments index error: ' . $e->getMessage());
            setFlash('error', 'Error loading shipments.');
            redirect('admin/dashboard');
        }
    }

    /**
     * View single shipment details
     */
    public function view(): void
    {
        $shipmentId = (int)($_GET['id'] ?? 0);

        try {
            // Get shipment with business info
            $stmt = $this->db->prepare("
                SELECT s.*, bp.company_name,
                       u.first_name, u.last_name, u.email, u.phone
                FROM distribution_shipments s
                INNER JOIN business_profiles bp ON s.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE s.id = ?
            ");
            $stmt->execute([$shipmentId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found.');
                redirect('admin/shipments');
                return;
            }

            // Get destinations
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_destinations WHERE shipment_id = ? ORDER BY sequence_order");
            $stmt->execute([$shipmentId]);
            $destinations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get items
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_items WHERE shipment_id = ?");
            $stmt->execute([$shipmentId]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get quote
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_quotes WHERE shipment_id = ? ORDER BY created_at DESC LIMIT 1");
            $stmt->execute([$shipmentId]);
            $quote = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get status history
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_status_history WHERE shipment_id = ? ORDER BY created_at DESC");
            $stmt->execute([$shipmentId]);
            $statusHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get payments
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_payments WHERE shipment_id = ? ORDER BY created_at DESC");
            $stmt->execute([$shipmentId]);
            $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin.shipments.view', [
                'shipment' => $shipment,
                'destinations' => $destinations,
                'items' => $items,
                'quote' => $quote,
                'statusHistory' => $statusHistory,
                'payments' => $payments
            ]);

        } catch (\PDOException $e) {
            error_log('Admin shipment view error: ' . $e->getMessage());
            setFlash('error', 'Error loading shipment.');
            redirect('admin/shipments');
        }
    }

    /**
     * Create quote for shipment
     */
    public function createQuote(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/shipments');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);

        try {
            // Get shipment
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipments WHERE id = ? AND status = 'submitted'");
            $stmt->execute([$shipmentId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found or not in submitted status.');
                redirect('admin/shipments');
                return;
            }

            // Get pricing inputs
            $baseRate = (float)($_POST['base_rate'] ?? 0);
            $perStopRate = (float)($_POST['per_stop_rate'] ?? 0);
            $weightSurcharge = (float)($_POST['weight_surcharge'] ?? 0);
            $distanceSurcharge = (float)($_POST['distance_surcharge'] ?? 0);
            $rushSurcharge = (float)($_POST['rush_surcharge'] ?? 0);
            $taxRate = (float)($_POST['tax_rate'] ?? 14.975);
            $validDays = (int)($_POST['valid_days'] ?? 7);
            $notes = sanitize($_POST['notes'] ?? '');

            // Get stops count
            if ($shipment['is_multi_drop']) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_shipment_destinations WHERE shipment_id = ?");
                $stmt->execute([$shipmentId]);
                $stopsCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
            } else {
                $stopsCount = 1;
            }

            // Calculate totals
            $stopsTotal = $perStopRate * $stopsCount;
            $subtotal = $baseRate + $stopsTotal + $weightSurcharge + $distanceSurcharge + $rushSurcharge;
            $taxAmount = $subtotal * ($taxRate / 100);
            $totalAmount = $subtotal + $taxAmount;

            $validUntil = date('Y-m-d', strtotime("+{$validDays} days"));

            $this->db->beginTransaction();

            // Delete any existing quote
            $this->db->prepare("DELETE FROM distribution_shipment_quotes WHERE shipment_id = ?")->execute([$shipmentId]);

            // Create quote
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipment_quotes
                (shipment_id, base_rate, per_stop_rate, stops_count, stops_total,
                 weight_surcharge, distance_surcharge, rush_surcharge,
                 subtotal, tax_rate, tax_amount, total_amount,
                 valid_until, notes, created_by, created_at)
                VALUES (?, ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $shipmentId,
                $baseRate,
                $perStopRate,
                $stopsCount,
                $stopsTotal,
                $weightSurcharge,
                $distanceSurcharge,
                $rushSurcharge,
                $subtotal,
                $taxRate,
                $taxAmount,
                $totalAmount,
                $validUntil,
                $notes,
                $_SESSION['user']['id'] ?? null
            ]);

            // Update shipment
            $stmt = $this->db->prepare("
                UPDATE distribution_shipments
                SET status = 'quoted', subtotal = ?, tax_amount = ?, total_amount = ?, quoted_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$subtotal, $taxAmount, $totalAmount, $shipmentId]);

            // Log status change
            $this->logStatusChange($shipmentId, 'submitted', 'quoted', 'Quote created: $' . number_format($totalAmount, 2));

            $this->db->commit();

            setFlash('success', 'Quote created successfully. Customer has been notified.');
            redirect('admin/shipments/view?id=' . $shipmentId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin shipment quote error: ' . $e->getMessage());
            setFlash('error', 'Error creating quote.');
            redirect('admin/shipments/view?id=' . $shipmentId);
        }
    }

    /**
     * Update shipment status
     */
    public function updateStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/shipments');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        $scheduledFor = sanitize($_POST['scheduled_for'] ?? '');

        $validStatuses = ['submitted', 'quoted', 'pending_payment', 'paid', 'scheduled', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'];
        if (!in_array($newStatus, $validStatuses)) {
            setFlash('error', 'Invalid status.');
            redirect('admin/shipments/view?id=' . $shipmentId);
            return;
        }

        try {
            // Get current status
            $stmt = $this->db->prepare("SELECT status FROM distribution_shipments WHERE id = ?");
            $stmt->execute([$shipmentId]);
            $current = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$current) {
                setFlash('error', 'Shipment not found.');
                redirect('admin/shipments');
                return;
            }

            $oldStatus = $current['status'];

            // Build update query
            $updateFields = "status = ?, updated_at = NOW()";
            $params = [$newStatus];

            if ($newStatus === 'scheduled' && $scheduledFor) {
                $updateFields .= ", scheduled_for = ?";
                $params[] = $scheduledFor;
            } elseif ($newStatus === 'picked_up') {
                $updateFields .= ", actual_pickup_at = NOW()";
            } elseif ($newStatus === 'completed') {
                $updateFields .= ", completed_at = NOW()";
            } elseif ($newStatus === 'cancelled') {
                $updateFields .= ", cancelled_at = NOW()";
            } elseif ($newStatus === 'paid') {
                $updateFields .= ", payment_status = 'paid', paid_at = NOW()";
            }

            $params[] = $shipmentId;
            $stmt = $this->db->prepare("UPDATE distribution_shipments SET $updateFields WHERE id = ?");
            $stmt->execute($params);

            // Log status change
            $this->logStatusChange($shipmentId, $oldStatus, $newStatus, $notes ?: 'Status updated by admin');

            setFlash('success', 'Status updated to ' . ucwords(str_replace('_', ' ', $newStatus)));
            redirect('admin/shipments/view?id=' . $shipmentId);

        } catch (\PDOException $e) {
            error_log('Admin shipment status update error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/shipments/view?id=' . $shipmentId);
        }
    }

    /**
     * Update destination status (for multi-drop)
     */
    public function updateDestinationStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/shipments');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/shipments');
            return;
        }

        $destinationId = (int)($_POST['destination_id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');

        $validStatuses = ['pending', 'in_transit', 'delivered', 'failed', 'returned'];
        if (!in_array($newStatus, $validStatuses)) {
            setFlash('error', 'Invalid status.');
            redirect('admin/shipments');
            return;
        }

        try {
            // Get destination with shipment info
            $stmt = $this->db->prepare("
                SELECT d.*, s.id as shipment_id
                FROM distribution_shipment_destinations d
                INNER JOIN distribution_shipments s ON d.shipment_id = s.id
                WHERE d.id = ?
            ");
            $stmt->execute([$destinationId]);
            $destination = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$destination) {
                setFlash('error', 'Destination not found.');
                redirect('admin/shipments');
                return;
            }

            $oldStatus = $destination['status'];

            // Update destination
            $updateFields = "status = ?, delivery_notes = ?, updated_at = NOW()";
            $params = [$newStatus, $notes ?: null];

            if ($newStatus === 'delivered') {
                $updateFields .= ", delivered_at = NOW()";
            }

            $params[] = $destinationId;
            $stmt = $this->db->prepare("UPDATE distribution_shipment_destinations SET $updateFields WHERE id = ?");
            $stmt->execute($params);

            // Log in shipment history
            $this->logStatusChange(
                $destination['shipment_id'],
                null,
                $newStatus,
                "Destination #{$destination['sequence_order']} ({$destination['destination_name']}): " . ucwords($newStatus),
                $destinationId
            );

            // Check if all destinations are delivered
            if ($newStatus === 'delivered') {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as pending FROM distribution_shipment_destinations
                    WHERE shipment_id = ? AND status NOT IN ('delivered', 'failed', 'returned')
                ");
                $stmt->execute([$destination['shipment_id']]);
                $pending = $stmt->fetch(\PDO::FETCH_ASSOC)['pending'];

                if ($pending == 0) {
                    // All delivered, update shipment status
                    $this->db->prepare("UPDATE distribution_shipments SET status = 'delivered', updated_at = NOW() WHERE id = ?")->execute([$destination['shipment_id']]);
                    $this->logStatusChange($destination['shipment_id'], 'in_transit', 'delivered', 'All destinations delivered');
                }
            }

            setFlash('success', 'Destination status updated.');
            redirect('admin/shipments/view?id=' . $destination['shipment_id']);

        } catch (\PDOException $e) {
            error_log('Admin destination status update error: ' . $e->getMessage());
            setFlash('error', 'Error updating destination status.');
            redirect('admin/shipments');
        }
    }

    /**
     * Mark payment as received (for bank transfer)
     */
    public function markPaid(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/shipments');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('admin/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $reference = sanitize($_POST['reference'] ?? '');

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shipments
                WHERE id = ? AND status IN ('quoted', 'pending_payment')
            ");
            $stmt->execute([$shipmentId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found or not awaiting payment.');
                redirect('admin/shipments');
                return;
            }

            $this->db->beginTransaction();

            // Update shipment
            $stmt = $this->db->prepare("
                UPDATE distribution_shipments
                SET status = 'paid', payment_status = 'paid', payment_method = 'bank_transfer',
                    payment_reference = ?, paid_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reference ?: null, $shipmentId]);

            // Create payment record
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipment_payments
                (shipment_id, business_profile_id, payment_method, amount, bank_transfer_reference, status, completed_at, created_at)
                VALUES (?, ?, 'bank_transfer', ?, ?, 'completed', NOW(), NOW())
            ");
            $stmt->execute([
                $shipmentId,
                $shipment['business_profile_id'],
                $shipment['total_amount'],
                $reference ?: null
            ]);

            // Log status change
            $this->logStatusChange($shipment['status'], $shipment['status'], 'paid', 'Payment confirmed: ' . ($reference ?: 'Bank Transfer'));

            $this->db->commit();

            setFlash('success', 'Payment marked as received.');
            redirect('admin/shipments/view?id=' . $shipmentId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Admin shipment mark paid error: ' . $e->getMessage());
            setFlash('error', 'Error updating payment status.');
            redirect('admin/shipments/view?id=' . $shipmentId);
        }
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $shipmentId, ?string $fromStatus, string $toStatus, string $notes = '', ?int $destinationId = null): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipment_status_history
                (shipment_id, destination_id, old_status, new_status, changed_by_type, changed_by_id, notes, created_at)
                VALUES (?, ?, ?, ?, 'admin', ?, ?, NOW())
            ");
            $stmt->execute([
                $shipmentId,
                $destinationId,
                $fromStatus,
                $toStatus,
                $_SESSION['user']['id'] ?? null,
                $notes
            ]);
        } catch (\PDOException $e) {
            error_log('Shipment status history log error: ' . $e->getMessage());
        }
    }
}
