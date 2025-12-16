<?php
/**
 * Admin Translations Index
 * Manage multilingual translations
 */

$pageTitle = $pageTitle ?? 'Translations';
$currentPage = $currentPage ?? 'translations';

ob_start();
?>

<style>
  .translations-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 16px;
  }

  .translations-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn-primary {
    padding: 12px 24px;
    background: var(--primary);
    color: white;
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

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
  }

  .btn-secondary {
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

  .btn-secondary:hover {
    border-color: var(--gray-400);
    background: var(--gray-50);
  }

  /* Filters */
  .filters-bar {
    display: flex;
    gap: 16px;
    margin-bottom: 24px;
    flex-wrap: wrap;
    align-items: center;
    background: white;
    padding: 16px;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
  }

  .filter-group {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .filter-group label {
    font-weight: 600;
    font-size: 13px;
    color: var(--gray-600);
  }

  .filter-select, .filter-input {
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    min-width: 150px;
  }

  .filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: var(--primary);
  }

  .filter-input {
    min-width: 250px;
  }

  .stats-badge {
    background: var(--gray-100);
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-size: 13px;
    color: var(--gray-600);
  }

  .stats-badge strong {
    color: var(--primary);
  }

  /* Category Cards */
  .category-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
  }

  .category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: var(--gray-50);
    border-bottom: 2px solid var(--border);
    cursor: pointer;
  }

  .category-header:hover {
    background: var(--gray-100);
  }

  .category-title {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .category-title h2 {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
    text-transform: capitalize;
  }

  .category-badge {
    background: var(--primary);
    color: white;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
  }

  .category-toggle {
    color: var(--gray-500);
    transition: transform var(--transition-base);
  }

  .category-toggle.collapsed {
    transform: rotate(-90deg);
  }

  .category-content {
    padding: 0;
  }

  /* Translation Table */
  .trans-table {
    width: 100%;
    border-collapse: collapse;
  }

  .trans-table th {
    text-align: left;
    padding: 14px 20px;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    background: white;
    border-bottom: 2px solid var(--border);
  }

  .trans-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
    vertical-align: top;
  }

  .trans-table tr:hover {
    background: var(--gray-50);
  }

  .trans-key {
    font-family: 'Courier New', monospace;
    font-size: 13px;
    color: var(--primary);
    background: #dcfce7;
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
  }

  .trans-text {
    max-width: 300px;
    line-height: 1.5;
  }

  .trans-text-empty {
    color: var(--gray-400);
    font-style: italic;
  }

  .lang-label {
    font-size: 10px;
    font-weight: 700;
    color: white;
    padding: 2px 6px;
    border-radius: 4px;
    margin-right: 8px;
    display: inline-block;
    min-width: 24px;
    text-align: center;
  }

  .lang-en {
    background: #3b82f6;
  }

  .lang-fr {
    background: #8b5cf6;
  }

  .trans-description {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
  }

  /* Actions */
  .btn-edit-sm {
    padding: 6px 14px;
    background: white;
    color: var(--primary);
    border: 2px solid var(--primary);
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
  }

  .btn-edit-sm:hover {
    background: var(--primary);
    color: white;
  }

  .btn-delete-sm {
    padding: 6px 14px;
    background: white;
    color: #ef4444;
    border: 2px solid #ef4444;
    border-radius: var(--radius-sm);
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-delete-sm:hover {
    background: #ef4444;
    color: white;
  }

  .action-btns {
    display: flex;
    gap: 8px;
  }

  /* Empty State */
  .empty-state {
    text-align: center;
    padding: 60px 24px;
    color: var(--gray-500);
  }

  .empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
  }

  /* Info Banner */
  .info-banner {
    background: #dbeafe;
    border-left: 4px solid #3b82f6;
    padding: 16px;
    border-radius: var(--radius-md);
    margin-bottom: 24px;
    display: flex;
    align-items: start;
    gap: 12px;
  }

  .info-banner i {
    color: #3b82f6;
    font-size: 20px;
  }

  .info-banner p {
    margin: 0;
    font-size: 14px;
    color: #1e40af;
    line-height: 1.6;
  }
</style>

<!-- Header -->
<div class="translations-header">
  <div>
    <h1><i class="fas fa-language" style="color: var(--primary); margin-right: 12px;"></i>Translations</h1>
    <p style="color: var(--gray-600); margin-top: 8px;">Manage multilingual text across your marketplace</p>
  </div>
  <div class="header-actions">
    <a href="<?= url('admin/translations/export') ?>" class="btn-secondary">
      <i class="fas fa-download"></i> Export JSON
    </a>
    <a href="<?= url('admin/translations/create') ?>" class="btn-primary">
      <i class="fas fa-plus"></i> Add Translation
    </a>
  </div>
