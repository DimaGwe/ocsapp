<?php
/**
 * Checkout Success Page
 * Shows order confirmation with payment status
 * For Interac e-Transfer: shows payment instructions
 * File: app/Views/buyer/checkout-success.php
 */

$order = $order ?? [];
$items = $items ?? [];
$isPaid = $isPaid ?? false;
$interacSettings = $interacSettings ?? [];

$currentLang = $_SESSION['language'] ?? 'fr';
$isInterac = ($order['payment_method'] ?? '') === 'transfer';
$interacEmail = $interacSettings['interac_email'] ?? '';
$interacInstructions = $interacSettings['interac_instructions'] ?? 'Please send an Interac e-Transfer to the email above with your order number as the message.';
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - OCS Marketplace</title>

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
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }

        .success-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            margin-bottom: 24px;
        }

        .success-icon {
            width: 80px; height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
        }
        .success-icon.paid {
            background: #d1fae5;
            color: #059669;
        }
        .success-icon.pending {
            background: #fef3c7;
            color: #d97706;
        }

        .success-title {
            font-size: 26px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .success-subtitle {
            font-size: 15px;
            color: #666;
            margin-bottom: 24px;
        }

        .order-number-badge {
            display: inline-block;
            background: #f0fdf4;
            border: 2px solid #00b207;
            color: #00b207;
            padding: 10px 24px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .payment-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 16px;
        }
        .badge-paid { background: #d1fae5; color: #059669; }
        .badge-pending { background: #fef3c7; color: #92400e; }

        /* Interac Instructions */
        .interac-box {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border: 2px solid #f59e0b;
            border-radius: 14px;
            padding: 28px;
            margin: 24px 0;
            text-align: left;
        }

        .interac-box h3 {
            font-size: 18px;
            font-weight: 700;
            color: #92400e;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .interac-detail {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(245,158,11,0.2);
        }
        .interac-detail:last-child { border-bottom: none; }
        .interac-detail-label {
            font-size: 13px;
            font-weight: 600;
            color: #92400e;
        }
        .interac-detail-value {
            font-size: 15px;
            font-weight: 700;
            color: #1a1a1a;
        }
        .interac-detail-value.email {
            color: #00b207;
            font-size: 16px;
        }

        .interac-instructions {
            margin-top: 16px;
            padding: 14px;
            background: rgba(255,255,255,0.7);
            border-radius: 8px;
            font-size: 13px;
            color: #78350f;
            line-height: 1.6;
        }

        .interac-warning {
            margin-top: 12px;
            font-size: 12px;
            color: #b45309;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Order Items */
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .card-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .order-item {
            display: flex;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .order-item:last-child { border-bottom: none; }
        .order-item img {
            width: 50px; height: 50px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #e9ecef;
        }
        .order-item-info { flex: 1; }
        .order-item-name { font-size: 14px; font-weight: 500; color: #333; }
        .order-item-qty { font-size: 12px; color: #999; }
        .order-item-price { font-size: 14px; font-weight: 700; color: #1a1a1a; white-space: nowrap; }

        .order-totals {
            border-top: 2px solid #f0f0f0;
            margin-top: 12px;
            padding-top: 12px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 14px;
            color: #555;
        }
        .total-row.grand {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            border-top: 2px solid #e9ecef;
            margin-top: 8px;
            padding-top: 12px;
        }

        /* Actions */
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            margin-top: 24px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            font-family: inherit;
            transition: all 0.2s;
        }
        .btn-primary { background: #00b207; color: white; }
        .btn-primary:hover { background: #009906; }
        .btn-secondary { background: #f3f4f6; color: #555; border: 2px solid #e5e7eb; }
        .btn-secondary:hover { background: #e5e7eb; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../components/header.php'; ?>

<div class="container">
    <!-- Success Header -->
    <div class="success-card">
        <?php if ($isPaid): ?>
            <div class="success-icon paid">
                <i class="fas fa-check"></i>
            </div>
            <h1 class="success-title">Payment Confirmed!</h1>
            <p class="success-subtitle">Your order has been placed and payment received successfully.</p>
        <?php else: ?>
            <div class="success-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <h1 class="success-title">Order Placed!</h1>
            <p class="success-subtitle">
                <?php if ($isInterac): ?>
                    Please complete your Interac e-Transfer to confirm your order.
                <?php else: ?>
                    Your order is being processed.
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <div class="order-number-badge">
            #<?= htmlspecialchars($order['order_number'] ?? 'N/A') ?>
        </div>

        <div>
            <?php if ($isPaid): ?>
                <span class="payment-status-badge badge-paid">
                    <i class="fas fa-check-circle"></i> Payment Confirmed
                </span>
            <?php elseif ($isInterac): ?>
                <span class="payment-status-badge badge-pending">
                    <i class="fas fa-clock"></i> Awaiting e-Transfer
                </span>
            <?php else: ?>
                <span class="payment-status-badge badge-pending">
                    <i class="fas fa-clock"></i> Payment Pending
                </span>
            <?php endif; ?>
        </div>

        <?php if ($isInterac && !$isPaid): ?>
            <!-- Interac e-Transfer Instructions -->
            <div class="interac-box">
                <h3>
                    <i class="fas fa-university"></i>
                    Interac e-Transfer Instructions
                </h3>

                <?php if (!empty($interacEmail)): ?>
                    <div class="interac-detail">
                        <span class="interac-detail-label">Send e-Transfer to:</span>
                        <span class="interac-detail-value email"><?= htmlspecialchars($interacEmail) ?></span>
                    </div>
                <?php endif; ?>

                <div class="interac-detail">
                    <span class="interac-detail-label">Amount:</span>
                    <span class="interac-detail-value">$<?= number_format($order['total'] ?? 0, 2) ?> CAD</span>
                </div>

                <div class="interac-detail">
                    <span class="interac-detail-label">Reference / Message:</span>
                    <span class="interac-detail-value"><?= htmlspecialchars($order['order_number'] ?? '') ?></span>
                </div>

                <div class="interac-instructions">
                    <?= nl2br(htmlspecialchars($interacInstructions)) ?>
                </div>

                <div class="interac-warning">
                    <i class="fas fa-info-circle"></i>
                    Your order will be processed once we confirm receipt of your e-Transfer.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Order Items -->
    <div class="card">
        <h3 class="card-title">Items Ordered</h3>

        <?php foreach ($items as $item): ?>
            <div class="order-item">
                <img src="<?= !empty($item['image_path']) ? url($item['image_path']) : asset('images/placeholder.svg') ?>"
                     alt="<?= htmlspecialchars($item['product_name'] ?? '') ?>">
                <div class="order-item-info">
                    <div class="order-item-name"><?= htmlspecialchars($item['product_name'] ?? 'Product') ?></div>
                    <div class="order-item-qty">Qty: <?= (int)$item['quantity'] ?></div>
                </div>
                <div class="order-item-price">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
            </div>
        <?php endforeach; ?>

        <div class="order-totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>$<?= number_format($order['subtotal'] ?? 0, 2) ?></span>
            </div>
            <?php if (($order['tax'] ?? 0) > 0): ?>
            <div class="total-row">
                <span>Tax</span>
                <span>$<?= number_format($order['tax'], 2) ?></span>
            </div>
            <?php endif; ?>
            <?php if (($order['delivery_fee'] ?? 0) > 0): ?>
            <div class="total-row">
                <span>Delivery</span>
                <span>$<?= number_format($order['delivery_fee'], 2) ?></span>
            </div>
            <?php endif; ?>
            <div class="total-row grand">
                <span>Total</span>
                <span>$<?= number_format($order['total'] ?? 0, 2) ?> CAD</span>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="actions">
        <a href="<?= url('account/orders') ?>" class="btn btn-primary">
            <i class="fas fa-list"></i> View My Orders
        </a>
        <a href="<?= url('home') ?>" class="btn btn-secondary">
            <i class="fas fa-shopping-bag"></i> Continue Shopping
        </a>
    </div>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>

</body>
</html>
