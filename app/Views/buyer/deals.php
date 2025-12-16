<?php
/**
 * Deals Page - Promo Banner Products Display
 * File: app/Views/buyer/deals.php
 * Updated: 2025-11-20 - Now shows products from active promo banners
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$dealProducts = $dealProducts ?? [];
$promoBanners = $promoBanners ?? [];
$pageTitle = $t['deals_page_title'] ?? 'Hot Deals';
$storeLocation = 'Kirkland, QC';
$cartCount = $cartCount ?? 0;
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
        <div class="deals-header">
            <h1>🔥 Super Savings</h1>

            <?php if (!empty($promoBanners)): ?>
                <div class="active-promos">
                    <?php foreach ($promoBanners as $banner):
                        // Use language-specific subtitle
                        $bannerSubtitle = ($currentLang === 'fr' && !empty($banner['subtitle_fr'])) ? $banner['subtitle_fr'] : $banner['subtitle'];
                    ?>
                        <div class="promo-tag">
                            <span class="promo-icon">💰</span>
                            <?php if (!empty($bannerSubtitle)): ?>
                                <strong><?= htmlspecialchars($bannerSubtitle) ?></strong>
                            <?php endif; ?>
                            <span class="promo-discount"><?= $banner['discount_percentage'] ?>% OFF</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($dealProducts)): ?>
            <!-- Controls -->
            <div class="deals-controls">
                <div class="deals-count">
                    Showing <strong><?= count($dealProducts) ?></strong> of <strong><?= count($dealProducts) ?></strong> products
                </div>
                <select class="sort-select" onchange="sortDeals(this.value)">
                    <option value="discount" selected><?= $t['sort_discount'] ?? 'Sort by Highest Discount' ?></option>
                    <option value="price_low"><?= $t['sort_price_low'] ?? 'Price: Low to High' ?></option>
                    <option value="price_high"><?= $t['sort_price_high'] ?? 'Price: High to Low' ?></option>
                    <option value="newest"><?= $t['sort_newest'] ?? 'Newest First' ?></option>
                </select>
            </div>

            <!-- Products Grid -->
            <div class="deals-grid">
                <?php foreach ($dealProducts as $product):
                    $dealPrice = $product['deal_price'] ?? $product['base_price'];
                    $originalPrice = $product['original_price'] ?? $product['base_price'];
                    $savings = $product['savings'] ?? 0;
                    $percentage = $product['discount_percentage'] ?? 20;
                    $stock = $product['stock_quantity'] ?? 100;
                ?>
                    <div class="deal-card" data-discount="<?= $percentage ?>" data-price="<?= $dealPrice ?>">
                        <!-- Sale Badge -->
                        <div class="sale-badge">
                            -<?= $percentage ?>% <?= $t['off'] ?>
                        </div>

                        <!-- Wishlist Button (Top Right) -->
                        <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>

                        <!-- Product Image -->
                        <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="deal-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= url($product['image']) ?>"
                                     alt="<?= htmlspecialchars($product['name']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <img src="<?= asset('images/placeholder.jpg') ?>" alt="Product">
                            <?php endif; ?>
                        </a>

                        <!-- Product Info -->
                        <div class="deal-info">
                            <?php if (!empty($product['category_name'])): ?>
                                <div class="deal-category"><?= htmlspecialchars($product['category_name']) ?></div>
                            <?php endif; ?>

                            <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="deal-title">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>

                            <?php if (!empty($product['banner_title'])): ?>
                                <div class="banner-tag">
                                    💰 <?= htmlspecialchars($product['banner_title']) ?>
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

                            <div class="deal-pricing">
                                <div class="price-row">
                                    <span class="sale-price"><?= currency($dealPrice) ?></span>
                                    <span class="original-price"><?= currency($originalPrice) ?></span>
                                </div>
                                <span class="savings">
                                    <?= $t['you_save'] ?? 'You save' ?> <?= currency($savings) ?>
                                </span>
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

                            <button
                                class="add-to-cart-btn add-to-cart"
                                data-product-id="<?= $product['id'] ?>"
                                <?= $stock <= 0 ? 'disabled' : '' ?>
                                aria-label="<?= $t['add_to_cart'] ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <?= $t['add_to_cart'] ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-deals">
                <div class="empty-deals-icon">🎁</div>
                <h2><?= $t['no_deals_title'] ?></h2>
                <p>No active promotional deals at the moment. Check back soon!</p>
                <?php if (function_exists('hasRole') && hasRole('admin')): ?>
                    <p style="margin-top: 16px; padding: 16px; background: #fef3c7; border-radius: 8px; color: #92400e;">
                        <strong>Admin Notice:</strong> Create promo banners and select products in <a href="<?= url('/admin/promo-banners') ?>" style="color: #92400e; text-decoration: underline;">Promo Banners Management</a>
                    </p>
                <?php endif; ?>
                <a href="<?= url('/') ?>" class="browse-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <?= $t['browse_products'] ?>
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <style>
        /* Deals Page Header */
        .deals-header {
            text-align: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, #00b207 0%, #059669 50%, #10b981 100%);
            padding: 60px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 178, 7, 0.2);
            position: relative;
            overflow: hidden;
        }

        .deals-header::before {
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

        .deals-header h1 {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .deals-header p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 1;
        }

        /* Promo Banners */
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

        .promo-discount {
            padding: 4px 10px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
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

        .deals-count span {
            color: var(--primary);
            font-size: 1.3rem;
        }

        .sort-select {
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            background: white;
        }

        /* Deals Grid */
        .deals-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        /* Deal Card */
        .deal-card {
            background: white;
            border-radius: 12px;
            overflow: visible;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
        }

        .deal-card::after {
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

        .deal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            z-index: 5;
        }

        .deal-card:hover::after {
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
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            z-index: 2;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
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

        .wishlist-btn.active i {
            color: #ef4444;
        }

        /* Deal Image */
        .deal-image {
            display: block;
            width: 100%;
            height: 250px;
            overflow: hidden;
            border-radius: 12px 12px 0 0;
            padding: 12px;
            background: #f8f8f8;
        }

        .deal-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
            border-radius: 8px;
        }

        .deal-card:hover .deal-image img {
            transform: scale(1.05);
        }

        /* Deal Info */
        .deal-info {
            padding: 20px;
        }

        .deal-category {
            font-size: 12px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .deal-title {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            text-decoration: none;
            display: block;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .deal-title:hover {
            color: var(--primary);
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

        /* Deal Pricing */
        .deal-pricing {
            margin-bottom: 16px;
        }

        .price-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 6px;
        }

        .sale-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }

        .original-price {
            font-size: 1rem;
            color: #9ca3af;
            text-decoration: line-through;
        }

        .savings {
            font-size: 13px;
            color: #059669;
            font-weight: 600;
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
        .add-to-cart-btn {
            width: 100%;
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-to-cart-btn:hover:not(:disabled) {
            background: var(--primary-600);
            transform: translateY(-1px);
        }

        .add-to-cart-btn:disabled {
            background: #d1d5db;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-deals {
            text-align: center;
            padding: 80px 20px;
        }

        .empty-deals-icon {
            font-size: 5rem;
            margin-bottom: 24px;
        }

        .empty-deals h2 {
            font-size: 1.8rem;
            margin-bottom: 12px;
            color: #374151;
        }

        .empty-deals p {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .browse-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .browse-btn:hover {
            background: var(--primary-600);
            transform: translateY(-1px);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .deals-header h1 {
                font-size: 2rem;
            }

            .active-promos {
                flex-direction: column;
            }

            .promo-tag {
                font-size: 13px;
                padding: 10px 16px;
            }

            .deals-controls {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .deals-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }

            .deal-image {
                height: 180px;
            }
        }
    </style>

    <script>
        window.OCS_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            urls: {
                wishlistToggle: '<?= url("api/wishlist/toggle") ?>',
                cartAdd: '<?= url("cart/add") ?>',
                cartCount: '<?= url("cart/count") ?>'
            }
        };

        // Sort deals functionality
        function sortDeals(sortType) {
            const grid = document.querySelector('.deals-grid');
            const cards = Array.from(grid.children);

            cards.sort((a, b) => {
                switch(sortType) {
                    case 'price_low':
                        return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                    case 'price_high':
                        return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                    case 'discount':
                        return parseFloat(b.dataset.discount) - parseFloat(a.dataset.discount);
                    default:
                        return 0;
                }
            });

            cards.forEach(card => grid.appendChild(card));
        }

        // Wishlist toggle
        function toggleWishlist(productId) {
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');

            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.classList.add('active');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.classList.remove('active');
            }

            fetch(window.OCS_CONFIG.urls.wishlistToggle, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert if failed
                    if (icon.classList.contains('far')) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        btn.classList.add('active');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        btn.classList.remove('active');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating wishlist:', error);
                // Revert on error
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    btn.classList.add('active');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    btn.classList.remove('active');
                }
            });
        }
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
