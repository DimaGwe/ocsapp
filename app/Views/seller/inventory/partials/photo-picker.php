<?php
/**
 * Product photo picker (input images[]) for the seller create/edit product forms. The form needs
 * enctype="multipart/form-data". Optional: $photoSlots (photos still allowed, default 6).
 * Client-side checks are for convenience only; InventoryController::saveProductImages() validates.
 */
$photoSlots = max(0, (int) ($photoSlots ?? 6));
?>
<?php if ($photoSlots > 0): ?>
<div class="sp-field">
  <?= $fr ? 'Photos du produit' : 'Product photos' ?>
  <label class="sp-photo-drop">
    <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple data-max="<?= $photoSlots ?>" class="js-photo-input">
    <i class="fa-solid fa-camera"></i>
    <span class="sp-strong"><?= $fr ? 'Ajouter des photos' : 'Add photos' ?></span>
    <span class="sp-dim"><?= $fr
      ? "Jusqu'à {$photoSlots} photo" . ($photoSlots > 1 ? 's' : '') . '. JPG, PNG, WebP ou GIF, 5 Mo max chacune. La première devient la photo principale si le produit n\'en a pas.'
      : "Up to {$photoSlots} photo" . ($photoSlots > 1 ? 's' : '') . '. JPG, PNG, WebP or GIF, 5 MB max each. The first one becomes the main photo if the product has none.' ?></span>
  </label>
  <div class="sp-photo-grid js-photo-previews"></div>
</div>
<script>
(function () {
  var T = <?= json_encode($fr
      ? ['big' => 'dépasse 5 Mo', 'type' => "n'est pas une image JPG, PNG, WebP ou GIF", 'max' => 'Maximum de %n photos : les suivantes ne seront pas ajoutées.']
      : ['big' => 'is over 5 MB', 'type' => 'is not a JPG, PNG, WebP or GIF image', 'max' => 'Maximum %n photos: the extra ones will not be added.'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
  document.querySelectorAll('.js-photo-input').forEach(function (input) {
    var box = input.closest('.sp-field').querySelector('.js-photo-previews');
    input.addEventListener('change', function () {
      box.textContent = '';
      var max = +input.dataset.max, problems = [];
      var ok = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
      Array.from(input.files).forEach(function (f, i) {
        if (f.size > 5 * 1024 * 1024) { problems.push(f.name + ' ' + T.big); return; }
        if (ok.indexOf(f.type) === -1) { problems.push(f.name + ' ' + T.type); return; }
        if (i >= max) return;
        var img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(f);
        box.appendChild(img);
      });
      if (input.files.length > max) problems.push(T.max.replace('%n', max));
      if (problems.length) alert(problems.join('\n'));
    });
  });
})();
</script>
<?php else: ?>
  <p class="sp-dim"><?= $fr ? 'Nombre maximal de photos atteint. Retirez-en une pour en ajouter une autre.' : 'Maximum number of photos reached. Remove one to add another.' ?></p>
<?php endif; ?>
