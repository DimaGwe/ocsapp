<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

class AuthController {
    
    public function showLogin(): void {
        AuthMiddleware::guest();
        view('auth.login');
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

            // Check if user is active
            if ($user['status'] !== 'active') {
                setFlash('error', 'Your account is ' . $user['status'] . '. Please contact support.');
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

            // Log audit
            $this->logAudit($user['id'], 'user.login');

            clearOldInput();
            setFlash('success', 'Welcome back, ' . $user['first_name'] . '!');

            // FIXED: Redirect to appropriate dashboard with correct paths
            $role = $user['role_name'] ?? 'buyer';
            
            // If user was trying to checkout, redirect back to checkout
            if (isset($_SESSION['return_to_checkout'])) {
                unset($_SESSION['return_to_checkout']);
                redirect(url('checkout'));
                return;
            }
            
            $dashboards = [
                'admin' => 'admin/dashboard',
                'seller' => 'seller/dashboard',    // FIXED: Points to seller/dashboard
                'buyer' => 'account',              // Points to buyer/account/index.php
                'delivery' => 'delivery/dashboard',
                'advertiser' => 'advertiser/dashboard',
                'affiliate' => 'affiliate/dashboard',
            ];

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

        // Validate email
        if (!validateEmail($data['email'])) {
            setFlash('error', 'Invalid email format');
            setOldInput($data);
            back();
        }

        // Validate password strength
        if (strlen($data['password']) < 8) {
            setFlash('error', 'Password must be at least 8 characters');
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

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Get selected role or default to 'buyer'
            // Check GET parameter first (from auth popup), then POST parameter
            $selectedRole = sanitize($_GET['role'] ?? post('role', 'buyer'));

            // Determine user status based on role
            // Sellers need approval, buyers are active immediately
            $userStatus = ($selectedRole === 'seller') ? 'pending' : 'active';

            // Insert user
            $stmt = $db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, status, email_verified_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'],
                $userStatus
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

            // RESTORE CART if user was checking out as guest
            $shouldRedirectToCheckout = false;
            if (isset($_SESSION['pending_checkout_cart'])) {
                // Store user info in session
                $_SESSION['user'] = [
                    'id' => $userId,
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'role' => $selectedRole,
                    'avatar' => null,
                    'status' => 'active'
                ];
                
                // Restore cart
                $_SESSION['cart'] = $_SESSION['pending_checkout_cart'];
                unset($_SESSION['pending_checkout_cart']);
                
                $shouldRedirectToCheckout = isset($_SESSION['return_to_checkout']);
                unset($_SESSION['return_to_checkout']);
                
                logger("Cart restored for new user: " . $data['email'], 'info');
            }

            // Send welcome email based on role
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';

                $user = [
                    'email' => $data['email'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'phone' => $data['phone'] ?? ''
                ];

                if ($selectedRole === 'seller') {
                    // Seller application received (pending approval)
                    \App\Helpers\EmailHelper::sendSellerApplicationReceived($user);
                    logger("Seller application email sent to {$data['email']}", 'info');

                    // Notify admin about new seller application
                    \App\Helpers\EmailHelper::sendAdminSellerNotification($user);
                    logger("Admin notified of new seller application: {$data['email']}", 'info');
                } else {
                    // Buyer welcome email (account active)
                    \App\Helpers\EmailHelper::sendBuyerWelcome($user);
                    logger("Buyer welcome email sent to {$data['email']}", 'info');
                }
            } catch (\Exception $e) {
                logger("Failed to send welcome email to {$data['email']}: " . $e->getMessage(), 'warning');
            }

            // Log audit
            $this->logAudit($userId, 'user.register');

            clearOldInput();

            // If user was checking out, log them in and redirect to checkout
            if ($shouldRedirectToCheckout) {
                setFlash('success', 'Account created! Complete your order below.');
                redirect(url('checkout'));
                return;
            }

            // Different success messages for buyer vs seller
            if ($selectedRole === 'seller') {
                setFlash('success', 'Seller application received! Check your email. Your account will be reviewed within 1-2 business days.');
            } else {
                setFlash('success', 'Registration successful! You can now sign in to start shopping.');
            }
            redirect(url('login'));

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

    public function logout(): void {
        if (isLoggedIn()) {
            $userId = userId();
            $this->logAudit($userId, 'user.logout');
            
            // Clear session
            session_unset();
            session_destroy();
        }

        setFlash('success', 'You have been logged out');
        redirect(url('login'));
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