<?php

namespace App\Controllers\Api;

use App\Helpers\NotificationHelper;

/**
 * Business Notifications API Controller
 * Handles AJAX requests for distribution business portal notifications
 */
class BusinessNotificationsController
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
     * Get recent notifications
     * GET /api/business/notifications
     */
    public function index(): void
    {
        try {
            $limit = min((int) ($_GET['limit'] ?? 10), 20);
            $notifications = NotificationHelper::getBusinessRecent($this->businessId, $limit);
            $unreadCount   = NotificationHelper::getBusinessUnreadCount($this->businessId);

            echo json_encode([
                'success'       => true,
                'notifications' => $notifications,
                'unread_count'  => $unreadCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notifications']);
        }
    }

    /**
     * Server-Sent Events stream for instant notification delivery
     * GET /api/business/notifications/stream
     */
    public function stream(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Connection: keep-alive');

        while (ob_get_level()) {
            ob_end_clean();
        }

        set_time_limit(60);
        ignore_user_abort(true);

        $businessId = $this->businessId;
        session_write_close(); // release session lock so other requests aren't blocked
        $lastCount  = -1;
        $startTime  = time();
        $lastKeep   = 0;

        while ((time() - $startTime) < 55) {
            if (connection_aborted()) {
                break;
            }

            try {
                $count = NotificationHelper::getBusinessUnreadCount($businessId);
                if ($count !== $lastCount) {
                    $lastCount = $count;
                    echo 'data: ' . json_encode(['unread_count' => $count]) . "\n\n";
                    flush();
                }
            } catch (\Exception $e) {}

            if ((time() - $lastKeep) >= 15) {
                echo ": keepalive\n\n";
                flush();
                $lastKeep = time();
            }

            sleep(3);
        }

        echo "event: reconnect\ndata: {}\n\n";
        flush();
        exit;
    }

    /**
     * Get unread count only (for polling)
     * GET /api/business/notifications/count
     */
    public function count(): void
    {
        try {
            $db = \Database::getConnection();

            // Draft requests
            $draftStmt = $db->prepare(
                "SELECT COUNT(*) FROM distribution_requests WHERE business_profile_id = ? AND status = 'draft'"
            );
            $draftStmt->execute([$this->businessId]);
            $draftCount = (int)$draftStmt->fetchColumn();

            // Active requests (submitted but not completed/cancelled — need attention)
            $activeStmt = $db->prepare(
                "SELECT COUNT(*) FROM distribution_requests
                 WHERE business_profile_id = ? AND status NOT IN ('draft','completed','cancelled','delivered')"
            );
            $activeStmt->execute([$this->businessId]);
            $activeRequestCount = (int)$activeStmt->fetchColumn();

            // Unpaid invoices
            $invStmt = $db->prepare(
                "SELECT COUNT(*) FROM distribution_invoices WHERE business_profile_id = ? AND status IN ('sent','overdue','pending')"
            );
            $invStmt->execute([$this->businessId]);
            $unpaidCount = (int)$invStmt->fetchColumn();

            // Unpaid payables (distribution requests pending payment)
            $payStmt = $db->prepare(
                "SELECT COUNT(*) FROM distribution_requests
                 WHERE business_profile_id = ? AND payment_status NOT IN ('paid','refunded')
                 AND status NOT IN ('draft','cancelled','expired')"
            );
            $payStmt->execute([$this->businessId]);
            $unpaidPayablesCount = (int)$payStmt->fetchColumn();

            // Unread messages
            $unreadMsgCount = 0;
            try {
                $unreadMsgCount = \App\Controllers\BusinessMessagesController::getUnreadCount($this->businessId);
            } catch (\Exception $msgEx) {}

            echo json_encode([
                'success'               => true,
                'unread_count'          => NotificationHelper::getBusinessUnreadCount($this->businessId),
                'draft_count'           => $draftCount,
                'active_request_count'  => $activeRequestCount,
                'unpaid_invoice_count'  => $unpaidCount,
                'unpaid_payables_count' => $unpaidPayablesCount,
                'unread_msg_count'      => $unreadMsgCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * Mark a notification as read
     * POST /api/business/notifications/mark-read
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

            $success = NotificationHelper::markBusinessRead((int) $input['id'], $this->businessId);

            echo json_encode([
                'success'      => $success,
                'unread_count' => NotificationHelper::getBusinessUnreadCount($this->businessId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/business/notifications/mark-all-read
     */
    public function markAllRead(): void
    {
        try {
            $count = NotificationHelper::markAllBusinessRead($this->businessId);

            echo json_encode([
                'success'       => true,
                'marked_count'  => $count,
                'unread_count'  => 0,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark all as read']);
        }
    }
}
