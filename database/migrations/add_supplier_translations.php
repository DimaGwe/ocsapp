<?php
/**
 * Migration: Add French translations for supplier portal
 */

require __DIR__ . '/../../bootstrap/init.php';
require __DIR__ . '/../../config/database.php';

try {
    $db = Database::getConnection();

    $translations = [
        // Layout / Navigation
        ['sup_portal', 'supplier', 'Supplier Portal', 'Portail Fournisseur'],
        ['sup_dashboard', 'supplier', 'Dashboard', 'Tableau de bord'],
        ['sup_analytics', 'supplier', 'Analytics', 'Analytique'],
        ['sup_my_products', 'supplier', 'My Products', 'Mes produits'],
        ['sup_purchase_orders', 'supplier', 'Purchase Orders', 'Bons de commande'],
        ['sup_settings', 'supplier', 'Settings', 'Param&egrave;tres'],
        ['sup_account', 'supplier', 'Supplier Account', 'Compte fournisseur'],
        ['sup_logout', 'supplier', 'Logout', 'D&eacute;connexion'],

        // Dashboard
        ['sup_welcome_back', 'supplier', 'Welcome back', 'Bon retour'],
        ['sup_total_products', 'supplier', 'Total Products', 'Total des produits'],
        ['sup_available', 'supplier', 'Available', 'Disponible'],
        ['sup_recent_orders', 'supplier', 'Recent Orders', 'Commandes r&eacute;centes'],
        ['sup_order_number', 'supplier', 'Order #', 'Commande #'],
        ['sup_date', 'supplier', 'Date', 'Date'],
        ['sup_items', 'supplier', 'Items', 'Articles'],
        ['sup_total', 'supplier', 'Total', 'Total'],
        ['sup_status', 'supplier', 'Status', 'Statut'],
        ['sup_no_orders', 'supplier', 'No recent orders', 'Aucune commande r&eacute;cente'],
        ['sup_view_all', 'supplier', 'View All', 'Voir tout'],
        ['sup_view', 'supplier', 'View', 'Voir'],

        // Password reminder
        ['sup_pw_welcome', 'supplier', 'Welcome! For your security, please change your temporary password.', 'Bienvenue! Pour votre s&eacute;curit&eacute;, veuillez changer votre mot de passe temporaire.'],
        ['sup_pw_reminder', 'supplier', 'Reminder: You\'re still using a temporary password. Please update it for your account security.', 'Rappel: Vous utilisez toujours un mot de passe temporaire. Veuillez le mettre &agrave; jour pour la s&eacute;curit&eacute; de votre compte.'],
        ['sup_go_to_settings', 'supplier', 'Go to Settings', 'Aller aux param&egrave;tres'],

        // Settings page
        ['sup_profile', 'supplier', 'Profile', 'Profil'],
        ['sup_change_password', 'supplier', 'Change Password', 'Changer le mot de passe'],
        ['sup_current_password', 'supplier', 'Current Password', 'Mot de passe actuel'],
        ['sup_new_password', 'supplier', 'New Password', 'Nouveau mot de passe'],
        ['sup_confirm_password', 'supplier', 'Confirm Password', 'Confirmer le mot de passe'],
        ['sup_save_changes', 'supplier', 'Save Changes', 'Enregistrer'],
        ['sup_update_password', 'supplier', 'Update Password', 'Mettre &agrave; jour le mot de passe'],

        // Common
        ['sup_loading', 'supplier', 'Loading...', 'Chargement...'],
        ['sup_error', 'supplier', 'Error', 'Erreur'],
        ['sup_success', 'supplier', 'Success', 'Succ&egrave;s'],
        ['sup_actions', 'supplier', 'Actions', 'Actions'],

        // Application form
        ['sup_apply_title', 'supplier', 'Become a Supplier', 'Devenir fournisseur'],
        ['sup_apply_desc', 'supplier', 'Fill out the form below with your business information. Our team will review your application and get back to you.', 'Remplissez le formulaire ci-dessous avec les informations de votre entreprise. Notre &eacute;quipe examinera votre demande et vous contactera.'],
        ['sup_apply_owner_title', 'supplier', 'General Business Owner Information', 'Informations g&eacute;n&eacute;rales du propri&eacute;taire'],
        ['sup_apply_owner_desc', 'supplier', 'Tell us about yourself and your business.', 'Parlez-nous de vous et de votre entreprise.'],
        ['sup_apply_first_name', 'supplier', 'First Name', 'Pr&eacute;nom'],
        ['sup_apply_last_name', 'supplier', 'Last Name', 'Nom de famille'],
        ['sup_apply_email', 'supplier', 'Email Address', 'Adresse courriel'],
        ['sup_apply_phone', 'supplier', 'Phone Number', 'Num&eacute;ro de t&eacute;l&eacute;phone'],
        ['sup_apply_business_name', 'supplier', 'Business Name', 'Nom de l\'entreprise'],
        ['sup_apply_legal_title', 'supplier', 'Quebec Legal Identity Verification', 'V&eacute;rification de l\'identit&eacute; l&eacute;gale qu&eacute;b&eacute;coise'],
        ['sup_apply_legal_desc', 'supplier', 'Provide your Quebec business registration details for verification.', 'Fournissez vos d&eacute;tails d\'enregistrement d\'entreprise au Qu&eacute;bec pour v&eacute;rification.'],
        ['sup_apply_legal_info', 'supplier', 'Business records are verified through the', 'Les dossiers d\'entreprise sont v&eacute;rifi&eacute;s via le'],
        ['sup_apply_neq_hint', 'supplier', 'Your NEQ (Enterprise Number) can be found on your business registration documents.', 'Votre NEQ (num&eacute;ro d\'entreprise) se trouve sur vos documents d\'enregistrement.'],
        ['sup_apply_neq_label', 'supplier', 'NEQ (Enterprise Number)', 'NEQ (Num&eacute;ro d\'entreprise)'],
        ['sup_apply_neq_digits', 'supplier', '10-digit identification number from the Registraire des entreprises', 'Num&eacute;ro d\'identification &agrave; 10 chiffres du Registraire des entreprises'],
        ['sup_apply_legal_name', 'supplier', 'Legal Name', 'Raison sociale'],
        ['sup_apply_legal_name_hint', 'supplier', 'Must comply with the Charter of the French Language', 'Doit &ecirc;tre conforme &agrave; la Charte de la langue fran&ccedil;aise'],
        ['sup_apply_operating_names', 'supplier', 'Operating Name(s)', 'Nom(s) d\'exploitation'],
        ['sup_apply_operating_hint', 'supplier', 'Separate multiple names with commas', 'S&eacute;parez les noms multiples par des virgules'],
        ['sup_apply_address', 'supplier', 'Registered Office Address', 'Adresse du si&egrave;ge social'],
        ['sup_apply_street', 'supplier', 'Street address', 'Adresse'],
        ['sup_apply_city', 'supplier', 'City', 'Ville'],
        ['sup_apply_province', 'supplier', 'Province', 'Province'],
        ['sup_apply_postal', 'supplier', 'Postal Code', 'Code postal'],
        ['sup_apply_docs_title', 'supplier', 'Verification Documents', 'Documents de v&eacute;rification'],
        ['sup_apply_docs_desc', 'supplier', 'Upload copies of your business registration documents for verification.', 'T&eacute;l&eacute;versez des copies de vos documents d\'enregistrement pour v&eacute;rification.'],
        ['sup_apply_doc_cert', 'supplier', 'Certificate of Incorporation', 'Certificat de constitution'],
        ['sup_apply_doc_cert_hint', 'supplier', 'For corporations. PDF, JPG, or PNG (max 5MB)', 'Pour les soci&eacute;t&eacute;s. PDF, JPG ou PNG (max 5 Mo)'],
        ['sup_apply_doc_decl', 'supplier', 'Declaration of Registration', 'D&eacute;claration d\'immatriculation'],
        ['sup_apply_doc_decl_hint', 'supplier', 'For sole proprietorships / partnerships. PDF, JPG, or PNG (max 5MB)', 'Pour les entreprises individuelles / soci&eacute;t&eacute;s de personnes. PDF, JPG ou PNG (max 5 Mo)'],
        ['sup_apply_doc_reg', 'supplier', 'Enterprise Register File Search', 'Recherche au Registre des entreprises'],
        ['sup_apply_doc_reg_hint', 'supplier', 'Publicly available from the REQ. PDF, JPG, or PNG (max 5MB)', 'Disponible publiquement au REQ. PDF, JPG ou PNG (max 5 Mo)'],
        ['sup_apply_upload', 'supplier', 'Click to upload', 'Cliquer pour t&eacute;l&eacute;verser'],
        ['sup_apply_drag', 'supplier', 'or drag and drop', 'ou glisser-d&eacute;poser'],
        ['sup_apply_doc_secure', 'supplier', 'Your documents are securely stored and only used for verification purposes. They will not be shared with third parties.', 'Vos documents sont stock&eacute;s en toute s&eacute;curit&eacute; et utilis&eacute;s uniquement &agrave; des fins de v&eacute;rification. Ils ne seront pas partag&eacute;s avec des tiers.'],
        ['sup_apply_submit', 'supplier', 'Submit Application', 'Soumettre la demande'],
        ['sup_apply_agree', 'supplier', 'By submitting, you agree to OCSAPP\'s terms of service and privacy policy.', 'En soumettant, vous acceptez les conditions d\'utilisation et la politique de confidentialit&eacute; d\'OCSAPP.'],
        ['sup_apply_success_title', 'supplier', 'Application Submitted!', 'Demande soumise!'],
        ['sup_apply_success_msg', 'supplier', 'Thank you for your interest in becoming an OCSAPP supplier. Our team will review your application and contact you within 2-3 business days.', 'Merci de votre int&eacute;r&ecirc;t &agrave; devenir fournisseur OCSAPP. Notre &eacute;quipe examinera votre demande et vous contactera dans un d&eacute;lai de 2 &agrave; 3 jours ouvrables.'],
        ['sup_apply_back', 'supplier', 'Back to Supplier Portal', 'Retour au portail fournisseur'],
        ['sup_apply_full_legal', 'supplier', 'Full legal name as registered', 'Nom l&eacute;gal complet tel qu\'enregistr&eacute;'],
        ['sup_apply_trade_names', 'supplier', 'Trade names or DBA names (if different from legal name)', 'Noms commerciaux (si diff&eacute;rents du nom l&eacute;gal)'],
    ];

    $stmt = $db->prepare("INSERT INTO translations (`key`, category, en, fr) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)");

    $count = 0;
    foreach ($translations as $t) {
        $stmt->execute($t);
        $count++;
    }

    echo "Inserted/updated {$count} supplier translations.\n";
    echo "Migration complete.\n";

} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
