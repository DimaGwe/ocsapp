<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Planner Documents API Controller
 * Handles file uploads and document management
 *
 * Files live outside the web root (storage/uploads/planner/) and are only
 * reachable through view()/download(), which require an admin session.
 */
class PlannerDocumentsController
{
    private $db;
    private $uploadDir;

    // Max upload size in bytes (PHP's upload_max_filesize can be lower; that limit wins)
    private const MAX_SIZE = 20 * 1024 * 1024;

    // Allowed extensions => Content-Type we serve them with (never trust the browser's type)
    private const ALLOWED_TYPES = [
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls'  => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt'  => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'csv'  => 'text/csv',
        'txt'  => 'text/plain',
        'html' => 'text/html',
        'htm'  => 'text/html',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'mp4'  => 'video/mp4',
        'webm' => 'video/webm',
        'mov'  => 'video/quicktime',
        'mp3'  => 'audio/mpeg',
        'wav'  => 'audio/wav',
        'm4a'  => 'audio/mp4',
    ];

    // Served with a CSP sandbox so any script inside can't run on our domain
    private const SANDBOXED_TYPES = ['html', 'htm', 'txt', 'csv'];

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
        $this->uploadDir = __DIR__ . '/../../../storage/uploads/planner/';

        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0750, true);
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
                    d.id, d.user_id, d.original_filename, d.mime_type, d.file_size, d.uploaded_at,
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
            if (!isset($_FILES['file'])) {
                http_response_code(400);
                echo json_encode(['error' => 'File is required']);
                return;
            }

            $file = $_FILES['file'];
            $userId = (int)($_SESSION['user']['id'] ?? 0);

            // Validate file upload
            if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
                http_response_code(400);
                echo json_encode(['error' => 'File is too large (server limit ' . ini_get('upload_max_filesize') . ')']);
                return;
            }
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'File upload failed']);
                return;
            }
            if ($file['size'] > self::MAX_SIZE) {
                http_response_code(400);
                echo json_encode(['error' => 'File is too large (max 20 MB)']);
                return;
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!isset(self::ALLOWED_TYPES[$extension])) {
                http_response_code(400);
                echo json_encode(['error' => 'File type not allowed. Allowed: ' . implode(', ', array_keys(self::ALLOWED_TYPES))]);
                return;
            }

            // Random, unguessable stored name (the original name is kept in the DB only)
            $storedFilename = bin2hex(random_bytes(16)) . '.' . $extension;
            $filePath = $this->uploadDir . $storedFilename;
            $originalName = trim(str_replace(["\r", "\n", "\0"], '', basename($file['name'])));

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to save file']);
                return;
            }
            chmod($filePath, 0640);

            // Save to database
            $stmt = $this->db->prepare("
                INSERT INTO planner_documents (user_id, original_filename, stored_filename, file_path, mime_type, file_size)
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $originalName,
                $storedFilename,
                'storage/uploads/planner/' . $storedFilename,
                self::ALLOWED_TYPES[$extension],
                $file['size']
            ]);
            // Read before logActivity(), whose insert would change lastInsertId()
            $documentId = (int)$this->db->lastInsertId();

            // Log activity
            $this->logActivity($userId, 'document', 'uploaded a document');

            http_response_code(201);
            echo json_encode(['success' => true, 'id' => $documentId]);
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
        $this->sendFile('inline');
    }

    /**
     * Download a document
     */
    public function download(): void
    {
        $this->sendFile('attachment');
    }

    /**
     * Stream a stored document to the logged-in admin
     */
    private function sendFile(string $disposition): void
    {
        try {
            $id = (int)($_GET['id'] ?? 0);

            if (!$id) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Document ID is required']);
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM planner_documents WHERE id = ?");
            $stmt->execute([$id]);
            $document = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$document) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'Document not found']);
                return;
            }

            $filePath = $this->uploadDir . basename($document['stored_filename']);

            if (!is_file($filePath)) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'File not found on server']);
                return;
            }

            // Content-Type comes from our allowlist, not from what the browser sent at upload time
            $extension = strtolower(pathinfo($document['stored_filename'], PATHINFO_EXTENSION));
            $contentType = self::ALLOWED_TYPES[$extension] ?? 'application/octet-stream';
            if (!isset(self::ALLOWED_TYPES[$extension])) {
                $disposition = 'attachment';
            }

            // Header-safe filename (ASCII fallback + UTF-8 version)
            $name = str_replace(["\r", "\n", "\0", '"'], '', $document['original_filename']);
            $asciiName = preg_replace('/[^A-Za-z0-9._ ()-]/', '_', $name);

            header('Content-Type: ' . $contentType);
            header('Content-Disposition: ' . $disposition . '; filename="' . $asciiName . '"; filename*=UTF-8\'\'' . rawurlencode($name));
            header('Content-Length: ' . filesize($filePath));
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: private, no-store');
            if (in_array($extension, self::SANDBOXED_TYPES, true)) {
                header('Content-Security-Policy: sandbox');
            }

            readfile($filePath);
            exit;
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load document']);
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
            $docId = (int)($input['id'] ?? 0);

            if (!$docId) {
                http_response_code(400);
                echo json_encode(['error' => 'Document ID is required']);
                return;
            }

            // Get document info
            $stmt = $this->db->prepare("SELECT * FROM planner_documents WHERE id = ?");
            $stmt->execute([$docId]);
            $document = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$document) {
                http_response_code(404);
                echo json_encode(['error' => 'Document not found']);
                return;
            }

            // Delete file from filesystem
            $filePath = $this->uploadDir . basename($document['stored_filename']);
            if (is_file($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $stmt = $this->db->prepare("DELETE FROM planner_documents WHERE id = ?");
            $stmt->execute([$docId]);

            // Log activity
            $userId = (int)($_SESSION['user']['id'] ?? 0);
            if ($userId) {
                $this->logActivity($userId, 'document', 'deleted a document');
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
