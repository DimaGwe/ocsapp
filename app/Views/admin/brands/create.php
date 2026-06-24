<?php
/**
 * Admin Add Brand Page - CLEANED & STYLED
 * File: app/Views/admin/brands/create.php
 */

$pageTitle = 'Add Brand';
$currentPage = 'brands';

ob_start();
?>

<style>
  /* Page Layout */
  .add-brand-page {
    max-width: 900px;
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

  .alert-success {
    background: #dcfce7;
    border-left: 4px solid #22c55e;
    color: #166534;
  }

  /* Card */
  .card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  /* Form Elements */
  .form-group {
    margin-bottom: 24px;
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
  .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-textarea {
    resize: vertical;
  }

  .form-hint {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  /* Input with Icon */
  .input-with-icon {
    position: relative;
  }

  .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 14px;
  }

  .input-with-icon .form-input {
    padding-left: 40px;
  }

  /* Checkbox Group */
  .checkbox-group {
    display: flex;
    align-items: center;
  }

  .checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
  }

  .checkbox-group label {
    margin-left: 10px;
    font-size: 14px;
    color: var(--gray-700);
    font-weight: 500;
    cursor: pointer;
  }

  /* Section Divider */
  .section-divider {
    border-top: 2px solid var(--border);
    padding-top: 32px;
    margin-top: 32px;
  }

  .section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 20px;
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
    border: none;
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
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .add-brand-page {
      padding: 0 16px;
    }

    .card {
      padding: 24px;
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

<div class="add-brand-page">
  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-content">
      <a href="<?= url('admin/brands') ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </a>
      <div>
        <h1 class="page-title">Add New Brand</h1>
      </div>
    </div>
    <p class="page-subtitle">Create a new product brand</p>
  </div>

  <!-- Flash Messages -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error">
      <strong><?= getFlash('error') ?></strong>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('success')): ?>
    <div class="alert alert-success">
      <strong><?= getFlash('success') ?></strong>
    </div>
  <?php endif; ?>

  <!-- Brand Form -->
  <form method="POST" action="<?= url('admin/brands/store') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>

    <div class="card">
      <!-- Brand Name -->
      <div class="form-group">
        <label class="form-label">
          Brand Name <span class="required">*</span>
        </label>
        <input 
          type="text" 
          name="name" 
          value="<?= old('name') ?>"
          class="form-input"
          placeholder="e.g., Samsung, Apple, Nike"
          required
          autofocus
        >
        <p class="form-hint">The name of the brand as it will appear to customers</p>
      </div>

      <!-- Slug -->
      <div class="form-group">
        <label class="form-label">Slug (URL)</label>
        <input 
          type="text" 
          name="slug" 
          value="<?= old('slug') ?>"
          class="form-input"
          placeholder="auto-generated from name if left empty"
        >
        <p class="form-hint">Leave empty to auto-generate from brand name. Example: samsung, apple, nike</p>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea 
          name="description" 
          rows="4"
          class="form-textarea"
          placeholder="Brief description of the brand..."
        ><?= old('description') ?></textarea>
        <p class="form-hint">Describe what makes this brand special and unique</p>
      </div>

      <!-- Brand Logo/Image -->
      <div class="form-group">
        <label class="form-label">Brand Logo</label>
        <input 
          type="file" 
          name="image" 
          accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
          class="form-input"
          onchange="previewImage(event)"
          style="padding: 8px 12px;"
        >
        <p class="form-hint">Upload brand logo. Allowed: JPG, PNG, GIF, WebP. Max size: 5MB</p>
        
        <!-- Image Preview -->
        <div id="imagePreview" style="display: none; margin-top: 12px;">
          <img src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: var(--radius-md); border: 2px solid var(--border);">
        </div>
      </div>

      <!-- Website -->
      <div class="form-group">
        <label class="form-label">Website URL</label>
        <div class="input-with-icon">
          <i class="fas fa-globe input-icon"></i>
          <input 
            type="url" 
            name="website" 
            value="<?= old('website') ?>"
            class="form-input"
            placeholder="https://www.example.com"
          >
        </div>
        <p class="form-hint">Official brand website (optional)</p>
      </div>

      <!-- Sort Order -->
      <div class="form-group">
        <label class="form-label">Sort Order</label>
        <input 
          type="number" 
          name="sort_order" 
          value="<?= old('sort_order', '0') ?>"
          min="0"
          class="form-input"
          style="max-width: 200px;"
        >
        <p class="form-hint">Lower numbers appear first in brand listings (0 = highest priority)</p>
      </div>

      <!-- Active Status -->
      <div class="form-group">
        <div class="checkbox-group">
          <input 
            type="checkbox" 
            id="is_active" 
            name="is_active"
            value="1"
            <?= old('is_active', '1') ? 'checked' : '' ?>
          >
          <label for="is_active">Active (visible to customers)</label>
        </div>
        <p class="form-hint" style="margin-left: 28px;">Uncheck to hide this brand from customers</p>
      </div>

      <!-- Featured -->
      <div class="form-group">
        <div class="checkbox-group">
          <input 
            type="checkbox" 
            id="is_featured" 
            name="is_featured"
            value="1"
            <?= old('is_featured', '0') ? 'checked' : '' ?>
          >
          <label for="is_featured">Featured (show in featured brands section)</label>
        </div>
        <p class="form-hint" style="margin-left: 28px;">Featured brands appear prominently on the homepage</p>
      </div>

      <!-- SEO Section -->
      <div class="section-divider">
        <h3 class="section-title">SEO Settings</h3>
        
        <!-- Meta Title -->
        <div class="form-group">
          <label class="form-label">Meta Title</label>
          <input 
            type="text" 
            name="meta_title" 
            value="<?= old('meta_title') ?>"
            class="form-input"
            placeholder="Leave empty to use brand name"
            maxlength="60"
          >
          <p class="form-hint">Recommended: 50-60 characters. Used in search engine results.</p>
        </div>

        <!-- Meta Description -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Meta Description</label>
          <textarea 
            name="meta_description" 
            rows="2"
            class="form-textarea"
            placeholder="Brief description for search engines..."
            maxlength="160"
          ><?= old('meta_description') ?></textarea>
          <p class="form-hint">Recommended: 150-160 characters. Appears in search engine results below the title.</p>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <a href="<?= url('admin/brands') ?>" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Create Brand
      </button>
    </div>
  </form>
</div>

<script>
// Preview image before upload
function previewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('imagePreview');
  const img = preview.querySelector('img');
  
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('File is too large. Maximum size is 5MB.');
      event.target.value = '';
      preview.style.display = 'none';
      return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
      alert('Invalid file type. Please upload JPG, PNG, GIF, or WebP.');
      event.target.value = '';
      preview.style.display = 'none';
      return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
  } else {
    preview.style.display = 'none';
  }
}

