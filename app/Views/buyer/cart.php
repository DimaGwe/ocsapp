<?php

// DEBUG: Log cart structure
if (!empty($_SESSION['cart'])) {
    logger("Cart Debug - Raw cart data: " . print_r($_SESSION['cart'], true), 'debug');
    logger("Cart Debug - Cart keys: " . implode(', ', array_keys($_SESSION['cart'])), 'debug');
}

// FIX: Normalize cart structure if needed
if (!empty($_SESSION['cart'])) {
    $normalizedCart = [];

    foreach ($_SESSION['cart'] as $key => $item) {
        // Check if the key is numeric (product ID) or a generated key
        if (is_numeric($key)) {
            // Already correct format
            $normalizedCart[$key] = $item;
        } else {
            // Need to extract product_id from the item
            if (isset($item['product_id'])) {
                $normalizedCart[$item['product_id']] = $item;
            } elseif (isset($item['id'])) {
                $normalizedCart[$item['id']] = $item;
            }
        }
    }

    logger("Cart Debug - Normalized cart: " . print_r($normalizedCart, true), 'debug');
    $cart = $normalizedCart;
} else {
    $cart = [];
}
/**
 * Shopping Cart Page - Styled to match Checkout
 * File: app/Views/buyer/cart.php
 */

$cartItems = $cartItems ?? [];
$subtotal = $subtotal ?? 0;
$totalSavings = $totalSavings ?? 0;
$cartCount = $cartCount ?? 0;

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - OCSAPP</title>
    <?php echo csrfMeta(); ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Modular CSS Architecture -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo asset('css/global.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/components/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/components/footer.css'); ?>">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { height: 100%; }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; min-height: 100%; display: flex; flex-direction: column; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; flex: 1; }
        .footer { margin-top: auto; }

        /* Header matching checkout */
        .cart-header {
            background: white;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .cart-header h1 { font-size: 24px; color: #1a1a1a; }
        .cart-header .back-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .cart-header .back-link:hover { color: #00b207; }

        /* Grid layout matching checkout */
        .cart-content {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            align-items: start;
        }

        /* Card styling matching checkout */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title i { color: #00b207; }

        /* Shop group matching checkout */
        .shop-group {
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .shop-group:last-child { margin-bottom: 0; }
        .shop-name {
            font-size: 14px;
            font-weight: 600;
            color: #00b207;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Cart items */
        .cart-item {
            display: flex;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .cart-item:last-child { border-bottom: none; }
        .item-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border: 1px solid #e9ecef;
        }
        .item-image img { width: 100%; height: 100%; object-fit: cover; border-radius: 8px; }
        .item-details { flex: 1; }
        .item-name { font-size: 14px; font-weight: 500; color: #333; margin-bottom: 4px; }
        .item-price { font-size: 14px; font-weight: 700; color: #00b207; }
        .old-price { font-size: 12px; color: #999; text-decoration: line-through; margin-left: 8px; }
        .item-actions { display: flex; align-items: center; gap: 10px; margin-top: 8px; flex-wrap: wrap; }
        .qty-control { display: flex; align-items: center; gap: 5px; }
        .qty-btn {
            width: 28px;
            height: 28px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s;
            font-size: 14px;
        }
        .qty-btn:not(:disabled):hover { background: #f0fdf4; border-color: #00b207; }
        .qty-btn:disabled { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; }
        .qty-input {
            width: 45px;
            height: 28px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-weight: 600;
            font-size: 13px;
        }
        .remove-btn {
            color: #dc3545;
            cursor: pointer;
            padding: 4px 10px;
            border: 1px solid #dc3545;
            border-radius: 5px;
            font-size: 12px;
            background: white;
            transition: all 0.2s;
        }
        .remove-btn:hover { background: #dc3545; color: white; }
        .stock-warning { color: #dc3545; font-size: 11px; margin-top: 4px; }
        .stock-info { font-size: 11px; color: #888; }
        .item-total-row { margin-top: 8px; font-size: 13px; font-weight: 600; color: #1a1a1a; }

        /* Summary sticky matching checkout */
        .summary-sticky { position: sticky; top: 20px; }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 14px;
            color: #555;
        }
        .summary-row.total {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            border-top: 2px solid #e9ecef;
            margin-top: 8px;
            padding-top: 14px;
        }
        .summary-row .free-badge { color: #00b207; font-weight: 600; }

        .savings-badge {
            background: #d4edda;
            color: #155724;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .checkout-btn {
            width: 100%;
            padding: 16px;
            background: #00b207;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .checkout-btn:hover { background: #009906; transform: translateY(-1px); }

        .continue-shopping {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            text-align: center;
            color: #666;
            text-decoration: none;
            margin-top: 14px;
            font-size: 14px;
        }
        .continue-shopping:hover { color: #00b207; }

        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 12px;
            color: #888;
            margin-top: 12px;
        }
        .secure-badge i { color: #00b207; }

        /* Empty cart */
        .empty-cart { text-align: center; padding: 60px 20px; }
        .empty-cart-icon { font-size: 80px; margin-bottom: 20px; color: #ddd; }
        .empty-cart h2 { color: #1a1a1a; margin-bottom: 10px; font-size: 20px; }
        .empty-cart p { color: #888; margin-bottom: 24px; font-size: 14px; }
        .shop-now-btn {
            background: #00b207;
            color: white;
            padding: 14px 32px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s;
        }
        .shop-now-btn:hover { background: #009906; transform: translateY(-1px); }

        @media (max-width: 900px) {
            .cart-content { grid-template-columns: 1fr; }
            .summary-sticky { position: static; }
        }
        @media (max-width: 480px) {
            .cart-item { flex-direction: column; }
            .item-image { width: 100%; height: 160px; }
            .item-actions { justify-content: flex-start; }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="container">
        <div class="cart-header">
            <a href="<?php echo url('home'); ?>" class="back-link">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
            <h1>Shopping Cart</h1>
        </div>

        <?php if (empty($cartItems)): ?>
            <div class="card">
                <div class="empty-cart">
                    <div class="empty-cart-icon"><i class="fas fa-shopping-cart"></i></div>
                    <h2>Your cart is empty</h2>
                    <p>Add some products to get started!</p>
                    <a href="<?php echo url('home'); ?>" class="shop-now-btn">
                        <i class="fas fa-shopping-bag"></i> Start Shopping
                    </a>
                </div>
            </div>
        <?php else: ?>
            <?php
            // Group items by shop
            $itemsByShop = [];
            foreach ($cartItems as $item) {
                $shopId = $item['shop_id'] ?? 0;
                $shopName = $item['shop_name'] ?? 'OCSAPP Store';
                if (!isset($itemsByShop[$shopId])) {
                    $itemsByShop[$shopId] = [
                        'shop_name' => $shopName,
                        'items' => []
                    ];
                }
                $itemsByShop[$shopId]['items'][] = $item;
            }
            ?>
            <div class="cart-content">
                <!-- LEFT COLUMN: Cart Items -->
                <div>
                    <div class="card">
                        <h2 class="card-title">
                            <i class="fas fa-shopping-bag"></i>
                            Your Items (<?php echo $cartCount; ?> item<?php echo $cartCount !== 1 ? 's' : ''; ?>)
                        </h2>

                        <?php foreach ($itemsByShop as $shopId => $shop): ?>
                            <div class="shop-group">
                                <div class="shop-name">
                                    <i class="fas fa-store"></i>
                                    <?php echo htmlspecialchars($shop['shop_name']); ?>
                                </div>

                                <?php foreach ($shop['items'] as $item): ?>
                                    <div class="cart-item" data-key="<?php echo $item['key']; ?>" data-max-stock="<?php echo $item['stock_quantity']; ?>">
                                        <div class="item-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?php echo asset($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                            <?php else: ?>
                                                <i class="fas fa-box" style="font-size: 32px; color: #ccc;"></i>
                                            <?php endif; ?>
                                        </div>

                                        <div class="item-details">
                                            <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                            <div class="item-price">
                                                <?php echo currency($item['price']); ?>
                                                <?php if ($item['compare_at_price'] > $item['price']): ?>
                                                    <span class="old-price"><?php echo currency($item['compare_at_price']); ?></span>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($item['stock_quantity'] < 10): ?>
                                                <div class="stock-warning">
                                                    <i class="fas fa-exclamation-circle"></i> Only <?php echo $item['stock_quantity']; ?> left in stock
                                                </div>
                                            <?php endif; ?>

                                            <div class="item-actions">
                                                <div class="qty-control">
                                                    <button class="qty-btn qty-minus"
                                                            data-action="minus"
                                                            <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>−</button>
                                                    <input type="number"
                                                        class="qty-input"
                                                        value="<?php echo $item['quantity']; ?>"
                                                        min="1"
                                                        max="<?php echo $item['stock_quantity']; ?>"
                                                        readonly>
                                                    <button class="qty-btn qty-plus"
                                                            data-action="plus"
                                                            <?php echo $item['quantity'] >= $item['stock_quantity'] ? 'disabled' : ''; ?>>+</button>
                                                </div>

                                                <span class="stock-info">(<?php echo $item['stock_quantity']; ?> available)</span>

                                                <button class="remove-btn" data-remove="<?php echo $item['key']; ?>">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </div>

                                            <div class="item-total-row">
                                                Item Total: <span class="item-total"><?php echo currency($item['item_total']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Order Summary -->
                <div class="summary-sticky">
                    <div class="card">
                        <h2 class="card-title">
                            <i class="fas fa-receipt"></i>
                            Order Total
                        </h2>

                        <?php if ($totalSavings > 0): ?>
                            <div class="savings-badge">
                                <i class="fas fa-tag"></i> You're saving <?php echo currency($totalSavings); ?>!
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <span>Subtotal (<?php echo $cartCount; ?> item<?php echo $cartCount !== 1 ? 's' : ''; ?>)</span>
                            <span id="subtotalAmount"><?php echo currency($subtotal); ?></span>
                        </div>

                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span class="free-badge">FREE</span>
                        </div>

                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="totalAmount"><?php echo currency($subtotal); ?> CAD</span>
                        </div>

                        <button class="checkout-btn" onclick="proceedToCheckout()">
                            <i class="fas fa-lock"></i>
                            Proceed to Checkout
                        </button>

                        <a href="<?php echo url('home'); ?>" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>

                        <div class="secure-badge">
                            <i class="fas fa-shield-alt"></i>
                            Secure checkout - Your data is encrypted
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const csrfName = '<?php echo env('CSRF_TOKEN_NAME', '_csrf_token'); ?>';

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity buttons
            document.querySelectorAll('.qty-minus, .qty-plus').forEach(button => {
                button.addEventListener('click', function() {
                    const cartItem = this.closest('.cart-item');
                    const cartKey = cartItem.dataset.key;
                    const input = cartItem.querySelector('.qty-input');
                    const currentQty = parseInt(input.value);
                    const maxStock = parseInt(cartItem.dataset.maxStock);
                    const action = this.dataset.action;

                    let newQty = currentQty;
                    if (action === 'minus' && currentQty > 1) {
                        newQty = currentQty - 1;
                    } else if (action === 'plus' && currentQty < maxStock) {
                        newQty = currentQty + 1;
                    } else {
                        return; // Don't proceed if can't change
                    }

                    updateQuantity(cartKey, newQty, maxStock);
                });
            });

            // Remove buttons
            document.querySelectorAll('[data-remove]').forEach(button => {
                button.addEventListener('click', function() {
                    removeItem(this.dataset.remove);
                });
            });
        });

        function updateQuantity(cartKey, newQuantity, maxStock) {
            const cartItem = document.querySelector('[data-key="' + cartKey + '"]');
            const input = cartItem.querySelector('.qty-input');
            const minusBtn = cartItem.querySelector('.qty-minus');
            const plusBtn = cartItem.querySelector('.qty-plus');

            // Disable buttons during update
            minusBtn.disabled = true;
            plusBtn.disabled = true;
            input.style.opacity = '0.5';

            fetch('<?php echo url('cart/update'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: csrfName + '=' + csrfToken + '&cart_key=' + cartKey + '&quantity=' + newQuantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update display
                    input.value = newQuantity;
                    cartItem.querySelector('.item-total').textContent = formatCurrency(data.item_total);
                    document.getElementById('subtotalAmount').textContent = formatCurrency(data.subtotal);
                    document.getElementById('totalAmount').textContent = formatCurrency(data.subtotal) + ' CAD';

                    // Update cart count in header
                    if (window.updateCartDisplay) {
                        window.updateCartDisplay(data.cart_count);
                    }

                    // Re-enable buttons with proper states
                    input.style.opacity = '1';
                    minusBtn.disabled = (newQuantity <= 1);
                    plusBtn.disabled = (newQuantity >= maxStock);
                } else {
                    alert(data.message || 'Failed to update quantity');
                    input.style.opacity = '1';
                    minusBtn.disabled = false;
                    plusBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                input.style.opacity = '1';
                minusBtn.disabled = false;
                plusBtn.disabled = false;
            });
        }

        function removeItem(cartKey) {
            if (!confirm('Remove this item from cart?')) return;

            fetch('<?php echo url('cart/remove'); ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: csrfName + '=' + csrfToken + '&cart_key=' + cartKey
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count in header
                    if (window.updateCartDisplay) {
                        window.updateCartDisplay(data.cart_count);
                    }
                    // Reload page to show updated cart
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function proceedToCheckout() {
            window.location.href = '<?php echo url('checkout'); ?>';
        }

        function formatCurrency(amount) {
            const symbol = '<?php echo env('APP_CURRENCY', 'CAD') === 'DOP' ? '$' : '$'; ?>';
            return symbol + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    </script>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
