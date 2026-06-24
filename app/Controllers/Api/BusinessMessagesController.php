<?php

namespace App\Controllers\Api;

/**
 * Business Messages API Controller
 * Handles unread count polling and mark-read for sidebar badge
 */
class BusinessMessagesController
{
    private int $businessId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (empty($_SESSION['business']['id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->businessId = (int) $_SESSION['business']['id'];
    }

    /**
     * GET /api/business/messages/count
     * Returns unread message count for sidebar badge
     */
    public function count(): void
    {
        try {
            echo json_encode([
                'success'      => true,
                'unread_count' => \App\Controllers\BusinessMessagesController::getUnreadCount($this->businessId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * POST /api/business/messages/mark-read
     * Marks all admin messages as read for this business
     */
    public function markRead(): void
    {
        try {
            verifyCsrfForApi();

            $db = \Database::getConnection();
            $db->prepare("
                UPDATE business_messages
                SET is_read = 1, read_at = NOW()
                WHERE business_id = ? AND sender_type = 'admin' AND is_read = 0
            ")->execute([$this->businessId]);

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
