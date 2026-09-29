<?php
/**
 * Seller business verification (/seller/verification, SellerVerificationController::index)
 * Updated 2026-09-28: seller-portal.css kit, bilingual EN/FR (was English), fr-CA dates.
 * Same form (POST seller/verification/submit, documents[] PDF/JPG/PNG up to 5 MB each);
 * selected file names are now rendered as text, not HTML.
 */
$db = \Database::getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([userId()]);
$verifUser = $stmt->fetch() ?: [];
$verificationStatus = $verifUser['verification_status'] ?? 'unverified';
$documents = !empty($verifUser['verification_documents']) ? (json_decode($verifUser['verification_documents'], true) ?: []) : [];

$pageTitle = 'Verification';
require __DIR__ . '/layout-header.php';
$v = fn($k) => htmlspecialchars(html_entity_decode((string) ($verifUser[$k] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
$banner = [
    'unverified' => ['sp-alert-warn', 'fa-triangle-exclamation', $fr ? 'Vérification requise' : 'Verification required',
        $fr ? 'Complétez la vérification de votre entreprise pour déverrouiller toutes les fonctionnalités et commencer à vendre.' : 'Complete your business verification to unlock every feature and start selling.'],
    'pending'    => ['sp-alert-info', 'fa-clock', $fr ? 'Vérification en cours' : 'Verification in progress',
        $fr ? 'Votre dossier est en cours d\'examen. Nous vous aviserons dès qu\'il sera traité (habituellement en 1 à 2 jours ouvrables).' : "Your file is under review. We'll let you know once it's processed (usually 1 to 2 business days)."],
    'verified'   => ['sp-alert-ok', 'fa-circle-check', $fr ? 'Vendeur vérifié' : 'Verified seller',
        $fr ? 'Votre compte est vérifié. Vous avez accès à toutes les fonctionnalités.' : 'Your account is verified. You have access to every feature.'],
    'rejected'   => ['sp-alert-err', 'fa-circle-xmark', $fr ? 'Vérification refusée' : 'Verification rejected',
        $fr ? 'Votre vérification a été refusée. Consultez la raison ci-dessous et soumettez de nouveau.' : 'Your verification was rejected. Review the reason below and resubmit.'],
];
[$cls, $icon, $title, $text] = $banner[$verificationStatus] ?? $banner['rejected'];
$checks = [
    [!empty($verifUser['business_name']), $fr ? "Informations sur l'entreprise" : 'Business information', $fr ? "Nom de l'entreprise, numéro d'entreprise et numéros de taxes" : 'Business name, business number and tax numbers'],
    [!empty($verifUser['business_address']), $fr ? "Adresse de l'entreprise" : 'Business address', $fr ? "L'adresse enregistrée de votre entreprise" : 'Your registered business address'],
    [!empty($documents), $fr ? 'Documents de vérification' : 'Verification documents', $fr ? "Permis d'exploitation, certificat de taxes ou statuts constitutifs" : 'Business licence, tax certificate or articles of incorporation'],
];
?>
<div class="sp-stack" style="max-width:820px">
  <div class="sp-alert <?= $cls ?>" style="margin:0"><i class="fa-solid <?= $icon ?>" style="font-size:20px"></i>
    <div><strong style="display:block;font-size:15px;margin-bottom:2px"><?= $title ?></strong><?= $text ?></div></div>

  <?php if ($verificationStatus === 'rejected' && !empty($verifUser['verification_notes'])): ?>
    <section class="sp-card"><div class="sp-card-head"><h2><?= $fr ? 'Raison du refus' : 'Reason for rejection' ?></h2></div>
      <p style="margin:0"><?= nl2br($v('verification_notes')) ?></p></section>
  <?php endif; ?>

  <?php if ($verificationStatus === 'verified'): ?>
    <section class="sp-card"><p style="margin:0"><?= $fr ? 'Vérifié le ' : 'Verified on ' ?><strong><?= acct_date($verifUser['verified_at'] ?? '', $fr) ?></strong>.
      <?= $fr ? 'Votre commerce est visible sur le marché et vous pouvez ajouter des produits.' : 'Your shop is visible on the marketplace and you can add products.' ?></p></section>
  <?php else: ?>
    <section class="sp-card">
      <div class="sp-card-head"><h2><?= $fr ? 'Liste de vérification' : 'Checklist' ?></h2></div>
      <?php foreach ($checks as [$done, $h, $d]): ?>
        <div class="sp-list-row" style="justify-content:flex-start">
          <span class="sp-badge <?= $done ? 'sp-badge-ok' : '' ?>" style="width:28px;height:28px;padding:0;justify-content:center"><i class="fa-solid <?= $done ? 'fa-check' : 'fa-minus' ?>"></i></span>
          <div><div class="sp-strong"><?= $h ?></div><div class="sp-dim"><?= $d ?></div></div>
        </div>
      <?php endforeach; ?>
    </section>

    <form method="POST" action="<?= url('seller/verification/submit') ?>" enctype="multipart/form-data" class="sp-stack">
      <?= csrfField() ?>
      <section class="sp-card">
        <div class="sp-card-head"><h2><?= $fr ? "Informations sur l'entreprise" : 'Business information' ?></h2></div>
        <div class="sp-form">
          <label><?= $fr ? "Nom de l'entreprise" : 'Business name' ?> *<input type="text" name="business_name" value="<?= $v('business_name') ?>" required placeholder="<?= $fr ? 'ex. Épicerie ABC inc.' : 'e.g. ABC Grocery Inc.' ?>"></label>
          <div class="sp-form-row">
            <label><?= $fr ? "Numéro d'entreprise (NEQ ou NE)" : 'Business number (NEQ or BN)' ?><input type="text" name="business_number" value="<?= $v('business_number') ?>" placeholder="1181584997"></label>
            <label><?= $fr ? 'Numéros de TPS/TVQ' : 'GST/QST numbers' ?><input type="text" name="tax_id" value="<?= $v('tax_id') ?>" placeholder="123456789RT0001"></label>
          </div>
          <label><?= $fr ? "Adresse de l'entreprise" : 'Business address' ?> *<textarea name="business_address" rows="3" required><?= $v('business_address') ?></textarea></label>
        </div>
      </section>

      <section class="sp-card">
        <div class="sp-card-head"><h2><?= $fr ? 'Documents' : 'Documents' ?></h2></div>
        <p class="sp-muted" style="margin:-6px 0 12px"><?= $fr
          ? "Téléversez au moins un document officiel : permis d'exploitation, certificat de taxes, statuts constitutifs, etc. PDF, JPG ou PNG, 5 Mo max chacun."
          : 'Upload at least one official document: business licence, tax certificate, articles of incorporation, etc. PDF, JPG or PNG, 5 MB max each.' ?></p>
        <label class="sp-upload" style="display:grid;place-items:center;gap:6px;min-height:130px;border:1.5px dashed var(--sp-border);border-radius:14px;background:#FAFBFA;cursor:pointer;position:relative;text-align:center">
          <i class="fa-solid fa-cloud-arrow-up" style="font-size:28px;color:var(--sp-green)"></i>
          <span class="sp-strong"><?= $fr ? 'Cliquez pour choisir des fichiers' : 'Click to choose files' ?></span>
          <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" style="position:absolute;inset:0;opacity:0;cursor:pointer">
        </label>
        <div id="uploadedFiles" style="margin-top:10px"></div>
        <?php if (!empty($documents)): ?>
          <p class="sp-dim" style="margin:14px 0 6px"><?= $fr ? 'Documents déjà téléversés :' : 'Already uploaded:' ?></p>
          <?php foreach ($documents as $doc): ?>
            <div class="sp-list-row" style="justify-content:flex-start"><i class="fa-solid fa-file-lines sp-green"></i> <?= htmlspecialchars($doc['name'] ?? ($fr ? 'Document' : 'Document')) ?></div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

      <button type="submit" class="sp-btn sp-btn-primary" style="width:100%"><i class="fa-solid fa-paper-plane"></i> <?= $fr ? 'Soumettre pour vérification' : 'Submit for verification' ?></button>
    </form>
  <?php endif; ?>
</div>
<script>
(function () {
  var T = <?= json_encode($fr ? ['big' => 'est trop volumineux (5 Mo max).', 'type' => "n'est pas un fichier accepté (PDF, JPG ou PNG)."] : ['big' => 'is too large (5 MB max).', 'type' => 'is not an accepted file (PDF, JPG or PNG).'], JSON_UNESCAPED_UNICODE | JSON_HEX_APOS) ?>;
  var FR_KB = <?= json_encode($fr ? ' Ko' : ' KB') ?>;
  var input = document.getElementById('documents');
  if (!input) return;
  input.addEventListener('change', function () {
    var box = document.getElementById('uploadedFiles');
    box.textContent = '';
    Array.from(input.files).forEach(function (f) {
      if (f.size > 5 * 1024 * 1024) { alert(f.name + ' ' + T.big); return; }
      if (['application/pdf', 'image/jpeg', 'image/png'].indexOf(f.type) === -1) { alert(f.name + ' ' + T.type); return; }
      var row = document.createElement('div');
      row.className = 'sp-list-row';
      row.style.justifyContent = 'flex-start';
      var i = document.createElement('i');
      i.className = 'fa-solid ' + (f.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file-image') + ' sp-green';
      row.appendChild(i);
      row.appendChild(document.createTextNode(' ' + f.name + ' (' + (f.size / 1024).toFixed(1) + (FR_KB) + ')'));
      box.appendChild(row);
    });
  });
})();
</script>
<?php require __DIR__ . '/layout-footer.php'; ?>
