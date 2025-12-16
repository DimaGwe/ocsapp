<?php

namespace App\Controllers;

/**
 * AdminVendorController - Vendor Management for Admin Panel
 * Handles CRUD operations, approvals, invites, and vendor statistics
 */
class AdminVendorController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is logged in as admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            redirect('login');
            exit;
        }
    }

    /**
     * List all vendors with filters
     */
    public function index(): void
    {
        $status = get('status', 'all');
        $search = get('search', '');
        $page = (int) get('page', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        // Build query
        $where = [];
        $params = [];

        if ($status !== 'all') {
            $where[] = "status = ?";
            $params[] = $status;
        }

        if (!empty($search)) {
            $where[] = "(company_name LIKE ? OR email LIKE ? OR contact_person LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get vendors
        $sql = "SELECT * FROM vendors {$whereClause} ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $vendors = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM vendors {$whereClause}";
        $countParams = array_slice($params, 0, -2); // Remove limit and offset
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($countParams);
        $totalVendors = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];

        // Get status counts
        $statusCounts = [];
        $statusStmt = $this->db->query("SELECT status, COUNT(*) as count FROM vendors GROUP BY status");
        while ($row = $statusStmt->fetch(\PDO::FETCH_ASSOC)) {
            $statusCounts[$row['status']] = $row['count'];
        }

        view('admin/vendors/index', [
            'pageTitle' => 'Vendors',
            'currentPage' => 'vendors',
            'vendors' => $vendors,
            'statusCounts' => $statusCounts,
            'currentStatus' => $status,
            'search' => $search,
            'page' => $page,
            'totalPages' => ceil($totalVendors / $limit),
            'totalVendors' => $totalVendors
        ]);
    }

    /**
     * View vendor details and statistics
     */
    public function view(): void
    {
        $id = (int) get('id');

        // Get vendor
        $stmt = $this->db->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$id]);
        $vendor = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$vendor) {
            setFlash('error', 'Vendor not found');
            redirect('admin/vendors');
            return;
        }

        // Get vendor products
        $stmt = $this->db->prepare("
            SELECT vp.*, p.name, p.slug, p.image, p.base_price
            FROM vendor_products vp
            INNER JOIN products p ON vp.product_id = p.id
            WHERE vp.vendor_id = ?
            ORDER BY vp.created_at DESC
            LIMIT 50
        ");
        $stmt->execute([$id]);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get vendor orders
        $stmt = $this->db->prepare("
            SELECT vo.*, o.order_number, o.created_at as order_date
            FROM vendor_orders vo
            INNER JOIN orders o ON vo.order_id = o.id
            WHERE vo.vendor_id = ?
            ORDER BY vo.created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$id]);
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get statistics
        $stmt = $this->db->prepare("
            SELECT
                COUNT(DISTINCT vp.product_id) as total_products,
                COUNT(DISTINCT vo.id) as total_orders,
                COUNT(DISTINCT CASE WHEN vo.status = 'pending' THEN vo.id END) as pending_orders,
                SUM(CASE WHEN vo.status = 'approved' THEN vo.vendor_cost_total ELSE 0 END) as total_revenue
            FROM vendors v
            LEFT JOIN vendor_products vp ON v.id = vp.vendor_id AND vp.is_active = 1
            LEFT JOIN vendor_orders vo ON v.id = vo.vendor_id
            WHERE v.id = ?
        ");
        $stmt->execute([$id]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);

        view('admin/vendors/view', [
            'pageTitle' => $vendor['company_name'],
            'currentPage' => 'vendors',
            'vendor' => $vendor,
            'products' => $products,
            'orders' => $orders,
            'stats' => $stats
        ]);
    }

    /**
     * Show create vendor form
     */
    public function create(): void
    {
        view('admin/vendors/create', [
            'pageTitle' => 'Create Vendor',
            'currentPage' => 'vendors'
        ]);
    }

    /**
     * Store new vendor (admin creates directly)
     */
    public function store(): void
    {
        // Validate CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $companyName = sanitize(post('company_name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        $contactPerson = sanitize(post('contact_person', ''));
        $businessNumber = sanitize(post('business_number', ''));
        $address = sanitize(post('address', ''));
        $city = sanitize(post('city', ''));
        $province = sanitize(post('province', ''));
        $postalCode = sanitize(post('postal_code', ''));
        $status = post('status', 'active');
        $autoApprove = (int) post('auto_approve_orders', 0);

        // Validation
        if (empty($companyName) || empty($email)) {
            setFlash('error', 'Company name and email are required');
            redirect('admin/vendors/create');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Invalid email address');
            redirect('admin/vendors/create');
            return;
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM vendors WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            setFlash('error', 'Email already registered');
            redirect('admin/vendors/create');
            return;
        }

        // Generate slug
        $slug = generateSlug($companyName);

        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (true) {
            $stmt = $this->db->prepare("SELECT id FROM vendors WHERE slug = ?");
            $stmt->execute([$slug]);
            if (!$stmt->fetch()) break;
            $slug = $originalSlug . '-' . $counter++;
        }

        // Generate random password
        $randomPassword = bin2hex(random_bytes(8));
        $passwordHash = password_hash($randomPassword, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO vendors (
                    company_name, slug, email, phone, contact_person,
                    business_number, address, city, province, postal_code,
                    password_hash, status, is_approved, auto_approve_orders,
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, NOW())
            ");

            $stmt->execute([
                $companyName, $slug, $email, $phone, $contactPerson,
                $businessNumber, $address, $city, $province, $postalCode,
                $passwordHash, $status, $autoApprove
            ]);

            $vendorId = $this->db->lastInsertId();

            // Send welcome email with credentials
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $emailHelper = new \EmailHelper();

                $emailHelper->sendEmail(
                    $email,
                    'vendor-welcome-admin',
                    [
                        'company_name' => $companyName,
                        'email' => $email,
                        'password' => $randomPassword,
                        'login_url' => url('vendor/login'),
                        'current_year' => date('Y')
                    ],
                    "Welcome to OCSAPP Vendor Program"
                );
            } catch (\Exception $e) {
                logger("Failed to send vendor welcome email: " . $e->getMessage(), 'warning');
            }

            setFlash('success', "Vendor created successfully! Login credentials sent to {$email}");
            redirect('admin/vendors/view?id=' . $vendorId);

        } catch (\PDOException $e) {
            logger("Error creating vendor: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to create vendor. Please try again.');
            redirect('admin/vendors/create');
        }
    }

    /**
     * Show edit vendor form
     */
    public function edit(): void
    {
        $id = (int) get('id');

        $stmt = $this->db->prepare("SELECT * FROM vendors WHERE id = ?");
        $stmt->execute([$id]);
        $vendor = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$vendor) {
            setFlash('error', 'Vendor not found');
            redirect('admin/vendors');
            return;
        }

        view('admin/vendors/edit', [
            'pageTitle' => 'Edit Vendor',
            'currentPage' => 'vendors',
            'vendor' => $vendor
        ]);
    }

    /**
     * Update vendor
     */
    public function update(): void
    {
        // Validate CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id');
        $companyName = sanitize(post('company_name', ''));
        $email = sanitize(post('email', ''));
        $phone = sanitize(post('phone', ''));
        $contactPerson = sanitize(post('contact_person', ''));
        $businessNumber = sanitize(post('business_number', ''));
        $taxId = sanitize(post('tax_id', ''));
        $address = sanitize(post('address', ''));
        $city = sanitize(post('city', ''));
        $province = sanitize(post('province', ''));
        $postalCode = sanitize(post('postal_code', ''));
        $website = sanitize(post('website', ''));
        $description = sanitize(post('description', ''));
        $status = post('status', 'active');
        $autoApprove = (int) post('auto_approve_orders', 0);

        // Validation
        if (empty($companyName) || empty($email)) {
            setFlash('error', 'Company name and email are required');
            redirect('admin/vendors/edit?id=' . $id);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE vendors SET
                    company_name = ?,
                    email = ?,
                    phone = ?,
                    contact_person = ?,
                    business_number = ?,
                    tax_id = ?,
                    address = ?,
                    city = ?,
                    province = ?,
                    postal_code = ?,
                    website = ?,
                    description = ?,
                    status = ?,
                    auto_approve_orders = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $companyName, $email, $phone, $contactPerson,
                $businessNumber, $taxId, $address, $city, $province, $postalCode,
                $website, $description, $status, $autoApprove, $id
            ]);

            setFlash('success', 'Vendor updated successfully');
            redirect('admin/vendors/view?id=' . $id);

        } catch (\PDOException $e) {
            logger("Error updating vendor: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to update vendor');
            redirect('admin/vendors/edit?id=' . $id);
        }
    }

    /**
     * Approve pending vendor
     */
    public function approve(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id');

        try {
            $stmt = $this->db->prepare("
                UPDATE vendors
                SET status = 'active', is_approved = 1, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            // Get vendor email for notification
            $stmt = $this->db->prepare("SELECT company_name, email FROM vendors WHERE id = ?");
            $stmt->execute([$id]);
            $vendor = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Send approval email
            try {
                require_once __DIR__ . '/../Helpers/EmailHelper.php';
                $emailHelper = new \EmailHelper();

                $emailHelper->sendEmail(
                    $vendor['email'],
                    'vendor-approved',
                    [
                        'company_name' => $vendor['company_name'],
                        'login_url' => url('vendor/login'),
                        'dashboard_url' => url('vendor/dashboard'),
                        'current_year' => date('Y')
                    ],
                    "Your OCSAPP Vendor Account has been Approved!"
                );
            } catch (\Exception $e) {
                logger("Failed to send vendor approval email: " . $e->getMessage(), 'warning');
            }

            jsonResponse(['success' => true, 'message' => 'Vendor approved successfully']);

        } catch (\PDOException $e) {
            logger("Error approving vendor: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to approve vendor']);
        }
    }

    /**
     * Suspend vendor
     */
    public function suspend(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id');

        try {
            $stmt = $this->db->prepare("
                UPDATE vendors
                SET status = 'suspended', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            jsonResponse(['success' => true, 'message' => 'Vendor suspended successfully']);

        } catch (\PDOException $e) {
            logger("Error suspending vendor: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to suspend vendor']);
        }
    }

    /**
     * Activate vendor
     */
    public function activate(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id');

        try {
            $stmt = $this->db->prepare("
                UPDATE vendors
                SET status = 'active', updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$id]);

            jsonResponse(['success' => true, 'message' => 'Vendor activated successfully']);

        } catch (\PDOException $e) {
            logger("Error activating vendor: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to activate vendor']);
        }
    }

    /**
     * Delete vendor
     */
    public function delete(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $id = (int) post('id');

        try {
            // Check if vendor has any products or orders
            $stmt = $this->db->prepare("
                SELECT
                    (SELECT COUNT(*) FROM vendor_products WHERE vendor_id = ?) as product_count,
                    (SELECT COUNT(*) FROM vendor_orders WHERE vendor_id = ?) as order_count
            ");
            $stmt->execute([$id, $id]);
            $counts = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($counts['product_count'] > 0 || $counts['order_count'] > 0) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Cannot delete vendor with existing products or orders. Please suspend instead.'
                ]);
                return;
            }

            $stmt = $this->db->prepare("DELETE FROM vendors WHERE id = ?");
            $stmt->execute([$id]);

            jsonResponse(['success' => true, 'message' => 'Vendor deleted successfully']);

        } catch (\PDOException $e) {
            logger("Error deleting vendor: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to delete vendor']);
        }
    }

    /**
     * Generate vendor invite code
     */
    public function generateInvite(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token'), ''))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $note = sanitize(post('note', ''));
        $expiresInDays = (int) post('expires_in_days', 30);

        // Generate unique invite code
        $code = strtoupper(bin2hex(random_bytes(4))); // 8 character code
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$expiresInDays} days"));

        try {
            $stmt = $this->db->prepare("
                INSERT INTO vendor_invites (
                    code, created_by_admin_id, note, expires_at, created_at
                ) VALUES (?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $code,
                $_SESSION['user']['id'],
                $note,
                $expiresAt
            ]);

            $inviteId = $this->db->lastInsertId();
            $inviteUrl = url('vendor/register?invite=' . $code);

            jsonResponse([
                'success' => true,
                'message' => 'Invite code generated successfully',
                'code' => $code,
                'url' => $inviteUrl,
                'expires_at' => $expiresAt
            ]);

        } catch (\PDOException $e) {
            logger("Error generating invite code: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Failed to generate invite code']);
        }
    }

    /**
     * List all vendor invites
     */
    public function invites(): void
    {
        $stmt = $this->db->query("
            SELECT vi.*, u.name as created_by_name, v.company_name as used_by_vendor
            FROM vendor_invites vi
            LEFT JOIN users u ON vi.created_by_admin_id = u.id
            LEFT JOIN vendors v ON vi.used_by_vendor_id = v.id
            ORDER BY vi.created_at DESC
        ");
        $invites = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin/vendors/invites', [
            'pageTitle' => 'Vendor Invites',
            'currentPage' => 'vendors',
            'invites' => $invites
        ]);
    }
}
