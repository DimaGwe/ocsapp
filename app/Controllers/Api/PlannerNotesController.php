<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

use App\Helpers\NotificationHelper;

/**
 * Planner Notes API Controller
 * Handles CRUD operations for team planner notes
 */
class PlannerNotesController
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
     * Get all notes with user info and comment count
     */
    public function index(): void
    {
        try {
            $archived = isset($_GET['archived']) && $_GET['archived'] === '1' ? 1 : 0;
            $scope    = $_GET['scope'] ?? 'team';
            $userId   = (int)($_SESSION['user']['id'] ?? 0);

            if ($archived) {
                // Archived view: team archived + user's own personal archived
                $stmt = $this->db->prepare("
                    SELECT n.*,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                        (SELECT COUNT(*) FROM planner_note_comments WHERE note_id = n.id) as comment_count
                    FROM planner_notes n
                    LEFT JOIN users u ON n.user_id = u.id
                    WHERE n.is_archived = 1
                      AND (COALESCE(n.scope,'team') = 'team' OR (n.scope = 'personal' AND n.user_id = ?))
                    ORDER BY n.created_at DESC
                ");
                $stmt->execute([$userId]);
            } elseif ($scope === 'personal') {
                $stmt = $this->db->prepare("
                    SELECT n.*,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                        (SELECT COUNT(*) FROM planner_note_comments WHERE note_id = n.id) as comment_count
                    FROM planner_notes n
                    LEFT JOIN users u ON n.user_id = u.id
                    WHERE COALESCE(n.is_archived, 0) = 0
                      AND n.scope = 'personal' AND n.user_id = ?
                    ORDER BY n.created_at DESC
                ");
                $stmt->execute([$userId]);
            } else {
                $stmt = $this->db->prepare("
                    SELECT n.*,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name,
                        (SELECT COUNT(*) FROM planner_note_comments WHERE note_id = n.id) as comment_count
                    FROM planner_notes n
                    LEFT JOIN users u ON n.user_id = u.id
                    WHERE COALESCE(n.is_archived, 0) = 0
                      AND COALESCE(n.scope, 'team') = 'team'
                    ORDER BY n.created_at DESC
                ");
                $stmt->execute([]);
            }

            $notes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Counts for filter tabs
            $countStmt = $this->db->prepare("
                SELECT
                    SUM(CASE WHEN COALESCE(is_archived,0)=0 AND COALESCE(scope,'team')='team' THEN 1 ELSE 0 END) as team_count,
                    SUM(CASE WHEN COALESCE(is_archived,0)=0 AND scope='personal' AND user_id=? THEN 1 ELSE 0 END) as personal_count,
                    SUM(CASE WHEN is_archived=1 AND (COALESCE(scope,'team')='team' OR (scope='personal' AND user_id=?)) THEN 1 ELSE 0 END) as archived_count
                FROM planner_notes
            ");
            $countStmt->execute([$userId, $userId]);
            $counts = $countStmt->fetch(\PDO::FETCH_ASSOC);

            echo json_encode([
                'notes'          => $notes,
                'team_count'     => (int)($counts['team_count']     ?? 0),
                'personal_count' => (int)($counts['personal_count'] ?? 0),
                'archived_count' => (int)($counts['archived_count'] ?? 0)
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch notes']);
        }
    }

    /**
     * Create a new note
     */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['content']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Content and user_id are required']);
                return;
            }

            $allowedScopes = ['team', 'personal'];
            $scope = in_array($input['scope'] ?? '', $allowedScopes) ? $input['scope'] : 'team';

            $stmt = $this->db->prepare("
                INSERT INTO planner_notes (user_id, content, scope)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $input['user_id'],
                $input['content'],
                $scope
            ]);

            $noteId = $this->db->lastInsertId();

            $logMsg = $scope === 'personal' ? 'added a personal note' : 'added a note';
            $this->logActivity($input['user_id'], 'note', $logMsg);

            // Only fire @mention notifications for team notes
            if ($scope === 'team') {
                $mentions = NotificationHelper::parseMentions($input['content']);
                $authorName = $this->getUserName((int)$input['user_id']);
                $notePreview = mb_substr($input['content'], 0, 80);
                foreach ($mentions as $mention) {
                    if ($mention['user_id'] !== (int)$input['user_id']) {
                        NotificationHelper::addForUser(
                            $mention['user_id'],
                            NotificationHelper::TYPE_MENTION,
                            'You Were Mentioned in a Note',
                            "{$authorName} mentioned you in a note: \"{$notePreview}\"",
                            ['link' => '/admin/planner']
                        );
                    }
                }
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $noteId]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create note']);
        }
    }

    /**
     * Delete a note
     */
    public function destroy(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Note ID is required']);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM planner_notes WHERE id = ?");
            $stmt->execute([$input['id']]);

            // Log activity
            if (!empty($input['user_id'])) {
                $this->logActivity($input['user_id'], 'note', 'deleted a note');
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete note']);
        }
    }

    /**
     * Toggle archive status of a note
     */
    public function toggleArchive(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Note ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT is_archived FROM planner_notes WHERE id = ?");
            $stmt->execute([$input['id']]);
            $note = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$note) {
                http_response_code(404);
                echo json_encode(['error' => 'Note not found']);
                return;
            }

            $newArchived = !$note['is_archived'];

            $stmt = $this->db->prepare("UPDATE planner_notes SET is_archived = ? WHERE id = ?");
            $stmt->execute([$newArchived ? 1 : 0, $input['id']]);

            if (!empty($input['user_id'])) {
                $action = $newArchived ? 'archived a note' : 'restored a note';
                $this->logActivity((int)$input['user_id'], 'note', $action);
            }

            echo json_encode(['success' => true, 'is_archived' => $newArchived]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update note']);
        }
    }

    /**
     * Get comments for a note
     */
    public function getComments(): void
    {
        try {
            $noteId = $_GET['note_id'] ?? null;

            if (!$noteId) {
                http_response_code(400);
                echo json_encode(['error' => 'Note ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT
                    c.*,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM planner_note_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.note_id = ?
                ORDER BY c.created_at ASC
            ");

            $stmt->execute([$noteId]);
            $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch comments']);
        }
    }

    /**
     * Add a comment to a note
     */
    public function storeComment(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['note_id']) || empty($input['user_id']) || empty($input['comment'])) {
                http_response_code(400);
                echo json_encode(['error' => 'note_id, user_id, and comment are required']);
                return;
            }

            $stmt = $this->db->prepare("
                INSERT INTO planner_note_comments (note_id, user_id, comment)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $input['note_id'],
                $input['user_id'],
                $input['comment']
            ]);

            $commentId = $this->db->lastInsertId();

            // Log activity
            $this->logActivity($input['user_id'], 'comment', 'commented on a note');

            // Notify note creator (if different from commenter)
            $noteStmt = $this->db->prepare("SELECT user_id FROM planner_notes WHERE id = ?");
            $noteStmt->execute([$input['note_id']]);
            $note = $noteStmt->fetch(\PDO::FETCH_ASSOC);

            $commenterName = $this->getUserName((int)$input['user_id']);
            $commentPreview = mb_substr($input['comment'], 0, 80);
            $notifiedUsers = [(int)$input['user_id']]; // Don't notify the commenter

            if ($note) {
                $noteCreator = (int)($note['user_id'] ?? 0);
                if ($noteCreator && !in_array($noteCreator, $notifiedUsers)) {
                    NotificationHelper::addForUser(
                        $noteCreator,
                        NotificationHelper::TYPE_NOTE_COMMENT,
                        'New Comment on Your Note',
                        "{$commenterName} commented: \"{$commentPreview}\"",
                        ['link' => '/admin/planner']
                    );
                    $notifiedUsers[] = $noteCreator;
                }
            }

            // Handle @mentions in comment
            $mentions = NotificationHelper::parseMentions($input['comment']);
            foreach ($mentions as $mention) {
                if (!in_array($mention['user_id'], $notifiedUsers)) {
                    NotificationHelper::addForUser(
                        $mention['user_id'],
                        NotificationHelper::TYPE_MENTION,
                        'You Were Mentioned',
                        "{$commenterName} mentioned you in a note comment: \"{$commentPreview}\"",
                        ['link' => '/admin/planner']
                    );
                    $notifiedUsers[] = $mention['user_id'];
                }
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $commentId]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add comment']);
        }
    }

    /**
     * Get user display name by ID
     */
    private function getUserName(int $userId): string
    {
        try {
            $stmt = $this->db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $user ? trim($user['first_name'] . ' ' . $user['last_name']) : 'Someone';
        } catch (\Exception $e) {
            return 'Someone';
        }
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
            // Silent fail - activity logging shouldn't break the main flow
        }
    }
}
