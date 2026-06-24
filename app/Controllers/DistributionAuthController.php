<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * DistributionAuthController - Business Account Authentication
 * Handles registration, login, and dashboard for Distribution portal
 */
class DistributionAuthController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Distribution Portal Landing Page
     */
    public function landing(): void
    {
        // If already logged in as business, redirect to dashboard
        if ($this->isBusinessLoggedIn()) {
            redirect('distribution/dashboard');
            return;
        }

        view('distribution.landing');
    }

    /**
     * Show Login Form
     */
    public function showLogin(): void
    {
        // If already logged in, redirect to dashboard
        if ($this->isBusinessLoggedIn()) {
            redirect('distribution/dashboard');
            return;
        }

        view('distribution.login', [
            'error' => $_SESSION['login_error'] ?? null
        ]);

        unset($_SESSION['login_error']);
    }

    /**
     * Process Login
     */
    public function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/login');
            return;
        }

        // Verify CSRF
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
            return;
        }

        $email    = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = !empty($_POST['remember']);

        // Validate input
        if (empty($email) || empty($password)) {
            setFlash('error', 'Please enter your email and password.');
            back();
            return;
        }

        try {
            // Find user with business role
            $stmt = $this->db->prepare("
                SELECT u.*, r.name as role_name, bp.id as business_profile_id, bp.company_name,
                       bp.status as business_status, bp.agreement_agreed_at
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN business_profiles bp ON u.id = bp.user_id
                WHERE u.email = ? AND r.name = 'business'
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                setFlash('error', 'Invalid email or password.');
                back();
                return;
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                setFlash('error', 'Invalid email or password.');
                back();
                return;
            }

            // Check user status
            if ($user['status'] !== 'active') {
                setFlash('error', 'Your account is not active. Please contact support.');
                back();
                return;
            }

            // Check business profile status — suspended is blocked, pending gets limited access
            if ($user['business_status'] === 'suspended') {
                setFlash('error', 'Your business account has been suspended. Please contact support.');
                back();
                return;
            }

            // Login successful - set session
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => 'business',
                'status' => $user['status']
            ];

            $_SESSION['business'] = [
                'id'             => $user['business_profile_id'],
                'company_name'   => $user['company_name'],
                'user_id'        => $user['id'],
                'status'         => $user['business_status'],
            ];

            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // Remember me
            if ($remember) {
                require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
                \App\Helpers\RememberMeHelper::setBusinessToken((int)$user['id']);
            }

            // Gate: active accounts that haven't signed the agreement go to documents first
            if ($user['business_status'] === 'active' && empty($user['agreement_agreed_at'])) {
                $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
                setFlash('agreement_required', $fr ? 'Veuillez lire et signer votre Accord de services de distribution pour activer votre compte.' : 'Please review and sign your Distribution Service Agreement to activate your account.');
                redirect('distribution/documents');
                return;
            }

            redirect('distribution/dashboard');

        } catch (\PDOException $e) {
            error_log('Distribution login error: ' . $e->getMessage());
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    /**
     * Show Registration Form
     */
    public function showRegister(): void
    {
        // If already logged in, redirect to dashboard
        if ($this->isBusinessLoggedIn()) {
            redirect('distribution/dashboard');
            return;
        }

        view('distribution.register', [
            'errors' => $_SESSION['register_errors'] ?? [],
            'old' => $_SESSION['register_old'] ?? []
        ]);

        unset($_SESSION['register_errors'], $_SESSION['register_old']);
    }

    /**
     * Process Registration
     */
    public function register(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/register');
            return;
        }

        // Verify CSRF
        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['register_errors'] = ['general' => 'Invalid request. Please try again.'];
            redirect('distribution/register');
            return;
        }

        // Collect and sanitize input
        $data = [
            'company_name'                => sanitize($_POST['company_name'] ?? ''),
            'neq_number'                  => preg_replace('/[^0-9]/', '', $_POST['neq_number'] ?? ''),
            'legal_name'                  => sanitize($_POST['legal_name'] ?? ''),
            'operating_names'             => sanitize($_POST['operating_names'] ?? ''),
            'registered_address_street'   => sanitize($_POST['registered_address_street'] ?? ''),
            'registered_address_city'     => sanitize($_POST['registered_address_city'] ?? ''),
            'registered_address_province' => sanitize($_POST['registered_address_province'] ?? 'QC'),
            'registered_address_postal'   => strtoupper(sanitize($_POST['registered_address_postal'] ?? '')),
            'first_name'                  => sanitize($_POST['first_name'] ?? ''),
            'last_name'                   => sanitize($_POST['last_name'] ?? ''),
            'email'                       => sanitize($_POST['email'] ?? ''),
            'phone'                       => sanitize($_POST['phone'] ?? ''),
            'delivery_street'             => sanitize($_POST['delivery_street'] ?? ''),
            'delivery_city'               => sanitize($_POST['delivery_city'] ?? ''),
            'delivery_province'           => sanitize($_POST['delivery_province'] ?? ''),
            'delivery_postal_code'        => strtoupper(sanitize($_POST['delivery_postal_code'] ?? '')),
            'password'                    => $_POST['password'] ?? '',
            'password_confirmation'       => $_POST['password_confirmation'] ?? '',
            'terms'                       => isset($_POST['terms'])
        ];

        // Handle document upload
        $docPath = null;
        $uploadError = null;
        if (!empty($_FILES['doc_certificate']['name'])) {
            $file = $_FILES['doc_certificate'];
            $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];
            $allowedExts  = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if ($file['error'] !== UPLOAD_ERR_OK) {
                $uploadError = 'Document upload failed. Please try again.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $uploadError = 'Document must be less than 5MB.';
            } elseif (!in_array($ext, $allowedExts)) {
                $uploadError = 'Only PDF, JPG, and PNG files are allowed.';
            } elseif (!in_array(mime_content_type($file['tmp_name']), $allowedMimes)) {
                $uploadError = 'Invalid file type.';
            } else {
                $uploadDir     = 'uploads/distribution-applications';
                $fullUploadDir = BASE_PATH . '/public/' . $uploadDir;
                if (!is_dir($fullUploadDir)) {
                    mkdir($fullUploadDir, 0755, true);
                }
                $safeFilename = 'distapp_' . uniqid('', true) . '_' . time() . '.' . $ext;
                $destPath     = $fullUploadDir . '/' . $safeFilename;
                if (move_uploaded_file($file['tmp_name'], $destPath)) {
                    chmod($destPath, 0644);
                    $docPath = $uploadDir . '/' . $safeFilename;
                } else {
                    $uploadError = 'Failed to save document. Please try again.';
                }
            }
        }
        // Document is optional — $docPath stays null if nothing uploaded

        if ($uploadError) {
            $data['doc_certificate_error'] = $uploadError;
        }

        // Validation
        $errors = $this->validateRegistration($data);

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_old'] = $data;
            unset($_SESSION['register_old']['password'], $_SESSION['register_old']['password_confirmation']);
            redirect('distribution/register');
            return;
        }

        try {
            $this->db->beginTransaction();

            // Check if user already exists (admins, sellers, etc. can also register as distribution)
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            $existingUser = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($existingUser) {
                $userId = $existingUser['id'];
            } else {
                // Create new user account
                $stmt = $this->db->prepare("
                    INSERT INTO users (first_name, last_name, email, phone, password, status, email_verified_at, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW(), NOW())
                ");
                $stmt->execute([
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'],
                    password_hash($data['password'], PASSWORD_DEFAULT)
                ]);

                $userId = $this->db->lastInsertId();

                // Get business role ID and assign (only for new users)
                $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'business'");
                $stmt->execute();
                $role = $stmt->fetch(\PDO::FETCH_ASSOC);

                if (!$role) {
                    throw new \Exception('Business role not found. Please run the migration first.');
                }

                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$userId, $role['id']]);
            }

            // Create business profile with status = 'unverified' until email is confirmed
            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $stmt = $this->db->prepare("
                INSERT INTO business_profiles (
                    user_id, company_name,
                    neq_number, legal_name, operating_names,
                    registered_address_street, registered_address_city,
                    registered_address_province, registered_address_postal,
                    doc_certificate,
                    delivery_street, delivery_city, delivery_province,
                    delivery_postal_code, delivery_country,
                    status, verification_deadline,
                    email_verification_code, email_verification_expires_at,
                    created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', 'unverified', DATE_ADD(NOW(), INTERVAL 30 DAY), ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $userId,
                $data['company_name'],
                $data['neq_number'],
                $data['legal_name'],
                $data['operating_names'] ?: null,
                $data['registered_address_street'],
                $data['registered_address_city'],
                $data['registered_address_province'],
                $data['registered_address_postal'],
                $docPath,
                $data['delivery_street'],
                $data['delivery_city'],
                $data['delivery_province'],
                $data['delivery_postal_code'],
                $verificationCode,
                $verificationExpires,
            ]);

            $businessProfileId = $this->db->lastInsertId();

            $this->db->commit();

            // Store pending verification state in session (no auto-login yet)
            $_SESSION['pending_business_verification'] = [
                'business_profile_id' => $businessProfileId,
                'user_id'             => $userId,
                'email'               => $data['email'],
                'first_name'          => $data['first_name'],
                'last_name'           => $data['last_name'],
                'company_name'        => $data['company_name'],
                'neq_number'          => $data['neq_number'],
                'legal_name'          => $data['legal_name'],
            ];
            $_SESSION['verification_attempts'] = 0;

            // Send verification code email
            try {
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendDistributionVerificationCode([
                    'first_name'        => $data['first_name'],
                    'email'             => $data['email'],
                    'verification_code' => $verificationCode,
                    'verify_url_fr'     => $appUrl . '/distribution/verify-email?lang=fr',
                    'verify_url_en'     => $appUrl . '/distribution/verify-email?lang=en',
                    'magic_link_url_fr' => $appUrl . '/distribution/verify-email/auto?bpid=' . $businessProfileId . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/distribution/verify-email/auto?bpid=' . $businessProfileId . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
            } catch (\Exception $e) {
                error_log('Failed to send distribution verification code: ' . $e->getMessage());
            }

            redirect('distribution/verify-email');

        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            // Clean up uploaded file if DB failed
            if ($docPath) {
                $fullPath = BASE_PATH . '/public/' . $docPath;
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            error_log('Distribution registration error: ' . $e->getMessage());

            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $_SESSION['register_errors'] = ['email' => 'This email is already registered.'];
            } else {
                $_SESSION['register_errors'] = ['general' => 'An error occurred. Please try again.'];
            }

            $_SESSION['register_old'] = $data;
            unset($_SESSION['register_old']['password'], $_SESSION['register_old']['password_confirmation']);
            redirect('distribution/register');
        }
    }

    /**
     * Show the email verification page.
     */
    public function showVerifyEmail(): void
    {
        if (empty($_SESSION['pending_business_verification'])) {
            redirect('distribution/register');
            return;
        }
        require __DIR__ . '/../Views/distribution/verify-email.php';
    }

    /**
     * Handle code submission from the verification page.
     */
    public function verifyEmail(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/verify-email');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['verify_errors'] = ['general' => 'Invalid request. Please try again.'];
            redirect('distribution/verify-email');
            return;
        }

        $pending = $_SESSION['pending_business_verification'] ?? null;
        if (!$pending) {
            redirect('distribution/register');
            return;
        }

        $maxAttempts = 5;
        $attempts    = &$_SESSION['verification_attempts'];

        if ($attempts >= $maxAttempts) {
            $_SESSION['verify_errors'] = ['general' => 'Too many attempts. Please request a new code.'];
            redirect('distribution/verify-email');
            return;
        }

        $submitted = preg_replace('/\D/', '', $_POST['code'] ?? '');

        if (strlen($submitted) !== 6) {
            $attempts++;
            $_SESSION['verify_errors'] = ['general' => 'Please enter the complete 6-digit code.'];
            redirect('distribution/verify-email');
            return;
        }

        // Fetch code from DB
        $stmt = $this->db->prepare("
            SELECT email_verification_code, email_verification_expires_at
            FROM business_profiles
            WHERE id = ? AND status = 'unverified'
        ");
        $stmt->execute([$pending['business_profile_id']]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            $_SESSION['verify_errors'] = ['general' => 'Account not found. Please register again.'];
            redirect('distribution/register');
            return;
        }

        // Check expiry
        if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
            $_SESSION['verify_errors'] = ['general' => 'Your code has expired. Please request a new one.'];
            redirect('distribution/verify-email');
            return;
        }

        // Check code
        if (!hash_equals($row['email_verification_code'], $submitted)) {
            $attempts++;
            $remaining = $maxAttempts - $attempts;
            $msg = $remaining > 0
                ? "Incorrect code. {$remaining} attempt(s) remaining."
                : 'Too many failed attempts. Please request a new code.';
            $_SESSION['verify_errors'] = ['general' => $msg];
            redirect('distribution/verify-email');
            return;
        }

        // Code is valid — flip status to pending and clear code columns
        $this->db->prepare("
            UPDATE business_profiles
            SET status = 'pending',
                email_verification_code       = NULL,
                email_verification_expires_at = NULL,
                email_verification_attempts   = 0
            WHERE id = ?
        ")->execute([$pending['business_profile_id']]);

        // Now fire all post-verification notifications
        $businessProfileId = $pending['business_profile_id'];
        $userId            = $pending['user_id'];

        // 1. CRM lead
        $leadId = null;
        try {
            $leadsExists = $this->db->query("SHOW TABLES LIKE 'leads'")->rowCount();
            if ($leadsExists) {
                $interestDetails  = "NEQ: {$pending['neq_number']}\nLegal Name: {$pending['legal_name']}";
                $interestDetails .= "\nBusiness Profile ID: #{$businessProfileId}";

                $leadStmt = $this->db->prepare("
                    INSERT INTO leads (
                        first_name, last_name, email, phone, company_name,
                        source, source_details, status, priority,
                        interest_type, interest_details, business_profile_id,
                        notes, created_at, updated_at
                    ) VALUES (?, ?, ?, '', ?, 'website', 'Distribution Business Registration', 'new', 'medium', 'business', ?, ?, ?, NOW(), NOW())
                ");
                $leadStmt->execute([
                    $pending['first_name'], $pending['last_name'], $pending['email'],
                    $pending['company_name'], $interestDetails, $businessProfileId,
                    "Email-verified distribution application.\nCompany: {$pending['company_name']}\nNEQ: {$pending['neq_number']}",
                ]);
                $leadId = $this->db->lastInsertId();
                $this->db->prepare("
                    INSERT INTO lead_activities (lead_id, activity_type, description, created_at)
                    VALUES (?, 'note', 'Lead created from verified distribution registration.', NOW())
                ")->execute([$leadId]);
            }
        } catch (\Exception $e) {
            error_log('Failed to create CRM lead after verification: ' . $e->getMessage());
        }

        // 2. Application received email
        try {
            \App\Helpers\EmailHelper::sendDistributionApplicationReceived([
                'first_name'   => $pending['first_name'],
                'email'        => $pending['email'],
                'company_name' => $pending['company_name'],
                'neq_number'   => $pending['neq_number'],
            ]);
        } catch (\Exception $e) {
            error_log('Failed to send distribution application received email: ' . $e->getMessage());
        }

        // 3. Admin bell notification
        $adminLink = $leadId
            ? url('admin/leads/view?id=' . $leadId)
            : url('admin/business-accounts/view?id=' . $businessProfileId);
        try {
            \App\Helpers\NotificationHelper::add(
                'new_distribution_application',
                'New Distribution Application',
                htmlspecialchars($pending['company_name']) . ' (' . htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']) . ') submitted a verified distribution application.',
                ['link' => $adminLink, 'icon' => 'building']
            );
        } catch (\Exception $e) {
            error_log('Failed to add distribution bell notification: ' . $e->getMessage());
        }

        // 4. Admin email
        try {
            \App\Helpers\EmailHelper::sendRaw(
                'info@ocsapp.ca',
                'New Distribution Application — ' . $pending['company_name'],
                "<p>A new <strong>email-verified</strong> distribution business account application has been submitted.</p>
                <table style='border-collapse:collapse;width:100%;font-size:14px;'>
                    <tr><td style='padding:8px;color:#666;width:160px;'>Company</td><td style='padding:8px;font-weight:600;'>" . htmlspecialchars($pending['company_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>NEQ</td><td style='padding:8px;'>" . htmlspecialchars($pending['neq_number']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Legal Name</td><td style='padding:8px;'>" . htmlspecialchars($pending['legal_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Contact</td><td style='padding:8px;'>" . htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Email</td><td style='padding:8px;'>" . htmlspecialchars($pending['email']) . "</td></tr>
                </table>
                <p style='margin-top:20px;'><a href=\"{$adminLink}\" style=\"background:#00b207;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;\">Review Application</a></p>"
            );
        } catch (\Exception $e) {
            error_log('Failed to send admin distribution notification email: ' . $e->getMessage());
        }

        // Auto-login
        unset($_SESSION['pending_business_verification'], $_SESSION['verification_attempts']);
        $isAdminSession = in_array($_SESSION['user']['role'] ?? '', ['admin', 'super_admin'], true);
        if (!$isAdminSession) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'         => $userId,
                'email'      => $pending['email'],
                'first_name' => $pending['first_name'],
                'last_name'  => $pending['last_name'],
                'role'       => 'business',
                'status'     => 'active',
            ];
        }
        $_SESSION['business'] = [
            'id'           => $businessProfileId,
            'company_name' => $pending['company_name'],
            'user_id'      => $userId,
            'status'       => 'pending',
        ];

        $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
        setFlash('success', $fr
            ? 'Courriel vérifié ! Votre demande a été soumise et est en cours d\'examen.'
            : 'Email verified! Your application has been submitted and is under review.');
        redirect('distribution/dashboard');
    }

    /**
     * Resend a fresh verification code.
     */
    public function resendVerification(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/verify-email');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            redirect('distribution/verify-email');
            return;
        }

        $pending = $_SESSION['pending_business_verification'] ?? null;
        if (!$pending) {
            redirect('distribution/register');
            return;
        }

        $newCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $newExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

        $this->db->prepare("
            UPDATE business_profiles
            SET email_verification_code = ?, email_verification_expires_at = ?, email_verification_attempts = 0
            WHERE id = ? AND status = 'unverified'
        ")->execute([$newCode, $newExpires, $pending['business_profile_id']]);

        $_SESSION['verification_attempts'] = 0;

        try {
            $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
            \App\Helpers\EmailHelper::sendDistributionVerificationCode([
                'first_name'        => $pending['first_name'],
                'email'             => $pending['email'],
                'verification_code' => $newCode,
                'verify_url_fr'     => $appUrl . '/distribution/verify-email?lang=fr',
                'verify_url_en'     => $appUrl . '/distribution/verify-email?lang=en',
                'magic_link_url_fr' => $appUrl . '/distribution/verify-email/auto?bpid=' . $pending['business_profile_id'] . '&code=' . urlencode($newCode) . '&lang=fr',
                'magic_link_url_en' => $appUrl . '/distribution/verify-email/auto?bpid=' . $pending['business_profile_id'] . '&code=' . urlencode($newCode) . '&lang=en',
            ]);
            $_SESSION['verify_success'] = 'A new code has been sent to your email.';
        } catch (\Exception $e) {
            error_log('Failed to resend distribution verification code: ' . $e->getMessage());
            $_SESSION['verify_errors'] = ['general' => 'Failed to resend code. Please try again.'];
        }

        redirect('distribution/verify-email');
    }

    /**
     * Magic-link auto-verify from email button.
     */
    public function autoVerifyEmail(): void
    {
        $bpid = (int) ($_GET['bpid'] ?? 0);
        $code = preg_replace('/\D/', '', $_GET['code'] ?? '');
        $lang = in_array($_GET['lang'] ?? '', ['fr', 'en']) ? ($_GET['lang']) : 'fr';
        $fr   = ($lang === 'fr');

        if (!$bpid || strlen($code) !== 6) {
            setFlash('error', $fr ? 'Lien de vérification invalide.' : 'Invalid verification link.');
            redirect('distribution/register');
            return;
        }

        $stmt = $this->db->prepare("
            SELECT bp.id, bp.user_id, bp.company_name, bp.neq_number, bp.legal_name,
                   bp.status, bp.email_verification_code, bp.email_verification_expires_at,
                   u.email, u.first_name, u.last_name
            FROM business_profiles bp
            JOIN users u ON u.id = bp.user_id
            WHERE bp.id = ?
        ");
        $stmt->execute([$bpid]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) {
            setFlash('error', $fr ? 'Compte introuvable. Veuillez vous inscrire à nouveau.' : 'Account not found. Please register again.');
            redirect('distribution/register');
            return;
        }

        // Already verified
        if ($row['status'] !== 'unverified') {
            setFlash('info', $fr
                ? 'Votre courriel est déjà vérifié. Connectez-vous pour accéder à votre compte.'
                : 'Your email is already verified. Please log in to access your account.'
            );
            redirect('distribution/login');
            return;
        }

        if (empty($row['email_verification_code']) || empty($row['email_verification_expires_at'])) {
            setFlash('error', $fr
                ? 'Ce lien n\'est plus valide. Entrez votre code manuellement ou demandez-en un nouveau.'
                : 'This link is no longer valid. Enter your code manually or request a new one.'
            );
            redirect('distribution/verify-email');
            return;
        }

        if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
            setFlash('error', $fr
                ? 'Ce lien a expiré. Entrez votre code manuellement ou demandez-en un nouveau.'
                : 'This link has expired. Enter your code manually or request a new one.'
            );
            redirect('distribution/verify-email');
            return;
        }

        if (!hash_equals($row['email_verification_code'], $code)) {
            setFlash('error', $fr ? 'Lien invalide. Entrez votre code manuellement.' : 'Invalid link. Please enter your code manually.');
            redirect('distribution/verify-email');
            return;
        }

        // Code valid - flip status
        $this->db->prepare("
            UPDATE business_profiles
            SET status = 'pending',
                email_verification_code = NULL,
                email_verification_expires_at = NULL,
                email_verification_attempts = 0
            WHERE id = ?
        ")->execute([$bpid]);

        // Build pending array for post-verification side-effects
        $pending = [
            'business_profile_id' => $bpid,
            'user_id'             => $row['user_id'],
            'email'               => $row['email'],
            'first_name'          => $row['first_name'],
            'last_name'           => $row['last_name'],
            'company_name'        => $row['company_name'],
            'neq_number'          => $row['neq_number'],
            'legal_name'          => $row['legal_name'],
        ];

        // CRM lead
        $leadId = null;
        try {
            $leadsExists = $this->db->query("SHOW TABLES LIKE 'leads'")->rowCount();
            if ($leadsExists) {
                $interestDetails  = "NEQ: {$pending['neq_number']}\nLegal Name: {$pending['legal_name']}";
                $interestDetails .= "\nBusiness Profile ID: #{$bpid}";
                $leadStmt = $this->db->prepare("
                    INSERT INTO leads (
                        first_name, last_name, email, phone, company_name,
                        source, source_details, status, priority,
                        interest_type, interest_details, business_profile_id,
                        notes, created_at, updated_at
                    ) VALUES (?, ?, ?, '', ?, 'website', 'Distribution Business Registration', 'new', 'medium', 'business', ?, ?, ?, NOW(), NOW())
                ");
                $leadStmt->execute([
                    $pending['first_name'], $pending['last_name'], $pending['email'],
                    $pending['company_name'], $interestDetails, $bpid,
                    "Email-verified distribution application.\nCompany: {$pending['company_name']}\nNEQ: {$pending['neq_number']}",
                ]);
                $leadId = $this->db->lastInsertId();
                $this->db->prepare("
                    INSERT INTO lead_activities (lead_id, activity_type, description, created_at)
                    VALUES (?, 'note', 'Lead created from verified distribution registration.', NOW())
                ")->execute([$leadId]);
            }
        } catch (\Exception $e) {
            error_log('Failed to create CRM lead after auto-verification: ' . $e->getMessage());
        }

        // Application received email
        try {
            \App\Helpers\EmailHelper::sendDistributionApplicationReceived([
                'first_name'   => $pending['first_name'],
                'email'        => $pending['email'],
                'company_name' => $pending['company_name'],
                'neq_number'   => $pending['neq_number'],
            ]);
        } catch (\Exception $e) {
            error_log('Failed to send distribution application received email: ' . $e->getMessage());
        }

        // Admin bell notification
        $adminLink = $leadId
            ? url('admin/leads/view?id=' . $leadId)
            : url('admin/business-accounts/view?id=' . $bpid);
        try {
            \App\Helpers\NotificationHelper::add(
                'new_distribution_application',
                'New Distribution Application',
                htmlspecialchars($pending['company_name']) . ' (' . htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']) . ') submitted a verified distribution application.',
                ['link' => $adminLink, 'icon' => 'building']
            );
        } catch (\Exception $e) {
            error_log('Failed to add distribution bell notification: ' . $e->getMessage());
        }

        // Admin email
        try {
            \App\Helpers\EmailHelper::sendRaw(
                'info@ocsapp.ca',
                'New Distribution Application - ' . $pending['company_name'],
                "<p>A new <strong>email-verified</strong> distribution business account application has been submitted.</p>
                <table style='border-collapse:collapse;width:100%;font-size:14px;'>
                    <tr><td style='padding:8px;color:#666;width:160px;'>Company</td><td style='padding:8px;font-weight:600;'>" . htmlspecialchars($pending['company_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>NEQ</td><td style='padding:8px;'>" . htmlspecialchars($pending['neq_number']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Legal Name</td><td style='padding:8px;'>" . htmlspecialchars($pending['legal_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Contact</td><td style='padding:8px;'>" . htmlspecialchars($pending['first_name'] . ' ' . $pending['last_name']) . "</td></tr>
                    <tr><td style='padding:8px;color:#666;'>Email</td><td style='padding:8px;'>" . htmlspecialchars($pending['email']) . "</td></tr>
                </table>
                <p style='margin-top:20px;'><a href=\"{$adminLink}\" style=\"background:#00b207;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;\">Review Application</a></p>"
            );
        } catch (\Exception $e) {
            error_log('Failed to send admin distribution notification email: ' . $e->getMessage());
        }

        // Auto-login
        unset($_SESSION['pending_business_verification'], $_SESSION['verification_attempts']);
        $isAdminSession = in_array($_SESSION['user']['role'] ?? '', ['admin', 'super_admin'], true);
        if (!$isAdminSession) {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id'         => $row['user_id'],
                'email'      => $row['email'],
                'first_name' => $row['first_name'],
                'last_name'  => $row['last_name'],
                'role'       => 'business',
                'status'     => 'active',
            ];
        }
        $_SESSION['business'] = [
            'id'           => $bpid,
            'company_name' => $row['company_name'],
            'user_id'      => $row['user_id'],
            'status'       => 'pending',
        ];
        $_SESSION['language'] = $lang;

        setFlash('success', $fr
            ? 'Courriel vérifié ! Votre demande a été soumise et est en cours d\'examen.'
            : 'Email verified! Your application has been submitted and is under review.'
        );
        redirect('distribution/dashboard');
    }

    /**
     * Business Dashboard
     */
    public function dashboard(): void
    {
        // Require business login
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        // Block unverified accounts — they must complete email verification first
        if (($_SESSION['business']['status'] ?? '') === 'unverified') {
            redirect('distribution/verify-email');
            return;
        }

        $businessId = $_SESSION['business']['id'] ?? null;

        // Get business profile
        $stmt = $this->db->prepare("
            SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
            FROM business_profiles bp
            INNER JOIN users u ON bp.user_id = u.id
            WHERE bp.id = ?
        ");
        $stmt->execute([$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            $this->logout();
            return;
        }

        // Get request stats
        $stats = [
            'total' => 0,
            'pending' => 0,
            'processing' => 0,
            'completed' => 0
        ];

        try {
            // Total requests
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_requests WHERE business_profile_id = ?");
            $stmt->execute([$businessId]);
            $stats['total'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Pending (draft, submitted, quoted, pending_payment)
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_requests WHERE business_profile_id = ? AND status IN ('draft', 'submitted', 'quoted', 'pending_payment')");
            $stmt->execute([$businessId]);
            $stats['pending'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Processing (paid, processing, ready)
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_requests WHERE business_profile_id = ? AND status IN ('paid', 'processing', 'ready')");
            $stmt->execute([$businessId]);
            $stats['processing'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Completed
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM distribution_requests WHERE business_profile_id = ? AND status = 'completed'");
            $stmt->execute([$businessId]);
            $stats['completed'] = $stmt->fetch(\PDO::FETCH_ASSOC)['count'] ?? 0;

            // Get recent requests
            $stmt = $this->db->prepare("
                SELECT dr.*,
                    (SELECT COUNT(*) FROM distribution_request_items WHERE distribution_request_id = dr.id) as item_count
                FROM distribution_requests dr
                WHERE dr.business_profile_id = ?
                ORDER BY dr.created_at DESC
                LIMIT 5
            ");
            $stmt->execute([$businessId]);
            $recentRequests = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Tables might not exist yet, use defaults
            $recentRequests = [];
        }

        $lang = $_SESSION['language'] ?? 'fr';
        view('distribution.dashboard', [
            'business' => $business,
            'stats' => $stats,
            'recentRequests' => $recentRequests ?? [],
            'pageTitle' => $lang === 'fr' ? 'Tableau de bord' : 'Dashboard',
            'currentPage' => 'dashboard'
        ]);
    }

    /**
     * Settings page (delivery address + account info)
     */
    public function settings(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'] ?? null;

        $stmt = $this->db->prepare("
            SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
            FROM business_profiles bp
            INNER JOIN users u ON bp.user_id = u.id
            WHERE bp.id = ?
        ");
        $stmt->execute([$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$business) {
            $this->logout();
            return;
        }

        $lang = $_SESSION['language'] ?? 'fr';
        view('distribution.settings', [
            'business'    => $business,
            'pageTitle'   => $lang === 'fr' ? 'Paramètres' : 'Settings',
            'currentPage' => 'settings',
        ]);
    }

    /**
     * Update delivery address
     */
    public function updateAddress(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        // CSRF check
        $token = post('_csrf_token', '');
        if (!verifyCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $businessId = $_SESSION['business']['id'];

        $street = trim(post('delivery_street', ''));
        $city = trim(post('delivery_city', ''));
        $province = trim(post('delivery_province', ''));
        $postalCode = trim(post('delivery_postal_code', ''));
        $country = trim(post('delivery_country', 'Canada'));
        $lat = post('delivery_latitude', '');
        $lng = post('delivery_longitude', '');

        try {
            $stmt = $this->db->prepare("
                UPDATE business_profiles SET
                    delivery_street = ?,
                    delivery_city = ?,
                    delivery_province = ?,
                    delivery_postal_code = ?,
                    delivery_country = ?,
                    delivery_latitude = ?,
                    delivery_longitude = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $street, $city, $province, $postalCode, $country,
                $lat !== '' ? (float)$lat : null,
                $lng !== '' ? (float)$lng : null,
                $businessId
            ]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Delivery address updated successfully']);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update billing address
     */
    public function updateBilling(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $token = post('_csrf_token', '');
        if (!verifyCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $sameAsDelivery = (post('use_delivery_for_billing', '0') === '1') ? 1 : 0;

        if ($sameAsDelivery) {
            // Copy delivery address into billing columns
            try {
                $stmt = $this->db->prepare("
                    UPDATE business_profiles SET
                        use_delivery_for_billing = 1,
                        billing_street      = delivery_street,
                        billing_city        = delivery_city,
                        billing_province    = delivery_province,
                        billing_postal_code = delivery_postal_code,
                        billing_country     = delivery_country,
                        updated_at          = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$businessId]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Billing address updated successfully']);
            } catch (\PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
        } else {
            $street     = trim(post('billing_street', ''));
            $city       = trim(post('billing_city', ''));
            $province   = trim(post('billing_province', ''));
            $postalCode = strtoupper(trim(post('billing_postal_code', '')));
            $country    = trim(post('billing_country', 'Canada'));

            if (empty($street) || empty($city) || empty($province) || empty($postalCode)) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => 'Please fill in all billing address fields.']);
                return;
            }

            try {
                $stmt = $this->db->prepare("
                    UPDATE business_profiles SET
                        use_delivery_for_billing = 0,
                        billing_street      = ?,
                        billing_city        = ?,
                        billing_province    = ?,
                        billing_postal_code = ?,
                        billing_country     = ?,
                        updated_at          = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$street, $city, $province, $postalCode, $country, $businessId]);

                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Billing address updated successfully']);
            } catch (\PDOException $e) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
        }
    }

    /**
     * Update payment / banking information
     */
    public function updatePayment(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        $token = post('_csrf_token', '');
        if (!verifyCsrfToken($token)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $businessId = $_SESSION['business']['id'];

        $pref = trim(post('payment_preference', ''));
        if (!in_array($pref, ['eft', 'interac', 'cheque', ''])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid payment preference.']);
            return;
        }

        $interacEmail = trim(post('interac_email', ''));
        if ($pref === 'interac' && $interacEmail && !filter_var($interacEmail, FILTER_VALIDATE_EMAIL)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => 'Invalid e-Transfer email address.']);
            return;
        }

        try {
            $this->db->prepare("
                UPDATE business_profiles SET
                    payment_preference  = ?,
                    bank_name           = ?,
                    bank_transit        = ?,
                    bank_institution    = ?,
                    bank_account        = ?,
                    bank_account_holder = ?,
                    bank_account_type   = ?,
                    interac_email       = ?,
                    updated_at          = NOW()
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
                $businessId
            ]);

            $prefLabel = $pref ?: 'not set';
            \App\Helpers\NotificationHelper::logBusinessActivity(
                (int)$businessId,
                'payment_info_updated',
                "Payment information updated (preference: {$prefLabel}).",
                'business'
            );

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Payment information saved successfully']);
        } catch (\PDOException $e) {
            error_log('Distribution banking update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
    }

    /**
     * Update password
     */
    public function updatePassword(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Not authenticated']);
            return;
        }

        if (!verifyCsrfToken(post('_csrf_token', ''))) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }

        $currentPassword = post('current_password', '');
        $newPassword     = post('new_password', '');
        $confirmPassword = post('confirm_password', '');
        $fr = ($_SESSION['language'] ?? 'fr') === 'fr';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $fr ? 'Tous les champs sont requis.' : 'All fields are required.']);
            return;
        }

        if (strlen($newPassword) < 8) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $fr ? 'Le nouveau mot de passe doit contenir au moins 8 caractères.' : 'New password must be at least 8 characters.']);
            return;
        }

        if ($newPassword !== $confirmPassword) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => $fr ? 'Les mots de passe ne correspondent pas.' : 'New passwords do not match.']);
            return;
        }

        try {
            $userId = $_SESSION['user']['id'];

            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                http_response_code(422);
                echo json_encode(['success' => false, 'message' => $fr ? 'Le mot de passe actuel est incorrect.' : 'Current password is incorrect.']);
                return;
            }

            $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")
                     ->execute([password_hash($newPassword, PASSWORD_DEFAULT), $userId]);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $fr ? 'Mot de passe mis à jour avec succès.' : 'Password updated successfully.']);

        } catch (\PDOException $e) {
            error_log('Distribution password update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $fr ? 'Une erreur est survenue.' : 'An error occurred.']);
        }
    }

    /**
     * Logout
     */
    public function logout(): void
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $lang   = $_SESSION['language']  ?? 'fr';
        if ($userId) {
            require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
            \App\Helpers\RememberMeHelper::clearBusinessToken((int)$userId);
        }
        unset($_SESSION['user'], $_SESSION['business']);
        session_regenerate_id(true);
        $msg = $lang === 'fr' ? 'Vous avez été déconnecté avec succès.' : 'You have been logged out successfully.';
        setFlash('success', $msg);
        redirect('distribution/login');
    }

    /**
     * View uploaded verification documents
     */
    public function documents(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        $stmt = $this->db->prepare("
            SELECT id, doc_certificate, doc_declaration, company_name, status,
                   agreement_agreed_at, agreement_version
            FROM business_profiles
            WHERE id = ?
        ");
        $stmt->execute([$businessId]);
        $profile = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Fetch the current published agreement version
        $vStmt = $this->db->prepare("
            SELECT version FROM legal_content
            WHERE page_type = 'distribution_agreement' AND language = 'fr' AND is_published = 1
            LIMIT 1
        ");
        $vStmt->execute();
        $currentAgreementVersion = (int)($vStmt->fetchColumn() ?: 1);

        // Fetch pending document requests from admin
        $reqStmt = $this->db->prepare("
            SELECT * FROM document_requests
            WHERE business_id = ? AND status = 'pending'
            ORDER BY deadline ASC, created_at ASC
        ");
        $reqStmt->execute([$businessId]);
        $pendingRequests = $reqStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch activity log for business portal history section (last 20)
        $logStmt = $this->db->prepare("
            SELECT action_type, actor, actor_name, description, created_at
            FROM business_activity_log
            WHERE business_id = ?
            ORDER BY created_at DESC
            LIMIT 20
        ");
        $logStmt->execute([$businessId]);
        $activityLog = $logStmt->fetchAll(\PDO::FETCH_ASSOC);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $lang = $_SESSION['language'] ?? 'fr';
        view('distribution.documents', [
            'profile'                 => $profile,
            'flash'                   => $flash,
            'pageTitle'               => $lang === 'fr' ? 'Mes documents' : 'My Documents',
            'currentAgreementVersion' => $currentAgreementVersion,
            'pendingRequests'         => $pendingRequests,
            'activityLog'             => $activityLog,
        ]);
    }

    public function emails(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $lang = $_SESSION['language'] ?? 'fr';
        $fr   = ($lang === 'fr');
        $businessId = $_SESSION['business']['id'];

        try {
            $stmt = $this->db->prepare("
                SELECT u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ? LIMIT 1
            ");
            $stmt->execute([$businessId]);
            $businessEmail = $stmt->fetchColumn();

            if (!$businessEmail) {
                setFlash('error', $fr ? 'Compte introuvable.' : 'Account not found.');
                redirect('distribution/dashboard');
                return;
            }

            $stmt = $this->db->prepare("
                SELECT id, recipient_email, subject, email_type, status, error_message, created_at
                FROM email_log
                WHERE recipient_email = ?
                ORDER BY created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$businessEmail]);
            $emails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_count,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count,
                    MIN(created_at) as first_email,
                    MAX(created_at) as last_email
                FROM email_log
                WHERE recipient_email = ?
            ");
            $stmt->execute([$businessEmail]);
            $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('distribution.emails', [
                'pageTitle'   => $fr ? 'Mes courriels' : 'My Emails',
                'currentPage' => 'emails',
                'emails'      => $emails,
                'stats'       => $stats,
            ]);
        } catch (\PDOException $e) {
            logger("Distribution emails error: " . $e->getMessage(), 'error');
            setFlash('error', $fr ? 'Erreur lors du chargement des courriels.' : 'Error loading email history.');
            redirect('distribution/dashboard');
        }
    }

    /**
     * Upload or replace verification document
     */
    public function uploadDocument(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $fr = ($_SESSION['language'] ?? 'fr') === 'fr';

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', $fr ? 'Jeton de sécurité invalide. Veuillez réessayer.' : 'Invalid security token. Please try again.');
            redirect('distribution/documents');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        if (empty($_FILES['document']['tmp_name']) || !is_uploaded_file($_FILES['document']['tmp_name'])) {
            setFlash('error', $fr ? 'Veuillez sélectionner un fichier à téléverser.' : 'Please select a file to upload.');
            redirect('distribution/documents');
            return;
        }

        $file = $_FILES['document'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedExts  = ['pdf', 'jpg', 'jpeg', 'png'];
        $allowedMimes = ['application/pdf', 'image/jpeg', 'image/png'];

        if ($file['size'] > $maxSize) {
            setFlash('error', $fr ? 'La taille du fichier doit être inférieure à 5 Mo.' : 'File size must be less than 5MB.');
            redirect('distribution/documents');
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExts)) {
            setFlash('error', $fr ? 'Seuls les fichiers PDF, JPG et PNG sont acceptés.' : 'Only PDF, JPG, and PNG files are allowed.');
            redirect('distribution/documents');
            return;
        }

        // Block double/disguised extensions
        $innerName = pathinfo(basename($file['name']), PATHINFO_FILENAME);
        if (preg_match('/\.(php|phtml|php3|php4|php5|phar|exe|sh|bat|cmd)/i', $innerName)) {
            logger("Suspicious distribution doc upload blocked: {$file['name']}", 'error');
            setFlash('error', $fr ? 'Fichier invalide détecté.' : 'Invalid file detected.');
            redirect('distribution/documents');
            return;
        }

        // Validate MIME type
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes, true)) {
            setFlash('error', $fr ? 'Type de fichier invalide détecté.' : 'Invalid file type detected.');
            redirect('distribution/documents');
            return;
        }

        $uploadDir     = 'uploads/distribution-docs';
        $fullUploadDir = BASE_PATH . '/public/' . $uploadDir;
        if (!is_dir($fullUploadDir)) {
            mkdir($fullUploadDir, 0755, true);
        }

        $safeFilename = 'bizdoc_' . uniqid('', true) . '_' . time() . '.' . $ext;
        $destPath     = $fullUploadDir . '/' . $safeFilename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            setFlash('error', $fr ? 'Échec du téléversement. Veuillez réessayer.' : 'Failed to upload file. Please try again.');
            redirect('distribution/documents');
            return;
        }

        chmod($destPath, 0644);
        $relativePath = $uploadDir . '/' . $safeFilename;

        $docType   = $_POST['doc_type'] ?? '';
        $requestId = (int)($_POST['request_id'] ?? 0);

        $allowedDocTypes = ['doc_certificate', 'doc_declaration', 'other'];
        if (!in_array($docType, $allowedDocTypes, true)) {
            setFlash('error', $fr ? 'Type de document invalide.' : 'Invalid document type.');
            redirect('distribution/documents');
            return;
        }

        // Standard doc types update the business_profiles column; 'other' has no column
        if ($docType !== 'other') {
            $stmt = $this->db->prepare("UPDATE business_profiles SET {$docType} = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$relativePath, $businessId]);
        }

        // Resolve the display label — for 'other' look it up from the specific document_request
        $docLabels = [
            'doc_certificate' => "Certificat d'incorporation / Certificate of Incorporation",
            'doc_declaration'  => "Déclaration d'immatriculation",
        ];
        $resolvedLabel = $docLabels[$docType] ?? 'Document';
        if ($docType === 'other' && $requestId > 0) {
            try {
                $labelStmt = $this->db->prepare("SELECT doc_label FROM document_requests WHERE id = ? AND business_id = ? LIMIT 1");
                $labelStmt->execute([$requestId, $businessId]);
                $resolvedLabel = $labelStmt->fetchColumn() ?: 'Document';
            } catch (\Exception $e) {}
        }

        // Fulfil the matching document request
        try {
            if ($requestId > 0) {
                // Fulfill by ID — precise, handles 'other' with multiple open requests
                $this->db->prepare("
                    UPDATE document_requests
                    SET status = 'fulfilled', fulfilled_at = NOW(), updated_at = NOW()
                    WHERE id = ? AND business_id = ? AND status = 'pending'
                ")->execute([$requestId, $businessId]);
            } else {
                // Fallback: match by type for doc_certificate / doc_declaration
                $this->db->prepare("
                    UPDATE document_requests
                    SET status = 'fulfilled', fulfilled_at = NOW(), updated_at = NOW()
                    WHERE business_id = ? AND doc_type = ? AND status = 'pending'
                ")->execute([$businessId, $docType]);
            }
        } catch (\Exception $e) {
            error_log('Failed to fulfil document request: ' . $e->getMessage());
        }

        // Track in business_documents (insert new row; previous version stays for audit)
        try {
            $this->db->prepare("
                INSERT INTO business_documents (business_id, doc_type, doc_label, file_path, status, uploaded_at)
                VALUES (?, ?, ?, ?, 'pending', NOW())
            ")->execute([$businessId, $docType, $resolvedLabel, $relativePath]);
        } catch (\Exception $e) {
            error_log('Failed to insert business_documents record: ' . $e->getMessage());
        }

        // Admin bell notification
        try {
            $nameStmt    = $this->db->prepare("SELECT company_name FROM business_profiles WHERE id = ?");
            $nameStmt->execute([$businessId]);
            $companyName = $nameStmt->fetchColumn() ?: "Business #{$businessId}";
            $this->db->prepare("
                INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                VALUES ('document_upload', ?, ?, ?, 'file-alt', 'normal', NOW())
            ")->execute([
                'Document uploadé - ' . htmlspecialchars($companyName),
                htmlspecialchars($companyName) . ' a soumis un nouveau document : ' . htmlspecialchars($resolvedLabel) . '. Vérification requise.',
                '/admin/business-accounts/view?id=' . $businessId . '#documentsPanel',
            ]);
        } catch (\Exception $e) {
            error_log('Failed to insert admin notification for document upload: ' . $e->getMessage());
        }

        // Activity log
        try {
            \App\Helpers\NotificationHelper::logBusinessActivity((int)$businessId, 'document_uploaded', "Document uploaded: '{$resolvedLabel}'.", 'business');
        } catch (\Exception $e) {
            error_log('Failed to log document upload activity: ' . $e->getMessage());
        }

        setFlash('success', $fr ? 'Document téléversé avec succès.' : 'Document uploaded successfully.');
        redirect('distribution/documents');
    }

    /**
     * Check if business user is logged in and their account still exists in DB.
     * Destroys the session if the user or business profile has been deleted.
     */
    private function isBusinessLoggedIn(): bool
    {
        if (!isset($_SESSION['user']['id'], $_SESSION['user']['role'], $_SESSION['business']['id'])) {
            return false;
        }
        if (!in_array($_SESSION['user']['role'], ['business', 'admin', 'super_admin'], true)) {
            return false;
        }
        try {
            $stmt = $this->db->prepare("
                SELECT bp.id FROM business_profiles bp
                INNER JOIN users u ON u.id = bp.user_id
                WHERE bp.id = ? AND u.id = ?
                LIMIT 1
            ");
            $stmt->execute([(int)$_SESSION['business']['id'], (int)$_SESSION['user']['id']]);
            if (!$stmt->fetch()) {
                session_unset();
                session_destroy();
                return false;
            }
        } catch (\Throwable $e) {
            // On transient DB error keep the session — avoid locking out valid users
        }
        return true;
    }

    /**
     * Validate registration data
     */
    private function validateRegistration(array $data): array
    {
        $errors = [];

        // Company name
        if (empty($data['company_name'])) {
            $errors['company_name'] = 'Company name is required.';
        } elseif (strlen($data['company_name']) > 255) {
            $errors['company_name'] = 'Company name is too long.';
        }

        // NEQ
        if (empty($data['neq_number'])) {
            $errors['neq_number'] = 'NEQ (Enterprise Number) is required.';
        } elseif (!preg_match('/^\d{10}$/', $data['neq_number'])) {
            $errors['neq_number'] = 'NEQ must be exactly 10 digits.';
        }

        // Legal name
        if (empty($data['legal_name'])) {
            $errors['legal_name'] = 'Legal name is required.';
        } elseif (strlen($data['legal_name']) > 255) {
            $errors['legal_name'] = 'Legal name is too long.';
        }

        // Registered office address
        if (empty($data['registered_address_street'])) {
            $errors['registered_address_street'] = 'Registered office street address is required.';
        }

        if (empty($data['registered_address_city'])) {
            $errors['registered_address_city'] = 'Registered office city is required.';
        }

        if (empty($data['registered_address_postal'])) {
            $errors['registered_address_postal'] = 'Registered office postal code is required.';
        } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/i', $data['registered_address_postal'])) {
            $errors['registered_address_postal'] = 'Please enter a valid Canadian postal code.';
        }

        // Document upload
        if (!empty($data['doc_certificate_error'])) {
            $errors['doc_certificate'] = $data['doc_certificate_error'];
        }

        // First name
        if (empty($data['first_name'])) {
            $errors['first_name'] = 'First name is required.';
        }

        // Last name
        if (empty($data['last_name'])) {
            $errors['last_name'] = 'Last name is required.';
        }

        // Email
        if (empty($data['email'])) {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } else {
            // Block only if a distribution (business) account already exists for this email
            $stmt = $this->db->prepare("
                SELECT bp.id FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE u.email = ?
            ");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                $errors['email'] = 'This email already has a distribution account. <a href="' . url('distribution/login') . '" style="color:#991b1b;font-weight:700;text-decoration:underline;">Sign in instead</a>.';
            }

            // Check if email is banned from re-registering
            if (empty($errors['email'])) {
                $banStmt = $this->db->prepare("SELECT id FROM deleted_users WHERE email = ? AND can_rejoin = 0 LIMIT 1");
                $banStmt->execute([$data['email']]);
                if ($banStmt->fetch()) {
                    $errors['email'] = 'This account has been disabled. Please contact us at <a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a> for assistance.';
                }
            }
        }

        // Phone
        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone number is required.';
        }

        // Delivery address
        if (empty($data['delivery_street'])) {
            $errors['delivery_street'] = 'Street address is required.';
        }

        if (empty($data['delivery_city'])) {
            $errors['delivery_city'] = 'City is required.';
        }

        if (empty($data['delivery_province'])) {
            $errors['delivery_province'] = 'Province is required.';
        }

        if (empty($data['delivery_postal_code'])) {
            $errors['delivery_postal_code'] = 'Postal code is required.';
        } elseif (!preg_match('/^[A-Z]\d[A-Z]\s?\d[A-Z]\d$/i', $data['delivery_postal_code'])) {
            $errors['delivery_postal_code'] = 'Please enter a valid Canadian postal code.';
        }

        // Password (enforce 10 chars + complexity)
        $pw = $data['password'] ?? '';
        if (empty($pw)) {
            $errors['password'] = 'Password is required.';
        } elseif (strlen($pw) < 10) {
            $errors['password'] = 'Password must be at least 10 characters.';
        } elseif (!preg_match('/[A-Z]/', $pw)) {
            $errors['password'] = 'Password must contain at least one uppercase letter.';
        } elseif (!preg_match('/[a-z]/', $pw)) {
            $errors['password'] = 'Password must contain at least one lowercase letter.';
        } elseif (!preg_match('/[0-9]/', $pw)) {
            $errors['password'] = 'Password must contain at least one number.';
        } elseif (!preg_match('/[^A-Za-z0-9]/', $pw)) {
            $errors['password'] = 'Password must contain at least one special character.';
        }

        // Password confirmation
        if ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'] = 'Passwords do not match.';
        }

        // Terms
        if (!$data['terms']) {
            $errors['terms'] = 'You must accept the terms and conditions.';
        }

        return $errors;
    }

    /**
     * Stream bilingual Distribution Service Agreement as PDF (FR first, EN below)
     */
    public function agreementPdf(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied';
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT language, title, content, version
                FROM legal_content
                WHERE page_type = 'distribution_agreement' AND is_published = 1
                ORDER BY FIELD(language, 'fr', 'en')
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                header('HTTP/1.0 404 Not Found');
                echo 'Agreement not found';
                return;
            }

            $html  = $this->agreementPdfWrapper();
            foreach ($rows as $i => $row) {
                $langLabel = $row['language'] === 'fr' ? 'Version Francaise' : 'English Version';
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

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('Distribution-Service-Agreement.pdf', ['Attachment' => false]);

        } catch (\Exception $e) {
            error_log('Agreement PDF error: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error generating document';
        }
    }

    /**
     * Stream Business Onboarding Package as PDF
     */
    public function onboardingPdf(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            header('HTTP/1.0 403 Forbidden');
            echo 'Access denied';
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT name, content FROM planner_templates
                WHERE slug = 'business-onboarding-package' AND is_active = 1
                LIMIT 1
            ");
            $stmt->execute();
            $template = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$template) {
                header('HTTP/1.0 404 Not Found');
                echo 'Onboarding package not found';
                return;
            }

            $html  = $this->agreementPdfWrapper();
            $html .= '<div class="lang-section">' . $template['content'] . '</div>';
            $html .= '</body></html>';

            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new \Dompdf\Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            $dompdf->stream('OCSAPP-Business-Onboarding-Package.pdf', ['Attachment' => false]);

        } catch (\Exception $e) {
            error_log('Onboarding PDF error: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error generating document';
        }
    }

    /**
     * Record the business's agreement confirmation
     */
    public function confirmAgreement(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            redirect('distribution/documents');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        // Already agreed - idempotent
        $check = $this->db->prepare("SELECT agreement_agreed_at FROM business_profiles WHERE id = ?");
        $check->execute([$businessId]);
        if ($check->fetchColumn()) {
            redirect('distribution/documents');
            return;
        }

        // Get current published version
        $vStmt = $this->db->prepare("
            SELECT version FROM legal_content
            WHERE page_type = 'distribution_agreement' AND language = 'fr' AND is_published = 1
            LIMIT 1
        ");
        $vStmt->execute();
        $version = (int)($vStmt->fetchColumn() ?: 1);

        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $ip = trim(explode(',', $ip)[0]);

        $stmt = $this->db->prepare("
            UPDATE business_profiles
            SET agreement_agreed_at = NOW(),
                agreement_ip        = ?,
                agreement_version   = ?
            WHERE id = ?
        ");
        $stmt->execute([$ip, $version, $businessId]);

        // Fetch business details for notifications and email
        try {
            $infoStmt = $this->db->prepare("
                SELECT bp.company_name, u.first_name, u.email
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $infoStmt->execute([$businessId]);
            $bizInfo = $infoStmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $bizInfo = [];
        }

        // Admin bell — high priority so it surfaces immediately
        try {
            $companyLabel = htmlspecialchars($bizInfo['company_name'] ?? "Business #{$businessId}");
            $this->db->prepare("
                INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                VALUES ('agreement_signed', ?, ?, ?, 'file-signature', 'high', NOW())
            ")->execute([
                'Accord signé - ' . $companyLabel,
                $companyLabel . ' a signé l\'accord de services de distribution (v' . $version . ', IP: ' . htmlspecialchars($ip) . ').',
                '/admin/business-accounts/view?id=' . $businessId,
            ]);
        } catch (\Exception $e) {
            error_log('Failed to insert admin notification for agreement signing: ' . $e->getMessage());
        }

        // Business bell
        try {
            \App\Helpers\NotificationHelper::addBusinessNotification(
                (int)$businessId,
                'agreement_confirmed',
                'Accord signé / Agreement Signed',
                'Votre accord de services de distribution a été enregistré avec succès. / Your distribution service agreement has been successfully recorded.',
                '/distribution/documents',
                'file-signature'
            );
        } catch (\Exception $e) {
            error_log('Failed to add business agreement notification: ' . $e->getMessage());
        }

        // Thank-you email to business
        try {
            if (!empty($bizInfo['email'])) {
                $firstName   = htmlspecialchars($bizInfo['first_name'] ?? '');
                $companyName = htmlspecialchars($bizInfo['company_name'] ?? '');
                $loginUrl    = url('distribution/login');
                $year = date('Y');
                $emailHtml = "
<!DOCTYPE html>
<html lang=\"fr-CA\">
<head><meta charset=\"UTF-8\"><meta name=\"viewport\" content=\"width=device-width,initial-scale=1.0\"></head>
<body style=\"margin:0;padding:0;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:#f5f5f5;\">
<table role=\"presentation\" style=\"width:100%;border-collapse:collapse;background:#f5f5f5;\">
<tr><td align=\"center\" style=\"padding:40px 20px;\">
<table role=\"presentation\" style=\"max-width:620px;width:100%;background:#fff;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);\">
  <tr>
    <td style=\"background:linear-gradient(135deg,#00b207 0%,#007d05 100%);padding:36px 30px;text-align:center;border-radius:12px 12px 0 0;\">
      <img src=\"https://ocsapp.ca/assets/images/logo.png\" alt=\"OCSAPP\" style=\"max-width:160px;height:auto;margin:0 auto 16px;display:block;\">
      <h1 style=\"margin:0;color:#fff;font-size:26px;font-weight:700;\">Accord sign&eacute; !</h1>
      <p style=\"margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:15px;\">Portail Distribution OCSAPP</p>
    </td>
  </tr>
  <tr><td style=\"padding:40px 30px 24px;\">
    <h2 style=\"margin:0 0 16px;color:#1f2937;font-size:20px;\">Bonjour {$firstName},</h2>
    <p style=\"margin:0 0 16px;color:#4b5563;font-size:15px;line-height:1.7;\">
      Nous confirmons que l&rsquo;accord de services de distribution OCSAPP pour <strong>{$companyName}</strong> a &eacute;t&eacute; sign&eacute; avec succ&egrave;s.
    </p>
    <p style=\"margin:0 0 24px;color:#4b5563;font-size:14px;line-height:1.7;\">
      Vous pouvez maintenant acc&eacute;der &agrave; votre portail et soumettre vos premi&egrave;res demandes de distribution.
    </p>
    <p style=\"text-align:center;margin:0 0 24px;\">
      <a href=\"{$loginUrl}\" style=\"background:#00b207;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;\">Acc&eacute;der au portail Distribution</a>
    </p>
    <p style=\"margin:0;color:#6b7280;font-size:13px;\">Des questions ? Contactez-nous &agrave; <a href=\"mailto:info@ocsapp.ca\" style=\"color:#00b207;font-weight:600;\">info@ocsapp.ca</a></p>
  </td></tr>
  <tr><td style=\"padding:0 30px;\">
    <hr style=\"border:none;border-top:2px dashed #e5e7eb;margin:0 0 8px;\">
    <p style=\"text-align:center;font-size:11px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:1.5px;margin:0 0 8px;\">English version follows below / La version fran&ccedil;aise pr&eacute;c&egrave;de</p>
    <hr style=\"border:none;border-top:2px dashed #e5e7eb;margin:0 0 0;\">
  </td></tr>
  <tr>
    <td style=\"background:linear-gradient(135deg,#00b207 0%,#007d05 100%);padding:36px 30px;text-align:center;\">
      <img src=\"https://ocsapp.ca/assets/images/logo.png\" alt=\"OCSAPP\" style=\"max-width:160px;height:auto;margin:0 auto 16px;display:block;\">
      <h1 style=\"margin:0;color:#fff;font-size:26px;font-weight:700;\">Agreement Signed!</h1>
      <p style=\"margin:8px 0 0;color:rgba(255,255,255,0.85);font-size:15px;\">OCSAPP Distribution Portal</p>
    </td>
  </tr>
  <tr><td style=\"padding:40px 30px 24px;\">
    <h2 style=\"margin:0 0 16px;color:#1f2937;font-size:20px;\">Hi {$firstName},</h2>
    <p style=\"margin:0 0 16px;color:#4b5563;font-size:15px;line-height:1.7;\">
      This confirms that the OCSAPP distribution service agreement for <strong>{$companyName}</strong> has been successfully signed.
    </p>
    <p style=\"margin:0 0 24px;color:#4b5563;font-size:14px;line-height:1.7;\">
      You can now access your portal and submit your first distribution requests.
    </p>
    <p style=\"text-align:center;margin:0 0 24px;\">
      <a href=\"{$loginUrl}\" style=\"background:#00b207;color:#fff;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;\">Access Distribution Portal</a>
    </p>
    <p style=\"margin:0;color:#6b7280;font-size:13px;\">Questions? Contact us at <a href=\"mailto:info@ocsapp.ca\" style=\"color:#00b207;font-weight:600;\">info@ocsapp.ca</a></p>
  </td></tr>
  <tr>
    <td style=\"background:#f9fafb;padding:24px 30px;text-align:center;border-radius:0 0 12px 12px;border-top:1px solid #e5e7eb;\">
      <p style=\"margin:0 0 6px;color:#9ca3af;font-size:12px;\">&copy; {$year} OCSAPP. Tous droits r&eacute;serv&eacute;s. / All rights reserved.</p>
      <p style=\"margin:0;color:#9ca3af;font-size:12px;\">Courriel automatique - ne pas r&eacute;pondre. / Automated email - do not reply.</p>
    </td>
  </tr>
</table>
</td></tr>
</table>
</body>
</html>";
                \App\Helpers\EmailHelper::sendRaw(
                    $bizInfo['email'],
                    'Accord signé avec succès / Distribution Agreement Signed — OCSAPP Marketplace',
                    $emailHtml
                );
            }
        } catch (\Exception $e) {
            error_log('Failed to send agreement confirmation email: ' . $e->getMessage());
        }

        // Activity + email log
        try {
            \App\Helpers\NotificationHelper::logBusinessActivity((int)$businessId, 'agreement_signed', "Distribution service agreement signed (v{$version}, IP: {$ip}).", 'business');
            if (!empty($bizInfo['email'])) {
                \App\Helpers\NotificationHelper::logBusinessEmail((int)$businessId, 'Accord signé avec succès / Distribution Agreement Signed - OCSAPP Marketplace', 'Your distribution service agreement has been successfully recorded.');
            }
        } catch (\Exception $e) {
            error_log('Failed to log agreement signing: ' . $e->getMessage());
        }

        $fr = ($_SESSION['language'] ?? 'fr') === 'fr';
        setFlash('success', $fr ? 'Accord signé avec succès. Bienvenue chez OCSAPP Distribution !' : 'Agreement signed successfully. Welcome to OCSAPP Distribution!');
        redirect('distribution/documents');
    }

    /**
     * Shared PDF HTML wrapper with OCSAPP branding
     */
    private function agreementPdfWrapper(): string
    {
        // Embed logo as data URI so dompdf doesn't need a network request
        $logoPath = BASE_PATH . '/public/assets/images/logo.png';
        $logoImg  = '';
        if (file_exists($logoPath)) {
            $logoImg = '<img src="data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) . '" style="height:38px;width:38px;">';
        }

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><style>

            @page { margin: 82px 52px 68px 52px; }

            body {
                font-family: Helvetica, Arial, sans-serif;
                font-size: 11pt;
                color: #1a1a1a;
                margin: 0; padding: 0;
            }

            /* ── Branded header — repeats on every page ── */
            #pdf-header {
                position: fixed;
                top: -72px; left: -52px; right: -52px;
                height: 62px;
                background: #00b207;
            }
            .hdr-inner {
                display: table;
                width: 100%;
                height: 62px;
                padding: 0 20px;
            }
            .hdr-logo  { display: table-cell; vertical-align: middle; width: 46px; }
            .hdr-name  {
                display: table-cell; vertical-align: middle;
                color: #ffffff; font-size: 15pt; font-weight: bold;
                padding-left: 10px; letter-spacing: 0.5px;
            }
            .hdr-right {
                display: table-cell; vertical-align: middle;
                text-align: right;
                color: rgba(255,255,255,0.88);
                font-size: 9pt;
            }

            /* ── Branded footer — repeats on every page ── */
            #pdf-footer {
                position: fixed;
                bottom: -58px; left: -52px; right: -52px;
                height: 44px;
                border-top: 2px solid #00b207;
                background: #f9fafb;
            }
            .ftr-inner {
                display: table;
                width: 100%;
                height: 44px;
                padding: 0 20px;
            }
            .ftr-left  {
                display: table-cell; vertical-align: middle;
                font-size: 8pt; color: #6b7280;
            }
            .ftr-right {
                display: table-cell; vertical-align: middle;
                text-align: right;
                font-size: 8pt; color: #6b7280;
            }

            /* ── Content ── */
            h1 { font-size: 17pt; color: #00b207; margin: 18px 0 6px; }
            h2 { font-size: 13pt; color: #1a5c2a; margin-top: 20px; margin-bottom: 4px; }
            h3 { font-size: 11pt; color: #374151; margin-top: 14px; }
            p, li { line-height: 1.65; margin-bottom: 6px; }
            ul, ol { padding-left: 20px; }

            .lang-section { padding: 16px 0 24px; }
            .lang-label {
                font-size: 8.5pt; font-weight: bold;
                color: #00b207; text-transform: uppercase;
                letter-spacing: 1.2px; margin-bottom: 14px;
                padding-bottom: 6px;
                border-bottom: 1px solid #bbf7d0;
            }
            .page-break { page-break-after: always; }

            table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
            td, th { border: 1px solid #d1d5db; padding: 7px 10px; font-size: 10pt; }
            th { background: #f0fdf4; font-weight: 600; color: #166534; }

            .signature-block {
                margin-top: 28px; border-top: 1px solid #e5e7eb;
                padding-top: 16px;
            }

        </style></head><body>

        <div id="pdf-header">
            <div class="hdr-inner">
                <div class="hdr-logo">' . $logoImg . '</div>
                <div class="hdr-name">OCS Marketplace</div>
                <div class="hdr-right">Distribution Service Agreement<br><span style="font-size:8pt;opacity:0.8;">ocsapp.ca</span></div>
            </div>
        </div>

        <div id="pdf-footer">
            <div class="ftr-inner">
                <div class="ftr-left">Confidential &mdash; OCS Marketplace Inc. &mdash; ocsapp.ca</div>
                <div class="ftr-right">Distribution Service Agreement</div>
            </div>
        </div>

        ';
    }
}
