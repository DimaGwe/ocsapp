<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$cartCount = $cartCount ?? 0;
$shop = $shop ?? null;
$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
$user = user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .seller-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .seller-sidebar { width: 220px; flex-shrink: 0; }
        .seller-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .shop-logo { width: 64px; height: 64px; border-radius: 12px; background: #00b207; color: #fff; font-size: 24px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .shop-name { text-align: center; font-weight: 600; font-size: 15px; margin-bottom: 4px; }
        .shop-status { text-align: center; font-size: 12px; margin-bottom: 14px; }
        .status-active { color: #2e7d32; }
        .status-pending { color: #e65100; }
        .sidebar-nav { border-top: 1px solid #f0f0f0; padding-top: 12px; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .stat-icon { font-size: 24px; color: #00b207; margin-bottom: 8px; }
        .stat-value { font-size: 26px; font-weight: 700; }
        .stat-label { font-size: 12px; color: #888; margin-top: 2px; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 16px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center; justify-content: space-between; }
        .section-title a { font-size: 13px; color: #00b207; text-decoration: none; }
        .order-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f5f5f5; font-size: 14px; }
        .order-row:last-child { border-bottom: none; }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-confirmed,.badge-processing { background: #e3f2fd; color: #1565c0; }
        .badge-completed,.badge-delivered { background: #e8f5e9; color: #2e7d32; }
        .badge-cancelled { background: #fce4ec; color: #c62828; }
        .badge-ready { background: #f3e5f5; color: #6a1b9a; }
        .no-shop { text-align: center; padding: 40px; }
        .no-shop i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #00b207; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
        @media (max-width: 768px) { .seller-layout { flex-direction: column; } .seller-sidebar { width: 100%; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="seller-layout">
    <aside class="seller-sidebar">
        <div class="sidebar-card">
            <?php if ($shop): ?>
                <div class="shop-logo"><?= strtoupper(substr($shop['name'] ?? 'S', 0, 1)) ?></div>
                <div class="shop-name"><?= htmlspecialchars($shop['name']) ?></div>
                <div class="shop-status">
                    <?php if ($shop['is_active']): ?>
                        <span class="status-active"><i class="fas fa-circle" style="font-size:8px;"></i> Active</span>
                    <?php elseif ($shop['is_approved']): ?>
                        <span class="status-pending">Inactive</span>
                    <?php else: ?>
                        <span class="status-pending"><i class="fas fa-clock" style="font-size:8px;"></i> Pending Approval</span>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="shop-logo"><i class="fas fa-store"></i></div>
                <div class="shop-name">My Shop</div>
            <?php endif; ?>
            <nav class="sidebar-nav">
                <a href="<?= url('seller/dashboard') ?>" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('seller/orders') ?>"><i class="fas fa-box"></i> Orders</a>
                <a href="<?= url('seller/inventory') ?>"><i class="fas fa-cubes"></i> Inventory</a>
                <a href="<?= url('seller/shop/settings') ?>"><i class="fas fa-cog"></i> Shop Settings</a>
                <hr style="border:none;border-top:1px solid #f0f0f0;margin:6px 0;">
                <a href="#" style="color:#c62828;" onclick="event.preventDefault();document.getElementById('seller-logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
            <form id="seller-logout-form" method="POST" action="<?= url('logout') ?>" style="display:none;"><?= csrfField() ?></form>
        </div>
    </aside>

    <main class="seller-main">
        <?php if ($flash = getFlash('success')): ?>
            <div data-auto-dismiss style="background:#e8f5e9;color:#2e7d32;padding:12px 16px;border-radius:8px;margin-bottom:16px;transition:opacity 0.6s ease;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('error')): ?>
            <div style="background:#fce4ec;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <?php if (!$shop): ?>
            <div class="section-card">
                <div class="no-shop">
                    <i class="fas fa-store"></i>
                    <h2 style="font-size:20px;font-weight:600;margin-bottom:8px;">You don't have a shop yet</h2>
                    <p style="color:#888;margin-bottom:20px;">Create your shop to start selling on OCS Marketplace.</p>
                    <a href="<?= url('seller/shop/create') ?>" class="btn-primary"><i class="fas fa-plus"></i> Create My Shop</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Stats -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
                    <div class="stat-value"><?= (int)($stats['total_orders'] ?? 0) ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value"><?= (int)($stats['pending_orders'] ?? 0) ?></div>
                    <div class="stat-label">Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-value"><?= (int)($stats['today_orders'] ?? 0) ?></div>
                    <div class="stat-label">Today's Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-value">$<?= number_format((float)($stats['total_revenue'] ?? 0), 0) ?></div>
                    <div class="stat-label">Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-cubes"></i></div>
                    <div class="stat-value"><?= (int)($stats['products_count'] ?? 0) ?></div>
                    <div class="stat-label">Products</div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="section-card">
                <div class="section-title">
                    Recent Orders
                    <a href="<?= url('seller/orders') ?>">View all &rarr;</a>
                </div>
                <?php if (empty($recentOrders)): ?>
                    <p style="color:#aaa;text-align:center;padding:24px;">No orders yet.</p>
                <?php else: ?>
                    <?php foreach ($recentOrders as $order): ?>
                        <div class="order-row">
                            <div>
                                <strong>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong>
                                <span style="color:#888;margin-left:8px;font-size:13px;"><?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></span>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span class="badge badge-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                                <span style="font-weight:600;">$<?= number_format((float)$order['total'], 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="section-card">
                <div class="section-title">Quick Actions</div>
                <div style="display:flex;gap:12px;flex-wrap:wrap;">
                    <a href="<?= url('seller/orders') ?>?status=pending" class="btn-primary" style="background:#e65100;"><i class="fas fa-box"></i> View Pending Orders</a>
                    <a href="<?= url('seller/inventory/add') ?>" class="btn-primary"><i class="fas fa-plus"></i> Add Product</a>
                    <a href="<?= url('seller/shop/settings') ?>" class="btn-primary" style="background:#555;"><i class="fas fa-cog"></i> Shop Settings</a>
                </div>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
