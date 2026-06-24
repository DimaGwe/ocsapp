<?php
$pageTitle = 'Edit Sale';
$currentPage = 'sales';
ob_start();

// Calculate current discount
$savings = $product['base_price'] - ($product['sale_price'] ?? $product['base_price']);
$percentage = $product['base_price'] > 0 ? round(($savings / $product['base_price']) * 100, 2) : 0;
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

  .btn-secondary {
    background: #6b7280;
    color: white;
  }

  .btn-primary {
    background: linear-gradient(135deg, #00b207 0%, #008505 100%);
    color: white;
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
  }

  .product-overview {
    display: flex;
    gap: 20px;
    padding: 20px;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 24px;
  }

  .product-image {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 8px;
  }

  .product-details h3 {
    font-size: 20px;
    margin: 0 0 8px 0;
    color: #1f2937;
  }

  .product-details p {
    margin: 4px 0;
    color: #6b7280;
    font-size: 14px;
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
  }

  .form-input {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-input:focus {
    outline: none;
    border-color: #00b207;
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
  }

  .preview-section {
    background: #f9fafb;
    border-radius: 8px;
    padding: 16px;
    margin-top: 20px;
  }

  .preview-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    font-size: 14px;
  }

  .preview-label {
    color: #6b7280;
  }

  .preview-value {
    font-weight: 600;
    color: #1f2937;
  }

  .discount-badge {
    background: #00b207;
    color: white;
    padding: 4px 12px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 700;
  }
</style>

<div class="page-header">
  <h1 class="page-title">Edit Sale</h1>
  <a href="<?= url('admin/sales') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i>
    Back to Sales
  </a>
</div>

<!-- Product Overview -->
<div class="card">
  <h3 class="card-title">Product Information</h3>
  <div class="product-overview">
    <img src="<?= asset($product['image_path'] ?? 'images/placeholder.png') ?>" alt="" class="product-image">
    <div class="product-details">
      <h3><?= htmlspecialchars($product['name']) ?></h3>
      <p><strong>SKU:</strong> <?= htmlspecialchars($product['sku']) ?></p>
      <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></p>
      <p><strong>Brand:</strong> <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></p>
      <p><strong>Base Price:</strong> $<?= number_format($product['base_price'], 2) ?></p>
      <p><strong>Current Discount:</strong> <span class="discount-badge"><?= $percentage ?>% OFF</span></p>
    </div>
  </div>
</div>

<!-- Edit Form -->
<form method="POST" action="<?= url('admin/sales/update') ?>">
  <?= csrfField() ?>
  <input type="hidden" name="id" value="<?= $product['id'] ?>">

  <div class="card">
    <h3 class="card-title">Sale Details</h3>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Sale Price ($)</label>
        <input type="number" name="sale_price" id="salePrice" class="form-input"
               value="<?= number_format($product['sale_price'], 2, '.', '') ?>"
               step="0.01" min="0" max="<?= $product['base_price'] ?>"
               required onchange="updatePreview()">
        <small style="color: #6b7280;">Base price: $<?= number_format($product['base_price'], 2) ?></small>
      </div>

      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="is_on_sale" id="isOnSale" class="form-input">
          <option value="1" <?= $product['is_on_sale'] ? 'selected' : '' ?>>Active</option>
          <option value="0" <?= !$product['is_on_sale'] ? 'selected' : '' ?>>Inactive</option>
        </select>
      </div>
    </div>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Start Date & Time (Optional)</label>
        <input type="datetime-local" name="start_date" id="startDate" class="form-input"
               value="<?= $product['sale_start_date'] ? date('Y-m-d\TH:i', strtotime($product['sale_start_date'])) : '' ?>">
      </div>

      <div class="form-group">
        <label class="form-label">End Date & Time (Optional)</label>
        <input type="datetime-local" name="end_date" id="endDate" class="form-input"
               value="<?= $product['sale_end_date'] ? date('Y-m-d\TH:i', strtotime($product['sale_end_date'])) : '' ?>">
      </div>
    </div>
  </div>

  <!-- Preview -->
  <div class="card">
    <h3 class="card-title">Preview</h3>
    <div class="preview-section">
      <div class="preview-item">
        <span class="preview-label">Base Price:</span>
        <span class="preview-value">$<?= number_format($product['base_price'], 2) ?></span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Sale Price:</span>
        <span class="preview-value" id="previewSalePrice">$<?= number_format($product['sale_price'], 2) ?></span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Customer Saves:</span>
        <span class="preview-value" id="previewSavings">$<?= number_format($savings, 2) ?></span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Discount Percentage:</span>
        <span class="preview-value" id="previewPercentage"><?= $percentage ?>%</span>
      </div>
    </div>
  </div>

  <!-- Submit -->
  <div style="display: flex; gap: 12px; justify-content: flex-end;">
    <a href="<?= url('admin/sales') ?>" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-save"></i>
      Update Sale
    </button>
  </div>
</form>

<script>
  const basePrice = <?= $product['base_price'] ?>;

  function updatePreview() {
    const salePrice = parseFloat(document.getElementById('salePrice').value) || 0;
    const savings = basePrice - salePrice;
    const percentage = basePrice > 0 ? ((savings / basePrice) * 100) : 0;

    document.getElementById('previewSalePrice').textContent = '$' + salePrice.toFixed(2);
    document.getElementById('previewSavings').textContent = '$' + savings.toFixed(2);
    document.getElementById('previewPercentage').textContent = percentage.toFixed(2) + '%';
  }
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
