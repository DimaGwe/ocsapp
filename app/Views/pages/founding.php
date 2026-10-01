<?php
/**
 * Founding programs overview: /founding (FR /programmes-fondateurs)
 * All five cohorts on one page, with live spots remaining from the founding_*_program counters.
 *
 * Layout from Dima's "Founding Page HTML - Founder's Badge EN & FR" mockup (2026-09-28): hero with the
 * Founder badge orbited by the five Central icons, one card per program, beta steps, info boxes.
 * Step 2 of the join flow: every "Become a founder" CTA lands here, each card's main button goes to
 * the waitlist with that role pre-selected.
 *
 * Wording mirrors the LIVE founding sections on each Central page (the source of truth);
 * unbuilt perks stay marked "coming soon". No legal section numbers. No em dashes. FR is fr-CA.
 * $programs is built by PageController::founding().
 *
 * $quarter (Founding Quarter sections: quarter band, live meters on the cards, Founders' Wall of founders who
 * consented, "just joined" toast) comes from PageController::founding(): ['founders' => [...], 'end' => ISO or null].
 * Sections live in partials/founding-quarter.php, styles in css/pages/founding-quarter.css.
 */
use App\Helpers\VisitorTracker;
VisitorTracker::track();

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$L  = fn(array $pair) => $fr ? $pair[0] : $pair[1];
$betaOn = !\App\Helpers\BetaAccessHelper::isOpen();
$quarter = $quarter ?? null;

$content = [
    'buyer' => [
        'for'   => ['Pour les acheteurs', 'For buyers'],
        'name'  => ['Acheteur fondateur', 'Founding Buyer'],
        'who'   => ['Les 200 premiers comptes acheteurs à passer et à payer une commande avec livraison admissible', 'The first 200 buyer accounts to place and pay for an eligible delivery order'],
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
        'for'   => ['Pour les commerçants', 'For merchants'],
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
        'for'   => ['Pour les fournisseurs', 'For suppliers'],
        'name'  => ['Fournisseur fondateur', 'Founding Supplier'],
        'who'   => ['Les 15 premiers fournisseurs activés', 'The first 15 activated suppliers'],
        'perks' => [
            ['Taux Prestige verrouillé 6 mois : commission de 5 %, 0 $ de frais mensuels (normalement 79 $/mois).', 'Prestige rate locked for 6 months: 5% commission, $0 monthly fee (normally $79/month).'],
            ['Configuration de catalogue gratuite et un insigne Partenaire fondateur permanent.', 'Free catalog setup and a permanent Founding Partner badge.'],
            ["Prime d'étape de 150 $ : exécutez 10 bons de commande dans vos 30 premiers jours.", '$150 milestone bonus: fulfill 10 purchase orders in your first 30 days.'],
            ['Prime de parrainage de 75 $ pour vous et pour chaque fournisseur recommandé qui exécute 5 bons de commande dans ses 30 premiers jours.', '$75 referral bonus for you and for each supplier you refer who fulfills 5 purchase orders in their first 30 days.'],
        ],
        'soon'  => null,
        'note'  => null,
        'how'   => ["Automatique à l'activation de votre compte.", 'Automatic when your account is activated.'],
        'central' => ['supplier-central', 'Fournisseur Central', 'Supplier Central'],
    ],
    'driver' => [
        'for'   => ['Pour les livreurs', 'For drivers'],
        'name'  => ['Livreur fondateur', 'Founding Driver'],
        'who'   => ['Les 50 premiers livreurs approuvés', 'The first 50 approved drivers'],
        'perks' => [
            ['Un insigne Livreur fondateur permanent sur votre profil.', 'A permanent Founding Driver badge on your profile.'],
            ["Prime d'étape de 100 $ : complétez 20 livraisons dans vos 30 premiers jours.", '$100 milestone bonus: complete 20 deliveries in your first 30 days.'],
            ['Prime de parrainage de 50 $ pour vous et pour chaque livreur parrainé qui complète 15 livraisons dans ses 30 premiers jours.', '$50 referral bonus for you and for each driver you refer who completes 15 deliveries in their first 30 days.'],
            ['Répartition prioritaire : les nouvelles commandes vous sont offertes en premier.', 'Priority dispatch: new orders are offered to you first.'],
            ["Une trousse d'équipement OCSAPP.", 'An OCSAPP equipment kit.'],
        ],
        'soon'  => null,
        'note'  => null,
        'how'   => ["Automatique à l'approbation de votre candidature.", 'Automatic when your application is approved.'],
        'central' => ['driver-central', 'Livreur Central · ODA', 'Driver Central · ODA'],
    ],
    'business' => [
        'for'   => ['Pour les organisations', 'For organizations'],
        'name'  => ['Entreprise fondatrice', 'Founding Business'],
        'who'   => ['Les 5 premiers comptes entreprise approuvés', 'The first 5 approved business accounts'],
        'perks' => [
            ['Taux Distribution Débutant verrouillé 6 mois : 5 %, 0 $ de frais mensuels (normalement 49 $/mois).', 'Distribution Starter rate locked for 6 months: 5%, $0 monthly fee (normally $49/month).'],
            ['Un gestionnaire de compte dédié dès le premier jour, normalement réservé aux paliers supérieurs.', 'A dedicated account manager from day one, normally reserved for higher tiers.'],
            ['Routes de livraison récurrentes incluses pendant vos 6 mois fondateurs.', 'Recurring delivery routes included during your 6 Founding months.'],
            ["Aucuns frais d'Approvisionnement sur vos premiers 10 000 $ de volume.", 'No Procurement fee on your first $10,000 of volume.'],
        ],
        'soon'  => null,
        'note'  => null,
        'how'   => ["Automatique à l'approbation de votre compte.", 'Automatic when your account is approved.'],
        'central' => ['distribution', 'Entreprise Centrale', 'Business Central'],
    ],
];
// Hero orbit, clockwise from the top (same order as the mockup)
$orbit = ['seller', 'supplier', 'business', 'driver', 'buyer'];
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
  <?= seo_lang_links() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/founding.css') ?>">
  <?php if ($quarter): ?><link rel="stylesheet" href="<?= asset('css/pages/founding-quarter.css') ?>"><?php endif; ?>
