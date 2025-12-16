<?php

namespace App\Controllers;

/**
 * SearchController
 * Handles product/shop search functionality
 */
class SearchController {

    public function index(): void {
        $query = get('q', '');

        if (empty($query)) {
            redirect(url('/'));
            return;
        }

        try {
            $db = \Database::getConnection();

            // Search products
            $stmt = $db->prepare("
                SELECT p.*,
                       si.price,
                       si.compare_at_price,
                       si.stock_quantity,
                       s.name as shop_name,
                       s.slug as shop_slug,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id
                INNER JOIN shops s ON si.shop_id = s.id
                WHERE (p.name LIKE ? OR p.description LIKE ?)
                AND p.status = 'active'
                AND si.status = 'active'
                AND s.is_active = 1
                ORDER BY p.name
                LIMIT 50
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm]);
            $products = $stmt->fetchAll();

            // Search shops
            $stmt = $db->prepare("
                SELECT s.*,
                       COUNT(DISTINCT si.id) as product_count
                FROM shops s
                LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active'
                WHERE (s.name LIKE ? OR s.description LIKE ?)
                AND s.is_active = 1
                GROUP BY s.id
                ORDER BY s.average_rating DESC
                LIMIT 20
            ");
            $stmt->execute([$searchTerm, $searchTerm]);
            $shops = $stmt->fetchAll();

            view('buyer.search', [
                'query' => $query,
                'products' => $products,
                'shops' => $shops,
            ]);

        } catch (\PDOException $e) {
            logger("Search error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error performing search');
            redirect(url('/'));
        }
    }
}
