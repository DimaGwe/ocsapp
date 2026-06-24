<?php

namespace App\Controllers;

use App\Middlewares\AuthMiddleware;
use App\Helpers\ImageUploadHelper;

require_once __DIR__ . '/../Helpers/AdminPermissionHelper.php';

class ProductController {
    
    private ImageUploadHelper $imageUploader;
    
    public function __construct() {
        // Get the current request URI
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Remove query string and get clean path
        $uri = strtok($uri, '?');
        
        // Check if this is a public product detail page
        // Matches: /product/slug-name or https://domain.com/product/slug-name
        if (preg_match('#/product/[a-zA-Z0-9\-]+$#', $uri)) {
            // Public route - skip authentication
            return;
        }

        // All admin routes require authentication
        AuthMiddleware::handle('admin');

        // Initialize image uploader
        $this->imageUploader = new ImageUploadHelper('uploads/products');
    }

    // ============================================
    // PUBLIC METHOD - show() - NO AUTH REQUIRED
    // ============================================
    
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

    // ============================================
    // ADMIN METHODS - ALL REQUIRE AUTH
    // ============================================
    public function index(): void {
        try {
            $db = \Database::getConnection();

            $page = max(1, (int) get('page', 1));
            $perPage = (int) env('ITEMS_PER_PAGE', 20);
            $offset = ($page - 1) * $perPage;

            $search = get('search', '');
            $categoryFilter = get('category', '');
            $brandFilter = get('brand', '');
            $statusFilter = get('status', '');

            // === GLOBAL PRODUCTS (OCS Warehouse) ===
            $globalWhere = ["p.product_type = 'global'"];
            $globalParams = [];

            if ($search) {
                $globalWhere[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
                $searchTerm = "%{$search}%";
                $globalParams[] = $searchTerm;
                $globalParams[] = $searchTerm;
                $globalParams[] = $searchTerm;
            }

            if ($categoryFilter) {
                $globalWhere[] = "EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = ?)";
                $globalParams[] = $categoryFilter;
            }

            if ($brandFilter) {
                $globalWhere[] = "p.brand_id = ?";
                $globalParams[] = $brandFilter;
            }

            if ($statusFilter) {
                $globalWhere[] = "p.status = ?";
                $globalParams[] = $statusFilter;
            }

            $globalWhereClause = implode(' AND ', $globalWhere);

            $stmt = $db->prepare("SELECT COUNT(DISTINCT p.id) as count FROM products p WHERE {$globalWhereClause}");
            $stmt->execute($globalParams);
            $totalGlobal = $stmt->fetch()['count'];

            $stmt = $db->prepare("
                SELECT p.*,
                       b.name as brand_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE {$globalWhereClause}
                ORDER BY p.created_at DESC
                LIMIT {$perPage} OFFSET {$offset}
            ");
            $stmt->execute($globalParams);
            $globalProducts = $stmt->fetchAll();

            // === SELLER PRODUCTS ===
            $sellerWhere = ["p.product_type = 'seller'"];
            $sellerParams = [];

            if ($search) {
                $sellerWhere[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.description LIKE ? OR s.name LIKE ?)";
                $searchTerm = "%{$search}%";
                $sellerParams[] = $searchTerm;
                $sellerParams[] = $searchTerm;
                $sellerParams[] = $searchTerm;
                $sellerParams[] = $searchTerm;
            }

            if ($categoryFilter) {
                $sellerWhere[] = "EXISTS (SELECT 1 FROM product_categories pc WHERE pc.product_id = p.id AND pc.category_id = ?)";
                $sellerParams[] = $categoryFilter;
            }

            if ($brandFilter) {
                $sellerWhere[] = "p.brand_id = ?";
                $sellerParams[] = $brandFilter;
            }

            if ($statusFilter) {
                $sellerWhere[] = "p.status = ?";
                $sellerParams[] = $statusFilter;
            }

            $sellerWhereClause = implode(' AND ', $sellerWhere);

            $stmt = $db->prepare("
                SELECT p.*,
                       b.name as brand_name,
                       s.id as shop_id,
                       s.name as shop_name,
                       u.email as seller_email,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = TRUE LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                LEFT JOIN users u ON p.seller_id = u.id
                LEFT JOIN shops s ON u.id = s.seller_id
                WHERE {$sellerWhereClause}
                ORDER BY s.name ASC, p.created_at DESC
            ");
            $stmt->execute($sellerParams);
            $sellerProducts = $stmt->fetchAll();

            $categories = $db->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY sort_order")->fetchAll();
            $brands = $db->query("SELECT * FROM brands WHERE is_active = TRUE ORDER BY name")->fetchAll();

            view('admin.products.index', [
                'globalProducts' => $globalProducts,
                'sellerProducts' => $sellerProducts,
                'totalGlobal' => $totalGlobal,
                'totalSeller' => count($sellerProducts),
                'categories' => $categories,
                'brands' => $brands,
                'page' => $page,
                'perPage' => $perPage,
                'search' => $search,
                'categoryFilter' => $categoryFilter,
                'brandFilter' => $brandFilter,
                'statusFilter' => $statusFilter,
                'currentPage' => 'products',
            ]);

        } catch (\PDOException $e) {
            logger("Products index error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading products');
            back();
        }
    }

    public function create(): void {
        try {
            $db = \Database::getConnection();
            
            $categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
            $brands = $db->query("SELECT * FROM brands WHERE is_active = TRUE ORDER BY name")->fetchAll();
            $tags = $db->query("SELECT * FROM tags ORDER BY name")->fetchAll();

            view('admin.products.create', [
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'currentPage' => 'products',
            ]);

        } catch (\PDOException $e) {
            logger("Products create error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading form');
            back();
        }
    }

    public function store(): void {
        // Verify CSRF
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        // Collect form data
        $data = [
            'name' => sanitize(post('name', '')),
            'slug' => sanitize(post('slug', '')),
            'sku' => sanitize(post('sku', '')),
            'short_description' => sanitize(post('short_description', '')),
            'description' => sanitize(post('description', '')),
            'base_price' => (float) post('base_price', 0),
            'cost_price' => (float) post('cost_price', 0),
            'unit' => sanitize(post('unit', 'piece')),
            'weight' => (float) post('weight', 0),
            'status' => sanitize(post('status', 'active')),
            'brand_id' => (int) post('brand_id', 0) ?: null,
            'is_featured' => post('is_featured') ? 1 : 0,
            'show_on_home' => post('show_on_home') ? 1 : 0,  // Admin-curated "Best Sellers"
            // Note: is_most_selling is now calculated from actual sales data, not manually set
            'categories' => post('categories', []),
            'tags' => post('tags', []),
            'product_type' => 'global', // OCS product
            // SEO fields
            'meta_title' => sanitize(post('meta_title', '')),
            'meta_description' => sanitize(post('meta_description', '')),
            'meta_keywords' => sanitize(post('meta_keywords', '')),
            'canonical_url' => sanitize(post('canonical_url', '')),
            'og_image' => sanitize(post('og_image', '')),
            'robots_meta' => sanitize(post('robots_meta', 'index,follow')),
        ];

        // Validate required fields
        if (empty($data['name']) || $data['base_price'] <= 0) {
            setFlash('error', 'Product name and valid price are required');
            setOldInput($data);
            back();
        }

        // Auto-generate slug if empty
        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }

        try {
            $db = \Database::getConnection();
            $db->beginTransaction();

            // Insert product
            $stmt = $db->prepare("
                INSERT INTO products (
                    name, slug, sku, short_description, description,
                    base_price, cost_price, unit, weight,
                    status, brand_id, is_featured, show_on_home, is_most_selling,
                    product_type,
                    meta_title, meta_description, meta_keywords,
                    canonical_url, og_image, robots_meta
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['name'],
                $data['slug'],
                $data['sku'],
                $data['short_description'],
                $data['description'],
                $data['base_price'],
                $data['cost_price'],
                $data['unit'],
                $data['weight'],
                $data['status'],
                $data['brand_id'],
                $data['is_featured'],
                $data['show_on_home'],
                $data['is_most_selling'],
                $data['product_type'],
                $data['meta_title'],
                $data['meta_description'],
                $data['meta_keywords'],
                $data['canonical_url'],
                $data['og_image'],
                $data['robots_meta']
            ]);

            $productId = $db->lastInsertId();

            // Handle image uploads
            if (!empty($_FILES['images']['name'][0])) {
                $this->handleImageUploads($productId, $_FILES['images']);
            }

            // Add categories
            if (!empty($data['categories'])) {
                $stmt = $db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)");
                foreach ($data['categories'] as $categoryId) {
                    $stmt->execute([$productId, $categoryId]);
                }
            }

            // Add tags
            if (!empty($data['tags'])) {
                $stmt = $db->prepare("INSERT INTO product_tags (product_id, tag_id) VALUES (?, ?)");
                foreach ($data['tags'] as $tagId) {
                    $stmt->execute([$productId, $tagId]);
                }
            }

            // AUTO-ALLOCATE to OCS Store (shop_id = 1)
            // Get initial stock from products table (if set via total_stock field)
            $initialStock = (int) post('total_stock', 0);

            if ($initialStock > 0) {
                // Create shop_inventory record for OCS Store
                $stmt = $db->prepare("
                    INSERT INTO shop_inventory (
                        shop_id, product_id,
                        allocated_quantity, stock_quantity,
                        price, status,
                        created_at, updated_at
                    ) VALUES (1, ?, ?, ?, ?, 'active', NOW(), NOW())
                ");
                $stmt->execute([
                    $productId,
                    $initialStock,      // allocated_quantity
                    $initialStock,      // stock_quantity
                    $data['base_price'] // price (OCS Store uses base_price)
                ]);

                // Update products table to reflect allocation
                // Note: available_stock is a GENERATED column (total_stock - allocated_stock)
                // and will be auto-calculated by MySQL
                $stmt = $db->prepare("
                    UPDATE products
                    SET total_stock = ?,
                        allocated_stock = ?
                    WHERE id = ?
                ");
                $stmt->execute([$initialStock, $initialStock, $productId]);

                // Log stock movement
                $stmt = $db->prepare("
                    INSERT INTO stock_movements (
                        shop_inventory_id, type, quantity,
                        reference_type, reference_id, reason, created_at
                    ) VALUES (
                        (SELECT id FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1),
                        'allocation', ?, 'product', ?,
                        'Auto-allocated to OCS Store on product creation', NOW()
                    )
                ");
                $stmt->execute([$productId, $initialStock, $productId]);
            }

            $db->commit();

            clearOldInput();
            setFlash('success', 'Product created successfully!');
            redirect(url('admin/products'));

        } catch (\PDOException $e) {
            $db->rollBack();
            logger("Product store error: " . $e->getMessage(), 'error');
            
            // More specific error message
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                setFlash('error', 'A product with this name or SKU already exists');
            } else {
                setFlash('error', 'Database error: ' . $e->getMessage());
            }
            
            setOldInput($data);
            back();
        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Product store exception: " . $e->getMessage(), 'error');
            setFlash('error', 'Error: ' . $e->getMessage());
            setOldInput($data);
            back();
        }
    }


    /**
     * Handle image uploads
     */
    private function handleImageUploads(int $productId, array $files): void {
    $db = \Database::getConnection();
    $isPrimary = true;

    foreach ($files['tmp_name'] as $key => $tmpName) {
        // Skip empty uploads
        if (empty($tmpName) || $files['error'][$key] !== UPLOAD_ERR_OK) {
            continue;
        }
        
        // Prepare file array for ImageUploadHelper
        $file = [
            'name' => $files['name'][$key],
            'type' => $files['type'][$key],
            'tmp_name' => $tmpName,
            'error' => $files['error'][$key],
            'size' => $files['size'][$key]
        ];
        
        // Upload using ImageUploadHelper
        $uploadResult = $this->imageUploader->upload($file);
        
        if ($uploadResult['success']) {
            // Save to database
            $stmt = $db->prepare("
                INSERT INTO product_images (product_id, image_path, is_primary, sort_order)
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $productId,
                $uploadResult['path'],  // Path from ImageUploadHelper
                $isPrimary ? 1 : 0,
                $key
            ]);
            
            $isPrimary = false; // Only first image is primary
        } else {
            logger("Failed to upload product image: " . $uploadResult['message'], 'error');
        }
    }
}

    /**
     * Generate URL-friendly slug
     */
    private function generateSlug(string $text): string {
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
        $text = preg_replace('/[\s-]+/', '-', $text);
        return trim($text, '-');
    }

    public function edit(): void {
        $id = get('id');
        
        if (!$id) {
            setFlash('error', 'Product not found');
            redirect(url('admin/products'));
        }

        try {
            $db = \Database::getConnection();
            
            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('admin/products'));
            }

            $stmt = $db->prepare("SELECT category_id FROM product_categories WHERE product_id = ?");
            $stmt->execute([$id]);
            $productCategories = array_column($stmt->fetchAll(), 'category_id');

            $stmt = $db->prepare("SELECT tag_id FROM product_tags WHERE product_id = ?");
            $stmt->execute([$id]);
            $productTags = array_column($stmt->fetchAll(), 'tag_id');

            $categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
            $brands = $db->query("SELECT * FROM brands WHERE is_active = TRUE ORDER BY name")->fetchAll();
            $tags = $db->query("SELECT * FROM tags ORDER BY name")->fetchAll();

            view('admin.products.edit', [
                'product' => $product,
                'categories' => $categories,
                'brands' => $brands,
                'tags' => $tags,
                'productCategories' => $productCategories,
                'productTags' => $productTags,
                'currentPage' => 'products',
            ]);

        } catch (\PDOException $e) {
            logger("Product edit error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading product');
            redirect(url('admin/products'));
        }
    }

    public function update(): void {
        $id = post('id');
        
        if (!$id) {
            setFlash('error', 'Product not found');
            redirect(url('admin/products'));
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request. Please try again.');
            back();
        }

    $data = [
        'name' => sanitize(post('name', '')),
        'slug' => sanitize(post('slug', '')),
        'sku' => sanitize(post('sku', '')),
        'brand_id' => post('brand_id', null),
        'short_description' => sanitize(post('short_description', '')),
        'description' => post('description', ''),
        'base_price' => (float) post('base_price', 0),
        'cost_price' => (float) post('cost_price', 0),
        'unit' => sanitize(post('unit', 'piece')),
        'weight' => (float) post('weight', 0),
        'status' => post('status', 'draft'),
        'is_featured' => post('is_featured') === 'on' ? 1 : 0,
        'show_on_home' => post('show_on_home') === 'on' ? 1 : 0,
        // SEO fields
        'meta_title' => sanitize(post('meta_title', '')),
        'meta_description' => sanitize(post('meta_description', '')),
        'meta_keywords' => sanitize(post('meta_keywords', '')),
        'canonical_url' => sanitize(post('canonical_url', '')),
        'og_image' => sanitize(post('og_image', '')),
        'robots_meta' => sanitize(post('robots_meta', 'index,follow')),
    ];

    try {
        $db = \Database::getConnection();
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE products SET
                brand_id = ?, name = ?, slug = ?, sku = ?, short_description = ?,
                description = ?, base_price = ?, cost_price = ?, unit = ?,
                weight = ?, status = ?, is_featured = ?, show_on_home = ?,
                meta_title = ?, meta_description = ?, meta_keywords = ?,
                canonical_url = ?, og_image = ?, robots_meta = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['brand_id'] ?: null,
            $data['name'],
            $data['slug'],
            $data['sku'] ?: null,
            $data['short_description'],
            $data['description'],
            $data['base_price'],
            $data['cost_price'],
            $data['unit'],
            $data['weight'],
            $data['status'],
            $data['is_featured'],
            $data['show_on_home'],
            $data['meta_title'],
            $data['meta_description'],
            $data['meta_keywords'],
            $data['canonical_url'],
            $data['og_image'],
            $data['robots_meta'],
            $id
        ]);

            $db->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$id]);
            $categories = post('categories', []);
            if (!empty($categories)) {
                foreach ($categories as $index => $categoryId) {
                    $stmt = $db->prepare("
                        INSERT INTO product_categories (product_id, category_id, is_primary) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([$id, $categoryId, $index === 0 ? 1 : 0]);
                }
            }

            $db->prepare("DELETE FROM product_tags WHERE product_id = ?")->execute([$id]);
            $tags = post('tags', []);
            if (!empty($tags)) {
                foreach ($tags as $tagId) {
                    $stmt = $db->prepare("INSERT INTO product_tags (product_id, tag_id) VALUES (?, ?)");
                    $stmt->execute([$id, $tagId]);
                }
            }

            $db->commit();

            setFlash('success', 'Product updated successfully');
            redirect(url('admin/products'));

        } catch (\PDOException $e) {
            if (isset($db)) {
                $db->rollBack();
            }
            logger("Product update error: " . $e->getMessage(), 'error');
            setFlash('error', getFriendlyDatabaseError($e, 'product'));
            back();
        }
    }

