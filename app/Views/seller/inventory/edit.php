<?php
$shop = $shop ?? null;
$item = $item ?? [];
$pageTitle = 'Inventory';
require __DIR__ . '/../layout-header.php';
?>

<style>
  .form-card { max-width:600px; background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); padding:32px; }
  .form-card h2 { font-size:18px; font-weight:700; color:var(--gray-700); margin-bottom:6px; }
  .product-name { color:var(--gray-600); font-size:14px; margin-bottom:24px; }
  label { display:block; font-size:13px; font-weight:600; color:var(--gray-700); margin-bottom:6px; }
  input, select { width:100%; padding:10px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
  input:focus, select:focus { outline:none; border-color:var(--primary); }
  .btn-green { background:var(--primary); color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; }
</style>

<div class="form-card">
    <h2>Edit Inventory Item</h2>
    <p class="product-name"><?= htmlspecialchars($item['product_name'] ?? '') ?> <?= $item['sku'] ? '· SKU: ' . htmlspecialchars($item['sku']) : '' ?></p>

    <form method="POST" action="<?= url('seller/inventory/update') ?>">
        <input type="hidden" name="<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>" value="<?= generateCsrfToken() ?>">
        <input type="hidden" name="inventory_id" value="<?= $item['id'] ?>">

        <label for="price">Selling Price (CAD)</label>
        <input type="number" name="price" id="price" min="0.01" step="0.01"
               value="<?= htmlspecialchars($item['price'] ?? '') ?>" required>

        <label for="stock_quantity">Stock Quantity</label>
        <input type="number" name="stock_quantity" id="stock_quantity" min="0"
               value="<?= (int)($item['stock_quantity'] ?? 0) ?>">

        <?php if (($item['product_type'] ?? '') === 'seller'): ?>
            <label for="weight">Weight per unit (kg)</label>
            <input type="number" name="weight" id="weight" min="0.01" step="0.01"
                   value="<?= htmlspecialchars($item['product_weight'] ?? '') ?>" required>
        <?php endif; ?>

        <label for="status">Status</label>
        <select name="status" id="status">
            <option value="active"   <?= ($item['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (hidden from buyers)</option>
        </select>

        <button type="submit" class="btn-green">Save Changes</button>
    </form>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
