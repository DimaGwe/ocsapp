<?php
$currentPage = 'compliance';
if (empty($isDocTab)) include __DIR__ . '/layout-header.php';

/**
 * $docs  — array keyed by doc_type, each row from driver_compliance_docs (or null if not yet created)
 * $application — driver_applications row
 */

$docDefs = [
    'class5_license' => [
        'label'       => $fr ? 'Permis de conduire classe 5' : 'Class 5 Driver\'s License',
        'icon'        => 'id-card',
        'description' => $fr ? 'Un permis de conduire classe 5 valide et non suspendu.' : 'A valid, non-suspended Class 5 driver\'s license.',
        'date_label'  => $fr ? 'Date d\'expiration' : 'Expiry Date',
        'date_help'   => $fr ? 'Entrez la date d\'expiration indiquée sur votre permis.' : 'Enter the expiry date shown on your license.',
        'subtype'     => false,
        'date_future' => true,
    ],
    'saaq_record' => [
        'label'       => $fr ? 'Dossier de conduite SAAQ' : 'SAAQ Driving Record',
        'icon'        => 'car-side',
        'description' => $fr ? 'Un dossier de conduite SAAQ récent (daté de moins de 30 jours) confirmant 3 points d\'inaptitude ou moins.' : 'A recent SAAQ driving record (dated within the last 30 days) confirming 3 demerit points or fewer.',
        'date_label'  => $fr ? 'Date du document' : 'Document Date',
        'date_help'   => $fr ? 'Doit dater de moins de 30 jours.' : 'Must be within the last 30 days.',
        'subtype'     => false,
        'date_future' => false,
        'max_days_old'=> 30,
    ],
    'commercial_insurance' => [
        'label'       => $fr ? 'Preuve d\'assurance commerciale' : 'Proof of Commercial Insurance',
        'icon'        => 'file-shield',
        'description' => $fr ? 'Un certificat d\'assurance (COI) mentionnant explicitement "usage de livraison" ou "usage commercial".' : 'A Certificate of Insurance (COI) explicitly stating "delivery use" or "commercial use".',
        'date_label'  => $fr ? 'Date d\'expiration de la police' : 'Policy Expiry Date',
        'date_help'   => $fr ? 'Entrez la date d\'expiration de la police indiquée sur votre COI.' : 'Enter the policy expiry date from your COI.',
        'subtype'     => false,
        'date_future' => true,
    ],
    'vehicle_registration' => [
        'label'       => $fr ? 'Immatriculation du véhicule' : 'Vehicle Registration',
        'icon'        => 'file-contract',
        'description' => $fr ? 'Preuve que le véhicule est légalement immatriculé. Remarque : si utilisé principalement à des fins commerciales, la SAAQ peut exiger une immatriculation commerciale.' : 'Proof that the vehicle is legally registered. Note: if used primarily for business, SAAQ may require commercial registration.',
        'date_label'  => $fr ? 'Date d\'expiration de l\'immatriculation' : 'Registration Expiry Date',
        'date_help'   => $fr ? 'Facultatif - entrez si indiqué sur votre immatriculation.' : 'Optional - enter if shown on your registration.',
        'subtype'     => false,
        'date_future' => true,
        'date_required' => false,
    ],
    'work_authorization' => [
        'label'       => $fr ? 'Preuve d\'autorisation de travail' : 'Proof of Work Authorization',
        'icon'        => 'passport',
        'description' => $fr ? 'Preuve de citoyenneté canadienne, de résidence permanente ou d\'un permis de travail valide.' : 'Proof of Canadian citizenship, permanent residency, or a valid work permit.',
        'date_label'  => $fr ? 'Date d\'expiration / d\'émission du document' : 'Document Expiry / Issue Date',
        'date_help'   => $fr ? 'Entrez la date d\'expiration (ou la date d\'émission pour les documents de citoyenneté).' : 'Enter the expiry date (or issue date for citizenship documents).',
        'subtype'     => true,
        'subtype_label'=> $fr ? 'Type de document' : 'Document Type',
        'subtype_options' => [
            'canadian_citizen'    => $fr ? 'Citoyenneté canadienne' : 'Canadian Citizenship',
            'permanent_resident'  => $fr ? 'Résidence permanente (carte RP)' : 'Permanent Residency (PR Card)',
            'work_permit'         => $fr ? 'Permis de travail' : 'Work Permit',
            'other'               => $fr ? 'Autre' : 'Other',
        ],
        'date_future' => false,
    ],
];