</head>
<body class="founding-page<?= $fr ? ' lang-fr' : '' ?>">

<?php $navLinks = [
    [url(''), $fr ? 'Écosystème' : 'Ecosystem'],
    [url('marketplace-central'), $fr ? 'Marché Central' : 'Marketplace Central'],
]; require __DIR__ . '/partials/eco-page-header.php'; ?>

<main class="fd-main">

<!-- HERO -->
<section class="fd-hero">
  <div class="fd-wrap fd-hero-grid">
    <div>
      <div class="fd-eyebrow"><?= $fr ? 'UN ÉCOSYSTÈME · MEMBRES FONDATEURS' : 'ONE ECOSYSTEM · FOUNDING MEMBERS' ?></div>
      <h1><?= $fr ? 'Programmes fondateurs.<br>Cinq façons de rejoindre OCSAPP dès le départ.' : 'Founding programs.<br>Five ways to join early.' ?></h1>
      <p><?= $fr
        ? "Les programmes fondateurs d'OCSAPP relient acheteurs, vendeurs, fournisseurs, entreprises et livreurs à la même infrastructure de commerce local, avec une cohorte fondatrice limitée pour chaque rôle participant."
        : "OCSAPP's founding programs connect buyers, sellers, suppliers, businesses and drivers to the same local commerce infrastructure, with a limited founding cohort for each participating role." ?></p>
      <div class="fd-hero-actions">
        <a class="fd-btn fd-btn-green" href="#programs"><?= $fr ? 'Explorer les programmes fondateurs' : 'Explore founding programs' ?></a>
        <a class="fd-btn fd-btn-outline" href="<?= url('') ?>"><?= $fr ? "Explorer l'écosystème" : 'Explore the ecosystem' ?></a>
      </div>
    </div>
    <div class="fd-art">
      <div class="fd-orbit" aria-hidden="true">
        <?php foreach ($orbit as $i => $role): ?>
        <div class="fd-dot fd-d<?= $i + 1 ?>"><img src="<?= asset('images/about/central-' . $role . '.png') ?>" alt=""></div>
        <?php endforeach; ?>
      </div>
      <div class="fd-core">
        <img src="<?= asset('images/founding/founder-badge-' . ($fr ? 'fr' : 'en') . '.webp') ?>" alt="<?= $fr ? 'Insigne Fondateur OCSAPP' : 'OCSAPP Founder badge' ?>" width="220" height="220">
      </div>
    </div>
  </div>
</section>

<?php if ($quarter) { $fqSection = 'band'; require __DIR__ . '/partials/founding-quarter.php'; } ?>

