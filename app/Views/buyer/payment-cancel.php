<?php
/**
 * Payment Cancel Page - User Cancelled Stripe Payment
 * File: app/Views/buyer/payment-cancel.php
 * Supports ES/EN/HT languages
 */

$currentLang = $_SESSION['language'] ?? 'fr';

$translations = [
    'en' => [
        'title' => 'Payment Cancelled',
        'message' => 'You have cancelled the payment process',
        'description' => 'Your order has not been placed and no charges were made to your card. Your cart items are still saved.',
        'try_again' => 'Try Again',
        'back_to_cart' => 'Back to Cart',
        'need_help' => 'Need help?',
        'contact_support' => 'Contact Support'
    ],
    'es' => [
        'title' => 'Pago Cancelado',
        'message' => 'Has cancelado el proceso de pago',
        'description' => 'Tu pedido no ha sido realizado y no se hicieron cargos a tu tarjeta. Los artículos en tu carrito siguen guardados.',
        'try_again' => 'Intentar de Nuevo',
        'back_to_cart' => 'Volver al Carrito',
        'need_help' => '¿Necesitas ayuda?',
        'contact_support' => 'Contactar Soporte'
    ],
    'ht' => [
        'title' => 'Peman Anile',
        'message' => 'Ou anile pwosesis peman an',
        'description' => 'Kòmand ou pa fèt epi yo pa chaje kat ou. Atik nan panye ou toujou la.',
        'try_again' => 'Eseye Ankò',
        'back_to_cart' => 'Retounen nan Panye',
        'need_help' => 'Ou bezwen èd?',
        'contact_support' => 'Kontakte Sipò'
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
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .cancel-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .cancel-card {
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

        .cancel-icon {
            width: 100px;
            height: 100px;
            background: #ffc107;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            font-size: 50px;
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
            margin-bottom: 25px;
        }

        .description {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            font-size: 15px;
            line-height: 1.6;
            color: #666;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
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
            background: #ffc107;
            color: #1a1a1a;
        }

        .btn-primary:hover {
            background: #ffb300;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,193,7,0.3);
        }

        .btn-secondary {
            background: white;
            color: #ffc107;
            border: 2px solid #ffc107;
        }

        .btn-secondary:hover {
            background: #fff9e6;
        }

        .help-section {
            padding-top: 30px;
            border-top: 1px solid #e5e5e5;
        }

        .help-text {
            font-size: 14px;
            color: #999;
            margin-bottom: 15px;
        }

        .contact-link {
            color: #ffc107;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .contact-link:hover {
            color: #ff9800;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .cancel-card {
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

    <div class="cancel-container">
        <div class="cancel-card">
            <div class="cancel-icon">✕</div>

            <h1><?= $t['title'] ?></h1>
            <p class="subtitle"><?= $t['message'] ?></p>

            <div class="description">
                <?= $t['description'] ?>
            </div>

            <div class="action-buttons">
                <a href="<?= url('checkout') ?>" class="btn btn-primary">
                    <?= $t['try_again'] ?>
                </a>
                <a href="<?= url('cart') ?>" class="btn btn-secondary">
                    <?= $t['back_to_cart'] ?>
                </a>
            </div>

            <div class="help-section">
                <p class="help-text"><?= $t['need_help'] ?></p>
                <a href="<?= url('contact') ?>" class="contact-link">
                    <?= $t['contact_support'] ?>
                </a>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
