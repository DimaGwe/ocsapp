<?php
/**
 * OCSAPP About Us Page
 * Bilingual: EN / FR
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'À propos - OCSAPP' : 'About Us - OCSAPP' ?></title>
  <meta name="description" content="<?= $fr
    ? 'Découvrez OCSAPP - la marketplace locale qui connecte acheteurs, vendeurs, fournisseurs et chauffeurs au Québec.'
    : 'Discover OCSAPP - the local marketplace connecting buyers, sellers, suppliers, and drivers across Quebec.' ?>">
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <?= csrfMeta() ?>
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    :root {
      --green:      #00b207;
      --green-dark: #007a05;
      --neon:       #00ff88;
      --text:       #374151;
      --muted:      #6b7280;
      --border:     #e5e7eb;
      --light:      #f9fafb;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); line-height: 1.65; }
    a { text-decoration: none; }
    .container { max-width: 1160px; margin: 0 auto; padding: 0 24px; }

    /* ── HERO ── */
    .hero {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 100px 24px 96px; text-align: center; position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 700px; height: 700px; top: -200px; right: -150px;
      background: radial-gradient(circle, rgba(0,178,7,.16) 0%, transparent 65%);
    }
    .hero::after {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 500px; height: 500px; bottom: -120px; left: -80px;
      background: radial-gradient(circle, rgba(0,255,136,.08) 0%, transparent 65%);
    }
    .hero-inner { position: relative; z-index: 1; max-width: 760px; margin: 0 auto; }
    .eyebrow {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 20px;
      background: rgba(0,255,136,.12); border: 1px solid rgba(0,255,136,.25); color: var(--neon);
    }
    .hero h1 {
      font-size: clamp(38px, 6vw, 62px); font-weight: 900; color: white;
      line-height: 1.05; letter-spacing: -1.5px; margin-bottom: 22px;
    }
    .hero h1 span { color: var(--neon); }
    .hero p {
      font-size: 18px; color: rgba(255,255,255,.72);
      max-width: 600px; margin: 0 auto; line-height: 1.75;
    }

    /* ── STATS BAR ── */
    .stats-bar { background: white; padding: 52px 24px; border-bottom: 1px solid var(--border); }
    .stats-grid {
      display: grid; grid-template-columns: repeat(4, 1fr);
      gap: 0; text-align: center;
    }
    .stat-item {
      padding: 8px 24px;
      border-right: 1px solid var(--border);
    }
    .stat-item:last-child { border-right: none; }
    .stat-num {
      font-size: clamp(32px, 4vw, 44px); font-weight: 900;
      color: var(--green); line-height: 1; margin-bottom: 8px;
    }
    .stat-label { font-size: 13px; color: var(--muted); font-weight: 500; }
    @media (max-width: 640px) {
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .stat-item { border-right: none; border-bottom: 1px solid var(--border); padding: 20px; }
      .stat-item:last-child { border-bottom: none; }
    }

    /* ── SECTION HELPERS ── */
    .section-white { background: white; padding: 88px 24px; }
    .section-light { background: var(--light); padding: 88px 24px; }
    .section-dark  {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 88px 24px; position: relative; overflow: hidden;
    }
    .section-dark::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 600px; height: 600px; top: -160px; right: -100px;
      background: radial-gradient(circle, rgba(0,178,7,.14) 0%, transparent 65%);
    }
    .section-dark .container { position: relative; z-index: 1; }

    .eyebrow-green {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 16px;
      background: rgba(0,178,7,.1); color: var(--green);
    }
    .section-label { text-align: center; }
    h2.section-title {
      text-align: center; font-size: clamp(26px, 4vw, 38px);
      font-weight: 800; color: #111827; line-height: 1.2; margin-bottom: 14px;
    }
    h2.section-title.light { color: white; }
    p.section-sub {
      text-align: center; font-size: 17px; color: var(--muted);
      max-width: 620px; margin: 0 auto 56px; line-height: 1.7;
    }
    p.section-sub.light { color: rgba(255,255,255,.65); }

    /* ── MISSION / STORY ── */
    .story-layout {
      display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;
    }
    @media (max-width: 860px) { .story-layout { grid-template-columns: 1fr; gap: 40px; } }

    .story-text h2 {
      font-size: clamp(26px, 3.5vw, 36px); font-weight: 800; color: #111827;
      line-height: 1.2; margin-bottom: 20px;
    }
    .story-text h2 span { color: var(--green); }
    .story-text p { font-size: 16px; color: var(--muted); line-height: 1.8; margin-bottom: 18px; }
    .story-text p:last-child { margin-bottom: 0; }

    .story-visual {
      background: linear-gradient(135deg, rgba(0,178,7,.08) 0%, rgba(0,255,136,.05) 100%);
      border: 1px solid rgba(0,178,7,.15); border-radius: 24px;
      padding: 48px 40px; text-align: center;
    }
    .story-visual .big-icon {
      font-size: 64px; color: var(--green); margin-bottom: 24px; display: block;
    }
    .story-visual h3 { font-size: 22px; font-weight: 800; color: #111827; margin-bottom: 12px; }
    .story-visual p { font-size: 15px; color: var(--muted); line-height: 1.7; }

    /* ── VALUES ── */
    .values-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px;
    }
    .value-card {
      background: white; border: 1px solid var(--border);
      border-radius: 16px; padding: 32px 28px;
      transition: box-shadow .2s, transform .2s;
    }
    .value-card:hover { box-shadow: 0 8px 32px rgba(0,178,7,.1); transform: translateY(-3px); }
    .value-icon {
      width: 52px; height: 52px; border-radius: 14px;
      background: rgba(0,178,7,.1); color: var(--green);
      display: flex; align-items: center; justify-content: center;
      font-size: 20px; margin-bottom: 18px;
    }
    .value-card h3 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 10px; }
    .value-card p { font-size: 14px; color: var(--muted); line-height: 1.7; }

    /* ── HOW IT WORKS ── */
    .ecosystem-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 24px;
    }
    .eco-card {
      background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
      border-radius: 16px; padding: 32px 24px; text-align: center;
      transition: background .2s, border-color .2s;
    }
    .eco-card:hover { background: rgba(0,178,7,.1); border-color: rgba(0,178,7,.3); }
    .eco-icon {
      font-size: 36px; color: var(--neon); margin-bottom: 18px; display: block;
    }
    .eco-card h3 { font-size: 16px; font-weight: 700; color: white; margin-bottom: 10px; }
    .eco-card p { font-size: 14px; color: rgba(255,255,255,.6); line-height: 1.7; }

    /* ── MILESTONES ── */
    .timeline {
      max-width: 720px; margin: 0 auto; position: relative;
    }
    .timeline::before {
      content: ''; position: absolute; left: 50%; top: 0; bottom: 0;
      width: 2px; background: var(--border); transform: translateX(-50%);
    }
    @media (max-width: 600px) {
      .timeline::before { left: 20px; }
    }
    .tl-item {
      display: flex; gap: 32px; margin-bottom: 44px; position: relative;
    }
    .tl-item:nth-child(odd) { flex-direction: row-reverse; text-align: right; }
    @media (max-width: 600px) {
      .tl-item, .tl-item:nth-child(odd) { flex-direction: row; text-align: left; padding-left: 48px; }
    }
    .tl-dot {
      position: absolute; left: 50%; top: 4px; transform: translateX(-50%);
      width: 14px; height: 14px; border-radius: 50%;
      background: var(--green); border: 3px solid white;
      box-shadow: 0 0 0 2px var(--green); flex-shrink: 0;
    }
    @media (max-width: 600px) { .tl-dot { left: 14px; } }
    .tl-content { width: calc(50% - 32px); }
    @media (max-width: 600px) { .tl-content { width: 100%; } }
    .tl-year { font-size: 12px; font-weight: 700; color: var(--green); letter-spacing: 1px; text-transform: uppercase; margin-bottom: 6px; }
    .tl-content h4 { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 6px; }
    .tl-content p { font-size: 14px; color: var(--muted); line-height: 1.65; }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 88px 24px; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 500px; height: 500px; top: -120px; right: -80px;
      background: radial-gradient(circle, rgba(0,178,7,.15) 0%, transparent 65%);
    }
    .cta-inner { position: relative; z-index: 1; }
    .cta-section h2 { font-size: clamp(28px, 4vw, 44px); font-weight: 900; color: white; margin-bottom: 16px; }
    .cta-section p { font-size: 17px; color: rgba(255,255,255,.7); max-width: 540px; margin: 0 auto 40px; line-height: 1.7; }
    .cta-buttons { display: flex; gap: 16px; justify-content: center; flex-wrap: wrap; }
    .btn-primary {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--green); color: white;
      padding: 15px 36px; border-radius: 10px; font-size: 16px; font-weight: 700;
      transition: background .2s, transform .15s;
    }
    .btn-primary:hover { background: var(--green-dark); transform: translateY(-2px); }
    .btn-outline {
      display: inline-flex; align-items: center; gap: 10px;
      background: transparent; color: white;
      border: 1.5px solid rgba(255,255,255,.35);
      padding: 15px 36px; border-radius: 10px; font-size: 16px; font-weight: 600;
      transition: border-color .2s, background .2s, transform .15s;
    }
    .btn-outline:hover { border-color: white; background: rgba(255,255,255,.07); transform: translateY(-2px); }

    footer.footer { margin-top: 0; }
  </style>
