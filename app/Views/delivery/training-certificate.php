<?php
$currentPage = 'training';
include __DIR__ . '/layout-header.php';
?>
<style>
.cert-actions { display:flex; justify-content:flex-end; gap:10px; margin-bottom:20px; }
@media print {
    .delivery-nav, .cert-actions, .layout-header, .layout-footer { display:none !important; }
    body { background:#fff !important; }
    .cert-wrap { box-shadow:none !important; }
}
.cert-wrap {
    max-width:780px; margin:0 auto;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 4px 24px rgba(0,0,0,.08);
}
.cert-top {
    background:linear-gradient(135deg,#1e3a5f,#1d4ed8);
    padding:36px 48px 28px;
    text-align:center;
    color:#fff;
}
.cert-top .logo-line {
    font-size:1rem; font-weight:800; letter-spacing:.15em; text-transform:uppercase; opacity:.75; margin-bottom:16px;
}
.cert-top h1 {
    font-size:2.2rem; font-weight:900; letter-spacing:.02em; margin:0 0 6px; line-height:1.15;
}
.cert-top .cert-sub {
    font-size:1rem; opacity:.75; font-weight:500;
}
.cert-divider {
    display:flex; align-items:center; gap:0;
}
.cert-divider-line { flex:1; height:3px; background:linear-gradient(90deg,#f59e0b,#fbbf24); }
.cert-divider-star { color:#f59e0b; font-size:1.3rem; padding:0 12px; }
.cert-body { padding:36px 48px; text-align:center; }
.cert-presented { font-size:14px; color:#9ca3af; text-transform:uppercase; letter-spacing:.08em; margin-bottom:8px; }
.cert-name { font-size:2.2rem; font-weight:900; color:#111827; margin-bottom:20px; font-family:Georgia,serif; }
.cert-desc { font-size:15px; color:#374151; max-width:520px; margin:0 auto 28px; line-height:1.7; }
.cert-modules { display:grid; grid-template-columns:1fr 1fr; gap:8px; max-width:540px; margin:0 auto 28px; text-align:left; }
.cert-module-item { display:flex; align-items:center; gap:8px; font-size:13px; color:#374151; background:#f9fafb; border-radius:7px; padding:8px 12px; }
.cert-module-item i { color:#22c55e; }
.cert-footer { display:flex; justify-content:space-between; align-items:flex-end; padding-top:24px; border-top:1px solid #f3f4f6; margin-top:4px; }
.cert-sig { text-align:center; }
.cert-sig-line { width:140px; border-top:2px solid #374151; margin:0 auto 6px; }
.cert-sig-name { font-size:12px; font-weight:700; color:#374151; }
.cert-sig-role { font-size:11px; color:#9ca3af; }
.cert-seal { text-align:center; }
.cert-seal-circle {
    width:84px; height:84px; border-radius:50%;
    border:3px solid #f59e0b;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    color:#f59e0b; margin:0 auto;
}
.cert-seal-circle i { font-size:1.8rem; }
.cert-seal-circle span { font-size:9px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; }
.cert-id { text-align:center; font-size:11px; color:#d1d5db; margin-top:20px; }
</style>

<div class="cert-actions">
    <a href="<?= url('delivery/training') ?>" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> <?php echo $fr ? 'Retour à la formation' : 'Back to Training'; ?>
    </a>
    <button class="btn btn-primary btn-sm" onclick="window.print()">
        <i class="fas fa-print"></i> <?php echo $fr ? 'Imprimer le certificat' : 'Print Certificate'; ?>
    </button>
</div>

<div class="cert-wrap">
    <!-- Header -->
    <div class="cert-top">
        <div class="logo-line">OCS Marketplace</div>
        <h1><?php echo $fr ? 'Certificat de réussite' : 'Certificate of Completion'; ?></h1>
        <div class="cert-sub"><?php echo $fr ? 'Programme de certification des livreurs' : 'Driver Certification Program'; ?></div>
    </div>
    <div class="cert-divider">
        <div class="cert-divider-line"></div>
        <div class="cert-divider-star"><i class="fas fa-star"></i></div>
        <div class="cert-divider-line"></div>
    </div>

    <!-- Body -->
    <div class="cert-body">
        <div class="cert-presented"><?php echo $fr ? 'Ce certificat est fièrement remis à' : 'This certificate is proudly presented to'; ?></div>
        <div class="cert-name"><?= htmlspecialchars($driverName) ?></div>

        <div class="cert-desc">
            <?php if ($fr): ?>
                Pour avoir complété avec succès tous les modules de formation requis du
                <strong>Programme de certification des livreurs OCS Marketplace</strong>
                et avoir démontré les connaissances et compétences nécessaires pour offrir
                des services de livraison sécuritaires et professionnels.
            <?php else: ?>
                For successfully completing all required training modules of the
                <strong>OCS Marketplace Driver Certification Program</strong>
                and demonstrating the knowledge and skills required to provide
                safe, professional delivery services.
            <?php endif; ?>
        </div>

        <!-- Modules completed -->
        <div class="cert-modules">
            <?php foreach ($modules as $m): ?>
            <div class="cert-module-item">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($m['title']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Footer with signature + seal -->
        <div class="cert-footer">
            <div class="cert-sig">
                <div class="cert-sig-line"></div>
                <div class="cert-sig-name"><?php echo $fr ? 'Équipe des opérations OCS' : 'OCS Operations Team'; ?></div>
                <div class="cert-sig-role"><?php echo $fr ? 'Directeur des livraisons' : 'Delivery Director'; ?></div>
            </div>

            <div style="text-align:center;">
                <div style="font-size:12px; color:#9ca3af;"><?php echo $fr ? 'Date de certification' : 'Date Certified'; ?></div>
                <div style="font-size:15px; font-weight:800; color:#111827; margin-top:2px;">
                    <?php
                    $tsCert = strtotime($certDate);
                    if ($fr) {
                        $frMonthsCertPage = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                        echo (int)date('j', $tsCert) . ' ' . $frMonthsCertPage[(int)date('n', $tsCert)] . ' ' . date('Y', $tsCert);
                    } else {
                        echo date('F j, Y', $tsCert);
                    }
                    ?>
                </div>
            </div>

            <div class="cert-seal">
                <div class="cert-seal-circle">
                    <i class="fas fa-certificate"></i>
                    <span><?php echo $fr ? 'Certifié' : 'Certified'; ?></span>
                </div>
            </div>
        </div>

        <div class="cert-id"><?php echo $fr ? 'ID du certificat :' : 'Certificate ID:'; ?> <?= strtoupper(substr(md5($certId . $driverName), 0, 16)) ?></div>
    </div>
</div>

<?php include __DIR__ . '/layout-footer.php'; ?>
