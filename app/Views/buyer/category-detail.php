<?php
/**
 * Category Detail Page - View products in a category
 */

// Get current language and translations
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);

// Get store location
$storeLocation = $_SESSION['location'] ?? 'Santo Domingo, DR';

// Default values
$category = $category ?? [];
$products = $products ?? [];
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name'] ?? 'Category') ?> - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <?= $t['store_location'] ?>: <?= htmlspecialchars($storeLocation) ?> |
        <?= $t['need_help'] ?>: <a href="tel:+18095551234">+1 (809) 555-1234</a>
    </div>

    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <main class="page">
        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="<?= url('/') ?>"><?= $t['home'] ?? 'Home' ?></a>
            <span class="separator">›</span>
            <a href="<?= url('/categories') ?>"><?= $t['categories'] ?? 'Categories' ?></a>
            <span class="separator">›</span>
            <span class="current"><?= htmlspecialchars($category['name']) ?></span>
        </div>

        <!-- Category Header -->
        <div class="category-header">
            <div class="category-header-content">
                <?php if (!empty($category['icon'])): ?>
                    <div class="category-icon-large">
                        <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <h1><?= htmlspecialchars($category['name']) ?></h1>
                    <?php if (!empty($category['description'])): ?>
                        <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($products)): ?>
            <!-- Controls -->
            <div class="deals-controls">
                <div class="deals-count">
                    <?= $t['showing'] ?? 'Showing' ?> <strong><?= count($products) ?></strong>
                    <?= $t['products'] ?? 'products' ?>
                </div>
                <select class="sort-select" onchange="sortProducts(this.value)">
                    <option value="featured" selected><?= $t['sort_featured'] ?? 'Featured' ?></option>
                    <option value="price_low"><?= $t['sort_price_low'] ?? 'Price: Low to High' ?></option>
                    <option value="price_high"><?= $t['sort_price_high'] ?? 'Price: High to Low' ?></option>
                    <option value="newest"><?= $t['sort_newest'] ?? 'Newest First' ?></option>
                </select>
            </div>

            <!-- Products Grid -->
            <div class="products-grid">
                <?php foreach ($products as $product):
                    $price = $product['is_on_sale'] ? $product['sale_price'] : $product['base_price'];
                    $stock = $product['stock_quantity'] ?? 0;
                ?>
                    <article class="product-card" data-price="<?= $price ?>">
                        <!-- Badges -->
                        <?php if ($product['is_on_sale']): ?>
                            <div class="sale-badge">
                                <?= $t['sale'] ?? 'Sale' ?>
                            </div>
                        <?php endif; ?>

                        <!-- Wishlist Button -->
                        <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>

                        <!-- Product Image -->
                        <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= url($product['image']) ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="product-placeholder">📦</div>
                            <?php endif; ?>
                        </a>

                        <div class="product-info">
                            <!-- Brand Name -->
                            <?php if (!empty($product['brand_name'])): ?>
                                <div class="product-category"><?= htmlspecialchars($product['brand_name']) ?></div>
                            <?php endif; ?>

                            <!-- Product Name -->
                            <h3 class="product-name">
                                <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>

                            <!-- Stars Rating -->
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
                                <?php if (!empty($product['review_count'])): ?>
                                    <span class="review-count">(<?= $product['review_count'] ?>)</span>
                                <?php endif; ?>
                            </div>

                            <!-- Price -->
                            <div class="product-price">
                                <?= currency($price) ?>
                                <?php if ($product['is_on_sale'] && $product['base_price'] > $price): ?>
                                    <span class="old-price"><?= currency($product['base_price']) ?></span>
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
                                    aria-label="<?= $t['add_to_cart'] ?? 'Add to Cart' ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <?= $t['add_to_cart'] ?? 'Add to Cart' ?>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-box-open"></i>
                <h2><?= $t['no_products_in_category'] ?? 'No Products in This Category' ?></h2>
                <p><?= $t['no_products_desc'] ?? 'Check back soon for new products' ?></p>
                <a href="<?= url('/categories') ?>" class="btn-primary">
                    <i class="fas fa-arrow-left"></i> <?= $t['back_to_categories'] ?? 'Back to Categories' ?>
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <style>
        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb .separator {
            color: #9ca3af;
        }

        .breadcrumb .current {
            color: #6b7280;
        }

        /* Category Header */
        .category-header {
            background: linear-gradient(135deg, #00b207 0%, #059669 50%, #10b981 100%);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 178, 7, 0.2);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }

        .category-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="rgba(255,255,255,0.03)" x="0" y="0" width="50" height="50"/><rect fill="rgba(255,255,255,0.03)" x="50" y="50" width="50" height="50"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .category-header-content {
            display: flex;
            align-items: center;
            gap: 24px;
            position: relative;
            z-index: 1;
        }

        .category-icon-large {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: white;
        }

        .category-header h1 {
            font-size: 2rem;
            margin: 0 0 8px 0;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .category-header .category-description {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.95);
            margin: 0;
        }

        /* Controls */
        .deals-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .deals-count {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
        }

        .deals-count strong {
            color: var(--primary);
        }

        .sort-select {
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: white;
        }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        /* Product Card */
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid var(--primary);
            border-radius: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 10;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .product-card:hover::after {
            opacity: 1;
        }

        /* Sale Badge */
        .sale-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            z-index: 2;
        }

        /* Wishlist Button */
        .wishlist-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            background: white;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease;
        }

        .wishlist-btn:hover {
            background: #fef2f2;
            transform: scale(1.1);
        }

        .wishlist-btn.active i,
        .wishlist-btn i.fas {
            color: #ef4444;
        }

        /* Product Image */
        .product-image {
            padding: 12px;
            background: #f8f8f8;
            height: 250px;
            display: block;
        }

        .product-image img {
            border-radius: 8px;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            color: #d1d5db;
            background: white;
            border-radius: 8px;
        }

        /* Product Info */
        .product-info {
            padding: 20px;
        }

        .product-category {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .product-name a {
            color: #1f2937;
            text-decoration: none;
        }

        .product-name a:hover {
            color: var(--primary);
        }

        /* Product Rating */
        .product-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
        }

        .product-rating .stars {
            display: flex;
            gap: 2px;
        }

        .product-rating .stars i {
            font-size: 12px;
        }

        .product-rating .stars .fa-star,
        .product-rating .stars .fa-star-half-alt {
            color: #fbbf24;
        }

        .product-rating .stars .fa-star.far {
            color: #d1d5db;
        }

        .product-rating .rating-number {
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .product-rating .review-count {
            font-size: 12px;
            color: #9ca3af;
        }

        /* Product Price */
        .product-price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .old-price {
            font-size: 16px;
            color: #9ca3af;
            text-decoration: line-through;
            font-weight: 400;
            margin-left: 8px;
        }

        /* Stock Status */
        .stock-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            margin-bottom: 12px;
            font-weight: 500;
            width: fit-content;
        }

        .stock-status.in-stock {
            color: #00b207;
        }

        .stock-status.low-stock {
            color: #f59e0b;
        }

        .stock-status.out-of-stock {
            color: #ef4444;
        }

        /* Add to Cart Button */
        .add-to-cart {
            width: 100%;
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart:hover:not(:disabled) {
            background: var(--primary-600);
            transform: translateY(-1px);
        }

        .add-to-cart:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .empty-state i {
            font-size: 64px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #374151;
        }

        .empty-state p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .btn-primary {
            display: inline-block;
            padding: 12px 24px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-primary:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .category-header {
                padding: 30px 20px;
            }

            .category-header-content {
                flex-direction: column;
                text-align: center;
            }

            .category-header h1 {
                font-size: 1.5rem;
            }

            .deals-controls {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }
        }
    </style>

    <script>
        // Sort products functionality
        function sortProducts(sortType) {
            const grid = document.querySelector('.products-grid');
            const cards = Array.from(grid.children);

            cards.sort((a, b) => {
                switch(sortType) {
                    case 'price_low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'featured':
                    case 'newest':
                    default:
                        return 0;
                }
            });

            cards.forEach(card => grid.appendChild(card));
        }
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
