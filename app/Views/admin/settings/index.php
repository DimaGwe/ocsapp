<?php
/**
 * OCS Admin Site Settings
 * File: app/Views/admin/settings/index.php
 */

$pageTitle = 'Site Settings';
$currentPage = 'settings';

// Get current language
$currentLang = getCurrentLanguage();

// Use database translations
$t = getTranslations($currentLang);

// Fallback translations if not in database
$defaultTranslations = [
    'site_settings' => $currentLang === 'fr' ? 'Paramètres Site' : 'Site Settings',
    'manage_marketplace' => $currentLang === 'fr' ? 'Gérer la configuration de votre marketplace' : 'Manage your marketplace configuration',
    'settings_suffix' => $currentLang === 'fr' ? 'Paramètres' : 'Settings',
    'save_all_settings' => $currentLang === 'fr' ? 'Enregistrer tous' : 'Save All Settings',
    'current_image' => $currentLang === 'fr' ? 'Image actuelle' : 'Current Image',
    'enable' => $currentLang === 'fr' ? 'Activer' : 'Enable',
    'language_preference' => $currentLang === 'fr' ? 'Préférence de langue' : 'Language Preference',
    'select_language' => $currentLang === 'fr' ? 'Sélectionnez votre langue préférée' : 'Select your preferred language',
    'english' => 'English',
    'french' => 'Français',
    'general' => $currentLang === 'fr' ? 'Général' : 'General',
    'email' => 'Email',
    'payment' => $currentLang === 'fr' ? 'Paiement' : 'Payment',
    'shipping' => $currentLang === 'fr' ? 'Livraison' : 'Shipping',
    'social_media' => $currentLang === 'fr' ? 'Réseaux sociaux' : 'Social Media',
    'appearance' => $currentLang === 'fr' ? 'Apparence' : 'Appearance',
    'advanced' => $currentLang === 'fr' ? 'Avancé' : 'Advanced',
];

// Merge with defaults for missing keys
foreach ($defaultTranslations as $key => $value) {
    if (!isset($t[$key]) || empty($t[$key])) {
        $t[$key] = $value;
    }
}

// Category translations
$categoryNames = [
    'general' => $t['general'],
    'email' => $t['email'],
    'payment' => $t['payment'],
    'shipping' => $t['shipping'],
    'social_media' => $t['social_media'],
    'appearance' => $t['appearance'],
    'advanced' => $t['advanced'],
];

ob_start();
?>

