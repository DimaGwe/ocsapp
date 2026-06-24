<?php
/**
 * Search Results Page
 * File: app/Views/buyer/search-results.php
 * 
 * This page connects to your actual database structure
 */

// Get search parameters
$searchQuery = $_GET['q'] ?? '';
$categoryId = $_GET['category'] ?? '';
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 10000;
$sortBy = $_GET['sort'] ?? 'relevance';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;

// In production, this would come from your ProductController
// For now, we'll simulate the data structure based on your database

// Simulated products based on your actual database structure
// In real implementation, this would be: $products = $productModel->search($searchQuery, $filters);
$products = [
    [
        'id' => 11,
        'name' => 'Loreal Anti-Aging Cream',
        'slug' => 'loreal-anti-aging-cream',
        'sku' => 'LOR-AAC-001',
        'short_description' => 'Advanced anti-aging face cream',
        'base_price' => 34.99,
        'compare_at_price' => 49.99,
        'image' => 'https://images.unsplash.com/photo-1556228720-195a672e8a03?w=400',
        'brand_name' => 'L\'Oreal',
        'category_name' => 'Beauty & Personal Care',
        'average_rating' => 4.5,
        'reviews_count' => 23,
        'is_featured' => false,
        'shop_name' => 'Fresh Market Store',
        'shop_slug' => 'fresh-market-store',
        'stock_quantity' => 45,
        'unit' => 'piece'
    ],
    [
        'id' => 23,
        'name' => 'Test Product',
        'slug' => 'test-pr',
        'sku' => 'BRO-B012',
        'short_description' => 'Short description',
        'base_price' => 20.00,
        'compare_at_price' => 0,
        'image' => 'images/products/68e3a9949577b_1759750548.png',
        'brand_name' => null,
        'category_name' => 'Electronics',
        'average_rating' => 0,
        'reviews_count' => 0,
        'is_featured' => false,
        'shop_name' => 'Tech Haven',
        'shop_slug' => 'tech-haven',
        'stock_quantity' => 100,
        'unit' => 'piece'
    ],
    [
        'id' => 25,
        'name' => 'Mobile Phone',
        'slug' => 'mobile',
        'sku' => null,
        'short_description' => 'Latest smartphone with advanced features',
        'base_price' => 599.99,
        'compare_at_price' => 799.99,
        'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400',
        'brand_name' => 'Samsung',
        'category_name' => 'Electronics',
        'average_rating' => 4.8,
        'reviews_count' => 156,
        'is_featured' => true,
        'shop_name' => 'Tech Haven',
        'shop_slug' => 'tech-haven',
        'stock_quantity' => 25,
        'unit' => 'piece'
    ],
    [
        'id' => 26,
        'name' => 'Digicel SIM Card',
        'slug' => 'digicel',
        'sku' => null,
        'short_description' => 'Prepaid SIM card with data',
        'base_price' => 5.00,
        'compare_at_price' => 0,
        'image' => 'https://images.unsplash.com/photo-1597740049284-388659a41286?w=400',
        'brand_name' => 'Digicel',
        'category_name' => 'Mobile & Accessories',
        'average_rating' => 3.5,
        'reviews_count' => 42,
        'is_featured' => false,
        'shop_name' => 'Tech Haven',
        'shop_slug' => 'tech-haven',
        'stock_quantity' => 500,
        'unit' => 'piece'
    ]
];