    public function delete(): void {
        $id = post('id');
        
        if (!$id) {
            setFlash('error', 'Product not found');
            back();
        }

        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            setFlash('error', 'Invalid request');
            back();
        }

        try {
            $db = \Database::getConnection();
            
            $stmt = $db->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

// Use the class property instead of creating new instance
foreach ($images as $image) {
    $this->imageUploader->delete($image['image_path']);
}
            
            $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);

            setFlash('success', 'Product deleted successfully');
            redirect(url('admin/products'));

        } catch (\PDOException $e) {
            logger("Product delete error: " . $e->getMessage(), 'error');
            setFlash('error', getFriendlyDatabaseError($e, 'product'));
            back();
        }
    }

    public function toggleFeature(): void {
    // Verify CSRF
    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        jsonResponse(['success' => false, 'message' => 'Invalid request'], 403);
        return;
    }

    $productId = (int) post('product_id');
    $field = sanitize(post('field', ''));

    // Validate field
    $allowedFields = ['is_featured', 'show_on_home', 'is_on_sale', 'is_most_selling'];
    if (!in_array($field, $allowedFields)) {
        jsonResponse(['success' => false, 'message' => 'Invalid field'], 400);
        return;
    }

    if (!$productId) {
        jsonResponse(['success' => false, 'message' => 'Invalid product ID'], 400);
        return;
    }

    try {
        $db = \Database::getConnection();

        // Get current value
        $stmt = $db->prepare("SELECT {$field} FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if (!$product) {
            jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            return;
        }

        // Toggle value
        $newValue = $product[$field] ? 0 : 1;

        // Update database
        $stmt = $db->prepare("UPDATE products SET {$field} = ? WHERE id = ?");
        $stmt->execute([$newValue, $productId]);

        jsonResponse([
            'success' => true,
            'message' => 'Product updated successfully',
            'field' => $field,
            'product_id' => $productId,
            'new_value' => $newValue
        ]);

    } catch (\PDOException $e) {
        logger("Toggle feature error: " . $e->getMessage(), 'error');
        jsonResponse(['success' => false, 'message' => 'Database error'], 500);
    }
}

