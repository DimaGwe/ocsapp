<?php
/**
 * Search Results Page
 * File: app/Views/buyer/search.php
 */

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';

// Get translations
$t = getTranslations($currentLang);

// Variables from controller
$query = $query ?? '';
$products = $products ?? [];
$shops = $shops ?? [];
$storeLocation = 'Kirkland, QC';
$cartCount = $cartCount ?? 0;

$pageTitle = $t['search_results'] ?? 'Search Results';
?>
<!DOCTYPE html>
<html lang="<?= $currentLang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($query) ?> - <?= $pageTitle ?> | <?= env('APP_NAME', 'OCSAPP') ?></title>
    <?= csrfMeta() ?>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .search-results-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        .search-header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }
        .search-header h1 {
            font-size: 24px;
            color: var(--gray-900);
            margin-bottom: 8px;
        }
        .search-header p {
            color: var(--gray-600);
            font-size: 14px;
        }
        .search-tabs {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border);
        }
        .search-tab {
            padding: 12px 20px;
            font-weight: 600;
            color: var(--gray-600);
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }
        .search-tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }
        .search-tab:hover {
            color: var(--primary);
        }
        .tab-count {
            background: var(--gray-100);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }
        .search-tab.active .tab-count {
            background: var(--primary-50);
            color: var(--primary);
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: var(--gray-600);
        }
        .no-results i {
            font-size: 48px;
            color: var(--gray-300);
            margin-bottom: 16px;
        }
        .no-results h3 {
            font-size: 20px;
            color: var(--gray-800);
            margin-bottom: 8px;
        }
        .shops-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .shop-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }
        .shop-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .shop-card h3 {
            font-size: 18px;
            margin-bottom: 8px;
        }
        .shop-card h3 a {
            color: var(--gray-900);
            text-decoration: none;
        }
        .shop-card h3 a:hover {
            color: var(--primary);
        }
        .shop-meta {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: var(--gray-600);
        }
        .shop-meta span {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
        .product-shop {
            font-size: 12px;
            color: var(--gray-500);
            margin-bottom: 8px;
        }
        @media (max-width: 768px) {
            .search-tabs {
                overflow-x: auto;
            }
            .search-tab {
                white-space: nowrap;
                padding: 10px 16px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Banner -->
    <div class="top-banner">
        <?= $t['store_location'] ?>: <?= htmlspecialchars($storeLocation) ?> |
        <?= $t['need_help'] ?>: <a href="tel:+18005551234">+1 (800) 555-1234</a>
    </div>

    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <main class="page">
        <div class="search-results-container">
            <!-- Search Header -->
            <div class="search-header">
                <h1><?= $t['search_results_for'] ?? 'Search results for' ?> "<?= htmlspecialchars($query) ?>"</h1>
                <p><?= count($products) ?> <?= $t['products_found'] ?? 'products found' ?><?= count($shops) > 0 ? ', ' . count($shops) . ' ' . ($t['shops_found'] ?? 'shops found') : '' ?></p>
            </div>

            <!-- Tabs -->
            <div class="search-tabs">
                <div class="search-tab active" data-tab="products">
                    <?= $t['products'] ?? 'Products' ?>
                    <span class="tab-count"><?= count($products) ?></span>
                </div>
                <?php if (count($shops) > 0): ?>
                <div class="search-tab" data-tab="shops">
                    <?= $t['shops'] ?? 'Shops' ?>
                    <span class="tab-count"><?= count($shops) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Products Tab -->
            <div class="tab-content active" id="products-tab">
                <?php if (empty($products)): ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3><?= $t['no_products_found'] ?? 'No products found' ?></h3>
                    <p><?= $t['try_different_search'] ?? 'Try a different search term or browse our categories' ?></p>
                    <a href="<?= url('categories') ?>" class="btn btn-primary" style="margin-top: 16px; display: inline-block; padding: 12px 24px; background: var(--primary); color: white; border-radius: var(--radius-md); text-decoration: none;">
                        <?= $t['browse_categories'] ?? 'Browse Categories' ?>
                    </a>
                </div>
                <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="<?= url('product/' . $product['slug']) ?>" class="product-image">
                            <?php
                            $imageUrl = !empty($product['image'])
                                ? (strpos($product['image'], 'http') === 0 ? $product['image'] : asset($product['image']))
                                : asset('images/placeholder.png');
                            ?>
                            <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                            <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                                <?php $discount = round((1 - $product['price'] / $product['compare_at_price']) * 100); ?>
                                <span class="badge sale">-<?= $discount ?>%</span>
                            <?php endif; ?>
                        </a>
                        <div class="product-info">
                            <a href="<?= url('product/' . $product['slug']) ?>" class="product-name">
                                <?= htmlspecialchars($product['name']) ?>
                            </a>
                            <?php if (!empty($product['shop_name'])): ?>
                            <p class="product-shop"><?= htmlspecialchars($product['shop_name']) ?></p>
                            <?php endif; ?>
                            <div class="product-price">
                                <span class="current-price">$<?= number_format($product['price'], 2) ?></span>
                                <?php if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']): ?>
                                <span class="original-price">$<?= number_format($product['compare_at_price'], 2) ?></span>
                                <?php endif; ?>
                            </div>
                            <button class="add-to-cart" data-product-id="<?= $product['id'] ?>">
                                <i class="fas fa-cart-plus"></i> <?= $t['add_to_cart'] ?? 'Add to Cart' ?>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Shops Tab -->
            <?php if (count($shops) > 0): ?>
            <div class="tab-content" id="shops-tab">
                <div class="shops-grid">
                    <?php foreach ($shops as $shop): ?>
                    <div class="shop-card">
                        <h3><a href="<?= url('shop/' . $shop['slug']) ?>"><?= htmlspecialchars($shop['name']) ?></a></h3>
                        <?php if (!empty($shop['description'])): ?>
                        <p style="color: var(--gray-600); font-size: 14px; margin-bottom: 12px;"><?= htmlspecialchars(substr($shop['description'], 0, 100)) ?>...</p>
                        <?php endif; ?>
                        <div class="shop-meta">
                            <?php if (!empty($shop['average_rating'])): ?>
                            <span><i class="fas fa-star" style="color: #f59e0b;"></i> <?= number_format($shop['average_rating'], 1) ?></span>
                            <?php endif; ?>
                            <span><i class="fas fa-box"></i> <?= $shop['product_count'] ?? 0 ?> products</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <!-- Mobile Bottom Nav -->
    <?php include __DIR__ . '/../components/mobile-nav.php'; ?>

    <script>
    // OCS Config for header JS
    window.OCS_CONFIG = {
        urls: {
            setLocation: '<?= url("set-location") ?>',
            setLanguage: '<?= url("set-language") ?>',
            cartAdd: '<?= url("cart/add") ?>',
            cartCount: '<?= url("cart/count") ?>'
        }
    };

    // Tab switching
    document.querySelectorAll('.search-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.search-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            const tabName = this.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabName + '-tab').classList.add('active');
        });
    });

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const originalHtml = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            try {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', 1);
                formData.append('<?= csrfName() ?>', '<?= csrfToken() ?>');

                const response = await fetch('<?= url("cart/add") ?>', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    this.innerHTML = '<i class="fas fa-check"></i> Added!';
                    this.style.background = '#28a745';

                    const cartCount = document.getElementById('cartCount');
                    if (cartCount && data.cart_count) {
                        cartCount.textContent = data.cart_count;
                        cartCount.style.display = 'flex';
                    }

                    setTimeout(() => {
                        this.innerHTML = originalHtml;
                        this.style.background = '';
                        this.disabled = false;
                    }, 2000);
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Failed to add to cart');
                    this.innerHTML = originalHtml;
                    this.disabled = false;
                }
            } catch (error) {
                console.error('Add to cart error:', error);
                this.innerHTML = originalHtml;
                this.disabled = false;
            }
        });
    });
    </script>
</body>
</html>
