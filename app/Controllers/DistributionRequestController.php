<?php

namespace App\Controllers;

/**
 * DistributionRequestController - Procurement Request Management
 * Handles shopping list requests for business accounts
 */
class DistributionRequestController
{
    private $db;

    // Pricing Tiers Configuration (must match create.php JavaScript)
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
     * Calculate delivery fee based on distance and tier
     */
    private function calculateDeliveryFee(float $distance, int $tier): float
    {
        $tierConfig = self::PRICING_TIERS[$tier];
        if ($distance <= $tierConfig['freeDeliveryKm']) {
            return 0;
        }
        $extraKm = $distance - $tierConfig['freeDeliveryKm'];
        return $extraKm * $tierConfig['perKmRate'];
    }

    /**
     * Calculate full summary breakdown
     * @param float $itemsTotal Total cost of items
     * @param float $deliveryDistance Delivery distance in km
     * @param float $totalWeightKg Total weight of catalog items in kg
     * @param float $tipAmount Optional tip amount (pre-tax, calculated on items subtotal)
     */
    private function calculateSummary(float $itemsTotal, float $deliveryDistance, float $totalWeightKg = 0, float $tipAmount = 0): array
    {
        $tier = $this->getTier($itemsTotal);
        $tierConfig = self::PRICING_TIERS[$tier];

        $serviceFee = $itemsTotal * $tierConfig['serviceFee'];
        $handlingFee = $totalWeightKg * self::HANDLING_RATE_PER_KG;
        $deliveryFee = $this->calculateDeliveryFee($deliveryDistance, $tier);

        $subtotal = $itemsTotal + $serviceFee + $handlingFee + $deliveryFee;
        $gstAmount = $subtotal * self::GST_RATE;
        $qstAmount = $subtotal * self::QST_RATE;
        $totalAmount = $subtotal + $gstAmount + $qstAmount + $tipAmount;

        return [
            'tier' => $tier,
            'items_total' => round($itemsTotal, 2),
            'service_fee' => round($serviceFee, 2),
            'handling_fee' => round($handlingFee, 2),
            'total_weight_kg' => round($totalWeightKg, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'tip_amount' => round($tipAmount, 2),
            'subtotal' => round($subtotal, 2),
            'gst_amount' => round($gstAmount, 2),
            'qst_amount' => round($qstAmount, 2),
            'tax_amount' => round($gstAmount + $qstAmount, 2),
            'total_amount' => round($totalAmount, 2)
        ];
    }

    /**
     * Calculate delivery distance server-side using geocoding
     * Returns distance in km or null if unable to calculate
     */
    private function calculateServerDistance(int $businessId, array $catalogItems): ?float
    {
        try {
            require_once BASE_PATH . '/app/Helpers/GeocodingHelper.php';

            // Get business delivery coordinates
            $stmt = $this->db->prepare("
                SELECT delivery_postal_code, delivery_latitude, delivery_longitude
                FROM business_profiles WHERE id = ?
            ");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) return null;

            $deliveryLat = $business['delivery_latitude'];
            $deliveryLng = $business['delivery_longitude'];

            // If no stored coords, try geocoding
            if (!$deliveryLat || !$deliveryLng) {
                if (!empty($business['delivery_postal_code'])) {
                    $coords = \GeocodingHelper::geocodePostalCode($business['delivery_postal_code']);
                    if ($coords) {
                        $deliveryLat = $coords['lat'];
                        $deliveryLng = $coords['lng'];
                        // Save for future use
                        $update = $this->db->prepare("UPDATE business_profiles SET delivery_latitude = ?, delivery_longitude = ? WHERE id = ?");
                        $update->execute([$deliveryLat, $deliveryLng, $businessId]);
                    }
                }
            }

            if (!$deliveryLat || !$deliveryLng) return null;

            // Get involved supplier IDs from catalog items
            $supplierIds = [];
            if (!empty($catalogItems)) {
                $productIds = array_keys(array_filter($catalogItems, fn($qty) => (int)$qty > 0));
                if (!empty($productIds)) {
                    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
                    $stmt = $this->db->prepare("
                        SELECT DISTINCT s.id, s.latitude, s.longitude, s.postal_code, s.name
                        FROM supplier_products sp
                        INNER JOIN suppliers s ON sp.supplier_id = s.id
                        WHERE sp.id IN ({$placeholders})
                    ");
                    $stmt->execute($productIds);
                    $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    foreach ($suppliers as $s) {
                        $lat = $s['latitude'];
                        $lng = $s['longitude'];

                        // Geocode if needed
                        if ((!$lat || !$lng) && !empty($s['postal_code'])) {
                            $coords = \GeocodingHelper::geocodePostalCode($s['postal_code']);
                            if ($coords) {
                                $lat = $coords['lat'];
                                $lng = $coords['lng'];
                                $update = $this->db->prepare("UPDATE suppliers SET latitude = ?, longitude = ? WHERE id = ?");
                                $update->execute([$lat, $lng, $s['id']]);
                            }
                        }

                        if ($lat && $lng) {
                            $supplierIds[] = ['lat' => (float)$lat, 'lng' => (float)$lng];
                        }
                    }
                }
            }

            if (empty($supplierIds)) return null;

            // Build route and calculate distance
            $customerCoord = ['lat' => (float)$deliveryLat, 'lng' => (float)$deliveryLng];
            $route = \GeocodingHelper::optimizeRoute($supplierIds, $customerCoord);
            return \GeocodingHelper::calculateRouteDistance($route);

        } catch (\Exception $e) {
            error_log('Server distance calculation error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * List all requests for the logged-in business
     */
    /**
     * List invoices for the logged-in business
     */
    public function invoices(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        $stmt = $this->db->prepare("
            SELECT di.*,
                   dr.request_number
            FROM distribution_invoices di
            INNER JOIN distribution_requests dr ON di.distribution_request_id = dr.id
            WHERE di.business_profile_id = ?
            ORDER BY di.created_at DESC
        ");
        $stmt->execute([$businessId]);
        $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $lang = $_SESSION['language'] ?? 'fr';
        view('distribution.invoices.index', [
            'invoices'    => $invoices,
            'business'    => $this->getBusinessData(),
            'pageTitle'   => $lang === 'fr' ? 'Factures' : 'Invoices',
            'currentPage' => 'invoices',
        ]);
    }

    public function index(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $status = sanitize($_GET['status'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        try {
            // Build query with optional status filter
            $whereClause = "WHERE dr.business_profile_id = ?";
            $params = [$businessId];

            if ($status && in_array($status, ['draft', 'submitted', 'quoted', 'pending_payment', 'paid', 'processing', 'ready', 'completed', 'cancelled'])) {
                $whereClause .= " AND dr.status = ?";
                $params[] = $status;
            }

            // Get total count
            $countStmt = $this->db->prepare("
                SELECT COUNT(*) as total
                FROM distribution_requests dr
                $whereClause
            ");
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get requests with pagination
            $paginatedParams = array_merge($params, [$perPage, $offset]);
            $stmt = $this->db->prepare("
                SELECT
                    dr.*,
                    (SELECT COUNT(*) FROM distribution_request_items WHERE distribution_request_id = dr.id) as catalog_items_count,
                    (SELECT COUNT(*) FROM distribution_shopping_items WHERE distribution_request_id = dr.id) as shopping_items_count,
                    di.invoice_number,
                    di.total_amount as invoice_total
                FROM distribution_requests dr
                LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
                $whereClause
                ORDER BY dr.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute($paginatedParams);
            $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $totalPages = ceil($total / $perPage);

            view('distribution.requests.index', [
                'requests'      => $requests,
                'currentStatus' => $status,
                'currentPage'   => 'requests',
                'paginationPage'=> $page,
                'totalPages'    => $totalPages,
                'total'         => $total,
                'business'      => $this->getBusinessData(),
                'pageTitle'     => (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Mes demandes' : 'My Requests',
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution requests index error: ' . $e->getMessage());
            setFlash('error', 'Error loading requests.');
            redirect('distribution/dashboard');
        }
    }

    /**
     * Show create request form
     */
    public function create(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        try {
            // Get active suppliers with product counts and coordinates
            $stmt = $this->db->query("
                SELECT
                    s.id,
                    s.name,
                    s.company_name,
                    s.address,
                    s.city,
                    s.province,
                    s.postal_code,
                    s.latitude,
                    s.longitude,
                    COUNT(sp.id) as product_count
                FROM suppliers s
                LEFT JOIN supplier_products sp ON s.id = sp.supplier_id AND sp.is_available = 1
                WHERE s.status = 'active'
                GROUP BY s.id
                HAVING product_count > 0
                ORDER BY s.name ASC
            ");
            $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Build supplier coordinates map for JS
            // If a supplier is missing coordinates but has a postal code, geocode and save
            require_once BASE_PATH . '/app/Helpers/GeocodingHelper.php';
            $supplierCoords = [];
            foreach ($suppliers as $s) {
                $lat = $s['latitude'];
                $lng = $s['longitude'];

                if ((!$lat || !$lng) && !empty($s['postal_code'])) {
                    $coords = \GeocodingHelper::geocodePostalCode($s['postal_code']);
                    if ($coords) {
                        $lat = $coords['lat'];
                        $lng = $coords['lng'];
                        // Save so we don't need to geocode again
                        $update = $this->db->prepare("UPDATE suppliers SET latitude = ?, longitude = ? WHERE id = ?");
                        $update->execute([$lat, $lng, $s['id']]);
                    }
                }

                if ($lat && $lng) {
                    $supplierCoords[$s['id']] = [
                        'lat' => (float)$lat,
                        'lng' => (float)$lng,
                        'name' => $s['company_name'] ?? $s['name'] ?? 'Supplier',
                        'city' => $s['city'] ?? ''
                    ];
                }
            }

            // Get all supplier products grouped by supplier
            $stmt = $this->db->query("
                SELECT
                    sp.id,
                    sp.supplier_id,
                    sp.product_name as name,
                    sp.sku,
                    sp.description,
                    sp.unit_price as price,
                    sp.unit,
                    sp.minimum_order_quantity,
                    sp.image,
                    sp.weight_kg
                FROM supplier_products sp
                INNER JOIN suppliers s ON sp.supplier_id = s.id AND s.status = 'active'
                WHERE sp.is_available = 1
                ORDER BY sp.product_name ASC
            ");
            $allProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Group products by supplier
            $productsBySupplier = [];
            foreach ($allProducts as $product) {
                $supplierId = $product['supplier_id'];
                if (!isset($productsBySupplier[$supplierId])) {
                    $productsBySupplier[$supplierId] = [];
                }
                $productsBySupplier[$supplierId][] = $product;
            }

            // Get business delivery address with coordinates
            $stmt = $this->db->prepare("
                SELECT delivery_street, delivery_city, delivery_province, delivery_postal_code, delivery_country,
                       delivery_latitude, delivery_longitude
                FROM business_profiles
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['business']['id']]);
            $businessAddress = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Geocode server-side if coordinates are missing (saves to DB for next visit)
            if ($businessAddress && (empty($businessAddress['delivery_latitude']) || empty($businessAddress['delivery_longitude']))) {
                require_once BASE_PATH . '/app/Helpers/GeocodingHelper.php';
                $coords = null;
                // Try full address first, then city/province fallback
                if (!empty($businessAddress['delivery_postal_code']) && !empty($businessAddress['delivery_city'])) {
                    $coords = \GeocodingHelper::geocodePostalCode($businessAddress['delivery_postal_code']);
                }
                if (!$coords && !empty($businessAddress['delivery_city'])) {
                    $coords = \GeocodingHelper::geocodeAddress(
                        $businessAddress['delivery_city'],
                        $businessAddress['delivery_province'] ?? ''
                    );
                }
                if ($coords) {
                    $businessAddress['delivery_latitude']  = $coords['lat'];
                    $businessAddress['delivery_longitude'] = $coords['lng'];
                    $this->db->prepare("UPDATE business_profiles SET delivery_latitude = ?, delivery_longitude = ? WHERE id = ?")
                             ->execute([$coords['lat'], $coords['lng'], $_SESSION['business']['id']]);
                }
            }

            view('distribution.requests.create', [
                'suppliers'          => $suppliers,
                'supplierCoords'     => $supplierCoords,
                'productsBySupplier' => $productsBySupplier,
                'businessAddress'    => $businessAddress,
                'errors'             => $_SESSION['request_errors'] ?? [],
                'old'                => $_SESSION['request_old'] ?? [],
                'business'           => $this->getBusinessData(),
                'pageTitle'          => (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Nouvelle demande' : 'New Request',
                'currentPage'        => 'request-create',
            ]);

            unset($_SESSION['request_errors'], $_SESSION['request_old']);

        } catch (\PDOException $e) {
            error_log('Distribution request create error: ' . $e->getMessage());
            setFlash('error', 'Error loading request form.');
            redirect('distribution/requests');
        }
    }

    /**
     * Store new request
     */
    public function store(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/requests/create');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['request_errors'] = ['general' => 'Invalid request. Please try again.'];
            redirect('distribution/requests/create');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        // Collect data
        $rawDeliveryType = $_POST['delivery_type'] ?? 'scheduled';
        $deliveryType    = in_array($rawDeliveryType, ['express', 'scheduled', 'same_day']) ? $rawDeliveryType : 'scheduled';

        // same_day: use today's date, pull time window from same_day_time_* fields
        if ($deliveryType === 'same_day') {
            $scheduledDate     = date('Y-m-d');
            $scheduledTimeFrom = sanitize($_POST['same_day_time_from'] ?? '');
            $scheduledTimeTo   = sanitize($_POST['same_day_time_to'] ?? '');
        } elseif ($deliveryType === 'scheduled') {
            $scheduledDate     = sanitize($_POST['scheduled_date'] ?? '');
            $scheduledTimeFrom = sanitize($_POST['scheduled_time_from'] ?? '');
            $scheduledTimeTo   = sanitize($_POST['scheduled_time_to'] ?? '');
        } else {
            $scheduledDate = $scheduledTimeFrom = $scheduledTimeTo = null;
        }

        $data = [
            'request_name'         => sanitize($_POST['request_name'] ?? ''),
            'notes'                => sanitize($_POST['notes'] ?? ''),
            'delivery_street'      => sanitize($_POST['delivery_street'] ?? ''),
            'delivery_city'        => sanitize($_POST['delivery_city'] ?? ''),
            'delivery_province'    => sanitize($_POST['delivery_province'] ?? ''),
            'delivery_postal_code' => strtoupper(sanitize($_POST['delivery_postal_code'] ?? '')),
            'preferred_delivery_date' => sanitize($_POST['preferred_delivery_date'] ?? ''),
            'delivery_type'        => $deliveryType,
            'scheduled_date'       => $scheduledDate ?: null,
            'scheduled_time_from'  => $scheduledTimeFrom ?: null,
            'scheduled_time_to'    => $scheduledTimeTo ?: null,
            'delivery_distance'    => (float)($_POST['delivery_distance'] ?? 0),
            'catalog_items'        => $_POST['catalog_items'] ?? [],
            'shopping_items'       => $_POST['shopping_items'] ?? [],
            'tip_percentage'       => (int)($_POST['tip_percentage'] ?? 0),
            'tip_custom_amount'    => (float)($_POST['tip_custom_amount'] ?? 0),
        ];

        // Validation
        $errors = $this->validateRequest($data);

        if (!empty($errors)) {
            $_SESSION['request_errors'] = $errors;
            $_SESSION['request_old'] = $data;
            redirect('distribution/requests/create');
            return;
        }

        try {
            $this->db->beginTransaction();

            // Generate request number
            $requestNumber = 'REQ-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));

            // Pre-calculate items total and total weight
            $itemsTotal = 0;
            $totalWeightKg = 0;
            if (!empty($data['catalog_items'])) {
                foreach ($data['catalog_items'] as $supplierProductId => $qty) {
                    $quantity = (int)$qty;
                    if ($quantity > 0) {
                        // Get supplier product price and weight
                        $priceStmt = $this->db->prepare("
                            SELECT unit_price, weight_kg FROM supplier_products
                            WHERE id = ? AND is_available = 1 LIMIT 1
                        ");
                        $priceStmt->execute([$supplierProductId]);
                        $priceRow = $priceStmt->fetch(\PDO::FETCH_ASSOC);
                        if ($priceRow) {
                            $itemsTotal += $quantity * $priceRow['unit_price'];
                            $totalWeightKg += $quantity * ($priceRow['weight_kg'] ?? 0);
                        }
                    }
                }
            }

            // Calculate tip (based on pre-tax items total per Canadian law)
            $tipPercentage = (int)($data['tip_percentage'] ?? 0);
            $tipCustomAmount = (float)($data['tip_custom_amount'] ?? 0);
            $tipAmount = 0;
            if (in_array($tipPercentage, [15, 18, 20])) {
                $tipAmount = round($itemsTotal * ($tipPercentage / 100), 2);
            } elseif ($tipCustomAmount > 0) {
                // Custom dollar amount - cap at reasonable maximum (100% of items total)
                $tipAmount = min(round($tipCustomAmount, 2), $itemsTotal);
                $tipPercentage = 0;
            } else {
                $tipPercentage = 0;
            }

            // Server-side distance validation using geocoding
            $serverDistance = $this->calculateServerDistance($businessId, $data['catalog_items'] ?? []);
            if ($serverDistance !== null) {
                $data['delivery_distance'] = $serverDistance;
            }

            // Calculate full summary breakdown with weight and tip
            $summary = $this->calculateSummary($itemsTotal, $data['delivery_distance'], $totalWeightKg, $tipAmount);

            // Create request with summary data
            $stmt = $this->db->prepare("
                INSERT INTO distribution_requests
                (business_profile_id, request_number, request_name, notes, delivery_street, delivery_city,
                 delivery_province, delivery_postal_code, preferred_delivery_date, delivery_distance,
                 delivery_type, scheduled_date, scheduled_time_from, scheduled_time_to,
                 tier, items_total, service_fee, handling_fee, total_weight_kg, delivery_fee,
                 tip_amount, tip_percentage, subtotal,
                 gst_amount, qst_amount, tax_amount, total_amount, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'draft', NOW(), NOW())
            ");
            $stmt->execute([
                $businessId,
                $requestNumber,
                $data['request_name'],
                $data['notes'],
                $data['delivery_street'],
                $data['delivery_city'],
                $data['delivery_province'],
                $data['delivery_postal_code'],
                $data['preferred_delivery_date'] ?: null,
                $data['delivery_distance'],
                $data['delivery_type'],
                $data['scheduled_date'] ?: null,
                $data['scheduled_time_from'] ?: null,
                $data['scheduled_time_to'] ?: null,
                $summary['tier'],
                $summary['items_total'],
                $summary['service_fee'],
                $summary['handling_fee'],
                $summary['total_weight_kg'],
                $summary['delivery_fee'],
                $summary['tip_amount'],
                $tipPercentage,
                $summary['subtotal'],
                $summary['gst_amount'],
                $summary['qst_amount'],
                $summary['tax_amount'],
                $summary['total_amount']
            ]);

            $requestId = $this->db->lastInsertId();

            // Add catalog items (from supplier products)
            if (!empty($data['catalog_items'])) {
                $itemStmt = $this->db->prepare("
                    INSERT INTO distribution_request_items
                    (distribution_request_id, product_id, product_name, product_sku, product_image, quantity, unit_price, subtotal, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($data['catalog_items'] as $supplierProductId => $qty) {
                    $quantity = (int)$qty;
                    if ($quantity > 0) {
                        // Get supplier product details
                        $productStmt = $this->db->prepare("
                            SELECT sp.id, sp.product_name, sp.sku, sp.unit_price, sp.image
                            FROM supplier_products sp
                            INNER JOIN suppliers s ON sp.supplier_id = s.id AND s.status = 'active'
                            WHERE sp.id = ? AND sp.is_available = 1
                            LIMIT 1
                        ");
                        $productStmt->execute([$supplierProductId]);
                        $product = $productStmt->fetch(\PDO::FETCH_ASSOC);

                        if ($product) {
                            $subtotal = $quantity * $product['unit_price'];
                            $itemStmt->execute([
                                $requestId,
                                $supplierProductId, // Store supplier_product_id in product_id column
                                $product['product_name'],
                                $product['sku'],
                                $product['image'],
                                $quantity,
                                $product['unit_price'],
                                $subtotal
                            ]);
                        }
                    }
                }
            }

            // Add shopping list items (free-form)
            if (!empty($data['shopping_items'])) {
                $shoppingStmt = $this->db->prepare("
                    INSERT INTO distribution_shopping_items
                    (distribution_request_id, item_description, quantity, estimated_price, admin_notes, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                foreach ($data['shopping_items'] as $item) {
                    if (!empty($item['description'])) {
                        $shoppingStmt->execute([
                            $requestId,
                            sanitize($item['description']),
                            $item['quantity'] ?? '1',
                            !empty($item['estimated_price']) ? (float)$item['estimated_price'] : null,
                            sanitize($item['notes'] ?? '')
                        ]);
                    }
                }
            }

            // Log status history
            $this->logStatusChange($requestId, null, 'draft', 'Request created');

            $this->db->commit();

            // Admin bell + email notification for new distribution request
            try {
                $businessName = $_SESSION['business']['company_name'] ?? 'a business';
                $adminLink    = '/admin/distribution/view?id=' . $requestId;

                \App\Helpers\NotificationHelper::add(
                    'distribution_request',
                    'New Distribution Request',
                    "Request #{$requestNumber} submitted by {$businessName} — $" . number_format($summary['total_amount'], 2),
                    ['link' => $adminLink, 'icon' => 'truck']
                );

                $mailConfig = require dirname(__DIR__, 2) . '/config/mail.php';
                $adminEmail = $mailConfig['admin_email'] ?? 'info@ocsapp.ca';

                \App\Helpers\EmailHelper::setNextMeta('distribution_request', 'distribution_request', (int)$requestId);
                \App\Helpers\EmailHelper::sendTemplate(
                    $adminEmail,
                    'planner-notification',
                    [
                        'user_first_name'       => 'Admin',
                        'notification_title'    => 'New Distribution Request',
                        'notification_message'  => "A new distribution request #{$requestNumber} has been submitted by {$businessName} totalling $" . number_format($summary['total_amount'], 2) . ". Please review and process.",
                        'action_url'            => 'https://ocsapp.ca' . $adminLink,
                        'current_year'          => date('Y'),
                    ]
                );
            } catch (\Exception $e) {
                logger('Distribution request notification failed: ' . $e->getMessage(), 'warning');
            }

            setFlash('success', 'Request created successfully! You can review and submit it when ready.');
            redirect('distribution/requests/show?id=' . $requestId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Distribution request store error: ' . $e->getMessage());
            $_SESSION['request_errors'] = ['general' => 'An error occurred. Please try again.'];
            $_SESSION['request_old'] = $data;
            redirect('distribution/requests/create');
        }
    }

    /**
     * Show single request details
     */
    public function show(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $requestId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get request
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                WHERE dr.id = ? AND dr.business_profile_id = ?
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found.');
                redirect('distribution/requests');
                return;
            }

            // Get catalog items grouped by supplier
            $stmt = $this->db->prepare("
                SELECT
                    dri.*,
                    COALESCE(dri.product_name, sp.product_name) as product_name,
                    COALESCE(dri.product_sku, sp.sku)           as sku,
                    COALESCE(dri.product_image, sp.image)       as image,
                    s.id                                         as supplier_id,
                    COALESCE(s.company_name, s.name, 'Unknown Supplier') as supplier_name
                FROM distribution_request_items dri
                LEFT JOIN supplier_products sp ON dri.product_id = sp.id
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE dri.distribution_request_id = ?
                ORDER BY supplier_name, product_name
            ");
            $stmt->execute([$requestId]);
            $catalogItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get shopping list items
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shopping_items
                WHERE distribution_request_id = ?
                ORDER BY id
            ");
            $stmt->execute([$requestId]);
            $shoppingItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get invoice if exists
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_invoices WHERE distribution_request_id = ?
            ");
            $stmt->execute([$requestId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get status history
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_status_history
                WHERE distribution_request_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$requestId]);
            $statusHistory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get payments
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_payments
                WHERE distribution_request_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$requestId]);
            $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Calculate totals from items (for display)
            $catalogTotal = 0;
            foreach ($catalogItems as $item) {
                $catalogTotal += $item['quantity'] * $item['unit_price'];
            }

            $shoppingEstimate = 0;
            foreach ($shoppingItems as $item) {
                if (!empty($item['unit_price'])) {
                    $shoppingEstimate += (int)$item['quantity'] * $item['unit_price'];
                } elseif (!empty($item['estimated_price'])) {
                    $shoppingEstimate += (int)$item['quantity'] * $item['estimated_price'];
                }
            }

            // Get tier configuration for display
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

            // Check if assigned driver has accepted (for stage bar granularity)
            $driverAccepted = false;
            if (($request['status'] ?? '') === 'ready') {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) FROM purchase_orders
                    WHERE distribution_request_id = ? AND driver_acceptance_status = 'accepted'
                ");
                $stmt->execute([$requestId]);
                $driverAccepted = (int)$stmt->fetchColumn() > 0;
            }

            view('distribution.requests.show', [
                'request'          => $request,
                'catalogItems'     => $catalogItems,
                'shoppingItems'    => $shoppingItems,
                'invoice'          => $invoice,
                'statusHistory'    => $statusHistory,
                'payments'         => $payments,
                'catalogTotal'     => $catalogTotal,
                'shoppingEstimate' => $shoppingEstimate,
                'summary'          => $summary,
                'business'         => $this->getBusinessData(),
                'driverAccepted'   => $driverAccepted,
                'pageTitle'        => ((($_SESSION['language'] ?? 'fr') === 'fr') ? 'Demande #' : 'Request #') . ($request['request_number'] ?? ''),
                'currentPage'      => 'requests',
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution request show error: ' . $e->getMessage());
            setFlash('error', 'Error loading request.');
            redirect('distribution/requests');
        }
    }

    /**
     * Show edit form for draft requests
     */
    public function edit(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $requestId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get request (only drafts can be edited)
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_requests
                WHERE id = ? AND business_profile_id = ? AND status = 'draft'
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or cannot be edited.');
                redirect('distribution/requests');
                return;
            }

            // Get existing catalog items (now using supplier_products)
            $stmt = $this->db->prepare("
                SELECT dri.*, dri.product_name, dri.product_sku as sku, dri.product_image as image
                FROM distribution_request_items dri
                WHERE dri.distribution_request_id = ?
            ");
            $stmt->execute([$requestId]);
            $existingCatalogItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Convert to keyed array for form (product_id is supplier_product_id)
            $catalogItemsKeyed = [];
            foreach ($existingCatalogItems as $item) {
                $catalogItemsKeyed[$item['product_id']] = $item['quantity'];
            }

            // Get existing shopping items
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shopping_items WHERE distribution_request_id = ?
            ");
            $stmt->execute([$requestId]);
            $shoppingItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get active suppliers with product counts (same as create)
            $stmt = $this->db->query("
                SELECT
                    s.id,
                    s.name,
                    s.company_name,
                    COUNT(sp.id) as product_count
                FROM suppliers s
                LEFT JOIN supplier_products sp ON s.id = sp.supplier_id AND sp.is_available = 1
                WHERE s.status = 'active'
                GROUP BY s.id
                HAVING product_count > 0
                ORDER BY s.name ASC
            ");
            $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get all supplier products grouped by supplier (same as create)
            $stmt = $this->db->query("
                SELECT
                    sp.id,
                    sp.supplier_id,
                    sp.product_name as name,
                    sp.sku,
                    sp.description,
                    sp.unit_price as price,
                    sp.unit,
                    sp.minimum_order_quantity,
                    sp.image,
                    sp.weight_kg
                FROM supplier_products sp
                INNER JOIN suppliers s ON sp.supplier_id = s.id AND s.status = 'active'
                WHERE sp.is_available = 1
                ORDER BY sp.product_name ASC
            ");
            $allProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Group products by supplier
            $productsBySupplier = [];
            foreach ($allProducts as $product) {
                $supplierId = $product['supplier_id'];
                if (!isset($productsBySupplier[$supplierId])) {
                    $productsBySupplier[$supplierId] = [];
                }
                $productsBySupplier[$supplierId][] = $product;
            }

            // Get business delivery address
            $stmt = $this->db->prepare("
                SELECT delivery_street, delivery_city, delivery_province, delivery_postal_code, delivery_country
                FROM business_profiles
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['business']['id']]);
            $businessAddress = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('distribution.requests.edit', [
                'request'           => $request,
                'suppliers'         => $suppliers,
                'productsBySupplier'=> $productsBySupplier,
                'catalogItemsKeyed' => $catalogItemsKeyed,
                'shoppingItems'     => $shoppingItems,
                'businessAddress'   => $businessAddress,
                'errors'            => $_SESSION['request_errors'] ?? [],
                'business'          => $this->getBusinessData(),
                'pageTitle'         => ((($_SESSION['language'] ?? 'fr') === 'fr') ? 'Modifier la demande #' : 'Edit Request #') . ($request['request_number'] ?? ''),
                'currentPage'       => 'requests',
            ]);

            unset($_SESSION['request_errors']);

        } catch (\PDOException $e) {
            error_log('Distribution request edit error: ' . $e->getMessage());
            setFlash('error', 'Error loading request.');
            redirect('distribution/requests');
        }
    }

    /**
     * Update request
     */
    public function update(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/requests');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/requests');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        // Verify ownership and draft status
        $stmt = $this->db->prepare("
            SELECT id FROM distribution_requests
            WHERE id = ? AND business_profile_id = ? AND status = 'draft'
        ");
        $stmt->execute([$requestId, $businessId]);
        if (!$stmt->fetch()) {
            setFlash('error', 'Request not found or cannot be edited.');
            redirect('distribution/requests');
            return;
        }

        $data = [
            'request_name' => sanitize($_POST['request_name'] ?? ''),
            'notes' => sanitize($_POST['notes'] ?? ''),
            'delivery_street' => sanitize($_POST['delivery_street'] ?? ''),
            'delivery_city' => sanitize($_POST['delivery_city'] ?? ''),
            'delivery_province' => sanitize($_POST['delivery_province'] ?? ''),
            'delivery_postal_code' => strtoupper(sanitize($_POST['delivery_postal_code'] ?? '')),
            'preferred_delivery_date' => sanitize($_POST['preferred_delivery_date'] ?? ''),
            'delivery_distance' => (float)($_POST['delivery_distance'] ?? 0),
            'catalog_items' => $_POST['catalog_items'] ?? [],
            'shopping_items' => $_POST['shopping_items'] ?? [],
            'tip_percentage' => (int)($_POST['tip_percentage'] ?? 0),
            'tip_custom_amount' => (float)($_POST['tip_custom_amount'] ?? 0)
        ];

        $errors = $this->validateRequest($data);

        if (!empty($errors)) {
            $_SESSION['request_errors'] = $errors;
            redirect('distribution/requests/edit?id=' . $requestId);
            return;
        }

        try {
            $this->db->beginTransaction();

            // Pre-calculate items total and total weight from supplier products
            $itemsTotal = 0;
            $totalWeightKg = 0;
            if (!empty($data['catalog_items'])) {
                foreach ($data['catalog_items'] as $supplierProductId => $qty) {
                    $quantity = (int)$qty;
                    if ($quantity > 0) {
                        $priceStmt = $this->db->prepare("
                            SELECT unit_price, weight_kg FROM supplier_products
                            WHERE id = ? AND is_available = 1 LIMIT 1
                        ");
                        $priceStmt->execute([$supplierProductId]);
                        $priceRow = $priceStmt->fetch(\PDO::FETCH_ASSOC);
                        if ($priceRow) {
                            $itemsTotal += $quantity * $priceRow['unit_price'];
                            $totalWeightKg += $quantity * ($priceRow['weight_kg'] ?? 0);
                        }
                    }
                }
            }

            // Calculate tip (based on pre-tax items total per Canadian law)
            $tipPercentage = (int)($data['tip_percentage'] ?? 0);
            $tipCustomAmount = (float)($data['tip_custom_amount'] ?? 0);
            $tipAmount = 0;
            if (in_array($tipPercentage, [15, 18, 20])) {
                $tipAmount = round($itemsTotal * ($tipPercentage / 100), 2);
            } elseif ($tipCustomAmount > 0) {
                $tipAmount = min(round($tipCustomAmount, 2), $itemsTotal);
                $tipPercentage = 0;
            } else {
                $tipPercentage = 0;
            }

            // Calculate full summary breakdown with weight and tip
            $summary = $this->calculateSummary($itemsTotal, $data['delivery_distance'], $totalWeightKg, $tipAmount);

            // Update request with summary data
            $stmt = $this->db->prepare("
                UPDATE distribution_requests SET
                    request_name = ?,
                    notes = ?,
                    delivery_street = ?,
                    delivery_city = ?,
                    delivery_province = ?,
                    delivery_postal_code = ?,
                    preferred_delivery_date = ?,
                    delivery_distance = ?,
                    tier = ?,
                    items_total = ?,
                    service_fee = ?,
                    handling_fee = ?,
                    total_weight_kg = ?,
                    delivery_fee = ?,
                    tip_amount = ?,
                    tip_percentage = ?,
                    subtotal = ?,
                    gst_amount = ?,
                    qst_amount = ?,
                    tax_amount = ?,
                    total_amount = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $data['request_name'],
                $data['notes'],
                $data['delivery_street'],
                $data['delivery_city'],
                $data['delivery_province'],
                $data['delivery_postal_code'],
                $data['preferred_delivery_date'] ?: null,
                $data['delivery_distance'],
                $summary['tier'],
                $summary['items_total'],
                $summary['service_fee'],
                $summary['handling_fee'],
                $summary['total_weight_kg'],
                $summary['delivery_fee'],
                $summary['tip_amount'],
                $tipPercentage,
                $summary['subtotal'],
                $summary['gst_amount'],
                $summary['qst_amount'],
                $summary['tax_amount'],
                $summary['total_amount'],
                $requestId
            ]);

            // Delete existing items
            $this->db->prepare("DELETE FROM distribution_request_items WHERE distribution_request_id = ?")->execute([$requestId]);
            $this->db->prepare("DELETE FROM distribution_shopping_items WHERE distribution_request_id = ?")->execute([$requestId]);

            // Re-add catalog items (from supplier products)
            if (!empty($data['catalog_items'])) {
                $itemStmt = $this->db->prepare("
                    INSERT INTO distribution_request_items
                    (distribution_request_id, product_id, product_name, product_sku, product_image, quantity, unit_price, subtotal, created_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
                ");

                foreach ($data['catalog_items'] as $supplierProductId => $qty) {
                    $quantity = (int)$qty;
                    if ($quantity > 0) {
                        // Get supplier product details
                        $productStmt = $this->db->prepare("
                            SELECT sp.id, sp.product_name, sp.sku, sp.unit_price, sp.image
                            FROM supplier_products sp
                            INNER JOIN suppliers s ON sp.supplier_id = s.id AND s.status = 'active'
                            WHERE sp.id = ? AND sp.is_available = 1
                            LIMIT 1
                        ");
                        $productStmt->execute([$supplierProductId]);
                        $product = $productStmt->fetch(\PDO::FETCH_ASSOC);

                        if ($product) {
                            $subtotal = $quantity * $product['unit_price'];
                            $itemStmt->execute([
                                $requestId,
                                $supplierProductId,
                                $product['product_name'],
                                $product['sku'],
                                $product['image'],
                                $quantity,
                                $product['unit_price'],
                                $subtotal
                            ]);
                        }
                    }
                }
            }

            // Re-add shopping items
            if (!empty($data['shopping_items'])) {
                $shoppingStmt = $this->db->prepare("
                    INSERT INTO distribution_shopping_items
                    (distribution_request_id, item_description, quantity, estimated_price, admin_notes, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");

                foreach ($data['shopping_items'] as $item) {
                    if (!empty($item['description'])) {
                        $shoppingStmt->execute([
                            $requestId,
                            sanitize($item['description']),
                            $item['quantity'] ?? '1',
                            !empty($item['estimated_price']) ? (float)$item['estimated_price'] : null,
                            sanitize($item['notes'] ?? '')
                        ]);
                    }
                }
            }

            $this->db->commit();

            setFlash('success', 'Request updated successfully.');
            redirect('distribution/requests/show?id=' . $requestId);

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Distribution request update error: ' . $e->getMessage());
            setFlash('error', 'Error updating request.');
            redirect('distribution/requests/edit?id=' . $requestId);
        }
    }

    /**
     * Submit request for fulfillment
     */
    public function submit(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/requests');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/requests');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get request and verify (join user email/name for downstream emails)
            $stmt = $this->db->prepare("
                SELECT dr.*, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.business_profile_id = ? AND dr.status = 'draft'
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or already submitted.');
                redirect('distribution/requests');
                return;
            }

            // Check if request has items
            $stmt = $this->db->prepare("
                SELECT
                    (SELECT COUNT(*) FROM distribution_request_items WHERE distribution_request_id = ?) +
                    (SELECT COUNT(*) FROM distribution_shopping_items WHERE distribution_request_id = ?) as total_items
            ");
            $stmt->execute([$requestId, $requestId]);
            $itemCount = $stmt->fetch(\PDO::FETCH_ASSOC)['total_items'];

            if ($itemCount == 0) {
                setFlash('error', 'Cannot submit an empty request. Please add items first.');
                redirect('distribution/requests/show?id=' . $requestId);
                return;
            }

            // Auto-approve and dispatch directly to suppliers — no manual admin step needed
            $deliveryType = $request['delivery_type'] ?? 'scheduled';

            // Calculate order deadline (delivery promise clock starts at submission)
            if ($deliveryType === 'express') {
                // ASAP: must be delivered within 2 hours of submission
                $orderDeadline = date('Y-m-d H:i:s', strtotime('+2 hours'));
            } elseif ($deliveryType === 'same_day') {
                // Same Day: deadline is end of chosen delivery window today
                $windowEnd = $request['scheduled_time_to'] ?? '17:00:00';
                $orderDeadline = date('Y-m-d') . ' ' . $windowEnd;
            } else {
                // Scheduled: deadline is end of chosen delivery window on chosen date
                $schedDate  = $request['scheduled_date'] ?? date('Y-m-d', strtotime('+1 day'));
                $windowEnd  = $request['scheduled_time_to'] ?? '17:00:00';
                $orderDeadline = $schedDate . ' ' . $windowEnd;
            }

            $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'approved', submitted_at = NOW(), approved_at = NOW(),
                    order_deadline = ?, updated_at = NOW()
                WHERE id = ?
            ")->execute([$orderDeadline, $requestId]);

            $this->logStatusChange($requestId, 'draft', 'approved', 'Request submitted and auto-dispatched to suppliers');

            // Auto-create POs and notify each supplier
            require_once BASE_PATH . '/app/Controllers/AdminDistributionController.php';
            $adminCtrl = new \App\Controllers\AdminDistributionController();
            $poCount   = $adminCtrl->autoCreatePurchaseOrders($requestId, $request['request_number'], $deliveryType);

            // Email business: suppliers are now confirming
            $adminCtrl->sendAwaitingSupplierEmail($request);

            // Admin bell + business bell
            try {
                $businessName = $_SESSION['business']['company_name'] ?? 'a business';
                \App\Helpers\NotificationHelper::add(
                    'distribution_request',
                    'Distribution Request Auto-Dispatched',
                    "Request #{$request['request_number']} from {$businessName} — {$poCount} PO(s) sent to suppliers automatically.",
                    ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'truck']
                );
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    (int)$request['business_profile_id'],
                    'distribution_request',
                    '✅ Request Submitted — #' . $request['request_number'],
                    'Your distribution request has been submitted and suppliers have been notified. You will be updated once they confirm availability.',
                    'distribution/requests/show?id=' . $requestId
                );
            } catch (\Exception $e) {
                logger('Distribution submit notification failed: ' . $e->getMessage(), 'warning');
            }

            setFlash('success', 'Request submitted! Suppliers have been notified and are confirming availability.');
            redirect('distribution/requests/show?id=' . $requestId);

        } catch (\PDOException $e) {
            error_log('Distribution request submit error: ' . $e->getMessage());
            setFlash('error', 'Error submitting request.');
            redirect('distribution/requests/show?id=' . $requestId);
        }
    }

    /**
     * Send submission confirmation email to business
     */
    private function sendSubmissionConfirmationEmail(array $request): void
    {
        try {
            $email     = $_SESSION['user']['email'] ?? '';
            $firstName = $_SESSION['user']['first_name'] ?? 'there';

            if (!$email) return;

            $subject = "We Received Your Request #{$request['request_number']} — OCS Distribution";

            $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #00b207; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>Request Received!</h1>
                </div>
                <div style='padding: 30px; background: #f8f9fa;'>
                    <p>Hi {$firstName},</p>
                    <p>We've received your distribution request <strong>#{$request['request_number']}</strong> and our team is reviewing it now.</p>

                    <div style='background: white; border-radius: 8px; padding: 20px; margin: 20px 0;'>
                        <h3 style='margin-top: 0; color: #333;'>What happens next?</h3>
                        <ol style='margin: 0; padding-left: 20px; color: #555; line-height: 1.8;'>
                            <li>Our team reviews your items and confirms availability</li>
                            <li>We send you a quote/invoice to review</li>
                            <li>You complete payment securely online</li>
                            <li>We procure your items and arrange delivery</li>
                        </ol>
                    </div>

                    <p>You can track the status of your request at any time by logging in to your account.</p>

                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='" . url('distribution/requests/show?id=' . $request['id']) . "' style='display: inline-block; background: #00b207; color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;'>
                            View Your Request
                        </a>
                    </div>

                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='color: #888; font-size: 12px;'>
                        OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a>
                    </p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($email, $subject, $body);
        } catch (\Exception $e) {
            error_log('Send submission confirmation email error: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a request
     */
    public function cancel(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/requests');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/requests');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];
        $reason = sanitize($_POST['cancel_reason'] ?? '');

        try {
            // Get request - can cancel draft, submitted, pending, quoted statuses
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_requests
                WHERE id = ? AND business_profile_id = ? AND status IN ('draft', 'submitted', 'pending', 'quoted', 'approved', 'pending_payment')
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or cannot be cancelled.');
                redirect('distribution/dashboard');
                return;
            }

            $oldStatus = $request['status'];

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'cancelled',
                    cancelled_at = NOW(),
                    cancelled_by = 'customer',
                    cancellation_reason = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reason ?: null, $requestId]);

            $this->logStatusChange($requestId, $oldStatus, 'cancelled', 'Cancelled by business: ' . ($reason ?: 'No reason provided'));

            // Admin bell + email: business cancelled a request
            try {
                $businessName = $_SESSION['business']['company_name'] ?? 'a business';
                $adminLink    = '/admin/distribution/view?id=' . $requestId;

                \App\Helpers\NotificationHelper::add(
                    'distribution_request',
                    'Distribution Request Cancelled by Customer',
                    "Request #{$request['request_number']} cancelled by {$businessName}" . ($reason ? ": {$reason}" : '.'),
                    ['link' => $adminLink, 'icon' => 'truck']
                );

                $mailConfig = require dirname(__DIR__, 2) . '/config/mail.php';
                $adminEmail = $mailConfig['admin_email'] ?? 'info@ocsapp.ca';

                \App\Helpers\EmailHelper::setNextMeta('distribution_cancellation', 'distribution_request', $requestId);
                \App\Helpers\EmailHelper::sendTemplate(
                    $adminEmail,
                    'planner-notification',
                    [
                        'user_first_name'      => 'Admin',
                        'notification_title'   => 'Distribution Request Cancelled by Customer',
                        'notification_message' => "Request #{$request['request_number']} has been cancelled by {$businessName}." . ($reason ? " Reason: {$reason}" : ''),
                        'action_url'           => 'https://ocsapp.ca' . $adminLink,
                        'current_year'         => date('Y'),
                    ]
                );
            } catch (\Exception $e) {
                logger('Distribution cancel notification failed: ' . $e->getMessage(), 'warning');
            }

            setFlash('success', 'Request cancelled successfully.');
            redirect('distribution/dashboard');

        } catch (\PDOException $e) {
            error_log('Distribution request cancel error: ' . $e->getMessage());
            setFlash('error', 'Error cancelling request.');
            redirect('distribution/dashboard');
        }
    }

    /**
     * Delete a draft request (permanent deletion)
     */
    public function delete(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/requests');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/requests');
            return;
        }

        $requestId = (int)($_POST['distribution_request_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Only drafts can be deleted - submitted requests should be cancelled
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_requests
                WHERE id = ? AND business_profile_id = ? AND status = 'draft'
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found or cannot be deleted. Only draft requests can be deleted.');
                redirect('distribution/requests');
                return;
            }

            $this->db->beginTransaction();

            // Delete related items first
            $this->db->prepare("DELETE FROM distribution_request_items WHERE distribution_request_id = ?")->execute([$requestId]);
            $this->db->prepare("DELETE FROM distribution_shopping_items WHERE distribution_request_id = ?")->execute([$requestId]);
            $this->db->prepare("DELETE FROM distribution_status_history WHERE distribution_request_id = ?")->execute([$requestId]);

            // Delete the request
            $this->db->prepare("DELETE FROM distribution_requests WHERE id = ?")->execute([$requestId]);

            $this->db->commit();

            setFlash('success', 'Draft request deleted successfully.');
            redirect('distribution/requests');

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Distribution request delete error: ' . $e->getMessage());
            setFlash('error', 'Error deleting request.');
            redirect('distribution/requests');
        }
    }

    /**
     * Validate request data
     */
    private function validateRequest(array $data): array
    {
        $errors = [];

        if (empty($data['request_name'])) {
            $errors['request_name'] = 'Request name is required.';
        }

        if (empty($data['delivery_street'])) {
            $errors['delivery_street'] = 'Delivery address is required.';
        }

        if (empty($data['delivery_city'])) {
            $errors['delivery_city'] = 'City is required.';
        }

        if (empty($data['delivery_province'])) {
            $errors['delivery_province'] = 'Province is required.';
        }

        if (empty($data['delivery_postal_code'])) {
            $errors['delivery_postal_code'] = 'Postal code is required.';
        } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/i', $data['delivery_postal_code'])) {
            $errors['delivery_postal_code'] = 'Please enter a valid Canadian postal code.';
        }

        // Check at least one item
        $hasItems = false;
        if (!empty($data['catalog_items'])) {
            foreach ($data['catalog_items'] as $qty) {
                if ((int)$qty > 0) {
                    $hasItems = true;
                    break;
                }
            }
        }
        if (!$hasItems && !empty($data['shopping_items'])) {
            foreach ($data['shopping_items'] as $item) {
                if (!empty($item['description'])) {
                    $hasItems = true;
                    break;
                }
            }
        }

        if (!$hasItems) {
            $errors['items'] = 'Please add at least one item to your request.';
        }

        // Delivery type: scheduled date must be tomorrow to +7 days
        if (($data['delivery_type'] ?? '') === 'scheduled') {
            $scheduledDate = $data['scheduled_date'] ?? '';
            if (empty($scheduledDate)) {
                $errors['scheduled_date'] = 'Please select a delivery date.';
            } else {
                $minDate = date('Y-m-d', strtotime('+1 day'));
                $maxDate = date('Y-m-d', strtotime('+7 days'));
                if ($scheduledDate < $minDate) {
                    $errors['scheduled_date'] = 'Scheduled delivery must be at least tomorrow.';
                } elseif ($scheduledDate > $maxDate) {
                    $errors['scheduled_date'] = 'Scheduled delivery cannot be more than 7 days from today.';
                }
            }
        }

        return $errors;
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $requestId, ?string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, ?, ?, 'business', ?, ?, NOW())
            ");
            $stmt->execute([
                $requestId,
                $fromStatus,
                $toStatus,
                $_SESSION['business']['id'] ?? null,
                $notes
            ]);
        } catch (\PDOException $e) {
            error_log('Status history log error: ' . $e->getMessage());
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

    /**
     * Business confirms the supplier switch + updated price → proceed.
     */
    public function confirmPriceChange(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request.');
            back();
            return;
        }

        $requestId  = (int)post('distribution_request_id');
        $businessId = $_SESSION['business']['id'];

        try {
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON u.id = (
                    SELECT user_id FROM business_profiles WHERE id = dr.business_profile_id LIMIT 1
                )
                WHERE dr.id = ? AND dr.business_profile_id = ? LIMIT 1
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found.');
                redirect('distribution/requests');
                return;
            }

            if (!$request['supplier_switch_pending']) {
                setFlash('info', 'No pending supplier switch to confirm.');
                redirect('distribution/requests/show?id=' . $requestId);
                return;
            }

            // Check if deadline has passed
            if ($request['supplier_switch_deadline'] && strtotime($request['supplier_switch_deadline']) < time()) {
                // Auto-cancel since window passed
                $this->db->prepare("
                    UPDATE distribution_requests SET
                        status = 'cancelled',
                        supplier_switch_pending = 0,
                        cancelled_at = NOW(),
                        updated_at = NOW()
                    WHERE id = ?
                ")->execute([$requestId]);
                $this->logStatusChange($requestId, $request['status'], 'cancelled', 'Supplier switch confirmation window expired — auto-cancelled.');
                setFlash('error', 'The confirmation window has expired. This request has been cancelled.');
                redirect('distribution/requests/show?id=' . $requestId);
                return;
            }

            // Update total to new amount and clear pending flag
            $newTotal = (float)$request['supplier_switch_new_amount'];
            $this->db->prepare("
                UPDATE distribution_requests SET
                    total_amount             = ?,
                    supplier_switch_pending  = 0,
                    supplier_switch_deadline = NULL,
                    updated_at               = NOW()
                WHERE id = ?
            ")->execute([$newTotal, $requestId]);

            $this->logStatusChange($requestId, null, $request['status'], 'Business confirmed supplier switch. Updated total: $' . number_format($newTotal, 2));

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "Business Confirmed Supplier Switch — #{$request['request_number']}",
                "{$request['company_name']} confirmed the price change for request #{$request['request_number']}. New total: $" . number_format($newTotal, 2) . '.',
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'check-circle', 'priority' => 'normal']
            );

            setFlash('success', 'Thank you! The updated pricing has been confirmed. Suppliers will now proceed.');
            redirect('distribution/requests/show?id=' . $requestId);

        } catch (\Exception $e) {
            logger("confirmPriceChange error: " . $e->getMessage(), 'error');
            setFlash('error', 'Something went wrong. Please try again.');
            redirect('distribution/requests/show?id=' . $requestId);
        }
    }

    /**
     * Business declines the supplier switch → cancel the distribution request.
     */
    public function declinePriceChange(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request.');
            back();
            return;
        }

        $requestId  = (int)post('distribution_request_id');
        $businessId = $_SESSION['business']['id'];

        try {
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON u.id = (
                    SELECT user_id FROM business_profiles WHERE id = dr.business_profile_id LIMIT 1
                )
                WHERE dr.id = ? AND dr.business_profile_id = ? LIMIT 1
            ");
            $stmt->execute([$requestId, $businessId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$request) {
                setFlash('error', 'Request not found.');
                redirect('distribution/requests');
                return;
            }

            // Cancel the distribution request and all open POs
            $this->db->prepare("
                UPDATE distribution_requests SET
                    status = 'cancelled',
                    supplier_switch_pending = 0,
                    supplier_switch_deadline = NULL,
                    cancelled_at = NOW(),
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$requestId]);

            // Cancel any pending POs for this distribution request
            $this->db->prepare("
                UPDATE purchase_orders SET
                    status = 'cancelled',
                    updated_at = NOW()
                WHERE distribution_request_id = ? AND status IN ('sent','accepted','preparing')
            ")->execute([$requestId]);

            $this->logStatusChange($requestId, $request['status'], 'cancelled', 'Business declined supplier switch — request cancelled by customer.');

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "Business Declined Supplier Switch — #{$request['request_number']}",
                "{$request['company_name']} declined the price change for request #{$request['request_number']}. Request cancelled.",
                ['link' => '/admin/distribution/view?id=' . $requestId, 'icon' => 'times-circle', 'priority' => 'high']
            );

            // Confirmation email to business
            $subject = "Your Distribution Request #{$request['request_number']} Has Been Cancelled";
            $body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#ef4444;padding:20px;text-align:center;'>
                    <h1 style='color:white;margin:0;'>Request Cancelled</h1>
                </div>
                <div style='padding:30px;background:#f8f9fa;'>
                    <p>Hi {$request['first_name']},</p>
                    <p>As requested, distribution request <strong>#{$request['request_number']}</strong> has been cancelled. No charges have been made.</p>
                    <p>If you'd like to place a new request, you can do so anytime from your portal.</p>
                    <p>If you have any questions, please don't hesitate to contact us.</p>
                    <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0;'>
                    <p style='color:#9ca3af;font-size:12px;'>OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a></p>
                </div>
            </div>";
            \App\Helpers\EmailHelper::send($request['email'], $subject, $body);

            setFlash('success', 'Your request has been cancelled. No charges have been made.');
            redirect('distribution/requests');

        } catch (\Exception $e) {
            logger("declinePriceChange error: " . $e->getMessage(), 'error');
            setFlash('error', 'Something went wrong. Please try again.');
            redirect('distribution/requests/show?id=' . $requestId);
        }
    }
}
