<?php

namespace App\Controllers;

/**
 * SupplierReceivablesController
 * Shows a supplier what they are owed — invoices issued to admin and payment history.
 */
class SupplierReceivablesController
{
    private function checkAuth(): int
    {
        if (!isset($_SESSION['supplier_id'])) {
            redirect(url('supplier/login'));
        }
        return (int)$_SESSION['supplier_id'];
    }

    // -------------------------------------------------------------------------
    // index() — GET /supplier/receivables
    // -------------------------------------------------------------------------
    public function index(): void
    {
        $supplierId = $this->checkAuth();
        $db = \Database::getConnection();

        $statusFilter = trim($_GET['status'] ?? '');
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;

        $allowedStatuses = ['draft', 'sent', 'partial', 'paid', 'overdue', 'cancelled'];

        // Build WHERE
        $where  = ['si.supplier_id = ?'];
        $params = [$supplierId];

        if ($statusFilter !== '' && in_array($statusFilter, $allowedStatuses, true)) {
            $where[]  = 'si.status = ?';
            $params[] = $statusFilter;
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        // Count
        $countStmt = $db->prepare("SELECT COUNT(*) FROM supplier_invoices si $whereSql");
        $countStmt->execute($params);
        $total      = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * $perPage;

        // Invoices
        $stmt = $db->prepare("
            SELECT si.*,
                   po.po_number,
                   po.so_number
            FROM supplier_invoices si
            LEFT JOIN purchase_orders po ON si.po_id = po.id
            $whereSql
            ORDER BY si.created_at DESC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Summary stats for this supplier
        $statsStmt = $db->prepare("
            SELECT
                COALESCE(SUM(total_amount), 0)  AS total_invoiced,
                COALESCE(SUM(amount_paid), 0)   AS total_received,
                COALESCE(SUM(balance_due), 0)   AS total_outstanding,
                COUNT(CASE WHEN status = 'paid' THEN 1 END)                         AS paid_count,
                COUNT(CASE WHEN status IN ('sent','partial','overdue') THEN 1 END)  AS unpaid_count,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END)                      AS overdue_count
            FROM supplier_invoices
            WHERE supplier_id = ?
        ");
        $statsStmt->execute([$supplierId]);
        $stats = $statsStmt->fetch(\PDO::FETCH_ASSOC);

        // Recent payments received
        $paymentsStmt = $db->prepare("
            SELECT sp.id, sp.payment_date, sp.payment_method, sp.reference_number,
                   sip.amount_applied, si.invoice_number
            FROM supplier_payments sp
            JOIN supplier_invoice_payments sip ON sp.id = sip.payment_id
            JOIN supplier_invoices si ON sip.invoice_id = si.id
            WHERE sp.supplier_id = ?
            ORDER BY sp.payment_date DESC
            LIMIT 10
        ");
        $paymentsStmt->execute([$supplierId]);
        $recentPayments = $paymentsStmt->fetchAll(\PDO::FETCH_ASSOC);

        view('supplier.receivables', [
            'invoices'       => $invoices,
            'stats'          => $stats,
            'recentPayments' => $recentPayments,
            'statusFilter'   => $statusFilter,
            'page'           => $page,
            'perPage'        => $perPage,
            'total'          => $total,
            'totalPages'     => $totalPages,
            'pageTitle'      => 'Receivables',
        ]);
    }
}