<!-- INTRO -->
<section class="fd-wrap fd-intro" id="programs">
  <div class="fd-eyebrow"><?= $fr ? "VOTRE POINT D'ENTRÉE" : 'YOUR POINT OF ENTRY' ?></div>
  <h2><?= $fr ? 'Un écosystème. Un parcours fondateur pour chaque rôle.' : 'One ecosystem. A founding path for each role.' ?></h2>
  <p><?= $fr
    ? "Chaque programme a son propre déclencheur d'admissibilité et ses propres avantages, tout en étant relié à la Centrale conçue pour ce rôle. Le statut fondateur est automatique tant que la cohorte visée a encore des places."
    : 'Each program keeps its own eligibility trigger and benefits while connecting to the Central built for that role. Founding status is automatic while the applicable cohort still has space.' ?></p>
  <div class="fd-stats">
    <div class="fd-stat"><strong>5</strong> <?= $fr ? 'programmes fondateurs' : 'founding programs' ?></div>
    <div class="fd-stat"><strong>1</strong> <?= $fr ? 'infrastructure commune' : 'shared infrastructure' ?></div>
    <div class="fd-stat"><strong>6</strong> <?= $fr ? 'Centrales connectées' : 'connected Centrals' ?></div>
  </div>
</section>

<!-- PROGRAM CARDS -->
<section class="fd-wrap fd-cards">
  <?php $n = 0; foreach ($content as $role => $c): $n++; $p = $programs[$role]; $full = $p['remaining'] <= 0; ?>
  <article class="fd-card<?= $full ? ' fd-full' : '' ?>" id="<?= $role ?>">
    <div class="fd-card-art"><img src="<?= asset('images/about/central-' . $role . '.png') ?>" alt="<?= htmlspecialchars($fr ? 'Icône ' . $c['central'][1] : $c['central'][2] . ' icon') ?>"></div>
    <div class="fd-card-copy">
      <div class="fd-index"><?= sprintf('%02d', $n) ?></div>
      <div class="fd-role"><?= htmlspecialchars($L($c['for'])) ?></div>
      <h2><?= htmlspecialchars($fr ? $c['central'][1] : $c['central'][2]) ?></h2>
      <h3><?= htmlspecialchars($L($c['name'])) ?></h3>
      <span class="fd-pill<?= $full ? ' fd-pill-full' : '' ?>">
        <?php if ($full): ?>
          <?= $fr ? 'Complet' : 'Full' ?>
        <?php else: ?>
          <?php if ($quarter): ?><strong class="fq-count" data-from="<?= (int) $p['total'] ?>" data-to="<?= (int) $p['remaining'] ?>"><?= (int) $p['remaining'] ?></strong><?php else: ?><?= (int) $p['remaining'] ?><?php endif; ?> / <?= (int) $p['total'] ?> <?= $fr ? 'places restantes' : 'spots left' ?>
        <?php endif; ?>
      </span>
      <?php if ($quarter): $fqLow = !$full && $p['remaining'] <= max(3, $p['total'] * 0.25); ?>
      <div class="fq-card-meter<?= $fqLow ? ' fq-low' : '' ?>">
        <div class="fq-bar"><span style="width:<?= round(100 * ($p['total'] - $p['remaining']) / max(1, $p['total'])) ?>%"></span></div>
        <span><?= $fqLow
          ? '<i class="fa-solid fa-fire"></i> ' . ($fr ? 'Presque complet' : 'Almost full')
          : ($p['total'] - $p['remaining']) . ' ' . ($fr ? 'fondateurs' : 'founders') ?></span>
      </div>
      <?php endif; ?>
      <p class="fd-eligibility"><?= htmlspecialchars($L($c['who'])) ?></p>
      <ul>
        <?php foreach ($c['perks'] as $perk): ?>
        <li><span class="fd-tick">✓</span><span><?= htmlspecialchars($L($perk)) ?></span></li>
        <?php endforeach; ?>
      </ul>
      <?php if ($c['note']): ?>
      <div class="fd-note"><?= htmlspecialchars($L($c['note'])) ?></div>
      <?php endif; ?>
      <?php if ($c['soon']): ?>
      <div class="fd-note"><strong><?= $fr ? 'Bientôt :' : 'Coming soon:' ?></strong> <?= htmlspecialchars($L($c['soon'])) ?></div>
      <?php endif; ?>
      <p class="fd-automatic"><span>✓</span><?= htmlspecialchars($L($c['how'])) ?></p>
      <div class="fd-actions">
        <?php // Step 2 of the join flow: pick a program, land on the waitlist with that role selected ?>
        <a class="fd-primary<?= $full ? ' fd-primary-muted' : '' ?>" href="<?= url('waitlist?role=' . $role) ?>"><?= htmlspecialchars($full
          ? ($fr ? "Rejoindre la liste d'attente" : 'Join the waitlist')
          : ($fr ? 'Devenir ' . $L($c['name']) : 'Become a ' . $L($c['name']))) ?> →</a>
        <a class="fd-secondary" href="<?= url($c['central'][0]) ?>"><?= htmlspecialchars($fr ? 'Découvrir ' . $c['central'][1] : 'Discover ' . $c['central'][2]) ?></a>
      </div>
    </div>
  </article>
  <?php endforeach; ?>
