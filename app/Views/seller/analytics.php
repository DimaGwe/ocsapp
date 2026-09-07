<?php
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
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
  .date-filter { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 24px; }
  .date-filter label { font-size: 13px; font-weight: 600; color: var(--gray-700); margin-right: 6px; }
  .date-filter input { padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 8px; }
  .date-filter button { padding: 8px 20px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }

  .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px; }
  .metric-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .metric-label { font-size: 13px; color: var(--gray-600); margin-bottom: 8px; }
  .metric-value { font-size: 28px; font-weight: 700; color: var(--gray-700); margin-bottom: 8px; }
  .metric-change { font-size: 13px; display: flex; align-items: center; gap: 5px; }
  .metric-change.positive { color: #166534; }
  .metric-change.negative { color: #991b1b; }

  .chart-container { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
  .chart-container h2 { font-size: 16px; font-weight: 700; margin-bottom: 20px; color: var(--gray-700); }
  .two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
  .section { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .section h2 { font-size: 16px; font-weight: 700; margin-bottom: 15px; color: var(--gray-700); }
  .product-list { list-style: none; margin: 0; padding: 0; }
  .product-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-100); }
  .product-item:last-child { border-bottom: none; }
  .product-info { display: flex; align-items: center; gap: 12px; flex: 1; }
  .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; background: var(--gray-100); }
  .product-name { font-weight: 600; color: var(--gray-700); font-size: 14px; }
  .product-sku { font-size: 12px; color: var(--gray-600); }
  .product-stats { text-align: right; }
  .product-revenue { font-weight: 700; color: var(--primary); font-size: 15px; }
  .product-units { font-size: 12px; color: var(--gray-600); }
  .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
  .status-pending { background: #fff3e0; color: #e65100; }
  .status-confirmed, .status-processing { background: #e3f2fd; color: #1565c0; }
  .status-ready, .status-out_for_delivery { background: #ede9fe; color: #5b21b6; }
  .status-delivered { background: #dcfce7; color: #166534; }
  .status-cancelled, .status-refunded { background: #fee2e2; color: #991b1b; }
  .alert-warning { background: #fef3c7; border: 1px solid #f59e0b; color: #92400e; padding: 12px; border-radius: 8px; margin-bottom: 15px; }
  .empty-state { text-align: center; padding: 40px; color: var(--gray-600); }
  @media (max-width: 768px) { .two-column { grid-template-columns: 1fr; } }
</style>

<form method="GET" action="<?= url('seller/analytics') ?>" class="date-filter">
    <div><label>From:</label><input type="date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" required></div>
    <div><label>To:</label><input type="date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" required></div>
    <button type="submit"><i class="fas fa-filter"></i> Update</button>
</form>

<!-- Summary Metrics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Revenue</div>
        <div class="metric-value">$<?= number_format($summary['total_revenue'], 2) ?></div>
        <div class="metric-change <?= $revenueChange >= 0 ? 'positive' : 'negative' ?>">
            <?= $revenueChange >= 0 ? '▲' : '▼' ?> <?= abs($revenueChange) ?>% vs previous period
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Orders</div>
        <div class="metric-value"><?= number_format($summary['total_orders']) ?></div>
        <div class="metric-change <?= $ordersChange >= 0 ? 'positive' : 'negative' ?>">
            <?= $ordersChange >= 0 ? '▲' : '▼' ?> <?= abs($ordersChange) ?>% vs previous period
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Average Order Value</div>
        <div class="metric-value">$<?= number_format($summary['avg_order_value'], 2) ?></div>
        <div class="metric-change" style="color:var(--gray-600);">Per order average</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Completion Rate</div>
        <div class="metric-value">
            <?= $summary['total_orders'] > 0 ? round(($summary['completed_orders'] / $summary['total_orders']) * 100, 1) : 0 ?>%
        </div>
        <div class="metric-change" style="color:var(--gray-600);">
            <?= $summary['completed_orders'] ?> of <?= $summary['total_orders'] ?> delivered
        </div>
    </div>
</div>

<!-- Sales Chart -->
<div class="chart-container">
    <h2>Sales Trend</h2>
    <canvas id="salesChart" height="80"></canvas>
</div>

<!-- Two Column Layout -->
<div class="two-column">
    <div class="section">
        <h2>Top Selling Products</h2>
        <?php if (empty($topProducts)): ?>
            <div class="empty-state">No sales data available for this period</div>
        <?php else: ?>
            <ul class="product-list">
                <?php foreach ($topProducts as $product): ?>
                    <li class="product-item">
                        <div class="product-info">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= asset($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                            <?php else: ?>
                                <div class="product-img" style="display:flex;align-items:center;justify-content:center;color:var(--gray-400);"><i class="fa fa-box"></i></div>
                            <?php endif; ?>
                            <div>
                                <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                <div class="product-sku">SKU: <?= htmlspecialchars($product['sku'] ?? '—') ?></div>
                            </div>
                        </div>
                        <div class="product-stats">
                            <div class="product-revenue">$<?= number_format($product['total_revenue'], 2) ?></div>
                            <div class="product-units"><?= $product['units_sold'] ?> units sold</div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Order Status</h2>
        <?php if (empty($statusBreakdown)): ?>
            <div class="empty-state">No orders in this period</div>
        <?php else: ?>
            <canvas id="statusChart" height="200"></canvas>
            <div style="margin-top:15px;">
                <?php foreach ($statusBreakdown as $status): ?>
                    <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--gray-100);">
                        <span class="status-badge status-<?= $status['status'] ?>"><?= ucfirst(str_replace('_', ' ', $status['status'])) ?></span>
                        <span style="font-weight:600;"><?= $status['count'] ?> orders</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Low Stock Alerts -->
<?php if (!empty($lowStockProducts)): ?>
    <div class="section" style="margin-bottom:20px;">
        <h2>Low Stock Alerts</h2>
        <div class="alert-warning">
            <strong><?= count($lowStockProducts) ?> products</strong> are running low on stock!
        </div>
        <ul class="product-list">
            <?php foreach ($lowStockProducts as $product): ?>
                <li class="product-item">
                    <div class="product-info">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= asset($product['image_path']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-img">
                        <?php else: ?>
                            <div class="product-img" style="display:flex;align-items:center;justify-content:center;color:var(--gray-400);"><i class="fa fa-box"></i></div>
                        <?php endif; ?>
                        <div>
                            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                            <div class="product-sku"><?= $product['category_name'] ? htmlspecialchars($product['category_name']) : 'No category' ?></div>
                        </div>
                    </div>
                    <div class="product-stats">
                        <div style="font-weight:700;color:var(--danger);font-size:16px;"><?= $product['stock_quantity'] ?> left</div>
                        <div class="product-units">Alert at <?= $product['low_stock_alert'] ?></div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Product Statistics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Total Products</div>
        <div class="metric-value"><?= $productStats['total_products'] ?></div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Active Products</div>
        <div class="metric-value" style="color:#166534;"><?= $productStats['active_products'] ?></div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Out of Stock</div>
        <div class="metric-value" style="color:var(--danger);"><?= $productStats['out_of_stock'] ?></div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Low Stock</div>
        <div class="metric-value" style="color:#e65100;"><?= $productStats['low_stock'] ?></div>
    </div>
</div>

<script>
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: <?= $chartLabels ?>,
        datasets: [
            { label: 'Revenue (CAD$)', data: <?= $chartRevenue ?>, borderColor: '#00b207', backgroundColor: 'rgba(0,178,7,0.1)', tension: 0.4, yAxisID: 'y' },
            { label: 'Orders', data: <?= $chartOrders ?>, borderColor: '#2196F3', backgroundColor: 'rgba(33,150,243,0.1)', tension: 0.4, yAxisID: 'y1' }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { display: true, position: 'top' } },
        scales: {
            y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Revenue (CAD$)' } },
            y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Orders' }, grid: { drawOnChartArea: false } }
        }
    }
});

<?php if (!empty($statusBreakdown)): ?>
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusLabels = <?= json_encode(array_map(function($s) { return ucfirst(str_replace('_', ' ', $s['status'])); }, $statusBreakdown)) ?>;
const statusData = <?= json_encode(array_column($statusBreakdown, 'count')) ?>;

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{ data: statusData, backgroundColor: ['#ffc107', '#2196F3', '#9c27b0', '#4CAF50', '#f44336', '#9e9e9e', '#795548'] }]
    },
    options: { responsive: true, plugins: { legend: { display: false } } }
});
<?php endif; ?>
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
