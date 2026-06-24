<?php

namespace App\Controllers;

class SupplierAuthController {

    public function landing(): void {
        // Public landing page for suppliers
        view('supplier.landing', [
            'pageTitle' => 'Supplier Portal - Partner with OCSAPP'
        ]);
    }

    /**
     * Show the supplier application form
     */
    public function apply(): void {
        // Check if there's a flash message
        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        // Check for success redirect
        $success = isset($_GET['success']) && $_GET['success'] === '1';

        view('supplier.apply', [
            'pageTitle' => 'Become a Supplier - OCSAPP',
            'flash' => $flash,
            'success' => $success,
            'old' => $_SESSION['_old_input'] ?? []
        ]);

        // Clear old input
        unset($_SESSION['_old_input']);
    }

    /**
     * Process supplier application form submission
     */
    public function submitApplication(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        // Collect and sanitize form data
        $validPackages = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
        $rawPackage = trim(post('subscription_package', 'Essential'));
        $package = in_array($rawPackage, $validPackages) ? $rawPackage : 'Essential';

        $data = [
            'first_name' => trim(post('first_name', '')),
            'last_name' => trim(post('last_name', '')),
            'email' => trim(post('email', '')),
            'phone' => trim(post('phone', '')),
            'business_name' => trim(post('business_name', '')),
            'neq_number' => trim(post('neq_number', '')),
            'legal_name' => trim(post('legal_name', '')),
            'operating_names' => trim(post('operating_names', '')),
            'registered_address_street' => trim(post('registered_address_street', '')),
            'registered_address_city' => trim(post('registered_address_city', '')),
            'registered_address_province' => trim(post('registered_address_province', 'Quebec')),
            'registered_address_postal' => trim(post('registered_address_postal', '')),
            'subscription_package' => $package,
        ];

        // Store old input for repopulation
        $_SESSION['_old_input'] = $data;

        // Validate required fields
        $required = ['first_name', 'last_name', 'email', 'phone', 'business_name', 'neq_number', 'legal_name', 'registered_address_street', 'registered_address_city', 'registered_address_postal'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                setFlash('error', 'Please fill in all required fields.');
                back();
                return;
            }
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            back();
            return;
        }

        // Validate NEQ (10 digits)
        if (!preg_match('/^[0-9]{10}$/', $data['neq_number'])) {
            setFlash('error', 'NEQ must be exactly 10 digits.');
            back();
            return;
        }

