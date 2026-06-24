<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../../Helpers/EmailHelper.php';

/**
 * Planner Meetings API Controller
 * Handles CRUD operations for meeting minutes with email functionality
 */
class PlannerMeetingsController
{
    private $db;

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

        $this->db = \Database::getConnection();
    }

    /**
     * Get all meetings
     */
    public function index(): void
    {
        try {
            $status = $_GET['status'] ?? null;
            $limit = $_GET['limit'] ?? 20;

            $sql = "
                SELECT
                    m.*,
                    CONCAT(u.first_name, ' ', u.last_name) as created_by_name,
                    (SELECT COUNT(*) FROM planner_meeting_attendees WHERE meeting_id = m.id) as attendee_count,
                    (SELECT COUNT(*) FROM planner_meeting_actions WHERE meeting_id = m.id AND status != 'completed') as pending_actions
                FROM planner_meetings m
                LEFT JOIN users u ON m.created_by = u.id
            ";

            $params = [];

            if ($status && $status !== 'all') {
                $sql .= " WHERE m.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY m.meeting_date DESC, m.meeting_time DESC LIMIT ?";
            $params[] = (int)$limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $meetings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'meetings' => $meetings]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch meetings: ' . $e->getMessage()]);
        }
    }

    /**
     * Get a single meeting with all details
     */
    public function show(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            // Get meeting
            $stmt = $this->db->prepare("
                SELECT
                    m.*,
                    CONCAT(u.first_name, ' ', u.last_name) as created_by_name,
                    pm.title as previous_meeting_title
                FROM planner_meetings m
                LEFT JOIN users u ON m.created_by = u.id
                LEFT JOIN planner_meetings pm ON m.previous_meeting_id = pm.id
                WHERE m.id = ?
            ");
            $stmt->execute([$id]);
            $meeting = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            // Get attendees
            $stmt = $this->db->prepare("
                SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM planner_meeting_attendees a
                LEFT JOIN users u ON a.user_id = u.id
                WHERE a.meeting_id = ?
                ORDER BY a.name
            ");
            $stmt->execute([$id]);
            $meeting['attendees'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get items by type
            $stmt = $this->db->prepare("
                SELECT i.*, CONCAT(u.first_name, ' ', u.last_name) as owner_name
                FROM planner_meeting_items i
                LEFT JOIN users u ON i.owner_id = u.id
                WHERE i.meeting_id = ?
                ORDER BY i.sort_order, i.id
            ");
            $stmt->execute([$id]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $meeting['agenda'] = array_filter($items, fn($i) => $i['item_type'] === 'agenda');
            $meeting['discussions'] = array_filter($items, fn($i) => $i['item_type'] === 'discussion');
            $meeting['decisions'] = array_filter($items, fn($i) => $i['item_type'] === 'decision');

            // Get action items
            $stmt = $this->db->prepare("
                SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                FROM planner_meeting_actions a
                LEFT JOIN users u ON a.assigned_to = u.id
                WHERE a.meeting_id = ?
                ORDER BY a.due_date, a.id
            ");
            $stmt->execute([$id]);
            $meeting['actions'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get previous meeting's incomplete actions (for recap)
            $previousActions = [];
            if ($meeting['previous_meeting_id']) {
                $stmt = $this->db->prepare("
                    SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                    FROM planner_meeting_actions a
                    LEFT JOIN users u ON a.assigned_to = u.id
                    WHERE a.meeting_id = ?
                    ORDER BY a.status DESC, a.id
                ");
                $stmt->execute([$meeting['previous_meeting_id']]);
                $previousActions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Return in expected format for frontend
            echo json_encode([
                'success' => true,
                'meeting' => $meeting,
                'attendees' => $meeting['attendees'],
                'items' => $items,
                'actions' => $meeting['actions'],
                'previous_actions' => $previousActions
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch meeting: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new meeting
     */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = $input['user_id'] ?? $_SESSION['user']['id'] ?? null;

            if (empty($input['title']) || empty($input['meeting_date'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Title and meeting_date are required']);
                return;
            }

            if (!$userId) {
                http_response_code(401);
                echo json_encode(['error' => 'User not authenticated']);
                return;
            }

            $this->db->beginTransaction();

            // Insert meeting
            $stmt = $this->db->prepare("
                INSERT INTO planner_meetings (title, meeting_date, meeting_time, location, previous_meeting_id, next_meeting_date, next_meeting_topics, notes, created_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['title'],
                $input['meeting_date'],
                $input['meeting_time'] ?? null,
                $input['location'] ?? null,
                $input['previous_meeting_id'] ?? null,
                $input['next_meeting_date'] ?? null,
                $input['next_meeting_topics'] ?? null,
                $input['notes'] ?? null,
                $userId
            ]);

            $meetingId = $this->db->lastInsertId();

            // Add attendees if provided
            if (!empty($input['attendees'])) {
                $this->saveAttendees($meetingId, $input['attendees']);
            }

            // Add items if provided (agenda, discussion, decision)
            if (!empty($input['items'])) {
                $this->saveItems($meetingId, $input['items']);
            }

            // Add action items if provided
            if (!empty($input['actions'])) {
                $this->saveActions($meetingId, $input['actions']);
            }

            $this->db->commit();

            // Log activity
            $this->logActivity($userId, 'meeting', 'created meeting: ' . $input['title']);

            http_response_code(201);
            echo json_encode([
                'success' => true,
                'meeting' => [
                    'id' => $meetingId,
                    'title' => $input['title'],
                    'status' => 'draft'
                ]
            ]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create meeting: ' . $e->getMessage()]);
        }
    }

    /**
     * Update a meeting
     */
    public function update(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            $this->db->beginTransaction();

            // Build update query
            $updates = [];
            $params = [];

            $allowedFields = ['title', 'meeting_date', 'meeting_time', 'location', 'status',
                             'previous_meeting_id', 'next_meeting_date', 'next_meeting_topics',
                             'notes', 'email_subject', 'email_draft'];

            foreach ($allowedFields as $field) {
                if (isset($input[$field])) {
                    $updates[] = "$field = ?";
                    $params[] = $input[$field];
                }
            }

            if (!empty($updates)) {
                $params[] = $input['id'];
                $sql = "UPDATE planner_meetings SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            // Update attendees if provided
            if (isset($input['attendees'])) {
                $this->db->prepare("DELETE FROM planner_meeting_attendees WHERE meeting_id = ?")->execute([$input['id']]);
                $this->saveAttendees($input['id'], $input['attendees']);
            }

            // Update items if provided
            if (isset($input['items'])) {
                $this->db->prepare("DELETE FROM planner_meeting_items WHERE meeting_id = ?")->execute([$input['id']]);
                $this->saveItems($input['id'], $input['items']);
            }

            // Update actions if provided
            if (isset($input['actions'])) {
                $this->saveActions($input['id'], $input['actions']);
            }

            $this->db->commit();

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update meeting: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a meeting
     */
    public function destroy(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            // Get meeting title for activity log
            $stmt = $this->db->prepare("SELECT title FROM planner_meetings WHERE id = ?");
            $stmt->execute([$input['id']]);
            $meeting = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            // Delete meeting (cascades to related tables)
            $stmt = $this->db->prepare("DELETE FROM planner_meetings WHERE id = ?");
            $stmt->execute([$input['id']]);

            // Log activity
            if (!empty($input['user_id'])) {
                $this->logActivity($input['user_id'], 'meeting', 'deleted meeting: ' . $meeting['title']);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete meeting']);
        }
    }

    /**
     * Add/update a meeting item (agenda, discussion, decision)
     */
    public function addItem(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['meeting_id']) || empty($input['item_type']) || empty($input['content'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID, item_type, and content are required']);
                return;
            }

            $stmt = $this->db->prepare("
                INSERT INTO planner_meeting_items (meeting_id, item_type, content, owner_id, sort_order)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['meeting_id'],
                $input['item_type'],
                $input['content'],
                $input['owner_id'] ?? null,
                $input['sort_order'] ?? 0
            ]);

            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add item: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a meeting item
     */
    public function deleteItem(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Item ID is required']);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM planner_meeting_items WHERE id = ?");
            $stmt->execute([$input['id']]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete item']);
        }
    }

    /**
     * Add/update an action item
     */
    public function addAction(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['meeting_id']) || empty($input['description'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID and description are required']);
                return;
            }

            $stmt = $this->db->prepare("
                INSERT INTO planner_meeting_actions (meeting_id, description, assigned_to, assigned_name, due_date, status)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['meeting_id'],
                $input['description'],
                $input['assigned_to'] ?? null,
                $input['assigned_name'] ?? null,
                $input['due_date'] ?? null,
                $input['status'] ?? 'pending'
            ]);

            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add action: ' . $e->getMessage()]);
        }
    }

    /**
     * Update action item status
     */
    public function updateAction(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Action ID is required']);
                return;
            }

            $updates = [];
            $params = [];

            if (isset($input['description'])) {
                $updates[] = 'description = ?';
                $params[] = $input['description'];
            }
            if (isset($input['assigned_to'])) {
                $updates[] = 'assigned_to = ?';
                $params[] = $input['assigned_to'];
            }
            if (isset($input['assigned_name'])) {
                $updates[] = 'assigned_name = ?';
                $params[] = $input['assigned_name'];
            }
            if (isset($input['due_date'])) {
                $updates[] = 'due_date = ?';
                $params[] = $input['due_date'];
            }
            if (isset($input['status'])) {
                $updates[] = 'status = ?';
                $params[] = $input['status'];
                if ($input['status'] === 'completed') {
                    $updates[] = 'completed_at = NOW()';
                }
            }

            if (!empty($updates)) {
                $params[] = $input['id'];
                $sql = "UPDATE planner_meeting_actions SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update action']);
        }
    }

    /**
     * Delete an action item
     */
    public function deleteAction(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Action ID is required']);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM planner_meeting_actions WHERE id = ?");
            $stmt->execute([$input['id']]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete action']);
        }
    }

    /**
     * Generate email draft from meeting
     */
    public function generateEmail(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            // Get full meeting details
            $_GET['id'] = $id;
            ob_start();
            $this->show();
            $meetingJson = ob_get_clean();
            $decoded = json_decode($meetingJson, true);

            if (isset($decoded['error'])) {
                http_response_code(404);
                echo json_encode($decoded);
                return;
            }

            // show() returns { success, meeting: {...}, attendees: [...], actions: [...] }
            // Flatten into a single array for buildEmailHtml
            $meeting = $decoded['meeting'];
            $meeting['attendees'] = $decoded['attendees'] ?? [];
            $meeting['actions']   = $decoded['actions']   ?? [];

            // Generate email HTML
            $emailHtml = $this->buildEmailHtml($meeting);
            $emailSubject = "Meeting Minutes - {$meeting['title']} - " . date('M j, Y', strtotime($meeting['meeting_date']));

            // Save draft to meeting
            $stmt = $this->db->prepare("
                UPDATE planner_meetings SET email_subject = ?, email_draft = ?, status = 'completed' WHERE id = ?
            ");
            $stmt->execute([$emailSubject, $emailHtml, $id]);

            echo json_encode([
                'success' => true,
                'subject' => $emailSubject,
                'email_html' => $emailHtml
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate email: ' . $e->getMessage()]);
        }
    }

    /**
     * Send meeting minutes email
     */
    public function sendEmail(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            // Get meeting with email draft
            $stmt = $this->db->prepare("
                SELECT m.*, GROUP_CONCAT(a.email) as attendee_emails
                FROM planner_meetings m
                LEFT JOIN planner_meeting_attendees a ON m.id = a.meeting_id
                WHERE m.id = ?
                GROUP BY m.id
            ");
            $stmt->execute([$input['id']]);
            $meeting = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            // Use provided subject/html or saved ones
            $subject = $input['subject'] ?? $meeting['email_subject'];
            $html = $input['html'] ?? $meeting['email_draft'];

            if (empty($html)) {
                http_response_code(400);
                echo json_encode(['error' => 'No email content. Please generate email first.']);
                return;
            }

            // Get recipients from request or from attendee emails
            $recipients = [];
            if (!empty($input['recipients'])) {
                // Use recipients from request (array of {email, name})
                foreach ($input['recipients'] as $r) {
                    if (!empty($r['email'])) {
                        $recipients[] = $r['email'];
                    }
                }
            } elseif (!empty($meeting['attendee_emails'])) {
                // Fall back to meeting attendee emails
                $recipients = array_filter(explode(',', $meeting['attendee_emails']));
            }

            if (empty($recipients)) {
                http_response_code(400);
                echo json_encode(['error' => 'No recipients found. Please add attendees.']);
                return;
            }

            // Send email using EmailHelper
            $sent = \App\Helpers\EmailHelper::send(
                $recipients,
                $subject,
                $html,
                ['from_address' => 'info@ocsapp.ca', 'from_name' => 'OCSAPP Team']
            );

            if ($sent) {
                // Update meeting status
                $stmt = $this->db->prepare("
                    UPDATE planner_meetings
                    SET status = 'sent', sent_at = NOW(), email_subject = ?, email_draft = ?
                    WHERE id = ?
                ");
                $stmt->execute([$subject, $html, $input['id']]);

                // Log activity
                if (!empty($input['user_id'])) {
                    $this->logActivity($input['user_id'], 'meeting', 'sent meeting minutes: ' . $meeting['title']);
                }

                echo json_encode(['success' => true, 'recipients' => count($recipients)]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send email. Please check mail configuration.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send email: ' . $e->getMessage()]);
        }
    }

    /**
     * Get team members (for attendee selection)
     */
    public function getTeamMembers(): void
    {
        try {
            $stmt = $this->db->query("
                SELECT id, email, first_name, last_name, role
                FROM users
                WHERE role IN ('super_admin', 'senior_admin', 'admin', 'junior_admin')
                ORDER BY first_name, last_name
            ");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format for frontend
            $members = array_map(function($u) {
                return [
                    'id' => $u['id'],
                    'email' => $u['email'],
                    'name' => trim($u['first_name'] . ' ' . $u['last_name'])
                ];
            }, $users);

            echo json_encode(['success' => true, 'members' => $members]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch team members']);
        }
    }

    /**
     * Get previous meetings (for linking)
     */
    public function getPreviousMeetings(): void
    {
        try {
            $limit = $_GET['limit'] ?? 10;

            $stmt = $this->db->prepare("
                SELECT id, title, meeting_date
                FROM planner_meetings
                WHERE status IN ('completed', 'sent')
                ORDER BY meeting_date DESC
                LIMIT ?
            ");
            $stmt->execute([(int)$limit]);
            $meetings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'meetings' => $meetings]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch previous meetings']);
        }
    }

    /**
     * Save attendees helper
     */
    private function saveAttendees(int $meetingId, array $attendees): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO planner_meeting_attendees (meeting_id, user_id, email, name, attended)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($attendees as $attendee) {
            $stmt->execute([
                $meetingId,
                $attendee['user_id'] ?? null,
                $attendee['email'],
                $attendee['name'],
                $attendee['attended'] ?? true
            ]);
        }
    }

    /**
     * Save items helper
     */
    private function saveItems(int $meetingId, array $items): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO planner_meeting_items (meeting_id, item_type, content, owner_id, sort_order)
            VALUES (?, ?, ?, ?, ?)
        ");

        $sortOrder = 0;
        foreach ($items as $item) {
            $stmt->execute([
                $meetingId,
                $item['item_type'],
                $item['content'],
                $item['owner_id'] ?? null,
                $item['sort_order'] ?? $sortOrder++
            ]);
        }
    }

    /**
     * Save actions helper (update existing or insert new)
     */
    private function saveActions(int $meetingId, array $actions): void
    {
        foreach ($actions as $action) {
            if (!empty($action['id'])) {
                // Update existing
                $stmt = $this->db->prepare("
                    UPDATE planner_meeting_actions
                    SET description = ?, assigned_to = ?, assigned_name = ?, due_date = ?, status = ?
                    WHERE id = ? AND meeting_id = ?
                ");
                $stmt->execute([
                    $action['description'],
                    $action['assigned_to'] ?? null,
                    $action['assigned_name'] ?? null,
                    $action['due_date'] ?? null,
                    $action['status'] ?? 'pending',
                    $action['id'],
                    $meetingId
                ]);
            } else {
                // Insert new
                $stmt = $this->db->prepare("
                    INSERT INTO planner_meeting_actions (meeting_id, description, assigned_to, assigned_name, due_date, status)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $meetingId,
                    $action['description'],
                    $action['assigned_to'] ?? null,
                    $action['assigned_name'] ?? null,
                    $action['due_date'] ?? null,
                    $action['status'] ?? 'pending'
                ]);
            }
        }
    }

    /**
     * Build email HTML from meeting data
     */
    private function buildEmailHtml(array $meeting): string
    {
        $date = date('F j, Y', strtotime($meeting['meeting_date']));
        $time = $meeting['meeting_time'] ? date('g:i A', strtotime($meeting['meeting_time'])) : '';

        // Attendees list
        $attendeeNames = array_map(fn($a) => htmlspecialchars($a['name'] ?? '', ENT_QUOTES, 'UTF-8'), $meeting['attendees'] ?? []);
        $attendeesStr = implode(', ', $attendeeNames) ?: 'No attendees recorded';

        // Build sections
        $agendaHtml = '';
        if (!empty($meeting['agenda'])) {
            $agendaHtml = '<h3 class="section-title">Agenda Items</h3><ul class="agenda-list">';
            foreach ($meeting['agenda'] as $item) {
                $agendaHtml .= '<li class="agenda-item">' . htmlspecialchars($item['content']) . '</li>';
            }
            $agendaHtml .= '</ul>';
        }

        $discussionsHtml = '';
        if (!empty($meeting['discussions'])) {
            $discussionsHtml = '<h3 class="section-title">Discussion Points</h3><ul>';
            foreach ($meeting['discussions'] as $item) {
                $discussionsHtml .= '<li>' . htmlspecialchars($item['content']) . '</li>';
            }
            $discussionsHtml .= '</ul>';
        }

        $decisionsHtml = '';
        if (!empty($meeting['decisions'])) {
            $decisionsHtml = '<div class="decision-box"><h3 class="section-title">Decisions Made</h3><ul>';
            foreach ($meeting['decisions'] as $item) {
                $decisionsHtml .= '<li>' . htmlspecialchars($item['content']) . '</li>';
            }
            $decisionsHtml .= '</ul></div>';
        }

        $actionsHtml = '';
        if (!empty($meeting['actions'])) {
            $actionsHtml = '<div class="action-items"><h4>Action Items</h4><ul>';
            foreach ($meeting['actions'] as $action) {
                $assignee = htmlspecialchars($action['assigned_to_name'] ?? $action['assigned_name'] ?? 'Unassigned', ENT_QUOTES, 'UTF-8');
                $dueDate = $action['due_date'] ? date('M j', strtotime($action['due_date'])) : 'No due date';
                $status = $action['status'] === 'completed' ? '<span class="status-complete">[COMPLETED]</span>' : '';
                $actionsHtml .= "<li>{$status} " . htmlspecialchars($action['description']) . " - <strong>{$assignee}</strong> - Due: {$dueDate}</li>";
            }
            $actionsHtml .= '</ul></div>';
        }

        $nextMeetingHtml = '';
        if ($meeting['next_meeting_date']) {
            $nextDate = date('F j, Y', strtotime($meeting['next_meeting_date']));
            $nextTopics = $meeting['next_meeting_topics'] ? nl2br(htmlspecialchars($meeting['next_meeting_topics'])) : 'To be determined';
            $nextMeetingHtml = "
                <div class='meeting-details'>
                    <h3>Next Meeting</h3>
                    <p><strong>Date:</strong> {$nextDate}</p>
                    <p><strong>Topics to cover:</strong></p>
                    <p>{$nextTopics}</p>
                </div>
            ";
        }

        // Full email template
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 650px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #00b207 0%, #009906 100%); padding: 25px; text-align: center; border-radius: 8px 8px 0 0; }
        .logo { font-size: 24px; font-weight: 700; color: white; }
        .meeting-title { color: white; font-size: 14px; margin-top: 8px; opacity: 0.9; }
        .content { background: #fff; padding: 25px; border: 1px solid #e0e0e0; border-top: none; }
        .meeting-info { background: #f0fdf4; border: 2px solid #00b207; border-radius: 8px; padding: 15px; margin: 15px 0; }
        .section-title { color: #00b207; font-size: 16px; margin-top: 20px; margin-bottom: 10px; border-bottom: 2px solid #e5e7eb; padding-bottom: 5px; }
        .agenda-list { list-style: none; padding: 0; }
        .agenda-item { padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .decision-box { background: #f0fdf4; border-left: 4px solid #00b207; padding: 15px; margin: 15px 0; }
        .action-items { background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 15px 0; }
        .action-items h4 { color: #92400e; margin: 0 0 10px 0; }
        .status-complete { color: #059669; font-weight: bold; }
        .meeting-details { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 15px 0; }
        .footer { background: #1e293b; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; }
        .footer p { color: #94a3b8; font-size: 12px; margin: 5px 0; }
        .footer .brand { color: #00b207; font-weight: bold; font-size: 14px; }
        ul { padding-left: 20px; }
        li { margin-bottom: 6px; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='logo'>OCSAPP</div>
        <div class='meeting-title'>Meeting Minutes</div>
    </div>
    <div class='content'>
        <p>Hi Team,</p>
        <p>Here are the minutes from our meeting on <strong>{$date}</strong>.</p>

        <div class='meeting-info'>
            <p><strong>Meeting:</strong> " . htmlspecialchars($meeting['title'], ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Date:</strong> {$date} " . ($time ? "at {$time}" : "") . "</p>
            " . ($meeting['location'] ? "<p><strong>Location:</strong> " . htmlspecialchars($meeting['location'], ENT_QUOTES, 'UTF-8') . "</p>" : "") . "
            <p><strong>Attendees:</strong> {$attendeesStr}</p>
        </div>

        {$agendaHtml}
        {$discussionsHtml}
        {$decisionsHtml}
        {$actionsHtml}
        {$nextMeetingHtml}

        <p style='margin-top: 25px;'>If you have any questions or need clarification on any items, please reach out.</p>

        <p>Best regards,<br><strong>OCSAPP Team</strong></p>
    </div>
    <div class='footer'>
        <p class='brand'>OCSAPP Marketplace</p>
        <p>Building the future of B2B commerce in Canada</p>
    </div>
</body>
</html>
        ";
    }

    /**
     * Log activity
     */
    private function logActivity(int $userId, string $type, string $description): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO planner_activity (user_id, activity_type, description)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$userId, $type, $description]);
        } catch (\Exception $e) {
            // Silent fail
        }
    }
}
