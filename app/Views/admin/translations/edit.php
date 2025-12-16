<?php
/**
 * Admin Translations Edit
 * Edit a single translation
 */

$pageTitle = $pageTitle ?? 'Edit Translation';
$currentPage = $currentPage ?? 'translations';

ob_start();
?>

<style>
  .edit-header {
    margin-bottom: 32px;
  }

  .edit-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--gray-500);
  }

  .breadcrumb a {
    color: var(--primary);
    text-decoration: none;
  }

  .breadcrumb a:hover {
    text-decoration: underline;
  }

  .edit-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    max-width: 900px;
  }

  .key-display {
    background: #dcfce7;
    padding: 16px 20px;
    border-radius: var(--radius-md);
    margin-bottom: 32px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .key-display label {
    font-weight: 600;
    color: var(--gray-600);
    font-size: 13px;
  }

  .key-display code {
    font-family: 'Courier New', monospace;
    font-size: 16px;
    color: var(--primary);
    font-weight: 600;
  }

  .key-display .category-badge {
    background: var(--primary);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    margin-left: auto;
  }

  .form-row {
    margin-bottom: 28px;
  }

  .form-label {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .lang-badge {
    font-size: 11px;
    font-weight: 700;
    color: white;
    padding: 3px 10px;
    border-radius: 6px;
  }

  .lang-badge-en {
    background: #3b82f6;
  }

  .lang-badge-fr {
    background: #8b5cf6;
  }

  .form-textarea {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    line-height: 1.6;
    resize: vertical;
    min-height: 100px;
    transition: border-color var(--transition-base);
  }

  .form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: border-color var(--transition-base);
  }

  .form-input:focus {
    outline: none;
    border-color: var(--primary);
  }

  .form-help {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 6px;
  }

  .checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 28px;
  }

  .checkbox-row input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
  }

  .checkbox-row label {
    font-size: 14px;
    color: var(--gray-700);
  }

  .form-actions {
    display: flex;
    gap: 12px;
    padding-top: 24px;
    border-top: 2px solid var(--border);
  }

  .btn-save {
    padding: 14px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-save:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
  }

  .btn-cancel {
    padding: 14px 32px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all var(--transition-base);
  }

  .btn-cancel:hover {
    border-color: var(--gray-400);
    background: var(--gray-50);
  }

  .usage-example {
    background: var(--gray-50);
    padding: 16px;
    border-radius: var(--radius-md);
    margin-top: 24px;
  }

  .usage-example h4 {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 8px;
  }

  .usage-example code {
    display: block;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: var(--primary);
    background: white;
    padding: 10px 14px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
  }
</style>

<!-- Header -->
<div class="edit-header">
  <div class="breadcrumb">
    <a href="<?= url('admin/translations') ?>">Translations</a>
    <span>/</span>
    <span>Edit</span>
  </div>
  <h1><i class="fas fa-edit" style="color: var(--primary); margin-right: 12px;"></i>Edit Translation</h1>
</div>

<!-- Edit Form -->
<div class="edit-card">
  <div class="key-display">
    <label>Key:</label>
    <code><?= htmlspecialchars($translation['key']) ?></code>
    <span class="category-badge"><?= htmlspecialchars(ucfirst($translation['category'])) ?></span>
  </div>

  <form method="POST" action="<?= url('admin/translations/update') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="id" value="<?= $translation['id'] ?>">

    <!-- English -->
    <div class="form-row">
      <label class="form-label">
        <span class="lang-badge lang-badge-en notranslate" translate="no">EN</span>
        English Translation
      </label>
      <textarea name="en" class="form-textarea" rows="3"
                placeholder="Enter English text..."><?= htmlspecialchars($translation['en'] ?? '') ?></textarea>
    </div>

    <!-- French -->
    <div class="form-row">
      <label class="form-label">
        <span class="lang-badge lang-badge-fr notranslate" translate="no">FR</span>
        French Translation
      </label>
      <textarea name="fr" class="form-textarea" rows="3"
                placeholder="Enter French text..."><?= htmlspecialchars($translation['fr'] ?? '') ?></textarea>
    </div>

    <!-- Description -->
    <div class="form-row">
      <label class="form-label">Description (for reference)</label>
      <input type="text" name="description" class="form-input"
             value="<?= htmlspecialchars($translation['description'] ?? '') ?>"
             placeholder="Brief description of where this text is used...">
      <p class="form-help">Optional. Helps remember where this translation is used.</p>
    </div>

    <!-- HTML Toggle -->
    <div class="checkbox-row">
      <input type="checkbox" name="is_html" id="is_html" value="1"
             <?= ($translation['is_html'] ?? 0) ? 'checked' : '' ?>>
      <label for="is_html">Contains HTML (will not be escaped when displayed)</label>
    </div>

    <!-- Actions -->
    <div class="form-actions">
      <button type="submit" class="btn-save">
        <i class="fas fa-save"></i> Save Changes
      </button>
      <a href="<?= url('admin/translations') ?>" class="btn-cancel">Cancel</a>
    </div>
  </form>

  <!-- Usage Example -->
  <div class="usage-example">
    <h4>Usage in Code:</h4>
    <code>&lt;?= t('<?= htmlspecialchars($translation['key']) ?>') ?&gt;</code>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
