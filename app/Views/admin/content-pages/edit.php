<?php
/**
 * Admin Content Pages - Edit View
 */
$pageTitle = 'Edit ' . htmlspecialchars($page['title']);
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

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-info {
    background: #dbeafe;
    color: #1e40af;
  }

  .badge-warning {
    background: #fef3c7;
    color: #92400e;
  }

  .badge-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
  }

  .card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    margin-bottom: 24px;
  }

  .card-header {
    margin-bottom: 24px;
  }

  .card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--dark);
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

  .info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }

  .info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .info-item strong {
    font-size: 13px;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .info-item span,
  .info-item code {
    font-size: 14px;
    color: var(--dark);
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
</style>

<!-- TinyMCE Editor -->
<script src="https://cdn.tiny.cloud/1/2q2m3kagr07784sbu50bx4vlmycy4cxsr249gstug6teosyw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

<div class="page-header">
  <div>
    <h1>
      <i class="fas fa-edit"></i> Edit Page: <?= htmlspecialchars($page['title']) ?>
    </h1>
    <div>
      <span class="badge badge-<?= $page['page_type'] === 'about' ? 'info' : 'warning' ?>">
        <?= ucfirst($page['page_type']) ?>
      </span>
      <span class="badge badge-secondary" style="margin-left: 8px;">
        <?= strtoupper($page['language']) ?>
      </span>
    </div>
  </div>
  <a href="<?= url('/admin/content-pages') ?>" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to List
  </a>
</div>

<div id="alert-container"></div>

<div class="card">
  <form id="editPageForm">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $page['id'] ?>">

    <div class="form-row">
      <div class="form-group">
        <label for="title">Page Title *</label>
        <input type="text"
               id="title"
               name="title"
               class="form-control"
               value="<?= htmlspecialchars($page['title']) ?>"
               required>
      </div>

      <div class="form-group">
        <label for="is_published">Publication Status</label>
        <div class="checkbox-wrapper">
          <label class="switch">
            <input type="checkbox"
                   id="is_published"
                   name="is_published"
                   <?= $page['is_published'] ? 'checked' : '' ?>>
            <span class="slider"></span>
          </label>
          <span id="status-label" style="margin-left: 12px; font-weight: 600;">
            <?= $page['is_published'] ? 'Published' : 'Draft' ?>
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label for="meta_description">Meta Description</label>
      <textarea id="meta_description"
                name="meta_description"
                class="form-control"
                rows="2"
                maxlength="160"
                placeholder="SEO meta description (max 160 characters)"><?= htmlspecialchars($page['meta_description'] ?? '') ?></textarea>
      <small class="form-text">Character count: <span id="char-count">0</span>/160</small>
    </div>

    <div class="form-group">
      <label for="content">Page Content *</label>
      <textarea id="content"
                name="content"
                class="form-control"><?= htmlspecialchars($page['content']) ?></textarea>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary" id="saveBtn">
        <i class="fas fa-save"></i> Save Changes
      </button>
      <a href="<?= url('/' . $page['slug']) ?>" class="btn btn-secondary" target="_blank">
        <i class="fas fa-eye"></i> Preview Page
      </a>
    </div>
  </form>
</div>

<div class="card">
  <div class="card-header">
    <h3>Page Information</h3>
  </div>
  <div class="info-grid">
    <div class="info-item">
      <strong>Page Type:</strong>
      <span><?= ucfirst($page['page_type']) ?></span>
    </div>
    <div class="info-item">
      <strong>Slug:</strong>
      <code><?= htmlspecialchars($page['slug']) ?></code>
    </div>
    <div class="info-item">
      <strong>Created:</strong>
      <span><?= formatDate($page['created_at'], 'F d, Y g:i A') ?></span>
    </div>
    <div class="info-item">
      <strong>Last Updated:</strong>
      <span><?= formatDate($page['updated_at'], 'F d, Y g:i A') ?></span>
    </div>
  </div>
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

  // Character counter for meta description
  const metaDesc = document.getElementById('meta_description');
  const charCount = document.getElementById('char-count');

  function updateCharCount() {
    charCount.textContent = metaDesc.value.length;
    charCount.style.color = metaDesc.value.length > 160 ? '#ef4444' : '#00b207';
  }

  metaDesc.addEventListener('input', updateCharCount);
  updateCharCount();

  // Publication status toggle
  const publishCheckbox = document.getElementById('is_published');
  const statusLabel = document.getElementById('status-label');

  publishCheckbox.addEventListener('change', () => {
    statusLabel.textContent = publishCheckbox.checked ? 'Published' : 'Draft';
    statusLabel.style.color = publishCheckbox.checked ? '#00b207' : '#6b7280';
  });

  // Form submission
  document.getElementById('editPageForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const saveBtn = document.getElementById('saveBtn');
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

    const formData = new FormData(e.target);
    formData.set('content', tinymce.get('content').getContent());

    try {
      const response = await fetch('<?= url('/admin/content-pages/update') ?>', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        showAlert('success', data.message);
        setTimeout(() => {
          window.location.href = '<?= url('/admin/content-pages') ?>';
        }, 1500);
      } else {
        showAlert('error', data.message || 'Failed to save page');
        saveBtn.disabled = false;
        saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
      }
    } catch (error) {
      console.error('Error:', error);
      showAlert('error', 'An error occurred. Please try again.');
      saveBtn.disabled = false;
      saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';
    }
  });

  function showAlert(type, message) {
    const alertContainer = document.getElementById('alert-container');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-error';
    alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
    setTimeout(() => {
      alertContainer.innerHTML = '';
    }, 5000);
  }
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
