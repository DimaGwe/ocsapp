<?php

namespace App\Controllers;

class SupplierCatalogController {

    public function __construct() {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    public function browse(): void {
        try {
            $db = \Database::getConnection();

            // Get filters
            $search = get('search', '');
            $supplierId = get('supplier', '');
            $sortBy = get('sort', 'product_name');

            // Build query
            $where = ['s.status = "active"', 'sp.is_available = 1'];
            $params = [];

            if ($search) {
                $where[] = "(sp.product_name LIKE ? OR sp.sku LIKE ? OR s.name LIKE ? OR s.company_name LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($supplierId) {
                $where[] = "s.id = ?";
                $params[] = $supplierId;
            }

            $whereClause = implode(' AND ', $where);

            // Valid sort options
            $validSort = [
                'product_name' => 'sp.product_name ASC',
                'supplier' => 's.name ASC, sp.product_name ASC',
                'price_low' => 'sp.unit_price ASC',
                'price_high' => 'sp.unit_price DESC',
                'lead_time' => 'sp.lead_time_days ASC'
            ];
            $orderBy = $validSort[$sortBy] ?? 'sp.product_name ASC';

            // Get products
            $stmt = $db->prepare("
                SELECT sp.*,
                       s.id as supplier_id,
                       s.name as supplier_name,
                       s.company_name as supplier_company_name,
                       p.name as marketplace_product_name,
                       p.total_stock as marketplace_stock
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                LEFT JOIN products p ON sp.marketplace_product_id = p.id
                WHERE {$whereClause}
                ORDER BY {$orderBy}
            ");
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            // Get all active suppliers for filter dropdown
            $stmt = $db->query("SELECT id, name, company_name FROM suppliers WHERE status = 'active' ORDER BY name ASC");
            $suppliers = $stmt->fetchAll();

            // Get draft items count
            $draftCount = 0;
            if (isset($_SESSION['draft_po_items'])) {
                $draftCount = count($_SESSION['draft_po_items']);
            }

            view('admin.supplier-catalog.browse', [
                'products' => $products,
                'suppliers' => $suppliers,
                'search' => $search,
                'supplierId' => $supplierId,
                'sortBy' => $sortBy,
                'draftCount' => $draftCount,
                'currentPage' => 'supplier-catalog'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier catalog browse error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading supplier catalog');
            redirect(url('admin/dashboard'));
        }
    }

    public function addToDraft(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $productId = (int) post('product_id');
            $quantity = (int) post('quantity', 1);

            if (!$productId || $quantity < 1) {
                jsonResponse(['success' => false, 'message' => 'Invalid product or quantity'], 400);
            }

            // Get product details
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT sp.*, s.name as supplier_name, s.company_name
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE sp.id = ? AND sp.is_available = 1
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }

            // Initialize draft if not exists
            if (!isset($_SESSION['draft_po_items'])) {
                $_SESSION['draft_po_items'] = [];
            }

            // Add or update item
            $itemKey = "sp_{$productId}";
            if (isset($_SESSION['draft_po_items'][$itemKey])) {
                $_SESSION['draft_po_items'][$itemKey]['quantity'] += $quantity;
            } else {
                $_SESSION['draft_po_items'][$itemKey] = [
                    'supplier_product_id' => $productId,
                    'supplier_id' => $product['supplier_id'],
                    'supplier_name' => $product['supplier_name'] ?? $product['company_name'],
                    'product_name' => $product['product_name'],
                    'sku' => $product['sku'],
                    'unit_price' => $product['unit_price'],
                    'unit' => $product['unit'],
                    'quantity' => $quantity,
                    'minimum_order_quantity' => $product['minimum_order_quantity']
                ];
            }

            $draftCount = count($_SESSION['draft_po_items']);

            jsonResponse([
                'success' => true,
                'message' => 'Added to draft',
                'draft_count' => $draftCount
            ]);

        } catch (\PDOException $e) {
            logger("Add to draft error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error adding to draft'], 500);
        }
    }

    public function viewDraft(): void {
        $draftItems = $_SESSION['draft_po_items'] ?? [];

        // Group by supplier
        $itemsBySupplier = [];
        foreach ($draftItems as $item) {
            $supplierId = $item['supplier_id'];
            if (!isset($itemsBySupplier[$supplierId])) {
                $itemsBySupplier[$supplierId] = [
                    'supplier_name' => $item['supplier_name'],
                    'items' => []
                ];
            }
            $itemsBySupplier[$supplierId]['items'][] = $item;
        }

        view('admin.supplier-catalog.draft', [
            'itemsBySupplier' => $itemsBySupplier,
            'currentPage' => 'supplier-catalog'
        ]);
    }

    public function updateDraftItem(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $productId = (int) post('product_id');
            $quantity = (int) post('quantity');

            $itemKey = "sp_{$productId}";

            if (!isset($_SESSION['draft_po_items'][$itemKey])) {
                jsonResponse(['success' => false, 'message' => 'Item not found'], 404);
            }

            if ($quantity < 1) {
                // Remove item
                unset($_SESSION['draft_po_items'][$itemKey]);
            } else {
                $_SESSION['draft_po_items'][$itemKey]['quantity'] = $quantity;
            }

            jsonResponse([
                'success' => true,
                'message' => 'Draft updated',
                'draft_count' => count($_SESSION['draft_po_items'] ?? [])
            ]);

        } catch (\Exception $e) {
            logger("Update draft error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating draft'], 500);
        }
    }

    public function removeDraftItem(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $productId = (int) post('product_id');
            $itemKey = "sp_{$productId}";

            if (isset($_SESSION['draft_po_items'][$itemKey])) {
                unset($_SESSION['draft_po_items'][$itemKey]);
            }

            jsonResponse([
                'success' => true,
                'message' => 'Item removed',
                'draft_count' => count($_SESSION['draft_po_items'] ?? [])
            ]);

        } catch (\Exception $e) {
            logger("Remove draft item error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error removing item'], 500);
        }
    }

    public function clearDraft(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $_SESSION['draft_po_items'] = [];

        jsonResponse(['success' => true, 'message' => 'Draft cleared']);
    }

    public function createPOFromDraft(): void {
        try {
            $draftItems = $_SESSION['draft_po_items'] ?? [];

            if (empty($draftItems)) {
                setFlash('error', 'Draft is empty');
                redirect(url('admin/supplier-catalog/draft'));
            }

            // Group items by supplier
            $itemsBySupplier = [];
            foreach ($draftItems as $item) {
                $supplierId = $item['supplier_id'];
                if (!isset($itemsBySupplier[$supplierId])) {
                    $itemsBySupplier[$supplierId] = [];
                }
                $itemsBySupplier[$supplierId][] = $item;
            }

            // Store in session for PO creation
            $_SESSION['po_from_draft'] = $itemsBySupplier;

            // Clear draft
            $_SESSION['draft_po_items'] = [];

            setFlash('success', 'Draft items ready. Create purchase orders for each supplier below.');
            redirect(url('admin/supplier-catalog/create-pos'));

        } catch (\Exception $e) {
            logger("Create PO from draft error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error creating purchase orders');
            back();
        }
    }

    public function createPOs(): void {
        $itemsBySupplier = $_SESSION['po_from_draft'] ?? [];

        if (empty($itemsBySupplier)) {
            setFlash('error', 'No draft items found');
            redirect(url('admin/supplier-catalog/browse'));
        }

        view('admin.supplier-catalog.create-pos', [
            'itemsBySupplier' => $itemsBySupplier,
            'currentPage' => 'supplier-catalog'
        ]);
    }

    public function storeAllFromDraft(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $itemsBySupplier = $_SESSION['po_from_draft'] ?? [];

        if (empty($itemsBySupplier)) {
            setFlash('error', 'No draft items found');
            redirect(url('admin/supplier-catalog'));
            return;
        }

        try {
            $db = \Database::getConnection();
            $db->beginTransaction();

            // Get next PO number base
            $stmt = $db->query("SELECT po_number FROM purchase_orders ORDER BY id DESC LIMIT 1");
            $lastPO = $stmt->fetch();
            $nextNumber = 1;
            if ($lastPO && preg_match('/PO-(\d+)/', $lastPO['po_number'], $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }

            $createdCount = 0;
            $createdPOIds = [];

            foreach ($itemsBySupplier as $supplierId => $items) {
                $poNumber = 'PO-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
                $orderDate = date('Y-m-d');

                // Calculate subtotal
                $subtotal = 0;
                foreach ($items as $item) {
                    $subtotal += ($item['quantity'] * $item['unit_price']);
                }

                // Insert purchase order
                $stmt = $db->prepare("
                    INSERT INTO purchase_orders (
                        po_number, supplier_id, order_date, expected_delivery_date,
                        status, subtotal, tax_amount, shipping_cost, total_amount, notes, created_by
                    ) VALUES (?, ?, ?, NULL, 'draft', ?, 0, 0, ?, NULL, ?)
                ");
                $stmt->execute([
                    $poNumber,
                    $supplierId,
                    $orderDate,
                    $subtotal,
                    $subtotal,
                    $_SESSION['user']['id'] ?? 0
                ]);

                $poId = $db->lastInsertId();
                $createdPOIds[] = $poId;

                // Insert items
                $itemStmt = $db->prepare("
                    INSERT INTO purchase_order_items (
                        purchase_order_id, product_id, quantity_ordered, unit_cost, total_cost
                    ) VALUES (?, ?, ?, ?, ?)
                ");

                foreach ($items as $item) {
                    $qty = (int)$item['quantity'];
                    $cost = (float)$item['unit_price'];
                    $total = $qty * $cost;
                    $itemStmt->execute([$poId, $item['supplier_product_id'], $qty, $cost, $total]);
                }

                // Draft POs don't send email — email is sent when admin changes status to 'sent'

                $createdCount++;
                $nextNumber++;
            }

            $db->commit();

            // Clear session data
            unset($_SESSION['po_from_draft']);

            setFlash('success', "{$createdCount} purchase order(s) created successfully!");
            redirect(url('admin/purchase-orders'));

        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Store all POs error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error creating purchase orders: ' . $e->getMessage());
            redirect(url('admin/supplier-catalog/create-pos'));
        }
    }

    // -------------------------------------------------------------------------
    // GET /admin/supplier-catalog/alternatives?product_id=X
    // Returns JSON: current explicit backups + auto-discovered alternatives
    // -------------------------------------------------------------------------
    public function getAlternatives(): void {
        $db = \Database::getConnection();
        $productId = (int) get('product_id', 0);

        if (!$productId) {
            jsonResponse(['success' => false, 'message' => 'Product ID required'], 400);
        }

        try {
            // Get the primary product info
            $stmt = $db->prepare("
                SELECT sp.id, sp.product_name, sp.unit_price, sp.unit, sp.sku,
                       sp.stock_quantity, sp.marketplace_product_id,
                       s.name as supplier_name, s.company_name as supplier_company
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE sp.id = ?
            ");
            $stmt->execute([$productId]);
            $primary = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$primary) {
                jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }

            // Get explicit alternatives (admin-configured)
            $stmt = $db->prepare("
                SELECT spa.id as mapping_id, spa.priority, spa.notes,
                       sp.id as supplier_product_id, sp.product_name, sp.unit_price,
                       sp.unit, sp.sku, sp.stock_quantity,
                       s.name as supplier_name, s.company_name as supplier_company
                FROM supplier_product_alternatives spa
                JOIN supplier_products sp ON spa.alternative_supplier_product_id = sp.id
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE spa.supplier_product_id = ?
                ORDER BY spa.priority ASC
            ");
            $stmt->execute([$productId]);
            $explicit = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Auto-discovered: other suppliers with same marketplace_product_id
            $autoDiscovered = [];
            if (!empty($primary['marketplace_product_id'])) {
                $explicitIds = array_column($explicit, 'supplier_product_id');
                $explicitIds[] = $productId; // exclude primary itself

                $placeholders = implode(',', array_fill(0, count($explicitIds), '?'));
                $stmt = $db->prepare("
                    SELECT sp.id as supplier_product_id, sp.product_name, sp.unit_price,
                           sp.unit, sp.sku, sp.stock_quantity,
                           s.name as supplier_name, s.company_name as supplier_company
                    FROM supplier_products sp
                    LEFT JOIN suppliers s ON sp.supplier_id = s.id
                    WHERE sp.marketplace_product_id = ?
                    AND sp.id NOT IN ($placeholders)
                    AND sp.is_available = 1
                    AND s.status = 'active'
                    ORDER BY sp.unit_price ASC
                ");
                $params = array_merge([$primary['marketplace_product_id']], $explicitIds);
                $stmt->execute($params);
                $autoDiscovered = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // All available products for the "add backup" dropdown (exclude already mapped + self)
            $excludeIds = array_merge(
                array_column($explicit, 'supplier_product_id'),
                array_column($autoDiscovered, 'supplier_product_id'),
                [$productId]
            );
            $placeholders = implode(',', array_fill(0, count($excludeIds), '?'));
            $stmt = $db->prepare("
                SELECT sp.id, sp.product_name, sp.unit_price, sp.unit, sp.sku,
                       s.name as supplier_name, s.company_name as supplier_company
                FROM supplier_products sp
                LEFT JOIN suppliers s ON sp.supplier_id = s.id
                WHERE sp.id NOT IN ($placeholders)
                AND sp.is_available = 1
                AND s.status = 'active'
                ORDER BY s.name ASC, sp.product_name ASC
            ");
            $stmt->execute($excludeIds);
            $available = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            jsonResponse([
                'success'        => true,
                'primary'        => $primary,
                'explicit'       => $explicit,
                'auto_discovered' => $autoDiscovered,
                'available'      => $available,
            ]);

        } catch (\PDOException $e) {
            logger("getAlternatives error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error loading alternatives'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /admin/supplier-catalog/alternatives/add
    // -------------------------------------------------------------------------
    public function addAlternative(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $db = \Database::getConnection();
        $primaryId     = (int) post('supplier_product_id');
        $alternativeId = (int) post('alternative_supplier_product_id');
        $notes         = sanitize(post('notes', ''));

        if (!$primaryId || !$alternativeId || $primaryId === $alternativeId) {
            jsonResponse(['success' => false, 'message' => 'Invalid product IDs'], 400);
        }

        try {
            // Get next priority number
            $stmt = $db->prepare("
                SELECT COALESCE(MAX(priority), 0) + 1 as next_priority
                FROM supplier_product_alternatives
                WHERE supplier_product_id = ?
            ");
            $stmt->execute([$primaryId]);
            $nextPriority = (int) $stmt->fetchColumn();

            $stmt = $db->prepare("
                INSERT INTO supplier_product_alternatives
                    (supplier_product_id, alternative_supplier_product_id, priority, notes, created_by)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$primaryId, $alternativeId, $nextPriority, $notes ?: null, userId()]);

            jsonResponse(['success' => true, 'message' => 'Backup supplier added']);

        } catch (\PDOException $e) {
            if ($e->getCode() === '23000') {
                jsonResponse(['success' => false, 'message' => 'This alternative is already mapped'], 409);
            }
            logger("addAlternative error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error adding alternative'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /admin/supplier-catalog/alternatives/remove
    // -------------------------------------------------------------------------
    public function removeAlternative(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $db = \Database::getConnection();
        $mappingId = (int) post('mapping_id');

        if (!$mappingId) {
            jsonResponse(['success' => false, 'message' => 'Mapping ID required'], 400);
        }

        try {
            $stmt = $db->prepare("DELETE FROM supplier_product_alternatives WHERE id = ?");
            $stmt->execute([$mappingId]);

            jsonResponse(['success' => true, 'message' => 'Backup removed']);

        } catch (\PDOException $e) {
            logger("removeAlternative error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error removing alternative'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /admin/supplier-catalog/alternatives/reorder
    // Body: { supplier_product_id, order: [mappingId1, mappingId2, ...] }
    // -------------------------------------------------------------------------
    public function reorderAlternatives(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $db  = \Database::getConnection();
        $raw = json_decode(file_get_contents('php://input'), true);
        $order = $raw['order'] ?? [];

        if (empty($order) || !is_array($order)) {
            jsonResponse(['success' => false, 'message' => 'Order array required'], 400);
        }

        try {
            $stmt = $db->prepare("
                UPDATE supplier_product_alternatives SET priority = ? WHERE id = ?
            ");
            foreach ($order as $priority => $mappingId) {
                $stmt->execute([$priority + 1, (int) $mappingId]);
            }

            jsonResponse(['success' => true, 'message' => 'Order updated']);

        } catch (\PDOException $e) {
            logger("reorderAlternatives error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error reordering'], 500);
        }
    }
}
