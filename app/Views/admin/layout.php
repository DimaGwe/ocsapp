<?php

/**
 * OCSAPP Admin Layout
 * File: app/Views/admin/layout.php
 */

// Include admin permission helper
require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

// Get current language from session or default to English
$currentLang = $_SESSION['language'] ?? 'fr';

// Get current user's role and department for permission checks
$userRole = $_SESSION['user']['role'] ?? null;
$userDept = $_SESSION['user']['department'] ?? null;

// Kick deleted admin accounts — if the user record is gone, destroy session and redirect.
if (!empty($_SESSION['user']['id'])) {
    try {
        $__admCheck = \Database::getConnection()->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $__admCheck->execute([(int)$_SESSION['user']['id']]);
        if (!$__admCheck->fetch()) {
            session_unset();
            session_destroy();
            header('Location: ' . url('login'));
            exit;
        }
    } catch (\Throwable $e) {}
}

// Fetch notification badge counts
$badgeCounts = [
    'orders' => 0,
    'leads' => 0,
    'distribution' => 0,
    'sellers' => 0,
];

try {
    $db = \Database::getConnection();

    // Count pending orders
    $stmt = $db->query("SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'processing')");
    if ($stmt && $row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $badgeCounts['orders'] = (int)$row['count'];
    }

    // Count new leads (status = 'new')
    $stmt = $db->query("SELECT COUNT(*) as count FROM leads WHERE status = 'new'");
    if ($stmt && $row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $badgeCounts['leads'] = (int)$row['count'];
    }

    // Count pending distribution requests
    $stmt = $db->query("SELECT COUNT(*) as count FROM distribution_requests WHERE status IN ('pending', 'submitted', 'approved', 'awaiting_payment')");
    if ($stmt && $row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $badgeCounts['distribution'] = (int)$row['count'];
    }

    // Count pending seller verification requests
    $stmt = $db->query("SELECT COUNT(*) as count FROM sellers WHERE verification_status = 'pending'");
    if ($stmt && $row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $badgeCounts['sellers'] = (int)$row['count'];
    }
} catch (Exception $e) {
    // Silently fail - badges will show 0
}

