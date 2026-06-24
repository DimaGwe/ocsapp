<?php
/**
 * Terms of Service - OCSAPP
 * File: app/Views/legal/terms.php
 */
$currentLang = $_SESSION['language'] ?? 'fr';
$isFr = ($currentLang === 'fr');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $isFr ? 'Conditions d\'utilisation' : 'Terms of Service' ?> - OCSAPP</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= asset('images/logo.png') ?>">
    <link rel="apple-touch-icon" href="<?= asset('images/logo.png') ?>">
    <meta name="theme-color" content="#00b207">

    <!-- Modular CSS Architecture -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="<?= asset('css/global.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/header.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/components/footer.css') ?>">
    <style>
        .legal-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .legal-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #00b207;
        }
        .legal-header h1 {
            color: #1f2937;
            font-size: 32px;
            margin: 0 0 10px;
        }
        .legal-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }
        .legal-content h2 {
            color: #00b207;
            font-size: 24px;
            margin: 30px 0 15px;
            padding-top: 20px;
        }
        .legal-content h3 {
            color: #374151;
            font-size: 18px;
            margin: 20px 0 10px;
        }
        .legal-content p {
            color: #4b5563;
            line-height: 1.8;
            margin: 12px 0;
        }
        .legal-content ul, .legal-content ol {
            color: #4b5563;
            line-height: 1.8;
            margin: 12px 0;
            padding-left: 30px;
        }
        .legal-content li {
            margin: 8px 0;
        }
        .legal-content strong {
            color: #1f2937;
        }
        .highlight-box {
            background: #f0fdf4;
            border-left: 4px solid #00b207;
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #00b207;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include __DIR__ . '/../components/header.php'; ?>

    <div class="page">
        <div class="legal-container">
            <a href="<?= url('/') ?>" class="back-link">
                ← <?= $isFr ? 'Retour à l\'accueil' : 'Back to Home' ?>
            </a>

            <?php if ($isFr): ?>
            <!-- ==================== VERSION FRANÇAISE ==================== -->
            <div class="legal-header">
                <h1>Conditions d'utilisation</h1>
                <p>Dernière mise à jour : 25 février 2026</p>
            </div>

            <div class="legal-content">
                <div class="highlight-box">
                    <p><strong>Bienvenue sur OCSAPP !</strong></p>
                    <p>Les présentes conditions d'utilisation régissent votre utilisation de notre plateforme. En accédant à OCSAPP ou en l'utilisant, vous acceptez d'être lié par ces conditions. Veuillez les lire attentivement.</p>
                </div>

                <h2>1. Acceptation des conditions</h2>
                <p>En créant un compte, en accédant à OCSAPP (« la Plateforme ») ou en l'utilisant, vous acceptez de vous conformer aux présentes conditions d'utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.</p>

                <h2>2. Définitions</h2>
                <ul>
                    <li><strong>« Plateforme »</strong> désigne OCSAPP, y compris le site Web, les applications mobiles et tous les services associés.</li>
                    <li><strong>« Utilisateur »</strong> désigne toute personne accédant à la Plateforme ou l'utilisant.</li>
                    <li><strong>« Acheteur »</strong> désigne les utilisateurs qui achètent des produits via la Plateforme.</li>
                    <li><strong>« Vendeur »</strong> désigne les entreprises canadiennes ou québécoises enregistrées qui listent et vendent des produits sur la Plateforme.</li>
                    <li><strong>« Produits »</strong> désigne les biens mis en vente sur la Plateforme.</li>
                </ul>

                <h2>3. Inscription au compte</h2>

                <h3>3.1 Comptes acheteurs</h3>
                <ul>
                    <li>Vous devez avoir au moins 18 ans pour créer un compte acheteur</li>
                    <li>Vous devez fournir des informations exactes, actuelles et complètes</li>
                    <li>Vous êtes responsable de la confidentialité de vos identifiants de connexion</li>
                    <li>Vous êtes responsable de toutes les activités effectuées sous votre compte</li>
                    <li>Les comptes acheteurs sont activés immédiatement après l'inscription</li>
                </ul>

                <h3>3.2 Comptes vendeurs</h3>
                <ul>
                    <li>Les vendeurs doivent être des entreprises canadiennes ou québécoises enregistrées</li>
                    <li>Toutes les demandes de vendeurs sont soumises à un examen et à une approbation</li>
                    <li>Les vendeurs doivent fournir des documents d'enregistrement commercial valides</li>
                    <li>Les vendeurs doivent respecter toutes les lois et réglementations applicables</li>
                    <li>OCSAPP se réserve le droit de rejeter toute demande de vendeur</li>
                    <li>Les comptes vendeurs ne sont activés qu'après approbation administrative</li>
                </ul>

                <h2>4. Conduite des utilisateurs</h2>
                <p>Vous vous engagez à ne pas :</p>
                <ul>
                    <li>Violer des lois, réglementations ou droits de tiers</li>
                    <li>Publier du contenu faux, inexact, trompeur ou frauduleux</li>
                    <li>Vous livrer à du harcèlement, des abus ou des comportements menaçants</li>
                    <li>Tenter d'accéder sans autorisation à la Plateforme ou à d'autres comptes</li>
                    <li>Distribuer des virus, des logiciels malveillants ou d'autres codes nuisibles</li>
                    <li>Utiliser la Plateforme à des fins illégales ou non autorisées</li>
                    <li>Contourner des fonctionnalités de sécurité ou des restrictions</li>
                </ul>

                <h2>5. Obligations des vendeurs</h2>

                <h3>5.1 Fiches produits</h3>
                <ul>
                    <li>Toutes les informations sur les produits doivent être exactes et véridiques</li>
                    <li>Les images des produits doivent représenter fidèlement les articles mis en vente</li>
                    <li>Les prix doivent être clairement indiqués et inclure toutes les taxes applicables</li>
                    <li>Les vendeurs doivent maintenir des niveaux de stock exacts</li>
                </ul>

                <h3>5.2 Exécution des commandes</h3>
                <ul>
                    <li>Les vendeurs doivent exécuter les commandes dans les meilleurs délais</li>
                    <li>Les vendeurs doivent fournir des informations de suivi lorsqu'elles sont disponibles</li>
                    <li>Les vendeurs sont responsables d'un emballage sécurisé des produits</li>
                    <li>Les vendeurs doivent traiter les demandes des clients de manière professionnelle</li>
                </ul>

                <h3>5.3 Articles interdits</h3>
                <p>Les vendeurs ne peuvent pas mettre en vente :</p>
                <ul>
                    <li>Des articles illégaux ou réglementés</li>
                    <li>Des produits contrefaits ou non autorisés</li>
                    <li>Des matières dangereuses sans certification appropriée</li>
                    <li>Des articles portant atteinte aux droits de propriété intellectuelle</li>
                    <li>Tout produit interdit par la loi canadienne</li>
                </ul>

                <h2>6. Obligations des acheteurs</h2>
                <ul>
                    <li>Fournir des informations d'expédition et de paiement exactes</li>
                    <li>Effectuer les paiements en temps voulu pour les achats</li>
                    <li>Accepter la livraison des produits commandés</li>
                    <li>Évaluer les produits de manière équitable et honnête</li>
                    <li>Contacter les vendeurs ou le service d'assistance pour tout problème avant d'ouvrir un litige</li>
                </ul>

                <h2>7. Paiements et frais</h2>

                <h3>7.1 Paiements des acheteurs</h3>
                <ul>
                    <li>Tous les prix sont en dollars canadiens (CAD)</li>
                    <li>Le paiement est traité lors du paiement (checkout)</li>
                    <li>Nous acceptons les principales cartes de crédit et autres méthodes de paiement approuvées</li>
                    <li>Les acheteurs sont responsables de toutes les taxes applicables (TPS/TVQ)</li>
                </ul>

                <h3>7.2 Frais des vendeurs</h3>
                <ul>
                    <li>OCSAPP peut facturer des commissions sur les ventes</li>
                    <li>La structure des frais sera communiquée aux vendeurs lors de l'approbation du compte</li>
                    <li>Les vendeurs recevront leur paiement après la finalisation de la commande</li>
                    <li>OCSAPP se réserve le droit de modifier les structures tarifaires avec préavis</li>
                </ul>

                <h2>8. Retours et remboursements</h2>
                <ul>
                    <li>Les politiques de retour sont établies par les vendeurs individuels</li>
                    <li>Les acheteurs doivent consulter la politique de retour du vendeur avant l'achat</li>
                    <li>OCSAPP peut servir de médiateur dans les litiges entre acheteurs et vendeurs</li>
                    <li>Les remboursements seront traités conformément à la politique déclarée du vendeur</li>
                    <li>OCSAPP se réserve le droit d'émettre des remboursements en cas de fraude ou de violations de politique</li>
                </ul>
                <p>Consultez notre <a href="<?= url('returns') ?>" style="color:#00b207;font-weight:600;">Politique de retour et de remboursement</a> pour plus de détails.</p>

                <h2>9. Propriété intellectuelle</h2>
                <ul>
                    <li>Tout le contenu de la Plateforme, y compris les logos, les designs et les logiciels, appartient à OCSAPP</li>
                    <li>Les utilisateurs conservent la propriété du contenu qu'ils téléchargent</li>
                    <li>En téléchargeant du contenu, les utilisateurs accordent à OCSAPP une licence pour l'utiliser, l'afficher et le distribuer</li>
                    <li>Les utilisateurs ne doivent pas porter atteinte aux droits de propriété intellectuelle d'autrui</li>
                </ul>

                <h2>10. Vie privée et protection des données</h2>
                <p>Votre vie privée est importante pour nous. Veuillez consulter notre <a href="<?= url('privacy') ?>" style="color: #00b207; text-decoration: none; font-weight: 600;">Politique de confidentialité</a> pour comprendre comment nous collectons, utilisons et protégeons vos renseignements personnels.</p>

                <h2>11. Résolution des litiges</h2>
                <ul>
                    <li>Les utilisateurs s'engagent à tenter de résoudre les litiges à l'amiable</li>
                    <li>OCSAPP peut offrir des services de médiation</li>
                    <li>Les litiges seront régis par les lois du Canada et de la province de Québec</li>
                    <li>Les utilisateurs acceptent l'arbitrage contraignant pour les litiges non résolus</li>
                </ul>

                <h2>12. Limitation de responsabilité</h2>
                <p>OCSAPP est une plateforme qui met en relation acheteurs et vendeurs. Nous ne sommes pas responsables de :</p>
                <ul>
                    <li>La qualité, la sécurité ou la légalité des produits listés</li>
                    <li>L'exactitude des fiches ou du contenu des utilisateurs</li>
                    <li>La capacité des vendeurs à exécuter les commandes</li>
                    <li>La capacité des acheteurs à finaliser les transactions</li>
                    <li>Les litiges entre acheteurs et vendeurs</li>
                    <li>Tout dommage indirect, accessoire ou consécutif</li>
                </ul>

                <h2>13. Résiliation du compte</h2>
                <p>OCSAPP se réserve le droit de :</p>
                <ul>
                    <li>Suspendre ou résilier des comptes en cas de violations des présentes conditions</li>
                    <li>Supprimer des fiches qui violent nos politiques</li>
                    <li>Refuser le service à quiconque pour quelque raison que ce soit</li>
                    <li>Modifier ou interrompre la Plateforme à tout moment</li>
                </ul>

                <h2>14. Modifications des conditions</h2>
                <p>Nous nous réservons le droit de modifier les présentes conditions d'utilisation à tout moment. Les modifications seront effectives immédiatement après leur publication. L'utilisation continue de la Plateforme constitue une acceptation des conditions modifiées.</p>

                <h2>15. Coordonnées</h2>
                <p>Pour toute question concernant les présentes conditions d'utilisation :</p>
                <ul>
                    <li><strong>Courriel :</strong> <a href="mailto:legal@ocsapp.ca" style="color: #00b207;">legal@ocsapp.ca</a></li>
                    <li><strong>Soutien :</strong> <a href="mailto:support@ocsapp.ca" style="color: #00b207;">support@ocsapp.ca</a></li>
                    <li><strong>Téléphone :</strong> +1 (514) 746-3789</li>
                </ul>

                <h2>16. Droit applicable</h2>
                <p>Les présentes conditions d'utilisation sont régies et interprétées conformément aux lois du Canada et de la province de Québec, sans égard aux dispositions relatives aux conflits de lois.</p>

                <div class="highlight-box" style="margin-top: 40px;">
                    <p><strong>En utilisant OCSAPP, vous reconnaissez avoir lu, compris et accepté d'être lié par les présentes conditions d'utilisation.</strong></p>
                </div>
            </div>

            <?php else: ?>
            <!-- ==================== ENGLISH VERSION ==================== -->
            <div class="legal-header">
                <h1>Terms of Service</h1>
                <p>Last Updated: February 25, 2026</p>
            </div>

            <div class="legal-content">
                <div class="highlight-box">
                    <p><strong>Welcome to OCSAPP!</strong></p>
                    <p>These Terms of Service govern your use of our platform. By accessing or using OCSAPP, you agree to be bound by these terms. Please read them carefully.</p>
                </div>

                <h2>1. Acceptance of Terms</h2>
                <p>By creating an account, accessing, or using OCSAPP ("the Platform"), you agree to comply with and be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.</p>

                <h2>2. Definitions</h2>
                <ul>
                    <li><strong>"Platform"</strong> refers to OCSAPP, including the website, mobile applications, and all associated services.</li>
                    <li><strong>"User"</strong> refers to any person accessing or using the Platform.</li>
                    <li><strong>"Buyer"</strong> refers to users who purchase products through the Platform.</li>
                    <li><strong>"Seller"</strong> refers to registered Canadian or Quebec businesses that list and sell products on the Platform.</li>
                    <li><strong>"Products"</strong> refers to goods listed for sale on the Platform.</li>
                </ul>

                <h2>3. Account Registration</h2>

                <h3>3.1 Buyer Accounts</h3>
                <ul>
                    <li>You must be at least 18 years old to create a buyer account</li>
                    <li>You must provide accurate, current, and complete information</li>
                    <li>You are responsible for maintaining the confidentiality of your account credentials</li>
                    <li>You are responsible for all activities that occur under your account</li>
                    <li>Buyer accounts are activated immediately upon registration</li>
                </ul>

                <h3>3.2 Seller Accounts</h3>
                <ul>
                    <li>Sellers must be registered Canadian or Quebec businesses</li>
                    <li>All seller applications are subject to review and approval</li>
                    <li>Sellers must provide valid business registration documents</li>
                    <li>Sellers must comply with all applicable laws and regulations</li>
                    <li>OCSAPP reserves the right to reject any seller application</li>
                    <li>Seller accounts are activated only after administrative approval</li>
                </ul>

                <h2>4. User Conduct</h2>
                <p>You agree not to:</p>
                <ul>
                    <li>Violate any laws, regulations, or third-party rights</li>
                    <li>Post false, inaccurate, misleading, or fraudulent content</li>
                    <li>Engage in any form of harassment, abuse, or threatening behavior</li>
                    <li>Attempt to gain unauthorized access to the Platform or other user accounts</li>
                    <li>Distribute viruses, malware, or other harmful code</li>
                    <li>Use the Platform for any illegal or unauthorized purpose</li>
                    <li>Circumvent any security features or restrictions</li>
                </ul>

                <h2>5. Seller Obligations</h2>

                <h3>5.1 Product Listings</h3>
                <ul>
                    <li>All product information must be accurate and truthful</li>
                    <li>Product images must accurately represent the items being sold</li>
                    <li>Prices must be clearly stated and include all applicable taxes</li>
                    <li>Sellers must maintain accurate inventory levels</li>
                </ul>

                <h3>5.2 Order Fulfillment</h3>
                <ul>
                    <li>Sellers must fulfill orders in a timely manner</li>
                    <li>Sellers must provide tracking information when available</li>
                    <li>Sellers are responsible for packaging products securely</li>
                    <li>Sellers must handle customer inquiries professionally</li>
                </ul>

                <h3>5.3 Prohibited Items</h3>
                <p>Sellers may not list:</p>
                <ul>
                    <li>Illegal or regulated items</li>
                    <li>Counterfeit or unauthorized products</li>
                    <li>Hazardous materials without proper certification</li>
                    <li>Items that violate intellectual property rights</li>
                    <li>Any products prohibited by Canadian law</li>
                </ul>

                <h2>6. Buyer Obligations</h2>
                <ul>
                    <li>Provide accurate shipping and payment information</li>
                    <li>Make timely payments for purchases</li>
                    <li>Accept delivery of ordered products</li>
                    <li>Review products fairly and honestly</li>
                    <li>Contact sellers or support for any issues before disputes</li>
                </ul>

                <h2>7. Payments and Fees</h2>

                <h3>7.1 Buyer Payments</h3>
                <ul>
                    <li>All prices are in Canadian Dollars (CAD)</li>
                    <li>Payment is processed at checkout</li>
                    <li>We accept major credit cards and other approved payment methods</li>
                    <li>Buyers are responsible for all applicable taxes</li>
                </ul>

                <h3>7.2 Seller Fees</h3>
                <ul>
                    <li>OCSAPP may charge commission fees on sales</li>
                    <li>Fee structure will be provided to sellers upon account approval</li>
                    <li>Sellers will receive payment after order completion</li>
                    <li>OCSAPP reserves the right to modify fee structures with notice</li>
                </ul>

                <h2>8. Returns and Refunds</h2>
                <ul>
                    <li>Return policies are set by individual sellers</li>
                    <li>Buyers should review seller return policies before purchase</li>
                    <li>OCSAPP may mediate disputes between buyers and sellers</li>
                    <li>Refunds will be processed according to the seller's stated policy</li>
                    <li>OCSAPP reserves the right to issue refunds in cases of fraud or policy violations</li>
                </ul>

                <h2>9. Intellectual Property</h2>
                <ul>
                    <li>All Platform content, including logos, designs, and software, is owned by OCSAPP</li>
                    <li>Users retain ownership of content they upload</li>
                    <li>By uploading content, users grant OCSAPP a license to use, display, and distribute it</li>
                    <li>Users must not infringe on others' intellectual property rights</li>
                </ul>

                <h2>10. Privacy and Data Protection</h2>
                <p>Your privacy is important to us. Please review our <a href="<?= url('privacy') ?>" style="color: #00b207; text-decoration: none; font-weight: 600;">Privacy Policy</a> to understand how we collect, use, and protect your personal information.</p>

                <h2>11. Dispute Resolution</h2>
                <ul>
                    <li>Users agree to attempt to resolve disputes amicably</li>
                    <li>OCSAPP may provide mediation services</li>
                    <li>Disputes will be governed by the laws of Canada and the Province of Quebec</li>
                    <li>Users agree to binding arbitration for unresolved disputes</li>
                </ul>

                <h2>12. Limitation of Liability</h2>
                <p>OCSAPP is a platform connecting buyers and sellers. We are not responsible for:</p>
                <ul>
                    <li>The quality, safety, or legality of products listed</li>
                    <li>The accuracy of listings or user content</li>
                    <li>The ability of sellers to fulfill orders</li>
                    <li>The ability of buyers to complete transactions</li>
                    <li>Any disputes between buyers and sellers</li>
                    <li>Any indirect, incidental, or consequential damages</li>
                </ul>

                <h2>13. Account Termination</h2>
                <p>OCSAPP reserves the right to:</p>
                <ul>
                    <li>Suspend or terminate accounts for violations of these terms</li>
                    <li>Remove listings that violate our policies</li>
                    <li>Refuse service to anyone for any reason</li>
                    <li>Modify or discontinue the Platform at any time</li>
                </ul>

                <h2>14. Changes to Terms</h2>
                <p>We reserve the right to modify these Terms of Service at any time. Changes will be effective immediately upon posting. Continued use of the Platform constitutes acceptance of modified terms.</p>

                <h2>15. Contact Information</h2>
                <p>For questions about these Terms of Service, please contact us:</p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:legal@ocsapp.ca" style="color: #00b207;">legal@ocsapp.ca</a></li>
                    <li><strong>Support:</strong> <a href="mailto:support@ocsapp.ca" style="color: #00b207;">support@ocsapp.ca</a></li>
                    <li><strong>Phone:</strong> +1 (514) 746-3789</li>
                </ul>

                <h2>16. Governing Law</h2>
                <p>These Terms of Service shall be governed by and construed in accordance with the laws of Canada and the Province of Quebec, without regard to conflict of law provisions.</p>

                <div class="highlight-box" style="margin-top: 40px;">
                    <p><strong>By using OCSAPP, you acknowledge that you have read, understood, and agree to be bound by these Terms of Service.</strong></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include __DIR__ . '/../components/footer.php'; ?>
</body>
</html>
