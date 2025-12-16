<?php
/**
 * OCS Admin Dashboard
 * File: app/Views/admin/dashboard.php
 */

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'dashboard' => 'Dashboard',
        'welcome_back' => 'Welcome back',
        'whats_happening' => 'Here\'s what\'s happening today',
        'total_users' => 'Total Users',
        'active_sellers' => 'Active Sellers',
        'total_buyers' => 'Total Buyers',
        'delivery_staff' => 'Delivery Staff',
        'active_accounts' => 'Active accounts',
        'verified_vendors' => 'Verified vendors',
        'registered_customers' => 'Registered customers',
        'active_drivers' => 'Active drivers',
        'recent_users' => 'Recent Users',
        'user' => 'User',
        'role' => 'Role',
        'status' => 'Status',
        'no_recent_users' => 'No recent users',
        'view_all_users' => 'View all users',
        'recent_activity' => 'Recent Activity',
        'logged_in_from' => 'Logged in from',
        'no_recent_activity' => 'No recent activity',
        'quick_actions' => 'Quick Actions',
        'manage' => 'Manage',
        'users' => 'Users',
        'products' => 'Products',
        'delivery' => 'Delivery',
        'system' => 'System',
        'settings' => 'Settings',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'stock_overview' => 'Stock Overview',
        'view_all' => 'View All',
    ],
    'fr' => [
        'dashboard' => 'Tableau de bord',
        'welcome_back' => 'Bienvenue',
        'whats_happening' => 'Voici ce qui se passe aujourd\'hui',
        'total_users' => 'Utilisateurs Totaux',
        'active_sellers' => 'Vendeurs Actifs',
        'total_buyers' => 'Acheteurs Totaux',
        'delivery_staff' => 'Livreurs',
        'active_accounts' => 'Comptes actifs',
        'verified_vendors' => 'Vendeurs vérifiés',
        'registered_customers' => 'Clients enregistrés',
        'active_drivers' => 'Chauffeurs actifs',
        'recent_users' => 'Utilisateurs Récents',
        'user' => 'Utilisateur',
        'role' => 'Rôle',
        'status' => 'Statut',
        'no_recent_users' => 'Aucun utilisateur récent',
        'view_all_users' => 'Voir tous les utilisateurs',
        'recent_activity' => 'Activité Récente',
        'logged_in_from' => 'Connecté depuis',
        'no_recent_activity' => 'Aucune activité récente',
        'quick_actions' => 'Actions Rapides',
        'manage' => 'Gérer',
        'users' => 'Utilisateurs',
        'products' => 'Produits',
        'delivery' => 'Livraison',
        'system' => 'Système',
        'settings' => 'Paramètres',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'stock_overview' => 'Aperçu du Stock',
        'view_all' => 'Voir Tout',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Dashboard Specific Styles */
  .page-header {
    margin-bottom: 32px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .page-subtitle {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border-left: 4px solid;
    transition: all var(--transition-base);
  }

  .stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .stat-card.blue { border-color: #3b82f6; }
  .stat-card.green { border-color: var(--primary); }
  .stat-card.purple { border-color: #a855f7; }
  .stat-card.orange { border-color: #f97316; }

  /* Clickable stat cards */
  a.stat-card-link {
    text-decoration: none;
    color: inherit;
    display: block;
  }

  a.stat-card-link .stat-card {
    cursor: pointer;
  }

  .stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }

  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .stat-icon.blue { background: #dbeafe; color: #3b82f6; }
  .stat-icon.green { background: #dcfce7; color: var(--primary); }
  .stat-icon.purple { background: #f3e8ff; color: #a855f7; }
  .stat-icon.orange { background: #ffedd5; color: #f97316; }

  .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .stat-meta {
    font-size: 12px;
    color: var(--gray-500);
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .stat-meta i {
    font-size: 11px;
  }

  /* Content Grid */
  .content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
  }

  .content-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  .content-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
  }

  .content-card-title {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
  }

  /* Table */
  .table-wrapper {
    overflow-x: auto;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 24px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  td {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .user-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    flex-shrink: 0;
  }

  .user-info {
    min-width: 0;
  }

  .user-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .user-email {
    font-size: 12px;
    color: var(--gray-500);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.blue { background: #dbeafe; color: #1e40af; }
  .badge.green { background: #dcfce7; color: #166534; }
  .badge.gray { background: var(--gray-200); color: var(--gray-700); }

  .card-footer {
    padding: 16px 24px;
    background: var(--gray-50);
    border-top: 1px solid var(--border);
  }

  .card-footer a {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary);
    text-decoration: none;
    transition: color var(--transition-base);
  }

  .card-footer a:hover {
    color: var(--primary-600);
  }

  /* Activity List */
  .activity-list {
    padding: 24px;
  }

  .activity-item {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
  }

  .activity-item:last-child {
    margin-bottom: 0;
  }

  .activity-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dcfce7;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
  }

  .activity-content {
    flex: 1;
    min-width: 0;
  }

  .activity-user {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
    margin-bottom: 4px;
  }

  .activity-action {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 4px;
  }

  .activity-time {
    font-size: 11px;
    color: var(--gray-500);
  }

  .empty-state {
    padding: 48px 24px;
    text-align: center;
    color: var(--gray-500);
    font-size: 14px;
  }

  /* Quick Actions */
  .quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 24px;
  }

  .action-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    border-left: 4px solid;
    text-decoration: none;
    transition: all var(--transition-base);
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .action-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .action-card.blue { border-color: #3b82f6; }
  .action-card.green { border-color: var(--primary); }
  .action-card.purple { border-color: #a855f7; }
  .action-card.orange { border-color: #f97316; }

  .action-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
  }

  .action-icon.blue { background: #dbeafe; color: #3b82f6; }
  .action-icon.green { background: #dcfce7; color: var(--primary); }
  .action-icon.purple { background: #f3e8ff; color: #a855f7; }
  .action-icon.orange { background: #ffedd5; color: #f97316; }

  .action-content {
    flex: 1;
  }

  .action-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    margin-bottom: 4px;
  }

  .action-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    .content-grid {
      grid-template-columns: 1fr;
    }

    .quick-actions {
      grid-template-columns: 1fr;
    }
  }
</style>

<!-- Page Header -->
<div class="page-header">
  <h1 class="page-title"><?= $t['dashboard'] ?></h1>
  <p class="page-subtitle"><?= $t['welcome_back'] ?>, <?= htmlspecialchars(user()['first_name']) ?>! <?= $t['whats_happening'] ?></p>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
  <!-- Total Users -->
  <div class="stat-card blue">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_users'] ?></span>
      <div class="stat-icon blue">
        <i class="fas fa-users"></i>
      </div>
    </div>
    <div class="stat-value"><?= number_format($total_users ?? 0) ?></div>
    <div class="stat-meta">
      <i class="fas fa-arrow-up" style="color: #22c55e;"></i>
      <?= $t['active_accounts'] ?>
    </div>
  </div>

  <!-- Active Sellers -->
  <div class="stat-card green">
    <div class="stat-header">
      <span class="stat-label"><?= $t['active_sellers'] ?></span>
      <div class="stat-icon green">
        <i class="fas fa-store"></i>
      </div>
    </div>
    <div class="stat-value"><?= number_format($total_sellers ?? 0) ?></div>
    <div class="stat-meta">
      <i class="fas fa-store-alt"></i>
      <?= $t['verified_vendors'] ?>
    </div>
  </div>

  <!-- Total Buyers -->
  <div class="stat-card purple">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_buyers'] ?></span>
      <div class="stat-icon purple">
        <i class="fas fa-shopping-cart"></i>
      </div>
    </div>
    <div class="stat-value"><?= number_format($total_buyers ?? 0) ?></div>
    <div class="stat-meta">
      <i class="fas fa-user-check"></i>
      <?= $t['registered_customers'] ?>
    </div>
  </div>

  <!-- Delivery Staff (Clickable) -->
  <a href="<?= url('/admin/delivery/staff') ?>" class="stat-card-link">
    <div class="stat-card orange">
      <div class="stat-header">
        <span class="stat-label"><?= $t['delivery_staff'] ?></span>
        <div class="stat-icon orange">
          <i class="fas fa-truck"></i>
        </div>
      </div>
      <div class="stat-value"><?= number_format($active_delivery_staff ?? 0) ?></div>
      <div class="stat-meta">
        <i class="fas fa-check-circle" style="color: #22c55e;"></i>
        <?= $t['active_drivers'] ?>
      </div>
    </div>
  </a>
</div>

<!-- Stock Overview Widget -->
<div class="content-card" style="margin-bottom: 32px;">
  <div class="content-card-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h3 class="content-card-title"><?= $t['stock_overview'] ?? 'Stock Overview' ?></h3>
    <a href="<?= url('admin/products/stock') ?>" style="font-size: 13px; color: var(--primary); text-decoration: none; font-weight: 600;">
      <?= $t['view_all'] ?? 'View All' ?> <i class="fas fa-arrow-right"></i>
    </a>
  </div>

  <?php
  // Get stock statistics
  $db = \Database::getConnection();

  // Total products with OCS Store inventory
  $stmt = $db->query("SELECT COUNT(*) FROM shop_inventory WHERE shop_id = 1");
  $totalOcsProducts = (int)$stmt->fetchColumn();

  // Total OCS Store stock quantity
  $stmt = $db->query("SELECT SUM(stock_quantity) FROM shop_inventory WHERE shop_id = 1");
  $totalOcsStock = (int)$stmt->fetchColumn();

  // Products out of stock in OCS Store
  $stmt = $db->query("SELECT COUNT(*) FROM shop_inventory WHERE shop_id = 1 AND stock_quantity = 0");
  $ocsOutOfStock = (int)$stmt->fetchColumn();

  // Products low stock in OCS Store (< 10)
  $stmt = $db->query("SELECT COUNT(*) FROM shop_inventory WHERE shop_id = 1 AND stock_quantity > 0 AND stock_quantity < 10");
  $ocsLowStock = (int)$stmt->fetchColumn();

  // Total available for sellers across all products
  $stmt = $db->query("SELECT SUM(available_stock) FROM products");
  $totalAvailableForSellers = (int)$stmt->fetchColumn();
  ?>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; padding: 24px;">
    <!-- OCS Store Total -->
    <div style="text-align: center; padding: 16px; background: #dcfce7; border-radius: var(--radius-md);">
      <div style="font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
        OCS Store Products
      </div>
      <div style="font-size: 32px; font-weight: 700; color: var(--primary); margin-bottom: 4px;">
        <?= number_format($totalOcsProducts) ?>
      </div>
      <div style="font-size: 12px; color: var(--gray-600);">
        <?= number_format($totalOcsStock) ?> units total
      </div>
    </div>

    <!-- Out of Stock -->
    <div style="text-align: center; padding: 16px; background: #fee2e2; border-radius: var(--radius-md);">
      <div style="font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
        Out of Stock
      </div>
      <div style="font-size: 32px; font-weight: 700; color: #ef4444; margin-bottom: 4px;">
        <?= number_format($ocsOutOfStock) ?>
      </div>
      <div style="font-size: 12px; color: var(--gray-600);">
        <?= $ocsOutOfStock > 0 ? 'Needs restocking' : 'All in stock' ?>
      </div>
    </div>

    <!-- Low Stock -->
    <div style="text-align: center; padding: 16px; background: #fef3c7; border-radius: var(--radius-md);">
      <div style="font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
        Low Stock
      </div>
      <div style="font-size: 32px; font-weight: 700; color: #f59e0b; margin-bottom: 4px;">
        <?= number_format($ocsLowStock) ?>
      </div>
      <div style="font-size: 12px; color: var(--gray-600);">
        <?= $ocsLowStock > 0 ? 'Below 10 units' : 'Stock healthy' ?>
      </div>
    </div>

    <!-- Available for Sellers -->
    <div style="text-align: center; padding: 16px; background: #dbeafe; border-radius: var(--radius-md);">
      <div style="font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px;">
        For Sellers
      </div>
      <div style="font-size: 32px; font-weight: 700; color: #3b82f6; margin-bottom: 4px;">
        <?= number_format($totalAvailableForSellers) ?>
      </div>
      <div style="font-size: 12px; color: var(--gray-600);">
        units available
      </div>
    </div>
  </div>
</div>

<!-- Two Column Layout -->
<div class="content-grid">
  
  <!-- Recent Users -->
  <div class="content-card">
    <div class="content-card-header">
      <h3 class="content-card-title"><?= $t['recent_users'] ?></h3>
    </div>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th><?= $t['user'] ?></th>
            <th><?= $t['role'] ?></th>
            <th><?= $t['status'] ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($recent_users)): ?>
            <?php foreach ($recent_users as $u): ?>
              <tr>
                <td>
                  <div class="user-cell">
                    <div class="user-avatar">
                      <?= strtoupper(substr($u['first_name'], 0, 1)) ?>
                    </div>
                    <div class="user-info">
                      <div class="user-name"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></div>
                      <div class="user-email"><?= htmlspecialchars($u['email']) ?></div>
                    </div>
                  </div>
                </td>
                <td>
                  <span class="badge blue">
                    <?= htmlspecialchars($u['role_display'] ?? 'N/A') ?>
                  </span>
                </td>
                <td>
                  <span class="badge <?= $u['status'] === 'active' ? 'green' : 'gray' ?>">
                    <?= $u['status'] === 'active' ? $t['active'] : $t['inactive'] ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="empty-state">
                <?= $t['no_recent_users'] ?>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      <a href="<?= url('admin/users') ?>">
        <?= $t['view_all_users'] ?> <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  </div>

  <!-- Recent Activity -->
  <div class="content-card">
    <div class="content-card-header">
      <h3 class="content-card-title"><?= $t['recent_activity'] ?></h3>
    </div>
    <div class="activity-list">
      <?php if (!empty($recent_logins)): ?>
        <?php foreach (array_slice($recent_logins, 0, 5) as $log): ?>
          <div class="activity-item">
            <div class="activity-icon">
              <i class="fas fa-sign-in-alt"></i>
            </div>
            <div class="activity-content">
              <div class="activity-user">
                <?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?>
              </div>
              <div class="activity-action">
                <?= $t['logged_in_from'] ?> <?= htmlspecialchars(substr($log['ip_address'] ?? 'unknown', 0, 20)) ?>
              </div>
              <div class="activity-time">
                <?= formatDate($log['created_at'], 'M d, Y h:i A') ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <?= $t['no_recent_activity'] ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
  <a href="<?= url('admin/users') ?>" class="action-card blue">
    <div class="action-icon blue">
      <i class="fas fa-user-plus"></i>
    </div>
    <div class="action-content">
      <div class="action-label"><?= $t['manage'] ?></div>
      <div class="action-title"><?= $t['users'] ?></div>
    </div>
  </a>

  <a href="<?= url('admin/products') ?>" class="action-card green">
    <div class="action-icon green">
      <i class="fas fa-box"></i>
    </div>
    <div class="action-content">
      <div class="action-label"><?= $t['manage'] ?></div>
      <div class="action-title"><?= $t['products'] ?></div>
    </div>
  </a>

  <a href="<?= url('admin/emails') ?>" class="action-card purple">
    <div class="action-icon purple">
      <i class="fas fa-envelope"></i>
    </div>
    <div class="action-content">
      <div class="action-label"><?= $t['manage'] ?></div>
      <div class="action-title">Email Templates</div>
    </div>
  </a>

  <a href="<?= url('/admin/delivery') ?>" class="action-card orange">
    <div class="action-icon orange">
      <i class="fas fa-truck"></i>
    </div>
    <div class="action-content">
      <div class="action-label"><?= $t['manage'] ?></div>
      <div class="action-title"><?= $t['delivery'] ?></div>
    </div>
  </a>

  <a href="<?= url('admin/settings') ?>" class="action-card blue">
    <div class="action-icon blue">
      <i class="fas fa-cog"></i>
    </div>
    <div class="action-content">
      <div class="action-label"><?= $t['system'] ?></div>
      <div class="action-title"><?= $t['settings'] ?></div>
    </div>
  </a>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/layout.php';
?>