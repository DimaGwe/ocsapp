<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$t = getTranslations($currentLang);

$_pageTitleMap = [
    'Supplier Dashboard'  => $fr ? 'Tableau de bord'          : 'Supplier Dashboard',
    'Analytics'           => $fr ? 'Analytique'               : 'Analytics',
    'My Products'         => $fr ? 'Mes produits'             : 'My Products',
    'Add New Product'     => $fr ? 'Ajouter un produit'       : 'Add New Product',
    'Edit Product'        => $fr ? 'Modifier le produit'      : 'Edit Product',
    'Purchase Orders'     => $fr ? 'Bons de commande'         : 'Purchase Orders',
    'Sales Orders'        => $fr ? 'Bons de vente'            : 'Sales Orders',
    'Receivables'         => $fr ? 'Comptes clients'          : 'Receivables',
    'Invoices & Payments' => $fr ? 'Factures et paiements'    : 'Invoices & Payments',
    'Schedule Pickup'     => $fr ? 'Planifier un ramassage'   : 'Schedule Pickup',
    'My Documents'        => $fr ? 'Mes documents'            : 'My Documents',
    'My Emails'           => $fr ? 'Mes courriels'            : 'My Emails',
    'Messages'            => $fr ? 'Messages'                 : 'Messages',
    'Settings'            => $fr ? 'Paramètres'               : 'Settings',
];
$_ptDisplay = $_pageTitleMap[$pageTitle ?? ''] ?? ($pageTitle ?? ($t['sup_portal'] ?? 'Supplier Portal'));

// Kick deleted suppliers — if the record is gone, destroy session and redirect.
if (!empty($_SESSION['supplier_id'])) {
    try {
        $__supCheck = \Database::getConnection()->prepare("SELECT id FROM suppliers WHERE id = ? LIMIT 1");
        $__supCheck->execute([(int)$_SESSION['supplier_id']]);
        if (!$__supCheck->fetch()) {
            session_unset();
            session_destroy();
            header('Location: ' . url('supplier/login'));
            exit;
        }
    } catch (\Throwable $e) {}
}

// Unread message count for sidebar badge
$_unreadMsgCount = 0;
if (!empty($_SESSION['supplier_id'])) {
    try {
        $_unreadMsgCount = \App\Controllers\SupplierMessagesController::getUnreadCount((int)$_SESSION['supplier_id']);
    } catch (\Throwable $e) {
        // table may not exist yet on first deploy
        $_unreadMsgCount = 0;
    }
}

// Unpaid supplier invoices badge
$_supplierUnpaidInvoiceCount = 0;

