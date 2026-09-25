<?php
/**
 * OCSAPP Buyer Central - marketing landing page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "Buyer Central content update and standardization"
 * package: transparent-pricing surcharge disclosure (oversize/stop/long-distance),
 * Founding Buyer Program (first 200 orders free delivery + $5 referral credit),
 * tip messaging, expanded FAQ, About-page-style icon set.
 */
use App\Helpers\VisitorTracker;
VisitorTracker::track();

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr-CA' : 'en-CA' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "Acheteur Central - Magasinez sur OCSAPP" : "Buyer Central - Shop on OCSAPP" ?></title>
  <meta name="description" content="<?= $fr
    ? "Magasinez local sur OCSAPP : compte gratuit pour toujours, prix du vendeur sans majoration, un seul frais de livraison divulgué avant le paiement."
    : "Shop local on OCSAPP: free-forever account, the seller's exact price with no markup, one disclosed delivery fee shown before checkout." ?>">
  <?= seo_lang_links() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/buyer-central.css') ?>">
</head>
<body class="buyer-central-page<?= $fr ? ' lang-fr' : '' ?>">
<div class="eco-beta">
  <span class="eco-beta-badge"><?= $fr ? 'Bêta' : 'Beta' ?></span>
  <span class="eco-beta-full"><?= $fr
      ? 'Plateforme en cours de développement. Certaines fonctionnalités ne sont pas encore disponibles.'
      : 'Platform under development. Some features are not yet available.'
  ?></span>
  <a href="<?= url('waitlist') ?>"><?= $fr ? "Rejoindre la liste d'attente" : 'Join the waitlist' ?></a>
</div>

<header class="eco-header">
  <div class="eco-wrap eco-header-inner">
    <a class="eco-brand" href="<?= url('') ?>" aria-label="<?= $fr ? 'OCSAPP - Accueil' : 'OCSAPP - Home' ?>">
      <img src="<?= asset('images/logo.png') ?>" alt="Logo OCSAPP">
      <span class="eco-brand-text">OCSAPP</span>
    </a>

    <nav aria-label="<?= $fr ? 'Navigation principale' : 'Main navigation' ?>">
      <a class="eco-nav-link" href="<?= url('') ?>#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
      <a class="eco-nav-link" href="<?= url('') ?>#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
      <a class="eco-nav-link" href="<?= url('') ?>#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
      <a class="eco-nav-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
      <div class="eco-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
        <a href="<?= lang_switch_url('fr') ?>" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="<?= lang_switch_url('en') ?>" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
      </div>
      <a class="eco-btn eco-btn-secondary" href="<?= url('login') ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> <?= $fr ? 'Se connecter' : 'Sign in' ?></a>
      <a class="eco-btn eco-btn-primary eco-header-join" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
      <button type="button" class="eco-mobile-toggle" id="navToggle" aria-label="Menu" aria-expanded="false" aria-controls="mobileMenu">
        <i class="fa-solid fa-bars"></i>
      </button>
    </nav>
  </div>
  <div class="eco-wrap">
    <div class="eco-mobile-menu" id="mobileMenu">
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#ecosysteme"><?= $fr ? 'Écosystème' : 'Ecosystem' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#centrales"><?= $fr ? 'Nos Centrales' : 'Our Centrals' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('') ?>#fonctionnement"><?= $fr ? 'Comment ça fonctionne' : 'How it works' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('about') ?>"><?= $fr ? 'À propos' : 'About' ?></a>
      <a class="eco-mobile-menu-link" href="<?= url('login') ?>"><?= $fr ? 'Se connecter' : 'Sign in' ?></a>
      <div class="eco-mobile-menu-lang" aria-label="<?= $fr ? 'Langue' : 'Language' ?>">
        <a href="<?= lang_switch_url('fr') ?>" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="<?= lang_switch_url('en') ?>" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
      </div>
      <a class="eco-btn eco-btn-primary" style="width:100%" href="<?= url('waitlist') ?>"><?= $fr ? 'Rejoindre OCSAPP' : 'Join OCSAPP' ?></a>
    </div>
  </div>
</header>

