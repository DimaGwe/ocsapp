<?php

namespace App\Controllers\Api;

use PDO;
use Exception;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * DeliveryTrackingController
 * Handles real-time GPS location updates and retrieval for delivery tracking.
 */
class DeliveryTrackingController
{
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        $this->db = \Database::getConnection();
    }

    /**
     * POST /api/delivery/location
     * Receive GPS coordinates from a delivery driver.
     * Auth: delivery role required.
     */
    public function updateLocation()
    {
        if (!$this->isDriver()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $driverId = $_SESSION['user']['id'];
        $lat = (float)($input['latitude'] ?? 0);
        $lng = (float)($input['longitude'] ?? 0);
        $accuracy = isset($input['accuracy']) ? (float)$input['accuracy'] : null;
        $heading = isset($input['heading']) ? (int)$input['heading'] : null;
        $speed = isset($input['speed']) ? (float)$input['speed'] : null;
        $deliveryId = isset($input['delivery_id']) ? (int)$input['delivery_id'] : null;

        // Validate coordinates
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($lat == 0 && $lng == 0)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid coordinates']);
            return;
        }

        // Rate limit: check last update was at least 10s ago
        $stmt = $this->db->prepare("
            SELECT last_location_update FROM driver_availability WHERE driver_id = ?
        ");
        $stmt->execute([$driverId]);
        $lastUpdate = $stmt->fetchColumn();
        if ($lastUpdate && (time() - strtotime($lastUpdate)) < 10) {
            echo json_encode(['success' => true, 'throttled' => true, 'timestamp' => $lastUpdate]);
            return;
        }

        $now = date('Y-m-d H:i:s');

        try {
            $this->db->beginTransaction();

            // Update driver_availability
            $stmt = $this->db->prepare("
                UPDATE driver_availability
                SET current_latitude = ?, current_longitude = ?, last_location_update = ?
                WHERE driver_id = ?
            ");
            $stmt->execute([$lat, $lng, $now, $driverId]);

            // If delivery_id provided, verify driver owns it and it's active
            if ($deliveryId) {
                $stmt = $this->db->prepare("
                    UPDATE delivery_assignments
                    SET last_latitude = ?, last_longitude = ?, last_location_update = ?
                    WHERE id = ? AND driver_id = ? AND status IN ('accepted', 'picked_up', 'on_the_way')
                ");
                $stmt->execute([$lat, $lng, $now, $deliveryId, $driverId]);
            }

            // Insert into location log
            $stmt = $this->db->prepare("
                INSERT INTO driver_location_log (driver_id, delivery_id, latitude, longitude, accuracy, heading, speed)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$driverId, $deliveryId, $lat, $lng, $accuracy, $heading, $speed]);

            $this->db->commit();

            echo json_encode([
                'success' => true,
                'timestamp' => $now
            ]);

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('GPS update error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update location']);
        }
    }

    /**
     * GET /api/delivery/location?tracking_code=X
     * Get current location data for a delivery.
     * - tracking_code: public access (customer link) — no auth required
     * - delivery_id: requires admin or driver auth (prevents enumeration)
     */
    public function getLocation()
    {
        $deliveryId   = (int)($_GET['delivery_id'] ?? 0);
        $trackingCode = trim($_GET['tracking_code'] ?? '');

        if (!$deliveryId && !$trackingCode) {
            http_response_code(400);
            echo json_encode(['error' => 'tracking_code required']);
            return;
        }

        // Raw delivery_id access requires authentication to prevent enumeration
        if ($deliveryId && !$trackingCode) {
            $isAdmin  = isset($_SESSION['user']) && in_array(($_SESSION['user']['role'] ?? ''), ['admin', 'super_admin', 'admin_staff']);
            $isDriver = isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'delivery';
            if (!$isAdmin && !$isDriver) {
                http_response_code(403);
                echo json_encode(['error' => 'A tracking code is required to view delivery location']);
                return;
            }
        }

        // Build query based on identifier
        if ($trackingCode) {
            $stmt = $this->db->prepare("
                SELECT da.id, da.driver_id, da.shop_id, da.status,
                       da.last_latitude, da.last_longitude, da.last_location_update,
                       da.delivery_address, da.pickup_address,
                       s.name as shop_name, s.latitude as shop_lat, s.longitude as shop_lng, s.address as shop_address,
                       u.first_name as driver_first_name, u.last_name as driver_last_name
                FROM delivery_assignments da
                LEFT JOIN shops s ON da.shop_id = s.id
                LEFT JOIN users u ON da.driver_id = u.id
                WHERE da.tracking_code = ?
                LIMIT 1
            ");
            $stmt->execute([$trackingCode]);
        } else {
            $stmt = $this->db->prepare("
                SELECT da.id, da.driver_id, da.shop_id, da.status,
                       da.last_latitude, da.last_longitude, da.last_location_update,
                       da.delivery_address, da.pickup_address,
                       s.name as shop_name, s.latitude as shop_lat, s.longitude as shop_lng, s.address as shop_address,
                       u.first_name as driver_first_name, u.last_name as driver_last_name
                FROM delivery_assignments da
                LEFT JOIN shops s ON da.shop_id = s.id
                LEFT JOIN users u ON da.driver_id = u.id
                WHERE da.id = ?
                LIMIT 1
            ");
            $stmt->execute([$deliveryId]);
        }

        $delivery = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$delivery) {
            http_response_code(404);
            echo json_encode(['error' => 'Delivery not found']);
            return;
        }

        $result = [
            'status' => $delivery['status'],
            'driver' => null,
            'pickup' => null,
            'dropoff' => null
        ];

        // Driver location
        if ($delivery['last_latitude'] && $delivery['last_longitude']) {
            $result['driver'] = [
                'lat' => (float)$delivery['last_latitude'],
                'lng' => (float)$delivery['last_longitude'],
                'name' => trim(($delivery['driver_first_name'] ?? '') . ' ' . ($delivery['driver_last_name'] ?? '')),
                'lastUpdate' => $delivery['last_location_update']
            ];
        }

        // Pickup (shop) location
        if ($delivery['shop_lat'] && $delivery['shop_lng']) {
            $result['pickup'] = [
                'lat' => (float)$delivery['shop_lat'],
                'lng' => (float)$delivery['shop_lng'],
                'name' => $delivery['shop_name'] ?? 'Pickup',
                'address' => $delivery['shop_address'] ?? $delivery['pickup_address'] ?? ''
            ];
        }

        // Dropoff — try to geocode the delivery address if we don't have coords
        // For now, return the address text (frontend can use Nominatim if needed)
        $result['dropoff'] = [
            'address' => $delivery['delivery_address'] ?? ''
        ];

        echo json_encode($result);
    }

    /**
     * GET /api/admin/delivery/active-drivers
     * Get all active drivers with their current positions.
     * Auth: admin role required.
     */
    public function getActiveDrivers()
    {
        if (!$this->isAdmin()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        // Join driver_api_tokens so we catch online drivers even without GPS yet
        $stmt = $this->db->prepare("
            SELECT
                u.id as driver_id,
                u.first_name, u.last_name, u.phone,
                COALESCE(da_v.status, 'available') as availability_status,
                da_v.current_latitude, da_v.current_longitude,
                da_v.last_location_update,
                COALESCE(da_v.active_deliveries, 0) as active_deliveries,
                da_v.zone_id,
                dz.name as zone_name,
                (SELECT COUNT(*) FROM orders o2
                 WHERE o2.driver_id = u.id
                 AND o2.status = 'out_for_delivery') as current_delivery_count,
                (SELECT po.id FROM purchase_orders po
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_id,
                (SELECT po.po_number FROM purchase_orders po
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_number,
                (SELECT po.driver_acceptance_status FROM purchase_orders po
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_acceptance,
                (SELECT s.company_name FROM purchase_orders po
                 JOIN suppliers s ON s.id = po.supplier_id
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_supplier,
                (SELECT s.latitude FROM purchase_orders po
                 JOIN suppliers s ON s.id = po.supplier_id
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_supplier_lat,
                (SELECT s.longitude FROM purchase_orders po
                 JOIN suppliers s ON s.id = po.supplier_id
                 WHERE po.assigned_driver_id = u.id
                 AND po.status IN ('ready_for_pickup','picked_up')
                 ORDER BY po.driver_assigned_at DESC LIMIT 1) as active_po_supplier_lng
            FROM driver_api_tokens dat
            JOIN users u ON u.id = dat.user_id
            LEFT JOIN driver_availability da_v ON da_v.driver_id = u.id
            LEFT JOIN delivery_zones dz ON dz.id = da_v.zone_id
            WHERE dat.driver_online = 1
              AND (da_v.status IS NULL OR da_v.status != 'offline')
            ORDER BY da_v.last_location_update DESC
        ");
        $stmt->execute();
        $drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result = [];
        foreach ($drivers as $d) {
            $hasGps = !empty($d['current_latitude']) && !empty($d['current_longitude']);
            $result[] = [
                'driver_id'           => (int)$d['driver_id'],
                'name'                => trim($d['first_name'] . ' ' . $d['last_name']),
                'phone'               => $d['phone'],
                'lat'                 => $hasGps ? (float)$d['current_latitude']  : null,
                'lng'                 => $hasGps ? (float)$d['current_longitude'] : null,
                'status'              => $d['availability_status'],
                'zone'                => $d['zone_name'],
                'active_deliveries'   => (int)$d['current_delivery_count'],
                'active_po_id'          => $d['active_po_id'] ? (int)$d['active_po_id'] : null,
                'active_po_number'      => $d['active_po_number'],
                'active_po_acceptance'  => $d['active_po_acceptance'], // null | 'accepted'
                'active_po_supplier'    => $d['active_po_supplier'],
                'active_po_supplier_lat'=> $d['active_po_supplier_lat'] ? (float)$d['active_po_supplier_lat'] : null,
                'active_po_supplier_lng'=> $d['active_po_supplier_lng'] ? (float)$d['active_po_supplier_lng'] : null,
                'last_update'           => $d['last_location_update'],
            ];
        }

        echo json_encode(['drivers' => $result]);
    }

    /**
     * GET /admin/api/delivery/driver-route?driver_id=X
     * Returns breadcrumb trail + planned OSRM route for a driver.
     * Auth: admin role required.
     */
    public function driverRoute(): void
    {
        if (!$this->isAdmin()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $driverId = (int)($_GET['driver_id'] ?? 0);
        if (!$driverId) {
            echo json_encode(['error' => 'driver_id required']);
            return;
        }

        // -- Find active assignment start time to anchor breadcrumbs --
        // Check PO pickup first, then B2C order
        $anchorStmt = $this->db->prepare("
            SELECT LEAST(
                COALESCE((
                    SELECT driver_assigned_at FROM purchase_orders
                    WHERE assigned_driver_id = ? AND status IN ('ready_for_pickup','picked_up')
                    ORDER BY driver_assigned_at DESC LIMIT 1
                ), NOW()),
                COALESCE((
                    SELECT o.updated_at FROM orders o
                    WHERE o.driver_id = ? AND o.status = 'out_for_delivery'
                    ORDER BY o.updated_at DESC LIMIT 1
                ), NOW())
            ) AS anchor_time
        ");
        $anchorStmt->execute([$driverId, $driverId]);
        $anchor = $anchorStmt->fetchColumn();
        // Fall back to 24 hours if no active assignment found
        if (!$anchor || $anchor === date('Y-m-d H:i:s')) {
            $anchor = date('Y-m-d H:i:s', strtotime('-24 hours'));
        }

        // -- Breadcrumbs: pings from last 2 hours (avoids stale/stationary clutter) --
        $crumbStmt = $this->db->prepare("
            SELECT latitude, longitude, heading, speed, created_at
            FROM driver_location_log
            WHERE driver_id = ?
              AND created_at >= ?
              AND created_at > NOW() - INTERVAL 2 HOUR
            ORDER BY created_at ASC
            LIMIT 300
        ");
        $crumbStmt->execute([$driverId, $anchor]);
        $breadcrumbs = array_map(fn($r) => [
            'lat' => (float)$r['latitude'],
            'lng' => (float)$r['longitude'],
            'heading' => $r['heading'] ? (int)$r['heading'] : null,
            'speed'   => $r['speed']   ? (float)$r['speed']  : null,
            'ts'      => $r['created_at'],
        ], $crumbStmt->fetchAll(PDO::FETCH_ASSOC));

        // -- Active ODA order (out_for_delivery) --
        $orderStmt = $this->db->prepare("
            SELECT o.id, o.order_number, o.driver_status, o.delivery_address,
                   o.delivery_lat, o.delivery_lng,
                   u.first_name AS customer_first, u.last_name AS customer_last,
                   s.name AS shop_name, s.latitude AS shop_lat, s.longitude AS shop_lng,
                   s.address AS shop_address
            FROM orders o
            JOIN users u ON u.id = o.user_id
            JOIN shops s ON s.id = o.shop_id
            WHERE o.driver_id = ?
              AND o.status = 'out_for_delivery'
            ORDER BY o.updated_at DESC
            LIMIT 1
        ");
        $orderStmt->execute([$driverId]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        $merchant  = null;
        $customer  = null;
        $supplier  = null;
        $plannedPolyline = null;

        // ── Active B2C delivery ───────────────────────────────────────────────
        if ($order) {
            if (!empty($order['shop_lat']) && !empty($order['shop_lng'])) {
                $merchant = [
                    'name'    => $order['shop_name'],
                    'address' => $order['shop_address'],
                    'lat'     => (float)$order['shop_lat'],
                    'lng'     => (float)$order['shop_lng'],
                ];
            }
            if (!empty($order['delivery_lat']) && !empty($order['delivery_lng'])) {
                $customer = [
                    'name'    => trim($order['customer_first'] . ' ' . $order['customer_last']),
                    'address' => $order['delivery_address'],
                    'lat'     => (float)$order['delivery_lat'],
                    'lng'     => (float)$order['delivery_lng'],
                ];
            }
        }

        // ── Active PO pickup ─────────────────────────────────────────────────
        $poStmt = $this->db->prepare("
            SELECT po.id, po.po_number, po.status, po.driver_acceptance_status,
                   s.company_name, s.address, s.city, s.province, s.postal_code,
                   s.latitude AS supplier_lat, s.longitude AS supplier_lng
            FROM purchase_orders po
            JOIN suppliers s ON s.id = po.supplier_id
            WHERE po.assigned_driver_id = ?
              AND po.status IN ('ready_for_pickup','picked_up')
            ORDER BY po.driver_assigned_at DESC
            LIMIT 1
        ");
        $poStmt->execute([$driverId]);
        $po = $poStmt->fetch(\PDO::FETCH_ASSOC);

        $depot = null;
        if ($po && !empty($po['supplier_lat']) && !empty($po['supplier_lng'])) {
            $supplier = [
                'name'       => $po['company_name'],
                'address'    => implode(', ', array_filter([$po['address'], $po['city'], $po['province']])),
                'lat'        => (float)$po['supplier_lat'],
                'lng'        => (float)$po['supplier_lng'],
                'po_number'  => $po['po_number'],
                'po_status'  => $po['status'],
                'acceptance' => $po['driver_acceptance_status'],
            ];
        }

        // When driver has already collected goods, route them to the depot instead
        if ($po && $po['status'] === 'picked_up') {
            $depotRows     = $this->db->query(
                "SELECT `key`, value FROM settings WHERE `key` IN ('depot_lat','depot_lng','depot_address')"
            )->fetchAll(\PDO::FETCH_ASSOC);
            $depotSettings = array_column($depotRows, 'value', 'key');
            if (!empty($depotSettings['depot_lat']) && !empty($depotSettings['depot_lng'])) {
                $depot = [
                    'name'    => 'OCS Depot',
                    'address' => $depotSettings['depot_address'] ?? 'OCS Depot',
                    'lat'     => (float)$depotSettings['depot_lat'],
                    'lng'     => (float)$depotSettings['depot_lng'],
                ];
            }
        }

        // ── Planned route (Google Directions, OSRM fallback) ─────────────────
        // Determine origin (last breadcrumb) and destination
        $origin = null;
        $dest   = null;
        $waypointParam = '';

        if (!empty($breadcrumbs)) {
            $last   = end($breadcrumbs);
            $origin = $last['lat'] . ',' . $last['lng'];
        }

        if ($depot) {
            // Driver has picked up goods — route to depot
            $dest = $depot['lat'] . ',' . $depot['lng'];
        } elseif ($supplier) {
            // Driver heading to supplier for pickup
            $dest = $supplier['lat'] . ',' . $supplier['lng'];
        } elseif ($customer) {
            // B2C delivery: route to customer (via merchant if not yet picked up)
            $dest = $customer['lat'] . ',' . $customer['lng'];
            $driverStatus = $order['driver_status'] ?? '';
            $pickedUp = in_array($driverStatus, ['picked_up', 'en_route', 'arrived_customer']);
            if ($merchant && !$pickedUp) {
                $waypointParam = '&waypoints=' . $merchant['lat'] . ',' . $merchant['lng'];
            }
        }

        if ($origin && $dest) {
            $googleKey = env('GOOGLE_DIRECTIONS_KEY', '');
            if ($googleKey) {
                $dirUrl = "https://maps.googleapis.com/maps/api/directions/json"
                        . "?origin={$origin}&destination={$dest}{$waypointParam}"
                        . "&mode=driving&key={$googleKey}";
                $ch = curl_init($dirUrl);
                curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5]);
                $resp = curl_exec($ch);
                curl_close($ch);
                $dirData = json_decode($resp ?: '', true);
                if (!empty($dirData['routes'][0]['overview_polyline']['points'])) {
                    $plannedPolyline = $dirData['routes'][0]['overview_polyline']['points'];
                }
            }

            // OSRM fallback if Google failed or key not set
            if (!$plannedPolyline) {
                [$oLat, $oLng] = explode(',', $origin);
                [$dLat, $dLng] = explode(',', $dest);
                $osrmUrl = "https://router.project-osrm.org/route/v1/driving/{$oLng},{$oLat};{$dLng},{$dLat}?overview=full&geometries=polyline";
                $ch = curl_init($osrmUrl);
                curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 5, CURLOPT_USERAGENT => 'OCSApp/1.0']);
                $resp = curl_exec($ch);
                curl_close($ch);
                $osrmData = json_decode($resp ?: '', true);
                if (!empty($osrmData['routes'][0]['geometry'])) {
                    $plannedPolyline = $osrmData['routes'][0]['geometry'];
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode([
            'breadcrumbs'     => $breadcrumbs,
            'merchant'        => $merchant,
            'customer'        => $customer,
            'supplier'        => $supplier,
            'depot'           => $depot,
            'google_polyline' => $plannedPolyline,
            'order'           => $order ? [
                'id'          => (int)$order['id'],
                'order_number'=> $order['order_number'],
                'status'      => $order['driver_status'],
            ] : null,
        ]);
    }

    /**
     * GET /admin/api/delivery/oda-live
     * Returns orders currently being tracked through the ODA driver app.
     * Auth: admin role required.
     */
    public function odaLive(): void
    {
        if (!$this->isAdmin()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stmt = $this->db->prepare("
            SELECT
                o.id, o.order_number, o.total, o.driver_payout,
                o.driver_status, o.status, o.accepted_at, o.picked_up_at,
                o.delivery_address, o.updated_at,
                s.name AS shop_name, s.address AS shop_address,
                u.first_name AS customer_first, u.last_name AS customer_last,
                u.phone AS customer_phone,
                d.first_name AS driver_first, d.last_name AS driver_last,
                d.phone AS driver_phone, d.id AS driver_id,
                da_av.current_latitude AS driver_lat,
                da_av.current_longitude AS driver_lng,
                da_av.last_location_update AS driver_last_gps
            FROM orders o
            JOIN shops s ON s.id = o.shop_id
            JOIN users u ON u.id = o.user_id
            JOIN users d ON d.id = o.driver_id
            LEFT JOIN driver_availability da_av ON da_av.driver_id = o.driver_id
            WHERE o.status = 'out_for_delivery'
              AND o.driver_status IS NOT NULL
              AND o.driver_status != 'delivered'
            ORDER BY o.updated_at DESC
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stepOrder = [
            'accepted'           => 0,
            'heading_to_merchant'=> 1,
            'arrived_merchant'   => 2,
            'picked_up'          => 3,
            'en_route'           => 4,
            'arrived_customer'   => 5,
            'delivered'          => 6,
        ];

        $orders = [];
        foreach ($rows as $r) {
            $stepIndex = $stepOrder[$r['driver_status']] ?? 0;
            $orders[] = [
                'id'              => (int)$r['id'],
                'order_number'    => $r['order_number'],
                'total'           => (float)$r['total'],
                'driver_payout'   => (float)$r['driver_payout'],
                'driver_status'   => $r['driver_status'],
                'step_index'      => $stepIndex,
                'step_total'      => 6,
                'shop_name'       => $r['shop_name'],
                'shop_address'    => $r['shop_address'],
                'delivery_address'=> $r['delivery_address'],
                'accepted_at'     => $r['accepted_at'],
                'picked_up_at'    => $r['picked_up_at'],
                'last_update'     => $r['updated_at'],
                'driver' => [
                    'id'    => (int)$r['driver_id'],
                    'name'  => trim($r['driver_first'] . ' ' . $r['driver_last']),
                    'phone' => $r['driver_phone'],
                    'lat'   => $r['driver_lat'] ? (float)$r['driver_lat'] : null,
                    'lng'   => $r['driver_lng'] ? (float)$r['driver_lng'] : null,
                    'last_gps' => $r['driver_last_gps'],
                ],
                'customer' => [
                    'name'    => trim($r['customer_first'] . ' ' . $r['customer_last']),
                    'phone'   => $r['customer_phone'],
                    'address' => $r['delivery_address'],
                ],
            ];
        }

        echo json_encode(['orders' => $orders, 'timestamp' => date('Y-m-d H:i:s')]);
    }

    private function isDriver(): bool
    {
        return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'delivery';
    }

    private function isAdmin(): bool
    {
        return isset($_SESSION['user']['role']) && \AdminPermissionHelper::isAdminRole($_SESSION['user']['role']);
    }
}
