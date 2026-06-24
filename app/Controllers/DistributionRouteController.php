<?php

namespace App\Controllers;

/**
 * DistributionRouteController - Recurring Routes Management
 * Handles creation and management of recurring distribution routes
 */
class DistributionRouteController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function getBusinessData(): array
    {
        $id = $_SESSION['business']['id'] ?? null;
        if (!$id) return $_SESSION['business'] ?? [];
        try {
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.email, u.phone
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ? LIMIT 1
            ");
            $stmt->execute([$id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC) ?: ($_SESSION['business'] ?? []);
        } catch (\Exception $e) {
            return $_SESSION['business'] ?? [];
        }
    }

    /**
     * List all recurring routes
     */
    public function index(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];
        $status = sanitize($_GET['status'] ?? '');

        try {
            $whereClause = "WHERE business_profile_id = ?";
            $params = [$businessId];

            if ($status && in_array($status, ['active', 'paused', 'completed', 'cancelled'])) {
                $whereClause .= " AND status = ?";
                $params[] = $status;
            }

            $stmt = $this->db->prepare("
                SELECT r.*,
                    (SELECT COUNT(*) FROM distribution_shipments WHERE recurring_route_id = r.id) as shipments_count
                FROM distribution_recurring_routes r
                $whereClause
                ORDER BY r.created_at DESC
            ");
            $stmt->execute($params);
            $routes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get stats
            $statsStmt = $this->db->prepare("
                SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'paused' THEN 1 ELSE 0 END) as paused
                FROM distribution_recurring_routes
                WHERE business_profile_id = ?
            ");
            $statsStmt->execute([$businessId]);
            $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

            view('distribution.routes.index', [
                'routes'        => $routes,
                'stats'         => $stats,
                'currentStatus' => $status,
                'business'      => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution routes index error: ' . $e->getMessage());
            setFlash('error', 'Error loading routes.');
            redirect('distribution/dashboard');
        }
    }

    /**
     * Show create route form
     */
    public function create(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        // Get business profile
        $stmt = $this->db->prepare("
            SELECT bp.*, u.first_name, u.last_name, u.phone
            FROM business_profiles bp
            INNER JOIN users u ON bp.user_id = u.id
            WHERE bp.id = ?
        ");
        $stmt->execute([$businessId]);
        $business = $stmt->fetch(\PDO::FETCH_ASSOC);

        view('distribution.routes.create', [
            'business' => $business,
            'errors' => $_SESSION['route_errors'] ?? [],
            'old' => $_SESSION['route_old'] ?? []
        ]);

        unset($_SESSION['route_errors'], $_SESSION['route_old']);
    }

    /**
     * Store new recurring route
     */
    public function store(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/create');
            return;
        }

        $businessId = $_SESSION['business']['id'];

        // Collect data
        $data = [
            'route_name' => sanitize($_POST['route_name'] ?? ''),
            'frequency' => sanitize($_POST['frequency'] ?? 'weekly'),
            'days_of_week' => $_POST['days_of_week'] ?? [],
            'day_of_month' => !empty($_POST['day_of_month']) ? (int)$_POST['day_of_month'] : null,
            'start_date' => sanitize($_POST['start_date'] ?? ''),
            'end_date' => sanitize($_POST['end_date'] ?? '') ?: null,
            'pickup_street' => sanitize($_POST['pickup_street'] ?? ''),
            'pickup_city' => sanitize($_POST['pickup_city'] ?? ''),
            'pickup_province' => sanitize($_POST['pickup_province'] ?? ''),
            'pickup_postal_code' => strtoupper(sanitize($_POST['pickup_postal_code'] ?? '')),
            'pickup_time_start' => sanitize($_POST['pickup_time_start'] ?? '') ?: null,
            'pickup_time_end' => sanitize($_POST['pickup_time_end'] ?? '') ?: null,
            'auto_submit' => isset($_POST['auto_submit']) ? 1 : 0,
            'notify_days_before' => (int)($_POST['notify_days_before'] ?? 2)
        ];

        // Collect destinations
        $destinations = [];
        if (isset($_POST['destinations'])) {
            foreach ($_POST['destinations'] as $index => $dest) {
                if (!empty($dest['street'])) {
                    $destinations[] = [
                        'sequence_order' => $index + 1,
                        'destination_name' => sanitize($dest['name'] ?? ''),
                        'street' => sanitize($dest['street']),
                        'city' => sanitize($dest['city'] ?? ''),
                        'province' => sanitize($dest['province'] ?? ''),
                        'postal_code' => strtoupper(sanitize($dest['postal_code'] ?? '')),
                        'contact_name' => sanitize($dest['contact_name'] ?? ''),
                        'contact_phone' => sanitize($dest['contact_phone'] ?? ''),
                        'delivery_instructions' => sanitize($dest['instructions'] ?? ''),
                        'packages_count' => (int)($dest['packages_count'] ?? 1)
                    ];
                }
            }
        }

        // Validation
        $errors = $this->validateRoute($data, $destinations);

        if (!empty($errors)) {
            $_SESSION['route_errors'] = $errors;
            $_SESSION['route_old'] = $_POST;
            redirect('distribution/routes/create');
            return;
        }

        try {
            // Calculate next generation date
            $nextGenDate = $this->calculateNextGenerationDate(
                $data['frequency'],
                $data['days_of_week'],
                $data['day_of_month'],
                $data['start_date']
            );

            $stmt = $this->db->prepare("
                INSERT INTO distribution_recurring_routes
                (business_profile_id, route_name, frequency, days_of_week, day_of_month,
                 pickup_street, pickup_city, pickup_province, pickup_postal_code,
                 pickup_time_start, pickup_time_end, destinations_template,
                 start_date, end_date, next_generation_date, auto_submit, notify_days_before,
                 status, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?,
                        ?, ?, ?, ?,
                        ?, ?, ?,
                        ?, ?, ?, ?, ?,
                        'active', NOW(), NOW())
            ");

            $stmt->execute([
                $businessId,
                $data['route_name'],
                $data['frequency'],
                !empty($data['days_of_week']) ? json_encode($data['days_of_week']) : null,
                $data['day_of_month'],
                $data['pickup_street'],
                $data['pickup_city'],
                $data['pickup_province'],
                $data['pickup_postal_code'],
                $data['pickup_time_start'],
                $data['pickup_time_end'],
                json_encode($destinations),
                $data['start_date'],
                $data['end_date'],
                $nextGenDate,
                $data['auto_submit'],
                $data['notify_days_before']
            ]);

            $routeId = $this->db->lastInsertId();

            setFlash('success', 'Recurring route created successfully.');
            redirect('distribution/routes/show?id=' . $routeId);

        } catch (\PDOException $e) {
            error_log('Distribution route store error: ' . $e->getMessage());
            setFlash('error', 'Error creating route.');
            redirect('distribution/routes/create');
        }
    }

    /**
     * View route details
     */
    public function show(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $routeId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_recurring_routes
                WHERE id = ? AND business_profile_id = ?
            ");
            $stmt->execute([$routeId, $businessId]);
            $route = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$route) {
                setFlash('error', 'Route not found.');
                redirect('distribution/routes');
                return;
            }

            // Parse JSON fields
            $route['days_of_week'] = $route['days_of_week'] ? json_decode($route['days_of_week'], true) : [];
            $route['destinations_template'] = json_decode($route['destinations_template'], true);

            // Get generated shipments
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_shipments
                WHERE recurring_route_id = ?
                ORDER BY created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$routeId]);
            $shipments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('distribution.routes.show', [
                'route'     => $route,
                'shipments' => $shipments,
                'business'  => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution route show error: ' . $e->getMessage());
            setFlash('error', 'Error loading route.');
            redirect('distribution/routes');
        }
    }

    /**
     * Edit route
     */
    public function edit(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $routeId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM distribution_recurring_routes
                WHERE id = ? AND business_profile_id = ? AND status IN ('active', 'paused')
            ");
            $stmt->execute([$routeId, $businessId]);
            $route = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$route) {
                setFlash('error', 'Route not found or cannot be edited.');
                redirect('distribution/routes');
                return;
            }

            // Parse JSON fields
            $route['days_of_week'] = $route['days_of_week'] ? json_decode($route['days_of_week'], true) : [];
            $route['destinations_template'] = json_decode($route['destinations_template'], true);

            // Get business profile
            $stmt = $this->db->prepare("
                SELECT bp.*, u.first_name, u.last_name, u.phone
                FROM business_profiles bp
                INNER JOIN users u ON bp.user_id = u.id
                WHERE bp.id = ?
            ");
            $stmt->execute([$businessId]);
            $business = $stmt->fetch(\PDO::FETCH_ASSOC);

            view('distribution.routes.edit', [
                'route' => $route,
                'business' => $business,
                'errors' => $_SESSION['route_errors'] ?? [],
                'old' => $_SESSION['route_old'] ?? []
            ]);

            unset($_SESSION['route_errors'], $_SESSION['route_old']);

        } catch (\PDOException $e) {
            error_log('Distribution route edit error: ' . $e->getMessage());
            setFlash('error', 'Error loading route.');
            redirect('distribution/routes');
        }
    }

    /**
     * Update route
     */
    public function update(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        $routeId = (int)($_POST['route_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/edit?id=' . $routeId);
            return;
        }

        try {
            // Verify ownership
            $stmt = $this->db->prepare("SELECT id FROM distribution_recurring_routes WHERE id = ? AND business_profile_id = ? AND status IN ('active', 'paused')");
            $stmt->execute([$routeId, $businessId]);
            if (!$stmt->fetch()) {
                setFlash('error', 'Route not found or cannot be edited.');
                redirect('distribution/routes');
                return;
            }

            // Collect destinations
            $destinations = [];
            if (isset($_POST['destinations'])) {
                foreach ($_POST['destinations'] as $index => $dest) {
                    if (!empty($dest['street'])) {
                        $destinations[] = [
                            'sequence_order' => $index + 1,
                            'destination_name' => sanitize($dest['name'] ?? ''),
                            'street' => sanitize($dest['street']),
                            'city' => sanitize($dest['city'] ?? ''),
                            'province' => sanitize($dest['province'] ?? ''),
                            'postal_code' => strtoupper(sanitize($dest['postal_code'] ?? '')),
                            'contact_name' => sanitize($dest['contact_name'] ?? ''),
                            'contact_phone' => sanitize($dest['contact_phone'] ?? ''),
                            'delivery_instructions' => sanitize($dest['instructions'] ?? ''),
                            'packages_count' => (int)($dest['packages_count'] ?? 1)
                        ];
                    }
                }
            }

            $frequency = sanitize($_POST['frequency'] ?? 'weekly');
            $daysOfWeek = $_POST['days_of_week'] ?? [];
            $dayOfMonth = !empty($_POST['day_of_month']) ? (int)$_POST['day_of_month'] : null;
            $startDate = sanitize($_POST['start_date'] ?? '');

            // Recalculate next generation date
            $nextGenDate = $this->calculateNextGenerationDate($frequency, $daysOfWeek, $dayOfMonth, $startDate);

            $stmt = $this->db->prepare("
                UPDATE distribution_recurring_routes SET
                    route_name = ?,
                    frequency = ?,
                    days_of_week = ?,
                    day_of_month = ?,
                    pickup_street = ?,
                    pickup_city = ?,
                    pickup_province = ?,
                    pickup_postal_code = ?,
                    pickup_time_start = ?,
                    pickup_time_end = ?,
                    destinations_template = ?,
                    start_date = ?,
                    end_date = ?,
                    next_generation_date = ?,
                    auto_submit = ?,
                    notify_days_before = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                sanitize($_POST['route_name'] ?? ''),
                $frequency,
                !empty($daysOfWeek) ? json_encode($daysOfWeek) : null,
                $dayOfMonth,
                sanitize($_POST['pickup_street'] ?? ''),
                sanitize($_POST['pickup_city'] ?? ''),
                sanitize($_POST['pickup_province'] ?? ''),
                strtoupper(sanitize($_POST['pickup_postal_code'] ?? '')),
                sanitize($_POST['pickup_time_start'] ?? '') ?: null,
                sanitize($_POST['pickup_time_end'] ?? '') ?: null,
                json_encode($destinations),
                $startDate,
                sanitize($_POST['end_date'] ?? '') ?: null,
                $nextGenDate,
                isset($_POST['auto_submit']) ? 1 : 0,
                (int)($_POST['notify_days_before'] ?? 2),
                $routeId
            ]);

            setFlash('success', 'Route updated successfully.');
            redirect('distribution/routes/show?id=' . $routeId);

        } catch (\PDOException $e) {
            error_log('Distribution route update error: ' . $e->getMessage());
            setFlash('error', 'Error updating route.');
            redirect('distribution/routes/edit?id=' . $routeId);
        }
    }

    /**
     * Pause a route
     */
    public function pause(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        $routeId = (int)($_POST['route_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/show?id=' . $routeId);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE distribution_recurring_routes
                SET status = 'paused', updated_at = NOW()
                WHERE id = ? AND business_profile_id = ? AND status = 'active'
            ");
            $stmt->execute([$routeId, $businessId]);

            if ($stmt->rowCount() > 0) {
                setFlash('success', 'Route paused successfully.');
            } else {
                setFlash('error', 'Route not found or already paused.');
            }

            redirect('distribution/routes/show?id=' . $routeId);

        } catch (\PDOException $e) {
            error_log('Distribution route pause error: ' . $e->getMessage());
            setFlash('error', 'Error pausing route.');
            redirect('distribution/routes/show?id=' . $routeId);
        }
    }

    /**
     * Resume a paused route
     */
    public function resume(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        $routeId = (int)($_POST['route_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/show?id=' . $routeId);
            return;
        }

        try {
            // Get route to recalculate next generation date
            $stmt = $this->db->prepare("SELECT * FROM distribution_recurring_routes WHERE id = ? AND business_profile_id = ? AND status = 'paused'");
            $stmt->execute([$routeId, $businessId]);
            $route = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$route) {
                setFlash('error', 'Route not found or not paused.');
                redirect('distribution/routes');
                return;
            }

            // Recalculate next generation date from today
            $daysOfWeek = $route['days_of_week'] ? json_decode($route['days_of_week'], true) : [];
            $nextGenDate = $this->calculateNextGenerationDate(
                $route['frequency'],
                $daysOfWeek,
                $route['day_of_month'],
                date('Y-m-d')
            );

            $stmt = $this->db->prepare("
                UPDATE distribution_recurring_routes
                SET status = 'active', next_generation_date = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$nextGenDate, $routeId]);

            setFlash('success', 'Route resumed successfully.');
            redirect('distribution/routes/show?id=' . $routeId);

        } catch (\PDOException $e) {
            error_log('Distribution route resume error: ' . $e->getMessage());
            setFlash('error', 'Error resuming route.');
            redirect('distribution/routes/show?id=' . $routeId);
        }
    }

    /**
     * Cancel a route
     */
    public function cancel(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        $routeId = (int)($_POST['route_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/show?id=' . $routeId);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE distribution_recurring_routes
                SET status = 'cancelled', updated_at = NOW()
                WHERE id = ? AND business_profile_id = ? AND status IN ('active', 'paused')
            ");
            $stmt->execute([$routeId, $businessId]);

            if ($stmt->rowCount() > 0) {
                setFlash('success', 'Route cancelled successfully.');
            } else {
                setFlash('error', 'Route not found or already cancelled.');
            }

            redirect('distribution/routes');

        } catch (\PDOException $e) {
            error_log('Distribution route cancel error: ' . $e->getMessage());
            setFlash('error', 'Error cancelling route.');
            redirect('distribution/routes/show?id=' . $routeId);
        }
    }

    /**
     * View generated draft for approval
     */
    public function viewDraft(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        $shipmentId = (int)($_GET['id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        try {
            // Get draft shipment from recurring route
            $stmt = $this->db->prepare("
                SELECT s.*, r.route_name
                FROM distribution_shipments s
                INNER JOIN distribution_recurring_routes r ON s.recurring_route_id = r.id
                WHERE s.id = ? AND s.business_profile_id = ? AND s.status = 'draft' AND s.is_recurring_instance = 1
            ");
            $stmt->execute([$shipmentId, $businessId]);
            $shipment = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$shipment) {
                setFlash('error', 'Draft not found.');
                redirect('distribution/routes');
                return;
            }

            // Get destinations
            $stmt = $this->db->prepare("SELECT * FROM distribution_shipment_destinations WHERE shipment_id = ? ORDER BY sequence_order");
            $stmt->execute([$shipmentId]);
            $destinations = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('distribution.routes.draft-review', [
                'shipment'     => $shipment,
                'destinations' => $destinations,
                'business'     => $this->getBusinessData(),
            ]);

        } catch (\PDOException $e) {
            error_log('Distribution route draft view error: ' . $e->getMessage());
            setFlash('error', 'Error loading draft.');
            redirect('distribution/routes');
        }
    }

    /**
     * Approve draft and submit
     */
    public function approveDraft(): void
    {
        if (!$this->isBusinessLoggedIn()) {
            redirect('distribution/login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('distribution/routes');
            return;
        }

        $shipmentId = (int)($_POST['shipment_id'] ?? 0);
        $businessId = $_SESSION['business']['id'];

        if (!verifyCsrfToken($_POST['_csrf_token'] ?? '')) {
            setFlash('error', 'Invalid request.');
            redirect('distribution/routes/draft?id=' . $shipmentId);
            return;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE distribution_shipments
                SET status = 'submitted', submitted_at = NOW(), updated_at = NOW()
                WHERE id = ? AND business_profile_id = ? AND status = 'draft' AND is_recurring_instance = 1
            ");
            $stmt->execute([$shipmentId, $businessId]);

            if ($stmt->rowCount() > 0) {
                // Log status change
                $this->logStatusChange($shipmentId, 'draft', 'submitted', 'Recurring draft approved and submitted');
                setFlash('success', 'Shipment submitted successfully.');
                redirect('distribution/shipments/show?id=' . $shipmentId);
            } else {
                setFlash('error', 'Draft not found or already submitted.');
                redirect('distribution/routes');
            }

        } catch (\PDOException $e) {
            error_log('Distribution route approve draft error: ' . $e->getMessage());
            setFlash('error', 'Error approving draft.');
            redirect('distribution/routes/draft?id=' . $shipmentId);
        }
    }

    /**
     * Validate route data
     */
    private function validateRoute(array $data, array $destinations): array
    {
        $errors = [];

        if (empty($data['route_name'])) {
            $errors['route_name'] = 'Route name is required.';
        }

        if (empty($data['start_date'])) {
            $errors['start_date'] = 'Start date is required.';
        }

        if (empty($data['pickup_street'])) {
            $errors['pickup_street'] = 'Pickup address is required.';
        }

        if (empty($data['pickup_city'])) {
            $errors['pickup_city'] = 'Pickup city is required.';
        }

        if (empty($data['pickup_province'])) {
            $errors['pickup_province'] = 'Pickup province is required.';
        }

        if (empty($data['pickup_postal_code'])) {
            $errors['pickup_postal_code'] = 'Pickup postal code is required.';
        }

        if ($data['frequency'] === 'weekly' && empty($data['days_of_week'])) {
            $errors['days_of_week'] = 'Select at least one day of the week.';
        }

        if ($data['frequency'] === 'monthly' && empty($data['day_of_month'])) {
            $errors['day_of_month'] = 'Day of month is required for monthly routes.';
        }

        if (empty($destinations)) {
            $errors['destinations'] = 'At least one destination is required.';
        }

        return $errors;
    }

    /**
     * Calculate next generation date based on frequency
     */
    private function calculateNextGenerationDate(string $frequency, array $daysOfWeek, ?int $dayOfMonth, string $startDate): string
    {
        $start = new \DateTime($startDate);
        $today = new \DateTime();

        // If start date is in the future, use it
        if ($start > $today) {
            return $start->format('Y-m-d');
        }

        switch ($frequency) {
            case 'daily':
                return $today->modify('+1 day')->format('Y-m-d');

            case 'weekly':
                if (!empty($daysOfWeek)) {
                    // Find next occurrence of any selected day
                    $dayMap = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];
                    $selectedDays = array_map(fn($d) => $dayMap[strtolower($d)] ?? 0, $daysOfWeek);
                    sort($selectedDays);

                    $currentDay = (int)$today->format('w');
                    foreach ($selectedDays as $day) {
                        if ($day > $currentDay) {
                            $diff = $day - $currentDay;
                            return $today->modify("+{$diff} days")->format('Y-m-d');
                        }
                    }
                    // Next week
                    $diff = 7 - $currentDay + $selectedDays[0];
                    return $today->modify("+{$diff} days")->format('Y-m-d');
                }
                return $today->modify('+7 days')->format('Y-m-d');

            case 'biweekly':
                return $today->modify('+14 days')->format('Y-m-d');

            case 'monthly':
                if ($dayOfMonth) {
                    $next = new \DateTime($today->format('Y-m') . '-' . str_pad($dayOfMonth, 2, '0', STR_PAD_LEFT));
                    if ($next <= $today) {
                        $next->modify('+1 month');
                    }
                    return $next->format('Y-m-d');
                }
                return $today->modify('+1 month')->format('Y-m-d');

            default:
                return $today->modify('+7 days')->format('Y-m-d');
        }
    }

    /**
     * Log status change
     */
    private function logStatusChange(int $shipmentId, ?string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_shipment_status_history
                (shipment_id, old_status, new_status, changed_by_type, changed_by_id, notes, created_at)
                VALUES (?, ?, ?, 'business', ?, ?, NOW())
            ");
            $stmt->execute([
                $shipmentId,
                $fromStatus,
                $toStatus,
                $_SESSION['user']['id'] ?? null,
                $notes
            ]);
        } catch (\PDOException $e) {
            error_log('Shipment status history log error: ' . $e->getMessage());
        }
    }

    /**
     * Check if business user is logged in
     */
    private function isBusinessLoggedIn(): bool
    {
        if (!isset($_SESSION['user']['role'], $_SESSION['business']['id'])) {
            return false;
        }
        // Business users, or admins who also have a business profile
        return in_array($_SESSION['user']['role'], ['business', 'admin', 'super_admin'], true);
    }
}
