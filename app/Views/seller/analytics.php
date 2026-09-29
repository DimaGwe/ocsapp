<?php
/**
 * Seller analytics (/seller/analytics, ShopController::analytics)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR (was English), fr-CA money; revenue is
 * "paid sales" everywhere (controller); product images used asset() on "assets/..." paths (404).
 */
$shop = $shop ?? null;
$startDate = $startDate ?? date('Y-m-d', strtotime('-29 days'));
$endDate = $endDate ?? date('Y-m-d');
$summary = $summary ?? ['total_revenue' => 0, 'total_orders' => 0, 'avg_order_value' => 0, 'completed_orders' => 0];
$revenueChange = $revenueChange ?? 0;
$ordersChange = $ordersChange ?? 0;
$topProducts = $topProducts ?? [];
$statusBreakdown = $statusBreakdown ?? [];
$lowStockProducts = $lowStockProducts ?? [];
$productStats = $productStats ?? ['total_products' => 0, 'active_products' => 0, 'out_of_stock' => 0, 'low_stock' => 0];
$chartLabels = $chartLabels ?? '[]';
$chartRevenue = $chartRevenue ?? '[]';
$chartOrders = $chartOrders ?? '[]';
$pageTitle = 'Analytics';
require __DIR__ . '/layout-header.php';

