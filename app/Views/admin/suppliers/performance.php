<?php
$pageTitle = 'Supplier Performance';
$currentPage = 'suppliers';
ob_start();

// Sort helper
function sortLink(string $col, string $label, string $currentSort, string $currentDir): string {
    $newDir = ($currentSort === $col && $currentDir === 'desc') ? 'asc' : 'desc';
    $icon = '';
    if ($currentSort === $col) {
        $icon = $currentDir === 'desc'
            ? ' <i class="fas fa-sort-down" style="color:var(--primary);"></i>'
            : ' <i class="fas fa-sort-up" style="color:var(--primary);"></i>';
    } else {
        $icon = ' <i class="fas fa-sort" style="color:var(--gray-400);font-size:10px;"></i>';
    }
    $url = url('admin/suppliers/performance?sort=' . $col . '&dir=' . $newDir);
    return "<a href=\"{$url}\" style=\"color:inherit;text-decoration:none;white-space:nowrap;\">{$label}{$icon}</a>";
}
?>

<style>
  .perf-header { margin-bottom: 28px; display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; flex-wrap: wrap; }
  .perf-title { font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
  .perf-sub { font-size: 14px; color: var(--gray-500); }
  .totals-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }
  .total-card { background: white; border-radius: var(--radius-xl); padding: 20px 24px; box-shadow: var(--shadow-sm); }
  .total-label { font-size: 12px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
  .total-value { font-size: 28px; font-weight: 700; color: var(--dark); }
  .table-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  table { width: 100%; border-collapse: collapse; }
  thead th { padding: 12px 16px; background: var(--gray-50); font-size: 12px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: .04em; border-bottom: 2px solid var(--border); text-align: left; }
  thead th.right { text-align: right; }
  tbody td { padding: 14px 16px; border-bottom: 1px solid var(--border); font-size: 14px; vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: var(--gray-50); }
  .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
  .badge.active { background: #dcfce7; color: #166534; }
  .badge.inactive { background: #f3f4f6; color: #374151; }
  .badge.suspended { background: #fee2e2; color: #991b1b; }
  .badge.pending_verification { background: #fef3c7; color: #92400e; }
  .rate-bar { height: 6px; background: var(--gray-200); border-radius: 3px; margin-top: 4px; }
  .rate-fill { height: 6px; border-radius: 3px; background: var(--primary); }
  .empty-cell { text-align: center; padding: 60px; color: var(--gray-400); }
</style>

<div class="perf-header">
  <div>
    <div class="perf-title">Supplier Performance</div>
    <div class="perf-sub">Order acceptance rates, spend, and activity across all suppliers</div>
  </div>
  <div style="display:flex;gap:10px;">
    <a href="<?= url('admin/suppliers') ?>" class="btn btn-secondary" style="padding:10px 18px;font-size:13px;">
      <i class="fas fa-arrow-left"></i> Back to Suppliers
    </a>
  </div>
</div>

<!-- Totals -->
<div class="totals-grid">
  <div class="total-card">
    <div class="total-label">Active Suppliers</div>
    <div class="total-value"><?= number_format($totals['total_suppliers'] ?? 0) ?></div>
  </div>
  <div class="total-card">
    <div class="total-label">Total Purchase Orders</div>
    <div class="total-value"><?= number_format($totals['total_orders'] ?? 0) ?></div>
  </div>
  <div class="total-card">
    <div class="total-label">Total Spend</div>
    <div class="total-value">$<?= number_format($totals['total_spend'] ?? 0, 2) ?></div>
  </div>
  <div class="total-card">
    <div class="total-label">Avg Order Value</div>
    <div class="total-value">$<?= number_format($totals['avg_order_value'] ?? 0, 2) ?></div>
  </div>
</div>

<!-- Per-Supplier Table -->
<div class="table-card">
  <table>
    <thead>
      <tr>
        <th>Supplier</th>
        <th class="right"><?= sortLink('total_orders', 'Orders', $sort, $dir) ?></th>
        <th class="right"><?= sortLink('accepted', 'Accepted', $sort, $dir) ?></th>
        <th class="right"><?= sortLink('declined', 'Declined', $sort, $dir) ?></th>
        <th style="min-width:140px;"><?= sortLink('acceptance_rate', 'Acceptance Rate', $sort, $dir) ?></th>
        <th class="right"><?= sortLink('total_spend', 'Total Spend', $sort, $dir) ?></th>
        <th class="right"><?= sortLink('avg_order_value', 'Avg Order', $sort, $dir) ?></th>
        <th>Status</th>
        <th class="right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($suppliers)): ?>
        <?php foreach ($suppliers as $s): ?>
          <tr>
            <td>
              <div style="font-weight:600;color:var(--dark);"><?= htmlspecialchars($s['company_name'] ?: $s['name']) ?></div>
              <div style="font-size:12px;color:var(--gray-500);"><?= htmlspecialchars($s['email']) ?></div>
            </td>
            <td class="right" style="font-weight:600;"><?= number_format($s['total_orders']) ?></td>
            <td class="right" style="color:#059669;font-weight:600;"><?= number_format($s['accepted']) ?></td>
            <td class="right" style="color:#dc2626;"><?= number_format($s['declined']) ?></td>
            <td>
              <div style="font-size:13px;font-weight:600;color:<?= $s['acceptance_rate'] >= 80 ? '#059669' : ($s['acceptance_rate'] >= 50 ? '#d97706' : '#dc2626') ?>;">
                <?= number_format($s['acceptance_rate'], 1) ?>%
              </div>
              <div class="rate-bar">
                <div class="rate-fill" style="width:<?= min(100, $s['acceptance_rate']) ?>%;background:<?= $s['acceptance_rate'] >= 80 ? '#059669' : ($s['acceptance_rate'] >= 50 ? '#f59e0b' : '#ef4444') ?>;"></div>
              </div>
            </td>
            <td class="right" style="font-weight:600;">$<?= number_format($s['total_spend'], 2) ?></td>
            <td class="right" style="color:var(--gray-600);">$<?= number_format($s['avg_order_value'], 2) ?></td>
            <td>
              <span class="badge <?= $s['status'] ?>">
                <?= $s['status'] === 'pending_verification' ? 'Pending' : ucfirst($s['status']) ?>
              </span>
            </td>
            <td class="right">
              <a href="<?= url('admin/suppliers/edit?id=' . $s['id']) ?>" style="color:var(--primary);font-size:13px;font-weight:600;text-decoration:none;">
                <i class="fas fa-edit"></i> Edit
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="9" class="empty-cell">
            <i class="fas fa-chart-bar" style="font-size:40px;display:block;margin-bottom:12px;"></i>
            No suppliers found
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
