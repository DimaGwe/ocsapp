<?php
/**
 * OCSAPP Driver Central - marketing landing page
 * Bilingual: EN / FR
 * Rebuilt 2026-08 from the approved "Driver Central updated and standardized"
 * package: fixed zone-based pay rates with the 70/30 split, surcharge
 * transparency (oversize/stop/long-distance), reverse-logistics (returns)
 * pay premium, mandatory pickup/delivery photo proof, tip messaging
 * (not yet active), expanded FAQ, About-page-style icon set.
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
  <title><?= $fr ? "Livreur Central - Livrez avec OCSAPP" : "Driver Central - Deliver with OCSAPP" ?></title>
  <meta name="description" content="<?= $fr
    ? "Livrez avec OCSAPP : tarif fixe divulgué avant chaque course, 70 % de chaque frais de livraison, payé chaque semaine, zone de votre choix."
    : "Deliver with OCSAPP: a fixed rate disclosed before every job, 70% of every delivery fee, paid weekly, in the zone you choose." ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="stylesheet" href="<?= asset('css/pages/driver-central.css') ?>">
</head>
<body class="driver-central-page<?= $fr ? ' lang-fr' : '' ?>">
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

<?php
  $isDriver = function_exists('isLoggedIn') && isLoggedIn() && function_exists('hasRole') && hasRole('delivery');
?>

<!-- HERO -->
<section class="hero">
  <div class="wrap">
    <span class="eyebrow"><?= $fr ? "LIVREUR CENTRAL" : "DRIVER CENTRAL" ?></span>
    <h1><?= $fr ? "Livrez avec <span>OCSAPP</span>" : "Deliver with <span>OCSAPP</span>" ?></h1>
    <p class="hero-sub"><?= $fr
      ? "Gagnez selon votre horaire. Choisissez vos heures. Soyez payé chaque semaine. Rejoignez notre flotte de livreurs grandissante dans le West Island, avec Laval et le centre-ville de Montréal à venir bientôt."
      : "Earn on your schedule. Choose your hours. Get paid every week. Join our growing driver fleet in the West Island, with Laval and downtown Montreal coming soon." ?></p>
    <div class="hero-actions">
      <a class="btn" href="<?= url('delivery/apply') ?>"><?= $fr ? "Postuler comme livreur →" : "Apply to Drive →" ?></a>
      <?php if ($isDriver): ?>
        <a class="btn-secondary" href="<?= url('delivery/dashboard') ?>"><?= $fr ? "Mon tableau de bord" : "My Dashboard" ?></a>
      <?php else: ?>
        <a class="btn-secondary" href="<?= url('delivery/login') ?>"><?= $fr ? "Connexion livreur" : "Driver Login" ?></a>
      <?php endif; ?>
    </div>
    <div class="hero-proof-row cols-4">
      <div class="hero-proof-item"><strong><?= $fr ? "18 $+ /h" : "$18+/hr" ?></strong><span><?= $fr ? "gains horaires moyens*" : "average hourly earnings*" ?></span></div>
      <div class="hero-proof-item"><strong><?= $fr ? "Chaque semaine" : "Every week" ?></strong><span><?= $fr ? "paiements, tous les lundis" : "payouts, every Monday" ?></span></div>
      <div class="hero-proof-item"><strong>4 <?= $fr ? "types" : "types" ?></strong><span><?= $fr ? "de livraisons disponibles" : "of deliveries available" ?></span></div>
      <div class="hero-proof-item"><strong><?= $fr ? "Zone à votre choix" : "Zone of your choice" ?></strong><span><?= $fr ? "jamais un trajet imposé à travers la ville" : "never a trip imposed across town" ?></span></div>
    </div>
    <p style="font-size:11.5px;color:var(--text-grey-light);margin-top:14px;"><?= $fr
      ? "*Fourchette cible de conception tarifaire pour la zone Ouest-de-l'Île en période hors pointe (Ouest-de-l'Île, la seule zone active) - pas un montant garanti ni un minimum horaire. Figure calculée sans pourboire, puisque cette fonctionnalité n'est pas encore active."
      : "*Rate-design target range for the West Island zone, off-peak (West Island is currently the only active zone) - not a guaranteed amount or hourly minimum. Figure calculated tip-free, since that feature is not yet active." ?></p>
  </div>
</section>

<!-- POSITIONING STRIP -->
<section class="positioning-strip">
  <div class="wrap">
    <p><?= $fr
      ? "Votre tarif est <span>fixe et affiché avant d'accepter</span> - jamais un calcul en temps réel selon la demande du moment."
      : "Your rate is <span>fixed and disclosed before you accept</span> - never a real-time calculation based on current demand." ?></p>
  </div>
</section>

<!-- INTRO -->
<section class="intro">
  <div class="wrap">
    <h2><?= $fr ? "Connaissez votre tarif avant de dire oui - pas après, et pas selon la demande." : "Know your rate before you say yes - not after, and not based on demand." ?></h2>
    <p><?= $fr
      ? "Sur la plupart des applications de livraison, la paie de base évolue selon un algorithme que vous ne voyez jamais - elle augmente par petits incréments jusqu'à ce qu'un livreur accepte. OCSAPP fonctionne différemment : un tarif fixe, calibré par zone, publié avant même que vous acceptiez une course. Ce n'est pas toujours le montant le plus élevé par livraison - mais c'est le seul que vous pouvez vérifier vous-même, avant d'accepter."
      : "On most delivery apps, base pay moves according to an algorithm you never see - it climbs in small increments until a driver accepts. OCSAPP works differently: a fixed rate, calibrated by zone, published before you ever accept a job. It isn't always the highest number per delivery - but it's the only one you can verify yourself, before you accept." ?></p>
  </div>
</section>

<!-- WHY OCSAPP -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI OCSAPP" : "WHY OCSAPP" ?></div>
    <h2><?= $fr ? "Un tarif clair. Votre zone. Votre horaire." : "A clear rate. Your zone. Your schedule." ?></h2>
    <p class="section-lead"><?= $fr
      ? "OCSAPP est un écosystème numérique tout-en-un québécois - chaque livraison, qu'elle vienne du détail, du gros ou d'un retour, passe par le même réseau de livreurs à objectif zéro émission."
      : "OCSAPP is an all-in-one Quebec digital ecosystem - every delivery, whether retail, wholesale, or a return, moves through the same driver network working toward a zero-emission objective." ?></p>
    <div class="why-grid">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Tarif fixe, affiché par zone" : "Fixed rate, disclosed by zone" ?></h3>
        <p><?= $fr
          ? "Chaque course affiche son tarif avant que vous l'acceptiez - jamais un prix qui change selon l'heure ou la demande. Vous savez exactement ce que vous gagnez avant de dire oui."
          : "Every job shows its rate before you accept it - never a price that shifts with the time of day or demand. You know exactly what you'll earn before you say yes." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "70 % pour vous, toujours" : "70% is yours, always" ?></h3>
        <p><?= $fr
          ? "Vous gardez 70 % de chaque frais de livraison, peu importe la zone ou le type de commande. Le partage ne change jamais durant une promotion ou une période d'essai."
          : "You keep 70% of every delivery fee, regardless of zone or order type. The split never changes during a promotion or a trial period." ?></p>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Vous choisissez votre zone" : "You choose your zone" ?></h3>
        <p><?= $fr
          ? "Vous indiquez la zone où vous voulez travailler, et vous pouvez la changer à tout moment depuis votre tableau de bord - ce n'est jamais une zone imposée par OCSAPP. Vous demeurez entièrement libre d'accepter ou de refuser chaque course, peu importe d'où elle provient."
          : "You tell us which zone you want to work in, and you can change it anytime from your dashboard - it's never a zone OCSAPP imposes on you. You remain entirely free to accept or decline every job, regardless of where it comes from." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- TRANSPARENCY VALUE PANEL -->
<div class="reveal value-panel">
  <h2><?= $fr ? "Chaque livraison, <span>en toute transparence.</span>" : "Every delivery, <span>fully transparent.</span>" ?></h2>
  <p class="sub"><?= $fr ? "Ce que chaque livreur peut tenir pour acquis, dès sa première course." : "What every driver can count on, from their very first job." ?></p>
  <div class="value-grid">
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Tarif affiché avant d'accepter - jamais un algorithme qui change selon la demande" : "Rate disclosed before you accept - never an algorithm that shifts with demand" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "70 % de chaque frais de livraison vous revient, sur toutes les zones et tous les types de commande" : "70% of every delivery fee is yours, across every zone and order type" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Payé chaque semaine, tous les lundis, par dépôt direct" : "Paid every week, every Monday, by direct deposit" ?></p></div>
    <div class="value-row"><div class="value-tick">✓</div><p><?= $fr ? "Vous choisissez votre zone de travail - jamais une zone ou un trajet imposé par OCSAPP" : "You choose your work zone - never a zone or trip imposed by OCSAPP" ?></p></div>
  </div>
</div>

<!-- HOW IT WORKS -->
<section class="card-section" style="padding-top:4px;">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "COMMENT ÇA MARCHE" : "HOW IT WORKS" ?></div>
    <h2><?= $fr ? "De la candidature à votre première livraison" : "From application to your first delivery" ?></h2>
    <p class="section-lead"><?= $fr ? "Voici exactement à quoi vous attendre." : "Here's exactly what to expect." ?></p>
    <div class="why-grid cols-4">
      <article class="why-card">
        <div class="why-num">01</div>
        <h3><?= $fr ? "Postuler en ligne" : "Apply online" ?></h3>
        <p><?= $fr ? "Remplissez notre courte demande. Prend moins de 5 minutes." : "Fill out our short application. Takes less than 5 minutes." ?></p>
        <span class="step-time">~5 <?= $fr ? "minutes" : "minutes" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">02</div>
        <h3><?= $fr ? "Obtenir l'approbation" : "Get approved" ?></h3>
        <p><?= $fr ? "Notre équipe examine votre candidature, incluant une vérification des antécédents." : "Our team reviews your application, including a background check." ?></p>
        <span class="step-time"><?= $fr ? "1 à 3 jours ouvrables" : "1–3 business days" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">03</div>
        <h3><?= $fr ? "Commencer à livrer" : "Start delivering" ?></h3>
        <p><?= $fr ? "Connectez-vous, passez en ligne et acceptez les livraisons offertes dans la zone que vous avez choisie." : "Log in, go online, and accept deliveries offered in the zone you've chosen." ?></p>
        <span class="step-time"><?= $fr ? "Dès l'approbation" : "As soon as approved" ?></span>
      </article>
      <article class="why-card">
        <div class="why-num">04</div>
        <h3><?= $fr ? "Être payé" : "Get paid" ?></h3>
        <p><?= $fr ? "Vos gains sont suivis automatiquement et versés chaque semaine, tous les lundis." : "Your earnings are tracked automatically and paid out every week, every Monday." ?></p>
        <span class="step-time"><?= $fr ? "Chaque lundi" : "Every Monday" ?></span>
      </article>
    </div>
  </div>
</section>

<!-- WHY DELIVER WITH OCSAPP -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "POURQUOI LIVRER AVEC OCSAPP" : "WHY DELIVER WITH OCSAPP" ?></div>
    <h2><?= $fr ? "Tout ce qu'il vous faut pour gagner à votre façon" : "Everything you need to earn your way" ?></h2>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc1" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#dc1)"/><circle cx="32" cy="32" r="12" fill="#f5fff5"/><path d="M32 20v12l8 5" fill="none" stroke="#00B207" stroke-width="3.5" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Horaires flexibles" : "Flexible hours" ?></h3>
        <p><?= $fr ? "Travaillez quand ça vous convient. Définissez votre propre horaire et activez ou désactivez votre disponibilité depuis votre tableau de bord à tout moment. Aucun minimum d'heures ni de courses requis." : "Work when it suits you. Set your own schedule and toggle your availability from your dashboard at any time. No minimum hours or deliveries required." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc2" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#2be933"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#dc2)"/><path d="M36 19c-8-2-15 1-15 7 0 11 22 4 22 15 0 7-8 9-17 6" fill="none" stroke="#f4fff5" stroke-width="4" stroke-linecap="round"/><path d="M32 14v36" stroke="#d8ffda" stroke-width="3"/></svg></span>
        <h3><?= $fr ? "Rémunération compétitive" : "Competitive pay" ?></h3>
        <p><?= $fr ? "Gagnez des frais de livraison fixes par zone à chaque course complétée - vous gardez 70 % de chaque frais. Vos gains sont suivis en temps réel dans votre portail, avec un relevé détaillé chaque semaine." : "Earn a fixed, zone-based delivery fee on every completed job - you keep 70% of every fee. Your earnings are tracked in real time in your portal, with a detailed statement every week." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc3" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#076e0a"/></linearGradient></defs><path d="M32 8c10 0 18 8 18 18 0 13-18 30-18 30S14 39 14 26C14 16 22 8 32 8z" fill="url(#dc3)"/><circle cx="32" cy="26" r="7" fill="#f5fff5"/><circle cx="32" cy="26" r="3.2" fill="#00B207"/></svg></span>
        <h3><?= $fr ? "Trajets par zone" : "Zone-based trips" ?></h3>
        <p><?= $fr ? "OCSAPP vous propose des livraisons dans la zone que vous avez choisie, pour garder vos trajets courts et efficaces. Vous demeurez libre d'accepter ou de refuser chaque course, et de changer de zone à tout moment depuis votre tableau de bord." : "OCSAPP offers you deliveries in the zone you've chosen, to keep your trips short and efficient. You remain free to accept or decline every job, and to change zones anytime from your dashboard." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc4" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><path d="M11 23l21-11 21 11-21 11z" fill="#3af042"/><path d="M11 23v24l21 10V34z" fill="url(#dc4)"/><path d="M53 23v24L32 57V34z" fill="#0a8f0f"/></svg></span>
        <h3><?= $fr ? "Variété de travail" : "Variety of work" ?></h3>
        <p><?= $fr ? "Gérez les commandes clients B2C, les distributions B2B, les collectes chez les fournisseurs et les retours - plusieurs sources de revenus via une seule application." : "Handle B2C customer orders, B2B distributions, supplier pickups, and returns - multiple income streams through a single app." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc5" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M19 7h26v50H19z" fill="url(#dc5)"/><rect x="23" y="13" width="18" height="34" rx="3" fill="#f6fff6"/><circle cx="32" cy="52" r="2.3" fill="#d6ffda"/><path d="M27 26h10M27 32h7" stroke="#00B207" stroke-width="2.6" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Application facile à utiliser" : "Easy-to-use app" ?></h3>
        <p><?= $fr ? "Votre tableau de bord livreur fonctionne sur n'importe quel navigateur mobile. Acceptez des missions, mettez à jour les statuts et suivez vos gains - tout depuis votre téléphone." : "Your driver dashboard works on any mobile browser. Accept jobs, update statuses, and track your earnings - all from your phone." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc6" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M10 15h44v30H31l-11 8v-8H10z" fill="url(#dc6)"/><circle cx="23" cy="30" r="3" fill="#fff"/><circle cx="32" cy="30" r="3" fill="#fff"/><circle cx="41" cy="30" r="3" fill="#fff"/></svg></span>
        <h3><?= $fr ? "Support administratif" : "Admin support" ?></h3>
        <p><?= $fr ? "Notre équipe opérationnelle est disponible pour vous aider. Vous n'êtes jamais seul - le support est à portée d'un message." : "Our operations team is available to help. You're never on your own - support is a message away." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- HOW PAY WORKS -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "COMMENT FONCTIONNE LA RÉMUNÉRATION" : "HOW PAY WORKS" ?></div>
    <h2><?= $fr ? "Des frais de livraison par zone, à chaque course - sans surprise" : "Zone-based delivery fees, every job - no surprises" ?></h2>
    <p class="section-lead"><?= $fr
      ? "Le même partage 70/30 s'applique à chaque tarif ci-dessous - vous gardez 70 % de chaque frais de livraison (les 30 % restants couvrent l'assurance, le support et les opérations de l'application), suivi en temps réel et versé chaque semaine."
      : "The same 70/30 split applies to every rate below - you keep 70% of every delivery fee (the remaining 30% covers insurance, support, and app operations), tracked in real time and paid out every week." ?></p>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc7" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#22e42a"/><stop offset="1" stop-color="#066c0a"/></linearGradient></defs><path d="M11 23l21-11 21 11-21 11z" fill="#3af042"/><path d="M11 23v24l21 10V34z" fill="url(#dc7)"/><path d="M53 23v24L32 57V34z" fill="#0a8f0f"/><path d="M20 39h11" stroke="#fff" stroke-width="3"/></svg></span>
        <h3><?= $fr ? "Commandes clients (B2C) - Tarif Marché" : "Customer orders (B2C) - Marché rate" ?></h3>
        <p><?= $fr
          ? "<strong>7,99 $</strong> West Island (en service) · 8,99 $ Laval · 9,99 $ Montréal (centre) - bientôt disponibles. Votre part (70 %) : <strong>5,59 $</strong> West Island."
          : "<strong>\$7.99</strong> West Island (active) · \$8.99 Laval · \$9.99 Downtown Montreal - coming soon. Your share (70%): <strong>\$5.59</strong> West Island." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc8" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#21e529"/><stop offset="1" stop-color="#07710b"/></linearGradient></defs><path d="M12 50V20h40v30z" fill="url(#dc8)"/><path d="M18 26h7v7h-7zm11 0h7v7h-7zm11 0h7v7h-7zM24 39h16v11H24z" fill="#f5fff5"/></svg></span>
        <h3><?= $fr ? "Distributions aux entreprises (B2B) - Tarif Fournisseur" : "Business distributions (B2B) - Fournisseur rate" ?></h3>
        <p><?= $fr
          ? "<strong>19,00 $</strong> West Island (en service) · 21,00 $ Laval · 24,00 $ Montréal (centre) - bientôt disponibles. Votre part (70 %) : <strong>13,30 $</strong> West Island."
          : "<strong>\$19.00</strong> West Island (active) · \$21.00 Laval · \$24.00 Downtown Montreal - coming soon. Your share (70%): <strong>\$13.30</strong> West Island." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc9" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#1fe528"/><stop offset="1" stop-color="#08730c"/></linearGradient></defs><path d="M9 50V22h30v28z" fill="url(#dc9)"/><path d="M39 30h11l6 8v12H39z" fill="#0b8d10"/><path d="M16 16h16" stroke="#00B207" stroke-width="4"/><circle cx="19" cy="51" r="5" fill="#082d0a"/><circle cx="47" cy="51" r="5" fill="#082d0a"/></svg></span>
        <h3><?= $fr ? "Collectes chez les fournisseurs" : "Supplier pickups" ?></h3>
        <p><?= $fr
          ? "Rémunérées au même tarif Fournisseur ci-dessus - une catégorie distincte dans votre portail, mais pas un tarif différent. Des courses planifiées à l'avance et prévisibles."
          : "Paid at the same Fournisseur rate above - its own category in your portal, but not a separate rate. Scheduled, predictable jobs planned in advance." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc10" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#28e830"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><path d="M20 17h18c9 0 16 7 16 16s-7 16-16 16H20" fill="none" stroke="url(#dc10)" stroke-width="7" stroke-linecap="round"/><path d="M21 9L10 17l11 8" fill="none" stroke="#00B207" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Retours &amp; livraison inversée" : "Returns &amp; reverse logistics" ?></h3>
        <p><?= $fr
          ? "Un type de course distinct pour les retours et échanges - payé <strong>plus</strong> qu'une livraison standard dans la même zone : 9,99 $ (Marché) / 24,00 $ (Fournisseur) à West Island. Votre part (70 %) reste identique peu importe qui finance le tarif."
          : "A distinct job type for returns and exchanges - paid <strong>more</strong> than a standard delivery in the same zone: \$9.99 (Marché) / \$24.00 (Fournisseur) in West Island. Your 70% share stays the same regardless of who ultimately funds the fee." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- SURCHARGES / TRUST -->
<section class="trust">
  <div class="wrap">
    <h3><?= $fr ? "Suppléments - des gains additionnels, pas une exception" : "Surcharges - extra earnings, not an exception" ?></h3>
    <p><?= $fr
      ? "Certaines courses portent un supplément en plus du tarif de base. Le même partage 70/30 s'applique à chacun - ce sont de vrais gains supplémentaires pour vous, jamais une déduction."
      : "Some jobs carry a surcharge on top of the base rate. The same 70/30 split applies to each one - these are real additional earnings for you, never a deduction." ?></p>
    <div class="trust-grid trust-grid-3col">
      <div class="trust-card">
        <h4><?= $fr ? "Supplément commande volumineuse" : "Oversize order surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 15 kg (Marché) ou 25 kg (Fournisseur). West Island : +4,00 $ / +10,00 $, plus un incrément pour poids additionnel. Votre part (70 %) : +2,80 $ / +7,00 $." : "Above 15 kg (Marché) or 25 kg (Fournisseur). West Island: +\$4.00 / +\$10.00, plus an increment for additional weight. Your share (70%): +\$2.80 / +\$7.00." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Frais d'arrêt additionnel" : "Additional-stop fee" ?></h4>
        <p><?= $fr ? "Pour chaque arrêt au-delà des deux premiers sur une commande consolidée. West Island : +2,00 $ (Marché) / +4,00 $ (Fournisseur). Votre part (70 %) : +1,40 $ / +2,80 $." : "For each stop beyond the first two on a consolidated order. West Island: +\$2.00 (Marché) / +\$4.00 (Fournisseur). Your share (70%): +\$1.40 / +\$2.80." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Supplément longue distance" : "Long-distance surcharge" ?></h4>
        <p><?= $fr ? "Au-delà de 8 km (Marché) ou 10 km (Fournisseur), calculé automatiquement à partir du trajet réel. West Island : +4,00 $ / +10,00 $, plus un incrément pour distance additionnelle." : "Beyond 8 km (Marché) or 10 km (Fournisseur), calculated automatically from the actual route. West Island: +\$4.00 / +\$10.00, plus an increment for additional distance." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Poids et distance : jamais à déclarer" : "Weight and distance: never yours to declare" ?></h4>
        <p><?= $fr ? "Le poids et le nombre d'arrêts proviennent des données du vendeur ou du fournisseur, pas de vous. Vous n'avez rien à mesurer - si vous constatez un écart à la cueillette, signalez-le simplement dans l'application." : "Weight and stop count come from the seller's or supplier's own data, not from you. There's nothing to measure - if you spot a discrepancy at pickup, just flag it in the app." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Toujours affiché avant d'accepter" : "Always disclosed before you accept" ?></h4>
        <p><?= $fr ? "Chaque supplément applicable est visible dans l'application avant que vous acceptiez la course - jamais calculé après coup, jamais une surprise sur votre relevé." : "Every applicable surcharge is visible in the app before you accept the job - never calculated after the fact, never a surprise on your statement." ?></p>
      </div>
      <div class="trust-card">
        <h4><?= $fr ? "Ajustement basé sur des preuves" : "Evidence-based adjustment" ?></h4>
        <p><?= $fr ? "Si le poids réel diffère nettement de ce qui était déclaré, OCSAPP peut ajuster le supplément - et donc votre paie - à partir de la preuve photo ou de balayage que vous fournissez. Vous avez le droit de demander une explication des facteurs utilisés et une révision humaine de cette décision." : "If the actual weight materially differs from what was declared, OCSAPP may adjust the surcharge - and therefore your pay - based on the photo or scan evidence you provide. You have the right to request an explanation of the factors used and a human review of that decision." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- PROOF AT PICKUP AND DELIVERY -->
<section class="card-section" style="padding-top:4px; background:var(--bg-light);">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "PREUVE À LA CUEILLETTE ET À LA LIVRAISON" : "PROOF AT PICKUP AND DELIVERY" ?></div>
    <h2><?= $fr ? "Votre photo, votre protection" : "Your photo, your protection" ?></h2>
    <p class="section-lead"><?= $fr
      ? "Sur chaque commande - pas seulement les retours - une photo ou un balayage à la cueillette et une preuve de livraison sont obligatoires. Ce n'est pas de la paperasse : c'est ce qui vous protège si un problème est découvert plus tard."
      : "On every order - not just returns - a photo or scan at pickup and proof of delivery are required. This isn't paperwork for its own sake: it's what protects you if an issue is discovered later." ?></p>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc11" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#26e82e"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><rect x="9" y="18" width="46" height="32" rx="6" fill="url(#dc11)"/><path d="M21 18l4-7h14l4 7" fill="#00B207"/><circle cx="32" cy="34" r="10" fill="#f4fff5"/><circle cx="32" cy="34" r="5" fill="#00B207"/></svg></span>
        <h3><?= $fr ? "Photo ou balayage à la cueillette" : "Photo or scan at pickup" ?></h3>
        <p><?= $fr ? "Confirmez l'état du colis avant de quitter le point de cueillette, sur chaque commande. Cette preuve devient la référence de départ - faites-le avant de partir, pas plus tard." : "Confirm the package's condition before you leave the pickup location, on every order. This becomes the starting reference point - do it before you leave, not later." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc12" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M15 10h34v44H15z" fill="url(#dc12)"/><path d="M22 20h20M22 28h20M22 36h11" stroke="#f5fff5" stroke-width="3" stroke-linecap="round"/><path d="M34 45l5 4 9-11" fill="none" stroke="#d7ffda" stroke-width="3.5" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Preuve de livraison" : "Proof of delivery" ?></h3>
        <p><?= $fr ? "Signature ou photo à la livraison, ou remise en main propre pour les commandes Marché. C'est ce qui confirme que le colis vous a quitté en bon état." : "Signature or photo at delivery, or hand-to-hand delivery for Marché orders. This is what confirms the package left your custody in good condition." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc13" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#26e82e"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><path d="M32 8l20 8v14c0 13-8 22-20 28C20 52 12 43 12 30V16z" fill="url(#dc13)"/><path d="M23 32l6 6 12-14" fill="none" stroke="#f5fff5" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Ça vous protège, pas seulement le client" : "It protects you, not just the customer" ?></h3>
        <p><?= $fr ? "Si un problème est signalé après coup, ce sont ces deux preuves - cueillette et livraison - qui déterminent si l'incident est survenu avant que vous preniez le colis (responsabilité du vendeur/fournisseur) ou pendant le transport. Sans votre photo de cueillette, une réclamation n'a aucune base de comparaison - et le doute pourrait vous nuire." : "If an issue is reported afterward, these two pieces of evidence - pickup and delivery - determine whether it happened before you took the package (seller/supplier responsibility) or in transit. Without your pickup photo, a claim has no baseline to compare against - and the uncertainty could work against you." ?></p>
      </article>
    </div>
    <p style="font-size:12.5px;color:var(--text-grey-light);margin-top:20px;text-align:center;max-width:700px;margin-left:auto;margin-right:auto;"><?= $fr
      ? "Omettre, retarder ou falsifier une étape de photo ou de balayage requise est considéré comme un manquement sérieux à votre entente avec OCSAPP."
      : "Omitting, delaying, or falsifying a required photo or scan step is treated as a serious breach of your agreement with OCSAPP." ?></p>
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
        <li><?= $fr ? "100 % de tout pourboire vous revient directement - OCSAPP ne prend jamais de commission sur un pourboire." : "100% of any tip goes directly to you - OCSAPP never takes a cut of a tip." ?></li>
        <li><?= $fr ? "Les montants suggérés (15 %, 18 %, 20 % ou personnalisé) seront calculés sur le sous-total avant taxes, tous affichés avec la même importance visuelle." : "Suggested amounts (15%, 18%, 20%, or custom) will be calculated on the pre-tax subtotal, all displayed with equal visual weight." ?></li>
        <li><?= $fr ? "Cette fonctionnalité est confirmée pour le lancement mais n'est pas encore active dans l'application - les figures de gains présentées ailleurs sur cette page n'incluent aucun pourboire, ce qui veut dire qu'elles ne peuvent qu'augmenter une fois la fonctionnalité lancée." : "This feature is confirmed for launch but is not yet active in the app - the earnings figures shown elsewhere on this page include no tips at all, which means they can only go up once it ships." ?></li>
      </ul>
    </div>
  </div>
</section>

<!-- REQUIREMENTS -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "EXIGENCES" : "REQUIREMENTS" ?></div>
    <h2><?= $fr ? "Ce dont vous avez besoin pour commencer" : "What you need to get started" ?></h2>
    <div class="feature-grid">
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc14" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#24e62c"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#dc14)"/><text x="32" y="39" text-anchor="middle" font-size="21" font-weight="700" fill="#f5fff5">18+</text></svg></span>
        <h3><?= $fr ? "18 ans et plus" : "18 or older" ?></h3>
        <p><?= $fr ? "Vous devez avoir au moins 18 ans au moment de la candidature." : "You must be at least 18 years of age at the time of application." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc15" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#20e528"/><stop offset="1" stop-color="#076d0a"/></linearGradient></defs><rect x="9" y="16" width="46" height="32" rx="6" fill="url(#dc15)"/><circle cx="22" cy="31" r="7" fill="#f4fff5"/><path d="M35 26h12M35 33h12M17 42h30" stroke="#d8ffda" stroke-width="3" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Permis de conduire valide" : "Valid driver's licence" ?></h3>
        <p><?= $fr ? "Permis québécois complet et valide, de la classe appropriée à votre véhicule." : "A full, valid Quebec licence of the class appropriate to your vehicle." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc16" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#26e82e"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><path d="M32 8l20 8v14c0 13-8 22-20 28C20 52 12 43 12 30V16z" fill="url(#dc16)"/><path d="M22 32h20M32 22v20" stroke="#f5fff5" stroke-width="4" stroke-linecap="round"/></svg></span>
        <h3><?= $fr ? "Assurance à usage commercial" : "Commercial-use insurance" ?></h3>
        <p><?= $fr ? "Assurance automobile responsabilité civile et assurance cargo/responsabilité civile, adaptées à votre type de livraison, avec une couverture minimale de 1 000 000 \$ CAD. Une police à usage personnel standard exclut habituellement la livraison commerciale - vérifiez avec votre assureur avant de postuler." : "Commercial automobile liability insurance and cargo/civil liability insurance appropriate to your delivery type, with minimum coverage of \$1,000,000 CAD. A standard personal-use policy typically excludes commercial delivery - check with your insurer before you apply." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc17" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#25e72d"/><stop offset="1" stop-color="#08720c"/></linearGradient></defs><path d="M19 7h26v50H19z" fill="url(#dc17)"/><rect x="23" y="13" width="18" height="34" rx="3" fill="#f6fff6"/><circle cx="32" cy="52" r="2.3" fill="#d6ffda"/></svg></span>
        <h3><?= $fr ? "Téléphone intelligent" : "Smartphone" ?></h3>
        <p><?= $fr ? "N'importe quel téléphone moderne avec un navigateur suffit pour accéder à votre tableau de bord." : "Any modern phone with a browser is enough to access your dashboard." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc18" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#21e529"/><stop offset="1" stop-color="#07710b"/></linearGradient></defs><path d="M12 26h32l7 10v11H8V34z" fill="url(#dc18)"/><circle cx="19" cy="48" r="6" fill="#082d0a"/><circle cx="43" cy="48" r="6" fill="#082d0a"/><path d="M18 20h18l6 6H15z" fill="#00B207"/></svg></span>
        <h3><?= $fr ? "Véhicule admissible" : "Eligible vehicle" ?></h3>
        <p><?= $fr ? "Vélo électrique, moto, voiture, VUS ou fourgonnette pour les livraisons B2C (jusqu'à 15 kg). Fourgonnette ou petit camion commercial pour les distributions B2B (jusqu'à 25 kg régulièrement - certification de chariot élévateur un atout)." : "E-bike, motorcycle, car, SUV, or van for B2C deliveries (up to 15 kg). Van or small commercial truck for B2B distribution (up to 25 kg regularly - forklift certification an asset)." ?></p>
      </article>
      <article class="feature-card">
        <span class="feature-icon feature-icon-3d" aria-hidden="true"><svg viewBox="0 0 64 64"><defs><linearGradient id="dc19" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#26e82e"/><stop offset="1" stop-color="#08710c"/></linearGradient></defs><circle cx="32" cy="32" r="22" fill="url(#dc19)"/><path d="M21 32l7 7 15-17" fill="none" stroke="#f5fff5" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
        <h3><?= $fr ? "Bonne réputation" : "Good standing" ?></h3>
        <p><?= $fr ? "Un dossier vierge est préférable. Une vérification des antécédents fait partie de notre processus d'approbation." : "A clean record is preferred. A background check is part of our approval process." ?></p>
      </article>
    </div>
  </div>
</section>

<!-- FOUNDING PARTNER PROGRAM -->
<section class="card-section">
  <div class="wrap">
    <div class="section-eyebrow"><?= $fr ? "PROGRAMME PARTENAIRE FONDATEUR" : "FOUNDING PARTNER PROGRAM" ?></div>
    <h2><?= $fr ? "Rejoignez nos 50 tout premiers livreurs" : "Join our first 50 drivers" ?></h2>
    <div class="requirements-box">
      <h4><?= $fr ? "Réservé aux 50 premiers livreurs approuvés :" : "Reserved for the first 50 approved drivers:" ?></h4>
      <ul style="grid-template-columns:1fr;">
        <li><?= $fr
          ? "Un insigne « Livreur Fondateur » permanent sur votre profil, reconnaissant votre statut de partenaire précoce de la plateforme."
          : "A permanent \"Founding Driver\" badge on your profile, recognizing your status as an early platform partner." ?></li>
        <li><?= $fr
          ? "Aucune démarche à faire : votre statut est confirmé automatiquement à l'approbation de votre candidature, tant que la cohorte n'est pas fermée."
          : "Nothing to request: your status is confirmed automatically when your application is approved, as long as the cohort isn't already closed." ?></li>
        <li><?= $fr
          ? "Bientôt : une prime d'étape, une prime de parrainage et un accès prioritaire à la répartition sont prévus pour les livreurs fondateurs, en plus du partage standard 70/30 - ces avantages seront annoncés une fois lancés."
          : "Coming soon: a milestone bonus, a referral bonus, and priority dispatch access are planned for Founding Drivers, on top of the standard 70/30 split - these will be announced once they launch." ?></li>
      </ul>
      <p style="margin:14px 0 0;font-weight:600;"><a href="<?= url('founding') ?>#driver" style="color:#00b207;"><?= $fr ? 'Voir tous les programmes fondateurs et les places restantes' : 'See all founding programs and spots remaining' ?> &rarr;</a></p>
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
        <h4><?= $fr ? "Comment et quand suis-je payé ?" : "How and when do I get paid?" ?></h4>
        <p><?= $fr ? "Vos gains sont versés chaque semaine, par dépôt direct, pour les courses complétées durant la période du lundi au dimanche précédent - versés le lundi suivant. Un relevé détaillé est disponible dans l'application, indiquant chaque course, sa zone et le tarif appliqué." : "Your earnings are paid weekly by direct deposit, for jobs completed during the preceding Monday-to-Sunday period - paid out the following Monday. A detailed statement is available in the app, showing each job, its zone, and the rate applied." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Que faire si je remarque une erreur sur mon versement ?" : "What if I notice an error in my payout?" ?></h4>
        <p><?= $fr ? "Signalez tout écart dans les 30 jours suivant la date du versement en question. Passé ce délai, le versement est considéré comme accepté, sauf erreur manifeste." : "Report any discrepancy within 30 days of the payout date in question. After that, the payout is considered accepted absent manifest error." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Suis-je obligé d'accepter un nombre minimum de courses ?" : "Am I required to accept a minimum number of jobs?" ?></h4>
        <p><?= $fr ? "Non. OCSAPP ne garantit ni un nombre minimum de courses, ni des gains minimums, ni un équivalent horaire garanti. Vous acceptez ou refusez chaque course librement - rien ne vous oblige à un minimum d'heures ou de courses." : "No. OCSAPP doesn't guarantee a minimum number of jobs, minimum earnings, or a guaranteed hourly equivalent. You accept or decline each job freely - nothing obligates you to a minimum number of hours or deliveries." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Pourquoi dois-je prendre une photo à la cueillette et à la livraison ?" : "Why do I need to take a photo at pickup and delivery?" ?></h4>
        <p><?= $fr ? "Sur chaque commande, pas seulement les retours, une photo ou un balayage à la cueillette confirme l'état du colis avant que vous partiez - et une preuve de livraison confirme qu'il vous a quitté en bon état. Si un problème est signalé plus tard, ce sont ces deux preuves qui déterminent où l'incident s'est produit : avant votre garde (responsabilité du vendeur ou du fournisseur) ou pendant le transport. Sans votre photo de cueillette, une réclamation n'a aucune base de comparaison - cette étape vous protège autant qu'elle protège le client. Omettre ou falsifier cette étape est considéré comme un manquement sérieux." : "On every order, not just returns, a photo or scan at pickup confirms the package's condition before you leave - and proof of delivery confirms it left your custody in good condition. If an issue is reported later, these two pieces of evidence determine where the problem happened: before your custody (seller or supplier responsibility) or in transit. Without your pickup photo, a claim has no baseline to compare against - this step protects you as much as it protects the customer. Omitting or falsifying this step is treated as a serious breach." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Qu'est-ce qu'une livraison inversée (un retour) ?" : "What's a reverse-logistics job (a return)?" ?></h4>
        <p><?= $fr ? "C'est une course distincte dispatchée pour un retour, un échange ou une collecte liée à la Politique de retours et remboursements d'OCSAPP. Elle est offerte de la même façon qu'une course standard - vous pouvez l'accepter ou la refuser librement - et elle paie davantage qu'une livraison standard dans la même zone, puisqu'une collecte de retour prend généralement plus de temps qu'une livraison groupée." : "It's a distinct job dispatched for a return, exchange, or pickup connected to OCSAPP's Returns & Refund Policy. It's offered the same way as a standard job - you can accept or decline freely - and it pays more than a standard delivery in the same zone, since a return pickup generally takes longer than a batched delivery." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Les suppléments changent-ils mon partage de 70 % ?" : "Do surcharges change my 70% split?" ?></h4>
        <p><?= $fr ? "Non. Le partage 70/30 s'applique de façon identique au tarif de base et à tout supplément applicable (commande volumineuse, arrêt additionnel, longue distance). Ce sont des gains additionnels calculés sur le même principe, jamais une exception à votre taux." : "No. The 70/30 split applies identically to the base rate and to any applicable surcharge (oversize, additional-stop, long-distance). These are additional earnings calculated the same way, never an exception to your rate." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Existe-t-il un système de paliers ou de priorité ?" : "Is there a priority tier or status system?" ?></h4>
        <p><?= $fr
          ? "Pas encore, mais c'est prévu. OCSAPP prévoit offrir un système de paliers non monétaire, basé sur votre volume de livraisons complétées et votre évaluation, donnant aux livreurs de palier supérieur un accès prioritaire aux courses disponibles. Ce statut s'acquerrait uniquement par la performance, ne s'achèterait jamais, et ne changerait ni votre partage de 70 % ni le tarif de base. En attendant son lancement, tous les livreurs ont un accès égal, au premier arrivé, aux courses de leur zone choisie."
          : "Not yet, but it's planned. OCSAPP intends to offer a non-monetary tier system based on your completed-delivery volume and rating, giving higher-tier drivers priority access to available jobs. This status would be earned through performance alone, never purchased, and wouldn't change your 70% split or base rate. Until it launches, every driver has equal, first-come access to jobs in their chosen zone." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je travailler dans plus d'une zone ?" : "Can I work in more than one zone?" ?></h4>
        <p><?= $fr ? "Vous indiquez la zone où vous souhaitez travailler, et vous pouvez la modifier à tout moment depuis votre tableau de bord - ce n'est jamais une zone que vous impose OCSAPP. Les courses qui vous sont offertes correspondent à la zone que vous avez choisie, pour garder vos trajets courts plutôt que de vous voir proposer des courses à travers un vaste bassin métropolitain. Si une mission inter-zones est un jour proposée, elle est présentée comme une mission distincte et entièrement facultative - vous demeurez libre de l'accepter ou de la refuser, exactement comme pour toute autre course, sans aucune conséquence." : "You tell us which zone you want to work in, and you can change it anytime from your dashboard - it's never a zone OCSAPP imposes on you. The jobs offered to you match the zone you've chosen, to keep your trips short rather than offering you jobs across a wide metropolitan pool. If a cross-zone assignment is ever offered, it comes as a distinct, entirely optional assignment - you remain free to accept or decline it, exactly like any other job, without any consequence." ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? "Puis-je livrer pour d'autres plateformes en même temps ?" : "Can I deliver for other platforms at the same time?" ?></h4>
        <p><?= $fr ? "Oui. Votre entente avec OCSAPP est non exclusive - vous pouvez offrir vos services à toute autre personne ou entreprise, y compris des concurrents, et vous n'êtes jamais tenu d'accepter un minimum de courses." : "Yes. Your agreement with OCSAPP is non-exclusive - you can offer your services to any other person or business, including competitors, and you're never required to accept a minimum number of jobs." ?></p>
      </div>
      <div class="faq-item new">
        <h4><?= $fr ? "Comment fonctionne le programme Partenaire Fondateur ?" : "How does the Founding Partner Program work?" ?></h4>
        <p><?= $fr
          ? "Les 50 premiers livreurs approuvés sur OCSAPP obtiennent automatiquement le statut de Livreur Fondateur - aucune candidature séparée requise. Vous obtenez un insigne permanent sur votre profil. La prime d'étape, la prime de parrainage et l'accès prioritaire décrits dans nos documents du programme sont prévus mais pas encore actifs - ils seront annoncés séparément une fois lancés."
          : "The first 50 approved drivers on OCSAPP automatically get Founding Driver status - no separate application needed. You get a permanent badge on your profile. The milestone bonus, referral bonus, and priority access described in our program materials are planned but not yet active - they'll be announced separately once they launch." ?></p>
      </div>
    </div>
  </div>
</section>

<!-- SUPPORT -->
<section class="support">
  <div class="wrap">
    <div class="support-grid">
      <div class="support-card">
        <h4><?= $fr ? "Support livreur" : "Driver Support" ?></h4>
        <p class="support-main"><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        <p><?= $fr ? "Toutes les demandes livreur" : "All driver inquiries" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Téléphone" : "Phone" ?></h4>
        <p class="support-main">514-746-3789</p>
        <p><?= $fr ? "Lun–Dim · 7h – 23h" : "Mon–Sun · 7am – 11pm" ?></p>
      </div>
      <div class="support-card">
        <h4><?= $fr ? "Portail livreur" : "Driver Portal" ?></h4>
        <p class="support-main">ocsapp.ca/delivery/login</p>
        <p><?= $fr ? "Connexion &amp; tableau de bord" : "Login &amp; dashboard" ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="cta-band">
  <div class="wrap">
    <h2><?= $fr ? "Prêt à commencer à gagner ?" : "Ready to start earning?" ?></h2>
    <p><?= $fr ? "Rejoignez le réseau de livreurs OCSAPP aujourd'hui. Postulez en quelques minutes - commencez à livrer cette semaine." : "Join the OCSAPP driver network today. Apply in minutes - start delivering this week." ?></p>
    <div class="cta-actions">
      <a class="btn" href="<?= url('delivery/apply') ?>"><?= $fr ? "Postuler maintenant - c'est gratuit" : "Apply Now - It's Free" ?></a>
      <?php if ($isDriver): ?>
        <a class="btn-secondary" href="<?= url('delivery/dashboard') ?>"><?= $fr ? "Mon tableau de bord" : "My Dashboard" ?></a>
      <?php else: ?>
        <a class="btn-secondary" href="<?= url('delivery/login') ?>"><?= $fr ? "Déjà livreur? Se connecter" : "Already a driver? Log in" ?></a>
      <?php endif; ?>
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
