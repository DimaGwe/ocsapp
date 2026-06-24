<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$wishlist = $wishlist ?? [];
$cartCount = $cartCount ?? 0;
$user = $user ?? user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Wishlist - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; color: #333; }
        .account-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .account-sidebar { width: 240px; flex-shrink: 0; }
        .account-main { flex: 1; min-width: 0; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-user { text-align: center; padding-bottom: 16px; border-bottom: 1px solid #f0f0f0; margin-bottom: 12px; }
        .sidebar-avatar { width: 64px; height: 64px; border-radius: 50%; background: #00b207; color: #fff; font-size: 24px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; }
        .sidebar-name { font-weight: 600; font-size: 15px; }
        .sidebar-email { font-size: 12px; color: #888; }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .empty-state { text-align: center; padding: 60px 20px; color: #888; }
        .empty-state i { font-size: 56px; color: #ddd; display: block; margin-bottom: 16px; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; }
        .product-card { border: 1px solid #f0f0f0; border-radius: 12px; overflow: hidden; transition: box-shadow .2s; }
        .product-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.1); }
        .product-img { width: 100%; height: 160px; object-fit: cover; background: #f5f5f5; display: flex; align-items: center; justify-content: center; }
        .product-img img { width: 100%; height: 100%; object-fit: cover; }
        .product-info { padding: 12px; }
        .product-name { font-size: 14px; font-weight: 500; margin-bottom: 4px; }
        .product-price { font-size: 16px; font-weight: 700; color: #00b207; }
        .product-actions { display: flex; gap: 8px; margin-top: 10px; }
        .btn-cart { flex: 1; padding: 8px; background: #00b207; color: #fff; border: none; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; }
        .btn-remove { padding: 8px 10px; background: #fce4ec; color: #c62828; border: none; border-radius: 6px; cursor: pointer; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../../components/header.php'; ?>

<div class="account-layout">
    <aside class="account-sidebar">
        <div class="sidebar-card">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($user['first_name'] ?? 'U', 0, 1)) ?></div>
                <div class="sidebar-name"><?= htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></div>
                <div class="sidebar-email"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('account') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('account/orders') ?>"><i class="fas fa-box"></i> My Orders</a>
                <a href="<?= url('account/addresses') ?>"><i class="fas fa-map-marker-alt"></i> Addresses</a>
                <a href="<?= url('account/wishlist') ?>" class="active"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="<?= url('account/settings') ?>"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>
    </aside>

    <main class="account-main">
        <div class="section-card">
            <h2 style="font-size:18px;font-weight:600;margin-bottom:20px;">My Wishlist</h2>

            <?php if (empty($wishlist)): ?>
                <div class="empty-state">
                    <i class="fas fa-heart"></i>
                    <p style="font-size:16px;font-weight:500;color:#555;margin-bottom:8px;">Your wishlist is empty</p>
                    <p>Save items you love to find them easily later.</p>
                    <a href="<?= url('/') ?>" style="display:inline-block;margin-top:16px;padding:10px 24px;background:#00b207;color:#fff;border-radius:8px;text-decoration:none;font-weight:500;">Browse Products</a>
                </div>
            <?php else: ?>
                <div class="product-grid">
                    <?php foreach ($wishlist as $item): ?>
                        <div class="product-card">
                            <div class="product-img">
                                <?php if (!empty($item['image_path'])): ?>
                                    <img src="<?= asset('uploads/' . htmlspecialchars($item['image_path'])) ?>" alt="<?= htmlspecialchars($item['name'] ?? '') ?>">
                                <?php else: ?>
                                    <i class="fas fa-image" style="color:#ddd;font-size:40px;"></i>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <div class="product-name"><?= htmlspecialchars($item['name'] ?? '') ?></div>
                                <div class="product-price">$<?= number_format((float)($item['price'] ?? 0), 2) ?></div>
                                <div class="product-actions">
                                    <button class="btn-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
                                    <form method="POST" action="<?= url('account/wishlist/remove') ?>" style="margin:0;">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="product_id" value="<?= (int)$item['product_id'] ?>">
                                        <button type="submit" class="btn-remove"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
