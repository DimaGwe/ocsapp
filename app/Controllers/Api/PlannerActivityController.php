<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Planner Activity API Controller
 * Handles activity feed
 */
class PlannerActivityController
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
     * Get all activity
     */
    public function index(): void
    {
        try {
            $mine   = isset($_GET['mine']) && $_GET['mine'] === '1';
            $userId = (int)($_SESSION['user']['id'] ?? 0);

            if ($mine) {
                $stmt = $this->db->prepare("
                    SELECT a.*,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name
                    FROM planner_activity a
                    LEFT JOIN users u ON a.user_id = u.id
                    WHERE a.user_id = ?
                    ORDER BY a.created_at DESC
                    LIMIT 20
                ");
                $stmt->execute([$userId]);
            } else {
                $stmt = $this->db->query("
                    SELECT a.*,
                        CONCAT(u.first_name, ' ', u.last_name) as user_name
                    FROM planner_activity a
                    LEFT JOIN users u ON a.user_id = u.id
                    ORDER BY a.created_at DESC
                    LIMIT 100
                ");
            }

            $activities = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($activities);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch activity']);
        }
    }
}
