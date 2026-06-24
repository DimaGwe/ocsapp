<?php
/**
 * OCS Admin Delivery Staff Management
 * File: app/Views/admin/delivery/staff.php
 */

$pageTitle = 'Delivery Staff';
$currentPage = 'delivery-staff';

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Translations
$translations = [
    'en' => [
        'delivery_staff' => 'Delivery Staff',
        'manage_drivers' => 'Manage delivery drivers and their performance',
        'add_driver' => 'Add Driver',
        'search_drivers' => 'Search drivers...',
        'clear' => 'Clear',
        'driver' => 'Driver',
        'contact' => 'Contact',
        'zone' => 'Zone',
        'status' => 'Status',
        'deliveries' => 'Deliveries',
        'rating' => 'Rating',
        'earnings' => 'Earnings',
        'actions' => 'Actions',
        'of' => 'of',
        'active' => 'active',
        'no_ratings' => 'No ratings',
        'no_drivers' => 'No delivery drivers found',
        'previous' => 'Previous',
        'next' => 'Next',
        'page' => 'Page',
        'of_total' => 'of',
        'total_drivers' => 'drivers',
        'total' => 'total',
        'not_assigned' => 'Not assigned',
        'n_a' => 'N/A'
    ],
    'fr' => [
        'delivery_staff' => 'Livreurs',
        'manage_drivers' => 'Gérer les livreurs et leurs performances',
        'add_driver' => 'Ajouter Livreur',
        'search_drivers' => 'Rechercher livreurs...',
        'clear' => 'Effacer',
        'driver' => 'Livreur',
        'contact' => 'Contact',
        'zone' => 'Zone',
        'status' => 'Statut',
        'deliveries' => 'Livraisons',
        'rating' => 'Note',
        'earnings' => 'Gains',
        'actions' => 'Actions',
        'of' => 'sur',
        'active' => 'actif',
        'no_ratings' => 'Pas de notes',
        'no_drivers' => 'Aucun livreur trouvé',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'page' => 'Page',
        'of_total' => 'sur',
        'total_drivers' => 'livreurs',
        'total' => 'total',
        'not_assigned' => 'Non assigné',
        'n_a' => 'N/D'
    ],
];

$t = $translations[$currentLang] ?? $translations['en'];

ob_start();
?>

<style>
/* Page Header */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.page-title {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
    font-family: 'Poppins', sans-serif;
}

.page-subtitle {
    color: var(--gray-600);
    font-size: 16px;
    font-family: 'Poppins', sans-serif;
}

.btn {
    display: inline-flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all var(--transition-base);
    cursor: pointer;
    border: none;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--gray-200);
    color: var(--gray-700);
}

.btn-secondary:hover {
    background: var(--gray-300);
}

/* Card */
.card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
}

/* Form */
.form-row {
    display: flex;
    gap: 16px;
    margin-bottom: 16px;
}

.form-group {
    flex: 1;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--gray-700);
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    transition: all var(--transition-base);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
}

/* Table */
.table-container {
    overflow-x: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: var(--gray-50);
}

.table th {
    padding: 16px 24px;
    text-align: left;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--gray-500);
    border-bottom: 1px solid var(--border);
}

.table td {
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.table-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 14px;
    margin-right: 12px;
    overflow: hidden;
    flex-shrink: 0;
}

.table-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.table-user-info {
    display: flex;
    align-items: center;
}

.table-user-name {
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 4px;
}

.table-user-email {
    font-size: 13px;
    color: var(--gray-500);
}

.status-badge {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: var(--radius-full);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.status-available {
    background: #dcfce7;
    color: #166534;
}

.status-busy {
    background: #ffedd5;
    color: #9a3412;
}

.status-offline {
    background: #f3f4f6;
    color: #374151;
}

.status-break {
    background: #fef9c3;
    color: #854d0e;
}

.rating {
    display: flex;
    align-items: center;
    justify-content: center;
}

.rating-value {
    font-weight: 600;
    margin-left: 6px;
}

.no-rating {
    color: var(--gray-400);
    font-size: 13px;
}

.earnings {
    font-weight: 700;
    color: var(--primary);
    text-align: right;
}

.table-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
}

.action-link {
    color: var(--gray-500);
    transition: color var(--transition-base);
}

.action-link:hover {
    color: var(--primary);
}

.empty-state {
    text-align: center;
    padding: 64px 24px;
}

.empty-state i {
    font-size: 48px;
    color: var(--gray-300);
    margin-bottom: 16px;
}

.empty-state p {
    color: var(--gray-500);
    font-size: 16px;
}

/* Pagination */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 32px;
}

.pagination-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: var(--radius-md);
    background: white;
    border: 1px solid var(--border);
    color: var(--gray-700);
    font-weight: 600;
    transition: all var(--transition-base);
}

