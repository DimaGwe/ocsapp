<?php

namespace App\Controllers;

use App\Helpers\NotificationHelper;
use App\Helpers\EmailHelper;

/**
 * SellerMessagesController
 * Handles bidirectional messaging between sellers and admin
 */
class SellerMessagesController
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    // ─────────────────────────────────────────────
    // SELLER PORTAL
    // ─────────────────────────────────────────────

    /**
     * Seller messages page
     * GET /seller/messages
     */
    public function index(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        $sellerId = userId();

        $stmt = $this->db->prepare("
            SELECT sm.*,
                   COALESCE(u.first_name, '') as admin_first_name,
                   COALESCE(u.last_name, '')  as admin_last_name
            FROM seller_messages sm
            LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
            WHERE sm.seller_id = ?
            ORDER BY sm.created_at ASC
        ");
        $stmt->execute([$sellerId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Mark all unread admin messages as read
        $this->db->prepare("
            UPDATE seller_messages
            SET is_read = 1, read_at = NOW()
            WHERE seller_id = ? AND sender_type = 'admin' AND is_read = 0
        ")->execute([$sellerId]);

        $pageTitle = 'Messages';
        view('seller/messages', [
            'pageTitle' => $pageTitle,
            'messages'  => $messages,
        ]);
    }

    /**
     * Seller sends a message to admin
     * POST /seller/messages/send
     */
    public function send(): void
    {
        if (!isLoggedIn() || !hasRole('seller')) {
            redirect(url('login'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('seller/messages'));
            return;
        }

        $sellerId = userId();
        $message  = trim(post('message', ''));

        if (empty($message)) {
            setFlash('error', 'Message cannot be empty.');
            redirect(url('seller/messages'));
            return;
        }

        if (mb_strlen($message) > 2000) {
            setFlash('error', 'Message is too long (maximum 2000 characters).');
            redirect(url('seller/messages'));
            return;
        }

        try {
            $this->db->prepare("
                INSERT INTO seller_messages (seller_id, sender_type, sender_id, message, created_at)
                VALUES (?, 'seller', ?, ?, NOW())
            ")->execute([$sellerId, $sellerId, $message]);
        } catch (\Exception $e) {
            error_log("SellerMessagesController::send insert error: " . $e->getMessage());
            setFlash('error', 'Failed to send message. Please try again.');
            redirect(url('seller/messages'));
            return;
        }

        // Fetch seller info for notifications
        try {
            $seller = $this->getSeller($sellerId);
        } catch (\Exception $e) {
            error_log("SellerMessagesController::send getSeller error: " . $e->getMessage());
            $seller = null;
        }

        if ($seller) {
            $shopName = $seller['shop_name'] ?? trim(($seller['first_name'] ?? '') . ' ' . ($seller['last_name'] ?? '')) ?: 'Seller';

            // Admin bell notification
            try {
                NotificationHelper::add(
                    'seller_message',
                    "New message from {$shopName}",
                    mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                    ['link' => "/admin/sellers/view?id={$sellerId}", 'icon' => 'envelope', 'priority' => 'normal']
                );
            } catch (\Exception $e) {
                error_log("SellerMessagesController::send bell error: " . $e->getMessage());
            }

            // Admin email notification
            try {
                EmailHelper::sendAdminNewMessageFromSeller($seller, $message);
            } catch (\Exception $e) {
                error_log("SellerMessagesController::send email error: " . $e->getMessage());
            }
        }

        setFlash('success', 'Message sent.');
        redirect(url('seller/messages'));
    }

    // ─────────────────────────────────────────────
    // ADMIN SIDE
    // ─────────────────────────────────────────────

    /**
     * Admin sends a message to a seller (AJAX)
     * POST /admin/sellers/messages/send
     * Returns JSON
     */
    public function adminSend(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        verifyCsrfForApi();

        $input    = json_decode(file_get_contents('php://input'), true) ?? [];
        $sellerId = (int) ($input['seller_id'] ?? 0);
        $message  = trim($input['message'] ?? '');

        if (!$sellerId || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'seller_id and message are required']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(400);
            echo json_encode(['error' => 'Message is too long (maximum 2000 characters)']);
            return;
        }

        $adminId = (int) $_SESSION['user']['id'];

        $this->db->prepare("
            INSERT INTO seller_messages (seller_id, sender_type, sender_id, message, created_at)
            VALUES (?, 'admin', ?, ?, NOW())
        ")->execute([$sellerId, $adminId, $message]);

        $newId = (int) $this->db->lastInsertId();

        // Seller bell notification
        try {
            NotificationHelper::addSellerNotification(
                $sellerId,
                'message',
                'New Message from Admin',
                mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                'seller/messages',
                'envelope'
            );
        } catch (\Exception $e) {
            error_log("SellerMessagesController::adminSend bell error: " . $e->getMessage());
        }

        // Seller email notification
        try {
            $seller = $this->getSeller($sellerId);
            if ($seller) {
                EmailHelper::sendSellerNewMessage($seller, $message);
            }
        } catch (\Exception $e) {
            error_log("SellerMessagesController::adminSend email error: " . $e->getMessage());
        }

        $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? ''));

        echo json_encode([
            'success'    => true,
            'message_id' => $newId,
            'admin_name' => $adminName ?: 'Admin',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ─────────────────────────────────────────────
    // ADMIN API
    // ─────────────────────────────────────────────

    /**
     * Poll for new messages (admin AJAX)
     * GET /api/admin/seller-messages?seller_id=X&after=TIMESTAMP
     */
    public function apiGetMessages(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $sellerId = (int) ($_GET['seller_id'] ?? 0);
        $after    = $_GET['after'] ?? null;

        if (!$sellerId) {
            http_response_code(400);
            echo json_encode(['error' => 'seller_id required']);
            return;
        }

        try {
            if ($after) {
                $stmt = $this->db->prepare("
                    SELECT sm.*,
                           COALESCE(u.first_name, '') as admin_first_name,
                           COALESCE(u.last_name, '')  as admin_last_name
                    FROM seller_messages sm
                    LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
                    WHERE sm.seller_id = ? AND sm.created_at > ?
                    ORDER BY sm.created_at ASC
                ");
                $stmt->execute([$sellerId, $after]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT sm.*,
                           COALESCE(u.first_name, '') as admin_first_name,
                           COALESCE(u.last_name, '')  as admin_last_name
                    FROM seller_messages sm
                    LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
                    WHERE sm.seller_id = ?
                    ORDER BY sm.created_at ASC
                    LIMIT 50
                ");
                $stmt->execute([$sellerId]);
            }

            $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'messages' => $messages]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'DB error']);
        }
    }

    // ─────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Get unread message count for a seller (messages from admin)
     */
    public static function getUnreadCount(int $sellerId): int
    {
        $db = \Database::getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM seller_messages
            WHERE seller_id = ? AND sender_type = 'admin' AND is_read = 0
        ");
        $stmt->execute([$sellerId]);
        return (int) $stmt->fetchColumn();
    }

    private function getSeller(int $sellerId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, s.name AS shop_name
            FROM users u
            LEFT JOIN shops s ON s.seller_id = u.id
            WHERE u.id = ?
        ");
        $stmt->execute([$sellerId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
