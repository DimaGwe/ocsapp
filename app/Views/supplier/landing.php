<?php
/**
 * OCSAPP Supplier Central - Landing Page (v4)
 * Marketing-first rebuild: persuasion over documentation - EN/FR bilingual
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
  <title><?= $fr ? 'Portail Fournisseur - Partenariat OCSAPP' : 'Supplier Central - Partner with OCSAPP' ?></title>
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
    .pkg-rate { margin: 14px 0 0; }
    .pkg-rate-num { font-size: 38px; font-weight: 900; line-height: 1; display: block; }
    .pkg-rate-lbl { font-size: 11px; color: rgba(255,255,255,.45); font-weight: 600; text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; display: block; }
    .pkg-ess .pkg-rate-num { color: #4ade80; }
    .pkg-exp .pkg-rate-num { color: #93c5fd; }
    .pkg-pre .pkg-rate-num { color: #c084fc; }
    .pkg-ent .pkg-rate-num { color: #94a3b8; }
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

    /* Per-tier card styles */
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

    /* Plan note */
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

    /* Remove global footer top margin so CTA connects directly */
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
      <span class="eyebrow eyebrow-neon"><i class="fas fa-leaf"></i> <?= $fr ? 'Programme Partenaire Fournisseur' : 'Supplier Partner Program' ?></span>
    </div>
    <h1>
      <?= $fr ? 'Transformez vos produits' : 'Turn Your Products Into' ?><br>
      <span class="accent"><?= $fr ? 'en commandes récurrentes' : 'Recurring Purchase Orders' ?></span>
    </h1>
    <p class="hero-sub">
      <?= $fr
        ? 'Listez votre catalogue une fois. Laissez notre réseau de distribution et de vendeurs vous trouver. Recevez des bons de commande directement dans votre tableau de bord - la livraison prise en charge pour vous.'
        : 'List your catalog once. Let our distribution and seller network find you. Receive purchase orders directly to your dashboard - with every delivery handled for you.' ?>
    </p>
    <div class="hero-badges">
      <span class="hero-badge"><i class="fas fa-leaf"></i> <?= $fr ? 'Livraison zéro émission' : 'Zero-Emission Delivery' ?></span>
      <span class="hero-badge"><i class="fas fa-map-marker-alt"></i> <?= $fr ? 'L\'Ouest-de-l\'Île et la région métropolitaine de Montréal' : 'West Island · Greater Montréal' ?></span>
      <span class="hero-badge"><i class="fas fa-clock"></i> <?= $fr ? 'Approuvé en 1 à 3 jours' : 'Approved in 1-3 Days' ?></span>
      <span class="hero-badge"><i class="fas fa-shield-alt"></i> <?= $fr ? 'NEQ vérifié' : 'NEQ Verified' ?></span>
    </div>
    <div class="hero-ctas">
      <a href="<?= url('supplier/apply') ?>" class="btn-primary-lg">
        <i class="fas fa-rocket"></i> <?= $fr ? 'Postuler maintenant - C\'est gratuit' : 'Apply Now - It\'s Free' ?>
      </a>
      <a href="<?= url('supplier/login') ?>" class="btn-outline-lg">
        <i class="fas fa-sign-in-alt"></i> <?= $fr ? 'Connexion fournisseur' : 'Supplier Login' ?>
      </a>
    </div>
  </div>
</section>

