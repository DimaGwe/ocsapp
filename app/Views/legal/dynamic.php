<?php
/**
 * Dynamic Legal Page View
 * Displays legal content from database
 * Header/footer match the ecosystem landing page (public/landing.php) via the
 * shared components/eco-header.css + .mc-footer markup, same port used by
 * app/Views/auth/login.php (2026-09-08).
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
    <?= csrfMeta() ?>
    <title><?= htmlspecialchars($page['title'] ?? 'Legal Page') ?> - OCSAPP</title>
    <?php if (!empty($page['meta_description'])): ?>
        <meta name="description" content="<?= htmlspecialchars($page['meta_description']) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/eco-header.css') ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    :root {
        --primary: #00b207;
        --dark: #1a1a1a;
        --gray-600: #4b5563;
        --gray-100: #f3f4f6;
        --border: #e5e7eb;
    }

    .legal-page {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .legal-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 48px 64px;
    }

    .legal-header {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border);
    }

    .legal-header h1 {
        font-size: 36px;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 12px;
    }

    .legal-meta {
        display: flex;
        justify-content: center;
        gap: 24px;
        font-size: 14px;
        color: var(--gray-600);
        flex-wrap: wrap;
    }

    .legal-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .legal-meta-item i {
        color: var(--primary);
    }

    .legal-content {
        font-size: 15px;
        line-height: 1.8;
        color: #374151;
    }

    .legal-content h1,
    .legal-content h2,
    .legal-content h3,
    .legal-content h4,
    .legal-content h5,
    .legal-content h6 {
        margin-top: 32px;
        margin-bottom: 16px;
        font-weight: 600;
        color: var(--dark);
    }

    .legal-content h2 {
        font-size: 24px;
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border);
    }

    .legal-content h3 {
        font-size: 20px;
    }

    .legal-content h4 {
        font-size: 18px;
    }

    .legal-content p {
        margin-bottom: 16px;
    }

    .legal-content ul,
    .legal-content ol {
        margin-bottom: 16px;
        padding-left: 28px;
    }

    .legal-content li {
        margin-bottom: 8px;
    }

    .legal-content a {
        color: var(--primary);
        text-decoration: none;
        font-weight: 500;
    }

    .legal-content a:hover {
        text-decoration: underline;
    }

    .legal-content strong {
        font-weight: 600;
        color: var(--dark);
    }

    .legal-content em {
        font-style: italic;
    }

    .legal-content blockquote {
        margin: 24px 0;
        padding: 16px 24px;
        background: var(--gray-100);
        border-left: 4px solid var(--primary);
        font-style: italic;
    }

    .legal-content table {
        width: 100%;
        margin: 24px 0;
        border-collapse: collapse;
    }

    .legal-content table th,
    .legal-content table td {
        padding: 12px;
        border: 1px solid var(--border);
        text-align: left;
    }

    .legal-content table th {
        background: var(--gray-100);
        font-weight: 600;
    }

    .legal-footer {
        margin-top: 48px;
        padding-top: 24px;
        border-top: 2px solid var(--border);
        text-align: center;
        color: var(--gray-600);
        font-size: 14px;
    }

    .back-to-top {
        margin-top: 24px;
    }

    .back-to-top a {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--primary);
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .back-to-top a:hover {
        background: #009606;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .legal-container {
            padding: 32px 24px;
        }

        .legal-header h1 {
            font-size: 28px;
        }

        .legal-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
<style>
    /* Legal policy pages (returns, privacy, cookies, accessibility) - enhanced design, scoped to .policy-page so terms.php and any other legal_content page type are unaffected */
    .policy-page {
        --pr-green: #00b207;
        --pr-green-rgb: 0,178,7;
        --pr-green-dark: #0a2e14;
        --pr-panel-1: #0b2f16;
        --pr-panel-2: #0f4020;
        --pr-bg-light: #f7f8f7;
        --pr-border: #e5e7e4;
        --pr-text-dark: #17181a;
        --pr-text-grey: #6b7280;
    }
    .policy-page .wrap { max-width: 1040px; margin: 0 auto; padding: 0 32px; }
    .policy-page a { text-decoration: none; }

    .policy-page .policy-hero {
        position: relative; overflow: hidden; text-align: center;
        padding: 56px 0 40px; border-bottom: 1px solid var(--pr-border);
        background: radial-gradient(circle at 50% 18%, rgba(var(--pr-green-rgb),.075), transparent 48%), linear-gradient(180deg,#fff 0%,#fbfefb 100%);
    }
    .policy-page .policy-hero::before {
        content: ""; position: absolute; width: 520px; height: 260px; left: 50%; top: 45%;
        transform: translate(-50%,-50%);
        background: radial-gradient(circle, rgba(var(--pr-green-rgb),.07), transparent 68%);
        pointer-events: none;
    }
    .policy-page .policy-hero > * { position: relative; z-index: 1; }
    .policy-page .eyebrow {
        display: inline-block; font-size: 13px; font-weight: 600; letter-spacing: .06em;
        color: var(--pr-green); background: #eafbea; padding: 7px 16px; border-radius: 100px;
        margin-bottom: 20px; box-shadow: inset 0 0 0 1px rgba(var(--pr-green-rgb),.10);
    }
    .policy-page .policy-hero h1 {
        font-size: clamp(30px,4vw,42px); font-weight: 700; letter-spacing: -.02em;
        margin-bottom: 12px; color: var(--pr-text-dark);
    }
    .policy-page .policy-meta {
        display: flex; gap: 12px 18px; justify-content: center; flex-wrap: wrap;
        font-size: 13px; color: var(--pr-text-grey); margin-top: 16px; padding: 0;
    }
    .policy-page .policy-meta span {
        background: #fff; border: 1px solid rgba(var(--pr-green-rgb),.12); border-radius: 100px;
        padding: 7px 12px; box-shadow: 0 7px 20px rgba(16,24,18,.05);
    }
    .policy-page .policy-meta span strong { color: var(--pr-text-dark); }

    .policy-page .track-selector { padding: 44px 0; background: var(--pr-bg-light); border-bottom: 1px solid var(--pr-border); }
    .policy-page .track-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; margin: 0; padding: 0; list-style: none; }
    .policy-page .track-card {
        position: relative; overflow: hidden; background: #fff; border: 1px solid rgba(var(--pr-green-rgb),.14);
        border-top: 3px solid var(--pr-green); border-radius: 20px; padding: 28px 26px;
        background-image: radial-gradient(circle at 90% 8%, rgba(var(--pr-green-rgb),.09), transparent 36%);
        box-shadow: 0 14px 34px rgba(16,24,18,.08), 0 6px 22px rgba(var(--pr-green-rgb),.05);
        transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
    }
    .policy-page .track-card:hover { transform: translateY(-3px); border-color: rgba(var(--pr-green-rgb),.30); box-shadow: 0 20px 44px rgba(16,24,18,.11), 0 10px 30px rgba(var(--pr-green-rgb),.09); }
    .policy-page .track-card h3 { font-size: 18px; margin-bottom: 8px; color: var(--pr-text-dark); }
    .policy-page .track-card p { font-size: 13.5px; color: var(--pr-text-grey); line-height: 1.7; margin-bottom: 14px; }
    .policy-page .track-card a { font-size: 13px; font-weight: 600; color: var(--pr-green); }

    .policy-page .toc-section { padding: 36px 0; }
    .policy-page .toc-box {
        max-width: 760px; margin: 0 auto; background: #fff; border: 1px solid rgba(var(--pr-green-rgb),.12);
        border-radius: 18px; padding: 26px 30px; box-shadow: 0 12px 30px rgba(16,24,18,.07);
    }
    .policy-page .toc-box h4 { font-size: 14px; letter-spacing: .06em; text-transform: uppercase; color: var(--pr-green); margin-bottom: 14px; font-weight: 700; }
    .policy-page .toc-cols { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 30px; }
    .policy-page .toc-cols a { display: block; font-size: 13.5px; color: var(--pr-text-grey); padding: 6px 8px; border-radius: 8px; transition: .18s ease; }
    .policy-page .toc-cols a:hover { color: var(--pr-green); background: #f1fff2; transform: translateX(2px); }

    .policy-page .policy-track { padding: 20px 0 56px; }
    .policy-page .track-banner {
        max-width: 900px; margin: 0 auto 36px; padding: 22px 28px; border-radius: 18px; position: relative; overflow: hidden;
        background: linear-gradient(135deg, var(--pr-panel-1) 0%, var(--pr-panel-2) 100%); color: #fff;
        box-shadow: 0 20px 50px rgba(4,39,13,.16);
    }
    .policy-page .track-banner::after {
        content: ""; position: absolute; width: 260px; height: 260px; right: -120px; top: -140px; border-radius: 50%;
        background: radial-gradient(circle, rgba(var(--pr-green-rgb),.24), transparent 70%);
    }
    .policy-page .track-banner > * { position: relative; z-index: 1; }
    .policy-page .track-banner h2 { color: #fff; font-size: 22px; margin-bottom: 4px; }
    .policy-page .track-banner p { color: #c9ecc9; font-size: 13.5px; margin: 0; }

    .policy-page .policy-section { max-width: 820px; margin: 0 auto 30px; scroll-margin-top: 100px; }
    .policy-page .policy-section h3 { font-size: 18px; margin-bottom: 10px; color: var(--pr-text-dark); scroll-margin-top: 100px; }
    .policy-page .policy-section h3 .num {
        display: inline-flex; align-items: center; justify-content: center; min-width: 30px; height: 30px;
        margin-right: 10px; padding: 0 7px; border-radius: 9px; background: #effff0;
        border: 1px solid rgba(var(--pr-green-rgb),.16); color: var(--pr-green); font-weight: 700; font-size: 13px;
    }
    .policy-page .policy-section p { font-size: 14.5px; color: var(--pr-text-grey); line-height: 1.8; margin-bottom: 12px; }
    .policy-page .policy-section ul, .policy-page .policy-section ol { margin: 0 0 14px 22px; }
    .policy-page .policy-section li { font-size: 14.5px; color: var(--pr-text-grey); line-height: 1.8; margin-bottom: 6px; }
    .policy-page .policy-section strong { color: var(--pr-text-dark); }
    .policy-page .policy-section code {
        background: #f1f5f1; border: 1px solid var(--pr-border); border-radius: 4px;
        padding: 1px 6px; font-size: 13px; font-family: 'Courier New', monospace; color: var(--pr-text-dark);
    }

    .policy-page .info-callout {
        max-width: 820px; margin: 16px auto; background: linear-gradient(135deg,#f2fff3 0%,#e9fbea 100%);
        border: 1px solid rgba(var(--pr-green-rgb),.18); border-left: 4px solid var(--pr-green); border-radius: 12px;
        padding: 18px 20px; font-size: 14px; color: var(--pr-green-dark); box-shadow: 0 8px 24px rgba(var(--pr-green-rgb),.05);
    }
    .policy-page .info-callout strong { color: var(--pr-green-dark); }

    .policy-page .policy-table-wrap {
        max-width: 820px; margin: 0 auto 30px; overflow-x: auto; border: 1px solid var(--pr-border);
        border-radius: 14px; box-shadow: 0 10px 28px rgba(16,24,18,.06); background: #fff;
    }
    .policy-page .policy-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 13.5px; }
    .policy-page .policy-table th {
        background: linear-gradient(180deg,#f6faf6 0%,#f0f6f0 100%); text-align: left; padding: 10px 14px;
        font-weight: 600; font-size: 12.5px; color: var(--pr-text-dark); border-bottom: 2px solid var(--pr-border);
    }
    .policy-page .policy-table td { padding: 10px 14px; border-bottom: 1px solid var(--pr-border); color: var(--pr-text-grey); }

    .policy-page .contact-block-policy {
        max-width: 820px; margin: 0 auto 30px; border: 1px solid rgba(var(--pr-green-rgb),.12); border-radius: 16px;
        background: linear-gradient(145deg,#fafcfa 0%,#f5faf5 100%); padding: 22px 26px;
        box-shadow: 0 10px 28px rgba(16,24,18,.06);
    }
    .policy-page .contact-block-policy h4 { font-size: 14px; margin-bottom: 10px; color: var(--pr-text-dark); }
    .policy-page .contact-block-policy p { font-size: 13.5px; color: var(--pr-text-grey); margin-bottom: 4px; }

    .policy-page .cta-band {
        background: linear-gradient(180deg,#fafcfa 0%,#f5f8f5 100%); border-top: 1px solid var(--pr-border);
        padding: 60px 0; text-align: center;
    }
    .policy-page .cta-band h2 { font-size: 26px; letter-spacing: -.02em; margin-bottom: 10px; color: var(--pr-text-dark); }
    .policy-page .cta-band p { color: var(--pr-text-grey); font-size: 15px; margin-bottom: 26px; }
    .policy-page .cta-actions { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .policy-page .cta-band .btn {
        display: inline-flex; align-items: center; gap: 8px; background: var(--pr-green); color: #fff;
        font-weight: 600; font-size: 15px; padding: 13px 26px; border-radius: 100px; border: none;
        box-shadow: 0 14px 34px rgba(var(--pr-green-rgb),.22); transition: .2s ease;
    }
    .policy-page .cta-band .btn:hover { transform: translateY(-2px); box-shadow: 0 18px 40px rgba(var(--pr-green-rgb),.28); }
    .policy-page .cta-band .btn-secondary {
        display: inline-flex; align-items: center; gap: 8px; background: #fff; color: var(--pr-text-dark);
        font-weight: 600; font-size: 15px; padding: 13px 24px; border-radius: 100px; border: 1px solid var(--pr-border);
    }
    .policy-page .cta-band .btn-secondary:hover { border-color: var(--pr-green); color: var(--pr-green); }

    @media (max-width: 700px) {
        .policy-page .policy-hero { padding: 44px 0 30px; }
        .policy-page .track-grid, .policy-page .toc-cols { grid-template-columns: 1fr; }
        .policy-page .track-card { padding: 24px 20px; }
        .policy-page .track-banner { padding: 20px; }
        .policy-page .policy-meta span { padding: 6px 10px; }
        .policy-page .wrap { padding: 0 20px; }
    }

    /* Seller/Supplier Account Agreements - scoped to .legal-agreement-page, distinct system from .policy-page */
    .legal-agreement-page {
        --la-green: #00b207;
        --la-green-dark: #0a2e14;
        --la-bg: #f7f8f7;
        --la-border: #e3e7e3;
        --la-text: #17181a;
        --la-grey: #606963;
        --la-shadow: 0 8px 28px rgba(16,24,18,.07);
        font-family: Arial, 'Helvetica Neue', sans-serif;
        line-height: 1.68;
    }
    .legal-agreement-page a { color: inherit; text-decoration: none; }
    .legal-agreement-page .wrap { max-width: 1040px; margin: 0 auto; padding: 0 32px; }

    .legal-agreement-page .hero {
        padding: 60px 0 44px; text-align: center;
        background: radial-gradient(circle at 50% 55%, rgba(0,178,7,.08), transparent 48%);
    }
    .legal-agreement-page .eyebrow {
        display: inline-block; color: var(--la-green); font-size: 12px; font-weight: 800;
        letter-spacing: .1em; background: #eafbea; border: 1px solid #cdeecd; border-radius: 999px;
        padding: 7px 15px; margin-bottom: 18px;
    }
    .legal-agreement-page .hero h1 {
        font-size: clamp(32px,4.5vw,46px); line-height: 1.15; letter-spacing: -.03em;
        margin: 0 auto 14px; max-width: 850px; color: var(--la-text); font-weight: 700;
    }
    .legal-agreement-page .hero-sub { font-size: 15.5px; color: var(--la-grey); max-width: 760px; margin: 0 auto 18px; }
    .legal-agreement-page .policy-meta { display: flex; justify-content: center; }
    .legal-agreement-page .policy-meta span {
        font-size: 13px; color: var(--la-grey); background: #fff; border: 1px solid rgba(0,178,7,.12);
        border-radius: 100px; padding: 7px 14px; box-shadow: 0 7px 20px rgba(16,24,18,.05);
    }
    .legal-agreement-page .policy-meta span strong { color: var(--la-text); }

    .legal-agreement-page .toc-wrap { padding: 32px 0; background: var(--la-bg); border-top: 1px solid var(--la-border); border-bottom: 1px solid var(--la-border); }
    .legal-agreement-page .toc { max-width: 900px; margin: auto; background: #fff; border: 1px solid var(--la-border); border-radius: 18px; padding: 24px 28px; box-shadow: var(--la-shadow); }
    .legal-agreement-page .toc h2 { font-size: 14px; color: var(--la-green); letter-spacing: .08em; text-transform: uppercase; margin: 0 0 14px; font-weight: 700; }
    .legal-agreement-page .toc-links { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 8px 22px; }
    .legal-agreement-page .toc-links a { font-size: 13px; color: var(--la-grey); padding: 4px 0; display: block; }
    .legal-agreement-page .toc-links a:hover { color: var(--la-green); }

    .legal-agreement-page .content { padding: 50px 0 70px; }
    .legal-agreement-page .legal-section { max-width: 860px; margin: 0 auto 34px; scroll-margin-top: 96px; }
    .legal-agreement-page .legal-section h2 {
        font-size: 20px; line-height: 1.35; margin: 0 0 14px; padding-bottom: 10px;
        border-bottom: 1px solid var(--la-border); color: var(--la-text);
    }
    .legal-agreement-page .legal-section h2 .num { display: inline-flex; min-width: 36px; color: var(--la-green); font-weight: 800; margin-right: 7px; }
    .legal-agreement-page .legal-section h3 { font-size: 15.5px; line-height: 1.4; margin: 22px 0 8px; color: var(--la-text); }
    .legal-agreement-page .subnum { color: var(--la-green); font-weight: 800; }
    .legal-agreement-page .legal-section p, .legal-agreement-page .content > p { font-size: 14px; color: var(--la-grey); margin: 0 auto 11px; max-width: 860px; }
    .legal-agreement-page .legal-section strong { color: var(--la-text); }
    .legal-agreement-page .legal-list { max-width: 830px; margin: 0 auto 16px; padding-left: 28px; }
    .legal-agreement-page .legal-list li { font-size: 14px; color: var(--la-grey); margin: 6px 0; line-height: 1.65; }
    .legal-agreement-page .schedule-section { margin-top: 44px; }
    .legal-agreement-page .signature-line {
        font-family: 'Courier New', monospace; color: #343a36; background: #fafafa;
        border: 1px solid var(--la-border); padding: 10px 12px; border-radius: 8px; display: inline-block;
    }
    .legal-agreement-page table { width: 100%; border-collapse: collapse; max-width: 860px; margin: 0 auto 20px; font-size: 13.5px; }
    .legal-agreement-page table th { background: var(--la-bg); text-align: left; padding: 10px 12px; font-weight: 700; color: var(--la-text); border-bottom: 2px solid var(--la-border); }
    .legal-agreement-page table td { padding: 10px 12px; border-bottom: 1px solid var(--la-border); color: var(--la-grey); }

    @media (max-width: 700px) {
        .legal-agreement-page .wrap { padding: 0 20px; }
        .legal-agreement-page .toc-links { grid-template-columns: 1fr; }
        .legal-agreement-page .hero { padding: 44px 0 32px; }
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

    <?php if (in_array($page['page_type'] ?? '', ['refund', 'privacy', 'cookies', 'accessibility', 'terms'], true)): ?>
    <div class="policy-page">
        <?= $page['content'] ?>
    </div>
    <?php elseif (in_array($page['page_type'] ?? '', ['seller_agreement', 'supplier_agreement'], true)): ?>
    <div class="legal-agreement-page">
        <?= $page['content'] ?>
    </div>
    <?php else: ?>
    <div class="page">
        <div class="legal-page">
            <div class="legal-container">
                <a href="<?= url('/') ?>" style="display: inline-block; margin-bottom: 20px; color: #00b207; text-decoration: none; font-weight: 600;">← Back to Home</a>

                <!-- Header -->
                <div class="legal-header">
                    <h1><?= htmlspecialchars($page['title']) ?></h1>
                    <div class="legal-meta">
                        <?php if (!empty($page['version'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-code-branch"></i>
                                <span>Version <?= $page['version'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($page['updated_at'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>Last Updated: <?= formatDate($page['updated_at'], 'F d, Y') ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($page['language'])): ?>
                            <div class="legal-meta-item">
                                <i class="fas fa-language"></i>
                                <span><?= strtoupper($page['language']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Content -->
                <div class="legal-content">
                    <?= $page['content'] ?>
                </div>

                <!-- Footer -->
                <div class="legal-footer">
                    <p>
                        <i class="fas fa-shield-alt"></i>
                        This document is legally binding and effective as of <?= formatDate($page['updated_at'] ?? date('Y-m-d'), 'F d, Y') ?>
                    </p>
                    <p style="margin-top: 8px;">
                        &copy; <?= date('Y') ?> OCSAPP. All rights reserved.
                    </p>

                    <div class="back-to-top">
                        <a href="#" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                            <i class="fas fa-arrow-up"></i> Back to Top
                        </a>
                    </div>
                </div>
            </div>
    </div>
    </div>
    <?php endif; ?>

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
