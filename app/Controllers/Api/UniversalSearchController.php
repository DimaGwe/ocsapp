<?php

namespace App\Controllers\Api;

require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

/**
 * Universal Search API
 * GET /admin/api/universal-search?q=...
 * Returns contacts grouped by type: buyers, sellers, drivers, suppliers, leads
 */
class UniversalSearchController
{
    private \PDO $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        header('Content-Type: application/json');

        if (!isset($_SESSION['user']) || !\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $this->db = \Database::getConnection();
    }

    public function search(): void
    {
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            echo json_encode(['results' => []]);
            exit;
        }

        $like = '%' . $q . '%';
        $results = [];

        // --- Buyers ---
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, phone, role, status, created_at
            FROM users
            WHERE role = 'buyer'
              AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?
                   OR CONCAT(first_name,' ',last_name) LIKE ?)
              AND status != 'inactive'
            ORDER BY last_login_at DESC
            LIMIT 4
        ");
        $stmt->execute([$like, $like, $like, $like, $like]);
        foreach ($stmt->fetchAll() as $row) {
            $results[] = [
                'type'     => 'buyer',
                'id'       => $row['id'],
                'name'     => trim($row['first_name'] . ' ' . $row['last_name']),
                'sub'      => $row['email'],
                'phone'    => $row['phone'] ?? null,
                'status'   => $row['status'],
                'url'      => '/admin/users/view?id=' . $row['id'],
            ];
        }

        // --- Sellers ---
        $stmt = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status,
                   sh.name AS shop_name
            FROM users u
            LEFT JOIN shops sh ON sh.seller_id = u.id
            WHERE u.role = 'seller'
              AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.email LIKE ?
                   OR u.phone LIKE ? OR sh.name LIKE ?
                   OR CONCAT(u.first_name,' ',u.last_name) LIKE ?)
            ORDER BY u.created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$like, $like, $like, $like, $like, $like]);
        foreach ($stmt->fetchAll() as $row) {
            $results[] = [
                'type'   => 'seller',
                'id'     => $row['id'],
                'name'   => $row['shop_name'] ?: trim($row['first_name'] . ' ' . $row['last_name']),
                'sub'    => $row['email'],
                'phone'  => $row['phone'] ?? null,
                'status' => $row['status'],
                'url'    => '/admin/sellers/view?id=' . $row['id'],
            ];
        }

        // --- Drivers ---
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, phone, status
            FROM users
            WHERE role = 'delivery'
              AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?
                   OR CONCAT(first_name,' ',last_name) LIKE ?)
            ORDER BY created_at DESC
            LIMIT 3
        ");
        $stmt->execute([$like, $like, $like, $like, $like]);
        foreach ($stmt->fetchAll() as $row) {
            $results[] = [
                'type'   => 'driver',
                'id'     => $row['id'],
                'name'   => trim($row['first_name'] . ' ' . $row['last_name']),
                'sub'    => $row['email'],
                'phone'  => $row['phone'] ?? null,
                'status' => $row['status'],
                'url'    => '/admin/delivery/view?id=' . $row['id'],
            ];
        }

        // --- Suppliers ---
        $stmt = $this->db->prepare("
            SELECT id, name, company_name, contact_person, email, phone, status, supplier_code
            FROM suppliers
            WHERE deleted_at IS NULL
              AND (name LIKE ? OR company_name LIKE ? OR contact_person LIKE ?
                   OR email LIKE ? OR phone LIKE ? OR supplier_code LIKE ?)
            ORDER BY created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$like, $like, $like, $like, $like, $like]);
        foreach ($stmt->fetchAll() as $row) {
            $results[] = [
                'type'   => 'supplier',
                'id'     => $row['id'],
                'name'   => $row['company_name'] ?: $row['name'],
                'sub'    => $row['email'],
                'phone'  => $row['phone'] ?? null,
                'status' => $row['status'],
                'badge'  => $row['supplier_code'],
                'url'    => '/admin/suppliers/view?id=' . $row['id'],
            ];
        }

        // --- Leads ---
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, phone, company_name, interest_type, status
            FROM leads
            WHERE status NOT IN ('won','lost')
              AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?
                   OR phone LIKE ? OR company_name LIKE ?
                   OR CONCAT(first_name,' ',last_name) LIKE ?)
            ORDER BY created_at DESC
            LIMIT 4
        ");
        $stmt->execute([$like, $like, $like, $like, $like, $like]);
        foreach ($stmt->fetchAll() as $row) {
            $results[] = [
                'type'   => 'lead',
                'id'     => $row['id'],
                'name'   => trim($row['first_name'] . ' ' . $row['last_name']),
                'sub'    => $row['company_name'] ?: $row['email'],
                'phone'  => $row['phone'] ?? null,
                'status' => $row['status'],
                'badge'  => $row['interest_type'],
                'url'    => '/admin/leads/view?id=' . $row['id'],
            ];
        }

        // --- Orders (search by ID) ---
        if (is_numeric($q)) {
            $stmt = $this->db->prepare("
                SELECT o.id, o.status, o.total, o.created_at,
                       u.first_name, u.last_name, u.email
                FROM orders o
                LEFT JOIN users u ON u.id = o.user_id
                WHERE o.id = ?
                LIMIT 1
            ");
            $stmt->execute([(int)$q]);
            if ($row = $stmt->fetch()) {
                $results[] = [
                    'type'   => 'order',
                    'id'     => $row['id'],
                    'name'   => 'Order #' . $row['id'],
                    'sub'    => trim($row['first_name'] . ' ' . $row['last_name']) . ' — $' . number_format($row['total'], 2),
                    'phone'  => null,
                    'status' => $row['status'],
                    'url'    => '/admin/orders/view?id=' . $row['id'],
                ];
            }
        }

        echo json_encode(['results' => $results]);
    }

    /**
     * Get full contact card data for slide-in panel
     * GET /admin/api/contact-card?type=buyer&id=5
     */
    public function contactCard(): void
    {
        $type = $_GET['type'] ?? '';
        $id   = (int)($_GET['id'] ?? 0);

        if (!$id || !in_array($type, ['buyer','seller','driver','supplier','lead'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid parameters']);
            exit;
        }

        $card = match($type) {
            'buyer'    => $this->getBuyerCard($id),
            'seller'   => $this->getSellerCard($id),
            'driver'   => $this->getDriverCard($id),
            'supplier' => $this->getSupplierCard($id),
            'lead'     => $this->getLeadCard($id),
            default    => null,
        };

        if (!$card) {
            http_response_code(404);
            echo json_encode(['error' => 'Not found']);
            exit;
        }

        echo json_encode(['card' => $card]);
    }

    private function getBuyerCard(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email, phone, status, created_at, last_login_at FROM users WHERE id = ? AND role = 'buyer' LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) return null;

        // Recent orders
        $stmt = $this->db->prepare("SELECT id, status, total, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3");
        $stmt->execute([$id]);
        $orders = $stmt->fetchAll();

        // Recent activities from any ticket or lead
        $stmt = $this->db->prepare("
            SELECT la.activity_type, la.description, la.created_at, u.first_name AS agent
            FROM lead_activities la
            LEFT JOIN leads l ON l.id = la.lead_id
            LEFT JOIN users u ON u.id = la.created_by
            WHERE l.email = ?
            ORDER BY la.created_at DESC LIMIT 3
        ");
        $stmt->execute([$user['email']]);
        $activities = $stmt->fetchAll();

        return [
            'type'       => 'buyer',
            'id'         => $user['id'],
            'name'       => trim($user['first_name'] . ' ' . $user['last_name']),
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'status'     => $user['status'],
            'member_since' => date('M Y', strtotime($user['created_at'])),
            'last_login' => $user['last_login_at'] ? date('M j, Y', strtotime($user['last_login_at'])) : null,
            'profile_url'=> '/admin/users/view?id=' . $id,
            'orders'     => array_map(fn($o) => [
                'id'     => $o['id'],
                'status' => $o['status'],
                'total'  => '$' . number_format($o['total'], 2),
                'date'   => date('M j', strtotime($o['created_at'])),
                'url'    => '/admin/orders/view?id=' . $o['id'],
            ], $orders),
            'activities' => array_map(fn($a) => [
                'type'        => $a['activity_type'],
                'description' => $a['description'],
                'date'        => date('M j', strtotime($a['created_at'])),
                'agent'       => $a['agent'],
            ], $activities),
        ];
    }

    private function getSellerCard(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.created_at, sh.name AS shop_name, sh.id AS shop_id FROM users u LEFT JOIN shops sh ON sh.seller_id = u.id WHERE u.id = ? AND u.role = 'seller' LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) return null;

        $stmt = $this->db->prepare("SELECT COUNT(*) as cnt, SUM(total) as gmv FROM orders WHERE shop_id = ?");
        $stmt->execute([$user['shop_id']]);
        $stats = $stmt->fetch();

        return [
            'type'       => 'seller',
            'id'         => $user['id'],
            'name'       => $user['shop_name'] ?: trim($user['first_name'] . ' ' . $user['last_name']),
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'status'     => $user['status'],
            'member_since' => date('M Y', strtotime($user['created_at'])),
            'profile_url'=> '/admin/sellers/view?id=' . $id,
            'stats'      => [
                'orders' => (int)($stats['cnt'] ?? 0),
                'gmv'    => '$' . number_format($stats['gmv'] ?? 0, 2),
            ],
            'activities' => [],
        ];
    }

    private function getDriverCard(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email, phone, status, created_at FROM users WHERE id = ? AND role = 'delivery' LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) return null;

        return [
            'type'       => 'driver',
            'id'         => $user['id'],
            'name'       => trim($user['first_name'] . ' ' . $user['last_name']),
            'email'      => $user['email'],
            'phone'      => $user['phone'],
            'status'     => $user['status'],
            'member_since' => date('M Y', strtotime($user['created_at'])),
            'profile_url'=> '/admin/delivery/view?id=' . $id,
            'activities' => [],
        ];
    }

    private function getSupplierCard(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, name, company_name, contact_person, email, phone, status, supplier_code, created_at FROM suppliers WHERE id = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$id]);
        $sup = $stmt->fetch();
        if (!$sup) return null;

        return [
            'type'         => 'supplier',
            'id'           => $sup['id'],
            'name'         => $sup['company_name'] ?: $sup['name'],
            'email'        => $sup['email'],
            'phone'        => $sup['phone'],
            'status'       => $sup['status'],
            'badge'        => $sup['supplier_code'],
            'contact_person' => $sup['contact_person'],
            'member_since' => date('M Y', strtotime($sup['created_at'])),
            'profile_url'  => '/admin/suppliers/view?id=' . $id,
            'activities'   => [],
        ];
    }

    private function getLeadCard(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email, phone, company_name, interest_type, status, created_at FROM leads WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $lead = $stmt->fetch();
        if (!$lead) return null;

        $stmt = $this->db->prepare("SELECT la.activity_type, la.description, la.created_at, u.first_name AS agent FROM lead_activities la LEFT JOIN users u ON u.id = la.created_by WHERE la.lead_id = ? ORDER BY la.created_at DESC LIMIT 3");
        $stmt->execute([$id]);
        $activities = $stmt->fetchAll();

        return [
            'type'       => 'lead',
            'id'         => $lead['id'],
            'name'       => trim($lead['first_name'] . ' ' . $lead['last_name']),
            'email'      => $lead['email'],
            'phone'      => $lead['phone'],
            'status'     => $lead['status'],
            'badge'      => $lead['interest_type'],
            'company'    => $lead['company_name'],
            'member_since' => date('M Y', strtotime($lead['created_at'])),
            'profile_url'=> '/admin/leads/view?id=' . $id,
            'activities' => array_map(fn($a) => [
                'type'        => $a['activity_type'],
                'description' => $a['description'],
                'date'        => date('M j', strtotime($a['created_at'])),
                'agent'       => $a['agent'],
            ], $activities),
        ];
    }
}