<div class="beta-strip"><p><?= $fr
  ? "⚠️ Version bêta - veuillez ne pas effectuer d'achats réels pour le moment"
  : "⚠️ Beta version - please do not make real purchases at this time" ?></p></div>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <span class="eyebrow"><?= $fr ? "ACHETEUR CENTRAL" : "BUYER CENTRAL" ?></span>
    <h1><?= $fr ? "Magasinez local, <span>livré chez vous</span> par OCSAPP" : "Shop local, <span>delivered to you</span> by OCSAPP" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Explorez des produits locaux, en pleine croissance dans le West Island, avec Laval et le centre-ville de Montréal à venir bientôt. Commandez en ligne, suivez votre livraison en temps réel, et recevez vos achats à domicile - sans effort."
      : "Explore local products, growing in the West Island, with Laval and downtown Montreal coming soon. Order online, track your delivery in real time, and receive your purchases at home - effortlessly." ?></p>
    <div class="hero-actions">
      <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
        <a class="btn" href="<?= url('marketplace-central') ?>"><?= $fr ? "Aller au Marketplace" : "Go to Marketplace" ?></a>
      <?php else: ?>
        <a class="btn" href="<?= url('register') ?>"><?= $fr ? "Créer mon compte - c'est gratuit →" : "Create My Account - It's Free →" ?></a>
        <a class="btn-secondary" href="<?= url('buyer/login') ?>"><?= $fr ? "Connexion acheteur" : "Buyer Login" ?></a>
      <?php endif; ?>
    </div>
    <div class="hero-proof-row cols-4">
      <div class="hero-proof-item"><strong><?= $fr ? "0 $" : "$0" ?></strong><span><?= $fr ? "compte gratuit, pour toujours" : "account, free forever" ?></span></div>
      <div class="hero-proof-item"><strong>15–30 min</strong><span><?= $fr ? "livraison estimée" : "estimated delivery" ?></span></div>
      <div class="hero-proof-item"><strong>100%</strong><span><?= $fr ? "objectif zéro émission" : "zero-emission goal" ?></span></div>
      <div class="hero-proof-item"><strong><?= $fr ? "Ouest-de-l'Île" : "West Island" ?></strong><span><?= $fr ? "zone active, Laval &amp; Montréal à venir" : "active zone, Laval &amp; Montreal coming" ?></span></div>
    </div>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p><?= $fr
      ? "Vous payez <span>le prix affiché par le vendeur, sans majoration</span> - plus, seulement si vous choisissez la livraison, un seul frais de zone divulgué avant le paiement."
      : "You pay <span>the price the seller lists, with no markup</span> - plus, only if you choose delivery, one disclosed zone fee shown before checkout." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Magasiner local n'a jamais été aussi simple." : "Shopping local has never been simpler." ?></h2>
    <p><?= $fr
      ? "OCSAPP est un écosystème numérique tout-en-un québécois qui connecte les acheteurs aux boutiques locales et aux fournisseurs via un réseau de livraison hyperlocal à objectif zéro émission. Épicerie fraîche, articles ménagers, mode et bien plus - tout au même endroit, livré depuis votre quartier."
      : "OCSAPP is an all-in-one Quebec digital ecosystem connecting buyers to local shops and suppliers through a hyperlocal, zero-emission delivery network. Fresh groceries, household goods, fashion, and more - all in one place, delivered from your own neighbourhood." ?></p>
  </div>
</section>

<!-- WHY OCSAPP - 3 STEPS -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI OCSAPP" : "WHY OCSAPP" ?></div>
    <h2><?= $fr ? "Trois étapes entre vous et votre commande" : "Three steps between you and your order" ?></h2>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Parcourez des milliers de produits" : "Browse thousands of products" ?></h3>
        <p><?= $fr
          ? "Explorez le Marché Central - l'épicerie, la mode, la maison et les boutiques locales de votre région, tout au même endroit. Trouvez de la nourriture fraîche, des articles ménagers, des vêtements et bien plus encore."
          : "Explore Marketplace Central - groceries, fashion, home goods, and local shops in your area, all in one place. Find fresh food, household items, clothing, and much more." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Commandez en quelques clics" : "Order in a few clicks" ?></h3>
        <p><?= $fr
          ? "Ajoutez vos articles au panier, choisissez votre créneau de livraison et payez en toute sécurité en ligne. Votre commande est transmise instantanément à la boutique - aucun appel téléphonique requis."
          : "Add items to your cart, choose your delivery window, and pay securely online. Your order is sent instantly to the shop - no phone call required." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Reçu chez vous rapidement" : "Received at your door, fast" ?></h3>
        <p><?= $fr
          ? "Un livreur ODA récupère votre commande et la livre chez vous. Suivez chaque étape en temps réel sur votre téléphone - de la boutique à votre porte, avec un objectif zéro émission."
          : "An ODA driver picks up your order and delivers it to you. Track every step in real time on your phone - from the shop to your door, with a zero-emission goal." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- TRANSPARENCY VALUE PANEL -->
