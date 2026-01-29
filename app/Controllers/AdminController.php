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

            // Pass data to view
            view('admin.dashboard', compact(
                'total_users',
                'total_sellers',
                'total_buyers',
                'active_delivery_staff',
                'recent_users',
                'recent_logins'
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

            view('admin.dashboard', compact(
                'total_users',
                'total_sellers',
                'total_buyers',
                'active_delivery_staff',
                'recent_users',
                'recent_logins'
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

            // Get all roles for the filter dropdown
            $stmt = $this->db->query("SELECT id, name, display_name FROM roles ORDER BY id");
            $roles = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Build query with JOIN to get actual roles from user_roles table
            $where = [];
            $params = [];

            if (!empty($roleFilter)) {
                $where[] = "r.name = ?";
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
                SELECT u.*, r.name as role, r.display_name as role_display
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                {$whereClause}
                ORDER BY u.created_at DESC
            ");
            $stmt->execute($params);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get total count
            $total = count($users);

            view('admin.users.index', compact('users', 'roles', 'roleFilter', 'statusFilter', 'search', 'total'));

        } catch (\Exception $e) {
            error_log('Admin Users Error: ' . $e->getMessage());
            $users = [];
            $roles = [];
            $roleFilter = '';
            $statusFilter = '';
            $search = '';
            $total = 0;
            view('admin.users.index', compact('users', 'roles', 'roleFilter', 'statusFilter', 'search', 'total'));
        }
    }

    /**
     * Store new user (admin-created)
     */
    public function storeUser(): void
    {
        try {
            $firstName = sanitize(post('first_name'));
            $lastName = sanitize(post('last_name'));
            $email = sanitize(post('email'));
            $phone = sanitize(post('phone', ''));
            $roleName = post('role');
            $status = post('status', 'active');
            $password = post('password');
            $sendWelcomeEmail = post('send_welcome_email') === 'on';

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
                    INSERT INTO users (first_name, last_name, email, phone, password, role, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$firstName, $lastName, $email, $phone, $hashedPassword, $roleName, $status]);
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

            // Check if current user is super_admin to determine which roles can be assigned
            $currentUserRole = userRole();
            $canAssignAdminRoles = ($currentUserRole === 'super_admin');

            view('admin.users.edit', compact('user', 'roles', 'canAssignAdminRoles'));

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
        try {
            $userId = post('user_id');
            $firstName = sanitize(post('first_name'));
            $lastName = sanitize(post('last_name'));
            $email = sanitize(post('email'));
            $phone = sanitize(post('phone', ''));
            $role = post('role');
            $status = post('status');
            $password = post('password');

            // Security check: Only super_admin can assign admin tier roles
            $adminTierRoles = ['super_admin', 'admin', 'admin_staff'];
            $currentUserRole = userRole();

            if (in_array($role, $adminTierRoles) && $currentUserRole !== 'super_admin') {
                // Check if role is actually changing (allow keeping current role)
                $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $currentRole = $stmt->fetchColumn();

                if ($currentRole !== $role) {
                    setFlash('error', 'Only Super Administrators can assign admin tier roles');
                    back();
                    return;
                }
            }

            // Build update query
            if (!empty($password)) {
                // Update with password
                $stmt = $this->db->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, status = ?, password = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt->execute([$firstName, $lastName, $email, $phone, $role, $status, $hashedPassword, $userId]);
            } else {
                // Update without password
                $stmt = $this->db->prepare("
                    UPDATE users
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, status = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$firstName, $lastName, $email, $phone, $role, $status, $userId]);
            }

            // Also update user_roles table for proper role management
            $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = ?");
            $stmt->execute([$role]);
            $roleRecord = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($roleRecord) {
                // Delete existing user_roles for this user
                $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
                $stmt->execute([$userId]);

                // Insert new role
                $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$userId, $roleRecord['id']]);
            }

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

            // Check if user has delivery assignments (drivers)
            if ($this->tableExists('delivery_assignments')) {
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count
                    FROM delivery_assignments
                    WHERE driver_id = ?
                ");
                $stmt->execute([$userId]);
                $deliveryCount = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];

                if ($deliveryCount > 0) {
                    setFlash('error', "Cannot delete this user. They have {$deliveryCount} delivery assignment(s). Please reassign or complete deliveries first, or change user status to 'inactive' instead.");
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

                // Delete the user (this will CASCADE to:
                // - addresses, orders, reviews, sessions, shop_reviews, shops, user_roles)
                // - audit_logs will SET NULL (keeping audit trail)
                // - products will SET NULL (products remain but without seller)
                $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$userId]);

                // Commit transaction
                $this->db->commit();

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

    /**
     * HTML Editor - Document editor for team content
     */
    public function htmlEditor(): void
    {
        view('admin.planner.html-editor', [
            'pageTitle' => 'HTML Editor - OCSAPP Admin'
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

        $result = \App\Helpers\NotificationHelper::getAll($page, 20, $typeFilter, $readFilter);

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
     * Check if a table exists in the database
     */
    private function tableExists(string $tableName): bool
    {
        try {
            $result = $this->db->query("SHOW TABLES LIKE '{$tableName}'");
            return $result->rowCount() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}
