<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'dashboard'                   => 'Dashboard',
        'procurement'                 => 'Procurement',
        'new_request'                 => 'New Request',
        'my_requests'                 => 'My Requests',
        'my_invoices'                 => 'Invoices',
        'distribution'                => 'Distribution',
        'new_shipment'                => 'New Shipment',
        'my_shipments'                => 'My Shipments',
        'recurring_routes'            => 'Recurring Routes',
        'track_shipment'              => 'Track Shipment',
        'messages'                    => 'Messages',
        'my_emails'                   => 'My Emails',
        'documents'                   => 'Documents',
        'logout'                      => 'Logout',
        'portal_title'                => 'Distribution Portal',
        'dist_account'                => 'Distribution Account',
        'settings'                    => 'Settings',
        'verification_pending_title'  => 'Account Verification Pending',
        'verification_pending_msg'    => 'Your application is being reviewed by our team. You have limited access to the portal while verification is in progress. Full features will be unlocked once your account is approved.',
        'verification_contact'        => 'Questions? Contact us at',
        'payables'                    => 'Payables',
        'locked_tooltip'              => 'Available after account approval',
        'unlock_hint'                 => 'Features unlock after your account is approved.',
        'contact_us'                  => 'Contact us',
    ],
    'fr' => [
        'dashboard'                   => 'Tableau de bord',
        'procurement'                 => 'Approvisionnement',
        'new_request'                 => 'Nouvelle demande',
        'my_requests'                 => 'Mes demandes',
        'my_invoices'                 => 'Factures',
        'distribution'                => 'Distribution',
        'new_shipment'                => 'Nouvel envoi',
        'my_shipments'                => 'Mes envois',
        'recurring_routes'            => 'Routes récurrentes',
        'track_shipment'              => 'Suivre un envoi',
        'messages'                    => 'Messages',
        'my_emails'                   => 'Mes courriels',
        'documents'                   => 'Documents',
        'logout'                      => 'Déconnexion',
        'portal_title'                => 'Portail de Distribution',
        'dist_account'                => 'Compte Distribution',
        'settings'                    => 'Paramètres',
        'verification_pending_title'  => 'Vérification du compte en attente',
        'verification_pending_msg'    => 'Votre demande est en cours d\'examen par notre équipe. Vous avez un accès limité au portail pendant la vérification. Toutes les fonctionnalités seront débloquées une fois votre compte approuvé.',
        'verification_contact'        => 'Questions? Contactez-nous à',
        'payables'                    => 'Paiements dus',
        'locked_tooltip'              => 'Disponible après approbation du compte',
        'unlock_hint'                 => 'Les fonctionnalités sont débloquées une fois votre compte approuvé.',
        'contact_us'                  => 'Contactez-nous',
    ],
];
$t = $translations[$currentLang] ?? $translations['en'];

// Unread message count for sidebar badge
$_bizUnreadMsgCount = 0;
if (!empty($_SESSION['business']['id'])) {
    try {
        $_bizUnreadMsgCount = \App\Controllers\BusinessMessagesController::getUnreadCount((int)$_SESSION['business']['id']);
    } catch (\Throwable $e) {
        $_bizUnreadMsgCount = 0;
    }
}

// Always refresh status from DB so approval takes effect without requiring re-login.
// Also kicks deleted accounts — if the record is gone, destroy session and redirect.
if (!empty($_SESSION['business']['id'])) {
    try {
        $__bpStmt = \Database::getConnection()->prepare("SELECT status, verification_deadline FROM business_profiles WHERE id = ? LIMIT 1");
        $__bpStmt->execute([$_SESSION['business']['id']]);
        $__bpRow = $__bpStmt->fetch(\PDO::FETCH_ASSOC);
        if ($__bpRow) {
            $_SESSION['business']['status'] = $__bpRow['status'];
            $_SESSION['business']['verification_deadline'] = $__bpRow['verification_deadline'];
        } else {
            session_unset();
            session_destroy();
            header('Location: ' . url('distribution/login'));
            exit;
        }
    } catch (\Throwable $e) {
        // silently keep session value
    }
}
$_bizIsPending = ($_SESSION['business']['status'] ?? '') === 'pending';

