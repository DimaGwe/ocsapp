<?php

namespace App\Controllers;

/**
 * ShopController
 * Handles seller shop management (dashboard, create, settings)
 */
class ShopController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Seller dashboard
     */
    public function dashboard(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        $userId = userId();

        // Get seller's shop
        $stmt = $this->db->prepare("SELECT * FROM shops WHERE seller_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$userId]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

        $stats = ['total_orders' => 0, 'pending_orders' => 0, 'today_orders' => 0, 'total_revenue' => 0, 'products_count' => 0];
        $recentOrders = [];

        if ($shop) {
            try {
                // Order stats
                $stmt = $this->db->prepare("
                    SELECT
                        COUNT(*) as total_orders,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
                        SUM(CASE WHEN status IN ('completed','delivered') THEN total ELSE 0 END) as total_revenue
                    FROM orders
                    WHERE shop_id = ?
                ");
                $stmt->execute([$shop['id']]);
                $stats = array_merge($stats, $stmt->fetch(\PDO::FETCH_ASSOC) ?: []);

                // Product count
                $stmt = $this->db->prepare("SELECT COUNT(*) as cnt FROM shop_inventory WHERE shop_id = ?");
                $stmt->execute([$shop['id']]);
                $stats['products_count'] = (int)($stmt->fetch(\PDO::FETCH_ASSOC)['cnt'] ?? 0);

                // Recent orders
                $stmt = $this->db->prepare("
                    SELECT o.*, u.first_name, u.last_name
                    FROM orders o
                    LEFT JOIN users u ON o.user_id = u.id
                    WHERE o.shop_id = ?
                    ORDER BY o.created_at DESC
                    LIMIT 10
                ");
                $stmt->execute([$shop['id']]);
                $recentOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                logger("ShopController dashboard error: " . $e->getMessage(), 'error');
            }
        }

        view('seller/dashboard', [
            'shop' => $shop,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }

    /**
     * Show create shop form
     */
    public function create(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        // If seller already has a shop, redirect to dashboard
        $stmt = $this->db->prepare("SELECT id FROM shops WHERE seller_id = ? LIMIT 1");
        $stmt->execute([userId()]);
        if ($stmt->fetch()) {
            redirect(url('seller/dashboard'));
            return;
        }

        view('seller/shop-create', []);
    }

    /**
     * Store new shop
     */
    public function store(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('seller/shop/create'));
            return;
        }

        $name = sanitize(post('name', ''));
        $description = sanitize(post('description', ''));
        $phone = sanitize(post('phone', ''));
        $address = sanitize(post('address', ''));

        if (empty($name)) {
            setFlash('error', 'Shop name is required');
            redirect(url('seller/shop/create'));
            return;
        }

        try {
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name)) . '-' . time();
            $stmt = $this->db->prepare("
                INSERT INTO shops (seller_id, name, slug, description, phone, address, is_approved, is_active, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, 0, 0, NOW(), NOW())
            ");
            $stmt->execute([userId(), $name, $slug, $description, $phone, $address]);

            setFlash('success', 'Shop application submitted. Pending admin approval.');
            redirect(url('seller/dashboard'));
        } catch (\PDOException $e) {
            logger("ShopController store error: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to create shop');
            redirect(url('seller/shop/create'));
        }
    }

    /**
     * Shop settings
     */
    public function settings(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM shops WHERE seller_id = ? LIMIT 1");
        $stmt->execute([userId()]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$shop) {
            setFlash('error', 'No shop found');
            redirect(url('seller/shop/create'));
            return;
        }

        view('seller/shop-settings', ['shop' => $shop]);
    }

    /**
     * Update shop settings
     */
    public function update(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('seller/shop/settings'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM shops WHERE seller_id = ? LIMIT 1");
        $stmt->execute([userId()]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$shop) {
            setFlash('error', 'No shop found');
            redirect(url('seller/dashboard'));
            return;
        }

        $name        = sanitize(post('name', $shop['name']));
        $description = sanitize(post('description', ''));
        $phone       = sanitize(post('phone', ''));
        $email       = sanitize(post('email', ''));
        $address     = sanitize(post('address', ''));

        $logo        = $shop['logo'];
        $coverImage  = $shop['cover_image'];

        // Handle logo upload
        if (!empty($_FILES['logo']['name'])) {
            $uploader = new \App\Helpers\ImageUploadHelper('uploads/shops/logos');
            $result = $uploader->upload($_FILES['logo']);
            if ($result['success']) {
                // Delete old logo if exists
                if ($shop['logo'] && file_exists(BASE_PATH . '/public/' . $shop['logo'])) {
                    @unlink(BASE_PATH . '/public/' . $shop['logo']);
                }
                $logo = $result['path'];
            } else {
                setFlash('error', 'Logo upload failed: ' . $result['error']);
                redirect(url('seller/shop/settings'));
                return;
            }
        }

        // Handle banner/cover upload
        if (!empty($_FILES['cover_image']['name'])) {
            $uploader = new \App\Helpers\ImageUploadHelper('uploads/shops/covers');
            $result = $uploader->upload($_FILES['cover_image']);
            if ($result['success']) {
                if ($shop['cover_image'] && file_exists(BASE_PATH . '/public/' . $shop['cover_image'])) {
                    @unlink(BASE_PATH . '/public/' . $shop['cover_image']);
                }
                $coverImage = $result['path'];
            } else {
                setFlash('error', 'Banner upload failed: ' . $result['error']);
                redirect(url('seller/shop/settings'));
                return;
            }
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE shops SET name = ?, description = ?, phone = ?, email = ?, address = ?,
                                 logo = ?, cover_image = ?, updated_at = NOW()
                WHERE id = ? AND seller_id = ?
            ");
            $stmt->execute([$name, $description, $phone, $email, $address, $logo, $coverImage, $shop['id'], userId()]);

            setFlash('success', 'Shop settings updated successfully');
        } catch (\PDOException $e) {
            logger("ShopController update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to update shop');
        }

        redirect(url('seller/shop/settings'));
    }
}