/**
 * ADD THESE THREE METHODS TO YOUR PRODUCTCONTROLLER
 * Place them after the delete() method, before the stock management section
 */

/**
 * Upload Product Images (AJAX)
 */
public function uploadImages() {
    // Check authentication
    if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
        return jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Verify CSRF
    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        return jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    $productId = (int) post('product_id', 0);
    
    if (!$productId) {
        return jsonResponse(['success' => false, 'message' => 'Product ID required'], 400);
    }

    // Check if files were uploaded
    if (empty($_FILES['images'])) {
        return jsonResponse(['success' => false, 'message' => 'No images uploaded'], 400);
    }

    try {
        $db = \Database::getConnection();
        $db->beginTransaction();
        
        // Verify product exists
        $stmt = $db->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            throw new \Exception('Product not found');
        }

        $uploadedCount = 0;
        $files = $_FILES['images'];
        
        // Get current max sort order
        $stmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) as max_order FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        $sortOrder = $stmt->fetch()['max_order'];
        
        // Check if product has any images (for primary flag)
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        $hasImages = $stmt->fetch()['count'] > 0;

        // Handle multiple files
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;
        
        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $fileTmp = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];

            // Check for upload errors
            if ($fileError !== UPLOAD_ERR_OK) {
                continue;
            }

            // Validate file size (5MB max)
            if ($fileSize > 5 * 1024 * 1024) {
                continue;
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $fileTmp);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                continue;
            }

            // Prepare file array for ImageUploadHelper
            $file = [
                'name' => $fileName,
                'type' => $mimeType,
                'tmp_name' => $fileTmp,
                'error' => $fileError,
                'size' => $fileSize
            ];

            // Upload using ImageUploadHelper
            $uploadResult = $this->imageUploader->upload($file);

            if ($uploadResult['success']) {
                $sortOrder++;
                
                // Insert into database
                $stmt = $db->prepare("
                    INSERT INTO product_images 
                    (product_id, image_path, sort_order, is_primary)
                    VALUES (?, ?, ?, ?)
                ");
                
                $isPrimary = !$hasImages && $uploadedCount === 0 ? 1 : 0;
                $stmt->execute([
                    $productId,
                    $uploadResult['path'],
                    $sortOrder,
                    $isPrimary
                ]);
                
                $uploadedCount++;
                $hasImages = true;
            }
        }

        $db->commit();

        if ($uploadedCount > 0) {
            return jsonResponse([
                'success' => true,
                'message' => $uploadedCount . ' image(s) uploaded successfully'
            ]);
        } else {
            throw new \Exception('No valid images were uploaded');
        }

    } catch (\Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        logger("Upload images error: " . $e->getMessage(), 'error');
        return jsonResponse([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Delete Product Image (AJAX)
 */
public function deleteImage() {
    // Check authentication
    if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
        return jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Verify CSRF
    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        return jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    $imageId = (int) post('image_id', 0);
    
    if (!$imageId) {
        return jsonResponse(['success' => false, 'message' => 'Image ID required'], 400);
    }

    try {
        $db = \Database::getConnection();
        $db->beginTransaction();
        
        // Get image details
        $stmt = $db->prepare("SELECT * FROM product_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if (!$image) {
            throw new \Exception('Image not found');
        }

        // Delete physical file using ImageUploadHelper
        $this->imageUploader->delete($image['image_path']);

        // If this was primary, set another image as primary
        if ($image['is_primary']) {
            $stmt = $db->prepare("
                SELECT id FROM product_images 
                WHERE product_id = ? AND id != ?
                ORDER BY sort_order ASC
                LIMIT 1
            ");
            $stmt->execute([$image['product_id'], $imageId]);
            $nextImage = $stmt->fetch();
            
            if ($nextImage) {
                $stmt = $db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?");
                $stmt->execute([$nextImage['id']]);
            }
        }

        // Delete from database
        $stmt = $db->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->execute([$imageId]);

        $db->commit();

        return jsonResponse([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);

    } catch (\Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        logger("Delete image error: " . $e->getMessage(), 'error');
        return jsonResponse([
            'success' => false,
            'message' => 'Delete failed: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Set Primary Image (AJAX)
 */
public function setPrimaryImage() {
    // Check authentication
    if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
        return jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Verify CSRF
    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        return jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    $imageId = (int) post('image_id', 0);
    
    if (!$imageId) {
        return jsonResponse(['success' => false, 'message' => 'Image ID required'], 400);
    }

    try {
        $db = \Database::getConnection();
        $db->beginTransaction();
        
        // Get image details
        $stmt = $db->prepare("SELECT product_id FROM product_images WHERE id = ?");
        $stmt->execute([$imageId]);
        $image = $stmt->fetch();
        
        if (!$image) {
            throw new \Exception('Image not found');
        }

        // Remove primary flag from all images of this product
        $stmt = $db->prepare("UPDATE product_images SET is_primary = 0 WHERE product_id = ?");
        $stmt->execute([$image['product_id']]);

        // Set this image as primary
        $stmt = $db->prepare("UPDATE product_images SET is_primary = 1 WHERE id = ?");
        $stmt->execute([$imageId]);

        $db->commit();

        return jsonResponse([
            'success' => true,
            'message' => 'Primary image updated'
        ]);

    } catch (\Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        logger("Set primary error: " . $e->getMessage(), 'error');
        return jsonResponse([
            'success' => false,
            'message' => 'Update failed: ' . $e->getMessage()
        ], 500);
    }
}
    // Image management methods (uploadImages, deleteImage, setPrimaryImage) - Keep your existing code

    // ============================================
    // STOCK MANAGEMENT METHODS (NEW)
    // ============================================

    /**
     * Stock management page
     */
/**
 * Stock management page (MariaDB Compatible - No JSON_ARRAYAGG)
 */
public function stock(): void {
    try {
        $db = \Database::getConnection();

        $page = max(1, (int) get('page', 1));
        $perPage = (int) env('ITEMS_PER_PAGE', 20);
        $offset = ($page - 1) * $perPage;

        $search = get('search', '');
        $stockStatus = get('stock_status', '');

        // === GLOBAL PRODUCTS (OCS Warehouse) ===
        $globalWhere = ["p.product_type = 'global'"];
        $globalParams = [];

        if ($search) {
            $globalWhere[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $searchTerm = "%{$search}%";
            $globalParams[] = $searchTerm;
            $globalParams[] = $searchTerm;
        }

        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out_of_stock':
                    $globalWhere[] = "p.stock_quantity = 0 AND p.track_inventory = TRUE";
                    break;
                case 'low_stock':
                    $globalWhere[] = "p.stock_quantity > 0 AND p.stock_quantity <= p.low_stock_threshold AND p.track_inventory = TRUE";
                    break;
                case 'in_stock':
                    $globalWhere[] = "p.stock_quantity > p.low_stock_threshold AND p.track_inventory = TRUE";
                    break;
            }
        }

        $globalWhereClause = implode(' AND ', $globalWhere);

        $stmt = $db->prepare("SELECT COUNT(*) as count FROM products p WHERE {$globalWhereClause}");
        $stmt->execute($globalParams);
        $totalGlobal = $stmt->fetch()['count'];

        $stmt = $db->prepare("
            SELECT
                p.*,
                pi.image_path as primary_image
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE {$globalWhereClause}
            ORDER BY p.stock_quantity ASC, p.name ASC
            LIMIT {$perPage} OFFSET {$offset}
        ");
        $stmt->execute($globalParams);
        $globalProducts = $stmt->fetchAll();

        foreach ($globalProducts as &$product) {
            $stmt = $db->prepare("
                SELECT id, image_path, is_primary
                FROM product_images
                WHERE product_id = ?
                ORDER BY is_primary DESC, sort_order ASC
            ");
            $stmt->execute([$product['id']]);
            $images = $stmt->fetchAll();
            $product['images'] = json_encode($images);
        }

        // === SELLER PRODUCTS ===
        $sellerWhere = ["p.product_type = 'seller'"];
        $sellerParams = [];

        if ($search) {
            $sellerWhere[] = "(p.name LIKE ? OR p.sku LIKE ? OR s.name LIKE ?)";
            $searchTerm = "%{$search}%";
            $sellerParams[] = $searchTerm;
            $sellerParams[] = $searchTerm;
            $sellerParams[] = $searchTerm;
        }

        $sellerWhereClause = implode(' AND ', $sellerWhere);

        $stmt = $db->prepare("
            SELECT
                p.*,
                pi.image_path as primary_image,
                s.id as shop_id,
                s.name as shop_name,
                u.email as seller_email,
                si.stock_quantity as shop_stock
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            LEFT JOIN users u ON p.seller_id = u.id
            LEFT JOIN shops s ON u.id = s.seller_id
            LEFT JOIN shop_inventory si ON p.id = si.product_id AND s.id = si.shop_id
            WHERE {$sellerWhereClause}
            ORDER BY s.name ASC, p.name ASC
        ");
        $stmt->execute($sellerParams);
        $sellerProducts = $stmt->fetchAll();

        foreach ($sellerProducts as &$product) {
            $stmt = $db->prepare("
                SELECT id, image_path, is_primary
                FROM product_images
                WHERE product_id = ?
                ORDER BY is_primary DESC, sort_order ASC
            ");
            $stmt->execute([$product['id']]);
            $images = $stmt->fetchAll();
            $product['images'] = json_encode($images);
        }

        view('admin.products.stock', [
            'globalProducts' => $globalProducts,
            'sellerProducts' => $sellerProducts,
            'totalGlobal' => $totalGlobal,
            'totalSeller' => count($sellerProducts),
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'stockStatus' => $stockStatus,
            'currentPage' => 'stock',
        ]);

    } catch (\PDOException $e) {
        logger("Stock management error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error loading stock management');
        back();
    }
}

    /**
     * Update product stock
     */
    public function updateStock(): void {
        if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
            jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        try {
            $productId = (int) post('product_id');
            $stockQuantity = (int) post('stock_quantity');
            $operation = post('operation', 'set');

            if (!$productId) {
                jsonResponse(['success' => false, 'message' => 'Invalid product ID'], 400);
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("SELECT stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
            }

            $newStock = $stockQuantity;

            switch ($operation) {
                case 'add':
                    $newStock = $product['stock_quantity'] + $stockQuantity;
                    break;
                case 'subtract':
                    $newStock = max(0, $product['stock_quantity'] - $stockQuantity);
                    break;
                case 'set':
                default:
                    $newStock = max(0, $stockQuantity);
                    break;
            }

            $db->beginTransaction();

            // Update products table (total_stock)
            $stmt = $db->prepare("UPDATE products SET total_stock = ? WHERE id = ?");
            $stmt->execute([$newStock, $productId]);

            // AUTO-SYNC with OCS Store inventory
            // Check if product exists in OCS Store inventory
            $stmt = $db->prepare("
                SELECT id, stock_quantity FROM shop_inventory
                WHERE shop_id = 1 AND product_id = ? LIMIT 1
            ");
            $stmt->execute([$productId]);
            $ocsInventory = $stmt->fetch();

            if ($ocsInventory) {
                // Update existing OCS Store inventory
                $stmt = $db->prepare("
                    UPDATE shop_inventory
                    SET allocated_quantity = ?,
                        stock_quantity = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$newStock, $newStock, $ocsInventory['id']]);

                // Log stock movement
                $change = $newStock - $ocsInventory['stock_quantity'];
                if ($change != 0) {
                    $movementType = $change > 0 ? 'restock' : 'adjustment';
                    $stmt = $db->prepare("
                        INSERT INTO stock_movements (
                            shop_inventory_id, type, quantity,
                            reference_type, reference_id, reason, created_at
                        ) VALUES (?, ?, ?, 'admin', ?, 'Admin stock update', NOW())
                    ");
                    $stmt->execute([$ocsInventory['id'], $movementType, abs($change), userId()]);
                }
            } else {
                // Create new OCS Store inventory if doesn't exist
                $stmt = $db->prepare("SELECT base_price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $prod = $stmt->fetch();

                if ($prod && $newStock > 0) {
                    $stmt = $db->prepare("
                        INSERT INTO shop_inventory (
                            shop_id, product_id,
                            allocated_quantity, stock_quantity,
                            price, status, created_at, updated_at
                        ) VALUES (1, ?, ?, ?, ?, 'active', NOW(), NOW())
                    ");
                    $stmt->execute([$productId, $newStock, $newStock, $prod['base_price']]);

                    // Log creation
                    $stmt = $db->prepare("
                        INSERT INTO stock_movements (
                            shop_inventory_id, type, quantity,
                            reference_type, reference_id, reason, created_at
                        ) VALUES (
                            (SELECT id FROM shop_inventory WHERE shop_id = 1 AND product_id = ? LIMIT 1),
                            'allocation', ?, 'admin', ?, 'Auto-allocated to OCS Store', NOW()
                        )
                    ");
                    $stmt->execute([$productId, $newStock, userId()]);
                }
            }

            // Update allocated_stock in products table
            // Note: available_stock is a GENERATED column and will be auto-calculated
            $stmt = $db->prepare("
                UPDATE products
                SET allocated_stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$newStock, $productId]);

            $this->logStockChange($productId, $product['stock_quantity'], $newStock, $operation, userId());

            $db->commit();

            jsonResponse([
                'success' => true,
                'message' => 'Stock updated successfully and synced with OCS Store',
                'old_stock' => $product['stock_quantity'],
                'new_stock' => $newStock
            ]);

        } catch (\PDOException $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Update stock error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => getFriendlyDatabaseError($e, 'product')], 500);
        } catch (\Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            logger("Update stock exception: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating stock'], 500);
        }
    }

    /**
     * Get stock history
     */
    public function stockHistory(): void {
        try {
            $productId = (int) get('id');

            if (!$productId) {
                setFlash('error', 'Invalid product ID');
                back();
            }

            $db = \Database::getConnection();

            $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('admin/products'));
            }

            $stmt = $db->prepare("
                SELECT 
                    sl.*,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM stock_logs sl
                INNER JOIN users u ON sl.user_id = u.id
                WHERE sl.product_id = ?
                ORDER BY sl.created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$productId]);
            $history = $stmt->fetchAll();

            view('admin.products.stock_history', [
                'product' => $product,
                'history' => $history,
                'currentPage' => 'stock'
            ]);

        } catch (\PDOException $e) {
            logger("Stock history error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading stock history');
            back();
        }
    }

    /**
     * Log stock changes
     */
    private function logStockChange(int $productId, int $oldStock, int $newStock, string $operation, int $userId): void {
        try {
            $db = \Database::getConnection();

            $change = $newStock - $oldStock;

            $stmt = $db->prepare("
                INSERT INTO stock_logs (product_id, user_id, old_quantity, new_quantity, change_quantity, operation)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$productId, $userId, $oldStock, $newStock, $change, $operation]);

        } catch (\PDOException $e) {
            logger("Stock log error: " . $e->getMessage(), 'error');
        }
    }


    /**
 * BULK UPLOAD METHODS FOR PRODUCTCONTROLLER
 * Add these methods to your ProductController
 */

/**
 * Show bulk upload page
 */
public function bulkUpload(): void {
    try {
        view('admin.products.bulk-upload', [
            'currentPage' => 'products',
        ]);
    } catch (\Exception $e) {
        logger("Bulk upload page error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error loading page');
        redirect(url('admin/products'));
    }
}

/**
 * Download CSV template
 */
public function downloadTemplate(): void {
    try {
        $filename = 'product_import_template_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers - MORE READABLE
        $headers = [
            'name',
            'slug',
            'sku',
            'short_description',
            'description',
            'base_price',
            'cost_price',
            'unit',
            'weight',
            'status',
            'brand_name',           // ← CHANGED: Now use brand name instead of ID
            'category_names',       // ← CHANGED: Now use category names instead of IDs
            'is_featured',
            'show_on_home',
            'image_url_1',         // ← NEW: Image URLs
            'image_url_2',
            'image_url_3',
            'image_url_4',
            'image_url_5'
        ];
        
        fputcsv($output, $headers);
        
        // Get actual brands and categories for examples
        $db = \Database::getConnection();
        $brands = $db->query("SELECT name FROM brands WHERE is_active = 1 LIMIT 3")->fetchAll();
        $categories = $db->query("SELECT name FROM categories LIMIT 5")->fetchAll();
        
        $brandNames = array_column($brands, 'name');
        $categoryNames = array_column($categories, 'name');
        
        // Example rows with CLEAR data
        $examples = [
            [
                'Samsung Galaxy S23',
                'samsung-galaxy-s23',
                'SAM-S23-001',
                'Latest flagship smartphone',
                'Powerful Snapdragon processor, amazing camera system, 5G support, 8GB RAM',
                '45000.00',
                '35000.00',
                'piece',
                '0.17',
                'active',
                !empty($brandNames[0]) ? $brandNames[0] : 'Samsung',
                !empty($categoryNames[0]) && !empty($categoryNames[1]) ? $categoryNames[0] . ', ' . $categoryNames[1] : 'Electronics, Smartphones',
                '1',
                '0',
                'https://example.com/images/s23-front.jpg',
                'https://example.com/images/s23-back.jpg',
                'https://example.com/images/s23-side.jpg',
                '',
                ''
            ],
            [
                'Apple iPhone 14',
                '',
                'APPL-IP14-001',
                'Premium smartphone',
                'A15 Bionic chip, Pro camera system, Ceramic Shield, All-day battery',
                '55000.00',
                '45000.00',
                'piece',
                '0.17',
                'active',
                !empty($brandNames[1]) ? $brandNames[1] : 'Apple',
                !empty($categoryNames[0]) ? $categoryNames[0] : 'Electronics',
                '0',
                '1',
                'https://example.com/images/iphone14.jpg',
                '',
                '',
                '',
                ''
            ],
            [
                'Wireless Headphones',
                'wireless-headphones',
                'WH-1000',
                'Noise cancelling headphones',
                'Active noise cancellation, 30-hour battery life, premium sound quality',
                '15000.00',
                '10000.00',
                'piece',
                '0.25',
                'active',
                '',  // No brand
                !empty($categoryNames[2]) ? $categoryNames[2] : 'Audio',
                '0',
                '0',
                '',  // No images
                '',
                '',
                '',
                ''
            ]
        ];
        
        foreach ($examples as $example) {
            fputcsv($output, $example);
        }
        
        fclose($output);
        exit;
        
    } catch (\Exception $e) {
        logger("Template download error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error generating template');
        redirect(url('admin/products/bulk-upload'));
    }
}


/**
 * Process bulk upload - IMPROVED VERSION WITH IMAGES
 */
public function processBulkUpload() {
    // Check authentication
    if (!\AdminPermissionHelper::isAdminRole($_SESSION['user']['role'] ?? null)) {
        return jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    // Verify CSRF
    if (!verifyCsrfToken(post(env('CSRF_TOKEN_NAME', '_csrf_token')))) {
        return jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    // Check file upload
    if (empty($_FILES['csv_file'])) {
        return jsonResponse(['success' => false, 'message' => 'No file uploaded'], 400);
    }

    $file = $_FILES['csv_file'];
    
    // Validate file type
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($fileExt !== 'csv') {
        return jsonResponse(['success' => false, 'message' => 'Only CSV files are allowed'], 400);
    }

    // Validate file size (5MB max)
    if ($file['size'] > 5 * 1024 * 1024) {
        return jsonResponse(['success' => false, 'message' => 'File too large. Maximum 5MB allowed'], 400);
    }

    try {
        $db = \Database::getConnection();
        
        // Cache brands and categories for lookup
        $brandsCache = [];
        $stmt = $db->query("SELECT id, name FROM brands WHERE is_active = 1");
        while ($row = $stmt->fetch()) {
            $brandsCache[strtolower(trim($row['name']))] = $row['id'];
        }
        
        $categoriesCache = [];
        $stmt = $db->query("SELECT id, name FROM categories");
        while ($row = $stmt->fetch()) {
            $categoriesCache[strtolower(trim($row['name']))] = $row['id'];
        }
        
        $successCount = 0;
        $errorCount = 0;
        $skipCount = 0;
        $errors = [];
        $rowNumber = 0;
        
        // Open CSV file
        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            // Skip BOM if present
            $bom = fread($handle, 3);
            if ($bom !== "\xEF\xBB\xBF") {
                rewind($handle);
            }
            
            // Read header
            $headers = fgetcsv($handle);
            if (!$headers) {
                return jsonResponse(['success' => false, 'message' => 'Invalid CSV format'], 400);
            }
            
            // Normalize headers
            $headers = array_map('trim', $headers);
            $headers = array_map('strtolower', $headers);
            
            // Required columns
            $requiredColumns = ['name', 'base_price'];
            foreach ($requiredColumns as $required) {
                if (!in_array($required, $headers)) {
                    return jsonResponse([
                        'success' => false,
                        'message' => "Missing required column: {$required}"
                    ], 400);
                }
            }
            
            // Process each row
            while (($data = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                // Skip empty rows
                if (empty(array_filter($data))) {
                    $skipCount++;
                    continue;
                }
                
                // Convert to associative array
                $row = array_combine($headers, $data);
                
                // Validate required fields
                if (empty($row['name']) || empty($row['base_price'])) {
                    $errors[] = [
                        'row' => $rowNumber + 1,
                        'message' => 'Missing required fields: name and base_price'
                    ];
                    $errorCount++;
                    continue;
                }
                
                try {
                    $db->beginTransaction();
                    
                    // Generate slug if empty
                    $slug = !empty($row['slug']) ? trim($row['slug']) : $this->generateSlug($row['name']);
                    
                    // Check for duplicate slug
                    $stmt = $db->prepare("SELECT id FROM products WHERE slug = ?");
                    $stmt->execute([$slug]);
                    if ($stmt->fetch()) {
                        $slug = $slug . '-' . time() . '-' . $rowNumber;
                    }
                    
                    // LOOKUP BRAND BY NAME
                    $brandId = null;
                    if (!empty($row['brand_name'])) {
                        $brandName = strtolower(trim($row['brand_name']));
                        $brandId = $brandsCache[$brandName] ?? null;
                        
                        if (!$brandId) {
                            $errors[] = [
                                'row' => $rowNumber + 1,
                                'message' => "Warning: Brand '{$row['brand_name']}' not found. Product created without brand."
                            ];
                        }
                    }
                    
                    // Prepare product data
                    $productData = [
                        'name' => trim($row['name']),
                        'slug' => $slug,
                        'sku' => trim($row['sku'] ?? ''),
                        'short_description' => trim($row['short_description'] ?? ''),
                        'description' => trim($row['description'] ?? ''),
                        'base_price' => (float) ($row['base_price'] ?? 0),
                        'cost_price' => (float) ($row['cost_price'] ?? 0),
                        'unit' => trim($row['unit'] ?? 'piece'),
                        'weight' => (float) ($row['weight'] ?? 0),
                        'status' => trim($row['status'] ?? 'active'),
                        'brand_id' => $brandId,
                        'is_featured' => !empty($row['is_featured']) && $row['is_featured'] == '1' ? 1 : 0,
                        'show_on_home' => !empty($row['show_on_home']) && $row['show_on_home'] == '1' ? 1 : 0,
                        // is_most_selling is calculated from sales data, not imported
                        'product_type' => 'global'
                    ];
                    
                    // Insert product
                    $stmt = $db->prepare("
                        INSERT INTO products (
                            name, slug, sku, short_description, description,
                            base_price, cost_price, unit, weight, status,
                            brand_id, is_featured, show_on_home, is_most_selling, product_type
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->execute([
                        $productData['name'],
                        $productData['slug'],
                        $productData['sku'],
                        $productData['short_description'],
                        $productData['description'],
                        $productData['base_price'],
                        $productData['cost_price'],
                        $productData['unit'],
                        $productData['weight'],
                        $productData['status'],
                        $productData['brand_id'],
                        $productData['is_featured'],
                        $productData['show_on_home'],
                        $productData['is_most_selling'],
                        $productData['product_type']
                    ]);
                    
                    $productId = $db->lastInsertId();
                    
                    // LOOKUP CATEGORIES BY NAME
                    if (!empty($row['category_names'])) {
                        $categoryNames = array_map('trim', explode(',', $row['category_names']));
                        $stmt = $db->prepare("INSERT INTO product_categories (product_id, category_id, is_primary) VALUES (?, ?, ?)");
                        
                        $isPrimary = true;
                        foreach ($categoryNames as $categoryName) {
                            $categoryKey = strtolower($categoryName);
                            $categoryId = $categoriesCache[$categoryKey] ?? null;
                            
                            if ($categoryId) {
                                $stmt->execute([$productId, $categoryId, $isPrimary ? 1 : 0]);
                                $isPrimary = false;
                            }
                        }
                    }
                    
                    // PROCESS IMAGE URLS
                    $imageCount = 0;
                    for ($i = 1; $i <= 5; $i++) {
                        $imageKey = 'image_url_' . $i;
                        if (!empty($row[$imageKey])) {
                            $imageUrl = trim($row[$imageKey]);
                            
                            // Download and save image
                            $imageResult = $this->downloadAndSaveImage($imageUrl, $productId, $imageCount === 0);
                            
                            if ($imageResult['success']) {
                                $imageCount++;
                            } else {
                                $errors[] = [
                                    'row' => $rowNumber + 1,
                                    'message' => "Warning: Failed to download image {$i}: {$imageResult['message']}"
                                ];
                            }
                        }
                    }
                    
                    $db->commit();
                    $successCount++;
                    
                } catch (\PDOException $e) {
                    $db->rollBack();
                    $errors[] = [
                        'row' => $rowNumber + 1,
                        'message' => 'Database error: ' . $e->getMessage()
                    ];
                    $errorCount++;
                }
            }
            
            fclose($handle);
        }
        
        return jsonResponse([
            'success' => true,
            'message' => 'Import completed',
            'success' => $successCount,
            'errors' => $errors,
            'skipped' => $skipCount
        ]);
        
    } catch (\Exception $e) {
        logger("Bulk upload error: " . $e->getMessage(), 'error');
        return jsonResponse([
            'success' => false,
            'message' => 'Import failed: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * Download image from URL and save to product
 */
private function downloadAndSaveImage(string $url, int $productId, bool $isPrimary = false): array {
    try {
        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'message' => 'Invalid URL'];
        }
        
        // Download image
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return ['success' => false, 'message' => 'Failed to download'];
        }
        
        // Validate it's an image
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($imageData);
        
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Invalid image type'];
        }
        
        // Generate filename
        $extension = match($mimeType) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../../public/uploads/products/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filepath = $uploadDir . $filename;
        
        // Save image
        if (file_put_contents($filepath, $imageData) === false) {
            return ['success' => false, 'message' => 'Failed to save'];
        }
        
        // Get sort order
        $db = \Database::getConnection();
        $stmt = $db->prepare("SELECT COALESCE(MAX(sort_order), 0) as max_order FROM product_images WHERE product_id = ?");
        $stmt->execute([$productId]);
        $sortOrder = $stmt->fetch()['max_order'] + 1;
        
        // Save to database
        $stmt = $db->prepare("
            INSERT INTO product_images (product_id, image_path, sort_order, is_primary)
            VALUES (?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $productId,
            'uploads/products/' . $filename,
            $sortOrder,
            $isPrimary ? 1 : 0
        ]);
        
        return ['success' => true, 'path' => 'uploads/products/' . $filename];
        
    } catch (\Exception $e) {
        logger("Image download error: " . $e->getMessage(), 'error');
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * STOCK EXPORT METHOD FOR PRODUCTCONTROLLER
 * Add this method to your ProductController
 */

/**
 * Export stock data to CSV
 */
public function exportStock(): void {
    try {
        $db = \Database::getConnection();
        
        // Get filters from request
        $search = get('search', '');
        $stockStatus = get('stock_status', '');
        
        // Build query
        $where = ['1=1'];
        $params = [];

        if ($search) {
            $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($stockStatus) {
            switch ($stockStatus) {
                case 'out_of_stock':
                    $where[] = "p.stock_quantity = 0 AND p.track_inventory = TRUE";
                    break;
                case 'low_stock':
                    $where[] = "p.stock_quantity > 0 AND p.stock_quantity <= p.low_stock_threshold AND p.track_inventory = TRUE";
                    break;
                case 'in_stock':
                    $where[] = "p.stock_quantity > p.low_stock_threshold AND p.track_inventory = TRUE";
                    break;
            }
        }

        $whereClause = implode(' AND ', $where);

        // Get all products (no pagination for export)
        $stmt = $db->prepare("
            SELECT 
                p.id,
                p.name,
                p.sku,
                p.base_price,
                p.cost_price,
                p.stock_quantity,
                p.low_stock_threshold,
                p.track_inventory,
                p.allow_backorder,
                p.status,
                b.name as brand_name
            FROM products p
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE {$whereClause}
            ORDER BY p.name ASC
        ");
        
        $stmt->execute($params);
        $products = $stmt->fetchAll();

        // Generate filename
        $filename = 'stock_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // CSV Headers
        $headers = [
            'Product ID',
            'Product Name',
            'SKU',
            'Brand',
            'Base Price',
            'Cost Price',
            'Current Stock',
            'Low Stock Threshold',
            'Track Inventory',
            'Allow Backorder',
            'Stock Status',
            'Product Status'
        ];
        
        fputcsv($output, $headers);
        
        // Data rows
        foreach ($products as $product) {
            $trackInventory = (bool)$product['track_inventory'];
            $stockQuantity = (int)$product['stock_quantity'];
            $lowStockThreshold = (int)$product['low_stock_threshold'];
            $allowBackorder = (bool)$product['allow_backorder'];
            
            // Determine stock status
            $stockStatus = 'In Stock';
            if (!$trackInventory) {
                $stockStatus = 'Unlimited';
            } elseif ($stockQuantity == 0) {
                $stockStatus = $allowBackorder ? 'Backorder' : 'Out of Stock';
            } elseif ($stockQuantity <= $lowStockThreshold) {
                $stockStatus = 'Low Stock';
            }
            
            $row = [
                $product['id'],
                $product['name'],
                $product['sku'] ?: 'N/A',
                $product['brand_name'] ?: 'N/A',
                number_format($product['base_price'], 2),
                number_format($product['cost_price'], 2),
                $trackInventory ? $stockQuantity : 'Unlimited',
                $trackInventory ? $lowStockThreshold : 'N/A',
                $trackInventory ? 'Yes' : 'No',
                $allowBackorder ? 'Yes' : 'No',
                $stockStatus,
                ucfirst($product['status'])
            ];
            
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
        
    } catch (\Exception $e) {
        logger("Stock export error: " . $e->getMessage(), 'error');
        setFlash('error', 'Error exporting stock data');
        redirect(url('admin/products/stock'));
    }
}

public function restock(): void {
    ob_start();
    
    try {
        // Set JSON header
        header('Content-Type: application/json');
        
        // Get POST data
        $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';
        
        // Validation
        if ($productId <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
            exit;
        }
        
        if ($quantity <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Quantity must be greater than 0']);
            exit;
        }
        
        $db = \Database::getConnection();
        
        // Check which stock columns exist
        $stmt = $db->query("SHOW COLUMNS FROM products LIKE '%stock%'");
        $columns = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $hasNewColumns = in_array('total_stock', $columns);
        $hasOldColumn = in_array('stock_quantity', $columns);
        
        // Get product and current stock
        if ($hasNewColumns) {
            // New allocation system
            $stmt = $db->prepare("SELECT id, name, total_stock, available_stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            $oldTotalStock = (int)$product['total_stock'];
            $newTotalStock = $oldTotalStock + $quantity;

            // Update total_stock
            // Note: available_stock is a GENERATED column (total_stock - allocated_stock)
            // and will be auto-calculated by MySQL when we update total_stock
            $stmt = $db->prepare("
                UPDATE products
                SET total_stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$newTotalStock, $productId]);
            
            $message = "Added {$quantity} units to warehouse. New total: {$newTotalStock}";
            
        } elseif ($hasOldColumn) {
            // Old system - just update stock_quantity
            $stmt = $db->prepare("SELECT id, name, stock_quantity FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if (!$product) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                exit;
            }
            
            $oldStock = (int)$product['stock_quantity'];
            $newStock = $oldStock + $quantity;
            
            $stmt = $db->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
            $stmt->execute([$newStock, $productId]);
            
            $message = "Added {$quantity} units. New stock: {$newStock}";
            
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No stock column found in database']);
            exit;
        }
        
        // Try to log the movement (optional - won't fail if table doesn't exist)
        try {
            $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
            $logNotes = $notes ? $notes : "Admin restock: added {$quantity} units";
            
            $stmt = $db->prepare("
                INSERT INTO stock_movements (
                    product_id, movement_type, quantity,
                    previous_quantity, new_quantity,
                    notes, created_by, created_at
                ) VALUES (?, 'admin_restock', ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $productId,
                $quantity,
                $hasNewColumns ? $oldTotalStock : $oldStock,
                $hasNewColumns ? $newTotalStock : $newStock,
                $logNotes,
                $userId
            ]);
        } catch (\Exception $logError) {
            // Logging failed - that's okay, stock was still updated
        }
        
        ob_end_clean();
        echo json_encode(['success' => true, 'message' => $message]);
        exit;
        
    } catch (\PDOException $e) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        exit;
        
    } catch (\Exception $e) {
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        exit;
    }
}

    /**
     * Product Allocations Page - Shows which shops have this product
     */
    public function allocations(): void {
        try {
            $productId = (int) get('id');

            if (!$productId) {
                setFlash('error', 'Product ID is required');
                redirect(url('admin/products/stock'));
                return;
            }

            $db = \Database::getConnection();

            // Get product details
            $stmt = $db->prepare("
                SELECT p.*, b.name as brand_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('admin/products/stock'));
                return;
            }

            // Get all shop allocations for this product
            $stmt = $db->prepare("
                SELECT
                    si.*,
                    s.id as shop_id,
                    s.name as shop_name,
                    s.slug as shop_slug,
                    s.is_active as shop_is_active,
                    u.first_name as seller_first_name,
                    u.last_name as seller_last_name,
                    u.email as seller_email
                FROM shop_inventory si
                INNER JOIN shops s ON si.shop_id = s.id
                LEFT JOIN users u ON s.seller_id = u.id
                WHERE si.product_id = ?
                ORDER BY s.name ASC
            ");
            $stmt->execute([$productId]);
            $allocations = $stmt->fetchAll();

            view('admin.products.allocations', [
                'product' => $product,
                'allocations' => $allocations,
                'currentPage' => 'stock',
            ]);

        } catch (\PDOException $e) {
            logger("Allocations error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading allocations');
            redirect(url('admin/products/stock'));
        }
    }

    /**
     * Stock Movements Page - Shows stock movement history for a product
     */
    public function stockMovements(): void {
        try {
            $productId = (int) get('id');

            if (!$productId) {
                setFlash('error', 'Product ID is required');
                redirect(url('admin/products/stock'));
                return;
            }

            $db = \Database::getConnection();

            // Get product details
            $stmt = $db->prepare("
                SELECT p.*, b.name as brand_name,
                       (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as primary_image
                FROM products p
                LEFT JOIN brands b ON p.brand_id = b.id
                WHERE p.id = ?
            ");
            $stmt->execute([$productId]);
            $product = $stmt->fetch();

            if (!$product) {
                setFlash('error', 'Product not found');
                redirect(url('admin/products/stock'));
                return;
            }

            // Get stock movements for this product
            // Movements can be linked via shop_inventory.product_id OR via reference_id
            $stmt = $db->prepare("
                SELECT
                    sm.*,
                    si.shop_id,
                    s.name as shop_name,
                    u.first_name,
                    u.last_name,
                    u.email
                FROM stock_movements sm
                LEFT JOIN shop_inventory si ON sm.shop_inventory_id = si.id
                LEFT JOIN shops s ON si.shop_id = s.id
                LEFT JOIN users u ON sm.created_by = u.id
                WHERE (si.product_id = ? AND si.id IS NOT NULL)
                   OR (sm.reference_type = 'product' AND sm.reference_id = ?)
                   OR (sm.reference_type = 'admin' AND sm.reference_id = ?)
                ORDER BY sm.created_at DESC
                LIMIT 100
            ");
            $stmt->execute([$productId, $productId, $productId]);
            $movements = $stmt->fetchAll();

            view('admin.products.stock-movements', [
                'product' => $product,
                'movements' => $movements,
                'currentPage' => 'stock',
            ]);

        } catch (\PDOException $e) {
            logger("Stock movements error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading stock movements');
            redirect(url('admin/products/stock'));
        }
    }
}