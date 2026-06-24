<?php

namespace App\Controllers;

/**
 * InventoryController
 * Seller inventory management — add/edit/delete products in their shop_inventory.
 */
class InventoryController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
        if (!isLoggedIn() || !hasRole('seller')) {
            setFlash('error', 'Seller account required.');
            redirect(url('login'));
            exit;
        }
    }

    private function getSellerShop(): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM shops WHERE seller_id = ? AND is_active = 1 LIMIT 1"
        );
        $stmt->execute([userId()]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * GET /seller/inventory — list inventory items for this seller's shop
     */
    public function index(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            setFlash('info', 'Please set up your shop first.');
            redirect(url('seller/shop/create'));
            return;
        }

        $stmt = $this->db->prepare("
            SELECT si.*, p.name AS product_name, p.sku,
                   (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image_path
            FROM shop_inventory si
            JOIN products p ON si.product_id = p.id
            WHERE si.shop_id = ?
            ORDER BY p.name ASC
        ");
        $stmt->execute([$shop['id']]);
        $inventory = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/index', ['shop' => $shop, 'inventory' => $inventory]);
    }

    /**
     * GET /seller/inventory/add — form to add an existing product to inventory
     */
    public function add(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        // Products not already in this shop's inventory
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.sku
            FROM products p
            WHERE p.id NOT IN (SELECT product_id FROM shop_inventory WHERE shop_id = ?)
            AND p.status = 'active'
            ORDER BY p.name ASC
        ");
        $stmt->execute([$shop['id']]);
        $availableProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/add', ['shop' => $shop, 'availableProducts' => $availableProducts]);
    }

    /**
     * POST /seller/inventory/store — add product to shop_inventory
     */
    public function store(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token.');
            redirect(url('seller/inventory/add'));
            return;
        }

        $productId = intval(post('product_id', 0));
        $price     = floatval(post('price', 0));
        $stock     = intval(post('stock_quantity', 0));

        if ($productId <= 0 || $price <= 0) {
            setFlash('error', 'Product and price are required.');
            redirect(url('seller/inventory/add'));
            return;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
                ON DUPLICATE KEY UPDATE price = VALUES(price), stock_quantity = VALUES(stock_quantity), updated_at = NOW()
            ");
            $stmt->execute([$shop['id'], $productId, $price, $stock]);
            setFlash('success', 'Product added to inventory.');
        } catch (\PDOException $e) {
            logger("InventoryController::store() failed: " . $e->getMessage(), 'error');
            setFlash('error', 'Could not add product. Please try again.');
        }

        redirect(url('seller/inventory'));
    }

    /**
     * GET /seller/inventory/edit?id=X — form to edit an inventory item
     */
    public function edit(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        $inventoryId = intval($_GET['id'] ?? 0);
        $stmt = $this->db->prepare("
            SELECT si.*, p.name AS product_name, p.sku
            FROM shop_inventory si
            JOIN products p ON si.product_id = p.id
            WHERE si.id = ? AND si.shop_id = ?
        ");
        $stmt->execute([$inventoryId, $shop['id']]);
        $item = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$item) {
            setFlash('error', 'Inventory item not found.');
            redirect(url('seller/inventory'));
            return;
        }

        view('seller/inventory/edit', ['shop' => $shop, 'item' => $item]);
    }

    /**
     * POST /seller/inventory/update — update price/stock
     */
    public function update(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token.');
            redirect(url('seller/inventory'));
            return;
        }

        $inventoryId = intval(post('inventory_id', 0));
        $price       = floatval(post('price', 0));
        $stock       = intval(post('stock_quantity', 0));
        $status      = in_array(post('status', 'active'), ['active', 'inactive']) ? post('status', 'active') : 'active';

        try {
            $stmt = $this->db->prepare("
                UPDATE shop_inventory
                SET price = ?, stock_quantity = ?, status = ?, updated_at = NOW()
                WHERE id = ? AND shop_id = ?
            ");
            $stmt->execute([$price, $stock, $status, $inventoryId, $shop['id']]);
            setFlash('success', 'Inventory updated.');
        } catch (\PDOException $e) {
            logger("InventoryController::update() failed: " . $e->getMessage(), 'error');
            setFlash('error', 'Update failed. Please try again.');
        }

        redirect(url('seller/inventory'));
    }

    /**
     * POST /seller/inventory/delete — remove item from inventory
     */
    public function delete(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/dashboard'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token.');
            redirect(url('seller/inventory'));
            return;
        }

        $inventoryId = intval(post('inventory_id', 0));

        try {
            $stmt = $this->db->prepare(
                "DELETE FROM shop_inventory WHERE id = ? AND shop_id = ?"
            );
            $stmt->execute([$inventoryId, $shop['id']]);
            setFlash('success', 'Item removed from inventory.');
        } catch (\PDOException $e) {
            logger("InventoryController::delete() failed: " . $e->getMessage(), 'error');
            setFlash('error', 'Could not remove item.');
        }

        redirect(url('seller/inventory'));
    }

    /**
     * GET /seller/inventory/create-product — form to create a brand new product
     */
    public function createProduct(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        // Fetch categories for the form
        $stmt = $this->db->query("SELECT id, name FROM categories WHERE status = 'active' ORDER BY name");
        $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('seller/inventory/create-product', ['shop' => $shop, 'categories' => $categories]);
    }

    /**
     * POST /seller/inventory/store-product — create new product and add to inventory
     */
    public function storeProduct(): void
    {
        $shop = $this->getSellerShop();
        if (!$shop) {
            redirect(url('seller/shop/create'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token.');
            redirect(url('seller/inventory/create-product'));
            return;
        }

        $name        = sanitize(post('name', ''));
        $description = sanitize(post('description', ''));
        $categoryId  = intval(post('category_id', 0));
        $price       = floatval(post('price', 0));
        $stock       = intval(post('stock_quantity', 0));
        $sku         = sanitize(post('sku', ''));
        $weight      = floatval(post('weight', 0));

        if (empty($name) || $price <= 0 || $categoryId <= 0) {
            setFlash('error', 'Product name, category, and price are required.');
            redirect(url('seller/inventory/create-product'));
            return;
        }

        try {
            $this->db->beginTransaction();

            // Generate a unique slug from the product name
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name)) . '-' . time();

            // Create product — categories live in product_categories join table
            $stmt = $this->db->prepare("
                INSERT INTO products (name, slug, description, sku, weight, base_price, product_type, seller_id, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 'seller', ?, 'active', NOW(), NOW())
            ");
            $stmt->execute([$name, $slug, $description, $sku ?: null, $weight ?: null, $price, userId()]);
            $productId = $this->db->lastInsertId();

            // Link category via join table
            if ($categoryId > 0) {
                $this->db->prepare("
                    INSERT INTO product_categories (product_id, category_id, is_primary, created_at)
                    VALUES (?, ?, 1, NOW())
                ")->execute([$productId, $categoryId]);
            }

            // Add to this shop's inventory
            $stmt2 = $this->db->prepare("
                INSERT INTO shop_inventory (shop_id, product_id, price, stock_quantity, status, created_at, updated_at)
                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
            ");
            $stmt2->execute([$shop['id'], $productId, $price, $stock]);

            $this->db->commit();
            setFlash('success', 'Product created and added to your inventory.');
        } catch (\PDOException $e) {
            $this->db->rollBack();
            logger("InventoryController::storeProduct() failed: " . $e->getMessage(), 'error');
            setFlash('error', 'Could not create product. Please try again.');
        }

        redirect(url('seller/inventory'));
    }
}
