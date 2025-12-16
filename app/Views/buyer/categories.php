<?php
/**
 * Categories Page - Browse all product categories
 */

// Get current language and translations
$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);

// Get store location
$storeLocation = $_SESSION['location'] ?? 'Santo Domingo, DR';

// Default values
$categories = $categories ?? [];
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['shop_by_category'] ?? 'Shop by Category' ?> - OCSAPP</title>
    <?= csrfMeta() ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Styles -->
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

    <main class="page">
        <!-- Page Header -->
        <div class="categories-header">
            <h1>🛍️ <?= $t['shop_by_category'] ?? 'Shop by Category' ?></h1>
            <p><?= $t['browse_categories_desc'] ?? 'Browse our wide selection of product categories' ?></p>
        </div>

        <?php if (!empty($categories)): ?>
            <!-- Categories Count -->
            <div class="categories-info">
                <p>
                    <?= $t['showing'] ?? 'Showing' ?> <strong><?= count($categories) ?></strong>
                    <?= $t['categories'] ?? 'categories' ?>
                </p>
            </div>

            <!-- Categories Grid -->
            <div class="categories-grid">
                <?php foreach ($categories as $category): ?>
                    <a href="<?= url('category/' . $category['slug']) ?>" class="category-card">
                        <div class="category-image">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?= url($category['image']) ?>"
                                     alt="<?= htmlspecialchars($category['name']) ?>"
                                     loading="lazy">
                            <?php elseif (!empty($category['icon'])): ?>
                                <div class="category-icon">
                                    <i class="<?= htmlspecialchars($category['icon']) ?>"></i>
                                </div>
                            <?php else: ?>
                                <div class="category-placeholder">
                                    <i class="fas fa-box"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="category-info">
                            <h3 class="category-name"><?= htmlspecialchars($category['name']) ?></h3>

                            <?php if (!empty($category['description'])): ?>
                                <p class="category-description">
                                    <?= htmlspecialchars(substr($category['description'], 0, 80)) ?>
                                    <?= strlen($category['description']) > 80 ? '...' : '' ?>
                                </p>
                            <?php endif; ?>

                            <div class="category-meta">
                                <span class="product-count">
                                    <i class="fas fa-cube"></i>
                                    <?= $category['product_count'] ?? 0 ?>
                                    <?= $t['products'] ?? 'products' ?>
                                </span>
                                <span class="view-link">
                                    <?= $t['view_all'] ?? 'View all' ?> <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state" style="text-align: center; padding: 80px 20px; background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <i class="fas fa-boxes" style="font-size: 64px; color: #d1d5db; margin-bottom: 20px;"></i>
                <h2 style="font-size: 24px; margin-bottom: 12px; color: #374151;">
                    <?= $t['no_categories'] ?? 'No Categories Available' ?>
                </h2>
                <p style="font-size: 16px; color: #6b7280; margin-bottom: 24px;">
                    <?= $t['no_categories_desc'] ?? 'Check back soon for new categories' ?>
                </p>
                <a href="<?= url('/') ?>"
                   class="btn-primary"
                   style="display: inline-block; padding: 12px 24px; background: #00b207; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-home"></i> <?= $t['back_to_home'] ?? 'Back to Homepage' ?>
                </a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>

    <style>
        /* Categories Page Header */
        .categories-header {
            text-align: center;
            margin-bottom: 40px;
            background: linear-gradient(135deg, #00b207 0%, #059669 50%, #10b981 100%);
            padding: 60px 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 178, 7, 0.2);
            position: relative;
            overflow: hidden;
        }

        .categories-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><rect fill="rgba(255,255,255,0.03)" x="0" y="0" width="50" height="50"/><rect fill="rgba(255,255,255,0.03)" x="50" y="50" width="50" height="50"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .categories-header h1 {
            font-size: 2.5rem;
            margin-bottom: 12px;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .categories-header p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.95);
            position: relative;
            z-index: 1;
        }

        /* Categories Info */
        .categories-info {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 32px;
        }

        .categories-info p {
            font-size: 1.1rem;
            font-weight: 600;
            color: #374151;
            margin: 0;
        }

        .categories-info strong {
            color: var(--primary);
        }

        /* Categories Grid */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        /* Category Card */
        .category-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            position: relative;
        }

        .category-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border: 2px solid var(--primary);
            border-radius: 12px;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            z-index: 10;
        }

        .category-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .category-card:hover::after {
            opacity: 1;
        }

        /* Category Image */
        .category-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: #f8f8f8;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .category-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .category-card:hover .category-image img {
            transform: scale(1.05);
        }

        .category-icon {
            font-size: 64px;
            color: var(--primary);
        }

        .category-placeholder {
            font-size: 64px;
            color: #d1d5db;
        }

        /* Category Info */
        .category-info {
            padding: 20px;
        }

        .category-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 8px 0;
        }

        .category-description {
            font-size: 14px;
            color: #6b7280;
            margin: 0 0 16px 0;
            line-height: 1.5;
        }

        .category-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
        }

        .product-count {
            font-size: 13px;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .product-count i {
            color: var(--primary);
        }

        .view-link {
            font-size: 13px;
            color: var(--primary);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .category-card:hover .view-link {
            gap: 8px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .categories-header h1 {
                font-size: 2rem;
            }

            .categories-header {
                padding: 40px 20px;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
                gap: 16px;
            }

            .category-image {
                height: 160px;
            }
        }
    </style>

    <script src="<?= asset('js/home.js') ?>"></script>
</body>
</html>
