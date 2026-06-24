<?php

namespace App\Controllers;

/**
 * BgcheckController
 * Handles the public (token-based, no login required) background check upload page.
 * Also handles secure admin download of uploaded files.
 */
class BgcheckController
{
    private $db;
    private const UPLOAD_DIR = __DIR__ . '/../../storage/uploads/bgchecks/';
    private const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function isFr(): bool {
        return (($_SESSION['language'] ?? 'fr') === 'fr');
    }

    /**
     * GET /delivery/bgcheck/upload?token=xxx
     * Public page — driver uploads their background check document
     */
    public function uploadPage(): void
    {
        $token = sanitize($_GET['token'] ?? '');
        $app   = $this->getAppByToken($token);

        if (!$app) {
            view('delivery.bgcheck-upload', [
                'pageTitle' => $this->isFr() ? 'Téléversement du casier judiciaire' : 'Background Check Upload',
                'error'     => $this->isFr() ? 'Ce lien est invalide ou a expiré. Veuillez contacter le support OCSAPP.' : 'This link is invalid or has expired. Please contact OCSAPP support.',
                'app'       => null,
                'token'     => '',
            ]);
            return;
        }

        if ($app['bgcheck_status'] === 'uploaded' || $app['bgcheck_status'] === 'verified') {
            view('delivery.bgcheck-upload', [
                'pageTitle' => $this->isFr() ? 'Téléversement du casier judiciaire' : 'Background Check Upload',
                'app'       => $app,
                'token'     => $token,
                'alreadyUploaded' => true,
            ]);
            return;
        }

        view('delivery.bgcheck-upload', [
            'pageTitle' => $this->isFr() ? 'Téléversement du casier judiciaire' : 'Background Check Upload',
            'app'       => $app,
            'token'     => $token,
            'alreadyUploaded' => false,
        ]);
    }

    /**
     * POST /delivery/bgcheck/upload
     * Handles the file upload from the driver
     */
    public function handleUpload(): void
    {
        $token  = sanitize($_POST['token'] ?? '');
        $app    = $this->getAppByToken($token);

        if (!$app) {
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Lien invalide ou expiré.' : 'Invalid or expired link.');
            return;
        }

        if ($app['bgcheck_status'] === 'verified') {
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Votre vérification des antécédents a déjà été vérifiée.' : 'Your background check has already been verified.');
            return;
        }

        // Validate file
        $file = $_FILES['bgcheck_document'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Aucun fichier reçu ou erreur de téléversement. Veuillez réessayer.' : 'No file received or upload error. Please try again.');
            return;
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Le fichier est trop volumineux. Taille maximale : 10 Mo.' : 'File is too large. Maximum size is 10 MB.');
            return;
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, self::ALLOWED_TYPES, true)) {
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Type de fichier invalide. Veuillez téléverser un PDF, JPG ou PNG.' : 'Invalid file type. Please upload a PDF, JPG, or PNG.');
            return;
        }

        $docType = sanitize($_POST['doc_type'] ?? 'Not specified');
        $docDate = $_POST['doc_date'] ?? null;
        if ($docDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $docDate)) {
            $docDate = null;
        }

        // Save file
        $ext      = $mimeType === 'application/pdf' ? 'pdf' : ($mimeType === 'image/png' ? 'png' : 'jpg');
        $filename = 'bgcheck_' . $app['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destPath = self::UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            error_log('BgcheckController: failed to move uploaded file to ' . $destPath);
            $this->redirectUpload($token, 'error', $this->isFr() ? 'Échec du téléversement. Veuillez réessayer ou contacter le support.' : 'Upload failed. Please try again or contact support.');
            return;
        }

        // Delete old file if one exists
        if (!empty($app['bgcheck_file_path'])) {
            $oldPath = self::UPLOAD_DIR . basename($app['bgcheck_file_path']);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Update DB
        $this->db->prepare("
            UPDATE driver_applications
            SET bgcheck_status = 'uploaded',
                bgcheck_file_path = ?,
                bgcheck_doc_type = ?,
                bgcheck_doc_date = ?,
                bgcheck_uploaded_at = NOW()
            WHERE id = ?
        ")->execute([$filename, $docType, $docDate ?: null, $app['id']]);

        // Notify admin
        try {
            \App\Helpers\NotificationHelper::add(
                'system',
                'Background Check Uploaded',
                "{$app['first_name']} {$app['last_name']} has uploaded their background check document and is awaiting verification.",
                [
                    'link'     => '/admin/leads/view?id=' . ($app['lead_id'] ?? ''),
                    'icon'     => 'shield-halved',
                    'priority' => 'high',
                ]
            );
        } catch (\Exception $e) { /* non-critical */ }

        $this->redirectUpload($token, 'success', $this->isFr() ? 'Votre document a été téléversé avec succès. Notre équipe le révisera sous peu.' : 'Your document has been uploaded successfully. Our team will review it shortly.');
    }

    /**
     * GET /admin/delivery/bgcheck/download?app_id=xxx
     * Admin-only secure file download
     */
    public function adminDownload(): void
    {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(403);
            exit('Access denied.');
        }

        $appId = (int)($_GET['app_id'] ?? 0);
        if (!$appId) { http_response_code(400); exit('Invalid request.'); }

        $stmt = $this->db->prepare("SELECT bgcheck_file_path, first_name, last_name FROM driver_applications WHERE id = ? LIMIT 1");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$app || empty($app['bgcheck_file_path'])) {
            http_response_code(404);
            exit('File not found.');
        }

        $filePath = self::UPLOAD_DIR . basename($app['bgcheck_file_path']);
        if (!file_exists($filePath)) {
            http_response_code(404);
            exit('File not found on disk.');
        }

        $ext      = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeMap  = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $mime     = $mimeMap[$ext] ?? 'application/octet-stream';
        $safeName = 'bgcheck_' . preg_replace('/[^a-z0-9_]/i', '_', $app['first_name'] . '_' . $app['last_name']) . '.' . $ext;

        header('Content-Type: ' . $mime);
        header('Content-Disposition: inline; filename="' . $safeName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        readfile($filePath);
        exit;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function getAppByToken(string $token): ?array
    {
        if (strlen($token) < 32) return null;

        $stmt = $this->db->prepare("
            SELECT * FROM driver_applications
            WHERE bgcheck_token = ?
              AND bgcheck_token_expires_at > NOW()
              AND bgcheck_status NOT IN ('verified', 'flagged')
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $app ?: null;
    }

    private function redirectUpload(string $token, string $type, string $message): void
    {
        $_SESSION['bgcheck_flash'] = ['type' => $type, 'message' => $message];
        redirect(url('delivery/bgcheck/upload?token=' . urlencode($token)));
    }
}