<div class="reveal value-panel">
  <h2><?= $fr ? "Magasiner sur OCSAPP, <span>en toute transparence.</span>" : "Shopping on OCSAPP, <span>fully transparent.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Ce que chaque acheteur peut tenir pour acquis, dès sa première commande." : "What every buyer can count on, from their very first order." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Compte gratuit, pour toujours - aucun palier, aucun frais d'inscription" : "Free account, forever - no tier, no signup cost" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Vous payez le prix exact du vendeur - la commission lui est facturée, jamais à vous" : "You pay the seller's exact price - commission is charged to them, never to you" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Un seul frais possible : la livraison - fixe par zone, affiché avant le paiement" : "Only one possible fee: delivery - fixed by zone, disclosed before checkout" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Ramassage en boutique disponible en tout temps - aucun frais de livraison si vous ne l'utilisez pas" : "In-store pickup always available - no delivery fee if you don't use it" ?></p></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "COMMENT ÇA MARCHE" : "HOW IT WORKS" ?></div>
    <h2><?= $fr ? "Prêt à magasiner en 3 étapes" : "Ready to shop in 3 steps" ?></h2>
    <p class="section-lead"><?= $fr ? "De votre inscription à votre première livraison - voici exactement à quoi vous attendre." : "From sign-up to your first delivery - here's exactly what to expect." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Créez votre compte" : "Create your account" ?></h3>
        <p><?= $fr
          ? "Inscrivez-vous gratuitement en quelques minutes. Renseignez votre adresse, choisissez vos préférences de livraison, et vous êtes prêt à magasiner."
          : "Sign up for free in minutes. Enter your address, set your delivery preferences, and you're ready to shop." ?></p>
        <span class="step-time">~2 <?= $fr ? "minutes" : "minutes" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Parcourez &amp; commandez" : "Browse &amp; order" ?></h3>
        <p><?= $fr
          ? "Trouvez les produits que vous voulez parmi les boutiques locales du Marché Central. Ajoutez au panier et passez à la caisse - livraison ou créneau programmé."
          : "Find the products you want from local shops on Marketplace Central. Add to cart and check out - delivery or a scheduled window." ?></p>
        <span class="step-time"><?= $fr ? "À votre rythme" : "At your pace" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Suivez &amp; recevez" : "Track &amp; receive" ?></h3>
        <p><?= $fr
          ? "Suivez votre livreur ODA en temps réel jusqu'à votre porte. Recevez une notification à chaque étape. Aucune surprise - juste votre commande, livrée comme promis."
          : "Track your ODA driver in real time right to your door. Get a notification at every step. No surprises - just your order, delivered as promised." ?></p>
        <span class="step-time">15–30 min</span>
      </article>
    </div>
  </div>
</section>

