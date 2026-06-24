<?php
$pageTitle   = 'Delivery Zones';
$currentPage = 'zones';
$currentLang = $_SESSION['language'] ?? 'fr';
ob_start();
?>
<style>
.page-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:28px; flex-wrap:wrap; gap:12px; }
.page-title  { font-size:22px; font-weight:700; color:var(--gray-900); margin-bottom:4px; }
.page-subtitle { font-size:14px; color:var(--gray-500); }
.btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:var(--radius-md); font-size:13px; font-weight:600; cursor:pointer; border:none; transition:var(--transition-base); text-decoration:none; }
.btn-primary   { background:var(--primary); color:#fff; }
.btn-primary:hover { background:var(--primary-600); }
.btn-secondary { background:var(--gray-200); color:var(--gray-700); }
.btn-secondary:hover { background:var(--gray-300); }
.btn-warning   { background:#f59e0b; color:#fff; }
.btn-warning:hover { background:#d97706; }
.btn-sm { padding:6px 12px; font-size:12px; }

.zones-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:20px; }

.zone-card { background:#fff; border-radius:var(--radius-lg); box-shadow:var(--shadow-sm); border:1px solid var(--border); overflow:hidden; transition:box-shadow var(--transition-base); }
.zone-card:hover { box-shadow:var(--shadow-md); }
.zone-card-header { padding:16px 20px 12px; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:flex-start; }
.zone-name { font-size:16px; font-weight:700; color:var(--gray-900); }
.zone-code { font-size:11px; color:var(--gray-400); font-weight:500; margin-left:6px; }
.zone-location { font-size:12px; color:var(--gray-500); margin-top:2px; }
.badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:var(--radius-full); font-size:11px; font-weight:600; }
.badge-active   { background:#dcfce7; color:#166534; }
.badge-inactive { background:var(--gray-200); color:var(--gray-600); }

.zone-stats { padding:12px 20px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; }
.stat-item { background:var(--gray-50); border-radius:var(--radius-sm); padding:10px; text-align:center; }
.stat-label { font-size:10px; text-transform:uppercase; letter-spacing:.5px; color:var(--gray-500); font-weight:600; margin-bottom:2px; }
.stat-value { font-size:15px; font-weight:700; color:var(--gray-900); }

.zone-footer { padding:12px 20px; border-top:1px solid var(--border); display:flex; gap:8px; justify-content:space-between; align-items:center; }
.zone-meta { font-size:11px; color:var(--gray-500); }

.empty-state { background:#fff; border-radius:var(--radius-lg); border:1px solid var(--border); padding:60px 20px; text-align:center; color:var(--gray-500); grid-column:1/-1; }
.empty-state i { font-size:48px; color:var(--gray-300); margin-bottom:16px; display:block; }

.info-box { background:#eff6ff; border-left:4px solid #3b82f6; border-radius:var(--radius-md); padding:16px 20px; margin-top:24px; font-size:13px; color:#1e40af; }
.info-box strong { font-weight:600; }

/* Modal */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:200; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal-box { background:#fff; border-radius:var(--radius-xl); box-shadow:var(--shadow-lg); width:100%; max-width:640px; max-height:90vh; overflow-y:auto; }
.modal-header { display:flex; justify-content:space-between; align-items:center; padding:20px 24px; border-bottom:1px solid var(--border); }
.modal-header h2 { font-size:18px; font-weight:700; color:var(--gray-900); }
.modal-close { background:none; border:none; font-size:22px; color:var(--gray-400); cursor:pointer; line-height:1; }
.modal-close:hover { color:var(--gray-700); }
.modal-body { padding:24px; }
.modal-body .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
.modal-body .form-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
.modal-body .form-row.cols-1 { grid-template-columns:1fr; }
.form-group { margin-bottom:0; }
.form-label { display:block; font-size:12px; font-weight:600; color:var(--gray-700); margin-bottom:6px; }
.form-input, .form-textarea { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:8px 12px; font-size:13px; font-family:inherit; color:var(--gray-900); transition:border-color var(--transition-base); }
.form-input:focus, .form-textarea:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(0,178,7,.1); }
.form-textarea { resize:none; }
.form-check { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--gray-700); }
.form-check input[type=checkbox] { width:16px; height:16px; accent-color:var(--primary); cursor:pointer; }
.form-error { display:none; background:#fef2f2; border:1px solid #fecaca; border-radius:var(--radius-sm); padding:8px 12px; font-size:12px; color:#b91c1c; margin-top:12px; }
.modal-footer { display:flex; gap:10px; padding:16px 24px; border-top:1px solid var(--border); }
.modal-footer .btn { flex:1; justify-content:center; }

#flashMsg { display:none; margin-bottom:20px; }
</style>

<!-- Flash -->
<div id="flashMsg" class="alert"></div>

<!-- Header -->
<div class="page-header">
    <div>
        <h1 class="page-title"><i class="fas fa-map-marked-alt" style="color:var(--primary);margin-right:8px;"></i><?= $currentLang === 'fr' ? 'Zones de livraison' : 'Delivery Zones' ?></h1>
        <p class="page-subtitle"><?= $currentLang === 'fr' ? 'Gérer les zones de livraison et les tarifs' : 'Manage delivery areas and pricing' ?></p>
    </div>
    <button onclick="openZoneModal()" class="btn btn-primary">
        <i class="fas fa-plus"></i> <?= $currentLang === 'fr' ? 'Ajouter une zone' : 'Add Zone' ?>
    </button>
</div>

<!-- Zones Grid -->
<div class="zones-grid">
    <?php if (!empty($zones)): ?>
        <?php foreach ($zones as $zone): ?>
        <div class="zone-card" id="zone-card-<?= $zone['id'] ?>">
            <div class="zone-card-header">
                <div>
                    <div>
                        <span class="zone-name"><?= htmlspecialchars($zone['name']) ?></span>
                        <span class="zone-code"><?= htmlspecialchars($zone['code']) ?></span>
                    </div>
                    <div class="zone-location"><i class="fas fa-location-dot" style="margin-right:4px;"></i><?= htmlspecialchars($zone['city'] . ($zone['state'] ? ', ' . $zone['state'] : '')) ?></div>
                </div>
                <span id="zone-status-<?= $zone['id'] ?>" class="badge <?= $zone['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                    <?= $zone['is_active'] ? ($currentLang === 'fr' ? 'Actif' : 'Active') : ($currentLang === 'fr' ? 'Inactif' : 'Inactive') ?>
                </span>
            </div>
            <div class="zone-stats">
                <div class="stat-item">
                    <div class="stat-label"><?= $currentLang === 'fr' ? 'Frais de base' : 'Base Fee' ?></div>
                    <div class="stat-value"><?= currency($zone['base_fee']) ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label"><?= $currentLang === 'fr' ? 'Par km' : 'Per KM' ?></div>
                    <div class="stat-value"><?= currency($zone['per_km_fee']) ?></div>
                </div>
                <div class="stat-item">
                    <div class="stat-label"><?= $currentLang === 'fr' ? 'Dist. max' : 'Max Dist.' ?></div>
                    <div class="stat-value"><?= number_format($zone['max_distance_km'] ?? 0, 1) ?> km</div>
                </div>
            </div>
            <div class="zone-footer">
                <div class="zone-meta">
                    <i class="fas fa-users" style="margin-right:4px;"></i><?= $zone['driver_count'] ?? 0 ?> <?= $currentLang === 'fr' ? 'livreurs' : 'drivers' ?>
                    &nbsp;·&nbsp;
                    <i class="fas fa-star" style="margin-right:4px;"></i><?= $currentLang === 'fr' ? 'Priorité' : 'Priority' ?>: <?= $zone['priority'] ?? 0 ?>
                </div>
                <div style="display:flex;gap:6px;">
                    <button onclick="openZoneModal(<?= $zone['id'] ?>)" class="btn btn-secondary btn-sm">
                        <i class="fas fa-edit"></i> <?= $currentLang === 'fr' ? 'Modifier' : 'Edit' ?>
                    </button>
                    <button id="toggle-btn-<?= $zone['id'] ?>"
                            onclick="toggleZoneStatus(<?= $zone['id'] ?>, <?= $zone['is_active'] ? 'false' : 'true' ?>)"
                            class="btn btn-sm <?= $zone['is_active'] ? 'btn-secondary' : 'btn-primary' ?>">
                        <?= $zone['is_active']
                            ? ($currentLang === 'fr' ? 'Désactiver' : 'Deactivate')
                            : ($currentLang === 'fr' ? 'Activer' : 'Activate') ?>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-map-marked-alt"></i>
            <p style="font-size:16px;font-weight:600;margin-bottom:8px;"><?= $currentLang === 'fr' ? 'Aucune zone configurée' : 'No delivery zones configured' ?></p>
            <p style="font-size:13px;margin-bottom:20px;"><?= $currentLang === 'fr' ? 'Ajoutez votre première zone pour commencer.' : 'Add your first zone to get started.' ?></p>
            <button onclick="openZoneModal()" class="btn btn-primary"><i class="fas fa-plus"></i> <?= $currentLang === 'fr' ? 'Ajouter une zone' : 'Add Zone' ?></button>
        </div>
    <?php endif; ?>
</div>

<div class="info-box">
    <strong><?= $currentLang === 'fr' ? 'À propos des zones de livraison' : 'About Delivery Zones' ?></strong>
    <ul style="margin-top:8px;padding-left:18px;line-height:1.8;">
        <li><strong><?= $currentLang === 'fr' ? 'Frais de base' : 'Base Fee' ?>:</strong> <?= $currentLang === 'fr' ? 'Frais fixes pour les livraisons dans cette zone' : 'Fixed charge for deliveries within this zone' ?></li>
        <li><strong><?= $currentLang === 'fr' ? 'Tarif par km' : 'Per KM Rate' ?>:</strong> <?= $currentLang === 'fr' ? 'Frais supplémentaires par kilomètre' : 'Additional charge per kilometer traveled' ?></li>
        <li><strong><?= $currentLang === 'fr' ? 'Distance max' : 'Max Distance' ?>:</strong> <?= $currentLang === 'fr' ? 'Distance de livraison maximale autorisée' : 'Maximum delivery distance allowed in this zone' ?></li>
        <li><strong><?= $currentLang === 'fr' ? 'Priorité' : 'Priority' ?>:</strong> <?= $currentLang === 'fr' ? 'Les chiffres bas ont la priorité pour l\'assignation' : 'Lower numbers get priority when assigning drivers' ?></li>
    </ul>
</div>

<!-- Add / Edit Modal -->
<div id="zoneModal" class="modal-overlay" onclick="if(event.target===this)closeZoneModal()">
    <div class="modal-box">
        <div class="modal-header">
            <h2 id="modalTitle"><?= $currentLang === 'fr' ? 'Ajouter une zone' : 'Add Delivery Zone' ?></h2>
            <button class="modal-close" onclick="closeZoneModal()">&times;</button>
        </div>
        <form id="zoneForm" onsubmit="submitZoneForm(event)">
            <div class="modal-body">
                <input type="hidden" id="zoneId" name="id">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Nom de la zone' : 'Zone Name' ?> *</label>
                        <input type="text" name="name" id="f_name" required placeholder="<?= $currentLang === 'fr' ? 'ex. Centre-ville' : 'e.g. Downtown' ?>" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Code (court)' : 'Code (short)' ?> *</label>
                        <input type="text" name="code" id="f_code" required placeholder="e.g. DT" maxlength="20" style="text-transform:uppercase" class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Ville' : 'City' ?> *</label>
                        <input type="text" name="city" id="f_city" required placeholder="Santo Domingo" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Province / État' : 'State / Province' ?></label>
                        <input type="text" name="state" id="f_state" placeholder="Distrito Nacional" class="form-input">
                    </div>
                </div>

                <div class="form-row cols-1">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Pays' : 'Country' ?></label>
                        <input type="text" name="country" id="f_country" value="Dominican Republic" class="form-input">
                    </div>
                </div>

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Frais de base ($)' : 'Base Fee ($)' ?></label>
                        <input type="number" name="base_fee" id="f_base_fee" min="0" step="0.01" value="50.00" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Tarif par km ($)' : 'Per KM Fee ($)' ?></label>
                        <input type="number" name="per_km_fee" id="f_per_km_fee" min="0" step="0.01" value="10.00" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Dist. max (km)' : 'Max Distance (km)' ?></label>
                        <input type="number" name="max_distance_km" id="f_max_distance_km" min="0" step="0.1" value="10.0" class="form-input">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Temps estimé (min)' : 'Est. Time (minutes)' ?></label>
                        <input type="number" name="estimated_time" id="f_estimated_time" min="1" value="30" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Priorité (0 = plus haute)' : 'Priority (0 = highest)' ?></label>
                        <input type="number" name="priority" id="f_priority" min="0" value="0" class="form-input">
                    </div>
                </div>

                <div class="form-row cols-1">
                    <div class="form-group">
                        <label class="form-label"><?= $currentLang === 'fr' ? 'Notes' : 'Notes' ?></label>
                        <textarea name="notes" id="f_notes" rows="2" class="form-textarea form-input"
                                  placeholder="<?= $currentLang === 'fr' ? 'Notes optionnelles...' : 'Optional notes...' ?>"></textarea>
                    </div>
                </div>

                <label class="form-check">
                    <input type="checkbox" name="is_active" id="f_is_active" value="1" checked>
                    <?= $currentLang === 'fr' ? 'Zone active (les livreurs peuvent être assignés)' : 'Active zone (drivers can be assigned)' ?>
                </label>

                <div id="formError" class="form-error"></div>
            </div>
            <div class="modal-footer">
                <button type="submit" id="submitBtn" class="btn btn-primary">
                    <?= $currentLang === 'fr' ? 'Enregistrer' : 'Save Zone' ?>
                </button>
                <button type="button" onclick="closeZoneModal()" class="btn btn-secondary">
                    <?= $currentLang === 'fr' ? 'Annuler' : 'Cancel' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
const lang = '<?= $currentLang ?>';

function showFlash(msg, ok) {
    const el = document.getElementById('flashMsg');
    el.textContent = msg;
    el.className = 'alert ' + (ok ? 'alert-success' : 'alert-error');
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function openZoneModal(id) {
    document.getElementById('formError').style.display = 'none';
    document.getElementById('zoneForm').reset();
    document.getElementById('f_country').value       = 'Dominican Republic';
    document.getElementById('f_base_fee').value      = '50.00';
    document.getElementById('f_per_km_fee').value    = '10.00';
    document.getElementById('f_max_distance_km').value = '10.0';
    document.getElementById('f_estimated_time').value = '30';
    document.getElementById('f_priority').value      = '0';
    document.getElementById('f_is_active').checked   = true;

    if (id) {
        document.getElementById('modalTitle').textContent = lang === 'fr' ? 'Modifier la zone' : 'Edit Delivery Zone';
        document.getElementById('zoneId').value = id;
        document.getElementById('submitBtn').textContent = lang === 'fr' ? 'Mettre à jour' : 'Update Zone';
        fetch(`<?= url('admin/delivery/zones/get') ?>?id=${id}`)
            .then(r => r.json())
            .then(z => {
                if (z.error) { showFlash(z.error, false); return; }
                document.getElementById('f_name').value            = z.name ?? '';
                document.getElementById('f_code').value            = z.code ?? '';
                document.getElementById('f_city').value            = z.city ?? '';
                document.getElementById('f_state').value           = z.state ?? '';
                document.getElementById('f_country').value         = z.country ?? 'Dominican Republic';
                document.getElementById('f_base_fee').value        = z.base_fee ?? '50.00';
                document.getElementById('f_per_km_fee').value      = z.per_km_fee ?? '10.00';
                document.getElementById('f_max_distance_km').value = z.max_distance_km ?? '10.0';
                document.getElementById('f_estimated_time').value  = z.estimated_time ?? '30';
                document.getElementById('f_priority').value        = z.priority ?? '0';
                document.getElementById('f_notes').value           = z.notes ?? '';
                document.getElementById('f_is_active').checked     = z.is_active == 1;
                document.getElementById('zoneModal').classList.add('open');
            });
    } else {
        document.getElementById('modalTitle').textContent = lang === 'fr' ? 'Ajouter une zone' : 'Add Delivery Zone';
        document.getElementById('zoneId').value = '';
        document.getElementById('submitBtn').textContent = lang === 'fr' ? 'Enregistrer' : 'Save Zone';
        document.getElementById('zoneModal').classList.add('open');
    }
}

function closeZoneModal() {
    document.getElementById('zoneModal').classList.remove('open');
}

function submitZoneForm(e) {
    e.preventDefault();
    const btn   = document.getElementById('submitBtn');
    const errEl = document.getElementById('formError');
    const id    = document.getElementById('zoneId').value;
    const url   = id ? '<?= url('admin/delivery/zones/update') ?>' : '<?= url('admin/delivery/zones/create') ?>';
    const data  = new FormData(document.getElementById('zoneForm'));
    if (!document.getElementById('f_is_active').checked) data.delete('is_active');
    data.set('_csrf_token', csrfToken);

    btn.disabled = true;
    btn.textContent = lang === 'fr' ? 'Enregistrement...' : 'Saving...';
    errEl.style.display = 'none';

    fetch(url, { method:'POST', body:data })
        .then(r => r.json())
        .then(res => {
            if (res.error) {
                errEl.textContent = res.error;
                errEl.style.display = 'block';
                btn.disabled = false;
                btn.textContent = id ? (lang === 'fr' ? 'Mettre à jour' : 'Update Zone') : (lang === 'fr' ? 'Enregistrer' : 'Save Zone');
            } else {
                showFlash(res.message, true);
                closeZoneModal();
                setTimeout(() => location.reload(), 800);
            }
        })
        .catch(() => {
            errEl.textContent = lang === 'fr' ? 'Erreur réseau. Veuillez réessayer.' : 'Network error. Please try again.';
            errEl.style.display = 'block';
            btn.disabled = false;
            btn.textContent = id ? (lang === 'fr' ? 'Mettre à jour' : 'Update Zone') : (lang === 'fr' ? 'Enregistrer' : 'Save Zone');
        });
}

function toggleZoneStatus(id, activate) {
    const label = activate ? (lang === 'fr' ? 'activer' : 'activate') : (lang === 'fr' ? 'désactiver' : 'deactivate');
    if (!confirm(lang === 'fr' ? `Confirmer : ${label} cette zone ?` : `Are you sure you want to ${label} this zone?`)) return;

    const data = new FormData();
    data.set('id', id);
    data.set('activate', activate ? '1' : '0');
    data.set('_csrf_token', csrfToken);

    fetch('<?= url('admin/delivery/zones/toggle') ?>', { method:'POST', body:data })
        .then(r => r.json())
        .then(res => {
            if (res.error) { showFlash(res.error, false); return; }
            showFlash(res.message, true);
            const statusEl  = document.getElementById(`zone-status-${id}`);
            const toggleBtn = document.getElementById(`toggle-btn-${id}`);
            if (activate) {
                statusEl.textContent = lang === 'fr' ? 'Actif' : 'Active';
                statusEl.className = 'badge badge-active';
                toggleBtn.textContent = lang === 'fr' ? 'Désactiver' : 'Deactivate';
                toggleBtn.className = 'btn btn-secondary btn-sm';
                toggleBtn.setAttribute('onclick', `toggleZoneStatus(${id}, false)`);
            } else {
                statusEl.textContent = lang === 'fr' ? 'Inactif' : 'Inactive';
                statusEl.className = 'badge badge-inactive';
                toggleBtn.textContent = lang === 'fr' ? 'Activer' : 'Activate';
                toggleBtn.className = 'btn btn-primary btn-sm';
                toggleBtn.setAttribute('onclick', `toggleZoneStatus(${id}, true)`);
            }
        })
        .catch(() => showFlash(lang === 'fr' ? 'Erreur réseau.' : 'Network error.', false));
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