</div>

<!-- Info Banner -->
<div class="info-banner">
  <i class="fas fa-info-circle"></i>
  <p>
    <strong>How it works:</strong> Edit translations here to update text across your website.
    Use <code>t('key')</code> in your code to display translated text. Changes take effect immediately.
  </p>
</div>

<!-- Filters -->
<form method="GET" action="<?= url('admin/translations') ?>" class="filters-bar">
  <div class="filter-group">
    <label>Category:</label>
    <select name="category" class="filter-select" onchange="this.form.submit()">
      <option value="">All Categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= $currentCategory === $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars(ucfirst($cat)) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="filter-group">
    <label>Search:</label>
    <input type="text" name="search" class="filter-input"
           placeholder="Search keys, text, description..."
           value="<?= htmlspecialchars($search) ?>">
  </div>

  <button type="submit" class="btn-primary" style="padding: 10px 20px;">
    <i class="fas fa-search"></i> Search
  </button>

  <?php if ($currentCategory || $search): ?>
    <a href="<?= url('admin/translations') ?>" class="btn-secondary" style="padding: 10px 20px;">
      <i class="fas fa-times"></i> Clear
    </a>
  <?php endif; ?>

  <div class="stats-badge" style="margin-left: auto;">
    <strong><?= $totalCount ?></strong> translations total
  </div>
</form>

<!-- Translations by Category -->
<?php if (empty($translationsByCategory)): ?>
  <div class="category-card">
    <div class="empty-state">
      <i class="fas fa-language"></i>
      <h3>No translations found</h3>
      <p>Try adjusting your search or create a new translation.</p>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($translationsByCategory as $category => $translations): ?>
    <div class="category-card">
      <div class="category-header" onclick="toggleCategory('<?= htmlspecialchars($category) ?>')">
        <div class="category-title">
          <h2><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $category))) ?></h2>
          <span class="category-badge"><?= count($translations) ?></span>
        </div>
        <i class="fas fa-chevron-down category-toggle" id="toggle-<?= htmlspecialchars($category) ?>"></i>
      </div>

      <div class="category-content" id="content-<?= htmlspecialchars($category) ?>">
        <table class="trans-table">
          <thead>
            <tr>
              <th style="width: 180px;">Key</th>
              <th>English</th>
              <th>French</th>
              <th style="width: 120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($translations as $t): ?>
              <tr>
                <td>
                  <span class="trans-key"><?= htmlspecialchars($t['key']) ?></span>
                  <?php if ($t['description']): ?>
                    <div class="trans-description"><?= htmlspecialchars($t['description']) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="trans-text">
                    <span class="lang-label lang-en notranslate" translate="no">EN</span>
                    <?php if ($t['en']): ?>
                      <?= htmlspecialchars(mb_substr($t['en'], 0, 100)) ?>
                      <?= mb_strlen($t['en']) > 100 ? '...' : '' ?>
                    <?php else: ?>
                      <span class="trans-text-empty">Not set</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <div class="trans-text">
                    <span class="lang-label lang-fr notranslate" translate="no">FR</span>
                    <?php if ($t['fr']): ?>
                      <?= htmlspecialchars(mb_substr($t['fr'], 0, 100)) ?>
                      <?= mb_strlen($t['fr']) > 100 ? '...' : '' ?>
                    <?php else: ?>
                      <span class="trans-text-empty">Not set</span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <div class="action-btns">
                    <a href="<?= url('admin/translations/edit?id=' . $t['id']) ?>" class="btn-edit-sm">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="<?= url('admin/translations/delete') ?>"
                          style="display: inline; margin: 0;"
                          onsubmit="return confirm('Delete this translation?');">
                      <?= csrfField() ?>
                      <input type="hidden" name="id" value="<?= $t['id'] ?>">
                      <button type="submit" class="btn-delete-sm">
                        <i class="fas fa-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleCategory(category) {
  const content = document.getElementById('content-' + category);
  const toggle = document.getElementById('toggle-' + category);

  if (content.style.display === 'none') {
    content.style.display = 'block';
    toggle.classList.remove('collapsed');
  } else {
    content.style.display = 'none';
    toggle.classList.add('collapsed');
  }
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
