<?php

namespace App\Controllers;

/**
 * CheckoutController - CLEANED & OPTIMIZED FOR OCS
 * Handles checkout and order creation with shop_inventory
 */
class CheckoutController
{
    private $db;
    
    public function __construct()
    {
        $this->db = \Database::getConnection();
    }
    
    /**
     * Display checkout page
     */
    public function index()
    {
        // Check if user is logged in
        if (!isLoggedIn()) {
            // Save current cart to preserve it during registration
            if (!empty($_SESSION['cart'])) {
                $_SESSION['pending_checkout_cart'] = $_SESSION['cart'];
                $_SESSION['return_to_checkout'] = true;
                
                logger("Guest user cart saved for checkout. Cart items: " . count($_SESSION['cart']), 'info');
            }
            
            setFlash('info', 'Please create an account or login to complete your order');
            redirect(url('register?redirect=/checkout'));
            return;
        }
        
        // Restore cart if user just logged in/registered
        if (isset($_SESSION['pending_checkout_cart'])) {
            $_SESSION['cart'] = $_SESSION['pending_checkout_cart'];
            unset($_SESSION['pending_checkout_cart']);
            unset($_SESSION['return_to_checkout']);
            
            logger("Cart restored for user ID: " . userId(), 'info');
        }
        
        // Get cart from session
        $cart = $_SESSION['cart'] ?? [];
        
        // Check if cart is empty
        if (empty($cart)) {
            setFlash('info', 'Your cart is empty');
            redirect(url('/'));
            return;
        }
        
        $userId = userId();
        
        // Extract product IDs from cart
        $productIds = [];
        foreach ($cart as $key => $item) {
            // Cart key format: productId_shopInventoryId
            $parts = explode('_', $key);
            $productId = (int)$parts[0];
            if ($productId) {
                $productIds[] = $productId;
            }
        }
        
        // Get cart items with shop_inventory details
        $cartItems = [];
        
        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            
            $query = "
                SELECT 
                    p.*,
                    si.shop_id,
                    si.price as inventory_price,
                    si.stock_quantity,
                    si.id as shop_inventory_id,
                    si.status as inventory_status,
                    s.name as shop_name,
                    (SELECT image_path FROM product_images WHERE product_id = p.id AND is_primary = 1 LIMIT 1) as image_path
                FROM products p
                INNER JOIN shop_inventory si ON p.id = si.product_id
                INNER JOIN shops s ON si.shop_id = s.id
                WHERE p.id IN ($placeholders)
                AND si.status = 'active'
                AND si.stock_quantity > 0
                ORDER BY FIELD(p.id, " . implode(',', $productIds) . ")
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($productIds);
            $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            foreach ($products as $product) {
                // Find matching cart item
                foreach ($_SESSION['cart'] as $key => $item) {
                    $parts = explode('_', $key);
                    $cartProductId = (int)$parts[0];
                    $cartShopInventoryId = isset($parts[1]) ? (int)$parts[1] : 0;
                    
                    if ($cartProductId == $product['id'] && 
                        ($cartShopInventoryId == 0 || $cartShopInventoryId == $product['shop_inventory_id'])) {
                        
                        $quantity = (int)($item['quantity'] ?? 1);
                        
                        $cartItems[] = [
                            'product' => $product,
                            'quantity' => $quantity,
                            'shop_id' => $product['shop_id'],
                            'shop_name' => $product['shop_name'],
                            'price' => $product['inventory_price'],
                            'subtotal' => $product['inventory_price'] * $quantity,
                            'shop_inventory_id' => $product['shop_inventory_id']
                        ];
                        break;
                    }
                }
            }
        }
        
        // If no valid items found, show helpful error
        if (empty($cartItems)) {
            logger("No valid cart items found. Product IDs: " . implode(',', $productIds), 'error');
            setFlash('error', 'Unable to process checkout. Please check if products are available.');
            redirect(url('cart'));
            return;
        }
        
        // Group items by shop for display
        $ordersByShop = [];
        foreach ($cartItems as $item) {
            $shopId = $item['shop_id'];
            if (!isset($ordersByShop[$shopId])) {
                $ordersByShop[$shopId] = [
                    'shop_id' => $shopId,
                    'shop_name' => $item['shop_name'],
                    'items' => [],
                    'subtotal' => 0
                ];
            }
            $ordersByShop[$shopId]['items'][] = $item;
            $ordersByShop[$shopId]['subtotal'] += $item['subtotal'];
        }
        