<!-- ═══════════════ STATS BAR ═══════════════ -->
<div class="stats-bar">
  <div class="stats-inner">
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-bolt"></i></div>
      <div>
        <div class="stat-pill-val">~15 <?= $fr ? 'min' : 'min' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Pour postuler' : 'To apply' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-check-circle"></i></div>
      <div>
        <div class="stat-pill-val">1-3 <?= $fr ? 'j.' : 'days' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Délai d\'approbation' : 'Review &amp; approval' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-layer-group"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? '4 forfaits' : '4 plans' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Sans frais pour commencer' : 'Free to start' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-leaf"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? 'Zéro' : 'Zero' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Objectif zéro émission' : 'Zero-emission goal' ?></div>
      </div>
    </div>
    <div class="stat-pill">
      <div class="stat-pill-icon"><i class="fas fa-headset"></i></div>
      <div>
        <div class="stat-pill-val"><?= $fr ? 'Lun-Dim' : 'Mon-Sun' ?></div>
        <div class="stat-pill-lbl"><?= $fr ? 'Support 8h - 20h' : '8 am - 8 pm support' ?></div>
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
    <h2 class="section-title"><?= $fr ? 'Votre catalogue. Leur bon de commande.' : 'Your Catalog. Their Purchase Order.' ?></h2>
    <p class="section-sub">
      <?= $fr
        ? 'OCSAPP est un écosystème numérique tout-en-un canadien-québécois connectant fournisseurs, vendeurs, entreprises et acheteurs à travers un réseau de livraison hyperlocal à <strong>objectif zéro émission</strong>.'
        : 'OCSAPP is a Canadian-Quebec digital ecosystem connecting suppliers, sellers, businesses, and buyers through a <strong>zero-emission goal hyper-local delivery network</strong>.' ?>
    </p>

    <div class="pillars-grid">
      <div class="pillar-card g">
        <div class="pillar-num">1</div>
        <div class="pillar-icon">📦</div>
        <h3><?= $fr ? 'Listez une fois, vendez en continu' : 'List Once, Sell Continuously' ?></h3>
        <p><?= $fr
          ? 'Ajoutez vos produits au catalogue OCSAPP une seule fois. Notre réseau de distribution et de vendeurs les trouve quand ils en ont besoin - et les bons de commande arrivent automatiquement dans votre tableau de bord.'
          : 'Add your products to the OCSAPP catalog once. Our distribution and seller network finds them when they need them - and purchase orders land automatically in your dashboard.' ?></p>
      </div>
      <div class="pillar-card b">
        <div class="pillar-num">2</div>
        <div class="pillar-icon">💼</div>
        <h3><?= $fr ? 'Des revenus confirmés, pas des prospects' : 'Confirmed Revenue, Not Leads' ?></h3>
        <p><?= $fr
          ? 'Chaque notification que vous recevez est un bon de commande réel avec un montant, une quantité et une date. Pas de négociation, pas de démarchage à froid. Confirmez, préparez, et encaissez.'
          : 'Every notification you receive is a real purchase order with an amount, quantity, and date. No negotiation, no cold leads. Confirm, fulfill, and get paid.' ?></p>
      </div>
      <div class="pillar-card p">
        <div class="pillar-num">3</div>
        <div class="pillar-icon">🚚</div>
        <h3><?= $fr ? 'On gère chaque livraison pour vous' : 'We Handle Every Delivery' ?></h3>
        <p><?= $fr
          ? 'Notre réseau de livreurs ODA passe chez vous et livre aux acheteurs. Vous vous concentrez sur l\'exécution des commandes. Nous gérons le dernier kilomètre - avec un objectif de livraison zéro émission.'
          : 'Our ODA driver network picks up from your location and delivers to buyers. You focus on fulfilling orders. We handle the last mile - with a zero-emission delivery goal.' ?></p>
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
    <p class="section-sub"><?= $fr ? 'De votre candidature à votre premier bon de commande - voici exactement à quoi vous attendre.' : 'From your application to your first purchase order - here\'s exactly what to expect.' ?></p>

    <div class="how-3">
      <div class="how-step">
        <div class="how-num">1</div>
        <span class="how-icon">📝</span>
        <h3><?= $fr ? 'Postulez en 15 minutes' : 'Apply in 15 Minutes' ?></h3>
        <p><?= $fr
          ? 'Remplissez votre profil, téléversez vos documents d\'entreprise québécois (NEQ, certificat de constitution) et choisissez votre forfait. Aucun appel téléphonique requis.'
          : 'Fill out your profile, upload your Quebec business documents (NEQ, certificate of incorporation), and choose your plan. No phone call required.' ?></p>
        <span class="how-time">~15 <?= $fr ? 'minutes' : 'minutes' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">2</div>
        <span class="how-icon">✅</span>
        <h3><?= $fr ? 'Approuvé en 1 à 3 jours' : 'Approved in 1-3 Days' ?></h3>
        <p><?= $fr
          ? 'Notre équipe examine personnellement chaque candidature. Vous recevrez une décision par courriel avec votre code fournisseur (SUP-XXXXXXXX) - votre identifiant permanent sur le réseau.'
          : 'Our team personally reviews every application. You\'ll receive a decision by email with your Supplier Code (SUP-XXXXXXXX) - your permanent identifier on the network.' ?></p>
        <span class="how-time"><?= $fr ? '1 à 3 jours ouvrables' : '1-3 business days' ?></span>
      </div>

      <div class="how-arrow">→</div>

      <div class="how-step">
        <div class="how-num">3</div>
        <span class="how-icon">📦</span>
        <h3><?= $fr ? 'Commencez à recevoir des bons de commande' : 'Start Receiving Purchase Orders' ?></h3>
        <p><?= $fr
          ? 'Listez vos produits et laissez le réseau travailler. Chaque nouveau BC déclenche une alerte courriel. Confirmez et préparez depuis votre tableau de bord - la facturation et le suivi sont intégrés.'
          : 'List your products and let the network work. Every new PO triggers an email alert. Confirm and prepare from your dashboard - invoicing and tracking built in.' ?></p>
        <span class="how-time"><?= $fr ? 'Dès le premier jour' : 'From day one' ?></span>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ WHAT YOU GET - BENEFIT-LED ═══════════════ -->
