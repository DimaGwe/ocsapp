<?php
/**
 * OCS Admin Brands Management
 * File: app/Views/admin/brands/index.php
 */

$pageTitle = 'Brands';
$currentPage = 'brands';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'brands' => 'Brands',
        'manage_brands' => 'Manage product brands',
        'add_brand' => 'Add Brand',
        'search' => 'Search',
        'search_placeholder' => 'Search brands...',
        'reset' => 'Reset',
        'brand' => 'Brand',
        'website' => 'Website',
        'products' => 'Products',
        'sort_order' => 'Sort Order',
        'status' => 'Status',
        'actions' => 'Actions',
        'visit' => 'Visit',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_brands_found' => 'No brands found',
        'create_first_brand' => 'Create your first brand to get started',
        'confirm_delete' => 'Are you sure you want to delete this brand?',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
        'previous' => 'Previous',
        'next' => 'Next',
        'none' => '—',
    ],
    'fr' => [
        'brands' => 'Marques',
        'manage_brands' => 'Gérer les marques de produits',
        'add_brand' => 'Ajouter Marque',
        'search' => 'Rechercher',
        'search_placeholder' => 'Rechercher marques...',
        'reset' => 'Réinitialiser',
        'brand' => 'Marque',
        'website' => 'Site web',
        'products' => 'Produits',
        'sort_order' => 'Ordre',
        'status' => 'Statut',
        'actions' => 'Actions',
        'visit' => 'Visiter',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'no_brands_found' => 'Aucune marque trouvée',
        'create_first_brand' => 'Créez votre première marque pour commencer',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer cette marque?',
        'showing' => 'Affichage',
        'to' => 'à',
        'of' => 'sur',
        'results' => 'résultats',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'none' => '—',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .brands-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    gap: 24px;
  }

  .brands-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .brands-header-content p {
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

  /* Search Card */
  .search-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .search-form {
    display: flex;
    gap: 12px;
  }

  .search-wrapper {
    flex: 1;
    position: relative;
  }

  .search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 14px;
  }

  .btn-search {
    padding: 10px 24px;
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
    white-space: nowrap;
  }

  .btn-search:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-reset {
    padding: 10px 20px;
    background: var(--gray-200);
    color: var(--gray-700);
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

  .btn-reset:hover {
    background: var(--gray-300);
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

  /* Brand Cell */
  .brand-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .brand-logo {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .brand-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .brand-logo i {
    color: var(--gray-400);
    font-size: 18px;
  }

  .brand-info {
    min-width: 0;
  }

  .brand-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .brand-slug {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .website-link {
    color: var(--primary);
    text-decoration: none;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color var(--transition-base);
  }

  .website-link:hover {
    color: var(--primary-600);
    text-decoration: underline;
  }

  .website-link i {
    font-size: 12px;
  }

  .none-text {
    color: var(--gray-400);
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
    text-decoration: none;
  }

  a.action-btn.edit,
  a.action-btn.edit i { 
    color: var(--primary); 
  }
  
  a.action-btn.edit:hover,
  a.action-btn.edit:hover i { 
    color: var(--primary-600); 
  }

  .action-btn.delete,
  .action-btn.delete i { 
    color: #ef4444; 
  }
  
  .action-btn.delete:hover,
  .action-btn.delete:hover i { 
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

  /* Pagination */
  .pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: var(--gray-50);
    border-top: 1px solid var(--border);
  }

  .pagination-info {
    font-size: 14px;
    color: var(--gray-700);
  }

  .pagination-info span {
    font-weight: 600;
  }

  .pagination-buttons {
    display: flex;
    gap: 8px;
  }

  .pagination-btn {
    padding: 8px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    background: white;
    text-decoration: none;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 4px;
  }

  .pagination-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
  }

  .pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
  }

  .pagination-btn.active:hover {
    background: var(--primary-600);
    border-color: var(--primary-600);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .brands-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .btn-primary {
      width: 100%;
      justify-content: center;
    }

    .search-form {
      flex-direction: column;
    }

    .btn-search,
    .btn-reset {
      width: 100%;
      justify-content: center;
    }

    th, td {
      padding: 12px 16px;
    }

    .brand-logo {
      width: 36px;
      height: 36px;
    }

    .pagination {
      flex-direction: column;
      gap: 16px;
    }

    .pagination-buttons {
      flex-wrap: wrap;
      justify-content: center;
    }
  }
</style>

<!-- Page Header -->
<div class="brands-header">
  <div class="brands-header-content">
    <h1><?= $t['brands'] ?></h1>
    <p><?= $t['manage_brands'] ?></p>
  </div>
  <a href="<?= url('admin/brands/create') ?>" class="btn-primary">
    <i class="fas fa-plus"></i> <?= $t['add_brand'] ?>
  </a>
</div>

<!-- Search -->
<div class="search-card">
  <form method="GET" action="<?= url('admin/brands') ?>" class="search-form">
    <div class="search-wrapper">
      <input 
        type="search" 
        name="search" 
        value="<?= htmlspecialchars($search ?? '') ?>"
        placeholder="<?= $t['search_placeholder'] ?>" 
        class="search-input"
      >
      <i class="fas fa-search search-icon"></i>
    </div>
    <button type="submit" class="btn-search">
      <i class="fas fa-filter"></i> <?= $t['search'] ?>
    </button>
    <a href="<?= url('admin/brands') ?>" class="btn-reset">
      <i class="fas fa-redo"></i> <?= $t['reset'] ?>
    </a>
  </form>
</div>

<!-- Brands Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['brand'] ?></th>
          <th><?= $t['website'] ?></th>
          <th><?= $t['products'] ?></th>
          <th><?= $t['sort_order'] ?></th>
          <th><?= $t['status'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($brands)): ?>
          <?php foreach ($brands as $brand): ?>
            <tr>
              <td>
                <div class="brand-cell">
                  <div class="brand-logo">
                    <?php if (!empty($brand['logo'])): ?>
                      <img src="<?= asset($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
                    <?php else: ?>
                      <i class="fas fa-trademark"></i>
                    <?php endif; ?>
                  </div>
                  <div class="brand-info">
                    <div class="brand-name"><?= htmlspecialchars($brand['name']) ?></div>
                    <div class="brand-slug"><?= htmlspecialchars($brand['slug']) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <?php if (!empty($brand['website'])): ?>
                  <a href="<?= htmlspecialchars($brand['website']) ?>" target="_blank" rel="noopener" class="website-link">
                    <i class="fas fa-external-link-alt"></i> <?= $t['visit'] ?>
                  </a>
                <?php else: ?>
                  <span class="none-text"><?= $t['none'] ?></span>
                <?php endif; ?>
              </td>
              <td>
                <span class="count-text"><?= $brand['products_count'] ?></span>
              </td>
              <td>
                <span class="order-text"><?= $brand['sort_order'] ?></span>
              </td>
              <td>
                <span class="badge <?= $brand['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $brand['is_active'] ? $t['active'] : $t['inactive'] ?>
                </span>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <a 
                    href="<?= url('admin/brands/edit?id=' . $brand['id']) ?>" 
                    class="action-btn edit"
                    title="<?= $t['edit'] ?>"
                  >
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= url('admin/brands/delete') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_delete'] ?>')">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $brand['id'] ?>">
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
            <td colspan="6">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-trademark"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_brands_found'] ?></div>
                <div class="empty-state-text"><?= $t['create_first_brand'] ?></div>
                <a href="<?= url('admin/brands/create') ?>" class="btn-primary">
                  <i class="fas fa-plus"></i> <?= $t['add_brand'] ?>
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if (!empty($brands) && ($total ?? 0) > ($perPage ?? 20)): ?>
    <div class="pagination">
      <div class="pagination-info">
        <?= $t['showing'] ?>
        <span><?= (($page - 1) * $perPage) + 1 ?></span>
        <?= $t['to'] ?>
        <span><?= min($page * $perPage, $total) ?></span>
        <?= $t['of'] ?>
        <span><?= number_format($total) ?></span>
        <?= $t['results'] ?>
      </div>
      
      <div class="pagination-buttons">
        <?php
        $totalPages = ceil($total / $perPage);
        $queryParams = $search ? 'search=' . urlencode($search) : '';
        ?>
        
        <!-- Previous -->
        <?php if ($page > 1): ?>
          <a 
            href="<?= url('admin/brands?page=' . ($page - 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <i class="fas fa-chevron-left"></i> <?= $t['previous'] ?>
          </a>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a 
            href="<?= url('admin/brands?page=' . $i . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($page < $totalPages): ?>
          <a 
            href="<?= url('admin/brands?page=' . ($page + 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <?= $t['next'] ?> <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>