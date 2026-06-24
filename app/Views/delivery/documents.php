<?php
$currentPage = 'documents';
include __DIR__ . '/layout-header.php';

$activeTab = $_GET['tab'] ?? 'bgcheck';
if (!in_array($activeTab, ['bgcheck','compliance','agreement','onboarding'])) $activeTab = 'bgcheck';
$isDocTab = true;

$tabLabels = [
    'bgcheck'    => $fr ? 'Antécédents'   : 'Background Check',
    'compliance' => $fr ? 'Conformité'    : 'Compliance Docs',
    'agreement'  => $fr ? 'Contrat'       : 'Agreement',
    'onboarding' => $fr ? 'Intégration'   : 'Onboarding',
];

// Badge counts for tabs
$bgStatus    = $application['bgcheck_status'] ?? 'not_requested';
$bgBadge     = in_array($bgStatus, ['not_requested','flagged']) ? '!' : ($bgStatus === 'uploaded' ? '~' : null);
$cdFlagged   = 0; $cdDone = 0;
if ($application) {
    foreach ($docs as $d) {
        if ($d['status'] === 'flagged') $cdFlagged++;
        if (in_array($d['status'], ['verified','not_required'])) $cdDone++;
    }
}
$cdBadge = $cdFlagged > 0 ? '!' : ($cdDone < 5 ? (5 - $cdDone) : null);
?>

