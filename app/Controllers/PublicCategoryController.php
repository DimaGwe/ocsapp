<?php

namespace App\Controllers;

/**
 * Public Category Controller
 * Handles public-facing category pages
 */
class PublicCategoryController {

    /**
     * Display all categories page
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();

            // Get current language
            $currentLang = $_SESSION['language'] ?? 'fr';
            $t = getTranslations($currentLang);

            // Get cart count for header
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }

            // Get all active categories with product count
            $stmt = $db->query("
                SELECT
                    c.id,
                    c.name,
                    c.slug,
                    c.description,
                    c.image,
                    c.icon,
                    COUNT(DISTINCT pc.product_id) as product_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                LEFT JOIN products p ON pc.product_id = p.id AND p.status = 'active'
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.name ASC
            ");
            $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            view('buyer.categories', [
                'categories' => $categories,
                'cartCount' => $cartCount,
                't' => $t,
                'currentLang' => $currentLang,
            ]);

        } catch (\PDOException $e) {
            logger("Categories page error: " . $e->getMessage(), 'error');

            // Fallback to empty state
            view('buyer.categories', [
                'categories' => [],
                'cartCount' => 0,
                't' => getTranslations('fr'),
                'currentLang' => 'fr',
            ]);
        }
    }

    /**
     * Display single category page with products
     */
    public function show($slug = null): void {
        try {
            $db = \Database::getConnection();
            $currentLang = $_SESSION['language'] ?? 'fr';
            $t = getTranslations($currentLang);

            // Get category slug from route parameter
            if (empty($slug)) {
                redirect('/categories');
                return;
            }

            // Get category details
            $stmt = $db->prepare("
                SELECT * FROM categories
                WHERE slug = ? AND is_active = 1
                LIMIT 1
            ");
            $stmt->execute([$slug]);
            $category = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$category) {
                redirect('/categories');
                return;
            }

            // Get products in this category
            $stmt = $db->prepare("
                SELECT p.*,
                       pi.image_path as image,
                       b.name as brand_name
                FROM products p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE pc.category_id = ?
                  AND p.status = 'active'
                ORDER BY p.created_at DESC
            ");
            $stmt->execute([$category['id']]);
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }

            view('buyer.category-detail', [
                'category' => $category,
                'products' => $products,
                'cartCount' => $cartCount,
                't' => $t,
                'currentLang' => $currentLang,
            ]);

        } catch (\PDOException $e) {
            logger("Category detail page error: " . $e->getMessage(), 'error');
            redirect('/categories');
        }
    }
}
