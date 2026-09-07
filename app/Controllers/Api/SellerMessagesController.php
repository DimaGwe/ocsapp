<?php

namespace App\Controllers\Api;

/**
 * Seller Messages API Controller
 * Handles unread count polling and mark-read for sidebar badge
 */
class SellerMessagesController
{
    private int $sellerId;

    public function __construct()
    {
        header('Content-Type: application/json');

        if (!isLoggedIn() || !hasRole('seller')) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->sellerId = userId();
    }

    /**
     * GET /api/seller/messages/count
     */
    public function count(): void
    {
        try {
            echo json_encode([
                'success'      => true,
                'unread_count' => \App\Controllers\SellerMessagesController::getUnreadCount($this->sellerId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * POST /api/seller/messages/mark-read
     * Marks all admin messages as read for this seller
     */
    public function markRead(): void
    {
        try {
            verifyCsrfForApi();

            $db = \Database::getConnection();
            $db->prepare("
                UPDATE seller_messages
                SET is_read = 1, read_at = NOW()
                WHERE seller_id = ? AND sender_type = 'admin' AND is_read = 0
            ")->execute([$this->sellerId]);

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
