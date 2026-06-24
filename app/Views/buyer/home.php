<?php

/**
 * OCSAPP Landing Page - With Centralized Translations
 * File: app/Views/buyer/home.php
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get ALL translations for current language
$t = getTranslations($currentLang);

// Set default values - UPDATED WITH ALL VARIABLES
$mostSellingProducts = $mostSellingProducts ?? [];
$featuredProducts = $featuredProducts ?? [];
$saleProducts = $saleProducts ?? [];
$topVendors = $topVendors ?? [];  // UPDATED: Was topBrands
$categories = $categories ?? [];
$groceryStoreShops = $groceryStoreShops ?? [];  // NEW!
$foodCourtShops = $foodCourtShops ?? [];
$storesShops = $storesShops ?? [];
$productsShops = $productsShops ?? [];
$recentlyViewed = $recentlyViewed ?? [];  // NEW!
$currentLocation = $currentLocation ?? 'Santo Domingo, DR';
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OCSAPP – Zero-Emission Grocery Delivery</title>
  <?= csrfMeta() ?>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Modular CSS Architecture -->
  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/pages/home.css') ?>">
</head>
<body>
  <!-- Header (includes beta notice and top banner) -->
  <?php include __DIR__ . '/../components/header.php'; ?>

  <!-- Main Content -->
  <main class="page">
    <!-- Hero Slider -->
    <section class="hero-slider" aria-label="Promotional Banners">
      <div class="slides-wrapper" id="heroSlider">
        <!-- Intro Slide - Welcome to OCSAPP -->
        <div class="slide active" data-bg="<?= asset('images/hero/hero1.png') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_intro'] ?></h2>
            <p><?= $t['hero_desc_intro'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['shop_now'] ?></button>
          </div>
        </div>

        <!-- Slide 1 -->
        <div class="slide" data-bg="<?= asset('images/hero/hero1.png') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_1'] ?></h2>
            <p><?= $t['hero_desc_1'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['shop_now'] ?></button>
          </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide" data-bg="<?= asset('images/hero/hero2.jpg') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_2'] ?></h2>
            <p><?= $t['hero_desc_2'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('deals') ?>'"><?= $t['view_deals'] ?></button>
          </div>
        </div>
        
        <!-- Slide 3 -->
        <div class="slide" data-bg="<?= asset('images/hero/hero3.jpg') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_3'] ?></h2>
            <p><?= $t['hero_desc_3'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['explore_local'] ?></button>
          </div>
        </div>
        
        <!-- Slide 4 -->
        <div class="slide" data-bg="<?= asset('images/feature/low-cost-groceries.png') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_4'] ?></h2>
            <p><?= $t['hero_desc_4'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['shop_now'] ?></button>
          </div>
        </div>
        
        <!-- Slide 5 -->
        <div class="slide" data-bg="<?= asset('images/feature/local-products.png') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_5'] ?></h2>
            <p><?= $t['hero_desc_5'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['shop_now'] ?></button>
          </div>
        </div>
        
        <!-- Slide 6 -->
        <div class="slide" data-bg="<?= asset('images/feature/international-brands.png') ?>">
          <div class="slide-content">
            <h2><?= $t['hero_title_6'] ?></h2>
            <p><?= $t['hero_desc_6'] ?></p>
            <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $t['shop_now'] ?></button>
          </div>
        </div>
      </div>
      <button class="hero-nav prev" aria-label="Previous Slide">‹</button>
      <button class="hero-nav next" aria-label="Next Slide">›</button>
      <div class="hero-dots" role="tablist"></div>
    </section>

    <!-- Promo Banner - Admin Managed (products from Sales Management) -->
    <?php if (!empty($promoBanner)): ?>
    <?php
      // Get language-specific promo banner text
      $promoTitle = $currentLang === 'fr' ? ($promoBanner['title_fr'] ?? $promoBanner['title_en']) : $promoBanner['title_en'];
      $promoSubtitle = $currentLang === 'fr' ? ($promoBanner['subtitle_fr'] ?? $promoBanner['subtitle_en'] ?? '') : ($promoBanner['subtitle_en'] ?? '');
      $promoDiscountBadge = $currentLang === 'fr' ? ($promoBanner['discount_badge_fr'] ?? $promoBanner['discount_badge_en'] ?? '20% DE RABAIS') : ($promoBanner['discount_badge_en'] ?? '20% OFF');
      $promoButtonText = $currentLang === 'fr' ? ($promoBanner['button_text_fr'] ?? $promoBanner['button_text_en'] ?? 'Magasiner maintenant') : ($promoBanner['button_text_en'] ?? 'Shop Now');
    ?>
    <section class="promo-banner">
      <div class="promo-text">
        <div class="discount-badge">
          <?php
            // Split discount badge into percentage and label (e.g., "20% OFF" -> "20%" + "OFF")
            $badgeParts = explode(' ', $promoDiscountBadge, 2);
            $percentage = $badgeParts[0] ?? '20%';
            $label = $badgeParts[1] ?? 'OFF';
          ?>
          <div class="discount-percent"><?= htmlspecialchars($percentage) ?></div>
          <div class="discount-label"><?= htmlspecialchars($label) ?></div>
        </div>
        <div class="promo-content">
          <h2><?= htmlspecialchars($promoTitle) ?></h2>
          <?php if (!empty($promoSubtitle)): ?>
            <p><?= htmlspecialchars($promoSubtitle) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div class="promo-image-slider">
        <?php
        // Automatically use sale products from Sales Management, fallback to default images
        $promoImages = [];

        if (!empty($saleProducts)) {
          foreach (array_slice($saleProducts, 0, 5) as $product) {
            if (!empty($product['image'])) {
              $promoImages[] = url($product['image']);
            }
          }
        }

        if (empty($promoImages)) {
          $promoImages = [
            asset('images/products/promo1.jpeg'),
            asset('images/products/promo2.jpg'),
            asset('images/products/promo3.jpg'),
            asset('images/products/promo4.jpg'),
            asset('images/products/promo5.jpg'),
          ];
        }
        ?>

        <div class="promo-slides-container">
          <?php foreach ($promoImages as $index => $image): ?>
            <div class="promo-slide <?= $index === 0 ? 'active' : '' ?> <?= $index === 1 ? 'next' : '' ?>"
                 style="background-image: url('<?= $image ?>');">
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <a href="<?= url($promoBanner['button_url'] ?? 'deals') ?>" class="promo-cta">
        <?= htmlspecialchars($promoButtonText) ?> →
      </a>
    </section>
    <?php endif; ?>
    <!-- Delivery Features -->
    <section class="promo-duo">
      <div class="section-header section-header-centered">
        <h2 class="section-title section-title-large"><?= $t['delivery_title'] ?></h2>
      </div>

      <ul class="check-strip">
        <li><span class="tick">✔</span><span><?= $t['electric_delivery'] ?></span></li>
        <li><span class="tick">✔</span><span><?= $t['same_day_pickup'] ?></span></li>
        <li><span class="tick">✔</span><span><?= $t['freshness_guarantee'] ?></span></li>
      </ul>
    </section>

    <!-- Most Selling Products -->
    <?php if (!empty($mostSellingProducts)): ?>
<section class="section">
  <div class="section-header">
    <h2 class="section-title"><?= $t['most_selling'] ?></h2>
    <a href="<?= url('best-sellers') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="mostSellingScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="mostSellingScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="mostSellingScroll">
    <?php foreach ($mostSellingProducts as $product):
        $discount = $product['discount_percentage'] ?? 0;
        $productTags = $product['tags'] ?? [];
        if (is_string($productTags)) {
            $productTags = json_decode($productTags, true) ?: [];
        }
        $stock = $product['stock_quantity'] ?? 100;
    ?>
        <article class="product-card">
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
  </div>
</section>
<?php endif; ?>

    <!-- Best Sellers (Horizontal Scrollable) -->
    <?php if (!empty($featuredProducts)): ?>
<section class="section">
  <div class="section-header">
    <h2 class="section-title"><?= $t['best_sellers'] ?></h2>
    <a href="<?= url('best-sellers') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="bestSellersScroll">
    <?php foreach ($featuredProducts as $product):
        $discount = $product['discount_percentage'] ?? 0;
        $productTags = $product['tags'] ?? [];
        if (is_string($productTags)) {
            $productTags = json_decode($productTags, true) ?: [];
        }
        $stock = $product['stock_quantity'] ?? 100;
    ?>
        <article class="product-card">
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
  </div>
</section>
<?php endif; ?>

    <!-- Categories -->
    <?php if (!empty($categories)): ?>
    <section class="section">
      <div class="section-header">
        <h2 class="section-title"><?= $t['popular_categories'] ?></h2>
        <a href="<?= url('categories') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
      </div>

      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="categoriesScroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="categoriesScroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="categoriesScroll">
          <?php foreach ($categories as $category): ?>
          <a href="<?= url('category/' . $category['slug']) ?>" class="category-card">
            <div class="category-icon">
              <?php if (!empty($category['image'])): ?>
                <img src="<?= url($category['image']) ?>" alt="<?= htmlspecialchars($category['name']) ?>">
              <?php else: ?>
                <span class="category-icon-placeholder">📦</span>
              <?php endif; ?>
            </div>
            <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Top Shops (Most Products) -->
    <?php if (!empty($topVendors)): ?>
<section class="brands-section">
  <div class="section-header">
    <h2 class="section-title"><?= $t['top_shops'] ?? 'Popular Shops' ?></h2>
    <a href="<?= url('shops') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="topShopsScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="topShopsScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="topShopsScroll">
    <?php foreach ($topVendors as $shop): ?>
    <div class="brand-card">
      <div class="brand-logo">
        <?php if (!empty($shop['logo'])): ?>
          <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['company_name']) ?>">
        <?php else: ?>
          <div class="brand-logo-placeholder">
            <?= strtoupper(substr($shop['company_name'], 0, 2)) ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="product-name"><?= htmlspecialchars($shop['company_name']) ?></div>
      <div class="product-price"><?= $t['from'] ?> <?= currency($shop['min_price'] ?? 0) ?></div>
      <button class="add-to-cart" onclick="window.location.href='<?= url('shops/' . ($shop['slug'] ?? '')) ?>'">
        <?= $t['visit_shop'] ?? 'Visit Shop' ?>
      </button>
    </div>
    <?php endforeach; ?>
    </div>
  </div> 
</section>
<?php endif; ?>



    <!-- ============================================ -->
<!-- UPDATED VIRTUAL MALLS SECTIONS -->
<!-- Replace lines 400-550 in your home.php view -->
<!-- ============================================ -->

<!-- Virtual Mall Main Header -->
<section class="malls-header">
  <div class="malls-header-content">
    <h2><?= $t['virtual_mall'] ?? 'OCSAPP Virtual Mall' ?></h2>
    <p><?= $t['virtual_mall_desc'] ?? 'Discover more than groceries! Explore restaurants, stores, and specialty shops. All in one place.' ?></p>
  </div>
</section>

<!-- VIRTUAL MALL: Grocery Stores (NEW!) -->
<?php if (!empty($groceryStoreShops)): ?>
<section class="section">
  <div class="section-header">
    <div>
      <h2 class="section-title"><?= $t['grocery_stores'] ?? 'Grocery Stores' ?></h2>
      <p class="mall-subtitle"><?= $t['grocery_stores_desc'] ?? 'Fresh produce, meats & daily essentials' ?></p>
    </div>
    <a href="<?= url('shops?type=grocery_store') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="groceryStoresScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="groceryStoresScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="groceryStoresScroll">
    <?php foreach ($groceryStoreShops as $shop): ?>
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
      <!-- Shop Type Badge -->
      <?php $rating = $shop['average_rating'] ?? 0; $packagingTime = $shop['packaging_time'] ?? 30; ?>
      <div class="shop-badges">
        <span class="shop-badge grocery">
          <?= $t['grocery_store'] ?? 'Grocery' ?>
        </span>
      </div>

      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['logo'])): ?>
          <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
        <?php else: ?>
          <span class="category-icon-placeholder">🛒</span>
        <?php endif; ?>
      </div>
      <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
      <?php if (!empty($shop['description'])): ?>
        <div class="shop-mall-description"><?= htmlspecialchars(substr($shop['description'], 0, 80)) ?><?= strlen($shop['description']) > 80 ? '...' : '' ?></div>
      <?php endif; ?>
      <div class="shop-meta">
        <div class="shop-rating">
          <span class="stars">
            <?php
              for($i = 0; $i < 5; $i++):
                echo ($i < floor($rating)) ? '⭐' : '☆';
              endfor;
            ?>
          </span>
          <span><?= number_format($rating, 1) ?></span>
        </div>
        <div class="category-count">⚡ <?= $packagingTime ?> <?= $t['mins'] ?? 'mins' ?></div>
      </div>
    </a>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- VIRTUAL MALL: Food Court -->
<?php if (!empty($foodCourtShops)): ?>
<section class="section">
  <div class="section-header">
    <div>
      <h2 class="section-title"><?= $t['food_court'] ?? 'Food Court' ?></h2>
      <p class="mall-subtitle"><?= $t['food_court_desc'] ?? 'Restaurants, fast food & dining' ?></p>
    </div>
    <a href="<?= url('shops?type=food_court') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="foodCourtScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="foodCourtScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="foodCourtScroll">
    <?php foreach ($foodCourtShops as $shop): ?>
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
      <!-- Shop Type Badge -->
      <?php $rating = $shop['average_rating'] ?? 0; $packagingTime = $shop['packaging_time'] ?? 30; ?>
      <div class="shop-badges">
        <span class="shop-badge foodcourt">
          <?= $t['food_court'] ?? 'Food Court' ?>
        </span>
      </div>

      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['logo'])): ?>
          <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
        <?php else: ?>
          <span class="category-icon-placeholder">🍽️</span>
        <?php endif; ?>
      </div>
      <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
      <?php if (!empty($shop['description'])): ?>
        <div class="shop-mall-description"><?= htmlspecialchars(substr($shop['description'], 0, 80)) ?><?= strlen($shop['description']) > 80 ? '...' : '' ?></div>
      <?php endif; ?>
      <div class="shop-meta">
        <div class="shop-rating">
          <span class="stars">
            <?php
              for($i = 0; $i < 5; $i++):
                echo ($i < floor($rating)) ? '⭐' : '☆';
              endfor;
            ?>
          </span>
          <span><?= number_format($rating, 1) ?></span>
        </div>
        <div class="category-count">⚡ <?= $packagingTime ?> <?= $t['mins'] ?? 'mins' ?></div>
      </div>
    </a>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- VIRTUAL MALL: Stores (FIXED - changed from 'stores' to 'store') -->
<?php if (!empty($storesShops)): ?>
<section class="section">
  <div class="section-header">
    <div>
      <h2 class="section-title"><?= $t['stores'] ?? 'Stores' ?></h2>
      <p class="mall-subtitle"><?= $t['stores_desc'] ?? 'Clothing, services & specialty shops' ?></p>
    </div>
    <a href="<?= url('shops?type=store') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="storesScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="storesScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="storesScroll">
    <?php foreach ($storesShops as $shop): ?>
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
      <!-- Shop Type Badge -->
      <?php $rating = $shop['average_rating'] ?? 0; $packagingTime = $shop['packaging_time'] ?? 30; ?>
      <div class="shop-badges">
        <span class="shop-badge store">
          <?= $t['store'] ?? 'Store' ?>
        </span>
      </div>

      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['logo'])): ?>
          <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
        <?php else: ?>
          <span class="category-icon-placeholder">🛍️</span>
        <?php endif; ?>
      </div>
      <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
      <?php if (!empty($shop['description'])): ?>
        <div class="shop-mall-description"><?= htmlspecialchars(substr($shop['description'], 0, 80)) ?><?= strlen($shop['description']) > 80 ? '...' : '' ?></div>
      <?php endif; ?>
      <div class="shop-meta">
        <div class="shop-rating">
          <span class="stars">
            <?php
              for($i = 0; $i < 5; $i++):
                echo ($i < floor($rating)) ? '⭐' : '☆';
              endfor;
            ?>
          </span>
          <span><?= number_format($rating, 1) ?></span>
        </div>
        <div class="category-count">⚡ <?= $packagingTime ?> <?= $t['mins'] ?? 'mins' ?></div>
      </div>
    </a>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- VIRTUAL MALL: More Products -->
<?php if (!empty($productsShops)): ?>
<section class="section">
  <div class="section-header">
    <div>
      <h2 class="section-title"><?= $t['more_products'] ?? 'More Products' ?></h2>
      <p class="mall-subtitle"><?= $t['more_products_desc'] ?? 'Electronics, furniture, toys & more' ?></p>
    </div>
    <a href="<?= url('shops?type=products') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="moreProductsScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="moreProductsScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="moreProductsScroll">
    <?php foreach ($productsShops as $shop): ?>
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="category-card shop-card">
      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['logo'])): ?>
          <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
        <?php else: ?>
          <span class="category-icon-placeholder">🎁</span>
        <?php endif; ?>
      </div>
      <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
      <?php if (!empty($shop['description'])): ?>
        <div class="shop-mall-description"><?= htmlspecialchars(substr($shop['description'], 0, 80)) ?><?= strlen($shop['description']) > 80 ? '...' : '' ?></div>
      <?php endif; ?>
      <div class="shop-meta">
        <div class="shop-rating">
          <span class="stars">
            <?php 
              $rating = $shop['average_rating'] ?? 0;
              for($i = 0; $i < 5; $i++): 
                echo ($i < floor($rating)) ? '⭐' : '☆';
              endfor; 
            ?>
          </span>
          <span><?= number_format($rating, 1) ?></span>
        </div>
        <div class="category-count">⚡ <?= $shop['packaging_time'] ?? 30 ?> <?= $t['mins'] ?? 'mins' ?></div>
      </div>
    </a>
    <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

    <!-- Sustainability -->
    <section class="sustainability">
      <div class="section-header">
        <h2 class="section-title"><?= $t['zero_carbon_title'] ?></h2>
        <a href="<?= url('about') ?>" class="view-all"><?= $t['learn_more'] ?> →</a>
      </div>
      <p><?= $t['zero_carbon_desc'] ?></p>
      <div class="s-grid">
        <div class="stat">
          <h3><?= $t['electric_fleet'] ?></h3>
          <p><?= $t['electric_fleet_desc'] ?></p>
        </div>
        <div class="stat">
          <h3><?= $t['zero_emissions'] ?></h3>
          <p><?= $t['zero_emissions_desc'] ?></p>
        </div>
        <div class="stat">
          <h3><?= $t['smart_routing'] ?></h3>
          <p><?= $t['smart_routing_desc'] ?></p>
        </div>
      </div>
    </section>

    <!-- Customer Reviews -->
    <section class="section">
      <div class="section-header">
        <h2 class="section-title"><?= $t['customer_reviews'] ?></h2>
      </div>
      <div class="testimonials">
        <?php 
          $testimonials = [
            ['name' => 'Rachel Davis', 'initials' => 'RD', 'review' => $t['review_1']],
            ['name' => 'Michael Johnson', 'initials' => 'MJ', 'review' => $t['review_2']],
            ['name' => 'Sarah Lopez', 'initials' => 'SL', 'review' => $t['review_3']]
          ];
          
          foreach ($testimonials as $testimonial):
        ?>
        <div class="testimonial-card">
          <div class="testimonial-author">
            <div class="author-avatar"><?= $testimonial['initials'] ?></div>
            <div class="testimonial-author-info">
              <div class="testimonial-author-name"><?= $testimonial['name'] ?></div>
              <div class="testimonial-author-role"><?= $t['customer'] ?></div>
            </div>
          </div>
          <div class="testimonial-text"><?= $testimonial['review'] ?></div>
          <div class="product-rating testimonial-rating">
            <span class="stars">⭐⭐⭐⭐⭐</span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- App Download -->
    <section class="app-download">
      <div class="phone-mockup"></div>
      <div>
        <h2><?= $t['download_app'] ?></h2>
        <p><?= $t['download_desc'] ?></p>
        <div class="app-download-info"><?= $t['download_from'] ?></div>
        <div class="download-buttons">
          <a href="#" class="download-btn">
            <span class="download-btn-icon">▶</span>
            <span><?= $t['google_play'] ?></span>
          </a>
          <a href="#" class="download-btn">
            <span class="download-btn-icon">🍎</span>
            <span><?= $t['app_store'] ?></span>
          </a>
        </div>
      </div>
    </section>
    <!-- Newsletter signup now rendered by the shared component in footer.php -->
  </main>

  <!-- Footer -->
  <?php include __DIR__ . '/../components/footer.php'; ?>

  <!-- JavaScript -->
  <script>
    window.OCSAPP_CONFIG = {
      isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
      currentLang: '<?= $currentLang ?>',
      urls: {
        setLanguage: '<?= url('set-language') ?>',
        setLocation: '<?= url('set-location') ?>',
        search: '<?= url('search') ?>',
        newsletter: '<?= url('api/newsletter/subscribe') ?>',
        cartAdd: '<?= url('cart/add') ?>',
        cartCount: '<?= url('cart/count') ?>',
        wishlistToggle: '<?= url('api/wishlist/toggle') ?>'
      }
    };

    // Toggle wishlist function
    function toggleWishlist(productId) {
      const btn = event.currentTarget;
      const icon = btn.querySelector('i');

      // Toggle visual state immediately
      if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        icon.style.color = '#ef4444';
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        icon.style.color = '#d1d5db';
      }

      // Send to server
      fetch(window.OCSAPP_CONFIG.urls.wishlistToggle, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
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
            icon.style.color = '#ef4444';
          } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            icon.style.color = '#d1d5db';
          }
        }
      })
      .catch(error => {
        console.error('Wishlist error:', error);
      });
    }
  </script>
  
  <script src="<?= asset('js/promo-banner.js') ?>"></script>
  <script src="<?= asset('js/home.js') ?>"></script>
  <script src="<?= asset('js/smart-scroll.js') ?>"></script>

<!-- Auth Popup for Guests -->
<?php include __DIR__ . "/../components/auth-popup.php"; ?>
</body>
</html>