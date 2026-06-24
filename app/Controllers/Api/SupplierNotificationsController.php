<?php

namespace App\Controllers\Api;

use App\Helpers\NotificationHelper;

/**
 * Supplier Notifications API Controller
 * Handles AJAX requests for supplier portal notifications
 */
class SupplierNotificationsController
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
     * Get recent notifications
     * GET /api/supplier/notifications
     */
    public function index(): void
    {
        try {
            $limit = min((int) ($_GET['limit'] ?? 10), 20);
            $notifications = NotificationHelper::getSupplierRecent($this->supplierId, $limit);
            $unreadCount = NotificationHelper::getSupplierUnreadCount($this->supplierId);

            $lang = $_SESSION['language'] ?? 'fr';
            foreach ($notifications as &$n) {
                if ($lang === 'fr') {
                    if (!empty($n['title_fr']))   $n['title']   = $n['title_fr'];
                    if (!empty($n['message_fr'])) $n['message'] = $n['message_fr'];
                }
                unset($n['title_fr'], $n['message_fr']);
            }
            unset($n);

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
     * Server-Sent Events stream for instant notification delivery
     * GET /api/supplier/notifications/stream
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

        $supplierId = $this->supplierId;
        session_write_close(); // release session lock so other requests aren't blocked
        $db         = \Database::getConnection();
        $lastCount  = -1;
        $startTime  = time();
        $lastKeep   = 0;

        while ((time() - $startTime) < 55) {
            if (connection_aborted()) {
                break;
            }

            try {
                $count = NotificationHelper::getSupplierUnreadCount($supplierId);

                // Also factor in pending POs
                try {
                    $poStmt = $db->prepare("SELECT COUNT(*) FROM purchase_orders WHERE supplier_id = ? AND status = 'sent'");
                    $poStmt->execute([$supplierId]);
                    $count += (int) $poStmt->fetchColumn();
                } catch (\Exception $e) {}

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
     * GET /api/supplier/notifications/count
     */
    public function count(): void
    {
        try {
            $pendingPoCount = 0;
            $activePoCount  = 0;
            try {
                $poStmt = \Database::getConnection()->prepare(
                    "SELECT status, COUNT(*) as cnt FROM purchase_orders
                     WHERE supplier_id = ? AND status IN ('sent','accepted','preparing','ready_for_pickup')
                     GROUP BY status"
                );
                $poStmt->execute([$this->supplierId]);
                foreach ($poStmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                    if ($row['status'] === 'sent') $pendingPoCount += (int)$row['cnt'];
                    else $activePoCount += (int)$row['cnt'];
                }
            } catch (\Exception $poEx) {
                // non-fatal
            }

            $unpaidInvoiceCount = 0;
            try {
                $invStmt = \Database::getConnection()->prepare(
                    "SELECT COUNT(*) FROM supplier_invoices WHERE supplier_id = ? AND status IN ('sent','overdue','partial')"
                );
                $invStmt->execute([$this->supplierId]);
                $unpaidInvoiceCount = (int)$invStmt->fetchColumn();
            } catch (\Exception $invEx) {}

            // Sales Orders active count (POs with an SO number, not yet completed/cancelled)
            $activeSoCount = 0;
            try {
                $soStmt = \Database::getConnection()->prepare(
                    "SELECT COUNT(*) FROM purchase_orders
                     WHERE supplier_id = ? AND so_number IS NOT NULL
                     AND status NOT IN ('completed','cancelled')"
                );
                $soStmt->execute([$this->supplierId]);
                $activeSoCount = (int)$soStmt->fetchColumn();
            } catch (\Exception $soEx) {}

            // Receivables: invoices not yet paid (sent, partial, overdue, draft)
            $unpaidReceivablesCount = 0;
            try {
                $recStmt = \Database::getConnection()->prepare(
                    "SELECT COUNT(*) FROM supplier_invoices
                     WHERE supplier_id = ? AND status NOT IN ('paid','cancelled')"
                );
                $recStmt->execute([$this->supplierId]);
                $unpaidReceivablesCount = (int)$recStmt->fetchColumn();
            } catch (\Exception $recEx) {}

            $unreadMsgCount = 0;
            try {
                $unreadMsgCount = \App\Controllers\SupplierMessagesController::getUnreadCount($this->supplierId);
            } catch (\Exception $msgEx) {}

            echo json_encode([
                'success'                  => true,
                'unread_count'             => NotificationHelper::getSupplierUnreadCount($this->supplierId),
                'pending_po_count'         => $pendingPoCount,
                'active_po_count'          => $activePoCount,
                'unpaid_invoice_count'     => $unpaidInvoiceCount,
                'active_so_count'          => $activeSoCount,
                'unpaid_receivables_count' => $unpaidReceivablesCount,
                'unread_msg_count'         => $unreadMsgCount,
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch count']);
        }
    }

    /**
     * Mark notification as read
     * POST /api/supplier/notifications/mark-read
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

            $success = NotificationHelper::markSupplierRead((int) $input['id'], $this->supplierId);

            echo json_encode([
                'success' => $success,
                'unread_count' => NotificationHelper::getSupplierUnreadCount($this->supplierId),
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to mark as read']);
        }
    }

    /**
     * Mark all notifications as read
     * POST /api/supplier/notifications/mark-all-read
     */
    public function markAllRead(): void
    {
        try {
            $count = NotificationHelper::markAllSupplierRead($this->supplierId);

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