$pct = fn($v) => number_format(abs((float) $v), 1, $fr ? ',' : '.', '') . ($fr ? "\u{00A0}%" : '%');
$vsPrev = $fr ? 'par rapport à la période précédente' : 'vs previous period';
$completion = $summary['total_orders'] > 0 ? round(($summary['completed_orders'] / $summary['total_orders']) * 100, 1) : 0;
$img = fn($p) => !empty($p) ? htmlspecialchars(url(ltrim($p, '/'))) : '';
$name = fn($s) => htmlspecialchars(html_entity_decode((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="sp-stack">
  <form method="GET" action="<?= url('seller/analytics') ?>" class="sp-card sp-row" style="padding:14px 18px">
    <label class="sp-field" style="grid-auto-flow:column;align-items:center"><?= $fr ? 'Du' : 'From' ?> <input class="sp-input" type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required></label>
    <label class="sp-field" style="grid-auto-flow:column;align-items:center"><?= $fr ? 'au' : 'to' ?> <input class="sp-input" type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required></label>
    <button type="submit" class="sp-btn sp-btn-primary sp-btn-sm"><i class="fa-solid fa-rotate"></i> <?= $fr ? 'Actualiser' : 'Update' ?></button>
  </form>

  <div class="sp-stats">
    <div class="sp-stat"><i class="fa-solid fa-sack-dollar"></i><strong><?= acct_money($summary['total_revenue'], $fr) ?></strong>
      <span><?= $fr ? 'Ventes payées' : 'Paid sales' ?></span>
      <span class="<?= $revenueChange >= 0 ? 'sp-green' : '' ?>" style="<?= $revenueChange < 0 ? 'color:var(--sp-danger)' : '' ?>"><?= $revenueChange >= 0 ? '▲' : '▼' ?> <?= $pct($revenueChange) ?> <?= $vsPrev ?></span></div>
    <div class="sp-stat"><i class="fa-solid fa-receipt"></i><strong><?= (int) $summary['total_orders'] ?></strong>
      <span><?= $fr ? 'Commandes' : 'Orders' ?></span>
      <span class="<?= $ordersChange >= 0 ? 'sp-green' : '' ?>" style="<?= $ordersChange < 0 ? 'color:var(--sp-danger)' : '' ?>"><?= $ordersChange >= 0 ? '▲' : '▼' ?> <?= $pct($ordersChange) ?> <?= $vsPrev ?></span></div>
    <div class="sp-stat"><i class="fa-solid fa-scale-balanced"></i><strong><?= acct_money($summary['avg_order_value'], $fr) ?></strong>
      <span><?= $fr ? 'Panier moyen (commandes payées)' : 'Average order (paid orders)' ?></span></div>
    <div class="sp-stat"><i class="fa-solid fa-circle-check"></i><strong><?= $pct($completion) ?></strong>
      <span><?= $fr ? (int) $summary['completed_orders'] . ' sur ' . (int) $summary['total_orders'] . ' livrées' : (int) $summary['completed_orders'] . ' of ' . (int) $summary['total_orders'] . ' delivered' ?></span></div>
  </div>

  <section class="sp-card">
    <div class="sp-card-head"><h2><?= $fr ? 'Tendance des ventes' : 'Sales trend' ?></h2></div>
    <canvas id="salesChart" height="90"></canvas>
  </section>

  <div class="sp-grid-2">
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Produits les plus vendus' : 'Top-selling products' ?></h2></div>
      <?php if (empty($topProducts)): ?>
        <div class="sp-empty"><i class="fa-solid fa-chart-simple"></i><p><?= $fr ? 'Aucune vente payée pour cette période.' : 'No paid sales for this period.' ?></p></div>
      <?php else: foreach ($topProducts as $p): ?>
        <div class="sp-list-row">
          <div class="sp-row" style="flex-wrap:nowrap">
            <?php if ($img($p['image_path'] ?? '')): ?><img class="sp-thumb" src="<?= $img($p['image_path']) ?>" alt="" loading="lazy"><?php endif; ?>
            <div><div class="sp-strong"><?= $name($p['name']) ?></div><div class="sp-dim"><?= (int) $p['units_sold'] ?> <?= $fr ? 'unités vendues' : 'units sold' ?></div></div>
          </div>
          <span class="sp-strong"><?= acct_money($p['total_revenue'], $fr) ?></span>
        </div>
      <?php endforeach; endif; ?>
    </section>

    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Commandes par statut' : 'Orders by status' ?></h2></div>
      <?php if (empty($statusBreakdown)): ?>
        <div class="sp-empty"><i class="fa-solid fa-inbox"></i><p><?= $fr ? 'Aucune commande pour cette période.' : 'No orders in this period.' ?></p></div>
      <?php else: ?>
        <div style="max-width:220px;margin:0 auto 10px"><canvas id="statusChart"></canvas></div>
        <?php foreach ($statusBreakdown as $st): $c = (int) $st['count']; ?>
          <div class="sp-list-row"><span class="sp-badge sp-badge-<?= htmlspecialchars($st['status']) ?>"><?= htmlspecialchars(acct_status((string) $st['status'], $fr)) ?></span>
            <span class="sp-strong"><?= $c ?> <?= $fr ? ($c > 1 ? 'commandes' : 'commande') : ($c === 1 ? 'order' : 'orders') ?></span></div>
        <?php endforeach; ?>
      <?php endif; ?>
    </section>
  </div>

  <?php if (!empty($lowStockProducts)): ?>
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Stock faible' : 'Low stock' ?></h2><a href="<?= url('seller/inventory') ?>"><?= $fr ? 'Inventaire' : 'Inventory' ?> <i class="fa-solid fa-arrow-right"></i></a></div>
      <?php foreach ($lowStockProducts as $p): ?>
        <div class="sp-list-row">
          <div class="sp-row" style="flex-wrap:nowrap">
            <?php if ($img($p['image_path'] ?? '')): ?><img class="sp-thumb" src="<?= $img($p['image_path']) ?>" alt="" loading="lazy"><?php endif; ?>
            <div><div class="sp-strong"><?= $name($p['name']) ?></div><div class="sp-dim"><?= !empty($p['category_name']) ? $name($p['category_name']) : ($fr ? 'Sans catégorie' : 'No category') ?></div></div>
          </div>
          <div style="text-align:right"><span class="sp-badge sp-badge-warn"><?= (int) $p['stock_quantity'] ?> <?= $fr ? 'restants' : 'left' ?></span>
            <div class="sp-dim"><?= $fr ? 'Alerte à ' : 'Alert at ' ?><?= (int) $p['low_stock_alert'] ?></div></div>
        </div>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>

  <div class="sp-stats">
    <div class="sp-stat"><i class="fa-solid fa-cubes"></i><strong><?= (int) $productStats['total_products'] ?></strong><span><?= $fr ? 'Produits' : 'Products' ?></span></div>
    <div class="sp-stat"><i class="fa-solid fa-eye"></i><strong><?= (int) $productStats['active_products'] ?></strong><span><?= $fr ? 'Actifs' : 'Active' ?></span></div>
    <div class="sp-stat is-warn"><i class="fa-solid fa-ban"></i><strong><?= (int) $productStats['out_of_stock'] ?></strong><span><?= $fr ? 'En rupture' : 'Out of stock' ?></span></div>
    <div class="sp-stat is-warn"><i class="fa-solid fa-triangle-exclamation"></i><strong><?= (int) $productStats['low_stock'] ?></strong><span><?= $fr ? 'Stock faible' : 'Low stock' ?></span></div>
  </div>
</div>
<script>
(function () {
  var FR = <?= $fr ? 'true' : 'false' ?>;
  var money = function (v) { return new Intl.NumberFormat(FR ? 'fr-CA' : 'en-CA', { style: 'currency', currency: 'CAD' }).format(v); };
  new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: { labels: <?= $chartLabels ?>, datasets: [
      { label: <?= json_encode($fr ? 'Ventes payées' : 'Paid sales', JSON_UNESCAPED_UNICODE) ?>, data: <?= $chartRevenue ?>, borderColor: '#00B207', backgroundColor: 'rgba(0,178,7,.08)', fill: true, tension: .35, yAxisID: 'y' },
      { label: <?= json_encode($fr ? 'Commandes' : 'Orders') ?>, data: <?= $chartOrders ?>, borderColor: '#0D3F10', backgroundColor: 'rgba(13,63,16,.1)', tension: .35, yAxisID: 'y1' }
    ] },
    options: { responsive: true, interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'top' }, tooltip: { callbacks: { label: function (c) { return c.dataset.label + ' : ' + (c.datasetIndex === 0 ? money(c.parsed.y) : c.parsed.y); } } } },
      scales: { y: { position: 'left', ticks: { callback: function (v) { return money(v); } } }, y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } } } }
  });
  <?php if (!empty($statusBreakdown)): ?>
  new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: { labels: <?= json_encode(array_map(fn($s) => acct_status((string) $s['status'], $fr), $statusBreakdown), JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{ data: <?= json_encode(array_map('intval', array_column($statusBreakdown, 'count'))) ?>, backgroundColor: <?= json_encode(array_map(fn($s) => ['pending' => '#F5A700', 'confirmed' => '#1D4ED8', 'processing' => '#3B6FE0', 'ready' => '#7AA2F0', 'out_for_delivery' => '#5B3CC4', 'delivered' => '#00B207', 'cancelled' => '#B42318', 'refunded' => '#E07A6F'][$s['status']] ?? '#929A94', $statusBreakdown)) ?> }] },
    options: { plugins: { legend: { display: false } } }
  });
  <?php endif; ?>
})();
</script>
<?php require __DIR__ . '/layout-footer.php'; ?>