.pagination-btn:hover:not(.disabled) {
    background: var(--gray-100);
    border-color: var(--gray-300);
}

.pagination-btn.active {
    background: var(--primary);
    color: white;
    border-color: var(--primary);
}

.pagination-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-info {
    text-align: center;
    margin-top: 16px;
    color: var(--gray-500);
    font-size: 14px;
}

/* Tab Bar */
.tab-bar {
    display: flex;
    gap: 4px;
    margin-bottom: 24px;
    border-bottom: 2px solid var(--border);
}
.tab-btn {
    padding: 10px 20px;
    border: none;
    background: none;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-500);
    cursor: pointer;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: color 0.2s;
    text-decoration: none;
}
.tab-btn.active, .tab-btn:hover { color: var(--primary); }
.tab-btn.active { border-bottom-color: var(--primary); }
.tab-badge {
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 10px;
    min-width: 20px;
    text-align: center;
}

/* Application cards */
.app-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 20px; }
.app-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--border);
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.app-card-header { display: flex; align-items: center; gap: 14px; }
.app-avatar {
    width: 46px; height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white; display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: 700; flex-shrink: 0;
}
.app-name { font-weight: 700; font-size: 15px; color: var(--dark); }
.app-date { font-size: 12px; color: var(--gray-500); margin-top: 2px; }
.app-details { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; }
.app-detail-item { font-size: 13px; }
.app-detail-label { color: var(--gray-500); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px; }
.app-detail-value { color: var(--dark); font-weight: 500; }

