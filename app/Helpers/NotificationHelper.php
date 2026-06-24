<?php

namespace App\Helpers;

/**
 * NotificationHelper - Admin Notification System
 * Handles creation, retrieval, and management of admin notifications
 * Supports both broadcast (user_id=NULL) and per-user targeted notifications
 */
class NotificationHelper
{
    // Notification types - System/Broadcast
    const TYPE_ACCOUNT_LOCKOUT = 'account_lockout';
    const TYPE_NEW_USER = 'new_user';
    const TYPE_SELLER_APPLICATION = 'seller_application';
    const TYPE_SUPPLIER_APPLICATION = 'supplier_application';
    const TYPE_SELLER_VERIFIED = 'seller_verified';
    const TYPE_NEW_ORDER = 'new_order';
    const TYPE_LOW_STOCK = 'low_stock';
    const TYPE_SYSTEM = 'system';
    const TYPE_SECURITY = 'security';

    // Notification types - Supplier messaging
    const TYPE_SUPPLIER_MESSAGE = 'supplier_message';

    // Notification types - Per-user (Planner)
    const TYPE_TASK_ASSIGNED = 'task_assigned';
    const TYPE_TASK_COMPLETED = 'task_completed';
    const TYPE_TASK_COMMENT = 'task_comment';
    const TYPE_NOTE_COMMENT = 'note_comment';
    const TYPE_MENTION = 'mention';

    // Priority levels
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Icon mappings for notification types
    private static $typeIcons = [
        self::TYPE_ACCOUNT_LOCKOUT => 'lock',
        self::TYPE_NEW_USER => 'user-plus',
        self::TYPE_SELLER_APPLICATION => 'store',
        self::TYPE_SUPPLIER_APPLICATION => 'truck-loading',
        self::TYPE_SELLER_VERIFIED => 'check-circle',
        self::TYPE_NEW_ORDER => 'shopping-cart',
        self::TYPE_LOW_STOCK => 'exclamation-triangle',
        self::TYPE_SYSTEM => 'cog',
        self::TYPE_SECURITY => 'shield-halved',
        self::TYPE_SUPPLIER_MESSAGE => 'envelope',
        self::TYPE_TASK_ASSIGNED => 'user-check',
        self::TYPE_TASK_COMPLETED => 'circle-check',
        self::TYPE_TASK_COMMENT => 'comment',
        self::TYPE_NOTE_COMMENT => 'comment-dots',
        self::TYPE_MENTION => 'at',
    ];

