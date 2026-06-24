<?php

namespace App\Controllers;

use PDO;
use Exception;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

/**
 * AdminVehicleController
 *
 * Manage delivery vehicle fleet:
 * - Track vehicle inventory (bicycles, e-bikes, scooters, motorcycles, cars, vans)
 * - Assign vehicles to drivers
 * - Monitor vehicle status (active, maintenance, retired)
 * - Manage insurance and documentation
 * - View vehicle delivery history
 */

class AdminVehicleController {
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
     * Vehicle Fleet Index - List all vehicles with filtering
     * GET /admin/delivery/vehicles
     */
    public function index() {
        $filterStatus = get('status', '');
        $filterType = get('vehicle_type', '');

        // Build query with filters
        $whereClause = "WHERE 1=1";
        $params = [];

        if ($filterStatus) {
            $whereClause .= " AND v.status = ?";
            $params[] = $filterStatus;
        }

        if ($filterType) {
            $whereClause .= " AND v.vehicle_type = ?";
            $params[] = $filterType;
        }

        // Get vehicles with driver info
        $query = "
            SELECT
                v.*,
                u.first_name as driver_first_name,
                u.last_name as driver_last_name,
                u.email as driver_email,
                u.phone as driver_phone,
                (SELECT COUNT(*)
                 FROM delivery_assignments da
                 JOIN driver_availability dav ON da.driver_id = dav.driver_id
                 WHERE dav.current_vehicle_id = v.id
                 AND da.status = 'delivered') as total_deliveries
            FROM vehicles v
            LEFT JOIN users u ON v.driver_id = u.id
            {$whereClause}
            ORDER BY v.created_at DESC
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get statistics
        $statsQuery = "
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance,
                SUM(CASE WHEN status = 'retired' THEN 1 ELSE 0 END) as retired,
                SUM(CASE WHEN driver_id IS NULL THEN 1 ELSE 0 END) as unassigned
            FROM vehicles
        ";

        $stmt = $this->db->prepare($statsQuery);
        $stmt->execute();
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        return view('admin/delivery/vehicles/index', [
            'vehicles' => $vehicles,
            'stats' => $stats,
            'filterStatus' => $filterStatus,
            'filterType' => $filterType,
            'pageTitle' => 'Vehicle Fleet Management'
        ]);
    }

    /**
     * Show Create Vehicle Form
     * GET /admin/delivery/vehicles/create
     */
    public function create() {
        // Get available drivers (delivery role, no vehicle assigned)
        $query = "
            SELECT
                u.id,
                u.first_name,
                u.last_name,
                u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN vehicles v ON u.id = v.driver_id
            WHERE r.name = 'delivery'
            AND u.status = 'active'
            AND v.id IS NULL
            ORDER BY u.first_name, u.last_name
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return view('admin/delivery/vehicles/create', [
            'drivers' => $drivers,
            'pageTitle' => 'Add New Vehicle'
        ]);
    }

