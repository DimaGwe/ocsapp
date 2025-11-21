<?php

namespace App\Controllers;

class HomeController {
    
    /**
     * Set user language
     */
    public function setLanguage(): void {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }
        
        $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }
        
        $language = post('language', 'en');
        if (!in_array($language, ['en', 'fr'])) {
            $language = 'en';
        }
        
        $_SESSION['language'] = $language;
        logger("Language changed to: {$language}", 'info');
        
        jsonResponse([
            'success' => true,
            'language' => $language,
            'message' => 'Language updated successfully'
        ]);
    }

    /**
     * Set user location - IMPROVED with better error handling
     */
    public function setLocation(): void {
        // Log the request for debugging
        error_log("setLocation called - Method: " . $_SERVER['REQUEST_METHOD']);
        
        // Handle OPTIONS request (CORS preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
        
        // Must be POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("setLocation error: Not a POST request");
            header('Content-Type: application/json');
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }
        
        // Get input data - handle both JSON and form-encoded
        $rawInput = file_get_contents('php://input');
        error_log("Raw input: " . $rawInput);
        
        $data = json_decode($rawInput, true);
        
        // If JSON decode fails, try POST data
        if (!$data) {
            $data = $_POST;
            error_log("Using POST data instead of JSON");
        }
        
        error_log("Parsed data: " . print_r($data, true));
        
        // CSRF Token Verification - Try multiple approaches
        $csrfTokenName = env('CSRF_TOKEN_NAME', '_csrf_token');
        
        // Try to get token from multiple sources
        $token = $data[$csrfTokenName] ?? 
                 $data['_csrf_token'] ?? 
                 $_POST[$csrfTokenName] ?? 
                 $_POST['_csrf_token'] ?? 
                 $_SERVER['HTTP_X_CSRF_TOKEN'] ?? 
                 '';
        
        error_log("CSRF Token received: " . $token);
        error_log("Session CSRF Token: " . ($_SESSION['csrf_token'] ?? 'NOT SET'));
        
        // Verify CSRF token
        if (!verifyCsrfToken($token)) {
            error_log("CSRF verification failed!");
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode([
                'success' => false, 
                'error' => 'Invalid CSRF token',
                'debug' => [
                    'token_received' => substr($token, 0, 10) . '...',
                    'session_exists' => isset($_SESSION['csrf_token'])
                ]
            ]);
            exit;
        }
        
        // Get location from request
        $location = trim($data['location'] ?? '');
        
        if (empty($location)) {
            error_log("Location is empty");
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Location is required']);
            exit;
        }
        
        // Sanitize location
        $location = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
        
        // Save to session
        $_SESSION['user_location'] = $location;
        $_SESSION['location'] = $location; // Both keys for compatibility
        
        // Save additional location data if provided
        if (isset($data['latitude']) && is_numeric($data['latitude'])) {
            $_SESSION['user_latitude'] = floatval($data['latitude']);
        }
        
        if (isset($data['longitude']) && is_numeric($data['longitude'])) {
            $_SESSION['user_longitude'] = floatval($data['longitude']);
        }
        
        if (isset($data['radius']) && is_numeric($data['radius'])) {
            $_SESSION['delivery_radius'] = intval($data['radius']);
        }
        
        if (isset($data['city'])) {
            $_SESSION['user_city'] = htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8');
        }
        
        if (isset($data['country'])) {
            $_SESSION['user_country'] = htmlspecialchars($data['country'], ENT_QUOTES, 'UTF-8');
        }
        
        // Log success
        error_log("Location saved successfully: {$location}");
        
        // Return success response
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'location' => $location,
            'message' => 'Location updated successfully',
            'session_data' => [
                'location' => $_SESSION['location'],
                'user_location' => $_SESSION['user_location'],
            ]
        ]);
        exit;
    }
    
    /**
     * Home page - Enhanced with Most Selling, Featured Categories, Virtual Malls
     */
    public function index(): void {
        try {
            $db = \Database::getConnection();
            
            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            // Get current location
            $currentLocation = $_SESSION['user_location'] ?? env('DEFAULT_LOCATION', 'Kirkland, QC');
            
            // ============================================
            // 1. MOST SELLING PRODUCTS (Data-Driven by Actual Sales)
            // ============================================
            // Get products with highest sales volume from last 30 days
            // Filtered to show only OCS Store (Shop ID 1) - ALL products regardless of seller
            $stmt = $db->query("
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug,
                       c.name as category_name,
                       c.slug as category_slug,
                       COALESCE(SUM(oi.quantity), 0) as total_sold
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                LEFT JOIN order_items oi ON p.id = oi.product_id
                LEFT JOIN orders o ON oi.order_id = o.id
                    AND o.status IN ('delivered', 'confirmed', 'preparing', 'ready')
                    AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                WHERE p.status = 'active'
                  AND si.status = 'active'
                GROUP BY p.id
                HAVING total_sold > 0
                ORDER BY total_sold DESC, p.created_at DESC
                LIMIT 24
            ");
            $mostSellingProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize image paths
            foreach ($mostSellingProducts as &$product) {
                $product['image'] = $this->normalizeImagePath($product['image']);
                
                // Calculate discount percentage
                if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']) {
                    $product['discount_percentage'] = round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100);
                } else {
                    $product['discount_percentage'] = 0;
                }
            }
            
            // Get tags for most selling products
            foreach ($mostSellingProducts as &$product) {
                $stmt = $db->prepare("
                    SELECT t.id, t.name, t.slug
                    FROM tags t
                    INNER JOIN product_tags pt ON t.id = pt.tag_id
                    WHERE pt.product_id = ?
                    ORDER BY t.name
                ");
                $stmt->execute([$product['id']]);
                $product['tags'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            
            // ============================================
            // 2. BEST SELLERS (Admin-Curated Featured Products)
            // ============================================
            // Products manually selected by admin via "Show on Home" checkbox
            // Filtered to show only OCS Store (Shop ID 1) - ALL products regardless of seller
            $stmt = $db->query("
                SELECT p.*,
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id AND si.shop_id = 1
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.show_on_home = 1
                  AND p.status = 'active'
                  AND si.status = 'active'
                ORDER BY p.sort_order DESC, p.created_at DESC
                LIMIT 24
            ");
            $featuredProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize image paths
            foreach ($featuredProducts as &$product) {
                $product['image'] = $this->normalizeImagePath($product['image']);
                
                // Calculate discount
                if (!empty($product['compare_at_price']) && $product['compare_at_price'] > $product['price']) {
                    $product['discount_percentage'] = round((($product['compare_at_price'] - $product['price']) / $product['compare_at_price']) * 100);
                } else {
                    $product['discount_percentage'] = 0;
                }
            }
            
            // Get tags for featured products
            foreach ($featuredProducts as &$product) {
                $stmt = $db->prepare("
                    SELECT t.id, t.name, t.slug
                    FROM tags t
                    INNER JOIN product_tags pt ON t.id = pt.tag_id
                    WHERE pt.product_id = ?
                ");
                $stmt->execute([$product['id']]);
                $product['tags'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
            
            // ============================================
            // 3. SALE PRODUCTS (20% OFF Banner)
            // ============================================
            $stmt = $db->query("
                SELECT p.*, 
                       p.sale_price as price,
                       p.base_price as compare_at_price,
                       p.sale_percentage,
                       pi.image_path as image,
                       b.name as brand_name
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_on_sale = 1
                  AND p.sale_price IS NOT NULL
                  AND p.status = 'active'
                ORDER BY p.sale_percentage DESC
                LIMIT 8
            ");
            $saleProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize image paths
            foreach ($saleProducts as &$product) {
                $product['image'] = $this->normalizeImagePath($product['image']);
            }
            
            // ============================================
            // 4. TOP BRANDS
            // ============================================
            $stmt = $db->query("
                SELECT b.*,
                       COUNT(DISTINCT p.id) as product_count,
                       MIN(p.base_price) as min_price
                FROM brands b
                INNER JOIN products p ON b.id = p.brand_id
                WHERE b.is_active = 1 AND p.status = 'active'
                GROUP BY b.id
                HAVING product_count > 0
                ORDER BY product_count DESC
                LIMIT 12
            ");
            $topBrands = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // ============================================
            // 5. FEATURED CATEGORIES (with product count and images)
            // ============================================
            $stmt = $db->query("
                SELECT c.*,
                       COUNT(DISTINCT pc.product_id) as product_count,
                       (SELECT pi.image_path 
                        FROM product_images pi
                        INNER JOIN product_categories pc2 ON pc2.product_id = pi.product_id
                        WHERE pc2.category_id = c.id 
                          AND pi.is_primary = 1
                        LIMIT 1) as sample_image
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                LEFT JOIN products p ON pc.product_id = p.id AND p.status = 'active'
                WHERE c.is_active = 1
                  AND c.parent_id IS NULL
                GROUP BY c.id
                HAVING product_count > 0
                ORDER BY product_count DESC, c.sort_order ASC
                LIMIT 8
            ");
            $categories = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize category images
            foreach ($categories as &$category) {
                if (!empty($category['image'])) {
                    $category['display_image'] = asset($category['image']);
                } elseif (!empty($category['sample_image'])) {
                    $category['display_image'] = asset($category['sample_image']);
                } else {
                    $category['display_image'] = null;
                }
            }
            
            // ============================================
// 6. VIRTUAL MALLS - Grocery Stores (NEW!)
// ============================================
try {
    $stmt = $db->prepare("
        SELECT s.*,
               COALESCE(s.average_rating, 0) as rating,
               s.reviews_count,
               COUNT(DISTINCT si.id) as product_count,
               s.packaging_time as delivery_time
        FROM shops s
        LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active' AND si.stock_quantity > 0
        WHERE s.is_active = 1
          AND s.is_approved = 1
          AND s.shop_type = 'grocery_store'
        GROUP BY s.id
        HAVING product_count > 0
        ORDER BY s.average_rating DESC, s.reviews_count DESC, s.total_orders DESC
        LIMIT 6
    ");
    
    $stmt->execute();
    $groceryStoreShops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Normalize shop images
    foreach ($groceryStoreShops as &$shop) {
        if (!empty($shop['logo'])) {
            $shop['display_logo'] = asset($shop['logo']);
        } else {
            $shop['display_logo'] = null;
        }
        $shop['formatted_delivery_time'] = ($shop['delivery_time'] ?? 30) . ' mins';
    }
    unset($shop);
    
} catch (\Exception $e) {
    logger("ERROR in grocery store shops query: " . $e->getMessage(), 'error');
    $groceryStoreShops = [];
}

// ============================================
// 7. VIRTUAL MALLS - Food Court
// ============================================
try {
    $stmt = $db->prepare("
        SELECT s.*,
               COALESCE(s.average_rating, 0) as rating,
               s.reviews_count,
               COUNT(DISTINCT si.id) as product_count,
               s.packaging_time as delivery_time
        FROM shops s
        LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active' AND si.stock_quantity > 0
        WHERE s.is_active = 1
          AND s.is_approved = 1
          AND s.shop_type = 'food_court'
        GROUP BY s.id
        HAVING product_count > 0
        ORDER BY s.average_rating DESC, s.reviews_count DESC, s.total_orders DESC
        LIMIT 6
    ");
    
    $stmt->execute();
    $foodCourtShops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Normalize shop images
    foreach ($foodCourtShops as &$shop) {
        if (!empty($shop['logo'])) {
            $shop['display_logo'] = asset($shop['logo']);
        } else {
            $shop['display_logo'] = null;
        }
        $shop['formatted_delivery_time'] = ($shop['delivery_time'] ?? 30) . ' mins';
    }
    unset($shop);
    
} catch (\Exception $e) {
    logger("ERROR in food court shops query: " . $e->getMessage(), 'error');
    $foodCourtShops = [];
}

// ============================================
// 8. VIRTUAL MALLS - Stores (FIXED!)
// ============================================
try {
    $stmt = $db->prepare("
        SELECT s.*,
               COALESCE(s.average_rating, 0) as rating,
               s.reviews_count,
               COUNT(DISTINCT si.id) as product_count,
               s.packaging_time as delivery_time
        FROM shops s
        LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active' AND si.stock_quantity > 0
        WHERE s.is_active = 1
          AND s.is_approved = 1
          AND s.shop_type = 'store'
        GROUP BY s.id
        HAVING product_count > 0
        ORDER BY s.average_rating DESC, s.reviews_count DESC, s.total_orders DESC
        LIMIT 6
    ");
    
    $stmt->execute();
    $storesShops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Normalize shop images
    foreach ($storesShops as &$shop) {
        if (!empty($shop['logo'])) {
            $shop['display_logo'] = asset($shop['logo']);
        } else {
            $shop['display_logo'] = null;
        }
        $shop['formatted_delivery_time'] = ($shop['delivery_time'] ?? 30) . ' mins';
    }
    unset($shop);
    
} catch (\Exception $e) {
    logger("ERROR in stores shops query: " . $e->getMessage(), 'error');
    $storesShops = [];
}

// ============================================
// 9. VIRTUAL MALLS - Products
// ============================================
try {
    $stmt = $db->prepare("
        SELECT s.*,
               COALESCE(s.average_rating, 0) as rating,
               s.reviews_count,
               COUNT(DISTINCT si.id) as product_count,
               s.packaging_time as delivery_time
        FROM shops s
        LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active' AND si.stock_quantity > 0
        WHERE s.is_active = 1
          AND s.is_approved = 1
          AND s.shop_type = 'products'
        GROUP BY s.id
        HAVING product_count > 0
        ORDER BY s.average_rating DESC, s.reviews_count DESC, s.total_orders DESC
        LIMIT 6
    ");
    
    $stmt->execute();
    $productsShops = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    // Normalize shop images
    foreach ($productsShops as &$shop) {
        if (!empty($shop['logo'])) {
            $shop['display_logo'] = asset($shop['logo']);
        } else {
            $shop['display_logo'] = null;
        }
        $shop['formatted_delivery_time'] = ($shop['delivery_time'] ?? 30) . ' mins';
    }
    unset($shop);
    
} catch (\Exception $e) {
    logger("ERROR in products shops query: " . $e->getMessage(), 'error');
    $productsShops = [];
}


// ============================================
// 10. RECENTLY VIEWED (if user is logged in)
// ============================================
$recentlyViewed = [];
if (function_exists('isLoggedIn') && isLoggedIn()) {
    try {
        $stmt = $db->prepare("
            SELECT p.*,
                   p.base_price as price,
                   pi.image_path as image,
                   b.name as brand_name
            FROM product_views pv
            INNER JOIN products p ON pv.product_id = p.id
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE pv.user_id = ?
              AND p.status = 'active'
            ORDER BY pv.viewed_at DESC
            LIMIT 8
        ");
        $stmt->execute([userId()]);
        $recentlyViewed = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Normalize images
        foreach ($recentlyViewed as &$product) {
            $product['image'] = $this->normalizeImagePath($product['image']);
        }
    } catch (\PDOException $e) {
        // product_views table doesn't exist - skip
        logger("Recently viewed error: " . $e->getMessage(), 'info');
        $recentlyViewed = [];
    }
}

// ============================================
// HERO SLIDERS - Get active sliders from database
// ============================================
$stmt = $db->query("
    SELECT id, title, description, button_text, button_url, image_path
    FROM hero_sliders
    WHERE status = 'active'
    ORDER BY sort_order ASC, id ASC
");
$heroSliders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// ============================================
// PROMO BANNER - Get active promo banner and selected products
// ============================================
$promoBanner = null;
$promoProducts = [];

try {
    // Get first active promo banner
    $stmt = $db->query("
        SELECT id, title, subtitle, discount_percentage, selected_products, button_text, button_url
        FROM promo_banners
        WHERE status = 'active'
        ORDER BY sort_order ASC, id ASC
        LIMIT 1
    ");
    $promoBanner = $stmt->fetch(\PDO::FETCH_ASSOC);

    // If banner exists and has selected products, fetch them
    if ($promoBanner && !empty($promoBanner['selected_products'])) {
        $selectedProductIds = json_decode($promoBanner['selected_products'], true);

        if (!empty($selectedProductIds) && is_array($selectedProductIds)) {
            $placeholders = str_repeat('?,', count($selectedProductIds) - 1) . '?';

            $stmt = $db->prepare("
                SELECT
                    p.id,
                    p.name,
                    p.slug,
                    pi.image_path as image
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                WHERE p.id IN ($placeholders)
                  AND p.status = 'active'
                ORDER BY FIELD(p.id, $placeholders)
            ");
            $stmt->execute(array_merge($selectedProductIds, $selectedProductIds));
            $promoProducts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
} catch (\PDOException $e) {
    logger("Promo banner fetch error: " . $e->getMessage(), 'info');
    $promoBanner = null;
    $promoProducts = [];
}

// ============================================
// Render view with ALL data
// ============================================
view('buyer.home', [
    'heroSliders' => $heroSliders,  // NEW! Hero sliders from database
    'promoBanner' => $promoBanner,  // NEW! Active promo banner
    'promoProducts' => $promoProducts,  // NEW! Selected products for promo
    'mostSellingProducts' => $mostSellingProducts,
    'featuredProducts' => $featuredProducts,
    'saleProducts' => $saleProducts,
    'topBrands' => $topBrands,
    'categories' => $categories,
    'groceryStoreShops' => $groceryStoreShops,  // NEW!
    'foodCourtShops' => $foodCourtShops,
    'storesShops' => $storesShops,
    'productsShops' => $productsShops,
    'recentlyViewed' => $recentlyViewed,  // FIXED!
    'currentLocation' => $currentLocation,
    'cartCount' => $cartCount,
]);
            
        } catch (\PDOException $e) {
            logger("Home page error: " . $e->getMessage(), 'error');
            
            // Fallback to empty arrays
            view('buyer.home', [
                'mostSellingProducts' => [],
                'featuredProducts' => [],
                'saleProducts' => [],
                'topBrands' => [],
                'categories' => [],
                'foodCourtShops' => [],
                'storesShops' => [],
                'productsShops' => [],
                'recentlyViewed' => [],
                'currentLocation' => 'Santo Domingo, DR',
                'cartCount' => 0,
            ]);
        }
    }

    /**
     * Best Sellers page (dedicated page for most selling products)
     */
    public function bestSellers(): void {
        try {
            $db = \Database::getConnection();
            
            $page = max(1, (int) get('page', 1));
            $perPage = 24;
            $offset = ($page - 1) * $perPage;
            
            // Get total count
            $stmt = $db->query("
                SELECT COUNT(*) as count 
                FROM products 
                WHERE is_most_selling = 1 AND status = 'active'
            ");
            $total = $stmt->fetch()['count'];
            
            // Get products
            $stmt = $db->prepare("
                SELECT p.*, 
                       p.base_price as price,
                       p.compare_at_price,
                       pi.image_path as image,
                       b.name as brand_name,
                       b.slug as brand_slug
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_most_selling = 1
                  AND p.status = 'active'
                ORDER BY p.sort_order DESC, p.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute();
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize images
            foreach ($products as &$product) {
                $product['image'] = $this->normalizeImagePath($product['image']);
            }
            
            view('buyer.best-sellers', [
                'products' => $products,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
            ]);
            
        } catch (\PDOException $e) {
            logger("Best sellers page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading best sellers');
            redirect(url('/'));
        }
    }

    /**
     * Categories listing page
     */
    public function categories(): void {
        try {
            $db = \Database::getConnection();
            
            $stmt = $db->query("
                SELECT c.*, 
                       COUNT(DISTINCT pc.product_id) as product_count,
                       (SELECT COUNT(*) FROM categories WHERE parent_id = c.id) as subcategory_count
                FROM categories c
                LEFT JOIN product_categories pc ON c.id = pc.category_id
                WHERE c.is_active = 1
                GROUP BY c.id
                ORDER BY c.sort_order, c.name
            ");
            $categories = $stmt->fetchAll();
            
            view('buyer.categories', ['categories' => $categories]);
            
        } catch (\PDOException $e) {
            logger("Categories page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading categories');
            redirect(url('/'));
        }
    }

    /**
     * Single category page
     */
    public function category($slug = null): void {
        if (!$slug) {
            $slug = get('slug', '');
        }
        
        if (!$slug) {
            redirect(url('categories'));
        }

        try {
            $db = \Database::getConnection();
            
            $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
            $stmt->execute([$slug]);
            $category = $stmt->fetch();
            
            if (!$category) {
                setFlash('error', 'Category not found');
                redirect(url('categories'));
            }
            
            $stmt = $db->prepare("
                SELECT p.*,
                       p.base_price as price,
                       pi.image_path as image,
                       b.name as brand_name
                FROM products p
                INNER JOIN product_categories pc ON p.id = pc.product_id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE pc.category_id = ?
                  AND p.status = 'active'
                ORDER BY p.name
            ");
            $stmt->execute([$category['id']]);
            $products = $stmt->fetchAll();
            
            view('buyer.category-single', [
                'category' => $category,
                'products' => $products,
            ]);
            
        } catch (\PDOException $e) {
            logger("Category page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading category');
            redirect(url('categories'));
        }
    }

    /**
     * Display shops page with filtering
     */
    public function shops(): void {
        try {
            $db = \Database::getConnection();
            
            // Get filter parameters
            $filterType = $_GET['type'] ?? 'all';
            $page = (int) ($_GET['page'] ?? 1);
            $perPage = 12;
            $search = $_GET['search'] ?? '';
            
            // STEP 1: Get counts for ALL shop types (for the filter tabs)
            $shopCounts = $this->getShopCounts($db);
            
            // STEP 2: Get filtered shops based on selected type
            if ($filterType === 'all') {
                $shops = $this->getAllActiveShops($db, $page, $perPage, $search);
                $total = $shopCounts['all'];
            } else {
                $dbType = $this->mapUrlTypeToDbType($filterType);
                $shops = $this->getShopsByType($db, $dbType, $page, $perPage, $search);
                $total = $shopCounts[$filterType] ?? 0;
            }
            
            // Get current location from session
            $currentLocation = $_SESSION['location'] ?? 'Santo Domingo';
            
            // Pass everything to view
            view('buyer.shops', [
                'shops' => $shops,
                'shopCounts' => $shopCounts,  // CRITICAL: This fixes the counts!
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
                'search' => $search,
                'currentLocation' => $currentLocation,
            ]);
            
        } catch (\PDOException $e) {
            logger("Shops page error: " . $e->getMessage(), 'error');
            view('buyer.shops', [
                'shops' => [],
                'shopCounts' => ['all' => 0, 'grocery' => 0, 'food_court' => 0, 'stores' => 0, 'products' => 0],
                'total' => 0,
                'page' => 1,
                'perPage' => 12,
                'search' => '',
                'currentLocation' => $_SESSION['location'] ?? 'Santo Domingo',
            ]);
        }
    }
    

    /**
     * Map URL-friendly type names to database enum values
     * URL: grocery, food_court, stores, products
     * DB:  grocery_store, food_court, store, products
     */
    private function mapUrlTypeToDbType(string $urlType): string {
        $mapping = [
            'grocery' => 'grocery_store',
            'food_court' => 'food_court',
            'stores' => 'store',
            'products' => 'products',
        ];
        return $mapping[$urlType] ?? 'grocery_store';
    }

    /**
     * Map database type to URL-friendly type
     */
    private function mapDbTypeToUrlType(string $dbType): string {
        $mapping = [
            'grocery_store' => 'grocery',
            'food_court' => 'food_court',
            'store' => 'stores',
            'products' => 'products',
        ];
        return $mapping[$dbType] ?? 'grocery';
    }
    
    /**
     * Get counts for all shop types
     * This runs ALWAYS, regardless of filter
     */
    private function getShopCounts($db): array {
        $counts = [
            'all' => 0,
            'grocery' => 0,
            'food_court' => 0,
            'stores' => 0,
            'products' => 0,
        ];
        
        // Get total count
        $stmt = $db->prepare("
            SELECT COUNT(*) as total 
            FROM shops 
            WHERE is_active = 1 AND is_approved = 1
        ");
        $stmt->execute();
        $counts['all'] = (int) $stmt->fetch()['total'];
        
        // Get counts per type
        $stmt = $db->prepare("
            SELECT shop_type, COUNT(*) as count 
            FROM shops 
            WHERE is_active = 1 AND is_approved = 1 
            GROUP BY shop_type
        ");
        $stmt->execute();
        $results = $stmt->fetchAll();
        
        // Map database types to URL-friendly types
        foreach ($results as $row) {
            $dbType = $row['shop_type'] ?? 'grocery_store';
            $urlType = $this->mapDbTypeToUrlType($dbType);
            if (isset($counts[$urlType])) {
                $counts[$urlType] = (int) $row['count'];
            }
        }
        
        return $counts;
    }
    
    /**
     * Get all active shops (no type filter)
     */
    private function getAllActiveShops($db, int $page, int $perPage, string $search): array {
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT s.*,
                   COUNT(DISTINCT si.id) as product_count
            FROM shops s
            LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active'
            WHERE s.is_active = 1 AND s.is_approved = 1
        ";
        
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (s.name LIKE ? OR s.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.average_rating DESC, s.name ASC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll() ?: [];
    }
    
    /**
     * Get shops filtered by type
     */
    private function getShopsByType($db, string $type, int $page, int $perPage, string $search): array {
        $offset = ($page - 1) * $perPage;
        
        $sql = "
            SELECT s.*,
                   COUNT(DISTINCT si.id) as product_count
            FROM shops s
            LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active'
            WHERE s.is_active = 1 
            AND s.is_approved = 1 
            AND s.shop_type = ?
        ";
        
        $params = [$type];
        
        if (!empty($search)) {
            $sql .= " AND (s.name LIKE ? OR s.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $sql .= " GROUP BY s.id ORDER BY s.average_rating DESC, s.name ASC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Single shop page - Display shop details and products
     */
    public function shop($slug = null): void {
        if (!$slug) {
            $slug = get('slug', '');
        }
        
        if (!$slug) {
            setFlash('error', 'Shop not found');
            redirect(url('shops'));
            return;
        }

        try {
            $db = \Database::getConnection();
            
            // Get shop details
            $stmt = $db->prepare("
                SELECT s.*,
                       COUNT(DISTINCT si.id) as product_count
                FROM shops s
                LEFT JOIN shop_inventory si ON s.id = si.shop_id AND si.status = 'active'
                WHERE s.slug = ? 
                AND s.is_active = 1 
                AND s.is_approved = 1
                GROUP BY s.id
            ");
            $stmt->execute([$slug]);
            $shop = $stmt->fetch();
            
            if (!$shop) {
                setFlash('error', 'Shop not found');
                redirect(url('shops'));
                return;
            }
            
            // Get shop hours
            $stmt = $db->prepare("
                SELECT * FROM shop_hours 
                WHERE shop_id = ? 
                ORDER BY day_of_week
            ");
            $stmt->execute([$shop['id']]);
            $shopHours = $stmt->fetchAll();
            
            // Get shop products from inventory
            $stmt = $db->prepare("
                SELECT p.*,
                       p.base_price,
                       si.price as shop_price,
                       si.stock_quantity,
                       si.status as inventory_status,
                       pi.image_path as image,
                       b.name as brand_name,
                       c.name as category_name
                FROM shop_inventory si
                INNER JOIN products p ON si.product_id = p.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN product_categories pc ON p.id = pc.product_id AND pc.is_primary = 1
                LEFT JOIN categories c ON pc.category_id = c.id
                WHERE si.shop_id = ?
                AND si.status = 'active'
                AND p.status = 'active'
                ORDER BY p.name
            ");
            $stmt->execute([$shop['id']]);
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Normalize image paths
            foreach ($products as &$product) {
                $product['image'] = $this->normalizeImagePath($product['image']);
                $product['price'] = $product['shop_price'] ?? $product['base_price'];
            }
            
            // Get shop reviews (if table exists)
            $reviews = [];
            try {
                $stmt = $db->prepare("
                    SELECT r.*,
                           u.name as user_name
                    FROM reviews r
                    LEFT JOIN users u ON r.user_id = u.id
                    WHERE r.shop_id = ?
                    ORDER BY r.created_at DESC
                    LIMIT 10
                ");
                $stmt->execute([$shop['id']]);
                $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            } catch (\PDOException $e) {
                // Reviews table doesn't exist yet
                logger("Reviews error: " . $e->getMessage(), 'info');
            }
            
            // Get current location
            $currentLocation = $_SESSION['user_location'] ?? env('DEFAULT_LOCATION', 'Santo Domingo');
            
            // Get cart count
            $cartCount = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $cartCount += $item['quantity'] ?? 0;
                }
            }
            
            view('buyer.shop-single', [
                'shop' => $shop,
                'shopHours' => $shopHours,
                'products' => $products,
                'reviews' => $reviews,
                'currentLocation' => $currentLocation,
                'cartCount' => $cartCount,
            ]);
            
        } catch (\PDOException $e) {
            logger("Shop page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading shop');
            redirect(url('shops'));
        }
    }

    /**
     * Search functionality
     */
    public function search(): void {
        try {
            $db = \Database::getConnection();
            
            $query = get('q', '');
            $page = max(1, (int) get('page', 1));
            $perPage = 20;
            $offset = ($page - 1) * $perPage;
            
            if (empty($query)) {
                redirect(url('/'));
            }
            
            $searchTerm = "%{$query}%";
            
            $stmt = $db->prepare("
                SELECT p.*,
                       p.base_price as price,
                       pi.image_path as image,
                       b.name as brand_name
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                  AND p.status = 'active'
                ORDER BY p.name
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $products = $stmt->fetchAll();
            
            $stmt = $db->prepare("
                SELECT COUNT(*) as count
                FROM products p
                WHERE (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)
                  AND p.status = 'active'
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            $total = $stmt->fetch()['count'];
            
            view('buyer.search-results', [
                'products' => $products,
                'query' => $query,
                'total' => $total,
                'page' => $page,
                'perPage' => $perPage,
            ]);
            
        } catch (\PDOException $e) {
            logger("Search error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error performing search');
            redirect(url('/'));
        }
    }

    /**
     * Deals page (20% OFF)
     */
    public function deals(): void {
        try {
            $db = \Database::getConnection();
            
            $stmt = $db->query("
                SELECT p.*, 
                       p.sale_price as price,
                       p.base_price as compare_at_price,
                       p.sale_percentage,
                       pi.image_path as image,
                       b.name as brand_name
                FROM products p
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.is_on_sale = 1
                  AND p.sale_price IS NOT NULL
                  AND p.status = 'active'
                ORDER BY p.sale_percentage DESC
            ");
            $saleProducts = $stmt->fetchAll();
            
            view('buyer.deals', [
                'saleProducts' => $saleProducts
            ]);
            
        } catch (\PDOException $e) {
            logger("Deals page error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading deals');
            redirect(url('/'));
        }
    }
    
    /**
     * Normalize image path - handles both 'images/' and 'uploads/' paths
     * Ensures compatibility with both old seeded data and new uploads
     * 
     * @param string|null $path - Image path from database
     * @return string - Normalized path or placeholder
     */
    private function normalizeImagePath(?string $path): string {
        // If no path provided, return placeholder
        if (empty($path)) {
            return 'https://via.placeholder.com/400x400?text=No+Image';
        }
        
        // Path is already in correct format relative to public/
        // Both 'images/products/...' and 'uploads/products/...' work
        // asset() helper will add the base URL
        return $path;
    }
}