<?php
$currentPage = 'bgcheck';
if (empty($isDocTab)) include __DIR__ . '/layout-header.php';

$bgStatus = $application['bgcheck_status'] ?? 'not_requested';

$docTypeLabels = [
    'RCMP Criminal Record Check'                => $fr ? 'Vérification du casier judiciaire GRC'                          : 'RCMP Criminal Record Check',
    'Police Information Check'                  => $fr ? 'Vérification des informations policières (police locale)'        : 'Police Information Check (Local Police)',
    'Criminal Record & Judicial Matters Check'  => $fr ? 'Vérification du casier judiciaire et des dossiers judiciaires'  : 'Criminal Record & Judicial Matters Check',
    'Vulnerable Sector Check'                   => $fr ? 'Vérification du secteur vulnérable'                             : 'Vulnerable Sector Check',
    'SAAQ Certificate of No Judicial Record'    => $fr ? 'Certificat d\'absence de dossier judiciaire SAAQ (Québec)'      : 'SAAQ Certificate of No Judicial Record (Quebec)',
    'Other'                                     => $fr ? 'Autre'                                                           : 'Other',
    'Not specified'                             => $fr ? 'Non spécifié'                                                    : 'Not specified',
];
$fmtDocType = function(string $raw) use ($docTypeLabels): string {
    return $docTypeLabels[$raw] ?? $raw;
};

$bgColors = [
    'not_requested' => ['bg' => '#f3f4f6', 'color' => '#6b7280', 'icon' => 'circle-minus',      'label' => $fr ? 'Non soumis'             : 'Not Submitted'],
    'uploaded'      => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'clock',              'label' => $fr ? 'En cours d\'examen'     : 'Under Review'],
    'verified'      => ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'shield-check',       'label' => $fr ? 'Vérifié - Sans problème': 'Verified - Clear'],
    'waived'        => ['bg' => '#dcfce7', 'color' => '#16a34a', 'icon' => 'shield-halved',      'label' => $fr ? 'Dispensé par l\'admin'  : 'Waived by Admin'],
    'flagged'       => ['bg' => '#fee2e2', 'color' => '#dc2626', 'icon' => 'shield-exclamation', 'label' => $fr ? 'Problèmes détectés'     : 'Issues Found'],
];
$bgc = $bgColors[$bgStatus] ?? $bgColors['not_requested'];
?>

