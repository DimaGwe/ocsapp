<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$pageTitle = $t['sup_analytics'] ?? 'Analytics';
require __DIR__ . '/layout-header.php';

// Calculate percentages
$acceptanceRate = 0;
if ($acceptanceStats['total_received'] > 0) {
    $acceptanceRate = ($acceptanceStats['accepted'] / $acceptanceStats['total_received']) * 100;
}
?>

<style>
  .analytics-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .analytics-header h2 {
    font-size: 24px;
    color: var(--gray-700);
    margin: 0;
  }

  .period-selector {
    display: flex;
    gap: 8px;
    background: white;
    padding: 4px;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .period-btn {
    padding: 8px 16px;
    border: none;
    background: transparent;
    color: var(--gray-600);
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
  }

  .period-btn:hover {
    background: var(--gray-100);
  }

  .period-btn.active {
    background: var(--primary);
    color: white;
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: transform 0.2s;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }

  .stat-icon.primary { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; }
  .stat-icon.success { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; }
  .stat-icon.warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; }
  .stat-icon.danger { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); color: #991b1b; }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-700);
    line-height: 1;
  }

  .stat-label {
    font-size: 14px;
    color: var(--gray-600);
    margin-top: 8px;
  }

  .stat-change {
    font-size: 12px;
    font-weight: 600;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .stat-change.positive { color: var(--success); }
  .stat-change.negative { color: var(--danger); }

  .card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .chart-container {
    height: 300px;
    position: relative;
  }

  .chart-bar {
    display: flex;
    gap: 8px;
    height: 100%;
    align-items: flex-end;
  }

  .bar-group {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .bar {
    width: 100%;
    background: linear-gradient(180deg, var(--primary) 0%, var(--primary-dark) 100%);
    border-radius: 8px 8px 0 0;
    transition: all 0.3s;
    position: relative;
    min-height: 4px;
  }

  .bar:hover {
    opacity: 0.8;
  }

  .bar-value {
    position: absolute;
    top: -24px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-700);
    white-space: nowrap;
  }

  .bar-label {
    margin-top: 8px;
    font-size: 12px;
    color: var(--gray-600);
    text-align: center;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
  }

  td {
    padding: 16px 12px;
    border-top: 1px solid var(--gray-200);
    font-size: 14px;
  }

  .progress-bar {
    width: 100%;
    height: 8px;
    background: var(--gray-200);
    border-radius: 4px;
    overflow: hidden;
    margin: 8px 0;
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary) 0%, var(--success) 100%);
    transition: width 0.3s;
  }

  .badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge.sent { background: #dbeafe; color: #1e40af; }
  .badge.receiving { background: #fef3c7; color: #92400e; }
  .badge.completed { background: #dcfce7; color: #166534; }
  .badge.cancelled { background: #fee2e2; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: var(--gray-400);
  }
</style>

<div class="analytics-header">
  <div>
    <h2 style="margin:0 0 4px;"><?= $fr ? 'Analytique et aperçus' : 'Analytics & Insights' ?></h2>
    <?php if ($period === 'custom'): ?>
      <p style="margin:0;font-size:13px;color:var(--gray-500);">
        <?= date('M j, Y', strtotime($startDate)) ?> &ndash; <?= date('M j, Y', strtotime($endDate)) ?>
      </p>
    <?php elseif ($period === 'all'): ?>
      <p style="margin:0;font-size:13px;color:var(--gray-500);"><?= $fr ? 'Toutes les données' : 'Showing all time' ?></p>
    <?php elseif ($period === 'year'): ?>
      <p style="margin:0;font-size:13px;color:var(--gray-500);"><?= $fr ? 'Depuis le début de l\'année' : 'Showing year to date' ?></p>
    <?php else: ?>
      <p style="margin:0;font-size:13px;color:var(--gray-500);"><?= sprintf($fr ? 'Derniers %d jours' : 'Last %d days', (int)$period) ?></p>
    <?php endif; ?>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <div class="period-selector">
      <a href="?period=7" class="period-btn <?= $period == '7' ? 'active' : '' ?>">7D</a>
      <a href="?period=30" class="period-btn <?= $period == '30' ? 'active' : '' ?>">30D</a>
      <a href="?period=90" class="period-btn <?= $period == '90' ? 'active' : '' ?>">90D</a>
      <a href="?period=year" class="period-btn <?= $period == 'year' ? 'active' : '' ?>">YTD</a>
      <a href="?period=all" class="period-btn <?= $period == 'all' ? 'active' : '' ?>"><?= $currentLang === 'fr' ? 'Tout' : 'All' ?></a>
      <button type="button" class="period-btn <?= $period == 'custom' ? 'active' : '' ?>" onclick="document.getElementById('custom-range').style.display=document.getElementById('custom-range').style.display==='none'?'flex':'none'"><?= $fr ? 'Perso' : 'Custom' ?></button>
    </div>
  </div>
</div>

<!-- Custom date range form -->
<div id="custom-range" style="display:<?= $period === 'custom' ? 'flex' : 'none' ?>;align-items:center;gap:12px;background:white;padding:12px 16px;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,0.1);margin-bottom:16px;flex-wrap:wrap;">
  <span style="font-size:13px;font-weight:600;color:var(--gray-700);"><?= $fr ? 'Plage personnalisée :' : 'Custom Range:' ?></span>
  <form method="GET" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
    <input type="hidden" name="period" value="custom">
    <label style="font-size:13px;color:var(--gray-600);"><?= $fr ? 'Du' : 'From' ?></label>
    <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom ?: date('Y-m-d', strtotime('-30 days'))) ?>" style="padding:6px 10px;border:2px solid var(--gray-200);border-radius:6px;font-size:13px;">
    <label style="font-size:13px;color:var(--gray-600);"><?= $fr ? 'au' : 'To' ?></label>
    <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo ?: date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>" style="padding:6px 10px;border:2px solid var(--gray-200);border-radius:6px;font-size:13px;">
    <button type="submit" style="padding:6px 16px;background:var(--primary);color:white;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;"><?= $fr ? 'Appliquer' : 'Apply' ?></button>
  </form>
</div>

<!-- Key Metrics -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value">$<?= number_format($orderStats['total_revenue'] ?? 0, 0) ?></div>
        <div class="stat-label"><?= $fr ? 'Revenus totaux' : 'Total Revenue' ?></div>
      </div>
      <div class="stat-icon success">
        <i class="fas fa-dollar-sign"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= $orderStats['total_orders'] ?? 0 ?></div>
        <div class="stat-label"><?= $fr ? 'Commandes totales' : 'Total Orders' ?></div>
      </div>
      <div class="stat-icon primary">
        <i class="fas fa-shopping-cart"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value">$<?= number_format($orderStats['avg_order_value'] ?? 0, 0) ?></div>
        <div class="stat-label"><?= $fr ? 'Valeur moy. commande' : 'Avg Order Value' ?></div>
      </div>
      <div class="stat-icon warning">
        <i class="fas fa-chart-line"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= number_format($acceptanceRate, 1) ?>%</div>
        <div class="stat-label"><?= $fr ? 'Taux d\'acceptation' : 'Acceptance Rate' ?></div>
      </div>
      <div class="stat-icon <?= $acceptanceRate >= 90 ? 'success' : 'warning' ?>">
        <i class="fas fa-check-circle"></i>
      </div>
    </div>
  </div>
</div>

<!-- Order Status Breakdown -->
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-chart-pie"></i>
    <?= $fr ? 'Répartition par statut' : 'Order Status Breakdown' ?>
  </h3>
  <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));">
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #1e40af;"><?= $orderStats['pending_orders'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600);"><?= $fr ? 'En attente' : 'Pending' ?></div>
    </div>
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #92400e;"><?= $orderStats['receiving_orders'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600);"><?= $fr ? 'En réception' : 'Receiving' ?></div>
    </div>
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #166534;"><?= $orderStats['completed_orders'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600);"><?= $fr ? 'Complétées' : 'Completed' ?></div>
    </div>
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #991b1b;"><?= $orderStats['cancelled_orders'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600);"><?= $fr ? 'Annulées' : 'Cancelled' ?></div>
    </div>
  </div>
