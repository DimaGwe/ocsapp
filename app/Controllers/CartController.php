<?php

namespace App\Controllers;

/**
 * CartController - FIXED FOR shop_inventory TABLE
 * Handles shopping cart functionality using sessions
 */
class CartController {
    
    /**
     * Initialize cart in session if not exists
     */
    private function initCart(): void {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }
    
    /**
     * Get cart count - Returns JSON
     */
    public function getCount(): void {
        try {
            $this->initCart();
            
            $count = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $count += (int)($item['quantity'] ?? 0);
                }
            }
            
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['count' => $count, 'success' => true]);
            exit;
            
        } catch (\Exception $e) {
            logger("Cart count error: " . $e->getMessage(), 'error');
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['count' => 0, 'success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    /**
     * Add product to cart
     * FIXED: Uses shop_inventory table
     * FIXED: Handles JSON requests properly
     */
    public function add(): void {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        // Handle JSON requests - parse php://input for JSON content type
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true) ?? [];
        }

        // Get CSRF token from JSON data, POST data, or header
        $tokenName = env('CSRF_TOKEN_NAME', '_csrf_token');
        $token = $data[$tokenName] ?? post($tokenName, '') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        // Get product data from JSON or POST
        $productId = (int) ($data['product_id'] ?? post('product_id', 0));
        $quantity = (int) ($data['quantity'] ?? post('quantity', 1));
        $shopInventoryId = (int) ($data['shop_inventory_id'] ?? post('shop_inventory_id', 0)); // Optional: specific shop
        
        if ($productId <= 0 || $quantity <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid product or quantity']);
            return;
        }
        
        try {
            $db = \Database::getConnection();
            
            // Get product details with shop_inventory information
            // FIXED: Query shop_inventory instead of inventories
            $stmt = $db->prepare("
                SELECT 
                    p.id, 
                    p.name, 
                    p.sku,
                    si.price,
                    si.compare_at_price,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image,
                    si.id as shop_inventory_id,
                    si.stock_quantity,
                    si.shop_id,
                    s.name as shop_name
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id 
                    AND si.status = 'active' 
                    AND si.stock_quantity > 0
                INNER JOIN shops s ON si.shop_id = s.id AND s.is_active = 1
                WHERE p.id = ? AND p.status = 'active'
                " . ($shopInventoryId ? " AND si.id = ?" : "") . "
                ORDER BY si.price ASC
                LIMIT 1
            ");
            
            $params = [$productId];
            if ($shopInventoryId) {
                $params[] = $shopInventoryId;
            }
            
            $stmt->execute($params);
            $product = $stmt->fetch();
            
            if (!$product) {
                jsonResponse(['success' => false, 'message' => 'Product not found or unavailable']);
                return;
            }
            
            // Check if product has inventory
            if (!$product['shop_inventory_id']) {
                jsonResponse(['success' => false, 'message' => 'Product not available for purchase']);
                return;
            }
            
            // Initialize cart
            $this->initCart();
            
            // Create cart item key - product ID + shop inventory ID
            $cartKey = $productId . '_' . $product['shop_inventory_id'];
            
            // Calculate total quantity if item already in cart
            $existingQty = isset($_SESSION['cart'][$cartKey]) ? $_SESSION['cart'][$cartKey]['quantity'] : 0;
            $totalRequestedQty = $existingQty + $quantity;
            
            // Check stock availability
            if ($totalRequestedQty > $product['stock_quantity']) {
                jsonResponse([
                    'success' => false, 
                    'message' => "Only {$product['stock_quantity']} units available in stock",
                    'available_stock' => $product['stock_quantity']
                ]);
                return;
            }
            
            // Add or update cart item
            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] = $totalRequestedQty;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => (float)$product['price'],
                    'compare_at_price' => (float)($product['compare_at_price'] ?? 0),
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'stock_quantity' => $product['stock_quantity'],
                    'shop_inventory_id' => $product['shop_inventory_id'],
                    'shop_id' => $product['shop_id'],
                    'shop_name' => $product['shop_name'],
                    'added_at' => time()
                ];
            }
            
            // Calculate cart count
            $cartCount = 0;
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
            
            logger("Product added to cart: Product ID {$productId}, Qty: {$quantity}, Shop Inventory: {$product['shop_inventory_id']}", 'info');
            
            jsonResponse([
                'success' => true, 
                'message' => 'Product added to cart!',
                'cart_count' => $cartCount,
                'quantity' => $_SESSION['cart'][$cartKey]['quantity'],
                'available_stock' => $product['stock_quantity']
            ]);
            
        } catch (\PDOException $e) {
            logger("Cart add error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error adding product to cart: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            logger("Cart add general error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error adding product to cart']);
        }
    }
    
    /**
     * Display cart page
     * FIXED: Uses shop_inventory
     */
    public function index(): void {
        $this->initCart();
        
        try {
            $db = \Database::getConnection();
            
            $cartItems = [];
            $subtotal = 0;
            $totalSavings = 0;
            $stockIssues = [];
            
            foreach ($_SESSION['cart'] as $cartKey => $item) {
                // Verify product still exists and get current price from shop_inventory
                $stmt = $db->prepare("
                    SELECT 
                        p.id, 
                        p.name, 
                        p.sku,
                        si.price,
                        si.compare_at_price,
                        (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image,
                        si.id as shop_inventory_id,
                        si.stock_quantity,
                        si.shop_id,
                        s.name as shop_name
                    FROM products p
                    INNER JOIN shop_inventory si ON p.id = si.product_id AND si.status = 'active'
                    INNER JOIN shops s ON si.shop_id = s.id
                    WHERE p.id = ? 
                        AND si.id = ?
                        AND p.status = 'active'
                ");
                $stmt->execute([
                    $item['product_id'], 
                    $item['shop_inventory_id']
                ]);
                $currentProduct = $stmt->fetch();
                
                if ($currentProduct) {
                    // Update cart with current prices and stock
                    $_SESSION['cart'][$cartKey]['price'] = (float)$currentProduct['price'];
                    $_SESSION['cart'][$cartKey]['compare_at_price'] = (float)($currentProduct['compare_at_price'] ?? 0);
                    $_SESSION['cart'][$cartKey]['stock_quantity'] = $currentProduct['stock_quantity'];
                    $_SESSION['cart'][$cartKey]['name'] = $currentProduct['name'];
                    $_SESSION['cart'][$cartKey]['image'] = $currentProduct['image'];
                    
                    // Check stock availability
                    $stockAvailable = true;
                    $stockMessage = '';
                    
                    if ($item['quantity'] > $currentProduct['stock_quantity']) {
                        $stockAvailable = false;
                        
                        if ($currentProduct['stock_quantity'] > 0) {
                            $stockMessage = "Only {$currentProduct['stock_quantity']} units available";
                            // Auto-adjust quantity
                            $_SESSION['cart'][$cartKey]['quantity'] = $currentProduct['stock_quantity'];
                            $item['quantity'] = $currentProduct['stock_quantity'];
                        } else {
                            $stockMessage = "Out of stock";
                        }
                        
                        $stockIssues[] = [
                            'cart_key' => $cartKey,
                            'product_name' => $currentProduct['name'],
                            'message' => $stockMessage,
                            'available_stock' => $currentProduct['stock_quantity']
                        ];
                    }
                    
                    $itemTotal = (float)$currentProduct['price'] * $item['quantity'];
                    $subtotal += $itemTotal;
                    
                    if ($currentProduct['compare_at_price'] > $currentProduct['price']) {
                        $totalSavings += ($currentProduct['compare_at_price'] - $currentProduct['price']) * $item['quantity'];
                    }
                    
                    $cartItems[] = array_merge($item, [
                        'key' => $cartKey,
                        'item_total' => $itemTotal,
                        'stock_available' => $stockAvailable,
                        'stock_message' => $stockMessage
                    ]);
                } else {
                    // Product no longer available - remove from cart
                    unset($_SESSION['cart'][$cartKey]);
                    $stockIssues[] = [
                        'cart_key' => $cartKey,
                        'product_name' => $item['name'] ?? 'Unknown Product',
                        'message' => 'Product no longer available',
                        'available_stock' => 0
                    ];
                }
            }
            
            view('buyer.cart', [
                'cartItems' => $cartItems,
                'subtotal' => $subtotal,
                'totalSavings' => $totalSavings,
                'cartCount' => count($cartItems),
                'stockIssues' => $stockIssues
            ]);
            
        } catch (\PDOException $e) {
            logger("Cart view error: " . $e->getMessage(), 'error');
            setFlash('error', 'Error loading cart');
            redirect(url('/'));
        }
    }
    
    /**
     * Update cart item quantity
     * FIXED: Validates against shop_inventory stock
     * FIXED: Handles JSON requests properly
     */
    public function update(): void {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        // Handle JSON requests
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true) ?? [];
        }

        // Get CSRF token from JSON data, POST data, or header
        $tokenName = env('CSRF_TOKEN_NAME', '_csrf_token');
        $token = $data[$tokenName] ?? post($tokenName, '') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $cartKey = $data['cart_key'] ?? post('cart_key', '');
        $quantity = (int) ($data['quantity'] ?? post('quantity', 0));
        
        $this->initCart();
        
        if (!isset($_SESSION['cart'][$cartKey])) {
            jsonResponse(['success' => false, 'message' => 'Cart item not found']);
            return;
        }
        
        if ($quantity <= 0) {
            jsonResponse(['success' => false, 'message' => 'Invalid quantity']);
            return;
        }
        
        try {
            $db = \Database::getConnection();
            
            $item = $_SESSION['cart'][$cartKey];
            
            // Check current stock in shop_inventory
            $stmt = $db->prepare("
                SELECT stock_quantity 
                FROM shop_inventory 
                WHERE id = ? AND status = 'active'
            ");
            $stmt->execute([$item['shop_inventory_id']]);
            $inventory = $stmt->fetch();
            
            if (!$inventory) {
                jsonResponse(['success' => false, 'message' => 'Product no longer available']);
                return;
            }
            
            if ($quantity > $inventory['stock_quantity']) {
                jsonResponse([
                    'success' => false, 
                    'message' => "Only {$inventory['stock_quantity']} units available",
                    'available_stock' => $inventory['stock_quantity']
                ]);
                return;
            }
            
            $_SESSION['cart'][$cartKey]['quantity'] = $quantity;
            $_SESSION['cart'][$cartKey]['stock_quantity'] = $inventory['stock_quantity'];
            
            $itemTotal = $_SESSION['cart'][$cartKey]['price'] * $quantity;
            
            $subtotal = 0;
            $cartCount = 0;
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
                $cartCount += $item['quantity'];
            }
            
            jsonResponse([
                'success' => true,
                'item_total' => $itemTotal,
                'subtotal' => $subtotal,
                'cart_count' => $cartCount
            ]);
            
        } catch (\Exception $e) {
            logger("Cart update error: " . $e->getMessage(), 'error');
            jsonResponse(['success' => false, 'message' => 'Error updating cart']);
        }
    }
    
    /**
     * Remove item from cart
     * FIXED: Handles JSON requests properly
     */
    public function remove(): void {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        // Handle JSON requests
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true) ?? [];
        }

        // Get CSRF token from JSON data, POST data, or header
        $tokenName = env('CSRF_TOKEN_NAME', '_csrf_token');
        $token = $data[$tokenName] ?? post($tokenName, '') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $cartKey = $data['cart_key'] ?? post('cart_key', '');
        
        $this->initCart();
        
        if (isset($_SESSION['cart'][$cartKey])) {
            unset($_SESSION['cart'][$cartKey]);
        }
        
        $cartCount = 0;
        foreach ($_SESSION['cart'] as $item) {
            $cartCount += $item['quantity'];
        }
        
        jsonResponse(['success' => true, 'cart_count' => $cartCount]);
    }
    
    /**
     * Clear entire cart
     * FIXED: Handles JSON requests properly
     */
    public function clear(): void {
        if (!isPost()) {
            jsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
            return;
        }

        // Handle JSON requests
        $data = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            $jsonInput = file_get_contents('php://input');
            $data = json_decode($jsonInput, true) ?? [];
        }

        // Get CSRF token from JSON data, POST data, or header
        $tokenName = env('CSRF_TOKEN_NAME', '_csrf_token');
        $token = $data[$tokenName] ?? post($tokenName, '') ?: ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');

        if (!verifyCsrfToken($token)) {
            jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
            return;
        }

        $_SESSION['cart'] = [];
        
        jsonResponse(['success' => true]);
    }
}