<?php
/**
 * OCS Admin - Drivers Management
 * File: app/Views/admin/drivers/index.php
 */

$pageTitle   = 'Drivers Management';
$currentPage = 'drivers-list';

$currentLang = $_SESSION['language'] ?? 'fr';

$translations = [
    'en' => [
        'title'             => 'Drivers Management',
        'subtitle'          => 'Manage all registered drivers on the marketplace',
        'search'            => 'Search',
        'search_placeholder'=> 'Search by name, email, phone...',
        'status'            => 'Status',
        'all_statuses'      => 'All Statuses',
        'active'            => 'Active',
        'inactive'          => 'Inactive',
        'suspended'         => 'Suspended',
        'filter'            => 'Filter',
        'total_drivers'     => 'Total Drivers',
        'active_drivers'    => 'Active',
        'suspended_drivers' => 'Suspended',
        'pending_apps'      => 'Pending Applications',
        'driver'            => 'Driver',
        'contact'           => 'Contact',
        'zone'              => 'Zone',
        'deliveries'        => 'Deliveries',
        'rating'            => 'Rating',
        'status_col'        => 'Status',
        'joined'            => 'Joined',
        'actions'           => 'Actions',
        'view_details'      => 'View Details',
        'suspend_driver'    => 'Suspend Driver',
        'activate_driver'   => 'Activate Driver',
        'delete_driver'     => 'Delete Driver',
        'no_drivers_found'  => 'No drivers found',
        'clear_filters'     => 'Clear filters',
        'showing'           => 'Showing',
        'to'                => 'to',
        'of'                => 'of',
        'drivers_text'      => 'drivers',
        'previous'          => 'Previous',
        'next'              => 'Next',
        'no_zone'           => 'No zone',
        'completed'         => 'completed',
        'no_rating'         => 'N/A',
        'view_applications' => 'View Applications',
    ],
    'fr' => [
        'title'             => 'Gestion Livreurs',
        'subtitle'          => 'Gerer tous les livreurs inscrits sur le marketplace',
        'search'            => 'Rechercher',
        'search_placeholder'=> 'Rechercher par nom, courriel, telephone...',
        'status'            => 'Statut',
        'all_statuses'      => 'Tous les statuts',
        'active'            => 'Actif',
        'inactive'          => 'Inactif',
        'suspended'         => 'Suspendu',
        'filter'            => 'Filtrer',
        'total_drivers'     => 'Total Livreurs',
        'active_drivers'    => 'Actifs',
        'suspended_drivers' => 'Suspendus',
        'pending_apps'      => 'Candidatures en attente',
        'driver'            => 'Livreur',
        'contact'           => 'Contact',
        'zone'              => 'Zone',
        'deliveries'        => 'Livraisons',
        'rating'            => 'Cote',
        'status_col'        => 'Statut',
        'joined'            => 'Inscrit',
        'actions'           => 'Actions',
        'view_details'      => 'Voir details',
        'suspend_driver'    => 'Suspendre',
        'activate_driver'   => 'Activer',
        'delete_driver'     => 'Supprimer',
        'no_drivers_found'  => 'Aucun livreur trouve',
        'clear_filters'     => 'Effacer les filtres',
        'showing'           => 'Affichage',
        'to'                => 'a',
        'of'                => 'sur',
        'drivers_text'      => 'livreurs',
        'previous'          => 'Precedent',
        'next'              => 'Suivant',
        'no_zone'           => 'Aucune zone',
        'completed'         => 'completes',
        'no_rating'         => 'N/D',
        'view_applications' => 'Voir candidatures',
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
  .drivers-header { margin-bottom: 32px; }
  .drivers-header h1 { font-size: 28px; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
  .drivers-header p  { font-size: 15px; color: var(--gray-600); }

  .header-actions { display: flex; align-items: flex-start; justify-content: space-between; }
  .btn-applications { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; text-decoration: none; transition: all var(--transition-base); }
  .btn-applications:hover { background: #2563eb; transform: translateY(-1px); }
  .btn-applications .badge-count { background: white; color: #3b82f6; font-size: 11px; font-weight: 700; padding: 2px 7px; border-radius: 10px; }

  .filters-card  { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); padding: 24px; margin-bottom: 24px; }
  .filters-grid  { display: grid; grid-template-columns: 2fr 1fr auto; gap: 16px; }
  .form-group    { display: flex; flex-direction: column; }
  .form-label    { font-size: 13px; font-weight: 600; color: var(--dark); margin-bottom: 8px; }
  .form-input, .form-select { width: 100%; padding: 10px 16px; border: 2px solid var(--border); border-radius: var(--radius-md); font-size: 14px; font-family: inherit; transition: all var(--transition-base); }
  .form-input:focus, .form-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,178,7,0.1); }
  .btn-filter { align-self: flex-end; padding: 10px 24px; background: var(--primary); color: white; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; cursor: pointer; transition: all var(--transition-base); display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; }
  .btn-filter:hover { background: var(--primary-600); transform: translateY(-1px); box-shadow: var(--shadow-md); }

  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px; margin-bottom: 24px; }
  .stat-card  { background: white; border-radius: var(--radius-xl); padding: 24px; box-shadow: var(--shadow-sm); transition: all var(--transition-base); }
  .stat-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
  .stat-header { display: flex; align-items: center; justify-content: space-between; }
  .stat-label  { font-size: 13px; font-weight: 600; color: var(--gray-600); }
  .stat-icon   { width: 48px; height: 48px; border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; font-size: 20px; }
  .stat-icon.blue   { background: #dbeafe; color: #3b82f6; }
  .stat-icon.green  { background: #dcfce7; color: #22c55e; }
  .stat-icon.red    { background: #fee2e2; color: #ef4444; }
  .stat-icon.orange { background: #ffedd5; color: #f97316; }
  .stat-value       { font-size: 28px; font-weight: 700; margin-top: 12px; }
  .stat-value.blue  { color: #3b82f6; }
  .stat-value.green { color: #22c55e; }
  .stat-value.red   { color: #ef4444; }
  .stat-value.orange{ color: #f97316; }

  .table-card    { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  .table-wrapper { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--gray-50); }
  th { padding: 12px 24px; text-align: left; font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.05em; }
  th.text-right { text-align: right; }
  td { padding: 16px 24px; border-top: 1px solid var(--border); font-size: 14px; }
  td.text-right { text-align: right; }
  tbody tr { transition: background var(--transition-base); }
  tbody tr:hover { background: var(--gray-50); }

  .driver-cell   { display: flex; align-items: center; gap: 12px; }
  .driver-avatar { width: 40px; height: 40px; border-radius: 50%; background: #f97316; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 16px; flex-shrink: 0; overflow: hidden; }
  .driver-avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
  .driver-name   { font-weight: 600; color: var(--dark); font-size: 14px; }
  .driver-id     { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
  .contact-email { color: var(--dark); font-size: 14px; }
  .contact-phone { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
  .zone-name     { font-size: 14px; color: var(--dark); }
  .deliveries-count { font-weight: 600; color: var(--dark); }
  .deliveries-sub   { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
  .rating-stars  { display: flex; align-items: center; gap: 4px; font-size: 14px; font-weight: 600; color: #f59e0b; }
  .rating-count  { font-size: 12px; color: var(--gray-500); margin-left: 4px; font-weight: 400; }
  .date-cell     { font-size: 13px; color: var(--gray-500); }

  .badge { display: inline-block; padding: 4px 12px; border-radius: var(--radius-full); font-size: 12px; font-weight: 600; }
  .badge.status-active    { background: #dcfce7; color: #166534; }
  .badge.status-inactive  { background: var(--gray-200); color: var(--gray-700); }
  .badge.status-suspended { background: #fee2e2; color: #991b1b; }
  .badge.status-pending   { background: #fef3c7; color: #92400e; }

  .action-buttons { display: flex; align-items: center; justify-content: flex-end; gap: 12px; }
  .action-btn { background: none; border: none; cursor: pointer; font-size: 16px; transition: color var(--transition-base); padding: 4px; }
  .action-btn.view     { color: var(--primary); } .action-btn.view:hover     { color: var(--primary-600); }
  .action-btn.suspend  { color: #f59e0b; }         .action-btn.suspend:hover  { color: #d97706; }
  .action-btn.activate { color: #22c55e; }         .action-btn.activate:hover { color: #16a34a; }
  .action-btn.delete   { color: #ef4444; }         .action-btn.delete:hover   { color: #dc2626; }

  .empty-state { padding: 64px 24px; text-align: center; }
  .empty-state-icon  { font-size: 64px; color: var(--gray-300); margin-bottom: 16px; }
  .empty-state-title { font-size: 18px; font-weight: 600; color: var(--gray-500); margin-bottom: 8px; }
  .empty-state-link  { display: inline-block; margin-top: 8px; color: var(--primary); font-size: 14px; text-decoration: none; }

  .pagination         { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; background: var(--gray-50); border-top: 1px solid var(--border); }
  .pagination-info    { font-size: 14px; color: var(--gray-700); }
  .pagination-buttons { display: flex; gap: 8px; }
  .pagination-btn { padding: 8px 16px; border: 2px solid var(--border); border-radius: var(--radius-md); font-size: 13px; font-weight: 600; color: var(--gray-700); background: white; text-decoration: none; transition: all var(--transition-base); }
  .pagination-btn:hover  { background: var(--gray-50); border-color: var(--gray-300); }
  .pagination-btn.active { background: var(--primary); color: white; border-color: var(--primary); }

  @media (max-width: 1024px) {
    .filters-grid { grid-template-columns: 1fr; }
    .btn-filter   { width: 100%; justify-content: center; }
    .header-actions { flex-direction: column; gap: 16px; }
  }
  @media (max-width: 768px) {
    .stats-grid { grid-template-columns: 1fr; }
    th, td      { padding: 12px 16px; }
    .pagination { flex-direction: column; gap: 16px; }
    .pagination-buttons { flex-wrap: wrap; justify-content: center; }
  }
</style>

<!-- Header -->
<div class="header-actions">
  <div class="drivers-header">
    <h1><?= $t['title'] ?></h1>
    <p><?= $t['subtitle'] ?></p>
  </div>
  <?php if (($pendingApps ?? 0) > 0): ?>
  <a href="<?= url('admin/delivery?tab=applications') ?>" class="btn-applications">
    <i class="fas fa-user-clock"></i>
    <?= $t['view_applications'] ?>
    <span class="badge-count"><?= $pendingApps ?></span>
  </a>
  <?php endif; ?>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('admin/drivers') ?>">
    <div class="filters-grid">
      <div class="form-group">
        <label class="form-label"><?= $t['search'] ?></label>
        <input type="text" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="<?= $t['search_placeholder'] ?>" class="form-input">
      </div>
      <div class="form-group">
        <label class="form-label"><?= $t['status'] ?></label>
        <select name="status" class="form-select">
          <option value=""><?= $t['all_statuses'] ?></option>
          <option value="active"    <?= ($statusFilter ?? '') === 'active'    ? 'selected' : '' ?>><?= $t['active'] ?></option>
          <option value="inactive"  <?= ($statusFilter ?? '') === 'inactive'  ? 'selected' : '' ?>><?= $t['inactive'] ?></option>
          <option value="suspended" <?= ($statusFilter ?? '') === 'suspended' ? 'selected' : '' ?>><?= $t['suspended'] ?></option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" style="visibility:hidden;">Action</label>
        <button type="submit" class="btn-filter">
          <i class="fas fa-filter"></i> <?= $t['filter'] ?>
        </button>
      </div>
    </div>
  </form>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['total_drivers'] ?></span>
      <div class="stat-icon blue"><i class="fas fa-motorcycle"></i></div>
    </div>
    <div class="stat-value blue"><?= number_format($total ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['active_drivers'] ?></span>
      <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
    </div>
    <div class="stat-value green"><?= number_format($activeCount ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['suspended_drivers'] ?></span>
      <div class="stat-icon red"><i class="fas fa-ban"></i></div>
    </div>
    <div class="stat-value red"><?= number_format($suspendedCount ?? 0) ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-header">
      <span class="stat-label"><?= $t['pending_apps'] ?></span>
      <div class="stat-icon orange"><i class="fas fa-user-clock"></i></div>
    </div>
    <div class="stat-value orange"><?= number_format($pendingApps ?? 0) ?></div>
  </div>
</div>

<!-- Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><?= $t['driver'] ?></th>
          <th><?= $t['contact'] ?></th>
          <th><?= $t['zone'] ?></th>
          <th><?= $t['deliveries'] ?></th>
          <th><?= $t['rating'] ?></th>
          <th><?= $t['status_col'] ?></th>
          <th><?= $t['joined'] ?></th>
          <th class="text-right"><?= $t['actions'] ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($drivers)): ?>
          <?php foreach ($drivers as $driver): ?>
            <tr>
              <td>
                <div class="driver-cell">
                  <div class="driver-avatar">
                    <?php if (!empty($driver['avatar'])): ?>
                      <img src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($driver['avatar'], '/')) ?>" alt="">
                    <?php else: ?>
                      <?= strtoupper(substr($driver['first_name'] ?? '?', 0, 1)) ?>
                    <?php endif; ?>
                  </div>
                  <div>
                    <div class="driver-name"><?= htmlspecialchars(trim(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? ''))) ?></div>
                    <div class="driver-id">ID: <?= $driver['id'] ?></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="contact-email"><?= htmlspecialchars($driver['email'] ?? '') ?></div>
                <?php if (!empty($driver['phone'])): ?>
                  <div class="contact-phone"><?= htmlspecialchars($driver['phone']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <div class="zone-name"><?= htmlspecialchars($driver['zone_name'] ?? $t['no_zone']) ?></div>
              </td>
              <td>
                <div class="deliveries-count"><?= number_format($driver['completed_deliveries'] ?? 0) ?></div>
                <div class="deliveries-sub"><?= $t['completed'] ?></div>
              </td>
              <td>
                <?php if (!empty($driver['avg_rating'])): ?>
                  <div class="rating-stars">
                    <i class="fas fa-star"></i>
                    <?= number_format((float)$driver['avg_rating'], 1) ?>
                  </div>
                <?php else: ?>
                  <span style="color:var(--gray-400);font-size:13px;"><?= $t['no_rating'] ?></span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge status-<?= $driver['status'] ?? 'inactive' ?>">
                  <?= $t[strtolower($driver['status'] ?? 'inactive')] ?? ucfirst($driver['status'] ?? '') ?>
                </span>
              </td>
              <td class="date-cell"><?= formatDate($driver['created_at'], 'M d, Y') ?></td>
              <td class="text-right">
                <div class="action-buttons">
                  <a href="<?= url('admin/delivery/edit-driver?id=' . $driver['id']) ?>" class="action-btn view" title="<?= $t['view_details'] ?>">
                    <i class="fas fa-eye"></i>
                  </a>
                  <?php if (($driver['status'] ?? '') === 'active'): ?>
                    <button type="button" class="action-btn suspend" title="<?= $t['suspend_driver'] ?>"
                      onclick="openStatusModal(<?= $driver['id'] ?>, '<?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>', 'suspend')">
                      <i class="fas fa-ban"></i>
                    </button>
                  <?php else: ?>
                    <button type="button" class="action-btn activate" title="<?= $t['activate_driver'] ?>"
                      onclick="openStatusModal(<?= $driver['id'] ?>, '<?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>', 'activate')">
                      <i class="fas fa-check-circle"></i>
                    </button>
                  <?php endif; ?>
                  <button type="button" class="action-btn delete" title="<?= $t['delete_driver'] ?>"
                    onclick="openDeleteModal(<?= $driver['id'] ?>, '<?= htmlspecialchars($driver['first_name'] . ' ' . $driver['last_name']) ?>')">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="8">
              <div class="empty-state">
                <div class="empty-state-icon"><i class="fas fa-motorcycle"></i></div>
                <div class="empty-state-title"><?= $t['no_drivers_found'] ?></div>
                <?php if (($search ?? '') || ($statusFilter ?? '')): ?>
                  <a href="<?= url('admin/drivers') ?>" class="empty-state-link"><?= $t['clear_filters'] ?></a>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if (($total ?? 0) > ($perPage ?? 20)): ?>
    <div class="pagination">
      <div class="pagination-info">
        <?= $t['showing'] ?> <?= (($page - 1) * $perPage) + 1 ?> <?= $t['to'] ?> <?= min($page * $perPage, $total) ?> <?= $t['of'] ?> <?= $total ?> <?= $t['drivers_text'] ?>
      </div>
      <div class="pagination-buttons">
        <?php
        $totalPages  = ceil($total / $perPage);
        $qp          = http_build_query(array_filter(['search' => $search ?? '', 'status' => $statusFilter ?? '']));
        $qpx         = $qp ? '&' : '';
        ?>
        <?php if ($page > 1): ?>
          <a href="<?= url('admin/drivers?page=' . ($page - 1) . $qpx . $qp) ?>" class="pagination-btn"><?= $t['previous'] ?></a>
        <?php endif; ?>
        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
          <a href="<?= url('admin/drivers?page=' . $i . $qpx . $qp) ?>" class="pagination-btn <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?>
          <a href="<?= url('admin/drivers?page=' . ($page + 1) . $qpx . $qp) ?>" class="pagination-btn"><?= $t['next'] ?></a>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Status Modal -->
<div id="statusModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:12px;padding:32px;max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;">
    <div id="statusModalIcon" style="width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;"></div>
    <h3 id="statusModalTitle" style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:8px;"></h3>
    <p id="statusModalText"   style="font-size:14px;color:#6b7280;margin-bottom:24px;"></p>
    <form id="statusForm" method="POST" action="">
      <?= csrfField() ?>
      <input type="hidden" name="user_id" id="statusUserId">
      <input type="hidden" name="status"  id="statusValue">
      <div style="display:flex;gap:12px;justify-content:center;">
        <button type="button" onclick="closeStatusModal()" style="padding:10px 24px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-weight:600;color:#6b7280;background:white;cursor:pointer;">Cancel</button>
        <button type="submit" id="statusSubmitBtn" style="padding:10px 24px;border:none;border-radius:8px;font-size:14px;font-weight:600;color:white;cursor:pointer;"></button>
      </div>
    </form>
  </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:12px;padding:32px;max-width:450px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;">
    <div style="width:56px;height:56px;background:#fee2e2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
      <i class="fas fa-exclamation-triangle" style="font-size:24px;color:#dc2626;"></i>
    </div>
    <h3 style="font-size:20px;font-weight:700;color:#1f2937;margin-bottom:8px;">Delete Driver</h3>
    <p style="font-size:14px;color:#6b7280;margin-bottom:24px;">Are you sure you want to delete <strong id="deleteNameDisplay"></strong>? This cannot be undone.</p>
    <form id="deleteForm" method="POST" action="<?= url('admin/users/delete') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="user_id" id="deleteUserId">
      <div style="margin:16px 0 10px;text-align:left;">
        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Reason</label>
        <select name="delete_reason" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;">
          <option value="voluntary">Voluntary departure</option>
          <option value="inactive">Inactivity</option>
          <option value="terms_violation">Terms violation</option>
          <option value="test">Test account</option>
          <option value="other">Other</option>
        </select>
      </div>
      <div style="margin-bottom:10px;text-align:left;">
        <label style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:5px;">Notes (optional)</label>
        <textarea name="delete_notes" rows="2" style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;"></textarea>
      </div>
      <div style="margin-bottom:16px;text-align:left;display:flex;align-items:center;gap:8px;">
        <input type="checkbox" name="can_rejoin" value="1" checked style="width:15px;height:15px;accent-color:#dc2626;">
        <label style="font-size:13px;color:#374151;cursor:pointer;">Allow re-registration</label>
      </div>
      <div style="display:flex;gap:12px;justify-content:center;">
        <button type="button" onclick="closeDeleteModal()" style="padding:10px 24px;border:2px solid #e5e7eb;border-radius:8px;font-size:14px;font-weight:600;color:#6b7280;background:white;cursor:pointer;">Cancel</button>
        <button type="submit" style="padding:10px 24px;border:none;border-radius:8px;font-size:14px;font-weight:600;color:white;background:#dc2626;cursor:pointer;"><i class="fas fa-trash"></i> Delete</button>
      </div>
    </form>
  </div>
</div>

<script>
function openStatusModal(userId, name, action) {
  const isSuspend = action === 'suspend';
  document.getElementById('statusUserId').value  = userId;
  document.getElementById('statusValue').value   = isSuspend ? 'suspended' : 'active';
  document.getElementById('statusForm').action   = '<?= url('admin/users/change-status') ?>';
  document.getElementById('statusModalTitle').textContent = isSuspend ? 'Suspend Driver' : 'Activate Driver';
  document.getElementById('statusModalText').textContent  = (isSuspend ? 'Suspend ' : 'Activate ') + name + '?';
  const icon = document.getElementById('statusModalIcon');
  icon.style.background = isSuspend ? '#fef3c7' : '#dcfce7';
  icon.innerHTML = isSuspend
    ? '<i class="fas fa-ban" style="font-size:24px;color:#d97706;"></i>'
    : '<i class="fas fa-check-circle" style="font-size:24px;color:#22c55e;"></i>';
  const btn = document.getElementById('statusSubmitBtn');
  btn.style.background = isSuspend ? '#f59e0b' : '#22c55e';
  btn.textContent      = isSuspend ? 'Suspend' : 'Activate';
  document.getElementById('statusModal').style.display = 'flex';
}
function closeStatusModal() { document.getElementById('statusModal').style.display = 'none'; }
document.getElementById('statusModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeStatusModal(); });

function openDeleteModal(userId, name) {
  document.getElementById('deleteUserId').value           = userId;
  document.getElementById('deleteNameDisplay').textContent = name;
  document.getElementById('deleteModal').style.display    = 'flex';
}
function closeDeleteModal() { document.getElementById('deleteModal').style.display = 'none'; }
document.getElementById('deleteModal')?.addEventListener('click', e => { if (e.target === e.currentTarget) closeDeleteModal(); });
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