// Sidebar badge counts
$_bizDraftCount          = 0;
$_bizActiveRequestCount  = 0;
$_bizUnpaidInvoiceCount  = 0;
if (!empty($_SESSION['business']['id']) && !$_bizIsPending) {
    try {
        $__db = \Database::getConnection();
        // Draft requests
        $__s = $__db->prepare("SELECT COUNT(*) FROM distribution_requests WHERE business_profile_id = ? AND status = 'draft'");
        $__s->execute([$_SESSION['business']['id']]);
        $_bizDraftCount = (int)$__s->fetchColumn();
        // Active (in-flight) requests — need attention
        $__sa = $__db->prepare("SELECT COUNT(*) FROM distribution_requests WHERE business_profile_id = ? AND status NOT IN ('draft','completed','cancelled','delivered')");
        $__sa->execute([$_SESSION['business']['id']]);
        $_bizActiveRequestCount = (int)$__sa->fetchColumn();
        // Unpaid invoices
        $__s2 = $__db->prepare("SELECT COUNT(*) FROM distribution_invoices WHERE business_profile_id = ? AND status IN ('sent','overdue','pending')");
        $__s2->execute([$_SESSION['business']['id']]);
        $_bizUnpaidInvoiceCount = (int)$__s2->fetchColumn();
        // Payables: distribution requests with unpaid payment status
        $__s3 = $__db->prepare("SELECT COUNT(*) FROM distribution_requests WHERE business_profile_id = ? AND payment_status NOT IN ('paid','refunded') AND status NOT IN ('draft','cancelled','expired')");
        $__s3->execute([$_SESSION['business']['id']]);
        $_bizUnpaidPayablesCount = (int)$__s3->fetchColumn();
    } catch (\Throwable $e) {}
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?? $t['portal_title'] ?></title>
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
      left: 0;
      height: 100vh;
      overflow-y: auto;
      z-index: 200;
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

    .sidebar-logo-img { height: 36px; width: auto; object-fit: contain; flex-shrink: 0; }
    .sidebar-logo-text { display: flex; flex-direction: column; line-height: 1.3; }
    .sidebar-logo-brand { font-size: 16px; font-weight: 700; color: white; font-family: 'Poppins', sans-serif; }
    .sidebar-logo-portal { font-size: 10px; font-weight: 500; color: rgba(255,255,255,0.7); letter-spacing: 0.6px; text-transform: uppercase; font-family: 'Poppins', sans-serif; }

    .sidebar-logo i {
      font-size: 24px;
    }

    .sidebar-nav {
      padding: 20px 0;
    }

    .nav-section-label {
      padding: 8px 20px 4px;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255,255,255,0.4);
      font-weight: 600;
      margin-top: 8px;
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
      background: #fee2e2;
      color: #b91c1c;
      border-left: 4px solid #b91c1c;
    }

    .alert-success {
      background: #d1fae5;
      color: #065f46;
      border-left: 4px solid #059669;
    }

    .alert-verification {
      background: #eff6ff;
      border-left: 4px solid #3b82f6;
      color: #1e40af;
      padding: 16px 20px;
      border-radius: 8px;
      margin-bottom: 24px;
      display: flex;
      align-items: flex-start;
      gap: 12px;
    }

    /* Notification Bell */
    .notif-wrapper {
      position: relative;
      margin-right: 8px;
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
    .notif-panel {
      display: none;
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      width: 360px;
      max-height: 420px;
      background: white;
      border: 1px solid var(--gray-200);
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.12);
      z-index: 1000;
      overflow: hidden;
    }
    .notif-panel.open { display: block; }
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
    .notif-list { max-height: 320px; overflow-y: auto; }
    .notif-item {
      display: flex;
      gap: 12px;
      padding: 12px 16px;
      text-decoration: none;
      color: inherit;
      border-bottom: 1px solid var(--gray-100);
      transition: background 0.15s;
    }
    .notif-item:hover { background: var(--gray-50); }
    .notif-item.unread { background: #f0fdf4; }
    .notif-item-icon {
      width: 36px; height: 36px; border-radius: 8px;
      background: var(--gray-100);
      display: flex; align-items: center; justify-content: center;
      color: var(--primary); flex-shrink: 0; font-size: 14px;
    }
    .notif-item.unread .notif-item-icon { background: #dcfce7; }
    .notif-item-content { flex: 1; min-width: 0; }
    .notif-item-title { font-size: 13px; font-weight: 600; color: var(--gray-700); margin-bottom: 2px; }
    .notif-item-msg {
      font-size: 12px; color: var(--gray-600); line-height: 1.4;
      display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .notif-item-time { font-size: 11px; color: #6b7280; margin-top: 3px; }
    .notif-empty { padding: 32px 16px; text-align: center; color: var(--gray-400); font-size: 13px; }
    .notif-empty i { font-size: 24px; display: block; margin-bottom: 8px; }

    /* Mobile sidebar toggle */
    .sidebar-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 24px;
      color: var(--gray-700);
      cursor: pointer;
      padding: 4px;
    }

    /* Language switcher */
    .lang-switcher {
      display: inline-flex;
      align-items: center;
      gap: 2px;
    }
    .lang-btn { background: none; border: none; cursor: pointer; font-weight: 700; padding: 4px 6px; font-size: 13px; font-family: inherit; color: #9ca3af; transition: color 0.2s; }
    .lang-btn.active { color: #00b207; }
    .lang-divider { color: #d1d5db; font-size: 13px; padding: 0 2px; }
    .topbar-left { display: flex; align-items: center; gap: 12px; }
    .notif-panel-header h4 i { margin-right: 6px; }

    /* ===================== SHARED PAGE STYLES ===================== */

    /* Page Header */
    .page-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }
    .page-title {
      font-size: 20px;
      font-weight: 600;
      color: #111827;
    }
    .page-title i { color: #00b207; margin-right: 8px; }

    /* Primary / Secondary Buttons */
    .btn-primary {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 20px; background: #00b207; color: white;
      border: none; border-radius: 8px; font-size: 14px; font-weight: 600;
      text-decoration: none; cursor: pointer; transition: background 0.2s;
    }
    .btn-primary:hover { background: #009906; }
    .btn-secondary {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 20px; background: #f3f4f6; color: #374151;
      border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-weight: 500;
      text-decoration: none; cursor: pointer; transition: background 0.2s;
    }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-sm {
      padding: 6px 14px; font-size: 13px; border-radius: 6px;
      display: inline-flex; align-items: center; gap: 6px;
      text-decoration: none; font-weight: 500; cursor: pointer; transition: background 0.2s;
    }
    .btn-outline {
      background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
    }
    .btn-outline:hover { background: #e5e7eb; }
    .btn-danger {
      background: #fee2e2; color: #dc2626; border: 1px solid #fecaca;
    }
    .btn-danger:hover { background: #fecaca; }

    /* Stats */
    .stats-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
      gap: 16px; margin-bottom: 24px;
    }
    .stats-row {
      display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;
    }
    .stats-row .stat-card { flex: 1; min-width: 140px; }
    .stat-card {
      background: white; border-radius: 12px; padding: 20px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .stat-value { font-size: 26px; font-weight: 700; color: #111827; margin-bottom: 4px; }
    .stat-label { font-size: 13px; color: #6b7280; }

    /* Cards */
    .section-card, .card {
      background: white; border-radius: 12px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden;
      margin-bottom: 24px;
    }
    .section-card { padding: 24px; }
    .section-header {
      display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
    }
    .section-title {
      font-size: 18px; font-weight: 600; color: #111827;
      display: flex; align-items: center; gap: 10px;
    }
    .section-title i { color: #00b207; }
    .card-header {
      padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
      display: flex; justify-content: space-between; align-items: center;
    }
    .card-title { font-size: 15px; font-weight: 600; color: #111827; }
    .card-body { padding: 20px; }

    /* Filters */
    .filters {
      display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;
    }
    .filter-btn {
      padding: 6px 14px; border-radius: 20px; font-size: 13px;
      font-weight: 500; text-decoration: none; color: #6b7280;
      background: white; border: 1px solid #e5e7eb; transition: all 0.2s;
    }
    .filter-btn:hover { border-color: #00b207; color: #00b207; }
    .filter-btn.active { background: #00b207; color: white; border-color: #00b207; }

    /* Tables */
    .table-container, .table-responsive { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    thead th {
      padding: 12px 20px; text-align: left; font-size: 12px;
      font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em;
    }
    tbody tr { border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: #f9fafb; }
    tbody td { padding: 14px 20px; font-size: 14px; color: #374151; vertical-align: middle; }

    /* Request / Shipment list items */
    .request-number, .shipment-number {
      font-weight: 600; color: #111827; text-decoration: none;
    }
    .request-name, .shipment-name { font-size: 13px; color: #6b7280; margin-top: 2px; }
    .item-count { display: flex; gap: 8px; font-size: 13px; color: #6b7280; }
    .item-count span { display: flex; align-items: center; gap: 4px; }

    /* Badges (shared across all portals) */
    .badge {
      display: inline-block; padding: 4px 10px; border-radius: 20px;
      font-size: 11px; font-weight: 600;
    }
    .badge-draft     { background: #f3f4f6; color: #6b7280; }
    .badge-submitted, .badge-pending { background: #dbeafe; color: #1d4ed8; }
    .badge-quoted, .badge-pending_payment { background: #fef3c7; color: #b45309; }
    .badge-approved  { background: #dbeafe; color: #1d4ed8; }
    .badge-paid      { background: #d1fae5; color: #059669; }
    .badge-processing, .badge-procurement { background: #e0e7ff; color: #4f46e5; }
    .badge-ready, .badge-in_transit { background: #cffafe; color: #0891b2; }
    .badge-completed, .badge-delivered { background: #d1fae5; color: #059669; }
    .badge-cancelled { background: #fef2f2; color: #991b1b; }
    .badge-active    { background: #d1fae5; color: #059669; }
    .badge-paused    { background: #fef3c7; color: #b45309; }
    .badge-expired   { background: #f3f4f6; color: #6b7280; }

    /* Action buttons */
    .btn-pay {
      display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
      background: #00b207; color: white; border: none; border-radius: 6px;
      font-size: 12px; font-weight: 600; cursor: pointer; text-decoration: none; transition: background 0.2s;
    }
    .btn-pay:hover { background: #009906; }
    .btn-view {
      display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
      background: #f3f4f6; color: #374151; border: none; border-radius: 6px;
      font-size: 12px; font-weight: 500; cursor: pointer; text-decoration: none; transition: background 0.2s;
    }
    .btn-view:hover { background: #e5e7eb; }
    .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

    /* Empty state */
    .empty-state { text-align: center; padding: 56px 24px; color: #6b7280; }
    .empty-state i { font-size: 48px; color: #d1d5db; margin-bottom: 16px; display: block; }
    .empty-state h3 { font-size: 16px; color: #111827; margin-bottom: 8px; }
    .empty-state p { font-size: 14px; margin-bottom: 20px; }

    /* Pagination */
    .pagination {
      display: flex; justify-content: center; align-items: center;
      gap: 6px; padding: 20px;
    }
    .pagination a, .pagination span {
      display: inline-flex; align-items: center; justify-content: center;
      width: 36px; height: 36px; border-radius: 8px; font-size: 14px;
      text-decoration: none; color: #374151; background: white;
      border: 1px solid #e5e7eb; transition: all 0.2s;
    }
    .pagination a:hover { border-color: #00b207; color: #00b207; }
    .pagination span.active { background: #00b207; color: white; border-color: #00b207; }

    /* Form elements */
    .form-group { margin-bottom: 20px; }
    .form-label {
      display: block; font-size: 13px; font-weight: 600;
      color: #374151; margin-bottom: 6px;
    }
    .form-control {
      width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb;
      border-radius: 8px; font-size: 14px; color: #374151;
      font-family: inherit; transition: border-color 0.2s;
    }
    .form-control:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .form-hint { font-size: 12px; color: #9ca3af; margin-top: 4px; }

    /* Route cards */
    .route-card {
      display: flex; align-items: center; gap: 16px; padding: 16px 20px;
      border-bottom: 1px solid #f3f4f6;
    }
    .route-card:last-child { border-bottom: none; }
    .route-icon {
      width: 44px; height: 44px; border-radius: 10px;
      background: #f0fdf4; color: #00b207;
      display: flex; align-items: center; justify-content: center;
      font-size: 18px; flex-shrink: 0;
    }
    .route-info { flex: 1; min-width: 0; }
    .route-name { font-weight: 600; color: #111827; font-size: 14px; }
    .route-meta { font-size: 12px; color: #6b7280; margin-top: 3px; }
    .route-schedule { text-align: center; min-width: 100px; }
    .schedule-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
    .schedule-value { font-size: 14px; font-weight: 600; color: #374151; margin-top: 2px; }
    .route-status { min-width: 80px; text-align: center; }
    .route-actions { display: flex; gap: 8px; }

    /* Detail/show page layouts */
    .detail-grid {
      display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;
    }
    @media (max-width: 768px) {
      .detail-grid { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .page-header { flex-direction: column; align-items: flex-start; gap: 12px; }
      .route-card { flex-wrap: wrap; }
      .route-schedule { min-width: auto; }
    }

    /* ===================== FORM PAGES ===================== */

    .back-link {
      display: inline-flex; align-items: center; gap: 6px;
      color: #6b7280; font-size: 13px; text-decoration: none;
      margin-bottom: 16px; transition: color 0.2s;
    }
    .back-link:hover { color: #00b207; }
    .page-subtitle { font-size: 14px; color: #6b7280; margin-top: 4px; }
    .breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #6b7280; margin-bottom: 16px; }

    .form-card {
      background: white; border-radius: 12px;
      box-shadow: 0 1px 4px rgba(0,0,0,0.06);
      padding: 24px; margin-bottom: 24px;
    }
    .form-section-title {
      font-size: 15px; font-weight: 600; color: #111827;
      margin-bottom: 20px; display: flex; align-items: center; gap: 8px;
      padding-bottom: 12px; border-bottom: 1px solid #f3f4f6;
    }
    .form-section-title i { color: #00b207; }

    .hidden { display: none !important; }
    .required { color: #ef4444; margin-left: 2px; }
    .form-error, .error-text { font-size: 12px; color: #ef4444; margin-top: 4px; display: block; }

    .form-input {
      width: 100%; padding: 10px 14px; border: 1px solid #e5e7eb;
      border-radius: 8px; font-size: 14px; color: #374151;
      font-family: inherit; transition: border-color 0.2s; background: white;
    }
    .form-input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .form-input.error { border-color: #ef4444; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-grid.three-cols { grid-template-columns: 1fr 1fr 1fr; }
    .form-grid .full-width, .full-width { grid-column: 1 / -1; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .form-grid.page-layout { grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    .main-column { min-width: 0; }
    .summary-column { position: sticky; top: 80px; }

    .form-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; flex-wrap: wrap; }
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all 0.2s; font-family: inherit; }
    .btn-remove, .remove-item, .remove-destination { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 6px; font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.2s; font-family: inherit; }
    .btn-remove:hover, .remove-item:hover, .remove-destination:hover { background: #fecaca; }
    .btn-add-destination, .btn-add-item { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #f0fdf4; color: #00b207; border: 2px dashed #86efac; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; margin-top: 16px; font-family: inherit; }
    .btn-add-destination:hover, .btn-add-item:hover { background: #dcfce7; border-color: #00b207; }
    .btn-submit { display: inline-flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; font-family: inherit; background: #00b207; color: white; margin-top: 8px; }
    .btn-submit:hover { background: #009906; }
    .btn-back { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; font-family: inherit; background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }
    .btn-back:hover { background: #e5e7eb; }
    .btn-download-template, .btn-upload-excel { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; font-family: inherit; background: #f0fdf4; color: #00b207; border: 1px solid #86efac; }
    .btn-download-template:hover, .btn-upload-excel:hover { background: #dcfce7; }

    @media (max-width: 1024px) { .form-grid.page-layout { grid-template-columns: 1fr; } .summary-column { position: static; } }
    @media (max-width: 768px) { .form-grid, .form-grid.three-cols, .form-row { grid-template-columns: 1fr; } .form-grid .full-width { grid-column: auto; } .shopping-item { grid-template-columns: 1fr auto; } }

    /* Shipment type selector */
    .shipment-type-selector { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    .type-option { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 20px 16px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px; cursor: pointer; transition: all 0.2s; text-align: center; }
    .type-option input[type="radio"] { display: none; }
    .type-option i { font-size: 28px; color: #9ca3af; transition: color 0.2s; }
    .type-option h4 { font-size: 14px; font-weight: 600; color: #374151; margin: 0; }
    .type-option p { font-size: 12px; color: #9ca3af; margin: 0; line-height: 1.4; }
    .type-option:hover, .type-option.selected { border-color: #00b207; background: #f0fdf4; }
    .type-option:hover i, .type-option.selected i { color: #00b207; }
    @media (max-width: 600px) { .shipment-type-selector { grid-template-columns: 1fr; } }

    /* Multi-drop destinations & item rows */
    .destination-item { border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px; margin-bottom: 16px; background: #fafafa; }
    .destination-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .destination-header h4 { font-size: 14px; font-weight: 600; color: #374151; margin: 0; }
    .item-row { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 12px; align-items: end; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
    .item-row:last-child { border-bottom: none; }
    @media (max-width: 768px) { .item-row { grid-template-columns: 1fr 1fr; } }

    /* Tabs */
    .tabs { display: flex; gap: 4px; border-bottom: 2px solid #e5e7eb; margin-bottom: 20px; }
    .tab-btn { padding: 10px 20px; background: none; border: none; cursor: pointer; font-size: 14px; font-weight: 500; color: #6b7280; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.2s; font-family: inherit; }
    .tab-btn.active { color: #00b207; border-bottom-color: #00b207; }
    .tab-btn:hover:not(.active) { color: #374151; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }

    /* Supplier catalog */
    .suppliers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 20px; }
    .supplier-card { background: white; border: 2px solid #e5e7eb; border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.2s; text-align: center; }
    .supplier-card:hover, .supplier-card.active { border-color: #00b207; background: #f0fdf4; }
    .supplier-logo { width: 56px; height: 56px; border-radius: 10px; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; background: #f3f4f6; font-size: 20px; color: #6b7280; overflow: hidden; }
    .supplier-logo img { width: 100%; height: 100%; object-fit: cover; }
    .supplier-name { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 4px; }
    .supplier-meta { font-size: 12px; color: #6b7280; }
    .supplier-products-header { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px; }
    .current-supplier-name { font-weight: 700; color: #00b207; }
    .catalog-scroll { max-height: 400px; overflow-y: auto; }
    .product-item { display: flex; align-items: center; gap: 12px; padding: 12px; border-bottom: 1px solid #f3f4f6; transition: background 0.15s; }
    .product-item:last-child { border-bottom: none; }
    .product-item:hover { background: #f9fafb; }
    .product-item.selected { background: #f0fdf4; }
    .product-item.selected .product-qty { border-color: #00b207; }
    .product-image { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; background: #f3f4f6; flex-shrink: 0; }
    .product-info { flex: 1; min-width: 0; }
    .product-name { font-size: 14px; font-weight: 500; color: #111827; }
    .product-sku { font-size: 12px; color: #6b7280; }
    .product-price { font-size: 14px; font-weight: 600; color: #00b207; white-space: nowrap; }
    .product-qty { width: 64px; padding: 6px 8px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; text-align: center; font-family: inherit; }

    /* Shopping list */
    .shopping-items-list { margin-bottom: 16px; }
    .shopping-item { display: grid; grid-template-columns: 1fr auto auto auto; gap: 8px; align-items: center; padding: 8px 0; border-bottom: 1px solid #f3f4f6; }
    .shopping-item:last-child { border-bottom: none; }
    .divider-or { display: flex; align-items: center; gap: 12px; margin: 16px 0; color: #9ca3af; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .divider-or::before, .divider-or::after { content: ''; flex: 1; border-top: 1px solid #e5e7eb; }
    .excel-upload-section { margin-bottom: 20px; border: 2px dashed transparent; border-radius: 8px; padding: 8px; transition: all 0.2s; }
    .excel-upload-section.dragover { border-color: #00b207; background: #f0fdf4; }
    .excel-upload-title { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
    .excel-upload-desc { font-size: 12px; color: #6b7280; margin-bottom: 12px; }
    .excel-upload-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 8px; }
    .upload-info { font-size: 12px; color: #6b7280; }
    .upload-file-input { display: none; }
    .upload-result { font-size: 13px; margin-top: 8px; }
    .upload-result.success { color: #059669; }
    .upload-result.error { color: #b91c1c; }

    /* Order summary */
    .summary-item { display: flex; justify-content: space-between; padding: 8px 0; font-size: 14px; color: #374151; }
    .summary-subtotal { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; font-weight: 600; border-top: 1px solid #e5e7eb; margin-top: 4px; }
    .summary-total { display: flex; justify-content: space-between; padding: 12px 0; font-size: 16px; font-weight: 700; color: #111827; border-top: 2px solid #111827; margin-top: 4px; }
    .summary-note { font-size: 12px; color: #6b7280; margin-top: 12px; line-height: 1.5; }
    .summary-empty-text { color: #6b7280; font-size: 14px; text-align: center; padding: 24px 0; }
    .fee-row, .tax-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; }
    .tax-section { margin-top: 8px; border-top: 1px solid #f3f4f6; padding-top: 4px; }
    .tip-section { margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6; }
    .tip-header { font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
    .tip-header i { color: #00b207; }
    .tip-description { font-size: 11px; color: #6b7280; margin-bottom: 10px; line-height: 1.4; }
    .tip-options { display: flex; gap: 8px; flex-wrap: wrap; margin: 8px 0; }
    .tip-btn { padding: 6px 14px; border: 1px solid #e5e7eb; border-radius: 20px; background: white; font-size: 13px; cursor: pointer; transition: all 0.2s; font-family: inherit; }
    .tip-btn.active { background: #00b207; color: white; border-color: #00b207; }
    .tip-custom-input { /* container for custom tip input row */ }
    .tip-custom-row { display: flex; align-items: center; gap: 8px; margin-top: 8px; }
    .tip-currency { font-size: 14px; font-weight: 500; }
    .tip-custom-amount-input { flex: 1; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 14px; font-family: inherit; }
    .tip-custom-amount-input:focus { outline: none; border-color: #00b207; }
    .tip-display { display: flex; justify-content: space-between; align-items: center; font-size: 13px; color: #6b7280; margin-top: 6px; }

    /* Delivery route */
    .delivery-route-section { margin-top: 12px; }
    .delivery-route-header { display: flex; justify-content: space-between; align-items: center; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 8px; }
    .route-legs { display: flex; flex-direction: column; gap: 6px; }
    .route-leg { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; }
    .route-stop { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #374151; padding: 3px 0; }
    .route-destination { color: #00b207; font-weight: 600; }
    .delivery-route-distance { font-size: 13px; font-weight: 600; color: #00b207; }
    .geocoding-status { font-size: 12px; color: #6b7280; margin-top: 6px; }
    .geocoding-status.error { color: #b91c1c; }
    .delivery-manual-fallback { display: flex; align-items: center; gap: 8px; font-size: 12px; color: #6b7280; margin-top: 6px; }
    .manual-distance-label { font-size: 11px; color: #6b7280; }
    .search-box { position: relative; margin-bottom: 16px; }
    .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; font-size: 14px; pointer-events: none; }
    .search-box input { width: 100%; padding: 10px 14px 10px 38px; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 14px; font-family: inherit; background: white; }
    .search-box input:focus { outline: none; border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .tier-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 12px; background: #f3f4f6; color: #374151; }
    .tier-badge.tier-1 { background: #dbeafe; color: #1d4ed8; }
    .tier-badge.tier-2 { background: #d1fae5; color: #059669; }
    .tier-badge.tier-3 { background: #fef3c7; color: #b45309; }
    .tier-badge.tier-4 { background: #fce7f3; color: #9d174d; }
    /* Helper classes for form pages */
    .tab-intro-text { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
    .no-suppliers-msg { text-align: center; color: #6b7280; padding: 24px; }
    /* Table cell helpers */
    .invoice-number { font-weight: 500; color: #111827; }
    .invoice-total { font-size: 12px; color: #6b7280; }
    .text-placeholder { color: #9ca3af; }
    .col-date { font-size: 13px; color: #6b7280; white-space: nowrap; }
    /* Accessibility */
    .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }

    .sidebar-backdrop {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 199;
    }
    .sidebar-backdrop.active { display: block; }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-260px);
        transition: transform 0.3s;
      }
      .sidebar.open {
        transform: translateX(0);
      }
      .main-content {
        margin-left: 0;
      }
      .sidebar-toggle {
        display: block;
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
      .notif-panel {
        width: min(360px, calc(100vw - 32px));
      }
    }

    /* ── Payment page ── */
    .payment-layout { display: flex; gap: 32px; align-items: flex-start; }
    .order-column { flex: 1; min-width: 0; }
    .payment-column { flex: 0 0 360px; width: 360px; }
    .cancel-notice {
      display: flex; gap: 12px; align-items: flex-start;
      background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;
      padding: 16px; margin-bottom: 20px; font-size: 14px; color: #92400e;
    }
    .cancel-notice i { color: #d97706; margin-top: 2px; flex-shrink: 0; }
    .status-badge { display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .status-approved { background: #d1fae5; color: #065f46; }
    .pay-request-meta { font-size: 14px; color: #6b7280; margin-bottom: 16px; line-height: 1.6; }
    .items-section-label { font-size: 13px; color: #6b7280; margin-bottom: 12px; font-weight: 500; }
    .items-section-label--gap { margin-top: 20px; }
    .items-toggle {
      display: flex; justify-content: space-between; align-items: center;
      padding: 12px 0; border-top: 1px solid #f3f4f6; cursor: pointer;
      font-size: 14px; color: #374151; font-weight: 500; margin-top: 4px; user-select: none;
    }
    .items-toggle i { transition: transform 0.2s; }
    .items-toggle.active i { transform: rotate(180deg); }
    .order-details { display: none; padding-top: 12px; }
    .order-details.show { display: block; }
    @media (min-width: 769px) { .order-details { display: block !important; } .items-toggle { display: none; } }
    .order-items { margin-bottom: 8px; }
    .order-items .item-row { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f3f4f6; grid-template-columns: unset; }
    .order-items .item-row:last-child { border-bottom: none; }
    .item-info { flex: 1; min-width: 0; }
    .item-name { font-size: 14px; font-weight: 500; color: #111827; }
    .item-details { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .item-price { font-size: 14px; font-weight: 600; color: #111827; white-space: nowrap; }
    .summary-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 14px; color: #374151; border-top: 1px solid #f3f4f6; }
    .summary-row.subtotal { border-top: 2px solid #e5e7eb; margin-top: 8px; font-weight: 500; }
    .summary-row.total { border-top: 2px solid #111827; font-size: 16px; font-weight: 700; color: #111827; padding: 12px 0; }
    .summary-label { }
    .summary-value { font-weight: 600; }
    .delivery-card-title { margin-bottom: 16px; color: var(--primary); }
    .delivery-info { margin-bottom: 16px; }
    .delivery-info:last-child { margin-bottom: 0; }
    .delivery-info h4 { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
    .delivery-info p { font-size: 14px; color: #374151; line-height: 1.6; margin: 0; }
    .payment-card { position: sticky; top: 80px; }
    .total-display { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #f3f4f6; margin-bottom: 20px; }
    .total-label { font-size: 12px; color: #6b7280; margin-bottom: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }
    .total-amount { font-size: 42px; font-weight: 700; color: #111827; line-height: 1.1; display: flex; align-items: baseline; justify-content: center; gap: 6px; }
    .total-currency { font-size: 20px; font-weight: 600; color: #6b7280; }
    .expiry-notice {
      display: flex; gap: 10px; align-items: flex-start;
      background: #fef3c7; border-radius: 8px; padding: 12px 14px;
      font-size: 13px; color: #92400e; margin-bottom: 20px;
    }
    .expiry-notice i { margin-top: 2px; flex-shrink: 0; }
    .payment-card .btn-pay {
      display: flex; width: 100%; padding: 16px 24px;
      font-size: 16px; font-weight: 700; border-radius: 10px;
      justify-content: center; align-items: center; gap: 10px;
      margin-bottom: 16px; position: relative;
    }
    .payment-card .btn-pay .spinner {
      display: none; width: 18px; height: 18px; border-radius: 50%;
      border: 2px solid rgba(255,255,255,0.4); border-top-color: white;
      animation: pay-spin 0.7s linear infinite; flex-shrink: 0;
    }
    .payment-card .btn-pay.loading .spinner { display: inline-block; }
    .payment-card .btn-pay.loading .btn-text { opacity: 0.7; }
    @keyframes pay-spin { to { transform: rotate(360deg); } }
    .payment-methods { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #6b7280; margin-bottom: 16px; flex-wrap: wrap; }
    .security-info { display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 12px; color: #9ca3af; text-align: center; }
    .security-info i { color: #00b207; }
    .pay-footer { text-align: center; padding: 24px; color: #9ca3af; font-size: 13px; }
    @media (max-width: 900px) {
      .payment-layout { flex-direction: column; }
      .payment-column { width: 100%; flex: none; }
      .payment-card { position: static; }
      .total-amount { font-size: 32px; }
    }

    /* ── Payment Success page ── */
    .success-container { max-width: 640px; margin: 0 auto; padding: 32px 16px 48px; }
    .success-card { background: white; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); overflow: hidden; }
    .success-header { background: linear-gradient(135deg, #00b207 0%, #009906 100%); padding: 40px 32px; text-align: center; }
    .success-icon { width: 72px; height: 72px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .success-icon i { font-size: 36px; color: white; }
    .success-title { font-size: 28px; font-weight: 700; color: white; margin: 0 0 8px; }
    .success-subtitle { font-size: 15px; color: rgba(255,255,255,0.85); margin: 0; }
    .success-body { padding: 32px; }
    .order-info { background: #f9fafb; border-radius: 12px; padding: 20px 24px; margin-bottom: 28px; display: grid; gap: 12px; }
    .order-number { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; }
    .order-value { font-size: 22px; font-weight: 700; color: #111827; margin-top: -6px; }
    .amount-label { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 8px; }
    .amount-value { font-size: 28px; font-weight: 700; color: #00b207; margin-top: -4px; }
    .next-steps { margin-bottom: 28px; }
    .next-steps h3 { font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 16px; }
    .step-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px; }
    .step-list li { display: flex; align-items: flex-start; gap: 12px; }
    .step-number { width: 28px; height: 28px; background: #d1fae5; color: #065f46; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
    .step-text { font-size: 14px; color: #4b5563; line-height: 1.6; padding-top: 4px; }
    .success-body .btn-group { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px; }
    .success-body .btn { padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none; }
    .success-body .btn-primary { background: #00b207; color: white; }
    .success-body .btn-primary:hover { background: #009906; }
    .success-body .btn-secondary { background: #f3f4f6; color: #374151; }
    .success-body .btn-secondary:hover { background: #e5e7eb; }
    .email-notice { display: flex; align-items: center; gap: 10px; background: #eff6ff; border-radius: 8px; padding: 14px 16px; font-size: 13px; color: #1e40af; }
    .email-notice i { flex-shrink: 0; }
    .success-footer { text-align: center; padding: 24px; color: #9ca3af; font-size: 13px; }

    /* ── Payment Error page ── */
    .error-container { max-width: 640px; margin: 0 auto; padding: 32px 16px 48px; }
    .error-card { background: white; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); overflow: hidden; }
    .error-header { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); padding: 40px 32px; text-align: center; }
    .error-icon { width: 72px; height: 72px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
    .error-icon i { font-size: 36px; color: white; }
    .error-title { font-size: 28px; font-weight: 700; color: white; margin: 0 0 8px; }
    .error-subtitle { font-size: 15px; color: rgba(255,255,255,0.85); margin: 0; }
    .error-body { padding: 32px; }
    .error-body .error-message { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; }
    .error-body .error-message p { margin: 0; font-size: 14px; color: #991b1b; line-height: 1.6; }
    .help-text { font-size: 14px; color: #6b7280; line-height: 1.6; margin-bottom: 28px; }
    .error-body .btn-group { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 28px; }
    .error-body .btn { padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; border: none; cursor: pointer; }
    .error-body .btn-primary { background: #00b207; color: white; }
    .error-body .btn-primary:hover { background: #009906; }
    .error-body .btn-secondary { background: #f3f4f6; color: #374151; }
    .error-body .btn-secondary:hover { background: #e5e7eb; }
    .contact-info { text-align: center; padding-top: 20px; border-top: 1px solid #f3f4f6; }
    .contact-info p { font-size: 13px; color: #9ca3af; margin: 0 0 8px; }
    .contact-link { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; font-weight: 600; color: #00b207; text-decoration: none; }
    .contact-link:hover { text-decoration: underline; }
    .error-footer { text-align: center; padding: 24px; color: #9ca3af; font-size: 13px; }

    /* ── Request Show / Detail pages ── */
    .content-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; align-items: start; }
    .sidebar-column { position: sticky; top: 80px; display: flex; flex-direction: column; gap: 20px; }
    .header-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .card-header.status-header { flex-wrap: wrap; gap: 10px; }
    .status-msg { font-size: 14px; color: #6b7280; }
    .status-msg-cancelled { font-size: 14px; color: #dc2626; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .info-item { }
    .notes-item { margin-top: 16px; }
    .info-label { font-size: 12px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 4px; }
    .info-value { font-size: 14px; color: #111827; line-height: 1.5; }
    .items-table { width: 100%; border-collapse: collapse; }
    .items-table th { padding: 10px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .items-table td { padding: 12px 16px; font-size: 14px; color: #374151; border-bottom: 1px solid #f3f4f6; vertical-align: middle; }
    .items-table tr:last-child td { border-bottom: none; }
    .product-cell { display: flex; align-items: center; gap: 12px; }
    .fee-breakdown { background: #f9fafb; border-radius: 8px; padding: 12px 16px; margin: 4px 0 8px; }
    .btn-full { width: 100%; justify-content: center; }
    .doc-links { display: flex; flex-direction: column; gap: 8px; }

    /* Show page timeline (flex-based, not track-list style) */
    .timeline { display: flex; flex-direction: column; }
    .timeline .timeline-item { display: flex; gap: 12px; position: relative; padding-bottom: 20px; }
    .timeline .timeline-item:last-child { padding-bottom: 0; }
    .timeline .timeline-item::before { content: ''; position: absolute; left: 6px; top: 18px; bottom: 0; width: 2px; background: #f3f4f6; }
    .timeline .timeline-item:last-child::before { display: none; }
    .timeline .timeline-dot { position: static; flex-shrink: 0; margin-top: 3px; width: 14px; height: 14px; border-radius: 50%; background: #00b207; border: 2px solid white; box-shadow: 0 0 0 2px #00b207; }
    .timeline-content { flex: 1; min-width: 0; }
    .timeline-status { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 2px; }
    .timeline-note { font-size: 13px; color: #6b7280; margin-bottom: 4px; }
    .timeline-date { font-size: 12px; color: #9ca3af; }
    .modal-form-group { margin-bottom: 20px; }

    /* Modals */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: opacity 0.2s; }
    .modal-overlay.active { opacity: 1; pointer-events: all; }
    .modal { background: white; border-radius: 16px; padding: 32px; max-width: 480px; width: calc(100% - 40px); box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    .modal h3 { font-size: 18px; font-weight: 700; color: #111827; margin-bottom: 12px; }
    .modal > p { font-size: 14px; color: #6b7280; margin-bottom: 20px; line-height: 1.6; }
    .modal-actions { display: flex; gap: 12px; justify-content: flex-end; flex-wrap: wrap; }

    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } .sidebar-column { position: static; } }
    @media (max-width: 600px) { .info-grid { grid-template-columns: 1fr; } .header-actions { width: 100%; flex-direction: column; align-items: stretch; } .modal { padding: 24px; } .modal-actions { flex-direction: column-reverse; } }

    /* ── Track Shipment page ── */
    .search-card {
      background: white; border-radius: 12px; padding: 32px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08); margin-bottom: 24px;
    }
    .search-form { display: flex; gap: 12px; }
    .search-input {
      flex: 1; padding: 12px 16px; border: 1px solid #e5e7eb; border-radius: 8px;
      font-size: 15px; font-family: inherit; color: #374151; background: white;
      transition: border-color 0.2s, box-shadow 0.2s; outline: none;
    }
    .search-input:focus { border-color: #00b207; box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
    .search-btn {
      padding: 12px 24px; background: #00b207; color: white; border: none;
      border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer;
      display: inline-flex; align-items: center; gap: 8px;
      transition: background 0.2s; white-space: nowrap; font-family: inherit;
    }
    .search-btn:hover { background: #009906; }
    .result-card {
      background: white; border-radius: 12px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden;
    }
    .error-message { text-align: center; padding: 60px 40px; color: #6b7280; }
    .error-message i { font-size: 40px; margin-bottom: 16px; color: #d1d5db; display: block; }
    .error-message h3 { font-size: 18px; color: #374151; margin-bottom: 8px; }
    .shipment-header { padding: 24px 28px; border-bottom: 1px solid #e5e7eb; }
    .shipment-header .shipment-number { font-size: 20px; font-weight: 700; color: #111827; margin-bottom: 4px; }
    .shipment-meta { font-size: 14px; color: #6b7280; }
    .status-banner {
      padding: 20px 28px; display: flex; justify-content: space-between;
      align-items: center; gap: 16px; flex-wrap: wrap;
      background: #f9fafb; border-bottom: 1px solid #e5e7eb;
    }
    .status-banner.draft, .status-banner.submitted { background: #eff6ff; border-bottom-color: #bfdbfe; }
    .status-banner.quoted, .status-banner.scheduled { background: #fef3c7; border-bottom-color: #fde68a; }
    .status-banner.paid, .status-banner.picked_up { background: #ecfdf5; border-bottom-color: #a7f3d0; }
    .status-banner.in_transit { background: #eff6ff; border-bottom-color: #bfdbfe; }
    .status-banner.delivered, .status-banner.completed { background: #d1fae5; border-bottom-color: #6ee7b7; }
    .status-text h3 { font-size: 18px; font-weight: 600; color: #111827; margin: 0 0 4px; }
    .status-text p { font-size: 13px; color: #6b7280; margin: 0; }
    .shipment-details { padding: 24px 28px; }
    .route-visual {
      display: flex; align-items: center;
      background: #f9fafb; border-radius: 10px; padding: 20px; margin-bottom: 24px;
    }
    .route-point { flex: 1; text-align: center; }
    .route-point i { font-size: 24px; color: #00b207; margin-bottom: 8px; display: block; }
    .route-point h4 { font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 4px; }
    .route-point p { font-size: 12px; color: #9ca3af; margin: 0; text-transform: uppercase; letter-spacing: 0.05em; }
    .route-line { flex: 0 0 80px; text-align: center; position: relative; color: #d1d5db; }
    .route-line::before {
      content: ''; position: absolute; top: 50%; left: 0; right: 0;
      height: 2px; background: #e5e7eb; transform: translateY(-50%);
    }
    .route-line.complete::before { background: #00b207; }
    .route-line i { position: relative; font-size: 18px; background: white; padding: 0 8px; }
    .route-line.complete i { color: #00b207; }
    .destinations-list { margin-bottom: 24px; }
    .destinations-list > h4 { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 12px; }
    .destinations-list .destination-item { display: flex; align-items: center; gap: 12px; }
    .destination-number {
      width: 28px; height: 28px; border-radius: 50%; background: #e5e7eb; color: #374151;
      display: flex; align-items: center; justify-content: center;
      font-size: 13px; font-weight: 600; flex-shrink: 0;
    }
    .destination-info { flex: 1; }
    .destination-info h5 { font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 2px; }
    .destination-info p { font-size: 13px; color: #6b7280; margin: 0; }
    .destination-status {
      font-size: 12px; font-weight: 500; padding: 3px 10px;
      border-radius: 20px; background: #f3f4f6; color: #6b7280; white-space: nowrap;
    }
    .destination-status.delivered { background: #d1fae5; color: #059669; }
    .destination-status.in_transit { background: #cffafe; color: #0891b2; }
    .destination-status.pending, .destination-status.scheduled { background: #fef3c7; color: #b45309; }
    .timeline { border-top: 1px solid #f3f4f6; padding-top: 20px; }
    .timeline > h4 { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 16px; }
    .timeline-list { position: relative; padding-left: 24px; }
    .timeline-list::before {
      content: ''; position: absolute; left: 7px; top: 4px; bottom: 4px; width: 2px; background: #e5e7eb;
    }
    .timeline-item { position: relative; padding-bottom: 20px; }
    .timeline-item:last-child { padding-bottom: 0; }
    .timeline-dot {
      position: absolute; left: -20px; top: 4px;
      width: 14px; height: 14px; border-radius: 50%;
      background: #00b207; border: 2px solid white; box-shadow: 0 0 0 2px #00b207;
    }
    .timeline-item h5 { font-size: 14px; font-weight: 600; color: #111827; margin: 0 0 4px; }
    .timeline-item p { font-size: 13px; color: #6b7280; margin: 0 0 4px; }
    .timeline-item time { font-size: 12px; color: #9ca3af; }
    .track-footer { text-align: center; padding: 24px; color: #9ca3af; font-size: 13px; }
    .track-footer a { color: #6b7280; text-decoration: none; }
    .track-footer a:hover { color: #00b207; }
    @media (max-width: 600px) {
      .search-form { flex-direction: column; }
      .search-btn { width: 100%; justify-content: center; }
      .search-card { padding: 20px; }
      .shipment-header, .shipment-details, .status-banner { padding: 16px 20px; }
      .route-visual { flex-direction: column; gap: 12px; }
      .route-line { flex: 0; width: 40px; transform: rotate(90deg); }
      .status-banner { flex-direction: column; align-items: flex-start; }
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
  </style>
</head>
<body>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <img src="<?= url('assets/images/logo.png') ?>" alt="OCSAPP" class="sidebar-logo-img">
          <div class="sidebar-logo-text">
            <span class="sidebar-logo-brand">OCSAPP</span>
            <span class="sidebar-logo-portal"><?= $t['portal_title'] ?></span>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">

        <?php /* ── Always accessible ── */ ?>
        <a href="<?= url('distribution/dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
          <i class="fas fa-chart-line"></i>
          <span><?= $t['dashboard'] ?></span>
        </a>

        <?php /* ── Procurement section ── */ ?>
        <div class="nav-section-label"><?= $t['procurement'] ?></div>
        <?php if ($_bizIsPending): ?>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-plus-circle"></i>
          <span><?= $t['new_request'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-clipboard-list"></i>
          <span><?= $t['my_requests'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-file-invoice-dollar"></i>
          <span><?= $t['my_invoices'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?>">
          <i class="fas fa-money-check-alt"></i>
          <span><?= $t['payables'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <?php else: ?>
        <a href="<?= url('distribution/requests/create') ?>" class="nav-link <?= ($currentPage ?? '') === 'request-create' ? 'active' : '' ?>">
          <i class="fas fa-plus-circle"></i>
          <span><?= $t['new_request'] ?></span>
        </a>
        <a href="<?= url('distribution/requests') ?>" class="nav-link <?= ($currentPage ?? '') === 'requests' ? 'active' : '' ?>">
          <i class="fas fa-clipboard-list"></i>
          <span><?= $t['my_requests'] ?></span>
          <span id="bizDraftBadge" class="notif-count-badge<?= $_bizDraftCount > 0 ? '' : ' hidden' ?>"><?= $_bizDraftCount > 9 ? '9+' : $_bizDraftCount ?></span>
          <span id="bizActiveReqBadge" class="notif-count-badge<?= $_bizActiveRequestCount > 0 ? '' : ' hidden' ?>" style="background:#d97706;"><?= $_bizActiveRequestCount > 9 ? '9+' : $_bizActiveRequestCount ?></span>
        </a>
        <a href="<?= url('distribution/invoices') ?>" class="nav-link <?= ($currentPage ?? '') === 'invoices' ? 'active' : '' ?>">
          <i class="fas fa-file-invoice-dollar"></i>
          <span><?= $t['my_invoices'] ?></span>
          <span id="bizInvoiceBadge" class="notif-count-badge<?= $_bizUnpaidInvoiceCount > 0 ? '' : ' hidden' ?>"><?= $_bizUnpaidInvoiceCount > 9 ? '9+' : $_bizUnpaidInvoiceCount ?></span>
        </a>
        <a href="<?= url('distribution/payables') ?>" class="nav-link <?= ($currentPage ?? '') === 'payables' ? 'active' : '' ?>">
          <i class="fas fa-money-check-alt"></i>
          <span><?= $t['payables'] ?></span>
          <span id="bizPayablesBadge" class="notif-count-badge<?= ($_bizUnpaidPayablesCount ?? 0) > 0 ? '' : ' hidden' ?>" style="background:#d97706;"><?= ($_bizUnpaidPayablesCount ?? 0) > 9 ? '9+' : ($_bizUnpaidPayablesCount ?? 0) ?></span>
        </a>
        <?php endif; ?>

        <?php /* ── Distribution section ── */ ?>
        <div class="nav-section-label"><?= $t['distribution'] ?></div>
        <?php if ($_bizIsPending): ?>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-box"></i>
          <span><?= $t['new_shipment'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-truck-loading"></i>
          <span><?= $t['my_shipments'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-route"></i>
          <span><?= $t['recurring_routes'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <span class="nav-link locked" title="<?= $t['locked_tooltip'] ?? 'Available after account approval' ?>">
          <i class="fas fa-search-location"></i>
          <span><?= $t['track_shipment'] ?></span>
          <i class="fas fa-lock nav-lock-icon"></i>
        </span>
        <?php else: ?>
        <a href="<?= url('distribution/shipments/create') ?>" class="nav-link <?= ($currentPage ?? '') === 'shipment-create' ? 'active' : '' ?>">
          <i class="fas fa-box"></i>
          <span><?= $t['new_shipment'] ?></span>
        </a>
        <a href="<?= url('distribution/shipments') ?>" class="nav-link <?= ($currentPage ?? '') === 'shipments' ? 'active' : '' ?>">
          <i class="fas fa-truck-loading"></i>
          <span><?= $t['my_shipments'] ?></span>
        </a>
        <a href="<?= url('distribution/routes') ?>" class="nav-link <?= ($currentPage ?? '') === 'routes' ? 'active' : '' ?>">
          <i class="fas fa-route"></i>
          <span><?= $t['recurring_routes'] ?></span>
        </a>
        <a href="<?= url('distribution/shipments/track') ?>" class="nav-link <?= ($currentPage ?? '') === 'track' ? 'active' : '' ?>">
          <i class="fas fa-search-location"></i>
          <span><?= $t['track_shipment'] ?></span>
        </a>
        <?php endif; ?>

        <?php /* ── Always accessible ── */ ?>
        <div class="nav-section-label">&nbsp;</div>
        <a href="<?= url('distribution/messages') ?>" class="nav-link <?= ($currentPage ?? '') === 'messages' ? 'active' : '' ?>">
          <i class="fas fa-comments"></i>
          <span><?= $t['messages'] ?></span>
          <span id="bizMsgNavBadge" class="notif-count-badge<?= $_bizUnreadMsgCount > 0 ? '' : ' hidden' ?>"><?= $_bizUnreadMsgCount > 0 ? min($_bizUnreadMsgCount, 99) : '' ?></span>
        </a>
        <a href="<?= url('distribution/emails') ?>" class="nav-link <?= ($currentPage ?? '') === 'emails' ? 'active' : '' ?>">
          <i class="fas fa-envelope-open-text"></i>
          <span><?= $t['my_emails'] ?></span>
        </a>
        <a href="<?= url('distribution/documents') ?>" class="nav-link <?= ($currentPage ?? '') === 'documents' ? 'active' : '' ?>">
          <i class="fas fa-folder-open"></i>
          <span><?= $t['documents'] ?></span>
        </a>
        <a href="<?= url('distribution/settings') ?>" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
          <i class="fas fa-cog"></i>
          <span><?= $t['settings'] ?></span>
        </a>

        <?php if ($_bizIsPending): ?>
        <div class="nav-unlock-hint">
          <i class="fas fa-lock" style="margin-right:5px;"></i>
          <?= $t['unlock_hint'] ?>
          <br><a href="mailto:info@ocsapp.ca"><?= $t['contact_us'] ?> &rarr;</a>
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
        <div class="topbar-left">
          <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle menu">
            <i class="fas fa-bars"></i>
          </button>
          <h1><?= htmlspecialchars($pageTitle ?? $t['portal_title']) ?></h1>
        </div>
        <div class="user-menu">
          <!-- Notification Bell -->
          <div class="notif-wrapper" id="bizNotifWrapper">
            <button class="notif-bell" id="bizNotifBellBtn" type="button" onclick="toggleBizNotifPanel()">
              <i class="fas fa-bell"></i>
              <span class="notif-badge hidden" id="bizNotifBadge">0</span>
            </button>
            <div class="notif-panel" id="bizNotifPanel">
              <div class="notif-panel-header">
                <h4><i class="fas fa-bell"></i> <?= $currentLang === 'fr' ? 'Notifications' : 'Notifications' ?></h4>
                <button class="notif-mark-all hidden" id="bizNotifMarkAllBtn" onclick="markAllBizNotifRead()">
                  <i class="fas fa-check-double"></i> <?= $currentLang === 'fr' ? 'Tout marquer lu' : 'Mark all read' ?>
                </button>
              </div>
              <div class="notif-list" id="bizNotifList">
                <div class="notif-empty">
                  <i class="fas fa-bell-slash"></i>
                  <?= $currentLang === 'fr' ? 'Aucune notification' : 'No notifications' ?>
                </div>
              </div>
            </div>
          </div>

          <!-- Language Switcher -->
          <div class="lang-switcher">
            <form method="POST" action="<?= url('language/switch') ?>" style="display:inline;">
              <?= csrfField() ?>
              <input type="hidden" name="lang" value="en">
              <button type="submit" class="lang-btn<?= $currentLang === 'en' ? ' active' : '' ?>">EN</button>
            </form>
            <span class="lang-divider">|</span>
            <form method="POST" action="<?= url('language/switch') ?>" style="display:inline;">
              <?= csrfField() ?>
              <input type="hidden" name="lang" value="fr">
              <button type="submit" class="lang-btn<?= $currentLang === 'fr' ? ' active' : '' ?>">FR</button>
            </form>
          </div>
          <div class="user-info">
            <div class="user-name"><?= htmlspecialchars(($business['first_name'] ?? '') . ' ' . ($business['last_name'] ?? '')) ?></div>
            <div class="user-role"><?= htmlspecialchars($business['company_name'] ?? $t['dist_account']) ?></div>
          </div>
          <a href="<?= url('distribution/logout') ?>" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i> <?= $t['logout'] ?>
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

// ── Unified badge refresh — one API call updates every sidebar badge ──────────
(function pollBizNotifications() {
  var prevNotifCount = -1;

  // Shared AudioContext — unlocked on first user gesture
  var _bizAudioCtx = null;
  var _bizAudioUnlocked = false;
  function _unlockBizAudio() {
    if (_bizAudioUnlocked) return;
    try {
      _bizAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
      var buf = _bizAudioCtx.createBuffer(1, 1, 22050);
      var src = _bizAudioCtx.createBufferSource();
      src.buffer = buf; src.connect(_bizAudioCtx.destination); src.start(0);
      _bizAudioUnlocked = true;
    } catch(e) {}
  }
  ['click','touchstart','keydown'].forEach(function(evt) {
    document.addEventListener(evt, _unlockBizAudio, { once: true, passive: true });
  });

  window.playChimeBiz = function() {
    if (localStorage.getItem('biz_sound_enabled') === 'off') return;
    try {
      if (!_bizAudioCtx) _bizAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
      var ctx = _bizAudioCtx;
      function _doBizChime() {
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
      }
      if (ctx.state === 'suspended') {
        ctx.resume().then(_doBizChime).catch(function() {});
      } else {
        _doBizChime();
      }
    } catch(e) {}
  };

  // Exported so footer SSE handler can call it
  window._refreshAllBizBadges = function() {
    fetch('<?= url('api/business/notifications/count') ?>', { credentials: 'same-origin' })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (!data || !data.success) return;

        var n   = data.unread_count || 0;
        var dr  = data.draft_count || 0;
        var act = data.active_request_count || 0;
        var inv = data.unpaid_invoice_count || 0;
        var pay = data.unpaid_payables_count || 0;
        var msg = data.unread_msg_count || 0;

        // ── Bell badge ──────────────────────────────────────────────────────
        var bell = document.getElementById('bizNotifBadge');
        if (bell) {
          if (n > 0) { bell.textContent = n > 9 ? '9+' : n; bell.classList.remove('hidden'); }
          else        { bell.classList.add('hidden'); }
        }

        // ── My Requests — drafts (red) ──────────────────────────────────────
        var draftB = document.getElementById('bizDraftBadge');
        if (draftB) {
          if (dr > 0) { draftB.textContent = dr > 9 ? '9+' : dr; draftB.classList.remove('hidden'); }
          else         { draftB.classList.add('hidden'); }
        }

        // ── My Requests — active/in-flight (amber) ──────────────────────────
        var actB = document.getElementById('bizActiveReqBadge');
        if (actB) {
          if (act > 0) { actB.textContent = act > 9 ? '9+' : act; actB.classList.remove('hidden'); }
          else          { actB.classList.add('hidden'); }
        }

        // ── Invoices ────────────────────────────────────────────────────────
        var invB = document.getElementById('bizInvoiceBadge');
        if (invB) {
          if (inv > 0) { invB.textContent = inv > 9 ? '9+' : inv; invB.classList.remove('hidden'); }
          else          { invB.classList.add('hidden'); }
        }

        // ── Payables — unpaid requests ──────────────────────────────────────
        var payB = document.getElementById('bizPayablesBadge');
        if (payB) {
          if (pay > 0) { payB.textContent = pay > 9 ? '9+' : pay; payB.classList.remove('hidden'); }
          else          { payB.classList.add('hidden'); }
        }

        // ── Messages ────────────────────────────────────────────────────────
        var msgB = document.getElementById('bizMsgNavBadge');
        if (msgB) {
          if (msg > 0) { msgB.textContent = msg > 99 ? '99+' : msg; msgB.classList.remove('hidden'); }
          else          { msgB.classList.add('hidden'); }
        }

        // ── Sound on new notification ───────────────────────────────────────
        if (prevNotifCount >= 0 && n > prevNotifCount) window.playChimeBiz();
        prevNotifCount = n;
      })
      .catch(function() {});
  };

  setTimeout(window._refreshAllBizBadges, 2000);
  setInterval(window._refreshAllBizBadges, 10000);
})();
</script>

