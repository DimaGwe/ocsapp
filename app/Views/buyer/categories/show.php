<?php
/**
 * Single Category Page
 * Shows products in a category with filters and sorting
 * Updated: November 2, 2025 - Using Centralized Translations
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$category = $category ?? [];
$subcategories = $subcategories ?? [];
$products = $products ?? [];
$sortBy = $sortBy ?? 'popularity';
$pageTitle = !empty($category['name']) ? $category['name'] : 'Category';
$currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= env('APP_NAME', 'OCS') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            padding-bottom: 80px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .category-header { 
            background: linear-gradient(135deg, #00b207 0%, #009206 100%); 
            color: white; 
            padding: 40px; 
            border-radius: 16px; 
            margin-bottom: 30px; 
            text-align: center;
        }
        .category-title { 
            font-size: 36px; 
            font-weight: 700; 
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .category-icon {
            font-size: 48px;
        }
        .category-description { 
            font-size: 16px; 
            opacity: 0.9;
            margin-bottom: 15px;
        }
        .category-meta { 
            display: flex; 
            gap: 20px; 
            justify-content: center;
            font-size: 14px; 
            opacity: 0.9;
        }
        
        .subcategories-bar { 
            background: white; 
            padding: 15px 20px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
            overflow-x: auto; 
            white-space: nowrap;
            display: flex;
            gap: 10px;
        }
        .subcategory-chip { 
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px; 
            background: #f7f7f7; 
            border: 1px solid #e6e6e6; 
            border-radius: 20px; 
            text-decoration: none; 
            color: #333; 
            font-size: 14px; 
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .subcategory-chip:hover, .subcategory-chip.active { 
            background: #00b207; 
            color: white; 
            border-color: #00b207;
            transform: translateY(-2px);
        }
        
        .controls-bar { 
            background: white; 
            padding: 15px 20px; 
            border-radius: 12px; 
            margin-bottom: 20px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        .item-count { 
            font-size: 16px; 
            color: #333; 
            font-weight: 600; 
        }
        .sort-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sort-label {
            font-size: 14px;
            color: #666;
            font-weight: 500;
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
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
            gap: 20px; 
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
        }
        
        .breadcrumb-menu {
            background: white;
            border-bottom: 1px solid #e6e6e6;
            padding: 15px 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            overflow-x: auto;
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
            white-space: nowrap;
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
            .products-grid { 
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); 
                gap: 15px; 
            }
            .category-title { 
                font-size: 28px;
                flex-direction: column;
                gap: 10px;
            }
            .category-meta {
                flex-direction: column;
                gap: 8px;
            }
            .controls-bar {
                flex-direction: column;
                align-items: flex-start;
            }
            .sort-control {
                width: 100%;
            }
            .sort-select {
                flex: 1;
                min-width: 0;
            }
            .breadcrumb-menu {
                display: none;
            }
            .subcategories-bar {
                padding: 10px 15px;
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
        <a href="<?= url('categories') ?>" class="breadcrumb-btn">
            <span>☰</span>
            <span><?= $t['categories'] ?></span>
        </a>
        <?php if (!empty($category['name'])): ?>
            <a href="<?= url('category/' . $category['slug']) ?>" class="breadcrumb-btn active">
                <span><?= $category['icon'] ?? '📁' ?></span>
                <span><?= htmlspecialchars($category['name']) ?></span>
            </a>
        <?php endif; ?>
    </div>

    <div class="container">
        <!-- Category Header -->
        <div class="category-header">
            <h1 class="category-title">
                <?php if (!empty($category['icon'])): ?>
                    <span class="category-icon"><?= $category['icon'] ?></span>
                <?php endif; ?>
                <span><?= htmlspecialchars($category['name'] ?? 'Category') ?></span>
            </h1>
            <?php if (!empty($category['description'])): ?>
                <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
            <?php endif; ?>
            <div class="category-meta">
                <span>📦 <?= count($products) ?> <?= $t['category_items_found'] ?></span>
                <?php if (!empty($subcategories)): ?>
                    <span>🏷️ <?= count($subcategories) ?> <?= $t['subcategories'] ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subcategories Bar -->
        <?php if (!empty($subcategories)): ?>
            <div class="subcategories-bar">
                <a href="<?= url('category/' . $category['slug']) ?>" 
                   class="subcategory-chip <?= empty($_GET['subcategory']) ? 'active' : '' ?>">
                    <span>📂</span>
                    <span><?= $t['all_subcategories'] ?></span>
                </a>
                <?php foreach ($subcategories as $sub): ?>
                    <a href="<?= url('category/' . $category['slug'] . '?subcategory=' . $sub['slug']) ?>" 
                       class="subcategory-chip <?= (!empty($_GET['subcategory']) && $_GET['subcategory'] == $sub['slug']) ? 'active' : '' ?>">
                        <span><?= $sub['icon'] ?? '📦' ?></span>
                        <span><?= htmlspecialchars($sub['name']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Controls Bar -->
        <div class="controls-bar">
            <div class="item-count">
                <?= count($products) ?> <?= $t['category_items_found'] ?>
            </div>
            <div class="sort-control">
                <span class="sort-label"><?= $t['sort_by'] ?>:</span>
                <select class="sort-select" onchange="window.location.href=this.value">
                    <option value="<?= url('category/' . ($category['slug'] ?? '') . '?sort=popularity') ?>" <?= $sortBy === 'popularity' ? 'selected' : '' ?>>
                        <?= $t['sort_popularity'] ?>
                    </option>
                    <option value="<?= url('category/' . ($category['slug'] ?? '') . '?sort=price_asc') ?>" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>
                        <?= $t['sort_price_low'] ?>
                    </option>
                    <option value="<?= url('category/' . ($category['slug'] ?? '') . '?sort=price_desc') ?>" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>
                        <?= $t['sort_price_high'] ?>
                    </option>
                    <option value="<?= url('category/' . ($category['slug'] ?? '') . '?sort=newest') ?>" <?= $sortBy === 'newest' ? 'selected' : '' ?>>
                        <?= $t['sort_newest'] ?>
                    </option>
                    <option value="<?= url('category/' . ($category['slug'] ?? '') . '?sort=name') ?>" <?= $sortBy === 'name' ? 'selected' : '' ?>>
                        <?= $t['sort_name_az'] ?>
                    </option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
            <div class="empty-state">
                <div style="font-size: 80px; margin-bottom: 20px;">📦</div>
                <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_products'] ?></h2>
                <p style="color: #999; margin-bottom: 30px;"><?= $t['no_products_category'] ?></p>
                <a href="<?= url('categories') ?>" style="background:#00b207; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; display:inline-block; font-weight:600;">
                    <?= $t['browse_categories'] ?>
                </a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <article class="product-card">
                        <a href="<?= url('product/' . $product['slug']) ?>" class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= url($product['image']) ?>" 
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="product-placeholder">📦</div>
                            <?php endif; ?>
                        </a>
                        
                        <div class="product-info">
                            <h3 class="product-name">
                                <a href="<?= url('product/' . $product['slug']) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>
                            
                            <div class="product-price">
                                <?= currency($product['base_price'] ?? $product['price']) ?>
                                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > ($product['base_price'] ?? $product['price'])): ?>
                                    <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <button class="add-to-cart" 
                                    data-product-id="<?= $product['id'] ?>"
                                    aria-label="<?= $t['add_to_cart'] ?>">
                                <?= $t['add_to_cart'] ?>
                            </button>
                        </div>
                    </article>
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