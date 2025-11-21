<?php
/**
 * Deals Page - Promo Banner Products Display
 * File: app/Views/buyer/deals.php
 * Updated: 2025-11-20 - Now shows products from active promo banners
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

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
    <title><?= $pageTitle ?> - <?= env('APP_NAME', 'OCS') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/deals.css') ?>">
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <?= $t['store_location'] ?>: <?= htmlspecialchars($storeLocation) ?> |
        <?= $t['need_help'] ?>: <a href="tel:+18095551234">+1 (809) 555-1234</a>
    </div>

    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="deals-container">
        <!-- Page Header -->
        <div class="deals-header">
            <h1>🔥 <?= $t['deals_page_title'] ?></h1>
            <p><?= $t['deals_subtitle'] ?></p>

            <?php if (!empty($promoBanners)): ?>
                <div class="active-promos">
                    <?php foreach ($promoBanners as $banner): ?>
                        <div class="promo-tag">
                            <span class="promo-icon">💰</span>
                            <strong><?= htmlspecialchars($banner['title']) ?></strong>
                            <?php if (!empty($banner['subtitle'])): ?>
                                - <?= htmlspecialchars($banner['subtitle']) ?>
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
                    <span><?= count($dealProducts) ?></span> <?= $t['all_deals'] ?>
                </div>
                <select class="sort-select" onchange="sortDeals(this.value)">
                    <option value="discount"><?= $t['sort_discount'] ?></option>
                    <option value="price_low"><?= $t['sort_price_low'] ?></option>
                    <option value="price_high"><?= $t['sort_price_high'] ?></option>
                    <option value="newest"><?= $t['sort_newest'] ?></option>
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

                        <!-- Wishlist -->
                        <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)">
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

                            <div class="deal-pricing">
                                <div class="price-row">
                                    <span class="sale-price"><?= currency($dealPrice) ?></span>
                                    <span class="original-price"><?= currency($originalPrice) ?></span>
                                </div>
                                <span class="savings">
                                    <?= $t['you_save'] ?> <?= currency($savings) ?>
                                </span>
                            </div>

                            <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                                <?php if ($stock > 10): ?>
                                    <i class="fas fa-check-circle"></i>
                                    <?= $t['in_stock'] ?>
                                <?php elseif ($stock > 0): ?>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= str_replace('{count}', $stock, $t['low_stock']) ?>
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i>
                                    <?= $t['out_of_stock'] ?>
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

    <style>
        /* Additional styles for promo banners */
        .active-promos {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 16px;
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

        .banner-tag {
            display: inline-block;
            padding: 4px 10px;
            background: #fef3c7;
            color: #92400e;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        @media (max-width: 768px) {
            .active-promos {
                flex-direction: column;
            }

            .promo-tag {
                font-size: 13px;
                padding: 10px 16px;
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
