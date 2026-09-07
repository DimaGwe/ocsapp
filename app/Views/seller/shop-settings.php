<?php
$shop = $shop ?? [];
$pageTitle = 'Shop Settings';
require __DIR__ . '/layout-header.php';
?>

<style>
  .card { background: #fff; border-radius: 12px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; max-width: 720px; }
  .card h2 { font-size: 16px; font-weight: 700; color: var(--gray-700); margin-bottom: 16px; padding-bottom: 10px; border-bottom: 1px solid var(--gray-100); }
  .form-group { margin-bottom: 18px; }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
  label { display: block; font-size: 13px; font-weight: 600; margin-bottom: 5px; color: var(--gray-700); }
  input, textarea, select { width: 100%; padding: 11px 14px; border: 1px solid var(--gray-200); border-radius: 8px; font-size: 14px; font-family: inherit; box-sizing: border-box; }
  input:focus, textarea:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(0,178,7,.1); }
  textarea { height: 100px; resize: vertical; }
  .btn { padding: 12px 28px; background: var(--primary); color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; }
  .btn:hover { background: var(--primary-600); }
  /* Image upload */
  .image-upload-box { border: 2px dashed var(--gray-200); border-radius: 10px; padding: 20px; text-align: center; cursor: pointer; transition: border-color .2s; position: relative; }
  .image-upload-box:hover { border-color: var(--primary); }
  .image-upload-box input[type=file] { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%; }
  .image-preview { width: 100%; max-height: 160px; object-fit: cover; border-radius: 8px; margin-bottom: 8px; display: block; }
  .image-preview-logo { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; margin: 0 auto 8px; display: block; }
  .upload-hint { font-size: 12px; color: var(--gray-400); }
  /* Toggle switch (notifications & sound) */
  .toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; }
  .toggle-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
  .toggle-switch input { opacity: 0; width: 0; height: 0; }
  .toggle-slider { position: absolute; cursor: pointer; inset: 0; background-color: var(--gray-300); border-radius: 24px; transition: .2s; }
  .toggle-slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; border-radius: 50%; transition: .2s; }
  .toggle-switch input:checked + .toggle-slider { background-color: var(--primary); }
  .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }
  @media (max-width: 768px) { .form-row { grid-template-columns: 1fr; } }
</style>

<!-- Shop Status -->
<div style="padding:12px 16px;background:#fff;border-radius:10px;box-shadow:0 1px 3px rgba(0,0,0,.1);margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;max-width:720px;">
    <strong>Shop Status:</strong>
    <?php if ($shop['is_active']): ?>
        <span style="color:#166534;font-weight:600;"><i class="fas fa-circle" style="font-size:8px;"></i> Active</span>
    <?php elseif ($shop['is_approved']): ?>
        <span style="color:var(--gray-600);">Inactive — contact admin to reactivate</span>
    <?php else: ?>
        <span style="color:#e65100;font-weight:600;"><i class="fas fa-clock" style="font-size:8px;"></i> Pending Approval</span>
    <?php endif; ?>
</div>

<form method="POST" action="<?= url('seller/shop/update') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>

    <!-- Shop Images -->
    <div class="card">
        <h2><i class="fas fa-image" style="color:var(--primary);margin-right:6px;"></i>Shop Images</h2>
        <div class="form-row">
            <!-- Logo -->
            <div class="form-group">
                <label>Shop Logo</label>
                <div class="image-upload-box" id="logo-box">
                    <input type="file" name="logo" accept="image/*" onchange="previewImage(this,'logo-preview')">
                    <?php if (!empty($shop['logo'])): ?>
                        <img id="logo-preview" class="image-preview-logo" src="<?= asset($shop['logo']) ?>" alt="Logo">
                    <?php else: ?>
                        <img id="logo-preview" class="image-preview-logo" src="" alt="" style="display:none;">
                        <i class="fas fa-store" style="font-size:32px;color:var(--gray-300);display:block;margin-bottom:6px;"></i>
                    <?php endif; ?>
                    <div class="upload-hint">Click to upload logo<br>JPG, PNG, WebP — max 5MB</div>
                </div>
            </div>
            <!-- Banner -->
            <div class="form-group">
                <label>Shop Banner / Cover Image</label>
                <div class="image-upload-box" id="cover-box">
                    <input type="file" name="cover_image" accept="image/*" onchange="previewImage(this,'cover-preview')">
                    <?php if (!empty($shop['cover_image'])): ?>
                        <img id="cover-preview" class="image-preview" src="<?= asset($shop['cover_image']) ?>" alt="Banner">
                    <?php else: ?>
                        <img id="cover-preview" class="image-preview" src="" alt="" style="display:none;">
                        <i class="fas fa-panorama" style="font-size:32px;color:var(--gray-300);display:block;margin-bottom:6px;"></i>
                    <?php endif; ?>
                    <div class="upload-hint">Click to upload banner<br>Recommended: 1200×300px</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Basic Info -->
    <div class="card">
        <h2><i class="fas fa-store" style="color:var(--primary);margin-right:6px;"></i>Shop Information</h2>
        <div class="form-group">
            <label>Shop Name *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($shop['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($shop['description'] ?? '') ?></textarea>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Phone</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($shop['phone'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($shop['email'] ?? '') ?>">
            </div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <input type="text" name="address" value="<?= htmlspecialchars($shop['address'] ?? '') ?>">
        </div>

        <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
    </div>
</form>

<!-- Notifications & Sound -->
<div class="card">
    <h2><i class="fas fa-bell" style="color:var(--primary);margin-right:6px;"></i>Notifications &amp; Sound</h2>
    <div class="toggle-row">
        <div>
            <div style="font-weight:600;font-size:14px;color:var(--gray-700);">Notification sound</div>
            <div style="font-size:13px;color:var(--gray-600);">Play a chime when a new notification or message arrives</div>
        </div>
        <label class="toggle-switch" title="Toggle notification sounds">
            <input type="checkbox" id="sellerSoundToggle" checked>
            <span class="toggle-slider"></span>
        </label>
    </div>
</div>

<!-- Change Password -->
<div class="card">
    <h2><i class="fas fa-lock" style="color:var(--primary);margin-right:6px;"></i>Change Password</h2>
    <form method="POST" action="<?= url('seller/settings/password') ?>">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Current Password *</label>
            <input type="password" name="current_password" required>
        </div>
        <div class="form-group">
            <label>New Password *</label>
            <input type="password" name="new_password" minlength="8" required>
        </div>
        <div class="form-group">
            <label>Confirm New Password *</label>
            <input type="password" name="confirm_password" minlength="8" required>
        </div>
        <button type="submit" class="btn"><i class="fas fa-key"></i> Update Password</button>
    </form>
</div>

<script>
function previewImage(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            var box = input.closest('.image-upload-box');
            var icon = box.querySelector('i.fas:not(.fa-check)');
            if (icon) icon.style.display = 'none';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Notification sound toggle — client-side only, same pattern as the supplier portal
(function() {
    var toggle = document.getElementById('sellerSoundToggle');
    if (!toggle) return;
    toggle.checked = localStorage.getItem('sel_sound_enabled') !== 'off';
    toggle.addEventListener('change', function() {
        localStorage.setItem('sel_sound_enabled', toggle.checked ? 'on' : 'off');
    });
})();
</script>

<?php require __DIR__ . '/layout-footer.php'; ?>
