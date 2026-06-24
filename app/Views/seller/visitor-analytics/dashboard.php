<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - <?= htmlspecialchars($shop['name']) ?></title>
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .header { background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { font-size: 24px; color: #333; margin-bottom: 10px; }
        .date-filter { display: flex; gap: 15px; align-items: center; flex-wrap: wrap; }
        .date-filter input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; }
        .date-filter button { padding: 8px 20px; background: #4CAF50; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; }
        .date-filter button:hover { background: #45a049; }
        .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .metric-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .metric-label { font-size: 14px; color: #666; margin-bottom: 8px; }
        .metric-value { font-size: 32px; font-weight: 700; color: #333; margin-bottom: 8px; }
        .metric-change { font-size: 14px; display: flex; align-items: center; gap: 5px; }
        .metric-change.positive { color: #4CAF50; }
        .metric-change.negative { color: #f44336; }
        .chart-container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .chart-container h2 { font-size: 18px; margin-bottom: 20px; color: #333; }
        .two-column { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .section { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .section h2 { font-size: 18px; margin-bottom: 15px; color: #333; }
        .product-list { list-style: none; }
        .product-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0; }
        .product-item:last-child { border-bottom: none; }
        .product-info { display: flex; align-items: center; gap: 12px; flex: 1; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; background: #f0f0f0; }
        .product-name { font-weight: 600; color: #333; font-size: 14px; }
        .product-sku { font-size: 12px; color: #666; }
        .product-stats { text-align: right; }
        .product-revenue { font-weight: 700; color: #4CAF50; font-size: 16px; }
        .product-units { font-size: 12px; color: #666; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-delivered { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .alert-warning { background: #fff3cd; border: 1px solid #ffc107; color: #856404; padding: 12px; border-radius: 5px; margin-bottom: 15px; }
        .empty-state { text-align: center; padding: 40px; color: #999; }
        @media (max-width: 768px) {
            .two-column { grid-template-columns: 1fr; }
            .metrics-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header with Date Filter -->
        <div class="header">
            <h1>📊 Analytics Dashboard</h1>
            <p style="color: #666; margin-bottom: 15px;"><?= htmlspecialchars($shop['name']) ?></p>
            
            <form method="GET" action="<?= url('seller/analytics') ?>" class="date-filter">
                <div>
                    <label>From:</label>
                    <input type="date" name="start_date" value="<?= $startDate ?>" required>
                </div>
                <div>
                    <label>To:</label>
                    <input type="date" name="end_date" value="<?= $endDate ?>" required>
                </div>
                <button type="submit">Update</button>
                <a href="<?= url('seller/dashboard') ?>" style="padding: 8px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Back</a>
            </form>
        </div>

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
                <div class="metric-change" style="color: #666;">
                    Per order average
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Completion Rate</div>
                <div class="metric-value">
                    <?= $summary['total_orders'] > 0 ? round(($summary['completed_orders'] / $summary['total_orders']) * 100, 1) : 0 ?>%
                </div>
                <div class="metric-change" style="color: #666;">
                    <?= $summary['completed_orders'] ?> of <?= $summary['total_orders'] ?> delivered
                </div>
            </div>
        </div>

        <!-- Sales Chart -->
        <div class="chart-container">
            <h2>📈 Sales Trend</h2>
            <canvas id="salesChart" height="80"></canvas>
        </div>

        <!-- Two Column Layout -->
        <div class="two-column">
            <!-- Top Products -->
            <div class="section">
                <h2>🏆 Top Selling Products</h2>
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
                                        <div class="product-img" style="display: flex; align-items: center; justify-content: center; background: #e0e0e0;">📦</div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                        <div class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></div>
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

            <!-- Order Status Breakdown -->
            <div class="section">
                <h2>📦 Order Status</h2>
                <canvas id="statusChart" height="200"></canvas>
                <div style="margin-top: 15px;">
                    <?php foreach ($statusBreakdown as $status): ?>
                        <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0f0f0;">
                            <span class="status-badge status-<?= $status['status'] ?>">
                                <?= ucfirst($status['status']) ?>
                            </span>
                            <span style="font-weight: 600;"><?= $status['count'] ?> orders</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <?php if (!empty($lowStockProducts)): ?>
            <div class="section">
                <h2>⚠️ Low Stock Alerts</h2>
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
                                    <div class="product-img" style="display: flex; align-items: center; justify-content: center; background: #e0e0e0;">📦</div>
                                <?php endif; ?>
                                <div>
                                    <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
                                    <div class="product-sku">
                                        <?= $product['category_name'] ? htmlspecialchars($product['category_name']) : 'No category' ?>
                                    </div>
                                </div>
                            </div>
                            <div class="product-stats">
                                <div style="font-weight: 700; color: #f44336; font-size: 18px;">
                                    <?= $product['stock_quantity'] ?> left
                                </div>
                                <div class="product-units">Alert at <?= $product['low_stock_alert'] ?></div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Product Statistics -->
        <div class="metrics-grid" style="margin-top: 20px;">
            <div class="metric-card">
                <div class="metric-label">Total Products</div>
                <div class="metric-value"><?= $productStats['total_products'] ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Active Products</div>
                <div class="metric-value" style="color: #4CAF50;"><?= $productStats['active_products'] ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Out of Stock</div>
                <div class="metric-value" style="color: #f44336;"><?= $productStats['out_of_stock'] ?></div>
            </div>
            <div class="metric-card">
                <div class="metric-label">Low Stock</div>
                <div class="metric-value" style="color: #ff9800;"><?= $productStats['low_stock'] ?></div>
            </div>
        </div>
    </div>

    <script>
        // Sales Trend Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: <?= $chartLabels ?>,
                datasets: [
                    {
                        label: 'Revenue (CAD$)',
                        data: <?= $chartRevenue ?>,
                        borderColor: '#4CAF50',
                        backgroundColor: 'rgba(76, 175, 80, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Orders',
                        data: <?= $chartOrders ?>,
                        borderColor: '#2196F3',
                        backgroundColor: 'rgba(33, 150, 243, 0.1)',
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Revenue (CAD$)' } },
                    y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Orders' }, grid: { drawOnChartArea: false } }
                }
            }
        });

        // Status Breakdown Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusLabels = <?= json_encode(array_column($statusBreakdown, 'status')) ?>;
        const statusData = <?= json_encode(array_column($statusBreakdown, 'count')) ?>;
        
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels.map(s => s.charAt(0).toUpperCase() + s.slice(1)),
                datasets: [{
                    data: statusData,
                    backgroundColor: ['#ffc107', '#2196F3', '#4CAF50', '#f44336', '#9e9e9e']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });
    </script>
</body>
</html>