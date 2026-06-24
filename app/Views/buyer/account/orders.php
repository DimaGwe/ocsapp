<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$orders = $orders ?? [];
$currentPage = $currentPage ?? 1;
$totalPages = $totalPages ?? 1;
$totalOrders = $totalOrders ?? 0;
$status = $status ?? '';
$search = $search ?? '';
$cartCount = $cartCount ?? 0;
$user = user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - OCS Marketplace</title>
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
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .filter-bar { display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; }
        .filter-bar form { display: flex; gap: 8px; flex: 1; min-width: 200px; }
        .filter-bar input { flex: 1; padding: 8px 14px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; }
        .filter-bar button, .filter-bar a { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; }
        .btn-filter { background: #00b207; color: #fff; }
        .status-tabs { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
        .status-tab { padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 500; text-decoration: none; color: #555; background: #f5f5f5; border: 1px solid #e0e0e0; }
        .status-tab.active { background: #00b207; color: #fff; border-color: #00b207; }
        .order-card { border: 1px solid #f0f0f0; border-radius: 10px; padding: 16px; margin-bottom: 12px; transition: box-shadow .2s; }
        .order-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.08); }
        .order-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
        .order-number { font-weight: 600; font-size: 15px; }
        .order-shop { font-size: 13px; color: #888; }
        .order-footer { display: flex; align-items: center; justify-content: space-between; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f5f5f5; }
        .order-total { font-weight: 700; font-size: 16px; }
        .order-date { font-size: 12px; color: #aaa; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-completed,.badge-delivered { background: #e8f5e9; color: #2e7d32; }
        .badge-cancelled { background: #fce4ec; color: #c62828; }
        .badge-processing { background: #e3f2fd; color: #1565c0; }
        .badge-paid { background: #e8f5e9; color: #2e7d32; }
        .btn-view { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; background: #f5f5f5; color: #333; font-size: 13px; font-weight: 500; text-decoration: none; transition: all .2s; }
        .btn-view:hover { background: #00b207; color: #fff; }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        .pagination { display: flex; gap: 8px; justify-content: center; margin-top: 24px; }
        .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 14px; text-decoration: none; }
        .pagination a { background: #f5f5f5; color: #333; }
        .pagination a:hover { background: #e8f5e9; color: #00b207; }
        .pagination span { background: #00b207; color: #fff; font-weight: 600; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } .order-header { flex-direction: column; align-items: flex-start; gap: 6px; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="account-layout">
    <aside class="account-sidebar">
        <div class="sidebar-card">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                <div class="sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('account') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('account/orders') ?>" class="active"><i class="fas fa-box"></i> My Orders</a>
                <a href="<?= url('account/addresses') ?>"><i class="fas fa-map-marker-alt"></i> Addresses</a>
                <a href="<?= url('account/wishlist') ?>"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="<?= url('account/settings') ?>"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>
    </aside>

    <main class="account-main">
        <?php if ($flash = getFlash('error')): ?>
            <div style="background:#fce4ec;color:#c62828;padding:12px 16px;border-radius:8px;margin-bottom:16px;"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <div class="section-card">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:20px;">My Orders <span style="font-size:14px;color:#888;font-weight:400;">(<?= (int)$totalOrders ?> total)</span></h2>

            <!-- Status tabs -->
            <div class="status-tabs">
                <a href="<?= url('account/orders') ?>" class="status-tab <?= $status === '' ? 'active' : '' ?>">All</a>
                <a href="<?= url('account/orders') ?>?status=pending" class="status-tab <?= $status === 'pending' ? 'active' : '' ?>">Pending</a>
                <a href="<?= url('account/orders') ?>?status=processing" class="status-tab <?= $status === 'processing' ? 'active' : '' ?>">Processing</a>
                <a href="<?= url('account/orders') ?>?status=completed" class="status-tab <?= $status === 'completed' ? 'active' : '' ?>">Completed</a>
                <a href="<?= url('account/orders') ?>?status=cancelled" class="status-tab <?= $status === 'cancelled' ? 'active' : '' ?>">Cancelled</a>
            </div>

            <!-- Search -->
            <div class="filter-bar">
                <form method="GET" action="<?= url('account/orders') ?>">
                    <?php if ($status): ?><input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>"><?php endif; ?>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="<?= $currentLang === 'fr' ? 'Rechercher par # commande ou nom de boutique...' : 'Search by order # or shop name...' ?>">
                    <button type="submit" class="btn-filter"><i class="fas fa-search"></i></button>
                    <?php if ($search): ?><a href="<?= url('account/orders') ?><?= $status ? '?status='.htmlspecialchars($status) : '' ?>">Clear</a><?php endif; ?>
                </form>
            </div>

            <?php if (empty($orders)): ?>
                <div class="empty-state">
                    <i class="fas fa-shopping-bag"></i>
                    <p style="font-size:16px;font-weight:500;color:#555;margin-bottom:8px;">No orders found</p>
                    <p>Start shopping to see your orders here.</p>
                    <a href="<?= url('/') ?>" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#00b207;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">Browse Products</a>
                </div>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div>
                                <div class="order-number">Order #<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></div>
                                <div class="order-shop"><?= htmlspecialchars($order['shop_name'] ?? 'Shop') ?> &middot; <?= (int)($order['items_count'] ?? 0) ?> item(s)</div>
                            </div>
                            <span class="badge badge-<?= htmlspecialchars($order['status']) ?>"><?= ucfirst($order['status']) ?></span>
                        </div>
                        <div class="order-footer">
                            <div>
                                <span class="order-total">$<?= number_format((float)$order['total'], 2) ?></span>
                                <span class="order-date" style="margin-left:10px;"><?= date('M j, Y', strtotime($order['created_at'])) ?></span>
                            </div>
                            <a href="<?= url('account/orders/detail') ?>?id=<?= (int)$order['id'] ?>" class="btn-view">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>">&laquo; Prev</a>
                        <?php endif; ?>
                        <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                            <?php if ($p === $currentPage): ?>
                                <span><?= $p ?></span>
                            <?php else: ?>
                                <a href="?page=<?= $p ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>"><?= $p ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 ?><?= $status ? '&status='.htmlspecialchars($status) : '' ?><?= $search ? '&search='.urlencode($search) : '' ?>">Next &raquo;</a>
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