</div>

<!-- Monthly Trend Chart -->
<?php if (!empty($monthlyOrders)): ?>
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-chart-bar"></i>
    <?= $fr ? 'Tendance mensuelle — Commandes et revenus (6 derniers mois)' : 'Monthly Orders &amp; Revenue (Last 6 Months)' ?>
  </h3>
  <div class="chart-container">
    <div class="chart-bar">
      <?php
      $maxRevenue = max(array_column($monthlyOrders, 'revenue'));
      foreach ($monthlyOrders as $monthData):
        $height = $maxRevenue > 0 ? ($monthData['revenue'] / $maxRevenue) * 100 : 0;
        $monthLabel = date('M Y', strtotime($monthData['month'] . '-01'));
      ?>
        <div class="bar-group">
          <div class="bar" style="height: <?= $height ?>%;">
            <div class="bar-value">$<?= number_format($monthData['revenue'], 0) ?></div>
          </div>
          <div class="bar-label"><?= $monthLabel ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Top Products -->
<?php if (!empty($topProducts)): ?>
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-star"></i>
    <?= $fr ? 'Produits les plus performants' : 'Top Performing Products' ?>
  </h3>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Produit' : 'Product' ?></th>
        <th>SKU</th>
        <th style="text-align: center;"><?= $fr ? 'Commandes' : 'Orders' ?></th>
        <th style="text-align: center;"><?= $fr ? 'Quantité' : 'Quantity' ?></th>
        <th style="text-align: right;"><?= $fr ? 'Revenus' : 'Revenue' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($topProducts as $product): ?>
        <tr>
          <td><strong><?= htmlspecialchars($product['product_name']) ?></strong></td>
          <td><?= htmlspecialchars($product['sku']) ?></td>
          <td style="text-align: center;"><?= $product['order_count'] ?></td>
          <td style="text-align: center;"><?= number_format($product['total_quantity']) ?></td>
          <td style="text-align: right;"><strong>$<?= number_format($product['total_revenue'], 2) ?></strong></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-star"></i>
    <?= $fr ? 'Produits les plus performants' : 'Top Performing Products' ?>
  </h3>
  <div class="empty-state">
    <i class="fas fa-chart-line"></i>
    <p><?= $fr ? 'Aucune donnée produit disponible pour la période sélectionnée' : 'No product data available for the selected period' ?></p>
  </div>
