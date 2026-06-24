<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Planner Users API Controller
 * Returns admin users for assignment dropdown
 */
class PlannerUsersController
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

        $this->db = \Database::getConnection();
    }

    /**
     * Get all admin users
     */
    public function index(): void
    {
        try {
            $stmt = $this->db->query("
                SELECT
                    id,
                    CONCAT(first_name, ' ', last_name) as name,
                    email
                FROM users
                WHERE role IN ('super_admin', 'admin', 'admin_staff')
                AND status = 'active'
                ORDER BY first_name, last_name
            ");

            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($users);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch users']);
        }
    }
}