// Sidebar PO badge: pending acceptance (sent) — shown on Purchase Orders nav link
$_pendingPoCount = 0;
// Active PO badge: orders accepted/preparing (in-progress, need supplier action)
$_activePoCount  = 0;
if (!empty($_SESSION['supplier_id']) && ($_SESSION['supplier_status'] ?? '') !== 'pending_verification') {
    try {
        $__db = \Database::getConnection();
        $__poStmt = $__db->prepare(
            "SELECT status, COUNT(*) as cnt FROM purchase_orders
             WHERE supplier_id = ? AND status IN ('sent','accepted','preparing','ready_for_pickup')
             GROUP BY status"
        );
        $__poStmt->execute([(int)$_SESSION['supplier_id']]);
        foreach ($__poStmt->fetchAll(\PDO::FETCH_ASSOC) as $__row) {
            if ($__row['status'] === 'sent') $_pendingPoCount += (int)$__row['cnt'];
            else $_activePoCount += (int)$__row['cnt'];
        }
    } catch (\Throwable $e) {
        $_pendingPoCount = 0;
        $_activePoCount  = 0;
    }
}
if (!empty($_SESSION['supplier_id']) && ($_SESSION['supplier_status'] ?? '') !== 'pending_verification') {
    try {
        $__invS = \Database::getConnection()->prepare(
            "SELECT COUNT(*) FROM supplier_invoices WHERE supplier_id = ? AND status IN ('sent','overdue','partial')"
        );
        $__invS->execute([(int)$_SESSION['supplier_id']]);
        $_supplierUnpaidInvoiceCount = (int)$__invS->fetchColumn();
    } catch (\Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($_ptDisplay) ?></title>
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

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: var(--gray-50);
    }

    .dashboard-wrapper {
      display: flex;
      min-height: 100vh;
    }

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

    .sidebar-header {
      padding: 24px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo {
      font-size: 20px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-logo i {
      font-size: 24px;
    }

    .sidebar-nav {
      padding: 20px 0;
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 12px 20px;
      color: rgba(255,255,255,0.8);
      text-decoration: none;
      transition: all 0.2s;
      gap: 12px;
    }

    .nav-link:hover, .nav-link.active {
      background: rgba(255,255,255,0.1);
      color: white;
    }

    .nav-link i {
      width: 20px;
      text-align: center;
    }

    .nav-link.locked {
      color: rgba(255,255,255,0.35);
      cursor: not-allowed;
    }
    .nav-link.locked:hover {
      background: none;
      color: rgba(255,255,255,0.35);
    }
    .nav-lock-icon {
      margin-left: auto;
      font-size: 10px;
      opacity: 0.7;
    }
    .nav-section-label {
      padding: 16px 20px 6px;
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: rgba(255,255,255,0.4);
    }
    .nav-unlock-hint {
      margin: 12px 12px 4px;
      background: rgba(255,255,255,0.08);
      border: 1px solid rgba(255,255,255,0.15);
      border-radius: 8px;
      padding: 10px 12px;
      font-size: 11px;
      color: rgba(255,255,255,0.6);
      line-height: 1.5;
    }
    .nav-unlock-hint a {
      color: rgba(255,255,255,0.9);
      font-weight: 600;
      text-decoration: underline;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 260px;
    }

    .topbar {
      background: white;
      padding: 16px 32px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .topbar h1 {
      font-size: 20px;
      color: var(--gray-700);
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .user-info {
      text-align: right;
    }

    .user-name {
      font-weight: 600;
      font-size: 14px;
      color: var(--gray-700);
    }

    .user-role {
      font-size: 12px;
      color: var(--gray-600);
    }

    .btn-logout {
      padding: 8px 16px;
      background: var(--danger);
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 6px;
      font-size: 13px;
    }

    .content-area {
      padding: 32px;
    }

    .alert {
      padding: 14px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .alert-error {
      background: #fee;
      color: #c33;
      border-left: 4px solid #c33;
    }

    .alert-success {
      background: #efe;
      color: #3c3;
      border-left: 4px solid #3c3;
    }

    .alert-password-reminder {
      background: #fef3c7;
      color: #92400e;
      border-left: 4px solid #f59e0b;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .alert-verification {
      background: #eff6ff;
      color: #1e40af;
      border-left: 4px solid #3b82f6;
    }

    .alert-verification .verification-steps {
      margin: 10px 0 0;
      padding-left: 20px;
      font-size: 13px;
      line-height: 1.8;
    }

    .alert-verification .verification-steps li {
      color: #1e40af;
    }

    .alert-verification .verification-steps li.done {
      color: #059669;
      text-decoration: line-through;
    }

    /* Language Toggle */
    .lang-toggle-btn {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 7px 12px;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      background: #fff;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    .lang-toggle-btn:hover {
      border-color: var(--primary);
    }
    .lang-dropdown {
      display: none;
      position: absolute;
      top: calc(100% + 6px);
      right: 0;
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      min-width: 150px;
      z-index: 1000;
      overflow: hidden;
    }
    .lang-dropdown.open {
      display: block;
    }

    /* Notification Bell */
    .notif-wrapper {
      position: relative;
      margin-right: 16px;
    }
    .notif-bell {
      background: none;
      border: 1px solid var(--gray-200);
      border-radius: 8px;
      padding: 8px 10px;
      cursor: pointer;
      position: relative;
      font-size: 16px;
      color: var(--gray-600);
      transition: border-color 0.2s;
    }
    .notif-bell:hover {
      border-color: var(--primary);
      color: var(--primary);
    }
    .notif-badge {
      position: absolute;
      top: -5px;
      right: -5px;
      background: var(--danger);
      color: white;
      font-size: 10px;
      font-weight: 700;
      min-width: 18px;
      height: 18px;
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 4px;
    }
    .notif-count-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 18px;
      height: 18px;
      padding: 0 5px;
      background: #ef4444;
      color: white;
      font-size: 10px;
      font-weight: 700;
      border-radius: 9px;
      margin-left: auto;
    }
    .notif-count-badge.hidden { display: none; }
    .sidebar-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 22px;
      color: var(--gray-700);
      cursor: pointer;
      padding: 4px 8px;
      line-height: 1;
    }
    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 199;
    }
    .sidebar-backdrop.active { display: block; }

    .notif-panel {
      display: none;
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      width: min(360px, calc(100vw - 32px));
      max-height: 420px;
      background: white;
      border: 1px solid var(--gray-200);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      z-index: 1000;
      overflow: hidden;
    }
    .notif-panel.open {
      display: block;
    }
    .notif-panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 16px;
      border-bottom: 1px solid var(--gray-200);
    }
    .notif-panel-header h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 600;
      color: var(--gray-700);
    }
    .notif-mark-all {
      background: none;
      border: none;
      font-size: 12px;
      color: var(--primary);
      cursor: pointer;
      font-weight: 500;
    }
    .notif-list {
      max-height: 320px;
      overflow-y: auto;
    }
    .notif-item {
      display: flex;
      gap: 12px;
      padding: 12px 16px;
      text-decoration: none;
      color: inherit;
      border-bottom: 1px solid var(--gray-100);
      transition: background 0.15s;
    }
    .notif-item:hover {
      background: var(--gray-50);
    }
    .notif-item.unread {
      background: #f0fdf4;
    }
    .notif-item-icon {
      width: 36px;
      height: 36px;
      border-radius: 8px;
      background: var(--gray-100);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      flex-shrink: 0;
      font-size: 14px;
    }
    .notif-item.unread .notif-item-icon {
      background: #dcfce7;
    }
    .notif-item-content {
      flex: 1;
      min-width: 0;
    }
    .notif-item-title {
      font-size: 13px;
      font-weight: 600;
      color: var(--gray-700);
      margin-bottom: 2px;
    }
    .notif-item-msg {
      font-size: 12px;
      color: var(--gray-400);
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    .notif-item-time {
      font-size: 11px;
      color: var(--gray-300);
      margin-top: 3px;
    }
    .notif-empty {
      padding: 32px 16px;
      text-align: center;
      color: var(--gray-400);
      font-size: 13px;
    }
    .notif-empty i {
      font-size: 24px;
      display: block;
      margin-bottom: 8px;
    }
    .lang-option {
      padding: 10px 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 13px;
      transition: background 0.15s;
    }
    .lang-option:hover {
      background: #f3f4f6;
    }
    .lang-option.active {
      background: #e8f5e9;
      color: var(--primary);
      font-weight: 600;
    }

    /* ── Mobile / Tablet ── */
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-260px);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
      .sidebar-toggle {
        display: inline-flex;
        align-items: center;
      }
      .topbar {
        padding: 12px 16px;
      }
      .topbar h1 {
        font-size: 16px;
      }
      .content-area {
        padding: 16px;
      }
      .user-info {
        display: none;
      }
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
            <span style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.7);letter-spacing:0.6px;text-transform:uppercase;font-family:'Poppins',sans-serif;"><?= $fr ? 'Portail fournisseur' : 'Supplier Portal' ?></span>
          </div>
        </div>
      </div>

      <?php $isPendingVerification = ($_SESSION['supplier_status'] ?? '') === 'pending_verification'; ?>
      <nav class="sidebar-nav">

        <?php /* ── Always accessible ── */ ?>
        <a href="<?= url('supplier/dashboard') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Supplier Dashboard' ? 'active' : '' ?>">
          <i class="fas fa-chart-line"></i>
          <span><?= $t['sup_dashboard'] ?? 'Dashboard' ?></span>
        </a>

        <?php /* ── Locked for pending, visible to tease what's coming ── */ ?>
        <?php if ($isPendingVerification): ?>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-chart-bar"></i>
          <span><?= $t['sup_analytics'] ?? 'Analytics' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-box"></i>
          <span><?= $t['sup_my_products'] ?? 'My Products' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-file-invoice"></i>
          <span><?= $t['sup_purchase_orders'] ?? 'Purchase Orders' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-receipt"></i>
          <span><?= $t['sup_sales_orders'] ?? 'Sales Orders' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-hand-holding-usd"></i>
          <span><?= $t['sup_receivables'] ?? 'Receivables' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-file-invoice-dollar"></i>
          <span><?= $t['sup_invoices'] ?? 'Invoices & Payments' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <?php else: ?>
        <a href="<?= url('supplier/analytics') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Analytics' ? 'active' : '' ?>">
          <i class="fas fa-chart-bar"></i>
          <span><?= $t['sup_analytics'] ?? 'Analytics' ?></span>
        </a>
        <a href="<?= url('supplier/products') ?>" class="nav-link <?= strpos($pageTitle ?? '', 'Product') !== false ? 'active' : '' ?>">
          <i class="fas fa-box"></i>
          <span><?= $t['sup_my_products'] ?? 'My Products' ?></span>
        </a>
        <a href="<?= url('supplier/orders') ?>" class="nav-link <?= strpos($pageTitle ?? '', 'Purchase Order') !== false ? 'active' : '' ?>">
          <i class="fas fa-file-invoice"></i>
          <span><?= $t['sup_purchase_orders'] ?? 'Purchase Orders' ?></span>
          <?php $__totalPoBadge = $_pendingPoCount + $_activePoCount; ?>
          <?php if ($_pendingPoCount > 0): ?>
            <span id="poNavBadge" class="notif-count-badge" title="<?= $fr ? $_pendingPoCount . ' en attente d\'acceptation' : $_pendingPoCount . ' pending acceptance' ?>"><?= $_pendingPoCount > 9 ? '9+' : $_pendingPoCount ?></span>
          <?php else: ?>
            <span id="poNavBadge" class="notif-count-badge hidden"></span>
          <?php endif; ?>
          <?php if ($_activePoCount > 0): ?>
            <span id="poActiveNavBadge" class="notif-count-badge" title="<?= $fr ? $_activePoCount . ' en cours' : $_activePoCount . ' in progress' ?>" style="background:#d97706;"><?= $_activePoCount > 9 ? '9+' : $_activePoCount ?></span>
          <?php else: ?>
            <span id="poActiveNavBadge" class="notif-count-badge hidden" style="background:#d97706;"></span>
          <?php endif; ?>
        </a>
        <a href="<?= url('supplier/sales-orders') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Sales Orders' ? 'active' : '' ?>">
          <i class="fas fa-receipt"></i>
          <span><?= $t['sup_sales_orders'] ?? 'Sales Orders' ?></span>
          <?php
            $__activeSoCount = 0;
            try {
                $__soStmt = \Database::getConnection()->prepare("SELECT COUNT(*) FROM purchase_orders WHERE supplier_id = ? AND so_number IS NOT NULL AND status NOT IN ('completed','cancelled')");
                $__soStmt->execute([(int)$_SESSION['supplier_id']]);
                $__activeSoCount = (int)$__soStmt->fetchColumn();
            } catch (\Throwable $e) {}
          ?>
          <span id="soNavBadge" class="notif-count-badge<?= $__activeSoCount > 0 ? '' : ' hidden' ?>" style="background:#d97706;"><?= $__activeSoCount > 9 ? '9+' : $__activeSoCount ?></span>
        </a>
        <a href="<?= url('supplier/receivables') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Receivables' ? 'active' : '' ?>">
          <i class="fas fa-hand-holding-usd"></i>
          <span><?= $t['sup_receivables'] ?? 'Receivables' ?></span>
          <?php
            $__unpaidRecCount = 0;
            try {
                $__recStmt = \Database::getConnection()->prepare("SELECT COUNT(*) FROM supplier_invoices WHERE supplier_id = ? AND status NOT IN ('paid','cancelled')");
                $__recStmt->execute([(int)$_SESSION['supplier_id']]);
                $__unpaidRecCount = (int)$__recStmt->fetchColumn();
            } catch (\Throwable $e) {}
          ?>
          <span id="supReceivablesBadge" class="notif-count-badge<?= $__unpaidRecCount > 0 ? '' : ' hidden' ?>" style="background:#d97706;"><?= $__unpaidRecCount > 9 ? '9+' : $__unpaidRecCount ?></span>
        </a>
        <a href="<?= url('supplier/invoices') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Invoices & Payments' ? 'active' : '' ?>">
          <i class="fas fa-file-invoice-dollar"></i>
          <span><?= $t['sup_invoices'] ?? 'Invoices & Payments' ?></span>
          <span id="supInvoiceBadge" class="notif-count-badge<?= $_supplierUnpaidInvoiceCount > 0 ? '' : ' hidden' ?>"><?= $_supplierUnpaidInvoiceCount > 9 ? '9+' : $_supplierUnpaidInvoiceCount ?></span>
        </a>
        <?php endif; ?>

        <?php /* ── Always accessible ── */ ?>
        <a href="<?= url('supplier/documents') ?>" class="nav-link <?= ($pageTitle ?? '') === 'My Documents' ? 'active' : '' ?>">
          <i class="fas fa-folder-open"></i>
          <span><?= $t['sup_documents'] ?? 'My Documents' ?></span>
        </a>
        <a href="<?= url('supplier/emails') ?>" class="nav-link <?= ($pageTitle ?? '') === 'My Emails' ? 'active' : '' ?>">
          <i class="fas fa-envelope-open-text"></i>
          <span><?= $t['sup_emails'] ?? 'My Emails' ?></span>
        </a>
        <a href="<?= url('supplier/messages') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Messages' ? 'active' : '' ?>">
          <i class="fas fa-comments"></i>
          <span><?= $t['sup_messages'] ?? 'Messages' ?></span>
          <span id="msgNavBadge" class="notif-count-badge<?= $_unreadMsgCount > 0 ? '' : ' hidden' ?>"><?= $_unreadMsgCount > 0 ? min($_unreadMsgCount, 99) : '' ?></span>
        </a>

        <?php /* ── Schedule Pickup: locked for pending, only shown for active otherwise ── */ ?>
        <?php if ($isPendingVerification): ?>
        <span class="nav-link locked" title="<?= $t['sup_locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-truck-loading"></i>
          <span><?= $t['sup_schedule_pickup'] ?? 'Schedule Pickup' ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <?php elseif (($_SESSION['supplier_status'] ?? '') === 'active'): ?>
        <a href="<?= url('supplier/pickup') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Schedule Pickup' ? 'active' : '' ?>">
          <i class="fas fa-truck-loading"></i>
          <span><?= $t['sup_schedule_pickup'] ?? 'Schedule Pickup' ?></span>
        </a>
        <?php endif; ?>

        <?php /* ── Settings: always accessible ── */ ?>
        <a href="<?= url('supplier/settings') ?>" class="nav-link <?= ($pageTitle ?? '') === 'Settings' ? 'active' : '' ?>">
          <i class="fas fa-cog"></i>
          <span><?= $t['sup_settings'] ?? 'Settings' ?></span>
        </a>

        <?php if ($isPendingVerification): ?>
        <div class="nav-unlock-hint">
          <i class="fas fa-lock" style="margin-right:5px;"></i>
          <?= $t['sup_unlock_hint'] ?? 'Upload your documents to unlock all features.' ?>
          <br><a href="<?= url('supplier/documents') ?>"><?= $t['sup_go_to_docs'] ?? 'Go to Documents &rarr;' ?></a>
        </div>
        <?php endif; ?>

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
                <h4><i class="fas fa-bell" style="margin-right:6px;"></i> <?= $fr ? 'Notifications' : 'Notifications' ?></h4>
                <button class="notif-mark-all" id="notifMarkAllBtn" onclick="markAllNotifRead()" style="display:none;">
                  <i class="fas fa-check-double"></i> <?= $fr ? 'Tout marquer comme lu' : 'Mark all read' ?>
                </button>
              </div>
              <div class="notif-list" id="notifList">
                <div class="notif-empty">
                  <i class="fas fa-bell-slash"></i>
                  <?= $fr ? 'Aucune notification' : 'No notifications' ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Language Selector -->
          <div class="language-selector" style="position:relative;margin-right:16px;">
            <button class="lang-toggle-btn" id="supplierLangBtn" type="button">
              <i class="fas fa-globe"></i>
              <span><?= strtoupper($currentLang) ?></span>
              <span style="font-size:10px;">&#9660;</span>
            </button>
            <div class="lang-dropdown" id="supplierLangDropdown">
              <div class="lang-option <?= $currentLang === 'en' ? 'active' : '' ?>" data-lang="en">
                <span>&#127482;&#127480;</span> English
              </div>
              <div class="lang-option <?= $currentLang === 'fr' ? 'active' : '' ?>" data-lang="fr">
                <span>&#127467;&#127479;</span> Fran&ccedil;ais
              </div>
            </div>
          </div>

          <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['supplier_name'] ?? 'Supplier') ?></div>
            <div class="user-role"><?= $t['sup_account'] ?? 'Supplier Account' ?></div>
          </div>
          <a href="<?= url('supplier/logout') ?>" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <?= $t['sup_logout'] ?? 'Logout' ?>
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

        <?php if (!empty($_SESSION['supplier_password_reminder']) && empty($_SESSION['supplier_reminder_dismissed'])): ?>
          <div class="alert alert-password-reminder" id="passwordReminderBanner">
            <div style="display:flex;align-items:center;gap:12px;flex:1;flex-wrap:wrap;">
              <i class="fas fa-shield-alt" style="font-size:20px;color:#d97706;"></i>
              <div>
                <?php if ($_SESSION['supplier_password_reminder'] === 'first'): ?>
                  <?= $t['sup_pw_welcome'] ?? 'Welcome! For your security, please change your temporary password.' ?>
                <?php else: ?>
                  <?= $t['sup_pw_reminder'] ?? 'Reminder: You\'re still using a temporary password. Please update it for your account security.' ?>
                <?php endif; ?>
                <a href="<?= url('supplier/settings') ?>" style="color:#00b207;font-weight:600;margin-left:4px;"><?= $t['sup_go_to_settings'] ?? 'Go to Settings' ?> &rarr;</a>
              </div>
            </div>
            <button onclick="dismissPasswordReminder()" style="background:none;border:none;color:#92400e;cursor:pointer;font-size:18px;padding:4px 8px;line-height:1;" title="Dismiss">&times;</button>
          </div>
        <?php endif; ?>

        <?php if ($isPendingVerification):
          // Make days-left available to dashboard view
          $deadlineStr = $_SESSION['supplier_verification_deadline'] ?? null;
          $GLOBALS['_supplierDaysLeft'] = $deadlineStr ? max(0, (int)ceil((strtotime($deadlineStr) - time()) / 86400)) : null;
        endif; ?>

