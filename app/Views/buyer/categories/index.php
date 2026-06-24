<?php
/**
 * All Categories Page
 * Shows grid of all categories with subcategories
 * Updated: November 2, 2025 - Redesigned Card Layout
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$categories = $categories ?? [];
$pageTitle = $t['categories_page_title'] ?? 'Shop by Categories';
$currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['categories_page_title'] ?> - <?= env('APP_NAME', 'OCS') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            padding-bottom: 80px;
            background: #f8f9fa;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Page Header */
        .page-header { 
            background: linear-gradient(135deg, #00b207 0%, #009206 100%); 
            color: white; 
            padding: 40px; 
            border-radius: 16px; 
            margin-bottom: 40px; 
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
        }
        
        /* Categories Grid */
        .categories-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 24px;
            margin-bottom: 40px;
        }
        
        /* Category Card */
        .category-card { 
            background: white; 
            border: 2px solid #e6e6e6; 
            border-radius: 16px; 
            overflow: hidden;
            transition: all 0.3s; 
            text-decoration: none; 
            color: inherit; 
            display: flex;
            flex-direction: column;
        }
        
        .category-card:hover { 
            transform: translateY(-8px); 
            box-shadow: 0 12px 24px rgba(0,0,0,0.15); 
            border-color: #00b207; 
        }
        
        /* Category Image */
        .category-image { 
            width: 100%; 
            height: 200px; 
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden;
            position: relative;
        }
        
        .category-image img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover;
            transition: transform 0.3s;
        }
        
        .category-card:hover .category-image img {
            transform: scale(1.1);
        }
        
        .category-image-icon {
            font-size: 80px;
            opacity: 0.3;
        }
        
        /* Product Count Badge */
        .product-count-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* Category Info */
        .category-info { 
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .category-name { 
            font-size: 20px; 
            font-weight: 700; 
            color: #1a1a1a; 
            margin-bottom: 8px;
            line-height: 1.3;
        }
        
        .category-description {
            font-size: 13px;
            color: #666;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            flex: 1;
        }
        
        .category-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e6e6e6;
        }
        
        .category-count { 
            font-size: 14px; 
            color: #666; 
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .subcategories-count {
            font-size: 13px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* View All Button */
        .view-all-btn {
            width: 100%;
            background: #00b207;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .view-all-btn:hover {
            background: #009206;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 178, 7, 0.3);
        }
        
        .view-all-btn span:last-child {
            transition: transform 0.3s;
        }
        
        .category-card:hover .view-all-btn span:last-child {
            transform: translateX(4px);
        }
        
        /* Empty State */
        .empty-state { 
            text-align: center; 
            padding: 80px 20px; 
            background: white; 
            border-radius: 16px; 
            margin: 40px 0; 
        }
        .empty-icon { 
            font-size: 100px; 
            margin-bottom: 20px; 
            opacity: 0.5; 
        }
        
        /* Breadcrumb Menu */
        .breadcrumb-menu {
            background: white;
            border-bottom: 1px solid #e6e6e6;
            padding: 15px 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
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
        
        @media (max-width: 768px) {
            .categories-grid { 
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); 
                gap: 16px; 
            }
            
            .category-image {
                height: 160px;
            }
            
            .page-title { 
                font-size: 28px; 
            }
            
            .breadcrumb-menu {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .categories-grid { 
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <?= $t['store_location'] ?>: <?= htmlspecialchars($currentLocation) ?> | 
        <?= $t['need_help'] ?>: <a href="tel:+18095551234">+1 (809) 555-1234</a>
    </div>

    <!-- Header -->
    <?php include __DIR__ . '/../../components/header.php'; ?>

    <!-- Breadcrumb Menu -->
    <div class="breadcrumb-menu">
        <a href="<?= url('/') ?>" class="breadcrumb-btn">
            <span>🏠</span>
            <span><?= $t['home'] ?></span>
        </a>
        <a href="<?= url('shops') ?>" class="breadcrumb-btn">
            <span>🏪</span>
            <span><?= $t['shops'] ?></span>
        </a>
        <a href="<?= url('categories') ?>" class="breadcrumb-btn active">
            <span>☰</span>
            <span><?= $t['categories'] ?></span>
        </a>
    </div>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">☰ <?= $t['categories_page_title'] ?></h1>
            <p class="page-subtitle"><?= $t['categories_page_subtitle'] ?></p>
        </div>

        <!-- Categories Grid -->
        <?php if (empty($categories)): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_categories'] ?></h2>
                <p style="color: #999;"><?= $t['categories_appear_soon'] ?></p>
            </div>
        <?php else: ?>
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <!-- Category Image -->
                        <div class="category-image">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?= asset($category['image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
                            <?php elseif (!empty($category['icon'])): ?>
                                <div class="category-image-icon"><?= $category['icon'] ?></div>
                            <?php else: ?>
                                <div class="category-image-icon">📁</div>
                            <?php endif; ?>
                            
                            <!-- Product Count Badge -->
                            <div class="product-count-badge">
                                <span>📦</span>
                                <span><?= $category['product_count'] ?? 0 ?></span>
                            </div>
                        </div>
                        
                        <!-- Category Info -->
                        <div class="category-info">
                            <h2 class="category-name"><?= htmlspecialchars($category['name']) ?></h2>
                            
                            <?php if (!empty($category['description'])): ?>
                                <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
                            <?php endif; ?>
                            
                            <div class="category-meta">
                                <div class="category-count">
                                    <span>📦</span>
                                    <span><?= $category['product_count'] ?? 0 ?> <?= $t['products_available'] ?></span>
                                </div>
                                
                                <?php if (!empty($category['subcategories'])): ?>
                                    <div class="subcategories-count">
                                        <span>🏷️</span>
                                        <span><?= count($category['subcategories']) ?> <?= $t['subcategories'] ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <a href="<?= url('category/' . $category['slug']) ?>" class="view-all-btn">
                                <span><?= $t['view_all_products'] ?></span>
                                <span>→</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h4><?= $t['get_to_know'] ?></h4>
                <ul>
                    <li><a href="<?= url('about') ?>"><?= $t['about_us'] ?></a></li>
                    <li><a href="<?= url('contact') ?>"><?= $t['contact_us'] ?></a></li>
                </ul>
            </div>
            <div>
                <h4><?= $t['promote_with_us'] ?></h4>
                <ul>
                    <li><a href="<?= url('register') ?>"><?= $t['sell_on'] ?></a></li>
                    <li><a href="<?= url('seller/help') ?>"><?= $t['vendor_central'] ?></a></li>
                </ul>
            </div>
            <div>
                <h4><?= $t['connect_with_us'] ?></h4>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>OCS © <?= date('Y') ?>. <?= $t['all_rights'] ?></p>
        </div>
    </footer>

    <script>
        window.OCS_CONFIG = {
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