<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$user = user();

// Shop approval status drives the Verification nav item — queried directly so every
// seller page shows it consistently regardless of what that page's own controller passes in.
$_sellerShopUnapproved = false;
if (!empty($user['id'])) {
    try {
        $__shopStmt = \Database::getConnection()->prepare("SELECT is_approved FROM shops WHERE seller_id = ? ORDER BY created_at DESC LIMIT 1");
        $__shopStmt->execute([(int)$user['id']]);
        $__shopRow = $__shopStmt->fetch(\PDO::FETCH_ASSOC);
        $_sellerShopUnapproved = $__shopRow && empty($__shopRow['is_approved']);
    } catch (\Throwable $e) {}
}

$_pageTitleMap = [
    'Seller Dashboard' => $fr ? 'Tableau de bord' : 'Seller Dashboard',
    'Analytics'        => $fr ? 'Analytique'      : 'Analytics',
    'Orders'           => $fr ? 'Commandes'        : 'Orders',
    'Inventory'        => $fr ? 'Inventaire'       : 'Inventory',
    'Add Product'      => $fr ? 'Ajouter un produit' : 'Add Product',
    'Edit Product'     => $fr ? 'Modifier le produit' : 'Edit Product',
    'Messages'         => $fr ? 'Messages'         : 'Messages',
    'Payouts'          => $fr ? 'Paiements'        : 'Payouts',
    'Verification'     => $fr ? 'Vérification'     : 'Verification',
    'Shop Settings'    => $fr ? 'Paramètres'       : 'Settings',
];
$_ptDisplay = $_pageTitleMap[$pageTitle ?? ''] ?? ($pageTitle ?? 'Seller Portal');