<style>
  .bgcheck-status-banner {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 24px;
    border-radius: 12px;
    margin-bottom: 28px;
    background: <?= $bgc['bg'] ?>;
    border: 1px solid <?= $bgc['color'] ?>33;
  }
  .bgcheck-status-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: <?= $bgc['color'] ?>22;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  .bgcheck-status-icon i { font-size: 20px; color: <?= $bgc['color'] ?>; }
  .bgcheck-status-title { font-size: 16px; font-weight: 700; color: <?= $bgc['color'] ?>; }
  .bgcheck-status-sub   { font-size: 13px; color: #6b7280; margin-top: 2px; }

  .info-card {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 24px;
  }
  .info-card h4 { font-size: 14px; font-weight: 700; color: #1e40af; margin-bottom: 8px; }
  .info-card p, .info-card li { font-size: 13px; color: #1e3a8a; line-height: 1.7; }
  .info-card ul { padding-left: 18px; }

  .steps-list { margin: 0; padding: 0; list-style: none; }
  .step-item { display: flex; gap: 14px; padding: 14px 0; border-bottom: 1px solid #f3f4f6; }
  .step-item:last-child { border-bottom: none; }
  .step-num { width: 28px; height: 28px; border-radius: 50%; background: #1e40af; color: white; font-size: 12px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
  .step-content strong { font-size: 14px; color: #111827; display: block; margin-bottom: 3px; }
  .step-content span { font-size: 13px; color: #6b7280; line-height: 1.5; }

  .upload-zone { border: 2px dashed #d1d5db; border-radius: 12px; padding: 36px 20px; text-align: center; cursor: pointer; transition: .2s; position: relative; background: #f9fafb; }
  .upload-zone:hover, .upload-zone.drag-over { border-color: #2563eb; background: #eff6ff; }
  .upload-zone input[type="file"] { position: absolute; inset: 0; opacity: 0; cursor: pointer; }
  .upload-zone i { font-size: 36px; color: #9ca3af; margin-bottom: 10px; display: block; }
  .zone-title { font-size: 15px; font-weight: 600; color: #374151; margin-bottom: 4px; }
  .zone-sub { font-size: 12px; color: #9ca3af; }

  .file-preview { display: none; align-items: center; gap: 12px; padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; margin-top: 10px; }
  .file-preview i { color: #16a34a; font-size: 20px; }
  .file-name { font-size: 13px; font-weight: 600; color: #166534; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .file-remove { background: none; border: none; color: #dc2626; cursor: pointer; font-size: 16px; }

  .form-label-sm { font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
  .form-select-sm, .form-input-sm { width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: inherit; color: #111827; background: white; }
  .form-select-sm:focus, .form-input-sm:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }

  .btn-upload { width: 100%; padding: 14px; background: #1e40af; color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: inherit; display: flex; align-items: center; justify-content: center; gap: 8px; transition: .2s; margin-top: 20px; }
  .btn-upload:hover { background: #1d4ed8; }
  .btn-upload:disabled { background: #93c5fd; cursor: not-allowed; }

  .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; font-size: 13px; }
  .detail-label { font-size: 11px; color: #6b7280; text-transform: uppercase; font-weight: 600; margin-bottom: 3px; }
  .detail-value { font-weight: 500; color: #111827; }
  @media (max-width: 480px) { .detail-grid { grid-template-columns: 1fr; } }
</style>

<!-- Status Banner -->
<div class="bgcheck-status-banner">
  <div class="bgcheck-status-icon">
    <i class="fas fa-<?= $bgc['icon'] ?>"></i>
  </div>
  <div>
    <div class="bgcheck-status-title"><?= $bgc['label'] ?></div>
    <div class="bgcheck-status-sub">
      <?php
      $fmtBgDate = function($ts) use ($fr) {
          if (!$ts) return $fr ? 'N/D' : 'N/A';
          $t = strtotime($ts);
          if ($fr) {
              $m = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
              return (int)date('j', $t) . ' ' . $m[(int)date('n', $t)] . ' ' . date('Y', $t);
          }
          return date('M j, Y', $t);
      };
      if ($bgStatus === 'not_requested'): ?>
        <?php echo $fr ? 'Pas encore soumis - téléversez votre document ci-dessous pour continuer.' : 'Not submitted yet - upload your document below to proceed.'; ?>
      <?php elseif ($bgStatus === 'uploaded'): ?>
        <?php echo $fr
            ? 'Soumis le ' . $fmtBgDate($application['bgcheck_uploaded_at']) . '. Notre équipe l\'examine.'
            : 'Submitted on ' . $fmtBgDate($application['bgcheck_uploaded_at']) . '. Our team is reviewing it.'; ?>
      <?php elseif ($bgStatus === 'verified'): ?>
        <?php echo $fr
            ? 'Vérifié le ' . $fmtBgDate($application['bgcheck_verified_at']) . '. Vous êtes autorisé à aller en ligne une fois la formation terminée.'
            : 'Verified on ' . $fmtBgDate($application['bgcheck_verified_at']) . '. You\'re cleared to go online once training is complete.'; ?>
      <?php elseif ($bgStatus === 'waived'): ?>
        <?php echo $fr ? 'L\'exigence de vérification des antécédents a été dispensée par un administrateur OCSAPP.' : 'Background check requirement has been waived by an OCSAPP admin.'; ?>
      <?php elseif ($bgStatus === 'flagged'): ?>
        <?php echo $fr ? 'Notre équipe a trouvé un problème avec votre soumission. Veuillez télécharger un nouveau document ou contacter le support.' : 'Our team found an issue with your submission. Please upload a new document or contact support.'; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if ($bgStatus === 'verified' || $bgStatus === 'waived'): ?>
<!-- Verified / Waived state — show details only -->
<div class="card" style="margin-bottom: 24px;">
  <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:16px; display:flex; align-items:center; gap:8px;">
    <i class="fas fa-file-shield" style="color:#16a34a;"></i> <?php echo $fr ? 'Document en dossier' : 'Document on File'; ?>
  </h3>
  <div class="detail-grid">
    <div>
      <div class="detail-label"><?php echo $fr ? 'Type de document' : 'Document Type'; ?></div>
      <div class="detail-value"><?= htmlspecialchars($fmtDocType($application['bgcheck_doc_type'] ?? '')) ?: '-' ?></div>
    </div>
    <div>
      <div class="detail-label"><?php echo $fr ? 'Date du document' : 'Document Date'; ?></div>
      <div class="detail-value"><?= $fmtBgDate($application['bgcheck_doc_date'] ?? null) ?></div>
    </div>
    <div>
      <div class="detail-label"><?php echo $fr ? 'Soumis le' : 'Submitted'; ?></div>
      <div class="detail-value"><?= $fmtBgDate($application['bgcheck_uploaded_at'] ?? null) ?></div>
    </div>
    <div>
      <div class="detail-label"><?php echo $bgStatus === 'verified' ? ($fr ? 'Vérifié le' : 'Verified On') : ($fr ? 'Dispensé le' : 'Waived On'); ?></div>
      <div class="detail-value"><?= $fmtBgDate($application['bgcheck_verified_at'] ?? null) ?></div>
    </div>
  </div>
  <?php if (!empty($application['bgcheck_notes'])): ?>
    <div style="background:#f8fafc; border-radius:8px; padding:12px 14px; font-size:13px; color:#374151; border-left:3px solid #16a34a; margin-top:8px;">
      <strong><?php echo $fr ? 'Notes de l\'admin :' : 'Admin Notes:'; ?></strong> <?= nl2br(htmlspecialchars($application['bgcheck_notes'])) ?>
    </div>
  <?php endif; ?>
</div>

<?php elseif ($bgStatus === 'uploaded'): ?>
<!-- Under review — show submitted details, allow re-upload -->
<div class="card" style="margin-bottom: 24px;">
  <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:16px;">
    <i class="fas fa-clock" style="color:#b45309;"></i> <?php echo $fr ? 'Document soumis' : 'Submitted Document'; ?>
  </h3>
  <div class="detail-grid">
    <div>
      <div class="detail-label"><?php echo $fr ? 'Type de document' : 'Document Type'; ?></div>
      <div class="detail-value"><?= htmlspecialchars($fmtDocType($application['bgcheck_doc_type'] ?? '')) ?: '-' ?></div>
    </div>
    <div>
      <div class="detail-label"><?php echo $fr ? 'Date du document' : 'Document Date'; ?></div>
      <div class="detail-value"><?= $fmtBgDate($application['bgcheck_doc_date'] ?? null) ?></div>
    </div>
    <div>
      <div class="detail-label"><?php echo $fr ? 'Soumis le' : 'Submitted'; ?></div>
      <div class="detail-value"><?php
          if (!empty($application['bgcheck_uploaded_at'])) {
              $tBg = strtotime($application['bgcheck_uploaded_at']);
              if ($fr) {
                  $mBg = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                  echo (int)date('j', $tBg) . ' ' . $mBg[(int)date('n', $tBg)] . ' ' . date('Y', $tBg) . ' à ' . date('G', $tBg) . 'h' . date('i', $tBg);
              } else {
                  echo date('M j, Y g:i A', $tBg);
              }
          } else { echo '-'; }
      ?></div>
    </div>
  </div>
  <div style="background:#fef3c7; border-radius:8px; padding:12px 14px; font-size:13px; color:#92400e; border-left:3px solid #f59e0b;">
    <i class="fas fa-info-circle"></i> <?php echo $fr ? 'Votre document est en cours d\'examen. Si vous devez le remplacer, vous pouvez en télécharger un nouveau ci-dessous et il remplacera la soumission existante.' : 'Your document is under review. If you need to replace it, you can upload a new one below and it will replace the existing submission.'; ?>
  </div>
</div>

<?php elseif ($bgStatus === 'flagged'): ?>
<div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:16px 20px; margin-bottom:24px; font-size:13px; color:#991b1b;">
  <strong><i class="fas fa-triangle-exclamation"></i> <?php echo $fr ? 'Action requise' : 'Action Required'; ?></strong>
  <?php if (!empty($application['bgcheck_notes'])): ?>
    <div style="margin-top:8px;"><?= nl2br(htmlspecialchars($application['bgcheck_notes'])) ?></div>
  <?php endif; ?>
  <div style="margin-top:8px;"><?php echo $fr ? 'Veuillez télécharger un nouveau document ci-dessous, ou contacter <strong>support@ocsapp.ca</strong> si vous avez des questions.' : 'Please upload a new document below, or contact <strong>support@ocsapp.ca</strong> if you have questions.'; ?></div>
</div>
<?php endif; ?>

<?php if ($bgStatus !== 'verified' && $bgStatus !== 'waived'): ?>
<!-- How to obtain your background check -->
<div class="card" style="margin-bottom: 24px;">
  <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:4px;">
    <i class="fas fa-list-check" style="color:#1e40af;"></i> <?php echo $fr ? 'Comment obtenir votre vérification des antécédents' : 'How to Get Your Background Check'; ?>
  </h3>
  <p style="font-size:13px; color:#6b7280; margin-bottom:16px;"><?php echo $fr ? 'Vous obtenez vous-même votre vérification des antécédents - OCSAPP ne l\'organise pas pour vous. Voici vos options :' : 'You self-obtain your background check - OCSAPP does not arrange it on your behalf. Here are your options:'; ?></p>
  <ul class="steps-list">
    <li class="step-item">
      <div class="step-num">1</div>
      <div class="step-content">
        <strong><?php echo $fr ? 'GRC en ligne (recommandé)' : 'RCMP Online (Recommended)'; ?></strong>
        <span><?php echo $fr ? 'Visitez le partenaire en ligne certifié de la GRC et soumettez vos informations. Les résultats arrivent généralement dans 1 à 3 jours ouvrables par courriel. Coût : ~25 $ à 70 $ CAD.' : 'Visit the RCMP\'s certified online partner and submit your information. Results typically arrive within 1-3 business days by email. Cost: ~$25-$70 CAD.'; ?></span>
      </div>
    </li>
    <li class="step-item">
      <div class="step-num">2</div>
      <div class="step-content">
        <strong><?php echo $fr ? 'Poste de police local' : 'Local Police Station'; ?></strong>
        <span><?php echo $fr ? 'Visitez votre poste de police le plus proche (SPVM à Montréal, GRC, etc.) et demandez une vérification du casier judiciaire. Apportez une pièce d\'identité émise par le gouvernement. Coût : ~25 $ à 50 $ CAD. Traitement : 1 à 5 jours ouvrables.' : 'Visit your nearest police station (SPVM in Montreal, OPP, RCMP detachment, etc.) and request a criminal record check. Bring government-issued ID. Cost: ~$25-$50 CAD. Processing: 1-5 business days.'; ?></span>
      </div>
    </li>
    <li class="step-item">
      <div class="step-num">3</div>
      <div class="step-content">
        <strong><?php echo $fr ? 'Livreurs au Québec - Certificat SAAQ' : 'Quebec Drivers - SAAQ Certificate'; ?></strong>
        <span><?php echo $fr ? 'Les résidents du Québec peuvent aussi avoir besoin d\'un certificat d\'absence de dossier judiciaire de la <strong>SAAQ</strong>. Contactez-nous si vous n\'êtes pas certain de ce qui s\'applique à votre rôle.' : 'Quebec residents may also need a certificate of no judicial record from the <strong>SAAQ</strong>. Contact us if you\'re unsure which applies to your role.'; ?></span>
      </div>
    </li>
    <li class="step-item">
      <div class="step-num">4</div>
      <div class="step-content">
        <strong><?php echo $fr ? 'Téléchargez ci-dessous' : 'Upload below'; ?></strong>
        <span><?php echo $fr ? 'Une fois votre document obtenu (scan PDF ou photo nette), téléchargez-le via le formulaire ci-dessous. Il doit avoir été émis <strong>dans les 12 derniers mois</strong>.' : 'Once you have your document (PDF scan or clear photo), upload it using the form below. It must have been issued <strong>within the last 12 months</strong>.'; ?></span>
      </div>
    </li>
  </ul>
</div>

<!-- Upload Form -->
<div class="card">
  <h3 style="font-size:16px; font-weight:700; color:#111827; margin-bottom:20px;">
    <i class="fas fa-cloud-arrow-up" style="color:#1e40af;"></i>
    <?php echo ($bgStatus === 'uploaded' || $bgStatus === 'flagged')
        ? ($fr ? 'Remplacer le document' : 'Replace Document')
        : ($fr ? 'Télécharger votre document' : 'Upload Your Document'); ?>
  </h3>

  <form method="POST" action="<?= url('delivery/bgcheck/upload') ?>" enctype="multipart/form-data" id="bgcheckForm" onsubmit="return validateBgForm()">
    <?= csrfField() ?>

    <div style="margin-bottom: 16px;">
      <label class="form-label-sm"><?php echo $fr ? 'Type de document' : 'Document Type'; ?> <span style="color:#dc2626;">*</span></label>
      <select name="doc_type" class="form-select-sm" required>
        <option value=""><?php echo $fr ? '- Sélectionner le type -' : '- Select type -'; ?></option>
        <option value="RCMP Criminal Record Check"><?php echo $fr ? 'Vérification du casier judiciaire GRC' : 'RCMP Criminal Record Check'; ?></option>
        <option value="Police Information Check"><?php echo $fr ? 'Vérification des informations policières (police locale)' : 'Police Information Check (Local Police)'; ?></option>
        <option value="Criminal Record &amp; Judicial Matters Check"><?php echo $fr ? 'Vérification du casier judiciaire et des dossiers judiciaires' : 'Criminal Record &amp; Judicial Matters Check'; ?></option>
        <option value="Vulnerable Sector Check"><?php echo $fr ? 'Vérification du secteur vulnérable' : 'Vulnerable Sector Check'; ?></option>
        <option value="SAAQ Certificate of No Judicial Record"><?php echo $fr ? 'Certificat d\'absence de dossier judiciaire SAAQ (Québec)' : 'SAAQ Certificate of No Judicial Record (Quebec)'; ?></option>
        <option value="Other"><?php echo $fr ? 'Autre' : 'Other'; ?></option>
      </select>
    </div>

    <div style="margin-bottom: 16px;">
      <label class="form-label-sm"><?php echo $fr ? 'Date sur le document' : 'Date on Document'; ?> <span style="color:#dc2626;">*</span></label>
      <?php
      $bgMonths = $fr
          ? ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre']
          : ['January','February','March','April','May','June','July','August','September','October','November','December'];
      $bgYear = (int)date('Y');
      ?>
      <div style="display:flex; gap:8px;">
        <select id="bgDocDay" class="form-select-sm" style="flex:1;" onchange="updateBgDocDate()">
          <option value=""><?php echo $fr ? 'Jour' : 'Day'; ?></option>
          <?php for ($d = 1; $d <= 31; $d++): ?><option value="<?= $d ?>"><?= $d ?></option><?php endfor; ?>
        </select>
        <select id="bgDocMonth" class="form-select-sm" style="flex:2;" onchange="updateBgDocDate()">
          <option value=""><?php echo $fr ? 'Mois' : 'Month'; ?></option>
          <?php foreach ($bgMonths as $i => $mn): ?><option value="<?= $i + 1 ?>"><?= $mn ?></option><?php endforeach; ?>
        </select>
        <select id="bgDocYear" class="form-select-sm" style="flex:1;" onchange="updateBgDocDate()">
          <option value=""><?php echo $fr ? 'Année' : 'Year'; ?></option>
          <?php for ($y = $bgYear; $y >= $bgYear - 1; $y--): ?><option value="<?= $y ?>"><?= $y ?></option><?php endfor; ?>
        </select>
      </div>
      <input type="hidden" name="doc_date" id="bgDocDateHidden">
      <div style="font-size:11px; color:#9ca3af; margin-top:4px;"><?php echo $fr ? 'Doit avoir été émis dans les 12 derniers mois.' : 'Must be within the last 12 months.'; ?></div>
    </div>

    <div style="margin-bottom: 16px;">
      <label class="form-label-sm"><?php echo $fr ? 'Télécharger le document' : 'Upload Document'; ?> <span style="color:#dc2626;">*</span></label>
      <div class="upload-zone" id="uploadZone">
        <input type="file" name="bgcheck_document" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" required onchange="previewFile(this)">
        <i class="fas fa-cloud-arrow-up" id="uploadIcon"></i>
        <div class="zone-title"><?php echo $fr ? 'Cliquez pour télécharger ou glisser-déposer' : 'Click to upload or drag &amp; drop'; ?></div>
        <div class="zone-sub"><?php echo $fr ? 'PDF, JPG ou PNG - max 10 Mo' : 'PDF, JPG, or PNG - max 10 MB'; ?></div>
      </div>
      <div class="file-preview" id="filePreview">
        <i class="fas fa-file-check"></i>
        <span class="file-name" id="fileName"></span>
        <button type="button" class="file-remove" onclick="clearFile()" title="Remove">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <div style="background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:14px 16px; margin-bottom:4px;">
      <label style="display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13px; color:#374151; line-height:1.6;">
        <input type="checkbox" id="consentCheck" name="consent" required style="margin-top:3px; width:16px; height:16px; flex-shrink:0;">
        <span><?php echo $fr ? 'Je confirme qu\'il s\'agit de ma vérification des antécédents criminels, que les informations sont exactes et non modifiées, et je consens à ce qu\'OCSAPP examine ce document à des fins d\'emploi.' : 'I confirm this is my criminal background check, the information is accurate and unaltered, and I consent to OCSAPP reviewing this document for employment purposes.'; ?></span>
      </label>
    </div>

    <button type="submit" class="btn-upload" id="submitBtn">
      <i class="fas fa-upload"></i> <?php echo $fr ? 'Soumettre la vérification des antécédents' : 'Submit Background Check'; ?>
    </button>
  </form>
</div>
<?php endif; ?>

<script>
function updateBgDocDate() {
  var d = document.getElementById('bgDocDay').value;
  var m = document.getElementById('bgDocMonth').value;
  var y = document.getElementById('bgDocYear').value;
  document.getElementById('bgDocDateHidden').value = (d && m && y)
    ? y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0')
    : '';
}
function previewFile(input) {
  if (!input.files || !input.files[0]) return;
  document.getElementById('uploadIcon').style.display = 'none';
  document.getElementById('uploadZone').querySelector('.zone-title').style.display = 'none';
  document.getElementById('uploadZone').querySelector('.zone-sub').style.display = 'none';
  document.getElementById('fileName').textContent = input.files[0].name;
  document.getElementById('filePreview').style.display = 'flex';
}
function clearFile() {
  document.getElementById('fileInput').value = '';
  document.getElementById('filePreview').style.display = 'none';
  document.getElementById('uploadIcon').style.display = '';
  document.getElementById('uploadZone').querySelector('.zone-title').style.display = '';
  document.getElementById('uploadZone').querySelector('.zone-sub').style.display = '';
}
const zone = document.getElementById('uploadZone');
if (zone) {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
}
function validateBgForm() {
  var d = document.getElementById('bgDocDay').value;
  var m = document.getElementById('bgDocMonth').value;
  var y = document.getElementById('bgDocYear').value;
  if (!d || !m || !y) {
    alert(<?php echo json_encode($fr ? 'Veuillez sélectionner la date du document.' : 'Please select the document date.'); ?>);
    return false;
  }
  var docDate = new Date(y, m - 1, d);
  var oneYearAgo = new Date(); oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
  if (docDate > new Date() || docDate < oneYearAgo) {
    alert(<?php echo json_encode($fr ? 'La date du document doit être dans les 12 derniers mois.' : 'Document date must be within the last 12 months.'); ?>);
    return false;
  }
  if (!document.getElementById('consentCheck').checked) {
    alert(<?php echo json_encode($fr ? 'Veuillez confirmer votre consentement avant de soumettre.' : 'Please confirm your consent before submitting.'); ?>);
    return false;
  }
  const btn = document.getElementById('submitBtn');
  setTimeout(() => {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?php echo $fr ? 'Téléversement...' : 'Uploading...'; ?>';
  }, 30);
  return true;
}
</script>

<?php if (empty($isDocTab)) include __DIR__ . '/layout-footer.php'; ?>
