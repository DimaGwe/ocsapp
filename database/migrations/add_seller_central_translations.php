<?php
/**
 * Migration: Add French translations for Seller Central page
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $translations = [
        // Page title & hero
        ['sc_title', 'seller', 'Seller Central', 'Portail vendeur'],
        ['sc_hero_subtitle', 'seller', 'Open your online store on OCSAPP and reach customers across Canada', 'Ouvrez votre boutique en ligne sur OCSAPP et rejoignez des clients partout au Canada'],

        // CTA buttons
        ['sc_dashboard', 'seller', 'Seller Dashboard', 'Tableau de bord vendeur'],
        ['sc_become_seller', 'seller', 'Become a Seller', 'Devenir vendeur'],
        ['sc_login', 'seller', 'Seller Login', 'Connexion vendeur'],

        // Get to Know Us section
        ['sc_know_us', 'seller', 'Get to Know Us', 'Apprenez &agrave; nous conna&icirc;tre'],
        ['sc_sell_on', 'seller', 'Sell on OCSAPP', 'Vendez sur OCSAPP'],

        // Paragraphs
        ['sc_para1', 'seller',
            'The OCSapp platform promotes a wide range of offerings that prioritize ease and time saving solutions for consumers and businesses. Sell on OCSAPP; create an account to open an online store on the platform, and launch or increase your online presence instantly. The OCSapp platform provides a range of tools and features including a shop dashboard to view sales activity, analytics and insights, and reports to support you.',
            'La plateforme OCSapp offre une vaste gamme de solutions qui privil&eacute;gient la facilit&eacute; et le gain de temps pour les consommateurs et les entreprises. Vendez sur OCSAPP; cr&eacute;ez un compte pour ouvrir une boutique en ligne sur la plateforme et lancez ou augmentez votre pr&eacute;sence en ligne instantan&eacute;ment. La plateforme OCSapp fournit une gamme d&rsquo;outils et de fonctionnalit&eacute;s, y compris un tableau de bord boutique pour consulter l&rsquo;activit&eacute; des ventes, des analyses et des rapports pour vous accompagner.'],

        ['sc_para2', 'seller',
            'Enjoy our services and leverage OCSapp technology to take your business from physical to virtual offering your clients and OCSapp users the benefit of visiting and purchasing from your online store on the OCSapp platform. The platform integrates key aspects of your business, including product or service information updates, stock picture uploads, pricing, inventory management, order processing, and delivery tracking through its user-friendly administrative portal which you can access and manage on your mobile phone. If you don\'t have the time, OCSapp can manage your shop for you.',
            'Profitez de nos services et tirez parti de la technologie OCSapp pour faire passer votre entreprise du physique au virtuel, offrant &agrave; vos clients et aux utilisateurs d&rsquo;OCSapp l&rsquo;avantage de visiter et d&rsquo;acheter dans votre boutique en ligne sur la plateforme OCSapp. La plateforme int&egrave;gre les aspects cl&eacute;s de votre entreprise, y compris les mises &agrave; jour d&rsquo;information sur les produits ou services, le t&eacute;l&eacute;versement d&rsquo;images, la tarification, la gestion des stocks, le traitement des commandes et le suivi des livraisons via son portail administratif convivial accessible depuis votre t&eacute;l&eacute;phone. Si vous n&rsquo;avez pas le temps, OCSapp peut g&eacute;rer votre boutique pour vous.'],

        ['sc_para3', 'seller',
            'The OCSapp platform provides your clients and OCSapp users immediate access to your company profile, products, and services from anywhere with an internet connection, and ensures deliveries through its zero-carbon footprint distribution service.',
            'La plateforme OCSapp offre &agrave; vos clients et aux utilisateurs d&rsquo;OCSapp un acc&egrave;s imm&eacute;diat &agrave; votre profil d&rsquo;entreprise, vos produits et services depuis n&rsquo;importe o&ugrave; avec une connexion Internet, et assure les livraisons gr&acirc;ce &agrave; son service de distribution &agrave; empreinte carbone z&eacute;ro.'],

        ['sc_para4', 'seller',
            'OCSapp is revolutionizing convenience by making buying and selling online very convenient and fun.',
            'OCSapp r&eacute;volutionne la commodit&eacute; en rendant l&rsquo;achat et la vente en ligne tr&egrave;s pratiques et agr&eacute;ables.'],

        ['sc_para5', 'seller',
            'Join OCSapp as a seller and offer the best virtual customer service experience to your customers.',
            'Rejoignez OCSapp en tant que vendeur et offrez la meilleure exp&eacute;rience de service client virtuel &agrave; vos clients.'],

        // Motto & CTA
        ['sc_motto', 'seller', 'Convenience is Priceless!', 'La commodit&eacute; n&rsquo;a pas de prix!'],
        ['sc_cta_text', 'seller', 'Find a more convenient way; register and find out more', 'Trouvez une fa&ccedil;on plus pratique; inscrivez-vous et d&eacute;couvrez-en plus'],

        // Feature cards
        ['sc_feat_store_title', 'seller', 'Your Own Online Store', 'Votre propre boutique en ligne'],
        ['sc_feat_store_desc', 'seller',
            'Create and customize your branded storefront on OCSAPP. Showcase your products and build your online presence instantly.',
            'Cr&eacute;ez et personnalisez votre vitrine sur OCSAPP. Pr&eacute;sentez vos produits et b&acirc;tissez votre pr&eacute;sence en ligne instantan&eacute;ment.'],

        ['sc_feat_analytics_title', 'seller', 'Shop Dashboard & Analytics', 'Tableau de bord et analytique'],
        ['sc_feat_analytics_desc', 'seller',
            'View sales activity, track performance metrics, and access detailed reports. Make data-driven decisions to grow your business.',
            'Consultez l&rsquo;activit&eacute; des ventes, suivez les indicateurs de performance et acc&eacute;dez &agrave; des rapports d&eacute;taill&eacute;s. Prenez des d&eacute;cisions fond&eacute;es sur les donn&eacute;es pour faire cro&icirc;tre votre entreprise.'],

        ['sc_feat_mobile_title', 'seller', 'Mobile Management', 'Gestion mobile'],
        ['sc_feat_mobile_desc', 'seller',
            'Manage your entire store from your mobile phone. Update products, process orders, and track inventory on the go.',
            'G&eacute;rez toute votre boutique depuis votre t&eacute;l&eacute;phone. Mettez &agrave; jour les produits, traitez les commandes et suivez les stocks en d&eacute;placement.'],

        ['sc_feat_inventory_title', 'seller', 'Inventory Management', 'Gestion des stocks'],
        ['sc_feat_inventory_desc', 'seller',
            'Upload product images, update pricing, manage stock levels, and keep your catalog fresh with our easy-to-use tools.',
            'T&eacute;l&eacute;versez des images de produits, mettez &agrave; jour les prix, g&eacute;rez les niveaux de stock et gardez votre catalogue &agrave; jour avec nos outils faciles &agrave; utiliser.'],

        ['sc_feat_delivery_title', 'seller', 'Zero-Carbon Delivery', 'Livraison z&eacute;ro carbone'],
        ['sc_feat_delivery_desc', 'seller',
            'OCSAPP handles deliveries through our zero-carbon footprint distribution service. Your customers get eco-friendly shipping.',
            'OCSAPP g&egrave;re les livraisons via notre service de distribution &agrave; empreinte carbone z&eacute;ro. Vos clients b&eacute;n&eacute;ficient d&rsquo;une exp&eacute;dition &eacute;cologique.'],

        ['sc_feat_managed_title', 'seller', 'Optional Store Management', 'Gestion de boutique optionnelle'],
        ['sc_feat_managed_desc', 'seller',
            'Don\'t have time? Let OCSAPP manage your shop for you. We\'ll handle the day-to-day operations while you focus on your business.',
            'Vous n&rsquo;avez pas le temps? Laissez OCSAPP g&eacute;rer votre boutique pour vous. Nous nous occupons des op&eacute;rations quotidiennes pendant que vous vous concentrez sur votre entreprise.'],

        // Stats section
        ['sc_stats_title', 'seller', 'Join Thousands of Successful Sellers', 'Rejoignez des milliers de vendeurs prosp&egrave;res'],
        ['sc_stat_customers', 'seller', 'Active Customers', 'Clients actifs'],
        ['sc_stat_stores', 'seller', 'Seller Stores', 'Boutiques vendeur'],
        ['sc_stat_sales', 'seller', 'Monthly Sales', 'Ventes mensuelles'],
        ['sc_stat_canadian', 'seller', 'Canadian Owned', 'Propri&eacute;t&eacute; canadienne'],
    ];

    $stmt = $db->prepare("INSERT INTO translations (`key`, category, en, fr) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)");

    $count = 0;
    foreach ($translations as $t) {
        $stmt->execute($t);
        $count++;
    }

    echo "Inserted/updated {$count} seller central translations.\n";
    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
