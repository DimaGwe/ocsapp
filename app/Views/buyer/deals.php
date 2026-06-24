<?php
/**
 * Deals Page - Sale Products Display
 * File: app/Views/buyer/deals.php
 * Updated: November 2, 2025 - Using Centralized Translations
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$saleProducts = $saleProducts ?? [];
$pageTitle = $t['deals_page_title'] ?? 'Hot Deals';
$currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/deals.css') ?>">
</head>
<body>
    <!-- Header (includes beta notice and top banner) -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="deals-container">
        <!-- Page Header -->
        <div class="deals-header">
            <h1>🔥 <?= $t['deals_page_title'] ?></h1>
            <p><?= $t['deals_subtitle'] ?></p>
        </div>

        <?php if (!empty($saleProducts)): ?>
            <!-- Controls -->
            <div class="deals-controls">
                <div class="deals-count">
                    <span><?= count($saleProducts) ?></span> <?= $t['all_deals'] ?>
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
                <?php foreach ($saleProducts as $product):
                    $salePrice = $product['sale_price'] ?? ($product['base_price'] * 0.8);
                    $savings = $product['base_price'] - $salePrice;
                    $percentage = round(($savings / $product['base_price']) * 100);
                    $stock = $product['stock_quantity'] ?? 100;

                    // Parse tags
                    $productTags = $product['tags'] ?? [];
                    if (is_string($productTags)) {
                        $productTags = json_decode($productTags, true) ?: [];
                    }
                ?>
                    <article class="deal-card product-card">
                        <!-- Product Badges -->
                        <div class="product-badges">
                            <?php if ($percentage > 0): ?>
                                <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $percentage ?>%</div>
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
                                <img src="<?= asset('images/placeholder.svg') ?>" alt="Product">
                            <?php endif; ?>
                        </a>

                        <!-- Product Info -->
                        <div class="product-info">
                            <?php if (!empty($product['category_name'])): ?>
                                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                            <?php endif; ?>

                            <h3 class="product-name">
                                <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>">
                                    <?= htmlspecialchars($product['name']) ?>
                                </a>
                            </h3>

                            <div class="product-price">
                                <?= currency($salePrice) ?>
                                <?php if (!empty($product['base_price']) && $product['base_price'] > $salePrice): ?>
                                    <span class="old-price"><?= currency($product['base_price']) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($savings > 0): ?>
                                <div class="savings-badge">
                                    <?= $t['you_save'] ?> <?= currency($savings) ?>
                                </div>
                            <?php endif; ?>

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
                    </article>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-deals">
                <div class="empty-deals-icon">🎁</div>
                <h2><?= $t['no_deals_title'] ?></h2>
                <p><?= $t['no_deals_text'] ?></p>
                <a href="<?= url('/') ?>" class="browse-btn">
                    <i class="fas fa-shopping-bag"></i>
                    <?= $t['browse_products'] ?>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <script>
        window.OCSAPP_CONFIG = {
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
                        return parseFloat(a.querySelector('.sale-price').textContent.replace(/[^0-9.]/g, '')) - 
                               parseFloat(b.querySelector('.sale-price').textContent.replace(/[^0-9.]/g, ''));
                    case 'price_high':
                        return parseFloat(b.querySelector('.sale-price').textContent.replace(/[^0-9.]/g, '')) - 
                               parseFloat(a.querySelector('.sale-price').textContent.replace(/[^0-9.]/g, ''));
                    case 'discount':
                        return parseFloat(b.querySelector('.sale-badge').textContent) - 
                               parseFloat(a.querySelector('.sale-badge').textContent);
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
            
            fetch(window.OCSAPP_CONFIG.urls.wishlistToggle, {
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