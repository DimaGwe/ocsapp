<?php
/**
 * OCS Shops Page - With Virtual Mall Categories (FIXED COUNTS)
 * Shows all available shops/stores organized by type
 * Updated: November 10, 2025
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$shops = $shops ?? [];

// User's delivery location - match header behavior
$defaultLocationText = $t['select_location'] ?? ($currentLang === 'fr' ? 'Choisir votre emplacement' : 'Select your location');
$currentLocation = $_SESSION['location'] ?? $defaultLocationText;

$cartCount = $cartCount ?? 0;
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 12;
$search = $search ?? '';
$filterType = $_GET['type'] ?? 'all'; // Filter by shop type

// FIXED: Get counts from database for all types
$shopCounts = $shopCounts ?? [
    'all' => 0,
    'grocery' => 0,
    'food_court' => 0,
    'stores' => 0,
    'products' => 0
];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['shops_page_title'] ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <style>
        .container {
            max-width: 1400px;
            margin: 20px auto 40px;
            padding: 0 5%;
        }
        
        .page-header { 
            background: linear-gradient(135deg, #00b207 0%, #009206 100%); 
            color: white; 
            padding: 40px; 
            border-radius: 16px; 
            margin-bottom: 30px; 
            text-align: center;
        }
        .page-title { 
            font-size: 36px; 
            font-weight: 700; 
            margin-bottom: 10px; 
        }
        .page-subtitle { 
            font-size: 16px; 
            opacity: 0.9;
            margin-bottom: 20px;
        }
        
        .location-info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255,255,255,0.2);
            padding: 15px 20px;
            border-radius: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .location-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 600;
        }
        .change-location-btn {
            background: white;
            color: #00b207;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .change-location-btn:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }
        
        /* Shop Type Filter Tabs */
        .shop-type-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .shop-type-tab {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: white;
            border: 2px solid #e6e6e6;
            border-radius: 12px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .shop-type-tab:hover {
            border-color: #00b207;
            background: #f8fdf8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 178, 7, 0.15);
        }
        
        .shop-type-tab.active {
            background: #00b207;
            border-color: #00b207;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
        }
        
        .shop-type-icon {
            font-size: 24px;
        }
        
        .shop-type-label {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }
        
        .shop-type-name {
            font-weight: 600;
            font-size: 15px;
        }
        
        .shop-type-count {
            font-size: 12px;
            opacity: 0.8;
        }
        
        .shop-type-tab.active .shop-type-count {
            opacity: 1;
        }
        
        .shops-info-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .shop-count {
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }
        
        .shops-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }
        
        .shop-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            z-index: 1;
        }

        .shop-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid #00b207;
            border-radius: 16px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .shop-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            z-index: 10;
        }

        .shop-card:hover::after {
            opacity: 1;
        }
        
        /* Shop Type Badge on Card */
        .shop-type-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            padding: 4px 10px;
            background: #00b207;
            color: white;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .shop-type-badge.grocery { background: #4caf50; }
        .shop-type-badge.food_court { background: #ff9800; }
        .shop-type-badge.stores { background: #2196f3; }
        .shop-type-badge.products { background: #9c27b0; }
        
        .shop-logo {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            background: #f7f7f7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 3px solid #e6e6e6;
        }
        .shop-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .shop-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        
        .shop-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 40px;
        }
        
        .shop-location {
            color: #666;
            font-size: 14px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        
        .shop-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 15px;
        }
        .shop-rating .stars {
            color: #ffc107;
            font-size: 16px;
        }
        .shop-rating-value {
            font-weight: 600;
            color: #333;
        }
        
        .shop-meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            font-size: 13px;
            color: #666;
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #e6e6e6;
            width: 100%;
        }
        .shop-meta-item {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .pagination a {
            padding: 10px 15px;
            background: white;
            border: 1px solid #e6e6e6;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: all 0.2s;
        }
        .pagination a:hover {
            background: #00b207;
            color: white;
            border-color: #00b207;
        }
        .pagination a.active {
            background: #00b207;
            color: white;
            border-color: #00b207;
        }
        
        .breadcrumb-menu {
            background: transparent;
            padding: 20px 5%;
            display: flex;
            gap: 15px;
            align-items: center;
            max-width: 1400px;
            margin: 20px auto 0;
            position: relative;
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
            gap: 8px;
            padding: 10px 20px;
            background: #f7f7f7;
            color: #333;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            border: 1px solid #e6e6e6;
        }
        
        .breadcrumb-btn:hover {
            background: #00b207;
            color: white;
            border-color: #00b207;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,178,7,0.2);
        }
        
        .breadcrumb-btn.active {
            background: #00b207;
            color: white;
            border-color: #00b207;
        }

        @media (max-width: 1200px) {
            .shops-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1024px) {
            .container {
                padding: 0 4%;
            }
            .breadcrumb-menu {
                padding: 20px 4%;
            }
            .breadcrumb-menu::after {
                left: 4%;
                right: 4%;
            }
        }

        @media (max-width: 900px) {
            .shops-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .shop-type-tabs {
                gap: 8px;
            }
            .shop-type-tab {
                padding: 12px 20px;
                font-size: 14px;
            }
            .shop-type-icon {
                font-size: 20px;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 4%;
            }
            .shops-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .page-title {
                font-size: 28px;
            }
            .location-info-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            .breadcrumb-menu {
                display: none;
            }
            .shop-type-tabs {
                flex-direction: column;
            }
            .shop-type-tab {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 640px) {
            .shop-card {
                padding: 20px;
            }
            .shop-logo {
                width: 80px;
                height: 80px;
            }
            .shop-name {
                font-size: 16px;
            }
            .shop-description {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header (includes beta notice and top banner) -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Breadcrumb Menu -->
    <div class="breadcrumb-menu">
        <a href="<?= url('/') ?>" class="breadcrumb-btn">
            <span>🏠</span>
            <span><?= $t['home'] ?></span>
        </a>
        <a href="<?= url('categories') ?>" class="breadcrumb-btn">
            <span>☰</span>
            <span><?= $t['categories'] ?></span>
        </a>
        <a href="<?= url('shops') ?>" class="breadcrumb-btn active">
            <span>🏪</span>
            <span><?= $t['shops'] ?></span>
        </a>
    </div>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title"><?= $t['shops_page_title'] ?? 'OCSAPP Virtual Mall' ?></h1>
            <p class="page-subtitle"><?= $t['shops_page_subtitle'] ?? 'Discover groceries, restaurants, stores, and more!' ?></p>
            
            <div class="location-info-bar">
                <div class="location-badge">
                    <span>📍</span>
                    <span id="currentLocationText">
                        <?php if ($currentLocation === 'All Locations'): ?>
                            <?= $t['all_locations'] ?? 'All Locations' ?>: <?= $t['showing_all_shops'] ?? 'Showing all shops' ?>
                        <?php else: ?>
                            <?= htmlspecialchars($currentLocation) ?>: <?= $t['under_radius'] ?? 'Showing shops within' ?> 20 km
                        <?php endif; ?>
                    </span>
                </div>
                <button class="change-location-btn" id="changeLocationBtn" onclick="document.getElementById('locationBtn')?.click()">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= $t['change_location'] ?? 'Change Location' ?>
                </button>
            </div>
        </div>

        <!-- Shop Type Filter Tabs (FIXED: Using $shopCounts from controller) -->
        <div class="shop-type-tabs">
            <a href="<?= url('shops?type=all') ?>" class="shop-type-tab <?= $filterType === 'all' ? 'active' : '' ?>">
                <span class="shop-type-icon">🏬</span>
                <div class="shop-type-label">
                    <span class="shop-type-name"><?= $t['all_shops'] ?? 'All Shops' ?></span>
                    <span class="shop-type-count"><?= $shopCounts['all'] ?> <?= $t['shops'] ?? 'shops' ?></span>
                </div>
            </a>
            
            <a href="<?= url('shops?type=grocery') ?>" class="shop-type-tab <?= $filterType === 'grocery' ? 'active' : '' ?>">
                <span class="shop-type-icon">🛒</span>
                <div class="shop-type-label">
                    <span class="shop-type-name"><?= $t['grocery_store'] ?? 'Grocery Store' ?></span>
                    <span class="shop-type-count"><?= $shopCounts['grocery'] ?> <?= $t['shops'] ?? 'shops' ?></span>
                </div>
            </a>
            
            <a href="<?= url('shops?type=food_court') ?>" class="shop-type-tab <?= $filterType === 'food_court' ? 'active' : '' ?>">
                <span class="shop-type-icon">🍽️</span>
                <div class="shop-type-label">
                    <span class="shop-type-name"><?= $t['food_court'] ?? 'Food Court' ?></span>
                    <span class="shop-type-count"><?= $shopCounts['food_court'] ?> <?= $t['shops'] ?? 'shops' ?></span>
                </div>
            </a>
            
            <a href="<?= url('shops?type=stores') ?>" class="shop-type-tab <?= $filterType === 'stores' ? 'active' : '' ?>">
                <span class="shop-type-icon">🛍️</span>
                <div class="shop-type-label">
                    <span class="shop-type-name"><?= $t['stores'] ?? 'Stores' ?></span>
                    <span class="shop-type-count"><?= $shopCounts['stores'] ?> <?= $t['shops'] ?? 'shops' ?></span>
                </div>
            </a>
            
            <a href="<?= url('shops?type=products') ?>" class="shop-type-tab <?= $filterType === 'products' ? 'active' : '' ?>">
                <span class="shop-type-icon">🎁</span>
                <div class="shop-type-label">
                    <span class="shop-type-name"><?= $t['more_products'] ?? 'Products' ?></span>
                    <span class="shop-type-count"><?= $shopCounts['products'] ?> <?= $t['shops'] ?? 'shops' ?></span>
                </div>
            </a>
        </div>

        <!-- Shops Info Bar -->
        <div class="shops-info-bar">
            <div class="shop-count">
                <?= count($shops) ?> <?= $t['shops_found'] ?? 'shops found' ?>
            </div>
        </div>

        <!-- Shops Grid -->
        <?php if (!empty($shops)): ?>
            <div class="shops-grid">
                <?php foreach ($shops as $shop): ?>
                    <?php
                    $shopType = $shop['shop_type'] ?? 'grocery';
                    
                    // Shop type labels
                    $typeLabels = [
                        'grocery_store' => 'Grocery',
                        'food_court' => 'Food Court',
                        'store' => 'Store',
                        'products' => 'Products'
                    ];
                    ?>
                    <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
                        <span class="shop-type-badge <?= $shopType ?>">
                            <?= $typeLabels[$shopType] ?? 'Shop' ?>
                        </span>
                        
                        <div class="shop-logo">
                            <?php if (!empty($shop['logo'])): ?>
                                <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                            <?php else: ?>
                                <span style="font-size: 48px;">
                                    <?php
                                    $icons = [
                                        'grocery' => '🛒',
                                        'food_court' => '🍽️',
                                        'stores' => '🛍️',
                                        'products' => '🎁'
                                    ];
                                    echo $icons[$shopType] ?? '🏪';
                                    ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="shop-name"><?= htmlspecialchars($shop['name']) ?></div>
                        
                        <?php if (!empty($shop['description'])): ?>
                            <div class="shop-description">
                                <?= htmlspecialchars($shop['description']) ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($shop['address'])): ?>
                            <div class="shop-location">
                                <span>📍</span>
                                <span><?= htmlspecialchars(explode(',', $shop['address'])[0] ?? $shop['address']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="shop-rating">
                            <span class="stars">
                                <?php 
                                    $rating = $shop['average_rating'] ?? 0;
                                    for($i = 0; $i < 5; $i++): 
                                        echo ($i < floor($rating)) ? '⭐' : '☆';
                                    endfor; 
                                ?>
                            </span>
                            <span class="shop-rating-value"><?= number_format($rating, 1) ?></span>
                        </div>
                        
                        <div class="shop-meta">
                            <div class="shop-meta-item">
                                <span>📦</span>
                                <span><?= number_format($shop['product_count'] ?? 0) ?> <?= $t['items'] ?? 'items' ?></span>
                            </div>
                            <div class="shop-meta-item">
                                <span>🕐</span>
                                <span><?= $shop['packaging_time'] ?? 30 ?> <?= $t['mins'] ?? 'mins' ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total > $perPage): ?>
                <div class="pagination">
                    <?php
                    $totalPages = ceil($total / $perPage);
                    for ($i = 1; $i <= $totalPages; $i++): 
                    ?>
                        <a 
                            href="<?= url('shops?page=' . $i . ($filterType !== 'all' ? '&type=' . $filterType : '') . ($search ? '&search=' . urlencode($search) : '')) ?>" 
                            class="<?= $i === $page ? 'active' : '' ?>"
                        >
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <div style="font-size: 80px; margin-bottom: 20px;">🏪</div>
                <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_shops_found'] ?? 'No Shops Found' ?></h2>
                <p style="color: #999; margin-bottom: 30px;"><?= $t['no_shops_desc'] ?? 'Try changing your location or browse all shops' ?></p>
                <button class="change-location-btn" onclick="document.getElementById('locationBtn')?.click()">
                    <?= $t['change_location'] ?? 'Change Location' ?>
                </button>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        window.OCSAPP_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            urls: {
                cartAdd: '<?= url('cart/add') ?>',
                cartCount: '<?= url('cart/count') ?>'
            }
        };
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>