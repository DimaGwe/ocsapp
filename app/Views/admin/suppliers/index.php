<?php
$pageTitle = 'Suppliers';
$currentPage = 'suppliers';
ob_start();
$activeTab = get('tab', 'suppliers');
?>

<style>
  .page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
  }

  .page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
  }

  .btn-primary {
    background: var(--primary);
    color: white;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.2s;
  }

  .btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
  }

  .tabs-container {
    display: flex;
    gap: 4px;
    background: var(--gray-100);
    padding: 4px;
    border-radius: var(--radius-lg);
    margin-bottom: 24px;
    width: fit-content;
  }

  .tab-btn {
    padding: 10px 20px;
    border: none;
    background: transparent;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .tab-btn:hover {
    color: var(--dark);
  }

  .tab-btn.active {
    background: white;
    color: var(--primary);
    box-shadow: var(--shadow-sm);
  }

  .tab-btn .badge-count {
    background: var(--gray-200);
    color: var(--gray-700);
    padding: 2px 8px;
    border-radius: var(--radius-full);
    font-size: 12px;
  }

  .tab-btn.active .badge-count {
    background: var(--primary-100);
    color: var(--primary);
  }

  .filters-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }

  .filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr auto;
    gap: 16px;
  }

  .form-input, .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
  }

  .btn-filter {
    padding: 10px 24px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    cursor: pointer;
    font-weight: 600;
  }

  .table-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }

  thead {
    background: var(--gray-50);
  }

  th {
    padding: 12px 20px;
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    color: var(--gray-600);
    text-transform: uppercase;
  }

  td {
    padding: 16px 20px;
    border-top: 1px solid var(--border);
  }

  tbody tr:hover {
    background: var(--gray-50);
  }

  .badge {
    padding: 4px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
  }

  .badge.active { background: #dcfce7; color: #166534; }
  .badge.inactive { background: #fee2e2; color: #991b1b; }
  .badge.pending { background: #fef3c7; color: #92400e; }
  .badge.pending_verification { background: #fef3c7; color: #92400e; }
  .badge.suspended { background: #fee2e2; color: #991b1b; }
  .badge.accepted { background: #dbeafe; color: #1e40af; }
  .badge.expired { background: #f3f4f6; color: #6b7280; }
  .badge.cancelled { background: #fee2e2; color: #991b1b; }

  .action-btn {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--gray-600);
    padding: 6px;
    margin: 0 4px;
    transition: color 0.2s;
  }

  .action-btn:hover { color: var(--primary); }
  .action-btn.delete:hover { color: #ef4444; }
  .action-btn.resend:hover { color: #10b981; }

  .tab-content {
    display: none;
  }

  .tab-content.active {
    display: block;
  }

  .time-ago {
    font-size: 12px;
    color: var(--gray-500);
  }
</style>

<div class="page-header">
  <h1 class="page-title">Suppliers</h1>
  <div style="display: flex; gap: 12px; flex-wrap: wrap;">
    <a href="<?= url('admin/suppliers/performance') ?>" class="btn-primary" style="background:#6366f1;border:none;">
      <i class="fas fa-chart-bar"></i> Performance
    </a>
    <button onclick="openInviteModal()" class="btn-primary" style="background: #10b981; border: none; cursor: pointer;">
      <i class="fas fa-envelope"></i> Invite Supplier
    </button>
    <a href="<?= url('admin/suppliers/create') ?>" class="btn-primary">
      <i class="fas fa-plus"></i> Add Supplier
    </a>
  </div>
</div>

<!-- Tabs -->
<div class="tabs-container">
  <button class="tab-btn <?= $activeTab === 'suppliers' ? 'active' : '' ?>" onclick="switchTab('suppliers')">
    <i class="fas fa-building"></i> Suppliers
    <span class="badge-count"><?= count($suppliers ?? []) ?></span>
  </button>
  <button class="tab-btn <?= $activeTab === 'invites' ? 'active' : '' ?>" onclick="switchTab('invites')">
    <i class="fas fa-paper-plane"></i> Invitations
    <span class="badge-count" id="invitesCount"><?= count($invites ?? []) ?></span>
  </button>
</div>

<!-- Suppliers Tab -->
<div class="tab-content <?= $activeTab === 'suppliers' ? 'active' : '' ?>" id="suppliersTab">
  <!-- Filters -->
  <div class="filters-card">
    <form method="GET">
      <input type="hidden" name="tab" value="suppliers">
      <div class="filters-grid">
        <input
          type="text"
          name="search"
          placeholder="Search suppliers..."
          value="<?= htmlspecialchars($search ?? '') ?>"
          class="form-input"
        >
        <select name="status" class="form-select">
          <option value="">All Status</option>
          <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
          <option value="inactive" <?= ($status ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
          <option value="suspended" <?= ($status ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended</option>
          <option value="pending_verification" <?= ($status ?? '') === 'pending_verification' ? 'selected' : '' ?>>Pending Verification</option>
          <option value="archived" <?= ($status ?? '') === 'archived' ? 'selected' : '' ?>>Archived</option>
        </select>
        <select name="package" class="form-select">
          <option value="">All Packages</option>
          <option value="Essential" <?= ($package ?? '') === 'Essential' ? 'selected' : '' ?>>Essential</option>
          <option value="Experience" <?= ($package ?? '') === 'Experience' ? 'selected' : '' ?>>Experience</option>
          <option value="Prestige" <?= ($package ?? '') === 'Prestige' ? 'selected' : '' ?>>Prestige</option>
          <option value="Enterprise" <?= ($package ?? '') === 'Enterprise' ? 'selected' : '' ?>>Enterprise</option>
        </select>
        <button type="submit" class="btn-filter">
          <i class="fas fa-filter"></i> Filter
        </button>
      </div>
    </form>
  </div>

  <!-- Table -->
  <div class="table-card">
  <table>
    <thead>
      <tr>
        <th>Company Name</th>
        <th>Contact Person</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Package</th>
        <th>Status</th>
        <th style="text-align: right;">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($suppliers)): ?>
        <?php foreach ($suppliers as $supplier): ?>
          <tr>
            <td>
              <strong><?= htmlspecialchars($supplier['company_name'] ?? $supplier['name']) ?></strong>
            </td>
            <td><?= htmlspecialchars($supplier['contact_person'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($supplier['email'] ?? 'N/A') ?></td>
            <td><?= htmlspecialchars($supplier['phone'] ?? 'N/A') ?></td>
            <td>
              <?php
              $spkg = $supplier['subscription_package'] ?? 'Essential';
              $spkgColors = ['Essential'=>'#00b207','Experience'=>'#3b82f6','Prestige'=>'#7c3aed','Enterprise'=>'#1f2937'];
              $spkgColor  = $spkgColors[$spkg] ?? '#00b207';
              ?>
              <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                <span class="pkg-badge" id="pkg-badge-<?= $supplier['id'] ?>" style="display:inline-flex;align-items:center;gap:5px;background:<?= $spkgColor ?>18;color:<?= $spkgColor ?>;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:700;border:1px solid <?= $spkgColor ?>33;">
                  <i class="fas fa-star" style="font-size:9px;"></i> <?= htmlspecialchars($spkg) ?>
                </span>
                <select onchange="updatePkg(<?= $supplier['id'] ?>, this.value)" style="font-size:11px;padding:2px 4px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;" title="Change package">
                  <?php foreach (['Essential','Experience','Prestige','Enterprise'] as $p): ?>
                  <option value="<?= $p ?>" <?= $spkg === $p ? 'selected' : '' ?>><?= $p ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </td>
            <td>
              <span class="badge <?= $supplier['status'] ?>">
                <?= $supplier['status'] === 'pending_verification' ? 'Pending Verification' : ucfirst($supplier['status']) ?>
              </span>
            </td>
            <td style="text-align: right;">
              <?php if (!empty($supplier['deleted_at'])): ?>
                <button onclick="restoreSupplier(<?= $supplier['id'] ?>)" class="action-btn" title="Restore" style="background:#d1fae5;color:#065f46;">
                  <i class="fas fa-undo"></i>
                </button>
              <?php else: ?>
                <?php if ($supplier['status'] === 'pending_verification' && !empty($supplier['lead_id'])): ?>
                  <a href="<?= url('admin/leads/view?id=' . $supplier['lead_id']) ?>" class="action-btn" title="Review Application" style="background:#fef3c7;color:#d97706;">
                    <i class="fas fa-clipboard-check"></i>
                  </a>
                <?php endif; ?>
                <a href="<?= url('admin/suppliers/edit?id=' . $supplier['id']) ?>" class="action-btn" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                <button onclick="deleteSupplier(<?= $supplier['id'] ?>)" class="action-btn delete" title="Delete">
                  <i class="fas fa-trash"></i>
                </button>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align: center; padding: 40px; color: var(--gray-500);">
            <i class="fas fa-box" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
            No suppliers found
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php
  $baseUrl = 'admin/suppliers';
  $queryParams = ['search' => $search ?? '', 'status' => $status ?? '', 'package' => $package ?? ''];
  require dirname(dirname(__DIR__)) . '/components/pagination.php';
?>
</div><!-- End Suppliers Tab -->

<!-- Invitations Tab -->
<div class="tab-content <?= $activeTab === 'invites' ? 'active' : '' ?>" id="invitesTab">
  <div class="table-card">
    <table>
      <thead>
        <tr>
          <th>Email</th>
          <th>Sent By</th>
          <th>Sent Date</th>
          <th>Expires</th>
          <th>Status</th>
          <th style="text-align: right;">Actions</th>
        </tr>
      </thead>
      <tbody id="invitesTableBody">
        <?php if (!empty($invites)): ?>
          <?php foreach ($invites as $invite): ?>
            <?php
              $isExpired = strtotime($invite['expires_at']) < time() && $invite['status'] === 'pending';
              $isOrphaned = $invite['status'] === 'accepted' && empty($invite['supplier_exists']);
              $displayStatus = $isExpired ? 'expired' : ($isOrphaned ? 'cancelled' : $invite['status']);
            ?>
            <tr data-invite-id="<?= $invite['id'] ?>">
              <td>
                <strong><?= htmlspecialchars($invite['email']) ?></strong>
              </td>
              <td><?= htmlspecialchars($invite['invited_by_name'] ?? 'Admin') ?></td>
              <td>
                <?= date('M j, Y', strtotime($invite['created_at'])) ?>
                <div class="time-ago"><?= date('g:i A', strtotime($invite['created_at'])) ?></div>
              </td>
              <td>
                <?php if ($isExpired): ?>
                  <span style="color: #ef4444;">Expired</span>
                <?php else: ?>
                  <?= date('M j, Y', strtotime($invite['expires_at'])) ?>
                  <div class="time-ago"><?= date('g:i A', strtotime($invite['expires_at'])) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?= $displayStatus ?>">
                  <?= ucfirst($displayStatus) ?>
                </span>
                <?php if ($invite['status'] === 'accepted' && !empty($invite['accepted_at'])): ?>
                  <div class="time-ago"><?= date('M j, Y', strtotime($invite['accepted_at'])) ?></div>
                <?php endif; ?>
                <?php if ($isOrphaned): ?>
                  <div class="time-ago" style="color: #ef4444;">Account removed</div>
                <?php endif; ?>
              </td>
              <td style="text-align: right;">
                <?php if ($invite['status'] === 'pending'): ?>
                  <button onclick="resendInvite(<?= $invite['id'] ?>)" class="action-btn resend" title="Resend Invite">
                    <i class="fas fa-redo"></i>
                  </button>
                  <button onclick="copyInviteUrl('<?= htmlspecialchars(url('supplier/accept-invite?token=' . $invite['token'])) ?>')" class="action-btn" title="Copy Link">
                    <i class="fas fa-link"></i>
                  </button>
                  <button onclick="cancelInvite(<?= $invite['id'] ?>)" class="action-btn delete" title="Cancel Invite">
                    <i class="fas fa-times"></i>
                  </button>
                <?php elseif ($invite['status'] === 'accepted' && !$isOrphaned): ?>
                  <span style="color: #10b981; font-size: 12px;"><i class="fas fa-check-circle"></i> Joined</span>
                <?php elseif ($isOrphaned): ?>
                  <button onclick="resendInvite(<?= $invite['id'] ?>)" class="action-btn resend" title="Resend Invite">
                    <i class="fas fa-redo"></i>
                  </button>
                  <button onclick="deleteInvite(<?= $invite['id'] ?>)" class="action-btn delete" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                <?php elseif ($invite['status'] === 'cancelled' || $isExpired): ?>
                  <button onclick="resendInvite(<?= $invite['id'] ?>)" class="action-btn resend" title="Reactivate & Resend">
                    <i class="fas fa-redo"></i>
                  </button>
                  <button onclick="deleteInvite(<?= $invite['id'] ?>)" class="action-btn delete" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                <?php else: ?>
                  <button onclick="deleteInvite(<?= $invite['id'] ?>)" class="action-btn delete" title="Delete">
                    <i class="fas fa-trash"></i>
                  </button>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 40px; color: var(--gray-500);">
              <i class="fas fa-paper-plane" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
              No invitations sent yet
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div><!-- End Invitations Tab -->

<!-- Invite Modal -->
<div id="inviteModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 16px; padding: 32px; max-width: 500px; width: 90%;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
      <h2 style="font-size: 20px; font-weight: 700; color: var(--dark);">Invite Supplier</h2>
      <button onclick="closeInviteModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--gray-600);">&times;</button>
    </div>

    <div id="inviteForm">
      <div style="margin-bottom: 20px;">
        <label style="display: block; font-size: 14px; font-weight: 600; color: var(--dark); margin-bottom: 8px;">
          Supplier Email <span style="color: #ef4444;">*</span>
        </label>
        <input
          type="email"
          id="supplierEmail"
          placeholder="supplier@example.com"
          style="width: 100%; padding: 12px 16px; border: 2px solid var(--border); border-radius: 8px; font-size: 14px;"
          required
        >
        <p style="font-size: 12px; color: var(--gray-600); margin-top: 4px;">
          An invitation link will be sent to this email
        </p>
      </div>

      <div style="display: flex; gap: 12px; justify-content: flex-end;">
        <button onclick="closeInviteModal()" style="padding: 10px 20px; background: var(--gray-200); color: var(--dark); border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
          Cancel
        </button>
        <button onclick="sendInvite()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
          <i class="fas fa-paper-plane"></i> Send Invite
        </button>
      </div>
    </div>

    <div id="inviteSuccess" style="display: none;">
      <div style="text-align: center; margin-bottom: 24px;">
        <div style="width: 64px; height: 64px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <i class="fas fa-check" style="font-size: 32px; color: #16a34a;"></i>
        </div>
        <h3 style="font-size: 18px; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Invitation Sent!</h3>
        <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 16px;">Copy this link and send it to the supplier:</p>
      </div>

      <div style="background: var(--gray-50); padding: 12px; border-radius: 8px; margin-bottom: 20px; position: relative;">
        <input
          type="text"
          id="inviteLink"
          readonly
          style="width: 100%; background: transparent; border: none; font-size: 13px; color: var(--dark); padding-right: 60px;"
        >
        <button onclick="copyInviteLink()" style="position: absolute; right: 12px; top: 12px; background: var(--primary); color: white; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; cursor: pointer;">
          <i class="fas fa-copy"></i> Copy
        </button>
      </div>

      <button onclick="closeInviteModal()" style="width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
        Done
      </button>
    </div>
  </div>
</div>

<script>
function switchTab(tab) {
  // Update URL without reload
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  window.history.pushState({}, '', url);

  // Update tab buttons
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
  event.target.closest('.tab-btn').classList.add('active');

  // Update tab content
  document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
  document.getElementById(tab + 'Tab').classList.add('active');
}

function openInviteModal() {
  document.getElementById('inviteModal').style.display = 'flex';
  document.getElementById('inviteForm').style.display = 'block';
  document.getElementById('inviteSuccess').style.display = 'none';
  document.getElementById('supplierEmail').value = '';
}

function closeInviteModal() {
  document.getElementById('inviteModal').style.display = 'none';
}

function sendInvite() {
  const email = document.getElementById('supplierEmail').value.trim();

  if (!email) {
    alert('Please enter an email address');
    return;
  }

  if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
    alert('Please enter a valid email address');
    return;
  }

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/send-invite') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, email: email })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('inviteForm').style.display = 'none';
      document.getElementById('inviteSuccess').style.display = 'block';
      document.getElementById('inviteLink').value = data.invite_url;
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error sending invitation'));
}

function copyInviteLink() {
  const linkInput = document.getElementById('inviteLink');
  linkInput.select();
  document.execCommand('copy');

  const btn = event.target.closest('button');
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
  setTimeout(() => {
    btn.innerHTML = originalText;
  }, 2000);
}

function deleteSupplier(id) {
  if (!confirm('Are you sure you want to delete this supplier?')) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/delete') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error deleting supplier'));
}

function restoreSupplier(id) {
  if (!confirm('Are you sure you want to restore this supplier?')) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/restore') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error restoring supplier'));
}

// Close modal when clicking outside
document.getElementById('inviteModal')?.addEventListener('click', function(e) {
  if (e.target === this) {
    closeInviteModal();
  }
});

function copyInviteUrl(url) {
  navigator.clipboard.writeText(url).then(() => {
    alert('Invite link copied to clipboard!');
  }).catch(() => {
    // Fallback for older browsers
    const temp = document.createElement('input');
    temp.value = url;
    document.body.appendChild(temp);
    temp.select();
    document.execCommand('copy');
    document.body.removeChild(temp);
    alert('Invite link copied to clipboard!');
  });
}

function resendInvite(id) {
  if (!confirm('Resend invitation email to this supplier? This will also reactivate cancelled/expired invites.')) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/resend-invite') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload(); // Reload to show updated status
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error resending invitation'));
}

function cancelInvite(id) {
  if (!confirm('Cancel this invitation? The invite link will no longer work.')) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/cancel-invite') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error cancelling invitation'));
}

function deleteInvite(id) {
  if (!confirm('Delete this invitation record?')) return;

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/delete-invite') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, id: id })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      alert(data.message);
      location.reload();
    } else {
      alert('Error: ' + data.message);
    }
  })
  .catch(() => alert('Error deleting invitation'));
}

const pkgColors = { Essential:'#00b207', Experience:'#3b82f6', Prestige:'#7c3aed', Enterprise:'#1f2937' };

function updatePkg(supplierId, pkg) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const csrfName  = document.querySelector('meta[name="csrf-token"]')?.dataset.name || '_csrf_token';

  fetch('<?= url('admin/suppliers/update-package') ?>', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({ [csrfName]: csrfToken, supplier_id: supplierId, subscription_package: pkg })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      const badge = document.getElementById('pkg-badge-' + supplierId);
      if (badge) {
        const c = pkgColors[pkg] || '#00b207';
        badge.style.background = c + '18';
        badge.style.color = c;
        badge.style.borderColor = c + '33';
        badge.innerHTML = '<i class="fas fa-star" style="font-size:9px;"></i> ' + pkg;
      }
    } else {
      alert(data.message || 'Error updating package');
    }
  })
  .catch(() => alert('Error updating package'));
}
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>
