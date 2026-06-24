<?php
/**
 * OCSAPP Buyer Central - Landing Page
 * Marketing-first: persuasion over documentation - EN/FR bilingual
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$t  = function_exists('getTranslations') ? getTranslations($currentLang) : [];
$fr = $currentLang === 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $fr ? 'Acheteur Central - Magasinez sur OCSAPP' : 'Buyer Central - Shop on OCSAPP' ?></title>
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
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
      --blue:       #3b82f6;
      --purple:     #7c3aed;
      --orange:     #f97316;
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); line-height: 1.65; }
    a { text-decoration: none; }
    .container { max-width: 1160px; margin: 0 auto; padding: 0 24px; }

    .eyebrow {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 16px;
    }
    .eyebrow-green { background: rgba(0,178,7,.12); color: var(--green); }
    .eyebrow-neon  { background: rgba(0,255,136,.12); border: 1px solid rgba(0,255,136,.25); color: var(--neon); }

    .section-label { text-align: center; margin-bottom: 12px; }
    h2.section-title {
      text-align: center; font-size: clamp(26px,4vw,38px);
      font-weight: 800; color: #111827; line-height: 1.2; margin-bottom: 14px;
    }
    h2.section-title.light { color: white; }
    p.section-sub {
      text-align: center; font-size: 17px; color: var(--muted);
      max-width: 640px; margin: 0 auto 52px; line-height: 1.7;
    }
    p.section-sub.light { color: rgba(255,255,255,.65); }

    /* ── SECTION WRAPPERS ── */
    .section-white { background: white;        padding: 88px 24px; }
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
    .section-dark::after {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 400px; height: 400px; bottom: -100px; left: -60px;
      background: radial-gradient(circle, rgba(0,255,136,.07) 0%, transparent 65%);
    }
    .section-dark .container { position: relative; z-index: 1; }

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
    .hero-inner { position: relative; z-index: 1; max-width: 820px; margin: 0 auto; }
    .hero h1 {
      font-size: clamp(38px,6vw,66px); font-weight: 900; color: white;
      line-height: 1.0; letter-spacing: -2px; margin-bottom: 24px;
    }
    .hero h1 .accent { color: var(--neon); }
    .hero-sub {
      font-size: 19px; color: rgba(255,255,255,.7); max-width: 580px;
      margin: 0 auto 44px; line-height: 1.7;
    }
    .hero-badges { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-bottom: 44px; }
    .hero-badge {
      background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
      color: rgba(255,255,255,.75); font-size: 12px; font-weight: 600;
      padding: 6px 14px; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;
    }
    .hero-ctas { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .btn-primary-lg {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--green); color: white; font-weight: 700; font-size: 15px;
      padding: 15px 34px; border-radius: 10px; transition: background .2s, transform .15s;
    }
    .btn-primary-lg:hover { background: var(--green-dark); transform: translateY(-2px); }
    .btn-outline-lg {
      display: inline-flex; align-items: center; gap: 8px;
      border: 2px solid rgba(255,255,255,.25); color: rgba(255,255,255,.85);
      font-weight: 600; font-size: 15px; padding: 13px 28px; border-radius: 10px;
      transition: border-color .2s, color .2s;
    }
    .btn-outline-lg:hover { border-color: rgba(255,255,255,.55); color: white; }

    /* ── STATS BAR ── */
    .stats-bar { background: white; border-bottom: 1px solid var(--border); padding: 20px 0; }
    .stats-inner { display: flex; justify-content: center; flex-wrap: wrap; }
    .stat-pill { display: flex; align-items: center; gap: 12px; padding: 12px 32px; border-right: 1px solid var(--border); }
    .stat-pill:last-child { border-right: none; }
    .stat-pill-icon { width: 36px; height: 36px; border-radius: 8px; background: rgba(0,178,7,.1); color: var(--green); display: flex; align-items: center; justify-content: center; font-size: 15px; flex-shrink: 0; }
    .stat-pill-val { font-size: 20px; font-weight: 800; color: #111827; line-height: 1; }
    .stat-pill-lbl { font-size: 11px; color: var(--muted); margin-top: 2px; }

    /* ── 3 PILLARS ── */
    .pillars-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; margin-top: 48px; }
    .pillar-card { border-radius: 16px; padding: 36px 28px; border: 2px solid transparent; position: relative; overflow: hidden; }
    .pillar-card.g { background: #f0fdf4; border-color: #86efac; }
    .pillar-card.b { background: #eff6ff; border-color: #bfdbfe; }
    .pillar-card.p { background: #f5f3ff; border-color: #ddd6fe; }
    .pillar-num { position: absolute; top: 16px; right: 20px; font-size: 64px; font-weight: 900; opacity: .06; line-height: 1; }
    .pillar-icon { width: 56px; height: 56px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 26px; margin-bottom: 20px; }
    .pillar-card.g .pillar-icon { background: #d1fae5; }
    .pillar-card.b .pillar-icon { background: #dbeafe; }
    .pillar-card.p .pillar-icon { background: #ede9fe; }
    .pillar-card h3 { font-size: 19px; font-weight: 700; color: #111827; margin-bottom: 10px; }
    .pillar-card p  { font-size: 14px; color: var(--muted); line-height: 1.75; }

    /* ── HOW IT WORKS - 3 horizontal steps ── */
    .how-3 { display: flex; align-items: flex-start; gap: 0; margin-top: 56px; }
    .how-step { flex: 1; text-align: center; padding: 0 28px; }
    .how-arrow { font-size: 26px; color: var(--green); padding-top: 36px; flex-shrink: 0; opacity: .35; font-weight: 900; }
    .how-num {
      width: 48px; height: 48px; border-radius: 50%; background: var(--green);
      color: white; font-size: 20px; font-weight: 900;
      display: inline-flex; align-items: center; justify-content: center; margin-bottom: 18px;
    }
    .how-icon { font-size: 38px; margin-bottom: 14px; display: block; }
    .how-step h3 { font-size: 17px; font-weight: 700; color: #111827; margin-bottom: 10px; }
    .how-step p  { font-size: 14px; color: var(--muted); line-height: 1.7; }
    .how-time {
      display: inline-block; margin-top: 16px; font-size: 12px; font-weight: 700;
      color: var(--green); background: rgba(0,178,7,.1); padding: 5px 14px; border-radius: 20px;
    }

    /* ── FEATURES ── */
    .features-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-top: 48px; }
    .feature-card { border-radius: 14px; padding: 26px; border: 1px solid var(--border); background: white; }
    .feature-icon { font-size: 28px; margin-bottom: 14px; display: block; }
    .feature-card h4 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 8px; }
    .feature-card p  { font-size: 13px; color: var(--muted); line-height: 1.7; }

    /* ── TRUST / CATEGORY CHIPS ── */
    .category-chips { display: flex; flex-wrap: wrap; gap: 12px; justify-content: center; margin-top: 40px; }
    .category-chip {
      display: inline-flex; align-items: center; gap: 8px;
      background: white; border: 1px solid var(--border);
      padding: 10px 20px; border-radius: 40px; font-size: 14px; font-weight: 600; color: var(--text);
      box-shadow: 0 1px 4px rgba(0,0,0,.06);
    }
    .quote-card {
      background: white; border-radius: 16px; border: 1px solid var(--border);
      padding: 36px 40px; max-width: 680px; margin: 52px auto 0;
      position: relative; box-shadow: 0 4px 24px rgba(0,0,0,.06);
    }
    .quote-card::before {
      content: '"'; position: absolute; top: -18px; left: 36px;
      font-size: 80px; color: var(--green); font-family: Georgia, serif; line-height: 1; opacity: .25;
    }
    .quote-text { font-size: 17px; color: #111827; line-height: 1.8; font-style: italic; margin-bottom: 20px; }
    .quote-author { display: flex; align-items: center; gap: 14px; }
    .quote-avatar { width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(135deg, #d1fae5, #86efac); display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0; }
    .quote-name { font-size: 14px; font-weight: 700; color: #111827; }
    .quote-role { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* ── FAQ ── */
    .faq { margin-top: 48px; }
    .faq-item { border-bottom: 1px solid var(--border); }
    .faq-item:last-child { border-bottom: none; }
    .faq-q {
      width: 100%; text-align: left; background: none; border: none; cursor: pointer;
      font-size: 15px; font-weight: 700; color: #111827; padding: 20px 0;
      display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    .faq-q:hover { color: var(--green); }
    .faq-q i { font-size: 12px; color: var(--muted); transition: transform .25s; flex-shrink: 0; }
    .faq-q.open i { transform: rotate(180deg); color: var(--green); }
    .faq-a { font-size: 14px; color: var(--muted); line-height: 1.8; padding: 0 0 20px 0; display: none; }
    .faq-a.open { display: block; }

    /* ── CONTACT BOX ── */
    .contact-box {
      background: linear-gradient(135deg, #0c1a0e 0%, #0d3010 100%);
      border-radius: 20px; padding: 52px 48px; color: white; position: relative; overflow: hidden;
    }
    .contact-box::before {
      content: ''; position: absolute; top: -40px; right: -40px;
      width: 280px; height: 280px; border-radius: 50%; background: rgba(0,178,7,.12);
    }
    .contact-box h3 { font-size: 26px; font-weight: 800; margin-bottom: 12px; position: relative; }
    .contact-box p  { font-size: 15px; opacity: .8; line-height: 1.8; max-width: 560px; margin-bottom: 36px; position: relative; }
    .contact-meta { display: grid; grid-template-columns: repeat(3,1fr); gap: 28px; position: relative; }
    .contact-item .c-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; opacity: .5; margin-bottom: 4px; }
    .contact-item .c-value { font-size: 16px; font-weight: 700; color: #86efac; }
    .contact-item .c-sub   { font-size: 12px; opacity: .55; margin-top: 2px; }

    /* ── CTA SECTION ── */
    .cta-section { background: var(--green); padding: 80px 24px; text-align: center; }
    .cta-section h2 { font-size: clamp(28px,4vw,42px); font-weight: 900; color: white; margin-bottom: 16px; line-height: 1.2; }
    .cta-section p  { font-size: 17px; color: rgba(255,255,255,.8); margin-bottom: 36px; max-width: 520px; margin-left: auto; margin-right: auto; }
    .btn-white-lg {
      display: inline-flex; align-items: center; gap: 8px;
      background: white; color: var(--green); font-weight: 800; font-size: 15px;
      padding: 15px 36px; border-radius: 10px; margin: 0 8px;
      transition: box-shadow .2s, transform .15s;
    }
    .btn-white-lg:hover { box-shadow: 0 8px 24px rgba(0,0,0,.2); transform: translateY(-2px); }
    .btn-ghost-lg {
      display: inline-flex; align-items: center; gap: 8px;
      border: 2px solid rgba(255,255,255,.5); color: white; font-weight: 700; font-size: 15px;
      padding: 13px 28px; border-radius: 10px; margin: 0 8px; transition: border-color .2s;
    }
    .btn-ghost-lg:hover { border-color: white; }

    footer.footer { margin-top: 0; }

    @media (max-width: 900px) {
      .pillars-grid, .features-grid { grid-template-columns: 1fr 1fr; }
      .how-3 { flex-direction: column; align-items: center; }
      .how-arrow { transform: rotate(90deg); padding: 0; margin: 4px 0; }
      .how-step { padding: 20px 0; }
      .contact-meta { grid-template-columns: 1fr; }
    }
    @media (max-width: 620px) {
      .pillars-grid, .features-grid { grid-template-columns: 1fr; }
      .stats-inner { flex-direction: column; align-items: center; }
      .stat-pill { border-right: none; border-bottom: 1px solid var(--border); width: 100%; justify-content: center; }
      .stat-pill:last-child { border-bottom: none; }
      .hero-ctas { flex-direction: column; align-items: center; }
      .contact-box { padding: 36px 24px; }
      .quote-card { padding: 28px 24px; }
    }
  </style>
</head>
<body>

<?php require __DIR__ . '/../components/header.php'; ?>

<!-- ═══════════════ HERO ═══════════════ -->
<section class="hero">
  <div class="hero-inner">
    <div class="section-label">
      <span class="eyebrow eyebrow-neon"><i class="fas fa-shopping-bag"></i> <?= $fr ? 'Programme Acheteur' : 'Buyer Program' ?></span>
    </div>
    <h1>
      <?= $fr ? 'Magasinez Local,' : 'Shop Local,' ?><br>
      <?= $fr ? 'Livré chez vous par' : 'Delivered by' ?> <span class="accent">OCSAPP</span>
    </h1>
    <p class="hero-sub">
      <?= $fr
        ? 'Explorez des milliers de produits locaux au Grand Montréal. Commandez en ligne, suivez votre livraison en temps réel, et recevez vos achats à domicile — sans effort.'
        : 'Explore thousands of local products across Greater Montréal. Order online, track delivery in real time, and get your purchases delivered to your door — effortlessly.' ?>
    </p>
    <div class="hero-badges">
      <span class="hero-badge"><i class="fas fa-leaf"></i> <?= $fr ? 'Livraison zéro émission' : 'Zero-Emission Delivery' ?></span>
      <span class="hero-badge"><i class="fas fa-map-marker-alt"></i> West Island · Grand Montréal</span>
      <span class="hero-badge"><i class="fas fa-store"></i> <?= $fr ? 'Boutiques locales' : 'Local Stores' ?></span>
      <span class="hero-badge"><i class="fas fa-mobile-alt"></i> <?= $fr ? 'Suivi en temps réel' : 'Real-Time Tracking' ?></span>
    </div>
    <div class="hero-ctas">
      <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
        <a href="<?= url('home') ?>" class="btn-primary-lg">
          <i class="fas fa-shopping-cart"></i> <?= $fr ? 'Aller au Marketplace' : 'Go to Marketplace' ?>
        </a>
      <?php else: ?>
        <a href="<?= url('register') ?>" class="btn-primary-lg">
          <i class="fas fa-user-plus"></i> <?= $fr ? 'Créer mon compte — C\'est gratuit' : 'Create Account — It\'s Free' ?>
        </a>
        <a href="<?= url('buyer/login') ?>" class="btn-outline-lg">
          <i class="fas fa-sign-in-alt"></i> <?= $fr ? 'Connexion acheteur' : 'Buyer Login' ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ STATS BAR ═══════════════ -->
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-box-open"></i></div>
      <div>
        <div class="stat-pill-val">10,000+</div>
        <div class="stat-pill-lbl"><?= $fr ? 'produits disponibles' : 'Products available' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-store"></i></div>
      <div>
        <div class="stat-pill-val">500+</div>
        <div class="stat-pill-lbl"><?= $fr ? 'boutiques locales' : 'Local stores' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-clock"></i></div>
      <div>
        <div class="stat-pill-val">15–30</div>
        <div class="stat-pill-lbl"><?= $fr ? 'min · livraison' : 'min · delivery' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-leaf"></i></div>
      <div>
        <div class="stat-pill-val">100%</div>
        <div class="stat-pill-lbl"><?= $fr ? 'objectif éco-livraison' : 'Eco-delivery goal' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-headset"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? 'Lun–Sam' : 'Mon–Sat' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Support 8h – 20h' : '8 am – 8 pm support' ?></div>
      </div>
    </div>
  </div>
</div>

<!-- ═══════════════ WHY OCSAPP - 3 PILLARS ═══════════════ -->
<section class="section-white">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Pourquoi OCSAPP' : 'Why OCSAPP' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Magasiner local n\'a jamais été aussi simple.' : 'Shopping local has never been this easy.' ?></h2>
    <p class="section-sub">
      <?= $fr
        ? 'OCSAPP est un écosystème numérique tout-en-un canadien-québécois qui connecte les acheteurs aux boutiques locales et aux fournisseurs via un réseau de livraison hyperlocal à <strong>objectif zéro émission</strong>.'
        : 'OCSAPP is a Canadian-Quebec digital ecosystem connecting buyers to local stores and suppliers through a <strong>zero-emission goal hyper-local delivery network</strong>.' ?>
    </p>

    <div class="pillars-grid">
      <div class="pillar-card g">
        <div class="pillar-num">1</div>
        <div class="pillar-icon">🛍️</div>
        <h3><?= $fr ? 'Parcourez des milliers de produits' : 'Browse Thousands of Products' ?></h3>
        <p><?= $fr
          ? 'Explorez l\'épicerie en ligne OCSAPP, notre Virtual Mall et les boutiques locales de votre région — tout au même endroit. Trouvez de la nourriture fraîche, des articles ménagers, des vêtements et bien plus encore.'
          : 'Explore the OCSAPP online grocery store, our Virtual Mall, and local stores in your area — all in one place. Find fresh food, household essentials, clothing, and much more.' ?></p>
      </div>
      <div class="pillar-card b">
        <div class="pillar-num">2</div>
        <div class="pillar-icon">🛒</div>
        <h3><?= $fr ? 'Commandez en quelques clics' : 'Order in a Few Clicks' ?></h3>
        <p><?= $fr
          ? 'Ajoutez vos articles au panier, choisissez votre créneau de livraison et payez en toute sécurité en ligne. Votre commande est transmise instantanément à la boutique — aucun appel téléphonique requis.'
          : 'Add items to your cart, choose your delivery window, and pay securely online. Your order goes instantly to the store — no phone calls required.' ?></p>
      </div>
      <div class="pillar-card p">
        <div class="pillar-num">3</div>
        <div class="pillar-icon">🚚</div>
        <h3><?= $fr ? 'Reçu chez vous rapidement' : 'Delivered to Your Door Fast' ?></h3>
        <p><?= $fr
          ? 'Un chauffeur ODA récupère votre commande et la livre chez vous. Suivez chaque étape en temps réel sur votre téléphone — de la boutique à votre porte, avec un objectif zéro émission.'
          : 'An ODA driver picks up your order and delivers it to you. Track every step in real time on your phone — from the store to your door, with a zero-emission goal.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ HOW IT WORKS - 3 STEPS ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Comment ça marche' : 'How It Works' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Prêt à magasiner en 3 étapes' : 'Ready to Shop in 3 Steps' ?></h2>
    <p class="section-sub"><?= $fr ? 'De votre inscription à votre première livraison — voici exactement à quoi vous attendre.' : 'From creating your account to your first delivery — here\'s exactly what to expect.' ?></p>

    <div class="how-3">
      <div class="how-step">
        <div class="how-num">1</div>
        <span class="how-icon">👤</span>
        <h3><?= $fr ? 'Créez votre compte' : 'Create Your Account' ?></h3>
        <p><?= $fr
          ? 'Inscrivez-vous gratuitement en quelques minutes. Renseignez votre adresse, choisissez vos préférences de livraison, et vous êtes prêt à magasiner.'
          : 'Sign up for free in minutes. Add your delivery address, set your preferences, and you\'re ready to shop.' ?></p>
        <span class="how-time"><?= $fr ? '~2 minutes' : '~2 minutes' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">2</div>
        <span class="how-icon">🛒</span>
        <h3><?= $fr ? 'Parcourez &amp; commandez' : 'Browse &amp; Order' ?></h3>
        <p><?= $fr
          ? 'Trouvez les produits que vous voulez parmi les boutiques locales et l\'épicerie OCSAPP. Ajoutez au panier et passez à la caisse — livraison ou créneau programmé.'
          : 'Find what you want from local stores and the OCSAPP grocery. Add to cart and check out — immediate delivery or scheduled window.' ?></p>
        <span class="how-time"><?= $fr ? 'À votre rythme' : 'At your own pace' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">3</div>
        <span class="how-icon">📦</span>
        <h3><?= $fr ? 'Suivez &amp; recevez' : 'Track &amp; Receive' ?></h3>
        <p><?= $fr
          ? 'Suivez votre chauffeur ODA en temps réel jusqu\'à votre porte. Recevez une notification à chaque étape. Aucune surprise — juste votre commande, livrée comme promis.'
          : 'Track your ODA driver in real time right to your door. Get notified at every step. No surprises — just your order, delivered as promised.' ?></p>
        <span class="how-time"><?= $fr ? '15–30 min' : '15–30 min' ?></span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ WHAT YOU GET ═══════════════ -->
<section class="section-white">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Votre expérience d\'achat' : 'Your Shopping Experience' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Tout ce dont vous avez besoin, livré.' : 'Everything You Need, Delivered.' ?></h2>
    <p class="section-sub"><?= $fr ? 'OCSAPP combine épicerie locale, boutiques de quartier et livraison rapide en une seule plateforme simple.' : 'OCSAPP combines local grocery, neighbourhood stores, and fast delivery into one simple platform.' ?></p>

    <div class="features-grid">
      <div class="feature-card">
        <span class="feature-icon">🥗</span>
        <h4><?= $fr ? 'Épicerie en ligne' : 'Online Grocery' ?></h4>
        <p><?= $fr
          ? 'Faites votre épicerie sur OCSAPP Shop Smart — des produits frais, biologiques et locaux livrés directement chez vous. Programmez vos achats hebdomadaires ou commandez à la demande.'
          : 'Do your groceries on OCSAPP Shop Smart — fresh, organic, and locally sourced products delivered to your door. Schedule weekly shops or order on demand.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">🏬</span>
        <h4><?= $fr ? 'Virtual Mall' : 'Virtual Mall' ?></h4>
        <p><?= $fr
          ? 'Naviguez dans notre Virtual Mall pour acheter des produits variés de vos boutiques locales préférées — vêtements, articles ménagers, beauté et plus encore, le tout depuis votre téléphone.'
          : 'Browse our Virtual Mall to shop a variety of products from your favourite local stores — clothing, home goods, beauty, and more, all from your phone.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📍</span>
        <h4><?= $fr ? 'Suivi en temps réel' : 'Real-Time Tracking' ?></h4>
        <p><?= $fr
          ? 'Suivez votre chauffeur ODA sur une carte en direct dès que votre commande est en route. Sachez exactement quand elle arrivera — à la minute près.'
          : 'Track your ODA driver on a live map the moment your order is on its way. Know exactly when it arrives — down to the minute.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📅</span>
        <h4><?= $fr ? 'Livraison programmée' : 'Scheduled Delivery' ?></h4>
        <p><?= $fr
          ? 'Choisissez un créneau de livraison qui vous convient — ce soir, demain matin ou le week-end. OCSAPP s\'adapte à votre emploi du temps, pas l\'inverse.'
          : 'Choose a delivery window that works for you — tonight, tomorrow morning, or the weekend. OCSAPP fits your schedule, not the other way around.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">🌱</span>
        <h4><?= $fr ? 'Livraison éco-responsable' : 'Eco-Friendly Delivery' ?></h4>
        <p><?= $fr
          ? 'Chaque livraison s\'inscrit dans notre objectif zéro émission au Québec. Magasinez local, réduisez votre empreinte carbone — sans effort supplémentaire de votre part.'
          : 'Every delivery works toward our zero-emission goal in Quebec. Shop local, reduce your carbon footprint — no extra effort on your part.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">💳</span>
        <h4><?= $fr ? 'Paiement sécurisé' : 'Secure Checkout' ?></h4>
        <p><?= $fr
          ? 'Payez en toute sécurité avec carte de crédit, débit ou PayPal. Vos informations de paiement sont chiffrées et protégées. Vos commandes et l\'historique des achats sont toujours accessibles.'
          : 'Pay securely by credit card, debit, or PayPal. Your payment information is encrypted and protected. Your orders and purchase history are always accessible.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ TRUST / SOCIAL PROOF ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Ce que vous pouvez commander' : 'What You Can Order' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Des produits dans toutes les catégories' : 'Products Across Every Category' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'OCSAPP rassemble les boutiques locales du Québec dans toutes les catégories — de l\'épicerie fraîche aux articles de mode, le tout livré depuis votre quartier.'
      : 'OCSAPP brings together local Quebec stores across all categories — from fresh groceries to fashion, all delivered from your neighbourhood.' ?></p>

    <div class="category-chips">
      <span class="category-chip">🥗 <?= $fr ? 'Épicerie &amp; alimentation' : 'Grocery &amp; Food' ?></span>
      <span class="category-chip">🧴 <?= $fr ? 'Santé &amp; beauté' : 'Health &amp; Beauty' ?></span>
      <span class="category-chip">🏠 <?= $fr ? 'Maison &amp; décoration' : 'Home &amp; Living' ?></span>
      <span class="category-chip">👕 <?= $fr ? 'Mode &amp; accessoires' : 'Clothing &amp; Accessories' ?></span>
      <span class="category-chip">🌱 <?= $fr ? 'Bio &amp; naturel' : 'Organic &amp; Natural' ?></span>
      <span class="category-chip">🍽️ <?= $fr ? 'Restaurants &amp; traiteurs' : 'Restaurants &amp; Food Courts' ?></span>
    </div>

    <div class="quote-card">
      <p class="quote-text">
        <?= $fr
          ? '« Je commande mon épicerie hebdomadaire sur OCSAPP depuis quelques mois. Livraison rapide, produits frais, et je soutiens les commerces locaux de mon quartier. C\'est une évidence. »'
          : '"I\'ve been ordering my weekly groceries on OCSAPP for a few months. Fast delivery, fresh products, and I\'m supporting local stores in my neighbourhood. It\'s a no-brainer."' ?>
      </p>
      <div class="quote-author">
        <div class="quote-avatar">🛍️</div>
        <div>
          <div class="quote-name"><?= $fr ? 'Acheteur OCSAPP — Kirkland, QC' : 'OCSAPP Buyer — Kirkland, QC' ?></div>
          <div class="quote-role"><?= $fr ? 'Client actif depuis 2024' : 'Active buyer since 2024' ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ FAQ ═══════════════ -->
<section class="section-white">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green">FAQ</span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Questions fréquentes' : 'Common Questions' ?></h2>
    <p class="section-sub"><?= $fr ? 'Tout ce que vous devez savoir avant de passer votre première commande.' : 'Everything you need to know before placing your first order.' ?></p>

    <div class="faq">
      <?php
      $faqs = $fr ? [
        ['Est-ce que c\'est gratuit de créer un compte acheteur ?',
         'Oui, entièrement gratuit. La création d\'un compte acheteur OCSAPP ne coûte rien. Vous ne payez que les produits que vous achetez, plus les frais de livraison affichés au moment de la commande.'],
        ['Dans quelles zones livrez-vous ?',
         'OCSAPP livre actuellement dans la région du Grand Montréal, y compris West Island et les zones environnantes. Les zones de livraison disponibles s\'affichent automatiquement lors de la saisie de votre adresse.'],
        ['Comment puis-je suivre ma commande ?',
         'Dès qu\'un chauffeur ODA prend en charge votre commande, vous recevez un lien de suivi en temps réel. Vous pouvez suivre la progression sur une carte directement depuis votre téléphone ou votre navigateur.'],
        ['Puis-je programmer une livraison à l\'avance ?',
         'Oui. Lors du passage en caisse, vous pouvez choisir une livraison immédiate ou sélectionner un créneau programmé — ce soir, demain ou plus tard dans la semaine, selon la disponibilité.'],
        ['Que faire si un article de ma commande est manquant ou incorrect ?',
         'Contactez notre équipe de support à info@ocsapp.ca ou par téléphone au 514-746-3789 (Lun–Sam, 8h–20h). Nous traitons toutes les réclamations rapidement. Incluez votre numéro de commande pour un traitement plus rapide.'],
        ['Quels modes de paiement acceptez-vous ?',
         'OCSAPP accepte les cartes de crédit (Visa, Mastercard), les cartes de débit et PayPal. Tous les paiements sont traités de façon sécurisée via Stripe. Vos informations de paiement ne sont jamais stockées en clair sur nos serveurs.'],
      ] : [
        ['Is it free to create a buyer account?',
         'Yes, completely free. Creating an OCSAPP buyer account costs nothing. You only pay for the products you purchase, plus any delivery fees shown at checkout.'],
        ['Which areas do you deliver to?',
         'OCSAPP currently delivers across Greater Montréal, including West Island and surrounding areas. Available delivery zones are shown automatically when you enter your address.'],
        ['How do I track my order?',
         'As soon as an ODA driver picks up your order, you receive a real-time tracking link. You can follow the progress on a live map directly from your phone or browser.'],
        ['Can I schedule a delivery in advance?',
         'Yes. At checkout, you can choose immediate delivery or select a scheduled window — tonight, tomorrow, or later in the week, depending on availability.'],
        ['What if an item in my order is missing or incorrect?',
         'Contact our support team at info@ocsapp.ca or by phone at 514-746-3789 (Mon–Sat, 8 am–8 pm). We handle all claims promptly. Include your order number for faster resolution.'],
        ['What payment methods do you accept?',
         'OCSAPP accepts credit cards (Visa, Mastercard), debit cards, and PayPal. All payments are processed securely via Stripe. Your payment details are never stored in plain text on our servers.'],
      ];
      foreach ($faqs as $faq): ?>
      <div class="faq-item">
        <button class="faq-q" onclick="toggleFaq(this)" aria-expanded="false">
          <?= htmlspecialchars($faq[0]) ?>
          <i class="fas fa-chevron-down"></i>
        </button>
        <div class="faq-a"><?= htmlspecialchars($faq[1]) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ CONTACT ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="contact-box">
      <h3><?= $fr ? 'Nous sommes là pour vous aider' : 'We\'re Here to Help' ?></h3>
      <p><?= $fr
        ? 'Notre équipe de support est disponible pour répondre à toutes vos questions — commandes, livraisons, remboursements ou problèmes de compte. Incluez toujours votre <strong style="color:#86efac;">numéro de commande ou courriel d\'inscription</strong> pour un traitement plus rapide.'
        : 'Our support team is available to answer any questions — orders, deliveries, refunds, or account issues. Always include your <strong style="color:#86efac;">order number or registered email</strong> for faster service.' ?></p>
      <div class="contact-meta">
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Support acheteur' : 'Buyer Support' ?></div>
          <div class="c-value">info@ocsapp.ca</div>
          <div class="c-sub"><?= $fr ? 'Commandes, livraisons &amp; comptes' : 'Orders, deliveries &amp; accounts' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Téléphone' : 'Phone' ?></div>
          <div class="c-value">514-746-3789</div>
          <div class="c-sub"><?= $fr ? 'Lun–Sam · 8h – 20h' : 'Mon–Sat · 8 am – 8 pm' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Marketplace' : 'Marketplace' ?></div>
          <div class="c-value">ocsapp.ca</div>
          <div class="c-sub"><?= $fr ? 'Commencez à magasiner' : 'Start shopping' ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ FINAL CTA ═══════════════ -->
<section class="cta-section">
  <div class="container">
    <h2><?= $fr ? 'Prêt à magasiner local ?' : 'Ready to Shop Local?' ?></h2>
    <p><?= $fr ? 'Créez votre compte en 2 minutes. Gratuit pour toujours. Livraison rapide dès votre première commande.' : 'Create your account in 2 minutes. Free forever. Fast delivery from your very first order.' ?></p>
    <div>
      <a href="<?= url('register') ?>" class="btn-white-lg">
        <i class="fas fa-shopping-bag"></i> <?= $fr ? 'Créer mon compte gratuit' : 'Create Free Account' ?>
      </a>
      <a href="<?= url('home') ?>" class="btn-ghost-lg">
        <i class="fas fa-store"></i> <?= $fr ? 'Parcourir le marketplace' : 'Browse the Marketplace' ?>
      </a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../components/footer.php'; ?>

<script>
function toggleFaq(btn) {
  const answer = btn.nextElementSibling;
  const isOpen = btn.classList.contains('open');
  document.querySelectorAll('.faq-q.open').forEach(b => {
    b.classList.remove('open');
    b.setAttribute('aria-expanded','false');
    b.nextElementSibling.classList.remove('open');
  });
  if (!isOpen) {
    btn.classList.add('open');
    btn.setAttribute('aria-expanded','true');
    answer.classList.add('open');
  }
}
</script>

</body>
</html>
