<?php
$pageTitle = 'Receive Shipment - PO #' . $po['po_number'];
$currentPage = 'purchase-orders';
ob_start();
?>

<style>
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .breadcrumb {
    display: flex;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-600);
  }

  .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .alert {
    padding: 16px 20px;
    border-radius: var(--radius-lg);
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }

  .alert-info {
    background: #dbeafe;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
  }

  .card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
  }

  .card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 20px;
  }

  .info-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
  }

  .info-box {
    padding: 16px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    text-align: center;
  }

  .info-label {
    font-size: 12px;
    color: var(--gray-600);
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  .info-value {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
  }

  .receive-table {
    width: 100%;
    border-collapse: collapse;
  }

  .receive-table th {
    background: var(--gray-50);
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    border-bottom: 2px solid var(--border);
  }

  .receive-table td {
    padding: 16px 12px;
    border-bottom: 1px solid var(--border);
  }

  .qty-input {
    width: 100px;
    padding: 8px 12px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 16px;
    font-weight: 600;
    text-align: center;
  }

  .qty-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
  }

  .badge {
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.pending { background: #fef3c7; color: #92400e; }
  .badge.partial { background: #dbeafe; color: #1e40af; }
  .badge.complete { background: #dcfce7; color: #166534; }

  .stock-info {
    font-size: 13px;
    color: var(--gray-600);
    margin-top: 4px;
  }

  .form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 24px;
    background: white;
    border-top: 1px solid var(--border);
    position: sticky;
    bottom: 0;
  }

  .btn {
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
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
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .btn-success {
    background: #10b981;
    color: white;
  }

  .btn-success:hover {
    background: #059669;
  }

  .quick-action {
    color: var(--primary);
    cursor: pointer;
    text-decoration: underline;
    font-size: 13px;
  }

  .quick-action:hover {
    color: var(--primary-600);
  }
</style>

<div class="page-header">
  <h1 class="page-title">Receive Shipment</h1>
  <div class="breadcrumb">
    <a href="<?= url('admin/purchase-orders') ?>">Purchase Orders</a>
    <span>/</span>
    <a href="<?= url('admin/purchase-orders/view?id=' . $po['id']) ?>"><?= htmlspecialchars($po['po_number']) ?></a>
    <span>/</span>
    <span>Receive</span>
  </div>
</div>

<div class="alert alert-info">
  <i class="fas fa-info-circle" style="font-size: 20px;"></i>
  <div>
    <strong>Important:</strong> Enter the quantity received for each item. Stock levels will be automatically updated for products linked to marketplace inventory. Products without a marketplace link will be received but stock will not be updated.
  </div>
</div>

<!-- Summary Info -->
<div class="info-grid">
  <div class="info-box">
    <div class="info-label">PO Number</div>
    <div class="info-value"><?= htmlspecialchars($po['po_number']) ?></div>
  </div>

  <div class="info-box">
    <div class="info-label">Supplier</div>
    <div class="info-value" style="font-size: 16px;">
      <?= htmlspecialchars($po['supplier_name'] ?? 'N/A') ?>
    </div>
  </div>

  <div class="info-box">
    <div class="info-label">Order Date</div>
    <div class="info-value" style="font-size: 16px;">
      <?= date('M d, Y', strtotime($po['order_date'])) ?>
    </div>
  </div>

  <div class="info-box">
    <div class="info-label">Status</div>
    <div class="info-value" style="font-size: 16px;">
      <?= ucfirst($po['status']) ?>
    </div>
  </div>
</div>

<!-- Receiving Form -->
<form method="POST" action="<?= url('admin/purchase-orders/process-receiving') ?>" id="receiveForm">
  <?= csrfField() ?>
  <input type="hidden" name="po_id" value="<?= $po['id'] ?>">

  <div class="card">
    <h3 class="card-title">Items to Receive</h3>

    <table class="receive-table">
      <thead>
        <tr>
          <th>Product</th>
          <th style="text-align: center;">SKU</th>
          <th style="text-align: center;">Current Stock</th>
          <th style="text-align: center;">Ordered</th>
          <th style="text-align: center;">Previously Received</th>
          <th style="text-align: center;">Remaining</th>
          <th style="text-align: center;">Receive Now</th>
          <th style="text-align: center;">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($items as $item):
          $remaining = $item['quantity_ordered'] - $item['quantity_received'];
          $status = $item['quantity_received'] == 0 ? 'pending' :
                   ($item['quantity_received'] < $item['quantity_ordered'] ? 'partial' : 'complete');
        ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($item['product_name']) ?></strong>
              <?php if ($item['marketplace_product_id']): ?>
                <div class="stock-info" style="color: #10b981;">
                  <i class="fas fa-link"></i> Linked to: <?= htmlspecialchars($item['marketplace_product_name'] ?? 'Unknown') ?>
                </div>
                <div class="stock-info">
                  Will update: <strong><?= number_format($item['current_stock'] ?? 0) ?></strong> →
                  <strong id="new-stock-<?= $item['id'] ?>"><?= number_format($item['current_stock'] ?? 0) ?></strong> units
                </div>
              <?php else: ?>
                <div class="stock-info" style="color: #f59e0b;">
                  <i class="fas fa-exclamation-triangle"></i> Not linked to marketplace product - stock will not be updated
                </div>
              <?php endif; ?>
            </td>

            <td style="text-align: center;">
              <?= htmlspecialchars($item['product_sku'] ?? 'N/A') ?>
            </td>

            <td style="text-align: center;">
              <?php if ($item['marketplace_product_id']): ?>
                <strong><?= number_format($item['current_stock'] ?? 0) ?></strong>
              <?php else: ?>
                <span style="color: var(--gray-400);">N/A</span>
              <?php endif; ?>
            </td>

            <td style="text-align: center;">
              <strong><?= $item['quantity_ordered'] ?></strong>
            </td>

            <td style="text-align: center;">
              <?= $item['quantity_received'] ?>
            </td>

            <td style="text-align: center;">
              <strong style="color: <?= $remaining > 0 ? '#f59e0b' : '#10b981' ?>">
                <?= $remaining ?>
              </strong>
            </td>

            <td style="text-align: center;">
              <input
                type="number"
                name="received_quantities[<?= $item['id'] ?>]"
                id="qty-<?= $item['id'] ?>"
                class="qty-input"
                min="0"
                max="<?= $remaining ?>"
                value="<?= $remaining ?>"
                data-item-id="<?= $item['id'] ?>"
                data-current-stock="<?= $item['current_stock'] ?? 0 ?>"
                data-has-link="<?= $item['marketplace_product_id'] ? '1' : '0' ?>"
                onchange="updateStockPreview(this)"
              >
              <div style="margin-top: 8px;">
                <span class="quick-action" onclick="setQty(<?= $item['id'] ?>, <?= $remaining ?>)">Fill All</span>
                <span style="color: var(--gray-400); margin: 0 4px;">|</span>
                <span class="quick-action" onclick="setQty(<?= $item['id'] ?>, 0)">Clear</span>
              </div>
            </td>

            <td style="text-align: center;">
              <span class="badge <?= $status ?>" id="status-<?= $item['id'] ?>">
                <?= ucfirst($status) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="form-actions">
    <a href="<?= url('admin/purchase-orders/view?id=' . $po['id']) ?>" class="btn btn-secondary">
      Cancel
    </a>
    <button type="submit" class="btn btn-success" id="submitBtn">
      <i class="fas fa-check-circle"></i> Receive & Update Stock
    </button>
  </div>
</form>

<script>
function setQty(itemId, qty) {
  const input = document.getElementById('qty-' + itemId);
  input.value = qty;
  updateStockPreview(input);
}

function updateStockPreview(input) {
  const itemId = input.dataset.itemId;
  const hasLink = input.dataset.hasLink === '1';

  // Only update preview for products linked to marketplace
  if (!hasLink) return;

  const currentStock = parseInt(input.dataset.currentStock);
  const receiveQty = parseInt(input.value) || 0;
  const newStock = currentStock + receiveQty;

  const stockDisplay = document.getElementById('new-stock-' + itemId);
  if (stockDisplay) {
    stockDisplay.textContent = number_format(newStock);
    stockDisplay.style.color = receiveQty > 0 ? '#10b981' : 'inherit';
  }
}

function number_format(number) {
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Confirm before submission
document.getElementById('receiveForm').addEventListener('submit', function(e) {
  const inputs = document.querySelectorAll('.qty-input');
  let totalReceiving = 0;

  inputs.forEach(input => {
    totalReceiving += parseInt(input.value) || 0;
  });

  if (totalReceiving === 0) {
    e.preventDefault();
    alert('Please enter at least one quantity to receive.');
    return false;
  }

  if (!confirm(`You are about to receive ${totalReceiving} total units. Stock levels will be updated automatically. Continue?`)) {
    e.preventDefault();
    return false;
  }
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
