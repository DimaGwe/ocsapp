<?php

namespace App\Controllers;

/**
 * DealsController
 * Handles the deals/sales page - Now displays promo banner products
 */
class DealsController {

    /**
     * Display deals page with products from active promo banners
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

            // Get all active promo banners
            $stmt = $db->query("
                SELECT id, title, subtitle, discount_percentage, selected_products, button_text, button_url
                FROM promo_banners
                WHERE status = 'active'
                ORDER BY sort_order ASC, id ASC
            ");
            $promoBanners = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Collect all unique product IDs from all active banners
            $allProductIds = [];
            $productBannerMap = []; // Map product ID to banner info

            foreach ($promoBanners as $banner) {
                if (!empty($banner['selected_products'])) {
                    $selectedProducts = json_decode($banner['selected_products'], true);
                    if (is_array($selectedProducts) && !empty($selectedProducts)) {
                        foreach ($selectedProducts as $productId) {
                            $productId = intval($productId); // Convert to integer
                            $allProductIds[] = $productId;
                            // Store banner info for this product (first banner wins if product is in multiple)
                            if (!isset($productBannerMap[$productId])) {
                                $productBannerMap[$productId] = [
                                    'banner_id' => $banner['id'],
                                    'banner_title' => $banner['title'],
                                    'banner_subtitle' => $banner['subtitle'],
                                    'discount_percentage' => $banner['discount_percentage']
                                ];
                            }
                        }
                    }
                }
            }

            // Remove duplicates
            $allProductIds = array_unique($allProductIds);

            $dealProducts = [];

            // Fetch products if we have any
            if (!empty($allProductIds)) {
                $placeholders = str_repeat('?,', count($allProductIds) - 1) . '?';

                $stmt = $db->prepare("
                    SELECT p.*,
                           p.sale_price,
                           p.base_price,
                           p.stock_quantity,
                           pi.image_path as image,
                           b.name as brand_name,
                           c.name as category_name,
                           c.slug as category_slug,
                           si.stock_quantity as shop_stock
                    FROM products p
                    LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                    LEFT JOIN brands b ON p.brand_id = b.id
                    LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                    LEFT JOIN categories c ON pc.category_id = c.id
                    LEFT JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                    WHERE p.id IN ($placeholders)
                      AND p.status = 'active'
                    GROUP BY p.id
                    ORDER BY FIELD(p.id, $placeholders)
                ");

                $stmt->execute(array_merge($allProductIds, $allProductIds));
                $dealProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                // Apply banner discount percentages and calculate pricing
                foreach ($dealProducts as &$product) {
                    $productId = $product['id'];

                    // Normalize image path
                    if (empty($product['image'])) {
                        $product['image'] = 'images/placeholder.jpg';
                    }

                    // Get banner info for this product
                    if (isset($productBannerMap[$productId])) {
                        $bannerInfo = $productBannerMap[$productId];
                        $product['banner_title'] = $bannerInfo['banner_title'];
                        $product['banner_subtitle'] = $bannerInfo['banner_subtitle'];
                        $product['discount_percentage'] = $bannerInfo['discount_percentage'];

                        // Calculate discounted price using banner discount
                        $basePrice = $product['is_on_sale'] ? $product['sale_price'] : $product['base_price'];
                        $discountAmount = $basePrice * ($bannerInfo['discount_percentage'] / 100);
                        $product['deal_price'] = $basePrice - $discountAmount;
                        $product['original_price'] = $basePrice;
                        $product['savings'] = $discountAmount;
                    } else {
                        // Fallback (shouldn't happen)
                        $product['discount_percentage'] = 20;
                        $product['deal_price'] = $product['is_on_sale'] ? $product['sale_price'] : $product['base_price'];
                        $product['original_price'] = $product['base_price'];
                        $product['savings'] = $product['base_price'] - $product['deal_price'];
                    }

                    // Use shop stock if available, otherwise use product stock
                    $product['stock_quantity'] = $product['shop_stock'] ?? $product['stock_quantity'] ?? 0;
                }
            }

            view('buyer.deals', [
                'dealProducts' => $dealProducts,
                'promoBanners' => $promoBanners,
                'cartCount' => $cartCount,
            ]);

        } catch (\PDOException $e) {
            logger("Deals page error: " . $e->getMessage(), 'error');

            // Fallback to empty state
            view('buyer.deals', [
                'dealProducts' => [],
                'promoBanners' => [],
                'cartCount' => 0,
            ]);
        }
    }
}
