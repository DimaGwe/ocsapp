<?php
/**
 * OCS Admin Users Management
 * File: app/Views/admin/users/index.php
 */

$pageTitle = $pageTitle ?? 'Users Management';
$currentPage = $currentPage ?? 'users';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

ob_start();
?>

<style>
  /* Page Header */
  .users-header {
    margin-bottom: 32px;
  }

  .users-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .users-header p {
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
    grid-template-columns: 2fr 1fr 1fr auto;
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
  .stat-icon.orange { background: #ffedd5; color: #ea580c; }

  .stat-value {
    font-size: 28px;
    font-weight: 700;
    margin-top: 12px;
  }

  .stat-value.blue { color: #3b82f6; }
  .stat-value.green { color: #22c55e; }
  .stat-value.red { color: #ef4444; }
  .stat-value.purple { color: #a855f7; }
  .stat-value.orange { color: #ea580c; }

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

  .user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .user-avatar {
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

  .user-info {
    min-width: 0;
  }

  .user-name {
    font-weight: 600;
    color: var(--dark);
    font-size: 14px;
  }

  .user-email {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 2px;
  }

  .user-company {
    font-size: 11px;
    color: #ea580c;
    margin-top: 2px;
    font-weight: 500;
  }

  /* Badges */
  .badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.role-super_admin { background: #fef3c7; color: #b45309; }
  .badge.role-admin { background: #fee2e2; color: #991b1b; }
  .badge.role-admin_staff { background: #ffedd5; color: #ea580c; }
  .badge.role-seller { background: #dbeafe; color: #1e40af; }
  .badge.role-buyer { background: #dcfce7; color: #166534; }
  .badge.role-delivery { background: #f3e8ff; color: #7c3aed; }
  .badge.role-business { background: #ccfbf1; color: #0d9488; }
  .badge.role-supplier { background: #fef3c7; color: #92400e; }
  .badge.role-advertiser { background: #fce7f3; color: #be185d; }
  .badge.role-affiliate { background: #cffafe; color: #0891b2; }

  .badge.status-active { background: #dcfce7; color: #166534; }
  .badge.status-pending { background: #fef3c7; color: #92400e; }
  .badge.status-suspended { background: #fee2e2; color: #991b1b; }
  .badge.status-inactive { background: var(--gray-200); color: var(--gray-700); }
  .badge.status-rejected { background: #fee2e2; color: #991b1b; }

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

  .action-btn.edit { color: var(--primary); }
  .action-btn.edit:hover { color: var(--primary-600); }

  .action-btn.suspend { color: #f59e0b; }
  .action-btn.suspend:hover { color: #d97706; }

  .action-btn.activate { color: #10b981; }
  .action-btn.activate:hover { color: #059669; }

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

  /* Create User Button */
  .btn-create-user {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    white-space: nowrap;
  }

  .btn-create-user:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Modal Styles */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .modal-overlay.active {
    display: flex;
  }

  .modal-content {
    background: white;
    border-radius: var(--radius-xl);
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
  }

  .modal-header {
    padding: 24px;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .modal-title {
    font-size: 20px;
    font-weight: 700;
    color: var(--dark);
  }

  .modal-close {
    background: none;
    border: none;
    font-size: 24px;
    color: var(--gray-400);
    cursor: pointer;
    transition: color var(--transition-base);
    padding: 4px;
  }

  .modal-close:hover {
    color: var(--dark);
  }

  .modal-body {
    padding: 24px;
  }

  .modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
  }

  .btn-cancel {
    padding: 10px 20px;
    background: var(--gray-100);
    color: var(--gray-700);
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-cancel:hover {
    background: var(--gray-200);
  }

  .btn-submit {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .btn-submit:hover {
    background: var(--primary-600);
  }

  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  @media (max-width: 480px) {
    .form-row {
      grid-template-columns: 1fr;
    }
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
  }

  .checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: var(--primary);
  }

  .checkbox-group label {
    font-size: 14px;
    color: var(--gray-700);
  }

  .help-text {
    font-size: 12px;
    color: var(--gray-500);
    margin-top: 4px;
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .filters-grid {
      grid-template-columns: 1fr 1fr;
    }

    .btn-filter {
      width: 100%;
      justify-content: center;
    }

    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .users-header {
      margin-bottom: 20px;
    }

    .users-header h1 {
      font-size: 22px;
    }

    .filters-grid {
      grid-template-columns: 1fr;
    }

    .stats-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }

    .stat-card {
      padding: 16px;
    }

    .stat-value {
      font-size: 22px;
    }

    .stat-icon {
      width: 40px;
      height: 40px;
      font-size: 16px;
    }

    th, td {
      padding: 12px 12px;
    }

    .hide-mobile {
      display: none;
    }

    .user-cell {
      gap: 10px;
    }

    .user-avatar {
      width: 36px;
      height: 36px;
      font-size: 14px;
    }

    .user-name {
      font-size: 13px;
    }

    .user-email {
      font-size: 11px;
    }

    .action-buttons {
      gap: 8px;
    }

    .pagination {
      flex-direction: column;
      gap: 16px;
    }

    .pagination-buttons {
      flex-wrap: wrap;
      justify-content: center;
    }

    .btn-create-user {
      padding: 10px 16px;
      font-size: 13px;
    }
  }

  @media (max-width: 480px) {
    .users-header {
      flex-direction: column;
      align-items: stretch;
    }

    .btn-create-user {
      width: 100%;
      justify-content: center;
    }

    .stats-grid {
      grid-template-columns: 1fr 1fr;
      gap: 8px;
    }

    .stat-card {
      padding: 12px;
    }

    .stat-value {
      font-size: 20px;
      margin-top: 8px;
    }

    .stat-label {
      font-size: 11px;
    }

    .stat-icon {
      width: 32px;
      height: 32px;
      font-size: 14px;
    }

    th, td {
      padding: 10px 8px;
      font-size: 12px;
    }

    th {
      font-size: 10px;
    }

    .badge {
      padding: 3px 8px;
      font-size: 11px;
    }

    .modal-content {
      margin: 10px;
    }

    .form-row {
      grid-template-columns: 1fr;
    }
  }

  /* ── Sticky table header ─────────────────── */
  thead {
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: 0 1px 0 var(--border);
  }

  /* ── Action button touch targets ─────────── */
  .action-btn {
    min-width: 36px;
    min-height: 36px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  /* ── Clear-filters button ────────────────── */
  .btn-clear-filters {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 10px 13px;
    background: var(--gray-100);
    color: var(--gray-600);
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition-base);
    white-space: nowrap;
    line-height: 1;
  }
  .btn-clear-filters:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
  }

  /* ── Active filter input highlight ──────── */
  .form-input.filter-active,
  .form-select.filter-active {
    border-color: var(--primary);
    background: #f0fdf4;
  }

  /* ── Mobile card layout (≤ 640 px) ─────── */
  @media (max-width: 640px) {

    /* Dissolve outer card shell — cards are the rows now */
    .table-card { background: transparent; box-shadow: none; border-radius: 0; overflow: visible; }
    .table-wrapper { overflow-x: visible; }

    table, table tbody { display: block; }
    table thead { display: none; }

    /* Each row = white card */
    table tbody tr {
      display: flex;
      flex-wrap: wrap;
      align-content: flex-start;
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      margin: 0 16px 12px;
      overflow: hidden;
      box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    }

    table tbody td {
      border-top: none !important;
      padding: 0;
    }

    /* User — full-width card header */
    table tbody td.td-user {
      flex: 0 0 100%;
      padding: 14px 16px 12px;
      border-bottom: 1px solid var(--border) !important;
    }

    /* Role badge — left */
    table tbody td.td-role {
      flex: 1 1 auto;
      padding: 10px 8px 8px 16px;
      display: flex;
      align-items: center;
    }

    /* Status badge — right */
    table tbody td.td-status {
      flex: 0 0 auto;
      padding: 10px 16px 8px 8px;
      display: flex !important;
      align-items: center;
      justify-content: flex-end;
    }

    /* Phone + Joined — omitted in card view */
    table tbody td.td-phone,
    table tbody td.td-joined { display: none !important; }

    /* Actions — full-width card footer */
    table tbody td.td-actions {
      flex: 0 0 100%;
      padding: 10px 16px 14px;
      background: var(--gray-50);
      border-top: 1px solid var(--border) !important;
    }
    table tbody td.td-actions .action-buttons {
      justify-content: flex-start;
      gap: 10px;
    }
    table tbody td.td-actions .action-btn {
      min-width: 44px;
      min-height: 44px;
      font-size: 16px;
      border-radius: 10px;
    }
    table tbody td.td-actions .action-btn.edit     { background: #f0fdf4; }
    table tbody td.td-actions .action-btn.suspend  { background: #fffbeb; }
    table tbody td.td-actions .action-btn.activate { background: #f0fdf4; }
    table tbody td.td-actions .action-btn.delete   { background: #fef2f2; }

    /* Empty-state cell spans full width */
    table tbody td[colspan] {
      flex: 0 0 100%;
      display: block;
    }

    /* Pagination sits inside the now-transparent table-card */
    .pagination {
      background: white;
      border: 1px solid var(--border);
      border-radius: 12px;
      margin: 0 16px 16px;
    }
  }
</style>

<!-- Page Header -->
<div class="users-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
  <div>
    <h1><?= $currentLang === 'fr' ? 'Gestion des utilisateurs' : 'Users Management' ?></h1>
    <p><?= $currentLang === 'fr' ? 'Gérez tous les utilisateurs inscrits et leurs comptes' : 'Manage all registered users and their accounts' ?></p>
  </div>
  <button type="button" class="btn-create-user" onclick="openCreateModal()">
    <i class="fas fa-plus"></i> <?= $currentLang === 'fr' ? 'Créer un utilisateur' : 'Create User' ?>
  </button>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/users') ?>">
    <div class="filters-grid">
      <!-- Search -->
      <div class="form-group">
        <label class="form-label"><?= $currentLang === 'fr' ? 'Rechercher' : 'Search' ?></label>
        <input
          type="text"
          name="search"
          value="<?= htmlspecialchars($search ?? '') ?>"
          placeholder="<?= $currentLang === 'fr' ? 'Rechercher par nom, courriel...' : 'Search by name, email...' ?>"
          class="form-input <?= !empty($search) ? 'filter-active' : '' ?>"
        >
      </div>

      <!-- Role Filter -->
      <div class="form-group">
        <label class="form-label"><?= $currentLang === 'fr' ? 'Rôle' : 'Role' ?></label>
        <select name="role" class="form-select <?= !empty($roleFilter) ? 'filter-active' : '' ?>">
          <option value=""><?= $currentLang === 'fr' ? 'Tous les rôles' : 'All Roles' ?></option>
          <?php foreach ($roles ?? [] as $role): ?>
            <option value="<?= $role['name'] ?>" <?= ($roleFilter ?? '') === $role['name'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($role['display_name'] ?: ucfirst(str_replace('_', ' ', $role['name']))) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Status Filter -->
      <div class="form-group">
        <label class="form-label"><?= $currentLang === 'fr' ? 'Statut' : 'Status' ?></label>
        <select name="status" class="form-select <?= !empty($statusFilter) ? 'filter-active' : '' ?>">
          <option value=""><?= $currentLang === 'fr' ? 'Tous les statuts' : 'All Statuses' ?></option>
          <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Actif' : 'Active' ?></option>
          <option value="pending" <?= ($statusFilter ?? '') === 'pending' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'En attente' : 'Pending' ?></option>
          <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Suspendu' : 'Suspended' ?></option>
          <option value="inactive" <?= ($statusFilter ?? '') === 'inactive' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Inactif' : 'Inactive' ?></option>
          <option value="rejected" <?= ($statusFilter ?? '') === 'rejected' ? 'selected' : '' ?>><?= $currentLang === 'fr' ? 'Rejeté' : 'Rejected' ?></option>
        </select>
      </div>

      <!-- Filter Button -->
      <div class="form-group">
        <label class="form-label" style="visibility: hidden;">Action</label>
        <div style="display: flex; gap: 8px;">
          <button type="submit" class="btn-filter" style="flex: 1;">
            <i class="fas fa-filter"></i> <?= $currentLang === 'fr' ? 'Filtrer' : 'Filter' ?>
          </button>
          <?php if (($search ?? '') || ($roleFilter ?? '') || ($statusFilter ?? '')): ?>
          <a href="<?= url('admin/users') ?>" class="btn-clear-filters" title="<?= $currentLang === 'fr' ? 'Effacer les filtres' : 'Clear filters' ?>">
            <i class="fas fa-times"></i>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </form>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $currentLang === 'fr' ? 'Total utilisateurs' : 'Total Users' ?></span>
      <div class="stat-icon blue">
        <i class="fas fa-users"></i>
      </div>
    </div>
    <div class="stat-value blue"><?= number_format($total ?? 0) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $currentLang === 'fr' ? 'Utilisateurs actifs' : 'Active Users' ?></span>
      <div class="stat-icon green">
        <i class="fas fa-check-circle"></i>
      </div>
    </div>
    <div class="stat-value green"><?= number_format($activeCount ?? 0) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $currentLang === 'fr' ? 'Vendeurs' : 'Sellers' ?></span>
      <div class="stat-icon purple">
        <i class="fas fa-store"></i>
      </div>
    </div>
    <div class="stat-value purple"><?= number_format($sellerCount ?? 0) ?></div>
  </div>

  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $currentLang === 'fr' ? 'Fournisseurs' : 'Suppliers' ?></span>
      <div class="stat-icon orange">
        <i class="fas fa-truck-loading"></i>
      </div>
    </div>
    <div class="stat-value orange"><?= number_format($supplierCount ?? 0) ?></div>
  </div>
</div>

<!-- Users Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $currentLang === 'fr' ? 'Utilisateur' : 'User' ?></th>
          <th><?= $currentLang === 'fr' ? 'Rôle' : 'Role' ?></th>
          <th class="hide-mobile"><?= $currentLang === 'fr' ? 'Téléphone' : 'Phone' ?></th>
          <th><?= $currentLang === 'fr' ? 'Statut' : 'Status' ?></th>
          <th class="hide-mobile"><?= $currentLang === 'fr' ? 'Inscrit' : 'Joined' ?></th>
          <th class="text-right"><?= $currentLang === 'fr' ? 'Actions' : 'Actions' ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($allUsers)): ?>
          <?php foreach ($allUsers as $user): ?>
            <?php $isSupplier = !empty($user['is_supplier']); ?>
            <tr>
              <!-- User Info -->
              <td class="td-user">
                <div class="user-cell">
                  <div class="user-avatar" <?php if ($isSupplier): ?>style="background: #ea580c;"<?php endif; ?>>
                    <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                  </div>
                  <div class="user-info">
                    <div class="user-name" style="display:flex;align-items:center;gap:6px;">
                      <?php
                        require_once BASE_PATH . '/app/Helpers/PresenceHelper.php';
                        echo PresenceHelper::dot($user['last_seen_at'] ?? null);
                      ?>
                      <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </div>
                    <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                    <?php if ($isSupplier && !empty($user['company_name'])): ?>
                      <div class="user-company"><?= htmlspecialchars($user['company_name']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($user['last_seen_at'])): ?>
                      <div style="font-size:11px;color:#9ca3af;margin-top:2px;">
                        <?= PresenceHelper::lastSeen($user['last_seen_at']) ?>
                        <?php if (!empty($user['current_page']) && PresenceHelper::getStatus($user['last_seen_at']) !== 'offline'): ?>
                          · <?= htmlspecialchars(parse_url($user['current_page'], PHP_URL_PATH) ?: $user['current_page']) ?>
                        <?php endif; ?>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              </td>

              <!-- Role -->
              <td class="td-role">
                <?php
                $roleClass = 'role-' . ($user['role'] ?? 'buyer');
                $roleLabels = [
                  'super_admin' => ['fr' => 'Super Admin',    'en' => 'Super Admin'],
                  'admin'       => ['fr' => 'Admin',          'en' => 'Admin'],
                  'admin_staff' => ['fr' => 'Admin - Équipe', 'en' => 'Admin Staff'],
                  'seller'      => ['fr' => 'Vendeur',        'en' => 'Seller'],
                  'buyer'       => ['fr' => 'Acheteur',       'en' => 'Buyer'],
                  'delivery'    => ['fr' => 'Livreur',        'en' => 'Driver'],
                  'business'    => ['fr' => 'Distribution',   'en' => 'Business'],
                  'supplier'    => ['fr' => 'Fournisseur',    'en' => 'Supplier'],
                  'advertiser'  => ['fr' => 'Annonceur',      'en' => 'Advertiser'],
                  'affiliate'   => ['fr' => 'Affilié',        'en' => 'Affiliate'],
                ];
                $roleKey = $user['role'] ?? 'buyer';
                $roleDisplay = $roleLabels[$roleKey][$currentLang] ?? ucfirst(str_replace('_', ' ', $roleKey));
                $deptLabels = ['ops' => ['fr' => 'Opérations', 'en' => 'Operations'], 'finance' => ['fr' => 'Finance', 'en' => 'Finance'], 'tech' => ['fr' => 'Technologie', 'en' => 'Technology'], 'support' => ['fr' => 'Soutien', 'en' => 'Support'], 'logistics' => ['fr' => 'Logistique', 'en' => 'Logistics'], 'management' => ['fr' => 'Direction', 'en' => 'Management']];
                $dept = $user['department'] ?? '';
                ?>
                <div>
                  <span class="badge <?= $roleClass ?>"><?= htmlspecialchars($roleDisplay) ?></span>
                  <?php if ($roleKey === 'admin_staff' && $dept && isset($deptLabels[$dept])): ?>
                    <div style="font-size:11px;color:#ea580c;margin-top:3px;font-weight:500;"><?= $deptLabels[$dept][$currentLang] ?></div>
                  <?php endif; ?>
                </div>
              </td>

              <!-- Phone -->
              <td class="hide-mobile td-phone">
                <?= htmlspecialchars($user['phone'] ?? 'N/A') ?>
              </td>

              <!-- Status -->
              <td class="td-status">
                <?php
                $statusClass = 'status-' . ($user['status'] ?? 'inactive');
                $statusLabels = ['active' => ['fr' => 'Actif', 'en' => 'Active'], 'pending' => ['fr' => 'En attente', 'en' => 'Pending'], 'suspended' => ['fr' => 'Suspendu', 'en' => 'Suspended'], 'inactive' => ['fr' => 'Inactif', 'en' => 'Inactive'], 'rejected' => ['fr' => 'Rejeté', 'en' => 'Rejected']];
                $statusKey = $user['status'] ?? 'inactive';
                $statusDisplay = $statusLabels[$statusKey][$currentLang] ?? ucfirst($statusKey);
                ?>
                <span class="badge <?= $statusClass ?>"><?= $statusDisplay ?></span>
              </td>

              <!-- Joined Date -->
              <td class="date-cell hide-mobile td-joined">
                <?= formatDate($user['created_at'], 'M d, Y') ?>
              </td>

              <!-- Actions -->
              <td class="text-right td-actions">
                <div class="action-buttons">
                  <?php if ($isSupplier): ?>
                    <!-- Edit Supplier -->
                    <a
                      href="<?= url('admin/suppliers/edit?id=' . $user['id']) ?>"
                      class="action-btn edit"
                      title="Edit Supplier"
                    >
                      <i class="fas fa-edit"></i>
                    </a>

                    <!-- Supplier Status Actions -->
                    <?php if ($user['status'] === 'active'): ?>
                      <button
                        type="button"
                        class="action-btn suspend"
                        title="Suspend Supplier"
                        onclick="changeSupplierStatus(<?= $user['id'] ?>, 'suspended', '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>')"
                      >
                        <i class="fas fa-pause-circle"></i>
                      </button>
                      <button
                        type="button"
                        class="action-btn delete"
                        title="Disable Supplier"
                        style="background:#fee2e2;color:#991b1b;"
                        onclick="changeSupplierStatus(<?= $user['id'] ?>, 'inactive', '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>')"
                      >
                        <i class="fas fa-ban"></i>
                      </button>
                    <?php elseif ($user['status'] === 'suspended' || $user['status'] === 'inactive'): ?>
                      <button
                        type="button"
                        class="action-btn activate"
                        title="Activate Supplier"
                        onclick="changeSupplierStatus(<?= $user['id'] ?>, 'active', '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>')"
                      >
                        <i class="fas fa-check-circle"></i>
                      </button>
                    <?php elseif ($user['status'] === 'pending_verification'): ?>
                      <button
                        type="button"
                        class="action-btn activate"
                        title="Approve Supplier"
                        onclick="changeSupplierStatus(<?= $user['id'] ?>, 'active', '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>')"
                      >
                        <i class="fas fa-check-circle"></i>
                      </button>
                      <button
                        type="button"
                        class="action-btn delete"
                        title="Reject Supplier"
                        style="background:#fee2e2;color:#991b1b;"
                        onclick="changeSupplierStatus(<?= $user['id'] ?>, 'inactive', '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>')"
                      >
                        <i class="fas fa-times-circle"></i>
                      </button>
                    <?php endif; ?>

                    <!-- Delete Supplier -->
                    <button
                      type="button"
                      class="action-btn delete"
                      title="Delete Supplier"
                      onclick="openSupplierDeleteModal(<?= $user['id'] ?>, '<?= htmlspecialchars(addslashes($user['first_name'] . ' ' . $user['last_name'])) ?>', '<?= htmlspecialchars(addslashes($user['company_name'] ?? '')) ?>')"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  <?php else: ?>
                    <!-- Edit User -->
                    <a
                      href="<?= url('admin/users/edit?id=' . $user['id']) ?>"
                      class="action-btn edit"
                      title="Edit User"
                    >
                      <i class="fas fa-edit"></i>
                    </a>

                    <!-- Status Actions -->
                    <?php if ($user['status'] === 'pending' && ($user['role'] ?? '') === 'delivery'): ?>
                      <a
                        href="<?= url('admin/delivery/staff?tab=applications') ?>"
                        class="action-btn"
                        title="View Driver Application"
                        style="background:#f3e8ff;color:#7c3aed;border-color:#e9d5ff;"
                      >
                        <i class="fas fa-id-card"></i>
                      </a>
                    <?php elseif ($user['status'] === 'active'): ?>
                      <button
                        type="button"
                        class="action-btn suspend"
                        title="Suspend User"
                        onclick="changeUserStatus(<?= $user['id'] ?>, 'suspended', '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')"
                      >
                        <i class="fas fa-ban"></i>
                      </button>
                    <?php elseif ($user['status'] === 'suspended' || $user['status'] === 'inactive'): ?>
                      <button
                        type="button"
                        class="action-btn activate"
                        title="Activate User"
                        onclick="changeUserStatus(<?= $user['id'] ?>, 'active', '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')"
                      >
                        <i class="fas fa-check-circle"></i>
                      </button>
                    <?php endif; ?>

                    <!-- Test account toggle -->
                    <form method="POST" action="<?= url('admin/users/toggle-test-account') ?>" style="display:inline;">
                      <?= csrfField() ?>
                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                      <input type="hidden" name="current_value" value="<?= $user['is_test_account'] ?? 0 ?>">
                      <button type="submit"
                        class="action-btn"
                        title="<?= ($user['is_test_account'] ?? 0) ? 'Remove test flag' : 'Mark as test account' ?>"
                        style="color:<?= ($user['is_test_account'] ?? 0) ? '#7c3aed' : '#9ca3af' ?>;font-size:13px;"
                      ><i class="fas fa-flask"></i></button>
                    </form>

                    <?php if ($user['is_test_account'] ?? 0): ?>
                    <!-- Reset test account -->
                    <form method="POST" action="<?= url('admin/users/reset-test-account') ?>" style="display:inline;" onsubmit="return confirm('Reset all data for this test account? The user row is kept but all orders, sessions, and activity are wiped.')">
                      <?= csrfField() ?>
                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                      <button type="submit"
                        class="action-btn"
                        title="Reset test account data"
                        style="color:#d97706;"
                      ><i class="fas fa-redo"></i></button>
                    </form>
                    <?php endif; ?>

                    <!-- Delete -->
                    <button
                      type="button"
                      class="action-btn delete"
                      title="Delete User"
                      onclick="openDeleteModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>')"
                    >
                      <i class="fas fa-trash"></i>
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">
              <div class="empty-state">
                <div class="empty-state-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="empty-state-title"><?= $currentLang === 'fr' ? 'Aucun utilisateur trouvé' : 'No users found' ?></div>
                <?php if (($search ?? '') || ($roleFilter ?? '') || ($statusFilter ?? '')): ?>
                  <a href="<?= url('admin/users') ?>" class="empty-state-link">
                    <?= $currentLang === 'fr' ? 'Effacer les filtres' : 'Clear filters' ?>
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
        <?php
          $from = (($page - 1) * $perPage) + 1;
          $to   = min($page * $perPage, $total);
          echo $currentLang === 'fr'
            ? "Affichage de {$from} à {$to} sur {$total} utilisateurs"
            : "Showing {$from} to {$to} of {$total} users";
        ?>
      </div>
      <div class="pagination-buttons">
        <?php
        $totalPages = ceil($total / $perPage);
        $queryParams = http_build_query(array_filter([
          'search' => $search ?? '', 
          'role' => $roleFilter ?? '',
          'status' => $statusFilter ?? ''
        ]));
        $queryPrefix = $queryParams ? '&' : '';
        ?>
        
        <?php if ($page > 1): ?>
          <a href="<?= url('admin/users?page=' . ($page - 1) . $queryPrefix . $queryParams) ?>" class="pagination-btn">
            <?= $currentLang === 'fr' ? 'Précédent' : 'Previous' ?>
          </a>
        <?php endif; ?>

        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a 
            href="<?= url('admin/users?page=' . $i . $queryPrefix . $queryParams) ?>" 
            class="pagination-btn <?= $i === $page ? 'active' : '' ?>"
          >
            <?= $i ?>
          </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
          <a href="<?= url('admin/users?page=' . ($page + 1) . $queryPrefix . $queryParams) ?>" class="pagination-btn">
            <?= $currentLang === 'fr' ? 'Suivant' : 'Next' ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
    <div style="text-align: center; margin-bottom: 24px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-exclamation-triangle" style="font-size: 32px; color: #ef4444;"></i>
      </div>
      <h3 style="font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 8px;"><?= $currentLang === 'fr' ? 'Supprimer l\'utilisateur' : 'Delete User' ?></h3>
      <p style="color: var(--gray-600); font-size: 14px;">
        <?= $currentLang === 'fr' ? 'Êtes-vous sûr de vouloir supprimer' : 'Are you sure you want to delete' ?> <strong id="deleteUserName"></strong>?
      </p>
      <p style="color: #ef4444; font-size: 13px; margin-top: 8px;">
        <?= $currentLang === 'fr' ? 'Cette action ne peut pas être annulée!' : 'This action cannot be undone!' ?>
      </p>
    </div>

    <form id="deleteForm" method="POST" action="<?= url('admin/users/delete') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="user_id" id="deleteUserId">

      <div style="margin-bottom:16px;text-align:left;">
        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
          <?= $currentLang === 'fr' ? 'Raison de la suppression' : 'Reason for deletion' ?>
        </label>
        <select name="delete_reason" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;color:#1f2937;background:#fff;">
          <option value="voluntary"><?= $currentLang === 'fr' ? 'Départ volontaire' : 'Voluntary departure' ?></option>
          <option value="inactive"><?= $currentLang === 'fr' ? 'Inactivité' : 'Inactivity' ?></option>
          <option value="terms_violation"><?= $currentLang === 'fr' ? 'Violation des conditions' : 'Terms violation' ?></option>
          <option value="business_conduct"><?= $currentLang === 'fr' ? 'Conduite commerciale' : 'Business conduct' ?></option>
          <option value="test"><?= $currentLang === 'fr' ? 'Compte de test' : 'Test account' ?></option>
          <option value="other"><?= $currentLang === 'fr' ? 'Autre' : 'Other' ?></option>
        </select>
      </div>

      <div style="margin-bottom:16px;text-align:left;">
        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">
          <?= $currentLang === 'fr' ? 'Notes (optionnel)' : 'Notes (optional)' ?>
        </label>
        <textarea name="delete_notes" rows="2" placeholder="<?= $currentLang === 'fr' ? 'Détails supplémentaires...' : 'Additional details...' ?>" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;"></textarea>
      </div>

      <div style="margin-bottom:20px;text-align:left;display:flex;align-items:center;gap:8px;">
        <input type="checkbox" name="can_rejoin" id="canRejoinCheck" value="1" checked style="width:16px;height:16px;accent-color:#ef4444;">
        <label for="canRejoinCheck" style="font-size:13px;color:#374151;cursor:pointer;">
          <?= $currentLang === 'fr' ? 'Autoriser la réinscription (décocher = compte banni)' : 'Allow re-registration (uncheck = banned account)' ?>
        </label>
      </div>

      <div style="display:flex;gap:12px;justify-content:center;">
        <button type="button" onclick="closeDeleteModal()" style="padding: 12px 24px; border-radius: 8px; border: 2px solid var(--border); background: white; color: var(--gray-700); font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <?= $currentLang === 'fr' ? 'Annuler' : 'Cancel' ?>
        </button>
        <button type="submit" style="padding: 12px 24px; border-radius: 8px; border: none; background: #ef4444; color: white; font-weight: 600; cursor: pointer; transition: all 0.2s;">
          <i class="fas fa-trash"></i> <?= $currentLang === 'fr' ? 'Supprimer' : 'Delete User' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Status Change Form (Hidden) -->
<form id="statusChangeForm" method="POST" action="<?= url('admin/users/change-status') ?>" style="display: none;">
  <?= csrfField() ?>
  <input type="hidden" name="user_id" id="statusChangeUserId">
  <input type="hidden" name="status" id="statusChangeStatus">
</form>

<!-- Supplier Status Change Form (Hidden) -->
<form id="supplierStatusForm" method="POST" action="<?= url('admin/users/supplier-change-status') ?>" style="display: none;">
  <?= csrfField() ?>
  <input type="hidden" name="supplier_id" id="supplierStatusId">
  <input type="hidden" name="status" id="supplierStatusValue">
</form>

<!-- Supplier Delete Modal -->
<div id="supplierDeleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 12px; padding: 32px; max-width: 520px; width: 90%; box-shadow: 0 20px 50px rgba(0,0,0,0.3);">
    <div style="text-align: center; margin-bottom: 24px;">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: #fee2e2; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center;">
        <i class="fas fa-exclamation-triangle" style="font-size: 32px; color: #ef4444;"></i>
      </div>
      <h3 style="font-size: 20px; font-weight: 700; color: var(--dark); margin-bottom: 8px;"><?= $currentLang === 'fr' ? 'Supprimer le fournisseur' : 'Delete Supplier' ?></h3>
      <p style="color: var(--gray-600); font-size: 14px;">
        <strong id="supplierDeleteName"></strong>
        <span id="supplierDeleteCompany" style="display: block; font-size: 13px; color: #ea580c; margin-top: 4px;"></span>
      </p>
    </div>

    <div style="display: flex; flex-direction: column; gap: 12px;">
      <!-- Disable Option -->
      <form method="POST" action="<?= url('admin/users/supplier-change-status') ?>">
        <?= csrfField() ?>
        <input type="hidden" name="supplier_id" id="supplierDisableId">
        <input type="hidden" name="status" value="inactive">
        <button type="submit" style="width: 100%; padding: 14px 20px; border-radius: 8px; border: 2px solid #f59e0b; background: #fffbeb; color: #92400e; font-weight: 600; font-size: 14px; cursor: pointer; text-align: left; transition: all 0.2s;">
          <i class="fas fa-ban" style="margin-right: 8px;"></i> Disable Account
          <span style="display: block; font-size: 12px; font-weight: 400; margin-top: 4px; color: #a16207;">
            Account will be deactivated. Data is preserved. Supplier will be notified. Reversible.
          </span>
        </button>
      </form>

      <!-- Permanent Delete Option -->
      <form method="POST" action="<?= url('admin/users/supplier-delete') ?>" id="supplierPermanentDeleteForm">
        <?= csrfField() ?>
        <input type="hidden" name="supplier_id" id="supplierPermanentDeleteId">

        <div style="margin-bottom:10px;">
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Reason / Raison</label>
          <select name="delete_reason" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;color:#1f2937;background:#fff;">
            <option value="voluntary">Voluntary departure / Départ volontaire</option>
            <option value="inactive">Inactivity / Inactivité</option>
            <option value="terms_violation">Terms violation / Violation des conditions</option>
            <option value="business_conduct">Business conduct / Conduite commerciale</option>
            <option value="test">Test account / Compte de test</option>
            <option value="other">Other / Autre</option>
          </select>
        </div>
        <div style="margin-bottom:10px;">
          <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:4px;">Notes (optional / optionnel)</label>
          <textarea name="delete_notes" rows="2" style="width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;resize:vertical;"></textarea>
        </div>
        <div style="margin-bottom:12px;display:flex;align-items:center;gap:8px;">
          <input type="checkbox" name="can_rejoin" id="supplierCanRejoin" value="1" checked style="width:15px;height:15px;accent-color:#ef4444;">
          <label for="supplierCanRejoin" style="font-size:12px;color:#374151;cursor:pointer;">Allow re-registration (uncheck = banned)</label>
        </div>

        <button type="submit" style="width: 100%; padding: 14px 20px; border-radius: 8px; border: 2px solid #ef4444; background: #fef2f2; color: #991b1b; font-weight: 600; font-size: 14px; cursor: pointer; text-align: left; transition: all 0.2s;">
          <i class="fas fa-trash" style="margin-right: 8px;"></i> Permanently Delete
          <span style="display: block; font-size: 12px; font-weight: 400; margin-top: 4px; color: #b91c1c;">
            All data will be erased: products, purchase orders, invites. Cannot be undone!
          </span>
        </button>
      </form>

      <!-- Cancel -->
      <button type="button" onclick="closeSupplierDeleteModal()" style="width: 100%; padding: 12px 20px; border-radius: 8px; border: 2px solid var(--border); background: white; color: var(--gray-700); font-weight: 600; font-size: 14px; cursor: pointer; text-align: center; transition: all 0.2s;">
        Cancel
      </button>
    </div>
  </div>
</div>

<!-- Create User Modal -->
<div id="createModal" class="modal-overlay">
  <div class="modal-content">
    <div class="modal-header">
      <h3 class="modal-title"><i class="fas fa-user-plus" style="color: var(--primary); margin-right: 8px;"></i> <?= $currentLang === 'fr' ? 'Créer un nouvel utilisateur' : 'Create New User' ?></h3>
      <button type="button" class="modal-close" onclick="closeCreateModal()">&times;</button>
    </div>
    <form id="createUserForm" method="POST" action="<?= url('admin/users/store') ?>">
      <?= csrfField() ?>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><?= $currentLang === 'fr' ? 'Prénom *' : 'First Name *' ?></label>
            <input type="text" name="first_name" class="form-input" required placeholder="Jean">
          </div>
          <div class="form-group">
            <label class="form-label"><?= $currentLang === 'fr' ? 'Nom de famille *' : 'Last Name *' ?></label>
            <input type="text" name="last_name" class="form-input" required placeholder="Dupont">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label"><?= $currentLang === 'fr' ? 'Adresse courriel *' : 'Email Address *' ?></label>
          <input type="email" name="email" class="form-input" required placeholder="jean@exemple.com">
        </div>

        <div class="form-group">
          <label class="form-label"><?= $currentLang === 'fr' ? 'Téléphone' : 'Phone' ?></label>
          <input type="tel" name="phone" class="form-input" placeholder="+1 (514) 123-4567">
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><?= $currentLang === 'fr' ? 'Rôle *' : 'Role *' ?></label>
            <select name="role" class="form-select" required id="createRoleSelect" onchange="toggleDeptField()">
              <option value=""><?= $currentLang === 'fr' ? 'Choisir un rôle' : 'Select Role' ?></option>
              <?php foreach ($roles ?? [] as $role): ?>
                <option value="<?= $role['name'] ?>"><?= htmlspecialchars($role['display_name'] ?: ucfirst(str_replace('_', ' ', $role['name']))) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label"><?= $currentLang === 'fr' ? 'Statut' : 'Status' ?></label>
            <select name="status" class="form-select">
              <option value="active" selected><?= $currentLang === 'fr' ? 'Actif' : 'Active' ?></option>
              <option value="pending"><?= $currentLang === 'fr' ? 'En attente' : 'Pending' ?></option>
              <option value="inactive"><?= $currentLang === 'fr' ? 'Inactif' : 'Inactive' ?></option>
            </select>
          </div>
        </div>

        <div class="form-group" id="deptFieldWrapper" style="display: none;">
          <label class="form-label"><?= $currentLang === 'fr' ? 'Département' : 'Department' ?></label>
          <select name="department" class="form-select" id="createDeptSelect">
            <option value=""><?= $currentLang === 'fr' ? 'Aucun (accès général)' : 'None (general access)' ?></option>
            <option value="ops"><?= $currentLang === 'fr' ? 'Opérations' : 'Operations' ?></option>
            <option value="finance"><?= $currentLang === 'fr' ? 'Finance' : 'Finance' ?></option>
            <option value="tech"><?= $currentLang === 'fr' ? 'Technologie' : 'Technology' ?></option>
            <option value="support"><?= $currentLang === 'fr' ? 'Soutien' : 'Support' ?></option>
            <option value="logistics"><?= $currentLang === 'fr' ? 'Logistique' : 'Logistics' ?></option>
            <option value="management"><?= $currentLang === 'fr' ? 'Direction' : 'Management' ?></option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label"><?= $currentLang === 'fr' ? 'Mot de passe temporaire' : 'Temporary Password' ?></label>
          <div style="display: flex; gap: 8px;">
            <input type="text" name="password" id="tempPassword" class="form-input" placeholder="<?= $currentLang === 'fr' ? 'Laisser vide pour générer' : 'Leave blank to auto-generate' ?>" style="flex: 1;">
            <button type="button" onclick="generatePassword()" class="btn-cancel" style="white-space: nowrap;">
              <i class="fas fa-random"></i> <?= $currentLang === 'fr' ? 'Générer' : 'Generate' ?>
            </button>
          </div>
          <p class="help-text"><?= $currentLang === 'fr' ? 'Si laissé vide, un mot de passe sécurisé sera généré automatiquement' : 'If left blank, a secure password will be auto-generated' ?></p>
        </div>

        <div class="checkbox-group">
          <input type="checkbox" name="send_welcome_email" id="sendWelcomeEmail" checked>
          <label for="sendWelcomeEmail"><?= $currentLang === 'fr' ? 'Envoyer un courriel de bienvenue avec les identifiants' : 'Send welcome email with login credentials' ?></label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeCreateModal()"><?= $currentLang === 'fr' ? 'Annuler' : 'Cancel' ?></button>
        <button type="submit" class="btn-submit">
          <i class="fas fa-user-plus"></i> <?= $currentLang === 'fr' ? 'Créer l\'utilisateur' : 'Create User' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Delete Modal Functions
function openDeleteModal(userId, userName) {
  document.getElementById('deleteUserId').value = userId;
  document.getElementById('deleteUserName').textContent = userName;
  document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
  document.getElementById('deleteModal').style.display = 'none';
}

// Create Modal Functions
function toggleDeptField() {
  var role = document.getElementById('createRoleSelect').value;
  document.getElementById('deptFieldWrapper').style.display = (role === 'admin_staff') ? 'block' : 'none';
  if (role !== 'admin_staff') document.getElementById('createDeptSelect').value = '';
}

function openCreateModal() {
  document.getElementById('createModal').classList.add('active');
  document.getElementById('createUserForm').reset();
  document.getElementById('sendWelcomeEmail').checked = true;
  document.getElementById('deptFieldWrapper').style.display = 'none';
}

function closeCreateModal() {
  document.getElementById('createModal').classList.remove('active');
}

// Generate secure password
function generatePassword() {
  const length = 12;
  const charset = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
  let password = '';

  // Ensure at least one of each type
  password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)];
  password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)];
  password += '0123456789'[Math.floor(Math.random() * 10)];
  password += '!@#$%^&*'[Math.floor(Math.random() * 8)];

  // Fill the rest
  for (let i = 4; i < length; i++) {
    password += charset[Math.floor(Math.random() * charset.length)];
  }

  // Shuffle the password
  password = password.split('').sort(() => Math.random() - 0.5).join('');

  document.getElementById('tempPassword').value = password;
}

// Change user status (suspend/activate)
function changeUserStatus(userId, newStatus, userName) {
  const statusText = newStatus === 'suspended' ? 'suspend' : newStatus === 'active' ? 'activate' : 'change status for';
  const confirmMessage = `Are you sure you want to ${statusText} ${userName}?`;

  if (confirm(confirmMessage)) {
    document.getElementById('statusChangeUserId').value = userId;
    document.getElementById('statusChangeStatus').value = newStatus;
    document.getElementById('statusChangeForm').submit();
  }
}

// Close delete modal when clicking outside
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeDeleteModal();
  }
});

// Close create modal when clicking outside
document.getElementById('createModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeCreateModal();
  }
});

// Supplier Status Change
function changeSupplierStatus(supplierId, newStatus, supplierName) {
  const labels = { active: 'activate', inactive: 'disable', suspended: 'suspend' };
  const statusText = labels[newStatus] || 'change status for';
  const confirmMessage = `Are you sure you want to ${statusText} supplier ${supplierName}?`;

  if (confirm(confirmMessage)) {
    document.getElementById('supplierStatusId').value = supplierId;
    document.getElementById('supplierStatusValue').value = newStatus;
    document.getElementById('supplierStatusForm').submit();
  }
}

// Supplier Delete Modal
function openSupplierDeleteModal(supplierId, supplierName, companyName) {
  document.getElementById('supplierDeleteName').textContent = supplierName;
  const companyEl = document.getElementById('supplierDeleteCompany');
  companyEl.textContent = companyName ? '(' + companyName + ')' : '';
  document.getElementById('supplierDisableId').value = supplierId;
  document.getElementById('supplierPermanentDeleteId').value = supplierId;
  document.getElementById('supplierDeleteModal').style.display = 'flex';
}

function closeSupplierDeleteModal() {
  document.getElementById('supplierDeleteModal').style.display = 'none';
}

// Confirm permanent delete
document.getElementById('supplierPermanentDeleteForm')?.addEventListener('submit', function(e) {
  if (!confirm('WARNING: This will permanently delete this supplier and ALL their data (products, purchase orders, invites). This cannot be undone. Are you absolutely sure?')) {
    e.preventDefault();
  }
});

// Close supplier delete modal when clicking outside
document.getElementById('supplierDeleteModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeSupplierDeleteModal();
  }
});

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeDeleteModal();
    closeCreateModal();
    closeSupplierDeleteModal();
  }
});

// Highlight filter inputs that have active values
(function() {
  document.querySelectorAll('.filters-card .form-input, .filters-card .form-select').forEach(function(el) {
    function update() { el.classList.toggle('filter-active', el.value !== ''); }
    update();
    el.addEventListener('input', update);
    el.addEventListener('change', update);
  });
})();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>