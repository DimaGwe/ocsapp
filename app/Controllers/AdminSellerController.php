<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class AdminSellerController {
    
    public function __construct() {
        AuthMiddleware::handle('admin');
    }

    /**
     * Display all sellers
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();
            
            $page = max(1, (int) get('page', 1));
            $perPage = (int) env('ITEMS_PER_PAGE', 20);
            $offset = ($page - 1) * $perPage;
            
            $search = get('search', '');
            $statusFilter = get('status', '');

            // Build query
            $where = ['r.name = "seller"'];
            $params = [];

            if ($search) {
                $where[] = "(u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.phone LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($statusFilter) {
                $where[] = "u.status = ?";
                $params[] = $statusFilter;
            }

            $whereClause = implode(' AND ', $where);

            // Get total count
            $stmt = $db->prepare("
                SELECT COUNT(DISTINCT u.id) as count
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE {$whereClause}
            ");
            $stmt->execute($params);
            $total = $stmt->fetch()['count'];

            // Get sellers with shop info (FIXED: uses shop_inventory correctly)
            $stmt = $db->prepare("
                SELECT 
                    u.*,
                    COUNT(DISTINCT s.id) as shop_count,
                    COUNT(DISTINCT si.id) as product_count,
                    SUM(CASE WHEN s.is_active = 1 THEN 1 ELSE 0 END) as active_shops
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN shops s ON u.id = s.seller_id
                LEFT JOIN shop_inventory si ON s.id = si.shop_id
                WHERE {$whereClause}
                GROUP BY u.id
                ORDER BY u.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($params);
            $sellers = $stmt->fetchAll();

            view('admin.sellers.index', [
                'sellers' => $sellers,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'search' => $search,
                'statusFilter' => $statusFilter,
                'currentPage' => 'sellers',
            ]);

        } catch (\PDOException $e) {
            logger("Admin sellers error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading sellers');
            back();
        }
    }

    /**
     * View single seller details
     */
    public function view(): void {
        try {
            $sellerId = (int) get('id');
            
            if (!$sellerId) {
                setFlash('error', 'Invalid seller ID');
                redirect(url('admin/sellers'));
            }

            $db = \Database::getConnection();

            // Get seller info
            $stmt = $db->prepare("
                SELECT u.*, r.name as role_name
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND r.name = 'seller'
                LIMIT 1
            ");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch();

            if (!$seller) {
                setFlash('error', 'Seller not found');
                redirect(url('admin/sellers'));
            }

            // Get seller's shops
            $stmt = $db->prepare("
                SELECT * FROM shops 
                WHERE seller_id = ?
                ORDER BY created_at DESC
            ");
            $stmt->execute([$sellerId]);
            $shops = $stmt->fetchAll();

            // Get seller's products (inventory)
            $stmt = $db->prepare("
                SELECT 
                    si.*,
                    p.name as product_name,
                    p.sku as product_sku,
                    s.name as shop_name
                FROM shop_inventory si
                INNER JOIN products p ON si.product_id = p.id
                INNER JOIN shops s ON si.shop_id = s.id
                WHERE s.seller_id = ?
                ORDER BY si.created_at DESC
                LIMIT 20
            ");
            $stmt->execute([$sellerId]);
            $inventory = $stmt->fetchAll();

            // Get statistics
            $stats = [
                'total_shops' => count($shops),
                'active_shops' => count(array_filter($shops, fn($s) => $s['is_active'] == 1)),
                'total_products' => count($inventory),
                'total_stock' => array_sum(array_column($inventory, 'stock_quantity')),
            ];

            view('admin.sellers.view', [
                'seller' => $seller,
                'shops' => $shops,
                'inventory' => $inventory,
                'stats' => $stats,
                'currentPage' => 'sellers',
            ]);

        } catch (\PDOException $e) {
            logger("View seller error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading seller details');
            redirect(url('admin/sellers'));
        }
    }

    /**
     * Suspend a seller
     */
    public function suspend(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $sellerId = (int) post('seller_id');
            $reason = sanitize(post('reason', ''));

            if (!$sellerId) {
                setFlash('error', 'Invalid seller ID');
                back();
            }

            $db = \Database::getConnection();

            // Update user status
            $stmt = $db->prepare("UPDATE users SET status = 'suspended' WHERE id = ?");
            $stmt->execute([$sellerId]);

            // Suspend all seller's shops
            $stmt = $db->prepare("UPDATE shops SET is_active = 0 WHERE seller_id = ?");
            $stmt->execute([$sellerId]);

            // Log action
            $this->logAudit(userId(), 'seller.suspended', $sellerId, $reason);

            setFlash('success', 'Seller suspended successfully');
            back();

        } catch (\PDOException $e) {
            logger("Suspend seller error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error suspending seller');
            back();
        }
    }

    /**
     * Activate a seller
     */
    public function activate(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $sellerId = (int) post('seller_id');

            if (!$sellerId) {
                setFlash('error', 'Invalid seller ID');
                back();
            }

            $db = \Database::getConnection();

            // Get seller info before update
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch();

            if (!$seller) {
                setFlash('error', 'Seller not found');
                back();
            }

            $wasInactive = $seller['status'] !== 'active';

            // Update user status
            $stmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $stmt->execute([$sellerId]);

            // Activate all seller's shops
            $stmt = $db->prepare("UPDATE shops SET is_active = 1 WHERE seller_id = ?");
            $stmt->execute([$sellerId]);

            // Send approval email if seller was previously inactive/pending
            if ($wasInactive) {
                try {
                    require_once __DIR__ . '/../Helpers/EmailHelper.php';

                    $user = [
                        'email' => $seller['email'],
                        'first_name' => $seller['first_name'],
                        'last_name' => $seller['last_name']
                    ];

                    \App\Helpers\EmailHelper::sendSellerApproved($user);
                    logger("Seller approval email sent to {$seller['email']}", 'info');
                } catch (\Exception $e) {
                    logger("Failed to send seller approval email to {$seller['email']}: " . $e->getMessage(), 'warning');
                    // Don't fail the activation if email fails
                }
            }

            // Log action
            $this->logAudit(userId(), 'seller.activated', $sellerId);

            setFlash('success', 'Seller activated successfully' . ($wasInactive ? ' and approval email sent' : ''));
            back();

        } catch (\PDOException $e) {
            logger("Activate seller error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error activating seller');
            back();
        }
    }

    /**
     * Delete a seller
     */
    public function delete(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $sellerId = (int) post('seller_id');

            if (!$sellerId) {
                setFlash('error', 'Invalid seller ID');
                back();
            }

            $db = \Database::getConnection();

            // Check if seller has orders (prevent deletion)
            // Note: You'll need to create orders table later
            // For now, we'll just delete

            // Delete seller's inventory
            $stmt = $db->prepare("
                DELETE si FROM shop_inventory si
                INNER JOIN shops s ON si.shop_id = s.id
                WHERE s.seller_id = ?
            ");
            $stmt->execute([$sellerId]);

            // Delete seller's shops
            $stmt = $db->prepare("DELETE FROM shops WHERE seller_id = ?");
            $stmt->execute([$sellerId]);

            // Delete user role
            $stmt = $db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$sellerId]);

            // Delete user
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$sellerId]);

            // Log action
            $this->logAudit(userId(), 'seller.deleted', $sellerId);

            setFlash('success', 'Seller deleted successfully');
            redirect(url('admin/sellers'));

        } catch (\PDOException $e) {
            logger("Delete seller error: " . $e->getMessage(), 'error');
            setFlash('error', getFriendlyDatabaseError($e, 'seller'));
            back();
        }
    }

    /**
     * Log audit trail
     */
    private function logAudit(int $userId, string $action, int $targetId = null, string $details = null): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, ip_address, user_agent) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (\PDOException $e) {
            logger("Audit log error: " . $e->getMessage(), 'error');
        }
    }
}