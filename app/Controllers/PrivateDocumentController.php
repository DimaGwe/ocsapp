<?php

namespace App\Controllers;

use App\Helpers\PrivateUploadHelper;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * Serves private business documents (registration / verification files)
 * GET /documents/file?path=uploads/supplier-applications/supapp_x.pdf
 *
 * Allowed viewers: any admin tier, or the supplier / business account that owns
 * the document. Everyone else gets a 404 so paths can't be probed.
 */
class PrivateDocumentController
{
    private const MIME_TYPES = [
        'pdf'  => 'application/pdf',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
    ];

    public function show(): void
    {
        $relPath = (string)($_GET['path'] ?? '');
        $fullPath = PrivateUploadHelper::absolutePath($relPath);

        if (!$fullPath || !$this->canView($relPath)) {
            $this->notFound();
        }

        if (!is_file($fullPath)) {
            $this->notFound();
        }

        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        header('Content-Type: ' . (self::MIME_TYPES[$ext] ?? 'application/octet-stream'));
        header('Content-Disposition: inline; filename="' . basename($fullPath) . '"');
        header('Content-Length: ' . filesize($fullPath));
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: private, no-store');
        readfile($fullPath);
        exit;
    }

    /**
     * Admins see everything; suppliers and businesses only their own documents
     */
    private function canView(string $relPath): bool
    {
        $role = $_SESSION['user']['role'] ?? null;
        if (isset($_SESSION['user']) && \AdminPermissionHelper::isAdminRole($role)) {
            return true;
        }

        $db = \Database::getConnection();

        // Supplier: documents on their own application
        if (!empty($_SESSION['supplier_id'])) {
            $stmt = $db->prepare("
                SELECT 1 FROM supplier_applications
                WHERE supplier_id = ?
                  AND ? IN (doc_certificate_incorporation, doc_declaration_registration, doc_enterprise_register)
                LIMIT 1
            ");
            $stmt->execute([(int)$_SESSION['supplier_id'], $relPath]);
            if ($stmt->fetchColumn()) {
                return true;
            }
        }

        // Business: documents on their profile or uploaded through their portal
        if (!empty($_SESSION['business']['id'])) {
            $businessId = (int)$_SESSION['business']['id'];
            $stmt = $db->prepare("
                SELECT 1 FROM business_profiles
                WHERE id = ? AND ? IN (doc_certificate, doc_declaration)
                UNION
                SELECT 1 FROM business_documents
                WHERE business_id = ? AND file_path = ?
                LIMIT 1
            ");
            $stmt->execute([$businessId, $relPath, $businessId, $relPath]);
            if ($stmt->fetchColumn()) {
                return true;
            }
        }

        return false;
    }

    private function notFound(): void
    {
        http_response_code(404);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Document not found.';
        exit;
    }
}
