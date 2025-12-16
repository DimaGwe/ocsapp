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
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            padding-bottom: 0;
            background: #f7f7f7;
        }
        
        .shop-layout {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 20px;
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
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            padding: 30px;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
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
        
        @media (max-width: 1024px) {
            .shop-layout {
                grid-template-columns: 1fr;
                padding: 15px;
            }
            .shop-sidebar {
                position: relative;
                top: 0;
            }
            .breadcrumb-menu {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .shop-layout {
                padding: 10px;
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
                padding: 15px;
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
                padding: 5px;
            }
            .products-grid {
                grid-template-columns: 1fr;
                gap: 10px;
                padding: 10px;
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
    <!-- Top Banner -->
    <div class="top-banner">
        <?= $t['store_location'] ?>: <?= htmlspecialchars($currentLocation) ?> | 
        <?= $t['need_help'] ?>: <a href="tel:+18095551234">+1 (809) 555-1234</a>
    </div>

    <!-- Header -->
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
                        <img src="<?= asset($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                    <?php elseif (!empty($shop['display_logo'])): ?>
                        <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); alert('Feedback feature coming soon!');">
                    <span>💬</span>
                    <span><?= $t['shop_feedback'] ?? 'Shop Feedback' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); alert('Contact feature coming soon!');">
                    <span>✉️</span>
                    <span><?= $t['shop_contact'] ?? 'Shop Contact' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); alert('Policy information coming soon!');">
                    <span>📋</span>
                    <span><?= $t['shop_policy'] ?? 'Shop Policy' ?></span>
                </a>
                <a href="#" class="sidebar-menu-item" onclick="event.preventDefault(); alert('Report feature coming soon!');">
                    <span>⚠️</span>
                    <span><?= $t['shop_report_spam'] ?? 'Report Spam' ?></span>
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
                    <img src="<?= asset($shop['cover_image']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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
                    ?>
                        <article class="product-card" data-price="<?= $price ?>" data-category="<?= htmlspecialchars($product['category_name'] ?? '') ?>">
                            <!-- Sale Badge -->
                            <?php if (!empty($product['is_on_sale'])): ?>
                                <div class="sale-badge"><?= $t['sale'] ?? 'Sale' ?></div>
                            <?php endif; ?>

                            <!-- Wishlist Button -->
                            <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="<?= $t['add_to_wishlist'] ?? 'Add to wishlist' ?>">
                                <i class="far fa-heart"></i>
                            </button>

                            <!-- Product Image -->
                            <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= asset($product['image']) ?>"
                                         alt="<?= htmlspecialchars($product['name']) ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x400?text=No+Image" alt="<?= htmlspecialchars($product['name']) ?>">
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
                                        <span class="rating-count">(<?= number_format($rating, 1) ?>)</span>
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
            <p>OCSAPP © <?= date('Y') ?>. <?= $t['all_rights'] ?></p>
        </div>
    </footer>

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
        window.OCS_CONFIG = {
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
<!-- Auth Popup for Guests -->
<?php include __DIR__ . "/../components/auth-popup.php"; ?>
</body>
</html>