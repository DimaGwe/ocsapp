<?php
/**
 * Terms of Service Page - Bilingual (EN/FR)
 */

$currentLang = $_SESSION['language'] ?? 'fr';

$translations = [
    'en' => [
        'title' => 'Terms of Service',
        'last_updated' => 'Last Updated',
        'accept' => 'Acceptance of Terms',
        'accept_text' => 'By accessing and using the OCSAPP platform, you accept and agree to be bound by the terms and provisions of this agreement.',
        'services' => 'Services',
        'services_text' => 'OCSAPP provides an online platform connecting buyers, sellers, and delivery drivers. We facilitate transactions but are not directly involved in the actual transaction between buyers and sellers.',
        'user_accounts' => 'User Accounts',
        'user_accounts_text' => 'You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.',
        'seller_terms' => 'Seller Terms',
        'seller_terms_text' => 'Sellers must provide accurate product information, maintain adequate stock levels, and fulfill orders in a timely manner. Sellers are responsible for product quality and customer satisfaction.',
        'buyer_terms' => 'Buyer Terms',
        'buyer_terms_text' => 'Buyers agree to provide accurate delivery information and payment details. Buyers must be present to receive deliveries at the scheduled time.',
        'payments' => 'Payments and Fees',
        'payments_text' => 'All payments are processed securely through our payment partners. Platform fees and commission rates are disclosed to sellers at registration.',
        'prohibited' => 'Prohibited Activities',
        'prohibited_text' => 'Users may not engage in fraudulent activities, sell prohibited items, or misuse the platform. Violations may result in account suspension or termination.',
        'liability' => 'Limitation of Liability',
        'liability_text' => 'OCSAPP is not liable for damages arising from use of the platform, including but not limited to product quality, delivery issues, or transaction disputes.',
        'changes' => 'Changes to Terms',
        'changes_text' => 'We reserve the right to modify these terms at any time. Continued use of the platform after changes constitutes acceptance of the modified terms.',
        'contact' => 'Contact Us',
        'contact_text' => 'If you have questions about these terms, please contact us at'
    ],
    'fr' => [
        'title' => 'Conditions d\'utilisation',
        'last_updated' => 'Dernière mise à jour',
        'accept' => 'Acceptation des conditions',
        'accept_text' => 'En accédant et en utilisant la plateforme OCSAPP, vous acceptez et vous engagez à respecter les conditions et les dispositions de cette entente.',
        'services' => 'Services',
        'services_text' => 'OCSAPP offre une plateforme en ligne qui met en relation acheteurs, vendeurs et livreurs. Nous facilitons les transactions, mais ne sommes pas directement impliqués dans la transaction réelle entre acheteurs et vendeurs.',
        'user_accounts' => 'Comptes utilisateurs',
        'user_accounts_text' => 'Vous êtes responsable de maintenir la confidentialité de votre compte et de votre mot de passe. Vous acceptez d\'assumer la responsabilité de toutes les activités qui se produisent sous votre compte.',
        'seller_terms' => 'Conditions pour les vendeurs',
        'seller_terms_text' => 'Les vendeurs doivent fournir des informations exactes sur les produits, maintenir des niveaux de stock adéquats et exécuter les commandes en temps opportun. Les vendeurs sont responsables de la qualité des produits et de la satisfaction de la clientèle.',
        'buyer_terms' => 'Conditions pour les acheteurs',
        'buyer_terms_text' => 'Les acheteurs acceptent de fournir des informations de livraison et de paiement exactes. Les acheteurs doivent être présents pour recevoir les livraisons à l\'heure prévue.',
        'payments' => 'Paiements et frais',
        'payments_text' => 'Tous les paiements sont traités de manière sécurisée par l\'intermédiaire de nos partenaires de paiement. Les frais de plateforme et les taux de commission sont divulgués aux vendeurs lors de l\'inscription.',
        'prohibited' => 'Activités interdites',
        'prohibited_text' => 'Les utilisateurs ne peuvent pas se livrer à des activités frauduleuses, vendre des articles interdits ou faire un usage abusif de la plateforme. Les violations peuvent entraîner la suspension ou la résiliation du compte.',
        'liability' => 'Limitation de responsabilité',
        'liability_text' => 'OCSAPP n\'est pas responsable des dommages découlant de l\'utilisation de la plateforme, y compris, mais sans s\'y limiter, la qualité des produits, les problèmes de livraison ou les litiges relatifs aux transactions.',
        'changes' => 'Modifications des conditions',
        'changes_text' => 'Nous nous réservons le droit de modifier ces conditions à tout moment. L\'utilisation continue de la plateforme après les modifications constitue l\'acceptation des conditions modifiées.',
        'contact' => 'Nous joindre',
        'contact_text' => 'Si vous avez des questions concernant ces conditions, veuillez nous contacter à'
    ]
];

$page = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page['title'] ?> - OCSAPP</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        .page-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .page-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e5e5e5;
        }
        .page-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        .page-header .date {
            color: #666;
            font-size: 14px;
        }
        .content-section {
            margin-bottom: 40px;
        }
        .content-section h2 {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }
        .content-section p {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
            margin-bottom: 15px;
        }
        .highlight-box {
            background: #f0f9f1;
            border-left: 4px solid #00b207;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="page-container">
        <div class="page-header">
            <h1><?= $page['title'] ?></h1>
            <p class="date"><?= $page['last_updated'] ?>: November 13, 2025</p>
        </div>

        <div class="content-section">
            <h2>1. <?= $page['accept'] ?></h2>
            <p><?= $page['accept_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>2. <?= $page['services'] ?></h2>
            <p><?= $page['services_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>3. <?= $page['user_accounts'] ?></h2>
            <p><?= $page['user_accounts_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>4. <?= $page['seller_terms'] ?></h2>
            <p><?= $page['seller_terms_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>5. <?= $page['buyer_terms'] ?></h2>
            <p><?= $page['buyer_terms_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>6. <?= $page['payments'] ?></h2>
            <p><?= $page['payments_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>7. <?= $page['prohibited'] ?></h2>
            <p><?= $page['prohibited_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>8. <?= $page['liability'] ?></h2>
            <p><?= $page['liability_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>9. <?= $page['changes'] ?></h2>
            <p><?= $page['changes_text'] ?></p>
        </div>

        <div class="highlight-box">
            <h2><?= $page['contact'] ?></h2>
            <p><?= $page['contact_text'] ?>: <a href="mailto:support@ocsapp.ca">support@ocsapp.ca</a></p>
        </div>
    </div>

    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