</div>
<?php endif; ?>

<!-- Recent Orders -->
<?php if (!empty($recentOrders)): ?>
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-clock"></i>
    <?= $fr ? 'Activité récente' : 'Recent Activity' ?>
  </h3>
  <table>
    <thead>
      <tr>
        <th><?= $fr ? 'Commande #' : 'Order #' ?></th>
        <th><?= $fr ? 'Date' : 'Date' ?></th>
        <th><?= $fr ? 'Articles' : 'Items' ?></th>
        <th><?= $fr ? 'Statut' : 'Status' ?></th>
        <th style="text-align: right;"><?= $fr ? 'Montant' : 'Amount' ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($recentOrders as $order): ?>
        <tr>
          <td>
            <a href="<?= url('supplier/orders/view?id=' . $order['id']) ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">
              <?= htmlspecialchars($order['po_number']) ?>
            </a>
          </td>
          <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
          <td><?= $order['item_count'] ?> <?= $fr ? 'articles' : 'items' ?></td>
          <td><span class="badge <?= $order['status'] ?>"><?= $fr ? [
            'sent'=>'Envoyé','accepted'=>'Accepté','preparing'=>'En prép.','ready_for_pickup'=>'Prêt',
            'picked_up'=>'Ramassé','completed'=>'Complété','cancelled'=>'Annulé'
          ][$order['status']] ?? ucfirst($order['status'])
          : ($t['po_status_' . $order['status']] ?? ucfirst($order['status'])) ?></span></td>
          <td style="text-align: right;"><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Product Catalog Stats -->
<div class="card">
  <h3 class="card-title">
    <i class="fas fa-box"></i>
    <?= $fr ? 'Aperçu du catalogue produits' : 'Product Catalog Overview' ?>
  </h3>
  <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div>
      <div style="font-size: 24px; font-weight: 700; color: var(--gray-700);"><?= $productStats['total_products'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 8px;"><?= $fr ? 'Produits totaux' : 'Total Products' ?></div>
      <div class="progress-bar">
        <div class="progress-fill" style="width: 100%;"></div>
      </div>
    </div>
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #166534;"><?= $productStats['available_products'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 8px;"><?= $fr ? 'Disponibles' : 'Available' ?></div>
      <div class="progress-bar">
        <?php
        $availablePercentage = $productStats['total_products'] > 0
          ? ($productStats['available_products'] / $productStats['total_products']) * 100
          : 0;
        ?>
        <div class="progress-fill" style="width: <?= $availablePercentage ?>%;"></div>
      </div>
    </div>
    <div>
      <div style="font-size: 24px; font-weight: 700; color: #991b1b;"><?= $productStats['unavailable_products'] ?? 0 ?></div>
      <div style="font-size: 14px; color: var(--gray-600); margin-bottom: 8px;"><?= $fr ? 'Non disponibles' : 'Unavailable' ?></div>
      <div class="progress-bar">
        <?php
        $unavailablePercentage = $productStats['total_products'] > 0
          ? ($productStats['unavailable_products'] / $productStats['total_products']) * 100
          : 0;
        ?>
        <div class="progress-fill" style="width: <?= $unavailablePercentage ?>%; background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);"></div>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/layout-footer.php'; ?>
