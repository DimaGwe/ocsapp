<?php
/**
 * Create a new product and add it to the seller's inventory (/seller/inventory/create-product)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR. Same form fields.
 * Photos (up to 6, first one = main) added 2026-09-28, saved by InventoryController::saveProductImages().
 */
$shop       = $shop ?? null;
$categories = $categories ?? [];
$pageTitle = 'Create Product';
require __DIR__ . '/../layout-header.php';
?>
<section class="sp-card" style="max-width:760px">
  <div class="sp-card-head"><h2><?= $fr ? 'Créer un produit' : 'Create a product' ?></h2>
    <a href="<?= url('seller/inventory') ?>"><i class="fa-solid fa-arrow-left"></i> <?= $fr ? 'Inventaire' : 'Inventory' ?></a></div>
  <p class="sp-muted" style="margin:-6px 0 18px"><?= $fr ? 'Le produit est ajouté automatiquement à votre inventaire.' : 'The product is added to your inventory automatically.' ?></p>
  <form class="sp-form" method="POST" action="<?= url('seller/inventory/store-product') ?>" enctype="multipart/form-data">
    <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= generateCsrfToken() ?>">
    <label><?= $fr ? 'Nom du produit' : 'Product name' ?> *<input type="text" name="name" required maxlength="200" placeholder="<?= $fr ? 'ex. Lait biologique 2 L' : 'e.g. Organic whole milk 2L' ?>"></label>
    <label><?= $fr ? 'Catégorie' : 'Category' ?> *
      <select name="category_id" required>
        <option value=""><?= $fr ? 'Choisir une catégorie' : 'Select a category' ?></option>
        <?php foreach ($categories as $cat): ?><option value="<?= (int) $cat['id'] ?>"><?= htmlspecialchars(html_entity_decode((string) $cat['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></option><?php endforeach; ?>
      </select></label>
    <label><?= $fr ? 'Description' : 'Description' ?><textarea name="description" placeholder="<?= $fr ? 'Détails, ingrédients, format, etc.' : 'Details, ingredients, size, etc.' ?>"></textarea></label>
    <div class="sp-form-row">
      <label><?= $fr ? 'Prix de vente ($ CA)' : 'Selling price (CAD)' ?> *<input type="number" name="price" min="0.01" step="0.01" placeholder="9,99" required></label>
      <label><?= $fr ? 'Stock initial' : 'Initial stock' ?><input type="number" name="stock_quantity" min="0" value="0"></label>
    </div>
    <div class="sp-form-row">
      <label><?= $fr ? 'SKU (facultatif)' : 'SKU (optional)' ?><input type="text" name="sku" maxlength="100" placeholder="<?= $fr ? 'ex. LAIT-BIO-2L' : 'e.g. MLK-ORG-2L' ?>"></label>
      <label><?= $fr ? 'Poids par unité (kg)' : 'Weight per unit (kg)' ?> *<input type="number" name="weight" min="0.01" step="0.01" placeholder="2" required>
        <small><?= $fr ? 'Sert au calcul des frais de livraison surdimensionnée.' : 'Used to calculate oversize delivery fees.' ?></small></label>
    </div>
    <label class="sp-check"><input type="checkbox" name="age_restricted" value="1"> <?= $fr ? 'Réservé aux 18 ans et plus' : 'Age-restricted (18+)' ?>
      <small><?= $fr ? "Cochez si la loi interdit la vente de ce produit aux mineurs. Il sera masqué et bloqué pour les comptes du Profil Maison (13 à 17 ans)." : 'Check if the law prohibits selling this product to minors. It will be hidden and blocked for Home Profile accounts (ages 13 to 17).' ?></small></label>
    <?php require __DIR__ . '/partials/photo-picker.php'; ?>
    <div><button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-plus"></i> <?= $fr ? "Créer et ajouter à l'inventaire" : 'Create and add to inventory' ?></button></div>
  </form>
</section>
<?php require __DIR__ . '/../layout-footer.php'; ?>
