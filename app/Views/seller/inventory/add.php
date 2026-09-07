<?php
$shop              = $shop ?? null;
$availableProducts = $availableProducts ?? [];
$pageTitle = 'Inventory';
require __DIR__ . '/../layout-header.php';
?>

<style>
  .form-card { max-width:640px; background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); padding:32px; }
  .form-card h2 { font-size:18px; font-weight:700; color:var(--gray-700); margin-bottom:24px; }
  label { display:block; font-size:13px; font-weight:600; color:var(--gray-700); margin-bottom:6px; }
  input, select { width:100%; padding:10px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
  input:focus, select:focus { outline:none; border-color:var(--primary); }
  .btn-green { background:var(--primary); color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; }
</style>

<div class="form-card">
    <h2>Add Product to Inventory</h2>

    <?php if (empty($availableProducts)): ?>
        <p style="color:var(--gray-600);margin-bottom:20px;">All available products are already in your inventory.</p>
        <a href="<?= url('seller/inventory/create-product') ?>" class="btn-green" style="display:inline-block;text-align:center;text-decoration:none;">Create New Product</a>
    <?php else: ?>
        <form method="POST" action="<?= url('seller/inventory/store') ?>">
            <input type="hidden" name="<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>" value="<?= generateCsrfToken() ?>">

            <label for="product_id">Product</label>
            <select name="product_id" id="product_id" required>
                <option value="">— Select a product —</option>
                <?php foreach ($availableProducts as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['name']) ?><?= $p['sku'] ? ' (' . htmlspecialchars($p['sku']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="price">Your Selling Price (CAD)</label>
            <input type="number" name="price" id="price" min="0.01" step="0.01" placeholder="e.g. 12.99" required>

            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" min="0" placeholder="e.g. 50" value="0">

            <button type="submit" class="btn-green">Add to Inventory</button>
        </form>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
