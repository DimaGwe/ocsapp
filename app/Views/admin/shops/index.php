<?php
/**
 * OCS Admin Shops Management
 * File: app/Views/admin/shops/index.php
 */

$pageTitle = 'Shops Management';
$currentPage = 'shops';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'shops_management' => 'Shops Management',
        'review_manage_shops' => 'Review and manage seller shops',
        'search' => 'Search',
        'search_placeholder' => 'Shop name, email, seller...',
        'status' => 'Status',
        'all_shops' => 'All Shops',
        'pending_approval' => 'Pending Approval',
        'approved_active' => 'Approved & Active',
        'inactive' => 'Inactive',
        'filter' => 'Filter',
        'reset' => 'Reset',
        'shop' => 'Shop',
        'seller' => 'Seller',
        'products' => 'Products',
        'created' => 'Created',
        'actions' => 'Actions',
        'pending' => 'Pending',
        'active' => 'Active',
        'view_details' => 'View Details',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'activate' => 'Activate',
        'deactivate' => 'Deactivate',
        'delete' => 'Delete',
        'no_shops_found' => 'No shops found',
        'clear_filters' => 'Clear filters',
        'showing' => 'Showing',
        'to' => 'to',
        'of' => 'of',
        'shops_text' => 'shops',
        'previous' => 'Previous',
        'next' => 'Next',
        'shop_details' => 'Shop Details',
        'slug' => 'Slug',
        'email' => 'Email',
        'phone' => 'Phone',
        'description' => 'Description',
        'no_description' => 'No description',
        'address' => 'Address',
        'delivery_settings' => 'Delivery Settings',
        'delivery_radius' => 'Delivery Radius',
        'base_fee' => 'Base Fee',
        'per_km_fee' => 'Per KM Fee',
        'packaging_time' => 'Packaging Time',
        'reject_shop' => 'Reject Shop',
        'rejection_reason' => 'Rejection Reason',
        'rejection_placeholder' => 'Please provide a reason for rejection...',
        'cancel' => 'Cancel',
        'confirm_approve' => 'Approve this shop?',
        'confirm_activate' => 'Activate this shop?',
        'confirm_deactivate' => 'Deactivate this shop?',
        'confirm_delete' => 'Are you sure? This will permanently delete the shop!',
    ],
    'fr' => [
        'shops_management' => 'Gestion Boutiques',
        'review_manage_shops' => 'Examiner et gérer les boutiques',
        'search' => 'Rechercher',
        'search_placeholder' => 'Nom boutique, email, vendeur...',
        'status' => 'Statut',
        'all_shops' => 'Toutes les boutiques',
        'pending_approval' => 'En attente',
        'approved_active' => 'Approuvé & Actif',
        'inactive' => 'Inactif',
        'filter' => 'Filtrer',
        'reset' => 'Réinitialiser',
        'shop' => 'Boutique',
        'seller' => 'Vendeur',
        'products' => 'Produits',
        'created' => 'Créé',
        'actions' => 'Actions',
        'pending' => 'En attente',
        'active' => 'Actif',
        'view_details' => 'Voir détails',
        'approve' => 'Approuver',
        'reject' => 'Rejeter',
        'activate' => 'Activer',
        'deactivate' => 'Désactiver',
        'delete' => 'Supprimer',
        'no_shops_found' => 'Aucune boutique trouvée',
        'clear_filters' => 'Effacer les filtres',
        'showing' => 'Affichage',
        'to' => 'à',
        'of' => 'sur',
        'shops_text' => 'boutiques',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'shop_details' => 'Détails Boutique',
        'slug' => 'Slug',
        'email' => 'Email',
        'phone' => 'Téléphone',
        'description' => 'Description',
        'no_description' => 'Pas de description',
        'address' => 'Adresse',
        'delivery_settings' => 'Paramètres Livraison',
        'delivery_radius' => 'Rayon Livraison',
        'base_fee' => 'Frais de base',
        'per_km_fee' => 'Frais par KM',
        'packaging_time' => 'Temps préparation',
        'reject_shop' => 'Rejeter Boutique',
        'rejection_reason' => 'Raison du rejet',
        'rejection_placeholder' => 'Veuillez fournir une raison pour le rejet...',
        'cancel' => 'Annuler',
        'confirm_approve' => 'Approuver cette boutique?',
        'confirm_activate' => 'Activer cette boutique?',
        'confirm_deactivate' => 'Désactiver cette boutique?',
        'confirm_delete' => 'Êtes-vous sûr? Cela supprimera définitivement la boutique!',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  /* Page Header */
  .shops-header {
    margin-bottom: 32px;
  }

  .shops-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .shops-header p {
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

  .form-input.with-icon {
    padding-left: 40px;
  }

  .form-input:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .input-wrapper {
    position: relative;
  }

  .input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    font-size: 14px;
  }

  .filters-actions {
    display: flex;
    gap: 8px;
    align-self: flex-end;
  }

  .btn-filter {
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

  .btn-reset {
    padding: 10px 16px;
    background: var(--gray-200);
    color: var(--gray-700);
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
  }

  .btn-reset:hover {
    background: var(--gray-300);
  }

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

  .shop-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .shop-icon {
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: #dcfce7;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
  }

  .shop-info {
    min-width: 0;
  }

  .shop-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .shop-slug {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .seller-cell {
    min-width: 0;
  }

  .seller-name {
    color: var(--dark);
    font-size: 14px;
  }

  .seller-email {
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
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.status-pending { background: #fef3c7; color: #92400e; }
  .badge.status-active { background: #dcfce7; color: #166534; }
  .badge.status-inactive { background: #fee2e2; color: #991b1b; }

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

  .action-btn.approve { color: #22c55e; }
  .action-btn.approve:hover { color: #16a34a; }

  .action-btn.reject { color: #ef4444; }
  .action-btn.reject:hover { color: #dc2626; }

  .action-btn.activate { color: #22c55e; }
  .action-btn.activate:hover { color: #16a34a; }

  .action-btn.deactivate { color: #f59e0b; }
  .action-btn.deactivate:hover { color: #d97706; }

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
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

  /* Modal */
  .modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
  }

  .modal-overlay.hidden {
    display: none;
  }

  .modal-content {
    background: white;
    border-radius: var(--radius-xl);
    max-width: 700px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
  }

  .modal-header {
    padding: 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
  }

  .modal-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
  }

  .modal-close {
    background: none;
    border: none;
    color: var(--gray-400);
    font-size: 20px;
    cursor: pointer;
    transition: color var(--transition-base);
    padding: 4px;
  }

  .modal-close:hover {
    color: var(--gray-600);
  }

  .modal-body {
    padding: 24px;
  }

  .detail-section {
    margin-bottom: 24px;
  }

  .detail-section:last-child {
    margin-bottom: 0;
  }

  .section-title {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 16px;
  }

  .detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
  }

  .detail-item {
    min-width: 0;
  }

  .detail-label {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 4px;
  }

  .detail-value {
    font-size: 14px;
    font-weight: 600;
    color: var(--dark);
  }

  .detail-description {
    font-size: 14px;
    color: var(--dark);
    line-height: 1.5;
  }

  .section-divider {
    border-top: 1px solid var(--border);
    padding-top: 24px;
  }

  /* Reject Modal */
  .reject-modal-content {
    max-width: 500px;
  }

  .form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    min-height: 100px;
    transition: all var(--transition-base);
  }

  .form-textarea:focus {
    outline: none;
    border-color: #ef4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
  }

  .modal-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
  }

  .btn-secondary {
    flex: 1;
    padding: 10px 24px;
    background: white;
    color: var(--gray-700);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-secondary:hover {
    background: var(--gray-50);
  }

  .btn-danger {
    flex: 1;
    padding: 10px 24px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-danger:hover {
    background: #dc2626;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .filters-grid {
      grid-template-columns: 1fr;
    }

    .filters-actions {
      width: 100%;
    }

    .btn-filter,
    .btn-reset {
      flex: 1;
      justify-content: center;
    }
  }

  @media (max-width: 768px) {
    th, td {
      padding: 12px 16px;
    }

    .shop-cell {
      gap: 12px;
    }

    .shop-icon {
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

    .detail-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<!-- Page Header -->
<div class="shops-header">
  <h1><?= $t['shops_management'] ?></h1>
  <p><?= $t['review_manage_shops'] ?></p>
</div>

<!-- Filters & Search -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/shops') ?>">
    <div class="filters-grid">
      <!-- Search -->
      <div class="form-group">
        <label class="form-label"><?= $t['search'] ?></label>
        <div class="input-wrapper">
          <input 
            type="search" 
            name="search" 
            value="<?= htmlspecialchars($search ?? '') ?>"
            placeholder="<?= $t['search_placeholder'] ?>" 
            class="form-input with-icon"
          >
          <i class="fas fa-search input-icon"></i>
        </div>
      </div>

      <!-- Status Filter -->
      <div class="form-group">
        <label class="form-label"><?= $t['status'] ?></label>
        <select name="status" class="form-select">
          <option value=""><?= $t['all_shops'] ?></option>
          <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>><?= $t['pending_approval'] ?></option>
          <option value="approved" <?= ($statusFilter ?? '') === 'approved' ? 'selected' : '' ?>><?= $t['approved_active'] ?></option>
          <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>><?= $t['inactive'] ?></option>
        </select>
      </div>

      <!-- Actions -->
      <div class="filters-actions">
        <button type="submit" class="btn-filter">
          <i class="fas fa-filter"></i> <?= $t['filter'] ?>
        </button>
        <a href="<?= url('admin/shops') ?>" class="btn-reset">
          <i class="fas fa-redo"></i>
        </a>
      </div>
    </div>
  </form>
</div>

<!-- Shops Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['shop'] ?></th>
          <th><?= $t['seller'] ?></th>
          <th><?= $t['products'] ?></th>
          <th><?= $t['status'] ?></th>
          <th><?= $t['created'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($shops)): ?>
          <?php foreach ($shops as $shop): ?>
            <tr>
              <td>
                <div class="shop-cell">
                  <div class="shop-icon">
                    <i class="fas fa-store"></i>
                  </div>
                  <div class="shop-info">
                    <div class="shop-name"><?= htmlspecialchars($shop['name']) ?></div>
                    <div class="shop-slug"><?= htmlspecialchars($shop['slug']) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="seller-cell">
                  <div class="seller-name">
                    <?= htmlspecialchars($shop['seller_first_name'] . ' ' . $shop['seller_last_name']) ?>
                  </div>
                  <div class="seller-email">
                    <?= htmlspecialchars($shop['seller_email']) ?>
                  </div>
                </div>
              </td>
              <td>
                <div class="products-cell">
                  <?= number_format($shop['products_count']) ?>
                </div>
              </td>
              <td>
                <?php if (!$shop['is_approved']): ?>
                  <span class="badge status-pending">
                    <i class="fas fa-clock"></i> <?= $t['pending'] ?>
                  </span>
                <?php elseif ($shop['is_approved'] && $shop['is_active']): ?>
                  <span class="badge status-active">
                    <i class="fas fa-check-circle"></i> <?= $t['active'] ?>
                  </span>
                <?php else: ?>
                  <span class="badge status-inactive">
                    <i class="fas fa-times-circle"></i> <?= $t['inactive'] ?>
                  </span>
                <?php endif; ?>
              </td>
              <td class="date-cell">
                <?= formatDate($shop['created_at'], 'M d, Y') ?>
              </td>
              <td class="text-right">
                <div class="action-buttons">
                  <!-- View Details -->
                  <button 
                    onclick="viewShopDetails(<?= htmlspecialchars(json_encode($shop), ENT_QUOTES) ?>)"
                    class="action-btn view"
                    title="<?= $t['view_details'] ?>"
                  >
                    <i class="fas fa-eye"></i>
                  </button>

                  <!-- Approve/Reject -->
                  <?php if (!$shop['is_approved']): ?>
                    <form method="POST" action="<?= url('admin/shops/approve') ?>" style="display: inline;">
                      <?= csrfField() ?>
                      <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>">
                      <button 
                        type="submit"
                        class="action-btn approve"
                        title="<?= $t['approve'] ?>"
                        onclick="return confirm('<?= $t['confirm_approve'] ?>')"
                      >
                        <i class="fas fa-check"></i>
                      </button>
                    </form>
                    <button 
                      onclick="showRejectModal(<?= $shop['id'] ?>)"
                      class="action-btn reject"
                      title="<?= $t['reject'] ?>"
                    >
                      <i class="fas fa-times"></i>
                    </button>
                  <?php endif; ?>

                  <!-- Activate/Deactivate -->
                  <?php if ($shop['is_approved']): ?>
                    <?php if ($shop['is_active']): ?>
                      <form method="POST" action="<?= url('admin/shops/deactivate') ?>" style="display: inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>">
                        <button 
                          type="submit"
                          class="action-btn deactivate"
                          title="<?= $t['deactivate'] ?>"
                          onclick="return confirm('<?= $t['confirm_deactivate'] ?>')"
                        >
                          <i class="fas fa-ban"></i>
                        </button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="<?= url('admin/shops/activate') ?>" style="display: inline;">
                        <?= csrfField() ?>
                        <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>">
                        <button 
                          type="submit"
                          class="action-btn activate"
                          title="<?= $t['activate'] ?>"
                          onclick="return confirm('<?= $t['confirm_activate'] ?>')"
                        >
                          <i class="fas fa-check-circle"></i>
                        </button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>

                  <!-- Delete -->
                  <form method="POST" action="<?= url('admin/shops/delete') ?>" style="display: inline;">
                    <?= csrfField() ?>
                    <input type="hidden" name="shop_id" value="<?= $shop['id'] ?>">
                    <button 
                      type="submit"
                      class="action-btn delete"
                      title="<?= $t['delete'] ?>"
                      onclick="return confirm('<?= $t['confirm_delete'] ?>')"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-store"></i>
                </div>
                <div class="empty-state-title"><?= $t['no_shops_found'] ?></div>
                <?php if (($search ?? '') || ($statusFilter ?? '')): ?>
                  <a href="<?= url('admin/shops') ?>" class="empty-state-link">
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
  <?php if (!empty($shops) && ($total ?? 0) > ($perPage ?? 20)): ?>
    <div class="pagination">
      <div class="pagination-info">
        <?= $t['showing'] ?> <?= (($page - 1) * $perPage) + 1 ?> <?= $t['to'] ?> <?= min($page * $perPage, $total) ?> <?= $t['of'] ?> <?= $total ?> <?= $t['shops_text'] ?>
      </div>
      
      <div class="pagination-buttons">
        <?php
        $totalPages = ceil($total / $perPage);
        $queryParams = http_build_query(array_filter([
            'search' => $search ?? '',
            'status' => $statusFilter ?? '',
        ]));
        ?>
        
        <!-- Previous -->
        <?php if ($page > 1): ?>
          <a 
            href="<?= url('admin/shops?page=' . ($page - 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <i class="fas fa-chevron-left"></i> <?= $t['previous'] ?>
          </a>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a 
            href="<?= url('admin/shops?page=' . $i . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <!-- Next -->
        <?php if ($page < $totalPages): ?>
          <a 
            href="<?= url('admin/shops?page=' . ($page + 1) . ($queryParams ? '&' . $queryParams : '')) ?>" 
            class="pagination-btn"
          >
            <?= $t['next'] ?> <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Shop Details Modal -->
<div id="shopDetailsModal" class="modal-overlay hidden">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><?= $t['shop_details'] ?></h3>
      <button onclick="closeShopDetailsModal()" class="modal-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <div id="shopDetailsContent" class="modal-body"></div>
  </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="modal-overlay hidden">
  <div class="modal-content reject-modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><?= $t['reject_shop'] ?></h3>
      <button onclick="closeRejectModal()" class="modal-close">
        <i class="fas fa-times"></i>
      </button>
    </div>
    <form method="POST" action="<?= url('admin/shops/reject') ?>" class="modal-body">
      <?= csrfField() ?>
      <input type="hidden" name="shop_id" id="rejectShopId">
      <div class="form-group">
        <label class="form-label"><?= $t['rejection_reason'] ?></label>
        <textarea 
          name="reason" 
          required
          placeholder="<?= $t['rejection_placeholder'] ?>"
          class="form-textarea"
        ></textarea>
      </div>
      <div class="modal-actions">
        <button 
          type="button"
          onclick="closeRejectModal()"
          class="btn-secondary"
        >
          <?= $t['cancel'] ?>
        </button>
        <button 
          type="submit"
          class="btn-danger"
        >
          <?= $t['reject'] ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function viewShopDetails(shop) {
    const modal = document.getElementById('shopDetailsModal');
    const content = document.getElementById('shopDetailsContent');
    
    content.innerHTML = `
        <div class="detail-section">
            <h4 class="section-title">${shop.name}</h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <p class="detail-label"><?= $t['slug'] ?></p>
                    <p class="detail-value">${shop.slug}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['seller'] ?></p>
                    <p class="detail-value">${shop.seller_first_name} ${shop.seller_last_name}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['email'] ?></p>
                    <p class="detail-value">${shop.email || 'N/A'}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['phone'] ?></p>
                    <p class="detail-value">${shop.phone}</p>
                </div>
            </div>
        </div>
        <div class="detail-section">
            <p class="detail-label"><?= $t['description'] ?></p>
            <p class="detail-description">${shop.description || '<?= $t['no_description'] ?>'}</p>
        </div>
        <div class="detail-section">
            <p class="detail-label"><?= $t['address'] ?></p>
            <p class="detail-description">${shop.address}</p>
        </div>
        <div class="detail-section section-divider">
            <h5 class="section-title"><?= $t['delivery_settings'] ?></h5>
            <div class="detail-grid">
                <div class="detail-item">
                    <p class="detail-label"><?= $t['delivery_radius'] ?></p>
                    <p class="detail-value">${shop.delivery_radius} km</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['base_fee'] ?></p>
                    <p class="detail-value"><?= env('APP_CURRENCY') ?>${shop.base_delivery_fee}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['per_km_fee'] ?></p>
                    <p class="detail-value"><?= env('APP_CURRENCY') ?>${shop.per_km_fee}</p>
                </div>
                <div class="detail-item">
                    <p class="detail-label"><?= $t['packaging_time'] ?></p>
                    <p class="detail-value">${shop.packaging_time} min</p>
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeShopDetailsModal() {
    document.getElementById('shopDetailsModal').classList.add('hidden');
}

function showRejectModal(shopId) {
    document.getElementById('rejectShopId').value = shopId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modals on outside click
document.getElementById('shopDetailsModal').addEventListener('click', function(e) {
    if (e.target === this) closeShopDetailsModal();
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>