</head>
<body>
<?php include __DIR__ . '/../components/header.php'; ?>

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <span class="eyebrow"><?= $fr ? 'Notre histoire' : 'Our story' ?></span>
    <h1><?= $fr
      ? 'Construire le commerce local de <span>demain</span>'
      : 'Building the local commerce of <span>tomorrow</span>' ?></h1>
    <p><?= $fr
      ? 'OCSAPP est une marketplace locale 100% canadienne qui connecte les communautés du Québec avec des vendeurs, fournisseurs et chauffeurs de confiance.'
      : 'OCSAPP is a 100% Canadian local marketplace connecting Quebec communities with trusted sellers, suppliers, and drivers.' ?></p>
  </div>
</section>

<!-- STATS BAR -->
<section class="stats-bar">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-num">4</div>
        <div class="stat-label"><?= $fr ? 'Types de membres' : 'Member types' ?></div>
      </div>
      <div class="stat-item">
        <div class="stat-num">100%</div>
        <div class="stat-label"><?= $fr ? 'Canadien' : 'Canadian' ?></div>
      </div>
      <div class="stat-item">
        <div class="stat-num">0</div>
        <div class="stat-label"><?= $fr ? 'Émissions - livraisons' : 'Emission deliveries' ?></div>
      </div>
      <div class="stat-item">
        <div class="stat-num">2</div>
        <div class="stat-label"><?= $fr ? 'Langues officielles' : 'Official languages' ?></div>
      </div>
    </div>
  </div>
