<?php
/**
 * Promo Banner - Create View
 * File: app/Views/admin/promo-banners/create.php
 */

$pageTitle = $pageTitle ?? 'Create Promo Banner';
$currentPage = $currentPage ?? 'cms';
$ocsProducts = $ocsProducts ?? [];

ob_start();
?>

<style>
  .edit-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .edit-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .edit-header .breadcrumb {
    color: var(--text-muted);
    font-size: 14px;
    margin-bottom: 8px;
  }

  .edit-header .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .edit-header .breadcrumb a:hover {
    text-decoration: underline;
  }

  .btn-back {
    padding: 10px 20px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-back:hover {
    border-color: var(--gray-400);
    background: var(--gray-50);
  }

  .edit-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    max-width: 900px;
  }

  .form-section {
    margin-bottom: 32px;
  }

  .form-section:last-child {
    margin-bottom: 0;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-label .required {
    color: var(--danger);
  }

  .form-description {
    font-size: 13px;
    color: var(--gray-500);
    margin-bottom: 12px;
  }

  .form-input,
  .form-textarea,
  .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-textarea:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 100px;
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 32px;
  }

  .toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: 0.4s;
    border-radius: 32px;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 24px;
    width: 24px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
  }

  .toggle-switch input:checked + .toggle-slider {
    background-color: var(--primary);
  }

  .toggle-switch input:checked + .toggle-slider:before {
    transform: translateX(28px);
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
    padding-top: 32px;
    border-top: 2px solid var(--border);
  }

  .btn-save {
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

  .btn-save:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-cancel {
    padding: 12px 24px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-cancel:hover {
    border-color: var(--gray-400);
    background: var(--gray-50);
  }

  /* Product Multi-Select */
  .product-select-container {
    border: 2px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 16px;
    background: var(--gray-50);
    max-height: 400px;
    overflow-y: auto;
  }

  .product-select-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: white;
    border-radius: var(--radius-md);
    margin-bottom: 8px;
    cursor: pointer;
    transition: all var(--transition-base);
    border: 2px solid transparent;
  }

  .product-select-item:hover {
    border-color: var(--primary);
    background: #eff6ff;
  }

  .product-select-item.selected {
    border-color: var(--primary);
    background: #eff6ff;
  }

  .product-select-checkbox {
    width: 20px;
    height: 20px;
    cursor: pointer;
  }

  .product-select-image {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    object-fit: cover;
    background: var(--gray-200);
  }

  .product-select-image-placeholder {
    width: 60px;
    height: 60px;
    border-radius: var(--radius-md);
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
  }

  .product-select-info {
    flex: 1;
  }

  .product-select-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
  }

  .product-select-price {
    font-size: 14px;
    color: var(--primary);
    font-weight: 600;
  }

  .product-select-sale {
    font-size: 12px;
    color: var(--text-muted);
    text-decoration: line-through;
    margin-left: 8px;
  }

  .selected-count {
    display: inline-block;
    padding: 4px 12px;
    background: var(--primary);
    color: white;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    margin-left: 8px;
  }
</style>

<!-- Page Header -->
<div class="edit-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= url('/admin/cms') ?>">CMS</a> /
      <a href="<?= url('/admin/promo-banners') ?>">Promo Banners</a> /
      Create
    </div>
    <h1>+ Create Promo Banner</h1>
  </div>
  <a href="<?= url('/admin/promo-banners') ?>" class="btn-back">
    ← Back to Promo Banners
  </a>
</div>

