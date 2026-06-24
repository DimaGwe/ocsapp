<?php
/**
 * OCS Admin Products Management - WITH BULK UPLOAD BUTTON
 * File: app/Views/admin/products/index.php
 */

$pageTitle = 'Products Management';
$currentPage = 'products';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'products_management' => 'Products Management',
        'manage_catalog' => 'Manage your product catalog',
        'add_product' => 'Add Product',
        'bulk_upload' => 'Bulk Upload',
        'search' => 'Search',
        'search_placeholder' => 'Name, SKU, description...',
        'category' => 'Category',
        'all_categories' => 'All Categories',
        'brand' => 'Brand',
        'all_brands' => 'All Brands',
        'status' => 'Status',
        'all_status' => 'All Status',
        'active' => 'Active',
        'draft' => 'Draft',
        'inactive' => 'Inactive',
        'out_of_stock' => 'Out of Stock',
        'apply_filters' => 'Apply Filters',
        'reset' => 'Reset',
        'image' => 'Image',
        'product' => 'Product',
        'price' => 'Price',
        'stock' => 'Stock',
        'ocs_store' => 'OCS Store',
        'warehouse' => 'Warehouse',
        'quick_actions' => 'Quick Actions',
        'actions' => 'Actions',
        'featured' => 'Featured',
        'show_home' => 'Show Home',
        'sku' => 'SKU',
        'cost' => 'Cost',
        'no_reviews' => 'No reviews',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'no_products_found' => 'No products found',
        'create_first_product' => 'Create your first product to get started',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'results' => 'results',
        'previous' => 'Previous',
        'next' => 'Next',
        'confirm_delete' => 'Are you sure you want to delete this product?',
    ],
    'fr' => [
        'products_management' => 'Gestion Produits',
        'manage_catalog' => 'Gérer votre catalogue de produits',
        'add_product' => 'Ajouter Produit',
        'bulk_upload' => 'Import Groupé',
        'search' => 'Rechercher',
        'search_placeholder' => 'Nom, SKU, description...',
        'category' => 'Catégorie',
        'all_categories' => 'Toutes catégories',
        'brand' => 'Marque',
        'all_brands' => 'Toutes marques',
        'status' => 'Statut',
        'all_status' => 'Tous les statuts',
        'active' => 'Actif',
        'draft' => 'Brouillon',
        'inactive' => 'Inactif',
        'out_of_stock' => 'Épuisé',
        'apply_filters' => 'Appliquer',
        'reset' => 'Réinitialiser',
        'image' => 'Image',
        'product' => 'Produit',
        'price' => 'Prix',
        'stock' => 'Stock',
        'ocs_store' => 'Magasin OCS',
        'warehouse' => 'Entrepôt',
        'quick_actions' => 'Actions Rapides',
        'actions' => 'Actions',
        'featured' => 'En vedette',
        'show_home' => 'Page Accueil',
        'sku' => 'SKU',
        'cost' => 'Coût',
        'no_reviews' => 'Aucun avis',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'no_products_found' => 'Aucun produit trouvé',
        'create_first_product' => 'Créez votre premier produit pour commencer',
        'showing' => 'Affichage',
        'to' => 'à',
        'of' => 'sur',
        'results' => 'résultats',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'confirm_delete' => 'Êtes-vous sûr de vouloir supprimer ce produit?',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .products-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    gap: 24px;
  }

  .products-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .products-header-content p {
    font-size: 15px;
    color: var(--gray-600);
  }

  .header-buttons {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
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

  .btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: white;
    color: var(--gray-700);
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid var(--border);
    cursor: pointer;
    transition: all var(--transition-base);
    white-space: nowrap;
  }

  .btn-secondary:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
    transform: translateY(-1px);
  }

  /* Quick Action Buttons */
  .quick-actions {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
  }

  .quick-toggle-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 6px 12px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 12px;
    font-weight: 600;
    background: white;
    color: var(--gray-600);
    cursor: pointer;
    transition: all var(--transition-base);
    white-space: nowrap;
  }

  .quick-toggle-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    transform: translateY(-1px);
  }

  .quick-toggle-btn.active {
    background: var(--primary);
    border-color: var(--primary);
    color: white;
  }

  .quick-toggle-btn.featured.active {
    background: #fbbf24;
    border-color: #fbbf24;
    color: #78350f;
  }

  .quick-toggle-btn i {
    font-size: 11px;
  }

  .quick-toggle-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }

  /* Loading spinner */
  @keyframes spin {
    to { transform: rotate(360deg); }
  }

  .spinner {
    animation: spin 1s linear infinite;
  }

  /* Filters Card */
  .filters-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-input,
  .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .input-wrapper {
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

  .form-input.with-icon {
    padding-left: 40px;
  }

  .filters-actions {
    display: flex;
    gap: 12px;
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

  /* Product Image */
  .product-image {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    flex-shrink: 0;
  }

  .product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-image i {
    color: var(--gray-400);
    font-size: 18px;
  }

  /* Product Info */
  .product-info {
    min-width: 0;
  }

  .product-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .product-sku {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .brand-text {
    color: var(--gray-500);
    font-size: 13px;
  }

  /* Price */
  .price-cell {
    min-width: 0;
  }

  .price-main {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .price-cost {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  /* Stock Cell */
  .stock-cell {
    min-width: 140px;
  }

  .stock-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 4px;
    font-size: 12px;
  }

  .stock-row:last-child {
    margin-bottom: 0;
  }

  .stock-label {
    font-weight: 600;
    color: var(--gray-600);
    width: 70px;
    flex-shrink: 0;
  }

  .stock-value {
    font-weight: 700;
  }

  .stock-value.ocs {
    color: var(--primary);
  }

  .stock-value.warehouse {
    color: #3b82f6;
  }

  .stock-value.low {
    color: #f97316;
  }

  .stock-value.out {
    color: #ef4444;
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

  .badge.featured { background: #fef3c7; color: #92400e; }
  .badge.status-active { background: #dcfce7; color: #166534; }
  .badge.status-draft { background: var(--gray-200); color: var(--gray-700); }
  .badge.status-inactive { background: #fee2e2; color: #991b1b; }
  .badge.status-out_of_stock { background: #ffedd5; color: #c2410c; }

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
  @media (max-width: 1024px) {
    .filters-grid {
      grid-template-columns: 1fr 1fr;
    }

    .filters-grid .form-group:first-child {
      grid-column: 1 / -1;
    }
  }

  @media (max-width: 768px) {
    .products-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .header-buttons {
      width: 100%;
      flex-direction: column;
    }

    .header-buttons .btn-primary,
    .header-buttons .btn-secondary {
      width: 100%;
      justify-content: center;
    }

    .filters-grid {
      grid-template-columns: 1fr;
    }

    .filters-actions {
      flex-direction: column;
    }

    .filters-actions .btn-primary,
    .filters-actions .btn-secondary {
      width: 100%;
      justify-content: center;
    }

    th, td {
      padding: 12px 16px;
    }

    .product-image {
      width: 40px;
      height: 40px;
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
<div class="products-header">
  <div class="products-header-content">
    <h1><?= $t['products_management'] ?></h1>
    <p><?= $t['manage_catalog'] ?></p>
  </div>
  <div class="header-buttons">
    <a href="<?= url('admin/products/bulk-upload') ?>" class="btn-secondary">
      <i class="fas fa-file-upload"></i> <?= $t['bulk_upload'] ?>
    </a>
    <a href="<?= url('admin/products/create') ?>" class="btn-primary">
      <i class="fas fa-plus"></i> <?= $t['add_product'] ?>
    </a>
  </div>
</div>

<!-- Filters & Search -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/products') ?>">
    <div class="filters-grid">
      <!-- Search -->
      <div class="form-group">
        <label class="form-label"><?= $t['search'] ?></label>
        <div class="input-wrapper">
          <input 
            type="search" 
            name="search" 
            value="<?= htmlspecialchars($search ?? '') ?>"
            placeholder="<?= $t['search_placeholder'] ?>" 
            class="form-input with-icon"
          >
          <i class="fas fa-search input-icon"></i>
        </div>
      </div>

      <!-- Category Filter -->
      <div class="form-group">
        <label class="form-label"><?= $t['category'] ?></label>
        <select name="category" class="form-select">
          <option value=""><?= $t['all_categories'] ?></option>
          <?php foreach ($categories ?? [] as $category): ?>
            <option value="<?= $category['id'] ?>" <?= ($categoryFilter ?? '') == $category['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Brand Filter -->
      <div class="form-group">
        <label class="form-label"><?= $t['brand'] ?></label>
        <select name="brand" class="form-select">
          <option value=""><?= $t['all_brands'] ?></option>
          <?php foreach ($brands ?? [] as $brand): ?>
            <option value="<?= $brand['id'] ?>" <?= ($brandFilter ?? '') == $brand['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($brand['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Filter -->
      <div class="form-group">
        <label class="form-label"><?= $t['status'] ?></label>
        <select name="status" class="form-select">
          <option value=""><?= $t['all_status'] ?></option>
          <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>><?= $t['active'] ?></option>
          <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>><?= $t['draft'] ?></option>
          <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>><?= $t['inactive'] ?></option>
          <option value="out_of_stock" <?= ($statusFilter ?? '') === 'out_of_stock' ? 'selected' : '' ?>><?= $t['out_of_stock'] ?></option>
        </select>
      </div>
    </div>

    <!-- Actions -->
    <div class="filters-actions">
      <button type="submit" class="btn-primary">
        <i class="fas fa-filter"></i> <?= $t['apply_filters'] ?>
      </button>
      <a href="<?= url('admin/products') ?>" class="btn-secondary">
        <i class="fas fa-redo"></i> <?= $t['reset'] ?>
      </a>
    </div>
  </form>
</div>

<!-- OCS Warehouse Products Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th style="width: 80px;"><?= $t['image'] ?></th>
          <th><?= $t['product'] ?></th>
          <th><?= $t['brand'] ?></th>
          <th><?= $t['price'] ?></th>
          <th><?= $t['stock'] ?></th>
          <th><?= $t['quick_actions'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($globalProducts)): ?>
          <?php foreach ($globalProducts as $product): ?>
            <tr>
              <td>
                <div class="product-image">
                  <?php if (!empty($product['primary_image'])): ?>
                    <img src="<?= asset($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                  <?php else: ?>
                    <i class="fas fa-image"></i>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="product-info">
                  <div class="product-name">
                    <?= htmlspecialchars($product['name']) ?>
                  </div>
                  <div class="product-sku">
                    <?= $t['sku'] ?>: <?= htmlspecialchars($product['sku'] ?: 'N/A') ?>
                  </div>
                </div>
              </td>
              <td>
                <span class="brand-text">
                  <?= htmlspecialchars($product['brand_name'] ?: 'N/A') ?>
                </span>
              </td>
              <td>
                <div class="price-cell">
                  <div class="price-main">
                    <?= currency($product['base_price']) ?>
                  </div>
                </div>
              </td>
              <td>
                <?php
                // Get OCS Store stock from shop_inventory
                $db = \Database::getConnection();
                $stmt = $db->prepare("SELECT stock_quantity FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1");
                $stmt->execute([$product['id']]);
                $ocsStock = $stmt->fetchColumn() ?: 0;

                $warehouseStock = (int)($product['available_stock'] ?? 0);
                $totalStock = (int)($product['total_stock'] ?? 0);

                // Determine stock status
                $stockClass = 'ocs';
                if ($ocsStock == 0) {
                  $stockClass = 'out';
                } elseif ($ocsStock < 10) {
                  $stockClass = 'low';
                }
                ?>
                <div class="stock-cell">
                  <div class="stock-row">
                    <span class="stock-label"><?= $t['ocs_store'] ?>:</span>
                    <span class="stock-value <?= $stockClass ?>"><?= number_format($ocsStock) ?></span>
                  </div>
                  <div class="stock-row">
                    <span class="stock-label"><?= $t['warehouse'] ?>:</span>
                    <span class="stock-value warehouse"><?= number_format($warehouseStock) ?></span>
                  </div>
                </div>
              </td>
              <td>
                <!-- QUICK ACTION TOGGLES -->
                <div class="quick-actions">
                  <button 
                    onclick="toggleFeature(<?= $product['id'] ?>, 'is_featured', this)"
                    class="quick-toggle-btn featured <?= !empty($product['is_featured']) ? 'active' : '' ?>"
                    title="Toggle Featured"
                    data-product-id="<?= $product['id'] ?>"
                    data-field="is_featured"
                  >
                    <i class="fas fa-star"></i>
                    <span><?= $t['featured'] ?></span>
                  </button>
                  
                  <button 
                    onclick="toggleFeature(<?= $product['id'] ?>, 'show_on_home', this)"
                    class="quick-toggle-btn <?= !empty($product['show_on_home']) ? 'active' : '' ?>"
                    title="Toggle Show on Homepage"
                    data-product-id="<?= $product['id'] ?>"
                    data-field="show_on_home"
                  >
                    <i class="fas fa-home"></i>
                    <span><?= $t['show_home'] ?></span>
                  </button>
                </div>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <a 
                    href="<?= url('admin/products/edit?id=' . $product['id']) ?>" 
                    class="action-btn edit"
                    title="<?= $t['edit'] ?>"
                  >
                    <i class="fas fa-edit"></i>
                  </a>
                  <form method="POST" action="<?= url('admin/products/delete') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_delete'] ?>')">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= $product['id'] ?>">
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
            <td colspan="8">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-box"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_products_found'] ?></div>
                <div class="empty-state-text"><?= $t['create_first_product'] ?></div>
                <a href="<?= url('admin/products/create') ?>" class="btn-primary">
                  <i class="fas fa-plus"></i> <?= $t['add_product'] ?>
                </a>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if (!empty($globalProducts) && ($total ?? 0) > ($perPage ?? 20)): ?>
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
        $queryParams = http_build_query(array_filter([
            'search' => $search ?? '',
            'category' => $categoryFilter ?? '',
            'brand' => $brandFilter ?? '',
            'status' => $statusFilter ?? '',
        ]));
        ?>
        
        <!-- Previous -->
        <?php if ($page > 1): ?>
          <a 
            href="<?= url('admin/products?page=' . ($page - 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <i class="fas fa-chevron-left"></i> <?= $t['previous'] ?>
          </a>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a 
            href="<?= url('admin/products?page=' . $i . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($page < $totalPages): ?>
          <a 
            href="<?= url('admin/products?page=' . ($page + 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <?= $t['next'] ?> <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>



  <!-- Seller Products Table -->
  <?php if (!empty($sellerProducts)): ?>
  <div class="table-card" style="margin-top: 32px;">
    <div style="padding: 20px 24px; background: #fff3cd; border-bottom: 2px solid #ffc107;">
      <h2 style="margin: 0; color: #333; font-size: 18px;">
        <i class="fas fa-store"></i> Seller Products
        <span style="color: #666; font-size: 14px; font-weight: normal; margin-left: 10px;">
          (<?= $totalSeller ?? 0 ?> products - managed by sellers)
        </span>
      </h2>
      <p style="margin: 8px 0 0 0; font-size: 13px; color: #856404;">
        These products are created and managed by individual sellers in their shops.
      </p>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th style="width: 80px;">Image</th>
            <th>Product</th>
            <th>Shop</th>
            <th>Seller</th>
            <th>Price</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sellerProducts as $product): ?>
            <tr>
              <td>
                <div class="product-image">
                  <?php if (!empty($product['primary_image'])): ?>
                    <img src="<?= asset($product['primary_image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                  <?php else: ?>
                    <i class="fas fa-image"></i>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="product-info">
                  <div class="product-name">
                    <?= htmlspecialchars($product['name']) ?>
                  </div>
                  <div class="product-sku">
                    SKU: <?= htmlspecialchars($product['sku'] ?: 'N/A') ?>
                  </div>
                </div>
              </td>
              <td>
                <strong style="color: #4CAF50;"><?= htmlspecialchars($product['shop_name'] ?? 'N/A') ?></strong>
              </td>
              <td>
                <span style="font-size: 13px; color: #666;">
                  <?= htmlspecialchars($product['seller_email'] ?? 'N/A') ?>
                </span>
              </td>
              <td>
                <div class="price-cell">
                  <div class="price-main">
                    <?= currency($product['base_price']) ?>
                  </div>
                </div>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <a href="<?= url('admin/products/edit?id=' . $product['id']) ?>" class="action-btn action-view" title="View Details">
                    <i class="fas fa-eye"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
<script>
// Get CSRF token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

/**
 * Toggle product feature (featured, show_on_home, etc.)
 */
async function toggleFeature(productId, field, buttonElement) {
  // Prevent double-click
  if (buttonElement.disabled) return;
  buttonElement.disabled = true;
  
  // Show loading state
  const originalHTML = buttonElement.innerHTML;
  buttonElement.innerHTML = '<i class="fas fa-spinner spinner"></i> <span>...</span>';
  
  try {
    const formData = new URLSearchParams();
    formData.append(csrfName, csrfToken);
    formData.append('product_id', productId);
    formData.append('field', field);
    
    const response = await fetch('<?= url('admin/products/toggle-feature') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Toggle active state
      buttonElement.classList.toggle('active');
      
      // Restore button
      buttonElement.innerHTML = originalHTML;
      buttonElement.disabled = false;
      
      // Optional: Show success message
      console.log(`✅ ${field} toggled successfully`);
    } else {
      throw new Error(data.message || 'Toggle failed');
    }
  } catch (error) {
    console.error('Toggle error:', error);
    alert('Failed to update product: ' + error.message);
    
    // Restore button
    buttonElement.innerHTML = originalHTML;
    buttonElement.disabled = false;
  }
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>