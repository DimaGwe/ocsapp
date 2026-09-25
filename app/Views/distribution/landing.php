<?php
/**
 * OCSAPP Business Central - Distribution/Procurement marketing landing page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "Business Central content update and
 * standardization" package, on the same OCSAPP brand-standard system as
 * buyer/seller/supplier/driver-central: Procurement (free, 1% fee) +
 * Distribution (tiered $49/$179/custom), surcharge transparency, net-30
 * credit terms, insurance requirements, expanded FAQ.
 */
use App\Helpers\VisitorTracker;
VisitorTracker::track();

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "Entreprise Centrale - Approvisionnement et distribution avec OCSAPP" : "Business Central - Procurement & Distribution with OCSAPP" ?></title>
  <meta name="description" content="<?= $fr
    ? "Soumettez des demandes d'approvisionnement multi-fournisseurs, suivez vos expéditions par GPS et automatisez vos routes de livraison récurrentes - le tout depuis un seul tableau de bord."
    : "Submit multi-supplier procurement requests, track shipments by GPS, and automate your recurring delivery routes - all from a single dashboard." ?>">
  <?= seo_lang_links() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/distribution-landing.css') ?>">
</head>
<body class="distribution-landing-page<?= $fr ? ' lang-fr' : '' ?>">
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
    <span class="eyebrow"><?= $fr ? "ENTREPRISE CENTRALE" : "BUSINESS CENTRAL" ?></span>
    <h1><?= $fr
      ? "Approvisionnement, expéditions &amp; <span>distribution récurrente</span> pour votre entreprise"
      : "Procurement, Shipments &amp; <span>Recurring Distribution</span> for Your Business" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Soumettez des demandes d'approvisionnement multi-fournisseurs, suivez vos expéditions par GPS et automatisez vos routes de livraison récurrentes - le tout depuis un seul tableau de bord."
      : "Submit multi-supplier procurement requests, track shipments by GPS, and automate your recurring delivery routes - all from a single dashboard." ?></p>
    <div class="hero-actions">
      <a class="btn" href="<?= url('distribution/register') ?>"><?= $fr ? "Inscrire votre entreprise →" : "Register Your Business →" ?></a>
      <a class="btn-secondary" href="<?= url('distribution/login') ?>"><?= $fr ? "Déjà inscrit? Se connecter" : "Already registered? Log in" ?></a>
    </div>
    <div class="hero-proof-row cols-4">
      <div class="hero-proof-item"><strong><?= $fr ? "0 $" : "$0" ?></strong><span><?= $fr ? "frais cachés" : "hidden fees" ?></span></div>
      <div class="hero-proof-item"><strong><?= $fr ? "Ouest-de-l'Île" : "West Island" ?></strong><span><?= $fr ? "zone active, Laval &amp; Montréal à venir" : "active zone, Laval &amp; Montreal coming" ?></span></div>
      <div class="hero-proof-item"><strong>100%</strong><span><?= $fr ? "objectif zéro émission" : "zero-emission goal" ?></span></div>
      <div class="hero-proof-item"><strong>GPS</strong><span><?= $fr ? "suivi en direct, de bout en bout" : "live tracking, end to end" ?></span></div>
    </div>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p><?= $fr
      ? "Utilisez <span>Approvisionnement, Distribution, ou les deux</span> - sur un seul compte entreprise, sans obligation de combiner les deux."
      : "Use <span>Procurement, Distribution, or both</span> - on a single business account, with no obligation to combine them." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Deux services d'entreprise. Un seul tableau de bord." : "Two business services. One dashboard." ?></h2>
    <p><?= $fr
      ? "Approvisionnez-vous auprès de notre réseau de fournisseurs vérifiés, distribuez vos propres produits à travers notre réseau de livraison, ou faites les deux à partir du même compte. Chaque expédition - la vôtre ou celle d'un fournisseur - passe par le même réseau de livreurs ODA à objectif zéro émission, avec un tarif affiché avant confirmation."
      : "Source from our network of verified suppliers, distribute your own products through our delivery network, or do both from the same account. Every shipment - yours or a supplier's - moves through the same ODA driver network working toward a zero-emission objective, with a rate disclosed before you confirm." ?></p>
  </div>
</section>

<!-- WHY CHOOSE OCSAPP -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI CHOISIR OCSAPP" : "WHY CHOOSE OCSAPP" ?></div>
    <h2><?= $fr ? "Tout ce dont votre entreprise a besoin, livré." : "Everything your business needs, delivered." ?></h2>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Demandes d'approvisionnement" : "Procurement requests" ?></h3>
        <p><?= $fr ? "Soumettez une demande couvrant plusieurs fournisseurs à la fois - OCSAPP coordonne l'approvisionnement et consolide la livraison en une seule expédition." : "Submit a single request spanning multiple suppliers - OCSAPP coordinates procurement and consolidates delivery into one shipment." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Expéditions suivies par GPS" : "GPS-tracked shipments" ?></h3>
        <p><?= $fr ? "Chaque expédition est suivie de bout en bout par notre réseau de livreurs ODA, de la collecte à la preuve de livraison." : "Every shipment is tracked end to end by our ODA driver network, from pickup to proof of delivery." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Routes récurrentes" : "Recurring routes" ?></h3>
        <p><?= $fr ? "Configurez des routes de livraison récurrentes pour votre réapprovisionnement régulier - sans devoir soumettre une nouvelle demande à chaque fois." : "Set up recurring delivery routes for your regular restocking - no need to submit a new request every time." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- TRANSPARENCY VALUE PANEL -->
