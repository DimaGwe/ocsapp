<?php
/**
 * Admin Edit Product Page
 * File: app/Views/admin/products/edit.php
 */

$pageTitle = 'Edit Product';
$currentPage = 'products';

ob_start();
?>

<style>
  /* Page Layout */
  .edit-product-page {
    max-width: 1400px;
    margin: 0 auto;
  }

  /* Page Header */
  .page-header {
    margin-bottom: 32px;
  }

  .page-header-content {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 8px;
  }

  .back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: white;
    border: 2px solid var(--border);
    color: var(--gray-600);
    text-decoration: none;
    transition: all var(--transition-base);
  }

  .back-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
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

  /* Alert */
  .alert {
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .alert-error {
    background: #fee2e2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
  }

  /* Form Grid */
  .form-grid {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
    margin-bottom: 24px;
  }

  /* Card */
  .card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .card-header {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
  }

  /* Form Elements */
  .form-group {
    margin-bottom: 20px;
  }

  .form-label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-label .required {
    color: #ef4444;
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
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-textarea {
    resize: vertical;
  }

  .input-with-icon {
    position: relative;
  }

  .input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-500);
    font-size: 14px;
  }

  .input-with-icon .form-input {
    padding-left: 40px;
  }

  /* Grid Layout */
  .grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }

  /* Image Upload */
  .images-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 20px;
  }

  .image-item {
    position: relative;
    border-radius: var(--radius-md);
    overflow: hidden;
    aspect-ratio: 1;
  }

  .image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    transition: border-color var(--transition-base);
  }

  .image-item.primary img {
    border-color: #facc15;
    border-width: 3px;
  }

  .image-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    opacity: 0;
    transition: opacity var(--transition-base);
  }

  .image-item:hover .image-overlay {
    opacity: 1;
  }

  .image-btn {
    padding: 6px 12px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .image-btn.primary {
    background: #facc15;
    color: #000;
  }

  .image-btn.primary:hover {
    background: #fbbf24;
  }

  .image-btn.primary-active {
    background: #facc15;
    color: #000;
    cursor: default;
  }

  .image-btn.delete {
    background: #ef4444;
    color: white;
  }

  .image-btn.delete:hover {
    background: #dc2626;
  }

  /* Upload Zone */
  .upload-zone {
    border: 2px dashed var(--border);
    border-radius: var(--radius-md);
    padding: 32px;
    text-align: center;
    transition: all var(--transition-base);
    cursor: pointer;
  }

  .upload-zone:hover {
    border-color: var(--primary);
    background: rgba(0, 178, 7, 0.02);
  }

  .upload-zone.dragover {
    border-color: var(--primary);
    background: rgba(0, 178, 7, 0.05);
  }

  .upload-icon {
    font-size: 48px;
    color: var(--gray-400);
    margin-bottom: 16px;
  }

  .upload-text {
    font-size: 14px;
    color: var(--gray-600);
    margin-bottom: 8px;
  }

  .upload-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 16px;
  }

  .upload-btn {
    display: inline-block;
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .upload-btn:hover {
    background: var(--primary-600);
  }

  #image-upload {
    display: none;
  }

  /* Upload Progress */
  .upload-progress {
    background: #dbeafe;
    border: 1px solid #93c5fd;
    border-radius: var(--radius-md);
    padding: 16px;
    margin-top: 16px;
    display: none;
  }

  .upload-progress.active {
    display: block;
  }

  .upload-progress-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .upload-spinner {
    color: #3b82f6;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  .upload-progress-text {
    font-size: 14px;
    color: #1e40af;
  }

  /* Empty State */
  .empty-images {
    text-align: center;
    padding: 48px 24px;
    color: var(--gray-400);
  }

  .empty-icon {
    font-size: 48px;
    margin-bottom: 12px;
  }

  .empty-text {
    font-size: 14px;
  }

  /* Checkbox Group */
  .checkbox-group {
    max-height: 300px;
    overflow-y: auto;
    padding: 4px;
  }

  .checkbox-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
  }

  .checkbox-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
  }

  .checkbox-item label {
    font-size: 14px;
    color: var(--dark);
    cursor: pointer;
    flex: 1;
  }

  /* Form Actions */
  .form-actions {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .btn {
    padding: 12px 24px;
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

  .btn-secondary {
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
  }

  .btn-secondary:hover {
    background: var(--gray-50);
  }

  .btn-primary {
    background: var(--primary);
    color: white;
    border: none;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .form-grid {
      grid-template-columns: 1fr;
    }

    .images-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 768px) {
    .grid-2 {
      grid-template-columns: 1fr;
    }

    .images-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .form-actions {
      flex-direction: column;
      gap: 12px;
    }

    .btn {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<div class="edit-product-page">
  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-content">
      <a href="<?= url('admin/products') ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </a>
      <div>
        <h1 class="page-title">Edit Product</h1>
      </div>
    </div>
    <p class="page-subtitle">Update product information and settings</p>
  </div>

  <!-- Flash Messages -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error">
      <p style="margin: 0; font-weight: 600;"><?= getFlash('error') ?></p>
    </div>
  <?php endif; ?>

  <!-- Product Form -->
  <form method="POST" action="<?= url('admin/products/update') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $product['id'] ?>">

    <div class="form-grid">
      <!-- Main Content -->
      <div>
        <!-- Basic Information -->
        <div class="card">
          <h3 class="card-header">Basic Information</h3>
          
          <div class="form-group">
            <label class="form-label">
              Product Name <span class="required">*</span>
            </label>
            <input 
              type="text" 
              name="name" 
              value="<?= htmlspecialchars($product['name']) ?>"
              required
              class="form-input"
            >
          </div>

          <div class="form-group">
            <label class="form-label">
              Slug (URL) <span class="required">*</span>
            </label>
            <input 
              type="text" 
              name="slug" 
              value="<?= htmlspecialchars($product['slug']) ?>"
              required
              class="form-input"
            >
          </div>

          <div class="form-group">
            <label class="form-label">SKU</label>
            <input 
              type="text" 
              name="sku" 
              value="<?= htmlspecialchars($product['sku'] ?? '') ?>"
              class="form-input"
            >
          </div>

          <div class="form-group">
            <label class="form-label">Short Description</label>
            <textarea 
              name="short_description" 
              rows="2"
              class="form-textarea"
            ><?= htmlspecialchars($product['short_description'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Full Description</label>
            <textarea 
              name="description" 
              rows="6"
              class="form-textarea"
            ><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- Product Images -->
        <div class="card">
          <h3 class="card-header">Product Images</h3>
          
          <!-- Existing Images -->
          <div class="images-grid">
            <?php
            try {
              $db = \Database::getConnection();
              $stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order");
              $stmt->execute([$product['id']]);
              $images = $stmt->fetchAll();
              
              if (empty($images)):
            ?>
              <div class="empty-images" style="grid-column: 1/-1;">
                <div class="empty-icon"><i class="fas fa-image"></i></div>
                <p class="empty-text">No images uploaded yet</p>
              </div>
            <?php 
              else:
                foreach ($images as $img):
            ?>
              <div class="image-item <?= $img['is_primary'] ? 'primary' : '' ?>" data-image-id="<?= $img['id'] ?>">
                <img src="<?= url($img['image_path']) ?>" alt="Product">
                <div class="image-overlay">
                  <?php if (!$img['is_primary']): ?>
                    <button 
                      type="button"
                      onclick="setPrimaryImage(<?= $img['id'] ?>)"
                      class="image-btn primary"
                    >
                      <i class="fas fa-star"></i> Primary
                    </button>
                  <?php else: ?>
                    <span class="image-btn primary-active">
                      <i class="fas fa-star"></i> Primary
                    </span>
                  <?php endif; ?>
                  
                  <button 
                    type="button"
                    onclick="deleteImage(<?= $img['id'] ?>)"
                    class="image-btn delete"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            <?php 
                endforeach;
              endif;
            } catch (\Exception $e) {
              echo '<div style="grid-column: 1/-1; text-align: center; color: #ef4444;">Error loading images</div>';
            }
            ?>
          </div>

          <!-- Upload Zone -->
          <div class="upload-zone" id="uploadZone">
            <div class="upload-icon">
              <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <p class="upload-text">Drag and drop images here or click to browse</p>
            <p class="upload-hint">Maximum 5MB per file. JPG, PNG, GIF, WebP supported</p>
            
            <input 
              type="file" 
              id="image-upload" 
              accept="image/*" 
              multiple
              onchange="uploadImages(this.files)"
            >
            <label for="image-upload" class="upload-btn">
              <i class="fas fa-plus"></i> Choose Images
            </label>
          </div>

          <!-- Upload Progress -->
          <div id="upload-progress" class="upload-progress">
            <div class="upload-progress-content">
              <i class="fas fa-spinner upload-spinner"></i>
              <span class="upload-progress-text">Uploading images...</span>
            </div>
          </div>
        </div>

        <!-- Stock Distribution -->
        <div class="card">
          <h3 class="card-header">Stock Distribution (Model B)</h3>

          <?php
          // Get OCS Store stock
          $db = \Database::getConnection();
          $stmt = $db->prepare("SELECT stock_quantity, allocated_quantity FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1");
          $stmt->execute([$product['id']]);
          $ocsInventory = $stmt->fetch();
          $ocsStock = $ocsInventory ? (int)$ocsInventory['stock_quantity'] : 0;
          $ocsAllocated = $ocsInventory ? (int)$ocsInventory['allocated_quantity'] : 0;

          $totalStock = (int)($product['total_stock'] ?? 0);
          $availableForSellers = (int)($product['available_stock'] ?? 0);
          ?>

          <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; padding: 20px; background: var(--gray-50); border-radius: var(--radius-md);">
            <div style="text-align: center;">
              <div style="font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; margin-bottom: 8px;">OCS Store Stock</div>
              <div style="font-size: 28px; font-weight: 700; color: var(--primary);"><?= number_format($ocsStock) ?></div>
              <div style="font-size: 11px; color: var(--gray-500); margin-top: 4px;">Auto-synced</div>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; margin-bottom: 8px;">Available for Sellers</div>
              <div style="font-size: 28px; font-weight: 700; color: #3b82f6;"><?= number_format($availableForSellers) ?></div>
              <div style="font-size: 11px; color: var(--gray-500); margin-top: 4px;">Manual allocation</div>
            </div>
            <div style="text-align: center;">
              <div style="font-size: 11px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; margin-bottom: 8px;">Total Stock</div>
              <div style="font-size: 28px; font-weight: 700; color: var(--gray-700);"><?= number_format($totalStock) ?></div>
              <div style="font-size: 11px; color: var(--gray-500); margin-top: 4px;">Warehouse</div>
            </div>
          </div>

          <div style="margin-top: 16px; padding: 12px; background: #dbeafe; border-radius: var(--radius-md); font-size: 13px; color: #1e40af;">
            <i class="fas fa-info-circle"></i> <strong>Note:</strong> OCS Store inventory is automatically synced when you update "Base Price" or total stock below. To make stock available for sellers, increase total stock beyond OCS Store needs.
          </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="card">
          <h3 class="card-header">Pricing & Inventory</h3>

          <div class="grid-2">
            <div class="form-group">
              <label class="form-label">
                Base Price <span class="required">*</span>
              </label>
              <div class="input-with-icon">
                <span class="input-icon"><?= currencySymbol() ?></span>
                <input
                  type="number"
                  name="base_price"
                  value="<?= $product['base_price'] ?>"
                  step="0.01"
                  min="0"
                  required
                  class="form-input"
                >
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Cost Price</label>
              <div class="input-with-icon">
                <span class="input-icon"><?= currencySymbol() ?></span>
                <input 
                  type="number" 
                  name="cost_price" 
                  value="<?= $product['cost_price'] ?? 0 ?>"
                  step="0.01"
                  min="0"
                  class="form-input"
                >
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Unit</label>
              <select name="unit" class="form-select">
                <option value="piece" <?= ($product['unit'] ?? '') === 'piece' ? 'selected' : '' ?>>Piece</option>
                <option value="kg" <?= ($product['unit'] ?? '') === 'kg' ? 'selected' : '' ?>>Kilogram</option>
                <option value="liter" <?= ($product['unit'] ?? '') === 'liter' ? 'selected' : '' ?>>Liter</option>
                <option value="meter" <?= ($product['unit'] ?? '') === 'meter' ? 'selected' : '' ?>>Meter</option>
                <option value="pair" <?= ($product['unit'] ?? '') === 'pair' ? 'selected' : '' ?>>Pair</option>
                <option value="set" <?= ($product['unit'] ?? '') === 'set' ? 'selected' : '' ?>>Set</option>
                <option value="box" <?= ($product['unit'] ?? '') === 'box' ? 'selected' : '' ?>>Box</option>
              </select>
            </div>

            <div class="form-group">
              <label class="form-label">Weight (kg)</label>
              <input 
                type="number" 
                name="weight" 
                value="<?= $product['weight'] ?? 0 ?>"
                step="0.01"
                min="0"
                class="form-input"
              >
            </div>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div>
        <!-- Status & Visibility -->
        <div class="card">
          <h3 class="card-header">Status & Visibility</h3>
          
          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
              <option value="draft" <?= ($product['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
              <option value="active" <?= ($product['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="out_of_stock" <?= ($product['status'] ?? '') === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>
          </div>

          <div class="checkbox-item">
            <input 
              type="checkbox" 
              id="is_featured" 
              name="is_featured"
              <?= ($product['is_featured'] ?? false) ? 'checked' : '' ?>
            >
            <label for="is_featured">Mark as Featured Product</label>
          </div>

          <div class="checkbox-item">
            <input 
              type="checkbox" 
              id="show_on_home" 
              name="show_on_home"
              <?= ($product['show_on_home'] ?? false) ? 'checked' : '' ?>
            >
            <label for="show_on_home">Show in "Best Sellers" on Homepage</label>
          </div>
        </div>

        <!-- Brand -->
        <div class="card">
          <h3 class="card-header">Brand</h3>
          <select name="brand_id" class="form-select">
            <option value="">No Brand</option>
            <?php foreach ($brands ?? [] as $brand): ?>
              <option value="<?= $brand['id'] ?>" <?= ($product['brand_id'] ?? '') == $brand['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($brand['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Categories -->
        <div class="card">
          <h3 class="card-header">Categories</h3>
          <div class="checkbox-group">
            <?php foreach ($categories ?? [] as $category): ?>
              <div class="checkbox-item">
                <input 
                  type="checkbox" 
                  id="category_<?= $category['id'] ?>" 
                  name="categories[]"
                  value="<?= $category['id'] ?>"
                  <?= in_array($category['id'], $productCategories ?? []) ? 'checked' : '' ?>
                >
                <label for="category_<?= $category['id'] ?>">
                  <?= htmlspecialchars($category['name']) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tags -->
        <div class="card">
          <h3 class="card-header">Tags</h3>
          <div class="checkbox-group">
            <?php foreach ($tags ?? [] as $tag): ?>
              <div class="checkbox-item">
                <input 
                  type="checkbox" 
                  id="tag_<?= $tag['id'] ?>" 
                  name="tags[]"
                  value="<?= $tag['id'] ?>"
                  <?= in_array($tag['id'], $productTags ?? []) ? 'checked' : '' ?>
                >
                <label for="tag_<?= $tag['id'] ?>">
                  <?= htmlspecialchars($tag['name']) ?>
                </label>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- SEO Settings -->
        <div class="card">
          <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">
              <i class="fas fa-search" style="color: #6366f1; margin-right: 8px;"></i>
              Search Engine Optimization
            </h3>
            <button
              type="button"
              onclick="toggleSeoSection()"
              class="btn-link"
              style="background: none; border: none; color: #6366f1; cursor: pointer; font-size: 14px;"
            >
              <span id="seo-toggle-text">Show SEO Fields</span>
              <i id="seo-toggle-icon" class="fas fa-chevron-down" style="margin-left: 4px;"></i>
            </button>
          </div>

          <p style="font-size: 14px; color: #6b7280; margin-bottom: 16px;">Optimize how this product appears in search engines and social media</p>

          <div id="seo-fields" class="hidden" style="display: none;">
            <!-- Meta Title -->
            <div class="form-group">
              <label for="meta_title">Meta Title</label>
              <input
                type="text"
                id="meta_title"
                name="meta_title"
                value="<?= htmlspecialchars($product['meta_title'] ?? '') ?>"
                maxlength="60"
                class="form-control"
                placeholder="Leave empty to use product name"
                onkeyup="updateCharCount('meta_title', 60)"
              >
              <div style="display: flex; justify-between; margin-top: 4px;">
                <small style="color: #6b7280;">Title that appears in search results</small>
                <small style="color: #6b7280;">
                  <span id="meta_title_count">0</span>/60
                </small>
              </div>
            </div>

            <!-- Meta Description -->
            <div class="form-group">
              <label for="meta_description">Meta Description</label>
              <textarea
                id="meta_description"
                name="meta_description"
                rows="3"
                maxlength="160"
                class="form-control"
                placeholder="Brief description for search results"
                onkeyup="updateCharCount('meta_description', 160)"
              ><?= htmlspecialchars($product['meta_description'] ?? '') ?></textarea>
              <div style="display: flex; justify-between; margin-top: 4px;">
                <small style="color: #6b7280;">Description that appears below title in search results</small>
                <small style="color: #6b7280;">
                  <span id="meta_description_count">0</span>/160
                </small>
              </div>
            </div>

            <!-- Meta Keywords -->
            <div class="form-group">
              <label for="meta_keywords">Meta Keywords</label>
              <input
                type="text"
                id="meta_keywords"
                name="meta_keywords"
                value="<?= htmlspecialchars($product['meta_keywords'] ?? '') ?>"
                class="form-control"
                placeholder="organic, fresh, local, groceries"
              >
              <small style="color: #6b7280;">Comma-separated keywords related to this product</small>
            </div>

            <!-- Robots Meta -->
            <div class="form-group">
              <label for="robots_meta">Robots Meta Tag</label>
              <select id="robots_meta" name="robots_meta" class="form-control">
                <option value="index,follow" <?= ($product['robots_meta'] ?? 'index,follow') === 'index,follow' ? 'selected' : '' ?>>
                  Index, Follow (Default - Allow search engines)
                </option>
                <option value="noindex,follow" <?= ($product['robots_meta'] ?? '') === 'noindex,follow' ? 'selected' : '' ?>>
                  No Index, Follow (Hide from search, but follow links)
                </option>
                <option value="index,nofollow" <?= ($product['robots_meta'] ?? '') === 'index,nofollow' ? 'selected' : '' ?>>
                  Index, No Follow (Show in search, don't follow links)
                </option>
                <option value="noindex,nofollow" <?= ($product['robots_meta'] ?? '') === 'noindex,nofollow' ? 'selected' : '' ?>>
                  No Index, No Follow (Hide completely)
                </option>
              </select>
              <small style="color: #6b7280;">Control how search engines index this product</small>
            </div>

            <!-- Canonical URL -->
            <div class="form-group">
              <label for="canonical_url">Canonical URL</label>
              <input
                type="url"
                id="canonical_url"
                name="canonical_url"
                value="<?= htmlspecialchars($product['canonical_url'] ?? '') ?>"
                class="form-control"
                placeholder="https://ocsapp.ca/product/<?= $product['slug'] ?? 'slug' ?>"
              >
              <small style="color: #6b7280;">Leave empty to use default product URL. Used to prevent duplicate content issues.</small>
            </div>

            <!-- OG Image URL -->
            <div class="form-group">
              <label for="og_image">Social Share Image URL</label>
              <input
                type="url"
                id="og_image"
                name="og_image"
                value="<?= htmlspecialchars($product['og_image'] ?? '') ?>"
                class="form-control"
                placeholder="https://ocsapp.ca/uploads/products/image.jpg"
              >
              <small style="color: #6b7280;">Leave empty to use primary product image. Recommended size: 1200x630px</small>
            </div>

            <!-- SEO Preview -->
            <div style="margin-top: 24px; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
              <p style="font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px;">Google Search Preview</p>
              <div style="background: white; padding: 16px; border-radius: 4px;">
                <div style="font-size: 14px; color: #1e40af; cursor: pointer;" id="preview_title">
                  <?= htmlspecialchars($product['name'] ?? 'Product Name') ?> | OCS Marketplace
                </div>
                <div style="font-size: 12px; color: #059669; margin-top: 4px;" id="preview_url">
                  https://ocsapp.ca/product/<?= $product['slug'] ?? 'product-slug' ?>
                </div>
                <div style="font-size: 14px; color: #4b5563; margin-top: 8px;" id="preview_description">
                  <?= htmlspecialchars(substr($product['description'] ?? 'Product description will appear here...', 0, 160)) ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <a href="<?= url('admin/products') ?>" class="btn btn-secondary">
        Cancel
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Update Product
      </button>
    </div>
  </form>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const csrfTokenName = document.querySelector('meta[name="csrf-token"]').getAttribute('data-name');

function uploadImages(files) {
  if (files.length === 0) return;
  
  const formData = new FormData();
  formData.append('product_id', <?= $product['id'] ?>);
  formData.append(csrfTokenName, csrfToken);
  
  for (let i = 0; i < files.length; i++) {
    formData.append('images[]', files[i]);
  }
  
  const progress = document.getElementById('upload-progress');
  progress.classList.add('active');
  
  fetch('<?= url('admin/products/upload-images') ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    progress.classList.remove('active');
    
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Upload failed: ' + data.message);
    }
  })
  .catch(error => {
    progress.classList.remove('active');
    console.error('Upload error:', error);
    alert('Upload failed');
  });
}

function deleteImage(imageId) {
  if (!confirm('Delete this image?')) return;
  
  const formData = new FormData();
  formData.append('image_id', imageId);
  formData.append(csrfTokenName, csrfToken);
  
  fetch('<?= url('admin/products/delete-image') ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Delete failed: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Delete error:', error);
    alert('Delete failed');
  });
}

function setPrimaryImage(imageId) {
  const formData = new FormData();
  formData.append('image_id', imageId);
  formData.append(csrfTokenName, csrfToken);
  
  fetch('<?= url('admin/products/set-primary-image') ?>', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      location.reload();
    } else {
      alert('Update failed: ' + data.message);
    }
  })
  .catch(error => {
    console.error('Update error:', error);
    alert('Update failed');
  });
}

// Drag and drop
const uploadZone = document.getElementById('uploadZone');

uploadZone.addEventListener('dragover', (e) => {
  e.preventDefault();
  uploadZone.classList.add('dragover');
});

uploadZone.addEventListener('dragleave', () => {
  uploadZone.classList.remove('dragover');
});

uploadZone.addEventListener('drop', (e) => {
  e.preventDefault();
  uploadZone.classList.remove('dragover');
  uploadImages(e.dataTransfer.files);
});

// ===========================
// SEO Functions
// ===========================

// Toggle SEO Section
function toggleSeoSection() {
    const seoFields = document.getElementById('seo-fields');
    const toggleText = document.getElementById('seo-toggle-text');
    const toggleIcon = document.getElementById('seo-toggle-icon');

    if (seoFields.style.display === 'none' || seoFields.classList.contains('hidden')) {
        seoFields.style.display = 'block';
        seoFields.classList.remove('hidden');
        toggleText.textContent = 'Hide SEO Fields';
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
    } else {
        seoFields.style.display = 'none';
        seoFields.classList.add('hidden');
        toggleText.textContent = 'Show SEO Fields';
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
    }
}

// Update Character Count
function updateCharCount(fieldId, maxLength) {
    const field = document.getElementById(fieldId);
    const counter = document.getElementById(fieldId + '_count');
    if (!field || !counter) return;

    const currentLength = field.value.length;
    counter.textContent = currentLength;

    // Change color based on length
    const parent = counter.parentElement;
    if (currentLength > maxLength * 0.9) {
        parent.style.color = '#dc2626';
    } else if (currentLength > maxLength * 0.7) {
        parent.style.color = '#ca8a04';
    } else {
        parent.style.color = '#6b7280';
    }

    // Update preview
    updateSeoPreview();
}

// Update SEO Preview
function updateSeoPreview() {
    const productName = document.getElementById('name')?.value || 'Product Name';
    const metaTitle = document.getElementById('meta_title')?.value || productName;
    const metaDescription = document.getElementById('meta_description')?.value || 'Product description will appear here...';
    const slug = document.getElementById('slug')?.value || 'product-slug';

    // Update preview elements
    const previewTitle = document.getElementById('preview_title');
    const previewUrl = document.getElementById('preview_url');
    const previewDescription = document.getElementById('preview_description');

    if (previewTitle) previewTitle.textContent = metaTitle + ' | OCS Marketplace';
    if (previewUrl) previewUrl.textContent = 'https://ocsapp.ca/product/' + slug;
    if (previewDescription) previewDescription.textContent = metaDescription;
}

// Update preview when fields change
if (document.getElementById('name')) {
    document.getElementById('name').addEventListener('input', updateSeoPreview);
}
if (document.getElementById('slug')) {
    document.getElementById('slug').addEventListener('input', updateSeoPreview);
}
if (document.getElementById('meta_title')) {
    document.getElementById('meta_title').addEventListener('input', updateSeoPreview);
}
if (document.getElementById('meta_description')) {
    document.getElementById('meta_description').addEventListener('input', updateSeoPreview);
}

// Initialize character counters on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCharCount('meta_title', 60);
    updateCharCount('meta_description', 160);
    updateSeoPreview();
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>