<?php
/**
 * Migration: Add French translations for Supplier Login page
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $translations = [
        ['sl_login_title', 'supplier', 'Supplier Login', 'Connexion fournisseur'],
        ['sl_login_heading', 'supplier', 'Supplier Portal', 'Portail fournisseur'],
        ['sl_login_subtitle', 'supplier', 'Sign in to manage your products and orders', 'Connectez-vous pour g&eacute;rer vos produits et commandes'],
        ['sl_login_email', 'supplier', 'Email Address', 'Adresse courriel'],
        ['sl_login_email_placeholder', 'supplier', 'supplier@company.com', 'fournisseur@entreprise.com'],
        ['sl_login_password', 'supplier', 'Password', 'Mot de passe'],
        ['sl_login_password_placeholder', 'supplier', 'Enter your password', 'Entrez votre mot de passe'],
        ['sl_login_show_password', 'supplier', 'Show password', 'Afficher le mot de passe'],
        ['sl_login_hide_password', 'supplier', 'Hide password', 'Masquer le mot de passe'],
        ['sl_login_remember', 'supplier', 'Remember me for 30 days', 'Se souvenir de moi pendant 30 jours'],
        ['sl_login_submit', 'supplier', 'Sign In', 'Se connecter'],
        ['sl_login_need_help', 'supplier', 'Need Help?', 'Besoin d&rsquo;aide?'],
        ['sl_login_no_access', 'supplier', 'Don\'t have access? Contact your account manager at', 'Vous n&rsquo;avez pas acc&egrave;s? Contactez votre gestionnaire de compte &agrave;'],
        ['sl_login_back', 'supplier', 'Back to Supplier Portal', 'Retour au portail fournisseur'],
    ];

    $stmt = $db->prepare("INSERT INTO translations (`key`, category, en, fr) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)");

    $count = 0;
    foreach ($translations as $t) {
        $stmt->execute($t);
        $count++;
    }

    echo "Inserted/updated {$count} supplier login translations.\n";
    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
