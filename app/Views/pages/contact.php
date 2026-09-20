<?php
/**
 * OCSAPP Contact Page
 * Bilingual: EN / FR
 * Rebuilt 2026-09-08 from the "Contact-Page" ecosystem-standard package: expanded
 * request-routing form (phone, contact method, role/Central, organization, priority,
 * reference, privacy consent), ecosystem-standard visual re-skin, and the same
 * eco-header/mc-footer used across login/legal/centrals.
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
  <title><?= $fr ? 'Contactez OCSAPP - Support de l\'écosystème' : 'Contact OCSAPP - Ecosystem Support' ?></title>
  <meta name="description" content="<?= $fr
    ? 'Contactez l\'équipe OCSAPP pour le support, les commandes, les Centrales, les partenariats et les questions techniques.'
    : 'Contact the OCSAPP team for support, orders, Centrals, partnerships and technical questions.' ?>">
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
      padding: 96px 24px 88px; text-align: center; position: relative; overflow: hidden;
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
    .hero-inner { position: relative; z-index: 1; max-width: 700px; margin: 0 auto; }
    .eyebrow {
      display: inline-block; font-size: 11px; font-weight: 700;
      letter-spacing: 1.8px; text-transform: uppercase;
      padding: 5px 16px; border-radius: 20px; margin-bottom: 20px;
      background: rgba(0,255,136,.12); border: 1px solid rgba(0,255,136,.25); color: var(--neon);
    }
    .hero h1 {
      font-size: clamp(36px, 5.5vw, 58px); font-weight: 900; color: white;
      line-height: 1.05; letter-spacing: -1.5px; margin-bottom: 20px;
    }
    .hero h1 span { color: var(--neon); }
    .hero p {
      font-size: 18px; color: rgba(255,255,255,.72); max-width: 560px;
      margin: 0 auto; line-height: 1.7;
    }

    /* ── INFO CARDS ── */
    .info-section { background: white; padding: 80px 24px; }
    .info-grid {
      display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 28px; margin-bottom: 72px;
    }
    .info-card {
      background: var(--light); border: 1px solid var(--border);
      border-radius: 16px; padding: 32px 28px; text-align: center;
      transition: box-shadow .2s, transform .2s;
    }
    .info-card:hover { box-shadow: 0 8px 32px rgba(0,178,7,.12); transform: translateY(-3px); }
    .info-card .icon {
      width: 56px; height: 56px; border-radius: 14px;
      background: rgba(0,178,7,.1); color: var(--green);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px; margin: 0 auto 18px;
    }
    .info-card h3 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 8px; }
    .info-card p { font-size: 14px; color: var(--muted); line-height: 1.6; }
    .info-card a { color: var(--green); font-weight: 600; }
    .info-card a:hover { text-decoration: underline; }

    /* ── FORM LAYOUT ── */
    .form-layout {
      display: grid; grid-template-columns: 1fr 1fr; gap: 64px;
      align-items: start;
    }
    @media (max-width: 860px) { .form-layout { grid-template-columns: 1fr; gap: 48px; } }

    .form-aside h2 {
      font-size: clamp(26px, 3.5vw, 34px); font-weight: 800; color: #111827;
      line-height: 1.2; margin-bottom: 16px;
    }
    .form-aside h2 span { color: var(--green); }
    .form-aside p { font-size: 16px; color: var(--muted); line-height: 1.75; margin-bottom: 28px; }

    .response-times { list-style: none; }
    .response-times li {
      display: flex; align-items: center; gap: 12px;
      font-size: 14px; color: #374151; padding: 10px 0;
      border-bottom: 1px solid var(--border);
    }
    .response-times li:last-child { border-bottom: none; }
    .response-times li i { color: var(--green); width: 18px; text-align: center; }

    /* ── CONTACT FORM ── */
    .contact-form-wrap {
      background: white; border: 1px solid var(--border);
      border-radius: 20px; padding: 40px 36px;
      box-shadow: 0 4px 24px rgba(0,0,0,.07);
    }
    .form-group { margin-bottom: 20px; }
    .form-group label {
      display: block; font-size: 13px; font-weight: 600; color: #374151;
      margin-bottom: 6px; letter-spacing: .2px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%; padding: 12px 16px; border: 1.5px solid var(--border);
      border-radius: 10px; font-size: 15px; color: #111827;
      background: white; transition: border-color .2s, box-shadow .2s;
      font-family: inherit; outline: none;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: var(--green); box-shadow: 0 0 0 3px rgba(0,178,7,.1);
    }
    .form-group textarea { resize: vertical; min-height: 130px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    @media (max-width: 480px) { .form-row { grid-template-columns: 1fr; } }

    .btn-submit {
      width: 100%; padding: 14px 24px;
      background: var(--green); color: white;
      border: none; border-radius: 10px; font-size: 16px; font-weight: 700;
      cursor: pointer; transition: background .2s, transform .15s;
      display: flex; align-items: center; justify-content: center; gap: 10px;
    }
    .btn-submit:hover { background: var(--green-dark); transform: translateY(-1px); }
    .btn-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; }

    .form-notice {
      margin-top: 16px; padding: 14px 18px; border-radius: 10px;
      font-size: 14px; display: none;
    }
    .form-notice.success { background: rgba(0,178,7,.08); color: #065f46; border: 1px solid rgba(0,178,7,.2); display: block; }
    .form-notice.error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; display: block; }

    /* ── FAQ STRIP ── */
    .faq-section { background: var(--light); padding: 80px 24px; }
    .section-title {
      text-align: center; font-size: clamp(26px,4vw,36px);
      font-weight: 800; color: #111827; margin-bottom: 12px;
    }
    .section-sub {
      text-align: center; font-size: 16px; color: var(--muted);
      max-width: 560px; margin: 0 auto 52px; line-height: 1.7;
    }
    .faq-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; }
    .faq-item {
      background: white; border: 1px solid var(--border);
      border-radius: 14px; padding: 28px 24px;
    }
    .faq-item h4 { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 10px; }
    .faq-item p { font-size: 14px; color: var(--muted); line-height: 1.7; }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(140deg, #060a1a 0%, #0a1a0c 55%, #0c2e10 100%);
      padding: 80px 24px; text-align: center; position: relative; overflow: hidden;
    }
    .cta-section::before {
      content: ''; position: absolute; border-radius: 50%; pointer-events: none;
      width: 500px; height: 500px; top: -120px; right: -80px;
      background: radial-gradient(circle, rgba(0,178,7,.15) 0%, transparent 65%);
    }
    .cta-inner { position: relative; z-index: 1; }
    .cta-section h2 { font-size: clamp(28px,4vw,42px); font-weight: 900; color: white; margin-bottom: 14px; }
    .cta-section p { font-size: 17px; color: rgba(255,255,255,.7); max-width: 520px; margin: 0 auto 36px; }
    .btn-cta {
      display: inline-flex; align-items: center; gap: 10px;
      background: var(--green); color: white;
      padding: 15px 36px; border-radius: 10px; font-size: 16px; font-weight: 700;
      transition: background .2s, transform .15s;
    }
    .btn-cta:hover { background: var(--green-dark); transform: translateY(-2px); }
  </style>
  <style>
    /* ── ECOSYSTEM-STANDARD RE-SKIN ── from the 2026-09-08 Contact-Page package */
    :root{
      --green:#00B207!important;
      --green-rgb:0,178,7;
      --forest:#0D3F10;
      --forest-2:#0A3310;
      --text:#17181A!important;
      --muted:#66706A!important;
      --border:#E5E7E4!important;
      --light:#F7F8F7!important;
    }
    body{font-family:'Inter',sans-serif!important;color:var(--text)!important}
    h1,h2,h3,h4,.section-title,.btn-submit,.btn-cta,.info-card h3{font-family:'Poppins',sans-serif}
    .hero{
      background:
        radial-gradient(circle at 84% 0%,rgba(255,255,255,.09),transparent 31%),
        linear-gradient(145deg,var(--forest) 0%,var(--forest-2) 100%)!important;
      padding:86px 24px 78px!important;
    }
    .hero::before{
      width:620px!important;height:620px!important;
      background:radial-gradient(circle,rgba(var(--green-rgb),.16),transparent 68%)!important;
    }
    .hero::after{display:none!important}
    .eyebrow{
      background:rgba(255,255,255,.08)!important;
      border:1px solid rgba(255,255,255,.12)!important;
      color:#A6EAAA!important;
      font-family:'Poppins',sans-serif!important;
    }
    .hero h1{font-weight:700!important;letter-spacing:-.035em!important}
    .hero h1 span{color:#5BE864!important}
    .hero p{color:#C3D8C5!important}

    .info-section{background:#fff!important;padding:70px 24px 82px!important}
    .info-grid{gap:18px!important;margin-bottom:66px!important}
    .info-card{
      background:#fff!important;
      border:1px solid var(--border)!important;
      border-radius:18px!important;
      box-shadow:0 8px 28px rgba(16,24,18,.06)!important;
      padding:28px 24px!important;
    }
    .info-card:hover{box-shadow:0 18px 44px rgba(16,24,18,.10)!important}
    .info-card .icon{
      width:58px!important;height:58px!important;border-radius:16px!important;
      background:linear-gradient(145deg,#F8FFF8,#D8F1DA 52%,#ADDCAF)!important;
      border:1px solid rgba(var(--green-rgb),.18)!important;
      color:#08750E!important;
      box-shadow:0 8px 0 #88C78D,0 14px 22px rgba(8,82,15,.12),inset 1px 1px 0 #fff!important;
      transform:translateY(-3px);
    }
    .info-card h3{color:var(--text)!important}
    .info-card p{color:var(--muted)!important}

    .form-layout{grid-template-columns:.82fr 1.18fr!important;gap:54px!important}
    .form-aside h2{font-family:'Poppins',sans-serif!important;color:var(--text)!important;letter-spacing:-.03em}
    .form-aside p{color:var(--muted)!important}
    .response-times li{color:#485149!important;border-bottom:1px solid var(--border)!important}
    .response-times li i{color:var(--green)!important}

    .contact-form-wrap{
      border:1px solid var(--border)!important;
      border-radius:22px!important;
      padding:38px 34px!important;
      box-shadow:0 18px 54px rgba(16,24,18,.10)!important;
    }
    .form-group label{color:#2A302C!important}
    .form-group input,.form-group select,.form-group textarea{
      border:1px solid var(--border)!important;
      border-radius:11px!important;
      background:#FBFCFB!important;
      font-family:'Inter',sans-serif!important;
    }
    .form-group input:focus,.form-group select:focus,.form-group textarea:focus{
      border-color:rgba(var(--green-rgb),.58)!important;
      box-shadow:0 0 0 3px rgba(var(--green-rgb),.08)!important;
      background:#fff!important;
    }
    .optional{font-weight:400;color:#8B948D;font-size:11px}
    .field-help{font-size:10.5px;color:#8B948D;margin-top:6px;line-height:1.5}
    .privacy-consent{
      display:flex;align-items:flex-start;gap:10px;
      margin:4px 0 20px;
      padding:14px;
      border:1px solid var(--border);
      border-radius:11px;
      background:#F8FAF8;
      color:#58615A;
      font-size:11px;
      line-height:1.55;
    }
    .privacy-consent input{margin-top:3px;accent-color:var(--green);flex:0 0 auto}
    .privacy-consent a{color:var(--green);font-weight:600}
    .btn-submit{
      background:var(--green)!important;
      border-radius:11px!important;
      font-size:14px!important;
      box-shadow:0 12px 28px rgba(var(--green-rgb),.20)!important;
    }
    .btn-submit:hover{background:#009B06!important}

    .faq-section{background:var(--light)!important;padding:78px 24px!important}
    .section-title{color:var(--text)!important;font-weight:700!important;letter-spacing:-.03em}
    .faq-item{
      border:1px solid var(--border)!important;
      border-radius:16px!important;
      box-shadow:0 8px 26px rgba(16,24,18,.045);
    }
    .faq-item h4{font-family:'Poppins',sans-serif!important;color:var(--text)!important}
    .faq-item p{color:var(--muted)!important}

    .cta-section{
      background:
        radial-gradient(circle at 50% 0%,rgba(var(--green-rgb),.13),transparent 38%),
        linear-gradient(145deg,var(--forest),var(--forest-2))!important;
    }
    .cta-section::before{display:none!important}
    .cta-section h2{font-family:'Poppins',sans-serif!important;font-weight:700!important}
    .btn-cta{background:var(--green)!important;border-radius:11px!important;font-family:'Poppins',sans-serif!important}

    @media(max-width:860px){
      .form-layout{grid-template-columns:1fr!important;gap:42px!important}
    }
    @media(max-width:560px){
      .contact-form-wrap{padding:28px 20px!important}
    }
  </style>
</head>
<body>

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

<!-- HERO -->
<section class="hero">
  <div class="hero-inner">
    <span class="eyebrow"><?= $fr ? 'SUPPORT · CONTACT · ÉCOSYSTÈME' : 'SUPPORT · CONTACT · ECOSYSTEM' ?></span>
    <h1><?= $fr ? 'Comment pouvons-nous <span>vous aider?</span>' : 'How can we <span>help you?</span>' ?></h1>
    <p><?= $fr
      ? 'Une seule porte d\'entrée pour vos questions sur Marché Central, les Centrales, les commandes, la livraison, les partenariats et le support technique.'
      : 'One point of contact for questions about Marketplace Central, the Centrals, orders, delivery, partnerships and technical support.' ?></p>
  </div>
</section>

<!-- INFO CARDS + FORM -->
<section class="info-section">
  <div class="container">

    <!-- Contact Info Cards -->
    <div class="info-grid">
      <div class="info-card">
        <div class="icon"><i class="fas fa-envelope"></i></div>
        <h3><?= $fr ? 'Courriel' : 'Email' ?></h3>
        <p><a href="mailto:info@ocsapp.ca">info@ocsapp.ca</a></p>
        <p style="margin-top:6px;"><?= $fr ? 'Point de contact général OCSAPP' : 'General OCSAPP contact point' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-clock"></i></div>
        <h3><?= $fr ? 'Délai de réponse' : 'Response time' ?></h3>
        <p><?= $fr ? 'Selon le type de demande' : 'Based on the type of request' ?></p>
        <p><?= $fr ? 'Les demandes urgentes sont priorisées' : 'Urgent requests are prioritized' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-map-marker-alt"></i></div>
        <h3><?= $fr ? 'Écosystème desservi' : 'Ecosystem support' ?></h3>
        <p><?= $fr ? 'Marché Central · Centrales · Livraison · Partenariats' : 'Marketplace Central · Centrals · Delivery · Partnerships' ?></p>
      </div>
      <div class="info-card">
        <div class="icon"><i class="fas fa-comments"></i></div>
        <h3><?= $fr ? 'Service bilingue' : 'Bilingual service' ?></h3>
        <p><?= $fr ? 'Nous répondons en français et en anglais.' : 'We respond in English and French.' ?></p>
      </div>
    </div>

    <!-- Form + Aside -->
    <div class="form-layout">
      <!-- Aside -->
      <div class="form-aside">
        <h2><?= $fr ? 'Envoyez-nous <span>votre demande</span>' : 'Send us <span>your request</span>' ?></h2>
        <p><?= $fr
          ? 'Donnez-nous suffisamment de contexte pour que votre demande soit acheminée au bon Central ou à la bonne équipe dès le premier contact.'
          : 'Give us enough context so your request can be routed to the right Central or team from the first contact.' ?></p>

        <ul class="response-times">
          <li><i class="fas fa-circle-info"></i> <?= $fr ? 'Questions générales : réponse selon disponibilité' : 'General questions: response based on availability' ?></li>
          <li><i class="fas fa-store"></i> <?= $fr ? 'Vendeur / fournisseur : indiquez votre entreprise' : 'Seller / supplier: include your business' ?></li>
          <li><i class="fas fa-bag-shopping"></i> <?= $fr ? 'Commande : ajoutez votre numéro de référence' : 'Order: include your reference number' ?></li>
          <li><i class="fas fa-truck"></i> <?= $fr ? 'Livraison : précisez la commande et la situation' : 'Delivery: specify the order and situation' ?></li>
          <li><i class="fas fa-screwdriver-wrench"></i> <?= $fr ? 'Technique : indiquez l\'appareil, le navigateur et le problème' : 'Technical: include the device, browser and issue' ?></li>
          <li><i class="fas fa-handshake"></i> <?= $fr ? 'Partenariat / entreprise : précisez votre organisation et votre objectif' : 'Partnership / business: specify your organization and objective' ?></li>
        </ul>
      </div>

      <!-- Form -->
      <div class="contact-form-wrap">
        <form id="contactForm" novalidate>
          <input type="hidden" name="<?= htmlspecialchars(env('CSRF_TOKEN_NAME', '_csrf_token')) ?>" value="<?= htmlspecialchars(csrfToken()) ?>">

          <div class="form-row">
            <div class="form-group">
              <label for="name"><?= $fr ? 'Nom complet *' : 'Full name *' ?></label>
              <input type="text" id="name" name="name" autocomplete="name" required
                     placeholder="<?= $fr ? 'Votre nom complet' : 'Your full name' ?>">
            </div>
            <div class="form-group">
              <label for="email"><?= $fr ? 'Courriel *' : 'Email *' ?></label>
              <input type="email" id="email" name="email" autocomplete="email" required
                     placeholder="<?= $fr ? 'vous@exemple.ca' : 'you@example.ca' ?>">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="phone"><?= $fr ? 'Téléphone' : 'Phone' ?> <span class="optional"><?= $fr ? '(optionnel)' : '(optional)' ?></span></label>
              <input type="tel" id="phone" name="phone" autocomplete="tel" placeholder="514 000-0000">
            </div>
            <div class="form-group">
              <label for="contact_method"><?= $fr ? 'Méthode de contact préférée' : 'Preferred contact method' ?></label>
              <select id="contact_method" name="contact_method">
                <option value="Email"><?= $fr ? 'Courriel' : 'Email' ?></option>
                <option value="Phone"><?= $fr ? 'Téléphone' : 'Phone' ?></option>
                <option value="Either"><?= $fr ? 'Courriel ou téléphone' : 'Email or phone' ?></option>
              </select>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="role"><?= $fr ? 'Votre lien avec OCSAPP *' : 'Your relationship with OCSAPP *' ?></label>
              <select id="role" name="role" required>
                <?php if ($fr): ?>
                  <option value="">Sélectionnez votre profil</option>
                  <option value="Buyer Central">Acheteur Central</option>
                  <option value="Seller Central">Vendeur Central</option>
                  <option value="Supplier Central">Fournisseur Central</option>
                  <option value="Business Central">Entreprise Centrale</option>
                  <option value="Driver Central ODA">Livreur Central · ODA</option>
                  <option value="Marketplace Central">Marché Central / visiteur</option>
                  <option value="Partner">Partenaire / média / institution</option>
                  <option value="Other">Autre</option>
                <?php else: ?>
                  <option value="">Select your profile</option>
                  <option value="Buyer Central">Buyer Central</option>
                  <option value="Seller Central">Seller Central</option>
                  <option value="Supplier Central">Supplier Central</option>
                  <option value="Business Central">Business Central</option>
                  <option value="Driver Central ODA">Driver Central · ODA</option>
                  <option value="Marketplace Central">Marketplace Central / visitor</option>
                  <option value="Partner">Partner / media / institution</option>
                  <option value="Other">Other</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="organization"><?= $fr ? 'Entreprise ou organisation' : 'Business or organization' ?> <span class="optional"><?= $fr ? '(si applicable)' : '(if applicable)' ?></span></label>
              <input type="text" id="organization" name="organization" autocomplete="organization"
                     placeholder="<?= $fr ? 'Nom de l\'entreprise ou organisation' : 'Business or organization name' ?>">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label for="subject"><?= $fr ? 'Type de demande *' : 'Request type *' ?></label>
              <select id="subject" name="subject" required>
                <?php if ($fr): ?>
                  <option value="">Sélectionnez un sujet</option>
                  <option value="General Inquiry">Question générale</option>
                  <option value="Order Issue">Commande / achat</option>
                  <option value="Buyer Support">Support Acheteur Central</option>
                  <option value="Seller Support">Support Vendeur Central</option>
                  <option value="Supplier Support">Support Fournisseur Central</option>
                  <option value="Business Support">Support Entreprise Centrale</option>
                  <option value="Driver Support">Support Livreur Central · ODA</option>
                  <option value="Partnership">Partenariat / distribution</option>
                  <option value="Technical Issue">Problème technique</option>
                  <option value="Billing">Facturation / paiement</option>
                  <option value="Privacy">Confidentialité / données personnelles</option>
                  <option value="Accessibility">Accessibilité</option>
                  <option value="Other">Autre</option>
                <?php else: ?>
                  <option value="">Select a subject</option>
                  <option value="General Inquiry">General inquiry</option>
                  <option value="Order Issue">Order / purchase</option>
                  <option value="Buyer Support">Buyer Central support</option>
                  <option value="Seller Support">Seller Central support</option>
                  <option value="Supplier Support">Supplier Central support</option>
                  <option value="Business Support">Business Central support</option>
                  <option value="Driver Support">Driver Central · ODA support</option>
                  <option value="Partnership">Partnership / distribution</option>
                  <option value="Technical Issue">Technical issue</option>
                  <option value="Billing">Billing / payment</option>
                  <option value="Privacy">Privacy / personal data</option>
                  <option value="Accessibility">Accessibility</option>
                  <option value="Other">Other</option>
                <?php endif; ?>
              </select>
            </div>
            <div class="form-group">
              <label for="priority"><?= $fr ? 'Priorité' : 'Priority' ?></label>
              <select id="priority" name="priority">
                <option value="Normal"><?= $fr ? 'Normale' : 'Normal' ?></option>
                <option value="Time-sensitive"><?= $fr ? 'Sensible au temps' : 'Time-sensitive' ?></option>
                <option value="Urgent"><?= $fr ? 'Urgente - service ou commande en cours' : 'Urgent - active service or order' ?></option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="reference"><?= $fr ? 'N° de commande, compte ou référence' : 'Order, account or reference number' ?> <span class="optional"><?= $fr ? '(si applicable)' : '(if applicable)' ?></span></label>
            <input type="text" id="reference" name="reference"
                   placeholder="<?= $fr ? 'Ex. commande #12345, boutique, dossier ou référence' : 'E.g. order #12345, shop, file or reference' ?>">
            <div class="field-help"><?= $fr
              ? 'N\'inscrivez jamais de mot de passe, numéro de carte ou autre information financière sensible.'
              : 'Never enter a password, card number or other sensitive financial information.' ?></div>
          </div>

          <div class="form-group">
            <label for="message"><?= $fr ? 'Message *' : 'Message *' ?></label>
            <textarea id="message" name="message" required
                      placeholder="<?= $fr
                        ? 'Décrivez votre demande, ce que vous essayiez de faire, le résultat obtenu et toute information utile...'
                        : 'Describe your request, what you were trying to do, the result you got, and any useful details...' ?>"></textarea>
          </div>

          <label class="privacy-consent">
            <input type="checkbox" id="privacyConsent" name="privacy_consent" value="accepted" required>
            <span><?= $fr
              ? 'J\'accepte qu\'OCSAPP utilise les renseignements fournis pour traiter et répondre à ma demande, conformément à la <a href="' . url('privacy') . '" target="_blank" rel="noopener">Politique de confidentialité</a>. *'
              : 'I agree that OCSAPP may use the information provided to process and respond to my request, in accordance with the <a href="' . url('privacy') . '" target="_blank" rel="noopener">Privacy Policy</a>. *' ?></span>
          </label>

          <button type="submit" class="btn-submit" id="submitBtn">
            <i class="fas fa-paper-plane"></i>
            <?= $fr ? 'Envoyer la demande' : 'Send request' ?>
          </button>

          <div class="form-notice" id="formNotice"></div>
        </form>
      </div>
    </div>

  </div>
</section>

<!-- FAQ STRIP -->
<section class="faq-section">
  <div class="container">
    <h2 class="section-title"><?= $fr ? 'Avant de nous écrire' : 'Before contacting us' ?></h2>
    <p class="section-sub"><?= $fr
      ? 'Quelques repères pour accélérer le traitement de votre demande.'
      : 'A few guidelines to help us process your request faster.' ?></p>

    <div class="faq-grid">
      <div class="faq-item">
        <h4><?= $fr ? 'Pour une commande ou une livraison' : 'For an order or delivery' ?></h4>
        <p><?= $fr
          ? 'Ajoutez votre numéro de commande ou de référence et décrivez la situation. N\'envoyez jamais de données de carte ou de mot de passe.'
          : 'Include your order or reference number and describe the situation. Never send card details or passwords.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Pour un Central' : 'For a Central' ?></h4>
        <p><?= $fr
          ? 'Sélectionnez Acheteur, Vendeur, Fournisseur, Entreprise ou Livreur Central · ODA afin que votre demande soit acheminée correctement.'
          : 'Select Buyer, Seller, Supplier, Business or Driver Central · ODA so your request can be routed correctly.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Pour un problème technique' : 'For a technical issue' ?></h4>
        <p><?= $fr
          ? 'Indiquez la page concernée, votre appareil et navigateur, ce que vous avez tenté de faire et le message d\'erreur observé.'
          : 'Include the affected page, your device and browser, what you tried to do and any error message you received.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Pour une demande de partenariat' : 'For a partnership request' ?></h4>
        <p><?= $fr
          ? 'Indiquez votre organisation, votre rôle, la nature du partenariat proposé et la façon dont vous souhaitez collaborer avec OCSAPP.'
          : 'Include your organization, role, the nature of the proposed partnership and how you would like to work with OCSAPP.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Pour la confidentialité ou les données' : 'For privacy or data requests' ?></h4>
        <p><?= $fr
          ? 'Sélectionnez « Confidentialité / données personnelles » dans le formulaire afin que la demande soit identifiée clairement.'
          : 'Select "Privacy / personal data" in the form so your request is clearly identified.' ?></p>
      </div>
      <div class="faq-item">
        <h4><?= $fr ? 'Pour l\'accessibilité' : 'For accessibility' ?></h4>
        <p><?= $fr
          ? 'Décrivez l\'obstacle rencontré, la page ou fonctionnalité concernée et, si possible, la technologie d\'assistance utilisée.'
          : 'Describe the barrier encountered, the affected page or feature and, if possible, the assistive technology used.' ?></p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <div class="container">
    <div class="cta-inner">
      <h2><?= $fr ? 'Vous cherchez plutôt votre Central?' : 'Looking for your Central?' ?></h2>
      <p><?= $fr
        ? 'Accédez à l\'écosystème OCSAPP et choisissez le Central qui correspond à votre rôle.'
        : 'Access the OCSAPP ecosystem and choose the Central that matches your role.' ?></p>
      <a href="<?= url('') ?>" class="btn-cta">
        <i class="fas fa-arrow-right"></i>
        <?= $fr ? "Explorer l'écosystème" : 'Explore the ecosystem' ?>
      </a>
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

<script>
(function () {
  const form    = document.getElementById('contactForm');
  const btn     = document.getElementById('submitBtn');
  const notice  = document.getElementById('formNotice');
  const lang    = <?= json_encode($currentLang) ?>;
  const isFr    = lang === 'fr';

  const sendLabel = isFr ? 'Envoyer la demande' : 'Send request';

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    notice.className = 'form-notice';
    notice.textContent = '';

    const name    = form.name.value.trim();
    const email   = form.email.value.trim();
    const role    = form.role.value.trim();
    const subject = form.subject.value.trim();
    const message = form.message.value.trim();
    const privacyConsent = form.privacy_consent.checked;

    if (!name || !email || !role || !subject || !message || !privacyConsent) {
      notice.className = 'form-notice error';
      notice.textContent = isFr
        ? 'Veuillez remplir tous les champs obligatoires et accepter la Politique de confidentialité.'
        : 'Please fill in all required fields and accept the Privacy Policy.';
      return;
    }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + (isFr ? 'Envoi en cours...' : 'Sending...');

    try {
      const data = new FormData(form);
      const res  = await fetch('/contact/submit', { method: 'POST', body: data });
      const json = await res.json();

      if (json.success) {
        notice.className = 'form-notice success';
        notice.textContent = json.message || (isFr
          ? 'Merci! Votre demande a été envoyée. Notre équipe vous répondra dans les meilleurs délais.'
          : 'Thank you! Your request has been sent. We\'ll be in touch soon.');
        form.reset();
      } else {
        notice.className = 'form-notice error';
        notice.textContent = json.message || (isFr ? 'Une erreur est survenue.' : 'An error occurred.');
      }
    } catch (err) {
      notice.className = 'form-notice error';
      notice.textContent = isFr ? 'Erreur réseau. Veuillez réessayer.' : 'Network error. Please try again.';
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-paper-plane"></i> ' + sendLabel;
    }
  });
})();
</script>
</body>
</html>
