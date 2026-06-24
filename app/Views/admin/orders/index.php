<?php
/**
 * Admin Orders Management Page - CLEANED & STYLED
 * File: app/Views/admin/orders/index.php
 */

$pageTitle = 'Orders Management';
$currentPage = 'orders';

// Fix undefined variable warnings
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? 'all';
$page = (int)($_GET['page'] ?? 1);

ob_start();
?>

<style>
  /* Page Layout */
  .orders-page {
    max-width: 1600px;
    margin: 0 auto;
  }

  /* Page Header */
  .orders-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    gap: 24px;
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
    margin-top: 8px;
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn-export {
    padding: 12px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-export:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
  }

  .stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
  }

  .stat-card.blue { border-color: #3b82f6; }
  .stat-card.orange { border-color: #f97316; }
  .stat-card.green { border-color: var(--primary); }
  .stat-card.red { border-color: #ef4444; }
  .stat-card.purple { border-color: #a855f7; }

  .stat-label {
    font-size: 13px;
    color: var(--gray-600);
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.05em;
    margin-bottom: 8px;
  }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
  }

  .stat-change {
    font-size: 12px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 4px;
  }

  .stat-change.up {
    color: #16a34a;
  }

  .stat-change.down {
    color: #dc2626;
  }

  /* Filters Card */
  .filters-card {
    background: white;
    padding: 24px;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
  }

  .filter-row {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
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

  .filter-actions {
    display: flex;
    gap: 8px;
  }

  .btn-filter {
    padding: 12px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-filter:hover {
    background: var(--primary-600);
  }

  .btn-reset {
    padding: 12px 20px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-reset:hover {
    background: var(--gray-50);
  }

  /* Orders Table */
  .orders-table {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 16px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  th.text-right {
    text-align: right;
  }

  td {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  td.text-right {
    text-align: right;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .order-number {
    font-weight: 700;
    color: var(--dark);
    font-size: 14px;
  }

  .customer-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .customer-name {
    font-weight: 600;
    color: var(--dark);
  }

  .customer-email {
    font-size: 12px;
    color: var(--gray-500);
  }

  .shop-name {
    font-weight: 500;
    color: var(--gray-700);
  }

  .items-count {
    color: var(--gray-600);
  }

  .order-total {
    font-weight: 700;
    font-size: 15px;
    color: var(--dark);
  }

  /* Status Badges */
  .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .status-pending { 
    background: #fef3c7; 
    color: #92400e; 
  }

  .status-processing { 
    background: #dbeafe; 
    color: #1e40af; 
  }

  .status-shipped { 
    background: #e0e7ff; 
    color: #3730a3; 
  }

  .status-delivered { 
    background: #dcfce7; 
    color: #166534; 
  }

  .status-cancelled { 
    background: #fee2e2; 
    color: #991b1b; 
  }

  .status-refunded { 
    background: #fce7f3; 
    color: #831843; 
  }

  /* Action Button */
  .action-btn {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
    transition: color var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 6px;
  }

  .action-btn:hover {
    color: var(--primary-600);
  }

  /* Empty State */
  .empty-state {
    padding: 64px 24px;
    text-align: center;
  }

  .empty-state-icon {
    font-size: 48px;
    color: var(--gray-400);
    margin-bottom: 16px;
  }

  .empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-500);
  }

  /* Pagination */
  .pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: var(--gray-50);
    border-top: 1px solid var(--border);
  }

  .pagination-info {
    font-size: 14px;
    color: var(--gray-700);
  }

  .pagination {
    display: flex;
    gap: 8px;
  }

  .pagination-btn {
    padding: 8px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    background: white;
    text-decoration: none;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .pagination-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
  }

  .pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .orders-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .header-actions {
      width: 100%;
    }

    .btn-export {
      flex: 1;
      justify-content: center;
    }

    .filter-row {
      grid-template-columns: 1fr;
    }

    .filter-actions {
      width: 100%;
    }

    .btn-filter,
    .btn-reset {
      flex: 1;
      justify-content: center;
    }

    .stats-grid {
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
  }

  @media (max-width: 768px) {
    .orders-table {
      overflow-x: auto;
    }

    table {
      min-width: 800px;
    }

    .pagination-wrapper {
      flex-direction: column;
      gap: 16px;
    }

    .pagination {
      flex-wrap: wrap;
      justify-content: center;
    }
  }
</style>

<div class="orders-page">
  <!-- Page Header -->
  <div class="orders-header">
    <div>
      <h1 class="page-title">Orders Management</h1>
      <p class="page-subtitle">Manage and track all customer orders</p>
    </div>
    <div class="header-actions">
      <button onclick="exportOrders()" class="btn-export">
        <i class="fas fa-download"></i> Export Orders
      </button>
    </div>
  </div>

  <!-- Stats Grid -->
  <div class="stats-grid">
    <div class="stat-card blue">
      <div class="stat-label">Total Orders</div>
      <div class="stat-value"><?= number_format($stats['total_orders'] ?? 0) ?></div>
    </div>

    <div class="stat-card orange">
      <div class="stat-label">Pending</div>
      <div class="stat-value"><?= number_format($stats['pending'] ?? 0) ?></div>
    </div>

    <div class="stat-card blue">
      <div class="stat-label">Processing</div>
      <div class="stat-value"><?= number_format($stats['processing'] ?? 0) ?></div>
    </div>

    <div class="stat-card green">
      <div class="stat-label">Delivered</div>
      <div class="stat-value"><?= number_format($stats['delivered'] ?? 0) ?></div>
    </div>

    <div class="stat-card purple">
      <div class="stat-label">Total Revenue</div>
      <div class="stat-value"><?= currency($stats['total_revenue'] ?? 0) ?></div>
    </div>
  </div>

  <!-- Filters -->
  <div class="filters-card">
    <form method="GET">
      <div class="filter-row">
        <!-- Search -->
        <div class="filter-group">
          <label class="filter-label">Search</label>
          <input 
            type="search" 
            name="search" 
            value="<?= htmlspecialchars($search) ?>"
            placeholder="Search by order #, customer name, email..."
            class="filter-input"
          >
        </div>

        <!-- Status Filter -->
        <div class="filter-group">
          <label class="filter-label">Status</label>
          <select name="status" class="filter-select">
            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>All Status</option>
            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
          </select>
        </div>

        <!-- Filter Actions -->
        <div class="filter-group">
          <label class="filter-label" style="visibility: hidden;">Actions</label>
          <div class="filter-actions">
            <button type="submit" class="btn-filter">
              <i class="fas fa-filter"></i> Filter
            </button>
            <a href="<?= url('admin/orders') ?>" class="btn-reset">
              <i class="fas fa-redo"></i>
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>

  <!-- Orders Table -->
  <div class="orders-table">
    <table>
      <thead>
        <tr>
          <th>Order #</th>
          <th>Customer</th>
          <th>Shop</th>
          <th>Items</th>
          <th class="text-right">Total</th>
          <th>Status</th>
          <th>Date</th>
          <th class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($orders)): ?>
          <?php foreach ($orders as $order): ?>
          <tr>
            <td>
              <span class="order-number">#<?= htmlspecialchars($order['order_number'] ?? 'N/A') ?></span>
            </td>
            
            <td>
              <div class="customer-info">
                <span class="customer-name">
                  <?= htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')) ?>
                </span>
                <span class="customer-email">
                  <?= htmlspecialchars($order['email'] ?? 'N/A') ?>
                </span>
              </div>
            </td>

            <td>
              <span class="shop-name"><?= htmlspecialchars($order['shop_name'] ?? 'N/A') ?></span>
            </td>

            <td>
              <span class="items-count"><?= (int)($order['items_count'] ?? 0) ?> items</span>
            </td>

            <td class="text-right">
              <span class="order-total"><?= currency($order['total'] ?? 0) ?></span>
            </td>

            <td>
              <span class="status-badge status-<?= $order['status'] ?? 'pending' ?>">
                <?= ucfirst($order['status'] ?? 'Pending') ?>
              </span>
            </td>

            <td>
              <?= date('M d, Y', strtotime($order['created_at'] ?? 'now')) ?>
              <br>
              <small style="color: var(--gray-500);">
                <?= date('g:i A', strtotime($order['created_at'] ?? 'now')) ?>
              </small>
            </td>

            <td class="text-right">
              <a href="<?= url('admin/orders/view?id=' . ($order['id'] ?? '')) ?>" class="action-btn">
                <i class="fas fa-eye"></i> View
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="empty-state-title">No orders found</div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>

    <!-- Pagination -->
    <?php if (($totalPages ?? 1) > 1): ?>
      <div class="pagination-wrapper">
        <div class="pagination-info">
          Showing <?= ((($page ?? 1) - 1) * ($perPage ?? 20)) + 1 ?> 
          to <?= min(($page ?? 1) * ($perPage ?? 20), $total ?? 0) ?> 
          of <?= $total ?? 0 ?> orders
        </div>
        
        <div class="pagination">
          <?php if (($page ?? 1) > 1): ?>
            <a 
              href="?page=<?= ($page ?? 1) - 1 ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
              class="pagination-btn"
            >
              <i class="fas fa-chevron-left"></i> Previous
            </a>
          <?php endif; ?>

          <?php 
          $startPage = max(1, ($page ?? 1) - 2);
          $endPage = min($totalPages ?? 1, ($page ?? 1) + 2);
          
          for ($i = $startPage; $i <= $endPage; $i++): 
          ?>
            <a 
              href="?page=<?= $i ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
              class="pagination-btn <?= $i === ($page ?? 1) ? 'active' : '' ?>"
            >
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if (($page ?? 1) < ($totalPages ?? 1)): ?>
            <a 
              href="?page=<?= ($page ?? 1) + 1 ?><?= $status !== 'all' ? '&status=' . urlencode($status) : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" 
              class="pagination-btn"
            >
              Next <i class="fas fa-chevron-right"></i>
            </a>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Export orders functionality
function exportOrders() {
  const params = new URLSearchParams(window.location.search);
  window.location.href = '<?= url('admin/orders/export') ?>?' + params.toString();
}

// Auto-refresh page every 30 seconds for real-time updates
<?php if (empty($search) && $status === 'all'): ?>
setTimeout(() => {
  location.reload();
}, 30000);
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>