        // Validate password — same strength requirements as main portal
        $password = post('password', '');
        $passwordConfirmation = post('password_confirmation', '');
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', 'Password must contain: ' . implode(', ', $pwErrors) . '.');
            back();
            return;
        }
        if ($password !== $passwordConfirmation) {
            setFlash('error', 'Passwords do not match.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            // Check for duplicate application by email
            $stmt = $db->prepare("SELECT id FROM supplier_applications WHERE email = ? AND status IN ('pending', 'under_review')");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                setFlash('error', 'An application with this email is already under review.');
                back();
                return;
            }

            // Check if a supplier account with this email already exists
            $stmt = $db->prepare("SELECT id, status FROM suppliers WHERE email = ?");
            $stmt->execute([$data['email']]);
            $existingSupplier = $stmt->fetch();
            if ($existingSupplier) {
                setFlash('error', 'An account with this email already exists. Please <a href="' . url('supplier/login') . '">log in</a> instead.');
                back();
                return;
            }

            // Handle document uploads
            $uploadDir = 'uploads/supplier-applications';
            $fullUploadDir = BASE_PATH . '/public/' . $uploadDir;
            if (!is_dir($fullUploadDir)) {
                mkdir($fullUploadDir, 0755, true);
            }

            $docFields = [
                'doc_certificate_incorporation' => 'doc_certificate_incorporation',
                'doc_declaration_registration' => 'doc_declaration_registration',
                'doc_enterprise_register' => 'doc_enterprise_register',
            ];

            $docPaths = [];
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            foreach ($docFields as $dbCol => $fieldName) {
                $docPaths[$dbCol] = null;

                if (!empty($_FILES[$fieldName]['tmp_name']) && is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
                    $file = $_FILES[$fieldName];

                    // Validate size
                    if ($file['size'] > $maxSize) {
                        setFlash('error', 'Document file size must be less than 5MB.');
                        back();
                        return;
                    }

                    // Validate extension
                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts)) {
                        setFlash('error', 'Only PDF, JPG, and PNG files are allowed for documents.');
                        back();
                        return;
                    }

                    // Reject double extensions
                    $filename = basename($file['name']);
                    if (preg_match('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd)/i', pathinfo($filename, PATHINFO_FILENAME))) {
                        logger("Suspicious supplier doc upload blocked: {$filename}", 'error');
                        setFlash('error', 'Invalid file detected.');
                        back();
                        return;
                    }

                    // Validate MIME type
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (!in_array($mimeType, $allowedMimes, true)) {
                        setFlash('error', 'Invalid file type detected.');
                        back();
                        return;
                    }

                    // Generate safe filename and move
                    $safeFilename = 'supapp_' . uniqid('', true) . '_' . time() . '.' . $ext;
                    $destPath = $fullUploadDir . '/' . $safeFilename;

                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        chmod($destPath, 0644);
                        $docPaths[$dbCol] = $uploadDir . '/' . $safeFilename;
                    }
                }
            }

            $db->beginTransaction();

            // Insert supplier application
            $stmt = $db->prepare("
                INSERT INTO supplier_applications (
                    first_name, last_name, email, phone, business_name,
                    neq_number, legal_name, operating_names,
                    registered_address_street, registered_address_city, registered_address_province, registered_address_postal,
                    doc_certificate_incorporation, doc_declaration_registration, doc_enterprise_register,
                    subscription_package, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['business_name'],
                $data['neq_number'], $data['legal_name'], $data['operating_names'],
                $data['registered_address_street'], $data['registered_address_city'], $data['registered_address_province'], $data['registered_address_postal'],
                $docPaths['doc_certificate_incorporation'], $docPaths['doc_declaration_registration'], $docPaths['doc_enterprise_register'],
                $data['subscription_package'],
            ]);
            $applicationId = $db->lastInsertId();

            // Create CRM lead
            $fullAddress = $data['registered_address_street'] . ', ' . $data['registered_address_city'] . ', ' . $data['registered_address_province'] . ' ' . $data['registered_address_postal'];
            $interestDetails = "NEQ: {$data['neq_number']}\nLegal Name: {$data['legal_name']}";
            if (!empty($data['operating_names'])) {
                $interestDetails .= "\nOperating Names: {$data['operating_names']}";
            }
            $interestDetails .= "\nRegistered Address: {$fullAddress}";
            $interestDetails .= "\nApplication ID: #{$applicationId}";

            // Count uploaded docs
            $docCount = count(array_filter($docPaths));
            $interestDetails .= "\nDocuments uploaded: {$docCount}/3";

            $leadStmt = $db->prepare("
                INSERT INTO leads (
                    first_name, last_name, email, phone, company_name,
                    source, source_details, status, priority,
                    interest_type, interest_details,
                    city, province, country,
                    notes
                ) VALUES (?, ?, ?, ?, ?, 'website', 'Supplier Application Form', 'new', 'medium', 'supplier', ?, ?, ?, 'Canada', ?)
            ");
            $leadStmt->execute([
                $data['first_name'], $data['last_name'], $data['email'], $data['phone'], $data['business_name'],
                $interestDetails,
                $data['registered_address_city'], $data['registered_address_province'],
                "Supplier application submitted via website.\nBusiness: {$data['business_name']}\nNEQ: {$data['neq_number']}"
            ]);
            $leadId = $db->lastInsertId();

            // Link lead to application
            $db->prepare("UPDATE supplier_applications SET lead_id = ? WHERE id = ?")->execute([$leadId, $applicationId]);

            // Log lead activity
            $db->prepare("
                INSERT INTO lead_activities (lead_id, activity_type, description, created_by)
                VALUES (?, 'note', ?, NULL)
            ")->execute([$leadId, "Supplier application #{$applicationId} submitted via website. NEQ: {$data['neq_number']}, Business: {$data['business_name']}"]);

            // Create supplier account with pending_verification status
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $supplierCode = 'SUP-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $supplierStmt = $db->prepare("
                INSERT INTO suppliers (
                    name, supplier_code, company_name, contact_person, email, phone,
                    address, city, province, postal_code, country,
                    tax_number, password_hash, can_login, status, password_changed_at,
                    verification_deadline, subscription_package
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, ?, 1, 'unverified', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), ?)
            ");
            $contactPerson = trim($data['first_name'] . ' ' . $data['last_name']);
            $supplierStmt->execute([
                $data['business_name'],
                $supplierCode,
                $data['business_name'],
                $contactPerson,
                $data['email'],
                $data['phone'],
                $data['registered_address_street'],
                $data['registered_address_city'],
                $data['registered_address_province'],
                $data['registered_address_postal'],
                $data['neq_number'],
                $passwordHash,
                $data['subscription_package'],
            ]);
            $supplierId = $db->lastInsertId();

            // Link supplier account to application
            $db->prepare("UPDATE supplier_applications SET supplier_id = ? WHERE id = ?")->execute([$supplierId, $applicationId]);

            $db->commit();

            // Clear old input on success
            unset($_SESSION['_old_input']);

            // Generate email verification code
            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $db->prepare("
                UPDATE suppliers SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?
                WHERE id = ?
            ")->execute([$verificationCode, $verificationExpires, $supplierId]);

            // Store pending verification state (admin emails sent after verification)
            $_SESSION['pending_supplier_verification'] = [
                'supplier_id'    => $supplierId,
                'application_id' => $applicationId,
                'lead_id'        => $leadId,
                'email'          => $data['email'],
                'first_name'     => $data['first_name'],
                'last_name'      => $data['last_name'],
                'business_name'  => $data['business_name'],
                'neq_number'     => $data['neq_number'],
            ];
            $_SESSION['supplier_verification_attempts'] = 0;

            // Lock language for the rest of the onboarding flow
            $submittedLang = post('lang');
            if (in_array($submittedLang, ['en', 'fr'])) {
                $_SESSION['language'] = $submittedLang;
            } elseif (!isset($_SESSION['language'])) {
                $_SESSION['language'] = 'fr';
            }

            // Send verification code email
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendSupplierVerificationCode([
                    'first_name'        => $data['first_name'],
                    'email'             => $data['email'],
                    'verification_code' => $verificationCode,
                    'verify_url'        => $appUrl . '/supplier/verify-email?email=' . urlencode($data['email']) . '&lang=fr',
                    'verify_url_en'     => $appUrl . '/supplier/verify-email?email=' . urlencode($data['email']) . '&lang=en',
                    'magic_link_url_fr' => $appUrl . '/supplier/verify-email/auto?sid=' . $supplierId . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/supplier/verify-email/auto?sid=' . $supplierId . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
                logger("Supplier verification code sent to {$data['email']}", 'info');
            } catch (\Exception $e) {
                logger("Failed to send supplier verification code: " . $e->getMessage(), 'warning');
            }

            redirect(url('supplier/verify-email'));

        } catch (\PDOException $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Supplier application error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred while submitting your application. Please try again.');
            back();
        }
    }

    /**
     * Send email notification to admin about new supplier application
     */
    private function sendAdminApplicationEmail(array $data, int $applicationId, int $leadId): void {
        $name = $data['first_name'] . ' ' . $data['last_name'];
        $leadUrl = url('admin/leads/view?id=' . $leadId);

        $subject = "New Supplier Application - {$data['business_name']}";
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #00b207 0%, #008505 100%); padding: 24px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 22px;'>New Supplier Application</h1>
                </div>
                <div style='padding: 24px; background: #f9f9f9;'>
                    <p style='font-size: 15px; color: #333;'>A new supplier application has been submitted and requires review.</p>

                    <div style='background: white; border-left: 4px solid #00b207; padding: 16px; margin: 16px 0;'>
                        <h3 style='margin: 0 0 12px; color: #1a1a1a;'>Business Owner</h3>
                        <p style='margin: 4px 0; color: #555;'><strong>Name:</strong> {$name}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Email:</strong> {$data['email']}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Phone:</strong> {$data['phone']}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Business:</strong> {$data['business_name']}</p>
                    </div>

                    <div style='background: white; border-left: 4px solid #3b82f6; padding: 16px; margin: 16px 0;'>
                        <h3 style='margin: 0 0 12px; color: #1a1a1a;'>Quebec Legal Identity</h3>
                        <p style='margin: 4px 0; color: #555;'><strong>NEQ:</strong> {$data['neq_number']}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Legal Name:</strong> {$data['legal_name']}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Operating Names:</strong> " . ($data['operating_names'] ?: 'N/A') . "</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Registered Address:</strong> {$data['registered_address_street']}, {$data['registered_address_city']}, {$data['registered_address_province']} {$data['registered_address_postal']}</p>
                    </div>

                    <p style='font-size: 13px; color: #888; margin-top: 16px;'>Application ID: #{$applicationId} | Lead ID: #{$leadId}</p>

                    <div style='text-align: center; margin: 24px 0;'>
                        <a href='{$leadUrl}' style='display: inline-block; background: #00b207; color: white; padding: 12px 32px; text-decoration: none; border-radius: 8px; font-weight: bold;'>View Lead in CRM</a>
                    </div>
                </div>
                <div style='background: #333; color: #999; padding: 16px; text-align: center; font-size: 12px;'>
                    <p style='margin: 0;'>&copy; " . date('Y') . " OCSAPP. Automated notification.</p>
                </div>
            </div>
        ";

        \App\Helpers\EmailHelper::send('info@ocsapp.ca', $subject, $body);
    }

    /**
     * Send confirmation email to supplier applicant
     */
    private function sendApplicantConfirmationEmail(array $data, int $applicationId): void {
        // Fetch supplier code generated during account creation
        $supplierCode = '';
        try {
            if (!empty($data['supplier_id'])) {
                $stmt = \Database::getConnection()->prepare("SELECT supplier_code FROM suppliers WHERE id = ?");
                $stmt->execute([(int) $data['supplier_id']]);
                $supplierCode = $stmt->fetchColumn() ?: '';
            }
        } catch (\Throwable $e) {
            // non-fatal — email sends without supplier code
        }

        $subject = "Candidature reçue — OCSAPP / Application Received — OCSAPP";

        $templatePath = BASE_PATH . '/app/Views/emails/supplier-invite-confirmed.php';
        ob_start();
        extract([
            'registeredEmail' => $data['email'],
            'companyName'     => $data['business_name'],
            'contactPerson'   => trim($data['first_name'] . ' ' . $data['last_name']),
            'supplierCode'    => $supplierCode,
            'applicationId'   => $applicationId,
        ]);
        include $templatePath;
        $body = ob_get_clean();

        \App\Helpers\EmailHelper::send($data['email'], $subject, $body);
        logger("Supplier application confirmation sent to {$data['email']}", 'info');
    }

    public function showVerifyEmail(): void {
        if (!empty($_SESSION['supplier_id'])) {
            redirect(url('supplier/dashboard'));
            return;
        }

        if (empty($_SESSION['pending_supplier_verification'])) {
            // Try to restore session from email link param
            $email = isset($_GET['email']) ? filter_var(urldecode($_GET['email']), FILTER_VALIDATE_EMAIL) : null;
            if ($email) {
                $db   = \Database::getConnection();
                $stmt = $db->prepare("SELECT id, name, email FROM suppliers WHERE email = ? AND status = 'unverified' LIMIT 1");
                $stmt->execute([$email]);
                $row  = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($row) {
                    $_SESSION['pending_supplier_verification'] = [
                        'supplier_id' => $row['id'],
                        'email'       => $row['email'],
                        'first_name'  => $row['name'],
                        'last_name'   => '',
                    ];
                }
            }
        }

        if (empty($_SESSION['pending_supplier_verification'])) {
            redirect(url('supplier/apply'));
            return;
        }

        require __DIR__ . '/../Views/supplier/verify-email.php';
    }

    public function verifyEmail(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('supplier/verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('supplier/verify-email'));
            return;
        }

        $pending = $_SESSION['pending_supplier_verification'] ?? null;
        if (!$pending) {
            redirect(url('supplier/apply'));
            return;
        }

        $maxAttempts = 5;
        $attempts    = &$_SESSION['supplier_verification_attempts'];

        if ($attempts >= $maxAttempts) {
            setFlash('error', 'Too many attempts. Please request a new code.');
            redirect(url('supplier/verify-email'));
            return;
        }

        $submitted = preg_replace('/\D/', '', post('code', ''));

        if (strlen($submitted) !== 6) {
            $attempts++;
            setFlash('error', 'Please enter the complete 6-digit code.');
            redirect(url('supplier/verify-email'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT email_verification_code, email_verification_expires_at
                FROM suppliers WHERE id = ? AND status = 'unverified'
            ");
            $stmt->execute([$pending['supplier_id']]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                setFlash('error', 'Account not found. Please apply again.');
                unset($_SESSION['pending_supplier_verification'], $_SESSION['supplier_verification_attempts']);
                redirect(url('supplier/apply'));
                return;
            }

            if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
                setFlash('error', 'Your code has expired. Please request a new one.');
                redirect(url('supplier/verify-email'));
                return;
            }

            if (!hash_equals($row['email_verification_code'], $submitted)) {
                $attempts++;
                $remaining = $maxAttempts - $attempts;
                $msg = $remaining > 0
                    ? "Incorrect code. {$remaining} attempt(s) remaining."
                    : 'Too many failed attempts. Please request a new code.';
                setFlash('error', $msg);
                redirect(url('supplier/verify-email'));
                return;
            }

            // Code valid - flip to pending_verification
            $db->prepare("
                UPDATE suppliers SET
                    status = 'pending_verification',
                    email_verification_code = NULL,
                    email_verification_expires_at = NULL,
                    email_verification_attempts = 0
                WHERE id = ?
            ")->execute([$pending['supplier_id']]);

            // Now fire admin notifications (held until email is confirmed)
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                \App\Helpers\NotificationHelper::supplierApplication([
                    'id'            => $pending['application_id'],
                    'lead_id'       => $pending['lead_id'],
                    'first_name'    => $pending['first_name'],
                    'last_name'     => $pending['last_name'],
                    'email'         => $pending['email'],
                    'business_name' => $pending['business_name'],
                    'neq_number'    => $pending['neq_number'],
                ]);
            } catch (\Exception $e) {
                logger("Supplier app notification error: " . $e->getMessage(), 'error');
            }

            try {
                // Re-fetch data needed for admin email
                $appStmt = $db->prepare("SELECT * FROM supplier_applications WHERE id = ?");
                $appStmt->execute([$pending['application_id']]);
                $appData = $appStmt->fetch(\PDO::FETCH_ASSOC);
                if ($appData) {
                    $this->sendAdminApplicationEmail($appData, $pending['application_id'], $pending['lead_id']);
                    $this->sendApplicantConfirmationEmail($appData, $pending['application_id']);
                }
            } catch (\Exception $e) {
                logger("Supplier post-verification email error: " . $e->getMessage(), 'error');
            }

            // Auto-login
            $_SESSION['supplier_id']                      = $pending['supplier_id'];
            $_SESSION['supplier_email']                   = $pending['email'];
            $_SESSION['supplier_name']                    = $pending['business_name'];
            $_SESSION['supplier_status']                  = 'pending_verification';
            $_SESSION['supplier_verification_deadline']   = date('Y-m-d H:i:s', strtotime('+30 days'));

            unset($_SESSION['pending_supplier_verification'], $_SESSION['supplier_verification_attempts']);

            logger("Supplier application verified: {$pending['email']} (Supplier #{$pending['supplier_id']})", 'info');
            $lang = $_SESSION['language'] ?? 'fr';
            setFlash('success', $lang === 'fr'
                ? 'Courriel vérifié ! Bienvenue sur OCSAPP. Votre demande est en cours d\'examen.'
                : 'Email verified! Welcome to OCSAPP. Your application is under review.'
            );
            redirect(url('supplier/dashboard'));

        } catch (\PDOException $e) {
            logger("Supplier email verification error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('supplier/verify-email'));
        }
    }

    public function resendSupplierVerification(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('supplier/verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('supplier/verify-email'));
            return;
        }

        $pending = $_SESSION['pending_supplier_verification'] ?? null;
        if (!$pending) {
            redirect(url('supplier/apply'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $db->prepare("
                UPDATE suppliers SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?,
                    email_verification_attempts = 0
                WHERE id = ? AND status = 'unverified'
            ")->execute([$verificationCode, $verificationExpires, $pending['supplier_id']]);

            $_SESSION['supplier_verification_attempts'] = 0;

            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendSupplierVerificationCode([
                    'first_name'        => $pending['first_name'],
                    'email'             => $pending['email'],
                    'verification_code' => $verificationCode,
                    'verify_url'        => $appUrl . '/supplier/verify-email?email=' . urlencode($pending['email']) . '&lang=fr',
                    'verify_url_en'     => $appUrl . '/supplier/verify-email?email=' . urlencode($pending['email']) . '&lang=en',
                    'magic_link_url_fr' => $appUrl . '/supplier/verify-email/auto?sid=' . $pending['supplier_id'] . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/supplier/verify-email/auto?sid=' . $pending['supplier_id'] . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
            } catch (\Exception $e) {
                logger("Failed to resend supplier verification code: " . $e->getMessage(), 'warning');
            }

            setFlash('success', 'A new code has been sent to your email.');
            redirect(url('supplier/verify-email'));

        } catch (\PDOException $e) {
            logger("Supplier resend verification error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('supplier/verify-email'));
        }
    }

    public function autoVerifyEmail(): void
    {
        $sid  = (int) ($_GET['sid'] ?? 0);
        $code = preg_replace('/\D/', '', $_GET['code'] ?? '');
        $lang = in_array($_GET['lang'] ?? '', ['fr', 'en']) ? ($_GET['lang']) : 'fr';
        $fr   = ($lang === 'fr');

        if (!$sid || strlen($code) !== 6) {
            setFlash('error', $fr ? 'Lien de vérification invalide.' : 'Invalid verification link.');
            redirect(url('supplier/apply'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT id, email, name, status,
                       email_verification_code, email_verification_expires_at
                FROM suppliers WHERE id = ?
            ");
            $stmt->execute([$sid]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$supplier) {
                setFlash('error', $fr ? 'Compte introuvable. Veuillez soumettre une nouvelle demande.' : 'Account not found. Please submit a new application.');
                redirect(url('supplier/apply'));
                return;
            }

            // Already verified
            if ($supplier['status'] !== 'unverified') {
                setFlash('info', $fr
                    ? 'Votre courriel est déjà vérifié. Connectez-vous pour accéder à votre compte.'
                    : 'Your email is already verified. Please log in to access your account.'
                );
                redirect(url('supplier/login'));
                return;
            }

            if (empty($supplier['email_verification_code']) || empty($supplier['email_verification_expires_at'])) {
                setFlash('error', $fr
                    ? 'Ce lien n\'est plus valide. Entrez votre code manuellement ou demandez-en un nouveau.'
                    : 'This link is no longer valid. Enter your code manually or request a new one.'
                );
                redirect(url('supplier/verify-email'));
                return;
            }

            if (new \DateTime() > new \DateTime($supplier['email_verification_expires_at'])) {
                setFlash('error', $fr
                    ? 'Ce lien a expiré. Entrez votre code manuellement ou demandez-en un nouveau.'
                    : 'This link has expired. Enter your code manually or request a new one.'
                );
                redirect(url('supplier/verify-email'));
                return;
            }

            if (!hash_equals($supplier['email_verification_code'], $code)) {
                setFlash('error', $fr
                    ? 'Lien invalide. Entrez votre code manuellement.'
                    : 'Invalid link. Please enter your code manually.'
                );
                redirect(url('supplier/verify-email'));
                return;
            }

            // Code valid
            $db->prepare("
                UPDATE suppliers SET
                    status = 'pending_verification',
                    email_verification_code = NULL,
                    email_verification_expires_at = NULL,
                    email_verification_attempts = 0
                WHERE id = ?
            ")->execute([$sid]);

            // Fire post-verification notifications
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appStmt = $db->prepare("SELECT * FROM supplier_applications WHERE supplier_id = ? ORDER BY created_at DESC LIMIT 1");
                $appStmt->execute([$sid]);
                $appData = $appStmt->fetch(\PDO::FETCH_ASSOC);
                if ($appData) {
                    $leadStmt = $db->prepare("SELECT id FROM leads WHERE supplier_id = ? ORDER BY created_at DESC LIMIT 1");
                    $leadStmt->execute([$sid]);
                    $leadRow  = $leadStmt->fetch(\PDO::FETCH_ASSOC);
                    $leadId   = $leadRow['id'] ?? null;

                    \App\Helpers\NotificationHelper::supplierApplication([
                        'id'            => $appData['id'],
                        'lead_id'       => $leadId,
                        'first_name'    => $appData['first_name'] ?? $supplier['name'],
                        'last_name'     => $appData['last_name'] ?? '',
                        'email'         => $supplier['email'],
                        'business_name' => $appData['business_name'] ?? $supplier['name'],
                        'neq_number'    => $appData['neq_number'] ?? '',
                    ]);
                    $this->sendAdminApplicationEmail($appData, $appData['id'], $leadId);
                    $this->sendApplicantConfirmationEmail($appData, $appData['id']);
                }
            } catch (\Exception $e) {
                logger("Supplier auto-verify post-notification error: " . $e->getMessage(), 'error');
            }

            // Auto-login
            unset($_SESSION['pending_supplier_verification'], $_SESSION['supplier_verification_attempts']);
            $_SESSION['supplier_id']                    = $sid;
            $_SESSION['supplier_email']                 = $supplier['email'];
            $_SESSION['supplier_name']                  = $supplier['name'];
            $_SESSION['supplier_status']                = 'pending_verification';
            $_SESSION['supplier_verification_deadline'] = date('Y-m-d H:i:s', strtotime('+30 days'));
            $_SESSION['language']                       = $lang;

            logger("Supplier auto-verified: {$supplier['email']} (Supplier #{$sid})", 'info');
            setFlash('success', $fr
                ? 'Courriel vérifié ! Bienvenue sur OCSAPP. Votre demande est en cours d\'examen.'
                : 'Email verified! Welcome to OCSAPP. Your application is under review.'
            );
            redirect(url('supplier/dashboard'));

        } catch (\PDOException $e) {
            logger("Supplier auto email verification error: " . $e->getMessage(), 'error');
            setFlash('error', $fr ? 'Une erreur est survenue. Veuillez réessayer.' : 'An error occurred. Please try again.');
            redirect(url('supplier/verify-email'));
        }
    }

    public function login(): void {
        // Already logged in?
        if (isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/dashboard'));
            return;
        }

        view('supplier.login', [
            'pageTitle' => 'Supplier Login - OCSAPP Marketplace'
        ]);
    }

    public function processLogin(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        $email = post('email', '');
        $password = post('password', '');
        $remember = post('remember', false);

        if (empty($email) || empty($password)) {
            setFlash('error', 'Email and password are required');
            back();
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT * FROM suppliers
                WHERE email = ?
                AND status IN ('active', 'pending_verification')
                AND can_login = 1
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                // Check if the account exists but is inactive/expired
                $checkStmt = $db->prepare("SELECT status FROM suppliers WHERE email = ? LIMIT 1");
                $checkStmt->execute([$email]);
                $found = $checkStmt->fetch();
                if ($found && $found['status'] === 'inactive') {
                    setFlash('error', 'Your account has been deactivated. Your verification period may have expired. Please contact <a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a> for assistance.');
                } else {
                    setFlash('error', 'Invalid email or password');
                }
                logger("Supplier login failed: Email not found or inactive - {$email}", 'info');
                back();
            }

            // Verify password
            if (!password_verify($password, $supplier['password_hash'])) {
                setFlash('error', 'Invalid email or password');
                logger("Supplier login failed: Invalid password - {$email}", 'info');
                back();
            }

            // Set session
            $_SESSION['supplier_id'] = $supplier['id'];
            $_SESSION['supplier_email'] = $supplier['email'];
            $_SESSION['supplier_name'] = $supplier['company_name'] ?? $supplier['name'];
            $_SESSION['supplier_status'] = $supplier['status'];
            $_SESSION['supplier_verification_deadline'] = $supplier['verification_deadline'] ?? null;

            // Check if password was never changed — show reminder
            $isFirstLogin = empty($supplier['last_login_at']);
            $passwordNeverChanged = empty($supplier['password_changed_at']);

            if ($passwordNeverChanged) {
                $createdAt = strtotime($supplier['created_at'] ?? 'now');
                $daysSinceCreated = (time() - $createdAt) / 86400;

                if ($isFirstLogin) {
                    $_SESSION['supplier_password_reminder'] = 'first';
                } elseif ($daysSinceCreated >= 7) {
                    $_SESSION['supplier_password_reminder'] = 'week';
                }
            }

            // Update last login
            $stmt = $db->prepare("UPDATE suppliers SET last_login_at = NOW() WHERE id = ?");
            $stmt->execute([$supplier['id']]);

            // Handle remember me
            if ($remember) {
                require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
                \App\Helpers\RememberMeHelper::setSupplierToken((int)$supplier['id']);
            }

            logger("Supplier logged in: {$supplier['email']}", 'info');
            supplierAuditLog($supplier['id'], 'login', 'Supplier logged in', null);
            $lang = $_SESSION['language'] ?? 'fr';
            $greeting = $lang === 'fr'
                ? 'Bon retour, ' . ($supplier['company_name'] ?? $supplier['name'])
                : 'Welcome back, ' . ($supplier['company_name'] ?? $supplier['name']);
            setFlash('success', $greeting);
            redirect(url('supplier/dashboard'));

        } catch (\PDOException $e) {
            logger("Supplier login error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    public function logout(): void {
        $supplierEmail = $_SESSION['supplier_email'] ?? 'Unknown';
        $supplierId    = $_SESSION['supplier_id']    ?? null;
        $lang          = $_SESSION['language']       ?? 'fr';

        unset($_SESSION['supplier_id']);
        unset($_SESSION['supplier_email']);
        unset($_SESSION['supplier_name']);

        // Clear remember me cookie and DB token
        if ($supplierId) {
            require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
            \App\Helpers\RememberMeHelper::clearSupplierToken((int)$supplierId);
        }

        $msg = $lang === 'fr' ? 'Vous avez été déconnecté avec succès.' : 'You have been logged out successfully.';
        logger("Supplier logged out: {$supplierEmail}", 'info');
        setFlash('success', $msg);
        redirect(url('supplier/login'));
    }

    public function dashboard(): void {
        // Check authentication
        if (!isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/login'));
            return;
        }

        try {
            $db = \Database::getConnection();
            $supplierId = $_SESSION['supplier_id'];

            // Get supplier info
            $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                unset($_SESSION['supplier_id']);
                redirect(url('supplier/login'));
                return;
            }

            // Ensure supplier_status is in session (for older sessions)
            $_SESSION['supplier_status'] = $supplier['status'];
            $_SESSION['supplier_verification_deadline'] = $supplier['verification_deadline'] ?? null;

            // Get product count
            $stmt = $db->prepare("
                SELECT COUNT(*) as total,
                       SUM(CASE WHEN is_available = 1 THEN 1 ELSE 0 END) as available
                FROM supplier_products
                WHERE supplier_id = ?
            ");
            $stmt->execute([$supplierId]);
            $productStats = $stmt->fetch();

            // Get recent purchase orders (exclude drafts — only show sent+)
            $stmt = $db->prepare("
                SELECT po.*,
                       COUNT(poi.id) as item_count
                FROM purchase_orders po
                LEFT JOIN purchase_order_items poi ON po.id = poi.purchase_order_id
                WHERE po.supplier_id = ? AND po.status != 'draft'
                GROUP BY po.id
                ORDER BY po.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$supplierId]);
            $recentOrders = $stmt->fetchAll();

            view('supplier.dashboard', [
                'supplier' => $supplier,
                'productStats' => $productStats,
                'recentOrders' => $recentOrders,
                'pageTitle' => 'Supplier Dashboard'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier dashboard error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading dashboard');
            redirect(url('supplier/login'));
        }
    }

    public function acceptInvite(): void {
        $token = get('token', '');

        if (empty($token)) {
            setFlash('error', 'Invalid invitation link');
            redirect(url('/'));
        }

        try {
            $db = \Database::getConnection();

            // Check if invite exists and is valid
            $stmt = $db->prepare("
                SELECT * FROM supplier_invites
                WHERE token = ?
                AND status = 'pending'
                AND expires_at > NOW()
                LIMIT 1
            ");
            $stmt->execute([$token]);
            $invite = $stmt->fetch();

            if (!$invite) {
                setFlash('error', 'This invitation is invalid or has expired');
                redirect(url('/'));
            }

            view('supplier.accept-invite', [
                'invite' => $invite,
                'token' => $token,
                'pageTitle' => 'Accept Supplier Invitation'
            ]);

        } catch (\PDOException $e) {
            logger("Accept invite error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error processing invitation');
            redirect(url('/'));
        }
    }

    public function completeRegistration(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
            return;
        }

        $token = post('token', '');

        // Collect & sanitise
        $validPackages = ['Essential', 'Experience', 'Prestige', 'Enterprise'];
        $rawPackage    = trim(post('subscription_package', 'Essential'));
        $package       = in_array($rawPackage, $validPackages) ? $rawPackage : 'Essential';

        $data = [
            'email'                       => trim(post('email', '')),
            'first_name'                  => trim(post('first_name', '')),
            'last_name'                   => trim(post('last_name', '')),
            'phone'                       => trim(post('phone', '')),
            'business_name'               => trim(post('business_name', '')),
            'legal_name'                  => trim(post('legal_name', '')),
            'operating_names'             => trim(post('operating_names', '')),
            'neq_number'                  => trim(post('neq_number', '')),
            'registered_address_street'   => trim(post('registered_address_street', '')),
            'registered_address_city'     => trim(post('registered_address_city', '')),
            'registered_address_province' => trim(post('registered_address_province', 'QC')),
            'registered_address_postal'   => trim(post('registered_address_postal', '')),
            'subscription_package'        => $package,
        ];

        $_SESSION['_old_input'] = $data;

        // Validate required fields
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid account email address.');
            back();
            return;
        }

        foreach (['first_name','last_name','phone','business_name','legal_name','neq_number',
                  'registered_address_street','registered_address_city','registered_address_postal'] as $f) {
            if (empty($data[$f])) {
                setFlash('error', 'Please fill in all required fields.');
                back();
                return;
            }
        }

        if (!preg_match('/^[0-9]{10}$/', $data['neq_number'])) {
            setFlash('error', 'NEQ must be exactly 10 digits.');
            back();
            return;
        }

        $password = post('password', '');
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', 'Password must contain: ' . implode(', ', $pwErrors) . '.');
            back();
            return;
        }
        if ($password !== post('password_confirmation', '')) {
            setFlash('error', 'Passwords do not match.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            // Verify invite
            $stmt = $db->prepare("
                SELECT * FROM supplier_invites
                WHERE token = ? AND status = 'pending' AND expires_at > NOW()
            ");
            $stmt->execute([$token]);
            $invite = $stmt->fetch();

            if (!$invite) {
                throw new \Exception('Invalid or expired invitation');
            }

            $registeredEmail = $data['email'];

            // If registering with a different email, ensure it's not already in use
            if ($registeredEmail !== $invite['email']) {
                $chk = $db->prepare("SELECT id FROM suppliers WHERE email = ? LIMIT 1");
                $chk->execute([$registeredEmail]);
                if ($chk->fetch()) {
                    setFlash('error', 'A supplier account already exists with that email address.');
                    back();
                    return;
                }
            }

            // Check duplicate application
            $dup = $db->prepare("SELECT id FROM supplier_applications WHERE email = ? AND status IN ('pending','under_review')");
            $dup->execute([$registeredEmail]);
            if ($dup->fetch()) {
                setFlash('error', 'An application with this email is already under review.');
                back();
                return;
            }

            // Handle document uploads (same rules as self-apply)
            $uploadDir     = 'uploads/supplier-applications';
            $fullUploadDir = BASE_PATH . '/public/' . $uploadDir;
            if (!is_dir($fullUploadDir)) {
                mkdir($fullUploadDir, 0755, true);
            }

            $docFields  = ['doc_certificate_incorporation','doc_declaration_registration','doc_enterprise_register'];
            $docPaths   = array_fill_keys($docFields, null);
            $allowedMimes = ['application/pdf','image/jpeg','image/png'];
            $allowedExts  = ['pdf','jpg','jpeg','png'];
            $maxSize      = 5 * 1024 * 1024;

            foreach ($docFields as $field) {
                if (!empty($_FILES[$field]['tmp_name']) && is_uploaded_file($_FILES[$field]['tmp_name'])) {
                    $file = $_FILES[$field];

                    if ($file['size'] > $maxSize) {
                        setFlash('error', 'Document files must be under 5MB each.');
                        back();
                        return;
                    }

                    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, $allowedExts)) {
                        setFlash('error', 'Only PDF, JPG, and PNG files are allowed for documents.');
                        back();
                        return;
                    }

                    if (preg_match('/\.(php|phtml|php[0-9]|phar|exe|sh|bat)/i', pathinfo($file['name'], PATHINFO_FILENAME))) {
                        setFlash('error', 'Invalid file detected.');
                        back();
                        return;
                    }

                    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $file['tmp_name']);
                    finfo_close($finfo);

                    if (!in_array($mimeType, $allowedMimes, true)) {
                        setFlash('error', 'Invalid file type detected.');
                        back();
                        return;
                    }

                    $safeFilename = 'supapp_' . uniqid('', true) . '_' . time() . '.' . $ext;
                    $destPath     = $fullUploadDir . '/' . $safeFilename;

                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        chmod($destPath, 0644);
                        $docPaths[$field] = $uploadDir . '/' . $safeFilename;
                    }
                }
            }

            $db->beginTransaction();

            // 1. Create supplier_applications record — status PENDING (same as self-apply)
            $stmt = $db->prepare("
                INSERT INTO supplier_applications (
                    first_name, last_name, email, phone, business_name,
                    neq_number, legal_name, operating_names,
                    registered_address_street, registered_address_city,
                    registered_address_province, registered_address_postal,
                    doc_certificate_incorporation, doc_declaration_registration, doc_enterprise_register,
                    subscription_package, status
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $data['first_name'], $data['last_name'], $registeredEmail, $data['phone'], $data['business_name'],
                $data['neq_number'], $data['legal_name'], $data['operating_names'],
                $data['registered_address_street'], $data['registered_address_city'],
                $data['registered_address_province'], $data['registered_address_postal'],
                $docPaths['doc_certificate_incorporation'],
                $docPaths['doc_declaration_registration'],
                $docPaths['doc_enterprise_register'],
                $data['subscription_package'],
            ]);
            $applicationId = (int) $db->lastInsertId();

            // 2. CRM lead — status NEW (admin still needs to qualify/convert)
            $companyName   = $data['business_name'];
            $contactPerson = trim($data['first_name'] . ' ' . $data['last_name']);
            $fullAddress   = $data['registered_address_street'] . ', ' . $data['registered_address_city']
                           . ', ' . $data['registered_address_province'] . ' ' . $data['registered_address_postal'];
            $interestDetails = "Invited by admin.\nNEQ: {$data['neq_number']}\nLegal Name: {$data['legal_name']}";
            if ($data['operating_names']) $interestDetails .= "\nOperating Names: {$data['operating_names']}";
            $interestDetails .= "\nRegistered Address: {$fullAddress}\nApplication ID: #{$applicationId}";
            $docCount = count(array_filter($docPaths));
            $interestDetails .= "\nDocuments uploaded: {$docCount}/3";

            $db->prepare("
                INSERT INTO leads (
                    first_name, last_name, email, phone, company_name,
                    source, source_details, status, priority, interest_type, interest_details,
                    city, province, country, notes
                ) VALUES (?, ?, ?, ?, ?, 'invite', 'Admin Invitation', 'new', 'medium', 'supplier', ?, ?, ?, 'Canada', ?)
            ")->execute([
                $data['first_name'], $data['last_name'], $registeredEmail, $data['phone'], $companyName,
                $interestDetails,
                $data['registered_address_city'], $data['registered_address_province'],
                "Invited supplier application submitted. Business: {$companyName}\nNEQ: {$data['neq_number']}"
            ]);
            $leadId = (int) $db->lastInsertId();

            $db->prepare("UPDATE supplier_applications SET lead_id = ? WHERE id = ?")->execute([$leadId, $applicationId]);
            $db->prepare("INSERT INTO lead_activities (lead_id, activity_type, description, created_by) VALUES (?, 'note', ?, NULL)")
               ->execute([$leadId, "Invited supplier submitted application #{$applicationId}. Pending admin review."]);

            // 3. Create supplier account — status PENDING_VERIFICATION (same as self-apply)
            $passwordHash  = password_hash($password, PASSWORD_DEFAULT);
            $supplierCode  = 'SUP-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

            $db->prepare("
                INSERT INTO suppliers (
                    name, supplier_code, company_name, contact_person, email, phone,
                    address, city, province, postal_code, country,
                    tax_number, password_hash, can_login, status, password_changed_at, verification_deadline,
                    subscription_package
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, ?, 1, 'pending_verification', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), ?)
            ")->execute([
                $companyName, $supplierCode, $companyName, $contactPerson,
                $registeredEmail, $data['phone'],
                $data['registered_address_street'], $data['registered_address_city'],
                $data['registered_address_province'], $data['registered_address_postal'],
                $data['neq_number'], $passwordHash,
                $data['subscription_package'],
            ]);
            $supplierId = (int) $db->lastInsertId();

            $db->prepare("UPDATE supplier_applications SET supplier_id = ? WHERE id = ?")->execute([$supplierId, $applicationId]);

            // 4. Mark invite as accepted, link supplier, record actual signup email
            $db->prepare("UPDATE supplier_invites SET status = 'accepted', accepted_at = NOW(), supplier_id = ?, registered_email = ? WHERE id = ?")
               ->execute([$supplierId, $registeredEmail, $invite['id']]);

            $db->commit();
            unset($_SESSION['_old_input']);

            // Admin bell — notify to review (not "account created")
            try {
                \App\Helpers\NotificationHelper::add(
                    'supplier_application',
                    'Invited Supplier — Pending Review',
                    "{$companyName} completed their invited application and is awaiting approval.",
                    ['link' => 'admin/sellers?status=pending', 'icon' => 'user-check', 'priority' => 'high']
                );
            } catch (\Exception $e) {
                logger("Invite application notification error: " . $e->getMessage(), 'error');
            }

            // Admin email
            try {
                $this->sendInviteRegistrationEmail($registeredEmail, $companyName, $contactPerson, $supplierId, $leadId);
            } catch (\Exception $e) {
                logger("Invite registration email error: " . $e->getMessage(), 'error');
            }

            // Supplier confirmation email — sent to the registered email
            try {
                $this->sendInviteConfirmationToSupplier($registeredEmail, $companyName, $contactPerson, $supplierCode, $applicationId);
            } catch (\Exception $e) {
                logger("Invite supplier confirmation email error: " . $e->getMessage(), 'error');
            }

            $emailNote = ($registeredEmail !== $invite['email']) ? " [registered as {$registeredEmail}]" : '';
            logger("Invited supplier submitted application: {$invite['email']}{$emailNote} (Supplier #{$supplierId}, App #{$applicationId}, Lead #{$leadId})", 'info');
            setFlash('success', 'Your application has been submitted! We\'ll review it and be in touch within 2–3 business days.');
            redirect(url('supplier/login'));

        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) $db->rollBack();
            logger("Complete registration error: " . $e->getMessage(), 'error');
            setFlash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Show forgot password form
     */
    public function forgotPassword(): void {
        // Already logged in?
        if (isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/dashboard'));
            return;
        }

        view('supplier.forgot-password', [
            'pageTitle' => 'Forgot Password - Supplier Portal'
        ]);
    }

    /**
     * Process forgot password form — send reset link
     */
    public function sendResetLink(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        $email = trim(post('email', ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            // Always show success message to prevent email enumeration
            $successMessage = 'If an account exists with that email, a password reset link has been sent. Please check your inbox.';

            // Look up the supplier
            $stmt = $db->prepare("SELECT id, email, company_name, contact_person FROM suppliers WHERE email = ? AND can_login = 1 LIMIT 1");
            $stmt->execute([$email]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                // Don't reveal that the email doesn't exist
                setFlash('success', $successMessage);
                redirect(url('supplier/forgot-password'));
                return;
            }

            // Rate limit: max 3 reset requests per hour per email
            $stmt = $db->prepare("
                SELECT COUNT(*) as cnt FROM supplier_password_resets
                WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND used_at IS NULL
            ");
            $stmt->execute([$email]);
            $recent = $stmt->fetch();

            if ($recent && $recent['cnt'] >= 3) {
                setFlash('error', 'Too many reset requests. Please try again in an hour.');
                redirect(url('supplier/forgot-password'));
                return;
            }

            // Generate secure token
            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);          // store hash, never the raw token
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token hash
            $stmt = $db->prepare("
                INSERT INTO supplier_password_resets (supplier_id, email, token, expires_at)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$supplier['id'], $email, $tokenHash, $expiresAt]);

            // Send reset email
            $resetUrl = url('supplier/reset-password?token=' . $token);
            $firstName = explode(' ', $supplier['contact_person'] ?? $supplier['company_name'])[0];

            $this->sendPasswordResetEmail($email, $firstName, $resetUrl);

            logger("Supplier password reset requested: {$email}", 'info');
            setFlash('success', $successMessage);
            redirect(url('supplier/forgot-password'));

        } catch (\PDOException $e) {
            logger("Supplier forgot password error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    /**
     * Show reset password form (with token)
     */
    public function resetPassword(): void {
        $token = get('token', '');

        if (empty($token)) {
            setFlash('error', 'Invalid or missing reset token.');
            redirect(url('supplier/forgot-password'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Validate token
            $stmt = $db->prepare("
                SELECT spr.*, s.email as supplier_email, s.company_name
                FROM supplier_password_resets spr
                JOIN suppliers s ON spr.supplier_id = s.id
                WHERE spr.token = ? AND spr.expires_at > NOW() AND spr.used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([hash('sha256', $token)]);
            $reset = $stmt->fetch();

            if (!$reset) {
                setFlash('error', 'This password reset link is invalid or has expired. Please request a new one.');
                redirect(url('supplier/forgot-password'));
                return;
            }

            view('supplier.reset-password', [
                'pageTitle' => 'Reset Password - Supplier Portal',
                'token' => $token,
                'email' => $reset['supplier_email']
            ]);

        } catch (\PDOException $e) {
            logger("Supplier reset password view error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('supplier/forgot-password'));
        }
    }

    /**
     * Process password reset form
     */
    public function processResetPassword(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        $token = post('token', '');
        $password = post('password', '');
        $passwordConfirm = post('password_confirmation', '');

        if (empty($token)) {
            setFlash('error', 'Invalid reset token.');
            redirect(url('supplier/forgot-password'));
            return;
        }

        // Validate password — same strength requirements as main portal
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', 'Password must contain: ' . implode(', ', $pwErrors) . '.');
            back();
            return;
        }

        if ($password !== $passwordConfirm) {
            setFlash('error', 'Passwords do not match.');
            back();
            return;
        }

        try {
            $db = \Database::getConnection();

            // Validate token
            $stmt = $db->prepare("
                SELECT spr.*, s.email as supplier_email
                FROM supplier_password_resets spr
                JOIN suppliers s ON spr.supplier_id = s.id
                WHERE spr.token = ? AND spr.expires_at > NOW() AND spr.used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([hash('sha256', $token)]);
            $reset = $stmt->fetch();

            if (!$reset) {
                setFlash('error', 'This password reset link is invalid or has expired. Please request a new one.');
                redirect(url('supplier/forgot-password'));
                return;
            }

            $db->beginTransaction();

            // Update password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE suppliers SET password_hash = ?, password_changed_at = NOW() WHERE id = ?");
            $stmt->execute([$passwordHash, $reset['supplier_id']]);

            // Mark token as used
            $stmt = $db->prepare("UPDATE supplier_password_resets SET used_at = NOW() WHERE id = ?");
            $stmt->execute([$reset['id']]);

            // Invalidate all other pending tokens for this supplier
            $stmt = $db->prepare("UPDATE supplier_password_resets SET used_at = NOW() WHERE supplier_id = ? AND used_at IS NULL AND id != ?");
            $stmt->execute([$reset['supplier_id'], $reset['id']]);

            $db->commit();

            logger("Supplier password reset completed: {$reset['supplier_email']}", 'info');
            setFlash('success', 'Your password has been reset successfully. Please log in with your new password.');
            redirect(url('supplier/login'));

        } catch (\PDOException $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Supplier reset password error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    /**
     * Send password reset email to supplier
     */
    private function sendPasswordResetEmail(string $email, string $firstName, string $resetUrl): void {
        $templatePath = __DIR__ . '/../Views/emails/supplier-password-reset.php';

        if (file_exists($templatePath)) {
            $body = file_get_contents($templatePath);
            $body = str_replace([
                '{{first_name}}',
                '{{reset_url}}',
                '{{current_year}}'
            ], [
                htmlspecialchars($firstName),
                htmlspecialchars($resetUrl),
                date('Y')
            ], $body);
        } else {
            // Fallback plain HTML
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <h2>Password Reset Request</h2>
                    <p>Hi {$firstName},</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='{$resetUrl}'>{$resetUrl}</a></p>
                    <p>This link expires in 1 hour.</p>
                    <p>If you didn't request this, ignore this email.</p>
                </div>
            ";
        }

        \App\Helpers\EmailHelper::send(
            $email,
            'Reset Your Password - OCSAPP Supplier Portal',
            $body
        );
    }

    /**
     * Display settings page
     */
    public function settings(): void {
        // Check authentication
        if (!isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/login'));
            return;
        }

        try {
            $db = \Database::getConnection();
            $supplierId = $_SESSION['supplier_id'];

            // Get supplier info
            $stmt = $db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                unset($_SESSION['supplier_id']);
                redirect(url('supplier/login'));
                return;
            }

            view('supplier.settings', [
                'supplier' => $supplier,
                'pageTitle' => 'Settings'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier settings error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading settings');
            redirect(url('supplier/dashboard'));
        }
    }

    /**
     * Update profile/address info
     */
    public function updateProfile(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        if (!isset($_SESSION['supplier_id'])) {
            jsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }

        try {
            $db = \Database::getConnection();
            $supplierId = $_SESSION['supplier_id'];

            // Validate phone (non-blocking — warn but save)
            $phone = post('phone', '');
            $phoneDigits = preg_replace('/\D/', '', $phone);
            if ($phone && (strlen($phoneDigits) < 10 || strlen($phoneDigits) > 11)) {
                jsonResponse(['success' => false, 'message' => 'Phone number must be 10-11 digits'], 422);
                return;
            }

            // Validate postal code
            $postalCode = post('postal_code', '');
            if ($postalCode && !preg_match('/^[A-Za-z]\d[A-Za-z]\s?\d[A-Za-z]\d$/', $postalCode)) {
                jsonResponse(['success' => false, 'message' => 'Invalid Canadian postal code format (e.g. M5V 2T6)'], 422);
                return;
            }

            $lat = post('latitude', '');
            $lng = post('longitude', '');

            $stmt = $db->prepare("
                UPDATE suppliers SET
                    company_name = ?,
                    contact_person = ?,
                    phone = ?,
                    address = ?,
                    city = ?,
                    province = ?,
                    postal_code = ?,
                    country = ?,
                    tax_number = ?,
                    latitude = ?,
                    longitude = ?
                WHERE id = ?
            ");

            $stmt->execute([
                post('company_name', ''),
                post('contact_person', ''),
                post('phone', ''),
                post('address', ''),
                post('city', ''),
                post('province', ''),
                post('postal_code', ''),
                post('country', 'Canada'),
                post('tax_number', ''),
                $lat !== '' ? (float)$lat : null,
                $lng !== '' ? (float)$lng : null,
                $supplierId
            ]);

            // Update session name if changed
            $_SESSION['supplier_name'] = post('company_name', $_SESSION['supplier_name'] ?? '');

            logger("Supplier profile updated: " . $_SESSION['supplier_email'], 'info');
            jsonResponse(['success' => true, 'message' => 'Profile updated successfully']);

        } catch (\PDOException $e) {
            logger("Supplier profile update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating profile'], 500);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        // Check authentication
        if (!isset($_SESSION['supplier_id'])) {
            jsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }

        $currentPassword = post('current_password', '');
        $newPassword = post('new_password', '');
        $confirmPassword = post('confirm_password', '');

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            jsonResponse(['success' => false, 'message' => 'All fields are required']);
            return;
        }

        if (strlen($newPassword) < 8) {
            jsonResponse(['success' => false, 'message' => 'New password must be at least 8 characters']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            jsonResponse(['success' => false, 'message' => 'New passwords do not match']);
            return;
        }

        try {
            $db = \Database::getConnection();
            $supplierId = $_SESSION['supplier_id'];

            // Get current password hash
            $stmt = $db->prepare("SELECT password_hash FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch();

            if (!$supplier) {
                jsonResponse(['success' => false, 'message' => 'Supplier not found'], 404);
                return;
            }

            // Verify current password
            if (!password_verify($currentPassword, $supplier['password_hash'])) {
                jsonResponse(['success' => false, 'message' => 'Current password is incorrect']);
                return;
            }

            // Update password and mark as changed
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE suppliers SET password_hash = ?, password_changed_at = NOW() WHERE id = ?");
            $stmt->execute([$newPasswordHash, $supplierId]);

            // Clear password reminder flag
            unset($_SESSION['supplier_password_reminder']);

            logger("Supplier password changed: " . $_SESSION['supplier_email'], 'info');
            jsonResponse(['success' => true, 'message' => 'Password updated successfully']);

        } catch (\PDOException $e) {
            logger("Supplier password update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating password'], 500);
        }
    }

    /**
     * Update supplier banking / payment information
     * POST /supplier/update-banking
     */
    public function updateBanking(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
            return;
        }

        if (!isset($_SESSION['supplier_id'])) {
            jsonResponse(['success' => false, 'message' => 'Not authenticated'], 401);
            return;
        }

        try {
            $db = \Database::getConnection();
            $supplierId = (int) $_SESSION['supplier_id'];

            $pref = post('payment_preference', '');
            if (!in_array($pref, ['eft', 'interac', 'cheque', ''])) {
                jsonResponse(['success' => false, 'message' => 'Invalid payment preference'], 422);
                return;
            }

            // Validate interac email if preference is interac
            $interacEmail = trim(post('interac_email', ''));
            if ($pref === 'interac' && $interacEmail && !filter_var($interacEmail, FILTER_VALIDATE_EMAIL)) {
                jsonResponse(['success' => false, 'message' => 'Invalid e-Transfer email address'], 422);
                return;
            }

            $db->prepare("
                UPDATE suppliers SET
                    payment_preference  = ?,
                    bank_name           = ?,
                    bank_transit        = ?,
                    bank_institution    = ?,
                    bank_account        = ?,
                    bank_account_holder = ?,
                    bank_account_type   = ?,
                    interac_email       = ?
                WHERE id = ?
            ")->execute([
                $pref ?: null,
                trim(post('bank_name', '')) ?: null,
                preg_replace('/\D/', '', post('bank_transit', '')) ?: null,
                preg_replace('/\D/', '', post('bank_institution', '')) ?: null,
                preg_replace('/\D/', '', post('bank_account', '')) ?: null,
                trim(post('bank_account_holder', '')) ?: null,
                in_array(post('bank_account_type'), ['chequing', 'savings']) ? post('bank_account_type') : null,
                $interacEmail ?: null,
                $supplierId
            ]);

            logger("Supplier banking info updated: supplier #{$supplierId}", 'info');

            \App\Helpers\NotificationHelper::addSupplierNotification(
                $supplierId,
                'payment_info_updated',
                'Payment Info Updated',
                'Your preferred payment method and banking details have been saved.',
                url('supplier/settings'),
                'university',
                'Infos de paiement mises à jour',
                'Votre mode de paiement préféré et vos coordonnées bancaires ont été enregistrés.'
            );

            $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
            jsonResponse(['success' => true, 'message' => $fr
                ? 'Informations de paiement enregistrées avec succès'
                : 'Payment information saved successfully'
            ]);

        } catch (\PDOException $e) {
            logger("Supplier banking update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error saving payment information'], 500);
        }
    }

    /**
     * Dismiss password change reminder for this session
     */
    public function dismissPasswordReminder(): void {
        $_SESSION['supplier_reminder_dismissed'] = true;
        jsonResponse(['success' => true]);
    }

    /**
     * Send admin email when an invited supplier completes registration
     */
    /**
     * Send confirmation email to the supplier at their registered email
     */
    private function sendInviteConfirmationToSupplier(
        string $registeredEmail,
        string $companyName,
        string $contactPerson,
        string $supplierCode,
        int $applicationId
    ): void {
        $subject = "Candidature reçue — OCSAPP / Application Received — OCSAPP";

        $templatePath = BASE_PATH . '/app/Views/emails/supplier-invite-confirmed.php';
        ob_start();
        extract([
            'registeredEmail' => $registeredEmail,
            'companyName'     => $companyName,
            'contactPerson'   => $contactPerson,
            'supplierCode'    => $supplierCode,
            'applicationId'   => $applicationId,
        ]);
        include $templatePath;
        $body = ob_get_clean();

        \App\Helpers\EmailHelper::send($registeredEmail, $subject, $body);
    }

    private function sendInviteRegistrationEmail(string $email, string $companyName, string $contactPerson, int $supplierId, int $leadId): void {
        $supplierUrl = url('admin/suppliers/edit?id=' . $supplierId);
        $leadUrl     = url('admin/leads/view?id=' . $leadId);

        $subject = "New Supplier Registered — {$companyName}";
        $body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: linear-gradient(135deg, #00b207 0%, #008505 100%); padding: 24px; text-align: center;'>
                    <h1 style='color: white; margin: 0; font-size: 22px;'>New Supplier Registered</h1>
                </div>
                <div style='padding: 24px; background: #f9f9f9;'>
                    <p style='font-size: 15px; color: #333;'>An invited supplier has completed registration and their account is now active.</p>
                    <div style='background: white; border-left: 4px solid #00b207; padding: 16px; margin: 16px 0;'>
                        <p style='margin: 4px 0; color: #555;'><strong>Company:</strong> {$companyName}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Contact:</strong> {$contactPerson}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Email:</strong> {$email}</p>
                        <p style='margin: 4px 0; color: #555;'><strong>Supplier ID:</strong> #{$supplierId}</p>
                    </div>
                    <div style='text-align: center; margin: 24px 0;'>
                        <a href='{$leadUrl}' style='display: inline-block; background: #00b207; color: white; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: bold; margin-right: 8px;'>View in CRM</a>
                        <a href='{$supplierUrl}' style='display: inline-block; background: #374151; color: white; padding: 12px 28px; text-decoration: none; border-radius: 8px; font-weight: bold;'>Supplier Profile</a>
                    </div>
                </div>
                <div style='background: #333; color: #999; padding: 16px; text-align: center; font-size: 12px;'>
                    <p style='margin: 0;'>&copy; " . date('Y') . " OCSAPP. Automated notification.</p>
                </div>
            </div>
        ";

        \App\Helpers\EmailHelper::send('info@ocsapp.ca', $subject, $body);
    }
}
