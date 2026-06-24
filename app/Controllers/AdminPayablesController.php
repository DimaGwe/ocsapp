<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

class AdminPayablesController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Payables dashboard — overview of invoices, due this week, overdue, etc.
     */
    public function index(): void
    {
        $statusFilter = get('status', '');
        $supplierFilter = (int)get('supplier_id', 0);

        // Build query
        $where = ['1=1'];
        $params = [];

        if ($statusFilter) {
            $where[] = 'si.status = ?';
            $params[] = $statusFilter;
        }
        if ($supplierFilter) {
            $where[] = 'si.supplier_id = ?';
            $params[] = $supplierFilter;
        }

        $whereStr = implode(' AND ', $where);

        // Get invoices
        $stmt = $this->db->prepare("
            SELECT si.*, s.company_name, s.name as supplier_name, s.email as supplier_email,
                   po.po_number
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            LEFT JOIN purchase_orders po ON si.po_id = po.id
            WHERE {$whereStr}
            ORDER BY
                CASE si.status
                    WHEN 'overdue' THEN 1
                    WHEN 'sent' THEN 2
                    WHEN 'partial' THEN 3
                    WHEN 'draft' THEN 4
                    WHEN 'paid' THEN 5
                    WHEN 'cancelled' THEN 6
                END,
                si.created_at DESC
        ");
        $stmt->execute($params);
        $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Summary stats
        $stats = $this->db->query("
            SELECT
                COUNT(*) as total_invoices,
                SUM(CASE WHEN status IN ('sent','partial') THEN balance_due ELSE 0 END) as total_outstanding,
                SUM(CASE WHEN status = 'overdue' THEN balance_due ELSE 0 END) as total_overdue,
                SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status IN ('sent','partial') AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN balance_due ELSE 0 END) as due_this_week,
                COUNT(CASE WHEN status = 'overdue' THEN 1 END) as overdue_count,
                COUNT(CASE WHEN status IN ('sent','partial') AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 1 END) as due_this_week_count
            FROM supplier_invoices
        ")->fetch(\PDO::FETCH_ASSOC);

        // Get suppliers for filter dropdown
        $suppliers = $this->db->query("SELECT id, company_name, name FROM suppliers WHERE status IN ('active','pending_verification') ORDER BY company_name")->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.payables.index', [
            'pageTitle' => 'Supplier Payables',
            'invoices' => $invoices,
            'stats' => $stats,
            'suppliers' => $suppliers,
            'statusFilter' => $statusFilter,
            'supplierFilter' => $supplierFilter,
        ]);
    }

    /**
     * View single invoice detail
     */
    public function viewInvoice(): void
    {
        $id = (int)get('id');

        $stmt = $this->db->prepare("
            SELECT si.*, s.company_name, s.name as supplier_name, s.email as supplier_email,
                   s.phone as supplier_phone, s.payment_terms, s.address as supplier_address,
                   s.city as supplier_city, s.province as supplier_province, s.postal_code as supplier_postal,
                   po.po_number, po.order_date as po_date
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            LEFT JOIN purchase_orders po ON si.po_id = po.id
            WHERE si.id = ?
        ");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$invoice) {
            setFlash('error', 'Invoice not found.');
            redirect('admin/payables');
            return;
        }

        // Get PO items if linked
        $items = [];
        if ($invoice['po_id']) {
            $stmt = $this->db->prepare("
                SELECT poi.*, sp.product_name, sp.sku
                FROM purchase_order_items poi
                LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                WHERE poi.purchase_order_id = ?
            ");
            $stmt->execute([$invoice['po_id']]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        // Get payment history for this invoice
        $stmt = $this->db->prepare("
            SELECT sp.*, sip.amount_applied
            FROM supplier_invoice_payments sip
            JOIN supplier_payments sp ON sip.payment_id = sp.id
            WHERE sip.invoice_id = ?
            ORDER BY sp.payment_date DESC
        ");
        $stmt->execute([$id]);
        $payments = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        view('admin.payables.view', [
            'pageTitle' => 'Invoice ' . $invoice['invoice_number'],
            'invoice' => $invoice,
            'items' => $items,
            'payments' => $payments,
        ]);
    }

    /**
     * Create invoice for a PO (reusable static method).
     * Returns invoice data array on success, false on failure.
     */
    public static function createInvoiceForPO(int $poId, ?int $createdBy = null): array|false
    {
        try {
            $db = \Database::getConnection();

            // Get PO
            $stmt = $db->prepare("SELECT * FROM purchase_orders WHERE id = ?");
            $stmt->execute([$poId]);
            $po = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$po) {
                logger("createInvoiceForPO: PO #{$poId} not found", 'error');
                return false;
            }

            // Check if invoice already exists for this PO
            $check = $db->prepare("SELECT id, invoice_number FROM supplier_invoices WHERE po_id = ?");
            $check->execute([$poId]);
            $existing = $check->fetch(\PDO::FETCH_ASSOC);
            if ($existing) {
                logger("createInvoiceForPO: Invoice already exists for PO #{$poId}: {$existing['invoice_number']}", 'info');
                return false;
            }

            // Get supplier payment terms to calculate due date
            $stmt = $db->prepare("SELECT payment_terms FROM suppliers WHERE id = ?");
            $stmt->execute([$po['supplier_id']]);
            $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

            $paymentTermsDays = 30; // default Net 30
            if ($supplier && $supplier['payment_terms']) {
                if (preg_match('/Net\s*(\d+)/i', $supplier['payment_terms'], $m)) {
                    $paymentTermsDays = (int)$m[1];
                }
            }

            // Generate invoice number (INV-YYYYMM-XXXX)
            $prefix = 'INV-' . date('Ym') . '-';
            $lastInv = $db->query("SELECT invoice_number FROM supplier_invoices WHERE invoice_number LIKE '{$prefix}%' ORDER BY id DESC LIMIT 1")->fetchColumn();
            if ($lastInv) {
                $lastSeq = (int)substr($lastInv, -4);
                $invoiceNumber = $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $invoiceNumber = $prefix . '0001';
            }

            // Calculate taxes (Quebec: GST 5%, QST 9.975%)
            $subtotal = (float)$po['subtotal'];
            $shipping = (float)$po['shipping_cost'];
            $taxableAmount = $subtotal + $shipping;
            $gst = round($taxableAmount * 0.05, 2);
            $qst = round($taxableAmount * 0.09975, 2);
            $total = round($subtotal + $shipping + $gst + $qst, 2);

            $issueDate = date('Y-m-d');
            $dueDate = date('Y-m-d', strtotime("+{$paymentTermsDays} days"));

            $stmt = $db->prepare("
                INSERT INTO supplier_invoices (
                    invoice_number, supplier_id, po_id,
                    subtotal, tax_gst, tax_qst, shipping, total_amount, amount_paid, balance_due,
                    status, issue_date, due_date, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.00, ?, 'sent', ?, ?, ?)
            ");
            $stmt->execute([
                $invoiceNumber,
                $po['supplier_id'],
                $poId,
                $subtotal,
                $gst,
                $qst,
                $shipping,
                $total,
                $total,
                $issueDate,
                $dueDate,
                $createdBy,
            ]);

            $invoiceId = (int)$db->lastInsertId();

            if (function_exists('auditLog')) {
                auditLog('invoice_created', "Generated invoice {$invoiceNumber} for PO {$po['po_number']} — \${$total}", $invoiceId);
            }

            return [
                'id' => $invoiceId,
                'invoice_number' => $invoiceNumber,
                'supplier_id' => (int)$po['supplier_id'],
                'po_id' => $poId,
                'po_number' => $po['po_number'],
                'subtotal' => $subtotal,
                'tax_gst' => $gst,
                'tax_qst' => $qst,
                'shipping' => $shipping,
                'total_amount' => $total,
                'issue_date' => $issueDate,
                'due_date' => $dueDate,
            ];

        } catch (\Exception $e) {
            logger("createInvoiceForPO error: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * POST endpoint — generate invoice (fallback for manual generation)
     */
    public function generateInvoice(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/purchase-orders');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $poId = (int)post('po_id');
        $result = self::createInvoiceForPO($poId, $_SESSION['user']['id'] ?? null);

        if ($result) {
            setFlash('success', "Invoice <strong>{$result['invoice_number']}</strong> generated successfully. Due date: " . date('M j, Y', strtotime($result['due_date'])));
            redirect('admin/payables/view?id=' . $result['id']);
        } else {
            setFlash('error', 'Could not generate invoice. It may already exist or the PO was not found.');
            back();
        }
    }

    /**
     * Generate PDF for an invoice. Returns file path on success, false on failure.
     */
    public static function generateInvoicePdf(int $invoiceId): string|false
    {
        try {
            $db = \Database::getConnection();

            // Get invoice + supplier + PO data
            $stmt = $db->prepare("
                SELECT si.*, s.company_name, s.name as supplier_name, s.email as supplier_email,
                       s.phone as supplier_phone, s.address as supplier_address,
                       s.city as supplier_city, s.province as supplier_province, s.postal_code as supplier_postal,
                       s.payment_terms,
                       po.po_number, po.order_date as po_date
                FROM supplier_invoices si
                JOIN suppliers s ON si.supplier_id = s.id
                LEFT JOIN purchase_orders po ON si.po_id = po.id
                WHERE si.id = ?
            ");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$invoice) return false;

            // Get PO items
            $items = [];
            if ($invoice['po_id']) {
                $stmt = $db->prepare("
                    SELECT poi.*, sp.product_name, sp.sku
                    FROM purchase_order_items poi
                    LEFT JOIN supplier_products sp ON poi.product_id = sp.id
                    WHERE poi.purchase_order_id = ?
                ");
                $stmt->execute([$invoice['po_id']]);
                $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Build items HTML
            $itemsHtml = '';
            $lineNum = 1;
            foreach ($items as $item) {
                $totalCost = (float)($item['total_cost'] ?? $item['quantity_ordered'] * $item['unit_cost']);
                $itemsHtml .= "
                    <tr>
                        <td style='padding:10px 12px; border-bottom:1px solid #e5e7eb;'>{$lineNum}</td>
                        <td style='padding:10px 12px; border-bottom:1px solid #e5e7eb;'>"
                            . htmlspecialchars($item['product_name'] ?? 'Product') .
                            ($item['sku'] ? "<br><small style='color:#6b7280;'>SKU: " . htmlspecialchars($item['sku']) . "</small>" : "") .
                        "</td>
                        <td style='padding:10px 12px; border-bottom:1px solid #e5e7eb; text-align:center;'>{$item['quantity_ordered']}</td>
                        <td style='padding:10px 12px; border-bottom:1px solid #e5e7eb; text-align:right;'>$" . number_format((float)$item['unit_cost'], 2) . "</td>
                        <td style='padding:10px 12px; border-bottom:1px solid #e5e7eb; text-align:right;'>$" . number_format($totalCost, 2) . "</td>
                    </tr>";
                $lineNum++;
            }

            $statusLabel = strtoupper($invoice['status']);
            $statusColor = match($invoice['status']) {
                'paid' => '#065f46', 'sent' => '#1d4ed8', 'partial' => '#92400e',
                'overdue' => '#991b1b', default => '#6b7280',
            };
            $statusBg = match($invoice['status']) {
                'paid' => '#d1fae5', 'sent' => '#dbeafe', 'partial' => '#fef3c7',
                'overdue' => '#fee2e2', default => '#f3f4f6',
            };

            $html = "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Invoice {$invoice['invoice_number']}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; margin: 0; padding: 30px; }
        .header { display: table; width: 100%; margin-bottom: 30px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; vertical-align: top; text-align: right; }
        .logo { font-size: 28px; font-weight: bold; color: #00b207; margin-bottom: 5px; }
        .company-info { font-size: 11px; color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class='header'>
        <div class='header-left'>
            <div class='logo'>OCSAPP</div>
            <div class='company-info'>
                OCS Marketplace<br>
                Montreal, Quebec, Canada<br>
                info@ocsapp.ca
            </div>
        </div>
        <div class='header-right'>
            <div style='font-size:24px; font-weight:bold; color:#333; margin-bottom:5px;'>INVOICE</div>
            <div style='font-size:14px; color:#666; margin-bottom:10px;'>{$invoice['invoice_number']}</div>
            <div style='display:inline-block; padding:4px 12px; border-radius:12px; font-size:11px; font-weight:600; background:{$statusBg}; color:{$statusColor};'>{$statusLabel}</div>
        </div>
    </div>

    <div style='display:table; width:100%; margin-bottom:25px;'>
        <div style='display:table-cell; width:50%; vertical-align:top;'>
            <div style='background:#f9fafb; padding:15px; border-radius:8px;'>
                <div style='font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; margin-bottom:8px;'>Bill To (Supplier)</div>
                <div style='font-size:14px; font-weight:600; color:#1f2937;'>" . htmlspecialchars($invoice['company_name'] ?: $invoice['supplier_name']) . "</div>
                " . ($invoice['supplier_address'] ? "<div style='color:#4b5563; margin-top:4px;'>" . htmlspecialchars($invoice['supplier_address']) . "</div>" : "") . "
                " . ($invoice['supplier_city'] ? "<div style='color:#4b5563;'>" . htmlspecialchars($invoice['supplier_city']) . ", " . htmlspecialchars($invoice['supplier_province'] ?? '') . " " . htmlspecialchars($invoice['supplier_postal'] ?? '') . "</div>" : "") . "
                " . ($invoice['supplier_email'] ? "<div style='color:#4b5563; margin-top:4px;'>" . htmlspecialchars($invoice['supplier_email']) . "</div>" : "") . "
            </div>
        </div>
        <div style='display:table-cell; width:50%; vertical-align:top; padding-left:20px;'>
            <div style='background:#f9fafb; padding:15px; border-radius:8px;'>
                <div style='font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; margin-bottom:8px;'>Invoice Details</div>
                <table style='width:100%; font-size:12px;'>
                    <tr><td style='color:#6b7280; padding:3px 0;'>Invoice Date:</td><td style='text-align:right; font-weight:500;'>" . date('M j, Y', strtotime($invoice['issue_date'])) . "</td></tr>
                    <tr><td style='color:#6b7280; padding:3px 0;'>Due Date:</td><td style='text-align:right; font-weight:600; color:#991b1b;'>" . date('M j, Y', strtotime($invoice['due_date'])) . "</td></tr>
                    <tr><td style='color:#6b7280; padding:3px 0;'>PO Reference:</td><td style='text-align:right; font-weight:500;'>" . htmlspecialchars($invoice['po_number'] ?? 'N/A') . "</td></tr>
                    <tr><td style='color:#6b7280; padding:3px 0;'>Payment Terms:</td><td style='text-align:right; font-weight:500;'>" . htmlspecialchars($invoice['payment_terms'] ?? 'Net 30') . "</td></tr>
                </table>
            </div>
        </div>
    </div>

    <table style='width:100%; border-collapse:collapse; margin-bottom:20px;'>
        <thead>
            <tr style='background:#f3f4f6;'>
                <th style='padding:10px 12px; text-align:left; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; border-bottom:2px solid #e5e7eb;'>#</th>
                <th style='padding:10px 12px; text-align:left; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; border-bottom:2px solid #e5e7eb;'>Item</th>
                <th style='padding:10px 12px; text-align:center; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; border-bottom:2px solid #e5e7eb;'>Qty</th>
                <th style='padding:10px 12px; text-align:right; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; border-bottom:2px solid #e5e7eb;'>Unit Price</th>
                <th style='padding:10px 12px; text-align:right; font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; border-bottom:2px solid #e5e7eb;'>Total</th>
            </tr>
        </thead>
        <tbody>
            {$itemsHtml}
        </tbody>
    </table>

    <div style='display:table; width:100%;'>
        <div style='display:table-cell; width:55%;'></div>
        <div style='display:table-cell; width:45%;'>
            <table style='width:100%; border-collapse:collapse;'>
                <tr><td style='padding:6px 12px; color:#6b7280;'>Subtotal</td><td style='padding:6px 12px; text-align:right; font-weight:500;'>$" . number_format((float)$invoice['subtotal'], 2) . "</td></tr>
                <tr><td style='padding:6px 12px; color:#6b7280;'>Shipping</td><td style='padding:6px 12px; text-align:right; font-weight:500;'>$" . number_format((float)$invoice['shipping'], 2) . "</td></tr>
                <tr><td style='padding:6px 12px; color:#6b7280;'>GST (5%)</td><td style='padding:6px 12px; text-align:right; font-weight:500;'>$" . number_format((float)$invoice['tax_gst'], 2) . "</td></tr>
                <tr><td style='padding:6px 12px; color:#6b7280;'>QST (9.975%)</td><td style='padding:6px 12px; text-align:right; font-weight:500;'>$" . number_format((float)$invoice['tax_qst'], 2) . "</td></tr>
                <tr style='border-top:2px solid #1f2937;'><td style='padding:10px 12px; font-size:15px; font-weight:700;'>Total Due</td><td style='padding:10px 12px; text-align:right; font-size:15px; font-weight:700; color:#00b207;'>$" . number_format((float)$invoice['total_amount'], 2) . "</td></tr>
            </table>
        </div>
    </div>

    <div style='margin-top:30px; padding-top:20px; border-top:1px solid #e5e7eb; font-size:11px; color:#9ca3af; text-align:center;'>
        This invoice was auto-generated by OCSAPP Marketplace. For questions, contact info@ocsapp.ca
    </div>
</body>
</html>";

            // Render PDF
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');
            $options->set('isFontSubsettingEnabled', true);
            $options->set('isPhpEnabled', false);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save to storage
            $storageDir = defined('BASE_PATH') ? BASE_PATH . '/storage/invoices' : dirname(__DIR__, 2) . '/storage/invoices';
            if (!is_dir($storageDir)) {
                mkdir($storageDir, 0755, true);
            }

            $filename = $invoice['invoice_number'] . '.pdf';
            $filePath = $storageDir . '/' . $filename;
            file_put_contents($filePath, $dompdf->output());

            return $filePath;

        } catch (\Exception $e) {
            logger("generateInvoicePdf error: " . $e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoicePdf(): void
    {
        $id = (int)get('id');

        $stmt = $this->db->prepare("SELECT invoice_number FROM supplier_invoices WHERE id = ?");
        $stmt->execute([$id]);
        $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$invoice) {
            setFlash('error', 'Invoice not found.');
            redirect('admin/payables');
            return;
        }

        $pdfPath = self::generateInvoicePdf($id);

        if (!$pdfPath || !file_exists($pdfPath)) {
            setFlash('error', 'Could not generate PDF.');
            redirect('admin/payables/view?id=' . $id);
            return;
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $invoice['invoice_number'] . '.pdf"');
        header('Content-Length: ' . filesize($pdfPath));
        readfile($pdfPath);
        exit;
    }

    /**
     * Record a payment against one or more invoices
     */
    public function recordPayment(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('admin/payables');
            return;
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid security token.');
            back();
            return;
        }

        $invoiceId = (int)post('invoice_id');
        $amount = (float)post('amount');
        $paymentMethod = post('payment_method', '');
        $referenceNumber = trim(post('reference_number', ''));
        $paymentDate = post('payment_date', date('Y-m-d'));
        $notes = trim(post('notes', ''));

        // Validate
        if ($amount <= 0) {
            setFlash('error', 'Payment amount must be greater than zero.');
            back();
            return;
        }

        if (!in_array($paymentMethod, ['interac', 'bank_transfer', 'cheque', 'other'])) {
            setFlash('error', 'Invalid payment method.');
            back();
            return;
        }

        try {
            // Get invoice
            $stmt = $this->db->prepare("SELECT * FROM supplier_invoices WHERE id = ?");
            $stmt->execute([$invoiceId]);
            $invoice = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$invoice) {
                setFlash('error', 'Invoice not found.');
                redirect('admin/payables');
                return;
            }

            if ($invoice['status'] === 'paid') {
                setFlash('error', 'This invoice is already fully paid.');
                redirect('admin/payables/view?id=' . $invoiceId);
                return;
            }

            if ($invoice['status'] === 'cancelled') {
                setFlash('error', 'Cannot record payment for a cancelled invoice.');
                back();
                return;
            }

            // Cap payment at balance due
            $effectiveAmount = min($amount, (float)$invoice['balance_due']);

            // Generate payment number (PAY-YYYYMM-XXXX)
            $prefix = 'PAY-' . date('Ym') . '-';
            $lastPay = $this->db->query("SELECT payment_number FROM supplier_payments WHERE payment_number LIKE '{$prefix}%' ORDER BY id DESC LIMIT 1")->fetchColumn();
            if ($lastPay) {
                $lastSeq = (int)substr($lastPay, -4);
                $paymentNumber = $prefix . str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
            } else {
                $paymentNumber = $prefix . '0001';
            }

            $this->db->beginTransaction();

            // Create payment record
            $stmt = $this->db->prepare("
                INSERT INTO supplier_payments (
                    payment_number, supplier_id, amount, payment_method,
                    reference_number, payment_date, notes, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $paymentNumber,
                $invoice['supplier_id'],
                $effectiveAmount,
                $paymentMethod,
                $referenceNumber ?: null,
                $paymentDate,
                $notes ?: null,
                $_SESSION['user']['id'] ?? null,
            ]);
            $paymentId = $this->db->lastInsertId();

            // Link payment to invoice
            $this->db->prepare("
                INSERT INTO supplier_invoice_payments (invoice_id, payment_id, amount_applied)
                VALUES (?, ?, ?)
            ")->execute([$invoiceId, $paymentId, $effectiveAmount]);

            // Update invoice balances
            $newAmountPaid = (float)$invoice['amount_paid'] + $effectiveAmount;
            $newBalance = (float)$invoice['total_amount'] - $newAmountPaid;
            $newStatus = $newBalance <= 0.01 ? 'paid' : 'partial'; // 0.01 tolerance for rounding
            $paidAt = $newStatus === 'paid' ? date('Y-m-d H:i:s') : null;

            $this->db->prepare("
                UPDATE supplier_invoices
                SET amount_paid = ?, balance_due = ?, status = ?, paid_at = ?
                WHERE id = ?
            ")->execute([
                round($newAmountPaid, 2),
                round(max(0, $newBalance), 2),
                $newStatus,
                $paidAt,
                $invoiceId,
            ]);

            $this->db->commit();

            if (function_exists('auditLog')) {
                $methodLabel = ucfirst(str_replace('_', ' ', $paymentMethod));
                auditLog('payment_recorded', "Payment {$paymentNumber}: \${$effectiveAmount} via {$methodLabel} for invoice {$invoice['invoice_number']}", $paymentId);
            }

            // Send payment confirmation to supplier
            try {
                $stmt = $this->db->prepare("SELECT * FROM suppliers WHERE id = ?");
                $stmt->execute([$invoice['supplier_id']]);
                $supplier = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($supplier) {
                    // Reload updated invoice for accurate balance
                    $stmt = $this->db->prepare("SELECT * FROM supplier_invoices WHERE id = ?");
                    $stmt->execute([$invoiceId]);
                    $updatedInvoice = $stmt->fetch(\PDO::FETCH_ASSOC);

                    $paymentData = [
                        'payment_number' => $paymentNumber,
                        'amount' => $effectiveAmount,
                        'payment_method' => $paymentMethod,
                        'payment_date' => $paymentDate,
                        'reference_number' => $referenceNumber,
                    ];

                    // Email to supplier
                    \App\Helpers\EmailHelper::sendPaymentConfirmation($updatedInvoice, $paymentData, $supplier);

                    // Supplier bell notification
                    $bellMsg = $newStatus === 'paid'
                        ? "Payment of \$" . number_format($effectiveAmount, 2) . " received. Invoice fully paid."
                        : "Payment of \$" . number_format($effectiveAmount, 2) . " received. Remaining balance: \$" . number_format(max(0, $newBalance), 2);
                    $bellMsgFr = $newStatus === 'paid'
                        ? "Paiement de " . number_format($effectiveAmount, 2) . " \$ reçu. Facture entièrement payée."
                        : "Paiement de " . number_format($effectiveAmount, 2) . " \$ reçu. Solde restant : " . number_format(max(0, $newBalance), 2) . " \$.";
                    \App\Helpers\NotificationHelper::addSupplierNotification(
                        (int)$invoice['supplier_id'],
                        'payment',
                        "Payment Received: {$invoice['invoice_number']}",
                        $bellMsg,
                        'supplier/invoices',
                        'money-bill-wave',
                        "Paiement reçu : {$invoice['invoice_number']}",
                        $bellMsgFr
                    );
                }
            } catch (\Exception $e) {
                logger("Payment notification error: " . $e->getMessage(), 'error');
            }

            $msg = "Payment of <strong>\$" . number_format($effectiveAmount, 2) . "</strong> recorded successfully ({$paymentNumber}).";
            if ($newStatus === 'paid') {
                $msg .= " Invoice is now <strong>fully paid</strong>.";
            }
            setFlash('success', $msg);
            redirect('admin/payables/view?id=' . $invoiceId);

        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            logger("Payment recording error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error recording payment: ' . $e->getMessage());
            back();
        }
    }

    /**
     * Export invoices as CSV
     */
    public function export(): void
    {
        $stmt = $this->db->query("
            SELECT si.invoice_number, s.company_name as supplier, po.po_number,
                   si.subtotal, si.tax_gst, si.tax_qst, si.shipping, si.total_amount,
                   si.amount_paid, si.balance_due, si.status,
                   si.issue_date, si.due_date, si.paid_at
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            LEFT JOIN purchase_orders po ON si.po_id = po.id
            ORDER BY si.issue_date DESC
        ");
        $invoices = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="supplier-payables-' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Invoice #', 'Supplier', 'PO #', 'Subtotal', 'GST', 'QST', 'Shipping', 'Total', 'Paid', 'Balance', 'Status', 'Issue Date', 'Due Date', 'Paid At']);

        foreach ($invoices as $inv) {
            fputcsv($out, [
                $inv['invoice_number'],
                $inv['supplier'],
                $inv['po_number'] ?? '',
                $inv['subtotal'],
                $inv['tax_gst'],
                $inv['tax_qst'],
                $inv['shipping'],
                $inv['total_amount'],
                $inv['amount_paid'],
                $inv['balance_due'],
                $inv['status'],
                $inv['issue_date'],
                $inv['due_date'],
                $inv['paid_at'] ?? '',
            ]);
        }

        fclose($out);
        exit;
    }

    /**
     * Mark overdue invoices (can be called via cron or manually).
     * Sends admin bell notification + email summary when invoices go overdue.
     */
    public static function markOverdueInvoices(): int
    {
        $db = \Database::getConnection();

        // First, find invoices that WILL become overdue (before updating)
        $stmt = $db->query("
            SELECT si.id, si.invoice_number, si.balance_due, si.due_date,
                   s.company_name, s.name as supplier_name
            FROM supplier_invoices si
            JOIN suppliers s ON si.supplier_id = s.id
            WHERE si.status IN ('sent', 'partial')
            AND si.due_date < CURDATE()
        ");
        $newlyOverdue = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Update statuses
        $count = $db->exec("
            UPDATE supplier_invoices
            SET status = 'overdue'
            WHERE status IN ('sent', 'partial')
            AND due_date < CURDATE()
        ");

        $overdueCount = (int)$count;

        // Send notifications if any invoices just went overdue
        if ($overdueCount > 0 && !empty($newlyOverdue)) {
            try {
                // Admin bell notification
                $totalOverdue = array_sum(array_column($newlyOverdue, 'balance_due'));
                \App\Helpers\NotificationHelper::add(
                    'invoice',
                    "{$overdueCount} Invoice" . ($overdueCount > 1 ? 's' : '') . " Now Overdue",
                    "Total overdue: \$" . number_format($totalOverdue, 2),
                    ['link' => '/admin/payables?status=overdue', 'icon' => 'exclamation-triangle', 'priority' => 'high']
                );

                // Admin email summary
                $listHtml = '';
                foreach ($newlyOverdue as $inv) {
                    $supplierDisplay = htmlspecialchars($inv['company_name'] ?: $inv['supplier_name']);
                    $listHtml .= "<tr>
                        <td style='padding:8px 12px; border-bottom:1px solid #f3f4f6; font-weight:600;'>{$inv['invoice_number']}</td>
                        <td style='padding:8px 12px; border-bottom:1px solid #f3f4f6;'>{$supplierDisplay}</td>
                        <td style='padding:8px 12px; border-bottom:1px solid #f3f4f6; color:#dc2626; font-weight:600; text-align:right;'>\$" . number_format($inv['balance_due'], 2) . "</td>
                        <td style='padding:8px 12px; border-bottom:1px solid #f3f4f6; color:#dc2626;'>" . date('M j, Y', strtotime($inv['due_date'])) . "</td>
                    </tr>";
                }

                $emailBody = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); padding: 24px; text-align: center; border-radius: 12px 12px 0 0;'>
                        <h1 style='color: white; margin: 0; font-size: 22px;'>Overdue Invoice Alert</h1>
                        <p style='color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 14px;'>{$overdueCount} invoice" . ($overdueCount > 1 ? 's have' : ' has') . " become overdue</p>
                    </div>
                    <div style='padding: 24px; background: #f9f9f9;'>
                        <div style='background: #fee2e2; border-left: 4px solid #dc2626; padding: 16px; border-radius: 4px; margin-bottom: 20px;'>
                            <strong style='color: #991b1b;'>Total Overdue: \$" . number_format($totalOverdue, 2) . "</strong>
                        </div>
                        <table style='width:100%; border-collapse:collapse; background:white; border-radius:8px; overflow:hidden;'>
                            <thead>
                                <tr style='background:#f9fafb;'>
                                    <th style='padding:10px 12px; text-align:left; font-size:12px; color:#6b7280; text-transform:uppercase;'>Invoice</th>
                                    <th style='padding:10px 12px; text-align:left; font-size:12px; color:#6b7280; text-transform:uppercase;'>Supplier</th>
                                    <th style='padding:10px 12px; text-align:right; font-size:12px; color:#6b7280; text-transform:uppercase;'>Balance</th>
                                    <th style='padding:10px 12px; text-align:left; font-size:12px; color:#6b7280; text-transform:uppercase;'>Due Date</th>
                                </tr>
                            </thead>
                            <tbody>{$listHtml}</tbody>
                        </table>
                        <div style='text-align:center; margin: 24px 0;'>
                            <a href='" . url('admin/payables?status=overdue') . "' style='display:inline-block; background:#dc2626; color:white; padding:12px 32px; text-decoration:none; border-radius:8px; font-weight:bold;'>View Overdue Invoices</a>
                        </div>
                    </div>
                    <div style='background: #333; color: #999; padding: 16px; text-align: center; font-size: 12px; border-radius: 0 0 12px 12px;'>
                        <p style='margin: 0;'>&copy; " . date('Y') . " OCSAPP. Automated notification.</p>
                    </div>
                </div>";

                \App\Helpers\EmailHelper::send(
                    'info@ocsapp.ca',
                    "Overdue Invoice Alert: {$overdueCount} invoice" . ($overdueCount > 1 ? 's' : '') . " — \$" . number_format($totalOverdue, 2),
                    $emailBody,
                    ['no_admin_bcc' => true]
                );

                logger("Marked {$overdueCount} invoices as overdue. Admin notified.", 'info');
            } catch (\Exception $e) {
                logger("Overdue invoice notification error: " . $e->getMessage(), 'error');
            }
        }

        return $overdueCount;
    }
}
