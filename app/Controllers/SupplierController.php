<?php

namespace App\Controllers;

use App\Helpers\EmailHelper;
use App\Middlewares\AuthMiddleware;

class SupplierController {

    public function index(): void {
        AuthMiddleware::handle('admin');

        try {
            $db = \Database::getConnection();
            $search  = get('search', '');
            $status  = get('status', '');
            $package = get('package', '');

            $where = ['1=1'];
            $params = [];

            // Filter soft-deleted: show archived only when explicitly filtered
            if ($status === 'archived') {
                $where[] = "s.deleted_at IS NOT NULL";
                $status = ''; // clear so it doesn't get used in status filter below
            } else {
                $where[] = "(s.deleted_at IS NULL)";
            }

            if ($search) {
                $where[] = "(s.name LIKE ? OR s.company_name LIKE ? OR s.email LIKE ?)";
                $searchTerm = "%{$search}%";
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }

            if ($status) {
                $where[] = "s.status = ?";
                $params[] = $status;
            }

            $validPkgs = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
            if (in_array($package, $validPkgs)) {
                $where[] = "s.subscription_package = ?";
                $params[] = $package;
            }

            $whereClause = implode(' AND ', $where);

            // Count total suppliers
            $countStmt = $db->prepare("SELECT COUNT(*) FROM suppliers s WHERE {$whereClause}");
            $countStmt->execute($params);
            $total = (int)$countStmt->fetchColumn();

            $perPage = 20;
            $page = max(1, (int)get('page', 1));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, max(1, $totalPages));
            $offset = ($page - 1) * $perPage;

            // Join supplier_applications to get lead_id for review links
            $stmt = $db->prepare("
                SELECT s.*, sa.lead_id
                FROM suppliers s
                LEFT JOIN supplier_applications sa ON sa.supplier_id = s.id
                WHERE {$whereClause}
                ORDER BY s.name ASC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($params);
            $suppliers = $stmt->fetchAll();

            // Fetch invitations — join suppliers to detect orphaned accepted rows
            $stmt = $db->prepare("
                SELECT si.*, u.first_name as invited_by_name,
                       s.id as supplier_exists
                FROM supplier_invites si
                LEFT JOIN users u ON si.invited_by = u.id
                LEFT JOIN suppliers s ON si.supplier_id = s.id
                ORDER BY si.created_at DESC
            ");
            $stmt->execute();
            $invites = $stmt->fetchAll();

            view('admin.suppliers.index', [
                'suppliers' => $suppliers,
                'invites' => $invites,
                'search' => $search,
                'status' => $status,
                'package' => $package,
                'page' => $page,
                'perPage' => $perPage,
                'total' => $total,
                'currentPage' => 'suppliers',
            ]);
        } catch (\PDOException $e) {
            logger("Suppliers list error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading suppliers');
            back();
        }
    }

    public function create(): void {
        AuthMiddleware::handle('admin');
        view('admin.suppliers.create', ['currentPage' => 'suppliers']);
    }

    public function store(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();

            // Generate random password for supplier
            $randomPassword = $this->generateRandomPassword(12);
            $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);

            // Generate unique supplier code
            $supplierCode = 'SUP-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

            $stmt = $db->prepare("
                INSERT INTO suppliers (supplier_code, name, company_name, email, phone, address, city, province,
                    postal_code, country, contact_person, payment_terms, tax_number, notes, status,
                    password_hash, can_login)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ");

            $stmt->execute([
                $supplierCode,
                post('name'), post('company_name'), post('email'), post('phone'),
                post('address'), post('city'), post('province'), post('postal_code'),
                post('country', 'Canada'), post('contact_person'), post('payment_terms', 'Net 30'),
                post('tax_number'), post('notes'), post('status', 'active'),
                $passwordHash
            ]);

            // Send login credentials via email
            $this->sendCredentialsEmail(post('email'), post('name'), $randomPassword);

            setFlash('success', 'Supplier added successfully. Login credentials have been sent via email.');
            redirect(url('admin/suppliers'));
        } catch (\PDOException $e) {
            logger("Supplier create error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error creating supplier');
            back();
        }
    }

    /**
     * Generate a random secure password
     */
    private function generateRandomPassword(int $length = 12): string {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*';
        $password = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $maxIndex)];
        }

