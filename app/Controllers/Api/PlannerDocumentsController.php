<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Planner Documents API Controller
 * Handles file uploads and document management
 */
class PlannerDocumentsController
{
    private $db;
    private $uploadDir;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check authentication - must be logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized. Please log in as admin.']);
            exit;
        }

        // Verify CSRF token for state-changing requests
        verifyCsrfForApi();

        $this->db = \Database::getConnection();
        $this->uploadDir = __DIR__ . '/../../../public/uploads/planner/';

        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }

        // Don't set JSON header here - let each method set appropriate headers
    }

    /**
     * Get all documents
     */
    public function index(): void
    {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("
                SELECT
                    d.*,
                    CONCAT(u.first_name, ' ', u.last_name) as user_name
                FROM planner_documents d
                LEFT JOIN users u ON d.user_id = u.id
                ORDER BY d.uploaded_at DESC
            ");

            $documents = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($documents);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch documents']);
        }
    }

    /**
     * Upload a new document
     */
    public function store(): void
    {
        header('Content-Type: application/json');
        try {
            if (!isset($_FILES['file']) || !isset($_POST['user_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'File and user_id are required']);
                return;
            }

            $file = $_FILES['file'];
            $userId = $_POST['user_id'];

            // Validate file upload
            if ($file['error'] !== UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['error' => 'File upload failed']);
                return;
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $storedFilename = uniqid() . '_' . time() . '.' . $extension;
            $filePath = $this->uploadDir . $storedFilename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save file']);
                return;
            }

            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO planner_documents (user_id, original_filename, stored_filename, file_path, mime_type, file_size)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $file['name'],
                $storedFilename,
                'uploads/planner/' . $storedFilename,
                $file['type'],
                $file['size']
            ]);

            // Log activity
            $this->logActivity($userId, 'document', 'uploaded a document');

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $this->db->lastInsertId()]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload document']);
        }
    }

    /**
     * View/preview a document
     */
    public function view(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Document ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM planner_documents WHERE id = ?");
            $stmt->execute([$id]);
            $document = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$document) {
                http_response_code(404);
                echo json_encode(['error' => 'Document not found']);
                return;
            }

            $filePath = $this->uploadDir . $document['stored_filename'];

            if (!file_exists($filePath)) {
                http_response_code(404);
                echo json_encode(['error' => 'File not found on server']);
                return;
            }

            // Set headers for inline display
            header('Content-Type: ' . $document['mime_type']);
            header('Content-Disposition: inline; filename="' . $document['original_filename'] . '"');
            header('Content-Length: ' . $document['file_size']);

            readfile($filePath);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to view document']);
        }
    }

    /**
     * Download a document
     */
    public function download(): void
    {
        try {
            $id = $_GET['id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['error' => 'Document ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM planner_documents WHERE id = ?");
            $stmt->execute([$id]);
            $document = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$document) {
                http_response_code(404);
                echo json_encode(['error' => 'Document not found']);
                return;
            }

            $filePath = $this->uploadDir . $document['stored_filename'];

            if (!file_exists($filePath)) {
                http_response_code(404);
                echo json_encode(['error' => 'File not found on server']);
                return;
            }

            // Set headers for download
            header('Content-Type: ' . $document['mime_type']);
            header('Content-Disposition: attachment; filename="' . $document['original_filename'] . '"');
            header('Content-Length: ' . $document['file_size']);

            readfile($filePath);
            exit;
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to download document']);
        }
    }

    /**
     * Delete a document
     */
    public function destroy(): void
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            if (empty($input['id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Document ID is required']);
                return;
            }

            // Get document info
            $stmt = $this->db->prepare("SELECT * FROM planner_documents WHERE id = ?");
            $stmt->execute([$input['id']]);
            $document = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$document) {
                http_response_code(404);
                echo json_encode(['error' => 'Document not found']);
                return;
            }

            // Delete file from filesystem
            $filePath = $this->uploadDir . $document['stored_filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM planner_documents WHERE id = ?");
            $stmt->execute([$input['id']]);

            // Log activity
            if (!empty($input['user_id'])) {
                $this->logActivity($input['user_id'], 'document', 'deleted a document');
            }

            echo json_encode(['success' => true]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete document']);
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
