<?php
/**
 * Founding programs overview: /founding
 * All five cohorts on one page, with live spots remaining from the founding_*_program counters.
 *
 * Wording mirrors the LIVE founding sections on each Central page (the source of truth);
 * unbuilt perks stay marked "coming soon". No legal section numbers. No em dashes. FR is fr-CA.
 * $programs is built by PageController::founding().
 */
use App\Helpers\VisitorTracker;
VisitorTracker::track();

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$L  = fn(array $pair) => $fr ? $pair[0] : $pair[1];
$betaOn = !\App\Helpers\BetaAccessHelper::isOpen();

$content = [
    'buyer' => [
        'name'  => ['Acheteur fondateur', 'Founding Buyer'],
        'who'   => ['Les 200 premiers comptes acheteurs à passer une commande avec livraison admissible', 'The first 200 buyer accounts to place an eligible delivery order'],
        'perks' => [
            ["Votre première livraison est gratuite : OCSAPP absorbe le frais de zone standard, automatiquement.", 'Your first delivery is free: OCSAPP covers the standard zone fee, automatically.'],
            ["Les suppléments (commande volumineuse, arrêt additionnel) s'appliquent toujours à cette commande si elle y est admissible.", 'Surcharges (oversize order, additional stop) still apply to that order if it qualifies.'],
        ],
        'soon'  => null,
        'note'  => ["Offert à tous les acheteurs, fondateurs ou non : parrainez un ami qui complète sa première commande et recevez tous les deux un crédit de 5 $ sur un futur frais de livraison.", 'Open to every buyer, founding or not: refer a friend who completes their first order and you both get a $5 credit toward a future delivery fee.'],
        'how'   => ['Automatique à votre première commande avec livraison admissible.', 'Automatic on your first eligible delivery order.'],
        'central' => ['buyer-central', 'Acheteur Central', 'Buyer Central'],
    ],
    'seller' => [
        'name'  => ['Vendeur fondateur', 'Founding Seller'],
        'who'   => ['Les 20 premières boutiques approuvées', 'The first 20 approved shops'],
        'perks' => [
            ['Taux Expérience verrouillé 12 mois : 12 % livraison / 6 % cueillette, 0 $ de frais mensuels (normalement 39 $/mois).', 'Experience rate locked for 12 months: 12% delivery / 6% pickup, $0 monthly fee (normally $39/month).'],
            ['Vos 5 premières commandes livrées sans commission.', 'Your first 5 delivery orders commission-free.'],
            ['3 mois de placement en vedette, une intégration personnalisée et un insigne Partenaire fondateur permanent.', '3 months of featured placement, personalized onboarding and a permanent Founding Partner badge.'],
        ],
        'soon'  => null,
        'note'  => ['À la fin des 12 mois, la boutique passe au forfait Essentiel (15 % / 8 %). Vous pouvez ensuite changer de forfait en tout temps.', 'When the 12 months end, the shop moves to the Essential plan (15% / 8%). You can switch plans at any time after that.'],
        'how'   => ["Automatique à l'approbation de votre boutique.", 'Automatic when your shop is approved.'],
        'central' => ['seller-central', 'Vendeur Central', 'Seller Central'],
    ],
    'supplier' => [
        'name'  => ['Fournisseur fondateur', 'Founding Supplier'],
        'who'   => ['Les 15 premiers fournisseurs activés', 'The first 15 activated suppliers'],
        'perks' => [
            ['Taux Prestige verrouillé 6 mois : commission de 5 %, 0 $ de frais mensuels (normalement 79 $/mois).', 'Prestige rate locked for 6 months: 5% commission, $0 monthly fee (normally $79/month).'],
            ['Configuration de catalogue gratuite et un insigne Partenaire fondateur permanent.', 'Free catalog setup and a permanent Founding Partner badge.'],
        ],
        'soon'  => null,
        'note'  => null,
        'how'   => ["Automatique à l'activation de votre compte.", 'Automatic when your account is activated.'],
        'central' => ['supplier-central', 'Fournisseur Central', 'Supplier Central'],
    ],
    'driver' => [
        'name'  => ['Livreur fondateur', 'Founding Driver'],
        'who'   => ['Les 50 premiers livreurs approuvés', 'The first 50 approved drivers'],
        'perks' => [
            ['Un insigne Livreur fondateur permanent sur votre profil.', 'A permanent Founding Driver badge on your profile.'],
        ],
        'soon'  => ["Une prime d'étape, une prime de parrainage et un accès prioritaire à la répartition, en plus du partage standard 70/30. Ils seront annoncés une fois lancés.", 'A milestone bonus, a referral bonus and priority dispatch access, on top of the standard 70/30 split. They will be announced once they launch.'],
        'note'  => null,
        'how'   => ["Automatique à l'approbation de votre candidature.", 'Automatic when your application is approved.'],
        'central' => ['driver-central', 'Livreur Central', 'Driver Central'],
    ],
    'business' => [
        'name'  => ['Entreprise fondatrice', 'Founding Business'],
        'who'   => ['Les 5 premiers comptes entreprise approuvés', 'The first 5 approved business accounts'],
        'perks' => [
            ['Taux Distribution Débutant verrouillé 6 mois : 5 %, 0 $ de frais mensuels (normalement 49 $/mois).', 'Distribution Starter rate locked for 6 months: 5%, $0 monthly fee (normally $49/month).'],
            ['Un gestionnaire de compte dédié dès le premier jour, normalement réservé aux paliers supérieurs.', 'A dedicated account manager from day one, normally reserved for higher tiers.'],
        ],
        'soon'  => ["Une exemption des frais d'Approvisionnement sur vos premiers 10 000 $ de volume. Elle sera annoncée une fois lancée.", 'A Procurement fee waiver on your first $10,000 of volume. It will be announced once it launches.'],
        'note'  => null,
        'how'   => ["Automatique à l'approbation de votre compte.", 'Automatic when your account is approved.'],
        'central' => ['distribution', 'Entreprise Centrale', 'Business Central'],
    ],
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Programmes fondateurs' : 'Founding programs' ?> | OCSAPP</title>
  <meta name="description" content="<?= $fr
    ? "Les programmes fondateurs d'OCSAPP pour les acheteurs, vendeurs, fournisseurs, livreurs et entreprises : avantages, places restantes et admissibilité."
    : "OCSAPP's founding programs for buyers, sellers, suppliers, drivers and businesses: perks, spots remaining and eligibility." ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/buyer-central.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/pages/founding.css') ?>">
</head>
<body class="buyer-central-page founding-page<?= $fr ? ' lang-fr' : '' ?>">

<?php $navLinks = [[url('waitlist'), $fr ? "Liste d'attente" : 'Waitlist']]; require __DIR__ . '/partials/eco-page-header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <span class="eyebrow"><?= $fr ? 'PROGRAMMES FONDATEURS' : 'FOUNDING PROGRAMS' ?></span>
    <h1><?= $fr ? 'Rejoignez les premiers membres d\'OCSAPP' : "Be one of OCSAPP's first members" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Chaque rôle a son programme fondateur, avec un nombre de places limité. Aucune demande à faire : le statut est accordé automatiquement tant que la cohorte n'est pas complète."
      : "Every role has its own founding program, with a limited number of spots. Nothing to apply for: status is granted automatically as long as the cohort isn't full." ?></p>
  </div>
</section>

<!-- PROGRAMS -->
<section class="card-section">
  <div class="wrap">
    <div class="fd-grid">
      <?php foreach ($content as $role => $c): $p = $programs[$role]; $full = $p['remaining'] <= 0; ?>
      <article class="fd-card<?= $full ? ' fd-full' : '' ?>" id="<?= $role ?>">
        <div class="fd-card-head">
          <h2><?= htmlspecialchars($L($c['name'])) ?></h2>
          <span class="fd-spots<?= $full ? ' fd-spots-full' : '' ?>">
            <?php if ($full): ?>
              <?= $fr ? 'Complet' : 'Full' ?>
            <?php else: ?>
              <?= (int) $p['remaining'] ?> / <?= (int) $p['total'] ?> <?= $fr ? 'places restantes' : 'spots left' ?>
            <?php endif; ?>
          </span>
        </div>
        <div class="fd-bar" aria-hidden="true"><span style="width:<?= $p['total'] > 0 ? round(100 * ($p['total'] - $p['remaining']) / $p['total']) : 0 ?>%"></span></div>
        <p class="fd-who"><?= htmlspecialchars($L($c['who'])) ?></p>
        <ul class="fd-perks">
          <?php foreach ($c['perks'] as $perk): ?>
          <li><i class="fa-solid fa-check"></i><span><?= htmlspecialchars($L($perk)) ?></span></li>
          <?php endforeach; ?>
        </ul>
        <?php if ($c['soon']): ?>
        <p class="fd-soon"><strong><?= $fr ? 'Bientôt :' : 'Coming soon:' ?></strong> <?= htmlspecialchars($L($c['soon'])) ?></p>
        <?php endif; ?>
        <?php if ($c['note']): ?>
        <p class="fd-note"><?= htmlspecialchars($L($c['note'])) ?></p>
        <?php endif; ?>
        <p class="fd-how"><i class="fa-regular fa-circle-check"></i> <?= htmlspecialchars($L($c['how'])) ?></p>
        <div class="fd-links">
          <a href="<?= url($c['central'][0]) ?>"><?= $fr ? $c['central'][1] : $c['central'][2] ?> <i class="fa-solid fa-arrow-right"></i></a>
          <a href="<?= url('onboarding/' . $role) ?>"><?= $fr ? "Guide d'accueil" : 'Onboarding guide' ?></a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- HOW IT WORKS DURING BETA -->
<section class="card-section fd-soft">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? 'COMMENT EN PROFITER' : 'HOW TO GET IT' ?></div>
    <h2><?= $betaOn
      ? ($fr ? 'Pendant la période bêta' : 'During the beta')
      : ($fr ? 'Il suffit de vous inscrire' : 'Just sign up') ?></h2>
    <p class="fd-steps-lead"><?= $betaOn
      ? ($fr ? "La création de compte se fait sur invitation. Rejoignez la liste d'attente; lorsque nous vous invitons et que votre compte est approuvé, votre place fondatrice est confirmée automatiquement s'il en reste."
             : "Accounts are by invitation. Join the waitlist; once we invite you and your account is approved, your founding spot is confirmed automatically if any remain.")
      : ($fr ? "Créez votre compte : dès qu'il est approuvé, votre place fondatrice est confirmée automatiquement s'il en reste."
             : 'Create your account: once it is approved, your founding spot is confirmed automatically if any remain.') ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/partials/eco-page-footer.php'; ?>
</body>
</html>
