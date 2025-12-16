<?php
/**
 * Best Sellers Page - OCS Store Featured Products
 * Shows same products as homepage Best Sellers section (show_on_home = 1)
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$products = $products ?? [];
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 24;
$pageTitle = $t['best_sellers'] ?? 'Best Sellers';
$storeLocation = 'Kirkland, QC';
$cartCount = $cartCount ?? 0;

// Calculate pagination
$totalPages = ceil($total / $perPage);
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
        <!-- Page Header -->
        <div class="best-sellers-header">
            <h1>🏆 <?= $pageTitle ?></h1>

            <div class="active-promos">
                <div class="promo-tag">
                    <span class="promo-icon">💎</span>
                    <strong><?= $t['best_sellers_subtitle'] ?? 'Our most popular products' ?></strong>
                </div>
            </div>
        </div>

        <?php if (!empty($products)): ?>
            <!-- Controls -->
            <div class="deals-controls">
                <div class="deals-count">
                    Showing <strong><?= count($products) ?></strong> of <strong><?= $total ?></strong> products
                </div>
                <select class="sort-select" onchange="sortProducts(this.value)">
                    <option value="featured" selected><?= $t['sort_featured'] ?? 'Featured' ?></option>
                    <option value="price_low"><?= $t['sort_price_low'] ?? 'Price: Low to High' ?></option>
                    <option value="price_high"><?= $t['sort_price_high'] ?? 'Price: High to Low' ?></option>
                    <option value="newest"><?= $t['sort_newest'] ?? 'Newest First' ?></option>
                </select>
            </div>

            <!-- Products Grid -->
            <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
                <?php foreach ($products as $product):
                    $discount = $product['discount_percentage'] ?? 0;
                    $productTags = $product['tags'] ?? [];
                    if (is_string($productTags)) {
                        $productTags = json_decode($productTags, true) ?: [];
                    }
                    $stock = $product['stock_quantity'] ?? 100;
                ?>
                    <article class="product-card" data-price="<?= $product['price'] ?>">
                        <div class="product-badges">
                            <?php if ($discount > 0): ?>
                                <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $discount ?>%</div>
                            <?php endif; ?>

                            <?php if (!empty($product['is_featured'])): ?>
                                <div class="product-badge featured">⭐ <?= $t['featured'] ?? 'Featured' ?></div>
                            <?php endif; ?>
                        </div>

                        <!-- Wishlist Button (Top Right) -->
                        <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>

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
                            <?php if (!empty($product['category_name'])): ?>
                                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                            <?php endif; ?>

                            <h3 class="product-name">
                                <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>

                            <?php if (!empty($product['show_on_home'])): ?>
                                <div class="banner-tag">
                                    🏆 <?= $t['bestseller'] ?? 'Best Seller' ?>
                                </div>
                            <?php endif; ?>

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
                            </div>

                            <div class="product-price">
                                <?= currency($product['price']) ?>
                                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                                    <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                                <?php endif; ?>
                            </div>

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

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 40px;">
                    <?php if ($page > 1): ?>
                        <a href="<?= url('best-sellers?page=' . ($page - 1)) ?>"
                           class="pagination-btn"
                           style="padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151;">
                            ← Previous
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="pagination-current"
                                  style="padding: 8px 16px; background: #10b981; color: white; border-radius: 6px; font-weight: 600;">
                                <?= $i ?>
                            </span>
                        <?php elseif ($i <= 3 || $i > $totalPages - 3 || abs($i - $page) <= 1): ?>
                            <a href="<?= url('best-sellers?page=' . $i) ?>"
                               style="padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151;">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == 4 || $i == $totalPages - 3): ?>
                            <span style="padding: 8px;">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= url('best-sellers?page=' . ($page + 1)) ?>"
                           class="pagination-btn"
                           style="padding: 8px 16px; border: 1px solid #e5e7eb; border-radius: 6px; text-decoration: none; color: #374151;">
                            Next →
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state" style="text-align: center; padding: 80px 20px;">
                <div style="font-size: 4rem; margin-bottom: 16px;">🏆</div>
                <h2 style="font-size: 1.5rem; margin-bottom: 12px; color: #374151;">
                    No Best Sellers Yet
                </h2>
                <p style="color: #6b7280; margin-bottom: 24px;">
                    Our admin team is working on selecting the best products for you!
                </p>
                <a href="<?= url('/') ?>"
                   class="btn-primary"
                   style="display: inline-block; padding: 12px 24px; background: #10b981; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-home"></i> Back to Homepage
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <style>
        /* Best Sellers Page Header */
        .best-sellers-header {
            text-align: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, #00b207 0%, #059669 50%, #10b981 100%);
            padding: 60px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 178, 7, 0.2);
            position: relative;
            overflow: hidden;
        }

        .best-sellers-header::before {
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

        .best-sellers-header h1 {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        /* Promo Tag */
        .active-promos {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .promo-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            border-radius: 50px;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .promo-icon {
            font-size: 18px;
        }

        /* Deals Controls */
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

        /* Product Card Enhancements */
        .product-card {
            overflow: visible !important;
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

        .product-card:hover::after {
            opacity: 1;
        }

        /* Product Image with padding for border spacing */
        .product-image {
            padding: 12px !important;
            background: #f8f8f8 !important;
            height: 250px !important;
            display: block;
        }

        .product-image img {
            border-radius: 8px;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Product Info Styling */
        .product-category {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .banner-tag {
            display: inline-block;
            padding: 4px 10px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 12px;
            width: fit-content;
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

        /* Product Rating */
        .product-rating {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .best-sellers-header h1 {
                font-size: 2rem;
            }

            .best-sellers-header {
                padding: 40px 20px;
            }

            .deals-controls {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .promo-tag {
                font-size: 13px;
                padding: 10px 16px;
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
