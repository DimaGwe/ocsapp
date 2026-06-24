<?php
/**
 * Checkout Page - B2C Payment Integration
 * Supports Card (Stripe), PayPal, and Interac e-Transfer
 * File: app/Views/buyer/checkout.php
 */

$cartItems = $cartItems ?? [];
$ordersByShop = $ordersByShop ?? [];
$addresses = $addresses ?? [];
$cartCount = $cartCount ?? 0;

$currentLang = $_SESSION['language'] ?? 'fr';
$t = getTranslations($currentLang);

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['subtotal'];
}
$deliveryFee = isset($deliveryFee) ? (float)$deliveryFee : 5.00;
// Canadian tax: GST 5% + QST 9.975% = 14.975% (Quebec)
$gst   = round($subtotal * 0.05, 2);
$qst   = round($subtotal * 0.09975, 2);
$tax   = $gst + $qst;
$total = $subtotal + $deliveryFee + $tax;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['checkout_title'] ?? 'Checkout' ?> - OCS Marketplace</title>
    <?= csrfMeta() ?>

    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }

        .checkout-header {
            background: white;
            padding: 20px 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .checkout-header h1 { font-size: 24px; color: #1a1a1a; }
        .checkout-header .back-link {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .checkout-header .back-link:hover { color: #00b207; }

        .checkout-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .checkout-grid { grid-template-columns: 1fr; }
        }

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

        /* Shop group */
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

        .checkout-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .checkout-item:last-child { border-bottom: none; }
        .checkout-item img {
            width: 56px; height: 56px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        .checkout-item-info { flex: 1; }
        .checkout-item-name { font-size: 14px; font-weight: 500; color: #333; }
        .checkout-item-meta { font-size: 12px; color: #999; margin-top: 2px; }
        .checkout-item-price { font-size: 14px; font-weight: 700; color: #1a1a1a; white-space: nowrap; }

        /* Address */
        .address-list { display: flex; flex-direction: column; gap: 10px; }
        .address-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 14px 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .address-option:hover { border-color: #00b207; }
        .address-option.selected { border-color: #00b207; background: #f0fdf4; }
        .address-option input { margin-top: 4px; accent-color: #00b207; }
        .address-details { font-size: 13px; color: #555; line-height: 1.5; }
        .address-details strong { color: #1a1a1a; font-size: 14px; }
        .address-badge {
            font-size: 10px;
            background: #00b207;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-weight: 600;
            margin-left: 8px;
        }

        /* Delivery */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .form-group { margin-bottom: 12px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 6px; }
        .form-input, .form-select {
            width: 100%; padding: 10px 14px;
            border: 2px solid #e9ecef; border-radius: 8px;
            font-size: 14px; font-family: inherit;
            transition: border-color 0.2s;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: #00b207;
            box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
        }
        .form-textarea { resize: vertical; min-height: 70px; }

        /* Payment Methods */
        .payment-methods { display: flex; flex-direction: column; gap: 10px; }
        .payment-option {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .payment-option:hover { border-color: #00b207; }
        .payment-option.selected { border-color: #00b207; background: #f0fdf4; }
        .payment-option input[type="radio"] { display: none; }
        .payment-radio {
            width: 20px; height: 20px;
            border: 2px solid #ccc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
        }
        .payment-option.selected .payment-radio {
            border-color: #00b207;
        }
        .payment-option.selected .payment-radio::after {
            content: '';
            width: 10px; height: 10px;
            background: #00b207;
            border-radius: 50%;
        }
        .payment-icon {
            width: 40px; height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .payment-icon.card { background: #ede9fe; color: #635bff; }
        .payment-icon.paypal { background: #dbeafe; color: #003087; }
        .payment-icon.interac { background: #fef3c7; color: #d97706; }
        .payment-info h4 { font-size: 14px; font-weight: 600; color: #1a1a1a; }
        .payment-info p { font-size: 12px; color: #888; margin-top: 2px; }

        /* Summary (right column) */
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

        .place-order-btn {
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
        .place-order-btn:hover { background: #009906; transform: translateY(-1px); }
        .place-order-btn:disabled { background: #ccc; cursor: not-allowed; transform: none; }

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

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-info { background: #dbeafe; color: #1e40af; }
        .alert-error { background: #fee2e2; color: #991b1b; }

        .spinner { display: none; width: 20px; height: 20px; border: 3px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<div class="container">
    <div class="checkout-header">
        <a href="<?= url('cart') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> <?= $t['checkout_back_to_cart'] ?? 'Back to Cart' ?>
        </a>
        <h1><?= $t['checkout_title'] ?? 'Checkout' ?></h1>
    </div>

    <?php if ($flash = getFlash('error')): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <?php if ($flash = getFlash('info')): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <?= htmlspecialchars($flash) ?>
        </div>
    <?php endif; ?>

    <form id="checkoutForm">
        <?= csrfField() ?>

        <div class="checkout-grid">
            <!-- LEFT COLUMN -->
            <div>
                <!-- Order Summary by Shop -->
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-shopping-bag"></i>
                        <?= $t['checkout_order_summary'] ?? 'Order Summary' ?> (<?= count($cartItems) ?> <?= count($cartItems) !== 1 ? ($t['checkout_items'] ?? 'items') : ($t['checkout_item'] ?? 'item') ?>)
                    </h2>

                    <?php foreach ($ordersByShop as $shop): ?>
                        <div class="shop-group">
                            <div class="shop-name">
                                <i class="fas fa-store"></i>
                                <?= htmlspecialchars($shop['shop_name']) ?>
                            </div>
                            <?php foreach ($shop['items'] as $item): ?>
                                <div class="checkout-item">
                                    <img src="<?= !empty($item['product']['image_path']) ? url($item['product']['image_path']) : asset('images/placeholder.svg') ?>"
                                         alt="<?= htmlspecialchars($item['product']['name'] ?? '') ?>">
                                    <div class="checkout-item-info">
                                        <div class="checkout-item-name"><?= htmlspecialchars($item['product']['name'] ?? 'Product') ?></div>
                                        <div class="checkout-item-meta"><?= $t['checkout_qty'] ?? 'Qty' ?>: <?= (int)$item['quantity'] ?></div>
                                    </div>
                                    <div class="checkout-item-price">$<?= number_format($item['subtotal'], 2) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Delivery Address -->
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= $t['checkout_delivery_address'] ?? 'Delivery Address' ?>
                    </h2>

                    <?php if (!empty($addresses)): ?>
                        <div class="address-list">
                            <?php foreach ($addresses as $i => $addr): ?>
                                <label class="address-option <?= $i === 0 ? 'selected' : '' ?>">
                                    <input type="radio" name="address_id" value="<?= $addr['id'] ?>" <?= $i === 0 ? 'checked' : '' ?>>
                                    <div class="address-details">
                                        <strong>
                                            <?= htmlspecialchars($addr['label'] ?? 'Address') ?>
                                            <?php if (!empty($addr['is_default'])): ?>
                                                <span class="address-badge"><?= $t['checkout_address_default'] ?? 'Default' ?></span>
                                            <?php endif; ?>
                                        </strong><br>
                                        <?= htmlspecialchars($addr['street'] ?? $addr['address_line_1'] ?? '') ?><br>
                                        <?= htmlspecialchars(($addr['city'] ?? '') . ', ' . ($addr['province'] ?? '') . ' ' . ($addr['postal_code'] ?? '')) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: #666; font-size: 14px;"><?= $t['checkout_no_address'] ?? 'No saved addresses.' ?> <a href="<?= url('account/addresses') ?>" style="color: #00b207;"><?= $t['checkout_add_address'] ?? 'Add one' ?></a></p>
                    <?php endif; ?>
                </div>

                <!-- Delivery Preferences -->
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-truck"></i>
                        <?= $t['checkout_delivery_prefs'] ?? 'Delivery Preferences' ?>
                    </h2>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label"><?= $t['checkout_delivery_date'] ?? 'Delivery Date' ?></label>
                            <input type="date" name="delivery_date" class="form-input"
                                   value="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label"><?= $t['checkout_delivery_time'] ?? 'Delivery Time' ?></label>
                            <select name="delivery_time" class="form-select">
                                <option value="09:00-12:00"><?= $t['checkout_time_morning'] ?? 'Morning (9 AM – 12 PM)' ?></option>
                                <option value="12:00-15:00"><?= $t['checkout_time_afternoon'] ?? 'Afternoon (12 PM – 3 PM)' ?></option>
                                <option value="15:00-18:00"><?= $t['checkout_time_evening'] ?? 'Evening (3 PM – 6 PM)' ?></option>
                                <option value="18:00-21:00"><?= $t['checkout_time_night'] ?? 'Night (6 PM – 9 PM)' ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label"><?= $t['checkout_order_notes'] ?? 'Order Notes (Optional)' ?></label>
                        <textarea name="notes" class="form-input form-textarea" placeholder="<?= htmlspecialchars($t['checkout_notes_placeholder'] ?? 'Special delivery instructions...') ?>"></textarea>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-credit-card"></i>
                        <?= $t['checkout_payment_method'] ?? 'Payment Method' ?>
                    </h2>

                    <div class="payment-methods">
                        <!-- Credit/Debit Card -->
                        <label class="payment-option selected" data-method="card">
                            <input type="radio" name="payment_method" value="card" checked>
                            <div class="payment-radio"></div>
                            <div class="payment-icon card">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div class="payment-info">
                                <h4><?= $t['checkout_pay_card'] ?? 'Credit / Debit Card' ?></h4>
                                <p><?= $t['checkout_pay_card_sub'] ?? 'Visa, Mastercard, Amex' ?></p>
                            </div>
                        </label>

                        <!-- PayPal -->
                        <label class="payment-option" data-method="paypal">
                            <input type="radio" name="payment_method" value="paypal">
                            <div class="payment-radio"></div>
                            <div class="payment-icon paypal">
                                <i class="fab fa-paypal"></i>
                            </div>
                            <div class="payment-info">
                                <h4><?= $t['checkout_pay_paypal'] ?? 'PayPal' ?></h4>
                                <p><?= $t['checkout_pay_paypal_sub'] ?? 'Pay with your PayPal account' ?></p>
                            </div>
                        </label>

                        <!-- Interac e-Transfer -->
                        <label class="payment-option" data-method="transfer">
                            <input type="radio" name="payment_method" value="transfer">
                            <div class="payment-radio"></div>
                            <div class="payment-icon interac">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="payment-info">
                                <h4><?= $t['checkout_pay_interac'] ?? 'Interac e-Transfer' ?></h4>
                                <p><?= $t['checkout_pay_interac_sub'] ?? 'Send payment directly from your bank' ?></p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN (Summary + Place Order) -->
            <div class="summary-sticky">
                <div class="card">
                    <h2 class="card-title">
                        <i class="fas fa-receipt"></i>
                        <?= $t['checkout_order_total'] ?? 'Order Total' ?>
                    </h2>

                    <div class="summary-row">
                        <span><?= $t['checkout_subtotal'] ?? 'Subtotal' ?> (<?= count($cartItems) ?> <?= count($cartItems) !== 1 ? ($t['checkout_items'] ?? 'items') : ($t['checkout_item'] ?? 'item') ?>)</span>
                        <span>$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?= $t['checkout_delivery_fee'] ?? 'Delivery Fee' ?></span>
                        <span>$<?= number_format($deliveryFee, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?= $currentLang === 'fr' ? 'TPS (5%)' : 'GST (5%)' ?></span>
                        <span>$<?= number_format($gst, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span><?= $currentLang === 'fr' ? 'TVQ (9,975%)' : 'QST (9.975%)' ?></span>
                        <span>$<?= number_format($qst, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span><?= $t['checkout_total'] ?? 'Total' ?></span>
                        <span>$<?= number_format($total, 2) ?> CAD</span>
                    </div>

                    <button type="submit" class="place-order-btn" id="placeOrderBtn">
                        <span id="btnText"><?= $t['checkout_place_order'] ?? 'Place Order' ?> - $<?= number_format($total, 2) ?></span>
                        <div class="spinner" id="btnSpinner"></div>
                    </button>

                    <div class="secure-badge">
                        <i class="fas fa-lock"></i>
                        <?= $t['checkout_secure'] ?? 'Secure checkout – Your data is encrypted' ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>

<script>
// Payment method selection
document.querySelectorAll('.payment-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector('input[type="radio"]').checked = true;
    });
});

// Address selection
document.querySelectorAll('.address-option').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.address-option').forEach(o => o.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// CSRF helpers
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.content;
    const input = document.querySelector('input[name="_csrf_token"]');
    return input ? input.value : '';
}

function getCsrfName() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.dataset?.name || '_csrf_token';
}

// Form submission
document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('placeOrderBtn');
    const btnText = document.getElementById('btnText');
    const spinner = document.getElementById('btnSpinner');

    btn.disabled = true;
    btnText.textContent = '<?= addslashes($t['checkout_processing'] ?? 'Processing...') ?>';
    spinner.style.display = 'block';

    const formData = new FormData(this);
    formData.append(getCsrfName(), getCsrfToken());

    try {
        const response = await fetch('<?= url("checkout/process") ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (!data.success) {
            alert(data.message || '<?= addslashes($t['checkout_error_retry'] ?? 'Checkout failed. Please try again.') ?>');
            btn.disabled = false;
            btnText.textContent = '<?= addslashes($t['checkout_place_order'] ?? 'Place Order') ?> - $<?= number_format($total, 2) ?>';
            spinner.style.display = 'none';
            return;
        }

        // Route based on payment method
        if (data.redirect === 'gateway') {
            // Card or PayPal: create payment session then redirect
            btnText.textContent = '<?= addslashes($t['checkout_redirecting'] ?? 'Redirecting to payment...') ?>';

            const sessionData = new FormData();
            sessionData.append('payment_method', data.payment_method);
            sessionData.append(getCsrfName(), getCsrfToken());

            const sessionResponse = await fetch('<?= url("payment/create-session") ?>', {
                method: 'POST',
                body: sessionData
            });

            const sessionResult = await sessionResponse.json();

            if (sessionResult.error) {
                alert(sessionResult.error);
                btn.disabled = false;
                btnText.textContent = '<?= addslashes($t['checkout_place_order'] ?? 'Place Order') ?> - $<?= number_format($total, 2) ?>';
                spinner.style.display = 'none';
                return;
            }

            // Redirect to gateway
            if (sessionResult.redirect) {
                window.location.href = sessionResult.redirect;
            }
        } else {
            // Interac or direct redirect
            window.location.href = data.redirect;
        }

    } catch (error) {
        console.error('Checkout error:', error);
        alert('<?= addslashes($t['checkout_error_occurred'] ?? 'An error occurred. Please try again.') ?>');
        btn.disabled = false;
        btnText.textContent = '<?= addslashes($t['checkout_place_order'] ?? 'Place Order') ?> - $<?= number_format($total, 2) ?>';
        spinner.style.display = 'none';
    }
});
</script>

</body>
</html>
