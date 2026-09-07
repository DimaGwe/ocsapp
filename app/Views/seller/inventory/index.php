<?php
$shop      = $shop ?? null;
$inventory = $inventory ?? [];
$pageTitle = 'Inventory';
require __DIR__ . '/../layout-header.php';
?>

<style>
  .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
  .btn-green { background:var(--primary); color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
  .btn-outline { background:#fff; color:var(--primary); border:2px solid var(--primary); padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; text-decoration:none; }
  .btn-red { background:#fff; color:var(--danger); border:2px solid var(--danger); padding:6px 14px; border-radius:6px; cursor:pointer; font-size:13px; }
  .card { background:#fff; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.1); overflow:hidden; }
  table { width:100%; border-collapse:collapse; }
  th { background:var(--gray-50); font-size:12px; font-weight:700; text-transform:uppercase; color:var(--gray-600); padding:12px 16px; text-align:left; border-bottom:1px solid var(--gray-200); }
  td { padding:16px; border-top:1px solid var(--gray-100); font-size:14px; vertical-align:middle; }
  .badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; }
  .badge-active { background:#dcfce7; color:#166534; }
  .badge-inactive { background:#fee2e2; color:#991b1b; }
  .product-img { width:44px; height:44px; border-radius:8px; object-fit:cover; background:var(--gray-100); display:flex; align-items:center; justify-content:center; color:var(--gray-400); font-size:18px; }
  .empty-state { text-align:center; padding:60px 20px; color:var(--gray-600); }
</style>

<div class="page-header">
    <h2 style="font-size:18px;font-weight:700;color:var(--gray-700);"><i class="fa fa-boxes" style="color:var(--primary);margin-right:8px;"></i> My Inventory</h2>
    <div style="display:flex;gap:10px;">
        <a href="<?= url('seller/inventory/add') ?>" class="btn-outline"><i class="fa fa-plus"></i> Add Existing Product</a>
        <a href="<?= url('seller/inventory/create-product') ?>" class="btn-green"><i class="fa fa-plus"></i> Create New Product</a>
    </div>
</div>

<div class="card">
    <?php if (empty($inventory)): ?>
        <div class="empty-state">
            <i class="fa fa-box-open" style="font-size:48px;margin-bottom:16px;display:block;color:var(--gray-300);"></i>
            <p style="font-size:16px;font-weight:600;color:var(--gray-700);margin-bottom:8px;">No products in inventory yet.</p>
            <p>Add products to start selling.</p>
            <a href="<?= url('seller/inventory/create-product') ?>" class="btn-green" style="margin-top:16px;">Create First Product</a>
        </div>
    <?php else: ?>
        <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $item): ?>
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?= url($item['image_path']) ?>" class="product-img" alt="">
                                <?php else: ?>
                                    <div class="product-img"><i class="fa fa-box"></i></div>
                                <?php endif; ?>
                                <span style="font-weight:600;color:var(--gray-700);"><?= htmlspecialchars($item['product_name']) ?></span>
                            </div>
                        </td>
                        <td style="color:var(--gray-600);"><?= htmlspecialchars($item['sku'] ?? '—') ?></td>
                        <td style="font-weight:700;color:var(--primary);">$<?= number_format($item['price'], 2) ?></td>
                        <td>
                            <span style="<?= $item['stock_quantity'] <= 5 ? 'color:var(--danger);font-weight:700;' : '' ?>">
                                <?= (int)$item['stock_quantity'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-<?= $item['status'] === 'active' ? 'active' : 'inactive' ?>">
                                <?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= url('seller/inventory/edit?id=' . $item['id']) ?>" class="btn-outline" style="font-size:12px;">Edit</a>
                            <form method="POST" action="<?= url('seller/inventory/delete') ?>" style="display:inline;" onsubmit="return confirm('Remove from inventory?')">
                                <input type="hidden" name="<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="inventory_id" value="<?= $item['id'] ?>">
                                <button type="submit" class="btn-red">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/../layout-footer.php'; ?>
