<?php
/**
 * Migration: Add supplier page translation keys
 * Covers: layout-header nav, products page, pickup page, orders page, settings page
 */
require_once __DIR__ . '/../../bootstrap/init.php';

$pdo = Database::getConnection();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$keys = [
    // ── Layout / Nav ──────────────────────────────────────────
    'sup_schedule_pickup'         => ['Schedule Pickup',              'Planifier un ramassage'],

    // ── Products page ─────────────────────────────────────────
    'sup_my_products'             => ['My Products',                  'Mes produits'],
    'sup_add_product'             => ['Add Product',                  'Ajouter un produit'],
    'sup_add_first_product'       => ['Add Your First Product',       'Ajouter votre premier produit'],
    'sup_search_products'         => ['Search by name or SKU...',     'Rechercher par nom ou UGS...'],
    'sup_all_products'            => ['All Products',                 'Tous les produits'],
    'sup_available_only'          => ['Available Only',               'Disponibles seulement'],
    'sup_unavailable_only'        => ['Unavailable Only',             'Non disponibles seulement'],
    'sup_available'               => ['Available',                    'Disponible'],
    'sup_unavailable'             => ['Unavailable',                  'Non disponible'],
    'sup_stock'                   => ['Stock:',                       'Stock :'],
    'sup_weight'                  => ['Weight:',                      'Poids :'],
    'sup_min_order'               => ['Min Order:',                   'Commande min :'],
    'sup_lead_time'               => ['Lead Time:',                   'Délai :'],
    'sup_no_products'             => ['No Products Yet',              "Aucun produit pour l'instant"],
    'sup_no_products_desc'        => ['Start by adding your first product to your catalog', 'Commencez par ajouter votre premier produit à votre catalogue'],
    'sup_confirm_delete_product'  => ['Are you sure you want to delete this product?', 'Êtes-vous sûr de vouloir supprimer ce produit ?'],
    'sup_edit'                    => ['Edit',                         'Modifier'],

    // ── Pickup page ───────────────────────────────────────────
    'sup_pickup_desc'             => ['Request a pickup for your accepted purchase orders', 'Demandez un ramassage pour vos commandes acceptées'],
    'sup_schedule_new_pickup'     => ['Schedule New Pickup',          'Planifier un nouveau ramassage'],
    'sup_my_requests'             => ['My Requests',                  'Mes demandes'],
    'sup_no_orders_pickup'        => ['No orders ready for pickup',   'Aucune commande prête pour ramassage'],
    'sup_view_purchase_orders'    => ['View Purchase Orders',         'Voir les bons de commande'],
    'sup_pickup_step1'            => ['Step 1 — Select Purchase Orders', 'Étape 1 — Sélectionner les bons de commande'],
    'sup_pickup_step1_desc'       => ['Select the purchase orders you want picked up in this request. You can combine multiple orders in a single pickup.', 'Sélectionnez les bons de commande à inclure dans cette demande. Vous pouvez combiner plusieurs commandes.'],
    'sup_pickup_step2'            => ['Step 2 — Pickup Address',      'Étape 2 — Adresse de ramassage'],
    'sup_pickup_address'          => ['Pickup Address',               'Adresse de ramassage'],
    'sup_pickup_address_hint'     => ['Pre-filled from your profile. Edit if pickup is from a different location.', 'Pré-rempli à partir de votre profil. Modifiez si le ramassage est à un autre endroit.'],
    'sup_pickup_step3'            => ['Step 3 — Preferred Date & Time Window', 'Étape 3 — Date et plage horaire souhaitées'],
    'sup_pickup_date'             => ['Pickup Date',                  'Date de ramassage'],
    'sup_pickup_time_from'        => ['Earliest Time',                'Heure au plus tôt'],
    'sup_pickup_time_to'          => ['Latest Time',                  'Heure au plus tard'],
    'sup_select_time'             => ['Select time',                  "Choisir l'heure"],
    'sup_pickup_step4'            => ['Step 4 — Additional Notes (Optional)', 'Étape 4 — Notes supplémentaires (optionnel)'],
    'sup_notes_for_driver'        => ['Notes for our driver',         'Notes pour notre chauffeur'],
    'sup_submit_pickup'           => ['Submit Pickup Request',        'Soumettre la demande de ramassage'],
    'sup_pickup_history'          => ['Pickup Request History',       'Historique des demandes de ramassage'],
    'sup_no_pickups'              => ['No pickup requests yet.',      'Aucune demande de ramassage pour l\'instant.'],
    'sup_date_requested'          => ['Date Requested',               'Date de demande'],
    'sup_time_window'             => ['Time Window',                  'Plage horaire'],
    'sup_orders_col'              => ['Orders',                       'Commandes'],
    'sup_status'                  => ['Status',                       'Statut'],
    'sup_actions'                 => ['Actions',                      'Actions'],
    'sup_confirmed'               => ['Confirmed:',                   'Confirmé :'],
    'sup_cancel'                  => ['Cancel',                       'Annuler'],
    'sup_pickup_status_pending'   => ['Pending',                      'En attente'],
    'sup_pickup_status_scheduled' => ['Scheduled',                    'Planifié'],
    'sup_pickup_status_completed' => ['Completed',                    'Complété'],
    'sup_pickup_status_cancelled' => ['Cancelled',                    'Annulé'],
    'sup_pickup_select_po_error'  => ['Please select at least one purchase order.', 'Veuillez sélectionner au moins un bon de commande.'],
    'sup_pickup_time_error'       => ['Latest time must be after earliest time.', "L'heure au plus tard doit être après l'heure au plus tôt."],
    'sup_pickup_cancel_confirm'   => ['Cancel this pickup request?',  'Annuler cette demande de ramassage ?'],

    // ── Orders page ───────────────────────────────────────────
    'sup_purchase_orders'         => ['Purchase Orders',              'Bons de commande'],
    'sup_all_statuses'            => ['All Statuses',                 'Tous les statuts'],
    'sup_no_purchase_orders'      => ['No Purchase Orders',           'Aucun bon de commande'],
    'sup_no_purchase_orders_desc' => ["You don't have any purchase orders yet", "Vous n'avez pas encore de bons de commande"],
    'sup_po_number'               => ['PO Number',                    'N° de commande'],
    'sup_order_date'              => ['Order Date',                   'Date de commande'],
    'sup_expected_delivery'       => ['Expected Delivery',            'Livraison prévue'],
    'sup_items'                   => ['Items',                        'Articles'],
    'sup_total_amount'            => ['Total Amount',                 'Montant total'],
    'sup_filter'                  => ['Filter',                       'Filtrer'],

    // ── Settings page ─────────────────────────────────────────
    'sup_settings_desc'           => ['Manage your account settings and security', 'Gérez vos paramètres de compte et la sécurité'],
    'sup_profile_info'            => ['Profile Information',          'Informations du profil'],
    'sup_company_name'            => ['Company Name',                 "Nom de l'entreprise"],
    'sup_contact_person'          => ['Contact Person',               'Personne-ressource'],
    'sup_email'                   => ['Email',                        'Courriel'],
    'sup_email_hint'              => ['Contact admin to change email', "Contactez l'administrateur pour modifier le courriel"],
    'sup_phone'                   => ['Phone',                        'Téléphone'],
    'sup_tax_number'              => ['HST / GST Number',             'Numéro TPS / TVH'],
    'sup_tax_hint'                => ['Your Canada Revenue Agency business number', "Votre numéro d'entreprise de l'ARC"],
    'sup_address'                 => ['Address',                      'Adresse'],
    'sup_street_address'          => ['Street Address',               'Adresse civique'],
    'sup_city'                    => ['City',                         'Ville'],
    'sup_province'                => ['Province',                     'Province'],
    'sup_select_province'         => ['Select Province',              'Sélectionner la province'],
    'sup_postal_code'             => ['Postal Code',                  'Code postal'],
    'sup_country'                 => ['Country',                      'Pays'],
    'sup_save_profile'            => ['Save Profile',                 'Enregistrer le profil'],
    'sup_last_login'              => ['Last login:',                  'Dernière connexion :'],
    'sup_payment_info'            => ['Payment Information',          'Informations de paiement'],
    'sup_payment_info_desc'       => ['Provide your banking details so we can process payments for your purchase orders. All information is encrypted and only accessible by authorized OCSAPP staff.', "Fournissez vos coordonnées bancaires afin que nous puissions traiter les paiements de vos bons de commande. Toutes les informations sont chiffrées et accessibles uniquement par le personnel autorisé d'OCSAPP."],
    'sup_payment_preference'      => ['Payment Preference',           'Méthode de paiement préférée'],
    'sup_pref_eft'                => ['EFT / Direct Deposit',         'TVE / Dépôt direct'],
    'sup_pref_interac'            => ['Interac e-Transfer',           'Virement électronique Interac'],
    'sup_pref_cheque'             => ['Cheque',                       'Chèque'],
    'sup_eft_details'             => ['EFT / Direct Deposit Details', 'Détails TVE / Dépôt direct'],
    'sup_bank_name'               => ['Bank Name',                    'Nom de la banque'],
    'sup_bank_holder'             => ['Account Holder Name',          'Nom du titulaire du compte'],
    'sup_bank_transit'            => ['Transit Number (5 digits)',    'Numéro de transit (5 chiffres)'],
    'sup_bank_institution'        => ['Institution Number (3 digits)','Numéro d\'institution (3 chiffres)'],
    'sup_bank_account'            => ['Account Number',               'Numéro de compte'],
    'sup_bank_account_type'       => ['Account Type',                 'Type de compte'],
    'sup_select_type'             => ['Select type',                  'Sélectionner le type'],
    'sup_chequing'                => ['Chequing',                     'Chèques'],
    'sup_savings'                 => ['Savings',                      'Épargne'],
    'sup_interac_details'         => ['Interac e-Transfer Details',   'Détails du virement Interac'],
    'sup_interac_email'           => ['e-Transfer Email',             'Courriel de virement'],
    'sup_interac_email_hint'      => ['The email address you use to receive Interac e-Transfers', "L'adresse courriel que vous utilisez pour recevoir les virements Interac"],
    'sup_save_payment'            => ['Save Payment Info',            'Enregistrer les infos de paiement'],
    'sup_change_password'         => ['Change Password',              'Changer le mot de passe'],
    'sup_current_password'        => ['Current Password',             'Mot de passe actuel'],
    'sup_new_password'            => ['New Password',                 'Nouveau mot de passe'],
    'sup_confirm_password'        => ['Confirm New Password',         'Confirmer le nouveau mot de passe'],
    'sup_min_8_chars'             => ['Minimum 8 characters',         'Minimum 8 caractères'],
    'sup_update_password'         => ['Update Password',              'Mettre à jour le mot de passe'],
];

$stmt = $pdo->prepare("
    INSERT INTO translations (`key`, category, en, fr)
    VALUES (?, 'supplier', ?, ?)
    ON DUPLICATE KEY UPDATE en = VALUES(en), fr = VALUES(fr)
");

$inserted = 0;
$updated  = 0;

foreach ($keys as $key => [$en, $fr]) {
    $stmt->execute([$key, $en, $fr]);
    $rows = $stmt->rowCount();
    if ($rows === 1) $inserted++;
    elseif ($rows === 2) $updated++;
}

$total = $pdo->query('SELECT COUNT(*) FROM translations')->fetchColumn();
echo "Done! Inserted: $inserted  Updated: $updated\n";
echo "Total translations in DB: $total\n";
