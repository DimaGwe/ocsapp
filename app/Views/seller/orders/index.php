<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$cartCount = $cartCount ?? 0;
$orders = $orders ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalOrders = $totalOrders ?? 0;
$todayStats = $todayStats ?? [];
$status = $status ?? '';
$date = $date ?? '';
$user = user();

// Index today stats by status
$todayByStatus = [];
foreach ($todayStats as $ts) {
    $todayByStatus[$ts['status']] = $ts;
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Orders - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .seller-layout { display: flex; max-width: 1240px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .seller-sidebar { width: 220px; flex-shrink: 0; }
        .seller-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 16px; }
        .today-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; margin-bottom: 20px; }
        .today-stat { background: #f9f9f9; border-radius: 8px; padding: 14px; text-align: center; }
        .today-stat .num { font-size: 22px; font-weight: 700; }
        .today-stat .lbl { font-size: 11px; color: #888; margin-top: 2px; }
        .filter-bar { display: flex; gap: 10px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; }
        .status-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
        .status-tab { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 500; text-decoration: none; color: #555; background: #f5f5f5; border: 1px solid #e0e0e0; }
        .status-tab.active { background: #00b207; color: #fff; border-color: #00b207; }
        .orders-table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .orders-table th { text-align: left; padding: 10px 12px; font-size: 12px; color: #888; font-weight: 600; border-bottom: 1px solid #f0f0f0; }
        .orders-table td { padding: 12px; border-bottom: 1px solid #f5f5f5; vertical-align: middle; }
        .orders-table tr:last-child td { border-bottom: none; }
        .orders-table tr:hover { background: #fafafa; }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-confirmed, .badge-processing { background: #e3f2fd; color: #1565c0; }
        .badge-ready { background: #f3e5f5; color: #6a1b9a; }
        .badge-completed, .badge-delivered { background: #e8f5e9; color: #2e7d32; }
        .badge-cancelled { background: #fce4ec; color: #c62828; }
        .status-select { padding: 5px 8px; border: 1px solid #e0e0e0; border-radius: 6px; font-size: 12px; cursor: pointer; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        .pagination { display: flex; gap: 8px; justify-content: center; margin-top: 20px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 14px; text-decoration: none; }
        .pagination a { background: #f5f5f5; color: #333; }
        .pagination a:hover { background: #e8f5e9; color: #00b207; }
        .pagination span { background: #00b207; color: #fff; font-weight: 600; }
        .date-input { padding: 7px 12px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 13px; }
        .btn-filter { padding: 7px 14px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 13px; cursor: pointer; }
        @media (max-width: 768px) { .seller-layout { flex-direction: column; } .seller-sidebar { width: 100%; } .orders-table { font-size: 12px; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="seller-layout">
    <aside class="seller-sidebar">
        <div class="sidebar-card">
            <div style="font-weight:600;margin-bottom:12px;font-size:15px;">Seller Panel</div>
            <nav class="sidebar-nav">
                <a href="<?= url('seller/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('seller/orders') ?>" class="active"><i class="fas fa-box"></i> Orders</a>
                <a href="<?= url('seller/inventory') ?>"><i class="fas fa-cubes"></i> Inventory</a>
                <a href="<?= url('seller/shop/settings') ?>"><i class="fas fa-cog"></i> Shop Settings</a>
                <hr style="border:none;border-top:1px solid #f0f0f0;margin:6px 0;">
                <a href="#" style="color:#c62828;" onclick="event.preventDefault();document.getElementById('seller-logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            <form id="seller-logout-form" method="POST" action="<?= url('logout') ?>" style="display:none;"><?= csrfField() ?></form>
        </div>
    </aside>

    <main class="seller-main">
        <?php if ($flash = getFlash('error')): ?>
            <div style="background:#fce4ec;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('success')): ?>
            <div style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div class="section-card">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:16px;">
                Orders <span style="font-size:14px;color:#888;font-weight:400;">(<?= (int)$totalOrders ?> total)</span>
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
                <div class="today-stat"><div class="num" style="color:#2e7d32;"><?= $completedToday ?></div><div class="lbl">Today Completed</div></div>
                <div class="today-stat"><div class="num" style="color:#00b207;">$<?= number_format((float)$revenueToday, 0) ?></div><div class="lbl">Today Revenue</div></div>
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
                <?php if ($date): ?><a href="<?= url('seller/orders') ?><?= $status ? '?status='.htmlspecialchars($status) : '' ?>" style="font-size:13px;color:#888;">Clear</a><?php endif; ?>
            </form>

            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p style="font-size:16px;font-weight:500;color:#555;margin-bottom:8px;">No orders found</p>
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
                                    <td style="color:#888;font-size:12px;"><?= date('M j, g:i A', strtotime($order['created_at'])) ?></td>
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
                                            <span style="color:#aaa;font-size:12px;">&mdash;</span>
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
    </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
