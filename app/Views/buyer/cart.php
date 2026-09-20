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
 * Shopping Cart Page
 * File: app/Views/buyer/cart.php
 * Updated 2026-09-09: moved onto the mc-header/mc-footer ecosystem standard
 * shared by /home, /categories and /shops; added EN/FR bilingual text
 * (previously hardcoded English throughout). Cart AJAX logic (quantity
 * update, remove item, checkout handoff) is unchanged.
 */

$cartItems = $cartItems ?? [];
$subtotal = $subtotal ?? 0;
$totalSavings = $totalSavings ?? 0;
$cartCount = $cartCount ?? 0;

// Get current language
$currentLang = $_SESSION['language'] ?? 'fr';
$fr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $fr ? 'Panier - Marché Central' : 'Cart - Marché Central' ?> | OCSAPP</title>
    <?php echo csrfMeta(); ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Modular CSS Architecture -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?php echo asset('css/global.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/components/header.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/components/footer.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset('css/pages/cart.css'); ?>">
</head>
<body>
    <!-- Header (Marché Central variant, consistent with /home, /categories and /shops) -->
    <?php $useMarcheHeader = true; ?>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="mc-shell" id="main-content" tabindex="-1">
        <div class="mc-wrap">
            <nav class="mc-breadcrumb" aria-label="<?= $fr ? "Fil d'Ariane" : 'Breadcrumb' ?>">
                <a href="<?= url('home') ?>"><i class="fas fa-store"></i><span><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></span></a>
                <span class="mc-sep">/</span>
                <span aria-current="page"><i class="fas fa-cart-shopping"></i> <?= $fr ? 'Panier' : 'Cart' ?></span>
            </nav>

            <div class="cart-header">
                <h1><?= $fr ? 'Votre panier' : 'Shopping Cart' ?></h1>
                <a href="<?php echo url('home'); ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> <?= $fr ? 'Continuer mes achats' : 'Continue Shopping' ?>
                </a>
            </div>

            <?php if (empty($cartItems)): ?>
                <div class="card">
                    <div class="empty-cart">
                        <div class="empty-cart-icon"><i class="fas fa-shopping-cart"></i></div>
                        <h2><?= $fr ? 'Votre panier est vide' : 'Your cart is empty' ?></h2>
                        <p><?= $fr ? 'Ajoutez des produits pour commencer !' : 'Add some products to get started!' ?></p>
                        <a href="<?php echo url('home'); ?>" class="shop-now-btn">
                            <i class="fas fa-shopping-bag"></i> <?= $fr ? 'Commencer à magasiner' : 'Start Shopping' ?>
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
                                <?= $fr ? 'Vos articles' : 'Your Items' ?> (<?php echo $cartCount; ?> <?= $fr
                                    ? ($cartCount > 1 ? 'articles' : 'article')
                                    : ($cartCount !== 1 ? 'items' : 'item') ?>)
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
                                                        <i class="fas fa-exclamation-circle"></i> <?= $fr
                                                            ? "Plus que {$item['stock_quantity']} en stock"
                                                            : "Only {$item['stock_quantity']} left in stock" ?>
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

                                                    <span class="stock-info">(<?php echo $item['stock_quantity']; ?> <?= $fr ? 'disponibles' : 'available' ?>)</span>

                                                    <button class="remove-btn" data-remove="<?php echo $item['key']; ?>">
                                                        <i class="fas fa-trash-alt"></i> <?= $fr ? 'Retirer' : 'Remove' ?>
                                                    </button>
                                                </div>

                                                <div class="item-total-row">
                                                    <?= $fr ? "Total de l'article :" : 'Item Total:' ?> <span class="item-total"><?php echo currency($item['item_total']); ?></span>
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
                                <?= $fr ? 'Total de la commande' : 'Order Total' ?>
                            </h2>

                            <?php if ($totalSavings > 0): ?>
                                <div class="savings-badge">
                                    <i class="fas fa-tag"></i> <?= $fr
                                        ? 'Vous économisez ' . currency($totalSavings) . ' !'
                                        : "You're saving " . currency($totalSavings) . '!' ?>
                                </div>
                            <?php endif; ?>

                            <div class="summary-row">
                                <span><?= $fr ? 'Sous-total' : 'Subtotal' ?> (<?php echo $cartCount; ?> <?= $fr
                                    ? ($cartCount > 1 ? 'articles' : 'article')
                                    : ($cartCount !== 1 ? 'items' : 'item') ?>)</span>
                                <span id="subtotalAmount"><?php echo currency($subtotal); ?></span>
                            </div>

                            <div class="summary-row">
                                <span><?= $fr ? 'Frais de livraison' : 'Delivery Fee' ?></span>
                                <span class="free-badge"><?= $fr ? 'GRATUIT' : 'FREE' ?></span>
                            </div>

                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="totalAmount"><?php echo currency($subtotal); ?> CAD</span>
                            </div>

                            <button class="checkout-btn" onclick="proceedToCheckout()">
                                <i class="fas fa-lock"></i>
                                <?= $fr ? 'Procéder au paiement' : 'Proceed to Checkout' ?>
                            </button>

                            <a href="<?php echo url('home'); ?>" class="continue-shopping">
                                <i class="fas fa-arrow-left"></i> <?= $fr ? 'Continuer mes achats' : 'Continue Shopping' ?>
                            </a>

                            <div class="secure-badge">
                                <i class="fas fa-shield-alt"></i>
                                <?= $fr ? 'Paiement sécurisé - vos données sont chiffrées' : 'Secure checkout - Your data is encrypted' ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer (Marché Central variant, matches /home, /categories and /shops) -->
    <footer class="mc-footer">
        <div class="mc-footer-wrap">
            <div class="mc-footer-top">
                <div class="mc-footer-brand-col">
                    <div class="mc-footer-brand">
                        <img src="<?= asset('images/logo.png') ?>" alt="<?= $fr ? 'Logo OCSAPP' : 'OCSAPP Logo' ?>">
                        <span class="mc-footer-logo-text">OCSAPP</span>
                    </div>
                    <p class="mc-footer-tagline"><?= $fr ? "L'infrastructure numérique tout-en-un du commerce local." : 'The all-in-one digital infrastructure for local commerce.' ?></p>
                    <p><?= $fr
                        ? 'OCSAPP Inc. · Constituée sous le régime fédéral de la Loi canadienne sur les sociétés par actions (n<sup>o</sup> de société 1750354-7) · Numéro d\'entreprise du Québec (NEQ) 1181584997'
                        : 'OCSAPP Inc. · Federally incorporated under the Canada Business Corporations Act (Corporation No. 1750354-7) · Quebec enterprise number (NEQ) 1181584997'
                    ?></p>
                    <p><?= $fr ? 'Siège social : Laval, Québec (H7H)' : 'Registered office: Laval, Québec (H7H)' ?></p>
                </div>

                <div class="mc-footer-col">
                    <h5><?= $fr ? 'Apprenez à nous connaître' : 'Get to Know Us' ?></h5>
                    <a href="<?= url('about') ?>"><?= $fr ? "À propos d'OCSAPP" : 'About OCSAPP' ?></a>
                    <a href="<?= url('contact') ?>"><?= $fr ? 'Contactez-nous' : 'Contact Us' ?></a>
                </div>

                <div class="mc-footer-col">
                    <h5><?= $fr ? 'Écosystème OCSAPP' : 'OCSAPP Ecosystem' ?></h5>
                    <a href="<?= url('home') ?>"><?= $fr ? 'Marché Central' : 'Marketplace Central' ?></a>
                    <a href="<?= url('buyer-central') ?>"><?= $fr ? 'Acheteur Central' : 'Buyer Central' ?></a>
                    <a href="<?= url('seller-central') ?>"><?= $fr ? 'Vendeur Central' : 'Seller Central' ?></a>
                    <a href="<?= url('supplier-central') ?>"><?= $fr ? 'Fournisseur Central' : 'Supplier Central' ?></a>
                    <a href="<?= url('driver-central') ?>"><?= $fr ? 'Livreur Central · ODA' : 'Driver Central · ODA' ?></a>
                    <a href="<?= url('distribution') ?>"><?= $fr ? 'Entreprise Centrale' : 'Business Central' ?></a>
                </div>

                <div class="mc-footer-col">
                    <h5><?= $fr ? 'Connectez-vous avec nous' : 'Connect With Us' ?></h5>
                    <a href="https://www.facebook.com/ocsapp.ca" target="_blank" rel="noopener">Facebook</a>
                    <a href="https://www.instagram.com/ocsapp.ca" target="_blank" rel="noopener">Instagram</a>
                    <a href="https://www.linkedin.com/company/ocsapp" target="_blank" rel="noopener">LinkedIn</a>
                </div>
            </div>

            <div class="mc-footer-bottom">
                <p>OCSAPP &copy; <?= date('Y') ?>. <?= $fr ? 'Tous droits réservés.' : 'All rights reserved.' ?></p>
                <div class="mc-footer-legal">
                    <a href="<?= url('privacy') ?>"><?= $fr ? 'Politique de confidentialité' : 'Privacy Policy' ?></a>
                    <a href="<?= url('terms') ?>"><?= $fr ? "Conditions d'utilisation" : 'Terms of Service' ?></a>
                    <a href="<?= url('cookies') ?>"><?= $fr ? 'Politique de cookies' : 'Cookie Policy' ?></a>
                    <a href="<?= url('returns') ?>"><?= $fr ? 'Retours' : 'Returns' ?></a>
                    <a href="<?= url('accessibility') ?>"><?= $fr ? 'Accessibilité' : 'Accessibility' ?></a>
                </div>
            </div>
        </div>
    </footer>

    <?php include __DIR__ . '/../components/auth-popup.php'; ?>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const csrfName = '<?php echo env('CSRF_TOKEN_NAME', '_csrf_token'); ?>';
        const cartText = <?php echo json_encode([
            'confirmRemove' => $fr ? 'Retirer cet article du panier ?' : 'Remove this item from cart?',
            'updateFailed' => $fr ? "Échec de la mise à jour de la quantité" : 'Failed to update quantity',
            'removeFailed' => $fr ? "Échec du retrait de l'article" : 'Failed to remove item',
            'genericError' => $fr ? 'Une erreur est survenue. Veuillez réessayer.' : 'An error occurred. Please try again.',
        ]); ?>;

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
                    alert(data.message || cartText.updateFailed);
                    input.style.opacity = '1';
                    minusBtn.disabled = false;
                    plusBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(cartText.genericError);
                input.style.opacity = '1';
                minusBtn.disabled = false;
                plusBtn.disabled = false;
            });
        }

        function removeItem(cartKey) {
            if (!confirm(cartText.confirmRemove)) return;

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
                    alert(data.message || cartText.removeFailed);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(cartText.genericError);
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
</body>
</html>
