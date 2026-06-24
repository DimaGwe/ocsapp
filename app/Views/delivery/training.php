<?php
$currentPage = 'training';
include __DIR__ . '/layout-header.php';
?>
<style>
.training-hero { background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); border-radius: 14px; padding: 28px 32px; color: #fff; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; gap: 20px; }
.training-hero h1 { font-size: 1.5rem; font-weight: 800; margin: 0 0 6px; }
.training-hero p { font-size: 14px; opacity: .85; margin: 0; }
.prog-bar-wrap { margin-top: 16px; }
.prog-bar-track { height: 8px; background: rgba(255,255,255,.25); border-radius: 8px; overflow: hidden; }
.prog-bar-fill { height: 8px; background: #fff; border-radius: 8px; transition: width .5s; }
.prog-label { font-size: 13px; margin-top: 6px; opacity: .9; }
.cert-ready { background: #dcfce7; color: #15803d; border: 2px solid #86efac; border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
.cert-ready i { font-size: 2rem; }
.modules-list { display: flex; flex-direction: column; gap: 12px; }
.module-card { background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 18px 20px; display: flex; align-items: center; gap: 16px; transition: all .2s; }
.module-card.available { border-color: #3b82f6; cursor: pointer; }
.module-card.available:hover { box-shadow: 0 4px 14px rgba(59,130,246,.2); transform: translateY(-1px); }
.module-card.passed { border-color: #86efac; background: #f0fdf4; }
.module-card.failed-retry { border-color: #fcd34d; background: #fffbeb; }
.module-card.failed-max { border-color: #fca5a5; background: #fff1f2; }
.module-card.locked { opacity: .55; }
.mod-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; flex-shrink: 0; }
.mod-icon.locked { background: #f3f4f6; color: #9ca3af; }
.mod-icon.available { background: #eff6ff; color: #3b82f6; }
.mod-icon.passed { background: #dcfce7; color: #16a34a; }
.mod-icon.failed-retry { background: #fef3c7; color: #d97706; }
.mod-icon.failed-max { background: #fee2e2; color: #dc2626; }
.mod-body { flex: 1; }
.mod-num { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; margin-bottom: 3px; }
.mod-title { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 3px; }
.mod-desc { font-size: 13px; color: #6b7280; }
.mod-status { text-align: right; flex-shrink: 0; }
.status-badge { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-locked { background: #f3f4f6; color: #6b7280; }
.badge-available { background: #eff6ff; color: #1d4ed8; }
.badge-passed { background: #dcfce7; color: #15803d; }
.badge-failed { background: #fef3c7; color: #92400e; }
.badge-failed-max { background: #fee2e2; color: #991b1b; }
.mod-score { font-size: 22px; font-weight: 800; color: #16a34a; margin-bottom: 2px; }
.mod-btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none; margin-top: 8px; }
.mod-btn-primary { background: #3b82f6; color: #fff; }
.mod-btn-retry { background: #f59e0b; color: #fff; }
.mod-btn-review { background: #f3f4f6; color: #374151; }
</style>

<div class="training-hero">
    <div style="flex:1;">
        <h1><i class="fas fa-graduation-cap"></i> <?php echo $fr ? 'Programme de formation des livreurs' : 'Driver Training Program'; ?></h1>
        <p><?php echo $fr ? 'Complétez tous les modules pour débloquer les livraisons et obtenir votre certificat.' : 'Complete all modules to unlock deliveries and earn your certificate.'; ?></p>
        <div class="prog-bar-wrap">
            <div class="prog-bar-track">
                <div class="prog-bar-fill" style="width:<?= $totalModules > 0 ? round(($passedCount/$totalModules)*100) : 0 ?>%;"></div>
            </div>
            <div class="prog-label"><?php echo $fr ? $passedCount . ' de ' . $totalModules . ' modules complétés' : $passedCount . ' of ' . $totalModules . ' modules completed'; ?></div>
        </div>
    </div>
    <div style="font-size: 4rem; opacity:.7;">🎓</div>
</div>

<?php if ($certificate): ?>
<div class="cert-ready">
    <i class="fas fa-certificate" style="color:#16a34a; font-size:2rem;"></i>
    <div style="flex:1;">
        <div style="font-weight:700; font-size:16px;"><?php echo $fr ? 'Formation terminée - Vous êtes certifié !' : 'Training Complete - You\'re Certified!'; ?></div>
        <div style="font-size:13px; margin-top:2px;">
            <?php
            $certTs = strtotime($certificate['issued_at']);
            if ($fr) {
                $frMonthsCert = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                $certDate = (int)date('j', $certTs) . ' ' . $frMonthsCert[(int)date('n', $certTs)] . ' ' . date('Y', $certTs);
                echo 'Certificat n°' . htmlspecialchars($certificate['cert_number']) . ' - Émis le ' . $certDate;
            } else {
                echo 'Certificate #' . htmlspecialchars($certificate['cert_number']) . ' · Issued ' . date('F j, Y', $certTs);
            }
            ?>
        </div>
    </div>
    <a href="<?= url('delivery/training/certificate') ?>" class="mod-btn mod-btn-primary" style="text-decoration:none;">
        <i class="fas fa-download"></i> <?php echo $fr ? 'Voir le certificat' : 'View Certificate'; ?>
    </a>
</div>
<?php endif; ?>

<!-- Flash Messages -->
<?php if ($flash = getFlash('error')): ?>
<div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>
<?php if ($flash = getFlash('info')): ?>
<div style="background:#eff6ff; border-left:4px solid #3b82f6; color:#1e40af; padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:14px;">
    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<div class="modules-list">
<?php foreach ($modules as $m):
    $p = $progress[$m['id']] ?? null;
    $status = $p['status'] ?? 'locked';
    $cardClass = match($status) {
        'available' => 'available',
        'passed'    => 'passed',
        'failed'    => ($p['attempts'] >= $m['max_attempts']) ? 'failed-max' : 'failed-retry',
        default     => 'locked',
    };
    $iconClass = $cardClass;
?>
<div class="module-card <?= $cardClass ?>">
    <div class="mod-icon <?= $iconClass ?>">
        <?php if ($status === 'locked'): ?>
            <i class="fas fa-lock"></i>
        <?php elseif ($status === 'passed'): ?>
            <i class="fas fa-check-circle"></i>
        <?php elseif ($status === 'available'): ?>
            <i class="fas fa-play-circle"></i>
        <?php else: ?>
            <i class="fas fa-exclamation-circle"></i>
        <?php endif; ?>
    </div>
    <div class="mod-body">
        <div class="mod-num"><?php echo $fr ? 'Module' : 'Module'; ?> <?= $m['order_num'] ?></div>
        <div class="mod-title"><?= htmlspecialchars($m['title']) ?></div>
        <div class="mod-desc"><?= htmlspecialchars($m['description'] ?? '') ?></div>
        <?php if ($status === 'available' || $status === 'passed'): ?>
            <a href="<?= url('delivery/training/module?id=' . $m['id']) ?>" class="mod-btn <?= $status === 'passed' ? 'mod-btn-review' : 'mod-btn-primary' ?>">
                <?php if ($status === 'passed'): ?>
                    <i class="fas fa-eye"></i> <?php echo $fr ? 'Revoir' : 'Review'; ?>
                <?php else: ?>
                    <i class="fas fa-play"></i> <?php echo $fr ? 'Commencer le module' : 'Start Module'; ?>
                <?php endif; ?>
            </a>
        <?php elseif ($cardClass === 'failed-retry'): ?>
            <a href="<?= url('delivery/training/module?id=' . $m['id']) ?>" class="mod-btn mod-btn-retry">
                <i class="fas fa-redo"></i> <?php
                $attemptsLeft = $m['max_attempts'] - ($p['attempts'] ?? 0);
                echo $fr ? 'Réessayer (' . $attemptsLeft . ' restant)' : 'Retry (' . $attemptsLeft . ' left)';
                ?>
            </a>
        <?php elseif ($cardClass === 'failed-max'): ?>
            <span style="font-size:12px; color:#dc2626; margin-top:6px; display:block;">
                <i class="fas fa-ban"></i> <?php echo $fr ? 'Nombre maximal de tentatives atteint - contacter l\'admin' : 'Max attempts reached - contact admin'; ?>
            </span>
        <?php else: ?>
            <span style="font-size:12px; color:#9ca3af; margin-top:6px; display:block;">
                <i class="fas fa-lock"></i> <?php echo $fr ? 'Terminez le module précédent pour déverrouiller' : 'Complete the previous module to unlock'; ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="mod-status">
        <?php if ($status === 'passed'): ?>
            <div class="mod-score"><?= $p['best_score'] ?>%</div>
            <div class="status-badge badge-passed"><i class="fas fa-check"></i> <?php echo $fr ? 'Réussi' : 'Passed'; ?></div>
        <?php elseif ($status === 'available'): ?>
            <div class="status-badge badge-available"><i class="fas fa-unlock"></i> <?php echo $fr ? 'Déverrouillé' : 'Unlocked'; ?></div>
        <?php elseif ($cardClass === 'failed-retry'): ?>
            <div class="mod-score" style="color:#d97706;"><?= $p['best_score'] ?>%</div>
            <div class="status-badge badge-failed"><i class="fas fa-times"></i> <?php echo $fr ? 'Échoué' : 'Failed'; ?></div>
        <?php elseif ($cardClass === 'failed-max'): ?>
            <div class="mod-score" style="color:#dc2626;"><?= $p['best_score'] ?>%</div>
            <div class="status-badge badge-failed-max"><i class="fas fa-ban"></i> <?php echo $fr ? 'Bloqué' : 'Locked out'; ?></div>
        <?php else: ?>
            <div class="status-badge badge-locked"><i class="fas fa-lock"></i> <?php echo $fr ? 'Verrouillé' : 'Locked'; ?></div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php include __DIR__ . '/layout-footer.php'; ?>