<section class="section-white">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Ce que vous obtenez' : 'What You Get' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Un portail conçu pour votre réussite' : 'A Portal Built Around Your Success' ?></h2>
    <p class="section-sub"><?= $fr ? 'Tout ce dont vous avez besoin pour gérer vos commandes, vos stocks et vos paiements - au même endroit.' : 'Everything you need to manage orders, inventory, and payments - all in one place.' ?></p>

    <div class="features-grid">
      <div class="feature-card">
        <span class="feature-icon">📊</span>
        <h4><?= $fr ? 'Visibilité totale sur chaque commande' : 'Full Visibility on Every Order' ?></h4>
        <p><?= $fr
          ? 'Suivez chaque bon de commande en temps réel - de la réception à la confirmation, à la préparation, à la collecte ODA et au paiement. Vous savez toujours où vous en êtes.'
          : 'Track every purchase order in real time - from received to confirmed, preparing, ODA pickup, and payment. You always know exactly where things stand.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">💳</span>
        <h4><?= $fr ? 'Des paiements prévisibles, des termes clairs' : 'Predictable Payments, Clear Terms' ?></h4>
        <p><?= $fr
          ? 'Suivez vos comptes clients, consultez les statuts de paiement et téléchargez vos relevés. OCSAPP règle selon des termes nets convenus - avec une transparence totale sur chaque transaction.'
          : 'Track your receivables, view payment statuses, and download statements. OCSAPP pays on agreed net terms - with full transparency on every transaction.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">📦</span>
        <h4><?= $fr ? 'Votre catalogue complet, un seul endroit' : 'Your Entire Catalog, One Place' ?></h4>
        <p><?= $fr
          ? 'Gérez vos produits, prix, formats d\'emballage et niveaux de stock depuis votre tableau de bord. Mises à jour en masse disponibles. Ce que les acheteurs voient reflète toujours la réalité.'
          : 'Manage products, pricing, pack sizes, and stock levels from your dashboard. Bulk updates available. What buyers see always reflects reality.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">💬</span>
        <h4><?= $fr ? 'Une ligne directe avec notre équipe' : 'A Direct Line to Our Team' ?></h4>
        <p><?= $fr
          ? 'Communiquez directement avec l\'équipe OCSAPP depuis le portail. Pas de tickets externes, pas d\'attente en ligne. Chaque message est associé à votre compte et reçoit une réponse rapide.'
          : 'Communicate directly with the OCSAPP team from inside the portal. No external tickets, no hold queues. Every message is tied to your account and gets a fast response.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">🚚</span>
        <h4><?= $fr ? 'Livraison gérée, empreinte minimale' : 'Delivery Managed, Footprint Minimal' ?></h4>
        <p><?= $fr
          ? 'Notre réseau ODA ramasse les commandes chez vous et les livre aux acheteurs. Aucune logistique propre nécessaire. Chaque livraison s\'inscrit dans notre objectif zéro émission au Québec.'
          : 'Our ODA network picks up orders from you and delivers to buyers. No in-house logistics needed. Every delivery works toward our zero-emission goal in Quebec.' ?></p>
      </div>
      <div class="feature-card">
        <span class="feature-icon">👥</span>
        <h4><?= $fr ? 'Votre équipe, vos règles' : 'Your Team, Your Rules' ?></h4>
        <p><?= $fr
          ? 'Ajoutez des utilisateurs par emplacement, définissez leurs rôles et contrôlez leur accès. Gérez plusieurs emplacements depuis un seul compte - chacun avec ses stocks et ses accès dédiés.'
          : 'Add users per location, define their roles, and control what they can access. Manage multiple locations from one account - each with dedicated inventory and access.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ TRUST / SOCIAL PROOF ═══════════════ -->
