<?php
$shop       = $shop ?? null;
$categories = $categories ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['language'] ?? 'fr') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product - OCS Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family:'Poppins',sans-serif; background:#f5f5f5; color:#333; }
        .wrap { max-width:680px; margin:40px auto; padding:0 16px; }
        .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:32px; }
        .card h2 { font-size:18px; font-weight:600; margin-bottom:6px; }
        .card p.sub { color:#767676; font-size:13px; margin-bottom:24px; }
        label { display:block; font-size:13px; font-weight:500; margin-bottom:6px; }
        input, select, textarea { width:100%; padding:10px 14px; border:1px solid #ddd; border-radius:8px; font-size:14px; font-family:inherit; box-sizing:border-box; margin-bottom:18px; }
        textarea { resize:vertical; min-height:90px; }
        input:focus, select:focus, textarea:focus { outline:none; border-color:#00b207; }
        .row-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .btn-green { background:#00b207; color:#fff; border:none; padding:12px 24px; border-radius:8px; cursor:pointer; font-size:14px; font-weight:600; width:100%; margin-top:8px; }
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
        <h2>Create New Product</h2>
        <p class="sub">The product will be added to your inventory automatically.</p>

        <form method="POST" action="<?= url('seller/inventory/store-product') ?>">
            <input type="hidden" name="<?= env('CSRF_TOKEN_NAME','_csrf_token') ?>" value="<?= generateCsrfToken() ?>">

            <label for="name">Product Name <span style="color:#dc3545;">*</span></label>
            <input type="text" name="name" id="name" placeholder="e.g. Organic Whole Milk 2L" required maxlength="200">

            <label for="category_id">Category <span style="color:#dc3545;">*</span></label>
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
                    <label for="price">Selling Price (CAD) <span style="color:#dc3545;">*</span></label>
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
                    <label for="weight">Weight (kg, optional)</label>
                    <input type="number" name="weight" id="weight" min="0" step="0.001" placeholder="e.g. 2.0">
                </div>
            </div>

            <button type="submit" class="btn-green"><i class="fa fa-plus"></i> Create Product &amp; Add to Inventory</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
