<?php
/**
 * OCSAPP Seller Central - Landing Page
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
  <title><?= $fr ? 'Vendeur Central - Ouvrez Votre Boutique sur OCSAPP' : 'Seller Central - Open Your Store on OCSAPP' ?></title>
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

    /* ── PACKAGES ── */
    .packages-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; margin-top: 48px; }
    .pkg-card {
      border-radius: 16px; border: 2px solid var(--border);
      padding: 28px 22px; background: white; position: relative;
      transition: transform .2s, box-shadow .2s;
      display: flex; flex-direction: column;
    }
    .pkg-card:hover { transform: translateY(-4px); box-shadow: 0 16px 40px rgba(0,0,0,.15); }
    .pkg-popular {
      position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
      color: white; font-size: 10px; font-weight: 700;
      padding: 3px 14px; border-radius: 20px; white-space: nowrap; letter-spacing: .5px;
    }
    .pkg-name { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
    .pkg-desc { font-size: 12px; color: rgba(255,255,255,.55); margin-bottom: 12px; line-height: 1.5; }
    .pkg-price { margin: 14px 0 0; }
    .pkg-price-num { font-size: 38px; font-weight: 900; line-height: 1; display: block; }
    .pkg-price-lbl { font-size: 11px; color: rgba(255,255,255,.45); font-weight: 600; text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; display: block; }
    .pkg-ess .pkg-price-num { color: #4ade80; }
    .pkg-exp .pkg-price-num { color: #93c5fd; }
    .pkg-pre .pkg-price-num { color: #c084fc; }
    .pkg-ent .pkg-price-num { color: #94a3b8; }
    .pkg-divider { border: none; border-top: 1px solid rgba(255,255,255,.12); margin: 18px 0; }
    .pkg-features { list-style: none; flex: 1; }
    .pkg-features li { font-size: 12.5px; padding: 5px 0; display: flex; gap: 8px; align-items: flex-start; line-height: 1.5; color: rgba(255,255,255,.85); }
    .pkg-features li i { font-size: 11px; flex-shrink: 0; margin-top: 2px; }
    .pkg-features li.inherited { color: rgba(255,255,255,.35); }
    .pkg-features li.inherited i { color: rgba(255,255,255,.35); }
    .pkg-cta {
      display: flex; align-items: center; justify-content: center;
      margin-top: 28px; padding: 10px 20px;
      border-radius: 8px; font-size: 13px; font-weight: 700;
      transition: opacity .2s, transform .15s; color: white;
    }
    .pkg-cta:hover { opacity: .88; transform: translateY(-1px); }
    .pkg-cta.outline { background: rgba(255,255,255,.08) !important; border: 1px solid rgba(255,255,255,.2); }
    .pkg-cta.outline:hover { background: rgba(255,255,255,.15) !important; }

    .pkg-ess { background: #14532d; border-color: #16a34a; }
    .pkg-ess .pkg-name { color: #4ade80; }
    .pkg-ess .pkg-features li:not(.inherited) i { color: #4ade80; }
    .pkg-ess .pkg-cta { background: #16a34a; }
    .pkg-ess .pkg-divider { border-color: rgba(74,222,128,.2); }

    .pkg-exp { background: #1e3a8a; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,.2); }
    .pkg-exp .pkg-name { color: #93c5fd; }
    .pkg-exp .pkg-popular { background: #3b82f6; }
    .pkg-exp .pkg-features li:not(.inherited) i { color: #93c5fd; }
    .pkg-exp .pkg-cta { background: #3b82f6; }
    .pkg-exp .pkg-divider { border-color: rgba(147,197,253,.2); }

    .pkg-pre { background: #3b0764; border-color: #9333ea; }
    .pkg-pre .pkg-name { color: #c084fc; }
    .pkg-pre .pkg-features li:not(.inherited) i { color: #c084fc; }
    .pkg-pre .pkg-cta { background: #9333ea; }
    .pkg-pre .pkg-divider { border-color: rgba(192,132,252,.2); }

    .pkg-ent { background: #0f172a; border-color: #475569; }
    .pkg-ent .pkg-name { color: #94a3b8; }
    .pkg-ent .pkg-features li:not(.inherited) i { color: #94a3b8; }
    .pkg-ent .pkg-cta { background: #334155; }
    .pkg-ent .pkg-divider { border-color: rgba(148,163,184,.15); }

    .plan-note {
      background: rgba(251,146,60,.08); border-left: 4px solid var(--orange);
      border-radius: 0 12px 12px 0; padding: 16px 22px;
      font-size: 14px; color: #fed7aa; line-height: 1.7; margin-top: 32px;
    }
    .plan-note strong { color: #fdba74; }

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
      .pillars-grid, .packages-grid, .features-grid { grid-template-columns: 1fr 1fr; }
      .how-3 { flex-direction: column; align-items: center; }
      .how-arrow { transform: rotate(90deg); padding: 0; margin: 4px 0; }
      .how-step { padding: 20px 0; }
      .contact-meta { grid-template-columns: 1fr; }
    }
    @media (max-width: 620px) {
      .pillars-grid, .packages-grid, .features-grid { grid-template-columns: 1fr; }
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
      <span class="eyebrow eyebrow-neon"><i class="fas fa-store"></i> <?= $fr ? 'Programme Vendeur' : 'Seller Program' ?></span>
    </div>
    <h1>
      <?= $fr ? 'Ouvrez Votre Boutique sur' : 'Open Your Store on the' ?><br>
      <span class="accent"><?= $fr ? 'la Marketplace OCSAPP' : 'OCSAPP Marketplace' ?></span>
    </h1>
    <p class="hero-sub">
      <?= $fr
        ? 'Rejoignez le marché hyperlocal du Grand Montréal. Listez vos produits, gérez vos commandes, et laissez OCSAPP s\'occuper de la livraison — depuis un seul tableau de bord.'
        : 'Reach customers across Greater Montréal and beyond. List your products, manage orders, and let OCSAPP handle delivery — all from one seller dashboard.' ?>
    </p>
    <div class="hero-badges">
      <span class="hero-badge"><i class="fas fa-leaf"></i> <?= $fr ? 'Livraison zéro émission' : 'Zero-Emission Delivery' ?></span>
      <span class="hero-badge"><i class="fas fa-map-marker-alt"></i> West Island · Grand Montréal</span>
      <span class="hero-badge"><i class="fas fa-store"></i> <?= $fr ? 'Votre boutique de marque' : 'Your Branded Storefront' ?></span>
      <span class="hero-badge"><i class="fas fa-mobile-alt"></i> <?= $fr ? 'Gérez depuis mobile' : 'Manage from Mobile' ?></span>
    </div>
    <div class="hero-ctas">
      <?php if (function_exists('isLoggedIn') && isLoggedIn() && function_exists('userRole') && userRole() === 'seller'): ?>
        <a href="<?= url('seller/dashboard') ?>" class="btn-primary-lg">
          <i class="fas fa-tachometer-alt"></i> <?= $fr ? 'Tableau de bord vendeur' : 'Go to Seller Dashboard' ?>
        </a>
      <?php else: ?>
        <a href="<?= url('register') ?>?role=seller" class="btn-primary-lg">
          <i class="fas fa-rocket"></i> <?= $fr ? 'Ouvrir ma boutique — C\'est gratuit' : 'Open Your Store — It\'s Free' ?>
        </a>
        <a href="<?= url('seller/login') ?>" class="btn-outline-lg">
          <i class="fas fa-sign-in-alt"></i> <?= $fr ? 'Connexion vendeur' : 'Seller Login' ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ═══════════════ STATS BAR ═══════════════ -->
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-clock"></i></div>
      <div>
        <div class="stat-pill-val">2–5</div>
        <div class="stat-pill-lbl"><?= $fr ? 'jours d\'approbation' : 'Day review time' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-layer-group"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? '4 forfaits' : '4 Plans' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Sans frais pour commencer' : 'Free to start' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-percent"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? 'Commission' : 'Commission' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Selon votre forfait' : 'Based on your plan' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-mobile-alt"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? 'Mobile' : 'Mobile' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Gérez n\'importe où' : 'Manage anywhere' ?></div>
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
    <h2 class="section-title"><?= $fr ? 'Vendre sur OCSAPP, c\'est simple.' : 'Selling on OCSAPP is Simple.' ?></h2>
    <p class="section-sub">
      <?= $fr
        ? 'OCSAPP est un écosystème numérique tout-en-un canadien-québécois connectant vendeurs et acheteurs à travers un réseau de livraison hyperlocal à <strong>objectif zéro émission</strong>.'
        : 'OCSAPP is a Canadian-Quebec all-in-one marketplace connecting sellers and buyers through a <strong>zero-emission goal hyper-local delivery network</strong>.' ?>
    </p>

    <div class="pillars-grid">
      <div class="pillar-card g">
        <div class="pillar-num">1</div>
        <div class="pillar-icon">🏪</div>
        <h3><?= $fr ? 'Listez vos produits' : 'List Your Products' ?></h3>
        <p><?= $fr
          ? 'Créez votre boutique de marque OCSAPP en quelques minutes. Téléversez vos produits, fixez vos prix, ajoutez vos photos et gérez votre inventaire complet depuis un seul tableau de bord.'
          : 'Create your branded OCSAPP storefront in minutes. Upload products, set pricing, add images, and manage your full inventory — everything from one dashboard.' ?></p>
      </div>
      <div class="pillar-card b">
        <div class="pillar-num">2</div>
        <div class="pillar-icon">🛒</div>
        <h3><?= $fr ? 'Les acheteurs vous découvrent' : 'Buyers Discover &amp; Order' ?></h3>
        <p><?= $fr
          ? 'Les clients qui naviguent sur la marketplace OCSAPP trouvent votre boutique, ajoutent au panier et passent commande. Vous êtes notifié dès qu\'une commande arrive — confirmez et préparez pour le ramassage.'
          : 'Customers browsing the OCSAPP marketplace find your store, add to cart, and check out. You get notified the moment an order lands — confirm, pack, and update order status.' ?></p>
      </div>
      <div class="pillar-card p">
        <div class="pillar-num">3</div>
        <div class="pillar-icon">🚚</div>
        <h3><?= $fr ? 'OCSAPP livre pour vous' : 'OCSAPP Delivers' ?></h3>
        <p><?= $fr
          ? 'Notre réseau de chauffeurs ODA passe chez vous et livre aux acheteurs. Vous gérez le produit, nous gérons le dernier kilomètre — avec suivi en direct pour tout le monde.'
          : 'Our ODA driver network picks up from your location and delivers to customers. You handle the product; we handle the last mile — with live tracking for everyone involved.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ HOW IT WORKS - 3 STEPS ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Mise en route' : 'Getting Started' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Opérationnel en 3 étapes' : 'Up and Running in 3 Steps' ?></h2>
    <p class="section-sub"><?= $fr ? 'De votre inscription à votre première vente — voici exactement à quoi vous attendre.' : 'From registration to your first sale — here\'s exactly what to expect.' ?></p>

    <div class="how-3">
      <div class="how-step">
        <div class="how-num">1</div>
        <span class="how-icon">📝</span>
        <h3><?= $fr ? 'Inscrivez-vous gratuitement' : 'Register for Free' ?></h3>
        <p><?= $fr
          ? 'Créez votre compte vendeur en quelques minutes. Renseignez vos informations, décrivez vos produits et choisissez votre forfait. Aucun appel téléphonique requis.'
          : 'Create your seller account in minutes. Add your business details, describe your products, and choose your plan. No phone call required.' ?></p>
        <span class="how-time">~5 <?= $fr ? 'minutes' : 'minutes' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">2</div>
        <span class="how-icon">✅</span>
        <h3><?= $fr ? 'Approuvé en 2 à 5 jours' : 'Approved in 2–5 Days' ?></h3>
        <p><?= $fr
          ? 'Notre équipe examine chaque candidature personnellement. Vous recevrez une décision par courriel. Vous pouvez explorer le tableau de bord dès votre inscription pendant la révision.'
          : 'Our team personally reviews every seller application. You\'ll receive a decision by email. You can explore the dashboard immediately while we review.' ?></p>
        <span class="how-time"><?= $fr ? '2 à 5 jours ouvrables' : '2–5 business days' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">3</div>
        <span class="how-icon">🚀</span>
        <h3><?= $fr ? 'Commencez à vendre' : 'Start Selling' ?></h3>
        <p><?= $fr
          ? 'Votre boutique est en ligne sur OCSAPP. Ajoutez vos produits, recevez des commandes et laissez les chauffeurs ODA s\'occuper de la livraison — vous encaissez.'
          : 'Your store is live on OCSAPP. Add products, receive orders, and let ODA drivers handle delivery — you focus on the product and get paid.' ?></p>
        <span class="how-time"><?= $fr ? 'Dès le premier jour' : 'From day one' ?></span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ WHAT YOU GET ═══════════════ -->
<section class="section-white">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Ce que vous obtenez' : 'What You Get' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Un tableau de bord conçu pour votre succès' : 'A Dashboard Built Around Your Success' ?></h2>
    <p class="section-sub"><?= $fr ? 'Tout ce dont vous avez besoin pour gérer votre boutique, vos commandes et vos revenus — au même endroit.' : 'Everything you need to run your store, manage orders, and track earnings — all in one place.' ?></p>

    <div class="features-grid">
      <div class="feature-card">
        <span class="feature-icon">🏪</span>
        <h4><?= $fr ? 'Votre boutique de marque' : 'Your Branded Storefront' ?></h4>
        <p><?= $fr
          ? 'Une page boutique dédiée, visible par tous les acheteurs OCSAPP. Logo, bannière, description — construisez une identité que vos clients reconnaissent et à laquelle ils reviennent.'
          : 'A dedicated store page visible to all OCSAPP customers. Logo, banner, description — build an identity your customers recognize and return to.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📦</span>
        <h4><?= $fr ? 'Gestion des commandes' : 'Order Management' ?></h4>
        <p><?= $fr
          ? 'Recevez, confirmez et gérez chaque commande depuis un seul écran. Mettez à jour le statut en temps réel pour que vos clients sachent toujours où en est leur commande.'
          : 'Receive, confirm, and manage every order from one screen. Update status in real time so customers always know where their order stands.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📊</span>
        <h4><?= $fr ? 'Analytiques &amp; ventes' : 'Analytics &amp; Insights' ?></h4>
        <p><?= $fr
          ? 'Suivez vos meilleurs produits, vos tendances de revenus et le comportement de vos clients. Prenez des décisions éclairées sur les prix et les promotions grâce aux données.'
          : 'Track your top products, revenue trends, and customer behaviour. Use data to make smarter decisions about pricing and promotions.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">💳</span>
        <h4><?= $fr ? 'Revenus &amp; versements' : 'Earnings &amp; Payouts' ?></h4>
        <p><?= $fr
          ? 'Suivez vos gains, consultez l\'historique des versements et téléchargez vos relevés. OCSAPP verse sur un calendrier régulier avec une transparence totale par commande.'
          : 'Track your earnings, view payout history, and download statements. OCSAPP pays out on a regular schedule with full transparency per order.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">🚚</span>
        <h4><?= $fr ? 'Livraison ODA incluse' : 'ODA Delivery Included' ?></h4>
        <p><?= $fr
          ? 'Les chauffeurs ODA ramassent les commandes chez vous et les livrent aux acheteurs. Aucune logistique propre requise. Chaque livraison s\'inscrit dans notre objectif zéro émission.'
          : 'ODA drivers pick up orders from your location and deliver to buyers. No in-house logistics needed. Every delivery works toward our zero-emission goal.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📱</span>
        <h4><?= $fr ? 'Gestion depuis mobile' : 'Mobile Management' ?></h4>
        <p><?= $fr
          ? 'Votre tableau de bord complet, accessible sur n\'importe quel appareil. Confirmez des commandes, mettez à jour vos stocks et suivez vos revenus où que vous soyez.'
          : 'Your full dashboard, accessible on any device. Confirm orders, update inventory, and track earnings from wherever you are.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ TRUST / SOCIAL PROOF ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Notre réseau de vendeurs' : 'Our Seller Network' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Des boutiques dans toutes les catégories' : 'Stores Across Every Category' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'OCSAPP accueille des vendeurs locaux au Québec dans toutes les catégories — des petites boutiques artisanales aux commerces établis de la région.'
      : 'OCSAPP welcomes local Quebec sellers across all product categories — from small artisan shops to established local retailers.' ?></p>

    <div class="category-chips">
      <span class="category-chip">🥗 <?= $fr ? 'Alimentation &amp; boissons' : 'Food &amp; Beverage' ?></span>
      <span class="category-chip">🧴 <?= $fr ? 'Santé &amp; beauté' : 'Health &amp; Beauty' ?></span>
      <span class="category-chip">🏠 <?= $fr ? 'Maison &amp; décoration' : 'Home &amp; Living' ?></span>
      <span class="category-chip">👕 <?= $fr ? 'Mode &amp; accessoires' : 'Clothing &amp; Accessories' ?></span>
      <span class="category-chip">🌱 <?= $fr ? 'Bio &amp; naturel' : 'Organic &amp; Natural' ?></span>
      <span class="category-chip">📦 <?= $fr ? 'Biens de consommation' : 'Consumer Goods' ?></span>
    </div>

    <div class="quote-card">
      <p class="quote-text">
        <?= $fr
          ? '« Depuis qu\'on est sur OCSAPP, on reçoit des commandes régulières sans avoir à gérer la livraison. On s\'occupe de nos produits — eux s\'occupent du reste. »'
          : '"Since joining OCSAPP, we receive consistent orders without managing delivery ourselves. We focus on our products — they handle the rest."' ?>
      </p>
      <div class="quote-author">
        <div class="quote-avatar">🏪</div>
        <div>
          <div class="quote-name"><?= $fr ? 'Vendeur OCSAPP — Alimentation, Montréal' : 'OCSAPP Seller — Food &amp; Beverage, Montréal' ?></div>
          <div class="quote-role"><?= $fr ? 'Forfait Essential · Actif depuis 2024' : 'Essential Plan · Active since 2024' ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ PACKAGES ═══════════════ -->
<section class="section-dark">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-neon"><?= $fr ? 'Forfaits vendeur' : 'Seller Plans' ?></span>
    </div>
    <h2 class="section-title light"><?= $fr ? 'Choisissez le bon forfait pour votre boutique' : 'Choose the Right Plan for Your Store' ?></h2>
    <p class="section-sub light"><?= $fr ? 'Démarrez gratuitement sur Essential. Passez à niveau à tout moment pour débloquer plus d\'outils et de visibilité.' : 'Start free on Essential. Upgrade any time to unlock more tools and visibility.' ?></p>

    <div class="packages-grid">

      <div class="pkg-card pkg-ess">
        <div class="pkg-name">Essential</div>
        <div class="pkg-desc"><?= $fr ? 'Tout ce qu\'il faut pour ouvrir' : 'Everything you need to open' ?></div>
        <div class="pkg-price">
          <span class="pkg-price-num"><?= $fr ? 'Gratuit' : 'Free' ?></span>
          <span class="pkg-price-lbl"><?= $fr ? 'pour commencer' : 'to start' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Jusqu\'à 30 produits actifs' : 'Up to 30 active listings' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Boutique de marque OCSAPP' : 'Branded OCSAPP storefront' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Tableau de bord de commandes' : 'Order management dashboard' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestion des stocks' : 'Inventory management' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Accès réseau livraison ODA' : 'ODA delivery network access' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Suivi des versements' : 'Payout tracking' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Messagerie client' : 'Customer messaging' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestion depuis mobile' : 'Mobile management' ?></li>
        </ul>
        <a href="<?= url('register') ?>?role=seller" class="pkg-cta outline"><?= $fr ? 'Commencer gratuitement' : 'Get Started Free' ?></a>
      </div>

      <div class="pkg-card pkg-exp">
        <div class="pkg-popular"><?= $fr ? 'Le plus populaire' : 'Most Popular' ?></div>
        <div class="pkg-name">Experience</div>
        <div class="pkg-desc"><?= $fr ? 'Pour les boutiques en croissance' : 'For growing stores' ?></div>
        <div class="pkg-price">
          <span class="pkg-price-num" style="font-size:22px;opacity:.7"><?= $fr ? 'Bientôt' : 'Coming Soon' ?></span>
          <span class="pkg-price-lbl"><?= $fr ? 'tarif à confirmer' : 'pricing to be confirmed' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce qu\'Essential inclut' : 'Everything in Essential' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Produits illimités' : 'Unlimited product listings' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Analytiques avancées' : 'Advanced analytics' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Outils de promotions' : 'Discount &amp; promotion tools' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Support vendeur prioritaire' : 'Priority seller support' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Taux de commission réduit' : 'Lower commission rate' ?></li>
        </ul>
        <a href="mailto:sellers@ocsapp.ca?subject=Experience%20Plan%20Inquiry" class="pkg-cta"><?= $fr ? 'Postuler' : 'Get Experience' ?></a>
      </div>

      <div class="pkg-card pkg-pre">
        <div class="pkg-name">Prestige</div>
        <div class="pkg-desc"><?= $fr ? 'Pour les vendeurs établis' : 'For established sellers' ?></div>
        <div class="pkg-price">
          <span class="pkg-price-num" style="font-size:22px;opacity:.7"><?= $fr ? 'Bientôt' : 'Coming Soon' ?></span>
          <span class="pkg-price-lbl"><?= $fr ? 'tarif à confirmer' : 'pricing to be confirmed' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce qu\'Experience inclut' : 'Everything in Experience' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Placement en vedette' : 'Featured store placement' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Bannières publicitaires' : 'Homepage &amp; category banner ads' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestionnaire de compte dédié' : 'Dedicated account manager' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Rapports de ventes avancés' : 'Advanced sales reporting' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Commission la plus basse' : 'Lowest commission rate' ?></li>
        </ul>
        <a href="mailto:sellers@ocsapp.ca?subject=Prestige%20Plan%20Inquiry" class="pkg-cta outline"><?= $fr ? 'Postuler' : 'Get Prestige' ?></a>
      </div>

      <div class="pkg-card pkg-ent">
        <div class="pkg-name">Enterprise</div>
        <div class="pkg-desc"><?= $fr ? 'Solutions sur mesure, grande échelle' : 'Custom solutions, large scale' ?></div>
        <div class="pkg-price">
          <span class="pkg-price-num" style="font-size:22px;opacity:.7"><?= $fr ? 'Bientôt' : 'Coming Soon' ?></span>
          <span class="pkg-price-lbl"><?= $fr ? 'tarif à confirmer' : 'pricing to be confirmed' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce que Prestige inclut' : 'Everything in Prestige' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Intégration sur mesure' : 'Custom integration support' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestion multi-emplacements' : 'Multi-location management' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Équipe de succès dédiée' : 'Dedicated seller success team' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Commission personnalisée' : 'Custom commission agreement' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Intégration accompagnée' : 'White-glove onboarding' ?></li>
        </ul>
        <a href="mailto:sellers@ocsapp.ca?subject=Enterprise%20Plan%20Inquiry" class="pkg-cta outline"><?= $fr ? 'Nous contacter' : 'Contact Us' ?></a>
      </div>

    </div>

    <div class="plan-note">
      <?= $fr
        ? '<strong>Remarque :</strong> Tous les nouveaux comptes démarrent sur <strong>Essential</strong> sans frais. Pour passer à niveau, contactez <strong>sellers@ocsapp.ca</strong> ou votre gestionnaire de compte. Les changements prennent effet dans un délai d\'un jour ouvrable.'
        : '<strong>Note:</strong> All new seller accounts start on the <strong>Essential</strong> plan at no cost. To upgrade, contact <strong>sellers@ocsapp.ca</strong> or your account manager. Changes take effect within one business day.' ?>
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
    <p class="section-sub"><?= $fr ? 'Tout ce que vous devez savoir avant d\'ouvrir votre boutique.' : 'Everything you need to know before opening your store.' ?></p>

    <div class="faq">
      <?php
      $faqs = $fr ? [
        ['Combien ça coûte d\'ouvrir une boutique sur OCSAPP ?',
         'Rien. Le forfait Essential est entièrement gratuit. Vous payez seulement lorsque vous vendez — OCSAPP prélève une petite commission par vente complétée. Les forfaits payants (Experience à 50$/mois, Prestige à 100$/mois) offrent des produits illimités, un taux de commission réduit et des outils supplémentaires.'],
        ['Combien de temps dure l\'approbation ?',
         'La plupart des candidatures sont examinées dans un délai de 2 à 5 jours ouvrables. Vous serez notifié par courriel. Si vous n\'avez pas eu de nouvelles après 5 jours, contactez sellers@ocsapp.ca avec votre courriel d\'inscription.'],
        ['Comment fonctionne la livraison ? Est-ce que j\'envoie les commandes moi-même ?',
         'Non — vous n\'expédiez pas les commandes vous-même. Le réseau de chauffeurs ODA d\'OCSAPP gère toutes les livraisons. Lorsqu\'une commande est prête, un chauffeur ODA est envoyé pour la ramasser chez vous et la livrer directement au client.'],
        ['Comment et quand suis-je payé ?',
         'OCSAPP traite les versements sur un calendrier régulier après confirmation de la livraison des commandes. Vos gains sont suivis en temps réel dans votre tableau de bord. Pour les questions de versement, contactez sellers@ocsapp.ca.'],
        ['Combien de produits puis-je lister ?',
         'Sur le forfait Essential, vous pouvez lister jusqu\'à 30 produits actifs à la fois. Les forfaits Experience et plus offrent des listes de produits illimitées. Contactez sellers@ocsapp.ca pour discuter d\'une mise à niveau.'],
        ['Puis-je changer de forfait plus tard ?',
         'Oui — vous pouvez passer à niveau à tout moment. Contactez sellers@ocsapp.ca ou votre gestionnaire de compte. Les changements prennent effet dans un délai d\'un jour ouvrable. Tous les forfaits payants sont au mois, sans engagement à long terme.'],
      ] : [
        ['Is there a cost to open a store on OCSAPP?',
         'No. The Essential plan is completely free to start. You only pay when you sell — OCSAPP takes a small commission per completed order. Paid plans (Experience at $50/month, Prestige at $100/month) offer unlimited listings, lower commission rates, and additional tools.'],
        ['How long does seller approval take?',
         'Most seller applications are reviewed within 2–5 business days. You\'ll receive a decision by email. If you haven\'t heard back after 5 business days, email sellers@ocsapp.ca with your registration email and we\'ll follow up.'],
        ['How does delivery work? Do I ship orders myself?',
         'No — you do not ship orders yourself. OCSAPP\'s ODA driver network handles all deliveries. When an order is ready, an ODA driver is dispatched to pick it up from your location and deliver it directly to the customer.'],
        ['How and when do I get paid?',
         'OCSAPP processes seller payouts on a regular schedule after orders are confirmed as delivered. Your earnings are tracked in real time in your seller dashboard. For payout questions, contact sellers@ocsapp.ca.'],
        ['How many products can I list?',
         'On the Essential plan, you can list up to 30 active products at a time. The Experience plan and above offer unlimited product listings. Contact sellers@ocsapp.ca to discuss upgrading.'],
        ['Can I upgrade my plan later?',
         'Yes — you can upgrade at any time. Contact sellers@ocsapp.ca or your account manager. Changes take effect within one business day. All paid plans are month-to-month with no long-term commitment.'],
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
        ? 'Notre équipe de succès vendeur est disponible pour vous aider avec votre candidature, la configuration de votre boutique et tout ce qui suit. Incluez toujours votre <strong style="color:#86efac;">courriel d\'inscription ou nom de boutique</strong> pour que nous trouvions votre compte rapidement.'
        : 'Our seller success team is available to assist with applications, store setup, and everything that comes after. Always include your <strong style="color:#86efac;">registered email or store name</strong> when reaching out so we can find your account quickly.' ?></p>
      <div class="contact-meta">
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Support vendeur' : 'Seller Support' ?></div>
          <div class="c-value">sellers@ocsapp.ca</div>
          <div class="c-sub"><?= $fr ? 'Candidatures, forfaits &amp; boutiques' : 'Applications, plans &amp; store questions' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Téléphone' : 'Phone' ?></div>
          <div class="c-value">514-746-3789</div>
          <div class="c-sub"><?= $fr ? 'Lun–Sam · 8h – 20h' : 'Mon–Sat · 8 am – 8 pm' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Info générale' : 'General Info' ?></div>
          <div class="c-value">info@ocsapp.ca</div>
          <div class="c-sub"><?= $fr ? 'Renseignements généraux' : 'General inquiries' ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ FINAL CTA ═══════════════ -->
<section class="cta-section">
  <div class="container">
    <h2><?= $fr ? 'Votre boutique vous attend.' : 'Your Store Is Waiting.' ?></h2>
    <p><?= $fr ? 'Inscrivez-vous en quelques minutes. Approuvé en 2 à 5 jours. Sans frais pour commencer — jamais.' : 'Register in minutes. Reviewed in 2–5 business days. No fees to start — ever.' ?></p>
    <div>
      <a href="<?= url('register') ?>?role=seller" class="btn-white-lg">
        <i class="fas fa-store"></i> <?= $fr ? 'Ouvrir ma boutique gratuitement' : 'Open Your Store Free' ?>
      </a>
      <a href="mailto:sellers@ocsapp.ca" class="btn-ghost-lg">
        <i class="fas fa-envelope"></i> <?= $fr ? 'Contacter notre équipe' : 'Contact Our Team' ?>
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
