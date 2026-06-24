<?php
/**
 * Admin Content Pages - Create View
 */
$pageTitle = 'Create Content Page';
$currentPage = 'content-pages';

ob_start();
?>

<style>
  .page-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 16px;
  }

  .page-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
  }

  .card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  .form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 14px;
    color: var(--gray-700);
  }

  .form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: all var(--transition-base);
  }

  .form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-text {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: var(--gray-500);
  }

  .checkbox-wrapper {
    display: flex;
    align-items: center;
    margin-top: 8px;
  }

  .switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 28px;
  }

  .switch input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: var(--gray-300);
    transition: 0.3s;
    border-radius: 28px;
  }

  .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
  }

  input:checked + .slider {
    background-color: var(--primary);
  }

  input:checked + .slider:before {
    transform: translateX(22px);
  }

  .form-actions {
    display: flex;
    gap: 12px;
    margin-top: 32px;
  }

  .btn {
    padding: 12px 24px;
    border: none;
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

  .btn-primary {
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover:not(:disabled) {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }

  .btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .btn-secondary:hover {
    background: var(--gray-300);
  }

  .alert {
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .alert-success {
    background: #dcfce7;
    color: #166534;
    border-left: 4px solid #16a34a;
  }

  .alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #dc2626;
  }

  @media (max-width: 768px) {
    .form-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/2q2m3kagr07784sbu50bx4vlmycy4cxsr249gstug6teosyw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div class="page-header">
  <div>
    <h1>
      <i class="fas fa-plus-circle"></i> Create New Content Page
    </h1>
  </div>
  <a href="<?= url('/admin/content-pages') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-error">
  <?= htmlspecialchars($_SESSION['error']) ?>
  <?php unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="card">
  <form action="<?= url('/admin/content-pages/create') ?>" method="POST" id="createPageForm">
    <?= csrfField() ?>

    <div class="form-row">
      <div class="form-group">
        <label for="page_type">Page Type *</label>
        <select id="page_type" name="page_type" class="form-control" required>
          <option value="">Select Page Type</option>
          <option value="about">About Us</option>
          <option value="contact">Contact Us</option>
          <option value="faq">FAQ</option>
          <option value="help">Help</option>
          <option value="custom">Custom Page</option>
        </select>
        <small class="form-text">Choose the type of content page</small>
      </div>

      <div class="form-group">
        <label for="language">Language *</label>
        <select id="language" name="language" class="form-control" required>
          <option value="fr">French (FR)</option>
          <option value="en">English (EN)</option>
        </select>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="title">Page Title *</label>
        <input type="text"
               id="title"
               name="title"
               class="form-control"
               placeholder="Enter page title"
               required>
      </div>

      <div class="form-group">
        <label for="slug">URL Slug *</label>
        <input type="text"
               id="slug"
               name="slug"
               class="form-control"
               placeholder="e.g., about-us"
               required>
        <small class="form-text">URL-friendly identifier (lowercase, hyphens only)</small>
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="meta_description">Meta Description</label>
        <textarea id="meta_description"
                  name="meta_description"
                  class="form-control"
                  rows="2"
                  maxlength="160"
                  placeholder="SEO meta description (max 160 characters)"></textarea>
        <small class="form-text">Character count: <span id="char-count">0</span>/160</small>
      </div>

      <div class="form-group">
        <label for="is_published">Publication Status</label>
        <div class="checkbox-wrapper">
          <label class="switch">
            <input type="checkbox" id="is_published" name="is_published">
            <span class="slider"></span>
          </label>
          <span id="status-label" style="margin-left: 12px; font-weight: 600; color: #6b7280;">
            Draft
          </span>
        </div>
        <small class="form-text">Enable to make the page publicly visible</small>
      </div>
    </div>

    <div class="form-group">
      <label for="content">Page Content *</label>
      <textarea id="content"
                name="content"
                class="form-control"></textarea>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="saveBtn">
        <i class="fas fa-save"></i> Create Page
      </button>
      <a href="<?= url('/admin/content-pages') ?>" class="btn btn-secondary">
        <i class="fas fa-times"></i> Cancel
      </a>
    </div>
  </form>
</div>

<script>
  // Initialize TinyMCE
  tinymce.init({
    selector: '#content',
    height: 500,
    menubar: true,
    plugins: [
      'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
      'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
      'insertdatetime', 'media', 'table', 'help', 'wordcount'
    ],
    toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
    content_style: 'body { font-family: Poppins, sans-serif; font-size: 14px }'
  });

  // Auto-generate slug from title
  const titleInput = document.getElementById('title');
  const slugInput = document.getElementById('slug');
  let slugEdited = false;

  slugInput.addEventListener('input', () => {
    slugEdited = true;
  });

  titleInput.addEventListener('input', () => {
    if (!slugEdited) {
      slugInput.value = titleInput.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/[\s_]+/g, '-')
        .replace(/-+/g, '-')
        .trim();
    }
  });

  // Character counter for meta description
  const metaDesc = document.getElementById('meta_description');
  const charCount = document.getElementById('char-count');

  function updateCharCount() {
    charCount.textContent = metaDesc.value.length;
    charCount.style.color = metaDesc.value.length > 160 ? '#ef4444' : '#00b207';
  }

  metaDesc.addEventListener('input', updateCharCount);

  // Publication status toggle
  const publishCheckbox = document.getElementById('is_published');
  const statusLabel = document.getElementById('status-label');

  publishCheckbox.addEventListener('change', () => {
    statusLabel.textContent = publishCheckbox.checked ? 'Published' : 'Draft';
    statusLabel.style.color = publishCheckbox.checked ? '#00b207' : '#6b7280';
  });

  // Form validation before submit
  document.getElementById('createPageForm').addEventListener('submit', function(e) {
    // Sync TinyMCE content to textarea
    tinymce.triggerSave();

    const content = document.getElementById('content').value;
    if (!content || content.trim() === '') {
      e.preventDefault();
      alert('Page content is required');
      return false;
    }

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...';
  });
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
