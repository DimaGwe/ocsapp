<?php
/**
 * Hero Slider - Edit View
 * File: app/Views/admin/sliders/edit.php
 */

$pageTitle = $pageTitle ?? 'Edit Slider';
$currentPage = $currentPage ?? 'cms';
$slider = $slider ?? [];

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

  .current-image {
    margin-bottom: 16px;
    padding: 16px;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
  }

  .current-image img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
  }

  .file-upload-wrapper {
    border: 2px dashed var(--border);
    border-radius: var(--radius-lg);
    padding: 32px;
    text-align: center;
    transition: all var(--transition-base);
    cursor: pointer;
    background: var(--gray-50);
  }

  .file-upload-wrapper:hover {
    border-color: var(--primary);
    background: #eff6ff;
  }

  .file-upload-wrapper.drag-over {
    border-color: var(--primary);
    background: #eff6ff;
  }

  .file-upload-wrapper input[type="file"] {
    display: none;
  }

  .upload-icon {
    font-size: 48px;
    margin-bottom: 8px;
  }

  .file-name-display {
    margin-top: 12px;
    color: var(--primary);
    font-weight: 600;
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
</style>

<!-- Page Header -->
<div class="edit-header">
  <div>
    <div class="breadcrumb">
      <a href="<?= url('/admin/cms') ?>">CMS</a> /
      <a href="<?= url('/admin/sliders') ?>">Hero Sliders</a> /
      Edit
    </div>
    <h1>✎ Edit Slider</h1>
  </div>
  <a href="<?= url('/admin/sliders') ?>" class="btn-back">
    ← Back to Sliders
  </a>
</div>

<!-- Edit Form -->
<div class="edit-card">
  <form method="POST" action="<?= url('/admin/sliders/update') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $slider['id'] ?>">

    <!-- Title -->
    <div class="form-section">
      <label class="form-label" for="title">
        Slide Title <span class="required">*</span>
      </label>
      <div class="form-description">Main heading text for the slide</div>
      <input type="text"
             id="title"
             name="title"
             class="form-input"
             value="<?= htmlspecialchars($slider['title'] ?? '') ?>"
             required>
    </div>

    <!-- Description -->
    <div class="form-section">
      <label class="form-label" for="description">Description</label>
      <div class="form-description">Subtitle or description text (optional)</div>
      <textarea id="description"
                name="description"
                class="form-textarea"
                rows="3"><?= htmlspecialchars($slider['description'] ?? '') ?></textarea>
    </div>

    <!-- Button Text -->
    <div class="form-section">
      <label class="form-label" for="button_text">Button Text</label>
      <div class="form-description">Text displayed on the button (e.g., "Shop Now", "View Deals")</div>
      <input type="text"
             id="button_text"
             name="button_text"
             class="form-input"
             value="<?= htmlspecialchars($slider['button_text'] ?? '') ?>"
             placeholder="Shop Now">
    </div>

    <!-- Button URL -->
    <div class="form-section">
      <label class="form-label" for="button_url">Button URL</label>
      <div class="form-description">Where the button links to (e.g., /categories, /deals)</div>
      <input type="text"
             id="button_url"
             name="button_url"
             class="form-input"
             value="<?= htmlspecialchars($slider['button_url'] ?? '') ?>"
             placeholder="/categories">
    </div>

    <!-- Image Upload -->
    <div class="form-section">
      <label class="form-label" for="image">Slider Image</label>
      <div class="form-description">Upload a new image to replace the current one (leave empty to keep current)</div>

      <?php if (!empty($slider['image_path'])): ?>
        <div class="current-image">
          <strong style="display: block; margin-bottom: 12px; color: var(--dark);">Current Image:</strong>
          <img src="<?= url($slider['image_path']) ?>"
               alt="Current slider image">
        </div>
      <?php endif; ?>

      <label for="image" class="file-upload-wrapper" id="fileUploadWrapper">
        <div class="upload-icon">📤</div>
        <div><strong>Click to upload</strong> or drag and drop</div>
        <div class="form-description" style="margin-top: 8px;">JPEG, PNG, GIF, WebP (Max 5MB)</div>
        <input type="file"
               id="image"
               name="image"
               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
      </label>
      <div id="fileName" class="file-name-display"></div>
    </div>

    <!-- Sort Order -->
    <div class="form-section">
      <label class="form-label" for="sort_order">Display Order</label>
      <div class="form-description">Lower numbers appear first (e.g., 1, 2, 3...)</div>
      <input type="number"
             id="sort_order"
             name="sort_order"
             class="form-input"
             value="<?= $slider['sort_order'] ?? 0 ?>"
             min="0">
    </div>

    <!-- Status -->
    <div class="form-section">
      <label class="form-label">Status</label>
      <div class="form-description">Toggle to show or hide this slider on the website</div>
      <div style="display: flex; align-items: center; gap: 16px;">
        <label class="toggle-switch">
          <input type="checkbox"
                 id="statusCheckbox"
                 <?= ($slider['status'] ?? 'active') === 'active' ? 'checked' : '' ?>>
          <span class="toggle-slider"></span>
        </label>
        <span id="statusText" style="color: var(--text); font-weight: 500;">
          <?= ($slider['status'] ?? 'active') === 'active' ? 'Active (Visible on website)' : 'Inactive (Hidden)' ?>
        </span>
      </div>
      <input type="hidden" name="status" id="statusInput" value="<?= $slider['status'] ?? 'active' ?>">
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
      <button type="submit" class="btn-save">
        💾 Save Changes
      </button>
      <a href="<?= url('/admin/sliders') ?>" class="btn-cancel">
        Cancel
      </a>
    </div>
  </form>
</div>

<script>
  // File upload handling
  const fileInput = document.getElementById('image');
  const fileUploadWrapper = document.getElementById('fileUploadWrapper');
  const fileNameDisplay = document.getElementById('fileName');

  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      fileNameDisplay.textContent = '📎 Selected: ' + this.files[0].name;
    }
  });

  // Drag and drop
  fileUploadWrapper.addEventListener('dragover', function(e) {
    e.preventDefault();
    this.classList.add('drag-over');
  });

  fileUploadWrapper.addEventListener('dragleave', function() {
    this.classList.remove('drag-over');
  });

  fileUploadWrapper.addEventListener('drop', function(e) {
    e.preventDefault();
    this.classList.remove('drag-over');

    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
      fileInput.files = e.dataTransfer.files;
      fileNameDisplay.textContent = '📎 Selected: ' + e.dataTransfer.files[0].name;
    }
  });

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
