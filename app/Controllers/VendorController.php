<?php

namespace App\Controllers;

use PDO;

class VendorController {

    private $db;

    public function __construct() {
        $this->db = \Database::getConnection();
    }

    /**
     * Vendor Central Landing Page (public)
     */
    public function vendorCentral(): void {
        // Check if vendor is logged in
        $isLoggedIn = isset($_SESSION['vendor_id']);

        if ($isLoggedIn) {
            // Redirect to dashboard if already logged in
            redirect(url('vendor/dashboard'));
            return;
        }

        // Show vendor central landing page
        view('vendor.vendor-central', [
            'pageTitle' => 'Vendor Central - OCSAPP'
        ]);
    }

    /**
     * Vendor Login Page
     */
    public function login(): void {
        // Already logged in?
        if (isset($_SESSION['vendor_id'])) {
            redirect(url('vendor/dashboard'));
            return;
        }

        view('vendor.login', [
            'pageTitle' => 'Vendor Login - OCSAPP'
        ]);
    }

    /**
     * Process Vendor Login
     */
    public function processLogin(): void {
        if (!isPost()) {
            redirect(url('vendor/login'));
            return;
        }

        // Verify CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Invalid security token');
            redirect(url('vendor/login'));
            return;
        }

        $email = post('email', '');
        $password = post('password', '');
        $remember = post('remember', false);

        // Validation
        if (empty($email) || empty($password)) {
            setFlash('error', 'Email and password are required');
            redirect(url('vendor/login'));
            return;
        }

        try {
            // Find vendor by email
            $stmt = $this->db->prepare("
                SELECT * FROM vendors
                WHERE email = ?
                AND status != 'inactive'
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendor) {
                setFlash('error', 'Invalid email or password');
                logger("Vendor login failed: Email not found - {$email}", 'info');
                redirect(url('vendor/login'));
                return;
            }

            // Check if vendor is approved
            if (!$vendor['is_approved'] || $vendor['status'] === 'pending') {
                setFlash('error', 'Your vendor account is pending approval. Please contact OCSAPP support.');
                logger("Vendor login blocked: Pending approval - {$email}", 'info');
                redirect(url('vendor/login'));
                return;
            }

            // Check if vendor is suspended
            if ($vendor['status'] === 'suspended') {
                setFlash('error', 'Your vendor account has been suspended. Please contact OCSAPP support.');
                logger("Vendor login blocked: Account suspended - {$email}", 'warning');
                redirect(url('vendor/login'));
                return;
            }

            // Verify password
            if (!password_verify($password, $vendor['password_hash'])) {
                setFlash('error', 'Invalid email or password');
                logger("Vendor login failed: Invalid password - {$email}", 'info');
                redirect(url('vendor/login'));
                return;
            }

            // Login successful - Set session
            $_SESSION['vendor_id'] = $vendor['id'];
            $_SESSION['vendor_email'] = $vendor['email'];
            $_SESSION['vendor_name'] = $vendor['company_name'];
            $_SESSION['vendor_role'] = 'vendor';

            // Update last login
            $stmt = $this->db->prepare("
                UPDATE vendors
                SET last_login_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$vendor['id']]);

            // Remember me functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('vendor_remember', $token, time() + (86400 * 30), '/'); // 30 days

                $stmt = $this->db->prepare("
                    UPDATE vendors
                    SET remember_token = ?
                    WHERE id = ?
                ");
                $stmt->execute([$token, $vendor['id']]);
            }

            logger("Vendor logged in successfully: {$email}", 'info');
            setFlash('success', "Welcome back, {$vendor['company_name']}!");

