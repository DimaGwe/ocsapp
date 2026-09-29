<?php

namespace App\Controllers\Api;

/**
 * Driver notification inbox, shared by the web driver portal (session) and the ODA app (Bearer token).
 * Updated 2026-09-28:
 * - The session check required role 'driver', but drivers have role 'delivery', so the portal bell
 *   always got 401. Both are accepted now.
 * - Bearer auth (same driver_api_tokens check as DriverApiController, suspended/rejected refused) so
 *   the app can show general notifications (founding welcome, compliance reminders...), which have
 *   no order_id/po_id and never reached it through /api/driver/notifications.
 * - Bilingual: `message` is returned in the requested language (?lang=fr|en, else the session
 *   language, else fr) using message_fr when present; both raw texts are also returned.
 * - Session-authenticated POSTs verify the X-CSRF-Token header the portal already sends.
 */
class DriverNotificationsController
{
    private int $driverId;
    private bool $viaToken = false;
    private bool $fr = true;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (str_starts_with($header, 'Bearer ')) {
            $stmt = \Database::getConnection()->prepare(
                "SELECT t.user_id FROM driver_api_tokens t
                 JOIN users u ON u.id = t.user_id AND u.status NOT IN ('suspended', 'rejected')
                 WHERE t.token = ? AND (t.expires_at IS NULL OR t.expires_at > NOW())
                 LIMIT 1"
            );
            $stmt->execute([substr($header, 7)]);
            $userId = (int) $stmt->fetchColumn();
            if (!$userId) {
                $this->deny();
            }
            $this->driverId = $userId;
            $this->viaToken = true;
        } elseif (!empty($_SESSION['user']['id']) && in_array($_SESSION['user']['role'] ?? '', ['delivery', 'driver'], true)) {
            $this->driverId = (int) $_SESSION['user']['id'];
        } else {
            $this->deny();
        }

        $lang = $_GET['lang'] ?? ($_SESSION['language'] ?? 'fr');
        $this->fr = $lang !== 'en';
    }

    private function deny(): never
    {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    /** Session callers (portal) must send the CSRF header; token callers (app) are not cookie-based. */
    private function checkCsrf(): bool
    {
        if ($this->viaToken || verifyCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
            return true;
        }
        http_response_code(419);
        echo json_encode(['error' => 'Invalid CSRF token']);
        return false;
    }

    // GET /api/driver/notifications/inbox
    public function index(): void
    {
        try {
            $db    = \Database::getConnection();
            $limit = max(1, min((int) ($_GET['limit'] ?? 10), 50));
            $stmt  = $db->prepare(
                "SELECT id, message, message_fr, type, order_id, po_id, created_at,
                        (read_at IS NOT NULL) AS is_read
                 FROM driver_delivery_notifications
                 WHERE driver_id = ?
                 ORDER BY (read_at IS NULL) DESC, created_at DESC
                 LIMIT ?"
            );
            $stmt->bindValue(1, $this->driverId, \PDO::PARAM_INT);
            $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
            $stmt->execute();
            $notifications = [];
            foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $n) {
                $notifications[] = [
                    'id'         => (int) $n['id'],
                    'message'    => ($this->fr && !empty($n['message_fr'])) ? $n['message_fr'] : $n['message'],
                    'message_en' => $n['message'],
                    'message_fr' => $n['message_fr'],
                    'type'       => $n['type'] !== '' ? $n['type'] : 'info',
                    'order_id'   => $n['order_id'] !== null ? (int) $n['order_id'] : null,
                    'po_id'      => $n['po_id'] !== null ? (int) $n['po_id'] : null,
                    'created_at' => $n['created_at'],
                    'is_read'    => (bool) $n['is_read'],
                ];
            }

            echo json_encode([
                'success'       => true,
                'notifications' => $notifications,
                'unread_count'  => $this->getUnreadCount($db),
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            logger('Driver inbox error: ' . $e->getMessage(), 'error');
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
        if (!$this->checkCsrf()) return;
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
        if (!$this->checkCsrf()) return;
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
