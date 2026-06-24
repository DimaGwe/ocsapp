<?php
$shop              = $shop ?? null;
$availableProducts = $availableProducts ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['language'] ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product to Inventory - OCS Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#333; }
        .wrap { max-width:640px; margin:40px auto; padding:0 16px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:32px; }
        .card h2 { font-size:18px; font-weight:600; margin-bottom:24px; }
        label { display:block; font-size:13px; font-weight:500; margin-bottom:6px; }
        input, select { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
        input:focus, select:focus { outline:none; border-color:#00b207; }
        .btn-green { background:#00b207; color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; }
        .back-link { color:#00b207; text-decoration:none; font-size:14px; margin-bottom:16px; display:inline-block; }
        .flash-error { background:#fce4ec; color:#c62828; padding:12px 16px; border-radius:8px; margin-bottom:16px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="wrap">
    <a href="<?= url('seller/inventory') ?>" class="back-link"><i class="fa fa-arrow-left"></i> Back to Inventory</a>

    <?php if ($flash = getFlash('error')): ?>
        <div class="flash-error"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Add Product to Inventory</h2>

        <?php if (empty($availableProducts)): ?>
            <p style="color:#767676;margin-bottom:20px;">All available products are already in your inventory.</p>
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
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
