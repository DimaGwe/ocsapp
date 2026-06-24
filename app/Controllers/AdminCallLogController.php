<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * Admin Call Log Controller — stores and displays quick disposition records
 */
class AdminCallLogController
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
    // Store a call log (AJAX POST from disposition modal)
    // -------------------------------------------------------------------------
    public function store(): void
    {
        verifyCsrf();
        header('Content-Type: application/json');

        $direction    = in_array($_POST['direction'] ?? '', ['inbound','outbound']) ? $_POST['direction'] : 'outbound';
        $contactType  = sanitize($_POST['contact_type']  ?? 'unknown');
        $contactId    = (int)($_POST['contact_id']   ?? 0) ?: null;
        $contactName  = sanitize($_POST['contact_name']  ?? '');
        $contactPhone = sanitize($_POST['contact_phone'] ?? '');
        $contactEmail = sanitize($_POST['contact_email'] ?? '');
        $outcome      = sanitize($_POST['outcome']       ?? 'other');
        $notes        = sanitize($_POST['notes']         ?? '');
        $callbackAt   = sanitize($_POST['callback_at']   ?? '');
        $createTicket = !empty($_POST['create_ticket']);
        $ticketSubject= sanitize($_POST['ticket_subject'] ?? '');

        $validOutcomes = ['resolved','follow_up','no_answer','voicemail','wrong_number','transferred','callback_scheduled','other'];
        if (!in_array($outcome, $validOutcomes)) $outcome = 'other';

        $validTypes = ['buyer','seller','driver','supplier','lead','unknown'];
        if (!in_array($contactType, $validTypes)) $contactType = 'unknown';

        $callbackAtVal = null;
        if ($callbackAt) {
            $dt = DateTime::createFromFormat('Y-m-d\TH:i', $callbackAt);
            if ($dt) $callbackAtVal = $dt->format('Y-m-d H:i:s');
        }

        // Optionally create support ticket
        $ticketId = null;
        if ($createTicket && $ticketSubject) {
            $year     = date('Y');
            $count    = (int)$this->db->query("SELECT COUNT(*) FROM support_tickets WHERE YEAR(created_at) = $year")->fetchColumn();
            $ticketNo = 'TKT-' . $year . '-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);

            $stmt = $this->db->prepare("
                INSERT INTO support_tickets
                    (ticket_number, subject, channel, category, priority, status,
                     contact_type, contact_id, contact_name, contact_email, contact_phone,
                     assigned_to, created_by, description)
                VALUES (?,?,'phone','general','medium','open',?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $ticketNo, $ticketSubject,
                $contactType, $contactId, $contactName, $contactEmail, $contactPhone,
                $this->user['id'], $this->user['id'],
                $notes ? "Created from call log.\n\n$notes" : 'Created from call log.',
            ]);
            $ticketId = (int)$this->db->lastInsertId();
        }

        $stmt = $this->db->prepare("
            INSERT INTO call_logs
                (agent_id, direction, contact_type, contact_id, contact_name, contact_phone, contact_email,
                 outcome, notes, ticket_id, ticket_subject, callback_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
        ");
        $stmt->execute([
            $this->user['id'], $direction, $contactType, $contactId,
            $contactName, $contactPhone, $contactEmail,
            $outcome, $notes,
            $ticketId,
            ($createTicket && $ticketSubject) ? $ticketSubject : null,
            $callbackAtVal,
        ]);

        $response = ['success' => true, 'outcome' => $outcome];
        if ($ticketId) $response['ticket_id'] = $ticketId;
        echo json_encode($response);
        exit;
    }

    // -------------------------------------------------------------------------
    // Call log history index
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $page    = max(1, (int)($_GET['page']    ?? 1));
        $perPage = 40;
        $offset  = ($page - 1) * $perPage;
        $search  = trim($_GET['q'] ?? '');
        $outcome = $_GET['outcome'] ?? '';
        $agentId = (int)($_GET['agent'] ?? 0);

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(cl.contact_name LIKE ? OR cl.contact_phone LIKE ? OR cl.contact_email LIKE ? OR cl.notes LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if ($outcome) { $where[] = 'cl.outcome = ?'; $params[] = $outcome; }
        if ($agentId) { $where[] = 'cl.agent_id = ?'; $params[] = $agentId; }

        $whereSQL = implode(' AND ', $where);

        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM call_logs cl WHERE $whereSQL");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT cl.*,
                   u.first_name AS agent_first, u.last_name AS agent_last
            FROM call_logs cl
            LEFT JOIN users u ON u.id = cl.agent_id
            WHERE $whereSQL
            ORDER BY cl.created_at DESC
            LIMIT $perPage OFFSET $offset
        ");
        $stmt->execute($params);
        $logs = $stmt->fetchAll();

        // Today's quick stats
        $todayStats = [
            'total'    => (int)$this->db->query("SELECT COUNT(*) FROM call_logs WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
            'resolved' => (int)$this->db->query("SELECT COUNT(*) FROM call_logs WHERE outcome = 'resolved' AND DATE(created_at) = CURDATE()")->fetchColumn(),
            'callbacks'=> (int)$this->db->query("SELECT COUNT(*) FROM call_logs WHERE outcome = 'callback_scheduled' AND callback_at > NOW()")->fetchColumn(),
            'tickets'  => (int)$this->db->query("SELECT COUNT(*) FROM call_logs WHERE ticket_id IS NOT NULL AND DATE(created_at) = CURDATE()")->fetchColumn(),
        ];

        $agents = $this->db->query("SELECT id, first_name, last_name FROM users WHERE role IN ('super_admin','admin','admin_staff') AND status = 'active' ORDER BY first_name")->fetchAll();

        $pageTitle   = 'Call Log';
        $currentPage = 'call-log';
        $content     = $this->renderView('call-logs/index', compact(
            'logs','total','page','perPage','search','outcome','agentId','agents','todayStats','pageTitle'
        ));
        require __DIR__ . '/../Views/admin/layout.php';
    }

    // -------------------------------------------------------------------------
    // Upcoming callbacks (AJAX — used by agent dashboard widget)
    // -------------------------------------------------------------------------
    public function callbacks(): void
    {
        header('Content-Type: application/json');
        $uid = (int)$this->user['id'];

        $stmt = $this->db->prepare("
            SELECT cl.id, cl.contact_name, cl.contact_phone, cl.contact_type,
                   cl.notes, cl.callback_at, cl.ticket_id
            FROM call_logs cl
            WHERE cl.agent_id = ?
              AND cl.callback_at > NOW()
              AND cl.callback_at < DATE_ADD(NOW(), INTERVAL 7 DAY)
            ORDER BY cl.callback_at ASC
            LIMIT 10
        ");
        $stmt->execute([$uid]);
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // =========================================================================
    private function renderView(string $view, array $data = []): string
    {
        extract($data);
        ob_start();
        require __DIR__ . '/../Views/admin/' . $view . '.php';
        return ob_get_clean();
    }
}
