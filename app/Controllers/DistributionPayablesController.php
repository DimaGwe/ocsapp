<?php

namespace App\Controllers;

/**
 * DistributionPayablesController
 * Shows the business/distribution portal all outstanding and paid amounts
 * for their distribution requests.
 */
class DistributionPayablesController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    private function requireBusiness(): int
    {
        if (!isset($_SESSION['user']['role'], $_SESSION['business']['id'])) {
            redirect('distribution/login');
            exit;
        }
        if (!in_array($_SESSION['user']['role'], ['business', 'admin', 'super_admin'], true)) {
            redirect('distribution/login');
            exit;
        }
        return (int)$_SESSION['business']['id'];
    }

    // -------------------------------------------------------------------------
    // index() — GET /distribution/payables
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $businessId = $this->requireBusiness();

        $statusFilter  = trim($_GET['status']   ?? '');
        $page    = max(1, (int)($_GET['page']   ?? 1));
        $perPage = 20;

        $allowedStatuses = ['pending','paid','partially_paid','overdue'];

        // Build WHERE
        $where  = ['dr.business_profile_id = ?', 'dr.status NOT IN (\'draft\',\'cancelled\',\'expired\')'];
        $params = [$businessId];

        if (in_array($statusFilter, ['paid', 'pending', 'refunded'], true)) {
            $where[]  = 'dr.payment_status = ?';
            $params[] = $statusFilter;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        // Count
        $countStmt = $this->db->prepare("SELECT COUNT(*) FROM distribution_requests dr $whereSql");
        $countStmt->execute($params);
        $total      = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        // Fetch DRs with payment info including refunds
        $stmt = $this->db->prepare("
            SELECT dr.id, dr.request_number, dr.status, dr.payment_status,
                   dr.total_amount, dr.payment_method, dr.payment_reference,
                   dr.created_at, dr.updated_at,
                   (SELECT COALESCE(SUM(dp.amount), 0) FROM distribution_payments dp
                    WHERE dp.distribution_request_id = dr.id AND dp.status = 'completed') AS amount_paid,
                   (SELECT COALESCE(SUM(dp.amount), 0) FROM distribution_payments dp
                    WHERE dp.distribution_request_id = dr.id AND dp.status = 'refunded') AS amount_refunded
            FROM distribution_requests dr
            $whereSql
            ORDER BY dr.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $payables = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Summary stats
        $statsStmt = $this->db->prepare("
            SELECT
                COUNT(*) AS total_requests,
                COALESCE(SUM(dr.total_amount), 0) AS total_ordered,
                COALESCE(SUM(
                    (SELECT COALESCE(SUM(dp.amount), 0) FROM distribution_payments dp
                     WHERE dp.distribution_request_id = dr.id AND dp.status = 'completed')
                ), 0) AS total_paid,
                COALESCE(SUM(
                    (SELECT COALESCE(SUM(dp.amount), 0) FROM distribution_payments dp
                     WHERE dp.distribution_request_id = dr.id AND dp.status = 'refunded')
                ), 0) AS total_refunded,
                COUNT(CASE WHEN dr.payment_status = 'pending' AND dr.status NOT IN ('draft','cancelled','expired') THEN 1 END) AS pending_count
            FROM distribution_requests dr
            WHERE dr.business_profile_id = ?
        ");
        $statsStmt->execute([$businessId]);
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);
        $stats['outstanding'] = max(0, ($stats['total_ordered'] ?? 0) - ($stats['total_paid'] ?? 0));

        view('distribution.payables', [
            'payables'     => $payables,
            'stats'        => $stats,
            'statusFilter' => $statusFilter,
            'page'         => $page,
            'perPage'      => $perPage,
            'total'        => $total,
            'totalPages'   => $totalPages,
            'pageTitle'    => (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Paiements dus' : 'Payables',
            'currentPage'  => 'payables',
        ]);
    }
}
