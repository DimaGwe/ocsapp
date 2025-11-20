<?php
/**
 * OCS Admin CMS - Edit Content
 * File: app/Views/admin/cms/edit.php
 */

$pageTitle = $pageTitle ?? 'Edit Content';
$currentPage = $currentPage ?? 'cms';

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
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 200px;
    font-family: 'Courier New', monospace;
    line-height: 1.6;
  }

  .info-box {
    background: #f0f9ff;
    border-left: 4px solid #0284c7;
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .info-box h3 {
    font-size: 14px;
    font-weight: 600;
    color: #0369a1;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .info-box ul {
    margin: 8px 0 0 20px;
    font-size: 13px;
    color: #075985;
    line-height: 1.8;
  }

  .meta-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    background: var(--gray-50);
    padding: 20px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
  }

  .meta-item {
    display: flex;
    flex-direction: column;
  }

  .meta-label {
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
  }

  .meta-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
    font-family: 'Courier New', monospace;
  }

  .btn-save {
    padding: 14px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 10px;
  }

  .btn-save:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 24px;
    border-top: 2px solid var(--border);
  }

  .status-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 26px;
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
    background-color: #ccc;
    transition: 0.3s;
    border-radius: 26px;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
  }

  input:checked + .toggle-slider {
    background-color: var(--primary);
  }

  input:checked + .toggle-slider:before {
    transform: translateX(24px);
  }

  .char-counter {
    text-align: right;
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }
</style>

<!-- Page Header -->
<div class="edit-header">
  <div>
    <h1>✏️ Edit Content</h1>
    <p style="color: var(--gray-600); margin-top: 8px;"><?= htmlspecialchars($content['label']) ?></p>
  </div>
  <a href="<?= url('admin/cms') ?>" class="btn-back">
    <i class="fas fa-arrow-left"></i> Back to CMS
  </a>
</div>

<!-- Edit Form -->
<div class="edit-card">
  <!-- Content Metadata -->
  <div class="meta-grid">
    <div class="meta-item">
      <span class="meta-label">Page</span>
      <span class="meta-value"><?= htmlspecialchars($content['page']) ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Section</span>
      <span class="meta-value"><?= htmlspecialchars($content['section']) ?></span>
    </div>
    <div class="meta-item">
      <span class="meta-label">Content Type</span>
      <span class="meta-value"><?= htmlspecialchars($content['content_type']) ?></span>
    </div>
  </div>

  <?php if (!empty($content['description'])): ?>
    <div class="info-box">
      <h3><i class="fas fa-info-circle"></i> Description</h3>
      <p style="margin: 0; font-size: 13px; color: #075985;">
        <?= htmlspecialchars($content['description']) ?>
      </p>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= url('admin/cms/update') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $content['id'] ?>">

    <div class="form-section">
      <label for="content" class="form-label">Content</label>
      <p class="form-description">
        <?php if ($content['content_type'] === 'html'): ?>
          You can use HTML tags for formatting. Be careful with syntax!
        <?php else: ?>
          Enter plain text content that will be displayed on your website.
        <?php endif; ?>
      </p>
      <textarea
        id="content"
        name="content"
        class="form-textarea"
        placeholder="Enter your content here..."
        oninput="updateCharCount(this)"
      ><?= htmlspecialchars($content['content'] ?? '') ?></textarea>
      <div class="char-counter">
        <span id="charCount">0</span> characters
      </div>
    </div>

    <div class="form-actions">
      <div class="status-toggle">
        <label class="toggle-switch">
          <input
            type="checkbox"
            name="status"
            value="active"
            <?= $content['status'] === 'active' ? 'checked' : '' ?>
          >
          <span class="toggle-slider"></span>
        </label>
        <span style="font-size: 14px; font-weight: 600;">
          <span id="statusText"><?= $content['status'] === 'active' ? 'Active' : 'Inactive' ?></span>
        </span>
      </div>

      <button type="submit" class="btn-save">
        <i class="fas fa-save"></i> Save Changes
      </button>
    </div>
  </form>
</div>

<script>
// Character counter
function updateCharCount(textarea) {
  const count = textarea.value.length;
  document.getElementById('charCount').textContent = count.toLocaleString();
}

// Initialize character count
document.addEventListener('DOMContentLoaded', function() {
  const textarea = document.getElementById('content');
  if (textarea) {
    updateCharCount(textarea);
  }

  // Status toggle text update
  const statusCheckbox = document.querySelector('input[name="status"]');
  const statusText = document.getElementById('statusText');

  if (statusCheckbox && statusText) {
    statusCheckbox.addEventListener('change', function() {
      statusText.textContent = this.checked ? 'Active' : 'Inactive';
    });
  }
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
