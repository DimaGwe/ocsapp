<?php

namespace App\Controllers;

class AdminDriverActivityController
{
    private $db;

    public function __construct()
    {
        requireAdminAuth();
        $this->db = \Database::getConnection();
    }

    public function index(): void
    {
        $driverId  = (int)($_GET['driver_id'] ?? 0);
        $action    = $_GET['action'] ?? '';
        $orderType = $_GET['order_type'] ?? '';
        $dateFrom  = $_GET['date_from'] ?? '';
        $dateTo    = $_GET['date_to'] ?? '';
        $search    = trim($_GET['search'] ?? '');
        $page      = max(1, (int)($_GET['page'] ?? 1));
        $perPage   = 25;
        $offset    = ($page - 1) * $perPage;

        // Build WHERE clause
        $where  = ['1=1'];
        $params = [];

        if ($driverId) {
            $where[] = 'dal.driver_id = ?';
            $params[] = $driverId;
        }
        if ($action && in_array($action, ['accepted', 'declined'], true)) {
            $where[] = 'dal.action = ?';
            $params[] = $action;
        }
        if ($orderType && in_array($orderType, ['marketplace', 'distribution'], true)) {
            $where[] = 'dal.order_type = ?';
            $params[] = $orderType;
        }
        if ($dateFrom) {
            $where[] = 'DATE(dal.created_at) >= ?';
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'DATE(dal.created_at) <= ?';
            $params[] = $dateTo;
        }
        if ($search) {
            $where[] = 'dal.reference_number LIKE ?';
            $params[] = '%' . $search . '%';
        }

        $whereSQL = implode(' AND ', $where);

        // Total row count for pagination
        $countStmt = $this->db->prepare("
            SELECT COUNT(*) FROM driver_activity_log dal WHERE {$whereSQL}
        ");
        $countStmt->execute($params);
        $total      = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));

        // Paginated rows
        $stmt = $this->db->prepare("
            SELECT dal.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS driver_name,
                   u.email AS driver_email
            FROM driver_activity_log dal
            JOIN users u ON u.id = dal.driver_id
            WHERE {$whereSQL}
            ORDER BY dal.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Summary cards — always unfiltered totals
        $stats = $this->db->query("
            SELECT
                SUM(action = 'accepted')  AS total_accepted,
                SUM(action = 'declined')  AS total_declined,
                SUM(action = 'accepted'
                    AND MONTH(created_at) = MONTH(NOW())
                    AND YEAR(created_at)  = YEAR(NOW()))  AS month_accepted,
                SUM(action = 'declined'
                    AND MONTH(created_at) = MONTH(NOW())
                    AND YEAR(created_at)  = YEAR(NOW()))  AS month_declined
            FROM driver_activity_log
        ")->fetch(\PDO::FETCH_ASSOC);

        $totalActions         = (int)($stats['total_accepted'] ?? 0) + (int)($stats['total_declined'] ?? 0);
        $stats['decline_rate'] = $totalActions > 0
            ? round((int)$stats['total_declined'] / $totalActions * 100, 1)
            : 0;

        // Per-driver summary table (all time, unfiltered)
        $driverStats = $this->db->query("
            SELECT
                dal.driver_id,
                CONCAT(u.first_name, ' ', u.last_name) AS driver_name,
                SUM(dal.action = 'accepted') AS accepts,
                SUM(dal.action = 'declined') AS declines,
                MAX(dal.created_at)          AS last_active
            FROM driver_activity_log dal
            JOIN users u ON u.id = dal.driver_id
            GROUP BY dal.driver_id, driver_name
            ORDER BY declines DESC, driver_name ASC
        ")->fetchAll(\PDO::FETCH_ASSOC);

        // Dropdown: all active delivery drivers
        $drivers = $this->db->query("
            SELECT id, CONCAT(first_name, ' ', last_name) AS name
            FROM users
            WHERE role IN ('driver', 'delivery') AND status = 'active'
            ORDER BY first_name, last_name
        ")->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.driver-activity.index', compact(
            'rows', 'stats', 'driverStats', 'drivers',
            'total', 'totalPages', 'page', 'perPage',
            'driverId', 'action', 'orderType', 'dateFrom', 'dateTo', 'search'
        ));
    }

    public function export(): void
    {
        requireAdminAuth();

        $driverId  = (int)($_GET['driver_id'] ?? 0);
        $action    = $_GET['action'] ?? '';
        $orderType = $_GET['order_type'] ?? '';
        $dateFrom  = $_GET['date_from'] ?? '';
        $dateTo    = $_GET['date_to'] ?? '';

        $where  = ['1=1'];
        $params = [];

        if ($driverId) {
            $where[] = 'dal.driver_id = ?';
            $params[] = $driverId;
        }
        if ($action && in_array($action, ['accepted', 'declined'], true)) {
            $where[] = 'dal.action = ?';
            $params[] = $action;
        }
        if ($orderType && in_array($orderType, ['marketplace', 'distribution'], true)) {
            $where[] = 'dal.order_type = ?';
            $params[] = $orderType;
        }
        if ($dateFrom) {
            $where[] = 'DATE(dal.created_at) >= ?';
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = 'DATE(dal.created_at) <= ?';
            $params[] = $dateTo;
        }

        $whereSQL = implode(' AND ', $where);

        $stmt = $this->db->prepare("
            SELECT
                dal.created_at,
                CONCAT(u.first_name, ' ', u.last_name) AS driver,
                u.email,
                dal.action,
                dal.order_type,
                dal.reference_number,
                dal.reason
            FROM driver_activity_log dal
            JOIN users u ON u.id = dal.driver_id
            WHERE {$whereSQL}
            ORDER BY dal.created_at DESC
        ");
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="driver-activity-' . date('Y-m-d') . '.csv"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM for Excel compatibility

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Date', 'Driver', 'Email', 'Action', 'Type', 'Reference', 'Reason']);
        foreach ($rows as $r) {
            fputcsv($out, $r);
        }
        fclose($out);
    }
}
