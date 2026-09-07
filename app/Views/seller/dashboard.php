<?php
$cartCount = $cartCount ?? 0;
$shop = $shop ?? null;
$stats = $stats ?? [];
$recentOrders = $recentOrders ?? [];
$pageTitle = 'Seller Dashboard';
require __DIR__ . '/layout-header.php';
?>

<style>
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
  .stat-card { background: white; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; background: rgba(0,178,7,0.1); color: var(--primary); margin-bottom: 16px; }
  .stat-value { font-size: 32px; font-weight: 700; color: var(--gray-700); }
  .stat-label { font-size: 14px; color: var(--gray-600); margin-top: 4px; }

  .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 24px; }
  .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
  .card-title { font-size: 18px; font-weight: 700; color: var(--gray-700); }
  .btn-view-all { color: var(--primary); text-decoration: none; font-weight: 600; font-size: 14px; }

  .order-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--gray-100); font-size: 14px; }
  .order-row:last-child { border-bottom: none; }
  .badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .badge-pending { background: #fff3e0; color: #e65100; }
  .badge-confirmed, .badge-processing { background: #e3f2fd; color: #1565c0; }
  .badge-completed, .badge-delivered { background: #dcfce7; color: #166534; }
  .badge-cancelled { background: #fee2e2; color: #991b1b; }
  .badge-ready { background: #ede9fe; color: #5b21b6; }

  .empty-state { text-align: center; padding: 40px 20px; color: var(--gray-600); }
  .empty-state i { font-size: 48px; margin-bottom: 16px; color: var(--gray-400); display: block; }

  .no-shop { text-align: center; padding: 40px; }
  .no-shop i { font-size: 56px; color: var(--gray-300); display: block; margin-bottom: 16px; }
  .btn-primary { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: var(--primary); color: #fff; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 14px; border: none; cursor: pointer; }
</style>

<?php if (!$shop): ?>
  <div class="card">
    <div class="no-shop">
      <i class="fas fa-store"></i>
      <h2 style="font-size:20px;font-weight:600;margin-bottom:8px;color:var(--gray-700);">You don't have a shop yet</h2>
      <p style="color:var(--gray-600);margin-bottom:20px;">Create your shop to start selling on OCS Marketplace.</p>
      <a href="<?= url('seller/shop/create') ?>" class="btn-primary"><i class="fas fa-plus"></i> Create My Shop</a>
    </div>
  </div>
<?php else: ?>
  <!-- Stats -->
  <div class="stats-grid">
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

  <!-- My Plan -->
  <?php
  $sellerPkg = $shop['subscription_package'] ?? 'Essential';
  $sellerPkgColors = ['Essential'=>'#00b207','Experience'=>'#3b82f6','Prestige'=>'#7c3aed','Enterprise'=>'#1f2937'];
  $sellerPkgColor = $sellerPkgColors[$sellerPkg] ?? '#00b207';
  $sellerIsFounding = !empty($shop['founding_partner']);
  ?>
  <div class="card">
    <div class="card-header"><h2 class="card-title">My Plan</h2></div>
    <div style="display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
      <div style="display:flex;align-items:center;gap:10px;">
        <div style="width:40px;height:40px;border-radius:10px;background:<?= $sellerPkgColor ?>18;color:<?= $sellerPkgColor ?>;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-star"></i>
        </div>
        <div>
          <div style="font-weight:600;font-size:15px;color:<?= $sellerPkgColor ?>;"><?= htmlspecialchars($sellerPkg) ?></div>
          <div style="font-size:13px;color:var(--gray-600);">
            <?= number_format((float)($shop['commission_rate'] ?? 15), 2) ?>% delivery &middot; <?= number_format((float)($shop['pickup_commission_rate'] ?? 8), 2) ?>% pickup
          </div>
        </div>
      </div>
      <?php if ($sellerIsFounding): ?>
        <div style="display:inline-flex;align-items:center;gap:6px;background:#fef3c722;color:#b45309;padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;border:1px solid #fbbf2455;">
          🌟 Founding Partner #<?= (int)$shop['founding_partner_number'] ?> of 20
          <?php if (!empty($shop['founding_partner_expires_at'])): ?>
            <span style="font-weight:400;opacity:.85;">&middot; locked until <?= formatDate($shop['founding_partner_expires_at'], 'M d, Y') ?></span>
          <?php endif; ?>
        </div>
        <?php if ((int)($shop['founding_free_deliveries_remaining'] ?? 0) > 0): ?>
          <div style="font-size:13px;color:#166534;"><?= (int)$shop['founding_free_deliveries_remaining'] ?> free delivery order<?= (int)$shop['founding_free_deliveries_remaining'] === 1 ? '' : 's' ?> remaining</div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Recent Orders</h2>
      <a href="<?= url('seller/orders') ?>" class="btn-view-all">View all &rarr;</a>
    </div>
    <?php if (empty($recentOrders)): ?>
      <div class="empty-state"><i class="fas fa-inbox"></i>No orders yet.</div>
    <?php else: ?>
      <?php foreach ($recentOrders as $order): ?>
        <div class="order-row">
          <div>
            <strong>#<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?></strong>
            <span style="color:var(--gray-600);margin-left:8px;font-size:13px;"><?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?></span>
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
  <div class="card">
    <div class="card-header"><h2 class="card-title">Quick Actions</h2></div>
    <div style="display:flex;gap:12px;flex-wrap:wrap;">
      <a href="<?= url('seller/orders') ?>?status=pending" class="btn-primary" style="background:#e65100;"><i class="fas fa-box"></i> View Pending Orders</a>
      <a href="<?= url('seller/inventory/add') ?>" class="btn-primary"><i class="fas fa-plus"></i> Add Product</a>
      <a href="<?= url('seller/shop/settings') ?>" class="btn-primary" style="background:#555;"><i class="fas fa-cog"></i> Shop Settings</a>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/layout-footer.php'; ?>
