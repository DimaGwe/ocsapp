<?php
/**
 * Best Sellers Page - OCS Store Featured Products
 * Shows same products as homepage Best Sellers section (show_on_home = 1)
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

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
    <title><?= $pageTitle ?> - <?= env('APP_NAME', 'OCS') ?></title>
    <?= csrfMeta() ?>
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

    <div class="container" style="max-width: 1400px; margin: 0 auto; padding: 40px 20px;">
        <!-- Page Header -->
        <div class="page-header" style="margin-bottom: 40px; text-align: center;">
            <h1 style="font-size: 2.5rem; margin-bottom: 12px; color: #1f2937;">
                🏆 <?= $pageTitle ?>
            </h1>
            <p style="font-size: 1.1rem; color: #6b7280; max-width: 600px; margin: 0 auto;">
                <?= $t['best_sellers_subtitle'] ?? 'Our most popular products - handpicked by our admin team' ?>
            </p>
            <?php if ($total > 0): ?>
                <p style="margin-top: 16px; font-size: 0.95rem; color: #9ca3af;">
                    Showing <strong><?= count($products) ?></strong> of <strong><?= $total ?></strong> products
                </p>
            <?php endif; ?>
        </div>

        <?php if (!empty($products)): ?>
            <!-- Products Grid -->
            <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; margin-bottom: 40px;">
                <?php foreach ($products as $product):
                    $discount = $product['discount_percentage'] ?? 0;
                    $productTags = $product['tags'] ?? [];
                    if (is_string($productTags)) {
                        $productTags = json_decode($productTags, true) ?: [];
                    }
                ?>
                    <article class="product-card">
                        <div class="product-badges">
                            <?php if ($discount > 0): ?>
                                <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $discount ?>%</div>
                            <?php endif; ?>

                            <?php if (!empty($product['is_featured'])): ?>
                                <div class="product-badge featured">⭐ <?= $t['featured'] ?? 'Featured' ?></div>
                            <?php endif; ?>

                            <?php
                                $badgeMap = [
                                    'bestseller' => ['class' => 'bestseller', 'icon' => '🏆', 'label' => $t['bestseller'] ?? 'Best Seller'],
                                    'new-arrival' => ['class' => 'new', 'icon' => '🆕', 'label' => $t['new'] ?? 'New'],
                                    'organic' => ['class' => 'organic', 'icon' => '🌿', 'label' => $t['organic'] ?? 'Organic'],
                                    'premium' => ['class' => 'premium', 'icon' => '💎', 'label' => $t['premium'] ?? 'Premium'],
                                    'eco-friendly' => ['class' => 'eco', 'icon' => '♻️', 'label' => $t['eco'] ?? 'Eco'],
                                    'limited-edition' => ['class' => 'limited', 'icon' => '⚡', 'label' => $t['limited'] ?? 'Limited']
                                ];

                                foreach ($productTags as $tag):
                                    if (isset($tag['slug']) && isset($badgeMap[$tag['slug']])):
                                        $badge = $badgeMap[$tag['slug']];
                            ?>
                                <div class="product-badge <?= $badge['class'] ?>">
                                    <?= $badge['icon'] ?> <?= $badge['label'] ?>
                                </div>
                            <?php
                                    endif;
                                endforeach;
                            ?>
                        </div>

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
                            <?php if (!empty($product['brand_name'])): ?>
                                <div class="product-brand" style="font-size: 0.85rem; color: #9ca3af; margin-bottom: 4px;">
                                    <?= htmlspecialchars($product['brand_name']) ?>
                                </div>
                            <?php endif; ?>

                            <h3 class="product-name">
                                <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>

                            <div class="product-price">
                                <?= currency($product['price']) ?>
                                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                                    <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($product['average_rating'])): ?>
                                <div class="product-rating">
                                    <span class="stars">
                                        <?php
                                            $rating = $product['average_rating'] ?? 0;
                                            for($i = 0; $i < 5; $i++):
                                                echo ($i < floor($rating)) ? '⭐' : '☆';
                                            endfor;
                                        ?>
                                    </span>
                                    <span><?= number_format($rating, 1) ?></span>
                                </div>
                            <?php endif; ?>

                            <button class="add-to-cart"
                                    data-product-id="<?= $product['id'] ?>"
                                    aria-label="<?= $t['add_to_cart'] ?? 'Add to Cart' ?>">
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
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-grid">
            <div>
                <h4><?= $t['get_to_know'] ?? 'Get to Know Us' ?></h4>
                <ul>
                    <li><a href="<?= url('about') ?>"><?= $t['about_us'] ?? 'About Us' ?></a></li>
                    <li><a href="<?= url('contact') ?>"><?= $t['contact_us'] ?? 'Contact Us' ?></a></li>
                </ul>
            </div>
            <div>
                <h4><?= $t['promote_with_us'] ?? 'Promote with Us' ?></h4>
                <ul>
                    <li><a href="<?= url('register') ?>"><?= $t['sell_on'] ?? 'Sell on OCS' ?></a></li>
                    <li><a href="<?= url('seller/help') ?>"><?= $t['vendor_central'] ?? 'Vendor Central' ?></a></li>
                </ul>
            </div>
            <div>
                <h4><?= $t['connect_with_us'] ?? 'Connect with Us' ?></h4>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>OCS © <?= date('Y') ?>. <?= $t['all_rights'] ?? 'All rights reserved' ?></p>
        </div>
    </footer>

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