</section>

<!-- MISSION / STORY -->
<section class="section-white">
  <div class="container">
    <div class="story-layout">
      <div class="story-text">
        <span class="eyebrow-green"><?= $fr ? 'Notre mission' : 'Our mission' ?></span>
        <h2><?= $fr
          ? 'Une plateforme bâtie pour les <span>communautés locales</span>'
          : 'A platform built for <span>local communities</span>' ?></h2>
        <p><?= $fr
          ? 'OCSAPP est née d\'une conviction simple : le commerce local mérite une infrastructure numérique aussi puissante que celle des géants mondiaux - mais conçue pour les réalités québécoises.'
          : 'OCSAPP was born from a simple belief: local commerce deserves digital infrastructure as powerful as global giants - but built for Quebec realities.' ?></p>
        <p><?= $fr
          ? 'Nous connectons les acheteurs avec des vendeurs de leur région, nous donnons aux fournisseurs un canal de distribution efficace, et nous offrons aux chauffeurs un emploi flexible avec des livraisons à zéro émission.'
          : 'We connect buyers with sellers in their region, give suppliers an efficient distribution channel, and offer drivers flexible work with zero-emission deliveries.' ?></p>
        <p><?= $fr
          ? 'Notre vision : un écosystème où chaque transaction renforce l\'économie locale et réduit l\'empreinte carbone du commerce au Québec.'
          : 'Our vision: an ecosystem where every transaction strengthens the local economy and reduces the carbon footprint of commerce in Quebec.' ?></p>
      </div>
      <div class="story-visual">
        <span class="big-icon"><i class="fas fa-leaf"></i></span>
        <h3><?= $fr ? 'Zéro émission, 100% impact' : 'Zero emissions, 100% impact' ?></h3>
        <p><?= $fr
          ? 'Toutes nos livraisons sont effectuées par des chauffeurs utilisant des véhicules à faibles émissions ou zéro émission. Commerce local, impact global.'
          : 'All our deliveries are made by drivers using low or zero-emission vehicles. Local commerce, global impact.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- VALUES -->
