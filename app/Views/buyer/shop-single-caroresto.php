<?php
/**
 * One-off pitch variant of the shop page for Caro Resto: same URL/shell as the
 * normal shop-single page, but opens on a branded "display" view (hero, about,
 * menu highlights, hours/map) and switches in-place to the real storefront
 * (sidebar + product grid) when the visitor clicks an order CTA - no page reload.
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$shop = $shop ?? [];
$products = $products ?? [];
$categories = $categories ?? [];
$selectedCategory = $selectedCategory ?? '';
$sortBy = $sortBy ?? 'popularity';
$currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title><?= htmlspecialchars($shop['name'] ?? 'Shop') ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <style>
        body {
            padding-bottom: 0;
            background: #f7f7f7;
        }

        .shop-layout {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 5%;
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 20px;
        }

        .shop-sidebar {
            background: white;
            border-radius: 12px;
            padding: 0;
            position: sticky;
            top: 80px;
            height: fit-content;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .shop-info {
            text-align: center;
            padding: 30px;
            border-bottom: 1px solid #e6e6e6;
        }
        .shop-logo-large {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            background: #f7f7f7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #e6e6e6;
        }
        .shop-logo-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .shop-name-large {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        .shop-location-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .shop-trust-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
            margin-bottom: 6px;
        }
        .shop-rating-large {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3cd;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .shop-rating-large .stars {
            color: #ffc107;
        }
        .shop-rating-large .reviews-count {
            color: #92720a;
            font-weight: 500;
        }
        .shop-delivery-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e6f7e8;
            color: #0d7a12;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .view-info-btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background: #00b207;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .view-info-btn:hover {
            background: #888;
            color: white;
        }

        .sidebar-menu {
            padding: 20px 0;
        }
        .sidebar-menu-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 30px;
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-menu-item:hover {
            background: #f7f7f7;
            border-left-color: #00b207;
        }
        .sidebar-menu-item svg, .sidebar-menu-item i {
            width: 20px;
            text-align: center;
            font-size: 16px;
            color: #00b207;
        }

        .categories-section {
            padding: 20px 30px;
            border-top: 1px solid #e6e6e6;
        }
        .categories-title {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }
        .category-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            cursor: pointer;
            transition: all 0.2s;
        }
        .category-item input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .category-item label {
            flex: 1;
            cursor: pointer;
            font-size: 14px;
            color: #333;
        }
        .category-item:hover label {
            color: #00b207;
        }

        .shop-main {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .shop-banner {
            position: relative;
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            display: flex;
            align-items: flex-end;
            overflow: hidden;
        }
        .shop-banner img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .shop-banner-icon-fallback {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: rgba(255,255,255,0.35);
        }
        .shop-banner-overlay {
            position: relative;
            z-index: 1;
            width: 100%;
            padding: 40px 30px 18px;
            background: linear-gradient(to top, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0) 100%);
            color: white;
        }
        .shop-banner-name {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 10px;
            text-shadow: 0 1px 4px rgba(0,0,0,0.4);
        }
        .shop-banner-tagline {
            font-size: 14px;
            opacity: 0.92;
            margin-top: 4px;
            max-width: 640px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.4);
        }
        .shop-founding-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(251,191,36,0.9);
            color: #4a2c00;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
        }

        .products-header {
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e6e6e6;
        }
        .products-header-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .products-title {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            line-height: 1.2;
        }
        .products-count {
            color: #666;
            font-size: 15px;
            font-weight: 500;
        }
        .sort-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sort-select {
            padding: 10px 15px;
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: white;
            min-width: 180px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 24px;
            padding: 30px;
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
        }

        .breadcrumb-menu {
            background: transparent;
            padding: 16px 5% 14px;
            display: flex;
            gap: 8px;
            align-items: center;
            max-width: 1400px;
            margin: 10px auto 0;
            position: relative;
            font-size: 13px;
        }

        .breadcrumb-menu::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 5%;
            right: 5%;
            height: 1px;
            background: #e6e6e6;
        }

        .breadcrumb-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .breadcrumb-btn:hover {
            color: #00b207;
        }

        .breadcrumb-btn.active {
            color: #222;
            font-weight: 600;
        }

        .breadcrumb-sep {
            color: #ccc;
            font-size: 11px;
        }

        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 1024px) {
            .shop-layout {
                grid-template-columns: 1fr;
                padding: 30px 4%;
            }
            .shop-sidebar {
                position: relative;
                top: 0;
            }
            .breadcrumb-menu {
                padding: 20px 4%;
            }

            .breadcrumb-menu::after {
                left: 4%;
                right: 4%;
            }
        }

        @media (max-width: 768px) {
            .shop-layout {
                padding: 30px 4%;
            }
            .breadcrumb-menu {
                gap: 10px;
            }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
                gap: 16px;
            }
            .products-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
                padding: 20px;
            }
            .products-title {
                font-size: 20px;
            }
            .products-count {
                font-size: 14px;
            }
            .sort-select {
                width: 100%;
                min-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .shop-layout {
                padding: 30px 4%;
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .products-title {
                font-size: 18px;
            }
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .modal-header {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 12px 12px 0 0;
            position: relative;
        }
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            font-size: 24px;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        .modal-body {
            padding: 30px;
        }
        .shop-info-section {
            margin-bottom: 25px;
        }
        .shop-info-section:last-child {
            margin-bottom: 0;
        }
        .shop-info-label {
            font-size: 14px;
            color: #666;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .shop-info-value {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .shop-info-value i {
            color: #00b207;
            margin-right: 8px;
            width: 20px;
        }
        .shop-hours-table {
            width: 100%;
            margin-top: 10px;
        }
        .shop-hours-table td {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .shop-hours-table td:first-child {
            font-weight: 600;
            color: #333;
            width: 120px;
        }
        .shop-hours-table td:last-child {
            color: #666;
        }
        .shop-hours-table tr:last-child td {
            border-bottom: none;
        }

        /* ===== Landing / display view (Caro Resto branded) ===== */
        :root{
            --cr-red:#c8202c;
            --cr-red-dark:#9c1720;
            --cr-cream:#fdf6ee;
            --cr-ink:#2a211d;
            --cr-muted:#6b5f58;
            --cr-green:#2e7d4f;
        }
        #landingView{font-family:'Poppins',sans-serif;color:var(--cr-ink);}
        #landingView h1,#landingView h2,#landingView h3{font-family:'Playfair Display',serif;}
        .cr-wrap{max-width:1140px;margin:0 auto;padding:0 24px;}
        #landingView img{max-width:100%;display:block;}

        .cr-back-bar{background:#fff;border-bottom:1px solid rgba(0,0,0,0.06);}
        .cr-back-bar .cr-wrap{padding:12px 24px;}
        .cr-back-link{display:inline-flex;align-items:center;gap:8px;color:var(--cr-muted);font-size:14px;font-weight:600;cursor:pointer;}
        .cr-back-link:hover{color:var(--cr-red);}

        .cr-hero{position:relative;min-height:64vh;display:flex;align-items:center;color:#fff;overflow:hidden;border-radius:0;}
        .cr-hero-bg{position:absolute;inset:0;background-size:cover;background-position:center;}
        .cr-hero-bg::after{content:'';position:absolute;inset:0;background:linear-gradient(180deg,rgba(20,10,8,0.55) 0%,rgba(20,10,8,0.78) 100%);}
        .cr-hero-content{position:relative;z-index:2;padding:70px 24px;max-width:1140px;margin:0 auto;}
        .cr-hero-eyebrow{display:inline-flex;gap:10px;background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.3);padding:6px 16px;border-radius:30px;font-size:13px;font-weight:600;letter-spacing:1px;margin-bottom:22px;}
        .cr-hero h1{font-size:44px;line-height:1.15;max-width:680px;margin-bottom:16px;text-shadow:0 2px 12px rgba(0,0,0,0.3);}
        .cr-hero h1 em{font-style:normal;color:#ffcf8f;}
        .cr-hero p{font-size:17px;max-width:560px;opacity:0.92;margin-bottom:28px;}
        .cr-hero-actions{display:flex;gap:16px;flex-wrap:wrap;}
        .cr-btn-primary{background:var(--cr-red);color:#fff;padding:15px 30px;border-radius:30px;font-weight:600;font-size:15px;display:inline-flex;align-items:center;gap:10px;box-shadow:0 8px 24px rgba(200,32,44,0.4);transition:transform .2s;border:none;cursor:pointer;}
        .cr-btn-primary:hover{transform:translateY(-2px);background:var(--cr-red-dark);color:#fff;}
        .cr-btn-ghost{border:2px solid rgba(255,255,255,0.6);color:#fff;padding:13px 28px;border-radius:30px;font-weight:600;font-size:15px;}
        .cr-btn-ghost:hover{background:rgba(255,255,255,0.12);color:#fff;}
        .cr-hero-info-row{display:flex;gap:28px;margin-top:36px;flex-wrap:wrap;}
        .cr-hero-info-item{display:flex;align-items:center;gap:10px;font-size:14px;opacity:0.95;}
        .cr-hero-info-item i{color:#ffcf8f;font-size:17px;}

        .cr-about{padding:76px 0;background:var(--cr-cream);}
        .cr-about .cr-wrap{display:grid;grid-template-columns:1fr 1fr;gap:54px;align-items:center;}
        .cr-eyebrow{color:var(--cr-red);font-weight:700;letter-spacing:2px;font-size:12px;text-transform:uppercase;margin-bottom:12px;}
        .cr-about h2{font-size:30px;margin-bottom:16px;}
        .cr-about p{color:var(--cr-muted);font-size:15.5px;margin-bottom:14px;}
        .cr-about-badges{display:flex;gap:12px;margin-top:22px;flex-wrap:wrap;}
        .cr-about-badge{display:flex;align-items:center;gap:9px;background:#fff;border:1px solid rgba(0,0,0,0.06);padding:11px 16px;border-radius:14px;box-shadow:0 6px 18px rgba(0,0,0,0.04);font-size:13.5px;font-weight:600;}
        .cr-about-badge i{color:var(--cr-green);}
        .cr-about-photo{border-radius:20px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.15);}

        .cr-menu{padding:76px 0;background:#fff;}
        .cr-menu-head{text-align:center;max-width:620px;margin:0 auto 44px;}
        .cr-menu-head h2{font-size:30px;margin-bottom:12px;}
        .cr-menu-head p{color:var(--cr-muted);font-size:15.5px;}
        .cr-menu-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;}
        .cr-menu-card{background:var(--cr-cream);border-radius:18px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.05);transition:transform .2s;}
        .cr-menu-card:hover{transform:translateY(-6px);}
        .cr-menu-card-img{height:200px;overflow:hidden;}
        .cr-menu-card-img img{width:100%;height:100%;object-fit:cover;}
        .cr-menu-card-body{padding:20px;}
        .cr-menu-card-top{display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:8px;}
        .cr-menu-card-top h3{font-size:18px;}
        .cr-menu-price{color:var(--cr-red);font-weight:700;font-size:17px;white-space:nowrap;font-family:'Poppins',sans-serif;}
        .cr-menu-card-body p{color:var(--cr-muted);font-size:13.5px;margin-bottom:0;}
        .cr-menu-cta{text-align:center;margin-top:36px;}

        .cr-events{padding:76px 0;background:var(--cr-cream);}
        .cr-events .cr-wrap{display:grid;grid-template-columns:1fr 1fr;gap:54px;align-items:center;}
        .cr-events h2{font-size:30px;margin-bottom:16px;}
        .cr-events p{color:var(--cr-muted);font-size:15.5px;margin-bottom:14px;}
        .cr-events-photo{border-radius:20px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.15);}
        .cr-events-list{list-style:none;margin:18px 0 26px;padding:0;display:flex;flex-direction:column;gap:10px;}
        .cr-events-list li{display:flex;align-items:center;gap:10px;font-size:14.5px;color:var(--cr-ink);font-weight:500;}
        .cr-events-list i{color:var(--cr-green);width:18px;}

        .cr-hours{padding:76px 0;background:#fff;}
        .cr-hours .cr-wrap{display:grid;grid-template-columns:1fr 1.2fr;gap:46px;}
        .cr-hours-card{background:#fff;border-radius:20px;padding:32px;box-shadow:0 10px 30px rgba(0,0,0,0.05);}
        .cr-hours-card h3{font-size:20px;margin-bottom:18px;}
        .cr-hours-row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(0,0,0,0.06);font-size:14.5px;}
        .cr-hours-row:last-child{border-bottom:none;}
        .cr-hours-row span:first-child{font-weight:600;}
        .cr-hours-row span:last-child{color:var(--cr-muted);}
        .cr-contact-list{margin-top:22px;display:flex;flex-direction:column;gap:13px;}
        .cr-contact-list a{display:flex;align-items:center;gap:12px;color:var(--cr-ink);font-size:14px;font-weight:500;text-decoration:none;}
        .cr-contact-list i{width:20px;color:var(--cr-red);}
        .cr-map-frame{border-radius:20px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.05);min-height:320px;}
        .cr-map-frame iframe{width:100%;height:100%;min-height:320px;border:0;}

        .cr-cta-band{background:linear-gradient(120deg,var(--cr-red) 0%,var(--cr-red-dark) 100%);color:#fff;padding:60px 0;text-align:center;}
        .cr-cta-band h2{font-size:28px;margin-bottom:12px;color:#fff;}
        .cr-cta-band p{opacity:0.9;max-width:520px;margin:0 auto 26px;}
        .cr-cta-band .cr-btn-primary{background:#fff;color:var(--cr-red);box-shadow:none;}
        .cr-cta-band .cr-btn-primary:hover{background:#fff2e6;color:var(--cr-red-dark);}

        @media (max-width:860px){
            .cr-hero h1{font-size:30px;}
            .cr-hero-content{padding:50px 20px;}
            .cr-about .cr-wrap{grid-template-columns:1fr;}
            .cr-about-photo{order:-1;}
            .cr-menu-grid{grid-template-columns:1fr;}
            .cr-events .cr-wrap{grid-template-columns:1fr;}
            .cr-hours .cr-wrap{grid-template-columns:1fr;}
        }
    </style>
</head>
<body>
    <!-- Header (Marché Central variant, consistent with /home, /categories, /shops, /cart) -->
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Breadcrumb Menu -->
    <div class="breadcrumb-menu">
        <a href="<?= url('/') ?>" class="breadcrumb-btn">
            <i class="fas fa-house"></i>
            <span><?= $t['home'] ?></span>
        </a>
        <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
        <a href="<?= url('shops') ?>" class="breadcrumb-btn">
            <span><?= $t['shops'] ?></span>
        </a>
        <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
        <a href="<?= url('shops/' . ($shop['slug'] ?? '')) ?>" class="breadcrumb-btn active">
            <span><?= htmlspecialchars(substr($shop['name'] ?? 'Shop', 0, 30)) ?></span>
        </a>
    </div>

    <!-- ============== DISPLAY / LANDING VIEW (default) ============== -->
    <div id="landingView">
        <section class="cr-hero">
            <div class="cr-hero-bg" style="background-image:url('<?= url($shop['cover_image'] ?? '') ?>')"></div>
            <div class="cr-hero-content">
                <span class="cr-hero-eyebrow">DÉJEUNER &nbsp;|&nbsp; DÎNER &nbsp;|&nbsp; SOUPER</span>
                <h1><?= $fr ? 'Des matins gourmands, <em>toute la journée</em>, chez Caro Resto' : 'Gourmet mornings, <em>all day long</em>, at Caro Resto' ?></h1>
                <p><?= $fr
                    ? 'Restaurant familial à Pierrefonds. Assiettes généreuses, ingrédients frais et spécialités du jour - sur place, pour emporter, ou livrées chez vous par OCSAPP.'
                    : 'A family restaurant in Pierrefonds. Generous plates, fresh ingredients, daily specials - dine in, takeout, or delivered to you by OCSAPP.' ?></p>
                <div class="cr-hero-actions">
                    <button type="button" class="cr-btn-primary" onclick="showStorefront()"><i class="fas fa-bag-shopping"></i> <?= $fr ? 'Commander en ligne' : 'Order Online' ?></button>
                    <a href="#cr-menu" class="cr-btn-ghost"><?= $fr ? 'Voir le menu' : 'View Menu' ?></a>
                </div>
                <div class="cr-hero-info-row">
                    <div class="cr-hero-info-item"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($shop['address'] ?? '') ?></div>
                    <div class="cr-hero-info-item"><i class="fas fa-clock"></i> <?= $fr ? 'Lun-Ven 6h-15h · Sam-Dim 7h-15h' : 'Mon-Fri 6am-3pm · Sat-Sun 7am-3pm' ?></div>
                    <div class="cr-hero-info-item"><i class="fas fa-phone"></i> <?= htmlspecialchars($shop['phone'] ?? '') ?></div>
                </div>
            </div>
        </section>

        <section class="cr-about">
            <div class="cr-wrap">
                <div>
                    <div class="cr-eyebrow"><?= $fr ? 'NOTRE HISTOIRE' : 'OUR STORY' ?></div>
                    <h2><?= $fr ? 'Une expérience culinaire authentique' : 'An authentic culinary experience' ?></h2>
                    <p><?= $fr
                        ? 'Chez Caro Resto, on mise sur des plats copieux et réconfortants, sucrés comme salés, préparés avec des ingrédients de qualité et servis à prix honnête. Nos spécialités du jour changent régulièrement - il y a toujours une raison de revenir.'
                        : 'At Caro Resto, we focus on hearty, comforting dishes, sweet and savoury, made with quality ingredients at honest prices. Our daily specials rotate regularly - there is always a reason to come back.' ?></p>
                    <p><?= $fr
                        ? 'Que ce soit pour un déjeuner en famille, un dîner rapide entre collègues ou un souper convivial, notre équipe vous accueille dans une ambiance chaleureuse, sur place ou pour emporter - et maintenant, livré chez vous via OCSAPP.'
                        : 'Whether it is a family breakfast, a quick lunch with coworkers, or a relaxed dinner, our team welcomes you in a warm atmosphere, dine-in or takeout - and now, delivered to your door via OCSAPP.' ?></p>
                    <div class="cr-about-badges">
                        <div class="cr-about-badge"><i class="fas fa-leaf"></i> <?= $fr ? 'Ingrédients frais' : 'Fresh ingredients' ?></div>
                        <div class="cr-about-badge"><i class="fas fa-utensils"></i> <?= $fr ? 'Fait maison' : 'Homemade' ?></div>
                        <div class="cr-about-badge"><i class="fas fa-truck"></i> <?= $fr ? 'Livraison OCSAPP' : 'OCSAPP delivery' ?></div>
                    </div>
                </div>
                <div class="cr-about-photo">
                    <img src="<?= url($shop['cover_image'] ?? '') ?>" alt="<?= htmlspecialchars($shop['name'] ?? '') ?>">
                </div>
            </div>
        </section>

        <section class="cr-menu" id="cr-menu">
            <div class="cr-wrap">
                <div class="cr-menu-head">
                    <div class="cr-eyebrow"><?= $fr ? 'NOS SPÉCIALITÉS' : 'OUR SPECIALTIES' ?></div>
                    <h2><?= $fr ? 'Quelques favoris de la maison' : 'A few house favourites' ?></h2>
                    <p><?= $fr
                        ? 'Un aperçu de nos assiettes les plus populaires. Commandez directement sur OCSAPP pour le menu complet.'
                        : 'A preview of our most popular plates. Order directly on OCSAPP for the full menu.' ?></p>
                </div>
                <div class="cr-menu-grid">
                    <?php foreach ($products as $product): ?>
                        <article class="cr-menu-card">
                            <div class="cr-menu-card-img">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <img src="<?= asset('images/placeholder.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php endif; ?>
                            </div>
                            <div class="cr-menu-card-body">
                                <div class="cr-menu-card-top">
                                    <h3><?= htmlspecialchars($product['name']) ?></h3>
                                    <span class="cr-menu-price"><?= currency($product['price'] ?? $product['base_price']) ?></span>
                                </div>
                                <?php if (!empty($product['short_description'])): ?>
                                    <p><?= htmlspecialchars($product['short_description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                <div class="cr-menu-cta">
                    <button type="button" class="cr-btn-primary" onclick="showStorefront()"><i class="fas fa-bag-shopping"></i> <?= $fr ? 'Commander en ligne' : 'Order Online' ?></button>
                </div>
            </div>
        </section>

        <section class="cr-events" id="cr-events">
            <div class="cr-wrap">
                <div class="cr-events-photo">
                    <img src="<?= url('uploads/shops/covers/caroresto-demo-event-hall.jpg') ?>" alt="<?= $fr ? 'Salle de réception Caro Resto' : 'Caro Resto event hall' ?>">
                </div>
                <div>
                    <div class="cr-eyebrow"><?= $fr ? 'SERVICES' : 'SERVICES' ?></div>
                    <h2><?= $fr ? 'Louez notre salle pour votre prochain événement' : 'Rent our hall for your next event' ?></h2>
                    <p><?= $fr
                        ? 'Mariages, anniversaires ou célébrations privées - Caro Resto met sa salle à votre disposition dans une ambiance conviviale et chaleureuse, pour les réceptions intimes comme pour les grands événements.'
                        : 'Weddings, birthdays, or private celebrations - Caro Resto makes its hall available in a warm, welcoming atmosphere, for intimate gatherings as well as larger events.' ?></p>
                    <ul class="cr-events-list">
                        <li><i class="fas fa-check"></i> <?= $fr ? 'Forfaits flexibles, adaptables à vos besoins et votre budget' : 'Flexible packages, tailored to your needs and budget' ?></li>
                        <li><i class="fas fa-check"></i> <?= $fr ? 'Décoration sur demande' : 'Decoration on request' ?></li>
                        <li><i class="fas fa-check"></i> <?= $fr ? 'Service traiteur' : 'Catering service' ?></li>
                        <li><i class="fas fa-check"></i> <?= $fr ? 'Équipement audiovisuel disponible' : 'Audiovisual equipment available' ?></li>
                    </ul>
                    <button type="button" class="cr-btn-primary" onclick="openShopModal('contactModal')"><i class="fas fa-calendar-check"></i> <?= $fr ? 'Faire une demande' : 'Request a Booking' ?></button>
                </div>
            </div>
        </section>

        <section class="cr-hours">
            <div class="cr-wrap">
                <div class="cr-hours-card">
                    <h3><i class="fas fa-clock" style="color:var(--cr-red);margin-right:8px;"></i><?= $fr ? "Horaire d'ouverture" : 'Opening Hours' ?></h3>
                    <div class="cr-hours-row"><span><?= $fr ? 'Lundi - Vendredi' : 'Monday - Friday' ?></span><span>6h00 - 15h00</span></div>
                    <div class="cr-hours-row"><span><?= $fr ? 'Samedi - Dimanche' : 'Saturday - Sunday' ?></span><span>7h00 - 15h00</span></div>
                    <div class="cr-contact-list">
                        <a href="tel:<?= htmlspecialchars($shop['phone'] ?? '') ?>"><i class="fas fa-phone"></i> <?= htmlspecialchars($shop['phone'] ?? '') ?></a>
                        <a href="mailto:<?= htmlspecialchars($shop['email'] ?? '') ?>"><i class="fas fa-envelope"></i> <?= htmlspecialchars($shop['email'] ?? '') ?></a>
                        <a href="https://maps.google.com/?q=<?= urlencode($shop['address'] ?? '') ?>" target="_blank" rel="noopener"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($shop['address'] ?? '') ?></a>
                    </div>
                </div>
                <div class="cr-map-frame">
                    <iframe src="https://www.google.com/maps?q=<?= urlencode($shop['address'] ?? '') ?>&output=embed" loading="lazy" allowfullscreen></iframe>
                </div>
            </div>
        </section>

        <section class="cr-cta-band">
            <div class="cr-wrap">
                <h2><?= $fr ? 'Envie de déguster? Commandez en 2 minutes.' : 'Craving something good? Order in 2 minutes.' ?></h2>
                <p><?= $fr
                    ? 'Livraison locale zéro émission ou ramassage sur place - suivez votre commande en temps réel, directement sur OCSAPP.'
                    : 'Zero-emission local delivery or pickup - track your order in real time, right here on OCSAPP.' ?></p>
                <button type="button" class="cr-btn-primary" onclick="showStorefront()"><i class="fas fa-bag-shopping"></i> <?= $fr ? 'Commander maintenant' : 'Order Now' ?></button>
            </div>
        </section>
    </div>

    <!-- ============== STOREFRONT VIEW (hidden until "Order Online" is clicked) ============== -->
    <div id="storefrontView" style="display:none;">
        <div class="cr-back-bar">
            <div class="cr-wrap">
                <span class="cr-back-link" onclick="showLanding()"><i class="fas fa-arrow-left"></i> <?= $fr ? 'Retour à la page du restaurant' : 'Back to restaurant page' ?></span>
            </div>
        </div>

        <div class="shop-layout">
            <!-- Sidebar -->
            <aside class="shop-sidebar">
                <!-- Shop Info -->
                <div class="shop-info">
                    <div class="shop-logo-large">
                        <?php if (!empty($shop['logo'])): ?>
                            <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                        <?php else: ?>
                            <i class="fas fa-store" style="font-size: 48px; color: #ccc;"></i>
                        <?php endif; ?>
                    </div>
                    <div class="shop-name-large"><?= htmlspecialchars($shop['name'] ?? 'Shop Name') ?></div>
                    <?php if (!empty($shop['founding_partner'])): ?>
                        <div style="text-align:center;margin-bottom:8px;">
                            <span style="display:inline-flex;align-items:center;gap:5px;background:#fef3c722;color:#b45309;font-size:12px;font-weight:600;padding:5px 12px;border-radius:20px;border:1px solid #fbbf2455;">
                                <i class="fas fa-star"></i> <?= $fr ? 'Partenaire Fondateur' : 'Founding Partner' ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="shop-location-text">
                        <i class="fas fa-location-dot" style="color:#999;margin-right:4px;"></i>
                        <?= htmlspecialchars(explode(',', $shop['address'] ?? ($fr ? 'Emplacement non precise' : 'Location not provided'))[0]) ?>
                    </div>
                    <div class="shop-trust-row">
                        <div class="shop-rating-large">
                            <span class="stars"><i class="fas fa-star"></i></span>
                            <span><?= number_format($shop['average_rating'] ?? 4.8, 1) ?></span>
                            <?php if (!empty($shop['reviews_count'])): ?>
                                <span class="reviews-count">(<?= $shop['reviews_count'] ?>)</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($shop['packaging_time'])): ?>
                            <div class="shop-delivery-badge">
                                <i class="fas fa-clock"></i>
                                <?= htmlspecialchars($shop['packaging_time']) ?> min
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="view-info-btn" onclick="showShopInfoModal()">
                        <?= $t['view_information'] ?? 'View Information' ?> >
                    </button>
                </div>

                <!-- Sidebar Menu -->
                <div class="sidebar-menu">
                    <span class="sidebar-menu-item" style="cursor:pointer;" onclick="showLanding()">
                        <i class="fas fa-house"></i>
                        <span><?= $fr ? 'Page du restaurant' : 'Restaurant Page' ?></span>
                    </span>
                    <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('feedbackModal');">
                        <i class="fas fa-comment-dots"></i>
                        <span><?= $fr ? 'Laisser un avis' : 'Leave a Review' ?></span>
                    </a>
                    <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('contactModal');">
                        <i class="fas fa-envelope"></i>
                        <span><?= $fr ? 'Contacter la boutique' : 'Contact Shop' ?></span>
                    </a>
                    <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('policyModal');">
                        <i class="fas fa-file-lines"></i>
                        <span><?= $fr ? 'Politiques' : 'Shop Policy' ?></span>
                    </a>
                    <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('reportModal');">
                        <i class="fas fa-flag"></i>
                        <span><?= $fr ? 'Signaler' : 'Report' ?></span>
                    </a>
                </div>

                <!-- Categories -->
                <?php if (!empty($categories)): ?>
                <div class="categories-section">
                    <div class="categories-title"><?= $t['categories'] ?></div>
                    <?php foreach (array_slice($categories, 0, 8) as $category): ?>
                        <div class="category-item">
                            <input type="checkbox" id="cat-<?= $category['id'] ?>"
                                   onchange="filterByCategory(<?= $category['id'] ?>)">
                            <label for="cat-<?= $category['id'] ?>">
                                <?= htmlspecialchars($category['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </aside>

            <!-- Main Content -->
            <main class="shop-main">
                <!-- Shop Banner -->
                <div class="shop-banner">
                    <?php if (!empty($shop['cover_image'])): ?>
                        <img src="<?= url($shop['cover_image']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                    <?php else: ?>
                        <div class="shop-banner-icon-fallback"><i class="fas fa-store"></i></div>
                    <?php endif; ?>
                    <div class="shop-banner-overlay">
                        <div class="shop-banner-name">
                            <?= htmlspecialchars($shop['name'] ?? 'Shop') ?>
                            <?php if (!empty($shop['founding_partner'])): ?>
                                <span class="shop-founding-pill">
                                    <i class="fas fa-star"></i> <?= $fr ? 'Fondateur' : 'Founding' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($shop['description'])): ?>
                            <div class="shop-banner-tagline"><?= htmlspecialchars(mb_strimwidth($shop['description'], 0, 140, '…')) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Products Header -->
                <div class="products-header">
                    <div class="products-header-info">
                        <div class="products-title"><?= htmlspecialchars($shop['name'] ?? 'Shop') ?></div>
                        <div class="products-count"><?= count($products) ?> <?= $t['items'] ?? 'items' ?></div>
                    </div>
                    <div class="sort-dropdown">
                        <select class="sort-select" onchange="window.location.href=this.value">
                            <option value="?sort=popularity" <?= $sortBy === 'popularity' ? 'selected' : '' ?>>
                                <?= $t['sort_popularity'] ?? 'Sort by Popularity' ?>
                            </option>
                            <option value="?sort=price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>
                                <?= $t['sort_price_low'] ?? 'Price: Low to High' ?>
                            </option>
                            <option value="?sort=price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>
                                <?= $t['sort_price_high'] ?? 'Price: High to Low' ?>
                            </option>
                            <option value="?sort=newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>
                                <?= $t['sort_newest'] ?? 'Newest' ?>
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <?php if (!empty($products)): ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product):
                            $price = $product['is_on_sale'] ?? false ? ($product['sale_price'] ?? $product['base_price']) : $product['base_price'];
                            $stock = $product['stock_quantity'] ?? 0;

                            // Calculate discount
                            $discount = 0;
                            if (!empty($product['is_on_sale']) && !empty($product['base_price']) && $product['base_price'] > $price) {
                                $discount = round((($product['base_price'] - $price) / $product['base_price']) * 100);
                            } elseif (!empty($product['compare_at_price']) && $product['compare_at_price'] > $price) {
                                $discount = round((($product['compare_at_price'] - $price) / $product['compare_at_price']) * 100);
                            }

                            // Parse tags
                            $productTags = $product['tags'] ?? [];
                            if (is_string($productTags)) {
                                $productTags = json_decode($productTags, true) ?: [];
                            }
                        ?>
                            <article class="product-card" data-price="<?= $price ?>" data-category="<?= htmlspecialchars($product['category_name'] ?? '') ?>">
                                <!-- Product Badges -->
                                <div class="product-badges">
                                    <?php if ($discount > 0): ?>
                                        <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $discount ?>%</div>
                                    <?php endif; ?>

                                    <?php if (!empty($product['is_featured'])): ?>
                                        <div class="product-badge featured">⭐ <?= $t['featured'] ?? 'Featured' ?></div>
                                    <?php endif; ?>

                                    <?php
                                    // Tag badges
                                    $tagBadges = [
                                        'organic' => ['label' => $t['organic'] ?? 'Organic', 'class' => 'organic'],
                                        'bestseller' => ['label' => $t['bestseller'] ?? 'Best Seller', 'class' => 'bestseller'],
                                        'new-arrival' => ['label' => $t['new'] ?? 'New', 'class' => 'new'],
                                        'premium' => ['label' => $t['premium'] ?? 'Premium', 'class' => 'premium'],
                                    ];
                                    foreach ($productTags as $tag):
                                        $tagSlug = is_array($tag) ? ($tag['slug'] ?? '') : $tag;
                                        if (isset($tagBadges[$tagSlug])):
                                    ?>
                                        <div class="product-badge <?= $tagBadges[$tagSlug]['class'] ?>"><?= $tagBadges[$tagSlug]['label'] ?></div>
                                    <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </div>

                                <!-- Wishlist Button -->
                                <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="<?= $t['add_to_wishlist'] ?? 'Add to wishlist' ?>">
                                    <i class="far fa-heart"></i>
                                </button>

                                <!-- Product Image -->
                                <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="product-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?= url($product['image']) ?>"
                                             alt="<?= htmlspecialchars($product['name']) ?>"
                                             loading="lazy">
                                    <?php else: ?>
                                        <img src="<?= asset('images/placeholder.svg') ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                    <?php endif; ?>
                                </a>

                                <div class="product-info">
                                    <!-- Brand/Category -->
                                    <?php if (!empty($product['brand_name'])): ?>
                                        <div class="product-category"><?= htmlspecialchars($product['brand_name']) ?></div>
                                    <?php elseif (!empty($product['category_name'])): ?>
                                        <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                                    <?php endif; ?>

                                    <!-- Product Name -->
                                    <h3 class="product-name">
                                        <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </a>
                                    </h3>

                                    <!-- Star Rating -->
                                    <div class="product-rating">
                                        <?php $rating = $product['average_rating'] ?? 0; ?>
                                        <span class="stars">
                                            <?php for($i = 1; $i <= 5; $i++): ?>
                                                <?php if ($i <= floor($rating)): ?>
                                                    <i class="fas fa-star"></i>
                                                <?php elseif ($i - 0.5 <= $rating): ?>
                                                    <i class="fas fa-star-half-alt"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </span>
                                        <?php if ($rating > 0): ?>
                                            <span class="rating-number"><?= number_format($rating, 1) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Price -->
                                    <div class="product-price">
                                        <?= currency($price) ?>
                                        <?php if (!empty($product['is_on_sale']) && !empty($product['base_price'])): ?>
                                            <span class="old-price"><?= currency($product['base_price']) ?></span>
                                        <?php elseif (!empty($product['compare_at_price']) && $product['compare_at_price'] > $price): ?>
                                            <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Stock Status -->
                                    <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                                        <?php if ($stock > 10): ?>
                                            <i class="fas fa-check-circle"></i>
                                            <?= $t['in_stock'] ?? 'In Stock' ?>
                                        <?php elseif ($stock > 0): ?>
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <?= sprintf($t['low_stock'] ?? 'Only %d left', $stock) ?>
                                        <?php else: ?>
                                            <i class="fas fa-times-circle"></i>
                                            <?= $t['out_of_stock'] ?? 'Out of Stock' ?>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Add to Cart Button -->
                                    <button class="add-to-cart"
                                            data-product-id="<?= $product['id'] ?>"
                                            <?= $stock <= 0 ? 'disabled' : '' ?>
                                            aria-label="<?= $t['add_to_cart'] ?>">
                                        <i class="fas fa-shopping-cart"></i>
                                        <?= $t['add_to_cart'] ?>
                                    </button>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div style="font-size: 64px; margin-bottom: 20px; color: #ddd;"><i class="fas fa-box-open"></i></div>
                        <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_products'] ?></h2>
                        <p style="color: #999;"><?= $t['shop_no_products_desc'] ?></p>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Footer (Marché Central variant, matches /home and /shops) -->
    <footer class="mc-footer">
      <div class="mc-footer-wrap">
        <div class="mc-footer-top">
          <div class="mc-footer-brand-col">
            <div class="mc-footer-brand">
              <img alt="Logo OCSAPP" src="<?= asset('images/logo.png') ?>">
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

    <?php include __DIR__ . '/../components/auth-popup.php'; ?>

    <!-- Shop Information Modal -->
    <div class="modal-overlay" id="shopInfoModal" onclick="closeShopInfoModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h2 class="modal-title"><?= htmlspecialchars($shop['name'] ?? 'Shop Information') ?></h2>
                <button class="modal-close" onclick="closeShopInfoModal()">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Address -->
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['address'] ?? 'Address' ?></div>
                    <div class="shop-info-value">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($shop['address'] ?? 'No address provided') ?>
                    </div>
                </div>

                <!-- Phone -->
                <?php if (!empty($shop['phone'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['phone'] ?? 'Phone' ?></div>
                    <div class="shop-info-value">
                        <i class="fas fa-phone"></i>
                        <a href="tel:<?= htmlspecialchars($shop['phone']) ?>" style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($shop['phone']) ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Email -->
                <?php if (!empty($shop['email'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['email'] ?? 'Email' ?></div>
                    <div class="shop-info-value">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:<?= htmlspecialchars($shop['email']) ?>" style="color: #333; text-decoration: none;">
                            <?= htmlspecialchars($shop['email']) ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Description -->
                <?php if (!empty($shop['description'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['about'] ?? 'About' ?></div>
                    <div class="shop-info-value">
                        <?= nl2br(htmlspecialchars($shop['description'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Shop Hours -->
                <?php if (!empty($shopHours) && is_array($shopHours)): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['shop_hours'] ?? 'Shop Hours' ?></div>
                    <table class="shop-hours-table">
                        <?php
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($shopHours as $hours):
                            $dayName = $days[$hours['day_of_week']] ?? 'Day ' . $hours['day_of_week'];
                        ?>
                        <tr>
                            <td><?= $dayName ?></td>
                            <td>
                                <?php if ($hours['is_closed']): ?>
                                    <span style="color: #999;"><?= $t['closed'] ?? 'Closed' ?></span>
                                <?php else: ?>
                                    <?= date('g:i A', strtotime($hours['opens_at'])) ?> - <?= date('g:i A', strtotime($hours['closes_at'])) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Delivery Info -->
                <?php if (!empty($shop['packaging_time'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['delivery_time'] ?? 'Delivery Time' ?></div>
                    <div class="shop-info-value">
                        <i class="fas fa-clock"></i>
                        <?= htmlspecialchars($shop['packaging_time']) ?> <?= $t['minutes'] ?? 'minutes' ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Rating -->
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $t['rating'] ?? 'Rating' ?></div>
                    <div class="shop-info-value">
                        <i class="fas fa-star" style="color: #ffc107;"></i>
                        <?= number_format($shop['average_rating'] ?? 0, 1) ?> / 5.0
                        <?php if (!empty($shop['reviews_count'])): ?>
                            <span style="color: #999;">(<?= $shop['reviews_count'] ?> <?= $t['reviews'] ?? 'reviews' ?>)</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.OCSAPP_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            urls: {
                cartAdd: '<?= url('cart/add') ?>',
                cartCount: '<?= url('cart/count') ?>'
            }
        };

        // Store all products data with their categories
        const allProducts = <?= json_encode(array_map(function($p) {
            return [
                'id' => $p['id'],
                'category_name' => $p['category_name'] ?? ''
            ];
        }, $products ?? [])) ?>;

        function filterByCategory(categoryId) {
            const checkboxes = document.querySelectorAll('.category-item input[type="checkbox"]');
            const selectedCategories = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const label = checkbox.nextElementSibling;
                    if (label) {
                        selectedCategories.push(label.textContent.trim());
                    }
                }
            });

            const productCards = document.querySelectorAll('.product-card');

            if (selectedCategories.length === 0) {
                productCards.forEach(card => {
                    card.style.display = '';
                });
                return;
            }

            productCards.forEach(card => {
                const categoryDiv = card.querySelector('.product-category');
                const productCategory = categoryDiv ? categoryDiv.textContent.trim() : '';

                if (selectedCategories.some(cat => productCategory.toLowerCase().includes(cat.toLowerCase()))) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            const visibleProducts = Array.from(productCards).filter(card => card.style.display !== 'none').length;
            const countSpan = document.querySelector('.products-count');
            if (countSpan) {
                countSpan.textContent = `${visibleProducts} <?= $t['items'] ?? 'items' ?>`;
            }
        }

        // Shop Info Modal Functions
        function showShopInfoModal() {
            const modal = document.getElementById('shopInfoModal');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeShopInfoModal(event) {
            if (!event || event.target.id === 'shopInfoModal' || event.target.classList.contains('modal-close')) {
                const modal = document.getElementById('shopInfoModal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeShopInfoModal({ target: { id: 'shopInfoModal' } });
            }
        });

        // ===== Landing <-> Storefront view toggle (same page, same URL) =====
        function showStorefront() {
            document.getElementById('landingView').style.display = 'none';
            document.getElementById('storefrontView').style.display = 'block';
            if (history.pushState) { history.pushState(null, '', '#order'); }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        function showLanding() {
            document.getElementById('storefrontView').style.display = 'none';
            document.getElementById('landingView').style.display = 'block';
            if (history.pushState) { history.pushState(null, '', location.pathname); }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        window.addEventListener('DOMContentLoaded', function() {
            if (location.hash === '#order') { showStorefront(); }
        });
        window.addEventListener('popstate', function() {
            if (location.hash === '#order') { showStorefront(); } else { showLanding(); }
        });
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>

<!-- Feedback Modal -->
<div class="modal-overlay" id="feedbackModal" onclick="closeShopModalOverlay(event, 'feedbackModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 class="modal-title"><?= $fr ? 'Laisser un avis' : 'Leave a Review' ?></h2>
            <button class="modal-close" onclick="closeShopModal('feedbackModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (!isLoggedIn()): ?>
                <p style="color:#666;text-align:center;padding:20px 0">
                    <?= $fr ? 'Veuillez vous connecter pour laisser un avis.' : 'Please log in to leave a review.' ?>
                    <br><br>
                    <a href="<?= url('login') ?>" style="background:#00b207;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600">
                        <?= $fr ? 'Se connecter' : 'Log In' ?>
                    </a>
                </p>
            <?php else: ?>
                <form id="feedbackForm" onsubmit="submitShopReview(event)">
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $fr ? 'Note' : 'Rating' ?> *</div>
                        <div id="starRating" style="display:flex;gap:8px;font-size:32px;cursor:pointer;margin:8px 0">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star-btn" data-value="<?= $i ?>" onclick="setRating(<?= $i ?>)" style="color:#ddd;transition:color 0.15s">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="feedbackRating" name="rating" value="0">
                    </div>
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $fr ? 'Commentaire' : 'Comment' ?></div>
                        <textarea name="comment" rows="4" placeholder="<?= $fr ? 'Partagez votre experience...' : 'Share your experience...' ?>"
                                  style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                    </div>
                    <div id="feedbackMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                    <button type="submit" style="background:#00b207;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                        <?= $fr ? 'Soumettre' : 'Submit Review' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div class="modal-overlay" id="contactModal" onclick="closeShopModalOverlay(event, 'contactModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 class="modal-title"><?= $fr ? 'Contacter la boutique' : 'Contact Shop' ?></h2>
            <button class="modal-close" onclick="closeShopModal('contactModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="contactForm" onsubmit="submitShopContact(event)">
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $fr ? 'Votre nom' : 'Your Name' ?> *</div>
                    <input type="text" name="name" value="<?= htmlspecialchars(($_SESSION['user_name'] ?? '')) ?>"
                           placeholder="<?= $fr ? 'Nom complet' : 'Full name' ?>"
                           required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $fr ? 'Votre courriel' : 'Your Email' ?> *</div>
                    <input type="email" name="email" value="<?= htmlspecialchars(($_SESSION['user_email'] ?? '')) ?>"
                           placeholder="courriel@exemple.com"
                           required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $fr ? 'Message' : 'Message' ?> *</div>
                    <textarea name="message" rows="4" required
                              placeholder="<?= $fr ? 'Votre message...' : 'Your message...' ?>"
                              style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                </div>
                <div id="contactMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                <button type="submit" style="background:#00b207;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                    <?= $fr ? 'Envoyer' : 'Send Message' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Policy Modal -->
<div class="modal-overlay" id="policyModal" onclick="closeShopModalOverlay(event, 'policyModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 class="modal-title"><?= $fr ? 'Politiques de la boutique' : 'Shop Policies' ?></h2>
            <button class="modal-close" onclick="closeShopModal('policyModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php
            $policies = $shopPolicies ?? [];
            $hasAny = !empty($policies['return_policy']) || !empty($policies['shipping_policy']) || !empty($policies['privacy_policy']) || !empty($policies['terms_of_service']);
            ?>
            <?php if ($hasAny): ?>
                <?php if (!empty($policies['return_policy'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-undo" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Politique de retour' : 'Return Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['return_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['shipping_policy'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-truck" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Politique de livraison' : 'Shipping Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['shipping_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['privacy_policy'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-shield-alt" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Confidentialite' : 'Privacy Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['privacy_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['terms_of_service'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-file-contract" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Conditions d\'utilisation' : 'Terms of Service' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['terms_of_service']) ?></div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="color:#666;margin-bottom:20px">
                    <?= $fr
                        ? 'Cette boutique n\'a pas encore publie de politiques specifiques. Les politiques generales OCSAPP s\'appliquent.'
                        : 'This shop has not published specific policies yet. OCSAPP general marketplace policies apply.' ?>
                </p>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-undo" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Retours' : 'Returns' ?></div>
                    <div class="shop-info-value"><?= $fr
                        ? 'Les retours sont acceptes dans les 14 jours suivant la reception de la commande, sous reserve que les articles soient en etat non utilise et dans leur emballage d\'origine.'
                        : 'Returns are accepted within 14 days of receiving your order, provided items are unused and in original packaging.' ?></div>
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-truck" style="color:#00b207;margin-right:6px"></i><?= $fr ? 'Livraison' : 'Shipping' ?></div>
                    <div class="shop-info-value"><?= $fr
                        ? 'Les delais de livraison varient selon la zone. Consultez la page de commande pour les estimations en temps reel.'
                        : 'Delivery times vary by zone. See your order page for real-time estimates.' ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal-overlay" id="reportModal" onclick="closeShopModalOverlay(event, 'reportModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header" style="background:linear-gradient(135deg,#e53e3e 0%,#c53030 100%)">
            <h2 class="modal-title"><?= $fr ? 'Signaler cette boutique' : 'Report This Shop' ?></h2>
            <button class="modal-close" onclick="closeShopModal('reportModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (!isLoggedIn()): ?>
                <p style="color:#666;text-align:center;padding:20px 0">
                    <?= $fr ? 'Veuillez vous connecter pour signaler une boutique.' : 'Please log in to report a shop.' ?>
                    <br><br>
                    <a href="<?= url('login') ?>" style="background:#00b207;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600">
                        <?= $fr ? 'Se connecter' : 'Log In' ?>
                    </a>
                </p>
            <?php else: ?>
                <form id="reportForm" onsubmit="submitShopReport(event)">
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $fr ? 'Raison' : 'Reason' ?> *</div>
                        <select name="reason" required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                            <option value=""><?= $fr ? '-- Choisir une raison --' : '-- Select a reason --' ?></option>
                            <option value="spam"><?= $fr ? 'Pourriels / contenu non sollicite' : 'Spam / unsolicited content' ?></option>
                            <option value="counterfeit"><?= $fr ? 'Produits contrefaits ou frauduleux' : 'Counterfeit or fraudulent products' ?></option>
                            <option value="inappropriate"><?= $fr ? 'Contenu inapproprie' : 'Inappropriate content' ?></option>
                            <option value="other"><?= $fr ? 'Autre' : 'Other' ?></option>
                        </select>
                    </div>
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $fr ? 'Description (optionnel)' : 'Description (optional)' ?></div>
                        <textarea name="description" rows="3"
                                  placeholder="<?= $fr ? 'Decrivez le probleme...' : 'Describe the issue...' ?>"
                                  style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                    </div>
                    <div id="reportMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                    <button type="submit" style="background:#e53e3e;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                        <?= $fr ? 'Soumettre le signalement' : 'Submit Report' ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    const SHOP_SLUG = '<?= addslashes($shop['slug'] ?? '') ?>';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    function openShopModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    function closeShopModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }
    function closeShopModalOverlay(e, id) {
        if (e.target.id === id) closeShopModal(id);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            ['feedbackModal','contactModal','policyModal','reportModal'].forEach(closeShopModal);
            closeShopInfoModal({ target: { id: 'shopInfoModal' } });
        }
    });

    // Star rating
    function setRating(val) {
        document.getElementById('feedbackRating').value = val;
        document.querySelectorAll('.star-btn').forEach(function(s) {
            s.style.color = parseInt(s.dataset.value) <= val ? '#ffc107' : '#ddd';
        });
    }

    function showMsg(id, text, ok) {
        var el = document.getElementById(id);
        el.textContent = text;
        el.style.display = 'block';
        el.style.background = ok ? '#d4edda' : '#f8d7da';
        el.style.color = ok ? '#155724' : '#721c24';
    }

    async function shopPost(path, data) {
        const params = new URLSearchParams(data);
        params.append('_csrf_token', CSRF_TOKEN);
        const resp = await fetch(path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-Token': CSRF_TOKEN },
            body: params.toString()
        });
        return resp.json();
    }

    async function submitShopReview(e) {
        e.preventDefault();
        const form = e.target;
        const rating = parseInt(document.getElementById('feedbackRating').value);
        if (!rating) {
            showMsg('feedbackMsg', '<?= $fr ? 'Veuillez selectionner une note.' : 'Please select a rating.' ?>', false);
            return;
        }
        const btn = form.querySelector('button[type=submit]');
        btn.disabled = true;
        const res = await shopPost('<?= url('shops/' . ($shop['slug'] ?? '') . '/review') ?>', {
            rating: rating,
            comment: form.comment.value
        });
        showMsg('feedbackMsg', res.message, res.success);
        if (res.success) form.reset(), setRating(0);
        btn.disabled = false;
    }

    async function submitShopContact(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type=submit]');
        btn.disabled = true;
        const res = await shopPost('<?= url('shops/' . ($shop['slug'] ?? '') . '/contact') ?>', {
            name: form.name.value,
            email: form.email.value,
            message: form.message.value
        });
        showMsg('contactMsg', res.message, res.success);
        if (res.success) form.reset();
        btn.disabled = false;
    }

    async function submitShopReport(e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type=submit]');
        btn.disabled = true;
        const res = await shopPost('<?= url('shops/' . ($shop['slug'] ?? '') . '/report') ?>', {
            reason: form.reason.value,
            description: form.description?.value || ''
        });
        showMsg('reportMsg', res.message, res.success);
        if (res.success) setTimeout(function() { closeShopModal('reportModal'); }, 2000);
        btn.disabled = false;
    }
</script>

<!-- Auth Popup for Guests -->
<?php include __DIR__ . "/../components/auth-popup.php"; ?>
</body>
</html>
