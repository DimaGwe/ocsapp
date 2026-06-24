<?php

namespace App\Controllers\Api;

/**
 * Supplier Messages API Controller
 * Handles unread count polling and mark-read for sidebar badge
 */
class SupplierMessagesController
{
    private int $supplierId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (empty($_SESSION['supplier_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->supplierId = (int) $_SESSION['supplier_id'];
    }

    /**
     * GET /api/supplier/messages/count
     * Returns unread message count for sidebar badge
     */
    public function count(): void
    {
        try {
            echo json_encode([
                'success'      => true,
                'unread_count' => \App\Controllers\SupplierMessagesController::getUnreadCount($this->supplierId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * POST /api/supplier/messages/mark-read
     * Marks all admin messages as read for this supplier
     */
    public function markRead(): void
    {
        try {
            verifyCsrfForApi();

            $db = \Database::getConnection();
            $db->prepare("
                UPDATE supplier_messages
                SET is_read = 1, read_at = NOW()
                WHERE supplier_id = ? AND sender_type = 'admin' AND is_read = 0
            ")->execute([$this->supplierId]);

            echo json_encode([
                'success'      => true,
                'unread_count' => 0,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }
}