    /**
     * Add a new notification
     *
     * @param string $type Notification type constant
     * @param string $title Notification title
     * @param string $message Notification message
     * @param array $options Optional: data, link, icon, priority, user_id
     * @return int|false Notification ID or false on failure
     */
    public static function add(
        string $type,
        string $title,
        string $message,
        array $options = []
    ) {
        try {
            $db = \Database::getConnection();

            $userId = $options['user_id'] ?? null;
            $data = isset($options['data']) ? json_encode($options['data']) : null;
            $link = $options['link'] ?? null;
            $icon = $options['icon'] ?? (self::$typeIcons[$type] ?? 'bell');
            $priority = $options['priority'] ?? self::PRIORITY_NORMAL;

            $stmt = $db->prepare("
                INSERT INTO admin_notifications
                (user_id, type, title, message, data, link, icon, priority, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $userId,
                $type,
                $title,
                $message,
                $data,
                $link,
                $icon,
                $priority
            ]);

            return $db->lastInsertId();

        } catch (\PDOException $e) {
            error_log("NotificationHelper::add error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a notification targeted at a specific user
     * Checks user's in-app and email preferences before creating
     *
     * @param int $targetUserId User to notify
     * @param string $type Notification type
     * @param string $title Notification title
     * @param string $message Notification message
     * @param array $options Optional: data, link, priority
     * @return int|false Notification ID or false
     */
    public static function addForUser(
        int $targetUserId,
        string $type,
        string $title,
        string $message,
        array $options = []
    ) {
        // Check in-app preference
        $inAppEnabled = self::isInAppEnabled($targetUserId, $type);
        $emailEnabled = self::isEmailEnabled($targetUserId, $type);

        $notificationId = false;

        // Create in-app notification if enabled
        if ($inAppEnabled) {
            $options['user_id'] = $targetUserId;
            $notificationId = self::add($type, $title, $message, $options);
        }

        // Send email notification if enabled
        if ($emailEnabled) {
            self::sendNotificationEmail($targetUserId, $type, $title, $message, $options);
        }

        return $notificationId;
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount(?int $userId = null): int
    {
        try {
            $db = \Database::getConnection();

            if ($userId !== null) {
                $stmt = $db->prepare("
                    SELECT COUNT(*) as count FROM admin_notifications
                    WHERE is_read = 0 AND (user_id = ? OR user_id IS NULL)
                ");
                $stmt->execute([$userId]);
            } else {
                $stmt = $db->query("SELECT COUNT(*) as count FROM admin_notifications WHERE is_read = 0");
            }

            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        } catch (\PDOException $e) {
            error_log("NotificationHelper::getUnreadCount error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent notifications for dropdown
     */
    public static function getRecent(int $limit = 5, ?int $userId = null): array
    {
        try {
            $db = \Database::getConnection();

            $userFilter = '';
            $params = [];

            if ($userId !== null) {
                $userFilter = 'WHERE (n.user_id = ? OR n.user_id IS NULL)';
                $params[] = $userId;
            }

            $params[] = $limit;

            $stmt = $db->prepare("
                SELECT
                    n.*,
                    CONCAT(u.first_name, ' ', u.last_name) as read_by_name
                FROM admin_notifications n
                LEFT JOIN users u ON n.read_by = u.id
                {$userFilter}
                ORDER BY n.is_read ASC, n.created_at DESC
                LIMIT ?
            ");
            $stmt->execute($params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("NotificationHelper::getRecent error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get all notifications with pagination
     */
    public static function getAll(
        int $page = 1,
        int $perPage = 20,
        ?string $typeFilter = null,
        ?string $readFilter = null,
        ?int $userId = null
    ): array {
        try {
            $db = \Database::getConnection();
            $offset = ($page - 1) * $perPage;

            $where = [];
            $params = [];

            if ($userId !== null) {
                $where[] = "(n.user_id = ? OR n.user_id IS NULL)";
                $params[] = $userId;
            }

            if ($typeFilter) {
                $where[] = "n.type = ?";
                $params[] = $typeFilter;
            }

            if ($readFilter === 'read') {
                $where[] = "n.is_read = 1";
            } elseif ($readFilter === 'unread') {
                $where[] = "n.is_read = 0";
            }

            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Get total count
            $countStmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM admin_notifications n
                {$whereClause}
            ");
            $countStmt->execute($params);
            $total = (int) $countStmt->fetch(\PDO::FETCH_ASSOC)['count'];

            // Get notifications
            $stmt = $db->prepare("
                SELECT
                    n.*,
                    CONCAT(u.first_name, ' ', u.last_name) as read_by_name
                FROM admin_notifications n
                LEFT JOIN users u ON n.read_by = u.id
                {$whereClause}
                ORDER BY n.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($params);
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'notifications' => $notifications,
                'total' => $total,
                'pages' => ceil($total / $perPage)
            ];

        } catch (\PDOException $e) {
            error_log("NotificationHelper::getAll error: " . $e->getMessage());
            return ['notifications' => [], 'total' => 0, 'pages' => 0];
        }
    }

    /**
     * Mark notification as read
     */
    public static function markRead(int $id, ?int $userId = null): bool
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                UPDATE admin_notifications
                SET is_read = 1, read_at = NOW(), read_by = ?
                WHERE id = ?
            ");
            $stmt->execute([$userId, $id]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("NotificationHelper::markRead error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllRead(?int $userId = null): int
    {
        try {
            $db = \Database::getConnection();

            if ($userId !== null) {
                $stmt = $db->prepare("
                    UPDATE admin_notifications
                    SET is_read = 1, read_at = NOW(), read_by = ?
                    WHERE is_read = 0 AND (user_id = ? OR user_id IS NULL)
                ");
                $stmt->execute([$userId, $userId]);
            } else {
                $stmt = $db->prepare("
                    UPDATE admin_notifications
                    SET is_read = 1, read_at = NOW(), read_by = ?
                    WHERE is_read = 0
                ");
                $stmt->execute([$userId]);
            }

            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("NotificationHelper::markAllRead error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete old notifications (cleanup)
     */
    public static function cleanup(int $daysOld = 30): int
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                DELETE FROM admin_notifications
                WHERE is_read = 1
                AND created_at < DATE_SUB(NOW(), INTERVAL ? DAY)
            ");
            $stmt->execute([$daysOld]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            error_log("NotificationHelper::cleanup error: " . $e->getMessage());
            return 0;
        }
    }

    // =====================================================
    // PREFERENCE CHECKING
    // =====================================================

    /**
     * Check if in-app notifications are enabled for a user + type
     */
    private static function isInAppEnabled(int $userId, string $type): bool
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT in_app_enabled FROM admin_notification_preferences
                WHERE user_id = ? AND notification_type = ?
            ");
            $stmt->execute([$userId, $type]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (bool) $row['in_app_enabled'] : true;
        } catch (\PDOException $e) {
            return true; // Default to enabled
        }
    }

    /**
     * Check if email notifications are enabled for a user + type
     */
    private static function isEmailEnabled(int $userId, string $type): bool
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT email_enabled FROM admin_notification_preferences
                WHERE user_id = ? AND notification_type = ?
            ");
            $stmt->execute([$userId, $type]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);
            return $row ? (bool) $row['email_enabled'] : true;
        } catch (\PDOException $e) {
            return true; // Default to enabled
        }
    }

    /**
     * Send notification email to a user
     */
    private static function sendNotificationEmail(
        int $targetUserId,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): bool {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("SELECT email, first_name, last_name FROM users WHERE id = ?");
            $stmt->execute([$targetUserId]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$user || empty($user['email'])) {
                return false;
            }

            $link = $options['link'] ?? null;
            if ($link && strpos($link, 'http') !== 0) {
                $link = 'https://ocsapp.ca' . $link;
            }

            return EmailHelper::sendTemplate(
                $user['email'],
                'planner-notification',
                [
                    'user_first_name' => $user['first_name'],
                    'user_last_name' => $user['last_name'],
                    'user_email' => $user['email'],
                    'notification_title' => $title,
                    'notification_message' => $message,
                    'notification_type' => $type,
                    'action_url' => $link ?: 'https://ocsapp.ca/admin/planner',
                    'subject' => "OCSAPP: {$title}",
                ]
            );
        } catch (\Exception $e) {
            error_log("NotificationHelper::sendNotificationEmail error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse @mentions from text content
     * Returns array of ['name' => string, 'user_id' => int]
     */
    public static function parseMentions(string $text): array
    {
        $mentions = [];
        if (preg_match_all('/@\[([^\]]+)\]\((\d+)\)/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $mentions[] = [
                    'name' => $match[1],
                    'user_id' => (int) $match[2],
                ];
            }
        }
        return $mentions;
    }

    // =====================================================
    // DEPARTMENT ROUTING
    // =====================================================

    /**
     * Notify all active admin users in a given department.
     * Falls back to a broadcast notification if no one is assigned to that department.
     *
     * @param string $department  e.g. 'ops', 'finance', 'support', 'logistics', 'tech', 'management'
     */
    public static function notifyDepartment(
        string $department,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): void {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT id FROM users
                WHERE department = ?
                  AND role IN ('super_admin', 'admin', 'admin_staff')
                  AND status = 'active'
            ");
            $stmt->execute([$department]);
            $userIds = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            if (empty($userIds)) {
                self::add($type, $title, $message, $options);
                return;
            }

            foreach ($userIds as $userId) {
                self::addForUser((int) $userId, $type, $title, $message, $options);
            }
        } catch (\PDOException $e) {
            error_log("NotificationHelper::notifyDepartment error: " . $e->getMessage());
            self::add($type, $title, $message, $options);
        }
    }

    // =====================================================
    // CONVENIENCE METHODS FOR COMMON NOTIFICATION TYPES
    // =====================================================

    /**
     * Create account lockout notification - routed to management
     */
    public static function accountLockout(string $email, string $ipAddress): void
    {
        self::notifyDepartment(
            'management',
            self::TYPE_ACCOUNT_LOCKOUT,
            'Account Locked',
            "Account '{$email}' has been locked due to too many failed login attempts from IP: {$ipAddress}",
            [
                'data' => ['email' => $email, 'ip' => $ipAddress],
                'link' => '/admin/users?search=' . urlencode($email),
                'priority' => self::PRIORITY_HIGH
            ]
        );
    }

    /**
     * Create new user registration notification
     */
    public static function newUserRegistration(array $user): int|false
    {
        $name = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
        $role = $user['role'] ?? 'buyer';

        return self::add(
            self::TYPE_NEW_USER,
            'New User Registration',
            "New {$role} registered: {$name} ({$user['email']})",
            [
                'data' => ['user_id' => $user['id'] ?? null, 'email' => $user['email'], 'role' => $role],
                'link' => '/admin/users?search=' . urlencode($user['email']),
                'priority' => self::PRIORITY_NORMAL
            ]
        );
    }

    /**
     * Create seller application notification - routed to ops
     */
    public static function sellerApplication(array $seller): void
    {
        $name = trim(($seller['first_name'] ?? '') . ' ' . ($seller['last_name'] ?? ''));

        self::notifyDepartment(
            'ops',
            self::TYPE_SELLER_APPLICATION,
            'New Seller Application',
            "New seller application from {$name} ({$seller['email']}). Review required.",
            [
                'data' => ['seller_id' => $seller['id'] ?? null, 'email' => $seller['email']],
                'link' => '/admin/sellers/view?id=' . ($seller['id'] ?? ''),
                'priority' => self::PRIORITY_HIGH
            ]
        );
    }

    /**
     * Create seller verification submitted notification - routed to ops
     */
    public static function sellerVerificationSubmitted(array $seller): void
    {
        $name = trim(($seller['first_name'] ?? '') . ' ' . ($seller['last_name'] ?? ''));

        self::notifyDepartment(
            'ops',
            self::TYPE_SELLER_VERIFIED,
            'Seller Verification Submitted',
            "{$name} has submitted documents for verification review.",
            [
                'data' => ['seller_id' => $seller['id'] ?? null, 'email' => $seller['email']],
                'link' => '/admin/sellers/verification-review?id=' . ($seller['id'] ?? ''),
                'priority' => self::PRIORITY_HIGH
            ]
        );
    }

    /**
     * Create supplier application notification - routed to ops
     */
    public static function supplierApplication(array $application): void
    {
        $name = trim(($application['first_name'] ?? '') . ' ' . ($application['last_name'] ?? ''));
        $business = $application['business_name'] ?? '';
        $neq = $application['neq_number'] ?? '';

        self::notifyDepartment(
            'ops',
            self::TYPE_SUPPLIER_APPLICATION,
            'New Supplier Application',
            "New supplier application from {$name} ({$business}). NEQ: {$neq}. Review required.",
            [
                'data' => [
                    'application_id' => $application['id'] ?? null,
                    'lead_id' => $application['lead_id'] ?? null,
                    'email' => $application['email'] ?? '',
                    'business' => $business,
                ],
                'link' => '/admin/leads/view?id=' . ($application['lead_id'] ?? ''),
                'priority' => self::PRIORITY_HIGH
            ]
        );
    }

    /**
     * Create new order notification
     */
    public static function newOrder(array $order): int|false
    {
        $total = isset($order['total']) ? number_format($order['total'], 2) : '0.00';

        return self::add(
            self::TYPE_NEW_ORDER,
            'New Order Received',
            "Order #{$order['order_number']} received - \${$total}",
            [
                'data' => ['order_id' => $order['id'] ?? null, 'order_number' => $order['order_number']],
                'link' => '/admin/orders/view?id=' . ($order['id'] ?? ''),
                'priority' => self::PRIORITY_NORMAL
            ]
        );
    }

    /**
     * Create low stock alert notification - routed to ops
     */
    public static function lowStockAlert(array $product, int $currentStock): void
    {
        self::notifyDepartment(
            'ops',
            self::TYPE_LOW_STOCK,
            'Low Stock Alert',
            "Product '{$product['name']}' (SKU: {$product['sku']}) is running low. Current stock: {$currentStock}",
            [
                'data' => ['product_id' => $product['id'] ?? null, 'sku' => $product['sku'], 'stock' => $currentStock],
                'link' => '/admin/products/edit?id=' . ($product['id'] ?? ''),
                'priority' => self::PRIORITY_HIGH
            ]
        );
    }

    /**
     * Create system notification
     */
    public static function system(string $title, string $message, ?string $link = null): int|false
    {
        return self::add(
            self::TYPE_SYSTEM,
            $title,
            $message,
            [
                'link' => $link,
                'priority' => self::PRIORITY_LOW
            ]
        );
    }

    // =====================================================
    // SUPPLIER NOTIFICATION METHODS
    // =====================================================

    /**
     * Add a notification for a supplier (supplier_notifications table)
     */
    public static function addSupplierNotification(
        int $supplierId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        string $icon = 'bell',
        ?string $title_fr = null,
        ?string $message_fr = null
    ): int|false {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO supplier_notifications
                (supplier_id, type, title, message, link, icon, title_fr, message_fr, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$supplierId, $type, $title, $message, $link, $icon, $title_fr, $message_fr]);
            return (int) $db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("NotificationHelper::addSupplierNotification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread supplier notification count
     */
    public static function getSupplierUnreadCount(int $supplierId): int
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM supplier_notifications WHERE supplier_id = ? AND is_read = 0");
            $stmt->execute([$supplierId]);
            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * Get recent supplier notifications
     */
    public static function getSupplierRecent(int $supplierId, int $limit = 10): array
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT * FROM supplier_notifications
                WHERE supplier_id = ?
                ORDER BY is_read ASC, created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$supplierId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Mark a supplier notification as read
     */
    public static function markSupplierRead(int $id, int $supplierId): bool
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("UPDATE supplier_notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND supplier_id = ?");
            $stmt->execute([$id, $supplierId]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Mark all supplier notifications as read
     */
    public static function markAllSupplierRead(int $supplierId): int
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("UPDATE supplier_notifications SET is_read = 1, read_at = NOW() WHERE supplier_id = ? AND is_read = 0");
            $stmt->execute([$supplierId]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    // =====================================================
    // BUSINESS NOTIFICATION METHODS
    // =====================================================

    /**
     * Add a notification for a distribution business (business_notifications table)
     */
    public static function addBusinessNotification(
        int $businessId,
        string $type,
        string $title,
        string $message,
        string $link = '',
        string $icon = 'bell'
    ): int|false {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                INSERT INTO business_notifications
                (business_id, type, title, message, link, icon, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$businessId, $type, $title, $message, $link, $icon]);
            return (int) $db->lastInsertId();
        } catch (\PDOException $e) {
            error_log("NotificationHelper::addBusinessNotification error: " . $e->getMessage());
            return false;
        }
    }

    public static function getBusinessUnreadCount(int $businessId): int
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM business_notifications WHERE business_id = ? AND is_read = 0");
            $stmt->execute([$businessId]);
            return (int) $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        } catch (\PDOException $e) {
            return 0;
        }
    }

    /**
     * Get recent business notifications
     */
    public static function getBusinessRecent(int $businessId, int $limit = 10): array
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("
                SELECT * FROM business_notifications
                WHERE business_id = ?
                ORDER BY is_read ASC, created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$businessId, $limit]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Mark a business notification as read
     */
    public static function markBusinessRead(int $id, int $businessId): bool
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("UPDATE business_notifications SET is_read = 1, read_at = NOW() WHERE id = ? AND business_id = ?");
            $stmt->execute([$id, $businessId]);
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Mark all business notifications as read
     */
    public static function markAllBusinessRead(int $businessId): int
    {
        try {
            $db = \Database::getConnection();
            $stmt = $db->prepare("UPDATE business_notifications SET is_read = 1, read_at = NOW() WHERE business_id = ? AND is_read = 0");
            $stmt->execute([$businessId]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            return 0;
        }
    }

    // =====================================================
    // BUSINESS ACTIVITY + EMAIL LOGGING
    // =====================================================

    /**
     * Append an entry to business_activity_log.
     *
     * @param int    $businessId  business_profiles.id
     * @param string $actionType  snake_case event name
     * @param string $description Human-readable description
     * @param string $actor       'admin' | 'system' | 'business'
     * @param string|null $actorName Display name of the actor (admin user name, etc.)
     */
    public static function logBusinessActivity(
        int $businessId,
        string $actionType,
        string $description,
        string $actor = 'system',
        ?string $actorName = null
    ): void {
        try {
            $db = \Database::getConnection();
            $db->prepare("
                INSERT INTO business_activity_log (business_id, actor, actor_name, action_type, description, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ")->execute([$businessId, $actor, $actorName, $actionType, $description]);
        } catch (\PDOException $e) {
            error_log("NotificationHelper::logBusinessActivity error: " . $e->getMessage());
        }
    }

    /**
     * Append an entry to business_email_log.
     *
     * @param int    $businessId business_profiles.id
     * @param string $subject    Email subject line
     * @param string $preview    Short plain-text preview (first ~200 chars of body)
     */
    public static function logBusinessEmail(
        int $businessId,
        string $subject,
        string $preview = ''
    ): void {
        try {
            $db = \Database::getConnection();
            $db->prepare("
                INSERT INTO business_email_log (business_id, subject, preview, sent_at)
                VALUES (?, ?, ?, NOW())
            ")->execute([$businessId, $subject, $preview]);
        } catch (\PDOException $e) {
            error_log("NotificationHelper::logBusinessEmail error: " . $e->getMessage());
        }
    }
}
