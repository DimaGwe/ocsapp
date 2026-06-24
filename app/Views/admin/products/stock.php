<?php
/**
 * OCS Admin Stock Management - WITH ALLOCATION TRACKING
 * File: app/Views/admin/products/stock.php
 */

$pageTitle = 'Stock Management';
$currentPage = 'stock';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'stock_management' => 'Stock Management',
        'manage_inventory_levels' => 'Manage OCS Store inventory (auto-synced) and track seller allocations',
        'export_csv' => 'Export CSV',
        'restock' => 'Restock Products',
        'out_of_stock' => 'Out of Stock',
        'products_out_of_stock' => 'product(s) are out of stock',
        'low_stock' => 'Low Stock',
        'products_low_stock' => 'product(s) are running low',
        'highly_allocated' => 'Highly Allocated',
        'products_highly_allocated' => 'product(s) have most stock allocated',
        'search' => 'Search',
        'search_placeholder' => 'Search by name, SKU...',
        'stock_status' => 'Stock Status',
        'all_products' => 'All Products',
        'out_of_stock_status' => 'Out of Stock',
        'low_stock_status' => 'Low Stock',
        'in_stock' => 'In Stock',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'product' => 'Product',
        'sku' => 'SKU',
        'warehouse_stock' => 'Stock Distribution',
        'allocated' => 'Allocated',
        'available' => 'Available',
        'status' => 'Status',
        'actions' => 'Actions',
        'total' => 'Total',
        'ocs_store' => 'OCS Store',
        'for_sellers' => 'For Sellers',
        'to_sellers' => 'To Sellers',
        'for_allocation' => 'For Allocation',
        'restock_product' => 'Restock',
        'view_allocations' => 'View Allocations',
        'movements' => 'Movements',
        'edit_product' => 'Edit Product',
        'no_products_found' => 'No products found',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'products_text' => 'products',
        'previous' => 'Previous',
        'next' => 'Next',
        'add_stock_to_warehouse' => 'Add Stock to Warehouse',
        'current_warehouse_stock' => 'Current Warehouse Stock',
        'quantity_to_add' => 'Quantity to Add',
        'notes' => 'Notes',
        'cancel' => 'Cancel',
        'add_to_warehouse' => 'Add to Warehouse',
        'update_success' => 'Stock updated successfully',
        'update_error' => 'An error occurred while updating stock',
        'export_success' => 'Stock exported successfully',
        'units' => 'units',
        'view_details' => 'View Details',
    ],
    'es' => [
        'stock_management' => 'Gestión de Stock',
        'manage_inventory_levels' => 'Gestionar inventario y rastrear asignaciones a vendedores',
        'export_csv' => 'Exportar CSV',
        'restock' => 'Reabastecer',
        'out_of_stock' => 'Agotado',
        'products_out_of_stock' => 'producto(s) agotados',
        'low_stock' => 'Stock Bajo',
        'products_low_stock' => 'producto(s) con stock bajo',
        'highly_allocated' => 'Altamente Asignado',
        'products_highly_allocated' => 'producto(s) con mayor asignación',
        'search' => 'Buscar',
        'search_placeholder' => 'Buscar por nombre, SKU...',
        'stock_status' => 'Estado Stock',
        'all_products' => 'Todos',
        'out_of_stock_status' => 'Agotado',
        'low_stock_status' => 'Stock Bajo',
        'in_stock' => 'En Stock',
        'filter' => 'Filtrar',
        'reset' => 'Restablecer',
        'product' => 'Producto',
        'sku' => 'SKU',
        'warehouse_stock' => 'Distribución Stock',
        'allocated' => 'Asignado',
        'available' => 'Disponible',
        'status' => 'Estado',
        'actions' => 'Acciones',
        'total' => 'Total',
        'ocs_store' => 'Tienda OCS',
        'for_sellers' => 'Para Vendedores',
        'to_sellers' => 'A Vendedores',
        'for_allocation' => 'Para Asignar',
        'restock_product' => 'Reabastecer',
        'view_allocations' => 'Ver Asignaciones',
        'movements' => 'Movimientos',
        'edit_product' => 'Editar',
        'no_products_found' => 'No se encontraron productos',
        'showing' => 'Mostrando',
        'to' => 'a',
        'of' => 'de',
        'products_text' => 'productos',
        'previous' => 'Anterior',
        'next' => 'Siguiente',
        'add_stock_to_warehouse' => 'Agregar al Almacén',
        'current_warehouse_stock' => 'Stock Actual',
        'quantity_to_add' => 'Cantidad a Agregar',
        'notes' => 'Notas',
        'cancel' => 'Cancelar',
        'add_to_warehouse' => 'Agregar',
        'update_success' => 'Stock actualizado exitosamente',
        'update_error' => 'Error al actualizar stock',
        'export_success' => 'Stock exportado exitosamente',
        'units' => 'unidades',
        'view_details' => 'Ver Detalles',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

// Calculate stock alerts - BASED ON OCS STORE STOCK (Global Products Only)
$outOfStock = 0;
$lowStock = 0;
$highlyAllocated = 0;

if (!empty($globalProducts) && is_array($globalProducts)) {
    $db = \Database::getConnection();

    foreach ($globalProducts as $product) {
        $totalStock = isset($product['total_stock']) ? (int)$product['total_stock'] : 0;
        $availableStock = isset($product['available_stock']) ? (int)$product['available_stock'] : 0;
        $lowStockThreshold = isset($product['low_stock_threshold']) ? (int)$product['low_stock_threshold'] : 10;

        // Get OCS Store stock
        $stmt = $db->prepare("SELECT stock_quantity FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1");
        $stmt->execute([$product['id']]);
        $ocsStock = (int)($stmt->fetchColumn() ?: 0);

        $trackInventory = isset($product['track_inventory']) ? (bool)$product['track_inventory'] : true;

        if ($trackInventory) {
            // Out of stock if OCS Store has no stock
            if ($ocsStock == 0) {
                $outOfStock++;
            }
            // Low stock if OCS Store is below threshold
            elseif ($ocsStock > 0 && $ocsStock <= $lowStockThreshold) {
                $lowStock++;
            }

            // Highly allocated if most stock is in OCS Store (little available for sellers)
            if ($totalStock > 0 && $ocsStock > 0 && ($ocsStock / $totalStock) > 0.8) {
                $highlyAllocated++;
            }
        }
    }
}

ob_start();
?>

<style>
  /* Stock Management Page */
  .stock-page {
    max-width: 1600px;
    margin: 0 auto;
  }

  /* Page Header */
  .stock-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 32px;
    gap: 24px;
  }

  .stock-header-content h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .stock-header-content p {
    font-size: 15px;
    color: var(--gray-600);
  }

  .header-actions {
    display: flex;
    gap: 12px;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
  }

  .btn-export {
    background: var(--primary);
    color: white;
  }

  .btn-export:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  .btn-restock {
    background: #3b82f6;
    color: white;
  }

  .btn-restock:hover {
    background: #2563eb;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Stock Alerts - UPDATED WITH ALLOCATION ALERT */
  .alerts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .alert-card {
    padding: 20px;
    border-radius: var(--radius-xl);
    border-left: 4px solid;
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .alert-card.danger {
    background: #fef2f2;
    border-color: #ef4444;
  }

  .alert-card.warning {
    background: #fefce8;
    border-color: #eab308;
  }

  .alert-card.info {
    background: #eff6ff;
    border-color: #3b82f6;
  }

  .alert-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
  }

  .alert-card.danger .alert-icon {
    background: #fee2e2;
    color: #ef4444;
  }

  .alert-card.warning .alert-icon {
    background: #fef3c7;
    color: #eab308;
  }

  .alert-card.info .alert-icon {
    background: #dbeafe;
    color: #3b82f6;
  }

  .alert-content h3 {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 4px;
  }

  .alert-card.danger h3 { color: #991b1b; }
  .alert-card.warning h3 { color: #854d0e; }
  .alert-card.info h3 { color: #1e40af; }
  .alert-card.danger p { color: #b91c1c; }
  .alert-card.warning p { color: #a16207; }
  .alert-card.info p { color: #2563eb; }

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
    grid-template-columns: 2fr 1fr;
    gap: 16px;
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

  .filters-actions {
    display: flex;
    gap: 12px;
    margin-top: 16px;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
  }

  .btn-filter:hover {
    background: var(--primary-600);
  }

  .btn-reset {
    padding: 10px 24px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
  }

  .btn-reset:hover {
    background: var(--gray-50);
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
    padding: 12px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    white-space: nowrap;
  }

  th.text-center { text-align: center; }
  th.text-right { text-align: right; }

  td {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    font-size: 14px;
    vertical-align: middle;
  }

  td.text-center { text-align: center; }
  td.text-right { text-align: right; }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  /* Product Cell */
  .product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

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

  .product-info { min-width: 0; }

  .product-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .product-price {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .sku-text {
    color: var(--gray-600);
    font-size: 13px;
  }

  /* Stock Columns - NEW */
  .stock-breakdown {
    display: flex;
    flex-direction: column;
    gap: 4px;
  }

  .stock-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
  }

  .stock-label {
    color: var(--gray-500);
    text-transform: uppercase;
    font-size: 10px;
    letter-spacing: 0.05em;
  }

  .stock-value {
    font-weight: 700;
    font-size: 14px;
  }

  .stock-value.total { color: var(--gray-600); }
  .stock-value.allocated { color: var(--primary); }
  .stock-value.available { color: #3b82f6; }
  .stock-value.low { color: #ef4444; }

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

  .badge.available { background: #dcfce7; color: #166534; }
  .badge.low-available { background: #fef3c7; color: #92400e; }
  .badge.no-available { background: #fee2e2; color: #991b1b; }
  .badge.unlimited { background: #dbeafe; color: #1e40af; }

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
    transition: color var(--transition-base);
    padding: 6px;
    text-decoration: none;
  }

  .action-btn.restock { color: #3b82f6; }
  .action-btn.restock:hover { color: #2563eb; }
  .action-btn.allocations { color: #8b5cf6; }
  .action-btn.allocations:hover { color: #7c3aed; }
  .action-btn.movements { color: var(--primary); }
  .action-btn.movements:hover { color: var(--primary-600); }
  .action-btn.edit { color: var(--gray-600); }
  .action-btn.edit:hover { color: var(--dark); }

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
    color: var(--gray-500);
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

  /* Modal */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 50;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .modal-overlay.active {
    display: flex;
  }

  .modal-content {
    background: white;
    border-radius: var(--radius-xl);
    max-width: 500px;
    width: 100%;
    box-shadow: var(--shadow-lg);
  }

  .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .modal-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
  }

  .modal-close {
    background: none;
    border: none;
    color: var(--gray-400);
    font-size: 20px;
    cursor: pointer;
    transition: color var(--transition-base);
    padding: 4px;
  }

  .modal-close:hover {
    color: var(--gray-600);
  }

  .modal-body {
    padding: 24px;
  }

  .modal-field {
    margin-bottom: 20px;
  }

  .modal-field label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 8px;
    display: block;
  }

  .modal-field .value {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
  }

  .modal-field .value.large {
    font-size: 28px;
    font-weight: 700;
    color: var(--primary);
  }

  .modal-field textarea {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    min-height: 80px;
    resize: vertical;
  }

  .modal-actions {
    display: flex;
    gap: 12px;
  }

  .btn-secondary {
    flex: 1;
    padding: 10px 24px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
  }

  .btn-secondary:hover {
    background: var(--gray-50);
  }

  .btn-primary {
    flex: 1;
    padding: 10px 24px;
    background: var(--primary);
    color: white;
  }

  .btn-primary:hover {
    background: var(--primary-600);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .stock-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .header-actions {
      width: 100%;
    }

    .btn-export,
    .btn-restock {
      flex: 1;
      justify-content: center;
    }

    .filters-grid {
      grid-template-columns: 1fr;
    }

    .alerts-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="stock-page">
  <!-- Page Header -->
  <div class="stock-header">
    <div class="stock-header-content">
      <h1><?= $t['stock_management'] ?></h1>
      <p><?= $t['manage_inventory_levels'] ?></p>
    </div>
    <div class="header-actions">
      <button onclick="exportStock()" class="btn btn-export">
        <i class="fas fa-download"></i> <?= $t['export_csv'] ?>
      </button>
    </div>
  </div>

  <!-- Stock Alerts - WITH ALLOCATION ALERT -->
  <?php if ($outOfStock > 0 || $lowStock > 0 || $highlyAllocated > 0): ?>
    <div class="alerts-grid">
      <?php if ($outOfStock > 0): ?>
        <div class="alert-card danger">
          <div class="alert-icon">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <div class="alert-content">
            <h3><?= $t['out_of_stock'] ?></h3>
            <p><?= $outOfStock ?> <?= $t['products_out_of_stock'] ?></p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($lowStock > 0): ?>
        <div class="alert-card warning">
          <div class="alert-icon">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="alert-content">
            <h3><?= $t['low_stock'] ?></h3>
            <p><?= $lowStock ?> <?= $t['products_low_stock'] ?></p>
          </div>
        </div>
      <?php endif; ?>

      <?php if ($highlyAllocated > 0): ?>
        <div class="alert-card info">
          <div class="alert-icon">
            <i class="fas fa-info-circle"></i>
          </div>
          <div class="alert-content">
            <h3><?= $t['highly_allocated'] ?></h3>
            <p><?= $highlyAllocated ?> <?= $t['products_highly_allocated'] ?></p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <!-- Filters -->
  <div class="filters-card">
    <form method="GET" action="<?= url('admin/products/stock') ?>">
      <div class="filters-grid">
        <!-- Search -->
        <div class="form-group">
          <label class="form-label"><?= $t['search'] ?></label>
          <input 
            type="text" 
            name="search" 
            value="<?= htmlspecialchars($search ?? '') ?>"
            placeholder="<?= $t['search_placeholder'] ?>" 
            class="form-input"
          >
        </div>

        <!-- Stock Status Filter -->
        <div class="form-group">
          <label class="form-label"><?= $t['stock_status'] ?></label>
          <select name="stock_status" class="form-select">
            <option value=""><?= $t['all_products'] ?></option>
            <option value="out_of_stock" <?= ($stockStatus ?? '') === 'out_of_stock' ? 'selected' : '' ?>><?= $t['out_of_stock_status'] ?></option>
            <option value="low_stock" <?= ($stockStatus ?? '') === 'low_stock' ? 'selected' : '' ?>><?= $t['low_stock_status'] ?></option>
            <option value="in_stock" <?= ($stockStatus ?? '') === 'in_stock' ? 'selected' : '' ?>><?= $t['in_stock'] ?></option>
          </select>
        </div>
      </div>

      <!-- Actions -->
      <div class="filters-actions">
        <button type="submit" class="btn btn-filter">
          <i class="fas fa-filter"></i> <?= $t['filter'] ?>
        </button>
        <a href="<?= url('admin/products/stock') ?>" class="btn btn-reset">
          <i class="fas fa-redo"></i> <?= $t['reset'] ?>
        </a>
      </div>
    </form>
  </div>

  <!-- OCS Warehouse Products Table -->
  <div class="table-card">
    <div style="padding: 20px 24px; background: #f8f9fa; border-bottom: 2px solid #4CAF50;">
      <h2 style="margin: 0; color: #333; font-size: 18px;">
        <i class="fas fa-warehouse"></i> OCS Warehouse Products
        <span style="color: #666; font-size: 14px; font-weight: normal; margin-left: 10px;">
          (<?= $totalGlobal ?? 0 ?> products)
        </span>
      </h2>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th><?= $t['product'] ?></th>
            <th class="text-center"><?= $t['sku'] ?></th>
            <th class="text-center"><?= $t['warehouse_stock'] ?></th>
            <th class="text-center"><?= $t['status'] ?></th>
            <th class="text-right"><?= $t['actions'] ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($globalProducts)): ?>
            <?php foreach ($globalProducts as $product): ?>
              <?php
              // Get stock values
              $totalStock = isset($product['total_stock']) ? (int)$product['total_stock'] : 0;
              $allocatedStock = isset($product['allocated_stock']) ? (int)$product['allocated_stock'] : 0;
              $availableStock = isset($product['available_stock']) ? (int)$product['available_stock'] : 0;

              // Get OCS Store stock
              $db = \Database::getConnection();
              $stmt = $db->prepare("SELECT stock_quantity FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1");
              $stmt->execute([$product['id']]);
              $ocsStockCheck = (int)($stmt->fetchColumn() ?: 0);

              $trackInventory = isset($product['track_inventory']) ? (bool)$product['track_inventory'] : true;

              // Determine status badge based on OCS Store stock
              if (!$trackInventory) {
                  $statusBadge = 'unlimited';
                  $statusText = $t['unlimited'] ?? 'Unlimited';
              } elseif ($ocsStockCheck == 0) {
                  $statusBadge = 'no-available';
                  $statusText = $t['out_of_stock'];
              } elseif ($ocsStockCheck <= 10) {
                  $statusBadge = 'low-available';
                  $statusText = $t['low_stock'];
              } else {
                  $statusBadge = 'available';
                  $statusText = $t['in_stock'];
              }

              // Handle product image
              $imagePath = null;
              if (!empty($product['primary_image'])) {
                  $imagePath = $product['primary_image'];
              } elseif (!empty($product['images'])) {
                  $images = json_decode($product['images'], true);
                  if (is_array($images) && !empty($images)) {
                      $imagePath = $images[0]['image_path'] ?? null;
                  }
              }
              ?>
              <tr>
                <td>
                  <div class="product-cell">
                    <div class="product-image">
                      <?php if ($imagePath): ?>
                        <img src="<?= asset($imagePath) ?>" alt="Product">
                      <?php else: ?>
                        <i class="fas fa-box"></i>
                      <?php endif; ?>
                    </div>
                    <div class="product-info">
                      <div class="product-name"><?= htmlspecialchars($product['name'] ?? 'N/A') ?></div>
                      <div class="product-price"><?= currency($product['base_price'] ?? 0) ?></div>
                    </div>
                  </div>
                </td>

                <td class="text-center">
                  <span class="sku-text"><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
                </td>

                <td class="text-center">
                  <?php if ($trackInventory): ?>
                    <?php
                    // Get OCS Store stock from shop_inventory
                    $db = \Database::getConnection();
                    $stmt = $db->prepare("SELECT stock_quantity FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1");
                    $stmt->execute([$product['id']]);
                    $ocsStock = (int)($stmt->fetchColumn() ?: 0);
                    ?>
                    <div class="stock-breakdown">
                      <div class="stock-row">
                        <span class="stock-label"><?= $t['ocs_store'] ?>:</span>
                        <span class="stock-value allocated"><?= number_format($ocsStock) ?></span>
                      </div>
                      <div class="stock-row">
                        <span class="stock-label"><?= $t['for_sellers'] ?>:</span>
                        <span class="stock-value <?= $availableStock <= 10 && $availableStock > 0 ? 'low' : ($availableStock == 0 ? 'low' : 'available') ?>">
                          <?= number_format($availableStock) ?>
                        </span>
                      </div>
                      <div class="stock-row">
                        <span class="stock-label"><?= $t['total'] ?>:</span>
                        <span class="stock-value total"><?= number_format($totalStock) ?></span>
                      </div>
                    </div>
                  <?php else: ?>
                    <span class="stock-value" style="font-size: 24px;">∞</span>
                  <?php endif; ?>
                </td>

                <td class="text-center">
                  <span class="badge <?= $statusBadge ?>">
                    <?= $statusText ?>
                  </span>
                </td>

                <td class="text-right">
                  <div class="action-buttons">
                    <button 
                      onclick="openRestockModal(<?= $product['id'] ?>, '<?= htmlspecialchars($product['name'] ?? 'Product', ENT_QUOTES) ?>', <?= $totalStock ?>)"
                      class="action-btn restock"
                      title="<?= $t['restock_product'] ?>"
                    >
                      <i class="fas fa-plus-circle"></i>
                    </button>
                    <a 
                      href="<?= url('admin/products/allocations?id=' . $product['id']) ?>" 
                      class="action-btn allocations"
                      title="<?= $t['view_allocations'] ?>"
                    >
                      <i class="fas fa-users"></i>
                    </a>
                    <a 
                      href="<?= url('admin/products/stock-movements?id=' . $product['id']) ?>" 
                      class="action-btn movements"
                      title="<?= $t['movements'] ?>"
                    >
                      <i class="fas fa-history"></i>
                    </a>
                    <a 
                      href="<?= url('admin/products/edit?id=' . $product['id']) ?>" 
                      class="action-btn edit"
                      title="<?= $t['edit_product'] ?>"
                    >
                      <i class="fas fa-cog"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <i class="fas fa-box"></i>
                  </div>
                  <div class="empty-state-title"><?= $t['no_products_found'] ?></div>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if (($total ?? 0) > ($perPage ?? 20)): ?>
      <div class="pagination">
        <div class="pagination-info">
          <?= $t['showing'] ?> <?= ((($page ?? 1) - 1) * ($perPage ?? 20)) + 1 ?> <?= $t['to'] ?> <?= min(($page ?? 1) * ($perPage ?? 20), $total ?? 0) ?> <?= $t['of'] ?> <?= $total ?? 0 ?> <?= $t['products_text'] ?>
        </div>
        <div class="pagination-buttons">
          <?php
          $totalPages = ceil(($total ?? 0) / ($perPage ?? 20));
          $queryParams = http_build_query(array_filter([
              'search' => $search ?? '',
              'stock_status' => $stockStatus ?? '',
          ]));
          ?>
          
          <?php if (($page ?? 1) > 1): ?>
            <a 
              href="<?= url('admin/products/stock?page=' . (($page ?? 1) - 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
              class="pagination-btn"
            >
              <i class="fas fa-chevron-left"></i> <?= $t['previous'] ?>
            </a>
          <?php endif; ?>

          <?php for ($i = max(1, ($page ?? 1) - 2); $i <= min($totalPages, ($page ?? 1) + 2); $i++): ?>
            <a 
              href="<?= url('admin/products/stock?page=' . $i . ($queryParams ? '&' . $queryParams : '')) ?>" 
              class="pagination-btn <?= $i === ($page ?? 1) ? 'active' : '' ?>"
            >
              <?= $i ?>
            </a>
          <?php endfor; ?>

          <?php if (($page ?? 1) < $totalPages): ?>
            <a 
              href="<?= url('admin/products/stock?page=' . (($page ?? 1) + 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
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
          (<?= $totalSeller ?? 0 ?> products - view only)
        </span>
      </h2>
      <p style="margin: 8px 0 0 0; font-size: 13px; color: #856404;">
        Sellers manage their own inventory. This is a read-only view for admin monitoring.
      </p>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th class="text-center">SKU</th>
            <th class="text-center">Shop</th>
            <th class="text-center">Seller</th>
            <th class="text-center">Stock</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sellerProducts as $product): ?>
            <?php
            // Handle product image
            $imagePath = null;
            if (!empty($product['primary_image'])) {
                $imagePath = $product['primary_image'];
            } elseif (!empty($product['images'])) {
                $images = json_decode($product['images'], true);
                if (is_array($images) && !empty($images)) {
                    $imagePath = $images[0]['image_path'] ?? null;
                }
            }
            ?>
            <tr>
              <td>
                <div class="product-cell">
                  <div class="product-image">
                    <?php if ($imagePath): ?>
                      <img src="<?= asset($imagePath) ?>" alt="Product">
                    <?php else: ?>
                      <i class="fas fa-box"></i>
                    <?php endif; ?>
                  </div>
                  <div class="product-info">
                    <div class="product-name"><?= htmlspecialchars($product['name'] ?? 'N/A') ?></div>
                    <div class="product-price"><?= currency($product['base_price'] ?? 0) ?></div>
                  </div>
                </div>
              </td>

              <td class="text-center">
                <span class="sku-text"><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></span>
              </td>

              <td class="text-center">
                <strong style="color: #4CAF50;"><?= htmlspecialchars($product['shop_name'] ?? 'N/A') ?></strong>
              </td>

              <td class="text-center">
                <span style="font-size: 13px; color: #666;">
                  <?= htmlspecialchars($product['seller_email'] ?? 'N/A') ?>
                </span>
              </td>

              <td class="text-center">
                <span style="font-weight: 700; font-size: 16px; color: #333;">
                  <?= number_format($product['shop_stock'] ?? 0) ?>
                </span>
              </td>

              <td class="text-right">
                <div class="action-buttons">
                  <a
                    href="<?= url('admin/products/edit?id=' . $product['id']) ?>"
                    class="action-btn edit"
                    title="View Details"
                  >
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

</div>

<!-- Restock Modal -->
<div id="restockModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><?= $t['add_stock_to_warehouse'] ?></h3>
      <button onclick="closeRestockModal()" class="modal-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
    
    <form id="restockForm" class="modal-body">
      <?= csrfField() ?>
      <input type="hidden" id="modalProductId" name="product_id">
      
      <div class="modal-field">
        <label><?= $t['product'] ?></label>
        <p id="modalProductName" class="value"></p>
      </div>

      <div class="modal-field">
        <label><?= $t['current_warehouse_stock'] ?></label>
        <p id="modalCurrentStock" class="value large"></p>
      </div>

      <div class="modal-field">
        <label><?= $t['quantity_to_add'] ?></label>
        <input 
          type="number" 
          id="restockQuantity" 
          name="quantity" 
          min="1" 
          class="form-input"
          required
          placeholder="100"
        >
      </div>

      <div class="modal-field">
        <label><?= $t['notes'] ?></label>
        <textarea 
          id="restockNotes" 
          name="notes" 
          class="form-input"
          placeholder="New shipment arrived, supplier invoice #123..."
        ></textarea>
      </div>

      <div class="modal-actions">
        <button type="button" onclick="closeRestockModal()" class="btn btn-secondary">
          <?= $t['cancel'] ?>
        </button>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-plus-circle"></i> <?= $t['add_to_warehouse'] ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

// Restock Modal
function openRestockModal(productId, productName, currentStock) {
  document.getElementById('modalProductId').value = productId;
  document.getElementById('modalProductName').textContent = productName;
  document.getElementById('modalCurrentStock').textContent = currentStock;
  document.getElementById('restockQuantity').value = '';
  document.getElementById('restockNotes').value = '';
  document.getElementById('restockModal').classList.add('active');
}

function closeRestockModal() {
  document.getElementById('restockModal').classList.remove('active');
}

// Restock Form Submit
document.getElementById('restockForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  const formData = new URLSearchParams();
  formData.append(csrfName, csrfToken);
  formData.append('product_id', document.getElementById('modalProductId').value);
  formData.append('quantity', document.getElementById('restockQuantity').value);
  formData.append('notes', document.getElementById('restockNotes').value);
  
  try {
    const response = await fetch('<?= url('admin/products/restock') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: formData.toString()
    });
    
    const result = await response.json();
    
    if (result.success) {
      alert(result.message || '<?= $t['update_success'] ?>');
      closeRestockModal();
      location.reload();
    } else {
      alert('Error: ' + (result.message || '<?= $t['update_error'] ?>'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('<?= $t['update_error'] ?>');
  }
});

// Export Stock
function exportStock() {
  const params = new URLSearchParams(window.location.search);
  window.location.href = '<?= url('admin/products/stock/export') ?>?' + params.toString();
}

// ESC key to close modal
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeRestockModal();
  }
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>