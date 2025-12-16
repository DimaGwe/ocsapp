<?php
/**
 * OCS Admin Sellers Management
 * File: app/Views/admin/sellers/index.php
 */

$pageTitle = 'Sellers Management';
$currentPage = 'sellers';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'sellers_management' => 'Sellers Management',
        'manage_sellers_shops' => 'Manage all registered sellers and their shops',
        'search' => 'Search',
        'search_placeholder' => 'Search by name, email, phone...',
        'status' => 'Status',
        'all_statuses' => 'All Statuses',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'total_sellers' => 'Total Sellers',
        'active_sellers' => 'Active',
        'suspended_sellers' => 'Suspended',
        'total_shops' => 'Total Shops',
        'seller' => 'Seller',
        'contact' => 'Contact',
        'shops' => 'Shops',
        'products' => 'Products',
        'joined' => 'Joined',
        'actions' => 'Actions',
        'total' => 'total',
        'active_count' => 'active',
        'view_details' => 'View Details',
        'suspend_seller' => 'Suspend Seller',
        'activate_seller' => 'Activate Seller',
        'delete_seller' => 'Delete Seller',
        'no_sellers_found' => 'No sellers found',
        'clear_filters' => 'Clear filters',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'sellers_text' => 'sellers',
        'previous' => 'Previous',
        'next' => 'Next',
        'confirm_suspend' => 'Are you sure you want to suspend this seller?',
        'confirm_activate' => 'Are you sure you want to activate this seller?',
        'confirm_delete' => 'Are you sure? This action cannot be undone!',
    ],
    'fr' => [
        'sellers_management' => 'Gestion Vendeurs',
        'manage_sellers_shops' => 'Gérer tous les vendeurs et leurs boutiques',
        'search' => 'Rechercher',
        'search_placeholder' => 'Rechercher par nom, email, téléphone...',
        'status' => 'Statut',
        'all_statuses' => 'Tous les statuts',
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'suspended' => 'Suspendu',
        'filter' => 'Filtrer',
        'reset' => 'Réinitialiser',
        'total_sellers' => 'Total Vendeurs',
        'active_sellers' => 'Actifs',
        'suspended_sellers' => 'Suspendus',
        'total_shops' => 'Total Boutiques',
        'seller' => 'Vendeur',
        'contact' => 'Contact',
        'shops' => 'Boutiques',
        'products' => 'Produits',
        'joined' => 'Inscrit',
        'actions' => 'Actions',
        'total' => 'total',
        'active_count' => 'actifs',
        'view_details' => 'Voir détails',
        'suspend_seller' => 'Suspendre',
        'activate_seller' => 'Activer',
        'delete_seller' => 'Supprimer',
        'no_sellers_found' => 'Aucun vendeur trouvé',
        'clear_filters' => 'Effacer les filtres',
        'showing' => 'Affichage',
        'to' => 'à',
        'of' => 'sur',
        'sellers_text' => 'vendeurs',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'confirm_suspend' => 'Êtes-vous sûr de vouloir suspendre ce vendeur?',
        'confirm_activate' => 'Êtes-vous sûr de vouloir activer ce vendeur?',
        'confirm_delete' => 'Êtes-vous sûr? Cette action est irréversible!',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .sellers-header {
    margin-bottom: 32px;
  }

  .sellers-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .sellers-header p {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Filters Card */
  .filters-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr auto;
    gap: 16px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
  }

  .form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-input,
  .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .btn-filter {
    align-self: flex-end;
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    white-space: nowrap;
  }

  .btn-filter:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Stats Grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
  }

  .stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    box-shadow: var(--shadow-sm);
    transition: all var(--transition-base);
  }

  .stat-card:hover {
    box-shadow: var(--shadow-md);
    transform: translateY(-2px);
  }

  .stat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .stat-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-600);
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
  .stat-icon.green { background: #dcfce7; color: #22c55e; }
  .stat-icon.red { background: #fee2e2; color: #ef4444; }
  .stat-icon.purple { background: #f3e8ff; color: #a855f7; }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    margin-top: 12px;
  }

  .stat-value.blue { color: #3b82f6; }
  .stat-value.green { color: #22c55e; }
  .stat-value.red { color: #ef4444; }
  .stat-value.purple { color: #a855f7; }

  /* Table Card */
  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

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

  th.text-right {
    text-align: right;
  }

  td {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    font-size: 14px;
  }

  td.text-right {
    text-align: right;
  }

  tbody tr {
    transition: background var(--transition-base);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .seller-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .seller-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    flex-shrink: 0;
  }

  .seller-info {
    min-width: 0;
  }

  .seller-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .seller-id {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .contact-cell {
    min-width: 0;
  }

  .contact-email {
    color: var(--dark);
    font-size: 14px;
  }

  .contact-phone {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .shops-cell {
    min-width: 0;
  }

  .shops-total {
    color: var(--dark);
    font-size: 14px;
  }

  .shops-active {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .products-cell {
    color: var(--dark);
    font-size: 14px;
  }

  /* Badges */
  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.status-pending { background: #fef3c7; color: #92400e; }
  .badge.status-active { background: #dcfce7; color: #166534; }
  .badge.status-suspended { background: #fee2e2; color: #991b1b; }
  .badge.status-inactive { background: var(--gray-200); color: var(--gray-700); }

  .date-cell {
    font-size: 13px;
    color: var(--gray-500);
  }

  /* Action Buttons */
  .action-buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
  }

  .action-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    transition: color var(--transition-base);
    padding: 4px;
  }

  .action-btn.view { color: var(--primary); }
  .action-btn.view:hover { color: var(--primary-600); }

  .action-btn.suspend { color: #f59e0b; }
  .action-btn.suspend:hover { color: #d97706; }

  .action-btn.activate { color: #22c55e; }
  .action-btn.activate:hover { color: #16a34a; }

  .action-btn.delete { color: #ef4444; }
  .action-btn.delete:hover { color: #dc2626; }

  /* Empty State */
  .empty-state {
    padding: 64px 24px;
    text-align: center;
  }

  .empty-state-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 16px;
  }

  .empty-state-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-500);
    margin-bottom: 8px;
  }

  .empty-state-link {
    display: inline-block;
    margin-top: 8px;
    color: var(--primary);
    font-size: 14px;
    text-decoration: none;
    transition: color var(--transition-base);
  }

  .empty-state-link:hover {
    color: var(--primary-600);
  }

  /* Pagination */
  .pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    background: var(--gray-50);
    border-top: 1px solid var(--border);
  }

  .pagination-info {
    font-size: 14px;
    color: var(--gray-700);
  }

  .pagination-buttons {
    display: flex;
    gap: 8px;
  }

  .pagination-btn {
    padding: 8px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    background: white;
    text-decoration: none;
    transition: all var(--transition-base);
  }

  .pagination-btn:hover {
    background: var(--gray-50);
    border-color: var(--gray-300);
  }

  .pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
  }

  .pagination-btn.active:hover {
    background: var(--primary-600);
    border-color: var(--primary-600);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .filters-grid {
      grid-template-columns: 1fr;
    }

    .btn-filter {
      width: 100%;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    .stats-grid {
      grid-template-columns: 1fr;
    }

    th, td {
      padding: 12px 16px;
    }

    .seller-cell {
      gap: 12px;
    }

    .seller-avatar {
      width: 36px;
      height: 36px;
      font-size: 14px;
    }

    .pagination {
      flex-direction: column;
      gap: 16px;
    }

    .pagination-buttons {
      flex-wrap: wrap;
      justify-content: center;
    }
  }
</style>

<!-- Page Header -->
<div class="sellers-header">
  <h1><?= $t['sellers_management'] ?></h1>
  <p><?= $t['manage_sellers_shops'] ?></p>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/sellers') ?>">
    <div class="filters-grid">
      <!-- Search -->
      <div class="form-group">
        <label class="form-label"><?= $t['search'] ?></label>
        <input 
          type="text" 
          name="search" 
          value="<?= htmlspecialchars($search ?? '') ?>"
          placeholder="<?= $t['search_placeholder'] ?>" 
          class="form-input"
        >
      </div>

      <!-- Status Filter -->
      <div class="form-group">
        <label class="form-label"><?= $t['status'] ?></label>
        <select name="status" class="form-select">
          <option value=""><?= $t['all_statuses'] ?></option>
          <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>>⏳ Pending Approval</option>
          <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>><?= $t['active'] ?></option>
          <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>><?= $t['suspended'] ?></option>
          <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>><?= $t['inactive'] ?></option>
        </select>
      </div>

      <!-- Filter Button -->
      <div class="form-group">
        <label class="form-label" style="visibility: hidden;">Action</label>
        <button type="submit" class="btn-filter">
          <i class="fas fa-filter"></i> <?= $t['filter'] ?>
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_sellers'] ?></span>
      <div class="stat-icon blue">
        <i class="fas fa-store"></i>
      </div>
    </div>
    <div class="stat-value blue"><?= number_format($total ?? 0) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['active_sellers'] ?></span>
      <div class="stat-icon green">
        <i class="fas fa-check-circle"></i>
      </div>
    </div>
    <div class="stat-value green">
      <?= number_format(count(array_filter($sellers ?? [], fn($s) => $s['status'] === 'active'))) ?>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['suspended_sellers'] ?></span>
      <div class="stat-icon red">
        <i class="fas fa-ban"></i>
      </div>
    </div>
    <div class="stat-value red">
      <?= number_format(count(array_filter($sellers ?? [], fn($s) => $s['status'] === 'suspended'))) ?>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_shops'] ?></span>
      <div class="stat-icon purple">
        <i class="fas fa-store-alt"></i>
      </div>
    </div>
    <div class="stat-value purple">
      <?= number_format(array_sum(array_column($sellers ?? [], 'shop_count'))) ?>
    </div>
  </div>
</div>

<!-- Sellers Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['seller'] ?></th>
          <th><?= $t['contact'] ?></th>
          <th><?= $t['shops'] ?></th>
          <th><?= $t['products'] ?></th>
          <th><?= $t['status'] ?></th>
          <th><?= $t['joined'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($sellers)): ?>
          <?php foreach ($sellers as $seller): ?>
            <tr>
              <!-- Seller Info -->
              <td>
                <div class="seller-cell">
                  <div class="seller-avatar">
                    <?= strtoupper(substr($seller['first_name'], 0, 1)) ?>
                  </div>
                  <div class="seller-info">
                    <div class="seller-name">
                      <?= htmlspecialchars($seller['first_name'] . ' ' . $seller['last_name']) ?>
                    </div>
                    <div class="seller-id">ID: <?= $seller['id'] ?></div>
                  </div>
                </div>
              </td>

              <!-- Contact -->
              <td>
                <div class="contact-cell">
                  <div class="contact-email"><?= htmlspecialchars($seller['email']) ?></div>
                  <?php if (!empty($seller['phone'])): ?>
                    <div class="contact-phone"><?= htmlspecialchars($seller['phone']) ?></div>
                  <?php endif; ?>
                </div>
              </td>

              <!-- Shops -->
              <td>
                <div class="shops-cell">
                  <div class="shops-total"><?= $seller['shop_count'] ?> <?= $t['total'] ?></div>
                  <div class="shops-active"><?= $seller['active_shops'] ?> <?= $t['active_count'] ?></div>
                </div>
              </td>

              <!-- Products -->
              <td>
                <div class="products-cell">
                  <?= number_format($seller['product_count']) ?>
                </div>
              </td>

              <!-- Status -->
              <td>
                <?php
                $statusClass = 'status-' . ($seller['status'] ?? 'inactive');
                $statusText = $t[strtolower($seller['status'])] ?? ucfirst($seller['status']);
                ?>
                <span class="badge <?= $statusClass ?>">
                  <?= $statusText ?>
                </span>
              </td>

              <!-- Joined Date -->
              <td class="date-cell">
                <?= formatDate($seller['created_at'], 'M d, Y') ?>
              </td>

              <!-- Actions -->
              <td class="text-right">
                <div class="action-buttons">
                  <!-- View -->
                  <a 
                    href="<?= url('admin/sellers/view?id=' . $seller['id']) ?>" 
                    class="action-btn view"
                    title="<?= $t['view_details'] ?>"
                  >
                    <i class="fas fa-eye"></i>
                  </a>

                  <!-- Suspend/Activate -->
                  <?php if ($seller['status'] === 'active'): ?>
                    <form method="POST" action="<?= url('admin/sellers/suspend') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_suspend'] ?>')">
                      <?= csrfField() ?>
                      <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
                      <button 
                        type="submit" 
                        class="action-btn suspend"
                        title="<?= $t['suspend_seller'] ?>"
                      >
                        <i class="fas fa-ban"></i>
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="POST" action="<?= url('admin/sellers/activate') ?>" style="display: inline;" onsubmit="return confirm('<?= $t['confirm_activate'] ?>')">
                      <?= csrfField() ?>
                      <input type="hidden" name="seller_id" value="<?= $seller['id'] ?>">
                      <button 
                        type="submit" 
                        class="action-btn activate"
                        title="<?= $t['activate_seller'] ?>"
                      >
                        <i class="fas fa-check-circle"></i>
                      </button>
                    </form>
                  <?php endif; ?>

                  <!-- Delete -->
                  <form method="POST" action="<?= url('admin/users/delete') ?>" 
      style="display: inline;" 
      onsubmit="return confirm('⚠️ PERMANENT DELETE\n\nThis will COMPLETELY remove:\n- User account\n- All their shops\n- All their orders\n- All related data\n\nThis CANNOT be undone!\n\nAre you absolutely sure?')">
    <?= csrfField() ?>
    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
    <button type="submit" class="action-btn delete" title="Delete User">
        <i class="fas fa-trash"></i>
    </button>
</form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-store"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_sellers_found'] ?></div>
                <?php if (($search ?? '') || ($statusFilter ?? '')): ?>
                  <a href="<?= url('admin/sellers') ?>" class="empty-state-link">
                    <?= $t['clear_filters'] ?>
                  </a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  <?php if (($total ?? 0) > ($perPage ?? 20)): ?>
    <div class="pagination">
      <div class="pagination-info">
        <?= $t['showing'] ?> <?= (($page - 1) * $perPage) + 1 ?> <?= $t['to'] ?> <?= min($page * $perPage, $total) ?> <?= $t['of'] ?> <?= $total ?> <?= $t['sellers_text'] ?>
      </div>
      <div class="pagination-buttons">
        <?php
        $totalPages = ceil($total / $perPage);
        $queryParams = http_build_query(array_filter(['search' => $search ?? '', 'status' => $statusFilter ?? '']));
        $queryPrefix = $queryParams ? '&' : '';
        ?>
        
        <?php if ($page > 1): ?>
          <a href="<?= url('admin/sellers?page=' . ($page - 1) . $queryPrefix . $queryParams) ?>" class="pagination-btn">
            <?= $t['previous'] ?>
          </a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a 
            href="<?= url('admin/sellers?page=' . $i . $queryPrefix . $queryParams) ?>" 
            class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="<?= url('admin/sellers?page=' . ($page + 1) . $queryPrefix . $queryParams) ?>" class="pagination-btn">
            <?= $t['next'] ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>