<?php

namespace App\Controllers;

/**
 * DistributionShipmentController - Business-facing Shipment Management
 * Handles creation, editing, and tracking of outbound shipments
 */
class DistributionShipmentController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function getBusinessData(): array
    {
        $id = $_SESSION['business']['id'] ?? null;
        if (!$id) return $_SESSION['business'] ?? [];
        try {
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ? LIMIT 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ($_SESSION['business'] ?? []);
        } catch (\Exception $e) {
            return $_SESSION['business'] ?? [];
        }
    }

    /**
     * List all shipments for the business
     */
    public function index(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $status = sanitize($_GET['status'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;

        try {
            $whereClause = "WHERE business_profile_id = ?";
            $params = [$businessId];

            if ($status && in_array($status, ['draft', 'submitted', 'quoted', 'pending_payment', 'paid', 'scheduled', 'picked_up', 'in_transit', 'delivered', 'completed', 'cancelled'])) {
                $whereClause .= " AND status = ?";
                $params[] = $status;
            }

            // Get total count
            $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM distribution_shipments $whereClause");
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get shipments
            $stmt = $this->db->prepare("
                SELECT s.*,
                    (SELECT COUNT(*) FROM distribution_shipment_destinations WHERE shipment_id = s.id) as destinations_count
                FROM distribution_shipments s
                $whereClause
                ORDER BY s.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute(array_merge($params, [$perPage, $offset]));
            $shipments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get stats
            $statsStmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as drafts,
                    SUM(CASE WHEN status IN ('submitted', 'quoted', 'pending_payment') THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status IN ('paid', 'scheduled', 'picked_up', 'in_transit') THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status IN ('delivered', 'completed') THEN 1 ELSE 0 END) as completed
                FROM distribution_shipments
                WHERE business_profile_id = ?
            ");
            $statsStmt->execute([$businessId]);
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            $totalPages = ceil($total / $perPage);

            view('distribution.shipments.index', [
                'shipments'      => $shipments,
                'stats'          => $stats,
                'currentStatus'  => $status,
                'currentPage'    => $page,
                'totalPages'     => $totalPages,
                'total'          => $total,
                'business'       => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution shipments index error: ' . $e->getMessage());
            setFlash('error', 'Error loading shipments.');
            redirect('distribution/dashboard');
        }
    }

    /**
     * Show create shipment form
     */
    public function create(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        // Get business profile for default pickup address
        $stmt = $this->db->prepare("
            SELECT bp.*, u.first_name, u.last_name, u.phone
            FROM business_profiles bp
            INNER JOIN users u ON bp.user_id = u.id
            WHERE bp.id = ?
        ");
        $stmt->execute([$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        view('distribution.shipments.create', [
            'business' => $business,
            'errors' => $_SESSION['shipment_errors'] ?? [],
            'old' => $_SESSION['shipment_old'] ?? []
        ]);

        unset($_SESSION['shipment_errors'], $_SESSION['shipment_old']);
    }

    /**
     * Store new shipment
     */
    public function store(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/shipments');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/shipments/create');
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $shipmentType = sanitize($_POST['shipment_type'] ?? 'parcel');
        $isMultiDrop = $shipmentType === 'multi_drop' || isset($_POST['is_multi_drop']);

        // Collect data
        $data = [
            'shipment_type' => $shipmentType,
            'is_multi_drop' => $isMultiDrop,
            // Pickup info
            'pickup_street' => sanitize($_POST['pickup_street'] ?? ''),
            'pickup_city' => sanitize($_POST['pickup_city'] ?? ''),
            'pickup_province' => sanitize($_POST['pickup_province'] ?? ''),
            'pickup_postal_code' => strtoupper(sanitize($_POST['pickup_postal_code'] ?? '')),
            'pickup_contact_name' => sanitize($_POST['pickup_contact_name'] ?? ''),
            'pickup_contact_phone' => sanitize($_POST['pickup_contact_phone'] ?? ''),
            'pickup_instructions' => sanitize($_POST['pickup_instructions'] ?? ''),
            'requested_pickup_date' => sanitize($_POST['requested_pickup_date'] ?? ''),
            'requested_pickup_time_start' => sanitize($_POST['requested_pickup_time_start'] ?? ''),
            'requested_pickup_time_end' => sanitize($_POST['requested_pickup_time_end'] ?? ''),
            // Package info
            'total_packages' => (int)($_POST['total_packages'] ?? 1),
            'total_weight_kg' => !empty($_POST['total_weight_kg']) ? (float)$_POST['total_weight_kg'] : null,
            'package_description' => sanitize($_POST['package_description'] ?? ''),
            // Notes
            'business_notes' => sanitize($_POST['business_notes'] ?? ''),
        ];

        // Single destination (if not multi-drop)
        if (!$isMultiDrop) {
            $data['destination_street'] = sanitize($_POST['destination_street'] ?? '');
            $data['destination_city'] = sanitize($_POST['destination_city'] ?? '');
            $data['destination_province'] = sanitize($_POST['destination_province'] ?? '');
            $data['destination_postal_code'] = strtoupper(sanitize($_POST['destination_postal_code'] ?? ''));
            $data['destination_contact_name'] = sanitize($_POST['destination_contact_name'] ?? '');
            $data['destination_contact_phone'] = sanitize($_POST['destination_contact_phone'] ?? '');
            $data['destination_instructions'] = sanitize($_POST['destination_instructions'] ?? '');
        }

        // Multi-drop destinations
        $destinations = [];
        if ($isMultiDrop && isset($_POST['destinations'])) {
            foreach ($_POST['destinations'] as $index => $dest) {
                $destinations[] = [
                    'sequence_order' => $index + 1,
                    'destination_name' => sanitize($dest['name'] ?? ''),
                    'street' => sanitize($dest['street'] ?? ''),
                    'city' => sanitize($dest['city'] ?? ''),
                    'province' => sanitize($dest['province'] ?? ''),
                    'postal_code' => strtoupper(sanitize($dest['postal_code'] ?? '')),
                    'contact_name' => sanitize($dest['contact_name'] ?? ''),
                    'contact_phone' => sanitize($dest['contact_phone'] ?? ''),
                    'delivery_instructions' => sanitize($dest['instructions'] ?? ''),
                    'packages_count' => (int)($dest['packages_count'] ?? 1)
                ];
            }
        }

        // Items (for product fulfillment)
        $items = [];
        if ($shipmentType === 'product_fulfillment' && isset($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                if (!empty($item['name'])) {
                    $items[] = [
                        'item_name' => sanitize($item['name']),
                        'item_sku' => sanitize($item['sku'] ?? ''),
                        'item_description' => sanitize($item['description'] ?? ''),
                        'quantity' => (int)($item['quantity'] ?? 1),
                        'unit_value' => !empty($item['value']) ? (float)$item['value'] : null,
                        'weight_kg' => !empty($item['weight']) ? (float)$item['weight'] : null,
                        'is_fragile' => isset($item['fragile']) ? 1 : 0,
                        'special_handling' => sanitize($item['special_handling'] ?? '')
                    ];
                }
            }
        }

        // Validation
        $errors = $this->validateShipment($data, $isMultiDrop, $destinations);

        if (!empty($errors)) {
            $_SESSION['shipment_errors'] = $errors;
            $_SESSION['shipment_old'] = $_POST;
            redirect('distribution/shipments/create');
            return;
        }

        try {
            $this->db->beginTransaction();

            // Generate shipment number
            $shipmentNumber = 'SHP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Determine action (save draft or submit)
            $action = $_POST['action'] ?? 'draft';
            $status = ($action === 'submit') ? 'submitted' : 'draft';

            // Create shipment
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipments
                (business_profile_id, shipment_number, shipment_type, status, is_multi_drop,
                 pickup_street, pickup_city, pickup_province, pickup_postal_code,
                 pickup_contact_name, pickup_contact_phone, pickup_instructions,
                 requested_pickup_date, requested_pickup_time_start, requested_pickup_time_end,
                 destination_street, destination_city, destination_province, destination_postal_code,
                 destination_contact_name, destination_contact_phone, destination_instructions,
                 total_packages, total_weight_kg, package_description, business_notes,
                 submitted_at, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, NOW(), NOW())
            ");

            $stmt->execute([
                $businessId,
                $shipmentNumber,
                $data['shipment_type'],
                $status,
                $isMultiDrop ? 1 : 0,
                $data['pickup_street'],
                $data['pickup_city'],
                $data['pickup_province'],
                $data['pickup_postal_code'],
                $data['pickup_contact_name'],
                $data['pickup_contact_phone'],
                $data['pickup_instructions'] ?: null,
                $data['requested_pickup_date'] ?: null,
                $data['requested_pickup_time_start'] ?: null,
                $data['requested_pickup_time_end'] ?: null,
                $data['destination_street'] ?? null,
                $data['destination_city'] ?? null,
                $data['destination_province'] ?? null,
                $data['destination_postal_code'] ?? null,
                $data['destination_contact_name'] ?? null,
                $data['destination_contact_phone'] ?? null,
                $data['destination_instructions'] ?? null,
                $data['total_packages'],
                $data['total_weight_kg'],
                $data['package_description'] ?: null,
                $data['business_notes'] ?: null,
                ($status === 'submitted') ? date('Y-m-d H:i:s') : null
            ]);

            $shipmentId = $this->db->lastInsertId();

            // Add destinations (multi-drop)
            if ($isMultiDrop && !empty($destinations)) {
                $destStmt = $this->db->prepare("
                    INSERT INTO distribution_shipment_destinations
                    (shipment_id, sequence_order, destination_name, street, city, province, postal_code,
                     contact_name, contact_phone, delivery_instructions, packages_count, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($destinations as $dest) {
                    $destStmt->execute([
                        $shipmentId,
                        $dest['sequence_order'],
                        $dest['destination_name'],
                        $dest['street'],
                        $dest['city'],
                        $dest['province'],
                        $dest['postal_code'],
                        $dest['contact_name'] ?: null,
                        $dest['contact_phone'] ?: null,
                        $dest['delivery_instructions'] ?: null,
                        $dest['packages_count']
                    ]);
                }
            }

            // Add items (product fulfillment)
            if (!empty($items)) {
                $itemStmt = $this->db->prepare("
                    INSERT INTO distribution_shipment_items
                    (shipment_id, item_name, item_sku, item_description, quantity, unit_value, weight_kg, is_fragile, special_handling, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($items as $item) {
                    $itemStmt->execute([
                        $shipmentId,
                        $item['item_name'],
                        $item['item_sku'] ?: null,
                        $item['item_description'] ?: null,
                        $item['quantity'],
                        $item['unit_value'],
                        $item['weight_kg'],
                        $item['is_fragile'],
                        $item['special_handling'] ?: null
                    ]);
                }
            }

            // Log status
            $this->logStatusChange($shipmentId, null, $status, 'Shipment created');

            $this->db->commit();

            if ($status === 'submitted') {
                setFlash('success', 'Shipment submitted successfully. You will receive a quote shortly.');
            } else {
                setFlash('success', 'Shipment saved as draft.');
            }

            redirect('distribution/shipments/show?id=' . $shipmentId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Distribution shipment store error: ' . $e->getMessage());
            setFlash('error', 'Error creating shipment.');
            redirect('distribution/shipments/create');
        }
    }

    /**
     * View shipment details
     */
    public function show(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $shipmentId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get shipment
            $stmt = $this->db->prepare("
                SELECT s.*
                FROM distribution_shipments s
                WHERE s.id = ? AND s.business_profile_id = ?
            ");
            $stmt->execute([$shipmentId, $businessId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found.');
                redirect('distribution/shipments');
                return;
            }

            // Get destinations
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shipment_destinations
                WHERE shipment_id = ?
                ORDER BY sequence_order
            ");
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

            view('distribution.shipments.show', [
                'shipment'      => $shipment,
                'destinations'  => $destinations,
                'items'         => $items,
                'quote'         => $quote,
                'statusHistory' => $statusHistory,
                'business'      => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution shipment show error: ' . $e->getMessage());
            setFlash('error', 'Error loading shipment.');
            redirect('distribution/shipments');
        }
    }

    /**
     * Edit shipment (draft only)
     */
    public function edit(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $shipmentId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get shipment (draft only)
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shipments
                WHERE id = ? AND business_profile_id = ? AND status = 'draft'
            ");
            $stmt->execute([$shipmentId, $businessId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found or cannot be edited.');
                redirect('distribution/shipments');
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

            // Get business profile
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.phone
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('distribution.shipments.edit', [
                'shipment' => $shipment,
                'destinations' => $destinations,
                'items' => $items,
                'business' => $business,
                'errors' => $_SESSION['shipment_errors'] ?? [],
                'old' => $_SESSION['shipment_old'] ?? []
            ]);

            unset($_SESSION['shipment_errors'], $_SESSION['shipment_old']);

        } catch (\PDOException $e) {
            error_log('Distribution shipment edit error: ' . $e->getMessage());
            setFlash('error', 'Error loading shipment.');
            redirect('distribution/shipments');
        }
    }

    /**
     * Update shipment
     */
    public function update(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/shipments/edit?id=' . $shipmentId);
            return;
        }

        try {
            // Verify ownership and draft status
            $stmt = $this->db->prepare("SELECT id FROM distribution_shipments WHERE id = ? AND business_profile_id = ? AND status = 'draft'");
            $stmt->execute([$shipmentId, $businessId]);
            if (!$stmt->fetch()) {
                setFlash('error', 'Shipment not found or cannot be edited.');
                redirect('distribution/shipments');
                return;
            }

            $isMultiDrop = isset($_POST['is_multi_drop']) || $_POST['shipment_type'] === 'multi_drop';

            // Update shipment
            $stmt = $this->db->prepare("
                UPDATE distribution_shipments SET
                    shipment_type = ?,
                    is_multi_drop = ?,
                    pickup_street = ?,
                    pickup_city = ?,
                    pickup_province = ?,
                    pickup_postal_code = ?,
                    pickup_contact_name = ?,
                    pickup_contact_phone = ?,
                    pickup_instructions = ?,
                    requested_pickup_date = ?,
                    requested_pickup_time_start = ?,
                    requested_pickup_time_end = ?,
                    destination_street = ?,
                    destination_city = ?,
                    destination_province = ?,
                    destination_postal_code = ?,
                    destination_contact_name = ?,
                    destination_contact_phone = ?,
                    destination_instructions = ?,
                    total_packages = ?,
                    total_weight_kg = ?,
                    package_description = ?,
                    business_notes = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                sanitize($_POST['shipment_type'] ?? 'parcel'),
                $isMultiDrop ? 1 : 0,
                sanitize($_POST['pickup_street'] ?? ''),
                sanitize($_POST['pickup_city'] ?? ''),
                sanitize($_POST['pickup_province'] ?? ''),
                strtoupper(sanitize($_POST['pickup_postal_code'] ?? '')),
                sanitize($_POST['pickup_contact_name'] ?? ''),
                sanitize($_POST['pickup_contact_phone'] ?? ''),
                sanitize($_POST['pickup_instructions'] ?? '') ?: null,
                sanitize($_POST['requested_pickup_date'] ?? '') ?: null,
                sanitize($_POST['requested_pickup_time_start'] ?? '') ?: null,
                sanitize($_POST['requested_pickup_time_end'] ?? '') ?: null,
                $isMultiDrop ? null : sanitize($_POST['destination_street'] ?? ''),
                $isMultiDrop ? null : sanitize($_POST['destination_city'] ?? ''),
                $isMultiDrop ? null : sanitize($_POST['destination_province'] ?? ''),
                $isMultiDrop ? null : strtoupper(sanitize($_POST['destination_postal_code'] ?? '')),
                $isMultiDrop ? null : sanitize($_POST['destination_contact_name'] ?? ''),
                $isMultiDrop ? null : sanitize($_POST['destination_contact_phone'] ?? ''),
                $isMultiDrop ? null : sanitize($_POST['destination_instructions'] ?? ''),
                (int)($_POST['total_packages'] ?? 1),
                !empty($_POST['total_weight_kg']) ? (float)$_POST['total_weight_kg'] : null,
                sanitize($_POST['package_description'] ?? '') ?: null,
                sanitize($_POST['business_notes'] ?? '') ?: null,
                $shipmentId
            ]);

            // Update destinations if multi-drop
            if ($isMultiDrop) {
                // Delete existing destinations
                $this->db->prepare("DELETE FROM distribution_shipment_destinations WHERE shipment_id = ?")->execute([$shipmentId]);

                // Add new destinations
                if (isset($_POST['destinations'])) {
                    $destStmt = $this->db->prepare("
                        INSERT INTO distribution_shipment_destinations
                        (shipment_id, sequence_order, destination_name, street, city, province, postal_code,
                         contact_name, contact_phone, delivery_instructions, packages_count, created_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
                    ");

                    foreach ($_POST['destinations'] as $index => $dest) {
                        $destStmt->execute([
                            $shipmentId,
                            $index + 1,
                            sanitize($dest['name'] ?? ''),
                            sanitize($dest['street'] ?? ''),
                            sanitize($dest['city'] ?? ''),
                            sanitize($dest['province'] ?? ''),
                            strtoupper(sanitize($dest['postal_code'] ?? '')),
                            sanitize($dest['contact_name'] ?? '') ?: null,
                            sanitize($dest['contact_phone'] ?? '') ?: null,
                            sanitize($dest['instructions'] ?? '') ?: null,
                            (int)($dest['packages_count'] ?? 1)
                        ]);
                    }
                }
            }

            // Check if submitting
            if (($_POST['action'] ?? '') === 'submit') {
                $this->db->prepare("UPDATE distribution_shipments SET status = 'submitted', submitted_at = NOW() WHERE id = ?")->execute([$shipmentId]);
                $this->logStatusChange($shipmentId, 'draft', 'submitted', 'Shipment submitted for quote');
                setFlash('success', 'Shipment submitted successfully.');
            } else {
                setFlash('success', 'Shipment updated successfully.');
            }

            redirect('distribution/shipments/show?id=' . $shipmentId);

        } catch (\PDOException $e) {
            error_log('Distribution shipment update error: ' . $e->getMessage());
            setFlash('error', 'Error updating shipment.');
            redirect('distribution/shipments/edit?id=' . $shipmentId);
        }
    }

    /**
     * Submit a draft shipment
     */
    public function submit(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/shipments/show?id=' . $shipmentId);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE distribution_shipments
                SET status = 'submitted', submitted_at = NOW(), updated_at = NOW()
                WHERE id = ? AND business_profile_id = ? AND status = 'draft'
            ");
            $stmt->execute([$shipmentId, $businessId]);

            if ($stmt->rowCount() > 0) {
                $this->logStatusChange($shipmentId, 'draft', 'submitted', 'Shipment submitted for quote');
                setFlash('success', 'Shipment submitted successfully. You will receive a quote shortly.');
            } else {
                setFlash('error', 'Shipment not found or already submitted.');
            }

            redirect('distribution/shipments/show?id=' . $shipmentId);

        } catch (\PDOException $e) {
            error_log('Distribution shipment submit error: ' . $e->getMessage());
            setFlash('error', 'Error submitting shipment.');
            redirect('distribution/shipments/show?id=' . $shipmentId);
        }
    }

    /**
     * Cancel a shipment
     */
    public function cancel(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/shipments');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];
        $reason = sanitize($_POST['reason'] ?? '');

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/shipments/show?id=' . $shipmentId);
            return;
        }

        try {
            // Can only cancel if not picked up yet
            $stmt = $this->db->prepare("
                SELECT status FROM distribution_shipments
                WHERE id = ? AND business_profile_id = ? AND status NOT IN ('picked_up', 'in_transit', 'delivered', 'completed', 'cancelled')
            ");
            $stmt->execute([$shipmentId, $businessId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Shipment not found or cannot be cancelled.');
                redirect('distribution/shipments');
                return;
            }

            $oldStatus = $shipment['status'];

            $stmt = $this->db->prepare("
                UPDATE distribution_shipments
                SET status = 'cancelled', cancelled_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$shipmentId]);

            $this->logStatusChange($shipmentId, $oldStatus, 'cancelled', $reason ?: 'Cancelled by business');

            setFlash('success', 'Shipment cancelled successfully.');
            redirect('distribution/shipments');

        } catch (\PDOException $e) {
            error_log('Distribution shipment cancel error: ' . $e->getMessage());
            setFlash('error', 'Error cancelling shipment.');
            redirect('distribution/shipments/show?id=' . $shipmentId);
        }
    }

    /**
     * Public tracking page
     */
    public function track(): void
    {
        $shipmentNumber = sanitize($_GET['number'] ?? '');

        if (empty($shipmentNumber)) {
            $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
            view('distribution.shipments.track', ['shipment' => null, 'error' => $fr ? 'Veuillez entrer un numéro d\'envoi.' : 'Please enter a shipment number.', 'business' => $this->getBusinessData()]);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT s.*, bp.company_name
                FROM distribution_shipments s
                INNER JOIN business_profiles bp ON s.business_profile_id = bp.id
                WHERE s.shipment_number = ?
            ");
            $stmt->execute([$shipmentNumber]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
                view('distribution.shipments.track', ['shipment' => null, 'error' => $fr ? 'Envoi introuvable.' : 'Shipment not found.', 'business' => $this->getBusinessData()]);
                return;
            }

            // Get destinations
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_destinations WHERE shipment_id = ? ORDER BY sequence_order");
            $stmt->execute([$shipment['id']]);
            $destinations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get status history
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_status_history WHERE shipment_id = ? ORDER BY created_at DESC");
            $stmt->execute([$shipment['id']]);
            $statusHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('distribution.shipments.track', [
                'shipment'      => $shipment,
                'destinations'  => $destinations,
                'statusHistory' => $statusHistory,
                'error'         => null,
                'business'      => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution shipment track error: ' . $e->getMessage());
            view('distribution.shipments.track', ['shipment' => null, 'error' => 'Error loading shipment.', 'business' => $this->getBusinessData()]);
        }
    }

    /**
     * Validate shipment data
     */
    private function validateShipment(array $data, bool $isMultiDrop, array $destinations): array
    {
        $errors = [];

        // Pickup validation
        if (empty($data['pickup_street'])) {
            $errors['pickup_street'] = 'Pickup address is required.';
        }
        if (empty($data['pickup_city'])) {
            $errors['pickup_city'] = 'Pickup city is required.';
        }
        if (empty($data['pickup_province'])) {
            $errors['pickup_province'] = 'Pickup province is required.';
        }
        if (empty($data['pickup_postal_code'])) {
            $errors['pickup_postal_code'] = 'Pickup postal code is required.';
        } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/i', $data['pickup_postal_code'])) {
            $errors['pickup_postal_code'] = 'Please enter a valid Canadian postal code.';
        }

        // Destination validation
        if (!$isMultiDrop) {
            if (empty($data['destination_street'])) {
                $errors['destination_street'] = 'Destination address is required.';
            }
            if (empty($data['destination_city'])) {
                $errors['destination_city'] = 'Destination city is required.';
            }
            if (empty($data['destination_province'])) {
                $errors['destination_province'] = 'Destination province is required.';
            }
            if (empty($data['destination_postal_code'])) {
                $errors['destination_postal_code'] = 'Destination postal code is required.';
            } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/i', $data['destination_postal_code'])) {
                $errors['destination_postal_code'] = 'Please enter a valid Canadian postal code.';
            }
        } else {
            if (empty($destinations)) {
                $errors['destinations'] = 'At least one destination is required for multi-drop shipments.';
            }
        }

        return $errors;
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $shipmentId, ?string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipment_status_history
                (shipment_id, old_status, new_status, changed_by_type, changed_by_id, notes, created_at)
                VALUES (?, ?, ?, 'business', ?, ?, NOW())
            ");
            $stmt->execute([
                $shipmentId,
                $fromStatus,
                $toStatus,
                $_SESSION['user']['id'] ?? null,
                $notes
            ]);
        } catch (\PDOException $e) {
            error_log('Shipment status history log error: ' . $e->getMessage());
        }
    }

    /**
     * Check if business user is logged in
     */
    private function isBusinessLoggedIn(): bool
    {
        if (!isset($_SESSION['user']['role'], $_SESSION['business']['id'])) {
            return false;
        }
        // Business users, or admins who also have a business profile
        return in_array($_SESSION['user']['role'], ['business', 'admin', 'super_admin'], true);
    }
}
