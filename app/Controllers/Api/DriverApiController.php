<?php

namespace App\Controllers\Api;


class DriverApiController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    // -------------------------------------------------------------------------
    // POST /api/auth/login
    // -------------------------------------------------------------------------
    public function login(): void
    {
        $body = $this->jsonBody();
        $email    = trim($body['email'] ?? '');
        $password = $body['password'] ?? '';

        if (!$email || !$password) {
            $this->error('Email and password required', 400);
        }

        $stmt = $this->db->prepare(
            "SELECT id, first_name, last_name, email, phone, password, role, status
             FROM users
             WHERE email = ?
             LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->error('Invalid email or password', 401);
        }

        // Only drivers can use this app
        if (!in_array($user['role'], ['delivery', 'driver', 'admin', 'super_admin'])) {
            $this->error('This app is for drivers only. Please use the main website.', 403);
        }

        // Driver accounts must be active (approved by admin)
        if (in_array($user['role'], ['delivery', 'driver']) && $user['status'] !== 'active') {
            $this->error('Your application is pending approval. You will receive an email once your account is activated.', 403);
        }

        $token = $this->generateToken($user['id']);

        $this->json([
            'token'  => $token,
            'driver' => $this->driverProfile($user['id']),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/auth/logout
    // -------------------------------------------------------------------------
    public function logout(): void
    {
        $userId = $this->authenticate();
        // Invalidate token
        $this->db->prepare("DELETE FROM driver_api_tokens WHERE user_id = ?")
                 ->execute([$userId]);
        $this->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // GET /api/driver/profile
    // -------------------------------------------------------------------------
    public function profile(): void
    {
        $userId = $this->authenticate();
        $this->json(['driver' => $this->driverProfile($userId)]);
    }

    // -------------------------------------------------------------------------
    // GET /api/driver/compliance-docs
    // Returns the 5 compliance doc statuses for the authenticated driver
    // -------------------------------------------------------------------------
    public function complianceDocs(): void
    {
        $userId = $this->authenticate();

        $docTypes = [
            'class5_license'       => "Class 5 Driver's License",
            'saaq_record'          => 'SAAQ Driving Record',
            'commercial_insurance' => 'Commercial Insurance',
            'vehicle_registration' => 'Vehicle Registration',
            'work_authorization'   => 'Work Authorization',
        ];

        // Get latest application ID for this driver
        $appStmt = $this->db->prepare(
            "SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1"
        );
        $appStmt->execute([$userId]);
        $appId = $appStmt->fetchColumn();

        $rows = [];
        if ($appId) {
            $cdStmt = $this->db->prepare(
                "SELECT doc_type, status, admin_notes, uploaded_at, verified_at
                 FROM driver_compliance_docs WHERE application_id = ?"
            );
            $cdStmt->execute([$appId]);
            foreach ($cdStmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
                $rows[$r['doc_type']] = $r;
            }
        }

        $docs = [];
        foreach ($docTypes as $t => $label) {
            $r = $rows[$t] ?? null;
            $docs[] = [
                'doc_type'    => $t,
                'label'       => $label,
                'status'      => $r ? $r['status'] : 'not_uploaded',
                'admin_notes' => ($r && $r['status'] === 'flagged') ? ($r['admin_notes'] ?? null) : null,
                'uploaded_at' => $r ? $r['uploaded_at'] : null,
                'verified_at' => $r ? $r['verified_at'] : null,
            ];
        }

        $this->json(['docs' => $docs]);
    }

    // -------------------------------------------------------------------------
    // POST /api/driver/status
    // -------------------------------------------------------------------------
    public function updateStatus(): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $status = $body['status'] ?? 'offline';

        if (!in_array($status, ['online', 'offline'])) {
            $this->error('Invalid status', 400);
        }

        if ($status === 'online') {
            // Background check must be verified before going online
            $bg = $this->db->prepare(
                "SELECT id FROM driver_applications
                 WHERE user_id = ? AND bgcheck_status IN ('verified','waived')
                 LIMIT 1"
            );
            $bg->execute([$userId]);
            if (!$bg->fetch()) {
                $this->error('Background check must be verified before going online.', 403);
            }
        }

        // Update lat/lng if provided
        $lat = $body['lat'] ?? null;
        $lng = $body['lng'] ?? null;

        $this->db->prepare(
            "UPDATE driver_api_tokens SET driver_online = ?, updated_at = NOW()
             WHERE user_id = ?"
        )->execute([$status === 'online' ? 1 : 0, $userId]);

        // Sync driver_availability so the live map knows about this driver
        if ($status === 'online') {
            $this->db->prepare(
                "INSERT INTO driver_availability (driver_id, status, max_deliveries, last_location_update)
                 VALUES (?, 'available', 3, NOW())
                 ON DUPLICATE KEY UPDATE status = 'available', last_location_update = NOW()"
            )->execute([$userId]);

            // Retry any distribution requests that were ready but had no driver available.
            // Find DRs that are processing, have all POs ready_for_pickup, but no active assignment.
            $unassigned = $this->db->query("
                SELECT dr.id
                FROM distribution_requests dr
                WHERE dr.status = 'processing'
                  AND NOT EXISTS (
                      SELECT 1 FROM delivery_assignments da
                      WHERE da.distribution_request_id = dr.id
                        AND da.delivery_type = 'distribution'
                        AND da.status NOT IN ('cancelled', 'failed')
                  )
                  AND EXISTS (
                      SELECT 1 FROM purchase_orders po
                      WHERE po.distribution_request_id = dr.id
                        AND po.status = 'ready_for_pickup'
                  )
                  AND NOT EXISTS (
                      SELECT 1 FROM purchase_orders po2
                      WHERE po2.distribution_request_id = dr.id
                        AND po2.status IN ('sent', 'accepted', 'preparing')
                  )
            ")->fetchAll(\PDO::FETCH_COLUMN);

            foreach ($unassigned as $drId) {
                \App\Controllers\AdminDistributionController::autoAssignDistributionDriver((int)$drId, $this->db);
            }
        } else {
            $this->db->prepare(
                "UPDATE driver_availability SET status = 'offline' WHERE driver_id = ?"
            )->execute([$userId]);
        }

        $this->json(['success' => true, 'status' => $status]);
    }

    // -------------------------------------------------------------------------
    // GET /api/orders/available
    // -------------------------------------------------------------------------
    public function availableOrders(): void
    {
        $userId = $this->authenticate();

        // Get driver's zone from their application record
        $driver = $this->db->prepare(
            "SELECT city FROM driver_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1"
        );
        $driver->execute([$userId]);
        $driverRow = $driver->fetch(\PDO::FETCH_ASSOC);
        $zone = $driverRow['city'] ?? null;

        // Active order for this driver (out_for_delivery = driver has been assigned and is en route)
        $activeStmt = $this->db->prepare(
            "SELECT o.*, s.name AS merchant_name, s.address AS merchant_address,
                    s.latitude AS merchant_lat, s.longitude AS merchant_lng
             FROM orders o
             JOIN shops s ON s.id = o.shop_id
             WHERE o.driver_id = ? AND o.status = 'out_for_delivery'
             ORDER BY o.updated_at DESC LIMIT 1"
        );
        $activeStmt->execute([$userId]);
        $active = $activeStmt->fetch(\PDO::FETCH_ASSOC);

        // Orders that are ready for pickup and unassigned (or pre-assigned to this driver)
        $zoneClause = $zone ? "AND o.delivery_zone = :zone" : "";
        $pending = $this->db->prepare(
            "SELECT o.*, s.name AS merchant_name, s.address AS merchant_address,
                    s.latitude AS merchant_lat, s.longitude AS merchant_lng,
                    30 AS accept_deadline_seconds
             FROM orders o
             JOIN shops s ON s.id = o.shop_id
             WHERE o.status = 'ready' AND (o.driver_id IS NULL OR o.driver_id = :uid) $zoneClause
             ORDER BY o.created_at ASC
             LIMIT 20"
        );
        $params = ['uid' => $userId];
        if ($zone) $params['zone'] = $zone;
        $pending->execute($params);
        $pendingOrders = $pending->fetchAll(\PDO::FETCH_ASSOC);

        $orders = [];
        if ($active) $orders[] = $this->formatOrder($active);
        foreach ($pendingOrders as $o) $orders[] = $this->formatOrder($o);

        $this->json(['orders' => $orders]);
    }

    // -------------------------------------------------------------------------
    // GET /api/orders/:id
    // -------------------------------------------------------------------------
    public function getOrder(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare(
            "SELECT o.*, s.name AS merchant_name, s.address AS merchant_address,
                    s.latitude AS merchant_lat, s.longitude AS merchant_lng,
                    CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,
                    CONCAT(du.first_name, ' ', du.last_name) AS driver_name,
                    da.license_number AS driver_license
             FROM orders o
             JOIN shops s ON s.id = o.shop_id
             LEFT JOIN users cu ON cu.id = o.user_id
             LEFT JOIN users du ON du.id = o.driver_id
             LEFT JOIN driver_applications da ON da.user_id = o.driver_id AND da.status = 'approved'
             WHERE o.id = ? AND (o.driver_id = ? OR o.status = 'ready')
             LIMIT 1"
        );
        $stmt->execute([$id, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) $this->error('Order not found', 404);

        // Load order items for checklist display
        $itemsStmt = $this->db->prepare(
            "SELECT oi.id, oi.quantity, oi.unit_price, oi.total_price,
                    p.name AS product_name
             FROM order_items oi
             JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?
             ORDER BY p.name ASC"
        );
        $itemsStmt->execute([$id]);
        $rawItems = $itemsStmt->fetchAll(\PDO::FETCH_ASSOC);

        $items = array_map(fn($i) => [
            'id'           => (int)$i['id'],
            'product_name' => $i['product_name'],
            'quantity'     => (int)$i['quantity'],
            'unit_price'   => (float)$i['unit_price'],
        ], $rawItems);

        $formatted = $this->formatOrder($order);
        $formatted['items'] = $items;

        $this->json(['order' => $formatted]);
    }

    // -------------------------------------------------------------------------
    // GET /api/driver/notifications?order_id=X  OR  ?po_id=X
    // Returns unread admin notifications for this driver's active delivery or pickup
    // -------------------------------------------------------------------------
    public function notifications(): void
    {
        $userId  = $this->authenticate();
        $orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
        $poId    = isset($_GET['po_id'])    ? (int)$_GET['po_id']    : 0;

        if (!$orderId && !$poId) $this->error('order_id or po_id required', 400);

        if ($poId) {
            $stmt = $this->db->prepare(
                "SELECT id, message, type, created_at
                 FROM driver_delivery_notifications
                 WHERE driver_id = ? AND po_id = ? AND read_at IS NULL
                 ORDER BY created_at ASC"
            );
            $stmt->execute([$userId, $poId]);
        } else {
            $stmt = $this->db->prepare(
                "SELECT id, message, type, created_at
                 FROM driver_delivery_notifications
                 WHERE driver_id = ? AND order_id = ? AND read_at IS NULL
                 ORDER BY created_at ASC"
            );
            $stmt->execute([$userId, $orderId]);
        }

        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $this->json(['notifications' => $rows]);
    }

    // -------------------------------------------------------------------------
    // POST /api/driver/notifications/:id/read
    // -------------------------------------------------------------------------
    public function markNotificationRead(int $id): void
    {
        $userId = $this->authenticate();

        $this->db->prepare(
            "UPDATE driver_delivery_notifications
             SET read_at = NOW()
             WHERE id = ? AND driver_id = ?"
        )->execute([$id, $userId]);

        $this->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // POST /api/orders/:id/accept
    // -------------------------------------------------------------------------
    public function acceptOrder(int $id): void
    {
        $userId = $this->authenticate();

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "SELECT id, delivery_fee, distance_km, driver_payout FROM orders
                 WHERE id = ? AND status = 'ready'
                 AND (driver_id IS NULL OR driver_id = ?) FOR UPDATE"
            );
            $stmt->execute([$id, $userId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$order) {
                $this->db->rollBack();
                $this->error('Order no longer available', 409);
            }

            // Calculate payout if not already set by admin
            $payout = (float)($order['driver_payout'] ?? 0);
            if ($payout <= 0) {
                $payout = $this->calculatePayout(
                    (float)($order['delivery_fee'] ?? 0),
                    (float)($order['distance_km'] ?? 0)
                );
            }

            $this->db->prepare(
                "UPDATE orders SET driver_id = ?, status = 'out_for_delivery',
                 driver_status = 'accepted', driver_payout = ?,
                 accepted_at = NOW(), updated_at = NOW() WHERE id = ?"
            )->execute([$userId, $payout, $id]);

            // Sync delivery_assignments so admin dashboard reflects accepted status
            $this->db->prepare(
                "UPDATE delivery_assignments
                 SET status = 'accepted', driver_id = ?, accepted_at = NOW(), updated_at = NOW()
                 WHERE order_id = ? AND status IN ('pending','assigned')"
            )->execute([$userId, $id]);

            $this->db->commit();

            // Notify admin + seller + buyer
            try {
                $orderStmt = $this->db->prepare(
                    "SELECT o.order_number, o.user_id,
                            d.first_name AS driver_first, d.last_name AS driver_last,
                            s.user_id AS shop_user_id,
                            u.first_name AS customer_first, u.email AS customer_email
                     FROM orders o
                     JOIN users d ON d.id = ?
                     JOIN shops s ON s.id = o.shop_id
                     JOIN users u ON u.id = o.user_id
                     WHERE o.id = ? LIMIT 1"
                );
                $orderStmt->execute([$userId, $id]);
                $info = $orderStmt->fetch(\PDO::FETCH_ASSOC);
                if ($info) {
                    $driverName = trim($info['driver_first'] . ' ' . $info['driver_last']);

                    // Admin
                    require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
                    \App\Helpers\NotificationHelper::add(
                        'delivery',
                        '🚗 Driver Accepted Order #' . $info['order_number'],
                        "{$driverName} accepted the order and is heading to the merchant.",
                        ['link' => '/admin/delivery/active', 'icon' => 'truck', 'priority' => 'normal']
                    );

                    // Seller in-app
                    if (!empty($info['shop_user_id'])) {
                        try {
                            $this->db->prepare(
                                "INSERT INTO user_notifications
                                 (user_id, type, title, message, link, is_read, created_at)
                                 VALUES (?, 'order', ?, ?, '/seller/orders', 0, NOW())"
                            )->execute([
                                $info['shop_user_id'],
                                '🚗 Driver On the Way — #' . $info['order_number'],
                                "A driver has accepted order #{$info['order_number']} and is heading to pick it up.",
                            ]);
                        } catch (\Exception $e) { /* non-blocking */ }
                    }

                    // Buyer email
                    if (!empty($info['customer_email'])) {
                        require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                        \App\Helpers\EmailHelper::sendOrderStatusUpdate(
                            ['id' => $id, 'order_number' => $info['order_number'], 'user_id' => $info['user_id']],
                            'processing',
                            'out_for_delivery'
                        );
                    }
                }
            } catch (\Exception $e) { /* non-blocking */ }

            // Log acceptance (marketplace order)
            $orderRef = $this->db->prepare("SELECT order_number FROM orders WHERE id = ? LIMIT 1");
            $orderRef->execute([$id]);
            $orderData = $orderRef->fetch(\PDO::FETCH_ASSOC);
            $this->logDriverActivity(
                $userId, 'accepted', 'marketplace',
                $id, null, null,
                $orderData['order_number'] ?? ''
            );

            $this->json(['success' => true, 'payout' => $payout]);
        } catch (\Exception $e) {
            $this->db->rollBack();
            $this->error('Could not accept order', 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/orders/:id/status
    // -------------------------------------------------------------------------
    public function updateOrderStatus(int $id): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $status = $body['status'] ?? '';

        $allowed = ['heading_to_merchant','arrived_merchant','picked_up',
                    'en_route','arrived_customer'];
        if (!in_array($status, $allowed)) {
            $this->error('Invalid status', 400);
        }

        $stmt = $this->db->prepare(
            "SELECT o.id, o.order_number, s.id AS shop_id, s.name AS shop_name
             FROM orders o
             JOIN shops s ON s.id = o.shop_id
             WHERE o.id = ? AND o.driver_id = ? AND o.status = 'out_for_delivery'
             LIMIT 1"
        );
        $stmt->execute([$id, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) $this->error('Order not found', 404);

        // ODA driver_status → delivery_assignments.status mapping
        $daStatusMap = [
            'heading_to_merchant' => 'accepted',
            'arrived_merchant'    => 'accepted',
            'picked_up'           => 'picked_up',
            'en_route'            => 'on_the_way',
            'arrived_customer'    => 'on_the_way',
            'delivered'           => 'delivered',
        ];
        $daStatus = $daStatusMap[$status] ?? null;

        // Update orders table
        if ($status === 'delivered') {
            $this->db->prepare(
                "UPDATE orders SET driver_status = 'delivered', status = 'delivered',
                 delivered_at = NOW(), updated_at = NOW() WHERE id = ?"
            )->execute([$id]);
            $this->onDeliveryCompleted($id, $userId);

        } elseif ($status === 'picked_up') {
            $this->db->prepare(
                "UPDATE orders SET driver_status = ?, picked_up_at = NOW(), updated_at = NOW() WHERE id = ?"
            )->execute([$status, $id]);
        } else {
            $this->db->prepare(
                "UPDATE orders SET driver_status = ?, updated_at = NOW() WHERE id = ?"
            )->execute([$status, $id]);
        }

        // Sync delivery_assignments so admin dashboard stays current
        if ($daStatus && $status !== 'delivered') {
            $pickupField = $status === 'picked_up' ? ', picked_up_at = NOW()' : '';
            $onWayField  = $status === 'en_route'  ? ', on_the_way_at = NOW()' : '';
            $this->db->prepare(
                "UPDATE delivery_assignments
                 SET status = ?, updated_at = NOW() $pickupField $onWayField
                 WHERE order_id = ? AND driver_id = ?"
            )->execute([$daStatus, $id, $userId]);
        }

        // ── Notifications for key steps ──────────────────────────────────────
        try {
            require_once __DIR__ . '/../../Helpers/NotificationHelper.php';

            if ($status === 'picked_up') {
                // Admin
                \App\Helpers\NotificationHelper::add(
                    'delivery',
                    '📦 Order Picked Up — #' . $order['order_number'],
                    "Driver picked up order #{$order['order_number']} from {$order['shop_name']} and is en route.",
                    ['link' => '/admin/delivery/active', 'icon' => 'box', 'priority' => 'normal']
                );

                // Seller (shop owner) — notify via seller notifications table if exists
                $sellerStmt = $this->db->prepare(
                    "SELECT user_id FROM shops WHERE id = ? LIMIT 1"
                );
                $sellerStmt->execute([$order['shop_id']]);
                $sellerId = $sellerStmt->fetchColumn();
                if ($sellerId) {
                    try {
                        $this->db->prepare(
                            "INSERT INTO user_notifications
                             (user_id, type, title, message, link, is_read, created_at)
                             VALUES (?, 'order', ?, ?, ?, 0, NOW())"
                        )->execute([
                            $sellerId,
                            '📦 Order Picked Up — #' . $order['order_number'],
                            "Your order #{$order['order_number']} has been picked up by the driver.",
                            '/seller/orders',
                        ]);
                    } catch (\Exception $e) { /* table may not exist */ }
                }

            } elseif ($status === 'en_route') {
                \App\Helpers\NotificationHelper::add(
                    'delivery',
                    '🚗 Driver En Route — #' . $order['order_number'],
                    "Driver is on the way to the customer for order #{$order['order_number']}.",
                    ['link' => '/admin/delivery/active', 'icon' => 'truck', 'priority' => 'low']
                );

                // Buyer out-for-delivery email — load full order + driver details
                try {
                    $fullStmt = $this->db->prepare(
                        "SELECT o.*,
                                u.first_name  AS customer_first_name,
                                u.last_name   AS customer_last_name,
                                u.email       AS customer_email,
                                d.first_name  AS driver_first_name,
                                d.last_name   AS driver_last_name,
                                d.phone       AS driver_phone,
                                CONCAT_WS(' ', o.delivery_address, o.delivery_city, o.delivery_province, o.delivery_postal_code) AS delivery_address
                         FROM orders o
                         JOIN users u ON u.id = o.user_id
                         JOIN users d ON d.id = o.driver_id
                         WHERE o.id = ? LIMIT 1"
                    );
                    $fullStmt->execute([$id]);
                    $fullOrder = $fullStmt->fetch(\PDO::FETCH_ASSOC);

                    if ($fullOrder && !empty($fullOrder['customer_email'])) {
                        // Build items summary
                        $itemsStmt = $this->db->prepare(
                            "SELECT oi.quantity, p.name AS product_name
                             FROM order_items oi
                             JOIN products p ON p.id = oi.product_id
                             WHERE oi.order_id = ?"
                        );
                        $itemsStmt->execute([$id]);
                        $itemLines = array_map(
                            fn($i) => "{$i['quantity']}x {$i['product_name']}",
                            $itemsStmt->fetchAll(\PDO::FETCH_ASSOC)
                        );
                        $fullOrder['items_summary'] = implode("\n", $itemLines);

                        require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                        \App\Helpers\EmailHelper::sendBuyerOutForDelivery(
                            $fullOrder,
                            [
                                'name'  => trim($fullOrder['driver_first_name'] . ' ' . $fullOrder['driver_last_name']),
                                'phone' => $fullOrder['driver_phone'] ?? 'Contact support',
                            ]
                        );
                    }
                } catch (\Exception $e) { /* non-blocking */ }
            }
        } catch (\Exception $e) { /* non-blocking */ }

        $this->json(['success' => true, 'status' => $status]);
    }

    // -------------------------------------------------------------------------
    // POST /api/driver/location
    // Body: { lat, lng, accuracy?, heading?, speed?, order_id? }
    // -------------------------------------------------------------------------
    public function updateLocation(): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();

        $lat      = (float)($body['lat'] ?? 0);
        $lng      = (float)($body['lng'] ?? 0);
        $accuracy = isset($body['accuracy']) ? (float)$body['accuracy'] : null;
        $heading  = isset($body['heading'])  ? (int)$body['heading']   : null;
        $speed    = isset($body['speed'])    ? (float)$body['speed']   : null;
        $orderId  = isset($body['order_id']) ? (int)$body['order_id']  : null;

        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($lat == 0 && $lng == 0)) {
            $this->error('Invalid coordinates', 400);
        }

        // Rate-limit: ignore if last update was < 10s ago
        $lastStmt = $this->db->prepare(
            "SELECT last_location_update FROM driver_availability WHERE driver_id = ?"
        );
        $lastStmt->execute([$userId]);
        $lastUpdate = $lastStmt->fetchColumn();
        if ($lastUpdate && (time() - strtotime($lastUpdate)) < 10) {
            $this->json(['success' => true, 'throttled' => true]);
        }

        $now = date('Y-m-d H:i:s');

        // Determine busy/available based on active deliveries
        $busyCheck = $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE driver_id = ? AND status = 'out_for_delivery'"
        );
        $busyCheck->execute([$userId]);
        $availStatus = $busyCheck->fetchColumn() > 0 ? 'busy' : 'available';

        // Upsert driver_availability location
        $this->db->prepare(
            "INSERT INTO driver_availability (driver_id, current_latitude, current_longitude, last_location_update, status, max_deliveries)
             VALUES (?, ?, ?, ?, ?, 3)
             ON DUPLICATE KEY UPDATE current_latitude = VALUES(current_latitude),
                                     current_longitude = VALUES(current_longitude),
                                     last_location_update = VALUES(last_location_update),
                                     status = VALUES(status)"
        )->execute([$userId, $lat, $lng, $now, $availStatus]);

        // Log to driver_location_log
        $this->db->prepare(
            "INSERT INTO driver_location_log (driver_id, delivery_id, latitude, longitude, accuracy, heading, speed)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        )->execute([$userId, $orderId, $lat, $lng, $accuracy, $heading, $speed]);

        // If order given, also update delivery_assignment last location
        if ($orderId) {
            $this->db->prepare(
                "UPDATE delivery_assignments
                 SET last_latitude = ?, last_longitude = ?, last_location_update = ?
                 WHERE order_id = ? AND driver_id = ?
                   AND status IN ('assigned','accepted','picked_up','on_the_way')"
            )->execute([$lat, $lng, $now, $orderId, $userId]);
        }

        $this->json(['success' => true, 'timestamp' => $now]);
    }

    // -------------------------------------------------------------------------
    // GET /api/driver/earnings
    // -------------------------------------------------------------------------
    public function earnings(): void
    {
        $userId = $this->authenticate();

        // All earnings come from delivery_earnings — the single source of truth
        // shared with the admin earnings page. net_earning = what driver actually receives.
        $stats = $this->db->prepare("
            SELECT
                COALESCE(SUM(CASE WHEN DATE(de.created_at) = CURDATE() THEN de.net_earning ELSE 0 END), 0)                                       AS today,
                COALESCE(SUM(CASE WHEN YEARWEEK(de.created_at,1) = YEARWEEK(NOW(),1) THEN de.net_earning ELSE 0 END), 0)                         AS week,
                COALESCE(SUM(CASE WHEN YEAR(de.created_at) = YEAR(NOW()) AND MONTH(de.created_at) = MONTH(NOW()) THEN de.net_earning ELSE 0 END), 0) AS month,
                COUNT(CASE WHEN DATE(de.created_at) = CURDATE() THEN 1 END)                                                                      AS cnt_today,
                COUNT(CASE WHEN YEARWEEK(de.created_at,1) = YEARWEEK(NOW(),1) THEN 1 END)                                                        AS cnt_week,
                COUNT(CASE WHEN YEAR(de.created_at) = YEAR(NOW()) AND MONTH(de.created_at) = MONTH(NOW()) THEN 1 END)                            AS cnt_month
            FROM delivery_earnings de
            WHERE de.driver_id = ? AND de.payment_status != 'cancelled'
        ");
        $stats->execute([$userId]);
        $s = $stats->fetch(\PDO::FETCH_ASSOC);

        // History: join to orders (shop deliveries) or distribution_requests (distribution runs)
        $history = $this->db->prepare("
            SELECT
                de.net_earning                                                        AS payout,
                de.total_earning,
                de.platform_commission,
                de.tip,
                de.payment_status,
                de.created_at                                                         AS date,
                COALESCE(sh.name, bp.company_name, 'Delivery')                       AS merchant,
                CASE WHEN da.delivery_type = 'distribution' THEN 1 ELSE 0 END        AS is_distribution,
                dr.request_number
            FROM delivery_earnings de
            LEFT JOIN delivery_assignments da ON da.id = de.delivery_id
            LEFT JOIN orders o ON o.id = de.order_id
            LEFT JOIN shops sh ON sh.id = o.shop_id
            LEFT JOIN distribution_requests dr ON dr.id = da.distribution_request_id
            LEFT JOIN business_profiles bp ON bp.id = dr.business_profile_id
            WHERE de.driver_id = ? AND de.payment_status != 'cancelled'
            ORDER BY de.created_at DESC
            LIMIT 50
        ");
        $history->execute([$userId]);

        $this->json([
            'today'            => (float)$s['today'],
            'week'             => (float)$s['week'],
            'month'            => (float)$s['month'],
            'deliveries_today' => (int)$s['cnt_today'],
            'deliveries_week'  => (int)$s['cnt_week'],
            'deliveries_month' => (int)$s['cnt_month'],
            'history'          => $history->fetchAll(\PDO::FETCH_ASSOC),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/driver/photo  (multipart/form-data, field: photo)
    // -------------------------------------------------------------------------
    public function updatePhoto(): void
    {
        $userId = $this->authenticate();

        if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $this->error('No photo uploaded', 400);
        }

        $file = $_FILES['photo'];
        $maxBytes = 5 * 1024 * 1024; // 5 MB
        if ($file['size'] > $maxBytes) {
            $this->error('Photo must be under 5 MB', 400);
        }

        $mime = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            $this->error('Only JPEG, PNG or WebP images are allowed', 400);
        }

        $ext      = $allowed[$mime];
        $filename = "avatar_{$userId}_" . time() . ".{$ext}";
        $destDir  = __DIR__ . '/../../../public/uploads/avatars/';
        $destPath = $destDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->error('Failed to save photo', 500);
        }

        $relativePath = 'uploads/avatars/' . $filename;

        // Remove old avatar file if it was ours
        $old = $this->db->prepare("SELECT avatar FROM users WHERE id = ? LIMIT 1");
        $old->execute([$userId]);
        $oldPath = $old->fetchColumn();
        if ($oldPath && str_starts_with($oldPath, 'uploads/avatars/') && file_exists($destDir . basename($oldPath))) {
            @unlink($destDir . basename($oldPath));
        }

        $this->db->prepare("UPDATE users SET avatar = ? WHERE id = ?")
                 ->execute([$relativePath, $userId]);

        $this->json(['photo_url' => $this->fullAvatarUrl($relativePath)]);
    }

    // -------------------------------------------------------------------------
    // POST /api/driver/fcm-token
    // -------------------------------------------------------------------------
    public function saveFcmToken(): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $token  = trim($body['fcm_token'] ?? '');

        if (!$token) $this->error('FCM token required', 400);

        // Add column if missing
        try {
            $this->db->exec("ALTER TABLE driver_api_tokens ADD COLUMN fcm_token VARCHAR(255) NULL");
        } catch (\Exception $e) { /* column exists */ }

        $this->db->prepare(
            "UPDATE driver_api_tokens SET fcm_token = ? WHERE user_id = ?"
        )->execute([$token, $userId]);

        $this->json(['success' => true]);
    }

    // -------------------------------------------------------------------------
    // GET /api/chat/messages
    // -------------------------------------------------------------------------
    public function chatMessages(): void
    {
        $userId = $this->authenticate();
        $appId  = $this->driverApplicationId($userId);

        if (!$appId) {
            $this->json(['messages' => []]);
        }

        // Mark admin messages as read
        $this->db->prepare(
            "UPDATE driver_application_messages SET is_read = 1
             WHERE application_id = ? AND sender_type = 'admin' AND is_read = 0"
        )->execute([$appId]);

        $stmt = $this->db->prepare(
            "SELECT id,
                    CASE WHEN sender_type = 'applicant' THEN 'driver' ELSE 'admin' END AS sender,
                    message,
                    CASE WHEN is_read = 1 THEN created_at ELSE NULL END AS read_at,
                    created_at
             FROM driver_application_messages
             WHERE application_id = ?
             ORDER BY created_at ASC
             LIMIT 100"
        );
        $stmt->execute([$appId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $this->json(['messages' => $rows]);
    }

    // -------------------------------------------------------------------------
    // POST /api/chat/send
    // -------------------------------------------------------------------------
    public function chatSend(): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $text   = trim($body['message'] ?? '');

        if (!$text) $this->error('Message cannot be empty', 400);
        if (mb_strlen($text) > 2000) $this->error('Message too long', 400);

        $appId = $this->driverApplicationId($userId);
        if (!$appId) $this->error('No driver application found', 404);

        $this->db->prepare(
            "INSERT INTO driver_application_messages (application_id, sender_type, sender_id, message, is_read)
             VALUES (?, 'applicant', ?, ?, 0)"
        )->execute([$appId, $userId, htmlspecialchars($text)]);

        $id = (int)$this->db->lastInsertId();

        // Notify admin (bell + lead activity) — mirrors DeliveryController::sendApplicationMessage()
        try {
            $driverRow = $this->db->prepare("SELECT first_name, last_name FROM users WHERE id = ? LIMIT 1");
            $driverRow->execute([$userId]);
            $driver = $driverRow->fetch(\PDO::FETCH_ASSOC);
            $driverName = trim(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? ''));

            $leadRow = $this->db->prepare("SELECT lead_id FROM driver_applications WHERE id = ? LIMIT 1");
            $leadRow->execute([$appId]);
            $leadId = $leadRow->fetchColumn();

            $bellLink = $leadId
                ? "/admin/leads/view?id={$leadId}#messages"
                : "/admin/delivery/staff?tab=applications&app={$appId}";

            require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'driver_message',
                'Driver Applicant Message',
                $driverName . ' replied on their application.',
                ['link' => $bellLink, 'icon' => 'comment']
            );

            if ($leadId) {
                $this->db->prepare(
                    "INSERT INTO lead_activities (lead_id, activity_type, description, outcome, created_by)
                     VALUES (?, 'email', ?, NULL, ?)"
                )->execute([$leadId, $driverName . ' (applicant) sent a message: ' . mb_strimwidth($text, 0, 100, '…'), $userId]);
            }
        } catch (\Exception $e) { /* non-fatal */ }

        $this->json(['success' => true, 'id' => $id]);
    }

    // -------------------------------------------------------------------------
    // GET /api/pickups
    // Returns purchase orders assigned to this driver, split into:
    //   available — newly assigned, driver hasn't responded (driver_acceptance_status IS NULL)
    //   active    — driver accepted and is heading to supplier (driver_acceptance_status = 'accepted')
    //               or already picked_up
    // -------------------------------------------------------------------------
    public function listPickups(): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT po.id, po.po_number, po.total_amount, po.notes,
                   po.driver_assigned_at, po.status, po.driver_acceptance_status,
                   po.distribution_request_id,
                   s.company_name AS supplier_name,
                   CONCAT_WS(', ', s.address, s.city, s.province, s.postal_code) AS supplier_address,
                   s.phone AS supplier_phone,
                   s.latitude  AS supplier_lat,
                   s.longitude AS supplier_lng,
                   (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) AS item_count
            FROM purchase_orders po
            JOIN suppliers s ON s.id = po.supplier_id
            WHERE po.assigned_driver_id = ?
              AND (
                (po.status IN ('ready_for_pickup', 'picked_up')
                 AND (po.driver_acceptance_status IS NULL OR po.driver_acceptance_status = 'accepted'))
                OR
                (po.status = 'completed' AND po.updated_at >= NOW() - INTERVAL 30 DAY)
              )
            ORDER BY po.driver_assigned_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Read depot coordinates from settings (OCS drop-off location)
        $depotRows     = $this->db->query(
            "SELECT `key`, value FROM settings WHERE `key` IN ('depot_lat','depot_lng','depot_address')"
        )->fetchAll(\PDO::FETCH_ASSOC);
        $depotSettings = array_column($depotRows, 'value', 'key');
        $depotLat  = (isset($depotSettings['depot_lat'])  && $depotSettings['depot_lat']  !== '')
            ? (float)$depotSettings['depot_lat']  : null;
        $depotLng  = (isset($depotSettings['depot_lng'])  && $depotSettings['depot_lng']  !== '')
            ? (float)$depotSettings['depot_lng']  : null;
        $depotAddr = $depotSettings['depot_address'] ?? null;

        // Pre-load items for all POs in one query, including supplier name
        $poIds = array_column($rows, 'id');
        $lineItems  = []; // keyed by purchase_order_id
        $distItems  = []; // keyed by distribution_request_id — all items merged across POs
        if (!empty($poIds)) {
            $placeholders = implode(',', array_fill(0, count($poIds), '?'));
            $iStmt = $this->db->prepare("
                SELECT poi.purchase_order_id, poi.id, sp.product_name,
                       poi.quantity_ordered, poi.unit_cost, poi.total_cost, poi.notes,
                       COALESCE(sp.weight_kg, 0) * poi.quantity_ordered AS weight_kg,
                       po.distribution_request_id,
                       s.company_name AS supplier_name
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON sp.id = poi.product_id
                JOIN purchase_orders po ON po.id = poi.purchase_order_id
                JOIN suppliers s ON s.id = po.supplier_id
                WHERE poi.purchase_order_id IN ({$placeholders})
                ORDER BY po.distribution_request_id ASC, s.company_name ASC, poi.id ASC
            ");
            $iStmt->execute($poIds);
            foreach ($iStmt->fetchAll(\PDO::FETCH_ASSOC) as $li) {
                $entry = [
                    'id'            => (int)$li['id'],
                    'product_name'  => $li['product_name'] ?? 'Unknown product',
                    'quantity'      => (int)$li['quantity_ordered'],
                    'unit_cost'     => (float)$li['unit_cost'],
                    'total_cost'    => (float)$li['total_cost'],
                    'weight_kg'     => (float)$li['weight_kg'],
                    'notes'         => $li['notes'],
                    'supplier_name' => $li['supplier_name'] ?? '',
                ];
                $lineItems[(int)$li['purchase_order_id']][] = $entry;
                if (!empty($li['distribution_request_id'])) {
                    $distItems[(int)$li['distribution_request_id']][] = $entry;
                }
            }
        }

        $available = [];
        $active    = [];
        $completed = [];

        // Track distribution_request_ids already included to avoid duplicates
        $seenDistributions = [];

        foreach ($rows as $r) {
            $poId          = (int)$r['id'];
            $distRequestId = !empty($r['distribution_request_id']) ? (int)$r['distribution_request_id'] : null;

            // For multi-stop distribution orders, only emit one entry per DR
            if ($distRequestId) {
                if (isset($seenDistributions[$distRequestId])) continue;
                $seenDistributions[$distRequestId] = true;
            }

            // Delivery destination: distribution → business address; regular PO → depot
            $delivAddr = $depotAddr;
            $delivLat  = $depotLat;
            $delivLng  = $depotLng;

            $pickupStops  = null;
            $totalStops   = 1;
            $isDistrib    = false;

            if ($distRequestId) {
                $isDistrib = true;

                // Fetch delivery_assignment pickup_stops + business delivery coords
                $daStmt = $this->db->prepare("
                    SELECT da.pickup_stops, da.total_stops, da.status AS da_status,
                           dr.delivery_street, dr.delivery_city, dr.delivery_province, dr.delivery_postal_code,
                           dr.delivery_type, dr.order_deadline, dr.submitted_at, dr.delivery_distance,
                           bp.delivery_latitude, bp.delivery_longitude
                    FROM delivery_assignments da
                    JOIN distribution_requests dr ON dr.id = da.distribution_request_id
                    JOIN business_profiles bp ON bp.id = dr.business_profile_id
                    WHERE da.distribution_request_id = ? AND da.delivery_type = 'distribution'
                      AND da.driver_id = ?
                      AND da.status NOT IN ('cancelled','failed')
                    LIMIT 1
                ");
                $daStmt->execute([$distRequestId, $userId]);
                $da = $daStmt->fetch(\PDO::FETCH_ASSOC);

                if ($da) {
                    $totalStops  = (int)($da['total_stops'] ?? 1);
                    $delivAddr   = trim("{$da['delivery_street']}, {$da['delivery_city']}, {$da['delivery_province']} {$da['delivery_postal_code']}");
                    $delivLat    = $da['delivery_latitude'] ? (float)$da['delivery_latitude'] : null;
                    $delivLng    = $da['delivery_longitude'] ? (float)$da['delivery_longitude'] : null;

                    if (!empty($da['pickup_stops'])) {
                        $stopsRaw = json_decode($da['pickup_stops'], true) ?? [];

                        // Attach realtime PO status for each stop
                        $distPoStmt = $this->db->prepare("
                            SELECT po.id, po.status, po.driver_acceptance_status
                            FROM purchase_orders po
                            WHERE po.distribution_request_id = ? AND po.assigned_driver_id = ?
                        ");
                        $distPoStmt->execute([$distRequestId, $userId]);
                        $poStatusMap = [];
                        foreach ($distPoStmt->fetchAll(\PDO::FETCH_ASSOC) as $dp) {
                            $poStatusMap[(int)$dp['id']] = $dp['status'];
                        }

                        foreach ($stopsRaw as &$stop) {
                            if ($stop['type'] !== 'pickup') continue;

                            // Attach PO status from the first PO in this stop
                            $stopPoIds = array_map('intval', (array)($stop['po_ids'] ?? []));
                            foreach ($stopPoIds as $spid) {
                                if (isset($poStatusMap[$spid])) {
                                    $stop['po_status'] = $poStatusMap[$spid];
                                    break;
                                }
                            }

                            // Embed items belonging to this stop's POs
                            $stopItems = [];
                            foreach ($stopPoIds as $spid) {
                                foreach ($lineItems[$spid] ?? [] as $item) {
                                    $stopItems[] = $item;
                                }
                            }
                            $stop['items'] = $stopItems;
                        }
                        unset($stop);
                        $pickupStops = $stopsRaw;
                    }
                }
            }

            // For distribution: sum totals + count items across all POs in the DR
            $allItems = $distRequestId
                ? ($distItems[$distRequestId] ?? $lineItems[$poId] ?? [])
                : ($lineItems[$poId] ?? []);
            $effectiveTotal = $distRequestId && !empty($distItems[$distRequestId])
                ? array_sum(array_column($distItems[$distRequestId], 'total_cost'))
                : (float)$r['total_amount'];
            $effectiveItemCount = $distRequestId && !empty($distItems[$distRequestId])
                ? count($distItems[$distRequestId])
                : (int)$r['item_count'];

            // Compute delivery timeline & earning fields
            $distanceKm    = null;
            $etaMinutes    = null;
            $orderDeadline = null;
            $submittedAt   = null;
            $deliveryType  = null;
            $estimatedEarning = null;
            $deadlineAt    = null;

            if ($isDistrib && $da) {
                $distanceKm   = $da['delivery_distance'] !== null ? (float)$da['delivery_distance'] : null;
                $etaMinutes   = $distanceKm !== null ? (int)ceil($distanceKm / 30 * 60) : null;
                $orderDeadline = $da['order_deadline'] ?? null;
                $submittedAt  = $da['submitted_at'] ?? null;
                $deliveryType = $da['delivery_type'] ?? null;
                if ($distanceKm !== null) {
                    $estimatedEarning = round(5.0 + $distanceKm * 0.50, 2);
                }
            }

            // Accept deadline: assigned_at + 5 minutes (for pending pickups)
            if (!empty($r['driver_assigned_at'])) {
                $assignedTs = strtotime($r['driver_assigned_at']);
                if ($assignedTs) {
                    $deadlineAt = date('Y-m-d H:i:s', $assignedTs + 300);
                }
            }

            // Total weight across all items
            $totalWeightKg = array_sum(array_column($allItems, 'weight_kg'));

            $item = [
                'id'                   => $poId,
                'po_number'            => $r['po_number'],
                'total_amount'         => $effectiveTotal,
                'notes'                => $r['notes'],
                'status'               => $r['status'],
                'acceptance_status'    => $r['driver_acceptance_status'],
                'assigned_at'          => $r['driver_assigned_at'],
                'item_count'           => $effectiveItemCount,
                'supplier_name'        => $r['supplier_name'],
                'supplier_address'     => $r['supplier_address'],
                'supplier_phone'       => $r['supplier_phone'],
                'supplier_lat'         => $r['supplier_lat'] !== null ? (float)$r['supplier_lat'] : null,
                'supplier_lng'         => $r['supplier_lng'] !== null ? (float)$r['supplier_lng'] : null,
                'delivery_address'     => $delivAddr,
                'delivery_lat'         => $delivLat,
                'delivery_lng'         => $delivLng,
                // Delivery timeline & distance fields
                'delivery_type'        => $deliveryType,
                'order_deadline'       => $orderDeadline,
                'submitted_at'         => $submittedAt,
                'delivery_distance_km' => $distanceKm,
                'delivery_eta_minutes' => $etaMinutes,
                'total_weight_kg'      => $totalWeightKg > 0 ? round($totalWeightKg, 2) : null,
                'estimated_earning'    => $estimatedEarning,
                'deadline_at'          => $deadlineAt,
                // Distribution multi-stop fields
                'is_distribution'      => $isDistrib,
                'distribution_request_id' => $distRequestId,
                'total_stops'          => $totalStops,
                'pickup_stops'         => $pickupStops,
                // Line items: for distribution orders merge all POs; for single orders use own PO only
                'items'                => $allItems,
            ];

            if ($r['status'] === 'completed') {
                $completed[] = $item;
            } elseif ($r['driver_acceptance_status'] === null) {
                $available[] = $item;
            } else {
                $active[] = $item;
            }
        }

        $this->json(['available' => $available, 'active' => $active, 'completed' => $completed]);
    }

    // -------------------------------------------------------------------------
    // POST /api/pickups/:id/accept
    // Driver accepts the assigned pickup — they will head to the supplier.
    // -------------------------------------------------------------------------
    public function acceptPickup(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT id, po_number, distribution_request_id FROM purchase_orders
            WHERE id = ? AND assigned_driver_id = ?
              AND status = 'ready_for_pickup'
              AND driver_acceptance_status IS NULL
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $po = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$po) {
            $this->error('Pickup not found or already responded', 404);
        }

        if (!empty($po['distribution_request_id'])) {
            // Distribution: mark all POs in this request accepted so every supplier sees driver is coming
            $this->db->prepare("
                UPDATE purchase_orders
                SET driver_acceptance_status = 'accepted', updated_at = NOW()
                WHERE distribution_request_id = ? AND assigned_driver_id = ?
            ")->execute([(int)$po['distribution_request_id'], $userId]);
        } else {
            $this->db->prepare("
                UPDATE purchase_orders
                SET driver_acceptance_status = 'accepted', updated_at = NOW()
                WHERE id = ?
            ")->execute([$id]);
        }

        // Log acceptance (distribution pickup)
        $this->logDriverActivity(
            $userId, 'accepted', 'distribution',
            null, $id,
            $po['distribution_request_id'] ? (int)$po['distribution_request_id'] : null,
            $po['po_number'] ?? ''
        );

        // Log to distribution status history so activity section shows the acceptance
        if (!empty($po['distribution_request_id'])) {
            $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, 'ready', 'ready', 'driver', ?, 'Driver accepted assignment — en route to suppliers', NOW())
            ")->execute([(int)$po['distribution_request_id'], $userId]);
        }

        $this->json(['success' => true, 'po_number' => $po['po_number']]);
    }

    // -------------------------------------------------------------------------
    // POST /api/pickups/:id/decline
    // Driver declines the pickup. System tries to reassign to another online
    // driver; if none available, admin is notified.
    // -------------------------------------------------------------------------
    public function declinePickup(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT po.id, po.po_number, po.distribution_request_id, s.city AS supplier_city
            FROM purchase_orders po
            JOIN suppliers s ON s.id = po.supplier_id
            WHERE po.id = ? AND po.assigned_driver_id = ?
              AND po.status = 'ready_for_pickup'
              AND po.driver_acceptance_status IS NULL
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $po = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$po) {
            $this->error('Pickup not found or already responded', 404);
        }

        $distRequestId = (int)($po['distribution_request_id'] ?? 0);

        if ($distRequestId > 0) {
            // ── Distribution order: reset the entire DR assignment ──────────
            // 1. Clear driver from ALL POs in this distribution request
            $this->db->prepare("
                UPDATE purchase_orders
                SET assigned_driver_id = NULL,
                    driver_acceptance_status = NULL,
                    driver_assigned_at = NULL,
                    updated_at = NOW()
                WHERE distribution_request_id = ?
            ")->execute([$distRequestId]);

            // 2. Cancel the existing delivery_assignment record so auto-assign creates a fresh one
            $this->db->prepare("
                UPDATE delivery_assignments
                SET status = 'cancelled', updated_at = NOW()
                WHERE distribution_request_id = ? AND status NOT IN ('delivered','completed')
            ")->execute([$distRequestId]);

            // 3. Reset DR status back to 'paid' so the safety gate in autoAssignDistributionDriver passes
            $this->db->prepare("
                UPDATE distribution_requests
                SET status = 'paid', updated_at = NOW()
                WHERE id = ? AND status = 'ready'
            ")->execute([$distRequestId]);

            // 4. Log the decline in distribution_status_history
            $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, 'ready', 'paid', 'driver', ?, 'Driver declined — reassigning', NOW())
            ")->execute([$distRequestId, $userId]);

            // 5. Re-run full distribution auto-assign (excludes declined driver via driver_availability)
            // Mark the declining driver as unavailable temporarily so they aren't re-selected
            $this->db->prepare("
                UPDATE driver_availability SET status = 'busy', updated_at = NOW()
                WHERE driver_id = ? AND status = 'available'
            ")->execute([$userId]);

            \App\Controllers\AdminDistributionController::autoAssignDistributionDriver($distRequestId, $this->db);

            // Restore the declining driver's availability after reassign attempt
            $this->db->prepare("
                UPDATE driver_availability SET status = 'available', updated_at = NOW()
                WHERE driver_id = ? AND status = 'busy'
            ")->execute([$userId]);

            // Log decline (distribution pickup)
            $this->logDriverActivity(
                $userId, 'declined', 'distribution',
                null, $id,
                $distRequestId,
                $po['po_number'] ?? '',
                'Driver declined distribution pickup'
            );

            $this->json(['success' => true, 'distribution' => true]);
            return;
        }

        // ── Regular marketplace order: original single-PO logic ─────────────
        // Clear current driver assignment
        $this->db->prepare("
            UPDATE purchase_orders
            SET assigned_driver_id = NULL,
                driver_acceptance_status = NULL,
                driver_assigned_at = NULL,
                updated_at = NOW()
            WHERE id = ?
        ")->execute([$id]);

        // Try to find another available online driver (excluding the declining driver)
        // driver_online = 1 is the explicit source of truth — don't filter by GPS staleness
        // (a stationary driver may not emit GPS pings but is still genuinely available)
        $nextDriver = $this->db->prepare("
            SELECT da.driver_id AS user_id
            FROM driver_availability da
            JOIN users u ON u.id = da.driver_id
            WHERE da.status = 'available'
              AND da.driver_id != ?
              AND u.status = 'active'
              AND u.role IN ('driver', 'delivery')
              AND EXISTS (
                  SELECT 1 FROM driver_api_tokens dat
                  WHERE dat.user_id = da.driver_id AND dat.driver_online = 1
              )
              AND da.driver_id NOT IN (
                  SELECT assigned_driver_id FROM purchase_orders
                  WHERE assigned_driver_id IS NOT NULL
                    AND status IN ('ready_for_pickup')
                    AND driver_acceptance_status = 'accepted'
              )
            ORDER BY RAND()
            LIMIT 1
        ");
        $nextDriver->execute([$userId]);
        $newDriver = $nextDriver->fetch(\PDO::FETCH_ASSOC);

        if ($newDriver) {
            $this->db->prepare("
                UPDATE purchase_orders
                SET assigned_driver_id = ?, driver_assigned_at = NOW(),
                    driver_acceptance_status = NULL, updated_at = NOW()
                WHERE id = ?
            ")->execute([$newDriver['user_id'], $id]);

            try {
                self::sendPush(
                    $this->db,
                    $newDriver['user_id'],
                    'New Pickup Assigned',
                    "You have been assigned PO #{$po['po_number']} for pickup.",
                    ['type' => 'pickup', 'pickup_id' => $id]
                );
            } catch (\Exception $e) { /* non-fatal */ }
        } else {
            try {
                $this->db->prepare("
                    INSERT INTO admin_notifications (type, title, message, link, priority, created_at)
                    VALUES ('warning', ?, ?, ?, 'high', NOW())
                ")->execute([
                    "Pickup Declined — No Driver Available",
                    "PO #{$po['po_number']} was declined by driver and no other drivers are online. Manual reassignment needed.",
                    "/admin/purchase-orders/view?id={$id}",
                ]);
            } catch (\Exception $e) { /* non-fatal */ }
        }

        // Log decline (marketplace/regular PO)
        $this->logDriverActivity(
            $userId, 'declined', 'distribution',
            null, $id, null,
            $po['po_number'] ?? '',
            'Driver declined pickup'
        );

        $this->json(['success' => true, 'reassigned' => $newDriver !== false]);
    }

    // -------------------------------------------------------------------------
    // POST /api/pickups/:id/confirm
    // Driver confirms they have collected the goods from the supplier.
    // -------------------------------------------------------------------------
    public function confirmPickup(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT id, po_number, distribution_request_id FROM purchase_orders
            WHERE id = ? AND assigned_driver_id = ? AND status = 'ready_for_pickup'
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $po = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$po) {
            $this->error('Pickup not found or already confirmed', 404);
        }

        $this->db->prepare("
            UPDATE purchase_orders SET status = 'picked_up', updated_at = NOW() WHERE id = ?
        ")->execute([$id]);

        // Notify admin
        try {
            $this->db->prepare("
                INSERT INTO admin_notifications (type, title, message, link, priority, created_at)
                VALUES ('delivery', ?, ?, ?, 'normal', NOW())
            ")->execute([
                "PO #{$po['po_number']} — Picked Up by Driver",
                "Driver confirmed collection of PO #{$po['po_number']}.",
                "/admin/purchase-orders/view?id={$id}",
            ]);
        } catch (\Exception $e) { /* silently ignore */ }

        // Supplier bell — confirm their items were collected
        try {
            require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
            $supRow = $this->db->prepare("SELECT supplier_id FROM purchase_orders WHERE id = ? LIMIT 1");
            $supRow->execute([$id]);
            $supId = (int)$supRow->fetchColumn();
            if ($supId) {
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    $supId, 'delivery',
                    '✅ Items Collected — PO #' . $po['po_number'],
                    "The driver has confirmed collection of PO #{$po['po_number']}. Items are on their way to the customer.",
                    'supplier/orders/view?id=' . $id, 'check-circle',
                    '✅ Articles collectés — BC #' . $po['po_number'],
                    "Le chauffeur a confirmé la collecte de BC #{$po['po_number']}. Les articles sont en route vers le client."
                );
            }
        } catch (\Exception $e) { /* non-fatal */ }

        // If this is a distribution PO, check whether all stops are now picked up
        $drId = (int)($po['distribution_request_id'] ?? 0);
        if ($drId > 0) {
            try {
                $stillPending = $this->db->prepare("
                    SELECT COUNT(*) FROM purchase_orders
                    WHERE distribution_request_id = ?
                      AND status NOT IN ('picked_up','completed','cancelled')
                ");
                $stillPending->execute([$drId]);
                if ((int)$stillPending->fetchColumn() === 0) {
                    // All stops collected — driver is now en route to the business
                    $this->db->prepare("
                        UPDATE distribution_requests
                        SET status = 'in_transit', in_transit_at = NOW(), updated_at = NOW()
                        WHERE id = ? AND status = 'ready'
                    ")->execute([$drId]);

                    $this->db->prepare("
                        UPDATE delivery_assignments
                        SET status = 'in_transit', picked_up_at = NOW(), updated_at = NOW()
                        WHERE distribution_request_id = ?
                          AND status NOT IN ('delivered','completed','cancelled')
                    ")->execute([$drId]);

                    // Log status change
                    $this->db->prepare("
                        INSERT INTO distribution_status_history
                        (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                        VALUES (?, 'ready', 'in_transit', 'driver', ?, 'All items collected, en route to delivery', NOW())
                    ")->execute([$drId, $userId]);

                    // Notify business — driver is on the way
                    $drInfo = $this->db->prepare("
                        SELECT dr.request_number, dr.business_profile_id
                        FROM distribution_requests dr WHERE dr.id = ? LIMIT 1
                    ");
                    $drInfo->execute([$drId]);
                    $drRow = $drInfo->fetch(\PDO::FETCH_ASSOC);
                    if ($drRow) {
                        require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
                        \App\Helpers\NotificationHelper::addBusinessNotification(
                            (int)$drRow['business_profile_id'],
                            'delivery',
                            '🚗 Driver En Route — #' . $drRow['request_number'],
                            'Your driver has collected all items and is now heading to your delivery address.',
                            'distribution/requests/show?id=' . $drId
                        );
                    }
                }
            } catch (\Exception $e) { /* non-fatal — don't block the pickup confirmation */ }
        }

        $this->json(['success' => true, 'po_number' => $po['po_number']]);
    }

    // -------------------------------------------------------------------------
    // POST /api/pickups/:id/complete
    // Driver confirms goods have been dropped off at the depot.
    // Moves PO from 'picked_up' → 'completed'.
    // -------------------------------------------------------------------------
    public function completePickup(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT id, po_number FROM purchase_orders
            WHERE id = ? AND assigned_driver_id = ? AND status = 'picked_up'
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $po = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$po) {
            $this->error('Pickup not found or not in transit', 404);
        }

        $this->db->prepare("
            UPDATE purchase_orders
            SET status = 'completed', updated_at = NOW()
            WHERE id = ?
        ")->execute([$id]);

        // Admin notification
        try {
            $this->db->prepare("
                INSERT INTO admin_notifications (type, title, message, link, priority, created_at)
                VALUES ('delivery', ?, ?, ?, 'normal', NOW())
            ")->execute([
                "PO #{$po['po_number']} — Delivered to Depot",
                "Driver confirmed drop-off of PO #{$po['po_number']} at the OCS depot.",
                "/admin/purchase-orders/view?id={$id}",
            ]);
        } catch (\Exception $e) { /* non-fatal */ }

        // Calculate performance scores if this PO belongs to a distribution request
        if (!empty($po['distribution_request_id'])) {
            try {
                \App\Services\ScoringService::calculateForDistributionRequest(
                    (int)$po['distribution_request_id'], $this->db
                );
            } catch (\Throwable $e) { /* non-fatal */ }
        }

        $this->json(['success' => true, 'po_number' => $po['po_number']]);
    }

    // -------------------------------------------------------------------------
    // POST /api/distribution/:id/step
    // Driver app fires a lightweight step event during a distribution run.
    // Body: {"step": "heading_to_supplier" | "en_route_to_customer"}
    // Records the timestamp on delivery_assignments — admin timeline reads it.
    // -------------------------------------------------------------------------
    public function recordDistributionStep(int $id): void
    {
        $userId = $this->authenticate();
        $body   = json_decode(file_get_contents('php://input'), true) ?? [];
        $step   = $body['step'] ?? '';

        $allowed = [
            'heading_to_supplier'  => 'heading_to_supplier_at',
            'en_route_to_customer' => 'en_route_to_customer_at',
        ];

        if (!isset($allowed[$step])) {
            $this->error('Invalid step. Use: ' . implode(', ', array_keys($allowed)), 400);
        }

        $col = $allowed[$step];

        // Verify the driver has an active assignment for this distribution request
        $stmt = $this->db->prepare("
            SELECT id FROM delivery_assignments
            WHERE distribution_request_id = ? AND driver_id = ?
              AND delivery_type = 'distribution'
              AND status NOT IN ('delivered','completed','cancelled')
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $da = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$da) {
            $this->error('No active assignment found for this distribution request', 404);
        }

        // Only write if not already set (idempotent — screen may reload)
        $this->db->prepare("
            UPDATE delivery_assignments
            SET $col = COALESCE($col, NOW()), updated_at = NOW()
            WHERE id = ?
        ")->execute([$da['id']]);

        $this->json(['success' => true, 'step' => $step]);
    }

    // -------------------------------------------------------------------------
    // POST /api/distribution/:id/complete
    // Driver confirms goods have been delivered to the business (distribution).
    // :id = distribution_request_id
    // Transitions DR: ready|in_transit → delivered
    // -------------------------------------------------------------------------
    public function completeDistributionDelivery(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT da.id AS assignment_id, dr.id AS dr_id,
                   dr.request_number, dr.business_profile_id, bp.company_name, dr.status AS dr_status,
                   COALESCE(dr.service_fee, 0)  AS service_fee,
                   COALESCE(dr.delivery_fee, 0) AS delivery_fee,
                   COALESCE(dr.handling_fee, 0) AS handling_fee,
                   COALESCE(dr.tip_amount, 0)   AS tip_amount
            FROM delivery_assignments da
            JOIN distribution_requests dr ON dr.id = da.distribution_request_id
            JOIN business_profiles bp ON bp.id = dr.business_profile_id
            WHERE da.distribution_request_id = ?
              AND da.driver_id = ?
              AND da.delivery_type = 'distribution'
              AND da.status NOT IN ('delivered','completed','cancelled')
              AND dr.status IN ('ready','in_transit')
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $da = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$da) {
            $this->error('Assignment not found or delivery already completed', 404);
        }

        $drId = (int)$da['dr_id'];

        // Mark delivery_assignment as delivered
        $this->db->prepare("
            UPDATE delivery_assignments
            SET status = 'delivered', delivered_at = NOW(), updated_at = NOW()
            WHERE id = ?
        ")->execute([$da['assignment_id']]);

        // Mark distribution_request as delivered
        $this->db->prepare("
            UPDATE distribution_requests
            SET status = 'delivered', delivered_at = NOW(), updated_at = NOW()
            WHERE id = ?
        ")->execute([$drId]);

        // Mark all POs as completed
        $this->db->prepare("
            UPDATE purchase_orders
            SET status = 'completed', updated_at = NOW()
            WHERE distribution_request_id = ? AND status NOT IN ('completed','cancelled')
        ")->execute([$drId]);

        // Free driver
        $this->db->prepare("
            UPDATE driver_availability SET status = 'available', updated_at = NOW()
            WHERE driver_id = ?
        ")->execute([$userId]);

        // Record driver earnings for this distribution run
        try {
            $serviceFeeShare = round((float)$da['service_fee'] * 0.05, 2);
            $deliveryFee     = (float)$da['delivery_fee'];
            $handlingFee     = (float)$da['handling_fee'];
            $tip             = (float)$da['tip_amount'];
            $commission      = round(($deliveryFee * 0.20) + ($handlingFee * 0.20), 2);
            $totalEarning    = round($serviceFeeShare + $deliveryFee + $handlingFee + $tip, 2);
            $netEarning      = round($totalEarning - $commission, 2);

            // Avoid duplicate earnings record if called twice
            $exists = $this->db->prepare("SELECT id FROM delivery_earnings WHERE delivery_id = ? AND driver_id = ? LIMIT 1");
            $exists->execute([$da['assignment_id'], $userId]);
            if (!$exists->fetch()) {
                $this->db->prepare("
                    INSERT INTO delivery_earnings
                    (driver_id, delivery_id, order_id, base_fee, distance_fee, bonus, tip,
                     total_earning, platform_commission, net_earning,
                     payment_status, payout_method, notes, created_at, updated_at)
                    VALUES (?, ?, NULL, ?, ?, ?, ?, ?, ?, ?, 'pending', 'pending', ?, NOW(), NOW())
                ")->execute([
                    $userId,
                    $da['assignment_id'],
                    $serviceFeeShare,   // base_fee     = 5% of service fee
                    $deliveryFee,       // distance_fee = 100% delivery fee
                    $handlingFee,       // bonus        = 100% handling fee
                    $tip,               // tip (separate)
                    $totalEarning,
                    $commission,        // platform_commission = 20% delivery + 20% handling
                    $netEarning,
                    "Distribution run #{$da['request_number']}" . ($tip > 0 ? " + tip \${$tip}" : ''),
                ]);
            }
        } catch (\Exception $e) {
            error_log('Distribution earnings record error: ' . $e->getMessage());
        }

        // Status history
        try {
            $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, ?, 'delivered', 'driver', ?, 'Driver confirmed delivery to business', NOW())
            ")->execute([$drId, $da['dr_status'], $userId]);
        } catch (\Exception $e) { /* non-fatal */ }

        // Admin bell
        require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
        \App\Helpers\NotificationHelper::add(
            'delivery',
            '✅ Distribution Delivered — #' . $da['request_number'],
            "Driver confirmed delivery of request #{$da['request_number']} to {$da['company_name']}.",
            ['link' => '/admin/distribution/view?id=' . $drId, 'icon' => 'check-circle', 'priority' => 'normal']
        );

        // Business bell
        \App\Helpers\NotificationHelper::addBusinessNotification(
            (int)$da['business_profile_id'],
            'delivery',
            '✅ Order Delivered — #' . $da['request_number'],
            'Your distribution order has been delivered. Please verify the received goods and contact us if there are any discrepancies.',
            'distribution/requests/show?id=' . $drId
        );

        // Supplier bell — notify all suppliers their items reached the customer
        try {
            $supDelivered = $this->db->prepare("
                SELECT po.id, po.po_number, po.supplier_id
                FROM purchase_orders po
                WHERE po.distribution_request_id = ?
                  AND po.status NOT IN ('cancelled')
            ");
            $supDelivered->execute([$drId]);
            foreach ($supDelivered->fetchAll(\PDO::FETCH_ASSOC) as $spd) {
                \App\Helpers\NotificationHelper::addSupplierNotification(
                    (int)$spd['supplier_id'], 'delivery',
                    '✅ Delivered — PO #' . $spd['po_number'],
                    "PO #{$spd['po_number']} has been successfully delivered to the customer for request #{$da['request_number']}.",
                    'supplier/orders/view?id=' . $spd['id'], 'check-circle',
                    '✅ Livré — BC #' . $spd['po_number'],
                    "BC #{$spd['po_number']} a été livré avec succès au client pour la demande #{$da['request_number']}."
                );
            }
        } catch (\Exception $e) { /* non-fatal */ }

        // Business delivery confirmation email
        try {
            $bizStmt = $this->db->prepare("
                SELECT u.email, u.first_name
                FROM distribution_requests dr
                JOIN business_profiles bp ON bp.id = dr.business_profile_id
                JOIN users u ON u.id = bp.user_id
                WHERE dr.id = ? LIMIT 1
            ");
            $bizStmt->execute([$drId]);
            $biz = $bizStmt->fetch(\PDO::FETCH_ASSOC);

            if ($biz && !empty($biz['email'])) {
                $subject = "Your Order #{$da['request_number']} Has Been Delivered!";
                $body = "
                <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                    <div style='background:#00b207;padding:20px;text-align:center;'>
                        <h1 style='color:white;margin:0;'>Order Delivered!</h1>
                    </div>
                    <div style='padding:30px;background:#f8f9fa;'>
                        <p>Hi {$biz['first_name']},</p>
                        <p>Your distribution order <strong>#{$da['request_number']}</strong> has been delivered to your address.</p>
                        <div style='background:#d1fae5;border-radius:8px;padding:15px;margin:20px 0;'>
                            <p style='margin:0;color:#065f46;'><strong>Please verify your received goods.</strong><br>
                            If you notice any discrepancies, please contact us immediately.</p>
                        </div>
                        <div style='text-align:center;margin:30px 0;'>
                            <a href='" . url('distribution/requests/show?id=' . $drId) . "' style='display:inline-block;background:#00b207;color:white;padding:12px 30px;text-decoration:none;border-radius:8px;font-weight:bold;'>
                                View Order Details
                            </a>
                        </div>
                        <hr style='border:none;border-top:1px solid #ddd;margin:20px 0;'>
                        <p style='color:#888;font-size:12px;'>OCS Distribution | ocsapp.ca</p>
                    </div>
                </div>";
                require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                \App\Helpers\EmailHelper::send($biz['email'], $subject, $body);
            }
        } catch (\Exception $e) { /* non-fatal */ }

        // Calculate performance scores for all parties
        try {
            \App\Services\ScoringService::calculateForDistributionRequest($drId, $this->db);
        } catch (\Throwable $e) { /* non-fatal */ }

        $this->json(['success' => true, 'request_number' => $da['request_number']]);
    }

    // -------------------------------------------------------------------------
    // POST /api/orders/:id/outcome
    // Driver reports the final outcome of an attempted delivery.
    //
    // Successful outcomes  → order status = 'delivered'
    //   delivered           — handed directly to customer
    //   left_at_door        — safe-drop, customer not home but approved
    //
    // Failed outcomes      → order status = 'failed'
    //   customer_not_available — no answer after reasonable wait
    //   wrong_address          — address incorrect / doesn't exist
    //   other                  — free-text reason required
    // -------------------------------------------------------------------------
    public function orderOutcome(int $id): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $outcome = trim($body['outcome'] ?? '');
        $note    = trim($body['note']    ?? '');

        $successOutcomes = ['delivered', 'left_at_door'];
        $failedOutcomes  = ['customer_not_available', 'wrong_address', 'other'];
        $allowed = array_merge($successOutcomes, $failedOutcomes);

        if (!in_array($outcome, $allowed)) {
            $this->error('Invalid outcome', 400);
        }
        if ($outcome === 'other' && $note === '') {
            $this->error('A note is required for "other" outcome', 400);
        }

        // Ensure outcome columns exist
        foreach (['outcome VARCHAR(50)', 'outcome_note TEXT'] as $col) {
            try { $this->db->exec("ALTER TABLE orders ADD COLUMN $col NULL"); }
            catch (\Exception $e) { /* already exists */ }
        }

        // Verify ownership — driver must be assigned and order must be active
        $stmt = $this->db->prepare(
            "SELECT id, driver_status FROM orders
             WHERE id = ? AND driver_id = ? AND status = 'out_for_delivery'
             LIMIT 1"
        );
        $stmt->execute([$id, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$order) $this->error('Order not found or not active', 404);

        if (in_array($outcome, $successOutcomes)) {
            // Mark as delivered
            $this->db->prepare(
                "UPDATE orders
                 SET status = 'delivered', driver_status = 'delivered',
                     outcome = ?, outcome_note = ?,
                     delivered_at = NOW(), updated_at = NOW()
                 WHERE id = ?"
            )->execute([$outcome, $note ?: null, $id]);

            $this->onDeliveryCompleted($id, $userId);

        } else {
            // Mark as failed
            $this->db->prepare(
                "UPDATE orders
                 SET status = 'failed', driver_status = 'failed',
                     outcome = ?, outcome_note = ?,
                     updated_at = NOW()
                 WHERE id = ?"
            )->execute([$outcome, $note ?: null, $id]);

            // Free up driver availability
            $this->db->prepare(
                "UPDATE driver_availability SET status = 'available' WHERE driver_id = ?"
            )->execute([$userId]);

            // Load order + buyer + shop for notifications
            try {
                $failStmt = $this->db->prepare(
                    "SELECT o.*,
                            u.first_name  AS customer_first_name,
                            u.email       AS customer_email,
                            s.name        AS shop_name,
                            s.user_id     AS shop_user_id
                     FROM orders o
                     JOIN users u ON u.id = o.user_id
                     JOIN shops s ON s.id = o.shop_id
                     WHERE o.id = ? LIMIT 1"
                );
                $failStmt->execute([$id]);
                $failOrder = $failStmt->fetch(\PDO::FETCH_ASSOC);

                $reasonLabel = match($outcome) {
                    'customer_not_available' => 'Customer not available',
                    'wrong_address'          => 'Wrong address',
                    'other'                  => 'Other: ' . $note,
                    default                  => $outcome,
                };

                // Admin in-app notification
                require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
                \App\Helpers\NotificationHelper::add(
                    'delivery',
                    '⚠️ Failed Delivery — #' . ($failOrder['order_number'] ?? $id),
                    "Driver reported: {$reasonLabel}. Order needs reassignment.",
                    ['link' => '/admin/orders/view?id=' . $id, 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );

                if ($failOrder) {
                    // Seller in-app notification
                    if (!empty($failOrder['shop_user_id'])) {
                        try {
                            $this->db->prepare(
                                "INSERT INTO user_notifications
                                 (user_id, type, title, message, link, is_read, created_at)
                                 VALUES (?, 'order', ?, ?, '/seller/orders', 0, NOW())"
                            )->execute([
                                $failOrder['shop_user_id'],
                                '⚠️ Delivery Failed — #' . $failOrder['order_number'],
                                "Order #{$failOrder['order_number']} could not be delivered. Reason: {$reasonLabel}. Admin will follow up.",
                            ]);
                        } catch (\Exception $e) { /* non-blocking */ }
                    }

                    // Buyer email notification
                    if (!empty($failOrder['customer_email'])) {
                        require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                        \App\Helpers\EmailHelper::sendOrderStatusUpdate(
                            $failOrder,
                            'out_for_delivery',
                            'failed'
                        );
                    }
                }
            } catch (\Exception $e) { /* non-blocking */ }
        }

        $this->json(['success' => true, 'outcome' => $outcome]);
    }

    // -------------------------------------------------------------------------
    // POST /api/orders/:id/cancel
    // Driver cancels before pickup (before or at picked_up step).
    // Order returns to 'ready' for admin reassignment.
    //
    // Body: { reason: 'cannot_find_merchant' | 'vehicle_issue' |
    //                 'wrong_order' | 'other', note?: string }
    // -------------------------------------------------------------------------
    public function cancelOrder(int $id): void
    {
        $userId = $this->authenticate();
        $body   = $this->jsonBody();
        $reason = trim($body['reason'] ?? '');
        $note   = trim($body['note']   ?? '');

        $allowed = ['cannot_find_merchant', 'vehicle_issue', 'wrong_order', 'other'];
        if (!in_array($reason, $allowed)) {
            $this->error('Invalid reason', 400);
        }

        // Can only cancel if not yet delivered (allow up through picked_up, not en_route or later)
        $cancelableSteps = ['heading_to_merchant', 'arrived_merchant', 'picked_up'];

        $stmt = $this->db->prepare(
            "SELECT id, driver_status, order_number FROM orders
             WHERE id = ? AND driver_id = ? AND status = 'out_for_delivery'
             LIMIT 1"
        );
        $stmt->execute([$id, $userId]);
        $order = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$order) $this->error('Order not found or not active', 404);
        if (!in_array($order['driver_status'], $cancelableSteps)) {
            $this->error('Cannot cancel after being en route to customer', 409);
        }

        // Ensure cancel columns exist
        foreach (['cancel_reason VARCHAR(50)', 'cancel_note TEXT'] as $col) {
            try { $this->db->exec("ALTER TABLE orders ADD COLUMN $col NULL"); }
            catch (\Exception $e) { /* already exists */ }
        }

        // Reset order to ready, un-assign driver
        $this->db->prepare(
            "UPDATE orders
             SET status = 'ready', driver_id = NULL, driver_status = NULL,
                 cancel_reason = ?, cancel_note = ?,
                 accepted_at = NULL, picked_up_at = NULL,
                 updated_at = NOW()
             WHERE id = ?"
        )->execute([$reason, $note ?: null, $id]);

        // Free driver
        $this->db->prepare(
            "UPDATE driver_availability SET status = 'available' WHERE driver_id = ?"
        )->execute([$userId]);

        // Notify admin + seller + buyer
        try {
            $reasonLabel = match($reason) {
                'cannot_find_merchant' => 'Cannot find merchant',
                'vehicle_issue'        => 'Vehicle issue',
                'wrong_order'          => 'Wrong order items',
                'other'                => 'Other: ' . $note,
                default                => $reason,
            };

            // Load shop + buyer info
            $cancelInfo = $this->db->prepare(
                "SELECT o.user_id, s.user_id AS shop_user_id
                 FROM orders o JOIN shops s ON s.id = o.shop_id
                 WHERE o.id = ? LIMIT 1"
            );
            $cancelInfo->execute([$id]);
            $cInfo = $cancelInfo->fetch(\PDO::FETCH_ASSOC);

            // Admin
            require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'delivery',
                '🔄 Order Returned to Queue — #' . $order['order_number'],
                "Driver cancelled before pickup. Reason: {$reasonLabel}. Needs reassignment.",
                ['link' => '/admin/orders/view?id=' . $id, 'icon' => 'undo', 'priority' => 'high']
            );

            if ($cInfo) {
                // Seller in-app
                if (!empty($cInfo['shop_user_id'])) {
                    try {
                        $this->db->prepare(
                            "INSERT INTO user_notifications
                             (user_id, type, title, message, link, is_read, created_at)
                             VALUES (?, 'order', ?, ?, '/seller/orders', 0, NOW())"
                        )->execute([
                            $cInfo['shop_user_id'],
                            '🔄 Delivery Cancelled — #' . $order['order_number'],
                            "The driver cancelled order #{$order['order_number']} before pickup. Reason: {$reasonLabel}. Admin will reassign.",
                        ]);
                    } catch (\Exception $e) { /* non-blocking */ }
                }

                // Buyer email
                require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                \App\Helpers\EmailHelper::sendOrderStatusUpdate(
                    ['id' => $id, 'order_number' => $order['order_number'], 'user_id' => $cInfo['user_id']],
                    'out_for_delivery',
                    'processing'
                );
            }
        } catch (\Exception $e) { /* non-blocking */ }

        $this->json(['success' => true, 'reason' => $reason]);
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * Send a push notification to a driver.
     * Requires FIREBASE_CREDENTIALS_PATH env var pointing to service-account JSON.
     *
     * Usage: DriverApiController::sendPush($db, $driverId, 'New Order', 'You have a delivery nearby!', ['order_id' => 123]);
     */
    public static function sendPush(\PDO $db, int $driverId, string $title, string $body, array $data = []): void
    {
        $stmt = $db->prepare("SELECT fcm_token FROM driver_api_tokens WHERE user_id = ? AND fcm_token IS NOT NULL LIMIT 1");
        $stmt->execute([$driverId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row || !$row['fcm_token']) return;

        $credPath = getenv('FIREBASE_CREDENTIALS_PATH') ?: '';
        if (!$credPath || !file_exists($credPath)) return;

        $accessToken = self::getFirebaseAccessToken($credPath);
        if (!$accessToken) return;

        $projectId = json_decode(file_get_contents($credPath), true)['project_id'] ?? '';
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Determine channel: non-urgent types use oda_updates, everything else oda_dispatch
        $type = $data['type'] ?? '';
        $nonUrgent = in_array($type, ['status_update', 'info', 'chat']);
        $channelId = $nonUrgent ? 'oda_updates' : 'oda_dispatch';

        $payload = json_encode([
            'message' => [
                'token' => $row['fcm_token'],
                'notification' => ['title' => $title, 'body' => $body],
                'data' => array_map('strval', $data),
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'channel_id'          => $channelId,
                        'notification_priority' => 'PRIORITY_MAX',
                        'default_sound'       => true,
                        'default_vibrate_timings' => false,
                        'vibrate_timings'     => ['0s', '0.4s', '0.2s', '0.4s', '0.2s', '0.6s'],
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'interruption-level' => $nonUrgent ? 'active' : 'time-sensitive',
                        ],
                    ],
                ],
            ],
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ],
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    private static function getFirebaseAccessToken(string $credPath): ?string
    {
        $cred = json_decode(file_get_contents($credPath), true);
        $now  = time();
        $jwt  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']))
              . '.' . base64_encode(json_encode([
                  'iss'   => $cred['client_email'],
                  'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                  'aud'   => 'https://oauth2.googleapis.com/token',
                  'iat'   => $now,
                  'exp'   => $now + 3600,
              ]));
        openssl_sign($jwt, $sig, $cred['private_key'], 'SHA256');
        $jwt .= '.' . base64_encode($sig);

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
            CURLOPT_RETURNTRANSFER => true,
        ]);
        $resp = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $resp['access_token'] ?? null;
    }

    // -------------------------------------------------------------------------
    // POST /api/route
    // Fetches a driving route from Google Directions API and returns decoded
    // polyline points + distance + duration. Runs server-side so the API key
    // is never exposed in the app.
    // -------------------------------------------------------------------------
    public function getRoute(): void
    {
        $this->authenticate();

        $body      = $this->jsonBody();
        $originLat = (float)($body['origin_lat'] ?? 0);
        $originLng = (float)($body['origin_lng'] ?? 0);
        $destLat   = (float)($body['dest_lat']   ?? 0);
        $destLng   = (float)($body['dest_lng']   ?? 0);

        if (!$originLat || !$originLng || !$destLat || !$destLng) {
            $this->error('origin_lat, origin_lng, dest_lat, dest_lng required', 400);
        }

        $googleKey = env('GOOGLE_DIRECTIONS_KEY', '');

        if ($googleKey) {
            // ── Google Directions API (IP-restricted server key) ──────────────
            $url = 'https://maps.googleapis.com/maps/api/directions/json?' . http_build_query([
                'origin'      => "{$originLat},{$originLng}",
                'destination' => "{$destLat},{$destLng}",
                'mode'        => 'driving',
                'key'         => $googleKey,
            ]);

            $ch = curl_init($url);
            curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 10]);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response ?: '', true);

            if (($data['status'] ?? '') === 'OK' && !empty($data['routes'][0])) {
                $route = $data['routes'][0];
                $leg   = $route['legs'][0];
                $this->json([
                    'points'   => $this->decodePolyline($route['overview_polyline']['points']),
                    'distance' => $leg['distance']['text'] ?? '',
                    'duration' => $leg['duration']['text'] ?? '',
                ]);
                return;
            }
            // Fall through to OSRM if Google fails for any reason
        }

        // ── OSRM fallback (free, no key needed) ───────────────────────────────
        // Coords are lng,lat order for OSRM
        $url = "https://router.project-osrm.org/route/v1/driving/"
             . "{$originLng},{$originLat};{$destLng},{$destLat}"
             . "?overview=full&geometries=geojson";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'OCSApp/1.0',
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response ?: '', true);

        if (empty($data['routes'][0])) {
            $this->json(['points' => [], 'distance' => '', 'duration' => '']);
            return;
        }

        $route       = $data['routes'][0];
        $points      = array_map(fn($c) => ['lat' => $c[1], 'lng' => $c[0]], $route['geometry']['coordinates'] ?? []);
        $distanceM   = $route['distance'] ?? 0;
        $durationS   = $route['duration'] ?? 0;
        $distanceKm  = round($distanceM / 1000, 1);
        $durationMin = (int)ceil($durationS / 60);

        $this->json([
            'points'   => $points,
            'distance' => $distanceKm < 1 ? round($distanceM) . ' m' : "{$distanceKm} km",
            'duration' => $durationMin < 60 ? "{$durationMin} min" : floor($durationMin / 60) . 'h ' . ($durationMin % 60) . 'min',
        ]);
    }

    private function decodePolyline(string $encoded): array
    {
        $index  = 0;
        $lat    = 0;
        $lng    = 0;
        $points = [];
        $len    = strlen($encoded);

        while ($index < $len) {
            $b = 0; $shift = 0; $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift  += 5;
            } while ($b >= 0x20);
            $lat += ($result & 1) ? ~($result >> 1) : ($result >> 1);

            $b = 0; $shift = 0; $result = 0;
            do {
                $b = ord($encoded[$index++]) - 63;
                $result |= ($b & 0x1f) << $shift;
                $shift  += 5;
            } while ($b >= 0x20);
            $lng += ($result & 1) ? ~($result >> 1) : ($result >> 1);

            $points[] = ['lat' => round($lat / 1e5, 6), 'lng' => round($lng / 1e5, 6)];
        }

        return $points;
    }

    private function fullAvatarUrl(string $path): string
    {
        if (str_starts_with($path, 'http')) return $path;
        return 'https://ocsapp.ca/' . ltrim($path, '/');
    }

    private function driverApplicationId(int $userId): ?int
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM driver_applications WHERE user_id = ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }

    private function driverProfile(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT u.id, u.first_name, u.last_name, u.email, u.phone,
                    u.avatar as photo_url,
                    (SELECT COUNT(*) FROM driver_certificates WHERE driver_id = u.id) as cert_count,
                    COALESCE(da.bgcheck_status, 'not_requested') as bgcheck_status,
                    COALESCE(da.city, '') as zone,
                    (SELECT COUNT(*) FROM orders WHERE driver_id = u.id AND status = 'delivered') as total_deliveries,
                    (SELECT ROUND(AVG(rating), 1) FROM driver_ratings WHERE driver_id = u.id) as avg_rating
             FROM users u
             LEFT JOIN driver_applications da ON da.user_id = u.id
             WHERE u.id = ?
             LIMIT 1"
        );
        $stmt->execute([$userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        // Get stored online status from session cache or default offline
        $statusRow = $this->db->prepare(
            "SELECT driver_online FROM driver_api_tokens WHERE user_id = ? LIMIT 1"
        );
        $statusRow->execute([$userId]);
        $tokenRow = $statusRow->fetch(\PDO::FETCH_ASSOC);
        $onlineStatus = ($tokenRow && $tokenRow['driver_online']) ? 'online' : 'offline';

        $rating = $row['avg_rating'] !== null ? (float)$row['avg_rating'] : null;

        // Compliance summary
        $cdStmt = $this->db->prepare(
            "SELECT status, COUNT(*) as cnt FROM driver_compliance_docs
             WHERE application_id = (
                 SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1
             ) GROUP BY status"
        );
        $cdStmt->execute([$userId]);
        $cdCounts = array_fill_keys(['verified','uploaded','flagged','not_required','not_uploaded'], 0);
        foreach ($cdStmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            if (isset($cdCounts[$r['status']])) {
                $cdCounts[$r['status']] = (int)$r['cnt'];
            }
        }

        return [
            'id'               => (int)$row['id'],
            'first_name'       => $row['first_name'],
            'last_name'        => $row['last_name'],
            'email'            => $row['email'],
            'phone'            => $row['phone'] ?? '',
            'status'           => $onlineStatus,
            'zone'             => $row['zone'] ?: null,
            'photo_url'        => $row['photo_url'] ? $this->fullAvatarUrl($row['photo_url']) : null,
            'rating'           => $rating,
            'total_deliveries' => (int)$row['total_deliveries'],
            'is_certified'     => (int)$row['cert_count'] > 0,
            'bgcheck_clear'    => in_array($row['bgcheck_status'], ['verified', 'waived']),
            'compliance_summary' => [
                'total'        => 5,
                'verified'     => $cdCounts['verified'],
                'not_required' => $cdCounts['not_required'],
                'flagged'      => $cdCounts['flagged'],
                'pending'      => $cdCounts['uploaded'],
                'not_uploaded' => $cdCounts['not_uploaded'],
                'complete'     => $cdCounts['verified'] + $cdCounts['not_required'],
            ],
        ];
    }

    private function formatOrder(array $row): array
    {
        return [
            'id'                      => (int)$row['id'],
            'order_number'            => $row['order_number'] ?? '',
            'merchant_name'           => $row['merchant_name'] ?? '',
            'merchant_address'        => $row['merchant_address'] ?? '',
            'merchant_lat'            => (float)($row['merchant_lat'] ?? 0),
            'merchant_lng'            => (float)($row['merchant_lng'] ?? 0),
            'customer_name'           => $row['customer_name'] ?? '',
            'customer_address'        => $row['delivery_address'] ?? '',
            'customer_lat'            => (float)($row['delivery_lat'] ?? 0),
            'customer_lng'            => (float)($row['delivery_lng'] ?? 0),
            'driver_name'             => $row['driver_name'] ?? '',
            'driver_license'          => $row['driver_license'] ?? '',
            'distance_km'             => (float)($row['distance_km'] ?? 0),
            'payout'                  => (float)($row['driver_payout'] ?? 0),
            'status'                  => $row['driver_status'] ?: $row['status'],
            'notes'                   => $row['delivery_notes'] ?? null,
            'created_at'              => $row['created_at'] ?? '',
            'accept_deadline_seconds' => isset($row['accept_deadline_seconds'])
                                         ? (int)$row['accept_deadline_seconds'] : null,
        ];
    }

    // -------------------------------------------------------------------------
    // POST /api/orders/:id/photo
    // Driver uploads a proof-of-delivery photo for an order
    // -------------------------------------------------------------------------
    public function orderPhoto(int $id): void
    {
        $userId = $this->authenticate();

        if (empty($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            $this->error('No photo uploaded', 400);
        }

        $file     = $_FILES['photo'];
        $maxBytes = 10 * 1024 * 1024; // 10 MB
        if ($file['size'] > $maxBytes) {
            $this->error('Photo must be under 10 MB', 400);
        }

        $mime    = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$mime])) {
            $this->error('Only JPEG, PNG or WebP images are allowed', 400);
        }

        // Verify this driver owns the order
        $chk = $this->db->prepare(
            "SELECT id FROM orders WHERE id = ? AND driver_id = ? AND status IN ('out_for_delivery','delivered') LIMIT 1"
        );
        $chk->execute([$id, $userId]);
        if (!$chk->fetch()) {
            $this->error('Order not found', 404);
        }

        $ext      = $allowed[$mime];
        $filename = "proof_{$id}_{$userId}_" . time() . ".{$ext}";
        $destDir  = __DIR__ . '/../../../public/uploads/delivery/';
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }
        $destPath = $destDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $this->error('Failed to save photo', 500);
        }

        $relativePath = 'uploads/delivery/' . $filename;

        // Save to delivery_assignments for this order
        $this->db->prepare(
            "UPDATE delivery_assignments SET proof_of_delivery = ?, updated_at = NOW()
             WHERE order_id = ? AND driver_id = ?"
        )->execute([$relativePath, $id, $userId]);

        $this->json(['success' => true, 'photo_url' => $this->fullAvatarUrl($relativePath)]);
    }

    // -------------------------------------------------------------------------
    // GET /api/pickups/:id
    // Returns a single pickup (PO) assigned to this driver
    // -------------------------------------------------------------------------
    public function pickupDetail(int $id): void
    {
        $userId = $this->authenticate();

        $stmt = $this->db->prepare("
            SELECT po.id, po.po_number, po.total_amount, po.notes,
                   po.driver_assigned_at, po.status, po.driver_acceptance_status,
                   po.distribution_request_id,
                   s.company_name AS supplier_name,
                   CONCAT_WS(', ', s.address, s.city, s.province, s.postal_code) AS supplier_address,
                   s.phone AS supplier_phone,
                   s.latitude  AS supplier_lat,
                   s.longitude AS supplier_lng,
                   (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) AS item_count
            FROM purchase_orders po
            JOIN suppliers s ON s.id = po.supplier_id
            WHERE po.id = ? AND po.assigned_driver_id = ?
            LIMIT 1
        ");
        $stmt->execute([$id, $userId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) $this->error('Pickup not found', 404);

        // Load line items
        $iStmt = $this->db->prepare("
            SELECT poi.id, sp.product_name, poi.quantity_ordered, poi.unit_cost, poi.total_cost, poi.notes
            FROM purchase_order_items poi
            LEFT JOIN supplier_products sp ON sp.id = poi.product_id
            WHERE poi.purchase_order_id = ?
            ORDER BY poi.id ASC
        ");
        $iStmt->execute([$id]);
        $items = array_map(fn($i) => [
            'id'           => (int)$i['id'],
            'product_name' => $i['product_name'] ?? 'Unknown product',
            'quantity'     => (int)$i['quantity_ordered'],
            'unit_cost'    => (float)$i['unit_cost'],
            'total_cost'   => (float)$i['total_cost'],
            'notes'        => $i['notes'],
        ], $iStmt->fetchAll(\PDO::FETCH_ASSOC));

        $pickup = [
            'id'                   => (int)$row['id'],
            'po_number'            => $row['po_number'],
            'total_amount'         => (float)$row['total_amount'],
            'notes'                => $row['notes'],
            'status'               => $row['status'],
            'acceptance_status'    => $row['driver_acceptance_status'],
            'assigned_at'          => $row['driver_assigned_at'],
            'item_count'           => (int)$row['item_count'],
            'supplier_name'        => $row['supplier_name'],
            'supplier_address'     => $row['supplier_address'],
            'supplier_phone'       => $row['supplier_phone'],
            'supplier_lat'         => $row['supplier_lat'] !== null ? (float)$row['supplier_lat'] : null,
            'supplier_lng'         => $row['supplier_lng'] !== null ? (float)$row['supplier_lng'] : null,
            'distribution_request_id' => !empty($row['distribution_request_id']) ? (int)$row['distribution_request_id'] : null,
            'items'                => $items,
        ];

        $this->json(['pickup' => $pickup]);
    }

    private function generateToken(int $userId): string
    {
        $token = bin2hex(random_bytes(32));

        // Ensure table exists
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS driver_api_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(64) NOT NULL,
                driver_online TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                expires_at TIMESTAMP NULL,
                INDEX(token),
                INDEX(user_id)
            )"
        );
        // Add driver_online column if table already existed without it
        try {
            $this->db->exec("ALTER TABLE driver_api_tokens ADD COLUMN driver_online TINYINT(1) DEFAULT 0");
        } catch (\Exception $e) { /* column already exists */ }

        // Replace any existing token for this user
        $this->db->prepare(
            "DELETE FROM driver_api_tokens WHERE user_id = ?"
        )->execute([$userId]);

        $this->db->prepare(
            "INSERT INTO driver_api_tokens (user_id, token, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 30 DAY))"
        )->execute([$userId, $token]);

        return $token;
    }

    private function authenticate(): int
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        $token  = '';
        if (str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        }

        if (!$token) $this->error('Unauthorized', 401);

        $stmt = $this->db->prepare(
            "SELECT user_id FROM driver_api_tokens
             WHERE token = ? AND (expires_at IS NULL OR expires_at > NOW())
             LIMIT 1"
        );
        $stmt->execute([$token]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$row) $this->error('Unauthorized', 401);

        $userId = (int)$row['user_id'];

        // Passive presence tracking — update driver's last_seen_at
        \PresenceHelper::trackDriver($userId);

        return $userId;
    }

    private function jsonBody(): array
    {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }

    private function json(array $data, int $status = 200): never
    {
        // Discard any buffered HTML (from ob_start in index.php)
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        http_response_code($status);
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        echo json_encode($data);
        exit;
    }

    private function error(string $message, int $status): never
    {
        $this->json(['error' => $message], $status);
    }

    /**
     * Calculate driver net payout for a delivery.
     *
     * Rates (configurable via env):
     *   DRIVER_BASE_PAY      — minimum base pay per delivery (default $5.00)
     *   DRIVER_PER_KM_RATE   — bonus per km (default $0.50)
     *   DRIVER_PLATFORM_CUT  — platform commission 0–1 (default 0.20 = 20%)
     */
    private function calculatePayout(float $orderDeliveryFee, float $distanceKm): float
    {
        $basePay      = (float)(getenv('DRIVER_BASE_PAY')     ?: 5.00);
        $perKmRate    = (float)(getenv('DRIVER_PER_KM_RATE')  ?: 0.50);
        $platformCut  = (float)(getenv('DRIVER_PLATFORM_CUT') ?: 0.20);

        // Use whichever is higher: configured base or the order's own delivery_fee
        $base         = max($basePay, $orderDeliveryFee);
        $distanceBonus = round($distanceKm * $perKmRate, 2);
        $gross         = $base + $distanceBonus;
        $net           = round($gross * (1 - $platformCut), 2);

        return max($net, $basePay * (1 - $platformCut)); // floor at base minus commission
    }

    /**
     * Called when driver marks an order delivered via ODA.
     * - Inserts delivery_earnings if a delivery_assignment exists
     * - Sends buyer the "order delivered" email
     * - Sends admin in-app notification
     */
    private function onDeliveryCompleted(int $orderId, int $driverId): void
    {
        try {
            // Load full order (field aliases match EmailHelper expectations)
            $stmt = $this->db->prepare(
                "SELECT o.*,
                        u.first_name AS customer_first_name,
                        u.last_name  AS customer_last_name,
                        u.email      AS customer_email,
                        s.name       AS shop_name,
                        s.user_id    AS shop_user_id
                 FROM orders o
                 JOIN users u ON u.id = o.user_id
                 JOIN shops s ON s.id = o.shop_id
                 WHERE o.id = ? LIMIT 1"
            );
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$order) return;

            $payout = (float)$order['driver_payout'];

            // --- delivery_earnings record (only if delivery_assignment exists) ---
            $daStmt = $this->db->prepare(
                "SELECT id FROM delivery_assignments WHERE order_id = ? AND driver_id = ? LIMIT 1"
            );
            $daStmt->execute([$orderId, $driverId]);
            $daRow = $daStmt->fetch(\PDO::FETCH_ASSOC);

            if ($daRow) {
                $basePay     = (float)(getenv('DRIVER_BASE_PAY')    ?: 5.00);
                $platformCut = (float)(getenv('DRIVER_PLATFORM_CUT') ?: 0.20);
                $commission  = round($payout / (1 - $platformCut) * $platformCut, 2);
                $gross       = round($payout + $commission, 2);

                // Avoid duplicate earnings record
                $dupCheck = $this->db->prepare(
                    "SELECT id FROM delivery_earnings WHERE order_id = ? AND driver_id = ? LIMIT 1"
                );
                $dupCheck->execute([$orderId, $driverId]);
                if (!$dupCheck->fetch()) {
                    $this->db->prepare(
                        "INSERT INTO delivery_earnings
                         (driver_id, delivery_id, order_id, base_fee, total_earning, platform_commission, net_earning, payment_status)
                         VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
                    )->execute([$driverId, $daRow['id'], $orderId, $basePay, $gross, $commission, $payout]);
                }

                // Mark delivery_assignment delivered
                $this->db->prepare(
                    "UPDATE delivery_assignments SET status = 'delivered', delivered_at = NOW() WHERE id = ?"
                )->execute([$daRow['id']]);
            }

            // --- Buyer email: order delivered ---
            if (!empty($order['customer_email'])) {
                require_once __DIR__ . '/../../Helpers/EmailHelper.php';
                \App\Helpers\EmailHelper::sendBuyerOrderDelivered($order);
            }

            // --- Seller in-app notification: order delivered ---
            if (!empty($order['shop_user_id'])) {
                try {
                    $this->db->prepare(
                        "INSERT INTO user_notifications
                         (user_id, type, title, message, link, is_read, created_at)
                         VALUES (?, 'order', ?, ?, '/seller/orders', 0, NOW())"
                    )->execute([
                        $order['shop_user_id'],
                        '✅ Order Delivered — #' . $order['order_number'],
                        "Order #{$order['order_number']} was successfully delivered to the customer.",
                    ]);
                } catch (\Exception $e) { /* non-blocking */ }
            }

            // --- Admin in-app notification ---
            require_once __DIR__ . '/../../Helpers/NotificationHelper.php';
            \App\Helpers\NotificationHelper::add(
                'delivery',
                '✅ Order Delivered — #' . $order['order_number'],
                "Driver delivered order #{$order['order_number']} ({$order['shop_name']}) to {$order['customer_first_name']} {$order['customer_last_name']}.",
                ['link' => '/admin/orders/view?id=' . $orderId, 'icon' => 'check-circle', 'priority' => 'normal']
            );

            // --- Free up driver availability ---
            $this->db->prepare(
                "UPDATE driver_availability SET status = 'available', updated_at = NOW() WHERE driver_id = ?"
            )->execute([$driverId]);

        } catch (\Exception $e) {
            error_log('onDeliveryCompleted error: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Log a driver accept/decline event to driver_activity_log — non-fatal
    // -------------------------------------------------------------------------
    private function logDriverActivity(
        int    $driverId,
        string $action,
        string $orderType,
        ?int   $orderId         = null,
        ?int   $poId            = null,
        ?int   $distRequestId   = null,
        string $referenceNumber = '',
        string $reason          = ''
    ): void {
        try {
            $this->db->prepare("
                INSERT INTO driver_activity_log
                (driver_id, action, order_type, order_id, po_id, distribution_request_id, reference_number, reason, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ")->execute([
                $driverId, $action, $orderType,
                $orderId ?: null, $poId ?: null, $distRequestId ?: null,
                $referenceNumber, $reason
            ]);
        } catch (\Exception $e) {
            // Non-fatal — never block the main flow
            error_log('logDriverActivity error: ' . $e->getMessage());
        }
    }
}
