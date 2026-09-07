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
     * Seller analytics dashboard
     */
    public function analytics(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        $stmt = $this->db->prepare("SELECT * FROM shops WHERE seller_id = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([userId()]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$shop) {
            setFlash('error', 'No shop found');
            redirect(url('seller/shop/create'));
            return;
        }

        $endDate   = sanitize(get('end_date', date('Y-m-d')));
        $startDate = sanitize(get('start_date', date('Y-m-d', strtotime('-29 days', strtotime($endDate)))));
        if (!strtotime($startDate) || !strtotime($endDate) || $startDate > $endDate) {
            $endDate   = date('Y-m-d');
            $startDate = date('Y-m-d', strtotime('-29 days'));
        }

        $rangeDays  = (int) ((strtotime($endDate) - strtotime($startDate)) / 86400) + 1;
        $prevEnd    = date('Y-m-d', strtotime($startDate . ' -1 day'));
        $prevStart  = date('Y-m-d', strtotime($prevEnd . " -" . ($rangeDays - 1) . " days"));

        $summary = ['total_revenue' => 0, 'total_orders' => 0, 'avg_order_value' => 0, 'completed_orders' => 0];
        $revenueChange = 0;
        $ordersChange = 0;
        $topProducts = [];
        $statusBreakdown = [];
        $lowStockProducts = [];
        $productStats = ['total_products' => 0, 'active_products' => 0, 'out_of_stock' => 0, 'low_stock' => 0];
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        try {
            $periodTotals = function (string $from, string $to) use ($shop) {
                $stmt = $this->db->prepare("
                    SELECT
                        COUNT(*) AS total_orders,
                        SUM(total) AS all_orders_total,
                        SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) AS total_revenue,
                        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) AS completed_orders
                    FROM orders
                    WHERE shop_id = ? AND DATE(created_at) BETWEEN ? AND ?
                ");
                $stmt->execute([$shop['id'], $from, $to]);
                return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
            };

            $current  = $periodTotals($startDate, $endDate);
            $previous = $periodTotals($prevStart, $prevEnd);

            $summary['total_orders']      = (int) ($current['total_orders'] ?? 0);
            $summary['completed_orders']  = (int) ($current['completed_orders'] ?? 0);
            $summary['total_revenue']     = (float) ($current['total_revenue'] ?? 0);
            $summary['avg_order_value']   = $summary['total_orders'] > 0
                ? (float) ($current['all_orders_total'] ?? 0) / $summary['total_orders']
                : 0;

            $prevRevenue = (float) ($previous['total_revenue'] ?? 0);
            $prevOrders  = (int) ($previous['total_orders'] ?? 0);
            $revenueChange = $prevRevenue > 0
                ? round((($summary['total_revenue'] - $prevRevenue) / $prevRevenue) * 100, 1)
                : ($summary['total_revenue'] > 0 ? 100 : 0);
            $ordersChange = $prevOrders > 0
                ? round((($summary['total_orders'] - $prevOrders) / $prevOrders) * 100, 1)
                : ($summary['total_orders'] > 0 ? 100 : 0);

            // Status breakdown
            $stmt = $this->db->prepare("
                SELECT status, COUNT(*) AS count
                FROM orders
                WHERE shop_id = ? AND DATE(created_at) BETWEEN ? AND ?
                GROUP BY status
            ");
            $stmt->execute([$shop['id'], $startDate, $endDate]);
            $statusBreakdown = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Top selling products
            $stmt = $this->db->prepare("
                SELECT oi.product_name AS name, oi.sku,
                       (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) AS image_path,
                       SUM(oi.subtotal) AS total_revenue,
                       SUM(oi.quantity) AS units_sold
                FROM order_items oi
                INNER JOIN orders o ON o.id = oi.order_id
                WHERE o.shop_id = ? AND DATE(o.created_at) BETWEEN ? AND ?
                GROUP BY oi.product_id, oi.product_name, oi.sku
                ORDER BY total_revenue DESC
                LIMIT 5
            ");
            $stmt->execute([$shop['id'], $startDate, $endDate]);
            $topProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Low stock products
            $stmt = $this->db->prepare("
                SELECT si.stock_quantity, si.low_stock_threshold AS low_stock_alert,
                       p.name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) AS image_path,
                       (SELECT c.name FROM product_categories pc INNER JOIN categories c ON c.id = pc.category_id WHERE pc.product_id = p.id AND pc.is_primary = 1 LIMIT 1) AS category_name
                FROM shop_inventory si
                INNER JOIN products p ON p.id = si.product_id
                WHERE si.shop_id = ? AND si.stock_quantity <= si.low_stock_threshold
                ORDER BY si.stock_quantity ASC
                LIMIT 10
            ");
            $stmt->execute([$shop['id']]);
            $lowStockProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Product stats
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) AS total_products,
                    SUM(status = 'active') AS active_products,
                    SUM(stock_quantity = 0) AS out_of_stock,
                    SUM(stock_quantity > 0 AND stock_quantity <= low_stock_threshold) AS low_stock
                FROM shop_inventory
                WHERE shop_id = ?
            ");
            $stmt->execute([$shop['id']]);
            $productStats = array_merge($productStats, $stmt->fetch(\PDO::FETCH_ASSOC) ?: []);

            // Daily chart series (zero-filled for days with no orders)
            $stmt = $this->db->prepare("
                SELECT DATE(created_at) AS d, SUM(total) AS rev, COUNT(*) AS cnt
                FROM orders
                WHERE shop_id = ? AND DATE(created_at) BETWEEN ? AND ?
                GROUP BY DATE(created_at)
            ");
            $stmt->execute([$shop['id'], $startDate, $endDate]);
            $byDay = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                $byDay[$row['d']] = $row;
            }

            $cursor = strtotime($startDate);
            $endTs  = strtotime($endDate);
            while ($cursor <= $endTs) {
                $d = date('Y-m-d', $cursor);
                $chartLabels[]  = date('M j', $cursor);
                $chartRevenue[] = (float) ($byDay[$d]['rev'] ?? 0);
                $chartOrders[]  = (int) ($byDay[$d]['cnt'] ?? 0);
                $cursor = strtotime('+1 day', $cursor);
            }
        } catch (\PDOException $e) {
            logger("ShopController analytics error: " . $e->getMessage(), 'error');
        }

        view('seller/analytics', [
            'shop' => $shop,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'summary' => $summary,
            'revenueChange' => $revenueChange,
            'ordersChange' => $ordersChange,
            'topProducts' => $topProducts,
            'statusBreakdown' => $statusBreakdown,
            'lowStockProducts' => $lowStockProducts,
            'productStats' => $productStats,
            'chartLabels' => json_encode($chartLabels),
            'chartRevenue' => json_encode($chartRevenue),
            'chartOrders' => json_encode($chartOrders),
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

    /**
     * Seller payout statement (Ecosystem Backend Requirements Sec. 7) -
     * commission and processing fee itemized separately per order, per the
     * Seller Central marketing copy's promise.
     */
    public function payouts(): void
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

        $payouts = [];
        try {
            $stmt = $this->db->prepare("
                SELECT sp.*, o.order_number
                FROM seller_payouts sp
                INNER JOIN orders o ON o.id = sp.order_id
                WHERE sp.shop_id = ?
                ORDER BY sp.created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$shop['id']]);
            $payouts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            logger("ShopController payouts error: " . $e->getMessage(), 'error');
        }

        require_once __DIR__ . '/../Helpers/SellerPayoutHelper.php';
        $pendingBalance = \App\Helpers\SellerPayoutHelper::pendingBalance($shop['id']);

        view('seller/payouts', [
            'shop' => $shop,
            'payouts' => $payouts,
            'pendingBalance' => $pendingBalance,
        ]);
    }

    /**
     * Update seller account password (from Shop Settings)
     */
    public function updatePassword(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect(url('seller/shop/settings'));
            return;
        }

        $userId = userId();
        $currentPassword = post('current_password', '');
        $newPassword = post('new_password', '');
        $confirmPassword = post('confirm_password', '');

        if (empty($currentPassword) || empty($newPassword)) {
            setFlash('error', 'Current and new password are required.');
            redirect(url('seller/shop/settings'));
            return;
        }

        if (strlen($newPassword) < 8) {
            setFlash('error', 'New password must be at least 8 characters.');
            redirect(url('seller/shop/settings'));
            return;
        }

        if ($newPassword !== $confirmPassword) {
            setFlash('error', 'New password and confirmation do not match.');
            redirect(url('seller/shop/settings'));
            return;
        }

        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($currentPassword, $user['password'])) {
            setFlash('error', 'Current password is incorrect.');
            redirect(url('seller/shop/settings'));
            return;
        }

        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $userId]);

        setFlash('success', 'Password updated successfully.');
        redirect(url('seller/shop/settings'));
    }
}
