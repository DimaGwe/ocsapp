<?php
/**
 * Shop Detail Page - Individual Shop View
 * Shows shop info, categories, and products
 * Updated: November 2, 2025 - Using Centralized Translations
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

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
    <title><?= htmlspecialchars($shop['name'] ?? 'Shop') ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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
        .sidebar-menu-item svg, .sidebar-menu-item span:first-child {
            width: 20px;
            text-align: center;
            font-size: 18px;
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
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .shop-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
        <a href="<?= url('shops') ?>" class="breadcrumb-btn">
            <span>🏪</span>
            <span><?= $t['shops'] ?></span>
        </a>
        <a href="<?= url('shops/' . ($shop['slug'] ?? '')) ?>" class="breadcrumb-btn active">
            <span><?= htmlspecialchars(substr($shop['name'] ?? 'Shop', 0, 20)) ?></span>
        </a>
    </div>

    <!-- Main Layout -->
    <div class="shop-layout">
        <!-- Sidebar -->
        <aside class="shop-sidebar">
            <!-- Shop Info -->
            <div class="shop-info">
                <div class="shop-logo-large">
                    <?php if (!empty($shop['logo'])): ?>
                        <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                    <?php else: ?>
                        <span style="font-size: 60px;">🏪</span>
                    <?php endif; ?>
                </div>
                <div class="shop-name-large"><?= htmlspecialchars($shop['name'] ?? 'Shop Name') ?></div>
                <div class="shop-location-text">
                    <?= htmlspecialchars(explode(',', $shop['address'] ?? 'Location')[0]) ?>
                </div>
                <div class="shop-rating-large">
                    <span class="stars">⭐</span>
                    <span><?= number_format($shop['average_rating'] ?? 4.8, 1) ?></span>
                </div>
                <button class="view-info-btn" onclick="showShopInfoModal()">
                    <?= $t['view_information'] ?? 'View Information' ?> >
                </button>
            </div>
            
            <!-- Sidebar Menu -->
            <div class="sidebar-menu">
                <a href="<?= url('shops/' . ($shop['slug'] ?? '')) ?>" class="sidebar-menu-item">
                    <span>🛍️</span>
                    <span><?= $t['products'] ?? 'Products' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('feedbackModal');">
                    <span>💬</span>
                    <span><?= $currentLang === 'fr' ? 'Laisser un avis' : 'Leave a Review' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('contactModal');">
                    <span>✉️</span>
                    <span><?= $currentLang === 'fr' ? 'Contacter la boutique' : 'Contact Shop' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('policyModal');">
                    <span>📋</span>
                    <span><?= $currentLang === 'fr' ? 'Politiques' : 'Shop Policy' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); openShopModal('reportModal');">
                    <span>⚠️</span>
                    <span><?= $currentLang === 'fr' ? 'Signaler' : 'Report' ?></span>
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
                    <span style="font-size: 80px; color: white;">🏪</span>
                <?php endif; ?>
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
                    <div style="font-size: 80px; margin-bottom: 20px;">📦</div>
                    <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_products'] ?></h2>
                    <p style="color: #999;"><?= $t['shop_no_products_desc'] ?></p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

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
                                    <?= date('g:i A', strtotime($hours['open_time'])) ?> - <?= date('g:i A', strtotime($hours['close_time'])) ?>
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

            // Collect all checked categories
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const label = checkbox.nextElementSibling;
                    if (label) {
                        selectedCategories.push(label.textContent.trim());
                    }
                }
            });

            // Get all product cards
            const productCards = document.querySelectorAll('.product-card');

            // If no categories selected, show all products
            if (selectedCategories.length === 0) {
                productCards.forEach(card => {
                    card.style.display = '';
                });
                return;
            }

            // Filter products
            productCards.forEach(card => {
                const categoryDiv = card.querySelector('.product-category');
                const productCategory = categoryDiv ? categoryDiv.textContent.trim() : '';

                // Show if product category matches any selected category
                if (selectedCategories.some(cat => productCategory.toLowerCase().includes(cat.toLowerCase()))) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });

            // Update product count
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
                document.body.style.overflow = 'hidden'; // Prevent background scrolling
            }
        }

        function closeShopInfoModal(event) {
            // Only close if clicking the overlay itself, not the modal content
            if (!event || event.target.id === 'shopInfoModal' || event.target.classList.contains('modal-close')) {
                const modal = document.getElementById('shopInfoModal');
                if (modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = ''; // Restore scrolling
                }
            }
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeShopInfoModal({ target: { id: 'shopInfoModal' } });
            }
        });
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>

<!-- Feedback Modal -->
<div class="modal-overlay" id="feedbackModal" onclick="closeShopModalOverlay(event, 'feedbackModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 class="modal-title"><?= $currentLang === 'fr' ? 'Laisser un avis' : 'Leave a Review' ?></h2>
            <button class="modal-close" onclick="closeShopModal('feedbackModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (!isLoggedIn()): ?>
                <p style="color:#666;text-align:center;padding:20px 0">
                    <?= $currentLang === 'fr' ? 'Veuillez vous connecter pour laisser un avis.' : 'Please log in to leave a review.' ?>
                    <br><br>
                    <a href="<?= url('login') ?>" style="background:#00b207;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600">
                        <?= $currentLang === 'fr' ? 'Se connecter' : 'Log In' ?>
                    </a>
                </p>
            <?php else: ?>
                <form id="feedbackForm" onsubmit="submitShopReview(event)">
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Note' : 'Rating' ?> *</div>
                        <div id="starRating" style="display:flex;gap:8px;font-size:32px;cursor:pointer;margin:8px 0">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="star-btn" data-value="<?= $i ?>" onclick="setRating(<?= $i ?>)" style="color:#ddd;transition:color 0.15s">&#9733;</span>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="feedbackRating" name="rating" value="0">
                    </div>
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Commentaire' : 'Comment' ?></div>
                        <textarea name="comment" rows="4" placeholder="<?= $currentLang === 'fr' ? 'Partagez votre experience...' : 'Share your experience...' ?>"
                                  style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                    </div>
                    <div id="feedbackMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                    <button type="submit" style="background:#00b207;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                        <?= $currentLang === 'fr' ? 'Soumettre' : 'Submit Review' ?>
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
            <h2 class="modal-title"><?= $currentLang === 'fr' ? 'Contacter la boutique' : 'Contact Shop' ?></h2>
            <button class="modal-close" onclick="closeShopModal('contactModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="contactForm" onsubmit="submitShopContact(event)">
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Votre nom' : 'Your Name' ?> *</div>
                    <input type="text" name="name" value="<?= htmlspecialchars(($_SESSION['user_name'] ?? '')) ?>"
                           placeholder="<?= $currentLang === 'fr' ? 'Nom complet' : 'Full name' ?>"
                           required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Votre courriel' : 'Your Email' ?> *</div>
                    <input type="email" name="email" value="<?= htmlspecialchars(($_SESSION['user_email'] ?? '')) ?>"
                           placeholder="courriel@exemple.com"
                           required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Message' : 'Message' ?> *</div>
                    <textarea name="message" rows="4" required
                              placeholder="<?= $currentLang === 'fr' ? 'Votre message...' : 'Your message...' ?>"
                              style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                </div>
                <div id="contactMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                <button type="submit" style="background:#00b207;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                    <?= $currentLang === 'fr' ? 'Envoyer' : 'Send Message' ?>
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Policy Modal -->
<div class="modal-overlay" id="policyModal" onclick="closeShopModalOverlay(event, 'policyModal')">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="modal-header">
            <h2 class="modal-title"><?= $currentLang === 'fr' ? 'Politiques de la boutique' : 'Shop Policies' ?></h2>
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
                    <div class="shop-info-label"><i class="fas fa-undo" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Politique de retour' : 'Return Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['return_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['shipping_policy'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-truck" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Politique de livraison' : 'Shipping Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['shipping_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['privacy_policy'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-shield-alt" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Confidentialite' : 'Privacy Policy' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['privacy_policy']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($policies['terms_of_service'])): ?>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-file-contract" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Conditions d\'utilisation' : 'Terms of Service' ?></div>
                    <div class="shop-info-value" style="white-space:pre-line"><?= htmlspecialchars($policies['terms_of_service']) ?></div>
                </div>
                <?php endif; ?>
            <?php else: ?>
                <p style="color:#666;margin-bottom:20px">
                    <?= $currentLang === 'fr'
                        ? 'Cette boutique n\'a pas encore publie de politiques specifiques. Les politiques generales OCSAPP s\'appliquent.'
                        : 'This shop has not published specific policies yet. OCSAPP general marketplace policies apply.' ?>
                </p>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-undo" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Retours' : 'Returns' ?></div>
                    <div class="shop-info-value"><?= $currentLang === 'fr'
                        ? 'Les retours sont acceptes dans les 14 jours suivant la reception de la commande, sous reserve que les articles soient en etat non utilise et dans leur emballage d\'origine.'
                        : 'Returns are accepted within 14 days of receiving your order, provided items are unused and in original packaging.' ?></div>
                </div>
                <div class="shop-info-section">
                    <div class="shop-info-label"><i class="fas fa-truck" style="color:#00b207;margin-right:6px"></i><?= $currentLang === 'fr' ? 'Livraison' : 'Shipping' ?></div>
                    <div class="shop-info-value"><?= $currentLang === 'fr'
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
            <h2 class="modal-title"><?= $currentLang === 'fr' ? 'Signaler cette boutique' : 'Report This Shop' ?></h2>
            <button class="modal-close" onclick="closeShopModal('reportModal')">&times;</button>
        </div>
        <div class="modal-body">
            <?php if (!isLoggedIn()): ?>
                <p style="color:#666;text-align:center;padding:20px 0">
                    <?= $currentLang === 'fr' ? 'Veuillez vous connecter pour signaler une boutique.' : 'Please log in to report a shop.' ?>
                    <br><br>
                    <a href="<?= url('login') ?>" style="background:#00b207;color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-weight:600">
                        <?= $currentLang === 'fr' ? 'Se connecter' : 'Log In' ?>
                    </a>
                </p>
            <?php else: ?>
                <form id="reportForm" onsubmit="submitShopReport(event)">
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Raison' : 'Reason' ?> *</div>
                        <select name="reason" required style="width:100%;padding:10px 12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px">
                            <option value=""><?= $currentLang === 'fr' ? '-- Choisir une raison --' : '-- Select a reason --' ?></option>
                            <option value="spam"><?= $currentLang === 'fr' ? 'Pourriels / contenu non sollicite' : 'Spam / unsolicited content' ?></option>
                            <option value="counterfeit"><?= $currentLang === 'fr' ? 'Produits contrefaits ou frauduleux' : 'Counterfeit or fraudulent products' ?></option>
                            <option value="inappropriate"><?= $currentLang === 'fr' ? 'Contenu inapproprie' : 'Inappropriate content' ?></option>
                            <option value="other"><?= $currentLang === 'fr' ? 'Autre' : 'Other' ?></option>
                        </select>
                    </div>
                    <div class="shop-info-section">
                        <div class="shop-info-label"><?= $currentLang === 'fr' ? 'Description (optionnel)' : 'Description (optional)' ?></div>
                        <textarea name="description" rows="3"
                                  placeholder="<?= $currentLang === 'fr' ? 'Decrivez le probleme...' : 'Describe the issue...' ?>"
                                  style="width:100%;padding:12px;border:1px solid #e6e6e6;border-radius:8px;font-size:14px;resize:vertical;font-family:inherit"></textarea>
                    </div>
                    <div id="reportMsg" style="display:none;padding:10px;border-radius:6px;margin-bottom:12px;font-size:14px"></div>
                    <button type="submit" style="background:#e53e3e;color:white;border:none;padding:12px 28px;border-radius:8px;font-weight:600;cursor:pointer;font-size:15px">
                        <?= $currentLang === 'fr' ? 'Soumettre le signalement' : 'Submit Report' ?>
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
            showMsg('feedbackMsg', '<?= $currentLang === 'fr' ? 'Veuillez selectionner une note.' : 'Please select a rating.' ?>', false);
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