            redirect(url('vendor/dashboard'));

        } catch (\PDOException $e) {
            logger("Vendor login error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred during login. Please try again.');
            redirect(url('vendor/login'));
        }
    }

    /**
     * Vendor Logout
     */
    public function logout(): void {
        // Clear vendor session
        unset($_SESSION['vendor_id']);
        unset($_SESSION['vendor_email']);
        unset($_SESSION['vendor_name']);
        unset($_SESSION['vendor_role']);

        // Clear remember me cookie
        if (isset($_COOKIE['vendor_remember'])) {
            setcookie('vendor_remember', '', time() - 3600, '/');
        }

        logger("Vendor logged out", 'info');
        setFlash('success', 'You have been logged out successfully');

        redirect(url('vendor/login'));
    }

    /**
     * Vendor Dashboard (requires authentication)
     */
    public function dashboard(): void {
        $this->requireVendorAuth();

        $vendorId = $_SESSION['vendor_id'];

        try {
            // Get vendor details
            $stmt = $this->db->prepare("SELECT * FROM vendors WHERE id = ?");
            $stmt->execute([$vendorId]);
            $vendor = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendor) {
                setFlash('error', 'Vendor account not found');
                $this->logout();
                return;
            }

            // Get vendor statistics
            $stats = $this->getVendorStatistics($vendorId);

            // Get recent orders requiring approval
            $pendingOrders = $this->getPendingOrders($vendorId);

            // Get vendor products
            $products = $this->getVendorProducts($vendorId);

            // Get recent activity
            $recentActivity = $this->getRecentActivity($vendorId);

            view('vendor.dashboard', [
                'vendor' => $vendor,
                'stats' => $stats,
                'pendingOrders' => $pendingOrders,
                'products' => $products,
                'recentActivity' => $recentActivity,
                'pageTitle' => 'Vendor Dashboard - ' . $vendor['company_name']
            ]);

        } catch (\PDOException $e) {
            logger("Vendor dashboard error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading dashboard');
            redirect(url('vendor/login'));
        }
    }

    /**
     * Vendor Orders - View all orders
     */
    public function orders(): void {
        $this->requireVendorAuth();

        $vendorId = $_SESSION['vendor_id'];
        $page = max(1, (int) get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $status = get('status', 'all');

        try {
            // Build query
            $sql = "
                SELECT
                    vo.*,
                    o.order_number,
                    o.customer_name,
                    o.created_at as order_date,
                    o.total as order_total,
                    p.name as product_name
                FROM vendor_orders vo
                INNER JOIN orders o ON vo.order_id = o.id
                LEFT JOIN products p ON vo.order_item_id = (
                    SELECT oi.product_id
                    FROM order_items oi
                    WHERE oi.order_id = o.id
                    LIMIT 1
                )
                WHERE vo.vendor_id = ?
            ";

            $params = [$vendorId];

            if ($status !== 'all') {
                $sql .= " AND vo.status = ?";
                $params[] = $status;
            }

            $sql .= " ORDER BY vo.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $perPage;
            $params[] = $offset;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get total count
            $countSql = "
                SELECT COUNT(*) as count
                FROM vendor_orders
                WHERE vendor_id = ?
            ";
            $countParams = [$vendorId];

            if ($status !== 'all') {
                $countSql .= " AND status = ?";
                $countParams[] = $status;
            }

            $stmt = $this->db->prepare($countSql);
            $stmt->execute($countParams);
            $total = $stmt->fetch()['count'];

            view('vendor.orders', [
                'orders' => $orders,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'status' => $status,
                'pageTitle' => 'Orders - Vendor Dashboard'
            ]);

        } catch (\PDOException $e) {
            logger("Vendor orders error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading orders');
            redirect(url('vendor/dashboard'));
        }
    }

    /**
     * Approve Order
     */
    public function approveOrder(): void {
        $this->requireVendorAuth();

        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $vendorId = $_SESSION['vendor_id'];
        $orderItemId = post('order_item_id', 0);
        $notes = post('notes', '');

        try {
            // Find vendor order
            $stmt = $this->db->prepare("
                SELECT * FROM vendor_orders
                WHERE vendor_id = ? AND id = ?
                LIMIT 1
            ");
            $stmt->execute([$vendorId, $orderItemId]);
            $vendorOrder = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendorOrder) {
                jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
                return;
            }

            // Update status to approved
            $stmt = $this->db->prepare("
                UPDATE vendor_orders
                SET status = 'approved',
                    approved_at = NOW(),
                    vendor_notes = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$notes, $orderItemId]);

            logger("Vendor order approved: Order #{$vendorOrder['order_id']} by Vendor #{$vendorId}", 'info');

            // TODO: Send email notification to admin/customer

            jsonResponse([
                'success' => true,
                'message' => 'Order approved successfully'
            ]);

        } catch (\PDOException $e) {
            logger("Vendor approve order error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error approving order'], 500);
        }
    }

    /**
     * Reject Order
     */
    public function rejectOrder(): void {
        $this->requireVendorAuth();

        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
            return;
        }

        $vendorId = $_SESSION['vendor_id'];
        $orderItemId = post('order_item_id', 0);
        $reason = post('reason', '');

        if (empty($reason)) {
            jsonResponse(['success' => false, 'message' => 'Rejection reason is required'], 400);
            return;
        }

        try {
            // Find vendor order
            $stmt = $this->db->prepare("
                SELECT * FROM vendor_orders
                WHERE vendor_id = ? AND id = ?
                LIMIT 1
            ");
            $stmt->execute([$vendorId, $orderItemId]);
            $vendorOrder = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vendorOrder) {
                jsonResponse(['success' => false, 'message' => 'Order not found'], 404);
                return;
            }

            // Update status to rejected
            $stmt = $this->db->prepare("
                UPDATE vendor_orders
                SET status = 'rejected',
                    rejected_at = NOW(),
                    rejection_reason = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reason, $orderItemId]);

            logger("Vendor order rejected: Order #{$vendorOrder['order_id']} by Vendor #{$vendorId}", 'warning');

            // TODO: Send email notification to admin/customer

            jsonResponse([
                'success' => true,
                'message' => 'Order rejected successfully'
            ]);

        } catch (\PDOException $e) {
            logger("Vendor reject order error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error rejecting order'], 500);
        }
    }

    /**
     * Get Vendor Statistics
     */
    private function getVendorStatistics(int $vendorId): array {
        // Total products
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM vendor_products WHERE vendor_id = ?
        ");
        $stmt->execute([$vendorId]);
        $totalProducts = $stmt->fetch()['count'];

        // Total orders
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM vendor_orders WHERE vendor_id = ?
        ");
        $stmt->execute([$vendorId]);
        $totalOrders = $stmt->fetch()['count'];

        // Pending orders
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM vendor_orders
            WHERE vendor_id = ? AND status = 'pending'
        ");
        $stmt->execute([$vendorId]);
        $pendingOrders = $stmt->fetch()['count'];

        // Total revenue (approved orders)
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(vendor_cost_total), 0) as total
            FROM vendor_orders
            WHERE vendor_id = ? AND status = 'approved'
        ");
        $stmt->execute([$vendorId]);
        $totalRevenue = $stmt->fetch()['total'];

        return [
            'total_products' => $totalProducts,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'total_revenue' => $totalRevenue
        ];
    }

    /**
     * Get Pending Orders for Vendor
     */
    private function getPendingOrders(int $vendorId, int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT
                vo.*,
                o.order_number,
                o.customer_name,
                o.created_at as order_date
            FROM vendor_orders vo
            INNER JOIN orders o ON vo.order_id = o.id
            WHERE vo.vendor_id = ? AND vo.status = 'pending'
            ORDER BY vo.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$vendorId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Vendor Products
     */
    private function getVendorProducts(int $vendorId, int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT
                vp.*,
                p.name as product_name,
                p.sku,
                p.base_price,
                pi.image_path as image
            FROM vendor_products vp
            INNER JOIN products p ON vp.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE vp.vendor_id = ? AND vp.is_active = 1
            ORDER BY p.name ASC
            LIMIT ?
        ");
        $stmt->execute([$vendorId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get Recent Activity
     */
    private function getRecentActivity(int $vendorId, int $limit = 10): array {
        $stmt = $this->db->prepare("
            SELECT
                vo.id,
                vo.status,
                vo.created_at,
                vo.approved_at,
                vo.rejected_at,
                o.order_number,
                CASE
                    WHEN vo.status = 'approved' THEN 'Order approved'
                    WHEN vo.status = 'rejected' THEN 'Order rejected'
                    WHEN vo.status = 'fulfilled' THEN 'Order fulfilled'
                    ELSE 'New order received'
                END as activity_type
            FROM vendor_orders vo
            INNER JOIN orders o ON vo.order_id = o.id
            WHERE vo.vendor_id = ?
            ORDER BY vo.updated_at DESC
            LIMIT ?
        ");
        $stmt->execute([$vendorId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Show Vendor Application Form
     */
    public function showApplyForm(): void {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['vendor_id'])) {
            redirect(url('vendor/dashboard'));
            return;
        }

        view('vendor/apply', [
            'pageTitle' => 'Apply to Become a Vendor - OCSAPP'
        ]);
    }

    /**
     * Submit Vendor Application
     */
    public function submitApplication(): void {
        if (!isPost()) {
            redirect(url('vendor/apply'));
            return;
        }

        // Verify CSRF
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            setFlash('error', 'Invalid security token');
            redirect(url('vendor/apply'));
            return;
        }

        // Get form data
        $companyName = sanitize(post('company_name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        $contactPerson = sanitize(post('contact_person', ''));
        $businessNumber = sanitize(post('business_number', ''));
        $taxId = sanitize(post('tax_id', ''));
        $website = sanitize(post('website', ''));
        $description = sanitize(post('description', ''));
        $address = sanitize(post('address', ''));
        $city = sanitize(post('city', ''));
        $province = sanitize(post('province', ''));
        $postalCode = sanitize(post('postal_code', ''));
        $password = post('password', '');
        $passwordConfirm = post('password_confirm', '');
        $acceptTerms = post('accept_terms', false);
        $inviteCode = sanitize(post('invite_code', ''));

        // Validation
        if (empty($companyName) || empty($email) || empty($phone) || empty($contactPerson)) {
            setFlash('error', 'Please fill in all required fields');
            redirect(url('vendor/apply'));
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Invalid email address');
            redirect(url('vendor/apply'));
            return;
        }

        if (strlen($password) < 8) {
            setFlash('error', 'Password must be at least 8 characters');
            redirect(url('vendor/apply'));
            return;
        }

        if ($password !== $passwordConfirm) {
            setFlash('error', 'Passwords do not match');
            redirect(url('vendor/apply'));
            return;
        }

        if (!$acceptTerms) {
            setFlash('error', 'You must accept the Terms of Service');
            redirect(url('vendor/apply'));
            return;
        }

        try {
            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM vendors WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                setFlash('error', 'Email address already registered');
                redirect(url('vendor/apply'));
                return;
            }

            // Check invite code if provided
            $isPreApproved = false;
            $inviteId = null;

            if (!empty($inviteCode)) {
                $stmt = $this->db->prepare("
                    SELECT * FROM vendor_invites
                    WHERE code = ?
                    AND (expires_at IS NULL OR expires_at > NOW())
                    AND uses_count < max_uses
                    LIMIT 1
                ");
                $stmt->execute([$inviteCode]);
                $invite = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($invite) {
                    $isPreApproved = true;
                    $inviteId = $invite['id'];
                } else {
                    setFlash('warning', 'Invalid or expired invite code. Your application will require admin approval.');
                }
            }

            // Generate slug
            $slug = generateSlug($companyName);
            $originalSlug = $slug;
            $counter = 1;
            while (true) {
                $stmt = $this->db->prepare("SELECT id FROM vendors WHERE slug = ?");
                $stmt->execute([$slug]);
                if (!$stmt->fetch()) break;
                $slug = $originalSlug . '-' . $counter++;
            }

            // Hash password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Determine status based on invite
            $status = $isPreApproved ? 'active' : 'pending';
            $isApproved = $isPreApproved ? 1 : 0;

            // Insert vendor
            $stmt = $this->db->prepare("
                INSERT INTO vendors (
                    company_name, slug, email, phone, contact_person,
                    business_number, tax_id, website, description,
                    address, city, province, postal_code, country,
                    password_hash, status, is_approved,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $companyName, $slug, $email, $phone, $contactPerson,
                $businessNumber, $taxId, $website, $description,
                $address, $city, $province, $postalCode,
                $passwordHash, $status, $isApproved
            ]);

            $vendorId = $this->db->lastInsertId();

            // Update invite if used
            if ($inviteId) {
                $stmt = $this->db->prepare("
                    UPDATE vendor_invites
                    SET uses_count = uses_count + 1,
                        used_by_vendor_id = ?,
                        used_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$vendorId, $inviteId]);
            }

            // Send emails
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $emailHelper = new \EmailHelper();

                if ($isPreApproved) {
                    // Send welcome email for pre-approved vendor
                    $emailHelper->sendEmail(
                        $email,
                        'vendor-approved',
                        [
                            'company_name' => $companyName,
                            'login_url' => url('vendor/login'),
                            'dashboard_url' => url('vendor/dashboard'),
                            'current_year' => date('Y')
                        ],
                        "Welcome to OCSAPP Vendor Program!"
                    );
                } else {
                    // Send application received email
                    $emailHelper->sendEmail(
                        $email,
                        'vendor-application-received',
                        [
                            'company_name' => $companyName,
                            'current_year' => date('Y')
                        ],
                        "Vendor Application Received - OCSAPP"
                    );

                    // Notify admin
                    $adminEmail = env('ADMIN_EMAIL', 'admin@ocsapp.ca');
                    $emailHelper->sendEmail(
                        $adminEmail,
                        'admin-new-vendor-application',
                        [
                            'company_name' => $companyName,
                            'email' => $email,
                            'contact_person' => $contactPerson,
                            'review_url' => url('admin/vendors/view?id=' . $vendorId),
                            'current_year' => date('Y')
                        ],
                        "New Vendor Application: {$companyName}"
                    );
                }
            } catch (\Exception $e) {
                logger("Failed to send vendor application emails: " . $e->getMessage(), 'warning');
            }

            logger("Vendor application submitted: {$email} - " . ($isPreApproved ? 'Pre-approved' : 'Pending'), 'info');

            if ($isPreApproved) {
                setFlash('success', 'Application approved! You can now login to your vendor dashboard.');
                redirect(url('vendor/login'));
            } else {
                setFlash('success', 'Application submitted successfully! We will review it and contact you soon.');
                redirect(url('vendor-central'));
            }

        } catch (\PDOException $e) {
            logger("Vendor application error: " . $e->getMessage(), 'error');
            setFlash('error', 'An error occurred. Please try again.');
            redirect(url('vendor/apply'));
        }
    }

    /**
     * Require vendor authentication
     */
    private function requireVendorAuth(): void {
        if (!isset($_SESSION['vendor_id'])) {
            setFlash('error', 'Please login to access vendor dashboard');
            redirect(url('vendor/login'));
            exit;
        }
    }
}
