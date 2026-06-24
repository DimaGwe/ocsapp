<?php
$shop      = $shop ?? null;
$inventory = $inventory ?? [];
$user      = user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['language'] ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inventory - OCS Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#333; }
        .wrap { max-width:1100px; margin:40px auto; padding:0 16px; }
        .page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; }
        .page-header h1 { font-size:22px; font-weight:600; }
        .btn-green { background:#00b207; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:500; text-decoration:none; display:inline-flex; align-items:center; gap:8px; }
        .btn-outline { background:#fff; color:#00b207; border:2px solid #00b207; padding:8px 16px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:500; text-decoration:none; }
        .btn-red { background:#fff; color:#dc3545; border:2px solid #dc3545; padding:6px 14px; border-radius:6px; cursor:pointer; font-size:13px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { background:#f8f9fa; font-size:12px; font-weight:600; text-transform:uppercase; color:#666; padding:12px 16px; text-align:left; border-bottom:1px solid #eee; }
        td { padding:14px 16px; border-bottom:1px solid #f0f0f0; font-size:14px; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-active { background:#e8f5e9; color:#2e7d32; }
        .badge-inactive { background:#fce4ec; color:#c62828; }
        .product-img { width:44px; height:44px; border-radius:8px; object-fit:cover; background:#f0f0f0; display:flex; align-items:center; justify-content:center; color:#999; font-size:18px; }
        .flash-success { background:#e8f5e9; color:#2e7d32; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
        .flash-error   { background:#fce4ec; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
        .empty-state { text-align:center; padding:60px 20px; color:#999; }
        .back-link { color:#00b207; text-decoration:none; font-size:14px; margin-bottom:16px; display:inline-block; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="wrap">
    <a href="<?= url('seller/dashboard') ?>" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>

    <div class="page-header">
        <h1><i class="fa fa-boxes" style="color:#00b207;margin-right:8px;"></i> My Inventory</h1>
        <div style="display:flex;gap:10px;">
            <a href="<?= url('seller/inventory/add') ?>" class="btn-outline"><i class="fa fa-plus"></i> Add Existing Product</a>
            <a href="<?= url('seller/inventory/create-product') ?>" class="btn-green"><i class="fa fa-plus"></i> Create New Product</a>
        </div>
    </div>

    <?php if ($flash = getFlash('success')): ?>
        <div class="flash-success"><?= htmlspecialchars($flash) ?></div>
    <?php elseif ($flash = getFlash('error')): ?>
        <div class="flash-error"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="card">
        <?php if (empty($inventory)): ?>
            <div class="empty-state">
                <i class="fa fa-box-open" style="font-size:48px;margin-bottom:16px;display:block;"></i>
                <p style="font-size:16px;font-weight:500;margin-bottom:8px;">No products in inventory yet.</p>
                <p>Add products to start selling.</p>
                <a href="<?= url('seller/inventory/create-product') ?>" class="btn-green" style="margin-top:16px;">Create First Product</a>
            </div>
        <?php else: ?>
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
                                        <img src="<?= htmlspecialchars($item['image_path']) ?>" class="product-img" alt="">
                                    <?php else: ?>
                                        <div class="product-img"><i class="fa fa-box"></i></div>
                                    <?php endif; ?>
                                    <span style="font-weight:500;"><?= htmlspecialchars($item['product_name']) ?></span>
                                </div>
                            </td>
                            <td style="color:#767676;"><?= htmlspecialchars($item['sku'] ?? '—') ?></td>
                            <td style="font-weight:600;color:#00b207;">$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <span style="<?= $item['stock_quantity'] <= 5 ? 'color:#dc3545;font-weight:600;' : '' ?>">
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
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
