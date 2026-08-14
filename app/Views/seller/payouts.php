<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$cartCount = $cartCount ?? 0;
$shop = $shop ?? null;
$payouts = $payouts ?? [];
$pendingBalance = $pendingBalance ?? 0.00;
$user = user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payouts - OCS Marketplace</title>
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
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 16px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 16px; }
        .balance-card { background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: #fff; border-radius: 12px; padding: 24px; margin-bottom: 16px; }
        .balance-label { font-size: 13px; opacity: .85; margin-bottom: 6px; }
        .balance-value { font-size: 32px; font-weight: 700; }
        table.payouts-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        table.payouts-table th { text-align: left; padding: 10px 12px; color: #888; font-weight: 600; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #f0f0f0; }
        table.payouts-table td { padding: 10px 12px; border-bottom: 1px solid #f5f5f5; }
        table.payouts-table tr:last-child td { border-bottom: none; }
        .amount-negative { color: #c62828; }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
        .badge-pending { background: #fff3e0; color: #e65100; }
        .badge-paid { background: #e8f5e9; color: #2e7d32; }
        .badge-held { background: #fce4ec; color: #c62828; }
        .no-shop { text-align: center; padding: 40px; }
        .no-shop i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #00b207; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; }
        .table-scroll { overflow-x: auto; }
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
                <a href="<?= url('seller/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('seller/orders') ?>"><i class="fas fa-box"></i> Orders</a>
                <a href="<?= url('seller/inventory') ?>"><i class="fas fa-cubes"></i> Inventory</a>
                <a href="<?= url('seller/payouts') ?>" class="active"><i class="fas fa-dollar-sign"></i> Payouts</a>
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
            <div class="balance-card">
                <div class="balance-label">Pending Balance</div>
                <div class="balance-value">$<?= number_format($pendingBalance, 2) ?></div>
            </div>

            <div class="section-card">
                <div class="section-title">Payout Statement</div>
                <?php if (empty($payouts)): ?>
                    <p style="color:#aaa;text-align:center;padding:24px;">No payouts yet - they're created once an order is delivered.</p>
                <?php else: ?>
                    <div class="table-scroll">
                    <table class="payouts-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Date</th>
                                <th>Subtotal</th>
                                <th>Commission</th>
                                <th>Processing Fee</th>
                                <th>Chargeback</th>
                                <th>Net Payout</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($payouts as $p): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($p['order_number']) ?></strong></td>
                                    <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
                                    <td>$<?= number_format((float)$p['subtotal'], 2) ?></td>
                                    <td class="amount-negative">-$<?= number_format((float)$p['commission_amount'], 2) ?></td>
                                    <td class="amount-negative">-$<?= number_format((float)$p['processing_fee_amount'], 2) ?></td>
                                    <td class="amount-negative"><?= (float)$p['chargeback_amount'] > 0 ? '-$' . number_format((float)$p['chargeback_amount'], 2) : '-' ?></td>
                                    <td><strong>$<?= number_format((float)$p['net_payout_amount'] - (float)$p['chargeback_amount'], 2) ?></strong></td>
                                    <td><span class="badge badge-<?= htmlspecialchars($p['status']) ?>"><?= ucfirst($p['status']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
