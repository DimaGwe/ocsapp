<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
require __DIR__ . '/layout-header.php';
?>

<style>
.pickup-tabs { display:flex; gap:0; border-bottom:2px solid #e5e7eb; margin-bottom:28px; }
.pickup-tab  { padding:12px 24px; font-size:14px; font-weight:600; color:#6b7280; cursor:pointer; border-bottom:3px solid transparent; margin-bottom:-2px; transition:all 0.2s; background:none; border-top:none; border-left:none; border-right:none; }
.pickup-tab.active { color:#00b207; border-bottom-color:#00b207; }
.pickup-tab:hover:not(.active) { color:#374151; }

.tab-panel { display:none; }
.tab-panel.active { display:block; }

.page-header { margin-bottom:28px; }
.page-header h1 { font-size:24px; font-weight:700; color:#111827; display:flex; align-items:center; gap:10px; }
.page-header p  { font-size:14px; color:#6b7280; margin-top:6px; }

.card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,.08); padding:28px; margin-bottom:24px; }
.card-title { font-size:17px; font-weight:700; color:#111827; margin-bottom:20px; display:flex; align-items:center; gap:8px; }

/* PO selection grid */
.po-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:14px; margin-bottom:24px; }
.po-card { border:2px solid #e5e7eb; border-radius:10px; padding:16px; cursor:pointer; transition:all .2s; position:relative; }
.po-card:hover { border-color:#00b207; background:#f0fdf4; }
.po-card input[type=checkbox] { position:absolute; top:12px; right:12px; width:18px; height:18px; accent-color:#00b207; cursor:pointer; }
.po-card.selected { border-color:#00b207; background:#f0fdf4; }
.po-number { font-size:14px; font-weight:700; color:#00b207; margin-bottom:6px; }
.po-meta   { font-size:12px; color:#6b7280; display:flex; flex-direction:column; gap:2px; }
.po-amount { font-size:15px; font-weight:600; color:#111827; margin-top:8px; }

/* Form */
.form-group  { margin-bottom:20px; }
.form-group label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; transition:border-color .2s; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { outline:none; border-color:#00b207; box-shadow:0 0 0 3px rgba(0,178,7,.1); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.required { color:#ef4444; }

.btn { padding:10px 20px; border:none; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:all .2s; font-family:inherit; display:inline-flex; align-items:center; gap:8px; }
.btn-primary { background:linear-gradient(135deg,#00b207 0%,#008505 100%); color:#fff; }
.btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 10px rgba(0,178,7,.3); }
.btn-danger { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
.btn-danger:hover { background:#fca5a5; }

/* Time options */
.time-select { padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; width:100%; }

/* Status badges */
.badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.badge-pending   { background:#fef3c7; color:#92400e; }
.badge-scheduled { background:#dbeafe; color:#1e40af; }
.badge-completed { background:#dcfce7; color:#166534; }
.badge-cancelled { background:#f3f4f6; color:#6b7280; }

/* History table */
.history-table { width:100%; border-collapse:collapse; }
.history-table th { padding:10px 14px; text-align:left; font-size:12px; font-weight:700; color:#6b7280; text-transform:uppercase; background:#f9fafb; }
.history-table td { padding:14px; border-top:1px solid #f3f4f6; font-size:14px; color:#374151; vertical-align:top; }

.empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
.empty-state i { font-size:40px; margin-bottom:14px; display:block; }

.flash-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px; }
.flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:14px 18px; border-radius:8px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px; }

@media (max-width:640px) {
  .form-row { grid-template-columns:1fr; }
  .po-grid  { grid-template-columns:1fr; }
}
</style>

<div class="page-header">
    <h1><i class="fas fa-truck-loading" style="color:#00b207;"></i> <?= $fr ? 'Planifier un ramassage' : 'Schedule Pickup' ?></h1>
    <p><?= $fr ? 'Demandez un ramassage pour vos bons de commande acceptés' : 'Request a pickup for your accepted purchase orders' ?></p>
</div>

<?php if (!empty($flash)): ?>
  <div class="flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
    <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
    <?= htmlspecialchars($flash['message']) ?>
  </div>
<?php endif; ?>

<!-- Tabs -->
<div class="pickup-tabs">
    <button class="pickup-tab active" data-tab="schedule">
        <i class="fas fa-calendar-plus"></i> <?= $fr ? 'Planifier un ramassage' : 'Schedule New Pickup' ?>
    </button>
    <button class="pickup-tab" data-tab="history">
        <i class="fas fa-history"></i> <?= $fr ? 'Mes demandes' : 'My Requests' ?>
        <?php if (count(array_filter($pickupHistory, fn($r) => $r['status'] === 'pending')) > 0): ?>
          <span style="background:#fef3c7;color:#92400e;padding:1px 7px;border-radius:10px;font-size:11px;margin-left:4px;">
            <?= count(array_filter($pickupHistory, fn($r) => $r['status'] === 'pending')) ?> <?= $fr ? 'en attente' : 'pending' ?>
          </span>
        <?php endif; ?>
    </button>
</div>

<!-- Tab: Schedule New Pickup -->
<div class="tab-panel active" id="tab-schedule">

  <?php if (empty($acceptedOrders)): ?>
    <div class="card">
      <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <p style="font-size:16px;font-weight:600;color:#374151;margin-bottom:8px;"><?= $fr ? 'Aucune commande prête pour le ramassage' : 'No orders ready for pickup' ?></p>
        <p><?= $fr ? 'Vous n\'avez aucun bon de commande en statut <strong>Accepté</strong> pour le moment. Acceptez d\'abord un bon de commande, puis planifiez un ramassage ici.' : 'You have no purchase orders currently in <strong>Receiving</strong> status. Accept a purchase order first, then schedule a pickup here.' ?></p>
        <a href="<?= url('supplier/orders') ?>" class="btn btn-primary" style="margin-top:16px;"><?= $fr ? 'Voir les bons de commande' : 'View Purchase Orders' ?></a>
      </div>
    </div>
  <?php else: ?>
    <form method="POST" action="<?= url('supplier/pickup/request') ?>" id="pickupForm">
        <?= csrfField() ?>

        <!-- Step 1: Select POs -->
        <div class="card">
            <div class="card-title"><i class="fas fa-file-invoice" style="color:#00b207;"></i> <?= $fr ? 'Étape 1 — Sélectionner les bons de commande' : 'Step 1 — Select Purchase Orders' ?></div>
            <p style="font-size:13px;color:#6b7280;margin-bottom:16px;"><?= $fr ? 'Sélectionnez les bons de commande que vous souhaitez faire ramasser. Vous pouvez combiner plusieurs commandes en un seul ramassage.' : 'Select the purchase orders you want picked up in this request. You can combine multiple orders in a single pickup.' ?></p>

            <div class="po-grid">
                <?php foreach ($acceptedOrders as $po): ?>
                <label class="po-card" for="po_<?= $po['id'] ?>">
                    <input type="checkbox" id="po_<?= $po['id'] ?>" name="po_ids[]" value="<?= $po['id'] ?>">
                    <div class="po-number"><?= htmlspecialchars($po['po_number']) ?></div>
                    <div class="po-meta">
                        <span><i class="fas fa-calendar" style="width:14px;"></i> <?= date('M j, Y', strtotime($po['order_date'])) ?></span>
                        <span><i class="fas fa-box" style="width:14px;"></i> <?= (int)$po['item_count'] ?> <?= $fr ? ($po['item_count'] != 1 ? 'articles' : 'article') : ($po['item_count'] != 1 ? 'items' : 'item') ?></span>
                    </div>
                    <div class="po-amount">$<?= number_format($po['total_amount'], 2) ?></div>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Step 2: Pickup Address -->
        <div class="card">
            <div class="card-title"><i class="fas fa-map-marker-alt" style="color:#00b207;"></i> <?= $fr ? 'Étape 2 — Adresse de ramassage' : 'Step 2 — Pickup Address' ?></div>
            <div class="form-group">
                <label for="pickup_address"><?= $fr ? 'Adresse de ramassage' : 'Pickup Address' ?> <span class="required">*</span></label>
                <textarea id="pickup_address" name="pickup_address" rows="3" required placeholder="<?= $fr ? 'Adresse complète où notre livreur doit récupérer les commandes' : 'Full address where our driver should pick up the orders' ?>"><?= htmlspecialchars($defaultAddress) ?></textarea>
                <small style="font-size:12px;color:#6b7280;"><?= $fr ? 'Pré-rempli depuis votre profil. Modifiez si le ramassage se fait à un autre endroit.' : 'Pre-filled from your profile. Edit if pickup is from a different location.' ?></small>
            </div>
        </div>

        <!-- Step 3: Date & Time -->
        <div class="card">
            <div class="card-title"><i class="fas fa-clock" style="color:#00b207;"></i> <?= $fr ? 'Étape 3 — Date et plage horaire souhaitées' : 'Step 3 — Preferred Date & Time Window' ?></div>
            <div class="form-row" style="margin-bottom:20px;">
                <div class="form-group">
                    <label for="requested_date"><?= $fr ? 'Date de ramassage' : 'Pickup Date' ?> <span class="required">*</span></label>
                    <input type="date" id="requested_date" name="requested_date" required
                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
                <div class="form-group"><!-- spacer --></div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="requested_time_from"><?= $fr ? 'Heure la plus tôt' : 'Earliest Time' ?> <span class="required">*</span></label>
                    <select id="requested_time_from" name="requested_time_from" class="time-select" required>
                        <option value=""><?= $fr ? 'Sélectionner une heure' : 'Select time' ?></option>
                        <?php
                        for ($h = 7; $h <= 19; $h++) {
                            foreach (['00', '30'] as $m) {
                                $val = sprintf('%02d:%s', $h, $m);
                                $label = date('g:i A', strtotime("2000-01-01 {$val}:00"));
                                echo "<option value=\"{$val}\">{$label}</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="requested_time_to"><?= $fr ? 'Heure la plus tard' : 'Latest Time' ?> <span class="required">*</span></label>
                    <select id="requested_time_to" name="requested_time_to" class="time-select" required>
                        <option value=""><?= $fr ? 'Sélectionner une heure' : 'Select time' ?></option>
                        <?php
                        for ($h = 8; $h <= 20; $h++) {
                            foreach (['00', '30'] as $m) {
                                $val = sprintf('%02d:%s', $h, $m);
                                $label = date('g:i A', strtotime("2000-01-01 {$val}:00"));
                                echo "<option value=\"{$val}\">{$label}</option>\n";
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Step 4: Notes -->
        <div class="card">
            <div class="card-title"><i class="fas fa-sticky-note" style="color:#00b207;"></i> <?= $fr ? 'Étape 4 — Notes supplémentaires (optionnel)' : 'Step 4 — Additional Notes (Optional)' ?></div>
            <div class="form-group">
                <label for="notes"><?= $fr ? 'Notes pour notre livreur' : 'Notes for our driver' ?></label>
                <textarea id="notes" name="notes" rows="3" placeholder="<?= $fr ? 'ex. Interphone unité 4, quai de chargement à l\'arrière, appeler à l\'arrivée…' : 'e.g. Buzz unit 4, Loading dock at rear, call on arrival…' ?>"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:8px;">
                <i class="fas fa-paper-plane"></i> <?= $fr ? 'Soumettre la demande de ramassage' : 'Submit Pickup Request' ?>
            </button>
        </div>
    </form>
  <?php endif; ?>
</div>

<!-- Tab: My Requests -->
<div class="tab-panel" id="tab-history">
    <div class="card">
        <div class="card-title"><i class="fas fa-history" style="color:#00b207;"></i> <?= $fr ? 'Historique des demandes de ramassage' : 'Pickup Request History' ?></div>

        <?php if (empty($pickupHistory)): ?>
          <div class="empty-state">
            <i class="fas fa-truck"></i>
            <p><?= $fr ? 'Aucune demande de ramassage pour l\'instant.' : 'No pickup requests yet.' ?></p>
          </div>
        <?php else: ?>
          <div style="overflow-x:auto;">
            <table class="history-table">
              <thead>
                <tr>
                  <th><?= $fr ? 'Date demandée' : 'Date Requested' ?></th>
                  <th><?= $fr ? 'Date de ramassage' : 'Pickup Date' ?></th>
                  <th><?= $fr ? 'Plage horaire' : 'Time Window' ?></th>
                  <th><?= $fr ? 'Commandes' : 'Orders' ?></th>
                  <th><?= $fr ? 'Statut' : 'Status' ?></th>
                  <th><?= $fr ? 'Actions' : 'Actions' ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($pickupHistory as $req): ?>
                <tr>
                  <td><?= date('M j, Y', strtotime($req['created_at'])) ?></td>
                  <td><?= date('M j, Y', strtotime($req['requested_date'])) ?></td>
                  <td><?= date('g:i A', strtotime($req['requested_time_from'])) ?> – <?= date('g:i A', strtotime($req['requested_time_to'])) ?></td>
                  <td>
                    <?php $poList = json_decode($req['purchase_order_ids'], true) ?? []; ?>
                    <?= count($poList) ?> BC<?= count($poList) != 1 ? ($fr ? '' : 's') : '' ?>
                  </td>
                  <td>
                    <?php
                    $badgeMap = ['pending'=>'pending','scheduled'=>'scheduled','completed'=>'completed','cancelled'=>'cancelled'];
                    $badgeCls = $badgeMap[$req['status']] ?? 'cancelled';
                    $icons = ['pending'=>'clock','scheduled'=>'calendar-check','completed'=>'check-circle','cancelled'=>'times-circle'];
                    $statusLabels = $fr ? [
                        'pending'   => 'En attente',
                        'scheduled' => 'Planifié',
                        'completed' => 'Complété',
                        'cancelled' => 'Annulé',
                    ] : [
                        'pending'   => 'Pending',
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ];
                    ?>
                    <span class="badge badge-<?= $badgeCls ?>">
                      <i class="fas fa-<?= $icons[$req['status']] ?? 'circle' ?>"></i>
                      <?= $statusLabels[$req['status']] ?? ucfirst($req['status']) ?>
                    </span>
                    <?php if ($req['status'] === 'scheduled' && $req['scheduled_at']): ?>
                      <div style="font-size:11px;color:#6b7280;margin-top:3px;"><?= $fr ? 'Confirmé :' : 'Confirmed:' ?> <?= date('M j, Y', strtotime($req['scheduled_at'])) ?></div>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($req['status'] === 'pending'): ?>
                      <button class="btn btn-danger btn-cancel-pickup" data-id="<?= $req['id'] ?>" style="padding:6px 12px;font-size:12px;">
                        <i class="fas fa-times"></i> <?= $fr ? 'Annuler' : 'Cancel' ?>
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
</div>

<script>
const _pickupSelectPoError = <?= json_encode($fr ? 'Veuillez sélectionner au moins un bon de commande.' : 'Please select at least one purchase order.') ?>;
const _pickupTimeError     = <?= json_encode($fr ? 'L\'heure la plus tard doit être après l\'heure la plus tôt.' : 'Latest time must be after earliest time.') ?>;
const _pickupCancelConfirm = <?= json_encode($fr ? 'Annuler cette demande de ramassage ?' : 'Cancel this pickup request?') ?>;

// Tabs
document.querySelectorAll('.pickup-tab').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.pickup-tab').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById('tab-' + this.dataset.tab).classList.add('active');
    });
});

// PO card selection highlight
document.querySelectorAll('.po-card input[type=checkbox]').forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('.po-card').classList.toggle('selected', this.checked);
    });
});

// Form validation
document.getElementById('pickupForm')?.addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('input[name="po_ids[]"]:checked');
    if (checked.length === 0) {
        e.preventDefault();
        alert(_pickupSelectPoError);
        return;
    }
    const from = document.getElementById('requested_time_from').value;
    const to   = document.getElementById('requested_time_to').value;
    if (from && to && to <= from) {
        e.preventDefault();
        alert(_pickupTimeError);
    }
});

// Cancel pickup request
document.querySelectorAll('.btn-cancel-pickup').forEach(function(btn) {
    btn.addEventListener('click', async function() {
        if (!confirm(_pickupCancelConfirm)) return;
        const id = this.dataset.id;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        try {
            const fd = new FormData();
            fd.append('request_id', id);
            fd.append('<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>', csrfToken);
            const resp = await fetch('<?= url('supplier/pickup/cancel') ?>', { method: 'POST', body: fd });
            const data = await resp.json();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error cancelling request.');
            }
        } catch (err) {
            alert('An error occurred. Please try again.');
        }
    });
});
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