<div class="reveal value-panel">
  <h2><?= $fr ? "Deux services, un compte, <span>en toute transparence.</span>" : "Two services, one account, <span>fully transparent.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Ce que chaque entreprise peut tenir pour acquis, peu importe le service utilisé." : "What every business can count on, regardless of which service you use." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Aucune majoration cachée sur les prix des fournisseurs - jamais" : "No hidden markup on supplier prices - ever" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Chaque livraison passe par le réseau ODA d'OCSAPP - jamais un transporteur tiers" : "Every delivery moves through OCSAPP's own ODA network - never a third-party carrier" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Frais de traitement toujours détaillés séparément - jamais ajoutés en cachette" : "Processing fees always itemized separately - never added quietly" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Utilisez Approvisionnement, Distribution, ou les deux, sans obligation de combiner" : "Use Procurement, Distribution, or both, with no obligation to combine them" ?></p></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "COMMENT ÇA MARCHE" : "HOW IT WORKS" ?></div>
    <h2><?= $fr ? "En trois étapes simples" : "Three simple steps" ?></h2>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Inscrivez-vous" : "Register" ?></h3>
        <p><?= $fr ? "Créez votre compte entreprise, vérifié par NEQ. Votre compte est activé sous 1 à 2 jours ouvrables une fois votre NEQ vérifié." : "Create your NEQ-verified business account. Your account is activated within 1–2 business days once your NEQ is verified." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Demandez ou automatisez" : "Request or automate" ?></h3>
        <p><?= $fr ? "Soumettez une demande d'approvisionnement ponctuelle, ou configurez une route récurrente avec vos fournisseurs et votre calendrier de livraison." : "Submit a one-time procurement request, or set up a recurring route with your suppliers and delivery schedule." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Suivez et recevez" : "Track and receive" ?></h3>
        <p><?= $fr ? "Suivez votre expédition en direct par GPS, recevez vos documents (BC, BL, facture) et payez en ligne en toute sécurité." : "Track your shipment live by GPS, receive your documents (PO, BL, invoice), and pay securely online." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- BEFORE YOU REGISTER -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "AVANT DE VOUS INSCRIRE" : "BEFORE YOU REGISTER" ?></div>
    <h2><?= $fr ? "Ce qu'il faut préparer" : "What to have ready" ?></h2>
    <p class="section-lead"><?= $fr
      ? "Avoir ces informations en main accélère votre inscription et nous permet de configurer votre compte correctement dès le départ."
      : "Having this information on hand speeds up your registration and ensures we configure your account correctly from day one." ?></p>
    <div class="requirements-box">
      <h4><?= $fr ? "Informations d'entreprise" : "Business information" ?></h4>
      <ul>
        <li><?= $fr ? "Nom légal de l'entreprise" : "Legal business name" ?></li>
        <li><?= $fr ? "Adresse d'affaires et province" : "Business address and province" ?></li>
        <li><?= $fr ? "Numéro d'entreprise du Québec (NEQ)" : "Quebec business registration number (NEQ)" ?></li>
        <li><?= $fr ? "Numéros d'inscription TPS / TVQ (GST/QST)" : "GST / QST registration numbers (TPS/TVQ)" ?></li>
        <li><?= $fr ? "Type d'entreprise et secteur d'activité" : "Business type and industry" ?></li>
        <li><?= $fr ? "Volume d'approvisionnement mensuel estimé" : "Estimated monthly procurement volume" ?></li>
      </ul>
    </div>
    <div class="requirements-box" style="margin-top:18px;">
      <h4><?= $fr ? "Contacts clés" : "Key contacts" ?></h4>
      <ul>
        <li><?= $fr ? "Contact principal d'approvisionnement (nom + courriel)" : "Primary procurement contact (name + email)" ?></li>
        <li><?= $fr ? "Contact finance / comptes payables pour les factures" : "Finance / accounts payable contact for invoices" ?></li>
        <li><?= $fr ? "Contact réception pour la coordination des livraisons" : "Receiving contact for delivery coordination" ?></li>
        <li><?= $fr ? "Directeur général ou propriétaire pour les décisions de compte" : "General manager or owner for account decisions" ?></li>
      </ul>
    </div>
    <div class="requirements-box" style="margin-top:18px;">
      <h4><?= $fr ? "Besoins d'approvisionnement" : "Sourcing requirements" ?></h4>
      <ul>
        <li><?= $fr ? "Catégories de produits recherchées" : "Product categories you source" ?></li>
        <li><?= $fr ? "Fréquence de commande approximative" : "Approximate order frequency" ?></li>
        <li><?= $fr ? "Tailles et quantités de commande typiques" : "Typical order sizes and quantities" ?></li>
        <li><?= $fr ? "Certifications requises (biologique, halal, casher)" : "Any certifications required (organic, halal, kosher)" ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- EVERYTHING YOU GET -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "TOUT CE QUE VOUS OBTENEZ" : "EVERYTHING YOU GET" ?></div>
    <h2><?= $fr ? "Des outils conçus pour les entreprises modernes" : "Tools built for modern businesses" ?></h2>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc1" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#27e92f"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M9 18h17v16H9zM38 18h17v16H38zM24 37h17v16H24z" fill="url(#bc1)"/><path d="M26 26h12M18 34l9 7M46 34l-9 7" stroke="#dffff0" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Approvisionnement multi-fournisseurs" : "Multi-supplier procurement" ?></h3>
        <p><?= $fr ? "Soumettez une seule demande couvrant plusieurs fournisseurs - OCSAPP coordonne l'approvisionnement et consolide la livraison." : "Submit a single request spanning multiple suppliers - OCSAPP coordinates procurement and consolidates delivery." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc2" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#21e529"/><stop offset="1" stop-color="#07710b"/></linearGradient></defs><path d="M9 30h31v17H9z" fill="url(#bc2)"/><path d="M40 34h9l7 8v5H40z" fill="#0b8d10"/><circle cx="20" cy="49" r="5" fill="#082d0a"/><circle cx="47" cy="49" r="5" fill="#082d0a"/><path d="M18 19c8-7 24-7 31 1" fill="none" stroke="#00B207" stroke-width="4" stroke-linecap="round"/><path d="M46 15l5 6-7 4" fill="none" stroke="#00B207" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Routes de livraison récurrentes" : "Recurring delivery routes" ?></h3>
        <p><?= $fr ? "Automatisez votre réapprovisionnement régulier avec des routes récurrentes - mettez en pause, reprenez ou ajustez à tout moment. Disponible dès le forfait Pro." : "Automate your regular restocking with recurring routes - pause, resume, or adjust anytime. Available from the Pro tier." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc3" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#076e0a"/></linearGradient></defs><path d="M32 7c10 0 18 8 18 18 0 13-18 31-18 31S14 38 14 25C14 15 22 7 32 7z" fill="url(#bc3)"/><circle cx="32" cy="25" r="8" fill="#f5fff5"/><circle cx="32" cy="25" r="3.5" fill="#00B207"/></svg></span>
        <h3><?= $fr ? "Expéditions suivies par GPS" : "GPS-tracked shipments" ?></h3>
        <p><?= $fr ? "Suivez chaque expédition en direct, de la collecte à la livraison, avec confirmation de preuve de livraison." : "Track every shipment live, from pickup to delivery, with proof-of-delivery confirmation." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc4" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M15 8h28l8 8v40H15z" fill="url(#bc4)"/><path d="M43 8v10h9" fill="#dffff0"/><path d="M22 27h22M22 35h17M22 43h20" stroke="#f4fff5" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Documents BC, BL &amp; facture" : "PO, BL &amp; invoice documents" ?></h3>
        <p><?= $fr ? "Bons de commande, bons de livraison et factures générés automatiquement - prêts en PDF ou EDI pour vos dossiers." : "Purchase orders, bills of lading, and invoices generated automatically - ready in PDF or EDI for your records." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc5" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#20e528"/><stop offset="1" stop-color="#076d0a"/></linearGradient></defs><rect x="9" y="18" width="46" height="30" rx="7" fill="url(#bc5)"/><rect x="14" y="24" width="36" height="6" rx="3" fill="#f5fff5"/><path d="M24 36h12" stroke="#dffff0" stroke-width="3" stroke-linecap="round"/><path d="M42 37v-3a5 5 0 0 1 10 0v3" fill="none" stroke="#eaffeb" stroke-width="2.5"/><rect x="40" y="37" width="14" height="11" rx="3" fill="#effff0"/></svg></span>
        <h3><?= $fr ? "Paiements en ligne sécurisés" : "Secure online payments" ?></h3>
        <p><?= $fr ? "Payez en toute sécurité par carte de crédit, PayPal, virement bancaire (EFT), ou sur termes nets une fois admissible - avec une facturation détaillée claire, sans frais caché." : "Pay securely by credit card, PayPal, bank transfer (EFT), or net terms once eligible - with clear, itemized billing and no hidden fees." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc6" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="22" r="10" fill="url(#bc6)"/><path d="M13 52c2-12 9-19 19-19s17 7 19 19z" fill="url(#bc6)"/><path d="M15 24c0-10 7-17 17-17s17 7 17 17" fill="none" stroke="#dffff0" stroke-width="3"/><path d="M49 25v9h-7" fill="none" stroke="#f5fff5" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Support de compte dédié" : "Dedicated account support" ?></h3>
        <p><?= $fr ? "Une équipe dédiée et une messagerie intégrée pour répondre à vos questions et résoudre tout problème rapidement." : "A dedicated team and built-in messaging to answer your questions and resolve any issue quickly." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc7" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#28e830"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><circle cx="23" cy="22" r="8" fill="url(#bc7)"/><circle cx="43" cy="25" r="7" fill="#17c81f"/><path d="M9 51c1-10 7-16 14-16s14 6 15 16z" fill="url(#bc7)"/><path d="M35 50c1-8 5-13 10-13s10 5 11 13z" fill="#0a8f0f"/></svg></span>
        <h3><?= $fr ? "Accès d'équipe" : "Team access" ?></h3>
        <p><?= $fr ? "Ajoutez des membres de votre équipe avec des permissions basées sur leur rôle - séparez l'accès approvisionnement, finance et réception selon votre organisation." : "Add team members with role-based permissions - separate procurement, finance, and receiving access across your organization." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc8" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08760c"/></linearGradient></defs><path d="M12 49V31h8v18zm13 0V23h8v26zm13 0V14h8v35z" fill="url(#bc8)"/><path d="M9 53h45" stroke="#075d0a" stroke-width="4" stroke-linecap="round"/><path d="M15 22l12-7 9 5 14-10" fill="none" stroke="#00B207" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Analytiques de dépenses" : "Spend analytics" ?></h3>
        <p><?= $fr ? "Rapports de dépenses mensuels et trimestriels par fournisseur et catégorie. Identifiez vos meilleurs fournisseurs et vos occasions d'économie." : "Monthly and quarterly spend reports by supplier and category. Identify your top suppliers and savings opportunities." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc9" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M9 15h46v31H31l-11 8v-8H9z" fill="url(#bc9)"/><circle cx="22" cy="30" r="3" fill="#fff"/><circle cx="32" cy="30" r="3" fill="#fff"/><circle cx="42" cy="30" r="3" fill="#fff"/></svg></span>
        <h3><?= $fr ? "Messagerie fournisseurs" : "Supplier messaging" ?></h3>
        <p><?= $fr ? "Communiquez directement avec vos fournisseurs depuis le portail pour des précisions de commande, des prix personnalisés ou la planification de livraison - tout est consigné et consultable." : "Communicate directly with suppliers through the portal for order clarifications, custom quotes, or delivery scheduling - all logged and searchable." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- SURCHARGES / TRUST -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Suppléments - affichés avant confirmation, jamais une surprise" : "Surcharges - disclosed before you confirm, never a surprise" ?></h3>
    <p><?= $fr
      ? "Certaines demandes ou expéditions portent un supplément en plus des frais de base. Chacun est calculé automatiquement et affiché avant que vous confirmiez."
      : "Some requests or shipments carry a surcharge on top of the base fee. Each one is calculated automatically and shown before you confirm." ?></p>
    <div class="trust-grid trust-grid-3col">
      <div class="trust-card">
        <h4><?= $fr ? "Supplément commande volumineuse" : "Oversize order surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 25 kg (bande de base 25-50 kg) : 10,00 $ / 11,25 $ / 12,50 $ selon la zone, plus un incrément de 5,00 $ / 5,63 $ / 6,25 $ par 10 kg additionnels au-delà de 50 kg. Maximum standard : 100 kg." : "Above 25 kg (25-50 kg base band): \$10.00 / \$11.25 / \$12.50 by zone, plus a \$5.00 / \$5.63 / \$6.25 increment per additional 10 kg beyond 50 kg. Standard maximum: 100 kg." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Frais d'arrêt additionnel" : "Additional-stop fee" ?></h4>
        <p><?= $fr ? "Approvisionnement : 4,00 $ / 4,50 $ / 5,00 $ par fournisseur au-delà des deux premiers. Distribution : même tarif, par emplacement de cueillette au-delà des deux premiers (forfait Pro et plus)." : "Procurement: \$4.00 / \$4.50 / \$5.00 per supplier beyond the first two. Distribution: same rate, per pickup location beyond the first two (Pro tier and above)." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément longue distance" : "Long-distance surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 10 km de trajet routier : 10,00 $ / 11,25 $ / 12,50 $, plus un incrément de 5,00 $ / 5,63 $ / 6,25 $ par 5 km additionnels au-delà de 15 km. Maximum standard : 30 km." : "Beyond 10 km of routed distance: \$10.00 / \$11.25 / \$12.50, plus a \$5.00 / \$5.63 / \$6.25 increment per additional 5 km beyond 15 km. Standard maximum: 30 km." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Poids : jamais à déclarer vous-même" : "Weight: never yours to declare" ?></h4>
        <p><?= $fr ? "Pour Approvisionnement, le poids provient de la fiche produit de chaque fournisseur. Pour Distribution, il provient des données de votre propre expédition au moment de sa création - dans les deux cas, rien à estimer." : "For Procurement, weight comes from each supplier's own product listing. For Distribution, it comes from your own shipment data at creation - nothing to estimate either way." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Contestation possible" : "You can dispute it" ?></h4>
        <p><?= $fr ? "Vous disposez de 5 jours ouvrables pour contester un supplément ou une facturation, directement depuis le tableau de bord entreprise, avec preuve à l'appui." : "You have 5 business days to dispute a surcharge or charge, directly from the Business Dashboard, with supporting evidence." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Financé par la transaction, pas par OCSAPP" : "Funded by the transaction, not by OCSAPP" ?></h4>
        <p><?= $fr ? "Ces suppléments financent le réseau de livraison ODA - ils ne créent aucun coût additionnel pour OCSAPP et ne s'appliquent que lorsqu'une commande ou expédition dépasse le seuil standard." : "These surcharges fund the ODA delivery network - they create no additional cost for OCSAPP and only apply when an order or shipment exceeds the standard threshold." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CUSTOM PRICING -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "TARIFICATION PERSONNALISÉE" : "CUSTOM PRICING" ?></div>
    <h2><?= $fr ? "Un volume élevé et constant? Parlons contrat sur mesure." : "High, consistent volume? Let's talk custom contracts." ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Pour les entreprises à volume élevé et prévisible :" : "For businesses with high, predictable order volume:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr ? "OCSAPP peut offrir une tarification basée sur le volume, des termes négociés, ou des arrangements de livraison personnalisés aux entreprises clientes dont les commandes sont élevées et constantes." : "OCSAPP may offer volume-based pricing, negotiated terms, or custom delivery arrangements to business clients with consistent, high-volume orders." ?></li>
        <li><?= $fr ? "Cette entente prend la forme d'un addenda écrit à votre contrat entreprise - en son absence, la tarification standard par forfait (ci-dessous) s'applique." : "This arrangement takes the form of a written addendum to your business account agreement - in its absence, standard tier pricing (below) applies." ?></li>
        <li><?= $fr ? "Pour discuter d'une tarification personnalisée, contactez info@ocsapp.ca avec votre volume mensuel estimé et vos catégories de produits." : "To discuss custom pricing, contact info@ocsapp.ca with your estimated monthly volume and product categories." ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="pricing">
  <div class="wrap">
    <div class="pricing-head">
      <div class="section-eyebrow"><?= $fr ? "DEUX FAÇONS DE TRAVAILLER AVEC NOUS" : "TWO WAYS TO WORK WITH US" ?></div>
      <h2><?= $fr ? "Approvisionnez-vous, distribuez, ou les deux" : "Source, distribute, or both" ?></h2>
      <p><?= $fr ? "Approvisionnement est inclus gratuitement avec tout compte entreprise. Distribution se choisit par forfait, selon vos volumes." : "Procurement is included free with any business account. Distribution is tiered by volume." ?></p>
    </div>
    <div class="pricing-grid">
      <div class="pricing-card">
        <h3><?= $fr ? "Approvisionnement" : "Procurement" ?></h3>
        <p class="pricing-tagline"><?= $fr ? "Inclus gratuitement avec tout compte" : "Included free with any account" ?></p>
        <div class="pricing-price"><?= $fr ? "Gratuit" : "Free" ?></div>
        <p class="pricing-commission"><strong>1%</strong> <?= $fr ? "frais d'approvisionnement, non paliers" : "procurement fee, flat, not tiered" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Demandes multi-fournisseurs" : "Multi-supplier requests" ?></li>
          <li><?= $fr ? "Documents BC, BL &amp; facture automatiques" : "Automatic PO, BL &amp; invoice documents" ?></li>
          <li><?= $fr ? "Paiements en ligne sécurisés" : "Secure online payments" ?></li>
          <li><?= $fr ? "Tableau de bord &amp; messagerie" : "Dashboard &amp; messaging" ?></li>
          <li><?= $fr ? "Aucune majoration sur les prix fournisseurs" : "No markup on supplier prices" ?></li>
        </ul>
        <a class="pricing-cta" href="<?= url('distribution/register') ?>"><?= $fr ? "Inscription gratuite" : "Register Free" ?></a>
      </div>
      <div class="pricing-card">
        <h3><?= $fr ? "Débutant" : "Starter" ?></h3>
        <p class="pricing-tagline"><?= $fr ? "Distribution pour vos propres produits" : "Distribution for your own products" ?></p>
        <div class="pricing-price">$49 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>5%</strong> <?= $fr ? "frais distribution (Ouest-de-l'Île)" : "distribution fee (West Island)" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Expéditions suivies par GPS" : "GPS-tracked shipments" ?></li>
          <li><?= $fr ? "Intégration livraison locale" : "Local delivery integration" ?></li>
          <li><?= $fr ? "Frais de livraison inclus (19 $/21 $/24 $ selon zone)" : "Delivery fee included (\$19/\$21/\$24 by zone)" ?></li>
        </ul>
        <a class="pricing-cta outline" href="<?= url('distribution/register') ?>"><?= $fr ? "Commencer" : "Get Started" ?></a>
      </div>
      <div class="pricing-card popular">
        <span class="pricing-badge"><?= $fr ? "Le plus populaire" : "Most Popular" ?></span>
        <h3>Pro</h3>
        <p class="pricing-tagline"><?= $fr ? "Distribution pour PME en croissance" : "Distribution for growing SMEs" ?></p>
        <div class="pricing-price">$179 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>7%</strong> <?= $fr ? "frais distribution (Ouest-de-l'Île)" : "distribution fee (West Island)" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce que Débutant inclut" : "Everything in Starter" ?></li>
          <li><?= $fr ? "Routes de livraison récurrentes" : "Recurring delivery routes" ?></li>
          <li><?= $fr ? "Support multi-emplacements" : "Multi-location support" ?></li>
          <li><?= $fr ? "Routage dédié" : "Dedicated routing" ?></li>
          <li><?= $fr ? "Analytiques avancées" : "Advanced analytics" ?></li>
        </ul>
        <a class="pricing-cta" href="<?= url('distribution/register') ?>"><?= $fr ? "Commencer" : "Get Started" ?></a>
      </div>
      <div class="pricing-card">
        <h3>Enterprise</h3>
        <p class="pricing-tagline"><?= $fr ? "Distribution sur mesure, grande échelle" : "Custom distribution, large scale" ?></p>
        <div class="pricing-price"><?= $fr ? "Sur devis" : "Custom quote" ?></div>
        <p class="pricing-commission"><?= $fr ? "Frais distribution" : "Distribution fee" ?> <strong><?= $fr ? "personnalisés" : "custom" ?></strong></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce que Pro inclut" : "Everything in Pro" ?></li>
          <li><?= $fr ? "Accès API complet" : "Full API access" ?></li>
          <li><?= $fr ? "Support logistique dédié" : "Dedicated logistics support" ?></li>
          <li><?= $fr ? "SLA personnalisés" : "Custom SLAs" ?></li>
          <li><?= $fr ? "Équipe de compte dédiée" : "Dedicated account team" ?></li>
        </ul>
        <a class="pricing-cta outline" href="mailto:info@ocsapp.ca?subject=Enterprise%20Distribution%20Inquiry"><?= $fr ? "Nous contacter" : "Contact Us" ?></a>
      </div>
    </div>
    <div class="pricing-note">
      <p><strong><?= $fr ? "Remarque :" : "Note:" ?></strong> <?= $fr
        ? "Vous pouvez utiliser Approvisionnement seul, Distribution seul, ou les deux ensemble sur le même compte entreprise. Une livraison forfaitaire de 19 $ (Ouest-de-l'Île), 21 $ (Laval) ou 24 $ (Grand Montréal), selon la zone, est incluse avec tous les forfaits Distribution. Le frais distribution ci-dessus est le taux de base (Ouest-de-l'Île), zone-ajusté selon le tableau ci-dessus."
        : "You can use Procurement alone, Distribution alone, or both together on the same business account. A flat delivery fee of \$19 (West Island), \$21 (Laval), or \$24 (Greater Montreal), by zone, is included with every Distribution tier. The distribution fee above is the West Island base rate, zone-adjusted per the table above." ?></p>
      <p><strong><?= $fr ? "Frais de traitement des paiements :" : "Payment processing fee:" ?></strong> <?= $fr
        ? "2,9 % + 0,30 $ CAD, absorbés par le compte entreprise et déduits du montant net avant versement (Distribution) ou ajoutés à la facture (Approvisionnement) - toujours détaillés séparément, jamais facturés à vos propres clients."
        : "2.9% + \$0.30 CAD, absorbed by the business account and deducted from net proceeds before payout (Distribution) or added to the invoice (Procurement) - always itemized separately, never charged to your own customers." ?></p>
    </div>
  </div>
</section>

<!-- PAYMENT & CREDIT TERMS -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "MODALITÉS DE PAIEMENT ET DE CRÉDIT" : "PAYMENT &amp; CREDIT TERMS" ?></div>
    <h2><?= $fr ? "Comment fonctionnent les termes nets (net-30)" : "How net-30 terms work" ?></h2>
    <p class="section-lead"><?= $fr ? "Les termes nets ne sont pas offerts dès l'activation du compte - voici exactement comment y devenir admissible." : "Net terms aren't available immediately on activation - here's exactly how to become eligible." ?></p>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc10" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#bc10)"/><circle cx="32" cy="32" r="12" fill="#f5fff5"/><path d="M32 20v12l8 5" fill="none" stroke="#00B207" stroke-width="3.5" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Période de qualification" : "Qualification period" ?></h3>
        <p><?= $fr
          ? "Payez par carte, PayPal, ou virement pour vos 3 premières commandes complétées et vos 30 premiers jours d'activité de compte, les deux étant requis, avant de devenir admissible aux termes nets."
          : "Pay by card, PayPal, or EFT for your first 3 completed orders and your first 30 days of account activity - both are required - before becoming eligible for net-30 terms." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc11" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#20e528"/><stop offset="1" stop-color="#076d0a"/></linearGradient></defs><rect x="9" y="17" width="46" height="32" rx="6" fill="url(#bc11)"/><circle cx="22" cy="31" r="7" fill="#f4fff5"/><path d="M35 26h12M35 33h12M17 42h30" stroke="#d8ffda" stroke-width="3" stroke-linecap="round"/><path d="M44 44l4 4 8-10" fill="none" stroke="#effff0" stroke-width="3"/></svg></span>
        <h3><?= $fr ? "Vérification de crédit" : "Credit check" ?></h3>
        <p><?= $fr ? "Avant d'accorder les termes nets, OCSAPP effectue une vérification de crédit commercial auprès d'un bureau reconnu (Equifax Canada ou Dun &amp; Bradstreet Canada), avec votre autorisation." : "Before extending net-30 terms, OCSAPP conducts a business credit check through a recognized bureau (Equifax Canada or Dun &amp; Bradstreet Canada), with your authorization." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc12" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#2be933"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#bc12)"/><path d="M37 19c-9-2-16 1-16 7 0 11 22 4 22 15 0 7-8 9-18 6" fill="none" stroke="#f4fff5" stroke-width="4" stroke-linecap="round"/><path d="M32 14v36" stroke="#d8ffda" stroke-width="3"/></svg></span>
        <h3><?= $fr ? "Limites de crédit par type de compte" : "Credit limits by account type" ?></h3>
        <p><?= $fr ? "Approvisionnement seul ou Débutant : 2 500 $. Pro : 10 000 $. Enterprise : négocié individuellement. Augmentation possible au cas par cas, selon votre historique de paiement." : "Procurement-only or Starter: \$2,500. Pro: \$10,000. Enterprise: negotiated individually. Increases available case-by-case based on your payment history." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc13" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#20e528"/><stop offset="1" stop-color="#076d0a"/></linearGradient></defs><rect x="9" y="17" width="46" height="32" rx="7" fill="url(#bc13)"/><rect x="14" y="23" width="36" height="7" rx="3.5" fill="#f2fff3"/><circle cx="21" cy="40" r="4" fill="#fff"/><path d="M31 40h14" stroke="#d8ffda" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Carte au dossier requise" : "Card on file required" ?></h3>
        <p><?= $fr ? "Une carte valide reste au dossier en tout temps, peu importe le mode de paiement choisi par commande - un filet de sécurité si une facture nette demeure impayée 5 jours ouvrables après échéance." : "A valid card stays on file at all times, regardless of the payment method chosen per order - a backstop if a net invoice remains unpaid 5 business days past due." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc14" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><path d="M11 23l21-11 21 11-21 11z" fill="#3af042"/><path d="M11 23v24l21 10V34z" fill="url(#bc14)"/><path d="M53 23v24L32 57V34z" fill="#0a8f0f"/><circle cx="47" cy="16" r="10" fill="#effff0"/><path d="M50 10c-6-1-10 1-10 5 0 7 13 3 13 9 0 4-5 6-11 4" fill="none" stroke="#00B207" stroke-width="2.6"/></svg></span>
        <h3><?= $fr ? "Dépôt sur grosses commandes" : "Deposit on large orders" ?></h3>
        <p><?= $fr ? "Pour une commande dépassant 50 % de votre limite de crédit approuvée, un dépôt pouvant aller jusqu'à 50 % de la valeur peut être exigé avant traitement." : "For an order exceeding 50% of your approved credit limit, a deposit of up to 50% of the order value may be required before processing." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc15" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#26e82e"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><path d="M32 8l20 8v14c0 13-8 22-20 28C20 52 12 43 12 30V16z" fill="url(#bc15)"/><path d="M21 32l7 7 15-17" fill="none" stroke="#f5fff5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Maintien de l'admissibilité" : "Maintaining eligibility" ?></h3>
        <p><?= $fr ? "Les termes nets peuvent être suspendus après 15 jours de retard, deux retards ou plus sur 12 mois, ou une détérioration importante de votre cote de crédit. Le rétablissement se fait à la discrétion d'OCSAPP." : "Net terms may be suspended after 15 days past due, two or more late payments in 12 months, or a material drop in credit standing. Reinstatement is at OCSAPP's discretion." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="bc16" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#076e0a"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#bc16)"/><path d="M32 18v14l9 6" fill="none" stroke="#f5fff5" stroke-width="3.5" stroke-linecap="round"/><path d="M20 20l-4-2 1-4" fill="none" stroke="#dffff0" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Intérêt sur paiement en retard" : "Late payment interest" ?></h3>
        <p><?= $fr ? "Une facture non payée à l'échéance porte intérêt à compter de sa date d'échéance jusqu'au paiement intégral. Le taux applicable est précisé dans votre Entente de compte Entreprise et sur votre facture." : "An invoice not paid by its due date accrues interest from the due date until paid in full. The applicable rate is stated in your Business Account Agreement and on your invoice." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- INSURANCE -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "ASSURANCE" : "INSURANCE" ?></div>
    <h2><?= $fr ? "Exigences d'assurance pour Distribution" : "Insurance requirements for Distribution" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Applicable uniquement si vous utilisez Distribution - les comptes Approvisionnement seul en sont exemptés :" : "Only applies if you use Distribution - Procurement-only accounts are exempt:" ?></h4>
      <ul>
        <li><?= $fr ? "Forfait Débutant : assurance responsabilité civile générale minimum 1 000 000 $" : "Starter tier: minimum \$1,000,000 commercial general liability" ?></li>
        <li><?= $fr ? "Forfait Pro : assurance responsabilité civile générale minimum 2 000 000 $" : "Pro tier: minimum \$2,000,000 commercial general liability" ?></li>
        <li><?= $fr ? "Forfait Enterprise : minimum 2 000 000 $, jusqu'à 5 000 000 $ selon le volume et la catégorie de produits" : "Enterprise tier: minimum \$2,000,000, up to \$5,000,000 depending on volume and product category" ?></li>
        <li><?= $fr ? "Garantie responsabilité produits requise si vous distribuez des aliments, produits frais ou biens de consommation" : "Product liability coverage required if you distribute food, produce, or consumable goods" ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- OUR BUSINESSES -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "NOS ENTREPRISES" : "OUR BUSINESSES" ?></div>
    <h2><?= $fr ? "Des entreprises qui nous font confiance" : "Businesses that trust us" ?></h2>
    <p class="section-lead"><?= $fr ? "De tous les secteurs, dans le West Island et en expansion." : "Across every sector, in the West Island and expanding." ?></p>
    <div class="category-chips">
      <span class="category-chip"><?= $fr ? "Restaurants" : "Restaurants" ?></span>
      <span class="category-chip"><?= $fr ? "Bureaux" : "Offices" ?></span>
      <span class="category-chip"><?= $fr ? "Commerce de détail" : "Retail" ?></span>
      <span class="category-chip"><?= $fr ? "Cliniques" : "Clinics" ?></span>
      <span class="category-chip"><?= $fr ? "Construction" : "Construction" ?></span>
      <span class="category-chip"><?= $fr ? "Éducation" : "Education" ?></span>
    </div>
  </div>
</section>

<!-- FOUNDING PARTNER PROGRAM -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "PROGRAMME PARTENAIRE FONDATEUR" : "FOUNDING PARTNER PROGRAM" ?></div>
    <h2><?= $fr ? "Rejoignez nos 5 tout premiers comptes entreprise" : "Join our first 5 business accounts" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Réservé aux 5 premiers comptes entreprise approuvés :" : "Reserved for the first 5 approved business accounts:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr
          ? "Taux de commission Distribution Débutant verrouillé 6 mois - 5 %, 0 $ de frais mensuels (le forfait Débutant coûte normalement 49 $/mois)."
          : "Distribution Starter-tier commission locked for 6 months - 5%, $0 monthly fee (the Starter plan normally costs $49/month)." ?></li>
        <li><?= $fr
          ? "Un gestionnaire de compte dédié dès le premier jour, normalement réservé aux paliers supérieurs."
          : "A dedicated account manager from day one, normally reserved for higher plan tiers." ?></li>
        <li><?= $fr
          ? "Bientôt : une exemption des frais d'Approvisionnement sur votre premier 10 000 $ de volume est prévue - elle sera annoncée une fois lancée."
          : "Coming soon: a fee waiver on your first \$10,000 of Procurement volume is planned - it'll be announced once it launches." ?></li>
        <li><?= $fr
          ? "Aucune démarche à faire : votre statut est confirmé automatiquement à l'approbation de votre compte, tant que la cohorte n'est pas fermée."
          : "Nothing to request: your status is confirmed automatically when your account is approved, as long as the cohort isn't already closed." ?></li>
      </ul>
      <p style="margin:14px 0 0;font-weight:600;"><a href="<?= url('founding') ?>#business" style="color:#00b207;"><?= $fr ? 'Voir tous les programmes fondateurs et les places restantes' : 'See all founding programs and spots remaining' ?> &rarr;</a></p>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="wrap">
    <div class="faq-head">
      <div class="section-eyebrow">FAQ</div>
      <h2><?= $fr ? "Questions fréquentes" : "Frequently Asked Questions" ?></h2>
      <p><?= $fr ? "Tout ce que vous devez savoir." : "Everything you need to know." ?></p>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <h4><?= $fr ? "Comment puis-je inscrire mon entreprise ?" : "How do I register my business?" ?></h4>
        <p><?= $fr ? "Cliquez sur « Inscrire votre entreprise », complétez la vérification NEQ et votre compte est activé une fois approuvé." : "Click \"Register Your Business,\" complete NEQ verification, and your account is activated once approved." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment fonctionne l'approvisionnement ?" : "How does procurement work?" ?></h4>
        <p><?= $fr ? "Soumettez une demande listant les produits et quantités dont vous avez besoin. OCSAPP s'approvisionne auprès de vos fournisseurs, consolide la commande et confirme les prix avant l'expédition." : "Submit a request listing the products and quantities you need. OCSAPP sources from your suppliers, consolidates the order, and confirms pricing before shipment." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je configurer des livraisons récurrentes ?" : "Can I set up recurring deliveries?" ?></h4>
        <p><?= $fr ? "Oui, avec le forfait Distribution Pro ou supérieur - configurez une route récurrente pour automatiser votre réapprovisionnement selon le calendrier de votre choix. Mettez en pause, reprenez ou annulez à tout moment depuis votre tableau de bord." : "Yes, with the Distribution Pro tier or above - set up a recurring route to automate your restocking on your chosen schedule. Pause, resume, or cancel anytime from your dashboard." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Combien ça coûte ?" : "How much does it cost?" ?></h4>
        <p><?= $fr ? "Approvisionnement est gratuit (1 % de frais, aucun abonnement mensuel) - nous nous approvisionnons auprès de nos fournisseurs pour vous. Distribution est un service séparé pour vos propres produits, avec des forfaits Débutant, Pro et Enterprise. Utilisez l'un, l'autre, ou les deux. Aucune majoration cachée sur les produits." : "Procurement is free (1% fee, no monthly subscription) - we source from our suppliers for you. Distribution is a separate service for your own products, with Starter, Pro, and Enterprise tiers. Use one, the other, or both. No hidden markup on products." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je suivre mon expédition en temps réel ?" : "Can I track my shipment in real time?" ?></h4>
        <p><?= $fr ? "Oui. Chaque expédition est suivie par GPS de la collecte à la livraison, avec des mises à jour en direct dans votre tableau de bord." : "Yes. Every shipment is tracked by GPS from pickup to delivery, with live updates in your dashboard." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment fonctionne le paiement ?" : "How does payment work?" ?></h4>
        <p><?= $fr ? "Payez par carte, PayPal, virement (EFT), ou sur termes nets (net-30) une fois admissible - voir la section Modalités de paiement ci-dessus pour les conditions exactes. Vous recevez un bon de commande, un bon de livraison et une facture pour chaque transaction." : "Pay by card, PayPal, EFT, or net-30 terms once eligible - see the Payment &amp; Credit Terms section above for exact conditions. You receive a purchase order, bill of lading, and invoice for every transaction." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Que se passe-t-il si un fournisseur ne peut pas fournir un article ?" : "What happens if a supplier can't fulfill an item?" ?></h4>
        <p><?= $fr ? "Nous vous contactons immédiatement pour approuver un substitut ou ajuster la commande avant l'expédition. Vous avez toujours le dernier mot." : "We contact you immediately to approve a substitute or adjust the order before shipment. You always have the final say." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Quelles zones sont desservies ?" : "What zones are served?" ?></h4>
        <p><?= $fr ? "Nous desservons actuellement l'Ouest-de-l'Île, avec une expansion prévue à Laval et au Grand Montréal. Contactez-nous pour vérifier la disponibilité dans votre région." : "We currently serve the West Island, with expansion planned to Laval and Greater Montreal. Contact us to check availability in your area." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Y a-t-il une quantité minimale de commande ?" : "Is there a minimum order size?" ?></h4>
        <p><?= $fr ? "Les quantités minimales de commande (QMC) sont fixées par chaque fournisseur individuellement et varient selon le produit - elles sont clairement affichées dans le catalogue avant que vous commandiez. Il n'y a aucun minimum imposé par la plateforme elle-même." : "Minimum order quantities (MOQs) are set by individual suppliers and vary by product - they're clearly displayed in the catalog before you order. There's no platform-level minimum." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Puis-je négocier les prix directement avec les fournisseurs ?" : "Can I negotiate pricing directly with suppliers?" ?></h4>
        <p><?= $fr ? "Oui. Pour les commandes importantes ou récurrentes, vous pouvez demander des prix personnalisés directement auprès des fournisseurs via la messagerie du portail. OCSAPP peut aussi faciliter des négociations de prix contractuels pour les comptes à volume élevé - contactez info@ocsapp.ca." : "Yes. For large or recurring orders, you can request custom quotes directly from suppliers through the portal's messaging feature. OCSAPP can also facilitate contract pricing negotiations for high-volume accounts - contact info@ocsapp.ca." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Puis-je ajouter plusieurs membres de mon équipe ?" : "Can I add multiple team members?" ?></h4>
        <p><?= $fr ? "Oui. Les comptes entreprise prennent en charge l'accès d'équipe avec des permissions basées sur les rôles - ajoutez du personnel d'approvisionnement, des contacts finance et des équipes de réception avec les niveaux d'accès appropriés. Contactez info@ocsapp.ca ou votre gestionnaire de compte pour ajouter des membres." : "Yes. Business accounts support team access with role-based permissions - add procurement staff, finance contacts, and receiving teams with appropriate access levels. Contact info@ocsapp.ca or your account manager to add team members." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Les suppléments affectent-ils mes frais de base ?" : "Do surcharges affect my base fees?" ?></h4>
        <p><?= $fr ? "Non - ils s'ajoutent aux frais standards uniquement lorsqu'une commande dépasse un seuil (poids, distance, ou nombre d'arrêts), et sont toujours affichés avant que vous confirmiez. Ils ne changent jamais votre taux de frais d'approvisionnement ou de distribution." : "No - they're added on top of standard fees only when an order exceeds a threshold (weight, distance, or stop count), and are always disclosed before you confirm. They never change your procurement or distribution fee rate." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Que se passe-t-il si un de mes clients signale un problème avec une expédition Distribution ?" : "What happens if one of my customers reports an issue with a Distribution shipment?" ?></h4>
        <p><?= $fr ? "Le même système automatisé de preuve photo et de balayage détermine si le problème est survenu avant la cueillette (votre produit ou emballage) ou en transit. S'il est déterminé que la faute vous revient, un débit dynamique - les frais de logistique inverse plus la valeur réclamée - est appliqué à votre prochain versement ou facture. Vous n'êtes jamais facturé pour un problème survenu en transit, que OCSAPP absorbe directement. Vous disposez de 5 jours ouvrables pour contester, avec droit à une explication et à une révision humaine de toute décision automatisée." : "The same automated photo-and-scan evidence system determines whether the issue occurred before pickup (your product or packaging) or in transit. If it's determined to be your fault, a Dynamic Chargeback - the reverse-logistics fee plus the claimed value - is applied to your next payout or invoice. You're never charged for an issue that occurred in transit, which OCSAPP absorbs directly. You have 5 business days to dispute, with the right to an explanation and a human review of any automated decision." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Ai-je besoin d'une assurance ?" : "Do I need insurance?" ?></h4>
        <p><?= $fr ? "Seulement si vous utilisez Distribution. Les exigences varient selon votre forfait (1 000 000 $ minimum pour Débutant, jusqu'à 5 000 000 $ pour Enterprise selon le volume) - voir la section Assurance ci-dessus. Les comptes Approvisionnement seul n'ont aucune exigence d'assurance." : "Only if you use Distribution. Requirements vary by tier (\$1,000,000 minimum for Starter, up to \$5,000,000 for Enterprise depending on volume) - see the Insurance section above. Procurement-only accounts have no insurance requirement." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment sont résolus les différends avec OCSAPP ?" : "How are disputes with OCSAPP resolved?" ?></h4>
        <p><?= $fr
          ? "OCSAPP et votre entreprise tenterez d'abord de résoudre tout différend par la négociation de bonne foi. Si le différend n'est pas résolu dans les 30 jours, il peut être soumis aux tribunaux du district judiciaire de Montréal, Québec, à la compétence exclusive desquels vous et OCSAPP vous soumettez. OCSAPP n'exige aucun arbitrage obligatoire dans votre entente."
          : "OCSAPP and your business will first attempt to resolve any dispute through good-faith negotiation. If unresolved within 30 days, it may be submitted to the courts of the judicial district of Montréal, Québec, to whose exclusive jurisdiction you and OCSAPP submit. OCSAPP does not require mandatory arbitration in your agreement." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment fonctionne le programme Partenaire Fondateur ?" : "How does the Founding Partner Program work?" ?></h4>
        <p><?= $fr
          ? "Les 5 premiers comptes entreprise approuvés sur OCSAPP obtiennent automatiquement le statut de Partenaire Fondateur - aucune candidature séparée requise. Vous obtenez le taux Distribution Débutant (5 %) verrouillé 6 mois sans frais mensuel et un gestionnaire de compte dédié. L'exemption des frais d'Approvisionnement décrite dans nos documents du programme est prévue mais pas encore active - elle sera annoncée séparément une fois lancée."
          : "The first 5 approved business accounts on OCSAPP automatically get Founding Partner status - no separate application needed. You get the Distribution Starter rate (5%) locked for 6 months at no monthly fee and a dedicated account manager. The Procurement fee waiver described in our program materials is planned but not yet active - it'll be announced separately once it launches." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SUPPORT -->
<section class="support">
  <div class="wrap">
    <div class="support-grid">
      <div class="support-card">
        <h4><?= $fr ? "Support entreprise" : "Business Support" ?></h4>
        <p class="support-main"><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        <p><?= $fr ? "Réponse dans les 24 heures ouvrables" : "Response within 24 business hours" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Téléphone" : "Phone" ?></h4>
        <p class="support-main">514-746-3789</p>
        <p><?= $fr ? "Lun–Dim · 7h – 23h" : "Mon–Sun · 7am – 11pm" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Tableau de bord entreprise" : "Business Dashboard" ?></h4>
        <p class="support-main">ocsapp.ca/distribution/login</p>
        <p><?= $fr ? "Connexion &amp; gestion de compte" : "Login &amp; account management" ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="wrap">
    <h2><?= $fr ? "Prêt à simplifier votre distribution ?" : "Ready to simplify your distribution?" ?></h2>
    <p><?= $fr ? "Rejoignez les entreprises québécoises qui font confiance à OCSAPP pour leur approvisionnement, leurs expéditions et leurs routes récurrentes." : "Join the Quebec businesses that trust OCSAPP for their procurement, shipments, and recurring routes." ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('distribution/register') ?>"><?= $fr ? "Créer un compte entreprise" : "Create a Business Account" ?></a>
      <a class="btn-secondary" href="<?= url('distribution/login') ?>"><?= $fr ? "Se connecter" : "Log In" ?></a>
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
