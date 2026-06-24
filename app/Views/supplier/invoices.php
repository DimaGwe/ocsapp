<?php require __DIR__ . '/layout-header.php'; ?>

<style>
  .stats-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
  .stat-card { background: white; border-radius: 12px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
  .stat-label { font-size: 13px; color: var(--gray-400); font-weight: 500; margin-bottom: 6px; }
  .stat-value { font-size: 24px; font-weight: 700; color: var(--gray-700); font-family: 'SF Mono', monospace; }

  .card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px; }
  .card-title { font-size: 16px; font-weight: 700; color: var(--gray-700); margin-bottom: 18px; display: flex; align-items: center; gap: 8px; }

  table { width: 100%; border-collapse: collapse; }
  th { padding: 12px 14px; text-align: left; font-size: 12px; font-weight: 600; color: var(--gray-400); text-transform: uppercase; background: var(--gray-50); border-bottom: 2px solid var(--gray-200); }
  td { padding: 12px 14px; font-size: 14px; color: var(--gray-700); border-bottom: 1px solid var(--gray-100); }
  tr:hover td { background: var(--gray-50); }

  .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
  .badge-sent { background: #dbeafe; color: #1d4ed8; }
  .badge-partial { background: #fef3c7; color: #92400e; }
  .badge-paid { background: #d1fae5; color: #065f46; }
  .badge-overdue { background: #fee2e2; color: #991b1b; }
  .badge-draft { background: #f3f4f6; color: #6b7280; }
  .badge-cancelled { background: #f3f4f6; color: #9ca3af; }

  .amount { font-weight: 600; font-family: 'SF Mono', monospace; }

  .payment-item { padding: 12px; border: 1px solid var(--gray-100); border-radius: 8px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; }
  .method-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: var(--gray-100); color: var(--gray-600); text-transform: uppercase; }

  .empty-state { text-align: center; padding: 40px; color: var(--gray-400); }
  .empty-state i { font-size: 40px; margin-bottom: 12px; }

  @media (max-width: 768px) { .stats-row { grid-template-columns: 1fr; } }
</style>

<!-- Stats -->
<div class="stats-row">
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Total facturé' : 'Total Invoiced' ?></div>
    <div class="stat-value">$<?= number_format($stats['total_invoiced'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Total payé' : 'Total Paid' ?></div>
    <div class="stat-value" style="color:#059669;">$<?= number_format($stats['total_paid'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Solde en souffrance' : 'Outstanding Balance' ?></div>
    <div class="stat-value" style="color:<?= ($stats['total_outstanding'] ?? 0) > 0 ? '#dc2626' : '#059669' ?>;">$<?= number_format($stats['total_outstanding'] ?? 0, 2) ?></div>
    <div style="font-size:12px;color:var(--gray-400);margin-top:4px;"><?= $stats['unpaid_count'] ?? 0 ?> <?= $fr ? 'facture' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') . ' impayée' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') : 'unpaid invoice' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') ?></div>
  </div>
</div>

<!-- Invoices Table -->
<div class="card">
  <h3 class="card-title"><i class="fas fa-file-invoice-dollar" style="color:var(--primary);"></i> <?= $fr ? 'Factures' : 'Invoices' ?></h3>

  <?php if (empty($invoices)): ?>
    <div class="empty-state">
      <i class="fas fa-file-invoice"></i>
      <p><?= $fr ? 'Aucune facture pour l\'instant.' : 'No invoices yet.' ?></p>
      <p style="font-size:13px;"><?= $fr ? 'Les factures apparaîtront ici une fois vos bons de commande complétés.' : 'Invoices will appear here after your purchase orders are completed.' ?></p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Facture #' : 'Invoice #' ?></th>
        <th><?= $fr ? 'BC #' : 'PO #' ?></th>
        <th><?= $fr ? 'Total' : 'Total' ?></th>
        <th><?= $fr ? 'Payé' : 'Paid' ?></th>
        <th><?= $fr ? 'Solde' : 'Balance' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th><?= $fr ? 'Date d\'échéance' : 'Due Date' ?></th>
        <th style="text-align:center;">PDF</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
      <tr>
        <td><a href="<?= url('supplier/invoices/view?id=' . $inv['id']) ?>" style="color:var(--primary);font-weight:700;text-decoration:none;"><?= htmlspecialchars($inv['invoice_number']) ?></a></td>
        <td style="color:var(--gray-400);"><?= htmlspecialchars($inv['po_number'] ?? '—') ?></td>
        <td class="amount">$<?= number_format($inv['total_amount'], 2) ?></td>
        <td class="amount" style="color:#059669;">$<?= number_format($inv['amount_paid'], 2) ?></td>
        <td class="amount" style="color:<?= $inv['balance_due'] > 0 ? '#dc2626' : '#059669' ?>;">$<?= number_format($inv['balance_due'], 2) ?></td>
        <td><span class="badge badge-<?= $inv['status'] ?>"><?= ucfirst($inv['status']) ?></span></td>
        <td style="<?= $inv['status'] === 'overdue' ? 'color:#dc2626;font-weight:600;' : '' ?>">
          <?= date('M j, Y', strtotime($inv['due_date'])) ?>
        </td>
        <td style="text-align:center;">
          <a href="<?= url('supplier/invoices/download-pdf?id=' . $inv['id']) ?>" title="Download PDF" style="color:#00b207; font-size:16px;">
            <i class="fas fa-file-pdf"></i>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php
  $baseUrl = 'supplier/invoices';
  $queryParams = [];
  require __DIR__ . '/../components/pagination.php';
?>

<!-- Recent Payments -->
<?php if (!empty($recentPayments)): ?>
<div class="card">
  <h3 class="card-title"><i class="fas fa-money-check-alt" style="color:#059669;"></i> <?= $fr ? 'Paiements récents' : 'Recent Payments' ?></h3>
  <?php foreach ($recentPayments as $p): ?>
  <div class="payment-item">
    <div>
      <strong style="font-size:13px;"><?= htmlspecialchars($p['payment_number']) ?></strong>
      <div style="font-size:12px;color:var(--gray-400);margin-top:2px;">
        <?= date('M j, Y', strtotime($p['payment_date'])) ?>
        &middot; <?= $fr ? 'Facture :' : 'Invoice:' ?> <?= htmlspecialchars($p['invoice_number']) ?>
        <?php if ($p['reference_number']): ?> &middot; <?= $fr ? 'Réf :' : 'Ref:' ?> <?= htmlspecialchars($p['reference_number']) ?><?php endif; ?>
      </div>
    </div>
    <div style="text-align:right;">
      <div class="amount" style="color:#059669;">$<?= number_format($p['amount_applied'], 2) ?></div>
      <span class="method-badge"><?= ucfirst(str_replace('_', ' ', $p['payment_method'])) ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