<!-- WHAT YOU GET -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "VOTRE EXPÉRIENCE D'ACHAT" : "YOUR SHOPPING EXPERIENCE" ?></div>
    <h2><?= $fr ? "Tout ce dont vous avez besoin, livré." : "Everything you need, delivered." ?></h2>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc1" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><path d="M12 25h40v26H12z" fill="url(#bc1)"/><path d="M8 25l7-14h34l7 14z" fill="#00B207"/><path d="M18 32h10v19H18zM35 32h11v9H35z" fill="#fff"/></svg></span>
        <h3><?= $fr ? "Épicerie fraîche &amp; quotidien" : "Fresh groceries &amp; everyday essentials" ?></h3>
        <p><?= $fr
          ? "Faites votre épicerie sur le Marché Central - des produits frais, biologiques et locaux livrés directement chez vous. Programmez vos achats hebdomadaires ou commandez à la demande."
          : "Do your grocery shopping on Marketplace Central - fresh, organic, and local products delivered right to you. Schedule your weekly shop or order on demand." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc2" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M13 20h38l-4 31H17z" fill="url(#bc2)"/><path d="M22 20a10 10 0 0 1 20 0" fill="none" stroke="#dffff0" stroke-width="3"/></svg></span>
        <h3><?= $fr ? "Toutes vos boutiques locales" : "Every local shop, one place" ?></h3>
        <p><?= $fr
          ? "Parcourez le Marché Central pour magasiner des produits variés de vos boutiques locales préférées - vêtements, articles ménagers, beauté et plus encore, le tout depuis votre téléphone."
          : "Browse Marketplace Central to shop a variety of products from your favourite local shops - clothing, household goods, beauty, and more, all from your phone." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc3" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1fe528"/><stop offset="1" stop-color="#08730c"/></linearGradient></defs><path d="M8 24h31v20H8z" fill="url(#bc3)"/><path d="M39 29h10l7 8v7H39z" fill="#0b8d10"/><circle cx="20" cy="47" r="6" fill="#082d0a"/><circle cx="47" cy="47" r="6" fill="#082d0a"/></svg></span>
        <h3><?= $fr ? "Suivi en temps réel" : "Real-time tracking" ?></h3>
        <p><?= $fr
          ? "Suivez votre livreur ODA sur une carte en direct dès que votre commande est en route. Sachez exactement quand elle arrivera."
          : "Track your ODA driver on a live map as soon as your order is on the way. Know exactly when it'll arrive." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc4" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#076e0a"/></linearGradient></defs><path d="M32 8c10 0 18 8 18 18 0 13-18 30-18 30S14 39 14 26C14 16 22 8 32 8z" fill="url(#bc4)"/><circle cx="32" cy="26" r="7" fill="#f5fff5"/></svg></span>
        <h3><?= $fr ? "Livraison programmée" : "Scheduled delivery" ?></h3>
        <p><?= $fr
          ? "Choisissez un créneau de livraison qui vous convient - ce soir, demain matin ou le week-end. OCSAPP s'adapte à votre emploi du temps."
          : "Choose a delivery window that works for you - tonight, tomorrow morning, or the weekend. OCSAPP fits your schedule." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc5" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#20e528"/><stop offset="1" stop-color="#076d0a"/></linearGradient></defs><rect x="9" y="17" width="46" height="32" rx="7" fill="url(#bc5)"/><rect x="14" y="23" width="36" height="7" rx="3.5" fill="#f2fff3"/></svg></span>
        <h3><?= $fr ? "Livraison éco-responsable" : "Eco-friendly delivery" ?></h3>
        <p><?= $fr
          ? "Chaque livraison s'inscrit dans notre objectif zéro émission au Québec. Magasinez local, réduisez votre empreinte carbone - sans effort supplémentaire de votre part."
          : "Every delivery is part of our zero-emission goal for Quebec. Shop local, reduce your carbon footprint - no extra effort on your part." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc6" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M10 15h44v30H31l-11 8v-8H10z" fill="url(#bc6)"/><circle cx="23" cy="30" r="3" fill="#fff"/><circle cx="32" cy="30" r="3" fill="#fff"/><circle cx="41" cy="30" r="3" fill="#fff"/></svg></span>
        <h3><?= $fr ? "Paiement sécurisé" : "Secure payment" ?></h3>
        <p><?= $fr
          ? "Payez en toute sécurité avec carte de crédit, débit ou PayPal, traité via Stripe. Vos informations de paiement sont chiffrées et jamais stockées en clair sur nos serveurs."
          : "Pay securely with credit card, debit, or PayPal, processed through Stripe. Your payment information is encrypted and never stored in plain text on our servers." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- FEE TRANSPARENCY / TRUST -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Frais possibles - toujours affichés avant le paiement" : "Possible fees - always disclosed before checkout" ?></h3>
    <p><?= $fr
      ? "Votre coût total est le prix du produit, plus - seulement si vous choisissez la livraison - le frais de zone. Certaines commandes déclenchent un supplément additionnel, calculé automatiquement et affiché avant que vous confirmiez."
      : "Your total cost is the product price, plus - only if you choose delivery - the zone fee. Some orders trigger an additional surcharge, calculated automatically and shown before you confirm." ?></p>
    <div class="trust-grid trust-grid-3col">
      <div class="trust-card">
        <h4><?= $fr ? "Supplément commande volumineuse" : "Oversize order surcharge" ?></h4>
        <p><?= $fr
          ? "Au-delà de 15 kg au total dans le panier (jusqu'à 25 kg) : +4,00 $ / 4,50 $ / 5,00 $ selon la zone, plus un incrément de 2,00 $ / 2,25 $ / 2,50 $ par 10 kg additionnels (40 kg maximum pour livraison standard)."
          : "Above 15 kg total in the cart (up to 25 kg): +$4.00 / $4.50 / $5.00 by zone, plus a $2.00 / $2.25 / $2.50 increment per additional 10 kg (40 kg maximum for standard delivery)." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Frais d'arrêt additionnel" : "Additional-stop fee" ?></h4>
        <p><?= $fr
          ? "Lorsque votre panier regroupe plus de 2 boutiques, un frais de 2,00 $ / 2,25 $ / 2,50 $ s'applique par boutique additionnelle au-delà des deux premières."
          : "When your cart draws from more than 2 shops, a $2.00 / $2.25 / $2.50 fee applies per additional shop beyond the first two." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément longue distance" : "Long-distance surcharge" ?></h4>
        <p><?= $fr
          ? "Au-delà de 8 km de livraison (jusqu'à 12 km) : +4,00 $ / 4,50 $ / 5,00 $, plus un incrément de 2,00 $ / 2,25 $ / 2,50 $ par 4 km additionnels (20 km maximum pour livraison standard)."
          : "Beyond 8 km of delivery (up to 12 km): +$4.00 / $4.50 / $5.00, plus a $2.00 / $2.25 / $2.50 increment per additional 4 km (20 km maximum for standard delivery)." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Poids : jamais à estimer vous-même" : "Weight: never yours to estimate" ?></h4>
        <p><?= $fr
          ? "Le poids provient de la fiche produit déclarée par chaque vendeur, additionné automatiquement à la caisse. Vous n'avez jamais à estimer quoi que ce soit."
          : "Weight comes from the product listing each seller declares, summed automatically at checkout. You never have to estimate anything." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Toujours affiché avant confirmation" : "Always shown before you confirm" ?></h4>
        <p><?= $fr
          ? "Chaque supplément applicable est calculé et affiché à la caisse, avant que vous confirmiez votre commande - jamais une surprise sur votre reçu."
          : "Every applicable surcharge is calculated and displayed at checkout, before you confirm your order - never a surprise on your receipt." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Ramassage : aucun de ces frais" : "Pickup: none of these fees" ?></h4>
        <p><?= $fr
          ? "Si vous choisissez le ramassage en boutique plutôt que la livraison, aucun frais de zone ni supplément ne s'applique - jamais."
          : "If you choose in-store pickup instead of delivery, no zone fee or surcharge ever applies." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- TRANSPARENT PRICING -->
<section class="pricing">
  <div class="wrap">
    <div class="pricing-head">
      <div class="section-eyebrow"><?= $fr ? "TARIFICATION TRANSPARENTE" : "TRANSPARENT PRICING" ?></div>
      <h2><?= $fr ? "Ce que vous payez, sans surprise" : "What you pay, no surprises" ?></h2>
      <p><?= $fr
        ? "Votre compte est gratuit, pour toujours. Vous payez le prix affiché par le vendeur, plus - seulement si vous choisissez la livraison - un seul frais de zone divulgué avant le paiement."
        : "Your account is free, forever. You pay the price the seller lists, plus - only if you choose delivery - one disclosed zone fee shown before checkout." ?></p>
    </div>
    <div class="requirements-box">
      <h4><?= $fr ? "Le seul frais possible : la livraison" : "The only possible fee: delivery" ?></h4>
      <ul>
        <li><?= $fr ? "Compte / abonnement : 0 $ - aucun palier, aucun frais d'inscription" : "Account / subscription: $0 - no tier, no signup cost" ?></li>
        <li><?= $fr ? "Majoration sur le prix du produit : 0 $ - vous payez le prix exact du vendeur" : "Markup on product price: $0 - you pay the seller's exact price" ?></li>
        <li><?= $fr ? "Ramassage en boutique : 0 $ - aucun service de livraison utilisé, aucun frais" : "In-store pickup: $0 - no delivery service used, no fee" ?></li>
        <li><?= $fr ? "Frais de traitement de paiement : aucun - absorbé par le vendeur, jamais facturé séparément (interdit au Québec)" : "Payment processing fee: none - absorbed by the seller, never charged separately (prohibited in Quebec)" ?></li>
        <li><?= $fr ? "Livraison Ouest-de-l'Île : 7,99 $ - en service" : "West Island delivery: $7.99 - active" ?></li>
        <li><?= $fr ? "Livraison Laval : 8,99 $ - bientôt disponible" : "Laval delivery: $8.99 - coming soon" ?></li>
        <li><?= $fr ? "Livraison Montréal (centre) : 9,99 $ - bientôt disponible" : "Downtown Montreal delivery: $9.99 - coming soon" ?></li>
      </ul>
    </div>
    <p class="pricing-note" style="text-align:center;"><?= $fr
      ? "Frais fixe par zone, affiché avant le paiement - jamais calculé de façon algorithmique, jamais de majoration selon la demande. Comparé à la concurrence, le frais de livraison d'OCSAPP se situe dans la moyenne du marché, mais reste parmi les plus prévisibles."
      : "A fixed, zone-based fee, shown before checkout - never algorithmic, never surge-priced. Compared to competitors, OCSAPP's delivery fee sits mid-pack on price, but stays among the most predictable." ?></p>
  </div>
</section>

<!-- TIPS -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURBOIRES" : "TIPS" ?></div>
    <h2><?= $fr ? "100 % du pourboire, sans exception" : "100% of every tip, no exceptions" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Conçu pour être lancé avant le début des opérations physiques :" : "Designed to launch before physical operations begin:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr ? "100 % de tout pourboire que vous laissez revient directement à votre livreur - OCSAPP ne prend jamais de commission sur un pourboire." : "100% of any tip you leave goes directly to your driver - OCSAPP never takes a cut of a tip." ?></li>
        <li><?= $fr ? "Les montants suggérés (15 %, 18 %, 20 % ou personnalisé) seront calculés sur le sous-total avant taxes, tous affichés avec la même importance visuelle." : "Suggested amounts (15%, 18%, 20%, or custom) will be calculated on the pre-tax subtotal, all displayed with equal visual weight." ?></li>
        <li><?= $fr ? "Cette fonctionnalité est confirmée pour le lancement mais n'est pas encore active dans l'application - vous pourrez laisser un pourboire dès son lancement, prévu avant le début des opérations physiques." : "This feature is confirmed for launch but is not yet active in the app - you'll be able to leave a tip once it ships, ahead of physical operations beginning." ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- WELCOME BONUS -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "PROGRAMME ACHETEUR FONDATEUR" : "FOUNDING BUYER PROGRAM" ?></div>
    <h2><?= $fr ? "Votre première commande, livrée gratuitement" : "Your First Order, Delivered Free" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Aucun code, aucune demande à faire :" : "No code, nothing to request:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr
          ? "Si vous faites partie des 200 premiers comptes acheteurs à passer une commande avec livraison admissible, votre première livraison est gratuite - OCSAPP absorbe le frais de zone standard en votre nom, automatiquement."
          : "If you're among the first 200 buyer accounts to place an eligible delivery order, your first delivery is free - OCSAPP covers the standard zone fee on your behalf, automatically." ?></li>
        <li><?= $fr
          ? "Le supplément pour commande volumineuse et les frais d'arrêt additionnel s'appliquent toujours à cette première commande si elle y est admissible - ce bonus annule uniquement le frais de livraison standard."
          : "The oversize order surcharge and additional-stop fee still apply to this first order if it qualifies - this bonus waives the standard delivery fee only." ?></li>
        <li><?= $fr
          ? "Séparément, et offert à tous les acheteurs peu importe ce bonus : parrainez un ami qui complète sa première commande, et vous recevez tous les deux un crédit de 5 $ vers un futur frais de livraison - sans limite sur le nombre d'amis que vous pouvez parrainer."
          : "Separately, and available to every buyer regardless of this bonus: refer a friend who completes their first order, and you both receive a $5 credit toward a future delivery fee - with no limit on how many friends you can refer." ?></li>
      </ul>
      <p style="margin:14px 0 0;font-weight:600;"><a href="<?= url('founding') ?>#buyer" style="color:#00b207;"><?= $fr ? 'Voir tous les programmes fondateurs et les places restantes' : 'See all founding programs and spots remaining' ?> &rarr;</a></p>
    </div>
  </div>
</section>

<!-- CATEGORIES + SOCIAL PROOF -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "CE QUE VOUS POUVEZ COMMANDER" : "WHAT YOU CAN ORDER" ?></div>
    <h2><?= $fr ? "Des produits dans toutes les catégories" : "Products across every category" ?></h2>
    <p class="section-lead"><?= $fr
      ? "OCSAPP rassemble les boutiques locales du Québec dans toutes les catégories - de l'épicerie fraîche aux articles de mode, le tout livré depuis votre quartier."
      : "OCSAPP brings together local Quebec shops across all categories - from fresh groceries to fashion, all delivered from your own neighbourhood." ?></p>

    <div class="category-chips">
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-restauration.png') ?>"></span><?= $fr ? "Restauration" : "Food &amp; Dining" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-epicerie.png') ?>"></span><?= $fr ? "Épicerie" : "Grocery" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-sante.png') ?>"></span><?= $fr ? "Santé &amp; pharmacie" : "Health &amp; Pharmacy" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-mode.png') ?>"></span><?= $fr ? "Mode &amp; boutiques" : "Fashion &amp; Boutiques" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-wellness-beauty.png') ?>"></span><?= $fr ? "Bien-être &amp; beauté" : "Wellness &amp; Beauty" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-evenements.png') ?>"></span><?= $fr ? "Événements &amp; traiteur" : "Events &amp; Catering" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-artisans.png') ?>"></span><?= $fr ? "Artisans locaux" : "Local Artisans" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-automotive.png') ?>"></span><?= $fr ? "Pièces auto &amp; industrielles" : "Auto &amp; Industrial Parts" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-maison.png') ?>"></span><?= $fr ? "Maison &amp; quotidien" : "Home &amp; Everyday" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-electronics.png') ?>"></span><?= $fr ? "Électronique &amp; technologie" : "Electronics &amp; Tech" ?></span>
      <span class="category-chip"><span class="category-icon-3d"><img alt="" src="<?= asset('images/about/chip-saveurs.png') ?>"></span><?= $fr ? "Saveurs du monde" : "World Flavors" ?></span>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="wrap">
    <div class="faq-head">
      <div class="section-eyebrow">FAQ</div>
      <h2><?= $fr ? "Questions fréquentes" : "Frequently Asked Questions" ?></h2>
      <p><?= $fr ? "Tout ce que vous devez savoir avant de passer votre première commande." : "Everything you need to know before placing your first order." ?></p>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <h4><?= $fr ? "Est-ce que c'est gratuit de créer un compte acheteur ?" : "Is it free to create a buyer account?" ?></h4>
        <p><?= $fr
          ? "Oui, entièrement gratuit. La création d'un compte acheteur OCSAPP ne coûte rien. Vous ne payez que les produits que vous achetez, plus les frais de livraison affichés au moment de la commande."
          : "Yes, entirely free. Creating an OCSAPP buyer account costs nothing. You only pay for the products you buy, plus the delivery fee shown at checkout." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Dans quelles zones livrez-vous ?" : "What zones do you deliver to?" ?></h4>
        <p><?= $fr
          ? "OCSAPP livre actuellement dans le West Island. Laval et le centre-ville de Montréal sont publiés et s'activeront au fur et à mesure de notre expansion. Les zones de livraison disponibles s'affichent automatiquement lors de la saisie de votre adresse."
          : "OCSAPP currently delivers in the West Island. Laval and downtown Montreal are published and will activate as we expand. Available delivery zones show automatically when you enter your address." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment puis-je suivre ma commande ?" : "How can I track my order?" ?></h4>
        <p><?= $fr
          ? "Dès qu'un livreur ODA prend en charge votre commande, vous recevez un lien de suivi en temps réel. Vous pouvez suivre la progression sur une carte directement depuis votre téléphone ou votre navigateur."
          : "As soon as an ODA driver takes your order, you receive a real-time tracking link. You can follow progress on a map right from your phone or browser." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je programmer une livraison à l'avance ?" : "Can I schedule a delivery in advance?" ?></h4>
        <p><?= $fr
          ? "Oui. Lors du passage en caisse, vous pouvez choisir une livraison immédiate ou sélectionner un créneau programmé - ce soir, demain ou plus tard dans la semaine, selon la disponibilité."
          : "Yes. At checkout, you can choose immediate delivery or select a scheduled window - tonight, tomorrow, or later in the week, depending on availability." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je annuler ma commande ?" : "Can I cancel my order?" ?></h4>
        <p><?= $fr
          ? "Oui, tant qu'elle est au statut « Commande passée » ou « En traitement ». Une fois votre commande marquée « Prête pour ramassage » ou « En livraison », l'annulation pourrait ne plus être possible - contactez notre support immédiatement et nous ferons de notre mieux pour vous aider."
          : "Yes, as long as it's in \"Order Placed\" or \"Processing\" status. Once your order is marked \"Ready for Pickup\" or \"Out for Delivery,\" cancellation may no longer be possible - contact support immediately and we'll do our best to help." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Que faire si un article de ma commande est manquant ou incorrect ?" : "What if an item in my order is missing or incorrect?" ?></h4>
        <p><?= $fr
          ? "Contactez notre équipe de support à info@ocsapp.ca ou par téléphone au 514-746-3789 (Lun–Dim, 7h–23h) dans les 14 jours suivant la livraison. Nous traitons toutes les réclamations rapidement. Incluez votre numéro de commande pour un traitement plus rapide."
          : "Contact our support team at info@ocsapp.ca or by phone at 514-746-3789 (Mon–Sun, 7am–11pm) within 14 days of delivery. We handle all claims quickly. Include your order number for faster processing." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Quels modes de paiement acceptez-vous ?" : "What payment methods do you accept?" ?></h4>
        <p><?= $fr
          ? "OCSAPP accepte les cartes de crédit (Visa, Mastercard), les cartes de débit et PayPal. Tous les paiements sont traités de façon sécurisée via Stripe. Vos informations de paiement ne sont jamais stockées en clair sur nos serveurs."
          : "OCSAPP accepts credit cards (Visa, Mastercard), debit cards, and PayPal. All payments are processed securely through Stripe. Your payment information is never stored in plain text on our servers." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment fonctionnent les retours et remboursements ?" : "How do returns and refunds work?" ?></h4>
        <p><?= $fr
          ? "Les retours sont couverts par la Politique de retours et remboursements d'OCSAPP. Si votre réclamation est admissible, OCSAPP utilise une preuve photo et de balayage - prise à la fois lors de la préparation par le vendeur et lors de la livraison - pour déterminer automatiquement si un problème est survenu avant la cueillette ou pendant le transport, et résout le dossier en conséquence (remplacement, crédit ou remboursement). Vous pouvez soumettre une réclamation directement depuis votre compte ou en contactant le support."
          : "Returns are covered by OCSAPP's Returns &amp; Refund Policy. If your claim is eligible, OCSAPP uses photo and scan evidence - captured both when the seller prepares your order and at delivery - to automatically determine whether an issue occurred before pickup or in transit, and resolves it accordingly (replacement, credit, or refund). You can submit a claim directly from your account or by contacting support." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Les suppléments affectent-ils le prix affiché par le vendeur ?" : "Do surcharges affect the seller's listed price?" ?></h4>
        <p><?= $fr
          ? "Non. Le supplément pour commande volumineuse, les frais d'arrêt additionnel et le supplément longue distance s'ajoutent uniquement au frais de livraison - jamais au prix du produit lui-même, qui reste toujours le prix exact affiché par le vendeur."
          : "No. The oversize order surcharge, additional-stop fee, and long-distance surcharge are added only to the delivery fee - never to the product price itself, which always stays the seller's exact listed price." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SUPPORT -->
