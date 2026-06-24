<?php

namespace App\Controllers;

/**
 * DealsController
 * Handles the deals/sales page
 */
class DealsController {
    
    /**
     * Display deals page with sale products
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();
            
            // Get cart count for header
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            // Get all products on sale
            $stmt = $db->query("
                SELECT p.*, 
                       p.sale_price,
                       p.base_price,
                       p.sale_percentage,
                       p.stock_quantity,
                       pi.image_path as image,
                       b.name as brand_name,
                       c.name as category_name,
                       c.slug as category_slug
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE p.is_on_sale = 1
                  AND p.sale_price IS NOT NULL
                  AND p.sale_price > 0
                  AND p.status = 'active'
                ORDER BY p.sale_percentage DESC, p.created_at DESC
            ");
            $saleProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Calculate actual savings and percentages
            foreach ($saleProducts as &$product) {
                // Normalize image path
                if (empty($product['image'])) {
                    $product['image'] = 'images/placeholder.jpg';
                }
                
                // Calculate sale percentage if not set
                if (empty($product['sale_percentage']) && $product['base_price'] > 0) {
                    $savings = $product['base_price'] - $product['sale_price'];
                    $product['sale_percentage'] = round(($savings / $product['base_price']) * 100);
                }
                
                // Ensure we have price as sale_price for compatibility
                $product['price'] = $product['sale_price'];
            }
            
            view('buyer.deals', [
                'saleProducts' => $saleProducts,
                'cartCount' => $cartCount,
            ]);
            
        } catch (\PDOException $e) {
            logger("Deals page error: " . $e->getMessage(), 'error');
            
            // Fallback to empty state
            view('buyer.deals', [
                'saleProducts' => [],
                'cartCount' => 0,
            ]);
        }
    }
}