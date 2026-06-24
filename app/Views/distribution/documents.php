<?php
$currentLang = $currentLang ?? $_SESSION['language'] ?? 'fr';
$t = [
    'en' => [
        'page_title'            => 'My Documents',
        'page_subtitle'         => 'Your business documents, agreements, and onboarding resources.',
        'info_formats'          => 'Accepted formats: <strong>PDF, JPG, PNG</strong> — Max <strong>5 MB</strong> per file. Documents are kept confidential and used only for verification purposes.',
        'action_required'       => 'Action required',
        // Certificate card
        'cert_name'             => 'Certificate of Incorporation',
        'decl_name'             => 'Declaration of Registration',
        'badge_verified'        => 'Verified',
        'badge_under_review'    => 'Uploaded — Under Review',
        'badge_missing'         => 'Not yet uploaded',
        'view_document'         => 'View Document',
        'replace'               => 'Replace',
        'upload_new'            => 'Upload New',
        'replace_hint'          => 'This will replace the current document.',
        'upload'                => 'Upload',
        'upload_hint'           => 'Accepted: PDF, JPG, PNG — Max 5MB',
        'choose_file'           => 'Choose file',
        'no_file_chosen'        => 'No file chosen',
        // Agreement card
        'agreement_name'        => 'Distribution Service Agreement',
        'signed_on'             => 'Signed on',
        'sig_required'          => 'Signature required',
        'provided_by'           => 'Provided by OCSAPP',
        'view_agreement'        => 'View Agreement (FR/EN)',
        'download_pdf'          => 'Download PDF',
        'you_signed'            => 'You signed this agreement on',
        'confirm_sign'          => 'Confirm & Sign',
        // Onboarding card
        'onboarding_name'       => 'Business Onboarding Package',
        'available'             => 'Available',
        'view_onboarding'       => 'View Onboarding Package',
        'onboarding_desc'       => 'Your complete guide to getting started with OCSAPP Distribution - services, processes, pricing, and contacts.',
        'req_section_title'     => 'Documents Requested by OCSAPP',
        'req_deadline'          => 'Deadline',
        'req_message'           => 'Message',
        'req_upload_btn'        => 'Submit Document',
        'req_overdue'           => 'Overdue',
        // Account History
        'history_title'         => 'Account History',
        'history_empty'         => 'No activity recorded yet.',
        'history_by_ocsapp'     => 'OCSAPP',
        'history_by_you'        => 'You',
    ],
    'fr' => [
        'page_title'            => 'Mes documents',
        'page_subtitle'         => 'Vos documents d\'affaires, accords et ressources d\'intégration.',
        'info_formats'          => 'Formats acceptés : <strong>PDF, JPG, PNG</strong> — Max <strong>5 Mo</strong> par fichier. Les documents sont traités de façon confidentielle et utilisés uniquement à des fins de vérification.',
        'action_required'       => 'Action requise',
        // Certificate card
        'cert_name'             => 'Certificat de constitution',
        'decl_name'             => 'Déclaration d\'immatriculation',
        'badge_verified'        => 'Vérifié',
        'badge_under_review'    => 'Téléchargé — En cours d\'examen',
        'badge_missing'         => 'Pas encore téléchargé',
        'view_document'         => 'Voir le document',
        'replace'               => 'Remplacer',
        'upload_new'            => 'Télécharger un nouveau',
        'replace_hint'          => 'Cela remplacera le document actuel.',
        'upload'                => 'Télécharger',
        'upload_hint'           => 'Accepté : PDF, JPG, PNG — Max 5 Mo',
        'choose_file'           => 'Choisir un fichier',
        'no_file_chosen'        => 'Aucun fichier sélectionné',
        // Agreement card
        'agreement_name'        => 'Accord de services de distribution',
        'signed_on'             => 'Signé le',
        'sig_required'          => 'Signature requise',
        'provided_by'           => 'Fourni par OCSAPP',
        'view_agreement'        => 'Voir l\'accord (FR/EN)',
        'download_pdf'          => 'Télécharger le PDF',
        'you_signed'            => 'Vous avez signé cet accord le',
        'confirm_sign'          => 'Confirmer et signer',
        // Onboarding card
        'onboarding_name'       => 'Trousse d\'intégration des entreprises',
        'available'             => 'Disponible',
        'view_onboarding'       => 'Voir la trousse d\'intégration',
        'onboarding_desc'       => 'Votre guide complet pour démarrer avec OCSAPP Distribution - services, processus, tarification et contacts.',
        // Document requests
        'req_section_title'     => 'Documents requis par OCSAPP',
        'req_deadline'          => 'Échéance',
        'req_message'           => 'Message',
        'req_upload_btn'        => 'Soumettre le document',
        'req_overdue'           => 'En retard',
        // Account History
        'history_title'         => 'Historique du compte',
        'history_empty'         => 'Aucune activité enregistrée.',
        'history_by_ocsapp'     => 'OCSAPP',
        'history_by_you'        => 'Vous',
    ],
][$currentLang] ?? [];

