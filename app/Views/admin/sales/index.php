<?php
$pageTitle = 'Sales Management';
$currentPage = 'sales';
ob_start();
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }

  .page-title {
    font-size: 28px;
    color: #1f2937;
    margin: 0;
  }

  .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }

  .btn-primary {
    background: linear-gradient(135deg, #00b207 0%, #008505 100%);
    color: white;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
  }

  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
  }

  .stat-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .stat-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
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
    color: #1f2937;
    line-height: 1;
  }

  .stat-label {
    font-size: 14px;
    color: #6b7280;
    margin-top: 8px;
  }

  .card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .tabs {
    display: flex;
    gap: 16px;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 24px;
  }

  .tab {
    padding: 12px 24px;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    font-weight: 600;
    color: #6b7280;
    transition: all 0.2s;
  }

  .tab.active {
    color: #00b207;
    border-bottom-color: #00b207;
  }

  .tab:hover {
    color: #00b207;
  }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: #f9fafb;
  }

  th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
  }

  td {
    padding: 16px 12px;
    border-top: 1px solid #e5e7eb;
  }

  .product-info {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .product-image {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
  }

  .product-details h4 {
    font-size: 14px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 4px 0;
  }

  .product-details p {
    font-size: 12px;
    color: #6b7280;
    margin: 0;
  }

  .badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-success { background: #dcfce7; color: #166534; }
  .badge-warning { background: #fef3c7; color: #92400e; }
  .badge-danger { background: #fee2e2; color: #991b1b; }

  .price-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .sale-price {
    font-size: 16px;
    font-weight: 700;
    color: #00b207;
  }

  .original-price {
    font-size: 14px;
    color: #9ca3af;
    text-decoration: line-through;
  }

  .discount-badge {
    background: #00b207;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 700;
  }

  .action-buttons {
    display: flex;
    gap: 8px;
  }

  .btn-icon {
    padding: 8px;
    border: none;
    background: transparent;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.2s;
    border-radius: 4px;
  }

  .btn-icon:hover {
    background: #f3f4f6;
    color: #00b207;
  }

  .btn-icon.danger:hover {
    color: #ef4444;
  }

  .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    color: #d1d5db;
  }
</style>

<div class="page-header">
  <h1 class="page-title">Sales Management</h1>
  <a href="<?= url('admin/sales/create') ?>" class="btn btn-primary">
    <i class="fas fa-plus"></i>
    Create New Sale
  </a>
</div>

<!-- Statistics -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= $stats['total_sale_products'] ?? 0 ?></div>
        <div class="stat-label">Active Sales</div>
      </div>
      <div class="stat-icon success">
        <i class="fas fa-tag"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= number_format($stats['avg_discount'] ?? 0, 1) ?>%</div>
        <div class="stat-label">Avg Discount</div>
      </div>
      <div class="stat-icon primary">
        <i class="fas fa-percent"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= $stats['upcoming_count'] ?? 0 ?></div>
        <div class="stat-label">Upcoming Sales</div>
      </div>
      <div class="stat-icon warning">
        <i class="fas fa-clock"></i>
      </div>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <div>
        <div class="stat-value"><?= $stats['expired_count'] ?? 0 ?></div>
        <div class="stat-label">Recently Ended</div>
      </div>
      <div class="stat-icon danger">
        <i class="fas fa-history"></i>
      </div>
    </div>
  </div>
</div>

<!-- Tabs -->
<div class="card">
  <div class="tabs">
    <div class="tab active" onclick="switchTab('active')">
      <i class="fas fa-fire"></i> Active Sales (<?= count($saleProducts) ?>)
    </div>
    <div class="tab" onclick="switchTab('upcoming')">
      <i class="fas fa-calendar-alt"></i> Upcoming (<?= count($upcomingSales) ?>)
    </div>
    <div class="tab" onclick="switchTab('expired')">
      <i class="fas fa-history"></i> Recently Ended (<?= count($expiredSales) ?>)
    </div>
  </div>

  <!-- Active Sales -->
  <div id="active" class="tab-content active">
    <?php if (!empty($saleProducts)): ?>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Price</th>
            <th>Discount</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($saleProducts as $product): ?>
            <tr>
              <td>
                <div class="product-info">
                  <img src="<?= asset($product['image_path'] ?? 'images/placeholder.png') ?>" alt="" class="product-image">
                  <div class="product-details">
                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                    <p>SKU: <?= htmlspecialchars($product['sku']) ?></p>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
              <td>
                <div class="price-info">
                  <span class="sale-price">$<?= number_format($product['sale_price'], 2) ?></span>
                  <span class="original-price">$<?= number_format($product['base_price'], 2) ?></span>
                </div>
              </td>
              <td><span class="discount-badge"><?= number_format($product['sale_percentage'], 0) ?>% OFF</span></td>
              <td><?= $product['sale_start_date'] ? date('M d, Y', strtotime($product['sale_start_date'])) : 'Immediate' ?></td>
              <td><?= $product['sale_end_date'] ? date('M d, Y', strtotime($product['sale_end_date'])) : 'No end date' ?></td>
              <td>
                <div class="action-buttons">
                  <a href="<?= url('admin/sales/edit?id=' . $product['id']) ?>" class="btn-icon" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <button onclick="endSale(<?= $product['id'] ?>)" class="btn-icon danger" title="End Sale">
                    <i class="fas fa-stop-circle"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-tags"></i>
        <p>No active sales. Create a new sale to get started.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Upcoming Sales -->
  <div id="upcoming" class="tab-content">
    <?php if (!empty($upcomingSales)): ?>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Sale Price</th>
            <th>Discount</th>
            <th>Starts In</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($upcomingSales as $product): ?>
            <tr>
              <td>
                <div class="product-info">
                  <img src="<?= asset($product['image_path'] ?? 'images/placeholder.png') ?>" alt="" class="product-image">
                  <div class="product-details">
                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                    <p>SKU: <?= htmlspecialchars($product['sku']) ?></p>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
              <td>
                <div class="price-info">
                  <span class="sale-price">$<?= number_format($product['sale_price'], 2) ?></span>
                  <span class="original-price">$<?= number_format($product['base_price'], 2) ?></span>
                </div>
              </td>
              <td><span class="discount-badge"><?= number_format($product['sale_percentage'], 0) ?>% OFF</span></td>
              <td><?= date('M d, Y g:i A', strtotime($product['sale_start_date'])) ?></td>
              <td>
                <div class="action-buttons">
                  <a href="<?= url('admin/sales/edit?id=' . $product['id']) ?>" class="btn-icon" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-calendar-alt"></i>
        <p>No upcoming scheduled sales.</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Expired Sales -->
  <div id="expired" class="tab-content">
    <?php if (!empty($expiredSales)): ?>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Was Sale Price</th>
            <th>Discount</th>
            <th>Ended On</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($expiredSales as $product): ?>
            <tr style="opacity: 0.7;">
              <td>
                <div class="product-info">
                  <img src="<?= asset($product['image_path'] ?? 'images/placeholder.png') ?>" alt="" class="product-image">
                  <div class="product-details">
                    <h4><?= htmlspecialchars($product['name']) ?></h4>
                    <p>SKU: <?= htmlspecialchars($product['sku']) ?></p>
                  </div>
                </div>
              </td>
              <td><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></td>
              <td>$<?= number_format($product['sale_price'], 2) ?></td>
              <td><span class="discount-badge"><?= number_format($product['sale_percentage'], 0) ?>% OFF</span></td>
              <td><?= date('M d, Y g:i A', strtotime($product['sale_end_date'])) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-history"></i>
        <p>No recently ended sales.</p>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(tab => {
      tab.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    event.target.closest('.tab').classList.add('active');
  }

  function endSale(productId) {
    if (!confirm('Are you sure you want to end this sale?')) return;

    const formData = new FormData();
    formData.append('id', productId);
    formData.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', document.querySelector('meta[name="csrf-token"]').content);

    fetch('<?= url('admin/sales/end') ?>', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        location.reload();
      } else {
        alert(data.message || 'Error ending sale');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error ending sale');
    });
  }
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
