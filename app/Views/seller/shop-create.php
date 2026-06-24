<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Shop - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; }
        .form-wrap { max-width: 600px; margin: 40px auto; padding: 0 16px; }
        .card { background: #fff; border-radius: 16px; padding: 36px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .subtitle { color: #888; font-size: 14px; margin-bottom: 28px; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; color: #555; }
        input, textarea { width: 100%; padding: 11px 14px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box; }
        input:focus, textarea:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
        textarea { height: 100px; resize: vertical; }
        .btn { width: 100%; padding: 13px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn:hover { background: #009206; }
        .alert-error { background: #fce4ec; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>
<div class="form-wrap">
    <div class="card">
        <h1><i class="fas fa-store" style="color:#00b207;"></i> Create Your Shop</h1>
        <p class="subtitle">Fill in the details below. Your shop will be reviewed by our team before going live.</p>

        <?php if ($flash = getFlash('error')): ?>
            <div class="alert-error"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('seller/shop/store') ?>">
            <?= csrfField() ?>
            <div class="form-group">
                <label>Shop Name *</label>
                <input type="text" name="name" required placeholder="e.g. Fresh Produce Market">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Tell customers about your shop..."></textarea>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" placeholder="+1 (514) 000-0000">
            </div>
            <div class="form-group">
                <label>Address</label>
                <input type="text" name="address" placeholder="Shop address">
            </div>
            <button type="submit" class="btn"><i class="fas fa-check"></i> Submit for Approval</button>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
