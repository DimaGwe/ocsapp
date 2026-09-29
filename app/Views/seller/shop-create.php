<?php
/**
 * Create a shop (/seller/shop/create, ShopController::create) for a seller account with no shop.
 * Updated 2026-09-28: now inside the seller portal shell (was a standalone English page on the old
 * header/footer), bilingual EN/FR. Same form (POST seller/shop/store).
 */
$pageTitle = 'Create Shop';
require __DIR__ . '/layout-header.php';
?>
<section class="sp-card" style="max-width:680px">
  <div class="sp-card-head"><h2><?= $fr ? 'Créer votre commerce' : 'Create your shop' ?></h2></div>
  <p class="sp-muted" style="margin:-6px 0 18px"><?= $fr
    ? "Remplissez les renseignements ci-dessous. L'équipe OCSAPP examinera votre commerce avant sa mise en ligne."
    : 'Fill in the details below. The OCSAPP team reviews your shop before it goes live.' ?></p>
  <form class="sp-form" method="POST" action="<?= url('seller/shop/store') ?>">
    <?= csrfField() ?>
    <label><?= $fr ? 'Nom du commerce' : 'Shop name' ?> *<input type="text" name="name" required placeholder="<?= $fr ? 'ex. Marché des produits frais' : 'e.g. Fresh Produce Market' ?>"></label>
    <label><?= $fr ? 'Description' : 'Description' ?><textarea name="description" placeholder="<?= $fr ? 'Présentez votre commerce à vos clients…' : 'Tell customers about your shop…' ?>"></textarea></label>
    <div class="sp-form-row">
      <label><?= $fr ? 'Téléphone' : 'Phone' ?><input type="tel" name="phone" placeholder="514 000-0000"></label>
      <label><?= $fr ? 'Adresse' : 'Address' ?><input type="text" name="address"></label>
    </div>
    <div><button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-check"></i> <?= $fr ? "Soumettre pour approbation" : 'Submit for approval' ?></button></div>
  </form>
</section>
<?php require __DIR__ . '/layout-footer.php'; ?>