<section class="section-light">
  <div class="container">
    <div class="section-label"><span class="eyebrow-green"><?= $fr ? 'Nos valeurs' : 'Our values' ?></span></div>
    <h2 class="section-title"><?= $fr ? 'Ce qui nous guide' : 'What guides us' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'Chaque décision que nous prenons est ancrée dans ces principes fondamentaux.'
      : 'Every decision we make is grounded in these core principles.' ?></p>

    <div class="values-grid">
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-handshake"></i></div>
        <h3><?= $fr ? 'Confiance' : 'Trust' ?></h3>
        <p><?= $fr
          ? 'Chaque vendeur, fournisseur et chauffeur est vérifié par notre équipe. Vous pouvez acheter et vendre en toute confiance.'
          : 'Every seller, supplier, and driver is verified by our team. Buy and sell with complete confidence.' ?></p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-users"></i></div>
        <h3><?= $fr ? 'Communauté' : 'Community' ?></h3>
        <p><?= $fr
          ? 'Nous construisons des liens durables entre les membres de la communauté locale, pas seulement des transactions.'
          : 'We build lasting bonds between local community members, not just transactions.' ?></p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-leaf"></i></div>
        <h3><?= $fr ? 'Durabilité' : 'Sustainability' ?></h3>
        <p><?= $fr
          ? 'La livraison à zéro émission n\'est pas une option - c\'est notre engagement fondamental envers l\'environnement.'
          : 'Zero-emission delivery isn\'t an option - it\'s our core commitment to the environment.' ?></p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-balance-scale"></i></div>
        <h3><?= $fr ? 'Équité' : 'Fairness' ?></h3>
        <p><?= $fr
          ? 'Des frais transparents, des règles claires et un traitement égal pour tous les membres de notre écosystème.'
          : 'Transparent fees, clear rules, and equal treatment for every member of our ecosystem.' ?></p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-bolt"></i></div>
        <h3><?= $fr ? 'Innovation' : 'Innovation' ?></h3>
        <p><?= $fr
          ? 'Nous améliorons constamment notre plateforme pour rester à la pointe des besoins du commerce local.'
          : 'We continuously improve our platform to stay ahead of local commerce needs.' ?></p>
      </div>
      <div class="value-card">
        <div class="value-icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3><?= $fr ? 'Local d\'abord' : 'Local first' ?></h3>
        <p><?= $fr
          ? 'Nous privilégions toujours les solutions qui bénéficient directement aux commerces et résidents de nos régions.'
          : 'We always prioritize solutions that directly benefit businesses and residents in our regions.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ECOSYSTEM -->
