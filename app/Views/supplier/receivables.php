<?php require __DIR__ . '/layout-header.php'; ?>

<style>
  .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .page-title  { font-size:24px; font-weight:700; color:var(--gray-700); }

  .stats-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px; }
  .stat-card  { background:white; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,.08); border-left:4px solid var(--primary); }
  .stat-card.green { border-left-color:#10b981; }
  .stat-card.amber { border-left-color:#f59e0b; }
  .stat-card.red   { border-left-color:#ef4444; }
  .stat-label { font-size:12px; color:var(--gray-400); font-weight:600; text-transform:uppercase; letter-spacing:.4px; margin-bottom:4px; }
  .stat-value { font-size:22px; font-weight:700; color:var(--gray-700); font-family:'SF Mono',monospace; }
  .stat-sub   { font-size:11px; color:#9ca3af; margin-top:3px; }

  .card { background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.08); margin-bottom:24px; }
  .card-title { font-size:15px; font-weight:700; color:var(--gray-700); margin-bottom:16px; display:flex; align-items:center; gap:8px; }

  .filters-row { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:20px; }
  .form-select { padding:8px 12px; border:2px solid #e5e7eb; border-radius:8px; font-size:13px; color:#374151; background:white; }
  .form-select:focus { outline:none; border-color:var(--primary); }
  .btn-filter { padding:8px 18px; background:var(--primary); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600; font-size:13px; }

  table { width:100%; border-collapse:collapse; }
  th  { padding:12px 14px; text-align:left; font-size:11px; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; background:#f9fafb; border-bottom:2px solid #e5e7eb; }
  td  { padding:12px 14px; font-size:13px; color:#374151; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:#f9fafb; }

  .inv-link { font-weight:700; color:var(--primary); text-decoration:none; font-family:'SF Mono',monospace; }
  .amount   { font-weight:600; font-family:'SF Mono',monospace; }
  .amount.green { color:#059669; }
  .amount.red   { color:#dc2626; }

  .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.3px; }
  .badge-draft     { background:#f3f4f6; color:#6b7280; }
  .badge-sent      { background:#dbeafe; color:#1d4ed8; }
  .badge-partial   { background:#fef3c7; color:#92400e; }
  .badge-paid      { background:#d1fae5; color:#065f46; }
  .badge-overdue   { background:#fee2e2; color:#991b1b; }
  .badge-cancelled { background:#f3f4f6; color:#9ca3af; }

  .payment-row { display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6; font-size:13px; }
  .payment-row:last-child { border-bottom:none; }
  .payment-method { font-size:10px; font-weight:700; padding:2px 8px; border-radius:4px; background:#f3f4f6; color:#6b7280; text-transform:uppercase; }

  .empty-state { padding:40px; text-align:center; color:#9ca3af; }
  .empty-state i { font-size:36px; margin-bottom:10px; display:block; }

  .pagination { display:flex; justify-content:center; gap:8px; padding:16px; }
  .page-btn { padding:7px 12px; border:1px solid #e5e7eb; border-radius:6px; color:#374151; text-decoration:none; font-size:13px; }
  .page-btn.active { background:var(--primary); color:white; border-color:var(--primary); font-weight:600; }
  .page-btn:hover:not(.active) { border-color:var(--primary); color:var(--primary); }
</style>

<div class="page-header">
  <h2 class="page-title"><i class="fas fa-hand-holding-usd" style="color:var(--primary);margin-right:8px;"></i> <?= $fr ? 'Comptes clients' : 'Receivables' ?></h2>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label"><?= $fr ? 'Total facturé' : 'Total Invoiced' ?></div>
    <div class="stat-value">$<?= number_format($stats['total_invoiced'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card green">
    <div class="stat-label"><?= $fr ? 'Total reçu' : 'Total Received' ?></div>
    <div class="stat-value">$<?= number_format($stats['total_received'] ?? 0, 2) ?></div>
  </div>
  <div class="stat-card <?= ($stats['total_outstanding'] ?? 0) > 0 ? 'amber' : 'green' ?>">
    <div class="stat-label"><?= $fr ? 'En souffrance' : 'Outstanding' ?></div>
    <div class="stat-value">$<?= number_format($stats['total_outstanding'] ?? 0, 2) ?></div>
    <div class="stat-sub"><?= $stats['unpaid_count'] ?? 0 ?> <?= $fr ? 'facture' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') . ' impayée' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') : 'unpaid invoice' . (($stats['unpaid_count'] ?? 0) != 1 ? 's' : '') ?></div>
  </div>
  <?php if (($stats['overdue_count'] ?? 0) > 0): ?>
  <div class="stat-card red">
    <div class="stat-label"><?= $fr ? 'En retard' : 'Overdue' ?></div>
    <div class="stat-value"><?= $stats['overdue_count'] ?></div>
    <div class="stat-sub"><?= $fr ? 'facture' . ($stats['overdue_count'] != 1 ? 's' : '') . ' en souffrance' : 'invoice' . ($stats['overdue_count'] != 1 ? 's' : '') . ' past due' ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- Filter + Table -->
<div class="card">
  <h3 class="card-title"><i class="fas fa-file-invoice-dollar" style="color:var(--primary);"></i> <?= $fr ? 'Factures' : 'Invoices' ?></h3>

  <form method="GET" action="<?= url('supplier/receivables') ?>">
    <div class="filters-row">
      <select name="status" class="form-select">
        <option value=""><?= $fr ? 'Tous les statuts' : 'All Statuses' ?></option>
        <?php
        $recStatuses = $fr
          ? ['sent'=>'Envoyée','partial'=>'Partielle','paid'=>'Payée','overdue'=>'En retard','draft'=>'Brouillon','cancelled'=>'Annulée']
          : ['sent'=>'Sent','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue','draft'=>'Draft','cancelled'=>'Cancelled'];
        foreach ($recStatuses as $val => $label): ?>
        <option value="<?= $val ?>" <?= $statusFilter === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn-filter"><i class="fas fa-search"></i> <?= $fr ? 'Filtrer' : 'Filter' ?></button>
      <?php if ($statusFilter): ?>
        <a href="<?= url('supplier/receivables') ?>" style="font-size:13px;color:#6b7280;text-decoration:none;"><?= $fr ? 'Effacer' : 'Clear' ?></a>
      <?php endif; ?>
    </div>
  </form>

  <?php if (empty($invoices)): ?>
    <div class="empty-state">
      <i class="fas fa-file-invoice"></i>
      <p style="font-size:14px;font-weight:600;"><?= $fr ? 'Aucune facture trouvée' : 'No invoices found' ?></p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Facture #' : 'Invoice #' ?></th>
        <th><?= $fr ? 'BV # / BC #' : 'SO # / PO #' ?></th>
        <th><?= $fr ? 'Total' : 'Total' ?></th>
        <th><?= $fr ? 'Reçu' : 'Received' ?></th>
        <th><?= $fr ? 'Solde' : 'Outstanding' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th><?= $fr ? 'Échéance' : 'Due Date' ?></th>
        <th>PDF</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
      <tr>
        <td>
          <a href="<?= url('supplier/invoices/view?id=' . $inv['id']) ?>" class="inv-link">
            <?= htmlspecialchars($inv['invoice_number']) ?>
          </a>
        </td>
        <td>
          <?php if (!empty($inv['so_number'])): ?>
            <span style="font-family:'SF Mono',monospace;font-size:12px;font-weight:600;color:var(--primary);"><?= htmlspecialchars($inv['so_number']) ?></span><br>
          <?php endif; ?>
          <?php if (!empty($inv['po_number'])): ?>
            <span style="font-family:'SF Mono',monospace;font-size:11px;color:#6b7280;"><?= htmlspecialchars($inv['po_number']) ?></span>
          <?php else: ?>
            <span style="color:#9ca3af;">—</span>
          <?php endif; ?>
        </td>
        <td class="amount">$<?= number_format($inv['total_amount'], 2) ?></td>
        <td class="amount green">$<?= number_format($inv['amount_paid'], 2) ?></td>
        <td class="amount <?= $inv['balance_due'] > 0 ? 'red' : '' ?>">$<?= number_format($inv['balance_due'], 2) ?></td>
        <td>
          <?php
            $statusLabels = $fr
              ? ['draft'=>'Brouillon','sent'=>'Envoyée','partial'=>'Partielle','paid'=>'Payée','overdue'=>'En retard','cancelled'=>'Annulée']
              : ['draft'=>'Draft','sent'=>'Sent','partial'=>'Partial','paid'=>'Paid','overdue'=>'Overdue','cancelled'=>'Cancelled'];
            $s = $inv['status'];
          ?>
          <span class="badge badge-<?= htmlspecialchars($s) ?>"><?= $statusLabels[$s] ?? ucfirst($s) ?></span>
        </td>
        <td style="font-size:12px;color:#6b7280;">
          <?php if ($inv['due_date']): ?>
            <?= date('M j, Y', strtotime($inv['due_date'])) ?>
            <?php if ($s === 'overdue'): ?>
              <span style="color:#dc2626;font-size:11px;display:block;"><?= $fr ? 'En retard' : 'Overdue' ?></span>
            <?php endif; ?>
          <?php else: ?>—<?php endif; ?>
        </td>
        <td>
          <a href="<?= url('supplier/invoices/download-pdf?id=' . $inv['id']) ?>" style="color:#6b7280;font-size:14px;" title="Download PDF" target="_blank">
            <i class="fas fa-file-pdf"></i>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($totalPages > 1): ?>
  <div class="pagination">
    <?php if ($page > 1): ?>
      <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn">&laquo; <?= $fr ? 'Préc.' : 'Prev' ?></a>
    <?php endif; ?>
    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
      <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?>
      <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($statusFilter) ?>" class="page-btn"><?= $fr ? 'Suiv.' : 'Next' ?> &raquo;</a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>

<!-- Recent Payments -->
<?php if (!empty($recentPayments)): ?>
<div class="card">
  <h3 class="card-title"><i class="fas fa-money-bill-wave" style="color:#10b981;"></i> <?= $fr ? 'Paiements récents reçus' : 'Recent Payments Received' ?></h3>
  <?php foreach ($recentPayments as $pmt): ?>
  <div class="payment-row">
    <div>
      <strong style="font-size:13px;"><?= htmlspecialchars($pmt['invoice_number']) ?></strong>
      <?php if ($pmt['reference_number']): ?>
        <span style="font-size:11px;color:#9ca3af;margin-left:8px;"><?= $fr ? 'Réf :' : 'Ref:' ?> <?= htmlspecialchars($pmt['reference_number']) ?></span>
      <?php endif; ?>
      <div style="font-size:11px;color:#9ca3af;margin-top:2px;"><?= date('M j, Y', strtotime($pmt['payment_date'])) ?></div>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
      <span class="payment-method"><?= htmlspecialchars($pmt['payment_method'] ?? 'N/A') ?></span>
      <span class="amount green">+$<?= number_format($pmt['amount_applied'], 2) ?></span>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
