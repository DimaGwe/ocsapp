<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * DistributionDocumentController - PDF document generation for distribution requests
 * Generates Invoice, Purchase Order, and Sales Order documents
 */
class DistributionDocumentController
{
    private $db;

    public function __construct()
    {
        $this->db = \Database::getConnection();
    }

    /**
     * Generate and download Invoice PDF
     */
    public function invoice(): void
    {
        $requestId = (int)($_GET['id'] ?? 0);
        $this->generateDocument($requestId, 'invoice');
    }

    /**
     * Generate and download Purchase Order PDF
     */
    public function purchaseOrder(): void
    {
        $requestId = (int)($_GET['id'] ?? 0);
        $this->generateDocument($requestId, 'purchase_order');
    }

    /**
     * Generate and download Sales Order PDF
     */
    public function salesOrder(): void
    {
        $requestId = (int)($_GET['id'] ?? 0);
        $this->generateDocument($requestId, 'sales_order');
    }

    /**
     * Generate PDF document
     */
    private function generateDocument(int $requestId, string $type): void
    {
        try {
            // Verify access
            if (!$this->verifyAccess($requestId)) {
                header('HTTP/1.0 403 Forbidden');
                echo 'Access denied';
                return;
            }

            // Get request data
            $data = $this->getRequestData($requestId);
            if (!$data) {
                header('HTTP/1.0 404 Not Found');
                echo 'Request not found';
                return;
            }

            // Check if request has been paid (documents only available after payment)
            if (!in_array($data['request']['status'], ['paid', 'procurement', 'processing', 'ready', 'in_transit', 'delivered', 'completed'])) {
                header('HTTP/1.0 403 Forbidden');
                echo 'Documents are only available after payment';
                return;
            }

            // Get document number
            $stmt = $this->db->prepare("
                SELECT document_number FROM distribution_documents
                WHERE distribution_request_id = ? AND type = ?
            ");
            $stmt->execute([$requestId, $type]);
            $doc = $stmt->fetch(\PDO::FETCH_ASSOC);
            $documentNumber = $doc['document_number'] ?? $this->generateDocumentNumber($type);

            // Generate HTML based on document type
            $html = match($type) {
                'invoice' => $this->generateInvoiceHtml($data, $documentNumber),
                'purchase_order' => $this->generatePurchaseOrderHtml($data, $documentNumber),
                'sales_order' => $this->generateSalesOrderHtml($data, $documentNumber),
                default => throw new \Exception('Invalid document type')
            };

            // Initialize dompdf
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isRemoteEnabled', true);
            $options->set('defaultFont', 'Helvetica');

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Output PDF
            $filename = match($type) {
                'invoice' => 'Invoice_' . $documentNumber . '.pdf',
                'purchase_order' => 'PO_' . $documentNumber . '.pdf',
                'sales_order' => 'SO_' . $documentNumber . '.pdf',
                default => 'Document.pdf'
            };

            $dompdf->stream($filename, ['Attachment' => true]);

        } catch (\Exception $e) {
            error_log('Document generation error: ' . $e->getMessage());
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error generating document';
        }
    }

    /**
     * Verify user has access to this document
     */
    private function verifyAccess(int $requestId): bool
    {
        // Admin always has access
        if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'] ?? '', ['admin', 'super_admin', 'admin_staff'])) {
            return true;
        }

        // Business / distribution portal user — owns this request
        if (isset($_SESSION['business']['id'])) {
            $stmt = $this->db->prepare("
                SELECT id FROM distribution_requests
                WHERE id = ? AND business_profile_id = ?
            ");
            $stmt->execute([$requestId, (int)$_SESSION['business']['id']]);
            return (bool)$stmt->fetch();
        }

        return false;
    }

    /**
     * Get all request data for document generation
     */
    private function getRequestData(int $requestId): ?array
    {
        // Get request with business info
        $stmt = $this->db->prepare("
            SELECT dr.*, bp.company_name, bp.neq_number,
                   bp.billing_street, bp.billing_city, bp.billing_province, bp.billing_postal_code,
                   u.first_name, u.last_name, u.email, u.phone
            FROM distribution_requests dr
            INNER JOIN business_profiles bp ON dr.business_profile_id = bp.id
            INNER JOIN users u ON bp.user_id = u.id
            WHERE dr.id = ?
        ");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$request) {
            return null;
        }

        // Get catalog items
        $stmt = $this->db->prepare("
            SELECT dri.*, sp.product_name, sp.sku
            FROM distribution_request_items dri
            LEFT JOIN supplier_products sp ON dri.product_id = sp.id
            WHERE dri.distribution_request_id = ?
        ");
        $stmt->execute([$requestId]);
        $catalogItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Get shopping items
        $stmt = $this->db->prepare("SELECT * FROM distribution_shopping_items WHERE distribution_request_id = ?");
        $stmt->execute([$requestId]);
        $shoppingItems = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'request' => $request,
            'catalogItems' => $catalogItems,
            'shoppingItems' => $shoppingItems
        ];
    }

    /**
     * Generate document number if not exists
     */
    private function generateDocumentNumber(string $type): string
    {
        $prefix = match($type) {
            'invoice' => 'INV',
            'purchase_order' => 'PO',
            'sales_order' => 'SO',
            default => 'DOC'
        };

        return $prefix . '-' . date('Y') . '-' . strtoupper(substr(uniqid(), -5));
    }

    /**
     * Generate Invoice HTML
     */
    private function generateInvoiceHtml(array $data, string $documentNumber): string
    {
        $request = $data['request'];
        $catalogItems = $data['catalogItems'];
        $shoppingItems = $data['shoppingItems'];

        $itemsHtml = '';
        $lineNum = 1;

        foreach ($catalogItems as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['product_name']) . "<br><small style='color:#666;'>SKU: " . htmlspecialchars($item['sku'] ?? 'N/A') . "</small></td>
                    <td style='text-align:center;'>{$item['quantity']}</td>
                    <td style='text-align:right;'>\$" . number_format($item['unit_price'], 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        foreach ($shoppingItems as $item) {
            $unitPrice = $item['unit_price'] ?? 0;
            $subtotal = $item['quantity'] * $unitPrice;
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['item_name']) . "<br><small style='color:#666;'>Shopping List Item</small></td>
                    <td style='text-align:center;'>{$item['quantity']} " . htmlspecialchars($item['unit'] ?? 'units') . "</td>
                    <td style='text-align:right;'>\$" . number_format($unitPrice, 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        return $this->getDocumentTemplate([
            'title' => 'INVOICE',
            'documentNumber' => $documentNumber,
            'date' => date('F j, Y', strtotime($request['paid_at'] ?? 'now')),
            'request' => $request,
            'itemsHtml' => $itemsHtml,
            'showPaymentInfo' => true
        ]);
    }

    /**
     * Generate Purchase Order HTML
     */
    private function generatePurchaseOrderHtml(array $data, string $documentNumber): string
    {
        $request = $data['request'];
        $catalogItems = $data['catalogItems'];
        $shoppingItems = $data['shoppingItems'];

        $itemsHtml = '';
        $lineNum = 1;

        foreach ($catalogItems as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['product_name']) . "<br><small style='color:#666;'>SKU: " . htmlspecialchars($item['sku'] ?? 'N/A') . "</small></td>
                    <td style='text-align:center;'>{$item['quantity']}</td>
                    <td style='text-align:right;'>\$" . number_format($item['unit_price'], 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        foreach ($shoppingItems as $item) {
            $unitPrice = $item['unit_price'] ?? 0;
            $subtotal = $item['quantity'] * $unitPrice;
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['item_name']) . "</td>
                    <td style='text-align:center;'>{$item['quantity']} " . htmlspecialchars($item['unit'] ?? 'units') . "</td>
                    <td style='text-align:right;'>\$" . number_format($unitPrice, 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        return $this->getDocumentTemplate([
            'title' => 'PURCHASE ORDER',
            'documentNumber' => $documentNumber,
            'date' => date('F j, Y'),
            'request' => $request,
            'itemsHtml' => $itemsHtml,
            'showPaymentInfo' => false,
            'showDeliveryInfo' => true
        ]);
    }

    /**
     * Generate Sales Order HTML
     */
    private function generateSalesOrderHtml(array $data, string $documentNumber): string
    {
        $request = $data['request'];
        $catalogItems = $data['catalogItems'];
        $shoppingItems = $data['shoppingItems'];

        $itemsHtml = '';
        $lineNum = 1;

        foreach ($catalogItems as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['product_name']) . "<br><small style='color:#666;'>SKU: " . htmlspecialchars($item['sku'] ?? 'N/A') . "</small></td>
                    <td style='text-align:center;'>{$item['quantity']}</td>
                    <td style='text-align:right;'>\$" . number_format($item['unit_price'], 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        foreach ($shoppingItems as $item) {
            $unitPrice = $item['unit_price'] ?? 0;
            $subtotal = $item['quantity'] * $unitPrice;
            $itemsHtml .= "
                <tr>
                    <td>{$lineNum}</td>
                    <td>" . htmlspecialchars($item['item_name']) . "</td>
                    <td style='text-align:center;'>{$item['quantity']} " . htmlspecialchars($item['unit'] ?? 'units') . "</td>
                    <td style='text-align:right;'>\$" . number_format($unitPrice, 2) . "</td>
                    <td style='text-align:right;'>\$" . number_format($subtotal, 2) . "</td>
                </tr>";
            $lineNum++;
        }

        return $this->getDocumentTemplate([
            'title' => 'SALES ORDER',
            'documentNumber' => $documentNumber,
            'date' => date('F j, Y', strtotime($request['paid_at'] ?? 'now')),
            'request' => $request,
            'itemsHtml' => $itemsHtml,
            'showPaymentInfo' => false,
            'showDeliveryInfo' => true,
            'isInternal' => true
        ]);
    }

    /**
     * Get the base document template
     */
    private function getDocumentTemplate(array $params): string
    {
        $request = $params['request'];
        $showPaymentInfo = $params['showPaymentInfo'] ?? false;
        $showDeliveryInfo = $params['showDeliveryInfo'] ?? false;
        $isInternal = $params['isInternal'] ?? false;

        $statusLabel = match($request['status']) {
            'paid' => 'PAID',
            'procurement' => 'IN PROCUREMENT',
            'in_transit' => 'IN TRANSIT',
            'delivered' => 'DELIVERED',
            default => strtoupper($request['status'])
        };

        $paymentSection = '';
        if ($showPaymentInfo && $request['paid_at']) {
            $paymentSection = "
                <div style='background:#e8f5e9; padding:15px; border-radius:5px; margin-top:20px;'>
                    <h4 style='color:#2e7d32; margin:0 0 10px 0;'>Payment Information</h4>
                    <p style='margin:5px 0;'><strong>Status:</strong> PAID</p>
                    <p style='margin:5px 0;'><strong>Payment Date:</strong> " . date('F j, Y', strtotime($request['paid_at'])) . "</p>
                    <p style='margin:5px 0;'><strong>Method:</strong> " . ucfirst($request['payment_method'] ?? 'Stripe') . "</p>
                </div>";
        }

        $deliverySection = '';
        if ($showDeliveryInfo) {
            $deliverySection = "
                <div style='background:#f5f5f5; padding:15px; border-radius:5px; margin-top:20px;'>
                    <h4 style='margin:0 0 10px 0;'>Delivery Information</h4>
                    <p style='margin:5px 0;'><strong>Address:</strong><br>
                    " . htmlspecialchars($request['delivery_street']) . "<br>
                    " . htmlspecialchars($request['delivery_city']) . ", " . htmlspecialchars($request['delivery_province']) . "<br>
                    " . htmlspecialchars($request['delivery_postal_code']) . "</p>
                    " . ($request['preferred_delivery_date'] ? "<p style='margin:5px 0;'><strong>Preferred Date:</strong> " . date('F j, Y', strtotime($request['preferred_delivery_date'])) . "</p>" : "") . "
                </div>";
        }

        $internalBadge = $isInternal ? "<div style='background:#fff3cd; color:#856404; padding:5px 10px; border-radius:3px; display:inline-block; margin-bottom:10px;'>INTERNAL DOCUMENT</div>" : "";

        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>{$params['title']} - {$params['documentNumber']}</title>
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 30px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #00b207;
            margin-bottom: 5px;
        }
        .company-info {
            font-size: 11px;
            color: #666;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        .document-number {
            font-size: 14px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            background: #00b207;
            color: white;
            padding: 5px 15px;
            border-radius: 3px;
            font-weight: bold;
            margin-top: 10px;
        }
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .info-box {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-right: 20px;
        }
        .info-box h4 {
            font-size: 12px;
            color: #666;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            border-bottom: 2px solid #00b207;
            padding-bottom: 5px;
        }
        .info-box p {
            margin: 5px 0;
        }
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.items th {
            background: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        table.items td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        table.items tr:last-child td {
            border-bottom: 2px solid #ddd;
        }
        .totals {
            width: 300px;
            margin-left: auto;
        }
        .totals table {
            width: 100%;
        }
        .totals td {
            padding: 8px 0;
        }
        .totals .label {
            text-align: left;
            color: #666;
        }
        .totals .value {
            text-align: right;
            font-weight: bold;
        }
        .totals .total-row {
            border-top: 2px solid #333;
            font-size: 16px;
        }
        .totals .total-row td {
            padding-top: 15px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #888;
            text-align: center;
        }
        .terms {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 30px;
            font-size: 10px;
        }
        .terms h4 {
            margin: 0 0 10px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class='header'>
        <div class='header-left'>
            <div class='logo'>OCSAPP</div>
            <div class='company-info'>
                OCS Distribution Services<br>
                Montreal, Quebec, Canada<br>
                support@ocsapp.ca<br>
                GST/HST: [Pending Registration]<br>
                QST: [Pending Registration]
            </div>
        </div>
        <div class='header-right'>
            {$internalBadge}
            <div class='document-title'>{$params['title']}</div>
            <div class='document-number'>{$params['documentNumber']}</div>
            <p>Date: {$params['date']}</p>
            <div class='status-badge'>{$statusLabel}</div>
        </div>
    </div>

    <div class='info-section'>
        <div class='info-box'>
            <h4>Bill To</h4>
            <p><strong>" . htmlspecialchars($request['company_name']) . "</strong></p>
            <p>" . htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) . "</p>
            <p>" . htmlspecialchars($request['billing_street'] ?? $request['delivery_street']) . "</p>
            <p>" . htmlspecialchars(($request['billing_city'] ?? $request['delivery_city']) . ', ' . ($request['billing_province'] ?? $request['delivery_province'])) . "</p>
            <p>" . htmlspecialchars($request['billing_postal_code'] ?? $request['delivery_postal_code']) . "</p>
            <p>Email: " . htmlspecialchars($request['email']) . "</p>
            " . (!empty($request['neq_number']) ? "<p>NEQ #: " . htmlspecialchars($request['neq_number']) . "</p>" : "") . "
        </div>
        <div class='info-box'>
            <h4>Ship To</h4>
            <p><strong>" . htmlspecialchars($request['company_name']) . "</strong></p>
            <p>" . htmlspecialchars($request['delivery_street']) . "</p>
            <p>" . htmlspecialchars($request['delivery_city'] . ', ' . $request['delivery_province']) . "</p>
            <p>" . htmlspecialchars($request['delivery_postal_code']) . "</p>
            <br>
            <p><strong>Request #:</strong> " . htmlspecialchars($request['request_number']) . "</p>
            <p><strong>Request Name:</strong> " . htmlspecialchars($request['request_name']) . "</p>
        </div>
    </div>

    <table class='items'>
        <thead>
            <tr>
                <th style='width:40px;'>#</th>
                <th>Description</th>
                <th style='width:80px; text-align:center;'>Qty</th>
                <th style='width:100px; text-align:right;'>Unit Price</th>
                <th style='width:100px; text-align:right;'>Amount</th>
            </tr>
        </thead>
        <tbody>
            {$params['itemsHtml']}
        </tbody>
    </table>

    <div class='totals'>
        <table>
            <tr>
                <td class='label'>Items Subtotal</td>
                <td class='value'>\$" . number_format($request['items_total'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>Service Fee</td>
                <td class='value'>\$" . number_format($request['service_fee'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>Handling (" . number_format($request['total_weight_kg'] ?? 0, 1) . " kg × \$0.20/kg)</td>
                <td class='value'>\$" . number_format($request['handling_fee'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>Delivery Fee</td>
                <td class='value'>\$" . number_format($request['delivery_fee'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>Subtotal</td>
                <td class='value'>\$" . number_format($request['subtotal'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>GST (5%)</td>
                <td class='value'>\$" . number_format($request['gst_amount'] ?? 0, 2) . "</td>
            </tr>
            <tr>
                <td class='label'>QST (9.975%)</td>
                <td class='value'>\$" . number_format($request['qst_amount'] ?? 0, 2) . "</td>
            </tr>
            " . (($request['tip_amount'] ?? 0) > 0 ? "
            <tr>
                <td class='label'>Tip " . ((int)($request['tip_percentage'] ?? 0) > 0 ? "(" . (int)$request['tip_percentage'] . "%)" : "(Custom)") . "</td>
                <td class='value'>\$" . number_format($request['tip_amount'], 2) . "</td>
            </tr>" : "") . "
            <tr class='total-row'>
                <td class='label'><strong>Total</strong></td>
                <td class='value'><strong>\$" . number_format($request['total_amount'], 2) . " CAD</strong></td>
            </tr>
        </table>
    </div>

    {$paymentSection}
    {$deliverySection}

    <div class='terms'>
        <h4>Terms & Conditions</h4>
        <p>1. All prices are in Canadian Dollars (CAD).</p>
        <p>2. Prices include procurement and handling services.</p>
        <p>3. Delivery times are estimates and may vary based on product availability.</p>
        <p>4. All sales are final once procurement has begun.</p>
        <p>5. For questions or concerns, contact support@ocsapp.ca.</p>
    </div>

    <div class='footer'>
        <p>Thank you for your business!</p>
        <p>OCS Distribution Services | ocsapp.ca | Generated on " . date('F j, Y \a\t g:i A') . "</p>
    </div>
</body>
</html>";
    }
}