<section class="section-dark">
  <div class="container">
    <div class="section-label"><span class="eyebrow"><?= $fr ? 'Notre écosystème' : 'Our ecosystem' ?></span></div>
    <h2 class="section-title light"><?= $fr ? 'Quatre acteurs, une plateforme' : 'Four players, one platform' ?></h2>
    <p class="section-sub light"><?= $fr
      ? 'OCSAPP réunit quatre types de membres en un écosystème fluide et interconnecté.'
      : 'OCSAPP brings together four types of members in a seamless, interconnected ecosystem.' ?></p>

    <div class="ecosystem-grid">
      <div class="eco-card">
        <span class="eco-icon"><i class="fas fa-shopping-bag"></i></span>
        <h3><?= $fr ? 'Acheteurs' : 'Buyers' ?></h3>
        <p><?= $fr
          ? 'Commandez des produits locaux, livrés rapidement à votre porte par nos chauffeurs certifiés.'
          : 'Order local products, delivered quickly to your door by our certified drivers.' ?></p>
      </div>
      <div class="eco-card">
        <span class="eco-icon"><i class="fas fa-store"></i></span>
        <h3><?= $fr ? 'Vendeurs' : 'Sellers' ?></h3>
        <p><?= $fr
          ? 'Ouvrez votre boutique en ligne et atteignez des milliers de clients locaux sans infrastructure technique complexe.'
          : 'Open your online store and reach thousands of local customers without complex technical infrastructure.' ?></p>
      </div>
      <div class="eco-card">
        <span class="eco-icon"><i class="fas fa-warehouse"></i></span>
        <h3><?= $fr ? 'Fournisseurs' : 'Suppliers' ?></h3>
        <p><?= $fr
          ? 'Distribuez vos produits aux vendeurs et entreprises de votre région via notre réseau logistique.'
          : 'Distribute your products to sellers and businesses in your region via our logistics network.' ?></p>
      </div>
      <div class="eco-card">
        <span class="eco-icon"><i class="fas fa-truck"></i></span>
        <h3><?= $fr ? 'Chauffeurs' : 'Drivers' ?></h3>
        <p><?= $fr
          ? 'Livrez des commandes B2C et B2B selon votre horaire, avec des revenus hebdomadaires transparents.'
          : 'Deliver B2C and B2B orders on your schedule, with transparent weekly earnings.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- MILESTONES -->
<section class="section-white">
  <div class="container">
    <div class="section-label"><span class="eyebrow-green"><?= $fr ? 'Notre parcours' : 'Our journey' ?></span></div>
    <h2 class="section-title"><?= $fr ? 'Les grandes étapes' : 'Key milestones' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'De l\'idée au lancement - les moments qui ont défini OCSAPP.'
      : 'From idea to launch - the moments that defined OCSAPP.' ?></p>

    <div class="timeline">
      <div class="tl-item">
        <div class="tl-dot"></div>
        <div class="tl-content">
          <div class="tl-year">2023</div>
          <h4><?= $fr ? 'L\'idée prend forme' : 'The idea takes shape' ?></h4>
          <p><?= $fr
            ? 'Constatant le manque d\'une marketplace locale vraiment adaptée au Québec, l\'équipe fondatrice commence à concevoir OCSAPP.'
            : 'Noticing the lack of a truly Quebec-adapted local marketplace, the founding team begins designing OCSAPP.' ?></p>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot"></div>
        <div class="tl-content">
          <div class="tl-year">2024</div>
          <h4><?= $fr ? 'Développement de la plateforme' : 'Platform development' ?></h4>
          <p><?= $fr
            ? 'Construction de l\'infrastructure complète : marketplace, portail vendeur, portail fournisseur, portail chauffeur et application mobile.'
            : 'Building the complete infrastructure: marketplace, seller portal, supplier portal, driver portal, and mobile app.' ?></p>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot"></div>
        <div class="tl-content">
          <div class="tl-year">2025</div>
          <h4><?= $fr ? 'Lancement bêta' : 'Beta launch' ?></h4>
          <p><?= $fr
            ? 'Lancement sur le West Island de Montréal avec les premiers vendeurs, fournisseurs et chauffeurs. Les premiers clients reçoivent leur commande.'
            : 'Launch in Montreal\'s West Island with the first sellers, suppliers, and drivers. First customers receive their orders.' ?></p>
        </div>
      </div>
      <div class="tl-item">
        <div class="tl-dot"></div>
        <div class="tl-content">
          <div class="tl-year">2026</div>
          <h4><?= $fr ? 'Expansion en cours' : 'Expansion underway' ?></h4>
          <p><?= $fr
            ? 'Déploiement des offres distribution B2B, renforcement du réseau de chauffeurs et extension des zones de livraison à travers le Grand Montréal.'
            : 'Rollout of B2B distribution offerings, strengthening the driver network, and extending delivery zones across Greater Montreal.' ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- LOCATION / REGION -->
