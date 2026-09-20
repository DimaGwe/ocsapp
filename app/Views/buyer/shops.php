<?php
/**
 * OCS Shops Page - Marché Central taxonomy + results architecture
 * Shows all available shops/stores organized by type
 * Updated: 2026-09-09 - moved onto the mc-header/mc-footer standard shared by
 * /home and /categories, added the taxonomy discovery grid and ecosystem
 * bridge section. Shop-card loop and pagination logic unchanged.
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');

// Get translations
$t = getTranslations($currentLang);

// Set defaults
$shops = $shops ?? [];

// User's delivery location - match header behavior
$defaultLocationText = $t['select_location'] ?? ($currentLang === 'fr' ? 'Choisir votre emplacement' : 'Select your location');
$currentLocation = $_SESSION['location'] ?? $defaultLocationText;

$cartCount = $cartCount ?? 0;
$total = $total ?? 0;
$page = $page ?? 1;
$perPage = $perPage ?? 12;
$search = $search ?? '';
$filterType = $_GET['type'] ?? 'all'; // Filter by shop type

// FIXED: Get counts from database for all types
$shopCounts = $shopCounts ?? [
    'all' => 0,
    'grocery' => 0,
    'food_court' => 0,
    'stores' => 0,
    'products' => 0,
    'bakery' => 0,
    'butcher' => 0,
    'pharmacy' => 0,
    'bookstore' => 0,
    'hardware' => 0,
    'boutique' => 0,
    'electronics' => 0,
    'health_beauty' => 0,
    'food_drink' => 0,
    'automotive' => 0,
    'food_dining' => 0,
    'health_pharmacy' => 0,
    'home_everyday' => 0,
    'home_services' => 0,
    'wellness_beauty' => 0,
    'events_catering' => 0,
    'local_gems' => 0,
];

// Official Marché Central taxonomy (same 11 categories as /categories):
// [icon file, shop-type filter, name FR, name EN, desc FR, desc EN]
$taxonomy = [
    ['icon-food-dining', 'food_dining', 'Restauration', 'Food & Dining', 'Restaurants, prêts-à-manger, cafés et expériences culinaires locales.', 'Restaurants, ready-to-eat meals, cafés and local culinary experiences.'],
    ['icon-grocery', 'grocery', 'Épicerie', 'Grocery', 'Produits frais, garde-manger et essentiels du quotidien.', 'Fresh products, pantry staples and everyday essentials.'],
    ['icon-health-pharmacy', 'health_pharmacy', 'Santé & pharmacie', 'Health & Pharmacy', 'Produits de santé, pharmacie, soins et essentiels bien-être.', 'Health products, pharmacy, care and wellness essentials.'],
    ['icon-boutique', 'boutique', 'Mode & boutiques', 'Fashion & Boutiques', 'Mode, accessoires et commerces spécialisés.', 'Fashion, accessories and specialty shops.'],
    ['icon-wellness-beauty', 'wellness_beauty', 'Bien-être & beauté', 'Wellness & Beauty', 'Beauté, soins personnels, mieux-être et produits spécialisés.', 'Beauty, personal care, wellness and specialty products.'],
    ['icon-events-catering', 'events_catering', 'Événements & traiteur', 'Events & Catering', 'Traiteur, événements, célébrations et services connexes.', 'Catering, events, celebrations and related services.'],
    ['icon-local-gems', 'local_gems', 'Artisans locaux', 'Local Artisans', "Créateurs d'ici, produits faits localement et petites séries.", 'Local makers, locally-made products and small batches.'],
    ['icon-automotive', 'automotive', 'Pièces auto & industrielles', 'Auto & Industrial Parts', 'Pièces automobiles, fournitures techniques et besoins industriels.', 'Auto parts, technical supplies and industrial needs.'],
    ['icon-home-everyday', 'home_everyday', 'Maison & quotidien', 'Home & Everyday', 'Maison, entretien, cuisine et essentiels de la vie courante.', 'Home, cleaning, kitchen and everyday essentials.'],
    ['icon-electronics', 'electronics', 'Électronique & technologie', 'Electronics & Tech', 'Électronique, accessoires, appareils et solutions technologiques.', 'Electronics, accessories, devices and tech solutions.'],
    ['icon-world-flavors', 'food_drink', 'Saveurs du monde', 'World Flavors', 'Produits, spécialités et cuisines représentatives de nos communautés.', 'Products, specialties and cuisines representing our communities.'],
];

// Type filter options shown in the results dropdown - built from the same official
// 11-category taxonomy as the cards above, so the dropdown never drifts from the cards.
$typeFilterOptions = [];
foreach ($taxonomy as $cat) {
    $typeFilterOptions[$cat[1]] = $fr ? $cat[2] : $cat[3];
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Commerces - Marché Central' : 'Shops - Marketplace Central' ?> | OCSAPP</title>
    <meta name="description" content="<?= $fr
        ? "Découvrez les commerces disponibles dans Marché Central et explorez l'offre locale selon votre emplacement."
        : 'Discover the shops available on Marketplace Central and explore local offerings based on your location.' ?>">
    <?= csrfMeta() ?>

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/shops.css') ?>">
</head>
<body>
    <!-- Header (Marché Central variant, consistent with /home and /categories) -->
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('/') ?>"><i class="fas fa-house"></i><span><?= $fr ? 'Accueil' : 'Home' ?></span></a>
                <span class="mc-sep">/</span>
                <a href="<?= url('categories') ?>"><i class="fas fa-grip"></i><span><?= $fr ? 'Catégories' : 'Categories' ?></span></a>
                <span class="mc-sep">/</span>
                <span aria-current="page"><i class="fas fa-store"></i> <?= $t['shops'] ?? ($fr ? 'Commerces' : 'Shops') ?></span>
            </nav>

            <!-- Hero -->
            <section class="mc-shops-hero">
                <div class="mc-shops-hero-kicker"><?= $fr ? 'MARCHÉ CENTRAL · COMMERCES' : 'MARKETPLACE CENTRAL · SHOPS' ?></div>
                <h1><?= $fr ? 'Découvrez les commerces autour de vous' : 'Discover shops around you' ?></h1>
                <p><?= $fr
                    ? "Explorez les commerces de Marché Central selon votre emplacement, vos besoins et les catégories disponibles."
                    : "Explore Marketplace Central shops based on your location, your needs and the categories available." ?></p>

                <div class="mc-shops-location-bar">
                    <div class="mc-shops-location-badge">
                        <i class="fas fa-location-dot"></i>
                        <span id="currentLocationText">
                            <?php if ($currentLocation === 'All Locations'): ?>
                                <?= $t['all_locations'] ?? ($fr ? 'Tous les emplacements' : 'All Locations') ?>: <?= $t['showing_all_shops'] ?? ($fr ? 'Affichage de tous les commerces' : 'Showing all shops') ?>
                            <?php else: ?>
                                <?= htmlspecialchars($currentLocation) ?>: <?= $t['under_radius'] ?? ($fr ? 'Commerces affichés dans un rayon de' : 'Showing shops within') ?> <?= (int) ($_SESSION['delivery_radius'] ?? 20) ?> km
                            <?php endif; ?>
                        </span>
                    </div>
                    <button class="mc-shops-change-location" id="changeLocationBtn" onclick="document.getElementById('locationBtn')?.click()">
                        <i class="fas fa-location-dot"></i>
                        <?= $t['change_location'] ?? ($fr ? "Modifier l'emplacement" : 'Change Location') ?>
                    </button>
                </div>
            </section>

            <!-- Results -->
            <section class="mc-shops-results">
                <div class="mc-results-heading-row">
                    <div>
                        <span class="mc-eyebrow"><?= $fr ? 'COMMERÇANTS DISPONIBLES' : 'AVAILABLE SHOPS' ?></span>
                        <h2><?= $fr ? 'Commerces près de vous' : 'Shops near you' ?></h2>
                        <p><?= $fr
                            ? "Affinez les résultats selon votre emplacement ou votre besoin, sans modifier la taxonomie officielle."
                            : 'Refine results by location or need, without changing the official taxonomy.' ?></p>
                    </div>
                    <div class="mc-discovery-controls">
                        <label class="mc-control-field">
                            <span><?= $fr ? 'Type de commerce' : 'Shop type' ?></span>
                            <select aria-label="<?= $fr ? 'Filtrer par type de commerce' : 'Filter by shop type' ?>" id="shopTypeControl">
                                <option value="<?= url('shops?type=all') ?>" <?= $filterType === 'all' ? 'selected' : '' ?>><?= $t['all_shops'] ?? ($fr ? 'Tous les commerces' : 'All shops') ?></option>
                                <?php foreach ($typeFilterOptions as $typeKey => $typeLabel): ?>
                                    <option value="<?= url('shops?type=' . $typeKey) ?>" <?= $filterType === $typeKey ? 'selected' : '' ?>><?= htmlspecialchars($typeLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label class="mc-control-field">
                            <span><?= $fr ? 'Trier' : 'Sort' ?></span>
                            <select aria-label="<?= $fr ? 'Trier les commerces' : 'Sort shops' ?>" id="shopSortControl">
                                <option value="default"><?= $fr ? 'Pertinence' : 'Relevance' ?></option>
                                <option value="name"><?= $fr ? 'Nom A-Z' : 'Name A-Z' ?></option>
                                <option value="rating"><?= $fr ? 'Mieux notés' : 'Top rated' ?></option>
                            </select>
                        </label>
                        <button class="mc-nearby-control" type="button" onclick="document.getElementById('locationBtn')?.click()">
                            <i class="fas fa-location-dot"></i> <?= $t['change_location'] ?? ($fr ? "Modifier l'emplacement" : 'Change Location') ?>
                        </button>
                    </div>
                </div>

                <div class="mc-results-count-bar">
                    <div class="mc-shop-count">
                        <?php // Not using $t['shops_found'] here - that DB translation is a single fixed
                        // string ("boutiques trouvées") and can't agree with a singular count. ?>
                        <?= count($shops) ?> <?= $fr
                            ? (count($shops) > 1 ? 'commerces trouvés' : 'commerce trouvé')
                            : (count($shops) > 1 ? 'shops found' : 'shop found') ?>
                    </div>
                    <div class="mc-results-hint"><?= $fr
                        ? 'Les catégories décrivent le commerce; les filtres affinent votre recherche.'
                        : 'Categories describe the shop; filters refine your search.' ?></div>
                </div>

                <!-- Shops Grid -->
                <?php if (!empty($shops)): ?>
                    <div class="shops-grid">
                        <?php foreach ($shops as $shop): ?>
                            <?php
                            $shopType = $shop['shop_type'] ?? 'grocery_store';

                            // Shop type labels (keyed by the raw DB enum value)
                            $typeLabels = [
                                'grocery_store' => $t['grocery_store'] ?? 'Grocery',
                                'food_court' => $t['food_court'] ?? 'Restaurant',
                                'store' => $t['stores'] ?? 'Store',
                                'products' => $t['more_products'] ?? 'Products',
                                'bakery' => $t['bakery'] ?? 'Bakery',
                                'butcher' => $t['butcher'] ?? 'Butcher Shop',
                                'pharmacy' => $t['pharmacy'] ?? 'Pharmacy',
                                'bookstore' => $t['bookstore'] ?? 'Bookstore',
                                'hardware' => $t['hardware'] ?? 'Hardware',
                                'boutique' => $t['boutique'] ?? 'Fashion & Local Boutiques',
                                'electronics' => $t['electronics'] ?? 'Electronics',
                                'health_beauty' => $t['health_beauty'] ?? 'Health & Beauty',
                                'food_drink' => $t['food_drink'] ?? 'Food & Drink',
                                'automotive' => $t['automotive'] ?? 'Automotive & Industrial',
                                'food_dining' => $t['food_dining'] ?? 'Food & Dining',
                                'health_pharmacy' => $t['health_pharmacy'] ?? 'Health & Pharmacy',
                                'home_everyday' => $t['home_everyday'] ?? 'Home & Everyday Goods',
                                'home_services' => $t['home_services'] ?? 'Home Services',
                                'wellness_beauty' => $t['wellness_beauty'] ?? 'Wellness & Beauty',
                                'events_catering' => $t['events_catering'] ?? 'Events & Catering',
                                'local_gems' => $t['local_gems'] ?? 'Local Gems',
                            ];
                            ?>
                            <a href="<?= url('shops/' . $shop['slug']) ?>" class="shop-card">
                                <span class="shop-type-badge <?= $shopType ?>">
                                    <?= $typeLabels[$shopType] ?? 'Shop' ?>
                                </span>

                                <div class="shop-logo">
                                    <?php if (!empty($shop['logo'])): ?>
                                        <img src="<?= url($shop['logo']) ?>" alt="<?= htmlspecialchars($shop['name']) ?>">
                                    <?php else: ?>
                                        <span style="font-size: 48px;">
                                            <?php
                                            $icons = [
                                                'grocery_store' => '🛒',
                                                'food_court' => '🍽️',
                                                'store' => '🛍️',
                                                'products' => '🎁',
                                                'bakery' => '🥐',
                                                'butcher' => '🥩',
                                                'pharmacy' => '💊',
                                                'bookstore' => '📚',
                                                'hardware' => '🔧',
                                                'boutique' => '👗',
                                                'electronics' => '🔌',
                                                'health_beauty' => '💄',
                                                'food_drink' => '🥖',
                                                'automotive' => '🚗',
                                                'food_dining' => '🍽️',
                                                'health_pharmacy' => '💊',
                                                'home_everyday' => '🏠',
                                                'home_services' => '🧹',
                                                'wellness_beauty' => '💆',
                                                'events_catering' => '🎉',
                                                'local_gems' => '💎',
                                            ];
                                            echo $icons[$shopType] ?? '🏪';
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="shop-name"><?= htmlspecialchars($shop['name']) ?></div>

                                <?php if (!empty($shop['description'])): ?>
                                    <div class="shop-description">
                                        <?= htmlspecialchars($shop['description']) ?>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($shop['address'])): ?>
                                    <div class="shop-location">
                                        <span>📍</span>
                                        <span><?= htmlspecialchars(explode(',', $shop['address'])[0] ?? $shop['address']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <div class="shop-rating">
                                    <span class="stars">
                                        <?php
                                            $rating = $shop['average_rating'] ?? 0;
                                            for($i = 0; $i < 5; $i++):
                                                echo ($i < floor($rating)) ? '⭐' : '☆';
                                            endfor;
                                        ?>
                                    </span>
                                    <span class="shop-rating-value"><?= number_format($rating, 1) ?></span>
                                </div>

                                <div class="shop-meta">
                                    <div class="shop-meta-item">
                                        <span>📦</span>
                                        <span><?= number_format($shop['product_count'] ?? 0) ?> <?= $t['items'] ?? 'items' ?></span>
                                    </div>
                                    <div class="shop-meta-item">
                                        <span>🕐</span>
                                        <span><?= $shop['packaging_time'] ?? 30 ?> <?= $t['mins'] ?? 'mins' ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total > $perPage): ?>
                        <div class="pagination">
                            <?php
                            $totalPages = ceil($total / $perPage);
                            for ($i = 1; $i <= $totalPages; $i++):
                            ?>
                                <a
                                    href="<?= url('shops?page=' . $i . ($filterType !== 'all' ? '&type=' . $filterType : '') . ($search ? '&search=' . urlencode($search) : '')) ?>"
                                    class="<?= $i === $page ? 'active' : '' ?>"
                                >
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <div style="font-size: 80px; margin-bottom: 20px;">🏪</div>
                        <h2 style="color: #666; margin-bottom: 10px;"><?= $t['no_shops_found'] ?? ($fr ? 'Aucun commerce trouvé' : 'No Shops Found') ?></h2>
                        <p style="color: #999; margin-bottom: 30px;"><?= $t['no_shops_desc'] ?? ($fr ? "Essayez de changer votre emplacement ou parcourez tous les commerces" : 'Try changing your location or browse all shops') ?></p>
                        <button class="mc-shops-change-location" onclick="document.getElementById('locationBtn')?.click()">
                            <?= $t['change_location'] ?? ($fr ? "Modifier l'emplacement" : 'Change Location') ?>
                        </button>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Taxonomy discovery -->
            <section class="mc-taxonomy-discovery">
                <div class="mc-section-head">
                    <span class="mc-eyebrow"><?= $fr ? 'TAXONOMIE OCSAPP' : 'OCSAPP TAXONOMY' ?></span>
                    <h2><?= $fr ? 'Explorer par catégorie' : 'Explore by category' ?></h2>
                    <p><?= $fr
                        ? "Onze catégories officielles pour parcourir les commerces de Marché Central avec la même structure que partout dans l'écosystème."
                        : 'Eleven official categories to browse Marketplace Central shops with the same structure used across the ecosystem.' ?></p>
                </div>
                <div class="mc-taxonomy-discovery-grid">
                    <?php foreach ($taxonomy as $cat):
                        $catCount = $shopCounts[$cat[1]] ?? 0;
                    ?>
                        <a class="mc-taxonomy-discovery-card" href="<?= url('shops?type=' . $cat[1]) ?>">
                            <div class="mc-taxonomy-art-wrap">
                                <img src="<?= asset('images/marketplace-categories/' . $cat[0] . '.jpg') ?>" alt="<?= htmlspecialchars($fr ? $cat[2] : $cat[3]) ?>">
                            </div>
                            <div class="mc-taxonomy-discovery-copy">
                                <h3><?= htmlspecialchars($fr ? $cat[2] : $cat[3]) ?></h3>
                                <p><?= htmlspecialchars($fr ? $cat[4] : $cat[5]) ?></p>
                                <span class="mc-taxonomy-meta">
                                    <?php if ($catCount > 0): ?>
                                        <?= $catCount ?> <?= $fr
                                            ? ($catCount > 1 ? 'commerces disponibles' : 'commerce disponible')
                                            : ($catCount > 1 ? 'shops available' : 'shop available') ?>
                                    <?php else: ?>
                                        <?= $fr ? 'Explorer la catégorie' : 'Explore category' ?>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- Ecosystem bridge -->
            <section class="mc-ecosystem-bridge">
                <div class="mc-section-head mc-bridge-heading">
                    <span class="mc-eyebrow"><?= $fr ? 'CONTINUEZ VOTRE DÉCOUVERTE' : 'CONTINUE EXPLORING' ?></span>
                    <h2><?= $fr ? 'Trouvez ce qu\'il vous faut dans Marché Central' : 'Find what you need on Marketplace Central' ?></h2>
                    <p><?= $fr
                        ? "Passez des commerces aux catégories, aux produits ou à la découverte locale sans quitter l'écosystème OCSAPP."
                        : 'Move from shops to categories, products or local discovery without leaving the OCSAPP ecosystem.' ?></p>
                </div>
                <div class="mc-ecosystem-bridge-grid">
                    <a class="mc-ecosystem-bridge-card" href="<?= url('categories') ?>">
                        <div class="mc-bridge-icon"><i class="fas fa-grip"></i></div>
                        <div><h3><?= $fr ? 'Catégories' : 'Categories' ?></h3><p><?= $fr ? 'Explorer les 11 catégories OCSAPP' : 'Explore the 11 OCSAPP categories' ?></p></div>
                    </a>
                    <a class="mc-ecosystem-bridge-card" href="<?= url('home') ?>">
                        <div class="mc-bridge-icon"><i class="fas fa-bag-shopping"></i></div>
                        <div><h3><?= $fr ? 'Produits' : 'Products' ?></h3><p><?= $fr ? 'Parcourir les produits disponibles' : 'Browse available products' ?></p></div>
                    </a>
                    <a class="mc-ecosystem-bridge-card" href="<?= url('shops') ?>">
                        <div class="mc-bridge-icon"><i class="fas fa-location-dot"></i></div>
                        <div><h3><?= $fr ? 'À proximité' : 'Nearby' ?></h3><p><?= $fr ? 'Découvrir selon votre emplacement' : 'Discover based on your location' ?></p></div>
                    </a>
                    <a class="mc-ecosystem-bridge-card" href="<?= url('home') ?>">
                        <div class="mc-bridge-icon"><i class="fas fa-store"></i></div>
                        <div><h3><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></h3><p><?= $fr ? "Retourner au point d'entrée du marché" : 'Return to the marketplace entry point' ?></p></div>
                    </a>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer (Marché Central variant, matches /home and /categories) -->
    <footer class="mc-footer">
        <div class="mc-footer-wrap">
            <div class="mc-footer-top">
                <div class="mc-footer-brand-col">
                    <div class="mc-footer-brand">
                        <img src="<?= asset('images/logo.png') ?>" alt="<?= $fr ? 'Logo OCSAPP' : 'OCSAPP Logo' ?>">
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

    <?php include __DIR__ . '/../components/auth-popup.php'; ?>

    <script>
        window.OCSAPP_CONFIG = {
            isLoggedIn: <?= function_exists('isLoggedIn') && isLoggedIn() ? 'true' : 'false' ?>,
            currentLang: '<?= $currentLang ?>',
            urls: {
                cartAdd: '<?= url('cart/add') ?>',
                cartCount: '<?= url('cart/count') ?>'
            }
        };
    </script>

    <script src="<?= asset('js/home.js') ?>"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeControl = document.getElementById('shopTypeControl');
        if (typeControl) {
            typeControl.addEventListener('change', function () {
                window.location.href = this.value;
            });
        }

        const sortControl = document.getElementById('shopSortControl');
        const grid = document.querySelector('.mc-shops-results .shops-grid');
        if (sortControl && grid) {
            const originalCards = [...grid.querySelectorAll('.shop-card')];
            sortControl.addEventListener('change', function () {
                let cards = [...originalCards];
                if (this.value === 'name') {
                    cards.sort((a, b) => (a.querySelector('.shop-name')?.textContent || '').localeCompare(b.querySelector('.shop-name')?.textContent || '', 'fr'));
                } else if (this.value === 'rating') {
                    cards.sort((a, b) => parseFloat(b.querySelector('.shop-rating-value')?.textContent || '0') - parseFloat(a.querySelector('.shop-rating-value')?.textContent || '0'));
                }
                cards.forEach(card => grid.appendChild(card));
            });
        }
    });
    </script>
</body>
</html>
