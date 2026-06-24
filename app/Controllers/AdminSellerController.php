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
            $where = ['u.role = "seller"'];
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
                WHERE {$whereClause}
            ");
            $stmt->execute($params);
            $total = $stmt->fetch()['count'];

            // Get sellers with shop info
            $stmt = $db->prepare("
                SELECT
                    u.*,
                    COUNT(DISTINCT s.id) as shop_count,
                    COUNT(DISTINCT si.id) as product_count,
                    SUM(CASE WHEN s.is_active = 1 THEN 1 ELSE 0 END) as active_shops
                FROM users u
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
                SELECT u.*
                FROM users u
                WHERE u.id = ? AND u.role = 'seller'
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

            // Update user status and ensure role column is correctly set to seller
            $stmt = $db->prepare("UPDATE users SET status = 'active', role = 'seller' WHERE id = ?");
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

            // Prevent deletion if seller has any orders on record
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM orders o
                INNER JOIN shops s ON o.shop_id = s.id
                WHERE s.seller_id = ?
            ");
            $stmt->execute([$sellerId]);
            if ((int)$stmt->fetchColumn() > 0) {
                setFlash('error', 'Cannot delete seller - they have existing orders on record. Deactivate the account instead.');
                back();
                return;
            }

            // Fetch seller info before deletion for notifications + archive
            $infoStmt = $db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ? LIMIT 1");
            $infoStmt->execute([$sellerId]);
            $sellerInfo = $infoStmt->fetch(\PDO::FETCH_ASSOC);

            $deleteReason = post('delete_reason', 'other');
            $deleteNotes  = post('delete_notes', '');
            $canRejoin    = post('can_rejoin', '1') === '1' ? 1 : 0;

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

            // Archive to deleted_users before hard delete
            try {
                $db->prepare("
                    INSERT INTO deleted_users
                        (original_id, email, first_name, last_name, role, reason, notes, deleted_by, can_rejoin)
                    VALUES (?, ?, ?, ?, 'seller', ?, ?, ?, ?)
                ")->execute([
                    $sellerId,
                    $sellerInfo['email']      ?? '',
                    $sellerInfo['first_name'] ?? '',
                    $sellerInfo['last_name']  ?? '',
                    $deleteReason,
                    $deleteNotes,
                    userId(),
                    $canRejoin,
                ]);
            } catch (\Exception $e) {
                error_log('Failed to archive seller to deleted_users: ' . $e->getMessage());
            }

            // Delete user
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$sellerId]);

            // Log action
            $this->logAudit(userId(), 'seller.deleted', $sellerId);

            // Notify seller by email
            if (!empty($sellerInfo['email'])) {
                try {
                    \App\Helpers\EmailHelper::sendSellerAccountRemoved([
                        'first_name' => $sellerInfo['first_name'],
                        'email'      => $sellerInfo['email'],
                    ]);
                } catch (\Exception $e) { /* non-fatal */ }
            }

            // Admin bell notification
            try {
                $sellerLabel = htmlspecialchars($sellerInfo['email'] ?? "ID {$sellerId}");
                $db->prepare("
                    INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                    VALUES ('account_removed', 'Seller Account Deleted', ?, '/admin/sellers', 'trash', 'normal', NOW())
                ")->execute(["Seller account ({$sellerLabel}) was deleted by admin."]);
            } catch (\Exception $e) {
                error_log('Failed to insert admin notification for seller deletion: ' . $e->getMessage());
            }

            setFlash('success', 'Seller deleted successfully');
            redirect(url('admin/sellers'));

        } catch (\PDOException $e) {
            logger("Delete seller error: " . $e->getMessage(), 'error');
            setFlash('error', getFriendlyDatabaseError($e, 'seller'));
            back();
        }
    }

    /**
     * Show verification review page
     */
    public function verificationReview(): void {
        try {
            $sellerId = (int) get('id');

            if (!$sellerId) {
                setFlash('error', 'Invalid seller ID');
                redirect(url('admin/sellers'));
                return;
            }

            $db = \Database::getConnection();

            // Get seller info with shop details
            $stmt = $db->prepare("
                SELECT
                    u.*,
                    s.id as shop_id,
                    s.name as shop_name,
                    s.is_visible as shop_is_visible,
                    admin.first_name as verified_by_name
                FROM users u
                LEFT JOIN shops s ON u.id = s.seller_id
                LEFT JOIN users admin ON u.verified_by = admin.id
                WHERE u.id = ? AND u.role = 'seller'
                LIMIT 1
            ");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch();

            if (!$seller) {
                setFlash('error', 'Seller not found');
                redirect(url('admin/sellers'));
                return;
            }

            view('admin.sellers.verification-review', [
                'seller' => $seller,
                'currentPage' => 'sellers',
            ]);

        } catch (\PDOException $e) {
            logger("Verification review error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading verification details');
            redirect(url('admin/sellers'));
        }
    }

    /**
     * Approve or reject seller verification
     */
    public function verificationAction(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        try {
            $sellerId = (int) post('seller_id');
            $action = post('action'); // 'approve' or 'reject'
            $notes = trim(post('notes', ''));

            if (!$sellerId || !in_array($action, ['approve', 'reject'])) {
                setFlash('error', 'Invalid request');
                back();
                return;
            }

            $db = \Database::getConnection();

            // Get seller info
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'seller' LIMIT 1");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch();

            if (!$seller) {
                setFlash('error', 'Seller not found');
                back();
                return;
            }

            if ($action === 'approve') {
                // Approve verification — also promote role to 'seller' in both columns
                $stmt = $db->prepare("
                    UPDATE users
                    SET verification_status = 'verified',
                        verified_at = NOW(),
                        verified_by = ?,
                        verification_notes = ?,
                        status = 'active',
                        role = 'seller'
                    WHERE id = ?
                ");
                $stmt->execute([userId(), $notes, $sellerId]);

                // Sync user_roles table: ensure the seller role row is correct
                $roleRow = $db->prepare("SELECT id FROM roles WHERE name = 'seller' LIMIT 1");
                $roleRow->execute();
                $sellerRole = $roleRow->fetch();
                if ($sellerRole) {
                    $db->prepare("
                        INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE role_id = VALUES(role_id)
                    ")->execute([$sellerId, $sellerRole['id']]);
                }

                // Make seller's shops visible
                $stmt = $db->prepare("UPDATE shops SET is_visible = 1, is_active = 1 WHERE seller_id = ?");
                $stmt->execute([$sellerId]);

                // Send approval email
                try {
                    \App\Helpers\EmailHelper::sendSellerVerificationApproved($seller);
                } catch (\Exception $emailError) {
                    logger("Failed to send verification approval email: " . $emailError->getMessage(), 'error');
                }

                $this->logAudit(userId(), 'seller.verification.approved', $sellerId, $notes);
                setFlash('success', 'Seller verification approved successfully!');

            } else {
                // Reject verification
                if (empty($notes)) {
                    setFlash('error', 'Please provide a reason for rejection in the notes field.');
                    back();
                    return;
                }

                $stmt = $db->prepare("
                    UPDATE users
                    SET verification_status = 'rejected',
                        status = 'rejected',
                        verified_by = ?,
                        verification_notes = ?
                    WHERE id = ?
                ");
                $stmt->execute([userId(), $notes, $sellerId]);

                // Ensure shops remain hidden
                $stmt = $db->prepare("UPDATE shops SET is_visible = 0 WHERE seller_id = ?");
                $stmt->execute([$sellerId]);

                // Send rejection email
                try {
                    \App\Helpers\EmailHelper::sendSellerVerificationRejected($seller, $notes);
                } catch (\Exception $emailError) {
                    logger("Failed to send verification rejection email: " . $emailError->getMessage(), 'error');
                }

                $this->logAudit(userId(), 'seller.verification.rejected', $sellerId, $notes);
                setFlash('success', 'Seller verification rejected. The seller has been notified.');
            }

            redirect(url('admin/sellers'));

        } catch (\PDOException $e) {
            logger("Verification action error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error processing verification');
            back();
        }
    }

    /**
     * Securely serve a seller verification document (admin only)
     * GET /admin/sellers/document?file=verification-documents/seller_1_abc.pdf
     */
    public function downloadDocument(): void
    {
        $relPath = get('file', '');

        // Strict whitelist: only files inside verification-documents/, no path traversal
        if (empty($relPath) || !preg_match('#^verification-documents/seller_\d+_[a-z0-9]+\.(pdf|jpg|jpeg|png)$#i', $relPath)) {
            http_response_code(400);
            echo 'Invalid file request.';
            return;
        }

        $fullPath = dirname(__DIR__, 2) . '/storage/uploads/' . $relPath;

        if (!is_file($fullPath)) {
            http_response_code(404);
            echo 'Document not found.';
            return;
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];

        header('Content-Type: ' . ($mimeTypes[$ext] ?? 'application/octet-stream'));
        header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('X-Content-Type-Options: nosniff');
        readfile($fullPath);
        exit;
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