$statusConfig = [
    'not_uploaded'  => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'icon' => 'circle-minus',       'label' => $fr ? 'Non soumis' : 'Not Submitted'],
    'not_required'  => ['bg' => '#f0fdf4', 'color' => '#16a34a', 'icon' => 'circle-check',       'label' => $fr ? 'Marqué non requis' : 'Marked as Not Required'],
    'uploaded'      => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'clock',              'label' => $fr ? 'En cours d\'examen' : 'Under Review'],
    'verified'      => ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'shield-check',       'label' => $fr ? 'Vérifié' : 'Verified'],
    'flagged'       => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'shield-exclamation', 'label' => $fr ? 'Action requise' : 'Action Required'],
];
?>
<style>
.compliance-grid { display: flex; flex-direction: column; gap: 20px; width: 100%; }
.doc-card { background: #fff; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,.08); overflow: hidden; }
.doc-card-header { display: flex; align-items: center; gap: 14px; padding: 18px 22px; border-bottom: 1px solid #f3f4f6; }
.doc-card-icon { width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
.doc-card-title { font-size: 15px; font-weight: 700; color: #111827; }
.doc-card-desc { font-size: 12px; color: #6b7280; margin-top: 2px; line-height: 1.5; }
.doc-card-status { margin-left: auto; flex-shrink: 0; }
.status-pill { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.doc-card-body { padding: 20px 22px; }

.upload-zone { border: 2px dashed #d1d5db; border-radius: 10px; padding: 28px 16px; text-align: center; cursor: pointer; transition: .2s; position: relative; background: #f9fafb; }
.upload-zone:hover, .upload-zone.drag-over { border-color: #2563eb; background: #eff6ff; }
.upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
.upload-zone i.upload-icon { font-size: 28px; color: #9ca3af; margin-bottom: 8px; display: block; }
.zone-title { font-size: 13px; font-weight: 600; color: #374151; }
.zone-sub { font-size: 11px; color: #9ca3af; margin-top: 2px; }

.file-preview { display: none; align-items: center; gap: 10px; padding: 10px 14px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin-top: 8px; }
.file-preview i { color: #16a34a; font-size: 18px; }
.file-name { font-size: 12px; font-weight: 600; color: #166534; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.file-remove { background: none; border: none; color: #dc2626; cursor: pointer; font-size: 14px; }

.form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
.form-group-sm { margin-bottom: 14px; }
.form-label-sm { font-size: 12px; font-weight: 600; color: #374151; display: block; margin-bottom: 5px; }
.form-input-sm, .form-select-sm { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; font-family: inherit; color: #111827; background: white; }
.form-input-sm:focus, .form-select-sm:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
.form-help { font-size: 11px; color: #9ca3af; margin-top: 3px; }

.btn-row { display: flex; gap: 10px; margin-top: 16px; }
.btn-upload-sm { flex: 1; padding: 11px; background: #1e40af; color: white; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 6px; transition: .2s; }
.btn-upload-sm:hover { background: #1d4ed8; }
.btn-upload-sm:disabled { background: #93c5fd; cursor: not-allowed; }
.btn-not-required { padding: 11px 16px; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: inherit; transition: .2s; white-space: nowrap; }
.btn-not-required:hover { background: #e5e7eb; }

.detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
.detail-row:last-child { border-bottom: none; }
.detail-label { color: #6b7280; font-weight: 500; }
.detail-value { color: #111827; font-weight: 600; }

.flagged-notice { background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #991b1b; margin-bottom: 14px; }
.uploaded-notice { background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #92400e; margin-bottom: 14px; }

.progress-bar-wrap { background: #f3f4f6; border-radius: 12px; padding: 16px 20px; margin-bottom: 20px; display: flex; align-items: center; gap: 16px; }
.progress-bar-track { flex: 1; height: 8px; background: #e5e7eb; border-radius: 8px; overflow: hidden; }
.progress-bar-fill { height: 8px; background: linear-gradient(90deg, #2563eb, #60a5fa); border-radius: 8px; transition: width .5s; }
.progress-label { font-size: 13px; color: #6b7280; white-space: nowrap; }

@media (max-width: 600px) { .form-row-2 { grid-template-columns: 1fr; } .btn-row { flex-direction: column; } }
</style>

<?php
// Count how many docs are complete (verified, not_required)
$total = count($docDefs);
$done  = 0;
foreach ($docDefs as $type => $def) {
    $row = $docs[$type] ?? null;
    $st  = $row['status'] ?? 'not_uploaded';
    if (in_array($st, ['verified', 'not_required'])) $done++;
}
?>

<div class="compliance-grid">

  <!-- Progress bar -->
  <div class="progress-bar-wrap">
    <i class="fas fa-list-check" style="color:#2563eb; font-size:20px;"></i>
    <div style="flex:1;">
      <div style="font-size:14px; font-weight:700; color:#111827; margin-bottom:6px;"><?php echo $fr ? 'Documents de conformité' : 'Compliance Documents'; ?></div>
      <div class="progress-bar-track">
        <div class="progress-bar-fill" style="width:<?= $total > 0 ? round(($done/$total)*100) : 0 ?>%;"></div>
      </div>
    </div>
    <div class="progress-label"><?= $done ?>/<?= $total ?> <?php echo $fr ? 'complété' : 'complete'; ?></div>
  </div>

  <?php foreach ($docDefs as $type => $def):
    $row    = $docs[$type] ?? null;
    $status = $row['status'] ?? 'not_uploaded';
    $sc     = $statusConfig[$status] ?? $statusConfig['not_uploaded'];
    $isLocked = in_array($status, ['verified', 'not_required']);
  ?>
  <div class="doc-card">
    <div class="doc-card-header">
      <div class="doc-card-icon"><i class="fas fa-<?= $def['icon'] ?>"></i></div>
      <div>
        <div class="doc-card-title"><?= htmlspecialchars($def['label']) ?></div>
        <div class="doc-card-desc"><?= htmlspecialchars($def['description']) ?></div>
      </div>
      <div class="doc-card-status">
        <span class="status-pill" style="background:<?= $sc['bg'] ?>; color:<?= $sc['color'] ?>;">
          <i class="fas fa-<?= $sc['icon'] ?>"></i> <?= $sc['label'] ?>
        </span>
      </div>
    </div>

    <div class="doc-card-body">

      <?php if ($status === 'flagged'): ?>
        <div class="flagged-notice">
          <i class="fas fa-triangle-exclamation"></i> <strong><?php echo $fr ? 'Action requise :' : 'Action Required:'; ?></strong>
          <?= !empty($row['admin_notes']) ? htmlspecialchars($row['admin_notes']) : ($fr ? 'Veuillez télécharger un nouveau document.' : 'Please upload a new document.') ?>
          <?php echo $fr ? 'Contactez <strong>support@ocsapp.ca</strong> si vous avez des questions.' : 'Contact <strong>support@ocsapp.ca</strong> if you have questions.'; ?>
        </div>
      <?php elseif ($status === 'uploaded'): ?>
        <div class="uploaded-notice">
          <?php
          $uploadedDateStr = '';
          if ($row['uploaded_at']) {
              $uploadedTs = strtotime($row['uploaded_at']);
              if ($fr) {
                  $frMonthsComp = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                  $uploadedDateStr = (int)date('j', $uploadedTs) . ' ' . $frMonthsComp[(int)date('n', $uploadedTs)] . ' ' . date('Y', $uploadedTs);
              } else {
                  $uploadedDateStr = date('M j, Y', $uploadedTs);
              }
          }
          ?>
          <i class="fas fa-clock"></i> <?php if ($fr): ?>Soumis le <?= $uploadedDateStr ?> - en cours d'examen. Vous pouvez le remplacer ci-dessous si nécessaire.<?php else: ?>Submitted <?= $uploadedDateStr ?> - under review. You can replace it below if needed.<?php endif; ?>
        </div>
      <?php elseif ($status === 'verified'): ?>
        <?php
        $verifiedDateStr = '-';
        if ($row['verified_at']) {
            $vTs = strtotime($row['verified_at']);
            if ($fr) {
                $frMonthsComp = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                $verifiedDateStr = (int)date('j', $vTs) . ' ' . $frMonthsComp[(int)date('n', $vTs)] . ' ' . date('Y', $vTs);
            } else {
                $verifiedDateStr = date('M j, Y', $vTs);
            }
        }
        ?>
        <div class="detail-row"><span class="detail-label"><?php echo $fr ? 'Vérifié le' : 'Verified On'; ?></span><span class="detail-value"><?= $verifiedDateStr ?></span></div>
        <?php if (!empty($row['doc_subtype'])): ?>
        <div class="detail-row"><span class="detail-label"><?php echo $fr ? 'Type de document' : 'Document Type'; ?></span><span class="detail-value"><?= htmlspecialchars($def['subtype_options'][$row['doc_subtype']] ?? $row['doc_subtype']) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($row['doc_date'])): ?>
        <?php
        $docDateStr = '';
        $dTs = strtotime($row['doc_date']);
        if ($fr) {
            $frMonthsComp = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
            $docDateStr = (int)date('j', $dTs) . ' ' . $frMonthsComp[(int)date('n', $dTs)] . ' ' . date('Y', $dTs);
        } else {
            $docDateStr = date('M j, Y', $dTs);
        }
        ?>
        <div class="detail-row"><span class="detail-label"><?= htmlspecialchars($def['date_label']) ?></span><span class="detail-value"><?= $docDateStr ?></span></div>
        <?php endif; ?>
        <?php if (!empty($row['admin_notes'])): ?>
        <div style="background:#f0fdf4; border-radius:8px; padding:10px 12px; font-size:12px; color:#374151; border-left:3px solid #16a34a; margin-top:8px;">
          <strong><?php echo $fr ? 'Notes admin :' : 'Admin Notes:'; ?></strong> <?= nl2br(htmlspecialchars($row['admin_notes'])) ?>
        </div>
        <?php endif; ?>
      <?php elseif ($status === 'not_required'): ?>
        <div style="font-size:13px; color:#6b7280;"><?php echo $fr ? 'Ce document a été marqué comme non requis pour votre rôle.' : 'This document has been marked as not required for your role.'; ?></div>
        <?php if (!empty($row['admin_notes'])): ?>
        <div style="background:#f0fdf4; border-radius:8px; padding:10px 12px; font-size:12px; color:#374151; border-left:3px solid #16a34a; margin-top:8px;">
          <strong><?php echo $fr ? 'Notes admin :' : 'Admin Notes:'; ?></strong> <?= nl2br(htmlspecialchars($row['admin_notes'])) ?>
        </div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if (!$isLocked): ?>
      <!-- Upload form -->
      <form method="POST" action="<?= url('delivery/compliance/upload') ?>" enctype="multipart/form-data"
            id="form_<?= $type ?>" onsubmit="return validateComplianceForm('<?= $type ?>')">
        <?= csrfField() ?>
        <input type="hidden" name="doc_type" value="<?= $type ?>">

        <?php if ($def['subtype']): ?>
        <div class="form-group-sm">
          <label class="form-label-sm"><?= htmlspecialchars($def['subtype_label']) ?> <span style="color:#dc2626;">*</span></label>
          <select name="doc_subtype" class="form-select-sm" required>
            <option value=""><?php echo $fr ? '- Sélectionner -' : '- Select -'; ?></option>
            <?php foreach ($def['subtype_options'] as $val => $lbl): ?>
              <option value="<?= $val ?>" <?= ($row['doc_subtype'] ?? '') === $val ? 'selected' : '' ?>><?= htmlspecialchars($lbl) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="form-group-sm">
          <label class="form-label-sm"><?= htmlspecialchars($def['date_label']) ?><?= ($def['date_required'] ?? true) ? ' <span style="color:#dc2626;">*</span>' : '' ?></label>
          <?php
            $existingDate  = $row['doc_date'] ?? '';
            $preDay = $preMonth = $preYear = 0;
            if ($existingDate && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $existingDate, $em)) {
                $preYear = (int)$em[1]; $preMonth = (int)$em[2]; $preDay = (int)$em[3];
            }
            $isFutureDate  = !empty($def['date_future']);
            $maxDaysOld    = $def['max_days_old'] ?? null;
            $dateRequired  = $def['date_required'] ?? true;
            $cYear         = (int)date('Y');
            if ($isFutureDate)          { $yMin = $cYear;      $yMax = $cYear + 15; }
            elseif ($maxDaysOld !== null){ $yMin = $cYear - 1;  $yMax = $cYear; }
            else                        { $yMin = $cYear - 20;  $yMax = $cYear + 10; }
            $cMonths = $fr
                ? ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre']
                : ['January','February','March','April','May','June','July','August','September','October','November','December'];
          ?>
          <div style="display:flex; gap:6px; flex-wrap:wrap;">
            <select id="cday_<?= $type ?>" class="form-select-sm" style="flex:1; min-width:70px;" onchange="updateCompDate('<?= $type ?>')">
              <option value=""><?php echo $fr ? 'Jour' : 'Day'; ?></option>
              <?php for ($d = 1; $d <= 31; $d++): ?><option value="<?= $d ?>" <?= $preDay === $d ? 'selected' : '' ?>><?= $d ?></option><?php endfor; ?>
            </select>
            <select id="cmonth_<?= $type ?>" class="form-select-sm" style="flex:2; min-width:110px;" onchange="updateCompDate('<?= $type ?>')">
              <option value=""><?php echo $fr ? 'Mois' : 'Month'; ?></option>
              <?php foreach ($cMonths as $mi => $mn): ?><option value="<?= $mi + 1 ?>" <?= $preMonth === ($mi + 1) ? 'selected' : '' ?>><?= $mn ?></option><?php endforeach; ?>
            </select>
            <select id="cyear_<?= $type ?>" class="form-select-sm" style="flex:1; min-width:85px;" onchange="updateCompDate('<?= $type ?>')">
              <option value=""><?php echo $fr ? 'Année' : 'Year'; ?></option>
              <?php for ($y = $yMax; $y >= $yMin; $y--): ?><option value="<?= $y ?>" <?= $preYear === $y ? 'selected' : '' ?>><?= $y ?></option><?php endfor; ?>
            </select>
          </div>
          <input type="hidden" name="doc_date" id="cdate_<?= $type ?>"
                 value="<?= htmlspecialchars($existingDate) ?>"
                 data-required="<?= $dateRequired ? 'true' : 'false' ?>"
                 data-future="<?= $isFutureDate ? 'true' : 'false' ?>"
                 <?= $maxDaysOld !== null ? 'data-max-days="' . (int)$maxDaysOld . '"' : '' ?>>
          <div class="form-help"><?= htmlspecialchars($def['date_help']) ?></div>
        </div>

        <div class="form-group-sm">
          <label class="form-label-sm"><?php echo $fr ? 'Télécharger le document' : 'Upload Document'; ?> <span style="color:#dc2626;">*</span></label>
          <div class="upload-zone" id="zone_<?= $type ?>">
            <input type="file" name="doc_file" id="file_<?= $type ?>" accept=".pdf,.jpg,.jpeg,.png" required
                   onchange="previewComplianceFile(this, '<?= $type ?>')">
            <i class="fas fa-cloud-arrow-up upload-icon" id="icon_<?= $type ?>"></i>
            <div class="zone-title"><?php echo $fr ? 'Cliquez pour télécharger ou glisser-déposer' : 'Click to upload or drag & drop'; ?></div>
            <div class="zone-sub"><?php echo $fr ? 'PDF, JPG ou PNG - max 10 Mo' : 'PDF, JPG, or PNG - max 10 MB'; ?></div>
          </div>
          <div class="file-preview" id="preview_<?= $type ?>">
            <i class="fas fa-file-check"></i>
            <span class="file-name" id="fname_<?= $type ?>"></span>
            <button type="button" class="file-remove" onclick="clearComplianceFile('<?= $type ?>')" title="Remove"><i class="fas fa-times"></i></button>
          </div>
        </div>

        <div class="btn-row">
          <button type="submit" class="btn-upload-sm" id="submit_<?= $type ?>">
            <i class="fas fa-upload"></i>
            <?php
            if ($status === 'uploaded' || $status === 'flagged') {
                echo $fr ? 'Remplacer le document' : 'Replace Document';
            } else {
                echo $fr ? 'Soumettre le document' : 'Submit Document';
            }
            ?>
          </button>
          <button type="button" class="btn-not-required"
                  onclick="markNotRequired('<?= $type ?>', '<?= addslashes($def['label']) ?>')">
            <i class="fas fa-ban"></i> <?php echo $fr ? 'Non requis' : 'Not Required'; ?>
          </button>
        </div>
      </form>
      <?php endif; ?>

    </div>
  </div>
  <?php endforeach; ?>

</div>

<!-- Not Required confirmation modal -->
<div id="notRequiredModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9999; align-items:center; justify-content:center;">
  <div style="background:#fff; border-radius:14px; padding:28px; max-width:420px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.2);">
    <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:8px;"><?php echo $fr ? 'Marquer comme non requis ?' : 'Mark as Not Required?'; ?></h3>
    <p style="font-size:13px; color:#6b7280; margin-bottom:16px;" id="nrModalDesc"></p>
    <div style="margin-bottom:14px;">
      <label style="font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:5px;"><?php echo $fr ? 'Raison' : 'Reason'; ?> <span style="color:#dc2626;">*</span></label>
      <textarea id="nrReason" rows="3" placeholder="<?php echo $fr ? 'Expliquez brièvement pourquoi ce document ne s\'applique pas (ex. livreur à vélo - aucun permis requis)...' : 'Briefly explain why this document does not apply (e.g. bicycle driver - no license required)...'; ?>"
                style="width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-family:inherit; resize:vertical;"></textarea>
    </div>
    <form id="nrForm" method="POST" action="<?= url('delivery/compliance/not-required') ?>">
      <?= csrfField() ?>
      <input type="hidden" name="doc_type" id="nrDocType">
      <input type="hidden" name="reason"   id="nrReasonHidden">
      <div style="display:flex; gap:10px; justify-content:flex-end;">
        <button type="button" onclick="closeNrModal()" style="padding:9px 18px; background:#f3f4f6; border:1px solid #d1d5db; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit;"><?php echo $fr ? 'Annuler' : 'Cancel'; ?></button>
        <button type="button" onclick="submitNrForm()" style="padding:9px 18px; background:#1e40af; color:#fff; border:none; border-radius:8px; font-size:13px; font-weight:700; cursor:pointer; font-family:inherit;"><?php echo $fr ? 'Confirmer' : 'Confirm'; ?></button>
      </div>
    </form>
  </div>
</div>

<script>
var _L = {
  uploading:    <?php echo json_encode($fr ? 'Téléversement...' : 'Uploading...'); ?>,
  markingDesc:  <?php echo json_encode($fr ? 'Vous marquez "%s" comme non requis. Veuillez fournir une brève raison.' : 'You are marking "%s" as not required. Please provide a brief reason.'); ?>,
  enterReason:  <?php echo json_encode($fr ? 'Veuillez entrer une raison.' : 'Please enter a reason.'); ?>,
  selectDate:   <?php echo json_encode($fr ? 'Veuillez sélectionner la date du document.' : 'Please select the document date.'); ?>,
  dateInFuture: <?php echo json_encode($fr ? 'La date doit être dans le futur.' : 'The date must be in the future.'); ?>,
  dateRange:    <?php echo json_encode($fr ? 'La date doit être dans les %d derniers jours.' : 'The date must be within the last %d days.'); ?>
};
function previewComplianceFile(input, type) {
    if (!input.files || !input.files[0]) return;
    document.getElementById('icon_' + type).style.display = 'none';
    document.getElementById('zone_' + type).querySelector('.zone-title').style.display = 'none';
    document.getElementById('zone_' + type).querySelector('.zone-sub').style.display = 'none';
    document.getElementById('fname_' + type).textContent = input.files[0].name;
    document.getElementById('preview_' + type).style.display = 'flex';
}
function clearComplianceFile(type) {
    document.getElementById('file_' + type).value = '';
    document.getElementById('preview_' + type).style.display = 'none';
    document.getElementById('icon_' + type).style.display = '';
    document.getElementById('zone_' + type).querySelector('.zone-title').style.display = '';
    document.getElementById('zone_' + type).querySelector('.zone-sub').style.display = '';
}
function updateCompDate(type) {
    var d = document.getElementById('cday_' + type).value;
    var m = document.getElementById('cmonth_' + type).value;
    var y = document.getElementById('cyear_' + type).value;
    document.getElementById('cdate_' + type).value = (d && m && y)
        ? y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0')
        : '';
}
function validateComplianceForm(type) {
    var el = document.getElementById('cdate_' + type);
    var d  = document.getElementById('cday_' + type).value;
    var m  = document.getElementById('cmonth_' + type).value;
    var y  = document.getElementById('cyear_' + type).value;
    var required = el.getAttribute('data-required') !== 'false';
    if (required && (!d || !m || !y)) { alert(_L.selectDate); return false; }
    if (d && m && y) {
        var docDate = new Date(y, m - 1, d);
        var today   = new Date(); today.setHours(0, 0, 0, 0);
        if (el.getAttribute('data-future') === 'true' && docDate < today) {
            alert(_L.dateInFuture); return false;
        }
        var maxDays = el.getAttribute('data-max-days');
        if (maxDays) {
            var minDate = new Date(); minDate.setDate(minDate.getDate() - parseInt(maxDays)); minDate.setHours(0,0,0,0);
            if (docDate < minDate || docDate > today) {
                alert(_L.dateRange.replace('%d', maxDays)); return false;
            }
        }
    }
    const btn = document.getElementById('submit_' + type);
    setTimeout(() => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + _L.uploading;
    }, 30);
    return true;
}

// Drag-and-drop on each zone
document.querySelectorAll('.upload-zone').forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
});

function markNotRequired(type, label) {
    document.getElementById('nrDocType').value = type;
    document.getElementById('nrModalDesc').textContent = _L.markingDesc.replace('%s', label);
    document.getElementById('nrReason').value = '';
    document.getElementById('notRequiredModal').style.display = 'flex';
}
function closeNrModal() {
    document.getElementById('notRequiredModal').style.display = 'none';
}
function submitNrForm() {
    const reason = document.getElementById('nrReason').value.trim();
    if (!reason) { alert(_L.enterReason); return; }
    document.getElementById('nrReasonHidden').value = reason;
    document.getElementById('nrForm').submit();
}
</script>

<?php if (empty($isDocTab)) include __DIR__ . '/layout-footer.php'; ?>
