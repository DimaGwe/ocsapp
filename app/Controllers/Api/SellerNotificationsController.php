<?php

namespace App\Controllers\Api;

use App\Helpers\NotificationHelper;

/**
 * Seller Notifications API Controller
 * Handles AJAX requests for seller portal notifications
 */
class SellerNotificationsController
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
     * Get recent notifications
     * GET /api/seller/notifications
     */
    public function index(): void
    {
        try {
            $limit = min((int) ($_GET['limit'] ?? 10), 20);
            $notifications = NotificationHelper::getSellerRecent($this->sellerId, $limit);
            $unreadCount = NotificationHelper::getSellerUnreadCount($this->sellerId);

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notifications']);
        }
    }

    /**
     * Get unread count only, plus unread message count (for sidebar badge)
     * GET /api/seller/notifications/count
     */
    public function count(): void
    {
        try {
            $unreadMsgCount = 0;
            try {
                $unreadMsgCount = \App\Controllers\SellerMessagesController::getUnreadCount($this->sellerId);
            } catch (\Exception $msgEx) {}

            echo json_encode([
                'success'          => true,
                'unread_count'     => NotificationHelper::getSellerUnreadCount($this->sellerId),
                'unread_msg_count' => $unreadMsgCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * Mark notification as read
     * POST /api/seller/notifications/mark-read
     */
    public function markRead(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Notification ID is required']);
                return;
            }

            $success = NotificationHelper::markSellerRead((int) $input['id'], $this->sellerId);

            echo json_encode([
                'success' => $success,
                'unread_count' => NotificationHelper::getSellerUnreadCount($this->sellerId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/seller/notifications/mark-all-read
     */
    public function markAllRead(): void
    {
        try {
            $count = NotificationHelper::markAllSellerRead($this->sellerId);

            echo json_encode([
                'success' => true,
                'marked_count' => $count,
                'unread_count' => 0,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark all as read']);
        }
    }
}
