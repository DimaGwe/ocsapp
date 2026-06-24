<?php
$shop = $shop ?? null;
$item = $item ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['language'] ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Inventory Item - OCS Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#333; }
        .wrap { max-width:600px; margin:40px auto; padding:0 16px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:32px; }
        .card h2 { font-size:18px; font-weight:600; margin-bottom:6px; }
        .product-name { color:#767676; font-size:14px; margin-bottom:24px; }
        label { display:block; font-size:13px; font-weight:500; margin-bottom:6px; }
        input, select { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
        input:focus, select:focus { outline:none; border-color:#00b207; }
        .btn-green { background:#00b207; color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; }
        .back-link { color:#00b207; text-decoration:none; font-size:14px; margin-bottom:16px; display:inline-block; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="wrap">
    <a href="<?= url('seller/inventory') ?>" class="back-link"><i class="fa fa-arrow-left"></i> Back to Inventory</a>

    <div class="card">
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

            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="active"   <?= ($item['status'] ?? '') === 'active'   ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive (hidden from buyers)</option>
            </select>

            <button type="submit" class="btn-green">Save Changes</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