$currentPage = 'documents';
$_docT = $t;
require __DIR__ . '/layout-header.php';
$t = $_docT; unset($_docT);
?>

<style>
  .docs-header { margin-bottom: 28px; }
  .docs-header p { font-size: 14px; color: var(--gray-600); margin-top: 4px; }

  .doc-card {
    background: white; border-radius: 12px; padding: 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    border: 3px solid var(--gray-100);
    transition: border-color 0.2s;
  }
  .doc-card.uploaded  { border-color: #bbf7d0; }
  .doc-card.missing   { border-color: #fecaca; }

  .doc-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
  .doc-card-title  { display: flex; align-items: center; gap: 12px; }
  .doc-card-title i { font-size: 28px; }
  .doc-card-title .doc-name   { font-size: 15px; font-weight: 700; color: var(--gray-700); }
  .doc-card-title .doc-status { font-size: 12px; margin-top: 2px; }

  .badge-uploaded  { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:#fef3c7; color:#92400e; }
  .badge-approved  { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:#d1fae5; color:#065f46; }
  .badge-missing   { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; background:#fee2e2; color:#991b1b; }

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

  .docs-grid { display: flex; flex-direction: column; gap: 16px; }

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

  @media (max-width: 600px) {
    .doc-card { padding: 16px; }
    .doc-card-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .upload-form { flex-direction: column; align-items: stretch; }
    .btn-upload, .btn-view, .btn-replace, .btn-file-pick { width: 100%; justify-content: center; }
    .doc-actions { flex-direction: column; }
  }

  .info-box {
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    padding: 14px 18px; font-size: 13px; color: #065f46; margin-bottom: 24px;
    display: flex; gap: 10px; align-items: flex-start;
  }
  .info-box i { margin-top: 1px; flex-shrink: 0; }

  .agreement-banner {
    background: #fffbeb; border: 2px solid #f59e0b; border-radius: 10px;
    padding: 14px 18px; font-size: 13px; color: #78350f; margin-bottom: 20px;
    display: flex; gap: 12px; align-items: flex-start;
  }
  .agreement-banner i { color: #f59e0b; margin-top: 1px; flex-shrink: 0; font-size: 16px; }
  .agreement-banner strong { display: block; margin-bottom: 2px; font-size: 14px; }

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
</style>

<?php if (!empty($flash['success'])): ?>
  <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($flash['success']) ?></div>
<?php elseif (!empty($flash['error'])): ?>
  <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($flash['error']) ?></div>
<?php endif; ?>

<?php if (!empty($flash['agreement_required'])): ?>
  <div class="agreement-banner">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
      <strong><?= $t['action_required'] ?></strong>
      <?= htmlspecialchars($flash['agreement_required']) ?>
    </div>
  </div>
<?php endif; ?>

<div class="docs-header">
  <div class="page-header">
    <h2 class="page-title"><i class="fas fa-folder-open"></i> <?= $t['page_title'] ?></h2>
  </div>
  <p><?= $t['page_subtitle'] ?></p>
</div>

<div class="info-box">
  <i class="fas fa-info-circle"></i>
  <span><?= $t['info_formats'] ?></span>
</div>

<?php
$docCards = [
    'doc_certificate' => $t['cert_name'],
    'doc_declaration' => $t['decl_name'],
];
$isApproved = ($profile['status'] ?? '') === 'approved';
$pendingRequests = $pendingRequests ?? [];
?>

<?php if (!empty($pendingRequests)): ?>
<div style="background:#fff7ed;border:2px solid #f97316;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
    <h3 style="margin:0 0 16px;font-size:15px;font-weight:700;color:#9a3412;display:flex;align-items:center;gap:8px;">
        <i class="fas fa-exclamation-circle"></i> <?= $t['req_section_title'] ?>
    </h3>
    <div style="display:flex;flex-direction:column;gap:14px;">
    <?php foreach ($pendingRequests as $req): ?>
    <?php
        $isOverdue = !empty($req['deadline']) && strtotime($req['deadline']) < strtotime('today');
        $deadlineStr = !empty($req['deadline']) ? date('M j, Y', strtotime($req['deadline'])) : null;
        $uploadId = 'req-upload-' . $req['id'];
        $labelId  = 'req-label-'  . $req['id'];
        $nameId   = 'req-name-'   . $req['id'];
    ?>
    <div style="background:white;border-radius:10px;padding:16px;border:1px solid #fed7aa;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;gap:10px;">
            <div>
                <div style="font-size:14px;font-weight:700;color:#1f2937;"><?= htmlspecialchars($req['doc_label']) ?></div>
                <?php if ($deadlineStr): ?>
                <div style="font-size:12px;color:<?= $isOverdue ? '#dc2626' : '#6b7280' ?>;margin-top:3px;">
                    <?= $t['req_deadline'] ?>: <strong><?= $deadlineStr ?></strong>
                    <?php if ($isOverdue): ?> — <span style="color:#dc2626;font-weight:700;"><?= $t['req_overdue'] ?></span><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <span style="background:#fff7ed;color:#c2410c;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;white-space:nowrap;">
                <i class="fas fa-clock"></i> <?= $currentLang === 'fr' ? 'En attente' : 'Pending' ?>
            </span>
        </div>
        <?php if (!empty($req['message'])): ?>
        <div style="background:#f9fafb;border-left:3px solid #f97316;padding:8px 12px;border-radius:0 6px 6px 0;font-size:13px;color:#374151;margin-bottom:12px;">
            <strong><?= $t['req_message'] ?>:</strong> <?= htmlspecialchars($req['message']) ?>
        </div>
        <?php endif; ?>
        <!-- Upload form for this document request -->
        <form method="POST" action="<?= url('distribution/documents/upload') ?>" enctype="multipart/form-data" class="upload-form">
            <?= csrfField() ?>
            <input type="hidden" name="doc_type" value="<?= htmlspecialchars($req['doc_type']) ?>">
            <input type="hidden" name="request_id" value="<?= (int)$req['id'] ?>">
            <label for="<?= $uploadId ?>" class="btn-file-pick" id="<?= $labelId ?>">
                <i class="fas fa-folder-open"></i> <?= $t['choose_file'] ?>
            </label>
            <input type="file" id="<?= $uploadId ?>" name="document" accept=".pdf,.jpg,.jpeg,.png" required
                onchange="handleFileChange(this,'<?= $labelId ?>','<?= $nameId ?>')">
            <span class="file-name-display" id="<?= $nameId ?>"><?= $t['no_file_chosen'] ?></span>
            <button type="submit" class="btn-upload">
                <i class="fas fa-cloud-upload-alt"></i> <?= $t['req_upload_btn'] ?>
            </button>
        </form>
        <div class="upload-hint"><?= $t['upload_hint'] ?></div>
    </div>
    <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="docs-grid">
<?php foreach ($docCards as $docField => $docLabel):
  $hasFile   = !empty($profile[$docField]);
  $ext       = $hasFile ? strtolower(pathinfo($profile[$docField], PATHINFO_EXTENSION)) : '';
  $icon      = $ext === 'pdf' ? 'fa-file-pdf' : ($hasFile ? 'fa-file-image' : 'fa-file-circle-question');
  $iconColor = $ext === 'pdf' ? '#dc2626' : ($hasFile ? '#3b82f6' : '#d1d5db');
  $cardClass = $hasFile ? 'uploaded' : 'missing';
  $replaceId = 'replace-form-' . $docField;
?>
  <div class="doc-card <?= $cardClass ?>">
    <div class="doc-card-header">
      <div class="doc-card-title">
        <i class="fas <?= $icon ?>" style="color: <?= $iconColor ?>;"></i>
        <div>
          <div class="doc-name"><?= htmlspecialchars($docLabel) ?></div>
          <div class="doc-status">
            <?php if ($hasFile && $isApproved): ?>
              <span class="badge-approved"><i class="fas fa-check-circle"></i> <?= $t['badge_verified'] ?></span>
            <?php elseif ($hasFile): ?>
              <span class="badge-uploaded"><i class="fas fa-clock"></i> <?= $t['badge_under_review'] ?></span>
            <?php else: ?>
              <span class="badge-missing"><i class="fas fa-exclamation-circle"></i> <?= $t['badge_missing'] ?></span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <?php if ($hasFile): ?>
      <div class="doc-file-info">
        <i class="fas fa-paperclip"></i>
        <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(basename($profile[$docField])) ?></span>
        <span style="color:var(--gray-400);text-transform:uppercase;font-weight:600;font-size:11px;"><?= strtoupper($ext) ?></span>
      </div>
      <div class="doc-actions">
        <a href="<?= url($profile[$docField]) ?>" target="_blank" class="btn-view">
          <i class="fas fa-external-link-alt"></i> <?= $t['view_document'] ?>
        </a>
        <button type="button" class="btn-replace" onclick="document.getElementById('<?= $replaceId ?>').classList.toggle('active')">
          <i class="fas fa-sync-alt"></i> <?= $t['replace'] ?>
        </button>
      </div>
      <div class="replace-form" id="<?= $replaceId ?>">
        <form method="POST" action="<?= url('distribution/documents/upload') ?>" enctype="multipart/form-data" class="upload-form">
          <?= csrfField() ?>
          <input type="hidden" name="doc_type" value="<?= $docField ?>">
          <label for="file-replace-<?= $docField ?>" class="btn-file-pick" id="label-replace-<?= $docField ?>">
            <i class="fas fa-folder-open"></i> <?= $t['choose_file'] ?>
          </label>
          <input type="file" id="file-replace-<?= $docField ?>" name="document" accept=".pdf,.jpg,.jpeg,.png" required
            onchange="handleFileChange(this,'label-replace-<?= $docField ?>','name-replace-<?= $docField ?>')">
          <span class="file-name-display" id="name-replace-<?= $docField ?>"><?= $t['no_file_chosen'] ?></span>
          <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> <?= $t['upload_new'] ?></button>
        </form>
        <div class="upload-hint"><?= $t['replace_hint'] ?></div>
      </div>
    <?php else: ?>
      <form method="POST" action="<?= url('distribution/documents/upload') ?>" enctype="multipart/form-data" class="upload-form">
        <?= csrfField() ?>
        <input type="hidden" name="doc_type" value="<?= $docField ?>">
        <label for="file-upload-<?= $docField ?>" class="btn-file-pick" id="label-upload-<?= $docField ?>">
          <i class="fas fa-folder-open"></i> <?= $t['choose_file'] ?>
        </label>
        <input type="file" id="file-upload-<?= $docField ?>" name="document" accept=".pdf,.jpg,.jpeg,.png" required
          onchange="handleFileChange(this,'label-upload-<?= $docField ?>','name-upload-<?= $docField ?>')">
        <span class="file-name-display" id="name-upload-<?= $docField ?>"><?= $t['no_file_chosen'] ?></span>
        <button type="submit" class="btn-upload"><i class="fas fa-cloud-upload-alt"></i> <?= $t['upload'] ?></button>
      </form>
      <div class="upload-hint"><?= $t['upload_hint'] ?></div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<?php
$hasAgreed     = !empty($profile['agreement_agreed_at']);
$agreedAt      = $hasAgreed ? date('F j, Y', strtotime($profile['agreement_agreed_at'])) : null;
$agreedVersion = $profile['agreement_version'] ?? null;
?>

<div class="doc-card <?= $hasAgreed ? 'agreed' : 'provided' ?>" style="margin-top:16px;">
  <div class="doc-card-header">
    <div class="doc-card-title">
      <i class="fas fa-file-contract" style="color:#16a34a;font-size:28px;"></i>
      <div>
        <div class="doc-name"><?= $t['agreement_name'] ?></div>
        <div class="doc-status">
          <?php if ($hasAgreed): ?>
            <span class="badge-approved"><i class="fas fa-check-circle"></i> <?= $t['signed_on'] ?> <?= $agreedAt ?></span>
          <?php else: ?>
            <span class="badge-missing"><i class="fas fa-exclamation-circle"></i> <?= $t['sig_required'] ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <span class="doc-provided-badge"><i class="fas fa-building"></i> <?= $t['provided_by'] ?></span>
  </div>

  <div class="doc-actions-row">
    <a href="<?= url('distribution/documents/agreement.pdf') ?>" target="_blank" class="btn-view">
      <i class="fas fa-eye"></i> <?= $t['view_agreement'] ?>
    </a>
    <a href="<?= url('distribution/documents/agreement.pdf') ?>" download class="btn-replace">
      <i class="fas fa-download"></i> <?= $t['download_pdf'] ?>
    </a>
  </div>

  <?php if ($hasAgreed): ?>
    <div class="agreement-confirm-signed">
      <i class="fas fa-check-circle"></i>
      <span><?= $t['you_signed'] ?> <strong><?= $agreedAt ?></strong><?= $agreedVersion ? ' (v' . $agreedVersion . ')' : '' ?>.</span>
    </div>
  <?php else: ?>
    <div class="agreement-confirm">
      <form method="POST" action="<?= url('distribution/documents/confirm-agreement') ?>">
        <?= csrfField() ?>
        <div class="checkbox-row">
          <input type="checkbox" id="agree_check" name="agreed" value="1" required
                 onchange="document.getElementById('btn_confirm').disabled = !this.checked;">
          <label for="agree_check">
            J'ai lu et j'accepte l'Accord de services de distribution d'OCSAPP / I have read and agree to the OCSAPP Distribution Service Agreement (v<?= $currentAgreementVersion ?>).
          </label>
        </div>
        <button type="submit" id="btn_confirm" class="btn-confirm" disabled>
          <i class="fas fa-signature"></i> <?= $t['confirm_sign'] ?>
        </button>
      </form>
    </div>
  <?php endif; ?>
</div>

<div class="doc-card provided" style="margin-top:16px;">
  <div class="doc-card-header">
    <div class="doc-card-title">
      <i class="fas fa-box-open" style="color:#2563eb;font-size:28px;"></i>
      <div>
        <div class="doc-name"><?= $t['onboarding_name'] ?></div>
        <div class="doc-status">
          <span class="badge-approved"><i class="fas fa-check-circle"></i> <?= $t['available'] ?></span>
        </div>
      </div>
    </div>
    <span class="doc-provided-badge"><i class="fas fa-building"></i> <?= $t['provided_by'] ?></span>
  </div>
  <div class="doc-actions-row">
    <a href="<?= url('distribution/documents/onboarding.pdf') ?>" target="_blank" class="btn-view">
      <i class="fas fa-eye"></i> <?= $t['view_onboarding'] ?>
    </a>
    <a href="<?= url('distribution/documents/onboarding.pdf') ?>" download class="btn-replace">
      <i class="fas fa-download"></i> <?= $t['download_pdf'] ?>
    </a>
  </div>
  <p style="font-size:13px;color:var(--gray-500);margin:0;"><?= $t['onboarding_desc'] ?></p>
</div>

<?php if (!empty($activityLog)): ?>
<?php
$actionLabels = [
    'fr' => [
        'account_approved'   => 'Compte approuvé',
        'account_rejected'   => 'Demande refusée',
        'account_suspended'  => 'Compte suspendu',
        'account_activated'  => 'Compte activé',
        'tier_updated'       => 'Niveau de compte mis à jour',
        'deadline_extended'  => 'Délai de vérification prolongé',
        'document_requested' => 'Document demandé par OCSAPP',
        'document_verified'  => 'Document vérifié',
        'document_rejected'  => 'Document refusé',
        'document_uploaded'  => 'Document téléversé',
        'agreement_signed'   => 'Accord signé',
    ],
    'en' => [
        'account_approved'   => 'Account Approved',
        'account_rejected'   => 'Application Rejected',
        'account_suspended'  => 'Account Suspended',
        'account_activated'  => 'Account Activated',
        'tier_updated'       => 'Account Tier Updated',
        'deadline_extended'  => 'Verification Deadline Extended',
        'document_requested' => 'Document Requested by OCSAPP',
        'document_verified'  => 'Document Verified',
        'document_rejected'  => 'Document Rejected',
        'document_uploaded'  => 'Document Uploaded',
        'agreement_signed'   => 'Agreement Signed',
    ],
][$currentLang] ?? [];
?>
<div style="margin-top:24px;background:white;border-radius:12px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
  <h3 style="font-size:15px;font-weight:700;color:var(--gray-700);margin:0 0 16px;display:flex;align-items:center;gap:8px;">
    <i class="fas fa-history" style="color:var(--primary);"></i>
    <?= $t['history_title'] ?>
  </h3>
  <div style="display:flex;flex-direction:column;gap:0;">
    <?php foreach ($activityLog as $i => $entry): ?>
      <?php
      $isBusiness = $entry['actor'] === 'business';
      $iconMap = [
          'account_approved'   => ['check-circle',  '#15803d', '#dcfce7'],
          'account_rejected'   => ['times-circle',  '#dc2626', '#fee2e2'],
          'account_suspended'  => ['ban',            '#d97706', '#fef3c7'],
          'account_activated'  => ['check',          '#15803d', '#dcfce7'],
          'tier_updated'       => ['star',           '#1d4ed8', '#dbeafe'],
          'deadline_extended'  => ['calendar-plus',  '#6b7280', '#f3f4f6'],
          'document_requested' => ['file-circle-exclamation', '#d97706', '#fef3c7'],
          'document_verified'  => ['file-circle-check', '#15803d', '#dcfce7'],
          'document_rejected'  => ['file-circle-xmark', '#dc2626', '#fee2e2'],
          'document_uploaded'  => ['file-arrow-up',  '#1d4ed8', '#dbeafe'],
          'agreement_signed'   => ['file-signature', '#15803d', '#dcfce7'],
      ];
      $ic = $iconMap[$entry['action_type']] ?? ['clock', '#6b7280', '#f3f4f6'];
      $label = $actionLabels[$entry['action_type']] ?? ucwords(str_replace('_', ' ', $entry['action_type']));
      $actor = $isBusiness ? $t['history_by_you'] : $t['history_by_ocsapp'];
      ?>
      <div style="display:flex;gap:12px;padding:10px 0;<?= $i > 0 ? 'border-top:1px solid #f3f4f6;' : '' ?>">
        <div style="flex-shrink:0;width:32px;height:32px;border-radius:50%;background:<?= $ic[2] ?>;display:flex;align-items:center;justify-content:center;margin-top:2px;">
          <i class="fas fa-<?= $ic[0] ?>" style="font-size:12px;color:<?= $ic[1] ?>;"></i>
        </div>
        <div style="flex:1;">
          <div style="font-size:13px;font-weight:600;color:var(--gray-700);"><?= htmlspecialchars($label) ?></div>
          <div style="font-size:11px;color:var(--gray-400);margin-top:2px;">
            <?= date('d M Y H:i', strtotime($entry['created_at'])) ?> &middot; <?= htmlspecialchars($actor) ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<script>
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
