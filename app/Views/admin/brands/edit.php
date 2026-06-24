<?php
/**
 * Admin Edit Brand Page - CLEANED & STYLED
 * File: app/Views/admin/brands/edit.php
 */

$pageTitle = 'Edit Brand';
$currentPage = 'brands';

ob_start();
?>

<style>
  /* Page Layout */
  .edit-brand-page {
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

  /* Image Upload */
  .current-image-section {
    margin-bottom: 20px;
  }

  .current-image-label {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 8px;
    display: block;
  }

  .image-preview {
    display: inline-block;
    position: relative;
  }

  .image-preview img {
    max-width: 200px;
    max-height: 200px;
    object-fit: contain;
    border-radius: var(--radius-md);
    border: 2px solid var(--border);
    transition: opacity var(--transition-base);
  }

  .remove-image-checkbox {
    display: flex;
    align-items: center;
    margin-top: 12px;
  }

  .remove-image-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
  }

  .remove-image-checkbox label {
    margin-left: 8px;
    font-size: 13px;
    color: #ef4444;
    font-weight: 500;
    cursor: pointer;
  }

  .upload-section {
    display: flex;
    align-items: start;
    gap: 16px;
  }

  .upload-input-wrapper {
    flex: 1;
  }

  .file-input {
    display: block;
    width: 100%;
    padding: 12px;
    border: 2px dashed var(--border);
    border-radius: var(--radius-md);
    background: var(--gray-50);
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .file-input:hover {
    border-color: var(--primary);
    background: rgba(0, 178, 7, 0.02);
  }

  .new-image-preview {
    width: 100px;
    height: 100px;
    display: none;
  }

  .new-image-preview.active {
    display: block;
  }

  .new-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--radius-md);
    border: 2px solid var(--primary);
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
    .edit-brand-page {
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

<div class="edit-brand-page">
  <!-- Page Header -->
  <div class="page-header">
    <div class="page-header-content">
      <a href="<?= url('admin/brands') ?>" class="back-btn">
        <i class="fas fa-arrow-left"></i>
      </a>
      <div>
        <h1 class="page-title">Edit Brand</h1>
      </div>
    </div>
    <p class="page-subtitle">Update brand information and settings</p>
  </div>

  <!-- Flash Messages -->
  <?php if (hasFlash('error')): ?>
    <div class="alert alert-error">
      <strong><?= getFlash('error') ?></strong>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('success')): ?>
    <div class="alert alert-success" style="background: #dcfce7; border-color: #22c55e; color: #166534;">
      <strong><?= getFlash('success') ?></strong>
    </div>
  <?php endif; ?>

  <!-- Brand Form -->
  <form method="POST" action="<?= url('admin/brands/update') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $brand['id'] ?>">

    <div class="card">
      <!-- Brand Name -->
      <div class="form-group">
        <label class="form-label">
          Brand Name <span class="required">*</span>
        </label>
        <input 
          type="text" 
          name="name" 
          value="<?= htmlspecialchars($brand['name']) ?>"
          class="form-input"
          placeholder="e.g., Samsung, Apple, Nike"
          required
        >
      </div>

      <!-- Slug -->
      <div class="form-group">
        <label class="form-label">
          Slug (URL) <span class="required">*</span>
        </label>
        <input 
          type="text" 
          name="slug" 
          value="<?= htmlspecialchars($brand['slug']) ?>"
          class="form-input"
          placeholder="e.g., samsung, apple, nike"
          required
        >
        <p class="form-hint">Used in URLs. Example: /brands/samsung</p>
      </div>

      <!-- Description -->
      <div class="form-group">
        <label class="form-label">Description</label>
        <textarea 
          name="description" 
          rows="4"
          class="form-textarea"
          placeholder="Brief description of the brand..."
        ><?= htmlspecialchars($brand['description'] ?? '') ?></textarea>
        <p class="form-hint">Describe what makes this brand special</p>
      </div>

      <!-- Brand Logo -->
      <div class="form-group">
        <label class="form-label">Brand Logo</label>
        
        <!-- Current Logo -->
        <?php if (!empty($brand['logo'])): ?>
          <div class="current-image-section">
            <span class="current-image-label">Current Logo:</span>
            <div class="image-preview">
              <img 
                src="<?= asset($brand['logo']) ?>" 
                alt="Brand Logo"
                id="currentImage"
              >
            </div>
            <div class="remove-image-checkbox">
              <input 
                type="checkbox" 
                id="remove_image" 
                name="remove_image"
                onchange="toggleImageRemoval()"
              >
              <label for="remove_image">Remove current logo</label>
            </div>
          </div>
        <?php endif; ?>

        <!-- Upload New Logo -->
        <div class="upload-section">
          <div class="upload-input-wrapper">
            <input 
              type="file" 
              id="image" 
              name="image" 
              accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
              class="file-input"
              onchange="previewNewImage(event)"
            >
            <p class="form-hint">JPG, PNG, GIF, WebP. Max 5MB</p>
          </div>
          <div id="newImagePreview" class="new-image-preview">
            <img src="" alt="New Preview">
          </div>
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
            value="<?= htmlspecialchars($brand['website'] ?? '') ?>"
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
          value="<?= $brand['sort_order'] ?? 0 ?>"
          min="0"
          class="form-input"
          style="max-width: 200px;"
        >
        <p class="form-hint">Lower numbers appear first in brand listings</p>
      </div>

      <!-- Active Status -->
      <div class="form-group">
        <div class="checkbox-group">
          <input 
            type="checkbox" 
            id="is_active" 
            name="is_active"
            <?= ($brand['is_active'] ?? 1) ? 'checked' : '' ?>
          >
          <label for="is_active">Active (visible to customers)</label>
        </div>
      </div>

      <!-- Featured -->
      <div class="form-group">
        <div class="checkbox-group">
          <input 
            type="checkbox" 
            id="is_featured" 
            name="is_featured"
            <?= ($brand['is_featured'] ?? 0) ? 'checked' : '' ?>
          >
          <label for="is_featured">Featured (show in featured brands section)</label>
        </div>
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
            value="<?= htmlspecialchars($brand['meta_title'] ?? '') ?>"
            class="form-input"
            placeholder="Leave empty to use brand name"
          >
          <p class="form-hint">Recommended: 50-60 characters</p>
        </div>

        <!-- Meta Description -->
        <div class="form-group" style="margin-bottom: 0;">
          <label class="form-label">Meta Description</label>
          <textarea 
            name="meta_description" 
            rows="2"
            class="form-textarea"
            placeholder="Brief description for search engines..."
          ><?= htmlspecialchars($brand['meta_description'] ?? '') ?></textarea>
          <p class="form-hint">Recommended: 150-160 characters</p>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <a href="<?= url('admin/brands') ?>" class="btn btn-secondary">
        Cancel
      </a>
      <button type="submit" class="btn btn-primary">
        <i class="fas fa-save"></i> Update Brand
      </button>
    </div>
  </form>
