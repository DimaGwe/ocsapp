<?php
/**
 * Privacy Policy Page - Bilingual (EN/FR)
 */

$currentLang = $_SESSION['language'] ?? 'fr';

$translations = [
    'en' => [
        'title' => 'Privacy Policy',
        'last_updated' => 'Last Updated',
        'intro' => 'Introduction',
        'intro_text' => 'OCSAPP ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform.',
        'info_collect' => 'Information We Collect',
        'info_collect_text' => 'We collect information that you provide directly to us, including your name, email address, phone number, delivery address, payment information, and any other information you choose to provide when creating an account or making a purchase.',
        'auto_collect' => 'Automatically Collected Information',
        'auto_collect_text' => 'When you access our platform, we automatically collect certain information about your device, including IP address, browser type, operating system, access times, and the pages you view. We also collect information about your interactions with our services.',
        'cookies' => 'Cookies and Tracking',
        'cookies_text' => 'We use cookies and similar tracking technologies to track activity on our platform and hold certain information. Cookies are files with a small amount of data. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent.',
        'use_info' => 'How We Use Your Information',
        'use_info_list' => [
            'Process transactions and send transaction notifications',
            'Provide customer service and respond to your requests',
            'Improve and personalize your experience on our platform',
            'Send marketing communications (with your consent)',
            'Monitor and analyze usage and trends to improve our services',
            'Detect, prevent, and address fraud and security issues'
        ],
        'third_party' => 'Third-Party Services',
        'third_party_text' => 'We may share your information with third-party service providers who perform services on our behalf, such as payment processing, data analysis, email delivery, hosting services, and customer service. We may also share information with delivery drivers to fulfill your orders.',
        'data_security' => 'Data Security',
        'data_security_text' => 'We implement appropriate technical and organizational security measures to protect your personal information. However, no method of transmission over the Internet or electronic storage is 100% secure. While we strive to protect your data, we cannot guarantee its absolute security.',
        'your_rights' => 'Your Rights (GDPR Compliance)',
        'your_rights_list' => [
            'Access: You can request a copy of your personal data',
            'Rectification: You can request correction of inaccurate data',
            'Erasure: You can request deletion of your data',
            'Portability: You can request transfer of your data',
            'Objection: You can object to processing of your data',
            'Withdrawal: You can withdraw consent at any time'
        ],
        'data_retention' => 'Data Retention',
        'data_retention_text' => 'We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required by law. When we no longer need your information, we will securely delete or anonymize it.',
        'children' => 'Children\'s Privacy',
        'children_text' => 'Our services are not intended for individuals under the age of 18. We do not knowingly collect personal information from children. If you believe we have collected information from a child, please contact us immediately.',
        'changes' => 'Changes to This Policy',
        'changes_text' => 'We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date. You are advised to review this Privacy Policy periodically.',
        'contact' => 'Contact Us',
        'contact_text' => 'If you have questions about this Privacy Policy or wish to exercise your rights, please contact us at'
    ],
    'fr' => [
        'title' => 'Politique de confidentialité',
        'last_updated' => 'Dernière mise à jour',
        'intro' => 'Introduction',
        'intro_text' => 'OCSAPP (« nous », « notre » ou « nos ») s\'engage à protéger votre vie privée. Cette politique de confidentialité explique comment nous recueillons, utilisons, divulguons et protégeons vos informations lorsque vous utilisez notre plateforme.',
        'info_collect' => 'Informations que nous recueillons',
        'info_collect_text' => 'Nous recueillons les informations que vous nous fournissez directement, notamment votre nom, votre adresse courriel, votre numéro de téléphone, votre adresse de livraison, vos informations de paiement et toute autre information que vous choisissez de fournir lors de la création d\'un compte ou d\'un achat.',
        'auto_collect' => 'Informations recueillies automatiquement',
        'auto_collect_text' => 'Lorsque vous accédez à notre plateforme, nous recueillons automatiquement certaines informations sur votre appareil, notamment l\'adresse IP, le type de navigateur, le système d\'exploitation, les heures d\'accès et les pages que vous consultez. Nous recueillons également des informations sur vos interactions avec nos services.',
        'cookies' => 'Témoins et suivi',
        'cookies_text' => 'Nous utilisons des témoins et des technologies de suivi similaires pour suivre l\'activité sur notre plateforme et conserver certaines informations. Les témoins sont des fichiers contenant une petite quantité de données. Vous pouvez configurer votre navigateur pour refuser tous les témoins ou pour indiquer lorsqu\'un témoin est envoyé.',
        'use_info' => 'Comment nous utilisons vos informations',
        'use_info_list' => [
            'Traiter les transactions et envoyer des notifications de transaction',
            'Fournir un service à la clientèle et répondre à vos demandes',
            'Améliorer et personnaliser votre expérience sur notre plateforme',
            'Envoyer des communications marketing (avec votre consentement)',
            'Surveiller et analyser l\'utilisation et les tendances pour améliorer nos services',
            'Détecter, prévenir et traiter les fraudes et les problèmes de sécurité'
        ],
        'third_party' => 'Services tiers',
        'third_party_text' => 'Nous pouvons partager vos informations avec des fournisseurs de services tiers qui fournissent des services en notre nom, tels que le traitement des paiements, l\'analyse de données, la livraison de courriels, les services d\'hébergement et le service à la clientèle. Nous pouvons également partager des informations avec les livreurs pour exécuter vos commandes.',
        'data_security' => 'Sécurité des données',
        'data_security_text' => 'Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos informations personnelles. Cependant, aucune méthode de transmission sur Internet ou de stockage électronique n\'est sécurisée à 100 %. Bien que nous nous efforcions de protéger vos données, nous ne pouvons pas garantir leur sécurité absolue.',
        'your_rights' => 'Vos droits (conformité au RGPD)',
        'your_rights_list' => [
            'Accès : Vous pouvez demander une copie de vos données personnelles',
            'Rectification : Vous pouvez demander la correction de données inexactes',
            'Effacement : Vous pouvez demander la suppression de vos données',
            'Portabilité : Vous pouvez demander le transfert de vos données',
            'Opposition : Vous pouvez vous opposer au traitement de vos données',
            'Retrait : Vous pouvez retirer votre consentement à tout moment'
        ],
        'data_retention' => 'Conservation des données',
        'data_retention_text' => 'Nous conservons vos informations personnelles aussi longtemps que nécessaire pour atteindre les objectifs décrits dans cette politique de confidentialité, à moins qu\'une période de conservation plus longue ne soit requise par la loi. Lorsque nous n\'avons plus besoin de vos informations, nous les supprimerons ou les anonymiserons de manière sécurisée.',
        'children' => 'Confidentialité des enfants',
        'children_text' => 'Nos services ne sont pas destinés aux personnes de moins de 18 ans. Nous ne recueillons pas sciemment d\'informations personnelles auprès d\'enfants. Si vous pensez que nous avons recueilli des informations auprès d\'un enfant, veuillez nous contacter immédiatement.',
        'changes' => 'Modifications de cette politique',
        'changes_text' => 'Nous pouvons mettre à jour cette politique de confidentialité de temps à autre. Nous vous informerons de tout changement en publiant la nouvelle politique de confidentialité sur cette page et en mettant à jour la date de « Dernière mise à jour ». Il vous est conseillé de consulter cette politique de confidentialité périodiquement.',
        'contact' => 'Nous joindre',
        'contact_text' => 'Si vous avez des questions concernant cette politique de confidentialité ou si vous souhaitez exercer vos droits, veuillez nous contacter à'
    ]
];