<style>
  .doc-tabs { display: flex; gap: 0; border-bottom: 2px solid #e5e7eb; margin-bottom: 24px; }
  .doc-tab-btn {
    padding: 12px 20px; border: none; background: none; cursor: pointer;
    font-family: inherit; font-size: 14px; font-weight: 600; color: var(--gray-500);
    border-bottom: 3px solid transparent; margin-bottom: -2px; transition: all .15s;
    display: flex; align-items: center; gap: 7px; white-space: nowrap;
  }
  .doc-tab-btn:hover { color: var(--primary); }
  .doc-tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
  .doc-tab-badge {
    background: #f59e0b; color: #fff; font-size: 10px; font-weight: 700;
    padding: 1px 6px; border-radius: 10px; min-width: 18px; text-align: center;
  }
  .doc-tab-badge.alert { background: #ef4444; }
  .doc-panel { display: none; }
  .doc-panel.active { display: block; }
  @media (max-width: 600px) {
    .doc-tabs { overflow-x: auto; }
    .doc-tab-btn { padding: 10px 14px; font-size: 13px; }
  }
</style>

<!-- Tab Bar -->
<div class="doc-tabs">
  <?php foreach ($tabLabels as $key => $label): ?>
    <button class="doc-tab-btn <?= $key === $activeTab ? 'active' : '' ?>"
            onclick="switchDocTab('<?= $key ?>')" data-tab="<?= $key ?>">
      <i class="fas fa-<?= ['bgcheck'=>'user-shield','compliance'=>'file-shield','agreement'=>'file-signature','onboarding'=>'box-open'][$key] ?>"></i>
      <?= $label ?>
      <?php
        if ($key === 'bgcheck' && $bgBadge):
          echo '<span class="doc-tab-badge' . ($bgBadge === '!' ? ' alert' : '') . '">' . $bgBadge . '</span>';
        elseif ($key === 'compliance' && $cdBadge):
          echo '<span class="doc-tab-badge' . ($cdBadge === '!' ? ' alert' : '') . '">' . $cdBadge . '</span>';
        endif;
      ?>
    </button>
  <?php endforeach; ?>
</div>

<!-- Background Check Tab -->
<div class="doc-panel <?= $activeTab === 'bgcheck' ? 'active' : '' ?>" id="doc-panel-bgcheck">
  <?php include __DIR__ . '/bgcheck.php'; ?>
</div>

<!-- Compliance Docs Tab -->
<div class="doc-panel <?= $activeTab === 'compliance' ? 'active' : '' ?>" id="doc-panel-compliance">
  <?php include __DIR__ . '/compliance.php'; ?>
</div>

<!-- Agreement Tab -->
<div class="doc-panel <?= $activeTab === 'agreement' ? 'active' : '' ?>" id="doc-panel-agreement">
  <div style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:32px;max-width:720px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
      <div style="width:44px;height:44px;border-radius:10px;background:#eff6ff;display:flex;align-items:center;justify-content:center;color:#2563eb;flex-shrink:0;">
        <i class="fas fa-file-signature" style="font-size:18px;"></i>
      </div>
      <div>
        <h2 style="margin:0;font-size:18px;font-weight:700;color:#111827;">
          <?= $fr ? 'Contrat de livreur' : 'Driver Agreement' ?>
        </h2>
        <p style="margin:4px 0 0;font-size:13px;color:#6b7280;">
          <?= $fr ? 'Contrat de sous-traitant indépendant OCSAPP' : 'OCSAPP Independent Contractor Agreement' ?>
        </p>
      </div>
    </div>

    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:24px;font-size:13px;color:#92400e;display:flex;align-items:flex-start;gap:10px;">
      <i class="fas fa-clock" style="margin-top:2px;flex-shrink:0;"></i>
      <span>
        <?= $fr
          ? "La signature électronique de votre contrat de livreur sera disponible prochainement. Si vous avez des questions sur votre contrat ou vos conditions d'engagement, contactez-nous à <strong>support@ocsapp.ca</strong>."
          : "Electronic signing of your driver agreement will be available soon. If you have questions about your contract or engagement terms, contact us at <strong>support@ocsapp.ca</strong>."
        ?>
      </span>
    </div>

    <h3 style="font-size:14px;font-weight:700;color:#374151;margin:0 0 12px;">
      <?= $fr ? 'Ce que couvre votre contrat' : 'What your agreement covers' ?>
    </h3>
    <?php
    $items = $fr ? [
        'Statut de sous-traitant indépendant et conditions d\'engagement',
        'Obligations de livraison et normes de service',
        'Structure de rémunération et conditions de paiement',
        'Conduite professionnelle et utilisation de l\'application',
        'Exigences d\'assurance et de conformité',
        'Résiliation et conditions de fin de contrat',
    ] : [
        'Independent contractor status and engagement terms',
        'Delivery obligations and service standards',
        'Compensation structure and payment terms',
        'Professional conduct and app usage',
        'Insurance and compliance requirements',
        'Termination and end-of-contract conditions',
    ];
    ?>
    <ul style="margin:0;padding-left:20px;color:#4b5563;font-size:13px;line-height:2;">
      <?php foreach ($items as $item): ?>
        <li><?= htmlspecialchars($item) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>

<!-- Onboarding Package Tab -->
<div class="doc-panel <?= $activeTab === 'onboarding' ? 'active' : '' ?>" id="doc-panel-onboarding">
  <div style="background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);padding:32px;max-width:720px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
      <div style="width:44px;height:44px;border-radius:10px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;color:#00b207;flex-shrink:0;">
        <i class="fas fa-box-open" style="font-size:18px;"></i>
      </div>
      <div>
        <h2 style="margin:0;font-size:18px;font-weight:700;color:#111827;">
          <?= $fr ? 'Trousse d\'intégration' : 'Onboarding Package' ?>
        </h2>
        <p style="margin:4px 0 0;font-size:13px;color:#6b7280;">
          <?= $fr ? 'Tout ce qu\'il vous faut pour bien démarrer' : 'Everything you need to get started' ?>
        </p>
      </div>
    </div>

    <!-- Checklist -->
    <h3 style="font-size:14px;font-weight:700;color:#374151;margin:0 0 14px;">
      <?= $fr ? 'Étapes d\'intégration' : 'Onboarding Checklist' ?>
    </h3>
    <?php
    $steps = $fr ? [
        ['icon' => 'graduation-cap', 'color' => '#6366f1', 'title' => 'Compléter la formation',      'desc' => '7 modules de formation obligatoires',              'link' => url('delivery/training'),  'done' => !empty($application)],
        ['icon' => 'user-shield',    'color' => '#2563eb', 'title' => 'Vérification des antécédents', 'desc' => 'Soumettre votre vérification des antécédents',   'link' => url('delivery/documents?tab=bgcheck'),  'done' => in_array($bgStatus, ['verified','waived'])],
        ['icon' => 'file-shield',    'color' => '#00b207', 'title' => 'Documents de conformité',      'desc' => '5 documents requis',                              'link' => url('delivery/documents?tab=compliance'), 'done' => $cdDone >= 5],
        ['icon' => 'file-signature', 'color' => '#f59e0b', 'title' => 'Signer le contrat',            'desc' => 'Bientôt disponible',                              'link' => url('delivery/documents?tab=agreement'), 'done' => false],
        ['icon' => 'cog',            'color' => '#6b7280', 'title' => 'Compléter votre profil',       'desc' => 'Photo, coordonnées bancaires, paramètres',        'link' => url('delivery/settings'),  'done' => true],
    ] : [
        ['icon' => 'graduation-cap', 'color' => '#6366f1', 'title' => 'Complete Training',            'desc' => '7 mandatory training modules',                    'link' => url('delivery/training'),  'done' => !empty($application)],
        ['icon' => 'user-shield',    'color' => '#2563eb', 'title' => 'Background Check',             'desc' => 'Submit your background check document',           'link' => url('delivery/documents?tab=bgcheck'),  'done' => in_array($bgStatus, ['verified','waived'])],
        ['icon' => 'file-shield',    'color' => '#00b207', 'title' => 'Compliance Documents',         'desc' => '5 required documents',                            'link' => url('delivery/documents?tab=compliance'), 'done' => $cdDone >= 5],
        ['icon' => 'file-signature', 'color' => '#f59e0b', 'title' => 'Sign Agreement',               'desc' => 'Coming soon',                                     'link' => url('delivery/documents?tab=agreement'), 'done' => false],
        ['icon' => 'cog',            'color' => '#6b7280', 'title' => 'Complete Your Profile',        'desc' => 'Photo, banking info, notification settings',      'link' => url('delivery/settings'),  'done' => true],
    ];
    ?>
    <div style="display:flex;flex-direction:column;gap:10px;">
      <?php foreach ($steps as $i => $step): ?>
      <a href="<?= $step['link'] ?>" style="text-decoration:none;display:flex;align-items:center;gap:14px;padding:14px 16px;background:<?= $step['done'] ? '#f0fdf4' : '#f9fafb' ?>;border:1px solid <?= $step['done'] ? '#bbf7d0' : '#e5e7eb' ?>;border-radius:10px;transition:.15s;" onmouseover="this.style.borderColor='<?= $step['color'] ?>'" onmouseout="this.style.borderColor='<?= $step['done'] ? '#bbf7d0' : '#e5e7eb' ?>'">
        <div style="width:36px;height:36px;border-radius:50%;background:<?= $step['done'] ? '#dcfce7' : $step['color'] . '15' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
          <i class="fas fa-<?= $step['done'] ? 'check' : $step['icon'] ?>" style="color:<?= $step['done'] ? '#16a34a' : $step['color'] ?>;font-size:14px;"></i>
        </div>
        <div style="flex:1;min-width:0;">
          <div style="font-size:14px;font-weight:600;color:<?= $step['done'] ? '#166534' : '#111827' ?>;"><?= htmlspecialchars($step['title']) ?></div>
          <div style="font-size:12px;color:#6b7280;"><?= htmlspecialchars($step['desc']) ?></div>
        </div>
        <i class="fas fa-chevron-right" style="color:#d1d5db;font-size:12px;"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<script>
function switchDocTab(tab) {
  document.querySelectorAll('.doc-tab-btn').forEach(b => b.classList.toggle('active', b.dataset.tab === tab));
  document.querySelectorAll('.doc-panel').forEach(p => p.classList.toggle('active', p.id === 'doc-panel-' + tab));
  const url = new URL(window.location);
  url.searchParams.set('tab', tab);
  history.replaceState(null, '', url);
}
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