// Language translations for admin panel
$translations = [
    'en' => [
        'admin_panel' => 'Admin Panel',
        'dashboard' => 'Dashboard',
        'user_management' => 'User Management',
        'users' => 'Users',
        'sellers' => 'Marketplace Sellers',
        'business_accounts' => 'Business Accounts',
        'shipments' => 'Shipments',
        'delivery' => 'Delivery',
        'shops' => 'Shops',
        'catalog' => 'Catalog',
        'products' => 'Products',
        'stock_management' => 'Stock Management',
        'suppliers' => 'Suppliers',
        'purchase_orders' => 'Purchase Orders',
        'supplier_catalog' => 'Supplier Catalog',
        'planner' => 'Team Planner',
        'categories' => 'Categories',
        'sales_management' => 'Sales Management',
        'sales' => 'Sales',
        'orders' => 'Orders',
        'reports' => 'Reports',
        'marketing' => 'Marketing',
        'promo_banners' => 'Promo Banners',
        'advertisements' => 'Advertisements',
        'affiliates' => 'Affiliates',
        'coupons' => 'Coupons',
        'system' => 'System',
        'settings' => 'Settings',
        'payment_settings' => 'Payment Settings',
        'homepage_settings' => 'Homepage Settings',
        'cms' => 'CMS',
        'profile' => 'Profile',
        'logout' => 'Logout',
        'search_placeholder' => 'Search users, orders, products...',
        'all_rights' => 'All Rights Reserved',
    ],
    'fr' => [
        'admin_panel' => 'Panneau Admin',
        'dashboard' => 'Tableau de bord',
        'user_management' => 'Gestion Utilisateurs',
        'users' => 'Utilisateurs',
        'sellers' => 'Vendeurs de Marché',
        'business_accounts' => 'Comptes Entreprises',
        'shipments' => 'Expéditions',
        'delivery' => 'Livreurs',
        'shops' => 'Boutiques',
        'catalog' => 'Catalogue',
        'products' => 'Produits',
        'stock_management' => 'Gestion Stock',
        'suppliers' => 'Fournisseurs',
        'purchase_orders' => 'Bons de Commande',
        'supplier_catalog' => 'Catalogue Fournisseurs',
        'planner' => 'Planificateur d\'Équipe',
        'categories' => 'Catégories',
        'sales_management' => 'Gestion des Ventes',
        'sales' => 'Ventes',
        'orders' => 'Commandes',
        'reports' => 'Rapports',
        'marketing' => 'Marketing',
        'promo_banners' => 'Bannières Promo',
        'advertisements' => 'Publicités',
        'affiliates' => 'Affiliés',
        'coupons' => 'Coupons',
        'system' => 'Système',
        'settings' => 'Paramètres',
        'payment_settings' => 'Paramètres de Paiement',
        'homepage_settings' => 'Paramètres Page d\'Accueil',
        'cms' => 'CMS',
        'profile' => 'Profil',
        'logout' => 'Déconnexion',
        'search_placeholder' => 'Rechercher utilisateurs, commandes, produits...',
        'all_rights' => 'Tous droits réservés',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? $t['dashboard']) ?> – OCSAPP Admin</title>
  <?= csrfMeta() ?>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">

  <!-- Font Awesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  
  <style>
    :root {
      --primary: #00b207;
      --primary-600: #009206;
      --primary-700: #007a05;
      --dark: #1a1a1a;
      --gray-50: #fafafa;
      --gray-100: #f5f5f5;
      --gray-200: #e5e5e5;
      --gray-300: #d4d4d4;
      --gray-400: #a3a3a3;
      --gray-500: #6b7280;
      --gray-600: #4b5563;
      --gray-700: #374151;
      --gray-800: #1f2937;
      --gray-900: #111827;
      --border: #e5e5e5;
      --radius-sm: 8px;
      --radius-md: 10px;
      --radius-lg: 12px;
      --radius-xl: 16px;
      --radius-full: 999px;
      --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 28px rgba(0, 0, 0, 0.12);
      --transition-base: 200ms ease;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: var(--gray-100);
      color: var(--dark);
    }

    [x-cloak] { display: none !important; }

    /* Layout */
    .admin-layout {
      min-height: 100vh;
      display: flex;
    }

    /* Sidebar */
    .sidebar {
      width: 260px;
      background: var(--gray-900);
      color: white;
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
      z-index: 30;
      transition: transform var(--transition-base);
      scroll-behavior: smooth;
      scrollbar-width: thin;
      scrollbar-color: var(--gray-600) var(--gray-800);
    }

    .sidebar::-webkit-scrollbar {
      width: 6px;
    }

    .sidebar::-webkit-scrollbar-track {
      background: var(--gray-800);
    }

    .sidebar::-webkit-scrollbar-thumb {
      background-color: var(--gray-600);
      border-radius: 3px;
    }

    .sidebar::-webkit-scrollbar-thumb:hover {
      background-color: var(--gray-500);
    }

    .sidebar.hidden-mobile {
      transform: translateX(-100%);
    }

    .sidebar-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      height: 64px;
      padding: 0 24px;
      background: var(--gray-800);
      border-bottom: 1px solid var(--gray-700);
    }

    .sidebar-logo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .sidebar-logo-img {
      width: 40px;
      height: 40px;
      background: white;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 6px;
    }

    .sidebar-logo-img img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }

    .sidebar-brand {
      font-size: 20px;
      font-weight: 800;
      color: var(--primary);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    a.sidebar-logo {
      text-decoration: none;
      transition: opacity var(--transition-base);
    }

    a.sidebar-logo:hover {
      opacity: 0.9;
    }

    .view-site-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 32px;
      height: 32px;
      background: var(--gray-700);
      border-radius: var(--radius-sm);
      color: var(--gray-300);
      text-decoration: none;
      transition: all var(--transition-base);
      margin-left: auto;
      margin-right: 8px;
    }

    .view-site-btn:hover {
      background: var(--primary);
      color: white;
    }

    .view-site-btn i {
      font-size: 12px;
    }

    .sidebar-close {
      display: none;
      color: var(--gray-400);
      background: none;
      border: none;
      font-size: 20px;
      cursor: pointer;
      transition: color var(--transition-base);
    }

    .sidebar-close:hover {
      color: white;
    }

    .sidebar-nav {
      padding: 24px 16px;
    }

    .nav-section-title {
      display: flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      margin: 20px 8px 8px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--gray-400);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      transition: all var(--transition-base);
      background: linear-gradient(90deg, rgba(55, 65, 81, 0.5), transparent);
      border-left: 3px solid var(--gray-600);
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    }

    .nav-section-title::before {
      content: '';
      width: 4px;
      height: 4px;
      background: currentColor;
      border-radius: 50%;
      opacity: 0.6;
    }

    .nav-section-title:first-child {
      margin-top: 0;
    }

    .nav-section-title.section-active {
      color: var(--primary);
      border-left-color: var(--primary);
      background: linear-gradient(90deg, rgba(34, 197, 94, 0.15), transparent);
    }

    .nav-section-title.section-active::before {
      opacity: 1;
      box-shadow: 0 0 6px var(--primary);
    }

    /* Collapsible nav section headers */
    .nav-section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
      padding: 10px 16px;
      margin: 20px 8px 4px;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--gray-400);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background: linear-gradient(90deg, rgba(55, 65, 81, 0.5), transparent);
      border-left: 3px solid var(--gray-600);
      border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
      cursor: pointer;
      user-select: none;
      transition: all var(--transition-base);
    }

    .nav-section-header:first-child {
      margin-top: 0;
    }

    .nav-section-header:hover {
      color: var(--gray-200);
      border-left-color: var(--gray-400);
    }

    .nav-section-header.section-active {
      color: var(--primary);
      border-left-color: var(--primary);
      background: linear-gradient(90deg, rgba(34, 197, 94, 0.15), transparent);
    }

    .nav-section-chevron {
      font-size: 9px;
      transition: transform 250ms ease;
      flex-shrink: 0;
      opacity: 0.6;
    }

    .nav-section-header.collapsed .nav-section-chevron {
      transform: rotate(-90deg);
    }

    .nav-section-items {
      overflow: hidden;
      max-height: 1200px;
      opacity: 1;
      transition: max-height 300ms ease, opacity 200ms ease;
    }

    .nav-section-items.collapsed {
      max-height: 0;
      opacity: 0;
    }

    /* Role Badge */
    .role-badge-container {
      padding: 12px 24px;
      background: var(--gray-800);
      border-bottom: 1px solid var(--gray-700);
    }

    .role-badge {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.03em;
    }

    .role-badge.super-admin {
      background: linear-gradient(135deg, #f59e0b, #d97706);
      color: white;
    }

    .role-badge.admin {
      background: linear-gradient(135deg, #3b82f6, #2563eb);
      color: white;
    }

    .role-badge.admin-staff {
      background: linear-gradient(135deg, #6b7280, #4b5563);
      color: white;
    }

    .role-badge i {
      font-size: 10px;
    }

    /* Notification Badges */
    .nav-badge {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 16px;
      height: 16px;
      padding: 0 3px;
      margin-left: auto;
      background: #ef4444;
      color: white;
      font-size: 10px;
      font-weight: 600;
      border-radius: 8px;
      flex-shrink: 0;
      animation: badge-pulse 2s ease-in-out infinite;
    }

    .nav-badge.warning {
      background: #f59e0b;
    }

    .nav-badge.info {
      background: #3b82f6;
    }

    .nav-badge.success {
      background: #22c55e;
    }

    .nav-link.active .nav-badge {
      background: rgba(255, 255, 255, 0.3);
      animation: none;
    }

    .nav-badge.hidden { display: none; }

    @keyframes badge-pulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.05); }
    }

    .nav-link {
      display: flex;
      align-items: center;
      padding: 12px 16px;
      margin-bottom: 4px;
      color: var(--gray-300);
      text-decoration: none;
      border-radius: var(--radius-md);
      transition: all var(--transition-base);
      font-size: 14px;
      font-weight: 500;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .nav-link:hover {
      background: var(--gray-800);
      color: white;
    }

    .nav-link.active {
      background: var(--primary);
      color: white;
    }

    .nav-link i {
      width: 20px;
      min-width: 20px;
      margin-right: 12px;
      font-size: 16px !important;
      text-align: center;
      flex-shrink: 0;
    }

    .nav-link span {
      font-size: 14px !important;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      flex: 1;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      margin-left: 260px;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    /* Top Bar */
    .topbar {
      height: 64px;
      background: white;
      box-shadow: var(--shadow-sm);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      position: sticky;
      top: 0;
      z-index: 20;
    }

    .topbar-menu-btn {
      display: none;
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 20px;
      cursor: pointer;
      transition: color var(--transition-base);
    }

    .topbar-menu-btn:hover {
      color: var(--dark);
    }

    .topbar-search {
      flex: 1;
      max-width: 600px;
      margin: 0 32px;
      position: relative;
    }

    .topbar-search input {
      width: 100%;
      padding: 10px 16px 10px 40px;
      border: 2px solid var(--border);
      border-radius: var(--radius-md);
      font-size: 14px;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      transition: all var(--transition-base);
    }

    .topbar-search input:focus {
      outline: none;
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
    }

    .topbar-search i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray-400);
    }

    .topbar-actions {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .topbar-btn {
      position: relative;
      background: none;
      border: none;
      color: var(--gray-600);
      font-size: 20px;
      cursor: pointer;
      transition: color var(--transition-base);
    }

    .topbar-btn:hover {
      color: var(--dark);
    }

    .notification-badge {
      position: absolute;
      top: -4px;
      right: -4px;
      background: #ef4444;
      color: white;
      font-size: 10px;
      font-weight: 700;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    /* Notification Panel Styles */
    .notification-dropdown {
      position: relative;
    }

    .notification-panel {
      right: 0;
      left: auto;
      min-width: 360px;
      max-height: 480px;
      padding: 0 !important;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .notification-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px;
      border-bottom: 1px solid var(--border);
      background: var(--gray-50);
    }

    .notification-header h4 {
      margin: 0;
      font-size: 14px;
      font-weight: 600;
      color: var(--dark);
    }

    .mark-all-btn {
      background: none;
      border: none;
      font-size: 12px;
      color: var(--primary);
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 4px;
      padding: 4px 8px;
      border-radius: 4px;
      transition: background var(--transition-base);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .mark-all-btn:hover {
      background: rgba(0, 178, 7, 0.1);
    }

    .notification-list {
      flex: 1;
      overflow-y: auto;
      max-height: 360px;
    }

    .notification-item {
      display: flex;
      padding: 12px 16px;
      text-decoration: none;
      border-bottom: 1px solid var(--border);
      transition: background var(--transition-base);
      gap: 12px;
    }

    .notification-item:hover {
      background: var(--gray-50);
    }

    .notification-item.unread {
      background: #eff6ff;
      border-left: 3px solid var(--primary);
    }

    .notification-item.unread:hover {
      background: #dbeafe;
    }

    .notification-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 16px;
    }

    .notification-icon.icon-account_lockout {
      background: #fee2e2;
      color: #dc2626;
    }

    .notification-icon.icon-new_user {
      background: #dcfce7;
      color: #16a34a;
    }

    .notification-icon.icon-seller_application,
    .notification-icon.icon-seller_verified {
      background: #dbeafe;
      color: #2563eb;
    }

    .notification-icon.icon-new_order {
      background: #f0fdf4;
      color: #15803d;
    }

    .notification-icon.icon-low_stock {
      background: #fef3c7;
      color: #d97706;
    }

    .notification-icon.icon-system,
    .notification-icon.icon-security {
      background: var(--gray-100);
      color: var(--gray-600);
    }

    .notification-icon.icon-supplier_message { background: #ede9fe; color: #7c3aed; }
    .notification-icon.icon-task_assigned { background: #dbeafe; color: #2563eb; }
    .notification-icon.icon-task_completed { background: #dcfce7; color: #16a34a; }
    .notification-icon.icon-task_comment { background: #fef3c7; color: #d97706; }
    .notification-icon.icon-note_comment { background: #f3e8ff; color: #9333ea; }
    .notification-icon.icon-mention { background: #fce7f3; color: #db2777; }
    .notification-icon.icon-distribution_request { background: #e0f2fe; color: #0369a1; }
    .notification-icon.icon-driver_application { background: #fef9c3; color: #854d0e; }

    .notification-content {
      flex: 1;
      min-width: 0;
    }

    .notification-title {
      font-size: 13px;
      font-weight: 600;
      color: var(--dark);
      margin-bottom: 2px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .notification-message {
      font-size: 12px;
      color: var(--gray-600);
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .notification-time {
      font-size: 11px;
      color: var(--gray-500);
      margin-top: 4px;
    }

    .notification-loading,
    .notification-empty {
      padding: 40px 20px;
      text-align: center;
      color: var(--gray-500);
    }

    .notification-loading i,
    .notification-empty i {
      font-size: 32px;
      margin-bottom: 12px;
      display: block;
    }

    .notification-footer {
      padding: 12px 16px;
      border-top: 1px solid var(--border);
      background: var(--gray-50);
    }

    .view-all-link {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      color: var(--primary);
      text-decoration: none;
      font-size: 13px;
      font-weight: 500;
    }

    .view-all-link:hover {
      text-decoration: underline;
    }

    @keyframes notificationPulse {
      0%, 100% { transform: scale(1); }
      50% { transform: scale(1.1); }
    }

    .topbar-btn.has-notifications .notification-badge {
      animation: notificationPulse 2s ease-in-out infinite;
    }

    .user-dropdown {
      position: relative;
    }

    .user-btn {
      display: flex;
      align-items: center;
      gap: 12px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--gray-700);
      transition: color var(--transition-base);
    }

    .user-btn:hover {
      color: var(--dark);
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      background: var(--primary);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 14px;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .user-name {
      font-weight: 600;
      font-size: 14px;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .dropdown-menu {
      position: absolute;
      top: calc(100% + 8px);
      right: 0;
      background: white;
      border-radius: var(--radius-md);
      box-shadow: var(--shadow-lg);
      min-width: 200px;
      padding: 8px 0;
      z-index: 50;
    }

    .dropdown-link {
      display: flex;
      align-items: center;
      padding: 10px 16px;
      color: var(--gray-700);
      text-decoration: none;
      transition: background var(--transition-base);
      font-size: 14px;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .dropdown-link:hover {
      background: var(--gray-100);
    }

    .dropdown-link.active {
      background: var(--primary-50);
      color: var(--primary-600);
      font-weight: 500;
    }

    .dropdown-link.danger {
      color: #ef4444;
    }

    .dropdown-link i {
      width: 20px;
      margin-right: 12px;
      text-align: center;
    }

    .dropdown-divider {
      height: 1px;
      background: var(--border);
      margin: 8px 0;
    }

    /* Page Content */
    .page-content {
      flex: 1;
      padding: 32px;
    }

    /* Flash Messages */
    .alert {
      padding: 16px 20px;
      border-radius: var(--radius-md);
      margin-bottom: 24px;
      font-size: 14px;
      font-weight: 500;
      border-left: 4px solid;
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    .alert-success {
      background: #f0fdf4;
      border-color: #22c55e;
      color: #166534;
    }

    .alert-error {
      background: #fef2f2;
      border-color: #ef4444;
      color: #991b1b;
    }

    /* Footer */
    .admin-footer {
      background: white;
      border-top: 1px solid var(--border);
      padding: 16px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 13px;
      color: var(--gray-600);
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    /* Overlay */
    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0, 0, 0, 0.5);
      z-index: 25;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.show-mobile {
        transform: translateX(0);
      }

      .sidebar-close {
        display: block;
      }

      .main-content {
        margin-left: 0;
      }

      .topbar-menu-btn {
        display: block;
      }

      .topbar-search {
        display: none;
      }

      .sidebar-overlay.show {
        display: block;
      }

      .user-name {
        display: none;
      }
    }

    @media (max-width: 640px) {
      .page-content {
        padding: 20px;
      }

      .topbar {
        padding: 0 16px;
      }

      .admin-footer {
        padding: 12px 16px;
        flex-direction: column;
        gap: 8px;
        text-align: center;
      }
    }
  </style>

  <?php
    // Per-page stylesheet: auto-link public/assets/css/pages/admin-<currentPage>.css
    // if it exists. Lets admin views move their inline <style> into a cached file.
    $___pageCss = 'css/pages/admin-' . ($currentPage ?? '') . '.css';
    if (!empty($currentPage) && file_exists(BASE_PATH . '/public/assets/' . $___pageCss)):
  ?>
  <link rel="stylesheet" href="<?= asset($___pageCss) ?>">
  <?php endif; ?>
</head>
<body>
  <div class="admin-layout">
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <!-- Logo -->
      <div class="sidebar-header">
        <a href="<?= url('/') ?>" class="sidebar-logo" title="<?= $currentLang === 'fr' ? 'Voir le site' : 'View Site' ?>" target="_blank">
          <div class="sidebar-logo-img">
            <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo">
          </div>
          <span class="sidebar-brand">OCSAPP</span>
        </a>
        <a href="<?= url('/') ?>" class="view-site-btn" target="_blank" title="<?= $currentLang === 'fr' ? 'Ouvrir le site' : 'Open Site' ?>">
          <i class="fa-solid fa-external-link-alt"></i>
        </a>
        <button class="sidebar-close" onclick="toggleSidebar()">✕</button>
      </div>

      <!-- Role Badge -->
      <div class="role-badge-container">
        <?php
        $roleClass = str_replace('_', '-', $userRole);
        $roleLabels = [
            'super_admin' => ['icon' => 'fa-crown', 'label' => $currentLang === 'fr' ? 'Super Admin' : 'Super Admin'],
            'admin' => ['icon' => 'fa-shield-halved', 'label' => $currentLang === 'fr' ? 'Administrateur' : 'Admin'],
            'admin_staff' => ['icon' => 'fa-user-gear', 'label' => $currentLang === 'fr' ? 'Personnel Admin' : 'Admin Staff'],
        ];
        $deptLabels = [
            'ops'        => ['en' => 'Operations',  'fr' => 'Opérations'],
            'finance'    => ['en' => 'Finance',      'fr' => 'Finance'],
            'tech'       => ['en' => 'IT / Infra',   'fr' => 'Informatique'],
            'support'    => ['en' => 'Support',      'fr' => 'Support'],
            'logistics'  => ['en' => 'Logistics',    'fr' => 'Logistique'],
            'management' => ['en' => 'Management',   'fr' => 'Direction'],
        ];
        $roleInfo = $roleLabels[$userRole] ?? ['icon' => 'fa-user', 'label' => ucfirst($userRole)];
        $deptLabel = $userDept && isset($deptLabels[$userDept])
            ? $deptLabels[$userDept][$currentLang] ?? $deptLabels[$userDept]['en']
            : null;
        ?>
        <span class="role-badge <?= $roleClass ?>">
          <i class="fa-solid <?= $roleInfo['icon'] ?>"></i>
          <?= $roleInfo['label'] ?><?= $deptLabel ? ' &mdash; ' . htmlspecialchars($deptLabel) : '' ?>
        </span>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <?php
        $sectionPages = [
            'admin'      => ['dashboard', 'planner', 'users', 'deleted-users'],
            'marketplace'=> ['buyers', 'sellers', 'shops', 'ocs-store', 'products', 'stock', 'categories'],
            'drivers'    => ['delivery', 'drivers-list', 'pickup-requests', 'training', 'route-replay', 'driver-activity', 'driver-earnings', 'vehicles', 'live-map', 'route-optimizer', 'zones'],
            'suppliers'  => ['suppliers', 'purchase-orders', 'supplier-catalog', 'payables', 'receivables'],
            'business'   => ['business-accounts', 'distribution'],
            'operations' => ['orders', 'shipments'],
            'crm'        => ['leads', 'support', 'agent-dashboard', 'call-log', 'waitlist'],
            'marketing'  => ['sales', 'promo-banners', 'ads', 'affiliates', 'coupons', 'marketing', 'content-creator', 'content-library', 'newsletter'],
            'analytics'  => ['analytics', 'reports'],
            'content'    => ['homepage', 'homepage-settings', 'cms', 'content-pages', 'legal', 'sliders', 'emails', 'translations'],
            'system'     => ['settings', 'payment-settings', 'integrations', 'email-log'],
        ];

        $activeSection = '';
        foreach ($sectionPages as $section => $pages) {
            if (in_array($currentPage ?? '', $pages)) {
                $activeSection = $section;
                break;
            }
        }
        ?>

        <!-- ADMIN -->
        <div class="nav-section-header <?= $activeSection === 'admin' || in_array($currentPage ?? '', ['dashboard', 'planner']) ? 'section-active' : '' ?>" data-section="admin">
          <span><?= $currentLang === 'fr' ? 'Admin' : 'Admin' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="admin">
          <?php if (AdminPermissionHelper::canAccessMenu('dashboard', $userRole)): ?>
          <a href="<?= url('admin/dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            <span><?= $t['dashboard'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('planner', $userRole)): ?>
          <a href="<?= url('admin/planner') ?>" class="nav-link <?= ($currentPage ?? '') === 'planner' ? 'active' : '' ?>">
            <i class="fa-solid fa-calendar-check"></i>
            <span><?= $t['planner'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('users', $userRole)): ?>
          <a href="<?= url('admin/users') ?>" class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            <span><?= $t['users'] ?></span>
          </a>
          <a href="<?= url('admin/deleted-users') ?>" class="nav-link <?= ($currentPage ?? '') === 'deleted-users' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-slash"></i>
            <span><?= $currentLang === 'fr' ? 'Utilisateurs supprimés' : 'Deleted Users' ?></span>
          </a>
          <?php endif; ?>
        </div>

        <!-- MARKETPLACE -->
        <?php
        $mktItems = ['sellers', 'shops', 'ocs-store', 'products', 'stock', 'categories'];
        $hasMktAccess = false;
        foreach ($mktItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasMktAccess = true; break; }
        }
        if ($hasMktAccess || AdminPermissionHelper::canAccessMenu('users', $userRole)):
        ?>
        <div class="nav-section-header <?= $activeSection === 'marketplace' ? 'section-active' : '' ?>" data-section="marketplace">
          <span><?= $currentLang === 'fr' ? 'Marketplace' : 'Marketplace' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="marketplace">
          <?php if (AdminPermissionHelper::canAccessMenu('users', $userRole)): ?>
          <a href="<?= url('admin/buyers') ?>" class="nav-link <?= ($currentPage ?? '') === 'buyers' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-tag"></i>
            <span><?= $currentLang === 'fr' ? 'Acheteurs' : 'Buyers' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('sellers', $userRole)): ?>
          <a href="<?= url('admin/sellers') ?>" class="nav-link <?= ($currentPage ?? '') === 'sellers' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-tie"></i>
            <span><?= $currentLang === 'fr' ? 'Vendeurs' : 'Sellers' ?></span>
            <span id="adminSellersBadge" class="nav-badge info<?= $badgeCounts['sellers'] > 0 ? '' : ' hidden' ?>"><?= $badgeCounts['sellers'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('shops', $userRole)): ?>
          <a href="<?= url('admin/shops') ?>" class="nav-link <?= ($currentPage ?? '') === 'shops' ? 'active' : '' ?>">
            <i class="fa-solid fa-shop"></i>
            <span><?= $t['shops'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('ocs-store', $userRole)): ?>
          <a href="<?= url('admin/ocs-store/edit') ?>" class="nav-link <?= ($currentPage ?? '') === 'ocs-store' ? 'active' : '' ?>">
            <i class="fa-solid fa-store"></i>
            <span>OCSAPP Store</span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('products', $userRole)): ?>
          <a href="<?= url('admin/products') ?>" class="nav-link <?= ($currentPage ?? '') === 'products' ? 'active' : '' ?>">
            <i class="fa-solid fa-box"></i>
            <span><?= $t['products'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('stock', $userRole)): ?>
          <a href="<?= url('admin/products/stock') ?>" class="nav-link <?= ($currentPage ?? '') === 'stock' ? 'active' : '' ?>">
            <i class="fa-solid fa-warehouse"></i>
            <span><?= $t['stock_management'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('categories', $userRole)): ?>
          <a href="<?= url('admin/categories') ?>" class="nav-link <?= ($currentPage ?? '') === 'categories' ? 'active' : '' ?>">
            <i class="fa-solid fa-tags"></i>
            <span><?= $t['categories'] ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- DRIVERS -->
        <?php if (AdminPermissionHelper::canAccessMenu('delivery', $userRole)): ?>
        <div class="nav-section-header <?= $activeSection === 'drivers' ? 'section-active' : '' ?>" data-section="drivers">
          <span><?= $currentLang === 'fr' ? 'Livraison' : 'Delivery' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="drivers">
          <a href="<?= url('/admin/delivery') ?>" class="nav-link <?= ($currentPage ?? '') === 'delivery' ? 'active' : '' ?>">
            <i class="fa-solid fa-truck"></i>
            <span><?= $currentLang === 'fr' ? 'Tableau de bord livraison' : 'Delivery Dashboard' ?></span>
          </a>
          <a href="<?= url('admin/drivers') ?>" class="nav-link <?= ($currentPage ?? '') === 'drivers-list' ? 'active' : '' ?>">
            <i class="fa-solid fa-motorcycle"></i>
            <span><?= $currentLang === 'fr' ? 'Livreurs' : 'Drivers' ?></span>
          </a>
          <a href="<?= url('admin/pickup-requests') ?>" class="nav-link <?= ($currentPage ?? '') === 'pickup-requests' ? 'active' : '' ?>">
            <i class="fas fa-truck-loading"></i>
            <span><?= $currentLang === 'fr' ? 'Demandes de ramassage' : 'Pickup Requests' ?></span>
          </a>
          <a href="<?= url('admin/training') ?>" class="nav-link <?= ($currentPage ?? '') === 'training' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span><?= $currentLang === 'fr' ? 'Formation livreurs' : 'Driver Training' ?></span>
          </a>
          <a href="<?= url('admin/delivery/route-replay') ?>" class="nav-link <?= ($currentPage ?? '') === 'route-replay' ? 'active' : '' ?>">
            <i class="fas fa-route"></i>
            <span><?= $currentLang === 'fr' ? 'Relecture de route' : 'Route Replay' ?></span>
          </a>
          <a href="<?= url('admin/driver-activity') ?>" class="nav-link <?= ($currentPage ?? '') === 'driver-activity' ? 'active' : '' ?>">
            <i class="fas fa-history"></i>
            <span><?= $currentLang === 'fr' ? 'Activite livreurs' : 'Driver Activity' ?></span>
          </a>
          <a href="<?= url('admin/delivery/earnings') ?>" class="nav-link <?= ($currentPage ?? '') === 'driver-earnings' ? 'active' : '' ?>">
            <i class="fas fa-dollar-sign"></i>
            <span><?= $currentLang === 'fr' ? 'Gains livreurs' : 'Driver Earnings' ?></span>
          </a>
          <a href="<?= url('admin/delivery/vehicles') ?>" class="nav-link <?= ($currentPage ?? '') === 'vehicles' ? 'active' : '' ?>">
            <i class="fa-solid fa-car-side"></i>
            <span><?= $currentLang === 'fr' ? 'Flotte de vehicules' : 'Fleet Vehicles' ?></span>
          </a>
          <a href="<?= url('admin/delivery/live-map') ?>" class="nav-link <?= ($currentPage ?? '') === 'live-map' ? 'active' : '' ?>">
            <i class="fa-solid fa-map-location-dot"></i>
            <span><?= $currentLang === 'fr' ? 'Carte en direct' : 'Live Map' ?></span>
          </a>
          <a href="<?= url('admin/delivery/route-optimizer') ?>" class="nav-link <?= ($currentPage ?? '') === 'route-optimizer' ? 'active' : '' ?>">
            <i class="fa-solid fa-diagram-project"></i>
            <span><?= $currentLang === 'fr' ? 'Optimiseur de route' : 'Route Optimizer' ?></span>
          </a>
          <a href="<?= url('admin/delivery/zones') ?>" class="nav-link <?= ($currentPage ?? '') === 'zones' ? 'active' : '' ?>">
            <i class="fas fa-map-marked-alt"></i>
            <span><?= $currentLang === 'fr' ? 'Zones de livraison' : 'Delivery Zones' ?></span>
          </a>
        </div>
        <?php endif; ?>

        <!-- SUPPLIERS -->
        <?php
        $suppliersItems = ['suppliers', 'purchase-orders', 'supplier-catalog', 'payables', 'receivables'];
        $hasSuppliersAccess = false;
        foreach ($suppliersItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasSuppliersAccess = true; break; }
        }
        if ($hasSuppliersAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'suppliers' ? 'section-active' : '' ?>" data-section="suppliers">
          <span><?= $currentLang === 'fr' ? 'Fournisseurs' : 'Suppliers' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="suppliers">
          <?php if (AdminPermissionHelper::canAccessMenu('suppliers', $userRole)): ?>
          <a href="<?= url('admin/suppliers') ?>" class="nav-link <?= ($currentPage ?? '') === 'suppliers' ? 'active' : '' ?>">
            <i class="fa-solid fa-truck-ramp-box"></i>
            <span><?= $t['suppliers'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('purchase-orders', $userRole)): ?>
          <a href="<?= url('admin/purchase-orders') ?>" class="nav-link <?= ($currentPage ?? '') === 'purchase-orders' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice"></i>
            <span><?= $t['purchase_orders'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('supplier-catalog', $userRole)): ?>
          <a href="<?= url('admin/supplier-catalog') ?>" class="nav-link <?= ($currentPage ?? '') === 'supplier-catalog' ? 'active' : '' ?>">
            <i class="fa-solid fa-book"></i>
            <span><?= $t['supplier_catalog'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('payables', $userRole)): ?>
          <a href="<?= url('admin/payables') ?>" class="nav-link <?= ($currentPage ?? '') === 'payables' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <span><?= $currentLang === 'fr' ? 'Comptes fournisseurs' : 'Payables' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('receivables', $userRole)): ?>
          <a href="<?= url('admin/receivables') ?>" class="nav-link <?= ($currentPage ?? '') === 'receivables' ? 'active' : '' ?>">
            <i class="fa-solid fa-hand-holding-usd"></i>
            <span><?= $currentLang === 'fr' ? 'Comptes clients' : 'Receivables' ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- BUSINESS -->
        <?php if (AdminPermissionHelper::canAccessMenu('business-accounts', $userRole)): ?>
        <div class="nav-section-header <?= $activeSection === 'business' ? 'section-active' : '' ?>" data-section="business">
          <span><?= $currentLang === 'fr' ? 'Entreprises' : 'Business' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="business">
          <a href="<?= url('admin/business-accounts') ?>" class="nav-link <?= ($currentPage ?? '') === 'business-accounts' ? 'active' : '' ?>">
            <i class="fa-solid fa-building"></i>
            <span><?= $t['business_accounts'] ?? 'Business Accounts' ?></span>
          </a>
          <?php if (AdminPermissionHelper::canAccessMenu('distribution', $userRole)): ?>
          <a href="<?= url('admin/distribution') ?>" class="nav-link <?= ($currentPage ?? '') === 'distribution' ? 'active' : '' ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span><?= $t['distribution_requests'] ?? 'Distribution' ?></span>
            <span id="adminDistBadge" class="nav-badge warning<?= $badgeCounts['distribution'] > 0 ? '' : ' hidden' ?>"><?= $badgeCounts['distribution'] ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- OPERATIONS -->
        <?php
        $opsItems = ['orders', 'shipments'];
        $hasOpsAccess = false;
        foreach ($opsItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasOpsAccess = true; break; }
        }
        if ($hasOpsAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'operations' ? 'section-active' : '' ?>" data-section="operations">
          <span><?= $currentLang === 'fr' ? 'Operations' : 'Operations' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="operations">
          <?php if (AdminPermissionHelper::canAccessMenu('orders', $userRole)): ?>
          <a href="<?= url('admin/orders') ?>" class="nav-link <?= ($currentPage ?? '') === 'orders' ? 'active' : '' ?>">
            <i class="fa-solid fa-cart-shopping"></i>
            <span><?= $t['orders'] ?></span>
            <span id="adminOrdersBadge" class="nav-badge<?= $badgeCounts['orders'] > 0 ? '' : ' hidden' ?>"><?= $badgeCounts['orders'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('shipments', $userRole)): ?>
          <a href="<?= url('admin/shipments') ?>" class="nav-link <?= ($currentPage ?? '') === 'shipments' ? 'active' : '' ?>">
            <i class="fa-solid fa-truck-fast"></i>
            <span><?= $t['shipments'] ?? 'Shipments' ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- CRM -->
        <?php
        $crmItems = ['leads', 'support', 'agent-dashboard', 'call-log', 'waitlist'];
        $hasCrmAccess = false;
        foreach ($crmItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasCrmAccess = true; break; }
        }
        if ($hasCrmAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'crm' ? 'section-active' : '' ?>" data-section="crm">
          <span>CRM</span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="crm">
          <?php if (AdminPermissionHelper::canAccessMenu('leads', $userRole)): ?>
          <a href="<?= url('admin/leads') ?>" class="nav-link <?= ($currentPage ?? '') === 'leads' ? 'active' : '' ?>">
            <i class="fa-solid fa-user-plus"></i>
            <span><?= $currentLang === 'fr' ? 'Prospects CRM' : 'Leads CRM' ?></span>
            <span id="adminLeadsBadge" class="nav-badge success<?= $badgeCounts['leads'] > 0 ? '' : ' hidden' ?>"><?= $badgeCounts['leads'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('support', $userRole)): ?>
          <a href="<?= url('admin/support') ?>" class="nav-link <?= ($currentPage ?? '') === 'support' ? 'active' : '' ?>">
            <i class="fa-solid fa-headset"></i>
            <span><?= $currentLang === 'fr' ? 'Support client' : 'Support Inbox' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('waitlist', $userRole)): ?>
          <a href="<?= url('admin/waitlist') ?>" class="nav-link <?= ($currentPage ?? '') === 'waitlist' ? 'active' : '' ?>">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span><?= $currentLang === 'fr' ? 'Liste d\'attente' : 'Waitlist' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('agent-dashboard', $userRole)): ?>
          <a href="<?= url('admin/agent-dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'agent-dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span><?= $currentLang === 'fr' ? 'Mon tableau de bord' : 'Agent Dashboard' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('call-log', $userRole)): ?>
          <a href="<?= url('admin/call-log') ?>" class="nav-link <?= ($currentPage ?? '') === 'call-log' ? 'active' : '' ?>">
            <i class="fa-solid fa-phone-volume"></i>
            <span><?= $currentLang === 'fr' ? 'Journal d\'appels' : 'Call Log' ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- MARKETING -->
        <?php
        $mktgItems = ['sales', 'promo-banners', 'ads', 'affiliates', 'coupons', 'marketing', 'content-creator', 'content-library'];
        $hasMktgAccess = false;
        foreach ($mktgItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasMktgAccess = true; break; }
        }
        if ($hasMktgAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'marketing' ? 'section-active' : '' ?>" data-section="marketing">
          <span><?= $currentLang === 'fr' ? 'Marketing' : 'Marketing' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="marketing">
          <?php if (AdminPermissionHelper::canAccessMenu('sales', $userRole)): ?>
          <a href="<?= url('admin/sales') ?>" class="nav-link <?= ($currentPage ?? '') === 'sales' ? 'active' : '' ?>">
            <i class="fa-solid fa-percent"></i>
            <span><?= $t['sales_management'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('marketing', $userRole)): ?>
          <a href="<?= url('admin/marketing') ?>" class="nav-link <?= ($currentPage ?? '') === 'marketing' ? 'active' : '' ?>">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
            <span><?= $currentLang === 'fr' ? 'Generateur IA' : 'AI Generator' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('content-creator', $userRole)): ?>
          <a href="<?= url('admin/content/create') ?>" class="nav-link <?= ($currentPage ?? '') === 'content-creator' ? 'active' : '' ?>">
            <i class="fa-solid fa-comments"></i>
            <span><?= $currentLang === 'fr' ? 'Createur de contenu' : 'Content Creator' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('content-library', $userRole)): ?>
          <a href="<?= url('admin/content/library') ?>" class="nav-link <?= ($currentPage ?? '') === 'content-library' ? 'active' : '' ?>">
            <i class="fa-solid fa-photo-film"></i>
            <span><?= $currentLang === 'fr' ? 'Bibliotheque de contenu' : 'Content Library' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('newsletter', $userRole)): ?>
          <a href="<?= url('admin/newsletter') ?>" class="nav-link <?= ($currentPage ?? '') === 'newsletter' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope-open-text"></i>
            <span><?= $currentLang === 'fr' ? 'Infolettres' : 'Newsletter' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('promo-banners', $userRole)): ?>
          <a href="<?= url('admin/promo-banners') ?>" class="nav-link <?= ($currentPage ?? '') === 'promo-banners' ? 'active' : '' ?>">
            <i class="fa-solid fa-image"></i>
            <span><?= $t['promo_banners'] ?? 'Promo Banners' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('ads', $userRole)): ?>
          <a href="<?= url('admin/ads') ?>" class="nav-link <?= ($currentPage ?? '') === 'ads' ? 'active' : '' ?>">
            <i class="fa-solid fa-rectangle-ad"></i>
            <span><?= $t['advertisements'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('affiliates', $userRole)): ?>
          <a href="<?= url('admin/affiliates') ?>" class="nav-link <?= ($currentPage ?? '') === 'affiliates' ? 'active' : '' ?>">
            <i class="fa-solid fa-handshake"></i>
            <span><?= $t['affiliates'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('coupons', $userRole)): ?>
          <a href="<?= url('admin/coupons') ?>" class="nav-link <?= ($currentPage ?? '') === 'coupons' ? 'active' : '' ?>">
            <i class="fa-solid fa-ticket"></i>
            <span><?= $t['coupons'] ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ANALYTICS -->
        <?php
        $analyticsItems = ['analytics', 'reports'];
        $hasAnalyticsAccess = false;
        foreach ($analyticsItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasAnalyticsAccess = true; break; }
        }
        if ($hasAnalyticsAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'analytics' ? 'section-active' : '' ?>" data-section="analytics">
          <span><?= $currentLang === 'fr' ? 'Rapports' : 'Analytics' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="analytics">
          <?php if (AdminPermissionHelper::canAccessMenu('analytics', $userRole)): ?>
          <a href="<?= url('admin/visitor-analytics') ?>" class="nav-link <?= ($currentPage ?? '') === 'analytics' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span><?= $currentLang === 'fr' ? 'Analytique visiteurs' : 'Visitor Analytics' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('reports', $userRole)): ?>
          <a href="<?= url('admin/reports') ?>" class="nav-link <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span><?= $t['reports'] ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- CONTENT -->
        <?php
        $contentItems = ['homepage', 'cms', 'content-pages', 'legal', 'sliders', 'emails', 'translations'];
        $hasContentAccess = false;
        foreach ($contentItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) { $hasContentAccess = true; break; }
        }
        if ($hasContentAccess):
        ?>
        <div class="nav-section-header <?= $activeSection === 'content' ? 'section-active' : '' ?>" data-section="content">
          <span><?= $currentLang === 'fr' ? 'Contenu' : 'Content' ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="content">
          <?php if (AdminPermissionHelper::canAccessMenu('homepage', $userRole)): ?>
          <a href="<?= url('admin/homepage') ?>" class="nav-link <?= ($currentPage ?? '') === 'homepage-settings' ? 'active' : '' ?>">
            <i class="fa-solid fa-house-circle-check"></i>
            <span><?= $t['homepage_settings'] ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('cms', $userRole)): ?>
          <a href="<?= url('admin/cms') ?>" class="nav-link <?= ($currentPage ?? '') === 'cms' ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            <span><?= $currentLang === 'fr' ? 'Pages statiques' : 'Static Pages' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('content-pages', $userRole)): ?>
          <a href="<?= url('admin/content-pages') ?>" class="nav-link <?= ($currentPage ?? '') === 'content-pages' ? 'active' : '' ?>">
            <i class="fa-solid fa-newspaper"></i>
            <span><?= $currentLang === 'fr' ? 'Pages de contenu' : 'Content Pages' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('legal', $userRole)): ?>
          <a href="<?= url('admin/legal') ?>" class="nav-link <?= ($currentPage ?? '') === 'legal' ? 'active' : '' ?>">
            <i class="fa-solid fa-scale-balanced"></i>
            <span><?= $currentLang === 'fr' ? 'Documents legaux' : 'Legal Documents' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('sliders', $userRole)): ?>
          <a href="<?= url('admin/sliders') ?>" class="nav-link <?= ($currentPage ?? '') === 'sliders' ? 'active' : '' ?>">
            <i class="fa-solid fa-images"></i>
            <span><?= $currentLang === 'fr' ? 'Diaporamas' : 'Hero Sliders' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('emails', $userRole)): ?>
          <a href="<?= url('admin/emails') ?>" class="nav-link <?= ($currentPage ?? '') === 'emails' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope"></i>
            <span><?= $currentLang === 'fr' ? 'Modeles courriel' : 'Email Templates' ?></span>
          </a>
          <?php endif; ?>
          <?php if (AdminPermissionHelper::canAccessMenu('translations', $userRole)): ?>
          <a href="<?= url('admin/translations') ?>" class="nav-link <?= ($currentPage ?? '') === 'translations' ? 'active' : '' ?>">
            <i class="fa-solid fa-language"></i>
            <span class="notranslate" translate="no">Translations</span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- SYSTEM -->
        <?php if (AdminPermissionHelper::canAccessMenu('settings', $userRole)): ?>
        <div class="nav-section-header <?= $activeSection === 'system' ? 'section-active' : '' ?>" data-section="system">
          <span><?= $t['system'] ?></span>
          <i class="fa-solid fa-chevron-down nav-section-chevron"></i>
        </div>
        <div class="nav-section-items" data-section-items="system">
          <a href="<?= url('admin/settings') ?>" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
            <i class="fa-solid fa-gear"></i>
            <span><?= $t['settings'] ?></span>
          </a>
          <a href="<?= url('admin/settings/payment') ?>" class="nav-link <?= ($currentPage ?? '') === 'payment-settings' ? 'active' : '' ?>">
            <i class="fa-solid fa-credit-card"></i>
            <span><?= $t['payment_settings'] ?></span>
          </a>
          <a href="<?= url('admin/settings/integrations') ?>" class="nav-link <?= ($currentPage ?? '') === 'integrations' ? 'active' : '' ?>">
            <i class="fa-solid fa-plug"></i>
            <span><?= $currentLang === 'fr' ? 'Integrations' : 'Integrations' ?></span>
          </a>
          <?php if (AdminPermissionHelper::canAccessMenu('email-log', $userRole)): ?>
          <a href="<?= url('admin/email-log') ?>" class="nav-link <?= ($currentPage ?? '') === 'email-log' ? 'active' : '' ?>">
            <i class="fa-solid fa-envelope-open-text"></i>
            <span><?= $currentLang === 'fr' ? 'Journal des courriels' : 'Email Log' ?></span>
          </a>
          <?php endif; ?>
        </div>
        <?php endif; ?>

      </nav>
    </aside>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <!-- Main Content -->
    <div class="main-content">
      
      <!-- Top Bar -->
      <header class="topbar">
        <!-- Mobile Menu Button -->
        <button class="topbar-menu-btn" onclick="toggleSidebar()">
          <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Universal Search (Desktop) -->
        <div class="topbar-search" id="uniSearchWrap" style="position:relative;">
          <input
            type="search"
            id="uniSearchInput"
            placeholder="Search people, orders... (name, email, phone)"
            autocomplete="off"
            style="padding-right:36px;"
          >
          <i class="fa-solid fa-magnifying-glass" id="uniSearchIcon"></i>
          <i class="fa-solid fa-spinner fa-spin" id="uniSearchSpinner" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:var(--gray-400);display:none;"></i>
          <!-- Dropdown results -->
          <div id="uniSearchDropdown" style="display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;background:white;border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:var(--shadow-lg);z-index:1000;max-height:420px;overflow-y:auto;"></div>
        </div>

        <!-- Contact Card Slide-in Panel -->
        <div id="contactCardOverlay" style="display:none;position:fixed;inset:0;z-index:9998;background:rgba(0,0,0,0.35);" onclick="closeContactCard()"></div>
        <div id="contactCardPanel" style="display:none;position:fixed;top:0;right:0;bottom:0;width:460px;max-width:100vw;background:white;z-index:9999;box-shadow:-8px 0 32px rgba(0,0,0,0.15);overflow-y:auto;transform:translateX(100%);transition:transform 0.28s cubic-bezier(0.4,0,0.2,1);">
          <div id="contactCardContent" style="padding:0;"></div>
        </div>

        <!-- Right Side -->
        <div class="topbar-actions">
          <!-- Language Switcher -->
          <div class="language-switcher" x-data="{ open: false }">
            <button @click="open = !open" class="topbar-btn" title="<?= $currentLang === 'fr' ? 'Changer la langue' : 'Change Language' ?>">
              <i class="fa-solid fa-globe"></i>
              <span style="font-size: 0.75rem; font-weight: 600;"><?= strtoupper($currentLang) ?></span>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu" style="display: none; right: 0; left: auto; min-width: 150px;">
              <a href="#" @click.prevent="window.switchLanguage('en'); open = false" class="dropdown-link <?= $currentLang === 'en' ? 'active' : '' ?>">
                <span style="margin-right: 8px;">🇺🇸</span> English
              </a>
              <a href="#" @click.prevent="window.switchLanguage('fr'); open = false" class="dropdown-link <?= $currentLang === 'fr' ? 'active' : '' ?>">
                <span style="margin-right: 8px;">🇫🇷</span> Français
              </a>
            </div>
          </div>

          <!-- Notifications -->
          <div class="notification-dropdown" x-data="notificationDropdown()">
            <button @click="toggle()" class="topbar-btn" :class="{ 'has-notifications': unreadCount > 0 }">
              <i class="fa-solid fa-bell"></i>
              <span x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" class="notification-badge" x-cloak></span>
            </button>

            <div x-show="open" x-transition @click.away="open = false" x-cloak style="display: none;" class="dropdown-menu notification-panel">
              <!-- Header -->
              <div class="notification-header">
                <h4><?= $currentLang === 'fr' ? 'Notifications' : 'Notifications' ?></h4>
                <button @click="markAllRead()" x-show="unreadCount > 0" class="mark-all-btn">
                  <i class="fa-solid fa-check-double"></i> <?= $currentLang === 'fr' ? 'Tout lu' : 'Mark all read' ?>
                </button>
              </div>

              <!-- Notification List -->
              <div class="notification-list">
                <template x-if="loading">
                  <div class="notification-loading">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                    <?= $currentLang === 'fr' ? 'Chargement...' : 'Loading...' ?>
                  </div>
                </template>

                <template x-if="!loading && notifications.length === 0">
                  <div class="notification-empty">
                    <i class="fa-regular fa-bell-slash"></i>
                    <p><?= $currentLang === 'fr' ? 'Aucune notification' : 'No notifications' ?></p>
                  </div>
                </template>

                <template x-for="notification in notifications" :key="notification.id">
                  <a :href="notification.link || '#'"
                     @click="markRead(notification.id, $event); if(notification.link) open = false"
                     class="notification-item"
                     :class="{ 'unread': notification.is_read == 0 }">
                    <div class="notification-icon" :class="'icon-' + notification.type">
                      <i :class="'fa-solid fa-' + notification.icon"></i>
                    </div>
                    <div class="notification-content">
                      <div class="notification-title" x-text="notification.title"></div>
                      <div class="notification-message" x-text="notification.message"></div>
                      <div class="notification-time" x-text="formatTime(notification.created_at)"></div>
                    </div>
                  </a>
                </template>
              </div>

              <!-- Footer -->
              <div class="notification-footer">
                <a href="<?= url('admin/notifications') ?>" @click="open = false" class="view-all-link">
                  <i class="fa-solid fa-list"></i>
                  <?= $currentLang === 'fr' ? 'Voir tout' : 'View All' ?>
                </a>
              </div>
            </div>
          </div>

          <!-- User Dropdown -->
          <div class="user-dropdown" x-data="{ open: false }">
            <button @click="open = !open" class="user-btn">
              <div class="user-avatar">
                <?= strtoupper(substr(user()['first_name'] ?? 'A', 0, 1)) ?>
              </div>
              <span class="user-name"><?= htmlspecialchars(user()['first_name'] ?? 'Admin') ?></span>
              <i class="fa-solid fa-chevron-down"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak style="display: none;" class="dropdown-menu">
              <a href="<?= url('admin/profile') ?>" class="dropdown-link">
                <i class="fa-solid fa-user-circle"></i> <?= $t['profile'] ?>
              </a>
              <a href="<?= url('admin/settings') ?>" class="dropdown-link">
                <i class="fa-solid fa-gear"></i> <?= $t['settings'] ?>
              </a>
              <a href="<?= url('admin/planner') ?>" class="dropdown-link">
                <i class="fa-solid fa-clipboard-list"></i> <?= $t['planner'] ?>
              </a>
              <div class="dropdown-divider"></div>
              <form method="POST" action="<?= url('logout') ?>" style="margin:0;padding:0;">
                <?= csrfField() ?>
                <button type="submit" class="dropdown-link danger" style="width:100%;background:none;border:none;cursor:pointer;text-align:left;">
                  <i class="fa-solid fa-right-from-bracket"></i> <?= $t['logout'] ?>
                </button>
              </form>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="page-content"<?php if (($currentPage ?? '') === 'planner'): ?> style="padding: 0;"<?php endif; ?>>
        <?php if (hasFlash('success')): ?>
          <div class="alert alert-success" role="alert" data-auto-dismiss style="transition:opacity 0.6s ease;">
            <?= getFlash('success') ?>
          </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
          <div class="alert alert-error" role="alert">
            <?= htmlspecialchars(getFlash('error')) ?>
          </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
      </main>

    </div>
  </div>

  <!-- Alpine.js for dropdowns -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
  <script>
    // Global CSRF token for all fetch requests
    const _csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const _originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
      options = options || {};
      options.headers = options.headers || {};
      // Add CSRF token header for non-GET requests
      if (options.method && options.method !== 'GET') {
        if (options.headers instanceof Headers) {
          if (!options.headers.has('X-CSRF-TOKEN')) {
            options.headers.set('X-CSRF-TOKEN', _csrfToken);
          }
        } else {
          options.headers['X-CSRF-TOKEN'] = options.headers['X-CSRF-TOKEN'] || _csrfToken;
        }
      }
      return _originalFetch.call(this, url, options);
    };

    // Sidebar toggle for mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');

      sidebar.classList.toggle('show-mobile');
      overlay.classList.toggle('show');
    }

    // Collapsible sidebar sections
    (function () {
      const STORAGE_KEY = 'ocs_nav_state';
      const activeSection = '<?= $activeSection ?>';

      function initNav() {
        let saved = {};
        try { saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}'); } catch(e) {}

        document.querySelectorAll('[data-section-items]').forEach(function(items) {
          const section = items.dataset.sectionItems;
          const header = document.querySelector('[data-section="' + section + '"]');
          if (!header) return;

          // Default: active section open, everything else collapsed
          const shouldCollapse = (section in saved) ? saved[section] : (section !== activeSection);

          if (shouldCollapse) {
            items.classList.add('collapsed');
            header.classList.add('collapsed');
          }

          header.addEventListener('click', function() {
            const isNowCollapsed = items.classList.toggle('collapsed');
            header.classList.toggle('collapsed', isNowCollapsed);
            saved[section] = isNowCollapsed;
            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(saved)); } catch(e) {}
          });
        });
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNav);
      } else {
        initNav();
      }
    })();

    // Auto-scroll sidebar to show active menu item
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const activeLink = sidebar ? sidebar.querySelector('.nav-link.active') : null;
      if (activeLink) {
        const sidebarH = sidebar.clientHeight;
        const linkTop  = activeLink.offsetTop;
        const linkH    = activeLink.offsetHeight;
        sidebar.scrollTop = linkTop - (sidebarH / 2) + (linkH / 2);
      }
    });

    // Language switcher function
    function switchLanguage(lang) {
      // Make AJAX request to change language
      fetch('<?= url('set-language') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
          'language': lang,
          '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= generateCsrfToken() ?>'
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Reload page to apply language change across entire admin panel
          window.location.reload();
        } else {
          console.error('Failed to change language:', data.message);
          alert('Failed to change language. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error changing language:', error);
        alert('An error occurred while changing language.');
      });
    }

    // Notification dropdown component
    function notificationDropdown() {
      return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,
        pollInterval: null,

        init() {
          this.fetchCount();
          this.startStream();
        },

        startStream() {
          const self = this;
          if (typeof EventSource === 'undefined') {
            // Fallback: poll every 5s if SSE not supported
            this.pollInterval = setInterval(() => self.fetchCount(), 5000);
            return;
          }

          function connect() {
            const es = new EventSource('<?= url('api/admin/notifications/stream') ?>');

            es.onmessage = function(e) {
              try {
                const d = JSON.parse(e.data);
                if (typeof d.unread_count !== 'undefined') {
                  self.unreadCount = d.unread_count;
                  // Refresh all sidebar badges on any SSE event
                  if (typeof window._refreshAllAdminBadges === 'function') {
                    window._refreshAllAdminBadges();
                  }
                }
              } catch(err) {}
            };

            es.addEventListener('reconnect', function() {
              es.close();
              connect(); // server asked us to reconnect
            });

            es.onerror = function() {
              es.close();
              // Retry after 5s on error
              setTimeout(connect, 5000);
            };
          }

          connect();
        },

        toggle() {
          this.open = !this.open;
          if (this.open) {
            this.fetchNotifications();
          }
        },

        async fetchNotifications() {
          this.loading = true;
          try {
            const response = await fetch('<?= url('api/admin/notifications') ?>?limit=5');
            const data = await response.json();
            if (data.success) {
              this.notifications = data.notifications;
              this.unreadCount = data.unread_count;
            }
          } catch (error) {
            console.error('Failed to fetch notifications:', error);
          }
          this.loading = false;
        },

        async fetchCount() {
          try {
            const response = await fetch('<?= url('api/admin/notifications/count') ?>');
            const data = await response.json();
            if (data.success) {
              this.unreadCount = data.unread_count;
            }
          } catch (error) {
            console.error('Failed to fetch notification count:', error);
          }
        },

        async markRead(id, event) {
          const notification = this.notifications.find(n => n.id == id);
          if (notification && notification.is_read == 1) {
            return; // Already read, just navigate
          }

          try {
            const response = await fetch('<?= url('api/admin/notifications/mark-read') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({ id: id })
            });
            const data = await response.json();
            if (data.success) {
              this.unreadCount = data.unread_count;
              const index = this.notifications.findIndex(n => n.id == id);
              if (index !== -1) {
                this.notifications[index].is_read = 1;
              }
            }
          } catch (error) {
            console.error('Failed to mark notification as read:', error);
          }
        },

        async markAllRead() {
          try {
            const response = await fetch('<?= url('api/admin/notifications/mark-all-read') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              }
            });
            const data = await response.json();
            if (data.success) {
              this.unreadCount = 0;
              this.notifications.forEach(n => n.is_read = 1);
            }
          } catch (error) {
            console.error('Failed to mark all as read:', error);
          }
        },

        formatTime(dateString) {
          const date = new Date(dateString);
          const now = new Date();
          const diff = Math.floor((now - date) / 1000);

          if (diff < 60) return <?= json_encode($currentLang === 'fr' ? 'À l\'instant' : 'Just now') ?>;
          if (diff < 3600) return Math.floor(diff / 60) + <?= json_encode($currentLang === 'fr' ? ' min' : 'm ago') ?>;
          if (diff < 86400) return Math.floor(diff / 3600) + <?= json_encode($currentLang === 'fr' ? 'h' : 'h ago') ?>;
          if (diff < 604800) return Math.floor(diff / 86400) + <?= json_encode($currentLang === 'fr' ? 'j' : 'd ago') ?>;

          return date.toLocaleDateString(<?= json_encode($currentLang === 'fr' ? 'fr-FR' : 'en-US') ?>, {
            month: 'short',
            day: 'numeric'
          });
        }
      };
    }
  </script>

  <script>
  // ── Admin: unified sidebar badge refresh + sound + browser notifications ──────
  (function() {
    // Shared AudioContext — unlocked on first user gesture
    var _adminAudioCtx = null;
    var _adminAudioUnlocked = false;
    function _unlockAdminAudio() {
      if (_adminAudioUnlocked) return;
      try {
        _adminAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
        var buf = _adminAudioCtx.createBuffer(1, 1, 22050);
        var src = _adminAudioCtx.createBufferSource();
        src.buffer = buf; src.connect(_adminAudioCtx.destination); src.start(0);
        _adminAudioUnlocked = true;
      } catch(e) {}
    }
    ['click','touchstart','keydown'].forEach(function(evt) {
      document.addEventListener(evt, _unlockAdminAudio, { once: true, passive: true });
    });

    function playAdminChime() {
      if (localStorage.getItem('admin_sound_enabled') === 'off') return;
      try {
        if (!_adminAudioCtx) _adminAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
        var ctx = _adminAudioCtx;
        function _doAdminChime() {
          [[880, 0], [1047, 0.15], [1319, 0.30]].forEach(function(note) {
            var osc = ctx.createOscillator(), gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.type = 'sine'; osc.frequency.value = note[0];
            gain.gain.setValueAtTime(0, ctx.currentTime + note[1]);
            gain.gain.linearRampToValueAtTime(0.12, ctx.currentTime + note[1] + 0.04);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + note[1] + 0.5);
            osc.start(ctx.currentTime + note[1]); osc.stop(ctx.currentTime + note[1] + 0.55);
          });
        }
        if (ctx.state === 'suspended') {
          ctx.resume().then(_doAdminChime).catch(function() {});
        } else {
          _doAdminChime();
        }
      } catch(e) {}
    }

    // Browser Notifications API
    var _adminBrowserNotifPerm = (typeof Notification !== 'undefined') ? Notification.permission : 'denied';
    document.addEventListener('click', function() {
      if (_adminBrowserNotifPerm === 'default' && typeof Notification !== 'undefined') {
        Notification.requestPermission().then(function(r) { _adminBrowserNotifPerm = r; });
      }
    }, { once: true, passive: true });

    function _showAdminBrowserNotif(title, body, link) {
      if (_adminBrowserNotifPerm !== 'granted') return;
      try {
        var n = new Notification('OCSAPP Admin — ' + title, {
          body: body, icon: '<?= url("assets/images/logo.png") ?>',
          tag: 'ocsapp-admin', requireInteraction: false
        });
        if (link) {
          n.onclick = function() { window.focus(); window.location.href = '<?= url("") ?>' + link.replace(/^\//, ''); n.close(); };
        }
        setTimeout(function() { try { n.close(); } catch(e) {} }, 8000);
      } catch(e) {}
    }

    function _setBadge(id, count) {
      var el = document.getElementById(id);
      if (!el) return;
      if (count > 0) {
        el.textContent = count > 99 ? '99+' : count;
        el.classList.remove('hidden');
      } else {
        el.classList.add('hidden');
      }
    }

    var _prevAdminNotifCount = -1;

    window._refreshAllAdminBadges = function() {
      fetch('<?= url('api/admin/notifications/count') ?>', { credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          if (!data || !data.success) return;

          var n = data.unread_count || 0;

          // Bell (Alpine.js component handles this — just sync the count)
          // Trigger Alpine update if component is present
          var bellEl = document.querySelector('[x-data]');
          if (bellEl && bellEl.__x) {
            try { bellEl.__x.getUnobservedData().unreadCount = n; } catch(e) {}
          }

          // Sidebar badges
          _setBadge('adminOrdersBadge',  data.orders_count  || 0);
          _setBadge('adminDistBadge',    data.dist_count    || 0);
          _setBadge('adminSellersBadge', data.sellers_count || 0);
          _setBadge('adminLeadsBadge',   data.leads_count   || 0);

          // Sound + browser notification on new bell notification
          if (_prevAdminNotifCount >= 0 && n > _prevAdminNotifCount) {
            playAdminChime();
            fetch('<?= url('api/admin/notifications') ?>?limit=1')
              .then(function(r) { return r.json(); })
              .then(function(d) {
                if (d.success && d.notifications && d.notifications.length) {
                  var notif = d.notifications[0];
                  if (!notif.is_read) _showAdminBrowserNotif(notif.title, notif.message, notif.link);
                }
              }).catch(function() {});
          }
          _prevAdminNotifCount = n;
        })
        .catch(function() {});
    };

    // Also update sidebar badges when SSE fires via Alpine's fetchCount
    // Patch Alpine component's fetchCount to also call our badge refresh
    document.addEventListener('alpine:initialized', function() {
      setTimeout(function() {
        var origFetch = window._refreshAllAdminBadges;
        // Sidebar: start polling
        setTimeout(window._refreshAllAdminBadges, 2000);
        setInterval(window._refreshAllAdminBadges, 10000);
      }, 500);
    });

    // Fallback: start if Alpine event never fires
    setTimeout(function() {
      if (!window._adminBadgePolling) {
        window._adminBadgePolling = true;
        setTimeout(window._refreshAllAdminBadges, 2000);
        setInterval(window._refreshAllAdminBadges, 10000);
      }
    }, 1500);
  })();
  </script>

  <?= $scripts ?? '' ?>

  <!-- Universal Search & Contact Card -->
  <script>
  (function() {
    const input    = document.getElementById('uniSearchInput');
    const dropdown = document.getElementById('uniSearchDropdown');
    const spinner  = document.getElementById('uniSearchSpinner');
    const icon     = document.getElementById('uniSearchIcon');
    let debounceTimer = null;
    let activeIndex   = -1;

    const TYPE_META = {
      buyer:    { label: 'Buyer',    color: '#3b82f6', icon: 'fa-user' },
      seller:   { label: 'Seller',   color: '#8b5cf6', icon: 'fa-store' },
      driver:   { label: 'Driver',   color: '#f59e0b', icon: 'fa-truck' },
      supplier: { label: 'Supplier', color: '#10b981', icon: 'fa-boxes-stacked' },
      lead:     { label: 'Lead',     color: '#6b7280', icon: 'fa-user-tag' },
      order:    { label: 'Order',    color: '#ef4444', icon: 'fa-receipt' },
    };

    function showSpinner(show) {
      spinner.style.display = show ? 'block' : 'none';
      icon.style.display    = show ? 'none'  : 'block';
    }

    function hideDropdown() {
      dropdown.style.display = 'none';
      dropdown.innerHTML = '';
      activeIndex = -1;
    }

    function renderDropdown(results) {
      if (!results.length) {
        dropdown.innerHTML = '<div style="padding:16px;text-align:center;color:#9ca3af;font-size:13px;"><i class="fa-solid fa-magnifying-glass" style="margin-right:6px;"></i>No results found</div>';
        dropdown.style.display = 'block';
        return;
      }

      // Group by type
      const groups = {};
      results.forEach(r => {
        if (!groups[r.type]) groups[r.type] = [];
        groups[r.type].push(r);
      });

      let html = '';
      let itemIndex = 0;
      Object.entries(groups).forEach(([type, items]) => {
        const meta = TYPE_META[type] || { label: type, color: '#6b7280', icon: 'fa-circle' };
        html += `<div style="padding:6px 12px 2px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #f3f4f6;">${meta.label}s</div>`;
        items.forEach(r => {
          const badge = r.badge ? `<span style="font-size:10px;background:#f3f4f6;color:#6b7280;padding:1px 7px;border-radius:8px;margin-left:6px;">${r.badge}</span>` : '';
          html += `<div class="uni-result-item" data-index="${itemIndex}" data-type="${r.type}" data-id="${r.id}" data-url="${r.url}"
            style="display:flex;align-items:center;gap:12px;padding:10px 14px;cursor:pointer;border-bottom:1px solid #f9fafb;transition:background 0.1s;"
            onmouseenter="this.style.background='#f0fdf4'" onmouseleave="this.style.background=''"
            onclick="handleSearchResult('${r.type}', ${r.id}, '${r.url}')">
            <div style="width:34px;height:34px;border-radius:50%;background:${meta.color}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <i class="fa-solid ${meta.icon}" style="color:${meta.color};font-size:13px;"></i>
            </div>
            <div style="flex:1;min-width:0;">
              <div style="font-size:13px;font-weight:600;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${r.name}${badge}</div>
              <div style="font-size:12px;color:#6b7280;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${r.sub || ''}${r.phone ? ' · ' + r.phone : ''}</div>
            </div>
            ${r.status ? `<span style="font-size:10px;padding:2px 8px;border-radius:8px;background:#f3f4f6;color:#6b7280;flex-shrink:0;">${r.status}</span>` : ''}
          </div>`;
          itemIndex++;
        });
      });

      dropdown.innerHTML = html;
      dropdown.style.display = 'block';
    }

    function doSearch(q) {
      if (q.length < 2) { hideDropdown(); return; }
      showSpinner(true);
      fetch('/admin/api/universal-search?q=' + encodeURIComponent(q), {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
      })
      .then(r => r.json())
      .then(data => {
        showSpinner(false);
        renderDropdown(data.results || []);
      })
      .catch(() => { showSpinner(false); });
    }

    input.addEventListener('input', function() {
      clearTimeout(debounceTimer);
      const q = this.value.trim();
      if (q.length < 2) { hideDropdown(); return; }
      debounceTimer = setTimeout(() => doSearch(q), 280);
    });

    // Keyboard navigation
    input.addEventListener('keydown', function(e) {
      const items = dropdown.querySelectorAll('.uni-result-item');
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, items.length - 1);
        items.forEach((el, i) => el.style.background = i === activeIndex ? '#f0fdf4' : '');
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, -1);
        items.forEach((el, i) => el.style.background = i === activeIndex ? '#f0fdf4' : '');
      } else if (e.key === 'Enter' && activeIndex >= 0) {
        const el = items[activeIndex];
        if (el) handleSearchResult(el.dataset.type, parseInt(el.dataset.id), el.dataset.url);
      } else if (e.key === 'Escape') {
        hideDropdown();
        input.blur();
      }
    });

    document.addEventListener('click', function(e) {
      if (!document.getElementById('uniSearchWrap').contains(e.target)) hideDropdown();
    });

    // ---- Contact Card ----
    window.handleSearchResult = function(type, id, url) {
      hideDropdown();
      input.value = '';
      if (type === 'order') { window.location.href = url; return; }
      openContactCard(type, id, url);
    };

    window.openContactCard = function(type, id, url) {
      const panel   = document.getElementById('contactCardPanel');
      const overlay = document.getElementById('contactCardOverlay');
      const content = document.getElementById('contactCardContent');

      content.innerHTML = '<div style="padding:40px;text-align:center;color:#9ca3af;"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>';
      panel.style.display   = 'block';
      overlay.style.display = 'block';
      document.body.style.overflow = 'hidden';
      requestAnimationFrame(() => { panel.style.transform = 'translateX(0)'; });

      fetch('/admin/api/contact-card?type=' + type + '&id=' + id, {
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' }
      })
      .then(r => r.json())
      .then(data => { if (data.card) renderContactCard(data.card, url); })
      .catch(() => {
        content.innerHTML = '<div style="padding:40px;text-align:center;color:#ef4444;">Failed to load contact.</div>';
      });
    };

    window.closeContactCard = function() {
      const panel   = document.getElementById('contactCardPanel');
      const overlay = document.getElementById('contactCardOverlay');
      panel.style.transform = 'translateX(100%)';
      setTimeout(() => {
        panel.style.display   = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = '';
      }, 280);
    };

    function renderContactCard(c, url) {
      const meta = TYPE_META[c.type] || { label: c.type, color: '#6b7280', icon: 'fa-user' };
      const initials = (c.name || '?').split(' ').slice(0,2).map(w => w[0]).join('').toUpperCase();

      const statusColor = { active:'#10b981', inactive:'#9ca3af', suspended:'#ef4444', pending:'#f59e0b', rejected:'#ef4444' };
      const sColor = statusColor[c.status] || '#9ca3af';

      let ordersHtml = '';
      if (c.orders && c.orders.length) {
        ordersHtml = `<div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;margin-bottom:10px;">Recent Orders</div>
          ${c.orders.map(o => `
            <a href="${o.url}" style="display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid #f9fafb;text-decoration:none;color:inherit;">
              <span style="font-size:13px;font-weight:600;color:#374151;">#${o.id}</span>
              <span style="font-size:12px;color:#6b7280;">${o.total}</span>
              <span style="font-size:11px;padding:2px 8px;background:#f3f4f6;border-radius:8px;color:#6b7280;">${o.status}</span>
              <span style="font-size:11px;color:#9ca3af;">${o.date}</span>
            </a>`).join('')}
        </div>`;
      }

      let statsHtml = '';
      if (c.stats) {
        statsHtml = `<div style="padding:14px 20px;border-top:1px solid #f3f4f6;display:flex;gap:20px;">
          <div style="text-align:center;"><div style="font-size:18px;font-weight:700;color:#111827;">${c.stats.orders}</div><div style="font-size:11px;color:#9ca3af;">Orders</div></div>
          <div style="text-align:center;"><div style="font-size:18px;font-weight:700;color:#111827;">${c.stats.gmv}</div><div style="font-size:11px;color:#9ca3af;">GMV</div></div>
        </div>`;
      }

      let actHtml = '';
      if (c.activities && c.activities.length) {
        const typeIcon = { call:'fa-phone', email:'fa-envelope', meeting:'fa-calendar', note:'fa-note-sticky', follow_up:'fa-clock', status_change:'fa-arrows-rotate' };
        actHtml = `<div style="padding:16px 20px;border-top:1px solid #f3f4f6;">
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#9ca3af;margin-bottom:10px;">Last Interactions</div>
          ${c.activities.map(a => `
            <div style="display:flex;gap:10px;padding:6px 0;border-bottom:1px solid #f9fafb;">
              <i class="fa-solid ${typeIcon[a.type]||'fa-circle-dot'}" style="color:#9ca3af;font-size:12px;margin-top:3px;flex-shrink:0;width:14px;text-align:center;"></i>
              <div style="flex:1;min-width:0;">
                <div style="font-size:12px;color:#374151;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${a.description || ''}</div>
                <div style="font-size:11px;color:#9ca3af;">${a.date}${a.agent ? ' · ' + a.agent : ''}</div>
              </div>
            </div>`).join('')}
        </div>`;
      }

      const phoneHtml = c.phone
        ? `<a href="tel:${c.phone}" style="font-size:13px;color:#374151;text-decoration:none;">${c.phone}</a>`
        : `<span style="font-size:13px;color:#d1d5db;">No phone</span>`;

      document.getElementById('contactCardContent').innerHTML = `
        <!-- Header -->
        <div style="background:linear-gradient(135deg,#111827,#1f2937);padding:20px;display:flex;align-items:center;gap:14px;">
          <div style="width:52px;height:52px;border-radius:50%;background:${meta.color};display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:white;flex-shrink:0;">${initials}</div>
          <div style="flex:1;min-width:0;">
            <div style="font-size:17px;font-weight:700;color:white;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${c.name}</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
              <span style="font-size:11px;background:${meta.color}30;color:${meta.color};padding:2px 10px;border-radius:10px;font-weight:600;text-transform:uppercase;">${meta.label}</span>
              <span style="font-size:11px;background:${sColor}22;color:${sColor};padding:2px 10px;border-radius:10px;font-weight:600;">${c.status || ''}</span>
              ${c.badge ? `<span style="font-size:11px;background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.7);padding:2px 10px;border-radius:10px;">${c.badge}</span>` : ''}
            </div>
          </div>
          <button onclick="closeContactCard()" style="background:rgba(255,255,255,0.1);border:none;color:white;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;" title="Close">&times;</button>
        </div>

        <!-- Contact info -->
        <div style="padding:16px 20px;display:flex;flex-direction:column;gap:8px;border-bottom:1px solid #f3f4f6;">
          <div style="display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-envelope" style="color:#9ca3af;width:16px;text-align:center;"></i>
            ${c.email ? `<a href="mailto:${c.email}" style="font-size:13px;color:#374151;text-decoration:none;">${c.email}</a>` : '<span style="font-size:13px;color:#d1d5db;">No email</span>'}
          </div>
          <div style="display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-phone" style="color:#9ca3af;width:16px;text-align:center;"></i>
            ${phoneHtml}
          </div>
          ${c.company ? `<div style="display:flex;align-items:center;gap:10px;"><i class="fa-solid fa-building" style="color:#9ca3af;width:16px;text-align:center;"></i><span style="font-size:13px;color:#374151;">${c.company}</span></div>` : ''}
          <div style="display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-calendar" style="color:#9ca3af;width:16px;text-align:center;"></i>
            <span style="font-size:12px;color:#9ca3af;">Member since ${c.member_since || '—'}</span>
          </div>
        </div>

        <!-- Quick Actions -->
        <div style="padding:14px 20px;display:flex;gap:8px;flex-wrap:wrap;border-bottom:1px solid #f3f4f6;">
          ${c.phone ? `<a href="tel:${c.phone}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f0fdf4;color:#00b207;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #bbf7d0;"><i class="fa-solid fa-phone"></i> Call</a>` : ''}
          ${c.email ? `<a href="mailto:${c.email}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#eff6ff;color:#3b82f6;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #bfdbfe;"><i class="fa-solid fa-envelope"></i> Email</a>` : ''}
          <button onclick="closeContactCard();openDispositionModal(${JSON.stringify(c.name)},${JSON.stringify(c.phone||'')},${JSON.stringify(c.type)},${c.id||0},${JSON.stringify(c.email||'')})" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#fff7ed;color:#ea580c;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;border:1px solid #fed7aa;"><i class="fa-solid fa-phone-volume"></i> Log Call</button>
          <a href="${url}" style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f9fafb;color:#374151;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;border:1px solid #e5e7eb;"><i class="fa-solid fa-arrow-up-right-from-square"></i> Full Profile</a>
        </div>

        ${statsHtml}
        ${ordersHtml}
        ${actHtml}
      `;
    }
  })();

  // =========================================================================
  // Quick Disposition Modal
  // =========================================================================
  (function() {
    const OUTCOMES = {
      resolved:           { label:'Resolved',           icon:'fa-check-circle',           color:'#10b981' },
      follow_up:          { label:'Follow-up needed',   icon:'fa-rotate-right',            color:'#3b82f6' },
      no_answer:          { label:'No answer',          icon:'fa-phone-slash',             color:'#9ca3af' },
      voicemail:          { label:'Left voicemail',     icon:'fa-voicemail',               color:'#8b5cf6' },
      wrong_number:       { label:'Wrong number',       icon:'fa-xmark',                   color:'#ef4444' },
      transferred:        { label:'Transferred',        icon:'fa-arrow-right-arrow-left',  color:'#f59e0b' },
      callback_scheduled: { label:'Callback scheduled', icon:'fa-calendar-check',          color:'#06b6d4' },
      other:              { label:'Other',              icon:'fa-circle-dot',              color:'#6b7280' },
    };

    window.openDispositionModal = function(name='', phone='', type='unknown', contactId=0, email='') {
      const f = document.getElementById('dispositionForm');
      if (!f) return;
      f.reset();
      document.getElementById('dm_contact_name').value  = name;
      document.getElementById('dm_contact_phone').value = phone;
      document.getElementById('dm_contact_email').value = email;
      const sel = document.getElementById('dm_contact_type');
      if (sel) sel.value = type;
      document.getElementById('dm_contact_id').value = contactId;
      document.getElementById('dmTicketFields').style.display   = 'none';
      document.getElementById('dmCallbackFields').style.display = 'none';
      document.getElementById('dmSuccessBanner').style.display  = 'none';
      document.getElementById('dmSubmitBtn').disabled = false;
      document.getElementById('dmSubmitBtn').textContent = 'Log Call';

      const modal   = document.getElementById('dispositionModal');
      const overlay = document.getElementById('dispositionOverlay');
      modal.style.display   = 'flex';
      overlay.style.display = 'block';
      document.body.style.overflow = 'hidden';
      requestAnimationFrame(() => { modal.querySelector('.dm-inner').style.transform = 'scale(1)'; });
      setTimeout(() => { document.getElementById('dm_contact_name').focus(); }, 120);
    };

    window.closeDispositionModal = function() {
      const modal   = document.getElementById('dispositionModal');
      const overlay = document.getElementById('dispositionOverlay');
      const inner   = modal.querySelector('.dm-inner');
      inner.style.transform = 'scale(.95)';
      setTimeout(() => {
        modal.style.display   = 'none';
        overlay.style.display = 'none';
        document.body.style.overflow = '';
      }, 160);
    };

    // Toggle ticket fields
    window.dmToggleTicket = function(cb) {
      document.getElementById('dmTicketFields').style.display = cb.checked ? 'block' : 'none';
    };

    // Toggle callback fields
    window.dmToggleCallback = function(cb) {
      document.getElementById('dmCallbackFields').style.display = cb.checked ? 'block' : 'none';
    };

    // Submit
    window.dmSubmit = function(e) {
      e.preventDefault();
      const form = document.getElementById('dispositionForm');
      const btn  = document.getElementById('dmSubmitBtn');
      btn.disabled = true;
      btn.textContent = 'Saving…';

      const fd = new FormData(form);
      fetch('/admin/call-log/store', {
        method: 'POST',
        body:   fd,
      })
      .then(r => r.json())
      .then(d => {
        if (d.success) {
          const banner  = document.getElementById('dmSuccessBanner');
          const oc      = OUTCOMES[d.outcome] || OUTCOMES.other;
          banner.innerHTML = `<i class="fa-solid ${oc.icon}" style="color:${oc.color};"></i> Call logged — <strong>${oc.label}</strong>${d.ticket_id ? ` · <a href="/admin/support/view?id=${d.ticket_id}" style="color:#3b82f6;font-weight:600;">View ticket →</a>` : ''}`;
          banner.style.display = 'flex';
          btn.textContent = '✓ Logged';
          setTimeout(() => closeDispositionModal(), 1800);
        } else {
          btn.disabled = false;
          btn.textContent = 'Log Call';
          alert('Failed to save. Please try again.');
        }
      })
      .catch(() => {
        btn.disabled = false;
        btn.textContent = 'Log Call';
        alert('Network error. Please try again.');
      });
    };
  })();
  </script>