$page = $translations[$currentLang] ?? $translations['en'];
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($page['intro_text']) ?>">
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
        .content-section ul {
            margin: 15px 0;
            padding-left: 30px;
        }
        .content-section ul li {
            font-size: 16px;
            line-height: 1.8;
            color: #444;
            margin-bottom: 10px;
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
            <h2>1. <?= $page['intro'] ?></h2>
            <p><?= $page['intro_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>2. <?= $page['info_collect'] ?></h2>
            <p><?= $page['info_collect_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>3. <?= $page['auto_collect'] ?></h2>
            <p><?= $page['auto_collect_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>4. <?= $page['cookies'] ?></h2>
            <p><?= $page['cookies_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>5. <?= $page['use_info'] ?></h2>
            <ul>
                <?php foreach ($page['use_info_list'] as $item): ?>
                    <li><?= $item ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="content-section">
            <h2>6. <?= $page['third_party'] ?></h2>
            <p><?= $page['third_party_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>7. <?= $page['data_security'] ?></h2>
            <p><?= $page['data_security_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>8. <?= $page['your_rights'] ?></h2>
            <ul>
                <?php foreach ($page['your_rights_list'] as $right): ?>
                    <li><?= $right ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="content-section">
            <h2>9. <?= $page['data_retention'] ?></h2>
            <p><?= $page['data_retention_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>10. <?= $page['children'] ?></h2>
            <p><?= $page['children_text'] ?></p>
        </div>

        <div class="content-section">
            <h2>11. <?= $page['changes'] ?></h2>
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