    /**
     * Store New Vehicle
     * POST /admin/delivery/vehicles/store
     */
    public function store() {
        if (!isPost()) {
            redirect(url('admin/delivery/vehicles'));
            return;
        }

        // Verify CSRF token
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        try {
            $this->db->beginTransaction();

            // Validate required field
            $vehicleType = post('vehicle_type');
            if (!$vehicleType) {
                throw new Exception('Vehicle type is required');
            }

            // Validate vehicle type enum
            $allowedTypes = ['bicycle', 'e-bike', 'scooter', 'motorcycle', 'car', 'van'];
            if (!in_array($vehicleType, $allowedTypes)) {
                throw new Exception('Invalid vehicle type');
            }

            // Get optional fields
            $make = sanitize(post('make', ''));
            $model = sanitize(post('model', ''));
            $year = post('year', null);
            $plateNumber = sanitize(post('plate_number', ''));
            $color = sanitize(post('color', ''));
            $insuranceExpiry = post('insurance_expiry', null);
            $driverId = post('driver_id', null);
            $notes = sanitize(post('notes', ''));
            $status = post('status', 'active');

            // Validate status enum
            $allowedStatuses = ['active', 'maintenance', 'retired'];
            if (!in_array($status, $allowedStatuses)) {
                $status = 'active';
            }

            // Check plate number uniqueness if provided
            if ($plateNumber) {
                $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE plate_number = ?");
                $stmt->execute([$plateNumber]);
                if ($stmt->fetch()) {
                    throw new Exception('A vehicle with this plate number already exists');
                }
            }

            // If driver is selected, check they don't already have a vehicle
            if ($driverId) {
                $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE driver_id = ?");
                $stmt->execute([$driverId]);
                if ($stmt->fetch()) {
                    throw new Exception('This driver already has a vehicle assigned');
                }
            }

            // Insert vehicle
            $stmt = $this->db->prepare("
                INSERT INTO vehicles
                (driver_id, vehicle_type, make, model, year, plate_number, color,
                 insurance_expiry, status, notes, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $stmt->execute([
                $driverId ?: null,
                $vehicleType,
                $make ?: null,
                $model ?: null,
                $year ?: null,
                $plateNumber ?: null,
                $color ?: null,
                $insuranceExpiry ?: null,
                $status,
                $notes ?: null
            ]);

            $vehicleId = $this->db->lastInsertId();

            // If driver assigned, update driver_availability
            if ($driverId) {
                $stmt = $this->db->prepare("
                    UPDATE driver_availability
                    SET current_vehicle_id = ?, updated_at = NOW()
                    WHERE driver_id = ?
                ");
                $stmt->execute([$vehicleId, $driverId]);
            }

            $this->db->commit();

            setFlash('success', 'Vehicle added successfully');
            redirect(url('admin/delivery/vehicles'));

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            setFlash('error', $e->getMessage());
            back();
        }
    }

    /**
     * Show Edit Vehicle Form
     * GET /admin/delivery/vehicles/edit?id=X
     */
    public function edit() {
        $vehicleId = (int) get('id', 0);

        if (!$vehicleId) {
            setFlash('error', 'Vehicle ID is required');
            redirect(url('admin/delivery/vehicles'));
            return;
        }

        try {
            // Get vehicle details
            $stmt = $this->db->prepare("
                SELECT v.*,
                       u.first_name as driver_first_name,
                       u.last_name as driver_last_name
                FROM vehicles v
                LEFT JOIN users u ON v.driver_id = u.id
                WHERE v.id = ?
            ");
            $stmt->execute([$vehicleId]);
            $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vehicle) {
                setFlash('error', 'Vehicle not found');
                redirect(url('admin/delivery/vehicles'));
                return;
            }

            // Get available drivers (include current driver if assigned)
            $query = "
                SELECT
                    u.id,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                LEFT JOIN vehicles v ON u.id = v.driver_id
                WHERE r.name = 'delivery'
                AND u.status = 'active'
                AND (v.id IS NULL OR v.id = ?)
                ORDER BY u.first_name, u.last_name
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([$vehicleId]);
            $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return view('admin/delivery/vehicles/edit', [
                'vehicle' => $vehicle,
                'drivers' => $drivers,
                'pageTitle' => 'Edit Vehicle'
            ]);

        } catch (Exception $e) {
            setFlash('error', 'Error loading vehicle: ' . $e->getMessage());
            redirect(url('admin/delivery/vehicles'));
        }
    }

    /**
     * Update Vehicle
     * POST /admin/delivery/vehicles/update
     */
    public function update() {
        if (!isPost()) {
            redirect(url('admin/delivery/vehicles'));
            return;
        }

        // Verify CSRF token
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token. Please try again.');
            back();
            return;
        }

        $vehicleId = (int) post('id', 0);

        if (!$vehicleId) {
            setFlash('error', 'Vehicle ID is required');
            back();
            return;
        }

        try {
            $this->db->beginTransaction();

            // Get current vehicle data
            $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicleId]);
            $currentVehicle = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentVehicle) {
                throw new Exception('Vehicle not found');
            }

            // Validate required field
            $vehicleType = post('vehicle_type');
            if (!$vehicleType) {
                throw new Exception('Vehicle type is required');
            }

            // Validate vehicle type enum
            $allowedTypes = ['bicycle', 'e-bike', 'scooter', 'motorcycle', 'car', 'van'];
            if (!in_array($vehicleType, $allowedTypes)) {
                throw new Exception('Invalid vehicle type');
            }

            // Get optional fields
            $make = sanitize(post('make', ''));
            $model = sanitize(post('model', ''));
            $year = post('year', null);
            $plateNumber = sanitize(post('plate_number', ''));
            $color = sanitize(post('color', ''));
            $insuranceExpiry = post('insurance_expiry', null);
            $newDriverId = post('driver_id', null);
            $notes = sanitize(post('notes', ''));
            $status = post('status', 'active');

            // Validate status enum
            $allowedStatuses = ['active', 'maintenance', 'retired'];
            if (!in_array($status, $allowedStatuses)) {
                $status = 'active';
            }

            // Check plate number uniqueness if provided and changed
            if ($plateNumber && $plateNumber !== $currentVehicle['plate_number']) {
                $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE plate_number = ? AND id != ?");
                $stmt->execute([$plateNumber, $vehicleId]);
                if ($stmt->fetch()) {
                    throw new Exception('A vehicle with this plate number already exists');
                }
            }

            $oldDriverId = $currentVehicle['driver_id'];

            // If driver changed, validate new driver
            if ($newDriverId && $newDriverId != $oldDriverId) {
                $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE driver_id = ? AND id != ?");
                $stmt->execute([$newDriverId, $vehicleId]);
                if ($stmt->fetch()) {
                    throw new Exception('This driver already has a vehicle assigned');
                }
            }

            // Update vehicle
            $stmt = $this->db->prepare("
                UPDATE vehicles
                SET driver_id = ?,
                    vehicle_type = ?,
                    make = ?,
                    model = ?,
                    year = ?,
                    plate_number = ?,
                    color = ?,
                    insurance_expiry = ?,
                    status = ?,
                    notes = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $newDriverId ?: null,
                $vehicleType,
                $make ?: null,
                $model ?: null,
                $year ?: null,
                $plateNumber ?: null,
                $color ?: null,
                $insuranceExpiry ?: null,
                $status,
                $notes ?: null,
                $vehicleId
            ]);

