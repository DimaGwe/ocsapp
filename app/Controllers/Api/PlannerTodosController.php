<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';
require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

use App\Helpers\NotificationHelper;

/**
 * Planner Todos API Controller
 * Handles CRUD operations for team planner tasks
 */
class PlannerTodosController
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
     * Get all todos with user info and comment count
     */
    public function index(): void
    {
        try {
            $mine   = isset($_GET['mine']) && $_GET['mine'] === '1';
            $userId = (int)($_SESSION['user']['id'] ?? 0);

            $baseSelect = "
                SELECT
                    t.*,
                    CONCAT(creator.first_name, ' ', creator.last_name) as creator_name,
                    CONCAT(assigned.first_name, ' ', assigned.last_name) as assigned_name,
                    CONCAT(completed.first_name, ' ', completed.last_name) as completed_name,
                    (SELECT COUNT(*) FROM planner_todo_comments WHERE todo_id = t.id) as comment_count
                FROM planner_todos t
                LEFT JOIN users creator ON t.user_id = creator.id
                LEFT JOIN users assigned ON t.assigned_to = assigned.id
                LEFT JOIN users completed ON t.completed_by = completed.id
            ";

            if ($mine) {
                $stmt = $this->db->prepare($baseSelect . "
                    WHERE (t.user_id = ? OR t.assigned_to = ?)
                    ORDER BY t.is_completed ASC, FIELD(t.priority, 'urgent', 'high', 'medium', 'low') ASC, t.created_at DESC
                ");
                $stmt->execute([$userId, $userId]);
            } else {
                $stmt = $this->db->query($baseSelect . "
                    ORDER BY t.is_completed ASC, FIELD(t.priority, 'urgent', 'high', 'medium', 'low') ASC, t.created_at DESC
                ");
            }

            $todos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Batch-fetch checklist items (avoids N+1)
            if (!empty($todos)) {
                $todoIds = array_column($todos, 'id');
                $placeholders = implode(',', array_fill(0, count($todoIds), '?'));

                $itemStmt = $this->db->prepare("
                    SELECT i.*, CONCAT(u.first_name, ' ', u.last_name) as completed_by_name
                    FROM planner_todo_items i
                    LEFT JOIN users u ON i.completed_by = u.id
                    WHERE i.todo_id IN ({$placeholders})
                    ORDER BY i.sort_order ASC, i.id ASC
                ");
                $itemStmt->execute($todoIds);
                $allItems = $itemStmt->fetchAll(\PDO::FETCH_ASSOC);

                $itemsByTodo = [];
                foreach ($allItems as $item) {
                    $itemsByTodo[$item['todo_id']][] = $item;
                }

                foreach ($todos as &$todo) {
                    $items = $itemsByTodo[$todo['id']] ?? [];
                    $todo['items'] = $items;
                    $todo['items_total'] = count($items);
                    $todo['items_completed'] = count(array_filter($items, fn($i) => $i['is_completed']));
                }
                unset($todo);
            }

            echo json_encode($todos);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch todos']);
        }
    }

    /**
     * Create a new todo
     */
    public function store(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['task']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Task and user_id are required']);
                return;
            }

            $assignedTo = $input['assigned_to'] ?? null;
            $description = !empty($input['description']) ? $input['description'] : null;
            $items = $input['items'] ?? [];
            $allowedPriorities = ['low', 'medium', 'high', 'urgent'];
            $priority = in_array($input['priority'] ?? '', $allowedPriorities) ? $input['priority'] : 'medium';

            $stmt = $this->db->prepare("
                INSERT INTO planner_todos (user_id, task, description, assigned_to, priority)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $input['user_id'],
                $input['task'],
                $description,
                $assignedTo,
                $priority
            ]);

            $todoId = $this->db->lastInsertId();

            // Insert checklist items if provided
            if (!empty($items)) {
                $itemStmt = $this->db->prepare("
                    INSERT INTO planner_todo_items (todo_id, title, sort_order)
                    VALUES (?, ?, ?)
                ");
                foreach ($items as $index => $itemTitle) {
                    $trimmed = trim($itemTitle);
                    if ($trimmed !== '') {
                        $itemStmt->execute([$todoId, $trimmed, $index]);
                    }
                }
            }

            // Log activity
            $description = $assignedTo
                ? 'created a task and assigned it'
                : 'created a task';
            $this->logActivity($input['user_id'], 'todo', $description);

            // Notify assigned user (if different from creator)
            if ($assignedTo && (int)$assignedTo !== (int)$input['user_id']) {
                $creatorName = $this->getUserName((int)$input['user_id']);
                $taskPreview = mb_substr($input['task'], 0, 80);
                NotificationHelper::addForUser(
                    (int)$assignedTo,
                    NotificationHelper::TYPE_TASK_ASSIGNED,
                    'Task Assigned to You',
                    "{$creatorName} assigned you a task: \"{$taskPreview}\"",
                    ['link' => '/admin/planner']
                );
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $todoId]);
        } catch (\PDOException $e) {
            error_log("Planner todo creation error: " . $e->getMessage());

            // Check if it's a table doesn't exist error
            if (strpos($e->getMessage(), "doesn't exist") !== false || strpos($e->getMessage(), 'does not exist') !== false) {
                http_response_code(500);
                echo json_encode(['error' => 'Database table not found. Please run the planner migration: php database/migrations/setup_planner_tables.php']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create todo: ' . $e->getMessage()]);
            }
        } catch (\Exception $e) {
            error_log("Planner todo error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create todo']);
        }
    }

    /**
     * Toggle todo completion status
     */
    public function toggleComplete(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Todo ID and user_id are required']);
                return;
            }

            // Get current status and creator info
            $stmt = $this->db->prepare("SELECT is_completed, user_id, task, assigned_to FROM planner_todos WHERE id = ?");
            $stmt->execute([$input['id']]);
            $todo = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$todo) {
                http_response_code(404);
                echo json_encode(['error' => 'Todo not found']);
                return;
            }

            $newStatus = !$todo['is_completed'];

            // Bulk-toggle checklist items if they exist
            $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM planner_todo_items WHERE todo_id = ?");
            $countStmt->execute([$input['id']]);
            $hasItems = (int)$countStmt->fetch(\PDO::FETCH_ASSOC)['total'] > 0;

            if ($hasItems) {
                if ($newStatus) {
                    $this->db->prepare("
                        UPDATE planner_todo_items
                        SET is_completed = 1, completed_by = ?, completed_at = NOW()
                        WHERE todo_id = ? AND is_completed = 0
                    ")->execute([$input['user_id'], $input['id']]);
                } else {
                    $this->db->prepare("
                        UPDATE planner_todo_items
                        SET is_completed = 0, completed_by = NULL, completed_at = NULL
                        WHERE todo_id = ?
                    ")->execute([$input['id']]);
                }
            }

            // Update parent status
            if ($newStatus) {
                $stmt = $this->db->prepare("
                    UPDATE planner_todos
                    SET is_completed = 1, completed_by = ?, completed_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$input['user_id'], $input['id']]);
                $this->logActivity($input['user_id'], 'todo', 'completed a task');
                $this->notifyTaskCompletion((int)$input['id'], (int)$input['user_id']);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE planner_todos
                    SET is_completed = 0, completed_by = NULL, completed_at = NULL
                    WHERE id = ?
                ");
                $stmt->execute([$input['id']]);
                $this->logActivity($input['user_id'], 'todo', 'reopened a task');
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to toggle todo']);
        }
    }

    /**
     * Delete a todo
     */
    public function destroy(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Todo ID is required']);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM planner_todos WHERE id = ?");
            $stmt->execute([$input['id']]);

            // Log activity
            if (!empty($input['user_id'])) {
                $this->logActivity($input['user_id'], 'todo', 'deleted a task');
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete todo']);
        }
    }

    /**
     * Get comments for a todo
     */
    public function getComments(): void
    {
        try {
            $todoId = $_GET['todo_id'] ?? null;

            if (!$todoId) {
                http_response_code(400);
                echo json_encode(['error' => 'Todo ID is required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT
                    c.*,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM planner_todo_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.todo_id = ?
                ORDER BY c.created_at ASC
            ");

            $stmt->execute([$todoId]);
            $comments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode($comments);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch comments']);
        }
    }

    /**
     * Add a comment to a todo
     */
    public function storeComment(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['todo_id']) || empty($input['user_id']) || empty($input['comment'])) {
                http_response_code(400);
                echo json_encode(['error' => 'todo_id, user_id, and comment are required']);
                return;
            }

            $stmt = $this->db->prepare("
                INSERT INTO planner_todo_comments (todo_id, user_id, comment)
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $input['todo_id'],
                $input['user_id'],
                $input['comment']
            ]);

            $commentId = $this->db->lastInsertId();

            // Log activity
            $this->logActivity($input['user_id'], 'comment', 'commented on a task');

            // Notify task creator and assigned user
            $todoStmt = $this->db->prepare("SELECT user_id, assigned_to, task FROM planner_todos WHERE id = ?");
            $todoStmt->execute([$input['todo_id']]);
            $todo = $todoStmt->fetch(\PDO::FETCH_ASSOC);

            if ($todo) {
                $commenterName = $this->getUserName((int)$input['user_id']);
                $commentPreview = mb_substr($input['comment'], 0, 80);
                $notifiedUsers = [(int)$input['user_id']]; // Don't notify the commenter

                // Notify task creator
                $taskCreator = (int)($todo['user_id'] ?? 0);
                if ($taskCreator && !in_array($taskCreator, $notifiedUsers)) {
                    NotificationHelper::addForUser(
                        $taskCreator,
                        NotificationHelper::TYPE_TASK_COMMENT,
                        'New Comment on Your Task',
                        "{$commenterName} commented: \"{$commentPreview}\"",
                        ['link' => '/admin/planner']
                    );
                    $notifiedUsers[] = $taskCreator;
                }

                // Notify assigned user
                $assignedTo = (int)($todo['assigned_to'] ?? 0);
                if ($assignedTo && !in_array($assignedTo, $notifiedUsers)) {
                    NotificationHelper::addForUser(
                        $assignedTo,
                        NotificationHelper::TYPE_TASK_COMMENT,
                        'New Comment on Your Task',
                        "{$commenterName} commented: \"{$commentPreview}\"",
                        ['link' => '/admin/planner']
                    );
                    $notifiedUsers[] = $assignedTo;
                }

                // Handle @mentions in comment
                $mentions = NotificationHelper::parseMentions($input['comment']);
                foreach ($mentions as $mention) {
                    if (!in_array($mention['user_id'], $notifiedUsers)) {
                        NotificationHelper::addForUser(
                            $mention['user_id'],
                            NotificationHelper::TYPE_MENTION,
                            'You Were Mentioned',
                            "{$commenterName} mentioned you in a task comment: \"{$commentPreview}\"",
                            ['link' => '/admin/planner']
                        );
                        $notifiedUsers[] = $mention['user_id'];
                    }
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
     * Add a checklist item to a todo
     */
    public function storeItem(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['todo_id']) || empty($input['title'])) {
                http_response_code(400);
                echo json_encode(['error' => 'todo_id and title are required']);
                return;
            }

            $stmt = $this->db->prepare("
                SELECT COALESCE(MAX(sort_order), -1) + 1 as next_order
                FROM planner_todo_items WHERE todo_id = ?
            ");
            $stmt->execute([$input['todo_id']]);
            $nextOrder = $stmt->fetch(\PDO::FETCH_ASSOC)['next_order'];

            $stmt = $this->db->prepare("
                INSERT INTO planner_todo_items (todo_id, title, sort_order)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$input['todo_id'], trim($input['title']), $nextOrder]);

            $itemId = $this->db->lastInsertId();

            $this->recalculateParentCompletion((int)$input['todo_id'], (int)($input['user_id'] ?? 0) ?: null);

            if (!empty($input['user_id'])) {
                $this->logActivity((int)$input['user_id'], 'todo', 'added a checklist item');
            }

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $itemId]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add checklist item']);
        }
    }

    /**
     * Toggle a checklist item
     */
    public function toggleItem(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id']) || empty($input['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Item ID and user_id are required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT id, todo_id, is_completed FROM planner_todo_items WHERE id = ?");
            $stmt->execute([$input['id']]);
            $item = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$item) {
                http_response_code(404);
                echo json_encode(['error' => 'Checklist item not found']);
                return;
            }

            if (!$item['is_completed']) {
                $this->db->prepare("
                    UPDATE planner_todo_items
                    SET is_completed = 1, completed_by = ?, completed_at = NOW()
                    WHERE id = ?
                ")->execute([$input['user_id'], $input['id']]);
            } else {
                $this->db->prepare("
                    UPDATE planner_todo_items
                    SET is_completed = 0, completed_by = NULL, completed_at = NULL
                    WHERE id = ?
                ")->execute([$input['id']]);
            }

            $this->recalculateParentCompletion((int)$item['todo_id'], (int)$input['user_id']);

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to toggle checklist item']);
        }
    }

    /**
     * Delete a checklist item
     */
    public function destroyItem(): void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Item ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT todo_id FROM planner_todo_items WHERE id = ?");
            $stmt->execute([$input['id']]);
            $item = $stmt->fetch(\PDO::FETCH_ASSOC);

            $this->db->prepare("DELETE FROM planner_todo_items WHERE id = ?")->execute([$input['id']]);

            if ($item) {
                $this->recalculateParentCompletion((int)$item['todo_id'], (int)($input['user_id'] ?? 0) ?: null);
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete checklist item']);
        }
    }

    /**
     * Recalculate parent todo completion based on checklist items
     */
    private function recalculateParentCompletion(int $todoId, ?int $userId): void
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total, COALESCE(SUM(is_completed), 0) as completed
                FROM planner_todo_items WHERE todo_id = ?
            ");
            $stmt->execute([$todoId]);
            $counts = $stmt->fetch(\PDO::FETCH_ASSOC);

            $total = (int)$counts['total'];
            if ($total === 0) return;

            $allCompleted = ((int)$counts['completed'] === $total);

            $stmt = $this->db->prepare("SELECT is_completed FROM planner_todos WHERE id = ?");
            $stmt->execute([$todoId]);
            $todo = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$todo) return;

            $currentlyCompleted = (bool)$todo['is_completed'];

            if ($allCompleted && !$currentlyCompleted) {
                $this->db->prepare("
                    UPDATE planner_todos
                    SET is_completed = 1, completed_by = ?, completed_at = NOW()
                    WHERE id = ?
                ")->execute([$userId, $todoId]);
                if ($userId) {
                    $this->logActivity($userId, 'todo', 'completed a task (all items done)');
                    $this->notifyTaskCompletion($todoId, $userId);
                }
            } elseif (!$allCompleted && $currentlyCompleted) {
                $this->db->prepare("
                    UPDATE planner_todos
                    SET is_completed = 0, completed_by = NULL, completed_at = NULL
                    WHERE id = ?
                ")->execute([$todoId]);
                if ($userId) {
                    $this->logActivity($userId, 'todo', 'reopened a task (unchecked item)');
                }
            }
        } catch (\Exception $e) {
            error_log("recalculateParentCompletion error: " . $e->getMessage());
        }
    }

    /**
     * Send task completion notification to creator
     */
    private function notifyTaskCompletion(int $todoId, int $completerId): void
    {
        try {
            $stmt = $this->db->prepare("SELECT user_id, task FROM planner_todos WHERE id = ?");
            $stmt->execute([$todoId]);
            $todo = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$todo) return;

            $taskCreator = (int)($todo['user_id'] ?? 0);
            if ($taskCreator && $taskCreator !== $completerId) {
                $completerName = $this->getUserName($completerId);
                $taskPreview = mb_substr($todo['task'] ?? '', 0, 80);
                NotificationHelper::addForUser(
                    $taskCreator,
                    NotificationHelper::TYPE_TASK_COMPLETED,
                    'Task Completed',
                    "{$completerName} completed the task: \"{$taskPreview}\"",
                    ['link' => '/admin/planner']
                );
            }
        } catch (\Exception $e) {
            // Silent fail
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