<section class="support">
  <div class="wrap">
    <div class="support-grid">
      <div class="support-card">
        <h4><?= $fr ? "Support acheteur" : "Buyer Support" ?></h4>
        <p class="support-main"><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        <p><?= $fr ? "Commandes, livraisons &amp; comptes" : "Orders, deliveries &amp; accounts" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Téléphone" : "Phone" ?></h4>
        <p class="support-main">514-746-3789</p>
        <p><?= $fr ? "Lun–Dim · 7h – 23h" : "Mon–Sun · 7am – 11pm" ?></p>
      </div>
      <div class="support-card">
        <h4>Marketplace</h4>
        <p class="support-main">ocsapp.ca</p>
        <p><?= $fr ? "Commencez à magasiner" : "Start shopping" ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="wrap">
    <h2><?= $fr ? "Prêt à magasiner local ?" : "Ready to shop local?" ?></h2>
    <p><?= $fr ? "Créez votre compte en 2 minutes. Gratuit pour toujours. Livraison rapide dès votre première commande." : "Create your account in 2 minutes. Free forever. Fast delivery from your very first order." ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('register') ?>"><?= $fr ? "Créer mon compte gratuit" : "Create My Free Account" ?></a>
      <a class="btn-secondary" href="<?= url('marketplace-central') ?>"><?= $fr ? "Parcourir le marketplace" : "Browse the Marketplace" ?></a>
    </div>
  </div>
