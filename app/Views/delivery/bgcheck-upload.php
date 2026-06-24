<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?php echo $fr ? 'fr-CA' : 'en'; ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $fr ? 'Vérification des antécédents - OCSAPP' : 'Background Check Upload - OCSAPP'; ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Poppins',sans-serif; background:#f0f4f8; min-height:100vh; display:flex; flex-direction:column; }
    .header { background:linear-gradient(135deg,#1e40af,#1e3a8a); color:white; padding:18px 28px; display:flex; align-items:center; gap:14px; }
    .header-logo { font-size:20px; font-weight:700; display:flex; align-items:center; gap:10px; }
    .header-logo i { font-size:24px; }
    .header-sub { font-size:12px; opacity:.7; font-weight:400; }
    .container { max-width:640px; margin:40px auto; padding:0 20px; flex:1; }
    .card { background:white; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden; }
    .card-header { background:linear-gradient(135deg,#1e40af,#2563eb); color:white; padding:28px 32px; }
    .card-header h1 { font-size:20px; font-weight:700; margin-bottom:6px; display:flex; align-items:center; gap:10px; }
    .card-header p { font-size:13px; opacity:.85; line-height:1.6; }
    .card-body { padding:32px; }
    .alert { padding:14px 18px; border-radius:10px; margin-bottom:24px; display:flex; align-items:flex-start; gap:12px; font-size:14px; line-height:1.6; }
    .alert-success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
    .alert-error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
    .alert-info { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }
    .alert i { margin-top:2px; flex-shrink:0; }
    .steps { margin-bottom:28px; }
    .steps-title { font-size:13px; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; }
    .step { display:flex; gap:14px; padding:12px 0; border-bottom:1px solid #f3f4f6; }
    .step:last-child { border-bottom:none; }
    .step-num { width:26px; height:26px; border-radius:50%; background:#1e40af; color:white; font-size:12px; font-weight:700; display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:1px; }
    .step-content { flex:1; }
    .step-content strong { font-size:14px; color:#111827; display:block; margin-bottom:3px; }
    .step-content span { font-size:13px; color:#6b7280; line-height:1.5; }
    .step-content a { color:#2563eb; }
    .form-section { margin-bottom:24px; }
    .form-label { font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; display:block; }
    .form-label span { color:#dc2626; }
    .form-select, .form-input { width:100%; padding:10px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; font-family:inherit; color:#111827; background:white; transition:.15s; }
    .form-select:focus, .form-input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,.1); }
    .upload-zone { border:2px dashed #d1d5db; border-radius:12px; padding:36px 20px; text-align:center; cursor:pointer; transition:.2s; position:relative; background:#f9fafb; }
    .upload-zone:hover, .upload-zone.drag-over { border-color:#2563eb; background:#eff6ff; }
    .upload-zone input[type="file"] { position:absolute; inset:0; opacity:0; cursor:pointer; }
    .upload-zone i { font-size:36px; color:#9ca3af; margin-bottom:10px; display:block; }
    .upload-zone .zone-title { font-size:15px; font-weight:600; color:#374151; margin-bottom:4px; }
    .upload-zone .zone-sub { font-size:12px; color:#9ca3af; }
    .file-preview { display:none; align-items:center; gap:12px; padding:12px 16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:8px; margin-top:10px; }
    .file-preview i { color:#16a34a; font-size:20px; }
    .file-preview .file-name { font-size:13px; font-weight:600; color:#166534; flex:1; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .file-preview .file-remove { background:none; border:none; color:#dc2626; cursor:pointer; font-size:16px; }
    .consent-box { background:#fefce8; border:1px solid #fde68a; border-radius:10px; padding:16px; margin-bottom:24px; }
    .consent-label { display:flex; align-items:flex-start; gap:10px; cursor:pointer; font-size:13px; color:#374151; line-height:1.6; }
    .consent-label input[type="checkbox"] { margin-top:3px; width:16px; height:16px; flex-shrink:0; cursor:pointer; }
    .btn-submit { width:100%; padding:14px; background:#1e40af; color:white; border:none; border-radius:10px; font-size:15px; font-weight:700; cursor:pointer; font-family:inherit; display:flex; align-items:center; justify-content:center; gap:8px; transition:.2s; }
    .btn-submit:hover { background:#1d4ed8; }
    .btn-submit:disabled { background:#93c5fd; cursor:not-allowed; }
    .footer { text-align:center; padding:20px; color:#9ca3af; font-size:12px; }
    .validity-note { background:#eff6ff; border-radius:8px; padding:12px 16px; font-size:12px; color:#1e40af; margin-bottom:20px; display:flex; align-items:center; gap:8px; }
  </style>
</head>
<body>

<div class="header">
  <div class="header-logo">
    <i class="fas fa-motorcycle"></i>
    <div>
      OCSAPP
      <div class="header-sub"><?php echo $fr ? 'Portail des livreurs' : 'Driver Portal'; ?></div>
    </div>
  </div>
</div>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h1><i class="fas fa-shield-halved"></i> <?php echo $fr ? 'Téléversement du casier judiciaire' : 'Background Check Upload'; ?></h1>
      <p><?php echo $fr ? 'Téléversez votre document de vérification des antécédents criminels pour compléter votre demande de livreur. Votre document est stocké de façon sécurisée et examiné uniquement par le personnel d\'OCSAPP.' : 'Upload your criminal background check document to complete your driver application. Your document is stored securely and only reviewed by OCSAPP staff.'; ?></p>
    </div>

    <div class="card-body">

      <?php
      $flash = $_SESSION['bgcheck_flash'] ?? null;
      unset($_SESSION['bgcheck_flash']);
      ?>

      <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>">
          <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?>"></i>
          <div><?= htmlspecialchars($flash['message']) ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-triangle"></i>
          <div>
            <strong><?php echo $fr ? 'Lien invalide' : 'Link Invalid'; ?></strong><br>
            <?= htmlspecialchars($error) ?><br>
            <span style="font-size:12px;"><?php echo $fr ? 'Contactez-nous à <strong>support@ocsapp.ca</strong> si vous avez besoin d\'un nouveau lien.' : 'Contact us at <strong>support@ocsapp.ca</strong> if you need a new link.'; ?></span>
          </div>
        </div>

      <?php elseif (!empty($alreadyUploaded)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <div>
            <strong><?php echo $fr ? 'Document déjà soumis' : 'Document already submitted'; ?></strong><br>
            <?php if ($fr): ?>Bonjour <?= htmlspecialchars($app['first_name']) ?>, nous avons bien reçu votre document de vérification des antécédents. Notre équipe l'examine et communiquera avec vous sous peu.<?php else: ?>Hi <?= htmlspecialchars($app['first_name']) ?>, we've received your background check document. Our team is reviewing it and will be in touch soon.<?php endif; ?>
          </div>
        </div>

      <?php elseif ($app): ?>

        <div class="alert alert-info">
          <i class="fas fa-user-circle"></i>
          <div><?php if ($fr): ?>Bonjour <strong><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></strong> - veuillez télécharger votre document de vérification des antécédents criminels ci-dessous pour poursuivre votre demande de livreur.<?php else: ?>Hi <strong><?= htmlspecialchars($app['first_name'] . ' ' . $app['last_name']) ?></strong> - please upload your criminal background check document below to proceed with your driver application.<?php endif; ?></div>
        </div>

        <!-- How to obtain your background check -->
        <div class="steps">
          <div class="steps-title"><i class="fas fa-list-check" style="color:#1e40af;margin-right:6px;"></i> <?php echo $fr ? 'Comment obtenir votre vérification des antécédents' : 'How to obtain your background check'; ?></div>
          <div class="step">
            <div class="step-num">1</div>
            <div class="step-content">
              <strong><?php echo $fr ? 'Option A - RCMP en ligne (recommandé)' : 'Option A - RCMP Online (Recommended)'; ?></strong>
              <span><?php echo $fr ? 'Visitez le site du partenaire certifié de la GRC et soumettez vos informations en ligne. Les résultats arrivent généralement dans 1 à 3 jours ouvrables par courriel. Coût : ~25 $ à 70 $ CAD.' : 'Visit the RCMP\'s certified partner site and submit your information online. Results typically arrive within 1-3 business days by email. Cost: ~$25-$70 CAD.'; ?></span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">2</div>
            <div class="step-content">
              <strong><?php echo $fr ? 'Option B - Poste de police local' : 'Option B - Local Police Station'; ?></strong>
              <span><?php echo $fr ? 'Rendez-vous au poste de police local (SPVM à Montréal, OPP, détachement de la GRC, etc.) et demandez une vérification de casier judiciaire. Apportez une pièce d\'identité officielle. Coût : ~25 $ à 50 $ CAD. Délai : 1 à 5 jours ouvrables.' : 'Visit your local police station (SPVM in Montreal, OPP, RCMP detachment, etc.) and request a criminal record check. Bring valid government-issued ID. Cost: ~$25-$50 CAD. Processing time: 1-5 business days.'; ?></span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">3</div>
            <div class="step-content">
              <strong><?php echo $fr ? 'Livreurs du Québec - Certificat SAAQ' : 'Quebec drivers - SAAQ Certificate'; ?></strong>
              <span><?php echo $fr ? 'Si vous êtes résident du Québec, vous pourriez aussi avoir besoin d\'un certificat de bonne conduite de la <strong>SAAQ</strong>. Vérifiez avec notre équipe si requis pour votre rôle.' : 'If you are a Quebec resident, you may also need a certificate of no judicial record from the <strong>SAAQ</strong>. Check with our team if required for your role.'; ?></span>
            </div>
          </div>
          <div class="step">
            <div class="step-num">4</div>
            <div class="step-content">
              <strong><?php echo $fr ? 'Téléversez ci-dessous' : 'Upload below'; ?></strong>
              <span><?php echo $fr ? 'Une fois votre document obtenu (scan PDF ou photo claire), téléversez-le via le formulaire ci-dessous.' : 'Once you have your document (PDF scan or clear photo), upload it using the form below.'; ?></span>
            </div>
          </div>
        </div>

        <div class="validity-note">
          <i class="fas fa-calendar-check"></i>
          <span><?php echo $fr ? 'Votre vérification des antécédents doit avoir été émise <strong>au cours des 12 derniers mois</strong>.' : 'Your background check must have been issued <strong>within the last 12 months</strong>.'; ?></span>
        </div>

        <!-- Upload Form -->
        <form method="POST" action="<?= url('delivery/bgcheck/upload') ?>" enctype="multipart/form-data" id="uploadForm" onsubmit="return validateForm()">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <div class="form-section">
            <label class="form-label"><?php echo $fr ? 'Type de document' : 'Document Type'; ?> <span>*</span></label>
            <select name="doc_type" class="form-select" required>
              <option value=""><?php echo $fr ? '- Sélectionner le type -' : '- Select type -'; ?></option>
              <option value="RCMP Criminal Record Check"><?php echo $fr ? 'Vérification de casier judiciaire de la GRC' : 'RCMP Criminal Record Check'; ?></option>
              <option value="Police Information Check"><?php echo $fr ? 'Vérification des informations policières (police locale)' : 'Police Information Check (Local Police)'; ?></option>
              <option value="Criminal Record & Judicial Matters Check"><?php echo $fr ? 'Vérification du casier judiciaire et des affaires judiciaires' : 'Criminal Record & Judicial Matters Check'; ?></option>
              <option value="Vulnerable Sector Check"><?php echo $fr ? 'Vérification du secteur vulnérable' : 'Vulnerable Sector Check'; ?></option>
              <option value="SAAQ Certificate of No Judicial Record"><?php echo $fr ? 'Certificat SAAQ - aucun antécédent judiciaire (Québec)' : 'SAAQ Certificate of No Judicial Record (Quebec)'; ?></option>
              <option value="Other"><?php echo $fr ? 'Autre' : 'Other'; ?></option>
            </select>
          </div>

          <div class="form-section">
            <label class="form-label"><?php echo $fr ? 'Date sur le document' : 'Date on Document'; ?> <span>*</span></label>
            <?php
            $upMonths = $fr
                ? ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre']
                : ['January','February','March','April','May','June','July','August','September','October','November','December'];
            $upYear = (int)date('Y');
            ?>
            <div style="display:flex; gap:8px;">
              <select id="upDocDay" class="form-select" style="flex:1;" onchange="updateUpDocDate()">
                <option value=""><?php echo $fr ? 'Jour' : 'Day'; ?></option>
                <?php for ($d = 1; $d <= 31; $d++): ?><option value="<?= $d ?>"><?= $d ?></option><?php endfor; ?>
              </select>
              <select id="upDocMonth" class="form-select" style="flex:2;" onchange="updateUpDocDate()">
                <option value=""><?php echo $fr ? 'Mois' : 'Month'; ?></option>
                <?php foreach ($upMonths as $i => $mn): ?><option value="<?= $i + 1 ?>"><?= $mn ?></option><?php endforeach; ?>
              </select>
              <select id="upDocYear" class="form-select" style="flex:1;" onchange="updateUpDocDate()">
                <option value=""><?php echo $fr ? 'Année' : 'Year'; ?></option>
                <?php for ($y = $upYear; $y >= $upYear - 1; $y--): ?><option value="<?= $y ?>"><?= $y ?></option><?php endfor; ?>
              </select>
            </div>
            <input type="hidden" name="doc_date" id="upDocDateHidden">
            <div style="font-size:11px;color:#9ca3af;margin-top:4px;"><?php echo $fr ? 'Doit être dans les 12 derniers mois.' : 'Must be within the last 12 months.'; ?></div>
          </div>

          <div class="form-section">
            <label class="form-label"><?php echo $fr ? 'Télécharger le document' : 'Upload Document'; ?> <span>*</span></label>
            <div class="upload-zone" id="uploadZone">
              <input type="file" name="bgcheck_document" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" required onchange="previewFile(this)">
              <i class="fas fa-cloud-arrow-up" id="uploadIcon"></i>
              <div class="zone-title"><?php echo $fr ? 'Cliquez pour télécharger ou glisser-déposer' : 'Click to upload or drag & drop'; ?></div>
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

          <div class="consent-box">
            <label class="consent-label">
              <input type="checkbox" name="consent" id="consentCheck" required>
              <span><?php echo $fr ? 'Je confirme qu\'il s\'agit de ma vérification des antécédents criminels, que les informations sont exactes et non modifiées, et je consens à ce qu\'OCSAPP examine ce document à des fins d\'emploi.' : 'I confirm that this is my criminal background check, the information is accurate and unaltered, and I consent to OCSAPP reviewing this document for employment purposes.'; ?></span>
            </label>
          </div>

          <button type="submit" class="btn-submit" id="submitBtn">
            <i class="fas fa-upload"></i> <?php echo $fr ? 'Soumettre la vérification des antécédents' : 'Submit Background Check'; ?>
          </button>
        </form>

      <?php endif; ?>

    </div>
  </div>
</div>

<div class="footer">
  &copy; <?= date('Y') ?> OCSAPP. <?php echo $fr ? 'Votre document est chiffré et stocké de façon sécurisée. Questions ?' : 'Your document is encrypted and stored securely. Questions?'; ?> <a href="mailto:support@ocsapp.ca" style="color:#2563eb;">support@ocsapp.ca</a>
</div>

<script>
function previewFile(input) {
  if (!input.files || !input.files[0]) return;
  const file = input.files[0];
  document.getElementById('uploadIcon').style.display = 'none';
  document.getElementById('uploadZone').querySelector('.zone-title').style.display = 'none';
  document.getElementById('uploadZone').querySelector('.zone-sub').style.display = 'none';
  const preview = document.getElementById('filePreview');
  document.getElementById('fileName').textContent = file.name;
  preview.style.display = 'flex';
}

function clearFile() {
  document.getElementById('fileInput').value = '';
  document.getElementById('filePreview').style.display = 'none';
  document.getElementById('uploadIcon').style.display = '';
  document.getElementById('uploadZone').querySelector('.zone-title').style.display = '';
  document.getElementById('uploadZone').querySelector('.zone-sub').style.display = '';
}

// Drag & drop highlighting
const zone = document.getElementById('uploadZone');
if (zone) {
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => { zone.classList.remove('drag-over'); });
}

function updateUpDocDate() {
  var d = document.getElementById('upDocDay').value;
  var m = document.getElementById('upDocMonth').value;
  var y = document.getElementById('upDocYear').value;
  document.getElementById('upDocDateHidden').value = (d && m && y)
    ? y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0')
    : '';
}
var _L = {
  consent: <?php echo json_encode($fr ? 'Veuillez confirmer votre consentement avant de soumettre.' : 'Please confirm your consent before submitting.'); ?>,
  uploading: <?php echo json_encode($fr ? 'Téléversement...' : 'Uploading...'); ?>,
  selectDate: <?php echo json_encode($fr ? 'Veuillez sélectionner la date du document.' : 'Please select the document date.'); ?>,
  dateRange: <?php echo json_encode($fr ? 'La date du document doit être dans les 12 derniers mois.' : 'Document date must be within the last 12 months.'); ?>
};
function validateForm() {
  var d = document.getElementById('upDocDay').value;
  var m = document.getElementById('upDocMonth').value;
  var y = document.getElementById('upDocYear').value;
  if (!d || !m || !y) { alert(_L.selectDate); return false; }
  var docDate = new Date(y, m - 1, d);
  var oneYearAgo = new Date(); oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
  if (docDate > new Date() || docDate < oneYearAgo) { alert(_L.dateRange); return false; }
  if (!document.getElementById('consentCheck').checked) {
    alert(_L.consent);
    return false;
  }
  const btn = document.getElementById('submitBtn');
  setTimeout(() => {
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + _L.uploading;
  }, 30);
  return true;
}
</script>

</body>
</html>
