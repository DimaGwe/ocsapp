<?php
$pageTitle   = 'Waitlist';
$currentPage = 'waitlist';

$roleLabels = [
    'buyer'    => 'Buyer',
    'seller'   => 'Seller',
    'supplier' => 'Supplier',
    'driver'   => 'Driver',
    'business' => 'Business',
    'partner'  => 'Partner',
];

$discoveryLabels = [
    'social_media'   => 'Social media',
    'friend_family'  => 'Friend / family',
    'google_search'  => 'Google search',
    'press'          => 'Press',
    'representative' => 'OCSAPP representative',
    'social'         => 'Social media',
    'referral'       => 'Referral',
    'local_business' => 'Local business',
    'event'          => 'Event',
    'web'            => 'Web',
    'other'          => 'Other',
];

ob_start();
?>

<style>
  .page-header { margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
  .page-header h1 { font-size: 26px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
  .page-header p  { font-size: 14px; color: var(--gray-600); }

  .header-actions { display: flex; gap: 10px; }
  .btn-export {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border: 2px solid var(--border); border-radius: var(--radius-md);
    font-size: 13px; font-weight: 600; color: var(--gray-700); background: white;
    text-decoration: none; cursor: pointer; transition: all var(--transition-base);
  }
  .btn-export:hover { background: var(--gray-50); border-color: var(--gray-300); }

  .btn-notify {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; border: 2px solid var(--primary); border-radius: var(--radius-md);
    font-size: 13px; font-weight: 600; color: var(--primary); background: white;
    cursor: pointer; transition: all var(--transition-base);
  }
  .btn-notify:hover { background: var(--primary); color: white; }
  .invite-meta { margin-top: 5px; font-size: 11px; color: #1e40af; white-space: nowrap; }
  .invite-meta-ok { color: #166534; }
  .invite-meta-off { color: var(--gray-500); }
  .action-btn.invite { color: var(--primary); }

  /* Stats */
  .stats-grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 16px; margin-bottom: 24px;
  }
  .stat-card { background: white; border-radius: var(--radius-xl); padding: 20px; box-shadow: var(--shadow-sm); }
  .stat-label { font-size: 12px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px; }
  .stat-value { font-size: 26px; font-weight: 800; }
  .stat-value.blue   { color: #3b82f6; }
  .stat-value.green  { color: #22c55e; }
  .stat-value.amber  { color: #f59e0b; }
  .stat-value.purple { color: #a855f7; }
  .stat-value.gray   { color: #6b7280; }
  .stat-value.dark   { color: #1f2937; }
  .stat-value.cyan   { color: #06b6d4; }
  .stat-value.red    { color: #ef4444; }

  /* Filters */
  .filters-card {
    background: white; border-radius: var(--radius-xl); padding: 20px 24px;
    box-shadow: var(--shadow-sm); margin-bottom: 20px;
  }
  .filters-grid { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 14px; align-items: end; }
  .form-group { display: flex; flex-direction: column; gap: 5px; }
  .form-label { font-size: 12px; font-weight: 600; color: var(--gray-600); text-transform: uppercase; letter-spacing: .05em; }
  .form-input, .form-select {
    padding: 9px 12px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
    font-size: 13px; color: var(--dark); background: white; transition: border-color var(--transition-base);
  }
  .form-input:focus, .form-select:focus { outline: none; border-color: var(--primary); }
  .btn-filter {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 18px; background: var(--primary); color: white; border: none;
    border-radius: var(--radius-md); font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background var(--transition-base); white-space: nowrap;
  }
  .btn-filter:hover { background: var(--primary-600); }

  /* Bulk bar */
  .bulk-bar {
    display: none; align-items: center; gap: 12px; margin-bottom: 12px;
    padding: 10px 16px; background: #eff6ff; border: 1px solid #bfdbfe;
    border-radius: var(--radius-md);
  }
  .bulk-bar.visible { display: flex; }
  .bulk-bar span { font-size: 13px; font-weight: 600; color: #1d4ed8; }

  /* Table */
  .table-card { background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-sm); overflow: hidden; }
  .table-wrapper { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  thead { background: var(--gray-50); }
  th { padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 700; color: var(--gray-600); text-transform: uppercase; letter-spacing: .05em; }
  td { padding: 14px 20px; border-top: 1px solid var(--border); font-size: 13px; vertical-align: middle; }
  tbody tr:hover { background: var(--gray-50); }

  /* Badge */
  .badge {
    display: inline-block; padding: 3px 10px; border-radius: var(--radius-full);
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em;
  }
  .badge-role-buyer    { background: #dbeafe; color: #1d4ed8; }
  .badge-role-seller   { background: #dcfce7; color: #166534; }
  .badge-role-supplier { background: #fef9c3; color: #854d0e; }
  .badge-role-driver   { background: #f3e8ff; color: #7e22ce; }
  .badge-role-business { background: #f1f5f9; color: #475569; }
  .badge-role-partner  { background: #ffe4e6; color: #be123c; }

  .badge-status-pending   { background: #fef3c7; color: #92400e; }
  .badge-status-notified  { background: #dbeafe; color: #1e40af; }
  .badge-status-converted { background: #dcfce7; color: #166534; }

  /* Status select */
  .status-select {
    padding: 5px 8px; border: 1.5px solid var(--border); border-radius: var(--radius-md);
    font-size: 12px; color: var(--dark); background: white; cursor: pointer;
  }
  .status-select:focus { outline: none; border-color: var(--primary); }

  /* Action buttons */
  .action-buttons { display: flex; align-items: center; gap: 8px; }
  .action-btn { background: none; border: none; cursor: pointer; font-size: 15px; padding: 4px; transition: color var(--transition-base); }
  .action-btn.delete { color: #ef4444; }
  .action-btn.delete:hover { color: #dc2626; }
  .action-btn.view { color: var(--gray-600); }
  .action-btn.view:hover { color: var(--primary); }

  /* Details modal */
  .modal-overlay {
    display: none; position: fixed; inset: 0; background: rgba(15,23,42,.5);
    align-items: center; justify-content: center; z-index: 1000; padding: 20px;
  }
  .modal-overlay.visible { display: flex; }
  .modal-box {
    background: white; border-radius: var(--radius-xl); max-width: 640px; width: 100%;
    max-height: 85vh; overflow-y: auto; box-shadow: var(--shadow-lg);
  }
  .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--border); }
  .modal-header h2 { font-size: 17px; font-weight: 700; color: var(--dark); }
  .modal-close { background: none; border: none; font-size: 18px; color: var(--gray-500); cursor: pointer; }
  .modal-body { padding: 20px 24px; }
  .modal-section-title {
    font-size: 11px; font-weight: 700; color: var(--gray-500); text-transform: uppercase;
    letter-spacing: .05em; margin: 18px 0 8px; padding-top: 12px; border-top: 1px solid var(--border);
  }
  .modal-section-title:first-child { margin-top: 0; padding-top: 0; border-top: none; }
  .detail-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 20px; }
  .detail-item { min-width: 0; }
  .detail-label { font-size: 11px; font-weight: 600; color: var(--gray-500); text-transform: uppercase; letter-spacing: .04em; }
  .detail-value { font-size: 13px; color: var(--dark); margin-top: 2px; word-break: break-word; }
  .detail-value.empty { color: var(--gray-400); }

  /* Empty state */
  .empty-state { padding: 64px 24px; text-align: center; }
  .empty-state i { font-size: 48px; color: var(--gray-300); margin-bottom: 14px; display: block; }
  .empty-state p { font-size: 15px; color: var(--gray-500); }

  /* Pagination */
  .pagination { display: flex; align-items: center; justify-content: space-between; padding: 14px 20px; background: var(--gray-50); border-top: 1px solid var(--border); }
  .pagination-info { font-size: 13px; color: var(--gray-700); }
  .pagination-buttons { display: flex; gap: 6px; }
  .pagination-btn {
    padding: 6px 12px; border: 2px solid var(--border); border-radius: var(--radius-md);
    font-size: 12px; font-weight: 600; color: var(--gray-700); background: white;
    text-decoration: none; transition: all var(--transition-base);
  }
  .pagination-btn:hover { background: var(--gray-50); border-color: var(--gray-300); }
  .pagination-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
</style>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1>Waitlist</h1>
    <p>Pre-launch signups - <?= number_format((int)$total) ?> total</p>
  </div>
  <div class="header-actions">
    <a href="<?= url('/admin/waitlist/export') ?>?<?= http_build_query(array_filter(['role' => $role, 'status' => $status])) ?>" class="btn-export">
      <i class="fas fa-download"></i> Export CSV
    </a>
  </div>
</div>

<!-- Stats -->
<div class="stats-grid">
  <?php
  $statCards = [
    ['Total',      $stats['total'],      'blue'],
    ['Buyers',     $stats['buyers'],     'blue'],
    ['Sellers',    $stats['sellers'],    'green'],
    ['Suppliers',  $stats['suppliers'],  'amber'],
    ['Drivers',    $stats['drivers'],    'purple'],
    ['Business',   $stats['businesses'],'gray'],
    ['Partners',   $stats['partners'],   'red'],
    ['Pending',    $stats['pending'],    'amber'],
    ['Notified',   $stats['notified'],   'cyan'],
    ['Converted',  $stats['converted'],  'green'],
  ];
  foreach ($statCards as [$label, $val, $color]):
  ?>
  <div class="stat-card">
    <div class="stat-label"><?= $label ?></div>
    <div class="stat-value <?= $color ?>"><?= number_format((int)$val) ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Filters -->
<div class="filters-card">
  <form method="GET" action="<?= url('/admin/waitlist') ?>">
    <div class="filters-grid">
      <div class="form-group">
        <label class="form-label">Search</label>
        <input type="text" name="search" class="form-input" placeholder="Email, name or referral code..." value="<?= htmlspecialchars($search) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select name="role" class="form-select">
          <option value="">All roles</option>
          <?php foreach ($roleLabels as $val => $lbl): ?>
          <option value="<?= $val ?>" <?= $role === $val ? 'selected' : '' ?>><?= $lbl ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-select">
          <option value="">All statuses</option>
          <option value="pending"   <?= $status === 'pending'   ? 'selected' : '' ?>>Pending</option>
          <option value="notified"  <?= $status === 'notified'  ? 'selected' : '' ?>>Notified</option>
          <option value="converted" <?= $status === 'converted' ? 'selected' : '' ?>>Converted</option>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" style="visibility:hidden;">Go</label>
        <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
      </div>
    </div>
  </form>
</div>

<!-- Bulk action bar -->
<div class="bulk-bar" id="bulk-bar">
  <span id="selected-count">0 selected</span>
  <button class="btn-notify" onclick="bulkNotify()"><i class="fas fa-paper-plane"></i> Send Account Invite</button>
</div>

<!-- Table -->
<div class="table-card">
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th style="width:36px;"><input type="checkbox" id="select-all" style="cursor:pointer;"></th>
          <th>Name / Email</th>
          <th>Role</th>
          <th title="Position within the role, fixed at signup">
            <?php $sortQ = http_build_query(array_filter(['search' => $search, 'role' => $role, 'status' => $status, 'sort' => ($sort ?? '') === 'position' ? '' : 'position'])); ?>
            <a href="<?= url('/admin/waitlist') ?><?= $sortQ ? '?' . $sortQ : '' ?>" style="color:inherit;text-decoration:none;">
              # <i class="fas fa-sort<?= ($sort ?? '') === 'position' ? '-up' : '' ?>" style="font-size:11px;opacity:.6;"></i>
            </a>
          </th>
          <th>Referrals</th>
          <th>Referred By</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($entries)): ?>
        <tr><td colspan="9">
          <div class="empty-state">
            <i class="fas fa-list-ul"></i>
            <p>No waitlist entries found.</p>
          </div>
        </td></tr>
      <?php else: ?>
        <?php foreach ($entries as $e): ?>
        <tr data-id="<?= $e['id'] ?>">
          <td><input type="checkbox" class="row-check" value="<?= $e['id'] ?>" style="cursor:pointer;"></td>
          <td>
            <div style="font-weight:600;color:var(--dark);"><?= htmlspecialchars(trim($e['first_name'] . ' ' . $e['last_name'])) ?></div>
            <div style="font-size:12px;color:var(--gray-500);margin-top:2px;"><?= htmlspecialchars($e['email']) ?></div>
          </td>
          <td><span class="badge badge-role-<?= $e['role'] ?>"><?= $roleLabels[$e['role']] ?? $e['role'] ?></span></td>
          <td style="font-weight:700;color:var(--dark);white-space:nowrap;"><?= $e['signup_position'] ? '#' . (int) $e['signup_position'] : '<span style="color:var(--gray-400);">-</span>' ?></td>
          <td style="text-align:center;">
            <?php if ($e['referral_count'] > 0): ?>
              <span style="font-weight:700;color:var(--primary);"><?= $e['referral_count'] ?></span>
            <?php else: ?>
              <span style="color:var(--gray-400);">-</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($e['referred_by']): ?>
              <code style="font-size:12px;background:var(--gray-100);padding:2px 6px;border-radius:4px;"><?= htmlspecialchars($e['referred_by']) ?></code>
            <?php else: ?>
              <span style="color:var(--gray-400);">-</span>
            <?php endif; ?>
          </td>
          <td>
            <select class="status-select" data-id="<?= $e['id'] ?>">
              <option value="pending"   <?= $e['status']==='pending'   ? 'selected':'' ?>>Pending</option>
              <option value="notified"  <?= $e['status']==='notified'  ? 'selected':'' ?>>Notified</option>
              <option value="converted" <?= $e['status']==='converted' ? 'selected':'' ?>>Converted</option>
            </select>
            <?php if (!empty($e['has_account'])): ?>
              <div class="invite-meta invite-meta-ok"><i class="fas fa-user-check"></i> Account created</div>
            <?php elseif (!empty($e['unsubscribed_at'])): ?>
              <div class="invite-meta invite-meta-off"><i class="fas fa-ban"></i> Unsubscribed</div>
            <?php elseif (!empty($e['invite_sent_at'])): ?>
              <div class="invite-meta"><i class="fas fa-paper-plane"></i> Invited <?= date('M j', strtotime($e['invite_sent_at'])) ?></div>
            <?php endif; ?>
          </td>
          <td style="font-size:12px;color:var(--gray-500);"><?= date('M j, Y', strtotime($e['created_at'])) ?></td>
          <td>
            <div class="action-buttons">
              <button class="action-btn view" data-entry="<?= htmlspecialchars(base64_encode(json_encode($e)), ENT_QUOTES) ?>" onclick="viewEntry(this)" title="View details">
                <i class="fas fa-eye"></i>
              </button>
              <?php if ($e['role'] !== 'partner' && empty($e['has_account']) && empty($e['unsubscribed_at']) && $e['status'] !== 'converted'): ?>
              <button class="action-btn invite" onclick="sendInvites([<?= (int) $e['id'] ?>])" title="<?= !empty($e['invite_sent_at']) ? 'Re-send account invite' : 'Send account invite' ?>">
                <i class="fas fa-paper-plane"></i>
              </button>
              <?php endif; ?>
              <button class="action-btn delete" onclick="deleteEntry(<?= $e['id'] ?>, '<?= htmlspecialchars(addslashes($e['email'])) ?>')" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if ($total > $perPage): ?>
  <div class="pagination">
    <div class="pagination-info">
      Showing <?= min(($page-1)*$perPage+1, $total) ?>-<?= min($page*$perPage, $total) ?> of <?= number_format($total) ?>
    </div>
    <div class="pagination-buttons">
      <?php
      $totalPages = (int) ceil($total / $perPage);
      $q = http_build_query(array_filter(['search' => $search, 'role' => $role, 'status' => $status, 'sort' => ($sort ?? '') === 'position' ? 'position' : '']));
      $qPrefix = $q ? '&' : '';
      for ($p = max(1, $page-2); $p <= min($totalPages, $page+2); $p++):
      ?>
      <a href="<?= url('/admin/waitlist') ?>?page=<?= $p ?><?= $qPrefix . $q ?>" class="pagination-btn <?= $p === $page ? 'active' : '' ?>">
        <?= $p ?>
      </a>
      <?php endfor; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<!-- Details modal -->
<div class="modal-overlay" id="details-modal" onclick="if (event.target === this) closeModal()">
  <div class="modal-box">
    <div class="modal-header">
      <h2 id="details-modal-title">Waitlist Entry</h2>
      <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
    </div>
    <div class="modal-body" id="details-modal-body"></div>
  </div>
</div>

<script>
const roleLabels = <?= json_encode($roleLabels) ?>;
const discoveryLabels = <?= json_encode($discoveryLabels) ?>;

function detailItem(label, value) {
  const v = (value === null || value === undefined || value === '') ? '<span class="empty">-</span>' : String(value).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  return `<div class="detail-item"><div class="detail-label">${label}</div><div class="detail-value">${v}</div></div>`;
}

function viewEntry(btn) {
  const e = JSON.parse(atob(btn.dataset.entry));
  const roleLabel = roleLabels[e.role] || e.role;
  document.getElementById('details-modal-title').textContent = `${e.first_name} ${e.last_name} - ${roleLabel}`;

  let html = '<div class="modal-section-title">Contact</div><div class="detail-grid">';
  html += detailItem('Position', e.signup_position ? ((roleLabels[e.role] || e.role) + ' #' + e.signup_position) : '');
  html += detailItem('Referral Code', e.referral_code);
  html += detailItem('Email', e.email);
  html += detailItem('Phone', e.phone);
  html += detailItem('City / Region', e.city_region);
  html += detailItem('Preferred Language', (e.locale || '').toUpperCase());
  html += detailItem('Business Name', e.business_name);
  html += '</div>';

  html += '<div class="modal-section-title">Role Details</div><div class="detail-grid">';
  if (e.role === 'seller') {
    html += detailItem('Business Type', e.seller_business_type);
    html += detailItem('Has Online Store', e.seller_online_store === 'yes' ? 'Yes' : (e.seller_online_store === 'no' ? 'No' : ''));
  } else if (e.role === 'supplier') {
    html += detailItem('Products', e.supplier_products);
    html += detailItem('Service Area', e.supplier_service_area);
  } else if (e.role === 'business') {
    html += detailItem('Sector', e.business_sector);
    html += detailItem('Need', e.business_need);
  } else if (e.role === 'driver') {
    html += detailItem('Area', e.driver_area);
    html += detailItem('Vehicle', e.driver_vehicle);
    html += detailItem('Availability', e.driver_availability);
  } else if (e.role === 'buyer') {
    html += detailItem('Interest', e.buyer_interest);
  } else if (e.role === 'partner') {
    html += detailItem('Interest', e.partner_interest);
  }
  html += '</div>';

  html += '<div class="modal-section-title">Marketing / Source</div><div class="detail-grid">';
  html += detailItem('Discovery Source', discoveryLabels[e.discovery_source] || e.discovery_source);
  html += detailItem('Marketing Consent', e.marketing_consent == 1 ? 'Yes' : 'No');
  html += detailItem('Referral Source', e.referral_source);
  html += detailItem('Referred By Code', e.referred_by);
  html += detailItem('UTM Source', e.utm_source);
  html += detailItem('UTM Medium', e.utm_medium);
  html += detailItem('UTM Campaign', e.utm_campaign);
  html += detailItem('UTM Content', e.utm_content);
  html += '</div>';

  html += '<div class="modal-section-title">System</div><div class="detail-grid">';
  html += detailItem('IP Address', e.ip_address);
  html += detailItem('Status', e.status);
  html += detailItem('Joined', e.created_at);
  html += '</div>';

  document.getElementById('details-modal-body').innerHTML = html;
  document.getElementById('details-modal').classList.add('visible');
}

function closeModal() {
  document.getElementById('details-modal').classList.remove('visible');
}

const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

document.getElementById('select-all').addEventListener('change', function() {
  document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
  updateBulkBar();
});
document.querySelectorAll('.row-check').forEach(cb => cb.addEventListener('change', updateBulkBar));

function updateBulkBar() {
  const n = document.querySelectorAll('.row-check:checked').length;
  document.getElementById('selected-count').textContent = n + ' selected';
  document.getElementById('bulk-bar').classList.toggle('visible', n > 0);
}

document.querySelectorAll('.status-select').forEach(sel => {
  sel.addEventListener('change', async function() {
    const fd = new FormData();
    fd.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', csrf);
    fd.append('id', this.dataset.id);
    fd.append('status', this.value);
    const res  = await fetch('<?= url('/admin/waitlist/status') ?>', { method: 'POST', body: fd });
    const json = await res.json();
    if (!json.success) alert('Failed to update status.');
  });
});

async function deleteEntry(id, email) {
  if (!confirm('Delete ' + email + ' from the waitlist?')) return;
  const fd = new FormData();
  fd.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', csrf);
  fd.append('id', id);
  const res  = await fetch('<?= url('/admin/waitlist/delete') ?>', { method: 'POST', body: fd });
  const json = await res.json();
  if (json.success) {
    document.querySelector('tr[data-id="' + id + '"]')?.remove();
  } else {
    alert('Failed to delete.');
  }
}

async function bulkNotify() {
  const ids = [...document.querySelectorAll('.row-check:checked')].map(cb => cb.value);
  if (!ids.length) return;
  sendInvites(ids);
}

// Beta invites: emails each entry its account-creation link + onboarding guide.
// Partners, unsubscribed and already-registered entries are skipped server-side.
async function sendInvites(ids) {
  if (!confirm('Send the account invite email to ' + ids.length + ' entry(s)? Re-sending reuses the same link.')) return;

  const fd = new FormData();
  fd.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', csrf);
  ids.forEach(id => fd.append('ids[]', id));

  const res  = await fetch('<?= url('/admin/waitlist/notify') ?>', { method: 'POST', body: fd });
  const json = await res.json();
  alert(json.message || (json.success ? 'Done.' : 'Error.'));
  if (json.success) location.reload();
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
