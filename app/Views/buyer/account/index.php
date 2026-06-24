<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$user = $user ?? [];
$stats = $stats ?? ['total_orders'=>0,'pending_orders'=>0,'completed_orders'=>0,'total_spent'=>0];
$recentOrders = $recentOrders ?? [];
$cartCount = $cartCount ?? 0;
$pageTitle = $pageTitle ?? 'My Account';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .account-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .account-sidebar { width: 240px; flex-shrink: 0; }
        .account-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-user { text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; margin-bottom: 12px; }
        .sidebar-avatar { width: 64px; height: 64px; border-radius: 50%; background: #00b207; color: #fff; font-size: 24px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .sidebar-name { font-weight: 600; font-size: 15px; }
        .sidebar-email { font-size: 12px; color: #888; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .stat-value { font-size: 28px; font-weight: 700; color: #00b207; }
        .stat-label { font-size: 13px; color: #888; margin-top: 4px; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }
        .section-title a { font-size: 13px; color: #00b207; text-decoration: none; font-weight: 500; }
        .order-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .order-row:last-child { border-bottom: none; }
        .order-id { font-weight: 600; font-size: 14px; }
        .order-meta { font-size: 12px; color: #888; margin-top: 2px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-completed, .badge-delivered { background: #e8f5e9; color: #2e7d32; }
        .badge-cancelled { background: #fce4ec; color: #c62828; }
        .badge-processing { background: #e3f2fd; color: #1565c0; }
        .empty-state { text-align: center; padding: 40px; color: #888; }
        .empty-state i { font-size: 48px; color: #ddd; display: block; margin-bottom: 16px; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="account-layout">
    <!-- Sidebar -->
    <aside class="account-sidebar">
        <div class="sidebar-card">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                <div class="sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('account') ?>" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('account/orders') ?>"><i class="fas fa-box"></i> My Orders</a>
                <a href="<?= url('account/addresses') ?>"><i class="fas fa-map-marker-alt"></i> Addresses</a>
                <a href="<?= url('account/wishlist') ?>"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="<?= url('account/settings') ?>"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>
    </aside>

    <!-- Main -->
    <main class="account-main">
        <?php if ($flash = getFlash('success')): ?>
            <div data-auto-dismiss style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;transition:opacity 0.6s ease;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('error')): ?>
            <div style="background:#fce4ec;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="stat-value"><?= (int)$stats['total_orders'] ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= (int)$stats['pending_orders'] ?></div>
                <div class="stat-label">Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= (int)$stats['completed_orders'] ?></div>
                <div class="stat-label">Completed</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">$<?= number_format((float)$stats['total_spent'], 2) ?></div>
                <div class="stat-label">Total Spent</div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="section-card">
            <div class="section-title">
                Recent Orders
                <a href="<?= url('account/orders') ?>">View all &rarr;</a>
            </div>
            <?php if (empty($recentOrders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p>No orders yet. <a href="<?= url('/') ?>" style="color:#00b207;">Start shopping!</a></p>
                </div>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                    <div class="order-row">
                        <div>
                            <div class="order-id">Order #<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></div>
                            <div class="order-meta"><?= date('M j, Y', strtotime($order['created_at'])) ?> &middot; <?= (int)($order['item_count'] ?? 0) ?> item(s)</div>
                        </div>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <span class="badge badge-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                            <span style="font-weight:600;font-size:14px;">$<?= number_format((float)$order['total'], 2) ?></span>
                            <a href="<?= url('account/orders/detail') ?>?id=<?= (int)$order['id'] ?>" style="color:#00b207;font-size:13px;">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