<style>
  /* Page Header */
  .settings-header {
    margin-bottom: 32px;
  }

  .settings-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .settings-header p {
    font-size: 15px;
    color: var(--gray-600);
  }

  /* Language Selection Card */
  .language-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
    border-left: 4px solid var(--primary);
  }

  .language-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
  }

  .language-icon {
    width: 40px;
    height: 40px;
    background: #dcfce7;
    color: var(--primary);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
  }

  .language-card-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--dark);
  }

  .language-description {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 16px;
  }

  .language-selector {
    display: flex;
    gap: 12px;
  }

  .language-option {
    flex: 1;
    padding: 12px 20px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all var(--transition-base);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    background: white;
  }

  .language-option input[type="radio"] {
    display: none;
  }

  .language-option:hover {
    border-color: var(--primary);
    background: #f0fdf4;
  }

  .language-option input[type="radio"]:checked + label,
  .language-option.selected {
    border-color: var(--primary);
    background: #dcfce7;
    color: var(--primary);
  }

  .language-flag {
    font-size: 18px;
  }

  /* Settings Card */
  .settings-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    margin-bottom: 24px;
  }

  .settings-card-header {
    padding-bottom: 16px;
    margin-bottom: 24px;
    border-bottom: 2px solid var(--border);
  }

  .settings-card-header h2 {
    font-size: 18px;
    font-weight: 700;
    color: var(--dark);
    text-transform: capitalize;
  }

  /* Form Grid */
  .settings-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
  }

  .form-field {
    display: flex;
    flex-direction: column;
  }

  .form-field.full-width {
    grid-column: 1 / -1;
  }

  .form-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--dark);
    margin-bottom: 8px;
  }

  .form-description {
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 8px;
  }

  .form-input,
  .form-textarea,
  .form-select {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    font-family: inherit;
    transition: all var(--transition-base);
  }

  .form-input:focus,
  .form-textarea:focus,
  .form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0, 178, 7, 0.1);
  }

  .form-textarea {
    resize: vertical;
    min-height: 100px;
  }

  /* Checkbox */
  .checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--gray-50);
    border-radius: var(--radius-md);
    border: 2px solid var(--border);
    transition: all var(--transition-base);
    cursor: pointer;
  }

  .checkbox-wrapper:hover {
    background: var(--gray-100);
  }

  .checkbox-wrapper input[type="checkbox"] {
    width: 20px;
    height: 20px;
    border: 2px solid var(--border);
    border-radius: 4px;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .checkbox-wrapper input[type="checkbox"]:checked {
    background: var(--primary);
    border-color: var(--primary);
  }

  .checkbox-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--dark);
    cursor: pointer;
  }

  /* Image Upload */
  .image-upload {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .current-image {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .current-image-label {
    font-size: 12px;
    color: var(--gray-500);
  }

  .current-image img {
    height: 80px;
    width: auto;
    border-radius: var(--radius-md);
    border: 2px solid var(--border);
  }

  .file-input {
    width: 100%;
    padding: 10px 16px;
    border: 2px solid var(--border);
    border-radius: var(--radius-md);
    font-size: 14px;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .file-input:hover {
    border-color: var(--primary);
  }

  .file-input::file-selector-button {
    margin-right: 16px;
    padding: 8px 16px;
    background: #dcfce7;
    color: var(--primary);
    border: none;
    border-radius: var(--radius-md);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
  }

  .file-input::file-selector-button:hover {
    background: var(--primary);
    color: white;
  }

  /* Save Button Card */
  .save-card {
    background: white;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-sm);
    padding: 24px;
    display: flex;
    justify-content: flex-end;
  }

  .btn-save {
    padding: 12px 32px;
    background: var(--primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--transition-base);
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-save:hover {
    background: var(--primary-600);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .settings-grid {
      grid-template-columns: 1fr;
    }

    .form-field.full-width {
      grid-column: 1;
    }

    .language-selector {
      flex-direction: column;
    }

    .save-card {
      justify-content: stretch;
    }

    .btn-save {
      width: 100%;
      justify-content: center;
    }
  }
</style>

<!-- Page Header -->
<div class="settings-header">
  <h1><?= $t['site_settings'] ?></h1>
  <p><?= $t['manage_marketplace'] ?></p>
</div>

<!-- Settings Form -->
<form method="POST" action="<?= url('admin/settings/update') ?>" enctype="multipart/form-data">
  <?= csrfField() ?>
  
  <!-- Language Selection Card -->
  <div class="language-card">
    <div class="language-card-header">
      <div class="language-icon">
        <i class="fas fa-globe"></i>
      </div>
      <h3><?= $t['language_preference'] ?></h3>
    </div>
    <p class="language-description"><?= $t['select_language'] ?></p>
    
    <div class="language-selector">
      <label class="language-option <?= $currentLang === 'en' ? 'selected' : '' ?>">
        <input type="radio" name="settings[admin_language]" value="en" <?= $currentLang === 'en' ? 'checked' : '' ?>>
        <span class="language-flag">🇺🇸</span>
        <span><?= $t['english'] ?></span>
      </label>
      <label class="language-option <?= $currentLang === 'fr' ? 'selected' : '' ?>">
        <input type="radio" name="settings[admin_language]" value="fr" <?= $currentLang === 'fr' ? 'checked' : '' ?>>
        <span class="language-flag">🇫🇷</span>
        <span><?= $t['french'] ?></span>
      </label>
    </div>
  </div>

  <!-- Settings Cards by Category -->
  <?php foreach ($settingsByCategory as $category => $settings): ?>
    <div class="settings-card">
      <div class="settings-card-header">
        <h2>
          <?= $categoryNames[$category] ?? ucwords(str_replace('_', ' ', $category)) ?> 
          <?= $t['settings_suffix'] ?>
        </h2>
      </div>
      
      <div class="settings-grid">
        <?php foreach ($settings as $setting): ?>
          <div class="form-field <?= in_array($setting['type'], ['textarea', 'image']) ? 'full-width' : '' ?>">
            <label for="setting_<?= $setting['key'] ?>" class="form-label">
              <?= htmlspecialchars($setting['label']) ?>
            </label>
            
            <?php if (!empty($setting['description'])): ?>
              <p class="form-description"><?= htmlspecialchars($setting['description']) ?></p>
            <?php endif; ?>
            
            <?php if ($setting['type'] === 'textarea'): ?>
              <textarea 
                id="setting_<?= $setting['key'] ?>"
                name="settings[<?= $setting['key'] ?>]"
                class="form-textarea"
              ><?= htmlspecialchars($setting['value']) ?></textarea>
              
            <?php elseif ($setting['type'] === 'boolean'): ?>
              <label class="checkbox-wrapper">
                <input 
                  type="checkbox"
                  id="setting_<?= $setting['key'] ?>"
                  name="settings[<?= $setting['key'] ?>]"
                  value="1"
                  <?= $setting['value'] === 'true' || $setting['value'] === '1' ? 'checked' : '' ?>
                >
                <span class="checkbox-label"><?= $t['enable'] ?></span>
              </label>
              
            <?php elseif ($setting['type'] === 'image'): ?>
              <div class="image-upload">
                <?php if (!empty($setting['value'])): ?>
                  <div class="current-image">
                    <span class="current-image-label"><?= $t['current_image'] ?>:</span>
                    <img 
                      src="<?= asset($setting['value']) ?>" 
                      alt="<?= htmlspecialchars($setting['label']) ?>"
                    >
                  </div>
                <?php endif; ?>
                <input 
                  type="file"
                  id="setting_<?= $setting['key'] ?>"
                  name="images[<?= $setting['key'] ?>]"
                  accept="image/*"
                  class="file-input"
                >
              </div>
              
            <?php else: ?>
              <input 
                type="<?= $setting['type'] === 'number' ? 'number' : ($setting['type'] === 'email' ? 'email' : ($setting['type'] === 'url' ? 'url' : 'text')) ?>"
                id="setting_<?= $setting['key'] ?>"
                name="settings[<?= $setting['key'] ?>]"
                value="<?= htmlspecialchars($setting['value']) ?>"
                class="form-input"
                <?= $setting['type'] === 'number' ? 'step="0.01"' : '' ?>
              >
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
  
  <!-- Save Button -->
  <div class="save-card">
    <button type="submit" class="btn-save">
      <i class="fas fa-save"></i> <?= $t['save_all_settings'] ?>
    </button>
  </div>
</form>

<script>
// Language selector interaction with site-wide language change
document.querySelectorAll('.language-option').forEach(option => {
  option.addEventListener('click', function() {
    // Get the selected language
    const radio = this.querySelector('input[type="radio"]');
    if (!radio) return;

    const selectedLang = radio.value;

    // Make AJAX request to change language
    fetch('<?= url('set-language') ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: new URLSearchParams({
        'language': selectedLang,
        '<?= env('CSRF_TOKEN_NAME', '_csrf_token') ?>': '<?= generateCsrfToken() ?>'
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Reload page to apply language change across entire admin panel
        window.location.reload();
      } else {
        console.error('Failed to change language:', data.message);
      }
    })
    .catch(error => {
      console.error('Error changing language:', error);
    });
  });
});

// Checkbox wrapper click handling
document.querySelectorAll('.checkbox-wrapper').forEach(wrapper => {
  wrapper.addEventListener('click', function(e) {
    if (e.target.tagName !== 'INPUT') {
      const checkbox = this.querySelector('input[type="checkbox"]');
      if (checkbox) {
        checkbox.checked = !checkbox.checked;
      }
    }
  });
});
</script>

<!-- Notifications & Sound -->
<div class="settings-card" style="margin-top:24px;">
  <div class="settings-card-header">
    <h2><i class="fas fa-bell" style="color:var(--primary);margin-right:8px;"></i> Notifications &amp; Sound</h2>
  </div>

  <div class="notif-pref-row">
    <div class="notif-pref-info">
      <div class="notif-pref-label"><i class="fas fa-volume-up"></i> Notification Sounds</div>
      <div class="notif-pref-desc">Play a chime when new orders, leads, or distribution requests arrive.</div>
    </div>
    <label class="toggle-switch" title="Toggle notification sounds">
      <input type="checkbox" id="soundToggle" onchange="onSoundToggle(this.checked)">
      <span class="toggle-track"></span>
    </label>
  </div>
  <div style="margin-top:8px;margin-bottom:20px;">
    <button type="button" class="btn-test-sound" onclick="testSound()">
      <i class="fas fa-play-circle"></i> Test Sound
    </button>
    <span id="testSoundMsg" style="margin-left:10px;font-size:13px;color:#6b7280;display:none;"></span>
  </div>

  <hr style="border:none;border-top:1px solid var(--border);margin:0 0 20px;">

  <div class="notif-pref-row">
    <div class="notif-pref-info">
      <div class="notif-pref-label"><i class="fas fa-desktop"></i> Browser Notifications</div>
      <div class="notif-pref-desc">Show a system popup even when you're looking at another tab.</div>
    </div>
    <span id="browserNotifStatus" class="notif-status-badge-admin"></span>
  </div>
  <div id="browserNotifAction" style="margin-top:10px;"></div>
</div>

<style>
.notif-pref-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:6px 0; }
.notif-pref-info { flex:1; }
.notif-pref-label { font-size:14px; font-weight:600; color:#111827; display:flex; align-items:center; gap:8px; margin-bottom:4px; }
.notif-pref-label i { color:var(--primary); }
.notif-pref-desc { font-size:13px; color:#6b7280; line-height:1.5; }
.toggle-switch { position:relative; display:inline-block; width:46px; height:26px; flex-shrink:0; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-track { position:absolute; inset:0; background:#d1d5db; border-radius:26px; transition:background 0.2s; }
.toggle-track::before { content:''; position:absolute; width:20px; height:20px; left:3px; bottom:3px; background:white; border-radius:50%; transition:transform 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
.toggle-switch input:checked + .toggle-track { background:var(--primary); }
.toggle-switch input:checked + .toggle-track::before { transform:translateX(20px); }
.btn-test-sound { background:none; border:1px solid var(--primary); color:var(--primary); border-radius:8px; padding:7px 16px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background 0.15s,color 0.15s; }
.btn-test-sound:hover { background:var(--primary); color:white; }
.notif-status-badge-admin { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; flex-shrink:0; }
.notif-status-badge-admin.granted { background:#d1fae5; color:#059669; }
.notif-status-badge-admin.default { background:#fef3c7; color:#92400e; }
.notif-status-badge-admin.denied  { background:#fee2e2; color:#dc2626; }
</style>

<script>
// ── Notifications & Sound Settings (Admin) ────────────────────────────────────
(function initAdminNotifSettings() {
  var soundToggle = document.getElementById('soundToggle');
  var soundEnabled = localStorage.getItem('admin_sound_enabled') !== 'off';
  soundToggle.checked = soundEnabled;

  window.onSoundToggle = function(checked) {
    localStorage.setItem('admin_sound_enabled', checked ? 'on' : 'off');
    if (checked && typeof _unlockAdminAudio === 'function') _unlockAdminAudio();
  };

  window.testSound = function() {
    var msg = document.getElementById('testSoundMsg');
    if (typeof _unlockAdminAudio === 'function') _unlockAdminAudio();
    localStorage.setItem('admin_sound_enabled', 'on');
    soundToggle.checked = true;
    if (typeof playAdminChime === 'function') {
      playAdminChime();
      msg.textContent = 'Sound played!';
    } else {
      msg.textContent = 'Sound function not available.';
    }
    msg.style.display = 'inline';
    setTimeout(function() { msg.style.display = 'none'; }, 2500);
  };

  var statusBadge = document.getElementById('browserNotifStatus');
  var actionArea  = document.getElementById('browserNotifAction');

  function renderNotifStatus() {
    if (typeof Notification === 'undefined') {
      statusBadge.textContent = 'Not supported';
      statusBadge.className = 'notif-status-badge-admin denied';
      actionArea.innerHTML = '<small style="color:#6b7280;">Your browser does not support desktop notifications.</small>';
      return;
    }
    var perm = Notification.permission;
    if (perm === 'granted') {
      statusBadge.textContent = 'Enabled';
      statusBadge.className = 'notif-status-badge-admin granted';
      actionArea.innerHTML = '<small style="color:#059669;"><i class="fas fa-check-circle"></i> You\'ll receive desktop notifications for new activity.</small>';
    } else if (perm === 'denied') {
      statusBadge.textContent = 'Blocked';
      statusBadge.className = 'notif-status-badge-admin denied';
      actionArea.innerHTML = '<small style="color:#dc2626;"><i class="fas fa-ban"></i> Notifications are blocked. Allow them in your browser\'s site settings.</small>';
    } else {
      statusBadge.textContent = 'Not enabled';
      statusBadge.className = 'notif-status-badge-admin default';
      actionArea.innerHTML = '<button type="button" class="btn-test-sound" onclick="requestBrowserNotif()"><i class="fas fa-bell"></i> Enable Browser Notifications</button>';
    }
  }

  window.requestBrowserNotif = function() {
    Notification.requestPermission().then(function() { renderNotifStatus(); });
  };

  renderNotifStatus();
})();
</script>

<?php
$content = ob_get_clean();
require dirname(__DIR__) . '/layout.php';
?>