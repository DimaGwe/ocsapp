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
  <title><?= $currentLang === 'fr' ? 'Marché Central - Magasinez local sur OCSAPP' : 'Marketplace Central - Shop Local on OCSAPP' ?></title>
  <meta name="description" content="<?= $currentLang === 'fr'
      ? "Découvrez les commerces, boutiques, restaurants et produits d'ici dans Marché Central, la couche commerce de l'écosystème OCSAPP."
      : 'Discover local shops, boutiques, restaurants and homegrown products in Marketplace Central, the commerce layer of the OCSAPP ecosystem.'
  ?>">
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
  <!-- Header (includes beta notice, top banner, location modal, mobile nav) -->
  <?php $useMarcheHeader = true; ?>
  <?php include __DIR__ . '/../components/header.php'; ?>

  <!-- Main Content -->
  <main class="page">
    <?php $fr = ($currentLang === 'fr'); ?>

    <!-- ============================================ -->
    <!-- MARCHÉ CENTRAL REDESIGN (staging, 2026-09-05) -->
    <!-- ============================================ -->

    <!-- Hero -->
    <section class="mc-hero">
      <div class="mc-hero-grid">
        <div>
          <span class="mc-eyebrow"><?= $fr ? 'Marché Central · OCSAPP' : 'Marketplace Central · OCSAPP' ?></span>
          <?php if ($fr): ?>
            <h1 class="mc-h1">Découvrez ce qui se passe <span>près de chez vous.</span></h1>
          <?php else: ?>
            <h1 class="mc-h1">Discover what's happening <span>near you.</span></h1>
          <?php endif; ?>
          <p class="mc-hero-sub"><?= $fr
            ? "Commerces locaux, restaurants, épiceries, boutiques spécialisées et produits d'ici - réunis dans une expérience de magasinage connectée à l'écosystème OCSAPP."
            : 'Local shops, restaurants, groceries, specialty boutiques and homegrown products - brought together in one shopping experience connected to the OCSAPP ecosystem.'
          ?></p>
          <div class="mc-hero-actions">
            <a class="mc-btn mc-btn-primary" href="<?= url('shops') ?>"><?= $fr ? 'Explorer les commerces' : 'Explore shops' ?></a>
            <a class="mc-btn mc-btn-secondary" href="<?= url('categories') ?>"><?= $fr ? 'Voir les catégories' : 'Browse categories' ?></a>
          </div>
          <div class="mc-hero-note"><i class="fa-solid fa-location-dot"></i><span><?= $fr
            ? "Votre expérience s'adapte à la zone de magasinage sélectionnée."
            : 'Your experience adapts to your selected shopping zone.'
          ?></span></div>
        </div>
        <div class="mc-hero-visual">
          <div class="mc-market-icon"><img src="<?= asset('images/centrals/icon-marketplace.jpg') ?>" alt="<?= $fr ? 'Icône officielle Marché Central' : 'Official Marketplace Central icon' ?>"></div>
          <h3><?= $fr ? 'Votre marché local, dans un seul endroit.' : 'Your local market, all in one place.' ?></h3>
          <p><?= $fr
            ? "Magasinez selon ce dont vous avez besoin, pas selon la plateforme qu'il faut ouvrir."
            : 'Shop by what you need, not by which app you have to open.'
          ?></p>
          <div class="mc-market-tags">
            <span><?= $fr ? 'Restauration' : 'Food &amp; Dining' ?></span>
            <span><?= $fr ? 'Épicerie' : 'Grocery' ?></span>
            <span><?= $fr ? 'Santé &amp; pharmacie' : 'Health &amp; Pharmacy' ?></span>
            <span><?= $fr ? 'Mode &amp; boutiques' : 'Fashion &amp; Boutiques' ?></span>
            <span><?= $fr ? 'Bien-être &amp; beauté' : 'Wellness &amp; Beauty' ?></span>
            <span><?= $fr ? 'Événements &amp; traiteur' : 'Events &amp; Catering' ?></span>
            <span><?= $fr ? 'Artisans locaux' : 'Local Artisans' ?></span>
            <span><?= $fr ? 'Pièces auto &amp; industrielles' : 'Auto &amp; Industrial Parts' ?></span>
            <span><?= $fr ? 'Maison &amp; quotidien' : 'Home &amp; Everyday' ?></span>
            <span><?= $fr ? 'Électronique &amp; technologie' : 'Electronics &amp; Tech' ?></span>
            <span><?= $fr ? 'Saveurs du monde' : 'World Flavors' ?></span>
          </div>
        </div>
      </div>
    </section>

    <!-- Quick links -->
    <section class="mc-quick">
      <div class="mc-quick-grid">
        <a class="mc-quick-card" href="<?= url('categories') ?>"><div class="mc-quick-ico"><i class="fa-solid fa-table-cells-large"></i></div><div><strong><?= $fr ? 'Catégories' : 'Categories' ?></strong><span><?= $fr ? 'Explorer par besoin' : 'Browse by need' ?></span></div></a>
        <a class="mc-quick-card" href="<?= url('shops') ?>"><div class="mc-quick-ico"><i class="fa-solid fa-store"></i></div><div><strong><?= $fr ? 'Commerces' : 'Shops' ?></strong><span><?= $fr ? 'Voir les boutiques' : 'View storefronts' ?></span></div></a>
        <a class="mc-quick-card" href="#mc-nearby"><div class="mc-quick-ico"><i class="fa-solid fa-location-crosshairs"></i></div><div><strong><?= $fr ? 'À proximité' : 'Nearby' ?></strong><span><?= $fr ? 'Découvrir autour de vous' : 'Discover around you' ?></span></div></a>
        <a class="mc-quick-card" href="#mc-produits"><div class="mc-quick-ico"><i class="fa-solid fa-bag-shopping"></i></div><div><strong><?= $fr ? 'Produits' : 'Products' ?></strong><span><?= $fr ? 'Magasiner maintenant' : 'Shop now' ?></span></div></a>
        <a class="mc-quick-card" href="<?= url('deals') ?>"><div class="mc-quick-ico"><i class="fa-solid fa-truck-fast"></i></div><div><strong><?= $fr ? 'Livraison' : 'Delivery' ?></strong><span><?= $fr ? 'Rapide ou planifiée' : 'Fast or scheduled' ?></span></div></a>
      </div>
    </section>

    <!-- Category taxonomy (informational) -->
    <section class="mc-section mc-section-soft">
      <div class="mc-section-head">
        <div><span class="mc-eyebrow"><?= $fr ? 'Magasinez par besoin' : 'Shop by need' ?></span><h2><?= $fr ? 'Tout ce qu\'il vous faut, plus simplement.' : 'Everything you need, more simply.' ?></h2></div>
        <a class="mc-see-all" href="<?= url('categories') ?>"><?= $fr ? 'Voir toutes les catégories' : 'See all categories' ?> →</a>
      </div>
      <div class="mc-taxonomy-grid">
        <?php
        $taxonomy = [
          ['icon-food-dining', $fr ? 'Restauration' : 'Food & Dining', $fr ? 'Restaurants, prêts-à-manger et saveurs locales.' : 'Restaurants, ready-to-eat and local flavors.'],
          ['icon-grocery', $fr ? 'Épicerie' : 'Grocery', $fr ? 'Produits frais et essentiels du quotidien.' : 'Fresh produce and everyday essentials.'],
          ['icon-health-pharmacy', $fr ? 'Santé & pharmacie' : 'Health & Pharmacy', $fr ? 'Produits de santé, soins et essentiels bien-être.' : 'Health products, care and wellness essentials.'],
          ['icon-boutique', $fr ? 'Mode & boutiques' : 'Fashion & Boutiques', $fr ? 'Mode, accessoires et commerces spécialisés.' : 'Fashion, accessories and specialty shops.'],
          ['icon-wellness-beauty', $fr ? 'Bien-être & beauté' : 'Wellness & Beauty', $fr ? 'Soins personnels, beauté et mieux-être.' : 'Personal care, beauty and wellbeing.'],
          ['icon-events-catering', $fr ? 'Événements & traiteur' : 'Events & Catering', $fr ? 'Services et produits pour vos occasions.' : 'Services and products for your occasions.'],
          ['icon-local-gems', $fr ? 'Artisans locaux' : 'Local Artisans', $fr ? 'Créateurs, produits faits ici et petites séries.' : 'Makers, locally-made goods and small batches.'],
          ['icon-automotive', $fr ? 'Pièces auto & industrielles' : 'Auto & Industrial Parts', $fr ? 'Pièces, fournitures et besoins spécialisés.' : 'Parts, supplies and specialized needs.'],
          ['icon-home-everyday', $fr ? 'Maison & quotidien' : 'Home & Everyday', $fr ? 'Essentiels pour la maison et la vie courante.' : 'Essentials for home and everyday life.'],
          ['icon-electronics', $fr ? 'Électronique & technologie' : 'Electronics & Tech', $fr ? 'Technologie, accessoires et appareils utiles.' : 'Technology, accessories and useful devices.'],
          ['icon-world-flavors', $fr ? 'Saveurs du monde' : 'World Flavors', $fr ? 'Produits et cuisines qui reflètent nos communautés.' : 'Products and cuisines that reflect our communities.'],
        ];
        foreach ($taxonomy as $cat):
        ?>
          <a class="mc-taxonomy-card" href="<?= url('categories') ?>">
            <div class="mc-taxonomy-art"><img src="<?= asset('images/marketplace-categories/' . $cat[0] . '.jpg') ?>" alt=""></div>
            <h3><?= $cat[1] ?></h3>
            <p><?= $cat[2] ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Nearby shops (real data) -->
    <section class="mc-section" id="mc-nearby">
      <div class="mc-section-head">
        <div><span class="mc-eyebrow"><?= $fr ? 'À proximité' : 'Nearby' ?></span><h2><?= $fr ? 'Découvrez les commerces autour de vous.' : 'Discover shops around you.' ?></h2><p><?= $fr ? "Votre zone sélectionnée aide Marché Central à mettre de l'avant les options locales pertinentes." : 'Your selected zone helps Marketplace Central surface relevant local options.' ?></p></div>
        <a class="mc-see-all" href="<?= url('shops') ?>"><?= $fr ? 'Voir tous les commerces' : 'See all shops' ?> →</a>
      </div>
      <?php
      $shopTypeMeta = [
        'grocery_store'   => [$fr ? 'Épicerie' : 'Grocery', 'fa-basket-shopping'],
        'food_dining'     => [$fr ? 'Restauration' : 'Food & Dining', 'fa-bowl-food'],
        'boutique'        => [$fr ? 'Mode & boutiques' : 'Fashion & Boutiques', 'fa-bag-shopping'],
        'home_everyday'   => [$fr ? 'Maison & quotidien' : 'Home & Everyday', 'fa-house'],
        'health_pharmacy' => [$fr ? 'Santé & pharmacie' : 'Health & Pharmacy', 'fa-briefcase-medical'],
        'local_gems'      => [$fr ? 'Artisans locaux' : 'Local Artisans', 'fa-gem'],
        'home_services'   => [$fr ? 'Services à domicile' : 'Home Services', 'fa-screwdriver-wrench'],
        'wellness_beauty' => [$fr ? 'Bien-être & beauté' : 'Wellness & Beauty', 'fa-spa'],
        'events_catering' => [$fr ? 'Événements & traiteur' : 'Events & Catering', 'fa-champagne-glasses'],
      ];
      $nearbyShops = array_slice($topVendors, 0, 6);
      ?>
      <?php if (!empty($nearbyShops)): ?>
      <div class="mc-shop-grid">
        <?php foreach ($nearbyShops as $shop):
          $typeMeta = $shopTypeMeta[$shop['shop_type'] ?? ''] ?? [$fr ? 'Commerce local' : 'Local shop', 'fa-store'];
        ?>
          <a class="mc-shop-card" href="<?= url('shops/' . ($shop['slug'] ?? '')) ?>">
            <div class="mc-shop-cover">
              <?php if (!empty($shop['logo'])): ?>
                <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['company_name']) ?>">
              <?php else: ?>
                <i class="fa-solid <?= $typeMeta[1] ?>"></i>
              <?php endif; ?>
            </div>
            <div class="mc-shop-body">
              <h3><?= htmlspecialchars($shop['company_name']) ?></h3>
              <p><?= $fr
                  ? ($shop['product_count'] . ' produits disponibles')
                  : ($shop['product_count'] . ' products available')
              ?></p>
              <div class="mc-shop-meta">
                <span class="mc-chip"><?= $typeMeta[0] ?></span>
                <?php if (!empty($shop['min_price'])): ?>
                  <span class="mc-chip"><?= $fr ? 'Dès' : 'From' ?> <?= currency($shop['min_price']) ?></span>
                <?php endif; ?>
              </div>
              <span class="mc-shop-link"><?= $fr ? 'Visiter' : 'Visit' ?> →</span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p class="mc-empty-note"><?= $fr ? 'Aucun commerce actif pour le moment.' : 'No active shops right now.' ?></p>
      <?php endif; ?>
    </section>

    <!-- Products to discover (real data) -->
    <section class="mc-section mc-section-soft" id="mc-produits">
      <div class="mc-section-head">
        <div><span class="mc-eyebrow"><?= $fr ? 'Produits à découvrir' : 'Products to discover' ?></span><h2><?= $fr ? 'Une vitrine locale qui évolue avec votre secteur.' : 'A local showcase that evolves with your area.' ?></h2><p><?= $fr ? "Les produits affichés proviennent des commerces et vendeurs actifs dans l'écosystème OCSAPP." : 'Products shown come from active shops and sellers in the OCSAPP ecosystem.' ?></p></div>
        <a class="mc-see-all" href="<?= url('best-sellers') ?>"><?= $fr ? 'Voir plus de produits' : 'See more products' ?> →</a>
      </div>
      <?php $discoverProducts = array_slice($mostSellingProducts, 0, 8); ?>
      <?php if (!empty($discoverProducts)): ?>
      <div class="mc-product-grid">
        <?php foreach ($discoverProducts as $product):
            $discount = $product['discount_percentage'] ?? 0;
            $stock = $product['stock_quantity'] ?? 100;
        ?>
            <article class="product-card">
                <div class="product-badges">
                    <?php if ($discount > 0): ?>
                        <div class="product-badge sale"><?= $fr ? 'Solde' : 'Sale' ?> <?= $discount ?>%</div>
                    <?php endif; ?>
                    <?php if (!empty($product['is_featured'])): ?>
                        <div class="product-badge featured">⭐ <?= $fr ? 'Vedette' : 'Featured' ?></div>
                    <?php endif; ?>
                </div>
                <button class="wishlist-btn" onclick="toggleWishlist(<?= $product['id'] ?>)" aria-label="<?= $fr ? 'Ajouter aux favoris' : 'Add to wishlist' ?>">
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
                    <h3 class="product-name">
                        <a href="<?= url('product/' . ($product['slug'] ?? $product['id'])) ?>"><?= htmlspecialchars($product['name']) ?></a>
                    </h3>
                    <div class="product-price">
                        <?= currency($product['price']) ?>
                        <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                            <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                        <?php if ($stock > 10): ?>
                            <i class="fas fa-check-circle"></i> <?= $fr ? 'En stock' : 'In Stock' ?>
                        <?php elseif ($stock > 0): ?>
                            <i class="fas fa-exclamation-triangle"></i> <?= $fr ? "Il n'en reste que $stock" : "Only $stock left" ?>
                        <?php else: ?>
                            <i class="fas fa-times-circle"></i> <?= $fr ? 'Épuisé' : 'Out of Stock' ?>
                        <?php endif; ?>
                    </div>
                    <button class="add-to-cart" data-product-id="<?= $product['id'] ?>" <?= $stock <= 0 ? 'disabled' : '' ?> aria-label="<?= $fr ? 'Ajouter au panier' : 'Add to Cart' ?>">
                        <i class="fas fa-shopping-cart"></i> <?= $fr ? 'Ajouter' : 'Add to Cart' ?>
                    </button>
                </div>
            </article>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
        <p class="mc-empty-note"><?= $fr ? 'Aucun produit à afficher pour le moment.' : 'No products to show right now.' ?></p>
      <?php endif; ?>
    </section>

    <!-- Ecosystem cross-links -->
    <section class="mc-section">
      <div class="mc-eco-band">
        <div>
          <span class="mc-eyebrow" style="color:#A4EFA8"><?= $fr ? "Un marché relié au reste d'OCSAPP" : 'A marketplace connected to the rest of OCSAPP' ?></span>
          <h2><?= $fr ? "Marché Central n'est pas une boutique isolée." : 'Marketplace Central is not an isolated storefront.' ?></h2>
          <p><?= $fr
            ? "Les vendeurs, fournisseurs, entreprises, acheteurs et livreurs utilisent des Centrales reliées à la même infrastructure. Marché Central est la couche de découverte et de magasinage de cet écosystème."
            : 'Sellers, suppliers, businesses, buyers and drivers use Centrals connected to the same infrastructure. Marketplace Central is the discovery and shopping layer of that ecosystem.'
          ?></p>
        </div>
        <div class="mc-eco-links">
          <a class="mc-eco-link" href="<?= url('seller-central') ?>"><img src="<?= asset('images/centrals/icon-seller.jpg') ?>" alt=""><span><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></span></a>
          <a class="mc-eco-link" href="<?= url('supplier-central') ?>"><img src="<?= asset('images/centrals/icon-supplier.jpg') ?>" alt=""><span><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></span></a>
          <a class="mc-eco-link" href="<?= url('distribution') ?>"><img src="<?= asset('images/centrals/icon-business.jpg') ?>" alt=""><span><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></span></a>
          <a class="mc-eco-link" href="<?= url('buyer-central') ?>"><img src="<?= asset('images/centrals/icon-buyer.jpg') ?>" alt=""><span><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></span></a>
          <a class="mc-eco-link" href="<?= url('driver-central') ?>"><img src="<?= asset('images/centrals/icon-driver.jpg') ?>" alt=""><span><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></span></a>
        </div>
      </div>
    </section>

    <!-- Delivery -->
    <section class="mc-section mc-section-soft" id="mc-livraison">
      <div class="mc-delivery">
        <div class="mc-delivery-visual">
          <div>
            <div class="mc-delivery-icon"><i class="fa-solid fa-truck-fast"></i></div>
            <h3><?= $fr ? "La livraison fait partie de l'expérience Marché." : 'Delivery is part of the Marketplace experience.' ?></h3>
            <p><?= $fr ? "Une couche logistique reliée à l'écosystème OCSAPP." : 'A logistics layer connected to the OCSAPP ecosystem.' ?></p>
          </div>
        </div>
        <div class="mc-delivery-panel">
          <span class="mc-eyebrow"><?= $fr ? 'Livraison OCSAPP' : 'OCSAPP Delivery' ?></span>
          <h2><?= $fr ? "Du commerce jusqu'à votre porte, dans le même système." : 'From shop to your door, in the same system.' ?></h2>
          <p><?= $fr
            ? "Marché Central est conçu pour relier le magasinage, la commande et la livraison dans une même expérience. La livraison OCSAPP évolue avec un objectif de progression vers un réseau zéro émission."
            : 'Marketplace Central is built to connect shopping, ordering and delivery into one experience. OCSAPP delivery is evolving toward a zero-emission network.'
          ?></p>
          <div class="mc-objective"><i class="fa-solid fa-leaf"></i> <?= $fr ? 'Objectif zéro émission' : 'Zero-emission objective' ?></div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer (matches ocsapp.ca landing page footer) -->
  <footer class="mc-footer">
    <div class="mc-footer-wrap">
      <div class="mc-footer-top">
        <div class="mc-footer-brand-col">
          <div class="mc-footer-brand">
            <img alt="Logo OCSAPP" src="<?= asset('images/logo.png') ?>">
            <span class="mc-footer-logo-text">OCSAPP</span>
          </div>
          <p class="mc-footer-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
          <p><?= $fr
            ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
            : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
          ?></p>
          <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
        </div>

        <div class="mc-footer-col">
          <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
          <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
          <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
        </div>

        <div class="mc-footer-col">
          <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
          <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
          <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
          <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
          <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
          <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
          <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
        </div>

        <div class="mc-footer-col">
          <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
          <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
          <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
          <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
        </div>
      </div>

      <div class="mc-footer-bottom">
        <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
        <div class="mc-footer-legal">
          <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
          <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
          <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
          <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
          <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
        </div>
      </div>
    </div>
  </footer>

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