<section class="section-light">
  <div class="container">
    <div class="section-label"><span class="eyebrow-green"><?= $fr ? 'Notre région' : 'Our region' ?></span></div>
    <h2 class="section-title"><?= $fr ? 'Fièrement enracinés au Québec' : 'Proudly rooted in Quebec' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'OCSAPP opère dans la région de Montréal et du West Island - une des communautés les plus dynamiques et diversifiées du Canada.'
      : 'OCSAPP operates in the Montreal and West Island region - one of Canada\'s most dynamic and diverse communities.' ?></p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px;">
      <div style="background: white; border: 1px solid var(--border); border-radius: 16px; padding: 32px 28px; display: flex; gap: 20px; align-items: flex-start;">
        <div style="width:48px; height:48px; border-radius:12px; background:rgba(0,178,7,.1); color:var(--green); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;"><i class="fas fa-flag"></i></div>
        <div>
          <h3 style="font-size:15px; font-weight:700; color:#111827; margin-bottom:8px;"><?= $fr ? '100% Canadien' : '100% Canadian' ?></h3>
          <p style="font-size:14px; color:var(--muted); line-height:1.7;"><?= $fr ? 'Fondé, développé et opéré au Canada. Nos données restent sur des serveurs canadiens.' : 'Founded, developed, and operated in Canada. Our data stays on Canadian servers.' ?></p>
        </div>
      </div>
      <div style="background: white; border: 1px solid var(--border); border-radius: 16px; padding: 32px 28px; display: flex; gap: 20px; align-items: flex-start;">
        <div style="width:48px; height:48px; border-radius:12px; background:rgba(0,178,7,.1); color:var(--green); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;"><i class="fas fa-language"></i></div>
        <div>
          <h3 style="font-size:15px; font-weight:700; color:#111827; margin-bottom:8px;"><?= $fr ? 'Bilingue de naissance' : 'Bilingual by design' ?></h3>
          <p style="font-size:14px; color:var(--muted); line-height:1.7;"><?= $fr ? 'Interface complète en français et en anglais. Nous respectons les deux langues officielles du Québec.' : 'Full interface in French and English. We respect both of Quebec\'s official languages.' ?></p>
        </div>
      </div>
      <div style="background: white; border: 1px solid var(--border); border-radius: 16px; padding: 32px 28px; display: flex; gap: 20px; align-items: flex-start;">
        <div style="width:48px; height:48px; border-radius:12px; background:rgba(0,178,7,.1); color:var(--green); display:flex; align-items:center; justify-content:center; font-size:20px; flex-shrink:0;"><i class="fas fa-map-marker-alt"></i></div>
        <div>
          <h3 style="font-size:15px; font-weight:700; color:#111827; margin-bottom:8px;"><?= $fr ? 'Montréal & West Island' : 'Montreal & West Island' ?></h3>
          <p style="font-size:14px; color:var(--muted); line-height:1.7;"><?= $fr ? 'Notre zone de lancement couvre l\'une des régions les plus actives et commerçantes du Québec.' : 'Our launch zone covers one of Quebec\'s most active and commercially vibrant regions.' ?></p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-inner">
      <h2><?= $fr ? 'Rejoignez l\'écosystème OCSAPP' : 'Join the OCSAPP ecosystem' ?></h2>
      <p><?= $fr
        ? 'Que vous soyez acheteur, vendeur, fournisseur ou chauffeur, votre place est ici. Créez votre compte gratuitement aujourd\'hui.'
        : 'Whether you\'re a buyer, seller, supplier, or driver - your place is here. Create your free account today.' ?></p>
      <div class="cta-buttons">
        <a href="<?= url('/register') ?>" class="btn-primary">
          <i class="fas fa-user-plus"></i>
          <?= $fr ? 'Créer un compte' : 'Create an account' ?>
        </a>
        <a href="<?= url('/contact') ?>" class="btn-outline">
          <i class="fas fa-envelope"></i>
          <?= $fr ? 'Nous contacter' : 'Contact us' ?>
        </a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
