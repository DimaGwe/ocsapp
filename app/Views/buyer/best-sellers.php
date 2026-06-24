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

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
</head>
<body>
    <!-- Header (includes beta notice and top banner) -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <!-- Breadcrumb Menu -->
    <div class="breadcrumb-menu">
        <a href="<?= url('/') ?>" class="breadcrumb-btn">
            <span>🏠</span>
            <span><?= $t['home'] ?? 'Home' ?></span>
        </a>
        <a href="<?= url('categories') ?>" class="breadcrumb-btn">
            <span>☰</span>
            <span><?= $t['categories'] ?? 'Categories' ?></span>
        </a>
        <a href="<?= url('shops') ?>" class="breadcrumb-btn">
            <span>🏪</span>
            <span><?= $t['shops'] ?? 'Shops' ?></span>
        </a>
        <a href="<?= url('best-sellers') ?>" class="breadcrumb-btn active">
            <span>🏆</span>
            <span><?= $t['best_sellers'] ?? 'Best Sellers' ?></span>
        </a>
    </div>

    <main class="page">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">🏆 <?= $t['best_sellers_page_title'] ?? $pageTitle ?></h1>
            <p class="page-subtitle"><?= $t['best_sellers_subtitle'] ?? 'Our most popular products' ?></p>
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
            <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 24px; margin-bottom: 40px;">
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
        /* Breadcrumb Menu */
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

        @media (max-width: 1024px) {
            .breadcrumb-menu {
                padding: 20px 4%;
            }

            .breadcrumb-menu::after {
                left: 4%;
                right: 4%;
            }
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #00b207 0%, #009206 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-top: 30px;
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

        /* Stock Status Override */
        .stock-status.in-stock {
            color: #1a1a1a;
        }

        .stock-status.low-stock {
            color: #ed8936;
        }

        .stock-status.out-of-stock {
            color: #f56565;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)) !important;
                gap: 20px !important;
            }
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 28px;
            }

            .page-header {
                padding: 40px 20px;
            }

            .breadcrumb-menu {
                gap: 10px;
            }

            .deals-controls {
                flex-direction: column;
                gap: 16px;
                align-items: stretch;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)) !important;
                gap: 16px !important;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
        }
    </style>

    <script>
        // Config for cart functionality
        window.OCSAPP_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            urls: {
                cartAdd: '<?= url('cart/add') ?>',
                cartCount: '<?= url('cart/count') ?>'
            }
        };

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
