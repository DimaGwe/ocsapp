<?php

namespace App\Controllers;

use PDO;
use Exception;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminDeliveryController
 *
 * Admin panel for delivery management:
 * - Manage delivery drivers
 * - Assign deliveries to drivers
 * - Monitor delivery performance
 * - Track earnings and payouts
 * - Manage delivery zones
 * - View analytics and reports
 */

class AdminDeliveryController {
    private $db;

    public function __construct() {
        $this->db = \Database::getConnection();

        // Ensure user is any admin tier
        if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            setFlash('error', 'Access denied. Admin role required.');
            redirect(url('/'));
        }
    }
    
    /**
     * Delivery Management Dashboard (Unified B2C + B2B)
     */
    public function index() {
        // Get overview statistics (B2C orders)
        $stats = $this->getDeliveryStats();

        // Get B2B distribution stats
        $distributionStats = $this->getDistributionDeliveryStats();

        // Get recent deliveries (combined B2C + B2B)
        $recentDeliveries = $this->getRecentDeliveries(10);

        // Get active drivers
        $activeDrivers = $this->getActiveDrivers();

        // Get today's deliveries
        $todayDeliveries = $this->getTodayDeliveries();

        // PO pickup counts
        $poStats = $this->db->query("
            SELECT
                SUM(CASE WHEN status = 'ready_for_pickup' AND assigned_driver_id IS NOT NULL THEN 1 ELSE 0 END) as po_assigned,
                SUM(CASE WHEN status = 'picked_up' THEN 1 ELSE 0 END) as po_picked_up
            FROM purchase_orders
            WHERE assigned_driver_id IS NOT NULL AND status IN ('ready_for_pickup','picked_up')
        ")->fetch(PDO::FETCH_ASSOC);

        return view('admin/delivery/index', [
            'stats' => $stats,
            'distributionStats' => $distributionStats,
            'recentDeliveries' => $recentDeliveries,
            'activeDrivers' => $activeDrivers,
            'todayDeliveries' => $todayDeliveries,
            'poStats' => $poStats,
            'pageTitle' => 'Delivery Management'
        ]);
    }
    
    /**
     * Drivers Management page (Marketplace people view — like Buyers/Sellers)
     */
    public function drivers(): void
    {
        $search       = get('search', '');
        $statusFilter = get('status', '');
        $perPage      = 20;
        $page         = max(1, (int)get('page', 1));
        $offset       = ($page - 1) * $perPage;

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = '(u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)';
            $st       = "%{$search}%";
            $params   = array_merge($params, [$st, $st, $st, $st]);
        }
        if ($statusFilter) {
            $where[]  = 'u.status = ?';
            $params[] = $statusFilter;
        }

        $whereClause = 'WHERE ' . implode(' AND ', $where) . " AND r.name = 'delivery'";

        $countStmt = $this->db->prepare("
            SELECT COUNT(DISTINCT u.id)
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            {$whereClause}
        ");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $queryParams   = array_merge($params, [$perPage, $offset]);
        $stmt          = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.avatar, u.created_at,
                   da.status AS availability_status,
                   dz.name   AS zone_name,
                   (SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = u.id AND status = 'delivered') AS completed_deliveries,
                   (SELECT AVG(rating) FROM delivery_assignments WHERE driver_id = u.id AND rating IS NOT NULL) AS avg_rating
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r       ON ur.role_id = r.id
            LEFT JOIN driver_availability da ON u.id = da.driver_id
            LEFT JOIN delivery_zones dz      ON da.zone_id = dz.id
            {$whereClause}
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute($queryParams);
        $drivers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $allStmt = $this->db->prepare("
            SELECT u.status FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE r.name = 'delivery'
        ");
        $allStmt->execute();
        $allStatuses    = $allStmt->fetchAll(\PDO::FETCH_COLUMN);
        $activeCount    = count(array_filter($allStatuses, fn($s) => $s === 'active'));
        $suspendedCount = count(array_filter($allStatuses, fn($s) => $s === 'suspended'));

        try {
            $pendingStmt = $this->db->query("SELECT COUNT(*) FROM driver_applications WHERE status IN ('pending','under_review')");
            $pendingApps = (int)$pendingStmt->fetchColumn();
        } catch (\Exception $e) {
            $pendingApps = 0;
        }

        view('admin.drivers.index', compact(
            'drivers', 'search', 'statusFilter', 'total', 'page', 'perPage',
            'activeCount', 'suspendedCount', 'pendingApps'
        ));
    }

    /**
     * Delivery Staff Management
     */
    public function staff() {
        $page = max(1, (int)get('page', 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $search = get('search', '');
        $status = get('status', '');
        
        // Build query
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if ($search) {
            $whereClause .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
        }
        
        // Get total count
        $countQuery = "
            SELECT COUNT(DISTINCT u.id)
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            $whereClause
            AND r.name = 'delivery'
        ";
        $stmt = $this->db->prepare($countQuery);
        $stmt->execute($params);
        $total = $stmt->fetchColumn();
        
        // Get drivers with stats
        $query = "
            SELECT
                u.id,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.status,
                u.avatar,
                u.created_at,
                da.status as availability_status,
                da.active_deliveries,
                da.last_location_update,
                dz.name as zone_name,
                (SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = u.id) as total_deliveries,
                (SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = u.id AND status = 'delivered') as completed_deliveries,
                (SELECT COALESCE(SUM(net_earning), 0) FROM delivery_earnings WHERE driver_id = u.id) as total_earnings,
                (SELECT AVG(rating) FROM delivery_assignments WHERE driver_id = u.id AND rating IS NOT NULL) as avg_rating
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN driver_availability da ON u.id = da.driver_id
            LEFT JOIN delivery_zones dz ON da.zone_id = dz.id
            $whereClause
            AND r.name = 'delivery'
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $totalPages = ceil($total / $perPage);

        // Fetch pending driver applications
        $appStmt = $this->db->query("
            SELECT * FROM driver_applications
            WHERE status IN ('pending', 'under_review')
            ORDER BY created_at DESC
        ");
        $applications = $appStmt->fetchAll(PDO::FETCH_ASSOC);

        // If arriving via bell notification (?app=X), ensure that application is
        // loaded even if its status is 'approved' (so the message thread is visible)
        $focusAppId = (int) get('app', 0);
        if ($focusAppId) {
            $existingIds = array_column($applications, 'id');
            if (!in_array($focusAppId, $existingIds)) {
                $focusStmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? LIMIT 1");
                $focusStmt->execute([$focusAppId]);
                $focusApp = $focusStmt->fetch(\PDO::FETCH_ASSOC);
                if ($focusApp) {
                    array_unshift($applications, $focusApp);
                }
            }
        }

        $activeTab = get('tab', 'drivers');

        return view('admin/delivery/staff', [
            'drivers'      => $drivers,
            'applications' => $applications,
            'activeTab'    => $activeTab,
            'page'         => $page,
            'totalPages'   => $totalPages,
            'total'        => $total,
            'search'       => $search,
            'pageTitle'    => 'Delivery Staff'
        ]);
    }

    // ========================================
    // DRIVER HIRING PIPELINE METHODS
    // ========================================

    /**
     * Log an activity to lead_activities and fire an admin bell notification.
     * $notifyApplicantUserId — if set, also send a bell notification to the driver (future use)
     */
    private function logPipelineActivity(int $leadId, string $type, string $description, string $adminBellType, string $adminBellTitle, string $adminBellMsg, string $adminLink): void
    {
        $adminId = $_SESSION['user']['id'] ?? null;

        // 1. Lead activity timeline
        try {
            $this->db->prepare("
                INSERT INTO lead_activities (lead_id, activity_type, description, outcome, created_by)
                VALUES (?, ?, ?, NULL, ?)
            ")->execute([$leadId, $type, $description, $adminId]);
        } catch (\Exception $e) {
            logger('logPipelineActivity lead_activities: ' . $e->getMessage(), 'warning');
        }

        // 2. Admin bell notification
        try {
            \App\Helpers\NotificationHelper::add(
                $adminBellType,
                $adminBellTitle,
                $adminBellMsg,
                ['link' => $adminLink, 'icon' => 'user-tie', 'priority' => 'normal']
            );
        } catch (\Exception $e) {
            logger('logPipelineActivity notification: ' . $e->getMessage(), 'warning');
        }
    }

    /**
     * Mark application as under_review
     */
    public function markUnderReview(): void
    {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();

        $appId  = (int) post('application_id', 0);
        if (!$appId) { jsonResponse(['error' => 'Invalid application ID'], 422); return; }

        try {
            $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? AND status IN ('pending') LIMIT 1");
            $stmt->execute([$appId]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) { throw new Exception('Application not found or already past this stage.'); }

            $adminId = $_SESSION['user']['id'] ?? null;
            $this->db->prepare("
                UPDATE driver_applications
                SET status = 'under_review', pipeline_stage = 'under_review',
                    reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ")->execute([$adminId, $appId]);

            // Message to applicant
            $this->db->prepare("
                INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message, message_fr)
                VALUES (?, 'admin', ?, ?, ?)
            ")->execute([
                $appId, $adminId,
                "Hi {$app['first_name']}, your application is now being reviewed by our team. We'll be in touch shortly!",
                "Bonjour {$app['first_name']}, votre candidature est maintenant en cours d'examen par notre équipe. Nous vous contacterons sous peu !"
            ]);

            // Notify applicant if they have a user account
            if ($app['user_id']) {
                try {
                    \App\Helpers\EmailHelper::sendDriverUnderReview([
                        'first_name'     => $app['first_name'],
                        'email'          => $app['email'],
                        'application_id' => $app['id'],
                    ]);
                } catch (\Exception $e) { logger('markUnderReview email: ' . $e->getMessage(), 'warning'); }
            }

            // Sync CRM lead status
            if ($app['lead_id']) {
                $this->db->prepare("UPDATE leads SET status = 'qualified', updated_at = NOW() WHERE id = ?")->execute([$app['lead_id']]);
                $this->logPipelineActivity(
                    $app['lead_id'],
                    'status_change',
                    "Application #{$app['id']} for {$app['first_name']} {$app['last_name']} moved to Under Review.",
                    'driver_pipeline',
                    'Application Under Review',
                    "{$app['first_name']} {$app['last_name']}'s driver application is now under review.",
                    "/admin/leads/view?id={$app['lead_id']}"
                );
            }

            jsonResponse(['success' => true, 'message' => 'Application marked as Under Review.']);
        } catch (Exception $e) {
            logger("markUnderReview error: " . $e->getMessage(), 'error');
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Propose interview time slots to applicant
     */
    public function requestInterview(): void
    {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();

        $appId         = (int) post('application_id', 0);
        $proposedTimes = post('proposed_times', []); // array of datetime strings
        $introMessage  = sanitize(post('intro_message', ''));

        if (!$appId || empty($proposedTimes)) {
            jsonResponse(['error' => 'Application ID and at least one time slot are required.'], 422);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? AND status IN ('pending','under_review') LIMIT 1");
            $stmt->execute([$appId]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) { throw new Exception('Application not found or cannot be moved to this stage.'); }

            $adminId = $_SESSION['user']['id'] ?? null;
            $timesJson = json_encode(array_values($proposedTimes));

            $this->db->prepare("
                UPDATE driver_applications
                SET status = 'interview_requested', pipeline_stage = 'interview_requested',
                    interview_proposed_times = ?,
                    reviewed_by = ?, updated_at = NOW()
                WHERE id = ?
            ")->execute([$timesJson, $adminId, $appId]);

            // Post a message to the applicant
            $defaultMsg   = "Hi {$app['first_name']}, we'd love to schedule an interview! Please log in to select a time that works for you.";
            $defaultMsgFr = "Bonjour {$app['first_name']}, nous aimerions planifier un entretien ! Veuillez vous connecter pour choisir un créneau horaire qui vous convient.";
            $this->db->prepare("
                INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message, message_fr)
                VALUES (?, 'admin', ?, ?, ?)
            ")->execute([$appId, $adminId, $introMessage ?: $defaultMsg, $introMessage ? null : $defaultMsgFr]);

            // Email applicant
            if ($app['user_id']) {
                try {
                    \App\Helpers\EmailHelper::sendDriverInterviewInvitation([
                        'first_name'     => $app['first_name'],
                        'email'          => $app['email'],
                        'application_id' => $app['id'],
                        'proposed_times' => $proposedTimes,
                    ]);
                } catch (\Exception $e) { logger('requestInterview email: ' . $e->getMessage(), 'warning'); }
            }

            // Sync CRM lead status
            if ($app['lead_id']) {
                $this->db->prepare("UPDATE leads SET status = 'qualified', updated_at = NOW() WHERE id = ?")->execute([$app['lead_id']]);
                $this->logPipelineActivity(
                    $app['lead_id'],
                    'meeting',
                    "Interview requested for {$app['first_name']} {$app['last_name']}. " . count($proposedTimes) . " time slot(s) proposed.",
                    'driver_pipeline',
                    'Interview Requested',
                    "Interview request sent to {$app['first_name']} {$app['last_name']}.",
                    "/admin/leads/view?id={$app['lead_id']}"
                );
            }

            jsonResponse(['success' => true, 'message' => 'Interview request sent to applicant.']);
        } catch (Exception $e) {
            logger("requestInterview error: " . $e->getMessage(), 'error');
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Send an admin message to a driver applicant
     */
    public function sendApplicationMessage(): void
    {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();

        $appId   = (int) post('application_id', 0);
        $message = trim(post('message', ''));

        if (!$appId || !$message) {
            jsonResponse(['error' => 'Application ID and message are required.'], 422);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT id, email, first_name, user_id FROM driver_applications WHERE id = ? LIMIT 1");
            $stmt->execute([$appId]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) { throw new Exception('Application not found.'); }

            $adminId = $_SESSION['user']['id'] ?? null;

            $this->db->prepare("
                INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message)
                VALUES (?, 'admin', ?, ?)
            ")->execute([$appId, $adminId, htmlspecialchars($message)]);

            // Driver bell + email (approved drivers with portal accounts)
            if ($app['user_id']) {
                try {
                    $this->db->prepare("
                        INSERT INTO driver_delivery_notifications (driver_id, message, type, sent_by, created_at)
                        VALUES (?, ?, 'normal', 0, NOW())
                    ")->execute([
                        $app['user_id'],
                        'New message from OCSAPP — log in to read and reply.',
                    ]);
                } catch (\Exception $e) { /* non-critical */ }
                try {
                    \App\Helpers\EmailHelper::sendRaw(
                        $app['email'],
                        'New Message on Your Driver Application — OCSAPP',
                        "<p>Hi {$app['first_name']},</p>
                         <p>Our team has sent you a message regarding your driver application:</p>
                         <blockquote style='border-left:3px solid #3b82f6;padding-left:12px;color:#374151;'>" . nl2br(htmlspecialchars($message)) . "</blockquote>
                         <p><a href='https://ocsapp.ca/delivery/messages'>Log in to read and reply</a></p>
                         <p>OCSAPP Driver Team</p>"
                    );
                } catch (\Exception $e) { logger('sendApplicationMessage email: ' . $e->getMessage(), 'warning'); }
            }

            // Log to timeline
            $fullApp = $this->db->prepare("SELECT lead_id, first_name, last_name FROM driver_applications WHERE id = ? LIMIT 1");
            $fullApp->execute([$appId]);
            $fullAppRow = $fullApp->fetch(\PDO::FETCH_ASSOC);
            if ($fullAppRow && $fullAppRow['lead_id']) {
                $this->logPipelineActivity(
                    $fullAppRow['lead_id'],
                    'email',
                    "Admin sent a message to {$fullAppRow['first_name']} {$fullAppRow['last_name']}: " . mb_strimwidth($message, 0, 100, '…'),
                    'driver_pipeline',
                    'Message Sent to Applicant',
                    "Message sent to {$fullAppRow['first_name']} {$fullAppRow['last_name']}.",
                    "/admin/leads/view?id={$fullAppRow['lead_id']}"
                );
            }

            jsonResponse(['success' => true, 'message' => 'Message sent.']);
        } catch (Exception $e) {
            logger("sendApplicationMessage error: " . $e->getMessage(), 'error');
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Get messages for a driver application (AJAX)
     */
    public function getApplicationMessages(): void
    {
        $appId = (int) get('application_id', 0);
        if (!$appId) { jsonResponse(['error' => 'Invalid ID'], 422); return; }

        $stmt = $this->db->prepare("
            SELECT * FROM driver_application_messages
            WHERE application_id = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$appId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mark applicant messages as read
        $this->db->prepare("
            UPDATE driver_application_messages
            SET is_read = 1
            WHERE application_id = ? AND sender_type = 'applicant' AND is_read = 0
        ")->execute([$appId]);

        jsonResponse(['success' => true, 'messages' => $messages]);
    }

    /**
     * Approve application (pipeline-aware: update pending user to active)
     */
    public function approveApplicationPipeline(): void
    {
        if (!isPost()) { redirect(url('admin/delivery/staff')); return; }
        verifyCsrfForApi();

        $appId    = (int) post('application_id', 0);
        $password = post('temp_password', '');
        $useExisting = post('use_existing_account', '0') === '1';

        if (!$appId) {
            jsonResponse(['error' => 'Invalid application ID'], 422);
            return;
        }

        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? AND status NOT IN ('approved','rejected') LIMIT 1");
            $stmt->execute([$appId]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) { throw new Exception('Application not found or already processed.'); }

            $adminId = $_SESSION['user']['id'] ?? null;

            if ($app['user_id'] && $useExisting) {
                // Applicant already has a pending account — just activate it
                $this->db->prepare("
                    UPDATE users SET status = 'active', role = 'delivery', updated_at = NOW() WHERE id = ?
                ")->execute([$app['user_id']]);

                // Create driver_availability row
                $this->db->prepare("
                    INSERT IGNORE INTO driver_availability (driver_id, status, max_deliveries) VALUES (?, 'offline', 3)
                ")->execute([$app['user_id']]);

                $credentialNote = "<p>Your account has been activated. Log in at <a href='https://ocsapp.ca/login'>ocsapp.ca/login</a> with your existing credentials.</p>";
                $finalUserId = $app['user_id'];

            } else {
                // Create brand-new user account
                if (strlen($password) < 8) {
                    throw new Exception('Password must be at least 8 characters.');
                }

                $check = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
                $check->execute([$app['email']]);
                $existingId = $check->fetchColumn();

                if ($existingId) {
                    // Activate existing account and force password reset since admin issued a new password
                    $this->db->prepare("UPDATE users SET status = 'active', role = 'delivery', force_password_reset = 1, updated_at = NOW() WHERE id = ?")->execute([$existingId]);
                    $finalUserId = $existingId;
                } else {
                    // Create new
                    $rStmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'delivery' LIMIT 1");
                    $rStmt->execute();
                    $roleId = $rStmt->fetchColumn();
                    if (!$roleId) {
                        $this->db->prepare("INSERT INTO roles (name, display_name, description) VALUES ('delivery','Delivery Driver','Delivery driver role')")->execute();
                        $roleId = $this->db->lastInsertId();
                    }

                    $this->db->prepare("
                        INSERT INTO users (email, password, first_name, last_name, phone, status, role, force_password_reset, email_verified_at, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, 'active', 'delivery', 1, NOW(), NOW(), NOW())
                    ")->execute([
                        $app['email'], password_hash($password, PASSWORD_DEFAULT),
                        $app['first_name'], $app['last_name'], $app['phone'],
                    ]);
                    $finalUserId = $this->db->lastInsertId();
                    $this->db->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?,?)")->execute([$finalUserId, $roleId]);
                }

                $this->db->prepare("INSERT IGNORE INTO driver_availability (driver_id, status, max_deliveries) VALUES (?, 'offline', 3)")->execute([$finalUserId]);
                $credentialNote = "<p><strong>Login:</strong> <a href='https://ocsapp.ca/login'>ocsapp.ca/login</a><br>
                    <strong>Email:</strong> {$app['email']}<br>
                    <strong>Password:</strong> {$password}</p>";
            }

            // Update application record
            $this->db->prepare("
                UPDATE driver_applications
                SET status = 'approved', pipeline_stage = 'approved',
                    user_id = ?, reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ")->execute([$finalUserId, $adminId, $appId]);

            // Update CRM lead if linked
            if ($app['lead_id']) {
                $leadsExist = $this->db->query("SHOW TABLES LIKE 'leads'")->rowCount();
                if ($leadsExist) {
                    $this->db->prepare("UPDATE leads SET status = 'converted', updated_at = NOW() WHERE id = ?")->execute([$app['lead_id']]);
                }
            }

            $this->db->commit();

            // Auto-unlock training module 1 for the approved driver
            try {
                $firstModule = $this->db->query("SELECT id FROM training_modules WHERE is_active=1 ORDER BY order_num ASC LIMIT 1")->fetchColumn();
                if ($firstModule) {
                    $this->db->prepare("INSERT IGNORE INTO driver_training_progress (driver_id, module_id, status, unlocked_at) VALUES (?, ?, 'available', NOW())")->execute([$finalUserId, $firstModule]);
                }
            } catch (\Exception $e) {
                logger('Training unlock on approval failed: ' . $e->getMessage(), 'warning');
            }

            // Log to timeline + admin notification
            if ($app['lead_id']) {
                $this->logPipelineActivity(
                    $app['lead_id'],
                    'status_change',
                    "Application #{$app['id']} for {$app['first_name']} {$app['last_name']} APPROVED. Driver account activated.",
                    'driver_approved',
                    'Driver Application Approved',
                    "{$app['first_name']} {$app['last_name']} has been approved as a delivery driver.",
                    "/admin/leads/view?id={$app['lead_id']}"
                );
            }

            // Send congratulations email
            try {
                \App\Helpers\EmailHelper::sendDriverApproved([
                    'first_name'   => $app['first_name'],
                    'email'        => $app['email'],
                    'use_existing' => $useExisting,
                    'password'     => $useExisting ? null : $password,
                ]);
            } catch (\Exception $e) { logger("Driver approval email failed: " . $e->getMessage(), 'warning'); }

            jsonResponse(['success' => true, 'message' => 'Driver approved and account activated.']);

        } catch (Exception $e) {
            $this->db->rollBack();
            logger("approveApplicationPipeline error: " . $e->getMessage(), 'error');
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Reject application (pipeline-aware: set users.status = rejected, add 90-day cooldown)
     */
    public function rejectApplicationPipeline(): void
    {
        if (!isPost()) { redirect(url('admin/delivery/staff')); return; }
        verifyCsrfForApi();

        $appId  = (int) post('application_id', 0);
        $reason = sanitize(post('reason', ''));

        if (!$appId) {
            jsonResponse(['error' => 'Invalid application ID'], 422);
            return;
        }

        try {
            $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? AND status NOT IN ('approved','rejected') LIMIT 1");
            $stmt->execute([$appId]);
            $app = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$app) { throw new Exception('Application not found or already processed.'); }

            $adminId    = $_SESSION['user']['id'] ?? null;
            $reapplyAfter = date('Y-m-d', strtotime('+90 days'));

            $this->db->prepare("
                UPDATE driver_applications
                SET status = 'rejected', pipeline_stage = 'rejected',
                    rejection_reason = ?, reapply_after = ?,
                    reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW()
                WHERE id = ?
            ")->execute([$reason ?: null, $reapplyAfter, $adminId, $appId]);

            // Set pending user to rejected
            if ($app['user_id']) {
                $this->db->prepare("UPDATE users SET status = 'rejected', updated_at = NOW() WHERE id = ? AND status = 'pending'")->execute([$app['user_id']]);
            }

            // Update CRM lead
            if ($app['lead_id']) {
                $leadsExist = $this->db->query("SHOW TABLES LIKE 'leads'")->rowCount();
                if ($leadsExist) {
                    $this->db->prepare("UPDATE leads SET status = 'lost', updated_at = NOW() WHERE id = ?")->execute([$app['lead_id']]);
                }
            }

            // Log to timeline + admin notification
            if ($app['lead_id']) {
                $detail = $reason ? " Reason: {$reason}" : '';
                $this->logPipelineActivity(
                    $app['lead_id'],
                    'status_change',
                    "Application #{$app['id']} for {$app['first_name']} {$app['last_name']} REJECTED.{$detail} Can reapply after {$reapplyAfter}.",
                    'driver_rejected',
                    'Driver Application Rejected',
                    "{$app['first_name']} {$app['last_name']}'s driver application was rejected.",
                    "/admin/leads/view?id={$app['lead_id']}"
                );
            }

            // Send rejection email
            try {
                $reasonHtml = $reason ? "<p><strong>Reason:</strong> " . htmlspecialchars($reason) . "</p>" : '';
                \App\Helpers\EmailHelper::sendRaw(
                    $app['email'],
                    'Update on Your OCSAPP Driver Application',
                    "<p>Hi {$app['first_name']},</p>
                     <p>Thank you for applying to join the OCSAPP delivery team. After careful review, we're unable to move forward with your application at this time.</p>
                     {$reasonHtml}
                     <p>You're welcome to reapply after " . date('F j, Y', strtotime($reapplyAfter)) . ".</p>
                     <p>Thank you for your interest.<br>OCSAPP Team</p>"
                );
            } catch (\Exception $e) { logger("Driver rejection email failed: " . $e->getMessage(), 'warning'); }

            jsonResponse(['success' => true, 'message' => 'Application rejected.']);

        } catch (Exception $e) {
            logger("rejectApplicationPipeline error: " . $e->getMessage(), 'error');
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Add New Delivery Driver
     */
    public function addDriver() {
        if (!isPost()) {
            return view('admin/delivery/add-driver', [
                'pageTitle' => 'Add Delivery Driver'
            ]);
        }
        
        try {
            $this->db->beginTransaction();
            
            // Validate input
            $email = post('email');
            $firstName = post('first_name');
            $lastName = post('last_name');
            $phone = post('phone');
            $password = post('password');
            
            if (!$email || !$firstName || !$lastName || !$password) {
                throw new Exception('All fields are required');
            }
            
            // Check if email exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new Exception('Email already exists');
            }
            
            // Create user
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("
                INSERT INTO users (email, password, first_name, last_name, phone, status)
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$email, $hashedPassword, $firstName, $lastName, $phone]);
            $userId = $this->db->lastInsertId();
            
            // Assign delivery role
            $stmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'delivery'");
            $stmt->execute();
            $roleId = $stmt->fetchColumn();
            
            if (!$roleId) {
                // Create delivery role if it doesn't exist
                $stmt = $this->db->prepare("
                    INSERT INTO roles (name, display_name, description)
                    VALUES ('delivery', 'Delivery Driver', 'Delivery driver role')
                ");
                $stmt->execute();
                $roleId = $this->db->lastInsertId();
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)
            ");
            $stmt->execute([$userId, $roleId]);
            
            // Create driver availability record
            $stmt = $this->db->prepare("
                INSERT INTO driver_availability (driver_id, status, max_deliveries)
                VALUES (?, 'offline', 3)
            ");
            $stmt->execute([$userId]);
            
            $this->db->commit();

            try {
                \App\Helpers\EmailHelper::sendRaw(
                    $email,
                    'Welcome to the OCSAPP Delivery Team!',
                    "<p>Hi {$firstName},</p>
                     <p>Your OCSAPP driver account has been created. You can log in and start accepting deliveries right away.</p>
                     <p><strong>Login:</strong> <a href='https://ocsapp.ca/delivery/login'>ocsapp.ca/delivery/login</a><br>
                     <strong>Email:</strong> {$email}<br>
                     <strong>Password:</strong> {$password}</p>
                     <p>Please change your password after your first login.</p>
                     <p>Welcome aboard!<br>OCSAPP Delivery Team</p>"
                );
            } catch (\Exception $e) {
                logger('Admin-added driver welcome email failed: ' . $e->getMessage(), 'warning');
            }

            setFlash('success', 'Delivery driver added successfully');
            redirect(url('admin/delivery/staff'));
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlash('error', $e->getMessage());
            redirect(url('admin/delivery/add-driver'));
        }
    }
    
    /**
     * Show edit driver form (GET)
     */
    public function editDriver() {
        $driverId = (int) get('id', 0);
        
        if (!$driverId) {
            setFlash('error', 'Driver ID is required');
            redirect(url('admin/delivery/staff'));
            return;
        }

        try {
            // Get driver details with availability settings
            $stmt = $this->db->prepare("
                SELECT u.*,
                       da.max_deliveries,
                       da.zone_id
                FROM users u
                INNER JOIN user_roles ur ON u.id = ur.user_id
                INNER JOIN roles r ON ur.role_id = r.id
                LEFT JOIN driver_availability da ON u.id = da.driver_id
                WHERE u.id = ? AND r.name = 'delivery'
            ");
            $stmt->execute([$driverId]);
            $driver = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$driver) {
                setFlash('error', 'Driver not found');
                redirect(url('admin/delivery/staff'));
                return;
            }
            
            // Get all delivery zones
            $stmt = $this->db->prepare("
                SELECT * FROM delivery_zones 
                WHERE is_active = 1 
                ORDER BY name
            ");
            $stmt->execute();
            $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return view('admin/delivery/edit-driver', [
                'driver' => $driver,
                'zones' => $zones,
                'pageTitle' => 'Edit Driver'
            ]);
            
        } catch (Exception $e) {
            logger("Edit driver error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading driver: ' . $e->getMessage());
            redirect(url('admin/delivery/staff'));
        }
    }

    /**
     * Update delivery driver (POST)
     */
    public function updateDriver() {
        if (!isPost()) {
            redirect(url('admin/delivery/staff'));
            return;
        }
        
        $driverId = (int) post('driver_id', 0);
        
        if (!$driverId) {
            setFlash('error', 'Driver ID is required');
            back();
            return;
        }

        try {
            $this->db->beginTransaction();
            
            // Update user details
            $stmt = $this->db->prepare("
                UPDATE users 
                SET first_name = ?, 
                    last_name = ?, 
                    phone = ?, 
                    status = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            
            $stmt->execute([
                post('first_name'),
                post('last_name'),
                post('phone'),
                post('status', 'active'),
                $driverId
            ]);
            
            // Update or insert driver-specific settings
            $maxDeliveries = (int) post('max_deliveries', 3);
            $zoneId = post('zone_id', null);
            if ($zoneId === '') $zoneId = null;
            
            // Check if driver_availability record exists
            $stmt = $this->db->prepare("SELECT id FROM driver_availability WHERE driver_id = ?");
            $stmt->execute([$driverId]);
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing record
                $stmt = $this->db->prepare("
                    UPDATE driver_availability 
                    SET max_deliveries = ?,
                        zone_id = ?,
                        updated_at = NOW()
                    WHERE driver_id = ?
                ");
                $stmt->execute([$maxDeliveries, $zoneId, $driverId]);
            } else {
                // Create new record
                $stmt = $this->db->prepare("
                    INSERT INTO driver_availability 
                    (driver_id, status, max_deliveries, zone_id, active_deliveries)
                    VALUES (?, 'offline', ?, ?, 0)
                ");
                $stmt->execute([$driverId, $maxDeliveries, $zoneId]);
            }
            
            $this->db->commit();

            // Notify driver by email when their account is deactivated or suspended
            $newDriverStatus = post('status', 'active');
            if (in_array($newDriverStatus, ['inactive', 'suspended'])) {
                try {
                    $driverRow = $this->db->prepare("SELECT first_name, email FROM users WHERE id = ? LIMIT 1");
                    $driverRow->execute([$driverId]);
                    $driverInfo = $driverRow->fetch(\PDO::FETCH_ASSOC);
                    if ($driverInfo) {
                        $statusDesc = $newDriverStatus === 'inactive' ? 'deactivated' : 'suspended';
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

            setFlash('success', 'Driver updated successfully');
            redirect(url('admin/delivery/edit-driver?id=' . $driverId));
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger("Update driver error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error updating driver: ' . $e->getMessage());
            back();
        }
    }
    
    /**
     * Active Deliveries (Unified B2C + B2B)
     */
    public function activeDeliveries() {
        $status = get('status', 'all');
        $type = get('type', 'all'); // all | orders | distribution

        $deliveries = [];

        // --- B2C Order Deliveries ---
        if ($type === 'all' || $type === 'orders') {
            $whereClause = "WHERE da.delivery_type = 'order' AND da.order_id IS NOT NULL";
            $params = [];

            if ($status !== 'all') {
                $whereClause .= " AND da.status = ?";
                $params[] = $status;
            } else {
                $whereClause .= " AND da.status NOT IN ('delivered', 'cancelled', 'failed')";
            }

            $query = "
                SELECT
                    da.*,
                    'order' as source_type,
                    o.order_number as reference_number,
                    o.total,
                    u.first_name as customer_first_name,
                    u.last_name as customer_last_name,
                    u.phone as customer_phone,
                    COALESCE(d.first_name, 'Unassigned') as driver_first_name,
                    COALESCE(d.last_name, '') as driver_last_name,
                    d.phone as driver_phone,
                    s.name as shop_name
                FROM delivery_assignments da
                JOIN orders o ON da.order_id = o.id
                JOIN users u ON o.user_id = u.id
                LEFT JOIN users d ON da.driver_id = d.id
                LEFT JOIN shops s ON da.shop_id = s.id
                $whereClause
                ORDER BY da.created_at DESC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            $deliveries = array_merge($deliveries, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        // --- B2B Distribution Deliveries ---
        if ($type === 'all' || $type === 'distribution') {
            // Get distribution requests that are in delivery-relevant statuses
            // These may or may not have a delivery_assignment record
            $distWhere = "WHERE 1=1";
            $distParams = [];

            if ($status !== 'all') {
                // Map delivery_assignment statuses to distribution statuses
                if ($status === 'assigned' || $status === 'accepted') {
                    $distWhere .= " AND dr.status = 'ready'";
                } elseif ($status === 'picked_up' || $status === 'on_the_way') {
                    $distWhere .= " AND dr.status = 'in_transit'";
                } else {
                    $distWhere .= " AND dr.status IN ('processing', 'ready', 'in_transit')";
                }
            } else {
                $distWhere .= " AND dr.status IN ('processing', 'ready', 'in_transit')";
            }

            $distQuery = "
                SELECT
                    dr.id as distribution_request_id,
                    dr.request_number as reference_number,
                    dr.status as dist_status,
                    dr.total_amount as total,
                    dr.delivery_street, dr.delivery_city, dr.delivery_postal_code,
                    dr.created_at,
                    'distribution' as source_type,
                    bp.company_name as shop_name,
                    u.first_name as customer_first_name,
                    u.last_name as customer_last_name,
                    u.phone as customer_phone,
                    da_link.id as delivery_assignment_id,
                    da_link.status as da_status,
                    da_link.driver_id,
                    COALESCE(drv.first_name, 'Unassigned') as driver_first_name,
                    COALESCE(drv.last_name, '') as driver_last_name,
                    drv.phone as driver_phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                LEFT JOIN delivery_assignments da_link ON da_link.distribution_request_id = dr.id AND da_link.delivery_type = 'distribution'
                LEFT JOIN users drv ON da_link.driver_id = drv.id
                $distWhere
                ORDER BY dr.created_at DESC
            ";

            $stmt = $this->db->prepare($distQuery);
            $stmt->execute($distParams);
            $distDeliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Normalize distribution rows to match the delivery format
            foreach ($distDeliveries as &$dd) {
                $dd['delivery_type'] = 'distribution';
                $dd['id'] = $dd['delivery_assignment_id'] ?? null;
                $dd['status'] = $dd['da_status'] ?? $this->mapDistStatusToDeliveryStatus($dd['dist_status']);
                $dd['delivery_address'] = trim(($dd['delivery_street'] ?? '') . ', ' . ($dd['delivery_city'] ?? '') . ' ' . ($dd['delivery_postal_code'] ?? ''));
            }
            unset($dd);

            $deliveries = array_merge($deliveries, $distDeliveries);
        }

        // Sort combined results by created_at DESC
        usort($deliveries, function($a, $b) {
            return strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0');
        });

        // --- PO Pickups (purchase_orders assigned to a driver) ---
        // Only show when the status filter is relevant to PO pickups
        $poStatusMap = match($status) {
            'all'        => "'ready_for_pickup','picked_up'",
            'assigned'   => "'ready_for_pickup'",
            'picked_up'  => "'picked_up'",
            default      => null, // hide PO pickups for unrelated filters
        };

        $poPickups = [];
        if ($poStatusMap !== null) {
            $poPickupsStmt = $this->db->query("
                SELECT
                    po.id,
                    po.po_number,
                    po.status,
                    po.total_amount,
                    po.driver_assigned_at,
                    s.company_name AS supplier_name,
                    CONCAT_WS(', ', s.address, s.city, s.province, s.postal_code) AS supplier_address,
                    CONCAT(d.first_name, ' ', d.last_name) AS driver_name,
                    d.phone AS driver_phone
                FROM purchase_orders po
                JOIN suppliers s ON s.id = po.supplier_id
                JOIN users d ON d.id = po.assigned_driver_id
                WHERE po.assigned_driver_id IS NOT NULL
                  AND po.status IN ($poStatusMap)
                ORDER BY po.driver_assigned_at DESC
            ");
            $poPickups = $poPickupsStmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return view('admin/delivery/active', [
            'deliveries' => $deliveries,
            'poPickups'  => $poPickups,
            'selectedStatus' => $status,
            'selectedType' => $type,
            'pageTitle' => 'Active Deliveries'
        ]);
    }

    /**
     * Map distribution request status to delivery assignment status equivalent
     */
    private function mapDistStatusToDeliveryStatus(string $distStatus): string {
        return match($distStatus) {
            'processing' => 'pending',
            'ready' => 'assigned',
            'in_transit' => 'on_the_way',
            'completed' => 'delivered',
            default => 'pending'
        };
    }
    
    /**
     * Single Delivery Details
     */
    public function deliveryDetails() {
        $deliveryId = (int) get('id', 0);

        if (!$deliveryId) {
            setFlash('error', 'Delivery ID is required');
            redirect(url('admin/delivery/active'));
            return;
        }

        try {
            // Get delivery details with all related info
            $stmt = $this->db->prepare("
                SELECT
                    da.*,
                    o.order_number,
                    o.total as order_total,
                    o.subtotal,
                    o.tax,
                    o.status as order_status,
                    o.notes as order_notes,
                    o.created_at as order_created_at,
                    c.first_name as customer_first_name,
                    c.last_name as customer_last_name,
                    c.email as customer_email,
                    c.phone as customer_phone,
                    COALESCE(d.first_name, 'Unassigned') as driver_first_name,
                    COALESCE(d.last_name, '') as driver_last_name,
                    d.email as driver_email,
                    d.phone as driver_phone,
                    s.name as shop_name,
                    s.address as shop_address,
                    s.phone as shop_phone
                FROM delivery_assignments da
                JOIN orders o ON da.order_id = o.id
                JOIN users c ON o.user_id = c.id
                LEFT JOIN users d ON da.driver_id = d.id
                JOIN shops s ON da.shop_id = s.id
                WHERE da.id = ?
            ");
            $stmt->execute([$deliveryId]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$delivery) {
                setFlash('error', 'Delivery not found');
                redirect(url('admin/delivery/active'));
                return;
            }

            // Get order items
            $stmt = $this->db->prepare("
                SELECT
                    oi.*,
                    p.name as product_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as product_image
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$delivery['order_id']]);
            $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get delivery status history
            $stmt = $this->db->prepare("
                SELECT
                    dsh.*,
                    u.first_name,
                    u.last_name
                FROM delivery_status_history dsh
                LEFT JOIN users u ON dsh.created_by = u.id
                WHERE dsh.delivery_id = ?
                ORDER BY dsh.created_at DESC
            ");
            $stmt->execute([$deliveryId]);
            $statusHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get available drivers for reassignment
            $stmt = $this->db->prepare("
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    da.status as availability_status,
                    da.active_deliveries
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                LEFT JOIN driver_availability da ON u.id = da.driver_id
                JOIN driver_api_tokens dat ON dat.user_id = u.id AND dat.driver_online = 1
                WHERE r.name = 'delivery'
                AND u.status = 'active'
                ORDER BY u.first_name
            ");
            $stmt->execute();
            $availableDrivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return view('admin/delivery/details', [
                'delivery' => $delivery,
                'orderItems' => $orderItems,
                'statusHistory' => $statusHistory,
                'availableDrivers' => $availableDrivers,
                'pageTitle' => 'Delivery Details'
            ]);

        } catch (Exception $e) {
            logger("Delivery details error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading delivery: ' . $e->getMessage());
            redirect(url('admin/delivery/active'));
        }
    }

    /**
     * Assign Delivery to Driver
     */
    public function assignDelivery() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }
        
        try {
            $orderId = post('order_id');
            $driverId = post('driver_id');
            
            $this->db->beginTransaction();
            
            // Get order details
            $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                throw new Exception('Order not found');
            }
            
            // Check if driver is available
            $stmt = $this->db->prepare("
                SELECT * FROM driver_availability 
                WHERE driver_id = ? AND status IN ('available', 'busy')
            ");
            $stmt->execute([$driverId]);
            $availability = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$availability) {
                throw new Exception('Driver is not available');
            }
            
            // Create delivery assignment
            $stmt = $this->db->prepare("
                INSERT INTO delivery_assignments 
                (order_id, driver_id, shop_id, status, delivery_fee, delivery_address, customer_phone)
                VALUES (?, ?, ?, 'assigned', ?, ?, ?)
            ");
            $stmt->execute([
                $orderId,
                $driverId,
                $order['shop_id'],
                $order['delivery_fee'] ?? 50.00,
                post('delivery_address'),
                post('customer_phone')
            ]);
            
            $deliveryId = $this->db->lastInsertId();
            
            // Update order status
            $stmt = $this->db->prepare("
                UPDATE orders SET status = 'processing' WHERE id = ?
            ");
            $stmt->execute([$orderId]);
            
            // Add to history
            $stmt = $this->db->prepare("
                INSERT INTO delivery_status_history 
                (delivery_id, status, notes, created_by)
                VALUES (?, 'assigned', 'Assigned by admin', ?)
            ");
            $stmt->execute([$deliveryId, userId()]);
            
            $this->db->commit();

            // Send driver assignment email
            try {
                $driverStmt = $this->db->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
                $driverStmt->execute([$driverId]);
                $driver = $driverStmt->fetch(PDO::FETCH_ASSOC);

                if ($driver && !empty($driver['email'])) {
                    $shopStmt = $this->db->prepare("SELECT name, address, phone FROM shops WHERE id = ?");
                    $shopStmt->execute([$order['shop_id']]);
                    $shop = $shopStmt->fetch(PDO::FETCH_ASSOC);

                    $custStmt = $this->db->prepare("SELECT first_name, last_name, phone FROM users WHERE id = ?");
                    $custStmt->execute([$order['user_id']]);
                    $customer = $custStmt->fetch(PDO::FETCH_ASSOC);

                    \App\Helpers\EmailHelper::sendDeliveryAssignment([
                        'driver_email'     => $driver['email'],
                        'driver_name'      => trim($driver['first_name'] . ' ' . $driver['last_name']),
                        'order_number'     => $order['order_number'],
                        'order_total'      => $order['total'],
                        'shop_name'        => $shop['name'] ?? '',
                        'shop_address'     => $shop['address'] ?? '',
                        'shop_phone'       => $shop['phone'] ?? '',
                        'shop_contact'     => $shop['name'] ?? '',
                        'customer_name'    => trim(($customer['first_name'] ?? '') . ' ' . ($customer['last_name'] ?? '')),
                        'delivery_address' => post('delivery_address'),
                        'customer_phone'   => post('customer_phone'),
                        'delivery_id'      => $deliveryId,
                        'items_count'      => '—',
                        'items_list'       => 'See delivery details in the app',
                    ]);
                }
            } catch (Exception $e) {
                error_log('Driver assignment email error: ' . $e->getMessage());
            }

            return jsonResponse([
                'success' => true,
                'message' => 'Delivery assigned successfully'
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Assign driver to a B2B distribution request
     */
    public function assignDistributionDriver() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $requestId = (int) ($input['distribution_request_id'] ?? 0);
            $driverId = (int) ($input['driver_id'] ?? 0);

            if (!$requestId || !$driverId) {
                throw new Exception('Distribution request ID and Driver ID are required');
            }

            $this->db->beginTransaction();

            // Get distribution request with delivery info
            $stmt = $this->db->prepare("
                SELECT dr.*, bp.company_name,
                       dr.delivery_street, dr.delivery_city, dr.delivery_province, dr.delivery_postal_code,
                       u.phone as customer_phone
                FROM distribution_requests dr
                INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
                INNER JOIN users u ON bp.user_id = u.id
                WHERE dr.id = ? AND dr.status IN ('processing', 'ready', 'paid')
            ");
            $stmt->execute([$requestId]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                throw new Exception('Distribution request not found or not in a valid status for driver assignment');
            }

            // Check if driver exists and has delivery role
            $stmt = $this->db->prepare("
                SELECT u.id FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND r.name = 'delivery' AND u.status = 'active'
            ");
            $stmt->execute([$driverId]);
            if (!$stmt->fetch()) {
                throw new Exception('Invalid driver');
            }

            // Block assignment if driver has not completed training
            $certStmt = $this->db->prepare("SELECT id FROM driver_certificates WHERE driver_id = ? LIMIT 1");
            $certStmt->execute([$driverId]);
            if (!$certStmt->fetchColumn()) {
                throw new Exception('This driver has not completed their training and cannot be assigned to deliveries.');
            }

            // Check if there's already a delivery assignment for this distribution request
            $stmt = $this->db->prepare("
                SELECT id FROM delivery_assignments
                WHERE distribution_request_id = ? AND delivery_type = 'distribution'
                AND status NOT IN ('cancelled', 'failed')
            ");
            $stmt->execute([$requestId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            $deliveryAddress = trim(
                ($request['delivery_street'] ?? '') . ', ' .
                ($request['delivery_city'] ?? '') . ' ' .
                ($request['delivery_province'] ?? '') . ' ' .
                ($request['delivery_postal_code'] ?? '')
            );

            if ($existing) {
                // Reassign existing delivery
                $stmt = $this->db->prepare("
                    UPDATE delivery_assignments
                    SET driver_id = ?, status = 'assigned', assigned_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$driverId, $existing['id']]);
                $deliveryId = $existing['id'];
            } else {
                // Create new delivery assignment
                $stmt = $this->db->prepare("
                    INSERT INTO delivery_assignments
                    (delivery_type, distribution_request_id, driver_id, status,
                     delivery_fee, delivery_address, customer_phone, pickup_address, assigned_at)
                    VALUES ('distribution', ?, ?, 'assigned', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $requestId,
                    $driverId,
                    $request['delivery_fee'] ?? 0,
                    $deliveryAddress,
                    $request['customer_phone'] ?? '',
                    $request['company_name'] . ' (Supplier pickup)'
                ]);
                $deliveryId = $this->db->lastInsertId();
            }

            // Update distribution request status to 'ready' if it was 'processing'
            if ($request['status'] === 'processing' || $request['status'] === 'paid') {
                $oldStatus = $request['status'];
                $stmt = $this->db->prepare("
                    UPDATE distribution_requests
                    SET status = 'ready', updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$requestId]);

                // Log to distribution status history
                $stmt = $this->db->prepare("
                    INSERT INTO distribution_status_history
                    (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                    VALUES (?, ?, 'ready', 'admin', ?, 'Driver assigned for delivery', NOW())
                ");
                $stmt->execute([$requestId, $oldStatus, userId()]);
            }

            // Log to delivery status history
            $stmt = $this->db->prepare("
                INSERT INTO delivery_status_history
                (delivery_id, status, notes, created_by)
                VALUES (?, 'assigned', 'Driver assigned to distribution delivery by admin', ?)
            ");
            $stmt->execute([$deliveryId, userId()]);

            $this->db->commit();

            return jsonResponse([
                'success' => true,
                'message' => 'Driver assigned to distribution delivery successfully'
            ]);

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Delivery Zones Management
     */
    public function zones() {
        $stmt = $this->db->prepare("
            SELECT 
                dz.*,
                (SELECT COUNT(*) FROM driver_availability WHERE zone_id = dz.id) as driver_count
            FROM delivery_zones dz
            ORDER BY dz.priority ASC, dz.name ASC
        ");
        $stmt->execute();
        $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return view('admin/delivery/zones', [
            'zones' => $zones,
            'pageTitle' => 'Delivery Zones'
        ]);
    }

    public function getZone(): void {
        header('Content-Type: application/json');
        $id = (int) get('id', 0);
        if (!$id) { echo json_encode(['error' => 'Invalid ID']); return; }
        $stmt = $this->db->prepare("SELECT * FROM delivery_zones WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $zone = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($zone ?: ['error' => 'Not found']);
    }

    public function createZone(): void {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();
        $name     = trim(post('name', ''));
        $code     = strtoupper(trim(post('code', '')));
        $city     = trim(post('city', ''));
        if (!$name || !$code || !$city) {
            jsonResponse(['error' => 'Name, code and city are required.'], 422); return;
        }
        try {
            $this->db->prepare("
                INSERT INTO delivery_zones
                    (name, code, city, state, country, base_fee, per_km_fee, max_distance_km,
                     estimated_time, priority, notes, is_active, created_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,NOW())
            ")->execute([
                $name, $code, $city,
                trim(post('state', '')),
                trim(post('country', 'Dominican Republic')) ?: 'Dominican Republic',
                (float) post('base_fee', 0),
                (float) post('per_km_fee', 0),
                (float) post('max_distance_km', 0),
                (int)   post('estimated_time', 30),
                (int)   post('priority', 0),
                trim(post('notes', '')),
                post('is_active') ? 1 : 0,
            ]);
            jsonResponse(['success' => true, 'message' => 'Zone created.']);
        } catch (\Exception $e) {
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function updateZone(): void {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();
        $id   = (int) post('id', 0);
        $name = trim(post('name', ''));
        $code = strtoupper(trim(post('code', '')));
        $city = trim(post('city', ''));
        if (!$id || !$name || !$code || !$city) {
            jsonResponse(['error' => 'ID, name, code and city are required.'], 422); return;
        }
        try {
            $this->db->prepare("
                UPDATE delivery_zones SET
                    name=?, code=?, city=?, state=?, country=?,
                    base_fee=?, per_km_fee=?, max_distance_km=?,
                    estimated_time=?, priority=?, notes=?, is_active=?
                WHERE id=?
            ")->execute([
                $name, $code, $city,
                trim(post('state', '')),
                trim(post('country', 'Dominican Republic')) ?: 'Dominican Republic',
                (float) post('base_fee', 0),
                (float) post('per_km_fee', 0),
                (float) post('max_distance_km', 0),
                (int)   post('estimated_time', 30),
                (int)   post('priority', 0),
                trim(post('notes', '')),
                post('is_active') ? 1 : 0,
                $id,
            ]);
            jsonResponse(['success' => true, 'message' => 'Zone updated.']);
        } catch (\Exception $e) {
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    public function toggleZone(): void {
        if (!isPost()) { jsonResponse(['error' => 'Method not allowed'], 405); return; }
        verifyCsrfForApi();
        $id       = (int) post('id', 0);
        $activate = post('activate') === '1';
        if (!$id) { jsonResponse(['error' => 'Invalid ID'], 422); return; }
        try {
            $this->db->prepare("UPDATE delivery_zones SET is_active=? WHERE id=?")
                     ->execute([$activate ? 1 : 0, $id]);
            jsonResponse(['success' => true, 'message' => $activate ? 'Zone activated.' : 'Zone deactivated.']);
        } catch (\Exception $e) {
            jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Analytics & Reports
     */
    public function analytics() {
        $period = get('period', 'week');
        
        // Get performance metrics
        $metrics = $this->getPerformanceMetrics($period);
        
        // Get top drivers
        $topDrivers = $this->getTopDrivers(10, $period);
        
        // Get delivery trends
        $trends = $this->getDeliveryTrends($period);
        
        return view('admin/delivery/analytics', [
            'metrics' => $metrics,
            'topDrivers' => $topDrivers,
            'trends' => $trends,
            'period' => $period,
            'pageTitle' => 'Delivery Analytics'
        ]);
    }
    
    /**
     * Live Map — real-time driver positions
     */
    public function liveMap() {
        return view('admin/delivery/live-map', [
            'pageTitle' => 'Live Driver Map',
            'gmapsKey'  => env('GOOGLE_MAPS_KEY', 'AIzaSyB43koHaoLagCIiwoEydQXPoQAfglYGTqY'),
        ]);
    }

    /**
     * Route Replay — pick a driver + date and replay their GPS path
     */
    public function routeReplay() {
        // Get all drivers that have location log entries
        $drivers = $this->db->query("
            SELECT DISTINCT u.id, CONCAT(u.first_name, ' ', u.last_name) AS name
            FROM driver_location_log l
            JOIN users u ON u.id = l.driver_id
            ORDER BY name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        return view('admin/delivery/route-replay', [
            'pageTitle' => 'Route Replay',
            'drivers'   => $drivers,
            'gmapsKey'  => env('GOOGLE_MAPS_KEY', 'AIzaSyB43koHaoLagCIiwoEydQXPoQAfglYGTqY'),
        ]);
    }

    /**
     * Route Replay Data API — returns GPS pings for a driver on a given date
     */
    public function replayData() {
        header('Content-Type: application/json');

        $driverId = (int) ($_GET['driver_id'] ?? 0);
        $date     = $_GET['date'] ?? date('Y-m-d');

        if (!$driverId) {
            echo json_encode(['error' => 'driver_id required']);
            return;
        }

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            echo json_encode(['error' => 'Invalid date format']);
            return;
        }

        $stmt = $this->db->prepare("
            SELECT
                latitude, longitude, heading, speed, accuracy,
                created_at AS ts
            FROM driver_location_log
            WHERE driver_id = ?
              AND DATE(created_at) = ?
            ORDER BY created_at ASC
        ");
        $stmt->execute([$driverId, $date]);
        $points = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Also get available dates for this driver
        $datesStmt = $this->db->prepare("
            SELECT DISTINCT DATE(created_at) AS date, COUNT(*) AS pings
            FROM driver_location_log
            WHERE driver_id = ?
            GROUP BY DATE(created_at)
            ORDER BY date DESC
            LIMIT 30
        ");
        $datesStmt->execute([$driverId]);
        $dates = $datesStmt->fetchAll(\PDO::FETCH_ASSOC);

        // Driver name
        $nameStmt = $this->db->prepare("SELECT CONCAT(first_name, ' ', last_name) AS name FROM users WHERE id = ?");
        $nameStmt->execute([$driverId]);
        $driverName = $nameStmt->fetchColumn();

        echo json_encode([
            'driver'   => $driverName,
            'date'     => $date,
            'points'   => $points,
            'dates'    => $dates,
            'total'    => count($points),
        ]);
    }

    /**
     * Route Optimizer — plan multi-stop routes
     */
    public function routeOptimizer() {
        // Get unassigned/pending deliveries
        $stmt = $this->db->prepare("
            SELECT da.id, da.delivery_address, da.pickup_address, da.status, da.tracking_code,
                   COALESCE(s.name, 'N/A') as shop_name,
                   s.latitude as shop_lat, s.longitude as shop_lng,
                   COALESCE(o.order_number, CONCAT('DR-', da.distribution_request_id)) as order_number
            FROM delivery_assignments da
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            WHERE da.status IN ('pending', 'assigned')
            ORDER BY da.created_at ASC
        ");
        $stmt->execute();
        $deliveries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get available drivers
        $stmt = $this->db->prepare("
            SELECT dav.driver_id, u.first_name, u.last_name, dav.active_deliveries, dav.max_deliveries
            FROM driver_availability dav
            JOIN users u ON dav.driver_id = u.id
            JOIN driver_api_tokens dat ON dat.user_id = dav.driver_id AND dat.driver_online = 1
            WHERE dav.status = 'available'
            ORDER BY u.first_name
        ");
        $stmt->execute();
        $drivers = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return view('admin/delivery/route-optimizer', [
            'deliveries' => $deliveries,
            'drivers' => $drivers,
            'pageTitle' => 'Route Optimizer'
        ]);
    }

    /**
     * Optimize Route — API endpoint for route optimization
     */
    public function optimizeRoute() {
        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $deliveryIds = $input['delivery_ids'] ?? [];

        if (empty($deliveryIds)) {
            echo json_encode(['error' => 'No deliveries selected']);
            return;
        }

        $placeholders = implode(',', array_fill(0, count($deliveryIds), '?'));
        $stmt = $this->db->prepare("
            SELECT da.id, da.delivery_address, da.pickup_address,
                   s.latitude as shop_lat, s.longitude as shop_lng, s.name as shop_name
            FROM delivery_assignments da
            LEFT JOIN shops s ON da.shop_id = s.id
            WHERE da.id IN ({$placeholders})
        ");
        $stmt->execute($deliveryIds);
        $deliveries = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Build waypoints from shop coordinates
        $waypoints = [];
        foreach ($deliveries as $d) {
            if ($d['shop_lat'] && $d['shop_lng']) {
                $waypoints[] = [
                    'lat' => (float)$d['shop_lat'],
                    'lng' => (float)$d['shop_lng'],
                    'id' => $d['id'],
                    'name' => $d['shop_name']
                ];
            }
        }

        if (count($waypoints) < 2) {
            echo json_encode(['error' => 'Need at least 2 deliveries with geocoded shops']);
            return;
        }

        // Use GeocodingHelper to optimize
        require_once __DIR__ . '/../Helpers/GeocodingHelper.php';
        $supplierCoords = array_map(fn($w) => ['lat' => $w['lat'], 'lng' => $w['lng']], $waypoints);
        $customerCoord = end($supplierCoords); // Use last as pseudo-destination
        $optimized = \GeocodingHelper::optimizeRoute($supplierCoords, $customerCoord);
        $totalDistance = \GeocodingHelper::calculateRouteDistance($optimized);

        // Map back to delivery IDs in optimized order
        $orderedIds = [];
        foreach ($optimized as $coord) {
            foreach ($waypoints as $w) {
                if (abs($w['lat'] - $coord['lat']) < 0.0001 && abs($w['lng'] - $coord['lng']) < 0.0001) {
                    if (!in_array($w['id'], $orderedIds)) {
                        $orderedIds[] = $w['id'];
                    }
                    break;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'route' => $optimized,
            'ordered_delivery_ids' => $orderedIds,
            'total_distance_km' => $totalDistance,
            'estimated_time_min' => round($totalDistance / 30 * 60) // ~30 km/h avg
        ]);
    }

    /**
     * Export earnings as CSV
     */
    public function exportEarnings() {
        $status = post('status', '');
        $driverId = post('driver_id', '');
        $dateFrom = post('date_from', '');
        $dateTo = post('date_to', '');
        $earningIds = post('earning_ids', []);

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($earningIds)) {
            $placeholders = implode(',', array_fill(0, count($earningIds), '?'));
            $where .= " AND de.id IN ({$placeholders})";
            $params = array_merge($params, $earningIds);
        } else {
            if ($status) {
                $where .= " AND de.payment_status = ?";
                $params[] = $status;
            }
            if ($driverId) {
                $where .= " AND de.driver_id = ?";
                $params[] = $driverId;
            }
            if ($dateFrom) {
                $where .= " AND de.created_at >= ?";
                $params[] = $dateFrom . ' 00:00:00';
            }
            if ($dateTo) {
                $where .= " AND de.created_at <= ?";
                $params[] = $dateTo . ' 23:59:59';
            }
        }

        $stmt = $this->db->prepare("
            SELECT de.id, u.first_name, u.last_name, u.email,
                   de.base_fee, de.distance_fee, de.total_earning,
                   de.platform_commission, de.net_earning, de.tip,
                   de.payment_status, de.paid_at, de.created_at
            FROM delivery_earnings de
            JOIN users u ON de.driver_id = u.id
            {$where}
            ORDER BY de.created_at DESC
        ");
        $stmt->execute($params);
        $earnings = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="earnings_export_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['ID', 'Driver First Name', 'Driver Last Name', 'Email', 'Base Fee', 'Distance Fee', 'Total Earning', 'Commission', 'Net Earning', 'Tip', 'Status', 'Paid At', 'Created At']);

        foreach ($earnings as $row) {
            fputcsv($output, [
                $row['id'],
                $row['first_name'],
                $row['last_name'],
                $row['email'],
                $row['base_fee'],
                $row['distance_fee'],
                $row['total_earning'],
                $row['platform_commission'],
                $row['net_earning'],
                $row['tip'] ?? '0.00',
                $row['payment_status'],
                $row['paid_at'] ?? '',
                $row['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Earnings & Payouts
     */
    public function earnings() {
        $status = get('status', 'pending');
        
        $whereClause = "WHERE 1=1";
        $params = [];
        
        if ($status !== 'all') {
            $whereClause .= " AND de.payment_status = ?";
            $params[] = $status;
        }
        
        $query = "
            SELECT
                de.*,
                u.first_name as driver_first_name,
                u.last_name as driver_last_name,
                o.order_number,
                da.distribution_request_id,
                dr.request_number AS dr_request_number,
                di.invoice_number AS dist_invoice_number,
                di.id             AS dist_invoice_id
            FROM delivery_earnings de
            JOIN users u ON de.driver_id = u.id
            LEFT JOIN orders o ON de.order_id = o.id
            LEFT JOIN delivery_assignments da ON de.delivery_id = da.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id
            LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
            $whereClause
            ORDER BY de.created_at DESC
            LIMIT 100
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $earnings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get summary
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_count,
                SUM(total_earning) as total_gross,
                SUM(platform_commission) as total_commission,
                SUM(net_earning) as total_net,
                SUM(CASE WHEN payment_status = 'pending' THEN net_earning ELSE 0 END) as pending_amount
            FROM delivery_earnings
        ");
        $stmt->execute();
        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return view('admin/delivery/earnings', [
            'earnings' => $earnings,
            'summary' => $summary,
            'selectedStatus' => $status,
            'pageTitle' => 'Driver Earnings',
            'currentPage' => 'driver-earnings',
        ]);
    }
    
    /**
     * Assign or reassign driver to delivery
     */
    public function assignDriverToDelivery() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $deliveryId = (int) ($input['delivery_id'] ?? 0);
            $driverId = (int) ($input['driver_id'] ?? 0);

            if (!$deliveryId || !$driverId) {
                throw new Exception('Delivery ID and Driver ID are required');
            }

            $this->db->beginTransaction();

            // Check if delivery exists
            $stmt = $this->db->prepare("SELECT * FROM delivery_assignments WHERE id = ?");
            $stmt->execute([$deliveryId]);
            $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$delivery) {
                throw new Exception('Delivery not found');
            }

            // Check if driver exists and has delivery role
            $stmt = $this->db->prepare("
                SELECT u.id FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND r.name = 'delivery' AND u.status = 'active'
            ");
            $stmt->execute([$driverId]);
            if (!$stmt->fetch()) {
                throw new Exception('Invalid driver');
            }

            // Block assignment if driver has not completed training
            $certStmt = $this->db->prepare("SELECT id FROM driver_certificates WHERE driver_id = ? LIMIT 1");
            $certStmt->execute([$driverId]);
            if (!$certStmt->fetchColumn()) {
                throw new Exception('This driver has not completed their training and cannot be assigned to deliveries.');
            }

            // Generate tracking code if not set
            $trackingCode = $delivery['tracking_code'] ?? null;
            if (!$trackingCode) {
                $trackingCode = 'OCS-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            }

            // Update delivery assignment
            $newStatus = $delivery['status'] === 'pending' ? 'assigned' : $delivery['status'];
            $stmt = $this->db->prepare("
                UPDATE delivery_assignments
                SET driver_id = ?, status = ?, assigned_at = NOW(), tracking_code = COALESCE(tracking_code, ?)
                WHERE id = ?
            ");
            $stmt->execute([$driverId, $newStatus, $trackingCode, $deliveryId]);

            // Add to status history
            $stmt = $this->db->prepare("
                INSERT INTO delivery_status_history
                (delivery_id, status, notes, created_by)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $deliveryId,
                $newStatus,
                'Driver assigned/reassigned by admin',
                userId()
            ]);

            // Set driver_payout on the order if not already set
            if (!empty($delivery['order_id'])) {
                $oStmt = $this->db->prepare(
                    "SELECT delivery_fee, distance_km, driver_payout FROM orders WHERE id = ? LIMIT 1"
                );
                $oStmt->execute([$delivery['order_id']]);
                $oRow = $oStmt->fetch(\PDO::FETCH_ASSOC);
                if ($oRow && (float)($oRow['driver_payout'] ?? 0) <= 0) {
                    $basePay    = (float)(getenv('DRIVER_BASE_PAY')     ?: 5.00);
                    $perKmRate  = (float)(getenv('DRIVER_PER_KM_RATE')  ?: 0.50);
                    $platCut    = (float)(getenv('DRIVER_PLATFORM_CUT') ?: 0.20);
                    $base       = max($basePay, (float)($oRow['delivery_fee'] ?? 0));
                    $gross      = $base + round((float)($oRow['distance_km'] ?? 0) * $perKmRate, 2);
                    $payout     = round($gross * (1 - $platCut), 2);
                    $this->db->prepare("UPDATE orders SET driver_payout = ? WHERE id = ?")
                             ->execute([$payout, $delivery['order_id']]);
                }
            }

            $this->db->commit();

            // Send email notification to driver (non-blocking)
            try {
                $driverInfo = $this->db->prepare("SELECT first_name, last_name, email, phone FROM users WHERE id = ?");
                $driverInfo->execute([$driverId]);
                $driver = $driverInfo->fetch(PDO::FETCH_ASSOC);

                if ($driver && $driver['email']) {
                    // Get order details for email
                    $orderInfo = $this->db->prepare("
                        SELECT da.*, COALESCE(o.order_number, CONCAT('DR-', dr.id)) as order_number,
                               COALESCE(o.total, dr.total_amount) as order_total,
                               COALESCE(s.name, bp.company_name) as shop_name,
                               COALESCE(s.address, '') as shop_address,
                               COALESCE(s.phone, '') as shop_phone,
                               COALESCE(u.first_name, bp_u.first_name, '') as cust_first,
                               COALESCE(u.last_name, bp_u.last_name, '') as cust_last,
                               COALESCE(u.phone, bp_u.phone, '') as cust_phone
                        FROM delivery_assignments da
                        LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
                        LEFT JOIN users u ON o.user_id = u.id
                        LEFT JOIN shops s ON da.shop_id = s.id
                        LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
                        LEFT JOIN business_profiles bp ON dr.business_id = bp.id
                        LEFT JOIN users bp_u ON bp.user_id = bp_u.id
                        WHERE da.id = ?
                    ");
                    $orderInfo->execute([$deliveryId]);
                    $info = $orderInfo->fetch(PDO::FETCH_ASSOC);

                    if ($info) {
                        \App\Helpers\EmailHelper::sendDeliveryAssignment([
                            'driver_email' => $driver['email'],
                            'driver_name' => trim($driver['first_name'] . ' ' . $driver['last_name']),
                            'order_number' => $info['order_number'] ?? '',
                            'order_total' => $info['order_total'] ?? 0,
                            'shop_name' => $info['shop_name'] ?? '',
                            'shop_address' => $info['shop_address'] ?? '',
                            'shop_phone' => $info['shop_phone'] ?? '',
                            'shop_contact' => $info['shop_name'] ?? '',
                            'customer_name' => trim(($info['cust_first'] ?? '') . ' ' . ($info['cust_last'] ?? '')),
                            'delivery_address' => $info['delivery_address'] ?? '',
                            'customer_phone' => $info['cust_phone'] ?? '',
                            'delivery_id' => $deliveryId,
                            'items_count' => '—',
                            'items_list' => 'See delivery details in the app',
                        ]);
                    }
                }
            } catch (Exception $emailErr) {
                error_log('Driver assignment email error: ' . $emailErr->getMessage());
            }

            // FCM push to driver's ODA app
            try {
                $orderNum = $info['order_number'] ?? "DA-{$deliveryId}";
                \App\Controllers\Api\DriverApiController::sendPush(
                    $this->db,
                    $driverId,
                    '🛵 New Delivery Assigned',
                    "Order #{$orderNum} — Pick up from " . ($info['shop_name'] ?? 'merchant') . '.',
                    ['type' => 'delivery', 'delivery_id' => (string)$deliveryId]
                );
            } catch (\Exception $fcmErr) {
                error_log('Driver FCM push error: ' . $fcmErr->getMessage());
            }

            // Notify seller/shop that driver is on the way
            if (!empty($info) && !empty($info['shop_id'] ?? $delivery['shop_id'])) {
                try {
                    $shopId = $delivery['shop_id'] ?? null;
                    if ($shopId) {
                        $sellerStmt = $this->db->prepare(
                            "SELECT u.id, u.email, u.first_name, s.name as shop_name
                             FROM shops s JOIN users u ON s.seller_id = u.id
                             WHERE s.id = ? LIMIT 1"
                        );
                        $sellerStmt->execute([$shopId]);
                        $seller = $sellerStmt->fetch(\PDO::FETCH_ASSOC);
                        if ($seller) {
                            \App\Helpers\NotificationHelper::addForUser(
                                $seller['id'],
                                'delivery',
                                '🚚 Driver Assigned to Your Order',
                                "Driver {$driver['first_name']} {$driver['last_name']} has been assigned to pick up order #{$info['order_number']}. Please have it ready.",
                                ['link' => '/seller/orders']
                            );
                        }
                    }
                } catch (\Exception $notifyErr) {
                    error_log('Seller notification error: ' . $notifyErr->getMessage());
                }
            }

            return jsonResponse([
                'success' => true,
                'message' => 'Driver assigned successfully',
                'tracking_code' => $trackingCode
            ]);

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Mark earnings as paid
     */
    public function markPaid() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        try {
            $earningIds = post('earning_ids', []);
            $paymentReference = sanitize(post('payment_reference', ''));
            $paymentNotes = sanitize(post('payment_notes', ''));

            if (empty($earningIds)) {
                throw new Exception('No earnings selected');
            }

            $placeholders = implode(',', array_fill(0, count($earningIds), '?'));
            $params = [];

            // Check if payment_reference column exists (Phase 3 migration)
            $cols = $this->db->query("DESCRIBE delivery_earnings")->fetchAll(\PDO::FETCH_COLUMN);
            $hasRefCol = in_array('payment_reference', $cols);

            if ($hasRefCol && ($paymentReference || $paymentNotes)) {
                $stmt = $this->db->prepare("
                    UPDATE delivery_earnings
                    SET payment_status = 'paid', paid_at = NOW(),
                        payment_reference = ?, payment_notes = ?
                    WHERE id IN ($placeholders)
                ");
                $params = array_merge([$paymentReference ?: null, $paymentNotes ?: null], $earningIds);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE delivery_earnings
                    SET payment_status = 'paid', paid_at = NOW()
                    WHERE id IN ($placeholders)
                ");
                $params = $earningIds;
            }

            $stmt->execute($params);

            return jsonResponse([
                'success' => true,
                'message' => 'Marked as paid successfully'
            ]);

        } catch (Exception $e) {
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
    
    // ========================================
    // HELPER METHODS
    // ========================================
    
    private function getDeliveryStats() {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status IN ('pending', 'assigned', 'accepted', 'picked_up', 'on_the_way') THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'pending' AND driver_id IS NULL THEN 1 ELSE 0 END) as awaiting_driver,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'delivered' THEN delivery_fee ELSE 0 END) as total_revenue,
                AVG(CASE WHEN status = 'delivered' AND actual_time IS NOT NULL THEN actual_time END) as avg_delivery_time,
                AVG(CASE WHEN rating IS NOT NULL THEN rating END) as avg_rating
            FROM delivery_assignments
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get B2B distribution delivery stats
     */
    private function getDistributionDeliveryStats() {
        $stmt = $this->db->prepare("
            SELECT
                COUNT(CASE WHEN status IN ('processing', 'ready', 'in_transit') THEN 1 END) as active,
                COUNT(CASE WHEN status = 'processing' THEN 1 END) as awaiting_driver,
                COUNT(CASE WHEN status IN ('ready', 'in_transit') THEN 1 END) as in_delivery,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed
            FROM distribution_requests
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getRecentDeliveries($limit = 10) {
        $results = [];

        // B2C order deliveries
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                'order' as source_type,
                o.order_number as reference_number,
                u.first_name as customer_name,
                COALESCE(d.first_name, 'Unassigned') as driver_name,
                s.name as shop_name
            FROM delivery_assignments da
            JOIN orders o ON da.order_id = o.id
            JOIN users u ON o.user_id = u.id
            LEFT JOIN users d ON da.driver_id = d.id
            LEFT JOIN shops s ON da.shop_id = s.id
            WHERE da.delivery_type = 'order'
            ORDER BY da.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // B2B distribution deliveries (recent ones in delivery-related statuses or completed)
        $stmt = $this->db->prepare("
            SELECT
                dr.id as distribution_request_id,
                dr.request_number as reference_number,
                dr.status as dist_status,
                dr.total_amount as total,
                dr.created_at,
                dr.delivered_at,
                'distribution' as source_type,
                bp.company_name as shop_name,
                CONCAT(u.first_name, ' ', u.last_name) as customer_name,
                COALESCE(drv.first_name, 'No driver') as driver_name,
                da_link.status as da_status
            FROM distribution_requests dr
            INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
            INNER JOIN users u ON bp.user_id = u.id
            LEFT JOIN delivery_assignments da_link ON da_link.distribution_request_id = dr.id AND da_link.delivery_type = 'distribution'
            LEFT JOIN users drv ON da_link.driver_id = drv.id
            WHERE dr.status IN ('processing', 'ready', 'in_transit', 'completed')
            ORDER BY dr.updated_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        $distResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Normalize distribution rows
        foreach ($distResults as &$dd) {
            $dd['delivery_type'] = 'distribution';
            $dd['status'] = $dd['da_status'] ?? $this->mapDistStatusToDeliveryStatus($dd['dist_status']);
        }
        unset($dd);

        $results = array_merge($results, $distResults);

        // Sort by created_at DESC and limit
        usort($results, function($a, $b) {
            return strtotime($b['created_at'] ?? '0') - strtotime($a['created_at'] ?? '0');
        });

        return array_slice($results, 0, $limit);
    }
    
    private function getActiveDrivers() {
        try {
            $stmt = $this->db->prepare("
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    da.status,
                    da.active_deliveries,
                    dz.name as zone_name
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                LEFT JOIN driver_availability da ON u.id = da.driver_id
                LEFT JOIN delivery_zones dz ON da.zone_id = dz.id
                JOIN driver_api_tokens dat ON dat.user_id = u.id AND dat.driver_online = 1
                WHERE r.name = 'delivery'
                AND da.status IN ('available', 'busy')
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return [];
        }
    }
    
    private function getTodayDeliveries() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count, status
            FROM delivery_assignments
            WHERE DATE(created_at) = CURDATE()
            GROUP BY status
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getPerformanceMetrics($period) {
        $whereClause = "";
        if ($period === 'week') {
            $whereClause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $whereClause = "WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed,
                AVG(CASE WHEN actual_time IS NOT NULL THEN actual_time END) as avg_time,
                AVG(CASE WHEN rating IS NOT NULL THEN rating END) as avg_rating
            FROM delivery_assignments
            $whereClause
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    private function getTopDrivers($limit, $period) {
        $whereClause = "";
        if ($period === 'week') {
            $whereClause = "AND da.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        } elseif ($period === 'month') {
            $whereClause = "AND da.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.first_name,
                u.last_name,
                COUNT(da.id) as delivery_count,
                AVG(da.rating) as avg_rating,
                SUM(de.net_earning) as total_earnings
            FROM users u
            JOIN delivery_assignments da ON u.id = da.driver_id
            LEFT JOIN delivery_earnings de ON da.id = de.delivery_id
            WHERE da.status = 'delivered'
            $whereClause
            GROUP BY u.id
            ORDER BY delivery_count DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function getDeliveryTrends($period) {
        $days = $period === 'week' ? 7 : 30;
        
        $stmt = $this->db->prepare("
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed
            FROM delivery_assignments
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        $stmt->execute([$days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
 * Driver Details Page
 * Add this method to AdminDeliveryController
 */
public function driverDetails() {
    $driverId = (int) get('id', 0);
    
    if (!$driverId) {
        setFlash('error', 'Driver ID is required');
        redirect(url('admin/delivery/staff'));
        return;
    }

    try {
        // Get driver details with availability settings
        $stmt = $this->db->prepare("
            SELECT u.*,
                   da.status as availability_status,
                   da.max_deliveries,
                   da.zone_id,
                   da.active_deliveries,
                   da.last_location_update,
                   dz.name as zone_name
            FROM users u
            INNER JOIN user_roles ur ON u.id = ur.user_id
            INNER JOIN roles r ON ur.role_id = r.id
            LEFT JOIN driver_availability da ON u.id = da.driver_id
            LEFT JOIN delivery_zones dz ON da.zone_id = dz.id
            WHERE u.id = ? AND r.name = 'delivery'
        ");
        $stmt->execute([$driverId]);
        $driver = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$driver) {
            setFlash('error', 'Driver not found');
            redirect(url('admin/delivery/staff'));
            return;
        }
        
        // Get driver statistics
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed_deliveries,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_deliveries,
                AVG(CASE WHEN rating IS NOT NULL THEN rating END) as avg_rating,
                SUM(CASE WHEN distance_km IS NOT NULL THEN distance_km ELSE 0 END) as total_distance
            FROM delivery_assignments
            WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Get total earnings
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(net_earning), 0) as total_earnings
            FROM delivery_earnings
            WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        $earnings = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['total_earnings'] = $earnings['total_earnings'];
        
        // Get recent deliveries (both B2C and B2B)
        $stmt = $this->db->prepare("
            SELECT
                da.*,
                da.delivery_type as source_type,
                COALESCE(o.order_number, dr.request_number) as order_number,
                CASE
                    WHEN da.delivery_type = 'distribution' THEN CONCAT(cu.first_name, ' ', cu.last_name)
                    ELSE CONCAT(u.first_name, ' ', u.last_name)
                END as customer_name,
                COALESCE(s.name, bp.company_name, 'N/A') as shop_name
            FROM delivery_assignments da
            LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
            LEFT JOIN users u ON o.user_id = u.id
            LEFT JOIN shops s ON da.shop_id = s.id
            LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
            LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
            LEFT JOIN users cu ON bp.user_id = cu.id
            WHERE da.driver_id = ?
            ORDER BY da.created_at DESC
            LIMIT 20
        ");
        $stmt->execute([$driverId]);
        $recent_deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get compliance documents + bgcheck data
        $appStmt = $this->db->prepare("
            SELECT id, bgcheck_status, bgcheck_file_path, bgcheck_uploaded_at, bgcheck_verified_at, bgcheck_notes
            FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
        ");
        $appStmt->execute([$driverId]);
        $appRow = $appStmt->fetch(PDO::FETCH_ASSOC);
        $complianceDocs = [];
        if ($appRow) {
            $cdStmt = $this->db->prepare("SELECT * FROM driver_compliance_docs WHERE application_id = ?");
            $cdStmt->execute([$appRow['id']]);
            foreach ($cdStmt->fetchAll(PDO::FETCH_ASSOC) as $cd) {
                $complianceDocs[$cd['doc_type']] = $cd;
            }
        }

        // 30-day rolling avg performance score
        $avgScoreStmt = $this->db->prepare("
            SELECT ROUND(AVG(total_score), 1) AS avg_score,
                   COUNT(*) AS total_scored
            FROM order_performance_scores
            WHERE entity_type = 'driver'
              AND entity_id = ?
              AND calculated_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $avgScoreStmt->execute([$driverId]);
        $driverScore = $avgScoreStmt->fetch(PDO::FETCH_ASSOC);

        // Recent per-delivery scores (last 10)
        $recentScoresStmt = $this->db->prepare("
            SELECT ops.*,
                   dr.request_number,
                   dr.order_deadline,
                   dr.submitted_at
            FROM order_performance_scores ops
            LEFT JOIN distribution_requests dr ON dr.id = ops.distribution_request_id
            WHERE ops.entity_type = 'driver'
              AND ops.entity_id = ?
            ORDER BY ops.calculated_at DESC
            LIMIT 10
        ");
        $recentScoresStmt->execute([$driverId]);
        $recentScores = $recentScoresStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get payment info
        $payStmt = $this->db->prepare("SELECT * FROM driver_payment_info WHERE user_id = ? LIMIT 1");
        $payStmt->execute([$driverId]);
        $driverPayment = $payStmt->fetch(PDO::FETCH_ASSOC) ?: [];

        return view('admin/delivery/driver-details', [
            'driver'           => $driver,
            'stats'            => $stats,
            'recent_deliveries'=> $recent_deliveries,
            'complianceDocs'   => $complianceDocs,
            'driverAppId'      => $appRow['id'] ?? null,
            'bgcheck'          => $appRow ?: null,
            'driverScore'      => $driverScore,
            'recentScores'     => $recentScores,
            'driverPayment'    => $driverPayment,
            'pageTitle'        => 'Driver Details'
        ]);
        
    } catch (Exception $e) {
        logger("Driver details error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error loading driver details: ' . $e->getMessage());
        redirect(url('admin/delivery/staff'));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// SUPPLIER PICKUP REQUESTS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * GET /admin/pickup-requests
 * List all supplier pickup requests
 */
public function pickupRequests(): void {
    \App\Middlewares\AuthMiddleware::handle('admin');

    try {
        $db = \Database::getConnection();

        $statusFilter = get('status', '');
        $where  = ['1=1'];
        $params = [];

        if ($statusFilter && in_array($statusFilter, ['pending','scheduled','completed','cancelled'])) {
            $where[]  = 'spr.status = ?';
            $params[] = $statusFilter;
        }

        $whereClause = implode(' AND ', $where);

        $stmt = $db->prepare("
            SELECT spr.*,
                   s.company_name, s.name AS supplier_name, s.email AS supplier_email,
                   s.address AS supplier_address,
                   CONCAT(u.first_name, ' ', u.last_name) AS driver_name
            FROM supplier_pickup_requests spr
            JOIN suppliers s ON s.id = spr.supplier_id
            LEFT JOIN users u ON u.id = spr.assigned_driver_id
            WHERE {$whereClause}
            ORDER BY FIELD(spr.status,'pending','scheduled','completed','cancelled'), spr.requested_date ASC
        ");
        $stmt->execute($params);
        $requests = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Fetch available drivers for assignment modal
        $driversStmt = $db->query("
            SELECT id, CONCAT(first_name, ' ', last_name) AS name
            FROM users
            WHERE role IN ('driver','admin')
              AND status = 'active'
            ORDER BY first_name
        ");
        $drivers = $driversStmt->fetchAll(\PDO::FETCH_ASSOC);

        $flash = null;
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
        }

        view('admin.delivery.pickup-requests', [
            'pageTitle'    => 'Pickup Requests',
            'requests'     => $requests,
            'drivers'      => $drivers,
            'statusFilter' => $statusFilter,
            'flash'        => $flash,
            'currentPage'  => 'delivery',
        ]);

    } catch (\PDOException $e) {
        logger("Admin pickup requests error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error loading pickup requests.');
        redirect(url('admin/delivery'));
    }
}

/**
 * POST /admin/pickup-requests/schedule
 * Assign a driver and confirm the pickup
 */
public function schedulePickup(): void {
    \App\Middlewares\AuthMiddleware::handle('admin');

    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
        return;
    }

    try {
        $db = \Database::getConnection();

        $requestId  = (int) post('request_id');
        $driverId   = (int) post('driver_id');
        $adminNotes = trim(post('admin_notes', ''));

        if (!$requestId || !$driverId) {
            jsonResponse(['success' => false, 'message' => 'Request ID and driver are required'], 400);
            return;
        }

        $reqStmt = $db->prepare("SELECT * FROM supplier_pickup_requests WHERE id = ? AND status = 'pending'");
        $reqStmt->execute([$requestId]);
        $req = $reqStmt->fetch(\PDO::FETCH_ASSOC);

        if (!$req) {
            jsonResponse(['success' => false, 'message' => 'Pickup request not found or already processed'], 404);
            return;
        }

        // Update pickup request
        $db->prepare("
            UPDATE supplier_pickup_requests
            SET status = 'scheduled', assigned_driver_id = ?, admin_notes = ?, scheduled_at = NOW()
            WHERE id = ?
        ")->execute([$driverId, $adminNotes ?: null, $requestId]);

        // Notify supplier
        try {
            $supStmt = $db->prepare("SELECT company_name, name FROM suppliers WHERE id = ?");
            $supStmt->execute([$req['supplier_id']]);
            $sup = $supStmt->fetch(\PDO::FETCH_ASSOC);

            \App\Helpers\NotificationHelper::addSupplierNotification(
                (int) $req['supplier_id'],
                'pickup_scheduled',
                'Pickup Confirmed',
                'Your pickup request for ' . date('M j, Y', strtotime($req['requested_date'])) . ' has been confirmed. Our driver will arrive between ' . date('g:i A', strtotime($req['requested_time_from'])) . ' and ' . date('g:i A', strtotime($req['requested_time_to'])) . '.',
                'supplier/pickup',
                'truck',
                'Collecte confirmée',
                'Votre demande de collecte du ' . date('j M Y', strtotime($req['requested_date'])) . ' a été confirmée. Notre chauffeur arrivera entre ' . date('G\hi', strtotime($req['requested_time_from'])) . ' et ' . date('G\hi', strtotime($req['requested_time_to'])) . '.'
            );
        } catch (\Exception $e) {
            error_log("Pickup schedule supplier notification error: " . $e->getMessage());
        }

        logger("Admin scheduled pickup request #{$requestId} with driver #{$driverId}", 'info');
        jsonResponse(['success' => true, 'message' => 'Pickup scheduled and supplier notified']);

    } catch (\PDOException $e) {
        logger("Admin schedule pickup error: " . $e->getMessage(), 'error');
        jsonResponse(['success' => false, 'message' => 'Error scheduling pickup'], 500);
    }
}

/**
 * POST /admin/pickup-requests/cancel
 * Cancel a pickup request (admin side)
 */
public function cancelPickupRequest(): void {
    \App\Middlewares\AuthMiddleware::handle('admin');

    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
        return;
    }

    try {
        $db = \Database::getConnection();
        $requestId  = (int) post('request_id');
        $adminNotes = trim(post('admin_notes', ''));

        $stmt = $db->prepare("
            UPDATE supplier_pickup_requests
            SET status = 'cancelled', admin_notes = ?, cancelled_at = NOW()
            WHERE id = ? AND status IN ('pending','scheduled')
        ");
        $stmt->execute([$adminNotes ?: null, $requestId]);

        if ($stmt->rowCount() === 0) {
            jsonResponse(['success' => false, 'message' => 'Request not found or already closed'], 404);
            return;
        }

        // Notify supplier
        try {
            $supStmt = $db->prepare("SELECT supplier_id, requested_date FROM supplier_pickup_requests WHERE id = ?");
            $supStmt->execute([$requestId]);
            $req = $supStmt->fetch(\PDO::FETCH_ASSOC);
            if ($req) {
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    (int) $req['supplier_id'],
                    'pickup_cancelled',
                    'Pickup Request Cancelled',
                    'Your pickup request for ' . date('M j, Y', strtotime($req['requested_date'])) . ' has been cancelled.' . ($adminNotes ? " Note: {$adminNotes}" : ' Please contact us if you have questions.'),
                    'supplier/pickup',
                    'times-circle',
                    'Demande de collecte annulée',
                    'Votre demande de collecte du ' . date('j M Y', strtotime($req['requested_date'])) . ' a été annulée.' . ($adminNotes ? " Note : {$adminNotes}" : ' Veuillez nous contacter si vous avez des questions.')
                );
            }
        } catch (\Exception $e) {
            error_log("Pickup cancel supplier notification error: " . $e->getMessage());
        }

        logger("Admin cancelled pickup request #{$requestId}", 'info');
        jsonResponse(['success' => true, 'message' => 'Pickup request cancelled']);

    } catch (\PDOException $e) {
        logger("Admin cancel pickup error: " . $e->getMessage(), 'error');
        jsonResponse(['success' => false, 'message' => 'Error cancelling pickup'], 500);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// BACKGROUND CHECK ACTIONS
// ─────────────────────────────────────────────────────────────────────────────

/**
 * POST /admin/delivery/bgcheck/request
 * Generate a secure upload token and email it to the driver applicant
 */
public function requestBgcheck(): void
{
    verifyCsrfForApi();

    $appId   = (int) post('application_id', 0);
    $adminId = $_SESSION['user']['id'] ?? null;

    if (!$appId) { jsonResponse(['error' => 'Invalid application ID'], 422); return; }

    try {
        $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? LIMIT 1");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$app) { jsonResponse(['error' => 'Application not found'], 404); return; }

        $token   = bin2hex(random_bytes(32)); // 64-char hex token
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));

        $this->db->prepare("
            UPDATE driver_applications
            SET bgcheck_status = 'requested',
                bgcheck_token = ?,
                bgcheck_token_expires_at = ?,
                bgcheck_requested_at = NOW()
            WHERE id = ?
        ")->execute([$token, $expires, $appId]);

        $uploadUrl = url('delivery/bgcheck/upload?token=' . $token);

        // Send email to applicant
        \App\Helpers\EmailHelper::sendRaw(
            $app['email'],
            'Action Required: Upload Your Background Check — OCSAPP',
            "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
              <div style='background:#1e40af;padding:24px;text-align:center;'>
                <h1 style='color:white;margin:0;font-size:22px;'>OCSAPP Driver Portal</h1>
              </div>
              <div style='padding:32px;background:#ffffff;'>
                <p style='font-size:16px;color:#111827;'>Hi <strong>{$app['first_name']}</strong>,</p>
                <p style='color:#374151;line-height:1.7;'>As part of your driver application, we need you to obtain and upload a <strong>criminal background check</strong>.</p>

                <div style='background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;margin:20px 0;'>
                  <p style='margin:0;color:#0c4a6e;font-weight:600;'>📋 What you need:</p>
                  <ul style='margin:8px 0 0 0;padding-left:20px;color:#374151;'>
                    <li>An RCMP criminal record check <strong>OR</strong> a police information check from your local police station</li>
                    <li>Must be issued within the last <strong>12 months</strong></li>
                    <li>Quebec drivers: may also need a SAAQ certificate of no judicial record</li>
                  </ul>
                </div>

                <div style='background:#fafafa;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:20px 0;'>
                  <p style='margin:0 0 6px;color:#374151;font-weight:600;'>How to obtain your check:</p>
                  <p style='margin:0;color:#6b7280;font-size:14px;line-height:1.6;'>
                    <strong>Option A (Online, fastest):</strong> Visit an RCMP-authorized online provider.<br>
                    <strong>Option B (In-person):</strong> Visit your local police station or RCMP detachment with valid government ID.<br>
                    Cost is typically $25–$70 CAD and results take 1–5 business days.
                  </p>
                </div>

                <p style='color:#374151;'>Once you have your document, click the button below to upload it securely:</p>

                <div style='text-align:center;margin:28px 0;'>
                  <a href='{$uploadUrl}'
                     style='background:#1e40af;color:white;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;'>
                    📤 Upload Background Check
                  </a>
                </div>

                <p style='font-size:12px;color:#9ca3af;'>This link expires in 30 days. If you need a new link, contact support@ocsapp.ca</p>
              </div>
              <div style='background:#f9fafb;padding:16px;text-align:center;font-size:12px;color:#9ca3af;'>
                OCSAPP Driver Recruitment &bull; support@ocsapp.ca
              </div>
            </div>
            "
        );

        // Log to lead timeline
        if ($app['lead_id']) {
            $this->logPipelineActivity(
                $app['lead_id'],
                'email',
                "Background check requested from {$app['first_name']} {$app['last_name']}. Upload link sent to {$app['email']}.",
                'bgcheck_requested',
                'Background Check Requested',
                "Upload link emailed. Expires in 30 days.",
                "/admin/leads/view?id={$app['lead_id']}"
            );
        }

        jsonResponse(['success' => true, 'message' => 'Background check request sent to ' . $app['email'] . '.']);

    } catch (\Exception $e) {
        logger("requestBgcheck error: " . $e->getMessage(), 'error');
        jsonResponse(['error' => 'Failed to send request. Please try again.'], 500);
    }
}

/**
 * POST /admin/delivery/compliance/request
 * Send a compliance docs reminder to an approved driver (bell + email)
 */
public function requestComplianceDocs(): void
{
    verifyCsrfForApi();

    $driverId = (int) post('driver_id', 0);
    if (!$driverId) { jsonResponse(['error' => 'Invalid driver ID'], 422); return; }

    try {
        $stmt = $this->db->prepare("SELECT first_name, last_name, email FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$driverId]);
        $driver = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$driver) { jsonResponse(['error' => 'Driver not found'], 404); return; }

        $portalUrl = url('delivery/compliance');

        // Driver bell
        $this->db->prepare("
            INSERT INTO driver_delivery_notifications (driver_id, message, type, sent_by, created_at)
            VALUES (?, ?, 'normal', 0, NOW())
        ")->execute([
            $driverId,
            'Action required: please log in and upload your compliance documents to complete your driver profile.',
        ]);

        // Driver email
        \App\Helpers\EmailHelper::sendRaw(
            $driver['email'],
            'Action Required: Upload Your Compliance Documents — OCSAPP',
            "
            <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
              <div style='background:#1e40af;padding:24px;text-align:center;'>
                <img src='https://ocsapp.ca/assets/images/logo.png' alt='OCSAPP' style='max-width:140px;'>
              </div>
              <div style='padding:32px;background:#fff;'>
                <p style='font-size:16px;color:#111827;'>Bonjour / Hi <strong>{$driver['first_name']}</strong>,</p>
                <p style='color:#374151;line-height:1.7;'>
                  Pour compléter votre profil de livreur, veuillez soumettre vos documents de conformité dans votre portail.<br>
                  To complete your driver profile, please upload your compliance documents in your portal.
                </p>
                <div style='background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;margin:20px 0;'>
                  <p style='margin:0 0 8px;color:#0c4a6e;font-weight:600;'>Documents requis / Required documents:</p>
                  <ul style='margin:0;padding-left:20px;color:#374151;font-size:14px;line-height:1.9;'>
                    <li>Permis de conduire classe 5 / Class 5 Driver's License</li>
                    <li>Dossier de conduite SAAQ / SAAQ Driving Record</li>
                    <li>Preuve d'assurance commerciale / Proof of Commercial Insurance (COI)</li>
                    <li>Immatriculation du véhicule / Vehicle Registration</li>
                    <li>Preuve d'autorisation de travail / Proof of Work Authorization</li>
                  </ul>
                </div>
                <div style='text-align:center;margin:28px 0;'>
                  <a href='{$portalUrl}'
                     style='background:#1e40af;color:white;padding:14px 32px;border-radius:8px;text-decoration:none;font-weight:700;font-size:15px;display:inline-block;'>
                    Accéder aux documents / Go to Documents
                  </a>
                </div>
              </div>
              <div style='background:#f9fafb;padding:16px;text-align:center;font-size:12px;color:#9ca3af;'>
                OCSAPP Delivery Team &bull; support@ocsapp.ca
              </div>
            </div>
            "
        );

        // Admin bell log
        try {
            \App\Helpers\NotificationHelper::add(
                'document_request',
                'Compliance Docs Reminder Sent',
                "Compliance document reminder sent to {$driver['first_name']} {$driver['last_name']} ({$driver['email']}).",
                ['link' => '/admin/delivery/driver?id=' . $driverId, 'priority' => 'normal', 'icon' => 'file-shield']
            );
        } catch (\Exception $e) { /* non-critical */ }

        jsonResponse(['success' => true, 'message' => "Reminder sent to {$driver['email']}."]);

    } catch (\Exception $e) {
        logger("requestComplianceDocs error: " . $e->getMessage(), 'error');
        jsonResponse(['error' => 'Failed to send reminder. Please try again.'], 500);
    }
}

/**
 * POST /admin/delivery/bgcheck/verify
 * Admin marks uploaded background check as verified, flagged, or waived
 */
public function verifyBgcheck(): void
{
    verifyCsrfForApi();

    $appId   = (int) post('application_id', 0);
    $action  = post('action', ''); // 'verify', 'flag', 'waive'
    $notes   = trim(post('notes', ''));
    $adminId = $_SESSION['user']['id'] ?? null;

    if (!$appId || !in_array($action, ['verify', 'flag', 'waive'])) {
        jsonResponse(['error' => 'Invalid parameters'], 422);
        return;
    }

    if ($action === 'waive' && empty($notes)) {
        jsonResponse(['error' => 'Notes are required when waiving a background check.'], 422);
        return;
    }

    $statusMap = ['verify' => 'verified', 'flag' => 'flagged', 'waive' => 'waived'];
    $newStatus = $statusMap[$action];

    try {
        $stmt = $this->db->prepare("SELECT * FROM driver_applications WHERE id = ? LIMIT 1");
        $stmt->execute([$appId]);
        $app = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$app) { jsonResponse(['error' => 'Application not found'], 404); return; }

        $this->db->prepare("
            UPDATE driver_applications
            SET bgcheck_status = ?,
                bgcheck_verified_at = NOW(),
                bgcheck_verified_by = ?,
                bgcheck_notes = ?
            WHERE id = ?
        ")->execute([$newStatus, $adminId, $notes ?: null, $appId]);

        $actionLabel = ucfirst($action);

        // Log to timeline
        if ($app['lead_id']) {
            $this->logPipelineActivity(
                $app['lead_id'],
                'status_change',
                "Background check for {$app['first_name']} {$app['last_name']} marked as {$newStatus}." . ($notes ? " Notes: {$notes}" : ''),
                'bgcheck_' . $newStatus,
                'Background Check ' . ucfirst($newStatus),
                ucfirst($newStatus) . ($notes ? ": {$notes}" : ''),
                "/admin/leads/view?id={$app['lead_id']}"
            );
        }

        // Notify admin on flag
        if ($action === 'flag') {
            \App\Helpers\NotificationHelper::add(
                'security',
                'Background Check Flagged',
                "{$app['first_name']} {$app['last_name']}'s background check has been flagged." . ($notes ? " Reason: {$notes}" : ''),
                ['link' => '/admin/leads/view?id=' . ($app['lead_id'] ?? ''), 'priority' => 'urgent', 'icon' => 'shield-halved']
            );
        }

        jsonResponse(['success' => true, 'status' => $newStatus, 'message' => 'Background check marked as ' . $newStatus . '.']);

    } catch (\Exception $e) {
        logger("verifyBgcheck error: " . $e->getMessage(), 'error');
        jsonResponse(['error' => 'Failed to update status.'], 500);
    }
}

// ========================================
// COMPLIANCE DOCUMENTS — ADMIN
// ========================================

private const COMPLIANCE_UPLOAD_DIR = __DIR__ . '/../../storage/uploads/compliance_docs/';

/**
 * GET /admin/delivery/compliance/download?doc_id=xxx
 * Secure file download for admin
 */
public function complianceDownload(): void
{
    if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
        http_response_code(403); exit('Access denied.');
    }

    $docId = (int)($_GET['doc_id'] ?? 0);
    if (!$docId) { http_response_code(400); exit('Invalid request.'); }

    $stmt = $this->db->prepare("
        SELECT d.*, a.first_name, a.last_name
        FROM driver_compliance_docs d
        JOIN driver_applications a ON a.id = d.application_id
        WHERE d.id = ? LIMIT 1
    ");
    $stmt->execute([$docId]);
    $doc = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$doc || empty($doc['file_path'])) { http_response_code(404); exit('File not found.'); }

    $filePath = self::COMPLIANCE_UPLOAD_DIR . basename($doc['file_path']);
    if (!file_exists($filePath)) { http_response_code(404); exit('File not found on disk.'); }

    $ext     = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeMap = ['pdf' => 'application/pdf', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
    $mime    = $mimeMap[$ext] ?? 'application/octet-stream';
    $label   = preg_replace('/[^a-z0-9_]/i', '_', $doc['doc_type'] . '_' . $doc['first_name'] . '_' . $doc['last_name']);

    header('Content-Type: ' . $mime);
    header('Content-Disposition: inline; filename="' . $label . '.' . $ext . '"');
    header('Content-Length: ' . filesize($filePath));
    header('X-Content-Type-Options: nosniff');
    readfile($filePath);
    exit;
}

/**
 * POST /admin/delivery/compliance/review
 * Verify, flag, or waive a compliance document
 */
public function reviewComplianceDoc(): void
{
    verifyCsrfForApi();

    $docId   = (int) post('doc_id', 0);
    $action  = post('action', ''); // 'verify', 'flag', 'waive'
    $notes   = trim(post('notes', ''));
    $adminId = $_SESSION['user']['id'] ?? null;

    if (!$docId || !in_array($action, ['verify', 'flag', 'waive'])) {
        jsonResponse(['error' => 'Invalid parameters'], 422);
        return;
    }

    $statusMap = ['verify' => 'verified', 'flag' => 'flagged', 'waive' => 'not_required'];
    $newStatus = $statusMap[$action];

    try {
        $stmt = $this->db->prepare("SELECT d.*, a.first_name, a.last_name, a.lead_id FROM driver_compliance_docs d JOIN driver_applications a ON a.id = d.application_id WHERE d.id = ? LIMIT 1");
        $stmt->execute([$docId]);
        $doc = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$doc) { jsonResponse(['error' => 'Document not found'], 404); return; }

        $this->db->prepare("
            UPDATE driver_compliance_docs
            SET status = ?, admin_notes = ?, verified_at = NOW(), verified_by = ?
            WHERE id = ?
        ")->execute([$newStatus, $notes ?: null, $adminId, $docId]);

        $docLabel = ucwords(str_replace('_', ' ', $doc['doc_type']));
        $driverName = $doc['first_name'] . ' ' . $doc['last_name'];

        if ($action === 'flag') {
            \App\Helpers\NotificationHelper::add(
                'security',
                'Compliance Document Flagged',
                "{$driverName}'s {$docLabel} has been flagged." . ($notes ? " Reason: {$notes}" : ''),
                ['link' => '/admin/delivery/driver-details?id=' . ($doc['driver_id'] ?? ''), 'priority' => 'urgent', 'icon' => 'file-shield']
            );
        }

        jsonResponse(['success' => true, 'status' => $newStatus, 'message' => "{$docLabel} marked as {$newStatus}."]);

    } catch (\Exception $e) {
        logger("reviewComplianceDoc error: " . $e->getMessage(), 'error');
        jsonResponse(['error' => 'Failed to update document status.'], 500);
    }
}
}