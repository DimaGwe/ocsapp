<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminShopController - Shop Management for Admin Panel
 * Handles shop approvals, status changes, and OCS Store management
 */
class AdminShopController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    /**
     * List all shops
     */
    public function index(): void
    {
        try {
            $statusFilter = get('status', '');
            $search = get('search', '');

            // Build query
            $where = [];
            $params = [];

            if ($statusFilter === 'pending') {
                $where[] = "s.is_approved = 0";
            } elseif ($statusFilter === 'active') {
                $where[] = "s.is_approved = 1 AND s.is_active = 1";
            } elseif ($statusFilter === 'inactive') {
                $where[] = "s.is_approved = 1 AND s.is_active = 0";
            }

            if (!empty($search)) {
                $where[] = "(s.name LIKE ? OR s.slug LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            $stmt = $this->db->prepare("
                SELECT
                    s.*,
                    u.first_name AS seller_first_name,
                    u.last_name AS seller_last_name,
                    u.email AS seller_email,
                    COUNT(DISTINCT si.id) as products_count
                FROM shops s
                LEFT JOIN users u ON s.seller_id = u.id
                LEFT JOIN shop_inventory si ON s.id = si.shop_id
                {$whereClause}
                GROUP BY s.id
                ORDER BY s.created_at DESC
            ");
            $stmt->execute($params);
            $shops = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get counts for stats
            $stmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN is_approved = 0 THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN is_approved = 1 AND is_active = 1 THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN is_approved = 1 AND is_active = 0 THEN 1 ELSE 0 END) as inactive
                FROM shops
            ");
            $counts = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('admin.shops.index', [
                'shops' => $shops,
                'statusCounts' => $counts,
                'statusFilter' => $statusFilter,
                'search' => $search
            ]);

        } catch (\Exception $e) {
            error_log('Admin Shops Error: ' . $e->getMessage());
            $shops = [];
            $statusCounts = ['total' => 0, 'pending' => 0, 'active' => 0, 'inactive' => 0];
            view('admin.shops.index', compact('shops', 'statusCounts'));
        }
    }

    /**
     * Approve shop
     */
    public function approve(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            $stmt = $this->db->prepare("UPDATE shops SET is_approved = 1, is_active = 1, approved_by = ?, approved_at = NOW() WHERE id = ?");
            $stmt->execute([userId(), $shopId]);

            setFlash('success', 'Shop approved successfully');
            back();

        } catch (\Exception $e) {
            error_log('Shop Approve Error: ' . $e->getMessage());
            setFlash('error', 'Failed to approve shop');
            back();
        }
    }

    /**
     * Reject shop
     */
    public function reject(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');
            $reason = sanitize(post('reason', ''));

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            $stmt = $this->db->prepare("UPDATE shops SET is_approved = 0, is_active = 0, rejection_reason = ? WHERE id = ?");
            $stmt->execute([$reason, $shopId]);

            setFlash('success', 'Shop rejected');
            back();

        } catch (\Exception $e) {
            error_log('Shop Reject Error: ' . $e->getMessage());
            setFlash('error', 'Failed to reject shop');
            back();
        }
    }

    /**
     * Activate shop
     */
    public function activate(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            $stmt = $this->db->prepare("UPDATE shops SET is_active = 1 WHERE id = ?");
            $stmt->execute([$shopId]);

            setFlash('success', 'Shop activated successfully');
            back();

        } catch (\Exception $e) {
            error_log('Shop Activate Error: ' . $e->getMessage());
            setFlash('error', 'Failed to activate shop');
            back();
        }
    }

    /**
     * Deactivate shop
     */
    public function deactivate(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            $stmt = $this->db->prepare("UPDATE shops SET is_active = 0 WHERE id = ?");
            $stmt->execute([$shopId]);

            setFlash('success', 'Shop deactivated successfully');
            back();

        } catch (\Exception $e) {
            error_log('Shop Deactivate Error: ' . $e->getMessage());
            setFlash('error', 'Failed to deactivate shop');
            back();
        }
    }

    /**
     * Toggle shop active status
     */
    public function toggleStatus(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            // Get current status
            $stmt = $this->db->prepare("SELECT is_active FROM shops WHERE id = ?");
            $stmt->execute([$shopId]);
            $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shop) {
                setFlash('error', 'Shop not found');
                back();
                return;
            }

            // Toggle status
            $newStatus = $shop['is_active'] ? 0 : 1;
            $stmt = $this->db->prepare("UPDATE shops SET is_active = ? WHERE id = ?");
            $stmt->execute([$newStatus, $shopId]);

            $statusText = $newStatus ? 'activated' : 'deactivated';
            setFlash('success', 'Shop ' . $statusText . ' successfully');
            back();

        } catch (\Exception $e) {
            error_log('Shop Toggle Status Error: ' . $e->getMessage());
            setFlash('error', 'Failed to toggle shop status');
            back();
        }
    }

    /**
     * Delete shop
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $shopId = post('shop_id');

            if (!$shopId) {
                setFlash('error', 'Shop ID is required');
                back();
                return;
            }

            // Don't allow deletion of OCS Store (ID 1)
            if ($shopId == 1) {
                setFlash('error', 'Cannot delete OCS Store');
                back();
                return;
            }

            // Delete shop inventory first
            $stmt = $this->db->prepare("DELETE FROM shop_inventory WHERE shop_id = ?");
            $stmt->execute([$shopId]);

            // Delete shop
            $stmt = $this->db->prepare("DELETE FROM shops WHERE id = ?");
            $stmt->execute([$shopId]);

            setFlash('success', 'Shop deleted successfully');
            redirect('admin/shops');

        } catch (\Exception $e) {
            error_log('Shop Delete Error: ' . $e->getMessage());
            setFlash('error', 'Failed to delete shop');
            back();
        }
    }

    /**
     * Edit OCS Store (Shop ID 1)
     */
    public function editOcsStore(): void
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM shops WHERE id = 1");
            $stmt->execute();
            $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shop) {
                setFlash('error', 'OCS Store not found');
                redirect('admin/shops');
                return;
            }

            // Get inventory count
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM shop_inventory WHERE shop_id = 1");
            $stmt->execute();
            $inventoryCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            view('admin.shops.edit-ocs-store', compact('shop', 'inventoryCount'));

        } catch (\Exception $e) {
            error_log('Edit OCS Store Error: ' . $e->getMessage());
            setFlash('error', 'Failed to load OCS Store');
            redirect('admin/shops');
        }
    }

    /**
     * Update OCS Store
     */
    public function updateOcsStore(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $name = sanitize(post('name'));
            $description = sanitize(post('description'));
            $phone = sanitize(post('phone'));
            $email = sanitize(post('email'));
            $address = sanitize(post('address'));

            // Handle file uploads
            $logoPath = null;
            $coverPath = null;

            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/shops/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = 'ocs-store-logo-' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
                    $logoPath = 'uploads/shops/' . $filename;
                }
            }

            // Handle cover image upload
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/shops/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION);
                $filename = 'ocs-store-cover-' . time() . '.' . $extension;
                $filepath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $filepath)) {
                    $coverPath = 'uploads/shops/' . $filename;
                }
            }

            // Build update query
            if ($logoPath && $coverPath) {
                $stmt = $this->db->prepare("
                    UPDATE shops
                    SET name = ?, description = ?, phone = ?, email = ?, address = ?, logo = ?, cover_image = ?
                    WHERE id = 1
                ");
                $stmt->execute([$name, $description, $phone, $email, $address, $logoPath, $coverPath]);
            } elseif ($logoPath) {
                $stmt = $this->db->prepare("
                    UPDATE shops
                    SET name = ?, description = ?, phone = ?, email = ?, address = ?, logo = ?
                    WHERE id = 1
                ");
                $stmt->execute([$name, $description, $phone, $email, $address, $logoPath]);
            } elseif ($coverPath) {
                $stmt = $this->db->prepare("
                    UPDATE shops
                    SET name = ?, description = ?, phone = ?, email = ?, address = ?, cover_image = ?
                    WHERE id = 1
                ");
                $stmt->execute([$name, $description, $phone, $email, $address, $coverPath]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE shops
                    SET name = ?, description = ?, phone = ?, email = ?, address = ?
                    WHERE id = 1
                ");
                $stmt->execute([$name, $description, $phone, $email, $address]);
            }

            setFlash('success', 'OCS Store updated successfully');
            redirect('admin/ocs-store/edit');

        } catch (\Exception $e) {
            error_log('Update OCS Store Error: ' . $e->getMessage());
            setFlash('error', 'Failed to update OCS Store');
            back();
        }
    }
}