        return $password;
    }

    /**
     * Send login credentials to supplier via email
     */
    private function sendCredentialsEmail(string $email, string $name, string $password): void {
        try {
            require_once __DIR__ . '/../Helpers/EmailHelper.php';

            $loginUrl = url('supplier/login');

            $subject = 'Your OCSAPP Supplier Portal Login Credentials';
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #00b207 0%, #008505 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>Welcome to OCSAPP Supplier Portal</h1>
                    </div>

                    <div style='padding: 30px; background: #f9f9f9;'>
                        <p style='font-size: 16px; color: #333;'>Hello {$name},</p>

                        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                            Your supplier account has been created successfully. Below are your login credentials to access the OCSAPP Supplier Portal.
                        </p>

                        <div style='background: white; border-left: 4px solid #00b207; padding: 20px; margin: 20px 0;'>
                            <p style='margin: 0 0 10px; color: #666;'><strong>Login URL:</strong></p>
                            <p style='margin: 0 0 20px;'><a href='{$loginUrl}' style='color: #00b207; text-decoration: none;'>{$loginUrl}</a></p>

                            <p style='margin: 0 0 10px; color: #666;'><strong>Email:</strong></p>
                            <p style='margin: 0 0 20px; font-family: monospace; background: #f5f5f5; padding: 8px; border-radius: 4px;'>{$email}</p>

                            <p style='margin: 0 0 10px; color: #666;'><strong>Temporary Password:</strong></p>
                            <p style='margin: 0; font-family: monospace; background: #f5f5f5; padding: 8px; border-radius: 4px; font-size: 18px; font-weight: bold; color: #00b207;'>{$password}</p>
                        </div>

                        <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;'>
                            <p style='margin: 0; color: #856404;'><strong>⚠️ Security Notice:</strong> For your security, please change this password after your first login.</p>
                        </div>

                        <p style='font-size: 16px; color: #333; line-height: 1.6;'>
                            Once logged in, you can:
                        </p>
                        <ul style='font-size: 16px; color: #333; line-height: 1.8;'>
                            <li>Manage your product catalog</li>
                            <li>View and manage purchase orders</li>
                            <li>Track order history and analytics</li>
                            <li>Update your profile and settings</li>
                            <li>Change your password</li>
                        </ul>

                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$loginUrl}' style='display: inline-block; background: linear-gradient(135deg, #00b207 0%, #008505 100%); color: white; padding: 15px 40px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;'>Login to Supplier Portal</a>
                        </div>

                        <p style='font-size: 14px; color: #666; margin-top: 30px;'>
                            If you have any questions or need assistance, please contact our support team.
                        </p>

                        <p style='font-size: 14px; color: #666;'>
                            Best regards,<br>
                            <strong>OCSAPP Team</strong>
                        </p>
                    </div>

                    <div style='background: #333; color: #999; padding: 20px; text-align: center; font-size: 12px;'>
                        <p style='margin: 0;'>© " . date('Y') . " OCSAPP. All rights reserved.</p>
                        <p style='margin: 5px 0 0;'>This is an automated message. Please do not reply to this email.</p>
                    </div>
                </div>
            ";

            \App\Helpers\EmailHelper::send($email, $subject, $body);
            logger("Supplier credentials sent to {$email}", 'info');

        } catch (\Exception $e) {
            logger("Failed to send supplier credentials email: " . $e->getMessage(), 'error');
            // Don't throw - we still want the supplier to be created even if email fails
        }
    }

    public function edit(): void {
        AuthMiddleware::handle('admin');

        try {
            $id = (int) get('id');
            if (!$id) {
                setFlash('error', 'Invalid supplier ID');
                redirect(url('admin/suppliers'));
            }

            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT s.*, sa.lead_id
                FROM suppliers s
                LEFT JOIN supplier_applications sa ON sa.supplier_id = s.id
                WHERE s.id = ?
            ");
            $stmt->execute([$id]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                setFlash('error', 'Supplier not found');
                redirect(url('admin/suppliers'));
            }

            // Fetch audit log
            $auditLog = [];
            try {
                $auditStmt = $db->prepare("
                    SELECT sal.*, COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'System') as performer_name
                    FROM supplier_audit_log sal
                    LEFT JOIN users u ON sal.performed_by = u.id
                    WHERE sal.supplier_id = ?
                    ORDER BY sal.created_at DESC
                    LIMIT 25
                ");
                $auditStmt->execute([$id]);
                $auditLog = $auditStmt->fetchAll();
            } catch (\Exception $e) {
                // Table may not exist yet — ignore
            }

            view('admin.suppliers.edit', [
                'supplier' => $supplier,
                'auditLog' => $auditLog,
                'currentPage' => 'suppliers',
            ]);
        } catch (\PDOException $e) {
            logger("Supplier edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading supplier');
            redirect(url('admin/suppliers'));
        }
    }

    public function update(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                setFlash('error', 'Invalid supplier ID');
                back();
            }

            $db = \Database::getConnection();

            // Validate phone
            $phone = post('phone', '');
            if ($phone) {
                $phoneDigits = preg_replace('/\D/', '', $phone);
                if (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 11) {
                    setFlash('error', 'Phone number must be 10-11 digits');
                    back();
                    return;
                }
            }

            // Validate postal code
            $postalCode = post('postal_code', '');
            if ($postalCode && !preg_match('/^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/', $postalCode)) {
                setFlash('error', 'Invalid Canadian postal code format (e.g. A1B 2C3)');
                back();
                return;
            }

            // Handle password change
            $password = post('password', '');
            $passwordConfirmation = post('password_confirmation', '');
            $passwordSql = '';
            $passwordParams = [];

            if (!empty($password)) {
                if (strlen($password) < 8) {
                    setFlash('error', 'Password must be at least 8 characters');
                    back();
                    return;
                }
                if ($password !== $passwordConfirmation) {
                    setFlash('error', 'Passwords do not match');
                    back();
                    return;
                }
                $passwordSql = ', password_hash = ?, password_changed_at = NOW()';
                $passwordParams = [password_hash($password, PASSWORD_DEFAULT)];
            }

            $validPkgs = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
            $pkg = post('subscription_package', 'Essential');
            if (!in_array($pkg, $validPkgs)) $pkg = 'Essential';
            $commissionMap = ['Essential' => 12.00, 'Experience' => 10.00, 'Prestige' => 8.00, 'Enterprise' => 6.00];
            $commissionRate = (float) post('commission_rate', $commissionMap[$pkg]);

            $stmt = $db->prepare("
                UPDATE suppliers SET name = ?, company_name = ?, email = ?, phone = ?,
                    address = ?, city = ?, province = ?, postal_code = ?, country = ?,
                    contact_person = ?, payment_terms = ?, tax_number = ?, notes = ?, status = ?,
                    subscription_package = ?, commission_rate = ?
                    {$passwordSql}
                WHERE id = ?
            ");

            $params = [
                post('name'), post('company_name'), post('email'), post('phone'),
                post('address'), post('city'), post('province'), post('postal_code'),
                post('country', 'Canada'), post('contact_person'), post('payment_terms', 'Net 30'),
                post('tax_number'), post('notes'), post('status', 'active'),
                $pkg, $commissionRate,
                ...$passwordParams, $id
            ];
            $stmt->execute($params);

            $changes = 'Updated supplier profile';
            if (!empty($password)) {
                $changes .= ', password changed';
            }
            supplierAuditLog($id, 'updated', $changes);

            $msg = 'Supplier updated successfully';
            if (!empty($password)) {
                $msg .= ' (password changed)';
            }
            setFlash('success', $msg);
            redirect(url('admin/suppliers'));
        } catch (\PDOException $e) {
            logger("Supplier update error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating supplier');
            back();
        }
    }

    public function updatePackage(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $id = (int) post('supplier_id');
        if (!$id) {
            jsonResponse(['success' => false, 'message' => 'Invalid supplier ID'], 400);
            return;
        }

        $validPackages = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
        $pkg = post('subscription_package', '');
        if (!in_array($pkg, $validPackages)) {
            jsonResponse(['success' => false, 'message' => 'Invalid package'], 422);
            return;
        }

        $commissionMap = ['Essential' => 12.00, 'Experience' => 10.00, 'Prestige' => 8.00, 'Enterprise' => 6.00];
        $commissionRate = $commissionMap[$pkg];

        try {
            $db = \Database::getConnection();
            $db->prepare("UPDATE suppliers SET subscription_package = ?, commission_rate = ? WHERE id = ?")
               ->execute([$pkg, $commissionRate, $id]);

            supplierAuditLog($id, 'package_updated', "Package changed to {$pkg} (commission {$commissionRate}%)", userId());

            jsonResponse(['success' => true, 'message' => "Package updated to {$pkg}", 'commission_rate' => $commissionRate]);
        } catch (\PDOException $e) {
            logger("Supplier package update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating package'], 500);
        }
    }

    public function delete(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid supplier ID'], 400);
            }

            $db = \Database::getConnection();

            // Check if supplier has purchase orders
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM purchase_orders WHERE supplier_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetch()['count'] ?? 0;

            if ($count > 0) {
                // Soft delete — supplier has PO history
                $stmt = $db->prepare("UPDATE suppliers SET deleted_at = NOW(), status = 'inactive', can_login = 0 WHERE id = ?");
                $stmt->execute([$id]);
                supplierAuditLog($id, 'soft_deleted', 'Supplier archived (has purchase orders)');
                jsonResponse(['success' => true, 'message' => 'Supplier archived successfully (has purchase order history)']);
                return;
            }

            // Hard delete only if no purchase orders
            $emailRow = $db->prepare("SELECT email FROM suppliers WHERE id = ?");
            $emailRow->execute([$id]);
            $supplierEmail = $emailRow->fetchColumn();

            $db->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$id]);

            // Clean up application record so the email can be re-invited
            if ($supplierEmail) {
                $db->prepare("DELETE FROM supplier_applications WHERE email = ?")->execute([$supplierEmail]);
                $db->prepare("DELETE FROM supplier_invites WHERE supplier_id = ?")->execute([$id]);
            }

            jsonResponse(['success' => true, 'message' => 'Supplier deleted successfully']);
        } catch (\PDOException $e) {
            logger("Supplier delete error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error deleting supplier'], 500);
        }
    }

    public function restore(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid supplier ID'], 400);
                return;
            }

            $db = \Database::getConnection();
            $stmt = $db->prepare("UPDATE suppliers SET deleted_at = NULL, status = 'inactive', can_login = 0 WHERE id = ?");
            $stmt->execute([$id]);

            supplierAuditLog($id, 'restored', 'Supplier restored from archive');

            jsonResponse(['success' => true, 'message' => 'Supplier restored successfully. Set status to Active to re-enable login.']);
        } catch (\PDOException $e) {
            logger("Supplier restore error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error restoring supplier'], 500);
        }
    }

    public function performance(): void {
        AuthMiddleware::handle('admin');

        try {
            $db = \Database::getConnection();

            // Aggregate totals at the top
            $totalsStmt = $db->query("
                SELECT
                    COUNT(DISTINCT s.id) as total_suppliers,
                    COUNT(po.id) as total_orders,
                    COALESCE(SUM(po.total_amount), 0) as total_spend,
                    COALESCE(AVG(po.total_amount), 0) as avg_order_value
                FROM suppliers s
                LEFT JOIN purchase_orders po ON po.supplier_id = s.id AND po.status != 'draft'
                WHERE s.deleted_at IS NULL AND s.status = 'active'
            ");
            $totals = $totalsStmt->fetch(\PDO::FETCH_ASSOC);

            // Per-supplier performance metrics
            $sort = get('sort', 'total_spend');
            $dir  = get('dir', 'desc') === 'asc' ? 'ASC' : 'DESC';
            $allowedSorts = ['total_orders', 'accepted', 'acceptance_rate', 'total_spend', 'avg_order_value', 'declined'];
            if (!in_array($sort, $allowedSorts)) $sort = 'total_spend';

            $stmt = $db->query("
                SELECT
                    s.id,
                    s.name,
                    s.company_name,
                    s.email,
                    s.status,
                    COUNT(po.id) as total_orders,
                    SUM(CASE WHEN po.status IN ('receiving','completed') THEN 1 ELSE 0 END) as accepted,
                    SUM(CASE WHEN po.status = 'cancelled' THEN 1 ELSE 0 END) as declined,
                    ROUND(
                        CASE WHEN COUNT(po.id) > 0
                            THEN SUM(CASE WHEN po.status IN ('receiving','completed') THEN 1 ELSE 0 END) * 100.0 / COUNT(po.id)
                            ELSE 0 END, 1
                    ) as acceptance_rate,
                    COALESCE(SUM(po.total_amount), 0) as total_spend,
                    COALESCE(AVG(po.total_amount), 0) as avg_order_value,
                    s.created_at
                FROM suppliers s
                LEFT JOIN purchase_orders po ON po.supplier_id = s.id AND po.status != 'draft'
                WHERE s.deleted_at IS NULL
                GROUP BY s.id, s.name, s.company_name, s.email, s.status, s.created_at
                ORDER BY {$sort} {$dir}
            ");
            $suppliers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin.suppliers.performance', [
                'suppliers' => $suppliers,
                'totals' => $totals,
                'sort' => $sort,
                'dir' => get('dir', 'desc'),
                'currentPage' => 'suppliers',
            ]);

        } catch (\PDOException $e) {
            logger("Supplier performance error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading performance data');
            redirect(url('admin/suppliers'));
        }
    }

    public function sendInvite(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $email = post('email', '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                jsonResponse(['success' => false, 'message' => 'Invalid email address'], 400);
            }

            $db = \Database::getConnection();

            // Check if supplier with this email already exists
            $stmt = $db->prepare("SELECT id FROM suppliers WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'Supplier with this email already exists'], 400);
            }

            // Check if there's already an active invite
            $stmt = $db->prepare("
                SELECT id FROM supplier_invites
                WHERE email = ?
                AND status = 'pending'
                AND expires_at > NOW()
            ");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                jsonResponse(['success' => false, 'message' => 'An active invitation already exists for this email'], 400);
            }

            // Generate invite token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
            $invitedBy = $_SESSION['user']['id'] ?? 1;

            // Insert invite
            $stmt = $db->prepare("
                INSERT INTO supplier_invites (email, company_name, token, invited_by, expires_at, status)
                VALUES (?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$email, 'Pending Registration', $token, $invitedBy, $expiresAt]);

            // Generate invite URL
            $inviteUrl = url('supplier/accept-invite?token=' . $token);

            // Send email with invite URL
            $emailSent = $this->sendSupplierInviteEmail($email, $inviteUrl);

            logger("Supplier invite sent to {$email} (email: " . ($emailSent ? 'sent' : 'failed') . ")", 'info');

            jsonResponse([
                'success' => true,
                'message' => $emailSent ? 'Invitation email sent successfully' : 'Invitation created (check email settings)',
                'invite_url' => $inviteUrl
            ]);

        } catch (\PDOException $e) {
            logger("Supplier invite error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error sending invitation'], 500);
        }
    }

    public function resetPassword(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid supplier ID'], 400);
            }

            $newPassword = post('password', '');
            if (strlen($newPassword) < 8) {
                jsonResponse(['success' => false, 'message' => 'Password must be at least 8 characters'], 400);
            }

            $db = \Database::getConnection();
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $db->prepare("UPDATE suppliers SET password_hash = ? WHERE id = ?");
            $stmt->execute([$passwordHash, $id]);

            logger("Supplier password reset for ID: {$id}", 'info');
            jsonResponse(['success' => true, 'message' => 'Password reset successfully']);

        } catch (\PDOException $e) {
            logger("Supplier password reset error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error resetting password'], 500);
        }
    }

    /**
     * Send supplier invite email (bilingual FR/EN)
     */
    private function sendSupplierInviteEmail(string $email, string $inviteUrl): bool
    {
        $subject = "Invitation fournisseur OCSAPP / OCSAPP Supplier Invitation";

        $templatePath = BASE_PATH . '/app/Views/emails/supplier-invite.php';
        ob_start();
        extract(['inviteUrl' => $inviteUrl]);
        include $templatePath;
        $body = ob_get_clean();

        return EmailHelper::send($email, $subject, $body);
    }

    /**
     * Resend invitation email (works for pending, cancelled, or expired invites)
     */
    public function resendInvite(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid invite ID'], 400);
            }

            $db = \Database::getConnection();

            // Get invite details (pending or cancelled - not accepted)
            $stmt = $db->prepare("SELECT * FROM supplier_invites WHERE id = ? AND status != 'accepted'");
            $stmt->execute([$id]);
            $invite = $stmt->fetch();

            if (!$invite) {
                jsonResponse(['success' => false, 'message' => 'Invite not found or already accepted'], 404);
            }

            // Reset status to pending and extend expiry
            $newExpiry = date('Y-m-d H:i:s', strtotime('+7 days'));
            $stmt = $db->prepare("UPDATE supplier_invites SET status = 'pending', expires_at = ? WHERE id = ?");
            $stmt->execute([$newExpiry, $id]);

            // Resend the email
            $inviteUrl = url('supplier/accept-invite?token=' . $invite['token']);
            $emailSent = $this->sendSupplierInviteEmail($invite['email'], $inviteUrl);

            logger("Supplier invite resent to {$invite['email']} (status reset to pending)", 'info');

            jsonResponse([
                'success' => true,
                'message' => $emailSent ? 'Invitation email resent successfully' : 'Invitation reactivated (check email settings)'
            ]);

        } catch (\PDOException $e) {
            logger("Resend invite error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error resending invitation'], 500);
        }
    }

    /**
     * Cancel invitation
     */
    public function cancelInvite(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid invite ID'], 400);
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("UPDATE supplier_invites SET status = 'cancelled' WHERE id = ? AND status = 'pending'");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                jsonResponse(['success' => false, 'message' => 'Invite not found or already processed'], 404);
            }

            logger("Supplier invite cancelled: ID {$id}", 'info');
            jsonResponse(['success' => true, 'message' => 'Invitation cancelled']);

        } catch (\PDOException $e) {
            logger("Cancel invite error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error cancelling invitation'], 500);
        }
    }

    /**
     * Delete invitation record
     */
    public function deleteInvite(): void {
        AuthMiddleware::handle('admin');

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $id = (int) post('id');
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Invalid invite ID'], 400);
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("DELETE FROM supplier_invites WHERE id = ?");
            $stmt->execute([$id]);

            if ($stmt->rowCount() === 0) {
                jsonResponse(['success' => false, 'message' => 'Invite not found'], 404);
            }

            logger("Supplier invite deleted: ID {$id}", 'info');
            jsonResponse(['success' => true, 'message' => 'Invitation deleted']);

        } catch (\PDOException $e) {
            logger("Delete invite error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error deleting invitation'], 500);
        }
    }
}