</section>

<footer class="mc-footer">
  <div class="mc-footer-wrap">
    <div class="mc-footer-top">
      <div class="mc-footer-brand-col">
        <div class="mc-footer-brand">
          <img src="<?= asset('images/logo.png') ?>" alt="<?= $fr ? 'Logo OCSAPP' : 'OCSAPP Logo' ?>">
          <span class="mc-footer-logo-text">OCSAPP</span>
        </div>
        <p class="mc-footer-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
        <p><?= $fr
          ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
          : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
        ?></p>
        <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
        <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
        <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
        <a href="<?= url('marketplace-central') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
        <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
        <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
        <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
        <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
        <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
      </div>

      <div class="mc-footer-col">
        <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
        <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
        <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
        <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
      </div>
    </div>

    <div class="mc-footer-bottom">
      <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
      <div class="mc-footer-legal">
        <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
        <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
        <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
        <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
        <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
      </div>
    </div>
  </div>
</footer>

<script>
(function(){
  var navToggle = document.getElementById('navToggle');
  var mobileMenu = document.getElementById('mobileMenu');
  if (navToggle && mobileMenu) {
    navToggle.addEventListener('click', function(){
      var open = mobileMenu.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      navToggle.innerHTML = open ? '<i class="fa-solid fa-xmark"></i>' : '<i class="fa-solid fa-bars"></i>';
    });
    mobileMenu.querySelectorAll('a').forEach(function(link){
      link.addEventListener('click', function(){
        mobileMenu.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
        navToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
      });
    });
  }
})();
</script>
</body>
</html>
