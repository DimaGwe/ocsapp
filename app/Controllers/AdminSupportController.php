<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../Helpers/EmailHelper.php';

/**
 * Admin Support Controller — Contact Center Inbox
 */
class AdminSupportController
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
    // Main inbox
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $filter   = $_GET['filter']   ?? 'all';       // all, mine, unassigned, urgent, resolved
        $search   = trim($_GET['q']   ?? '');
        $status   = $_GET['status']   ?? '';
        $priority = $_GET['priority'] ?? '';
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 30;
        $offset   = ($page - 1) * $perPage;

        $where  = ['1=1'];
        $params = [];

        // Preset filters
        switch ($filter) {
            case 'mine':
                $where[] = 'st.assigned_to = ?';
                $params[] = $this->user['id'];
                break;
            case 'unassigned':
                $where[] = 'st.assigned_to IS NULL';
                $where[] = "st.status NOT IN ('resolved','closed')";
                break;
            case 'urgent':
                $where[] = "st.priority = 'urgent'";
                $where[] = "st.status NOT IN ('resolved','closed')";
                break;
            case 'resolved':
                $where[] = "st.status IN ('resolved','closed')";
                break;
            default:
                $where[] = "st.status NOT IN ('closed')";
        }

        if ($status) { $where[] = 'st.status = ?'; $params[] = $status; }
        if ($priority) { $where[] = 'st.priority = ?'; $params[] = $priority; }

        if ($search) {
            $where[] = '(st.ticket_number LIKE ? OR st.subject LIKE ? OR st.contact_name LIKE ? OR st.contact_email LIKE ? OR st.contact_phone LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like, $like);
        }

        $whereSQL = implode(' AND ', $where);

        // Stats
        $stats = $this->getStats();

        // Agents for assignment dropdown
        $agents = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role IN ('super_admin','admin','admin_staff') AND status = 'active' ORDER BY first_name")->fetchAll();

        // Count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM support_tickets st WHERE $whereSQL");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        // Tickets
        $stmt = $this->db->prepare("
            SELECT st.*,
                   a.first_name AS agent_first, a.last_name AS agent_last,
                   (SELECT COUNT(*) FROM support_ticket_messages m WHERE m.ticket_id = st.id) AS message_count,
                   (SELECT COUNT(*) FROM support_ticket_messages m WHERE m.ticket_id = st.id AND m.is_internal = 0) AS reply_count,
                   (SELECT m.created_at FROM support_ticket_messages m WHERE m.ticket_id = st.id ORDER BY m.created_at DESC LIMIT 1) AS last_reply_at
            FROM support_tickets st
            LEFT JOIN users a ON a.id = st.assigned_to
            WHERE $whereSQL
            ORDER BY
                CASE st.priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END,
                st.updated_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        $pageTitle   = 'Support Inbox';
        $currentPage = 'support';
        $content     = $this->renderView('support/index', compact('tickets','stats','agents','filter','search','status','priority','page','perPage','total','pageTitle'));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    // -------------------------------------------------------------------------
    // Create ticket (GET = form, POST = store)
    // -------------------------------------------------------------------------
    public function create(): void
    {
        $agents = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role IN ('super_admin','admin','admin_staff') AND status = 'active' ORDER BY first_name")->fetchAll();

        $pageTitle   = 'New Support Ticket';
        $currentPage = 'support';
        $content     = $this->renderView('support/create', compact('agents','pageTitle'));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    public function store(): void
    {
        verifyCsrf();

        $ticketNumber = $this->generateTicketNumber();
        $subject      = sanitize($_POST['subject'] ?? '');
        $channel      = sanitize($_POST['channel'] ?? 'phone');
        $category     = sanitize($_POST['category'] ?? 'general');
        $priority     = sanitize($_POST['priority'] ?? 'medium');
        $contactType  = sanitize($_POST['contact_type'] ?? 'unknown');
        $contactId    = (int)($_POST['contact_id'] ?? 0) ?: null;
        $contactName  = sanitize($_POST['contact_name'] ?? '');
        $contactEmail = sanitize($_POST['contact_email'] ?? '');
        $contactPhone = sanitize($_POST['contact_phone'] ?? '');
        $orderId      = (int)($_POST['order_id'] ?? 0) ?: null;
        $assignedTo   = (int)($_POST['assigned_to'] ?? 0) ?: null;
        $description  = sanitize($_POST['description'] ?? '');

        if (!$subject) {
            setFlash('error', 'Subject is required.');
            header('Location: /admin/support/create');
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO support_tickets
                (ticket_number, subject, channel, category, priority, status,
                 contact_type, contact_id, contact_name, contact_email, contact_phone,
                 order_id, assigned_to, created_by, description)
            VALUES (?,?,?,?,?,'open',?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $ticketNumber, $subject, $channel, $category, $priority,
            $contactType, $contactId, $contactName, $contactEmail, $contactPhone,
            $orderId, $assignedTo, $this->user['id'], $description,
        ]);
        $ticketId = $this->db->lastInsertId();

        if ($description) {
            $this->addSystemMessage($ticketId, "Ticket opened: $description");
        }

        setFlash('success', "Ticket $ticketNumber created.");
        header("Location: /admin/support/view?id=$ticketId");
        exit;
    }

    // -------------------------------------------------------------------------
    // View single ticket
    // -------------------------------------------------------------------------
    public function view(): void
    {
        $id     = (int)($_GET['id'] ?? 0);
        $ticket = $this->getTicket($id);
        if (!$ticket) { header('Location: /admin/support'); exit; }

        $messages = $this->db->prepare("
            SELECT m.*, u.first_name, u.last_name
            FROM support_ticket_messages m
            LEFT JOIN users u ON u.id = m.sender_id
            WHERE m.ticket_id = ?
            ORDER BY m.created_at ASC
        ");
        $messages->execute([$id]);
        $messages = $messages->fetchAll();

        $agents = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role IN ('super_admin','admin','admin_staff') AND status = 'active' ORDER BY first_name")->fetchAll();

        $pageTitle   = $ticket['ticket_number'] . ' — ' . $ticket['subject'];
        $currentPage = 'support';
        $content     = $this->renderView('support/view', compact('ticket','messages','agents','pageTitle'));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    // -------------------------------------------------------------------------
    // Add message / reply
    // -------------------------------------------------------------------------
    public function reply(): void
    {
        verifyCsrf();
        $id         = (int)($_POST['ticket_id'] ?? 0);
        $message    = sanitize($_POST['message'] ?? '');
        $isInternal = (int)($_POST['is_internal'] ?? 0);
        $ticket     = $this->getTicket($id);

        if (!$ticket || !$message) {
            header("Location: /admin/support/view?id=$id");
            exit;
        }

        $stmt = $this->db->prepare("
            INSERT INTO support_ticket_messages (ticket_id, message, sender_type, sender_id, sender_name, is_internal)
            VALUES (?, ?, 'agent', ?, ?, ?)
        ");
        $agentName = trim(($this->user['first_name'] ?? '') . ' ' . ($this->user['last_name'] ?? ''));
        $stmt->execute([$id, $message, $this->user['id'], $agentName, $isInternal]);

        // Mark first response time
        if (!$ticket['first_response_at'] && !$isInternal) {
            $this->db->prepare("UPDATE support_tickets SET first_response_at = NOW() WHERE id = ?")->execute([$id]);
        }

        // Move to in_progress if still open
        if ($ticket['status'] === 'open' && !$isInternal) {
            $this->db->prepare("UPDATE support_tickets SET status = 'in_progress' WHERE id = ?")->execute([$id]);
        }

        header("Location: /admin/support/view?id=$id#thread-end");
        exit;
    }

    // -------------------------------------------------------------------------
    // Update status
    // -------------------------------------------------------------------------
    public function updateStatus(): void
    {
        verifyCsrf();
        $id     = (int)($_POST['ticket_id'] ?? 0);
        $status = sanitize($_POST['status'] ?? '');

        $allowed = ['open','in_progress','pending_contact','resolved','closed'];
        if (!in_array($status, $allowed)) {
            header("Location: /admin/support/view?id=$id");
            exit;
        }

        $resolvedAt = in_array($status, ['resolved','closed']) ? 'NOW()' : 'NULL';
        $this->db->prepare("UPDATE support_tickets SET status = ?, resolved_at = $resolvedAt WHERE id = ?")->execute([$status, $id]);
        $this->addSystemMessage($id, "Status changed to: $status by " . ($this->user['first_name'] ?? 'agent'));

        // AJAX or redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: /admin/support/view?id=$id");
        exit;
    }

    // -------------------------------------------------------------------------
    // Assign agent
    // -------------------------------------------------------------------------
    public function assign(): void
    {
        verifyCsrf();
        $id        = (int)($_POST['ticket_id'] ?? 0);
        $agentId   = (int)($_POST['assigned_to'] ?? 0) ?: null;

        $this->db->prepare("UPDATE support_tickets SET assigned_to = ? WHERE id = ?")->execute([$agentId, $id]);

        if ($agentId) {
            $agent = $this->db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $agent->execute([$agentId]);
            $a = $agent->fetch();
            $name = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
            $this->addSystemMessage($id, "Assigned to $name");
        } else {
            $this->addSystemMessage($id, "Unassigned");
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: /admin/support/view?id=$id");
        exit;
    }

    // -------------------------------------------------------------------------
    // Update priority
    // -------------------------------------------------------------------------
    public function updatePriority(): void
    {
        verifyCsrf();
        $id       = (int)($_POST['ticket_id'] ?? 0);
        $priority = sanitize($_POST['priority'] ?? '');
        $allowed  = ['low','medium','high','urgent'];
        if (in_array($priority, $allowed)) {
            $this->db->prepare("UPDATE support_tickets SET priority = ? WHERE id = ?")->execute([$priority, $id]);
        }
        header("Location: /admin/support/view?id=$id");
        exit;
    }

    // -------------------------------------------------------------------------
    // Delete ticket
    // -------------------------------------------------------------------------
    public function delete(): void
    {
        verifyCsrf();
        $id = (int)($_POST['ticket_id'] ?? 0);
        $this->db->prepare("DELETE FROM support_tickets WHERE id = ?")->execute([$id]);
        setFlash('success', 'Ticket deleted.');
        header('Location: /admin/support');
        exit;
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function getTicket(int $id): ?array
    {
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
        return $stmt->fetch() ?: null;
    }

    private function getStats(): array
    {
        $uid = $this->user['id'];
        return [
            'open'       => (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE status NOT IN ('closed')")->fetchColumn(),
            'mine'       => (int)$this->db->prepare("SELECT COUNT(*) FROM support_tickets WHERE assigned_to = $uid AND status NOT IN ('resolved','closed')")->fetchColumn(),
            'unassigned' => (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE assigned_to IS NULL AND status NOT IN ('resolved','closed')")->fetchColumn(),
            'urgent'     => (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE priority = 'urgent' AND status NOT IN ('resolved','closed')")->fetchColumn(),
            'resolved_today' => (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE DATE(resolved_at) = CURDATE()")->fetchColumn(),
        ];
    }

    private function addSystemMessage(int $ticketId, string $text): void
    {
        $this->db->prepare("
            INSERT INTO support_ticket_messages (ticket_id, message, sender_type, is_internal)
            VALUES (?, ?, 'system', 1)
        ")->execute([$ticketId, $text]);
    }

    private function generateTicketNumber(): string
    {
        $year  = date('Y');
        $count = (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE YEAR(created_at) = $year")->fetchColumn();
        return 'TKT-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/admin/' . $view . '.php';
        return ob_get_clean();
    }
}