<!-- Create Form -->
<div class="edit-card">
  <form method="POST" action="<?= url('/admin/promo-banners/create') ?>">
    <?= csrfField() ?>

    <!-- Title -->
    <div class="form-section">
      <label class="form-label" for="title">
        Banner Title <span class="required">*</span>
      </label>
      <div class="form-description">Main heading text (e.g., "Super Savings", "Flash Sale")</div>
      <input type="text"
             id="title"
             name="title"
             class="form-input"
             placeholder="Super Savings"
             required>
    </div>

    <!-- Subtitle -->
    <div class="form-section">
      <label class="form-label" for="subtitle">Subtitle</label>
      <div class="form-description">Additional description text (optional)</div>
      <textarea id="subtitle"
                name="subtitle"
                class="form-textarea"
                rows="2"
                placeholder="On Select Products"></textarea>
    </div>

    <!-- Discount Percentage -->
    <div class="form-section">
      <label class="form-label" for="discount_percentage">
        Discount Percentage <span class="required">*</span>
      </label>
      <div class="form-description">Displayed discount amount (e.g., 20, 30, 50)</div>
      <input type="number"
             id="discount_percentage"
             name="discount_percentage"
             class="form-input"
             value="20"
             min="0"
             max="100"
             required>
    </div>

    <!-- Product Selection -->
    <div class="form-section">
      <label class="form-label">
        Select Products <span id="selectedCount" class="selected-count">0 selected</span>
      </label>
      <div class="form-description">Choose products from OCS store to display in the banner carousel</div>

      <div class="product-select-container" id="productSelectContainer">
        <?php if (!empty($ocsProducts)): ?>
          <?php foreach ($ocsProducts as $product): ?>
            <?php
              $price = $product['is_on_sale'] ? $product['sale_price'] : $product['base_price'];
            ?>
            <div class="product-select-item"
                 data-product-id="<?= $product['id'] ?>">
              <input type="checkbox"
                     name="selected_products[]"
                     value="<?= $product['id'] ?>"
                     class="product-select-checkbox">

              <?php if (!empty($product['image'])): ?>
                <img src="<?= url($product['image']) ?>"
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="product-select-image">
              <?php else: ?>
                <div class="product-select-image-placeholder">📦</div>
              <?php endif; ?>

              <div class="product-select-info">
                <div class="product-select-name"><?= htmlspecialchars($product['name']) ?></div>
                <div class="product-select-price">
                  <?= currency($price) ?>
                  <?php if ($product['is_on_sale'] && $product['base_price'] > $price): ?>
                    <span class="product-select-sale"><?= currency($product['base_price']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p style="text-align: center; color: var(--text-muted); padding: 20px;">
            No OCS store products available
          </p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Button Text -->
    <div class="form-section">
      <label class="form-label" for="button_text">Button Text</label>
      <div class="form-description">Call-to-action button text (e.g., "Shop Now", "View Deals")</div>
      <input type="text"
             id="button_text"
             name="button_text"
             class="form-input"
             value="Shop Now"
             placeholder="Shop Now">
    </div>

    <!-- Button URL -->
    <div class="form-section">
      <label class="form-label" for="button_url">Button URL</label>
      <div class="form-description">Where the button links to (e.g., /deals, /categories)</div>
      <input type="text"
             id="button_url"
             name="button_url"
             class="form-input"
             value="/deals"
             placeholder="/deals">
    </div>

    <!-- Sort Order -->
    <div class="form-section">
      <label class="form-label" for="sort_order">Display Order</label>
      <div class="form-description">Lower numbers appear first (e.g., 1, 2, 3...)</div>
      <input type="number"
             id="sort_order"
             name="sort_order"
             class="form-input"
             value="0"
             min="0">
    </div>

    <!-- Status -->
    <div class="form-section">
      <label class="form-label">Status</label>
      <div class="form-description">Toggle to show or hide this banner on the website</div>
      <div style="display: flex; align-items: center; gap: 16px;">
        <label class="toggle-switch">
          <input type="checkbox"
                 id="statusCheckbox"
                 checked>
          <span class="toggle-slider"></span>
        </label>
        <span id="statusText" style="color: var(--text); font-weight: 500;">
          Active (Visible on website)
        </span>
      </div>
      <input type="hidden" name="status" id="statusInput" value="active">
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <button type="submit" class="btn-save">
        + Create Promo Banner
      </button>
      <a href="<?= url('/admin/promo-banners') ?>" class="btn-cancel">
        Cancel
      </a>
    </div>
  </form>
</div>

<script>
  // Product selection handling
  const productItems = document.querySelectorAll('.product-select-item');
  const selectedCountEl = document.getElementById('selectedCount');

  function updateSelectedCount() {
    const checkedBoxes = document.querySelectorAll('.product-select-checkbox:checked');
    selectedCountEl.textContent = `${checkedBoxes.length} selected`;
  }

  productItems.forEach(item => {
    const checkbox = item.querySelector('.product-select-checkbox');

    item.addEventListener('click', function(e) {
      if (e.target !== checkbox) {
        checkbox.checked = !checkbox.checked;
        item.classList.toggle('selected', checkbox.checked);
        updateSelectedCount();
      }
    });

    checkbox.addEventListener('change', function() {
      item.classList.toggle('selected', this.checked);
      updateSelectedCount();
    });
  });

  // Initialize count
  updateSelectedCount();

  // Status toggle
  const statusCheckbox = document.getElementById('statusCheckbox');
  const statusText = document.getElementById('statusText');
  const statusInput = document.getElementById('statusInput');

  statusCheckbox.addEventListener('change', function() {
    if (this.checked) {
      statusText.textContent = 'Active (Visible on website)';
      statusInput.value = 'active';
    } else {
      statusText.textContent = 'Inactive (Hidden)';
      statusInput.value = 'inactive';
    }
  });
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
