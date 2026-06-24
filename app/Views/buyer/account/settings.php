<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$user = $user ?? [];
$preferences = $preferences ?? ['email_orders'=>1,'email_promotions'=>0,'email_newsletter'=>0];
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Settings - OCS Marketplace</title>
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
        .section-card { background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,.06); margin-bottom: 20px; }
        .section-title { font-size: 16px; font-weight: 600; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #f0f0f0; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 5px; color: #555; }
        .form-group input { width: 100%; padding: 10px 14px; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .btn-save { padding: 11px 28px; background: #00b207; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .2s; }
        .btn-save:hover { background: #009206; }
        .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-label h4 { font-size: 14px; font-weight: 500; margin: 0 0 2px; }
        .toggle-label p { font-size: 12px; color: #888; margin: 0; }
        .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #e0e0e0; border-radius: 24px; transition: .3s; }
        .toggle-slider:before { content: ''; position: absolute; height: 18px; width: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .3s; }
        input:checked + .toggle-slider { background: #00b207; }
        input:checked + .toggle-slider:before { transform: translateX(20px); }
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: #e8f5e9; color: #2e7d32; }
        .alert-error { background: #fce4ec; color: #c62828; }
        @media (max-width: 768px) { .account-layout { flex-direction: column; } .account-sidebar { width: 100%; } .form-row { grid-template-columns: 1fr; } }
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
                <a href="<?= url('account/wishlist') ?>"><i class="fas fa-heart"></i> Wishlist</a>
                <a href="<?= url('account/settings') ?>" class="active"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>
    </aside>

    <main class="account-main">
        <?php if ($flash = getFlash('success')): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>
        <?php if ($flash = getFlash('error')): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <!-- Profile Information -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-user" style="color:#00b207;margin-right:8px;"></i> Profile Information</div>
            <form method="POST" action="<?= url('account/settings/update-profile') ?>">
                <?= csrfField() ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                </div>
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-lock" style="color:#00b207;margin-right:8px;"></i> Change Password</div>
            <form method="POST" action="<?= url('account/settings/update-password') ?>">
                <?= csrfField() ?>
                <div class="form-group">
                    <label>Current Password</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" required minlength="8">
                    </div>
                </div>
                <button type="submit" class="btn-save">Update Password</button>
            </form>
        </div>

        <!-- Email Preferences -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-bell" style="color:#00b207;margin-right:8px;"></i> Email Notifications</div>
            <form method="POST" action="<?= url('account/settings/update-notifications') ?>">
                <?= csrfField() ?>
                <div class="toggle-row">
                    <div class="toggle-label">
                        <h4>Order Updates</h4>
                        <p>Receive emails about your order status</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_orders" value="1" <?= $preferences['email_orders'] ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-label">
                        <h4>Promotions & Deals</h4>
                        <p>Special offers and discount notifications</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_promotions" value="1" <?= $preferences['email_promotions'] ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-label">
                        <h4>Newsletter</h4>
                        <p>Weekly marketplace updates and news</p>
                    </div>
                    <label class="toggle-switch">
                        <input type="checkbox" name="email_newsletter" value="1" <?= $preferences['email_newsletter'] ? 'checked' : '' ?>>
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div style="margin-top:16px;">
                    <button type="submit" class="btn-save">Save Preferences</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php include __DIR__ . '/../../components/footer.php'; ?>
</body>
</html>
