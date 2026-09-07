<?php
$orders = $orders ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalOrders = $totalOrders ?? 0;
$todayStats = $todayStats ?? [];
$status = $status ?? '';
$date = $date ?? '';

// Index today stats by status
$todayByStatus = [];
foreach ($todayStats as $ts) {
    $todayByStatus[$ts['status']] = $ts;
}

$pageTitle = 'Orders';
require __DIR__ . '/../layout-header.php';
?>

<style>
  .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; }
  .card-title { font-size: 18px; font-weight: 700; color: var(--gray-700); margin-bottom: 16px; }
  .today-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 20px; }
  .today-stat { background: var(--gray-50); border-radius: 8px; padding: 14px; text-align: center; }
  .today-stat .num { font-size: 22px; font-weight: 700; }
  .today-stat .lbl { font-size: 11px; color: var(--gray-600); margin-top: 2px; }
  .filter-bar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
  .status-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
  .status-tab { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; color: var(--gray-700); background: var(--gray-50); border: 1px solid var(--gray-200); }
  .status-tab.active { background: var(--primary); color: #fff; border-color: var(--primary); }
  .orders-table { width: 100%; border-collapse: collapse; font-size: 14px; }
  .orders-table th { text-align: left; padding: 10px 12px; font-size: 12px; color: var(--gray-600); font-weight: 700; text-transform: uppercase; border-bottom: 1px solid var(--gray-200); }
  .orders-table td { padding: 16px 12px; border-top: 1px solid var(--gray-100); vertical-align: middle; }
  .orders-table tr:hover { background: var(--gray-50); }
  .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .badge-pending { background: #fff3e0; color: #e65100; }
  .badge-confirmed, .badge-processing { background: #e3f2fd; color: #1565c0; }
  .badge-ready { background: #ede9fe; color: #5b21b6; }
  .badge-completed, .badge-delivered { background: #dcfce7; color: #166534; }
  .badge-cancelled { background: #fee2e2; color: #991b1b; }
  .status-select { padding: 5px 8px; border: 1px solid var(--gray-200); border-radius: 6px; font-size: 12px; cursor: pointer; }
  .empty-state { text-align: center; padding: 60px 20px; color: var(--gray-600); }
  .empty-state i { font-size: 56px; color: var(--gray-300); display: block; margin-bottom: 16px; }
  .pagination { display: flex; gap: 8px; justify-content: center; margin-top: 20px; }
  .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 14px; text-decoration: none; }
  .pagination a { background: var(--gray-50); color: var(--gray-700); }
  .pagination a:hover { background: #e8f5e9; color: var(--primary); }
  .pagination span { background: var(--primary); color: #fff; font-weight: 600; }
  .date-input { padding: 7px 12px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 13px; }
  .btn-filter { padding: 7px 14px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; }
</style>

<div class="card">
    <h2 class="card-title">
        Orders <span style="font-size:14px;color:var(--gray-600);font-weight:400;">(<?= (int)$totalOrders ?> total)</span>
    </h2>

    <!-- Today's snapshot -->
    <div class="today-stats">
        <?php
        $pendingToday  = (int)($todayByStatus['pending']['count'] ?? 0);
        $processingToday = (int)($todayByStatus['processing']['count'] ?? 0) + (int)($todayByStatus['confirmed']['count'] ?? 0);
        $completedToday = (int)($todayByStatus['completed']['count'] ?? 0) + (int)($todayByStatus['delivered']['count'] ?? 0);
        $revenueToday = array_sum(array_column($todayStats, 'total_amount'));
        ?>
        <div class="today-stat"><div class="num" style="color:#e65100;"><?= $pendingToday ?></div><div class="lbl">Today Pending</div></div>
        <div class="today-stat"><div class="num" style="color:#1565c0;"><?= $processingToday ?></div><div class="lbl">Today Processing</div></div>
        <div class="today-stat"><div class="num" style="color:#166534;"><?= $completedToday ?></div><div class="lbl">Today Completed</div></div>
        <div class="today-stat"><div class="num" style="color:var(--primary);">$<?= number_format((float)$revenueToday, 0) ?></div><div class="lbl">Today Revenue</div></div>
    </div>

    <!-- Status filter tabs -->
    <div class="status-tabs">
        <a href="<?= url('seller/orders') ?>" class="status-tab <?= $status === '' ? 'active' : '' ?>">All</a>
        <a href="<?= url('seller/orders') ?>?status=pending" class="status-tab <?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
        <a href="<?= url('seller/orders') ?>?status=confirmed" class="status-tab <?= $status === 'confirmed' ? 'active' : '' ?>">Confirmed</a>
        <a href="<?= url('seller/orders') ?>?status=processing" class="status-tab <?= $status === 'processing' ? 'active' : '' ?>">Processing</a>
        <a href="<?= url('seller/orders') ?>?status=ready" class="status-tab <?= $status === 'ready' ? 'active' : '' ?>">Ready</a>
        <a href="<?= url('seller/orders') ?>?status=completed" class="status-tab <?= $status === 'completed' ? 'active' : '' ?>">Completed</a>
        <a href="<?= url('seller/orders') ?>?status=cancelled" class="status-tab <?= $status === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
    </div>

    <!-- Date filter -->
    <form method="GET" action="<?= url('seller/orders') ?>" class="filter-bar">
        <?php if ($status): ?><input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>"><?php endif; ?>
        <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="date-input">
        <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
        <?php if ($date): ?><a href="<?= url('seller/orders') ?><?= $status ? '?status='.htmlspecialchars($status) : '' ?>" style="font-size:13px;color:var(--gray-600);">Clear</a><?php endif; ?>
    </form>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p style="font-size:16px;font-weight:600;color:var(--gray-700);margin-bottom:8px;">No orders found</p>
            <p>Orders from customers will appear here.</p>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong></td>
                            <td><?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></td>
                            <td><?= (int)($order['items_count'] ?? 0) ?> item(s)</td>
                            <td style="font-weight:600;">$<?= number_format((float)$order['total'], 2) ?></td>
                            <td><span class="badge badge-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span></td>
                            <td style="color:var(--gray-600);font-size:12px;"><?= date('M j, g:i A', strtotime($order['created_at'])) ?></td>
                            <td>
                                <?php if (in_array($order['status'], ['pending','confirmed','processing'])): ?>
                                    <form method="POST" action="<?= url('seller/orders/update-status') ?>" style="display:inline-flex;gap:6px;align-items:center;">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                        <select name="status" class="status-select">
                                            <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirm</option>
                                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                            <option value="ready" <?= $order['status'] === 'ready' ? 'selected' : '' ?>>Ready</option>
                                            <option value="cancelled">Cancel</option>
                                        </select>
                                        <button type="submit" class="btn-filter" style="padding:5px 10px;font-size:12px;">Update</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:var(--gray-400);font-size:12px;">&mdash;</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $date ? '&date='.urlencode($date) : '' ?>">&laquo;</a>
                <?php endif; ?>
                <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                    <?php if ($p === $currentPage): ?>
                        <span><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $date ? '&date='.urlencode($date) : '' ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $date ? '&date='.urlencode($date) : '' ?>">&raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Submit status updates via fetch so the seller stays on this page
// (the endpoint returns JSON, not a redirect)
document.querySelectorAll('form[action*="update-status"]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        fetch(form.action, { method: 'POST', body: new FormData(form) })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Update failed.');
                    btn.disabled = false;
                }
            })
            .catch(function () {
                alert('Update failed. Please try again.');
                btn.disabled = false;
            });
    });
});
</script>

<?php require __DIR__ . '/../layout-footer.php'; ?>
