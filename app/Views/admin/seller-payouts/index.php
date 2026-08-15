<?php
$pageTitle = 'Seller Payouts';
$currentPage = 'seller-payouts';
$payouts = $payouts ?? [];
$stats = $stats ?? [];
$shops = $shops ?? [];
$batches = $batches ?? [];
$statusFilter = $statusFilter ?? 'pending';
$shopFilter = $shopFilter ?? 0;
$batchFilter = $batchFilter ?? 0;
ob_start();
?>

<style>
  .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; flex-wrap: wrap; gap: 16px; }
  .page-title { font-size: 28px; font-weight: 700; color: var(--dark); }
  .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 28px; }
  .stat-card { background: white; border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-sm); border-left: 4px solid var(--primary); }
  .stat-card .stat-value { font-size: 26px; font-weight: 700; }
  .stat-card .stat-label { font-size: 13px; color: #888; margin-top: 4px; }
  .filters-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
  .filters-bar select { padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; font-size: 14px; }
  .payouts-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  table.admin-table { width: 100%; border-collapse: collapse; font-size: 13px; }
  table.admin-table th { text-align: left; padding: 12px 16px; background: #fafafa; color: #888; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #f0f0f0; }
  table.admin-table td { padding: 12px 16px; border-bottom: 1px solid #f5f5f5; }
  .amount-negative { color: #c62828; }
  .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
  .badge-pending { background: #fff3e0; color: #e65100; }
  .badge-paid { background: #e8f5e9; color: #2e7d32; }
  .badge-held { background: #fce4ec; color: #c62828; }
  .btn-mark-paid { padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; border: none; cursor: pointer; background: #00b207; color: #fff; }
  .btn-mark-paid:hover { background: #009206; }
  .table-scroll { overflow-x: auto; }
  .batches-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 24px; }
  .batches-card h3 { padding: 16px 16px 0; font-size: 15px; color: var(--dark); }
  .badge-open { background: #fff3e0; color: #e65100; }
  .badge-completed { background: #e8f5e9; color: #2e7d32; }
</style>

<div class="page-header">
  <div class="page-title">Seller Payouts</div>
</div>

<div class="batches-card">
  <h3>Weekly Payout Batches</h3>
  <?php if (empty($batches)): ?>
    <p style="color:#aaa;padding:16px;">No batches generated yet - the Monday cron creates one once a shop clears the $25 rollover threshold.</p>
  <?php else: ?>
    <div class="table-scroll">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Batch</th>
          <th>Date</th>
          <th>Shops</th>
          <th>Items</th>
          <th>Total</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($batches as $b): ?>
          <tr>
            <td><?= htmlspecialchars($b['batch_number']) ?></td>
            <td><?= date('M j, Y', strtotime($b['batch_date'])) ?></td>
            <td><?= (int)$b['shop_count'] ?></td>
            <td><?= (int)$b['item_count'] ?></td>
            <td><strong>$<?= number_format((float)$b['total_amount'], 2) ?></strong></td>
            <td><span class="badge badge-<?= htmlspecialchars($b['status']) ?>"><?= ucfirst($b['status']) ?></span></td>
            <td>
              <a href="<?= url('admin/seller-payouts') ?>?batch_id=<?= (int)$b['id'] ?>&status=" style="font-size:12px;">View items</a>
              <?php if ($b['status'] === 'open'): ?>
                &nbsp;|&nbsp;
                <form method="POST" action="<?= url('admin/seller-payouts/mark-batch-paid') ?>" style="display:inline;" onsubmit="return confirm('Mark the entire batch as paid? This assumes you already sent every transfer in it outside the system.');">
                  <?= csrfField() ?>
                  <input type="hidden" name="batch_id" value="<?= (int)$b['id'] ?>">
                  <button type="submit" class="btn-mark-paid">Mark Batch Paid</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-value">$<?= number_format((float)($stats['pending_total'] ?? 0), 2) ?></div>
    <div class="stat-label">Pending Balance (all sellers)</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int)($stats['pending_count'] ?? 0) ?></div>
    <div class="stat-label">Pending Payouts</div>
  </div>
  <div class="stat-card">
    <div class="stat-value"><?= (int)($stats['held_count'] ?? 0) ?></div>
    <div class="stat-label">Held (chargeback exceeds paid payout)</div>
  </div>
</div>

<form method="GET" action="<?= url('admin/seller-payouts') ?>" class="filters-bar">
  <select name="status" onchange="this.form.submit()">
    <option value="">All statuses</option>
    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="paid" <?= $statusFilter === 'paid' ? 'selected' : '' ?>>Paid</option>
    <option value="held" <?= $statusFilter === 'held' ? 'selected' : '' ?>>Held</option>
  </select>
  <select name="shop_id" onchange="this.form.submit()">
    <option value="0">All shops</option>
    <?php foreach ($shops as $s): ?>
      <option value="<?= (int)$s['id'] ?>" <?= $shopFilter === (int)$s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
    <?php endforeach; ?>
  </select>
  <select name="batch_id" onchange="this.form.submit()">
    <option value="0">All batches</option>
    <?php foreach ($batches as $b): ?>
      <option value="<?= (int)$b['id'] ?>" <?= $batchFilter === (int)$b['id'] ? 'selected' : '' ?>><?= htmlspecialchars($b['batch_number']) ?></option>
    <?php endforeach; ?>
  </select>
</form>

<div class="payouts-card">
  <?php if (empty($payouts)): ?>
    <p style="color:#aaa;text-align:center;padding:40px;">No payouts match this filter.</p>
  <?php else: ?>
    <div class="table-scroll">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Shop</th>
          <th>Order</th>
          <th>Date</th>
          <th>Subtotal</th>
          <th>Commission</th>
          <th>Processing Fee</th>
          <th>Chargeback</th>
          <th>Net Payout</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($payouts as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['shop_name']) ?></td>
            <td>#<?= htmlspecialchars($p['order_number']) ?></td>
            <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
            <td>$<?= number_format((float)$p['subtotal'], 2) ?></td>
            <td class="amount-negative">-$<?= number_format((float)$p['commission_amount'], 2) ?></td>
            <td class="amount-negative">-$<?= number_format((float)$p['processing_fee_amount'], 2) ?></td>
            <td class="amount-negative"><?= (float)$p['chargeback_amount'] > 0 ? '-$' . number_format((float)$p['chargeback_amount'], 2) : '-' ?></td>
            <td><strong>$<?= number_format((float)$p['net_payout_amount'] - (float)$p['chargeback_amount'], 2) ?></strong></td>
            <td><span class="badge badge-<?= htmlspecialchars($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
            <td>
              <?php if ($p['status'] === 'pending'): ?>
                <form method="POST" action="<?= url('admin/seller-payouts/mark-paid') ?>" onsubmit="return confirm('Mark this payout as paid? This assumes you already paid the seller outside the system.');">
                  <?= csrfField() ?>
                  <input type="hidden" name="payout_ids[]" value="<?= (int)$p['id'] ?>">
                  <button type="submit" class="btn-mark-paid">Mark Paid</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
