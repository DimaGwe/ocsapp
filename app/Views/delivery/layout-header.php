<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$_driverIsPending = ($_SESSION['user']['status'] ?? 'active') !== 'active';
$_driverLockedTooltip = $fr ? 'Disponible après approbation de votre candidature' : 'Available after your application is approved';

// Kick deleted drivers — if the user record is gone, destroy session and redirect.
if (!empty($_SESSION['user']['id'])) {
    try {
        $__drvCheck = \Database::getConnection()->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $__drvCheck->execute([(int)$_SESSION['user']['id']]);
        if (!$__drvCheck->fetch()) {
            session_unset();
            session_destroy();
            header('Location: ' . url('login'));
            exit;
        }
    } catch (\Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr-CA' : 'en-CA' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? ($fr ? 'Portail Livreur' : 'Driver Portal')) ?> - OCSAPP</title>
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    :root {
      --primary: #00b207;
      --primary-dark: #008505;
      --primary-700: #007a05;
      --success: #00b207;
      --warning: #f59e0b;
      --danger: #ef4444;
      --gray-50: #f9fafb;
      --gray-100: #f3f4f6;
      --gray-200: #e5e7eb;
      --gray-300: #d1d5db;
      --gray-400: #9ca3af;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

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
      background: linear-gradient(180deg, #00b207 0%, #009a06 100%);
      color: white;
      position: fixed;
      height: 100vh;
      overflow-y: auto;
      z-index: 200;
      transition: transform 0.3s;
    }

    .sidebar::-webkit-scrollbar { width: 4px; }
    .sidebar::-webkit-scrollbar-track { background: transparent; }
    .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }
    .sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
    .sidebar { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.15) transparent; }

    .sidebar-header {
      padding: 24px 20px;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo {
      font-size: 18px;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .sidebar-nav { padding: 16px 0; }

    .nav-section-label {
      padding: 12px 20px 4px;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255,255,255,0.35);
      font-weight: 600;
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

    .nav-link.active {
      border-right: 3px solid #86efac;
    }

    .nav-link i { width: 20px; text-align: center; }

    .nav-link.locked {
      color: rgba(255,255,255,0.3);
      cursor: not-allowed;
    }
    .nav-link.locked:hover {
      background: none;
      color: rgba(255,255,255,0.3);
    }
    .nav-lock-icon { margin-left: auto; font-size: 10px; opacity: 0.6; }
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
    .nav-unlock-hint a { color: rgba(255,255,255,0.9); font-weight: 600; text-decoration: underline; }

    .nav-badge {
      margin-left: auto;
      background: #ef4444;
      color: white;
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 10px;
      min-width: 20px;
      text-align: center;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 260px;
      transition: margin-left 0.3s;
    }

    .topbar {
      background: white;
      padding: 14px 28px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .topbar-left {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .topbar h1 {
      font-size: 18px;
      color: var(--gray-700);
      font-weight: 600;
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    /* Notification Bell */
    .notif-wrapper {
      position: relative;
    }
    .notif-bell {
      background: none;
      border: none;
      font-size: 18px;
      color: var(--gray-500);
      cursor: pointer;
      padding: 6px 8px;
      border-radius: 8px;
      position: relative;
      transition: color 0.2s, background 0.2s;
      display: flex;
      align-items: center;
    }
    .notif-bell:hover {
      color: var(--primary);
      background: var(--gray-100);
    }
    .notif-badge {
      position: absolute;
      top: 2px;
      right: 2px;
      background: #ef4444;
      color: white;
      font-size: 10px;
      font-weight: 700;
      min-width: 16px;
      height: 16px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 4px;
      line-height: 1;
    }
    .notif-panel {
      display: none;
      position: absolute;
      top: calc(100% + 10px);
      right: 0;
      width: 340px;
      background: #fff;
      border: 1px solid var(--gray-200);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      z-index: 1001;
      overflow: hidden;
    }
    .notif-panel.open { display: block; }
    .notif-panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 14px 16px;
      border-bottom: 1px solid var(--gray-100);
    }
    .notif-panel-header h4 {
      font-size: 14px;
      font-weight: 600;
      color: var(--gray-700);
      margin: 0;
    }
    .notif-mark-all {
      background: none;
      border: none;
      font-size: 12px;
      color: var(--primary);
      cursor: pointer;
      font-weight: 500;
      padding: 4px 8px;
      border-radius: 6px;
      display: flex;
      align-items: center;
      gap: 4px;
      transition: background 0.15s;
    }
    .notif-mark-all:hover { background: var(--gray-100); }
    .notif-list {
      max-height: 360px;
      overflow-y: auto;
    }
    .notif-item {
      display: flex;
      gap: 10px;
      padding: 12px 16px;
      border-bottom: 1px solid var(--gray-100);
      cursor: pointer;
      transition: background 0.15s;
    }
    .notif-item:last-child { border-bottom: none; }
    .notif-item:hover { background: var(--gray-50); }
    .notif-item.unread { background: #f0fdf4; }
    .notif-item-icon {
      width: 32px;
      height: 32px;
      border-radius: 50%;
      background: var(--gray-100);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 13px;
      color: var(--gray-500);
    }
    .notif-item-icon.urgent { background: #fef2f2; color: #ef4444; }
    .notif-item-icon.warning { background: #fffbeb; color: #f59e0b; }
    .notif-item-icon.info { background: #eff6ff; color: #3b82f6; }
    .notif-item-content { flex: 1; min-width: 0; }
    .notif-item-msg {
      font-size: 13px;
      color: var(--gray-700);
      line-height: 1.4;
      margin-bottom: 3px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .notif-item-time {
      font-size: 11px;
      color: var(--gray-400);
    }
    .notif-empty {
      padding: 28px 16px;
      text-align: center;
      color: var(--gray-400);
      font-size: 13px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 8px;
    }
    .notif-empty i { font-size: 22px; opacity: 0.5; }

    .user-info {
      text-align: right;
    }

    .user-name {
      font-weight: 600;
      font-size: 13px;
      color: var(--gray-700);
    }

    .user-role {
      font-size: 11px;
      color: var(--gray-500);
    }

    .btn-logout {
      padding: 7px 14px;
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
      font-size: 12px;
      font-family: inherit;
    }

    .content-area {
      padding: 28px;
    }

    /* Alerts */
    .alert {
      padding: 14px 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      font-weight: 500;
    }

    .alert-error {
      background: #fef2f2;
      color: #dc2626;
      border-left: 4px solid #dc2626;
    }

    .alert-success {
      background: #f0fdf4;
      color: #00b207;
      border-left: 4px solid #00b207;
    }

    /* Mobile toggle */
    .sidebar-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 22px;
      color: var(--gray-700);
      cursor: pointer;
      padding: 4px;
    }

    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 199;
    }

    /* Language Switcher */
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
    .lang-toggle-btn:hover { border-color: var(--primary); }
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
    .lang-dropdown.open { display: block; }
    .lang-option {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      font-size: 13px;
      cursor: pointer;
      transition: background 0.15s;
    }
    .lang-option:hover { background: #f3f4f6; }
    .lang-option.active { font-weight: 700; color: var(--primary); }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-260px);
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .sidebar-overlay.open {
        display: block;
      }
      .main-content {
        margin-left: 0;
      }
      .sidebar-toggle {
        display: block;
      }
      .content-area {
        padding: 16px;
      }
      .topbar {
        padding: 12px 16px;
      }
      .topbar h1 {
        font-size: 16px;
      }
      .user-info {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="dashboard-wrapper">
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <img src="<?= url('assets/images/logo.png') ?>" alt="OCSAPP" style="height:36px;width:auto;object-fit:contain;flex-shrink:0;">
          <div style="display:flex;flex-direction:column;line-height:1.3;">
            <span style="font-size:16px;font-weight:700;font-family:'Poppins',sans-serif;color:white;">OCSAPP</span>
            <span style="font-size:10px;font-weight:500;color:rgba(255,255,255,0.7);letter-spacing:0.6px;text-transform:uppercase;font-family:'Poppins',sans-serif;"><?= $fr ? 'Portail Livreur' : 'Driver Portal' ?></span>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <a href="<?= url('delivery/dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
          <i class="fas fa-tachometer-alt"></i>
          <span><?= $fr ? 'Tableau de bord' : 'Dashboard' ?></span>
        </a>

        <div class="nav-section-label"><?= $fr ? 'Livraisons' : 'Deliveries' ?></div>
        <?php if ($_driverIsPending): ?>
          <span class="nav-link locked" title="<?= $_driverLockedTooltip ?>">
            <i class="fas fa-clipboard-list"></i>
            <span><?= $fr ? 'Mes livraisons' : 'My Deliveries' ?></span>
            <i class="fas fa-lock nav-lock-icon"></i>
          </span>
          <span class="nav-link locked" title="<?= $_driverLockedTooltip ?>">
            <i class="fas fa-history"></i>
            <span><?= $fr ? 'Historique' : 'History' ?></span>
            <i class="fas fa-lock nav-lock-icon"></i>
          </span>
        <?php else: ?>
          <a href="<?= url('delivery/available') ?>" class="nav-link <?= ($currentPage ?? '') === 'available' ? 'active' : '' ?>">
            <i class="fas fa-clipboard-list"></i>
            <span><?= $fr ? 'Mes livraisons' : 'My Deliveries' ?></span>
            <?php if (!empty($pendingCount) && $pendingCount > 0): ?>
              <span class="nav-badge"><?= $pendingCount ?></span>
            <?php endif; ?>
          </a>
          <a href="<?= url('delivery/history') ?>" class="nav-link <?= ($currentPage ?? '') === 'history' ? 'active' : '' ?>">
            <i class="fas fa-history"></i>
            <span><?= $fr ? 'Historique' : 'History' ?></span>
          </a>
        <?php endif; ?>

        <div class="nav-section-label"><?= $fr ? 'Finances' : 'Finances' ?></div>
        <?php if ($_driverIsPending): ?>
          <span class="nav-link locked" title="<?= $_driverLockedTooltip ?>">
            <i class="fas fa-wallet"></i>
            <span><?= $fr ? 'Revenus' : 'Earnings' ?></span>
            <i class="fas fa-lock nav-lock-icon"></i>
          </span>
        <?php else: ?>
          <a href="<?= url('delivery/earnings') ?>" class="nav-link <?= ($currentPage ?? '') === 'earnings' ? 'active' : '' ?>">
            <i class="fas fa-wallet"></i>
            <span><?= $fr ? 'Revenus' : 'Earnings' ?></span>
          </a>
        <?php endif; ?>

        <div class="nav-section-label"><?= $fr ? 'Formation' : 'Onboarding' ?></div>
        <a href="<?= url('delivery/training') ?>" class="nav-link <?= ($currentPage ?? '') === 'training' ? 'active' : '' ?>">
          <i class="fas fa-graduation-cap"></i>
          <span><?= $fr ? 'Formation Livreur' : 'Driver Training' ?></span>
        </a>
        <a href="<?= url('delivery/documents') ?>" class="nav-link <?= in_array($currentPage ?? '', ['documents','bgcheck','compliance']) ? 'active' : '' ?>">
          <i class="fas fa-folder-open"></i>
          <span><?= $fr ? 'Documents' : 'Documents' ?></span>
          <?php
          try {
              $_docConn = \Database::getConnection();
              $_docApp  = $_docConn->prepare("SELECT id, bgcheck_status FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
              $_docApp->execute([userId()]);
              $_docRow  = $_docApp->fetch(\PDO::FETCH_ASSOC);
              $_docBadge = 0;
              if ($_docRow) {
                  // Bgcheck flags
                  if (in_array($_docRow['bgcheck_status'], ['not_requested','flagged'])) $_docBadge++;
                  // Compliance pending/flagged
                  $_cdFlag = $_docConn->prepare("SELECT COUNT(*) FROM driver_compliance_docs WHERE application_id = ? AND status = 'flagged'");
                  $_cdFlag->execute([$_docRow['id']]);
                  $_docBadge += (int)$_cdFlag->fetchColumn();
                  $_cdPending = $_docConn->prepare("SELECT COUNT(*) FROM driver_compliance_docs WHERE application_id = ? AND status IN ('verified','not_required')");
                  $_cdPending->execute([$_docRow['id']]);
                  if ((int)$_cdPending->fetchColumn() < 5) $_docBadge++;
              } else {
                  $_docBadge = 1;
              }
              if ($_docBadge > 0) echo '<span class="nav-badge" style="background:#f59e0b;">' . $_docBadge . '</span>';
          } catch (\Exception $_e) {}
          ?>
        </a>

        <div class="nav-section-label"><?= $fr ? 'Compte' : 'Account' ?></div>
        <a href="<?= url('delivery/emails') ?>" class="nav-link <?= ($currentPage ?? '') === 'emails' ? 'active' : '' ?>">
          <i class="fas fa-envelope-open-text"></i>
          <span><?= $fr ? 'Mes courriels' : 'My Emails' ?></span>
        </a>
        <a href="<?= url('delivery/messages') ?>" class="nav-link <?= ($currentPage ?? '') === 'messages' ? 'active' : '' ?>">
          <i class="fas fa-comments"></i>
          <span><?= $fr ? 'Messages' : 'Messages' ?></span>
          <?php
          try {
              $_msgAppStmt = \Database::getConnection()->prepare("SELECT id FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
              $_msgAppStmt->execute([userId()]);
              $_msgAppId = $_msgAppStmt->fetchColumn();
              if ($_msgAppId) {
                  $_msgUnread = \Database::getConnection()->prepare("SELECT COUNT(*) FROM driver_application_messages WHERE application_id = ? AND sender_type = 'admin' AND is_read = 0");
                  $_msgUnread->execute([$_msgAppId]);
                  $_msgCount = (int)$_msgUnread->fetchColumn();
                  if ($_msgCount > 0) echo '<span class="nav-badge">' . $_msgCount . '</span>';
              }
          } catch (\Exception $_e) {}
          ?>
        </a>
        <a href="<?= url('delivery/settings') ?>" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
          <i class="fas fa-cog"></i>
          <span><?= $fr ? 'Paramètres' : 'Settings' ?></span>
        </a>

        <?php if ($_driverIsPending): ?>
        <div class="nav-unlock-hint">
          <i class="fas fa-lock" style="margin-right:5px;"></i>
          <?= $fr ? 'Fonctionnalités débloquées après approbation de votre candidature.' : 'Features unlock after your application is approved.' ?>
          <br><a href="mailto:info@ocsapp.ca"><?= $fr ? 'Nous contacter' : 'Contact us' ?> &rarr;</a>
        </div>
        <?php endif; ?>

        <div style="padding: 20px; margin-top: auto;">
          <form method="POST" action="<?= url('logout') ?>" style="margin:0;">
            <?= csrfField() ?>
            <button type="submit" style="display:flex;align-items:center;gap:8px;color:rgba(255,255,255,0.6);background:none;border:none;cursor:pointer;font-size:13px;padding:10px;border-radius:8px;transition:0.2s;width:100%;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='none'">
              <i class="fas fa-sign-out-alt"></i>
              <span><?= $fr ? 'Se déconnecter' : 'Sign Out' ?></span>
            </button>
          </form>
        </div>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <div class="topbar">
        <div class="topbar-left">
          <button class="sidebar-toggle" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
          </button>
          <h1><?= htmlspecialchars($pageTitle ?? ($fr ? 'Portail Livreur' : 'Driver Portal')) ?></h1>
        </div>
        <div class="user-menu">
          <!-- Notification Bell -->
          <div class="notif-wrapper" id="driverNotifWrapper">
            <button class="notif-bell" id="driverNotifBellBtn" type="button" onclick="toggleDriverNotifPanel()">
              <i class="fas fa-bell"></i>
              <span class="notif-badge" id="driverNotifBadge" style="display:none;">0</span>
            </button>
            <div class="notif-panel" id="driverNotifPanel">
              <div class="notif-panel-header">
                <h4><i class="fas fa-bell" style="margin-right:6px;"></i> Notifications</h4>
                <button class="notif-mark-all" id="driverNotifMarkAllBtn" onclick="markAllDriverNotifRead()" style="display:none;">
                  <i class="fas fa-check-double"></i> <?= $fr ? 'Tout marquer lu' : 'Mark all read' ?>
                </button>
              </div>
              <div class="notif-list" id="driverNotifList">
                <div class="notif-empty">
                  <i class="fas fa-bell-slash"></i>
                  <?= $fr ? 'Aucune notification' : 'No notifications' ?>
                </div>
              </div>
            </div>
          </div>
          <!-- Language Switcher -->
          <div style="position:relative;">
            <button class="lang-toggle-btn" id="driverLangBtn" type="button">
              <i class="fas fa-globe"></i>
              <span><?= strtoupper($currentLang) ?></span>
              <span style="font-size:10px;">&#9660;</span>
            </button>
            <div class="lang-dropdown" id="driverLangDropdown">
              <div class="lang-option <?= $currentLang === 'en' ? 'active' : '' ?>" data-lang="en">
                &#127482;&#127480; English
              </div>
              <div class="lang-option <?= $currentLang === 'fr' ? 'active' : '' ?>" data-lang="fr">
                &#127467;&#127479; Fran&ccedil;ais
              </div>
            </div>
          </div>

          <div class="user-info">
            <div class="user-name"><?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></div>
            <div class="user-role"><?= $fr ? 'Livreur' : 'Delivery Driver' ?></div>
          </div>
          <form method="POST" action="<?= url('logout') ?>" style="margin:0;">
            <?= csrfField() ?>
            <button type="submit" class="btn-logout" title="Sign Out">
              <i class="fas fa-sign-out-alt"></i>
            </button>
          </form>
        </div>
      </div>

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
