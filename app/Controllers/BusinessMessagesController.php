<?php

namespace App\Controllers;

use App\Helpers\NotificationHelper;
use App\Helpers\EmailHelper;

/**
 * BusinessMessagesController
 * Handles bidirectional messaging between distribution businesses and admin
 */
class BusinessMessagesController
{
    private \PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = \Database::getConnection();
    }

    // ─────────────────────────────────────────────
    // BUSINESS PORTAL
    // ─────────────────────────────────────────────

    /**
     * Business messages page
     * GET /distribution/messages
     */
    public function index(): void
    {
        if (empty($_SESSION['business']['id'])) {
            redirect('distribution/login');
            return;
        }

        $businessId = (int) $_SESSION['business']['id'];

        // Fetch all messages for this business (ascending for chat order)
        $stmt = $this->db->prepare("
            SELECT bm.*,
                   COALESCE(u.first_name, '') as admin_first_name,
                   COALESCE(u.last_name, '')  as admin_last_name
            FROM business_messages bm
            LEFT JOIN users u ON bm.sender_type = 'admin' AND bm.sender_id = u.id
            WHERE bm.business_id = ?
            ORDER BY bm.created_at ASC
        ");
        $stmt->execute([$businessId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Mark all unread admin messages as read
        $this->db->prepare("
            UPDATE business_messages
            SET is_read = 1, read_at = NOW()
            WHERE business_id = ? AND sender_type = 'admin' AND is_read = 0
        ")->execute([$businessId]);

        // Flash message
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        $bStmt = $this->db->prepare("SELECT bp.*, u.first_name, u.last_name, u.email, u.phone FROM business_profiles bp INNER JOIN users u ON bp.user_id = u.id WHERE bp.id = ? LIMIT 1");
        $bStmt->execute([$businessId]);
        $business = $bStmt->fetch(\PDO::FETCH_ASSOC) ?: ($_SESSION['business'] ?? []);

        view('distribution.messages', [
            'pageTitle'   => 'Messages',
            'currentPage' => 'messages',
            'messages'    => $messages,
            'flash'       => $flash,
            'business'    => $business,
            'currentLang' => $_SESSION['language'] ?? 'fr',
        ]);
    }

    /**
     * Business sends a message to admin
     * POST /distribution/messages/send
     */
    public function send(): void
    {
        if (empty($_SESSION['business']['id'])) {
            redirect('distribution/login');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect('distribution/messages');
            return;
        }

        $businessId = (int) $_SESSION['business']['id'];
        $message    = trim(post('message', ''));

        if (empty($message)) {
            setFlash('error', 'Message cannot be empty.');
            redirect('distribution/messages');
            return;
        }

        if (mb_strlen($message) > 2000) {
            setFlash('error', 'Message is too long (maximum 2000 characters).');
            redirect('distribution/messages');
            return;
        }

        // Insert message
        try {
            $this->db->prepare("
                INSERT INTO business_messages (business_id, sender_type, sender_id, message, created_at)
                VALUES (?, 'business', ?, ?, NOW())
            ")->execute([$businessId, $businessId, $message]);
        } catch (\Exception $e) {
            error_log("BusinessMessagesController::send insert error: " . $e->getMessage());
            setFlash('error', 'Failed to send message. Please try again.');
            redirect('distribution/messages');
            return;
        }

        // Fetch business info for notifications
        try {
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email as user_email
                FROM business_profiles bp
                LEFT JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("BusinessMessagesController::send getBusiness error: " . $e->getMessage());
            $business = null;
        }

        if ($business) {
            $companyName = $business['company_name'] ?? trim($business['first_name'] . ' ' . $business['last_name']);

            // Admin bell notification
            try {
                NotificationHelper::add(
                    'business_message',
                    "New message from {$companyName}",
                    mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                    [
                        'link'     => url('admin/business-accounts/view?id=' . $businessId),
                        'icon'     => 'envelope',
                        'priority' => 'normal',
                    ]
                );
            } catch (\Exception $e) {
                error_log("BusinessMessagesController::send bell error: " . $e->getMessage());
            }

            // Admin email notification
            try {
                $contactEmail = $business['user_email'] ?? '';
                \App\Helpers\EmailHelper::sendRaw(
                    'info@ocsapp.ca',
                    "New message from {$companyName} — Distribution Portal",
                    "
                    <p><strong>{$companyName}</strong> sent a new message via the Distribution Portal:</p>
                    <blockquote style='border-left:4px solid #00b207;margin:16px 0;padding:12px 16px;background:#f9fafb;color:#374151;font-size:14px;'>
                        " . nl2br(htmlspecialchars($message)) . "
                    </blockquote>
                    <p style='margin-top:16px;'><a href='" . url('admin/business-accounts/view?id=' . $businessId) . "' style='background:#00b207;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;'>View Account</a></p>
                    "
                );
            } catch (\Exception $e) {
                error_log("BusinessMessagesController::send email error: " . $e->getMessage());
            }
        }

        $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
        setFlash('success', $fr ? 'Message envoyé.' : 'Message sent.');
        redirect('distribution/messages');
    }

    // ─────────────────────────────────────────────
    // ADMIN SIDE
    // ─────────────────────────────────────────────

    /**
     * Fetch message thread for a business (admin AJAX)
     * GET /admin/business-accounts/messages?business_id=X
     */
    public function adminIndex(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $businessId = (int)($_GET['business_id'] ?? 0);
        if (!$businessId) {
            http_response_code(400);
            echo json_encode(['error' => 'business_id required']);
            return;
        }

        $stmt = $this->db->prepare("
            SELECT bm.id, bm.sender_type, bm.message, bm.is_read, bm.created_at,
                   COALESCE(u.first_name, '') as admin_first_name,
                   COALESCE(u.last_name, '')  as admin_last_name
            FROM business_messages bm
            LEFT JOIN users u ON bm.sender_type = 'admin' AND bm.sender_id = u.id
            WHERE bm.business_id = ?
            ORDER BY bm.created_at ASC
        ");
        $stmt->execute([$businessId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Mark business messages as read by admin
        $this->db->prepare("
            UPDATE business_messages SET is_read = 1, read_at = NOW()
            WHERE business_id = ? AND sender_type = 'business' AND is_read = 0
        ")->execute([$businessId]);

        echo json_encode(['success' => true, 'messages' => $messages]);
    }

    /**
     * Admin sends a message to a business (AJAX)
     * POST /admin/business-accounts/messages/send
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

        $input      = json_decode(file_get_contents('php://input'), true) ?? [];
        $businessId = (int) ($input['business_id'] ?? 0);
        $message    = trim($input['message'] ?? '');

        if (!$businessId || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'business_id and message are required']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(400);
            echo json_encode(['error' => 'Message is too long (maximum 2000 characters)']);
            return;
        }

        $adminId = (int) $_SESSION['user']['id'];
        $db      = \Database::getConnection();

        $db->prepare("
            INSERT INTO business_messages (business_id, sender_type, sender_id, message, created_at)
            VALUES (?, 'admin', ?, ?, NOW())
        ")->execute([$businessId, $adminId, $message]);

        $newId = (int) $db->lastInsertId();

        // Business bell notification
        try {
            \App\Helpers\NotificationHelper::addBusinessNotification(
                $businessId,
                'message',
                'New Message from Admin',
                mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                'distribution/messages',
                'envelope'
            );
        } catch (\Exception $e) {
            error_log("BusinessMessagesController::adminSend bell error: " . $e->getMessage());
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
    // HELPERS
    // ─────────────────────────────────────────────

    /**
     * Get unread message count for a business (messages from admin)
     */
    public static function getUnreadCount(int $businessId): int
    {
        $db = \Database::getConnection();
        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) FROM business_messages
                WHERE business_id = ? AND sender_type = 'admin' AND is_read = 0
            ");
            $stmt->execute([$businessId]);
            return (int) $stmt->fetchColumn();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
