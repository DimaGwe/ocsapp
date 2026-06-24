<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * Admin Agent Dashboard Controller
 * Personal workspace for support agents: my queue, follow-ups, today's stats
 */
class AdminAgentDashboardController
{
    private \PDO $db;
    private array $user;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            header('Location: /admin/login');
            exit;
        }

        $this->db   = \Database::getConnection();
        $this->user = $_SESSION['user'];
    }

    // -------------------------------------------------------------------------
    // Main agent dashboard page
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $uid = (int)$this->user['id'];

        // Agent status
        $agentStatus = $this->getAgentStatus($uid);

        // My open queue (assigned to me, not resolved/closed)
        $queueStmt = $this->db->prepare("
            SELECT st.*,
                   (SELECT COUNT(*) FROM support_ticket_messages m WHERE m.ticket_id = st.id AND m.is_internal = 0) AS reply_count,
                   (SELECT m.created_at FROM support_ticket_messages m WHERE m.ticket_id = st.id ORDER BY m.created_at DESC LIMIT 1) AS last_reply_at
            FROM support_tickets st
            WHERE st.assigned_to = ?
              AND st.status NOT IN ('resolved','closed')
            ORDER BY
                CASE st.priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END,
                st.updated_at DESC
            LIMIT 20
        ");
        $queueStmt->execute([$uid]);
        $myQueue = $queueStmt->fetchAll();

        // Pending contact (any agent — status = pending_contact, or assigned to me)
        $followupStmt = $this->db->prepare("
            SELECT st.*,
                   a.first_name AS agent_first, a.last_name AS agent_last
            FROM support_tickets st
            LEFT JOIN users a ON a.id = st.assigned_to
            WHERE st.status = 'pending_contact'
              AND (st.assigned_to = ? OR st.assigned_to IS NULL)
            ORDER BY st.updated_at ASC
            LIMIT 15
        ");
        $followupStmt->execute([$uid]);
        $followUps = $followupStmt->fetchAll();

        // Today's personal stats
        $stats = [
            'my_open'         => (int)$this->db->prepare("SELECT COUNT(*) FROM support_tickets WHERE assigned_to = ? AND status NOT IN ('resolved','closed')")->execute([$uid]) ? $this->db->prepare("SELECT COUNT(*) FROM support_tickets WHERE assigned_to = ? AND status NOT IN ('resolved','closed')")->execute([$uid]) && 0 : 0,
            'my_resolved_today' => 0,
            'replies_today'     => 0,
            'urgent_open'       => 0,
            'unassigned'        => 0,
        ];

        // Fix stats with proper queries
        $s = $this->db->prepare("SELECT COUNT(*) FROM support_tickets WHERE assigned_to = ? AND status NOT IN ('resolved','closed')");
        $s->execute([$uid]);
        $stats['my_open'] = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM support_tickets WHERE assigned_to = ? AND DATE(resolved_at) = CURDATE()");
        $s->execute([$uid]);
        $stats['my_resolved_today'] = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM support_ticket_messages WHERE sender_id = ? AND sender_type = 'agent' AND is_internal = 0 AND DATE(created_at) = CURDATE()");
        $s->execute([$uid]);
        $stats['replies_today'] = (int)$s->fetchColumn();

        $s = $this->db->query("SELECT COUNT(*) FROM support_tickets WHERE priority = 'urgent' AND status NOT IN ('resolved','closed')");
        $stats['urgent_open'] = (int)$s->fetchColumn();

        $s = $this->db->query("SELECT COUNT(*) FROM support_tickets WHERE assigned_to IS NULL AND status NOT IN ('resolved','closed')");
        $stats['unassigned'] = (int)$s->fetchColumn();

        // Recent activity (last 8 messages/actions I did today)
        $actStmt = $this->db->prepare("
            SELECT m.message, m.created_at, m.is_internal, m.sender_type,
                   st.ticket_number, st.subject, st.id AS ticket_id
            FROM support_ticket_messages m
            JOIN support_tickets st ON st.id = m.ticket_id
            WHERE m.sender_id = ? AND DATE(m.created_at) = CURDATE()
            ORDER BY m.created_at DESC
            LIMIT 8
        ");
        $actStmt->execute([$uid]);
        $recentActivity = $actStmt->fetchAll();

        // Team status (other online agents)
        $teamStmt = $this->db->query("
            SELECT u.id, u.first_name, u.last_name,
                   COALESCE(sas.status, 'offline') AS agent_status,
                   sas.updated_at AS status_updated,
                   (SELECT COUNT(*) FROM support_tickets WHERE assigned_to = u.id AND status NOT IN ('resolved','closed')) AS open_count
            FROM users u
            LEFT JOIN support_agent_status sas ON sas.user_id = u.id
            WHERE u.role IN ('super_admin','admin','admin_staff')
              AND u.status = 'active'
            ORDER BY
                CASE COALESCE(sas.status,'offline') WHEN 'available' THEN 1 WHEN 'busy' THEN 2 WHEN 'break' THEN 3 ELSE 4 END,
                u.first_name
        ");
        $team = $teamStmt->fetchAll();

        $pageTitle   = 'Agent Dashboard';
        $currentPage = 'agent-dashboard';
        $content     = $this->renderView('agent-dashboard', compact(
            'agentStatus', 'myQueue', 'followUps', 'stats', 'recentActivity', 'team', 'pageTitle'
        ));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    // -------------------------------------------------------------------------
    // Update agent status (AJAX POST)
    // -------------------------------------------------------------------------
    public function updateStatus(): void
    {
        verifyCsrf();
        $uid    = (int)$this->user['id'];
        $status = sanitize($_POST['status'] ?? '');
        $allowed = ['available','busy','break','offline'];

        if (!in_array($status, $allowed)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }

        $this->db->prepare("
            INSERT INTO support_agent_status (user_id, status)
            VALUES (?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status), updated_at = NOW()
        ")->execute([$uid, $status]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'status' => $status]);
        exit;
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function getAgentStatus(int $userId): string
    {
        $stmt = $this->db->prepare("SELECT status FROM support_agent_status WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn() ?: 'offline';
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/admin/' . $view . '.php';
        return ob_get_clean();
    }
}