<section class="section-light">
  <div class="container">
    <div class="section-label">
      <span class="eyebrow eyebrow-green"><?= $fr ? 'Notre réseau fournisseur' : 'Our Supplier Network' ?></span>
    </div>
    <h2 class="section-title"><?= $fr ? 'Des fournisseurs dans toutes les catégories' : 'Suppliers Across Every Category' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'OCSAPP s\'associe à des fournisseurs locaux au Québec dans toutes les catégories de produits - des petites entreprises familiales aux distributeurs régionaux établis.'
      : 'OCSAPP partners with local Quebec suppliers across all product categories - from small family operations to established regional distributors.' ?></p>

    <div class="category-chips">
      <span class="category-chip">🥗 <?= $fr ? 'Alimentation &amp; boissons' : 'Food &amp; Beverage' ?></span>
      <span class="category-chip">🧴 <?= $fr ? 'Santé &amp; beauté' : 'Health &amp; Beauty' ?></span>
      <span class="category-chip">🏠 <?= $fr ? 'Maison &amp; décoration' : 'Home &amp; Living' ?></span>
      <span class="category-chip">👕 <?= $fr ? 'Vêtements' : 'Apparel' ?></span>
      <span class="category-chip">🌱 <?= $fr ? 'Bio &amp; naturel' : 'Organic &amp; Natural' ?></span>
      <span class="category-chip">📦 <?= $fr ? 'Biens de consommation' : 'Consumer Goods' ?></span>
    </div>

    <div class="quote-card">
      <p class="quote-text">
        <?= $fr
          ? '« Depuis qu\'on est sur OCSAPP, on reçoit des bons de commande réguliers sans avoir à chercher des clients. On s\'occupe de nos produits - eux s\'occupent du reste. »'
          : '"Since joining OCSAPP, we receive consistent purchase orders without hunting for clients. We focus on our products - they handle the rest."' ?>
      </p>
      <div class="quote-author">
        <div class="quote-avatar">🏪</div>
        <div>
          <div class="quote-name"><?= $fr ? 'Fournisseur OCSAPP - Alimentation, Montréal' : 'OCSAPP Supplier - Food &amp; Beverage, Montréal' ?></div>
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
      <span class="eyebrow eyebrow-neon"><?= $fr ? 'Forfaits fournisseur' : 'Supplier Plans' ?></span>
    </div>
    <h2 class="section-title light"><?= $fr ? 'Choisissez le bon forfait pour votre entreprise' : 'Choose the Right Plan for Your Business' ?></h2>
    <p class="section-sub light"><?= $fr ? 'Démarrez gratuitement sur Essential. Passez à un forfait supérieur à tout moment pour débloquer plus d\'UGS, d\'outils et de fonctionnalités.' : 'Start free on Essential. Upgrade any time to unlock more SKUs, tools, and growth features.' ?></p>

    <div class="packages-grid">

      <div class="pkg-card pkg-ess">
        <div class="pkg-name">Essential</div>
        <div class="pkg-desc"><?= $fr ? 'Tout ce qu\'il faut pour démarrer' : 'Everything you need to get started' ?></div>
        <div class="pkg-rate">
          <span class="pkg-rate-num">12%</span>
          <span class="pkg-rate-lbl"><?= $fr ? 'taux de commission' : 'commission rate' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestion des BC, BL &amp; factures' : 'PO, SO &amp; Invoice Management' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Comptes clients &amp; paiements' : 'Account Receivables &amp; Payments' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Messagerie d\'assistance interne' : 'Internal Support Messaging' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestion des stocks &amp; produits' : 'Inventory &amp; Product Management' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Barre de progression des commandes' : 'Order Progress Bar' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Journal d\'activité &amp; courriels' : 'Activity &amp; Email Log' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Partenaire réseau ODA' : 'ODA Delivery Network Partner' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Publicité collaborative' : 'Collaborative Ad' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Support multi-emplacements' : 'Multi-location Support' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Double accès utilisateur / emplacement' : 'Dual User Access per Location' ?></li>
        </ul>
        <a href="<?= url('supplier/apply') ?>?package=Essential" class="pkg-cta outline"><?= $fr ? 'Commencer gratuitement' : 'Get Started Free' ?></a>
      </div>

      <div class="pkg-card pkg-exp">
        <div class="pkg-popular"><?= $fr ? 'Le plus populaire' : 'Most Popular' ?></div>
        <div class="pkg-name">Experience</div>
        <div class="pkg-desc"><?= $fr ? 'Optimisez votre catalogue et votre équipe' : 'Optimize your catalog and team' ?></div>
        <div class="pkg-rate">
          <span class="pkg-rate-num">10%</span>
          <span class="pkg-rate-lbl"><?= $fr ? 'taux de commission' : 'commission rate' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce qu\'Essential inclut' : 'Everything in Essential' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Jusqu\'à 10 000 UGS' : 'Up to 10,000 SKUs' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Rôles utilisateur &amp; limites d\'accès' : 'User Roles &amp; Access Limits' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Support prioritaire (Lun-Dim)' : 'Priority support (Mon-Sun)' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Import en masse de produits' : 'Bulk product import' ?></li>
        </ul>
        <a href="<?= url('supplier/apply') ?>?package=Experience" class="pkg-cta"><?= $fr ? 'Postuler' : 'Apply' ?></a>
      </div>

      <div class="pkg-card pkg-pre">
        <div class="pkg-name">Prestige</div>
        <div class="pkg-desc"><?= $fr ? 'Pour les fournisseurs à fort volume' : 'For high-volume suppliers' ?></div>
        <div class="pkg-rate">
          <span class="pkg-rate-num">8%</span>
          <span class="pkg-rate-lbl"><?= $fr ? 'taux de commission' : 'commission rate' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce qu\'Experience inclut' : 'Everything in Experience' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'UGS illimitées' : 'Unlimited SKUs' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Gestionnaire de compte dédié' : 'Dedicated account manager' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Mise en avant dans le catalogue' : 'Featured catalog placement' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Traitement prioritaire des commandes' : 'Priority order processing' ?></li>
        </ul>
        <a href="<?= url('supplier/apply') ?>?package=Prestige" class="pkg-cta"><?= $fr ? 'Postuler' : 'Apply' ?></a>
      </div>

      <div class="pkg-card pkg-ent">
        <div class="pkg-name"><?= $fr ? 'Entreprise' : 'Enterprise' ?></div>
        <div class="pkg-desc"><?= $fr ? 'Solutions sur mesure à grande échelle' : 'Custom solutions, large scale' ?></div>
        <div class="pkg-rate">
          <span class="pkg-rate-num">6%</span>
          <span class="pkg-rate-lbl"><?= $fr ? 'taux de commission' : 'commission rate' ?></span>
        </div>
        <hr class="pkg-divider">
        <ul class="pkg-features">
          <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce que Prestige inclut' : 'Everything in Prestige' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Suivi de livraison en direct' : 'Live Delivery Tracking' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'CRM &amp; génération de prospects' : 'CRM &amp; Lead Generation' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Publicité &amp; marketing' : 'Advertising &amp; Marketing' ?></li>
          <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Intégration sur mesure &amp; SLA dédié' : 'Custom onboarding &amp; dedicated SLA' ?></li>
        </ul>
        <a href="mailto:suppliers@ocsapp.ca?subject=Enterprise%20Plan%20Inquiry" class="pkg-cta"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
      </div>

    </div>

    <div class="plan-note">
      <?= $fr
        ? '<strong>Remarque :</strong> Tous les nouveaux comptes démarrent sur <strong>Essential</strong> sans frais. Pour changer de forfait, contactez <strong>suppliers@ocsapp.ca</strong> ou votre gestionnaire de compte. Les changements prennent effet dans un délai d\'un jour ouvrable.'
        : '<strong>Note:</strong> All new accounts start on <strong>Essential</strong> at no cost. To upgrade, contact <strong>suppliers@ocsapp.ca</strong> or your account manager. Changes take effect within one business day.' ?>
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
    <p class="section-sub"><?= $fr ? 'Tout ce que vous devez savoir avant de postuler.' : 'Everything you need to know before applying.' ?></p>

    <div class="faq">
      <?php
      $faqs = $fr ? [
        ['Puis-je utiliser un courriel différent de celui avec lequel j\'ai été invité ?',
         'Oui. Si vous avez reçu une invitation directe, vous pouvez vous inscrire avec n\'importe quel courriel. L\'adresse enregistrée devient votre identifiant de connexion et l\'endroit où toutes les notifications du portail sont envoyées.'],
        ['Combien de temps dure le processus d\'approbation ?',
         'La plupart des candidatures sont examinées dans un délai de 1 à 3 jours ouvrables. Vous serez informé par courriel. Si vous n\'avez pas eu de nouvelles après 3 jours ouvrables, contactez suppliers@ocsapp.ca avec votre numéro de référence.'],
        ['Puis-je me connecter au portail avant d\'être approuvé ?',
         'Oui - immédiatement après avoir soumis votre candidature. L\'accès est limité pendant l\'examen : vous pouvez explorer le portail, mais vous ne pouvez pas lister des produits ni recevoir des bons de commande tant que votre compte n\'est pas pleinement activé.'],
        ['Qu\'est-ce qu\'un code fournisseur et pourquoi en ai-je besoin ?',
         'Votre code fournisseur (format : SUP-XXXXXXXX) est votre identifiant unique sur le réseau OCSAPP. Il apparaît dans votre courriel de confirmation et sur votre tableau de bord. Incluez-le toujours lorsque vous contactez notre équipe d\'assistance.'],
        ['Comment et quand suis-je payé ?',
         'Les modalités de paiement sont convenues lors de l\'intégration. OCSAPP traite les paiements selon des termes nets (généralement net-30) après confirmation de l\'exécution du bon de commande. Les factures et le statut des paiements sont visibles dans le portail sous Comptes clients.'],
        ['Puis-je changer de forfait à tout moment ?',
         'Oui. Contactez suppliers@ocsapp.ca ou votre gestionnaire de compte. Les changements prennent effet dans un délai d\'un jour ouvrable. Tous les forfaits sont mensuels, sans engagement à long terme.'],
      ] : [
        ['Can I use a different email than the one I was invited with?',
         'Yes. If you received a direct invite, you can register with any email. Your registered email becomes your login and where all portal notifications are sent.'],
        ['How long does the approval process take?',
         'Most applications are reviewed within 1-3 business days. You\'ll be notified by email. If you haven\'t heard back after 3 business days, contact suppliers@ocsapp.ca with your reference number.'],
        ['Can I log in to the portal before I\'m approved?',
         'Yes - immediately after submitting. Access is limited during review: you can explore the portal but cannot list products or receive purchase orders until your account is fully activated.'],
        ['What is a Supplier Code and why do I need it?',
         'Your Supplier Code (format: SUP-XXXXXXXX) is your unique identifier on the OCSAPP network. It appears in your confirmation email and on your dashboard. Always include it when contacting our support team.'],
        ['How and when do I get paid?',
         'Payment terms are agreed upon during onboarding. OCSAPP processes payments on net terms (typically net-30) after PO fulfillment is confirmed. Invoices and payment status are visible in the portal under Account Receivables.'],
        ['Can I upgrade my plan at any time?',
         'Yes. Contact suppliers@ocsapp.ca or your account manager. Changes take effect within one business day. All plans are month-to-month with no long-term commitment.'],
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
        ? 'Notre équipe de succès fournisseur est disponible pour vous aider avec votre candidature, la configuration de votre compte et tout ce qui se passe après. Incluez toujours votre <strong style="color:#86efac;">code fournisseur ou numéro de référence</strong> pour que nous trouvions votre compte rapidement.'
        : 'Our supplier success team is available to help with your application, account setup, and everything that comes after. Always include your <strong style="color:#86efac;">Supplier Code or reference number</strong> so we can find your account quickly.' ?></p>
      <div class="contact-meta">
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Support fournisseur' : 'Supplier Support' ?></div>
          <div class="c-value">suppliers@ocsapp.ca</div>
          <div class="c-sub"><?= $fr ? 'Toutes les demandes fournisseur' : 'All supplier inquiries' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Téléphone' : 'Phone' ?></div>
          <div class="c-value">514-746-3789</div>
          <div class="c-sub"><?= $fr ? 'Lun-Dim · 8h - 20h' : 'Mon-Sun · 8 am - 8 pm' ?></div>
        </div>
        <div class="contact-item">
          <div class="c-label"><?= $fr ? 'Portail fournisseur' : 'Supplier Portal' ?></div>
          <div class="c-value">ocsapp.ca/supplier/login</div>
          <div class="c-sub"><?= $fr ? 'Connexion &amp; tableau de bord' : 'Login &amp; dashboard' ?></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════ FINAL CTA ═══════════════ -->
<section class="cta-section">
  <div class="container">
    <h2><?= $fr ? 'Votre prochain bon de commande vous attend.' : 'Your Next Purchase Order Is Waiting.' ?></h2>
    <p><?= $fr ? 'Postulez en 15 minutes. Approuvé en 1 à 3 jours. Sans frais pour commencer.' : 'Apply in 15 minutes. Approved in 1-3 days. Free to start.' ?></p>
    <div>
      <a href="<?= url('supplier/apply') ?>" class="btn-white-lg">
        <i class="fas fa-rocket"></i> <?= $fr ? 'Commencer votre candidature' : 'Start Your Application' ?>
      </a>
      <a href="mailto:suppliers@ocsapp.ca" class="btn-ghost-lg">
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
