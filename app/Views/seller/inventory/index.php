<?php
/**
 * Seller inventory list (/seller/inventory, InventoryController::index)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR, fr-CA prices; low-stock highlight.
 */
$shop      = $shop ?? null;
$inventory = $inventory ?? [];
$pageTitle = 'Inventory';
require __DIR__ . '/../layout-header.php';
$csrfName = env('CSRF_TOKEN_NAME', '_csrf_token');
$lowStock = count(array_filter($inventory, fn($i) => (int) $i['stock_quantity'] <= 5));
?>
<div class="sp-stack">
  <div class="sp-between">
    <p class="sp-muted" style="margin:0"><?= count($inventory) ?> <?= $fr ? (count($inventory) > 1 ? 'produits' : 'produit') : (count($inventory) === 1 ? 'product' : 'products') ?><?= $lowStock > 0 ? ' · <span class="sp-badge sp-badge-warn">' . $lowStock . ' ' . ($fr ? 'en stock faible' : 'low on stock') . '</span>' : '' ?></p>
    <div class="sp-row">
      <a href="<?= url('seller/inventory/add') ?>" class="sp-btn sp-btn-ghost"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Ajouter un produit existant' : 'Add existing product' ?></a>
      <a href="<?= url('seller/inventory/create-product') ?>" class="sp-btn sp-btn-primary"><i class="fa-solid fa-plus"></i> <?= $fr ? 'Créer un produit' : 'Create product' ?></a>
    </div>
  </div>

  <section class="sp-card">
    <?php if (empty($inventory)): ?>
      <div class="sp-empty"><i class="fa-solid fa-box-open"></i>
        <p><?= $fr ? "Aucun produit dans votre inventaire. Ajoutez des produits pour commencer à vendre." : 'No products in your inventory yet. Add products to start selling.' ?></p>
        <a href="<?= url('seller/inventory/create-product') ?>" class="sp-btn sp-btn-primary"><?= $fr ? 'Créer mon premier produit' : 'Create my first product' ?></a></div>
    <?php else: ?>
      <div class="sp-table-wrap">
        <table class="sp-table">
          <thead><tr><th><?= $fr ? 'Produit' : 'Product' ?></th><th>SKU</th><th class="num"><?= $fr ? 'Prix' : 'Price' ?></th><th class="num"><?= $fr ? 'Stock' : 'Stock' ?></th><th><?= $fr ? 'Statut' : 'Status' ?></th><th></th></tr></thead>
          <tbody>
          <?php foreach ($inventory as $item): ?>
            <?php $stock = (int) $item['stock_quantity']; $active = ($item['status'] ?? '') === 'active'; ?>
            <tr>
              <td><div class="sp-row" style="flex-wrap:nowrap">
                <?php if (!empty($item['image_path'])): ?><img class="sp-thumb" src="<?= htmlspecialchars(url(ltrim($item['image_path'], '/'))) ?>" alt="" loading="lazy">
                <?php else: ?><div class="sp-thumb" style="display:grid;place-items:center;color:#C9CFCA"><i class="fa-solid fa-image"></i></div><?php endif; ?>
                <span class="sp-strong"><?= htmlspecialchars(html_entity_decode((string) $item['product_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></span></div></td>
              <td class="sp-dim"><?= htmlspecialchars($item['sku'] ?? '') ?></td>
              <td class="num sp-strong"><?= acct_money($item['price'], $fr) ?></td>
              <td class="num"><?= $stock <= 5 ? '<span class="sp-badge sp-badge-warn">' . $stock . '</span>' : $stock ?></td>
              <td><span class="sp-badge sp-badge-<?= $active ? 'active' : 'inactive' ?>"><?= $active ? ($fr ? 'Actif' : 'Active') : ($fr ? 'Masqué' : 'Hidden') ?></span></td>
              <td><div class="sp-row" style="flex-wrap:nowrap;justify-content:flex-end">
                <a href="<?= url('seller/inventory/edit?id=' . (int) $item['id']) ?>" class="sp-btn sp-btn-ghost sp-btn-sm"><i class="fa-solid fa-pen"></i> <?= $fr ? 'Modifier' : 'Edit' ?></a>
                <form method="POST" action="<?= url('seller/inventory/delete') ?>" onsubmit="return confirm(<?= htmlspecialchars(json_encode($fr ? "Retirer ce produit de votre inventaire ?" : 'Remove this product from your inventory?', JSON_UNESCAPED_UNICODE)) ?>)">
                  <input type="hidden" name="<?= htmlspecialchars($csrfName) ?>" value="<?= generateCsrfToken() ?>">
                  <input type="hidden" name="inventory_id" value="<?= (int) $item['id'] ?>">
                  <button type="submit" class="sp-btn sp-btn-danger sp-btn-sm" aria-label="<?= $fr ? 'Retirer' : 'Remove' ?>"><i class="fa-solid fa-trash"></i></button>
                </form></div></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>
</div>
<?php require __DIR__ . '/../layout-footer.php'; ?>
