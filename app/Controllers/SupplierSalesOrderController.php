<?php

namespace App\Controllers;

/**
 * SupplierSalesOrderController
 * Sales Orders = accepted Purchase Orders.
 * Gives suppliers a dedicated SO view with SO numbers.
 */
class SupplierSalesOrderController
{
    private function checkAuth(): int
    {
        if (!isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/login'));
        }
        return (int)$_SESSION['supplier_id'];
    }

    // -------------------------------------------------------------------------
    // index() — GET /supplier/sales-orders
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $supplierId = $this->checkAuth();

        $db = \Database::getConnection();

        $search   = trim($_GET['search']  ?? '');
        $status   = trim($_GET['status']  ?? '');
        $page     = max(1, (int)($_GET['page'] ?? 1));
        $perPage  = 20;

        // Allowed status filter values
        $allowedStatuses = ['accepted', 'preparing', 'ready_for_pickup', 'picked_up', 'completed', 'declined'];

        // Build WHERE
        $where  = ['po.supplier_id = ?', "po.so_number IS NOT NULL"];
        $params = [$supplierId];

        if ($search !== '') {
            $where[]  = '(po.so_number LIKE ? OR po.po_number LIKE ?)';
            $like     = '%' . $search . '%';
            $params[] = $like;
            $params[] = $like;
        }
        if ($status !== '' && in_array($status, $allowedStatuses, true)) {
            $where[]  = 'po.status = ?';
            $params[] = $status;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        // Count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM purchase_orders po $whereSql");
        $countStmt->execute($params);
        $total      = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        // Fetch
        $stmt = $db->prepare("
            SELECT po.id, po.so_number, po.po_number, po.status,
                   po.order_date, po.supplier_accepted_at, po.updated_at,
                   po.distribution_request_id,
                   dr.request_number,
                   (SELECT COUNT(*) FROM purchase_order_items WHERE purchase_order_id = po.id) AS item_count,
                   (SELECT COALESCE(SUM(unit_cost * quantity_ordered), 0) FROM purchase_order_items WHERE purchase_order_id = po.id) AS total_amount
            FROM purchase_orders po
            LEFT JOIN distribution_requests dr ON po.distribution_request_id = dr.id
            $whereSql
            ORDER BY po.supplier_accepted_at DESC, po.id DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $salesOrders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Summary stats
        $statsStmt = $db->prepare("
            SELECT
                COUNT(*) AS total_so,
                COUNT(CASE WHEN status IN ('accepted','preparing') THEN 1 END) AS in_progress,
                COUNT(CASE WHEN status IN ('ready_for_pickup','picked_up') THEN 1 END) AS fulfilling,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) AS completed,
                COALESCE(SUM(
                    (SELECT COALESCE(SUM(unit_cost * quantity_ordered), 0) FROM purchase_order_items WHERE purchase_order_id = po.id)
                ), 0) AS total_value
            FROM purchase_orders po
            WHERE po.supplier_id = ? AND po.so_number IS NOT NULL
        ");
        $statsStmt->execute([$supplierId]);
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        view('supplier.sales-orders', [
            'salesOrders' => $salesOrders,
            'stats'       => $stats,
            'search'      => $search,
            'status'      => $status,
            'page'        => $page,
            'perPage'     => $perPage,
            'total'       => $total,
            'totalPages'  => $totalPages,
            'pageTitle'   => 'Sales Orders',
        ]);
    }
}
