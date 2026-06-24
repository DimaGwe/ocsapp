<?php

namespace App\Controllers;

class PurchaseOrderController {

    public function __construct() {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    public function index(): void {
        try {
            $db = \Database::getConnection();
            $status = get('status', '');
            $search = get('search', '');

            $where = ['1=1'];
            $params = [];

            if ($status) {
                $where[] = "po.status = ?";
                $params[] = $status;
            }

            if ($search) {
                $where[] = "(po.po_number LIKE ? OR s.name LIKE ? OR s.company_name LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $whereClause = implode(' AND ', $where);

            $stmt = $db->prepare("
                SELECT po.*,
                       s.name as supplier_name,
                       s.company_name as supplier_company_name,
                       COUNT(poi.id) as item_count,
                       SUM(poi.quantity_ordered) as total_items
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id
                WHERE {$whereClause}
                GROUP BY po.id
                ORDER BY po.created_at DESC
            ");
            $stmt->execute($params);
            $purchaseOrders = $stmt->fetchAll();

            view('admin.purchase-orders.index', [
                'purchaseOrders' => $purchaseOrders,
                'status' => $status,
                'search' => $search,
                'currentPage' => 'purchase-orders',
            ]);
        } catch (\PDOException $e) {
            logger("Purchase orders list error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading purchase orders');
            back();
        }
    }

    public function create(): void {
        try {
            $db = \Database::getConnection();

            // Get active suppliers with contact info for info card
            $stmt = $db->query("SELECT id, name, company_name, contact_person, email, phone, address, city, province, postal_code, payment_terms FROM suppliers WHERE status = 'active' ORDER BY name ASC");
            $suppliers = $stmt->fetchAll();

            // Get supplier products with supplier info — always use unit_price (single price across all platforms)
            $stmt = $db->query("
                SELECT sp.id, sp.supplier_id, sp.product_name, sp.sku,
                       sp.unit_price as cost_price,
                       sp.unit_price, sp.unit, sp.minimum_order_quantity, sp.stock_quantity, sp.lead_time_days,
                       s.name as supplier_name, s.company_name
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE sp.is_available = 1 AND s.status = 'active'
                ORDER BY s.name ASC, sp.product_name ASC
            ");
            $supplierProducts = $stmt->fetchAll();

            // Generate next PO number
            $stmt = $db->query("SELECT po_number FROM purchase_orders ORDER BY id DESC LIMIT 1");
            $lastPO = $stmt->fetch();
            $nextNumber = 1;

            if ($lastPO && preg_match('/PO-(\d+)/', $lastPO['po_number'], $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }

            $nextPONumber = 'PO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

            view('admin.purchase-orders.create', [
                'suppliers' => $suppliers,
                'supplierProducts' => $supplierProducts,
                'nextPONumber' => $nextPONumber,
                'currentPage' => 'purchase-orders',
            ]);
        } catch (\PDOException $e) {
            logger("Purchase order create page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading purchase order form');
            redirect(url('admin/purchase-orders'));
        }
    }

    public function store(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();
            $db->beginTransaction();

            // Validate items
            $supplierProductIds = post('supplier_product_ids', []);
            $quantities = post('quantities', []);
            $unitCosts = post('unit_costs', []);

            if (empty($supplierProductIds) || count($supplierProductIds) === 0) {
                throw new \Exception('Please add at least one product to the purchase order');
            }

            // Validate stock availability
            foreach ($supplierProductIds as $index => $productId) {
                if (!empty($productId) && !empty($quantities[$index])) {
                    $qty = (int)$quantities[$index];
                    $stockStmt = $db->prepare("SELECT product_name, stock_quantity FROM supplier_products WHERE id = ?");
                    $stockStmt->execute([(int)$productId]);
                    $sp = $stockStmt->fetch(\PDO::FETCH_ASSOC);
                    if ($sp && (int)$sp['stock_quantity'] > 0 && $qty > (int)$sp['stock_quantity']) {
                        throw new \Exception("Cannot order {$qty} of \"{$sp['product_name']}\" — only {$sp['stock_quantity']} in stock.");
                    }
                }
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($supplierProductIds as $index => $productId) {
                if (!empty($productId) && !empty($quantities[$index]) && !empty($unitCosts[$index])) {
                    $qty = (int)$quantities[$index];
                    $cost = (float)$unitCosts[$index];
                    $subtotal += ($qty * $cost);
                }
            }

            $shippingCost = (float)post('shipping_cost', 0);
            $taxGst = round($subtotal * 0.05, 2);
            $taxQst = round($subtotal * 0.09975, 2);
            $taxAmount = $taxGst + $taxQst;
            $totalAmount = $subtotal + $shippingCost + $taxAmount;

            // Insert purchase order
            $stmt = $db->prepare("
                INSERT INTO purchase_orders (
                    po_number, supplier_id, order_date, expected_delivery_date,
                    status, subtotal, tax_gst, tax_qst, tax_amount, shipping_cost, total_amount, notes, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                post('po_number'),
                post('supplier_id'),
                post('order_date'),
                post('expected_delivery_date') ?: null,
                post('status', 'draft'),
                $subtotal,
                $taxGst,
                $taxQst,
                $taxAmount,
                $shippingCost,
                $totalAmount,
                post('notes'),
                $_SESSION['user']['id'] ?? 0
            ]);

            $poId = $db->lastInsertId();

            // Insert purchase order items (product_id references supplier_products table)
            $itemNotes = post('item_notes', []);
            $itemStmt = $db->prepare("
                INSERT INTO purchase_order_items (
                    purchase_order_id, product_id, quantity_ordered, unit_cost, total_cost, notes
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");

            foreach ($supplierProductIds as $index => $productId) {
                if (!empty($productId) && !empty($quantities[$index]) && !empty($unitCosts[$index])) {
                    $qty = (int)$quantities[$index];
                    $cost = (float)$unitCosts[$index];
                    $total = $qty * $cost;
                    $note = sanitize($itemNotes[$index] ?? '');

                    $itemStmt->execute([$poId, $productId, $qty, $cost, $total, $note ?: null]);
                }
            }

            $db->commit();

            // Only send email + notifications when PO is sent (not draft)
            $poStatus = post('status', 'draft');
            if ($poStatus === 'sent') {
                try {
                    // Get full PO details for email
                    $stmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = ?");
                    $stmt->execute([$poId]);
                    $po = $stmt->fetch();

                    // Get supplier details
                    $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
                    $stmt->execute([$po['supplier_id']]);
                    $supplier = $stmt->fetch();

                    // Get items with details
                    $stmt = $db->prepare("
                        SELECT poi.*, sp.product_name, sp.sku
                        FROM purchase_order_items poi
                        LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                        WHERE poi.purchase_order_id = ?
                    ");
                    $stmt->execute([$poId]);
                    $items = $stmt->fetchAll();

                    // Send email notification
                    \App\Helpers\EmailHelper::sendPurchaseOrderCreated($po, $supplier, $items);

                    // Admin bell notification
                    \App\Helpers\NotificationHelper::add(
                        'new_order',
                        'Purchase Order Sent',
                        "PO #{$po['po_number']} sent to " . ($supplier['company_name'] ?? 'supplier') . " — $" . number_format($po['total_amount'], 2),
                        [
                            'link' => '/admin/purchase-orders/view?id=' . $poId,
                            'icon' => 'file-invoice',
                            'priority' => 'normal',
                        ]
                    );

                    // Supplier bell notification
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        (int)$po['supplier_id'],
                        'purchase_order',
                        'New Purchase Order',
                        "PO #{$po['po_number']} has been sent to you — $" . number_format($po['total_amount'], 2) . ". Please review and accept or decline.",
                        '/supplier/orders',
                        'file-invoice',
                        'Nouveau bon de commande',
                        "BC #{$po['po_number']} vous a été envoyé — " . number_format($po['total_amount'], 2) . ' $. Veuillez l\'examiner et l\'accepter ou le refuser.'
                    );
                } catch (\Exception $emailError) {
                    logger("Failed to send PO creation email: " . $emailError->getMessage(), 'error');
                }
            }

            setFlash('success', 'Purchase order created successfully');
            redirect(url('admin/purchase-orders/view?id=' . $poId));
        } catch (\Exception $e) {
            $db->rollBack();
            logger("Purchase order create error: " . $e->getMessage(), 'error');
            setFlash('error', $e->getMessage());
            back();
        }
    }

    public function view(): void {
        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid purchase order ID');
                redirect(url('admin/purchase-orders'));
            }

            $db = \Database::getConnection();

            // Get purchase order with supplier info
            $stmt = $db->prepare("
                SELECT po.*,
                       s.name as supplier_name,
                       s.company_name as supplier_company_name,
                       s.email as supplier_email,
                       s.phone as supplier_phone,
                       s.address as supplier_address,
                       s.city as supplier_city,
                       s.province as supplier_province,
                       s.postal_code as supplier_postal_code
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $stmt->execute([$id]);
            $po = $stmt->fetch();

            if (!$po) {
                setFlash('error', 'Purchase order not found');
                redirect(url('admin/purchase-orders'));
            }

            // Get purchase order items with supplier product info
            $stmt = $db->prepare("
                SELECT poi.*,
                       sp.product_name,
                       sp.sku as product_sku,
                       sp.unit,
                       sp.unit_price
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE poi.purchase_order_id = ?
                ORDER BY poi.id ASC
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll();

            // Fetch active delivery drivers for assignment modal
            $driverStmt = $db->query("
                SELECT u.id, u.first_name, u.last_name,
                       COALESCE(da.status, 'offline') AS availability
                FROM users u
                LEFT JOIN driver_availability da ON da.driver_id = u.id
                WHERE u.role = 'delivery' AND u.status = 'active'
                ORDER BY u.first_name ASC
            ");
            $drivers = $driverStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Assigned driver name (if any)
            $assignedDriverName = null;
            if (!empty($po['assigned_driver_id'])) {
                $dN = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
                $dN->execute([$po['assigned_driver_id']]);
                $dRow = $dN->fetch(\PDO::FETCH_ASSOC);
                if ($dRow) {
                    $assignedDriverName = trim($dRow['first_name'] . ' ' . $dRow['last_name']);
                }
            }

            // Driver message log for this PO
            $msgStmt = $db->prepare("
                SELECT ddn.id, ddn.message, ddn.type, ddn.read_at, ddn.created_at,
                       u.first_name, u.last_name
                FROM driver_delivery_notifications ddn
                LEFT JOIN users u ON u.id = ddn.sent_by
                WHERE ddn.po_id = ?
                ORDER BY ddn.created_at DESC
            ");
            $msgStmt->execute([$id]);
            $driverMessages = $msgStmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin.purchase-orders.view', [
                'po' => $po,
                'items' => $items,
                'drivers' => $drivers,
                'assignedDriverName' => $assignedDriverName,
                'driverMessages' => $driverMessages,
                'currentPage' => 'purchase-orders',
            ]);
        } catch (\PDOException $e) {
            logger("Purchase order view error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading purchase order');
            redirect(url('admin/purchase-orders'));
        }
    }

    public function receive(): void {
        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid purchase order ID');
                redirect(url('admin/purchase-orders'));
            }

            $db = \Database::getConnection();

            // Get purchase order
            $stmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = ?");
            $stmt->execute([$id]);
            $po = $stmt->fetch();

            if (!$po) {
                setFlash('error', 'Purchase order not found');
                redirect(url('admin/purchase-orders'));
            }

            // Get purchase order items with supplier product info
            $stmt = $db->prepare("
                SELECT poi.*,
                       sp.product_name,
                       sp.sku as product_sku,
                       sp.unit,
                       sp.unit_price
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE poi.purchase_order_id = ?
                ORDER BY poi.id ASC
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll();

            view('admin.purchase-orders.receive', [
                'po' => $po,
                'items' => $items,
                'currentPage' => 'purchase-orders',
            ]);
        } catch (\PDOException $e) {
            logger("Purchase order receive page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading receive form');
            redirect(url('admin/purchase-orders'));
        }
    }

    public function processReceiving(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $poId = (int) post('po_id');
            $receivedQuantities = post('received_quantities', []);

            if (!$poId) {
                throw new \Exception('Invalid purchase order ID');
            }

            $db = \Database::getConnection();
            $db->beginTransaction();

            // Get PO items with supplier product info and marketplace product mapping
            $stmt = $db->prepare("
                SELECT poi.*,
                       sp.product_name,
                       sp.sku,
                       sp.marketplace_product_id,
                       p.name as marketplace_product_name,
                       p.total_stock as current_stock
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                LEFT JOIN products p ON sp.marketplace_product_id = p.id
                WHERE poi.purchase_order_id = ?
            ");
            $stmt->execute([$poId]);
            $items = $stmt->fetchAll();

            $allFullyReceived = true;

            foreach ($items as $item) {
                $itemId = $item['id'];
                $receivedQty = isset($receivedQuantities[$itemId]) ? (int)$receivedQuantities[$itemId] : 0;
                $newReceived = (int)$item['quantity_received'] + $receivedQty;

                if ($receivedQty > 0) {
                    // Update received quantity
                    $stmt = $db->prepare("UPDATE purchase_order_items SET quantity_received = ? WHERE id = ?");
                    $stmt->execute([$newReceived, $itemId]);

                    // Update stock if supplier product is linked to marketplace product
                    if ($item['marketplace_product_id']) {
                        $marketplaceProductId = $item['marketplace_product_id'];

                        // Update product stock
                        $newStock = ($item['current_stock'] ?? 0) + $receivedQty;
                        $stmt = $db->prepare("UPDATE products SET total_stock = ? WHERE id = ?");
                        $stmt->execute([$newStock, $marketplaceProductId]);

                        // Update shop_inventory for OCS Store (shop_id = 1)
                        $stmt = $db->prepare("
                            UPDATE shop_inventory
                            SET stock_quantity = stock_quantity + ?,
                                allocated_quantity = allocated_quantity + ?
                            WHERE shop_id = 1 AND product_id = ?
                        ");
                        $stmt->execute([$receivedQty, $receivedQty, $marketplaceProductId]);

                        logger("Stock updated for product #{$marketplaceProductId}: added {$receivedQty} units (new total: {$newStock})", 'info');
                    } else {
                        logger("Supplier product '{$item['product_name']}' is not linked to a marketplace product - stock not updated", 'warning');
                    }
                }

                // Check if this item is fully received
                if ($newReceived < $item['quantity_ordered']) {
                    $allFullyReceived = false;
                }
            }

            // Update PO status
            if ($allFullyReceived) {
                $stmt = $db->prepare("
                    UPDATE purchase_orders
                    SET status = 'completed', actual_delivery_date = CURDATE()
                    WHERE id = ?
                ");
            } else {
                $stmt = $db->prepare("
                    UPDATE purchase_orders
                    SET status = 'receiving'
                    WHERE id = ?
                ");
            }
            $stmt->execute([$poId]);

            $db->commit();

            // Send email notification if PO is now completed
            if ($allFullyReceived) {
                try {
                    // Get full PO details
                    $stmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = ?");
                    $stmt->execute([$poId]);
                    $po = $stmt->fetch();

                    // Get supplier details
                    $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
                    $stmt->execute([$po['supplier_id']]);
                    $supplier = $stmt->fetch();

                    // Send completion notification
                    \App\Helpers\EmailHelper::sendPurchaseOrderCompleted($po, $supplier);

                    // Admin bell notification
                    \App\Helpers\NotificationHelper::add(
                        'new_order',
                        "PO #{$po['po_number']} — Completed",
                        "All items received for PO #{$po['po_number']} from " . ($supplier['company_name'] ?? 'supplier') . ". Stock updated.",
                        ['link' => '/admin/purchase-orders/view?id=' . $poId, 'icon' => 'check-double', 'priority' => 'normal']
                    );

                    // Supplier bell notification
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        (int)$po['supplier_id'], 'purchase_order', "PO #{$po['po_number']} Completed",
                        "PO #{$po['po_number']} has been completed. All items received. Thank you!",
                        '/supplier/orders', 'check-double',
                        "BC #{$po['po_number']} complété",
                        "BC #{$po['po_number']} a été complété. Tous les articles ont été reçus. Merci !"
                    );
                } catch (\Exception $emailError) {
                    logger("Failed to send PO completion email: " . $emailError->getMessage(), 'error');
                }
            }

            setFlash('success', 'Stock received and updated successfully');
            redirect(url('admin/purchase-orders/view?id=' . $poId));
        } catch (\Exception $e) {
            $db->rollBack();
            logger("Purchase order receiving error: " . $e->getMessage(), 'error');
            setFlash('error', $e->getMessage());
            back();
        }
    }

    public function updateStatus(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            $status = post('status');

            if (!$id || !$status) {
                jsonResponse(['success' => false, 'message' => 'Invalid parameters'], 400);
            }

            $validStatuses = ['draft', 'sent', 'accepted', 'preparing', 'ready_for_pickup', 'picked_up', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
            }

            $db = \Database::getConnection();

            // Get current PO with supplier info before updating
            $stmt = $db->prepare("
                SELECT po.*, s.company_name AS supplier_name, s.email AS supplier_email, s.contact_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $stmt->execute([$id]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po) {
                jsonResponse(['success' => false, 'message' => 'Purchase order not found'], 404);
            }

            $oldStatus = $po['status'];

            // Update status
            $stmt = $db->prepare("UPDATE purchase_orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);

            // If cancelling a PO that the supplier already accepted, re-increment their stock
            $stockReleasedStatuses = ['accepted', 'preparing', 'ready_for_pickup'];
            if ($status === 'cancelled' && in_array($oldStatus, $stockReleasedStatuses)) {
                try {
                    $itemsStmt = $db->prepare("SELECT product_id, quantity_ordered FROM purchase_order_items WHERE purchase_order_id = ?");
                    $itemsStmt->execute([$id]);
                    foreach ($itemsStmt->fetchAll(\PDO::FETCH_ASSOC) as $item) {
                        $db->prepare("
                            UPDATE supplier_products
                            SET stock_quantity = COALESCE(stock_quantity, 0) + ?,
                                is_available = 1
                            WHERE id = ? AND supplier_id = ?
                        ")->execute([$item['quantity_ordered'], $item['product_id'], $po['supplier_id']]);
                    }
                    logger("Stock re-incremented for PO #{$po['po_number']} cancellation (was {$oldStatus})", 'info');
                } catch (\Exception $e) {
                    logger("Stock re-increment error on PO cancel: " . $e->getMessage(), 'error');
                }
            }

            // Send email + bell notifications for status changes (skip if same status or draft)
            if ($oldStatus !== $status && $status !== 'draft') {
                $supplier = [
                    'name' => $po['supplier_name'],
                    'company_name' => $po['supplier_name'],
                    'email' => $po['supplier_email'],
                ];

                $statusLabels = [
                    'sent' => 'Sent', 'receiving' => 'Accepted / Receiving',
                    'completed' => 'Completed', 'cancelled' => 'Cancelled',
                ];
                $statusLabel = $statusLabels[$status] ?? ucfirst($status);

                // --- Emails ---
                if ($status === 'sent' && $oldStatus === 'draft') {
                    // Draft → Sent: use the detailed PO created email with items
                    try {
                        $itemsStmt = $db->prepare("
                            SELECT poi.*, sp.product_name, sp.sku
                            FROM purchase_order_items poi
                            LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                            WHERE poi.purchase_order_id = ?
                        ");
                        $itemsStmt->execute([$id]);
                        $items = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);
                        \App\Helpers\EmailHelper::sendPurchaseOrderCreated($po, $supplier, $items);
                    } catch (\Exception $e) {
                        logger("PO email error: " . $e->getMessage(), 'error');
                    }
                } elseif ($status === 'completed') {
                    \App\Helpers\EmailHelper::sendPurchaseOrderCompleted($po, $supplier);
                } else {
                    \App\Helpers\EmailHelper::sendPurchaseOrderStatusUpdate($po, $supplier, $oldStatus, $status);
                }

                // --- Admin bell notification ---
                \App\Helpers\NotificationHelper::add(
                    'new_order',
                    "PO #{$po['po_number']} — {$statusLabel}",
                    "Purchase order for " . ($po['supplier_name'] ?? 'supplier') . " is now {$statusLabel}. Total: $" . number_format($po['total_amount'], 2),
                    [
                        'link' => '/admin/purchase-orders/view?id=' . $id,
                        'icon' => 'file-invoice',
                        'priority' => $status === 'cancelled' ? 'high' : 'normal',
                    ]
                );

                // --- Supplier bell notification ---
                $supplierMessages = [
                    'sent'      => "New PO #{$po['po_number']} — please review and accept or decline.",
                    'receiving' => "PO #{$po['po_number']} is now in receiving status.",
                    'completed' => "PO #{$po['po_number']} has been completed. Thank you!",
                    'cancelled' => "PO #{$po['po_number']} has been cancelled.",
                ];
                $supplierMessagesFr = [
                    'sent'      => "Nouveau BC #{$po['po_number']} — veuillez l'examiner et l'accepter ou le refuser.",
                    'receiving' => "BC #{$po['po_number']} est maintenant en statut de réception.",
                    'completed' => "BC #{$po['po_number']} a été complété. Merci !",
                    'cancelled' => "BC #{$po['po_number']} a été annulé.",
                ];
                $statusLabelsFr = [
                    'sent'      => 'Envoyé',
                    'receiving' => 'En réception',
                    'completed' => 'Complété',
                    'cancelled' => 'Annulé',
                ];
                $statusLabelFr = $statusLabelsFr[$status] ?? ucfirst($status);
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    (int)$po['supplier_id'],
                    'purchase_order',
                    "PO #{$po['po_number']} — {$statusLabel}",
                    $supplierMessages[$status] ?? "PO #{$po['po_number']} status updated to {$statusLabel}.",
                    '/supplier/orders',
                    'file-invoice',
                    "BC #{$po['po_number']} — {$statusLabelFr}",
                    $supplierMessagesFr[$status] ?? "Le statut de BC #{$po['po_number']} a été mis à jour : {$statusLabelFr}."
                );
            }

            jsonResponse(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\PDOException $e) {
            logger("Purchase order status update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }

    /**
     * Admin assigns a driver to pick up a supplier PO (AJAX — returns JSON)
     */
    public function assignPickupDriver(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $poId     = (int) post('po_id');
            $driverId = (int) post('driver_id');
            $notes    = sanitize(post('notes', ''));

            if (!$poId || !$driverId) {
                jsonResponse(['success' => false, 'message' => 'Missing required fields'], 400);
            }

            $db = \Database::getConnection();

            // Get PO + supplier address
            $stmt = $db->prepare("
                SELECT po.*, s.company_name AS supplier_name, s.email AS supplier_email,
                       s.address AS supplier_address, s.city AS supplier_city,
                       s.province AS supplier_province, s.postal_code AS supplier_postal
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $stmt->execute([$poId]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po) {
                jsonResponse(['success' => false, 'message' => 'Purchase order not found'], 404);
            }

            if ($po['status'] !== 'ready_for_pickup') {
                jsonResponse(['success' => false, 'message' => 'Order is not in ready-for-pickup status'], 400);
            }

            // Get driver name
            $dStmt = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ? AND status = 'active'");
            $dStmt->execute([$driverId]);
            $driver = $dStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$driver) {
                jsonResponse(['success' => false, 'message' => 'Driver not found'], 404);
            }

            $driverName  = trim($driver['first_name'] . ' ' . $driver['last_name']);
            $pickupAddr  = trim(implode(', ', array_filter([
                $po['supplier_address'] ?? '',
                $po['supplier_city']    ?? '',
                $po['supplier_province'] ?? '',
                $po['supplier_postal']  ?? '',
            ])));

            $db->beginTransaction();

            // Assign driver on the PO
            $db->prepare("
                UPDATE purchase_orders
                SET assigned_driver_id = ?, driver_assigned_at = NOW()
                WHERE id = ?
            ")->execute([$driverId, $poId]);

            // Notify the assigned driver via admin bell (they see admin_notifications)
            \App\Helpers\NotificationHelper::add(
                'delivery',
                "📦 Supplier Pickup Assigned — PO #{$po['po_number']}",
                "You have been assigned to pick up PO #{$po['po_number']} from {$po['supplier_name']}."
                    . ($pickupAddr ? " Address: {$pickupAddr}." : '')
                    . ($notes ? " Notes: {$notes}" : ''),
                ['link' => "/admin/purchase-orders/view?id={$poId}", 'icon' => 'truck', 'priority' => 'high', 'user_id' => $driverId]
            );

            // Notify supplier
            \App\Helpers\NotificationHelper::addSupplierNotification(
                (int) $po['supplier_id'],
                'purchase_order',
                "Driver Assigned — PO #{$po['po_number']}",
                "Driver {$driverName} has been assigned to pick up your order. Please have it ready.",
                'supplier/orders/view?id=' . $poId,
                'truck',
                "Chauffeur assigné — BC #{$po['po_number']}",
                "Le chauffeur {$driverName} a été assigné pour collecter votre commande. Veuillez vous assurer qu'elle est prête."
            );

            $db->commit();

            // Email supplier that driver is coming
            if (!empty($po['supplier_email'])) {
                try {
                    \App\Helpers\EmailHelper::sendSupplierDriverAssigned([
                        'supplier_email' => $po['supplier_email'],
                        'supplier_name'  => $po['supplier_name'] ?? 'Supplier',
                        'driver_name'    => $driverName,
                        'po_number'      => $po['po_number'],
                        'order_total'    => $po['total_amount'] ?? 0,
                        'pickup_notes'   => $notes,
                    ]);
                } catch (\Exception $e) {
                    error_log('Supplier driver-assigned email error: ' . $e->getMessage());
                }
            }

            // FCM push to driver's ODA app
            try {
                \App\Controllers\Api\DriverApiController::sendPush(
                    $db,
                    $driverId,
                    '📦 Supplier Pickup Assigned',
                    "PO #{$po['po_number']} — Pick up from {$po['supplier_name']}." . ($pickupAddr ? " $pickupAddr" : ''),
                    ['type' => 'pickup', 'po_id' => (string)$poId]
                );
            } catch (\Exception $e) {
                error_log('Driver FCM push error: ' . $e->getMessage());
            }

            logger("Admin assigned driver #{$driverId} ({$driverName}) to PO #{$po['po_number']}", 'info');
            jsonResponse([
                'success'     => true,
                'message'     => "Driver {$driverName} assigned successfully.",
                'driver_name' => $driverName,
            ]);

        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) $db->rollBack();
            logger("assignPickupDriver error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error assigning driver'], 500);
        }
    }

    /**
     * Admin marks a PO as picked up by driver
     */
    public function markPickedUp(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $poId = (int) post('po_id');
            if (!$poId) {
                jsonResponse(['success' => false, 'message' => 'Invalid PO ID'], 400);
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT po.id, po.po_number, po.status, po.supplier_id, po.assigned_driver_id,
                       s.company_name AS supplier_name
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.id
                WHERE po.id = ?
            ");
            $stmt->execute([$poId]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po || $po['status'] !== 'ready_for_pickup') {
                jsonResponse(['success' => false, 'message' => 'Order cannot be marked as picked up'], 400);
            }

            $db->prepare("UPDATE purchase_orders SET status = 'picked_up', updated_at = NOW() WHERE id = ?")
               ->execute([$poId]);

            // Admin notification
            \App\Helpers\NotificationHelper::add(
                'new_order',
                "PO #{$po['po_number']} — Picked Up",
                "Driver has picked up PO #{$po['po_number']} from {$po['supplier_name']}.",
                ['link' => "/admin/purchase-orders/view?id={$poId}", 'icon' => 'shipping-fast', 'priority' => 'normal']
            );

            // Supplier notification
            \App\Helpers\NotificationHelper::addSupplierNotification(
                (int) $po['supplier_id'],
                'purchase_order',
                "PO #{$po['po_number']} Picked Up",
                "Your order has been collected by the driver and is on its way.",
                'supplier/orders/view?id=' . $poId,
                'shipping-fast',
                "BC #{$po['po_number']} collecté",
                'Votre commande a été récupérée par le chauffeur et est en route.'
            );

            logger("Admin marked PO #{$po['po_number']} as picked_up", 'info');
            jsonResponse(['success' => true, 'message' => 'Order marked as picked up.']);

        } catch (\Exception $e) {
            logger("markPickedUp error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }

    public function updateTax(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();
            $id = (int) post('po_id');

            $stmt = $db->prepare("SELECT id, po_number, subtotal, shipping_cost, status FROM purchase_orders WHERE id = ?");
            $stmt->execute([$id]);
            $po = $stmt->fetch();

            if (!$po) {
                setFlash('error', 'Purchase order not found');
                redirect(url('admin/purchase-orders'));
                return;
            }

            if (!in_array($po['status'], ['draft', 'sent'])) {
                setFlash('error', 'Tax can only be updated on draft or sent purchase orders');
                redirect(url('admin/purchase-orders/view?id=' . $id));
                return;
            }

            $subtotal = (float)$po['subtotal'];
            $taxGst   = round($subtotal * 0.05, 2);
            $taxQst   = round($subtotal * 0.09975, 2);
            $taxAmount = $taxGst + $taxQst;
            $newTotal  = $subtotal + (float)$po['shipping_cost'] + $taxAmount;

            $db->prepare("UPDATE purchase_orders SET tax_gst = ?, tax_qst = ?, tax_amount = ?, total_amount = ?, updated_at = NOW() WHERE id = ?")
               ->execute([$taxGst, $taxQst, $taxAmount, $newTotal, $id]);

            logger("Admin applied GST+QST on PO #{$po['po_number']}: gst={$taxGst}, qst={$taxQst}, total={$newTotal}", 'info');
            setFlash('success', "Taxes applied - new total: " . currencySymbol() . number_format($newTotal, 2));
            redirect(url('admin/purchase-orders/view?id=' . $id));

        } catch (\Exception $e) {
            logger("updateTax error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating tax');
            back();
        }
    }

    // -------------------------------------------------------------------------
    // POST /admin/purchase-orders/{id}/notify-driver
    // Send an in-app + push notification to the driver assigned to a PO
    // -------------------------------------------------------------------------
    public function notifyPickupDriver(int $poId): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
            exit;
        }

        $message = trim(post('message', ''));
        $type    = post('type', 'info');

        if (!in_array($type, ['info', 'warning', 'urgent'])) $type = 'info';
        if (!$message) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Message is required']);
            exit;
        }

        try {
            $db = \Database::getConnection();

            // Load PO + driver info
            $stmt = $db->prepare(
                "SELECT po.id, po.po_number, po.assigned_driver_id, po.status,
                        u.first_name, u.last_name
                 FROM purchase_orders po
                 LEFT JOIN users u ON u.id = po.assigned_driver_id
                 WHERE po.id = ? LIMIT 1"
            );
            $stmt->execute([$poId]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po) {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Purchase order not found']);
                exit;
            }

            $driverId = (int)($po['assigned_driver_id'] ?? 0);
            if (!$driverId) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'No driver assigned to this PO']);
                exit;
            }

            $sentBy = $_SESSION['user']['id'] ?? 0;

            // Insert notification
            $db->prepare(
                "INSERT INTO driver_delivery_notifications (driver_id, po_id, message, type, sent_by, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())"
            )->execute([$driverId, $poId, $message, $type, $sentBy]);

            // Send push notification
            $pushTitle = match($type) {
                'urgent'  => 'Urgent — PO #' . $po['po_number'],
                'warning' => 'PO #' . $po['po_number'],
                default   => 'PO #' . $po['po_number'],
            };
            \App\Controllers\Api\DriverApiController::sendPush(
                $db, $driverId, $pushTitle, $message,
                ['type' => $type, 'po_id' => (string)$poId]
            );

            logger("Admin notified driver #{$driverId} for PO #{$po['po_number']}: [{$type}] {$message}", 'info');

            echo json_encode(['success' => true]);
            exit;

        } catch (\Exception $e) {
            logger("notifyPickupDriver error: " . $e->getMessage(), 'error');
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
            exit;
        }
    }
}
