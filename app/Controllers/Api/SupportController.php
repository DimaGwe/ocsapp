<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Support Ticket AJAX API
 * GET /admin/api/support/ticket?id=N  → renders ticket detail HTML for inbox panel
 */
class SupportController
{
    private \PDO $db;
    private array $user;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->db   = \Database::getConnection();
        $this->user = $_SESSION['user'];
    }

    public function ticket(): void
    {
        header('Content-Type: application/json');

        $id = (int)($_GET['id'] ?? 0);

        $stmt = $this->db->prepare("
            SELECT st.*,
                   a.first_name AS agent_first, a.last_name AS agent_last,
                   c.first_name AS creator_first, c.last_name AS creator_last
            FROM support_tickets st
            LEFT JOIN users a ON a.id = st.assigned_to
            LEFT JOIN users c ON c.id = st.created_by
            WHERE st.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch();

        if (!$ticket) {
            echo json_encode(['error' => 'Not found']);
            exit;
        }

        $msgStmt = $this->db->prepare("
            SELECT m.*, u.first_name, u.last_name
            FROM support_ticket_messages m
            LEFT JOIN users u ON u.id = m.sender_id
            WHERE m.ticket_id = ?
            ORDER BY m.created_at ASC
        ");
        $msgStmt->execute([$id]);
        $messages = $msgStmt->fetchAll();

        $agents = $this->db->query("
            SELECT id, first_name, last_name
            FROM users
            WHERE role IN ('super_admin','admin','admin_staff') AND status = 'active'
            ORDER BY first_name
        ")->fetchAll();

        // Render HTML
        ob_start();
        require __DIR__ . '/../../Views/admin/support/_ticket_detail.php';
        $html = ob_get_clean();

        echo json_encode(['html' => $html]);
    }
}
