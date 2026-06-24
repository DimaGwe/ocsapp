<?php
$currentLang = $_SESSION['language'] ?? 'fr';
$translations = [
    'en' => [
        'page_title'           => 'Settings',
        /* Delivery */
        'delivery_address'     => 'Delivery Address',
        'street_address'       => 'Street Address',
        'street_hint'          => 'Type to search — selecting a suggestion auto-fills all fields below',
        'city'                 => 'City',
        'province'             => 'Province',
        'select_province'      => 'Select Province',
        'postal_code'          => 'Postal Code',
        'country'              => 'Country',
        'save_address'         => 'Save Address',
        /* Billing */
        'billing_address'      => 'Billing Address',
        'billing_same'         => 'Same as delivery address',
        'billing_street'       => 'Street Address',
        'billing_city'         => 'City',
        'billing_province'     => 'Province',
        'billing_postal'       => 'Postal Code',
        'billing_country'      => 'Country',
        'save_billing'         => 'Save Billing Address',
        /* Payment */
        'payment_info'         => 'Payment Information',
        'payment_info_desc'    => 'Provide your banking details so we can process payments for your orders. All information is encrypted and only accessible by authorized OCSAPP staff.',
        'payment_pref'         => 'Payment Preference',
        'pref_eft'             => 'EFT / Direct Deposit',
        'pref_interac'         => 'Interac e-Transfer',
        'pref_cheque'          => 'Cheque',
        'eft_details'          => 'EFT / Direct Deposit Details',
        'bank_name'            => 'Bank Name',
        'account_holder'       => 'Account Holder Name',
        'name_on_account'      => 'Name on account',
        'transit_num'          => 'Transit Number (5 digits)',
        'institution_num'      => 'Institution Number (3 digits)',
        'account_num'          => 'Account Number',
        'account_type'         => 'Account Type',
        'select_type'          => 'Select type',
        'chequing'             => 'Chequing',
        'savings'              => 'Savings',
        'interac_details'      => 'Interac e-Transfer Details',
        'etransfer_email'      => 'e-Transfer Email',
        'etransfer_email_hint' => 'The email address you use to receive Interac e-Transfers',
        'cheque_notice'        => '<strong>Cheque payments</strong> will be mailed to the address on your profile. Please ensure your delivery address above is accurate and up to date.',
        'save_payment'         => 'Save Payment Info',
        /* Account */
        'account_info'         => 'Account Information',
        'company_name'         => 'Company Name',
        'contact_person'       => 'Contact Person',
        'email'                => 'Email',
        'phone'                => 'Phone',
        'account_tier'         => 'Account Tier',
        'member_since'         => 'Member Since',
        'tier_standard'        => 'Standard Account',
        'tier_approved'        => 'Approved Account',
        'tier_premium'         => 'Premium Account',
        /* JS messages */
        'saving'               => 'Saving...',
        'no_results'           => 'No results found — try adding the city name',
        'search_failed'        => 'Search failed — try again',
        'searching'            => 'Searching...',
        'error_occurred'       => 'An error occurred. Please try again.',
        'error_updating'       => 'Error updating',
        'address_updated'      => 'Address updated successfully',
        'billing_updated'      => 'Billing address updated successfully',
        'payment_updated'      => 'Payment information saved',
        /* Change Password */
        'change_password'      => 'Change Password',
        'current_password'     => 'Current Password',
        'new_password'         => 'New Password',
        'confirm_password'     => 'Confirm New Password',
        'update_password'      => 'Update Password',
        'pw_min_length'        => 'At least 8 characters',
        'pw_uppercase'         => 'One uppercase letter (A-Z)',
        'pw_lowercase'         => 'One lowercase letter (a-z)',
        'pw_number'            => 'One number (0-9)',
        'pw_special'           => 'One special character (!@#$%^&*)',
        'pw_no_match'          => 'Passwords do not match.',
        'pw_too_short'         => 'New password must be at least 8 characters.',
        'pw_mismatch'          => 'New passwords do not match.',
        'pw_generic_error'     => 'An error occurred. Please try again.',
        /* Notifications */
        'notif_title'          => 'Notifications &amp; Sound',
        'notif_sounds_label'   => 'Notification Sounds',
        'notif_sounds_desc'    => 'Play a chime when a new distribution request or alert arrives.',
        'test_sound'           => 'Test Sound',
        'browser_notif'        => 'Browser Notifications',
        'browser_notif_desc'   => "Show a system popup even when you're looking at another tab.",
        'sound_played'         => 'Sound played!',
        'sound_unavailable'    => 'Sound function not available.',
        'notif_not_supported'  => 'Not supported',
        'notif_not_sup_msg'    => 'Your browser does not support desktop notifications.',
        'notif_enabled'        => 'Enabled',
        'notif_enabled_msg'    => "You'll receive desktop notifications for new alerts.",
        'notif_blocked'        => 'Blocked',
        'notif_blocked_msg'    => "Notifications are blocked. Go to your browser's site settings to allow them for this site.",
        'notif_not_enabled'    => 'Not enabled',
        'notif_enable_btn'     => 'Enable Browser Notifications',
    ],
    'fr' => [
        'page_title'           => 'Param&egrave;tres',
        /* Delivery */
        'delivery_address'     => 'Adresse de livraison',
        'street_address'       => 'Adresse',
        'street_hint'          => 'Commencez &agrave; taper &mdash; s&eacute;lectionner une suggestion remplit tous les champs',
        'city'                 => 'Ville',
        'province'             => 'Province',
        'select_province'      => 'S&eacute;lectionnez une province',
        'postal_code'          => 'Code postal',
        'country'              => 'Pays',
        'save_address'         => 'Enregistrer l\'adresse',
        /* Billing */
        'billing_address'      => 'Adresse de facturation',
        'billing_same'         => 'Identique &agrave; l\'adresse de livraison',
        'billing_street'       => 'Adresse',
        'billing_city'         => 'Ville',
        'billing_province'     => 'Province',
        'billing_postal'       => 'Code postal',
        'billing_country'      => 'Pays',
        'save_billing'         => 'Enregistrer l\'adresse de facturation',
        /* Payment */
        'payment_info'         => 'Informations de paiement',
        'payment_info_desc'    => 'Fournissez vos coordonn&eacute;es bancaires afin que nous puissions traiter les paiements pour vos commandes. Toutes les informations sont chiffr&eacute;es et accessibles uniquement par le personnel autoris&eacute; d\'OCSAPP.',
        'payment_pref'         => 'Mode de paiement pr&eacute;f&eacute;r&eacute;',
        'pref_eft'             => 'TVE / Virement direct',
        'pref_interac'         => 'Virement Interac',
        'pref_cheque'          => 'Ch&egrave;que',
        'eft_details'          => 'D&eacute;tails TVE / Virement direct',
        'bank_name'            => 'Nom de la banque',
        'account_holder'       => 'Nom du titulaire du compte',
        'name_on_account'      => 'Nom sur le compte',
        'transit_num'          => 'Num&eacute;ro de transit (5 chiffres)',
        'institution_num'      => 'Num&eacute;ro d\'institution (3 chiffres)',
        'account_num'          => 'Num&eacute;ro de compte',
        'account_type'         => 'Type de compte',
        'select_type'          => 'S&eacute;lectionner le type',
        'chequing'             => 'Ch&egrave;ques',
        'savings'              => '&Eacute;pargne',
        'interac_details'      => 'D&eacute;tails Virement Interac',
        'etransfer_email'      => 'Courriel Virement',
        'etransfer_email_hint' => 'L\'adresse courriel que vous utilisez pour recevoir les virements Interac',
        'cheque_notice'        => '<strong>Les paiements par ch&egrave;que</strong> seront envoy&eacute;s par courrier &agrave; l\'adresse de livraison de votre profil. Veuillez vous assurer que votre adresse ci-dessus est exacte et &agrave; jour.',
        'save_payment'         => 'Enregistrer les informations de paiement',
        /* Account */
        'account_info'         => 'Informations du compte',
        'company_name'         => 'Nom de l\'entreprise',
        'contact_person'       => 'Personne-ressource',
        'email'                => 'Courriel',
        'phone'                => 'T&eacute;l&eacute;phone',
        'account_tier'         => 'Niveau de compte',
        'member_since'         => 'Membre depuis',
        'tier_standard'        => 'Compte Standard',
        'tier_approved'        => 'Compte Approuv&eacute;',
        'tier_premium'         => 'Compte Premium',
        /* JS messages */
        'saving'               => 'Enregistrement...',
        'no_results'           => 'Aucun r&eacute;sultat &mdash; essayez d\'ajouter le nom de la ville',
        'search_failed'        => 'Recherche &eacute;chou&eacute;e &mdash; veuillez r&eacute;essayer',
        'searching'            => 'Recherche...',
        'error_occurred'       => 'Une erreur s\'est produite. Veuillez r&eacute;essayer.',
        'error_updating'       => 'Erreur lors de la mise &agrave; jour',
        'address_updated'      => 'Adresse mise &agrave; jour avec succ&egrave;s',
        'billing_updated'      => 'Adresse de facturation mise &agrave; jour',
        'payment_updated'      => 'Informations de paiement enregistr&eacute;es',
        /* Change Password */
        'change_password'      => 'Modifier le mot de passe',
        'current_password'     => 'Mot de passe actuel',
        'new_password'         => 'Nouveau mot de passe',
        'confirm_password'     => 'Confirmer le nouveau mot de passe',
        'update_password'      => 'Mettre &agrave; jour le mot de passe',
        'pw_min_length'        => '8 caract&egrave;res minimum',
        'pw_uppercase'         => 'Une lettre majuscule (A-Z)',
        'pw_lowercase'         => 'Une lettre minuscule (a-z)',
        'pw_number'            => 'Un chiffre (0-9)',
        'pw_special'           => 'Un caract&egrave;re sp&eacute;cial (!@#$%^&*)',
        'pw_no_match'          => 'Les mots de passe ne correspondent pas.',
        'pw_too_short'         => 'Le nouveau mot de passe doit contenir au moins 8 caract&egrave;res.',
        'pw_mismatch'          => 'Les mots de passe ne correspondent pas.',
        'pw_generic_error'     => 'Une erreur est survenue. Veuillez r&eacute;essayer.',
        /* Notifications */
        'notif_title'          => 'Notifications et son',
        'notif_sounds_label'   => 'Sons de notification',
        'notif_sounds_desc'    => 'Jouer un son lorsqu\'une nouvelle demande de distribution ou alerte arrive.',
        'test_sound'           => 'Tester le son',
        'browser_notif'        => 'Notifications du navigateur',
        'browser_notif_desc'   => 'Afficher une fen&ecirc;tre contextuelle m&ecirc;me lorsque vous regardez un autre onglet.',
        'sound_played'         => 'Son jou&eacute; !',
        'sound_unavailable'    => 'Fonction sonore non disponible.',
        'notif_not_supported'  => 'Non pris en charge',
        'notif_not_sup_msg'    => 'Votre navigateur ne prend pas en charge les notifications.',
        'notif_enabled'        => 'Activ&eacute;es',
        'notif_enabled_msg'    => 'Vous recevrez des notifications pour les nouvelles demandes et alertes.',
        'notif_blocked'        => 'Bloqu&eacute;es',
        'notif_blocked_msg'    => 'Notifications bloqu&eacute;es. Allez dans les param&egrave;tres du site de votre navigateur pour les autoriser.',
        'notif_not_enabled'    => 'Non activ&eacute;es',
        'notif_enable_btn'     => 'Activer les notifications',
    ],
];
$t = $translations[$currentLang] ?? $translations['en'];

