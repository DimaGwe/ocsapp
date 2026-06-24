<?php
$pageTitle = 'Supplier Payables';
$currentPage = 'payables';
ob_start();
?>

<style>
  .page-header {
    display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px;
  }
  .page-title { font-size: 28px; font-weight: 700; color: var(--dark); }
  .header-actions { display: flex; gap: 10px; }

  .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 28px; }
  .stat-card {
    background: white; border-radius: var(--radius-xl); padding: 24px;
    box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary);
  }
  .stat-card.overdue { border-left-color: #ef4444; }
  .stat-card.due-soon { border-left-color: #f59e0b; }
  .stat-card.paid { border-left-color: #10b981; }
  .stat-label { font-size: 13px; color: #6b7280; font-weight: 500; margin-bottom: 6px; }
  .stat-value { font-size: 26px; font-weight: 700; color: #1f2937; }
  .stat-count { font-size: 12px; color: #9ca3af; margin-top: 4px; }

  .filters-card { background: white; border-radius: var(--radius-xl); padding: 20px; margin-bottom: 24px; box-shadow: var(--shadow-sm); }
  .filters-grid { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
  .form-select {
    padding: 9px 14px; border: 2px solid var(--border); border-radius: var(--radius-md);
    font-size: 13px; min-width: 160px;
  }
  .btn-filter { padding: 9px 20px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px; }
  .btn-export {
    padding: 9px 20px; background: white; color: #374151; border: 2px solid var(--border);
    border-radius: var(--radius-md); cursor: pointer; font-weight: 600; font-size: 13px;
    text-decoration: none; display: inline-flex; align-items: center; gap: 6px;
  }
  .btn-export:hover { border-color: var(--primary); color: var(--primary); }

  .table-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  th { padding: 14px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
  td { padding: 14px 16px; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; }
  tr:hover td { background: #f9fafb; }

  .badge {
    display: inline-block; padding: 4px 10px; border-radius: 20px;
    font-size: 11px; font-weight: 600; text-transform: uppercase;
  }
  .badge-draft { background: #f3f4f6; color: #6b7280; }
  .badge-sent { background: #dbeafe; color: #1d4ed8; }
  .badge-partial { background: #fef3c7; color: #92400e; }
  .badge-paid { background: #d1fae5; color: #065f46; }
  .badge-overdue { background: #fee2e2; color: #991b1b; }
  .badge-cancelled { background: #f3f4f6; color: #9ca3af; }

  .amount { font-weight: 600; font-family: 'SF Mono', 'Cascadia Code', monospace; }
  .amount-due { color: #dc2626; }
  .amount-paid { color: #059669; }

  .empty-state { text-align: center; padding: 60px 20px; color: #9ca3af; }
  .empty-state i { font-size: 48px; margin-bottom: 16px; }

  @media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
  }
</style>

<div class="page-header">
  <h1 class="page-title"><i class="fas fa-file-invoice-dollar" style="color:var(--primary);margin-right:8px;"></i> Supplier Payables</h1>
  <div class="header-actions">
    <a href="<?= url('admin/payables/export') ?>" class="btn-export"><i class="fas fa-download"></i> Export CSV</a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Outstanding</div>
    <div class="stat-value">$<?= number_format($stats['total_outstanding'] ?? 0, 2) ?></div>
    <div class="stat-count"><?= $stats['total_invoices'] ?? 0 ?> total invoices</div>
  </div>
  <div class="stat-card overdue">
    <div class="stat-label">Overdue</div>
    <div class="stat-value" style="color:#dc2626;">$<?= number_format($stats['total_overdue'] ?? 0, 2) ?></div>
    <div class="stat-count"><?= $stats['overdue_count'] ?? 0 ?> overdue invoices</div>
  </div>
  <div class="stat-card due-soon">
    <div class="stat-label">Due This Week</div>
    <div class="stat-value" style="color:#d97706;">$<?= number_format($stats['due_this_week'] ?? 0, 2) ?></div>
    <div class="stat-count"><?= $stats['due_this_week_count'] ?? 0 ?> invoices</div>
  </div>
  <div class="stat-card paid">
    <div class="stat-label">Total Paid</div>
    <div class="stat-value" style="color:#059669;">$<?= number_format($stats['total_paid'] ?? 0, 2) ?></div>
  </div>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/payables') ?>" class="filters-grid">
    <select name="status" class="form-select">
      <option value="">All Statuses</option>
      <option value="overdue" <?= $statusFilter === 'overdue' ? 'selected' : '' ?>>Overdue</option>
      <option value="sent" <?= $statusFilter === 'sent' ? 'selected' : '' ?>>Sent / Awaiting Payment</option>
      <option value="partial" <?= $statusFilter === 'partial' ? 'selected' : '' ?>>Partially Paid</option>
      <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
      <option value="draft" <?= $statusFilter === 'draft' ? 'selected' : '' ?>>Draft</option>
      <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
    </select>
    <select name="supplier_id" class="form-select">
      <option value="">All Suppliers</option>
      <?php foreach ($suppliers as $s): ?>
        <option value="<?= $s['id'] ?>" <?= $supplierFilter == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['company_name'] ?: $s['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
  </form>
</div>

<!-- Invoices Table -->
<div class="table-card">
  <?php if (empty($invoices)): ?>
    <div class="empty-state">
      <i class="fas fa-file-invoice-dollar"></i>
      <p>No invoices found.</p>
      <p style="font-size:13px;">Invoices are generated when purchase orders are completed.</p>
    </div>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Invoice #</th>
        <th>Supplier</th>
        <th>PO #</th>
        <th>Total</th>
        <th>Paid</th>
        <th>Balance</th>
        <th>Status</th>
        <th>Due Date</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($invoices as $inv): ?>
      <?php
        $isOverdue = in_array($inv['status'], ['overdue']);
        $isDueSoon = !$isOverdue && in_array($inv['status'], ['sent','partial']) && strtotime($inv['due_date']) <= strtotime('+7 days');
      ?>
      <tr>
        <td><strong><?= htmlspecialchars($inv['invoice_number']) ?></strong></td>
        <td><?= htmlspecialchars($inv['company_name'] ?: $inv['supplier_name']) ?></td>
        <td><?= $inv['po_number'] ? '<a href="' . url('admin/purchase-orders/view?id=' . $inv['po_id']) . '" style="color:var(--primary);">' . htmlspecialchars($inv['po_number']) . '</a>' : '—' ?></td>
        <td class="amount">$<?= number_format($inv['total_amount'], 2) ?></td>
        <td class="amount amount-paid">$<?= number_format($inv['amount_paid'], 2) ?></td>
        <td class="amount <?= $inv['balance_due'] > 0 ? 'amount-due' : '' ?>">$<?= number_format($inv['balance_due'], 2) ?></td>
        <td><span class="badge badge-<?= $inv['status'] ?>"><?= ucfirst($inv['status']) ?></span></td>
        <td style="<?= $isOverdue ? 'color:#dc2626;font-weight:600;' : ($isDueSoon ? 'color:#d97706;font-weight:600;' : '') ?>">
          <?= date('M j, Y', strtotime($inv['due_date'])) ?>
          <?php if ($isOverdue): ?><br><span style="font-size:11px;"><?= abs((int)((strtotime($inv['due_date']) - time()) / 86400)) ?> days overdue</span><?php endif; ?>
        </td>
        <td>
          <a href="<?= url('admin/payables/view?id=' . $inv['id']) ?>" style="color:var(--primary);font-weight:600;font-size:13px;">View</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
