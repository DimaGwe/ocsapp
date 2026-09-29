<?php
/**
 * Add an existing catalogue product to the seller's inventory (/seller/inventory/add)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR. Same form fields.
 */
$shop              = $shop ?? null;
$availableProducts = $availableProducts ?? [];
$pageTitle = 'Add Product';
require __DIR__ . '/../layout-header.php';
?>
<section class="sp-card" style="max-width:680px">
  <div class="sp-card-head"><h2><?= $fr ? 'Ajouter un produit existant' : 'Add an existing product' ?></h2>
    <a href="<?= url('seller/inventory') ?>"><i class="fa-solid fa-arrow-left"></i> <?= $fr ? 'Inventaire' : 'Inventory' ?></a></div>
  <?php if (empty($availableProducts)): ?>
    <div class="sp-empty"><i class="fa-solid fa-circle-check"></i>
      <p><?= $fr ? 'Tous les produits disponibles sont déjà dans votre inventaire.' : 'All available products are already in your inventory.' ?></p>
      <a href="<?= url('seller/inventory/create-product') ?>" class="sp-btn sp-btn-primary"><?= $fr ? 'Créer un produit' : 'Create a product' ?></a></div>
  <?php else: ?>
    <form class="sp-form" method="POST" action="<?= url('seller/inventory/store') ?>">
      <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= generateCsrfToken() ?>">
      <label><?= $fr ? 'Produit' : 'Product' ?> *
        <select name="product_id" required>
          <option value=""><?= $fr ? 'Choisir un produit' : 'Select a product' ?></option>
          <?php foreach ($availableProducts as $p): ?>
            <option value="<?= (int) $p['id'] ?>"><?= htmlspecialchars(html_entity_decode((string) $p['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?><?= !empty($p['sku']) ? ' (' . htmlspecialchars($p['sku']) . ')' : '' ?></option>
          <?php endforeach; ?>
        </select></label>
      <div class="sp-form-row">
        <label><?= $fr ? 'Votre prix de vente ($ CA)' : 'Your selling price (CAD)' ?> *<input type="number" name="price" min="0.01" step="0.01" placeholder="12,99" required></label>
        <label><?= $fr ? 'Quantité en stock' : 'Stock quantity' ?><input type="number" name="stock_quantity" min="0" value="0"></label>
      </div>
      <div><button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-plus"></i> <?= $fr ? "Ajouter à l'inventaire" : 'Add to inventory' ?></button></div>
    </form>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/../layout-footer.php'; ?>
