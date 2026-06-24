<?php

namespace App\Controllers;

/**
 * AdminLeadsController - CRM Lead Management
 * Handles lead tracking, follow-ups, and conversion
 */
class AdminLeadsController
{
    private $db;

    public function __construct()
    {
        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            redirect('login');
            exit;
        }
        $this->db = \Database::getConnection();
    }

    /**
     * List all leads with filtering
     */
    public function index(): void
    {


        $status     = sanitize($_GET['status'] ?? '');
        $source     = sanitize($_GET['source'] ?? '');
        $priority   = sanitize($_GET['priority'] ?? '');
        $interest   = sanitize($_GET['interest'] ?? '');
        $search     = sanitize($_GET['search'] ?? '');
        $assignedTo = !empty($_GET['assigned_to']) ? (int)$_GET['assigned_to'] : 0;
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        try {
            // Build query
            $where = "WHERE 1=1";
            $params = [];

            if ($status) {
                $where .= " AND l.status = ?";
                $params[] = $status;
            }
            if ($source) {
                $where .= " AND l.source = ?";
                $params[] = $source;
            }
            if ($priority) {
                $where .= " AND l.priority = ?";
                $params[] = $priority;
            }
            if ($interest) {
                $where .= " AND l.interest_type = ?";
                $params[] = $interest;
            }
            if ($search) {
                $where .= " AND (l.first_name LIKE ? OR l.last_name LIKE ? OR l.email LIKE ? OR l.company_name LIKE ? OR l.phone LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }
            if ($assignedTo) {
                $where .= " AND l.assigned_to = ?";
                $params[] = $assignedTo;
            }

            // Get total count
            $countStmt = $this->db->prepare("SELECT COUNT(*) as total FROM leads l $where");
            $countStmt->execute($params);
            $total = $countStmt->fetch(\PDO::FETCH_ASSOC)['total'];

            // Get leads (use all positional placeholders - PDO emulation is off)
            $stmt = $this->db->prepare("
                SELECT l.*,
                       CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name,
                       (SELECT COUNT(*) FROM lead_activities WHERE lead_id = l.id) as activity_count,
                       (SELECT user_id FROM driver_applications WHERE lead_id = l.id AND user_id IS NOT NULL ORDER BY id DESC LIMIT 1) as driver_user_id,
                       (SELECT COUNT(*) FROM driver_certificates WHERE driver_id = (SELECT user_id FROM driver_applications WHERE lead_id = l.id AND user_id IS NOT NULL ORDER BY id DESC LIMIT 1)) as training_certified,
                       (SELECT SUM(CASE WHEN status='passed' THEN 1 ELSE 0 END) FROM driver_training_progress WHERE driver_id = (SELECT user_id FROM driver_applications WHERE lead_id = l.id AND user_id IS NOT NULL ORDER BY id DESC LIMIT 1)) as training_modules_passed,
                       (SELECT COUNT(*) FROM driver_training_progress WHERE driver_id = (SELECT user_id FROM driver_applications WHERE lead_id = l.id AND user_id IS NOT NULL ORDER BY id DESC LIMIT 1) AND status='failed' AND attempts >= 3) as training_stuck
                FROM leads l
                LEFT JOIN users u ON l.assigned_to = u.id
                $where
                ORDER BY l.created_at DESC
                LIMIT ? OFFSET ?
            ");
            $allParams = $params;
            $allParams[] = $perPage;
            $allParams[] = $offset;
            foreach ($allParams as $i => $val) {
                $stmt->bindValue($i + 1, is_int($val) ? $val : $val, is_int($val) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            }
            $stmt->execute();
            $leads = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get stats
            $statsStmt = $this->db->query("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'new'         THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'contacted'   THEN 1 ELSE 0 END) as contacted_count,
                    SUM(CASE WHEN status = 'qualified'   THEN 1 ELSE 0 END) as qualified_count,
                    SUM(CASE WHEN status = 'proposal'    THEN 1 ELSE 0 END) as proposal_count,
                    SUM(CASE WHEN status = 'negotiation' THEN 1 ELSE 0 END) as negotiation_count,
                    SUM(CASE WHEN status = 'won'         THEN 1 ELSE 0 END) as won_count,
                    SUM(CASE WHEN status = 'lost'        THEN 1 ELSE 0 END) as lost_count,
                    SUM(CASE WHEN next_follow_up <= CURDATE() AND status NOT IN ('won', 'lost') THEN 1 ELSE 0 END) as overdue_count
                FROM leads
            ");
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            // Get admin users for assignment filter
            $adminsStmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role IN ('admin', 'super_admin') ORDER BY first_name, last_name");
            $admins = $adminsStmt->fetchAll(\PDO::FETCH_ASSOC);

            $totalPages = ceil($total / $perPage);

            view('admin.leads.index', [
                'leads' => $leads,
                'stats' => $stats,
                'admins' => $admins,
                'filters' => [
                    'status'      => $status,
                    'source'      => $source,
                    'priority'    => $priority,
                    'interest'    => $interest,
                    'search'      => $search,
                    'assigned_to' => $assignedTo ?: '',
                ],
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'total' => $total
            ]);

        } catch (\PDOException $e) {
            error_log('Leads index error: ' . $e->getMessage());
            setFlash('error', 'Error loading leads.');
            redirect('admin/dashboard');
        }
    }

    /**
     * Show create lead form
     */
    public function create(): void
    {


        try {
            // Get admin users for assignment
            $adminsStmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role IN ('admin', 'super_admin') ORDER BY first_name, last_name");
            $admins = $adminsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get tags
            $tagsStmt = $this->db->query("SELECT * FROM lead_tags ORDER BY name");
            $tags = $tagsStmt->fetchAll(\PDO::FETCH_ASSOC);

            view('admin.leads.create', [
                'admins' => $admins,
                'tags' => $tags
            ]);

        } catch (\PDOException $e) {
            error_log('Lead create form error: ' . $e->getMessage());
            setFlash('error', 'Error loading form.');
            redirect('admin/leads');
        }
    }

    /**
     * Store new lead
     */
    public function store(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO leads (
                    first_name, last_name, email, phone, company_name, job_title,
                    source, source_details, status, priority,
                    interest_type, interest_details, estimated_value,
                    city, province, country,
                    assigned_to, notes, next_follow_up
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                sanitize($_POST['first_name'] ?? ''),
                sanitize($_POST['last_name'] ?? ''),
                sanitize($_POST['email'] ?? ''),
                sanitize($_POST['phone'] ?? ''),
                sanitize($_POST['company_name'] ?? ''),
                sanitize($_POST['job_title'] ?? ''),
                sanitize($_POST['source'] ?? 'manual'),
                sanitize($_POST['source_details'] ?? ''),
                sanitize($_POST['status'] ?? 'new'),
                sanitize($_POST['priority'] ?? 'medium'),
                sanitize($_POST['interest_type'] ?? 'other'),
                sanitize($_POST['interest_details'] ?? ''),
                !empty($_POST['estimated_value']) ? (float)$_POST['estimated_value'] : null,
                sanitize($_POST['city'] ?? ''),
                sanitize($_POST['province'] ?? ''),
                sanitize($_POST['country'] ?? 'Canada'),
                !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                sanitize($_POST['notes'] ?? ''),
                !empty($_POST['next_follow_up']) ? $_POST['next_follow_up'] : null
            ]);

            $leadId = $this->db->lastInsertId();

            // Handle tags
            if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
                $tagStmt = $this->db->prepare("INSERT INTO lead_tag_assignments (lead_id, tag_id) VALUES (?, ?)");
                foreach ($_POST['tags'] as $tagId) {
                    $tagStmt->execute([$leadId, (int)$tagId]);
                }
            }

            // Log activity
            $this->logActivity($leadId, 'note', 'Lead created', null);

            setFlash('success', 'Lead created successfully.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\PDOException $e) {
            error_log('Lead store error: ' . $e->getMessage());
            setFlash('error', 'Error creating lead.');
            redirect('admin/leads/create');
        }
    }

    /**
     * View lead details
     */
    public function view(): void
    {


        $leadId = (int)($_GET['id'] ?? 0);

        try {
            // Get lead
            $stmt = $this->db->prepare("
                SELECT l.*, CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                FROM leads l
                LEFT JOIN users u ON l.assigned_to = u.id
                WHERE l.id = ?
            ");
            $stmt->execute([$leadId]);
            $lead = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$lead) {
                setFlash('error', 'Lead not found.');
                redirect('admin/leads');
                return;
            }

            // Get activities
            $activitiesStmt = $this->db->prepare("
                SELECT la.*, CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM lead_activities la
                LEFT JOIN users u ON la.created_by = u.id
                WHERE la.lead_id = ?
                ORDER BY la.created_at DESC
            ");
            $activitiesStmt->execute([$leadId]);
            $activities = $activitiesStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get tags
            $tagsStmt = $this->db->prepare("
                SELECT t.*
                FROM lead_tags t
                INNER JOIN lead_tag_assignments lta ON t.id = lta.tag_id
                WHERE lta.lead_id = ?
            ");
            $tagsStmt->execute([$leadId]);
            $leadTags = $tagsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get all tags for adding
            $allTagsStmt = $this->db->query("SELECT * FROM lead_tags ORDER BY name");
            $allTags = $allTagsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get admins for reassignment
            $adminsStmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role IN ('admin', 'super_admin') ORDER BY first_name, last_name");
            $admins = $adminsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Fetch linked supplier application if this is a supplier lead
            $supplierApp = null;
            $linkedSupplier = null;
            if ($lead['interest_type'] === 'supplier') {
                $appStmt = $this->db->prepare("SELECT * FROM supplier_applications WHERE lead_id = ? LIMIT 1");
                $appStmt->execute([$leadId]);
                $supplierApp = $appStmt->fetch(\PDO::FETCH_ASSOC);

                // Fetch the linked supplier account if exists
                if ($supplierApp && !empty($supplierApp['supplier_id'])) {
                    $supStmt = $this->db->prepare("SELECT id, status, verification_deadline, verification_reminder_sent, company_name, email FROM suppliers WHERE id = ?");
                    $supStmt->execute([$supplierApp['supplier_id']]);
                    $linkedSupplier = $supStmt->fetch(\PDO::FETCH_ASSOC);
                }
            }

            // Fetch linked business profile if this is a distribution lead
            $businessProfile = null;
            if (!empty($lead['business_profile_id'])) {
                $bpStmt = $this->db->prepare("
                    SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
                    FROM business_profiles bp
                    INNER JOIN users u ON bp.user_id = u.id
                    WHERE bp.id = ?
                ");
                $bpStmt->execute([$lead['business_profile_id']]);
                $businessProfile = $bpStmt->fetch(\PDO::FETCH_ASSOC);
            }

            // Fetch linked driver application + messages if this is a driver lead
            $driverApp = null;
            $driverMessages = [];
            $driverTrainingProgress = [];
            $driverCert = null;
            $driverComplianceDocs = [];
            if ($lead['interest_type'] === 'driver') {
                $driverStmt = $this->db->prepare("SELECT * FROM driver_applications WHERE lead_id = ? LIMIT 1");
                $driverStmt->execute([$leadId]);
                $driverApp = $driverStmt->fetch(\PDO::FETCH_ASSOC);

                if ($driverApp) {
                    $msgStmt = $this->db->prepare("
                        SELECT dam.*,
                               CONCAT(u.first_name, ' ', u.last_name) as sender_name,
                               u.email as sender_email
                        FROM driver_application_messages dam
                        LEFT JOIN users u ON dam.sender_id = u.id AND dam.sender_type = 'admin'
                        WHERE dam.application_id = ?
                        ORDER BY dam.created_at ASC
                    ");
                    $msgStmt->execute([$driverApp['id']]);
                    $driverMessages = $msgStmt->fetchAll(\PDO::FETCH_ASSOC);

                    // Fetch compliance documents
                    $cdStmt = $this->db->prepare("SELECT * FROM driver_compliance_docs WHERE application_id = ?");
                    $cdStmt->execute([$driverApp['id']]);
                    foreach ($cdStmt->fetchAll(\PDO::FETCH_ASSOC) as $cd) {
                        $driverComplianceDocs[$cd['doc_type']] = $cd;
                    }

                    // Fetch training data if driver has a user account
                    if (!empty($driverApp['user_id'])) {
                        $driverId = (int)$driverApp['user_id'];
                        $tpStmt = $this->db->prepare("
                            SELECT dtp.*, tm.id as tm_id, tm.title, tm.order_num, tm.max_attempts
                            FROM training_modules tm
                            LEFT JOIN driver_training_progress dtp ON dtp.module_id = tm.id AND dtp.driver_id = ?
                            WHERE tm.is_active = 1
                            ORDER BY tm.order_num ASC
                        ");
                        $tpStmt->execute([$driverId]);
                        $driverTrainingProgress = $tpStmt->fetchAll(\PDO::FETCH_ASSOC);

                        $certStmt = $this->db->prepare("SELECT * FROM driver_certificates WHERE driver_id = ? LIMIT 1");
                        $certStmt->execute([$driverId]);
                        $driverCert = $certStmt->fetch(\PDO::FETCH_ASSOC);
                    }
                }
            }

            view('admin.leads.view', [
                'lead' => $lead,
                'activities' => $activities,
                'leadTags' => $leadTags,
                'allTags' => $allTags,
                'admins' => $admins,
                'supplierApp' => $supplierApp,
                'linkedSupplier' => $linkedSupplier,
                'driverApp' => $driverApp,
                'driverMessages' => $driverMessages,
                'driverTrainingProgress' => $driverTrainingProgress,
                'driverCert' => $driverCert,
                'driverComplianceDocs' => $driverComplianceDocs,
                'businessProfile' => $businessProfile,
            ]);

        } catch (\PDOException $e) {
            error_log('Lead view error: ' . $e->getMessage());
            setFlash('error', 'Error loading lead.');
            redirect('admin/leads');
        }
    }

    /**
     * Show edit form
     */
    public function edit(): void
    {


        $leadId = (int)($_GET['id'] ?? 0);

        try {
            $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = ?");
            $stmt->execute([$leadId]);
            $lead = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$lead) {
                setFlash('error', 'Lead not found.');
                redirect('admin/leads');
                return;
            }

            // Get admins
            $adminsStmt = $this->db->query("SELECT id, CONCAT(first_name, ' ', last_name) as name FROM users WHERE role IN ('admin', 'super_admin') ORDER BY first_name, last_name");
            $admins = $adminsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get tags
            $tagsStmt = $this->db->query("SELECT * FROM lead_tags ORDER BY name");
            $tags = $tagsStmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get lead's current tags
            $leadTagsStmt = $this->db->prepare("SELECT tag_id FROM lead_tag_assignments WHERE lead_id = ?");
            $leadTagsStmt->execute([$leadId]);
            $leadTagIds = $leadTagsStmt->fetchAll(\PDO::FETCH_COLUMN);

            view('admin.leads.edit', [
                'lead' => $lead,
                'admins' => $admins,
                'tags' => $tags,
                'leadTagIds' => $leadTagIds
            ]);

        } catch (\PDOException $e) {
            error_log('Lead edit form error: ' . $e->getMessage());
            setFlash('error', 'Error loading lead.');
            redirect('admin/leads');
        }
    }

    /**
     * Update lead
     */
    public function update(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        $leadId = (int)($_POST['lead_id'] ?? 0);

        try {
            // Get current lead for comparison
            $currentStmt = $this->db->prepare("SELECT status FROM leads WHERE id = ?");
            $currentStmt->execute([$leadId]);
            $current = $currentStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$current) {
                setFlash('error', 'Lead not found.');
                redirect('admin/leads');
                return;
            }

            $newStatus = sanitize($_POST['status'] ?? 'new');

            $stmt = $this->db->prepare("
                UPDATE leads SET
                    first_name = ?, last_name = ?, email = ?, phone = ?,
                    company_name = ?, job_title = ?,
                    source = ?, source_details = ?, status = ?, priority = ?,
                    interest_type = ?, interest_details = ?, estimated_value = ?,
                    city = ?, province = ?, country = ?,
                    assigned_to = ?, notes = ?, next_follow_up = ?,
                    converted_at = ?
                WHERE id = ?
            ");

            $convertedAt = null;
            if ($newStatus === 'won' && $current['status'] !== 'won') {
                $convertedAt = date('Y-m-d H:i:s');
            }

            $stmt->execute([
                sanitize($_POST['first_name'] ?? ''),
                sanitize($_POST['last_name'] ?? ''),
                sanitize($_POST['email'] ?? ''),
                sanitize($_POST['phone'] ?? ''),
                sanitize($_POST['company_name'] ?? ''),
                sanitize($_POST['job_title'] ?? ''),
                sanitize($_POST['source'] ?? 'manual'),
                sanitize($_POST['source_details'] ?? ''),
                $newStatus,
                sanitize($_POST['priority'] ?? 'medium'),
                sanitize($_POST['interest_type'] ?? 'other'),
                sanitize($_POST['interest_details'] ?? ''),
                !empty($_POST['estimated_value']) ? (float)$_POST['estimated_value'] : null,
                sanitize($_POST['city'] ?? ''),
                sanitize($_POST['province'] ?? ''),
                sanitize($_POST['country'] ?? 'Canada'),
                !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null,
                sanitize($_POST['notes'] ?? ''),
                !empty($_POST['next_follow_up']) ? $_POST['next_follow_up'] : null,
                $convertedAt,
                $leadId
            ]);

            // Update tags
            $this->db->prepare("DELETE FROM lead_tag_assignments WHERE lead_id = ?")->execute([$leadId]);
            if (!empty($_POST['tags']) && is_array($_POST['tags'])) {
                $tagStmt = $this->db->prepare("INSERT INTO lead_tag_assignments (lead_id, tag_id) VALUES (?, ?)");
                foreach ($_POST['tags'] as $tagId) {
                    $tagStmt->execute([$leadId, (int)$tagId]);
                }
            }

            // Log status change
            if ($current['status'] !== $newStatus) {
                $this->logActivity($leadId, 'status_change', "Status changed from {$current['status']} to $newStatus", null);
            }

            setFlash('success', 'Lead updated successfully.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\PDOException $e) {
            error_log('Lead update error: ' . $e->getMessage());
            setFlash('error', 'Error updating lead.');
            redirect('admin/leads/edit?id=' . $leadId);
        }
    }

    /**
     * Add activity to lead
     */
    public function addActivity(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        $leadId = (int)($_POST['lead_id'] ?? 0);

        try {
            $activityType = sanitize($_POST['activity_type'] ?? 'note');
            $description = sanitize($_POST['description'] ?? '');
            $outcome = sanitize($_POST['outcome'] ?? '');

            $this->logActivity($leadId, $activityType, $description, $outcome);

            // Update last contacted if it was a contact activity
            if (in_array($activityType, ['call', 'email', 'meeting'])) {
                $this->db->prepare("UPDATE leads SET last_contacted_at = NOW() WHERE id = ?")->execute([$leadId]);
            }

            // Update follow-up if provided
            if (!empty($_POST['next_follow_up'])) {
                $this->db->prepare("UPDATE leads SET next_follow_up = ? WHERE id = ?")->execute([$_POST['next_follow_up'], $leadId]);
            }

            setFlash('success', 'Activity added.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\PDOException $e) {
            error_log('Add activity error: ' . $e->getMessage());
            setFlash('error', 'Error adding activity.');
            redirect('admin/leads/view?id=' . $leadId);
        }
    }

    /**
     * Quick status update
     */
    public function updateStatus(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        $leadId = (int)($_POST['lead_id'] ?? 0);
        $newStatus = sanitize($_POST['status'] ?? '');

        try {
            $currentStmt = $this->db->prepare("SELECT status FROM leads WHERE id = ?");
            $currentStmt->execute([$leadId]);
            $current = $currentStmt->fetch(\PDO::FETCH_ASSOC);

            if (!$current) {
                setFlash('error', 'Lead not found.');
                redirect('admin/leads');
                return;
            }

            $convertedAt = null;
            if ($newStatus === 'won' && $current['status'] !== 'won') {
                $convertedAt = date('Y-m-d H:i:s');
            }

            $stmt = $this->db->prepare("UPDATE leads SET status = ?, converted_at = COALESCE(?, converted_at) WHERE id = ?");
            $stmt->execute([$newStatus, $convertedAt, $leadId]);

            $this->logActivity($leadId, 'status_change', "Status changed from {$current['status']} to $newStatus", null);

            setFlash('success', 'Status updated.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\PDOException $e) {
            error_log('Status update error: ' . $e->getMessage());
            setFlash('error', 'Error updating status.');
            redirect('admin/leads');
        }
    }

    /**
     * Delete lead
     */
    public function delete(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        $leadId = (int)($_POST['lead_id'] ?? 0);

        try {
            $stmt = $this->db->prepare("DELETE FROM leads WHERE id = ?");
            $stmt->execute([$leadId]);

            setFlash('success', 'Lead deleted.');
            redirect('admin/leads');

        } catch (\PDOException $e) {
            error_log('Lead delete error: ' . $e->getMessage());
            setFlash('error', 'Error deleting lead.');
            redirect('admin/leads');
        }
    }

    /**
     * Approve supplier application - creates supplier account & sends welcome email
     */
    public function approveSupplier(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $leadId = (int)post('lead_id');
        $appId = (int)post('application_id');

        try {
            // Get the supplier application
            $stmt = $this->db->prepare("SELECT sa.*, l.first_name, l.last_name, l.email as lead_email, l.phone as lead_phone
                FROM supplier_applications sa
                JOIN leads l ON sa.lead_id = l.id
                WHERE sa.id = ? AND sa.lead_id = ?");
            $stmt->execute([$appId, $leadId]);
            $app = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$app) {
                setFlash('error', 'Supplier application not found.');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            }

            if ($app['status'] === 'approved') {
                setFlash('error', 'This application has already been approved.');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            }

            // Check if supplier with this email already exists (from application auto-creation)
            $checkStmt = $this->db->prepare("SELECT id, status, supplier_code FROM suppliers WHERE email = ?");
            $checkStmt->execute([$app['email']]);
            $existingSupplier = $checkStmt->fetch(\PDO::FETCH_ASSOC);

            $tempPassword = null;
            $sendCredentials = false;

            if ($existingSupplier && $existingSupplier['status'] === 'pending_verification') {
                // Supplier account was created during application — update it to active
                $supplierId = $existingSupplier['id'];
                $supplierCode = $existingSupplier['supplier_code'];
            } elseif ($existingSupplier) {
                setFlash('error', 'A supplier account with this email already exists (status: ' . $existingSupplier['status'] . ').');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            } else {
                // No existing account — will create a new one with temp password
                $tempPassword = $this->generateSecurePassword();
                $sendCredentials = true;
                $supplierCode = 'SUP-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
            }

            $this->db->beginTransaction();

            try {
                if ($existingSupplier && $existingSupplier['status'] === 'pending_verification') {
                    // Update existing pending_verification supplier to active
                    $updateStmt = $this->db->prepare("
                        UPDATE suppliers SET
                            status = 'active',
                            payment_terms = 'Net 30',
                            created_by = ?
                        WHERE id = ?
                    ");
                    $updateStmt->execute([
                        $_SESSION['user']['id'] ?? null,
                        $supplierId,
                    ]);
                } else {
                    // Create new supplier account (legacy path — no application account existed)
                    $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
                    $insertStmt = $this->db->prepare("
                        INSERT INTO suppliers (name, supplier_code, company_name, contact_person, email, phone,
                            address, city, province, postal_code, country,
                            tax_number, payment_terms, status, password_hash, can_login, created_by)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Canada', ?, 'Net 30', 'active', ?, 1, ?)
                    ");
                    $contactPerson = trim($app['first_name'] . ' ' . $app['last_name']);
                    $insertStmt->execute([
                        $app['business_name'] ?: $app['legal_name'],
                        $supplierCode,
                        $app['business_name'] ?: $app['legal_name'],
                        $contactPerson,
                        $app['email'],
                        $app['phone'] ?: $app['lead_phone'],
                        $app['registered_address_street'] ?? '',
                        $app['registered_address_city'] ?? '',
                        $app['registered_address_province'] ?? '',
                        $app['registered_address_postal'] ?? '',
                        $app['neq_number'] ?? '',
                        $hashedPassword,
                        $_SESSION['user']['id'] ?? null,
                    ]);
                    $supplierId = $this->db->lastInsertId();
                }

                // Update application status
                $this->db->prepare("
                    UPDATE supplier_applications SET status = 'approved', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ?
                    WHERE id = ?
                ")->execute([$_SESSION['user']['id'] ?? null, sanitize(post('admin_notes', '')), $appId]);

                // Update lead status to won
                $this->db->prepare("UPDATE leads SET status = 'won', converted_at = NOW() WHERE id = ?")->execute([$leadId]);

                $this->db->commit();

                // Log activity
                $this->logActivity($leadId, 'status_change', "Supplier application approved. Supplier account created (ID: {$supplierId}, Code: {$supplierCode}).", null);

                // Send welcome email
                $emailData = [
                    'email' => $app['email'],
                    'first_name' => $app['first_name'],
                    'last_name' => $app['last_name'],
                    'company_name' => $app['business_name'] ?: $app['legal_name'],
                    'supplier_code' => $supplierCode,
                ];
                if ($sendCredentials && $tempPassword) {
                    $emailData['temp_password'] = $tempPassword;
                }
                \App\Helpers\EmailHelper::sendSupplierApproved($emailData);

                // Bell notifications
                $companyName = $app['business_name'] ?: $app['legal_name'];
                \App\Helpers\NotificationHelper::add(
                    'supplier',
                    "Supplier Approved: {$companyName}",
                    "Application approved. Supplier account #{$supplierCode} created for " . ($app['first_name'] . ' ' . $app['last_name']),
                    ['link' => "/admin/leads/view?id={$leadId}", 'icon' => 'check-circle', 'priority' => 'normal']
                );
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supplierId,
                    'account',
                    'Application Approved!',
                    'Your supplier application has been approved. Welcome to OCSAPP! You can now manage your products and orders.',
                    'supplier/dashboard',
                    'check-circle',
                    'Demande approuvée !',
                    'Votre demande de fournisseur a été approuvée. Bienvenue sur OCSAPP ! Vous pouvez maintenant gérer vos produits et vos commandes.'
                );

                // Audit log
                if (function_exists('auditLog')) {
                    auditLog('supplier_approved', "Approved supplier application #{$appId}. Created supplier #{$supplierId} ({$supplierCode})", $supplierId);
                }

                setFlash('success', "Supplier application approved! Account created for <strong>" . htmlspecialchars($app['email']) . "</strong>. Welcome email sent with credentials.");
                redirect('admin/leads/view?id=' . $leadId);

            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            error_log('Approve supplier error: ' . $e->getMessage());
            setFlash('error', 'Error approving supplier: ' . $e->getMessage());
            redirect('admin/leads/view?id=' . $leadId);
        }
    }

    /**
     * Reject supplier application & send rejection email
     */
    public function rejectSupplier(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $leadId = (int)post('lead_id');
        $appId = (int)post('application_id');
        $reason = sanitize(post('rejection_reason', ''));
        $notes = sanitize(post('admin_notes', ''));

        if (empty($reason)) {
            setFlash('error', 'Please provide a rejection reason.');
            redirect('admin/leads/view?id=' . $leadId);
            return;
        }

        try {
            // Get the application
            $stmt = $this->db->prepare("SELECT sa.*, l.first_name, l.last_name, l.email as lead_email
                FROM supplier_applications sa
                JOIN leads l ON sa.lead_id = l.id
                WHERE sa.id = ? AND sa.lead_id = ?");
            $stmt->execute([$appId, $leadId]);
            $app = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$app) {
                setFlash('error', 'Supplier application not found.');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            }

            $fullNotes = $reason . ($notes ? "\n\nAdditional notes: " . $notes : '');

            // Update application
            $this->db->prepare("
                UPDATE supplier_applications SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW(), admin_notes = ?
                WHERE id = ?
            ")->execute([$_SESSION['user']['id'] ?? null, $fullNotes, $appId]);

            // Update lead status
            $this->db->prepare("UPDATE leads SET status = 'lost' WHERE id = ?")->execute([$leadId]);

            // Log activity
            $this->logActivity($leadId, 'status_change', "Supplier application rejected. Reason: {$reason}", null);

            // Send rejection email
            \App\Helpers\EmailHelper::sendSupplierRejected([
                'email' => $app['email'],
                'first_name' => $app['first_name'],
                'last_name' => $app['last_name'],
                'company_name' => $app['business_name'] ?: $app['legal_name'],
                'reason' => $reason,
                'notes' => $notes,
            ]);

            // Bell notifications
            $companyName = $app['business_name'] ?: $app['legal_name'];
            \App\Helpers\NotificationHelper::add(
                'supplier',
                "Supplier Rejected: {$companyName}",
                "Application rejected. Reason: {$reason}",
                ['link' => "/admin/leads/view?id={$leadId}", 'icon' => 'times-circle', 'priority' => 'normal']
            );
            if (!empty($app['supplier_id'])) {
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    (int) $app['supplier_id'],
                    'account',
                    'Application Update',
                    'Your supplier application status has been updated. Please check your email for details.',
                    'supplier/documents',
                    'info-circle',
                    'Mise à jour de votre demande',
                    'Le statut de votre demande a été mis à jour. Veuillez consulter votre courriel pour plus de détails.'
                );
            }

            if (function_exists('auditLog')) {
                auditLog('supplier_rejected', "Rejected supplier application #{$appId}. Reason: {$reason}", null);
            }

            setFlash('success', 'Supplier application rejected. Notification email sent.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\Exception $e) {
            error_log('Reject supplier error: ' . $e->getMessage());
            setFlash('error', 'Error rejecting supplier.');
            redirect('admin/leads/view?id=' . $leadId);
        }
    }

    /**
     * Request more info from supplier applicant
     */
    public function requestSupplierInfo(): void
    {


        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $leadId = (int)post('lead_id');
        $appId = (int)post('application_id');
        $additionalNotes = sanitize(post('info_message', ''));
        $missingDocs = post('missing_docs', []);

        // Build the full message from checkboxes + notes
        $docLabels = [
            'doc_certificate_incorporation' => 'Certificate of Incorporation',
            'doc_declaration_registration' => 'Declaration of Registration',
            'doc_enterprise_register' => 'Enterprise Register File Search',
        ];

        $messageParts = [];
        if (!empty($missingDocs) && is_array($missingDocs)) {
            $docNames = [];
            foreach ($missingDocs as $docField) {
                if (isset($docLabels[$docField])) {
                    $docNames[] = $docLabels[$docField];
                }
            }
            if (!empty($docNames)) {
                $messageParts[] = "Please upload the following document(s):\n- " . implode("\n- ", $docNames);
            }
        }

        if (!empty($additionalNotes)) {
            $messageParts[] = $additionalNotes;
        }

        $message = implode("\n\n", $messageParts);

        if (empty($message)) {
            setFlash('error', 'Please select at least one document or add a note.');
            redirect('admin/leads/view?id=' . $leadId);
            return;
        }

        try {
            // Get the application
            $stmt = $this->db->prepare("SELECT sa.*, l.first_name, l.last_name, l.email as lead_email
                FROM supplier_applications sa
                JOIN leads l ON sa.lead_id = l.id
                WHERE sa.id = ? AND sa.lead_id = ?");
            $stmt->execute([$appId, $leadId]);
            $app = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$app) {
                setFlash('error', 'Supplier application not found.');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            }

            // Update application status
            $this->db->prepare("
                UPDATE supplier_applications SET status = 'info_requested', admin_notes = CONCAT(COALESCE(admin_notes, ''), ?)
                WHERE id = ?
            ")->execute(["\n[Info Requested " . date('M j, Y') . "]: " . $message, $appId]);

            // Update lead status to in-progress
            $this->db->prepare("UPDATE leads SET status = 'contacted' WHERE id = ?")->execute([$leadId]);

            // Log activity
            $this->logActivity($leadId, 'email', "Requested additional information from supplier applicant: {$message}", null);

            // Send info request email
            \App\Helpers\EmailHelper::sendSupplierInfoRequest([
                'email' => $app['email'],
                'first_name' => $app['first_name'],
                'last_name' => $app['last_name'],
                'company_name' => $app['business_name'] ?: $app['legal_name'],
                'message' => $message,
            ]);

            // Bell notifications
            $companyName = $app['business_name'] ?: $app['legal_name'];
            \App\Helpers\NotificationHelper::add(
                'supplier',
                "Info Requested: {$companyName}",
                "Additional information requested from " . ($app['first_name'] . ' ' . $app['last_name']),
                ['link' => "/admin/leads/view?id={$leadId}", 'icon' => 'question-circle', 'priority' => 'normal']
            );
            if (!empty($app['supplier_id'])) {
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    (int) $app['supplier_id'],
                    'document',
                    'Documents Requested',
                    'We need additional documents for your application. Please upload them in My Documents.',
                    'supplier/documents',
                    'upload',
                    'Documents requis',
                    'Nous avons besoin de documents supplémentaires pour votre demande. Veuillez les téléverser dans Mes documents.'
                );
            }

            setFlash('success', 'Information request sent to the applicant.');
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\Exception $e) {
            error_log('Request supplier info error: ' . $e->getMessage());
            setFlash('error', 'Error sending information request.');
            redirect('admin/leads/view?id=' . $leadId);
        }
    }

    /**
     * Generate a secure random password
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lower = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '!@#$%*-_+=';

        $password = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $all = $upper . $lower . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Log activity
     */
    private function logActivity(int $leadId, string $type, string $description, ?string $outcome): void
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $stmt = $this->db->prepare("
            INSERT INTO lead_activities (lead_id, activity_type, description, outcome, created_by)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$leadId, $type, $description, $outcome, $userId]);
    }

    /**
     * Extend supplier verification deadline
     */
    public function extendSupplierDeadline(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/leads');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $leadId = (int)post('lead_id');
        $supplierId = (int)post('supplier_id');
        $extraDays = (int)post('extra_days', 15);

        // Clamp between 7 and 60 days
        $extraDays = max(7, min(60, $extraDays));

        try {
            $stmt = $this->db->prepare("
                SELECT id, email, company_name, name, verification_deadline, status
                FROM suppliers WHERE id = ?
            ");
            $stmt->execute([$supplierId]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$supplier) {
                setFlash('error', 'Supplier not found.');
                redirect('admin/leads/view?id=' . $leadId);
                return;
            }

            // Calculate new deadline
            $currentDeadline = $supplier['verification_deadline'] ? strtotime($supplier['verification_deadline']) : time();
            $base = max($currentDeadline, time()); // Don't extend from a past date
            $newDeadline = date('Y-m-d H:i:s', $base + ($extraDays * 86400));

            // If supplier was deactivated due to expiry, reactivate them
            $reactivated = false;
            if ($supplier['status'] === 'inactive') {
                $this->db->prepare("UPDATE suppliers SET status = 'pending_verification', can_login = 1, verification_deadline = ?, verification_reminder_sent = 0 WHERE id = ?")
                    ->execute([$newDeadline, $supplierId]);
                $reactivated = true;
            } else {
                $this->db->prepare("UPDATE suppliers SET verification_deadline = ? WHERE id = ?")
                    ->execute([$newDeadline, $supplierId]);
            }

            $formattedDeadline = date('M j, Y', strtotime($newDeadline));

            if ($leadId > 0) {
                $this->logActivity($leadId, 'note', "Verification deadline extended by {$extraDays} days to {$formattedDeadline}." . ($reactivated ? " Account reactivated." : ""), null);
            }

            if (function_exists('auditLog')) {
                auditLog('supplier_deadline_extended', "Extended supplier #{$supplierId} deadline by {$extraDays} days to {$formattedDeadline}" . ($reactivated ? " (reactivated)" : ""), $supplierId);
            }

            $msg = "Verification deadline extended to <strong>{$formattedDeadline}</strong> (+{$extraDays} days).";
            if ($reactivated) {
                $msg .= " Account has been reactivated.";
            }
            setFlash('success', $msg);
            redirect('admin/leads/view?id=' . $leadId);

        } catch (\Exception $e) {
            error_log('Extend deadline error: ' . $e->getMessage());
            setFlash('error', 'Error extending deadline: ' . $e->getMessage());
            redirect('admin/leads/view?id=' . $leadId);
        }
    }

    /**
     * Update document review status (AJAX)
     */
    public function updateDocStatus(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Invalid method'], 405);
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 400);
        }

        $appId = (int)post('application_id');
        $docField = post('doc_field');
        $status = post('status');

        // Validate doc field
        $validFields = [
            'doc_certificate_incorporation',
            'doc_declaration_registration',
            'doc_enterprise_register',
        ];
        if (!in_array($docField, $validFields)) {
            jsonResponse(['success' => false, 'message' => 'Invalid document field'], 400);
        }

        // Validate status
        $validStatuses = ['pending', 'approved', 'rejected', 'na'];
        if (!in_array($status, $validStatuses)) {
            jsonResponse(['success' => false, 'message' => 'Invalid status'], 400);
        }

        try {
            $statusCol = $docField . '_status';
            $stmt = $this->db->prepare("UPDATE supplier_applications SET {$statusCol} = ? WHERE id = ?");
            $stmt->execute([$status, $appId]);

            // Notify supplier about the status change
            $appRow = $this->db->prepare("SELECT supplier_id FROM supplier_applications WHERE id = ?");
            $appRow->execute([$appId]);
            $supplierId = (int) $appRow->fetchColumn();

            if ($supplierId) {
                $docLabels = [
                    'doc_certificate_incorporation' => 'Certificate of Incorporation',
                    'doc_declaration_registration'  => 'Declaration of Registration',
                    'doc_enterprise_register'       => 'Enterprise Register File Search',
                ];
                $docLabelsFr = [
                    'doc_certificate_incorporation' => 'Certificat de constitution',
                    'doc_declaration_registration'  => 'Déclaration d\'immatriculation',
                    'doc_enterprise_register'       => 'Recherche au registre des entreprises',
                ];
                $docLabel   = $docLabels[$docField]   ?? $docField;
                $docLabelFr = $docLabelsFr[$docField] ?? $docLabel;

                $notifConfig = [
                    'approved' => ['title' => 'Document Approved',      'body' => "Your {$docLabel} has been approved.",                                          'icon' => 'check-circle',  'title_fr' => 'Document approuvé',           'body_fr' => "Votre {$docLabelFr} a été approuvé."],
                    'rejected' => ['title' => 'Document Rejected',      'body' => "Your {$docLabel} was rejected. Please upload a corrected version.",            'icon' => 'times-circle', 'title_fr' => 'Document refusé',             'body_fr' => "Votre {$docLabelFr} a été refusé. Veuillez téléverser une version corrigée."],
                    'na'       => ['title' => 'Document Not Required',  'body' => "Your {$docLabel} has been marked as not required for your account.",           'icon' => 'minus-circle', 'title_fr' => 'Document non requis',         'body_fr' => "Votre {$docLabelFr} a été marqué comme non requis pour votre compte."],
                    'pending'  => ['title' => 'Document Under Review',  'body' => "Your {$docLabel} has been reset and is under review.",                         'icon' => 'clock',        'title_fr' => 'Document en cours d\'examen', 'body_fr' => "Votre {$docLabelFr} a été réinitialisé et est en cours d'examen."],
                ];

                if (isset($notifConfig[$status])) {
                    $cfg = $notifConfig[$status];
                    try {
                        \App\Helpers\NotificationHelper::addSupplierNotification(
                            $supplierId,
                            'document_status',
                            $cfg['title'],
                            $cfg['body'],
                            'supplier/documents',
                            $cfg['icon'],
                            $cfg['title_fr'],
                            $cfg['body_fr']
                        );
                    } catch (\Exception $ne) {
                        error_log('Doc status notification error: ' . $ne->getMessage());
                    }
                }
            }

            jsonResponse(['success' => true, 'message' => 'Document status updated']);
        } catch (\Exception $e) {
            error_log('Doc status update error: ' . $e->getMessage());
            jsonResponse(['success' => false, 'message' => 'Error updating status'], 500);
        }
    }

    /**
     * Check admin access
     */
}
