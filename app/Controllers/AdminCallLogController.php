<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../Helpers/ContactCenterHelper.php';

use App\Helpers\ContactCenterHelper;

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
        $contactName  = trim((string)($_POST['contact_name']  ?? ''));
        $contactPhone = trim((string)($_POST['contact_phone'] ?? ''));
        $contactEmail = trim((string)($_POST['contact_email'] ?? ''));
        $outcome      = sanitize($_POST['outcome']       ?? 'other');
        $notes        = trim((string)($_POST['notes']         ?? ''));
        $callbackAt   = sanitize($_POST['callback_at']   ?? '');
        $createTicket = !empty($_POST['create_ticket']);
        $ticketSubject= trim((string)($_POST['ticket_subject'] ?? ''));
        $callLogId    = (int)($_POST['call_log_id'] ?? 0);

        // A Twilio call already has its row (duration, status); the disposition fills it in
        $existing = null;
        if ($callLogId) {
            $stmt = $this->db->prepare("SELECT * FROM call_logs WHERE id = ?");
            $stmt->execute([$callLogId]);
            $existing = $stmt->fetch() ?: null;
        }

        $validOutcomes = ['resolved','follow_up','no_answer','voicemail','wrong_number','transferred','callback_scheduled','other'];
        if (!in_array($outcome, $validOutcomes)) $outcome = 'other';

        $validTypes = ['buyer','seller','driver','supplier','lead','unknown'];
        if (!in_array($contactType, $validTypes)) $contactType = 'unknown';

        $callbackAtVal = null;
        if ($callbackAt) {
            $dt = \DateTime::createFromFormat('Y-m-d\TH:i', $callbackAt);
            if ($dt) $callbackAtVal = $dt->format('Y-m-d H:i:s');
        }

        // Optionally create support ticket
        $ticketId = null;
        if ($createTicket && $ticketSubject) {
            $ticketId = ContactCenterHelper::createTicket([
                'subject'       => $ticketSubject,
                'channel'       => 'phone',
                'contact'       => ['type' => $contactType, 'id' => $contactId, 'name' => $contactName, 'email' => $contactEmail],
                'contact_phone' => $contactPhone,
                'assigned_to'   => $this->user['id'],
                'created_by'    => $this->user['id'],
                'description'   => $notes ? "Created from call log.\n\n$notes" : 'Created from call log.',
            ]);
        }

        if ($existing) {
            $this->db->prepare("
                UPDATE call_logs
                SET agent_id = COALESCE(agent_id, ?), contact_type = ?, contact_id = ?, contact_name = ?, contact_phone = ?,
                    contact_email = ?, outcome = ?, notes = TRIM(CONCAT(IFNULL(notes, ''), IF(? = '', '', CONCAT(' ', ?)))),
                    ticket_id = COALESCE(?, ticket_id), ticket_subject = COALESCE(?, ticket_subject),
                    callback_at = ?, needs_outcome = 0
                WHERE id = ?
            ")->execute([
                $this->user['id'], $contactType, $contactId, $contactName, $contactPhone ?: $existing['contact_phone'],
                $contactEmail, $outcome, $notes, $notes,
                $ticketId, ($createTicket && $ticketSubject) ? $ticketSubject : null,
                $callbackAtVal, $callLogId,
            ]);

            $response = ['success' => true, 'outcome' => $outcome, 'call_log_id' => $callLogId];
            if ($ticketId) $response['ticket_id'] = $ticketId;
            echo json_encode($response);
            exit;
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
        $pending = !empty($_GET['pending']);

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = '(cl.contact_name LIKE ? OR cl.contact_phone LIKE ? OR cl.contact_email LIKE ? OR cl.notes LIKE ?)';
            $like = '%' . $search . '%';
            array_push($params, $like, $like, $like, $like);
        }
        if ($outcome) { $where[] = 'cl.outcome = ?'; $params[] = $outcome; }
        if ($agentId) { $where[] = 'cl.agent_id = ?'; $params[] = $agentId; }
        if ($pending) { $where[] = 'cl.needs_outcome = 1 AND cl.agent_id = ?'; $params[] = $this->user['id']; }

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

        $agents = ContactCenterHelper::agents();

        // Twilio calls still waiting for this agent's disposition
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM call_logs WHERE needs_outcome = 1 AND agent_id = ?");
        $stmt->execute([$this->user['id']]);
        $todayStats['needs_outcome'] = (int)$stmt->fetchColumn();

        $pageTitle   = 'Call Log';
        $currentPage = 'call-log';
        $content     = $this->renderView('call-logs/index', compact(
            'logs','total','page','perPage','search','outcome','agentId','agents','todayStats','pageTitle','pending'
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
