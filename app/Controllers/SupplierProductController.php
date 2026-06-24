<?php

namespace App\Controllers;

class SupplierProductController {

    private function checkAuth() {
        if (!isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/login'));
            exit;
        }
        return $_SESSION['supplier_id'];
    }

    public function index(): void {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();
            $search = get('search', '');
            $available = get('available', '');

            $where = ['supplier_id = ?'];
            $params = [$supplierId];

            if ($search) {
                $where[] = "(product_name LIKE ? OR sku LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($available !== '') {
                $where[] = "is_available = ?";
                $params[] = (int)$available;
            }

            $whereClause = implode(' AND ', $where);

            // Count total
            $countStmt = $db->prepare("SELECT COUNT(*) FROM supplier_products WHERE {$whereClause}");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $perPage = 20;
            $page = max(1, (int)get('page', 1));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, max(1, $totalPages));
            $offset = ($page - 1) * $perPage;

            $stmt = $db->prepare("
                SELECT * FROM supplier_products
                WHERE {$whereClause}
                ORDER BY product_name ASC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            view('supplier.products.index', [
                'products' => $products,
                'search' => $search,
                'available' => $available,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'pageTitle' => 'My Products'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier products list error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading products');
            back();
        }
    }

    public function create(): void {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Get marketplace products this supplier has previously supplied
            $stmt = $db->prepare("
                SELECT DISTINCT p.id, p.name, p.sku
                FROM products p
                INNER JOIN supplier_products sp ON sp.marketplace_product_id = p.id
                WHERE sp.supplier_id = ? AND p.status = 'active'
                ORDER BY p.name ASC
            ");
            $stmt->execute([$supplierId]);
            $marketplaceProducts = $stmt->fetchAll();

            view('supplier.products.create', [
                'pageTitle' => 'Add New Product',
                'marketplaceProducts' => $marketplaceProducts
            ]);
        } catch (\PDOException $e) {
            logger("Supplier product create page error: " . $e->getMessage(), 'error');
            view('supplier.products.create', [
                'pageTitle' => 'Add New Product',
                'marketplaceProducts' => []
            ]);
        }
    }

    public function store(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();

            $marketplaceProductId = post('marketplace_product_id');
            $marketplaceProductId = $marketplaceProductId ? (int)$marketplaceProductId : null;

            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/supplier-products/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    // Check file size (max 5MB)
                    if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                        $fileName = 'supplier_' . $supplierId . '_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                            $imagePath = 'uploads/supplier-products/' . $fileName;
                        }
                    } else {
                        setFlash('error', 'Image file too large (max 5MB)');
                        back();
                        return;
                    }
                } else {
                    setFlash('error', 'Invalid image format. Only JPG, PNG, GIF, and WebP are allowed');
                    back();
                    return;
                }
            }

            $stockQuantity = post('stock_quantity');
            $stockQuantity = ($stockQuantity !== null && $stockQuantity !== '') ? (int)$stockQuantity : null;

            $stmt = $db->prepare("
                INSERT INTO supplier_products (
                    supplier_id, marketplace_product_id, product_name, image, sku, description, unit_price,
                    weight_kg, unit, minimum_order_quantity, stock_quantity, lead_time_days, is_available, notes
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $supplierId,
                $marketplaceProductId,
                post('product_name'),
                $imagePath,
                post('sku'),
                post('description'),
                post('unit_price'),
                post('weight_kg') ?: null,
                post('unit', 'unit'),
                post('minimum_order_quantity', 1),
                $stockQuantity,
                post('lead_time_days', 7),
                post('is_available', 1),
                post('notes')
            ]);

            setFlash('success', 'Product added successfully');
            redirect(url('supplier/products'));

        } catch (\PDOException $e) {
            logger("Supplier product create error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error adding product');
            back();
        }
    }

    public function edit(): void {
        $supplierId = $this->checkAuth();

        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid product ID');
                redirect(url('supplier/products'));
            }

            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT * FROM supplier_products
                WHERE id = ? AND supplier_id = ?
            ");
            $stmt->execute([$id, $supplierId]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('supplier/products'));
            }

            // Get marketplace products this supplier has previously supplied
            $stmt = $db->prepare("
                SELECT DISTINCT p.id, p.name, p.sku
                FROM products p
                INNER JOIN supplier_products sp ON sp.marketplace_product_id = p.id
                WHERE sp.supplier_id = ? AND p.status = 'active'
                ORDER BY p.name ASC
            ");
            $stmt->execute([$supplierId]);
            $marketplaceProducts = $stmt->fetchAll();

            view('supplier.products.edit', [
                'product' => $product,
                'marketplaceProducts' => $marketplaceProducts,
                'pageTitle' => 'Edit Product'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier product edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading product');
            redirect(url('supplier/products'));
        }
    }

    public function update(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                throw new \Exception('Invalid product ID');
            }

            $db = \Database::getConnection();

            // Get current product to check for existing image
            $stmt = $db->prepare("
                SELECT image FROM supplier_products
                WHERE id = ? AND supplier_id = ?
            ");
            $stmt->execute([$id, $supplierId]);
            $currentProduct = $stmt->fetch();

            if (!$currentProduct) {
                throw new \Exception('Product not found');
            }

            $marketplaceProductId = post('marketplace_product_id');
            $marketplaceProductId = $marketplaceProductId ? (int)$marketplaceProductId : null;

            // Handle image upload
            $imagePath = $currentProduct['image']; // Keep current image by default
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/supplier-products/';

                // Create directory if it doesn't exist
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }

                $fileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    // Check file size (max 5MB)
                    if ($_FILES['image']['size'] <= 5 * 1024 * 1024) {
                        $fileName = 'supplier_' . $supplierId . '_' . uniqid() . '.' . $fileExtension;
                        $uploadPath = $uploadDir . $fileName;

                        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                            // Delete old image file if exists
                            if (!empty($currentProduct['image'])) {
                                $oldImagePath = __DIR__ . '/../../public/' . $currentProduct['image'];
                                if (file_exists($oldImagePath)) {
                                    unlink($oldImagePath);
                                }
                            }
                            $imagePath = 'uploads/supplier-products/' . $fileName;
                        }
                    } else {
                        setFlash('error', 'Image file too large (max 5MB)');
                        back();
                        return;
                    }
                } else {
                    setFlash('error', 'Invalid image format. Only JPG, PNG, GIF, and WebP are allowed');
                    back();
                    return;
                }
            }

            $stockQuantity = post('stock_quantity');
            $stockQuantity = ($stockQuantity !== null && $stockQuantity !== '') ? (int)$stockQuantity : null;

            $stmt = $db->prepare("
                UPDATE supplier_products SET
                    marketplace_product_id = ?,
                    product_name = ?,
                    image = ?,
                    sku = ?,
                    description = ?,
                    unit_price = ?,
                    weight_kg = ?,
                    unit = ?,
                    minimum_order_quantity = ?,
                    stock_quantity = ?,
                    lead_time_days = ?,
                    is_available = ?,
                    notes = ?
                WHERE id = ? AND supplier_id = ?
            ");

            $stmt->execute([
                $marketplaceProductId,
                post('product_name'),
                $imagePath,
                post('sku'),
                post('description'),
                post('unit_price'),
                post('weight_kg') ?: null,
                post('unit', 'unit'),
                post('minimum_order_quantity', 1),
                $stockQuantity,
                post('lead_time_days', 7),
                post('is_available', 1),
                post('notes'),
                $id,
                $supplierId
            ]);

            setFlash('success', 'Product updated successfully');
            redirect(url('supplier/products'));

        } catch (\Exception $e) {
            logger("Supplier product update error: " . $e->getMessage(), 'error');
            setFlash('error', $e->getMessage());
            back();
        }
    }

    public function delete(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid product ID'], 400);
            }

            $db = \Database::getConnection();

            // Check if product is used in any purchase orders
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM purchase_order_items poi
                JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE sp.id = ?
            ");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'] ?? 0;

            if ($count > 0) {
                jsonResponse(['success' => false, 'message' => 'Cannot delete product that has been ordered'], 400);
            }

            $stmt = $db->prepare("
                DELETE FROM supplier_products
                WHERE id = ? AND supplier_id = ?
            ");
            $stmt->execute([$id, $supplierId]);

            jsonResponse(['success' => true, 'message' => 'Product deleted successfully']);

        } catch (\PDOException $e) {
            logger("Supplier product delete error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error deleting product'], 500);
        }
    }

    public function analytics(): void {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Get date range from query params (default: last 30 days)
            $period = get('period', '30');
            $dateFrom = get('date_from', '');
            $dateTo = get('date_to', '');

            if ($period === 'custom' && $dateFrom) {
                $startDate = date('Y-m-d', strtotime($dateFrom));
                $endDate = $dateTo ? date('Y-m-d', strtotime($dateTo)) : date('Y-m-d');
            } elseif ($period === 'all') {
                $startDate = '2000-01-01';
                $endDate = date('Y-m-d');
            } elseif ($period === 'year') {
                $startDate = date('Y-01-01');
                $endDate = date('Y-m-d');
            } else {
                $days = (int)$period ?: 30;
                $startDate = date('Y-m-d', strtotime("-{$days} days"));
                $endDate = date('Y-m-d');
            }

            // Order Statistics (exclude drafts)
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status IN ('accepted','preparing','ready_for_pickup','picked_up') THEN 1 ELSE 0 END) as receiving_orders,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as avg_order_value
                FROM purchase_orders
                WHERE supplier_id = ? AND status != 'draft' AND created_at >= ? AND created_at <= ?
            ");
            $stmt->execute([$supplierId, $startDate, $endDate . ' 23:59:59']);
            $orderStats = $stmt->fetch();

            // Orders by Month (within selected range, exclude drafts)
            $stmt = $db->prepare("
                SELECT
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as order_count,
                    SUM(total_amount) as revenue
                FROM purchase_orders
                WHERE supplier_id = ? AND status != 'draft' AND created_at >= ? AND created_at <= ?
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            $stmt->execute([$supplierId, $startDate, $endDate . ' 23:59:59']);
            $monthlyOrders = $stmt->fetchAll();

            // Top Products (by quantity ordered, exclude drafts)
            $stmt = $db->prepare("
                SELECT
                    sp.product_name,
                    sp.sku,
                    SUM(poi.quantity_ordered) as total_quantity,
                    SUM(poi.total_cost) as total_revenue,
                    COUNT(DISTINCT poi.purchase_order_id) as order_count
                FROM purchase_order_items poi
                JOIN supplier_products sp ON poi.product_id = sp.id
                JOIN purchase_orders po ON poi.purchase_order_id = po.id
                WHERE sp.supplier_id = ? AND po.status != 'draft' AND po.created_at >= ? AND po.created_at <= ?
                GROUP BY sp.id, sp.product_name, sp.sku
                ORDER BY total_quantity DESC
                LIMIT 10
            ");
            $stmt->execute([$supplierId, $startDate, $endDate . ' 23:59:59']);
            $topProducts = $stmt->fetchAll();

            // Acceptance Rate (exclude drafts)
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) as total_received,
                    SUM(CASE WHEN status IN ('accepted','preparing','ready_for_pickup','picked_up','completed') THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN status = 'cancelled' AND decline_reason IS NOT NULL THEN 1 ELSE 0 END) as declined
                FROM purchase_orders
                WHERE supplier_id = ? AND status != 'draft' AND created_at >= ? AND created_at <= ?
            ");
            $stmt->execute([$supplierId, $startDate, $endDate . ' 23:59:59']);
            $acceptanceStats = $stmt->fetch();

            // Recent Activity (exclude drafts)
            $stmt = $db->prepare("
                SELECT po.*,
                       COUNT(poi.id) as item_count
                FROM purchase_orders po
                LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id
                WHERE po.supplier_id = ? AND po.status != 'draft'
                GROUP BY po.id
                ORDER BY po.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$supplierId]);
            $recentOrders = $stmt->fetchAll();

            // Product Performance
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) as total_products,
                    SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available_products,
                    SUM(CASE WHEN is_available = 0 THEN 1 ELSE 0 END) as unavailable_products
                FROM supplier_products
                WHERE supplier_id = ?
            ");
            $stmt->execute([$supplierId]);
            $productStats = $stmt->fetch();

            view('supplier.analytics', [
                'orderStats' => $orderStats,
                'monthlyOrders' => $monthlyOrders,
                'topProducts' => $topProducts,
                'acceptanceStats' => $acceptanceStats,
                'recentOrders' => $recentOrders,
                'productStats' => $productStats,
                'period' => $period,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
                'pageTitle' => 'Analytics'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier analytics error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading analytics');
            redirect(url('supplier/dashboard'));
        }
    }

    public function orders(): void {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();
            $status = get('status', '');

            $where = ['po.supplier_id = ?', "po.status != 'draft'"];
            $params = [$supplierId];

            if ($status) {
                $where[] = "po.status = ?";
                $params[] = $status;
            }

            $whereClause = implode(' AND ', $where);

            // Count total
            $countStmt = $db->prepare("SELECT COUNT(*) FROM purchase_orders po WHERE {$whereClause}");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $perPage = 20;
            $page = max(1, (int)get('page', 1));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, max(1, $totalPages));
            $offset = ($page - 1) * $perPage;

            $stmt = $db->prepare("
                SELECT po.*,
                       COUNT(poi.id) as item_count,
                       SUM(poi.quantity_ordered) as total_items
                FROM purchase_orders po
                LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id
                WHERE {$whereClause}
                GROUP BY po.id
                ORDER BY po.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($params);
            $orders = $stmt->fetchAll();

            view('supplier.orders.index', [
                'orders' => $orders,
                'status' => $status,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'pageTitle' => 'Purchase Orders'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier orders list error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading orders');
            back();
        }
    }

    public function viewOrder(): void {
        $supplierId = $this->checkAuth();

        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid order ID');
                redirect(url('supplier/orders'));
            }

            $db = \Database::getConnection();

            // Get purchase order (with assigned driver name if applicable)
            $stmt = $db->prepare("
                SELECT po.*,
                       CONCAT(u.first_name, ' ', u.last_name) AS driver_name,
                       u.phone AS driver_phone,
                       dr.payment_status AS dr_payment_status,
                       dr.request_number AS dr_request_number,
                       dr.order_deadline,
                       dr.delivery_type,
                       dr.submitted_at AS dr_submitted_at,
                       dr.delivery_distance,
                       dr.delivery_street,
                       dr.delivery_city,
                       dr.delivery_province,
                       dr.delivery_postal_code
                FROM purchase_orders po
                LEFT JOIN users u ON u.id = po.assigned_driver_id
                LEFT JOIN distribution_requests dr ON po.distribution_request_id = dr.id
                WHERE po.id = ? AND po.supplier_id = ? AND po.status != 'draft'
            ");
            $stmt->execute([$id, $supplierId]);
            $order = $stmt->fetch();

            if (!$order) {
                setFlash('error', 'Order not found');
                redirect(url('supplier/orders'));
            }

            // Get order items
            $stmt = $db->prepare("
                SELECT poi.*, sp.product_name, sp.sku
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE poi.purchase_order_id = ?
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll();

            view('supplier.orders.view', [
                'order' => $order,
                'items' => $items,
                'pageTitle' => 'Order #' . $order['po_number']
            ]);

        } catch (\PDOException $e) {
            logger("Supplier order view error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading order');
            redirect(url('supplier/orders'));
        }
    }

    public function acceptOrder(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $orderId = (int) post('order_id');
            if (!$orderId) {
                setFlash('error', 'Invalid order ID');
                back();
            }

            $db = \Database::getConnection();

            // Verify order belongs to this supplier and is in 'sent' status
            $stmt = $db->prepare("
                SELECT id, po_number, status
                FROM purchase_orders
                WHERE id = ? AND supplier_id = ?
            ");
            $stmt->execute([$orderId, $supplierId]);
            $order = $stmt->fetch();

            if (!$order) {
                setFlash('error', 'Order not found');
                redirect(url('supplier/orders'));
            }

            if ($order['status'] !== 'sent') {
                setFlash('error', 'This order cannot be accepted. Current status: ' . ucfirst($order['status']));
                redirect(url('supplier/orders/view?id=' . $orderId));
            }

            // Get full PO + supplier data for email
            $poStmt = $db->prepare("
                SELECT po.*, s.company_name AS supplier_name, s.email AS supplier_email, s.contact_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $poStmt->execute([$orderId]);
            $poData = $poStmt->fetch(\PDO::FETCH_ASSOC);

            // Capture optional ready-by time (express orders)
            $readyByTime = null;
            $rawReadyBy  = trim(post('ready_by_time', ''));
            if ($rawReadyBy) {
                $ts = strtotime($rawReadyBy);
                if ($ts !== false) $readyByTime = date('Y-m-d H:i:s', $ts);
            }

            // Generate Sales Order number (SO-YYYYMM-NNNN)
            $soPrefix = 'SO-' . date('Ym') . '-';
            $lastSoStmt = $db->query("SELECT so_number FROM purchase_orders WHERE so_number LIKE '{$soPrefix}%' ORDER BY id DESC LIMIT 1");
            $lastSo = $lastSoStmt->fetchColumn();
            $nextSeq = $lastSo ? ((int)substr($lastSo, -4) + 1) : 1;
            $soNumber = $soPrefix . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            // Update order status to 'accepted' and set accepted timestamp + SO number
            $stmt = $db->prepare("
                UPDATE purchase_orders
                SET status = 'accepted',
                    supplier_accepted_at = NOW(),
                    ready_by_time = ?,
                    so_number = ?
                WHERE id = ?
            ");
            $stmt->execute([$readyByTime, $soNumber, $orderId]);

            // Decrement supplier product stock for each item in this PO
            $stockStmt = $db->prepare("
                SELECT product_id, quantity_ordered
                FROM purchase_order_items
                WHERE purchase_order_id = ?
            ");
            $stockStmt->execute([$orderId]);
            foreach ($stockStmt->fetchAll(\PDO::FETCH_ASSOC) as $item) {
                $db->prepare("
                    UPDATE supplier_products
                    SET stock_quantity = GREATEST(0, COALESCE(stock_quantity, 0) - ?),
                        is_available   = CASE
                                            WHEN GREATEST(0, COALESCE(stock_quantity, 0) - ?) <= 0
                                            THEN 0
                                            ELSE is_available
                                         END
                    WHERE id = ? AND supplier_id = ?
                ")->execute([
                    $item['quantity_ordered'],
                    $item['quantity_ordered'],
                    $item['product_id'],
                    $supplierId
                ]);
            }

            // Send status update email to both supplier and admin
            if ($poData) {
                $supplier = [
                    'name' => $poData['supplier_name'],
                    'company_name' => $poData['supplier_name'],
                    'email' => $poData['supplier_email'],
                ];
                \App\Helpers\EmailHelper::sendPurchaseOrderStatusUpdate($poData, $supplier, 'sent', 'accepted');

                // Admin bell notification
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    "PO #{$order['po_number']} — Accepted",
                    "Supplier " . ($poData['supplier_name'] ?? '') . " accepted PO #{$order['po_number']} and will begin preparing the order.",
                    ['link' => "/admin/purchase-orders/view?id={$orderId}", 'icon' => 'check-circle', 'priority' => 'normal']
                );

                // Supplier bell notification
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supplierId, 'purchase_order', "PO #{$order['po_number']} Accepted",
                    "You accepted PO #{$order['po_number']}. Click 'Start Preparing' when you begin packing.",
                    'supplier/orders/view?id=' . $orderId, 'check-circle',
                    "BC #{$order['po_number']} accepté",
                    "Vous avez accepté BC #{$order['po_number']}. Cliquez sur « Commencer la préparation » lorsque vous commencez l'emballage."
                );

                // Business bell notification — supplier confirmed their PO
                if (!empty($poData['distribution_request_id'])) {
                    try {
                        $drInfo = $db->prepare("
                            SELECT dr.id, dr.request_number, dr.business_profile_id,
                                   (SELECT COUNT(*) FROM purchase_orders
                                    WHERE distribution_request_id = dr.id AND status = 'sent') AS still_pending
                            FROM distribution_requests dr
                            WHERE dr.id = ?
                            LIMIT 1
                        ");
                        $drInfo->execute([$poData['distribution_request_id']]);
                        $drRow = $drInfo->fetch(\PDO::FETCH_ASSOC);

                        if ($drRow) {
                            $supplierName = $poData['supplier_name'] ?? 'A supplier';
                            $stillPending = (int)$drRow['still_pending'];
                            $msg = $stillPending > 0
                                ? "{$supplierName} confirmed your order for request #{$drRow['request_number']}. Waiting on {$stillPending} more supplier(s)."
                                : "{$supplierName} confirmed your order for request #{$drRow['request_number']}. All suppliers confirmed — payment link coming shortly.";

                            \App\Helpers\NotificationHelper::addBusinessNotification(
                                (int)$drRow['business_profile_id'],
                                'supplier_confirmed',
                                'Supplier Confirmed',
                                $msg,
                                'distribution/requests/show?id=' . $drRow['id'],
                                'check-circle'
                            );
                        }
                    } catch (\Exception $e) {
                        // non-fatal
                        error_log('Business notification on supplier accept error: ' . $e->getMessage());
                    }
                }
            }

            // Auto-generate invoice for this PO
            $invoiceData = \App\Controllers\AdminPayablesController::createInvoiceForPO($orderId);
            if ($invoiceData) {
                // Generate PDF
                $pdfPath = \App\Controllers\AdminPayablesController::generateInvoicePdf($invoiceData['id']);

                // Get PO items for the email
                $itemsStmt = $db->prepare("
                    SELECT poi.*, poi.quantity_ordered AS quantity, sp.product_name, sp.sku
                    FROM purchase_order_items poi
                    LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                    WHERE poi.purchase_order_id = ?
                ");
                $itemsStmt->execute([$orderId]);
                $poItems = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

                // Send invoice email to admin with PDF
                \App\Helpers\EmailHelper::sendInvoiceGenerated(
                    $invoiceData,
                    ['company_name' => $poData['supplier_name'] ?? '', 'email' => $poData['supplier_email'] ?? ''],
                    $poItems,
                    $pdfPath ?: null
                );

                // Admin bell notification for invoice
                \App\Helpers\NotificationHelper::add(
                    'invoice',
                    "New Invoice: {$invoiceData['invoice_number']}",
                    "Invoice {$invoiceData['invoice_number']} generated for PO #{$order['po_number']} from " . ($poData['supplier_name'] ?? 'supplier') . ". Total: $" . number_format($invoiceData['total_amount'], 2),
                    ['link' => "/admin/payables/view?id={$invoiceData['id']}", 'icon' => 'file-invoice-dollar', 'priority' => 'high']
                );

                // Supplier bell notification for invoice
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supplierId, 'invoice', "Invoice {$invoiceData['invoice_number']} Generated",
                    "Invoice {$invoiceData['invoice_number']} has been generated for PO #{$order['po_number']}. Total: $" . number_format($invoiceData['total_amount'], 2),
                    'supplier/invoices', 'file-invoice-dollar',
                    "Facture {$invoiceData['invoice_number']} générée",
                    "La facture {$invoiceData['invoice_number']} a été générée pour BC #{$order['po_number']}. Total : " . number_format($invoiceData['total_amount'], 2) . ' $'
                );

                logger("Auto-generated invoice {$invoiceData['invoice_number']} for PO #{$order['po_number']}", 'info');
            }

            // Check if all suppliers for the linked distribution request have confirmed
            $this->checkAllSuppliersConfirmed($orderId, $db);

            logger("Supplier {$supplierId} accepted purchase order #{$order['po_number']}", 'info');
            setFlash('success', 'Purchase order accepted. Click "Start Preparing" when you begin packing the items.');
            redirect(url('supplier/orders/view?id=' . $orderId));

        } catch (\Exception $e) {
            logger("Supplier accept order error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error accepting order');
            back();
        }
    }

    public function declineOrder(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $orderId = (int) post('order_id');
            $declineReason = post('decline_reason', '');

            if (!$orderId) {
                setFlash('error', 'Invalid order ID');
                back();
            }

            if (empty($declineReason)) {
                setFlash('error', 'Please provide a reason for declining the order');
                back();
            }

            $db = \Database::getConnection();

            // Verify order belongs to this supplier and is in 'sent' status
            $stmt = $db->prepare("
                SELECT id, po_number, status
                FROM purchase_orders
                WHERE id = ? AND supplier_id = ?
            ");
            $stmt->execute([$orderId, $supplierId]);
            $order = $stmt->fetch();

            if (!$order) {
                setFlash('error', 'Order not found');
                redirect(url('supplier/orders'));
            }

            if ($order['status'] !== 'sent') {
                setFlash('error', 'This order cannot be declined. Current status: ' . ucfirst($order['status']));
                redirect(url('supplier/orders/view?id=' . $orderId));
            }

            // Get full PO + supplier data for email
            $poStmt = $db->prepare("
                SELECT po.*, s.company_name AS supplier_name, s.email AS supplier_email, s.contact_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $poStmt->execute([$orderId]);
            $poData = $poStmt->fetch(\PDO::FETCH_ASSOC);

            // Update order status to 'cancelled' and set decline info
            $stmt = $db->prepare("
                UPDATE purchase_orders
                SET status = 'cancelled',
                    supplier_declined_at = NOW(),
                    decline_reason = ?
                WHERE id = ?
            ");
            $stmt->execute([$declineReason, $orderId]);

            // Send status update email to both supplier and admin
            if ($poData) {
                $supplier = [
                    'name' => $poData['supplier_name'],
                    'company_name' => $poData['supplier_name'],
                    'email' => $poData['supplier_email'],
                ];
                \App\Helpers\EmailHelper::sendPurchaseOrderStatusUpdate($poData, $supplier, 'sent', 'cancelled', $declineReason);

                // Admin bell notification (high priority — order was declined)
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    "PO #{$order['po_number']} — Declined",
                    "Supplier " . ($poData['supplier_name'] ?? '') . " declined PO #{$order['po_number']}. Reason: " . substr($declineReason, 0, 100),
                    ['link' => '/admin/purchase-orders/view?id=' . $orderId, 'icon' => 'times-circle', 'priority' => 'high']
                );

                // Supplier bell notification
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supplierId, 'purchase_order', "PO #{$order['po_number']} Declined",
                    "You declined PO #{$order['po_number']}. The administrator has been notified.",
                    '/supplier/orders', 'times-circle',
                    "BC #{$order['po_number']} refusé",
                    "Vous avez refusé BC #{$order['po_number']}. L'administrateur a été informé."
                );
            }

            // If this PO is linked to a distribution request, notify business and trigger escalation
            if (!empty($poData['distribution_request_id'])) {
                try {
                    $drDecline = $db->prepare("
                        SELECT id, request_number, business_profile_id FROM distribution_requests WHERE id = ? LIMIT 1
                    ");
                    $drDecline->execute([$poData['distribution_request_id']]);
                    $drDec = $drDecline->fetch(\PDO::FETCH_ASSOC);
                    if ($drDec) {
                        \App\Helpers\NotificationHelper::addBusinessNotification(
                            (int)$drDec['business_profile_id'],
                            'supplier_declined',
                            '⚠️ Supplier Issue — #' . $drDec['request_number'],
                            "A supplier for your request #{$drDec['request_number']} could not fulfill their items. We are finding an alternative — no action needed from you.",
                            'distribution/requests/show?id=' . $drDec['id']
                        );
                    }
                } catch (\Exception $e) { /* non-fatal */ }

                $this->escalateDeclinedPO($orderId, $poData, $db);
            }

            logger("Supplier {$supplierId} declined purchase order #{$order['po_number']}. Reason: {$declineReason}", 'info');
            setFlash('success', 'Purchase order declined. The administrator has been notified.');
            redirect(url('supplier/orders'));

        } catch (\PDOException $e) {
            logger("Supplier decline order error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error declining order');
            back();
        }
    }

    // ── Distribution request: check if all POs are confirmed ─────────────────

    private function checkAllSuppliersConfirmed(int $poId, \PDO $db): void
    {
        try {
            // Get distribution_request_id for this PO
            $stmt = $db->prepare("SELECT distribution_request_id FROM purchase_orders WHERE id = ? LIMIT 1");
            $stmt->execute([$poId]);
            $distRequestId = (int) $stmt->fetchColumn();
            if (!$distRequestId) return; // Not a distribution PO

            // Any POs still waiting for supplier response?
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM purchase_orders
                WHERE distribution_request_id = ? AND status = 'sent'
            ");
            $stmt->execute([$distRequestId]);
            if ((int)$stmt->fetchColumn() > 0) return; // Still waiting

            // Any POs declined/cancelled? (escalation in progress — don't generate payment link yet)
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM purchase_orders
                WHERE distribution_request_id = ? AND status = 'cancelled'
            ");
            $stmt->execute([$distRequestId]);
            if ((int)$stmt->fetchColumn() > 0) return;

            // All confirmed! Generate payment link & notify business
            $stmt = $db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status = 'approved'
                LIMIT 1
            ");
            $stmt->execute([$distRequestId]);
            $request = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$request) return;

            $token     = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+48 hours'));

            $db->prepare("
                UPDATE distribution_requests
                SET status = 'awaiting_payment',
                    payment_link_token = ?,
                    payment_link_expires_at = ?,
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$token, $expiresAt, $distRequestId]);

            // Log status change
            $db->prepare("
                INSERT INTO distribution_status_history
                    (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, 'approved', 'awaiting_payment', 'system', NULL, 'All suppliers confirmed — payment link sent to business', NOW())
            ")->execute([$distRequestId]);

            // Send payment link email to business
            \App\Controllers\AdminDistributionController::sendPaymentLinkEmail($request, $token, $expiresAt);

            // Create invoice record (status='sent') so business sees it before paying
            \App\Controllers\AdminDistributionController::ensureDistributionInvoice($distRequestId, $db);

            // In-app notification for business
            \App\Helpers\NotificationHelper::addBusinessNotification(
                (int)$request['business_profile_id'],
                'payment_required',
                '💳 Payment Required — All Suppliers Confirmed',
                "All suppliers have confirmed your request #{$request['request_number']}. Please complete your payment to proceed.",
                'distribution/pay?token=' . $token,
                'credit-card'
            );

            // Admin notification
            \App\Helpers\NotificationHelper::add(
                'new_order',
                'All Suppliers Confirmed — Payment Link Sent',
                "All suppliers confirmed for #{$request['request_number']} ({$request['company_name']}). Payment link sent to business.",
                ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'check-double', 'priority' => 'high']
            );

            logger("Distribution request #{$distRequestId}: all suppliers confirmed, payment link sent.", 'info');

        } catch (\Exception $e) {
            logger("checkAllSuppliersConfirmed error: " . $e->getMessage(), 'error');
        }
    }

    // ── Distribution request: escalate when a supplier declines ──────────────

    private function escalateDeclinedPO(int $poId, array $poData, \PDO $db): void
    {
        try {
            $distRequestId    = (int) $poData['distribution_request_id'];
            $escalationAttempt = (int) ($poData['escalation_attempt'] ?? 0);
            $deliveryType     = 'scheduled'; // fallback

            // Get delivery type from the distribution request
            $stmt = $db->prepare("SELECT delivery_type FROM distribution_requests WHERE id = ? LIMIT 1");
            $stmt->execute([$distRequestId]);
            $dr = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($dr) $deliveryType = $dr['delivery_type'] ?? 'scheduled';

            if ($escalationAttempt >= 2) {
                // Cap reached — alert admin to handle manually
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    '⚠️ Supplier Escalation Failed — Admin Action Required',
                    "PO #{$poData['po_number']} was declined twice. No more backup suppliers available. Manual intervention required for distribution request.",
                    ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );
                return;
            }

            // Collect already-tried supplier_product IDs for each item in this PO
            $stmt = $db->prepare("
                SELECT poi.product_id FROM purchase_order_items poi WHERE poi.purchase_order_id = ?
            ");
            $stmt->execute([$poId]);
            $primaryProductIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Get already-tried supplier IDs from previously cancelled POs for this distribution request
            $stmt = $db->prepare("
                SELECT DISTINCT sp.supplier_id
                FROM purchase_orders po
                JOIN purchase_order_items poi ON poi.purchase_order_id = po.id
                JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE po.distribution_request_id = ? AND po.status = 'cancelled'
            ");
            $stmt->execute([$distRequestId]);
            $triedSupplierIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            // Find backup for the first declined product
            $backup = null;
            $backupProductId = null;
            foreach ($primaryProductIds as $productId) {
                $excludeIds = array_merge([$productId]);
                // Exclude products from already-tried suppliers
                $backup = getBackupSupplierProduct((int)$productId, $excludeIds);
                if ($backup) {
                    $backupProductId = $productId;
                    break;
                }
            }

            if (!$backup) {
                // No backup found — alert admin
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    '⚠️ No Backup Supplier Found — Admin Action Required',
                    "PO #{$poData['po_number']} was declined and no backup supplier is mapped. Manual action required for distribution request.",
                    ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );
                return;
            }

            // Create replacement PO for the backup supplier
            $lastStmt = $db->query("SELECT po_number FROM purchase_orders ORDER BY id DESC LIMIT 1");
            $lastPO   = $lastStmt->fetch(\PDO::FETCH_ASSOC);
            $nextNum  = 1;
            if ($lastPO && preg_match('/PO-(\d+)/', $lastPO['po_number'], $m)) $nextNum = (int)$m[1] + 1;
            $newPoNumber = 'PO-' . str_pad($nextNum, 6, '0', STR_PAD_LEFT);

            // Recalculate totals using backup supplier's unit_price
            $subtotal    = $backup['unit_price'] * array_sum(array_column(
                $db->query("SELECT quantity_ordered FROM purchase_order_items WHERE purchase_order_id = {$poId}")->fetchAll(\PDO::FETCH_ASSOC),
                'quantity_ordered'
            ));
            $taxGst      = round($subtotal * 0.05, 2);
            $taxQst      = round($subtotal * 0.09975, 2);
            $taxAmount   = $taxGst + $taxQst;
            $totalAmount = $subtotal + $taxAmount;

            $confirmationDeadline = date('Y-m-d H:i:s', strtotime($deliveryType === 'express' ? '+2 hours' : '+24 hours'));

            $stmt = $db->prepare("
                INSERT INTO purchase_orders
                    (po_number, supplier_id, order_date, status, subtotal, tax_gst, tax_qst, tax_amount,
                     shipping_cost, total_amount, notes, created_by, distribution_request_id,
                     confirmation_deadline, escalation_attempt)
                VALUES (?, ?, CURDATE(), 'sent', ?, ?, ?, ?, 0, ?, ?, 0, ?, ?, ?)
            ");
            $stmt->execute([
                $newPoNumber,
                $backup['supplier_id'],
                $subtotal, $taxGst, $taxQst, $taxAmount, $totalAmount,
                "Escalation backup — replaces declined PO #{$poData['po_number']}",
                $distRequestId,
                $confirmationDeadline,
                $escalationAttempt + 1,
            ]);
            $newPoId = $db->lastInsertId();

            // Copy items from original PO, using backup product
            $stmt = $db->prepare("
                SELECT quantity_ordered FROM purchase_order_items WHERE purchase_order_id = ? LIMIT 1
            ");
            $stmt->execute([$poId]);
            $qty = (int)($stmt->fetchColumn() ?: 1);

            $db->prepare("
                INSERT INTO purchase_order_items
                    (purchase_order_id, product_id, quantity_ordered, unit_cost, total_cost)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$newPoId, $backup['id'], $qty, $backup['unit_price'], $backup['unit_price'] * $qty]);

            // Notify backup supplier
            $urgency   = $deliveryType === 'express' ? '⚡ EXPRESS' : '📅 Scheduled';
            $urgencyFr = $deliveryType === 'express' ? '⚡ EXPRESS' : '📅 Planifié';
            \App\Helpers\NotificationHelper::addSupplierNotification(
                $backup['supplier_id'],
                'purchase_order',
                "New PO #{$newPoNumber} — Confirmation Required",
                "{$urgency}. Please confirm PO #{$newPoNumber} by " . date('M j, g:i A', strtotime($confirmationDeadline)) . '.',
                "supplier/orders/view?id={$newPoId}",
                'clipboard-check',
                "Nouveau BC #{$newPoNumber} — Confirmation requise",
                "{$urgencyFr}. Veuillez confirmer BC #{$newPoNumber} avant le " . date('j M \à G\hi', strtotime($confirmationDeadline)) . '.'
            );

            // Admin: escalation in progress
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "PO Escalated to Backup Supplier",
                "PO #{$poData['po_number']} was declined. New PO #{$newPoNumber} sent to backup supplier " . ($backup['supplier_name'] ?? $backup['supplier_company'] ?? '') . ".",
                ['link' => '/admin/distribution/view?id=' . $distRequestId, 'icon' => 'sync', 'priority' => 'normal']
            );

            // ── Notify business of supplier switch + price change ────────────────
            $this->notifyBusinessSupplierSwitch($distRequestId, $poData, $backup, $totalAmount, $deliveryType, $db);

            logger("Distribution escalation: PO #{$poData['po_number']} declined, new PO #{$newPoNumber} sent to backup supplier #{$backup['supplier_id']}", 'info');

        } catch (\Exception $e) {
            logger("escalateDeclinedPO error: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Notify the business that a supplier was switched and the price changed.
     * Sets supplier_switch_pending on distribution_requests and emails the business.
     */
    private function notifyBusinessSupplierSwitch(
        int $distRequestId,
        array $declinedPoData,
        array $backup,
        float $newPoTotal,
        string $deliveryType,
        \PDO $db
    ): void {
        try {
            // Get distribution request + business contact info
            $stmt = $db->prepare("
                SELECT dr.*, bp.company_name, u.email, u.first_name, u.last_name
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON u.id = (
                    SELECT user_id FROM business_profiles WHERE id = dr.business_profile_id LIMIT 1
                )
                WHERE dr.id = ? LIMIT 1
            ");
            $stmt->execute([$distRequestId]);
            $dr = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$dr) return;

            $oldTotal  = (float)($dr['total_amount'] ?? 0);
            $priceDiff = $newPoTotal - $oldTotal;

            // Confirmation window: 30min express, 12h scheduled
            $windowSecs = $deliveryType === 'express' ? 1800 : 43200;
            $deadline   = date('Y-m-d H:i:s', time() + $windowSecs);
            $deadlineFormatted = date('F j, Y \a\t g:i A', time() + $windowSecs);

            $backupSupplierName = $backup['supplier_company'] ?? $backup['supplier_name'] ?? 'a backup supplier';
            $switchNotes = sprintf(
                'Original supplier declined. Switched to %s. Price changed from $%s to $%s (%s$%s).',
                $backupSupplierName,
                number_format($oldTotal, 2),
                number_format($newPoTotal, 2),
                $priceDiff >= 0 ? '+' : '-',
                number_format(abs($priceDiff), 2)
            );

            // Update distribution_requests with pending switch info
            $db->prepare("
                UPDATE distribution_requests SET
                    supplier_switch_pending   = 1,
                    supplier_switch_deadline  = ?,
                    supplier_switch_old_amount = ?,
                    supplier_switch_new_amount = ?,
                    supplier_switch_notes      = ?,
                    updated_at = NOW()
                WHERE id = ?
            ")->execute([$deadline, $oldTotal, $newPoTotal, $switchNotes, $distRequestId]);

            // Portal bell notification for business
            $portalLink = 'distribution/requests/show?id=' . $distRequestId;
            \App\Helpers\NotificationHelper::addBusinessNotification(
                (int)$dr['business_profile_id'],
                'supplier_switch',
                '⚠️ Supplier Switch — Confirmation Required',
                "A supplier for request #{$dr['request_number']} was unavailable. We found a backup. Price updated to $" . number_format($newPoTotal, 2) . ". Please confirm or cancel by {$deadlineFormatted}.",
                $portalLink,
                'sync-alt'
            );

            // Email business
            $confirmUrl = url('distribution/requests/show?id=' . $distRequestId);
            $priceBlock = $priceDiff == 0
                ? "<p style='color:#374151;'><strong>Price:</strong> No change — $" . number_format($newPoTotal, 2) . " CAD</p>"
                : "<p style='color:#374151;'><strong>Previous total:</strong> <span style='text-decoration:line-through;color:#9ca3af;'>$" . number_format($oldTotal, 2) . " CAD</span></p>
                   <p style='color:#374151;'><strong>Updated total:</strong> <span style='color:" . ($priceDiff > 0 ? '#dc2626' : '#059669') . ";font-size:18px;font-weight:700;'>$" . number_format($newPoTotal, 2) . " CAD</span></p>
                   <p style='color:#6b7280;font-size:13px;'>Difference: " . ($priceDiff > 0 ? '+' : '') . "$" . number_format($priceDiff, 2) . " CAD</p>";

            $subject = "Action Required: Supplier Switch for Request #{$dr['request_number']}";
            $body = "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#f59e0b;padding:20px;text-align:center;'>
                    <h1 style='color:white;margin:0;'>Supplier Switch Notice</h1>
                    <p style='color:#fffbeb;margin:8px 0 0;'>Your confirmation is required</p>
                </div>
                <div style='padding:30px;background:#f8f9fa;'>
                    <p>Hi {$dr['first_name']},</p>
                    <p>One of the suppliers for your distribution request <strong>#{$dr['request_number']}</strong> was unable to fulfill their portion of the order. We've found a backup supplier and need your confirmation to proceed.</p>

                    <div style='background:white;border-radius:8px;padding:20px;margin:20px 0;border-left:4px solid #f59e0b;'>
                        <h3 style='margin-top:0;color:#92400e;'>What changed?</h3>
                        <p style='color:#374151;margin:0 0 12px;'>{$switchNotes}</p>
                        {$priceBlock}
                    </div>

                    <div style='background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:14px 18px;margin-bottom:20px;'>
                        <strong style='color:#92400e;'>⏰ Please respond by:</strong><br>
                        <span style='color:#78350f;font-size:15px;'>{$deadlineFormatted}</span>
                        <br><small style='color:#a16207;'>If no response is received, the order will be automatically cancelled.</small>
                    </div>

                    <div style='text-align:center;margin:30px 0;display:flex;gap:16px;justify-content:center;flex-wrap:wrap;'>
                        <a href='{$confirmUrl}' style='display:inline-block;background:#00b207;color:white;padding:14px 32px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:15px;'>
                            ✓ Accept &amp; Proceed
                        </a>
                        <a href='{$confirmUrl}' style='display:inline-block;background:#ef4444;color:white;padding:14px 32px;text-decoration:none;border-radius:8px;font-weight:bold;font-size:15px;'>
                            ✕ Cancel Order
                        </a>
                    </div>
                    <p style='color:#6b7280;font-size:13px;text-align:center;'>Click either button in the portal to make your selection.</p>
                    <hr style='border:none;border-top:1px solid #e5e7eb;margin:24px 0;'>
                    <p style='color:#9ca3af;font-size:12px;'>OCS Distribution | <a href='" . url('/') . "'>ocsapp.ca</a></p>
                </div>
            </div>";

            \App\Helpers\EmailHelper::send($dr['email'], $subject, $body);

        } catch (\Exception $e) {
            logger("notifyBusinessSupplierSwitch error: " . $e->getMessage(), 'error');
        }
    }

    /**
     * Supplier advances PO to 'preparing' status
     */
    public function startPreparing(): void
    {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $orderId = (int) post('order_id');
            if (!$orderId) {
                setFlash('error', 'Invalid order ID');
                back();
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT po.id, po.po_number, po.status, po.admin_paid_at,
                       po.distribution_request_id, s.company_name AS supplier_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ? AND po.supplier_id = ?
            ");
            $stmt->execute([$orderId, $supplierId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order || $order['status'] !== 'accepted') {
                setFlash('error', 'Order cannot be moved to preparing at this stage.');
                redirect(url('supplier/orders/view?id=' . $orderId));
            }

            // Block distribution POs until OCSApp has paid the supplier
            if (!empty($order['distribution_request_id']) && empty($order['admin_paid_at'])) {
                setFlash('error', 'You cannot begin preparing this order until OCSApp has sent your payment. You will be notified when payment is confirmed.');
                redirect(url('supplier/orders/view?id=' . $orderId));
            }

            $db->prepare("UPDATE purchase_orders SET status = 'preparing', updated_at = NOW() WHERE id = ?")
               ->execute([$orderId]);

            // Admin bell
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "PO #{$order['po_number']} — Preparing",
                "Supplier " . ($order['supplier_name'] ?? '') . " has started preparing PO #{$order['po_number']}.",
                ['link' => "/admin/purchase-orders/view?id={$orderId}", 'icon' => 'box-open', 'priority' => 'low']
            );

            // Business bell — only for distribution POs
            if (!empty($order['distribution_request_id'])) {
                $drRow = $db->prepare("SELECT business_profile_id FROM distribution_requests WHERE id = ? LIMIT 1");
                $drRow->execute([$order['distribution_request_id']]);
                $dr = $drRow->fetch(\PDO::FETCH_ASSOC);
                if (!empty($dr['business_profile_id'])) {
                    \App\Helpers\NotificationHelper::addBusinessNotification(
                        (int)$dr['business_profile_id'],
                        'order_update',
                        '📦 Order Being Prepared',
                        'Supplier(s) are preparing your order and should be ready for pick up shortly.',
                        'distribution/requests/show?id=' . $order['distribution_request_id']
                    );
                }
            }

            logger("Supplier {$supplierId} started preparing PO #{$order['po_number']}", 'info');
            setFlash('success', 'Order marked as being prepared. Click "Ready for Pickup" when all items are packed.');
            redirect(url('supplier/orders/view?id=' . $orderId));

        } catch (\Exception $e) {
            logger("startPreparing error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating order status.');
            back();
        }
    }

    /**
     * Supplier flags an issue on a PO (partial stock, delay, damage, etc.)
     * Notifies admin via bell and stores a note on the PO.
     */
    public function reportIssue(): void
    {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request.');
            back();
        }

        $orderId   = (int) post('order_id');
        $issueType = trim(post('issue_type', ''));
        $message   = trim(post('message', ''));

        if (!$orderId || empty($issueType) || empty($message)) {
            setFlash('error', 'Please fill in all fields.');
            back();
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT po.id, po.po_number, po.status, po.distribution_request_id,
                       po.notes, s.company_name AS supplier_name
                FROM purchase_orders po
                JOIN suppliers s ON s.id = po.supplier_id
                WHERE po.id = ? AND po.supplier_id = ?
                  AND po.status NOT IN ('draft','completed','cancelled')
                LIMIT 1
            ");
            $stmt->execute([$orderId, $supplierId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                setFlash('error', 'Order not found.');
                redirect(url('supplier/orders'));
            }

            $issueLabels = [
                'partial_stock'  => 'Partial Stock — Cannot fully fulfill',
                'delay'          => 'Delay — Will not be ready on time',
                'out_of_stock'   => 'Out of Stock — Cannot fulfill at all',
                'damaged_goods'  => 'Damaged Goods',
                'other'          => 'Other Issue',
            ];
            $issueLabel = $issueLabels[$issueType] ?? $issueType;

            // Append issue note to PO notes
            $timestamp   = date('Y-m-d H:i');
            $noteAppend  = "\n[{$timestamp}] ⚠️ SUPPLIER ISSUE — {$issueLabel}: {$message}";
            $updatedNote = ($order['notes'] ?? '') . $noteAppend;

            $db->prepare("UPDATE purchase_orders SET notes = ?, updated_at = NOW() WHERE id = ?")
               ->execute([trim($updatedNote), $orderId]);

            // Admin bell — urgent
            \App\Helpers\NotificationHelper::add(
                'new_order',
                '⚠️ Supplier Issue — PO #' . $order['po_number'],
                $order['supplier_name'] . ' flagged an issue: ' . $issueLabel . '. "' . mb_substr($message, 0, 120) . '"',
                ['link' => '/admin/purchase-orders/view?id=' . $orderId, 'icon' => 'exclamation-triangle', 'priority' => 'high']
            );

            logger("Supplier {$supplierId} reported issue on PO #{$order['po_number']}: {$issueLabel}", 'info');
            setFlash('success', 'Issue reported. Our team has been notified and will follow up shortly.');
            redirect(url('supplier/orders/view?id=' . $orderId));

        } catch (\Exception $e) {
            logger('reportIssue error: ' . $e->getMessage(), 'error');
            setFlash('error', 'Error reporting issue. Please try again.');
            back();
        }
    }

    /**
     * Supplier marks PO as ready for driver pickup — triggers driver alert
     */
    public function markReadyForPickup(): void
    {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $orderId = (int) post('order_id');
            if (!$orderId) {
                setFlash('error', 'Invalid order ID');
                back();
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT po.id, po.po_number, po.status, po.distribution_request_id,
                       s.company_name AS supplier_name, s.email AS supplier_email,
                       s.address, s.city, s.province
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ? AND po.supplier_id = ?
            ");
            $stmt->execute([$orderId, $supplierId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order || $order['status'] !== 'preparing') {
                setFlash('error', 'Order cannot be marked ready for pickup at this stage.');
                redirect(url('supplier/orders/view?id=' . $orderId));
            }

            $db->prepare("UPDATE purchase_orders SET status = 'ready_for_pickup', updated_at = NOW() WHERE id = ?")
               ->execute([$orderId]);

            // --- B2B: check if all suppliers for this distribution request are now ready ---
            $distRequestId = (int)($order['distribution_request_id'] ?? 0);
            if ($distRequestId) {
                // Only dispatch a driver once the business has paid
                $drRow = $db->prepare("SELECT status FROM distribution_requests WHERE id = ? LIMIT 1");
                $drRow->execute([$distRequestId]);
                $drStatus = (string)$drRow->fetchColumn();

                if (in_array($drStatus, ['paid', 'processing'])) {
                    $notReady = $db->prepare("
                        SELECT COUNT(*) FROM purchase_orders
                        WHERE distribution_request_id = ?
                          AND status NOT IN ('ready_for_pickup','picked_up','completed','cancelled')
                    ");
                    $notReady->execute([$distRequestId]);
                    if ((int)$notReady->fetchColumn() === 0) {
                        // All suppliers ready — auto-assign driver
                        \App\Controllers\AdminDistributionController::autoAssignDistributionDriver($distRequestId, $db);
                        logger("All suppliers ready for distribution request #{$distRequestId} — auto-assign triggered", 'info');
                    }
                } else {
                    logger("Supplier marked PO ready for distribution #{$distRequestId} but DR status is '{$drStatus}' — driver assignment deferred.", 'info');
                }
            }

            $address = trim(($order['address'] ?? '') . ', ' . ($order['city'] ?? '') . ', ' . ($order['province'] ?? ''), ', ');

            // Email admin — driver assignment needed
            try {
                $config = require dirname(__DIR__, 2) . '/config/mail.php';
                $adminEmail = $config['admin_email'] ?? 'info@ocsapp.ca';
                $poUrl = 'https://ocsapp.ca/admin/purchase-orders/view?id=' . $orderId;
                $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #f59e0b; padding: 20px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>&#x1F69A; Pickup Ready — Action Required</h1>
                    </div>
                    <div style='padding: 30px; background: #f8f9fa;'>
                        <p><strong>PO #{$order['po_number']}</strong> from <strong>" . htmlspecialchars($order['supplier_name'] ?? '') . "</strong> is packed and ready for pickup.</p>
                        " . ($address ? "<p><strong>Pickup address:</strong> {$address}</p>" : '') . "
                        <p>Please assign a driver to collect this order.</p>
                        <div style='text-align: center; margin: 24px 0;'>
                            <a href='{$poUrl}' style='display: inline-block; background: #f59e0b; color: white; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-weight: bold;'>
                                Assign Driver
                            </a>
                        </div>
                        <hr style='border: none; border-top: 1px solid #ddd; margin: 24px 0;'>
                        <p style='color: #888; font-size: 12px;'>OCSAPP Admin | <a href='https://ocsapp.ca'>ocsapp.ca</a></p>
                    </div>
                </div>";
                \App\Helpers\EmailHelper::send($adminEmail, "Pickup Ready - PO #{$order['po_number']} Needs Driver Assignment", $emailBody);
            } catch (\Exception $e) {
                logger("Ready for pickup admin email error: " . $e->getMessage(), 'warning');
            }

            // High-priority admin notification — driver needs to be assigned
            \App\Helpers\NotificationHelper::add(
                'pickup_request',
                "🚚 Pickup Ready — PO #{$order['po_number']}",
                "Supplier " . ($order['supplier_name'] ?? '') . " has packed PO #{$order['po_number']} and is ready for pickup." . ($address ? " Address: {$address}." : ''),
                ['link' => "/admin/purchase-orders/view?id={$orderId}", 'icon' => 'truck', 'priority' => 'high']
            );

            // Supplier confirmation notification
            \App\Helpers\NotificationHelper::addSupplierNotification(
                $supplierId, 'purchase_order', "PO #{$order['po_number']} — Ready for Pickup",
                "Your order is marked as ready. A driver will be assigned shortly.",
                'supplier/orders/view?id=' . $orderId, 'truck',
                "BC #{$order['po_number']} — Prêt pour la collecte",
                'Votre commande est marquée comme prête. Un chauffeur vous sera assigné sous peu.'
            );

            // Business bell — one of their suppliers is packed and ready
            if ($distRequestId) {
                try {
                    $drReady = $db->prepare("
                        SELECT id, request_number, business_profile_id FROM distribution_requests WHERE id = ? LIMIT 1
                    ");
                    $drReady->execute([$distRequestId]);
                    $drR = $drReady->fetch(\PDO::FETCH_ASSOC);
                    if ($drR) {
                        \App\Helpers\NotificationHelper::addBusinessNotification(
                            (int)$drR['business_profile_id'],
                            'delivery',
                            '📦 Supplier Ready — #' . $drR['request_number'],
                            ($order['supplier_name'] ?? 'A supplier') . " has packed your items and is ready for driver pickup.",
                            'distribution/requests/show?id=' . $drR['id']
                        );
                    }
                } catch (\Exception $e) { /* non-fatal */ }
            }

            logger("Supplier {$supplierId} marked PO #{$order['po_number']} ready for pickup", 'info');
            setFlash('success', 'Order marked as ready for pickup. Admin has been notified to assign a driver.');
            redirect(url('supplier/orders/view?id=' . $orderId));

        } catch (\Exception $e) {
            logger("markReadyForPickup error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating order status.');
            back();
        }
    }

    /**
     * Supplier invoices & payments page
     */
    public function invoices(): void
    {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Count total invoices
            $countStmt = $db->prepare("SELECT COUNT(*) FROM supplier_invoices WHERE supplier_id = ?");
            $countStmt->execute([$supplierId]);
            $total = (int)$countStmt->fetchColumn();

            $perPage = 20;
            $page = max(1, (int)get('page', 1));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, max(1, $totalPages));
            $offset = ($page - 1) * $perPage;

            // Get invoices for this supplier (paginated)
            $stmt = $db->prepare("
                SELECT si.*, po.po_number
                FROM supplier_invoices si
                LEFT JOIN purchase_orders po ON si.po_id = po.id
                WHERE si.supplier_id = ?
                ORDER BY si.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute([$supplierId]);
            $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Summary stats
            $stmt = $db->prepare("
                SELECT
                    COALESCE(SUM(total_amount), 0) as total_invoiced,
                    COALESCE(SUM(amount_paid), 0) as total_paid,
                    COALESCE(SUM(balance_due), 0) as total_outstanding,
                    COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_count,
                    COUNT(CASE WHEN status IN ('sent','partial','overdue') THEN 1 END) as unpaid_count
                FROM supplier_invoices
                WHERE supplier_id = ?
            ");
            $stmt->execute([$supplierId]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get recent payments
            $stmt = $db->prepare("
                SELECT sp.*, si.invoice_number,
                       sip.amount_applied
                FROM supplier_payments sp
                JOIN supplier_invoice_payments sip ON sp.id = sip.payment_id
                JOIN supplier_invoices si ON sip.invoice_id = si.id
                WHERE sp.supplier_id = ?
                ORDER BY sp.payment_date DESC
                LIMIT 10
            ");
            $stmt->execute([$supplierId]);
            $recentPayments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('supplier.invoices', [
                'invoices' => $invoices,
                'stats' => $stats,
                'recentPayments' => $recentPayments,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'pageTitle' => 'Invoices & Payments',
            ]);

        } catch (\PDOException $e) {
            logger("Supplier invoices error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading invoices');
            redirect(url('supplier/dashboard'));
        }
    }

    /**
     * View single invoice detail — verifies ownership
     */
    public function viewInvoice(): void
    {
        $supplierId = $this->checkAuth();
        $id = (int)get('id');

        if (!$id) {
            setFlash('error', 'Invoice not specified.');
            redirect(url('supplier/invoices'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Get invoice — verify ownership
            $stmt = $db->prepare("
                SELECT si.*, po.po_number, po.order_date as po_date
                FROM supplier_invoices si
                LEFT JOIN purchase_orders po ON si.po_id = po.id
                WHERE si.id = ? AND si.supplier_id = ?
            ");
            $stmt->execute([$id, $supplierId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$invoice) {
                setFlash('error', 'Invoice not found.');
                redirect(url('supplier/invoices'));
                return;
            }

            // Get line items if linked to a PO
            $items = [];
            if ($invoice['po_id']) {
                $stmt = $db->prepare("
                    SELECT poi.*, sp.product_name, sp.sku
                    FROM purchase_order_items poi
                    LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                    WHERE poi.purchase_order_id = ?
                ");
                $stmt->execute([$invoice['po_id']]);
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Get payment history
            $stmt = $db->prepare("
                SELECT sp.*, sip.amount_applied
                FROM supplier_invoice_payments sip
                JOIN supplier_payments sp ON sip.payment_id = sp.id
                WHERE sip.invoice_id = ?
                ORDER BY sp.payment_date DESC
            ");
            $stmt->execute([$id]);
            $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('supplier.invoice-detail', [
                'invoice' => $invoice,
                'items' => $items,
                'payments' => $payments,
                'pageTitle' => 'Invoice ' . $invoice['invoice_number'],
            ]);

        } catch (\PDOException $e) {
            logger("Supplier view invoice error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading invoice.');
            redirect(url('supplier/invoices'));
        }
    }

    /**
     * Download invoice PDF — verifies ownership
     */
    public function downloadInvoicePdf(): void
    {
        $supplierId = $this->checkAuth();
        $id = (int)get('id');

        if (!$id) {
            setFlash('error', 'Invoice not specified.');
            redirect(url('supplier/invoices'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Verify the invoice belongs to this supplier
            $stmt = $db->prepare("SELECT id, invoice_number FROM supplier_invoices WHERE id = ? AND supplier_id = ?");
            $stmt->execute([$id, $supplierId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$invoice) {
                setFlash('error', 'Invoice not found.');
                redirect(url('supplier/invoices'));
                return;
            }

            $pdfPath = \App\Controllers\AdminPayablesController::generateInvoicePdf($id);

            if (!$pdfPath || !file_exists($pdfPath)) {
                setFlash('error', 'Could not generate PDF.');
                redirect(url('supplier/invoices'));
                return;
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $invoice['invoice_number'] . '.pdf"');
            header('Content-Length: ' . filesize($pdfPath));
            readfile($pdfPath);
            exit;

        } catch (\Exception $e) {
            logger("Supplier invoice PDF download error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error generating PDF.');
            redirect(url('supplier/invoices'));
        }
    }

    /**
     * Download PO or SO PDF for a specific purchase order
     * GET /supplier/orders/download-pdf?id=X&type=po|so
     */
    public function downloadOrderPdf(): void
    {
        $supplierId = $this->checkAuth();
        $id   = (int)get('id');
        $type = get('type', 'po'); // 'po' or 'so'

        if (!$id) {
            setFlash('error', 'Order not specified.');
            redirect(url('supplier/orders'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Verify ownership and fetch PO data
            $stmt = $db->prepare("
                SELECT po.*,
                       s.company_name AS supplier_name, s.email AS supplier_email,
                       s.contact_name, s.phone AS supplier_phone,
                       s.address AS supplier_address, s.city AS supplier_city,
                       s.province AS supplier_province, s.postal_code AS supplier_postal
                FROM purchase_orders po
                JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ? AND po.supplier_id = ?
            ");
            $stmt->execute([$id, $supplierId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                setFlash('error', 'Order not found.');
                redirect(url('supplier/orders'));
                return;
            }

            // SO requires an accepted order with a so_number
            if ($type === 'so' && empty($order['so_number'])) {
                setFlash('error', 'Sales Order is only available once a Purchase Order is accepted.');
                redirect(url('supplier/orders/view?id=' . $id));
                return;
            }

            // Fetch line items
            $stmt = $db->prepare("
                SELECT poi.quantity_ordered, poi.unit_cost, poi.total_cost,
                       sp.product_name, sp.sku
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE poi.purchase_order_id = ?
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $isSO      = ($type === 'so');
            $docNumber = $isSO ? $order['so_number'] : $order['po_number'];
            $title     = $isSO ? 'SALES ORDER' : 'PURCHASE ORDER';
            $filename  = ($isSO ? 'SO_' : 'PO_') . $docNumber . '.pdf';

            // Build items HTML
            $itemsHtml = '';
            foreach ($items as $i => $item) {
                $sub = (float)$item['total_cost'];
                $itemsHtml .= "
                    <tr>
                        <td>" . ($i + 1) . "</td>
                        <td>" . htmlspecialchars($item['product_name'] ?? 'N/A') . "<br>
                            <small style='color:#666;'>SKU: " . htmlspecialchars($item['sku'] ?? 'N/A') . "</small></td>
                        <td style='text-align:center;'>" . (int)$item['quantity_ordered'] . "</td>
                        <td style='text-align:right;'>\$" . number_format((float)$item['unit_cost'], 2) . "</td>
                        <td style='text-align:right;'>\$" . number_format($sub, 2) . "</td>
                    </tr>";
            }

            $statusLabel = strtoupper(str_replace('_', ' ', $order['status']));
            $orderDate   = $order['order_date'] ? date('F j, Y', strtotime($order['order_date'])) : date('F j, Y');
            $acceptedDate = $order['supplier_accepted_at'] ? date('F j, Y', strtotime($order['supplier_accepted_at'])) : '—';

            $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>{$title} - {$docNumber}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; margin: 0; padding: 30px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left  { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        .logo { font-size: 28px; font-weight: bold; color: #00b207; margin-bottom: 5px; }
        .company-info { font-size: 11px; color: #666; }
        .document-title  { font-size: 24px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .document-number { font-size: 14px; color: #666; }
        .status-badge { display: inline-block; background: #00b207; color: white; padding: 5px 15px; border-radius: 3px; font-weight: bold; margin-top: 10px; }
        .info-section { display: table; width: 100%; margin-bottom: 30px; }
        .info-box { display: table-cell; width: 50%; vertical-align: top; padding-right: 20px; }
        .info-box h4 { font-size: 12px; color: #666; margin: 0 0 10px 0; text-transform: uppercase; border-bottom: 2px solid #00b207; padding-bottom: 5px; }
        .info-box p { margin: 4px 0; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #f5f5f5; padding: 10px; text-align: left; font-weight: bold; border-bottom: 2px solid #ddd; }
        table.items td { padding: 10px; border-bottom: 1px solid #eee; }
        table.items tr:last-child td { border-bottom: 2px solid #ddd; }
        .totals { width: 280px; margin-left: auto; }
        .totals table { width: 100%; }
        .totals td { padding: 6px 0; }
        .totals .label { text-align: left; color: #666; }
        .totals .value { text-align: right; font-weight: bold; }
        .total-row { border-top: 2px solid #333; font-size: 14px; }
        .total-row td { padding-top: 12px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 10px; color: #888; text-align: center; }
        .so-badge { background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 3px; display: inline-block; margin-bottom: 10px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='header-left'>
            <div class='logo'>OCSAPP</div>
            <div class='company-info'>OCS Distribution Services<br>Montreal, Quebec, Canada<br>support@ocsapp.ca</div>
        </div>
        <div class='header-right'>
            " . ($isSO ? "<div class='so-badge'>SALES ORDER</div><br>" : "") . "
            <div class='document-title'>{$title}</div>
            <div class='document-number'>{$docNumber}</div>
            <p>Date: {$orderDate}</p>
            " . ($isSO ? "<p>Accepted: {$acceptedDate}</p>" : "") . "
            <div class='status-badge'>{$statusLabel}</div>
        </div>
    </div>

    <div class='info-section'>
        <div class='info-box'>
            <h4>" . ($isSO ? 'Sold By' : 'Vendor') . "</h4>
            <p><strong>" . htmlspecialchars($order['supplier_name'] ?? '') . "</strong></p>
            " . (!empty($order['contact_name']) ? "<p>" . htmlspecialchars($order['contact_name']) . "</p>" : "") . "
            " . (!empty($order['supplier_address']) ? "<p>" . htmlspecialchars($order['supplier_address']) . "</p>" : "") . "
            " . (!empty($order['supplier_city']) ? "<p>" . htmlspecialchars($order['supplier_city'] . ', ' . ($order['supplier_province'] ?? '')) . "</p>" : "") . "
            " . (!empty($order['supplier_email']) ? "<p>" . htmlspecialchars($order['supplier_email']) . "</p>" : "") . "
        </div>
        <div class='info-box'>
            <h4>" . ($isSO ? 'Ship To' : 'Issued By') . "</h4>
            <p><strong>OCS Distribution Services</strong></p>
            <p>support@ocsapp.ca</p>
            <p>ocsapp.ca</p>
            <br>
            <p><strong>PO #:</strong> " . htmlspecialchars($order['po_number']) . "</p>
            " . ($isSO ? "<p><strong>SO #:</strong> " . htmlspecialchars($order['so_number']) . "</p>" : "") . "
            " . (!empty($order['dr_request_number']) ? "<p><strong>DR #:</strong> " . htmlspecialchars($order['dr_request_number'] ?? '') . "</p>" : "") . "
        </div>
    </div>

    <table class='items'>
        <thead>
            <tr>
                <th style='width:40px;'>#</th>
                <th>Product</th>
                <th style='width:70px; text-align:center;'>Qty</th>
                <th style='width:100px; text-align:right;'>Unit Cost</th>
                <th style='width:100px; text-align:right;'>Total</th>
            </tr>
        </thead>
        <tbody>{$itemsHtml}</tbody>
    </table>

    <div class='totals'>
        <table>
            <tr>
                <td class='label'>Subtotal</td>
                <td class='value'>\$" . number_format((float)($order['subtotal'] ?? $order['total_amount']), 2) . "</td>
            </tr>
            " . ((float)($order['tax_amount'] ?? 0) > 0 ? "
            <tr>
                <td class='label'>Tax</td>
                <td class='value'>\$" . number_format((float)$order['tax_amount'], 2) . "</td>
            </tr>" : "") . "
            <tr class='total-row'>
                <td class='label'><strong>Total</strong></td>
                <td class='value'><strong>\$" . number_format((float)($order['total_amount'] ?? 0), 2) . " CAD</strong></td>
            </tr>
        </table>
    </div>

    " . (!empty($order['notes']) ? "<div style='background:#f9f9f9;padding:14px;border-radius:5px;margin-top:24px;'><h4 style='margin:0 0 8px;color:#666;font-size:11px;text-transform:uppercase;'>Notes</h4><p style='margin:0;'>" . htmlspecialchars($order['notes']) . "</p></div>" : "") . "

    <div class='footer'>
        <p>OCS Distribution Services | ocsapp.ca | Generated on " . date('F j, Y \a\t g:i A') . "</p>
    </div>
</body>
</html>";

            // Render PDF via dompdf
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream($filename, ['Attachment' => true]);
            exit;

        } catch (\Exception $e) {
            logger("Supplier order PDF error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error generating PDF.');
            redirect(url('supplier/orders/view?id=' . $id));
        }
    }

    /**
     * View email history for this supplier
     */
    public function emails(): void
    {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Get supplier email
            $stmt = $db->prepare("SELECT email FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplierEmail = $stmt->fetchColumn();

            if (!$supplierEmail) {
                setFlash('error', 'Supplier not found.');
                redirect(url('supplier/dashboard'));
                return;
            }

            // Get all emails sent to this supplier
            $stmt = $db->prepare("
                SELECT id, recipient_email, subject, email_type, status, error_message, created_at
                FROM email_log
                WHERE recipient_email = ?
                ORDER BY created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$supplierEmail]);
            $emails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Stats
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    MIN(created_at) as first_email,
                    MAX(created_at) as last_email
                FROM email_log
                WHERE recipient_email = ?
            ");
            $stmt->execute([$supplierEmail]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('supplier.emails', [
                'emails' => $emails,
                'stats' => $stats,
                'pageTitle' => 'My Emails',
            ]);

        } catch (\PDOException $e) {
            logger("Supplier emails error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading email history');
            redirect(url('supplier/dashboard'));
        }
    }

    /**
     * View uploaded verification documents
     */
    public function documents(): void
    {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Get application linked to this supplier
            $stmt = $db->prepare("
                SELECT id, doc_certificate_incorporation, doc_declaration_registration, doc_enterprise_register,
                       doc_certificate_incorporation_status, doc_declaration_registration_status, doc_enterprise_register_status,
                       status, created_at, updated_at
                FROM supplier_applications
                WHERE supplier_id = ?
                ORDER BY id DESC LIMIT 1
            ");
            $stmt->execute([$supplierId]);
            $application = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Get agreement data from suppliers table
            $stmtAg = $db->prepare("SELECT agreement_agreed_at, agreement_version FROM suppliers WHERE id = ?");
            $stmtAg->execute([$supplierId]);
            $supplierRow = $stmtAg->fetch(\PDO::FETCH_ASSOC);

            // Fetch current published supplier agreement version
            $vStmt = $db->prepare("
                SELECT version FROM legal_content
                WHERE page_type = 'supplier_agreement' AND language = 'fr' AND is_published = 1
                LIMIT 1
            ");
            $vStmt->execute();
            $currentAgreementVersion = (int)($vStmt->fetchColumn() ?: 1);

            $docFields = [
                'doc_certificate_incorporation' => 'Certificate of Incorporation',
                'doc_declaration_registration' => 'Declaration of Registration',
                'doc_enterprise_register' => 'Enterprise Register File Search',
            ];

            $flash = $_SESSION['flash'] ?? null;
            unset($_SESSION['flash']);

            view('supplier.documents', [
                'application'             => $application,
                'docFields'               => $docFields,
                'pageTitle'               => 'My Documents',
                'supplierRow'             => $supplierRow,
                'currentAgreementVersion' => $currentAgreementVersion,
                'flash'                   => $flash,
            ]);

        } catch (\PDOException $e) {
            logger("Supplier documents error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading documents');
            redirect(url('supplier/dashboard'));
        }
    }

    /**
     * Stream Supplier Service Agreement as PDF (from legal_content)
     */
    public function agreementPdf(): void
    {
        $this->checkAuth();

        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT language, title, content, version
                FROM legal_content
                WHERE page_type = 'supplier_agreement' AND is_published = 1
                ORDER BY FIELD(language, 'fr', 'en')
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                header('HTTP/1.0 404 Not Found');
                echo 'Agreement not found';
                return;
            }

            $html = $this->pdfWrapper();
            foreach ($rows as $i => $row) {
                $langLabel = $row['language'] === 'fr' ? 'Version Francaise' : 'English Version';
                $html .= '<div class="lang-section">';
                $html .= '<div class="lang-label">' . htmlspecialchars($langLabel) . '</div>';
                $html .= '<h1>' . htmlspecialchars($row['title']) . '</h1>';
                $html .= $row['content'];
                $html .= '</div>';
                if ($i < count($rows) - 1) {
                    $html .= '<div class="page-break"></div>';
                }
            }
            $html .= '</body></html>';

            $dompdf = $this->makeDompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('OCSAPP-Supplier-Service-Agreement.pdf', ['Attachment' => false]);

        } catch (\Exception $e) {
            error_log('Supplier agreement PDF error: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error generating document';
        }
    }

    /**
     * Stream Supplier Onboarding Package as PDF (from planner_templates)
     */
    public function onboardingPdf(): void
    {
        $this->checkAuth();

        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT name, content FROM planner_templates
                WHERE slug = 'supplier-onboarding-package' AND is_active = 1
                LIMIT 1
            ");
            $stmt->execute();
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$template) {
                header('HTTP/1.0 404 Not Found');
                echo 'Onboarding package not found';
                return;
            }

            $html  = $this->pdfWrapper();
            $html .= '<div class="lang-section">' . $template['content'] . '</div>';
            $html .= '</body></html>';

            $dompdf = $this->makeDompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('OCSAPP-Supplier-Onboarding-Package.pdf', ['Attachment' => false]);

        } catch (\Exception $e) {
            error_log('Supplier onboarding PDF error: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error generating document';
        }
    }

    /**
     * Record the supplier's agreement confirmation
     */
    public function confirmAgreement(): void
    {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('supplier/documents'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Idempotent — skip if already signed
            $check = $db->prepare("SELECT agreement_agreed_at FROM suppliers WHERE id = ?");
            $check->execute([$supplierId]);
            if ($check->fetchColumn()) {
                redirect(url('supplier/documents'));
                return;
            }

            // Current published version
            $vStmt = $db->prepare("
                SELECT version FROM legal_content
                WHERE page_type = 'supplier_agreement' AND language = 'fr' AND is_published = 1
                LIMIT 1
            ");
            $vStmt->execute();
            $version = (int)($vStmt->fetchColumn() ?: 1);

            $ip = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '')[0]);

            $db->prepare("
                UPDATE suppliers
                SET agreement_agreed_at = NOW(),
                    agreement_ip        = ?,
                    agreement_version   = ?
                WHERE id = ?
            ")->execute([$ip, $version, $supplierId]);

            // Fetch supplier data for email + notification
            $sStmt = $db->prepare("SELECT email, company_name, contact_person FROM suppliers WHERE id = ?");
            $sStmt->execute([$supplierId]);
            $supplier = $sStmt->fetch(\PDO::FETCH_ASSOC);

            // Confirmation email
            if ($supplier) {
                $signedAt = date('Y-m-d H:i');
                \App\Helpers\EmailHelper::sendSupplierAgreementSigned([
                    'supplier_id'    => $supplierId,
                    'email'          => $supplier['email'],
                    'company_name'   => $supplier['company_name'],
                    'contact_person' => $supplier['contact_person'] ?: $supplier['company_name'],
                    'signed_at'      => $signedAt,
                    'version'        => $version,
                ]);

                // Bell notification (bilingual)
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supplierId,
                    'agreement_signed',
                    'Agreement Signed',
                    'Your Supplier Service Agreement has been recorded. Thank you!',
                    url('supplier/documents'),
                    'file-contract',
                    'Accord signé',
                    'Votre Accord de services fournisseur a été enregistré. Merci !'
                );
            }

            $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
            setFlash('success', $fr
                ? 'Accord signé avec succès. Bienvenue chez OCSAPP !'
                : 'Agreement signed successfully. Welcome to OCSAPP!');
            redirect(url('supplier/documents'));

        } catch (\PDOException $e) {
            logger("Supplier confirmAgreement error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error saving agreement. Please try again.');
            redirect(url('supplier/documents'));
        }
    }

    private function pdfWrapper(): string
    {
        $logoPath = BASE_PATH . '/public/assets/images/logo.png';
        $logoImg  = '';
        if (file_exists($logoPath)) {
            $logoImg = '<img src="data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) . '" style="height:38px;width:38px;">';
        }

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>

            @page { margin: 82px 52px 68px 52px; }

            body {
                font-family: Helvetica, Arial, sans-serif;
                font-size: 11pt;
                color: #1a1a1a;
                margin: 0; padding: 0;
            }

            /* ── Branded header — repeats on every page ── */
            #pdf-header {
                position: fixed;
                top: -72px; left: -52px; right: -52px;
                height: 62px;
                background: #00b207;
            }
            .hdr-inner {
                display: table;
                width: 100%;
                height: 62px;
                padding: 0 20px;
            }
            .hdr-logo  { display: table-cell; vertical-align: middle; width: 46px; }
            .hdr-name  {
                display: table-cell; vertical-align: middle;
                color: #ffffff; font-size: 15pt; font-weight: bold;
                padding-left: 10px; letter-spacing: 0.5px;
            }
            .hdr-right {
                display: table-cell; vertical-align: middle;
                text-align: right;
                color: rgba(255,255,255,0.88);
                font-size: 9pt;
            }

            /* ── Branded footer — repeats on every page ── */
            #pdf-footer {
                position: fixed;
                bottom: -58px; left: -52px; right: -52px;
                height: 44px;
                border-top: 2px solid #00b207;
                background: #f9fafb;
            }
            .ftr-inner {
                display: table;
                width: 100%;
                height: 44px;
                padding: 0 20px;
            }
            .ftr-left  {
                display: table-cell; vertical-align: middle;
                font-size: 8pt; color: #6b7280;
            }
            .ftr-right {
                display: table-cell; vertical-align: middle;
                text-align: right;
                font-size: 8pt; color: #6b7280;
            }

            /* ── Content ── */
            h1 { font-size: 17pt; color: #00b207; margin: 18px 0 6px; }
            h2 { font-size: 13pt; color: #1a5c2a; margin-top: 20px; margin-bottom: 4px; }
            h3 { font-size: 11pt; color: #374151; margin-top: 14px; }
            p, li { line-height: 1.65; margin-bottom: 6px; }
            ul, ol { padding-left: 20px; }

            .lang-section { padding: 16px 0 24px; }
            .lang-label {
                font-size: 8.5pt; font-weight: bold;
                color: #00b207; text-transform: uppercase;
                letter-spacing: 1.2px; margin-bottom: 14px;
                padding-bottom: 6px;
                border-bottom: 1px solid #bbf7d0;
            }
            .page-break { page-break-after: always; }

            table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
            td, th { border: 1px solid #d1d5db; padding: 7px 10px; font-size: 10pt; }
            th { background: #f0fdf4; font-weight: 600; color: #166534; }

        </style></head><body>

        <div id="pdf-header">
            <div class="hdr-inner">
                <div class="hdr-logo">' . $logoImg . '</div>
                <div class="hdr-name">OCS Marketplace</div>
                <div class="hdr-right">Supplier Portal<br><span style="font-size:8pt;opacity:0.8;">ocsapp.ca</span></div>
            </div>
        </div>

        <div id="pdf-footer">
            <div class="ftr-inner">
                <div class="ftr-left">Confidential &mdash; OCS Marketplace Inc. &mdash; ocsapp.ca</div>
                <div class="ftr-right">Supplier Portal</div>
            </div>
        </div>

        ';
    }

    private function makeDompdf(): \Dompdf\Dompdf
    {
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');
        return new \Dompdf\Dompdf($options);
    }

    /**
     * Upload or replace a verification document
     */
    public function uploadDocument(): void
    {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('supplier/documents'));
            return;
        }

        $docType = post('doc_type');
        $validTypes = ['doc_certificate_incorporation', 'doc_declaration_registration', 'doc_enterprise_register'];

        if (!in_array($docType, $validTypes, true)) {
            setFlash('error', 'Invalid document type.');
            redirect(url('supplier/documents'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Verify this supplier has a linked application
            $stmt = $db->prepare("SELECT id, lead_id FROM supplier_applications WHERE supplier_id = ? ORDER BY id DESC LIMIT 1");
            $stmt->execute([$supplierId]);
            $application = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$application) {
                setFlash('error', 'No application found for your account.');
                redirect(url('supplier/documents'));
                return;
            }

            // Validate file upload
            if (empty($_FILES['document']['tmp_name']) || !is_uploaded_file($_FILES['document']['tmp_name'])) {
                setFlash('error', 'Please select a file to upload.');
                redirect(url('supplier/documents'));
                return;
            }

            $file = $_FILES['document'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

            if ($file['size'] > $maxSize) {
                setFlash('error', 'File size must be less than 5MB.');
                redirect(url('supplier/documents'));
                return;
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedExts)) {
                setFlash('error', 'Only PDF, JPG, and PNG files are allowed.');
                redirect(url('supplier/documents'));
                return;
            }

            // Reject double extensions
            $filename = basename($file['name']);
            if (preg_match('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd)/i', pathinfo($filename, PATHINFO_FILENAME))) {
                logger("Suspicious supplier doc upload blocked: {$filename}", 'error');
                setFlash('error', 'Invalid file detected.');
                redirect(url('supplier/documents'));
                return;
            }

            // Validate MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimes, true)) {
                setFlash('error', 'Invalid file type detected.');
                redirect(url('supplier/documents'));
                return;
            }

            // Upload
            $uploadDir = 'uploads/supplier-applications';
            $fullUploadDir = BASE_PATH . '/public/' . $uploadDir;
            if (!is_dir($fullUploadDir)) {
                mkdir($fullUploadDir, 0755, true);
            }

            $safeFilename = 'supapp_' . uniqid('', true) . '_' . time() . '.' . $ext;
            $destPath = $fullUploadDir . '/' . $safeFilename;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                setFlash('error', 'Failed to upload file. Please try again.');
                redirect(url('supplier/documents'));
                return;
            }

            chmod($destPath, 0644);
            $relativePath = $uploadDir . '/' . $safeFilename;

            // Update the application record and reset review status to pending
            $statusCol = $docType . '_status';
            $stmt = $db->prepare("UPDATE supplier_applications SET {$docType} = ?, {$statusCol} = 'pending', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$relativePath, $application['id']]);

            // Log activity on the CRM lead if linked
            if (!empty($application['lead_id'])) {
                $docLabels = [
                    'doc_certificate_incorporation' => 'Certificate of Incorporation',
                    'doc_declaration_registration' => 'Declaration of Registration',
                    'doc_enterprise_register' => 'Enterprise Register File Search',
                ];
                $label = $docLabels[$docType] ?? $docType;
                $db->prepare("
                    INSERT INTO lead_activities (lead_id, activity_type, description, created_by)
                    VALUES (?, 'note', ?, NULL)
                ")->execute([
                    $application['lead_id'],
                    "Supplier uploaded document: {$label} (via portal)"
                ]);
            }

            // Notify admin that supplier uploaded a document
            $supplierName = '';
            $stmtName = $db->prepare("SELECT company_name FROM suppliers WHERE id = ?");
            $stmtName->execute([$supplierId]);
            $supplierName = $stmtName->fetchColumn() ?: "Supplier #{$supplierId}";

            \App\Helpers\NotificationHelper::add(
                'supplier',
                "Document Uploaded: {$supplierName}",
                "{$supplierName} uploaded {$label} via the supplier portal.",
                ['link' => !empty($application['lead_id']) ? "admin/leads/view?id={$application['lead_id']}" : 'admin/suppliers', 'icon' => 'file-upload', 'priority' => 'normal']
            );

            logger("Supplier #{$supplierId} uploaded {$docType}", 'info');
            setFlash('success', 'Document uploaded successfully.');
            redirect(url('supplier/documents'));

        } catch (\PDOException $e) {
            logger("Supplier document upload error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error uploading document. Please try again.');
            redirect(url('supplier/documents'));
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PICKUP SCHEDULING
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * GET /supplier/pickup
     * Show pickup scheduling page with accepted POs and request history
     */
    public function pickupIndex(): void {
        $supplierId = $this->checkAuth();

        try {
            $db = \Database::getConnection();

            // Accepted POs eligible for pickup (status = 'accepted')
            $poStmt = $db->prepare("
                SELECT po.id, po.po_number, po.total_amount, po.order_date,
                       COUNT(poi.id) AS item_count
                FROM purchase_orders po
                LEFT JOIN purchase_order_items poi ON poi.purchase_order_id = po.id
                WHERE po.supplier_id = ?
                  AND po.status = 'accepted'
                GROUP BY po.id
                ORDER BY po.order_date DESC
            ");
            $poStmt->execute([$supplierId]);
            $acceptedOrders = $poStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Pickup request history
            $histStmt = $db->prepare("
                SELECT spr.*
                FROM supplier_pickup_requests spr
                WHERE spr.supplier_id = ?
                ORDER BY spr.created_at DESC
                LIMIT 30
            ");
            $histStmt->execute([$supplierId]);
            $pickupHistory = $histStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Supplier address (pre-fill)
            $supStmt = $db->prepare("SELECT address, city, province, postal_code, country FROM suppliers WHERE id = ?");
            $supStmt->execute([$supplierId]);
            $supplierAddr = $supStmt->fetch(\PDO::FETCH_ASSOC);
            $defaultAddress = trim(implode(', ', array_filter([
                $supplierAddr['address'] ?? '',
                $supplierAddr['city'] ?? '',
                $supplierAddr['province'] ?? '',
                $supplierAddr['postal_code'] ?? '',
            ])));

            $flash = null;
            if (isset($_SESSION['flash'])) {
                $flash = $_SESSION['flash'];
                unset($_SESSION['flash']);
            }

            view('supplier.pickup', [
                'pageTitle'      => 'Schedule Pickup',
                'acceptedOrders' => $acceptedOrders,
                'pickupHistory'  => $pickupHistory,
                'defaultAddress' => $defaultAddress,
                'flash'          => $flash,
            ]);

        } catch (\PDOException $e) {
            logger("Supplier pickup index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading pickup page.');
            redirect(url('supplier/dashboard'));
        }
    }

    /**
     * POST /supplier/pickup/request
     * Submit a new pickup request
     */
    public function pickupRequest(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request.');
            redirect(url('supplier/pickup'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $poIds        = array_map('intval', (array) post('po_ids', []));
            $pickupAddr   = trim(post('pickup_address', ''));
            $reqDate      = post('requested_date', '');
            $timeFrom     = post('requested_time_from', '');
            $timeTo       = post('requested_time_to', '');
            $notes        = trim(post('notes', ''));

            // Validate required fields
            if (empty($poIds)) {
                setFlash('error', 'Please select at least one purchase order.');
                redirect(url('supplier/pickup'));
                return;
            }
            if (empty($pickupAddr)) {
                setFlash('error', 'Pickup address is required.');
                redirect(url('supplier/pickup'));
                return;
            }
            if (empty($reqDate) || strtotime($reqDate) < strtotime('tomorrow')) {
                setFlash('error', 'Pickup date must be at least tomorrow.');
                redirect(url('supplier/pickup'));
                return;
            }
            if (empty($timeFrom) || empty($timeTo) || $timeTo <= $timeFrom) {
                setFlash('error', 'Please provide a valid time window (From must be before To).');
                redirect(url('supplier/pickup'));
                return;
            }

            // Verify all selected POs belong to this supplier and are in 'accepted' status
            $placeholders = implode(',', array_fill(0, count($poIds), '?'));
            $verifyStmt = $db->prepare("
                SELECT COUNT(*) FROM purchase_orders
                WHERE id IN ({$placeholders}) AND supplier_id = ? AND status = 'accepted'
            ");
            $verifyStmt->execute([...$poIds, $supplierId]);
            if ((int) $verifyStmt->fetchColumn() !== count($poIds)) {
                setFlash('error', 'One or more selected orders are not eligible for pickup.');
                redirect(url('supplier/pickup'));
                return;
            }

            // Get supplier info for notification
            $supStmt = $db->prepare("SELECT company_name, name FROM suppliers WHERE id = ?");
            $supStmt->execute([$supplierId]);
            $sup = $supStmt->fetch(\PDO::FETCH_ASSOC);
            $companyName = $sup['company_name'] ?: $sup['name'];

            // Insert pickup request
            $db->prepare("
                INSERT INTO supplier_pickup_requests
                    (supplier_id, purchase_order_ids, pickup_address, requested_date,
                     requested_time_from, requested_time_to, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
            ")->execute([
                $supplierId,
                json_encode($poIds),
                $pickupAddr,
                $reqDate,
                $timeFrom,
                $timeTo,
                $notes ?: null,
            ]);

            // Admin bell notification
            try {
                \App\Helpers\NotificationHelper::add(
                    'pickup_request',
                    'Supplier Pickup Requested',
                    "{$companyName} requests pickup on " . date('M j, Y', strtotime($reqDate)) . " ({$timeFrom}–{$timeTo}). " . count($poIds) . " PO(s).",
                    ['link' => 'admin/pickup-requests', 'icon' => 'truck-loading', 'priority' => 'normal']
                );
            } catch (\Exception $e) {
                logger("Pickup request notification error: " . $e->getMessage(), 'error');
            }

            logger("Supplier #{$supplierId} submitted pickup request for POs: " . implode(',', $poIds), 'info');
            setFlash('success', 'Pickup request submitted. Our team will confirm the schedule shortly.');
            redirect(url('supplier/pickup'));

        } catch (\PDOException $e) {
            logger("Supplier pickup request error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error submitting pickup request. Please try again.');
            redirect(url('supplier/pickup'));
        }
    }

    /**
     * POST /supplier/pickup/cancel
     * Cancel a pending pickup request
     */
    public function pickupCancel(): void {
        $supplierId = $this->checkAuth();

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        try {
            $db = \Database::getConnection();
            $requestId = (int) post('request_id');

            $stmt = $db->prepare("
                UPDATE supplier_pickup_requests
                SET status = 'cancelled', cancelled_at = NOW()
                WHERE id = ? AND supplier_id = ? AND status = 'pending'
            ");
            $stmt->execute([$requestId, $supplierId]);

            if ($stmt->rowCount() === 0) {
                jsonResponse(['success' => false, 'message' => 'Request not found or cannot be cancelled'], 404);
                return;
            }

            logger("Supplier #{$supplierId} cancelled pickup request #{$requestId}", 'info');
            jsonResponse(['success' => true, 'message' => 'Pickup request cancelled']);

        } catch (\PDOException $e) {
            logger("Supplier pickup cancel error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error cancelling request'], 500);
        }
    }
}
