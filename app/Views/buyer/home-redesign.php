<?php

/**
 * MARKETPLACE HOME REDESIGN - PREVIEW / CONCEPT ONLY
 * Route: /home-redesign (not linked from nav)
 *
 * Reworks the homepage for the post-OCS-Store facilitator model:
 * - Shop types promoted to primary navigation tiles (it's the whole
 *   marketplace now, not a bonus "Virtual Mall" section)
 * - "Most Selling" replaced by "New Arrivals" (works with zero order history)
 * - "Featured Shops" scaffolded as the future home for paid/sponsored placement
 * Does not touch buyer/home.php - that page is unchanged.
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$fr = ($currentLang === 'fr');

$heroSliders = $heroSliders ?? [];
$shopTypeTiles = $shopTypeTiles ?? [];
$featuredShops = $featuredShops ?? [];
$newArrivals = $newArrivals ?? [];
$featuredProducts = $featuredProducts ?? [];
$categories = $categories ?? [];
$groceryStoreShops = $groceryStoreShops ?? [];
$foodCourtShops = $foodCourtShops ?? [];
$storesShops = $storesShops ?? [];
$productsShops = $productsShops ?? [];
$currentLocation = $currentLocation ?? 'Kirkland, QC';
$cartCount = $cartCount ?? 0;

$hasVirtualMalls = !empty($groceryStoreShops) || !empty($foodCourtShops) || !empty($storesShops) || !empty($productsShops);
$hasAnyMarketplaceContent = !empty($newArrivals) || !empty($featuredProducts) || !empty($featuredShops) || $hasVirtualMalls;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>[PREVIEW] OCSAPP Marketplace Redesign</title>
  <meta name="robots" content="noindex, nofollow">
  <?= csrfMeta() ?>
  <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
  <meta name="theme-color" content="#00b207">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/pages/home.css') ?>">
  <link rel="stylesheet" href="<?= asset('css/pages/home-redesign.css') ?>">
</head>
<body>
  <div class="preview-ribbon"><?= $fr ? 'APERCU DE CONCEPT - Non lie a la page principale' : 'CONCEPT PREVIEW - Not linked to the live homepage' ?></div>

  <?php include __DIR__ . '/../components/header.php'; ?>

  <main class="page">
    <!-- Hero Slider (same source/markup as live homepage) -->
    <section class="hero-slider" aria-label="Promotional Banners">
      <div class="slides-wrapper" id="heroSlider">
        <?php if (!empty($heroSliders)): ?>
          <?php foreach ($heroSliders as $i => $slide): ?>
            <div class="slide <?= $i === 0 ? 'active' : '' ?>" data-bg="<?= !empty($slide['image_path']) ? asset($slide['image_path']) : asset('images/hero/hero1.png') ?>">
              <div class="slide-content">
                <h2><?= htmlspecialchars($slide['title'] ?? '') ?></h2>
                <p><?= htmlspecialchars($slide['description'] ?? '') ?></p>
                <button class="slide-btn" onclick="window.location.href='<?= url($slide['button_url'] ?? 'categories') ?>'"><?= htmlspecialchars($slide['button_text'] ?? ($fr ? 'Magasiner' : 'Shop Now')) ?></button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="slide active" data-bg="<?= asset('images/hero/hero1.png') ?>">
            <div class="slide-content">
              <h2><?= $fr ? 'Le commerce local, livre chez vous' : 'Local shops, delivered to you' ?></h2>
              <p><?= $fr ? 'Decouvrez des epiceries, restaurants et boutiques independantes pres de chez vous.' : 'Discover independent grocery stores, restaurants and shops near you.' ?></p>
              <button class="slide-btn" onclick="window.location.href='<?= url('categories') ?>'"><?= $fr ? 'Magasiner' : 'Shop Now' ?></button>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <button class="hero-nav prev" aria-label="Previous Slide">‹</button>
      <button class="hero-nav next" aria-label="Next Slide">›</button>
      <div class="hero-dots" role="tablist"></div>
    </section>

    <!-- Delivery Trust Strip -->
    <section class="promo-duo">
      <div class="section-header section-header-centered">
        <h2 class="section-title section-title-large"><?= $t['delivery_title'] ?? ($fr ? 'Livraison en 15-30 minutes' : 'Enjoy 15-30 Minute Delivery') ?></h2>
      </div>
      <ul class="check-strip">
        <li><span class="tick">✔</span><span><?= $t['electric_delivery'] ?? ($fr ? 'Livraison entierement electrique' : 'All-Electric Delivery') ?></span></li>
        <li><span class="tick">✔</span><span><?= $t['same_day_pickup'] ?? ($fr ? 'Ramassage le jour meme' : 'Same-Day Pickup') ?></span></li>
        <li><span class="tick">✔</span><span><?= $t['freshness_guarantee'] ?? ($fr ? 'Garantie de fraicheur' : 'Freshness Guarantee') ?></span></li>
      </ul>
    </section>

    <?php if (!$hasAnyMarketplaceContent): ?>
    <!-- Empty Marketplace State (reused concept from the live homepage fix) -->
    <section class="marketplace-empty-state">
      <div class="marketplace-empty-content">
        <div class="marketplace-empty-icon">🏪</div>
        <h2><?= $t['empty_marketplace_title'] ?? ($fr ? 'De nouvelles boutiques arrivent bientot !' : 'New shops joining soon!') ?></h2>
        <p><?= $t['empty_marketplace_desc'] ?? ($fr ? 'Nous accueillons actuellement des vendeurs locaux independants.' : "We're onboarding independent local sellers right now.") ?></p>
        <a href="<?= url('seller-central') ?>" class="marketplace-empty-cta"><?= $t['empty_marketplace_cta'] ?? ($fr ? 'Devenir vendeur' : 'Become a Seller') ?> →</a>
      </div>
    </section>
    <?php endif; ?>

    <!-- NEW: Featured Shops - scaffolded as the future home for paid/sponsored placement -->
    <?php if (!empty($featuredShops)): ?>
    <section class="section featured-shops-section">
      <div class="section-header">
        <div>
          <h2 class="section-title"><?= $fr ? 'Boutiques en vedette' : 'Featured Shops' ?></h2>
          <p class="mall-subtitle"><?= $fr ? 'Aujourd\'hui: nos meilleures boutiques actives. Plus tard: emplacement sponsorise.' : "Today: our top active shops. Later: sponsored placement goes here." ?></p>
        </div>
        <a href="<?= url('shops') ?>" class="view-all"><?= $t['view_all'] ?? ($fr ? 'Voir tout' : 'View All') ?> →</a>
      </div>
      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="featuredShopsScroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="featuredShopsScroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="featuredShopsScroll">
          <?php foreach ($featuredShops as $shop): ?>
          <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card featured-shop-card">
            <div class="shop-badges">
              <span class="shop-badge featured-badge"><?= $fr ? 'En vedette' : 'Featured' ?></span>
            </div>
            <div class="category-icon shop-mall-logo">
              <?php if (!empty($shop['display_logo'])): ?>
                <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
              <?php else: ?>
                <span class="category-icon-placeholder">🏬</span>
              <?php endif; ?>
            </div>
            <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
            <div class="shop-meta">
              <div class="shop-rating">
                <span class="stars">
                  <?php $rating = $shop['rating'] ?? 0; for ($i = 0; $i < 5; $i++): echo ($i < floor($rating)) ? '⭐' : '☆'; endfor; ?>
                </span>
                <span><?= number_format($rating, 1) ?></span>
              </div>
              <div class="category-count"><?= (int) $shop['product_count'] ?> <?= $fr ? 'produits' : 'products' ?></div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Categories: 9-category taxonomy, now wired to real shop counts (goods + service verticals) -->
    <section class="section">
      <div class="section-header">
        <h2 class="section-title"><?= $t['popular_categories'] ?? ($fr ? 'Categories populaires' : 'Popular Categories') ?></h2>
        <a href="<?= url('categories') ?>" class="view-all"><?= $t['view_all'] ?? ($fr ? 'Voir tout' : 'View All') ?> →</a>
      </div>
      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="categoriesScroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="categoriesScroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="categoriesScroll">
          <?php foreach ($shopTypeTiles as $cat): ?>
          <a href="<?= url('shops?type=' . $cat['type']) ?>" class="taxonomy-card">
            <div class="category-icon">
              <span class="category-icon-placeholder"><?= $cat['icon'] ?></span>
            </div>
            <div class="taxonomy-title"><?= htmlspecialchars($fr ? $cat['label_fr'] : $cat['label_en']) ?></div>
            <p class="taxonomy-desc"><?= htmlspecialchars($fr ? $cat['desc_fr'] : $cat['desc_en']) ?></p>
            <div class="taxonomy-footer">
              <span class="taxonomy-fulfillment"><?= htmlspecialchars($fr ? $cat['fulfill_fr'] : $cat['fulfill_en']) ?></span>
              <span class="taxonomy-count"><?= (int) $cat['count'] ?> <?= $fr ? 'boutiques' : 'shops' ?></span>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Best Sellers (unchanged concept - admin-curated via show_on_home) -->
    <?php if (!empty($featuredProducts)): ?>
    <section class="section">
      <div class="section-header">
        <h2 class="section-title"><?= $t['best_sellers'] ?? ($fr ? 'Meilleures ventes' : 'Best Sellers') ?></h2>
        <a href="<?= url('best-sellers') ?>" class="view-all"><?= $t['view_all'] ?? ($fr ? 'Voir tout' : 'View All') ?> →</a>
      </div>
      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="bestSellersScroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="bestSellersScroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="bestSellersScroll">
          <?php foreach ($featuredProducts as $product): $stock = $product['stock_quantity'] ?? 100; $discount = $product['discount_percentage'] ?? 0; $rating = $product['average_rating'] ?? 0; ?>
          <article class="product-card">
            <div class="product-badges">
              <?php if ($discount > 0): ?>
                <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $discount ?>%</div>
              <?php endif; ?>
              <?php if (!empty($product['is_featured'])): ?>
                <div class="product-badge featured">⭐ <?= $t['featured'] ?? 'Featured' ?></div>
              <?php endif; ?>
            </div>
            <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="Add to wishlist">
              <i class="far fa-heart"></i>
            </button>
            <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="product-image">
              <?php if (!empty($product['image'])): ?>
                <img src="<?= url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
              <?php else: ?>
                <div class="product-placeholder">📦</div>
              <?php endif; ?>
            </a>
            <div class="product-info">
              <?php if (!empty($product['category_name'])): ?>
                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
              <?php endif; ?>
              <h3 class="product-name"><a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
              <?php if (!empty($product['show_on_home'])): ?>
                <div class="banner-tag">🏆 <?= $t['bestseller'] ?? 'Best Seller' ?></div>
              <?php endif; ?>
              <div class="product-rating">
                <span class="stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= floor($rating)): ?><i class="fas fa-star"></i>
                    <?php elseif ($i - 0.5 <= $rating): ?><i class="fas fa-star-half-alt"></i>
                    <?php else: ?><i class="far fa-star"></i>
                    <?php endif; ?>
                  <?php endfor; ?>
                </span>
                <?php if ($rating > 0): ?><span class="rating-number"><?= number_format($rating, 1) ?></span><?php endif; ?>
              </div>
              <div class="product-price">
                <?= currency($product['price']) ?>
                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                  <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                <?php endif; ?>
              </div>
              <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                <?php if ($stock > 10): ?><i class="fas fa-check-circle"></i> <?= $t['in_stock'] ?? 'In Stock' ?>
                <?php elseif ($stock > 0): ?><i class="fas fa-exclamation-triangle"></i> <?= sprintf($t['low_stock'] ?? 'Only %d left', $stock) ?>
                <?php else: ?><i class="fas fa-times-circle"></i> <?= $t['out_of_stock'] ?? 'Out of Stock' ?>
                <?php endif; ?>
              </div>
              <button class="add-to-cart" data-product-id="<?= $product['id'] ?>" <?= $stock <= 0 ? 'disabled' : '' ?>>
                <i class="fas fa-shopping-cart"></i> <?= $t['add_to_cart'] ?? 'Add to Cart' ?>
              </button>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- New Arrivals (replaces "Most Selling" - works from product #1, no sales history needed) -->
    <?php if (!empty($newArrivals)): ?>
    <section class="section">
      <div class="section-header">
        <div>
          <h2 class="section-title"><?= $fr ? 'Nouveautes' : 'New Arrivals' ?></h2>
          <p class="mall-subtitle"><?= $fr ? 'Fraichement ajoute par nos vendeurs' : 'Freshly added by our sellers' ?></p>
        </div>
        <a href="<?= url('categories') ?>" class="view-all"><?= $t['view_all'] ?? ($fr ? 'Voir tout' : 'View All') ?> →</a>
      </div>
      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="newArrivalsScroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="newArrivalsScroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="newArrivalsScroll">
          <?php foreach ($newArrivals as $product): $stock = $product['stock_quantity'] ?? 100; $rating = $product['average_rating'] ?? 0; ?>
          <article class="product-card">
            <div class="product-badges">
              <div class="product-badge new-badge"><?= $fr ? 'Nouveau' : 'New' ?></div>
              <?php if (($product['discount_percentage'] ?? 0) > 0): ?>
                <div class="product-badge sale"><?= $t['sale'] ?? 'Sale' ?> <?= $product['discount_percentage'] ?>%</div>
              <?php endif; ?>
              <?php if (!empty($product['is_featured'])): ?>
                <div class="product-badge featured">⭐ <?= $t['featured'] ?? 'Featured' ?></div>
              <?php endif; ?>
            </div>
            <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="Add to wishlist">
              <i class="far fa-heart"></i>
            </button>
            <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>" class="product-image">
              <?php if (!empty($product['image'])): ?>
                <img src="<?= url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
              <?php else: ?>
                <div class="product-placeholder">📦</div>
              <?php endif; ?>
            </a>
            <div class="product-info">
              <?php if (!empty($product['category_name'])): ?>
                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
              <?php endif; ?>
              <h3 class="product-name"><a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
              <?php if (!empty($product['show_on_home'])): ?>
                <div class="banner-tag">🏆 <?= $t['bestseller'] ?? 'Best Seller' ?></div>
              <?php endif; ?>
              <div class="product-rating">
                <span class="stars">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <?php if ($i <= floor($rating)): ?><i class="fas fa-star"></i>
                    <?php elseif ($i - 0.5 <= $rating): ?><i class="fas fa-star-half-alt"></i>
                    <?php else: ?><i class="far fa-star"></i>
                    <?php endif; ?>
                  <?php endfor; ?>
                </span>
                <?php if ($rating > 0): ?><span class="rating-number"><?= number_format($rating, 1) ?></span><?php endif; ?>
              </div>
              <div class="product-price">
                <?= currency($product['price']) ?>
                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                  <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                <?php endif; ?>
              </div>
              <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                <?php if ($stock > 10): ?><i class="fas fa-check-circle"></i> <?= $t['in_stock'] ?? 'In Stock' ?>
                <?php elseif ($stock > 0): ?><i class="fas fa-exclamation-triangle"></i> <?= sprintf($t['low_stock'] ?? 'Only %d left', $stock) ?>
                <?php else: ?><i class="fas fa-times-circle"></i> <?= $t['out_of_stock'] ?? 'Out of Stock' ?>
                <?php endif; ?>
              </div>
              <button class="add-to-cart" data-product-id="<?= $product['id'] ?>" <?= $stock <= 0 ? 'disabled' : '' ?>>
                <i class="fas fa-shopping-cart"></i> <?= $t['add_to_cart'] ?? 'Add to Cart' ?>
              </button>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>

    <!-- Shop-type detail rows (deeper showcase beneath the tile nav above) -->
    <?php
      $mallRows = [
        ['data' => $groceryStoreShops, 'type' => 'grocery', 'badge' => 'grocery', 'icon' => '🛒', 'title_en' => 'Grocery Stores', 'title_fr' => 'Epiceries', 'desc_en' => 'Fresh produce, meats & daily essentials', 'desc_fr' => 'Produits frais, viandes et essentiels'],
        ['data' => $foodCourtShops, 'type' => 'food_court', 'badge' => 'foodcourt', 'icon' => '🍽️', 'title_en' => 'Food Court', 'title_fr' => 'Aire de restauration', 'desc_en' => 'Restaurants, fast food & dining', 'desc_fr' => 'Restaurants et repas prepares'],
        ['data' => $storesShops, 'type' => 'stores', 'badge' => 'store', 'icon' => '🛍️', 'title_en' => 'Stores', 'title_fr' => 'Boutiques', 'desc_en' => 'Clothing, services & specialty shops', 'desc_fr' => 'Vetements, services et specialites'],
        ['data' => $productsShops, 'type' => 'products', 'badge' => 'products', 'icon' => '🎁', 'title_en' => 'More Products', 'title_fr' => 'Autres produits', 'desc_en' => 'Electronics, furniture, toys & more', 'desc_fr' => 'Electronique, meubles, jouets et plus'],
      ];
    ?>
    <?php foreach ($mallRows as $row): if (empty($row['data'])) continue; ?>
    <section class="section">
      <div class="section-header">
        <div>
          <h2 class="section-title"><?= $fr ? $row['title_fr'] : $row['title_en'] ?></h2>
          <p class="mall-subtitle"><?= $fr ? $row['desc_fr'] : $row['desc_en'] ?></p>
        </div>
        <a href="<?= url('shops?type=' . $row['type']) ?>" class="view-all"><?= $t['view_all'] ?? ($fr ? 'Voir tout' : 'View All') ?> →</a>
      </div>
      <div class="products-scroll-container">
        <button class="scroll-btn scroll-btn-left" data-scroll-target="mall<?= $row['type'] ?>Scroll" aria-label="Scroll left">‹</button>
        <button class="scroll-btn scroll-btn-right" data-scroll-target="mall<?= $row['type'] ?>Scroll" aria-label="Scroll right">›</button>
        <div class="products-scroll-grid" id="mall<?= $row['type'] ?>Scroll">
          <?php foreach ($row['data'] as $shop): ?>
          <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
            <div class="shop-badges"><span class="shop-badge <?= $row['badge'] ?>"><?= $fr ? $row['title_fr'] : $row['title_en'] ?></span></div>
            <div class="category-icon shop-mall-logo">
              <?php if (!empty($shop['display_logo'])): ?>
                <img src="<?= $shop['display_logo'] ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
              <?php else: ?>
                <span class="category-icon-placeholder"><?= $row['icon'] ?></span>
              <?php endif; ?>
            </div>
            <div class="category-name"><?= htmlspecialchars($shop['name']) ?></div>
            <div class="shop-meta">
              <div class="shop-rating">
                <span class="stars">
                  <?php $rating = $shop['rating'] ?? 0; for ($i = 0; $i < 5; $i++): echo ($i < floor($rating)) ? '⭐' : '☆'; endfor; ?>
                </span>
                <span><?= number_format($rating, 1) ?></span>
              </div>
              <div class="category-count">⚡ <?= $shop['formatted_delivery_time'] ?? '30 mins' ?></div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endforeach; ?>

    <!-- Sustainability (unchanged - the differentiator vs Amazon/Alibaba) -->
    <section class="sustainability">
      <div class="section-header">
        <h2 class="section-title"><?= $t['zero_carbon_title'] ?? ($fr ? 'Notre mission zero carbone' : 'Our Zero-Carbon Mission') ?></h2>
        <a href="<?= url('about') ?>" class="view-all"><?= $t['learn_more'] ?? ($fr ? 'En savoir plus' : 'Learn More') ?> →</a>
      </div>
      <p><?= $t['zero_carbon_desc'] ?? ($fr ? 'Notre livraison est alimentee par une flotte entierement electrique.' : "Our delivery is powered by an all-electric fleet.") ?></p>
      <div class="s-grid">
        <div class="stat"><h3><?= $t['electric_fleet'] ?? ($fr ? 'Flotte 100% electrique' : '100% Electric Fleet') ?></h3><p><?= $t['electric_fleet_desc'] ?? '' ?></p></div>
        <div class="stat"><h3><?= $t['zero_emissions'] ?? ($fr ? '0g CO2' : '0g CO2') ?></h3><p><?= $t['zero_emissions_desc'] ?? '' ?></p></div>
        <div class="stat"><h3><?= $t['smart_routing'] ?? ($fr ? 'Routage intelligent' : 'Smart Routing') ?></h3><p><?= $t['smart_routing_desc'] ?? '' ?></p></div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../components/footer.php'; ?>

  <script>
    window.OCSAPP_CONFIG = {
      isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
      currentLang: '<?= $currentLang ?>',
      urls: {
        setLanguage: '<?= url('set-language') ?>',
        setLocation: '<?= url('set-location') ?>',
        search: '<?= url('search') ?>',
        cartAdd: '<?= url('cart/add') ?>',
        cartCount: '<?= url('cart/count') ?>',
        wishlistToggle: '<?= url('api/wishlist/toggle') ?>'
      }
    };

    function toggleWishlist(productId) {
      const btn = event.currentTarget;
      const icon = btn.querySelector('i');

      if (icon.classList.contains('far')) {
        icon.classList.remove('far');
        icon.classList.add('fas');
        icon.style.color = '#ef4444';
      } else {
        icon.classList.remove('fas');
        icon.classList.add('far');
        icon.style.color = '#d1d5db';
      }

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
  <script src="<?= asset('js/home.js') ?>"></script>
  <script src="<?= asset('js/smart-scroll.js') ?>"></script>
</body>
</html>