        // Get user's addresses
        $addresses = [];
        try {
            $stmt = $this->db->prepare("SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, created_at DESC");
            $stmt->execute(['user_id' => $userId]);
            $addresses = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            logger("Error fetching addresses: " . $e->getMessage(), 'error');
        }
        
        $appConfig = require BASE_PATH . '/config/app.php';
        $displayDeliveryFee = (float)($appConfig['delivery_fee'] ?? 5.00);

        view('buyer/checkout', [
            'cartItems' => $cartItems,
            'ordersByShop' => $ordersByShop,
            'addresses' => $addresses,
            'cartCount' => count($cart),
            'deliveryFee' => $displayDeliveryFee,
        ]);
    }
    
    /**
     * Process checkout and create orders
     * Payment flow:
     *   card/paypal → create orders (pending) → redirect to gateway → verify on return
     *   transfer (Interac) → create orders (pending) → redirect to success with instructions
     */
    public function process(): void
{
    if (!isLoggedIn()) {
        jsonResponse(['success' => false, 'message' => 'Please login to continue']);
        return;
    }

    // Validate CSRF
    $token = post(env('CSRF_TOKEN_NAME', '_csrf_token'), '');
    if (!verifyCsrfToken($token)) {
        jsonResponse(['success' => false, 'message' => 'Invalid token'], 403);
        return;
    }

    // Rate limit: max 5 checkout attempts per 5 minutes
    if (!rateLimit('checkout', 5, 300)) {
        jsonResponse(['success' => false, 'message' => 'Too many checkout attempts. Please wait a few minutes.'], 429);
        return;
    }

    $userId = userId();
    $cart = $_SESSION['cart'] ?? [];

    if (empty($cart)) {
        jsonResponse(['success' => false, 'message' => 'Cart is empty']);
        return;
    }

    // Get form data
    $addressId = intval(post('address_id', 0));
    $paymentMethod = sanitize(post('payment_method', 'card'));
    $deliveryDate = sanitize(post('delivery_date', date('Y-m-d', strtotime('+1 day'))));
    $deliveryTime = sanitize(post('delivery_time', '10:00-12:00'));
    $notes = sanitize(post('notes', ''));

    // Validate address belongs to current user and fetch full details
    $selectedAddress = null;
    if ($addressId > 0) {
        $addrStmt = $this->db->prepare("SELECT * FROM addresses WHERE id = :id AND user_id = :user_id");
        $addrStmt->execute(['id' => $addressId, 'user_id' => $userId]);
        $selectedAddress = $addrStmt->fetch(\PDO::FETCH_ASSOC);
        if (!$selectedAddress) {
            jsonResponse(['success' => false, 'message' => 'Invalid delivery address']);
            return;
        }
    }

    // Validate payment method
    $validPaymentMethods = ['card', 'paypal', 'transfer'];
    if (!in_array($paymentMethod, $validPaymentMethods)) {
        jsonResponse(['success' => false, 'message' => 'Invalid payment method']);
        return;
    }

    logger("Checkout - Payment: {$paymentMethod}, Address: {$addressId}, Date: {$deliveryDate}", 'info');

    // Determine if this is a gateway payment (requires redirect)
    $isGatewayPayment = in_array($paymentMethod, ['card', 'paypal']);

    try {
        $this->db->beginTransaction();

        logger("Processing checkout for user #{$userId}, cart items: " . count($cart), 'info');

        // Get cart items
        $cartItems = $this->getCartItemsWithShops($cart);

        if (empty($cartItems)) {
            $this->db->rollBack();
            jsonResponse(['success' => false, 'message' => 'No valid items in cart.']);
            return;
        }

        // Group by shop
        $itemsByShop = [];
        foreach ($cartItems as $item) {
            $shopId = $item['shop_id'];
            if (!isset($itemsByShop[$shopId])) {
                $itemsByShop[$shopId] = [];
            }
            $itemsByShop[$shopId][] = $item;
        }

        $createdOrders = [];

        foreach ($itemsByShop as $shopId => $items) {
            $orderNumber = $this->generateOrderNumber();

            // Calculate totals
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }

            $config = require BASE_PATH . '/config/app.php';
            $deliveryFee = (float)($config['delivery_fee'] ?? 5.00);
            // Canadian tax: GST 5% + QST 9.975% = 14.975% (Quebec)
            $gstRate  = 0.05;
            $qstRate  = 0.09975;
            $gst      = round($subtotal * $gstRate, 2);
            $qst      = round($subtotal * $qstRate, 2);
            $tax      = $gst + $qst;
            $discount = 0.00;
            $total    = $subtotal + $deliveryFee + $tax - $discount;

            // Create Order — always starts as pending
            $orderSQL = "
                INSERT INTO orders (
                    user_id, shop_id, order_number,
                    subtotal, tax, delivery_fee, discount, total,
                    payment_method, payment_status,
                    delivery_date, delivery_time,
                    delivery_address,
                    notes, status,
                    created_at, updated_at
                ) VALUES (
                    :user_id, :shop_id, :order_number,
                    :subtotal, :tax, :delivery_fee, :discount, :total,
                    :payment_method, :payment_status,
                    :delivery_date, :delivery_time,
                    :delivery_address,
                    :notes, 'pending',
                    NOW(), NOW()
                )
            ";

            $orderParams = [
                'user_id' => $userId,
                'shop_id' => $shopId,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'delivery_fee' => $deliveryFee,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'delivery_date' => $deliveryDate,
                'delivery_time' => $deliveryTime,
                'delivery_address' => $selectedAddress ? json_encode($selectedAddress) : null,
                'notes' => $notes
            ];

            $stmt = $this->db->prepare($orderSQL);

            try {
                $stmt->execute($orderParams);
                $orderId = $this->db->lastInsertId();
                logger("Order created: ID {$orderId}, Number {$orderNumber}", 'info');
            } catch (\PDOException $e) {
                logger("ORDER INSERT FAILED: " . $e->getMessage(), 'error');
                throw $e;
            }

            // Create Order Items
            foreach ($items as $itemIndex => $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];

                $itemSQL = "
                    INSERT INTO order_items (
                        order_id, product_id, shop_inventory_id,
                        product_name, sku,
                        quantity, price, subtotal,
                        created_at
                    ) VALUES (
                        :order_id, :product_id, :shop_inventory_id,
                        :product_name, :sku,
                        :quantity, :price, :subtotal,
                        NOW()
                    )
                ";

                $itemParams = [
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'shop_inventory_id' => $item['shop_inventory_id'],
                    'product_name' => $item['product_name'],
                    'sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $itemSubtotal
                ];

                $stmt = $this->db->prepare($itemSQL);

                try {
                    $stmt->execute($itemParams);
                } catch (\PDOException $e) {
                    logger("ITEM INSERT FAILED: " . $e->getMessage(), 'error');
                    throw $e;
                }

                // Update stock
                if ($item['shop_inventory_id']) {
                    try {
                        $this->updateInventoryStock($item['shop_inventory_id'], $item['quantity'], $orderId);
                    } catch (\Exception $e) {
                        logger("Stock update failed: " . $e->getMessage(), 'warning');
                    }
                }
            }

            // Status History
            try {
                $stmt = $this->db->prepare("
                    INSERT INTO delivery_status_history (
                        order_id, old_status, new_status,
                        changed_by, notes, created_at
                    ) VALUES (?, NULL, 'pending', ?, 'Order placed by customer', NOW())
                ");
                $stmt->execute([$orderId, $userId]);
            } catch (\PDOException $e) {
                logger("Status history failed: " . $e->getMessage(), 'warning');
            }

            // For Interac e-Transfer: NO delivery assignment yet (admin marks paid first)
            // For card/paypal: NO delivery assignment yet (assigned after payment verified)
            // Delivery is assigned ONLY after payment is confirmed

            $createdOrders[] = [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'shop_id' => $shopId,
                'total' => $total
            ];
        }

        $this->db->commit();

        // Store pending order IDs in session for payment verification
        $orderIds = array_column($createdOrders, 'order_id');
        $_SESSION['pending_order_ids'] = $orderIds;

        // Route based on payment method
        if ($paymentMethod === 'card') {
            // Don't clear cart yet — cleared after payment verification
            jsonResponse([
                'success' => true,
                'message' => 'Order created. Redirecting to payment...',
                'payment_method' => 'card',
                'order_ids' => $orderIds,
                'redirect' => 'gateway' // JS will call /payment/create-session
            ]);
        } elseif ($paymentMethod === 'paypal') {
            // Don't clear cart yet — cleared after payment verification
            jsonResponse([
                'success' => true,
                'message' => 'Order created. Redirecting to PayPal...',
                'payment_method' => 'paypal',
                'order_ids' => $orderIds,
                'redirect' => 'gateway' // JS will call /payment/create-session
            ]);
        } else {
            // Interac e-Transfer: clear cart, show instructions
            unset($_SESSION['cart']);

            $firstOrderNumber = $createdOrders[0]['order_number'];
            jsonResponse([
                'success' => true,
                'message' => 'Order placed! Please complete your Interac e-Transfer.',
                'payment_method' => 'transfer',
                'order_number' => $firstOrderNumber,
                'redirect' => url('/checkout/success?order=' . $firstOrderNumber . '&method=transfer')
            ]);
        }

    } catch (\PDOException $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        logger("CHECKOUT ERROR: {$e->getMessage()}", 'error');
        jsonResponse(['success' => false, 'message' => 'An error occurred processing your order. Please try again.']);

    } catch (\Exception $e) {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
        logger("GENERAL ERROR: " . $e->getMessage(), 'error');
        jsonResponse(['success' => false, 'message' => 'An error occurred. Please try again.']);
    }
}
    
    /**
     * Get cart items with shop information
     */
    private function getCartItemsWithShops(array $cart): array
    {
        if (empty($cart)) {
            return [];
        }
        
        // Extract product IDs and shop_inventory IDs
        $productIds = [];
        
        foreach ($cart as $key => $item) {
            $parts = explode('_', $key);
            $productId = (int)$parts[0];
            
            if ($productId) {
                $productIds[$productId] = $item;
            }
        }
        
        if (empty($productIds)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        // FOR UPDATE locks the rows so concurrent checkouts can't both pass the stock check
        $query = "
            SELECT
                p.id as product_id,
                p.name as product_name,
                p.sku,
                si.id as shop_inventory_id,
                si.shop_id,
                si.price,
                si.stock_quantity,
                si.status as inventory_status
            FROM products p
            INNER JOIN shop_inventory si ON p.id = si.product_id
            WHERE p.id IN ($placeholders)
            AND si.status = 'active'
            AND si.stock_quantity > 0
            FOR UPDATE
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(array_keys($productIds));
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        logger("Found " . count($products) . " products with shop_inventory", 'info');
        
        $result = [];
        foreach ($products as $product) {
            $productId = $product['product_id'];
            $cartItem = $productIds[$productId] ?? null;
            
            if (!$cartItem) {
                continue;
            }
            
            $quantity = (int)($cartItem['quantity'] ?? 1);
            
            // Check stock availability
            if ($product['stock_quantity'] < $quantity) {
                logger("Insufficient stock for product #{$productId}. Available: {$product['stock_quantity']}, Requested: {$quantity}", 'warning');
                continue;
            }
            
            $result[] = [
                'product_id' => $productId,
                'product_name' => $product['product_name'],
                'sku' => $product['sku'],
                'shop_inventory_id' => $product['shop_inventory_id'],
                'shop_id' => $product['shop_id'],
                'price' => floatval($product['price']),
                'quantity' => $quantity
            ];
        }
        
        return $result;
    }
    
    /**
     * Update shop_inventory stock
     */
    private function updateInventoryStock(int $shopInventoryId, int $quantity, int $orderId): void
{
    try {
        // Get current stock
        $stmt = $this->db->prepare("
            SELECT stock_quantity, sold_quantity 
            FROM shop_inventory 
            WHERE id = ?
        ");
        $stmt->execute([$shopInventoryId]);
        $inventory = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$inventory) {
            logger("Shop inventory #{$shopInventoryId} not found", 'error');
            return;
        }
        
        $quantityBefore = $inventory['stock_quantity'];
        $quantityAfter = $quantityBefore - $quantity;
        
        // WHERE stock_quantity >= ? prevents going negative under concurrent load
        $stmt = $this->db->prepare("
            UPDATE shop_inventory
            SET stock_quantity = stock_quantity - ?,
                sold_quantity  = sold_quantity + ?,
                updated_at     = NOW()
            WHERE id = ? AND stock_quantity >= ?
        ");
        $stmt->execute([$quantity, $quantity, $shopInventoryId, $quantity]);

        if ($stmt->rowCount() === 0) {
            throw new \RuntimeException("Insufficient stock for inventory #{$shopInventoryId} — order rejected to prevent oversell.");
        }
        
        logger("Updated shop_inventory #{$shopInventoryId}: -{$quantity} units (from {$quantityBefore} to {$quantityAfter})", 'info');
        
    } catch (\PDOException $e) {
        logger("Stock update error: " . $e->getMessage(), 'error');
        throw $e;
    }
}

    
    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        return 'OCS' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }
    
    /**
     * Checkout success page
     */
    public function success(): void
    {
        if (!isLoggedIn()) {
            redirect(url('login'));
            return;
        }

        $orderNumber = sanitize(get('order', ''));
        $isPaid = get('paid', '') === '1';
        $paymentMethodParam = sanitize(get('method', ''));

        if (empty($orderNumber)) {
            redirect(url('/'));
            return;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT o.*, s.name as shop_name
                FROM orders o
                LEFT JOIN shops s ON o.shop_id = s.id
                WHERE o.order_number = :order_number
                AND o.user_id = :user_id
            ");

            $stmt->execute([
                'order_number' => $orderNumber,
                'user_id' => userId()
            ]);

            $order = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$order) {
                setFlash('error', 'Order not found');
                redirect(url('/'));
                return;
            }

            // Get order items
            $stmt = $this->db->prepare("
                SELECT oi.*, p.slug as product_slug,
                       (SELECT image_path FROM product_images WHERE product_id = oi.product_id AND is_primary = 1 LIMIT 1) as image_path
                FROM order_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = :order_id
            ");

            $stmt->execute(['order_id' => $order['id']]);
            $items = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get Interac settings if this is an e-Transfer order
            $interacSettings = [];
            if ($order['payment_method'] === 'transfer') {
                try {
                    $stmt = $this->db->prepare("
                        SELECT setting_key, setting_value FROM payment_settings
                        WHERE setting_key IN ('interac_email', 'interac_instructions')
                    ");
                    $stmt->execute();
                    foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $row) {
                        $interacSettings[$row['setting_key']] = $row['setting_value'];
                    }
                } catch (\PDOException $e) {
                    logger("Error loading Interac settings: " . $e->getMessage(), 'warning');
                }
            }

            view('buyer/checkout-success', [
                'order' => $order,
                'items' => $items,
                'isPaid' => $isPaid || $order['payment_status'] === 'paid',
                'interacSettings' => $interacSettings
            ]);

        } catch (\PDOException $e) {
            logger("Error loading order success: " . $e->getMessage(), 'error');
            setFlash('error', 'Failed to load order details');
            redirect(url('/'));
        }
    }

    /**
     * Automatically assign delivery driver to an order
     * Finds available driver with lowest workload in shop's zone
     *
     * @param int $orderId Order ID
     * @param int $shopId Shop ID
     * @param float $orderTotal Order total for delivery fee calculation
     * @throws \Exception If assignment fails
     */
    private function autoAssignDelivery(int $orderId, int $shopId, float $orderTotal): void
    {
        // Get shop information (zone, location)
        $stmt = $this->db->prepare("
            SELECT id, name, zone_id, latitude, longitude
            FROM shops
            WHERE id = ?
        ");
        $stmt->execute([$shopId]);
        $shop = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$shop) {
            throw new \Exception("Shop #{$shopId} not found");
        }

        // Find available driver in the same zone with lowest active deliveries
        $stmt = $this->db->prepare("
            SELECT u.id, u.first_name, u.last_name,
                   COUNT(da.id) as active_deliveries
            FROM users u
            LEFT JOIN delivery_assignments da ON u.id = da.driver_id
                AND da.status IN ('assigned', 'accepted', 'picked_up', 'on_the_way')
            WHERE u.role = 'driver'
            AND u.is_active = 1
            AND (u.zone_id = :zone_id OR u.zone_id IS NULL)
            GROUP BY u.id
            ORDER BY active_deliveries ASC, u.id ASC
            LIMIT 1
        ");

        $stmt->execute(['zone_id' => $shop['zone_id']]);
        $driver = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$driver) {
            logger("No available drivers found for zone #{$shop['zone_id']}, order will require manual assignment", 'warning');
            return; // Don't throw exception - allow manual assignment
        }

        // Calculate delivery fee from config
        $appConfig = require BASE_PATH . '/config/app.php';
        $deliveryFee = (float)($appConfig['delivery_fee'] ?? 5.00);

        // Create delivery assignment
        $stmt = $this->db->prepare("
            INSERT INTO delivery_assignments
            (order_id, driver_id, shop_id, status, delivery_fee, assigned_at, created_at)
            VALUES
            (:order_id, :driver_id, :shop_id, 'assigned', :delivery_fee, NOW(), NOW())
        ");

        $stmt->execute([
            'order_id' => $orderId,
            'driver_id' => $driver['id'],
            'shop_id' => $shopId,
            'delivery_fee' => $deliveryFee
        ]);

        $assignmentId = $this->db->lastInsertId();

        logger("✓ Order #{$orderId} auto-assigned to driver #{$driver['id']} ({$driver['first_name']} {$driver['last_name']}), assignment #{$assignmentId}", 'info');

        // TODO: Send notification to driver (SMS/Email/Push)
        // This can be implemented later with notification service
    }
}