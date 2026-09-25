<?php
/**
 * Categories Page - Marché Central taxonomy
 */

// Get current language and translations
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);
$fr = ($currentLang === 'fr');

$cartCount = $cartCount ?? 0;

// Official Marché Central taxonomy: [icon file, shop-type filter, name FR, name EN, desc FR, desc EN]
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
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Catégories - Marché Central' : 'Categories - Marketplace Central' ?> | OCSAPP</title>
    <meta name="description" content="<?= $fr
        ? "Explorez les catégories de Marché Central : restauration, épicerie, santé, mode et plus, pour trouver rapidement ce qu'il vous faut sur OCSAPP."
        : 'Browse Marketplace Central categories: dining, grocery, health, fashion and more, to quickly find what you need on OCSAPP.' ?>">
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Modular CSS Architecture -->
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/categories.css') ?>">
</head>
<body>
    <!-- Header (Marché Central variant, consistent with /home) -->
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('marketplace-central') ?>"><i class="fas fa-store"></i><span><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span></a>
                <span class="mc-sep">/</span>
                <span aria-current="page"><?= $fr ? 'Catégories' : 'Categories' ?></span>
            </nav>

            <section class="mc-category-hero">
                <div class="mc-category-hero-copy">
                    <span class="mc-eyebrow"><?= $fr ? 'Marché Central · Taxonomie OCSAPP' : 'Marketplace Central · OCSAPP Taxonomy' ?></span>
                    <h1><?= $fr ? 'Trouvez plus vite ce qui vous convient.' : 'Find what you need, faster.' ?></h1>
                    <p><?= $fr
                        ? "Une structure de catégories simplifiée pour découvrir les produits, commerces et services d'ici selon vos besoins, dans le même écosystème OCSAPP."
                        : 'A simplified category structure to discover local products, shops and services by need, all within the same OCSAPP ecosystem.' ?></p>
                </div>
                <div class="mc-category-hero-note mc-market-identity">
                    <div class="mc-market-identity-icon">
                        <img src="<?= asset('images/marche-central-icon.png') ?>" alt="<?= $fr ? 'Icône officielle Marché Central' : 'Official Marketplace Central icon' ?>">
                    </div>
                    <div class="mc-market-identity-copy">
                        <span class="mc-market-label"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span>
                        <strong><?= $fr ? 'Découvrir & magasiner' : 'Discover & shop' ?></strong>
                        <span class="mc-market-sub"><?= $fr
                            ? "Votre point d'entrée pour explorer les catégories, commerces et produits de l'écosystème OCSAPP."
                            : 'Your entry point to explore the categories, shops and products of the OCSAPP ecosystem.' ?></span>
                    </div>
                </div>
            </section>

            <section class="mc-taxonomy-section" aria-labelledby="taxonomy-title">
                <div class="mc-section-head">
                    <span class="mc-eyebrow"><?= $fr ? 'Explorer par besoin' : 'Explore by need' ?></span>
                    <h2 id="taxonomy-title"><?= $fr ? 'Les catégories Marché Central.' : 'The Marketplace Central categories.' ?></h2>
                </div>
                <div class="mc-taxonomy-grid">
                    <?php foreach ($taxonomy as $cat): ?>
                        <a class="mc-tax-card-primary" href="<?= url('shops?type=' . $cat[1]) ?>" data-category="<?= htmlspecialchars($fr ? $cat[2] : $cat[3]) ?>">
                            <img src="<?= asset('images/marketplace-categories/' . $cat[0] . '.jpg') ?>" alt="<?= htmlspecialchars($fr ? $cat[2] : $cat[3]) ?>">
                            <div class="mc-tax-content">
                                <h3><?= htmlspecialchars($fr ? $cat[2] : $cat[3]) ?></h3>
                                <p><?= htmlspecialchars($fr ? $cat[4] : $cat[5]) ?></p>
                                <span class="mc-tax-cta"><?= $fr ? 'Explorer' : 'Explore' ?> <i class="fas fa-arrow-right"></i></span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer (Marché Central variant, matches /home) -->
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
                    <a href="<?= url('marketplace-central') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
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

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
