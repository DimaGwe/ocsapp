<?php

namespace App\Controllers;

class ProductController {

    /**
     * Show product detail page (PUBLIC)
     */
    public function show($slug = null): void {
        try {
            if (!$slug) {
                setFlash('error', 'Product not found');
                redirect(url('/'));
                return;
            }

            $db = \Database::getConnection();
            
            // Get product
            $stmt = $db->prepare("
                SELECT p.*, b.name as brand_name, b.slug as brand_slug
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.slug = ? AND p.status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$slug]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('/'));
                return;
            }

            // Get product images
            $stmt = $db->prepare("
                SELECT id, image_path, is_primary, sort_order
                FROM product_images 
                WHERE product_id = ? 
                ORDER BY is_primary DESC, sort_order ASC
            ");
            $stmt->execute([$product['id']]);
            $productImages = $stmt->fetchAll();

            if (empty($productImages)) {
                $productImages = [
                    ['url' => 'https://via.placeholder.com/800?text=No+Image', 'alt' => 'Product image']
                ];
            } else {
                $productImages = array_map(function($img) {
                    return [
                        'url' => !empty($img['image_path']) ? url($img['image_path']) : 'https://via.placeholder.com/800?text=No+Image',
                        'alt' => 'Product image'
                    ];
                }, $productImages);
            }

            // Get categories
            $stmt = $db->prepare("
                SELECT c.id, c.name, c.slug
                FROM categories c
                INNER JOIN product_categories pc ON c.id = pc.category_id
                WHERE pc.product_id = ?
                ORDER BY pc.is_primary DESC
            ");
            $stmt->execute([$product['id']]);
            $categories = $stmt->fetchAll();
            $primaryCategory = !empty($categories) ? $categories[0] : null;

            // Get tags
            $stmt = $db->prepare("
                SELECT t.id, t.name, t.slug
                FROM tags t
                INNER JOIN product_tags pt ON t.id = pt.tag_id
                WHERE pt.product_id = ?
            ");
            $stmt->execute([$product['id']]);
            $tags = $stmt->fetchAll();

            // Get reviews (optional - won't break if table doesn't exist)
            $reviews = [];
            try {
                $stmt = $db->prepare("
                    SELECT r.*, u.first_name, u.last_name
                    FROM reviews r
                    INNER JOIN users u ON r.user_id = u.id
                    WHERE r.product_id = ? AND r.status = 'approved'
                    ORDER BY r.created_at DESC
                    LIMIT 10
                ");
                $stmt->execute([$product['id']]);
                $reviewsData = $stmt->fetchAll();
                
                $reviews = array_map(function($r) {
                    return [
                        'name' => $r['first_name'] . ' ' . substr($r['last_name'], 0, 1) . '.',
                        'rating' => (int)$r['rating'],
                        'date' => timeAgo($r['created_at']),
                        'comment' => $r['comment'],
                        'verified' => (bool)($r['is_verified_purchase'] ?? false)
                    ];
                }, $reviewsData);
            } catch (\Exception $e) {
                // Reviews table doesn't exist - skip
            }

            // Get related products
            $relatedProducts = [];
            if ($primaryCategory) {
                try {
                    $stmt = $db->prepare("
                        SELECT p.id, p.name, p.slug, p.base_price as price, p.average_rating,
                               (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as image
                        FROM products p
                        INNER JOIN product_categories pc ON p.id = pc.product_id
                        WHERE pc.category_id = ? AND p.id != ? AND p.status = 'active'
                        ORDER BY RAND()
                        LIMIT 4
                    ");
                    $stmt->execute([$primaryCategory['id'], $product['id']]);
                    $relatedData = $stmt->fetchAll();
                    
                    $relatedProducts = array_map(function($p) {
                        return [
                            'id' => $p['id'],
                            'name' => $p['name'],
                            'slug' => $p['slug'],
                            'price' => (float)$p['price'],
                            'average_rating' => (float)($p['average_rating'] ?? 0),
                            'image' => $p['image'] ?? '' // Return raw path, view will handle url() conversion
                        ];
                    }, $relatedData);
                } catch (\Exception $e) {
                    // Skip related products
                }
            }

            // Get shop info (optional)
            $shop = null;
            if (isset($product['shop_id']) && $product['shop_id']) {
                try {
                    $stmt = $db->prepare("SELECT * FROM shops WHERE id = ? AND is_active = 1");
                    $stmt->execute([$product['shop_id']]);
                    $shopData = $stmt->fetch();
                    
                    if ($shopData) {
                        $shop = [
                            'id' => $shopData['id'],
                            'name' => $shopData['name'],
                            'slug' => $shopData['slug'] ?? '',
                            'rating' => (float)($shopData['average_rating'] ?? 0),
                            'delivery_time' => ($shopData['packaging_time'] ?? 30) . ' mins',
                            'delivery_fee' => 2.99,
                            'minimum_order' => 10.00
                        ];
                    }
                } catch (\Exception $e) {
                    // Skip shop info
                }
            }

            // Format product data
            $productData = [
                'id' => $product['id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'description' => $product['description'] ?? $product['short_description'] ?? '',
                'price' => (float)$product['base_price'],
                'compare_at_price' => (float)($product['compare_at_price'] ?? 0),
                'sku' => $product['sku'] ?? '',
                'stock_quantity' => (int)$product['stock_quantity'],
                'unit' => $product['unit'] ?? 'piece',
                'weight' => $product['weight'] ?? '',
                'is_veg' => (bool)($product['is_veg'] ?? false),
                'is_featured' => (bool)($product['is_featured'] ?? false),
                'average_rating' => (float)($product['average_rating'] ?? 0),
                'reviews_count' => (int)($product['reviews_count'] ?? count($reviews)),
                'brand_name' => $product['brand_name'] ?? '',
                'category' => $primaryCategory ? $primaryCategory['name'] : 'Uncategorized',
                'tags' => array_column($tags, 'slug'),
                'nutritional_info' => []
            ];

            // Calculate discount
            $discount = 0;
            if ($productData['compare_at_price'] > $productData['price']) {
                $discount = round((($productData['compare_at_price'] - $productData['price']) / $productData['compare_at_price']) * 100);
            }

            // Get cart count
            $cartCount = 0;
            if (isLoggedIn()) {
                try {
                    $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
                    $stmt->execute([userId()]);
                    $result = $stmt->fetch();
                    $cartCount = (int)($result['count'] ?? 0);
                } catch (\Exception $e) {
                    // Skip cart count
                }
            }

            // Render view
            view('buyer/product-detail', [
                'product' => $productData,
                'productImages' => $productImages,
                'shop' => $shop,
                'relatedProducts' => $relatedProducts,
                'reviews' => $reviews,
                'category' => $primaryCategory,
                'discount' => $discount,
                'cartCount' => $cartCount
            ]);

        } catch (\PDOException $e) {
            logger("Product detail error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading product');
            redirect(url('/'));
        }
    }
}
