<?php
/**
 * Product Detail Page
 * File: app/Views/buyer/product-detail.php
 * Updated: November 2, 2025 - Using Centralized Translations
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);



// ===== ADD THIS SECTION =====
// Initialize StockValidator for real-time stock checking
require_once __DIR__ . '/../../Helpers/StockValidator.php';
$stockValidator = new \App\Helpers\StockValidator();

// Get actual available stock from inventories
$availableStock = isset($product['id']) ? $stockValidator->getAvailableStock($product['id']) : 0;
$stockBadge = $stockValidator->getStockBadge($availableStock);
// ===== END OF ADDITION =====

// Set defaults
$product = $product ?? [];
$shop = $shop ?? [];
$productImages = $productImages ?? [];
$relatedProducts = $relatedProducts ?? [];
$reviews = $reviews ?? [];
$currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
$cartCount = $cartCount ?? 0;

// Calculate discount
$discount = 0;
if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']) {
    $discount = round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100);
}

// Default product image if none provided
if (empty($productImages) && !empty($product['image'])) {
    $productImages = [
        ['url' => $product['image'], 'alt' => $product['name'] ?? 'Product']
    ];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name'] ?? 'Product') ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <?php
    // Generate SEO Meta Tags and Schema
    use App\Helpers\SeoHelper;

    // Prepare product data for SEO
    $seoData = [
        'title' => $product['meta_title'] ?? $product['name'],
        'description' => $product['meta_description'] ?? strip_tags(substr($product['description'] ?? '', 0, 160)),
        'keywords' => $product['meta_keywords'] ?? '',
        'canonical' => $product['canonical_url'] ?? url('product/' . ($product['slug'] ?? '')),
        'image' => $product['og_image'] ?? ($productImages[0]['url'] ?? ''),
        'robots' => $product['robots_meta'] ?? 'index,follow',
        'type' => 'product',
        'url' => url('product/' . ($product['slug'] ?? '')),
        'product' => [
            'price' => $product['price'] ?? 0,
            'availability' => ($availableStock ?? 0) > 0,
            'brand' => $product['brand_name'] ?? '',
            'rating' => $product['average_rating'] ?? 0,
            'review_count' => $product['reviews_count'] ?? 0,
        ]
    ];

    echo SeoHelper::generateMetaTags($seoData);
    echo "\n    ";

    // Product Schema
    $productSchema = [
        'name' => $product['name'] ?? '',
        'description' => strip_tags($product['description'] ?? ''),
        'sku' => $product['sku'] ?? '',
        'image' => $productImages[0]['url'] ?? '',
        'url' => url('product/' . ($product['slug'] ?? '')),
        'brand' => $product['brand_name'] ?? '',
        'price' => $product['price'] ?? 0,
        'stock' => $availableStock ?? 0,
        'rating' => $product['average_rating'] ?? 0,
        'review_count' => $product['reviews_count'] ?? 0,
    ];

    echo SeoHelper::generateProductSchema($productSchema);
    echo "\n    ";

    // Breadcrumb Schema
    $breadcrumbs = [
        ['name' => 'Home', 'url' => '/'],
        ['name' => $product['category'] ?? 'Products', 'url' => '/products'],
        ['name' => $product['name'] ?? 'Product', 'url' => '/product/' . ($product['slug'] ?? '')]
    ];

    echo SeoHelper::generateBreadcrumbSchema($breadcrumbs);
    echo "\n    ";

    // Organization Schema (site-wide)
    echo SeoHelper::generateOrganizationSchema();
    ?>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            padding-bottom: 0;
            background: #f8f9fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .breadcrumb {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
            font-size: 14px;
            color: #666;
        }
        .breadcrumb a {
            color: #00b207;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        .product-container {
            max-width: 1400px;
            margin: 0 auto 40px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            background: white;
            border-radius: 12px;
            padding: 30px;
        }
        
        /* Image Gallery */
        .image-gallery {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .main-image {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            cursor: zoom-in;
        }
        .main-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .image-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff4444;
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            z-index: 2;
        }
        .image-thumbs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding-bottom: 8px;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
        }
        .image-thumbs::-webkit-scrollbar {
            height: 6px;
        }
        .image-thumbs::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .image-thumbs::-webkit-scrollbar-thumb {
            background: #00b207;
            border-radius: 10px;
        }
        .image-thumbs::-webkit-scrollbar-thumb:hover {
            background: #009206;
        }
        .thumb {
            min-width: 80px;
            width: 80px;
            height: 80px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s;
            scroll-snap-align: start;
        }
        .thumb.active {
            border-color: #00b207;
            box-shadow: 0 0 0 2px rgba(0, 178, 7, 0.2);
        }
        .thumb:hover {
            border-color: #00b207;
            transform: scale(1.05);
        }
        .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Product Info */
        .product-info {
            padding: 20px 0;
        }
        .product-badges {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge.veg { background: #d4edda; color: #155724; }
        .badge.featured { background: #fff3cd; color: #856404; }
        .badge.organic { background: #cce5ff; color: #004085; }
        
        .product-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #1a1a1a;
        }
        
        .product-rating {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stars {
            color: #ffc107;
            font-size: 18px;
        }
        .rating-text {
            font-size: 14px;
            color: #666;
        }
        
        .price-section {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        .current-price {
            font-size: 36px;
            font-weight: 700;
            color: #00b207;
        }
        .original-price {
            font-size: 24px;
            color: #999;
            text-decoration: line-through;
        }
        .discount-badge {
            background: #ff4444;
            color: white;
            padding: 5px 15px;
            border-radius: 25px;
            font-weight: 600;
        }
        
        .product-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e9ecef;
        }
        .meta-item {
            font-size: 14px;
        }
        .meta-label {
            color: #666;
            margin-bottom: 5px;
        }
        .meta-value {
            font-weight: 600;
            color: #333;
        }
        
        .quantity-section {
            margin-bottom: 25px;
        }
        .quantity-label {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .qty-controls {
            display: flex;
            align-items: center;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }
        .qty-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: white;
            font-size: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .qty-btn:hover {
            background: #f8f9fa;
        }
        .qty-input {
            width: 60px;
            height: 40px;
            border: none;
            text-align: center;
            font-size: 16px;
            font-weight: 600;
        }
        .stock-info {
            font-size: 14px;
            color: #666;
        }
        .stock-info.low {
            color: #ff4444;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        .btn {
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-primary {
            flex: 1;
            background: #00b207;
            color: white;
        }
        .btn-primary:hover {
            background: #009206;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: white;
            border: 2px solid #e9ecef;
            color: #666;
            min-width: 50px;
        }
        .btn-secondary:hover {
            background: #f8f9fa;
            border-color: #00b207;
        }
        
        .shop-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .shop-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        .shop-logo {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .shop-details {
            flex: 1;
        }
        .shop-name {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        .shop-rating {
            font-size: 14px;
            color: #666;
        }
        .shop-rating a {
            color: #00b207;
            text-decoration: none;
        }
        .shop-rating a:hover {
            text-decoration: underline;
        }
        .shop-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            font-size: 13px;
        }
        .shop-meta-item {
            text-align: center;
        }
        .shop-meta-label {
            color: #999;
            margin-bottom: 5px;
        }
        .shop-meta-value {
            font-weight: 600;
            color: #333;
        }
        
        /* Tabs Section */
        .tabs-section {
            max-width: 1400px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }
        .tabs {
            background: white;
            border-radius: 12px;
            padding: 30px;
        }
        .tab-nav {
            display: flex;
            gap: 30px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
            overflow-x: auto;
        }
        .tab-btn {
            padding: 15px 20px;
            background: none;
            border: none;
            font-size: 16px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            position: relative;
            transition: all 0.3s;
            white-space: nowrap;
            border-radius: 8px 8px 0 0;
        }
        .tab-btn:hover {
            color: #00b207;
            background: rgba(0, 178, 7, 0.05);
        }
        .tab-btn.active {
            color: #00b207;
            background: rgba(0, 178, 7, 0.1);
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 4px;
            background: #00b207;
            border-radius: 4px 4px 0 0;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        
        .description {
            font-size: 15px;
            line-height: 1.8;
            color: #666;
        }
        .description h3 {
            color: #333;
            margin: 20px 0 10px;
        }
        .description ul {
            margin: 15px 0;
            padding-left: 20px;
            line-height: 1.8;
        }
        
        .nutrition-table {
            width: 100%;
            border-collapse: collapse;
        }
        .nutrition-table td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .nutrition-table td:first-child {
            font-weight: 600;
            color: #333;
            width: 200px;
        }
        
        /* Reviews */
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        .review-summary {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .review-score {
            text-align: center;
        }
        .review-score-number {
            font-size: 48px;
            font-weight: 700;
            color: #333;
        }
        .review-score-stars {
            color: #ffc107;
            font-size: 20px;
            margin: 10px 0;
        }
        .review-score-count {
            font-size: 14px;
            color: #666;
        }
        .write-review-btn {
            background: white;
            border: 2px solid #00b207;
            color: #00b207;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
        }
        .write-review-btn:hover {
            background: #00b207;
            color: white;
        }
        
        .review-item {
            padding: 20px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .review-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .review-author {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .review-avatar {
            width: 40px;
            height: 40px;
            background: #00b207;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .review-name {
            font-weight: 600;
            color: #333;
        }
        .review-verified {
            background: #d4edda;
            color: #155724;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        .review-date {
            font-size: 13px;
            color: #999;
        }
        .review-rating {
            color: #ffc107;
            margin-bottom: 10px;
        }
        .review-text {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        /* Related Products */
        .related-section {
            max-width: 1400px;
            margin: 0 auto 40px;
            padding: 0 20px;
        }
        .section-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            color: #333;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            body {
                padding-bottom: 90px;
            }
            .breadcrumb {
                font-size: 13px;
                margin: 15px auto;
            }
            .product-container {
                grid-template-columns: 1fr;
                gap: 25px;
                padding: 20px;
                margin-bottom: 30px;
            }
            .image-thumbs {
                gap: 8px;
            }
            .thumb {
                min-width: 70px;
                width: 70px;
                height: 70px;
            }
            .product-info {
                padding: 10px 0;
            }
            .product-title {
                font-size: 22px;
                margin-bottom: 12px;
            }
            .current-price {
                font-size: 28px;
            }
            .original-price {
                font-size: 20px;
            }
            .discount-badge {
                font-size: 13px;
                padding: 4px 12px;
            }
            .product-meta {
                gap: 15px;
            }
            .qty-btn {
                width: 44px;
                height: 44px;
                font-size: 22px;
            }
            .qty-input {
                width: 70px;
                height: 44px;
                font-size: 18px;
            }
            .action-buttons {
                flex-direction: row;
                gap: 12px;
            }
            .btn-primary {
                flex: 1;
                padding: 16px 20px;
            }
            .btn-secondary {
                min-width: 52px;
                padding: 16px;
            }
            .shop-info {
                padding: 18px;
                margin-bottom: 20px;
            }
            .shop-meta {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .shop-meta-item {
                text-align: left;
                padding: 10px;
                background: white;
                border-radius: 6px;
            }
            .tabs {
                padding: 20px;
            }
            .tab-nav {
                gap: 15px;
                padding-bottom: 5px;
            }
            .tab-btn {
                padding: 12px 16px;
                font-size: 15px;
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            .section-title {
                font-size: 20px;
                margin-bottom: 20px;
            }
            .review-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }

        /* Stock Badge Styles */
.stock-badge-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

.badge {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.9rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #000;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.stock-warning {
    margin-top: 8px;
    padding: 6px 10px;
    background: #fff3cd;
    border-left: 3px solid #ff9800;
    border-radius: 4px;
    font-size: 0.85rem;
}

.stock-warning i {
    margin-right: 5px;
}

/* Image Modal */
.image-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.95);
    z-index: 10000;
    align-items: center;
    justify-content: center;
}

.image-modal.active {
    display: flex;
}

.modal-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-image {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
}

.modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10001;
    transition: all 0.3s;
}

.modal-close:hover {
    background: #f8f9fa;
    transform: scale(1.1);
}

.modal-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-nav:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
}

.modal-nav.prev {
    left: 20px;
}

.modal-nav.next {
    right: 20px;
}

.modal-counter {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(255, 255, 255, 0.9);
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

/* Page wrapper for proper footer positioning */
.page-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
}

/* Footer fix */
.footer {
    margin-top: auto;
    width: 100%;
}

.footer-bottom {
    margin-bottom: 0;
    padding-bottom: 20px;
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

    <!-- Page Wrapper -->
    <div class="page-wrapper">

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= url('/') ?>"><?= $t['home'] ?></a> / 
        <?php if (!empty($product['category'])): ?>
            <a href="<?= url('category/' . ($product['category_slug'] ?? '')) ?>"><?= htmlspecialchars($product['category']) ?></a> / 
        <?php endif; ?>
        <span><?= htmlspecialchars($product['name'] ?? 'Product') ?></span>
    </div>
    
    <!-- Product Container -->
    <div class="product-container">
        <!-- Image Gallery -->
        <div class="image-gallery">
            <div class="main-image" id="mainImage" style="position: relative;">
                <!-- Discount Badge (Top Left) -->
                <?php if ($discount > 0): ?>
                    <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 14px; z-index: 3; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.4);">
                        -<?= $discount ?>% <?= $t['off'] ?>
                    </div>
                <?php endif; ?>

                <!-- Product Badges (Below Discount Badge) -->
                <?php
                $productTags = $product['tags'] ?? [];
                if (is_string($productTags)) {
                    $productTags = json_decode($productTags, true) ?: [];
                }
                if (!empty($productTags) || !empty($product['is_featured'])):
                ?>
                    <div style="position: absolute; top: <?= $discount > 0 ? '55px' : '15px' ?>; left: 15px; z-index: 3; display: flex; flex-direction: column; gap: 6px; max-width: 70%;">
                        <?php if (!empty($product['is_featured'])): ?>
                            <span style="background: rgba(255, 193, 7, 0.95); color: #000; font-size: 11px; padding: 6px 12px; border-radius: 12px; white-space: nowrap; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.15); width: fit-content;">
                                ⭐ <?= $t['featured'] ?>
                            </span>
                        <?php endif; ?>
                        <?php
                        $badgeMap = [
                            'organic' => ['icon' => '🌿', 'label' => $t['organic'] ?? 'Organic', 'color' => 'rgba(52, 211, 153, 0.95)'],
                            'bestseller' => ['icon' => '🏆', 'label' => $t['bestseller'] ?? 'Best Seller', 'color' => 'rgba(0, 178, 7, 0.95)'],
                            'new-arrival' => ['icon' => '🆕', 'label' => $t['new'] ?? 'New', 'color' => 'rgba(59, 130, 246, 0.95)'],
                            'premium' => ['icon' => '💎', 'label' => $t['premium'] ?? 'Premium', 'color' => 'rgba(139, 92, 246, 0.95)'],
                        ];
                        foreach ($productTags as $tag):
                            $tagSlug = is_array($tag) ? ($tag['slug'] ?? '') : $tag;
                            if (isset($badgeMap[$tagSlug])):
                                $badge = $badgeMap[$tagSlug];
                        ?>
                            <span style="background: <?= $badge['color'] ?>; color: white; font-size: 11px; padding: 6px 12px; border-radius: 12px; white-space: nowrap; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.15); width: fit-content;">
                                <?= $badge['icon'] ?> <?= $badge['label'] ?>
                            </span>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                <?php endif; ?>

                <!-- Product Image -->
                <?php if (!empty($productImages)): ?>
                    <img src="<?= htmlspecialchars($productImages[0]['url']) ?>" alt="<?= htmlspecialchars($product['name'] ?? 'Product') ?>" id="mainImageSrc">
                <?php else: ?>
                    <div style="font-size: 80px;">📦</div>
                <?php endif; ?>
            </div>
            
            <?php if (count($productImages) > 1): ?>
                <div class="image-thumbs">
                    <?php foreach ($productImages as $index => $image): ?>
                        <div class="thumb <?= $index === 0 ? 'active' : '' ?>" onclick="changeImage(<?= $index ?>)">
                            <img src="<?= htmlspecialchars($image['url']) ?>" alt="<?= htmlspecialchars($image['alt'] ?? 'Product image') ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Product Info -->
        <div class="product-info">
            <!-- Title -->
            <h1 class="product-title"><?= htmlspecialchars($product['name'] ?? 'Product') ?></h1>
            
            <!-- Rating -->
            <?php if (!empty($product['average_rating'])): ?>
                <div class="product-rating">
                    <span class="stars">
                        <?php 
                        $rating = $product['average_rating'];
                        for($i = 1; $i <= 5; $i++): 
                            echo ($i <= floor($rating)) ? '⭐' : '☆';
                        endfor; 
                        ?>
                    </span>
                    <span class="rating-text">
                        <?= number_format($rating, 1) ?> 
                        (<?= number_format($product['reviews_count'] ?? 0) ?> <?= $t['reviews'] ?>)
                    </span>
                </div>
            <?php endif; ?>
            
            <!-- Price -->
            <div class="price-section">
                <span class="current-price"><?= currency($product['price'] ?? 0) ?></span>
                <?php if ($discount > 0): ?>
                    <span class="original-price"><?= currency($product['compare_at_price']) ?></span>
                    <span class="discount-badge"><?= $discount ?>% <?= $t['off'] ?></span>
                <?php endif; ?>
            </div>
            
            <!-- Product Meta -->
            <div class="product-meta">
                <?php if (!empty($product['sku'])): ?>
                    <div class="meta-item">
                        <div class="meta-label"><?= $t['sku'] ?></div>
                        <div class="meta-value"><?= htmlspecialchars($product['sku']) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['unit'])): ?>
                    <div class="meta-item">
                        <div class="meta-label"><?= $t['unit'] ?></div>
                        <div class="meta-value"><?= htmlspecialchars($product['unit']) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['weight'])): ?>
                    <div class="meta-item">
                        <div class="meta-label"><?= $t['weight'] ?></div>
                        <div class="meta-value"><?= htmlspecialchars($product['weight']) ?></div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($product['brand_name'])): ?>
                    <div class="meta-item">
                        <div class="meta-label"><?= $t['brand'] ?></div>
                        <div class="meta-value"><?= htmlspecialchars($product['brand_name']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Quantity Selector -->
            <div class="quantity-selector">
                <div class="qty-controls">
                    <button class="qty-btn" onclick="updateQuantity(-1)">−</button>
                    <input type="number" class="qty-input" id="quantity" value="1" min="1" max="<?= $availableStock ?>" data-max-stock="<?= $availableStock ?>">
                    <button class="qty-btn" onclick="updateQuantity(1)">+</button>
                </div>
                
                <!-- Stock Badge -->
                <?php if ($availableStock !== false): ?>
                    <div class="stock-badge-container mt-2">
                        <span class="badge <?= $stockBadge['class'] ?>" style="font-size: 0.9rem; padding: 6px 12px;">
                            <?= $stockBadge['badge'] ?>
                        </span>
                        
                        <?php if ($availableStock > 0 && $availableStock < 5): ?>
    <div style="background: #fff3cd; border-left: 4px solid #ff9800; padding: 12px 16px; border-radius: 6px; margin-top: 12px; color: #856404; font-weight: 600; font-size: 0.95rem;">
        ⚠️ <strong>Hurry!</strong> Only <?= $availableStock ?> left in stock!
    </div>
<?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons">
                <?php if ($availableStock <= 0): ?>
                    <button class="btn btn-secondary" disabled style="cursor: not-allowed;">
                        <span>❌</span>
                        <span><?= $t['out_of_stock'] ?? 'Out of Stock' ?></span>
                    </button>
                <?php else: ?>
                    <button class="btn btn-primary" onclick="addToCart()">
                        <span>🛒</span>
                        <span><?= $t['add_to_cart'] ?></span>
                    </button>
                <?php endif; ?>
                <button class="btn btn-secondary" onclick="toggleWishlist()" title="<?= $t['add_to_wishlist'] ?>">
                    <span id="wishlistIcon">🤍</span>
                </button>
                <button class="btn btn-secondary" onclick="share()" title="<?= $t['share'] ?>">
                    <span>📤</span>
                </button>
            </div>
            
            <!-- Shop Info -->
            <?php if (!empty($shop)): ?>
                <div class="shop-info">
                    <div class="shop-header">
                        <div class="shop-logo">
                            <?php if (!empty($shop['logo'])): ?>
                                <img src="<?= asset($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                🏪
                            <?php endif; ?>
                        </div>
                        <div class="shop-details">
                            <div class="shop-name"><?= htmlspecialchars($shop['name'] ?? 'Shop') ?></div>
                            <div class="shop-rating">
                                ⭐ <?= number_format($shop['rating'] ?? 4.5, 1) ?> • 
                                <a href="<?= url('shops/' . ($shop['slug'] ?? '')) ?>"><?= $t['view_shop'] ?></a>
                            </div>
                        </div>
                    </div>
                    <div class="shop-meta">
                        <div class="shop-meta-item">
                            <div class="shop-meta-label"><?= $t['delivery'] ?></div>
                            <div class="shop-meta-value"><?= htmlspecialchars($shop['delivery_time'] ?? '30-45 mins') ?></div>
                        </div>
                        <div class="shop-meta-item">
                            <div class="shop-meta-label"><?= $t['delivery_fee'] ?></div>
                            <div class="shop-meta-value"><?= currency($shop['delivery_fee'] ?? 2.99) ?></div>
                        </div>
                        <div class="shop-meta-item">
                            <div class="shop-meta-label"><?= $t['min_order'] ?></div>
                            <div class="shop-meta-value"><?= currency($shop['minimum_order'] ?? 10.00) ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Tabs Section -->
    <div class="tabs-section">
        <div class="tabs">
            <div class="tab-nav">
                <button class="tab-btn active" onclick="switchTab('description')"><?= $t['description'] ?></button>
                <?php if (!empty($product['nutritional_info'])): ?>
                    <button class="tab-btn" onclick="switchTab('nutrition')"><?= $t['nutritional_info'] ?></button>
                <?php endif; ?>
                <button class="tab-btn" onclick="switchTab('reviews')"><?= $t['reviews'] ?> (<?= $product['reviews_count'] ?? 0 ?>)</button>
            </div>
            
            <!-- Description Tab -->
            <div class="tab-content active" id="tab-description">
                <div class="description">
                    <?php if (!empty($product['description'])): ?>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    <?php else: ?>
                        <p><?= $t['no_description'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Nutrition Tab -->
            <?php if (!empty($product['nutritional_info'])): ?>
                <div class="tab-content" id="tab-nutrition">
                    <table class="nutrition-table">
                        <?php 
                        $nutritionalInfo = is_string($product['nutritional_info']) ? 
                            json_decode($product['nutritional_info'], true) : 
                            $product['nutritional_info'];
                        
                        if (is_array($nutritionalInfo)):
                            foreach ($nutritionalInfo as $key => $value): 
                        ?>
                            <tr>
                                <td><?= ucfirst($key) ?></td>
                                <td><?= htmlspecialchars($value) ?></td>
                            </tr>
                        <?php 
                            endforeach;
                        endif;
                        ?>
                    </table>
                </div>
            <?php endif; ?>
            
            <!-- Reviews Tab -->
            <div class="tab-content" id="tab-reviews">
                <?php if (!empty($product['average_rating'])): ?>
                    <div class="reviews-header">
                        <div class="review-summary">
                            <div class="review-score">
                                <div class="review-score-number"><?= number_format($product['average_rating'], 1) ?></div>
                                <div class="review-score-stars">
                                    <?php 
                                    $rating = $product['average_rating'];
                                    for($i = 1; $i <= 5; $i++): 
                                        echo ($i <= floor($rating)) ? '⭐' : '☆';
                                    endfor; 
                                    ?>
                                </div>
                                <div class="review-score-count"><?= number_format($product['reviews_count'] ?? 0) ?> <?= $t['reviews'] ?></div>
                            </div>
                        </div>
                        <button class="write-review-btn"><?= $t['write_review'] ?></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="review-author">
                                    <div class="review-avatar"><?= strtoupper(substr($review['name'] ?? 'U', 0, 1)) ?></div>
                                    <div>
                                        <div class="review-name">
                                            <?= htmlspecialchars($review['name'] ?? 'Anonymous') ?>
                                            <?php if (!empty($review['verified'])): ?>
                                                <span class="review-verified">✓ <?= $t['verified_purchase'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="review-date"><?= htmlspecialchars($review['date'] ?? '') ?></div>
                            </div>
                            <div class="review-rating">
                                <?php 
                                $reviewRating = $review['rating'] ?? 5;
                                for($i = 1; $i <= 5; $i++): 
                                    echo ($i <= $reviewRating) ? '⭐' : '☆';
                                endfor; 
                                ?>
                            </div>
                            <div class="review-text"><?= htmlspecialchars($review['comment'] ?? '') ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #999; padding: 40px 0;"><?= $t['no_reviews'] ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="related-section">
            <h2 class="section-title"><?= $t['you_may_also_like'] ?></h2>
            <div class="products-grid">
                <?php foreach ($relatedProducts as $related): ?>
                    <?php
                        $relatedDiscount = 0;
                        if (isset($related['compare_at_price']) && $related['compare_at_price'] > 0 && $related['compare_at_price'] > $related['price']) {
                            $relatedDiscount = round((($related['compare_at_price'] - $related['price']) / $related['compare_at_price']) * 100);
                        }
                        $relatedTags = $related['tags'] ?? [];
                        if (is_string($relatedTags)) {
                            $relatedTags = json_decode($relatedTags, true) ?: [];
                        }
                        $relatedStock = $related['stock_quantity'] ?? 100;
                        $relatedSavings = ($related['compare_at_price'] ?? 0) - ($related['price'] ?? 0);
                    ?>
                    <article class="product-card" style="position: relative; overflow: hidden; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s;">
                        <!-- Product Image Container -->
                        <div style="position: relative;">
                            <!-- Sale Badge -->
                            <?php if ($relatedDiscount > 0): ?>
                                <div style="position: absolute; top: 15px; left: 15px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: white; padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 14px; z-index: 3; box-shadow: 0 4px 12px rgba(0, 178, 7, 0.4);">
                                    -<?= $relatedDiscount ?>% <?= $t['off'] ?? 'OFF' ?>
                                </div>
                            <?php endif; ?>

                            <!-- Tags -->
                            <?php if (!empty($relatedTags)): ?>
                                <div style="position: absolute; top: <?= $relatedDiscount > 0 ? '55px' : '15px' ?>; left: 15px; z-index: 3; display: flex; flex-wrap: wrap; gap: 4px; max-width: 70%;">
                                    <?php
                                        $badgeMap = [
                                            'bestseller' => ['class' => 'bestseller', 'icon' => '🏆', 'label' => $t['bestseller'] ?? 'Best Seller'],
                                            'new-arrival' => ['class' => 'new', 'icon' => '🆕', 'label' => $t['new'] ?? 'New'],
                                            'organic' => ['class' => 'organic', 'icon' => '🌿', 'label' => $t['organic'] ?? 'Organic'],
                                            'premium' => ['class' => 'premium', 'icon' => '💎', 'label' => $t['premium'] ?? 'Premium'],
                                        ];
                                        foreach ($relatedTags as $tag):
                                            if (isset($tag['slug']) && isset($badgeMap[$tag['slug']])):
                                                $badge = $badgeMap[$tag['slug']];
                                    ?>
                                        <span style="background: rgba(0, 178, 7, 0.9); color: white; font-size: 11px; padding: 4px 10px; border-radius: 12px; white-space: nowrap; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.15);">
                                            <?= $badge['icon'] ?> <?= $badge['label'] ?>
                                        </span>
                                    <?php
                                            endif;
                                        endforeach;
                                    ?>
                                </div>
                            <?php endif; ?>

                            <!-- Wishlist Button -->
                            <button class="wishlist-btn" onclick="toggleWishlist(<?= $related['id'] ?>)" style="position: absolute; top: 15px; right: 15px; width: 36px; height: 36px; border-radius: 50%; background: white; border: none; cursor: pointer; font-size: 18px; color: #d1d5db; z-index: 3; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: all 0.2s;">
                                <i class="far fa-heart"></i>
                            </button>

                            <!-- Product Image -->
                            <a href="<?= url('product/' . ($related['slug'] ?? '')) ?>" style="display: block; height: 220px; background: #f9fafb; padding: 16px;">
                                <?php if (!empty($related['image'])): ?>
                                    <img src="<?= url($related['image']) ?>"
                                         alt="<?= htmlspecialchars($related['name']) ?>"
                                         loading="lazy"
                                         style="width: 100%; height: 100%; object-fit: contain; transition: transform 0.3s;">
                                <?php else: ?>
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 48px;">📦</div>
                                <?php endif; ?>
                            </a>
                        </div>

                        <!-- Product Info -->
                        <div style="padding: 16px;">
                            <!-- Category -->
                            <?php if (!empty($related['category_name'])): ?>
                                <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; color: #9ca3af; letter-spacing: 0.5px; margin-bottom: 6px;">
                                    <?= htmlspecialchars($related['category_name']) ?>
                                </div>
                            <?php endif; ?>

                            <!-- Product Name -->
                            <h3 style="margin: 0 0 8px 0;">
                                <a href="<?= url('product/' . ($related['slug'] ?? '')) ?>" style="font-size: 14px; font-weight: 600; color: #1f2937; text-decoration: none; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4;">
                                    <?= htmlspecialchars($related['name']) ?>
                                </a>
                            </h3>

                            <!-- Rating -->
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                <?php $relatedRating = $related['average_rating'] ?? 0; ?>
                                <span style="display: flex; gap: 2px;">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if ($i <= floor($relatedRating)): ?>
                                            <i class="fas fa-star" style="color: #fbbf24; font-size: 12px;"></i>
                                        <?php elseif ($i - 0.5 <= $relatedRating): ?>
                                            <i class="fas fa-star-half-alt" style="color: #fbbf24; font-size: 12px;"></i>
                                        <?php else: ?>
                                            <i class="far fa-star" style="color: #d1d5db; font-size: 12px;"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </span>
                                <?php if ($relatedRating > 0): ?>
                                    <span style="font-size: 12px; color: #6b7280; font-weight: 500;"><?= number_format($relatedRating, 1) ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Pricing -->
                            <div style="margin-bottom: 10px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-size: 18px; font-weight: 700; color: #00b207;"><?= currency($related['price']) ?></span>
                                    <?php if (!empty($related['compare_at_price']) && $related['compare_at_price'] > $related['price']): ?>
                                        <span style="font-size: 13px; color: #9ca3af; text-decoration: line-through;"><?= currency($related['compare_at_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php if ($relatedSavings > 0): ?>
                                    <div style="font-size: 12px; color: #00b207; font-weight: 600; margin-top: 2px;">
                                        <?= $t['you_save'] ?? 'You save' ?> <?= currency($relatedSavings) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Stock Status -->
                            <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; margin-bottom: 12px; font-weight: 500; <?= $relatedStock > 10 ? 'color: #00b207;' : ($relatedStock > 0 ? 'color: #f59e0b;' : 'color: #ef4444;') ?>">
                                <?php if ($relatedStock > 10): ?>
                                    <i class="fas fa-check-circle"></i>
                                    <?= $t['in_stock'] ?? 'In Stock' ?>
                                <?php elseif ($relatedStock > 0): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= sprintf($t['low_stock'] ?? 'Only %d left', $relatedStock) ?>
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i>
                                    <?= $t['out_of_stock'] ?? 'Out of Stock' ?>
                                <?php endif; ?>
                            </div>

                            <!-- Add to Cart Button -->
                            <button class="add-to-cart"
                                    data-product-id="<?= $related['id'] ?>"
                                    <?= $relatedStock <= 0 ? 'disabled' : '' ?>
                                    style="width: 100%; padding: 12px; background: linear-gradient(135deg, #00b207 0%, #009206 100%); color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; <?= $relatedStock <= 0 ? 'background: #d1d5db; cursor: not-allowed;' : '' ?>"
                                    aria-label="<?= $t['add_to_cart'] ?? 'Add to Cart' ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <?= $t['add_to_cart'] ?? 'Add to Cart' ?>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Image Modal -->
    <div class="image-modal" id="imageModal">
        <button class="modal-close" onclick="closeModal()" aria-label="Close">&times;</button>
        <button class="modal-nav prev" onclick="navigateModal(-1)" aria-label="Previous">‹</button>
        <div class="modal-content">
            <img src="" alt="Product image" class="modal-image" id="modalImage">
        </div>
        <button class="modal-nav next" onclick="navigateModal(1)" aria-label="Next">›</button>
        <div class="modal-counter" id="modalCounter">1 / 1</div>
    </div>

    </div><!-- End Page Wrapper -->

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
    
    <script>
        window.OCS_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            productId: <?= $product['id'] ?? 0 ?>,
            urls: {
                cartAdd: '<?= url('cart/add') ?>',
                cartCount: '<?= url('cart/count') ?>'
            }
        };

        const productImages = <?= json_encode($productImages) ?>;
        let currentImageIndex = 0;
        let inWishlist = false;
        
        // Image Gallery
        function changeImage(index) {
            if (!productImages[index]) return;
            
            currentImageIndex = index;
            document.getElementById('mainImageSrc').src = productImages[index].url;
            
            document.querySelectorAll('.thumb').forEach((thumb, i) => {
                thumb.classList.toggle('active', i === index);
            });
        }
        
        // Quantity Controls
        function updateQuantity(change) {
            const input = document.getElementById('quantity');
            const current = parseInt(input.value) || 1;
            const max = parseInt(input.max);
            const newValue = current + change;
            
            if (newValue >= 1 && newValue <= max) {
                input.value = newValue;
            } else if (newValue > max) {
                showToast('<?= $t['only'] ?> ' + max + ' <?= $t['items_available'] ?>', 'error');
            }
        }
        
        // Add to Cart
        async function addToCart() {
            const quantity = document.getElementById('quantity').value;
            const button = event.target.closest('.btn-primary');
            const originalContent = button.innerHTML;
            const productId = window.OCS_CONFIG.productId;
            
            button.disabled = true;
            button.innerHTML = '<span>⏳</span><span><?= $t['processing'] ?>...</span>';
            
            try {
                const csrf = {
                    token: document.querySelector('meta[name="csrf-token"]').content,
                    name: document.querySelector('meta[name="csrf-token"]').dataset.name || '_csrf_token'
                };
                
                const formData = new URLSearchParams({
                    [csrf.name]: csrf.token,
                    product_id: productId,
                    quantity: quantity
                });
                
                const response = await fetch(window.OCS_CONFIG.urls.cartAdd, {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: formData.toString()
                });
                
                const data = await response.json();
                
                if (data.success) {
                    button.innerHTML = '<span>✓</span><span><?= $t['added_to_cart'] ?>!</span>';
                    button.style.background = '#28a745';
                    
                    // Update cart count
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount && data.cart_count !== undefined) {
                        cartCount.textContent = data.cart_count;
                        cartCount.style.display = 'flex';
                    }
                    
                    const mobileCartBadge = document.getElementById('mobileCartCount');
                    if (mobileCartBadge && data.cart_count !== undefined) {
                        mobileCartBadge.textContent = data.cart_count;
                        mobileCartBadge.style.display = 'block';
                    }
                    
                    showToast('<?= $t['product_added_to_cart'] ?>', 'success');
                    
                    setTimeout(() => {
                        button.innerHTML = originalContent;
                        button.style.background = '';
                        button.disabled = false;
                    }, 2000);
                } else {
                    showToast(data.message || '<?= $t['failed_to_add'] ?>', 'error');
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                showToast('<?= $t['error_adding_to_cart'] ?>: ' + error.message, 'error');
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        }
        
        // Toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.style.cssText = `
                position: fixed;
                top: 100px;
                right: 20px;
                background: ${type === 'success' ? '#28a745' : '#dc3545'};
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 10000;
                font-family: 'Poppins', sans-serif;
                font-weight: 500;
            `;
            toast.textContent = message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Wishlist Toggle
        function toggleWishlist() {
            const icon = document.getElementById('wishlistIcon');
            inWishlist = !inWishlist;
            icon.textContent = inWishlist ? '❤️' : '🤍';
            showToast(inWishlist ? '<?= $t['added_to_wishlist'] ?>' : '<?= $t['removed_from_wishlist'] ?>');
        }
        
        // Share
        function share() {
            if (navigator.share) {
                navigator.share({
                    title: '<?= htmlspecialchars($product['name'] ?? 'Product') ?>',
                    text: '<?= $t['check_out_product'] ?>',
                    url: window.location.href
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                showToast('<?= $t['link_copied'] ?>');
            }
        }
        
        // Tab Switching
        function switchTab(tabName) {
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('tab-' + tabName).classList.add('active');
        }
        
        // Image Modal
        let modalCurrentIndex = 0;

        function openModal(index = 0) {
            if (!productImages || productImages.length === 0) return;

            modalCurrentIndex = index;
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const counter = document.getElementById('modalCounter');

            modalImg.src = productImages[modalCurrentIndex].url;
            counter.textContent = `${modalCurrentIndex + 1} / ${productImages.length}`;
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Hide nav buttons if only one image
            const prevBtn = modal.querySelector('.prev');
            const nextBtn = modal.querySelector('.next');
            if (productImages.length <= 1) {
                prevBtn.style.display = 'none';
                nextBtn.style.display = 'none';
            } else {
                prevBtn.style.display = 'flex';
                nextBtn.style.display = 'flex';
            }
        }

        function closeModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }

        function navigateModal(direction) {
            if (!productImages || productImages.length === 0) return;

            modalCurrentIndex += direction;

            if (modalCurrentIndex < 0) {
                modalCurrentIndex = productImages.length - 1;
            } else if (modalCurrentIndex >= productImages.length) {
                modalCurrentIndex = 0;
            }

            const modalImg = document.getElementById('modalImage');
            const counter = document.getElementById('modalCounter');

            modalImg.src = productImages[modalCurrentIndex].url;
            counter.textContent = `${modalCurrentIndex + 1} / ${productImages.length}`;
        }

        // Click main image to open modal
        document.getElementById('mainImage')?.addEventListener('click', function() {
            openModal(currentImageIndex);
        });

        // Click thumbnails to open modal
        document.querySelectorAll('.thumb').forEach((thumb, index) => {
            thumb.addEventListener('click', function(e) {
                // Change main image
                changeImage(index);
                // Don't open modal on thumbnail click
                e.stopPropagation();
            });
        });

        // Close modal on background click
        document.getElementById('imageModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            const modal = document.getElementById('imageModal');
            if (!modal.classList.contains('active')) return;

            if (e.key === 'Escape') {
                closeModal();
            } else if (e.key === 'ArrowLeft') {
                navigateModal(-1);
            } else if (e.key === 'ArrowRight') {
                navigateModal(1);
            }
        });
    </script>
    <script src="<?= asset('js/home.js') ?>"></script>
<!-- Auth Popup for Guests -->
<?php include __DIR__ . "/../components/auth-popup.php"; ?>
</body>
</html>