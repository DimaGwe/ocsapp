<?php
/**
 * Edit a seller inventory item (/seller/inventory/edit?id=)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR. Same form fields.
 */
$shop = $shop ?? null;
$item = $item ?? [];
$pageTitle = 'Edit Product';
require __DIR__ . '/../layout-header.php';
?>
<section class="sp-card" style="max-width:680px">
  <div class="sp-card-head"><h2><?= htmlspecialchars(html_entity_decode((string) ($item['product_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></h2>
    <a href="<?= url('seller/inventory') ?>"><i class="fa-solid fa-arrow-left"></i> <?= $fr ? 'Inventaire' : 'Inventory' ?></a></div>
  <?php if (!empty($item['sku'])): ?><p class="sp-dim" style="margin:-6px 0 16px">SKU : <?= htmlspecialchars($item['sku']) ?></p><?php endif; ?>
  <form class="sp-form" method="POST" action="<?= url('seller/inventory/update') ?>">
    <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= generateCsrfToken() ?>">
    <input type="hidden" name="inventory_id" value="<?= (int) ($item['id'] ?? 0) ?>">
    <div class="sp-form-row">
      <label><?= $fr ? 'Prix de vente ($ CA)' : 'Selling price (CAD)' ?> *<input type="number" name="price" min="0.01" step="0.01" value="<?= htmlspecialchars($item['price'] ?? '') ?>" required></label>
      <label><?= $fr ? 'Quantité en stock' : 'Stock quantity' ?><input type="number" name="stock_quantity" min="0" value="<?= (int) ($item['stock_quantity'] ?? 0) ?>"></label>
    </div>
    <?php if (($item['product_type'] ?? '') === 'seller'): ?>
      <label><?= $fr ? 'Poids par unité (kg)' : 'Weight per unit (kg)' ?> *<input type="number" name="weight" min="0.01" step="0.01" value="<?= htmlspecialchars($item['product_weight'] ?? '') ?>" required>
        <small><?= $fr ? 'Sert au calcul des frais de livraison surdimensionnée.' : 'Used to calculate oversize delivery fees.' ?></small></label>
    <?php endif; ?>
    <label><?= $fr ? 'Statut' : 'Status' ?>
      <select name="status">
        <option value="active" <?= ($item['status'] ?? '') === 'active' ? 'selected' : '' ?>><?= $fr ? 'Actif (visible par les acheteurs)' : 'Active (visible to buyers)' ?></option>
        <option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>><?= $fr ? 'Masqué (invisible pour les acheteurs)' : 'Hidden (not visible to buyers)' ?></option>
      </select></label>
    <div><button type="submit" class="sp-btn sp-btn-primary"><?= $fr ? 'Enregistrer' : 'Save changes' ?></button></div>
  </form>
</section>
<?php require __DIR__ . '/../layout-footer.php'; ?>
