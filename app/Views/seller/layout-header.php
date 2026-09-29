<?php
/**
 * Seller portal shell (opening half). Pair with layout-footer.php.
 * Updated 2026-09-28: Marché Central visual standard (white sidebar, css/pages/seller-portal.css) and
 * fully bilingual (the nav, topbar and notification panel were English). Pages set $pageTitle to one
 * of the keys of $_pageTitleMap (the English page name); it drives the heading and the active nav item.
 * Also loads components/format-helpers.php (acct_money/acct_date/acct_status, fr-CA formats).
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$user = user();
require_once __DIR__ . '/../components/format-helpers.php';

// Shop approval status drives the Verification nav item: queried directly so every
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
    'Seller Dashboard' => $fr ? 'Tableau de bord' : 'Dashboard',
    'Analytics'        => $fr ? 'Analytique'      : 'Analytics',
    'Orders'           => $fr ? 'Commandes'        : 'Orders',
    'Order Detail'     => $fr ? 'Détail de la commande' : 'Order detail',
    'Inventory'        => $fr ? 'Inventaire'       : 'Inventory',
    'Add Product'      => $fr ? 'Ajouter un produit' : 'Add product',
    'Create Product'   => $fr ? 'Créer un produit' : 'Create product',
    'Edit Product'     => $fr ? 'Modifier le produit' : 'Edit product',
    'Messages'         => $fr ? 'Messages'         : 'Messages',
    'Payouts'          => $fr ? 'Versements'       : 'Payouts',
    'Verification'     => $fr ? 'Vérification'     : 'Verification',
    'Shop Settings'    => $fr ? 'Paramètres'       : 'Settings',
    'Create Shop'      => $fr ? 'Créer votre commerce' : 'Create your shop',
];
$_ptDisplay = $_pageTitleMap[$pageTitle ?? ''] ?? ($pageTitle ?? ($fr ? 'Portail vendeur' : 'Seller portal'));

// Unread message count for sidebar badge
$_sellerUnreadMsgCount = 0;
if (!empty($user['id'])) {
    try {
        $_sellerUnreadMsgCount = \App\Controllers\SellerMessagesController::getUnreadCount((int)$user['id']);
    } catch (\Throwable $e) {
        $_sellerUnreadMsgCount = 0;
    }
}

$_pt = $pageTitle ?? '';
$_navActive = match (true) {
    $_pt === 'Seller Dashboard'                                        => 'dashboard',
    $_pt === 'Analytics'                                               => 'analytics',
    $_pt === 'Orders' || $_pt === 'Order Detail'                       => 'orders',
    str_contains($_pt, 'Inventory') || str_contains($_pt, 'Product')   => 'inventory',
    $_pt === 'Messages'                                                => 'messages',
    $_pt === 'Payouts'                                                 => 'payouts',
    $_pt === 'Verification'                                            => 'verification',
    $_pt === 'Shop Settings'                                           => 'settings',
    default                                                            => '',
};
// [key, path, icon, FR, EN, locked until the shop is approved]
$_navItems = [
    ['dashboard', 'seller/dashboard',     'fa-gauge-high',     'Tableau de bord', 'Dashboard', false],
    ['analytics', 'seller/analytics',     'fa-chart-column',   'Analytique',      'Analytics', true],
    ['orders',    'seller/orders',        'fa-box',            'Commandes',       'Orders',    true],
    ['inventory', 'seller/inventory',     'fa-cubes',          'Inventaire',      'Inventory', true],
    ['messages',  'seller/messages',      'fa-comments',       'Messages',        'Messages',  false],
    ['payouts',   'seller/payouts',       'fa-money-bill-wave','Versements',      'Payouts',   true],
];
if ($_sellerShopUnapproved) {
    $_navItems[] = ['verification', 'seller/verification', 'fa-file-signature', 'Vérification', 'Verification', false];
}
$_navItems[] = ['settings', 'seller/shop/settings', 'fa-gear', 'Paramètres', 'Settings', false];
$_lockedTitle = $fr ? "Verrouillé jusqu'à la vérification du compte" : 'Locked until account verification';
$_uFirst = html_entity_decode((string) ($user['first_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$_uName  = trim($_uFirst . ' ' . html_entity_decode((string) ($user['last_name'] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($_ptDisplay) ?> | <?= $fr ? 'Portail vendeur' : 'Seller portal' ?> | OCSAPP</title>
  <meta name="robots" content="noindex">
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/seller-portal.css') ?>">
  <style>
    /* Legacy tokens: page-level markup that still references them keeps working */
    :root { --primary:#00b207; --primary-dark:#009206; --danger:#B42318; --gray-50:#F7F8F7; --gray-100:#F3F6F3;
            --gray-200:#E5E7E4; --gray-300:#D5DAD6; --gray-400:#929A94; --gray-600:#66706A; --gray-700:#17181A; }
  </style>
</head>
<body class="sp-body">
<div class="sp-shell">
  <aside class="sp-side" id="sidebar" aria-label="<?= $fr ? 'Navigation du portail vendeur' : 'Seller portal navigation' ?>">
    <a class="sp-brand" href="<?= url('seller/dashboard') ?>">
      <img src="<?= asset('images/logo.png') ?>" alt="">
      <div><strong>OCSAPP</strong><span><?= $fr ? 'Portail vendeur' : 'Seller portal' ?></span></div>
    </a>
    <nav class="sp-nav">
      <?php foreach ($_navItems as [$key, $path, $icon, $labelFr, $labelEn, $lockable]): ?>
        <?php $label = $fr ? $labelFr : $labelEn; ?>
        <?php if ($lockable && $_sellerShopUnapproved): ?>
          <span class="sp-locked" title="<?= htmlspecialchars($_lockedTitle) ?>"><i class="fa-solid <?= $icon ?>"></i><?= $label ?><i class="fa-solid fa-lock sp-lock"></i></span>
        <?php else: ?>
          <a href="<?= url($path) ?>" class="<?= $_navActive === $key ? 'active' : '' ?>"<?= $_navActive === $key ? ' aria-current="page"' : '' ?>>
            <i class="fa-solid <?= $icon ?>"></i><?= $label ?>
            <?php if ($key === 'messages'): ?>
              <span id="msgNavBadge" class="sp-nav-badge<?= $_sellerUnreadMsgCount > 0 ? '' : ' hidden' ?>"><?= $_sellerUnreadMsgCount > 0 ? min($_sellerUnreadMsgCount, 99) : '' ?></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>
    <?php if ($_sellerShopUnapproved): ?>
      <div class="sp-side-note">
        <i class="fa-solid fa-circle-info"></i>
        <?= $fr ? 'Téléversez vos documents pour déverrouiller toutes les fonctionnalités.' : 'Upload your documents to unlock every feature.' ?>
        <a href="<?= url('seller/verification') ?>"><?= $fr ? 'Mes documents' : 'My documents' ?> →</a>
      </div>
    <?php endif; ?>
    <div class="sp-side-foot">
      <form id="seller-logout-form" method="POST" action="<?= url('logout') ?>"><?= csrfField() ?>
        <button type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i><?= $fr ? 'Déconnexion' : 'Log out' ?></button>
      </form>
    </div>
  </aside>
  <div class="sp-backdrop" id="sidebarBackdrop" onclick="closeSidebar()"></div>

  <div class="sp-main">
    <header class="sp-top">
      <div class="sp-top-left">
        <button class="sp-burger" type="button" onclick="toggleSidebar()" aria-label="<?= $fr ? 'Menu' : 'Menu' ?>"><i class="fa-solid fa-bars"></i></button>
        <h1><?= htmlspecialchars($_ptDisplay) ?></h1>
      </div>
      <div class="sp-top-right">
        <div class="sp-notif" id="notifWrapper">
          <button class="sp-icon-btn" id="notifBellBtn" type="button" onclick="toggleNotifPanel()" aria-label="<?= $fr ? 'Notifications' : 'Notifications' ?>">
            <i class="fa-solid fa-bell"></i><span class="sp-dot" id="notifBadge">0</span>
          </button>
          <div class="sp-notif-panel" id="notifPanel">
            <div class="sp-notif-head">
              <h4><?= $fr ? 'Notifications' : 'Notifications' ?></h4>
              <button id="notifMarkAllBtn" type="button" onclick="markAllNotifRead()" style="display:none;"><i class="fa-solid fa-check-double"></i> <?= $fr ? 'Tout marquer comme lu' : 'Mark all read' ?></button>
            </div>
            <div class="sp-notif-list" id="notifList"><div class="sp-notif-empty"><?= $fr ? 'Aucune notification' : 'No notifications' ?></div></div>
          </div>
        </div>
        <div class="sp-lang" role="group" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
          <button type="button" class="lang-option<?= $fr ? ' active' : '' ?>" data-lang="fr">FR</button>
          <button type="button" class="lang-option<?= !$fr ? ' active' : '' ?>" data-lang="en">EN</button>
        </div>
        <div class="sp-user">
          <div class="sp-avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr($_uFirst !== '' ? $_uFirst : 'V', 0, 1))) ?></div>
          <div class="sp-user-meta">
            <div class="sp-user-name"><?= htmlspecialchars($_uName !== '' ? $_uName : ($fr ? 'Vendeur' : 'Seller')) ?></div>
            <div class="sp-user-role"><?= $fr ? 'Compte vendeur' : 'Seller account' ?></div>
          </div>
        </div>
      </div>
    </header>

    <main class="sp-content" id="main-content">
      <?php if (hasFlash('error')): ?>
        <div class="sp-alert sp-alert-err"><i class="fa-solid fa-circle-exclamation"></i><div><?= htmlspecialchars(html_entity_decode((string) getFlash('error'), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></div></div>
      <?php endif; ?>
      <?php if (hasFlash('info')): ?>
        <div class="sp-alert sp-alert-info"><i class="fa-solid fa-circle-info"></i><div><?= htmlspecialchars(html_entity_decode((string) getFlash('info'), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></div></div>
      <?php endif; ?>
      <?php if (hasFlash('success')): ?>
        <div class="sp-alert sp-alert-ok"><i class="fa-solid fa-circle-check"></i><div><?= htmlspecialchars(html_entity_decode((string) getFlash('success'), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?></div></div>
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
