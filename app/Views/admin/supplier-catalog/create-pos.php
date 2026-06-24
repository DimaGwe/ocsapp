<?php
$pageTitle = 'Create Purchase Orders from Draft';
$currentPage = 'supplier-catalog';
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

  .alert-info {
    background: #dbeafe;
    color: #1e40af;
    border-left: 4px solid #3b82f6;
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
  }

  .po-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
  }

  .po-header {
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

  .po-total {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
  }

  .items-list {
    margin-bottom: 20px;
  }

  .item-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
  }

  .item-info {
    flex: 1;
  }

  .item-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
  }

  .item-details {
    font-size: 13px;
    color: var(--gray-600);
  }

  .item-price {
    text-align: right;
    min-width: 150px;
  }

  .item-unit-price {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 4px;
  }

  .item-total {
    font-weight: 700;
    font-size: 16px;
    color: var(--primary);
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

  .btn-success {
    background: #10b981;
    color: white;
    justify-content: center;
  }

  .btn-success:hover {
    background: #059669;
  }

  .create-all-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
    text-align: center;
  }

  .create-all-section .btn-success {
    font-size: 16px;
    padding: 16px 40px;
  }

  .grand-total {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 8px;
  }

  .po-summary {
    color: var(--gray-600);
    font-size: 14px;
    margin-bottom: 20px;
  }
</style>

<div class="page-header">
  <h1 class="page-title">Create Purchase Orders</h1>
  <p style="color: var(--gray-600); margin-top: 8px;">
    Review the details below, then create all purchase orders at once
  </p>
</div>

<div class="alert-info">
  <i class="fas fa-info-circle" style="font-size: 20px;"></i>
  <div>
    <strong>Ready to Create POs:</strong> Your draft items have been grouped by supplier.
    Review the details below, then click the button at the bottom to create all purchase orders at once.
  </div>
</div>

<?php
$grandTotal = 0;
$supplierCount = count($itemsBySupplier);
?>

<?php foreach ($itemsBySupplier as $supplierId => $items): ?>
  <?php
  $supplierName = $items[0]['supplier_name'];
  $subtotal = array_reduce($items, fn($sum, $item) => $sum + ($item['quantity'] * $item['unit_price']), 0);
  $grandTotal += $subtotal;
  ?>

  <div class="po-card">
    <div class="po-header">
      <div class="supplier-name">
        <i class="fas fa-building"></i>
        <?= htmlspecialchars($supplierName) ?>
      </div>
      <div class="po-total">
        <?= currencySymbol() ?><?= number_format($subtotal, 2) ?>
      </div>
    </div>

    <div class="items-list">
      <?php foreach ($items as $item): ?>
        <div class="item-row">
          <div class="item-info">
            <div class="item-name"><?= htmlspecialchars($item['product_name']) ?></div>
            <div class="item-details">
              <?php if ($item['sku']): ?>
                SKU: <?= htmlspecialchars($item['sku']) ?> &bull;
              <?php endif; ?>
              <?= $item['quantity'] ?> <?= htmlspecialchars($item['unit']) ?>
            </div>
          </div>
          <div class="item-price">
            <div class="item-unit-price">
              <?= currencySymbol() ?><?= number_format($item['unit_price'], 2) ?> / <?= htmlspecialchars($item['unit']) ?>
            </div>
            <div class="item-total">
              <?= currencySymbol() ?><?= number_format($item['quantity'] * $item['unit_price'], 2) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endforeach; ?>

<div class="create-all-section">
  <div class="grand-total">
    Grand Total: <?= currencySymbol() ?><?= number_format($grandTotal, 2) ?>
  </div>
  <div class="po-summary">
    <?= $supplierCount ?> purchase order<?= $supplierCount > 1 ? 's' : '' ?> will be created as drafts
  </div>
  <form method="POST" action="<?= url('admin/supplier-catalog/create-all-pos') ?>">
    <?= csrfField() ?>
    <button type="submit" class="btn btn-success">
      <i class="fas fa-file-invoice"></i>
      Create All <?= $supplierCount ?> Purchase Order<?= $supplierCount > 1 ? 's' : '' ?>
    </button>
  </form>
</div>

<div style="margin-top: 16px; text-align: center;">
  <a href="<?= url('admin/supplier-catalog') ?>" style="color: var(--gray-600); text-decoration: underline;">
    <i class="fas fa-arrow-left"></i> Back to Catalog
  </a>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
