<?php
/**
 * Admin Translations Create
 * Add a new translation
 */

$pageTitle = $pageTitle ?? 'Add Translation';
$currentPage = $currentPage ?? 'translations';

ob_start();
?>

<style>
  .create-header {
    margin-bottom: 32px;
  }

  .create-header h1 {
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

  .create-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 32px;
    max-width: 900px;
  }

  .form-row {
    margin-bottom: 28px;
  }

  .form-row-half {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
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

  .form-label .required {
    color: #ef4444;
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

  .form-input, .form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    transition: border-color var(--transition-base);
  }

  .form-input:focus, .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
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

  .info-box {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 14px 16px;
    border-radius: var(--radius-md);
    margin-bottom: 28px;
    font-size: 13px;
    color: #92400e;
  }

  .info-box strong {
    display: block;
    margin-bottom: 4px;
  }
</style>

<!-- Header -->
<div class="create-header">
  <div class="breadcrumb">
    <a href="<?= url('admin/translations') ?>">Translations</a>
    <span>/</span>
    <span>Add New</span>
  </div>
  <h1><i class="fas fa-plus" style="color: var(--primary); margin-right: 12px;"></i>Add Translation</h1>
</div>

<!-- Create Form -->
<div class="create-card">
  <div class="info-box">
    <strong>Naming Convention</strong>
    Use lowercase letters with underscores. Example: <code>checkout_button_text</code>, <code>cart_empty_message</code>
  </div>

  <form method="POST" action="<?= url('admin/translations/create') ?>">
    <?= csrfField() ?>

    <!-- Key and Category -->
    <div class="form-row-half">
      <div>
        <label class="form-label">
          Translation Key <span class="required">*</span>
        </label>
        <input type="text" name="key" class="form-input" required
               pattern="[a-z][a-z0-9_]*"
               placeholder="e.g., checkout_title"
               value="<?= htmlspecialchars($_POST['key'] ?? '') ?>">
        <p class="form-help">Lowercase letters, numbers, underscores only</p>
      </div>

      <div>
        <label class="form-label">
          Category <span class="required">*</span>
        </label>
        <select name="category" class="form-select" required>
          <option value="">Select category...</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars(ucfirst($cat)) ?></option>
          <?php endforeach; ?>
          <option value="general">General</option>
          <option value="header">Header</option>
          <option value="navigation">Navigation</option>
          <option value="footer">Footer</option>
          <option value="common">Common</option>
          <option value="product">Product</option>
          <option value="cart">Cart</option>
          <option value="checkout">Checkout</option>
          <option value="account">Account</option>
          <option value="pages">Pages</option>
          <option value="admin">Admin</option>
        </select>
        <p class="form-help">Group related translations together</p>
      </div>
    </div>

    <!-- English -->
    <div class="form-row">
      <label class="form-label">
        <span class="lang-badge lang-badge-en notranslate" translate="no">EN</span>
        English Translation
      </label>
      <textarea name="en" class="form-textarea" rows="3"
                placeholder="Enter English text..."><?= htmlspecialchars($_POST['en'] ?? '') ?></textarea>
    </div>

    <!-- French -->
    <div class="form-row">
      <label class="form-label">
        <span class="lang-badge lang-badge-fr notranslate" translate="no">FR</span>
        French Translation
      </label>
      <textarea name="fr" class="form-textarea" rows="3"
                placeholder="Enter French text..."><?= htmlspecialchars($_POST['fr'] ?? '') ?></textarea>
    </div>

    <!-- Description -->
    <div class="form-row">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-input"
             value="<?= htmlspecialchars($_POST['description'] ?? '') ?>"
             placeholder="Brief description of where this text is used...">
      <p class="form-help">Optional. Helps remember where this translation is used.</p>
    </div>

    <!-- HTML Toggle -->
    <div class="checkbox-row">
      <input type="checkbox" name="is_html" id="is_html" value="1">
      <label for="is_html">Contains HTML (will not be escaped when displayed)</label>
    </div>

    <!-- Actions -->
    <div class="form-actions">
      <button type="submit" class="btn-save">
        <i class="fas fa-plus"></i> Create Translation
      </button>
      <a href="<?= url('admin/translations') ?>" class="btn-cancel">Cancel</a>
    </div>
  </form>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