<!-- ======= Quick Disposition Modal ======= -->
<div id="dispositionOverlay" onclick="closeDispositionModal()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9010;"></div>

<div id="dispositionModal" style="display:none;position:fixed;inset:0;z-index:9011;align-items:center;justify-content:center;padding:20px;">
  <div class="dm-inner" style="background:white;border-radius:16px;width:100%;max-width:520px;box-shadow:0 20px 60px rgba(0,0,0,.18);transform:scale(.95);transition:transform .16s;overflow:hidden;">

    <!-- Modal header -->
    <div style="background:linear-gradient(135deg,#111827,#1f2937);padding:18px 22px;display:flex;align-items:center;gap:12px;">
      <div style="width:36px;height:36px;background:#00b207;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <i class="fa-solid fa-phone-volume" style="color:white;font-size:15px;"></i>
      </div>
      <div>
        <div style="font-size:16px;font-weight:700;color:white;">Log a Call</div>
        <div style="font-size:11px;color:rgba(255,255,255,.5);">Quick disposition — takes 10 seconds</div>
      </div>
      <button onclick="closeDispositionModal()" style="margin-left:auto;background:rgba(255,255,255,.1);border:none;color:white;width:30px;height:30px;border-radius:50%;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;">&times;</button>
    </div>

    <form id="dispositionForm" onsubmit="dmSubmit(event)" style="padding:20px;display:flex;flex-direction:column;gap:14px;">
      <?= csrfField() ?>
      <input type="hidden" name="contact_id" id="dm_contact_id" value="0">

      <!-- Direction + Contact type row -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Direction</label>
          <div style="display:flex;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
            <label style="flex:1;text-align:center;cursor:pointer;">
              <input type="radio" name="direction" value="outbound" checked style="display:none;">
              <span class="dm-dir-opt" data-val="outbound" style="display:block;padding:8px 6px;font-size:12px;font-weight:600;color:#374151;background:#f0fdf4;border-right:1px solid #e5e7eb;transition:all .1s;">
                <i class="fa-solid fa-phone-arrow-up-right" style="color:#00b207;"></i> Out
              </span>
            </label>
            <label style="flex:1;text-align:center;cursor:pointer;">
              <input type="radio" name="direction" value="inbound" style="display:none;">
              <span class="dm-dir-opt" data-val="inbound" style="display:block;padding:8px 6px;font-size:12px;font-weight:600;color:#6b7280;background:white;transition:all .1s;">
                <i class="fa-solid fa-phone-arrow-down-left" style="color:#3b82f6;"></i> In
              </span>
            </label>
          </div>
        </div>
        <div>
          <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Contact Type</label>
          <select name="contact_type" id="dm_contact_type" style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;background:white;">
            <option value="unknown">Unknown</option>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
            <option value="driver">Driver</option>
            <option value="supplier">Supplier</option>
            <option value="lead">Lead</option>
          </select>
        </div>
      </div>

      <!-- Contact name + phone -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Contact Name</label>
          <input type="text" name="contact_name" id="dm_contact_name" placeholder="Full name"
            style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
        </div>
        <div>
          <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Phone</label>
          <input type="tel" name="contact_phone" id="dm_contact_phone" placeholder="514-555-0100"
            style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;">
        </div>
      </div>
      <input type="hidden" name="contact_email" id="dm_contact_email" value="">

      <!-- Outcome -->
      <div>
        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Outcome <span style="color:#ef4444;">*</span></label>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:6px;" id="dmOutcomeGrid">
          <?php
          $outcomeOpts = [
            ['resolved',           'fa-check-circle',          '#10b981', '#d1fae5', 'Resolved'],
            ['follow_up',          'fa-rotate-right',           '#3b82f6', '#dbeafe', 'Follow-up'],
            ['no_answer',          'fa-phone-slash',            '#9ca3af', '#f3f4f6', 'No answer'],
            ['voicemail',          'fa-voicemail',              '#8b5cf6', '#ede9fe', 'Voicemail'],
            ['wrong_number',       'fa-xmark',                  '#ef4444', '#fee2e2', 'Wrong #'],
            ['transferred',        'fa-arrow-right-arrow-left', '#f59e0b', '#fef3c7', 'Transferred'],
            ['callback_scheduled', 'fa-calendar-check',         '#06b6d4', '#cffafe', 'Callback'],
            ['other',              'fa-circle-dot',             '#6b7280', '#f3f4f6', 'Other'],
          ];
          foreach ($outcomeOpts as [$val, $icon, $color, $bg, $lbl]):
          ?>
          <label style="cursor:pointer;">
            <input type="radio" name="outcome" value="<?= $val ?>" style="display:none;" required>
            <div class="dm-outcome-btn" data-val="<?= $val ?>" data-color="<?= $color ?>" data-bg="<?= $bg ?>"
              style="padding:8px 4px;border:2px solid #e5e7eb;border-radius:8px;text-align:center;font-size:10px;font-weight:700;color:#6b7280;background:white;transition:all .1s;line-height:1.3;">
              <i class="fa-solid <?= $icon ?>" style="display:block;font-size:14px;margin-bottom:3px;color:<?= $color ?>;"></i>
              <?= $lbl ?>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Notes -->
      <div>
        <label style="font-size:11px;font-weight:600;color:#6b7280;display:block;margin-bottom:5px;">Quick Note</label>
        <textarea name="notes" rows="2" placeholder="Brief summary of the call…"
          style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;resize:none;font-family:inherit;"></textarea>
      </div>

      <!-- Checkboxes -->
      <div style="display:flex;gap:20px;flex-wrap:wrap;">
        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;">
          <input type="checkbox" name="create_ticket" id="dm_create_ticket" onchange="dmToggleTicket(this)" style="width:15px;height:15px;accent-color:#00b207;">
          Create support ticket
        </label>
        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;">
          <input type="checkbox" name="schedule_callback" id="dm_schedule_callback" onchange="dmToggleCallback(this)" style="width:15px;height:15px;accent-color:#06b6d4;">
          Schedule callback
        </label>
      </div>

      <!-- Ticket subject (hidden by default) -->
      <div id="dmTicketFields" style="display:none;background:#f0fdf4;border-radius:8px;padding:12px;">
        <label style="font-size:11px;font-weight:600;color:#166534;display:block;margin-bottom:5px;">Ticket Subject</label>
        <input type="text" name="ticket_subject" placeholder="What is the issue about?"
          style="width:100%;padding:8px 10px;border:1px solid #bbf7d0;border-radius:8px;font-size:13px;">
      </div>

      <!-- Callback datetime (hidden by default) -->
      <div id="dmCallbackFields" style="display:none;background:#ecfeff;border-radius:8px;padding:12px;">
        <label style="font-size:11px;font-weight:600;color:#0e7490;display:block;margin-bottom:5px;">Callback Date & Time</label>
        <input type="datetime-local" name="callback_at"
          style="width:100%;padding:8px 10px;border:1px solid #a5f3fc;border-radius:8px;font-size:13px;">
      </div>

      <!-- Success banner -->
      <div id="dmSuccessBanner" style="display:none;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:13px;font-weight:600;color:#166534;align-items:center;gap:8px;"></div>

      <!-- Footer -->
      <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:4px;">
        <button type="button" onclick="closeDispositionModal()" style="padding:9px 18px;background:#f3f4f6;color:#374151;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
          Cancel
        </button>
        <button type="submit" id="dmSubmitBtn" style="padding:9px 24px;background:#00b207;color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
          Log Call
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Floating Log Call button (bottom-right) -->
<button id="quickLogCallBtn" onclick="openDispositionModal()" title="Log a Call"
  style="position:fixed;bottom:28px;right:28px;width:52px;height:52px;border-radius:50%;background:#00b207;color:white;border:none;font-size:19px;cursor:pointer;box-shadow:0 4px 18px rgba(0,178,7,.38);z-index:9000;display:flex;align-items:center;justify-content:center;transition:transform .15s,box-shadow .15s;"
  onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 24px rgba(0,178,7,.5)'"
  onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 18px rgba(0,178,7,.38)'">
  <i class="fa-solid fa-phone-volume"></i>
