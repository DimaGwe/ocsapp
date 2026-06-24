<?php
$pageTitle = 'Create New Sale';
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

  .form-input, .form-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
  }

  .form-input:focus, .form-select:focus {
    outline: none;
    border-color: #00b207;
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
  }

  .checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  .search-filter {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
  }

  .search-filter input {
    flex: 1;
  }

  .product-selection {
    max-height: 500px;
    overflow-y: auto;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
  }

  .product-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-bottom: 1px solid #f3f4f6;
    transition: background 0.2s;
  }

  .product-item:hover {
    background: #f9fafb;
  }

  .product-item:last-child {
    border-bottom: none;
  }

  .product-checkbox {
    flex-shrink: 0;
  }

  .product-info {
    flex: 1;
  }

  .product-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
  }

  .product-meta {
    font-size: 12px;
    color: #6b7280;
  }

  .product-price {
    font-weight: 700;
    color: #00b207;
  }

  .selection-info {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .selection-count {
    font-weight: 600;
    color: #1e40af;
  }

  .bulk-actions {
    display: flex;
    gap: 8px;
  }

  .btn-small {
    padding: 6px 12px;
    font-size: 13px;
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
</style>

<div class="page-header">
  <h1 class="page-title">Create New Sale</h1>
  <a href="<?= url('admin/sales') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i>
    Back to Sales
  </a>
</div>

<form method="POST" action="<?= url('admin/sales/store') ?>" id="saleForm">
  <?= csrfField() ?>

  <!-- Sale Configuration -->
  <div class="card">
    <h3 class="card-title">Sale Configuration</h3>

    <div class="form-grid">
      <div class="form-group">
        <label class="form-label">Discount Type</label>
        <select name="discount_type" id="discountType" class="form-select" onchange="updatePreview()">
          <option value="percentage">Percentage (%)</option>
          <option value="fixed">Fixed Amount ($)</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" id="discountLabel">Discount Percentage (%)</label>
        <input type="number" name="discount_value" id="discountValue" class="form-input"
               placeholder="20" step="0.01" min="0" max="100" required onchange="updatePreview()">
      </div>
    </div>

    <div class="checkbox-group">
      <input type="checkbox" name="start_immediately" id="startImmediately" value="1"
             onchange="toggleSchedule()" checked>
      <label for="startImmediately">Start sale immediately</label>
    </div>

    <div id="scheduleSection" style="display: none;">
      <div class="form-grid">
        <div class="form-group">
          <label class="form-label">Start Date & Time</label>
          <input type="datetime-local" name="start_date" id="startDate" class="form-input">
        </div>

        <div class="form-group">
          <label class="form-label">End Date & Time (Optional)</label>
          <input type="datetime-local" name="end_date" id="endDate" class="form-input">
        </div>
      </div>
    </div>
  </div>

  <!-- Product Selection -->
  <div class="card">
    <h3 class="card-title">Select Products</h3>

    <div class="selection-info">
      <span class="selection-count"><span id="selectedCount">0</span> products selected</span>
      <div class="bulk-actions">
        <button type="button" class="btn btn-small btn-secondary" onclick="selectAll()">Select All</button>
        <button type="button" class="btn btn-small btn-secondary" onclick="deselectAll()">Deselect All</button>
      </div>
    </div>

    <div class="search-filter">
      <input type="text" id="searchProducts" class="form-input"
             placeholder="Search products..." onkeyup="filterProducts()">
      <select id="filterCategory" class="form-select" onchange="filterProducts()">
        <option value="">All Categories</option>
        <?php foreach ($categories as $category): ?>
          <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="product-selection" id="productList">
      <?php foreach ($products as $product): ?>
        <div class="product-item" data-category="<?= $product['category_name'] ?? '' ?>"
             data-name="<?= strtolower($product['name']) ?>">
          <div class="product-checkbox">
            <input type="checkbox" name="product_ids[]" value="<?= $product['id'] ?>"
                   onchange="updateSelection()">
          </div>
          <div class="product-info">
            <div class="product-name"><?= htmlspecialchars($product['name']) ?></div>
            <div class="product-meta">
              SKU: <?= htmlspecialchars($product['sku']) ?> |
              Category: <?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?>
            </div>
          </div>
          <div class="product-price">$<?= number_format($product['base_price'], 2) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Preview -->
  <div class="card">
    <h3 class="card-title">Sale Preview</h3>
    <div class="preview-section">
      <div class="preview-item">
        <span class="preview-label">Discount Type:</span>
        <span class="preview-value" id="previewType">Percentage</span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Discount Value:</span>
        <span class="preview-value" id="previewValue">0%</span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Example: $100 product becomes:</span>
        <span class="preview-value" id="previewExample">$100.00</span>
      </div>
      <div class="preview-item">
        <span class="preview-label">Status:</span>
        <span class="preview-value" id="previewStatus">Will start immediately</span>
      </div>
    </div>
  </div>

  <!-- Submit -->
  <div style="display: flex; gap: 12px; justify-content: flex-end;">
    <a href="<?= url('admin/sales') ?>" class="btn btn-secondary">Cancel</a>
    <button type="submit" class="btn btn-primary">
      <i class="fas fa-tag"></i>
      Create Sale
    </button>
  </div>
</form>

<script>
  function updateSelection() {
    const checkboxes = document.querySelectorAll('input[name="product_ids[]"]:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
  }

  function selectAll() {
    const checkboxes = document.querySelectorAll('input[name="product_ids[]"]:visible');
    checkboxes.forEach(cb => {
      if (cb.closest('.product-item').style.display !== 'none') {
        cb.checked = true;
      }
    });
    updateSelection();
  }

  function deselectAll() {
    document.querySelectorAll('input[name="product_ids[]"]').forEach(cb => cb.checked = false);
    updateSelection();
  }

  function filterProducts() {
    const search = document.getElementById('searchProducts').value.toLowerCase();
    const category = document.getElementById('filterCategory').value;
    const items = document.querySelectorAll('.product-item');

    items.forEach(item => {
      const name = item.dataset.name;
      const itemCategory = item.dataset.category;

      const matchesSearch = name.includes(search);
      const matchesCategory = !category || itemCategory === document.getElementById('filterCategory').selectedOptions[0].text;

      item.style.display = (matchesSearch && matchesCategory) ? 'flex' : 'none';
    });

    updateSelection();
  }

  function toggleSchedule() {
    const immediate = document.getElementById('startImmediately').checked;
    const scheduleSection = document.getElementById('scheduleSection');
    scheduleSection.style.display = immediate ? 'none' : 'block';

    if (!immediate) {
      document.getElementById('startDate').required = true;
    } else {
      document.getElementById('startDate').required = false;
    }

    updatePreview();
  }

  function updatePreview() {
    const type = document.getElementById('discountType').value;
    const value = parseFloat(document.getElementById('discountValue').value) || 0;
    const immediate = document.getElementById('startImmediately').checked;

    // Update labels
    document.getElementById('discountLabel').textContent = type === 'percentage'
      ? 'Discount Percentage (%)'
      : 'Fixed Discount Amount ($)';

    // Update preview
    document.getElementById('previewType').textContent = type === 'percentage' ? 'Percentage' : 'Fixed Amount';
    document.getElementById('previewValue').textContent = type === 'percentage'
      ? value + '%'
      : '$' + value.toFixed(2);

    // Calculate example
    const basePrice = 100;
    let salePrice;
    if (type === 'percentage') {
      salePrice = basePrice * (1 - (value / 100));
    } else {
      salePrice = basePrice - value;
    }
    salePrice = Math.max(0, salePrice);

    document.getElementById('previewExample').textContent = '$' + salePrice.toFixed(2);
    document.getElementById('previewStatus').textContent = immediate
      ? 'Will start immediately'
      : 'Scheduled to start later';
  }

  // Initialize
  updatePreview();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
