<?php
/**
 * OCS Admin Categories Management
 * File: app/Views/admin/categories/index.php
 */

$pageTitle = 'Categories';
$currentPage = 'categories';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'categories' => 'Categories',
        'organize_catalog' => 'Organize your product catalog with categories',
        'add_category' => 'Add Category',
        'category' => 'Category',
        'parent' => 'Parent',
        'subcategories' => 'Subcategories',
        'products' => 'Products',
        'sort_order' => 'Sort Order',
        'status' => 'Status',
        'actions' => 'Actions',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_categories_found' => 'No categories found',
        'create_first_category' => 'Create your first category to get started',
        'confirm_delete' => 'Are you sure you want to delete this category?',
        'none' => '—',
    ],
    'fr' => [
        'categories' => 'Catégories',
        'organize_catalog' => 'Organisez votre catalogue avec des catégories',
        'add_category' => 'Ajouter Catégorie',
        'category' => 'Catégorie',
        'parent' => 'Parent',
        'subcategories' => 'Sous-catégories',
        'products' => 'Produits',
        'sort_order' => 'Ordre',
        'status' => 'Statut',
        'actions' => 'Actions',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'no_categories_found' => 'Aucune catégorie trouvée',
        'create_first_category' => 'Créez votre première catégorie pour commencer',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette catégorie?',
        'none' => '—',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .categories-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    gap: 24px;
  }

  .categories-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .categories-header-content p {
    font-size: 15px;
    color: var(--gray-600);
  }

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary);
    color: white;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    white-space: nowrap;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Table Card */
  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .table-wrapper {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 24px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  th.text-right {
    text-align: right;
  }

  td {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  td.text-right {
    text-align: right;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  tbody tr.subcategory {
    background: #fafafa;
  }

  tbody tr.subcategory:hover {
    background: var(--gray-100);
  }

  /* Category Cell */
  .category-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .category-visual {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .category-visual img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
  }

  .category-visual.icon {
    background: #dcfce7;
    color: var(--primary);
    font-size: 18px;
  }

  .category-visual.default {
    background: var(--gray-100);
    color: var(--gray-400);
    font-size: 18px;
  }

  .category-info {
    min-width: 0;
  }

  .category-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .subcategory-indicator {
    color: var(--gray-400);
    font-size: 16px;
  }

  .category-slug {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .parent-text {
    color: var(--gray-500);
    font-size: 13px;
  }

  .count-text {
    color: var(--gray-600);
    font-size: 13px;
    font-weight: 500;
  }

  .order-text {
    color: var(--gray-600);
    font-size: 13px;
  }

  /* Badges */
  .badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.active { background: #dcfce7; color: #166534; }
  .badge.inactive { background: var(--gray-200); color: var(--gray-700); }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
  }

  .action-btn {
    background: transparent;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: all var(--transition-base);
    padding: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .action-btn.edit,
  a.action-btn.edit { 
    color: var(--primary);
    text-decoration: none;
  }
  
  .action-btn.edit:hover,
  a.action-btn.edit:hover { 
    color: var(--primary-600);
  }

  .action-btn.edit i,
  a.action-btn.edit i {
    color: var(--primary);
  }

  .action-btn.edit:hover i,
  a.action-btn.edit:hover i {
    color: var(--primary-600);
  }

  .action-btn.delete,
  button.action-btn.delete { 
    color: #ef4444;
  }
  
  .action-btn.delete:hover,
  button.action-btn.delete:hover { 
    color: #dc2626;
  }

  .action-btn.delete i,
  button.action-btn.delete i {
    color: #ef4444;
  }

  .action-btn.delete:hover i,
  button.action-btn.delete:hover i {
    color: #dc2626;
  }

  /* Empty State */
  .empty-state {
    padding: 64px 24px;
    text-align: center;
  }

  .empty-state-icon {
    font-size: 48px;
    color: var(--gray-400);
    margin-bottom: 16px;
  }

  .empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 8px;
  }

  .empty-state-text {
    font-size: 14px;
    color: var(--gray-500);
    margin-bottom: 16px;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .categories-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .btn-primary {
      width: 100%;
      justify-content: center;
    }

    th, td {
      padding: 12px 16px;
    }

    .category-visual {
      width: 36px;
      height: 36px;
    }
  }
</style>

<!-- Page Header -->
<div class="categories-header">
  <div class="categories-header-content">
    <h1><?= $t['categories'] ?></h1>
    <p><?= $t['organize_catalog'] ?></p>
  </div>
  <a href="<?= url('admin/categories/create') ?>" class="btn-primary">
    <i class="fas fa-plus"></i> <?= $t['add_category'] ?>
  </a>
</div>

<!-- Categories Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['category'] ?></th>
          <th><?= $t['parent'] ?></th>
          <th><?= $t['subcategories'] ?></th>
          <th><?= $t['products'] ?></th>
          <th><?= $t['sort_order'] ?></th>
          <th><?= $t['status'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($categories)): ?>
          <?php foreach ($categories as $category): ?>
            <tr class="<?= $category['parent_id'] ? 'subcategory' : '' ?>">
              <td>
                <div class="category-cell">
                  <!-- Category Image or Icon -->
                  <div class="category-visual <?= !empty($category['image']) ? '' : (!empty($category['icon']) ? 'icon' : 'default') ?>">
                    <?php if (!empty($category['image'])): ?>
                      <img 
                        src="<?= asset($category['image']) ?>" 
                        alt="<?= htmlspecialchars($category['name']) ?>"
                      >
                    <?php elseif (!empty($category['icon'])): ?>
                      <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                    <?php else: ?>
                      <i class="fas fa-folder"></i>
                    <?php endif; ?>
                  </div>
                  
                  <div class="category-info">
                    <div class="category-name">
                      <?php if ($category['parent_id']): ?>
                        <span class="subcategory-indicator">└─</span>
                      <?php endif; ?>
                      <?= htmlspecialchars($category['name']) ?>
                    </div>
                    <div class="category-slug">
                      <?= htmlspecialchars($category['slug']) ?>
                    </div>
                  </div>
                </div>
              </td>
              <td>
                <span class="parent-text">
                  <?= $category['parent_name'] ? htmlspecialchars($category['parent_name']) : $t['none'] ?>
                </span>
              </td>
              <td>
                <span class="count-text">
                  <?= $category['children_count'] ?>
                </span>
              </td>
              <td>
                <span class="count-text">
                  <?= $category['products_count'] ?>
                </span>
              </td>
              <td>
                <span class="order-text">
                  <?= $category['sort_order'] ?>
                </span>
              </td>
              <td>
                <span class="badge <?= $category['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $category['is_active'] ? $t['active'] : $t['inactive'] ?>
                </span>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <a 
                    href="<?= url('admin/categories/edit?id=' . $category['id']) ?>" 
                    class="action-btn edit"
                    title="<?= $t['edit'] ?>"
                  >
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= url('admin/categories/delete') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_delete'] ?>')">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $category['id'] ?>">
                    <button 
                      type="submit"
                      class="action-btn delete"
                      title="<?= $t['delete'] ?>"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-tags"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_categories_found'] ?></div>
                <div class="empty-state-text"><?= $t['create_first_category'] ?></div>
                <a href="<?= url('admin/categories/create') ?>" class="btn-primary">
                  <i class="fas fa-plus"></i> <?= $t['add_category'] ?>
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>