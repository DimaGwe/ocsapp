<?php

/**
 * OCSAPP Admin Layout
 * File: app/Views/admin/layout.php
 */

// Get current language from session or default to English
$currentLang = $_SESSION['language'] ?? 'fr';

// Language translations for admin panel
$translations = [
    'en' => [
        'admin_panel' => 'Admin Panel',
        'dashboard' => 'Dashboard',
        'user_management' => 'User Management',
        'users' => 'Users',
        'sellers' => 'Sellers',
        'delivery' => 'Delivery',
        'shops' => 'Shops',
        'catalog' => 'Catalog',
        'products' => 'Products',
        'stock_management' => 'Stock Management',
        'categories' => 'Categories',
        'vendors' => 'Vendors',
        'sales' => 'Sales',
        'orders' => 'Orders',
        'reports' => 'Reports',
        'marketing' => 'Marketing',
        'advertisements' => 'Advertisements',
        'affiliates' => 'Affiliates',
        'coupons' => 'Coupons',
        'system' => 'System',
        'settings' => 'Settings',
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
        'sellers' => 'Vendeurs',
        'delivery' => 'Livreurs',
        'shops' => 'Boutiques',
        'catalog' => 'Catalogue',
        'products' => 'Produits',
        'stock_management' => 'Gestion Stock',
        'categories' => 'Catégories',
        'vendors' => 'Fournisseurs',
        'sales' => 'Ventes',
        'orders' => 'Commandes',
        'reports' => 'Rapports',
        'marketing' => 'Marketing',
        'advertisements' => 'Publicités',
        'affiliates' => 'Affiliés',
        'coupons' => 'Coupons',
        'system' => 'Système',
        'settings' => 'Paramètres',
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
      z-index: 30;
      transition: transform var(--transition-base);
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
      padding: 0 16px;
      margin: 24px 0 8px;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: var(--gray-500);
      font-family: 'Poppins', sans-serif;
    }

    .nav-section-title:first-child {
      margin-top: 0;
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
      margin-right: 12px;
      font-size: 16px;
      text-align: center;
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

      <!-- Navigation -->
      <nav class="sidebar-nav">
        <a href="<?= url('admin/dashboard') ?>" class="nav-link <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
          <i class="fa-solid fa-house"></i>
          <span><?= $t['dashboard'] ?></span>
        </a>

        <a href="<?= url('admin/visitor-analytics') ?>" class="nav-link">
  <i class="fa-solid fa-users-viewfinder"></i>
  <span>Visitor Analytics</span>
</a>

        <!-- User Management -->
        <p class="nav-section-title"><?= $t['user_management'] ?></p>
        <a href="<?= url('admin/users') ?>" class="nav-link <?= ($currentPage ?? '') === 'users' ? 'active' : '' ?>">
          <i class="fa-solid fa-users"></i>
          <span><?= $t['users'] ?></span>
        </a>
        <a href="<?= url('admin/sellers') ?>" class="nav-link <?= ($currentPage ?? '') === 'sellers' ? 'active' : '' ?>">
          <i class="fa-solid fa-store"></i>
          <span><?= $t['sellers'] ?></span>
        </a>
        <a href="<?= url('/admin/delivery') ?>" class="nav-link <?= ($currentPage ?? '') === 'delivery-staff' ? 'active' : '' ?>">
          <i class="fa-solid fa-truck"></i>
          <span><?= $t['delivery'] ?></span>
        </a>
        <a href="<?= url('admin/shops') ?>" class="nav-link <?= ($currentPage ?? '') === 'shops' ? 'active' : '' ?>">
          <i class="fa-solid fa-shop"></i>
          <span><?= $t['shops'] ?></span>
        </a>
        <a href="<?= url('admin/ocs-store/edit') ?>" class="nav-link <?= ($currentPage ?? '') === 'ocs-store' ? 'active' : '' ?>">
          <i class="fa-solid fa-store"></i>
          <span>OCS Store</span>
        </a>

        <!-- Catalog -->
        <p class="nav-section-title"><?= $t['catalog'] ?></p>
        <a href="<?= url('admin/products') ?>" class="nav-link <?= ($currentPage ?? '') === 'products' ? 'active' : '' ?>">
          <i class="fa-solid fa-box"></i>
          <span><?= $t['products'] ?></span>
        </a>
        <a href="<?= url('admin/products/stock') ?>" class="nav-link <?= ($currentPage ?? '') === 'stock' ? 'active' : '' ?>">
          <i class="fa-solid fa-warehouse"></i>
          <span><?= $t['stock_management'] ?></span>
        </a>
        <a href="<?= url('admin/categories') ?>" class="nav-link <?= ($currentPage ?? '') === 'categories' ? 'active' : '' ?>">
          <i class="fa-solid fa-tags"></i>
          <span><?= $t['categories'] ?></span>
        </a>
        <a href="<?= url('admin/vendors') ?>" class="nav-link <?= ($currentPage ?? '') === 'vendors' ? 'active' : '' ?>">
          <i class="fa-solid fa-truck-field"></i>
          <span><?= $t['vendors'] ?></span>
        </a>

        <!-- Sales -->
        <p class="nav-section-title"><?= $t['sales'] ?></p>
        <a href="<?= url('admin/orders') ?>" class="nav-link <?= ($currentPage ?? '') === 'orders' ? 'active' : '' ?>">
          <i class="fa-solid fa-cart-shopping"></i>
          <span><?= $t['orders'] ?></span>
        </a>
        <a href="<?= url('admin/reports') ?>" class="nav-link <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
          <i class="fa-solid fa-chart-line"></i>
          <span><?= $t['reports'] ?></span>
        </a>

        <!-- Marketing -->
        <p class="nav-section-title"><?= $t['marketing'] ?></p>
        <a href="<?= url('admin/ads') ?>" class="nav-link <?= ($currentPage ?? '') === 'ads' ? 'active' : '' ?>">
          <i class="fa-solid fa-rectangle-ad"></i>
          <span><?= $t['advertisements'] ?></span>
        </a>
        <a href="<?= url('admin/affiliates') ?>" class="nav-link <?= ($currentPage ?? '') === 'affiliates' ? 'active' : '' ?>">
          <i class="fa-solid fa-handshake"></i>
          <span><?= $t['affiliates'] ?></span>
        </a>
        <a href="<?= url('admin/coupons') ?>" class="nav-link <?= ($currentPage ?? '') === 'coupons' ? 'active' : '' ?>">
          <i class="fa-solid fa-ticket"></i>
          <span><?= $t['coupons'] ?></span>
        </a>

        <!-- System -->
        <p class="nav-section-title"><?= $t['system'] ?></p>
        <a href="<?= url('admin/settings') ?>" class="nav-link <?= ($currentPage ?? '') === 'settings' ? 'active' : '' ?>">
          <i class="fa-solid fa-gear"></i>
          <span><?= $t['settings'] ?></span>
        </a>
        <a href="<?= url('admin/cms') ?>" class="nav-link <?= ($currentPage ?? '') === 'cms' ? 'active' : '' ?>">
          <i class="fa-solid fa-file-lines"></i>
          <span><?= $t['cms'] ?></span>
        </a>
        <a href="<?= url('admin/emails') ?>" class="nav-link <?= ($currentPage ?? '') === 'emails' ? 'active' : '' ?>">
          <i class="fa-solid fa-envelope"></i>
          <span>Email Templates</span>
        </a>
        <a href="<?= url('admin/translations') ?>" class="nav-link <?= ($currentPage ?? '') === 'translations' ? 'active' : '' ?>">
          <i class="fa-solid fa-language"></i>
          <span class="notranslate" translate="no">Translations</span>
        </a>
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
          <!-- Notifications -->
          <button class="topbar-btn">
            <i class="fa-solid fa-bell"></i>
            <span class="notification-badge">3</span>
          </button>

          <!-- User Dropdown -->
          <div class="user-dropdown" x-data="{ open: false }">
            <button @click="open = !open" class="user-btn">
              <div class="user-avatar">
                <?= strtoupper(substr(user()['first_name'] ?? 'A', 0, 1)) ?>
              </div>
              <span class="user-name"><?= htmlspecialchars(user()['first_name'] ?? 'Admin') ?></span>
              <i class="fa-solid fa-chevron-down"></i>
            </button>

            <div x-show="open" @click.away="open = false" x-cloak class="dropdown-menu">
              <a href="<?= url('admin/profile') ?>" class="dropdown-link">
                <i class="fa-solid fa-user"></i> <?= $t['profile'] ?>
              </a>
              <a href="<?= url('admin/settings') ?>" class="dropdown-link">
                <i class="fa-solid fa-gear"></i> <?= $t['settings'] ?>
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
  </script>

  <?= $scripts ?? '' ?>
</body>
</html>