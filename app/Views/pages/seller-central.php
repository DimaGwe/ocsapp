<?php
/**
 * OCSAPP Seller Central - marketing landing page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "seller-central FINAL PACKAGE UPDATE"
 * (Updates.zip): weight-field messaging, buyer-paid surcharge disclosure,
 * Monday payout terms, expanded FAQ.
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? "Espace Vendeur - Ouvrez votre boutique - OCSAPP" : "Seller Central - Open Your Shop - OCSAPP" ?></title>
  <meta name="description" content="<?= $fr
    ? "Ouvrez votre boutique sur la Marketplace OCSAPP : 4 forfaits dès 0 $, commission fixe et affichée, livraison zéro émission incluse."
    : "Open your shop on the OCSAPP Marketplace: 4 plans starting at $0, fixed disclosed commission, zero-emission delivery included." ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/seller-central.css') ?>">
</head>
<body class="seller-central-page<?= $fr ? ' lang-fr' : '' ?>">
<?php include __DIR__ . '/../components/header.php'; ?>

<div class="beta-strip"><p><?= $fr
  ? "⚠️ Version bêta - veuillez ne pas effectuer d'achats réels pour le moment"
  : "⚠️ Beta version - please do not make real purchases at this time" ?></p></div>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <span class="eyebrow"><?= $fr ? "PROGRAMME VENDEUR" : "SELLER PROGRAM" ?></span>
    <h1><?= $fr ? "Ouvrez votre boutique sur la <span>Marketplace OCSAPP</span>" : "Open Your Shop on the <span>OCSAPP Marketplace</span>" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Rejoignez le marché hyperlocal en pleine croissance dans l'Ouest-de-l'Île, avec Laval et le centre-ville de Montréal à venir bientôt. Listez vos produits, gérez vos commandes, et laissez OCSAPP s'occuper de la livraison - depuis un seul tableau de bord."
      : "Join the growing hyperlocal marketplace in the West Island, with Laval and downtown Montreal coming soon. List your products, manage your orders, and let OCSAPP handle delivery - from a single dashboard." ?></p>
    <div class="hero-actions">
      <a class="btn" href="<?= url('register') ?>?role=seller"><?= $fr ? "Ouvrir ma boutique - c'est gratuit →" : "Open My Shop - It's Free →" ?></a>
      <a class="btn-secondary" href="<?= url('seller/login') ?>"><?= $fr ? "Connexion vendeur" : "Seller Login" ?></a>
    </div>
    <div class="hero-proof-row">
      <div class="hero-proof-item"><strong><?= $fr ? "2–5 jours" : "2–5 days" ?></strong><span><?= $fr ? "délai d'approbation" : "approval time" ?></span></div>
      <div class="hero-proof-item"><strong><?= $fr ? "4 forfaits" : "4 plans" ?></strong><span><?= $fr ? "dès 0$, sans engagement" : "starting at $0, no commitment" ?></span></div>
      <div class="hero-proof-item"><strong>100%</strong><span><?= $fr ? "des livraisons assurées par OCSAPP" : "of deliveries handled by OCSAPP" ?></span></div>
    </div>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p><?= $fr
      ? "Votre commission est <span>fixe et affichée avant votre inscription</span> - jamais un pourcentage caché derrière un forfait « à partir de »."
      : "Your commission is <span>fixed and disclosed before you sign up</span> - never a percentage hidden behind a \"starting at\" plan." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Vendre localement ne devrait pas coûter 30 % de commission." : "Selling locally shouldn't cost you 30% in commission." ?></h2>
    <p><?= $fr
      ? "Sur les grandes plateformes de livraison, une commission de 15 à 30 % ampute chaque vente avant même de couvrir vos frais. OCSAPP a été bâti pour les commerces indépendants du Québec : des forfaits qui commencent à 15 % et descendent jusqu'à 10 %, une commission toujours affichée avant votre inscription, et un réseau de livraison zéro émission inclus - jamais facturé en double."
      : "On the big delivery platforms, a 15–30% commission eats into every sale before you've even covered your own costs. OCSAPP was built for independent Quebec businesses: plans that start at 15% and drop as low as 10%, a commission that's always disclosed before you sign up, and a zero-emission delivery network included - never billed twice." ?></p>
  </div>
</section>

<!-- WHY OCSAPP -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI OCSAPP" : "WHY OCSAPP" ?></div>
    <h2><?= $fr ? "Vendre sur OCSAPP, c'est simple." : "Selling on OCSAPP is simple." ?></h2>
    <p class="section-lead"><?= $fr
      ? "OCSAPP est un écosystème numérique tout-en-un québécois connectant vendeurs et acheteurs à travers un réseau de livraison hyperlocal à objectif zéro émission."
      : "OCSAPP is an all-in-one Quebec digital ecosystem connecting sellers and buyers through a hyperlocal, zero-emission delivery network." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Listez vos produits" : "List your products" ?></h3>
        <p><?= $fr
          ? "Créez votre boutique de marque en quelques minutes. Téléversez vos produits, fixez vos prix, ajoutez vos photos - et le poids de chaque article, un champ obligatoire utilisé pour calculer automatiquement les frais de livraison."
          : "Create your branded storefront in minutes. Upload your products, set your prices, add your photos - and each item's weight, a required field used to automatically calculate delivery fees." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Les acheteurs vous découvrent" : "Buyers discover you" ?></h3>
        <p><?= $fr
          ? "Les clients qui naviguent sur la marketplace trouvent votre boutique, ajoutent au panier et passent commande. Vous êtes notifié dès qu'une commande arrive - confirmez et préparez pour le ramassage."
          : "Customers browsing the marketplace find your shop, add to cart, and place an order. You're notified the moment an order comes in - confirm it and prepare it for pickup." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "OCSAPP livre pour vous" : "OCSAPP delivers for you" ?></h3>
        <p><?= $fr
          ? "Notre réseau de chauffeurs ODA passe chez vous et livre aux acheteurs - 100 % des livraisons, sans exception. Vous gérez le produit, nous gérons le dernier kilomètre, avec suivi en direct."
          : "Our network of ODA drivers picks up from your shop and delivers to buyers - 100% of deliveries, no exceptions. You handle the product, we handle the last mile, with live tracking throughout." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- VALUE PANEL -->
<div class="value-panel">
  <h2><?= $fr ? "Vendre sur OCSAPP, <span>en toute transparence.</span>" : "Selling on OCSAPP, <span>fully transparent.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Ce que chaque vendeur peut tenir pour acquis, dès le premier jour." : "What every seller can count on, from day one." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Aucuns frais pour commencer - forfait Essential gratuit, à vie" : "No cost to get started - Essential plan is free, forever" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "OCSAPP livre 100 % de vos commandes - aucune logistique à gérer vous-même" : "OCSAPP delivers 100% of your orders - no logistics to manage yourself" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Versements chaque lundi - commission et frais de traitement toujours détaillés séparément" : "Payouts every Monday - commission and processing fees always itemized separately" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Votre commission ne change jamais selon le mode de livraison ni les frais applicables à l'acheteur" : "Your commission never changes based on delivery method or buyer-facing fees" ?></p></div>
  </div>
</div>

<!-- GETTING STARTED -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "MISE EN ROUTE" : "GETTING STARTED" ?></div>
    <h2><?= $fr ? "Opérationnel en 3 étapes" : "Up and running in 3 steps" ?></h2>
    <p class="section-lead"><?= $fr
      ? "De votre inscription à votre première vente - voici exactement à quoi vous attendre."
      : "From sign-up to your first sale - here's exactly what to expect." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Inscrivez-vous gratuitement" : "Sign up for free" ?></h3>
        <p><?= $fr
          ? "Créez votre compte vendeur en quelques minutes. Renseignez vos informations, décrivez vos produits et choisissez votre forfait. Aucun appel téléphonique requis."
          : "Create your seller account in minutes. Enter your business info, describe your products, and choose your plan. No phone call required." ?></p>
        <span class="step-time">~5 minutes</span>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Approuvé en 2 à 5 jours" : "Approved in 2–5 days" ?></h3>
        <p><?= $fr
          ? "Notre équipe examine chaque candidature personnellement. Vous recevrez une décision par courriel - et pouvez explorer le tableau de bord dès votre inscription."
          : "Our team personally reviews every application. You'll get a decision by email - and can explore the dashboard as soon as you sign up, while your application is under review." ?></p>
        <span class="step-time"><?= $fr ? "2 à 5 jours ouvrables" : "2–5 business days" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Commencez à vendre" : "Start selling" ?></h3>
        <p><?= $fr
          ? "Votre boutique est en ligne sur OCSAPP. Ajoutez vos produits, recevez des commandes et laissez les chauffeurs ODA s'occuper de la livraison - vous encaissez."
          : "Your shop goes live on OCSAPP. Add your products, receive orders, and let ODA drivers handle delivery - you collect the payout." ?></p>
        <span class="step-time"><?= $fr ? "Dès le premier jour" : "From day one" ?></span>
      </article>
    </div>
  </div>
</section>

<!-- DASHBOARD FEATURES -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "CE QUE VOUS OBTENEZ" : "WHAT YOU GET" ?></div>
    <h2><?= $fr ? "Un tableau de bord conçu pour votre succès" : "A dashboard built for your success" ?></h2>
    <p class="section-lead"><?= $fr
      ? "Tout ce dont vous avez besoin pour gérer votre boutique, vos commandes et vos revenus - au même endroit."
      : "Everything you need to manage your shop, your orders, and your revenue - in one place." ?></p>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g1" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#17d61f"/><stop offset="1" stop-color="#08790c"/></linearGradient></defs><path d="M12 27h40v25H12z" fill="url(#g1)"/><path d="M8 27l7-14h34l7 14z" fill="#00B207"/><path d="M18 33h10v19H18zM35 33h11v9H35z" fill="#fff" opacity=".92"/><path d="M15 13h34" stroke="#b7ffba" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Votre boutique de marque" : "Your branded storefront" ?></h3>
        <p><?= $fr
          ? "Une page boutique dédiée, visible par tous les acheteurs OCSAPP. Logo, bannière, description - une identité que vos clients reconnaissent."
          : "A dedicated shop page, visible to every OCSAPP buyer. Logo, banner, description - an identity your customers recognize and come back to." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g2" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><path d="M12 20l20-10 20 10-20 10z" fill="#36ef3d"/><path d="M12 20v25l20 10V30z" fill="url(#g2)"/><path d="M52 20v25L32 55V30z" fill="#0a8f0f"/><path d="M22 17l20 10" stroke="#eaffeb" stroke-width="3" opacity=".8"/></svg></span>
        <h3><?= $fr ? "Gestion des commandes" : "Order management" ?></h3>
        <p><?= $fr
          ? "Recevez, confirmez et gérez chaque commande depuis un seul écran. Mettez à jour le statut (« En préparation », puis « Prêt pour ramassage ») pour que le suivi de l'acheteur reflète une progression réelle."
          : "Receive, confirm, and manage every order from one screen. Update the status (\"Processing,\" then \"Ready for Pickup\") so the buyer's tracking reflects genuine progress, not a static screen." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g3" x1="0" y1="0" x2="0" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08760c"/></linearGradient></defs><path d="M13 49V29h9v20zm14 0V18h9v31zm14 0V11h9v38z" fill="url(#g3)"/><path d="M9 52h46" stroke="#075d0a" stroke-width="4" stroke-linecap="round"/><circle cx="45" cy="17" r="7" fill="#eaffeb" opacity=".95"/><path d="M42 17l2 2 4-5" fill="none" stroke="#00B207" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Analytiques &amp; ventes" : "Analytics &amp; sales" ?></h3>
        <p><?= $fr
          ? "Suivez vos meilleurs produits, vos tendances de revenus et le comportement de vos clients. Prenez des décisions éclairées grâce aux données."
          : "Track your top-selling products, revenue trends, and customer behavior. Make informed decisions on pricing and promotions with real data." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g4" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1ee626"/><stop offset="1" stop-color="#086c0b"/></linearGradient></defs><rect x="10" y="17" width="44" height="31" rx="6" fill="url(#g4)"/><rect x="15" y="23" width="34" height="7" rx="3.5" fill="#e9ffea" opacity=".92"/><circle cx="21" cy="39" r="4" fill="#fff"/><path d="M32 39h13" stroke="#d9ffda" stroke-width="3" stroke-linecap="round"/><path d="M25 12h14" stroke="#00B207" stroke-width="4" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Revenus &amp; versements" : "Revenue &amp; payouts" ?></h3>
        <p><?= $fr
          ? "Versements chaque lundi, dépôt direct. Les montants sous 25 $ sont reportés à la semaine suivante. Relevé détaillé - commission et frais de traitement en lignes distinctes."
          : "Payouts every Monday, direct deposit. Amounts under $25 roll over to the following week. Detailed statement - commission and processing fees shown as separate line items." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g5" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1fe528"/><stop offset="1" stop-color="#08730c"/></linearGradient></defs><path d="M8 24h31v20H8z" fill="url(#g5)"/><path d="M39 29h10l7 8v7H39z" fill="#0b8d10"/><circle cx="20" cy="47" r="6" fill="#082d0a"/><circle cx="47" cy="47" r="6" fill="#082d0a"/><circle cx="20" cy="47" r="2.5" fill="#baffbd"/><circle cx="47" cy="47" r="2.5" fill="#baffbd"/><path d="M44 32h5l4 5h-9z" fill="#dffff0"/></svg></span>
        <h3><?= $fr ? "Livraison ODA incluse" : "ODA delivery included" ?></h3>
        <p><?= $fr
          ? "Les chauffeurs ODA ramassent les commandes chez vous et les livrent aux acheteurs. Aucune logistique propre requise - objectif zéro émission sur chaque livraison."
          : "ODA drivers pick up orders from your shop and deliver them to buyers. No logistics of your own required - zero-emission on every delivery." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="g6" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#23e72b"/><stop offset="1" stop-color="#086d0b"/></linearGradient></defs><rect x="19" y="7" width="26" height="50" rx="7" fill="url(#g6)"/><rect x="23" y="13" width="18" height="34" rx="3" fill="#f6fff6"/><circle cx="32" cy="52" r="2.3" fill="#d6ffda"/><path d="M27 26h10M27 32h7" stroke="#00B207" stroke-width="2.6" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Gestion depuis mobile" : "Manage from mobile" ?></h3>
        <p><?= $fr
          ? "Votre tableau de bord complet, accessible sur n'importe quel appareil. Confirmez des commandes et suivez vos revenus où que vous soyez."
          : "Your full dashboard, accessible from any device. Confirm orders and track your revenue wherever you are." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- BUYER-PAID SURCHARGES -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Frais additionnels - payés par l'acheteur, jamais par vous" : "Additional fees - paid by the buyer, never by you" ?></h3>
    <p><?= $fr
      ? "Certaines commandes déclenchent un supplément affiché à l'acheteur avant qu'il confirme son panier. Ces frais financent la livraison - ils ne touchent jamais votre commission."
      : "Some orders trigger a surcharge disclosed to the buyer before they confirm their cart. These fees fund delivery - they never touch your commission." ?></p>
    <div class="trust-grid">
      <div class="trust-card">
        <h4><?= $fr ? "Supplément commande volumineuse" : "Oversize order surcharge" ?></h4>
        <p><?= $fr
          ? "Au-delà de 15 kg au total dans le panier. Calculé automatiquement à partir du poids que vous déclarez pour chaque produit - un champ obligatoire à la fiche produit."
          : "Applies above 15 kg total in the cart. Calculated automatically from the weight you declare for each product - a required field on every product listing." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Frais d'arrêt additionnel" : "Additional-stop fee" ?></h4>
        <p><?= $fr
          ? "Lorsqu'une commande multi-boutiques regroupe plus de 2 vendeurs, un frais par arrêt supplémentaire s'applique à l'acheteur pour chaque boutique au-delà des deux premières."
          : "When a multi-shop order draws from more than 2 sellers, a per-stop fee applies to the buyer for each shop beyond the first two." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément longue distance" : "Long-distance surcharge" ?></h4>
        <p><?= $fr
          ? "Au-delà de 8 km de livraison dans votre zone. La distance est calculée automatiquement à partir du trajet réel - rien à déclarer de votre part."
          : "Applies beyond 8 km of delivery within your zone. Distance is calculated automatically from the actual route - nothing for you to declare." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Votre commission reste inchangée" : "Your commission stays the same" ?></h4>
        <p><?= $fr
          ? "Ces trois frais s'ajoutent au montant payé par l'acheteur. Aucun ne réduit ni n'augmente le pourcentage de commission de votre forfait."
          : "All three fees are added to what the buyer pays. None of them reduce or increase your plan's commission percentage." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="pricing">
  <div class="wrap">
    <div class="pricing-head">
      <div class="section-eyebrow"><?= $fr ? "FORFAITS VENDEUR" : "SELLER PLANS" ?></div>
      <h2><?= $fr ? "Choisissez le bon forfait pour votre boutique" : "Choose the right plan for your shop" ?></h2>
      <p><?= $fr
        ? "Démarrez gratuitement sur Essential. Passez à niveau à tout moment pour débloquer plus d'outils et de visibilité."
        : "Start free on Essential. Upgrade anytime to unlock more tools and visibility." ?></p>
    </div>
    <div class="pricing-grid">
      <div class="pricing-card">
        <h3>Essential</h3>
        <p class="pricing-tagline"><?= $fr ? "Tout ce qu'il faut pour ouvrir" : "Everything you need to open" ?></p>
        <div class="pricing-price"><?= $fr ? "Gratuit" : "Free" ?></div>
        <p class="pricing-commission"><strong>15%</strong> <?= $fr ? "commission livraison" : "delivery commission" ?></p>
        <p class="pricing-commission"><strong>8%</strong> <?= $fr ? "commission ramassage" : "pickup commission" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Jusqu'à 30 produits actifs" : "Up to 30 active products" ?></li>
          <li><?= $fr ? "Boutique de marque OCSAPP" : "Branded OCSAPP storefront" ?></li>
          <li><?= $fr ? "Tableau de bord de commandes" : "Order dashboard" ?></li>
          <li><?= $fr ? "Gestion des stocks" : "Inventory management" ?></li>
          <li><?= $fr ? "Accès réseau livraison ODA" : "ODA delivery network access" ?></li>
          <li><?= $fr ? "Suivi des versements" : "Payout tracking" ?></li>
          <li><?= $fr ? "Messagerie client" : "Customer messaging" ?></li>
          <li><?= $fr ? "Gestion depuis mobile" : "Mobile management" ?></li>
        </ul>
        <a class="pricing-cta" href="<?= url('register') ?>?role=seller"><?= $fr ? "Commencer gratuitement" : "Start for Free" ?></a>
      </div>
      <div class="pricing-card popular">
        <span class="pricing-badge"><?= $fr ? "Le plus populaire" : "Most Popular" ?></span>
        <h3>Experience</h3>
        <p class="pricing-tagline"><?= $fr ? "Pour les boutiques en croissance" : "For growing shops" ?></p>
        <div class="pricing-price">$39 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>12%</strong> <?= $fr ? "commission livraison" : "delivery commission" ?></p>
        <p class="pricing-commission"><strong>6%</strong> <?= $fr ? "commission ramassage" : "pickup commission" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce qu'Essential inclut" : "Everything in Essential" ?></li>
          <li><?= $fr ? "Produits illimités" : "Unlimited products" ?></li>
          <li><?= $fr ? "Analytiques avancées" : "Advanced analytics" ?></li>
          <li><?= $fr ? "Outils de promotions" : "Promotional tools" ?></li>
          <li><?= $fr ? "Support vendeur prioritaire" : "Priority seller support" ?></li>
          <li><?= $fr ? "Taux de commission réduit" : "Reduced commission rate" ?></li>
        </ul>
        <a class="pricing-cta" href="mailto:sellers@ocsapp.ca?subject=Experience%20Plan%20Inquiry"><?= $fr ? "Postuler" : "Apply" ?></a>
      </div>
      <div class="pricing-card">
        <h3>Prestige</h3>
        <p class="pricing-tagline"><?= $fr ? "Pour les vendeurs établis" : "For established sellers" ?></p>
        <div class="pricing-price">$89 <span>/ <?= $fr ? "mois" : "month" ?></span></div>
        <p class="pricing-commission"><strong>10%</strong> <?= $fr ? "commission livraison" : "delivery commission" ?></p>
        <p class="pricing-commission"><strong>5%</strong> <?= $fr ? "commission ramassage" : "pickup commission" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce qu'Experience inclut" : "Everything in Experience" ?></li>
          <li><?= $fr ? "Placement en vedette" : "Featured placement" ?></li>
          <li><?= $fr ? "Bannières publicitaires" : "Advertising banners" ?></li>
          <li><?= $fr ? "Gestionnaire de compte dédié" : "Dedicated account manager" ?></li>
          <li><?= $fr ? "Rapports de ventes avancés" : "Advanced sales reports" ?></li>
          <li><?= $fr ? "Commission la plus basse" : "Lowest commission" ?></li>
        </ul>
        <a class="pricing-cta outline" href="mailto:sellers@ocsapp.ca?subject=Prestige%20Plan%20Inquiry"><?= $fr ? "Postuler" : "Apply" ?></a>
      </div>
      <div class="pricing-card">
        <h3>Enterprise</h3>
        <p class="pricing-tagline"><?= $fr ? "Solutions sur mesure, grande échelle" : "Custom solutions, large scale" ?></p>
        <div class="pricing-price"><?= $fr ? "Sur devis" : "Custom quote" ?></div>
        <p class="pricing-commission"><?= $fr ? "Commission <strong>personnalisée</strong>" : "Commission <strong>custom</strong>" ?></p>
        <p class="pricing-commission"><?= $fr ? "Ramassage <strong>personnalisé</strong>" : "Pickup <strong>custom</strong>" ?></p>
        <ul class="pricing-list">
          <li><?= $fr ? "Tout ce que Prestige inclut" : "Everything in Prestige" ?></li>
          <li><?= $fr ? "Intégration sur mesure" : "Custom integration" ?></li>
          <li><?= $fr ? "Gestion multi-emplacements" : "Multi-location management" ?></li>
          <li><?= $fr ? "Équipe de succès dédiée" : "Dedicated success team" ?></li>
          <li><?= $fr ? "Intégration accompagnée" : "Guided onboarding" ?></li>
        </ul>
        <a class="pricing-cta outline" href="mailto:sellers@ocsapp.ca?subject=Enterprise%20Plan%20Inquiry"><?= $fr ? "Nous contacter" : "Contact Us" ?></a>
      </div>
    </div>
    <div class="pricing-note">
      <p><strong><?= $fr ? "Remarque :" : "Note:" ?></strong> <?= $fr
        ? "Tous les nouveaux comptes démarrent sur Essential sans frais. Les forfaits Experience (39 $/mois) et Prestige (89 $/mois) sont facturés mensuellement, en plus du taux de commission réduit. Pour passer à niveau, contactez <strong>sellers@ocsapp.ca</strong> ou votre gestionnaire de compte - les changements prennent effet dans un délai d'un jour ouvrable."
        : "All new accounts start on Essential at no cost. The Experience ($39/month) and Prestige ($89/month) plans are billed monthly, in addition to the reduced commission rate. To upgrade, contact <strong>sellers@ocsapp.ca</strong> or your account manager - changes take effect within one business day." ?></p>
      <p><strong><?= $fr ? "Frais de traitement des paiements :" : "Payment processing fee:" ?></strong> <?= $fr
        ? "les frais standards (2,9 % + 0,30 $ CAD) sont absorbés par le vendeur et déduits du montant net avant paiement, aux côtés de la commission - ils ne sont jamais ajoutés comme frais séparé à la facture de l'acheteur."
        : "the standard rate (2.9% + $0.30 CAD) is absorbed by the seller and deducted from net proceeds before payout, alongside commission - it is never added as a separate line item to the buyer's bill." ?></p>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="faq">
  <div class="wrap">
    <div class="faq-head">
      <div class="section-eyebrow">FAQ</div>
      <h2><?= $fr ? "Questions fréquentes" : "Frequently Asked Questions" ?></h2>
      <p><?= $fr ? "Tout ce que vous devez savoir avant d'ouvrir votre boutique." : "Everything you need to know before opening your shop." ?></p>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <h4><?= $fr ? "Combien ça coûte d'ouvrir une boutique sur OCSAPP ?" : "How much does it cost to open a shop on OCSAPP?" ?></h4>
        <p><?= $fr
          ? "Rien. Le forfait Essential est entièrement gratuit. Vous payez seulement lorsque vous vendez - OCSAPP prélève une petite commission par vente complétée. Les forfaits payants (Experience à 39 $/mois, Prestige à 89 $/mois) offrent des produits illimités, un taux de commission réduit et des outils supplémentaires."
          : "Nothing. The Essential plan is entirely free. You only pay when you sell - OCSAPP takes a small commission on each completed sale. Paid plans (Experience at $39/month, Prestige at $89/month) offer unlimited products, a reduced commission rate, and additional tools." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Combien de temps dure l'approbation ?" : "How long does approval take?" ?></h4>
        <p><?= $fr
          ? "La plupart des candidatures sont examinées dans un délai de 2 à 5 jours ouvrables. Vous serez notifié par courriel. Si vous n'avez pas eu de nouvelles après 5 jours, contactez sellers@ocsapp.ca avec votre courriel d'inscription."
          : "Most applications are reviewed within 2–5 business days. You'll be notified by email. If you haven't heard back after 5 days, contact sellers@ocsapp.ca with the email you registered with." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment fonctionne la livraison ? Est-ce que j'envoie les commandes moi-même ?" : "How does delivery work? Do I ship orders myself?" ?></h4>
        <p><?= $fr
          ? "Non - vous n'expédiez pas les commandes vous-même. Le réseau de chauffeurs ODA d'OCSAPP gère 100 % des livraisons. Lorsqu'une commande est prête, un chauffeur ODA est envoyé pour la ramasser chez vous et la livrer directement au client."
          : "No - you never ship orders yourself. OCSAPP's ODA driver network handles 100% of deliveries. When an order is ready, an ODA driver is dispatched to pick it up from your shop and deliver it directly to the customer." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Comment et quand suis-je payé ?" : "How and when do I get paid?" ?></h4>
        <p><?= $fr
          ? "OCSAPP effectue les versements chaque semaine, tous les lundis, par dépôt direct dans votre compte bancaire, pour les commandes complétées durant la période précédente. Les montants inférieurs à 25 $ sont reportés à la semaine suivante plutôt que versés séparément. Chaque versement est accompagné d'un relevé détaillé, avec la commission et les frais de traitement indiqués comme deux lignes distinctes."
          : "OCSAPP processes payouts weekly, every Monday, by direct deposit to your bank account, for orders completed in the preceding period. Amounts under $25 roll over to the following week rather than being paid separately. Every payout comes with a detailed statement, with commission and processing fees shown as two separate line items." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Combien de produits puis-je lister ?" : "How many products can I list?" ?></h4>
        <p><?= $fr
          ? "Sur le forfait Essential, vous pouvez lister jusqu'à 30 produits actifs à la fois. Les forfaits Experience et plus offrent des listes de produits illimitées. Contactez sellers@ocsapp.ca pour discuter d'une mise à niveau."
          : "On the Essential plan, you can list up to 30 active products at a time. Experience and higher plans offer unlimited product listings. Contact sellers@ocsapp.ca to discuss upgrading." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je changer de forfait plus tard ?" : "Can I change plans later?" ?></h4>
        <p><?= $fr
          ? "Oui - vous pouvez passer à niveau à tout moment. Contactez sellers@ocsapp.ca ou votre gestionnaire de compte. Les changements prennent effet dans un délai d'un jour ouvrable. Tous les forfaits payants sont au mois, sans engagement à long terme."
          : "Yes - you can upgrade at any time. Contact sellers@ocsapp.ca or your account manager. Changes take effect within one business day. All paid plans are month-to-month, with no long-term commitment." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Qu'est-ce que le champ « poids » et pourquoi dois-je le remplir ?" : "What's the \"weight\" field, and why is it required?" ?></h4>
        <p><?= $fr
          ? "Le poids est un champ obligatoire sur chaque fiche produit - pas un détail facultatif. OCSAPP additionne le poids déclaré pour chaque article du panier d'un acheteur, à la caisse, pour déterminer si un supplément pour commande volumineuse s'applique. L'acheteur n'estime jamais lui-même le poids : votre valeur déclarée est la seule donnée utilisée pour ce calcul. Si un chauffeur constate à la cueillette un écart important avec le poids déclaré, OCSAPP peut ajuster le supplément rétroactivement à partir d'une preuve photo ou de balayage."
          : "Weight is a mandatory field on every product listing - not an optional detail. OCSAPP sums the weight you declare across every item in a buyer's cart, at checkout, to determine whether an oversize order surcharge applies. Buyers never estimate weight themselves: your declared figure is the only data used for that calculation. If a driver's inspection at pickup shows a material discrepancy with the declared weight, OCSAPP may adjust the surcharge retroactively based on photo or scan evidence." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Que se passe-t-il si un acheteur retourne un article ou demande un remboursement ?" : "What happens if a buyer returns an item or requests a refund?" ?></h4>
        <p><?= $fr
          ? "Les retours sont gérés par la Politique de retours et remboursements d'OCSAPP. Si le système détermine, à partir d'une preuve photo et de balayage, qu'un retour est lié à une erreur ou un défaut présent au moment où vous avez remis la commande au chauffeur, les frais de logistique inverse et la valeur de l'article remboursé sont déduits de votre prochain versement. Vous n'êtes jamais facturé pour un problème survenu après la prise en charge par le chauffeur, ni pour un simple changement d'avis de l'acheteur - et vous disposez de 5 jours ouvrables pour contester une déduction directement depuis votre tableau de bord."
          : "Returns are governed by OCSAPP's Returns &amp; Refund Policy. If the system determines, from photo and scan evidence, that a return is linked to an error or defect present when you handed the order to the driver, the reverse-logistics fee and the refunded item's value are deducted from your next payout. You're never charged for an issue that occurred after the driver took custody, or for a simple change of mind by the buyer - and you have 5 business days to dispute a deduction directly from your dashboard." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Les suppléments pour commande volumineuse ou multi-boutiques affectent-ils ma commission ?" : "Do the oversize or multi-shop surcharges affect my commission?" ?></h4>
        <p><?= $fr
          ? "Non. Le supplément pour commande volumineuse, les frais d'arrêt additionnel et le supplément longue distance sont payés par l'acheteur et servent uniquement à financer la livraison. Aucun des trois ne change le pourcentage de commission de votre forfait."
          : "No. The oversize order surcharge, the additional-stop fee, and the long-distance surcharge are all paid by the buyer and go entirely toward funding delivery. None of the three changes your plan's commission percentage." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SUPPORT -->
<section class="support">
  <div class="wrap">
    <div class="support-grid">
      <div class="support-card">
        <h4><?= $fr ? "Support vendeur" : "Seller Support" ?></h4>
        <p class="support-main"><a href="mailto:sellers@ocsapp.ca">sellers@ocsapp.ca</a></p>
        <p><?= $fr ? "Candidatures, forfaits &amp; boutiques" : "Applications, plans &amp; shops" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Téléphone" : "Phone" ?></h4>
        <p class="support-main">514-746-3789</p>
        <p><?= $fr ? "Lun–Sam · 8h – 20h" : "Mon–Sat · 8am – 8pm" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Info générale" : "General Info" ?></h4>
        <p class="support-main"><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        <p><?= $fr ? "Renseignements généraux" : "General inquiries" ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="wrap">
    <h2><?= $fr ? "Votre boutique vous attend." : "Your shop is waiting." ?></h2>
    <p><?= $fr
      ? "Inscrivez-vous en quelques minutes. Approuvé en 2 à 5 jours. Sans frais pour commencer - jamais."
      : "Sign up in minutes. Approved in 2–5 days. No cost to get started - ever." ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('register') ?>?role=seller"><?= $fr ? "Ouvrir ma boutique gratuitement" : "Open My Shop for Free" ?></a>
      <a class="btn-secondary" href="mailto:sellers@ocsapp.ca"><?= $fr ? "Contacter notre équipe" : "Contact Our Team" ?></a>
    </div>
  </div>
</section>

<!-- LEGAL IDENTITY -->
<section class="legal-identity">
  <div class="wrap">
    <p class="foot-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : "The all-in-one digital infrastructure for local commerce." ?></p>
    <p><?= $fr
      ? "OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n° de société 1750354-7) · Numéro d'entreprise du Québec (NEQ) 1181584997"
      : "OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997" ?></p>
    <p><?= $fr ? "Siège social : Laval, Québec (H7H)" : "Registered office: Laval, Québec (H7H)" ?></p>
  </div>
</section>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
