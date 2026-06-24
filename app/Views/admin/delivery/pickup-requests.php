<?php
/**
 * Admin: Supplier Pickup Requests
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$currentPage = 'pickup-requests';

$t = [
    'en' => [
        'title'           => 'Supplier Pickup Requests',
        'subtitle'        => 'Review and schedule supplier-initiated pickup requests',
        'filter_all'      => 'All',
        'filter_pending'  => 'Pending',
        'filter_scheduled'=> 'Scheduled',
        'filter_completed'=> 'Completed',
        'filter_cancelled'=> 'Cancelled',
        'request_s'       => 'request',
        'request_p'       => 'requests',
        'col_supplier'    => 'Supplier',
        'col_date'        => 'Pickup Date',
        'col_window'      => 'Time Window',
        'col_pos'         => 'POs',
        'col_address'     => 'Pickup Address',
        'col_status'      => 'Status',
        'col_driver'      => 'Driver',
        'col_actions'     => 'Actions',
        'empty_title'     => 'No pickup requests',
        'empty_desc'      => 'Suppliers can request pickups from their portal once they have accepted purchase orders.',
        'requested'       => 'Requested',
        'unassigned'      => 'Unassigned',
        'btn_schedule'    => 'Schedule',
        'btn_cancel'      => 'Cancel',
        'modal_schedule'  => 'Schedule Pickup',
        'lbl_driver'      => 'Assign Driver',
        'lbl_notes'       => 'Notes for supplier (optional)',
        'ph_notes'        => 'e.g. Driver will call 30 min before arrival',
        'btn_cancel_m'    => 'Cancel',
        'btn_confirm'     => 'Confirm Schedule',
        'modal_cancel'    => 'Cancel Pickup Request',
        'cancel_confirm'  => 'Are you sure you want to cancel this pickup request? The supplier will be notified.',
        'lbl_reason'      => 'Reason / notes for supplier (optional)',
        'ph_reason'       => 'e.g. No drivers available on that date, please reschedule',
        'btn_keep'        => 'Keep Request',
        'btn_cancel_req'  => 'Cancel Request',
        'js_err_schedule' => 'Error scheduling pickup.',
        'js_err_cancel'   => 'Error cancelling.',
        'js_err_generic'  => 'An error occurred. Please try again.',
        'js_err_short'    => 'An error occurred.',
        'js_scheduling'   => 'Scheduling...',
        'js_cancelling'   => 'Cancelling...',
    ],
    'fr' => [
        'title'           => 'Demandes de ramassage fournisseur',
        'subtitle'        => 'Révisez et planifiez les demandes de ramassage initiées par les fournisseurs',
        'filter_all'      => 'Tous',
        'filter_pending'  => 'En attente',
        'filter_scheduled'=> 'Planifié',
        'filter_completed'=> 'Complété',
        'filter_cancelled'=> 'Annulé',
        'request_s'       => 'demande',
        'request_p'       => 'demandes',
        'col_supplier'    => 'Fournisseur',
        'col_date'        => 'Date de ramassage',
        'col_window'      => 'Plage horaire',
        'col_pos'         => 'BCs',
        'col_address'     => 'Adresse de ramassage',
        'col_status'      => 'Statut',
        'col_driver'      => 'Livreur',
        'col_actions'     => 'Actions',
        'empty_title'     => 'Aucune demande de ramassage',
        'empty_desc'      => "Les fournisseurs peuvent demander des ramassages depuis leur portail une fois qu'ils ont accepté des bons de commande.",
        'requested'       => 'Demandé le',
        'unassigned'      => 'Non assigné',
        'btn_schedule'    => 'Planifier',
        'btn_cancel'      => 'Annuler',
        'modal_schedule'  => 'Planifier le ramassage',
        'lbl_driver'      => 'Assigner un livreur',
        'lbl_notes'       => 'Notes pour le fournisseur (optionnel)',
        'ph_notes'        => 'ex. : Le livreur appellera 30 min avant l\'arrivée',
        'btn_cancel_m'    => 'Annuler',
        'btn_confirm'     => 'Confirmer la planification',
        'modal_cancel'    => 'Annuler la demande de ramassage',
        'cancel_confirm'  => 'Êtes-vous sûr(e) de vouloir annuler cette demande de ramassage ? Le fournisseur sera avisé.',
        'lbl_reason'      => 'Raison / notes pour le fournisseur (optionnel)',
        'ph_reason'       => 'ex. : Aucun livreur disponible ce jour-là, veuillez replanifier',
        'btn_keep'        => 'Garder la demande',
        'btn_cancel_req'  => 'Annuler la demande',
        'js_err_schedule' => 'Erreur lors de la planification du ramassage.',
        'js_err_cancel'   => "Erreur lors de l'annulation.",
        'js_err_generic'  => 'Une erreur s\'est produite. Veuillez réessayer.',
        'js_err_short'    => 'Une erreur s\'est produite.',
        'js_scheduling'   => 'Planification...',
        'js_cancelling'   => 'Annulation...',
    ],
];
$t = $t[$currentLang] ?? $t['en'];

ob_start();
?>

<style>
.page-header { margin-bottom:28px; }
.page-header h1 { font-size:24px; font-weight:700; color:#111827; display:flex; align-items:center; gap:10px; }

.filter-bar { display:flex; gap:10px; align-items:center; margin-bottom:20px; flex-wrap:wrap; }
.filter-btn { padding:7px 16px; border:1px solid #d1d5db; border-radius:8px; background:#fff; font-size:13px; font-weight:600; cursor:pointer; color:#6b7280; transition:all .15s; }
.filter-btn.active, .filter-btn:hover { background:#00b207; color:#fff; border-color:#00b207; }

.card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); overflow:hidden; }

table { width:100%; border-collapse:collapse; }
thead { background:#f9fafb; }
th { padding:11px 16px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; white-space:nowrap; }
td { padding:14px 16px; border-top:1px solid #f3f4f6; font-size:14px; color:#374151; vertical-align:top; }

.badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-pending   { background:#fef3c7; color:#92400e; }
.badge-scheduled { background:#dbeafe; color:#1e40af; }
.badge-completed { background:#dcfce7; color:#166534; }
.badge-cancelled { background:#f3f4f6; color:#6b7280; }

.btn { padding:7px 14px; border:none; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer; transition:all .2s; font-family:inherit; display:inline-flex; align-items:center; gap:6px; }
.btn-primary { background:#00b207; color:#fff; }
.btn-primary:hover { background:#008505; }
.btn-danger  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
.btn-danger:hover { background:#fca5a5; }
.btn-sm { padding:5px 10px; font-size:12px; }

.empty-state { text-align:center; padding:60px 20px; color:#9ca3af; }
.empty-state i { font-size:40px; margin-bottom:14px; display:block; }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); z-index:9999; align-items:center; justify-content:center; }
.modal-overlay.open { display:flex; }
.modal { background:#fff; border-radius:14px; padding:32px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
.modal h3 { font-size:18px; font-weight:700; color:#111827; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
.form-group  { margin-bottom:16px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-group select, .form-group textarea, .form-group input { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:7px; font-size:14px; font-family:inherit; }
.form-group select:focus, .form-group textarea:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.1); }
.modal-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:24px; }
.btn-cancel-modal { background:#f3f4f6; color:#374151; border:none; }
.btn-cancel-modal:hover { background:#e5e7eb; }

.po-list-pills { display:flex; flex-wrap:wrap; gap:4px; }
.po-pill { background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:10px; font-size:12px; font-weight:600; }

.flash-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px; }
.flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px; }
</style>

<div class="page-header">
    <h1><i class="fas fa-truck-loading" style="color:#00b207;"></i> <?= $t['title'] ?></h1>
    <p style="font-size:14px;color:#6b7280;margin-top:4px;"><?= $t['subtitle'] ?></p>
</div>

<?php if (!empty($flash)): ?>
  <div class="flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>

<!-- Status Filter -->
<div class="filter-bar">
    <a href="?status=" class="filter-btn <?= $statusFilter === '' ? 'active' : '' ?>"><?= $t['filter_all'] ?></a>
    <a href="?status=pending"   class="filter-btn <?= $statusFilter === 'pending'   ? 'active' : '' ?>"><?= $t['filter_pending'] ?></a>
    <a href="?status=scheduled" class="filter-btn <?= $statusFilter === 'scheduled' ? 'active' : '' ?>"><?= $t['filter_scheduled'] ?></a>
    <a href="?status=completed" class="filter-btn <?= $statusFilter === 'completed' ? 'active' : '' ?>"><?= $t['filter_completed'] ?></a>
    <a href="?status=cancelled" class="filter-btn <?= $statusFilter === 'cancelled' ? 'active' : '' ?>"><?= $t['filter_cancelled'] ?></a>
    <span style="font-size:13px;color:#6b7280;margin-left:auto;">
        <?= count($requests) ?> <?= count($requests) != 1 ? $t['request_p'] : $t['request_s'] ?>
    </span>
</div>

<div class="card">
<?php if (empty($requests)): ?>
  <div class="empty-state">
    <i class="fas fa-truck"></i>
    <p style="font-size:16px;font-weight:600;color:#374151;margin-bottom:6px;"><?= $t['empty_title'] ?></p>
    <p><?= $t['empty_desc'] ?></p>
  </div>
<?php else: ?>
  <div style="overflow-x:auto;">
  <table>
    <thead>
      <tr>
        <th><?= $t['col_supplier'] ?></th>
        <th><?= $t['col_date'] ?></th>
        <th><?= $t['col_window'] ?></th>
        <th><?= $t['col_pos'] ?></th>
        <th><?= $t['col_address'] ?></th>
        <th><?= $t['col_status'] ?></th>
        <th><?= $t['col_driver'] ?></th>
        <th><?= $t['col_actions'] ?></th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $req): ?>
      <?php
        $poIds = json_decode($req['purchase_order_ids'], true) ?? [];
        $company = $req['company_name'] ?: $req['supplier_name'];
        $badgeMap = ['pending'=>'pending','scheduled'=>'scheduled','completed'=>'completed','cancelled'=>'cancelled'];
        $icons = ['pending'=>'clock','scheduled'=>'calendar-check','completed'=>'check-circle','cancelled'=>'times-circle'];
        $badgeCls = $badgeMap[$req['status']] ?? 'cancelled';
      ?>
      <tr>
        <td>
          <div style="font-weight:600;color:#111827;"><?= htmlspecialchars($company) ?></div>
          <div style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($req['supplier_email']) ?></div>
        </td>
        <td>
          <div style="font-weight:600;"><?= date('M j, Y', strtotime($req['requested_date'])) ?></div>
          <div style="font-size:12px;color:#6b7280;"><?= $t['requested'] ?> <?= date('M j', strtotime($req['created_at'])) ?></div>
        </td>
        <td style="white-space:nowrap;">
          <?= date('g:i A', strtotime($req['requested_time_from'])) ?> –<br>
          <?= date('g:i A', strtotime($req['requested_time_to'])) ?>
        </td>
        <td>
          <div class="po-list-pills">
            <?php foreach ($poIds as $poId): ?>
              <span class="po-pill">#<?= (int)$poId ?></span>
            <?php endforeach; ?>
          </div>
        </td>
        <td style="max-width:200px;">
          <div style="font-size:13px;word-break:break-word;"><?= htmlspecialchars($req['pickup_address']) ?></div>
          <?php if ($req['notes']): ?>
            <div style="font-size:11px;color:#6b7280;margin-top:3px;font-style:italic;"><?= htmlspecialchars(mb_substr($req['notes'], 0, 80)) ?><?= mb_strlen($req['notes']) > 80 ? '…' : '' ?></div>
          <?php endif; ?>
        </td>
        <td>
          <span class="badge badge-<?= $badgeCls ?>">
            <i class="fas fa-<?= $icons[$req['status']] ?? 'circle' ?>"></i>
            <?= ucfirst($req['status']) ?>
          </span>
          <?php if ($req['scheduled_at']): ?>
            <div style="font-size:11px;color:#6b7280;margin-top:3px;"><?= date('M j, Y', strtotime($req['scheduled_at'])) ?></div>
          <?php endif; ?>
        </td>
        <td>
          <?= $req['driver_name'] ? htmlspecialchars($req['driver_name']) : '<span style="color:#9ca3af;">' . $t['unassigned'] . '</span>' ?>
        </td>
        <td>
          <?php if ($req['status'] === 'pending'): ?>
            <button class="btn btn-primary btn-sm btn-schedule" style="margin-bottom:5px;"
                    data-id="<?= $req['id'] ?>"
                    data-company="<?= htmlspecialchars($company) ?>"
                    data-date="<?= htmlspecialchars(date('M j, Y', strtotime($req['requested_date']))) ?>"
                    data-time="<?= htmlspecialchars(date('g:i A', strtotime($req['requested_time_from'])) . '–' . date('g:i A', strtotime($req['requested_time_to']))) ?>">
              <i class="fas fa-calendar-check"></i> <?= $t['btn_schedule'] ?>
            </button>
            <br>
            <button class="btn btn-danger btn-sm btn-cancel-req" data-id="<?= $req['id'] ?>">
              <i class="fas fa-times"></i> <?= $t['btn_cancel'] ?>
            </button>
          <?php elseif ($req['status'] === 'scheduled'): ?>
            <button class="btn btn-danger btn-sm btn-cancel-req" data-id="<?= $req['id'] ?>">
              <i class="fas fa-times"></i> <?= $t['btn_cancel'] ?>
            </button>
          <?php else: ?>
            <span style="color:#9ca3af;font-size:13px;">—</span>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>
</div>

<!-- Schedule Modal -->
<div class="modal-overlay" id="scheduleModal">
  <div class="modal">
    <h3><i class="fas fa-calendar-check" style="color:#00b207;"></i> <?= $t['modal_schedule'] ?></h3>
    <div id="scheduleModalInfo" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:14px;color:#166534;"></div>
    <form id="scheduleForm">
      <?= csrfField() ?>
      <input type="hidden" name="request_id" id="scheduleRequestId">
      <div class="form-group">
        <label for="driver_id"><?= $t['lbl_driver'] ?> <span style="color:#ef4444;">*</span></label>
        <select id="driver_id" name="driver_id" required>
          <option value=""><?= $t['lbl_driver'] ?>…</option>
          <?php foreach ($drivers as $driver): ?>
            <option value="<?= $driver['id'] ?>"><?= htmlspecialchars($driver['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label for="scheduleAdminNotes"><?= $t['lbl_notes'] ?></label>
        <textarea id="scheduleAdminNotes" name="admin_notes" rows="2" placeholder="<?= htmlspecialchars($t['ph_notes']) ?>"></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel-modal" id="closeScheduleModal"><?= $t['btn_cancel_m'] ?></button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> <?= $t['btn_confirm'] ?></button>
      </div>
    </form>
  </div>
</div>

<!-- Cancel Modal -->
<div class="modal-overlay" id="cancelModal">
  <div class="modal">
    <h3><i class="fas fa-times-circle" style="color:#ef4444;"></i> <?= $t['modal_cancel'] ?></h3>
    <p style="font-size:14px;color:#6b7280;margin-bottom:16px;"><?= $t['cancel_confirm'] ?></p>
    <form id="cancelReqForm">
      <?= csrfField() ?>
      <input type="hidden" name="request_id" id="cancelRequestId">
      <div class="form-group">
        <label for="cancelAdminNotes"><?= $t['lbl_reason'] ?></label>
        <textarea id="cancelAdminNotes" name="admin_notes" rows="2" placeholder="<?= htmlspecialchars($t['ph_reason']) ?>"></textarea>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn btn-cancel-modal" id="closeCancelModal"><?= $t['btn_keep'] ?></button>
        <button type="submit" class="btn btn-danger"><i class="fas fa-times"></i> <?= $t['btn_cancel_req'] ?></button>
      </div>
    </form>
  </div>
</div>

<script>
const CSRF_NAME  = '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>';
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
const _PT = {
    errSchedule: <?= json_encode($t['js_err_schedule']) ?>,
    errCancel:   <?= json_encode($t['js_err_cancel']) ?>,
    errGeneric:  <?= json_encode($t['js_err_generic']) ?>,
    errShort:    <?= json_encode($t['js_err_short']) ?>,
    scheduling:  <?= json_encode($t['js_scheduling']) ?>,
    cancelling:  <?= json_encode($t['js_cancelling']) ?>,
    btnConfirm:  <?= json_encode($t['btn_confirm']) ?>,
    btnCancel:   <?= json_encode($t['btn_cancel_req']) ?>,
};

// Schedule modal
document.querySelectorAll('.btn-schedule').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('scheduleRequestId').value = this.dataset.id;
        document.getElementById('scheduleModalInfo').innerHTML =
            '<strong>' + this.dataset.company + '</strong> — ' + this.dataset.date + ' (' + this.dataset.time + ')';
        document.getElementById('scheduleModal').classList.add('open');
    });
});
document.getElementById('closeScheduleModal').addEventListener('click', function() {
    document.getElementById('scheduleModal').classList.remove('open');
});

document.getElementById('scheduleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + _PT.scheduling;
    try {
        const resp = await fetch('<?= url('admin/pickup-requests/schedule') ?>', {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await resp.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || _PT.errSchedule);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check"></i> ' + _PT.btnConfirm;
        }
    } catch (err) {
        alert(_PT.errGeneric);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check"></i> ' + _PT.btnConfirm;
    }
});

// Cancel modal
document.querySelectorAll('.btn-cancel-req').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('cancelRequestId').value = this.dataset.id;
        document.getElementById('cancelModal').classList.add('open');
    });
});
document.getElementById('closeCancelModal').addEventListener('click', function() {
    document.getElementById('cancelModal').classList.remove('open');
});

document.getElementById('cancelReqForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + _PT.cancelling;
    try {
        const resp = await fetch('<?= url('admin/pickup-requests/cancel') ?>', {
            method: 'POST',
            body: new FormData(this)
        });
        const data = await resp.json();
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || _PT.errCancel);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-times"></i> ' + _PT.btnCancel;
        }
    } catch (err) {
        alert(_PT.errShort);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-times"></i> ' + _PT.btnCancel;
    }
});
</script>

<?php
$content = ob_get_clean();
$pageTitle = $currentLang === 'fr' ? 'Demandes de ramassage' : 'Pickup Requests';
require __DIR__ . '/../../admin/layout.php';
?>