$tierLabels = [
    'standard' => $t['tier_standard'],
    'approved' => $t['tier_approved'],
    'premium'  => $t['tier_premium'],
];

$useSameForBilling = (bool)($business['use_delivery_for_billing'] ?? 1);
?>
<?php $dt = $t; include __DIR__ . '/layout-header.php'; $t = $dt; ?>

<style>
    .settings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        align-items: start;
    }
    .section-card {
        background: white;
        border-radius: 12px;
        padding: 28px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 24px;
    }
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 22px;
    }
    .section-title {
        font-size: 17px;
        font-weight: 600;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i { color: #00b207; }
    .section-desc {
        font-size: 13px;
        color: #9ca3af;
        margin-top: -14px;
        margin-bottom: 20px;
    }

    /* Form */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #374151;
        font-size: 13px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
        color: #1a1a1a;
        background: #fff;
    }
    .form-group textarea {
        resize: vertical;
        min-height: 90px;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #00b207;
        box-shadow: 0 0 0 3px rgba(0,178,7,0.1);
    }
    .form-group small {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
    }

    /* Checkbox toggle */
    .checkbox-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        padding: 12px 14px;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 8px;
        cursor: pointer;
    }
    .checkbox-row input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #00b207;
        flex-shrink: 0;
        cursor: pointer;
    }
    .checkbox-row label {
        font-size: 14px;
        font-weight: 500;
        color: #166534;
        cursor: pointer;
        margin: 0;
    }

    /* Save button */
    .btn-save {
        padding: 11px 24px;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #00b207 0%, #008505 100%);
        color: white;
        transition: all 0.2s;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,178,7,0.3);
    }
    .btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    /* Inline message */
    .form-message {
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 14px;
        font-size: 13px;
        display: none;
    }
    .form-message.alert-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #6ee7b7;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .form-message.alert-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Address autocomplete */
    .address-autocomplete-wrapper { position: relative; }
    .address-suggestions {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 6px 6px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 240px;
        overflow-y: auto;
    }
    .address-suggestions.active { display: block; }
    .address-suggestion-item {
        padding: 10px 14px;
        cursor: pointer;
        font-size: 14px;
        color: #374151;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        transition: background 0.15s;
    }
    .address-suggestion-item:last-child { border-bottom: none; }
    .address-suggestion-item:hover,
    .address-suggestion-item.highlighted { background: #f0fdf4; }
    .address-suggestion-item i { color: #00b207; margin-top: 3px; flex-shrink: 0; }
    .address-suggestion-item .suggestion-main  { font-weight: 500; }
    .address-suggestion-item .suggestion-detail{ font-size: 12px; color: #6b7280; }
    .address-loading { padding: 12px 14px; text-align: center; color: #6b7280; font-size: 13px; }

    /* Account Info */
    .account-info { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
    .info-group { margin-bottom: 16px; }
    .info-label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
    .info-value { font-size: 15px; color: #1a1a1a; font-weight: 500; }

    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .badge-standard { background: #f3f4f6; color: #666; }
    .badge-approved  { background: #d1fae5; color: #065f46; }
    .badge-premium   { background: #fef3c7; color: #b45309; }

    @media (max-width: 900px) {
        .settings-grid { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .account-info { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .section-card { padding: 16px; }
        .address-suggestions { max-height: 180px; }
    }

    /* Payment preference selector */
    .payment-pref-options {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 6px;
    }
    .pref-option {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border: 2px solid #d1d5db;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        transition: all 0.2s;
        background: #fff;
    }
    .pref-option:hover {
        border-color: #00b207;
        background: #f0fdf4;
    }
    .pref-option.active {
        border-color: #00b207;
        background: #f0fdf4;
        color: #166534;
    }
    .pref-option input[type="radio"] { display: none; }
    .pref-option i { color: #00b207; }

/* Notification section */
.notif-pref-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:6px 0; }
.notif-pref-info { flex:1; }
.notif-pref-label { font-size:14px; font-weight:600; color:#111827; display:flex; align-items:center; gap:8px; margin-bottom:4px; }
.notif-pref-label i { color:#00b207; }
.notif-pref-desc { font-size:13px; color:#6b7280; line-height:1.5; }
.toggle-switch { position:relative; display:inline-block; width:46px; height:26px; flex-shrink:0; cursor:pointer; }
.toggle-switch input { opacity:0; width:0; height:0; }
.toggle-track { position:absolute; inset:0; background:#d1d5db; border-radius:26px; transition:background 0.2s; }
.toggle-track::before { content:''; position:absolute; width:20px; height:20px; left:3px; bottom:3px; background:white; border-radius:50%; transition:transform 0.2s; box-shadow:0 1px 3px rgba(0,0,0,0.2); }
.toggle-switch input:checked + .toggle-track { background:#00b207; }
.toggle-switch input:checked + .toggle-track::before { transform:translateX(20px); }
.btn-test-sound { background:none; border:1px solid #00b207; color:#00b207; border-radius:8px; padding:7px 16px; font-size:13px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:6px; transition:background 0.15s,color 0.15s; }
.btn-test-sound:hover { background:#00b207; color:white; }
.notif-status-badge { display:inline-block; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600; flex-shrink:0; }
.notif-status-badge.granted { background:#d1fae5; color:#059669; }
.notif-status-badge.default { background:#fef3c7; color:#92400e; }
.notif-status-badge.denied  { background:#fee2e2; color:#dc2626; }

/* Password form */
.password-wrapper { position: relative; }
.password-wrapper input { padding-right: 44px; }
.password-toggle {
    position: absolute; right: 8px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: #6b7280;
    padding: 4px 8px; font-size: 16px; transition: color 0.2s;
}
.password-toggle:hover { color: #00b207; }
.pw-rules-box { margin-top: 8px; display: flex; flex-direction: column; gap: 2px; }
.pw-rule      { font-size: 13px; display: flex; align-items: center; gap: 6px; padding: 2px 0; transition: color 0.2s; }
.pw-rule-fail { color: #9ca3af; }
.pw-rule-ok   { color: #00b207; }
.pw-rule i    { font-size: 12px; }
.pw-match-error { font-size: 13px; color: #ef4444; margin-top: 6px; display: none; }
</style>

<div class="settings-grid">

<!-- LEFT COLUMN -->
<div>

    <!-- 1. Delivery Address -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title"><i class="fas fa-map-marker-alt"></i> <?= $t['delivery_address'] ?></div>
        </div>
        <form id="addressForm">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="delivery_street"><?= $t['street_address'] ?></label>
                <div class="address-autocomplete-wrapper">
                    <input type="text" id="delivery_street" name="delivery_street"
                           value="<?= htmlspecialchars($business['delivery_street'] ?? '') ?>"
                           placeholder="<?= $currentLang === 'fr' ? 'Commencez à taper une adresse...' : 'Start typing an address...' ?>"
                           autocomplete="off">
                    <div id="addressSuggestions" class="address-suggestions"></div>
                </div>
                <small><i class="fas fa-magic" style="color:#00b207;margin-right:4px;"></i><?= $t['street_hint'] ?></small>
                <input type="hidden" id="delivery_latitude"  name="delivery_latitude"  value="<?= htmlspecialchars($business['delivery_latitude']  ?? '') ?>">
                <input type="hidden" id="delivery_longitude" name="delivery_longitude" value="<?= htmlspecialchars($business['delivery_longitude'] ?? '') ?>">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="delivery_city"><?= $t['city'] ?></label>
                    <input type="text" id="delivery_city" name="delivery_city"
                           value="<?= htmlspecialchars($business['delivery_city'] ?? '') ?>" placeholder="Montreal">
                </div>
                <div class="form-group">
                    <label for="delivery_province"><?= $t['province'] ?></label>
                    <select id="delivery_province" name="delivery_province">
                        <option value=""><?= $t['select_province'] ?></option>
                        <?php
                        $provinces = [
                            'AB'=>'Alberta','BC'=>'British Columbia','MB'=>'Manitoba',
                            'NB'=>'New Brunswick','NL'=>'Newfoundland and Labrador',
                            'NS'=>'Nova Scotia','NT'=>'Northwest Territories',
                            'NU'=>'Nunavut','ON'=>'Ontario','PE'=>'Prince Edward Island',
                            'QC'=>'Quebec','SK'=>'Saskatchewan','YT'=>'Yukon'
                        ];
                        $curProv = $business['delivery_province'] ?? '';
                        foreach ($provinces as $code => $name):
                        ?>
                        <option value="<?= $code ?>" <?= ($curProv===$code||$curProv===$name)?'selected':'' ?>><?= $name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="delivery_postal_code"><?= $t['postal_code'] ?></label>
                    <input type="text" id="delivery_postal_code" name="delivery_postal_code"
                           value="<?= htmlspecialchars($business['delivery_postal_code'] ?? '') ?>"
                           placeholder="H9R 1B7" maxlength="7">
                </div>
                <div class="form-group">
                    <label for="delivery_country"><?= $t['country'] ?></label>
                    <input type="text" id="delivery_country" name="delivery_country"
                           value="<?= htmlspecialchars($business['delivery_country'] ?? 'Canada') ?>">
                </div>
            </div>

            <div id="addressMessage" class="form-message"></div>
            <button type="submit" class="btn-save" id="saveAddressBtn">
                <i class="fas fa-save"></i> <?= $t['save_address'] ?>
            </button>
        </form>
    </div>

    <!-- 2. Payment Information -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title"><i class="fas fa-university"></i> <?= $t['payment_info'] ?></div>
        </div>
        <p class="section-desc"><?= $t['payment_info_desc'] ?></p>
        <form id="paymentForm">
            <?= csrfField() ?>

            <!-- Payment Preference -->
            <div class="form-group">
                <label><?= $t['payment_pref'] ?></label>
                <div class="payment-pref-options">
                    <?php $pref = $business['payment_preference'] ?? ''; ?>
                    <label class="pref-option <?= $pref === 'eft' ? 'active' : '' ?>">
                        <input type="radio" name="payment_preference" value="eft" <?= $pref === 'eft' ? 'checked' : '' ?>>
                        <i class="fas fa-exchange-alt"></i> <?= $t['pref_eft'] ?>
                    </label>
                    <label class="pref-option <?= $pref === 'interac' ? 'active' : '' ?>">
                        <input type="radio" name="payment_preference" value="interac" <?= $pref === 'interac' ? 'checked' : '' ?>>
                        <i class="fas fa-envelope"></i> <?= $t['pref_interac'] ?>
                    </label>
                    <label class="pref-option <?= $pref === 'cheque' ? 'active' : '' ?>">
                        <input type="radio" name="payment_preference" value="cheque" <?= $pref === 'cheque' ? 'checked' : '' ?>>
                        <i class="fas fa-file-invoice-dollar"></i> <?= $t['pref_cheque'] ?>
                    </label>
                </div>
            </div>

            <!-- EFT Fields -->
            <div id="eftFields" class="banking-section" style="display:<?= $pref === 'eft' ? 'block' : 'none' ?>;">
                <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-landmark" style="color:#00b207;margin-right:8px;"></i><?= $t['eft_details'] ?></h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bank_name"><?= $t['bank_name'] ?> <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="bank_name" name="bank_name"
                               value="<?= htmlspecialchars($business['bank_name'] ?? '') ?>"
                               placeholder="ex. TD, RBC, BMO">
                    </div>
                    <div class="form-group">
                        <label for="bank_account_holder"><?= $t['account_holder'] ?> <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="bank_account_holder" name="bank_account_holder"
                               value="<?= htmlspecialchars($business['bank_account_holder'] ?? '') ?>"
                               placeholder="<?= htmlspecialchars(html_entity_decode($t['name_on_account'], ENT_QUOTES, 'UTF-8')) ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bank_transit"><?= $t['transit_num'] ?></label>
                        <input type="text" id="bank_transit" name="bank_transit"
                               value="<?= htmlspecialchars($business['bank_transit'] ?? '') ?>"
                               placeholder="12345" maxlength="5" pattern="\d{5}">
                    </div>
                    <div class="form-group">
                        <label for="bank_institution"><?= $t['institution_num'] ?></label>
                        <input type="text" id="bank_institution" name="bank_institution"
                               value="<?= htmlspecialchars($business['bank_institution'] ?? '') ?>"
                               placeholder="004" maxlength="3" pattern="\d{3}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="bank_account"><?= $t['account_num'] ?></label>
                        <input type="text" id="bank_account" name="bank_account"
                               value="<?= htmlspecialchars($business['bank_account'] ?? '') ?>"
                               placeholder="7-12 digits" maxlength="12">
                    </div>
                    <div class="form-group">
                        <label for="bank_account_type"><?= $t['account_type'] ?></label>
                        <select id="bank_account_type" name="bank_account_type">
                            <option value=""><?= $t['select_type'] ?></option>
                            <option value="chequing" <?= ($business['bank_account_type'] ?? '') === 'chequing' ? 'selected' : '' ?>><?= $t['chequing'] ?></option>
                            <option value="savings"  <?= ($business['bank_account_type'] ?? '') === 'savings'  ? 'selected' : '' ?>><?= $t['savings'] ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Interac Fields -->
            <div id="interacFields" class="banking-section" style="display:<?= $pref === 'interac' ? 'block' : 'none' ?>;">
                <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                <h3 style="font-size:15px;color:#374151;margin-bottom:16px;"><i class="fas fa-envelope" style="color:#00b207;margin-right:8px;"></i><?= $t['interac_details'] ?></h3>
                <div class="form-group">
                    <label for="interac_email"><?= $t['etransfer_email'] ?> <span style="color:#ef4444;">*</span></label>
                    <input type="email" id="interac_email" name="interac_email"
                           value="<?= htmlspecialchars($business['interac_email'] ?? '') ?>"
                           placeholder="payments@yourcompany.ca">
                    <small><?= $t['etransfer_email_hint'] ?></small>
                </div>
            </div>

            <!-- Cheque notice -->
            <div id="chequeFields" class="banking-section" style="display:<?= $pref === 'cheque' ? 'block' : 'none' ?>;">
                <hr style="margin:16px 0;border:none;border-top:1px solid #e5e7eb;">
                <div style="background:#fef3c7;border-left:4px solid #f59e0b;padding:14px 18px;border-radius:6px;">
                    <p style="margin:0;font-size:14px;color:#92400e;"><?= $t['cheque_notice'] ?></p>
                </div>
            </div>

            <div id="paymentMessage" class="form-message"></div>
            <button type="submit" class="btn-save" id="savePaymentBtn">
                <i class="fas fa-save"></i> <?= $t['save_payment'] ?>
            </button>
        </form>
    </div>

</div><!-- /left column -->

<!-- RIGHT COLUMN -->
<div>

    <!-- 3. Billing Address -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title"><i class="fas fa-file-invoice-dollar"></i> <?= $t['billing_address'] ?></div>
        </div>
        <form id="billingForm">
            <?= csrfField() ?>

            <div class="checkbox-row" onclick="toggleBillingSame()">
                <input type="checkbox"
                       id="use_delivery_for_billing"
                       name="use_delivery_for_billing"
                       value="1"
                       <?= $useSameForBilling ? 'checked' : '' ?>>
                <label for="use_delivery_for_billing"><?= $t['billing_same'] ?></label>
            </div>

            <div id="billingFields" style="<?= $useSameForBilling ? 'display:none;' : '' ?>">
                <div class="form-group">
                    <label><?= $t['billing_street'] ?></label>
                    <input type="text" name="billing_street"
                           value="<?= htmlspecialchars($business['billing_street'] ?? '') ?>"
                           placeholder="<?= $currentLang === 'fr' ? '123 rue de la Facturation' : '123 Billing Street' ?>">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><?= $t['billing_city'] ?></label>
                        <input type="text" name="billing_city"
                               value="<?= htmlspecialchars($business['billing_city'] ?? '') ?>"
                               placeholder="Montreal">
                    </div>
                    <div class="form-group">
                        <label><?= $t['billing_province'] ?></label>
                        <select name="billing_province">
                            <option value=""><?= $t['select_province'] ?></option>
                            <?php
                            $curBillProv = $business['billing_province'] ?? '';
                            foreach ($provinces as $code => $name):
                            ?>
                            <option value="<?= $code ?>" <?= ($curBillProv===$code||$curBillProv===$name)?'selected':'' ?>><?= $name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label><?= $t['billing_postal'] ?></label>
                        <input type="text" name="billing_postal_code"
                               value="<?= htmlspecialchars($business['billing_postal_code'] ?? '') ?>"
                               placeholder="H9R 1B7" maxlength="7">
                    </div>
                    <div class="form-group">
                        <label><?= $t['billing_country'] ?></label>
                        <input type="text" name="billing_country"
                               value="<?= htmlspecialchars($business['billing_country'] ?? 'Canada') ?>">
                    </div>
                </div>
            </div>

            <div id="billingMessage" class="form-message"></div>
            <button type="submit" class="btn-save" id="saveBillingBtn">
                <i class="fas fa-save"></i> <?= $t['save_billing'] ?>
            </button>
        </form>
    </div>

    <!-- 4. Account Information -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title"><i class="fas fa-building"></i> <?= $t['account_info'] ?></div>
        </div>
        <p style="font-size:12px;color:#9ca3af;margin:-14px 0 18px;display:flex;align-items:center;gap:6px;">
            <i class="fas fa-lock" style="font-size:11px;"></i>
            <?= $currentLang === 'fr' ? 'Pour modifier ces informations, contactez-nous à' : 'To update these details, contact us at' ?>
            <a href="mailto:info@ocsapp.ca" style="color:#00b207;font-weight:600;">info@ocsapp.ca</a>
        </p>
        <div class="account-info">
            <div>
                <div class="info-group">
                    <div class="info-label"><?= $t['company_name'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($business['company_name']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $t['contact_person'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($business['first_name'] . ' ' . $business['last_name']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $t['email'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($business['email']) ?></div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $t['phone'] ?></div>
                    <div class="info-value"><?= htmlspecialchars($business['phone']) ?></div>
                </div>
            </div>
            <div>
                <div class="info-group">
                    <div class="info-label"><?= $t['account_tier'] ?></div>
                    <div class="info-value">
                        <?php
                        $tierClass  = ['standard'=>'badge-standard','approved'=>'badge-approved','premium'=>'badge-premium'];
                        $tierLabel  = $tierLabels[$business['account_tier'] ?? 'standard'] ?? $t['tier_standard'];
                        $badgeClass = $tierClass[$business['account_tier'] ?? 'standard'] ?? 'badge-standard';
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= $tierLabel ?></span>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-label"><?= $t['member_since'] ?></div>
                    <div class="info-value"><?= date('F j, Y', strtotime($business['created_at'])) ?></div>
                </div>
            </div>
        </div>
    </div>

</div><!-- /right column -->
</div><!-- .settings-grid -->

<!-- Notifications & Sound -->
<div class="section-card" style="max-width:860px;margin:0 auto 24px;">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-bell"></i> <?= $t['notif_title'] ?></div>
    </div>

    <!-- Sound toggle -->
    <div class="notif-pref-row">
        <div class="notif-pref-info">
            <div class="notif-pref-label"><i class="fas fa-volume-up"></i> <?= $t['notif_sounds_label'] ?></div>
            <div class="notif-pref-desc"><?= $t['notif_sounds_desc'] ?></div>
        </div>
        <label class="toggle-switch" title="Toggle notification sounds">
            <input type="checkbox" id="soundToggle" onchange="onSoundToggle(this.checked)">
            <span class="toggle-track"></span>
        </label>
    </div>

    <!-- Test sound button -->
    <div style="margin-top:8px;margin-bottom:20px;">
        <button type="button" class="btn-test-sound" onclick="testSound()">
            <i class="fas fa-play-circle"></i> <?= $t['test_sound'] ?>
        </button>
        <span id="testSoundMsg" style="margin-left:10px;font-size:13px;color:#6b7280;display:none;"></span>
    </div>

    <hr style="border:none;border-top:1px solid #e5e7eb;margin:0 0 20px;">

    <!-- Browser Notifications toggle -->
    <div class="notif-pref-row">
        <div class="notif-pref-info">
            <div class="notif-pref-label"><i class="fas fa-desktop"></i> <?= $t['browser_notif'] ?></div>
            <div class="notif-pref-desc"><?= $t['browser_notif_desc'] ?></div>
        </div>
        <span id="browserNotifStatus" class="notif-status-badge"></span>
    </div>
    <div id="browserNotifAction" style="margin-top:10px;"></div>
</div>

<!-- Change Password -->
<div class="section-card" style="max-width:860px;margin:0 auto 24px;">
    <div class="section-header">
        <div class="section-title"><i class="fas fa-lock"></i> <?= $t['change_password'] ?></div>
    </div>
    <form id="passwordForm">
        <?= csrfField() ?>

        <div class="form-group">
            <label for="current_password"><?= $t['current_password'] ?> <span style="color:#ef4444;">*</span></label>
            <div class="password-wrapper">
                <input type="password" id="current_password" name="current_password" required>
                <button type="button" class="password-toggle" data-target="current_password" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
        </div>

        <div class="form-group">
            <label for="new_password"><?= $t['new_password'] ?> <span style="color:#ef4444;">*</span></label>
            <div class="password-wrapper">
                <input type="password" id="new_password" name="new_password" required minlength="8">
                <button type="button" class="password-toggle" data-target="new_password" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="pw-rules-box" id="pwRulesBox"></div>
        </div>

        <div class="form-group">
            <label for="confirm_password"><?= $t['confirm_password'] ?> <span style="color:#ef4444;">*</span></label>
            <div class="password-wrapper">
                <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
                <button type="button" class="password-toggle" data-target="confirm_password" aria-label="Show password">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <div class="pw-match-error" id="pwMatchError"><?= $t['pw_no_match'] ?></div>
        </div>

        <div id="passwordMessage" class="form-message"></div>

        <button type="submit" class="btn-save" id="updatePasswordBtn">
            <i class="fas fa-save"></i> <?= $t['update_password'] ?>
        </button>
    </form>
</div>

<script>
const settingsLang = <?= json_encode([
    'saving'          => html_entity_decode($t['saving'],         ENT_QUOTES, 'UTF-8'),
    'save_address'    => html_entity_decode($t['save_address'],   ENT_QUOTES, 'UTF-8'),
    'save_billing'    => html_entity_decode($t['save_billing'],   ENT_QUOTES, 'UTF-8'),
    'save_payment'    => html_entity_decode($t['save_payment'],   ENT_QUOTES, 'UTF-8'),
    'error_occurred'  => html_entity_decode($t['error_occurred'], ENT_QUOTES, 'UTF-8'),
    'error_updating'  => html_entity_decode($t['error_updating'], ENT_QUOTES, 'UTF-8'),
    'searching'       => html_entity_decode($t['searching'],      ENT_QUOTES, 'UTF-8'),
    'no_results'      => html_entity_decode($t['no_results'],     ENT_QUOTES, 'UTF-8'),
    'search_failed'   => html_entity_decode($t['search_failed'],  ENT_QUOTES, 'UTF-8'),
    'address_updated' => html_entity_decode($t['address_updated'],ENT_QUOTES, 'UTF-8'),
    'billing_updated' => html_entity_decode($t['billing_updated'],ENT_QUOTES, 'UTF-8'),
    'payment_updated' => html_entity_decode($t['payment_updated'],ENT_QUOTES, 'UTF-8'),
]) ?>;

// Generic AJAX form helper
function submitSettingsForm(formId, url, btnId, successMsg) {
    const form = document.getElementById(formId);
    const btn  = document.getElementById(btnId);
    const msgId = formId.replace('Form', 'Message');
    const msgDiv = document.getElementById(msgId);

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const origHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + settingsLang.saving;
        if (msgDiv) { msgDiv.className = 'form-message'; msgDiv.innerHTML = ''; }

        try {
            const res  = await fetch(url, { method: 'POST', body: new FormData(this) });
            const data = await res.json();
            if (msgDiv) {
                if (data.success) {
                    msgDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + successMsg;
                    msgDiv.className = 'form-message alert-success';
                    setTimeout(() => { msgDiv.className = 'form-message'; msgDiv.innerHTML = ''; }, 5000);
                } else {
                    msgDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || settingsLang.error_updating);
                    msgDiv.className = 'form-message alert-error';
                }
            }
        } catch {
            if (msgDiv) {
                msgDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + settingsLang.error_occurred;
                msgDiv.className = 'form-message alert-error';
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = origHtml;
        }
    });
}

submitSettingsForm('addressForm',  '<?= url('distribution/update-address') ?>',  'saveAddressBtn',  settingsLang.address_updated);
submitSettingsForm('billingForm',  '<?= url('distribution/update-billing') ?>',  'saveBillingBtn',  settingsLang.billing_updated);
submitSettingsForm('paymentForm',  '<?= url('distribution/update-payment') ?>',  'savePaymentBtn',  settingsLang.payment_updated);

// Payment preference toggle
document.querySelectorAll('input[name="payment_preference"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.banking-section').forEach(function(s) { s.style.display = 'none'; });
        document.querySelectorAll('.pref-option').forEach(function(o) { o.classList.remove('active'); });
        this.closest('.pref-option').classList.add('active');
        var map = { eft: 'eftFields', interac: 'interacFields', cheque: 'chequeFields' };
        if (map[this.value]) document.getElementById(map[this.value]).style.display = 'block';
    });
});

// Billing same-as-delivery toggle
function toggleBillingSame() {
    const cb     = document.getElementById('use_delivery_for_billing');
    const fields = document.getElementById('billingFields');
    // The click on .checkbox-row fires before the checkbox state toggles
    // so we read the *future* state
    const willBeChecked = !cb.checked;
    fields.style.display = willBeChecked ? 'none' : '';
}

// Address Autocomplete (Nominatim) — delivery address only
(function() {
    const addressInput   = document.getElementById('delivery_street');
    const suggestionsBox = document.getElementById('addressSuggestions');
    const cityInput      = document.getElementById('delivery_city');
    const provinceSelect = document.getElementById('delivery_province');
    const postalInput    = document.getElementById('delivery_postal_code');
    const countryInput   = document.getElementById('delivery_country');
    const latInput       = document.getElementById('delivery_latitude');
    const lngInput       = document.getElementById('delivery_longitude');

    const provinceMap = {
        'alberta':'AB','british columbia':'BC','manitoba':'MB','new brunswick':'NB',
        'newfoundland and labrador':'NL','nova scotia':'NS','northwest territories':'NT',
        'nunavut':'NU','ontario':'ON','prince edward island':'PE',
        'quebec':'QC','québec':'QC','saskatchewan':'SK','yukon':'YT'
    };
    const abbreviations = {
        'hwy':'Highway','blvd':'Boulevard','ave':'Avenue','st':'Street','dr':'Drive',
        'rd':'Road','crt':'Court','cres':'Crescent','pl':'Place','ln':'Lane',
        'pkwy':'Parkway','cir':'Circle','terr':'Terrace','ct':'Court',
        'aut':'Autoroute','ch':'Chemin','boul':'Boulevard','rte':'Route',
        'mtee':'Montée','cote':'Côte'
    };
    const bilingualNames = {'trans-canada':'Transcanadienne','trans canada':'Transcanadienne'};
    let debounceTimer = null, highlightedIndex = -1, currentResults = [];

    addressInput.addEventListener('input', function() {
        const q = this.value.trim();
        clearTimeout(debounceTimer);
        if (q.length < 3) { hideSuggestions(); return; }
        debounceTimer = setTimeout(() => searchAddress(q), 350);
    });
    addressInput.addEventListener('keydown', function(e) {
        if (!suggestionsBox.classList.contains('active')) return;
        const items = suggestionsBox.querySelectorAll('.address-suggestion-item');
        if      (e.key === 'ArrowDown') { e.preventDefault(); highlightedIndex = Math.min(highlightedIndex+1, items.length-1); updateHighlight(items); }
        else if (e.key === 'ArrowUp')   { e.preventDefault(); highlightedIndex = Math.max(highlightedIndex-1, 0);              updateHighlight(items); }
        else if (e.key === 'Enter' && highlightedIndex >= 0) { e.preventDefault(); selectSuggestion(currentResults[highlightedIndex]); }
        else if (e.key === 'Escape')    { hideSuggestions(); }
    });
    function updateHighlight(items) {
        items.forEach((item, i) => item.classList.toggle('highlighted', i === highlightedIndex));
        if (items[highlightedIndex]) items[highlightedIndex].scrollIntoView({ block: 'nearest' });
    }
    function expandAbbreviations(q) {
        return q.replace(/\b(\w+)\b/g, m => abbreviations[m.toLowerCase()] || m);
    }
    function translateToFrench(q) {
        let r = expandAbbreviations(q);
        const lower = r.toLowerCase();
        for (const [en, fr] of Object.entries(bilingualNames)) {
            if (lower.includes(en)) r = r.replace(new RegExp(en,'gi'), fr);
        }
        return r.replace(/\bHighway\b/gi, 'Autoroute');
    }
    async function nominatimSearch(query, viewbox) {
        const params = new URLSearchParams({ q: query, format:'json', addressdetails:1, limit:8 });
        if (viewbox) { params.set('viewbox', viewbox); params.set('bounded', 0); }
        const res  = await fetch('https://nominatim.openstreetmap.org/search?' + params, { headers: { 'User-Agent': 'OCSAPP-Distribution/1.0' } });
        const data = await res.json();
        return data.filter(r => r.address && r.address.country_code === 'ca');
    }
    const VIEWBOX = '-80.0,42.0,-57.0,62.0';
    async function searchAddress(query) {
        suggestionsBox.innerHTML = '<div class="address-loading"><i class="fas fa-spinner fa-spin"></i> ' + settingsLang.searching + '</div>';
        suggestionsBox.classList.add('active');
        try {
            let data = await nominatimSearch(query + ', Quebec', VIEWBOX);
            if (!data.length) { const fr = translateToFrench(query); if (fr !== query) data = await nominatimSearch(fr + ', Quebec', VIEWBOX); }
            if (!data.length) { const ex = expandAbbreviations(query); if (ex !== query) data = await nominatimSearch(ex + ', Quebec', VIEWBOX); }
            if (!data.length) data = await nominatimSearch(query, VIEWBOX);
            if (!data.length) { const ex = expandAbbreviations(query); if (ex !== query) data = await nominatimSearch(ex, VIEWBOX); }
            currentResults = data; highlightedIndex = -1;
            if (!data.length) { suggestionsBox.innerHTML = '<div class="address-loading">' + settingsLang.no_results + '</div>'; return; }
            suggestionsBox.innerHTML = data.map((item, i) => {
                const addr   = item.address || {};
                const street = [addr.house_number, addr.road].filter(Boolean).join(' ');
                const city   = addr.city || addr.town || addr.village || addr.municipality || '';
                return `<div class="address-suggestion-item" data-index="${i}">
                    <i class="fas fa-map-marker-alt"></i>
                    <div>
                        <div class="suggestion-main">${street || item.display_name.split(',')[0]}</div>
                        <div class="suggestion-detail">${[city, addr.state, addr.postcode].filter(Boolean).join(', ')}</div>
                    </div>
                </div>`;
            }).join('');
            suggestionsBox.querySelectorAll('.address-suggestion-item').forEach(el =>
                el.addEventListener('click', function() { selectSuggestion(currentResults[parseInt(this.dataset.index)]); })
            );
        } catch {
            suggestionsBox.innerHTML = '<div class="address-loading">' + settingsLang.search_failed + '</div>';
        }
    }
    function selectSuggestion(result) {
        const addr = result.address || {};
        addressInput.value    = [addr.house_number, addr.road].filter(Boolean).join(' ') || result.display_name.split(',')[0];
        cityInput.value       = addr.city || addr.town || addr.village || addr.municipality || '';
        const code            = provinceMap[(addr.state || '').toLowerCase()] || '';
        if (code) provinceSelect.value = code;
        postalInput.value     = addr.postcode || '';
        countryInput.value    = addr.country || 'Canada';
        latInput.value        = result.lat || '';
        lngInput.value        = result.lon || '';
        hideSuggestions();
        [cityInput, provinceSelect, postalInput, countryInput].forEach(el => {
            el.style.transition = 'background 0.3s';
            el.style.background = '#d1fae5';
            setTimeout(() => { el.style.background = ''; }, 1200);
        });
    }
    function hideSuggestions() {
        suggestionsBox.classList.remove('active');
        suggestionsBox.innerHTML = '';
        currentResults = []; highlightedIndex = -1;
    }
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsBox.contains(e.target)) hideSuggestions();
    });
})();
</script>

<script>
// ── Notifications & Sound Settings ───────────────────────────────────────────
var _ns = {
  soundPlayed:      <?= json_encode(html_entity_decode($t['sound_played'],        ENT_QUOTES, 'UTF-8')) ?>,
  soundUnavailable: <?= json_encode(html_entity_decode($t['sound_unavailable'],   ENT_QUOTES, 'UTF-8')) ?>,
  notSupported:     <?= json_encode(html_entity_decode($t['notif_not_supported'], ENT_QUOTES, 'UTF-8')) ?>,
  notSupportedMsg:  <?= json_encode(html_entity_decode($t['notif_not_sup_msg'],   ENT_QUOTES, 'UTF-8')) ?>,
  enabled:          <?= json_encode(html_entity_decode($t['notif_enabled'],       ENT_QUOTES, 'UTF-8')) ?>,
  enabledMsg:       <?= json_encode(html_entity_decode($t['notif_enabled_msg'],   ENT_QUOTES, 'UTF-8')) ?>,
  blocked:          <?= json_encode(html_entity_decode($t['notif_blocked'],       ENT_QUOTES, 'UTF-8')) ?>,
  blockedMsg:       <?= json_encode(html_entity_decode($t['notif_blocked_msg'],   ENT_QUOTES, 'UTF-8')) ?>,
  notEnabled:       <?= json_encode(html_entity_decode($t['notif_not_enabled'],   ENT_QUOTES, 'UTF-8')) ?>,
  enableBtn:        <?= json_encode(html_entity_decode($t['notif_enable_btn'],    ENT_QUOTES, 'UTF-8')) ?>,
};
(function initBizNotifSettings() {
  var soundToggle = document.getElementById('soundToggle');
  var soundEnabled = localStorage.getItem('biz_sound_enabled') !== 'off';
  soundToggle.checked = soundEnabled;

  window.onSoundToggle = function(checked) {
    localStorage.setItem('biz_sound_enabled', checked ? 'on' : 'off');
    if (checked) {
      if (typeof _unlockBizAudio === 'function') _unlockBizAudio();
      localStorage.setItem('biz_notif_enabled', '1');
      var banner = document.getElementById('bizNotifEnableBanner');
      if (banner) banner.style.display = 'none';
    }
  };

  window.testSound = function() {
    var msg = document.getElementById('testSoundMsg');
    if (typeof _unlockBizAudio === 'function') _unlockBizAudio();
    localStorage.setItem('biz_notif_enabled', '1');
    localStorage.setItem('biz_sound_enabled', 'on');
    soundToggle.checked = true;
    var banner = document.getElementById('bizNotifEnableBanner');
    if (banner) banner.style.display = 'none';
    if (typeof window.playChimeBiz === 'function') {
      window.playChimeBiz();
      msg.textContent = _ns.soundPlayed;
    } else {
      msg.textContent = _ns.soundUnavailable;
    }
    msg.style.display = 'inline';
    setTimeout(function() { msg.style.display = 'none'; }, 2500);
  };

  var statusBadge = document.getElementById('browserNotifStatus');
  var actionArea  = document.getElementById('browserNotifAction');

  function renderNotifStatus() {
    if (typeof Notification === 'undefined') {
      statusBadge.textContent = _ns.notSupported;
      statusBadge.className = 'notif-status-badge denied';
      actionArea.innerHTML = '<small style="color:#6b7280;">' + _ns.notSupportedMsg + '</small>';
      return;
    }
    var perm = Notification.permission;
    if (perm === 'granted') {
      statusBadge.textContent = _ns.enabled;
      statusBadge.className = 'notif-status-badge granted';
      actionArea.innerHTML = '<small style="color:#059669;"><i class="fas fa-check-circle"></i> ' + _ns.enabledMsg + '</small>';
    } else if (perm === 'denied') {
      statusBadge.textContent = _ns.blocked;
      statusBadge.className = 'notif-status-badge denied';
      actionArea.innerHTML = '<small style="color:#dc2626;"><i class="fas fa-ban"></i> ' + _ns.blockedMsg + '</small>';
    } else {
      statusBadge.textContent = _ns.notEnabled;
      statusBadge.className = 'notif-status-badge default';
      actionArea.innerHTML = '<button type="button" class="btn-test-sound" onclick="requestBrowserNotif()"><i class="fas fa-bell"></i> ' + _ns.enableBtn + '</button>';
    }
  }

  window.requestBrowserNotif = function() {
    Notification.requestPermission().then(function() { renderNotifStatus(); });
  };

  renderNotifStatus();
})();
</script>

<script>
// ── Change Password ───────────────────────────────────────────────────────────
var _pw = {
  tooShort:     <?= json_encode(html_entity_decode($t['pw_too_short'],    ENT_QUOTES, 'UTF-8')) ?>,
  mismatch:     <?= json_encode(html_entity_decode($t['pw_mismatch'],     ENT_QUOTES, 'UTF-8')) ?>,
  noMatch:      <?= json_encode(html_entity_decode($t['pw_no_match'],     ENT_QUOTES, 'UTF-8')) ?>,
  genericError: <?= json_encode(html_entity_decode($t['pw_generic_error'],ENT_QUOTES, 'UTF-8')) ?>,
  saving:       <?= json_encode(html_entity_decode($t['saving'],          ENT_QUOTES, 'UTF-8')) ?>,
  updateBtn:    <?= json_encode(html_entity_decode($t['update_password'], ENT_QUOTES, 'UTF-8')) ?>,
  rules: [
    { id: 'rule-length',  test: function(pw) { return pw.length >= 8; },           label: <?= json_encode(html_entity_decode($t['pw_min_length'], ENT_QUOTES, 'UTF-8')) ?> },
    { id: 'rule-upper',   test: function(pw) { return /[A-Z]/.test(pw); },         label: <?= json_encode(html_entity_decode($t['pw_uppercase'],  ENT_QUOTES, 'UTF-8')) ?> },
    { id: 'rule-lower',   test: function(pw) { return /[a-z]/.test(pw); },         label: <?= json_encode(html_entity_decode($t['pw_lowercase'],  ENT_QUOTES, 'UTF-8')) ?> },
    { id: 'rule-number',  test: function(pw) { return /[0-9]/.test(pw); },         label: <?= json_encode(html_entity_decode($t['pw_number'],     ENT_QUOTES, 'UTF-8')) ?> },
    { id: 'rule-special', test: function(pw) { return /[^A-Za-z0-9]/.test(pw); }, label: <?= json_encode(html_entity_decode($t['pw_special'],    ENT_QUOTES, 'UTF-8')) ?> },
  ],
};

// Build password rules UI
(function() {
  var box = document.getElementById('pwRulesBox');
  var newPwInput    = document.getElementById('new_password');
  var confirmInput  = document.getElementById('confirm_password');
  var matchError    = document.getElementById('pwMatchError');

  _pw.rules.forEach(function(r) {
    var el = document.createElement('div');
    el.id = r.id;
    el.className = 'pw-rule pw-rule-fail';
    el.innerHTML = '<i class="fas fa-circle-xmark"></i> ' + r.label;
    box.appendChild(el);
  });

  function updateRules(pw) {
    _pw.rules.forEach(function(r) {
      var el = document.getElementById(r.id);
      var pass = r.test(pw);
      el.className = 'pw-rule ' + (pass ? 'pw-rule-ok' : 'pw-rule-fail');
      el.innerHTML = '<i class="fas fa-' + (pass ? 'circle-check' : 'circle-xmark') + '"></i> ' + r.label;
    });
  }

  newPwInput.addEventListener('input', function() {
    updateRules(this.value);
    if (confirmInput.value.length > 0) {
      matchError.style.display = (this.value !== confirmInput.value) ? 'block' : 'none';
    }
  });

  confirmInput.addEventListener('input', function() {
    matchError.style.display = (newPwInput.value !== this.value && this.value.length > 0) ? 'block' : 'none';
  });
})();

// Password form submit
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  var newPassword     = document.getElementById('new_password').value;
  var confirmPassword = document.getElementById('confirm_password').value;
  var msgDiv          = document.getElementById('passwordMessage');
  var btn             = document.getElementById('updatePasswordBtn');

  if (newPassword.length < 8) {
    showPwMessage(_pw.tooShort, 'alert-error');
    return;
  }
  if (newPassword !== confirmPassword) {
    showPwMessage(_pw.mismatch, 'alert-error');
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + _pw.saving;

  try {
    var res  = await fetch('<?= url('distribution/update-password') ?>', { method: 'POST', body: new FormData(this) });
    var data = await res.json();
    if (data.success) {
      showPwMessage(data.message, 'alert-success');
      document.getElementById('passwordForm').reset();
      document.getElementById('pwRulesBox').querySelectorAll('.pw-rule').forEach(function(el) {
        el.className = 'pw-rule pw-rule-fail';
        var label = el.textContent.trim();
        el.innerHTML = '<i class="fas fa-circle-xmark"></i> ' + label;
      });
    } else {
      showPwMessage(data.message || _pw.genericError, 'alert-error');
    }
  } catch (err) {
    showPwMessage(_pw.genericError, 'alert-error');
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> ' + _pw.updateBtn;
  }
});

function showPwMessage(msg, cls) {
  var div = document.getElementById('passwordMessage');
  div.innerHTML = '<i class="fas fa-' + (cls === 'alert-success' ? 'check-circle' : 'exclamation-circle') + '"></i> ' + msg;
  div.className = 'form-message ' + cls;
  if (cls === 'alert-success') setTimeout(function() { div.className = 'form-message'; div.innerHTML = ''; }, 5000);
}

// Password visibility toggles
document.querySelectorAll('.password-toggle').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var input = document.getElementById(this.dataset.target);
    var icon  = this.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.replace('fa-eye', 'fa-eye-slash');
      this.setAttribute('aria-label', 'Hide password');
    } else {
      input.type = 'password';
      icon.classList.replace('fa-eye-slash', 'fa-eye');
      this.setAttribute('aria-label', 'Show password');
    }
  });
});
</script>

<?php include __DIR__ . '/layout-footer.php'; ?>
