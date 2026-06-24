<?php

namespace App\Controllers;

class AdminSalesController {

    /**
     * Sales Management Dashboard
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();

            // Get all products on sale
            $stmt = $db->query("
                SELECT p.*,
                       pi.image_path,
                       c.name as category_name,
                       b.name as brand_name,
                       CONCAT(u1.first_name, ' ', u1.last_name) as created_by_user,
                       CONCAT(u2.first_name, ' ', u2.last_name) as updated_by_user
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN users u1 ON p.created_by = u1.id
                LEFT JOIN users u2 ON p.updated_by = u2.id
                WHERE p.is_on_sale = 1
                ORDER BY p.sale_percentage DESC, p.created_at DESC
            ");
            $saleProducts = $stmt->fetchAll();

            // Get upcoming scheduled sales
            $stmt = $db->query("
                SELECT p.*,
                       pi.image_path,
                       c.name as category_name
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.sale_start_date > NOW()
                  AND p.sale_price IS NOT NULL
                ORDER BY p.sale_start_date ASC
                LIMIT 10
            ");
            $upcomingSales = $stmt->fetchAll();

            // Get expired sales (ended in last 7 days)
            $stmt = $db->query("
                SELECT p.*,
                       pi.image_path,
                       c.name as category_name
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.sale_end_date < NOW()
                  AND p.sale_end_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY p.sale_end_date DESC
                LIMIT 10
            ");
            $expiredSales = $stmt->fetchAll();

            // Statistics
            $stmt = $db->query("
                SELECT
                    COUNT(*) as total_sale_products,
                    AVG(sale_percentage) as avg_discount,
                    SUM(CASE WHEN sale_start_date > NOW() THEN 1 ELSE 0 END) as upcoming_count,
                    SUM(CASE WHEN sale_end_date < NOW() AND sale_end_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as expired_count
                FROM products
                WHERE is_on_sale = 1 OR sale_price IS NOT NULL
            ");
            $stats = $stmt->fetch();

            view('admin.sales.index', [
                'saleProducts' => $saleProducts,
                'upcomingSales' => $upcomingSales,
                'expiredSales' => $expiredSales,
                'stats' => $stats,
                'currentPage' => 'sales'
            ]);

        } catch (\PDOException $e) {
            logger("Sales index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading sales');
            redirect(url('admin/dashboard'));
        }
    }

    /**
     * Create sale page
     */
    public function create(): void {
        try {
            $db = \Database::getConnection();

            // Get all active products not on sale
            $stmt = $db->query("
                SELECT p.id, p.name, p.base_price, c.name as category_name, p.sku
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.status = 'active' AND p.is_on_sale = 0
                ORDER BY p.name ASC
            ");
            $products = $stmt->fetchAll();

            // Get categories for filtering
            $stmt = $db->query("SELECT id, name FROM categories ORDER BY name ASC");
            $categories = $stmt->fetchAll();

            view('admin.sales.create', [
                'products' => $products,
                'categories' => $categories,
                'currentPage' => 'sales'
            ]);

        } catch (\PDOException $e) {
            logger("Sales create page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading create sale page');
            redirect(url('admin/sales'));
        }
    }

    /**
     * Store new sale(s)
     */
    public function store(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();

            $productIds = post('product_ids', []);
            $discountType = post('discount_type', 'percentage'); // percentage or fixed
            $discountValue = (float) post('discount_value', 0);
            $startDate = post('start_date');
            $endDate = post('end_date');
            $startImmediately = post('start_immediately', 0);

            if (empty($productIds)) {
                setFlash('error', 'Please select at least one product');
                back();
            }

            if ($discountValue <= 0) {
                setFlash('error', 'Discount value must be greater than 0');
                back();
            }

            // Convert to array if single value
            if (!is_array($productIds)) {
                $productIds = [$productIds];
            }

            $updated = 0;
            $userId = $_SESSION['user_id'] ?? null;

            foreach ($productIds as $productId) {
                // Get product base price
                $stmt = $db->prepare("SELECT base_price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch();

                if (!$product) continue;

                $basePrice = $product['base_price'];

                // Calculate sale price
                if ($discountType === 'percentage') {
                    $salePrice = $basePrice * (1 - ($discountValue / 100));
                    $percentage = $discountValue;
                } else {
                    $salePrice = $basePrice - $discountValue;
                    $percentage = round((($basePrice - $salePrice) / $basePrice) * 100, 2);
                }

                // Ensure sale price is positive
                if ($salePrice < 0) $salePrice = 0.01;

                // Determine if sale should be active now
                $isOnSale = $startImmediately ? 1 : 0;
                if (!$startImmediately && $startDate) {
                    $isOnSale = (strtotime($startDate) <= time()) ? 1 : 0;
                }

                // Update product
                $stmt = $db->prepare("
                    UPDATE products
                    SET sale_price = ?,
                        sale_percentage = ?,
                        is_on_sale = ?,
                        sale_start_date = ?,
                        sale_end_date = ?,
                        updated_by = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    round($salePrice, 2),
                    $percentage,
                    $isOnSale,
                    $startImmediately ? null : $startDate,
                    $endDate,
                    $userId,
                    $productId
                ]);

                $updated++;
            }

            logger("Created sale for {$updated} products", 'info');
            setFlash('success', "Sale created for {$updated} product(s) successfully");
            redirect(url('admin/sales'));

        } catch (\PDOException $e) {
            logger("Sales store error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error creating sale');
            back();
        }
    }

    /**
     * Edit sale
     */
    public function edit(): void {
        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid product ID');
                redirect(url('admin/sales'));
            }

            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT p.*, c.name as category_name, b.name as brand_name
                FROM products p
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('admin/sales'));
            }