// Unread message count for sidebar badge
$_sellerUnreadMsgCount = 0;
if (!empty($user['id'])) {
    try {
        $_sellerUnreadMsgCount = \App\Controllers\SellerMessagesController::getUnreadCount((int)$user['id']);
    } catch (\Throwable $e) {
        $_sellerUnreadMsgCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($_ptDisplay) ?> - OCSAPP</title>
  <?= csrfMeta() ?>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

  <style>
    :root {
      --primary: #00b207;
      --primary-dark: #008505;
      --primary-600: #007a05;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-600: #4b5563;
      --gray-700: #374151;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--gray-50);
    }

    .dashboard-wrapper { display: flex; min-height: 100vh; }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: linear-gradient(180deg, #00b207 0%, #007a05 100%);
      color: white;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 200;
      transition: transform 0.3s;
    }

    .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .sidebar-logo { font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
    .sidebar-logo i { font-size: 24px; }
    .sidebar-nav { padding: 20px 0; }

    .nav-link {
      display: flex; align-items: center; padding: 12px 20px;
      color: rgba(255,255,255,0.8); text-decoration: none; transition: all 0.2s; gap: 12px;
    }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); color: white; }
    .nav-link i { width: 20px; text-align: center; }

    .nav-link.locked { color: rgba(255,255,255,0.35); cursor: not-allowed; }
    .nav-link.locked:hover { background: none; color: rgba(255,255,255,0.35); }
    .nav-lock-icon { margin-left: auto; font-size: 10px; opacity: 0.7; }

    /* Main Content */
    .main-content { flex: 1; margin-left: 260px; }

    .topbar {
      background: white; padding: 16px 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      display: flex; justify-content: space-between; align-items: center;
      position: sticky; top: 0; z-index: 100;
    }
    .topbar h1 { font-size: 20px; color: var(--gray-700); }

    .user-menu { display: flex; align-items: center; gap: 12px; }
    .user-info { text-align: right; }
    .user-name { font-weight: 600; font-size: 14px; color: var(--gray-700); }
    .user-role { font-size: 12px; color: var(--gray-600); }

    .btn-logout {
      padding: 8px 16px; background: var(--danger); color: white; border: none;
      border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none;
      display: inline-flex; align-items: center; gap: 6px; font-size: 13px;
    }

    .content-area { padding: 32px; }

    .alert { padding: 14px 20px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 12px; }
    .alert-error { background: #fee; color: #c33; border-left: 4px solid #c33; }
    .alert-success { background: #efe; color: #3c3; border-left: 4px solid #3c3; }

    /* Language Toggle */
    .lang-toggle-btn {
      display: flex; align-items: center; gap: 6px; padding: 7px 12px;
      border: 1px solid #e5e7eb; border-radius: 6px; background: #fff;
      font-size: 13px; font-weight: 500; cursor: pointer; transition: border-color 0.2s;
    }
    .lang-toggle-btn:hover { border-color: var(--primary); }
    .lang-dropdown {
      display: none; position: absolute; top: calc(100% + 6px); right: 0;
      background: #fff; border: 1px solid #e5e7eb; border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1); min-width: 150px; z-index: 1000; overflow: hidden;
    }
    .lang-dropdown.open { display: block; }
    .lang-option { padding: 10px 16px; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; transition: background 0.15s; }
    .lang-option:hover { background: #f3f4f6; }
    .lang-option.active { background: #e8f5e9; color: var(--primary); font-weight: 600; }

    /* Notification Bell */
    .notif-wrapper { position: relative; margin-right: 16px; }
    .notif-bell {
      background: none; border: 1px solid var(--gray-200); border-radius: 8px; padding: 8px 10px;
      cursor: pointer; position: relative; font-size: 16px; color: var(--gray-600); transition: border-color 0.2s;
    }
    .notif-bell:hover { border-color: var(--primary); color: var(--primary); }
    .notif-badge {
      position: absolute; top: -5px; right: -5px; background: var(--danger); color: white;
      font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; border-radius: 9px;
      display: flex; align-items: center; justify-content: center; padding: 0 4px;
    }
    .notif-count-badge {
      display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px;
      padding: 0 5px; background: #ef4444; color: white; font-size: 10px; font-weight: 700;
      border-radius: 9px; margin-left: auto;
    }
    .notif-count-badge.hidden { display: none; }

    .sidebar-toggle { display: none; background: none; border: none; font-size: 22px; color: var(--gray-700); cursor: pointer; padding: 4px 8px; line-height: 1; }
    .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; }
    .sidebar-backdrop.active { display: block; }

    .notif-panel {
      display: none; position: absolute; top: calc(100% + 8px); right: 0;
      width: min(360px, calc(100vw - 32px)); max-height: 420px; background: white;
      border: 1px solid var(--gray-200); border-radius: 12px; box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      z-index: 1000; overflow: hidden;
    }
    .notif-panel.open { display: block; }
    .notif-panel-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; border-bottom: 1px solid var(--gray-200); }
    .notif-panel-header h4 { margin: 0; font-size: 14px; font-weight: 600; color: var(--gray-700); }
    .notif-mark-all { background: none; border: none; font-size: 12px; color: var(--primary); cursor: pointer; font-weight: 500; }
    .notif-list { max-height: 320px; overflow-y: auto; }
    .notif-item { display: flex; gap: 12px; padding: 12px 16px; text-decoration: none; color: inherit; border-bottom: 1px solid var(--gray-100); transition: background 0.15s; }
    .notif-item:hover { background: var(--gray-50); }
    .notif-item.unread { background: #f0fdf4; }
    .notif-item-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--gray-100); display: flex; align-items: center; justify-content: center; color: var(--primary); flex-shrink: 0; font-size: 14px; }
    .notif-item.unread .notif-item-icon { background: #dcfce7; }
    .notif-item-content { flex: 1; min-width: 0; }
    .notif-item-title { font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 2px; }
    .notif-item-msg { font-size: 12px; color: var(--gray-400); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .notif-item-time { font-size: 11px; color: var(--gray-300); margin-top: 3px; }
    .notif-empty { padding: 32px 16px; text-align: center; color: var(--gray-400); font-size: 13px; }
    .notif-empty i { font-size: 24px; display: block; margin-bottom: 8px; }

    /* ── Mobile / Tablet ── */
    @media (max-width: 768px) {
      .sidebar { transform: translateX(-260px); }
      .sidebar.open { transform: translateX(0); }
      .main-content { margin-left: 0; }
      .sidebar-toggle { display: inline-flex; align-items: center; }
      .topbar { padding: 12px 16px; }
      .topbar h1 { font-size: 16px; }
      .content-area { padding: 16px; }
      .user-info { display: none; }
    }
  </style>
</head>
<body>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <img src="<?= url('assets/images/logo.png') ?>" alt="OCSAPP" style="height:36px;width:auto;object-fit:contain;flex-shrink:0;">
          <div style="display:flex;flex-direction:column;line-height:1.3;">
            <span style="font-size:16px;font-weight:700;font-family:'Poppins',sans-serif;color:white;">OCSAPP</span>
            <span style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.7);letter-spacing:0.6px;text-transform:uppercase;font-family:'Poppins',sans-serif;">Seller Portal</span>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <a href="<?= url('seller/dashboard') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Seller Dashboard' ? 'active' : '' ?>">
          <i class="fas fa-chart-line"></i>
          <span>Dashboard</span>
        </a>
        <a href="<?= url('seller/analytics') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Analytics' ? 'active' : '' ?>">
          <i class="fas fa-chart-bar"></i>
          <span>Analytics</span>
        </a>
        <a href="<?= url('seller/orders') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Orders' ? 'active' : '' ?>">
          <i class="fas fa-box"></i>
          <span>Orders</span>
        </a>
        <a href="<?= url('seller/inventory') ?>" class="nav-link <?= strpos($pageTitle ?? '', 'Inventory') !== false || strpos($pageTitle ?? '', 'Product') !== false ? 'active' : '' ?>">
          <i class="fas fa-cubes"></i>
          <span>Inventory</span>
        </a>
        <a href="<?= url('seller/messages') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Messages' ? 'active' : '' ?>">
          <i class="fas fa-comments"></i>
          <span>Messages</span>
          <span id="msgNavBadge" class="notif-count-badge<?= $_sellerUnreadMsgCount > 0 ? '' : ' hidden' ?>"><?= $_sellerUnreadMsgCount > 0 ? min($_sellerUnreadMsgCount, 99) : '' ?></span>
        </a>
        <a href="<?= url('seller/payouts') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Payouts' ? 'active' : '' ?>">
          <i class="fas fa-dollar-sign"></i>
          <span>Payouts</span>
        </a>
        <?php if ($_sellerShopUnapproved): ?>
        <a href="<?= url('seller/verification') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Verification' ? 'active' : '' ?>">
          <i class="fas fa-file-signature"></i>
          <span>Verification</span>
        </a>
        <?php endif; ?>
        <a href="<?= url('seller/shop/settings') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Shop Settings' ? 'active' : '' ?>">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>

        <hr style="border:none;border-top:1px solid rgba(255,255,255,0.15);margin:10px 20px;">
        <a href="#" class="nav-link" onclick="event.preventDefault();document.getElementById('seller-logout-form').submit();">
          <i class="fas fa-sign-out-alt"></i>
          <span>Logout</span>
        </a>
        <form id="seller-logout-form" method="POST" action="<?= url('logout') ?>" style="display:none;"><?= csrfField() ?></form>
      </nav>
    </aside>

    <!-- Mobile backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

    <!-- Main Content -->
    <main class="main-content">
      <!-- Topbar -->
      <div class="topbar">
        <div style="display:flex;align-items:center;gap:10px;">
          <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
          </button>
          <h1><?= htmlspecialchars($_ptDisplay) ?></h1>
        </div>
        <div class="user-menu">
          <!-- Notification Bell -->
          <div class="notif-wrapper" id="notifWrapper">
            <button class="notif-bell" id="notifBellBtn" type="button" onclick="toggleNotifPanel()">
              <i class="fas fa-bell"></i>
              <span class="notif-badge" id="notifBadge" style="display:none;">0</span>
            </button>
            <div class="notif-panel" id="notifPanel">
              <div class="notif-panel-header">
                <h4><i class="fas fa-bell" style="margin-right:6px;"></i> Notifications</h4>
                <button class="notif-mark-all" id="notifMarkAllBtn" onclick="markAllNotifRead()" style="display:none;">
                  <i class="fas fa-check-double"></i> Mark all read
                </button>
              </div>
              <div class="notif-list" id="notifList">
                <div class="notif-empty">
                  <i class="fas fa-bell-slash"></i>
                  No notifications
                </div>
              </div>
            </div>
          </div>

          <!-- Language Selector -->
          <div class="language-selector" style="position:relative;margin-right:16px;">
            <button class="lang-toggle-btn" id="sellerLangBtn" type="button">
              <i class="fas fa-globe"></i>
              <span><?= strtoupper($currentLang) ?></span>
              <span style="font-size:10px;">&#9660;</span>
            </button>
            <div class="lang-dropdown" id="sellerLangDropdown">
              <div class="lang-option <?= $currentLang === 'en' ? 'active' : '' ?>" data-lang="en">
                <span>&#127482;&#127480;</span> English
              </div>
              <div class="lang-option <?= $currentLang === 'fr' ? 'active' : '' ?>" data-lang="fr">
                <span>&#127467;&#127479;</span> Fran&ccedil;ais
              </div>
            </div>
          </div>

          <div class="user-info">
            <div class="user-name"><?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'Seller') ?></div>
            <div class="user-role">Seller Account</div>
          </div>
          <a href="#" class="btn-logout" onclick="event.preventDefault();document.getElementById('seller-logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </div>

      <!-- Content Area -->
      <div class="content-area">
        <?php if (hasFlash('error')): ?>
          <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= getFlash('error') ?>
          </div>
        <?php endif; ?>

        <?php if (hasFlash('success')): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= getFlash('success') ?>
          </div>
        <?php endif; ?>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarBackdrop').classList.toggle('active');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarBackdrop').classList.remove('active');
}
</script>
