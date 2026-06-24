<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$cartCount = $cartCount ?? 0;
$shop = $shop ?? [];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Settings - OCS Marketplace</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; }
        .seller-layout { display: flex; max-width: 1200px; margin: 40px auto; gap: 24px; padding: 0 16px; }
        .seller-sidebar { width: 220px; flex-shrink: 0; }
        .seller-main { flex: 1; max-width: 680px; }
        .sidebar-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .sidebar-nav a { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; text-decoration: none; color: #555; font-size: 14px; font-weight: 500; transition: all .2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background: #e8f5e9; color: #00b207; }
        .sidebar-nav a.logout-link:hover { background: #fce4ec; color: #c62828; }
        .sidebar-nav a i { width: 18px; text-align: center; }
        .sidebar-divider { border: none; border-top: 1px solid #f0f0f0; margin: 8px 0; }
        .card { background: #fff; border-radius: 16px; padding: 32px; box-shadow: 0 2px 12px rgba(0,0,0,.07); margin-bottom: 20px; }
        h1 { font-size: 20px; font-weight: 700; margin-bottom: 24px; }
        h2 { font-size: 16px; font-weight: 600; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid #f0f0f0; }
        .form-group { margin-bottom: 18px; }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; color: #555; }
        input, textarea, select { width: 100%; padding: 11px 14px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box; }
        input:focus, textarea:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
        textarea { height: 100px; resize: vertical; }
        .btn { padding: 12px 28px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
        .btn:hover { background: #009206; }
        .alert-success { background: #e8f5e9; color: #2e7d32; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        .alert-error { background: #fce4ec; color: #c62828; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; }
        /* Image upload */
        .image-upload-box { border: 2px dashed #e0e0e0; border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: border-color .2s; position: relative; }
        .image-upload-box:hover { border-color: #00b207; }
        .image-upload-box input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
        .image-preview { width: 100%; max-height: 160px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; display: block; }
        .image-preview-logo { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; margin: 0 auto 8px; display: block; }
        .upload-hint { font-size: 12px; color: #999; }
        @media (max-width: 768px) { .seller-layout { flex-direction: column; } .seller-sidebar { width: 100%; } .form-row { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>
<div class="seller-layout">
    <aside class="seller-sidebar">
        <div class="sidebar-card">
            <div style="font-weight:600;margin-bottom:12px;font-size:15px;">Seller Panel</div>
            <nav class="sidebar-nav">
                <a href="<?= url('seller/dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= url('seller/orders') ?>"><i class="fas fa-box"></i> Orders</a>
                <a href="<?= url('seller/inventory') ?>"><i class="fas fa-cubes"></i> Inventory</a>
                <a href="<?= url('seller/shop/settings') ?>" class="active"><i class="fas fa-cog"></i> Shop Settings</a>
                <hr class="sidebar-divider">
                <a href="#" class="logout-link" onclick="event.preventDefault();document.getElementById('seller-logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
            <form id="seller-logout-form" method="POST" action="<?= url('logout') ?>" style="display:none;">
                <?= csrfField() ?>
            </form>
        </div>
    </aside>

    <main class="seller-main">

        <?php if ($flash = getFlash('success')): ?>
            <div class="alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('error')): ?>
            <div class="alert-error"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <!-- Shop Status -->
        <div style="padding:12px 16px;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.05);margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;">
            <strong>Shop Status:</strong>
            <?php if ($shop['is_active']): ?>
                <span style="color:#2e7d32;font-weight:600;"><i class="fas fa-circle" style="font-size:8px;"></i> Active</span>
            <?php elseif ($shop['is_approved']): ?>
                <span style="color:#888;">Inactive — contact admin to reactivate</span>
            <?php else: ?>
                <span style="color:#e65100;font-weight:600;"><i class="fas fa-clock" style="font-size:8px;"></i> Pending Approval</span>
            <?php endif; ?>
        </div>

        <form method="POST" action="<?= url('seller/shop/update') ?>" enctype="multipart/form-data">
            <?= csrfField() ?>

            <!-- Shop Images -->
            <div class="card">
                <h2><i class="fas fa-image" style="color:#00b207;margin-right:6px;"></i>Shop Images</h2>
                <div class="form-row">
                    <!-- Logo -->
                    <div class="form-group">
                        <label>Shop Logo</label>
                        <div class="image-upload-box" id="logo-box">
                            <input type="file" name="logo" accept="image/*" onchange="previewImage(this,'logo-preview')">
                            <?php if (!empty($shop['logo'])): ?>
                                <img id="logo-preview" class="image-preview-logo" src="<?= asset($shop['logo']) ?>" alt="Logo">
                            <?php else: ?>
                                <img id="logo-preview" class="image-preview-logo" src="" alt="" style="display:none;">
                                <i class="fas fa-store" style="font-size:32px;color:#ccc;display:block;margin-bottom:6px;"></i>
                            <?php endif; ?>
                            <div class="upload-hint">Click to upload logo<br>JPG, PNG, WebP — max 5MB</div>
                        </div>
                    </div>
                    <!-- Banner -->
                    <div class="form-group">
                        <label>Shop Banner / Cover Image</label>
                        <div class="image-upload-box" id="cover-box">
                            <input type="file" name="cover_image" accept="image/*" onchange="previewImage(this,'cover-preview')">
                            <?php if (!empty($shop['cover_image'])): ?>
                                <img id="cover-preview" class="image-preview" src="<?= asset($shop['cover_image']) ?>" alt="Banner">
                            <?php else: ?>
                                <img id="cover-preview" class="image-preview" src="" alt="" style="display:none;">
                                <i class="fas fa-panorama" style="font-size:32px;color:#ccc;display:block;margin-bottom:6px;"></i>
                            <?php endif; ?>
                            <div class="upload-hint">Click to upload banner<br>Recommended: 1200×300px</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="card">
                <h2><i class="fas fa-store" style="color:#00b207;margin-right:6px;"></i>Shop Information</h2>
                <div class="form-group">
                    <label>Shop Name *</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($shop['name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description"><?= htmlspecialchars($shop['description'] ?? '') ?></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?= htmlspecialchars($shop['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($shop['email'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($shop['address'] ?? '') ?>">
                </div>

                <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </main>
</div>
<?php include __DIR__ . '/../components/footer.php'; ?>
<script>
function previewImage(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            // Hide placeholder icon
            var box = input.closest('.image-upload-box');
            var icon = box.querySelector('i.fas:not(.fa-check)');
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</body>
</html>
