<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../../Helpers/EmailHelper.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
require_once __DIR__ . '/../../Helpers/TwilioHelper.php';

use App\Helpers\NotificationHelper;
use App\Helpers\TwilioHelper;

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

            $where = [];
            $params = [];

            if ($status && $status !== 'all') {
                $where[] = "m.status = ?";
                $params[] = $status;
            }

            // Month filter from the list view (YYYY-MM)
            $month = $_GET['month'] ?? '';
            if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
                $where[] = "m.meeting_date >= ? AND m.meeting_date < ?";
                $params[] = $month . '-01';
                $params[] = date('Y-m-d', strtotime($month . '-01 +1 month'));
            }

            if ($where) {
                $sql .= " WHERE " . implode(' AND ', $where);
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
                    CONCAT(nt.first_name, ' ', nt.last_name) as note_taker_name,
                    CONCAT(ub.first_name, ' ', ub.last_name) as updated_by_name,
                    pm.title as previous_meeting_title
                FROM planner_meetings m
                LEFT JOIN users u ON m.created_by = u.id
                LEFT JOIN users nt ON m.note_taker_id = nt.id
                LEFT JOIN users ub ON m.updated_by = ub.id
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
            $userId = $_SESSION['user']['id'] ?? null;

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
                INSERT INTO planner_meetings (title, meeting_date, meeting_time, location, note_taker_id, previous_meeting_id, next_meeting_date, next_meeting_topics, notes, created_by, updated_by)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['title'],
                $input['meeting_date'],
                $input['meeting_time'] ?? null,
                $input['location'] ?? null,
                ($input['note_taker_id'] ?? '') ?: null,
                $input['previous_meeting_id'] ?? null,
                $input['next_meeting_date'] ?? null,
                $input['next_meeting_topics'] ?? null,
                $input['notes'] ?? null,
                $userId,
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

            // Edit guard: reject the save if someone changed the meeting after this client loaded it
            $stmt = $this->db->prepare("
                SELECT m.revision, m.updated_at, CONCAT(u.first_name, ' ', u.last_name) as updated_by_name
                FROM planner_meetings m
                LEFT JOIN users u ON m.updated_by = u.id
                WHERE m.id = ?
                FOR UPDATE
            ");
            $stmt->execute([$input['id']]);
            $current = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$current) {
                $this->db->rollBack();
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            if (isset($input['revision']) && (int)$input['revision'] !== (int)$current['revision']) {
                $this->db->rollBack();
                http_response_code(409);
                echo json_encode([
                    'success' => false,
                    'conflict' => true,
                    'updated_by_name' => $current['updated_by_name'],
                    'updated_at' => $current['updated_at'],
                    'error' => 'This meeting was changed by someone else after you opened it.'
                ]);
                return;
            }

            // Build update query
            $updates = ['revision = revision + 1', 'updated_by = ?'];
            $params = [$_SESSION['user']['id'] ?? null];

            $allowedFields = ['title', 'meeting_date', 'meeting_time', 'location', 'note_taker_id', 'status',
                             'previous_meeting_id', 'next_meeting_date', 'next_meeting_topics',
                             'notes', 'email_subject', 'email_draft'];

            // Required fields are only updated when non-empty; the rest can be cleared (sent as null/'')
            $requiredFields = ['title', 'meeting_date', 'status'];

            foreach ($allowedFields as $field) {
                if (!array_key_exists($field, $input)) {
                    continue;
                }
                $value = $input[$field];
                if ($value === '') {
                    $value = null;
                }
                if ($value === null && in_array($field, $requiredFields, true)) {
                    continue;
                }
                $updates[] = "$field = ?";
                $params[] = $value;
            }

            $params[] = $input['id'];
            $sql = "UPDATE planner_meetings SET " . implode(', ', $updates) . " WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

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

            echo json_encode(['success' => true, 'revision' => (int)$current['revision'] + 1]);
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

            if (!empty($_SESSION['user']['id'])) {
                $this->logActivity((int)$_SESSION['user']['id'], 'meeting', 'deleted meeting: ' . $meeting['title']);
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

            // Append to the end of the list so new items keep their position after a reload
            $stmt = $this->db->prepare("SELECT COALESCE(MAX(sort_order), -1) + 1 FROM planner_meeting_items WHERE meeting_id = ?");
            $stmt->execute([$input['meeting_id']]);
            $sortOrder = (int)$stmt->fetchColumn();

            $stmt = $this->db->prepare("
                INSERT INTO planner_meeting_items (meeting_id, item_type, content, owner_id, sort_order)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['meeting_id'],
                $input['item_type'],
                $input['content'],
                $input['owner_id'] ?? null,
                $sortOrder
            ]);
            $itemId = $this->db->lastInsertId();

            echo json_encode(['success' => true, 'id' => $itemId, 'revision' => $this->bumpRevision((int)$input['meeting_id'])]);
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

            $meetingId = $this->parentMeetingId('planner_meeting_items', (int)$input['id']);

            $stmt = $this->db->prepare("DELETE FROM planner_meeting_items WHERE id = ?");
            $stmt->execute([$input['id']]);

            echo json_encode(['success' => true, 'revision' => $meetingId ? $this->bumpRevision($meetingId) : null]);
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
            $actionId = $this->db->lastInsertId();

            echo json_encode(['success' => true, 'id' => $actionId, 'revision' => $this->bumpRevision((int)$input['meeting_id'])]);
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

            $meetingId = $this->parentMeetingId('planner_meeting_actions', (int)$input['id']);
            echo json_encode(['success' => true, 'revision' => $meetingId ? $this->bumpRevision($meetingId) : null]);
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

            $meetingId = $this->parentMeetingId('planner_meeting_actions', (int)$input['id']);

            $stmt = $this->db->prepare("DELETE FROM planner_meeting_actions WHERE id = ?");
            $stmt->execute([$input['id']]);

            echo json_encode(['success' => true, 'revision' => $meetingId ? $this->bumpRevision($meetingId) : null]);
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

            $meeting = $this->loadMeetingData((int)$id);
            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            // Generate email HTML
            $emailHtml = $this->buildEmailHtml($meeting);
            $emailSubject = "Meeting Minutes - {$meeting['title']} - " . date('M j, Y', strtotime($meeting['meeting_date']));

            // Save draft to meeting
            $stmt = $this->db->prepare("
                UPDATE planner_meetings
                SET email_subject = ?, email_draft = ?, status = IF(status = 'sent', 'sent', 'completed')
                WHERE id = ?
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

                $sms = [];
                if (!empty($input['recipients'])) {
                    $full = $this->loadMeetingData((int)$input['id']);
                    if ($full) {
                        $sms = $this->sendMeetingSms($input['recipients'], fn($r) => $this->buildMinutesSms($full, $r));
                    }
                }

                if (!empty($_SESSION['user']['id'])) {
                    $this->logActivity((int)$_SESSION['user']['id'], 'meeting', 'sent meeting minutes: ' . $meeting['title'] . $this->smsSummary($sms));
                }

                echo json_encode(['success' => true, 'recipients' => count($recipients), 'sms' => $sms]);
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
     * Preview the pre-meeting invitation (does not change meeting status)
     */
    public function generateInvite(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            $meeting = $this->loadMeetingData((int)$id);
            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            echo json_encode([
                'success' => true,
                'subject' => $this->buildInviteSubject($meeting),
                'email_html' => $this->buildInviteHtml($meeting)
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to generate invite: ' . $e->getMessage()]);
        }
    }

    /**
     * Send the pre-meeting invitation with an .ics calendar attachment
     */
    public function sendInvite(): void
    {
        $icsPath = null;

        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            $meeting = $this->loadMeetingData((int)$input['id']);
            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            // Recipients from request, falling back to all attendees
            $recipients = [];
            $source = !empty($input['recipients']) ? $input['recipients'] : $meeting['attendees'];
            foreach ($source as $r) {
                if (!empty($r['email'])) {
                    $recipients[] = $r['email'];
                }
            }
            $recipients = array_values(array_unique($recipients));

            if (empty($recipients)) {
                http_response_code(400);
                echo json_encode(['error' => 'No recipients found. Please add attendees.']);
                return;
            }

            // Body is always rebuilt server-side from the saved meeting
            $subject = trim($input['subject'] ?? '') ?: $this->buildInviteSubject($meeting);
            $html = $this->buildInviteHtml($meeting);

            $icsPath = tempnam(sys_get_temp_dir(), 'ocs_invite_');
            file_put_contents($icsPath, $this->buildIcs($meeting));

            $sent = \App\Helpers\EmailHelper::send(
                $recipients,
                $subject,
                $html,
                [
                    'from_address' => 'info@ocsapp.ca',
                    'from_name' => 'OCSAPP Team',
                    'attachments' => [['path' => $icsPath, 'name' => 'meeting-invite.ics']]
                ]
            );

            if ($sent) {
                $this->db->prepare("UPDATE planner_meetings SET invite_sent_at = NOW() WHERE id = ?")
                    ->execute([$meeting['id']]);

                // Short SMS after the email; failures are reported, never block the email
                $sms = $this->sendMeetingSms($input['recipients'] ?? [], fn($r) => $this->buildInviteSms($meeting));

                if (!empty($_SESSION['user']['id'])) {
                    $this->logActivity((int)$_SESSION['user']['id'], 'meeting', 'sent meeting invite: ' . $meeting['title'] . $this->smsSummary($sms));
                }

                echo json_encode(['success' => true, 'recipients' => count($recipients), 'sms' => $sms]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to send invite. Please check mail configuration.']);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send invite: ' . $e->getMessage()]);
        } finally {
            if ($icsPath && file_exists($icsPath)) {
                unlink($icsPath);
            }
        }
    }

    /**
     * Get team members (for attendee selection)
     */
    public function getTeamMembers(): void
    {
        try {
            // Role comes from user_roles/roles, not users.role
            // Phone: the number last used for them on a meeting, else their profile number
            $stmt = $this->db->query("
                SELECT DISTINCT u.id, u.email, u.first_name, u.last_name,
                    COALESCE(
                        (SELECT a.phone FROM planner_meeting_attendees a
                         WHERE a.user_id = u.id AND a.phone IS NOT NULL AND a.phone <> ''
                         ORDER BY a.id DESC LIMIT 1),
                        u.phone
                    ) as phone
                FROM users u
                JOIN user_roles ur ON ur.user_id = u.id
                JOIN roles r ON r.id = ur.role_id
                WHERE r.name IN ('super_admin', 'admin', 'admin_staff')
                  AND u.status = 'active'
                ORDER BY u.first_name, u.last_name
            ");
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Format for frontend; only offer numbers Twilio can actually dial
            $members = array_map(function($u) {
                $phone = trim((string)$u['phone']);
                return [
                    'id' => $u['id'],
                    'email' => $u['email'],
                    'name' => trim($u['first_name'] . ' ' . $u['last_name']),
                    'phone' => ($phone !== '' && TwilioHelper::formatPhoneNumber($phone)) ? $phone : ''
                ];
            }, $users);

            echo json_encode(['success' => true, 'members' => $members, 'sms_available' => TwilioHelper::isConfigured()]);
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
     * Comments on a meeting (agenda suggestions, review of the minutes)
     */
    public function getComments(): void
    {
        try {
            $meetingId = (int)($_GET['meeting_id'] ?? 0);

            if (!$meetingId) {
                http_response_code(400);
                echo json_encode(['error' => 'Meeting ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT c.id, c.user_id, c.comment, c.created_at,
                       CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM planner_meeting_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.meeting_id = ?
                ORDER BY c.created_at ASC, c.id ASC
            ");
            $stmt->execute([$meetingId]);

            echo json_encode(['success' => true, 'comments' => $stmt->fetchAll(\PDO::FETCH_ASSOC)]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to fetch comments']);
        }
    }

    public function storeComment(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userId = (int)($_SESSION['user']['id'] ?? 0);
            $meetingId = (int)($input['meeting_id'] ?? 0);
            $comment = trim($input['comment'] ?? '');

            if (!$meetingId || $comment === '') {
                http_response_code(400);
                echo json_encode(['error' => 'meeting_id and comment are required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT id, title, created_by, note_taker_id FROM planner_meetings WHERE id = ?");
            $stmt->execute([$meetingId]);
            $meeting = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$meeting) {
                http_response_code(404);
                echo json_encode(['error' => 'Meeting not found']);
                return;
            }

            $this->db->prepare("INSERT INTO planner_meeting_comments (meeting_id, user_id, comment) VALUES (?, ?, ?)")
                ->execute([$meetingId, $userId, $comment]);
            $commentId = $this->db->lastInsertId();

            $this->logActivity($userId, 'comment', 'commented on meeting: ' . $meeting['title']);

            // Notify whoever owns the minutes (note-taker, else the creator), then any @mentions
            $commenterName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? ''));
            $preview = mb_substr(preg_replace('/@\[([^\]]+)\]\(\d+\)/', '@$1', $comment), 0, 80);
            $link = '/admin/planner?meeting=' . $meetingId;
            $notified = [$userId];

            $owner = (int)($meeting['note_taker_id'] ?: $meeting['created_by']);
            if ($owner && !in_array($owner, $notified, true)) {
                NotificationHelper::addForUser(
                    $owner,
                    NotificationHelper::TYPE_NOTE_COMMENT,
                    'New Comment on Meeting',
                    "{$commenterName} commented on \"{$meeting['title']}\": \"{$preview}\"",
                    ['link' => $link]
                );
                $notified[] = $owner;
            }

            foreach (NotificationHelper::parseMentions($comment) as $mention) {
                $mentionId = (int)$mention['user_id'];
                if (!in_array($mentionId, $notified, true)) {
                    NotificationHelper::addForUser(
                        $mentionId,
                        NotificationHelper::TYPE_MENTION,
                        'You Were Mentioned',
                        "{$commenterName} mentioned you on meeting \"{$meeting['title']}\": \"{$preview}\"",
                        ['link' => $link]
                    );
                    $notified[] = $mentionId;
                }
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $commentId]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to add comment']);
        }
    }

    /**
     * Delete a comment: its author, or a super admin
     */
    public function deleteComment(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $id = (int)($input['id'] ?? 0);

            $stmt = $this->db->prepare("SELECT user_id FROM planner_meeting_comments WHERE id = ?");
            $stmt->execute([$id]);
            $authorId = $stmt->fetchColumn();

            if ($authorId === false) {
                http_response_code(404);
                echo json_encode(['error' => 'Comment not found']);
                return;
            }

            $isAuthor = (int)$authorId === (int)($_SESSION['user']['id'] ?? 0);
            if (!$isAuthor && ($_SESSION['user']['role'] ?? '') !== 'super_admin') {
                http_response_code(403);
                echo json_encode(['error' => 'You can only delete your own comments']);
                return;
            }

            $this->db->prepare("DELETE FROM planner_meeting_comments WHERE id = ?")->execute([$id]);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Failed to delete comment']);
        }
    }

    /**
     * Save attendees helper
     */
    private function saveAttendees(int $meetingId, array $attendees): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO planner_meeting_attendees (meeting_id, user_id, email, phone, name, attended)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        foreach ($attendees as $attendee) {
            $phone = trim((string)($attendee['phone'] ?? ''));
            $stmt->execute([
                $meetingId,
                $attendee['user_id'] ?? null,
                $attendee['email'],
                $phone !== '' ? mb_substr($phone, 0, 20) : null,
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

        // The on-screen order is the saved order
        $sortOrder = 0;
        foreach ($items as $item) {
            $stmt->execute([
                $meetingId,
                $item['item_type'],
                $item['content'],
                $item['owner_id'] ?? null,
                $sortOrder++
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
     * Load a meeting with attendees, items, actions and previous actions,
     * flattened into one array for the email builders. Null if not found.
     */
    private function loadMeetingData(int $id): ?array
    {
        $_GET['id'] = $id;
        ob_start();
        $this->show();
        $decoded = json_decode(ob_get_clean(), true);

        if (empty($decoded['success'])) {
            return null;
        }

        $meeting = $decoded['meeting'];
        $meeting['attendees'] = $decoded['attendees'] ?? [];
        $meeting['actions'] = $decoded['actions'] ?? [];
        $meeting['previous_actions'] = $decoded['previous_actions'] ?? [];

        return $meeting;
    }

    /**
     * Text the recipients who were ticked for SMS. Returns ['sent' => n, 'failed' => [[name, error]], 'skipped' => reason|null]
     */
    private function sendMeetingSms(array $recipients, callable $textFor): array
    {
        $wanted = array_filter($recipients, fn($r) => !empty($r['sms']) && trim((string)($r['phone'] ?? '')) !== '');
        $result = ['sent' => 0, 'failed' => [], 'skipped' => null];

        if (empty($wanted)) {
            return $result;
        }
        if (!TwilioHelper::isConfigured()) {
            $result['skipped'] = 'SMS is not configured (Twilio settings missing)';
            return $result;
        }

        $seen = [];
        foreach ($wanted as $r) {
            $to = TwilioHelper::formatPhoneNumber((string)$r['phone']);
            $name = (string)($r['name'] ?? $r['email'] ?? '');
            if (!$to) {
                $result['failed'][] = ['name' => $name, 'error' => 'Invalid phone number'];
                continue;
            }
            if (isset($seen[$to])) {
                continue;
            }
            $seen[$to] = true;

            $res = TwilioHelper::sendSMS($to, $textFor($r));
            if (!empty($res['success'])) {
                $result['sent']++;
            } else {
                $result['failed'][] = ['name' => $name, 'error' => $res['error'] ?? 'Failed to send'];
            }
        }

        return $result;
    }

    private function smsSummary(array $sms): string
    {
        if (empty($sms['sent']) && empty($sms['failed'])) {
            return '';
        }
        return ' (SMS: ' . (int)$sms['sent'] . ' sent' . (!empty($sms['failed']) ? ', ' . count($sms['failed']) . ' failed' : '') . ')';
    }

    private function smsTitle(array $meeting): string
    {
        $title = trim($meeting['title']);
        return mb_strlen($title) > 60 ? mb_substr($title, 0, 57) . '...' : $title;
    }

    private function buildInviteSms(array $meeting): string
    {
        $when = date('D M j', strtotime($meeting['meeting_date']));
        if (!empty($meeting['meeting_time'])) {
            $when .= ' at ' . date('g:i A', strtotime($meeting['meeting_time']));
        }
        $where = !empty($meeting['location']) ? ', ' . $meeting['location'] : '';

        return "OCSAPP meeting invite: {$this->smsTitle($meeting)}, {$when}{$where}. Agenda and calendar file sent to your email.";
    }

    /**
     * Minutes SMS, personalised with the recipient's own open action items
     */
    private function buildMinutesSms(array $meeting, array $recipient): string
    {
        $userId = (string)($recipient['user_id'] ?? '');
        $name = mb_strtolower(trim((string)($recipient['name'] ?? '')));

        $open = 0;
        foreach ($meeting['actions'] ?? [] as $action) {
            if (($action['status'] ?? '') === 'completed') {
                continue;
            }
            $mine = ($userId !== '' && (string)($action['assigned_to'] ?? '') === $userId)
                || ($name !== '' && mb_strtolower(trim((string)($action['assigned_name'] ?? ''))) === $name);
            if ($mine) {
                $open++;
            }
        }

        $date = date('M j', strtotime($meeting['meeting_date']));
        $actions = $open === 0 ? 'No open action items for you.'
            : "You have {$open} open action item" . ($open > 1 ? 's' : '') . '.';

        return "OCSAPP: minutes for {$this->smsTitle($meeting)} ({$date}) were sent to your email. {$actions}";
    }

    private function buildInviteSubject(array $meeting): string
    {
        return "Meeting Invitation - {$meeting['title']} - " . date('M j, Y', strtotime($meeting['meeting_date']));
    }

    /**
     * Build the pre-meeting invitation HTML
     */
    private function buildInviteHtml(array $meeting): string
    {
        $esc = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

        $date = date('l, F j, Y', strtotime($meeting['meeting_date']));
        $time = $meeting['meeting_time'] ? date('g:i A', strtotime($meeting['meeting_time'])) : '';

        $attendeeNames = array_map(fn($a) => $esc($a['name'] ?? ''), $meeting['attendees'] ?? []);
        $attendeesStr = implode(', ', $attendeeNames) ?: 'To be confirmed';

        $agendaHtml = '';
        if (!empty($meeting['agenda'])) {
            $agendaHtml = '<h3 class="section-title">Agenda</h3><ol>';
            foreach ($meeting['agenda'] as $item) {
                $agendaHtml .= '<li>' . $esc($item['content']) . '</li>';
            }
            $agendaHtml .= '</ol>';
        } else {
            $agendaHtml = '<h3 class="section-title">Agenda</h3><p>The agenda will be shared before the meeting.</p>';
        }

        // Open action items carried over from the previous meeting
        $carryHtml = '';
        $openActions = array_filter($meeting['previous_actions'] ?? [], fn($a) => ($a['status'] ?? '') !== 'completed');
        if (!empty($openActions)) {
            $carryHtml = '<div class="action-items"><h4>Open Action Items to Review'
                . (!empty($meeting['previous_meeting_title']) ? ' (from ' . $esc($meeting['previous_meeting_title']) . ')' : '')
                . '</h4><ul>';
            foreach ($openActions as $action) {
                $assignee = $esc($action['assigned_to_name'] ?? $action['assigned_name'] ?? 'Unassigned');
                $dueDate = $action['due_date'] ? date('M j', strtotime($action['due_date'])) : 'No due date';
                $carryHtml .= '<li>' . $esc($action['description']) . " - <strong>{$assignee}</strong> - Due: {$dueDate}</li>";
            }
            $carryHtml .= '</ul></div>';
        }

        $content = "
        <p>Hi Team,</p>
        <p>You are invited to <strong>" . $esc($meeting['title']) . "</strong>. Details and agenda are below.</p>

        <div class='meeting-info'>
            <p><strong>Meeting:</strong> " . $esc($meeting['title']) . "</p>
            <p><strong>Date:</strong> {$date}" . ($time ? " at {$time}" : "") . "</p>
            " . ($meeting['location'] ? "<p><strong>Location:</strong> " . $esc($meeting['location']) . "</p>" : "") . "
            <p><strong>Invited:</strong> {$attendeesStr}</p>
            " . (!empty($meeting['note_taker_name']) ? "<p><strong>Note-taker:</strong> " . $esc($meeting['note_taker_name']) . "</p>" : "") . "
        </div>

        {$agendaHtml}
        {$carryHtml}

        <p style='margin-top: 25px;'>A calendar file (.ics) is attached so you can add this meeting to your calendar. Please come prepared on the agenda items, and reply to this email if you cannot attend.</p>

        <p>See you there,<br><strong>OCSAPP Team</strong></p>";

        return $this->emailShell('Meeting Invitation', $content);
    }

    /**
     * Build an iCalendar (.ics) event for the meeting.
     * Timed meetings default to 1 hour in Montreal time; untimed ones are all-day.
     */
    private function buildIcs(array $meeting): string
    {
        $escText = fn($v) => str_replace(["\\", ";", ",", "\r\n", "\n"], ["\\\\", "\\;", "\\,", "\\n", "\\n"], (string)$v);

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//OCSAPP//Team Planner//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:planner-meeting-' . $meeting['id'] . '@ocsapp.ca',
            'SEQUENCE:' . time(),
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
        ];

        if (!empty($meeting['meeting_time'])) {
            $start = new \DateTime($meeting['meeting_date'] . ' ' . $meeting['meeting_time'], new \DateTimeZone('America/Toronto'));
            $end = (clone $start)->modify('+1 hour');
            $start->setTimezone(new \DateTimeZone('UTC'));
            $end->setTimezone(new \DateTimeZone('UTC'));
            $lines[] = 'DTSTART:' . $start->format('Ymd\THis\Z');
            $lines[] = 'DTEND:' . $end->format('Ymd\THis\Z');
        } else {
            $day = new \DateTime($meeting['meeting_date']);
            $lines[] = 'DTSTART;VALUE=DATE:' . $day->format('Ymd');
            $lines[] = 'DTEND;VALUE=DATE:' . (clone $day)->modify('+1 day')->format('Ymd');
        }

        $lines[] = 'SUMMARY:' . $escText($meeting['title']);
        if (!empty($meeting['location'])) {
            $lines[] = 'LOCATION:' . $escText($meeting['location']);
        }

        $description = '';
        if (!empty($meeting['agenda'])) {
            $description = "Agenda:\n";
            $n = 1;
            foreach ($meeting['agenda'] as $item) {
                $description .= ($n++) . '. ' . $item['content'] . "\n";
            }
        }
        if ($description !== '') {
            $lines[] = 'DESCRIPTION:' . $escText(rtrim($description));
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        // Fold lines longer than 75 octets (RFC 5545 3.1), without splitting UTF-8 characters
        $folded = [];
        foreach ($lines as $line) {
            while (strlen($line) > 75) {
                $cut = 75;
                while ($cut > 0 && (ord($line[$cut]) & 0xC0) === 0x80) {
                    $cut--;
                }
                $folded[] = substr($line, 0, $cut);
                $line = ' ' . substr($line, $cut);
            }
            $folded[] = $line;
        }

        return implode("\r\n", $folded) . "\r\n";
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

        $content = "
        <p>Hi Team,</p>
        <p>Here are the minutes from our meeting on <strong>{$date}</strong>.</p>

        <div class='meeting-info'>
            <p><strong>Meeting:</strong> " . htmlspecialchars($meeting['title'], ENT_QUOTES, 'UTF-8') . "</p>
            <p><strong>Date:</strong> {$date} " . ($time ? "at {$time}" : "") . "</p>
            " . ($meeting['location'] ? "<p><strong>Location:</strong> " . htmlspecialchars($meeting['location'], ENT_QUOTES, 'UTF-8') . "</p>" : "") . "
            <p><strong>Attendees:</strong> {$attendeesStr}</p>
            " . (!empty($meeting['note_taker_name']) ? "<p><strong>Minutes by:</strong> " . htmlspecialchars($meeting['note_taker_name'], ENT_QUOTES, 'UTF-8') . "</p>" : "") . "
        </div>

        {$agendaHtml}
        {$discussionsHtml}
        {$decisionsHtml}
        {$actionsHtml}
        {$nextMeetingHtml}

        <p style='margin-top: 25px;'>If you have any questions or need clarification on any items, please reach out.</p>

        <p>Best regards,<br><strong>OCSAPP Team</strong></p>";

        return $this->emailShell('Meeting Minutes', $content);
    }

    /**
     * Shared HTML shell (styles, header, footer) for meeting emails
     */
    private function emailShell(string $headerLabel, string $content): string
    {
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
        <div class='meeting-title'>" . htmlspecialchars($headerLabel, ENT_QUOTES, 'UTF-8') . "</div>
    </div>
    <div class='content'>
        {$content}
    </div>
    <div class='footer'>
        <p class='brand'>OCSAPP Marketplace</p>
        <p>The all-in-one digital infrastructure for local commerce.</p>
    </div>
</body>
</html>
        ";
    }

    /**
     * Record a content change on a meeting (edit guard) and return the new revision
     */
    private function bumpRevision(int $meetingId): int
    {
        $this->db->prepare("UPDATE planner_meetings SET revision = revision + 1, updated_by = ? WHERE id = ?")
            ->execute([$_SESSION['user']['id'] ?? null, $meetingId]);

        $stmt = $this->db->prepare("SELECT revision FROM planner_meetings WHERE id = ?");
        $stmt->execute([$meetingId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Meeting that owns an item/action row (table name is internal, never user input)
     */
    private function parentMeetingId(string $table, int $id): ?int
    {
        $stmt = $this->db->prepare("SELECT meeting_id FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        $meetingId = $stmt->fetchColumn();
        return $meetingId ? (int)$meetingId : null;
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
