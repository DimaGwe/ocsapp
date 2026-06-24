<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class AuthController {

    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    public function showLogin(): void {
        AuthMiddleware::guest();
        view('auth.login');
    }

    public function showSellerLogin(): void {
        AuthMiddleware::guest();
        view('seller.login');
    }

    public function showBuyerLogin(): void {
        AuthMiddleware::guest();
        view('buyer.login');
    }

    public function showDriverLogin(): void {
        AuthMiddleware::guest();
        view('delivery.login');
    }

    public function login(): void {
        AuthMiddleware::guest();
        
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        // Validate input
        $email = sanitize(post('email', ''));
        $password = post('password', '');
        $remember = post('remember') === 'on';

        $errors = validateRequired(['email', 'password'], ['email' => $email, 'password' => $password]);
        
        if (!empty($errors)) {
            setFlash('error', 'Please fill in all fields');
            setOldInput(['email' => $email]);
            back();
        }

        if (!validateEmail($email)) {
            setFlash('error', 'Invalid email format');
            setOldInput(['email' => $email]);
            back();
        }

        // Check rate limiting
        if (!AuthMiddleware::checkRateLimitLogin($email)) {
            // Notify admins about account lockout
            \App\Helpers\NotificationHelper::accountLockout($email, $_SERVER['REMOTE_ADDR'] ?? 'unknown');

            setFlash('error', 'Too many login attempts. Please try again in 15 minutes.');
            back();
        }

        try {
            $db = \Database::getConnection();
            
            // Get user with role
            $stmt = $db->prepare("
                SELECT u.*, r.name as role_name 
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.email = ? 
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password'])) {
                AuthMiddleware::logLoginAttempt($email);
                setFlash('error', 'Invalid credentials');
                setOldInput(['email' => $email]);
                back();
            }

            // Check account status
            $status = $user['status'];
            $role   = $user['role_name'] ?? 'buyer';

            if ($status === 'pending' && $role === 'delivery') {
                // Driver applicant — allow through to pending portal
                // (fall through; handled below)
            } elseif ($status === 'rejected' && $role === 'delivery') {
                // Rejected driver — show message and redirect to apply page
                setFlash('error', 'Your driver application was not successful. Please check your email or reapply.');
                back();
            } elseif ($status !== 'active') {
                setFlash('error', 'Your account is ' . $status . '. Please contact support.');
                back();
            }

            // Clear failed attempts
            AuthMiddleware::clearLoginAttempts($email);

            // Update last login
            $stmt = $db->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
            $stmt->execute([$user['id']]);

            // SECURITY FIX: Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // Store user in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'role' => $user['role_name'] ?? 'buyer',
                'avatar' => $user['avatar'],
                'status' => $user['status']
            ];

            // RESTORE CART if user was checking out as guest
            if (isset($_SESSION['pending_checkout_cart'])) {
                $_SESSION['cart'] = $_SESSION['pending_checkout_cart'];
                unset($_SESSION['pending_checkout_cart']);
                
                logger("Cart restored for user after login: " . $user['email'], 'info');
            }

            // Remember me
            if ($remember) {
                require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
                \App\Helpers\RememberMeHelper::setUserToken((int)$user['id']);
            }

            // Log audit
            $this->logAudit($user['id'], 'user.login');

            // Log login for dashboard activity
            $this->logLogin($user['id'], $role, 'success');

            clearOldInput();
            $lang = $_SESSION['language'] ?? 'en';
            $welcomeMsg = $lang === 'fr'
                ? 'Bienvenue, ' . $user['first_name'] . ' !'
                : 'Welcome back, ' . $user['first_name'] . '!';
            setFlash('success', $welcomeMsg);

            // Pending delivery driver → applicant portal
            if ($role === 'delivery' && $status === 'pending') {
                redirect(url('delivery/application-status'));
                return;
            }

            // If user was trying to checkout, redirect back to checkout
            if (isset($_SESSION['return_to_checkout'])) {
                unset($_SESSION['return_to_checkout']);
                redirect(url('checkout'));
                return;
            }

            $dashboards = [
                'super_admin' => 'admin/dashboard',
                'admin' => 'admin/dashboard',
                'admin_staff' => 'admin/dashboard',
                'seller' => 'seller/dashboard',
                'buyer' => 'account',
                'delivery' => 'delivery/dashboard',
                'advertiser' => 'advertiser/dashboard',
                'affiliate' => 'affiliate/dashboard',
            ];

            // If role is delivery (active), ensure driver_availability row exists.
            if ($role === 'delivery') {
                $db->prepare("
                    INSERT IGNORE INTO driver_availability (driver_id, status, updated_at)
                    VALUES (?, 'offline', NOW())
                ")->execute([$user['id']]);

                // Force password reset on first login if admin issued the password
                $forceReset = (bool)($user['force_password_reset'] ?? false);
                if (!$forceReset) {
                    $fStmt = $db->prepare("SELECT force_password_reset FROM users WHERE id = ? LIMIT 1");
                    $fStmt->execute([$user['id']]);
                    $forceReset = (bool)($fStmt->fetchColumn());
                }
                if ($forceReset) {
                    redirect(url('delivery/change-password'));
                    return;
                }
            }

            redirect(url($dashboards[$role] ?? 'account'));

        } catch (\PDOException $e) {
            logger("Login error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    public function showRegister(): void {
        AuthMiddleware::guest();
        view('auth.register');
    }

    public function register(): void {
        AuthMiddleware::guest();
        
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

        // Get and sanitize input
        $data = [
            'first_name' => sanitize(post('first_name', '')),
            'last_name' => sanitize(post('last_name', '')),
            'email' => sanitize(post('email', '')),
            'phone' => sanitize(post('phone', '')),
            'password' => post('password', ''),
            'password_confirmation' => post('password_confirmation', ''),
        ];

        // Validate required fields
        $errors = validateRequired(
            ['first_name', 'last_name', 'email', 'password', 'password_confirmation'],
            $data
        );

        if (!empty($errors)) {
            setFlash('error', 'Please fill in all required fields');
            setOldInput($data);
            back();
        }

        // Validate terms acceptance
        if (post('terms', '') !== 'on') {
            setFlash('error', 'You must accept the Terms of Service and Privacy Policy');
            setOldInput($data);
            back();
        }

        // Validate seller agreement acceptance (sellers only)
        $requestedRoleCheck = sanitize($_GET['role'] ?? post('role', 'buyer'));
        if ($requestedRoleCheck === 'seller' && post('seller_agreement', '') !== 'on') {
            setFlash('error', 'Sellers must read and accept the Seller Agreement.');
            setOldInput($data);
            back();
        }

        // Validate email
        if (!validateEmail($data['email'])) {
            setFlash('error', 'Invalid email format');
            setOldInput($data);
            back();
        }

        // Validate password strength
        $pwErrors = validatePasswordStrength($data['password']);
        if (!empty($pwErrors)) {
            setFlash('error', 'Password must have: ' . implode(', ', $pwErrors));
            setOldInput($data);
            back();
        }

        // Validate password match
        if ($data['password'] !== $data['password_confirmation']) {
            setFlash('error', 'Passwords do not match');
            setOldInput($data);
            back();
        }

        try {
            $db = \Database::getConnection();
            
            // Check if email exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                setFlash('error', 'Email already registered');
                setOldInput($data);
                back();
            }

            // Check if email is banned from re-registering
            $banStmt = $db->prepare("SELECT id FROM deleted_users WHERE email = ? AND can_rejoin = 0 LIMIT 1");
            $banStmt->execute([$data['email']]);
            if ($banStmt->fetch()) {
                $lang = $_SESSION['language'] ?? 'fr';
                setFlash('error', $lang === 'fr'
                    ? 'Ce compte a été désactivé. Veuillez nous contacter à info@ocsapp.ca pour toute question.'
                    : 'This account has been disabled. Please contact us at info@ocsapp.ca for assistance.');
                setOldInput($data);
                back();
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Only 'buyer' and 'seller' are valid self-registration roles
            $requestedRole = sanitize($_GET['role'] ?? post('role', 'buyer'));
            $selectedRole  = in_array($requestedRole, ['buyer', 'seller']) ? $requestedRole : 'buyer';

            $registrationIp = $_SERVER['HTTP_CF_CONNECTING_IP']
                ?? $_SERVER['HTTP_X_FORWARDED_FOR']
                ?? $_SERVER['REMOTE_ADDR']
                ?? null;
            if ($registrationIp && strpos($registrationIp, ',') !== false) {
                $registrationIp = trim(explode(',', $registrationIp)[0]);
            }

            // All new registrations start unverified until email code is confirmed
            $stmt = $db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, terms_accepted_at, terms_accepted_ip, status, role)
                VALUES (?, ?, ?, ?, ?, NOW(), ?, 'unverified', ?)
            ");
            $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $registrationIp,
                $selectedRole,
            ]);

            $userId = $db->lastInsertId();
            
            // Validate role exists in database
            $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
            $stmt->execute([$selectedRole]);
            $role = $stmt->fetch();
            
            if (!$role) {
                // FIXED: Set default role if selected role doesn't exist
                logger("Role '{$selectedRole}' not found, using default 'buyer'", 'warning');
                $selectedRole = 'buyer';
                $stmt = $db->prepare("SELECT id FROM roles WHERE name = ? LIMIT 1");
                $stmt->execute([$selectedRole]);
                $role = $stmt->fetch();
                
                if (!$role) {
                    // This is critical - buyer role must exist
                    throw new \Exception("Default role 'buyer' not found in database. Please run database seeders.");
                }
            }
            
            // Assign role to user
            $stmt = $db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$userId, $role['id']]);

            // Record seller agreement acceptance timestamp
            if ($selectedRole === 'seller') {
                $db->prepare("UPDATE users SET seller_agreement_accepted_at = NOW() WHERE id = ?")
                   ->execute([$userId]);
            }

            // Generate email verification code
            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $db->prepare("
                UPDATE users SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?
                WHERE id = ?
            ")->execute([$verificationCode, $verificationExpires, $userId]);

            // Store pending verification state (cart session keys left intact for after verification)
            $_SESSION['pending_user_verification'] = [
                'user_id'    => $userId,
                'email'      => $data['email'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'role'       => $selectedRole,
            ];
            $_SESSION['verification_attempts'] = 0;

            $this->logAudit($userId, 'user.register');
            clearOldInput();

            // Send verification code email
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendUserVerificationCode([
                    'first_name'        => $data['first_name'],
                    'email'             => $data['email'],
                    'verification_code' => $verificationCode,
                    'verify_url_fr'     => $appUrl . '/verify-email?lang=fr',
                    'verify_url_en'     => $appUrl . '/verify-email?lang=en',
                    'magic_link_url_fr' => $appUrl . '/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/verify-email/auto?uid=' . $userId . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
                logger("Verification code sent to {$data['email']}", 'info');
            } catch (\Exception $e) {
                logger("Failed to send verification code to {$data['email']}: " . $e->getMessage(), 'warning');
            }

            redirect(url('verify-email'));

        } catch (\PDOException $e) {
            logger("Registration error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        } catch (\Exception $e) {
            logger("Registration error: " . $e->getMessage(), 'error');
            setFlash('error', $e->getMessage());
            back();
        }
    }

    public function showVerifyEmail(): void {
        AuthMiddleware::guest();
        if (empty($_SESSION['pending_user_verification'])) {
            redirect(url('register'));
            return;
        }
        view('auth.verify-email');
    }

    public function verifyEmail(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('verify-email'));
            return;
        }

        $pending = $_SESSION['pending_user_verification'] ?? null;
        if (!$pending) {
            redirect(url('register'));
            return;
        }

        $maxAttempts = 5;
        $attempts    = &$_SESSION['verification_attempts'];

        if ($attempts >= $maxAttempts) {
            setFlash('error', 'Too many attempts. Please request a new code.');
            redirect(url('verify-email'));
            return;
        }

        $submitted = preg_replace('/\D/', '', post('code', ''));

        if (strlen($submitted) !== 6) {
            $attempts++;
            setFlash('error', 'Please enter the complete 6-digit code.');
            redirect(url('verify-email'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT email_verification_code, email_verification_expires_at
                FROM users WHERE id = ? AND status = 'unverified'
            ");
            $stmt->execute([$pending['user_id']]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                setFlash('error', 'Account not found. Please register again.');
                unset($_SESSION['pending_user_verification'], $_SESSION['verification_attempts']);
                redirect(url('register'));
                return;
            }

            if (new \DateTime() > new \DateTime($row['email_verification_expires_at'])) {
                setFlash('error', 'Your code has expired. Please request a new one.');
                redirect(url('verify-email'));
                return;
            }

            if (!hash_equals($row['email_verification_code'], $submitted)) {
                $attempts++;
                $remaining = $maxAttempts - $attempts;
                $msg = $remaining > 0
                    ? "Incorrect code. {$remaining} attempt(s) remaining."
                    : 'Too many failed attempts. Please request a new code.';
                setFlash('error', $msg);
                redirect(url('verify-email'));
                return;
            }

            // Code valid - buyers go active, sellers go pending (awaiting admin approval)
            $role        = $pending['role'];
            $finalStatus = ($role === 'seller') ? 'pending' : 'active';

            $db->prepare("
                UPDATE users SET
                    status = ?,
                    email_verified_at = NOW(),
                    email_verification_code = NULL,
                    email_verification_expires_at = NULL,
                    email_verification_attempts = 0
                WHERE id = ?
            ")->execute([$finalStatus, $pending['user_id']]);

            $user = [
                'id'         => $pending['user_id'],
                'email'      => $pending['email'],
                'first_name' => $pending['first_name'],
                'last_name'  => $pending['last_name'],
                'role'       => $role,
                'phone'      => '',
            ];

            // Send post-verification emails
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                if ($role === 'seller') {
                    \App\Helpers\EmailHelper::sendSellerApplicationReceived($user);
                    \App\Helpers\EmailHelper::sendAdminSellerNotification($user);
                    \App\Helpers\NotificationHelper::sellerApplication($user);
                    logger("Seller application emails sent for {$user['email']}", 'info');
                } else {
                    \App\Helpers\EmailHelper::sendBuyerWelcome($user);
                    logger("Buyer welcome email sent to {$user['email']}", 'info');
                }
            } catch (\Exception $e) {
                logger("Failed to send post-verification email: " . $e->getMessage(), 'warning');
            }

            unset($_SESSION['pending_user_verification'], $_SESSION['verification_attempts']);

            $this->logAudit($user['id'], 'user.email_verified');

            if ($role === 'seller') {
                setFlash('success', 'Email verified! Your seller application is under review. We\'ll be in touch within 1-2 business days.');
                redirect(url('login'));
                return;
            }

            // Buyer: auto-login and restore cart if applicable
            $_SESSION['user'] = [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'role'       => $role,
                'avatar'     => null,
                'status'     => 'active',
            ];

            if (isset($_SESSION['pending_checkout_cart'])) {
                $_SESSION['cart'] = $_SESSION['pending_checkout_cart'];
                unset($_SESSION['pending_checkout_cart']);
                $toCheckout = isset($_SESSION['return_to_checkout']);
                unset($_SESSION['return_to_checkout']);
                if ($toCheckout) {
                    setFlash('success', 'Account verified! Complete your order below.');
                    redirect(url('checkout'));
                    return;
                }
            }

            setFlash('success', 'Email verified! Welcome to OCSAPP.');
            redirect(url('home'));

        } catch (\PDOException $e) {
            logger("Email verification error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('verify-email'));
        }
    }

    public function resendVerification(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(url('verify-email'));
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            redirect(url('verify-email'));
            return;
        }

        $pending = $_SESSION['pending_user_verification'] ?? null;
        if (!$pending) {
            redirect(url('register'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $verificationCode    = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            $db->prepare("
                UPDATE users SET
                    email_verification_code = ?,
                    email_verification_expires_at = ?,
                    email_verification_attempts = 0
                WHERE id = ? AND status = 'unverified'
            ")->execute([$verificationCode, $verificationExpires, $pending['user_id']]);

            $_SESSION['verification_attempts'] = 0;

            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $appUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/');
                \App\Helpers\EmailHelper::sendUserVerificationCode([
                    'first_name'        => $pending['first_name'],
                    'email'             => $pending['email'],
                    'verification_code' => $verificationCode,
                    'verify_url_fr'     => $appUrl . '/verify-email?lang=fr',
                    'verify_url_en'     => $appUrl . '/verify-email?lang=en',
                    'magic_link_url_fr' => $appUrl . '/verify-email/auto?uid=' . $pending['user_id'] . '&code=' . urlencode($verificationCode) . '&lang=fr',
                    'magic_link_url_en' => $appUrl . '/verify-email/auto?uid=' . $pending['user_id'] . '&code=' . urlencode($verificationCode) . '&lang=en',
                ]);
            } catch (\Exception $e) {
                logger("Failed to resend verification code: " . $e->getMessage(), 'warning');
            }

            setFlash('success', 'A new code has been sent to your email.');
            redirect(url('verify-email'));

        } catch (\PDOException $e) {
            logger("Resend verification error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('verify-email'));
        }
    }

    public function autoVerifyEmail(): void
    {
        $uid  = (int) ($_GET['uid'] ?? 0);
        $code = preg_replace('/\D/', '', $_GET['code'] ?? '');
        $lang = in_array($_GET['lang'] ?? '', ['fr', 'en']) ? ($_GET['lang']) : 'fr';
        $fr   = ($lang === 'fr');

        if (!$uid || strlen($code) !== 6) {
            setFlash('error', $fr ? 'Lien de vérification invalide.' : 'Invalid verification link.');
            redirect(url('register'));
            return;
        }

        try {
            $db = \Database::getConnection();

            $stmt = $db->prepare("
                SELECT id, email, first_name, last_name, role, status,
                       email_verified_at, email_verification_code, email_verification_expires_at
                FROM users WHERE id = ?
            ");
            $stmt->execute([$uid]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                setFlash('error', $fr ? 'Compte introuvable. Veuillez vous inscrire à nouveau.' : 'Account not found. Please register again.');
                redirect(url('register'));
                return;
            }

            // Already verified — friendly message
            if (!empty($user['email_verified_at'])) {
                setFlash('info', $fr
                    ? 'Votre courriel est déjà vérifié. Connectez-vous pour accéder à votre compte.'
                    : 'Your email is already verified. Please log in to access your account.'
                );
                redirect(url('login'));
                return;
            }

            // Validate code
            if (empty($user['email_verification_code']) || empty($user['email_verification_expires_at'])) {
                setFlash('error', $fr
                    ? 'Ce lien n\'est plus valide. Entrez votre code manuellement ou demandez-en un nouveau.'
                    : 'This link is no longer valid. Enter your code manually or request a new one.'
                );
                redirect(url('verify-email'));
                return;
            }

            if (new \DateTime() > new \DateTime($user['email_verification_expires_at'])) {
                setFlash('error', $fr
                    ? 'Ce lien a expiré. Entrez votre code manuellement ou demandez-en un nouveau.'
                    : 'This link has expired. Enter your code manually or request a new one.'
                );
                redirect(url('verify-email'));
                return;
            }

            if (!hash_equals($user['email_verification_code'], $code)) {
                setFlash('error', $fr
                    ? 'Lien invalide. Entrez votre code manuellement.'
                    : 'Invalid link. Please enter your code manually.'
                );
                redirect(url('verify-email'));
                return;
            }

            // Code valid — buyers go active, sellers go pending
            $role        = $user['role'];
            $finalStatus = ($role === 'seller') ? 'pending' : 'active';

            $db->prepare("
                UPDATE users SET
                    status = ?,
                    email_verified_at = NOW(),
                    email_verification_code = NULL,
                    email_verification_expires_at = NULL,
                    email_verification_attempts = 0
                WHERE id = ?
            ")->execute([$finalStatus, $uid]);

            $userArr = [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'role'       => $role,
                'phone'      => '',
            ];

            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                if ($role === 'seller') {
                    \App\Helpers\EmailHelper::sendSellerApplicationReceived($userArr);
                    \App\Helpers\EmailHelper::sendAdminSellerNotification($userArr);
                    \App\Helpers\NotificationHelper::sellerApplication($userArr);
                } else {
                    \App\Helpers\EmailHelper::sendBuyerWelcome($userArr);
                }
            } catch (\Exception $e) {
                logger("Failed to send post-verification email: " . $e->getMessage(), 'warning');
            }

            unset($_SESSION['pending_user_verification'], $_SESSION['verification_attempts']);
            $this->logAudit($uid, 'user.email_verified');

            if ($role === 'seller') {
                setFlash('success', $fr
                    ? 'Courriel vérifié ! Votre demande de vendeur est en cours d\'examen. Nous vous contacterons dans 1 à 2 jours ouvrables.'
                    : 'Email verified! Your seller application is under review. We\'ll be in touch within 1-2 business days.'
                );
                redirect(url('login'));
                return;
            }

            // Buyer: auto-login
            $_SESSION['user'] = [
                'id'         => $user['id'],
                'email'      => $user['email'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'role'       => $role,
                'avatar'     => null,
                'status'     => 'active',
            ];
            $_SESSION['language'] = $lang;

            setFlash('success', $fr
                ? 'Courriel vérifié ! Bienvenue sur OCSAPP.'
                : 'Email verified! Welcome to OCSAPP.'
            );
            redirect(url('home'));

        } catch (\PDOException $e) {
            logger("Auto email verification error: " . $e->getMessage(), 'error');
            setFlash('error', $fr ? 'Une erreur est survenue. Veuillez réessayer.' : 'An error occurred. Please try again.');
            redirect(url('verify-email'));
        }
    }

    public function logout(): void {
        // CSRF check only for POST requests (GET logout is used by driver portal)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
                setFlash('error', 'Invalid security token.');
                redirect(url('/'));
                return;
            }
        }

        if (isLoggedIn()) {
            $lang = $_SESSION['language'] ?? null;
            $userId = userId();
            $this->logAudit($userId, 'user.logout');
            require_once BASE_PATH . '/app/Helpers/RememberMeHelper.php';
            \App\Helpers\RememberMeHelper::clearUserToken($userId);
            session_unset();
            session_destroy();
            // Start a clean session so the flash persists to the login page
            session_start();
            session_regenerate_id(true);
            if ($lang) $_SESSION['language'] = $lang;
        }

        $logoutLang = $lang ?? ($_SESSION['language'] ?? 'fr');
        $msg = $logoutLang === 'fr' ? 'Vous avez été déconnecté avec succès.' : 'You have been logged out successfully.';
        setFlash('success', $msg);
        redirect(url('login'));
    }

    // ─── Password Reset ───────────────────────────────────────────────────────

    /** Show forgot-password form (GET) */
    public function showForgotPassword(): void
    {
        if (isLoggedIn()) { redirect(url('/')); return; }
        view('auth.forgot-password', ['pageTitle' => 'Forgot Password']);
    }

    /** Process forgot-password form — send reset email (POST) */
    public function forgotPassword(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back(); return;
        }

        $email  = trim(post('email', ''));
        $portal = sanitize(get('portal', ''));
        $lang   = $_SESSION['language'] ?? 'fr';
        $portalSuffix = $portal ? '?portal=' . urlencode($portal) : '';

        // Always show the same message to prevent email enumeration
        $ok = $lang === 'fr'
            ? 'Si un compte existe avec ce courriel, un lien de réinitialisation a été envoyé.'
            : 'If an account exists with that email, a password reset link has been sent.';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', $lang === 'fr' ? 'Veuillez entrer une adresse courriel valide.' : 'Please enter a valid email address.');
            back(); return;
        }

        try {
            $stmt = $this->db->prepare("SELECT id, first_name FROM users WHERE email = ? AND status = 'active' LIMIT 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                setFlash('success', $ok);
                redirect(url('forgot-password' . $portalSuffix)); return;
            }

            // Rate limit: max 3 requests per hour
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM password_resets
                WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) AND used_at IS NULL
            ");
            $stmt->execute([$email]);
            if ((int)$stmt->fetchColumn() >= 3) {
                $errMsg = $lang === 'fr'
                    ? 'Trop de demandes. Veuillez réessayer dans une heure.'
                    : 'Too many reset requests. Please try again in an hour.';
                setFlash('error', $errMsg);
                redirect(url('forgot-password' . $portalSuffix)); return;
            }

            $token     = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);          // store hash, never the raw token
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->db->prepare("
                INSERT INTO password_resets (user_id, email, token, expires_at)
                VALUES (?, ?, ?, ?)
            ")->execute([$user['id'], $email, $tokenHash, $expiresAt]);

            $resetUrl = rtrim(env('APP_URL', 'https://ocsapp.ca'), '/') . '/reset-password?token=' . $token
                      . ($portal ? '&portal=' . urlencode($portal) : '');

            $subject = $lang === 'fr' ? 'Réinitialisation de votre mot de passe OCSAPP' : 'Reset your OCSAPP password';

            \App\Helpers\EmailHelper::sendTemplate(
                $email,
                'password-reset',
                [
                    'notification_title' => $user['first_name'],
                    'action_url'         => $resetUrl,
                    'subject'            => $subject,
                ]
            );

            logger("Password reset requested: {$email}", 'info');
            setFlash('success', $ok);
            redirect(url('forgot-password' . $portalSuffix));

        } catch (\Exception $e) {
            logger("Forgot password error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    /** Show reset-password form (GET /reset-password?token=...) */
    public function showResetPassword(): void
    {
        $token = sanitize(get('token', ''));
        if (empty($token)) {
            setFlash('error', 'Invalid or missing reset link.');
            redirect(url('forgot-password')); return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT pr.*, u.email FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([hash('sha256', $token)]);
            $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$reset) {
                setFlash('error', 'This reset link is invalid or has expired. Please request a new one.');
                redirect(url('forgot-password')); return;
            }

            view('auth.reset-password', [
                'pageTitle' => 'Reset Password',
                'token'     => $token,
                'email'     => $reset['email'],
                'portal'    => sanitize(get('portal', '')),
            ]);

        } catch (\Exception $e) {
            logger("Show reset password error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('forgot-password'));
        }
    }

    /** Process reset-password form (POST) */
    public function resetPassword(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back(); return;
        }

        $token    = post('token', '');
        $portal   = sanitize(post('portal', ''));
        $password = post('password', '');
        $confirm  = post('password_confirmation', '');

        if (empty($token)) {
            setFlash('error', 'Invalid reset token.');
            redirect(url('forgot-password')); return;
        }
        $pwErrors = validatePasswordStrength($password);
        if (!empty($pwErrors)) {
            setFlash('error', implode(' ', $pwErrors));
            back(); return;
        }
        if ($password !== $confirm) {
            setFlash('error', 'Passwords do not match.');
            back(); return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT pr.*, u.email FROM password_resets pr
                JOIN users u ON pr.user_id = u.id
                WHERE pr.token = ? AND pr.expires_at > NOW() AND pr.used_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([hash('sha256', $token)]);
            $reset = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$reset) {
                setFlash('error', 'This reset link is invalid or has expired. Please request a new one.');
                redirect(url('forgot-password')); return;
            }

            $this->db->beginTransaction();

            $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?")
                ->execute([password_hash($password, PASSWORD_DEFAULT), $reset['user_id']]);

            $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE id = ?")
                ->execute([$reset['id']]);

            // Invalidate all other unused tokens for this user
            $this->db->prepare("UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL AND id != ?")
                ->execute([$reset['user_id'], $reset['id']]);

            $this->db->commit();

            logger("Password reset completed: {$reset['email']}", 'info');
            $lang = $_SESSION['language'] ?? 'fr';
            $msg  = $lang === 'fr'
                ? 'Votre mot de passe a été réinitialisé. Veuillez vous connecter avec votre nouveau mot de passe.'
                : 'Your password has been reset. Please log in with your new password.';
            setFlash('success', $msg);
            redirect(url($portal === 'distribution' ? 'distribution/login' : 'login'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            logger("Reset password error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            back();
        }
    }

    private function logLogin(int $userId, string $loginType = 'buyer', string $status = 'success'): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO login_logs (user_id, ip_address, user_agent, login_type, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                $loginType,
                $status
            ]);
        } catch (\PDOException $e) {
            logger("Login log error: " . $e->getMessage(), 'error');
        }
    }

    private function logAudit(int $userId, string $action): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO audit_logs (user_id, action, ip_address, user_agent)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (\PDOException $e) {
            logger("Audit log error: " . $e->getMessage(), 'error');
        }
    }
}