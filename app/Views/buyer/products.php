<?php
/**
 * All products - Marché Central (/products EN, /produits FR)
 * Target of "Voir plus de produits" on /marche-central.
 * Same chrome as /shops (mc-header, hero, mc-footer); product cards reuse the
 * home.css .product-card styling so they match the home page section.
 * Data: HomeController::products().
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
$t = getTranslations($currentLang);

$products = $products ?? [];
$total = (int) ($total ?? 0);
$page = (int) ($page ?? 1);
$perPage = (int) ($perPage ?? 24);
$filters = $filters ?? ['q' => '', 'shop' => '', 'category' => '', 'stock' => false, 'sort' => 'recommended'];
$shopOptions = $shopOptions ?? [];
$categoryOptions = $categoryOptions ?? [];
$totalPages = max(1, (int) ceil($total / $perPage));

$defaultLocationText = $t['select_location'] ?? ($fr ? 'Choisir votre emplacement' : 'Select your location');
$currentLocation = $_SESSION['location'] ?? $defaultLocationText;

// Link to this page keeping the current filters (null removes a key)
$productsUrl = function (array $changes = []) use ($filters) {
    $query = array_merge([
        'q'        => $filters['q'],
        'shop'     => $filters['shop'],
        'category' => $filters['category'],
        'stock'    => $filters['stock'] ? '1' : '',
        'sort'     => $filters['sort'] === 'recommended' ? '' : $filters['sort'],
    ], $changes);
    $query = array_filter($query, fn($v) => $v !== null && $v !== '');
    if (isset($query['page']) && (int) $query['page'] <= 1) {
        unset($query['page']);
    }
    $qs = http_build_query($query);
    return url('products') . ($qs ? '?' . $qs : '');
};
$hasFilters = $filters['q'] !== '' || $filters['shop'] !== '' || $filters['category'] !== '' || $filters['stock'];

$sortLabels = [
    'recommended' => $fr ? 'Recommandés' : 'Recommended',
    'newest'      => $fr ? 'Plus récents' : 'Newest',
    'price_asc'   => $fr ? 'Prix croissant' : 'Price: low to high',
    'price_desc'  => $fr ? 'Prix décroissant' : 'Price: high to low',
    'name'        => $fr ? 'Nom (A à Z)' : 'Name (A to Z)',
];
?>
<!DOCTYPE html>
<html lang="<?= $fr ? 'fr-CA' : 'en-CA' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Tous les produits - Marché Central' : 'All products - Marketplace Central' ?> | OCSAPP</title>
    <meta name="description" content="<?= $fr
        ? "Parcourez tous les produits offerts par les commerces et vendeurs actifs de Marché Central, selon votre secteur."
        : 'Browse every product offered by active shops and sellers on Marketplace Central, based on your area.' ?>">
    <?= seo_lang_links() ?>
    <?= csrfMeta() ?>

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/home.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/shops.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/pages/products.css') ?>">
</head>
<body>
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('marketplace-central') ?>"><i class="fas fa-house"></i><span><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span></a>
                <span class="mc-sep">/</span>
                <span aria-current="page"><i class="fas fa-basket-shopping"></i> <?= $fr ? 'Tous les produits' : 'All products' ?></span>
            </nav>

            <!-- Hero -->
            <section class="mc-shops-hero">
                <div class="mc-shops-hero-kicker"><?= $fr ? 'MARCHÉ CENTRAL · PRODUITS' : 'MARKETPLACE CENTRAL · PRODUCTS' ?></div>
                <h1><?= $fr ? 'Tous les produits' : 'All products' ?></h1>
                <p><?= $fr
                    ? "Une vitrine locale qui évolue avec votre secteur. Les produits affichés proviennent des commerces et vendeurs actifs dans l'écosystème OCSAPP."
                    : 'A local showcase that evolves with your area. Products shown come from active shops and sellers in the OCSAPP ecosystem.' ?></p>

                <div class="mc-shops-location-bar">
                    <div class="mc-shops-location-badge">
                        <i class="fas fa-location-dot"></i>
                        <span>
                            <?php if (empty($_SESSION['user_latitude']) || $currentLocation === 'All Locations'): ?>
                                <?= $fr ? 'Tous les emplacements : affichage de tous les produits' : 'All locations: showing all products' ?>
                            <?php else: ?>
                                <?= htmlspecialchars($currentLocation) ?> : <?= $fr ? 'produits dans un rayon de' : 'products within' ?> <?= (int) ($_SESSION['delivery_radius'] ?? 20) ?> km
                            <?php endif; ?>
                        </span>
                    </div>
                    <button type="button" class="mc-shops-change-location" onclick="document.getElementById('locationBtn')?.click()">
                        <i class="fas fa-location-dot"></i>
                        <?= $t['change_location'] ?? ($fr ? "Modifier l'emplacement" : 'Change location') ?>
                    </button>
                </div>
            </section>

            <!-- Filters + results -->
            <section class="mc-products-results" aria-labelledby="productsHeading">
                <h2 id="productsHeading" class="sr-only"><?= $fr ? 'Résultats' : 'Results' ?></h2>

                <form class="mc-products-filters" method="get" action="<?= url('products') ?>" id="productFilters" role="search">
                    <label class="mc-products-search">
                        <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
                        <input type="search" name="q" value="<?= htmlspecialchars($filters['q']) ?>" maxlength="100"
                               placeholder="<?= $fr ? 'Rechercher un produit ou un commerce' : 'Search a product or a shop' ?>"
                               aria-label="<?= $fr ? 'Rechercher un produit' : 'Search products' ?>">
                    </label>

                    <?php if (count($shopOptions) > 1 || $filters['shop'] !== ''): ?>
                    <label class="mc-control-field">
                        <span><?= $fr ? 'Commerce' : 'Shop' ?></span>
                        <select name="shop" data-autosubmit>
                            <option value=""><?= $fr ? 'Tous les commerces' : 'All shops' ?></option>
                            <?php foreach ($shopOptions as $opt): ?>
                                <option value="<?= htmlspecialchars($opt['slug']) ?>" <?= $filters['shop'] === $opt['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($opt['name']) ?> (<?= (int) $opt['n'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <?php endif; ?>

                    <?php if (!empty($categoryOptions)): ?>
                    <label class="mc-control-field">
                        <span><?= $fr ? 'Catégorie' : 'Category' ?></span>
                        <select name="category" data-autosubmit>
                            <option value=""><?= $fr ? 'Toutes les catégories' : 'All categories' ?></option>
                            <?php foreach ($categoryOptions as $opt): ?>
                                <option value="<?= htmlspecialchars($opt['slug']) ?>" <?= $filters['category'] === $opt['slug'] ? 'selected' : '' ?>><?= htmlspecialchars($opt['name']) ?> (<?= (int) $opt['n'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <?php endif; ?>

                    <label class="mc-control-field">
                        <span><?= $fr ? 'Trier par' : 'Sort by' ?></span>
                        <select name="sort" data-autosubmit>
                            <?php foreach ($sortLabels as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $filters['sort'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label class="mc-products-stock">
                        <input type="checkbox" name="stock" value="1" data-autosubmit <?= $filters['stock'] ? 'checked' : '' ?>>
                        <span><?= $fr ? 'En stock seulement' : 'In stock only' ?></span>
                    </label>

                    <button type="submit" class="mc-products-submit"><?= $fr ? 'Rechercher' : 'Search' ?></button>
                </form>

                <div class="mc-results-count-bar">
                    <span class="mc-shop-count" aria-live="polite">
                        <?php if ($fr): ?>
                            <?= $total ?> produit<?= $total > 1 ? 's' : '' ?><?= $total > $perPage ? ' · page ' . $page . ' sur ' . $totalPages : '' ?>
                        <?php else: ?>
                            <?= $total ?> product<?= $total !== 1 ? 's' : '' ?><?= $total > $perPage ? ' · page ' . $page . ' of ' . $totalPages : '' ?>
                        <?php endif; ?>
                    </span>
                    <?php if ($hasFilters): ?>
                        <a class="mc-products-reset" href="<?= url('products') ?>"><i class="fas fa-rotate-left"></i> <?= $fr ? 'Effacer les filtres' : 'Clear filters' ?></a>
                    <?php endif; ?>
                </div>

                <?php if (!empty($products)): ?>
                <div class="mc-products-grid">
                    <?php foreach ($products as $product):
                        $discount = (int) ($product['discount_percentage'] ?? 0);
                        $stock = (int) ($product['stock_quantity'] ?? 0);
                        $productUrl = url('product/' . ($product['slug'] ?: $product['id']));
                    ?>
                    <article class="product-card">
                        <div class="product-badges">
                            <?php if ($discount > 0): ?>
                                <div class="product-badge sale"><?= $fr ? 'Solde' : 'Sale' ?> <?= $discount ?>%</div>
                            <?php endif; ?>
                            <?php if (!empty($product['is_featured'])): ?>
                                <div class="product-badge featured"><?= $fr ? 'Vedette' : 'Featured' ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="wishlist-btn" onclick="toggleWishlist(<?= (int) $product['id'] ?>)" aria-label="<?= $fr ? 'Ajouter aux favoris' : 'Add to wishlist' ?>">
                            <i class="far fa-heart"></i>
                        </button>
                        <a href="<?= $productUrl ?>" class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="<?= url($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="product-placeholder"><i class="fas fa-box-open"></i></div>
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <?php if (!empty($product['category_name'])): ?>
                                <div class="product-category"><?= htmlspecialchars($product['category_name']) ?></div>
                            <?php endif; ?>
                            <h3 class="product-name"><a href="<?= $productUrl ?>"><?= htmlspecialchars($product['name']) ?></a></h3>
                            <?php if (!empty($product['shop_name'])): ?>
                                <a class="mc-product-shop" href="<?= url('shops/' . $product['shop_slug']) ?>">
                                    <i class="fas fa-store"></i> <?= htmlspecialchars($product['shop_name']) ?><?php if ((int) $product['shop_count'] > 1): ?> <span>+<?= (int) $product['shop_count'] - 1 ?></span><?php endif; ?>
                                </a>
                            <?php endif; ?>
                            <div class="product-price">
                                <?= currency($product['price']) ?>
                                <?php if ($discount > 0): ?>
                                    <span class="old-price"><?= currency($product['compare_at_price']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="stock-status <?= $stock > 10 ? 'in-stock' : ($stock > 0 ? 'low-stock' : 'out-of-stock') ?>">
                                <?php if ($stock > 10): ?>
                                    <i class="fas fa-check-circle"></i> <?= $fr ? 'En stock' : 'In stock' ?>
                                <?php elseif ($stock > 0): ?>
                                    <i class="fas fa-exclamation-triangle"></i> <?= $fr ? "Il n'en reste que $stock" : "Only $stock left" ?>
                                <?php else: ?>
                                    <i class="fas fa-times-circle"></i> <?= $fr ? 'Épuisé' : 'Out of stock' ?>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="add-to-cart" data-product-id="<?= (int) $product['id'] ?>" <?= $stock <= 0 ? 'disabled' : '' ?> aria-label="<?= $fr ? 'Ajouter au panier' : 'Add to cart' ?>">
                                <i class="fas fa-shopping-cart"></i> <?= $fr ? 'Ajouter' : 'Add to cart' ?>
                            </button>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                <nav class="pagination" aria-label="<?= $fr ? 'Pagination' : 'Pagination' ?>">
                    <?php if ($page > 1): ?>
                        <a href="<?= $productsUrl(['page' => $page - 1]) ?>" aria-label="<?= $fr ? 'Page précédente' : 'Previous page' ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $page - 2);
                    $end = min($totalPages, $page + 2);
                    if ($start > 1): ?>
                        <a href="<?= $productsUrl(['page' => 1]) ?>">1</a>
                        <?php if ($start > 2): ?><span class="mc-pagination-gap">…</span><?php endif; ?>
                    <?php endif;
                    for ($i = $start; $i <= $end; $i++): ?>
                        <a href="<?= $productsUrl(['page' => $i]) ?>" class="<?= $i === $page ? 'active' : '' ?>" <?= $i === $page ? 'aria-current="page"' : '' ?>><?= $i ?></a>
                    <?php endfor;
                    if ($end < $totalPages): ?>
                        <?php if ($end < $totalPages - 1): ?><span class="mc-pagination-gap">…</span><?php endif; ?>
                        <a href="<?= $productsUrl(['page' => $totalPages]) ?>"><?= $totalPages ?></a>
                    <?php endif; ?>
                    <?php if ($page < $totalPages): ?>
                        <a href="<?= $productsUrl(['page' => $page + 1]) ?>" aria-label="<?= $fr ? 'Page suivante' : 'Next page' ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                <div class="empty-state">
                    <div class="mc-products-empty-icon"><i class="fas fa-basket-shopping"></i></div>
                    <h3><?= $fr ? 'Aucun produit trouvé' : 'No products found' ?></h3>
                    <p><?= $hasFilters
                        ? ($fr ? 'Essayez une autre recherche ou effacez les filtres.' : 'Try another search or clear the filters.')
                        : ($fr ? "Aucun produit n'est encore offert dans votre secteur. Essayez de modifier votre emplacement." : 'No products are offered in your area yet. Try changing your location.') ?></p>
                    <?php if ($hasFilters): ?>
                        <a class="mc-shops-change-location" href="<?= url('products') ?>"><?= $fr ? 'Effacer les filtres' : 'Clear filters' ?></a>
                    <?php else: ?>
                        <button type="button" class="mc-shops-change-location" onclick="document.getElementById('locationBtn')?.click()"><?= $t['change_location'] ?? ($fr ? "Modifier l'emplacement" : 'Change location') ?></button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </section>

            <!-- Shops bridge -->
            <section class="mc-products-bridge">
                <div>
                    <span class="mc-eyebrow"><?= $fr ? 'Magasiner par commerce' : 'Shop by store' ?></span>
                    <h2><?= $fr ? 'Vous préférez voir les commerces?' : 'Prefer to browse shops?' ?></h2>
                    <p><?= $fr ? 'Découvrez les commerces de votre secteur et leur offre complète.' : 'Discover the shops in your area and everything they offer.' ?></p>
                </div>
                <div class="mc-products-bridge-actions">
                    <a class="mc-products-bridge-btn" href="<?= url('shops') ?>"><i class="fas fa-store"></i> <?= $fr ? 'Voir les commerces' : 'See shops' ?></a>
                    <a class="mc-products-bridge-btn is-ghost" href="<?= url('categories') ?>"><i class="fas fa-grip"></i> <?= $fr ? 'Catégories' : 'Categories' ?></a>
                </div>
            </section>
        </div>
    </div>

    <!-- Footer (Marché Central variant, same as /shops) -->
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
                    <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique relative aux témoins' : 'Cookie Policy' ?></a>
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
                cartCount: '<?= url('cart/count') ?>',
                wishlistToggle: '<?= url('api/wishlist/toggle') ?>'
            }
        };

        function toggleWishlist(productId) {
            const btn = event.currentTarget;
            const icon = btn.querySelector('i');
            const flip = () => {
                const on = icon.classList.contains('far');
                icon.classList.toggle('far', !on);
                icon.classList.toggle('fas', on);
                icon.style.color = on ? '#ef4444' : '#d1d5db';
            };
            flip();
            fetch(window.OCSAPP_CONFIG.urls.wishlistToggle, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(r => r.json())
            .then(data => { if (!data.success) flip(); })
            .catch(() => flip());
        }

        // Filters apply as soon as a select or the stock box changes
        document.querySelectorAll('#productFilters [data-autosubmit]').forEach(el => {
            el.addEventListener('change', () => document.getElementById('productFilters').submit());
        });
    </script>
    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