</section>

<?php if ($quarter) { $fqSection = 'wall'; require __DIR__ . '/partials/founding-quarter.php'; } ?>

<!-- HOW TO GET IT -->
<section class="fd-flow">
  <div class="fd-wrap fd-flow-grid">
    <div>
      <div class="fd-eyebrow"><?= $fr ? 'COMMENT EN PROFITER' : 'HOW TO GET IT' ?></div>
      <h2><?= $betaOn
        ? ($fr ? 'Pendant la période bêta.' : 'During the beta.')
        : ($fr ? 'Il suffit de vous inscrire.' : 'Just sign up.') ?></h2>
      <p><?= $betaOn
        ? ($fr ? "La création de compte se fait sur invitation. Choisissez votre programme ci-dessus et inscrivez-vous à la liste d'attente; lorsque nous vous invitons et que votre compte est approuvé, votre place fondatrice est confirmée automatiquement s'il en reste."
               : "Accounts are by invitation. Choose your program above and join the waitlist; once we invite you and your account is approved, your founding spot is confirmed automatically if any remain.")
        : ($fr ? "Créez votre compte : dès qu'il est approuvé, votre place fondatrice est confirmée automatiquement s'il en reste."
               : 'Create your account: once it is approved, your founding spot is confirmed automatically if any remain.') ?></p>
      <a class="fd-btn fd-btn-green" href="#programs"><?= $fr ? 'Choisir mon programme' : 'Choose my program' ?></a>
      <p class="fd-partner"><?= $fr ? 'Votre rôle ne figure pas ci-dessus?' : "Don't see your role above?" ?>
        <a href="<?= url('waitlist?role=partner') ?>"><?= $fr ? "Rejoignez la liste d'attente comme partenaire" : 'Join the waitlist as a partner' ?> →</a></p>
    </div>
    <div class="fd-steps">
      <div class="fd-step"><b>01</b>
        <h3><?= $fr ? 'Choisissez votre programme' : 'Choose your program' ?></h3>
        <p><?= $fr
          ? "Choisissez le programme fondateur qui correspond à votre façon de participer à OCSAPP, puis inscrivez-vous à la liste d'attente."
          : 'Pick the founding program that fits how you want to take part in OCSAPP, then join the waitlist.' ?></p></div>
      <div class="fd-step"><b>02</b>
        <h3><?= $fr ? "Complétez l'accueil" : 'Complete onboarding' ?></h3>
        <p><?= $fr
          ? "Suivez le parcours d'accueil de votre rôle : Acheteur, Vendeur, Fournisseur, Entreprise ou Livreur Central."
          : 'Follow the onboarding path for your Buyer, Seller, Supplier, Business or Driver Central role.' ?></p></div>
      <div class="fd-step"><b>03</b>
        <h3><?= $fr ? 'Statut fondateur' : 'Founding status' ?></h3>
        <p><?= $fr
          ? "Une fois l'approbation ou l'action admissible complétée, le statut fondateur est automatique s'il reste des places."
          : 'Once the applicable approval or qualifying action is complete, founding status is automatic if spots remain.' ?></p></div>
    </div>
  </div>
</section>

<!-- INFO -->
<section class="fd-security">
  <div class="fd-wrap fd-security-box">
    <div class="fd-info">
      <h3><?= $fr ? 'Une architecture OCSAPP commune' : 'One shared OCSAPP architecture' ?></h3>
      <p><?= $fr
        ? "Les programmes fondateurs font partie du même écosystème que Marché Central, Vendeur Central, Fournisseur Central, Acheteur Central, Entreprise Centrale et Livreur Central · ODA. Ce n'est pas un produit d'authentification distinct."
        : 'Founding programs are part of the same ecosystem as Marketplace Central, Seller Central, Supplier Central, Buyer Central, Business Central and Driver Central · ODA, not a separate authentication product.' ?></p>
    </div>
    <div class="fd-info">
      <h3><?= $fr ? 'Compte et sécurité' : 'Account & security' ?></h3>
      <p><?= $fr
        ? "Utilisez les pages officielles d'accueil et de connexion d'OCSAPP pour soumettre vos renseignements de compte ou d'entreprise. N'inscrivez jamais de mot de passe ni de numéro de carte de paiement dans les champs de texte libre de l'accueil."
        : 'Use official OCSAPP onboarding and sign-in pages when submitting account or business information. Never place passwords or payment-card details in free-text onboarding fields.' ?></p>
    </div>
  </div>
</section>

</main>

<?php require __DIR__ . '/partials/eco-page-footer.php'; ?>
<?php if ($quarter) { $fqSection = 'scripts'; require __DIR__ . '/partials/founding-quarter.php'; } ?>
</body>
</html>
