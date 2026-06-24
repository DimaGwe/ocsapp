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
                       p.base_price as price,
                       p.compare_at_price,
                       p.stock_quantity,
                       s.name as shop_name,
                       s.slug as shop_slug,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image
                FROM products p
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN shops s ON u.id = s.seller_id
                WHERE (p.name LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)
                AND p.status = 'active'
                AND p.stock_quantity > 0
                ORDER BY p.name
                LIMIT 50
            ");
            $searchTerm = "%{$query}%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $products = $stmt->fetchAll();

            // Search shops
            $stmt = $db->prepare("
                SELECT s.*,
                       s.name,
                       COUNT(DISTINCT p.id) as product_count,
                       0 as average_rating
                FROM shops s
                LEFT JOIN users u ON s.seller_id = u.id
                LEFT JOIN products p ON u.id = p.seller_id AND p.status = 'active'
                WHERE (s.name LIKE ? OR s.description LIKE ?)
                AND s.is_active = 1
                AND s.is_approved = 1
                GROUP BY s.id
                ORDER BY s.name ASC
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
