<?php

namespace App\Controllers\Api;

class DriverNotificationsController
{
    private int $driverId;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        if (empty($_SESSION['user']['id']) || ($_SESSION['user']['role'] ?? '') !== 'driver') {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->driverId = (int) $_SESSION['user']['id'];
    }

    // GET /api/driver/notifications/inbox
    public function index(): void
    {
        try {
            $db    = \Database::getConnection();
            $limit = min((int) ($_GET['limit'] ?? 10), 20);
            $stmt  = $db->prepare(
                "SELECT id, message, type, order_id, po_id, created_at,
                        (read_at IS NOT NULL) AS is_read
                 FROM driver_delivery_notifications
                 WHERE driver_id = ?
                 ORDER BY (read_at IS NULL) DESC, created_at DESC
                 LIMIT ?"
            );
            $stmt->execute([$this->driverId, $limit]);
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Cast is_read to bool
            foreach ($notifications as &$n) {
                $n['is_read'] = (bool) $n['is_read'];
            }
            unset($n);

            echo json_encode([
                'success'       => true,
                'notifications' => $notifications,
                'unread_count'  => $this->getUnreadCount($db),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notifications']);
        }
    }

    // GET /api/driver/notifications/count
    public function count(): void
    {
        try {
            $db = \Database::getConnection();
            echo json_encode(['success' => true, 'unread_count' => $this->getUnreadCount($db)]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    // POST /api/driver/notifications/mark-read
    public function markRead(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Notification ID required']);
                return;
            }
            $db = \Database::getConnection();
            $db->prepare(
                "UPDATE driver_delivery_notifications SET read_at = NOW()
                 WHERE id = ? AND driver_id = ? AND read_at IS NULL"
            )->execute([(int) $input['id'], $this->driverId]);

            echo json_encode(['success' => true, 'unread_count' => $this->getUnreadCount($db)]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }

    // POST /api/driver/notifications/mark-all-read
    public function markAllRead(): void
    {
        try {
            $db = \Database::getConnection();
            $db->prepare(
                "UPDATE driver_delivery_notifications SET read_at = NOW()
                 WHERE driver_id = ? AND read_at IS NULL"
            )->execute([$this->driverId]);

            echo json_encode(['success' => true, 'unread_count' => 0]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark all as read']);
        }
    }

    private function getUnreadCount(\PDO $db): int
    {
        $stmt = $db->prepare(
            "SELECT COUNT(*) FROM driver_delivery_notifications WHERE driver_id = ? AND read_at IS NULL"
        );
        $stmt->execute([$this->driverId]);
        return (int) $stmt->fetchColumn();
    }
}
