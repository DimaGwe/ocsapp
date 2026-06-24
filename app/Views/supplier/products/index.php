<?php
$pageTitle = (($_SESSION['language'] ?? 'fr') === 'fr') ? 'Mes produits' : 'My Products';
require dirname(__DIR__) . '/layout-header.php';
// $t is loaded by layout-header.php
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-700);
  }

  .btn-primary {
    padding: 12px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
  }

  .btn-primary:hover {
    background: var(--primary-dark);
  }

  .filters-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 16px;
  }

  .form-input, .form-select {
    padding: 10px 16px;
    border: 2px solid var(--gray-200);
    border-radius: 8px;
    font-size: 14px;
    width: 100%;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
  }

  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
  }

  .product-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
  }

  .product-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .product-image-container {
    width: 100%;
    height: 200px;
    background: var(--gray-100);
    border-radius: 8px;
    margin-bottom: 16px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .product-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    color: var(--gray-400);
    font-size: 48px;
  }

  .product-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .product-name {
    font-size: 18px;
    font-weight: 700;
    color: var(--gray-700);
    margin-bottom: 4px;
  }

  .product-sku {
    font-size: 13px;
    color: var(--gray-600);
  }

  .product-price {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary);
    margin: 16px 0;
  }

  .product-details {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 16px;
  }

  .product-details div {
    margin-bottom: 8px;
  }

  .product-actions {
    display: flex;
    gap: 8px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid var(--gray-200);
  }

  .btn-edit {
    flex: 1;
    padding: 8px;
    background: var(--gray-100);
    color: var(--gray-700);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
  }

  .btn-delete {
    padding: 8px 16px;
    background: var(--danger);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }

  .badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge.available { background: #dcfce7; color: #166534; }
  .badge.unavailable { background: #fee2e2; color: #991b1b; }

  .empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 12px;
  }

  .empty-state i {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 20px;
  }

  .empty-state h3 {
    font-size: 20px;
    color: var(--gray-700);
    margin-bottom: 8px;
  }

  .empty-state p {
    color: var(--gray-600);
    margin-bottom: 24px;
  }
</style>

<div class="page-header">
  <h1 class="page-title"><?= $fr ? 'Mes produits' : 'My Products' ?></h1>
  <a href="<?= url('supplier/products/create') ?>" class="btn-primary">
    <i class="fas fa-plus"></i> <?= $fr ? 'Ajouter un produit' : 'Add Product' ?>
  </a>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET">
    <div class="filters-grid">
      <input
        type="text"
        name="search"
        placeholder="<?= $fr ? 'Rechercher par nom ou SKU...' : 'Search by name or SKU...' ?>"
        value="<?= htmlspecialchars($search ?? '') ?>"
        class="form-input"
      >
      <select name="available" class="form-select">
        <option value=""><?= $fr ? 'Tous les produits' : 'All Products' ?></option>
        <option value="1" <?= ($available ?? '') === '1' ? 'selected' : '' ?>><?= $fr ? 'Disponibles seulement' : 'Available Only' ?></option>
        <option value="0" <?= ($available ?? '') === '0' ? 'selected' : '' ?>><?= $fr ? 'Non disponibles seulement' : 'Unavailable Only' ?></option>
      </select>
      <button type="submit" class="btn-filter">
        <i class="fas fa-filter"></i> <?= $fr ? 'Filtrer' : 'Filter' ?>
      </button>
    </div>
  </form>
</div>

<!-- Products Grid -->
<?php if (!empty($products)): ?>
  <div class="products-grid">
    <?php foreach ($products as $product): ?>
      <div class="product-card">
        <div class="product-image-container">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= asset($product['image']) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>" class="product-image">
          <?php else: ?>
            <div class="product-image-placeholder">
              <i class="fas fa-box"></i>
            </div>
          <?php endif; ?>
        </div>

        <div class="product-header">
          <div>
            <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
            <div class="product-sku">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/A') ?></div>
          </div>
          <span class="badge <?= $product['is_available'] ? 'available' : 'unavailable' ?>">
            <?= $product['is_available'] ? ($fr ? 'Disponible' : 'Available') : ($fr ? 'Non disponible' : 'Unavailable') ?>
          </span>
        </div>

        <div class="product-price">
          $<?= number_format($product['unit_price'], 2) ?>
          <span style="font-size: 14px; color: var(--gray-600); font-weight: normal;">/ <?= htmlspecialchars($product['unit']) ?></span>
        </div>

        <div class="product-details">
          <?php
            $qty = (int)($product['stock_quantity'] ?? 0);
            $stockColor = $qty > 20 ? '#059669' : ($qty > 0 ? '#d97706' : '#dc2626');
          ?>
          <div><strong><?= $fr ? 'Stock :' : 'Stock:' ?></strong> <span style="color:<?= $stockColor ?>;font-weight:700;"><?= number_format($qty) ?></span> <?= $fr ? 'unités' : 'units' ?></div>
          <div><strong><?= $fr ? 'Poids :' : 'Weight:' ?></strong> <?= $product['weight_kg'] ? number_format($product['weight_kg'], 2) . ' kg' : ($fr ? 'Non défini' : 'Not set') ?></div>
          <div><strong><?= $fr ? 'Commande min :' : 'Min Order:' ?></strong> <?= $product['minimum_order_quantity'] ?> <?= $fr ? 'unités' : 'units' ?></div>
          <div><strong><?= $fr ? 'Délai :' : 'Lead Time:' ?></strong> <?= $product['lead_time_days'] ?> <?= $fr ? 'jours' : 'days' ?></div>
          <?php if ($product['description']): ?>
            <div style="margin-top: 12px; font-size: 13px; color: var(--gray-600);">
              <?= nl2br(htmlspecialchars(substr($product['description'], 0, 100))) ?>
              <?= strlen($product['description']) > 100 ? '...' : '' ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="product-actions">
          <a href="<?= url('supplier/products/edit?id=' . $product['id']) ?>" class="btn-edit">
            <i class="fas fa-edit"></i> <?= $fr ? 'Modifier' : 'Edit' ?>
          </a>
          <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn-delete">
            <i class="fas fa-trash"></i>
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php
    $baseUrl = 'supplier/products';
    $queryParams = ['search' => $search ?? '', 'available' => $available ?? ''];
    require dirname(dirname(__DIR__)) . '/components/pagination.php';
  ?>
<?php else: ?>
  <div class="empty-state">
    <i class="fas fa-box-open"></i>
    <h3><?= $fr ? 'Aucun produit pour l\'instant' : 'No Products Yet' ?></h3>
    <p><?= $fr ? 'Commencez par ajouter votre premier produit à votre catalogue' : 'Start by adding your first product to your catalog' ?></p>
    <a href="<?= url('supplier/products/create') ?>" class="btn-primary">
      <i class="fas fa-plus"></i> <?= $fr ? 'Ajouter votre premier produit' : 'Add Your First Product' ?>
    </a>
  </div>
<?php endif; ?>

<script>
const _confirmDeleteMsg = <?= json_encode($fr ? 'Êtes-vous sûr de vouloir supprimer ce produit ?' : 'Are you sure you want to delete this product?') ?>;
function deleteProduct(id) {
  if (!confirm(_confirmDeleteMsg)) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('supplier/products/delete') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error deleting product'));
}
</script>

<?php require dirname(__DIR__) . '/layout-footer.php'; ?>
