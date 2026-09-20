<?php
/**
 * OCSAPP Supplier Central - marketing landing page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "Update and Standardization - Lavery
 * processing" package, on the same OCSAPP brand-standard system as
 * buyer/seller/driver/business-central: purchase-order model, surcharge
 * transparency, weight-field requirement, dispute-resolution jurisdiction,
 * Bill 96 French labelling disclosure, expanded FAQ.
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
  <title><?= $fr ? "Fournisseur Central - Partenariat avec OCSAPP" : "Supplier Central - Partner with OCSAPP" ?></title>
  <meta name="description" content="<?= $fr
    ? "Listez votre catalogue une fois. Recevez des bons de commande confirmés directement dans votre tableau de bord - la livraison est prise en charge pour vous."
    : "List your catalog once. Receive confirmed purchase orders directly in your dashboard - delivery is handled for you." ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/supplier-central.css') ?>">
</head>
<body class="supplier-central-page<?= $fr ? ' lang-fr' : '' ?>">
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
    <span class="eyebrow"><?= $fr ? "FOURNISSEUR CENTRAL" : "SUPPLIER CENTRAL" ?></span>
    <h1><?= $fr ? "Transformez vos produits en <span>commandes récurrentes</span>" : "Turn your products into <span>recurring orders</span>" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Listez votre catalogue une fois. Laissez notre réseau de distribution et de vendeurs vous trouver. Recevez des bons de commande directement dans votre tableau de bord - la livraison est prise en charge pour vous."
      : "List your catalog once. Let our distribution and seller network find you. Receive purchase orders directly in your dashboard - delivery is handled for you." ?></p>
    <div class="hero-actions">
      <a class="btn" href="<?= url('supplier/apply') ?>"><?= $fr ? "Postuler maintenant - c'est gratuit →" : "Apply Now - It's Free →" ?></a>
      <a class="btn-secondary" href="<?= url('supplier/login') ?>"><?= $fr ? "Connexion fournisseur" : "Supplier Login" ?></a>
    </div>
    <div class="hero-proof-row">
      <div class="hero-proof-item"><strong>~15 min</strong><span><?= $fr ? "pour postuler" : "to apply" ?></span></div>
      <div class="hero-proof-item"><strong>1–3 <?= $fr ? "jours" : "days" ?></strong><span><?= $fr ? "délai d'approbation" : "approval time" ?></span></div>
      <div class="hero-proof-item"><strong>4 <?= $fr ? "forfaits" : "plans" ?></strong><span><?= $fr ? "sans frais pour commencer" : "no cost to get started" ?></span></div>
    </div>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p><?= $fr
      ? "Chaque bon de commande reçu est un <span>revenu confirmé</span> - montant, quantité et date connus avant même votre confirmation."
      : "Every purchase order you receive is a <span>confirmed order</span> - amount, quantity, and date known before you even confirm it." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Trouver des acheteurs, c'est le vrai défi du commerce de gros - pas fabriquer le produit." : "Finding buyers is the hard part of wholesale - not making the product." ?></h2>
    <p><?= $fr
      ? "Les appels à froid et les salons professionnels prennent des semaines à porter fruit. Un bon de commande peut traîner sans jamais se conclure. Et une fois le camion parti de votre quai, vous perdez toute visibilité. OCSAPP inverse ce modèle : listez votre catalogue une seule fois, et laissez notre réseau de distribution, de vendeurs et d'entreprises vous envoyer des bons de commande réels - pas des prospects à relancer."
      : "Cold calls and trade show relationships take weeks to pay off. A purchase order can drag on without ever closing. And once the truck leaves your dock, you lose all visibility. OCSAPP flips that model: list your catalog once, and let our distribution, seller, and business network send you real purchase orders - not leads you have to chase." ?></p>
  </div>
</section>

<!-- WHY OCSAPP -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI OCSAPP" : "WHY OCSAPP" ?></div>
    <h2><?= $fr ? "Votre catalogue. Leur bon de commande." : "Your catalog. Their purchase order." ?></h2>
    <p class="section-lead"><?= $fr
      ? "OCSAPP est un écosystème numérique tout-en-un québécois connectant fournisseurs, vendeurs, entreprises et acheteurs à travers un réseau de livraison hyperlocal à objectif zéro émission."
      : "OCSAPP is an all-in-one Quebec digital ecosystem connecting suppliers, sellers, businesses, and buyers through a hyperlocal, zero-emission delivery network." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Listez une fois, vendez en continu" : "List once, sell continuously" ?></h3>
        <p><?= $fr ? "Ajoutez vos produits au catalogue OCSAPP une seule fois - avec le poids de chaque unité, un champ obligatoire. Notre réseau de distribution et de vendeurs les trouve quand ils en ont besoin, et les bons de commande arrivent automatiquement." : "Add your products to the OCSAPP catalog once - with each unit's weight, a required field. Our distribution and seller network finds them when they're needed, and purchase orders arrive automatically." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Des revenus confirmés, pas des prospects" : "Confirmed revenue, not leads" ?></h3>
        <p><?= $fr ? "Chaque notification que vous recevez est un bon de commande réel avec un montant, une quantité et une date - pas un prospect à relancer. Aucune négociation, aucun démarchage. Confirmez, préparez, encaissez." : "Every notification you receive is a real purchase order with an amount, quantity, and date - not a lead to chase. No negotiation, no cold calling. Confirm, prepare, and get paid." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "On gère chaque livraison pour vous" : "We handle every delivery for you" ?></h3>
        <p><?= $fr ? "Notre réseau de livreurs ODA passe à votre quai et livre directement à l'acheteur. Vous vous concentrez sur l'exécution - nous gérons le dernier kilomètre, à objectif zéro émission." : "Our network of ODA drivers comes to your dock and delivers directly to the buyer. You focus on fulfillment - we handle the last mile, zero-emission every time." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- TRANSPARENCY VALUE PANEL -->
<div class="reveal value-panel">
  <h2><?= $fr ? "Vendre en gros sur OCSAPP, <span>en toute transparence.</span>" : "Selling wholesale on OCSAPP, <span>fully transparent.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Ce que chaque fournisseur peut tenir pour acquis, dès le premier bon de commande." : "What every supplier can count on, from the first purchase order." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Chaque bon de commande est confirmé - montant, quantité et date connus dès la réception" : "Every purchase order is confirmed - amount, quantity, and date known upon receipt" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "OCSAPP livre 100 % de vos commandes - le réseau ODA gère le dernier kilomètre" : "OCSAPP delivers 100% of your orders - the ODA network handles the last mile" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Paiement sur termes nets convenus, avec frais de traitement toujours détaillés séparément" : "Payment on agreed net terms, with processing fees always itemized separately" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Le poids que vous déclarez ne vous coûte jamais rien - les suppléments qu'il déclenche sont payés par le client d'affaires, jamais déduits de votre paiement" : "The weight you declare never costs you anything - the surcharges it triggers are paid by the business client, never deducted from your payout" ?></p></div>
  </div>
</div>

<!-- GETTING STARTED -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "MISE EN ROUTE" : "GETTING STARTED" ?></div>
    <h2><?= $fr ? "Opérationnel en 3 étapes" : "Up and running in 3 steps" ?></h2>
    <p class="section-lead"><?= $fr ? "De votre candidature à votre premier bon de commande - voici exactement à quoi vous attendre." : "From your application to your first purchase order - here's exactly what to expect." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Postulez en 15 minutes" : "Apply in 15 minutes" ?></h3>
        <p><?= $fr ? "Remplissez votre profil, téléversez vos documents d'entreprise québécois (NEQ, certificat de constitution) et choisissez votre forfait. Aucun appel téléphonique requis." : "Fill out your profile, upload your Quebec business documents (NEQ, incorporation certificate), and choose your plan. No phone call required." ?></p>
        <span class="step-time">~15 <?= $fr ? "minutes" : "minutes" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Approuvé en 1 à 3 jours" : "Approved in 1–3 days" ?></h3>
        <p><?= $fr ? "Notre équipe examine personnellement chaque candidature. Vous recevrez une décision par courriel avec votre code fournisseur (SUP-XXXXXXXX) - votre identifiant permanent sur le réseau." : "Our team personally reviews every application. You'll get a decision by email along with your supplier code (SUP-XXXXXXXX) - your permanent identifier on the network." ?></p>
        <span class="step-time"><?= $fr ? "1 à 3 jours ouvrables" : "1–3 business days" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Recevez vos premiers bons de commande" : "Start receiving purchase orders" ?></h3>
        <p><?= $fr ? "Listez vos produits et laissez le réseau travailler. Chaque nouveau BC déclenche une alerte courriel - vous devez le confirmer ou signaler un problème dans les 24 heures suivant sa réception." : "List your products and let the network do the work. Every new PO triggers an email alert - you must confirm it or flag an issue within 24 hours of receipt." ?></p>
        <span class="step-time"><?= $fr ? "Dès le premier jour" : "From day one" ?></span>
      </article>
    </div>
  </div>
</section>

<!-- WHAT YOU GET -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "CE QUE VOUS OBTENEZ" : "WHAT YOU GET" ?></div>
    <h2><?= $fr ? "Un portail conçu pour votre réussite" : "A portal built for your success" ?></h2>
    <p class="section-lead"><?= $fr ? "Tout ce dont vous avez besoin pour gérer vos commandes, vos stocks et vos paiements - au même endroit." : "Everything you need to manage your orders, inventory, and payments - in one place." ?></p>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup1" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#17d61f"/><stop offset="1" stop-color="#08790c"/></linearGradient></defs><rect x="14" y="10" width="36" height="46" rx="6" fill="url(#sup1)"/><rect x="22" y="6" width="20" height="10" rx="3" fill="#0a8f0f"/><rect x="20" y="24" width="24" height="5" rx="2.5" fill="#eaffeb" opacity=".92"/><rect x="20" y="34" width="24" height="5" rx="2.5" fill="#eaffeb" opacity=".7"/><path d="M21 45l4 4 8-8" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Visibilité totale sur chaque commande" : "Full visibility on every order" ?></h3>
        <p><?= $fr ? "Suivez chaque bon de commande en temps réel - Confirmé, puis En préparation, puis Prêt pour ramassage. Le client d'affaires et OCSAPP ont une visibilité continue sur votre progression réelle." : "Track every purchase order in real time - Confirmed, then Preparing, then Ready for Pickup. The business client and OCSAPP have continuous visibility into your real progress." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup2" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><rect x="9" y="16" width="46" height="32" rx="6" fill="url(#sup2)"/><rect x="9" y="24" width="46" height="7" fill="#075d0a"/><circle cx="45" cy="40" r="7" fill="#eaffeb"/><path d="M42 40h6M45 37v6" stroke="#00B207" stroke-width="2.4" stroke-linecap="round"/><rect x="15" y="38" width="14" height="4" rx="2" fill="#eaffeb" opacity=".85"/></svg></span>
        <h3><?= $fr ? "Des paiements prévisibles, des termes clairs" : "Predictable payments, clear terms" ?></h3>
        <p><?= $fr ? "Suivez vos comptes clients, consultez les statuts de paiement et téléchargez vos relevés. OCSAPP règle selon des termes nets convenus - commission et frais de traitement toujours en lignes distinctes." : "Track your accounts receivable, view payment status, and download your statements. OCSAPP settles on agreed net terms - commission and processing fees always shown as separate lines." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup3" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08760c"/></linearGradient></defs><rect x="8" y="34" width="20" height="18" rx="3" fill="url(#sup3)"/><rect x="30" y="34" width="20" height="18" rx="3" fill="#0a8f0f"/><rect x="19" y="14" width="20" height="18" rx="3" fill="url(#sup3)"/><path d="M8 40h20M30 40h20M19 20h20" stroke="#eaffeb" stroke-width="2.4" opacity=".85"/></svg></span>
        <h3><?= $fr ? "Votre catalogue complet, un seul endroit" : "Your full catalog, one place" ?></h3>
        <p><?= $fr ? "Gérez vos produits, prix, formats d'emballage, poids et niveaux de stock depuis votre tableau de bord. Mises à jour en masse disponibles. Ce que les acheteurs voient reflète toujours la réalité." : "Manage your products, pricing, pack sizes, weight, and stock levels from your dashboard. Bulk updates available. What buyers see always reflects reality." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup4" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1ee626"/><stop offset="1" stop-color="#086c0b"/></linearGradient></defs><path d="M10 14h44a6 6 0 0 1 6 6v20a6 6 0 0 1-6 6H30l-12 10v-10h-8a6 6 0 0 1-6-6V20a6 6 0 0 1 6-6z" fill="url(#sup4)"/><circle cx="24" cy="30" r="3.4" fill="#eaffeb"/><circle cx="36" cy="30" r="3.4" fill="#eaffeb"/><circle cx="48" cy="30" r="3.4" fill="#eaffeb"/></svg></span>
        <h3><?= $fr ? "Une ligne directe avec notre équipe" : "A direct line to our team" ?></h3>
        <p><?= $fr ? "Communiquez directement avec l'équipe OCSAPP depuis le portail. Pas de tickets externes, pas d'attente en ligne. Chaque message est associé à votre compte." : "Message the OCSAPP team directly from the portal. No external tickets, no hold music. Every message is tied to your account." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup5" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1fe528"/><stop offset="1" stop-color="#08730c"/></linearGradient></defs><path d="M8 24h31v20H8z" fill="url(#sup5)"/><path d="M39 29h10l7 8v7H39z" fill="#0b8d10"/><circle cx="20" cy="47" r="6" fill="#082d0a"/><circle cx="47" cy="47" r="6" fill="#082d0a"/><circle cx="20" cy="47" r="2.5" fill="#baffbd"/><circle cx="47" cy="47" r="2.5" fill="#baffbd"/><path d="M44 32h5l4 5h-9z" fill="#dffff0"/></svg></span>
        <h3><?= $fr ? "Livraison gérée, empreinte minimale" : "Delivery handled, minimal footprint" ?></h3>
        <p><?= $fr ? "Notre réseau ODA ramasse les commandes à votre quai et les livre aux acheteurs. Aucune logistique propre nécessaire - objectif zéro émission sur chaque livraison." : "Our ODA network picks up orders from your dock and delivers them to buyers. No logistics of your own required - zero-emission on every delivery." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="sup6" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#23e72b"/><stop offset="1" stop-color="#086d0b"/></linearGradient></defs><circle cx="24" cy="22" r="9" fill="url(#sup6)"/><path d="M8 50c1-11 9-17 16-17s15 6 16 17z" fill="url(#sup6)"/><circle cx="44" cy="26" r="7" fill="#0a8f0f"/><path d="M34 50c1-8 6-13 13-13 6 0 11 4 12.5 10.5" fill="#0a8f0f"/></svg></span>
        <h3><?= $fr ? "Votre équipe, vos règles" : "Your team, your rules" ?></h3>
        <p><?= $fr ? "Ajoutez des utilisateurs par emplacement, définissez leurs rôles et contrôlez leur accès. Gérez plusieurs emplacements depuis un seul compte." : "Add users by location, define their roles, and control their access. Manage multiple locations from a single account." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- FEE TRANSPARENCY / TRUST -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Frais additionnels - payés par le client d'affaires, jamais déduits de votre paiement" : "Additional fees - paid by the business client, never deducted from your payout" ?></h3>
    <p><?= $fr
      ? "Certaines commandes déclenchent des frais affichés au client d'affaires avant sa confirmation. Ces frais financent la livraison - ils ne touchent jamais votre commission ni votre paiement."
      : "Some orders trigger fees disclosed to the business client before they confirm. These fees fund delivery - they never touch your commission or your payout." ?></p>
    <div class="trust-grid trust-grid-3col">
      <div class="trust-card">
        <h4><?= $fr ? "Frais de livraison zone-calibré" : "Zone-calibrated delivery fee" ?></h4>
        <p><?= $fr ? "19 $ Ouest-de-l'Île / 21 $ Laval / 24 $ Grand Montréal par bon de commande. Finance le réseau de livreurs ODA - séparé de, et jamais déduit de, votre commission." : "\$19 West Island / \$21 Laval / \$24 Greater Montreal, per purchase order. Funds the ODA driver network - separate from, and never deducted from, your commission." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément commande volumineuse" : "Oversize order surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 25 kg au total dans une demande d'Approvisionnement. Calculé automatiquement à partir du poids que vous déclarez - un champ obligatoire à la fiche produit." : "Applies above 25 kg total in an Approvisionnement request. Calculated automatically from the weight you declare - a required field on every product listing." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Frais d'arrêt additionnel" : "Additional-stop fee" ?></h4>
        <p><?= $fr ? "Lorsqu'une commande consolide vos produits avec plus de 2 autres fournisseurs, un frais par arrêt supplémentaire s'applique au client d'affaires." : "When an order consolidates your products with more than 2 other suppliers, a per-stop fee applies to the business client." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément longue distance" : "Long-distance surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 10 km de trajet entre la cueillette et la livraison. Distance calculée automatiquement à partir du trajet réel - rien à déclarer de votre part." : "Applies beyond 10 km of routed distance between pickup and delivery. Distance is calculated automatically from the actual route - nothing for you to declare." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Votre paiement reste inchangé" : "Your payout stays the same" ?></h4>
        <p><?= $fr ? "Ces frais s'ajoutent au montant payé par le client d'affaires. Vous ne les voyez jamais sur votre propre relevé - aucun ne réduit votre commission ni votre paiement net." : "These fees are added to what the business client pays. You never see them on your own statement - none of them reduce your commission or net payout." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Calcul entièrement automatique" : "Fully automatic calculation" ?></h4>
        <p><?= $fr ? "Aucun de ces suppléments n'est estimé manuellement. Le système additionne le poids déclaré et calcule la distance réelle au moment de la commande - le client d'affaires voit le total avant de confirmer." : "None of these surcharges is manually estimated. The system sums the declared weight and calculates the real route distance at order time - the business client sees the total before confirming." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ELIGIBILITY -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "CONDITIONS D'ADMISSIBILITÉ" : "ELIGIBILITY" ?></div>
    <h2><?= $fr ? "Ce qu'il faut pour postuler" : "What you need to apply" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Avant de soumettre votre candidature, assurez-vous d'avoir :" : "Before submitting your application, make sure you have:" ?></h4>
      <ul>
        <li><?= $fr ? "Un numéro d'entreprise du Québec (NEQ) valide" : "A valid Quebec enterprise number (NEQ)" ?></li>
        <li><?= $fr ? "Vos numéros d'inscription TPS / TVQ (taxes de vente fédérale et provinciale)" : "Your GST / QST registration numbers (federal and provincial sales tax)" ?></li>
        <li><?= $fr ? "Une assurance responsabilité civile générale, minimum 1 000 000 $ (garantie responsabilité produits requise pour les aliments, produits frais ou biens de consommation)" : "Commercial general liability insurance, minimum \$1,000,000 (product liability coverage required for food, produce, or consumable goods)" ?></li>
        <li><?= $fr ? "Un catalogue de produits prêt à lister, incluant le poids exact par unité" : "A product catalog ready to list, including accurate weight per unit" ?></li>
        <li><?= $fr ? "La capacité à exécuter les bons de commande et à les remettre au réseau de livreurs ODA dans les délais convenus" : "The ability to fulfill purchase orders and hand off to the ODA driver network within agreed lead times" ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- OUR SUPPLIER NETWORK -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "NOTRE RÉSEAU FOURNISSEUR" : "OUR SUPPLIER NETWORK" ?></div>
    <h2><?= $fr ? "Des fournisseurs dans toutes les catégories" : "Suppliers across every category" ?></h2>
    <p class="section-lead"><?= $fr
      ? "OCSAPP s'associe à des fournisseurs locaux au Québec dans toutes les catégories de produits - des petites entreprises familiales aux distributeurs régionaux établis."
      : "OCSAPP partners with local Quebec suppliers across every product category - from small family businesses to established regional distributors." ?></p>
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

<!-- PRICING -->
<section class="pricing">
  <div class="wrap">
    <div class="pricing-head">
      <div class="section-eyebrow"><?= $fr ? "FORFAITS FOURNISSEUR" : "SUPPLIER PLANS" ?></div>
      <h2><?= $fr ? "Choisissez le bon forfait pour votre entreprise" : "Choose the right plan for your business" ?></h2>
      <p><?= $fr ? "Démarrez gratuitement sur Essential. Passez à un forfait supérieur à tout moment pour débloquer plus d'UGS, d'outils et de fonctionnalités." : "Start free on Essential. Upgrade anytime to unlock more SKUs, tools, and features." ?></p>
    </div>
    <div class="pricing-grid">
      <div class="pricing-card">
        <h3>Essential</h3>
        <p class="pricing-tagline"><?= $fr ? "Tout ce qu'il faut pour démarrer" : "Everything you need to get started" ?></p>
        <div class="pricing-price"><?= $fr ? "Gratuit" : "Free" ?></div>
        <p class="pricing-commission"><strong>8%</strong> <?= $fr ? "taux de commission" : "commission rate" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Gestion des BC, BL &amp; factures" : "PO, BL &amp; invoice management" ?></li>
          <li><?= $fr ? "Comptes clients &amp; paiements" : "Accounts receivable &amp; payments" ?></li>
          <li><?= $fr ? "Messagerie d'assistance interne" : "Internal support messaging" ?></li>
          <li><?= $fr ? "Gestion des stocks &amp; produits" : "Stock &amp; product management" ?></li>
          <li><?= $fr ? "Barre de progression des commandes" : "Order progress bar" ?></li>
          <li><?= $fr ? "Journal d'activité &amp; courriels" : "Activity log &amp; emails" ?></li>
          <li><?= $fr ? "Partenaire réseau ODA" : "ODA network partnership" ?></li>
          <li><?= $fr ? "Publicité collaborative" : "Collaborative advertising" ?></li>
          <li><?= $fr ? "Support multi-emplacements" : "Multi-location support" ?></li>
          <li><?= $fr ? "Double accès utilisateur / emplacement" : "Dual user / location access" ?></li>
        </ul>
        <a class="pricing-cta" href="<?= url('supplier/apply') ?>?package=Essential"><?= $fr ? "Commencer gratuitement" : "Start for Free" ?></a>
      </div>
      <div class="pricing-card popular">
        <span class="pricing-badge"><?= $fr ? "Le plus populaire" : "Most Popular" ?></span>
        <h3>Experience</h3>
        <p class="pricing-tagline"><?= $fr ? "Optimisez catalogue et équipe" : "Optimize your catalog and team" ?></p>
        <div class="pricing-price">$49 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>6%</strong> <?= $fr ? "taux de commission" : "commission rate" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce qu'Essential inclut" : "Everything in Essential" ?></li>
          <li><?= $fr ? "Jusqu'à 10 000 UGS" : "Up to 10,000 SKUs" ?></li>
          <li><?= $fr ? "Rôles utilisateur &amp; limites d'accès" : "User roles &amp; access limits" ?></li>
          <li><?= $fr ? "Support prioritaire (Lun-Dim)" : "Priority support (Mon–Sun)" ?></li>
          <li><?= $fr ? "Import en masse de produits" : "Bulk product import" ?></li>
        </ul>
        <a class="pricing-cta" href="<?= url('supplier/apply') ?>?package=Experience"><?= $fr ? "Postuler" : "Apply" ?></a>
      </div>
      <div class="pricing-card">
        <h3>Prestige</h3>
        <p class="pricing-tagline"><?= $fr ? "Pour les fournisseurs à fort volume" : "For high-volume suppliers" ?></p>
        <div class="pricing-price">$79 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>5%</strong> <?= $fr ? "taux de commission" : "commission rate" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce qu'Experience inclut" : "Everything in Experience" ?></li>
          <li><?= $fr ? "UGS illimitées" : "Unlimited SKUs" ?></li>
          <li><?= $fr ? "Gestionnaire de compte dédié" : "Dedicated account manager" ?></li>
          <li><?= $fr ? "Mise en avant dans le catalogue" : "Catalog featuring" ?></li>
          <li><?= $fr ? "Traitement prioritaire des commandes" : "Priority order processing" ?></li>
        </ul>
        <a class="pricing-cta outline" href="<?= url('supplier/apply') ?>?package=Prestige"><?= $fr ? "Postuler" : "Apply" ?></a>
      </div>
      <div class="pricing-card">
        <h3><?= $fr ? "Entreprise" : "Enterprise" ?></h3>
        <p class="pricing-tagline"><?= $fr ? "Solutions sur mesure, grande échelle" : "Custom solutions, large scale" ?></p>
        <div class="pricing-price"><?= $fr ? "Sur devis" : "Custom quote" ?></div>
        <p class="pricing-commission"><?= $fr ? "Commission" : "Commission" ?> <strong><?= $fr ? "personnalisée" : "custom" ?></strong></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce que Prestige inclut" : "Everything in Prestige" ?></li>
          <li><?= $fr ? "Suivi de livraison en direct" : "Live delivery tracking" ?></li>
          <li><?= $fr ? "CRM &amp; génération de prospects" : "CRM &amp; lead generation" ?></li>
          <li><?= $fr ? "Publicité &amp; marketing" : "Advertising &amp; marketing" ?></li>
          <li><?= $fr ? "Intégration sur mesure &amp; SLA dédié" : "Custom integration &amp; dedicated SLA" ?></li>
        </ul>
        <a class="pricing-cta outline" href="mailto:suppliers@ocsapp.ca?subject=Enterprise%20Plan%20Inquiry"><?= $fr ? "Contactez-nous" : "Contact Us" ?></a>
      </div>
    </div>
    <div class="pricing-note">
      <p><strong><?= $fr ? "Remarque :" : "Note:" ?></strong> <?= $fr
        ? "Tous les nouveaux comptes démarrent sur Essential sans frais. Les forfaits Experience (49 $/mois) et Prestige (79 $/mois) sont facturés mensuellement, en plus du taux de commission réduit. Pour changer de forfait, contactez <strong>suppliers@ocsapp.ca</strong> ou votre gestionnaire de compte - les changements prennent effet dans un délai d'un jour ouvrable."
        : "All new accounts start on Essential at no cost. The Experience (\$49/month) and Prestige (\$79/month) plans are billed monthly, in addition to the reduced commission rate. To change plans, contact <strong>suppliers@ocsapp.ca</strong> or your account manager - changes take effect within one business day." ?></p>
      <p><strong><?= $fr ? "Frais de traitement des paiements :" : "Payment processing fee:" ?></strong> <?= $fr
        ? "les frais standards (2,9 % + 0,30 $ CAD) sont absorbés par le fournisseur et déduits du montant net avant paiement, aux côtés de la commission - ils ne sont jamais ajoutés comme frais séparé à la facture de l'acheteur."
        : "the standard rate (2.9% + \$0.30 CAD) is absorbed by the supplier and deducted from net proceeds before payout, alongside commission - it is never added as a separate line item to the buyer's invoice." ?></p>
    </div>
  </div>
</section>

<!-- FOUNDING PARTNER PROGRAM -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "PROGRAMME PARTENAIRE FONDATEUR" : "FOUNDING PARTNER PROGRAM" ?></div>
    <h2><?= $fr ? "Rejoignez nos 15 tout premiers fournisseurs" : "Join our first 15 suppliers" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Réservé aux 15 premiers fournisseurs activés :" : "Reserved for the first 15 activated suppliers:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr
          ? "Taux de commission Prestige verrouillé 6 mois - 5 %, 0 $ de frais mensuels (le forfait Prestige coûte normalement 79 $/mois)."
          : "Prestige-tier commission locked for 6 months - 5%, $0 monthly fee (the Prestige plan normally costs $79/month)." ?></li>
        <li><?= $fr
          ? "Configuration de catalogue gratuite et un insigne Partenaire Fondateur permanent sur votre profil."
          : "Free catalog setup and a permanent Founding Partner badge on your profile." ?></li>
        <li><?= $fr
          ? "Aucune démarche à faire : votre statut est confirmé automatiquement à l'activation de votre compte, tant que la cohorte n'est pas fermée."
          : "Nothing to request: your status is confirmed automatically when your account is activated, as long as the cohort isn't already closed." ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="wrap">
    <div class="faq-head">
      <div class="section-eyebrow">FAQ</div>
      <h2><?= $fr ? "Questions fréquentes" : "Frequently Asked Questions" ?></h2>
      <p><?= $fr ? "Tout ce que vous devez savoir avant de postuler." : "Everything you need to know before applying." ?></p>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je utiliser un courriel différent de celui avec lequel j'ai été invité ?" : "Can I use a different email than the one I was invited with?" ?></h4>
        <p><?= $fr ? "Oui. Si vous avez reçu une invitation directe, vous pouvez vous inscrire avec n'importe quel courriel. L'adresse enregistrée devient votre identifiant de connexion et l'endroit où toutes les notifications du portail sont envoyées." : "Yes. If you received a direct invitation, you can register with any email. The registered address becomes your login and where all portal notifications are sent." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Combien de temps dure le processus d'approbation ?" : "How long does the approval process take?" ?></h4>
        <p><?= $fr ? "La plupart des candidatures sont examinées dans un délai de 1 à 3 jours ouvrables. Vous serez informé par courriel. Si vous n'avez pas eu de nouvelles après 3 jours ouvrables, contactez suppliers@ocsapp.ca avec votre numéro de référence." : "Most applications are reviewed within 1–3 business days. You'll be notified by email. If you haven't heard back after 3 business days, contact suppliers@ocsapp.ca with your reference number." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je me connecter au portail avant d'être approuvé ?" : "Can I log in to the portal before I'm approved?" ?></h4>
        <p><?= $fr ? "Oui - immédiatement après avoir soumis votre candidature. L'accès est limité pendant l'examen : vous pouvez explorer le portail, mais vous ne pouvez pas lister de produits ni recevoir de bons de commande tant que votre compte n'est pas pleinement activé." : "Yes - immediately after submitting your application. Access is limited during review: you can explore the portal, but you cannot list products or receive purchase orders until your account is fully activated." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Qu'est-ce qu'un code fournisseur et pourquoi en ai-je besoin ?" : "What's a supplier code and why do I need one?" ?></h4>
        <p><?= $fr ? "Votre code fournisseur (format : SUP-XXXXXXXX) est votre identifiant unique sur le réseau OCSAPP. Il apparaît dans votre courriel de confirmation et sur votre tableau de bord. Incluez-le toujours lorsque vous contactez notre équipe d'assistance." : "Your supplier code (format: SUP-XXXXXXXX) is your unique identifier on the OCSAPP network. It appears in your confirmation email and on your dashboard. Always include it when contacting our support team." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment et quand suis-je payé ?" : "How and when do I get paid?" ?></h4>
        <p><?= $fr ? "Les modalités de paiement sont convenues lors de l'intégration. OCSAPP traite les paiements selon des termes nets convenus (généralement net-30) après confirmation de l'exécution du bon de commande. Les frais de traitement des paiements (2,9 % + 0,30 $ CAD) sont déduits séparément de votre commission, jamais facturés à l'acheteur. Les factures et le statut des paiements sont visibles dans le portail sous Comptes clients." : "Payment terms are agreed during onboarding. OCSAPP processes payments on agreed net terms (typically net-30) after purchase order fulfillment is confirmed. Payment processing fees (2.9% + \$0.30 CAD) are deducted separately from your commission, never charged to the buyer. Invoices and payment status are visible in the portal under Accounts Receivable." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je changer de forfait à tout moment ?" : "Can I change plans at any time?" ?></h4>
        <p><?= $fr ? "Oui. Contactez suppliers@ocsapp.ca ou votre gestionnaire de compte. Les changements prennent effet dans un délai d'un jour ouvrable. Tous les forfaits sont mensuels, sans engagement à long terme." : "Yes. Contact suppliers@ocsapp.ca or your account manager. Changes take effect within one business day. All plans are month-to-month, with no long-term commitment." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Qu'est-ce que le champ « poids » et pourquoi dois-je le remplir ?" : "What's the \"weight\" field, and why is it required?" ?></h4>
        <p><?= $fr ? "Le poids est un champ obligatoire sur chaque fiche produit - pas un détail facultatif. OCSAPP additionne le poids que vous déclarez, avec celui des autres fournisseurs consolidés dans une même demande d'Approvisionnement, pour déterminer si un supplément pour commande volumineuse s'applique au client d'affaires. Vous ne voyez jamais ce supplément sur votre propre paiement - il est entièrement financé par le client d'affaires. Si un livreur constate à la cueillette un écart important avec le poids déclaré, OCSAPP peut ajuster le supplément rétroactivement à partir d'une preuve photo ou de balayage." : "Weight is a mandatory field on every product listing - not an optional detail. OCSAPP sums the weight you declare, together with other suppliers consolidated into the same Approvisionnement request, to determine whether an oversize order surcharge applies to the business client. You never see this surcharge on your own payout - it is entirely funded by the business client. If a driver's inspection at pickup shows a material discrepancy with the declared weight, OCSAPP may adjust the surcharge retroactively based on photo or scan evidence." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Combien de temps ai-je pour confirmer un bon de commande ?" : "How long do I have to confirm a purchase order?" ?></h4>
        <p><?= $fr ? "Vous devez confirmer ou signaler un problème avec un bon de commande dans les 24 heures suivant sa réception. Ne confirmez pas un bon de commande que vous ne comptez pas exécuter intégralement - une exécution partielle doit être communiquée à OCSAPP avant la confirmation. Une fois confirmée, la commande doit être remise au réseau ODA dans le délai indiqué sur votre fiche produit." : "You must confirm or flag an issue with a purchase order within 24 hours of receipt. Don't confirm a purchase order you don't intend to fulfill in full - partial fulfillment must be communicated to OCSAPP before confirmation. Once confirmed, the order must be handed off to the ODA network within the lead time stated on your product listing." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Que se passe-t-il si un client signale un problème avec un bon de commande ?" : "What happens if a client reports an issue with a purchase order?" ?></h4>
        <p><?= $fr ? "Les réclamations (manquant, dommage, défaut, exécution incorrecte) sont gérées par la Politique de retours et remboursements d'OCSAPP, Volet B. Le client d'affaires dispose de 48 heures après la livraison pour signaler un problème. Si le système détermine, à partir d'une preuve photo et de balayage, que le problème est survenu avant la cueillette (de votre responsabilité), le montant réclamé et les frais de logistique inverse applicables sont déduits de votre prochain paiement. Vous n'êtes jamais facturé pour un problème survenu après la prise en charge par le livreur - et vous disposez de 5 jours ouvrables pour contester une déduction directement depuis le portail." : "Claims (shortage, damage, defect, incorrect fulfillment) are governed by OCSAPP's Returns &amp; Refund Policy, Track B. The business client has 48 hours after delivery to report an issue. If the system determines, from photo and scan evidence, that the issue occurred before pickup (your responsibility), the claimed value and any applicable reverse-logistics fee are deducted from your next payout. You're never charged for an issue that occurred after the driver took custody - and you have 5 business days to dispute a deduction directly from the portal." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment sont résolus les différends avec OCSAPP ?" : "How are disputes with OCSAPP resolved?" ?></h4>
        <p><?= $fr
          ? "OCSAPP et vous tenterez d'abord de résoudre tout différend par la négociation de bonne foi. Si le différend n'est pas résolu dans les 30 jours, il peut être soumis aux tribunaux du district judiciaire de Montréal, Québec, à la compétence exclusive desquels vous et OCSAPP vous soumettez. OCSAPP n'exige aucun arbitrage obligatoire dans votre entente."
          : "OCSAPP and you will first attempt to resolve any dispute through good-faith negotiation. If unresolved within 30 days, it may be submitted to the courts of the judicial district of Montréal, Québec, to whose exclusive jurisdiction you and OCSAPP submit. OCSAPP does not require mandatory arbitration in your agreement." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Y a-t-il des exigences d'étiquetage en français pour mes produits ?" : "Are there French labelling requirements for my products?" ?></h4>
        <p><?= $fr
          ? "Depuis le 1er juin 2025, les termes génériques ou descriptifs associés à une marque de commerce - par exemple un nom de saveur ou un ingrédient - doivent apparaître en français sur le produit lui-même, même si la marque elle-même peut demeurer dans une autre langue. Une période de transition s'applique jusqu'au 1er juin 2027 pour les produits fabriqués avant le 1er juin 2025. Cette exigence concerne particulièrement les produits alimentaires, les boissons, et les soins personnels. Vous êtes responsable de la conformité de l'emballage de vos produits avant de les lister sur la Plateforme."
          : "Since June 1, 2025, generic or descriptive terms tied to a trademark - like a flavour name or an ingredient - must appear in French on the product itself, even if the trademark itself can stay in another language. A transition period runs until June 1, 2027 for products manufactured before June 1, 2025. This requirement is especially relevant for food, beverage, and personal care products. You're responsible for your product packaging being compliant before listing it on the Platform." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment fonctionne le programme Partenaire Fondateur ?" : "How does the Founding Partner Program work?" ?></h4>
        <p><?= $fr
          ? "Les 15 premiers fournisseurs activés sur OCSAPP obtiennent automatiquement le statut de Partenaire Fondateur - aucune candidature séparée requise. Vous obtenez le taux Prestige (5 %) verrouillé 6 mois sans frais mensuel, et un insigne permanent. Votre position dans la cohorte est confirmée à l'activation de votre compte et visible sur votre tableau de bord."
          : "The first 15 activated suppliers on OCSAPP automatically get Founding Partner status - no separate application needed. You get the Prestige rate (5%) locked for 6 months at no monthly fee, and a permanent badge. Your cohort position is confirmed when your account is activated and shown on your dashboard." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SUPPORT -->
<section class="support">
  <div class="wrap">
    <div class="support-grid">
      <div class="support-card">
        <h4><?= $fr ? "Support fournisseur" : "Supplier Support" ?></h4>
        <p class="support-main"><a href="mailto:suppliers@ocsapp.ca">suppliers@ocsapp.ca</a></p>
        <p><?= $fr ? "Toutes les demandes fournisseur" : "All supplier inquiries" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Téléphone" : "Phone" ?></h4>
        <p class="support-main">514-746-3789</p>
        <p><?= $fr ? "Lun–Dim · 7h – 23h" : "Mon–Sun · 7am – 11pm" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Portail fournisseur" : "Supplier Portal" ?></h4>
        <p class="support-main">ocsapp.ca/supplier/login</p>
        <p><?= $fr ? "Connexion &amp; tableau de bord" : "Login &amp; dashboard" ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="wrap">
    <h2><?= $fr ? "Votre prochain bon de commande vous attend." : "Your next purchase order is waiting." ?></h2>
    <p><?= $fr ? "Postulez en 15 minutes. Approuvé en 1 à 3 jours. Sans frais pour commencer." : "Apply in 15 minutes. Approved in 1–3 days. No cost to get started." ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('supplier/apply') ?>"><?= $fr ? "Commencer votre candidature" : "Start Your Application" ?></a>
      <a class="btn-secondary" href="<?= url('contact') ?>"><?= $fr ? "Contacter notre équipe" : "Contact Our Team" ?></a>
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
        <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
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
