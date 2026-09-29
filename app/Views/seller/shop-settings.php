<?php
/**
 * Seller shop settings (/seller/shop/settings, ShopController::settings)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR. Same three forms and field names.
 * Fixes: text fields showed the stored HTML-escaped value, so each save escaped it again
 * ("L'Épicerie" -> "L&amp;#039;..."); images used asset() on "assets/..." paths (404).
 */
$shop = $shop ?? [];
$pageTitle = 'Shop Settings';
require __DIR__ . '/layout-header.php';
$v = fn($k) => htmlspecialchars(html_entity_decode((string) ($shop[$k] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$img = fn($p) => !empty($p) ? htmlspecialchars(url(ltrim($p, '/'))) : '';
?>
<style>
  .sp-upload{position:relative;display:grid;place-items:center;gap:6px;min-height:150px;border:1.5px dashed var(--sp-border);border-radius:14px;background:#FAFBFA;text-align:center;padding:16px;cursor:pointer}
  .sp-upload:hover{border-color:var(--sp-green)}
  .sp-upload input{position:absolute;inset:0;opacity:0;cursor:pointer}
  .sp-upload img{max-width:100%;max-height:120px;border-radius:10px;object-fit:cover}
  .sp-upload .logo{width:96px;height:96px;border-radius:50%}
  .sp-upload > i{font-size:28px;color:#C9CFCA}
</style>
<div class="sp-stack" style="max-width:820px">
  <section class="sp-card sp-between" style="padding:14px 18px">
    <span class="sp-strong"><?= $fr ? 'Statut du commerce' : 'Shop status' ?></span>
    <?php if (!empty($shop['is_active'])): ?>
      <span class="sp-badge sp-badge-active"><i class="fa-solid fa-circle" style="font-size:7px"></i> <?= $fr ? 'Actif' : 'Active' ?></span>
    <?php elseif (!empty($shop['is_approved'])): ?>
      <span class="sp-badge"><?= $fr ? "Inactif : contactez l'équipe OCSAPP pour le réactiver" : 'Inactive: contact the OCSAPP team to reactivate' ?></span>
    <?php else: ?>
      <span class="sp-badge sp-badge-warn"><i class="fa-solid fa-clock"></i> <?= $fr ? "En attente d'approbation" : 'Pending approval' ?></span>
    <?php endif; ?>
  </section>

  <form method="POST" action="<?= url('seller/shop/update') ?>" enctype="multipart/form-data" class="sp-stack">
    <?= csrfField() ?>
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Images du commerce' : 'Shop images' ?></h2></div>
      <div class="sp-grid-2">
        <label class="sp-field"><?= $fr ? 'Logo' : 'Logo' ?>
          <span class="sp-upload">
            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" data-preview="logo-preview">
            <img id="logo-preview" class="logo" src="<?= $img($shop['logo'] ?? '') ?>" alt="" <?= empty($shop['logo']) ? 'hidden' : '' ?>>
            <?php if (empty($shop['logo'])): ?><i class="fa-solid fa-store"></i><?php endif; ?>
            <span class="sp-dim"><?= $fr ? 'Cliquez pour téléverser. JPG, PNG ou WebP, 5 Mo max.' : 'Click to upload. JPG, PNG or WebP, 5 MB max.' ?></span>
          </span></label>
        <label class="sp-field"><?= $fr ? 'Bannière' : 'Banner' ?>
          <span class="sp-upload">
            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" data-preview="cover-preview">
            <img id="cover-preview" src="<?= $img($shop['cover_image'] ?? '') ?>" alt="" <?= empty($shop['cover_image']) ? 'hidden' : '' ?>>
            <?php if (empty($shop['cover_image'])): ?><i class="fa-solid fa-panorama"></i><?php endif; ?>
            <span class="sp-dim"><?= $fr ? 'Format recommandé : 1200 x 300 px.' : 'Recommended: 1200 x 300 px.' ?></span>
          </span></label>
      </div>
    </section>

    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Informations du commerce' : 'Shop information' ?></h2></div>
      <div class="sp-form">
        <label><?= $fr ? 'Nom du commerce' : 'Shop name' ?> *<input type="text" name="name" value="<?= $v('name') ?>" required></label>
        <label><?= $fr ? 'Description' : 'Description' ?><textarea name="description"><?= $v('description') ?></textarea></label>
        <div class="sp-form-row">
          <label><?= $fr ? 'Téléphone' : 'Phone' ?><input type="tel" name="phone" value="<?= $v('phone') ?>"></label>
          <label><?= $fr ? 'Courriel' : 'Email' ?><input type="email" name="email" value="<?= $v('email') ?>"></label>
        </div>
        <label><?= $fr ? 'Adresse' : 'Address' ?><input type="text" name="address" value="<?= $v('address') ?>">
          <small><?= $fr ? "Pour les commandes en ramassage, c'est l'adresse que voit le client." : 'For pickup orders, this is the address the customer sees.' ?></small></label>
        <div><button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-floppy-disk"></i> <?= $fr ? 'Enregistrer' : 'Save changes' ?></button></div>
      </div>
    </section>
  </form>

  <section class="sp-card">
    <div class="sp-card-head"><h2><?= $fr ? 'Son des notifications' : 'Notification sound' ?></h2></div>
    <label class="sp-between" style="cursor:pointer">
      <span class="sp-muted"><?= $fr ? "Jouer un son à l'arrivée d'une notification ou d'un message." : 'Play a chime when a notification or message arrives.' ?></span>
      <input type="checkbox" id="sellerSoundToggle" checked style="width:20px;height:20px;accent-color:var(--sp-green)">
    </label>
  </section>

  <section class="sp-card">
    <div class="sp-card-head"><h2><?= $fr ? 'Mot de passe' : 'Password' ?></h2></div>
    <form class="sp-form" method="POST" action="<?= url('seller/settings/password') ?>">
      <?= csrfField() ?>
      <label><?= $fr ? 'Mot de passe actuel' : 'Current password' ?> *<input type="password" name="current_password" required autocomplete="current-password"></label>
      <div class="sp-form-row">
        <label><?= $fr ? 'Nouveau mot de passe' : 'New password' ?> *<input type="password" name="new_password" minlength="8" required autocomplete="new-password"></label>
        <label><?= $fr ? 'Confirmer' : 'Confirm' ?> *<input type="password" name="confirm_password" minlength="8" required autocomplete="new-password"></label>
      </div>
      <small class="sp-help"><?= $fr ? 'Au moins 8 caractères.' : 'At least 8 characters.' ?></small>
      <div><button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-key"></i> <?= $fr ? 'Mettre à jour le mot de passe' : 'Update password' ?></button></div>
    </form>
  </section>
</div>
<script>
document.querySelectorAll('.sp-upload input[type=file]').forEach(function (input) {
  input.addEventListener('change', function () {
    var img = document.getElementById(input.dataset.preview);
    if (!input.files || !input.files[0]) return;
    var r = new FileReader();
    r.onload = function (e) { img.src = e.target.result; img.hidden = false; var i = input.parentNode.querySelector(':scope > i'); if (i) i.hidden = true; };
    r.readAsDataURL(input.files[0]);
  });
});
(function () {
  var t = document.getElementById('sellerSoundToggle');
  try { t.checked = localStorage.getItem('sel_sound_enabled') !== 'off'; } catch (e) {}
  t.addEventListener('change', function () { try { localStorage.setItem('sel_sound_enabled', t.checked ? 'on' : 'off'); } catch (e) {} });
})();
</script>
<?php require __DIR__ . '/layout-footer.php'; ?>
