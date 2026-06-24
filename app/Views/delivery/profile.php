<?php $currentPage = 'profile'; include __DIR__ . '/layout-header.php'; ?>

<style>
  .profile-container {
    width: 100%;
    padding: 20px;
  }

  .page-header {
    margin-bottom: 28px;
  }

  .page-header h1 {
    font-size: 26px;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
  }

  .profile-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    overflow: hidden;
    margin-bottom: 24px;
  }

  .profile-card-header {
    padding: 24px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 15px;
    font-weight: 700;
    color: #1f2937;
  }

  .profile-card-body {
    padding: 28px 24px;
  }

  /* Avatar section */
  .avatar-section {
    display: flex;
    align-items: center;
    gap: 24px;
    flex-wrap: wrap;
  }

  .avatar-wrap {
    position: relative;
    flex-shrink: 0;
  }

  .avatar-img {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #e5e7eb;
  }

  .avatar-initials {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
    font-weight: 700;
    border: 3px solid #e5e7eb;
  }

  .avatar-info {
    flex: 1;
  }

  .avatar-info h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 4px;
  }

  .avatar-info p {
    font-size: 13px;
    color: #6b7280;
    margin: 0 0 14px;
  }

  .upload-btn-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .btn-upload {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    background: #00b207;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
  }

  .btn-upload:hover { background: #009206; }

  .upload-hint {
    font-size: 12px;
    color: #9ca3af;
  }

  #photo-input { display: none; }

  /* Preview */
  #preview-wrap {
    display: none;
    align-items: center;
    gap: 12px;
    margin-top: 14px;
    padding: 12px 16px;
    background: #f0fdf4;
    border-radius: 10px;
    border: 1px solid #bbf7d0;
  }

  #preview-img {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    object-fit: cover;
  }

  #preview-name {
    font-size: 13px;
    color: #16a34a;
    font-weight: 500;
    flex: 1;
  }

  .btn-save {
    padding: 9px 20px;
    background: #16a34a;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
  }

  .btn-save:hover { background: #15803d; }

  .btn-cancel-preview {
    padding: 9px 14px;
    background: none;
    color: #6b7280;
    border: 1px solid #d1d5db;
    border-radius: 9px;
    font-size: 13px;
    cursor: pointer;
    font-family: inherit;
  }

  /* Info rows */
  .info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #f3f4f6;
    gap: 12px;
  }

  .info-row:last-child { border-bottom: none; }

  .info-label {
    font-size: 13px;
    font-weight: 600;
    color: #6b7280;
    min-width: 100px;
  }

  .info-value {
    font-size: 14px;
    color: #1f2937;
    font-weight: 500;
    text-align: right;
  }

  .badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
  }

  .badge-green  { background: #dcfce7; color: #16a34a; }
  .badge-yellow { background: #fef9c3; color: #ca8a04; }
  .badge-red    { background: #fee2e2; color: #dc2626; }
  .badge-gray   { background: #f3f4f6; color: #6b7280; }
  .badge-blue   { background: #dbeafe; color: #2563eb; }

  /* Password form */
  .form-group {
    margin-bottom: 18px;
  }

  .form-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 6px;
  }

  .form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 9px;
    font-size: 14px;
    font-family: inherit;
    color: #1f2937;
    background: #fff;
    transition: border-color 0.2s, box-shadow 0.2s;
    box-sizing: border-box;
  }

  .form-input:focus {
    outline: none;
    border-color: #00b207;
    box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
  }

  .pw-input-wrap {
    position: relative;
  }

  .pw-input-wrap .form-input {
    padding-right: 42px;
  }

  .pw-toggle {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 2px;
    font-size: 14px;
    transition: color 0.15s;
  }

  .pw-toggle:hover { color: #6b7280; }

  .form-hint {
    font-size: 12px;
    color: #9ca3af;
    margin-top: 4px;
  }

  .btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 10px 22px;
    background: #00b207;
    color: white;
    border: none;
    border-radius: 9px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    font-family: inherit;
    transition: background 0.2s;
  }

  .btn-primary:hover { background: #009206; }

  /* Password requirements */
  .pw-requirements {
    list-style: none;
    padding: 10px 0 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .req {
    font-size: 12px;
    color: #9ca3af;
    display: flex;
    align-items: center;
    gap: 7px;
    transition: color 0.2s;
  }

  .req i {
    font-size: 7px;
    transition: color 0.2s;
  }

  .req.met {
    color: #16a34a;
  }

  .req.met i::before {
    content: '\f058'; /* fa-circle-check */
    font-size: 12px;
  }

  /* Responsive */
  @media (max-width: 640px) {
    .profile-container {
      padding: 12px;
    }
    .profile-card-body {
      padding: 18px 16px;
    }
    .profile-card-header {
      padding: 16px;
    }
    .avatar-section {
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
    }
    .info-row {
      flex-direction: column;
      align-items: flex-start;
      gap: 6px;
    }
    .info-value {
      text-align: left;
    }
    .page-header h1 {
      font-size: 22px;
    }
  }
</style>

<div class="profile-container">
  <div class="page-header">
    <h1><?php echo $fr ? 'Mon profil' : 'My Profile'; ?></h1>
  </div>

  <!-- Photo card -->
  <div class="profile-card">
    <div class="profile-card-header"><?php echo $fr ? 'Photo de profil' : 'Profile Photo'; ?></div>
    <div class="profile-card-body">
      <div class="avatar-section">
        <div class="avatar-wrap">
          <?php if (!empty($driver['avatar'])): ?>
            <img class="avatar-img"
                 src="<?= htmlspecialchars('https://ocsapp.ca/' . ltrim($driver['avatar'], '/')) ?>"
                 alt="Profile photo">
          <?php else: ?>
            <div class="avatar-initials">
              <?= strtoupper(substr($driver['first_name'] ?? 'D', 0, 1)) ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="avatar-info">
          <h2><?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?></h2>
          <p><?php echo $fr ? 'Livreur' : 'Delivery Driver'; ?></p>
          <div class="upload-btn-wrap">
            <label class="btn-upload" for="photo-input">
              <i class="fas fa-camera"></i> <?php echo $fr ? 'Changer la photo' : 'Change Photo'; ?>
            </label>
            <span class="upload-hint"><?php echo $fr ? 'JPEG, PNG ou WebP · Max 5 Mo' : 'JPEG, PNG or WebP · Max 5 MB'; ?></span>
          </div>
        </div>
      </div>

      <form method="POST" action="<?= url('delivery/profile/photo') ?>" enctype="multipart/form-data" id="photo-form">
        <?= csrfField() ?>
        <input type="file" name="photo" id="photo-input" accept="image/jpeg,image/png,image/webp">
        <div id="preview-wrap">
          <img id="preview-img" src="" alt="Preview">
          <span id="preview-name"></span>
          <button type="button" class="btn-cancel-preview" onclick="cancelPreview()"><?php echo $fr ? 'Annuler' : 'Cancel'; ?></button>
          <button type="submit" class="btn-save"><i class="fas fa-check"></i> <?php echo $fr ? 'Enregistrer la photo' : 'Save Photo'; ?></button>
        </div>
      </form>
    </div>
  </div>

  <!-- Account info -->
  <div class="profile-card">
    <div class="profile-card-header"><?php echo $fr ? 'Informations du compte' : 'Account Information'; ?></div>
    <div class="profile-card-body">
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Nom complet' : 'Full Name'; ?></span>
        <span class="info-value"><?= htmlspecialchars(($driver['first_name'] ?? '') . ' ' . ($driver['last_name'] ?? '')) ?></span>
      </div>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Courriel' : 'Email'; ?></span>
        <span class="info-value"><?= htmlspecialchars($driver['email'] ?? '') ?></span>
      </div>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Téléphone' : 'Phone'; ?></span>
        <span class="info-value"><?= htmlspecialchars($driver['phone'] ?? '-') ?></span>
      </div>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Membre depuis' : 'Member Since'; ?></span>
        <span class="info-value"><?php
          if (!empty($driver['created_at'])) {
              $memberTs = strtotime($driver['created_at']);
              if ($fr) {
                  $frMonthsProf = ['','janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
                  echo (int)date('j', $memberTs) . ' ' . $frMonthsProf[(int)date('n', $memberTs)] . ' ' . date('Y', $memberTs);
              } else {
                  echo date('M j, Y', $memberTs);
              }
          } else {
              echo '-';
          }
        ?></span>
      </div>
    </div>
  </div>

  <!-- Documents card -->
  <div class="profile-card">
    <div class="profile-card-header" style="display:flex;justify-content:space-between;align-items:center;">
      <span><?php echo $fr ? 'Documents de conformité' : 'Compliance Documents'; ?></span>
      <a href="<?= url('delivery/compliance') ?>"
         style="font-size:13px;font-weight:600;color:#3b82f6;text-decoration:none;">
        <?php echo $fr ? 'Gérer' : 'Manage'; ?> <i class="fas fa-arrow-right" style="font-size:11px;"></i>
      </a>
    </div>
    <div class="profile-card-body" style="padding-top:0;padding-bottom:0;">
      <?php
      $cdDefs = [
        'class5_license'        => ['icon' => 'fa-id-card',       'label' => $fr ? 'Permis de conduire classe 5' : "Class 5 Driver's License"],
        'saaq_record'           => ['icon' => 'fa-file-lines',    'label' => $fr ? 'Dossier de conduite SAAQ' : 'SAAQ Driving Record'],
        'commercial_insurance'  => ['icon' => 'fa-shield-halved', 'label' => $fr ? 'Assurance commerciale' : 'Commercial Insurance'],
        'vehicle_registration'  => ['icon' => 'fa-car',           'label' => $fr ? 'Immatriculation du véhicule' : 'Vehicle Registration'],
        'work_authorization'    => ['icon' => 'fa-passport',      'label' => $fr ? 'Autorisation de travail' : 'Work Authorization'],
      ];
      $cdStatusMap = [
        'verified'     => ['class' => 'badge-green',  'icon' => 'fa-circle-check', 'label' => $fr ? 'Vérifié' : 'Verified'],
        'uploaded'     => ['class' => 'badge-yellow', 'icon' => 'fa-clock',        'label' => $fr ? 'En cours d\'examen' : 'Under Review'],
        'flagged'      => ['class' => 'badge-red',    'icon' => 'fa-flag',         'label' => $fr ? 'Signalé' : 'Flagged'],
        'not_required' => ['class' => 'badge-gray',   'icon' => 'fa-minus-circle', 'label' => $fr ? 'Non requis' : 'Not Required'],
        'not_uploaded' => ['class' => 'badge-gray',   'icon' => 'fa-circle-xmark', 'label' => $fr ? 'Non soumis' : 'Not Uploaded'],
      ];
      foreach ($cdDefs as $cdType => $cdDef):
        $cdStatus = $complianceDocs[$cdType]['status'] ?? 'not_uploaded';
        $cdNotes  = $complianceDocs[$cdType]['admin_notes'] ?? '';
        $cdSt     = $cdStatusMap[$cdStatus] ?? $cdStatusMap['not_uploaded'];
      ?>
      <div class="info-row" style="padding:12px 0;">
        <span style="display:flex;align-items:center;gap:10px;">
          <i class="fas <?= $cdDef['icon'] ?>" style="color:#9ca3af;width:16px;text-align:center;font-size:13px;"></i>
          <span class="info-label" style="min-width:auto;font-size:13px;"><?= $cdDef['label'] ?></span>
        </span>
        <span style="text-align:right;">
          <span class="badge <?= $cdSt['class'] ?>">
            <i class="fas <?= $cdSt['icon'] ?>"></i> <?= $cdSt['label'] ?>
          </span>
          <?php if ($cdStatus === 'flagged' && $cdNotes): ?>
            <div style="font-size:11px;color:#dc2626;margin-top:3px;"><?= htmlspecialchars($cdNotes) ?></div>
          <?php endif; ?>
        </span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Change password card -->
  <div class="profile-card">
    <div class="profile-card-header"><?php echo $fr ? 'Changer le mot de passe' : 'Change Password'; ?></div>
    <div class="profile-card-body">
      <form method="POST" action="<?= url('delivery/profile/password') ?>" id="pw-form">
        <?= csrfField() ?>
        <div class="form-group">
          <label class="form-label" for="current_password"><?php echo $fr ? 'Mot de passe actuel' : 'Current Password'; ?></label>
          <div class="pw-input-wrap">
            <input type="password" name="current_password" id="current_password" class="form-input"
                   autocomplete="current-password" required>
            <button type="button" class="pw-toggle" onclick="togglePw('current_password', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="new_password"><?php echo $fr ? 'Nouveau mot de passe' : 'New Password'; ?></label>
          <div class="pw-input-wrap">
            <input type="password" name="new_password" id="new_password" class="form-input"
                   autocomplete="new-password" required>
            <button type="button" class="pw-toggle" onclick="togglePw('new_password', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <!-- Requirements checklist -->
          <ul class="pw-requirements" id="pw-reqs">
            <li id="req-length"  class="req"><i class="fas fa-circle"></i> <?php echo $fr ? 'Au moins 10 caractères' : 'At least 10 characters'; ?></li>
            <li id="req-upper"   class="req"><i class="fas fa-circle"></i> <?php echo $fr ? 'Une lettre majuscule (A-Z)' : 'One uppercase letter (A-Z)'; ?></li>
            <li id="req-lower"   class="req"><i class="fas fa-circle"></i> <?php echo $fr ? 'Une lettre minuscule (a-z)' : 'One lowercase letter (a-z)'; ?></li>
            <li id="req-number"  class="req"><i class="fas fa-circle"></i> <?php echo $fr ? 'Un chiffre (0-9)' : 'One number (0-9)'; ?></li>
            <li id="req-special" class="req"><i class="fas fa-circle"></i> <?php echo $fr ? 'Un caractère spécial (!@#$%^&*)' : 'One special character (!@#$%^&*)'; ?></li>
          </ul>
        </div>
        <div class="form-group" style="margin-bottom:22px;">
          <label class="form-label" for="confirm_password"><?php echo $fr ? 'Confirmer le nouveau mot de passe' : 'Confirm New Password'; ?></label>
          <div class="pw-input-wrap">
            <input type="password" name="confirm_password" id="confirm_password" class="form-input"
                   autocomplete="new-password" required>
            <button type="button" class="pw-toggle" onclick="togglePw('confirm_password', this)" tabindex="-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <p class="form-hint" id="pw-match-hint" style="display:none;color:#dc2626;">
            <?php echo $fr ? 'Les mots de passe ne correspondent pas.' : 'Passwords do not match.'; ?>
          </p>
        </div>
        <button type="submit" class="btn-primary">
          <i class="fas fa-lock"></i> <?php echo $fr ? 'Mettre à jour le mot de passe' : 'Update Password'; ?>
        </button>
      </form>
    </div>
  </div>

  <!-- Status card -->
  <div class="profile-card">
    <div class="profile-card-header"><?php echo $fr ? 'Statut du livreur' : 'Driver Status'; ?></div>
    <div class="profile-card-body">
      <?php
      $db = \Database::getConnection();

      // Certification
      $certStmt = $db->prepare("SELECT cert_number, issued_at FROM driver_certificates WHERE driver_id = ? LIMIT 1");
      $certStmt->execute([userId()]);
      $cert = $certStmt->fetch(\PDO::FETCH_ASSOC);

      // Background check
      $bgStmt = $db->prepare("SELECT bgcheck_status FROM driver_applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
      $bgStmt->execute([userId()]);
      $bgStatus = $bgStmt->fetchColumn();

      // Deliveries
      $delStmt = $db->prepare("SELECT COUNT(*) FROM delivery_assignments WHERE driver_id = ? AND status = 'delivered'");
      $delStmt->execute([userId()]);
      $totalDeliveries = (int) $delStmt->fetchColumn();

      // Rating
      $ratingStmt = $db->prepare("SELECT AVG(rating) FROM driver_ratings WHERE driver_id = ?");
      $ratingStmt->execute([userId()]);
      $avgRating = $ratingStmt->fetchColumn();
      ?>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Formation' : 'Training'; ?></span>
        <span class="info-value">
          <?php if ($cert): ?>
            <span class="badge badge-green"><i class="fas fa-graduation-cap"></i> <?php echo $fr ? 'Certifié · ' : 'Certified · '; ?><?= htmlspecialchars($cert['cert_number']) ?></span>
          <?php else: ?>
            <span class="badge badge-yellow"><i class="fas fa-clock"></i> <?php echo $fr ? 'En cours' : 'In Progress'; ?></span>
          <?php endif; ?>
        </span>
      </div>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Vérification des antécédents' : 'Background Check'; ?></span>
        <span class="info-value">
          <?php
          $bgMap = [
            'verified' => ['green',  'fa-shield-halved', $fr ? 'Vérifié' : 'Verified'],
            'waived'   => ['green',  'fa-shield-halved', $fr ? 'Dispensé' : 'Waived'],
            'uploaded' => ['yellow', 'fa-clock',         $fr ? 'En cours d\'examen' : 'Under Review'],
            'flagged'  => ['red',    'fa-flag',          $fr ? 'Signalé' : 'Flagged'],
          ];
          [$bc, $bi, $bl] = $bgMap[$bgStatus] ?? ['gray', 'fa-circle', $fr ? 'Non soumis' : 'Not Submitted'];
          ?>
          <span class="badge badge-<?= $bc ?>"><i class="fas <?= $bi ?>"></i> <?= $bl ?></span>
        </span>
      </div>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Livraisons' : 'Deliveries'; ?></span>
        <span class="info-value"><?= number_format($totalDeliveries) ?> <?php echo $fr ? 'complétées' : 'completed'; ?></span>
      </div>
      <?php if ($avgRating): ?>
      <div class="info-row">
        <span class="info-label"><?php echo $fr ? 'Évaluation' : 'Rating'; ?></span>
        <span class="info-value">
          <span class="badge badge-blue"><i class="fas fa-star"></i> <?= number_format((float)$avgRating, 1) ?></span>
        </span>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Photo preview
const photoInput = document.getElementById('photo-input');
const previewWrap = document.getElementById('preview-wrap');
const previewImg  = document.getElementById('preview-img');
const previewName = document.getElementById('preview-name');

photoInput.addEventListener('change', function () {
  const file = this.files[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    alert(<?php echo json_encode($fr ? 'Le fichier est trop volumineux. La taille maximale est de 5 Mo.' : 'File is too large. Maximum size is 5 MB.'); ?>);
    this.value = '';
    return;
  }
  const reader = new FileReader();
  reader.onload = e => {
    previewImg.src = e.target.result;
    previewName.textContent = file.name;
    previewWrap.style.display = 'flex';
  };
  reader.readAsDataURL(file);
});

function cancelPreview() {
  photoInput.value = '';
  previewWrap.style.display = 'none';
  previewImg.src = '';
  previewName.textContent = '';
}

// Password show/hide toggle
function togglePw(fieldId, btn) {
  const input = document.getElementById(fieldId);
  const icon  = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.className = 'fas fa-eye-slash';
  } else {
    input.type = 'password';
    icon.className = 'fas fa-eye';
  }
}

// Password requirements checker
const newPwInput  = document.getElementById('new_password');
const confPwInput = document.getElementById('confirm_password');
const matchHint   = document.getElementById('pw-match-hint');

const PW_RULES = [
  { id: 'req-length',  test: function(v) { return v.length >= 10; } },
  { id: 'req-upper',   test: function(v) { return /[A-Z]/.test(v); } },
  { id: 'req-lower',   test: function(v) { return /[a-z]/.test(v); } },
  { id: 'req-number',  test: function(v) { return /[0-9]/.test(v); } },
  { id: 'req-special', test: function(v) { return /[!@#$%^&*]/.test(v); } },
];

function checkPwRequirements() {
  const val = newPwInput ? newPwInput.value : '';
  PW_RULES.forEach(function(rule) {
    var el = document.getElementById(rule.id);
    if (el) el.classList.toggle('met', rule.test(val));
  });
}

function allRequirementsMet() {
  if (!newPwInput) return true;
  return PW_RULES.every(function(rule) { return rule.test(newPwInput.value); });
}

function checkPwMatch() {
  if (!confPwInput || !confPwInput.value) { matchHint.style.display = 'none'; return; }
  matchHint.style.display = (newPwInput.value !== confPwInput.value) ? '' : 'none';
}

if (newPwInput) {
  newPwInput.addEventListener('input', function() {
    checkPwRequirements();
    checkPwMatch();
  });
}
if (confPwInput) confPwInput.addEventListener('input', checkPwMatch);

// Block submit if requirements not met or passwords mismatch
document.getElementById('pw-form').addEventListener('submit', function(e) {
  if (!allRequirementsMet()) {
    e.preventDefault();
    checkPwRequirements();
    newPwInput.focus();
    return;
  }
  if (newPwInput.value !== confPwInput.value) {
    e.preventDefault();
    matchHint.style.display = '';
    confPwInput.focus();
  }
});
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
