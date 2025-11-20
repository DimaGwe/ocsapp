<?php

/**
 * OCS Landing Page - With Centralized Translations
 * File: app/Views/buyer/home.php
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'en';

// Get ALL translations for current language
$t = getTranslations($currentLang);

// Set default values - UPDATED WITH ALL VARIABLES
$mostSellingProducts = $mostSellingProducts ?? [];
$featuredProducts = $featuredProducts ?? [];
$saleProducts = $saleProducts ?? [];
$topBrands = $topBrands ?? [];
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
  <title>OCS – Zero-Emission Grocery Delivery</title>
  <?= csrfMeta() ?>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/promo-banner.css') ?>">
</head>
<body>
  <!-- Top Banner - Shows store's physical location (admin-set) -->
  <?php $storeLocation = getSetting('store_location', 'Kirkland, QC'); ?>
  <div class="top-banner">
    <?= $t['store_location'] ?>: <?= htmlspecialchars($storeLocation) ?> |
    <?= $t['need_help'] ?>: <a href="tel:<?= getSetting('store_phone', '+15146931001') ?>"><?= getSetting('store_phone', '+1 (514) 693-1001') ?></a>
  </div>

  <!-- Header -->
  <?php include __DIR__ . '/../components/header.php'; ?>

  <!-- Main Content -->
  <main class="page">
    <!-- Hero Slider -->
    <section class="hero-slider" aria-label="Promotional Banners">
      <div class="slides-wrapper" id="heroSlider">
        <!-- Slide 1 -->
        <div class="slide active" data-bg="<?= asset('images/hero/hero1.png') ?>">
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

    <!-- Promo Banner -->
    <section class="promo-banner">
      <div class="promo-text">
        <div class="discount-badge">
          <div class="discount-percent">20%</div>
          <div class="discount-label"><?= $t['sale'] ?></div>
        </div>

        <div class="promo-content">
          <h2><?= $t['super_savings'] ?></h2>
          <p><?= $t['on_select_products'] ?></p>
        </div>
      </div>

      <div class="promo-image-slider">
        <?php 
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
      
      <a href="<?= url('deals') ?>" class="promo-cta"><?= $t['shop_now'] ?> →</a>
    </section>

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
    <?php foreach ($mostSellingProducts as $product): ?>
      <?php 
        $discount = $product['discount_percentage'] ?? 0;
        if (!$discount && isset($product['compare_at_price']) && $product['compare_at_price'] > 0) {
          $discount = round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100);
        }
        
        $productTags = $product['tags'] ?? [];
        if (is_string($productTags)) {
          $productTags = json_decode($productTags, true) ?: [];
        }
      ?>
      <article class="product-card">
        <div class="product-badges">
          <?php if ($discount > 0): ?>
            <div class="product-badge sale"><?= $t['sale'] ?> <?= $discount ?>%</div>
          <?php endif; ?>
          
          <?php if (!empty($product['is_featured'])): ?>
            <div class="product-badge featured">⭐ <?= $t['featured'] ?></div>
          <?php endif; ?>
          
          <?php 
            $badgeMap = [
              'bestseller' => ['class' => 'bestseller', 'icon' => '🏆', 'label' => $t['bestseller']],
              'new-arrival' => ['class' => 'new', 'icon' => '🆕', 'label' => $t['new']],
              'organic' => ['class' => 'organic', 'icon' => '🌿', 'label' => $t['organic']],
              'premium' => ['class' => 'premium', 'icon' => '💎', 'label' => $t['premium']],
              'eco-friendly' => ['class' => 'eco', 'icon' => '♻️', 'label' => $t['eco']],
              'limited-edition' => ['class' => 'limited', 'icon' => '⚡', 'label' => $t['limited']]
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
        
        <a href="<?= url('product/' . $product['slug']) ?>" class="product-image">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= url($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy">
          <?php else: ?>
            <div class="product-placeholder">📦</div>
          <?php endif; ?>
        </a>
        
        <div class="product-info">
          <h3 class="product-name">
            <a href="<?= url('product/' . $product['slug']) ?>">
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
                  aria-label="<?= $t['add_to_cart'] ?>">
            <?= $t['add_to_cart'] ?>
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
    <?php foreach ($featuredProducts as $product): ?>
      <?php 
        $discount = 0;
        if (isset($product['compare_at_price']) && $product['compare_at_price'] > 0) {
          $discount = round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100);
        }
        
        $productTags = $product['tags'] ?? [];
        if (is_string($productTags)) {
          $productTags = json_decode($productTags, true) ?: [];
        }
      ?>
      <article class="product-card">
        <div class="product-badges">
          <?php if ($discount > 0): ?>
            <div class="product-badge sale"><?= $t['sale'] ?> <?= $discount ?>%</div>
          <?php endif; ?>
          
          <?php if (!empty($product['is_featured'])): ?>
            <div class="product-badge featured">⭐ <?= $t['featured'] ?></div>
          <?php endif; ?>
          
          <?php 
            $badgeMap = [
              'bestseller' => ['class' => 'bestseller', 'icon' => '🏆', 'label' => $t['bestseller']],
              'new-arrival' => ['class' => 'new', 'icon' => '🆕', 'label' => $t['new']],
              'organic' => ['class' => 'organic', 'icon' => '🌿', 'label' => $t['organic']],
              'premium' => ['class' => 'premium', 'icon' => '💎', 'label' => $t['premium']],
              'eco-friendly' => ['class' => 'eco', 'icon' => '♻️', 'label' => $t['eco']],
              'limited-edition' => ['class' => 'limited', 'icon' => '⚡', 'label' => $t['limited']]
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
        
        <a href="<?= url('product/' . $product['slug']) ?>" class="product-image">
          <?php if (!empty($product['image'])): ?>
            <img src="<?= url($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy">
          <?php else: ?>
            <div class="product-placeholder">📦</div>
          <?php endif; ?>
        </a>
        
        <div class="product-info">
          <h3 class="product-name">
            <a href="<?= url('product/' . $product['slug']) ?>">
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
                  aria-label="<?= $t['add_to_cart'] ?>">
            <?= $t['add_to_cart'] ?>
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
              <?php if (!empty($category['display_image'])): ?>
                <img src="<?= $category['display_image'] ?>" alt="<?= htmlspecialchars($category['name']) ?>">
              <?php else: ?>
                <span class="category-icon-placeholder">📦</span>
              <?php endif; ?>
            </div>
            <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
            <div class="category-count"><?= number_format($category['product_count']) ?> <?= $t['products'] ?></div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Top Brands -->
    <?php if (!empty($topBrands)): ?>
<section class="brands-section">
  <div class="section-header">
    <h2 class="section-title"><?= $t['top_brands'] ?></h2>
    <a href="<?= url('brands') ?>" class="view-all"><?= $t['view_all'] ?> →</a>
  </div>

  <div class="products-scroll-container">
    <button class="scroll-btn scroll-btn-left" data-scroll-target="topBrandsScroll" aria-label="Scroll left">‹</button>
    <button class="scroll-btn scroll-btn-right" data-scroll-target="topBrandsScroll" aria-label="Scroll right">›</button>
    <div class="products-scroll-grid" id="topBrandsScroll">
    <?php foreach ($topBrands as $brand): ?>
    <div class="brand-card">
      <div class="brand-logo">
        <?php if (!empty($brand['logo'])): ?>
          <img src="<?= asset($brand['logo']) ?>" alt="<?= htmlspecialchars($brand['name']) ?>">
        <?php else: ?>
          <div class="brand-logo-placeholder">
            <?= strtoupper(substr($brand['name'], 0, 2)) ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="product-name"><?= htmlspecialchars($brand['name']) ?></div>
      <div class="product-price"><?= $t['from'] ?> <?= currency($brand['min_price'] ?? 0) ?></div>
      <button class="add-to-cart" onclick="window.location.href='<?= url('brands/' . ($brand['slug'] ?? '')) ?>'">
        <?= $t['shop'] ?>
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
  <section class="promo-duo">
    <div class="section-header section-header-centered">
      <h2 class="section-title section-title-large">OCS Virtual Mall</h2>
    </div>
    <p style="text-align: center; color: var(--gray-600); font-size: 16px; max-width: 700px; margin: 0 auto 20px;">
      Discover more than groceries! Explore restaurants, stores, and specialty shops all in one place.
    </p>
  </section>
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
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="category-card shop-card">
      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['display_logo'])): ?>
          <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="category-card shop-card">
      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['display_logo'])): ?>
          <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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
    <a href="<?= url('shops/' . $shop['slug']) ?>" class="category-card shop-card">
      <div class="category-icon shop-mall-logo">
        <?php if (!empty($shop['display_logo'])): ?>
          <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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
        <?php if (!empty($shop['display_logo'])): ?>
          <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
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

    <!-- Newsletter -->
    <section class="newsletter">
      <div>
        <h3><?= $t['newsletter_title'] ?></h3>
        <p class="newsletter-desc"><?= $t['newsletter_desc'] ?></p>
      </div>
      <form class="newsletter-form">
        <input type="email" placeholder="<?= $t['email_placeholder'] ?>" id="newsletterEmail" required>
        <button class="subscribe-btn" type="submit"><?= $t['subscribe'] ?></button>
      </form>
    </section>
  </main>

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

  <!-- JavaScript -->
  <script>
    window.OCS_CONFIG = {
      isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
      currentLang: '<?= $currentLang ?>',
      urls: {
        setLanguage: '<?= url('set-language') ?>',
        setLocation: '<?= url('set-location') ?>',
        search: '<?= url('search') ?>',
        newsletter: '<?= url('newsletter/subscribe') ?>',
        cartAdd: '<?= url('cart/add') ?>',
        cartCount: '<?= url('cart/count') ?>'
      }
    };
  </script>
  
  <script src="<?= asset('js/promo-banner.js') ?>"></script>
  <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>