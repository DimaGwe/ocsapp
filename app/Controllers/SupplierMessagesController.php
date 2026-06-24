<?php

namespace App\Controllers;

use App\Helpers\NotificationHelper;
use App\Helpers\EmailHelper;

/**
 * SupplierMessagesController
 * Handles bidirectional messaging between suppliers and admin
 */
class SupplierMessagesController
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
    // SUPPLIER PORTAL
    // ─────────────────────────────────────────────

    /**
     * Supplier messages page
     * GET /supplier/messages
     */
    public function index(): void
    {
        if (empty($_SESSION['supplier_id'])) {
            redirect('supplier/login');
            return;
        }

        $supplierId = (int) $_SESSION['supplier_id'];

        // Fetch all messages for this supplier (ascending for chat order)
        $stmt = $this->db->prepare("
            SELECT sm.*,
                   COALESCE(u.first_name, '') as admin_first_name,
                   COALESCE(u.last_name, '')  as admin_last_name
            FROM supplier_messages sm
            LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
            WHERE sm.supplier_id = ?
            ORDER BY sm.created_at ASC
        ");
        $stmt->execute([$supplierId]);
        $messages = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Mark all unread admin messages as read
        $this->db->prepare("
            UPDATE supplier_messages
            SET is_read = 1, read_at = NOW()
            WHERE supplier_id = ? AND sender_type = 'admin' AND is_read = 0
        ")->execute([$supplierId]);

        // Flash message
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        view('supplier.messages', [
            'pageTitle' => 'Messages',
            'messages'  => $messages,
            'flash'     => $flash,
        ]);
    }

    /**
     * Supplier sends a message to admin
     * POST /supplier/messages/send
     */
    public function send(): void
    {
        if (empty($_SESSION['supplier_id'])) {
            redirect('supplier/login');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect('supplier/messages');
            return;
        }

        $supplierId = (int) $_SESSION['supplier_id'];
        $message    = trim(post('message', ''));

        if (empty($message)) {
            setFlash('error', 'Message cannot be empty.');
            redirect('supplier/messages');
            return;
        }

        if (mb_strlen($message) > 2000) {
            setFlash('error', 'Message is too long (maximum 2000 characters).');
            redirect('supplier/messages');
            return;
        }

        // Insert message
        try {
            $this->db->prepare("
                INSERT INTO supplier_messages (supplier_id, sender_type, sender_id, message, created_at)
                VALUES (?, 'supplier', ?, ?, NOW())
            ")->execute([$supplierId, $supplierId, $message]);
        } catch (\Exception $e) {
            error_log("SupplierMessagesController::send insert error: " . $e->getMessage());
            setFlash('error', 'Failed to send message. Please try again.');
            redirect('supplier/messages');
            return;
        }

        // Fetch supplier info for notifications
        try {
            $supplier = $this->getSupplier($supplierId);
        } catch (\Exception $e) {
            error_log("SupplierMessagesController::send getSupplier error: " . $e->getMessage());
            $supplier = null;
        }

        if ($supplier) {
            $companyName = $supplier['company_name'] ?: $supplier['contact_person'] ?: $supplier['name'];

            // Resolve CRM lead link (if supplier has a linked lead, link there; else fallback to supplier edit)
            $notifLink = "/admin/suppliers/edit?id={$supplierId}";
            try {
                $leadStmt = $this->db->prepare("
                    SELECT lead_id FROM supplier_applications WHERE supplier_id = ? AND lead_id IS NOT NULL LIMIT 1
                ");
                $leadStmt->execute([$supplierId]);
                $leadId = $leadStmt->fetchColumn();
                if ($leadId) {
                    $notifLink = "/admin/leads/view?id={$leadId}#messages";
                }
            } catch (\Exception $e) {
                error_log("SupplierMessagesController::send lead lookup error: " . $e->getMessage());
            }

            // Admin bell notification
            try {
                NotificationHelper::add(
                    'supplier_message',
                    "New message from {$companyName}",
                    mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                    ['link' => $notifLink, 'icon' => 'envelope', 'priority' => 'normal']
                );
            } catch (\Exception $e) {
                error_log("SupplierMessagesController::send bell error: " . $e->getMessage());
            }

            // Admin email notification
            try {
                EmailHelper::sendAdminNewMessage($supplier, $message);
            } catch (\Exception $e) {
                error_log("SupplierMessagesController::send email error: " . $e->getMessage());
            }
        }

        setFlash('success', 'Message sent.');
        redirect('supplier/messages');
    }

    // ─────────────────────────────────────────────
    // ADMIN SIDE
    // ─────────────────────────────────────────────

    /**
     * Admin sends a message to a supplier (AJAX)
     * POST /admin/suppliers/messages/send
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
        $supplierId = (int) ($input['supplier_id'] ?? 0);
        $message    = trim($input['message'] ?? '');

        if (!$supplierId || empty($message)) {
            http_response_code(400);
            echo json_encode(['error' => 'supplier_id and message are required']);
            return;
        }

        if (mb_strlen($message) > 2000) {
            http_response_code(400);
            echo json_encode(['error' => 'Message is too long (maximum 2000 characters)']);
            return;
        }

        $adminId = (int) $_SESSION['user']['id'];

        // Insert message
        $this->db->prepare("
            INSERT INTO supplier_messages (supplier_id, sender_type, sender_id, message, created_at)
            VALUES (?, 'admin', ?, ?, NOW())
        ")->execute([$supplierId, $adminId, $message]);

        $newId = (int) $this->db->lastInsertId();

        // Fetch supplier info
        $supplier = $this->getSupplier($supplierId);

        if ($supplier) {
            // Supplier bell notification
            try {
                NotificationHelper::addSupplierNotification(
                    $supplierId,
                    'message',
                    'New Message from Admin',
                    mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : ''),
                    'supplier/messages',
                    'envelope',
                    'Nouveau message de l\'administrateur',
                    mb_substr($message, 0, 120) . (mb_strlen($message) > 120 ? '…' : '')
                );
            } catch (\Exception $e) {
                error_log("SupplierMessagesController::adminSend bell error: " . $e->getMessage());
            }

            // Supplier email notification
            try {
                EmailHelper::sendSupplierNewMessage($supplier, $message);
            } catch (\Exception $e) {
                error_log("SupplierMessagesController::adminSend email error: " . $e->getMessage());
            }
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
     * GET /api/admin/supplier-messages?supplier_id=X&after=TIMESTAMP
     * Returns JSON array of messages created after the given timestamp
     */
    public function apiGetMessages(): void
    {
        header('Content-Type: application/json');

        if (empty($_SESSION['user'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $supplierId = (int) ($_GET['supplier_id'] ?? 0);
        $after      = $_GET['after'] ?? null; // MySQL datetime string

        if (!$supplierId) {
            http_response_code(400);
            echo json_encode(['error' => 'supplier_id required']);
            return;
        }

        try {
            if ($after) {
                $stmt = $this->db->prepare("
                    SELECT sm.*,
                           COALESCE(u.first_name, '') as admin_first_name,
                           COALESCE(u.last_name, '')  as admin_last_name
                    FROM supplier_messages sm
                    LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
                    WHERE sm.supplier_id = ? AND sm.created_at > ?
                    ORDER BY sm.created_at ASC
                ");
                $stmt->execute([$supplierId, $after]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT sm.*,
                           COALESCE(u.first_name, '') as admin_first_name,
                           COALESCE(u.last_name, '')  as admin_last_name
                    FROM supplier_messages sm
                    LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
                    WHERE sm.supplier_id = ?
                    ORDER BY sm.created_at ASC
                    LIMIT 50
                ");
                $stmt->execute([$supplierId]);
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
     * Get supplier row by ID (includes email + name fields)
     */
    public static function getMessages(int $supplierId, int $limit = 50): array
    {
        $db = \Database::getConnection();
        $stmt = $db->prepare("
            SELECT sm.*,
                   COALESCE(u.first_name, '') as admin_first_name,
                   COALESCE(u.last_name, '')  as admin_last_name
            FROM supplier_messages sm
            LEFT JOIN users u ON sm.sender_type = 'admin' AND sm.sender_id = u.id
            WHERE sm.supplier_id = ?
            ORDER BY sm.created_at ASC
            LIMIT ?
        ");
        $stmt->execute([$supplierId, $limit]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get unread message count for a supplier (messages from admin)
     */
    public static function getUnreadCount(int $supplierId): int
    {
        $db = \Database::getConnection();
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM supplier_messages
            WHERE supplier_id = ? AND sender_type = 'admin' AND is_read = 0
        ");
        $stmt->execute([$supplierId]);
        return (int) $stmt->fetchColumn();
    }

    private function getSupplier(int $supplierId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, contact_person, email, company_name
            FROM suppliers WHERE id = ?
        ");
        $stmt->execute([$supplierId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
