<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = [
    'en' => [
        'page_title'    => 'My Invoices',
        'invoice_no'    => 'Invoice #',
        'request'       => 'Request',
        'subtotal'      => 'Subtotal',
        'total'         => 'Total',
        'status'        => 'Status',
        'due_date'      => 'Due Date',
        'paid_date'     => 'Paid',
        'action'        => 'Action',
        'view_request'  => 'View Request',
        'empty_title'   => 'No invoices yet',
        'empty_desc'    => 'Invoices will appear here once your requests have been reviewed and quoted.',
        'status_sent'      => 'Awaiting Payment',
        'status_paid'      => 'Paid',
        'status_overdue'   => 'Overdue',
        'status_cancelled' => 'Cancelled',
        'status_draft'     => 'Draft',
    ],
    'fr' => [
        'page_title'    => 'Mes Factures',
        'invoice_no'    => 'Facture #',
        'request'       => 'Demande',
        'subtotal'      => 'Sous-total',
        'total'         => 'Total',
        'status'        => 'Statut',
        'due_date'      => 'Date d\'&#233;ch&#233;ance',
        'paid_date'     => 'Pay&#233;e le',
        'action'        => 'Action',
        'view_request'  => 'Voir la demande',
        'empty_title'   => 'Aucune facture',
        'empty_desc'    => 'Les factures apparaîtront ici une fois vos demandes examinées et cotées.',
        'status_sent'      => 'En attente de paiement',
        'status_paid'      => 'Payée',
        'status_overdue'   => 'En retard',
        'status_cancelled' => 'Annulée',
        'status_draft'     => 'Brouillon',
    ],
];
$tr = $t[$currentLang] ?? $t['en'];

$statusConfig = [
    'sent'      => ['label' => $tr['status_sent'],      'color' => '#f59e0b', 'bg' => '#fef3c7'],
    'paid'      => ['label' => $tr['status_paid'],      'color' => '#059669', 'bg' => '#d1fae5'],
    'overdue'   => ['label' => $tr['status_overdue'],   'color' => '#dc2626', 'bg' => '#fee2e2'],
    'cancelled' => ['label' => $tr['status_cancelled'], 'color' => '#6b7280', 'bg' => '#f3f4f6'],
    'draft'     => ['label' => $tr['status_draft'],     'color' => '#6b7280', 'bg' => '#f3f4f6'],
];

$currentPage = 'invoices';
$pageTitle = $tr['page_title'] ?? 'My Invoices';
$_pageT = $t; // preserve before layout-header.php overwrites $t
require __DIR__ . '/../layout-header.php';
$t = $_pageT; unset($_pageT); // restore page-specific translations
?>
<style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .page-header h1 {
            font-size: 22px;
            font-weight: 600;
            color: #111827;
        }
        .page-header h1 i { color: #00b207; margin-right: 10px; }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        thead th { padding: 14px 20px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        tbody tr { border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #f9fafb; }
        tbody td { padding: 16px 20px; font-size: 14px; color: #374151; vertical-align: middle; }

        .invoice-number { font-weight: 600; color: #111827; }
        .request-number { color: #6b7280; font-size: 13px; }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .btn-view {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            background: #f3f4f6;
            color: #374151;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-view:hover { background: #e5e7eb; }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #9ca3af;
        }
        .empty-state i { font-size: 48px; margin-bottom: 16px; opacity: 0.4; }
        .empty-state h3 { font-size: 16px; color: #374151; margin-bottom: 8px; }
        .empty-state p { font-size: 14px; }
</style>

    <div class="page-header">
        <h1><i class="fas fa-file-invoice-dollar"></i> <?= $tr['page_title'] ?></h1>
    </div>

    <div class="card">
        <?php if (empty($invoices)): ?>
            <div class="empty-state">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3><?= $tr['empty_title'] ?></h3>
                <p><?= $tr['empty_desc'] ?></p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th><?= $tr['invoice_no'] ?></th>
                        <th><?= $tr['request'] ?></th>
                        <th><?= $tr['total'] ?></th>
                        <th><?= $tr['status'] ?></th>
                        <th><?= $tr['due_date'] ?></th>
                        <th><?= $tr['action'] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                        <?php
                        $cfg = $statusConfig[$inv['status']] ?? ['label' => ucfirst($inv['status']), 'color' => '#6b7280', 'bg' => '#f3f4f6'];
                        $dueFormatted  = $inv['due_date']  ? date('M j, Y', strtotime($inv['due_date']))  : '—';
                        $paidFormatted = $inv['paid_at']   ? date('M j, Y', strtotime($inv['paid_at']))   : null;
                        ?>
                        <tr>
                            <td>
                                <span class="invoice-number"><?= htmlspecialchars($inv['invoice_number']) ?></span>
                            </td>
                            <td>
                                <span class="request-number">#<?= htmlspecialchars($inv['request_number']) ?></span>
                            </td>
                            <td>
                                <strong>$<?= number_format($inv['total_amount'], 2) ?> CAD</strong>
                            </td>
                            <td>
                                <span class="badge" style="color: <?= $cfg['color'] ?>; background: <?= $cfg['bg'] ?>;">
                                    <?= $cfg['label'] ?>
                                </span>
                            </td>
                            <td>
                                <?= $dueFormatted ?>
                                <?php if ($paidFormatted): ?>
                                    <br><small style="color: #059669;"><?= $tr['paid_date'] ?>: <?= $paidFormatted ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= url('distribution/requests/show?id=' . (int)$inv['distribution_request_id']) ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> <?= $tr['view_request'] ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
