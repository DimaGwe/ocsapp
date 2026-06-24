<?php
/**
 * Payment Success Page - Stripe Payment Confirmation
 * File: app/Views/buyer/payment-success.php
 * Supports ES/EN/HT languages
 */

$currentLang = $_SESSION['language'] ?? 'fr';
$orderNumbers = $orderNumbers ?? [];
$amount = $amount ?? 0;

$translations = [
    'en' => [
        'title' => 'Payment Successful!',
        'thank_you' => 'Thank you for your payment',
        'confirmed' => 'Your payment has been confirmed and your order is being processed.',
        'order_numbers' => 'Order Number(s)',
        'amount_paid' => 'Amount Paid',
        'next_steps' => 'What happens next?',
        'step1' => 'Your order will be confirmed by the vendor',
        'step2' => 'You will receive order updates via email',
        'step3' => 'Track your delivery in your account',
        'view_orders' => 'View My Orders',
        'continue_shopping' => 'Continue Shopping'
    ],
    'es' => [
        'title' => '¡Pago Exitoso!',
        'thank_you' => 'Gracias por su pago',
        'confirmed' => 'Su pago ha sido confirmado y su pedido está siendo procesado.',
        'order_numbers' => 'Número(s) de Pedido',
        'amount_paid' => 'Monto Pagado',
        'next_steps' => '¿Qué sigue?',
        'step1' => 'Su pedido será confirmado por el vendedor',
        'step2' => 'Recibirá actualizaciones del pedido por correo',
        'step3' => 'Rastree su entrega en su cuenta',
        'view_orders' => 'Ver Mis Pedidos',
        'continue_shopping' => 'Continuar Comprando'
    ],
    'ht' => [
        'title' => 'Peman Siksè!',
        'thank_you' => 'Mèsi pou peman ou',
        'confirmed' => 'Peman ou konfime epi kòmand ou ap trete.',
        'order_numbers' => 'Nimewo Kòmand',
        'amount_paid' => 'Montan Peye',
        'next_steps' => 'Kisa ki pral pase kounye a?',
        'step1' => 'Vandè a ap konfime kòmand ou',
        'step2' => 'Ou pral resevwa enfòmasyon pa imel',
        'step3' => 'Swiv livrezon ou nan kont ou',
        'view_orders' => 'Gade Kòmand Mwen',
        'continue_shopping' => 'Kontinye Achte'
    ]
];

$t = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $t['title'] ?> - OCSAPP</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #00b207 0%, #00830a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .success-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-card {
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #00b207;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 50px;
            animation: checkBounce 0.8s ease-out;
        }

        @keyframes checkBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 40px;
        }

        .order-info {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
        }

        .info-value {
            font-weight: 700;
            color: #1a1a1a;
        }

        .amount-paid {
            font-size: 24px;
            color: #00b207;
        }

        .next-steps {
            background: #f0f9f1;
            border-left: 4px solid #00b207;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .next-steps h3 {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 15px;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px 0;
            font-size: 15px;
            color: #666;
        }

        .step-number {
            width: 30px;
            height: 30px;
            background: #00b207;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            flex-shrink: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 18px 30px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #00b207;
            color: white;
        }

        .btn-primary:hover {
            background: #009206;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,178,7,0.3);
        }

        .btn-secondary {
            background: white;
            color: #00b207;
            border: 2px solid #00b207;
        }

        .btn-secondary:hover {
            background: #f0f9f1;
        }

        @media (max-width: 768px) {
            .success-card {
                padding: 40px 25px;
            }

            h1 {
                font-size: 26px;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">✓</div>

            <h1><?= $t['title'] ?></h1>
            <p class="subtitle"><?= $t['thank_you'] ?></p>

            <div class="order-info">
                <div class="info-row">
                    <span class="info-label"><?= $t['order_numbers'] ?>:</span>
                    <span class="info-value">
                        <?php foreach ($orderNumbers as $orderNumber): ?>
                            <?= htmlspecialchars($orderNumber) ?><br>
                        <?php endforeach; ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label"><?= $t['amount_paid'] ?>:</span>
                    <span class="info-value amount-paid">$<?= number_format($amount, 2) ?></span>
                </div>
            </div>

            <p style="color: #666; margin-bottom: 30px;"><?= $t['confirmed'] ?></p>

            <div class="next-steps">
                <h3><?= $t['next_steps'] ?></h3>
                <div class="step-item">
                    <div class="step-number">1</div>
                    <span><?= $t['step1'] ?></span>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <span><?= $t['step2'] ?></span>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <span><?= $t['step3'] ?></span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="<?= url('account/orders') ?>" class="btn btn-primary">
                    <?= $t['view_orders'] ?>
                </a>
                <a href="<?= url('/') ?>" class="btn btn-secondary">
                    <?= $t['continue_shopping'] ?>
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
