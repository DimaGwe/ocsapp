<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

$pageTitle = $fr ? 'Portail Distribution - OCSAPP' : 'Business Central - OCSAPP';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= generateCsrfToken() ?>">
    <title><?= $pageTitle ?></title>
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; color: #1f2937; }
        footer.footer { margin-top: 0; }

        /* ── Hero ── */
        .bc-hero {
            background: linear-gradient(135deg, #0a1628 0%, #0d2137 50%, #071220 100%);
            color: white;
            text-align: center;
            padding: 100px 24px 80px;
            position: relative;
            overflow: hidden;
        }
        .bc-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        .bc-hero-badge {
            display: inline-block;
            background: rgba(0,178,7,0.18);
            color: #4ade80;
            border: 1px solid rgba(0,178,7,0.35);
            padding: 8px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 28px;
        }
        .bc-hero h1 {
            font-size: clamp(30px, 5vw, 54px);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 20px;
        }
        .bc-hero h1 span { color: #4ade80; }
        .bc-hero p {
            font-size: clamp(15px, 2.2vw, 18px);
            opacity: 0.85;
            max-width: 620px;
            margin: 0 auto 40px;
            line-height: 1.65;
        }
        .bc-hero-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }
        .bc-btn-primary {
            background: #00b207;
            color: white;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            box-shadow: 0 4px 20px rgba(0,178,7,0.4);
        }
        .bc-btn-primary:hover {
            background: #009906;
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,178,7,0.5);
        }
        .bc-btn-secondary {
            background: rgba(255,255,255,0.12);
            color: white;
            border: 2px solid rgba(255,255,255,0.45);
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            backdrop-filter: blur(4px);
        }
        .bc-btn-secondary:hover {
            background: rgba(255,255,255,0.22);
            border-color: white;
            transform: translateY(-2px);
        }

        /* ── Stats bar ── */
        .bc-stats {
            background: white;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            border-bottom: 1px solid #e5e7eb;
        }
        .bc-stat {
            flex: 1;
            min-width: 150px;
            max-width: 240px;
            padding: 28px 16px;
            text-align: center;
            border-right: 1px solid #e5e7eb;
        }
        .bc-stat:last-child { border-right: none; }
        .bc-stat-val {
            font-size: 30px;
            font-weight: 800;
            color: #00b207;
            line-height: 1;
            margin-bottom: 6px;
        }
        .bc-stat-lbl {
            font-size: 13px;
            color: #6b7280;
            font-weight: 500;
        }

        /* ── Sections ── */
        .bc-section {
            padding: 80px 24px;
            max-width: 1100px;
            margin: 0 auto;
        }
        .bc-section-title {
            font-size: clamp(22px, 3.5vw, 34px);
            font-weight: 800;
            text-align: center;
            margin-bottom: 12px;
        }
        .bc-section-sub {
            text-align: center;
            color: #6b7280;
            font-size: 16px;
            margin-bottom: 52px;
        }
        .bc-alt-bg { background: #f0fdf4; }

        /* ── Pillars ── */
        .bc-pillars {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 32px;
            text-align: center;
        }
        .bc-pillar-icon {
            width: 72px;
            height: 72px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: #00b207;
        }
        .bc-pillar h3 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        .bc-pillar p {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── 3-step flow ── */
        .bc-steps {
            display: flex;
            gap: 0;
            flex-wrap: wrap;
            justify-content: center;
        }
        .bc-step {
            flex: 1;
            min-width: 200px;
            max-width: 280px;
            text-align: center;
            padding: 24px 20px;
            position: relative;
        }
        .bc-step:not(:last-child)::after {
            content: '→';
            position: absolute;
            right: -12px;
            top: 40px;
            font-size: 26px;
            color: #d1d5db;
        }
        .bc-step-num {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #00b207, #009906);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 800;
            margin: 0 auto 16px;
            box-shadow: 0 4px 14px rgba(0,178,7,0.3);
        }
        .bc-step h3 {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-step p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.55;
        }

        /* ── Feature cards ── */
        .bc-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .bc-feature {
            background: white;
            border-radius: 16px;
            padding: 32px 28px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: 1px solid #f0f0f0;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .bc-feature:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.10);
        }
        .bc-feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 18px;
        }
        .bc-feature h3 {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-feature p {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.65;
        }

        /* ── Social proof ── */
        .bc-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
            margin-bottom: 40px;
        }
        .bc-chip {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
        }
        .bc-quote {
            background: white;
            border-radius: 16px;
            padding: 32px 36px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            text-align: center;
        }
        .bc-quote p {
            font-size: 17px;
            color: #374151;
            font-style: italic;
            line-height: 1.7;
            margin-bottom: 16px;
        }
        .bc-quote span {
            font-size: 13px;
            color: #9ca3af;
            font-weight: 500;
        }

        /* ── FAQ ── */
        .bc-faq { max-width: 720px; margin: 0 auto; }
        .bc-faq-item {
            border-bottom: 1px solid #e5e7eb;
        }
        .bc-faq-item:first-child { border-top: 1px solid #e5e7eb; }
        .bc-faq-q {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            cursor: pointer;
            padding: 20px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 15px;
            font-weight: 600;
            color: #1f2937;
            gap: 16px;
            font-family: inherit;
        }
        .bc-faq-q i { color: #00b207; flex-shrink: 0; transition: transform 0.25s; }
        .bc-faq-q.open i { transform: rotate(45deg); }
        .bc-faq-a {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.7;
            padding-bottom: 20px;
            display: none;
        }
        .bc-faq-a.open { display: block; }

        /* ── Contact dark box ── */
        .bc-contact-box {
            background: #0a1628;
            color: white;
            border-radius: 20px;
            padding: 48px 40px;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }
        .bc-contact-box h3 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .bc-contact-box p {
            color: rgba(255,255,255,0.65);
            font-size: 14px;
            margin-bottom: 28px;
        }
        .bc-contact-links {
            display: flex;
            gap: 24px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .bc-contact-links a {
            color: #4ade80;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .bc-contact-links a:hover { color: #86efac; }

        /* ── Bottom CTA ── */
        .bc-cta {
            background: linear-gradient(135deg, #00b207 0%, #009906 100%);
            color: white;
            text-align: center;
            padding: 80px 24px;
        }
        .bc-cta h2 {
            font-size: clamp(24px, 4vw, 38px);
            font-weight: 800;
            margin-bottom: 16px;
        }
        .bc-cta p {
            font-size: 17px;
            opacity: 0.9;
            max-width: 500px;
            margin: 0 auto 36px;
            line-height: 1.6;
        }
        .bc-cta-btns {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .bc-cta-btn-primary {
            background: white;
            color: #00b207;
            padding: 16px 36px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .bc-cta-btn-primary:hover { background: #f0fdf4; transform: translateY(-2px); }
        .bc-cta-btn-secondary {
            background: rgba(255,255,255,0.18);
            color: white;
            border: 2px solid rgba(255,255,255,0.6);
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .bc-cta-btn-secondary:hover { background: rgba(255,255,255,0.28); transform: translateY(-2px); }

        @media (max-width: 640px) {
            .bc-hero { padding: 70px 20px 60px; }
            .bc-step:not(:last-child)::after { display: none; }
            .bc-contact-box { padding: 36px 24px; }
        }

        /* ── Pricing (matches Supplier/Seller Central card system) ── */
        .packages-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 20px; margin-top: 20px; }
        .pkg-card {
            border-radius: 16px; border: 2px solid #e5e7eb;
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
        .pkg-desc { font-size: 12px; color: rgba(255,255,255,.55); margin-bottom: 12px; line-height: 1.5; min-height: 34px; }
        .pkg-price { margin: 14px 0 0; }
        .pkg-price-num { font-size: 26px; font-weight: 900; line-height: 30px; display: block; }
        .pkg-price-lbl { font-size: 11px; color: rgba(255,255,255,.45); font-weight: 600; text-transform: uppercase; letter-spacing: .8px; margin-top: 3px; display: block; }
        .pkg-ess .pkg-price-num { color: #4ade80; }
        .pkg-exp .pkg-price-num { color: #93c5fd; }
        .pkg-pre .pkg-price-num { color: #c084fc; }
        .pkg-ent .pkg-price-num { color: #94a3b8; }
        .pkg-rate { margin: 10px 0 0; }
        .pkg-rate-num { font-size: 26px; font-weight: 900; line-height: 30px; display: block; }
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
            transition: opacity .2s, transform .15s; color: white; text-decoration: none;
        }
        .pkg-cta:hover { opacity: .88; transform: translateY(-1px); }
        .pkg-cta.outline { background: rgba(255,255,255,.08) !important; border: 1px solid rgba(255,255,255,.2); }
        .pkg-cta.outline:hover { background: rgba(255,255,255,.15) !important; }

        /* Per-tier card colors */
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

        .pkg-pre { background: #3b0764; border-color: #9333ea; box-shadow: 0 0 0 4px rgba(147,51,234,.2); }
        .pkg-pre .pkg-name { color: #c084fc; }
        .pkg-pre .pkg-popular { background: #9333ea; }
        .pkg-pre .pkg-features li:not(.inherited) i { color: #c084fc; }
        .pkg-pre .pkg-cta { background: #9333ea; }
        .pkg-pre .pkg-divider { border-color: rgba(192,132,252,.2); }

        .pkg-ent { background: #0f172a; border-color: #475569; }
        .pkg-ent .pkg-name { color: #94a3b8; }
        .pkg-ent .pkg-features li:not(.inherited) i { color: #94a3b8; }
        .pkg-ent .pkg-cta { background: #334155; }
        .pkg-ent .pkg-divider { border-color: rgba(148,163,184,.15); }

        .bc-price-note {
            background: #f0fdf4; border-left: 4px solid #00b207; border-radius: 0 12px 12px 0;
            padding: 16px 22px; font-size: 13.5px; color: #374151; line-height: 1.7; margin-top: 32px;
        }
        .bc-price-note strong { color: #0a1628; }
        @media (max-width: 900px) { .packages-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 640px) { .packages-grid { grid-template-columns: 1fr; } }

        /* suppress global header spacing against dark hero */
        .header { margin-bottom: 0; }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Hero -->
    <section class="bc-hero">
        <div class="bc-hero-badge">
            <i class="fas fa-truck-fast"></i>
            <?= $fr ? 'Livraison d\'entreprise zéro carbone' : 'Zero Carbon Business Delivery' ?>
        </div>
        <h1>
            <?= $fr ? 'Approvisionnement, Expéditions &amp; Distribution récurrente<br>pour <span>Votre Entreprise</span>' : 'Procurement, Shipments &amp; Recurring Distribution<br>for <span>Your Business</span>' ?>
        </h1>
        <p>
            <?= $fr
                ? 'Soumettez des demandes d\'approvisionnement multi-fournisseurs, suivez vos expéditions par GPS et automatisez vos routes de livraison récurrentes - le tout depuis un seul tableau de bord.'
                : 'Submit multi-supplier procurement requests, track GPS-verified shipments, and automate recurring delivery routes - all from one business dashboard.' ?>
        </p>
        <div class="bc-hero-btns">
            <a href="<?= url('distribution/register') ?>" class="bc-btn-primary">
                <i class="fas fa-building"></i>
                <?= $fr ? 'Inscrire votre entreprise' : 'Register Your Business' ?>
            </a>
            <a href="<?= url('distribution/login') ?>" class="bc-btn-secondary">
                <i class="fas fa-sign-in-alt"></i>
                <?= $fr ? 'Déjà inscrit? Se connecter' : 'Already Registered? Sign In' ?>
            </a>
        </div>
    </section>

    <!-- Stats -->
    <div class="bc-stats">
        <div class="bc-stat">
            <div class="bc-stat-val">500+</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Entreprises servies' : 'Businesses served' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val"><?= $fr ? 'Jour J' : 'Same Day' ?></div>
            <div class="bc-stat-lbl"><?= $fr ? 'Livraison disponible' : 'Delivery available' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val">0</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Frais cachés' : 'Hidden fees' ?></div>
        </div>
        <div class="bc-stat">
            <div class="bc-stat-val">100%</div>
            <div class="bc-stat-lbl"><?= $fr ? 'Objectif écologique' : 'Eco-delivery goal' ?></div>
        </div>
    </div>

    <!-- Pillars -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Pourquoi choisir OCSAPP?' : 'Why Choose OCSAPP?' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Tout ce dont votre entreprise a besoin, livré.' : 'Everything your business needs, delivered.' ?></p>
        <div class="bc-pillars">
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-clipboard-list"></i></div>
                <h3><?= $fr ? 'Demandes d\'approvisionnement' : 'Procurement Requests' ?></h3>
                <p><?= $fr ? 'Soumettez une demande couvrant plusieurs fournisseurs à la fois - OCSAPP coordonne l\'approvisionnement et consolide la livraison.' : 'Submit one request across multiple suppliers at once - OCSAPP coordinates sourcing and consolidates delivery.' ?></p>
            </div>
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-route"></i></div>
                <h3><?= $fr ? 'Expéditions suivies par GPS' : 'GPS-Tracked Shipments' ?></h3>
                <p><?= $fr ? 'Chaque expédition est suivie de bout en bout par notre réseau de chauffeurs ODA, de la collecte à la preuve de livraison.' : 'Every shipment is tracked door-to-door through our ODA driver network, from pickup to proof of delivery.' ?></p>
            </div>
            <div class="bc-pillar">
                <div class="bc-pillar-icon"><i class="fas fa-repeat"></i></div>
                <h3><?= $fr ? 'Routes récurrentes' : 'Recurring Routes' ?></h3>
                <p><?= $fr ? 'Configurez des routes de livraison récurrentes pour votre réapprovisionnement régulier - sans devoir soumettre une nouvelle demande à chaque fois.' : 'Set up recurring delivery routes for regular resupply - no need to submit a new request every time.' ?></p>
            </div>
        </div>
    </div>

    <!-- How it works -->
    <div class="bc-alt-bg">
        <div class="bc-section">
            <h2 class="bc-section-title"><?= $fr ? 'Comment ça marche' : 'How It Works' ?></h2>
            <p class="bc-section-sub"><?= $fr ? 'En trois étapes simples' : 'Three simple steps' ?></p>
            <div class="bc-steps">
                <div class="bc-step">
                    <div class="bc-step-num">1</div>
                    <h3><?= $fr ? 'Inscrivez-vous' : 'Register' ?></h3>
                    <p><?= $fr ? 'Créez votre compte entreprise vérifié par NEQ en quelques minutes.' : 'Create your NEQ-verified business account in minutes.' ?></p>
                </div>
                <div class="bc-step">
                    <div class="bc-step-num">2</div>
                    <h3><?= $fr ? 'Demandez ou automatisez' : 'Request or Automate' ?></h3>
                    <p><?= $fr ? 'Soumettez une demande d\'approvisionnement ponctuelle ou configurez une route récurrente avec vos fournisseurs et votre calendrier de livraison.' : 'Submit a one-time procurement request, or set up a recurring route with your suppliers and delivery schedule.' ?></p>
                </div>
                <div class="bc-step">
                    <div class="bc-step-num">3</div>
                    <h3><?= $fr ? 'Suivez et recevez' : 'Track & Receive' ?></h3>
                    <p><?= $fr ? 'Suivez votre expédition en direct par GPS, recevez vos documents (BC, BL, facture) et payez en ligne en toute sécurité.' : 'Track your shipment live via GPS, receive your PO/SO/invoice documents, and pay securely online.' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Features -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Tout ce que vous obtenez' : 'Everything You Get' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Des outils conçus pour les entreprises modernes' : 'Tools built for modern businesses' ?></p>
        <div class="bc-features">
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#dcfce7;color:#15803d;"><i class="fas fa-boxes-stacked"></i></div>
                <h3><?= $fr ? 'Approvisionnement multi-fournisseurs' : 'Multi-Supplier Procurement' ?></h3>
                <p><?= $fr ? 'Soumettez une seule demande couvrant plusieurs fournisseurs - OCSAPP coordonne l\'approvisionnement et consolide la livraison.' : 'Submit one request across multiple suppliers - OCSAPP coordinates sourcing and consolidates delivery.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-repeat"></i></div>
                <h3><?= $fr ? 'Routes de livraison récurrentes' : 'Recurring Delivery Routes' ?></h3>
                <p><?= $fr ? 'Automatisez votre réapprovisionnement régulier avec des routes récurrentes - mettez en pause, reprenez ou ajustez à tout moment.' : 'Automate regular resupply with scheduled recurring routes - pause, resume, or adjust anytime.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fas fa-map-marker-alt"></i></div>
                <h3><?= $fr ? 'Expéditions suivies par GPS' : 'GPS-Tracked Shipments' ?></h3>
                <p><?= $fr ? 'Suivez chaque expédition en direct, de la collecte à la livraison, avec confirmation de preuve de livraison.' : 'Track every shipment live from pickup to delivery, with proof-of-delivery confirmation.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#fef3c7;color:#b45309;"><i class="fas fa-file-invoice"></i></div>
                <h3><?= $fr ? 'Documents BC, BL &amp; facture' : 'PO, SO &amp; Invoice Documents' ?></h3>
                <p><?= $fr ? 'Bons de commande, bons de livraison et factures générés automatiquement - prêts en PDF pour vos dossiers.' : 'Purchase orders, sales orders, and invoices generated automatically - PDF-ready for your records.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#d1fae5;color:#065f46;"><i class="fas fa-credit-card"></i></div>
                <h3><?= $fr ? 'Paiements en ligne sécurisés' : 'Secure Online Payments' ?></h3>
                <p><?= $fr ? 'Payez en toute sécurité par carte via Stripe, avec une facturation détaillée claire - aucun frais caché.' : 'Pay securely by card via Stripe, with clear itemized invoicing - no hidden fees.' ?></p>
            </div>
            <div class="bc-feature">
                <div class="bc-feature-icon" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-headset"></i></div>
                <h3><?= $fr ? 'Support de compte dédié' : 'Dedicated Account Support' ?></h3>
                <p><?= $fr ? 'Une équipe dédiée et une messagerie intégrée pour répondre à vos questions et résoudre tout problème rapidement.' : 'A dedicated team and built-in messaging to answer questions and resolve any issues quickly.' ?></p>
            </div>
        </div>
    </div>

    <!-- Pricing -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Deux façons de travailler avec nous' : 'Two Ways to Work With Us' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Approvisionnez-vous auprès de notre réseau de fournisseurs, ou utilisez notre réseau de livraison pour vos propres produits - ou les deux.' : 'Source products through our supplier network, or use our delivery network for goods you already have - or both.' ?></p>

        <div class="packages-grid">

            <div class="pkg-card pkg-ess">
                <div class="pkg-name"><?= $fr ? 'Approvisionnement' : 'Procurement' ?></div>
                <div class="pkg-desc"><?= $fr ? 'Inclus gratuitement avec tout compte entreprise' : 'Included free with every business account' ?></div>
                <div class="pkg-price">
                    <span class="pkg-price-num"><?= $fr ? 'Gratuit' : 'Free' ?></span>
                    <span class="pkg-price-lbl"><?= $fr ? 'pour commencer' : 'to start' ?></span>
                </div>
                <div class="pkg-rate">
                    <span class="pkg-rate-num">1%</span>
                    <span class="pkg-rate-lbl"><?= $fr ? 'frais approvisionnement' : 'procurement fee' ?></span>
                </div>
                <hr class="pkg-divider">
                <ul class="pkg-features">
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Demandes multi-fournisseurs' : 'Multi-supplier procurement requests' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Documents BC, BL &amp; facture' : 'PO, SO &amp; invoice documents' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Paiements en ligne sécurisés' : 'Secure online payments' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Tableau de bord &amp; messagerie' : 'Dashboard &amp; messaging' ?></li>
                </ul>
                <a href="<?= url('distribution/register') ?>" class="pkg-cta outline"><?= $fr ? 'Inscription gratuite' : 'Sign Up Free' ?></a>
            </div>

            <div class="pkg-card pkg-exp">
                <div class="pkg-name"><?= $fr ? 'Débutant' : 'Starter' ?></div>
                <div class="pkg-desc"><?= $fr ? 'Distribution pour vos propres produits' : 'Distribution for your own products' ?></div>
                <div class="pkg-price">
                    <span class="pkg-price-num">$49</span>
                    <span class="pkg-price-lbl"><?= $fr ? 'par mois' : 'per month' ?></span>
                </div>
                <div class="pkg-rate">
                    <span class="pkg-rate-num">5%</span>
                    <span class="pkg-rate-lbl"><?= $fr ? 'frais distribution' : 'distribution fee' ?></span>
                </div>
                <hr class="pkg-divider">
                <ul class="pkg-features">
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Expéditions suivies par GPS' : 'GPS-tracked shipments' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Intégration livraison locale' : 'Local delivery integration' ?></li>
                </ul>
                <a href="<?= url('distribution/register') ?>" class="pkg-cta"><?= $fr ? 'Commencer' : 'Get Started' ?></a>
            </div>

            <div class="pkg-card pkg-pre">
                <div class="pkg-popular"><?= $fr ? 'Le plus populaire' : 'Most Popular' ?></div>
                <div class="pkg-name">Pro</div>
                <div class="pkg-desc"><?= $fr ? 'Distribution pour PME en croissance' : 'Distribution for growing SMEs' ?></div>
                <div class="pkg-price">
                    <span class="pkg-price-num">$179</span>
                    <span class="pkg-price-lbl"><?= $fr ? 'par mois' : 'per month' ?></span>
                </div>
                <div class="pkg-rate">
                    <span class="pkg-rate-num">7%</span>
                    <span class="pkg-rate-lbl"><?= $fr ? 'frais distribution' : 'distribution fee' ?></span>
                </div>
                <hr class="pkg-divider">
                <ul class="pkg-features">
                    <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce que Débutant inclut' : 'Everything in Starter' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Routes de livraison récurrentes' : 'Recurring delivery routes' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Support multi-emplacements' : 'Multi-location support' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Routage dédié' : 'Dedicated routing' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Analytiques avancées' : 'Advanced analytics' ?></li>
                </ul>
                <a href="<?= url('distribution/register') ?>" class="pkg-cta"><?= $fr ? 'Commencer' : 'Get Started' ?></a>
            </div>

            <div class="pkg-card pkg-ent">
                <div class="pkg-name">Enterprise</div>
                <div class="pkg-desc"><?= $fr ? 'Distribution sur mesure, grande échelle' : 'Custom distribution, large scale' ?></div>
                <div class="pkg-price">
                    <span class="pkg-price-num" style="font-size:18px;white-space:nowrap"><?= $fr ? 'Sur devis' : 'Custom quote' ?></span>
                    <span class="pkg-price-lbl"><?= $fr ? 'par mois' : 'per month' ?></span>
                </div>
                <div class="pkg-rate">
                    <span class="pkg-rate-num" style="font-size:16px;white-space:nowrap"><?= $fr ? 'Prix sur demande' : 'Price upon request' ?></span>
                    <span class="pkg-rate-lbl"><?= $fr ? 'frais distribution' : 'distribution fee' ?></span>
                </div>
                <hr class="pkg-divider">
                <ul class="pkg-features">
                    <li class="inherited"><i class="fas fa-layer-group"></i> <?= $fr ? 'Tout ce que Pro inclut' : 'Everything in Pro' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Accès API complet' : 'Full API access' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Support logistique dédié' : 'Dedicated logistics support' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'SLA personnalisés' : 'Custom SLAs' ?></li>
                    <li><i class="fas fa-check-circle"></i> <?= $fr ? 'Équipe de compte dédiée' : 'Dedicated account team' ?></li>
                </ul>
                <a href="mailto:info@ocsapp.ca?subject=Enterprise%20Distribution%20Inquiry" class="pkg-cta"><?= $fr ? 'Nous contacter' : 'Contact Us' ?></a>
            </div>

        </div>

        <div class="bc-price-note">
            <?= $fr
                ? '<strong>Remarque :</strong> Vous pouvez utiliser Approvisionnement seul, Distribution seul, ou les deux ensemble sur le même compte entreprise. Une livraison forfaitaire de 19$ (Île de Montréal Ouest), 21$ (Laval) ou 24$ (noyau du Grand Montréal), selon la zone, est incluse avec tous les forfaits. Le frais distribution est le taux de base (Île de Montréal Ouest); il est ajusté selon la zone de livraison (+12,5 % à Laval, +25 % dans le noyau du Grand Montréal). Pour Distribution, les frais de traitement des paiements (2,9 % + 0,30 $ CAD) sont absorbés par le compte entreprise et déduits du montant net avant versement.'
                : '<strong>Note:</strong> You can use Procurement alone, Distribution alone, or both together on the same business account. A flat delivery fee of $19 (West Island), $21 (Laval), or $24 (Greater Montreal core), depending on zone, is included with all packages. The distribution fee shown is the base (West Island) rate; it is zone-adjusted upward in Laval (+12.5%) and Greater Montreal core (+25%). For Distribution, payment processing fees (2.9% + $0.30 CAD) are absorbed by the business account and deducted from net proceeds before payout.' ?>
        </div>
    </div>

    <!-- Social proof -->
    <div class="bc-alt-bg">
        <div class="bc-section">
            <h2 class="bc-section-title"><?= $fr ? 'Des entreprises qui nous font confiance' : 'Businesses That Trust Us' ?></h2>
            <p class="bc-section-sub"><?= $fr ? 'De tous les secteurs, dans le West Island et en expansion' : 'Across all industries, in the West Island and expanding' ?></p>
            <div class="bc-chips">
                <span class="bc-chip"><i class="fas fa-utensils" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Restaurants' : 'Restaurants' ?></span>
                <span class="bc-chip"><i class="fas fa-briefcase" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Bureaux' : 'Offices' ?></span>
                <span class="bc-chip"><i class="fas fa-store" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Commerce de détail' : 'Retail Stores' ?></span>
                <span class="bc-chip"><i class="fas fa-clinic-medical" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Cliniques' : 'Clinics' ?></span>
                <span class="bc-chip"><i class="fas fa-hammer" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Construction' : 'Construction' ?></span>
                <span class="bc-chip"><i class="fas fa-graduation-cap" style="color:#00b207;margin-right:6px;"></i><?= $fr ? 'Éducation' : 'Education' ?></span>
            </div>
            <div class="bc-quote">
                <p>"<?= $fr ? 'OCSAPP consolide nos commandes auprès de plusieurs fournisseurs en une seule expédition, et nous pouvons tout suivre de la collecte à la livraison. Ça a coupé notre temps d\'administration d\'approvisionnement de moitié.' : 'OCSAPP consolidates our supplier orders into one shipment, and we can track everything from pickup to delivery. It has cut our procurement admin time in half.' ?>"</p>
                <span><?= $fr ? '- Responsable des opérations, Restaurant local' : '- Operations Manager, Local Restaurant' ?></span>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="bc-section">
        <h2 class="bc-section-title"><?= $fr ? 'Questions fréquentes' : 'Frequently Asked Questions' ?></h2>
        <p class="bc-section-sub"><?= $fr ? 'Tout ce que vous devez savoir' : 'Everything you need to know' ?></p>
        <div class="bc-faq">
            <?php
            $faqs = [
                [
                    'q' => $fr ? 'Comment puis-je inscrire mon entreprise?' : 'How do I register my business?',
                    'a' => $fr ? 'Cliquez sur "Inscrire votre entreprise", complétez la vérification NEQ et votre compte est activé une fois approuvé.' : 'Click "Register Your Business", complete NEQ verification, and your account is active once approved.',
                ],
                [
                    'q' => $fr ? 'Comment fonctionne l\'approvisionnement?' : 'How does procurement work?',
                    'a' => $fr ? 'Soumettez une demande listant les produits et quantités dont vous avez besoin. OCSAPP s\'approvisionne auprès de vos fournisseurs, consolide la commande et confirme les prix avant l\'expédition.' : 'Submit a request listing the products and quantities you need. OCSAPP sources from your suppliers, consolidates the order, and confirms pricing before anything ships.',
                ],
                [
                    'q' => $fr ? 'Puis-je configurer des livraisons récurrentes?' : 'Can I set up recurring deliveries?',
                    'a' => $fr ? 'Oui, avec le forfait Distribution Pro ou supérieur - configurez une route récurrente pour automatiser votre réapprovisionnement selon le calendrier de votre choix. Mettez en pause, reprenez ou annulez à tout moment depuis votre tableau de bord.' : 'Yes, with the Distribution Pro plan or higher - set up a recurring route to automate resupply on a schedule you choose. Pause, resume, or cancel anytime from your dashboard.',
                ],
                [
                    'q' => $fr ? 'Combien ça coûte?' : 'How much does it cost?',
                    'a' => $fr ? 'Approvisionnement est gratuit (1 % de frais d\'approvisionnement, aucun abonnement mensuel) - nous nous approvisionnons auprès de nos fournisseurs pour vous. Distribution est un service séparé pour vos propres produits, avec des forfaits Débutant, Pro et Enterprise - consultez la section Forfaits ci-dessus pour les frais exacts. Utilisez l\'un, l\'autre, ou les deux. Aucune majoration cachée sur les produits.' : 'Procurement is free (1% procurement fee, no monthly subscription) - we source products from our suppliers on your behalf. Distribution is a separate service for delivering your own products, with Starter, Pro, and Enterprise plans - see the Plans section above for exact fees. Use one, the other, or both. There is no hidden markup on goods.',
                ],
                [
                    'q' => $fr ? 'Puis-je suivre mon expédition en temps réel?' : 'Can I track my shipment in real time?',
                    'a' => $fr ? 'Oui. Chaque expédition est suivie par GPS de la collecte à la livraison, avec des mises à jour en direct dans votre tableau de bord.' : 'Yes. Every shipment is GPS-tracked from pickup to delivery, with live status updates in your dashboard.',
                ],
                [
                    'q' => $fr ? 'Comment fonctionne le paiement?' : 'How do I pay for orders?',
                    'a' => $fr ? 'Payez en toute sécurité en ligne via Stripe une fois votre expédition confirmée. Vous recevez un bon de commande, un bon de livraison et une facture pour chaque transaction.' : 'Pay securely online via Stripe once your shipment is confirmed. You will receive a PO, sales order, and invoice for every transaction.',
                ],
                [
                    'q' => $fr ? 'Que se passe-t-il si un fournisseur ne peut pas fournir un article?' : 'What if a supplier can\'t fulfill an item?',
                    'a' => $fr ? 'Nous vous contactons immédiatement pour approuver un substitut ou ajuster la commande avant l\'expédition. Vous avez toujours le dernier mot.' : 'We contact you immediately so you can approve a substitute or adjust the order before it ships. You always have the final say.',
                ],
                [
                    'q' => $fr ? 'Quelles zones sont desservies?' : 'Which areas do you serve?',
                    'a' => $fr ? 'Nous desservons actuellement l\'Île de Montréal Ouest, avec une expansion prévue à Laval et au noyau du Grand Montréal. Contactez-nous pour vérifier la disponibilité dans votre région.' : 'We currently serve the West Island, with expansion planned to Laval and the Greater Montreal core. Contact us to verify availability in your area.',
                ],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="bc-faq-item">
                <button class="bc-faq-q" onclick="bcToggleFaq(this)" type="button">
                    <?= htmlspecialchars($faq['q']) ?>
                    <i class="fas fa-plus"></i>
                </button>
                <div class="bc-faq-a"><?= htmlspecialchars($faq['a']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Contact -->
    <div class="bc-section" style="padding-top:0;">
        <div class="bc-contact-box">
            <h3><?= $fr ? 'Une question? Contactez-nous.' : 'Have a question? Get in touch.' ?></h3>
            <p><?= $fr ? 'Notre équipe répond dans les 24 heures ouvrables.' : 'Our team responds within 24 business hours.' ?></p>
            <div class="bc-contact-links">
                <a href="mailto:info@ocsapp.ca"><i class="fas fa-envelope"></i> info@ocsapp.ca</a>
                <a href="tel:5147463789"><i class="fas fa-phone"></i> 514-746-3789</a>
                <a href="<?= url('home') ?>"><i class="fas fa-globe"></i> ocsapp.ca</a>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <section class="bc-cta">
        <h2><?= $fr ? 'Prêt à simplifier votre distribution?' : 'Ready to Simplify Your Distribution?' ?></h2>
        <p><?= $fr ? 'Rejoignez les entreprises canadiennes qui font confiance à OCSAPP pour leur approvisionnement, leurs expéditions et leurs routes récurrentes.' : 'Join Canadian businesses that trust OCSAPP for procurement, shipments, and recurring routes.' ?></p>
        <div class="bc-cta-btns">
            <a href="<?= url('distribution/register') ?>" class="bc-cta-btn-primary">
                <i class="fas fa-building"></i>
                <?= $fr ? 'Créer un compte entreprise' : 'Create Business Account' ?>
            </a>
            <a href="<?= url('distribution/login') ?>" class="bc-cta-btn-secondary">
                <i class="fas fa-sign-in-alt"></i>
                <?= $fr ? 'Se connecter' : 'Sign In' ?>
            </a>
        </div>
    </section>

    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
    function bcToggleFaq(btn) {
        const answer = btn.nextElementSibling;
        const isOpen = btn.classList.contains('open');
        document.querySelectorAll('.bc-faq-q.open').forEach(b => {
            b.classList.remove('open');
            b.nextElementSibling.classList.remove('open');
        });
        if (!isOpen) {
            btn.classList.add('open');
            answer.classList.add('open');
        }
    }
    </script>
</body>
</html>
