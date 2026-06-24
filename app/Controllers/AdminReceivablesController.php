<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;

/**
 * AdminReceivablesController
 * Tracks all inbound payments: Distribution B2B and Marketplace orders.
 */
class AdminReceivablesController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    // -------------------------------------------------------------------------
    // index() — GET /admin/receivables
    // -------------------------------------------------------------------------
    public function index(): void
    {
        AuthMiddleware::handle('admin');

        // --- Filters ---
        $type     = sanitize($_GET['type']      ?? 'all');
        $method   = sanitize($_GET['method']    ?? '');
        $status   = sanitize($_GET['status']    ?? '');
        $search   = sanitize($_GET['search']    ?? '');
        $dateFrom = sanitize($_GET['date_from'] ?? '');
        $dateTo   = sanitize($_GET['date_to']   ?? '');
        $page     = max(1, (int)($_GET['page']    ?? 1));
        $perPage  = max(1, (int)($_GET['per_page'] ?? 25));
        $offset   = ($page - 1) * $perPage;

        // --- Build UNION SQL ---
        [$unionSql, $unionParams] = $this->buildUnionSql($type);

        // --- Wrapper filters ---
        [$whereSql, $whereParams] = $this->buildWhereClause($method, $status, $search, $dateFrom, $dateTo);

        // --- Count ---
        $countSql = "SELECT COUNT(*) FROM ({$unionSql}) AS u {$whereSql}";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute(array_merge($unionParams, $whereParams));
        $total = (int)$countStmt->fetchColumn();
        $totalPages = max(1, (int)ceil($total / $perPage));

        // --- Rows ---
        $rowsSql = "SELECT * FROM ({$unionSql}) AS u {$whereSql} ORDER BY paid_at DESC, source_id DESC LIMIT ? OFFSET ?";
        $rowsStmt = $this->db->prepare($rowsSql);
        $rowsStmt->execute(array_merge($unionParams, $whereParams, [$perPage, $offset]));
        $rows = $rowsStmt->fetchAll(\PDO::FETCH_ASSOC);

        // --- Summary stats (unfiltered) ---
        $stats = $this->buildStats();

        $filters = compact('type', 'method', 'status', 'search', 'dateFrom', 'dateTo', 'perPage');

        view('admin.receivables.index', compact('rows', 'stats', 'filters', 'page', 'totalPages', 'total'));
    }

    // -------------------------------------------------------------------------
    // markPaid() — POST /admin/receivables/mark-paid
    // -------------------------------------------------------------------------
    public function markPaid(): void
    {
        AuthMiddleware::handle('admin');
        verifyCsrfToken();

        $sourceId         = (int)($_POST['source_id'] ?? 0);
        $paymentMethod    = sanitize($_POST['payment_method']    ?? '');
        $paymentReference = sanitize($_POST['payment_reference'] ?? '');
        $paidAt           = sanitize($_POST['paid_at']           ?? '');

        if (!$sourceId || !in_array($paymentMethod, ['bank_transfer', 'interac'], true)) {
            setFlash('error', 'Invalid parameters.');
            redirect('admin/receivables');
            return;
        }

        if (!$paidAt) {
            $paidAt = date('Y-m-d H:i:s');
        } else {
            // Normalise datetime-local value (YYYY-MM-DDTHH:MM) to MySQL
            $paidAt = str_replace('T', ' ', $paidAt) . ':00';
        }

        try {
            // Fetch old status for history log
            $check = $this->db->prepare("SELECT status FROM distribution_requests WHERE id = ? AND status = 'awaiting_payment' LIMIT 1");
            $check->execute([$sourceId]);
            $row = $check->fetch(\PDO::FETCH_ASSOC);

            if (!$row) {
                setFlash('error', 'Request not found or not in awaiting_payment status.');
                redirect('admin/receivables');
                return;
            }

            $stmt = $this->db->prepare("
                UPDATE distribution_requests
                SET status             = 'paid',
                    payment_status     = 'paid',
                    payment_method     = ?,
                    payment_reference  = ?,
                    payment_intent_id  = ?,
                    paid_at            = ?,
                    payment_link_token = NULL,
                    updated_at         = NOW()
                WHERE id = ? AND status = 'awaiting_payment'
            ");
            $stmt->execute([
                $paymentMethod,
                $paymentReference,
                $paymentReference, // mirror into payment_intent_id field
                $paidAt,
                $sourceId,
            ]);

            // Log to status history
            $this->logStatusChange($sourceId, 'awaiting_payment', 'paid', "Manually marked paid via {$paymentMethod}. Ref: {$paymentReference}");

            // Check if all POs are ready — if so, trigger driver auto-assignment
            $this->maybeAutoAssignDriver($sourceId);

            setFlash('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            error_log('AdminReceivablesController::markPaid error: ' . $e->getMessage());
            setFlash('error', 'Failed to record payment. Please try again.');
        }

        redirect('admin/receivables');
    }

    // -------------------------------------------------------------------------
    // markRefunded() — POST /admin/receivables/mark-refunded
    // -------------------------------------------------------------------------
    public function markRefunded(): void
    {
        AuthMiddleware::handle('admin');
        verifyCsrfToken();

        $revenueType     = sanitize($_POST['revenue_type']     ?? '');
        $sourceId        = (int)($_POST['source_id']           ?? 0);
        $refundReference = sanitize($_POST['refund_reference'] ?? '');

        if (!$sourceId || !in_array($revenueType, ['distribution', 'marketplace'], true)) {
            setFlash('error', 'Invalid parameters.');
            redirect('admin/receivables');
            return;
        }

        try {
            if ($revenueType === 'distribution') {
                $check = $this->db->prepare("SELECT payment_status FROM distribution_requests WHERE id = ? LIMIT 1");
                $check->execute([$sourceId]);
                $oldStatus = (string)$check->fetchColumn();

                $stmt = $this->db->prepare("
                    UPDATE distribution_requests
                    SET payment_status = 'refunded',
                        updated_at     = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$sourceId]);

                $notes = 'Marked as refunded by admin.' . ($refundReference ? " Ref: {$refundReference}" : '');
                $this->logStatusChange($sourceId, $oldStatus, 'refunded', $notes);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE orders
                    SET payment_status = 'refunded',
                        status         = 'refunded',
                        updated_at     = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$sourceId]);
            }

            setFlash('success', 'Record marked as refunded.');
        } catch (\Exception $e) {
            error_log('AdminReceivablesController::markRefunded error: ' . $e->getMessage());
            setFlash('error', 'Failed to mark as refunded.');
        }

        redirect('admin/receivables');
    }

    // -------------------------------------------------------------------------
    // export() — GET /admin/receivables/export
    // -------------------------------------------------------------------------
    public function export(): void
    {
        AuthMiddleware::handle('admin');

        $type     = sanitize($_GET['type']      ?? 'all');
        $method   = sanitize($_GET['method']    ?? '');
        $status   = sanitize($_GET['status']    ?? '');
        $search   = sanitize($_GET['search']    ?? '');
        $dateFrom = sanitize($_GET['date_from'] ?? '');
        $dateTo   = sanitize($_GET['date_to']   ?? '');

        [$unionSql, $unionParams] = $this->buildUnionSql($type);
        [$whereSql, $whereParams] = $this->buildWhereClause($method, $status, $search, $dateFrom, $dateTo);

        $rowsSql = "SELECT * FROM ({$unionSql}) AS u {$whereSql} ORDER BY paid_at DESC, source_id DESC";
        $stmt = $this->db->prepare($rowsSql);
        $stmt->execute(array_merge($unionParams, $whereParams));
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $filename = 'receivables-' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, [
            'Date', 'Type', 'Reference', 'Invoice', 'Payer', 'Email',
            'Method', 'Transaction Ref', 'Subtotal', 'GST', 'QST',
            'Delivery Fee', 'Service Fee', 'Handling', 'Tip', 'Total', 'Status'
        ]);

        foreach ($rows as $row) {
            fputcsv($out, [
                $row['paid_at'] ? date('Y-m-d H:i', strtotime($row['paid_at'])) : '',
                ucfirst($row['revenue_type']),
                $row['reference_number'],
                $row['invoice_number'] ?? '',
                $row['payer_name'],
                $row['payer_email'],
                $row['payment_method'] ?? '',
                $row['transaction_ref'] ?? '',
                number_format((float)$row['subtotal'], 2, '.', ''),
                number_format((float)$row['gst_amount'], 2, '.', ''),
                number_format((float)$row['qst_amount'], 2, '.', ''),
                number_format((float)$row['delivery_fee'], 2, '.', ''),
                number_format((float)$row['service_fee'], 2, '.', ''),
                number_format((float)$row['handling_fee'], 2, '.', ''),
                number_format((float)$row['tip_amount'], 2, '.', ''),
                number_format((float)$row['total_amount'], 2, '.', ''),
                $row['payment_status'],
            ]);
        }

        fclose($out);
        exit;
    }

    // =========================================================================
    // Private helpers
    // =========================================================================

    /**
     * Build the UNION SQL for distribution + marketplace rows.
     * Returns [sql, params].
     */
    private function buildUnionSql(string $type): array
    {
        $params = [];
        $parts  = [];

        if ($type === 'all' || $type === 'distribution') {
            // --- Distribution: paid ---
            $parts[] = "
                SELECT
                    'distribution'                                        AS revenue_type,
                    dr.id                                                 AS source_id,
                    dr.request_number                                     AS reference_number,
                    di.invoice_number                                     AS invoice_number,
                    bp.company_name                                       AS payer_name,
                    CONCAT(u.first_name, ' ', u.last_name)                AS payer_contact,
                    u.email                                               AS payer_email,
                    dr.paid_at                                            AS paid_at,
                    dr.payment_method                                     AS payment_method,
                    COALESCE(dr.payment_intent_id, dr.payment_reference)  AS transaction_ref,
                    dr.subtotal                                           AS subtotal,
                    dr.gst_amount                                         AS gst_amount,
                    dr.qst_amount                                         AS qst_amount,
                    dr.delivery_fee                                       AS delivery_fee,
                    dr.service_fee                                        AS service_fee,
                    dr.handling_fee                                       AS handling_fee,
                    dr.tip_amount                                         AS tip_amount,
                    dr.total_amount                                       AS total_amount,
                    dr.payment_status                                     AS payment_status,
                    dr.id                                                 AS distribution_request_id,
                    NULL                                                  AS order_id
                FROM distribution_requests dr
                JOIN business_profiles bp ON dr.business_profile_id = bp.id
                JOIN users u              ON bp.user_id = u.id
                LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
                WHERE dr.paid_at IS NOT NULL
                  AND dr.payment_status = 'paid'
            ";

            // --- Distribution: outstanding ---
            $parts[] = "
                SELECT
                    'distribution'                                        AS revenue_type,
                    dr.id                                                 AS source_id,
                    dr.request_number                                     AS reference_number,
                    di.invoice_number                                     AS invoice_number,
                    bp.company_name                                       AS payer_name,
                    CONCAT(u.first_name, ' ', u.last_name)                AS payer_contact,
                    u.email                                               AS payer_email,
                    NULL                                                  AS paid_at,
                    NULL                                                  AS payment_method,
                    NULL                                                  AS transaction_ref,
                    0                                                     AS subtotal,
                    0                                                     AS gst_amount,
                    0                                                     AS qst_amount,
                    0                                                     AS delivery_fee,
                    0                                                     AS service_fee,
                    0                                                     AS handling_fee,
                    0                                                     AS tip_amount,
                    dr.total_amount                                       AS total_amount,
                    'outstanding'                                         AS payment_status,
                    dr.id                                                 AS distribution_request_id,
                    NULL                                                  AS order_id
                FROM distribution_requests dr
                JOIN business_profiles bp ON dr.business_profile_id = bp.id
                JOIN users u              ON bp.user_id = u.id
                LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
                WHERE dr.status = 'awaiting_payment'
                  AND dr.payment_link_token IS NOT NULL
                  AND (dr.payment_link_expires_at IS NULL OR dr.payment_link_expires_at > NOW())
            ";

            // --- Distribution: refunded ---
            $parts[] = "
                SELECT
                    'distribution'                                        AS revenue_type,
                    dr.id                                                 AS source_id,
                    dr.request_number                                     AS reference_number,
                    di.invoice_number                                     AS invoice_number,
                    bp.company_name                                       AS payer_name,
                    CONCAT(u.first_name, ' ', u.last_name)                AS payer_contact,
                    u.email                                               AS payer_email,
                    dr.paid_at                                            AS paid_at,
                    dr.payment_method                                     AS payment_method,
                    COALESCE(dr.payment_intent_id, dr.payment_reference)  AS transaction_ref,
                    dr.subtotal                                           AS subtotal,
                    dr.gst_amount                                         AS gst_amount,
                    dr.qst_amount                                         AS qst_amount,
                    dr.delivery_fee                                       AS delivery_fee,
                    dr.service_fee                                        AS service_fee,
                    dr.handling_fee                                       AS handling_fee,
                    dr.tip_amount                                         AS tip_amount,
                    dr.total_amount                                       AS total_amount,
                    dr.payment_status                                     AS payment_status,
                    dr.id                                                 AS distribution_request_id,
                    NULL                                                  AS order_id
                FROM distribution_requests dr
                JOIN business_profiles bp ON dr.business_profile_id = bp.id
                JOIN users u              ON bp.user_id = u.id
                LEFT JOIN distribution_invoices di ON dr.id = di.distribution_request_id
                WHERE dr.payment_status = 'refunded'
            ";
        }

        if ($type === 'all' || $type === 'marketplace') {
            // --- Marketplace: paid + refunded ---
            $parts[] = "
                SELECT
                    'marketplace'                                         AS revenue_type,
                    o.id                                                  AS source_id,
                    o.order_number                                        AS reference_number,
                    NULL                                                  AS invoice_number,
                    CONCAT(u.first_name, ' ', u.last_name)                AS payer_name,
                    CONCAT(u.first_name, ' ', u.last_name)                AS payer_contact,
                    u.email                                               AS payer_email,
                    o.created_at                                          AS paid_at,
                    o.payment_method                                      AS payment_method,
                    o.payment_intent_id                                   AS transaction_ref,
                    o.subtotal                                            AS subtotal,
                    o.tax                                                 AS gst_amount,
                    0                                                     AS qst_amount,
                    o.delivery_fee                                        AS delivery_fee,
                    0                                                     AS service_fee,
                    0                                                     AS handling_fee,
                    0                                                     AS tip_amount,
                    o.total                                               AS total_amount,
                    o.payment_status                                      AS payment_status,
                    NULL                                                  AS distribution_request_id,
                    o.id                                                  AS order_id
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.payment_status IN ('paid', 'refunded')
            ";
        }

        $sql = implode(' UNION ALL ', $parts);
        return [$sql, $params];
    }

    /**
     * Build the outer WHERE clause for filters.
     * Returns [whereSql, params].
     */
    private function buildWhereClause(string $method, string $status, string $search, string $dateFrom, string $dateTo): array
    {
        $conditions = [];
        $params     = [];

        if ($method) {
            $conditions[] = 'u.payment_method = ?';
            $params[]     = $method;
        }

        if ($status && in_array($status, ['paid', 'outstanding', 'refunded'], true)) {
            $conditions[] = 'u.payment_status = ?';
            $params[]     = $status;
        }

        if ($search) {
            $conditions[] = '(u.payer_name LIKE ? OR u.reference_number LIKE ?)';
            $term         = '%' . $search . '%';
            $params[]     = $term;
            $params[]     = $term;
        }

        if ($dateFrom) {
            $conditions[] = 'u.paid_at >= ?';
            $params[]     = $dateFrom . ' 00:00:00';
        }

        if ($dateTo) {
            $conditions[] = 'u.paid_at <= ?';
            $params[]     = $dateTo . ' 23:59:59';
        }

        $whereSql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$whereSql, $params];
    }

    /**
     * Calculate summary stats (no filters applied).
     */
    private function buildStats(): array
    {
        // Total received — distribution paid
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM distribution_requests
            WHERE payment_status = 'paid'
        ");
        $distReceived = (float)$stmt->fetchColumn();

        // Total received — marketplace paid
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total), 0)
            FROM orders
            WHERE payment_status = 'paid'
        ");
        $mktReceived = (float)$stmt->fetchColumn();

        $totalReceived = $distReceived + $mktReceived;

        // This month — distribution
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM distribution_requests
            WHERE payment_status = 'paid'
              AND YEAR(paid_at)  = YEAR(NOW())
              AND MONTH(paid_at) = MONTH(NOW())
        ");
        $distMonth = (float)$stmt->fetchColumn();

        // This month — marketplace
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total), 0)
            FROM orders
            WHERE payment_status = 'paid'
              AND YEAR(created_at)  = YEAR(NOW())
              AND MONTH(created_at) = MONTH(NOW())
        ");
        $mktMonth = (float)$stmt->fetchColumn();

        $thisMonth = $distMonth + $mktMonth;

        // Outstanding
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0), COUNT(*)
            FROM distribution_requests
            WHERE status = 'awaiting_payment'
              AND payment_link_token IS NOT NULL
              AND (payment_link_expires_at IS NULL OR payment_link_expires_at > NOW())
        ");
        $outRow = $stmt->fetch(\PDO::FETCH_NUM);
        $outstanding      = (float)($outRow[0] ?? 0);
        $outstandingCount = (int)($outRow[1] ?? 0);

        // Total refunded — distribution
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total_amount), 0)
            FROM distribution_requests
            WHERE payment_status = 'refunded'
        ");
        $distRefunded = (float)$stmt->fetchColumn();

        // Total refunded — marketplace
        $stmt = $this->db->query("
            SELECT COALESCE(SUM(total), 0)
            FROM orders
            WHERE payment_status = 'refunded'
        ");
        $mktRefunded = (float)$stmt->fetchColumn();

        $totalRefunded = $distRefunded + $mktRefunded;

        return compact('totalReceived', 'thisMonth', 'outstanding', 'outstandingCount', 'totalRefunded');
    }

    /**
     * Insert a row into distribution_status_history.
     */
    private function logStatusChange(int $requestId, string $fromStatus, string $toStatus, string $notes = ''): void
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO distribution_status_history
                (distribution_request_id, old_status, new_status, changed_by_type, changed_by, notes, created_at)
                VALUES (?, ?, ?, 'admin', ?, ?, NOW())
            ");
            $stmt->execute([
                $requestId,
                $fromStatus,
                $toStatus,
                $_SESSION['user']['id'] ?? null,
                $notes,
            ]);
        } catch (\PDOException $e) {
            error_log('AdminReceivablesController status history error: ' . $e->getMessage());
        }
    }

    /**
     * After marking a distribution request paid, check if all supplier POs are
     * ready_for_pickup — if so, delegate to AdminDistributionController::autoAssignDistributionDriver().
     */
    private function maybeAutoAssignDriver(int $distRequestId): void
    {
        try {
            // Count POs for this request
            $stmt = $this->db->prepare("
                SELECT COUNT(*) AS total,
                       SUM(CASE WHEN po.status = 'ready_for_pickup' THEN 1 ELSE 0 END) AS ready
                FROM purchase_orders po
                WHERE po.distribution_request_id = ?
            ");
            $stmt->execute([$distRequestId]);
            $row = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($row && $row['total'] > 0 && (int)$row['total'] === (int)$row['ready']) {
                AdminDistributionController::autoAssignDistributionDriver($distRequestId, $this->db);
            }
        } catch (\Exception $e) {
            error_log('AdminReceivablesController::maybeAutoAssignDriver error: ' . $e->getMessage());
        }
    }
}