</button>

<script>
// Direction toggle visual
document.querySelectorAll('#dispositionForm input[name="direction"]').forEach(radio => {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.dm-dir-opt').forEach(span => {
      const isSelected = span.dataset.val === this.value;
      span.style.background  = isSelected ? '#f0fdf4' : 'white';
      span.style.color       = isSelected ? '#166534' : '#6b7280';
    });
  });
});

// Outcome card visual toggle
document.querySelectorAll('.dm-outcome-btn').forEach(btn => {
  btn.closest('label').querySelector('input').addEventListener('change', function() {
    document.querySelectorAll('.dm-outcome-btn').forEach(b => {
      b.style.borderColor = '#e5e7eb';
      b.style.background  = 'white';
      b.style.color       = '#6b7280';
    });
    btn.style.borderColor = btn.dataset.color;
    btn.style.background  = btn.dataset.bg;
    btn.style.color       = btn.dataset.color;
  });
  // Click on the div itself fires the label
  btn.addEventListener('click', function() {
    this.closest('label').querySelector('input').dispatchEvent(new Event('change'));
  });
});
</script>

  <!-- Enable Notifications Banner (admin) -->
  <script>
  (function() {
    if (localStorage.getItem('admin_notif_enabled')) return;
    if (typeof Notification !== 'undefined' && Notification.permission === 'denied') return;
    setTimeout(function() {
      var b = document.getElementById('adminNotifEnableBanner');
      if (b) b.style.display = 'flex';
    }, 2000);
  })();
  window._enableAdminNotifs = function() {
    if (typeof _unlockAdminAudio === 'function') _unlockAdminAudio();
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
      Notification.requestPermission().then(function(r) { _adminBrowserNotifPerm = r; });
    }
    localStorage.setItem('admin_notif_enabled', '1');
    var b = document.getElementById('adminNotifEnableBanner');
    if (b) b.style.display = 'none';
  };
  window._dismissAdminNotifBanner = function(e) {
    e.stopPropagation();
    document.getElementById('adminNotifEnableBanner').style.display = 'none';
  };
  </script>
  <div id="adminNotifEnableBanner" onclick="window._enableAdminNotifs()" style="display:none;position:fixed;bottom:24px;right:24px;z-index:9999;background:var(--primary,#4f46e5);color:#fff;border-radius:12px;padding:14px 18px;box-shadow:0 4px 20px rgba(0,0,0,0.2);align-items:center;gap:12px;cursor:pointer;font-size:14px;font-weight:600;max-width:300px;animation:slideInBanner 0.4s ease;">
    <i class="fas fa-bell" style="font-size:18px;flex-shrink:0;"></i>
    <span style="flex:1;">Enable notifications &amp; sound</span>
    <button onclick="window._dismissAdminNotifBanner(event)" title="Dismiss" style="background:none;border:none;color:#fff;cursor:pointer;font-size:18px;line-height:1;padding:0;opacity:0.8;">&times;</button>
  </div>
  <style>
  @keyframes slideInBanner { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }
  </style>
<script>
document.querySelectorAll('[data-auto-dismiss]').forEach(function(el) {
    setTimeout(function() {
        el.style.opacity = '0';
        setTimeout(function() { el.style.display = 'none'; }, 600);
    }, 4000);
});
</script>
</body>
</html>