// Add more demo products for better display
$demoProducts = [
    ['id' => 100, 'name' => 'Organic Avocados', 'slug' => 'organic-avocados', 'base_price' => 4.99, 'compare_at_price' => 6.99, 'image' => 'https://images.unsplash.com/photo-1523049673857-eb18f1d7b578?w=400', 'category_name' => 'Fruits & Vegetables', 'shop_name' => 'Fresh Market Store', 'average_rating' => 4.7, 'reviews_count' => 89],
    ['id' => 101, 'name' => 'Fresh Tomatoes', 'slug' => 'fresh-tomatoes', 'base_price' => 3.49, 'compare_at_price' => 0, 'image' => 'https://images.unsplash.com/photo-1546094096-0df4bcaaa337?w=400', 'category_name' => 'Fruits & Vegetables', 'shop_name' => 'Fresh Market Store', 'average_rating' => 4.5, 'reviews_count' => 56],
    ['id' => 102, 'name' => 'Organic Milk', 'slug' => 'organic-milk', 'base_price' => 5.99, 'compare_at_price' => 7.99, 'image' => 'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400', 'category_name' => 'Dairy & Eggs', 'shop_name' => 'Fresh Market Store', 'average_rating' => 4.9, 'reviews_count' => 234],
    ['id' => 103, 'name' => 'Whole Wheat Bread', 'slug' => 'wheat-bread', 'base_price' => 2.99, 'compare_at_price' => 3.99, 'image' => 'https://images.unsplash.com/photo-1549931319-a545dcf3bc73?w=400', 'category_name' => 'Bakery', 'shop_name' => 'Fresh Market Store', 'average_rating' => 4.3, 'reviews_count' => 67],
    ['id' => 104, 'name' => 'Laptop Dell XPS', 'slug' => 'laptop-dell', 'base_price' => 1299.99, 'compare_at_price' => 1599.99, 'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400', 'category_name' => 'Computers', 'shop_name' => 'Tech Haven', 'average_rating' => 4.8, 'reviews_count' => 45, 'is_featured' => true],
    ['id' => 105, 'name' => 'Wireless Headphones', 'slug' => 'wireless-headphones', 'base_price' => 79.99, 'compare_at_price' => 99.99, 'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400', 'category_name' => 'Audio', 'shop_name' => 'Tech Haven', 'average_rating' => 4.6, 'reviews_count' => 178],
    ['id' => 106, 'name' => 'Smart Watch', 'slug' => 'smart-watch', 'base_price' => 249.99, 'compare_at_price' => 349.99, 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400', 'category_name' => 'Wearables', 'shop_name' => 'Tech Haven', 'average_rating' => 4.4, 'reviews_count' => 92],
    ['id' => 107, 'name' => 'Coffee Beans', 'slug' => 'coffee-beans', 'base_price' => 12.99, 'compare_at_price' => 15.99, 'image' => 'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400', 'category_name' => 'Beverages', 'shop_name' => 'Fresh Market Store', 'average_rating' => 4.7, 'reviews_count' => 201]
];

// Merge with demo products for better display
$products = array_merge($products, $demoProducts);

// Filter products based on search query
if ($searchQuery) {
    $products = array_filter($products, function($product) use ($searchQuery) {
        return stripos($product['name'], $searchQuery) !== false || 
               stripos($product['category_name'], $searchQuery) !== false ||
               stripos($product['shop_name'], $searchQuery) !== false;
    });
}

// Apply price filter
$products = array_filter($products, function($product) use ($minPrice, $maxPrice) {
    return $product['base_price'] >= $minPrice && $product['base_price'] <= $maxPrice;
});

// Apply sorting
switch ($sortBy) {
    case 'price_low':
        usort($products, function($a, $b) { return $a['base_price'] <=> $b['base_price']; });
        break;
    case 'price_high':
        usort($products, function($a, $b) { return $b['base_price'] <=> $a['base_price']; });
        break;
    case 'rating':
        usort($products, function($a, $b) { return $b['average_rating'] <=> $a['average_rating']; });
        break;
    case 'newest':
        $products = array_reverse($products);
        break;
}

// Pagination
$totalResults = count($products);
$totalPages = ceil($totalResults / $perPage);
$offset = ($page - 1) * $perPage;
$products = array_slice($products, $offset, $perPage);

// Get unique categories and shops for filters (from your actual database)
$categories = [
    ['id' => 1, 'name' => 'Fruits & Vegetables', 'count' => 24],
    ['id' => 2, 'name' => 'Dairy & Eggs', 'count' => 18],
    ['id' => 3, 'name' => 'Bakery', 'count' => 12],
    ['id' => 4, 'name' => 'Beverages', 'count' => 35],
    ['id' => 5, 'name' => 'Beauty & Personal Care', 'count' => 42],
    ['id' => 15, 'name' => 'Electronics', 'count' => 56],
    ['id' => 16, 'name' => 'Computers', 'count' => 23],
    ['id' => 17, 'name' => 'Mobile & Accessories', 'count' => 31],
    ['id' => 18, 'name' => 'Audio', 'count' => 19],
    ['id' => 19, 'name' => 'Wearables', 'count' => 14]
];

$brands = [
    ['id' => 1, 'name' => 'Samsung', 'count' => 23],
    ['id' => 2, 'name' => 'Apple', 'count' => 18],
    ['id' => 3, 'name' => 'Dell', 'count' => 12],
    ['id' => 4, 'name' => 'L\'Oreal', 'count' => 8],
    ['id' => 5, 'name' => 'Sony', 'count' => 15]
];

$shops = [
    ['id' => 1, 'name' => 'Fresh Market Store', 'count' => 45],
    ['id' => 2, 'name' => 'Tech Haven', 'count' => 38],
    ['id' => 3, 'name' => 'Awesome Shop', 'count' => 12]
];

$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?= htmlspecialchars($searchQuery) ?> - OCSAPP</title>
    <?= csrfMeta() ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; color: #333; }
        
        /* Header */
        .header {
            background: white;
            padding: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #00a651;
            text-decoration: none;
            white-space: nowrap;
        }
        .search-form {
            flex: 1;
            max-width: 700px;
            position: relative;
        }
        .search-input-group {
            display: flex;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            overflow: hidden;
            transition: all 0.3s;
        }
        .search-input-group:focus-within {
            border-color: #00a651;
            box-shadow: 0 0 0 3px rgba(0, 166, 81, 0.1);
        }
        .search-input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: none;
            font-size: 15px;
            outline: none;
        }
        .search-btn {
            padding: 0 25px;
            background: #00a651;
            border: none;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        .search-btn:hover {
            background: #008f44;
        }
        .header-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .cart-btn {
            position: relative;
            background: #00a651;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .cart-btn:hover {
            background: #008f44;
            transform: translateY(-2px);
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            max-width: 1400px;
            margin: 20px auto;
            padding: 0 20px;
            font-size: 14px;
            color: #666;
        }
        .breadcrumb a {
            color: #00a651;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        
        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto 40px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 30px;
        }
        
        /* Filters Sidebar */
        .filters-sidebar {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }
        .filter-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e9ecef;
        }
        .filter-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        .clear-filters {
            color: #00a651;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .clear-filters:hover {
            text-decoration: underline;
        }
        
        .filter-section {
            margin-bottom: 30px;
        }
        .filter-section-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }
        
        /* Price Range */
        .price-range {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        .price-input {
            flex: 1;
            padding: 8px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
        }
        .price-slider {
            margin-top: 20px;
        }
        .slider {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: #e9ecef;
            outline: none;
        }
        .slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #00a651;
            cursor: pointer;
        }
        
        /* Checkboxes */
        .filter-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filter-option:hover {
            color: #00a651;
        }
        .filter-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-checkbox input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #00a651;
            cursor: pointer;
        }
        .filter-count {
            font-size: 13px;
            color: #999;
        }
        
        /* Rating Filter */
        .rating-filter {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .rating-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .rating-option:hover {
            border-color: #00a651;
            background: #f0f9ff;
        }
        .rating-option.active {
            border-color: #00a651;
            background: #f0f9ff;
        }
        .stars {
            color: #ffc107;
        }
        
        /* Search Results */
        .search-results {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .results-header {
            background: white;
            border-radius: 12px;
            padding: 20px 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .results-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .results-count {
            font-size: 18px;
            color: #333;
        }
        .results-count strong {
            color: #00a651;
        }
        .sort-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .view-toggle {
            display: flex;
            gap: 5px;
        }
        .view-btn {
            padding: 8px;
            background: white;
            border: 2px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s;
        }
        .view-btn:first-child {
            border-radius: 6px 0 0 6px;
        }
        .view-btn:last-child {
            border-radius: 0 6px 6px 0;
        }
        .view-btn.active {
            background: #00a651;
            color: white;
            border-color: #00a651;
        }
        .sort-select {
            padding: 8px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        
        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }
        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        .product-image {
            position: relative;
            width: 100%;
            height: 250px;
            background: #f8f9fa;
            overflow: hidden;
        }
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ff4444;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .product-badge.featured {
            background: #ffc107;
        }
        .wishlist-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        .wishlist-btn:hover {
            background: #ff4444;
            color: white;
        }
        .product-info {
            padding: 15px;
        }
        .product-category {
            font-size: 12px;
            color: #999;
            margin-bottom: 5px;
        }
        .product-name {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-shop {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
        }
        .product-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
            font-size: 13px;
        }
        .product-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        .current-price {
            font-size: 20px;
            font-weight: 700;
            color: #00a651;
        }
        .original-price {
            font-size: 16px;
            color: #999;
            text-decoration: line-through;
        }
        .add-to-cart {
            width: 100%;
            padding: 10px;
            background: #00a651;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .add-to-cart:hover {
            background: #008f44;
        }
        
        /* No Results */
        .no-results {
            background: white;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .no-results-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }
        .no-results h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }
        .no-results p {
            color: #666;
            margin-bottom: 30px;
        }
        
        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }
        .page-link {
            padding: 10px 15px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            color: #666;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        .page-link:hover {
            border-color: #00a651;
            color: #00a651;
        }
        .page-link.active {
            background: #00a651;
            border-color: #00a651;
            color: white;
        }
        .page-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* List View */
        .products-list .product-card {
            display: grid;
            grid-template-columns: 200px 1fr;
            gap: 20px;
        }
        .products-list .product-image {
            height: 200px;
        }
        .products-list .product-info {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .products-list .add-to-cart {
            width: auto;
            padding: 10px 30px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            .filters-sidebar {
                display: none;
            }
            .mobile-filter-btn {
                display: block;
                width: 100%;
                padding: 12px;
                background: #00a651;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                margin-bottom: 20px;
            }
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .header-content {
                flex-wrap: wrap;
            }
            .search-form {
                order: 3;
                flex-basis: 100%;
                max-width: 100%;
                margin-top: 15px;
            }
        }
        
        /* Loading State */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <a href="<?= url('/') ?>" class="logo">🛒 OCS</a>
            
            <form action="<?= url('search') ?>" method="GET" class="search-form">
                <div class="search-input-group">
                    <input type="text" name="q" value="<?= htmlspecialchars($searchQuery) ?>" 
                           placeholder="Search for products..." class="search-input">
                    <button type="submit" class="search-btn">Search</button>
                </div>
            </form>
            
            <div class="header-actions">
                <a href="<?= url('cart') ?>" class="cart-btn">
                    🛒 Cart
                    <span class="cart-count">0</span>
                </a>
            </div>
        </div>
    </header>
    
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="<?= url('/') ?>">Home</a> / 
        <span>Search Results <?= $searchQuery ? 'for "' . htmlspecialchars($searchQuery) . '"' : '' ?></span>
    </div>
    
    <!-- Main Container -->
    <div class="container">
        <!-- Filters Sidebar -->
        <aside class="filters-sidebar">
            <div class="filter-header">
                <h2 class="filter-title">Filters</h2>
                <a href="<?= url('search') ?>" class="clear-filters">Clear All</a>
            </div>
            
            <!-- Price Range -->
            <div class="filter-section">
                <h3 class="filter-section-title">Price Range</h3>
                <div class="price-range">
                    <input type="number" class="price-input" placeholder="Min" value="<?= $minPrice ?>" id="minPrice">
                    <input type="number" class="price-input" placeholder="Max" value="<?= $maxPrice ?>" id="maxPrice">
                </div>
                <div class="price-slider">
                    <input type="range" min="0" max="10000" value="<?= $maxPrice ?>" class="slider" id="priceSlider">
                </div>
                <button onclick="applyPriceFilter()" style="width: 100%; margin-top: 15px; padding: 8px; background: #00a651; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Apply
                </button>
            </div>
            
            <!-- Categories -->
            <div class="filter-section">
                <h3 class="filter-section-title">Categories</h3>
                <?php foreach ($categories as $category): ?>
                    <div class="filter-option">
                        <label class="filter-checkbox">
                            <input type="checkbox" onchange="filterByCategory(<?= $category['id'] ?>)">
                            <span><?= htmlspecialchars($category['name']) ?></span>
                        </label>
                        <span class="filter-count">(<?= $category['count'] ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Brands -->
            <div class="filter-section">
                <h3 class="filter-section-title">Brands</h3>
                <?php foreach ($brands as $brand): ?>
                    <div class="filter-option">
                        <label class="filter-checkbox">
                            <input type="checkbox">
                            <span><?= htmlspecialchars($brand['name']) ?></span>
                        </label>
                        <span class="filter-count">(<?= $brand['count'] ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Shops -->
            <div class="filter-section">
                <h3 class="filter-section-title">Shops</h3>
                <?php foreach ($shops as $shop): ?>
                    <div class="filter-option">
                        <label class="filter-checkbox">
                            <input type="checkbox">
                            <span><?= htmlspecialchars($shop['name']) ?></span>
                        </label>
                        <span class="filter-count">(<?= $shop['count'] ?>)</span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Rating -->
            <div class="filter-section">
                <h3 class="filter-section-title">Customer Rating</h3>
                <div class="rating-filter">
                    <?php for ($i = 4; $i >= 1; $i--): ?>
                        <div class="rating-option" onclick="filterByRating(<?= $i ?>)">
                            <span class="stars">
                                <?php for ($j = 1; $j <= 5; $j++): ?>
                                    <?= $j <= $i ? '⭐' : '☆' ?>
                                <?php endfor; ?>
                            </span>
                            <span>& Up</span>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </aside>
        
        <!-- Search Results -->
        <main class="search-results">
            <!-- Results Header -->
            <div class="results-header">
                <div class="results-info">
                    <div class="results-count">
                        <?php if ($searchQuery): ?>
                            <strong><?= $totalResults ?></strong> results for "<?= htmlspecialchars($searchQuery) ?>"
                        <?php else: ?>
                            <strong><?= $totalResults ?></strong> products found
                        <?php endif; ?>
                    </div>
                    
                    <div class="sort-controls">
                        <div class="view-toggle">
                            <button class="view-btn active" onclick="setView('grid')">⊞</button>
                            <button class="view-btn" onclick="setView('list')">☰</button>
                        </div>
                        
                        <select class="sort-select" onchange="sortProducts(this.value)">
                            <option value="relevance" <?= $sortBy === 'relevance' ? 'selected' : '' ?>>Most Relevant</option>
                            <option value="price_low" <?= $sortBy === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                            <option value="price_high" <?= $sortBy === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                            <option value="rating" <?= $sortBy === 'rating' ? 'selected' : '' ?>>Customer Rating</option>
                            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Newest First</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid/List -->
            <?php if (empty($products)): ?>
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h2>No products found</h2>
                    <p>Try adjusting your search or filters to find what you're looking for.</p>
                    <a href="<?= url('/') ?>" style="padding: 12px 30px; background: #00a651; color: white; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: 600;">
                        Browse All Products
                    </a>
                </div>
            <?php else: ?>
                <div class="products-grid" id="productsContainer">
                    <?php foreach ($products as $product): ?>
                        <?php 
                        $discount = 0;
                        if (isset($product['compare_at_price']) && $product['compare_at_price'] > $product['base_price']) {
                            $discount = round((($product['compare_at_price'] - $product['base_price']) / $product['compare_at_price']) * 100);
                        }
                        ?>
                        <div class="product-card" onclick="window.location.href='<?= url('product/' . $product['slug']) ?>'">
                            <div class="product-image">
                                <?php if ($discount > 0): ?>
                                    <span class="product-badge"><?= $discount ?>% OFF</span>
                                <?php elseif (!empty($product['is_featured'])): ?>
                                    <span class="product-badge featured">Featured</span>
                                <?php endif; ?>
                                
                                <button class="wishlist-btn" onclick="event.stopPropagation(); toggleWishlist(<?= $product['id'] ?>)">
                                    🤍
                                </button>
                                
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?= strpos($product['image'], 'http') === 0 ? $product['image'] : asset($product['image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 80px;">
                                        📦
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="product-info">
                                <div class="product-category"><?= htmlspecialchars($product['category_name'] ?? 'Uncategorized') ?></div>
                                <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                                <div class="product-shop">🏪 <?= htmlspecialchars($product['shop_name'] ?? 'Unknown Shop') ?></div>
                                
                                <?php if (!empty($product['average_rating'])): ?>
                                    <div class="product-rating">
                                        <span class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <?= $i <= floor($product['average_rating']) ? '⭐' : '☆' ?>
                                            <?php endfor; ?>
                                        </span>
                                        <span><?= number_format($product['average_rating'], 1) ?></span>
                                        <span style="color: #999;">(<?= $product['reviews_count'] ?? 0 ?>)</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="product-price">
                                    <span class="current-price"><?= currency($product['base_price']) ?></span>
                                    <?php if ($discount > 0): ?>
                                        <span class="original-price"><?= currency($product['compare_at_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <button class="add-to-cart" onclick="event.stopPropagation(); addToCart(<?= $product['id'] ?>)">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?q=<?= urlencode($searchQuery) ?>&page=<?= $page - 1 ?>&sort=<?= $sortBy ?>" class="page-link">
                                ← Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= min($totalPages, 5); $i++): ?>
                            <a href="?q=<?= urlencode($searchQuery) ?>&page=<?= $i ?>&sort=<?= $sortBy ?>" 
                               class="page-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?q=<?= urlencode($searchQuery) ?>&page=<?= $page + 1 ?>&sort=<?= $sortBy ?>" class="page-link">
                                Next →
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let viewMode = 'grid';
        
        // Add to Cart
        function addToCart(productId) {
            const button = event.target;
            const originalText = button.textContent;
            
            button.textContent = 'Adding...';
            button.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                button.textContent = '✓ Added';
                button.style.background = '#28a745';
                
                // Update cart count
                const cartCount = document.querySelector('.cart-count');
                cartCount.textContent = parseInt(cartCount.textContent) + 1;
                
                // Reset button
                setTimeout(() => {
                    button.textContent = originalText;
                    button.style.background = '';
                    button.disabled = false;
                }, 2000);
            }, 500);
        }
        
        // Toggle Wishlist
        function toggleWishlist(productId) {
            const button = event.target;
            if (button.textContent === '🤍') {
                button.textContent = '❤️';
                button.style.color = '#ff4444';
            } else {
                button.textContent = '🤍';
                button.style.color = '';
            }
        }
        
        // Sort Products
        function sortProducts(sortBy) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('sort', sortBy);
            window.location.search = urlParams.toString();
        }
        
        // Filter by Category
        function filterByCategory(categoryId) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('category', categoryId);
            window.location.search = urlParams.toString();
        }
        
        // Filter by Rating
        function filterByRating(rating) {
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('rating', rating);
            window.location.search = urlParams.toString();
        }
        
        // Apply Price Filter
        function applyPriceFilter() {
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            
            const urlParams = new URLSearchParams(window.location.search);
            if (minPrice) urlParams.set('min_price', minPrice);
            if (maxPrice) urlParams.set('max_price', maxPrice);
            window.location.search = urlParams.toString();
        }
        
        // Toggle View Mode
        function setView(mode) {
            viewMode = mode;
            const container = document.getElementById('productsContainer');
            const gridBtn = document.querySelector('.view-btn:first-child');
            const listBtn = document.querySelector('.view-btn:last-child');
            
            if (mode === 'list') {
                container.classList.remove('products-grid');
                container.classList.add('products-list');
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            } else {
                container.classList.remove('products-list');
                container.classList.add('products-grid');
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            }
        }
        
        // Update price slider value
        document.getElementById('priceSlider')?.addEventListener('input', function() {
            document.getElementById('maxPrice').value = this.value;
        });
        
        // Initialize cart count
        fetch('<?= url('cart/count') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.count) {
                    document.querySelector('.cart-count').textContent = data.count;
                }
            })
            .catch(error => console.log('Cart count not loaded'));
    </script>
</body>
</html>