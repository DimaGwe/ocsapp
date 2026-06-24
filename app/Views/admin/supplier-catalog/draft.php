<?php
$pageTitle = 'Draft Purchase List';
$currentPage = 'supplier-catalog';
ob_start();
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
  }

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-success {
    background: #10b981;
    color: white;
  }

  .btn-danger {
    background: #ef4444;
    color: white;
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .supplier-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .supplier-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--border);
  }

  .supplier-name {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
  }

  .supplier-total {
    font-size: 18px;
    font-weight: 700;
    color: var(--primary);
  }

  .items-table {
    width: 100%;
    border-collapse: collapse;
  }

  .items-table thead {
    background: var(--gray-50);
  }

  .items-table th {
    padding: 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    border-bottom: 2px solid var(--border);
  }

  .items-table td {
    padding: 16px 12px;
    border-bottom: 1px solid var(--border);
  }

  .qty-input-inline {
    width: 80px;
    padding: 6px 10px;
    border: 2px solid var(--border);
    border-radius: 6px;
    text-align: center;
    font-weight: 600;
  }

  .btn-icon {
    background: none;
    border: none;
    color: var(--gray-600);
    cursor: pointer;
    padding: 6px;
    transition: color 0.2s;
  }

  .btn-icon:hover {
    color: var(--primary);
  }

  .btn-icon.delete:hover {
    color: #ef4444;
  }

  .empty-state {
    text-align: center;
    padding: 80px 20px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 80px;
    margin-bottom: 20px;
    display: block;
  }

  .draft-actions {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .actions-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
  }

  .action-card {
    text-align: center;
    padding: 20px;
    background: var(--gray-50);
    border-radius: 8px;
  }

  .action-card i {
    font-size: 32px;
    margin-bottom: 12px;
    display: block;
  }

  .action-card h3 {
    font-size: 16px;
    margin-bottom: 8px;
  }

  .action-card p {
    font-size: 14px;
    color: var(--gray-600);
    margin-bottom: 16px;
  }
</style>

<div class="page-header">
  <div>
    <h1 class="page-title">Draft Purchase List</h1>
    <p style="color: var(--gray-600); margin-top: 8px;">Review and organize items before creating purchase orders</p>
  </div>
  <div class="header-actions">
    <a href="<?= url('admin/supplier-catalog') ?>" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Back to Catalog
    </a>
  </div>
</div>

<?php if (empty($itemsBySupplier)): ?>
  <div class="empty-state">
    <i class="fas fa-clipboard-list"></i>
    <h2>Your draft list is empty</h2>
    <p>Browse the supplier catalog and add products to start planning your purchase orders</p>
    <a href="<?= url('admin/supplier-catalog') ?>" class="btn btn-primary" style="margin-top: 20px;">
      <i class="fas fa-search"></i> Browse Catalog
    </a>
  </div>
<?php else: ?>
  <!-- Draft Actions -->
  <div class="draft-actions">
    <div class="actions-grid">
      <div class="action-card">
        <i class="fas fa-shopping-cart" style="color: var(--primary);"></i>
        <h3>Total Items</h3>
        <p style="font-size: 24px; font-weight: 700; color: var(--dark); margin: 0;">
          <?= array_sum(array_map(fn($s) => count($s['items']), $itemsBySupplier)) ?>
        </p>
      </div>

      <div class="action-card">
        <i class="fas fa-building" style="color: #10b981;"></i>
        <h3>Suppliers</h3>
        <p style="font-size: 24px; font-weight: 700; color: var(--dark); margin: 0;">
          <?= count($itemsBySupplier) ?>
        </p>
      </div>

      <div class="action-card">
        <i class="fas fa-dollar-sign" style="color: #f59e0b;"></i>
        <h3>Estimated Total</h3>
        <p style="font-size: 20px; font-weight: 700; color: var(--dark); margin: 0;">
          <?php
          $grandTotal = 0;
          foreach ($itemsBySupplier as $supplier) {
            foreach ($supplier['items'] as $item) {
              $grandTotal += $item['quantity'] * $item['unit_price'];
            }
          }
          echo currencySymbol() . number_format($grandTotal, 2);
          ?>
        </p>
      </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border);">
      <button onclick="clearDraft()" class="btn btn-danger">
        <i class="fas fa-trash"></i> Clear All
      </button>
      <form method="POST" action="<?= url('admin/supplier-catalog/create-po-from-draft') ?>">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-success">
          <i class="fas fa-file-invoice"></i> Create Purchase Orders
        </button>
      </form>
    </div>
  </div>

  <!-- Items by Supplier -->
  <?php foreach ($itemsBySupplier as $supplierId => $supplierData): ?>
    <div class="supplier-section">
      <div class="supplier-header">
        <div class="supplier-name">
          <i class="fas fa-building"></i>
          <?= htmlspecialchars($supplierData['supplier_name']) ?>
        </div>
        <div class="supplier-total">
          <?php
          $supplierTotal = 0;
          foreach ($supplierData['items'] as $item) {
            $supplierTotal += $item['quantity'] * $item['unit_price'];
          }
          echo currencySymbol() . number_format($supplierTotal, 2);
          ?>
        </div>
      </div>

      <table class="items-table">
        <thead>
          <tr>
            <th>Product</th>
            <th style="text-align: center;">Unit Price</th>
            <th style="text-align: center;">Quantity</th>
            <th style="text-align: right;">Total</th>
            <th style="text-align: center;"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($supplierData['items'] as $item): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($item['product_name']) ?></strong>
                <?php if ($item['sku']): ?>
                  <div style="font-size: 13px; color: var(--gray-600);">SKU: <?= htmlspecialchars($item['sku']) ?></div>
                <?php endif; ?>
                <div style="font-size: 12px; color: var(--gray-500); margin-top: 4px;">
                  Min Order: <?= $item['minimum_order_quantity'] ?> <?= htmlspecialchars($item['unit']) ?>
                </div>
              </td>
              <td style="text-align: center;">
                <strong><?= currencySymbol() ?><?= number_format($item['unit_price'], 2) ?></strong>
                <div style="font-size: 12px; color: var(--gray-600);">/ <?= htmlspecialchars($item['unit']) ?></div>
              </td>
              <td style="text-align: center;">
                <input
                  type="number"
                  class="qty-input-inline"
                  value="<?= $item['quantity'] ?>"
                  min="<?= $item['minimum_order_quantity'] ?>"
                  onchange="updateQuantity(<?= $item['supplier_product_id'] ?>, this.value)"
                >
              </td>
              <td style="text-align: right;">
                <strong style="font-size: 16px; color: var(--primary);">
                  <?= currencySymbol() ?><?= number_format($item['quantity'] * $item['unit_price'], 2) ?>
                </strong>
              </td>
              <td style="text-align: center;">
                <button class="btn-icon delete" onclick="removeItem(<?= $item['supplier_product_id'] ?>)">
                  <i class="fas fa-times"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

function updateQuantity(productId, quantity) {
  fetch('<?= url('admin/supplier-catalog/update-draft-item') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, product_id: productId, quantity: quantity })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error updating quantity'));
}

function removeItem(productId) {
  if (!confirm('Remove this item from draft?')) return;

  fetch('<?= url('admin/supplier-catalog/remove-draft-item') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, product_id: productId })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error removing item'));
}

function clearDraft() {
  if (!confirm('Clear all items from draft? This cannot be undone.')) return;

  fetch('<?= url('admin/supplier-catalog/clear-draft') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error clearing draft'));
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
