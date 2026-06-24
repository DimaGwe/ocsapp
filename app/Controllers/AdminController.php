<?php

namespace App\Controllers;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminController - Main Admin Panel Controller
 * Handles admin dashboard and core admin functionality
 */
class AdminController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();

        // Ensure user is logged in as any admin tier (super_admin, admin, admin_staff)
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
    }

    /**
     * Admin Dashboard - Overview of system statistics
     */
    public function dashboard(): void
    {
        try {
            // Get total users count
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
            $total_users = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

            // Get total sellers count (from user_roles table)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = 'seller'
            ");
            $total_sellers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

            // Get total buyers count (from user_roles table)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = 'buyer'
            ");
            $total_buyers = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

            // Get total delivery staff count (from user_roles table)
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                WHERE r.name = 'delivery'
            ");
            $active_delivery_staff = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

            // Get recent users (last 5) with role from user_roles table
            $stmt = $this->db->query("
                SELECT u.id, u.first_name, u.last_name, u.email, r.name as role, u.status, u.created_at
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                ORDER BY u.created_at DESC
                LIMIT 5
            ");
            $recent_users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get recent login activity
            $recent_logins = [];
            if ($this->tableExists('login_logs')) {
                $stmt = $this->db->query("
                    SELECT ll.*, u.first_name, u.last_name, u.email
                    FROM login_logs ll
                    LEFT JOIN users u ON ll.user_id = u.id
                    ORDER BY ll.created_at DESC
                    LIMIT 10
                ");
                $recent_logins = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // My open tasks (assigned to the logged-in admin)
            $myTasks = [];
            $myTaskCount = 0;
            try {
                $currentUserId = $_SESSION['user']['id'] ?? null;
                if ($currentUserId) {
                    $stmt = $this->db->prepare("
                        SELECT t.id, t.task, t.created_at,
                               CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                        FROM planner_todos t
                        LEFT JOIN users u ON t.user_id = u.id
                        WHERE t.assigned_to = ? AND t.is_completed = 0
                        ORDER BY t.created_at DESC
                        LIMIT 5
                    ");
                    $stmt->execute([$currentUserId]);
                    $myTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                    $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM planner_todos WHERE assigned_to = ? AND is_completed = 0");
                    $stmt->execute([$currentUserId]);
                    $myTaskCount = (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
                }
            } catch (\Exception $e) {
                $myTasks = [];
                $myTaskCount = 0;
            }

            // Pass data to view
            view('admin.dashboard', compact(
                'total_users',
                'total_sellers',
                'total_buyers',
                'active_delivery_staff',
                'recent_users',
                'recent_logins',
                'myTasks',
                'myTaskCount'
            ));

        } catch (\Exception $e) {
            error_log('Admin Dashboard Error: ' . $e->getMessage());

            // Set default values if database error occurs
            $total_users = 0;
            $total_sellers = 0;
            $total_buyers = 0;
            $active_delivery_staff = 0;
            $recent_users = [];
            $recent_logins = [];
            $myTasks = [];
            $myTaskCount = 0;

            view('admin.dashboard', compact(
                'total_users',
                'total_sellers',
                'total_buyers',
                'active_delivery_staff',
                'recent_users',
                'recent_logins',
                'myTasks',
                'myTaskCount'
            ));
        }
    }

    /**
     * List all users
     */
    public function users(): void
    {
        try {
            $roleFilter = get('role', '');
            $statusFilter = get('status', '');
            $search = get('search', '');

            // Get all roles for the filter dropdown (include supplier as virtual role)
            $stmt = $this->db->query("SELECT id, name, display_name FROM roles ORDER BY id");
            $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            // Add supplier as a virtual role for filtering
            $roles[] = ['id' => 0, 'name' => 'supplier', 'display_name' => 'Supplier'];

            $users = [];
            $suppliers = [];

            // Fetch regular users (skip if filtering by supplier role)
            if ($roleFilter !== 'supplier') {
                $where = [];
                $params = [];

                if (!empty($roleFilter)) {
                    $where[] = "u.role = ?";
                    $params[] = $roleFilter;
                }

                if (!empty($statusFilter)) {
                    $where[] = "u.status = ?";
                    $params[] = $statusFilter;
                }

                if (!empty($search)) {
                    $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
                    $searchTerm = "%{$search}%";
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                    $params[] = $searchTerm;
                }

                $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

                $stmt = $this->db->prepare("
                    SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.role, u.department, u.created_at,
                           u.is_test_account,
                           us.last_seen_at, us.current_page, us.device
                    FROM users u
                    LEFT JOIN (
                        SELECT user_id, last_seen_at, current_page, device,
                               ROW_NUMBER() OVER (PARTITION BY user_id ORDER BY last_seen_at DESC) AS rn
                        FROM user_sessions WHERE user_id IS NOT NULL
                    ) us ON us.user_id = u.id AND us.rn = 1
                    {$whereClause}
                    ORDER BY u.created_at DESC
                ");
                $stmt->execute($params);
                $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Fetch suppliers and map to user format (skip if filtering by a non-supplier role)
            if (empty($roleFilter) || $roleFilter === 'supplier') {
                $sWhere = [];
                $sParams = [];

                if (!empty($statusFilter)) {
                    $sWhere[] = "s.status = ?";
                    $sParams[] = $statusFilter;
                }

                if (!empty($search)) {
                    $sWhere[] = "(s.name LIKE ? OR s.company_name LIKE ? OR s.email LIKE ? OR s.contact_person LIKE ?)";
                    $searchTerm = "%{$search}%";
                    $sParams[] = $searchTerm;
                    $sParams[] = $searchTerm;
                    $sParams[] = $searchTerm;
                    $sParams[] = $searchTerm;
                }

                $sWhereClause = !empty($sWhere) ? 'WHERE ' . implode(' AND ', $sWhere) : '';

                $stmt = $this->db->prepare("
                    SELECT s.*, us.last_seen_at, us.current_page, us.device
                    FROM suppliers s
                    LEFT JOIN (
                        SELECT supplier_id, last_seen_at, current_page, device,
                               ROW_NUMBER() OVER (PARTITION BY supplier_id ORDER BY last_seen_at DESC) AS rn
                        FROM user_sessions WHERE supplier_id IS NOT NULL
                    ) us ON us.supplier_id = s.id AND us.rn = 1
                    {$sWhereClause}
                    ORDER BY s.created_at DESC
                ");
                $stmt->execute($sParams);
                $supplierRows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Map supplier fields to user format
                foreach ($supplierRows as $s) {
                    $nameParts = explode(' ', trim($s['contact_person'] ?: $s['name']), 2);
                    $suppliers[] = [
                        'id' => $s['id'],
                        'first_name' => $nameParts[0] ?? $s['name'],
                        'last_name' => $nameParts[1] ?? '',
                        'email' => $s['email'],
                        'phone' => $s['phone'] ?? 'N/A',
                        'status' => $s['status'] ?? 'pending',
                        'role' => 'supplier',
                        'role_display' => 'Supplier',
                        'created_at' => $s['created_at'],
                        'company_name' => $s['company_name'],
                        'is_supplier' => true,
                        'last_seen_at' => $s['last_seen_at'] ?? null,
                        'current_page' => $s['current_page'] ?? null,
                        'device'       => $s['device'] ?? null,
                    ];
                }
            }

            // Merge users and suppliers, sorted by created_at desc
            $allUsers = array_merge($users, $suppliers);
            usort($allUsers, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));

            $total = count($allUsers);
            $activeCount   = count(array_filter($allUsers, fn($u) => $u['status'] === 'active'));
            $sellerCount   = count(array_filter($allUsers, fn($u) => ($u['role'] ?? '') === 'seller'));
            $supplierCount = count(array_filter($allUsers, fn($u) => ($u['role'] ?? '') === 'supplier'));

            $perPage = 20;
            $page = max(1, (int)(get('page', 1)));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page = min($page, max(1, $totalPages));
            $allUsers = array_slice($allUsers, ($page - 1) * $perPage, $perPage);

            view('admin.users.index', compact('allUsers', 'roles', 'roleFilter', 'statusFilter', 'search', 'total', 'page', 'perPage', 'activeCount', 'sellerCount', 'supplierCount'));

        } catch (\Exception $e) {
            error_log('Admin Users Error: ' . $e->getMessage());
            $allUsers = [];
            $roles = [];
            $roleFilter = '';
            $statusFilter = '';
            $search = '';
            $total = 0;
            $activeCount = 0;
            $sellerCount = 0;
            $supplierCount = 0;
            view('admin.users.index', compact('allUsers', 'roles', 'roleFilter', 'statusFilter', 'search', 'total', 'activeCount', 'sellerCount', 'supplierCount'));
        }
    }

    /**
     * Marketplace Buyers — role=buyer only, dedicated view
     */
    public function buyers(): void
    {
        $statusFilter = get('status', '');
        $search       = get('search', '');

        try {
            $where  = ["u.role = 'buyer'"];
            $params = [];

            if (!empty($statusFilter)) { $where[] = "u.status = ?"; $params[] = $statusFilter; }
            if (!empty($search)) {
                $where[] = "(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
                $st = "%{$search}%";
                $params = array_merge($params, [$st, $st, $st]);
            }

            $whereClause = 'WHERE ' . implode(' AND ', $where);
            $stmt = $this->db->prepare("
                SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.created_at,
                       COUNT(o.id) AS order_count
                FROM users u
                LEFT JOIN orders o ON o.user_id = u.id
                {$whereClause}
                GROUP BY u.id
                ORDER BY u.created_at DESC
            ");
            $stmt->execute($params);
            $buyers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $total          = count($buyers);
            $activeCount    = count(array_filter($buyers, fn($b) => $b['status'] === 'active'));
            $suspendedCount = count(array_filter($buyers, fn($b) => $b['status'] === 'suspended'));
            $inactiveCount  = count(array_filter($buyers, fn($b) => $b['status'] === 'inactive'));

            $perPage    = 20;
            $page       = max(1, (int)(get('page', 1)));
            $totalPages = $perPage > 0 ? (int)ceil($total / $perPage) : 1;
            $page       = min($page, max(1, $totalPages));
            $buyers     = array_slice($buyers, ($page - 1) * $perPage, $perPage);

            view('admin.buyers.index', compact('buyers', 'statusFilter', 'search', 'total', 'page', 'perPage', 'activeCount', 'suspendedCount', 'inactiveCount'));

        } catch (\Exception $e) {
            error_log('Admin Buyers Error: ' . $e->getMessage());
            $buyers = []; $statusFilter = ''; $search = '';
            $total = 0; $activeCount = 0; $suspendedCount = 0; $inactiveCount = 0;
            view('admin.buyers.index', compact('buyers', 'statusFilter', 'search', 'total', 'activeCount', 'suspendedCount', 'inactiveCount'));
        }
    }

    /**
     * Store new user (admin-created)
     */
    public function storeUser(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $firstName = sanitize(post('first_name'));
            $lastName = sanitize(post('last_name'));
            $email = sanitize(post('email'));
            $phone = sanitize(post('phone', ''));
            $roleName = post('role');
            $status = post('status', 'active');
            $password = post('password');
            $sendWelcomeEmail = post('send_welcome_email') === 'on';
            $deptRaw = post('department', '');
            $department = in_array($deptRaw, ['management', 'ops', 'finance', 'support', 'logistics', 'tech'])
                ? $deptRaw : null;

            // Validate required fields
            if (empty($firstName) || empty($lastName) || empty($email) || empty($roleName)) {
                setFlash('error', 'Please fill in all required fields');
                back();
                return;
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                setFlash('error', 'Please enter a valid email address');
                back();
                return;
            }

            // Check if email already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                setFlash('error', 'A user with this email address already exists');
                back();
                return;
            }

            // Get role ID
            $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$roleName]);
            $role = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$role) {
                setFlash('error', 'Invalid role selected');
                back();
                return;
            }

            // Generate password if not provided
            $tempPassword = $password;
            if (empty($password)) {
                $tempPassword = $this->generateSecurePassword();
            }

            // Hash the password
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);

            // Start transaction
            $this->db->beginTransaction();

            try {
                // Create user
                $stmt = $this->db->prepare("
                    INSERT INTO users (first_name, last_name, email, phone, department, password, role, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$firstName, $lastName, $email, $phone, $department, $hashedPassword, $roleName, $status]);
                $userId = $this->db->lastInsertId();

                // Assign role in user_roles table
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id, created_at)
                    VALUES (?, ?, NOW())
                ");
                $stmt->execute([$userId, $role['id']]);

                // Commit transaction
                $this->db->commit();

                // Send welcome email if requested
                if ($sendWelcomeEmail) {
                    $userData = [
                        'id' => $userId,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $email,
                        'role' => $roleName,
                        'temp_password' => $tempPassword
                    ];

                    \App\Helpers\EmailHelper::sendAdminCreatedUserWelcome($userData);
                }

                auditLog('user_create', "Created user: {$email} (role: {$roleName}, status: {$status})", (int)$userId);

                // Build success message with temp password for admin reference
                $successMsg = "User {$firstName} {$lastName} created successfully.";
                if ($sendWelcomeEmail) {
                    $successMsg .= " Welcome email sent.";
                }
                $successMsg .= " Temporary password: <code style='background:#f3f4f6;padding:2px 8px;border-radius:4px;font-family:monospace;'>{$tempPassword}</code>";

                setFlash('success', $successMsg);
                redirect(url('admin/users'));

            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            error_log('Store User Error: ' . $e->getMessage());
            setFlash('error', 'Failed to create user: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Generate a secure random password
     * Avoids HTML-problematic characters (& < > " ') to prevent email encoding issues
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        // Use only email-safe special characters (no & < > " ')
        $special = '!@#$%*-_+=';

        // Ensure at least one of each
        $password = $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        // Fill the rest
        $allChars = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Edit user
     */
    public function editUser(): void
    {
        try {
            $userId = get('id');

            if (!$userId) {
                setFlash('error', 'User ID is required');
                redirect(url('admin/users'));
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user) {
                setFlash('error', 'User not found');
                redirect(url('admin/users'));
                return;
            }

            // Get all roles for the dropdown
            $stmt = $this->db->query("SELECT id, name, display_name FROM roles ORDER BY id");
            $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch all assigned roles for this user (user_roles is source of truth)
            $rolesStmt = $this->db->prepare("
                SELECT r.name FROM user_roles ur
                JOIN roles r ON ur.role_id = r.id
                WHERE ur.user_id = ?
            ");
            $rolesStmt->execute([$userId]);
            $userRoles = $rolesStmt->fetchAll(\PDO::FETCH_COLUMN) ?: [];

            // Check if current user is super_admin to determine which roles can be assigned
            $currentUserRole = userRole();
            $canAssignAdminRoles = ($currentUserRole === 'super_admin');

            view('admin.users.edit', compact('user', 'roles', 'userRoles', 'canAssignAdminRoles'));

        } catch (\Exception $e) {
            error_log('Edit User Error: ' . $e->getMessage());
            setFlash('error', 'Failed to load user');
            redirect(url('admin/users'));
        }
    }

    /**
     * Update user
     */
    public function updateUser(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $userId = post('user_id');
            $firstName = sanitize(post('first_name'));
            $lastName = sanitize(post('last_name'));
            $email = sanitize(post('email'));
            $phone = sanitize(post('phone', ''));
            $submittedRoles = post('roles', []);
            $status = post('status');
            $password = post('password');
            $departmentRaw = post('department', '');
            $department = in_array($departmentRaw, ['management', 'ops', 'finance', 'support', 'logistics', 'tech'])
                ? $departmentRaw : null;

            if (!is_array($submittedRoles)) {
                $submittedRoles = [];
            }

            // Security: if not super_admin, preserve any existing admin-tier roles from DB
            // (disabled checkboxes don't POST, so we must add them back)
            $adminTierRoles = ['super_admin', 'admin', 'admin_staff'];
            $currentUserRole = userRole();

            if ($currentUserRole !== 'super_admin') {
                $stmt = $this->db->prepare("
                    SELECT r.name FROM user_roles ur
                    JOIN roles r ON ur.role_id = r.id
                    WHERE ur.user_id = ? AND r.name IN ('super_admin', 'admin', 'admin_staff')
                ");
                $stmt->execute([$userId]);
                $existingAdminRoles = $stmt->fetchAll(\PDO::FETCH_COLUMN);

                // Keep existing admin-tier roles; strip any admin-tier from submitted (can't assign them)
                $nonAdminSubmitted = array_filter($submittedRoles, fn($r) => !in_array($r, $adminTierRoles));
                $submittedRoles = array_merge($existingAdminRoles, array_values($nonAdminSubmitted));
            }

            $submittedRoles = array_unique($submittedRoles);

            if (empty($submittedRoles)) {
                setFlash('error', 'At least one role must be selected');
                back();
                return;
            }

            // Determine primary role by priority for users.role display cache
            $rolePriority = [
                'super_admin' => 1, 'admin' => 2, 'admin_staff' => 3,
                'seller' => 4, 'business' => 5, 'delivery' => 6, 'buyer' => 7,
            ];
            usort($submittedRoles, fn($a, $b) => ($rolePriority[$a] ?? 99) - ($rolePriority[$b] ?? 99));
            $primaryRole = $submittedRoles[0];

            // Build update query
            if (!empty($password)) {
                $stmt = $this->db->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, department = ?, role = ?, status = ?, password = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$firstName, $lastName, $email, $phone, $department, $primaryRole, $status, $hashedPassword, $userId]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, department = ?, role = ?, status = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$firstName, $lastName, $email, $phone, $department, $primaryRole, $status, $userId]);
            }

            // Sync user_roles: delete all existing, re-insert selected roles
            $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);

            $insertStmt = $this->db->prepare("
                INSERT IGNORE INTO user_roles (user_id, role_id, created_at)
                SELECT ?, id, NOW() FROM roles WHERE name = ?
            ");
            foreach ($submittedRoles as $roleName) {
                $insertStmt->execute([$userId, $roleName]);
            }

            $rolesStr = implode(', ', $submittedRoles);
            auditLog('user_update', "Updated user: {$email} (roles: {$rolesStr}, status: {$status})" . (!empty($password) ? ' [password changed]' : ''), (int)$userId);

            setFlash('success', 'User updated successfully');
            redirect(url('admin/users'));

        } catch (\Exception $e) {
            error_log('Update User Error: ' . $e->getMessage());
            setFlash('error', 'Failed to update user');
            back();
        }
    }

    /**
     * Change user status (activate/suspend/deactivate)
     */
    public function changeUserStatus(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $userId = post('user_id');
            $newStatus = post('status');

            if (!$userId || !$newStatus) {
                setFlash('error', 'User ID and status are required');
                back();
                return;
            }

            // Validate status
            $validStatuses = ['active', 'inactive', 'suspended', 'pending'];
            if (!in_array($newStatus, $validStatuses)) {
                setFlash('error', 'Invalid status');
                back();
                return;
            }

            // Don't allow changing your own status
            if ($userId == user()['id']) {
                setFlash('error', 'Cannot change your own account status');
                back();
                return;
            }

            // Authorization: only super_admin can modify admin-tier users
            $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetRole = $stmt->fetchColumn();
            $adminRoles = ['super_admin', 'admin', 'admin_staff'];
            if (in_array($targetRole, $adminRoles) && userRole() !== 'super_admin') {
                setFlash('error', 'Only Super Admin can modify administrative users');
                back();
                return;
            }

            // Update user status
            $stmt = $this->db->prepare("
                UPDATE users
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$newStatus, $userId]);

            // Set appropriate success message
            $statusMessages = [
                'active' => 'User activated successfully',
                'suspended' => 'User suspended successfully',
                'inactive' => 'User deactivated successfully',
                'pending' => 'User status set to pending'
            ];

            auditLog('user_status_change', "Changed user status to: {$newStatus} (was: {$targetRole})", (int)$userId);

            // Email driver when their account is deactivated or suspended
            if (in_array($newStatus, ['inactive', 'suspended'])) {
                try {
                    $driverStmt = $this->db->prepare("
                        SELECT u.first_name, u.email
                        FROM users u
                        JOIN user_roles ur ON u.id = ur.user_id
                        JOIN roles r ON ur.role_id = r.id
                        WHERE u.id = ? AND r.name = 'delivery'
                        LIMIT 1
                    ");
                    $driverStmt->execute([$userId]);
                    $driverInfo = $driverStmt->fetch(\PDO::FETCH_ASSOC);
                    if ($driverInfo) {
                        $statusDesc = $newStatus === 'inactive' ? 'deactivated' : 'suspended';
                        \App\Helpers\EmailHelper::send(
                            $driverInfo['email'],
                            'Your OCSAPP Driver Account Has Been ' . ucfirst($statusDesc),
                            "Hi {$driverInfo['first_name']},<br><br>
                            We're writing to let you know that your OCSAPP driver account has been {$statusDesc}.<br><br>
                            If you have questions or believe this was done in error, please contact us at <a href='mailto:info@ocsapp.ca'>info@ocsapp.ca</a>.<br><br>
                            Best,<br>The OCSAPP Team"
                        );
                    }
                } catch (\Exception $e) { /* non-fatal */ }
            }

            setFlash('success', $statusMessages[$newStatus] ?? 'User status updated successfully');
            redirect(url('admin/users'));

        } catch (\Exception $e) {
            error_log('Change User Status Error: ' . $e->getMessage());
            setFlash('error', 'Failed to change user status');
            back();
        }
    }

    /**
     * Delete user and all associated data
     */
    public function deleteUser(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $userId = post('user_id');

            if (!$userId) {
                setFlash('error', 'User ID is required');
                back();
                return;
            }

            // Don't allow deleting yourself
            if ($userId == user()['id']) {
                setFlash('error', 'Cannot delete your own account');
                back();
                return;
            }

            // Authorization: only super_admin can delete admin-tier users
            $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetRole = $stmt->fetchColumn();
            $adminRoles = ['super_admin', 'admin', 'admin_staff'];
            if (in_array($targetRole, $adminRoles) && userRole() !== 'super_admin') {
                setFlash('error', 'Only Super Admin can delete administrative users');
                back();
                return;
            }

            // Check if user has delivery assignments (NO ACTION FK - must be zero before deleting)
            if ($this->tableExists('delivery_assignments')) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM delivery_assignments WHERE driver_id = ?");
                $stmt->execute([$userId]);
                $deliveryCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($deliveryCount > 0) {
                    setFlash('error', "Cannot delete this user. They have {$deliveryCount} delivery assignment(s). Please reassign or complete deliveries first, or change user status to 'inactive' instead.");
                    back();
                    return;
                }
            }

            // Check if user has delivery earnings (NO ACTION FK - must be zero before deleting)
            if ($this->tableExists('delivery_earnings')) {
                $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM delivery_earnings WHERE driver_id = ?");
                $stmt->execute([$userId]);
                $earningsCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($earningsCount > 0) {
                    setFlash('error', "Cannot delete this driver. They have {$earningsCount} earnings record(s) on file. Change their status to 'inactive' instead to preserve financial records.");
                    back();
                    return;
                }
            }

            // Check if user has active orders (buyers)
            if ($this->tableExists('orders')) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count
                    FROM orders
                    WHERE user_id = ? AND status NOT IN ('delivered', 'cancelled', 'refunded')
                ");
                $stmt->execute([$userId]);
                $activeOrders = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($activeOrders > 0) {
                    setFlash('error', "Cannot delete this user. They have {$activeOrders} active order(s). Please complete or cancel orders first, or change user status to 'inactive' instead.");
                    back();
                    return;
                }
            }

            // Check if user owns shops with active inventory (sellers)
            if ($this->tableExists('shops')) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count
                    FROM shops
                    WHERE seller_id = ?
                ");
                $stmt->execute([$userId]);
                $shopCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($shopCount > 0) {
                    setFlash('error', "Cannot delete this user. They own {$shopCount} shop(s). Please delete or reassign shops first, or change user status to 'inactive' instead.");
                    back();
                    return;
                }
            }

            // Fetch user info before deletion for notifications + archive
            $userInfoStmt = $this->db->prepare("SELECT u.first_name, u.last_name, u.email FROM users u WHERE u.id = ?");
            $userInfoStmt->execute([$userId]);
            $userInfo = $userInfoStmt->fetch(\PDO::FETCH_ASSOC);

            $deleteReason   = post('delete_reason', 'other');
            $deleteNotes    = post('delete_notes', '');
            $canRejoin      = post('can_rejoin', '1') === '1' ? 1 : 0;

            $isDriver = false;
            if ($userInfo && $this->tableExists('driver_applications')) {
                $driverCheck = $this->db->prepare("SELECT id FROM driver_applications WHERE user_id = ? LIMIT 1");
                $driverCheck->execute([$userId]);
                $isDriver = (bool) $driverCheck->fetchColumn();
            }

            // Start transaction for data integrity
            $this->db->beginTransaction();

            try {
                // Clean up analytics/tracking data (no foreign key constraints)
                // These won't cascade automatically, so we delete them manually
                // Wrap each in try-catch in case table/column doesn't exist

                if ($this->tableExists('product_views')) {
                    try {
                        $this->db->prepare("DELETE FROM product_views WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping product_views cleanup: " . $e->getMessage());
                    }
                }

                if ($this->tableExists('search_logs')) {
                    try {
                        $this->db->prepare("DELETE FROM search_logs WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping search_logs cleanup: " . $e->getMessage());
                    }
                }

                if ($this->tableExists('visitor_logs')) {
                    try {
                        $this->db->prepare("DELETE FROM visitor_logs WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping visitor_logs cleanup: " . $e->getMessage());
                    }
                }

                if ($this->tableExists('visitor_sessions')) {
                    try {
                        $this->db->prepare("DELETE FROM visitor_sessions WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping visitor_sessions cleanup: " . $e->getMessage());
                    }
                }

                if ($this->tableExists('visitor_analytics')) {
                    try {
                        $this->db->prepare("DELETE FROM visitor_analytics WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping visitor_analytics cleanup: " . $e->getMessage());
                    }
                }

                // Clean up driver applications so email can be reused after deletion
                if ($this->tableExists('driver_applications')) {
                    try {
                        $this->db->prepare("DELETE FROM driver_applications WHERE user_id = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Skipping driver_applications cleanup: " . $e->getMessage());
                    }
                }

                // Archive to deleted_users before hard delete
                $this->db->prepare("
                    INSERT INTO deleted_users
                        (original_id, email, first_name, last_name, role, reason, notes, deleted_by, can_rejoin)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $userId,
                    $userInfo['email']      ?? '',
                    $userInfo['first_name'] ?? '',
                    $userInfo['last_name']  ?? '',
                    $targetRole             ?? 'user',
                    $deleteReason,
                    $deleteNotes,
                    user()['id'],
                    $canRejoin,
                ]);

                // Delete the user (this will CASCADE to:
                // - addresses, orders, reviews, sessions, shop_reviews, shops, user_roles)
                // - audit_logs will SET NULL (keeping audit trail)
                // - products will SET NULL (products remain but without seller)
                $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);

                // Commit transaction
                $this->db->commit();

                auditLog('user_delete', "Deleted user ID {$userId} (role: {$targetRole})", (int)$userId);

                // Notify driver by email
                if ($isDriver && !empty($userInfo['email'])) {
                    try {
                        \App\Helpers\EmailHelper::sendDriverAccountRemoved([
                            'first_name' => $userInfo['first_name'],
                            'email'      => $userInfo['email'],
                        ]);
                    } catch (\Exception $e) { /* non-fatal */ }
                }

                // Notify buyer by email
                if ($targetRole === 'buyer' && !empty($userInfo['email'])) {
                    try {
                        \App\Helpers\EmailHelper::sendBuyerAccountRemoved([
                            'first_name' => $userInfo['first_name'],
                            'email'      => $userInfo['email'],
                        ]);
                    } catch (\Exception $e) { /* non-fatal */ }
                }

                // Notify seller by email
                if ($targetRole === 'seller' && !empty($userInfo['email'])) {
                    try {
                        \App\Helpers\EmailHelper::sendSellerAccountRemoved([
                            'first_name' => $userInfo['first_name'],
                            'email'      => $userInfo['email'],
                        ]);
                    } catch (\Exception $e) { /* non-fatal */ }
                }

                // Admin bell notification
                try {
                    $roleLabel    = ucfirst(str_replace('_', ' ', $targetRole ?? 'user'));
                    $deletedEmail = htmlspecialchars($userInfo['email'] ?? "ID {$userId}");
                    $this->db->prepare("
                        INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                        VALUES ('account_removed', ?, ?, ?, 'trash', 'normal', NOW())
                    ")->execute([
                        "{$roleLabel} Account Deleted",
                        "{$roleLabel} account ({$deletedEmail}) was deleted by admin.",
                        '/admin/users',
                    ]);
                } catch (\Exception $e) {
                    error_log('Failed to insert admin notification for user deletion: ' . $e->getMessage());
                }

                setFlash('success', 'User and all associated data deleted successfully');
                redirect(url('admin/users'));

            } catch (\Exception $e) {
                // Rollback on error
                $this->db->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            error_log('Delete User Error: ' . $e->getMessage());
            setFlash('error', 'Failed to delete user: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Change supplier status (soft disable/enable)
     */
    public function changeSupplierStatus(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        try {
            $supplierId = (int) post('supplier_id');
            $newStatus = post('status');

            if (!$supplierId || !$newStatus) {
                setFlash('error', 'Supplier ID and status are required');
                back();
                return;
            }

            $validStatuses = ['active', 'inactive', 'suspended', 'pending_verification'];
            if (!in_array($newStatus, $validStatuses)) {
                setFlash('error', 'Invalid status');
                back();
                return;
            }

            // Get supplier info
            $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$supplier) {
                setFlash('error', 'Supplier not found');
                back();
                return;
            }

            // Update status and login ability
            $canLogin = in_array($newStatus, ['active', 'pending_verification']) ? 1 : 0;
            $stmt = $this->db->prepare("UPDATE suppliers SET status = ?, can_login = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$newStatus, $canLogin, $supplierId]);

            supplierAuditLog($supplierId, 'status_changed', "Status changed from {$supplier['status']} to {$newStatus}");

            // Send email notification
            try {
                $subject = ($newStatus === 'active')
                    ? 'Your OCSAPP Supplier Account Has Been Reactivated'
                    : 'Your OCSAPP Supplier Account Has Been Disabled';

                $message = ($newStatus === 'active')
                    ? "Hello {$supplier['name']},\n\nYour supplier account at OCSAPP has been reactivated. You can now log in and manage your products.\n\nLog in at: " . url('supplier/login')
                    : "Hello {$supplier['name']},\n\nYour supplier account at OCSAPP has been disabled. You will no longer be able to log in.\n\nIf you believe this is an error, please contact us at info@ocsapp.ca";

                if (!empty($supplier['email'])) {
                    \App\Helpers\EmailHelper::send(
                        $supplier['email'],
                        $subject,
                        nl2br($message)
                    );
                }
            } catch (\Exception $emailErr) {
                logger("Failed to send supplier status email: " . $emailErr->getMessage(), 'error');
            }

            $statusLabel = ($newStatus === 'active') ? 'activated' : 'disabled';
            auditLog('supplier_status_change', "Supplier {$supplier['name']} (ID: {$supplierId}) {$statusLabel}", $supplierId);
            setFlash('success', "Supplier account {$statusLabel} successfully");
            redirect(url('admin/users'));

        } catch (\Exception $e) {
            error_log('Change Supplier Status Error: ' . $e->getMessage());
            setFlash('error', 'Failed to change supplier status');
            back();
        }
    }

    /**
     * Permanently delete supplier and all associated data
     */
    public function deleteSupplier(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        try {
            $supplierId = (int) post('supplier_id');

            if (!$supplierId) {
                setFlash('error', 'Supplier ID is required');
                back();
                return;
            }

            // Get supplier info
            $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$supplier) {
                setFlash('error', 'Supplier not found');
                back();
                return;
            }

            // Check for active/pending purchase orders
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as cnt FROM purchase_orders
                WHERE supplier_id = ? AND status NOT IN ('completed', 'cancelled')
            ");
            $stmt->execute([$supplierId]);
            $activePOs = $stmt->fetch(\PDO::FETCH_ASSOC)['cnt'];

            if ($activePOs > 0) {
                setFlash('error', "Cannot delete supplier. They have {$activePOs} active purchase order(s). Complete or cancel them first, or disable the account instead.");
                back();
                return;
            }

            $deleteReason = post('delete_reason', 'other');
            $deleteNotes  = post('delete_notes', '');
            $canRejoin    = post('can_rejoin', '1') === '1' ? 1 : 0;

            $this->db->beginTransaction();

            try {
                // Archive to deleted_users before hard delete
                $this->db->prepare("
                    INSERT INTO deleted_users
                        (original_id, email, first_name, role, reason, notes, deleted_by, can_rejoin)
                    VALUES (?, ?, ?, 'supplier', ?, ?, ?, ?)
                ")->execute([
                    $supplierId,
                    $supplier['email'] ?? '',
                    $supplier['name']  ?? '',
                    $deleteReason,
                    $deleteNotes,
                    user()['id'],
                    $canRejoin,
                ]);

                // Delete purchase order items for this supplier's POs
                $stmt = $this->db->prepare("
                    DELETE poi FROM purchase_order_items poi
                    INNER JOIN purchase_orders po ON poi.purchase_order_id = po.id
                    WHERE po.supplier_id = ?
                ");
                $stmt->execute([$supplierId]);

                // Delete purchase orders
                $this->db->prepare("DELETE FROM purchase_orders WHERE supplier_id = ?")->execute([$supplierId]);

                // Delete supplier products
                $this->db->prepare("DELETE FROM supplier_products WHERE supplier_id = ?")->execute([$supplierId]);

                // Delete supplier invites
                if ($this->tableExists('supplier_invites')) {
                    $this->db->prepare("DELETE FROM supplier_invites WHERE supplier_id = ?")->execute([$supplierId]);
                }

                // Delete supplier applications (by email so re-invite works cleanly)
                if (!empty($supplier['email'])) {
                    $this->db->prepare("DELETE FROM supplier_applications WHERE email = ?")->execute([$supplier['email']]);
                }

                // Delete the supplier
                $this->db->prepare("DELETE FROM suppliers WHERE id = ?")->execute([$supplierId]);

                $this->db->commit();

                // Send notification email
                try {
                    if (!empty($supplier['email'])) {
                        \App\Helpers\EmailHelper::sendSupplierAccountRemoved([
                            'email'         => $supplier['email'],
                            'supplier_name' => $supplier['name'],
                        ]);
                    }
                } catch (\Exception $emailErr) {
                    logger("Failed to send supplier deletion email: " . $emailErr->getMessage(), 'error');
                }

                // Admin bell notification
                try {
                    $supplierLabel = htmlspecialchars($supplier['name'] ?? $supplier['email']);
                    $this->db->prepare("
                        INSERT INTO admin_notifications (type, title, message, link, icon, priority, created_at)
                        VALUES ('account_removed', ?, ?, ?, 'trash', 'normal', NOW())
                    ")->execute([
                        'Supplier Account Deleted',
                        "Supplier account for {$supplierLabel} ({$supplier['email']}) was deleted by admin.",
                        '/admin/suppliers',
                    ]);
                } catch (\Exception $e) {
                    error_log('Failed to insert admin notification for supplier deletion: ' . $e->getMessage());
                }

                auditLog('supplier_delete', "Permanently deleted supplier {$supplier['name']} (ID: {$supplierId})", $supplierId);
                setFlash('success', "Supplier '{$supplier['name']}' and all associated data deleted permanently");
                redirect(url('admin/users'));

            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            error_log('Delete Supplier Error: ' . $e->getMessage());
            setFlash('error', 'Failed to delete supplier: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Toggle is_test_account flag on a user
     */
    public function toggleTestAccount(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $userId  = (int) post('user_id');
        $current = (int) post('current_value', 0);

        if (!$userId) {
            setFlash('error', 'User ID required.');
            back();
            return;
        }

        $newValue = $current ? 0 : 1;
        $this->db->prepare("UPDATE users SET is_test_account = ? WHERE id = ?")->execute([$newValue, $userId]);

        setFlash('success', $newValue ? 'User marked as test account.' : 'Test account flag removed.');
        redirect(url('admin/users'));
    }

    /**
     * Reset a test account: wipe associated data, keep the user row and email
     */
    public function resetTestUser(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $userId = (int) post('user_id');

        if (!$userId) {
            setFlash('error', 'User ID required.');
            back();
            return;
        }

        // Only allow reset on flagged test accounts
        $stmt = $this->db->prepare("SELECT is_test_account, first_name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !$user['is_test_account']) {
            setFlash('error', 'This user is not flagged as a test account.');
            back();
            return;
        }

        try {
            $this->db->beginTransaction();

            $tables = [
                'orders'          => 'user_id',
                'addresses'       => 'user_id',
                'reviews'         => 'user_id',
                'user_sessions'   => 'user_id',
                'shop_reviews'    => 'user_id',
                'shops'           => 'seller_id',
                'product_views'   => 'user_id',
                'search_logs'     => 'user_id',
                'visitor_logs'    => 'user_id',
                'visitor_sessions'=> 'user_id',
                'visitor_analytics'=> 'user_id',
                'driver_applications'=> 'user_id',
            ];

            foreach ($tables as $table => $col) {
                if ($this->tableExists($table)) {
                    try {
                        $this->db->prepare("DELETE FROM {$table} WHERE {$col} = ?")->execute([$userId]);
                    } catch (\Exception $e) {
                        error_log("Reset test account: skipping {$table} — " . $e->getMessage());
                    }
                }
            }

            // Reset user to clean state (keep email, name, role, is_test_account=1)
            $this->db->prepare("
                UPDATE users SET
                    password       = '',
                    phone          = NULL,
                    avatar         = NULL,
                    status         = 'active',
                    last_login_at  = NULL,
                    updated_at     = NOW()
                WHERE id = ?
            ")->execute([$userId]);

            $this->db->commit();

            auditLog('test_account_reset', "Reset test account for user ID {$userId} ({$user['first_name']})", $userId);
            setFlash('success', "Test account '{$user['first_name']}' has been reset. They will need to set a new password.");
            redirect(url('admin/users'));

        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log('Reset Test User Error: ' . $e->getMessage());
            setFlash('error', 'Failed to reset test account: ' . $e->getMessage());
            back();
        }
    }

    /**
     * View deleted users archive
     */
    public function deletedUsers(): void
    {
        $reasonFilter = get('reason', '');
        $roleFilter   = get('role', '');
        $banFilter    = get('banned', '');
        $search       = get('search', '');

        $where  = [];
        $params = [];

        if ($reasonFilter) {
            $where[]  = "du.reason = ?";
            $params[] = $reasonFilter;
        }
        if ($roleFilter) {
            $where[]  = "du.role = ?";
            $params[] = $roleFilter;
        }
        if ($banFilter === '1') {
            $where[] = "du.can_rejoin = 0";
        } elseif ($banFilter === '0') {
            $where[] = "du.can_rejoin = 1";
        }
        if ($search) {
            $where[] = "(du.email LIKE ? OR du.first_name LIKE ? OR du.last_name LIKE ?)";
            $s = "%{$search}%";
            array_push($params, $s, $s, $s);
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->db->prepare("
            SELECT du.*,
                   CONCAT(COALESCE(a.first_name,''), ' ', COALESCE(a.last_name,'')) AS deleted_by_name
            FROM deleted_users du
            LEFT JOIN users a ON du.deleted_by = a.id
            {$whereClause}
            ORDER BY du.deleted_at DESC
        ");
        $stmt->execute($params);
        $deletedUsers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $statsStmt = $this->db->query("
            SELECT COUNT(*) as total,
                   SUM(can_rejoin = 0) as banned,
                   SUM(can_rejoin = 1) as can_rejoin
            FROM deleted_users
        ");
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        view('admin.users.deleted', compact(
            'deletedUsers', 'stats',
            'reasonFilter', 'roleFilter', 'banFilter', 'search'
        ));
    }

    /**
     * Flip can_rejoin on a deleted_users record (ban / unban)
     */
    public function toggleDeletedUserBan(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $id      = (int) post('id');
        $current = (int) post('current_value', 1);
        $newVal  = $current ? 0 : 1;

        $this->db->prepare("UPDATE deleted_users SET can_rejoin = ? WHERE id = ?")->execute([$newVal, $id]);

        setFlash('success', $newVal
            ? 'User can now re-register.'
            : 'User is now banned from re-registering.');
        back();
    }

    /**
     * Edit seller
     */
    public function editSeller(): void
    {
        try {
            $sellerId = get('id');

            if (!$sellerId) {
                setFlash('error', 'Seller ID is required');
                redirect(url('admin/sellers'));
                return;
            }

            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND role = 'seller'");
            $stmt->execute([$sellerId]);
            $seller = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$seller) {
                setFlash('error', 'Seller not found');
                redirect(url('admin/sellers'));
                return;
            }

            // Get seller's shop
            $stmt = $this->db->prepare("SELECT * FROM shops WHERE user_id = ?");
            $stmt->execute([$sellerId]);
            $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('admin.sellers.edit', compact('seller', 'shop'));

        } catch (\Exception $e) {
            error_log('Edit Seller Error: ' . $e->getMessage());
            setFlash('error', 'Failed to load seller');
            redirect(url('admin/sellers'));
        }
    }

    /**
     * Update seller
     */
    public function updateSeller(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $sellerId = post('seller_id');
            $firstName = sanitize(post('first_name'));
            $lastName = sanitize(post('last_name'));
            $email = sanitize(post('email'));
            $status = post('status');

            $stmt = $this->db->prepare("
                UPDATE users
                SET first_name = ?, last_name = ?, email = ?, status = ?, updated_at = NOW()
                WHERE id = ? AND role = 'seller'
            ");
            $stmt->execute([$firstName, $lastName, $email, $status, $sellerId]);

            auditLog('seller_update', "Updated seller: {$email} (status: {$status})", (int)$sellerId);

            setFlash('success', 'Seller updated successfully');
            redirect(url('admin/sellers'));

        } catch (\Exception $e) {
            error_log('Update Seller Error: ' . $e->getMessage());
            setFlash('error', 'Failed to update seller');
            back();
        }
    }

    /**
     * Team Planner - Collaboration tool for admin team
     */
    public function planner(): void
    {
        view('admin.planner', [
            'pageTitle' => 'Team Planner - OCSAPP Admin'
        ]);
    }

    public function plannerDashboard(): void
    {
        view('admin.planner_dashboard', [
            'pageTitle' => 'My Dashboard - OCSAPP Admin'
        ]);
    }

    /**
     * View all notifications page
     */
    public function notifications(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $typeFilter = $_GET['type'] ?? null;
        $readFilter = $_GET['read'] ?? null;

        $userId = $_SESSION['user']['id'] ?? null;
        $result = \App\Helpers\NotificationHelper::getAll($page, 20, $typeFilter, $readFilter, $userId);

        view('admin.notifications.index', [
            'notifications' => $result['notifications'],
            'total' => $result['total'],
            'pages' => $result['pages'],
            'page' => $page,
            'typeFilter' => $typeFilter,
            'readFilter' => $readFilter,
            'pageTitle' => 'Notifications - OCSAPP Admin'
        ]);
    }

    /**
     * Admin profile page with notification preferences
     */
    public function profile(): void
    {
        $userId = $_SESSION['user']['id'];

        // Get user record
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Activity stats from planner
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total_activities,
                    SUM(CASE WHEN activity_type = 'todo' THEN 1 ELSE 0 END) as todo_activities,
                    SUM(CASE WHEN activity_type = 'note' THEN 1 ELSE 0 END) as note_activities,
                    SUM(CASE WHEN activity_type = 'comment' THEN 1 ELSE 0 END) as comment_activities
                FROM planner_activity WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $activityStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $activityStats = ['total_activities' => 0, 'todo_activities' => 0, 'note_activities' => 0, 'comment_activities' => 0];
        }

        // Task stats
        try {
            $stmt = $this->db->prepare("
                SELECT
                    COUNT(*) as assigned_tasks,
                    SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_tasks
                FROM planner_todos WHERE assigned_to = ?
            ");
            $stmt->execute([$userId]);
            $taskStats = $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $taskStats = ['assigned_tasks' => 0, 'completed_tasks' => 0];
        }

        // Notification preferences
        $notificationTypes = [
            'task_assigned' => ['label' => 'Task Assigned', 'description' => 'When someone assigns a task to you'],
            'task_completed' => ['label' => 'Task Completed', 'description' => 'When someone completes a task you created'],
            'task_comment' => ['label' => 'Task Comment', 'description' => 'When someone comments on your task'],
            'note_comment' => ['label' => 'Note Comment', 'description' => 'When someone comments on your note'],
            'mention' => ['label' => '@Mention', 'description' => 'When someone mentions you in a note or comment'],
        ];

        try {
            $stmt = $this->db->prepare("
                SELECT notification_type, in_app_enabled, email_enabled
                FROM admin_notification_preferences WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $savedPrefs = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            $savedPrefs = [];
        }

        // Build preferences with defaults
        $preferences = [];
        $savedMap = [];
        foreach ($savedPrefs as $p) {
            $savedMap[$p['notification_type']] = $p;
        }
        foreach ($notificationTypes as $type => $info) {
            $preferences[$type] = [
                'label' => $info['label'],
                'description' => $info['description'],
                'in_app' => isset($savedMap[$type]) ? (bool)$savedMap[$type]['in_app_enabled'] : true,
                'email' => isset($savedMap[$type]) ? (bool)$savedMap[$type]['email_enabled'] : true,
            ];
        }

        view('admin.profile', compact('user', 'activityStats', 'taskStats', 'preferences'));
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(): void
    {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        $userId = $_SESSION['user']['id'];
        $prefs = $_POST['prefs'] ?? [];

        $types = ['task_assigned', 'task_completed', 'task_comment', 'note_comment', 'mention'];

        try {
            $stmt = $this->db->prepare("
                INSERT INTO admin_notification_preferences (user_id, notification_type, in_app_enabled, email_enabled)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    in_app_enabled = VALUES(in_app_enabled),
                    email_enabled = VALUES(email_enabled),
                    updated_at = NOW()
            ");

            foreach ($types as $type) {
                $inApp = isset($prefs[$type]['in_app']) ? 1 : 0;
                $email = isset($prefs[$type]['email']) ? 1 : 0;
                $stmt->execute([$userId, $type, $inApp, $email]);
            }

            setFlash('success', 'Notification preferences updated successfully.');
        } catch (\Exception $e) {
            setFlash('error', 'Failed to update preferences.');
        }

        redirect('admin/profile');
    }

    /**
     * Check if a table exists in the database
     */
    private function tableExists(string $tableName): bool
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tableName]);
            return (int) $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
