<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

use App\Helpers\NotificationHelper;

/**
 * Notifications API Controller
 * Handles AJAX requests for admin notifications
 */
class NotificationsController
{
    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        // Check authentication - must be logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized. Please log in as admin.']);
            exit;
        }

        // Verify CSRF token for state-changing requests
        verifyCsrfForApi();
    }

    /**
     * Get recent notifications for dropdown
     * GET /api/admin/notifications
     */
    public function index(): void
    {
        try {
            $limit = min((int) ($_GET['limit'] ?? 5), 20);

            $userId = $_SESSION['user']['id'] ?? null;
            $notifications = NotificationHelper::getRecent($limit, $userId);
            $unreadCount = NotificationHelper::getUnreadCount($userId);

            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notifications']);
        }
    }

    /**
     * Server-Sent Events stream for instant notification delivery
     * GET /api/admin/notifications/stream
     */
    public function stream(): void
    {
        // Override JSON content-type set in constructor
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no'); // disable nginx buffering
        header('Connection: keep-alive');

        // Flush any existing output buffers
        while (ob_get_level()) {
            ob_end_clean();
        }

        set_time_limit(60);
        ignore_user_abort(true);

        $userId    = $_SESSION['user']['id'] ?? null;
        session_write_close(); // release session lock so other requests aren't blocked
        $lastCount = -1;
        $startTime = time();
        $maxTime   = 55; // reconnect every 55s to free PHP worker
        $lastKeep  = 0;

        while ((time() - $startTime) < $maxTime) {
            // Abort if client disconnected
            if (connection_aborted()) {
                break;
            }

            try {
                $count = NotificationHelper::getUnreadCount($userId);
                if ($count !== $lastCount) {
                    $lastCount = $count;
                    echo 'data: ' . json_encode(['unread_count' => $count]) . "\n\n";
                    flush();
                }
            } catch (\Exception $e) {
                // DB error — send keepalive and continue
            }

            // Keepalive comment every 15 seconds
            if ((time() - $lastKeep) >= 15) {
                echo ": keepalive\n\n";
                flush();
                $lastKeep = time();
            }

            sleep(3);
        }

        // Tell browser to reconnect immediately
        echo "event: reconnect\ndata: {}\n\n";
        flush();
        exit;
    }

    /**
     * Get unread count only (for polling)
     * GET /api/admin/notifications/count
     */
    public function count(): void
    {
        try {
            $userId = $_SESSION['user']['id'] ?? null;
            $db = \Database::getConnection();

            // Sidebar counts — all non-completed items needing admin attention
            $ordersCount = 0;
            $leadsCount  = 0;
            $distCount   = 0;
            $sellersCount = 0;

            try {
                $r = $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','processing')");
                if ($r) $ordersCount = (int)$r->fetchColumn();
            } catch (\Exception $e) {}

            try {
                $r = $db->query("SELECT COUNT(*) FROM leads WHERE status = 'new'");
                if ($r) $leadsCount = (int)$r->fetchColumn();
            } catch (\Exception $e) {}

            try {
                $r = $db->query("SELECT COUNT(*) FROM distribution_requests WHERE status NOT IN ('draft','completed','cancelled','delivered')");
                if ($r) $distCount = (int)$r->fetchColumn();
            } catch (\Exception $e) {}

            try {
                $r = $db->query("SELECT COUNT(*) FROM sellers WHERE verification_status = 'pending'");
                if ($r) $sellersCount = (int)$r->fetchColumn();
            } catch (\Exception $e) {}

            echo json_encode([
                'success'       => true,
                'unread_count'  => NotificationHelper::getUnreadCount($userId),
                'orders_count'  => $ordersCount,
                'leads_count'   => $leadsCount,
                'dist_count'    => $distCount,
                'sellers_count' => $sellersCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * Mark notification as read
     * POST /api/admin/notifications/mark-read
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

            $userId = $_SESSION['user']['id'] ?? null;
            $success = NotificationHelper::markRead((int) $input['id'], $userId);

            if ($success) {
                echo json_encode([
                    'success' => true,
                    'unread_count' => NotificationHelper::getUnreadCount($userId)
                ]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Notification not found']);
            }

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark notification as read']);
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/admin/notifications/mark-all-read
     */
    public function markAllRead(): void
    {
        try {
            $userId = $_SESSION['user']['id'] ?? null;
            $count = NotificationHelper::markAllRead($userId);

            echo json_encode([
                'success' => true,
                'marked_count' => $count,
                'unread_count' => 0
            ]);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark all as read']);
        }
    }
}
