<?php
/**
 * Migration: Add 136 missing translation keys
 * Covers: auth pages, header, footer, buyer shop/search/payment pages,
 *         supplier nav, supplier apply, supplier forgot/reset password
 */

require_once __DIR__ . '/../../bootstrap/init.php';
require_once __DIR__ . '/../../config/database.php';

$pdo = Database::getConnection();

$keys = [
    // ─── AUTH / REGISTER ───────────────────────────────────────────────────────
    ['key' => 'tagline',              'cat' => 'auth',   'en' => 'Your Neighbourhood Online Market',        'fr' => 'Votre marché en ligne de quartier'],
    ['key' => 'join_ocs',             'cat' => 'auth',   'en' => 'Join OCS Marketplace',                    'fr' => 'Rejoindre OCS Marketplace'],
    ['key' => 'create_account',       'cat' => 'auth',   'en' => 'Create Account',                          'fr' => 'Créer un compte'],
    ['key' => 'register_as',          'cat' => 'auth',   'en' => 'Register as',                             'fr' => 'S\'inscrire en tant que'],
    ['key' => 'buyer',                'cat' => 'auth',   'en' => 'Buyer',                                   'fr' => 'Acheteur'],
    ['key' => 'seller',               'cat' => 'auth',   'en' => 'Seller',                                  'fr' => 'Vendeur'],
    ['key' => 'affiliate',            'cat' => 'auth',   'en' => 'Affiliate',                               'fr' => 'Affilié'],
    ['key' => 'first_name',           'cat' => 'auth',   'en' => 'First Name',                              'fr' => 'Prénom'],
    ['key' => 'first_name_placeholder','cat'=> 'auth',   'en' => 'Enter first name',                        'fr' => 'Entrez votre prénom'],
    ['key' => 'last_name',            'cat' => 'auth',   'en' => 'Last Name',                               'fr' => 'Nom de famille'],
    ['key' => 'last_name_placeholder','cat' => 'auth',   'en' => 'Enter last name',                         'fr' => 'Entrez votre nom'],
    ['key' => 'email',                'cat' => 'auth',   'en' => 'Email',                                   'fr' => 'Courriel'],
    ['key' => 'password',             'cat' => 'auth',   'en' => 'Password',                                'fr' => 'Mot de passe'],
    ['key' => 'password_placeholder', 'cat' => 'auth',   'en' => 'Create a password',                       'fr' => 'Créez un mot de passe'],
    ['key' => 'phone',                'cat' => 'auth',   'en' => 'Phone',                                   'fr' => 'Téléphone'],
    ['key' => 'phone_placeholder',    'cat' => 'auth',   'en' => 'Enter phone number',                      'fr' => 'Entrez votre numéro'],
    ['key' => 'confirm_password',     'cat' => 'auth',   'en' => 'Confirm Password',                        'fr' => 'Confirmer le mot de passe'],
    ['key' => 'min_8_chars',          'cat' => 'auth',   'en' => 'Minimum 10 characters',                   'fr' => 'Minimum 10 caractères'],
    ['key' => 'have_account',         'cat' => 'auth',   'en' => 'Already have an account?',                'fr' => 'Vous avez déjà un compte?'],
    ['key' => 'no_account',           'cat' => 'auth',   'en' => "Don't have an account?",                  'fr' => 'Vous n\'avez pas de compte?'],
    ['key' => 'sign_in_to',           'cat' => 'auth',   'en' => 'Sign in to your account',                 'fr' => 'Connectez-vous à votre compte'],
    ['key' => 'sign_up',              'cat' => 'auth',   'en' => 'Sign Up',                                 'fr' => 'S\'inscrire'],
    ['key' => 'create_btn',           'cat' => 'auth',   'en' => 'Create Account',                          'fr' => 'Créer un compte'],
    ['key' => 'create_and_checkout',  'cat' => 'auth',   'en' => 'Create Account & Checkout',               'fr' => 'Créer un compte et commander'],
    ['key' => 'sign_in_and_checkout', 'cat' => 'auth',   'en' => 'Sign In & Checkout',                      'fr' => 'Se connecter et commander'],
    ['key' => 'welcome_back',         'cat' => 'auth',   'en' => 'Welcome back!',                           'fr' => 'Bon retour!'],
    ['key' => 'remember_me',          'cat' => 'auth',   'en' => 'Remember me',                             'fr' => 'Se souvenir de moi'],
    ['key' => 'forgot_password',      'cat' => 'auth',   'en' => 'Forgot password?',                        'fr' => 'Mot de passe oublié?'],
    ['key' => 'terms',                'cat' => 'auth',   'en' => 'Terms of Service',                        'fr' => 'Conditions d\'utilisation'],
    ['key' => 'privacy',              'cat' => 'auth',   'en' => 'Privacy Policy',                          'fr' => 'Politique de confidentialité'],
    ['key' => 'agree_to',             'cat' => 'auth',   'en' => 'I agree to the',                          'fr' => 'J\'accepte les'],
    ['key' => 'and',                  'cat' => 'auth',   'en' => 'and',                                     'fr' => 'et'],
    ['key' => 'items_order',          'cat' => 'auth',   'en' => 'items in your order',                     'fr' => 'articles dans votre commande'],
    ['key' => 'checkout_notice_title','cat' => 'auth',   'en' => 'You have items in your cart',             'fr' => 'Vous avez des articles dans votre panier'],
    ['key' => 'checkout_notice_desc', 'cat' => 'auth',   'en' => 'Sign in or create an account to complete your purchase', 'fr' => 'Connectez-vous ou créez un compte pour finaliser votre achat'],

    // ─── HEADER ────────────────────────────────────────────────────────────────
    ['key' => 'admin',                'cat' => 'header', 'en' => 'Admin',                                   'fr' => 'Admin'],
    ['key' => 'seller_dashboard',     'cat' => 'header', 'en' => 'Seller Dashboard',                        'fr' => 'Tableau de bord vendeur'],
    ['key' => 'location_placeholder', 'cat' => 'header', 'en' => 'Enter street, city, or postal code',      'fr' => 'Rue, ville ou code postal'],
    ['key' => 'search_location',      'cat' => 'header', 'en' => 'Search for your address',                 'fr' => 'Recherchez votre adresse'],
    ['key' => 'recent_locations',     'cat' => 'header', 'en' => 'Recent locations',                        'fr' => 'Emplacements récents'],
    ['key' => 'use_current_location', 'cat' => 'header', 'en' => 'Use my current location',                 'fr' => 'Utiliser ma position actuelle'],

    // ─── FOOTER ────────────────────────────────────────────────────────────────
    ['key' => 'cookies',              'cat' => 'footer', 'en' => 'Cookie Policy',                           'fr' => 'Politique de cookies'],
    ['key' => 'buyer_central',        'cat' => 'footer', 'en' => 'Buyer Central',                           'fr' => 'Espace acheteur'],
    ['key' => 'seller_central',       'cat' => 'footer', 'en' => 'Seller Central',                          'fr' => 'Espace vendeur'],
    ['key' => 'supplier_central',     'cat' => 'footer', 'en' => 'Supplier Central',                        'fr' => 'Espace fournisseur'],

    // ─── BUYER SHOPS ───────────────────────────────────────────────────────────
    ['key' => 'shops_page_title',     'cat' => 'buyer',  'en' => 'All Shops',                               'fr' => 'Toutes les boutiques'],
    ['key' => 'shops_page_subtitle',  'cat' => 'buyer',  'en' => 'Discover local stores near you',          'fr' => 'Découvrez les boutiques locales près de chez vous'],
    ['key' => 'shops_found',          'cat' => 'buyer',  'en' => 'shops found',                             'fr' => 'boutiques trouvées'],
    ['key' => 'all_shops',            'cat' => 'buyer',  'en' => 'All Shops',                               'fr' => 'Toutes les boutiques'],
    ['key' => 'all_locations',        'cat' => 'buyer',  'en' => 'All Locations',                           'fr' => 'Tous les emplacements'],
    ['key' => 'change_location',      'cat' => 'buyer',  'en' => 'Change Location',                         'fr' => 'Changer d\'emplacement'],
    ['key' => 'no_shops_found',       'cat' => 'buyer',  'en' => 'No shops found',                          'fr' => 'Aucune boutique trouvée'],
    ['key' => 'no_shops_desc',        'cat' => 'buyer',  'en' => 'Try adjusting your location or search',   'fr' => 'Essayez d\'ajuster votre emplacement ou recherche'],
    ['key' => 'showing_all_shops',    'cat' => 'buyer',  'en' => 'Showing all shops',                       'fr' => 'Affichage de toutes les boutiques'],
    ['key' => 'under_radius',         'cat' => 'buyer',  'en' => 'Within your area',                        'fr' => 'Dans votre zone'],

    // ─── BUYER SHOP SINGLE ─────────────────────────────────────────────────────
    ['key' => 'about',                'cat' => 'buyer',  'en' => 'About',                                   'fr' => 'À propos'],
    ['key' => 'address',              'cat' => 'buyer',  'en' => 'Address',                                 'fr' => 'Adresse'],
    ['key' => 'closed',               'cat' => 'buyer',  'en' => 'Closed',                                  'fr' => 'Fermé'],
    ['key' => 'delivery_time',        'cat' => 'buyer',  'en' => 'Delivery Time',                           'fr' => 'Délai de livraison'],
    ['key' => 'minutes',              'cat' => 'buyer',  'en' => 'minutes',                                 'fr' => 'minutes'],
    ['key' => 'no_products',          'cat' => 'buyer',  'en' => 'No products available',                   'fr' => 'Aucun produit disponible'],
    ['key' => 'nutritional_info',     'cat' => 'buyer',  'en' => 'Nutritional Information',                 'fr' => 'Informations nutritionnelles'],
    ['key' => 'rating',               'cat' => 'buyer',  'en' => 'Rating',                                  'fr' => 'Évaluation'],
    ['key' => 'shop_contact',         'cat' => 'buyer',  'en' => 'Contact Shop',                            'fr' => 'Contacter la boutique'],
    ['key' => 'shop_feedback',        'cat' => 'buyer',  'en' => 'Leave Feedback',                          'fr' => 'Laisser un commentaire'],
    ['key' => 'shop_hours',           'cat' => 'buyer',  'en' => 'Store Hours',                             'fr' => 'Heures d\'ouverture'],
    ['key' => 'shop_no_products_desc','cat' => 'buyer',  'en' => 'This store has no products listed yet.',  'fr' => 'Cette boutique n\'a pas encore de produits listés.'],
    ['key' => 'shop_policy',          'cat' => 'buyer',  'en' => 'Store Policy',                            'fr' => 'Politique du magasin'],
    ['key' => 'shop_report_spam',     'cat' => 'buyer',  'en' => 'Report Spam',                             'fr' => 'Signaler un spam'],
    ['key' => 'view_information',     'cat' => 'buyer',  'en' => 'View Information',                        'fr' => 'Voir les informations'],
    ['key' => 'sort_popularity',      'cat' => 'buyer',  'en' => 'Most Popular',                            'fr' => 'Plus populaire'],

    // ─── BUYER PRODUCT DETAIL ──────────────────────────────────────────────────
    ['key' => 'added_to_wishlist',    'cat' => 'buyer',  'en' => 'Added to wishlist',                       'fr' => 'Ajouté à la liste de souhaits'],
    ['key' => 'removed_from_wishlist','cat' => 'buyer',  'en' => 'Removed from wishlist',                   'fr' => 'Retiré de la liste de souhaits'],
    ['key' => 'link_copied',          'cat' => 'buyer',  'en' => 'Link copied!',                            'fr' => 'Lien copié!'],
    ['key' => 'error_adding_to_cart', 'cat' => 'buyer',  'en' => 'Error adding to cart',                    'fr' => 'Erreur lors de l\'ajout au panier'],
    ['key' => 'check_out_product',    'cat' => 'buyer',  'en' => 'Check out this product',                  'fr' => 'Découvrez ce produit'],

    // ─── BUYER SEARCH ──────────────────────────────────────────────────────────
    ['key' => 'search_results',       'cat' => 'buyer',  'en' => 'Search Results',                          'fr' => 'Résultats de recherche'],
    ['key' => 'search_results_for',   'cat' => 'buyer',  'en' => 'Results for',                             'fr' => 'Résultats pour'],
    ['key' => 'products_found',       'cat' => 'buyer',  'en' => 'products found',                          'fr' => 'produits trouvés'],
    ['key' => 'no_products_found',    'cat' => 'buyer',  'en' => 'No products found',                       'fr' => 'Aucun produit trouvé'],
    ['key' => 'try_different_search', 'cat' => 'buyer',  'en' => 'Try a different search term',             'fr' => 'Essayez un terme de recherche différent'],
    ['key' => 'browse_categories',    'cat' => 'buyer',  'en' => 'Browse Categories',                       'fr' => 'Parcourir les catégories'],

    // ─── BUYER CATEGORY ────────────────────────────────────────────────────────
    ['key' => 'no_products_in_category', 'cat' => 'buyer', 'en' => 'No products in this category',         'fr' => 'Aucun produit dans cette catégorie'],
    ['key' => 'no_products_desc',     'cat' => 'buyer',  'en' => 'Check back later for new products',       'fr' => 'Revenez plus tard pour de nouveaux produits'],
    ['key' => 'back_to_categories',   'cat' => 'buyer',  'en' => 'Back to Categories',                      'fr' => 'Retour aux catégories'],
    ['key' => 'sort_featured',        'cat' => 'buyer',  'en' => 'Featured',                                'fr' => 'En vedette'],

    // ─── BUYER BEST SELLERS ────────────────────────────────────────────────────
    ['key' => 'best_sellers_page_title','cat'=> 'buyer', 'en' => 'Best Sellers',                            'fr' => 'Meilleures ventes'],
    ['key' => 'best_sellers_subtitle','cat' => 'buyer',  'en' => 'Our most popular products',               'fr' => 'Nos produits les plus populaires'],

    // ─── BUYER HOME ────────────────────────────────────────────────────────────
    ['key' => 'grocery_store',        'cat' => 'buyer',  'en' => 'Grocery Store',                           'fr' => 'Épicerie'],
    ['key' => 'store',                'cat' => 'buyer',  'en' => 'Store',                                   'fr' => 'Magasin'],
    ['key' => 'visit_shop',           'cat' => 'buyer',  'en' => 'Visit Shop',                              'fr' => 'Visiter la boutique'],
    ['key' => 'newsletter_consent',   'cat' => 'buyer',  'en' => 'I agree to receive newsletters and promotions', 'fr' => 'J\'accepte de recevoir des infolettres et promotions'],
    ['key' => 'unsubscribe',          'cat' => 'buyer',  'en' => 'Unsubscribe',                             'fr' => 'Se désabonner'],

    // ─── BUYER PAYMENT ─────────────────────────────────────────────────────────
    ['key' => 'thank_you',            'cat' => 'buyer',  'en' => 'Thank You!',                              'fr' => 'Merci!'],
    ['key' => 'title',                'cat' => 'buyer',  'en' => 'Order Confirmed',                         'fr' => 'Commande confirmée'],
    ['key' => 'confirmed',            'cat' => 'buyer',  'en' => 'Your order has been confirmed',           'fr' => 'Votre commande a été confirmée'],
    ['key' => 'amount_paid',          'cat' => 'buyer',  'en' => 'Amount Paid',                             'fr' => 'Montant payé'],
    ['key' => 'order_numbers',        'cat' => 'buyer',  'en' => 'Order Numbers',                           'fr' => 'Numéros de commande'],
    ['key' => 'next_steps',           'cat' => 'buyer',  'en' => 'What happens next?',                      'fr' => 'Prochaines étapes'],
    ['key' => 'step1',                'cat' => 'buyer',  'en' => 'We\'re preparing your order',             'fr' => 'Nous préparons votre commande'],
    ['key' => 'step2',                'cat' => 'buyer',  'en' => 'Your order will be dispatched',           'fr' => 'Votre commande sera expédiée'],
    ['key' => 'step3',                'cat' => 'buyer',  'en' => 'Track your delivery',                     'fr' => 'Suivez votre livraison'],
    ['key' => 'continue_shopping',    'cat' => 'buyer',  'en' => 'Continue Shopping',                       'fr' => 'Continuer vos achats'],
    ['key' => 'view_orders',          'cat' => 'buyer',  'en' => 'View Orders',                             'fr' => 'Voir les commandes'],
    ['key' => 'back_to_cart',         'cat' => 'buyer',  'en' => 'Back to Cart',                            'fr' => 'Retour au panier'],
    ['key' => 'contact_support',      'cat' => 'buyer',  'en' => 'Contact Support',                         'fr' => 'Contacter le support'],
    ['key' => 'message',              'cat' => 'buyer',  'en' => 'Message',                                 'fr' => 'Message'],
    ['key' => 'try_again',            'cat' => 'buyer',  'en' => 'Try Again',                               'fr' => 'Réessayer'],

    // ─── SUPPLIER NAV ──────────────────────────────────────────────────────────
    ['key' => 'sup_documents',        'cat' => 'supplier','en'=> 'Documents',                               'fr' => 'Documents'],
    ['key' => 'sup_emails',           'cat' => 'supplier','en'=> 'Emails',                                  'fr' => 'Courriels'],
    ['key' => 'sup_invoices',         'cat' => 'supplier','en'=> 'Invoices',                                'fr' => 'Factures'],
    ['key' => 'sup_messages',         'cat' => 'supplier','en'=> 'Messages',                                'fr' => 'Messages'],
    ['key' => 'sup_verification_pending_title','cat'=>'supplier','en'=>'Account Verification Pending',      'fr' => 'Vérification du compte en attente'],
    ['key' => 'sup_verification_pending_msg',  'cat'=>'supplier','en'=>'Your account is being reviewed. You\'ll receive an email once approved.','fr'=>'Votre compte est en cours d\'examen. Vous recevrez un courriel une fois approuvé.'],
    ['key' => 'sup_verification_docs_reminder','cat'=>'supplier','en'=>'Please upload your verification documents to complete registration.','fr'=>'Veuillez téléverser vos documents de vérification pour compléter l\'inscription.'],
    ['key' => 'sup_verification_contact',      'cat'=>'supplier','en'=>'Contact us if you have questions',  'fr' => 'Contactez-nous si vous avez des questions'],

    // ─── SUPPLIER APPLY — PASSWORD SECTION ─────────────────────────────────────
    ['key' => 'sup_apply_password_title','cat'=>'supplier','en'=> 'Set Your Password',                      'fr' => 'Définissez votre mot de passe'],
    ['key' => 'sup_apply_password_desc', 'cat'=>'supplier','en'=> 'Create a secure password for your account.','fr'=>'Créez un mot de passe sécurisé pour votre compte.'],
    ['key' => 'sup_apply_password',      'cat'=>'supplier','en'=> 'Password',                               'fr' => 'Mot de passe'],
    ['key' => 'sup_apply_confirm_password','cat'=>'supplier','en'=>'Confirm Password',                      'fr' => 'Confirmer le mot de passe'],

    // ─── SUPPLIER FORGOT PASSWORD ──────────────────────────────────────────────
    ['key' => 'sl_forgot_heading',          'cat'=>'supplier','en'=>'Forgot Password',                      'fr' => 'Mot de passe oublié'],
    ['key' => 'sl_forgot_subtitle',         'cat'=>'supplier','en'=>'Enter your email to receive a reset link','fr'=>'Entrez votre courriel pour recevoir un lien de réinitialisation'],
    ['key' => 'sl_forgot_email',            'cat'=>'supplier','en'=>'Email Address',                        'fr' => 'Adresse courriel'],
    ['key' => 'sl_forgot_email_placeholder','cat'=>'supplier','en'=>'supplier@company.com',                 'fr' => 'fournisseur@entreprise.com'],
    ['key' => 'sl_forgot_submit',           'cat'=>'supplier','en'=>'Send Reset Link',                      'fr' => 'Envoyer le lien de réinitialisation'],
    ['key' => 'sl_forgot_back',             'cat'=>'supplier','en'=>'Back to Login',                        'fr' => 'Retour à la connexion'],
    ['key' => 'sl_login_forgot',            'cat'=>'supplier','en'=>'Forgot Password?',                     'fr' => 'Mot de passe oublié?'],

    // ─── SUPPLIER RESET PASSWORD ───────────────────────────────────────────────
    ['key' => 'sl_reset_heading',              'cat'=>'supplier','en'=>'Reset Password',                    'fr' => 'Réinitialiser le mot de passe'],
    ['key' => 'sl_reset_subtitle',             'cat'=>'supplier','en'=>'Enter your new password below',     'fr' => 'Entrez votre nouveau mot de passe ci-dessous'],
    ['key' => 'sl_reset_new_password',         'cat'=>'supplier','en'=>'New Password',                      'fr' => 'Nouveau mot de passe'],
    ['key' => 'sl_reset_new_password_placeholder','cat'=>'supplier','en'=>'Enter new password',             'fr' => 'Entrez le nouveau mot de passe'],
    ['key' => 'sl_reset_confirm_password',     'cat'=>'supplier','en'=>'Confirm New Password',              'fr' => 'Confirmer le nouveau mot de passe'],
    ['key' => 'sl_reset_confirm_placeholder',  'cat'=>'supplier','en'=>'Confirm your new password',         'fr' => 'Confirmez votre nouveau mot de passe'],
    ['key' => 'sl_reset_password_hint',        'cat'=>'supplier','en'=>'Minimum 10 characters',             'fr' => 'Minimum 10 caractères'],
    ['key' => 'sl_reset_submit',               'cat'=>'supplier','en'=>'Reset Password',                    'fr' => 'Réinitialiser'],
    ['key' => 'sl_reset_back',                 'cat'=>'supplier','en'=>'Back to Login',                     'fr' => 'Retour à la connexion'],
];

$stmt = $pdo->prepare("
    INSERT INTO translations (`key`, category, en, fr)
    VALUES (?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)
");

$inserted = 0;
$updated  = 0;

foreach ($keys as $row) {
    // Check if exists
    $check = $pdo->prepare("SELECT COUNT(*) FROM translations WHERE `key` = ?");
    $check->execute([$row['key']]);
    $exists = (int)$check->fetchColumn();

    $stmt->execute([$row['key'], $row['cat'], $row['en'], $row['fr']]);
    if ($exists) {
        $updated++;
    } else {
        $inserted++;
    }
}

echo "Done!\n";
echo "  Inserted: $inserted new keys\n";
echo "  Updated:  $updated existing keys\n";
echo "  Total processed: " . count($keys) . "\n";
