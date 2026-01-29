<?php

/**
 * OCSAPP Admin Layout
 * File: app/Views/admin/layout.php
 */

// Include admin permission helper
require_once __DIR__ . '/../../Helpers/AdminPermissionHelper.php';

// Get current language from session or default to English
$currentLang = $_SESSION['language'] ?? 'fr';

// Get current user's role for permission checks
$userRole = $_SESSION['user']['role'] ?? null;

// Fetch notification badge counts
$badgeCounts = [
    'orders' => 0,
    'leads' => 0,
    'distribution' => 0,
    'sellers' => 0,
];

try {
    $db = \App\Helpers\Database::getInstance();

    // Count pending orders
    $pendingOrders = $db->query("SELECT COUNT(*) as count FROM orders WHERE status IN ('pending', 'processing')");
    if ($pendingOrders && $row = $pendingOrders->fetch_assoc()) {
        $badgeCounts['orders'] = (int)$row['count'];
    }

    // Count new leads (status = 'new')
    $newLeads = $db->query("SELECT COUNT(*) as count FROM leads WHERE status = 'new'");
    if ($newLeads && $row = $newLeads->fetch_assoc()) {
        $badgeCounts['leads'] = (int)$row['count'];
    }

    // Count pending distribution requests
    $pendingDist = $db->query("SELECT COUNT(*) as count FROM distribution_requests WHERE status = 'pending'");
    if ($pendingDist && $row = $pendingDist->fetch_assoc()) {
        $badgeCounts['distribution'] = (int)$row['count'];
    }

    // Count pending seller verification requests
    $pendingSellers = $db->query("SELECT COUNT(*) as count FROM sellers WHERE verification_status = 'pending'");
    if ($pendingSellers && $row = $pendingSellers->fetch_assoc()) {
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

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
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
      font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      min-width: 20px;
      height: 20px;
      padding: 0 6px;
      margin-left: auto;
      background: #ef4444;
      color: white;
      font-size: 11px;
      font-weight: 600;
      border-radius: 10px;
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
      font-family: 'Poppins', sans-serif;
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

    /* Dropdown Navigation */
    .nav-dropdown {
      margin-bottom: 4px;
    }

    .nav-dropdown-toggle {
      cursor: pointer;
      border: none;
      background: none;
    }

    .nav-dropdown-toggle .rotate-180 {
      transform: rotate(180deg);
    }

    .nav-dropdown-menu {
      overflow: hidden;
    }

    .nav-sub-link {
      padding: 10px 16px !important;
      font-size: 13px !important;
      margin-bottom: 2px !important;
    }

    .nav-sub-link i {
      font-size: 14px !important;
      width: 18px !important;
      min-width: 18px !important;
      margin-right: 10px !important;
    }

    .nav-sub-link span {
      font-size: 13px !important;
    }

    .nav-sub-link.active {
      background: var(--primary-600) !important;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
    }

    .user-name {
      font-weight: 600;
      font-size: 14px;
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
      font-family: 'Poppins', sans-serif;
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
</head>
<body>
  <div class="admin-layout">
    
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
      <!-- Logo -->
      <div class="sidebar-header">
        <div class="sidebar-logo">
          <div class="sidebar-logo-img">
            <img src="<?= asset('images/logo.png') ?>" alt="OCSAPP Logo">
          </div>
          <span class="sidebar-brand">OCSAPP</span>
        </div>
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
        $roleInfo = $roleLabels[$userRole] ?? ['icon' => 'fa-user', 'label' => ucfirst($userRole)];
        ?>
        <span class="role-badge <?= $roleClass ?>">
          <i class="fa-solid <?= $roleInfo['icon'] ?>"></i>
          <?= $roleInfo['label'] ?>
        </span>
      </div>

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <?php
        // Define which pages belong to which section for active highlighting
        $sectionPages = [
            'users_accounts' => ['users', 'sellers', 'business-accounts', 'shops'],
            'operations' => ['orders', 'shipments', 'delivery', 'distribution'],
            'catalog' => ['products', 'stock', 'categories', 'ocs-store'],
            'procurement' => ['suppliers', 'purchase-orders', 'supplier-catalog'],
            'sales_marketing' => ['leads', 'sales', 'promo-banners', 'ads', 'affiliates', 'coupons'],
            'reports_analytics' => ['analytics', 'reports'],
            'content' => ['homepage', 'homepage-settings', 'cms', 'content-pages', 'legal', 'sliders', 'emails', 'translations'],
            'system' => ['settings'],
        ];

        // Determine active section
        $activeSection = '';
        foreach ($sectionPages as $section => $pages) {
            if (in_array($currentPage ?? '', $pages)) {
                $activeSection = $section;
                break;
            }
        }
        ?>
        <?php if (AdminPermissionHelper::canAccessMenu('dashboard', $userRole)): ?>
        <a href="<?= url('admin/dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
          <i class="fa-solid fa-house"></i>
          <span><?= $t['dashboard'] ?></span>
        </a>
        <?php endif; ?>

        <?php if (AdminPermissionHelper::canAccessMenu('planner', $userRole)): ?>
        <!-- Team Planner with Dropdown -->
        <div class="nav-dropdown" x-data="{ open: <?= in_array($currentPage ?? '', ['planner', 'html-editor']) ? 'true' : 'false' ?> }">
          <button @click="open = !open" class="nav-link nav-dropdown-toggle <?= in_array($currentPage ?? '', ['planner', 'html-editor']) ? 'active' : '' ?>" style="width: 100%; justify-content: space-between;">
            <span style="display: flex; align-items: center;">
              <i class="fa-solid fa-calendar-check"></i>
              <span><?= $t['planner'] ?></span>
            </span>
            <i class="fa-solid fa-chevron-down" :class="open ? 'rotate-180' : ''" style="font-size: 10px; transition: transform 0.2s;"></i>
          </button>
          <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="nav-dropdown-menu" style="padding-left: 32px;">
            <a href="<?= url('admin/planner') ?>" class="nav-link nav-sub-link <?= ($currentPage ?? '') === 'planner' ? 'active' : '' ?>">
              <i class="fa-solid fa-clipboard-list"></i>
              <span><?= $currentLang === 'fr' ? 'Planificateur' : 'Team Planner' ?></span>
            </a>
            <a href="<?= url('admin/planner/html-editor') ?>" class="nav-link nav-sub-link <?= ($currentPage ?? '') === 'html-editor' ? 'active' : '' ?>">
              <i class="fa-solid fa-code"></i>
              <span><?= $currentLang === 'fr' ? 'Editeur HTML' : 'HTML Editor' ?></span>
            </a>
          </div>
        </div>
        <?php endif; ?>

        <!-- Users & Accounts -->
        <?php
        $usersItems = ['users', 'sellers', 'business-accounts', 'shops'];
        $hasUsersAccess = false;
        foreach ($usersItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasUsersAccess = true;
                break;
            }
        }
        if ($hasUsersAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'users_accounts' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Utilisateurs' : 'Users & Accounts' ?></p>
        <?php if (AdminPermissionHelper::canAccessMenu('users', $userRole)): ?>
        <a href="<?= url('admin/users') ?>" class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
          <i class="fa-solid fa-users"></i>
          <span><?= $t['users'] ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('sellers', $userRole)): ?>
        <a href="<?= url('admin/sellers') ?>" class="nav-link <?= ($currentPage ?? '') === 'sellers' ? 'active' : '' ?>">
          <i class="fa-solid fa-user-tie"></i>
          <span><?= $t['sellers'] ?></span>
          <?php if ($badgeCounts['sellers'] > 0): ?><span class="nav-badge info"><?= $badgeCounts['sellers'] ?></span><?php endif; ?>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('business-accounts', $userRole)): ?>
        <a href="<?= url('admin/business-accounts') ?>" class="nav-link <?= ($currentPage ?? '') === 'business-accounts' ? 'active' : '' ?>">
          <i class="fa-solid fa-building"></i>
          <span><?= $t['business_accounts'] ?? 'Business Accounts' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('shops', $userRole)): ?>
        <a href="<?= url('admin/shops') ?>" class="nav-link <?= ($currentPage ?? '') === 'shops' ? 'active' : '' ?>">
          <i class="fa-solid fa-shop"></i>
          <span><?= $t['shops'] ?></span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Operations -->
        <?php
        $opsItems = ['orders', 'shipments', 'delivery', 'distribution'];
        $hasOpsAccess = false;
        foreach ($opsItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasOpsAccess = true;
                break;
            }
        }
        if ($hasOpsAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'operations' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Opérations' : 'Operations' ?></p>
        <?php if (AdminPermissionHelper::canAccessMenu('orders', $userRole)): ?>
        <a href="<?= url('admin/orders') ?>" class="nav-link <?= ($currentPage ?? '') === 'orders' ? 'active' : '' ?>">
          <i class="fa-solid fa-cart-shopping"></i>
          <span><?= $t['orders'] ?></span>
          <?php if ($badgeCounts['orders'] > 0): ?><span class="nav-badge"><?= $badgeCounts['orders'] ?></span><?php endif; ?>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('shipments', $userRole)): ?>
        <a href="<?= url('admin/shipments') ?>" class="nav-link <?= ($currentPage ?? '') === 'shipments' ? 'active' : '' ?>">
          <i class="fa-solid fa-truck-fast"></i>
          <span><?= $t['shipments'] ?? 'Shipments' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('delivery', $userRole)): ?>
        <a href="<?= url('/admin/delivery') ?>" class="nav-link <?= ($currentPage ?? '') === 'delivery' ? 'active' : '' ?>">
          <i class="fa-solid fa-truck"></i>
          <span><?= $t['delivery'] ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('distribution', $userRole)): ?>
        <a href="<?= url('admin/distribution') ?>" class="nav-link <?= ($currentPage ?? '') === 'distribution' ? 'active' : '' ?>">
          <i class="fa-solid fa-boxes-stacked"></i>
          <span><?= $t['distribution_requests'] ?? 'Distribution' ?></span>
          <?php if ($badgeCounts['distribution'] > 0): ?><span class="nav-badge warning"><?= $badgeCounts['distribution'] ?></span><?php endif; ?>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Catalog -->
        <?php
        $catalogItems = ['products', 'stock', 'categories', 'ocs-store'];
        $hasCatalogAccess = false;
        foreach ($catalogItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasCatalogAccess = true;
                break;
            }
        }
        if ($hasCatalogAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'catalog' ? 'section-active' : '' ?>"><?= $t['catalog'] ?></p>
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
        <?php if (AdminPermissionHelper::canAccessMenu('ocs-store', $userRole)): ?>
        <a href="<?= url('admin/ocs-store/edit') ?>" class="nav-link <?= ($currentPage ?? '') === 'ocs-store' ? 'active' : '' ?>">
          <i class="fa-solid fa-store"></i>
          <span>OCS Store</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Procurement -->
        <?php
        $procurementItems = ['suppliers', 'purchase-orders', 'supplier-catalog'];
        $hasProcurementAccess = false;
        foreach ($procurementItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasProcurementAccess = true;
                break;
            }
        }
        if ($hasProcurementAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'procurement' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Approvisionnement' : 'Procurement' ?></p>
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
        <?php endif; ?>

        <!-- Sales & Marketing -->
        <?php
        $salesMarketingItems = ['leads', 'sales', 'promo-banners', 'ads', 'affiliates', 'coupons'];
        $hasSalesMarketingAccess = false;
        foreach ($salesMarketingItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasSalesMarketingAccess = true;
                break;
            }
        }
        if ($hasSalesMarketingAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'sales_marketing' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Ventes & Marketing' : 'Sales & Marketing' ?></p>
        <?php if (AdminPermissionHelper::canAccessMenu('leads', $userRole)): ?>
        <a href="<?= url('admin/leads') ?>" class="nav-link <?= ($currentPage ?? '') === 'leads' ? 'active' : '' ?>">
          <i class="fa-solid fa-user-plus"></i>
          <span><?= $currentLang === 'fr' ? 'Prospects CRM' : 'Leads CRM' ?></span>
          <?php if ($badgeCounts['leads'] > 0): ?><span class="nav-badge success"><?= $badgeCounts['leads'] ?></span><?php endif; ?>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('sales', $userRole)): ?>
        <a href="<?= url('admin/sales') ?>" class="nav-link <?= ($currentPage ?? '') === 'sales' ? 'active' : '' ?>">
          <i class="fa-solid fa-percent"></i>
          <span><?= $t['sales_management'] ?></span>
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
        <?php endif; ?>

        <!-- Reports & Analytics -->
        <?php
        $reportsItems = ['analytics', 'reports'];
        $hasReportsAccess = false;
        foreach ($reportsItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasReportsAccess = true;
                break;
            }
        }
        if ($hasReportsAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'reports_analytics' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Rapports' : 'Reports & Analytics' ?></p>
        <?php if (AdminPermissionHelper::canAccessMenu('analytics', $userRole)): ?>
        <a href="<?= url('admin/visitor-analytics') ?>" class="nav-link <?= ($currentPage ?? '') === 'analytics' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-pie"></i>
          <span><?= $currentLang === 'fr' ? 'Analytique Visiteurs' : 'Visitor Analytics' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('reports', $userRole)): ?>
        <a href="<?= url('admin/reports') ?>" class="nav-link <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-line"></i>
          <span><?= $t['reports'] ?></span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Content Management -->
        <?php
        $contentItems = ['homepage', 'cms', 'content-pages', 'legal', 'sliders', 'emails', 'translations'];
        $hasContentAccess = false;
        foreach ($contentItems as $item) {
            if (AdminPermissionHelper::canAccessMenu($item, $userRole)) {
                $hasContentAccess = true;
                break;
            }
        }
        if ($hasContentAccess):
        ?>
        <p class="nav-section-title <?= $activeSection === 'content' ? 'section-active' : '' ?>"><?= $currentLang === 'fr' ? 'Gestion de Contenu' : 'Content Management' ?></p>
        <?php if (AdminPermissionHelper::canAccessMenu('homepage', $userRole)): ?>
        <a href="<?= url('admin/homepage') ?>" class="nav-link <?= ($currentPage ?? '') === 'homepage-settings' ? 'active' : '' ?>">
          <i class="fa-solid fa-house-circle-check"></i>
          <span><?= $t['homepage_settings'] ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('cms', $userRole)): ?>
        <a href="<?= url('admin/cms') ?>" class="nav-link <?= ($currentPage ?? '') === 'cms' ? 'active' : '' ?>">
          <i class="fa-solid fa-file-lines"></i>
          <span><?= $currentLang === 'fr' ? 'Pages Statiques' : 'Static Pages' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('content-pages', $userRole)): ?>
        <a href="<?= url('admin/content-pages') ?>" class="nav-link <?= ($currentPage ?? '') === 'content-pages' ? 'active' : '' ?>">
          <i class="fa-solid fa-newspaper"></i>
          <span><?= $currentLang === 'fr' ? 'Pages de Contenu' : 'Content Pages' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('legal', $userRole)): ?>
        <a href="<?= url('admin/legal') ?>" class="nav-link <?= ($currentPage ?? '') === 'legal' ? 'active' : '' ?>">
          <i class="fa-solid fa-scale-balanced"></i>
          <span><?= $currentLang === 'fr' ? 'Documents Légaux' : 'Legal Documents' ?></span>
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
          <span><?= $currentLang === 'fr' ? 'Modèles Email' : 'Email Templates' ?></span>
        </a>
        <?php endif; ?>
        <?php if (AdminPermissionHelper::canAccessMenu('translations', $userRole)): ?>
        <a href="<?= url('admin/translations') ?>" class="nav-link <?= ($currentPage ?? '') === 'translations' ? 'active' : '' ?>">
          <i class="fa-solid fa-language"></i>
          <span class="notranslate" translate="no">Translations</span>
        </a>
        <?php endif; ?>
        <?php endif; ?>

        <!-- System (Super Admin Only) -->
        <?php if (AdminPermissionHelper::canAccessMenu('settings', $userRole)): ?>
        <p class="nav-section-title <?= $activeSection === 'system' ? 'section-active' : '' ?>"><?= $t['system'] ?></p>
        <a href="<?= url('admin/settings') ?>" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
          <i class="fa-solid fa-gear"></i>
          <span><?= $t['settings'] ?></span>
        </a>
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

        <!-- Search (Desktop) -->
        <div class="topbar-search">
          <input 
            type="search" 
            placeholder="<?= $t['search_placeholder'] ?>"
          >
          <i class="fa-solid fa-magnifying-glass"></i>
        </div>

        <!-- Right Side -->
        <div class="topbar-actions">
          <!-- Language Switcher -->
          <div class="language-switcher" x-data="{ open: false }">
            <button @click="open = !open" class="topbar-btn" title="<?= getCurrentLanguage() === 'fr' ? 'Changer la langue' : 'Change Language' ?>">
              <i class="fa-solid fa-globe"></i>
              <span style="font-size: 0.75rem; font-weight: 600;"><?= strtoupper(getCurrentLanguage()) ?></span>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu" style="display: none; right: 0; left: auto; min-width: 150px;">
              <a href="#" @click.prevent="window.switchLanguage('en'); open = false" class="dropdown-link <?= getCurrentLanguage() === 'en' ? 'active' : '' ?>">
                <span style="margin-right: 8px;">🇺🇸</span> English
              </a>
              <a href="#" @click.prevent="window.switchLanguage('fr'); open = false" class="dropdown-link <?= getCurrentLanguage() === 'fr' ? 'active' : '' ?>">
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
              <a href="<?= url('admin/settings') ?>" class="dropdown-link">
                <i class="fa-solid fa-gear"></i> <?= $t['settings'] ?>
              </a>
              <a href="<?= url('admin/planner') ?>" class="dropdown-link">
                <i class="fa-solid fa-clipboard-list"></i> <?= $t['planner'] ?>
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?= url('logout') ?>" class="dropdown-link danger">
                <i class="fa-solid fa-right-from-bracket"></i> <?= $t['logout'] ?>
              </a>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="page-content">
        <?php if (hasFlash('success')): ?>
          <div class="alert alert-success" role="alert">
            <?= htmlspecialchars(getFlash('success')) ?>
          </div>
        <?php endif; ?>

        <?php if (hasFlash('error')): ?>
          <div class="alert alert-error" role="alert">
            <?= htmlspecialchars(getFlash('error')) ?>
          </div>
        <?php endif; ?>

        <?= $content ?? '' ?>
      </main>

      <!-- Footer -->
      <footer class="admin-footer">
        <p>OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?></p>
        <p>Version 1.0.0</p>
      </footer>
    </div>
  </div>

  <!-- Alpine.js for dropdowns -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
  <script>
    // Sidebar toggle for mobile
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('sidebarOverlay');

      sidebar.classList.toggle('show-mobile');
      overlay.classList.toggle('show');
    }

    // Auto-scroll sidebar to show active menu item
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const activeLink = sidebar.querySelector('.nav-link.active');

      if (activeLink) {
        // Wait a brief moment for layout to settle
        setTimeout(function() {
          const sidebarRect = sidebar.getBoundingClientRect();
          const linkRect = activeLink.getBoundingClientRect();

          // Check if active link is below the visible area
          if (linkRect.bottom > sidebarRect.bottom - 50) {
            // Scroll the active item into view with some padding
            activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
          // Check if active link is above the visible area (shouldn't happen on load, but just in case)
          else if (linkRect.top < sidebarRect.top + 100) {
            activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        }, 100);
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
          // Initial load
          this.fetchCount();

          // Poll for new notifications every 30 seconds
          this.pollInterval = setInterval(() => {
            this.fetchCount();
          }, 30000);
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

  <?= $scripts ?? '' ?>
</body>
</html>