            view('admin.sales.edit', [
                'product' => $product,
                'currentPage' => 'sales'
            ]);

        } catch (\PDOException $e) {
            logger("Sales edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading sale');
            redirect(url('admin/sales'));
        }
    }

    /**
     * Update sale
     */
    public function update(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $id = (int) post('id');
            $salePrice = (float) post('sale_price');
            $startDate = post('start_date');
            $endDate = post('end_date');
            $isOnSale = post('is_on_sale', 0);

            if (!$id) {
                setFlash('error', 'Invalid product ID');
                back();
            }

            $db = \Database::getConnection();

            // Get base price to calculate percentage
            $stmt = $db->prepare("SELECT base_price FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                back();
            }

            $basePrice = $product['base_price'];
            $percentage = round((($basePrice - $salePrice) / $basePrice) * 100, 2);

            $stmt = $db->prepare("
                UPDATE products
                SET sale_price = ?,
                    sale_percentage = ?,
                    is_on_sale = ?,
                    sale_start_date = ?,
                    sale_end_date = ?,
                    updated_by = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $salePrice,
                $percentage,
                $isOnSale,
                $startDate,
                $endDate,
                $_SESSION['user_id'] ?? null,
                $id
            ]);

            setFlash('success', 'Sale updated successfully');
            redirect(url('admin/sales'));

        } catch (\PDOException $e) {
            logger("Sales update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating sale');
            back();
        }
    }

    /**
     * End sale (remove from sale)
     */
    public function endSale(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid product ID'], 400);
            }

            $db = \Database::getConnection();
            $stmt = $db->prepare("
                UPDATE products
                SET is_on_sale = 0,
                    sale_end_date = NOW(),
                    updated_by = ?
                WHERE id = ?
            ");

            $stmt->execute([$_SESSION['user_id'] ?? null, $id]);

            logger("Ended sale for product ID: {$id}", 'info');
            jsonResponse(['success' => true, 'message' => 'Sale ended successfully']);

        } catch (\PDOException $e) {
            logger("End sale error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error ending sale'], 500);
        }
    }

    /**
     * Process scheduled sales (cron job endpoint)
     * This should be called by a cron job or scheduled task
     */
    public function processScheduledSales(): void {
        try {
            $db = \Database::getConnection();

            // Start sales that should begin now
            $stmt = $db->prepare("
                UPDATE products
                SET is_on_sale = 1
                WHERE sale_start_date IS NOT NULL
                  AND sale_start_date <= NOW()
                  AND is_on_sale = 0
                  AND sale_price IS NOT NULL
            ");
            $stmt->execute();
            $started = $stmt->rowCount();

            // End sales that should end now
            $stmt = $db->prepare("
                UPDATE products
                SET is_on_sale = 0
                WHERE sale_end_date IS NOT NULL
                  AND sale_end_date <= NOW()
                  AND is_on_sale = 1
            ");
            $stmt->execute();
            $ended = $stmt->rowCount();

            logger("Scheduled sales processed: {$started} started, {$ended} ended", 'info');

            echo json_encode([
                'success' => true,
                'started' => $started,
                'ended' => $ended,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

        } catch (\PDOException $e) {
            logger("Process scheduled sales error: " . $e->getMessage(), 'error');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
