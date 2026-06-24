<?php
if (!defined('BASE_PATH')) { http_response_code(404); exit; }
$currentLang = $_GET['lang'] ?? $_SESSION['language'] ?? $_SESSION['lang'] ?? 'fr';
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $_SESSION['language'] = $_GET['lang'];
}
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr' : 'en' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'OCSAPP - L\'infrastructure numérique pour le commerce d\'ici' : 'OCSAPP - The Digital Infrastructure for Local Commerce' ?></title>
    <meta name="description" content="<?= $fr
        ? 'OCSAPP est la plateforme tout-en-un pour le commerce local : marché en ligne, portails vendeur, acheteur, fournisseur, livreur et entreprise.'
        : 'OCSAPP is the all-in-one platform for local commerce: online marketplace, seller, buyer, supplier, driver and business portals.'
    ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00ff88">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
            background: #0a0e27;
            color: white;
        }

        .landing-container {
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            width: 100%;
        }

        .tech-background {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            background:
                linear-gradient(135deg, rgba(10, 14, 39, 0.85) 0%, rgba(26, 31, 58, 0.85) 100%),
                url('<?= asset('images/landingbg.svg') ?>') center center / cover no-repeat;
            overflow: hidden;
        }

        .tech-background::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image:
                linear-gradient(rgba(0, 255, 136, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 136, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: gridMove 20s linear infinite;
        }

        @keyframes gridMove {
            0% { transform: translateY(0); }
            100% { transform: translateY(50px); }
        }

        .circuit-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.2), transparent);
            height: 1px; width: 100%;
            animation: lineMove 3s linear infinite;
        }

        @keyframes lineMove {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Lang switcher — fixed top-left, below beta strip */
        .lang-switcher {
            position: fixed;
            top: 46px; left: 16px;
            z-index: 999;
            display: flex;
            align-items: center;
            gap: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 700;
        }

        .lang-switcher a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.2s;
            letter-spacing: 0.5px;
        }

        .lang-switcher a:hover { color: #00ff88; border-color: rgba(0, 255, 136, 0.4); }

        .lang-switcher a.active {
            color: #0a0e27;
            background: #00ff88;
            border-color: #00ff88;
        }

        /* Login button — fixed top-right, below beta strip */
        .login-btn {
            position: fixed;
            top: 46px; right: 16px;
            z-index: 999;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: rgba(0, 255, 136, 0.12);
            border: 1px solid rgba(0, 255, 136, 0.5);
            color: #00ff88;
            font-family: 'Poppins', sans-serif;
            font-size: 12px;
            font-weight: 600;
            padding: 5px 16px;
            border-radius: 20px;
            text-decoration: none;
            letter-spacing: 0.3px;
            transition: all 0.25s;
            cursor: pointer;
        }

        .login-btn:hover {
            background: #00ff88;
            color: #0a0e27;
            border-color: #00ff88;
            box-shadow: 0 0 14px rgba(0, 255, 136, 0.35);
        }

        /* Beta Banner — sits at very top, standalone */
        .beta-strip {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(10, 14, 39, 0.92);
            border-bottom: 1px solid rgba(0, 255, 136, 0.25);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 7px 16px;
            font-size: 12px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            letter-spacing: 0.3px;
        }

        .beta-strip-badge {
            background: rgba(0, 255, 136, 0.15);
            border: 1px solid rgba(0, 255, 136, 0.4);
            color: #00ff88;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 2px 8px;
            border-radius: 20px;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .beta-strip a {
            color: rgba(0, 255, 136, 0.8);
            text-decoration: none;
            border-bottom: 1px solid rgba(0, 255, 136, 0.3);
            transition: color 0.2s;
            white-space: nowrap;
        }

        .beta-strip a:hover { color: #00ff88; }

        .beta-link-short { display: none; }
        .beta-text-short { display: none; }

        /* Push content below beta strip */
        .landing-container { padding-top: 60px; }

        /* Logo */
        .logo-section {
            position: relative;
            z-index: 10;
            text-align: center;
            margin-bottom: 60px;
            animation: fadeInDown 1s ease-out;
        }

        .logo-icon {
            width: 120px;
            height: auto; max-height: 120px;
            margin: 0 auto 10px;
            filter: drop-shadow(0 0 30px rgba(0, 255, 136, 0.5));
            animation: float 3s ease-in-out infinite;
            display: block;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-text {
            font-size: 56px;
            font-weight: 700;
            color: #00ff88;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            letter-spacing: 2px;
        }

        .logo-tagline {
            margin-top: 14px;
            font-size: 16px;
            font-weight: 300;
            color: rgba(255, 255, 255, 0.65);
            letter-spacing: 1.5px;
        }

        /* =====================================================
         * ORBIT GRID — 3 circles per row (original layout)
         * ===================================================== */

        .orbit-grid {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 0px 1fr 0px 1fr 0px 1fr;
            grid-template-rows: repeat(2, 1fr);
            gap: 80px 60px;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            transform: translateX(-6%);
        }

        /* Horizontal center line */
        .orbit-grid::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 135px;
            right: 108px;
            height: 2px;
            background: rgba(0, 255, 136, 0.3);
            transform: translateY(-50%);
            z-index: 0;
        }

        /* Vertical spikes */
        .orbit-item::before {
            content: '';
            position: absolute;
            width: 2px;
            background: rgba(0, 255, 136, 0.3);
            z-index: 0;
        }

        .orbit-item:nth-child(1)::before,
        .orbit-item:nth-child(2)::before,
        .orbit-item:nth-child(3)::before {
            top: 100%;
            left: 75px;
            height: 40px;
        }

        .orbit-item:nth-child(4)::before,
        .orbit-item:nth-child(5)::before,
        .orbit-item:nth-child(6)::before {
            bottom: 100%;
            left: 75px;
            height: 40px;
        }

        /* Grid placement */
        .orbit-item:nth-child(1) { grid-column: 2; grid-row: 1; position: relative; left: 50%; animation-delay: 0.1s; }
        .orbit-item:nth-child(2) { grid-column: 4; grid-row: 1; position: relative; left: 50%; animation-delay: 0.3s; }
        .orbit-item:nth-child(3) { grid-column: 6; grid-row: 1; position: relative; left: 50%; animation-delay: 0.5s; }
        .orbit-item:nth-child(4) { grid-column: 2; grid-row: 2; animation-delay: 0.7s; }
        .orbit-item:nth-child(5) { grid-column: 4; grid-row: 2; animation-delay: 0.9s; }
        .orbit-item:nth-child(6) { grid-column: 6; grid-row: 2; animation-delay: 1.1s; }

        .orbit-item {
            display: flex;
            align-items: center;
            gap: 18px;
            cursor: pointer;
            transition: transform 0.3s ease;
            animation: fadeInUp 1s ease-out both;
            text-decoration: none;
            color: inherit;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .orbit-item:hover { transform: scale(1.04); }

        /* Circle */
        .orbit-circle {
            width: 150px;
            height: 150px;
            min-width: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(0, 200, 100, 0.2));
            border: 3px solid #00ff88;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            font-weight: 700;
            color: #00ff88;
            text-align: center;
            padding: 15px;
            line-height: 1.2;
            text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
            box-shadow:
                0 0 30px rgba(0, 255, 136, 0.3),
                inset 0 0 20px rgba(0, 255, 136, 0.1);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .orbit-item:hover .orbit-circle {
            box-shadow:
                0 0 50px rgba(0, 255, 136, 0.5),
                inset 0 0 30px rgba(0, 255, 136, 0.2);
            border-color: #00ffaa;
        }

        /* Text content beside the circle */
        .orbit-content {
            flex: 1;
            min-width: 0;
        }

        .orbit-title {
            font-size: 13px;
            font-weight: 700;
            color: #00ff88;
            margin-bottom: 7px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-shadow: 0 0 8px rgba(0, 255, 136, 0.3);
        }

        .orbit-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .orbit-list li {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.72);
            padding-left: 14px;
            position: relative;
            line-height: 1.6;
        }

        .orbit-list li::before {
            content: '•';
            position: absolute;
            left: 0;
            color: #00ff88;
            font-size: 14px;
            line-height: 1.3;
        }

        .orbit-item:hover .orbit-title { text-shadow: 0 0 14px rgba(0, 255, 136, 0.6); }
        .orbit-item:hover .orbit-list li { color: rgba(255, 255, 255, 0.9); }

        /* ===== Responsive ===== */

        @media (max-width: 1400px) {
            .orbit-grid { gap: 70px 50px; transform: translateX(-5%); }
            .orbit-circle { width: 130px; height: 130px; min-width: 130px; font-size: 15px; }
            .orbit-grid::before { left: 115px; right: 95px; }
            .orbit-item:nth-child(1)::before,
            .orbit-item:nth-child(2)::before,
            .orbit-item:nth-child(3)::before { left: 65px; height: 35px; }
            .orbit-item:nth-child(4)::before,
            .orbit-item:nth-child(5)::before,
            .orbit-item:nth-child(6)::before { left: 65px; height: 35px; }
        }

        @media (max-width: 1200px) {
            .orbit-grid { gap: 60px 40px; transform: translateX(-2%); }
            .orbit-circle { width: 110px; height: 110px; min-width: 110px; font-size: 13px; padding: 12px; }
            .orbit-grid::before { left: 95px; right: 78px; }
            .orbit-item:nth-child(1)::before,
            .orbit-item:nth-child(2)::before,
            .orbit-item:nth-child(3)::before { left: 55px; height: 30px; }
            .orbit-item:nth-child(4)::before,
            .orbit-item:nth-child(5)::before,
            .orbit-item:nth-child(6)::before { left: 55px; height: 30px; }
            .orbit-item { gap: 14px; }
            .orbit-title { font-size: 12px; }
            .orbit-list li { font-size: 11px; }
        }

        @media (max-width: 960px) {
            .orbit-grid {
                grid-template-columns: 1fr 1fr;
                grid-template-rows: auto;
                gap: 20px;
                max-width: 720px;
                transform: none;
            }
            .orbit-grid::before { display: none; }
            .orbit-item::before { display: none; }
            .orbit-item,
            .orbit-item:nth-child(1),
            .orbit-item:nth-child(2),
            .orbit-item:nth-child(3),
            .orbit-item:nth-child(4),
            .orbit-item:nth-child(5),
            .orbit-item:nth-child(6) {
                grid-column: auto;
                grid-row: auto;
                left: 0;
                background: rgba(0, 255, 136, 0.04);
                border: 1px solid rgba(0, 255, 136, 0.2);
                border-radius: 16px;
                padding: 20px 18px;
                gap: 16px;
                align-items: flex-start;
            }
            .orbit-item:hover {
                transform: none;
                border-color: rgba(0, 255, 136, 0.5);
                background: rgba(0, 255, 136, 0.08);
            }
            .orbit-circle { width: 90px; height: 90px; min-width: 90px; font-size: 12px; padding: 10px; }
            .orbit-title { white-space: normal; font-size: 12px; }
            .orbit-list li { white-space: normal; font-size: 11px; }
            .logo-tagline { font-size: 13px; letter-spacing: 1px; }
        }

        @media (max-width: 640px) {
            .logo-text { font-size: 40px; }
            .logo-icon { width: 90px; max-height: 90px; }
            .logo-tagline { font-size: 12px; letter-spacing: 0.5px; }
            .orbit-grid { grid-template-columns: 1fr; max-width: 460px; gap: 14px; }
            .orbit-circle { width: 80px; height: 80px; min-width: 80px; font-size: 11px; padding: 8px; }
            .orbit-item,
            .orbit-item:nth-child(1),
            .orbit-item:nth-child(2),
            .orbit-item:nth-child(3),
            .orbit-item:nth-child(4),
            .orbit-item:nth-child(5),
            .orbit-item:nth-child(6) { padding: 16px 14px; align-items: center; }
            .beta-strip { top: 0; font-size: 11px; }
            .lang-switcher { top: 40px; }
            .login-btn { top: 40px; }
            .beta-text-full { display: none; }
            .beta-text-short { display: inline; }
            .beta-link-full { display: none; }
            .beta-link-short { display: inline; }
            .landing-container { padding-top: 78px; }
        }

        @media (max-width: 380px) {
            .logo-text { font-size: 32px; }
            .logo-icon { width: 75px; max-height: 75px; }
            .orbit-circle { width: 70px; height: 70px; min-width: 70px; font-size: 10px; }
            .orbit-title { font-size: 11px; }
            .orbit-list li { font-size: 10px; }
        }

        /* ===== Footer ===== */
        .landing-footer {
            position: relative;
            z-index: 10;
            width: 100%;
            padding: 24px 20px 20px;
            margin-top: 50px;
            border-top: 1px solid rgba(0, 255, 136, 0.2);
            text-align: center;
        }

        /* Social icons */
        .social-icons {
            display: flex;
            justify-content: center;
            gap: 14px;
            margin-bottom: 18px;
        }

        .social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.25s;
            border: none;
        }

        .social-icon.facebook  { background: #1877F2; }
        .social-icon.instagram { background: linear-gradient(135deg, #405DE6, #833AB4, #E1306C, #FD1D1D, #F77737, #FCAF45); }
        .social-icon.linkedin  { background: #0A66C2; }

        .social-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.4);
            filter: brightness(1.12);
        }

        .landing-footer p { color: rgba(255,255,255,0.6); font-size: 14px; margin-bottom: 14px; }

        .footer-links {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px 30px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .footer-links a:hover { color: #00ff88; }
    </style>
</head>
<body>

    <!-- Beta Banner -->
    <div class="beta-strip">
        <span class="beta-strip-badge">Beta</span>
        <span class="beta-text-full">
            <?= $fr
                ? 'Plateforme en cours de développement. Certaines fonctionnalités ne sont pas encore disponibles.'
                : 'Platform under development. Some features are not yet available.'
            ?>
        </span>
        <span class="beta-text-short">
            <?= $fr ? 'Version bêta. Fonctionnalités limitées.' : 'Beta version. Features may be limited.' ?>
        </span>
        <a href="<?= url('waitlist') ?>">
            <span class="beta-link-full"><?= $fr ? 'Rejoindre la liste d\'attente' : 'Join the waitlist' ?></span>
            <span class="beta-link-short"><?= $fr ? 'Liste d\'attente' : 'Waitlist' ?></span>
        </a>
    </div>

    <!-- Lang switcher (standalone, top-left) -->
    <div class="lang-switcher">
        <a href="?lang=fr" class="<?= $fr ? 'active' : '' ?>">FR</a>
        <a href="?lang=en" class="<?= !$fr ? 'active' : '' ?>">EN</a>
    </div>

    <!-- Login button (standalone, top-right) -->
    <a href="<?= url('login') ?>" class="login-btn">
        <i class="fas fa-sign-in-alt"></i>
        <?= $fr ? 'Se connecter' : 'Login' ?>
    </a>

    <div class="tech-background">
        <div class="circuit-line" style="top:20%"></div>
        <div class="circuit-line" style="top:40%;animation-delay:1s"></div>
        <div class="circuit-line" style="top:60%;animation-delay:2s"></div>
        <div class="circuit-line" style="top:80%;animation-delay:3s"></div>
    </div>

    <div class="landing-container">

        <div class="logo-section">
            <img src="<?= asset('images/landing-logo.png') ?>" alt="OCSAPP" class="logo-icon">
            <h1 class="logo-text">OCSAPP</h1>
            <p class="logo-tagline"><?= $fr
                ? 'L\'INFRASTRUCTURE NUMÉRIQUE TOUT-EN-UN POUR LE COMMERCE D\'ICI'
                : 'THE ALL-IN-ONE DIGITAL INFRASTRUCTURE LAYER FOR LOCAL COMMERCE' ?></p>
        </div>

        <div class="orbit-grid">

            <!-- Row 1 -->

            <a href="<?= url('home') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Marché<br>Central' : 'Marketplace<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Marché en ligne' : 'Online Marketplace' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Boutiques d'ici</li>
                            <li>Food court + meilleurs restos</li>
                            <li>Épicerie d'ici à prix doux</li>
                            <li>Livraison rapide</li>
                        <?php else: ?>
                            <li>Local shops</li>
                            <li>Food court + top resto</li>
                            <li>Local groceries at low prices</li>
                            <li>Express delivery</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

            <a href="<?= url('seller-central') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Vendeur<br>Central' : 'Seller<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Vendez sur OCSAPP' : 'Sell on OCSAPP' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Rejoignez des acheteurs d'ici</li>
                            <li>Gestion commandes & stocks</li>
                            <li>Boutique en ligne en minutes</li>
                            <li>Faites rayonner votre marque</li>
                        <?php else: ?>
                            <li>Reach local buyers</li>
                            <li>Order and stock control</li>
                            <li>Storefronts in minutes</li>
                            <li>Promote your brand</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

            <a href="<?= url('supplier-central') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Fournisseur<br>Central' : 'Supplier<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Fournissez la plateforme' : 'Supply the Platform' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Fournissez des produits</li>
                            <li>Tarifs de gros flexibles</li>
                            <li>Exécution & suivi en direct</li>
                            <li>Agrandissez votre réseau</li>
                        <?php else: ?>
                            <li>Supply products</li>
                            <li>Flexible wholesale pricing tiers</li>
                            <li>Live fulfillment & tracking</li>
                            <li>Expand distribution network</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

            <!-- Row 2 -->

            <a href="<?= url('buyer-central') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Acheteur<br>Central' : 'Buyer<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Commencez à magasiner' : 'Start Shopping' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Magasinez les produits d'ici</li>
                            <li>Livraison rapide ou planifiée</li>
                            <li>Suivez vos commandes en direct</li>
                            <li>Offres exclusives & récompenses</li>
                        <?php else: ?>
                            <li>Browse local products</li>
                            <li>ASAP & scheduled delivery</li>
                            <li>Track orders in real time</li>
                            <li>Exclusive deals & rewards</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

            <a href="<?= url('distribution') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Entreprise<br>Central' : 'Business<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Livraison pour entreprises' : 'Business Delivery' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Approvisionnement & distribution</li>
                            <li>Solutions bureau & cuisine</li>
                            <li>Horaires de livraison sur mesure</li>
                            <li>Gestion de compte dédiée</li>
                        <?php else: ?>
                            <li>Procurement & distribution</li>
                            <li>Office & breakroom solutions</li>
                            <li>Custom delivery schedules</li>
                            <li>Account management</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

            <a href="<?= url('driver-central') ?>" class="orbit-item">
                <div class="orbit-circle"><?= $fr ? 'Livreur<br>Central' : 'Driver<br>Central' ?></div>
                <div class="orbit-content">
                    <div class="orbit-title"><?= $fr ? 'Livrez & gagnez' : 'Drive & Earn' ?></div>
                    <ul class="orbit-list">
                        <?php if ($fr): ?>
                            <li>Choisissez vos horaires & trajets</li>
                            <li>Revenus de base hebdomadaires</li>
                            <li>Gérez vos jobs sur l'appli</li>
                            <li>Objectif zéro émission</li>
                        <?php else: ?>
                            <li>Set your own hours & routes</li>
                            <li>Weekly base earnings</li>
                            <li>App-based job management</li>
                            <li>Zero-emission goal</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </a>

        </div>

        <footer class="landing-footer">

            <div class="social-icons">
                <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener" class="social-icon facebook" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener" class="social-icon instagram" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener" class="social-icon linkedin" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>

            <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés' : 'All rights reserved' ?></p>
            <div class="footer-links">
                <a href="<?= url('privacy') ?>"><?= $fr ? 'Confidentialité' : 'Privacy' ?></a>
                <a href="<?= url('terms') ?>"><?= $fr ? 'Conditions' : 'Terms' ?></a>
                <a href="<?= url('cookies') ?>">Cookies</a>
            </div>
        </footer>

    </div>
</body>
</html>