</div>

<script>
// Preview new image upload
function previewNewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById('newImagePreview');
  const img = preview.querySelector('img');
  
  if (file) {
    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
      alert('File is too large. Maximum size is 5MB.');
      event.target.value = '';
      preview.classList.remove('active');
      return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!validTypes.includes(file.type)) {
      alert('Invalid file type. Please upload JPG, PNG, GIF, or WebP.');
      event.target.value = '';
      preview.classList.remove('active');
      return;
    }
    
    const reader = new FileReader();
    reader.onload = function(e) {
      img.src = e.target.result;
      preview.classList.add('active');
      
      // Uncheck remove image if new file selected
      const removeCheckbox = document.getElementById('remove_image');
      if (removeCheckbox) {
        removeCheckbox.checked = false;
        const currentImage = document.getElementById('currentImage');
        if (currentImage) {
          currentImage.style.opacity = '1';
        }
      }
    };
    reader.readAsDataURL(file);
  } else {
    preview.classList.remove('active');
  }
}

// Toggle current image removal
function toggleImageRemoval() {
  const removeCheckbox = document.getElementById('remove_image');
  const currentImage = document.getElementById('currentImage');
  
  if (removeCheckbox && currentImage) {
    if (removeCheckbox.checked) {
      currentImage.style.opacity = '0.3';
      
      // Clear file input
      const fileInput = document.getElementById('image');
      if (fileInput) {
        fileInput.value = '';
      }
      
      // Hide new preview
      const newPreview = document.getElementById('newImagePreview');
      if (newPreview) {
        newPreview.classList.remove('active');
      }
    } else {
      currentImage.style.opacity = '1';
    }
  }
}

// Auto-generate slug from name
document.querySelector('input[name="name"]')?.addEventListener('input', function(e) {
  const slugInput = document.querySelector('input[name="slug"]');
  if (slugInput && !slugInput.dataset.userModified) {
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
  this.dataset.userModified = 'true';
});

// Validate website URL format
document.querySelector('input[name="website"]')?.addEventListener('blur', function(e) {
  const url = e.target.value.trim();
  if (url && !url.startsWith('http://') && !url.startsWith('https://')) {
    e.target.value = 'https://' + url;
  }
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>