<?php
$shop       = $shop ?? null;
$categories = $categories ?? [];
$pageTitle = 'Inventory';
require __DIR__ . '/../layout-header.php';
?>

<style>
  .form-card { max-width:680px; background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); padding:32px; }
  .form-card h2 { font-size:18px; font-weight:700; color:var(--gray-700); margin-bottom:6px; }
  .form-card p.sub { color:var(--gray-600); font-size:13px; margin-bottom:24px; }
  label { display:block; font-size:13px; font-weight:600; color:var(--gray-700); margin-bottom:6px; }
  input, select, textarea { width:100%; padding:10px 14px; border:1px solid var(--gray-200); border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
  textarea { resize:vertical; min-height:90px; }
  input:focus, select:focus, textarea:focus { outline:none; border-color:var(--primary); }
  .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
  .btn-green { background:var(--primary); color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; margin-top:8px; }
</style>

<div class="form-card">
    <h2>Create New Product</h2>
    <p class="sub">The product will be added to your inventory automatically.</p>

    <form method="POST" action="<?= url('seller/inventory/store-product') ?>">
        <input type="hidden" name="<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>" value="<?= generateCsrfToken() ?>">

        <label for="name">Product Name <span style="color:var(--danger);">*</span></label>
        <input type="text" name="name" id="name" placeholder="e.g. Organic Whole Milk 2L" required maxlength="200">

        <label for="category_id">Category <span style="color:var(--danger);">*</span></label>
        <select name="category_id" id="category_id" required>
            <option value="">— Select category —</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="description">Description</label>
        <textarea name="description" id="description" placeholder="Product details, ingredients, size, etc."></textarea>

        <div class="row-2">
            <div>
                <label for="price">Selling Price (CAD) <span style="color:var(--danger);">*</span></label>
                <input type="number" name="price" id="price" min="0.01" step="0.01" placeholder="9.99" required>
            </div>
            <div>
                <label for="stock_quantity">Initial Stock Qty</label>
                <input type="number" name="stock_quantity" id="stock_quantity" min="0" value="0">
            </div>
        </div>

        <div class="row-2">
            <div>
                <label for="sku">SKU (optional)</label>
                <input type="text" name="sku" id="sku" placeholder="e.g. MLK-ORG-2L" maxlength="100">
            </div>
            <div>
                <label for="weight">Weight per unit (kg)</label>
                <input type="number" name="weight" id="weight" min="0.01" step="0.01" placeholder="e.g. 2.0" required>
            </div>
        </div>

        <button type="submit" class="btn-green"><i class="fa fa-plus"></i> Create Product &amp; Add to Inventory</button>
    </form>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