            // Handle driver reassignment in driver_availability
            if ($oldDriverId != $newDriverId) {
                // Remove vehicle from old driver if exists
                if ($oldDriverId) {
                    $stmt = $this->db->prepare("
                        UPDATE driver_availability
                        SET current_vehicle_id = NULL, updated_at = NOW()
                        WHERE driver_id = ? AND current_vehicle_id = ?
                    ");
                    $stmt->execute([$oldDriverId, $vehicleId]);
                }

                // Assign vehicle to new driver if exists
                if ($newDriverId) {
                    $stmt = $this->db->prepare("
                        UPDATE driver_availability
                        SET current_vehicle_id = ?, updated_at = NOW()
                        WHERE driver_id = ?
                    ");
                    $stmt->execute([$vehicleId, $newDriverId]);
                }
            }

            $this->db->commit();

            setFlash('success', 'Vehicle updated successfully');
            redirect(url('admin/delivery/vehicles/edit?id=' . $vehicleId));

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            setFlash('error', $e->getMessage());
            back();
        }
    }

    /**
     * View Vehicle Details with Delivery History
     * GET /admin/delivery/vehicles/view?id=X
     */
    public function view() {
        $vehicleId = (int) get('id', 0);

        if (!$vehicleId) {
            setFlash('error', 'Vehicle ID is required');
            redirect(url('admin/delivery/vehicles'));
            return;
        }

        try {
            // Get vehicle with driver info
            $stmt = $this->db->prepare("
                SELECT v.*,
                       u.id as driver_id,
                       u.first_name as driver_first_name,
                       u.last_name as driver_last_name,
                       u.email as driver_email,
                       u.phone as driver_phone,
                       da.status as driver_status,
                       da.active_deliveries
                FROM vehicles v
                LEFT JOIN users u ON v.driver_id = u.id
                LEFT JOIN driver_availability da ON u.id = da.driver_id
                WHERE v.id = ?
            ");
            $stmt->execute([$vehicleId]);
            $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vehicle) {
                setFlash('error', 'Vehicle not found');
                redirect(url('admin/delivery/vehicles'));
                return;
            }

            // Get delivery history for this vehicle's driver (last 20 deliveries)
            $deliveryHistory = [];
            if ($vehicle['driver_id']) {
                $stmt = $this->db->prepare("
                    SELECT
                        da.*,
                        COALESCE(o.order_number, dr.request_number) as order_number,
                        COALESCE(o.total, dr.total_amount) as order_total,
                        CASE
                            WHEN da.delivery_type = 'order' THEN CONCAT(cu.first_name, ' ', cu.last_name)
                            WHEN da.delivery_type = 'distribution' THEN bp.company_name
                            ELSE 'N/A'
                        END as customer_name,
                        COALESCE(s.name, 'N/A') as shop_name
                    FROM delivery_assignments da
                    LEFT JOIN orders o ON da.order_id = o.id AND da.delivery_type = 'order'
                    LEFT JOIN users cu ON o.user_id = cu.id
                    LEFT JOIN shops s ON da.shop_id = s.id
                    LEFT JOIN distribution_requests dr ON da.distribution_request_id = dr.id AND da.delivery_type = 'distribution'
                    LEFT JOIN business_profiles bp ON dr.business_profile_id = bp.id
                    WHERE da.driver_id = ?
                    ORDER BY da.created_at DESC
                    LIMIT 20
                ");
                $stmt->execute([$vehicle['driver_id']]);
                $deliveryHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            return view('admin/delivery/vehicles/view', [
                'vehicle' => $vehicle,
                'deliveryHistory' => $deliveryHistory,
                'pageTitle' => 'Vehicle Details'
            ]);

        } catch (Exception $e) {
            setFlash('error', 'Error loading vehicle details: ' . $e->getMessage());
            redirect(url('admin/delivery/vehicles'));
        }
    }

    /**
     * Assign or Unassign Driver to Vehicle (AJAX)
     * POST /admin/delivery/vehicles/assign-driver
     */
    public function assignDriver() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $vehicleId = (int) ($input['vehicle_id'] ?? 0);
            $driverId = (int) ($input['driver_id'] ?? 0);

            if (!$vehicleId) {
                throw new Exception('Vehicle ID is required');
            }

            $this->db->beginTransaction();

            // Check if vehicle exists
            $stmt = $this->db->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicleId]);
            $vehicle = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$vehicle) {
                throw new Exception('Vehicle not found');
            }

            $oldDriverId = $vehicle['driver_id'];

            // If unassigning (empty driver_id)
            if (!$driverId) {
                // Remove driver from vehicle
                $stmt = $this->db->prepare("
                    UPDATE vehicles
                    SET driver_id = NULL, updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$vehicleId]);

                // Update old driver's availability
                if ($oldDriverId) {
                    $stmt = $this->db->prepare("
                        UPDATE driver_availability
                        SET current_vehicle_id = NULL, updated_at = NOW()
                        WHERE driver_id = ? AND current_vehicle_id = ?
                    ");
                    $stmt->execute([$oldDriverId, $vehicleId]);
                }

                $this->db->commit();

                return jsonResponse([
                    'success' => true,
                    'message' => 'Driver unassigned from vehicle successfully'
                ]);
            }

            // Assigning a driver
            // Check if driver exists and has delivery role
            $stmt = $this->db->prepare("
                SELECT u.id FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND r.name = 'delivery' AND u.status = 'active'
            ");
            $stmt->execute([$driverId]);
            if (!$stmt->fetch()) {
                throw new Exception('Invalid driver or driver does not have delivery role');
            }

            // Check if driver already has another vehicle
            $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE driver_id = ? AND id != ?");
            $stmt->execute([$driverId, $vehicleId]);
            if ($stmt->fetch()) {
                throw new Exception('This driver already has a vehicle assigned');
            }

            // Assign driver to vehicle
            $stmt = $this->db->prepare("
                UPDATE vehicles
                SET driver_id = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$driverId, $vehicleId]);

            // Update old driver's availability if there was one
            if ($oldDriverId && $oldDriverId != $driverId) {
                $stmt = $this->db->prepare("
                    UPDATE driver_availability
                    SET current_vehicle_id = NULL, updated_at = NOW()
                    WHERE driver_id = ? AND current_vehicle_id = ?
                ");
                $stmt->execute([$oldDriverId, $vehicleId]);
            }

            // Update new driver's availability
            $stmt = $this->db->prepare("
                UPDATE driver_availability
                SET current_vehicle_id = ?, updated_at = NOW()
                WHERE driver_id = ?
            ");
            $stmt->execute([$vehicleId, $driverId]);

            $this->db->commit();

            return jsonResponse([
                'success' => true,
                'message' => 'Driver assigned to vehicle successfully'
            ]);

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Update Vehicle Status (AJAX)
     * POST /admin/delivery/vehicles/update-status
     */
    public function updateStatus() {
        if (!isPost()) {
            return jsonResponse(['error' => 'Invalid request'], 400);
        }

        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $vehicleId = (int) ($input['vehicle_id'] ?? 0);
            $status = $input['status'] ?? '';

            if (!$vehicleId) {
                throw new Exception('Vehicle ID is required');
            }

            // Validate status enum
            $allowedStatuses = ['active', 'maintenance', 'retired'];
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('Invalid status. Allowed values: active, maintenance, retired');
            }

            // Check if vehicle exists
            $stmt = $this->db->prepare("SELECT id FROM vehicles WHERE id = ?");
            $stmt->execute([$vehicleId]);
            if (!$stmt->fetch()) {
                throw new Exception('Vehicle not found');
            }

            // Update status
            $stmt = $this->db->prepare("
                UPDATE vehicles
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$status, $vehicleId]);

            return jsonResponse([
                'success' => true,
                'message' => 'Vehicle status updated to ' . $status . ' successfully'
            ]);

        } catch (Exception $e) {
            return jsonResponse(['error' => $e->getMessage()], 400);
        }
    }
}
