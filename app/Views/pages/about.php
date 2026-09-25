<?php
/**
 * OCSAPP About Us Page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "About Page Final" package (Updates.zip):
 * ecosystem/six-Centrals positioning + "Where your data lives" disclosure.
 * Updated 2026-09-09: moved onto the eco-header/mc-footer standard shared by
 * the five Central landing pages, "Driver Central" -> "Driver Central . ODA",
 * softened zero-emission wording to match the site-wide "objective" phrasing,
 * added the Marketplace Central category chips row.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "À propos d'OCSAPP - Achetez local, livré durablement" : "About OCSAPP - Shop Smart, Delivered Sustainably" ?></title>
  <meta name="description" content="<?= $fr
    ? "OCSAPP est une plateforme bilingue de commerce numérique et de logistique, née et bâtie au Québec - un écosystème, six Centrales connectées."
    : "OCSAPP is a bilingual digital commerce and logistics platform, born and built in Québec - one ecosystem, six connected Centrals." ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/about.css') ?>">
</head>
<body class="about-page<?= $fr ? ' lang-fr' : '' ?>">
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
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
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
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>" aria-current="<?= $fr ? 'page' : 'false' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>" aria-current="<?= !$fr ? 'page' : 'false' ?>">EN</a>
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
    <span class="eyebrow"><?= $fr ? "QUÉBEC · BILINGUE · OBJECTIF ZÉRO ÉMISSION" : "QUEBEC · BILINGUAL · ZERO-EMISSION OBJECTIVE" ?></span>
    <h1><?= $fr ? "Achetez local, livré <span>durablement</span>" : "Shop smart, delivered <span>sustainably</span>" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Un écosystème. Six Centrales connectées. Commerce local, approvisionnement, outils d’affaires, acheteurs et livraison - réunis dans un seul système."
      : "One ecosystem. Six connected Centrals. Local commerce, sourcing, business tools, buyers and delivery - working as one system." ?></p>
    <a class="btn" href="#services"><?= $fr ? "Découvrir l’écosystème OCSAPP →" : "Explore the OCSAPP ecosystem →" ?></a>
    <div class="hero-proof-row">
      <div class="hero-proof-item"><strong>6</strong><span><?= $fr ? "Centrales connectées" : "connected Centrals" ?></span></div>
      <div class="hero-proof-item"><strong>1</strong><span><?= $fr ? "écosystème local partagé" : "shared local ecosystem" ?></span></div>
      <div class="hero-proof-item"><strong>100%</strong><span><?= $fr ? "tarifs affichés avant engagement" : "rates shown before commitment" ?></span></div>
    </div>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p class="positioning-copy"><?= $fr
      ? "La <span>seule</span> plateforme québécoise qui vous indique votre tarif avant que vous disiez oui - sans qu’un algorithme décide à votre place."
      : "The <span>only</span> Québec platform that tells you your rate before you say yes - not an algorithm deciding for you." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Le commerce local devrait être connecté, pas fragmenté." : "Local commerce should feel connected, not fragmented." ?></h2>
    <p><?= $fr
      ? "OCSAPP est une plateforme bilingue de commerce numérique et de logistique, née et bâtie au Québec. Nous lançons d’abord dans l’Ouest-de-l’Île, puis bientôt à Laval et dans le cœur du Grand Montréal - en reliant commerces locaux, fournisseurs, entreprises et livreurs dans un même écosystème pour simplifier l’achat local, l’approvisionnement et la livraison durable."
      : "OCSAPP is a bilingual digital commerce and logistics platform, born and built in Québec. We're launching in the West Island, with Laval and the Greater Montreal core coming soon - connecting local shops, suppliers, businesses, and drivers on one ecosystem, so it's simple to shop local, source smarter, and deliver sustainably." ?></p>
  </div>
</section>

<!-- WHY OCSAPP -->
<section class="why-ocsapp">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI OCSAPP" : "WHY OCSAPP" ?></div>
    <h2><?= $fr ? "Une infrastructure. Différents rôles. Une valeur partagée." : "One infrastructure. Different roles. Shared value." ?></h2>
    <p class="section-lead"><?= $fr
      ? "Les meilleures plateformes de commerce rendent le système simple pour l’utilisateur. OCSAPP applique cette approche localement en reliant chaque rôle à une même couche de tarification, de logistique et de commerce."
      : "The strongest commerce platforms make the system feel simple from the outside. OCSAPP does that locally by connecting each role to the same pricing, logistics and commerce layer." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Un seul système connecté" : "One connected system" ?></h3>
        <p><?= $fr
          ? "Les expériences Marché, Vendeur, Fournisseur, Entreprise, Acheteur et Livreur sont conçues pour fonctionner ensemble - et non comme des outils séparés assemblés après coup."
          : "Marketplace, seller, supplier, business, buyer and driver experiences are designed to work together - not as separate tools stitched together." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Les entreprises locales restent visibles" : "Local businesses stay visible" ?></h3>
        <p><?= $fr
          ? "L’expérience est organisée autour de la découverte locale et des besoins concrets des entreprises, afin que les commerces indépendants et les fournisseurs soient plus faciles à trouver et à soutenir."
          : "The experience is organized around local discovery and practical business needs, so independent shops and suppliers are easier to find and support." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Tarification claire + livraison" : "Clear economics + delivery" ?></h3>
        <p><?= $fr
          ? "La tarification et la livraison font partie du même modèle d’exploitation, avec des tarifs divulgués avant tout engagement et la livraison OCSAPP intégrée au réseau."
          : "Pricing and delivery are presented as part of the same operating model, with rates disclosed before users commit and OCSAPP delivery underneath the network." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- VALUE PANEL -->
<div class="value-panel">
  <h2><?= $fr ? "Le commerce local, <span>réinventé.</span>" : "Local commerce, <span>reinvented.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Un réseau de livraison ayant pour objectif de devenir zéro émission partout où nous sommes présents." : "A delivery network with the objective of becoming zero-emission wherever we operate." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Magasinez gratuitement - aucuns frais de compte" : "Free to shop - no account fees, ever" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Vendeurs locaux de confiance, vérifiés avant leur mise en ligne" : "Trusted local sellers, verified before they go live" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Objectif de livraison zéro émission pour chaque commande" : "A delivery model working toward a zero-emission objective for every order" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Bâti et exploité au Québec" : "Built and operated in Québec" ?></p></div>
  </div>
</div>

<!-- SERVICES / SIX CENTRALS -->
<section class="services" id="services">
  <div class="wrap">
    <div class="services-head">
      <h2><?= $fr ? "Un écosystème, six façons d’y entrer" : "One ecosystem, six ways in" ?></h2>
      <p><?= $fr
        ? "Peu importe la porte qui vous convient, la même tarification fixe et un réseau de livraison visant l'objectif zéro émission se trouvent derrière."
        : "Whichever door fits you, the same fixed pricing and a delivery network working toward a zero-emission objective are underneath it." ?></p>
      <p class="service-architecture-note"><?= $fr
        ? "Commencez avec le rôle qui vous convient aujourd’hui. Le reste de l’écosystème est déjà connecté lorsque vous en avez besoin."
        : "Start with the role that fits you today. The rest of the ecosystem is already connected when you need it." ?></p>
    </div>

    <div class="ecosystem-flow">
      <div class="flow-kicker"><?= $fr ? "UN SEUL SYSTÈME, PAS SIX SILOS" : "ONE SYSTEM, NOT SIX SILOS" ?></div>
      <h3 class="flow-title"><?= $fr ? "Chaque rôle se connecte à la même infrastructure OCSAPP." : "Every role connects through the same OCSAPP infrastructure." ?></h3>
      <div class="flow-row">
        <span class="flow-badge"><?= $fr ? "Vendeur Central" : "Seller Central" ?></span>
        <span class="flow-badge"><?= $fr ? "Fournisseur Central" : "Supplier Central" ?></span>
        <span class="flow-badge"><?= $fr ? "Entreprise Centrale" : "Business Central" ?></span>
      </div>
      <div class="flow-arrow-down">↓</div>
      <div class="flow-row"><span class="flow-badge flow-badge-hub"><?= $fr ? "Marché Central" : "Marketplace Central" ?></span></div>
      <div class="flow-arrow-down">↓</div>
      <div class="flow-row"><span class="flow-badge"><?= $fr ? "Acheteur Central" : "Buyer Central" ?></span></div>
      <div class="flow-arrow-down">↓</div>
      <div class="flow-row"><span class="flow-badge flow-badge-livreur"><?= $fr
        ? "Livreur Central · ODA - la couche de livraison derrière toutes les commandes ci-dessus"
        : "Driver Central · ODA - the delivery layer underneath every order above" ?></span></div>
      <p class="flow-explainer"><?= $fr
        ? "OCSAPP n’est pas un assemblage de six applications distinctes - c’est un seul système connecté. Les vendeurs affichent leurs produits dans le <strong>Vendeur Central</strong>, les fournisseurs proposent leurs produits en gros dans le <strong>Fournisseur Central</strong>, et les entreprises s’approvisionnent ou distribuent par l’<strong>Entreprise Centrale</strong> - le tout se retrouve dans le <strong>Marché Central</strong>, où les acheteurs magasinent avec l’<strong>Acheteur Central</strong>. Et chacune de ces commandes passe par le <strong>Livreur Central · ODA</strong>, notre propre réseau de livreurs, avec l'objectif de progresser vers une livraison zéro émission. Les mêmes principes de tarification, les mêmes livreurs et la même plateforme, peu importe votre point d’entrée."
        : "OCSAPP isn't six separate apps stitched together - it's one connected system. Sellers list on <strong>Seller Central</strong>, suppliers list wholesale goods on <strong>Supplier Central</strong>, and businesses source or distribute through <strong>Business Central</strong> - all of it surfaces on <strong>Marketplace Central</strong>, where buyers shop through <strong>Buyer Central</strong>. And every one of those orders moves through <strong>Driver Central · ODA</strong>, our own driver network, working toward a zero-emission objective. Same pricing principles, same drivers, same platform, however you come in." ?></p>
    </div>

    <!-- 01 Marketplace Central -->
    <div class="service-card">
      <div class="service-index">01</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Marché Central" : "Marketplace Central" ?>" class="service-icon-img" src="<?= asset('images/about/central-marketplace.png') ?>">
        <div class="service-tag"><?= $fr ? "Marché Central" : "Marketplace Central" ?></div>
      </div>
      <h3><?= $fr ? "Votre marché local, livré en quelques minutes" : "Your local marketplace, delivered in minutes" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Commerces locaux, aire de restauration et restaurants populaires, épiceries locales à bas prix"
        : "Local shops, food court and top restaurants, local groceries at low prices" ?></p>
      <p class="desc"><?= $fr
        ? "Nous développons le Marché Central un commerce local à la fois - organisée selon ce dont vous avez réellement besoin maintenant, plutôt que selon qu’il s’agisse techniquement d’un produit ou d’un service. Chaque commande est à prix fixe, affiché avant le paiement, avec livraison express par notre propre réseau de livreurs, avec l'objectif de progresser vers une livraison zéro émission."
        : "We're growing Marketplace Central one local shop at a time - organized by what you actually need right now, not by whether it's technically a product or a service. Every order is fixed-price and disclosed before you check out, with express delivery through our own drivers, working toward a zero-emission objective." ?></p>
      <div class="chips">
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-restauration.png') ?>" alt=""><?= $fr ? "Restauration" : "Food &amp; Dining" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-epicerie.png') ?>" alt=""><?= $fr ? "Épicerie" : "Grocery" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-sante.png') ?>" alt=""><?= $fr ? "Santé &amp; pharmacie" : "Health &amp; Pharmacy" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-mode.png') ?>" alt=""><?= $fr ? "Mode &amp; boutiques" : "Fashion &amp; Boutiques" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-wellness-beauty.png') ?>" alt=""><?= $fr ? "Bien-être &amp; beauté" : "Wellness &amp; Beauty" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-evenements.png') ?>" alt=""><?= $fr ? "Événements &amp; traiteur" : "Events &amp; Catering" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-artisans.png') ?>" alt=""><?= $fr ? "Artisans locaux" : "Local Artisans" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-automotive.png') ?>" alt=""><?= $fr ? "Pièces auto &amp; industrielles" : "Auto &amp; Industrial Parts" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-maison.png') ?>" alt=""><?= $fr ? "Maison &amp; quotidien" : "Home &amp; Everyday" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-electronics.png') ?>" alt=""><?= $fr ? "Électronique &amp; technologie" : "Electronics &amp; Tech" ?></span>
        <span class="chip"><img class="chip-icon" src="<?= asset('images/about/chip-saveurs.png') ?>" alt=""><?= $fr ? "Saveurs du monde" : "World Flavors" ?></span>
      </div>
      <a class="service-cta" href="<?= url('/shops') ?>"><?= $fr ? "Explorer le Marché Central →" : "Browse Marketplace Central →" ?></a>
    </div>

    <!-- 02 Seller Central -->
    <div class="service-card">
      <div class="service-index">02</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Vendeur Central" : "Seller Central" ?>" class="service-icon-img" src="<?= asset('images/about/central-seller.png') ?>">
        <div class="service-tag"><?= $fr ? "Vendeur Central" : "Seller Central" ?></div>
      </div>
      <h3><?= $fr ? "Affichez votre commerce, gardez une plus grande part de chaque vente" : "List your shop, keep more of every sale" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Joignez des acheteurs locaux, contrôlez votre inventaire et lancez votre vitrine en quelques minutes"
        : "Reach local buyers, control your own stock, launch a storefront in minutes" ?></p>
      <p class="desc"><?= $fr
        ? "Le Vendeur Central met votre vitrine en ligne en quelques minutes, vous donne un véritable contrôle sur les commandes et l’inventaire et vous aide à promouvoir votre marque auprès d’acheteurs qui magasinent déjà à proximité."
        : "Seller Central gets your storefront live in minutes, gives you real order and stock control, and helps you promote your brand to buyers already shopping nearby." ?></p>
      <p class="quote"><?= $fr
        ? "« Toutes les plateformes annoncent un petit chiffre. Nous sommes les seuls à vous montrer la facture complète avant votre inscription - commission, traitement et frais de livraison, tous séparés et clairement divulgués. »"
        : "\"Every platform advertises a low number. We're the only one that shows you the whole invoice before you sign up - commission, processing, and delivery fee, all separate, all disclosed.\"" ?></p>
      <a class="service-cta" href="<?= url('/seller-central') ?>"><?= $fr ? "Devenir vendeur fondateur →" : "Become a founding seller →" ?></a>
    </div>

    <!-- 03 Buyer Central -->
    <div class="service-card">
      <div class="service-index">03</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Acheteur Central" : "Buyer Central" ?>" class="service-icon-img" src="<?= asset('images/about/central-buyer.png') ?>">
        <div class="service-tag"><?= $fr ? "Acheteur Central" : "Buyer Central" ?></div>
      </div>
      <h3><?= $fr ? "Magasinez local et suivez chaque commande en temps réel" : "Shop local, track every order in real time" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Parcourez les produits locaux, choisissez la livraison rapide ou planifiée et profitez d’offres exclusives"
        : "Browse local products, ASAP or scheduled delivery, exclusive deals" ?></p>
      <p class="desc"><?= $fr
        ? "L’Acheteur Central est gratuit, avec suivi de commande en temps réel du paiement jusqu’à votre porte - ainsi que des offres réservées aux acheteurs OCSAPP."
        : "Buyer Central is free to use, with real-time order tracking from the moment you check out to the moment it's at your door - plus deals only available to OCSAPP buyers." ?></p>
      <p class="quote"><?= $fr
        ? "« Aucun frais caché. Aucun calcul surprise. Seulement le prix que vous avez vu. »"
        : "\"No hidden fees. No surprise math. Just the price you saw.\"" ?></p>
      <a class="service-cta" href="<?= url('/buyer-central') ?>"><?= $fr ? "Commencer à magasiner →" : "Start shopping →" ?></a>
    </div>

    <!-- 04 Supplier Central -->
    <div class="service-card">
      <div class="service-index">04</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Fournisseur Central" : "Supplier Central" ?>" class="service-icon-img" src="<?= asset('images/about/central-supplier.png') ?>">
        <div class="service-tag"><?= $fr ? "Fournisseur Central" : "Supplier Central" ?></div>
      </div>
      <h3><?= $fr ? "Un réseau de fournisseurs conçu pour le commerce de gros, pas seulement le détail" : "A supplier network built for wholesale, not just retail" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Tarification de gros flexible, traitement et suivi en temps réel"
        : "Flexible wholesale pricing, live fulfillment and tracking" ?></p>
      <p class="desc"><?= $fr
        ? "Si vous fournissez en gros, le Fournisseur Central vous relie directement au réseau d’entreprises OCSAPP - avec des forfaits flexibles, une commission transparente et aucune majoration cachée ajoutée à vos prix."
        : "If you supply in bulk, Supplier Central connects you directly to OCSAPP's business network - flexible plans, transparent commission, and no hidden markup added on top of what you charge." ?></p>
      <p class="quote"><?= $fr
        ? "« Une seule commission. Pas des frais de marché en plus d’un contrat de logistique. »"
        : "\"One commission. Not a marketplace fee and a logistics contract.\"" ?></p>
      <a class="service-cta" href="<?= url('/supplier-central') ?>"><?= $fr ? "Devenir fournisseur fondateur →" : "Become a founding supplier →" ?></a>
    </div>

    <!-- 05 Business Central -->
    <div class="service-card">
      <div class="service-index">05</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Entreprise Centrale" : "Business Central" ?>" class="service-icon-img" src="<?= asset('images/about/central-business.png') ?>">
        <div class="service-tag"><?= $fr ? "Entreprise Centrale" : "Business Central" ?></div>
      </div>
      <h3><?= $fr ? "Approvisionnement, distribution et solutions de bureau pour les entreprises en croissance" : "Procurement, distribution, and office solutions for growing businesses" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Approvisionnement et distribution, solutions de bureau et d’aire de repos, gestion de compte"
        : "Procurement &amp; distribution, office &amp; breakroom solutions, account management" ?></p>
      <p class="desc"><?= $fr
        ? "L’Entreprise Centrale regroupe plusieurs fournisseurs en une seule livraison avec des frais fixes de 1 % et aucune majoration, intègre vos propres expéditions à notre réseau calibré par zone et couvre l’approvisionnement du bureau et de l’aire de repos - avec des forfaits adaptés aux entreprises de toutes tailles."
        : "Business Central consolidates multiple suppliers into a single delivery at a flat 1% fee with zero markup, puts your own shipments on our zone-calibrated network, and covers office and breakroom sourcing - with plans built for businesses of every size." ?></p>
      <p class="quote"><?= $fr
        ? "« Accès complet à l’API, soutien logistique dédié, une seule facture - pas cinq fournisseurs qui prétendent former un seul système. »"
        : "\"Full API access, dedicated logistics support, one bill - not five vendors pretending to be one system.\"" ?></p>
      <a class="service-cta" href="<?= url('/distribution') ?>"><?= $fr ? "Obtenir un compte entreprise →" : "Get a business account →" ?></a>
    </div>

    <!-- 06 Driver Central -->
    <div class="service-card">
      <div class="service-index">06</div>
      <div class="service-top">
        <img alt="<?= $fr ? "Livreur Central · ODA" : "Driver Central · ODA" ?>" class="service-icon-img" src="<?= asset('images/about/central-driver.png') ?>">
        <div class="service-tag"><?= $fr ? "Livreur Central · ODA" : "Driver Central · ODA" ?></div>
      </div>
      <h3><?= $fr ? "Objectif de livraison zéro émission pour chaque commande" : "A zero-emission delivery objective, across every order" ?></h3>
      <p class="service-tagline"><?= $fr
        ? "Choisissez vos heures, revenus de base hebdomadaires, objectif zéro émission"
        : "Set your own hours, weekly base earnings, zero-emission goal" ?></p>
      <p class="desc"><?= $fr
        ? "Chaque livraison OCSAPP - détail, fournisseur ou entreprise - passe par notre propre réseau de livreurs travailleurs autonomes, avec l’objectif d’une flotte entièrement zéro ou faible émission. La rémunération est fixe et affichée avant l’acceptation, et 100 % de chaque pourboire va directement au livreur."
        : "Every OCSAPP delivery - retail, supplier, or business - runs through our own network of independent-contractor drivers, working toward a fully zero and low-emission fleet. Pay is fixed and shown before you accept, and 100% of every tip goes straight to the driver." ?></p>
      <p class="quote"><?= $fr
        ? "« Connaissez votre tarif avant de dire oui. Pas après, et pas selon la demande du moment. »"
        : "\"Know your rate before you say yes. Not after, not 'it depends on demand right now.'\"" ?></p>
      <a class="service-cta" href="<?= url('/driver-central') ?>"><?= $fr ? "Livrer avec OCSAPP →" : "Drive with OCSAPP →" ?></a>
    </div>
  </div>
</section>

<!-- EXPLORE -->
<section class="explore">
  <div class="wrap">
    <h2><?= $fr ? "Voyez par vous-même" : "See it for yourself" ?></h2>
    <p class="explore-sub"><?= $fr
      ? "Choisissez le rôle qui correspond à ce que vous voulez faire. Chaque parcours mène au même réseau local."
      : "Choose the role that matches what you want to do. Each path leads into the same local network." ?></p>
    <div class="explore-grid">
      <a class="explore-card" href="<?= url('/shops') ?>">
        <span class="explore-label"><?= $fr ? "Magasiner" : "Shop" ?></span>
        <span class="explore-desc"><?= $fr ? "Parcourir les commerces locaux dans le Marché Central" : "Browse local shops on Marketplace Central" ?></span>
      </a>
      <a class="explore-card" href="<?= url('/seller-central') ?>">
        <span class="explore-label"><?= $fr ? "Vendre" : "Sell" ?></span>
        <span class="explore-desc"><?= $fr ? "Affichez votre entreprise, gratuit pour commencer" : "List your business, free to start" ?></span>
      </a>
      <a class="explore-card" href="<?= url('/supplier-central') ?>">
        <span class="explore-label"><?= $fr ? "Fournir" : "Supply" ?></span>
        <span class="explore-desc"><?= $fr ? "Une seule commission, aucun contrat logistique distinct" : "One commission, no separate logistics contract" ?></span>
      </a>
      <a class="explore-card" href="<?= url('/driver-central') ?>">
        <span class="explore-label"><?= $fr ? "Livrer" : "Drive" ?></span>
        <span class="explore-desc"><?= $fr ? "Connaissez votre tarif avant d’accepter" : "Know your rate before you accept" ?></span>
      </a>
    </div>
  </div>
</section>

<!-- TRUST / DATA RESIDENCY -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Où sont hébergées vos données" : "Where your data lives" ?></h3>
    <p><?= $fr
      ? "La plateforme et l’infrastructure propres à OCSAPP sont basées au Canada. Certains partenaires de traitement des paiements auxquels nous faisons appel - notamment Stripe et PayPal - peuvent traiter des données de transaction à l’extérieur du Canada, y compris aux États-Unis, dans le cadre de leurs opérations normales de sécurité et de conformité. Tous les détails se trouvent dans notre <a href=\"" . url('/privacy') . "\">Politique de confidentialité</a>."
      : "OCSAPP's own platform and infrastructure are based in Canada. Certain payment processing partners we rely on - including Stripe and PayPal - may process payment transaction data outside Canada, including in the United States, as part of their own standard security and compliance operations. Full details are in our <a href=\"" . url('/privacy') . "\">Privacy Policy</a>." ?></p>
    <div class="trust-grid">
      <div class="trust-card">
        <h4><?= $fr ? "Plateforme basée au Canada" : "Canadian-based platform" ?></h4>
        <p><?= $fr
          ? "La plateforme et l’infrastructure propres à OCSAPP sont basées au Canada."
          : "OCSAPP's own platform and infrastructure are based in Canada." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Transparence sur les tiers" : "Clear third-party disclosure" ?></h4>
        <p><?= $fr
          ? "Des partenaires de paiement comme Stripe et PayPal peuvent traiter des données de transaction à l’extérieur du Canada; cette relation est clairement indiquée sur la page."
          : "Payment partners such as Stripe and PayPal may process transaction data outside Canada; the page discloses that relationship directly." ?></p>
      </div>
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
