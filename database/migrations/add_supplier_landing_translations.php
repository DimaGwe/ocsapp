<?php
/**
 * Migration: Add French translations for Supplier Portal Landing Page
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $translations = [
        // Hero section
        ['sl_title', 'supplier', 'Supplier Portal', 'Portail fournisseur'],
        ['sl_hero_title', 'supplier', 'Partner with OCSAPP', 'Devenez partenaire d&rsquo;OCSAPP'],
        ['sl_hero_subtitle', 'supplier', 'Join our growing network of trusted suppliers and expand your business reach across Canada', 'Rejoignez notre r&eacute;seau croissant de fournisseurs de confiance et &eacute;tendez votre port&eacute;e commerciale &agrave; travers le Canada'],
        ['sl_login', 'supplier', 'Supplier Login', 'Connexion fournisseur'],
        ['sl_become_supplier', 'supplier', 'Become a Supplier', 'Devenir fournisseur'],

        // Get to Know Us section
        ['sl_know_us', 'supplier', 'Get to Know Us', 'Apprenez &agrave; nous conna&icirc;tre'],
        ['sl_supply_to', 'supplier', 'Supply to OCSAPP', 'Fournir &agrave; OCSAPP'],

        ['sl_para1', 'supplier',
            'The OCSAPP platform connects trusted suppliers with a growing marketplace serving customers and businesses across Canada. Become a supplier to OCSAPP and join our wholesale network, providing quality products that support our marketplace ecosystem. The OCSAPP supplier portal provides a range of tools and features including a comprehensive dashboard to manage your product catalog, track purchase orders, monitor inventory, and analyze your sales performance.',
            'La plateforme OCSAPP met en relation des fournisseurs de confiance avec un march&eacute; en croissance desservant des clients et des entreprises &agrave; travers le Canada. Devenez fournisseur d&rsquo;OCSAPP et rejoignez notre r&eacute;seau de gros, en fournissant des produits de qualit&eacute; qui soutiennent notre &eacute;cosyst&egrave;me. Le portail fournisseur OCSAPP offre une gamme d&rsquo;outils et de fonctionnalit&eacute;s, y compris un tableau de bord complet pour g&eacute;rer votre catalogue de produits, suivre les bons de commande, surveiller les stocks et analyser vos performances de vente.'],

        ['sl_para2', 'supplier',
            'Leverage OCSAPP technology to expand your wholesale business reach. Our platform integrates key aspects of your supplier operations, including product catalog management, bulk pricing setup, minimum order quantities, inventory synchronization, purchase order processing, and fulfillment tracking through an intuitive supplier portal accessible from any device. Focus on what you do best—supplying quality products—while we handle connecting you with demand from across our marketplace.',
            'Tirez parti de la technologie OCSAPP pour &eacute;tendre la port&eacute;e de votre entreprise de gros. Notre plateforme int&egrave;gre les aspects cl&eacute;s de vos op&eacute;rations de fournisseur, y compris la gestion du catalogue, la configuration des prix de gros, les quantit&eacute;s minimales de commande, la synchronisation des stocks, le traitement des bons de commande et le suivi de l&rsquo;ex&eacute;cution via un portail fournisseur intuitif accessible depuis n&rsquo;importe quel appareil. Concentrez-vous sur ce que vous faites le mieux &mdash; fournir des produits de qualit&eacute; &mdash; pendant que nous vous connectons &agrave; la demande de notre march&eacute;.'],

        ['sl_para3', 'supplier',
            'The OCSAPP platform provides OCSAPP buyers and marketplace shops immediate access to your wholesale catalog. Your products become available for purchase orders, helping us maintain fresh inventory and diverse product offerings for our customers.',
            'La plateforme OCSAPP offre aux acheteurs OCSAPP et aux boutiques du march&eacute; un acc&egrave;s imm&eacute;diat &agrave; votre catalogue de gros. Vos produits deviennent disponibles pour les bons de commande, nous aidant &agrave; maintenir un inventaire frais et des offres diversifi&eacute;es pour nos clients.'],

        ['sl_para4', 'supplier',
            'OCSAPP is revolutionizing wholesale distribution by making B2B commerce convenient, transparent, and efficient.',
            'OCSAPP r&eacute;volutionne la distribution en gros en rendant le commerce B2B pratique, transparent et efficace.'],

        ['sl_para5', 'supplier',
            'Join OCSAPP as a supplier and become part of Canada\'s growing online marketplace ecosystem.',
            'Rejoignez OCSAPP en tant que fournisseur et faites partie de l&rsquo;&eacute;cosyst&egrave;me croissant du march&eacute; en ligne au Canada.'],

        ['sl_motto', 'supplier', 'Partnership Drives Growth!', 'Le partenariat stimule la croissance!'],
        ['sl_cta_text', 'supplier', 'Find a more convenient way to wholesale; apply today and get started', 'Trouvez une fa&ccedil;on plus pratique de vendre en gros; postulez aujourd&rsquo;hui et commencez'],

        // Features section
        ['sl_features_title', 'supplier', 'Why Partner With Us?', 'Pourquoi devenir notre partenaire?'],
        ['sl_features_subtitle', 'supplier', 'Access powerful tools and features designed to grow your business', 'Acc&eacute;dez &agrave; des outils puissants con&ccedil;us pour faire cro&icirc;tre votre entreprise'],

        ['sl_feat_reach_title', 'supplier', 'Expand Your Reach', '&Eacute;tendez votre port&eacute;e'],
        ['sl_feat_reach_desc', 'supplier',
            'Connect with OCSAPP and gain access to a growing customer base across Canada. Your products will be featured in our marketplace catalog.',
            'Connectez-vous avec OCSAPP et acc&eacute;dez &agrave; une base de clients croissante partout au Canada. Vos produits seront pr&eacute;sent&eacute;s dans notre catalogue.'],

        ['sl_feat_products_title', 'supplier', 'Easy Product Management', 'Gestion facile des produits'],
        ['sl_feat_products_desc', 'supplier',
            'Manage your product catalog with our intuitive supplier portal. Add products, update pricing, and track inventory all in one place.',
            'G&eacute;rez votre catalogue de produits avec notre portail fournisseur intuitif. Ajoutez des produits, mettez &agrave; jour les prix et suivez les stocks en un seul endroit.'],

        ['sl_feat_orders_title', 'supplier', 'Streamlined Orders', 'Commandes simplifi&eacute;es'],
        ['sl_feat_orders_desc', 'supplier',
            'Receive purchase orders directly through our system. Track order status, manage fulfillment, and communicate with our procurement team.',
            'Recevez des bons de commande directement via notre syst&egrave;me. Suivez le statut des commandes, g&eacute;rez l&rsquo;ex&eacute;cution et communiquez avec notre &eacute;quipe d&rsquo;approvisionnement.'],

        ['sl_feat_updates_title', 'supplier', 'Real-Time Updates', 'Mises &agrave; jour en temps r&eacute;el'],
        ['sl_feat_updates_desc', 'supplier',
            'Stay informed with instant notifications for new orders, stock updates, and important communications. Never miss a business opportunity.',
            'Restez inform&eacute; avec des notifications instantan&eacute;es pour les nouvelles commandes, les mises &agrave; jour de stock et les communications importantes. Ne manquez jamais une opportunit&eacute; d&rsquo;affaires.'],

        ['sl_feat_trust_title', 'supplier', 'Trusted Partnership', 'Partenariat de confiance'],
        ['sl_feat_trust_desc', 'supplier',
            'Build a long-term relationship with OCSAPP. We value our suppliers and work together to ensure mutual success.',
            'B&acirc;tissez une relation &agrave; long terme avec OCSAPP. Nous valorisons nos fournisseurs et travaillons ensemble pour assurer un succ&egrave;s mutuel.'],

        ['sl_feat_support_title', 'supplier', 'Dedicated Support', 'Soutien d&eacute;di&eacute;'],
        ['sl_feat_support_desc', 'supplier',
            'Our team is here to help you succeed. Get assistance with onboarding, technical issues, and business development opportunities.',
            'Notre &eacute;quipe est l&agrave; pour vous aider &agrave; r&eacute;ussir. Obtenez de l&rsquo;aide pour l&rsquo;int&eacute;gration, les probl&egrave;mes techniques et les opportunit&eacute;s de d&eacute;veloppement commercial.'],

        // Benefits section
        ['sl_benefits_title', 'supplier', 'Supplier Benefits', 'Avantages fournisseur'],
        ['sl_benefits_subtitle', 'supplier', 'Everything you need to grow your business with OCSAPP', 'Tout ce dont vous avez besoin pour faire cro&icirc;tre votre entreprise avec OCSAPP'],

        ['sl_ben_pricing_title', 'supplier', 'Competitive Pricing', 'Prix comp&eacute;titifs'],
        ['sl_ben_pricing_desc', 'supplier', 'Set your own prices and minimum order quantities', 'D&eacute;finissez vos propres prix et quantit&eacute;s minimales de commande'],

        ['sl_ben_inventory_title', 'supplier', 'Inventory Integration', 'Int&eacute;gration des stocks'],
        ['sl_ben_inventory_desc', 'supplier', 'Link products to marketplace inventory for automatic stock updates', 'Liez vos produits &agrave; l&rsquo;inventaire du march&eacute; pour des mises &agrave; jour automatiques'],

        ['sl_ben_analytics_title', 'supplier', 'Sales Analytics', 'Analytique des ventes'],
        ['sl_ben_analytics_desc', 'supplier', 'Track your performance and identify growth opportunities', 'Suivez vos performances et identifiez les opportunit&eacute;s de croissance'],

        ['sl_ben_security_title', 'supplier', 'Secure Platform', 'Plateforme s&eacute;curis&eacute;e'],
        ['sl_ben_security_desc', 'supplier', 'Your data is protected with enterprise-grade security', 'Vos donn&eacute;es sont prot&eacute;g&eacute;es par une s&eacute;curit&eacute; de niveau entreprise'],

        ['sl_ben_mobile_title', 'supplier', 'Mobile Friendly', 'Adapt&eacute; au mobile'],
        ['sl_ben_mobile_desc', 'supplier', 'Manage your business on the go with our responsive platform', 'G&eacute;rez votre entreprise en d&eacute;placement avec notre plateforme responsive'],

        ['sl_ben_bilingual_title', 'supplier', 'Bilingual Support', 'Soutien bilingue'],
        ['sl_ben_bilingual_desc', 'supplier', 'Full support for English and French languages', 'Soutien complet en anglais et en fran&ccedil;ais'],

        // How It Works section
        ['sl_how_title', 'supplier', 'How It Works', 'Comment &ccedil;a fonctionne'],
        ['sl_how_subtitle', 'supplier', 'Get started in just a few simple steps', 'Commencez en quelques &eacute;tapes simples'],

        ['sl_step1_title', 'supplier', 'Apply Online', 'Postulez en ligne'],
        ['sl_step1_desc', 'supplier',
            'Fill out our supplier application form with your business details and Quebec legal identity information.',
            'Remplissez notre formulaire de demande fournisseur avec les d&eacute;tails de votre entreprise et vos informations l&eacute;gales du Qu&eacute;bec.'],

        ['sl_step2_title', 'supplier', 'Get Invited', 'Recevez une invitation'],
        ['sl_step2_desc', 'supplier',
            'Once approved, you\'ll receive an email invitation with a secure link to set up your supplier account.',
            'Une fois approuv&eacute;, vous recevrez une invitation par courriel avec un lien s&eacute;curis&eacute; pour configurer votre compte fournisseur.'],

        ['sl_step3_title', 'supplier', 'Add Products', 'Ajoutez des produits'],
        ['sl_step3_desc', 'supplier',
            'Log in to your supplier portal and start adding your products to our catalog with descriptions, pricing, and availability.',
            'Connectez-vous &agrave; votre portail fournisseur et commencez &agrave; ajouter vos produits &agrave; notre catalogue avec descriptions, prix et disponibilit&eacute;.'],

        ['sl_step4_title', 'supplier', 'Receive Orders', 'Recevez des commandes'],
        ['sl_step4_desc', 'supplier',
            'Start receiving purchase orders from OCSAPP. Track orders, manage fulfillment, and grow your business.',
            'Commencez &agrave; recevoir des bons de commande d&rsquo;OCSAPP. Suivez les commandes, g&eacute;rez l&rsquo;ex&eacute;cution et faites cro&icirc;tre votre entreprise.'],

        // CTA section
        ['sl_cta_title', 'supplier', 'Ready to Get Started?', 'Pr&ecirc;t &agrave; commencer?'],
        ['sl_cta_subtitle', 'supplier', 'Join OCSAPP today and take your business to the next level', 'Rejoignez OCSAPP aujourd&rsquo;hui et propulsez votre entreprise au niveau sup&eacute;rieur'],
        ['sl_existing_login', 'supplier', 'Existing Supplier? Login', 'Fournisseur existant? Connexion'],
    ];

    $stmt = $db->prepare("INSERT INTO translations (`key`, category, en, fr) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)");

    $count = 0;
    foreach ($translations as $t) {
        $stmt->execute($t);
        $count++;
    }

    echo "Inserted/updated {$count} supplier landing translations.\n";
    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
