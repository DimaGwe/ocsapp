<?php

namespace App\Controllers;

use Database;

class PromoBannerController
{
    /**
     * Display all promo banners
     */
    public function index(): void
    {
        $db = Database::getConnection();

        $stmt = $db->query("
            SELECT *
            FROM promo_banners
            ORDER BY sort_order ASC, id ASC
        ");

        $banners = $stmt->fetchAll();

        view('admin/promo-banners/index', [
            'banners' => $banners,
            'pageTitle' => 'Promo Banners Management'
        ]);
    }

    /**
     * Show edit form for a promo banner
     */
    public function edit(): void
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            setFlash('error', 'Invalid promo banner ID');
            redirect('/admin/promo-banners');
            return;
        }

        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            setFlash('error', 'Promo banner not found');
            redirect('/admin/promo-banners');
            return;
        }

        // Decode selected_products JSON
        $selectedProducts = json_decode($banner['selected_products'] ?? '[]', true) ?: [];

        // Fetch OCS store products for dropdown
        $ocsProducts = $this->getOcsProducts();

        view('admin/promo-banners/edit', [
            'banner' => $banner,
            'selectedProducts' => $selectedProducts,
            'ocsProducts' => $ocsProducts,
            'pageTitle' => 'Edit Promo Banner'
        ]);
    }

    /**
     * Update a promo banner
     */
    public function update(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $id = post('id');
        $title = post('title');
        $subtitle = post('subtitle');
        $discountPercentage = post('discount_percentage', 20);
        $selectedProducts = post('selected_products', []); // Array of product IDs
        $buttonText = post('button_text', 'Shop Now');
        $buttonUrl = post('button_url', '/deals');
        $sortOrder = post('sort_order', 0);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($title)) {
            setFlash('error', 'Title is required');
            back();
            return;
        }

        // Validate discount percentage
        if (!is_numeric($discountPercentage) || $discountPercentage < 0 || $discountPercentage > 100) {
            setFlash('error', 'Discount percentage must be between 0 and 100');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $db = Database::getConnection();

        // Check if banner exists
        $stmt = $db->prepare("SELECT id FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            setFlash('error', 'Promo banner not found');
            redirect('/admin/promo-banners');
            return;
        }

        // Encode selected products as JSON
        $selectedProductsJson = json_encode($selectedProducts);

        // Update banner
        $stmt = $db->prepare("
            UPDATE promo_banners
            SET title = ?,
                subtitle = ?,
                discount_percentage = ?,
                selected_products = ?,
                button_text = ?,
                button_url = ?,
                sort_order = ?,
                status = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");

        $result = $stmt->execute([
            $title,
            $subtitle,
            $discountPercentage,
            $selectedProductsJson,
            $buttonText,
            $buttonUrl,
            $sortOrder,
            $status,
            $id
        ]);

        if ($result) {
            setFlash('success', 'Promo banner updated successfully');
        } else {
            setFlash('error', 'Failed to update promo banner');
        }

        redirect('/admin/promo-banners');
    }

    /**
     * Show create form
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Fetch OCS store products for dropdown
            $ocsProducts = $this->getOcsProducts();

            view('admin/promo-banners/create', [
                'ocsProducts' => $ocsProducts,
                'pageTitle' => 'Create New Promo Banner'
            ]);
            return;
        }

        // Handle POST request
        if (!verifyCsrfToken(post('_csrf_token'))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $title = post('title');
        $subtitle = post('subtitle');
        $discountPercentage = post('discount_percentage', 20);
        $selectedProducts = post('selected_products', []); // Array of product IDs
        $buttonText = post('button_text', 'Shop Now');
        $buttonUrl = post('button_url', '/deals');
        $sortOrder = post('sort_order', 999);
        $status = post('status', 'active');

        // Validate required fields
        if (empty($title)) {
            setFlash('error', 'Title is required');
            back();
            return;
        }

        // Validate discount percentage
        if (!is_numeric($discountPercentage) || $discountPercentage < 0 || $discountPercentage > 100) {
            setFlash('error', 'Discount percentage must be between 0 and 100');
            back();
            return;
        }

        // Validate status
        if (!in_array($status, ['active', 'inactive'])) {
            $status = 'active';
        }

        $db = Database::getConnection();

        // Encode selected products as JSON
        $selectedProductsJson = json_encode($selectedProducts);

        // Insert new banner
        $stmt = $db->prepare("
            INSERT INTO promo_banners (title, subtitle, discount_percentage, selected_products, button_text, button_url, sort_order, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $result = $stmt->execute([
            $title,
            $subtitle,
            $discountPercentage,
            $selectedProductsJson,
            $buttonText,
            $buttonUrl,
            $sortOrder,
            $status
        ]);

        if ($result) {
            setFlash('success', 'Promo banner created successfully');
        } else {
            setFlash('error', 'Failed to create promo banner');
        }

        redirect('/admin/promo-banners');
    }

    /**
     * Delete a promo banner
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        $id = post('id');

        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'Invalid promo banner ID']);
            exit;
        }

        $db = Database::getConnection();

        // Check if banner exists
        $stmt = $db->prepare("SELECT id FROM promo_banners WHERE id = ?");
        $stmt->execute([$id]);
        $banner = $stmt->fetch();

        if (!$banner) {
            echo json_encode(['success' => false, 'error' => 'Promo banner not found']);
            exit;
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM promo_banners WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete promo banner']);
        }

        exit;
    }

    /**
     * Update promo banner order
     */
    public function updateOrder(): void
    {
        if (!verifyCsrfToken(post('_csrf_token'))) {
            echo json_encode(['success' => false, 'error' => 'Invalid request']);
            exit;
        }

        $order = post('order'); // Array of IDs in new order

        if (!is_array($order)) {
            echo json_encode(['success' => false, 'error' => 'Invalid order data']);
            exit;
        }

        $db = Database::getConnection();

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("UPDATE promo_banners SET sort_order = ? WHERE id = ?");

            foreach ($order as $index => $id) {
                $stmt->execute([$index + 1, $id]);
            }

            $db->commit();

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => 'Failed to update order']);
        }

        exit;
    }

    /**
     * Get OCS store products for dropdown
     * @return array
     */
    private function getOcsProducts(): array
    {
        $db = Database::getConnection();

        // Fetch all active products from OCS store (shop_id = 1, seller_id = 1)
        $stmt = $db->query("
            SELECT
                p.id,
                p.name,
                p.slug,
                p.base_price,
                p.sale_price,
                p.is_on_sale,
                p.sale_percentage,
                pi.image_path as image
            FROM products p
            INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.status = 'active'
              AND p.seller_id = 1
              AND si.status = 'active'
            GROUP BY p.id
            ORDER BY p.name ASC
        ");

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