// Auto-generate slug from name
document.querySelector('input[name="name"]')?.addEventListener('input', function(e) {
  const slugInput = document.querySelector('input[name="slug"]');
  if (slugInput && !slugInput.dataset.userModified && !slugInput.value) {
    slugInput.value = e.target.value
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim();
  }
});

// Mark slug as user-modified if manually edited
document.querySelector('input[name="slug"]')?.addEventListener('input', function() {
  if (this.value) {
    this.dataset.userModified = 'true';
  }
});

// Auto-add https:// to website URL
document.querySelector('input[name="website"]')?.addEventListener('blur', function(e) {
  const url = e.target.value.trim();
  if (url && !url.startsWith('http://') && !url.startsWith('https://')) {
    e.target.value = 'https://' + url;
  }
});

// Character counter for meta fields
function addCharCounter(inputSelector, maxChars) {
  const input = document.querySelector(inputSelector);
  if (!input) return;
  
  const counter = document.createElement('span');
  counter.style.cssText = 'font-size: 11px; color: var(--gray-500); float: right;';
  
  const hint = input.nextElementSibling;
  if (hint && hint.classList.contains('form-hint')) {
    hint.appendChild(counter);
  }
  
  function updateCounter() {
    const length = input.value.length;
    counter.textContent = `${length}/${maxChars}`;
    counter.style.color = length > maxChars ? '#ef4444' : 'var(--gray-500)';
  }
  
  input.addEventListener('input', updateCounter);
  updateCounter();
}

addCharCounter('input[name="meta_title"]', 60);
addCharCounter('textarea[name="meta_description"]', 160);
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>