<script>
// Sidebar toggle (mobile)
function toggleSidebar() {
  var sidebar = document.getElementById('sidebar');
  var backdrop = document.getElementById('sidebarBackdrop');
  sidebar.classList.toggle('open');
  backdrop.classList.toggle('active');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarBackdrop').classList.remove('active');
}

// ── Unified badge refresh — single API call updates every sidebar badge ──────
(function pollNotifCount() {
  var prevNotifCount = -1;
  var prevPoCount    = -1;

  // Delegate to footer's shared AudioContext if available, else use own
  function playChime() {
    if (localStorage.getItem('sup_sound_enabled') === 'off') return;
    if (typeof playChimeFooter === 'function') { playChimeFooter(); return; }
    try {
      var ctx = new (window.AudioContext || window.webkitAudioContext)();
      if (ctx.state === 'suspended') ctx.resume();
      [[880, 0], [1047, 0.15], [1319, 0.30]].forEach(function(note) {
        var osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine'; osc.frequency.value = note[0];
        gain.gain.setValueAtTime(0, ctx.currentTime + note[1]);
        gain.gain.linearRampToValueAtTime(0.12, ctx.currentTime + note[1] + 0.04);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + note[1] + 0.5);
        osc.start(ctx.currentTime + note[1]);
        osc.stop(ctx.currentTime + note[1] + 0.55);
      });
    } catch(e) {}
  }

  // Exported so the SSE handler in footer can call it directly
  window._refreshAllSupplierBadges = function refreshNotifCount() {
    fetch('<?= url('api/supplier/notifications/count') ?>', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data || !data.success) return;

        var n   = data.unread_count || 0;
        var po  = data.pending_po_count || 0;
        var act = data.active_po_count || 0;
        var inv = data.unpaid_invoice_count || 0;
        var so  = data.active_so_count || 0;
        var rec = data.unpaid_receivables_count || 0;
        var msg = data.unread_msg_count || 0;

        // ── Bell badge ──────────────────────────────────────────────────────
        var bell = document.getElementById('notifBadge');
        if (bell) {
          if (n > 0) { bell.textContent = n > 9 ? '9+' : n; bell.style.display = ''; }
          else        { bell.style.display = 'none'; }
        }

        // ── Purchase Orders — pending acceptance (red) ──────────────────────
        var poBadge = document.getElementById('poNavBadge');
        if (poBadge) {
          if (po > 0) { poBadge.textContent = po > 9 ? '9+' : po; poBadge.classList.remove('hidden'); }
          else         { poBadge.classList.add('hidden'); }
        }

        // ── Purchase Orders — in progress (amber) ───────────────────────────
        var poActiveBadge = document.getElementById('poActiveNavBadge');
        if (poActiveBadge) {
          if (act > 0) { poActiveBadge.textContent = act > 9 ? '9+' : act; poActiveBadge.classList.remove('hidden'); }
          else          { poActiveBadge.classList.add('hidden'); }
        }

        // ── Invoices & Payments ─────────────────────────────────────────────
        var invBadge = document.getElementById('supInvoiceBadge');
        if (invBadge) {
          if (inv > 0) { invBadge.textContent = inv > 9 ? '9+' : inv; invBadge.classList.remove('hidden'); }
          else          { invBadge.classList.add('hidden'); }
        }

        // ── Sales Orders — active (not completed/cancelled) ─────────────────
        var soBadge = document.getElementById('soNavBadge');
        if (soBadge) {
          if (so > 0) { soBadge.textContent = so > 9 ? '9+' : so; soBadge.classList.remove('hidden'); }
          else         { soBadge.classList.add('hidden'); }
        }

        // ── Receivables — unpaid ─────────────────────────────────────────────
        var recBadge = document.getElementById('supReceivablesBadge');
        if (recBadge) {
          if (rec > 0) { recBadge.textContent = rec > 9 ? '9+' : rec; recBadge.classList.remove('hidden'); }
          else          { recBadge.classList.add('hidden'); }
        }

        // ── Messages ────────────────────────────────────────────────────────
        var msgBadge = document.getElementById('msgNavBadge');
        if (msgBadge) {
          if (msg > 0) { msgBadge.textContent = msg > 99 ? '99+' : msg; msgBadge.style.display = ''; }
          else          { msgBadge.style.display = 'none'; }
        }

        // ── Sound: play chime if new notifications or new POs arrived ───────
        var isNewActivity = (prevNotifCount >= 0 && n > prevNotifCount) ||
                            (prevPoCount >= 0    && po > prevPoCount);
        if (isNewActivity) {
          playChime();
        }

        // ── Dashboard auto-reload: refresh page content when new PO arrives ─
        if (prevPoCount >= 0 && po > prevPoCount && document.getElementById('supplierDashboard')) {
          setTimeout(function() { window.location.reload(); }, 1200); // slight delay so chime starts first
        }

        prevNotifCount = n;
        prevPoCount    = po;
      })
      .catch(function() {});
  }

  setTimeout(refreshNotifCount, 2000);
  setInterval(refreshNotifCount, 10000);
})();
</script>