/* Pipeline stage badge */
.stage-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 4px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
}
.stage-submitted   { background: #f0f4ff; color: #4f46e5; }
.stage-under_review{ background: #fff7ed; color: #c2410c; }
.stage-interview_requested { background: #fffbeb; color: #b45309; }
.stage-interview_scheduled { background: #ecfdf5; color: #047857; }
.stage-approved    { background: #d1fae5; color: #065f46; }
.stage-rejected    { background: #fee2e2; color: #991b1b; }

/* Pipeline action buttons row */
.app-pipeline-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    border-top: 1px solid var(--border);
    padding-top: 14px;
}
.btn-pipeline {
    padding: 7px 14px; border: none; border-radius: 8px;
    font-size: 12px; font-weight: 600; cursor: pointer;
    font-family: 'Poppins', sans-serif;
    display: flex; align-items: center; gap: 5px;
    transition: all 0.15s;
}
.bp-review   { background: #eff6ff; color: #1d4ed8; }
.bp-review:hover { background: #dbeafe; }
.bp-interview{ background: #fffbeb; color: #b45309; }
.bp-interview:hover { background: #fef3c7; }
.bp-message  { background: #f0fdf4; color: #15803d; }
.bp-message:hover { background: #dcfce7; }
.bp-approve  { background: #10b981; color: #fff; }
.bp-approve:hover { background: #059669; }
.bp-reject   { background: #fee2e2; color: #dc2626; }
.bp-reject:hover { background: #fecaca; }
.bp-bgcheck  { background: #f5f3ff; color: #6d28d9; }
.bp-bgcheck:hover { background: #ede9fe; }

/* Inline message thread toggle */
.msg-thread-toggle {
    font-size: 12px; color: var(--gray-500); cursor: pointer;
    display: flex; align-items: center; gap: 5px;
    text-decoration: underline; text-underline-offset: 2px;
    background: none; border: none; font-family: inherit; padding: 0;
}
.msg-thread {
    border-top: 1px dashed var(--border);
    padding-top: 12px;
    display: none;
}
.msg-thread.open { display: block; }
.msg-list { max-height: 200px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; margin-bottom: 10px; }
.msg-b { max-width: 90%; padding: 8px 12px; border-radius: 10px; font-size: 13px; line-height: 1.4; }
.msg-b.admin { background: #f0f4ff; color: #1e40af; align-self: flex-start; border-bottom-left-radius: 3px; }
.msg-b.applicant { background: #f0fdf4; color: #15803d; align-self: flex-end; border-bottom-right-radius: 3px; }
.msg-b .ms { font-size: 10px; opacity: 0.6; margin-top: 3px; }

/* Modal */
.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
.modal-overlay.open { display: flex; }
.modal-box { background: white; border-radius: 16px; padding: 32px; max-width: 520px; width: 92%; box-shadow: 0 20px 60px rgba(0,0,0,0.2); max-height: 90vh; overflow-y: auto; }
.modal-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; }
.modal-desc { color: var(--gray-500); font-size: 14px; margin-bottom: 20px; }
.modal-field { margin-bottom: 16px; }
.modal-field label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: var(--gray-700); }
.modal-field input, .modal-field textarea, .modal-field select {
    width: 100%; padding: 10px 14px; border: 2px solid var(--border);
    border-radius: 8px; font-family: 'Poppins', sans-serif; font-size: 14px;
}
.modal-field input:focus, .modal-field textarea:focus, .modal-field select:focus { outline: none; border-color: var(--primary); }
.modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
.btn-modal-cancel { padding: 10px 20px; background: var(--gray-100); color: var(--gray-700); border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif; }
.btn-modal-confirm { padding: 10px 20px; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Poppins', sans-serif; }
.btn-modal-confirm.blue  { background: #3b82f6; } .btn-modal-confirm.blue:hover { background: #2563eb; }
.btn-modal-confirm.amber { background: #f59e0b; } .btn-modal-confirm.amber:hover { background: #d97706; }
.btn-modal-confirm.green { background: #10b981; } .btn-modal-confirm.green:hover { background: #059669; }
.btn-modal-confirm.red   { background: #ef4444; } .btn-modal-confirm.red:hover { background: #dc2626; }

/* Add interview slot button */
.add-slot-btn { background: none; border: 1.5px dashed #9ca3af; color: #6b7280; padding: 7px 14px; border-radius: 8px; cursor: pointer; font-family: 'Poppins', sans-serif; font-size: 13px; margin-top: 8px; width: 100%; }
.add-slot-btn:hover { background: #f9fafb; }

/* Responsive */
@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .form-row {
        flex-direction: column;
        gap: 0;
    }

    .table th,
    .table td {
        padding: 12px 16px;
    }

    .pagination {
        flex-wrap: wrap;
    }
}
</style>

<?php $pendingCount = count($applications ?? []); ?>

<!-- Page Header -->
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fa-solid fa-truck text-primary mr-2"></i>
            <?= $t['delivery_staff'] ?>
        </h1>
        <p class="page-subtitle"><?= $t['manage_drivers'] ?></p>
    </div>
    <a href="<?= url('/admin/delivery/add-driver') ?>" class="btn btn-primary">
        <i class="fa-solid fa-plus mr-2"></i> <?= $t['add_driver'] ?>
    </a>
</div>

<!-- Tab Bar -->
<div class="tab-bar">
    <a href="?tab=drivers<?= !empty($search) ? '&search='.urlencode($search) : '' ?>"
       class="tab-btn <?= ($activeTab ?? 'drivers') !== 'applications' ? 'active' : '' ?>">
        <i class="fa-solid fa-id-badge"></i>
        Active Drivers
        <?php if (($total ?? 0) > 0): ?>
            <span class="tab-badge" style="background:#6b7280"><?= $total ?></span>
        <?php endif; ?>
    </a>
    <a href="?tab=applications"
       class="tab-btn <?= ($activeTab ?? '') === 'applications' ? 'active' : '' ?>">
        <i class="fa-solid fa-file-alt"></i>
        Pending Applications
        <?php if ($pendingCount > 0): ?>
            <span class="tab-badge"><?= $pendingCount ?></span>
        <?php endif; ?>
    </a>
</div>

<?php if (($activeTab ?? 'drivers') === 'applications'): ?>
<!-- ===== APPLICATIONS TAB ===== -->
<?php
$stageLabels = [
    'submitted'            => 'Submitted',
    'under_review'         => 'Under Review',
    'interview_requested'  => 'Interview Offered',
    'interview_scheduled'  => 'Interview Booked',
    'approved'             => 'Approved',
    'rejected'             => 'Rejected',
];
?>
<?php if (empty($applications)): ?>
<div class="card" style="text-align:center; padding: 60px 24px;">
    <i class="fa-solid fa-file-circle-check" style="font-size:48px; color:#d1d5db; display:block; margin-bottom:16px;"></i>
    <p style="color:#6b7280; font-size:16px;">No pending driver applications</p>
</div>
<?php else: ?>
<div class="app-grid">
<?php foreach ($applications as $app):
    $stage     = $app['pipeline_stage'] ?? 'submitted';
    $hasUser   = !empty($app['user_id']);
    $hasLead   = !empty($app['lead_id']);
    $appId     = $app['id'];
    $appName   = htmlspecialchars($app['first_name'] . ' ' . $app['last_name']);
    $appNameJs = htmlspecialchars($app['first_name'] . ' ' . $app['last_name'], ENT_QUOTES);
?>
<div class="app-card" id="app-card-<?= $appId ?>">

    <!-- Header: avatar + name + stage badge -->
    <div class="app-card-header">
        <div class="app-avatar"><?= strtoupper(substr($app['first_name'],0,1) . substr($app['last_name'],0,1)) ?></div>
        <div style="flex:1">
            <div class="app-name"><?= $appName ?></div>
            <div class="app-date">Applied <?= date('M j, Y', strtotime($app['created_at'])) ?></div>
        </div>
        <span class="stage-badge stage-<?= $stage ?>"><?= $stageLabels[$stage] ?? ucfirst($stage) ?></span>
    </div>

    <!-- Details grid -->
    <div class="app-details">
        <div class="app-detail-item">
            <div class="app-detail-label">Email</div>
            <div class="app-detail-value"><?= htmlspecialchars($app['email']) ?></div>
        </div>
        <div class="app-detail-item">
            <div class="app-detail-label">Phone</div>
            <div class="app-detail-value"><?= htmlspecialchars($app['phone'] ?: '—') ?></div>
        </div>
        <div class="app-detail-item">
            <div class="app-detail-label">Vehicle</div>
            <div class="app-detail-value"><?= htmlspecialchars(ucfirst($app['vehicle_type'] ?? '—')) ?></div>
        </div>
        <div class="app-detail-item">
            <div class="app-detail-label">City</div>
            <div class="app-detail-value"><?= htmlspecialchars(($app['city'] ?? '') . ', ' . ($app['province'] ?? '')) ?></div>
        </div>
        <div class="app-detail-item">
            <div class="app-detail-label">Shift</div>
            <div class="app-detail-value"><?= htmlspecialchars(ucfirst($app['preferred_shift'] ?? '—')) ?></div>
        </div>
        <div class="app-detail-item">
            <div class="app-detail-label">Experience</div>
            <div class="app-detail-value"><?= ($app['previous_experience'] ?? 'no') === 'yes' ? 'Yes' : 'No' ?></div>
        </div>
    </div>

    <?php if (!empty($app['motivation'])): ?>
    <div style="font-size:12px; color:#374151; background:#f9fafb; border-radius:8px; padding:9px 12px; font-style:italic; border-left:3px solid #c7d2fe;">
        "<?= htmlspecialchars(mb_strimwidth($app['motivation'], 0, 150, '…')) ?>"
    </div>
    <?php endif; ?>

    <?php if (!empty($app['interview_selected_time'])): ?>
    <div style="background:#ecfdf5;border-radius:8px;padding:9px 12px;font-size:13px;color:#065f46;">
        <i class="fa-solid fa-calendar-check"></i>
        <strong>Interview:</strong> <?= date('D, M j, Y \a\t g:i A', strtotime($app['interview_selected_time'])) ?>
    </div>
    <?php endif; ?>

    <!-- Link badges -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <?php if ($hasLead): ?>
        <a href="<?= url('admin/leads/view?id=' . $app['lead_id']) ?>" style="font-size:11px;background:#eff6ff;color:#2563eb;padding:3px 10px;border-radius:12px;text-decoration:none;">
            <i class="fa-solid fa-id-card-clip"></i> CRM Lead
        </a>
        <?php endif; ?>
        <?php if ($hasUser): ?>
        <span style="font-size:11px;background:#f0fdf4;color:#15803d;padding:3px 10px;border-radius:12px;">
            <i class="fa-solid fa-user-check"></i> Portal Account Active
        </span>
        <?php endif; ?>
    </div>

    <!-- Message thread toggle -->
    <button class="msg-thread-toggle" onclick="toggleThread(<?= $appId ?>)">
        <i class="fa-solid fa-comments"></i> <span id="msg-toggle-label-<?= $appId ?>">Show Messages</span>
    </button>
    <div class="msg-thread" id="msg-thread-<?= $appId ?>">
        <div class="msg-list" id="msg-list-<?= $appId ?>">
            <p style="text-align:center;font-size:12px;color:#9ca3af;padding:8px 0;">Loading…</p>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px;">
            <input type="text" id="msg-input-<?= $appId ?>" placeholder="Type a message…" style="flex:1;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:'Poppins',sans-serif;font-size:13px;" onkeydown="if(event.key==='Enter')sendAdminMsg(<?= $appId ?>)">
            <button onclick="sendAdminMsg(<?= $appId ?>)" style="background:#3b82f6;color:#fff;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;font-size:13px;">
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <!-- Pipeline action buttons -->
    <div class="app-pipeline-actions">
        <?php if (in_array($stage, ['submitted'])): ?>
        <button class="btn-pipeline bp-review" onclick="doMarkUnderReview(<?= $appId ?>, '<?= $appNameJs ?>')">
            <i class="fa-solid fa-magnifying-glass"></i> Mark Under Review
        </button>
        <?php endif; ?>

        <?php if (in_array($stage, ['submitted','under_review'])): ?>
        <button class="btn-pipeline bp-interview" onclick="openInterviewModal(<?= $appId ?>, '<?= $appNameJs ?>')">
            <i class="fa-solid fa-calendar-plus"></i> Request Interview
        </button>
        <?php endif; ?>

        <?php if (!in_array($stage, ['approved','rejected'])): ?>
        <?php $bgSt = $app['bgcheck_status'] ?? 'not_requested'; ?>
        <button class="btn-pipeline bp-bgcheck" onclick="requestBgcheckLink(<?= $appId ?>, '<?= $appNameJs ?>', <?= $bgSt === 'requested' ? 'true' : 'false' ?>)">
            <i class="fa-solid fa-file-user"></i>
            <?= $bgSt === 'requested' ? 'Resend Bgcheck Link' : 'Request Background Check' ?>
        </button>
        <button class="btn-pipeline bp-approve" onclick="openApproveModal(<?= $appId ?>, '<?= $appNameJs ?>', <?= $hasUser ? 1 : 0 ?>)">
            <i class="fa-solid fa-check"></i> Approve
        </button>
        <button class="btn-pipeline bp-reject" onclick="openRejectModal(<?= $appId ?>, '<?= $appNameJs ?>')">
            <i class="fa-solid fa-times"></i> Reject
        </button>
        <?php endif; ?>

        <?php if (in_array($stage, ['approved','rejected'])): ?>
        <span style="font-size:12px;color:#9ca3af;align-self:center;">Application <?= ucfirst($stage) ?> &check;</span>
        <?php endif; ?>
    </div>

</div>
<?php endforeach; ?>
</div>
<?php endif; ?>

<?php else: ?>
<!-- ===== DRIVERS TAB ===== -->
<!-- Search & Filter -->
<div class="card">
    <form method="GET" class="form-row">
        <input type="hidden" name="tab" value="drivers">
        <div class="form-group">
            <input type="text"
                   name="search"
                   value="<?= htmlspecialchars($search ?? '') ?>"
                   placeholder="<?= $t['search_drivers'] ?>"
                   class="form-control">
        </div>
        <div class="form-group" style="display: flex; gap: 12px; align-items: flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-search mr-2"></i> Search
            </button>
            <?php if (!empty($search)): ?>
            <a href="<?= url('/admin/delivery/staff') ?>" class="btn btn-secondary"><?= $t['clear'] ?></a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Drivers Table -->
<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><?= $t['driver'] ?></th>
                    <th><?= $t['contact'] ?></th>
                    <th><?= $t['zone'] ?></th>
                    <th class="text-center"><?= $t['status'] ?></th>
                    <th class="text-center"><?= $t['deliveries'] ?></th>
                    <th class="text-center"><?= $t['rating'] ?></th>
                    <th class="text-right"><?= $t['earnings'] ?></th>
                    <th class="text-center"><?= $t['actions'] ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($drivers)): ?>
                    <?php foreach ($drivers as $driver): ?>
                    <tr>
                        <td>
                            <div class="table-user-info">
                                <div class="table-avatar">
                                    <?php if (!empty($driver['avatar'])): ?>
                                      <img src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($driver['avatar'], '/')) ?>"
                                           alt="<?= htmlspecialchars($driver['first_name'] ?? '') ?>">
                                    <?php else: ?>
                                      <?= strtoupper(substr($driver['first_name'] ?? '', 0, 1) . substr($driver['last_name'] ?? '', 0, 1)) ?>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="table-user-name">
                                        <?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?>
                                    </div>
                                    <div class="table-user-email">
                                        <?= htmlspecialchars($driver['email'] ?? '') ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($driver['phone'] ?? $t['n_a']) ?></div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($driver['zone_name'] ?? $t['not_assigned']) ?></div>
                        </td>
                        <td class="text-center">
                            <?php
                            $status = $driver['availability_status'] ?? 'offline';
                            $statusClasses = [
                                'available' => 'status-available',
                                'busy' => 'status-busy',
                                'offline' => 'status-offline',
                                'break' => 'status-break'
                            ];
                            $statusClass = $statusClasses[$status] ?? 'status-offline';
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= ucfirst($status) ?>
                            </span>
                            <?php if (($driver['active_deliveries'] ?? 0) > 0): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                <?= $driver['active_deliveries'] ?> <?= $t['active'] ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="font-bold"><?= $driver['completed_deliveries'] ?? 0 ?></div>
                            <div class="text-xs text-gray-500">
                                <?= $t['of'] ?> <?= $driver['total_deliveries'] ?? 0 ?> <?= strtolower($t['total'] ?? '') ?>
                            </div>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($driver['avg_rating'])): ?>
                            <div class="rating">
                                <i class="fa-solid fa-star text-yellow-400"></i>
                                <span class="rating-value"><?= number_format($driver['avg_rating'], 1) ?></span>
                            </div>
                            <?php else: ?>
                            <div class="no-rating"><?= $t['no_ratings'] ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="earnings">
                            <?= currency($driver['total_earnings'] ?? 0) ?>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="<?= url('/admin/delivery/edit-driver?id=' . ($driver['id'] ?? '')) ?>" 
                                   class="action-link" title="Edit">
                                    <i class="fa-solid fa-edit"></i>
                                </a>
                                <a href="<?= url('/admin/delivery/driver-details?id=' . ($driver['id'] ?? '')) ?>" 
                                   class="action-link" title="View Details">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fa-solid fa-truck"></i>
                            <p><?= $t['no_drivers'] ?></p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Pagination -->
<?php 
$totalPagesValue = $totalPages ?? 1;
$pageValue = $page ?? 1;
$totalValue = $total ?? 0;
?>
<?php if ($totalPagesValue > 1): ?>
<div class="pagination">
    <?php if ($pageValue > 1): ?>
    <a href="?tab=drivers&page=<?= ($pageValue - 1) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
       class="pagination-btn">
        <i class="fa-solid fa-chevron-left"></i>
    </a>
    <?php else: ?>
    <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-left"></i></span>
    <?php endif; ?>

    <?php
    $start = max(1, $pageValue - 2);
    $end   = min($totalPagesValue, $pageValue + 2);
    for ($i = $start; $i <= $end; $i++):
    ?>
    <a href="?tab=drivers&page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
       class="pagination-btn <?= $i === $pageValue ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($pageValue < $totalPagesValue): ?>
    <a href="?tab=drivers&page=<?= ($pageValue + 1) ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>"
       class="pagination-btn">
        <i class="fa-solid fa-chevron-right"></i>
    </a>
    <?php else: ?>
    <span class="pagination-btn disabled"><i class="fa-solid fa-chevron-right"></i></span>
    <?php endif; ?>
</div>
<div class="pagination-info">
    <?= $t['page'] ?> <?= $pageValue ?> <?= $t['of_total'] ?> <?= $totalPagesValue ?>
    (<?= $t['total'] ?>: <?= number_format($totalValue) ?> <?= $t['total_drivers'] ?>)
</div>
<?php endif; ?>

<?php endif; /* end drivers tab */ ?>

<!-- ===== MODALS ===== -->

<!-- Interview Request Modal -->
<div class="modal-overlay" id="interviewModal">
  <div class="modal-box">
    <div class="modal-title"><i class="fa-solid fa-calendar-plus" style="color:#f59e0b"></i> Request Interview</div>
    <div class="modal-desc" id="interviewModalDesc">Propose time slots for the applicant to choose from.</div>
    <div class="modal-field">
      <label>Intro message (optional)</label>
      <textarea id="interviewIntroMsg" rows="2" placeholder="e.g. Hi! We'd love to meet you…"></textarea>
    </div>
    <div class="modal-field">
      <label>Proposed Time Slots <span style="color:#ef4444">*</span></label>
      <div id="interviewSlots">
        <input type="datetime-local" class="interview-slot" style="margin-bottom:8px;">
      </div>
      <button type="button" class="add-slot-btn" onclick="addSlot()"><i class="fa-solid fa-plus"></i> Add another slot</button>
    </div>
    <input type="hidden" id="interviewAppId">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('interviewModal')">Cancel</button>
      <button class="btn-modal-confirm amber" onclick="submitInterview()"><i class="fa-solid fa-paper-plane"></i> Send to Applicant</button>
    </div>
  </div>
</div>

<!-- Approve Modal -->
<div class="modal-overlay" id="approveModal">
  <div class="modal-box">
    <div class="modal-title"><i class="fa-solid fa-check-circle" style="color:#10b981"></i> Approve Application</div>
    <div class="modal-desc" id="approveModalDesc"></div>
    <div id="approveExistingNote" style="display:none;background:#d1fae5;padding:10px 14px;border-radius:8px;font-size:13px;color:#065f46;margin-bottom:14px;">
        <i class="fa-solid fa-user-check"></i> This applicant already has a portal account. Approving will activate it (no new password needed).
    </div>
    <div id="approvePasswordWrap" class="modal-field">
      <label>Temporary Password <span style="color:#ef4444">*</span></label>
      <input type="text" id="approvePassword" placeholder="Min 8 characters">
      <div style="font-size:12px;color:#6b7280;margin-top:4px;">The applicant will receive this by email and should change it on first login.</div>
    </div>
    <input type="hidden" id="approveAppId">
    <input type="hidden" id="approveHasUser">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('approveModal')">Cancel</button>
      <button class="btn-modal-confirm green" id="approveBtnConfirm" onclick="submitApprove()"><i class="fa-solid fa-check"></i> Approve Driver</button>
    </div>
  </div>
</div>

<!-- Reject Modal -->
<div class="modal-overlay" id="rejectModal">
  <div class="modal-box">
    <div class="modal-title"><i class="fa-solid fa-times-circle" style="color:#ef4444"></i> Reject Application</div>
    <div class="modal-desc" id="rejectModalDesc">The applicant will be notified by email. They can reapply after 90 days.</div>
    <div class="modal-field">
      <label>Reason (optional)</label>
      <textarea id="rejectReason" rows="3" placeholder="e.g. Incomplete documentation, area not covered…"></textarea>
    </div>
    <input type="hidden" id="rejectAppId">
    <div class="modal-actions">
      <button class="btn-modal-cancel" onclick="closeModal('rejectModal')">Cancel</button>
      <button class="btn-modal-confirm red" id="rejectBtnConfirm" onclick="submitReject()"><i class="fa-solid fa-times"></i> Reject & Notify</button>
    </div>
  </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const csrfName  = '<?= env("CSRF_TOKEN_NAME", "_csrf_token") ?>';

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}
document.querySelectorAll('.modal-overlay').forEach(o => {
  o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

// ── Request Background Check ────────────────────────────────
async function requestBgcheckLink(appId, name, isResend) {
  const label = isResend ? 'Resend background check request to ' : 'Send background check request to ';
  if (!confirm(label + name + '?')) return;
  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  try {
    const res  = await fetch('<?= url('admin/delivery/bgcheck/request') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) { alert('✅ ' + data.message); location.reload(); }
    else { alert('Error: ' + (data.error || 'Failed to send.')); }
  } catch (e) { alert('Network error. Please try again.'); }
}

// ── Mark Under Review ───────────────────────────────────────
async function doMarkUnderReview(appId, name) {
  if (!confirm('Mark application from ' + name + ' as Under Review?')) return;
  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/under-review') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) {
      alert('✅ ' + data.message);
      location.href = '?tab=applications';
    } else { alert('❌ ' + (data.error || 'Error')); }
  } catch(e) { alert('Network error.'); }
}

// ── Interview Modal ─────────────────────────────────────────
function openInterviewModal(appId, name) {
  document.getElementById('interviewAppId').value = appId;
  document.getElementById('interviewModalDesc').textContent = 'Sending interview invitation to ' + name + '.';
  document.getElementById('interviewIntroMsg').value = '';
  // Reset slots to one
  const slots = document.getElementById('interviewSlots');
  slots.innerHTML = '<input type="datetime-local" class="interview-slot" style="margin-bottom:8px;">';
  document.getElementById('interviewModal').classList.add('open');
}

function addSlot() {
  const slots = document.getElementById('interviewSlots');
  const inp = document.createElement('input');
  inp.type = 'datetime-local';
  inp.className = 'interview-slot';
  inp.style.marginBottom = '8px';
  slots.appendChild(inp);
}

async function submitInterview() {
  const appId      = document.getElementById('interviewAppId').value;
  const introMsg   = document.getElementById('interviewIntroMsg').value.trim();
  const slotInputs = document.querySelectorAll('.interview-slot');
  const times = Array.from(slotInputs).map(i => i.value).filter(v => v);
  if (!times.length) { alert('Please add at least one time slot.'); return; }

  const btn = document.querySelector('#interviewModal .btn-modal-confirm');
  btn.disabled = true; btn.textContent = 'Sending…';

  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  fd.append('intro_message', introMsg);
  times.forEach(t => fd.append('proposed_times[]', t));

  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/request-interview') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) {
      alert('✅ ' + data.message);
      location.href = '?tab=applications';
    } else {
      alert('❌ ' + (data.error || 'Error'));
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send to Applicant';
    }
  } catch(e) { alert('Network error.'); btn.disabled = false; }
}

// ── Approve Modal ───────────────────────────────────────────
function openApproveModal(appId, name, hasUser) {
  document.getElementById('approveAppId').value  = appId;
  document.getElementById('approveHasUser').value = hasUser;
  document.getElementById('approveModalDesc').textContent = 'Approving application from ' + name + '.';

  const existNote  = document.getElementById('approveExistingNote');
  const pwdWrap    = document.getElementById('approvePasswordWrap');
  if (hasUser) {
    existNote.style.display  = 'block';
    pwdWrap.style.display    = 'none';
    document.getElementById('approvePassword').value = '__existing__';
  } else {
    existNote.style.display  = 'none';
    pwdWrap.style.display    = 'block';
    document.getElementById('approvePassword').value = 'Driver' + Math.random().toString(36).slice(-6).toUpperCase() + '!';
  }
  document.getElementById('approveModal').classList.add('open');
}

async function submitApprove() {
  const appId    = document.getElementById('approveAppId').value;
  const hasUser  = document.getElementById('approveHasUser').value == '1';
  const password = document.getElementById('approvePassword').value.trim();

  if (!hasUser && (!password || password.length < 8)) {
    alert('Password must be at least 8 characters.');
    return;
  }

  const btn = document.getElementById('approveBtnConfirm');
  btn.disabled = true; btn.textContent = 'Approving…';

  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  if (hasUser) {
    fd.append('use_existing_account', '1');
    fd.append('temp_password', '');
  } else {
    fd.append('temp_password', password);
  }

  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/approve') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) {
      alert('✅ ' + data.message);
      location.href = '?tab=applications';
    } else {
      alert('❌ ' + (data.error || 'Error'));
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-check"></i> Approve Driver';
    }
  } catch(e) { alert('Network error.'); btn.disabled = false; }
}

// ── Reject Modal ────────────────────────────────────────────
function openRejectModal(appId, name) {
  document.getElementById('rejectAppId').value = appId;
  document.getElementById('rejectModalDesc').textContent = 'Rejecting application from ' + name + '. They will be notified and can reapply after 90 days.';
  document.getElementById('rejectReason').value = '';
  document.getElementById('rejectModal').classList.add('open');
}

async function submitReject() {
  const appId  = document.getElementById('rejectAppId').value;
  const reason = document.getElementById('rejectReason').value.trim();
  const btn    = document.getElementById('rejectBtnConfirm');
  btn.disabled = true; btn.textContent = 'Rejecting…';

  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  fd.append('reason', reason);

  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/reject') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) {
      alert('✅ ' + data.message);
      location.href = '?tab=applications';
    } else {
      alert('❌ ' + (data.error || 'Error'));
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-times"></i> Reject & Notify';
    }
  } catch(e) { alert('Network error.'); btn.disabled = false; }
}

// ── Inline message thread ───────────────────────────────────
const threadCache = {};

async function toggleThread(appId) {
  const thread = document.getElementById('msg-thread-' + appId);
  const label  = document.getElementById('msg-toggle-label-' + appId);
  const isOpen = thread.classList.contains('open');

  if (isOpen) {
    thread.classList.remove('open');
    label.textContent = 'Show Messages';
  } else {
    thread.classList.add('open');
    label.textContent = 'Hide Messages';
    if (!threadCache[appId]) await loadMessages(appId);
  }
}

async function loadMessages(appId) {
  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/messages') ?>?application_id=' + appId);
    const data = await res.json();
    renderMessages(appId, data.messages || []);
    threadCache[appId] = true;
  } catch(e) {
    document.getElementById('msg-list-' + appId).innerHTML = '<p style="color:#ef4444;font-size:12px;text-align:center;">Failed to load messages.</p>';
  }
}

function renderMessages(appId, msgs) {
  const list = document.getElementById('msg-list-' + appId);
  if (!msgs.length) {
    list.innerHTML = '<p style="text-align:center;font-size:12px;color:#9ca3af;padding:6px 0;">No messages yet.</p>';
    return;
  }
  list.innerHTML = msgs.map(m => {
    const cls  = m.sender_type === 'admin' ? 'admin' : 'applicant';
    const who  = m.sender_type === 'admin' ? 'You (Admin)' : 'Applicant';
    const time = new Date(m.created_at).toLocaleString('en-CA', {month:'short', day:'numeric', hour:'numeric', minute:'2-digit'});
    return `<div style="display:flex;flex-direction:column;">
      <div class="msg-b ${cls}">
        <div style="font-size:10px;font-weight:700;opacity:.6;margin-bottom:3px;">${who}</div>
        ${m.message.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>')}
        <div class="ms">${time}</div>
      </div>
    </div>`;
  }).join('');
  list.scrollTop = list.scrollHeight;
}

async function sendAdminMsg(appId) {
  const input = document.getElementById('msg-input-' + appId);
  const text  = input.value.trim();
  if (!text) return;

  const fd = new FormData();
  fd.append(csrfName, csrfToken);
  fd.append('application_id', appId);
  fd.append('message', text);

  try {
    const res  = await fetch('<?= url('admin/delivery/pipeline/send-message') ?>', { method:'POST', body:fd });
    const data = await res.json();
    if (data.success) {
      input.value = '';
      threadCache[appId] = false;  // force reload
      await loadMessages(appId);
    } else { alert('❌ ' + (data.error || 'Error')); }
  } catch(e) { alert('Network error.'); }
}

// Auto-open message thread when arriving from bell notification (?app=X)
(function() {
  const focusId = parseInt(new URLSearchParams(window.location.search).get('app') || '0');
  if (!focusId) return;
  window.addEventListener('DOMContentLoaded', async function() {
    const card = document.getElementById('app-card-' + focusId);
    if (card) {
      card.scrollIntoView({ behavior: 'smooth', block: 'center' });
      await toggleThread(focusId);
    }
  });
})();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
