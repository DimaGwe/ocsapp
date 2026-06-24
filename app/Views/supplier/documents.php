<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = $currentLang === 'fr';

$hasAgreed     = !empty($supplierRow['agreement_agreed_at']);
$agreedAt      = $hasAgreed ? date('F j, Y', strtotime($supplierRow['agreement_agreed_at'])) : null;
$agreedVersion = $supplierRow['agreement_version'] ?? null;
?>
<?php require __DIR__ . '/layout-header.php'; ?>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash['success']) ?></div>
<?php elseif (!empty($flash['error'])): ?>
  <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash['error']) ?></div>
<?php endif; ?>

<style>
  .docs-header { margin-bottom: 28px; }
  .docs-header h2 { font-size: 16px; color: var(--gray-400); font-weight: 400; margin-top: 4px; }

  .docs-grid { display: flex; flex-direction: column; gap: 16px; }

  .doc-card {
    background: white; border-radius: 12px; padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 3px solid var(--gray-100);
    transition: border-color 0.2s;
  }
  .doc-card.uploaded { border-color: #bbf7d0; }
  .doc-card.missing { border-color: #fecaca; }

  .doc-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .doc-card-title { display: flex; align-items: center; gap: 12px; }
  .doc-card-title i { font-size: 28px; }
  .doc-card-title .doc-name { font-size: 15px; font-weight: 700; color: var(--gray-700); }
  .doc-card-title .doc-status { font-size: 12px; margin-top: 2px; }

  .badge-uploaded  { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d1fae5; color: #065f46; }
  .badge-missing   { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fee2e2; color: #991b1b; }
  .badge-approved  { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #d1fae5; color: #065f46; }
  .badge-rejected  { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fee2e2; color: #991b1b; }
  .badge-review    { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #fef3c7; color: #92400e; }
  .badge-na        { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #f3f4f6; color: #6b7280; }

  .doc-card.approved { border-color: #bbf7d0; }
  .doc-card.rejected { border-color: #fecaca; }
  .doc-card.na       { border-color: #e5e7eb; }
  .doc-card.review   { border-color: #fde68a; }

  .rejection-note {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 10px 14px; background: #fff1f2; border-radius: 8px;
    margin-bottom: 14px; font-size: 13px; color: #991b1b;
    border-left: 3px solid #f87171;
  }
  .rejection-note i { margin-top: 2px; flex-shrink: 0; }

  .doc-file-info {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; background: #f9fafb; border-radius: 8px;
    margin-bottom: 14px; font-size: 13px; color: var(--gray-600);
  }
  .doc-file-info i { color: var(--gray-400); }

  .doc-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

  .btn-view {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; background: var(--primary); color: white;
    border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600;
    transition: opacity 0.2s;
  }
  .btn-view:hover { opacity: 0.9; }

  .upload-form { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
  .upload-form input[type="file"] { display: none; }
  .btn-file-pick {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; background: #f9fafb; color: var(--gray-600);
    border: 2px dashed var(--gray-300); border-radius: 8px;
    font-size: 13px; font-weight: 500; cursor: pointer;
    transition: border-color 0.2s, color 0.2s;
  }
  .btn-file-pick:hover { border-color: var(--primary); color: var(--primary); }
  .btn-file-pick.has-file { border-color: #10b981; color: #065f46; background: #f0fdf4; }
  .file-name-display { font-size: 12px; color: var(--gray-400); }

  .btn-upload {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 18px; background: #2563eb; color: white;
    border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }
  .btn-upload:hover { background: #1d4ed8; }

  .btn-replace {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 8px 14px; background: white; color: var(--gray-600);
    border: 2px solid var(--gray-200); border-radius: 8px;
    font-size: 13px; font-weight: 500; cursor: pointer;
    transition: border-color 0.2s;
  }
  .btn-replace:hover { border-color: var(--primary); color: var(--primary); }

  .replace-form { display: none; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--gray-100); }
  .replace-form.active { display: block; }

  .upload-hint { font-size: 12px; color: var(--gray-400); margin-top: 8px; }

  .no-app-notice {
    background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px;
    padding: 24px; text-align: center; color: #92400e;
  }
  .no-app-notice i { font-size: 36px; margin-bottom: 12px; display: block; }

  .progress-bar-wrap { margin-bottom: 24px; }
  .progress-label { display: flex; justify-content: space-between; font-size: 13px; color: var(--gray-600); margin-bottom: 6px; font-weight: 500; }
  .progress-bar { height: 8px; background: var(--gray-100); border-radius: 10px; overflow: hidden; }
  .progress-fill { height: 100%; border-radius: 10px; transition: width 0.4s ease; }
  .progress-fill.complete { background: #10b981; }
  .progress-fill.partial { background: #f59e0b; }
  .progress-fill.none { background: #ef4444; }

  .doc-card.provided { border-color: #bbf7d0; }
  .doc-card.agreed   { border-color: #86efac; }

  .doc-provided-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600;
    background: #f0fdf4; color: #166534;
  }

  .doc-actions-row {
    display: flex; align-items: center; gap: 10px; flex-wrap: wrap;
    margin-bottom: 14px;
  }

  .agreement-confirm {
    border-top: 1px solid var(--gray-100); padding-top: 16px; margin-top: 4px;
  }
  .agreement-confirm-signed {
    border-top: 1px solid #d1fae5; padding-top: 16px; margin-top: 4px;
    display: flex; align-items: center; gap: 10px; font-size: 13px; color: #065f46;
  }
  .agreement-confirm-signed i { color: #16a34a; font-size: 18px; }

  .checkbox-row {
    display: flex; align-items: flex-start; gap: 10px; margin-bottom: 14px;
    font-size: 13px; color: var(--gray-700); line-height: 1.5;
  }
  .checkbox-row input[type="checkbox"] {
    margin-top: 2px; width: 16px; height: 16px; flex-shrink: 0; cursor: pointer;
  }

  .btn-confirm {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 9px 22px; background: #16a34a; color: white;
    border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
    cursor: pointer; transition: background 0.2s;
  }
  .btn-confirm:hover:not(:disabled) { background: #15803d; }
  .btn-confirm:disabled { opacity: 0.45; cursor: not-allowed; }

  @media (max-width: 600px) {
    .doc-card { padding: 16px; }
    .doc-card-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .upload-form { flex-direction: column; align-items: stretch; }
    .upload-form input[type="file"] { width: 100%; }
    .btn-upload, .btn-view, .btn-replace, .btn-confirm { width: 100%; justify-content: center; }
    .doc-actions, .doc-actions-row { flex-direction: column; }
  }
</style>

<?php if (!$application): ?>
  <div class="no-app-notice">
    <i class="fas fa-exclamation-triangle"></i>
    <strong><?= $fr ? 'Aucune demande trouvée' : 'No application found' ?></strong>
    <p style="margin-top:8px;font-size:14px;"><?= $fr ? "Votre compte n'est pas associé à une demande fournisseur. Veuillez contacter le soutien." : 'Your account is not linked to a supplier application. Please contact support.' ?></p>
  </div>
<?php else: ?>

<?php
  $uploadedCount = 0;
  $totalDocs = count($docFields);
  foreach ($docFields as $field => $label) {
      if (!empty($application[$field])) $uploadedCount++;
  }
  $progressPct = ($totalDocs > 0) ? round(($uploadedCount / $totalDocs) * 100) : 0;
  $progressClass = $uploadedCount === $totalDocs ? 'complete' : ($uploadedCount > 0 ? 'partial' : 'none');
?>

<!-- Progress -->
<div class="progress-bar-wrap">
  <div class="progress-label">
    <span><?= $fr ? 'Documents téléversés' : 'Documents Uploaded' ?></span>
    <span><?= $uploadedCount ?> / <?= $totalDocs ?></span>
  </div>
  <div class="progress-bar">
    <div class="progress-fill <?= $progressClass ?>" style="width: <?= $progressPct ?>%;"></div>
  </div>
</div>

<!-- Document Cards -->
<?php
$_docLabelsFr = [
    'Certificate of Incorporation'    => "Certificat d'incorporation",
    'Declaration of Registration'     => "Déclaration d'immatriculation",
    'Enterprise Register File Search' => 'Recherche au registre des entreprises',
];
?>
<div class="docs-grid">
  <?php foreach ($docFields as $field => $label):
    $label = $fr ? ($_docLabelsFr[$label] ?? $label) : $label;
  ?>
    <?php
      $hasFile    = !empty($application[$field]);
      $reviewSt   = $application[$field . '_status'] ?? 'pending';
      $ext        = $hasFile ? strtolower(pathinfo($application[$field], PATHINFO_EXTENSION)) : '';
      $icon       = $ext === 'pdf' ? 'fa-file-pdf' : ($hasFile ? 'fa-file-image' : 'fa-file-circle-question');
      $iconColor  = $ext === 'pdf' ? '#dc2626' : ($hasFile ? '#3b82f6' : '#d1d5db');

      // Determine card class based on review status
      if ($reviewSt === 'approved') {
          $cardClass = 'approved';
      } elseif ($reviewSt === 'rejected') {
          $cardClass = 'rejected';
      } elseif ($reviewSt === 'na') {
          $cardClass = 'na';
      } elseif ($hasFile) {
          $cardClass = 'review'; // uploaded, awaiting review
      } else {
          $cardClass = 'missing';
      }

      // Badge config
      $badges = [
          'approved' => ['class' => 'badge-approved', 'icon' => 'fa-check-circle',  'label' => $fr ? 'Approuvé'                      : 'Approved'],
          'rejected' => ['class' => 'badge-rejected', 'icon' => 'fa-times-circle',  'label' => $fr ? 'Rejeté - Veuillez retélécharger' : 'Rejected — Please re-upload'],
          'na'       => ['class' => 'badge-na',       'icon' => 'fa-minus-circle',  'label' => $fr ? 'Non requis'                    : 'Not Required'],
          'pending'  => $hasFile
              ? ['class' => 'badge-review',  'icon' => 'fa-clock',              'label' => $fr ? "En cours d'examen" : 'Under Review']
              : ['class' => 'badge-missing', 'icon' => 'fa-exclamation-circle', 'label' => $fr ? 'Manquant'          : 'Missing'],
      ];
      $badge = $badges[$reviewSt] ?? $badges['pending'];
    ?>
    <div class="doc-card <?= $cardClass ?>">
      <div class="doc-card-header">
        <div class="doc-card-title">
          <i class="fas <?= $icon ?>" style="color: <?= $iconColor ?>;"></i>
          <div>
            <div class="doc-name"><?= htmlspecialchars($label) ?></div>
            <div class="doc-status">
              <span class="<?= $badge['class'] ?>">
                <i class="fas <?= $badge['icon'] ?>"></i> <?= $badge['label'] ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <?php if ($reviewSt === 'na'): ?>
        <!-- Not required — no upload needed -->
        <p style="font-size:13px;color:var(--gray-500);margin:0;">
          <?= $fr ? "L'administrateur a indiqué que ce document n'est pas requis pour votre compte." : 'Admin has indicated this document is not required for your account.' ?>
        </p>

      <?php elseif ($hasFile): ?>
        <?php if ($reviewSt === 'rejected'): ?>
          <div class="rejection-note">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?= $fr ? 'Ce document a été rejeté. Veuillez télécharger une version corrigée ci-dessous.' : 'This document was rejected. Please upload a corrected version below.' ?></span>
          </div>
        <?php endif; ?>

        <!-- Uploaded file info -->
        <div class="doc-file-info">
          <i class="fas fa-paperclip"></i>
          <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(basename($application[$field])) ?></span>
          <span style="color:var(--gray-400);text-transform:uppercase;font-weight:600;font-size:11px;"><?= strtoupper($ext) ?></span>
        </div>

        <div class="doc-actions">
          <a href="<?= url($application[$field]) ?>" target="_blank" class="btn-view">
            <i class="fas fa-external-link-alt"></i> <?= $fr ? 'Voir le document' : 'View Document' ?>
          </a>
          <?php if ($reviewSt !== 'approved'): ?>
          <button type="button" class="btn-replace" onclick="toggleReplace('<?= $field ?>')">
            <i class="fas fa-sync-alt"></i> <?= $fr ? 'Remplacer' : 'Replace' ?>
          </button>
          <?php endif; ?>
        </div>

        <!-- Hidden replace form (not shown for approved docs) -->
        <?php if ($reviewSt !== 'approved'): ?>
        <div class="replace-form <?= $reviewSt === 'rejected' ? 'active' : '' ?>" id="replace-<?= $field ?>">
          <form method="POST" action="<?= url('supplier/documents/upload') ?>" enctype="multipart/form-data" class="upload-form">
            <?= csrfField() ?>
            <input type="hidden" name="doc_type" value="<?= $field ?>">
            <label for="file-replace-<?= $field ?>" class="btn-file-pick" id="label-replace-<?= $field ?>">
              <i class="fas fa-folder-open"></i> <?= $fr ? 'Choisir un fichier' : 'Choose file' ?>
            </label>
            <input type="file" id="file-replace-<?= $field ?>" name="document" accept=".pdf,.jpg,.jpeg,.png" required
              onchange="handleFileChange(this,'label-replace-<?= $field ?>','name-replace-<?= $field ?>')">
            <span class="file-name-display" id="name-replace-<?= $field ?>"><?= $fr ? 'Aucun fichier sélectionné' : 'No file chosen' ?></span>
            <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> <?= $fr ? 'Télécharger (nouveau)' : 'Upload New' ?></button>
          </form>
          <div class="upload-hint"><?= $fr ? 'Acceptés : PDF, JPG, PNG — Max 5 Mo. Ce fichier remplacera le fichier actuel.' : 'Accepted: PDF, JPG, PNG — Max 5MB. This will replace the current file.' ?></div>
        </div>
        <?php endif; ?>

      <?php else: ?>
        <!-- Upload form for missing document -->
        <form method="POST" action="<?= url('supplier/documents/upload') ?>" enctype="multipart/form-data" class="upload-form">
          <?= csrfField() ?>
          <input type="hidden" name="doc_type" value="<?= $field ?>">
          <label for="file-upload-<?= $field ?>" class="btn-file-pick" id="label-upload-<?= $field ?>">
            <i class="fas fa-folder-open"></i> <?= $fr ? 'Choisir un fichier' : 'Choose file' ?>
          </label>
          <input type="file" id="file-upload-<?= $field ?>" name="document" accept=".pdf,.jpg,.jpeg,.png" required
            onchange="handleFileChange(this,'label-upload-<?= $field ?>','name-upload-<?= $field ?>')">
          <span class="file-name-display" id="name-upload-<?= $field ?>"><?= $fr ? 'Aucun fichier sélectionné' : 'No file chosen' ?></span>
          <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> <?= $fr ? 'Télécharger' : 'Upload' ?></button>
        </form>
        <div class="upload-hint"><?= $fr ? 'Acceptés : PDF, JPG, PNG — Max 5 Mo' : 'Accepted: PDF, JPG, PNG — Max 5MB' ?></div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>

<!-- ── Supplier Service Agreement ── -->
<div class="doc-card <?= $hasAgreed ? 'agreed' : 'provided' ?>" style="margin-top:16px;">
  <div class="doc-card-header">
    <div class="doc-card-title">
      <i class="fas fa-file-contract" style="color:#16a34a;font-size:28px;"></i>
      <div>
        <div class="doc-name"><?= $fr ? 'Accord de services fournisseur' : 'Supplier Service Agreement' ?></div>
        <div class="doc-status">
          <?php if ($hasAgreed): ?>
            <span class="badge-approved"><i class="fas fa-check-circle"></i> <?= $fr ? 'Signé le' : 'Signed on' ?> <?= $agreedAt ?></span>
          <?php else: ?>
            <span class="badge-missing"><i class="fas fa-exclamation-circle"></i> <?= $fr ? 'Signature requise' : 'Signature required' ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <span class="doc-provided-badge"><i class="fas fa-building"></i> <?= $fr ? 'Fourni par OCSAPP' : 'Provided by OCSAPP' ?></span>
  </div>

  <div class="doc-actions-row">
    <a href="<?= url('supplier/documents/agreement.pdf') ?>" target="_blank" class="btn-view">
      <i class="fas fa-eye"></i> <?= $fr ? "Voir l'accord (FR/EN)" : 'View Agreement (FR/EN)' ?>
    </a>
    <a href="<?= url('supplier/documents/agreement.pdf') ?>" download class="btn-replace">
      <i class="fas fa-download"></i> <?= $fr ? 'Télécharger le PDF' : 'Download PDF' ?>
    </a>
  </div>

  <?php if ($hasAgreed): ?>
    <div class="agreement-confirm-signed">
      <i class="fas fa-check-circle"></i>
      <span>
        <?= $fr ? 'Vous avez signé cet accord le' : 'You signed this agreement on' ?>
        <strong><?= $agreedAt ?></strong><?= $agreedVersion ? ' (v' . $agreedVersion . ')' : '' ?>.
      </span>
    </div>
  <?php else: ?>
    <div class="agreement-confirm">
      <form method="POST" action="<?= url('supplier/documents/confirm-agreement') ?>">
        <?= csrfField() ?>
        <div class="checkbox-row">
          <input type="checkbox" id="sup_agree_check" name="agreed" value="1" required
                 onchange="document.getElementById('sup_btn_confirm').disabled = !this.checked;">
          <label for="sup_agree_check">
            J'ai lu et j'accepte l'Accord de services fournisseur d'OCSAPP / I have read and agree to the OCSAPP Supplier Service Agreement (v<?= $currentAgreementVersion ?>).
          </label>
        </div>
        <button type="submit" id="sup_btn_confirm" class="btn-confirm" disabled>
          <i class="fas fa-signature"></i> <?= $fr ? 'Confirmer et signer' : 'Confirm & Sign' ?>
        </button>
      </form>
    </div>
  <?php endif; ?>
</div>

<!-- ── Supplier Onboarding Package ── -->
<div class="doc-card provided" style="margin-top:16px;">
  <div class="doc-card-header">
    <div class="doc-card-title">
      <i class="fas fa-box-open" style="color:#2563eb;font-size:28px;"></i>
      <div>
        <div class="doc-name"><?= $fr ? "Trousse d'intégration fournisseur" : 'Supplier Onboarding Package' ?></div>
        <div class="doc-status">
          <span class="badge-approved"><i class="fas fa-check-circle"></i> <?= $fr ? 'Disponible' : 'Available' ?></span>
        </div>
      </div>
    </div>
    <span class="doc-provided-badge"><i class="fas fa-building"></i> <?= $fr ? 'Fourni par OCSAPP' : 'Provided by OCSAPP' ?></span>
  </div>
  <div class="doc-actions-row">
    <a href="<?= url('supplier/documents/onboarding.pdf') ?>" target="_blank" class="btn-view">
      <i class="fas fa-eye"></i> <?= $fr ? "Voir la trousse d'intégration" : 'View Onboarding Package' ?>
    </a>
    <a href="<?= url('supplier/documents/onboarding.pdf') ?>" download class="btn-replace">
      <i class="fas fa-download"></i> <?= $fr ? 'Télécharger le PDF' : 'Download PDF' ?>
    </a>
  </div>
  <p style="font-size:13px;color:var(--gray-500);margin:0;">
    <?= $fr
      ? "Votre guide complet pour démarrer avec OCSAPP - services, processus, tarification et contacts."
      : "Your complete guide to getting started with OCSAPP - services, processes, pricing, and contacts."
    ?>
  </p>
</div>

<script>
function toggleReplace(field) {
  const el = document.getElementById('replace-' + field);
  if (el) el.classList.toggle('active');
}
function handleFileChange(input, labelId, nameId) {
  const label = document.getElementById(labelId);
  const nameEl = document.getElementById(nameId);
  const file = input.files[0];
  if (file) {
    label.classList.add('has-file');
    nameEl.textContent = file.name;
  } else {
    label.classList.remove('has-file');
    nameEl.textContent = nameEl.dataset.empty || '';
  }
}
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
