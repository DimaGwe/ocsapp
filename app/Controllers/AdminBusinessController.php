<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminBusinessController - Admin Management of Business Accounts
 * Handles listing, viewing, and managing business accounts in the Distribution portal
 */
class AdminBusinessController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is logged in as any admin tier
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    /**
     * List all business accounts
     */
    public function index(): void
    {
        $search = sanitize($_GET['search'] ?? '');
        $status = sanitize($_GET['status'] ?? '');
        $tier = sanitize($_GET['tier'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Build query
        $where = ["r.name = 'business'"];
        $params = [];

        if ($search) {
            $where[] = "(bp.company_name LIKE ? OR u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)";
            $searchTerm = "%$search%";
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if ($status) {
            $where[] = "bp.status = ?";
            $params[] = $status;
        }

        if ($tier) {
            $where[] = "bp.account_tier = ?";
            $params[] = $tier;
        }

        $whereClause = implode(' AND ', $where);

        // Get total count
        $countSql = "
            SELECT COUNT(*) as total
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            INNER JOIN roles r ON ur.role_id = r.id
            LEFT JOIN business_profiles bp ON u.id = bp.user_id
            WHERE $whereClause
        ";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($params);
        $total = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Get business accounts
        $sql = "
            SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status as user_status, u.created_at,
                   bp.id as business_id, bp.company_name, bp.delivery_city, bp.delivery_province,
                   bp.account_tier, bp.credit_limit, bp.is_credit_approved, bp.status as business_status
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            INNER JOIN roles r ON ur.role_id = r.id
            LEFT JOIN business_profiles bp ON u.id = bp.user_id
            WHERE $whereClause
            ORDER BY u.created_at DESC
            LIMIT :_limit OFFSET :_offset
        ";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $i => $val) {
            $stmt->bindValue($i + 1, $val);
        }
        $stmt->bindValue(':_limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':_offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $businesses = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get stats
        $stats = $this->getStats();

        view('admin.business-accounts.index', [
            'businesses' => $businesses,
            'stats' => $stats,
            'search' => $search,
            'status' => $status,
            'tier' => $tier,
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => ceil($total / $perPage)
        ]);
    }

    /**
     * View single business account
     */
    public function view(): void
    {
        $id = (int)($_GET['id'] ?? 0);

        if (!$id) {
            redirect('admin/business-accounts');
            return;
        }

        // Get business details
        $stmt = $this->db->prepare("
            SELECT u.*, bp.*,
                   u.id as user_id, u.status as user_status, u.created_at as user_created_at,
                   bp.id as business_id, bp.status as business_status, bp.created_at as business_created_at
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            INNER JOIN roles r ON ur.role_id = r.id
            LEFT JOIN business_profiles bp ON u.id = bp.user_id
            WHERE bp.id = ? AND r.name = 'business'
        ");
        $stmt->execute([$id]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            $_SESSION['admin_error'] = 'Business account not found.';
            redirect('admin/business-accounts');
            return;
        }

        // Fetch documents from business_documents table
        $docStmt = $this->db->prepare("
            SELECT * FROM business_documents
            WHERE business_id = ?
            ORDER BY uploaded_at DESC
        ");
        $docStmt->execute([$id]);
        $documents = $docStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch activity log (last 50)
        $actStmt = $this->db->prepare("
            SELECT * FROM business_activity_log
            WHERE business_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $actStmt->execute([$id]);
        $activityLog = $actStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch email log (last 30)
        $emailStmt = $this->db->prepare("
            SELECT * FROM business_email_log
            WHERE business_id = ?
            ORDER BY sent_at DESC
            LIMIT 30
        ");
        $emailStmt->execute([$id]);
        $emailLog = $emailStmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.business-accounts.view', [
            'business'    => $business,
            'documents'   => $documents,
            'activityLog' => $activityLog,
            'emailLog'    => $emailLog,
        ]);
    }

    /**
     * Approve a pending business application
     */
    public function approve(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            back();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            // Fetch business and user info for email
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$id]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) {
                $_SESSION['admin_error'] = 'Business account not found.';
                redirect('admin/business-accounts');
                return;
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE business_profiles
                SET status = 'active',
                    verified_at = NOW(),
                    verified_by = ?,
                    rejection_reason = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user']['id'] ?? null, $id]);

            $this->db->commit();

            // Send approval email with PDF attachments (bilingual template)
            try {
                $attachments = $this->generateApprovalPdfs();

                \App\Helpers\EmailHelper::sendDistributionApproved(
                    $business['email'],
                    [
                        'first_name'   => $business['first_name'] ?? '',
                        'company_name' => $business['company_name'] ?? '',
                        'login_url'    => url('distribution/login'),
                    ],
                    $attachments
                );

                foreach ($attachments as $att) {
                    if (file_exists($att['path'])) {
                        @unlink($att['path']);
                    }
                }
            } catch (\Exception $e) {
                error_log('Failed to send distribution approval email: ' . $e->getMessage());
            }

            // Notify business bell
            try {
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    (int)$id,
                    'account_approved',
                    'Compte approuvé / Account Approved',
                    'Votre compte de distribution OCSAPP a été approuvé. Connectez-vous pour signer votre accord et accéder au portail. / Your OCSAPP distribution account has been approved. Log in to sign your agreement and access the portal.',
                    '/distribution/documents',
                    'check-circle'
                );
            } catch (\Exception $e) {
                error_log('Failed to add business approval notification: ' . $e->getMessage());
            }

            // Activity + email log
            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            \App\Helpers\NotificationHelper::logBusinessActivity((int)$id, 'account_approved', "Application approved by {$adminName}.", 'admin', $adminName);
            \App\Helpers\NotificationHelper::logBusinessEmail((int)$id, 'Distribution Account Approved - OCS Marketplace', 'Your distribution account has been approved. Log in to sign your agreement and access the portal.');

            $_SESSION['admin_success'] = 'Business account approved and applicant notified.';

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Approve business error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to approve business account.';
        }

        $backUrl = sanitize($_POST['back_url'] ?? '');
        redirect($backUrl ?: 'admin/business-accounts/view?id=' . $id);
    }

    /**
     * Reject a pending business application
     */
    public function reject(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            back();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $reason = sanitize($_POST['rejection_reason'] ?? '');

        try {
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$id]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) {
                $_SESSION['admin_error'] = 'Business account not found.';
                redirect('admin/business-accounts');
                return;
            }

            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE business_profiles
                SET status = 'suspended',
                    rejection_reason = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reason ?: null, $id]);

            $this->db->commit();

            // Send rejection email
            try {
                $firstName   = htmlspecialchars($business['first_name'] ?? '');
                $companyName = htmlspecialchars($business['company_name'] ?? '');
                $reasonHtml  = $reason ? '<p style="background:#fef2f2;border-left:4px solid #ef4444;padding:12px 16px;border-radius:0 8px 8px 0;color:#991b1b;font-size:14px;"><strong>Reason:</strong> ' . htmlspecialchars($reason) . '</p>' : '';
                \App\Helpers\EmailHelper::sendRaw(
                    $business['email'],
                    'Update on Your Distribution Application — OCS Marketplace',
                    "
                    <p>Hello {$firstName},</p>
                    <p>Thank you for your interest in OCSAPP Distribution. After reviewing your application for <strong>{$companyName}</strong>, we are unable to approve it at this time.</p>
                    {$reasonHtml}
                    <p>If you believe this is an error or would like to reapply with updated information, please contact us at <a href='mailto:info@ocsapp.ca'>info@ocsapp.ca</a>.</p>
                    <p>Thank you for your understanding,<br>OCS Marketplace Team</p>
                    "
                );
            } catch (\Exception $e) {
                error_log('Failed to send distribution rejection email: ' . $e->getMessage());
            }

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            $reasonNote = $reason ? " Reason: {$reason}" : '';
            \App\Helpers\NotificationHelper::logBusinessActivity((int)$id, 'account_rejected', "Application rejected by {$adminName}.{$reasonNote}", 'admin', $adminName);
            \App\Helpers\NotificationHelper::logBusinessEmail((int)$id, 'Update on Your Distribution Application - OCS Marketplace', 'After reviewing your application, we are unable to approve it at this time.' . $reasonNote);

            $_SESSION['admin_success'] = 'Application rejected and applicant notified.';

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Reject business error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to reject application.';
        }

        $backUrl = sanitize($_POST['back_url'] ?? '');
        redirect($backUrl ?: 'admin/business-accounts/view?id=' . $id);
    }

    /**
     * Suspend business account
     */
    public function suspend(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            redirect('admin/business-accounts');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            $stmt = $this->db->prepare("UPDATE business_profiles SET status = 'suspended', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            \App\Helpers\NotificationHelper::logBusinessActivity($id, 'account_suspended', "Account suspended by {$adminName}.", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Business account suspended successfully.';

        } catch (\PDOException $e) {
            error_log('Suspend business error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to suspend business account.';
        }

        redirect('admin/business-accounts');
    }

    /**
     * Activate business account
     */
    public function activate(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            redirect('admin/business-accounts');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            $stmt = $this->db->prepare("UPDATE business_profiles SET status = 'active', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            \App\Helpers\NotificationHelper::logBusinessActivity($id, 'account_activated', "Account activated by {$adminName}.", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Business account activated successfully.';

        } catch (\PDOException $e) {
            error_log('Activate business error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to activate business account.';
        }

        redirect('admin/business-accounts');
    }

    /**
     * Update account tier
     */
    public function updateTier(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            redirect('admin/business-accounts');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);
        $tier = sanitize($_POST['tier'] ?? '');
        $creditLimit = (float)($_POST['credit_limit'] ?? 0);

        $validTiers = ['standard', 'approved', 'premium'];
        if (!in_array($tier, $validTiers)) {
            $_SESSION['admin_error'] = 'Invalid account tier.';
            redirect('admin/business-accounts/view?id=' . $id);
            return;
        }

        try {
            $isCreditApproved = $tier === 'premium' ? 1 : 0;

            $stmt = $this->db->prepare("
                UPDATE business_profiles
                SET account_tier = ?,
                    credit_limit = ?,
                    is_credit_approved = ?,
                    credit_approved_at = ?,
                    credit_approved_by = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $tier,
                $creditLimit,
                $isCreditApproved,
                $isCreditApproved ? date('Y-m-d H:i:s') : null,
                $isCreditApproved ? $_SESSION['user']['id'] : null,
                $id
            ]);

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            $creditNote = $creditLimit > 0 ? ", credit limit: \${$creditLimit}" : '';
            \App\Helpers\NotificationHelper::logBusinessActivity($id, 'tier_updated', "Account tier updated to '{$tier}'{$creditNote} by {$adminName}.", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Account tier updated successfully.';

        } catch (\PDOException $e) {
            error_log('Update tier error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to update account tier.';
        }

        redirect('admin/business-accounts/view?id=' . $id);
    }

    /**
     * Delete business account
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            redirect('admin/business-accounts');
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        try {
            // Fetch full info before deletion so we can email and notify
            $stmt = $this->db->prepare("
                SELECT bp.id as business_id, bp.company_name, bp.user_id,
                       u.first_name, u.last_name, u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$id]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$business) {
                $_SESSION['admin_error'] = 'Business account not found.';
                redirect('admin/business-accounts');
                return;
            }

            $deleteReason = $_POST['delete_reason'] ?? 'other';
            $deleteNotes  = $_POST['delete_notes']  ?? '';
            $canRejoin    = (($_POST['can_rejoin'] ?? '1') === '1') ? 1 : 0;

            $this->db->beginTransaction();

            // Archive to deleted_users before hard delete
            $this->db->prepare("
                INSERT INTO deleted_users
                    (original_id, email, first_name, last_name, role, reason, notes, deleted_by, can_rejoin)
                VALUES (?, ?, ?, ?, 'business', ?, ?, ?, ?)
            ")->execute([
                $business['user_id'],
                $business['email']      ?? '',
                $business['first_name'] ?? '',
                $business['last_name']  ?? '',
                $deleteReason,
                $deleteNotes,
                $_SESSION['user']['id'] ?? null,
                $canRejoin,
            ]);

            // Delete business profile (cascades from user delete)
            $stmt = $this->db->prepare("DELETE FROM business_profiles WHERE id = ?");
            $stmt->execute([$id]);

            // Delete user_roles
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$business['user_id']]);

            // Delete user
            $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$business['user_id']]);

            $this->db->commit();

            // Email the deleted account holder
            try {
                \App\Helpers\EmailHelper::sendBusinessAccountRemoved([
                    'email'        => $business['email'],
                    'first_name'   => $business['first_name'],
                    'company_name' => $business['company_name'] ?? ($business['first_name'] . ' ' . $business['last_name']),
                ]);
            } catch (\Exception $e) {
                error_log('Failed to send business-account-removed email: ' . $e->getMessage());
            }

            // Admin bell notification
            try {
                $companyLabel = htmlspecialchars($business['company_name'] ?? ($business['first_name'] . ' ' . $business['last_name']));
                $this->db->prepare("
                    INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                    VALUES ('account_removed', ?, ?, ?, 'trash', 'normal', NOW())
                ")->execute([
                    'Business Account Deleted',
                    "Distribution account for {$companyLabel} ({$business['email']}) was deleted by admin.",
                    '/admin/business-accounts',
                ]);
            } catch (\Exception $e) {
                error_log('Failed to insert admin notification for business deletion: ' . $e->getMessage());
            }

            // Activity + email log (business_id still valid even after profile delete for audit purposes)
            try {
                $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
                \App\Helpers\NotificationHelper::logBusinessActivity((int)$id, 'account_deleted', "Account permanently deleted by {$adminName}.", 'admin', $adminName);
                \App\Helpers\NotificationHelper::logBusinessEmail((int)$id, 'Your OCSAPP Distribution Account Has Been Removed', 'Your distribution account has been permanently deleted. Contact info@ocsapp.ca for questions.');
            } catch (\Exception $e) {
                error_log('Failed to write deletion activity log: ' . $e->getMessage());
            }

            $_SESSION['admin_success'] = 'Business account deleted successfully.';

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log('Delete business error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to delete business account.';
        }

        redirect('admin/business-accounts');
    }

    /**
     * Admin requests a document from a business account
     */
    public function requestDocument(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/business-accounts'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.'; back(); return;
        }

        $businessId = (int)($_POST['business_id'] ?? 0);
        $docType    = sanitize($_POST['doc_type'] ?? '');
        $message    = sanitize($_POST['message'] ?? '');
        $deadline   = sanitize($_POST['deadline'] ?? '');

        $docLabels = [
            'doc_certificate' => "Certificat d'incorporation / Certificate of Incorporation",
            'doc_declaration' => "Déclaration d'immatriculation",
            'other'           => sanitize($_POST['custom_label'] ?? 'Document requis / Document Required'),
        ];

        if (!$businessId || !array_key_exists($docType, $docLabels)) {
            $_SESSION['admin_error'] = 'Invalid request parameters.';
            back(); return;
        }

        $docLabel = $docLabels[$docType];

        try {
            // Insert request
            $this->db->prepare("
                INSERT INTO document_requests (business_id, doc_type, doc_label, message, deadline, status, requested_by, created_at)
                VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW())
            ")->execute([
                $businessId,
                $docType,
                $docLabel,
                $message ?: null,
                $deadline ?: null,
                $_SESSION['user']['id'] ?? null,
            ]);

            // Business bell notification
            try {
                $deadlineStr = $deadline ? ' — ' . date('M j, Y', strtotime($deadline)) : '';
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId,
                    'document_request',
                    'Document requis / Document Required',
                    'L\'équipe OCSAPP vous demande de soumettre : ' . $docLabel . $deadlineStr . '. / OCSAPP team is requesting: ' . $docLabel . $deadlineStr . '.',
                    '/distribution/documents',
                    'file-upload'
                );
            } catch (\Exception $e) {
                error_log('Document request bell error: ' . $e->getMessage());
            }

            // Admin notification log
            try {
                $stmt = $this->db->prepare("SELECT company_name FROM business_profiles WHERE id = ?");
                $stmt->execute([$businessId]);
                $company = htmlspecialchars($stmt->fetchColumn() ?: "Business #{$businessId}");
                $this->db->prepare("
                    INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                    VALUES ('document_request', ?, ?, ?, 'file-upload', 'normal', NOW())
                ")->execute([
                    "Document demandé - {$company}",
                    "Une demande de document ({$docLabel}) a été envoyée à {$company}.",
                    '/admin/business-accounts/view?id=' . $businessId . '#documentsPanel',
                ]);
            } catch (\Exception $e) {
                error_log('Document request admin notification error: ' . $e->getMessage());
            }

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            \App\Helpers\NotificationHelper::logBusinessActivity($businessId, 'document_requested', "Document requested: '{$docLabel}'" . ($deadline ? " (deadline: {$deadline})" : '') . " by {$adminName}.", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Document request sent and business notified.';
        } catch (\PDOException $e) {
            error_log('Request document error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to send document request.';
        }

        redirect('admin/business-accounts/view?id=' . $businessId);
    }

    /**
     * Verify an individual business document
     */
    public function verifyDocument(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/business-accounts'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.'; back(); return;
        }

        $docId      = (int)($_POST['doc_id'] ?? 0);
        $businessId = (int)($_POST['business_id'] ?? 0);

        try {
            $stmt = $this->db->prepare("
                UPDATE business_documents
                SET status = 'verified', verified_by = ?, verified_at = NOW(), rejection_reason = NULL, updated_at = NOW()
                WHERE id = ? AND business_id = ?
            ");
            $stmt->execute([$_SESSION['user']['id'] ?? null, $docId, $businessId]);

            // Notify business bell
            try {
                $docRow = $this->db->prepare("SELECT doc_label FROM business_documents WHERE id = ?");
                $docRow->execute([$docId]);
                $docLabel = $docRow->fetchColumn() ?: 'Document';
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId,
                    'document_verified',
                    'Document vérifié / Document Verified',
                    htmlspecialchars($docLabel) . ' a été vérifié avec succès. / has been successfully verified.',
                    '/distribution/documents',
                    'check-circle'
                );
            } catch (\Exception $e) { error_log('Business doc verify bell error: ' . $e->getMessage()); }

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            \App\Helpers\NotificationHelper::logBusinessActivity($businessId, 'document_verified', "Document verified: '{$docLabel}' by {$adminName}.", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Document verified successfully.';
        } catch (\PDOException $e) {
            error_log('Verify document error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to verify document.';
        }

        redirect('admin/business-accounts/view?id=' . $businessId);
    }

    /**
     * Reject an individual business document
     */
    public function rejectDocument(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { redirect('admin/business-accounts'); return; }
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.'; back(); return;
        }

        $docId      = (int)($_POST['doc_id'] ?? 0);
        $businessId = (int)($_POST['business_id'] ?? 0);
        $reason     = sanitize($_POST['rejection_reason'] ?? '');

        try {
            $stmt = $this->db->prepare("
                UPDATE business_documents
                SET status = 'rejected', rejection_reason = ?, verified_by = ?, verified_at = NOW(), updated_at = NOW()
                WHERE id = ? AND business_id = ?
            ");
            $stmt->execute([$reason ?: null, $_SESSION['user']['id'] ?? null, $docId, $businessId]);

            // Notify business bell
            try {
                $docRow = $this->db->prepare("SELECT doc_label FROM business_documents WHERE id = ?");
                $docRow->execute([$docId]);
                $docLabel = $docRow->fetchColumn() ?: 'Document';
                $msg = htmlspecialchars($docLabel) . ' a été refusé.' . ($reason ? ' Raison : ' . htmlspecialchars($reason) : '') . ' / has been rejected.' . ($reason ? ' Reason: ' . htmlspecialchars($reason) : '');
                \App\Helpers\NotificationHelper::addBusinessNotification(
                    $businessId,
                    'document_rejected',
                    'Document refusé / Document Rejected',
                    $msg,
                    '/distribution/documents',
                    'times-circle'
                );
            } catch (\Exception $e) { error_log('Business doc reject bell error: ' . $e->getMessage()); }

            // Admin notification to track who rejected what
            try {
                $this->db->prepare("
                    INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                    VALUES ('document_rejected', ?, ?, ?, 'times-circle', 'normal', NOW())
                ")->execute([
                    'Document rejeté - Business #' . $businessId,
                    'Un document a été rejeté pour le compte #' . $businessId . ($reason ? ' - ' . $reason : '') . '.',
                    '/admin/business-accounts/view?id=' . $businessId . '#documentsPanel',
                ]);
            } catch (\Exception $e) { error_log('Admin doc reject notification error: ' . $e->getMessage()); }

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            $reasonNote = $reason ? " Reason: {$reason}" : '';
            \App\Helpers\NotificationHelper::logBusinessActivity($businessId, 'document_rejected', "Document rejected: '{$docLabel}' by {$adminName}.{$reasonNote}", 'admin', $adminName);

            $_SESSION['admin_success'] = 'Document rejected and business notified.';
        } catch (\PDOException $e) {
            error_log('Reject document error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to reject document.';
        }

        redirect('admin/business-accounts/view?id=' . $businessId);
    }

    /**
     * Get statistics for dashboard
     */
    private function getStats(): array
    {
        $stats = [
            'total' => 0,
            'pending' => 0,
            'active' => 0,
            'suspended' => 0,
            'standard' => 0,
            'approved' => 0,
            'premium' => 0
        ];

        try {
            // Total
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles");
            $stats['total'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            // Pending
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE status = 'pending'");
            $stats['pending'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            // Active
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE status = 'active'");
            $stats['active'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            // Suspended
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE status = 'suspended'");
            $stats['suspended'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            // By tier
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE account_tier = 'standard'");
            $stats['standard'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE account_tier = 'approved'");
            $stats['approved'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM business_profiles WHERE account_tier = 'premium'");
            $stats['premium'] = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

        } catch (\PDOException $e) {
            error_log('Stats error: ' . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Extend verification deadline for a pending business account
     */
    public function extendDeadline(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/business-accounts');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid request.';
            back();
            return;
        }

        $id        = (int)($_POST['id'] ?? 0);
        $extraDays = max(1, min(365, (int)($_POST['extra_days'] ?? 15)));

        try {
            // Fetch business + user before update so we have the email
            $infoStmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $infoStmt->execute([$id]);
            $business = $infoStmt->fetch(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("
                UPDATE business_profiles
                SET verification_deadline = DATE_ADD(
                    GREATEST(COALESCE(verification_deadline, NOW()), NOW()),
                    INTERVAL ? DAY
                ), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$extraDays, $id]);

            // Fetch the new deadline to show in email
            $newDeadlineRow = $this->db->prepare("SELECT verification_deadline FROM business_profiles WHERE id = ?");
            $newDeadlineRow->execute([$id]);
            $newDeadline = $newDeadlineRow->fetchColumn();

            // Email the applicant
            if ($business) {
                try {
                    $firstName   = htmlspecialchars($business['first_name'] ?? '');
                    $companyName = htmlspecialchars($business['company_name'] ?? '');
                    $deadlineStr = $newDeadline ? date('F j, Y', strtotime($newDeadline)) : 'N/A';
                    $loginUrl    = url('distribution/login');
                    \App\Helpers\EmailHelper::sendRaw(
                        $business['email'],
                        'Verification Deadline Extended — OCS Marketplace',
                        "
                        <p>Hello {$firstName},</p>
                        <p>Good news — the document verification deadline for your distribution account (<strong>{$companyName}</strong>) has been extended by <strong>{$extraDays} day" . ($extraDays !== 1 ? 's' : '') . "</strong>.</p>
                        <table style='border-collapse:collapse;width:100%;font-size:14px;background:#f9fafb;border-radius:8px;margin:16px 0;'>
                            <tr><td style='padding:10px 16px;color:#666;width:180px;'>New Deadline</td><td style='padding:10px 16px;font-weight:600;color:#00b207;'>{$deadlineStr}</td></tr>
                        </table>
                        <p>Please upload your verification documents before the new deadline to complete your registration.</p>
                        <p><a href='{$loginUrl}' style='background:#00b207;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;'>Upload Documents</a></p>
                        <p>If you have any questions, contact us at <a href='mailto:info@ocsapp.ca'>info@ocsapp.ca</a>.</p>
                        <p>Thank you,<br>OCS Marketplace Team</p>
                        "
                    );
                } catch (\Exception $e) {
                    error_log('Failed to send deadline extension email: ' . $e->getMessage());
                }
            }

            $adminName = trim(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?: 'Admin';
            $deadlineNote = $newDeadline ? ' New deadline: ' . date('Y-m-d', strtotime($newDeadline)) . '.' : '';
            \App\Helpers\NotificationHelper::logBusinessActivity($id, 'deadline_extended', "Verification deadline extended by {$extraDays} day(s) by {$adminName}.{$deadlineNote}", 'admin', $adminName);
            \App\Helpers\NotificationHelper::logBusinessEmail($id, 'Verification Deadline Extended - OCS Marketplace', "Your verification deadline has been extended by {$extraDays} day(s).{$deadlineNote}");

            $_SESSION['admin_success'] = "Verification deadline extended by {$extraDays} days and applicant notified.";

        } catch (\PDOException $e) {
            error_log('Extend business deadline error: ' . $e->getMessage());
            $_SESSION['admin_error'] = 'Failed to extend deadline.';
        }

        $backUrl = sanitize($_POST['back_url'] ?? '');
        redirect($backUrl ?: 'admin/business-accounts/view?id=' . $id);
    }

    /**
     * Generate agreement and onboarding PDFs as temp files for email attachment.
     * Returns array of ['path' => ..., 'name' => ...] entries.
     */
    private function generateApprovalPdfs(): array
    {
        $attachments = [];
        $pdfWrapper  = '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>
            body { font-family: Helvetica, Arial, sans-serif; font-size: 11pt; color: #1a1a1a; margin: 0; padding: 0; }
            h1 { font-size: 16pt; color: #1a5c2a; margin-bottom: 6px; }
            h2 { font-size: 13pt; color: #1a5c2a; margin-top: 18px; }
            h3 { font-size: 11pt; margin-top: 14px; }
            p, li { line-height: 1.6; margin-bottom: 6px; }
            .lang-section { padding: 32px 40px; }
            .lang-label { font-size: 9pt; font-weight: bold; color: #6b7280; text-transform: uppercase;
                          letter-spacing: 1px; margin-bottom: 12px; }
            .page-break { page-break-after: always; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
            td, th { border: 1px solid #d1d5db; padding: 6px 10px; font-size: 10pt; }
            th { background: #f3f4f6; font-weight: 600; }
        </style></head><body>';

        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        // --- Agreement PDF (FR first, EN below) ---
        try {
            $stmt = $this->db->prepare("
                SELECT language, title, content FROM legal_content
                WHERE page_type = 'distribution_agreement' AND is_published = 1
                ORDER BY FIELD(language, 'fr', 'en')
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                $html = $pdfWrapper;
                foreach ($rows as $i => $row) {
                    $langLabel = $row['language'] === 'fr' ? 'Version Française' : 'English Version';
                    $html .= '<div class="lang-section">';
                    $html .= '<div class="lang-label">' . htmlspecialchars($langLabel) . '</div>';
                    $html .= '<h1>' . htmlspecialchars($row['title']) . '</h1>';
                    $html .= $row['content'];
                    $html .= '</div>';
                    if ($i < count($rows) - 1) {
                        $html .= '<div class="page-break"></div>';
                    }
                }
                $html .= '</body></html>';

                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $tmpFile = tempnam(sys_get_temp_dir(), 'ocsapp_agreement_') . '.pdf';
                file_put_contents($tmpFile, $dompdf->output());
                $attachments[] = ['path' => $tmpFile, 'name' => 'Distribution-Service-Agreement.pdf'];
            }
        } catch (\Exception $e) {
            error_log('Agreement PDF generation error: ' . $e->getMessage());
        }

        // --- Onboarding Package PDF ---
        try {
            $stmt = $this->db->prepare("
                SELECT content FROM planner_templates
                WHERE slug = 'business-onboarding-package' AND is_active = 1
                LIMIT 1
            ");
            $stmt->execute();
            $content = $stmt->fetchColumn();

            if ($content) {
                $html  = $pdfWrapper;
                $html .= '<div class="lang-section">' . $content . '</div>';
                $html .= '</body></html>';

                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $tmpFile = tempnam(sys_get_temp_dir(), 'ocsapp_onboarding_') . '.pdf';
                file_put_contents($tmpFile, $dompdf->output());
                $attachments[] = ['path' => $tmpFile, 'name' => 'OCSAPP-Business-Onboarding-Package.pdf'];
            }
        } catch (\Exception $e) {
            error_log('Onboarding PDF generation error: ' . $e->getMessage());
        }

        return $attachments;
    }
}
