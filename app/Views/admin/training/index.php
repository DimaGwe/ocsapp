<?php
$currentPage = 'training';
$tab = $_GET['tab'] ?? 'modules';
ob_start();
?>
<style>
.training-tabs { display:flex; gap:0; border-bottom:2px solid #e5e7eb; margin-bottom:24px; }
.training-tab { padding:10px 24px; font-size:14px; font-weight:600; color:#6b7280; text-decoration:none; border-bottom:3px solid transparent; margin-bottom:-2px; transition:all .2s; }
.training-tab.active { color:#3b82f6; border-bottom-color:#3b82f6; }
.training-tab:hover { color:#3b82f6; }
.module-card { background:#fff; border:1px solid #e5e7eb; border-radius:10px; padding:16px 20px; display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; transition:box-shadow .2s; }
.module-card:hover { box-shadow:0 2px 8px rgba(0,0,0,.08); }
.module-num { width:36px; height:36px; border-radius:50%; background:#eff6ff; color:#3b82f6; font-weight:700; font-size:15px; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-right:14px; }
.module-info { flex:1; }
.module-title { font-weight:600; font-size:15px; color:#111827; margin-bottom:2px; }
.module-desc { font-size:12px; color:#6b7280; }
.module-meta { display:flex; gap:16px; align-items:center; font-size:12px; color:#6b7280; margin-right:16px; }
.meta-badge { display:inline-flex; align-items:center; gap:4px; }
.progress-table th { font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.05em; padding:10px 14px; background:#f9fafb; border-bottom:1px solid #e5e7eb; }
.progress-table td { padding:12px 14px; font-size:14px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.progress-table tr:last-child td { border-bottom:none; }
.cert-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
.cert-yes { background:#dcfce7; color:#16a34a; }
.cert-no { background:#f3f4f6; color:#6b7280; }
.mod-progress { font-weight:700; color:#1d4ed8; }
</style>

<div class="page-header" style="margin-bottom:24px;">
    <div>
        <h1 style="font-size:1.6rem; font-weight:700; color:#111827; margin:0 0 4px;">Driver Training</h1>
        <p style="color:#6b7280; font-size:14px; margin:0;">Manage training modules, questions, and track driver progress.</p>
    </div>
</div>

<!-- Tabs -->
<div class="training-tabs">
    <a href="<?= url('admin/training?tab=modules') ?>" class="training-tab <?= $tab === 'modules' ? 'active' : '' ?>">
        <i class="fas fa-book-open" style="margin-right:6px;"></i> Modules (<?= count($modules) ?>)
    </a>
    <a href="<?= url('admin/training?tab=drivers') ?>" class="training-tab <?= $tab === 'drivers' ? 'active' : '' ?>">
        <i class="fas fa-users" style="margin-right:6px;"></i> Driver Progress (<?= count($drivers) ?>)
    </a>
</div>

<?php if ($tab === 'modules'): ?>
<!-- MODULE LIST -->
<div style="max-width:820px;">
    <?php if (empty($modules)): ?>
        <p style="color:#9ca3af; text-align:center; padding:40px;">No modules found. Run the training migration to seed modules.</p>
    <?php else: ?>
        <?php foreach ($modules as $m): ?>
        <div class="module-card">
            <div class="module-num"><?= $m['order_num'] ?></div>
            <div class="module-info">
                <div class="module-title"><?= htmlspecialchars($m['title']) ?></div>
                <div class="module-desc"><?= htmlspecialchars($m['description'] ?? '') ?></div>
            </div>
            <div class="module-meta">
                <span class="meta-badge" title="Questions">
                    <i class="fas fa-question-circle" style="color:#3b82f6;"></i> <?= $m['question_count'] ?> Q
                </span>
                <span class="meta-badge" title="Drivers passed">
                    <i class="fas fa-check-circle" style="color:#16a34a;"></i> <?= $m['drivers_passed'] ?> passed
                </span>
                <span class="meta-badge" title="Pass score">
                    <i class="fas fa-bullseye" style="color:#f59e0b;"></i> <?= $m['pass_score'] ?>%
                </span>
                <?php if (!$m['is_active']): ?>
                    <span style="background:#fee2e2; color:#dc2626; padding:2px 8px; border-radius:10px; font-size:11px;">Inactive</span>
                <?php endif; ?>
            </div>
            <a href="<?= url('admin/training/module/edit?id=' . $m['id']) ?>" class="btn btn-primary btn-sm" style="flex-shrink:0;">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
    <p style="color:#9ca3af; font-size:12px; margin-top:12px;">
        <i class="fas fa-info-circle"></i> Add content and questions to each module by clicking Edit. Drivers see modules in order — they must pass one to unlock the next.
    </p>
</div>

<?php else: ?>
<!-- DRIVER PROGRESS -->
<?php $totalModules = count($modules); ?>
<?php if (empty($drivers)): ?>
    <p style="color:#9ca3af; text-align:center; padding:40px;">No delivery drivers found yet.</p>
<?php else: ?>
<div style="background:#fff; border:1px solid #e5e7eb; border-radius:10px; overflow:hidden;">
    <table class="progress-table" style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Driver</th>
                <th>Modules</th>
                <th>Certified</th>
                <th>Last Activity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($drivers as $d): ?>
            <tr>
                <td>
                    <div style="font-weight:600; color:#111827;"><?= htmlspecialchars($d['first_name'] . ' ' . $d['last_name']) ?></div>
                    <div style="font-size:12px; color:#9ca3af;"><?= htmlspecialchars($d['email']) ?></div>
                </td>
                <td>
                    <span class="mod-progress"><?= $d['modules_passed'] ?></span>
                    <span style="color:#9ca3af;">/ <?= $totalModules ?></span>
                    <?php if ($totalModules > 0): ?>
                    <div style="margin-top:4px; height:4px; background:#f3f4f6; border-radius:4px; width:80px;">
                        <div style="height:4px; background:#3b82f6; border-radius:4px; width:<?= round(($d['modules_passed']/$totalModules)*100) ?>%;"></div>
                    </div>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($d['certified_at']): ?>
                        <span class="cert-badge cert-yes"><i class="fas fa-certificate"></i> <?= $d['cert_number'] ?></span>
                        <div style="font-size:11px; color:#9ca3af; margin-top:3px;"><?= date('M j, Y', strtotime($d['certified_at'])) ?></div>
                    <?php else: ?>
                        <span class="cert-badge cert-no"><i class="fas fa-clock"></i> Not certified</span>
                    <?php endif; ?>
                </td>
                <td style="color:#6b7280; font-size:13px;">
                    <?= $d['last_activity'] ? date('M j, Y', strtotime($d['last_activity'])) : '—' ?>
                </td>
                <td>
                    <div style="display:flex; gap:6px; flex-wrap:wrap;">
                        <?php if (!$d['certified_at']): ?>
                        <form method="POST" action="<?= url('admin/training/driver/certify') ?>" onsubmit="return confirm('Manually certify this driver and bypass training?')">
                            <?= csrfField() ?>
                            <input type="hidden" name="driver_id" value="<?= $d['id'] ?>">
                            <button type="submit" class="btn btn-sm" style="background:#dcfce7; color:#16a34a; border:1px solid #bbf7d0; font-size:12px; padding:4px 10px; border-radius:6px; cursor:pointer;">
                                <i class="fas fa-certificate"></i> Certify
                            </button>
                        </form>
                        <?php endif; ?>
                        <button type="button" onclick="openResetModal(<?= $d['id'] ?>, '<?= htmlspecialchars(addslashes($d['first_name'] . ' ' . $d['last_name'])) ?>')"
                            class="btn btn-sm" style="background:#fff; border:1px solid #e5e7eb; color:#6b7280; font-size:12px; padding:4px 10px; border-radius:6px; cursor:pointer;">
                            <i class="fas fa-redo"></i> Reset Module
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php endif; ?>

<!-- Reset Module Modal -->
<div id="resetModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:14px; padding:28px; width:400px; max-width:95vw;">
        <h3 style="margin:0 0 8px; font-size:1.05rem; font-weight:700;"><i class="fas fa-redo" style="color:#3b82f6;"></i> Reset Module</h3>
        <p style="font-size:13px; color:#6b7280; margin:0 0 16px;" id="resetDriverName"></p>
        <form method="POST" action="<?= url('admin/training/driver/reset-module') ?>">
            <?= csrfField() ?>
            <input type="hidden" name="driver_id" id="resetDriverId">
            <div style="margin-bottom:14px;">
                <label style="font-size:13px; font-weight:600; display:block; margin-bottom:6px;">Select Module to Reset</label>
                <select name="module_id" style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit;">
                    <?php foreach ($modules as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= $m['order_num'] ?>. <?= htmlspecialchars($m['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('resetModal').style.display='none'" class="btn btn-secondary btn-sm">Cancel</button>
                <button type="submit" class="btn btn-primary btn-sm">Reset Module</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal(driverId, name) {
    document.getElementById('resetDriverId').value = driverId;
    document.getElementById('resetDriverName').textContent = 'Resetting a module for: ' + name + '. The driver will be able to retake the quiz.';
    document.getElementById('resetModal').style.display = 'flex';
}
</script>
<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
