<?php
/**
 * Admin Reports Dashboard
 * File: app/Views/admin/reports/index.php
 */

$pageTitle = 'Reports & Analytics';
$currentPage = 'reports';

// Get date range from request
$startDate = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$endDate = $_GET['end_date'] ?? date('Y-m-d'); // Today
$period = $_GET['period'] ?? 'month'; // day, week, month, year

ob_start();
?>

<style>
  /* Page Layout */
  .reports-page {
    max-width: 1600px;
    margin: 0 auto;
  }

  /* Page Header */
  .reports-header {
    margin-bottom: 32px;
  }

  .header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
  }

  .page-subtitle {
    font-size: 15px;
    color: var(--gray-600);
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-secondary {
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
  }

  .btn-secondary:hover {
    background: var(--gray-50);
  }

  /* Date Filter Card */
  .date-filter-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .filter-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr) auto;
    gap: 16px;
    align-items: end;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
  }

  .filter-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .filter-input,
  .filter-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .filter-input:focus,
  .filter-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  /* Quick Period Buttons */
  .quick-periods {
    display: flex;
    gap: 8px;
    margin-top: 16px;
  }

  .period-btn {
    padding: 8px 16px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .period-btn:hover {
    background: var(--gray-50);
  }

  .period-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: white;
    padding: 24px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    border-left: 4px solid;
    transition: transform var(--transition-base);
    position: relative;
    overflow: hidden;
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }

  .stat-card.revenue { border-color: var(--primary); }
  .stat-card.orders { border-color: #3b82f6; }
  .stat-card.customers { border-color: #f59e0b; }
  .stat-card.products { border-color: #8b5cf6; }
  .stat-card.avg-order { border-color: #ec4899; }

  .stat-icon {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 32px;
    opacity: 0.1;
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 16px;
  }

  .stat-label {
    font-size: 13px;
    color: var(--gray-600);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.05em;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .stat-change {
    font-size: 13px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: var(--radius-sm);
  }

  .stat-change.up {
    background: #dcfce7;
    color: #166534;
  }

  .stat-change.down {
    background: #fee2e2;
    color: #991b1b;
  }

  /* Charts Grid */
  .charts-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
  }

  .chart-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
  }

  .chart-card.full-width {
    grid-column: 1 / -1;
  }

  .chart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border);
  }

  .chart-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
  }

  .chart-subtitle {
    font-size: 13px;
    color: var(--gray-500);
    margin-top: 4px;
  }

  .chart-canvas {
    height: 300px;
  }

  /* Report Links Grid */
  .report-links-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
  }

  .report-link-card {
    background: white;
    padding: 24px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
    text-decoration: none;
    display: flex;
    align-items: start;
    gap: 16px;
    border: 2px solid transparent;
  }

  .report-link-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary);
  }

  .report-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .report-icon.sales { background: #dcfce7; color: var(--primary); }
  .report-icon.products { background: #dbeafe; color: #3b82f6; }
  .report-icon.customers { background: #fef3c7; color: #f59e0b; }
  .report-icon.inventory { background: #e0e7ff; color: #6366f1; }

  .report-info {
    flex: 1;
  }

  .report-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 4px;
  }

  .report-desc {
    font-size: 13px;
    color: var(--gray-600);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .charts-grid {
      grid-template-columns: 1fr;
    }

    .filter-row {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .report-links-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="reports-page">
  <!-- Page Header -->
  <div class="reports-header">
    <div class="header-top">
      <div>
        <h1 class="page-title">Reports & Analytics</h1>
      </div>
      <div class="header-actions">
        <button onclick="exportReport()" class="btn btn-secondary">
          <i class="fas fa-file-pdf"></i> Export PDF
        </button>
        <button onclick="printReport()" class="btn btn-secondary">
          <i class="fas fa-print"></i> Print
        </button>
      </div>
    </div>
    <p class="page-subtitle">Comprehensive business analytics and insights</p>
  </div>

  <!-- Date Filter -->
  <div class="date-filter-card">
    <form method="GET">
      <div class="filter-row">
        <div class="filter-group">
          <label class="filter-label">Start Date</label>
          <input 
            type="date" 
            name="start_date" 
            value="<?= htmlspecialchars($startDate) ?>"
            class="filter-input"
          >
        </div>

        <div class="filter-group">
          <label class="filter-label">End Date</label>
          <input 
            type="date" 
            name="end_date" 
            value="<?= htmlspecialchars($endDate) ?>"
            class="filter-input"
          >
        </div>

        <div class="filter-group">
          <label class="filter-label">Period</label>
          <select name="period" class="filter-select">
            <option value="day" <?= $period === 'day' ? 'selected' : '' ?>>Daily</option>
            <option value="week" <?= $period === 'week' ? 'selected' : '' ?>>Weekly</option>
            <option value="month" <?= $period === 'month' ? 'selected' : '' ?>>Monthly</option>
            <option value="year" <?= $period === 'year' ? 'selected' : '' ?>>Yearly</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">Comparison</label>
          <select name="compare" class="filter-select">
            <option value="none">No Comparison</option>
            <option value="previous">Previous Period</option>
            <option value="last_year">Last Year</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label" style="visibility: hidden;">Action</label>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search"></i> Apply
          </button>
        </div>
      </div>

      <!-- Quick Period Buttons -->
      <div class="quick-periods">
        <button type="button" class="period-btn" onclick="setQuickPeriod('today')">Today</button>
        <button type="button" class="period-btn" onclick="setQuickPeriod('yesterday')">Yesterday</button>
        <button type="button" class="period-btn" onclick="setQuickPeriod('last7days')">Last 7 Days</button>
        <button type="button" class="period-btn active" onclick="setQuickPeriod('thismonth')">This Month</button>
        <button type="button" class="period-btn" onclick="setQuickPeriod('lastmonth')">Last Month</button>
        <button type="button" class="period-btn" onclick="setQuickPeriod('thisyear')">This Year</button>
      </div>
    </form>
  </div>

  <!-- Key Stats -->
  <div class="stats-grid">
    <div class="stat-card revenue">
      <i class="fas fa-dollar-sign stat-icon"></i>
      <div class="stat-header">
        <div class="stat-label">Total Revenue</div>
      </div>
      <div class="stat-value"><?= currency($reportData['revenue'] ?? 0) ?></div>
      <span class="stat-change up">
        <i class="fas fa-arrow-up"></i> 12.5%
      </span>
    </div>

    <div class="stat-card orders">
      <i class="fas fa-shopping-cart stat-icon"></i>
      <div class="stat-header">
        <div class="stat-label">Total Orders</div>
      </div>
      <div class="stat-value"><?= number_format($reportData['orders'] ?? 0) ?></div>
      <span class="stat-change up">
        <i class="fas fa-arrow-up"></i> 8.3%
      </span>
    </div>

    <div class="stat-card customers">
      <i class="fas fa-users stat-icon"></i>
      <div class="stat-header">
        <div class="stat-label">New Customers</div>
      </div>
      <div class="stat-value"><?= number_format($reportData['customers'] ?? 0) ?></div>
      <span class="stat-change up">
        <i class="fas fa-arrow-up"></i> 15.2%
      </span>
    </div>

    <div class="stat-card products">
      <i class="fas fa-box stat-icon"></i>
      <div class="stat-header">
        <div class="stat-label">Products Sold</div>
      </div>
      <div class="stat-value"><?= number_format($reportData['products_sold'] ?? 0) ?></div>
      <span class="stat-change down">
        <i class="fas fa-arrow-down"></i> 3.1%
      </span>
    </div>

    <div class="stat-card avg-order">
      <i class="fas fa-receipt stat-icon"></i>
      <div class="stat-header">
        <div class="stat-label">Avg Order Value</div>
      </div>
      <div class="stat-value"><?= currency($reportData['avg_order'] ?? 0) ?></div>
      <span class="stat-change up">
        <i class="fas fa-arrow-up"></i> 5.7%
      </span>
    </div>
  </div>

  <!-- Charts -->
  <div class="charts-grid">
    <!-- Sales Chart -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Sales Overview</h3>
          <p class="chart-subtitle">Revenue and orders trend</p>
        </div>
      </div>
      <canvas id="salesChart" class="chart-canvas"></canvas>
    </div>

    <!-- Top Products -->
    <div class="chart-card">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Top Products</h3>
          <p class="chart-subtitle">Best sellers</p>
        </div>
      </div>
      <canvas id="productsChart" class="chart-canvas"></canvas>
    </div>

    <!-- Revenue by Category -->
    <div class="chart-card full-width">
      <div class="chart-header">
        <div>
          <h3 class="chart-title">Revenue by Category</h3>
          <p class="chart-subtitle">Sales distribution across categories</p>
        </div>
      </div>
      <canvas id="categoryChart" class="chart-canvas"></canvas>
    </div>
  </div>

  <!-- Report Links -->
  <div class="report-links-grid">
    <a href="<?= url('admin/reports/sales') ?>" class="report-link-card">
      <div class="report-icon sales">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="report-info">
        <h4 class="report-title">Sales Report</h4>
        <p class="report-desc">Detailed sales analytics and trends</p>
      </div>
    </a>

    <a href="<?= url('admin/reports/products') ?>" class="report-link-card">
      <div class="report-icon products">
        <i class="fas fa-boxes"></i>
      </div>
      <div class="report-info">
        <h4 class="report-title">Products Report</h4>
        <p class="report-desc">Product performance and inventory</p>
      </div>
    </a>

    <a href="<?= url('admin/reports/customers') ?>" class="report-link-card">
      <div class="report-icon customers">
        <i class="fas fa-user-friends"></i>
      </div>
      <div class="report-info">
        <h4 class="report-title">Customers Report</h4>
        <p class="report-desc">Customer analytics and behavior</p>
      </div>
    </a>

    <a href="<?= url('admin/reports/inventory') ?>" class="report-link-card">
      <div class="report-icon inventory">
        <i class="fas fa-warehouse"></i>
      </div>
      <div class="report-info">
        <h4 class="report-title">Inventory Report</h4>
        <p class="report-desc">Stock levels and movements</p>
      </div>
    </a>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Quick period selector
function setQuickPeriod(period) {
  const today = new Date();
  let startDate, endDate;

  switch(period) {
    case 'today':
      startDate = endDate = today.toISOString().split('T')[0];
      break;
    case 'yesterday':
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      startDate = endDate = yesterday.toISOString().split('T')[0];
      break;
    case 'last7days':
      const last7 = new Date(today);
      last7.setDate(last7.getDate() - 7);
      startDate = last7.toISOString().split('T')[0];
      endDate = today.toISOString().split('T')[0];
      break;
    case 'thismonth':
      startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
      endDate = today.toISOString().split('T')[0];
      break;
    case 'lastmonth':
      const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
      startDate = lastMonth.toISOString().split('T')[0];
      const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
      endDate = lastMonthEnd.toISOString().split('T')[0];
      break;
    case 'thisyear':
      startDate = new Date(today.getFullYear(), 0, 1).toISOString().split('T')[0];
      endDate = today.toISOString().split('T')[0];
      break;
  }

  document.querySelector('[name="start_date"]').value = startDate;
  document.querySelector('[name="end_date"]').value = endDate;
  document.querySelector('form').submit();
}

// Sales Chart
const salesCtx = document.getElementById('salesChart');
new Chart(salesCtx, {
  type: 'line',
  data: {
    labels: <?= json_encode($chartData['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) ?>,
    datasets: [{
      label: 'Revenue',
      data: <?= json_encode($chartData['revenue'] ?? [12000, 19000, 15000, 25000, 22000, 30000]) ?>,
      borderColor: '#00B207',
      backgroundColor: 'rgba(0, 178, 7, 0.1)',
      tension: 0.4,
      fill: true
    }, {
      label: 'Orders',
      data: <?= json_encode($chartData['orders'] ?? [45, 75, 60, 95, 85, 110]) ?>,
      borderColor: '#3b82f6',
      backgroundColor: 'rgba(59, 130, 246, 0.1)',
      tension: 0.4,
      fill: true
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// Top Products Chart
const productsCtx = document.getElementById('productsChart');
new Chart(productsCtx, {
  type: 'doughnut',
  data: {
    labels: <?= json_encode($chartData['product_labels'] ?? ['Product A', 'Product B', 'Product C', 'Product D']) ?>,
    datasets: [{
      data: <?= json_encode($chartData['product_sales'] ?? [35, 25, 20, 20]) ?>,
      backgroundColor: ['#00B207', '#3b82f6', '#f59e0b', '#8b5cf6']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// Category Chart
const categoryCtx = document.getElementById('categoryChart');
new Chart(categoryCtx, {
  type: 'bar',
  data: {
    labels: <?= json_encode($chartData['category_labels'] ?? ['Electronics', 'Clothing', 'Food', 'Books', 'Sports']) ?>,
    datasets: [{
      label: 'Revenue',
      data: <?= json_encode($chartData['category_revenue'] ?? [45000, 38000, 32000, 28000, 25000]) ?>,
      backgroundColor: '#00B207'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: false
      }
    }
  }
});

// Export & Print functions
function exportReport() {
  window.location.href = '<?= url('admin/reports/export') ?>?' + new URLSearchParams(window.location.search);
}

function printReport() {
  window.print();
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>